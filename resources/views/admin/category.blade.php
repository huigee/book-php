@extends('admin.master')

@section('content')
    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 产品管理 <span class="c-gray en">&gt;</span> 分类管理 <a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
    <div class="pd-20">
        <div class="cl pd-5 bg-1 bk-gray mt-20">
            <span class="l">
            <a href="javascript:;" onclick="category_add('添加分类','/admin/category_add')" class="btn btn-primary radius"><i class="icon-plus"></i> 添加分类</a></span>
            <span class="r">共有数据：<strong>{{count($categorys)}}</strong> 条</span>
        </div>
        <table class="table table-border table-bordered table-hover table-bg table-sort">
            <thead>
            <tr class="text-c">
                <th width="80">ID</th>
                <th width="100">名称</th>
                <th width="40">序号</th>
                <th width="90">父类别</th>
                <th width="90">预览图</th>
                <th width="100">操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($categorys as $category)
            <tr class="text-c">
                <td>{{$category->cat_id}}</td>
                <td>{{$category->name}}</td>
                <td>{{$category->category_no}}</td>
                <td>
                    @if($category->parent_id != null)
                        {{$category->parent->name}}
                    @endif
                </td>
                <td>
                    @if($category->preview != null)
                        <img src="{{$category->preview}}" style="border: 1px solid #B8B9B9; width: 100px; height: 100px;" />
                    @endif
                </td>
                <td class="f-14 user-manage">
                    <a title="编辑" href="javascript:;" onclick="category_edit('编辑类别','/admin/category_edit?cat_id={{$category->cat_id}}')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6df;</i></a>
                    <a title="删除" href="javascript:;" onclick="category_del('{{$category->name}}','{{$category->cat_id}}')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e2;</i></a>

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
        function category_add(title,url){
            var index = layer.open({
                type: 2,
                title: title,
                content: url
            });
            layer.full(index);
        }

        function category_edit(title,url) {
            var index = layer.open({
                type: 2,
                title: title,
                content: url
            });
            layer.full(index);
        }

        function category_del(name,id) {
            layer.confirm('确认要删除【'+name+'】吗？',function (index) {
               $.ajax({
                   type:"post",
                   url:"/admin/service/category/delete",
                   dataType:"json",
                   data:{cat_id:id,_token:"{{csrf_token()}}"},
                   success:function (data) {
                       if (data == null)
                       {
                           layer.msg('服务端错误',{icon:2,time:2000});
                           return;
                       }
                       if (data.status != 0)
                       {
                           layer.msg(data.message, {icon: 2, time: 2000});
                           return;
                       }
                       layer.msg(data.message, {icon:1, time:2000});
                       location.replace(location.href);
                   },
                   error:function (xhr,status,error) {
                       console.log(xhr);
                       console.log(status);
                       console.log(error);
                       layer.msg('ajax error', {icon:2,time:2000});
                   },
                   beforeSend:function (xhr) {
                       layer.msg(0, {shade:false});
                   }
               }); 
            });
        }
    </script>
@endsection