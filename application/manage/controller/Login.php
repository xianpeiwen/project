<?php
	namespace app\manage\controller;
	use think\Controller;
	use think\Session;
	use think\Db;

class Login extends Controller
{
	public function loginOut()
	{	
		Session::delete('admin');
		$this->redirect('manage/Login/index');
	}
	public function index()
	{
		if(Session::has('admin')){
			echo "<script type='text/javascript'>window.top.location.replace('/manage');</script>";
		}
		$adminTitle = '后台管理系统';
		$this->assign('adminTitle',$adminTitle);
		return $this->fetch();
	}
	public function login()
	{
		$userName = input('post.userName');
		$password = input('post.password');
		// $code = input('post.code');
		// if(!captcha_check($code)){
		//  	$this->error('验证码输入错误！');
		//  }
		if(strpos($userName,'@') !==false){
			$data['admin_email'] = $userName;
		}else{
		    $data['admin_phone'] = $userName;
		}

		$admin = Db::name('admin')->where($data)->find();

		if( empty($admin)){
			$this->error('您输入的帐号不存在或密码错误！');
		}
		if( $admin['admin_status'] == 2){
			$this->error('您的帐号已被禁用，请联系管理员！');
		}
		if (password_verify($password,$admin['admin_pass'])) { 
			$res['admin_id'] = $admin['admin_id'];
			$res['admin_name'] = $admin['admin_name'];
			$res['is_lock'] = 1;
			$res['pass'] = '';
		    Session::set('admin',$res);
		    $this->success('登录成功！');
		} else {  
		    $this->error('您输入的帐号不存在或密码错误！');
		}
	}
}