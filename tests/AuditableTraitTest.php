<?php

namespace AbdiZbn\SimpleAuditLog\Tests;

use AbdiZbn\SimpleAuditLog\AuditLog;
use AbdiZbn\SimpleAuditLog\SimpleAuditLogServiceProvider;
use AbdiZbn\SimpleAuditLog\Tests\Fixtures\Post;
use Illuminate\Auth\GenericUser;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AuditableTraitTest extends TestCase
{
    public function test_package_migration_creates_the_audit_log_table()
    {
        $this->assertTrue(Schema::hasTable('audit_log'));
        $this->assertTrue(Schema::hasColumns('audit_log', [
            'id', 'old_values', 'new_values', 'event', 'module', 'module_id',
            'user_id', 'ip', 'user_agent', 'created_at',
        ]));
    }

    public function test_default_config_is_available_without_publishing()
    {
        $this->assertTrue(config('audit.enabled'));
        $this->assertSame(['created', 'updating', 'deleted'], config('audit.events'));
    }

    public function test_publishable_files_exist()
    {
        foreach (['config', 'migrations'] as $tag) {
            $paths = ServiceProvider::pathsToPublish(SimpleAuditLogServiceProvider::class, $tag);

            $this->assertNotEmpty($paths, "Nothing is published for the [{$tag}] tag.");

            foreach (array_keys($paths) as $source) {
                $this->assertFileExists($source);
            }
        }
    }

    public function test_it_logs_the_created_event()
    {
        $post = Post::create(['title' => 'Hello', 'secret' => 'hidden']);

        $audit = AuditLog::sole();

        $this->assertSame('created', $audit->event);
        $this->assertSame('post', $audit->module);
        $this->assertEquals($post->getKey(), $audit->module_id);
        $this->assertEquals(0, $audit->user_id);
        $this->assertSame([], json_decode($audit->old_values, true));

        $new = json_decode($audit->new_values, true);
        $this->assertSame('Hello', $new['title']);
        $this->assertArrayNotHasKey('secret', $new);
    }

    public function test_it_logs_the_updating_event()
    {
        $post = Post::create(['title' => 'Hello']);
        $post->update(['title' => 'Bye']);

        $audit = AuditLog::where('event', 'updating')->sole();

        $this->assertEquals($post->getKey(), $audit->module_id);
        $this->assertSame('Hello', json_decode($audit->old_values, true)['title']);
        $this->assertSame('Bye', json_decode($audit->new_values, true)['title']);
    }

    public function test_it_logs_the_deleted_event()
    {
        $post = Post::create(['title' => 'Hello']);
        $post->delete();

        $audit = AuditLog::where('event', 'deleted')->sole();

        $this->assertEquals($post->getKey(), $audit->module_id);
        $this->assertSame('Hello', json_decode($audit->old_values, true)['title']);
        $this->assertSame([], json_decode($audit->new_values, true));
    }

    public function test_it_records_the_authenticated_user_and_client_data()
    {
        $_SERVER['REMOTE_ADDR'] = '203.0.113.7';
        $_SERVER['HTTP_USER_AGENT'] = 'PHPUnit';

        $this->actingAs(new GenericUser(['id' => 42]));

        Post::create(['title' => 'Hello']);

        $audit = AuditLog::sole();

        $this->assertEquals(42, $audit->user_id);
        $this->assertSame('203.0.113.7', $audit->ip);
        $this->assertSame('PHPUnit', $audit->user_agent);
    }

    public function test_it_only_logs_configured_events()
    {
        config(['audit.events' => ['created']]);

        $post = Post::create(['title' => 'Hello']);
        $post->update(['title' => 'Bye']);
        $post->delete();

        $this->assertSame(['created'], AuditLog::pluck('event')->all());
    }

    public function test_auditing_can_be_disabled()
    {
        config(['audit.enabled' => false]);
        Post::clearBootedModels();

        Post::create(['title' => 'Hello']);

        $this->assertSame(0, AuditLog::count());
    }

    public function test_audits_relation_returns_the_model_history()
    {
        $first = Post::create(['title' => 'First']);
        $second = Post::create(['title' => 'Second']);
        $first->update(['title' => 'First (edited)']);

        $this->assertEqualsCanonicalizing(['created', 'updating'], $first->audits->pluck('event')->all());
        $this->assertSame(['created'], $second->audits->pluck('event')->all());
        $this->assertInstanceOf(\DateTimeInterface::class, $first->audits->first()->created_at);
    }
}
