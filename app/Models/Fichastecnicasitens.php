<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Fichastecnicasitens extends Model
{
    use HasFactory;

    protected $table = 'ficha_tecnica_itens';

    public function materiais(): HasOne
    {
        return $this->hasOne(Materiais::class, 'id', 'materiais_id');
    }

    public function tabelaFichastecnicasitens(): HasMany
    {
        return $this->HasMany(Fichastecnicasitens::class, 'id', 'fichatecnica_id');
    }

    public function tabelaMateriais(): HasOne
    {
        return $this->HasOne(Materiais::class, 'id', 'materiais_id');
    }
}
