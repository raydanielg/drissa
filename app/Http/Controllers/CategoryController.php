<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $categories = Category::latest()->paginate(20);
        $total = Category::count();
        $active = Category::where('is_active', true)->count();
        $inactive = Category::where('is_active', false)->count();
        return view('categories.index', compact('categories', 'total', 'active', 'inactive'));
    }

    public function create()
    {
        if (request()->wantsJson()) {
            return response()->json(['categories' => Category::where('is_active', true)->get()]);
        }
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $category = Category::create($data);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Category added.', 'category' => $category]);
        }

        return redirect()->route('categories.index')->with('status', 'Category added.');
    }

    public function edit(Category $category)
    {
        if (request()->wantsJson()) {
            return response()->json(['category' => $category]);
        }
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $category->update($data);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Category updated.', 'category' => $category]);
        }

        return redirect()->route('categories.index')->with('status', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return back()->with('status', 'Category deleted.');
    }
}
