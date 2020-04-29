@extends('admin.master')

@section('content')
    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 产品管理 <span class="c-gray en">&gt;</span> 产品管理 <a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
    <div class="pd-20">
        <div class="cl pd-5 bg-1 bk-gray mt-20">
            <span class="l">
            <a href="javascript:;" onclick="product_add('添加产品','/admin/product_add')" class="btn btn-primary radius"><i class="icon-plus"></i> 添加产品</a></span>
            <span class="r">共有数据：<strong>{{count($products)}}</strong> 条</span>
        </div>
        <table class="table table-border table-bordered table-hover table-bg table-sort">
            <thead>
            <tr class="text-c">
                <th width="80">ID</th>
                <th width="100">名称</th>
                <th width="40">简介</th>
                <th width="90">价格</th>
                <th width="90">预览图</th>
                <th width="90">分类</th>
                <th width="100">操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($products as $product)
                <tr class="text-c">
                    <td>{{$product->product_id}}</td>
                    <td>{{$product->name}}</td>
                    <td>{{$product->summary}}</td>
                    <td>
                        {{$product->price}}
                    </td>
                    <td>
                        @if($product->preview != null)
                            <img src="{{$product->preview}}" style="border: 1px solid #B8B9B9; width: 100px; height: 100px;" />
                        @endif
                    </td>
                    <td>
                        {{$product->category_name}}
                    </td>
                    <td class="f-14 user-manage">
                        <a title="详情" href="javascript:;" onclick="product_info('产品详情','/admin/product_info?product_id={{$product->product_id}}')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe695;</i></a>
                        <a title="编辑" href="javascript:;" onclick="product_edit('编辑详情','/admin/product_edit?product_id={{$product->product_id}}')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6df;</i></a>
                        <a title="删除" href="javascript:;" onclick="product_del('{{$product->name}}','{{$product->product_id}}')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e2;</i></a>

                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div id="pageNav" class="pageNav"></div>
    </div>
@endsection

@section('my-js')
    <script>
        function product_info(title,url) {
            var index = layer.open({
                type: 2,
                title: title,
                content: url
            });
            layer.full(index);
        }

        function product_edit(title, url) {
            var index = layer.open({
                type: 2,
                title: title,
                content: url
            });
            layer.full(index);
        }

        function product_add(title, url) {
            var index = layer.open({
                type: 2,
                title: title,
                content: url
            });
            layer.full(index);
        }

        function product_del(name,id) {
            layer.confirm('确认要删除【'+name+'】吗？',function (index) {
                $.ajax({
                    type: "post",
                    url: "/admin/service/product/delete",
                    data: {product_id:id, _token:"{{csrf_token()}}"},
                    success:function (data) {
                        console.log(data);
                        if (data == null){
                            layer.msg('服务端错误', {icon:2, time:2000});
                            return;
                        }
                        if (data.status != 0){
                            layer.msg(data.message, {icon: 2, time: 2000});
                            return;
                        }
                        layer.msg(data.message, {icon:1,time:2000});
                        location.replace(location.href);
                    },
                    error: function (xhr, status, error) {
                        console.log(xhr);
                        console.log(status);
                        console.log(error);
                        layer.msg('ajax error', {icon:2,time:2000});
                    },
                    beforeSend:function (xhr) {
                        layer.msg(0, {shade:false});
                    }
                });
            })
        }
    </script>
@endsection