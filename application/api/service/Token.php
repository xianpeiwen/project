<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/26 0026
 * Time: 下午 2:39
 */
namespace app\api\service;

use think\Db;
use WeMini\Crypt;

class Token{

    private static $table = 'user';

    public static function getToken($code = ''){
        if (empty($code)){
            Tools::error('code参数');
        }else {
            $config = [
                'appid' => sysconf('wechat_appid'),
                'appsecret' => sysconf('wechat_appsecret'),
            ];
            $mini = new Crypt($config);
            //用code请求openid和session_key
            $res = $mini->session($code);
            //判断
            if (empty($res)||array_key_exists('errcode',$res)){
                Tools::error('配置错误',$res);
            }else{
                //查询数据库，处理，生成token
                $openid = $res['openid'];
                $user = Db::name(self::$table)->where('user_openid',$openid)->find();
                if ($user){
                    $uid = $user['id'];
                }else{
                    $uid = Db::name(self::$table)->insertGetId(['user_openid'=>$openid]);
                }

                $len = 10-mb_strlen($uid);
                Db::name(self::$table)->where('user_id',$uid)->update(['user_share_code'=>substr(md5($uid),0,$len).$uid]);

                $cacheRes = $res;
                $cacheRes['uid'] = $uid;
                $randChar = getRandChar(32);
                $timestamp = $_SERVER['REQUEST_TIME_FLOAT'];
                $salt = sysconf('salt');
                $key = md5($randChar.$timestamp.$salt);
                $value = json_encode($cacheRes);
                $expire_in = sysconf('expire_in');
                $result = cache($key,$value,$expire_in);
                if (!$result){
                    Tools::error('缓存失败');
                }else{
                    return ['token'=>$key,'uid'=>$uid];
                }
            }
        }
    }

    //验证TOKEN，正确则返回缓存数据
    public static function verifyToken($token = ''){
        if (empty($token)){
            return ['code'=>'empty token'];
        }else{
            $vars = cache($token);
            if(!$vars){
                return ['code'=>'error token'];
            }else{
                $vars = json_decode($vars,true);
                return ['code'=>'ok','uid'=>$vars['uid'],'openid'=>$vars['openid']];
            }
        }
    }
}