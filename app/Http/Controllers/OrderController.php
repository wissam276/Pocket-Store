<?php

namespace App\Http\Controllers;

use App\Models\Basket;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
 class OrderController extends Controller {
    public function checkout(Request $request)
    {
        $user = Auth::user();
        
        // جلب عناصر السلة الخاصة بالصديق
        $basketItems = Basket::where('user_id', $user->id)->get();

        if ($basketItems->isEmpty()) {
            return response()->json(['message' => 'السلة فارغة لا يمكن إتمام الطلب'], 400);
        }

        DB::beginTransaction();
        try {
            $totalPrice = 0;
            foreach ($basketItems as $basketItem) {
                $item = Item::find($basketItem->item_id);
                if ($item->quantity < $basketItem->quantity) {
                    return response()->json([
                        'message' => "الكمية المطلوبة من المنتج ({$item->name}) غير متوفرة حالياً"
                    ], 422);
                }
                $price = $item->priceAfterDiscount ?? $item->price;
                $totalPrice += $price * $basketItem->quantity;
                $item->quantity -= $basketItem->quantity;
                $item->save();
            }
            Basket::where('user_id', $user->id)->delete();
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل طلبك بنجاح وجاري المراجعة',
                'order_summary' => [
                    'customer' => $user->first_name . ' ' . $user->second_name,
                    'total_account' => $totalPrice,
                    'payment_method' => 'الدفع عند الاستلام (COD)',
                    'status' => 'pending' 
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'حدث خطأ غير متوقع أثناء المعالجة', 'error' => $e->getMessage()], 500);
        }
    }
}

