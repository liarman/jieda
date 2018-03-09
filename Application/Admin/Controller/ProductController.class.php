<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
 * 后台门店管理控制器
 */
class ProductController extends AdminBaseController{
	public function ajaxProductList(){
		$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
		$rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
		$offset = ($page-1)*$rows;
		$name = I("post.name");
		$catid=I('get.catId');
		//分类管理 查询商品
		if($catid){
			$countsql="select count(p.id) AS total from qfant_product p ,qfant_category c where 1=1 AND c.id=p.category_id and c.id='$catid'";
			$sql="select p.*, c.name AS categoryname,c.imgurl,c.sort,c.status AS categorystatus  from qfant_product p ,qfant_category c where 1=1 AND c.id= p.category_id and c.id='$catid'";
		}else{
			$countsql="select count(p.id) AS total from qfant_product p ,qfant_category c where 1=1 AND c.id=p.category_id";
			$sql="select p.*, c.name AS categoryname,c.imgurl,c.sort,c.status AS categorystatus  from qfant_product p ,qfant_category c where 1=1 AND c.id= p.category_id";
		}
        $param=array();

		if(!empty($name)){
			$countsql.=" and p.name like '%s'";
			$sql.=" and p.name like '%s'";
			array_push($param,$name);
		}

        array_push($param,$offset);
        array_push($param,$rows);
        $sql.=" limit %d,%d";
        $data=D('Product')->query($countsql,$param);
        $result['total']=$data[0]['total'];
        $data=D('Product')->query($sql,$param);
        $result["rows"] = $data;
		$this->ajaxReturn($result,'JSON');
	}


    public function ajaxCategoryAll(){
        $data=D('Category')->select();
        $this->ajaxReturn($data,'JSON');
    }

    public function changestatus(){
        $id=$_GET['id'];
        if($_GET['status'] == 0){
            $status= 1 ;
        }elseif($_GET['status'] == 1){
            $status= 0 ;
        }
        $result = D('Product')->where(array('id'=>$id))->setField('status',$status);
        if($result){
            $message['status']=1;
            $message['message']='修改成功';
        }else {
            $message['status']=0;
            $message['message']='修改失败';
        }
        $this->ajaxReturn($message,'JSON');
    }
	/**
	 * 添加
	 */
	public function add(){
		if(IS_POST){
			$data['name']=I('post.name');
			$data['pic1']=I('post.pic1');
			$data['pic2']=I('post.pic2');
			$data['pic3']=I('post.pic3');
			$data['category_id']=I('post.category_id');
			$data['price']=I('post.price');
			$data['marketprice']=I('post.marketprice');
			$data['storage']=I('post.storage');
			$data['sort']=I('post.sort');
			$data['particular']=I('post.particular');
			$data['recommend']=I('post.recommend');
			$data['intro']=I('post.intro','',false);
            $data['createtime']=date("Y-m-d H:i:s" ,time());
			unset($data['id']);
			$result=D('Product')->addData($data);
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
		$result=D('Product')->deleteData($map);
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
            $data['pic1']=I('post.pic1');
            $data['pic2']=I('post.pic2');
            $data['pic3']=I('post.pic3');
            $data['category_id']=I('post.category_id');
            $data['price']=I('post.price');
            $data['marketprice']=I('post.marketprice');
            $data['storage']=I('post.storage');
            $data['sort']=I('post.sort');
            $data['particular']=I('post.particular');
            $data['recommend']=I('post.recommend');
			$data['intro']=I('post.intro','',false);
			$where['id']=$data['id'];
			//print_r($data);die;
			$result=D('Product')->editData($where,$data);
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
}
