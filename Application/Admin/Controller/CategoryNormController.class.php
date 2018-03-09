<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
 * 后台门店管理控制器
 */
class CategoryNormController extends AdminBaseController{
	public function cateNormByCatId(){
		$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
		$rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
		$offset = ($page-1)*$rows;
		$data['catid']=I('get.catId');
		$data['type']=I('get.type');
		$type=$data['type'];
		$catid=$data['catid'];
		$countsql="select  count(cn.id) as total from qfant_category_norm cn where  cn.catid='$catid' and cn.type='$type'";
		$sql="select  cn.*,c.name as name from qfant_category c  LEFT JOIN qfant_category_norm  cn on cn.catid = c.id where c.id='$catid' and cn.type='$type' ";
		$param=array();
		$sql.=" limit %d,%d";
		array_push($param,$offset);
		array_push($param,$rows);
		$data=D('CategoryNorm')->query($countsql,$param);
		$result["total"]=$data[0]["total"];
		$data=D('CategoryNorm')->query($sql,$param);
		$result["rows"] = $data;
		$this->ajaxReturn($result,'JSON');
	}
	public function categoryLevel(){
		$pid=I('get.pid');
		$data=D('Category')->field('id,name')->where(array('pid'=>$pid))->select();
//		foreach ($data as $key => $value){
//			$children=D('Category')->field('id,name as text')->where(array('pid'=>$value['id']))->select();
//			$data[$key]['children']=$children;
//			$data[$key]['text']=$value['name'];
//		}
		$this->ajaxReturn($data,'JSON');
	}


	/**
	 * 添加
	 */
	public function add(){
		if(IS_POST){
			$data['type']=I('post.type');
			$data['catid']=I('post.catid');
			$data['value']=I('post.value');
			unset($data['id']);
			$result=D('CategoryNorm')->addData($data);
			if($result){
				$message['status']=1;
				$message['message']='保存成功';
			}else {
				$message['status']=0;
				$message['message']='保存失败';
			}
		}else {
			$message['status']=0;
			$message['message']='保存失败';
		}
		$this->ajaxReturn($message,'JSON');
	}
	/**
	 * 删除
	 */
	public function delete(){
		$id=I('get.id');
		$map=array(
			'id'=>$id
		);
		$result=D('CategoryNorm')->deleteData($map);
		if($result){
			$message['status']=1;
			$message['message']='删除成功';
		}else {
			$message['status']=0;
			$message['message']='删除失败';
		}
		$this->ajaxReturn($message,'JSON');
	}
	/**
	 * 添加
	 */
	public function edit(){
		if(IS_POST){
			/*$data['type']=I('post.type');
			$data['catid']=I('post.catid');*/
			$data['value']=I('post.value');
			$data['id']=I('post.id');
			$where['id']=$data['id'];
			$result=D('CategoryNorm')->editData($where,$data);
			if($result){
				$message['status']=1;
				$message['message']='保存成功';
			}else {
				$message['status']=0;
				$message['message']='保存失败';
			}
		}else {
			$message['status']=0;
			$message['message']='保存失败';
		}
		$this->ajaxReturn($message,'JSON');
	}
	public  function ajaxchildList(){
		$id=I('get.id');
		$data=D('Category')->where(array('pid'=>$id))->select();
		$this->ajaxReturn($data,'JSON');
	}



}
