<?php
// config/app.php - Parámetros globales de la aplicación

return [
    'name'        => 'Alcaldía Municipal de Lorica',
    'municipio'   => 'Santa Cruz de Lorica',
    'departamento'=> 'Córdoba',
    'pais'        => 'Colombia',
    'lema'        => 'Tradición, Progreso y Transparencia Institucional',
    'nit'         => '891.080.031-1',
    'direccion'   => 'Calle 101 # 25-10, Centro Histórico, Lorica, Córdoba',
    'telefono'    => '(+57) 604 773 8000',
    'email'       => 'contacto@lorica-cordoba.gov.co',
    'horario'     => 'Lunes a Viernes: 8:00 a.m. - 12:00 m. y 2:00 p.m. - 6:00 p.m.',
    'env'         => 'local',
    'base_url'    => '/alcaldia-lorica',
    'timezone'    => 'America/Bogota',
    'session_name'=> 'lorica_sess_id',
    'session_lifetime' => 7200, // 2 horas
    'uploads'     => [
        'max_size'     => 10 * 1024 * 1024, // 10MB
        'allowed_types'=> ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'],
        'allowed_mimes'=> [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ]
    ]
];
