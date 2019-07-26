<?php
namespace app\manage\controller;
use app\manage\controller\Base;
use think\Controller;
use think\Session;
use think\Db;
use think\Cache;
use think\auth\Auth;

class Index extends Base
{
    public function index()
    {
        $auth_rule = $this->autoMenu(0);
        for ($i=0; $i < count($auth_rule); $i++) {
            $father = Db::name('auth_rule')->where('status',1)->where('show',1)->where('pid',$auth_rule[$i]['id'])->find();
            if(!empty($father)){
                $authRule[] = $auth_rule[$i];
            }
        }
        $this->assign('authRule',$authRule);

        return $this->fetch();
    }

    public function  page()
    {

        return $this->fetch();
    }

    public function lock()
    {

        $img['image_surface_name'] = 'admin';
        $img['image_surface_id'] = Session::get('admin')['admin_id'];
      
        $adminImg['img'] = Db::name('image')->where($img)->field('image_path,image_name')->find();
        $adminImg['admin'] = Session::get('admin');
        $adminImg = json_encode($adminImg);  
        echo $adminImg;
    }
    public function locking()
    {
        $pass = input('post.value');

        Session::set('admin.is_lock',2);
        Session::set('admin.pass',$pass);
        $adminImg['admin'] = Session::get('admin');
        echo 1;
    }
    public function unlock()
    {
        $pass = input('post.value');
        if( $pass == Session::get('admin')['pass']){
            Session::set('admin.is_lock',1);
            Session::set('admin.pass','');
            $this->success('成功');
        }else{
            $this->error('密码输入错误！');
        }
    }
    public function SetUp()
    {

        if(Session::get('admin')['admin_id'] == 1){
            $power = input('post.power');
            Db::name('set_up')->where('set_up_id',1)->update(['set_up_value' => $power]);
            Session::set('admin.set_up_value',$power);
        }
        echo "<script type='text/javascript'>window.top.location.replace('/manage/Index/index');</script>";
    }

    public function get_the_menu()
    {

        $json = input('post.json');


        if( empty($json)){
            $auth_rule = $this->autoMenu(0);

            for ($i=0; $i < count($auth_rule); $i++) { 
                $pid = $this->autoMenu($auth_rule[$i]['id']);
                if(!empty($pid)){
                    $m[] = $auth_rule[$i]['id'];
                }
            }
            $pid = $m[0];
        }else{
            $json = explode('z', $json);
            $pid = $json[1];
        }
    
        $res =  $this->autoMenu($pid);
        for ($i=0; $i < count($res); $i++) { 

            $res[$i]['spread'] = false;
            $c = $this->autoMenu($res[$i]['id']);
            if(!empty($c)){
                for ($x=0; $x < count($c); $x++) { 
                    $res[$i]['children'][$x] = $c[$x];
                    $res[$i]['children'][$x]['spread'] = false;
                }
            }
        }
     
        $res = json_encode($res);  
        echo $res;
    }

    public function autoMenu($pid)
    {
        // $admin = Session::get('admin');
        // $auth = Auth::instance();
        // $auth_group_access = $auth->getGroups($admin['admin_id']);
        // $group_access = '';
        // for ($i=0; $i < count($auth_group_access); $i++) {
        //     $group_access = $group_access.','.$auth_group_access[$i]['rules'];
        // }
        $auth_rule = Db::name('auth_rule')->where('status',1)->where('pid',$pid)->where('show',1)->order('sort desc')->select();
        return $auth_rule;
    }
    public function NoJurisdiction()
    {
        $type = input('type');
        
        $data['title'] = '万酱汇';
        $data['titles'] = 'Backstage';
        switch ($type) {
            case '1':
                $module = strtolower(input('module'));
                $controller = strtolower(input('controller'));
                $action = strtolower(input('action'));
                $data['One'] = '您没有权限！';
                $data['Ones'] = 'You do not have permission.';
                $data['Two'] = '请联系管理员。';
                $data['Twos'] = 'Please contact the administrator.';
                $data['San'] = $module.'/'.$controller.'/'.$action;
            break;
            
            default:
                $data['One'] = '欢迎使用本系统。';
                $data['Ones'] = 'Welcome to use this system.';
                $data['Two'] = '欢迎使用本系统。';
                $data['Twos'] = 'Welcome to use this system.';
            break;
        }
        $data['type'] = $type;
        $this->assign('data',$data);
        return $this->fetch();
    }
}
