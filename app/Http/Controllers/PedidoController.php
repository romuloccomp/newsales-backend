<?php
// app/Http/Controllers/PedidoController.php

namespace App\Http\Controllers;

use App\Jobs\ProcessarPedidoJob;
use App\Models\Pedido;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    public function store(Request $request)
    {
        $pedido = Pedido::create([
            'cliente' => $request->cliente,
            'valor_total' => $request->valor_total,
            'status' => 'status_pedido1',
        ]);

        // $pedido2 = Pedido::create([
        //     'cliente' => $request->cliente,
        //     'valor_total' => $request->valor_total,
        //     'status' => 'status_pedido2',
        // ]);

        // Envia para fila
        ProcessarPedidoJob::dispatch($pedido->id);
        // ProcessarPedidoJob::dispatch($pedido2->id)->delay(now()->addMinutes(5));

        return response()->json([
            'message' => 'Pedido criado e enviado para processamento.',
            'pedido' => $pedido
        ]);
    }
}
