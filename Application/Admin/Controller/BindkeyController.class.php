<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
 * 后台门店管理控制器
 */
class BindkeyController extends AdminBaseController{
	public function ajaxList(){

		$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
		$rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
		$key=I("post.key");
		$offset = ($page-1)*$rows;
		$countsql="select count(n.id) AS total from  qfant_bindkey n where 1=1 ";
		$sql="select * from qfant_bindkey n  where 1=1";
		$param=array();
		if(!empty($key)){
			array_push($param,'%'.$key.'%');
			$countsql.=" and n.key like '%s'";
			$sql.=" and n.key like '%s'";
		}
		$sql.=" limit %d,%d";
		array_push($param,$offset);
		array_push($param,$rows);
		$data=D('Bindkey')->query($countsql,$param);
		$result['total']=$data[0]['total'];
		$data=D('Bindkey')->query($sql,$param);
		$result["rows"] = $data;
        $this->ajaxReturn($result,'JSON');
	}

	public function delete()
	{
		$id=I('get.id');
		$bindingModel = M("Binding");
		$buser=$bindingModel->where(array('bkey_id'=>$id))->find();
		if($buser){
			$message['status']=0;
			$message['message']='此秘钥和用户已经绑定，无法删除。';

			/*$this->error('此秘钥和用户已经绑定，无法删除。',U('BindKey/index'));*/
		}else {
			$map=array(
				'id'=>$id
			);
			$result=M('Bindkey')->where($map)->delete();
			if($result){
				$message['status']=1;
				$message['message']='删除成功';
			}else {
				$message['status']=0;
				$message['message']='删除失败';
			}
			$this->ajaxReturn($message,'JSON');
		}
		/*$bindKeyModel = M("Bindkey");
		$bindingModel = M("Binding");
		$id=$this->_get('id');
		$buser=$bindingModel->where(array('bkey_id'=>$id))->find();
		if($buser){
			$this->error('此秘钥和用户已经绑定，无法删除。',U('BindKey/index'));
		}else {
			$where = array('id'=>$id);
			$result=$bindKeyModel->where($where)->delete();
			if($result) {
				$this->success('操作成功',U('BindKey/index'));
			}else{
				$this->error('操作失败',U('BindKey/index'));
			}
		}*/
	}
	public function add(){
		$bindKeyModel = M("Bindkey");
		for ($i=1; $i<=50; $i++)
		{
			$bindKeyModel->create(); // 创建数据对象
			$data['key']=$this->generateKeyNum();
			$data['status']=1;
			$bindKeyModel->add($data); // 写入数据到数据库
		}
		$data['status'] = 1;
		$this->ajaxReturn($data,'JSON');
	}
	public function generateKeyNum(){
		$CONST_CODE = array('0','1','2','3','4','5','6','7','8','9');
		$code = ''; // 这里可以加上前缀
		for ($i=1; $i<=6; $i++)
		{
			$code=$code.$CONST_CODE[rand(0,9)];
		}
		return $code;
	}

	public function ajaxUsersList(){
//		$bindModel = M("Binding");
//		$bindKeyModel = M("Bindkey");
//		$where = array();
//		if (IS_POST) {
//			$key = $this->_post('key');
//			if (empty($key)) {
//				$this->error("关键词不能为空");
//			}
//			$map['key'] = array('like', '%'.$key.'%');
//			$count = $bindModel->where($map)->count();
//			$Page = new Page($count, 10);
//			$show = $Page->show();
//			$list = $bindModel->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
//		} else {
//			$count = $bindModel->where($where)->count();
//			$Page = new Page($count, 10);
//			$show = $Page->show();
//			$list = $bindModel->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
//		}
//		foreach($list as $k=>$v){
//			if($list[$k]['bkey_id']>0){
//				$key=$bindKeyModel->where(array('id'=>$list[$k]['bkey_id']))->find();
//				$list[$k]['key']=$key['key'];
//			}
//		}
//		$this->assign('page', $show);
//		$this->assign('list', $list);
//		$this->display();
		$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
		$rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
		$name=I("post.name");
		$bindKeyModel = M("Bindkey");
		$offset = ($page-1)*$rows;
		$countsql="select count(n.id) AS total from  qfant_binding n where 1=1 ";
		$sql="select * from qfant_binding n  where 1=1";
		$param=array();
		if(!empty($name)){
			array_push($param,'%'.$name.'%');
			$countsql.=" and n.name like '%s'";
			$sql.=" and n.name like '%s'";
		}
		$sql.=" limit %d,%d";
		array_push($param,$offset);
		array_push($param,$rows);
		$data=D('Binding')->query($countsql,$param);
		$result['total']=$data[0]['total'];
		$data=D('Binding')->query($sql,$param);
		foreach($data as $k=>$v){
			if($data[$k]['bkey_id']>0){
				$key=$bindKeyModel->where(array('id'=>$data[$k]['bkey_id']))->find();
				$data[$k]['key']=$key['key'];
			}
		}
		$result["rows"] = $data;
		$this->ajaxReturn($result,'JSON');

	}
}
