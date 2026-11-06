<?php

return [
    'env' => getenv('APP_ENV') ?: 'production',
    'debug' => filter_var(getenv('APP_DEBUG') ?: false, FILTER_VALIDATE_BOOL),
    'url' => rtrim(getenv('APP_URL') ?: 'http://localhost:8080', '/'),
    'base_path' => rtrim(getenv('APP_BASE_PATH') ?: '', '/'),
    'database' => [
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'port' => getenv('DB_PORT') ?: '3306',
        'name' => getenv('DB_DATABASE') ?: 'metalcom',
        'username' => getenv('DB_USERNAME') ?: 'metalcom',
        'password' => getenv('DB_PASSWORD') ?: '',
    ],
    'mail' => [
        'from_address' => getenv('MAIL_FROM_ADDRESS') ?: 'site@metalcom.com.br',
        'from_name' => getenv('MAIL_FROM_NAME') ?: 'Metalcom',
    ],
];

