<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function sidebar(Request $request)
    {
        $role = $request->user()->role;

        $menus = Menu::whereNull('parent_id')  // Top-level menus
            ->whereHas('roles', fn ($q) => $q->where('role', $role))
            ->with(['children' => function ($q) use ($role) {
                $q->whereHas('roles', fn ($q2) => $q2->where('role', $role))
                  ->orderBy('sort_order');
            }])
            ->orderBy('sort_order')  // Top-level order
            ->get()
            ->map(function ($menu) {
                // Remove children if empty
                if ($menu->children->isEmpty()) {
                    $menu->unsetRelation('children');
                }
                return $menu;
            });

        return response()->json($menus);
    }
}
