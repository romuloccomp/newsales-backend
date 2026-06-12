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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('nr_pedcli');
            $table->string('cod_rep')->nullable(false);
            $table->string('cod_emitente')->nullable(false);
            $table->string('nr_tabpre')->nullable(false);
            $table->date('dt_entrega');
            $table->text('observacoes')->nullable();
            $table->boolean('frete');
            $table->string('tipo_pedido'); //Bonificação | Venda
            $table->string('situacao_avaliacao')->default("pendente"); //pendente | aprovado | rejeitado
            $table->string('avaliador')->nullable();
            $table->timestamp('avaliado_em')->nullable();
            $table->text('descricao_avaliacao')->nullable();
            $table->string('email_supervisor')->nullable();
            $table->string('email_gerente')->nullable();
            $table->string('nr_pedido')->nullable();
            $table->text('situacao_integracao')->nullable();
            $table->text('retorno_integracao')->nullable();
            $table->float('valor_total')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
