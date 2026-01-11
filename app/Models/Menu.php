<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = ['heading','name','icon','url','parent_id','sort_order','is_active'];

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id', 'id')->orderBy('sort_order');
    }

    public function roles()
    {
        return $this->hasMany(MenuRole::class, 'menu_id', 'id');
    }
}
