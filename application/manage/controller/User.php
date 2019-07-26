<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/7/18
 * Time: 18:31
 */

namespace app\manage\controller;


use think\Db;

class User extends Base
{
    public $table = 'user';

    public function index()
    {
        $agent = Db::name('agent')->field('agent_id,agent_name')->order('sort desc')->select();
        $count = Db::name($this->table)->count('*');
        $tab1 = Db::name($this->table)->where('user_status', 1)->count('*');
        $tab2 = Db::name($this->table)->where('user_status', 0)->count('*');
        $this->assign([
            'agent'=>$agent,
            'count'=>$count,
            'tab1'=>$tab1,
            'tab2'=>$tab2
        ]);
        return view();
    }

    public function user_list()
    {

        $get = $this->request->get();
        $page = $get['page'] - 1;
        $size = $get['limit'];
        $data = Db::name($this->table)
            ->field('user_id,user_nickname,user_headimgurl,user_phone,user_sex,create_at,user_status,user_end_at,user_balance')
            ->page($page, $size);

        //搜索条件
        (isset($get['data-field']) && $get['data-field'] !== '' && $get['data-value'] !== '') && $data->whereLike($get['data-field'], "%{$get['data-value']}%");
        foreach (['user_agent_id','user_status'] as $field){
            (isset($get[$field]) && $get[$field] !== '') && $data->where($field, $get[$field]);
        }
        //时间
        if (isset($get['create_at']) && $get['create_at'] !== '') {
            list($start, $end) = explode(' - ', $get['create_at']);
            $data->whereBetween('create_at', ["{$start} 00:00:00", "{$end} 23:59:59"]);
        }
        $page = $data->paginate($size);
        $db = $page->all();
        foreach ($db as $k=>$v){
            if (empty($v['user_end_at']) || strtotime($v['user_end_at'])<time()  ){
                $db[$k]['user_end_at'] = 'out_of_date';
            }
        }
        return ['code' => 0, 'count' => $page->total(), 'data' => $db];
    }

    /**禁用/启用
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function forbid()
    {
        $post = $this->request->post();
        if (Db::name($this->table)->where('user_id', $post['id'])->update(['user_status' => $post['status']])) {
            $this->success('更改成功');
        } else {
            $this->error('更改失败');
        }
    }

    public function del()
    {
        $post = $this->request->post();
        if (Db::name($this->table)->where('user_id', $post['id'])->delete()) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    public function edit()
    {
        if ($this->request->isGet()) {
            $id = $this->request->get('id');
            $data = Db::name($this->table)->where('user_id', $id)->find();
            $this->assign([
                'data' => $data
            ]);
            return view();
        } elseif ($this->request->isPost()) {
            $post = $this->request->post();
            unset($post['file']);
            if (Db::name($this->table)->update($post)) {
                $this->success('修改成功');
            }else{
                $this->error('更改失败');
            }
        }
    }
}