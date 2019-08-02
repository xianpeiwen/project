<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/8/1
 * Time: 10:54
 */

namespace app\manage\controller;


use think\Db;
use think\Exception;

class Lib extends Base
{
    public $table = 'lib';

    public function index()
    {
        return view();
    }

    public function lib_list()
    {
        $get = $this->request->get();
        $page = $get['page'] - 1;
        $size = $get['limit'];
        $data = Db::name($this->table)->order('id desc')
            ->page($page, $size);

        //搜索条件
        (isset($get['data-field']) && $get['data-field'] !== '' && $get['data-value'] !== '') && $data->whereLike($get['data-field'], "%{$get['data-value']}%");
        foreach (['user_agent_id','user_status','user_agent_id'] as $field){
            (isset($get[$field]) && $get[$field] !== '') && $data->where($field, $get[$field]);
        }
        //时间
        if (isset($get['create_at']) && $get['create_at'] !== '') {
            list($start, $end) = explode(' - ', $get['create_at']);
            $data->whereBetween('create_at', ["{$start} 00:00:00", "{$end} 23:59:59"]);
        }
        $page = $data->paginate($size);
        $db = $page->all();
        return ['code' => 0, 'count' => $page->total(), 'data' => $db];
    }

    public function del()
    {
        $post = $this->request->post();
        if (Db::name($this->table)->where('id', $post['id'])->delete()) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    public function detail()
    {
        if ($this->request->isPost()){
            $post = $this->request->post();
            $a = $post['desc'];
            if (count($post['desc'])==1 && current($a)==''){
                $lesson_desc = '';
            }else{
                $lesson_desc = json_encode(array_values($post['desc']),320);
            }
            $data = [
                'logo'=>$post['logo'],
                'name'=>$post['name'],
                'name_eng'=>$post['name_eng'],
                'desc'=>$lesson_desc,
                'tags'=>$post['tags'],
                'tags_eng'=>$post['tags_eng'],
                'train_desc'=>$post['train_desc'],
                'train_desc_eng'=>$post['train_desc_eng'],
                'train_effect'=>$post['train_effect'],
                'train_effect_eng'=>$post['train_effect_eng'],
                'note'=>$post['note'],
                'note_eng'=>$post['note_eng'],
            ];

            try{
                if ($post['id']){
                    Db::name($this->table)->where(['id'=>$post['id']])->update($data);
                }else{
                    Db::name($this->table)->insert($data);
                }
            }catch (Exception $e){
                $this->error('数据保存失败！');
            }
            $this->success('保存成功！');

        }
        $get = $this->request->get();
        $data = Db::name($this->table)->where('id',$get['id'])->find();
        $this->assign([
            'data'=>$data
        ]);
        return view();
    }

    public function add_ctrl()
    {
        if ($this->request->isPost()){
            $post = $this->request->post();
            $a = $post['desc'];
            if (count($post['desc'])==1 && current($a)==''){
                $lesson_desc = '';
            }else{
                $lesson_desc = json_encode(array_values($post['desc']),320);
            }
            $data = [
                'highlights'=>$post['highlights'],
                'pipeline'=>$post['pipeline'],
                'desc'=>$lesson_desc,
            ];

            try{
                Db::name($this->table)->where(['id'=>$post['id']])->update($data);
            }catch (Exception $e){
                $this->error('数据保存失败！');
            }
            $this->success('保存成功！');
        }
        $get = $this->request->get();
        $data = Db::name($this->table)
            ->where('id',$get['id'])->find();
        $this->assign([
            'data'=>$data,
        ]);
        return view();
    }


    public function add_action()
    {
        if ($this->request->isPost()){
            $post = $this->request->post();
            foreach ([1,2,3] as $v){
                if ($post[$v]){
                    foreach ($post[$v] as $k=>$vo){
                        $post[$v][$k]['tv_type'] = $post['ac_tv'.$v.'type'];
                    }
                }else{
                    $post[$v] = [];
                }
            }
            $data = [
                'ac_tv1type'=>$post['ac_tv1type'],
                'ac_tv2type'=>$post['ac_tv2type'],
                'ac_tv3type'=>$post['ac_tv3type'],
            ];
            try{
                Db::name($this->table)->where(['id'=>$post['id']])->update($data);

                //更新lib_action表
                Db::name('lib_action')->where('lid',$post['id'])->delete();

                $action =  array_merge($post['1'],$post['2'],$post['3']);
                if ($action){
                    foreach ($action as $key=>$value){
                        $action[$key]['lid'] = $post['id'];
                    }
                    Db::name('lib_action')->insertAll($action);
                }

            }catch (Exception $e){
                $this->error('数据保存失败！');
            }
            $this->success('保存成功！');

        }
        $get = $this->request->get();
        $data = Db::name($this->table)
            ->where('id',$get['id'])->find();
        $data['tv1'] = Db::name('lib_action')->where(['lid'=>$get['id'],'tv'=>1])->select();
        $data['tv2'] = Db::name('lib_action')->where(['lid'=>$get['id'],'tv'=>2])->select();
        $data['tv3'] = Db::name('lib_action')->where(['lid'=>$get['id'],'tv'=>3])->select();

        $this->assign([
            'data'=>$data,
            'count'=>count($data['tv1'])+count($data['tv2'])+count($data['tv3'])
        ]);
        return view();
    }

    public function add_timeline()
    {
        if ($this->request->isPost()){
            $post = $this->request->post();
//            return $post;
            $lesson_id = $post['id'];
//            $lesson_date = $post['lesson_date'];
            unset($post['file']);
            unset($post['id']);
            Db::name('lib_tl')->where('lid',$lesson_id)->delete();
            foreach ($post as $k=>$v){
                if (empty($v['line_time'])){
                    $this->error('填写每个节点的时间');
                }
                //类型为1,4的需要存外表
                if (in_array($v['line_type'],[1,4])){
                    $tlid = Db::name('lib_tl')->insertGetId([
                        'lid'=>$lesson_id,
                        'sort'=>$k,
                        'line_type'=>$v['line_type'],
                        'line_time'=>$v['line_time']
                    ]);
                    if (count($v['line_detail'])>0){
                        foreach ($v['line_detail'] as $vo){
                            Db::name('lib_tl_detail')->insert(array_merge(['tlid'=>$tlid],$vo));
                        }
                    }
                }else{
                    if ($v['line_type']==3){
                        $v['line_detail'] = 'Ready for warm up';
                    }
                    if ($v['line_type']==7){
                        $v['line_detail'] = '/images/upload/2f912cb68a52dba9/e6aa0f79ce8da960.png';
                    }
                    if ($v['line_type']==8){
                        $v['line_detail'] = 'Workout complete Great job';
                    }
                    $tlid = Db::name('lib_tl')->insertGetId([
                        'lid'=>$lesson_id,
                        'sort'=>$k,
                        'line_type'=>$v['line_type'],
                        'line_time'=>$v['line_time'],
                        'line_detail'=>$v['line_detail'],
                        'line_workouts_time'=>$v['line_workouts_time'],
                        'line_work'=>$v['line_work'],
                        'line_rest'=>$v['line_rest'],
                        'line_sets'=>$v['line_sets'],
                        'line_laps'=>$v['line_laps'],
                    ]);
                }
            }
            $this->success('保存成功！');
        }
        $get = $this->request->get();
        //查找时间线
        $line = Db::name('lib_tl')->where(['lid' => $get['id']])->order('sort asc')->select();
        $detail = Db::name('lib_tl_detail')->where('tlid','in',array_column($line,'id'))->select();
        foreach ($line as $k=>$l){
            foreach ($detail as $d){
                if ($l['id'] == $d['tlid']){
                    $line[$k]['line_detail'][] = $d;
                }
            }
        }
        $count = Db::name('lib_action')->where(['lid'=>$get['id']])->count('*');
        $data = $line;
        $this->assign([
            'data'=>$data,
            'count'=>$count,
            'id'=>$get['id']
        ]);
        return view();
    }
}

