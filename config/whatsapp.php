<?php

// Sin proveedor conectado por defecto ("null") — ver
// App\Services\Whatsapp\NullWhatsappDriver. Para conectar uno real:
// implementar un driver contra WhatsappDriverInterface y registrarlo en
// AppServiceProvider::register(), igual que con el PAC de facturación.
return [
    'driver' => env('WHATSAPP_DRIVER', 'null'),

    'drivers' => [
        // Meta Cloud API: requiere un número de WhatsApp Business verificado.
        'meta' => [
            'access_token' => env('WHATSAPP_META_ACCESS_TOKEN'),
            'phone_number_id' => env('WHATSAPP_META_PHONE_NUMBER_ID'),
        ],
        // Alternativa con menos fricción de setup, pero de pago por mensaje.
        'twilio' => [
            'account_sid' => env('WHATSAPP_TWILIO_ACCOUNT_SID'),
            'auth_token' => env('WHATSAPP_TWILIO_AUTH_TOKEN'),
            'from' => env('WHATSAPP_TWILIO_FROM'), // ej. 'whatsapp:+14155238886'
        ],
        // WhatsApp Web no oficial (una sesión por tenant, QR desde
        // Integraciones). Va contra los Términos de Servicio de WhatsApp
        // -- ver whatsapp_baileys_service/index.js.
        'baileys' => [
            'url' => env('WHATSAPP_BAILEYS_URL', 'http://whatsapp_baileys:3001'),
        ],
    ],
];
