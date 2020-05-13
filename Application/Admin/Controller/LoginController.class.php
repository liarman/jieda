<?php
namespace Admin\Controller;
use Common\Controller\BaseController;
use Think\Verify;
/**
 * 后台首页控制器
 */
class LoginController extends BaseController{
	/**
	 * 首页
	 */
	public function index(){

		if(IS_POST){
			// 做一个简单的登录 组合where数组条件
			$map=I('post.');
			/*$geetest['geetest_challenge']=$map['geetest_challenge'];
			$geetest['geetest_validate']=$map['geetest_validate'];
			$geetest['geetest_seccode']=$map['geetest_seccode'];*/
			$logindata['username']=$map['username'];
			$logindata['password']=$map['password'];

			$logindata['password']=md5($logindata['password']);
			$data=M('Users')->where($logindata)->find();
			if (empty($data)) {
				//$this->error('账号或密码错误');
				$message['status']=0;
				$message['message']='账号或密码错误';
				$this->ajaxReturn($message,'JSON');
			}else{
				M('Users')->where(array('id'=>$data['id']))->save(array('last_login_time'=>time(),'last_login_ip'=>get_client_ip()));
				$user=array(
					'id'=>$data['id'],
					'username'=>$data['username'],
					'avatar'=>$data['avatar']
				);
                setSession("user",$user);
				$loginrecord['userid']=$data['id'];
				$loginrecord['loginTime']=date('Y-m-d H:i:s');
				$loginrecord['loginip']=get_client_ip();
//				$loginrecord['area']=getCityFromIp($loginrecord['loginip']);
				M('LoginAdmin')->add($loginrecord);
				$message['status']=1;
				$message['message']='登录成功';
				$this->ajaxReturn($message,'JSON');
			//	$this->success('登录成功、前往管理后台',U('Admin/Index/index'));
			//	$this->redirect('Admin/Index/index');
			}
		}else{
			$data=check_login() ? $_SESSION['user']['data']['username'].'已登录' : '未登录';
			$assign=array(
				'data'=>$data
			);
			$this->assign($assign);
			$this->display();
		}
	}
	/**
	 * 退出
	 */
	public function logout(){
		session('user',null);
		$this->success('退出成功、前往登录页面',U('Admin/Login/index'));
	}


	/**
	 * geetest生成验证码
	 */
	public function geetest_show_verify(){
		$geetest_id=C('GEETEST_ID');
		$geetest_key=C('GEETEST_KEY');
		$geetest=new \Org\Xb\Geetest($geetest_id,$geetest_key);
		$user_id = "test";
		$status = $geetest->pre_process($user_id);
		$_SESSION['geetest']=array(
			'gtserver'=>$status,
			'user_id'=>$user_id
		);
		echo $geetest->get_response_str();
	}

	/**
	 * geetest submit 提交验证
	 */
	public function geetest_submit_check(){
		$data=I('post.');
		if (geetest_chcek_verify($data)) {
			echo '验证成功';
		}else{
			echo '验证失败';
		}
	}

	/**
	 * geetest ajax 验证
	 */
	public function geetest_ajax_check(){
		$data=I('post.');
		echo intval(geetest_chcek_verify($data));
	}
    /* 生成验证码 */
    public function verify()
    {
        $config = [
            'fontSize' => 16, // 验证码字体大小
            'length' => 4, // 验证码位数
            'imageH' => 37,
            'imageW' => 106,
            'useCurve' =>false,
            'useNoise'    =>    false
        ];
        $Verify = new Verify($config);
        $Verify->entry();
    }

    /* 验证码校验 */
    public function check_verify($code, $id = '')
    {
        $code=I('post.code');
        $verify = new \Think\Verify();
        $res = $verify->check($code, $id);
        $this->ajaxReturn($res, 'json');
    }
}
