<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/7/31
 * Time: 10:47
 */

namespace app\api\controller;


use app\api\service\Tools;
use app\manage\model\UserOrder;
use think\Db;

class User extends Base
{
    public $table = 'user';

    public function login()
    {
        $post = $this->request->post();
        Db::name($this->table)->where('user_id',$this->user['user_id'])->update([
            'user_nickname'=>$post['nickName'],
            'user_headimgurl'=>$post['avatarUrl'],
            'user_sex'=>$post['gender'],
            'user_province'=>$post['province'],
            'user_city'=>$post['city'],
            'user_country'=>$post['country'],
            'wx_auth'=>1,
            'user_last_login'=>date('Y-m-d H:i:s')
        ]);
        Tools::success('用户信息更新成功');
    }

    public function card()
    {
        $card = Db::name('card')->where('card_status',1)->where('card_type','in',[1,2])
            ->field('card_id,card_name,card_name_eng,card_desc,card_desc_eng,card_price')->order('sort desc')
            ->select();
        Tools::success('',$card);
    }

    public function buy()
    {
        UserOrder::make(123);
    }
}