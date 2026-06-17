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


class WishlistController extends Controller {
    public function addToWishlist(Request $request)
    {
        $user = Auth::user();
         $request->validate([
            'item_id' => 'required|exists:items,id',
        ]);
        $item = Item::findOrFail($request->item_id);

        if ($user->wishlists()->where('item_id', $item->id)->exists()) {
            return response()->json(['message' => 'Item is already in your wishlist'], 400);
        }

        $user->wishlists()->attach($item->id);
        return response()->json(['message' => 'Item added to wishlist successfully']);
    }

    //----------------------------------------------------

    public function removeFromWishlist(Request $request)
    {
        $user = Auth::user();
         $request->validate([
            'item_id' => 'required|exists:items,id',
        ]);
        $item = Item::findOrFail($request->item_id);

        if (!$user->wishlists()->where('item_id', $item->id)->exists()) {
            return response()->json(['message' => 'Item is not in your wishlist'], 400);
        }

        $user->wishlists()->detach($item->id);
        return response()->json(['message' => 'Item removed from wishlist successfully']);
    }

    //----------------------------------------------------

    public function viewWishlist()
    {
        $user = Auth::user();
        $wishlistItems = $user->wishlists()->with('Item')->get();

        return response()->json($wishlistItems);
    }
    
    //----------------------------------------------------

    public function clearWishlist()
    {
        $user = Auth::user();
        $user->wishlists()->detach();

        return response()->json(['message' => 'Wishlist cleared successfully']);
    }

    //-------------------------------------------------------

    public function moveToBasket(Request $request)
    {
        $user = Auth::user();
         $request->validate([
            'item_id' => 'required|exists:items,id',
        ]);
        $item = Item::findOrFail($request->item_id);

        if (!$user->wishlists()->where('item_id', $item->id)->exists()) {
            return response()->json(['message' => 'Item is not in your wishlist'], 400);
        }

        $user->baskets()->attach($item->id);
        $user->wishlists()->detach($item->id);

        return response()->json(['message' => 'Item moved to basket successfully']);
    }
  //-----------------------------------------------
    public function returntowishlist(Request $request)
    {
        $user = Auth::user();
         $request->validate([
            'item_id' => 'required|exists:items,id',
        ]);
        $item = Item::findOrFail($request->item_id);

        if (!$user->baskets()->where('item_id', $item->id)->exists()) {
            return response()->json(['message' => 'Item is not in your basket'], 400);
        }

        $user->wishlists()->attach($item->id);
        $user->baskets()->detach($item->id);

        return response()->json(['message' => 'Item moved back to wishlist successfully']);
    }


}

