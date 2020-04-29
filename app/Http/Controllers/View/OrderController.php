<?php

namespace App\Http\Controllers\View;

use App\Entity\CartItem;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function toOrderCommit(Request $request)
    {
        $product_ids = $request->input('product_ids','');
        $product_ids_arr = ($product_ids != '' ? explode(',', $product_ids) : array());
        $member = $request->session()->get('member', '');
        $cart_items = CartItem::where('member_id', $member->member_id)->whereIn('product_id', $product_ids_arr)->get();

        $order = new Order();
        $order->member_id = $member->member_id;
        $order->save();

        $cart_items_arr = [];
        $cart_items_ids_arr = [];
        $total_price = 0;
        $name = '';
        foreach ($cart_items as $cart_item)
        {
            $cart_item->product = Product::find($cart_item->product_id);
            if ($cart_item->product != null)
            {
                $total_price += $cart_item->product->price * $cart_item->count;
                $name .= ('《'.$cart_item->product->name .'》');
                array_push($cart_items_arr, $cart_item);
                array_push($cart_items_ids_arr, $cart_item->cart_id);

                $order_item = new OrderItem();
                $order_item->order_id = $order->id;
                $order_item->product_id = $cart_item->product_id;
                $order_item->count = $cart_item->count;
                $order_item->pdt_snapshot = json_encode($cart_item->product);
                $order_item->save();
            }
        }
        CartItem::whereIn('cart_id',$cart_items_ids_arr)->delete();

        $order->name = $name;
        $order->total_price = $total_price;
        $order->order_no = 'E' . time() . $order->id;
        $order->save();

//        //JSSDK相关
//        $access_token = WXTool::getAccessToken();
//        $jsapi_ticket = WXTool::getJsApiTicket($access_token);
//        $noncestr = WXTool::createNonceStr();
//        $timestamp = time();
//        $url = 'http://' .$_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
//        //签名
//        $signature = WXTool::signature($jsapi_ticket,$noncestr,$timestamp,$url);
//        //返回微信参数
//        $bk_wx_js_config = new BKWXJsConfig();
//        $bk_wx_js_config->appId = config('wx_config.APPID');
//        $bk_wx_js_config->timestamp = $timestamp;
//        $bk_wx_js_config->nonceStr = $noncestr;
//        $bk_wx_js_config->signature = $signature;

        return view('order_commit')->with('cart_items', $cart_items_arr)
                                        ->with('total_price', $total_price)
                                        ->with('name', $order->name)
                                        ->with('order_no', $order->order_no);
//                                        ->with('bk_wx_js_config', $bk_wx_js_config->toJson());
    }

    public function toOrderList(Request $request)
    {
        $member = $request->session()->get('member','');
        $orders = Order::where('member_id', $member->member_id)->get();
        foreach ($orders as $order)
        {
            $order_items = OrderItem::where('order_id', $order->id)->get();
            $order->order_items = $order_items;
            foreach ($order_items as $order_item)
            {
                $order_item->product = json_decode($order_item->pdt_snapshot);
            }
        }
//        return $orders;
        return view('order_list')->with('orders', $orders);
    }
}