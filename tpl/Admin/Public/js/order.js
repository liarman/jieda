var url;
function addOrder(){
    $('#addOrder').dialog('open').dialog('setTitle','添加');
    $('#addOrderForm').form('clear');
    url=addOrderUrl;
}

function addOrderSubmit(){
    $('#addOrderForm').form('submit',{
        url: url,
        onSubmit: function(){
            return $(this).form('validate');
        },
        success:function(data){
            data=$.parseJSON(data);
            if(data.status==1){
                $.messager.alert('Info', data.message, 'info');
                $('#addOrder').dialog('close');
                $('#OrderGrid').datagrid('reload');
            }else {
                $.messager.alert('Warning', data.message, 'info');
                $('#addOrder').dialog('close');
                $('#OrderGrid').datagrid('reload');
            }
        }
    });
}
function editOrderSubmit(){
    $('#editOrderForm').form('submit',{
        url: url,
        onSubmit: function(){
            return $(this).form('validate');
        },
        success:function(data){
            data=$.parseJSON(data);
            if(data.status==1){
                $.messager.alert('Info', data.message, 'info');
                $('#editOrder').dialog('close');
                $('#OrderGrid').datagrid('reload');
            }else {
                $.messager.alert('Warning', data.message, 'info');
                $('#editOrder').dialog('close');
                $('#OrderGrid').datagrid('reload');
            }
        }
    });
}
//编辑会员对话窗
function editOrder(){
    var row = $('#OrderGrid').datagrid('getSelected');
    if(row==null){
        $.messager.alert('Warning',"请选择要编辑的行", 'info');return false;
    }
    if (row){
        $('#editOrder').dialog('open').dialog('setTitle','编辑');

        $('#editOrderForm').form('load',row);

          url =editOrderUrl+'/id/'+row.id;
    }
}

function destroyOrder(){
    var row = $('#OrderGrid').datagrid('getSelected');
    if(row==null){
        $.messager.alert('Warning',"请选择要删除的行", 'info');return false;
    }
    if (row){
        $.messager.confirm('删除提示','真的要删除?',function(r){
            if (r){
                var durl=deleteOrderUrl;
                $.getJSON(durl,{id:row.id},function(result){
                    if (result.status){
                        $('#OrderGrid').datagrid('reload');    // reload the user data
                    } else {
                        $.messager.alert('错误提示',result.message,'error');
                    }
                },'json').error(function(data){
                    var info=eval('('+data.responseText+')');
                    $.messager.confirm('错误提示',info.message,function(r){});
                });
            }
        });
    }
}

function doSearch(){
    $('#OrderGrid').datagrid('load',{
        goodsname: $('#goodsnamesearch').val(),
        receivername: $('#receivernamesearch').val(),
        shipper: $('#shippersearch').val()
    });
}

function  doShipperSearch(){
    $('#shiperGrid').datagrid('load',{
        shipper: $('#namesearch').val(),
        shippertel: $('#telsearch').val()
    });

}
function doReceiveSearch(){
    $('#receiveGrid').datagrid('load',{
        receivername: $('#renamesearch').val(),
        receivertel: $('#retelsearch').val()
    });
}

function doCarSearch(){
    $('#carorderGrid').datagrid('load',{

       name: $('#driversearch').val(),
       id: $('#caridsearch').val()
    });
}
function ajaxCarList(){
    var row = $('#OrderGrid').datagrid('getSelected');
    if(row==null){
        $.messager.alert('Warning',"请选择运单", 'info');return false;
    }
    $('#carorderDlg').dialog('open').dialog('setTitle','发车列表');
    $('#carorderGrid').datagrid({
        url:ajaxCarUrl+'/orderid/'+row.id,
        columns:[[
            {field:'id',title:'运单id',width:150,hidden:'true'},
            {field:'driver',title:'司机',width:150},
            {field:'carnumber',title:'车牌号',width:150},
            {field:'startdate',title:'发车时间',width:200,formatter:Common.TimeFormatter},
            {field:'cardriveid',title:'发车id',width:100,hidden:'true'},
            {field:'opt',title:'操作',width:50,align:'center',
                formatter:function(value,row,index){
                    console.log("roworderid:"+row.orderid+"rowcardriveid"+row.cardriveid);
                    var btn = '<a class="editcls" onclick="chooseCar(\''+row.orderid+'\',\''+row.cardriveid+'\')" href="javascript:void(0)">装车</a>';
                    return btn;
                }
            }
        ]]
    });
}

function shiperList(){
    $('#shiperDlg').dialog('open').dialog('setTitle','发货人列表');
    $('#shiperGrid').datagrid({
        url:ajaxShiperUrl,
        columns:[[
            {field:'id',title:'发货人id',width:150,hidden:'true'},
            {field:'shipper',title:'姓名',width:150},
            {field:'shippertel',title:'电话',width:150},
            {field:'opt',title:'操作',width:50,align:'center',
                formatter:function(value,row,index){
                    var btn = '<a class="editcls" onclick="chooseShipper(\''+row.shipper+'\',\''+row.shippertel+'\',\''+row.id+'\')" href="javascript:void(0)">确认</a>';
                    return btn;
                }
            }
        ]]

    });
    }

function  receiveList(){
    $('#receiveDlg').dialog('open').dialog('setTitle','发货人列表');
    var  shipperid=$('#addSid').val();
    $('#receiveGrid').datagrid({
        url:ajaxReceiveUrl+'/shipperid/'+shipperid,
        columns:[[
            {field:'id',title:'收货人id',width:150,hidden:'true'},
            {field:'receivername',title:'姓名',width:150},
            {field:'receivertel',title:'电话',width:150},
            {field:'receiveraddress',title:'地址',width:150},
            {field:'opt',title:'操作',width:50,align:'center',
                formatter:function(value,row,index){
                    var btn = '<a class="editcls" onclick="chooseReceive(\''+row.receivername+'\',\''+row.receivertel+'\',\''+row.receiveraddress+'\')" href="javascript:void(0)">确认</a>';
                    return btn;
                }
            }
        ]]

    });
}
function RouteformatStatus(val,rowData,row){
    if(val==1){
        val="<span style='color: green'>启用</span>";
    }else {
        val="<span style='color: red'>禁用</span>";
    }
    return val;
}

function  chooseReceive(receivername,receivertel,receiveraddress){
    var row= $('#receiveGrid').datagrid('getSelected');//发货人行
    var receivername=receivername;
    var receivertel=receivertel;
    var receiveraddress=receiveraddress;

    if(row==null){
        $.messager.alert('Warning',"请选择收货人", 'info');return false;
    }
    if(row){
        $('#addReceivename').textbox('setValue', receivername);
        $('#addReceivetel').textbox('setValue', receivertel);
        $('#addReceiveAddress').textbox('setValue', receiveraddress);
        $('#receiveDlg').dialog('close');
        $('#addOrderForm').form('load',row);
    }
}
function chooseShipper(shipper,shippertel,id){
    var row= $('#shiperGrid').datagrid('getSelected');//发货人行
    var shipper=shipper;//发货人
    var shippertel=shippertel;//发货人电话
    var id=id;
    console.log(shipper+"..."+shippertel+".."+id);
    if(row==null){
        $.messager.alert('Warning',"请选择发货人", 'info');return false;
    }
    if(row){
            $('#addShipper').textbox('setValue', shipper);
            $('#addShippertel').textbox('setValue', shippertel);
            $('#addSid').val(id);
            $('#shiperDlg').dialog('close');
            $('#addOrderForm').form('load',row);
    }
}
function chooseCar(orderid,cardriveid){

    var row = $('#carorderGrid').datagrid('getSelected');//发车行
    var orderid=orderid;//运单id
    var cardriveid=cardriveid;//发车id
    if(row==null){
        $.messager.alert('Warning',"请选择发车", 'info');return false;
    }
    if (row){
        $.messager.confirm('装车提示','真的要装车?',function(r){
            if (r){
                var durl=chooseCarUrl;//装车更新哪一个数据库表
                $.getJSON(durl,{id:cardriveid,orderid:orderid},function(result){
                    if (result.status){
                        $('#carorderDlg').dialog('close');
                        $('#OrderGrid').datagrid('reload');    // reload the user data

                    } else {
                        $.messager.alert('错误提示',result.message,'error');
                    }
                },'json').error(function(data){
                    var info=eval('('+data.responseText+')');
                    $.messager.confirm('错误提示',info.message,function(r){});
                });
            }
        });
    }
}

function timeStatus(val,rowData,row){
    if(val==null){
        return "";
    }else{
    return Common.TimeFormatter(val);
    }
}
function Status(val,rowData,row){
    if(val==0){
        return "";
    }else if(val==1){
        return "已装车";
    }else if(val==2){
        return "已到站";
    }
}


//查看对话窗
function lookOrder(){
    var row = $('#OrderGrid').datagrid('getSelected');
    if(row==null){
        $.messager.alert('Warning',"请选择要编辑的行", 'info');return false;
    }
    if (row){
        $('#lookOrder').dialog('open').dialog('setTitle','查看');
        $('#lookOrderForm').form('load',row);
        url =lookUrl+'/id/'+row.id;
    }
}



function print(inventoryId){
    if (inventoryId=='view') {
        id=$("#lookId").val();
    }
    var purl =lookUrl;
    $.getJSON(purl,{id:id,r:Math.random()},function(data){
        console.log(data);
   // $.getJSON(editOrderUrl?id="+inventoryId+"&r="+Math.random(), function(json){
        var LODOP=getLodop(document.getElementById('LODOP'),document.getElementById('LODOP_EM'));
        var payType="",goodUnit="";
     //   var backimg=root+"images/print_backg.jpg";
       LODOP.PRINT_INIT("");
        //LODOP.ADD_PRINT_SETUP_BKIMG("<img src='"+backimg+"' />");
        //LODOP.SET_SHOW_MODE("BKIMG_IN_PREVIEW",true);
        LODOP.SET_PRINT_STYLE("FontSize",12);
        LODOP.ADD_PRINT_TEXT(110,40,200,27,data[0].time);//打印时间
        LODOP.ADD_PRINT_TEXT(158,50,98,27,data[0].shipper);//托运人姓名
        LODOP.ADD_PRINT_TEXT(186,413,110,24,"");//
        LODOP.ADD_PRINT_TEXT(150,435,115,25,data[0].shippertel);//电话
        LODOP.ADD_PRINT_TEXT(193,50,105,22,data[0].receivername);//收获人名称
        LODOP.ADD_PRINT_TEXT(190,235,170,28,data[0].receiveraddress);//收货地址
        LODOP.ADD_PRINT_TEXT(190,435,120,20,data[0].receivertel);//收货电话
        LODOP.ADD_PRINT_TEXT(260,0,109,52,data[0].goodsname);//货物名称
        LODOP.ADD_PRINT_TEXT(260,100,66,47,data[0].goodscount);//件数
        if (data[0].goodsunit=="1"){
            goodsunit="千克";
        }else if (data[0].goodsunit=="2") {
            goodsunit="箱";
        }else {
            goodsunit="";
        }
        LODOP.ADD_PRINT_TEXT(260,155,79,47,data[0].goodsweight+goodsunit);//重量
        LODOP.ADD_PRINT_TEXT(270,300,104,25,data[0].goodsinsurance);//保险金额
        LODOP.ADD_PRINT_TEXT(270,480,72,28,data[0].insurance);//保险费
     //   LODOP.ADD_PRINT_TEXT(310,50,300,28,data[0].countFee);//合计金额.rmb
        LODOP.ADD_PRINT_TEXT(310,460,72,28,data[0].countFee);//合计金额
        if (data[0].paytype=="1"){
            paytype="欠付";
        }else if (data[0].paytype=="2") {
            paytype="货到付款";
        }else if(data[0].paytype=="3"){
            paytype="现付";
        }
       LODOP.ADD_PRINT_TEXT(347,50,180,25,paytype+"    "+data[0].col);//付款方式。备注表格字段没有添加
     //   LODOP.ADD_PRINT_TEXT(320,420,77,35,json.printuser);//经办人经办人没有添加
        LODOP.PREVIEW();
    });
}

