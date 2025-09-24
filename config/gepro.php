<?php

return [
    // Precio estimado del alimento por kilogramo (COP)
    'feed_price_per_kg' => 2500.0,

    // Costos estimados por tratamiento de sanidad (COP)
    'health_costs' => [
        'vaccine' => 1500.0,
        'dewormer' => 1200.0,
        'vitamin' => 800.0,
        'default' => 1000.0,
    ],

    // Precio estimado de compra por ave (COP)
    'bird_purchase_price' => 20000.0,

    // Ingresos estimados
    'egg_price_per_unit' => 500.0,   // precio por huevo
    'bird_sale_price' => 50000.0,    // precio por ave vendida
];
