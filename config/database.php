<?php

declare(strict_types=1);

use Cycle\Database\Config;

return [
    'default' => env('DB_CONNECTION', 'mysql'),

    'databases' => [
        'mysql' => [
            'driver' => 'mysql',
        ],
        'postgres' => [
            'driver' => 'postgres',
        ],
        'sqlite' => [
            'driver' => 'sqlite',
        ],
    ],

    'drivers' => [
        'mysql' => new Config\MySQLDriverConfig(
            connection: new Config\MySQL\TcpConnectionConfig(
                database: env('DB_DATABASE', 'prestoworld'),
                host: env('DB_HOST', '127.0.0.1'),
                port: (int)env('DB_PORT', 3306),
                user: env('DB_USERNAME', 'root'),
                password: env('DB_PASSWORD', ''),
                charset: env('DB_CHARSET', 'utf8mb4'),
            ),
            queryCache: true
        ),
        'postgres' => new Config\PostgresDriverConfig(
            connection: new Config\Postgres\TcpConnectionConfig(
                database: env('DB_DATABASE', 'prestoworld'),
                host: env('DB_HOST', '127.0.0.1'),
                port: (int)env('DB_PORT', 5432),
                user: env('DB_USERNAME', 'postgres'),
                password: env('DB_PASSWORD', ''),
            ),
            schema: env('DB_SCHEMA', 'public'),
            queryCache: true
        ),
        'sqlite' => new Config\SQLiteDriverConfig(
            connection: new Config\SQLite\FileConnectionConfig(
                database: env('DB_DATABASE', base_path('storage/database.sqlite'))
            ),
            queryCache: true
        ),
    ],
];
