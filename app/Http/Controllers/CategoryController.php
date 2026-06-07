<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $catregories=Category::all();
        return response()->json($catregories,200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'slug'  => 'required|string|unique:categories,slug',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('categories', 'public');
            $validated['image'] = $path;
        }

        $category = Category::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'new category created successfully',
            'data' => $category
        ], 201);
    }

    public function update(Request $request, Category $category){
        $validated = $request->validate([
            'name'  => 'sometimes|string|max:255',
            'slug'  => 'sometimes|string|unique:categories,slug',
            'image' => 'sometimes|nullable|image|max:2048',
        ]);
        $category->update($validated);

        return response()->json([
            'status' => true,
            'message'=>"edited successfully"

        ],200);
    }
    public function show($id)
    {
        $category=Category::findOrFail($id);
        return response()->json($category,200);
    }
    public function edit(UpdateCategoryRequest $request ,$id)
    {
        $category=Category::findOrFail($id);
        $category->update($request->only(['name','description']));
        return response()->json($category,200);
    }
    public function destroy($id)
    {
        $category=Category::findOrFail($id);
        $category->delete();
        return response()->json(null,204);
    }
}
