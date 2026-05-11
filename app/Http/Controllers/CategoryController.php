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
 
    public function store(StoreCategoryRequest $request)
    {
        $category=Category::create($request->validated());
        return response()->json($category,201);
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
