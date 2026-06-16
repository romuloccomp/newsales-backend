<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $results = Order::with('items')->get();
        return response()->json($results);
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
    public function show(string $order)
    {
        $results = Order::with('items')
            ->findOrFail($order);
        return response()->json($results);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {

        // print_r($request->ped_venda['nr_pedcli']);
        // echo "request: ";
        // dd($request);

        $data  = $request->validate([
                    'ped_venda'              => 'required|array',
                    'ped_venda.nr_pedcli'    => 'sometimes|required|string|max:255',
                    'ped_venda.cod_rep'      => 'sometimes|required|string|max:255',
                    'ped_venda.cod_emitente' => 'sometimes|required|integer',
                    'ped_venda.nr_tabpre'    => 'sometimes|required|string|max:255',
                    'ped_venda.dt_entrega'   => 'sometimes|required|date',
                    'ped_venda.frete'        => 'sometimes|required|boolean',
                    'ped_venda.tipo_pedido'  => 'sometimes|required|string|in:venda,bonificação,Venda,Bonificação',
                    'ped_venda.observacoes'  => 'nullable|string',
                    'ped_venda.situacao_avaliacao'  => 'nullable|string|in:pendente,aprovado,rejeitado,avaliação',
                    'ped_venda.avaliador'           => 'nullable|string|max:255',
                    'ped_venda.avaliado_em'         => 'nullable|blank_or:date',
                    'ped_venda.descricao_avaliacao' => 'nullable|string',
                    'ped_venda.email_supervisor'    => 'nullable|email|max:255',
                    'ped_venda.email_gerente'       => 'nullable|email|max:255',
                    'ped_venda.situacao_integracao' => 'nullable|string',
                    'ped_venda.retorno_integracao'  => 'nullable|string',
                ]);

        $order->update($data['ped_venda']);

        if ($request->has('ped_item')) {

            // Opcional: Coleta os IDs enviados para deletar itens que sumiram do frontend
            $itensEnviadosIds = collect($request->ped_item)->pluck('id')->filter()->toArray();

            // Deleta do banco APENAS os itens que foram removidos na tela pelo usuário
            $order->items()->whereNotIn('id', $itensEnviadosIds)->delete();

            foreach ($request->ped_item as $itemData) {
                $order->items()->updateOrCreate(
                    // Condição para encontrar o registro: Se tiver ID, atualiza. Se não tiver ID (nulo), cria um novo.
                    ['id' => $itemData['id'] ?? null],

                    // Dados que serão atualizados ou inseridos no banco
                    [
                        'cod_refer'    => !empty($itemData['cod_refer']) ? $itemData['cod_refer'] : null,
                        'preco_min'    => $itemData['preco_min'] ?? 0,
                        'vsearch'      => $itemData['vsearch'] ?? null,
                        'it_codigo'    => $itemData['it_codigo'],
                        'quant_min'    => $itemData['quant_min'] ?? 0,
                        'sub_familia'  => $itemData['sub_familia'] ?? null,
                        'peso_liquido' => $itemData['peso_liquido'] ?? 0,
                        'descricao'    => $itemData['descricao'],
                        'un'           => $itemData['un'],
                        'preco_venda'  => $itemData['preco_venda'] ?? 0,
                        'qt_estoque'   => $itemData['qt_estoque'] ?? 0,
                        'nr_tabpre'    => $itemData['nr_tabpre'] ?? null,
                        'fm_codigo'    => $itemData['fm_codigo'] ?? null,
                        'fm_descricao' => $itemData['fm_descricao'] ?? null,
                        'quantidade'   => $itemData['quantidade'],
                        'preco'        => $itemData['preco'],
                    ]
                );
            }
        }

        return response()->json($order->load('items'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
        $result = $order->delete();
        return $result;
    }
}
