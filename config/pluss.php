<?php

return [
    /*
     * Datos de contacto de respaldo. La fuente de verdad es la tabla
     * site_settings, editable desde el panel; esto solo cubre el arranque
     * en limpio y el entorno de pruebas.
     */
    'whatsapp' => env('PLUSS_WHATSAPP', '56951481009'),
    'email' => env('PLUSS_EMAIL', 'contacto@plussautos.cl'),

    /* Cantidad de vehículos por página en el catálogo. */
    'per_page' => 12,

    /* Vehículos destacados en la portada. */
    'home_featured' => 6,
];
