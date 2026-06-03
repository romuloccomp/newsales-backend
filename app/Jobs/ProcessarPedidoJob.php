<?php
namespace App\Jobs;

use App\Models\Pedido;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessarPedidoJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $pedidoId
    ) {}

    public function handle(): void
    {
        $pedido = Pedido::find($this->pedidoId, "*");

        if (!$pedido) {
            return;
        }

        $pedido->update(['status' => 'processando']);

        // Simula processamento pesado
        sleep(30);

        $pedido->update([
            'status' => 'processado'
        ]);

        logger()->info("Pedido {$pedido->id} processado.");
    }
}
