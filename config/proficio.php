<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Proficio Routes Configurations
    |--------------------------------------------------------------------------
    |
    | Set to true if you are using the Laravel ProficioServiceProvider and
    | want to expose http routes to proficio-client endpoints.
    |
    */

    'routes_enabled' => true,

    /*
    |--------------------------------------------------------------------------
    | Proficio Query Configurations
    |--------------------------------------------------------------------------
    |
    | Set the default age and maxrows for proficio queries.
    | Set the days between each pull (since)
    | set the default catalog and database connection
    |
    |
    */

    'start' => 0,
    'take' => 999,
    'since' => 10,
    'catalog' => 'collection',
    'database_connection' => 'proficio',
    'queries' => [
        'field_ids' => [
            'collection' =>
                "SELECT
                record_id AS object_uid,
                upd_dte AS updated_at
                from proficio_export_collection",
        ],
        'objects' => [
            'collection' => 'YourQuery'
        ],
        'actors' => [
            'collection' => 'YourQuery'
        ],
        'terms' => [
            'collection' => 'YourQuery'
        ],
        'texts' => [
            'collection' => 'YourQuery'
        ],
        'locations' => [
            'collection' => 'YourQuery'
        ],
        'dates' => [
            'collection' => 'YourQuery'
        ],
        'related' => [
            'collection' => 'YourQuery'
        ],
        'images' => [
            'collection' => 'YourQuery'
        ],
        'media' => [
            'collection' => 'YourQuery'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Transform Configuration
    |--------------------------------------------------------------------------
    |
    |
    |
    */

    'full_image_path' => 'server\path\to\fullimages',
    'thumb_image_path' => 'server\path\to\thumbnails',
    'media_path' => 'server\path\to\media',
    'field_transform_class' => 'Your\Proficio\Transformer\Class\Path',
    'build_relationship_job' => [
        'collection' => '\App\Jobs\YourJobToBuildRelationships',
    ],
];


