<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;


class Pedidos extends Model
{
    use HasFactory;

    protected $table = 'pedidos';

    public function tabelaStatus(): HasOne
    {
        return $this->HasOne(Status::class, 'id', 'status_id');
    }

    public function tabelaFichastecnicas(): HasOne
    {
        return $this->HasOne(Fichastecnicas::class, 'id', 'fichatecnica_id');
    }

    public function tabelaPessoas(): HasOne
    {
        return $this->HasOne(Pessoas::class, 'id', 'pessoas_id');
    }


    public function tabelaTransportes(): HasOne
    {
        return $this->HasOne(Transportes::class, 'id', 'transporte_id');
    }


    public function tabelaPrioridades(): HasOne
    {
        return $this->HasOne(Prioridades::class, 'id', 'prioridade_id');
    }

}
