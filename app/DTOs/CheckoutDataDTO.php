<?php

namespace App\DTOs;

class CheckoutDataDTO
{
    public float $subtotal;
    public float $impuestos;
    public float $costoEnvio;
    public float $total;
    public array $items;

    public function __construct(float $subtotal, float $impuestos, float $costoEnvio, array $items)
    {
        $this->subtotal = $subtotal;
        $this->impuestos = $impuestos;
        $this->costoEnvio = $costoEnvio;
        $this->total = $subtotal + $impuestos + $costoEnvio;
        $this->items = $items;
    }

    // Método para convertir el DTO en un array para la respuesta JSON
    public function toArray(): array
    {
        return [
            'resumen' => [
                'subtotal' => number_format($this->subtotal, 2, '.', ''),
                'impuestos' => number_format($this->impuestos, 2, '.', ''),
                'costo_envio' => number_format($this->costoEnvio, 2, '.', ''),
                'total' => number_format($this->total, 2, '.', ''),
            ],
            'items' => $this->items
        ];
    }
}
