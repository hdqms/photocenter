<?php

function entities(): array
{
    return [
        'categories' => [
            'table' => 'ServiceCategory',
            'id' => 'category_id',
            'title' => 'Категорії послуг',
            'fields' => [
                'category_name' => [
                    'label' => 'Назва категорії',
                    'type' => 'text',
                    'required' => true,
                    'max' => 100
                ],
                'description' => [
                    'label' => 'Опис',
                    'type' => 'textarea',
                    'required' => false,
                    'max' => 255
                ]
            ]
        ],

        'formats' => [
            'table' => 'PrintFormat',
            'id' => 'format_id',
            'title' => 'Формати друку',
            'fields' => [
                'format_name' => [
                    'label' => 'Назва формату',
                    'type' => 'text',
                    'required' => true,
                    'max' => 20
                ],
                'paper_type' => [
                    'label' => 'Тип паперу',
                    'type' => 'text',
                    'required' => true,
                    'max' => 30
                ]
            ]
        ],

        'halls' => [
            'table' => 'StudioHall',
            'id' => 'hall_id',
            'title' => 'Фотозали',
            'fields' => [
                'hall_name' => [
                    'label' => 'Назва фотозалу',
                    'type' => 'text',
                    'required' => true,
                    'max' => 50
                ],
                'hourly_rate' => [
                    'label' => 'Вартість за годину, грн',
                    'type' => 'decimal',
                    'required' => true
                ],
                'capacity' => [
                    'label' => 'Місткість, осіб',
                    'type' => 'integer',
                    'required' => true
                ]
            ]
        ]
    ];
}

function get_entity(): array
{
    $key = $_GET['entity'] ?? '';

    $all = entities();

    if (!is_string($key) || !isset($all[$key])) {
        http_response_code(404);
        exit('Розділ не знайдено.');
    }

    return $all[$key];
}
