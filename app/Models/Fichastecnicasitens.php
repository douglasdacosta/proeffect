<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\HasOne;

class Fichastecnicasitens extends Model
{    
    use HasFactory;

    protected $table = 'ficha_tecnica_itens';


    // public function materiais(): BelongsTo
    // {
    //     return $this->belongsTo(Materiais::class);
    // }


//     public function fichastecnicasitens()
// {
//      return $this->hasOne(Fichastecnicasitens::class,"materiais_id", "id");
// }
    public function materiais(): HasOne
    {
        return $this->hasOne(Materiais::class, 'id', 'materiais_id');
    }
}
