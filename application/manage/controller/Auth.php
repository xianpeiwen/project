<?php
namespace app\manage\controller;
use app\manage\controller\Base;
use think\Controller;
use think\Db;
// use think\auth\Auth;
class Auth extends Base
{
    public function index()
    {
        return $this->fetch();
    }
    //权限列表
    public function auto_list()
    {
        $auth_rule = Db::name('auth_rule')->order('sort desc')->select();
        for ($i=0; $i < count($auth_rule); $i++) { 
            
            switch ($auth_rule[$i]['level']) {
                case '1':
                    $auth_rule[$i]['title'] = '|-'.$auth_rule[$i]['title'];
                break;
                case '2':
                    $auth_rule[$i]['title'] = '|----'.$auth_rule[$i]['title'];
                break;
                case '3':
                    $auth_rule[$i]['title'] = '|-------'.$auth_rule[$i]['title'];
                break;
                case '4':
                    $auth_rule[$i]['title'] = '|----------'.$auth_rule[$i]['title'];
                break;
                case '5':
                    $auth_rule[$i]['title'] = '|-------------'.$auth_rule[$i]['title'];
                break;
                case '6':
                    $auth_rule[$i]['title'] = '|----------------'.$auth_rule[$i]['title'];
                break;
                default:
                    # code...
                break;
            }
        }
        $auth_rule = node_merge($auth_rule);
        $ac = $this->reformat($auth_rule);
        $ac = array_values($ac);
        $data['code'] = 0;
        $data['msg'] = '';
        $data['count'] = count($ac);
        $data['data'] = $ac;
        $data = json_encode($data);  
        echo $data;
    }
    //多维数组转二维数组
    public function reformat($auth_rule, $pid=0, &$ret=null) {
        foreach($auth_rule as $k => $v) {
            $v['update_time'] = date('Y-m-d H:i:s',$v['update_time']);
            $v['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
            $ret[$v['id']] = array(
                'id'=>$v['id'], 
                'name'=>$v['name'], 
                'title'=>$v['title'],
                'status'=>$v['status'], 
                'condition'=>$v['condition'], 
                'pid'=>$v['pid'],
                'level'=>$v['level'], 
                'sort'=>$v['sort'], 
                'update_time'=>$v['update_time'],
                'add_time'=>$v['add_time'], 
                'icon'=>$v['icon'],
                'show'=>$v['show']
            );
            if($v['_child']) {
                $this->reformat($v['_child'], $v['id'], $ret);
            }
        }
        return $ret;
    }
    //添加权限
    public function auth_add()
    {
        
        $auth_rule = Db::name('auth_rule')->order('sort desc')->select();
        $auth_rule = node_merge($auth_rule);
        $this->assign('auth_rule',$auth_rule);
        return $this->fetch();   
    }
    //添加权限FORM
    public function auto_add_form()
    {
        $data = input('post.');
        if($data['pid'] == 0){
            $data['level'] = 0 + 1;   
        }else{
            $level = Db::name('auth_rule')->where('id='.$data['pid'])->value('level');
            $data['level'] = $level + 1;
        }
        $data['update_time'] = time();
        if( empty($data['ids'])){
            $data['add_time'] = time();
            $bool = Db::name('auth_rule')->insertGetId($data);
        }else{
        
            $bool = Db::name('auth_rule')->where('id='.$data['ids'])->update($data);
        }
        if($bool){
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
    }
    //删除
    public function auto_del()
    {
        $id = input('post.id');
        
        $res = Db::name('auth_rule')->where('pid',$id)->find();
        if( empty($res)){
            $bool = Db::name('auth_rule')->delete($id);
            if( $bool ){
                $this->success('删除成功');
            }else{
                $this->error('删除失败');
            }
        }else{
            $this->error('请先删除子集');
        }
    }
}
