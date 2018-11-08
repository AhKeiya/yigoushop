$(document).ready(function () {
    $('#delall').click(function (e) {
        swal({
            title: '你确定要执行批量删除吗？',
            text: "确定删除后将无法恢复",
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
                    url: "goods_brand_del_all",
                    data: { goods_brand_id: id_array },
                    contentType: "application/x-www-form-urlencoded;charset=UTF-8",//post需要这个头
                    dataType: "json",
                    success: function (response) {
                        console.log(response);
                        location.href='/admin/goods/gbrandlst.html';
                    }
                });
            }
        })

    });
});