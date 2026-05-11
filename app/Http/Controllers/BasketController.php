<?php

namespace App\Http\Controllers;

use App\Models\Basket;
use App\Http\Requests\StoreBasketRequest;
use App\Http\Requests\UpdateBasketRequest;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class BasketController extends Controller
{
    public function store(Request $request)
    {
    $request->validate([
        'item_id' => 'required|exists:items,id',
        'quantity'   => 'required|integer|min:1'
    ]);

    $user = Auth::user();
    $item = Item::find($request->item_id);

    // فحص الكمية
    if ($item->quantity < $request->quantity) {
        return response()->json(['message' => 'الكمية المطلوبة غير متاحة في المخزون'], 422);
    }

    $basketItem = Basket::where('user_id', $user->id)
                    ->where('item_id', $request->item_id)
                    ->first();

    if ($basketItem) {
        // تحديث الكمية (يمكن جمع الكمية الجديدة مع القديمة أو استبدالها)
        $basketItem->quantity += $request->quantity; // أو $basketItem->quantity = $request->quantity;
        $basketItem->save();
    } else {
        $basketItem = Basket::create([
            'user_id'    => $user->id,
            'item_id' => $request->item_id,
            'quantity'   => $request->quantity,
        ]);
    }

    return response()->json([
        'message' => 'تمت إضافة المنتج إلى السلة',
        'basket' => $basketItem->load('item')
    ]);
}
//---------------------------------------------------------------
public function index()
{
    $basketItems = Basket::where('user_id', Auth::id())
                     ->with('item')
                     ->get();

    return response()->json([
        'data' => $basketItems,
        'total' => $basketItems->sum(fn($item) => $item->item->price * $item->quantity)
    ]);
}
}
