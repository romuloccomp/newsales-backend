<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    protected $table = 'orders';

    protected $fillable = [
        'nr_pedcli',
        'cod_rep',
        'cod_emitente',
        'nr_tabpre',
        'dt_entrega',
        'observacoes',
        'tipo_pedido',
        'situacao_avaliacao',
        'avaliador',
        'avaliado_em',
        'descricao_avaliacao',
        'email_supervisor',
        'email_gerente',
        'nr_pedido',
        'situacao_integracao',
        'retorno_integracao',
        'frete',
        'valor_total'
    ];

    public function items()
    {
        return $this->hasMany(ItemOrder::class);
    }
}
