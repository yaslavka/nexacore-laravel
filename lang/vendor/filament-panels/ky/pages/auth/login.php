<?php

return [
    'title' => 'Кирүү',
    'heading' => 'Аккаунтубузга кириңиз',
    'actions' => [
        'register' => [
            'before' => 'же',
            'label' => 'катталыңыз',
        ],
        'request_password_reset' => [
            'label' => 'Сыр сөздү унуттуңузбу?',
        ],
    ],
    'form' => [
        'email' => [
            'label' => 'Электрондук почта дареги',
        ],
        'password' => [
            'label' => 'Сыр сөз',
        ],
        'remember' => [
            'label' => 'Мени эстеп кал',
        ],
        'actions' => [
            'authenticate' => [
                'label' => 'Кирүү',
            ],
        ],
    ],
    'messages' => [
        'failed' => 'Логин же сыр сөз туура эмес.',
    ],
    'notifications' => [
        'throttled' => [
            'title' => 'Аракеттер өтө көп',
            'body' => 'Сураныч, :seconds секунддан кийин кайра аракет кылыңыз.',
        ],
    ],
];
