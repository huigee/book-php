<?php

namespace App\Http\Controllers\Admin;

use App\Entity\Category;
use App\Http\Controllers\Controller;
use App\Models\M3Result;
use DemeterChain\C;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function toCategory()
    {
        $categorys = Category::all();
        foreach ($categorys as $category)
        {
            if ($category->parent_id != null && $category->parent_id != '')
            {
                $category->parent = Category::find($category->parent_id);
            }
        }

        return view('admin.category')->with('categorys', $categorys);
    }

    public function toCategoryAdd()
    {
        $categorys = Category::whereNull('parent_id')->get();
        return view('admin.category_add')->with('categorys', $categorys);
    }

    public function toCategoryEdit(Request $request)
    {
        $cat_id = $request->input('cat_id', '');
        $category = Category::find($cat_id);
        $categories = Category::whereNull('parent_id')->get();

        return view('admin.category_edit')->with('category', $category)
                                            ->with('categories', $categories);
    }


    public function categoryAdd(Request $request)
    {
        $name = $request->input('name','');
        $category_no = $request->input('category_no','');
        $preview = $request->input('preview','');
        $parent_id = $request->input('parent_id', '');

        $category = new Category();
        $category->name = $name;
        $category->category_no = $category_no;
        $category->preview = $preview;
        if ($parent_id != '')
        {
            $category->parent_id = $parent_id;
        }
        $category->save();

        $m3_result = new M3Result();
        $m3_result->status = 0;
        $m3_result->message = '添加成功';

        return $m3_result->toJson();
    }

    public function categoryEdit(Request $request)
    {
        $cat_id = $request->input('cat_id', '');
        $name = $request->input('name', '');
        $category_no = $request->input('category_no', '');
        $parent_id = $request->input('parent_id', '');
        $preview = $request->input('preview', '');

        $category = Category::find($cat_id);

        $category->name = $name;
        $category->category_no = $category_no;
        if ($category->parent_id != '')
        {
            $category->parent_id = $parent_id;
        }
        $category->preview = $preview;
        $category->save();

        $m3_result = new M3Result();
        $m3_result->status = 0;
        $m3_result->message = '编辑成功';
        return $m3_result->toJson();
    }


    public function categoryDel(Request $request)
    {
        $cat_id = $request->input('cat_id', '');
        Category::find($cat_id)->delete();

        $m3_result = new M3Result();

        $m3_result->status = 0;
        $m3_result->message = '删除成功';
        return $m3_result->toJson();
    }
}
