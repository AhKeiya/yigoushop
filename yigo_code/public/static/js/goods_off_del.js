$(document).ready(function () {
    $("#select_id").change(function(){
        var goods_off_name =  $(this).val();//取值下拉值
        var GetThis = $(this);
        $('#del').click(function (e) { 
            swal({
                title: '您确定要删除吗?',
                text: "如果数据量大会稍微慢!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '确定'
              }).then(function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        type: "post",
                        url: "goods_off_del",
                        data: {goods_off_name:goods_off_name},
                        dataType: "json",
                        success: function (data) {
                            console.log(data);
                            if(data.Status == 200){
                                swal(
                                    '恭喜!',
                                    '删除成功!',
                                    'success'
                                  )
                                  $("#select_id option[value='"+goods_off_name+"']").remove();//删除Select中value值的标签
                            }else{
                                swal(
                                    '删除失败',
                                    'error'
                                )
                            }
                        }
                    });
                }
              })
        });
     });
});