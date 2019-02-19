<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductsController extends Controller
{
	public function index(Request $request)
	{
		// 创建一个查询构造器
		$builder = Product::query()->where('on_sale', true);
		// 判断是否有提交 search 参数
		if($search = $request->input('search', '')){
			$like = "%{$search}%";
			// 模糊搜索商品标题，商品详情，sku标题，sku详情
			$builder->where(function ($query) use ($like) {
				$query->where('title', 'like', $like)
					->orWhere('description', 'like', $like)
					->orWhereHas('skus', function($query) use ($like){
						$query->where('title', 'like', $like)
							->orWhere('description', 'like', $like);
					});
			});
		}

		// 判断是否有提交 order 参数
		if($order = $request->input('order', '')){
			// 判断 desc 或者 asc 结尾
			if(preg_match('/^(.+)_(asc|desc)$/', $order, $m)){
				// dump($m);
				if(in_array($m[1], ['price', 'sold_count', 'rating'])){
					// 根据传入的排序值来构造排序参数
					$builder->orderBy($m[1], $m[2]);
				}
			}
		}
		// 最后带上分页
		$products = $builder->paginate(16);
		return view('products.index', [
			'products' => $products,
			// 前端页面点击搜索刷新页面后 防止 input 框变成空白，所以传给前端，通过js代码输出到input框中
			'filters' => [
				'search' => $search,
				'order' => $order
			]
		]);
		
	}
}
