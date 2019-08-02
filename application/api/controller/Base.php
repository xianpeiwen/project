<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/7/27
 * Time: 14:22
 */

namespace app\api\controller;


use app\api\service\Token;
use app\api\service\Tools;
use think\Controller;
use think\Db;

class Base extends Controller
{
    protected $user = [];

    /*public function _initialize()
    {
        $uid = request()->header('uid', '');
        if (empty($uid)){
            Tools::error('miss uid');
        }else{
            $this->user = Db::name('User')->where('user_id',$uid)->find();
            if (!$this->user){
                Tools::error('no message');
            }
            if ($this->user['user_status']==2){
                Tools::error('blacklist',[],20001);
            }
        }
    }*/


    public function _initialize()
    {
        //头部带token
        $token = request()->header('token', '');
        $res = Token::verifyToken($token);
        if ($res['code']=='ok'){
            $uid = $res['uid'];
            $this->user = Db::name('User')->where('user_id',$uid)->find();
            if (!$this->user){
                Tools::error('token无效',[],10001);
            }
            if ($this->user['user_status']==2){
                Tools::error('黑名单',[],20001);
            }
        }else{
            Tools::error('token无效',[],10001);
        }
    }
}