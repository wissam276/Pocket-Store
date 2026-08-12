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
//-------------------------------------------------------------------------
public function toggleProductStatus(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:items,id',
        ]);
        $item = Item::findOrFail($request->product_id);
        $item->availability = !$item->availability; // أو الاعتماد على حقل تفعيل خاص إن وجد
        $item->save();

        return response()->json([
            'message' => 'Product status updated successfully',
            'product' => $item
        ], 200);
    }
    //------------------------------------------------------
    public function toggleCatigoryStatus(Request $request)
    {
        $id=$request->input('category_id');
        $category = Category::findOrFail($id);
        // افترض أن لديك حقل is_active في جدول التصنيفات
        $category->is_active = !$category->is_active;
        $category->save();

        return response()->json([
            'status' => true,
            'message' => 'Category status updated successfully',
            'data' => $category
        ], 200);
    }
    //------------------------------------------------------
    public function salesReport()
    {
        // استخراج المبيعات مرتبة حسب الأشهر للعام الحالي
        $monthlySales = Order::selectRaw('SUM(total_price) as total, MONTH(created_at) as month')
            ->where('status', '!=', 'Cancelled')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $totalSalesAllTime = Order::where('status', '!=', 'Cancelled')->sum('total_price');

        return response()->json([
            'status' => true,
            'total_sales' => $totalSalesAllTime,
            'monthly_sales' => $monthlySales
        ], 200);
    }
    //---------------------------------------------------------------
    public function customerDetailsWithOrders($id)
    {
        $customer = User::where('id', $id)->where('role', 'customer')->with('orders')->firstOrFail();
        
        // حساب إجمالي مشتريات العميل للطلبات غير الملغاة
        $totalSpent = $customer->orders()->where('status', '!=', 'Cancelled')->sum('total_price');

        return response()->json([
            'status' => true,
            'customer' => $customer,
            'total_spent' => $totalSpent,
            'orders_count' => $customer->orders->count()
        ], 200);
    }
}
