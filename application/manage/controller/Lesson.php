<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/7/22
 * Time: 14:13
 */

namespace app\manage\controller;


use think\Db;
use think\Exception;

class Lesson extends Base
{
    public $table = 'lesson';

    public function index()
    {
        $start = mktime(0,0,0,date('m'),date('d')-date('w'),date('Y'));
//        $end = mktime(0,0,0,date('m'),date('d'),date('Y'));
        $db = Db::name($this->table)->whereBetween('lesson_date',[date('Y-m-d',$start),date('Y-m-d',$start+(86400*6))])->select();
        $list = [
            ['lesson_date'=>date('Y-m-d',$start),'week'=>'周日'],
            ['lesson_date'=>date('Y-m-d',$start+86400),'week'=>'周一'],
            ['lesson_date'=>date('Y-m-d',$start+(86400*2)),'week'=>'周二'],
            ['lesson_date'=>date('Y-m-d',$start+(86400*3)),'week'=>'周三'],
            ['lesson_date'=>date('Y-m-d',$start+(86400*4)),'week'=>'周四'],
            ['lesson_date'=>date('Y-m-d',$start+(86400*5)),'week'=>'周五'],
            ['lesson_date'=>date('Y-m-d',$start+(86400*6)),'week'=>'周六'],
        ];
        foreach ($list as $k=>$v){
            foreach ($db as $kk=>$vv){
                if ($vv['lesson_date']==$v['lesson_date']){
                    $list[$k] = array_merge($vv,$v);
                }
                if ($v['lesson_date']==date('Y-m-d',time())){
                    $list[$k]['today'] = 'today';
                }
            }
        }
        $this->assign([
            'data'=>$list
        ]);
        return view();
    }

    //废弃的
    /*public function add()
    {
        if ($this->request->isPost()){
            $post = $this->request->post();
            $a = $post['lesson_desc'];
            if (count($post['lesson_desc'])==1 && current($a)==''){
                $lesson_desc = '';
            }else{
                $lesson_desc = json_encode(array_values($post['lesson_desc']),320);
            }
            $data = [
                'lesson_date'=>$post['lesson_date'],
                'lesson_logo'=>$post['lesson_logo'],
                'lesson_name'=>$post['lesson_name'],
                'lesson_name_eng'=>$post['lesson_name_eng'],
                'lesson_desc'=>$lesson_desc,
                'lesson_highlights'=>$post['lesson_highlights'],
                'lesson_pipeline'=>$post['lesson_pipeline'],
                'lesson_movement'=>$post['lesson_movement'],
            ];

            try{
                if ($post['lesson_id']){
                    Db::name($this->table)->where(['lesson_id'=>$post['lesson_id'],'lesson_date'=>$post['lesson_date']])->update($data);
                }else{
                    Db::name($this->table)->insert($data);
                }
            }catch (Exception $e){
                $this->error('数据保存失败！');
            }
            $this->success('保存成功！');

        }
        $get = $this->request->get();
        $data = Db::name($this->table)->where('lesson_id',$get['lesson_id'])->find();
        $this->assign([
            'data'=>$data,
            'lesson_date'=>$get['lesson_date']
        ]);
        return view();
    }*/

    //上一周下一周
    public function pre()
    {
        $get = $this->request->get();
        if ($get['type'] == 'pre'){
            $start = strtotime($get['lesson_date'])-(86400*7);
        }else{
            $start = strtotime($get['lesson_date'])+86400;
        }

        $db = Db::name($this->table)->whereBetween('lesson_date',[date('Y-m-d',$start),date('Y-m-d',$start+(86400*6))])->select();
        $list = [
            ['lesson_date'=>date('Y-m-d',$start),'week'=>'周日'],
            ['lesson_date'=>date('Y-m-d',$start+86400),'week'=>'周一'],
            ['lesson_date'=>date('Y-m-d',$start+(86400*2)),'week'=>'周二'],
            ['lesson_date'=>date('Y-m-d',$start+(86400*3)),'week'=>'周三'],
            ['lesson_date'=>date('Y-m-d',$start+(86400*4)),'week'=>'周四'],
            ['lesson_date'=>date('Y-m-d',$start+(86400*5)),'week'=>'周五'],
            ['lesson_date'=>date('Y-m-d',$start+(86400*6)),'week'=>'周六'],
        ];
        foreach ($list as $k=>$v){
            foreach ($db as $kk=>$vv){
                if ($vv['lesson_date']==$v['lesson_date']){
                    $list[$k] = array_merge($vv,$v);
                }
                if ($v['lesson_date']==date('Y-m-d',time())){
                    $list[$k]['today'] = 'today';
                }
            }
        }
        $this->assign([
            'data'=>$list
        ]);

        return view();
    }

    //基本信息
    public function add_base()
    {
        if ($this->request->isPost()){
            $post = $this->request->post();
            $a = $post['lesson_desc'];
            if (count($post['lesson_desc'])==1 && current($a)==''){
                $lesson_desc = '';
            }else{
                $lesson_desc = json_encode(array_values($post['lesson_desc']),320);
            }
            $data = [
                'lesson_date'=>$post['lesson_date'],
                'lesson_logo'=>$post['lesson_logo'],
                'lesson_name'=>$post['lesson_name'],
                'lesson_name_eng'=>$post['lesson_name_eng'],
                'lesson_desc'=>$lesson_desc
            ];

            try{
                if ($post['lesson_id']){
                    Db::name($this->table)->where(['lesson_id'=>$post['lesson_id'],'lesson_date'=>$post['lesson_date']])->update($data);
                }else{
                    Db::name($this->table)->insert($data);
                }
            }catch (Exception $e){
                $this->error('数据保存失败！');
            }
            $this->success('保存成功！');

        }
        $get = $this->request->get();
        $data = Db::name($this->table)->where('lesson_id',$get['lesson_id'])->find();
        $this->assign([
            'data'=>$data,
            'lesson_date'=>$get['lesson_date']
        ]);
        return view();
    }

    public function add_ctrl()
    {
        if ($this->request->isPost()){
            $post = $this->request->post();
            $data = [
                'lesson_date'=>$post['lesson_date'],
                'lesson_highlights'=>$post['lesson_highlights'],
                'lesson_pipeline'=>$post['lesson_pipeline'],
                'lesson_movement'=>$post['lesson_movement'],
            ];

            try{
                if ($post['lesson_id']){
                    Db::name($this->table)->where(['lesson_id'=>$post['lesson_id'],'lesson_date'=>$post['lesson_date']])->update($data);
                }else{
                    Db::name($this->table)->insert($data);
                }
            }catch (Exception $e){
                $this->error('数据保存失败！');
            }
            $this->success('保存成功！');

        }
        $get = $this->request->get();
        $data = Db::name($this->table)->where('lesson_id',$get['lesson_id'])->find();
        $this->assign([
            'data'=>$data,
            'lesson_date'=>$get['lesson_date']
        ]);
        return view();
    }

    public function add_timeline()
    {
        if ($this->request->isPost()){
            $post = $this->request->post();
//            return $post;
            if (empty($post['lesson_id'])){
                $post['lesson_id'] = Db::name($this->table)->insertGetId(['lesson_date'=>$post['lesson_date']]);
            }
            $lesson_id = $post['lesson_id'];
//            $lesson_date = $post['lesson_date'];
            unset($post['file']);
            unset($post['lesson_id']);
            unset($post['lesson_date']);
            Db::name('lesson_tl')->where('lid',$lesson_id)->delete();
            foreach ($post as $k=>$v){
                //类型为1,2,4的需要存外表
                if (in_array($v['line_type'],[1,2,4])){
                    $tlid = Db::name('lesson_tl')->insertGetId([
                        'lid'=>$lesson_id,
                        'sort'=>$k,
                        'line_type'=>$v['line_type'],
                        'line_time'=>$v['line_time']
                    ]);
                    if (count($v['line_detail'])>0){
                        foreach ($v['line_detail'] as $vo){
                            Db::name('tl_detail')->insert(array_merge(['tlid'=>$tlid],$vo));
                        }
                    }
                }else{
                    if ($v['line_type']==3){
                        $v['line_detail'] = 'Ready for warm up';
                    }
                    if ($v['line_type']==7){
                        $v['line_detail'] = '一张图片';
                    }
                    if ($v['line_type']==8){
                        $v['line_detail'] = 'Workout complete Great job';
                    }
                    $tlid = Db::name('lesson_tl')->insertGetId([
                        'lid'=>$lesson_id,
                        'sort'=>$k,
                        'line_type'=>$v['line_type'],
                        'line_time'=>$v['line_time'],
                        'line_detail'=>$v['line_detail']
                    ]);
                }
            }
            $this->success('保存成功！');
        }
        $get = $this->request->get();
        if (empty($get['lesson_id'])){
            $data = [];
        }else{
            //查找时间线
            $line = Db::name('lesson_tl')->where(['lid' => $get['lesson_id']])->select();
            $detail = Db::name('tl_detail')->where('tlid','in',array_column($line,'id'))->select();
            foreach ($line as $k=>$l){
                foreach ($detail as $d){
                    if ($l['id'] == $d['tlid']){
                        $line[$k]['line_detail'][] = $d;
                    }
                }
            }
            $data = $line;

        }
        $this->assign([
            'data'=>$data,
            'lesson_date'=>$get['lesson_date'],
            'lesson_id'=>$get['lesson_id'],
        ]);
        return view();
    }


}