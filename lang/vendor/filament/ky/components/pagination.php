<?php

return [
    'label' => 'Пагинация',
    'overview' => '{0} Жыйынтык табылган жок|{1} :total жыйынтыктын :first-си көрсөтүлдү|[2,*] :total жыйынтыктын :first—:last көрсөтүлдү',
    'fields' => [
        'records_per_page' => [
            'label' => 'бир бетке',
            'options' => [
                'all' => 'Бардыгы',
            ],
        ],
    ],
    'actions' => [
        'first' => [
            'label' => 'Биринчи',
        ],
        'go_to_page' => [
            'label' => ':page бетке өтүү',
        ],
        'last' => [
            'label' => 'Акыркы',
        ],
        'next' => [
            'label' => 'Кийинки',
        ],
        'previous' => [
            'label' => 'Мурунку',
        ],
    ],
];
