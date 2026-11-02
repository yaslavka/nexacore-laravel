<?php

return [

    'label' => 'Пагинация навигациясы',

    'overview' => '{1} 1 жыйынтык көрсөтүлүп жатат|[2,*] :total жыйынтыктын :first–:last көрсөтүлүп жатат',

    'fields' => [

        'records_per_page' => [

            'label' => 'Бетке',

            'options' => [
                'all' => 'Бардыгы',
            ],

        ],

    ],

    'actions' => [

        'first' => [
            'label' => 'Башы',
        ],

        'go_to_page' => [
            'label' => ':page бетке өтүү',
        ],

        'last' => [
            'label' => 'Аягы',
        ],

        'next' => [
            'label' => 'Кийинки',
        ],

        'previous' => [
            'label' => 'Мурунку',
        ],

    ],

];
