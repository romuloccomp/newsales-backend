<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('items_order', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->timestamps();
            $table->string('cod_refer')->nullable();
            $table->float('preco_min');
            $table->text('vsearch');
            $table->string('it_codigo');
            $table->integer('quant_min');
            $table->string('sub_familia');
            $table->float('peso_liquido');
            $table->text('descricao');
            $table->tinyText('un');
            $table->float('preco_venda');
            $table->integer('qt_estoque');
            $table->string('nr_tabpre');
            $table->string('fm_codigo');
            $table->string('fm_descricao');
            $table->integer('quantidade');
            $table->float('preco');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items_orders');
    }
};
