<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
 * 后台权限管理
 */
class OrderController extends AdminBaseController{
    public function ajaxOrderList(){
        $goodsname=I("post.goodsname");
        $receivername=I("post.receivername");
        $shipper=I("post.shipper");
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
        $offset = ($page-1)*$rows;
        $countsql = "SELECT	 count(o.id) AS total FROM	qfant_order o WHERE	1 = 1 ";
      //  $sql = "SELECT	o.* ,c.driver as driver ,r.name as endcityname FROM	qfant_order o left join qfant_cardrive c on o.cardriveid=c.id	LEFT JOIN qfant_route r on r.id=o.endcity WHERE 1=1";
     //   $sql = "SELECT	o.* ,c.driver as driver ,r.name as endcityname ,cd.number as number,cd.startdate as startdate FROM	qfant_order o left join qfant_cardrive cd on o.cardriveid=cd.id	LEFT JOIN qfant_route r on r.id=o.endcity LEFT JOIN qfant_car  c on c.id=cd.carid where 1=1";
      $sql="SELECT
            o.*,
            c.driver AS driver,
            r. NAME AS endcityname,
        r1.name as sitename,
            cd.number AS number,
            cd.startdate AS startdate
        FROM
            qfant_order o
        LEFT JOIN qfant_cardrive cd ON o.cardriveid = cd.id
        LEFT JOIN qfant_route r ON r.id = o.endcity
        LEFT JOIN qfant_route r1 ON r1.id = o.site
        LEFT JOIN qfant_car c ON c.id = cd.carid
        WHERE
            1 = 1 ";
        $param=array();
        if(!empty($goodsname)){
            $countsql.=" and o.goodsname like '%s'";
            $sql.=" and o.goodsname like '%s'";
            array_push($param,'%'.$goodsname.'%');
        }
        if(!empty($receivername)){
            $countsql.=" and o.receivername like '%s'";
            $sql.=" and o.receivername like '%s'";
            array_push($param,'%'.$receivername.'%');
        }
        if(!empty($shipper)){
            $countsql.=" and o.shipper like '%s'";
            $sql.=" and o.shipper like '%s'";
            array_push($param,'%'.$shipper.'%');
        }
        $sql.=" order by o.createdate desc,o.id desc limit %d,%d ";
        array_push($param,$offset);
        array_push($param,$rows);
        $data=D('Order')->query($countsql,$param);
        $result['total']=$data[0]['total'];
        $data=D('Order')->query($sql,$param);
        $result["rows"] = $data;
        $this->ajaxReturn($result,'JSON');

    }
    /**
     * 添加
     */
    public function add(){
        if(IS_POST){
            $data=I('post.');
            $data['createdate']=strtotime(I('post.createdate'));
            unset($data['id']);
            $res=D('Order')->addData($data);
            $id=$res;
            $where['id']=$res;
            $data['orderno']=$this->OrdernoMethod($id,"J");
            $data['assembledate']="";
            $result=D('Order')->editData($where,$data);
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
            $type = $_POST['id'];
            $where['id']=$type;
            $data['createdate']=strtotime(I('post.createdate'));
            $data['cardriveid']="";
            $data['status']="0";
            $data['site']="0";
            $result=D('Order')->editData($where,$data);
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

    /**
     *
     * 查看
     */
    public  function look(){
        $data=I('get.');
        $id=$data['id'];
        $sql="select * from qfant_order where id='$id'";
        $data=D('Order')->query($sql,"");
        $time = time();
        if($data[0]['assembledate']==0){
            $data['printtime']=date('Y-m-d H:i' ,$time ) ;
            $data[0]['assembledate']=$data['printtime'];
        }
        else{
            $data[0]['assembledate']=date('Y-m-d H:i' , $data[0]['assembledate'] ) ;
        }



        $this->ajaxReturn($data,'JSON');
    }

    /**
     * 删除
     */
    public function delete(){
        $id=I('get.id');
        $map=array(
            'id'=>$id
        );
        $map1=array('orderid'=>$id);
        $result1=D('Driverorder')->deleteData($map1);//删除装车与订单关联的表
        $result=D('Order')->deleteData($map);
        if($result){
            $message['status']=1;
            $message['message']='删除成功';
        }else {
            $message['status']=0;
            $message['message']='删除失败';
        }
        $this->ajaxReturn($message,'JSON');
    }

    public function addCarOrder(){
        $data['cardriveid']=I('get.id');//发车id
        $ids=I('get.orderid');//订单id
        $arr1 = explode("@@",$ids);
        $data['status']='1';//已装车
        $result="";
        for($index=count($arr1);$index>=0;$index--) {
            if($arr1[$index]){
                $where['id']=$arr1[$index];
                $data1['orderid']=$arr1[$index];
                $map1=array(
                    'orderid'=> $data1['orderid']
                );
                    $result1=D('Driverorder')->deleteData($map1);//删除装车与订单关联的表
                    $data1['cardriveid']=I('get.id');//发车id
                    D('Order')->editData($where,$data);
                    $result=D('Driverorder')->addData($data1);

            }
        }
        if($result){
            $message['status']=1;
            $message['message']='装车成功';
        }else {
            $message['status']=0;
            $message['message']='装车失败';
        }
        $this->ajaxReturn($message,'JSON');
    }

    public function orderList(){
        $cardriveid=I('get.id');//发车id
        $sql="SELECT
	o.id AS oid,
	o.orderno,
	o.shipper,
	o.shippertel,
	o.receivername,
	o.receiveraddress,
	o.receivertel,
	cd.number AS number,
  r.name as endciytname
from qfant_order as o
left join qfant_route as r
on o.endcity=r.id
left join
	qfant_cardrive AS cd
 on o.cardriveid = cd.id
left join
qfant_driverorder as dor
on o.id=dor.orderid
WHERE
 dor.cardriveid = '$cardriveid'
ORDER BY
	dor.id DESC";
       /* $sql ="SELECT o.id AS oid,	o.orderno,	o.shipper,	o.shippertel,	o.receivername,	o.receiveraddress,	o.receivertel,	cd.number AS number FROM	qfant_order AS o,	qfant_cardrive AS cd WHERE	o.cardriveid = cd.id AND o. STATUS = 1 AND cd.id = '$cardriveid'  order by o.id desc";*/
        $data=D('Order')->query($sql,"");
        $this->ajaxReturn($data,'JSON');

    }
    public  function printorderList(){
        $ids=I('get.id');
        $arr1 = explode("@@",$ids);
        for($index=0;$index<count($arr1);$index++) {
            if($arr1[$index]) {
                $data = D('Order')->where(array('id' => $arr1[$index]))->find();
                $datap[$index] = $data;
            }
        }
        $this->ajaxReturn($datap,'JSON');

    }

    public  function editEndCity(){
        $ids=I('post.ids');
        $endCity=I('post.endcity');
        $data['endcity']=$endCity;
        $data['status']="1";
        $arr1 = explode("@@",$ids);
            for($index=0;$index<count($arr1);$index++) {
                if($arr1[$index]) {
                    $where['id']=$arr1[$index];
                    $result=D('Order')->editData($where,$data);
                    if($result){
                        $message['status']=1;
                        $message['message']='保存成功';
                    }else {
                        $message['status']=0;
                        $message['message']='保存失败';
                    }

                }
            }
        $this->ajaxReturn($message,'JSON');

    }
}
