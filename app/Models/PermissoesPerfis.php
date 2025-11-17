<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermissoesPerfis extends Model
{
    use HasFactory;

    protected $table = 'permissoes_perfis';

    protected $fillable = [
        'perfil_id',
        'acao_id',
        'submenus_id',
    ];
}
