<?php

return [
    'empty' => [
        'heading' => ':model табылган жок',
        'description' => 'Баштоо үчүн :model түзүңүз.',
    ],

    'fields' => [
        'search' => [
            'label' => 'Издөө',
            'placeholder' => 'Издөө',
            'indicator' => 'Издөө',
        ],
    ],

    'actions' => [
        'disable_reordering' => [
            'label' => 'Тартипти сактоо',
        ],
        'enable_reordering' => [
            'label' => 'Тартипти өзгөртүү',
        ],
        'filter' => [
            'label' => 'Сүзгө',
        ],
        'group' => [
            'label' => 'Топтоо',
        ],
        'open_bulk_actions' => [
            'label' => 'Аракеттерди ачуу',
        ],
        'toggle_columns' => [
            'label' => 'Колонналарды өзгөртүү',
        ],
    ],

    'filters' => [
        'actions' => [
            'apply' => [
                'label' => 'Сүзгөчтөрдү колдонуу',
            ],
            'remove' => [
                'label' => 'Сүзгөчтү алып салуу',
            ],
            'remove_all' => [
                'label' => 'Сүзгөчтөрдү тазалоо',
                'tooltip' => 'Сүзгөчтөрдү тазалоо',
            ],
            'reset' => [
                'label' => 'Баштапкы абалга келтирүү',
            ],
        ],
        'heading' => 'Сүзгөчтөр',
        'indicator' => 'Активдүү сүзгөчтөр',
        'multi_select' => [
            'placeholder' => 'Бардыгы',
        ],
        'select' => [
            'placeholder' => 'Бардыгы',
        ],
    ],

    'grouping' => [
        'fields' => [
            'group' => [
                'label' => 'Топтоо',
                'placeholder' => 'Топтоо',
            ],
            'direction' => [
                'label' => 'Багыт',
                'options' => [
                    'asc' => 'Өсүү боюнча',
                    'desc' => 'Азайуу боюнча',
                ],
            ],
        ],
    ],

    'sorting' => [
        'fields' => [
            'column' => [
                'label' => 'Сорттоо',
            ],
            'direction' => [
                'label' => 'Багыт',
                'options' => [
                    'asc' => 'Өсүү боюнча',
                    'desc' => 'Азайуу боюнча',
                ],
            ],
        ],
    ],

    'column_toggle' => [
        'heading' => 'Колонналар',
    ],

    'selection_indicator' => [
        'selected_count' => '1 жазма тандылды|:count жазма тандылды',
        'actions' => [
            'select_all' => [
                'label' => 'Бардыгын тандоо (:count)',
            ],
            'deselect_all' => [
                'label' => 'Тандоону алып салуу',
            ],
        ],
    ],
];
