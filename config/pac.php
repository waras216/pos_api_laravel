<?php

// Configuración del PAC (Proveedor Autorizado de Certificación) usado para
// timbrar CFDI reales ante el SAT. Por defecto no hay ninguno conectado
// ("null"): el módulo de Facturación funciona igual para el modo "registro
// interno", pero cualquier intento de timbrado real falla con un mensaje
// claro hasta que se configure un driver real (ver
// App\Services\Erp\Pac\PacDriverInterface) y se registre en
// AppServiceProvider::register().
//
// Para conectar un PAC real: implementar un driver (ej. FacturamaDriver)
// que resuelva PacDriverInterface, agregar sus credenciales abajo, y
// registrar el binding condicional en AppServiceProvider.
return [
    'driver' => env('PAC_DRIVER', 'null'),

    'drivers' => [
        'facturama' => [
            'api_key' => env('PAC_FACTURAMA_API_KEY'),
            'api_secret' => env('PAC_FACTURAMA_API_SECRET'),
            'sandbox' => env('PAC_FACTURAMA_SANDBOX', true),
        ],
        'sw' => [
            'api_key' => env('PAC_SW_API_KEY'),
            'sandbox' => env('PAC_SW_SANDBOX', true),
        ],
        'finkok' => [
            'usuario' => env('PAC_FINKOK_USUARIO'),
            'password' => env('PAC_FINKOK_PASSWORD'),
            'sandbox' => env('PAC_FINKOK_SANDBOX', true),
        ],
    ],

    // Certificado de Sello Digital del emisor (CSD), requerido por
    // cualquier PAC para timbrar. Se sube una vez por tenant.
    'csd' => [
        'cer_path' => env('PAC_CSD_CER_PATH'),
        'key_path' => env('PAC_CSD_KEY_PATH'),
        'password' => env('PAC_CSD_PASSWORD'),
    ],
];
