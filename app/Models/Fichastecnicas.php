<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\HasOne;


class Fichastecnicas extends Model
{    
    use HasFactory;

    protected $table = 'ficha_tecnica';

    public function pedidos(): HasOne
    {
        return $this->hasOne(Pedidos::class, 'id', 'pedidos_id');
    }
}
