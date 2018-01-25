<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
 * 后台门店管理控制器
 */
class CardriveController extends AdminBaseController{
    public function index(){
        $this->display();
    }
    public function numberList(){
        $data=M('Car')->where(array('status'=>1 ))->select();
        $this->ajaxReturn($data,'JSON');
    }

    public function routeList(){
        $data=M('Route')->select();
        $this->ajaxReturn($data,'JSON');
    }

    public function ajaxarrive(){
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
        $offset = ($page-1)*$rows;
        $cardriveid = I('get.cardriveid');
        $countsql ="select count(id) AS total from qfant_cardriveroute ";
        $sql ="SELECT c2.id,r.name,c3.arrivedate ,c3.id as cdid,c3.routeid as routeid,c2.number FROM qfant_route AS r,qfant_car AS c1,qfant_cardrive AS c2,qfant_cardriveroute AS c3 WHERE c3.cardriveid = '$cardriveid' AND c3.cardriveid = c2.id AND c3.routeid = r.id AND c2.carid = c1.id ;";

        $param=array();
        array_push($param,$offset);
        array_push($param,$rows);
        $sql.=" order by c2.startdate desc limit %d,%d";
        $data=D('Cardriveroute')->query($countsql,$param);
        $result['total']=$data[0]['total'];
        $data=D('Cardriveroute')->query($sql,$param);
        $result["rows"] = $data;
        $this->ajaxReturn($result,'JSON');
    }
    public function ajaxCardriveList(){
        $driver=I("post.name");//司机名称
        $carnumber=I("post.id");//车牌号

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
        $offset = ($page-1)*$rows;
        $countsql ="select count(id) AS total from qfant_cardrive where 1=1 ";
        $sql ="select r.id,r.driver,r.carnumber,d.id AS cardriveid,d.carid,d.startdate ,d.number,d.id FROM qfant_car AS r ,qfant_cardrive AS d WHERE r.id = d.carid";

        $param=array();
        if(!empty($driver)){
            $countsql.=" and r.driver like '%s'";
            $sql.=" and r.driver like '%s'";
            array_push($param,'%'.$driver.'%');
        }
        if(!empty($carnumber)){
            $countsql.=" and r.carnumber like '%s'";
            $sql.=" and r.carnumber like '%s'";
            array_push($param,'%'.$carnumber.'%');
        }
        array_push($param,$offset);
        array_push($param,$rows);
        $sql.="  order by d.startdate desc limit %d,%d";
        $data=D('Cardrive')->query($countsql,$param);
        $result['total']=$data[0]['total'];
        $data=D('Cardrive')->query($sql,$param);
        $result["rows"] = $data;
        $this->ajaxReturn($result,'JSON');
    }

    public function addarrive(){
        if(IS_POST){
            //print_r($_POST);die;
            $data['cardriveid']=I('post.cardriveid');
            $data['routeid']=I('post.routeid');
            $data['arrivedate']=strtotime(I('post.arrivedate'));
            unset($data['id']);
            $result=D('Cardriveroute')->addData($data);

            if($result){
                $cardriveid=I('post.cardriveid');
                $routeid=I('post.routeid');
                $sql="SELECT
                        o.*
                    FROM
                        qfant_order AS o
                    WHERE
                    o.status=1 and
                     o.endcity = '$routeid'
                    AND o.cardriveid ='$cardriveid'";
             //   $sql = "select o.* from qfant_order as o ,qfant_cardrive as cd ,qfant_cardriveroute as cdr where o.endcity=cdr.routeid AND cd.id=cdr.cardriveid and o.cardriveid=cd.id  and o.endcity='$routeid' and o.cardriveid='$cardriveid'";
                $d=D('Order')->query($sql,"");
                if($d){//设置订单已到站
                    $order['status']='2';
                    $order['sitetime']=strtotime(I('post.arrivedate'));//更新到站时间
                    $order['site']= $routeid;//订单更新站点数据
                    for($i = 0; $i <sizeof($d); $i++)
                    {
                        $where['id']=$d[$i]['id'];
                        D('Order')->editData($where,$order);
                    }
                    $where['id']=$d['id'];
                    D('Order')->editData($where,$order);

                }
                //没有到站的也要添加站点
               // $sql1 = "select o.* from qfant_order as o ,qfant_cardrive as cd ,qfant_cardriveroute as cdr where o.endcity=cdr.routeid AND cd.id=cdr.cardriveid and o.cardriveid=cd.id  and o.endcity<>'$routeid' and o.cardriveid='$cardriveid'";
                $sql1="SELECT
                        o.*
                    FROM
                        qfant_order AS o
                    WHERE
                    o.status=1 and
                     o.endcity <> '$routeid'
                    AND o.cardriveid ='$cardriveid'";
                $d1=D('Order')->query($sql1,"");
                if($d1){//设置订单已到站
                    $order['site']= $routeid;//订单更新站点数据
                    $order['sitetime']=strtotime(I('post.arrivedate'));//更新到站时间
                    for($i = 0; $i <sizeof($d1); $i++)
                    {
                        $where['id']=$d1[$i]['id'];
                        $res=D('Order')->editData($where,$order);
                    }
                    $where['id']=$d1['id'];
                    D('Order')->editData($where,$order);

                }
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
     * 添加
     */
    public function addCardrive(){
        if(IS_POST){
            $data['carid']=I('post.carid');
            $data['number']=I('post.number');
            $data['startdate']=strtotime(I('post.startdate'));
            unset($data['id']);
            $result=D('Cardrive')->addData($data);

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
    public function editCardrive(){
        if(IS_POST){
            $data['id']=I('post.id');
            $data['carid']=I('post.carid');
            $data['startdate']=strtotime(I('post.startdate'));
            $data['number']=I('post.number');
            //print_r($data);die;
            $where['id']=$data['id'];
            $result=D('Cardrive')->editData($where,$data);
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
     * 编辑站点
     */
    public function editarrive(){
        $id=I('get.id');
        $data['routeid']=I('post.routeid');
        $data['arrivedate']=strtotime(I('post.arrivedate'));
        $where['id']=$id;
        $result=D('Cardriveroute')->editData($where,$data);
        if($result){
            $message['status']=1;
            $message['message']='保存成功';
        }else {
            $message['status']=0;
            $message['message']='保存失败';
        }
        $this->ajaxReturn($message,'JSON');
    }
    /**
     * 删除站点
     */
    public function deletearrive(){
        $id=I('get.id');
        $map=array(
            'id'=>$id
        );
        $result=D('Cardriveroute')->deleteData($map);
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
     * 删除
     */
    public function deleteCardrive(){
        $id=I('get.id');
        $map=array(
            'id'=>$id
        );
        $map1=array(
            'cardriveid'=>$id
        );
        //删除发车列表，如果该车已有的订单，设置为已提交订单的状态
        $dataOrder=D('Driverorder')->where(array('cardriveid' => $id))->select();
        if($dataOrder[0]){
            $data1['status']=0;
            for($index=0;$index<count($dataOrder);$index++) {
                if($dataOrder[$index]) {
                    $where['id']=$dataOrder[$index]['orderid'];
                    $result2=D('Order')->editData($where,$data1);
                }
            }
            $result1=D('Driverorder')->deleteData($map1);//删除发车表的记录同时删除装车与订单关联的表
        }

        $result=D('Cardrive')->deleteData($map);
        if($result){
            $message['status']=1;
            $message['message']='删除成功';
        }else {
            $message['status']=0;
            $message['message']='删除失败';
        }
        $this->ajaxReturn($message,'JSON');
    }

    public function ajaxCarDriv(){
        $orderid=I('get.orderid');
        $driver=I('post.driver');
        $number=I('post.number');
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
        $offset = ($page-1)*$rows;
        $countsql="select  count(r.id )as total  FROM qfant_car AS r ,qfant_cardrive AS d WHERE r.id = d.carid ";
        $sql ="select r.id as carid,r.driver as driver ,r.carnumber as carnumber,d.id AS cardriveid,d.carid,d.startdate as startdate ,d.number as number FROM qfant_car AS r ,qfant_cardrive AS d WHERE r.id = d.carid ";
        $param=array();
        if(!empty($driver)){
            $countsql.=" and r.driver like '%s'";
            $sql.=" and r.driver like '%s'";
            array_push($param,'%'.$driver.'%');
        }
        if(!empty($number)){
            $countsql.=" and d.number like '%s'";
            $sql.=" and d.number like '%s'";
            array_push($param,'%'.$number.'%');
        }
        array_push($param,$offset);
        array_push($param,$rows);
        $sql.=" order by d.startdate desc limit %d,%d";
        $data=D('Cardrive')->query($countsql,$param);
        $result['total']=$data[0]['total'];
        $data=D('Cardrive')->query($sql,$param);
        foreach ($data as $key=>$basevalue){
                $data[$key]['orderid']= $orderid;
        }
        $result["rows"] = $data;
        $this->ajaxReturn($result,'JSON');

    }
}
