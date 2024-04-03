<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Permissoes extends Model
{
    use HasFactory;

    protected $table = 'permissoes';

    public function tabelaTelas(): HasOne
    {
        return $this->HasOne(Telas::class, 'id', 'telas_id');
    }

    public function tabelaUsers(): HasOne
    {
        return $this->HasOne(User::class, 'id', 'users_id');
    }

    public function tabelaDescricaoPermicoes(): HasOne
    {
        return $this->HasOne(DescricaoPermissoes::class, 'id', 'permissoes_id');
    }
}

