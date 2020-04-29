<?php

namespace  App\Http\Controllers\View;

use App\Entity\CartItem;
use App\Entity\PdtContent;
use App\Entity\PdtImages;
use App\Entity\Product;
use App\Http\Controllers\Controller;
use App\Entity\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BookController extends Controller
{
    public function toCategory($value = '')
    {
        $categorys = Category::whereNull('parent_id')->get();
        Log::info("进入书籍类别：");
        return view('category')->with('categorys', $categorys);
    }

    public function toProduct($category_id)
    {
        $products = Product::where('cat_id', $category_id)->get();
        return view('product')->with('products', $products);
    }

    public function toPdtContent(Request $request,$product_id)
    {
        $product = Product::find($product_id);
        $pdtContent = PdtContent::where('product_id', $product_id)->first();
        $pdt_images = PdtImages::where('product_id', $product_id)->get();

        $count = 0;

        $member = $request->session()->get('member', '');
        if ($member != ''){
            //已登录
            $cart_items = CartItem::where('member_id', $member->member_id)->get();
            foreach ($cart_items as $cart_item){
                if ($cart_item->product_id == $product_id){
                    $count = $cart_item->count;
                    break;
                }
            }
        }else{
            //未登录
            $bk_cart = $request->cookie('bk_cart');
            $bk_cart_arr = ($bk_cart != null ? explode(',', $bk_cart) : array());
            foreach ($bk_cart_arr as $value)
            {
                $index = strpos($value, ":");
                if (substr($value, 0, $index) == $product_id)
                {
                    $count = (int)substr($value, $index+1) ;
                    break;
                }
            }
        }

        return view('pdt_content')->with('product', $product)
                                        ->with('pdtContent', $pdtContent)
                                        ->with('pdt_images', $pdt_images)
                                        ->with('count', $count);
    }
}