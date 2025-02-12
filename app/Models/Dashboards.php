<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Dashboards extends Model
{
    use HasFactory;

    protected $table = 'dashboards';

    public function perfis()
    {
        return $this->belongsToMany(Perfis::class, 'perfis_dashboard', 'dashboard_id', 'perfis_id');
    }

}
