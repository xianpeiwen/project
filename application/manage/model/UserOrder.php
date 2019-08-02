<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/7/31
 * Time: 16:50
 */

namespace app\manage\model;


use app\api\service\Tools;
use think\Db;
use think\Model;

class UserOrder extends Model
{
    public static function make($data)
    {
        foreach (['aid','cid','uid','pay_type'] as $v){
            if (empty($data[$v])){
                Tools::error('miss '.$v);
            }
        }
        $agent = Db::name('agent')->where(['agent_status'=>1,'agent_id'=>$data['aid']])->find();
        if (!$agent){
            Tools::error('商家不可用');
        }
        $card = Db::name('card')->where(['card_id'=>$data['cid'],'card_status'=>1])->find();
        if (!$card){
            Tools::error('会员卡不可用');
        }
        $order_data = [
            'order_no'=>makeOrderNo(),
            'status'=>0,
            'uid'=>$data['uid'],
            'agent_id'=>$data['aid'],
            'pay_type'=>$data['pay_type'],

            'pay_price'=>$card['card_price'],
            'card_type'=>$card['card_type'],
            'card_day_limit'=>$card['card_day_limit'],
            'card_discount'=>$card['card_discount'],
            'card_cost'=>$card['card_cost']
        ];
        $order_id = self::insertGetId($order_data);

        //微信支付
        $config = [
            'appid' => sysconf('wechat_appid'),
            'appsecret' => sysconf('wechat_appsecret'),
            'mch_id'         => sysconf('wechat_mch_id'),
            'mch_key'        => sysconf('wechat_partnerkey'),
        ];

        $pay = \We::WeChatPay($config);
        $option = [
            'body'=>$card['card_name'],
            'out_trade_no'     => $order_data['order_no'],
            'total_fee'        => 1,
//                'total_fee'        => $order['pay']*100,
            'openid'           => $data['openid'],
            'trade_type'       => 'JSAPI',
            'notify_url'       => request()->domain().'/api/pay/notify',
            'spbill_create_ip' => '120.79.24.203',
        ];
        $result = $pay->createOrder($option);
        $res = $pay->createParamsForJsApi($result['prepay_id']);
        return $res;

    }
}