<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
 * 后台门店管理控制器
 */
class CategoryController extends AdminBaseController{
	public function ajaxCategoryList(){
		$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
		$rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
		$offset = ($page-1)*$rows;
		$result["total"]=D('Category')->where(array('pid'=>0))->count();

		$data=D('Category')->where(array('pid'=>0))->limit($offset.','.$rows)->select();

		foreach ($data as $key => $value){
			$children=D('Category')->where(array('pid'=>$value['id']))->select();
			$data[$key]['children']=$children;
		}
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
			$data['name']=I('post.name');
			$data['imgurl']=I('post.imgurl');
			$data['sort']=I('post.sort');
			$data['pid']=I('post.pid');
			$data['recommend']=I('post.recommend');
			$data['norm']=I('post.norm');
			$data['color']=I('post.color');
			unset($data['id']);
			$result=D('Category')->addData($data);
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
		$result=D('Category')->deleteData($map);
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
			$data['id']=I('post.id');
			$data['name']=I('post.name');
			$data['imgurl']=I('post.imgurl');
			$data['sort']=I('post.sort');
			$data['pid']=I('post.pid');
			$data['recommend']=I('post.recommend');
			$data['norm']=I('post.norm');
			$data['color']=I('post.color');
			$where['id']=$data['id'];
			$result=D('Category')->editData($where,$data);
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
