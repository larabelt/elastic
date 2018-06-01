## Installation

Add the ServiceProvider to the providers array in config/app.php

```php
Belt\Elastic\BeltElasticServiceProvider::class,
```

```
# publish
php artisan belt-elastic:publish
composer dumpautoload

# migration
php artisan migrate

# compile assets
npm run
```