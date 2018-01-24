<?php
namespace App\Controller;
use Common\Controller\WapController;
/**
 * 认证控制器
 */
class BindController extends WapController{
    public function _initialize() {
		parent::_initialize();
        $this->assign('staticFilePath',str_replace('./','/',StaticFilePath.'Static'));
    }
    public  function bind(){
        $bindingModel = D("Binding");
        $bindkeyModel = D("Bindkey");
		
        if(IS_POST){
            $key=I('post.key');
            $wecha_id=I('post.wecha_id');
            $bindk=$bindkeyModel->where(array('key'=>$key,'status'=>1))->find();
			if($wecha_id){
				 if(empty($bindk)){
                $data['status'] = 2;
                $data['message'] = "您输入的绑定秘钥不存在";
                $this->ajaxReturn($data,'JSON');
				}else {
					$binduser=$bindingModel->where(array('wecha_id'=> $wecha_id))->find();
					if($binduser){
						$data['status'] = 5;
						$data['message'] = "您已经绑定，不需要再次绑定";
						$this->ajaxReturn($data,'JSON');
					}else {
						$bindkeyModel->where(array('key'=>$key))->setField(array('status'=>0));
						$user['wecha_id']=$wecha_id;
						$user['createtime']=time();
						$user['bkey_id']=$bindk['id'];
						$bindingModel->add($user);
						$data['status'] = 1;
						$data['message'] = "绑定成功";
						$this->ajaxReturn($data,'JSON');

					}
				}

			}else {
				$data['status'] = 3;
                $data['message'] = "绑定信息错误";
                $this->ajaxReturn($data,'JSON');
			}
           
        }else {
          // $wecha_id=$this->wecha_id;
		//	$res=D("Customer")->where(array('wecha_id'=>$wecha_id))->find();
       //    if($res){//已注册跳到运单列表
		//		$this->redirect('App/Bind/myOrder', array('wecha_id' =>$wecha_id));
		//		$this->assign('wecha_id',$wecha_id);
		//		$this->display();
		//	}else {//未注册跳到注册页面
			//	$this->assign('wecha_id', $wecha_id);
				$this->display();
		//	}
        }
    }

	public function register(){
		if(IS_POST) {
			$user['wecha_id'] = I("post.wecha_id", '');
			$user['phone'] = I("post.phone", '');
			$user['username'] = I("post.username", '');
			if ($user['wecha_id'] && $user['phone'] && $user['username']) {
				$user['regtime'] = time();
				$result = D("Customer")->add($user);
				if ($result) {
					$data['status'] = 1;
					$data['message'] = '注册成功';
				} else {
					$data['status'] = 0;
					$data['message'] = '注册失败';
				}
			} else {
				$data['status'] = 0;
				$data['message'] = '注册失败';
			}
		}else {
			$data['status'] = 0;
			$data['message'] = '注册失败';
		}
		$this->ajaxReturn($data,'JSON');
	}

	public function sendAdd(){
		if(IS_POST){
			$data['wecha_id']=I("post.wecha_id");
			$data['receivername']=I("post.receivername");
			$data['receivertel']=I("post.receivertel");
			$data['receiveraddress']=I("post.receiveraddress");
			$data['createdate']=time();
			$data['assembledate']="";
			unset($data['id']);
			$res=D("Order")->add($data);//写入订单表
			D("Receive")->add($data);//写入收货人表
		//	$res= M('Order')->field('id')->where($data)->find();
			$id=$res;
			$where['id']=$res;
			$data['orderno']=$this->OrdernoMethod($id,"J");
			$result=D('Order')->editData($where,$data);
				if ($result) {
					$data['status'] = 1;
					$data['message'] = '成功';
				} else {
					$data['status'] = 0;
					$data['message'] = '失败';
				}
			}else{
				$data['status'] = 0;
				$data['message'] = '失败';
			}
		$this->ajaxReturn($data,'JSON');

	}

	public function myInfoEdit(){
		if(IS_POST){
			$data=I('post.');
			$where['id']=$data['id'];
			$result=D('Customer')->editData($where,$data);
			if($result){
				$message['status']=1;
				$message['message']='保存成功';
			}else {
				$message['status']=0;
				$message['message']='保存失败';
			}
		}
		$this->ajaxReturn($message,'JSON');
	}
	public  function myInfo(){
	$d['wecha_id']=I("get.wecha_id");
	$wecha_id=$d['wecha_id'];
	if($d) {
		$customer = M('Customer')->where($d)->find();
		$this->assign("wecha_id",$wecha_id);
		$this->assign("customer",$customer);
		$this->display('myInfo');
	}
}

	public function myOrder(){
		$wecha_id=I("get.wecha_id",'');
		$this->assign("wecha_id",$wecha_id);
		$this->display();
	}
	public function ajaxMyOrder(){
		$d=I("get.wecha_id");
		$pageNo = I("get.pageNo");
		if($pageNo==0){
			$pageNo =1;
		}
		$rows = 10;
		$offset = ($pageNo-1)*$rows;
		$sql="select * from qfant_order where wecha_id='$d'";
		$param=array();
		array_push($param,$offset);
		array_push($param,$rows);
		$sql.=" order by id desc limit %d,%d";
		$data=D('Order')->query($sql,$param);
		//$data=D('Order')->where(array('wecha_id'=>$d))->limit($offset.','.$rows)->select();
		foreach ($data as $key=>$basevalue){
			if($basevalue['status']=='0'){
				$data[$key]['status']='已提交订单';
			}else if($basevalue['status']=='1'){
				$data[$key]['status']='已装车';
			}else{
				$data[$key]['status']='已到站';
			}
		}
		$this->ajaxReturn($data,'JSON');
	}
	public  function mySend(){
		$wecha_id=I("get.wecha_id",'');
		$this->assign("wecha_id",$wecha_id);
		$this->display('mySend');
	}
	public  function driveRoute(){
		$cardriveid=I("get.cardriveid");
		$wecha_id=I("get.wecha_id");
		if($cardriveid){
			$sql ="SELECT r.name,c3.arrivedate FROM qfant_route AS r,qfant_car AS c1,qfant_cardrive AS c2,qfant_cardriveroute AS c3 WHERE c3.cardriveid = '$cardriveid' AND c3.cardriveid = c2.id AND c3.routeid = r.id AND c2.carid = c1.id ;";
			$data=D('Cardriveroute')->query($sql,"");
			foreach ($data as $key=>$basevalue){
				$data[$key]['arrivedate']=date('Y-m-d H:i' , $basevalue['arrivedate']) ;
			}
			$this->assign("driveRoute",$data);
			$this->assign("wecha_id",$wecha_id);
			$this->display('driveRoute');
		}
	}

	public function OrdernoMethod($id,$type){
		$time=date('YmdHis');
		$str=strval($id);
		for($i = 0; $i <strlen($str); $i++)
		{
			$time=$time."0";
		}
		$code=$type.$time.$id;
		return $code;

	}
	//删除订单
	public function deleteOrder(){
		$id=I("post.id");
		if($id){
			$data=D('Order')->field('status')->where(array('id'=>$id))->find();
			if($data['status']=='1'){
				$message['status']=2;
				$message['message']='该订单已装车，请联系快递公司进行删除！';
			}else{
				$map=array(
					'id'=>$id
				);
				$result=D('Order')->deleteData($map);
				if($result){
					$message['status']=1;
					$message['message']='删除成功！';
				}else{
					$message['status']=0;
					$message['message']='删除失败！';
				}
			}
		}else{
			$message['status']=0;
			$message['message']='删除失败！';
		}
		$this->ajaxReturn($message,'JSON');
	}

	public  function EditMyOrder(){
		$orderno=I("get.id");
		if($orderno) {
			$order = D('Order')->where(array('id' => $orderno))->find();
			/*if($order['status']=='1'){
				$message['status']=1;
				$message['message']='该订单已装车，不可修改';
				$this->ajaxReturn($message,'JSON');
			}else if($order['status']=='2'){
				$message['status']=2;
				$message['message']='该订单已到站，不可修改';
				$this->ajaxReturn($message,'JSON');
			}else{*/
				$this->assign("order",$order);
				$this->display('EditMyOrder');
			/*}*/

		}
	}
	public  function orderSubmit(){
		$id=I("post.id");
		$data=I("post.");
		if($id){
			$order = D('Order')->where(array('id' => $id))->find();
			if($order['status']=='1'){
				$message['status']=1;
				$message['message']='该订单已装车，不可修改';
			}else if($order['status']=='2'){
				$message['status']=2;
				$message['message']='该订单已到站，不可修改';
			}else if($order['status']=='0'){
				$where['id']=$id;
				$result=D('Order')->editData($where,$data);
				if($result){
					$message['status']=3;
					$message['message']='保存成功';
				}else{
					$message['status']=4;
					$message['message']='保存失败';
				}
			}
		}else{
			$message['status']=4;
			$message['message']='保存失败';
		}
		$this->ajaxReturn($message,'JSON');
	}

	public function  myInfoDelete(){
		$id=I("post.wecha_id");
		if($id){
			$data=D('Customer')->where(array('wecha_id'=>$id))->find();
			$map=array(
				'id'=>$data['id']
			);
			$result=D('Customer')->deleteData($map);
			if($result){
				$message['status']=1;
				$message['message']='解除绑定成功！';
			}else{
				$message['status']=0;
				$message['message']='解除绑定失败！';
			}
		}else{
			$message['status']=0;
			$message['message']='解除绑定失败！';
		}
	$this->ajaxReturn($message,'JSON');
	}

	public  function  mySelectOrder(){
		$pageNo = I("get.pageNo");
		if($pageNo==0){
			$pageNo =1;
		}
		$rows = 10;
		$offset = ($pageNo-1)*$rows;
		$receivername=I("get.receivername");
		$receivertel=I("get.receivertel");
		$shipper=I("get.shipper");
		$shippertel=I("get.shippertel");
		$sql="SELECT	o.* ,c.driver as driver ,r.name as endcityname ,cd.number as number,cd.startdate as startdate FROM	qfant_order o left join qfant_cardrive cd on o.cardriveid=cd.id	LEFT JOIN qfant_route r on r.id=o.endcity LEFT JOIN qfant_car  c on c.id=cd.carid where 1=1";
		$param=array();
		if(!empty($receivername)){
			$sql.=" and o.receivername like '%s'";
			array_push($param,'%'.$receivername.'%');
		}
		if(!empty($receivertel)){
			$sql.=" and o.receivertel like '%s'";
			array_push($param,'%'.$receivertel.'%');
		}
		if(!empty($shipper)){
			$sql.=" and o.shipper like '%s'";
			array_push($param,'%'.$shipper.'%');
		}
		if(!empty($shippertel)){
			$sql.=" and o.shippertel like '%s'";
			array_push($param,'%'.$shippertel.'%');
		}
		$sql.=" order by o.createdate desc,o.id desc  limit %d,%d ";
		array_push($param,$offset);
		array_push($param,$rows);
		$data=D('Order')->query($sql,$param);
		foreach ($data as $key=>$basevalue){
			if($basevalue['status']=='0'){
				$data[$key]['status']='已提交订单';
			}else if($basevalue['status']=='1'){
				$data[$key]['status']='已装车';
			}else{
				$data[$key]['status']='已到站';
			}
		}
		$data['receivername']=$receivername;$data['receivertel']=$receivertel;$data['shipper']=$shipper;$data['shippertel']=$shippertel;
		$this->assign("order",$data);
		$this->display();

	}
}
