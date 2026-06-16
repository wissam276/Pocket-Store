<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Basket;
use App\Models\Item;
use App\Models\Order;
use App\Models\orderItems;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class OrderController extends Controller {


    public function index()
    {
        $orders = Order::with('user')->latest()->paginate(10);

        return response()->json($orders);
    }


    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->update($request->only(['status']));

        return response()->json(['message' => 'order updated successfully']);
    }


    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return response()->json(['message' => 'order deleted successfully']);
    }



    public function checkout(Request $request)
    {

        $request->validate([
            'shipping_address' => 'sometimes|string|max:255',
            'payment_method'   => 'required|in:COD,card',
        ]);

        $user = $request->user();
        $basketItems = Basket::where('user_id', $user->id)->get();
        $finalShippingAddress = $request->shipping_address ?? $user->address;

        if ($basketItems->isEmpty()) {
            return response()->json(['message' => 'Basket is empty'], 400);
        }

        DB::beginTransaction();
        try {
            $totalPrice = 0;

            foreach ($basketItems as $basketItem) {
                $item = Item::find($basketItem->item_id);
                if ($item->quantity < $basketItem->quantity) {
                    throw new \Exception("the required quantity of ({$item->name}) is not available");
                }
                $price = $item->priceAfterDiscount ?? $item->price;
                $totalPrice += $price * $basketItem->quantity;
                $unitPrice = $item->priceAfterDiscount ?? $item->price;
            }

            $order = Order::create([
                'user_id' => $user->id,
                'total_price' => $totalPrice,
                'status' => 'Pending',
                'shipping_address' => $finalShippingAddress,
                'payment_method' => 'COD'
            ]);

            foreach ($basketItems as $basketItem) {
                $item = Item::find($basketItem->item_id);

                orderItems::create([
                    'order_id' => $order->id,
                    'item_id'  => $basketItem->item_id,
                    'quantity' => $basketItem->quantity,
                    'price'       => $unitPrice,
                    'total_price' => $unitPrice * $basketItem->quantity,
                ]);

                $item->quantity -= $basketItem->quantity;
                $item->save();
            }

            Basket::where('user_id', $user->id)->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Your request has been successfully submitted.',
                'total_account' => $totalPrice,
                'address' => $finalShippingAddress,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
    public function getUserOrders(Request $request)
    {
        $orders = $request->user()->orders()->latest()->get();

        return response()->json([
            'status' => true,
            'count' => $orders->count(),
            'data' => $orders
        ]);
    }



}

