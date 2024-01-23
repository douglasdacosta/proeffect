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
}
