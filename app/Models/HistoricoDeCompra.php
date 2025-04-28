<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class HistoricoDeCompra extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'comprovante_de_pagamento',
        'status_do_pagamento',
        'user_id',
        'ingresso_id',
        'payment_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ingresso(): BelongsTo
    {
        return $this->belongsTo(Ingresso::class);
    }
}
