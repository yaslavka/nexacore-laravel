<?php

return [

    'column_toggle' => [

        'heading' => 'Мамычалар',

    ],

    'columns' => [

        'actions' => [
            'label' => 'Аракет|Аракеттер',
        ],

        'text' => [

            'actions' => [
                'collapse_list' => 'Дагы :count жашыруу',
                'expand_list' => 'Дагы :count көрсөтүү',
            ],

            'more_list_items' => 'жана дагы :count',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Топтук аракеттер үчүн бардыгын тандоо/тандоодон чыгаруу.',
        ],

        'bulk_select_record' => [
            'label' => 'Топтук аракеттер үчүн :key тандоо/тандоодон чыгаруу.',
        ],

        'bulk_select_group' => [
            'label' => ':title тобун тандоо/тандоодон чыгаруу.',
        ],

        'search' => [
            'label' => 'Издөө',
            'placeholder' => 'Издөө',
            'indicator' => 'Издөө',
        ],

    ],

    'summary' => [

        'heading' => 'Жыйынтык',

        'subheadings' => [
            'all' => 'Бардык :label',
            'group' => ':group жыйынтыгы',
            'page' => 'Ушул бет',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Орточо',
            ],

            'count' => [
                'label' => 'Сан',
            ],

            'sum' => [
                'label' => 'Сумма',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Тартипти өзгөртүүнү бүтүрүү',
        ],

        'enable_reordering' => [
            'label' => 'Жазуулардын тартибин өзгөртүү',
        ],

        'filter' => [
            'label' => 'Фильтр',
        ],

        'group' => [
            'label' => 'Топтоо',
        ],

        'open_bulk_actions' => [
            'label' => 'Топтук аракеттер',
        ],

        'toggle_columns' => [
            'label' => 'Мамычаларды башкаруу',
        ],

    ],

    'empty' => [

        'heading' => ':model жазуулары жок',

        'description' => 'Баштоо үчүн :model түзүңүз.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'Фильтрлерди колдонуу',
            ],

            'remove' => [
                'label' => 'Фильтрди алып салуу',
            ],

            'remove_all' => [
                'label' => 'Бардык фильтрлерди алып салуу',
                'tooltip' => 'Бардык фильтрлерди алып салуу',
            ],

            'reset' => [
                'label' => 'Баштапкы абалга келтирүү',
            ],

        ],

        'heading' => 'Фильтрлер',

        'indicator' => 'Активдүү фильтрлер',

        'multi_select' => [
            'placeholder' => 'Бардыгы',
        ],

        'select' => [
            'placeholder' => 'Бардыгы',
        ],

        'trashed' => [

            'label' => 'Өчүрүлгөн жазуулар',

            'only_trashed' => 'Тек өчүрүлгөндөр',

            'with_trashed' => 'Өчүрүлгөндөр менен',

            'without_trashed' => 'Өчүрүлгөндөрсүз',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Топтоо',
                'placeholder' => 'Топтоо',
            ],

            'direction' => [

                'label' => 'Топтоо багыты',

                'options' => [
                    'asc' => 'Өсүү боюнча',
                    'desc' => 'Кемүү боюнча',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Жазууларды сүйрөп тартипке келтириңиз.',

    'selection_indicator' => [

        'selected_count' => '1 жазуу тандалды|:count жазуу тандалды',

        'actions' => [

            'select_all' => [
                'label' => 'Бардыгын тандоо (:count)',
            ],

            'deselect_all' => [
                'label' => 'Тандоодон чыгаруу',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Сортировка',
            ],

            'direction' => [

                'label' => 'Сортировка багыты',

                'options' => [
                    'asc' => 'Өсүү боюнча',
                    'desc' => 'Кемүү боюнча',
                ],

            ],

        ],

    ],

];
