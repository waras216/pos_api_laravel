<?php

namespace App\Services\Erp\Pac;

readonly class PacTimbradoResultado
{
    public function __construct(
        public string $uuid,
        public ?string $xmlPath = null,
        public ?string $pdfPath = null,
    ) {}
}
