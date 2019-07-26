<?php
namespace app\manage\controller;
use app\manage\controller\Base;
use think\Controller;

use think\Db;
// use think\auth\Auth;

class Modular extends Base
{
    public function index()
    {

        return $this->fetch();
    }
    // //权限列表
    public function modular_list()
    {

        $modular = Db::name('modular')->order('modular_sort desc')->select();
        
        for ($i=0; $i < count($modular); $i++) { 
            
            switch ($modular[$i]['modular_grade']) {
                case '0':
                    $modular[$i]['modular_name'] = '|-'.$modular[$i]['modular_name'];
                break;
                case '1':
                    $modular[$i]['modular_name'] = '|----'.$modular[$i]['modular_name'];
                break;
                case '2':
                    $modular[$i]['modular_name'] = '|-------'.$modular[$i]['modular_name'];
                break;
                case '3':
                    $modular[$i]['modular_name'] = '|----------'.$modular[$i]['modular_name'];
                break;
                case '4':
                    $modular[$i]['modular_name'] = '|-------------'.$modular[$i]['modular_name'];
                break;
                case '5':
                    $modular[$i]['modular_name'] = '|----------------'.$modular[$i]['modular_name'];
                break;
                default:
                    # code...
                break;
            }
        }
        $modular = node_merge($modular,0,'modular_id','modular_father');

        $ac = $this->reformat($modular);
        $ac = array_values($ac);
        $data['code'] = 0;
        $data['msg'] = '';
        $data['count'] = count($ac);
        $data['data'] = $ac;
        $data = json_encode($data);  
        echo $data;
    }
    //多维数组转二维数组
    public function reformat($modular, $pid=0, &$ret=null) {
        foreach($modular as $k => $v) {
            $ret[$v['modular_id']] = array(
                'modular_id'=>$v['modular_id'], 
                'modular_name'=>$v['modular_name'], 
                'modular_father'=>$v['modular_father'], 
                'modular_icon'=>$v['modular_icon'], 
                'modular_sort'=>$v['modular_sort'],
                'modular_url'=>$v['modular_url'], 
                'modular_show'=>$v['modular_show']
            );
            if($v['_child']) {
                $this->reformat($v['_child'], $v['modular_id'], $ret);
            }
        }
        return $ret;
    }

    // //添加权限
    public function modular_add()
    {
        
        $modular = Db::name('modular')->order('modular_sort desc')->select();
        $modular = node_merge($modular,0,'modular_id','modular_father');
        $this->assign('modular',$modular);
        $this->assign('modular_sort',1);
        return $this->fetch();   
    }
    //添加权限FORM
    public function modular_add_form()
    {
        $data = input('post.');
        if( $data['modular_father'] == 0){
            $data['modular_grade'] = 0;
        }else{
            $modular_grade = Db::name('modular')->where('modular_id',$data['modular_father'])->value('modular_grade');
            $data['modular_grade'] = $modular_grade + 1;
        }
        if( empty($data['ids'])){

            $bool = Db::name('modular')->insertGetId($data);
        }else{
        
            $bool = Db::name('modular')->where('modular_id='.$data['ids'])->update($data);
        }
        if($bool){
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
    }

    //
    public function modular_del()
    {
        $id = input('post.id');

        $modular = Db::name('modular')->where('modular_father',$id)->find();

        if( !empty($modular)){
            $this->error('请先删除父级');
        }
        $bool = Db::name('modular')->where('modular_id',$id)->delete();
        if($bool){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }
   
}
