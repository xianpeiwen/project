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
        $today = $this->request->get('today')?:date('Y-m-d',time());
        //开始时间与today时间关联，开始时间为today时间的当周开始时间
//        $start = mktime(0,0,0,date('m'),date('d')-date('w'),date('Y'));
        $start = mktime(0,0,0,date('m',strtotime($today)),date('d',strtotime($today))-date('w',strtotime($today)),date('Y',strtotime($today)));
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
                if ($v['lesson_date']==$today){
                    $list[$k]['today'] = 'today';
                }
            }
        }
        $this->assign([
            'data'=>$list
        ]);
        return view();
    }

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
                'lesson_desc'=>$lesson_desc,
                'lesson_tags'=>$post['lesson_tags'],
                'lesson_tags_eng'=>$post['lesson_tags_eng'],
                'lesson_train_desc'=>$post['lesson_train_desc'],
                'lesson_train_desc_eng'=>$post['lesson_train_desc_eng'],
                'lesson_train_effect'=>$post['lesson_train_effect'],
                'lesson_train_effect_eng'=>$post['lesson_train_effect_eng'],
                'lesson_note'=>$post['lesson_note'],
                'lesson_note_eng'=>$post['lesson_note_eng'],
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
        $data = Db::name($this->table)
            ->field('lesson_id,lesson_date,lesson_logo,lesson_name,lesson_name_eng,lesson_desc,lesson_tags,lesson_tags_eng,lesson_train_desc,lesson_train_desc_eng,lesson_train_effect,lesson_train_effect_eng,lesson_note,lesson_note_eng')
            ->where('lesson_id',$get['lesson_id'])->find();
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
        $data = Db::name($this->table)
            ->field('lesson_id,lesson_date,lesson_highlights,lesson_pipeline,lesson_movement')
            ->where('lesson_id',$get['lesson_id'])->find();
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
            //新的课程
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

    public function add_action(){
        if ($this->request->isPost()){
            $post = $this->request->post();
            foreach ([1,2,3] as $v){
                if ($post[$v]){
                    foreach ($post[$v] as $k=>$vo){
                        $post[$v][$k]['tv_type'] = $post['lesson_ac_tv'.$v.'type'];
                    }
                }else{
                    $post[$v] = [];
                }
            }

            $data = [
                'lesson_date'=>$post['lesson_date'],
                'lesson_movement'=>$post['lesson_movement'],
                'lesson_ac_member'=>$post['lesson_ac_member'],
                'lesson_ac_steps'=>$post['lesson_ac_steps'],
                'lesson_ac_pods'=>$post['lesson_ac_pods'],
                'lesson_ac_rest'=>$post['lesson_ac_rest'],
                'lesson_ac_total'=>$post['lesson_ac_total'],
                'lesson_ac_sets'=>$post['lesson_ac_sets'],
                'lesson_ac_laps'=>$post['lesson_ac_laps'],
                'lesson_ac_tv1type'=>$post['lesson_ac_tv1type'],
                'lesson_ac_tv2type'=>$post['lesson_ac_tv2type'],
                'lesson_ac_tv3type'=>$post['lesson_ac_tv3type'],
            ];
            try{
                if ($post['lesson_id']){
                    Db::name($this->table)->where(['lesson_id'=>$post['lesson_id'],'lesson_date'=>$post['lesson_date']])->update($data);
                }else{
                    $post['lesson_id'] = Db::name($this->table)->insertGetId($data);
                }
                //更新lesson_action表
                Db::name('lesson_action')->where('lid',$post['lesson_id'])->delete();

                $action =  array_merge($post['1'],$post['2'],$post['3']);

                if ($action){
                    foreach ($action as $key=>$value){
                        $action[$key]['lid'] = $post['lesson_id'];
                    }
                    Db::name('lesson_action')->insertAll($action);
                }

            }catch (Exception $e){
                $this->error('数据保存失败！');
            }
            $this->success('保存成功！');

        }
        $get = $this->request->get();
        if (empty($get['lesson_id'])){
            $data = [];
        }else{
            $data = Db::name($this->table)
                ->field('lesson_movement,lesson_ac_member,lesson_ac_steps,lesson_ac_pods,lesson_ac_rest,lesson_ac_total,lesson_ac_sets,lesson_ac_laps,lesson_ac_tv1type,lesson_ac_tv2type,lesson_ac_tv3type')
                ->where('lesson_id',$get['lesson_id'])->find();
            $data['tv1'] = Db::name('lesson_action')->where(['lid'=>$get['lesson_id'],'tv'=>1])->select();
            $data['tv2'] = Db::name('lesson_action')->where(['lid'=>$get['lesson_id'],'tv'=>2])->select();
            $data['tv3'] = Db::name('lesson_action')->where(['lid'=>$get['lesson_id'],'tv'=>3])->select();
        }

        $this->assign([
            'data'=>$data,
            'lesson_date'=>$get['lesson_date'],
            'lesson_id'=>$get['lesson_id']
        ]);
        return view();
    }

    public function chose(){
        return view();
    }
}