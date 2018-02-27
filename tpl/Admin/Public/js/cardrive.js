var url;
function addCardrive(){
    $('#addCardrive').dialog('open').dialog('setTitle','添加');
    $('#addCardriveForm').form('clear');

    url= addCardriveUrl ;
}
function addarrive(row){
    $('#addarrive').dialog('open').dialog('setTitle','添加');
    $('#addarriveForm').form('clear');
    $('#route').combobox({});
    $('#addarrive_id').val(row.id);
    $('#cardrive_id').val(row.cardriveid);
    url= addarriveUrl;
}
function  editarrive(){
    var row = $('#arriveGrid').datagrid('getSelected');
    var  time=  row.arrivedate;
    time=timeStatus(time);
    if(row==null){
        $.messager.alert('Warning',"请选择要编辑的行", 'info');return false;
    }
    if (row){
        $('#editarrive').dialog('open').dialog('setTitle','编辑');
        $('#editarriveForm').form('load',row);
        $('#editarrivetime').datetimebox('setValue', time);
        url = editarriveUrl +'/id/'+row.cdid;
    }
}

function deletearrive(){
    var row = $('#arriveGrid').datagrid('getSelected');
    if(row==null){
        $.messager.alert('Warning',"请选择要删除的行", 'info');return false;
    }
    if (row){
        $.messager.confirm('删除提示','真的要删除?',function(r){
            if (r){
                var durl=deletearriveUrl;
                $.getJSON(durl,{id:row.cdid},function(result){
                    if (result.status){
                        $('#arriveGrid').datagrid('reload');    // reload the user data
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

function  editarriveSubmit(){
    $('#editarriveForm').form('submit',{
        url: url,
        onSubmit: function(){
            return $(this).form('validate');
        },
        success:function(data){
            data=$.parseJSON(data);
            if(data.status==1){
                $.messager.alert('Info', data.message, 'info');
                $('#editarrive').dialog('close');
                $('#arriveGrid').datagrid('reload');
            }else {
                $.messager.alert('Warning', data.message, 'info');
                $('#editarrive').dialog('close');
                $('#arriveGrid').datagrid('reload');
            }
        }
    });
}
function addCardriveSubmit(){
    $('#addCardriveForm').form('submit',{
        url: url,
        onSubmit: function(){
            return $(this).form('validate');
        },
        success:function(data){
            data=$.parseJSON(data);
            if(data.status==1){
                $.messager.alert('Info', data.message, 'info');
                $('#addCardrive').dialog('close');
                $('#CardriveGrid').datagrid('reload');
            }else {
                $.messager.alert('Warning', data.message, 'info');
                $('#addCardrive').dialog('close');
                $('#CardriveGrid').datagrid('reload');
            }
        }
    });
}
function editCardriveSubmit(){
    $('#editCardriveForm').form('submit',{
        url: url,
        onSubmit: function(){
            return $(this).form('validate');
        },
        success:function(data){
            data=$.parseJSON(data);
            if(data.status==1){
                $.messager.alert('Info', data.message, 'info');
                $('#editCardrive').dialog('close');
                $('#CardriveGrid').datagrid('reload');
            }else {
                $.messager.alert('Warning', data.message, 'info');
                $('#editCardrive').dialog('close');
                $('#CardriveGrid').datagrid('reload');
            }
        }
    });
}
//编辑会员对话窗
function editCardrive(){
    var row = $('#CardriveGrid').datagrid('getSelected');
    var  time=  row.startdate;
    time=timeStatus(time);
    if(row==null){
        $.messager.alert('Warning',"请选择要编辑的行", 'info');return false;
    }
    if (row){
        $('#editCardrive').dialog('open').dialog('setTitle','编辑');
        $('#editCardriveForm').form('load',row);
        $('#editStartdate').datetimebox('setValue', time);
        url = editCarUrl +'/id/'+row.id;
    }
}
function timeStatus(value){
    if (value == 0) {
        return "";
    }
    var newDate = new Date();
    newDate.setTime(value * 1000);
    if (newDate.getFullYear() < 1900) {
        return "";
    }
    var val = newDate.format("yyyy-MM-dd hh:mm:ss");
    return val;
}
function destroyCardrive(){
    var row = $('#CardriveGrid').datagrid('getSelected');
    if(row==null){
        $.messager.alert('Warning',"请选择要删除的行", 'info');return false;
    }
    if (row){
        $.messager.confirm('删除提示','真的要删除?',function(r){
            if (r){
                var durl= deleteCarUrl;
                $.getJSON(durl,{id:row.id},function(result){
                    if (result.status){
                        $('#CardriveGrid').datagrid('reload');    // reload the user data
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
function cararrive(){
    var row = $('#CardriveGrid').datagrid('getSelected');
    if(row==null){
        $.messager.alert('Warning',"请选择发出车辆", 'info');return false;
    }
    $('#arriveDlg').dialog('open').dialog('setTitle','到站信息');
    $('#arriveGrid').datagrid({
        url: addarriveListUrl +'/cardriveid/'+row.cardriveid,
        columns:[[
            {field:'cdid',title:'站点id',width:100,align:'center',hidden:'true'},
            {field:'name',title:'到达地',width:100,align:'center'},
            {field:'arrivedate',title:'到达时间',width:270,align:'center',formatter:Common.TimeFormatter}
        ]],
        toolbar: [{
            iconCls: 'fa fa-plus',
            handler: function(){addarrive(row);}
        },{
            iconCls: 'fa fa-edit',
            handler: function(){editarrive();}
        },
            {
                iconCls: 'fa fa-remove',
                handler: function(){deletearrive();}
            }]
    });
}
function orderList(){
    var row = $('#CardriveGrid').datagrid('getSelected');
    if(row==null){
        $.messager.alert('Warning',"请选择要查看的运单的车辆", 'info');return false;
    }
    $('#orderListDlg').dialog('open').dialog('setTitle','运单列表');
    $('#orderListGrid').datagrid({
        url: orderListUrl +'/id/'+row.cardriveid,
        singleSelect: false,
        selectOnCheck: true,
        checkOnSelect: true,
        columns: [[
            {field: 'ck', checkbox:"true", width: 100},
            {field: 'oid', title: '运单id',hidden:'true' ,width: 100},
            {field: 'orderno', title: '运单编号', width: 180},
            {field: 'shipper', title: '寄件人姓名', width: 100},
            {field: 'shippertel', title: '寄件人电话', width: 100},
            {field: 'receivername', title: '收件人姓名', width: 100},
            {field: 'receiveraddress', title: '收件人电话', width: 100},
            {field: 'receivertel', title: '收件人地址', width: 100},
            {field: 'endciytname', title: '收货城市', width: 100}
        ]],
        onLoadSuccess:function(data){
           if(data){
               $.each(data.rows, function(index, item){
                   if(item.checked){
                      $('#orderListGrid').datagrid('checkRow', index);
                       console.log(item);
                        }
                    });
               }
           },
        toolbar: [{
            iconCls: 'fa fa-file-text-o',
            id:'ButonGetCheck',
            text:'打印',
            handler: function(){printList();
            }
        },
            {
                iconCls: 'fa fa-file-text-o',
                id:'ButonGetCity',
                text:'收货城市',
                handler: function(){endCity();
                }
            }]
    });
}
function printList(){
    $('#ButonGetCheck').click(function(){
        var checkedItems = $('#orderListGrid').datagrid('getChecked');
        var ids = [];
        $.each(checkedItems, function(index, item){
            ids.push(item.oid);
            });
        ids=ids.join("@@");
        if (ids.length>0) {
            var url=printUrl;
            $.getJSON(url,{id:ids}, function(datap){
                console.log(datap);
                var LODOP=getLodop(document.getElementById('LODOP'),document.getElementById('LODOP_EM'));
                LODOP.PRINT_INIT("");
                LODOP.SET_PRINT_STYLE("FontSize",12);
                var y=150;
                $.each(datap,function (i,n) {
                    LODOP.ADD_PRINT_TEXT(y,0,100,30,n.shipper);
                    LODOP.ADD_PRINT_TEXT(y,100,100,30,n.goodsname);
                    LODOP.ADD_PRINT_TEXT(y,200,300,30,n.receiveraddress+"  "+n.receivername+"  "+n.receivertel);
                    LODOP.ADD_PRINT_TEXT(y,500,55,30,n.goodscount);
                    y+=34;
                });
                LODOP.PREVIEW();
            });
        }else {
            $.messager.alert('Warning',"要打印运单列表为空", 'info');return false;
            //alert("！");
            return;
        }
        });
}

function   endCity(){
    $('#ButonGetCity').click(function(){
        var checkedItems = $('#orderListGrid').datagrid('getChecked');
        var ids = [];
        $.each(checkedItems, function(index, item){
            ids.push(item.oid);
        });
        ids=ids.join("@@");
        if (ids.length>0) {
            $('#EndCityIds').val(ids);
            $('#endCityDlg').dialog('open').dialog('setTitle', '收货城市');
        }else{
            $.messager.alert('Warning',"设置收货城市的表为空", 'info');return false;

        }
    });
}

function  EndCitySubmit(){
    $('#endcityForm').form('submit',{
        url: editEndCity,
        onSubmit: function(){
            return $(this).form('validate');
        },
        success:function(data){
            data=$.parseJSON(data);
            if(data.status==1){
                $.messager.alert('Info', data.message, 'info');
                $('#endCityDlg').dialog('close');
                $('#orderListGrid').datagrid('reload');
            }else {
                $.messager.alert('Warning', data.message, 'info');
                $('#endCityDlg').dialog('close');
                $('#orderListGrid').datagrid('reload');
            }
        }
    });
}

function addarriveSubmit(){
    $('#addarriveForm').form('submit',{
        url: addarriveUrl,
        onSubmit: function(){
            return $(this).form('validate');
        },
        success:function(data){
            data=$.parseJSON(data);
            if(data.status==1){
                $.messager.alert('Info', data.message, 'info');
                $('#addarrive').dialog('close');
                $('#arriveGrid').datagrid('reload');
            }else {
                $.messager.alert('Warning', data.message, 'info');
                $('#addarrive').dialog('close');
                $('#arriveGrid').datagrid('reload');
            }
        }
    });
}


$('#number').combobox({});
