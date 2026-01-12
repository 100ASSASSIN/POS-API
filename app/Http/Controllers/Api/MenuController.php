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

        $menus = Menu::whereNull('parent_id')          // Top-level menus
            ->where('is_active', 1)                    // Only active menus
            ->whereHas('roles', function ($q) use ($role) {
                $q->where('role', $role);
            })
            ->with(['children' => function ($q) use ($role) {
                $q->where('is_active', 1)              // Only active children
                  ->whereHas('roles', function ($q2) use ($role) {
                      $q2->where('role', $role);
                  })
                  ->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get()
            ->map(function ($menu) {
                // Remove empty children relation
                if ($menu->children->isEmpty()) {
                    $menu->unsetRelation('children');
                }
                return $menu;
            });

        return response()->json($menus);
    }
}
