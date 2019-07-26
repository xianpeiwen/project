<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/7/15
 * Time: 16:14
 */

namespace app\manage\controller;


use think\Db;

class Agent extends Base
{
    public function index()
    {
        $count = Db::name('agent')->count('*');
        $tab1 = Db::name('agent')->where('agent_status', 1)->count('*');
        $tab2 = Db::name('agent')->where('agent_status', 0)->count('*');
        $this->assign([
            'count'=>$count,
            'tab1'=>$tab1,
            'tab2'=>$tab2,
        ]);
        return view();
    }

    public function del()
    {
        $post = $this->request->post();
        if (Db::name('agent')->where('agent_id',$post['id'])->delete()){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }

    /**禁用/启用
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function forbid()
    {
        $post = $this->request->post();
        if (Db::name('agent')->where('agent_id',$post['id'])->update(['agent_status'=>$post['status']])){
            $this->success('更改成功');
        }else{
            $this->error('更改失败');
        }
    }

    /**请求列表数据
     * @return array
     * @throws \think\exception\DbException
     */
    public function agent_list()
    {
        $get = $this->request->get();
        $page = $get['page']-1;
        $size = $get['limit'];
        $data = Db::name('agent')
            ->field('agent_id,agent_username,agent_addr,agent_name,agent_psw,agent_link,agent_phone,create_at,agent_status,agent_studio_id')
            ->order('sort desc')
            ->page($page,$size);

        //搜索条件
        (isset($get['data-field']) && $get['data-field'] !== '' && $get['data-value'] !== '') && $data->whereLike($get['data-field'], "%{$get['data-value']}%");
        foreach (['agent_status'] as $field){
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

    /**添加/编辑门店
     * @return \think\response\View
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function agent_add()
    {
        if ($this->request->isGet())
        {
            $id = $this->request->get('id');
            if ($id){
                $data = Db::name('agent')->where('agent_id',$id)->find();
                $this->assign(['data'=>$data]);
            }
            return view();
        } elseif($this->request->isPost()) {
            $post = $this->request->post();
            foreach (['agent_name','agent_username','agent_psw','psw_com','agent_link','agent_phone','city-picker','agent_addr','lng','lat','agent_studio_id'] as $v){
                if (empty($post[$v])){
                    $this->error('缺少必要信息'.$v);
                }
            }
            if (strlen($post['agent_psw'])<6){
                $this->error('密码不小于六位');
            }
            if ($post['agent_psw']!==$post['psw_com']){
                $this->error('2次密码不一致');
            }
            if(!preg_match("/^1[345789]\d{9}$/", $post['agent_phone'])){
                $this->error('请输入正确的手机号');
            }

            //编辑
            if (!empty($post['agent_id'])){
                $old = Db::name('agent')->where('agent_id',$post['agent_id'])->find();
                if ($old['agent_psw']==$post['agent_psw']){
                    $psw = $old['agent_psw'];
                }else{
                    $psw = md5($post['agent_psw']);
                }

                $update = [
                    'agent_name'=>$post['agent_name'],
                    'agent_username'=>$post['agent_username'],
                    'agent_psw'=>$psw,
                    'agent_link'=>$post['agent_link'],
                    'agent_phone'=>$post['agent_phone'],
                    'agent_area'=>$post['city-picker'],
                    'agent_addr'=>$post['agent_addr'],
                    'agent_status'=>$post['status'],
                    'agent_lng'=>$post['lng'],
                    'agent_lat'=>$post['lat'],
                    'agent_studio_id'=>$post['agent_studio_id'],
                    'sort'=>$post['sort'],
                ];
                if (Db::name('agent')->where('agent_id',$post['agent_id'])->update($update)){
                    $this->success('更新成功');
                }else{
                    $this->error('数据更新失败');
                }
            }

            if (Db::name('agent')->where('agent_username',$post['agent_username'])->whereOr('agent_studio_id',$post['agent_studio_id'])->find()){
                $this->error('已存在该账号');
            }
            $data = [
                'agent_name'=>$post['agent_name'],
                'agent_username'=>$post['agent_username'],
                'agent_psw'=>md5($post['agent_psw']),
                'agent_link'=>$post['agent_link'],
                'agent_phone'=>$post['agent_phone'],
                'agent_area'=>$post['city-picker'],
                'agent_addr'=>$post['agent_addr'],
                'agent_status'=>$post['status'],
                'agent_lng'=>$post['lng'],
                'agent_lat'=>$post['lat'],
                'agent_studio_id'=>$post['agent_studio_id'],
                'sort'=>$post['sort'],
            ];
            if (Db::name('agent')->insert($data)){
                $this->success('添加成功');
            }else{
                $this->error('数据插入失败');
            }
        }

    }
}