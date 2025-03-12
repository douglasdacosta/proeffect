<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Pessoas extends Model
{
    use HasFactory;
    protected $table = 'pessoas';
    protected $fillable = ['hash_consulta'];

    public function pessoas(){
        return $this->belongsTo(Pedidos::class);
    }

    public function pedidos()
    {
        return $this->hasMany(Pedidos::class);
    }

}
