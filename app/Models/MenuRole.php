<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuRole extends Model
{
    use HasFactory;

    protected $table = 'menu_role'; // <- important
    protected $fillable = ['menu_id', 'role'];
    public $timestamps = false; // optional if you don’t use created_at/updated_at
}
