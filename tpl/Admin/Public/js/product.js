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
    $('#addProduct').dialog('open').dialog('setTitle','添加商品');
    /**
     * 不知道商品是否有规格、外观、先隐藏
     * 判断如果有则就显示并显示出数据
     */
    //防止每次点开就追加一次
    $("#colorAppend").html("");
    $("#normAppend").html("");
    $("#priceList").html("");



    $('#visitdisplaycolor').hide();
    $('#visitdisplaynorm').hide();

    var catname = $('#catname').val();
    var pcatid = $('#pcatid').val();
    var Normhtml="";
    var Colorhtml="";
    var listcolorname = $('#listcolorname').val();
    var listnormname = $('#listnormname').val();
    console.log("pcatid"+pcatid);
    $.post(selectNormByCatidUrl, {"catid":pcatid}, function(message){
        console.log(message);
        $('#addProductForm').form('clear');
        if(message['Norm'].length !=0){
            $('#visitdisplaynorm').show();
        }else{
            $('#visitdisplaynorm').hide();
        }
        if(message['Color'].length  !=0){
            $('#visitdisplaycolor').show();
        }else{
            $('#visitdisplaycolor').hide();
        }
        for(var i=0;i<message['Color'].length;i++){
            Colorhtml+='<td width="130"><input type="checkbox"  class="color"  name="color[]" id="' + message['Color'][i]['id'] + '" value="' + message['Color'][i]['id'] + '"    atr="' + message['Color'][i]['value'] + '" />&nbsp;&nbsp;<label for="' + message['Color'][i]['id'] + '">' + message['Color'][i]['value'] + '</label></td>';
        }
        $("#colorAppend").html(Colorhtml);
        for(var i=0;i<message['Norm'].length;i++){
            Normhtml+='<td width="130"><input type="checkbox"   class="norms"   name="norms[]"   id="' + message['Norm'][i]['id'] + '"  value="' + message['Norm'][i]['id'] + '"  atr="' + message['Norm'][i]['value'] + '" />&nbsp;&nbsp;<label for="' + message['Norm'][i]['id'] + '">' + message['Norm'][i]['value'] + '</label></td>';
        }
        $("#normAppend").html(Normhtml);

        $('#addProductCatname').textbox('setValue',catname);
        $('#addProductCatid').val(pcatid);
        $('#normAdd').textbox('setValue',listnormname);
        $('#colorAdd').textbox('setValue',listcolorname);

        var oldselect = [];
        //不知道干什么用的
      /*  $(".editselect").each(function(){
            var t = $(this).val().split(",");
            oldselect[t[1] + '_' + t[3]] = new Array(t[0], t[1], t[2], t[3], t[4], t[5], t[6], t[7]);
        });*/
        $(".color").click(function(){
            var selectValue = [];
            var html = '';
            var header = '<tr><th width="130">产品外观</th><th width="130">产品规格</th><th width="130">价格</th><th width="130">会员价</th><th width="130">数量</th></tr>';
            console.log("NormsHtml:"+$(".norms").html());
            if ($(".norms").html() == null ||$(".norms").html() == "") {
                  $(".color").each(function(){
                    if ($(this).prop('checked')) {
                        var color = $(this).attr('atr');
                        var colorid = $(this).val();
                        selectValue[colorid + '_' + 0] = new Array(0, colorid, color, 0, '', 0, 0, 0);
                    }
                });
            } else {
                $(".color").each(function(){
                    if ($(this).attr('checked')) {
                        var color = $(this).attr('atr');
                        var colorid = $(this).val();
                        $(".norms").each(function(){
                            if ($(this).prop('checked')) {
                                var norms = $(this).attr('atr');
                                var normsid = $(this).val();
                                console.log("normsid1:"+normsid+"colorid1:"+colorid);
                                selectValue[colorid + '_' + normsid] = new Array(0, colorid, color, normsid, norms, 0, 0, 0);
                            }
                        });
                    }
                });
            }
           for (var index in selectValue) {
                if (oldselect[index] != null && oldselect[index] != '') {
                    html += '<tr class="tnorms"><td width="130">' + oldselect[index][2] + '<input type="hidden" value="' + oldselect[index][1] + '"/></td>';
                    html += '<td width="130">' + oldselect[index][4] + '<input type="hidden" value="' + oldselect[index][3] + '"/></td>';
                    html += '<td width="130"><input type="text" class="px" style="width:60px;" value="' + oldselect[index][5] + '"/></td>';
                    html += '<td width="130"><input type="text" class="px" style="width:60px;" value="' + oldselect[index][6] + '"/></td>';
                    html += '<td width="130"><input type="text" class="px" style="width:60px;" value="' + oldselect[index][7] + '"/></td>';
                    html += '<td><input type="hidden" value="' + oldselect[index][0] + '"/></td></tr>';
                } else {
                    html += '<tr class="tnorms"><td width="130">' + selectValue[index][2] + '<input type="hidden" value="' + selectValue[index][1] + '"/></td>';
                    html += '<td width="130">' + selectValue[index][4] + '<input type="hidden" value="' + selectValue[index][3] + '"/></td>';
                    html += '<td width="130"><input type="text" class="px" style="width:60px;" value="' + selectValue[index][5] + '"/></td>';
                    html += '<td width="130"><input type="text" class="px" style="width:60px;" value="' + selectValue[index][6] + '"/></td>';
                    html += '<td width="130"><input type="text" class="px" style="width:60px;" value="' + selectValue[index][7] + '"/></td>';
                    html += '<td><input type="hidden" value="' + selectValue[index][0] + '"/></td></tr>';
                }
            }
            if (html != '') {
                $("#priceList").html(header + html);
            } else {
                $("#priceList").html('');
            }
        });
        $(".norms").click(function(){
            var selectValue = [];
            var html = '';
            var header = '<tr><th width="130">产品外观</th><th width="130">产品规格</th><th width="130">价格</th><th width="130">会员价</th><th width="130">数量</th></tr>';
            console.log("ColorHtml:"+$(".color").html());
            if ($(".color").html() == null || $(".color").html() =="") {
                  $(".norms").each(function(){
                    if ($(this).prop('checked')) {
                        var norms = $(this).attr('atr');
                        var normsid = $(this).val();
                        selectValue[0 + '_' + normsid] = new Array(0, 0, '', normsid, norms, 0, 0, 0);
                    }
                });
            } else {
                $(".color").each(function(){
                    if ($(this).attr('checked')) {
                        var color = $(this).attr('atr');
                        var colorid = $(this).val();
                        $(".norms").each(function(){
                            if ($(this).prop('checked')) {
                                var norms = $(this).attr('atr');
                                var normsid = $(this).val();
                                console.log("normsid2:"+normsid+"colorid2:"+colorid);
                                selectValue[colorid + '_' + normsid] = new Array(0, colorid, color, normsid, norms, 0, 0, 0);
                            }
                        });
                    }
                });
            }
            for (var index in selectValue) {
                if (oldselect[index] != null && oldselect[index] != '') {
                    html += '<tr class="tnorms"><td width="130">' + oldselect[index][2] + '<input type="hidden" value="' + oldselect[index][1] + '"/></td>';
                    html += '<td width="130">' + oldselect[index][4] + '<input type="hidden" value="' + oldselect[index][3] + '"/></td>';
                    html += '<td width="130"><input type="text" class="px" style="width:60px;" value="' + oldselect[index][5] + '"/></td>';
                    html += '<td width="130"><input type="text" class="px" style="width:60px;" value="' + oldselect[index][6] + '"/></td>';
                    html += '<td width="130"><input type="text" class="px" style="width:60px;" value="' + oldselect[index][7] + '"/></td>';
                    html += '<td><input type="hidden" value="' + oldselect[index][0] + '"/></td></tr>';
                } else {
                    html += '<tr class="tnorms"><td width="130">' + selectValue[index][2] + '<input type="hidden" value="' + selectValue[index][1] + '"/></td>';
                    html += '<td width="130">' + selectValue[index][4] + '<input type="hidden" value="' + selectValue[index][3] + '"/></td>';
                    html += '<td width="130"><input type="text" class="px" style="width:60px;" value="' + selectValue[index][5] + '"/></td>';
                    html += '<td width="130"><input type="text" class="px" style="width:60px;" value="' + selectValue[index][6] + '"/></td>';
                    html += '<td width="130"><input type="text" class="px" style="width:60px;" value="' + selectValue[index][7] + '"/></td>';
                    html += '<td><input type="hidden" value="' + selectValue[index][0] + '"/></td></tr>';
                }
            }
            if (html != '') {
                $("#priceList").html(header + html);
            } else {
                $("#priceList").html('');
            }
        });

        url= addUrl ;


    });

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