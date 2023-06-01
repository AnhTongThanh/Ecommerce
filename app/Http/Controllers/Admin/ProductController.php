<?php

namespace App\Http\Controllers\Admin;

use App\Models\Brand;
use App\Models\Color;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use App\Models\ProductColor;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Http\Requests\ProductFormRequest;

class ProductController extends Controller
{
    public function index() 
    {
        $products = Product::paginate(10);
        return view('admin.products.index', compact('products'));
    }

    public function create() 
    {
        $categories = Category::all();
        $brands = Brand::all();
        $colors = Color::where('status', '0')->get();
        return view('admin.products.create', compact('categories', 'brands', 'colors'));
    }

    public function store(ProductFormRequest $request) 
    {
        $validateData = $request->validated();

        $category = Category::findOrFail($validateData['category_id']);

        $product = $category->products()->create([
            'category_id' => $validateData['category_id'],
            'name' => $validateData['name'],
            'slug' => Str::slug($validateData['slug']),
            'brand' => $validateData['brand'],
            'small_description'  => $validateData['small_description'],
            'description'  => $validateData['description'],	
            'original_price'  => $validateData['original_price'],	
            'selling_price'  => $validateData['selling_price'],	
            'quantity'  => $validateData['quantity'],
            'trending'  => $request->trending == true ? '1':'0',
            'featured'  => $request->featured == true ? '1':'0',
            'status'  => $request->status == true ? '1':'0',
            'meta_title'  => $validateData['meta_title'],	
            'meta_keyword'  => $validateData['meta_keyword'],	
            'meta_description'  => $validateData['meta_description'],
        ]);

        if ($request->hasFile('image')) {
            $uploadPath = 'uploads/products/';

            $i = 1;
            foreach ($request->file('image') as $imageFile) {
                $extention = $imageFile->getClientOriginalExtension();
                $filename = time().$i++.'.'.$extention;
                $imageFile->move($uploadPath, $filename);
                $finalImagePathName = $uploadPath.$filename;

                $product->productImages()->create([
                    'product_id' => $product->id,
                    'image' => $finalImagePathName,
                ]);
            }
        }

        if ($request->colors) {
            foreach ($request->colors as $key => $color) {
                $product->productColors()->create([
                    'product_id' => $product->id,
                    'color_id' => $color,
                    'quantity' => $request->colorquantity[$key] ?? 0,
                ]);
            }
        }

        return redirect('/admin/products')->with('message', 'Product Added Succesfully');
    }

    public function edit(int $product_id )
    {
        $categories = Category::all();
        $brands = Brand::all();
        $product = Product::findOrFail($product_id);
        $product_color = $product->productColors->pluck('color_id')->toArray();
        $colors = Color::whereNotIn('id', $product_color)->get();

        return view('admin.products.edit', compact('categories', 'brands', 'product', 'colors'));
    }

    public function update(ProductFormRequest $request, int $product_id)
    {
        $validateData = $request->validated();

        $product = Category::findOrFail($validateData['category_id'])
                    ->products()->where('id', $product_id)->first();
        if ($product)
        {
            $product->update([
                'category_id' => $validateData['category_id'],
                'name' => $validateData['name'],
                'slug' => Str::slug($validateData['slug']),
                'brand' => $validateData['brand'],
                'small_description'  => $validateData['small_description'],
                'description'  => $validateData['description'],	
                'original_price'  => $validateData['original_price'],	
                'selling_price'  => $validateData['selling_price'],	
                'quantity'  => $validateData['quantity'],
                'trending'  => $request->trending == true ? '1':'0',
                'featured'  => $request->featured == true ? '1':'0',
                'status'  => $request->status == true ? '1':'0',
                'meta_title'  => $validateData['meta_title'],	
                'meta_keyword'  => $validateData['meta_keyword'],	
                'meta_description'  => $validateData['meta_description'],
            ]);

            if ($request->hasFile('image')) {
                $uploadPath = 'uploads/products/';
    
                $i = 1;
                foreach ($request->file('image') as $imageFile) {
                    $extention = $imageFile->getClientOriginalExtension();
                    $filename = time().$i++.'.'.$extention;
                    $imageFile->move($uploadPath, $filename);
                    $finalImagePathName = $uploadPath.$filename;
    
                    $product->productImages()->create([
                        'product_id' => $product->id,
                        'image' => $finalImagePathName,
                    ]);
                }
            }

            if ($request->colors) {
                foreach ($request->colors as $key => $color) {
                    $product->productColors()->create([
                        'product_id' => $product->id,
                        'color_id' => $color,
                        'quantity' => $request->colorquantity[$key] ?? 0,
                    ]);
                }
            }
            
            return redirect('/admin/products')->with('message', 'Product Updated Succesfully');
        }
        else
        {
            return redirect('admin/products')->with('message', 'No Such Product Id Found');
        }
    }

    public function destroyImage(int $product_image_id)
    {
        $productImage = ProductImage::findOrFail($product_image_id);
        if (File::exists($productImage->image))
        {
            File::delete($productImage->image);
        }

        $productImage->delete();
        return redirect()->back()->with('message', 'Product Image Deleted');
    }

    public function destroy(int $product_id)
    {
        $product = Product::findOrFail($product_id);

        if ($product->productImages) {
            foreach ($product->productImages as $image) {
                if (File::exists($image->image)) {
                    File::delete($image->image);
                }
            }
        }
        $product->delete();
        return redirect()->back()->with('message', 'Product Deleted with all its image');
    }

    public function updateProdColorQty(Request $request, $prod_color_id)
    {
        $productColorData = Product::findOrFail($request->product_id)
                            ->productColors()->where('id', $prod_color_id)->first();
        $productColorData->update([
            'quantity' => $request->qty
        ]);

        return response()->json(['message' => 'Product Color Quantity Updated']);
    }

    public function deleteProdColor($prod_color_id)
    {
        $prodColor = ProductColor::findOrFail($prod_color_id);
        $prodColor->delete();

        return response()->json(['message' => 'Product Color Deleted']);
    }
}
