<?php

return [

    'title' => 'Кирүү',

    'heading' => 'Кирүү',

    'actions' => [

        'register' => [
            'before' => 'же',
            'label' => 'аккаунт түзүү',
        ],

        'request_password_reset' => [
            'label' => 'Паролду унуттуңузбу?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Электрондук почта',
        ],

        'password' => [
            'label' => 'Пароль',
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

        'failed' => 'Бул дайындар базада табылган жок.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Кирүү аракеттери өтө көп',
            'body' => 'Сураныч, :seconds секундадан кийин кайра аракет кылыңыз.',
        ],

    ],

];
