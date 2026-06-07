<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Category;
use App\Models\Order;

class AdminController extends Controller
{

//-------------------------------------------------
    public function toggleUserStatus(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'is_active' => 'required|boolean'
        ]);

        $user = User::findOrFail($request->user_id);
        if($user->id === Auth::id()) {
            return response()->json(['message' => 'you cannot block '], 403);
        }
        $user->is_active = $request->is_active;
        $user->save();

        return response()->json(['message' => 'user status updated successfully'], 200);
    }
//-------------------------------------------------
    public function dashboardStats()
    {
        return response()->json([
            'total_customers' => User::where('role', 'customer')->count(),
            'total_products' => Item::count(),
            'total_orders'=>Order::count(),
            'low_stock_products' => Item::where('quantity', '<', 5)->get(),

        ], 200);
    }
//---------------------------------------------------------------------


public function listUsers(){

        $users = User::where('is_admin',false)
            ->select(['id','name','email','role','is_active','created_at'])
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $users,
            'status' => 200,
            'users' => User::all()
        ]);
}
}
