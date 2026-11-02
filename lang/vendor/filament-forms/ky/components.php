<?php

return [
    'select' => [
        'actions' => [
            'create_option' => [
                'modal' => [
                    'heading' => 'Түзүү',
                    'actions' => [
                        'create' => [
                            'label' => 'Түзүү',
                        ],
                        'create_another' => [
                            'label' => 'Дагы бирин түзүү',
                        ],
                    ],
                ],
            ],
            'edit_option' => [
                'modal' => [
                    'heading' => 'Түзөтүү',
                    'actions' => [
                        'save' => [
                            'label' => 'Сактоо',
                        ],
                    ],
                ],
            ],
        ],
        'boolean' => [
            'true' => 'Ооба',
            'false' => 'Жок',
        ],
        'loading_message' => 'Жүктөлүп жатат...',
        'max_items_message' => 'Тек :count гана тандоого болот.',
        'no_search_results_message' => 'Сурооңузга дал келген варианттар жок.',
        'placeholder' => 'Вариантты тандыңыз',
        'searching_message' => 'Издөө...',
        'search_prompt' => 'Издөө үчүн текстти киргизиңиз...',
    ],

    'repeater' => [
        'actions' => [
            'add' => [
                'label' => ':label кошуу',
            ],
            'add_between' => [
                'label' => 'Арасына кошуу',
            ],
            'delete' => [
                'label' => 'Өчүрүү',
            ],
            'clone' => [
                'label' => 'Көчүрмө',
            ],
            'reorder' => [
                'label' => 'Жылдыруу',
            ],
            'move_down' => [
                'label' => 'Төмөн жылдыруу',
            ],
            'move_up' => [
                'label' => 'Жогору жылдыруу',
            ],
            'collapse' => [
                'label' => 'Жыйноо',
            ],
            'expand' => [
                'label' => 'Ачуу',
            ],
            'collapse_all' => [
                'label' => 'Бардыгын жыйноо',
            ],
            'expand_all' => [
                'label' => 'Бардыгын ачуу',
            ],
        ],
    ],

    'key_value' => [
        'actions' => [
            'add' => [
                'label' => 'Сап кошуу',
            ],
            'delete' => [
                'label' => 'Сапты өчүрүү',
            ],
            'reorder' => [
                'label' => 'Жылдыруу',
            ],
        ],
        'fields' => [
            'key' => [
                'label' => 'Ачкыч',
            ],
            'value' => [
                'label' => 'Маани',
            ],
        ],
    ],

    'checkbox_list' => [
        'actions' => [
            'deselect_all' => [
                'label' => 'Бардыгын тандоодон баш тартуу',
            ],
            'select_all' => [
                'label' => 'Бардыгын тандоо',
            ],
        ],
    ],

    'radio' => [
        'boolean' => [
            'true' => 'Ооба',
            'false' => 'Жок',
        ],
    ],

    'toggle_buttons' => [
        'boolean' => [
            'true' => 'Ооба',
            'false' => 'Жок',
        ],
    ],

    'wizard' => [
        'actions' => [
            'next_step' => [
                'label' => 'Кийинки кадам',
            ],
            'previous_step' => [
                'label' => 'Мурунку кадам',
            ],
        ],
    ],
];
