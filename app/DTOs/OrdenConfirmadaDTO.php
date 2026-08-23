<?php

namespace App\DTOs;

class OrdenConfirmadaDTO
{
    public int $ordenId;
    public string $estado;
    public string $metodoPago;
    public string $direccionEnvio;
    public float $total;
    public string $fecha;

    public function __construct(int $ordenId, string $metodoPago, string $direccionEnvio, float $total)
    {
        $this->ordenId = $ordenId;
        $this->estado = "Completado / Pagado";
        $this->metodoPago = $metodoPago;
        $this->direccionEnvio = $direccionEnvio;
        $this->total = $total;
        $this->fecha = now()->toDateTimeString();
    }

    public function toArray(): array
    {
        return [
            'mensaje' => '¡Compra confirmada con éxito!',
            'orden' => [
                'id' => $this->ordenId,
                'estado' => $this->estado,
                'metodo_pago' => $this->metodoPago,
                'direccion_envio' => $this->direccionEnvio,
                'total_pagado' => number_format($this->total, 2, '.', ''),
                'fecha_transaccion' => $this->fecha
            ]
        ];
    }
}