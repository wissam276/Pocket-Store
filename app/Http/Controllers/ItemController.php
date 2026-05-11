<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateItemRequest;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;



class ItemController extends Controller
{
       public function index()
    {
        $Items=Item::all();
        return response()->json($Items,200);
    }
 
    
public function show($id)
    {
        $item=Item::findOrFail($id);
        return response()->json($item,200);
    }

    public function destroy($id)
    {
        $item=Item::findOrFail($id);
        $item->delete();
        return response()->json(null,204);
}









//---------------------------------------------------------------
public function itemsByCategory(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
        ]);
        $categoryId = $request->input('category_id');
        $items = Item::where('category_id', $categoryId)->get();
        return response()->json($items, 200);
    }

//-----------------------------------------------------------
    public function search(Request $request)
    {
        $query = $request->input('query');
        $items = Item::where('name', 'like', "%$query%")
                     ->orWhere('description', 'like', "%$query%")
                     ->get();
        return response()->json($items, 200);
    }

//-----------------------------------------------------------
    public function filteringItem(Request $request){
        $query = Item::query();
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->input('min_price'));
        }
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->input('max_price'));
        }
        if ($request->has('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }
        $items = $query->get();
        return response()->json($items, 200);
    }
//-------------------------------------------------------
    public function ItemDetails(Request $request){
        $request->validate([
            'item_id' => 'required|exists:items,id',
        ]);
        $itemId = $request->input('item_id');
        $item = Item::with('category')->findOrFail($itemId);
        return response()->json($item, 200);
    }
//--------------------------------------------------
    public function store(CreateItemRequest $request)
    {
        
         unset($validatedData['item_image'], $validatedData['details_image']);

          // Store main item image
    if ($request->hasFile('item_image')) {
        $validatedData['item_image'] =
        $request->file('item_image')->store('public', 'public');    }

        // Store details images
    if ($request->hasFile('details_image')) {
        $images = [];
        foreach ($request->file('details_image') as $image) {
            $images[] = $image->store('public', 'public');
        }
        $validatedData['details_image'] = $images;
    }
        $validatedData = $request->validated();
        $item=Item::create($validatedData);
        $item->company = Auth::user()->name;
        $item->save();
        return response()->json($item,201);
    }
//--------------------------------------------------
    public function update(UpdateItemRequest $request)

    {

        $request->validate([
            'item_id' => 'required|exists:items,id',
        ]);
        $id = $request->input('item_id');
        $item = Item::findOrFail($id);
        if($item->company !== Auth::user()->name) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $validatedData = $request->validated();

        unset($validatedData['item_image'], $validatedData['details_image']);

        // Update main item image if provided
        if ($request->hasFile('item_image')) {
            $validatedData['item_image'] =
            $request->file('item_image')->store('public', 'public');
        }

        // Update details images if provided
        if ($request->hasFile('details_image')) {
            $images = [];
            foreach ($request->file('details_image') as $image) {
                $images[] = $image->store('public', 'public');
            }
            $validatedData['details_image'] = $images;
        }

        $item->update($validatedData);
        return response()->json($item, 200);
    }
//--------------------------------------------------

}
