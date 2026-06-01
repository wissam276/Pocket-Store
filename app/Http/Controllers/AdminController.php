<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class AdminController extends Controller
{
  public function acceptItem(Request $request)
    {
        $user=Auth::user();
        $request->validate([
            'item_id' => 'required|exists:items,id',
        ]);
        $id = $request->input('item_id');
        $item = Item::findOrFail($id);
        $item->accepted = 'accepted';
        $item->save();
        return response()->json($item, 200);
    }
//--------------------------------------------------
    public function rejectItem(Request $request)
    {
        $user=Auth::user();
        $request->validate([
            'item_id' => 'required|exists:items,id',
        ]);
        $id = $request->input('item_id');
        $item = Item::findOrFail($id);
        $item->accepted = 'rejected';
        $item->save();
        return response()->json($item, 200);}
//-------------------------------------------------
        public function toggleUserStatus(Request $request)
        {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'is_active' => 'required|boolean'
        ]);

        $user = User::findOrFail($request->user_id);
        if($user->id === Auth::id()) {
            return response()->json(['message' => 'لا يمكنك حظر حسابك الشخصي'], 403);
        }
        $user->is_active = $request->is_active;
         $user->save();

        return response()->json(['message' => 'تم تحديث حالة المستخدم بنجاح'], 200);
        }
//-------------------------------------------------
public function dashboardStats()
    {
        return response()->json([
            'total_customers' => User::where('role', 'customer')->count(),
            'total_products' => Item::count(),
            'pending_products' => Item::where('accepted', 'pending')->count(),
            'low_stock_products' => Item::where('quantity', '<', 5)->get(), 
        ], 200);
    }
//---------------------------------------------------------------------

}
