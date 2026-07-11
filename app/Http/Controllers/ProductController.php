<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $products = Product::latest()->paginate(20);
        $lowStock = Product::whereColumn('quantity', '<=', 'reorder_level')->where('quantity', '>', 0)->count();
        $outOfStock = Product::where('quantity', 0)->count();
        $totalValue = Product::sum(\DB::raw('quantity * cost_price'));
        $totalProducts = Product::count();
        $categories = Category::where('is_active', true)->orderBy('name')->pluck('name');
        return view('products.index', compact('products', 'lowStock', 'outOfStock', 'totalValue', 'totalProducts', 'categories'));
    }

    public function create()
    {
        if (request()->wantsJson()) {
            return response()->json(['categories' => Category::where('is_active', true)->orderBy('name')->pluck('name')]);
        }
        return view('products.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255|unique:products',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'unit' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $product = Product::create($data);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Product added.', 'product' => $product]);
        }

        return redirect()->route('products.index')->with('status', 'Product added.');
    }

    public function edit(Product $product)
    {
        if (request()->wantsJson()) {
            return response()->json(['product' => $product]);
        }
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255|unique:products,sku,' . $product->id,
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'unit' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $product->update($data);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Product updated.', 'product' => $product]);
        }

        return redirect()->route('products.index')->with('status', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return back()->with('status', 'Product deleted.');
    }
}
