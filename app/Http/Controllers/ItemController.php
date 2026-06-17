<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateItemRequest;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Http\Resources\ItemApiResource;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Filament\Resources\Items\ItemResource;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        return ItemApiResource::collection(Item::with('category')->paginate(10));
    }


    public function destroy(Item $item)
    {

        $item->delete();

        return response()->json([
            'message' => 'تم حذف المنتج بنجاح'
        ], 200);
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
        $searchQuery = $request->input('query');
        $items = Item::where('accepted', 'accepted')
            ->where(function($q) use ($searchQuery) {
                $q->where('name', 'like', "%$searchQuery%")
                    ->orWhere('description', 'like', "%$searchQuery%")
                    ->orWhere('company', 'like', "%$searchQuery%");
            })->paginate(10);

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
        if ($request->has('rating')) {
            $query->whereHas('ratings', function ($q) use ($request) {
                $q->where('rating', '>=', $request->input('rating'));
            });
        }
        if( $request->has('availability')) {
            $query->where('availability', $request->input('availability'));
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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'slug' => 'required|unique:items,slug',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric',
            'quantity' => 'required|numeric',
            'short_description' => 'nullable|string',
            'description' => 'required|string',
            'sales_count' => 'nullable|numeric',
            'company' => 'nullable|string',
            'priceAfterDiscount' => 'nullable|numeric',
            'DiscountPercentage' => 'nullable|numeric',
            'availability' => 'boolean',
            'item_image' => 'required|image|max:2048',
            'details_image' => 'nullable|array',
            'details_image.*' => 'image|max:2048',
        ]);

        if ($request->hasFile('item_image')) {
            $validated['item_image'] = $request->file('item_image')->store('items', 'public');
        }

        if ($request->hasFile('details_image')) {
            $paths = [];
            foreach ($request->file('details_image') as $image) {
                $paths[] = $image->store('items/details', 'public');
            }
            $validated['details_image'] = json_encode($paths);
        }

        // 3. إنشاء المنتج
        $item = Item::create($validated);


        return new ItemApiResource($item);
    }





//--------------------------------------------------
    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string',
            'price' => 'sometimes|numeric',
            'slug' => 'sometimes|unique:items,slug',
            'category_id' => 'sometimes|required|exists:categories,id',
            'quantity' => 'sometimes|numeric',
            'short_description' => 'sometimes|nullable|string',
            'description' => 'sometimes|string',
            'sales_count' => 'sometimes|nullable|numeric',
            'company' => 'sometimes|nullable|string',
            'priceAfterDiscount' => 'sometimes|nullable|numeric',
            'DiscountPercentage' => 'sometimes|nullable|numeric',
            'availability' => 'sometimes|boolean',
            'item_image' => 'sometimes|image|max:2048',
            'details_image' => 'sometimes|nullable|array',
            'details_image.*' => 'sometimes|image|max:2048',
        ]);


        if ($request->hasFile('item_image')) {

            $validated['item_image'] = $request->file('item_image')->store('items', 'public');
        }

        if ($request->has('DiscountPercentage')) {
            $discount = $request->DiscountPercentage;
            $price = $request->price ?? $item->price;

            $validated['priceAfterDiscount'] = $price - ($price * ($discount / 100));
        }
        elseif ($request->has('price') && $item->DiscountPercentage) {
            $validated['priceAfterDiscount'] = $request->price - ($request->price * ($item->DiscountPercentage / 100));
        }

        $item->update($validated);

        return new ItemApiResource($item);
    }






    public function ItemsWithSales()
    {
        $items = Item::with('sales')->get();
        return response()->json($items, 200);
    }
//----------------------------------------------------------------
    public function topSelling()
    {
        $items = Item::orderBy('sales_count', 'desc')
            ->limit(5)
            ->get();

        return response()->json($items, 200);
    }
//-----------------------------------------------------------------------





    public function show(Item $item)
    {
        return new ItemApiResource($item);
    }

    //------------------------------------------------
    public function Rating (Request $request)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'item_id' => 'required|exists:items,id',
        ]);

        $user = Auth::user();
        $item = Item::findOrFail($request->item_id);
        $item->ratings()->updateOrCreate(
            ['user_id' => $user->id],
            ['rating' => $request->rating]
        );
        return response()->json(['message' => 'Rating submitted successfully']);
    }

}
