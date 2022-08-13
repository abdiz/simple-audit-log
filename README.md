# simple-audit-log

[![Version](http://poser.pugx.org/abdi.zbn/simple-audit-log/v)](https://packagist.org/packages/abdi.zbn/simple-audit-log)
[![License](http://poser.pugx.org/abdi.zbn/simple-audit-log/license)](https://packagist.org/packages/abdi.zbn/simple-audit-log)
[![composer.lock](http://poser.pugx.org/abdi.zbn/simple-audit-log/composerlock)](https://packagist.org/packages/abdi.zbn/simple-audit-log)
[![PHP Version Require](http://poser.pugx.org/abdi.zbn/simple-audit-log/require/php)](https://packagist.org/abdi.zbn/simple-audit-log/phpunit)
[![.gitattributes](http://poser.pugx.org/abdi.zbn/simple-audit-log/gitattributes)](https://packagist.org/packages/abdi.zbn/simple-audit-log)

Laravel Simple Audit Log Package
============

This Package is for Laravel 5+ and make it easy to keep the history of the Eloquent's Model changes. just use the trait in the model and you're good to go.

#### Composer Install (for Laravel 5+)

	composer require abdi.zbn/simple-audit-log

#### Publish and Run the migrations


```bash
php artisan vendor:publish --provider="AbdiZbn\SimpleAuditLog\SimpleAuditLogServiceProvider" --tag=migrations

php artisan migrate
```


if you're using Laravel version 5.5+, Likeable package will be auto-discovered by Laravel. and if not: register the package in config/app.php providers array manually.
```php
'providers' => [
	...
	\AbdiZbn\SimpleAuditLog\SimpleAuditLogServiceProvider::class,
],
```


#### Setup models - just use the Trait in the Model.

```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use AbdiZbn\SimpleAuditLog\AuditableTrait;

class Post extends Model
{
    use AuditableTrait;

    public function getModule()
    {
        return 'post';
    }

}
```
#### Credits

 - Zeinab Abdi- <abdi.zbn@gmail.com>
