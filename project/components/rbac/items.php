<?php
return [
    'fullViewUsers' => [
        'type' => 2,
        'description' => 'Возможность детального просмотра кабинета любого пользователя',
    ],
    'fullViewOwnUser' => [
        'type' => 2,
        'description' => 'Возможность детального просмотра своего кабинета',
        'ruleName' => 'isOwnUser',
        'children' => [
            'fullViewUsers',
        ],
    ],
    'user' => [
        'type' => 1,
        'ruleName' => 'userGroup',
        'children' => [
            'fullViewOwnUser',
        ],
    ],
    'admin' => [
        'type' => 1,
        'ruleName' => 'userGroup',
        'children' => [
            'fullViewUsers',
            'user',
        ],
    ],
];
