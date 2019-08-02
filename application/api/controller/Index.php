<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/7/27
 * Time: 14:27
 */

namespace app\api\controller;


use app\api\service\Tools;
use app\manage\model\UserOrder;
use think\Controller;
use think\Db;

class Index extends Controller
{
    public function card()
    {
        $card = Db::name('card')->where('card_status',1)->where('card_type','in',[1,2])
            ->field('card_id,card_name,card_name_eng,card_desc,card_desc_eng,card_price')->order('sort desc')
            ->select();
        Tools::success('',$card);
    }


    public function buy()
    {
        $post = $this->request->post();
        $post['uid'] = 1;
        $post['pay_type'] = 1;
        $post['openid'] = 1;

        $res = UserOrder::make($post);
        Tools::success('',$res);
    }
}