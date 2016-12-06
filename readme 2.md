## Proficio Client

This package relies on a sqlite data dump from Proficio that is created by the `extractor` repositiory: https://bitbucket.org/imalab/extractor

###Provided routes / proficioController methods:

example.site/proficio/getAllObjectIDs
example.site/proficio/getAllObjectIDs?catalog=archive

example.site/proficio/getAllObjects
example.site/proficio/getAllObjects?catalog=archive&take=5&start=5

example.site/proficio/getSpecificObject/{id}
example.site/proficio/getSpecificObject/{id}?catalog=archive

example.site/proficio/getUpdatedObjectIDs
example.site/proficio/getUpdatedObjectIDs?catalog=archive

example.site/proficio/getAllUpdatedObjects
example.site/proficio/getAllUpdatedObjects?catalog=archive&take=5&start=5


### Laravel Setup

In `config\app.php` add to the autoloaded providers
```php
Imamuseum\PictionClient\PictionServiceProvider::class,
```

```php
php artisan vendor:publish
```

In laravel `config\database.php` add a connection to the proficio sqlite. The name of this connection is defined in the proficio.php config file.
```php
    'connections' => [
        'proficio' => [
            'driver'   => 'sqlite',
            'database' => storage_path('proficio_export/db.sqlite3'),
            'prefix'   => '',
        ],
    ]
```

### Composer Setup
```json
{
    "require": {
        "imamuseum/piction-client": "dev-master@dev"
    },
    "repositories": [
        {
            "type": "git",
            "url": "https://bitbucket.org/imalab/piction-client.git"
        }
    ]
}
```

