<?php
namespace App\Http\Controllers\Service;

use App\Entity\Order;
use App\Http\Controllers\Controller;
use App\Tool\wxpay\WXTool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PayController extends Controller
{
    public function aliPay(Request $request)
    {
        require_once app_path().'/Tool/Alipay/wappay/service/AlipayTradeService.php';
        require_once app_path().'/Tool/Alipay/wappay/buildermodel/AlipayTradeWapPayContentBuilder.php';
        require_once app_path().'/Tool/Alipay/config.php';

        //商户订单号，商户网站订单系统中唯一订单号，必填
        $out_trade_no = $_POST['order_no'];

        if ($out_trade_no != '')
        {
            //订单名称，必填
            $subject = $_POST['name'];

            //付款金额，必填
            $total_amount = $_POST['total_price'];

            //商品描述，可空
//            $body = $request->input('WIDbody', '');

            //超时时间
            $timeout_express="1m";

            $payRequestBuilder = new \AlipayTradeWapPayContentBuilder();
//            $payRequestBuilder->setBody($body);
            $payRequestBuilder->setSubject($subject);
            $payRequestBuilder->setOutTradeNo($out_trade_no);
            $payRequestBuilder->setTotalAmount($total_amount);
            $payRequestBuilder->setTimeExpress($timeout_express);

            $payResponse = new \AlipayTradeService($config);
            $result=$payResponse->wapPay($payRequestBuilder,$config['return_url'],$config['notify_url']);

            return ;
        }
    }

    public function aliNotify(Request $request)
    {
        require_once(app_path()."/Tool/Alipay/config.php");
        require_once app_path().'/Tool/Alipay/wappay/service/AlipayTradeService.php';


        $arr=$_POST;
        $alipaySevice = new \AlipayTradeService($config);
        $alipaySevice->writeLog(var_export($_POST,true));
        $result = $alipaySevice->check($arr);

        /* 实际验证过程建议商户添加以下校验。
        1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号，
        2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额），
        3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）
        4、验证app_id是否为该商户本身。
        */
        if($result) {//验证成功
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //请在这里加上商户的业务逻辑程序代


            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——

            //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表

            //商户订单号

            $out_trade_no = $_POST['out_trade_no'];

            //支付宝交易号

            $trade_no = $_POST['trade_no'];

            //交易状态
            $trade_status = $_POST['trade_status'];


            if($_POST['trade_status'] == 'TRADE_FINISHED') {

                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
                //如果有做过处理，不执行商户的业务程序
                Log::info('支付完成');
                //注意：
                //退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
            }
            else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
                //如果有做过处理，不执行商户的业务程序
                //注意：
                //付款完成后，支付宝系统发送该交易状态通知
                Log::info('支付成功');
                $order = Order::where('order_no', $out_trade_no)->first();
                $order->status = 2;
                $order->save();
            }
            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

            return "success";		//请不要修改或删除

        }else {
            //验证失败
            Log::info('支付验证失败');
            return "fail";	//请不要修改或删除

        }

    }

    public function aliReturn(Request $request)
    {
        require_once(app_path()."/Tool/Alipay/config.php");
        require_once app_path().'/Tool/Alipay/wappay/service/AlipayTradeService.php';


        $arr=$_GET;
        $alipaySevice = new \AlipayTradeService($config);
        $result = $alipaySevice->check($arr);

        /* 实际验证过程建议商户添加以下校验。
        1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号，
        2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额），
        3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）
        4、验证app_id是否为该商户本身。
        */
        if($result) {//验证成功
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //请在这里加上商户的业务逻辑程序代码

            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
            //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表

            //商户订单号

            $out_trade_no = htmlspecialchars($_GET['out_trade_no']);

            //支付宝交易号

            $trade_no = htmlspecialchars($_GET['trade_no']);

            return "验证成功<br />外部订单号：".$out_trade_no;

            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        }
        else {
            //验证失败
            return "验证失败";
        }
    }

    public function wxPay(Request $request)
    {
        $openid = $request->session()->get('openid', '');
        if ($openid == '')
        {
            $openid = WXTool::httpGet('http://'.$_SERVER['HTTP_HOST'] . '/service/openid/get');
        }
        return WXTool::wxPayData($request->input('name'),$request->input('order_no'), 1, $openid);
    }

    public function getOpenid(Request $request)
    {
        $code = $request->input('code', '');
        //通过code获得openid
        if ($code == '')
        {
            //触发微信返回code码
            $redirect_url = urlencode('http://'. $_SERVER['HTTP_HOST'] . '/service/openid/get');
            //微信重定向
            $url = 'https://open.weixin.qq.com/connect/oauth2/authorize' .
                '?appid=' . config('wx_config.APPID') .
                '&redirect_uri=' . $redirect_url .
                '&response_type=code' .
                '&scope=snsapi_base' .
                '&state=STATE' .
                '#wechat_redirect';
            return redirect($url);
        }
        //获取code码，以获取openid
        $openid = WXTool::getOpenid($code);
        //将openid保存到session
        $request->session()->put('openid', $openid);

        return $openid;
    }

    public function wxNotify() {
        Log::info('微信支付回调开始');
        $return_data = file_get_contents("php://input");
        Log::info('return_data: '.$return_data);
        libxml_disable_entity_loader(true);
        $data = simplexml_load_string($return_data, 'SimpleXMLElement', LIBXML_NOCDATA);
        Log::info('return_code: '.$data->return_code);
        if($data->return_code == 'SUCCESS') {
            $order = Order::where('order_no', $data->out_trade_no)->first();
            $order->status = 2;
            $order->save();
            return "<xml>
                <return_code><![CDATA[SUCCESS]]></return_code>
                <return_msg><![CDATA[OK]]></return_msg>
              </xml>";
        }
        return "<xml>
              <return_code><![CDATA[FAIL]]></return_code>
              <return_msg><![CDATA[FAIL]]></return_msg>
            </xml>";
    }
}