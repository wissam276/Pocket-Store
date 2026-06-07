<?php

namespace App\Http\Controllers;

use App\Http\Resources\BasketItemResource;
use App\Models\Basket;
use App\Http\Requests\StoreBasketRequest;
use App\Http\Requests\UpdateBasketRequest;
use App\Models\basketItem;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use ParagonIE\ConstantTime\Base32;


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
            return response()->json(['message' => 'the required quantity is not available !'], 422);
        }

        $basketItem = Basket::where('user_id', $user->id)
            ->where('item_id', $request->item_id)
            ->first();

        if ($basketItem) {

            $basketItem->quantity += $request->quantity;
            $basketItem->save();
        } else {
            $basketItem = Basket::create([
                'user_id'    => $user->id,
                'item_id' => $request->item_id,
                'quantity'   => $request->quantity,
            ]);
        }

        return response()->json([
            'message' => 'Item added to basket.',
            'basket' => $basketItem->load('item')
        ]);
    }
//---------------------------------------------------------------


    public function update(Request $request, $id)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        $basketItem = Basket::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$basketItem) {
            return response()->json(['message' => 'the element is not exist !'], 404);
        }

        $item = Item::find($basketItem->item_id);
        if (!$item || $request->quantity > $item->quantity) {
            return response()->json(['message' => 'required quantity is not available'], 422);
        }

        $basketItem->update(['quantity' => $request->quantity]);
        return response()->json(['message' => 'updated successfully']);
    }





    public function destroy($id)
    {
        $basketItem = Basket::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $basketItem->delete();

        return response()->json(['message' => 'Item deleted from the basket successfully']);
    }


    public function index()
    {
        $basketItems = Basket::where('user_id', auth()->id())->with('item')->get();

        if ($basketItems->isEmpty()) {
            return response()->json(['message' => 'basket is empty !'], 200);
        }

        // 2. حساب الإجمالي
        $total = $basketItems->sum(function ($basketItem) {
            $price = $basketItem->item->priceAfterDiscount ?? $basketItem->item->price;
            return $price * $basketItem->quantity;
        });

        return response()->json([
            'status' => true,
            'data'   => BasketItemResource::collection($basketItems),
            'total'  => $total
        ], 200);
    }


}
