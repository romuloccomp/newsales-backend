<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Order::with('items')->get();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $results = DB::transaction(function () use ($request) {
            $ped_venda = $request->ped_venda;
            $items = $request->ped_item;

            $order = Order::create([
                'nr_pedcli' => $ped_venda['nr_pedcli'],
                'cod_rep' => $ped_venda['cod_rep'],
                'cod_emitente' => $ped_venda['cod_emitente'],
                'nr_tabpre' => $ped_venda['nr_tabpre'],
                'dt_entrega' => $ped_venda['dt_entrega'],
                'observacoes' => $ped_venda['observacoes'],
                'tipo_pedido' => $ped_venda['tipo_pedido'],
                'situacao_avaliacao' => $ped_venda['situacao_avaliacao'],
                'avaliador' => $ped_venda['avaliador'],
                'avaliado_em' => $ped_venda['avaliado_em'],
                'descricao_avaliacao' => $ped_venda['descricao_avaliacao'],
                'email_supervisor' => $ped_venda['email_supervisor'],
                'email_gerente' => $ped_venda['email_gerente'],
                'nr_pedido' => $ped_venda['nr_pedido'],
                'situacao_integracao' => $ped_venda['situacao_integracao'],
                'retorno_integracao' => $ped_venda['retorno_integracao'],
                'frete' => $ped_venda['frete'],
                'valor_total' => 0
            ]);

            $total = 0;

            foreach ($items as $item) {
                $subtotal = $item['quantidade'] * $item['preco_venda'];

                $order->items()->create([
                    "cod_refer" => $item['cod_refer'],
                    "preco_min" => $item['preco_min'],
                    "vsearch" => $item['vsearch'],
                    "it_codigo" => $item['it_codigo'],
                    "quant_min" => $item['quant_min'],
                    "sub_familia" => $item['sub_familia'],
                    "peso_liquido" => $item['peso_liquido'],
                    "descricao" => $item['descricao'],
                    "un" => $item['un'],
                    "preco_venda" => $item['preco_venda'],
                    "qt_estoque" => $item['qt_estoque'],
                    "nr_tabpre" => $item['nr_tabpre'],
                    "fm_codigo" => $item['fm_codigo'],
                    "fm_descricao" => $item['fm_descricao'],
                    "quantidade" => $item['quantidade'],
                    "preco" => $item['preco'],
                ]);

                $total += $subtotal;
            }

            $order->update([
                'valor_total' => $total
            ]);

            return $order->load('items');
        });

        return $results;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Order::with('items')
            ->findOrFail($id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }
}
