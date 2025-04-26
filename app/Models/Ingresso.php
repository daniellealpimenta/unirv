<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ingresso extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'valor_aluno',
        'valor_externo',
        'nome_evento',
        'data_validade',
        'disponivel',
        'lote_id',
    ];

    public function lote(): BelongsTo
    {
        return $this->belongsTo(Lote::class);
    }

    public function historico(): HasOne
    {
        return $this->hasOne(HistoricoDeCompra::class);
    }
}
