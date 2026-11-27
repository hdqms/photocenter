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
        ],

        'employees' => [
            'table' => 'Employee',
            'id' => 'employee_id',
            'title' => 'Працівники',
            'fields' => [
                'full_name' => [
                    'label' => 'ПІБ працівника',
                    'type' => 'text',
                    'required' => true,
                    'max' => 100
                ],
                'role_id' => [
                    'label' => 'Посада',
                    'type' => 'select',
                    'required' => true,
                    'foreign' => [
                        'table' => 'Role',
                        'id' => 'role_id',
                        'label' => 'role_name'
                    ]
                ],
                'phone' => [
                    'label' => 'Номер телефону',
                    'type' => 'phone',
                    'required' => false,
                    'max' => 20
                ]
            ]
        ],

        'services' => [
            'table' => 'Service',
            'id' => 'service_id',
            'title' => 'Послуги',
            'fields' => [
                'service_name' => [
                    'label' => 'Назва послуги',
                    'type' => 'text',
                    'required' => true,
                    'max' => 100
                ],
                'category_id' => [
                    'label' => 'Категорія послуги',
                    'type' => 'select',
                    'required' => true,
                    'foreign' => [
                        'table' => 'ServiceCategory',
                        'id' => 'category_id',
                        'label' => 'category_name'
                    ]
                ],
                'base_price' => [
                    'label' => 'Вартість, грн',
                    'type' => 'decimal',
                    'required' => true
                ]
            ]
        ],

        'orders' => [
            'table' => 'Order',
            'id' => 'order_id',
            'title' => 'Замовлення',
            'fields' => [
                'client_id' => [
                    'label' => 'Клієнт',
                    'type' => 'select',
                    'required' => true,
                    'foreign' => [
                        'table' => 'Client',
                        'id' => 'client_id',
                        'label' => 'full_name'
                    ]
                ],
                'employee_id' => [
                    'label' => 'Працівник',
                    'type' => 'select',
                    'required' => true,
                    'foreign' => [
                        'table' => 'Employee',
                        'id' => 'employee_id',
                        'label' => 'full_name'
                    ]
                ],
                'order_date' => [
                    'label' => 'Дата замовлення',
                    'type' => 'datetime',
                    'required' => true
                ],
                'total_amount' => [
                    'label' => 'Загальна сума, грн',
                    'type' => 'decimal',
                    'required' => true,
                    'digits' => 8
                ],
                'status' => [
                    'label' => 'Статус',
                    'type' => 'text',
                    'required' => true,
                    'max' => 30
                ]
            ]
        ],

        'order_items' => [
            'table' => 'OrderItem',
            'id' => 'item_id',
            'title' => 'Позиції замовлень',
            'fields' => [
                'order_id' => [
                    'label' => 'Замовлення',
                    'type' => 'select',
                    'required' => true,
                    'foreign' => [
                        'table' => 'Order',
                        'id' => 'order_id',
                        'label' => 'order_date',
                        'with_id' => true
                    ]
                ],
                'service_id' => [
                    'label' => 'Послуга',
                    'type' => 'select',
                    'required' => true,
                    'foreign' => [
                        'table' => 'Service',
                        'id' => 'service_id',
                        'label' => 'service_name'
                    ]
                ],
                'quantity' => [
                    'label' => 'Кількість',
                    'type' => 'integer',
                    'required' => true
                ],
                'unit_price' => [
                    'label' => 'Ціна за одиницю, грн',
                    'type' => 'decimal',
                    'required' => true
                ]
            ]
        ],

        'bookings' => [
            'table' => 'Booking',
            'id' => 'booking_id',
            'title' => 'Бронювання',
            'fields' => [
                'client_id' => [
                    'label' => 'Клієнт',
                    'type' => 'select',
                    'required' => true,
                    'foreign' => [
                        'table' => 'Client',
                        'id' => 'client_id',
                        'label' => 'full_name'
                    ]
                ],
                'hall_id' => [
                    'label' => 'Фотозал',
                    'type' => 'select',
                    'required' => true,
                    'foreign' => [
                        'table' => 'StudioHall',
                        'id' => 'hall_id',
                        'label' => 'hall_name'
                    ]
                ],
                'employee_id' => [
                    'label' => 'Працівник',
                    'type' => 'select',
                    'required' => true,
                    'foreign' => [
                        'table' => 'Employee',
                        'id' => 'employee_id',
                        'label' => 'full_name'
                    ]
                ],
                'start_time' => [
                    'label' => 'Початок бронювання',
                    'type' => 'datetime',
                    'required' => true
                ],
                'end_time' => [
                    'label' => 'Завершення бронювання',
                    'type' => 'datetime',
                    'required' => true
                ],
                'total_cost' => [
                    'label' => 'Загальна вартість, грн',
                    'type' => 'decimal',
                    'required' => true,
                    'digits' => 8
                ],
                'status' => [
                    'label' => 'Статус',
                    'type' => 'text',
                    'required' => true,
                    'max' => 30
                ]
            ]
        ],

        'payments' => [
            'table' => 'Payment',
            'id' => 'payment_id',
            'title' => 'Оплати',
            'fields' => [
                'order_id' => [
                    'label' => 'Замовлення',
                    'type' => 'select',
                    'required' => true,
                    'foreign' => [
                        'table' => 'Order',
                        'id' => 'order_id',
                        'label' => 'order_date',
                        'with_id' => true
                    ]
                ],
                'amount' => [
                    'label' => 'Сума оплати, грн',
                    'type' => 'decimal',
                    'required' => true,
                    'digits' => 8,
                    'positive' => true
                ],
                'payment_date' => [
                    'label' => 'Дата оплати',
                    'type' => 'datetime',
                    'required' => true
                ],
                'payment_type' => [
                    'label' => 'Тип оплати',
                    'type' => 'text',
                    'required' => true,
                    'max' => 30
                ]
            ]
        ],

        'print_files' => [
            'table' => 'PrintFile',
            'id' => 'file_id',
            'title' => 'Файли друку',
            'fields' => [
                'item_id' => [
                    'label' => 'Позиція замовлення',
                    'type' => 'select',
                    'required' => true,
                    'foreign' => [
                        'table' => 'OrderItem',
                        'id' => 'item_id',
                        'label' => 'order_id',
                        'with_id' => true,
                        'label_prefix' => 'Замовлення №'
                    ]
                ],
                'format_id' => [
                    'label' => 'Формат друку',
                    'type' => 'select',
                    'required' => true,
                    'foreign' => [
                        'table' => 'PrintFormat',
                        'id' => 'format_id',
                        'label' => 'format_name',
                        'with_id' => true
                    ]
                ],
                'file_path' => [
                    'label' => 'Шлях до файлу',
                    'type' => 'text',
                    'required' => true,
                    'max' => 255
                ],
                'copies_count' => [
                    'label' => 'Кількість копій',
                    'type' => 'integer',
                    'required' => true
                ],
                'crop_type' => [
                    'label' => 'Тип обрізки',
                    'type' => 'text',
                    'required' => true,
                    'max' => 20
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

function foreign_label_sql(
    PDO $pdo,
    array $foreign,
    string $alias
): string {
    $id = $alias . '.`' . $foreign['id'] . '`';
    $label = $alias . '.`' . $foreign['label'] . '`';

    if (empty($foreign['with_id'])) {
        return $label;
    }

    $prefix = $pdo->quote(
        $foreign['label_prefix'] ?? ''
    );

    return "CONCAT('#', $id, ' — ', $prefix, $label)";
}
