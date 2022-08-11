<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audit_log', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->json('old_values');
            $table->json('new_values');
            $table->string('event');
            $table->string('module');
            $table->tinyInteger('module_id')->unsigned();

            $table->integer('user_id')->unsigned();
            $table->ipAddress('ip');
            $table->string('user_agent')->nullable();

            $table->dateTime('created_at')->useCurrent();


            $table->index(['module', 'module_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audit_log');
    }
}
