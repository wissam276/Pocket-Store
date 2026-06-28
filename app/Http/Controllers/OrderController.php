<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Basket;
use App\Models\Coupon;
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
            'city' => 'sometimes|nullable|string|max:255',
            'delivery_method' => 'sometimes|in:standard,express',
            'payment_method'   => 'required|in:COD,cod,card',
            'notes' => 'sometimes|nullable|string|max:1000',
            'coupon_code' => 'sometimes|nullable|string|max:50',
        ]);

        $user = $request->user();
        $basketItems = Basket::where('user_id', $user->id)->with('item')->get();
        $finalShippingAddress = $request->shipping_address ?? $user->address;
        $deliveryMethod = $request->delivery_method ?? 'standard';
        $paymentMethod = strtolower($request->payment_method) === 'card' ? 'card' : 'COD';

        if ($basketItems->isEmpty()) {
            return response()->json(['message' => 'Basket is empty'], 400);
        }

        DB::beginTransaction();
        try {
            $subtotal = 0;
            $orderLines = [];

            foreach ($basketItems as $basketItem) {
                $item = $basketItem->item;

                if (! $item) {
                    throw new \Exception('One of the basket items no longer exists');
                }

                if ($item->quantity < $basketItem->quantity) {
                    throw new \Exception("the required quantity of ({$item->name}) is not available");
                }

                $unitPrice = (float) ($item->priceAfterDiscount ?? $item->price);
                $quantity = (int) $basketItem->quantity;
                $subtotal += $unitPrice * $quantity;
                $orderLines[] = [
                    'item' => $item,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                ];
            }

            $deliveryCost = $this->deliveryCostFor($subtotal, $deliveryMethod);
            $coupon = null;
            $couponDiscount = 0;

            if ($request->filled('coupon_code')) {
                $couponCode = strtoupper(trim($request->coupon_code));
                $coupon = Coupon::where('code', $couponCode)->lockForUpdate()->first();

                if (! $coupon) {
                    DB::rollBack();
                    return response()->json(['message' => 'Coupon code is invalid.'], 422);
                }

                if (! $coupon->canApplyTo($subtotal)) {
                    DB::rollBack();
                    return response()->json(['message' => 'Coupon is not available for this order.'], 422);
                }

                $couponDiscount = $coupon->discountFor($subtotal);

                if ($coupon->type === Coupon::TYPE_FREE_SHIPPING) {
                    $deliveryCost = 0;
                }
            }

            $totalPrice = max($subtotal - $couponDiscount + $deliveryCost, 0);

            $order = Order::create([
                'user_id' => $user->id,
                'subtotal_price' => $subtotal,
                'total_price' => $totalPrice,
                'status' => 'Pending',
                'shipping_address' => $finalShippingAddress,
                'payment_method' => $paymentMethod,
                'coupon_id' => $coupon?->id,
                'coupon_code' => $coupon?->code,
                'coupon_type' => $coupon?->type,
                'coupon_discount' => $couponDiscount,
                'delivery_method' => $deliveryMethod,
                'delivery_cost' => $deliveryCost,
                'city' => $request->city,
                'notes' => $request->notes,
            ]);

            $itemsForResponse = [];

            foreach ($orderLines as $line) {
                $item = $line['item'];
                $quantity = $line['quantity'];
                $unitPrice = $line['unit_price'];

                orderItems::create([
                    'order_id' => $order->id,
                    'item_id'  => $item->id,
                    'quantity' => $quantity,
                    'price'       => $unitPrice,
                    'total_price' => $unitPrice * $quantity,
                ]);

                $itemsForResponse[] = "{$item->name} x {$quantity}";

                $item->quantity -= $quantity;
                $item->save();
            }

            if ($coupon) {
                $coupon->increment('usage_count');
            }

            Basket::where('user_id', $user->id)->delete();
            DB::commit();

            return response()->json([
                'id' => $order->id,
                'databaseId' => $order->id,
                'status' => $order->status,
                'subtotal' => (float) $subtotal,
                'discount' => (float) $couponDiscount,
                'delivery' => (float) $deliveryCost,
                'total' => (float) $totalPrice,
                'couponCode' => $coupon?->code,
                'couponType' => $coupon?->type,
                'address' => $finalShippingAddress,
                'city' => $request->city,
                'deliveryMethod' => $deliveryMethod,
                'payment' => $paymentMethod === 'card' ? 'Card payment' : 'Cash on delivery',
                'paid' => $paymentMethod === 'card',
                'customer' => $user->name,
                'items' => $itemsForResponse,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    private function deliveryCostFor(float $subtotal, string $deliveryMethod): float
    {
        if ($subtotal > 300 || $subtotal === 0.0) {
            return 0.0;
        }

        return $deliveryMethod === 'express' ? 20.0 : 12.0;
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
