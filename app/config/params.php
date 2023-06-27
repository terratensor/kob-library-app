<?php

return [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    'frontendHostInfo' => getenv('FRONTEND_URL'),
    'githubRepositoryUrl' => getenv('GH_REPO_URL'),
    'manticore' => [
        'host' => 'manticore',
        'port' => 9308
    ],
    'searchResults' => [
        'pageSize' => (int)getenv('PAGE_SIZE'),
    ],
    'indexes' => [
        'common' => 'vpsssr_library',
        'concept' => 'vpsssr_library_concept',
    ]
];
