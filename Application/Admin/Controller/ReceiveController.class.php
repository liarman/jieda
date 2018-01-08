<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
 * 后台权限管理
 */
class ReceiveController extends AdminBaseController{
    public function ajaxReceiveList(){
        /*条件查询*/
        $receivername=I("post.receivername");
        $receivertel=I("post.receivertel");
        $shipperid=I("get.shipperid");

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
        $offset = ($page-1)*$rows;
        $countsql ="select count(id) AS total from qfant_receive s where 1=1";
        $sql ="select * from qfant_receive s where 1=1";

        $param=array();
        if(!empty($receivername)){
            $countsql.=" and s.receivername like '%s'";
            $sql.=" and s.receivername like '%s'";
            array_push($param,'%'.$receivername.'%');
        }
        if(!empty($receivertel)){
            $countsql.=" and s.receivertel like '%s'";
            $sql.=" and s.receivertel like '%s'";
            array_push($param,'%'.$receivertel.'%');
        }
        if(!empty($shipperid)){
            $countsql.=" and s.shipperid =%d";
            $sql.=" and s.shipperid =%d";
            array_push($param,$shipperid);
        }
        array_push($param,$offset);
        array_push($param,$rows);
        $sql.="  order by s.id desc  limit %d,%d";
        $data=D('Receive')->query($countsql,$param);
        $result['total']=$data[0]['total'];
        $data=D('Receive')->query($sql,$param);
        $result["rows"] = $data;
        $this->ajaxReturn($result,'JSON');

    }
    /**
     * 添加
     */
    public function add(){
        if(IS_POST){
            $data=I('post.');
            unset($data['id']);
            $result=D('Receive')->addData($data);
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
     * 编辑
     */
    public function edit(){
        if(IS_POST){
            $data=I('post.');
            $where['id']=$data['id'];
            $result=D('Receive')->editData($where,$data);
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


    /**
     * 删除
     */
    public function delete(){
        $id=I('get.id');
        $map=array(
            'id'=>$id
        );
        $result=D('Receive')->deleteData($map);
        if($result){
            $message['status']=1;
            $message['message']='删除成功';
        }else {
            $message['status']=0;
            $message['message']='删除失败';
        }
        $this->ajaxReturn($message,'JSON');
    }

    public function addReceiveShipper(){
        $data['id']=I('get.id');//收货人id
        $data['shipperid']=I("get.shipperid");//发货人id
        $where['id']=$data['id'];
        $id=$data['id'];
        $da=D('Receive')->where(array('id'=>$id))->find();
        if($da['shipperid']!=null){
            $message['status']=2;
            $message['message']='该收货人已经与发货人建立关系！请重新选择！';
        }else{
            $result=D('Receive')->editData($where,$data);
            if($result){
                $message['status']=1;
                $message['message']='成功';
            }else {
                $message['status']=0;
                $message['message']='失败';
            }
        }
        $this->ajaxReturn($message,'JSON');
    }

    public function orderList(){
        $cardriveid=I('get.id');//发车id
        $sql ="select o.id as oid,o.orderno ,o.shipper,o.shippertel,o.receivername,o.receiveraddress,o.receivertel,r.name as rname from qfant_order as o ,qfant_cardrive as cd ,qfant_route as r where o.cardriveid=cd.id and o.status=1 and r.id=o.endcity and cd.id='$cardriveid'";
        $data=D('Order')->query($sql,"");
        $this->ajaxReturn($data,'JSON');

    }
}
