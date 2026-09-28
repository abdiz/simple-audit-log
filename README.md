# simple-audit-log

[![Version](http://poser.pugx.org/abdi.zbn/simple-audit-log/v)](https://packagist.org/packages/abdi.zbn/simple-audit-log)
[![License](http://poser.pugx.org/abdi.zbn/simple-audit-log/license)](https://packagist.org/packages/abdi.zbn/simple-audit-log)
[![composer.lock](http://poser.pugx.org/abdi.zbn/simple-audit-log/composerlock)](https://packagist.org/packages/abdi.zbn/simple-audit-log)
[![PHP Version Require](http://poser.pugx.org/abdi.zbn/simple-audit-log/require/php)](https://packagist.org/abdi.zbn/simple-audit-log/phpunit)
[![.gitattributes](http://poser.pugx.org/abdi.zbn/simple-audit-log/gitattributes)](https://packagist.org/packages/abdi.zbn/simple-audit-log)

Laravel Simple Audit Log Package
============

This Package makes it easy to keep the history of the Eloquent's Model changes. just use the trait in the model and you're good to go.

#### Requirements

| Laravel | PHP       |
|---------|-----------|
| 13.x    | 8.3 - 8.5 |
| 12.x    | 8.2 - 8.5 |
| 11.x    | 8.2 - 8.4 |
| 10.x    | 8.1 - 8.3 |

#### Composer Install

	composer require abdi.zbn/simple-audit-log

#### Publish and Run the migrations


```bash
php artisan vendor:publish --provider="AbdiZbn\SimpleAuditLog\SimpleAuditLogServiceProvider" --tag=migrations

php artisan migrate
```


The package will be auto-discovered by Laravel. If you have disabled package discovery, register the service provider manually (in `bootstrap/providers.php` for Laravel 11+, or in the `providers` array of `config/app.php` for Laravel 10).
```php
\AbdiZbn\SimpleAuditLog\SimpleAuditLogServiceProvider::class,
```

#### Configuration (optional)

The default configuration is loaded automatically. To customize it, publish the config file to `config/audit.php`:

```bash
php artisan vendor:publish --provider="AbdiZbn\SimpleAuditLog\SimpleAuditLogServiceProvider" --tag=config
```

Auditing can also be turned off with `AUDITING_ENABLED=false` in your `.env` file.


#### Setup models - just use the Trait in the Model.

```php
<?php

namespace App\Models;

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
#### Running the tests

```bash
composer install
composer test
```

#### Credits

 - Zeinab Abdi- <abdi.zbn@gmail.com>
