<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class SubMenus extends Model
{
    use HasFactory;

    protected $table = 'submenus';

    public function perfis()
    {
        return $this->belongsToMany(Perfis::class, 'perfil_submenu', 'submenu_id', 'perfil_id');
    }
}
