<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Materiais extends Model
{    
    use HasFactory;

    public function fichastecnicasitens(): BelongsTo
    {
        return $this->belongsTo(Fichastecnicasitens::class, 'foreign_key');
    }
}
