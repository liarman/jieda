<?php
namespace Common\Controller;
use Common\Controller\BaseController;
class WapController extends BaseController{
	public $token;
	public $wecha_id;
	public $siteUrl;
	public $fans;
	public $homeInfo;
	public $bottomeMenus;
	public $wxuser;
	public $user;
	public $group;
	public $company;
	public $shareScript;
	public $sign;

	private $_appid ; //应用id
	private $_secret ; //应用密钥
	private $_redirect_uri ; //跳转地址

	public function _initialize(){
		parent::_initialize();

		//应用唯一标识
		$this->_appid = "wx3f0fc84227a77e28" ;
		
		//应用密钥AppSecret，在微信开放平台提交应用审核通过后获得
		$this->_secret ="wx0bfc0c6a41517f06";
		$this->token="adoemv1422964222"; //gh_04bf4f0d8ab1
		$this->siteUrl=C('GLOBAL_DOMAIN_URL');
		$session_openid_name='token_openid_'.$this->token;
		$session_fakeopenid_name='token_fakeopenid_'.$this->token;
		$session_reopenid_name='token_reopenid_'.$this->token;
		$session_oauthed_name='token_oauthed_'.$this->token;		
		if (!isset($_SESSION[$session_openid_name])||!$_SESSION[$session_openid_name]){
			
			$scope='snsapi_base';
			/*if (!$this->wxuser['oauthinfo']||!$getUserinfo){
				$scope='snsapi_base';
			}*/

			if ( (!$_GET['wecha_id'] || urldecode($_GET['wecha_id']) == '{wechat_id}') && $_GET['wecha_id'] != 'no' && !isset($_GET['code'])){
				$customeUrl=$this->siteUrl.$_SERVER['REQUEST_URI'];
				
				$oauthUrl='https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$this->_appid.'&redirect_uri='.urlencode($customeUrl).'&response_type=code&scope='.$scope.'&state=oauth#wechat_redirect';

				header('Location:'.$oauthUrl);
				exit();
			}

			if (isset($_GET['code']) && isset($_GET['state']) && ($_GET['state']=='oauth')){
				$rt=$this->curlGet('https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->_appid.'&secret='.$this->_secret.'&code='.$_GET['code'].'&grant_type=authorization_code');
				
				$jsonrt=json_decode($rt,1);
				$openid= isset( $jsonrt['openid'] ) ? $jsonrt['openid'] : "";
				$access_token = isset( $jsonrt['access_token'] ) ? $jsonrt['access_token'] : "" ;
				//$_GET['wecha_id']=$openid;
				$this->wecha_id=$openid;
				//print_r($jsonrt);die;
				if (!$openid){
					$this->error('授权不对<br>'.$this->_appid.'<br>'.$this->_secret.'<br>'.$jsonrt['errcode'],'#');
					die;
				} else {
					
					//if ($scope=='snsapi_userinfo'){
						$uinfo = $this->curlGet('https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN') ;
						$jsonui = json_decode($uinfo, 1) ;
						if( isset( $jsonui['openid'] ) && $jsonui['openid'] ) {
							//insert the message to userinfo of table
							//update or add
							$fansInfo = M('Userinfo')->where( array( "token" => $this->token , "wecha_id" => $this->wecha_id ) )->getField("id") ;

							$datainfo['wechaname'] = str_replace(array("'","\\"),array(''),$jsonui['nickname']);
							$datainfo['sex']       = $jsonui['sex'];
							$datainfo['portrait']  = $jsonui['headimgurl'];
							$datainfo['token']     = $this->token ;
							$datainfo['wecha_id']  = $jsonui['openid'] ;
							$datainfo['city']      = $jsonui['city'] ;
							$datainfo['province']  = $jsonui['province'];
							if ($fake){
								$datainfo['wecha_id']  = $this->wecha_id ;
								$datainfo['fakeopenid']  = $jsonui['openid'] ;
							}

							if( $fansInfo ) {
								$datainfo['truename']  = $datainfo['wechaname'] ;
								//update
								M('Userinfo')->where( array( "id" => $exist ) )->save($datainfo) ;
							} else {
								$datainfo['truename']  = $datainfo['wechaname'] ;
								//add
								M('Userinfo')->add($datainfo) ;
							}
						} 
						/*else {
							$this->error('授权不对啊<br>'.$this->_appid.'<br>'.$this->_secret.'<br>'.$jsonui['errcode'],'#');
							die;
						}*/
					//}
				}
				$_SESSION[$session_openid_name]=$this->wecha_id;
				$_SESSION[$session_oauthed_name]=1;
			}else {
				
			}
			
		}else {
			
			$this->wecha_id=$_SESSION[$session_openid_name];
		}
		//print_r($this->wecha_id);die;
		if($this->wecha_id&&!preg_match("/^[0-9a-zA-Z_\-\s]{3,82}$/",$this->wecha_id)){
			exit('error openid:'.$this->wecha_id);
		}
		if (!$this->wecha_id){
			$this->wecha_id=$this->_get('wecha_id');
		}
		
		$this->assign('wecha_id',$this->wecha_id);
	}

	function curlGet($url){
		$ch = curl_init();
		$header = "Accept-Charset: utf-8";
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$temp = curl_exec($ch);
		return $temp;
	}


	private function redirect_uri() {
		return urlencode($this->_redirect_uri) ;
	}


/**
 * 判断粉丝是否关注信息
 * @ return { FALSE：没有关注 / TRUE:已关注 }
 */
	protected function isSubscribe()
	{

		$wecha_id = $this->wecha_id;

		if ($this->_appid == '' || $this->_secret == '') {
			//未填写appid 或 appsecret
			if($this->wxuser['winxintype'] == 1 || $this->wxuser['winxintype'] == 2){
				//非认证服务号 非认证订阅号
				if ($wecha_id) {
					return TRUE;
				}else{
					return FALSE;
				}
			}else{
				return FALSE;
			}

		}else{

			if($this->wxuser['winxintype'] == 3 || $this->wxuser['winxintype'] == 4){
				
				//认证服务号
				$url_get='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->_appid.'&secret='.$this->_secret;
				$json=json_decode($this->curlGet($url_get));

				$url='https://api.weixin.qq.com/cgi-bin/user/info?openid='.$wecha_id.'&access_token='.$json->access_token;
				$classData=json_decode($this->curlGet($url));

				if ($classData->subscribe == 0) {
					//没有关注
					return FALSE;
				}else{
					return TRUE;
				}

			}elseif($this->wxuser['winxintype'] == 1 || $this->wxuser['winxintype'] == 2){
					//非认证服务号 非认证订阅号
					if ($wecha_id) {
						return TRUE;
					}else{
						return FALSE;
					}

			}else{
				//其他情况返回FALSE
				return FALSE;
			}
		}

	}


}