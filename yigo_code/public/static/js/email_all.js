$(document).ready(function () {
    $('#delall').click(function (e) {
        swal({
            title: '你确认要批量发送邮件吗？',
            text: "发送之后请不要刷新网页,直到发送完成为止!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '确定'
        }).then(function (isConfirm) {
            if (isConfirm) {
                var id_array = new Array();
                $('input[name="id"]:checked').each(function () {
                    id_array.push($(this).val());//向数组中添加元素    push() 方法可以给数组末尾添加一个或多个数组项。
                });
                $.ajax({
                    type: "post",
                    url: "marketing_mail_all",
                    data: {id:id_array},
                    contentType: "application/x-www-form-urlencoded;charset=UTF-8",//post需要这个头
                    dataType: "json",
                    success: function (i) {
                       if(i.Status == 200){
                        swal(
                            '恭喜',
                            '邮件已经全部发送完毕',
                            'success'
                          )
                       }else{
                        swal(
                            '抱歉',
                            '邮件发送失败',
                            'error'
                          )
                       }
                    }
                });
            }
        })

    });
});