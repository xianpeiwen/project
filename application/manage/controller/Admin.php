<?php
namespace app\manage\controller;
use app\manage\controller\Base;
use think\Controller;
use think\Db;
// use think\auth\Auth;

class Admin extends Base
{
    public function index()
    {
        
        return $this->fetch();
    
    }
    //会员添加编辑
    public function admin_add()
    {
        $auth_group = Db::name('auth_group')->where('status',1)->order('sort desc')->field('title,pid,id')->select();
        $auth_group = node_merge($auth_group);
        $this->assign('auth_group',$auth_group);

        $id = input('get.id');
        if($id != 0){
            // $res['admin'] = Db::name('admin')->where('admin_id',$id)->find();
            // $res['img'] = Db::name('image')->where('image_surface_id',$id)->where('image_surface_name','admin')->find();
            $admin = Db::name('admin')
                ->alias('a')
                ->join('manage_auth_group_access access','a.admin_id = access.uid')
                ->join('manage_image image','a.admin_id = image.image_surface_id')
                ->where('a.admin_id='.$id)
                ->where('image.image_surface_name','admin')
                ->order('a.admin_sort desc')
                ->field('a.admin_id,a.admin_name,a.admin_phone,a.admin_email,a.admin_add_time,a.admin_status,a.admin_sort,image.image_path,image.image_name,access.group_id')
                ->find();
        }else{
            $admin['image_path'] = 'public/';
            $admin['image_name'] = '001.jpg';
            $admin['admin_sort'] =  1;
        }
        $this->assign('admin',$admin);
        return $this->fetch();
    }
    //会员添加编辑的FORM
    public function admin_add_form()
    {
        $data = input('post.');

        if( !empty($data['admin_pass'])){
            $data['admin_pass'] = password_hash($data['admin_pass'],PASSWORD_DEFAULT);
        }else{
            unset($data['admin_pass']);
        }

        if(empty($data['ids'])){

            if( empty($data['admin_pass'])){
                $this->error('密码不能为空！！！');
            }
            $admin_phone = Db::name('admin')->where('admin_phone',$data['admin_phone'])->find();
            if( !empty($admin_phone)){
                $this->error('您输入的手机号码已存在！！！');
            }
            $admin_email = Db::name('admin')->where('admin_email',$data['admin_email'])->find();
            if( !empty($admin_email)){
                $this->error('您输入的邮箱已存在！！！');
            }
            $data['admin_add_time'] = time();
            $id = Db::name('admin')->insertGetId($data);
        }else{

            $admin_phone = Db::name('admin')->where('admin_id','not between',[$data['ids']])->where('admin_phone',$data['admin_phone'])->find();

            if( !empty($admin_phone)){
                $this->error('您输入的手机号码已存在！！！');
            }
            $admin_email = Db::name('admin')->where('admin_id','not between',[$data['ids']])->where('admin_email',$data['admin_email'])->find();
            if( !empty($admin_email)){
                $this->error('您输入的邮箱已存在！！！');
            }

            $res = Db::name('admin')->where('admin_id', $data['ids'])->update($data);

            $id = $data['ids'];
        }
        if( !empty($data['imgShow'])){
            $img = model('Image')->findImg_deleteImg_addImg($data['imgShow'],'admin',$id,'admin/');
        }



        if($id){
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
    }

    public function admin_list()
    {

        $page = input('get.page');
        $limit = input('get.limit');
        $page = ($page - 1) * $limit;
        $admin = Db::name('admin')->limit($page,$limit)->order('admin_sort asc,admin_id desc')->select();
        for( $i = 0; $i < count($admin);$i++){
//            $admin[$i]['image'] = $this->FIND_IMAGE('admin',$admin[$i]['admin_id']);
            $admin[$i]['image'] = model('Image')->findImg('admin',$admin[$i]['admin_id']);
        }

        $data['code'] = 0;
        $data['msg'] = '';
        $data['count'] = Db::name('admin')->count();
        $data['data'] = $admin;
        $data = json_encode($data);
        echo $data;
    }
    public function admin_del()
    {
        $id = input('post.id');

        $img = model('Image')->findImg('admin',$id);

        if( !empty($img)){
            model('Image')->deleteImg($img);
        }
        $bool = Db::name('admin')->delete($id);

        if( $bool){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }
    public function admin_auth_list()
    {
        $id = input('get.id');
        if( !empty($id)){
            $auth_group_access = Db::name('auth_group_access')->where('uid',$id)->field('group_id')->select();
            for ($i=0; $i < count($auth_group_access); $i++) { 
                $rules[$i] = $auth_group_access[$i]['group_id'];
            }
            // $rules = explode(',', $rules);
        }
        $auth_group = Db::name('auth_group')->where('status',1)->order('sort desc')->field('id value,title name,pid')->select();
        for ($i=0; $i < count($auth_group); $i++) { 
            if( !empty($rules)){
                for ($j=0; $j < count($rules); $j++) { 
                    if( $rules[$j] == $auth_group[$i]['value']){
                        $auth_group[$i]['checked'] = true;
                        break;
                    }else{
                        $auth_group[$i]['checked'] = false;
                    }
                }
            }else{
                $auth_group[$i]['checked'] = false;
            }
        }
        $auth_group = node_merge($auth_group,0,'value','pid','list');
        $auth_group = filter_array($auth_group,[[]]);
        $data['code'] = 0;
        $data['msg'] = '';
        $data['count'] = count($auth_group);
        $data['data']['trees'] = $auth_group;
        $data = json_encode($data);    
        echo $data;
    }
}
