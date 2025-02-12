<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Perfis extends Model
{
    use HasFactory;

    protected $table = 'perfis';

    public function subMenus()
    {
        return $this->belongsToMany(SubMenus::class, 'perfil_submenu', 'perfil_id', 'submenu_id');
    }

    public function perfis_dashboards()
    {
        return $this->belongsToMany(Dashboards::class, 'perfis_dashboard', 'perfis_id', 'dashboard_id');
    }
}
