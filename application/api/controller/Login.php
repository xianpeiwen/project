<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/7/27
 * Time: 14:05
 */

namespace app\api\controller;


use app\api\service\Token;
use app\api\service\Tools;
use think\Controller;
use think\Db;
use WeMini\Crypt;

class Login extends Controller
{
    public function index(){
        $code = $this->request->param('code');
        $res = Token::getToken($code);
        if (isset($res['token']) && isset($res['uid'])){
            Tools::success('获取成功',['token'=>$res['token']]);
        }
    }

    /*public function index()
    {
        $code = $this->request->param('code');
        if (empty($code)){
            Tools::error('miss code');
        }else{
            $config = [
                'appid' => sysconf('wechat_appid'),
                'appsecret' => sysconf('wechat_appsecret'),
            ];
            $mini = new Crypt($config);
            //用code请求openid和session_key
            $res = $mini->session($code);
            if (empty($res)||array_key_exists('errcode',$res)){
                Tools::error($res['errmsg'],[],$res['errcode']);
            }else{
                Tools::success('发送成功',['openid'=>$res['openid'],'session_key'=>$res['session_key']],1);
            }
        }
    }*/

    /*public function uid()
    {
        $openid = $this->request->param('openid');
        $unionid = $this->request->param('unionid');
        $nickname = $this->request->param('wx_name');
        $headimgurl = $this->request->param('wx_img');
        if (empty($openid)){
            Tools::error('miss openid');
        }else{
            $user = Db::name('user')->where('user_openid',$openid)->find();
            $data = [
                'user_openid'=>$openid,
                'user_unionid'=>$unionid,
                'user_nickname'=>$nickname,
                'user_headimgurl'=>$headimgurl
            ];
            if ($user){
                Db::name('user')->where('user_id',$user['user_id'])->update($data);
                $uid = $user['user_id'];
            }else{
                $uid = Db::name('user')->insertGetId($data);
            }
            Tools::success('进入成功',['uid'=>$uid],1);
        }
    }*/


}