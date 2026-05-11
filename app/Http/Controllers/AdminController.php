<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

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
}
