<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/7/16
 * Time: 11:42
 */

namespace app\manage\controller;


use think\Db;

class Thumb extends Base
{
    public function index()
    {
        if ($this->request->isPost())
        {

        }
        $group = Db::name('thumb_group')->select();
        $thumb = Db::name('thumb')->select();
        $this->assign([
            'group'=>$group,
            'thumb'=>$thumb
        ]);
        return view();
    }

    public function upload()
    {
        $file = $this->request->file('file');
//        pp($file);

        //检查是否已有该图片
        $md5 = md5($file->getInfo('name'));
        $ext = strtolower(pathinfo($file->getInfo('name'), 4));
        $names = str_split($md5, 16);
        $filename = "{$names[0]}/{$names[1]}.{$ext}";
        if (file_exists( ROOT_PATH.'public/images/upload/' . $filename)){
            return ['code'=>1,'url'=>'/images/upload/'.$filename];
        }

        //上传
        if ($res = $file->move("images/upload/{$names[0]}", "{$names[1]}.{$ext}",true)){
            \app\manage\model\Thumb::create(['url'=>'/images/upload/'.$filename]);
            return ['code'=>1,'url'=>'/images/upload/'.$filename];
        }else{
            return ['code'=>0];
        }

    }

    public function group_add()
    {
        if ($this->request->isPost()){
            Db::name('thumb_group')->insert(['name'=>$this->request->post('name')]);
            return '1';
        }
        return view();
    }
}