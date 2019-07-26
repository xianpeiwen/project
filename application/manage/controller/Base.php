<?php
	namespace app\manage\controller;
	use think\Controller;
	use think\Db;
	use think\Session;
	use think\auth\Auth;
	class Base extends Controller
	{
		public function _initialize()
		{
			$request = \think\Request::instance();
			$auth = Auth::instance();

			if(!Session::has('admin')){
				echo "<script type='text/javascript'>window.top.location.replace('/manage/Login/index');</script>";
			}
			$img['image_surface_name'] = 'admin';
			$img['image_surface_id'] = Session::get('admin')['admin_id'];
	
			$adminImg['img'] = Db::name('image')->where($img)->field('image_path,image_name')->find();
			$adminImg['admin'] = Session::get('admin');
			$adminTitle = '后台管理系统';

//			if( strtolower($request->module()) == 'manage' && strtolower($request->controller()) == 'index' && strtolower($request->action()) == 'index'){
//
//			}else{
//				if( strtolower($request->module()) == 'manage' && strtolower($request->controller()) == 'index' && strtolower($request->action()) == 'nojurisdiction'){
//
//				}else{
//
//					$url = $request->module().'/'.$request->controller().'/'.$request->action();
//			    	$uid = Session::get('admin')['admin_id'];
//			    	if(!$auth->check($url,$uid)){// 第一个参数是规则名称,第二个参数是用户UID
//			    		//没有显示操作按钮的权限
//					 	if ($request->isAjax()){
//
//							if ($request->isPost()){
//
//								$data['code'] = 1000;
//						        $data['msg'] = '您没有权限！--'.$url;
//						        $data['count'] = 0;
//						        $data['data'] = '';
//						        $data = json_encode($data);
//						        echo $data;
//						        exit;
//							}
//							if ($request->isGet()){
//								$data['code'] = 1;
//						        $data['msg'] = '您没有权限！--'.$url;
//						        $data['count'] = 0;
//						        $data['data'] = '';
//						        $data = json_encode($data);
//						        echo $data;
//						        exit;
//							}
//						}else{
//							$module = $request->module();
//							$controller = $request->controller();
//							$action = $request->action();
//							$this->redirect('manage/Index/NoJurisdiction',['type'=>1,'module'=>$module,'controller'=>$controller,'action'=>$action]);
//							exit;
//						}
//					}
//				}
//			}
			$this->assign('adminTitle',$adminTitle);
			$this->assign('adminImg',$adminImg);
		}

		//更改状态公用
	    public function modular_status()
	    {

	        $id = input('post.id');
	        $idname = input('post.idname');
	        $status = input('post.status');
	        $title = input('post.title');
	        $zname = input('post.zname');
	        $bool = Db::name($title)->where($idname,$id)->setField($zname, $status);
	        if( $bool ){
	            $this->success('更新成功');
	        }else{
	            $this->error('更新失败');
	        }
	    }
	    public function is_jurisdiction($url)
	    {
	    	$auth = Auth::instance();
	    	$uid = Session::get('admin')['admin_id'];	
	    	if($auth->check($url,$uid)){// 第一个参数是规则名称,第二个参数是用户UID
			    return 1;
			}else{
			    return 2;
			}
	    }
	}