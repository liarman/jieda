$(document).ready(function(){
    KindEditor.ready(function(K){
        var editor = K.editor({
            allowFileManager:false
        });
        K('#addcategoryimg').click(function() {
            editor.loadPlugin('image', function() {
                editor.plugin.imageDialog({
                    fileUrl : K('#thumb').val(),
                    clickFn : function(url, title) {
                        $('.addimg').textbox("setValue", GLOBALUrl +url);
                        editor.hideDialog();
                    }
                });
            });
        });
        K('#editcategoryimg').click(function() {
            editor.loadPlugin('image', function() {
                editor.plugin.imageDialog({
                    fileUrl : K('#thumb').val(),
                    clickFn : function(url, title) {
                        $('.editimg').textbox("setValue", GLOBALUrl +url);
                        editor.hideDialog();
                    }
                });
            });
        });
    });
});
var url;
function addCategory(){
    $('#addCategory').dialog('open').dialog('setTitle','添加');
    $('#addCategoryForm').form('clear');
    $('#pid').combobox({});
    url= addUrl ;
}
function addCategorySubmit(){
    $('#addCategoryForm').form('submit',{
        url: url,
        onSubmit: function(){
            return $(this).form('validate');
        },
        success:function(data){
            data=$.parseJSON(data);
            if(data.status==1){
                $.messager.alert('Info', data.message, 'info');
                $('#addCategory').dialog('close');
                $('#categoryGrid').datagrid('reload');
            }else {
                $.messager.alert('Warning', data.message, 'info');
                $('#addCategory').dialog('close');
                $('#categoryGrid').datagrid('reload');
            }
        }
    });
}
function editCategorySubmit(){
    $('#editCategoryForm').form('submit',{
        url: url,
        onSubmit: function(){
            return $(this).form('validate');
        },
        success:function(data){
            data=$.parseJSON(data);
            if(data.status==1){
                $.messager.alert('Info', data.message, 'info');
                $('#editCategory').dialog('close');
                $('#categoryGrid').datagrid('reload');
            }else {
                $.messager.alert('Warning', data.message, 'info');
                $('#editCategory').dialog('close');
                $('#categoryGrid').datagrid('reload');
            }
        }
    });
}
//编辑会员对话窗
function editCategory(){
    var row = $('#categoryGrid').datagrid('getSelected');
    if(row==null){
        $.messager.alert('Warning',"请选择要编辑的行", 'info');return false;
    }
    if (row){
        $('#editCategory').dialog('open').dialog('setTitle','编辑');
        $('#editCategoryForm').form('load',row);
        if(row.recommend==1){ $(".editcategoryCheckbox").attr("checked", true);}
        if(row.pid==0){
            $('.pidedit').combobox('loadData',[{'id':0,'name':'无'}]);
        }else {
            // $('.pidedit').combobox('loadData',{});
            $('.pidedit').combobox({url: categoryLevelUrl });
            $('.pidedit').combobox('setValue', row.pid);
        }
        url = editUrl +'/id/'+row.id;
    }
}
function destroyCategory(){
    var row = $('#categoryGrid').datagrid('getSelected');
    if(row==null){
        $.messager.alert('Warning',"请选择要删除的行", 'info');return false;
    }
    if (row){
        $.messager.confirm('删除提示','真的要删除?',function(r){
            if (r){
                var durl= deleteUrl;
                $.getJSON(durl,{id:row.id},function(result){
                    if (result.status){
                        $('#categoryGrid').datagrid('reload');    // reload the user data
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
$('#pid').combobox({});
function formatRecommend(val,rowData,row){
    if(val==1){
        val="<span style='color: green'>是</span>";
    }else {
        val="<span style='color: red'>否</span>";
    }
    return val;
}

function listChild(){
    var row = $('#categoryGrid').datagrid('getSelected');
    if(row==null){
        $.messager.alert('Warning',"请选择查看子类的行", 'info');return false;
    }
    $('#childDlg').dialog('open').dialog('setTitle','子类列表');
    $('#CaChildGrid').datagrid({
        url:ajaxChildUrl+'/id/'+row.id,
        columns:[[
            {field:'name',title:'名称',width:100},
            {field:'imgurl',title:'图片',width:100,formatter:imgFormatter},
            {field:'sort',title:'排序',width:100,align:'right'}
        ]]
    });
}

function formatOper(val,row,index){

    var btn2 = '<a class="editcls" style="color:#666666"  onclick="catogy(\''+row.id+'\')" href="javascript:void(0)"> 分类属性管理 |  </a>';
    var print = '<a class="editcls" style="color:#666666" onclick="product(\''+row.id+'\')" href="javascript:void(0)"> 商品管理  </a>';
    var btnop = btn2+"   "+ print;
    return btnop;
}

function normStatus(val,row,index){
var  btn="";
    if(row.norm){
        btn = '<a class="editcls" id="normId" style="color:#666666" onclick="normbtn(\''+row.id+'\',\''+row.norm+'\')" href="javascript:void(0)"> 规格管理  </a>';
        return row.norm +" "+ btn;
    }else{
        return  "";
    }

}

function colorStatus(val,row,index){
    var  btn="";
    if(row.color){
        btn = '<a class="editcls" id=" colorId" style="color:#666666" onclick="colorbtn(\''+row.id+'\',\''+row.color+'\')" href="javascript:void(0)"> 外观管理  </a>';
        return row.color+" " + btn;
    } else{
        return  "";
    }

}
//0 norm  1 color
function  colorbtn( id,color){
        console.log(id+"11"+color);
        var catid = id;
        var color=color;
        var type = 1;
        $('#color').textbox('setValue',color);
        $('#colorcatid').textbox('setValue',catid);
        $('#colorDlg').dialog('open').dialog('setTitle',color+"  "+'规格列表');
        $('#colorlistGrid').datagrid({
            url:looknormUrl+'/catId/'+catid+'/type/'+type,
            columns:[[
                {field:'id',title:'id',width:30},
                {field:'name',title:'分类名称',width:80},
                {field:'type',title:'类型',width:120,formatter:formatType},
                {field:'value',title:'规格值',width:110}

            ]]
        });
}

function  normbtn( id,norm){
    console.log(id+"11"+norm);
    var catid = id;
    var norm=norm;
    var type = 0;
    $('#norm').textbox('setValue',norm);
    $('#catid').textbox('setValue',catid);
    $('#normDlg').dialog('open').dialog('setTitle',norm+"  "+'规格列表');
    $('#normlistGrid').datagrid({
        url:looknormUrl+'/catId/'+catid+'/type/'+type,
        columns:[[
            {field:'id',title:'id',width:30},
            {field:'name',title:'分类名称',width:80},
            {field:'type',title:'类型',width:120,formatter:formatType},
            {field:'value',title:'规格值',width:110}

        ]]
    });
}

function formatType(val,row,index){
    if(row.type==0){
        btn ="规格";
    } if(row.type==1){
        btn ="外观";
    }
    return  btn;
}

function addNorm(){
    $('#addNorm').dialog('open').dialog('setTitle','添加');
    var norm = $('#norm').val();
    var catid = $('#catid').val();
    $('#addNormForm').form('clear');
    $('#normName').textbox('setValue',norm);
    $('#catid1').textbox('setValue',catid);
    url= addNormUrl ;
}
function addColor(){
    $('#addColor').dialog('open').dialog('setTitle','添加');
    var color = $('#color').val();
    var colorcatid = $('#colorcatid').val();
    $('#addColorForm').form('clear');
    $('#colortypecolor').textbox('setValue',1);
    $('#addColorName').textbox('setValue',color);
    $('#colorcatid1').textbox('setValue',colorcatid);
    url= addNormUrl ;
}
function addColorSubmit(){
    $('#addColorForm').form('submit',{
        url: url,
        onSubmit: function(){
            return $(this).form('validate');
        },
        success:function(data){
            data=$.parseJSON(data);
            if(data.status==1){
                $.messager.alert('Info', data.message, 'info');
                $('#addColor').dialog('close');
                $('#normlistGrid').datagrid('reload');
            }else {
                $.messager.alert('Warning', data.message, 'info');
                $('#addColor').dialog('close');
                $('#normlistGrid').datagrid('reload');
            }
        }
    });
}
function addNormSubmit(){
    $('#addNormForm').form('submit',{
        url: url,
        onSubmit: function(){
            return $(this).form('validate');
        },
        success:function(data){
            data=$.parseJSON(data);
            if(data.status==1){
                $.messager.alert('Info', data.message, 'info');
                $('#addNorm').dialog('close');
                $('#normlistGrid').datagrid('reload');
            }else {
                $.messager.alert('Warning', data.message, 'info');
                $('#addNorm').dialog('close');
                $('#colorlistGrid').datagrid('reload');
            }
        }
    });
}

function editNorm(){
    var row = $('#normlistGrid').datagrid('getSelected');
    if(row==null){
        $.messager.alert('Warning',"请选择要编辑的行", 'info');return false;
    }
    if (row){
        var norm = $('#norm').val();
        var catid = $('#catid').val();
        $('#editNorm').dialog('open').dialog('setTitle','编辑');
        $('#editnormName').textbox('setValue',norm);

        $('#catid2').textbox('setValue',catid);
        $('#editNormForm').form('load',row);
        url = editNormUrl +'/id/'+row.id;
    }

}
function editColor(){
    var row = $('#colorlistGrid').datagrid('getSelected');
    if(row==null){
        $.messager.alert('Warning',"请选择要编辑的行", 'info');return false;
    }
    if (row){
        var color = $('#color').val();
        var colorcatid = $('#colorcatid').val();
        $('#editColor').dialog('open').dialog('setTitle','编辑');
        $('#editcolorName').textbox('setValue',color);
        $('#colortypecolor1').textbox('setValue',1);
        $('#colorcatid2').textbox('setValue',colorcatid);
        $('#editColorForm').form('load',row);
        url = editNormUrl +'/id/'+row.id;
    }

}
function editNormSubmit(){
    $('#editNormForm').form('submit',{
        url: url,
        onSubmit: function(){
            return $(this).form('validate');
        },
        success:function(data){
            data=$.parseJSON(data);
            if(data.status==1){
                $.messager.alert('Info', data.message, 'info');
                $('#editNorm').dialog('close');
                $('#normlistGrid').datagrid('reload');
            }else {
                $.messager.alert('Warning', data.message, 'info');
                $('#editNorm').dialog('close');
                $('#normlistGrid').datagrid('reload');
            }
        }
    });
}

function editColorSubmit(){
    $('#editColorForm').form('submit',{
        url: url,
        onSubmit: function(){
            return $(this).form('validate');
        },
        success:function(data){
            data=$.parseJSON(data);
            if(data.status==1){
                $.messager.alert('Info', data.message, 'info');
                $('#editColor').dialog('close');
                $('#colorlistGrid').datagrid('reload');
            }else {
                $.messager.alert('Warning', data.message, 'info');
                $('#editColor').dialog('close');
                $('#colorlistGrid').datagrid('reload');
            }
        }
    });
}

function destroyNorm(){
    var row = $('#normlistGrid').datagrid('getSelected');
    if(row==null){
        $.messager.alert('Warning',"请选择要删除的行", 'info');return false;
    }
    if (row){
        $.messager.confirm('删除提示','真的要删除?',function(r){
            if (r){
                var durl= deleteNormUrl;
                $.getJSON(durl,{id:row.id},function(result){
                    if (result.status){
                        $('#normlistGrid').datagrid('reload');    // reload the user data
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

function  destroyColor(){
    var row = $('#colorlistGrid').datagrid('getSelected');
    if(row==null){
        $.messager.alert('Warning',"请选择要删除的行", 'info');return false;
    }
    if (row){
        $.messager.confirm('删除提示','真的要删除?',function(r){
            if (r){
                var durl= deleteNormUrl;
                $.getJSON(durl,{id:row.id},function(result){
                    if (result.status){
                        $('#normlistGrid').datagrid('reload');    // reload the user data
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