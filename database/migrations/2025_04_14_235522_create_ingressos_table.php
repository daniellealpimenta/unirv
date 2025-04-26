<?php

use App\Models\Lote;
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
        Schema::create('ingressos', function (Blueprint $table) {
            $table->id();
            $table->float('valor_externo');
            $table->float('valor_aluno');
            $table->string("nome_evento");
            $table->date("data_validade");
            $table->boolean("disponivel")->default(true);
            $table->foreignIdFor(Lote::class)->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingressos');
    }
};
