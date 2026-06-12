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

        ProcessarPedidoJob::dispatch($pedido->id);

        return response()->json([
            'message' => 'Pedido criado e enviado para processamento.',
            'pedido' => $pedido
        ]);
    }
}
