<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    // عرض قائمة المفضلة الخاصة بالمستخدم (المنتجات التي وضع لها قلب)
    public function index()
    {
        $user = Auth::user();
        // جلب المنتجات المفضلة للمستخدم الحالي
        $favorites = Favorite::where('user_id', $user->id)->with('item')->latest()->get();

        return response()->json([
            'status' => true,
            'data' => $favorites
        ], 200);
    }

    public function toggleFavorite(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
        ]);

        $userId = Auth::id();
        $itemId = $request->item_id;

        $favorite = Favorite::where('user_id', $userId)
            ->where('item_id', $itemId)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json([
                'status' => true,
                'action' => 'removed',
                'message' => 'Item removed from favorites.'
            ], 200);
        } else {
            Favorite::create([
                'user_id' => $userId,
                'item_id' => $itemId,
            ]);
            return response()->json([
                'status' => true,
                'action' => 'added',
                'message' => 'Item added to favorites successfully.'
            ], 201);
        }
    }
}
