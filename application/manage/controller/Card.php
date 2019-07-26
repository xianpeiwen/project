<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/7/17
 * Time: 17:41
 */

namespace app\manage\controller;


use think\Db;

class Card extends Base
{
    public $table = 'card';
    public function index()
    {
        $count = Db::name($this->table)->count('*');
        $tab1 = Db::name($this->table)->where('card_type', 1)->count('*');
        $tab2 = Db::name($this->table)->where('card_type', 2)->count('*');
        $this->assign([
            'count'=>$count,
            'tab1'=>$tab1,
            'tab2'=>$tab2,
        ]);
        return view();
    }

    /**禁用/启用
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function forbid()
    {
        $post = $this->request->post();
        if (Db::name('card')->where('card_id',$post['id'])->update(['card_status'=>$post['status']])){
            $this->success('更改成功');
        }else{
            $this->error('更改失败');
        }
    }

    public function del()
    {
        $post = $this->request->post();
        if (Db::name($this->table)->where('card_id',$post['id'])->delete()){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }

    public function card_list()
    {
        $get = $this->request->get();
        $page = $get['page']-1;
        $size = $get['limit'];
        $data = Db::name('card')
            ->field('card_id,card_name,card_name_eng,card_day_limit,card_price,card_discount,create_at,card_status,card_cost,card_type')
            ->order('sort desc')
            ->page($page,$size);

        //搜索条件
        (isset($get['data-field']) && $get['data-field'] !== '' && $get['data-value'] !== '') && $data->whereLike($get['data-field'], "%{$get['data-value']}%");
        foreach (['card_type'] as $field){
            (isset($get[$field]) && $get[$field] !== '') && $data->where($field, $get[$field]);
        }
        //时间
        if (isset($get['create_at']) && $get['create_at'] !== '') {
            list($start, $end) = explode(' - ', $get['create_at']);
            $data->whereBetween('create_at', ["{$start} 00:00:00", "{$end} 23:59:59"]);
        }
        $page = $data->paginate($size);

        return ['code'=>0,'count'=>$page->total(),'data'=>$page->all()];
    }

    public function add()
    {
        if ($this->request->isGet())
        {
            $id = $this->request->get('id');
            $card_type = $this->request->get('card_type');
            if ($card_type==2){
                $tpl = 'add2';
            }else{
                $tpl = 'add';
            }
            if ($id){
                $data = Db::name($this->table)->where('card_id',$id)->find();
                $this->assign(['data'=>$data]);
            }
            return view($tpl);
        }elseif ($this->request->isPost()){
            $post = $this->request->post();
            $data = [];
            foreach (['card_name','card_name_eng','card_price','card_type','card_day_limit'] as $v){
                if (empty($post[$v])){
                    $this->error('缺少必要信息'.$v);
                }
            }
            if ($post['card_price']<=0){
                $this->error('价格需大于0');
            }
            if ($post['card_type']==2){
                (empty($post['card_cost']) || $post['card_cost']<=0 || $post['card_discount']<0) && $this->error('请输入每次消费扣除金额');
                $data['card_cost'] = $post['card_cost'];
                $data['card_discount'] = $post['card_discount'];
            }

            $data['card_name'] = $post['card_name'];
            $data['card_name_eng'] = $post['card_name_eng'];
            $data['card_price'] = $post['card_price'];
            $data['card_type'] = $post['card_type'];
            $data['card_day_limit'] = $post['card_day_limit'];
            $data['card_status'] = $post['status'];
            $data['card_desc'] = $post['card_desc'];
            $data['card_desc_eng'] = $post['card_desc_eng'];
            $data['sort'] = $post['sort'];
            if ($post['card_id']){
                if (Db::name($this->table)->where('card_id', $post['card_id'])->update($data)) {
                    $this->success('更新成功');
                }else{
                    $this->error('数据更新失败');
                }
            }
            if (Db::name('card')->insert($data)){
                $this->success('添加成功');
            }else{
                $this->error('数据插入失败');
            }

        }
    }
}