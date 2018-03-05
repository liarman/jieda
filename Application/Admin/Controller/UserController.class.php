<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
 * 后台首页控制器
 */
class UserController extends AdminBaseController{

	/**
	 * 用户列表
	 */
	public function index(){
		$word=I('get.word','');
		if (empty($word)) {
			$map=array();
		}else{
			$map=array(
				'username'=>$word
				);
		}
		$assign=D('Users')->getAdminPage($map,'register_time desc');
		$this->assign($assign);
		$this->display();
	}

	/**
	 * 修改面
	 */
	public function setPassword()
	{
		$id = I('post.id');
		$password = I('post.password', '');
		$password2 = I('post.password', '');
		if ($password != $password2) {
			$message['status'] = 0;
			$message['message'] = '两次密码输入不一样';
		} else {
			$user = D('Users')->where(array('id' => $id))->find();
			$password = md5($password);
			$result = D('Users')->where(array('id' => $id))->save(array('password' => $password));
			if ($result) {
				$message['status'] = 1;
				$message['message'] = '修改密码成功';
			} else {
				$message['status'] = 0;
				$message['message'] = '修改密码失败';
			}
		}

		$this->ajaxReturn($message, 'JSON');

	}
}
