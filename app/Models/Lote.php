<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lote extends Model
{
    use SoftDeletes,HasFactory;

    protected $fillable = [
        'nome',
        'quantidade_ingressos',
        'ativo',
    ];

    public function ingressos(): HasMany
    {
        return $this->hasMany(Ingresso::class);
    }
}
