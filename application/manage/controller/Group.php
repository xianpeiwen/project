<?php
namespace app\manage\controller;
use app\manage\controller\Base;
use think\Controller;
use think\Db;
// use think\auth\Auth;
class Group extends Base
{
    //列表的视图
    public function index()
    {
        return $this->fetch();
    }
    public function grout_list()
    {
        $auth_group = Db::name('auth_group')->order('sort desc')->select();
        $auth_group = node_merge($auth_group);
        $ac = $this->reformat($auth_group);
        if(!empty($ac)){

            $ac = array_values($ac);
        }
        $data['code'] = 0;
        $data['msg'] = '';
        $data['count'] = count($ac);
        $data['data'] = $ac;
        $data = json_encode($data);  
        echo $data;
    }
    //多维数组转二维数组
    public function reformat($auth_rule, $pid=0, &$ret=null,$num=0) {
        switch ($num) {
            case '0':
                $r = '|-';
            break;
            case '1':
                $r = '|----';
            break;
            case '2':
                $r = '|-------';
            break;
            case '3':
                $r = '|----------';
            break;
            case '4':
                $r = '|-------------';
            break;
            default:
                # code...
                break;
        }

        foreach($auth_rule as $k => $v) {
            $v['update_time'] = date('Y-m-d H:i:s',$v['update_time']);
            $v['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
            $ret[$v['id']] = array(
                'id'=>$v['id'], 
                'rules'=>$v['rules'], 
                'title'=>$r.$v['title'],
                'status'=>$v['status'], 
                'pid'=>$v['pid'],
                'sort'=>$v['sort'], 
                'update_time'=>$v['update_time'],
                'add_time'=>$v['add_time']
            );
            if($v['_child']) {
                $num = $num + 1;
                $this->reformat($v['_child'], $v['id'], $ret,$num);
            }
        }
        return $ret;
    }
    //添加编辑管理员组的视图
    public function group_add()
    {
    	$auth_group = Db::name('auth_group')->order('sort desc')->select();
        $auth_group = node_merge($auth_group);
        $this->assign('auth_group',$auth_group);
        return $this->fetch();
    }
    //添加编辑管理员组
    public function group_add_form()
    {
        $data = input('post.');
        $data['rules'] = implode(',', $data['rules']);
        $data['update_time'] = time();
        if( empty($data['ids'])){

            $data['add_time'] = time();
            $bool = Db::name('auth_group')->insert($data);
        }else{
            $bool = Db::name('auth_group')->where('id',$data['ids'])->update($data);
        }
        if( $bool ){
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
    }
    //获取权限
    public function group_auth_list()
    {
        $id = input('get.id');
        if( !empty($id)){
            $rules = Db::name('auth_group')->where('id',$id)->value('rules');
            $rules = explode(',', $rules);
        }
        $auth_rule = Db::name('auth_rule')->where('status',1)->order('sort desc')->field('id value,title name,pid')->select();
        for ($i=0; $i < count($auth_rule); $i++) { 
            if( !empty($rules)){
                for ($j=0; $j < count($rules); $j++) { 
                    if( $rules[$j] == $auth_rule[$i]['value']){
                        $auth_rule[$i]['checked'] = true;
                        break;
                    }else{
                        $auth_rule[$i]['checked'] = false;
                    }
                }
            }else{
                $auth_rule[$i]['checked'] = false;
            }
        }
        $auth_rule = node_merge($auth_rule,0,'value','pid','list');
        $auth_rule = filter_array($auth_rule,[[]]);
        $data['code'] = 0;
        $data['msg'] = '';
        $data['count'] = count($auth_rule);
        $data['data']['trees'] = $auth_rule;
        $data = json_encode($data);    
        echo $data;
    }
    //删除
    public function group_del()
    {
        $id = input('post.id');

        $res = Db::name('auth_group')->where('pid',$id)->find();
        if( empty($res)){
            $bool = Db::name('auth_group')->delete($id);
            if( $bool ){
                $this->success('删除成功');
            }else{
                $this->error('删除失败');
            }
        }else{
            $this->error('请先删除下级管理组');
        }
    }
}
