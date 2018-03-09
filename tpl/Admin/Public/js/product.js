var editor3;
KindEditor.ready(function(K) {
    editor3 = K.create('.addintro', {
        allowFileManager : false
    });
});
var editor2;
KindEditor.ready(function(K) {
    editor2 = K.create('.editcontent', {
        allowFileManager : false
    });
});

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
        K('#addcategoryimg1').click(function() {
            editor.loadPlugin('image', function() {
                editor.plugin.imageDialog({
                    fileUrl : K('#thumb').val(),
                    clickFn : function(url, title) {
                        $('.addimg1').textbox("setValue", GLOBALUrl +url);
                        editor.hideDialog();
                    }
                });
            });
        });
        K('#addcategoryimg2').click(function() {
            editor.loadPlugin('image', function() {
                editor.plugin.imageDialog({
                    fileUrl : K('#thumb').val(),
                    clickFn : function(url, title) {
                        $('.addimg2').textbox("setValue", GLOBALUrl +url);
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
        K('#editcategoryimg1').click(function() {
            editor.loadPlugin('image', function() {
                editor.plugin.imageDialog({
                    fileUrl : K('#thumb').val(),
                    clickFn : function(url, title) {
                        $('.editimg1').textbox("setValue", GLOBALUrl +url);
                        editor.hideDialog();
                    }
                });
            });
        });
        K('#editcategoryimg2').click(function() {
            editor.loadPlugin('image', function() {
                editor.plugin.imageDialog({
                    fileUrl : K('#thumb').val(),
                    clickFn : function(url, title) {
                        $('.editimg2').textbox("setValue", GLOBALUrl +url);
                        editor.hideDialog();
                    }
                });
            });
        });
    });
});

var url;
function addProduct(){
    $('#addProduct').dialog('open').dialog('setTitle','添加账号');
    $('#addProductForm').form('clear');
    $('#category').combobox({
        url: ajaxCategoryAllUrl ,
        valueField:'id',
        textField:'name',
        multiple:false,
        onChange:function(){
            $("#category_id").val($('#category').combobox('getValues').join(','));
        }
    });
    url= addUrl ;
}

function doSearch(){
    $('#productGrid').datagrid('load',{
        name: $('#namesearch').val()
    });
}

function addProductSubmit(){
    $('.addintro').val(editor3.html());
    $('#addProductForm').form('submit',{
        url: url,
        onSubmit: function(){
            return $(this).form('validate');
        },
        success:function(data){
            data=$.parseJSON(data);
            if(data.status==1){
                $.messager.alert('Info', data.message, 'info');
                $('#addProduct').dialog('close');
                $('#productGrid').datagrid('reload');
            }else {
                $.messager.alert('Warning', data.message, 'info');
                $('#addProduct').dialog('close');
                $('#productGrid').datagrid('reload');
            }
        }
    });
}
function editProductSubmit(){
    $('.editcontent').val(editor2.html());
    $('#editProductForm').form('submit',{
        url: url,
        onSubmit: function(){
            return $(this).form('validate');
        },
        success:function(data){
            data=$.parseJSON(data);
            if(data.status==1){
                $.messager.alert('Info', data.message, 'info');
                $('#editProduct').dialog('close');
                $('#productGrid').datagrid('reload');
            }else {
                $.messager.alert('Warning', data.message, 'info');
                $('#editProduct').dialog('close');
                $('#productGrid').datagrid('reload');
            }
        }
    });
}
//编辑会员对话窗
function editProduct(){
    var row = $('#productGrid').datagrid('getSelected');
    if(row==null){
        $.messager.alert('Warning',"请选择要编辑的行", 'info');return false;
    }
    if (row){
        $('#editProduct').dialog('open').dialog('setTitle','编辑');
        if(row.particular==1){ $(".editProductCheckbox").attr("checked", true);}
        if(row.recommend==1){ $(".editProductRecommendCheckbox").attr("checked", true);}
        $('#editProductForm').form('load',{
            name:row.name,
            id:row.id,
            pic1:row.pic1,
            pic2:row.pic2,
            pic3:row.pic3,
            price:row.price,
            storage:row.storage,
            marketprice:row.marketprice
        });
        editor2.html(row.intro);
//            alert(row.intro);
        $('#category_edit').combobox({
            url: ajaxCategoryAllUrl +'/id/' +row.id,
            valueField:'id',
            textField:'name',
            multiple:false,
            onChange:function(){
                $("#category_edit_id").val($('#category_edit').combobox('getValues').join(','));

            }
        });
        $('#category_edit').combobox('setValue', row.category_id);
        url = editUrl +'/id/'+row.id;
    }
}
function destroyProduct(){
    var row = $('#productGrid').datagrid('getSelected');
    if(row==null){
        $.messager.alert('Warning',"请选择要删除的行", 'info');return false;
    }
    if (row){
        $.messager.confirm('删除提示','真的要删除?',function(r){
            if (r){
                var durl= deleteUrl ;
                $.getJSON(durl,{id:row.id},function(result){
                    if (result.status){
                        $('#productGrid').datagrid('reload');    // reload the user data
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

function changeStatus() {
    var row = $('#productGrid').datagrid('getSelected');
    if (row){
        var durl= changestatusUrl +'/id/'+row.id;
        $.getJSON(durl,{status:row.status},function(result){
            //alert(result.status);return false;
            if (result.status){
                $('#productGrid').datagrid('reload');    // reload the user data
            } else {
                $.messager.alert('错误提示',result.message,'error');
            }
        },'json').error(function(data){
            var info=eval('('+data.responseText+')');
            $.messager.confirm('错误提示',info.message,function(r){});
        });
    }
}

function formatStatus(val,rowData,row){
    if(val==1){
        val="<span style='color: green'>在售中...</span>";
    }else{
        val="<span style='color: red'>已下架</span>";
    }
    return val;
}
function formatParticular(val,rowData,row){
    if(val==1){
        val="<span style='color: green'>是</span>";
    }else {
        val="<span style='color: red'>否</span>";
    }
    return val;
}