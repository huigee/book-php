<?php

namespace App\Http\Controllers\Admin;

use App\Entity\Category;
use App\Entity\PdtContent;
use App\Entity\PdtImages;
use App\Entity\Product;
use App\Http\Controllers\Controller;
use App\Models\M3Result;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function toProduct()
    {
        $products = Product::All();
        foreach ($products as $product)
        {
            $category = Category::find($product->cat_id);
            $product->category_name = $category->name;
        }
        return view('admin.product')->with('products', $products);
    }

    public function toProductAdd()
    {
        $categories = Category::whereNotNull('parent_id')->get();

        return view('admin.product_add')->with('categories',$categories);
    }

    public function toProductInfo(Request $request)
    {
        $product_id = $request->input('product_id', '');

        $product = Product::find($product_id);
        $product->category = Category::find($product->cat_id);

        $pdt_content = PdtContent::where('product_id', $product_id)->first();
        $pdt_images = PdtImages::where('product_id', $product_id)->get();

        return view('admin.product_info')->with('product', $product)
                                                ->with('pdt_content', $pdt_content)
                                                ->with('pdt_images', $pdt_images);
    }


    public function productAdd(Request $request)
    {
        $name = $request->input('name', '');
        $summary = $request->input('summary', '');
        $price = $request->input('price', '');
        $category_id = $request->input('category_id', '');
        $preview = $request->input('preview', '');
        $content = $request->input('content', '');

        $preview1 = $request->input('preview1', '');
        $preview2 = $request->input('preview2', '');
        $preview3 = $request->input('preview3', '');
        $preview4 = $request->input('preview4', '');
        $preview5 = $request->input('preview5', '');

        $product = new Product();
        $product->name = $name;
        $product->summary = $summary;
        $product->price = $price;
        $product->cat_id = $category_id;
        $product->preview = $preview;
        $product->save();

        $pdt_content = new PdtContent();
        $pdt_content->content = $content;
        $pdt_content->product_id = $product->product_id;
        $pdt_content->save();

        if ($preview1 != '')
        {
            $pdt_images = new PdtImages();
            $pdt_images->image_no = 1;
            $pdt_images->product_id = $product->product_id;
            $pdt_images->image_path = $preview1;
            $pdt_images->save();
        }
        if ($preview2 != '')
        {
            $pdt_images = new PdtImages();
            $pdt_images->image_no = 2;
            $pdt_images->product_id = $product->product_id;
            $pdt_images->image_path = $preview2;
            $pdt_images->save();
        }
        if ($preview3 != '')
        {
            $pdt_images = new PdtImages();
            $pdt_images->image_no = 3;
            $pdt_images->product_id = $product->product_id;
            $pdt_images->image_path = $preview3;
            $pdt_images->save();
        }
        if ($preview4 != '')
        {
            $pdt_images = new PdtImages();
            $pdt_images->image_no = 4;
            $pdt_images->product_id = $product->product_id;
            $pdt_images->image_path = $preview4;
            $pdt_images->save();
        }
        if ($preview5 != '')
        {
            $pdt_images = new PdtImages();
            $pdt_images->image_no = 5;
            $pdt_images->product_id = $product->product_id;
            $pdt_images->image_path = $preview5;
            $pdt_images->save();
        }

        $m3_result = new M3Result();
        $m3_result->status = 0;
        $m3_result->message = "添加成功";

        return $m3_result->toJson();
    }

    public function productDelete(Request $request)
    {
        $product_id = $request->input('product_id', '');
        Product::find($product_id)->delete();

        PdtContent::where('product_id',$product_id)->delete();
        PdtImages::where('product_id', $product_id)->delete();

        $m3_result = new M3Result();
        $m3_result->status = 0;
        $m3_result->message = '删除成功';


        return $m3_result->toJson();
    }
}