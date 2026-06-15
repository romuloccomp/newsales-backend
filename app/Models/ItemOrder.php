<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemOrder extends Model
{

    protected $fillable = [
        'order_id',
        'cod_refer',
        'preco_min',
        'vsearch',
        'it_codigo',
        'quant_min',
        'sub_familia',
        'peso_liquido',
        'descricao',
        'un',
        'preco_venda',
        'qt_estoque',
        'nr_tabpre',
        'fm_codigo',
        'fm_descricao',
        'quantidade',
        'preco'
    ];

    public function order() {
        return $this->belongsTo(Order::class);
    }
}
