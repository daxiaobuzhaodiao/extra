<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Exceptions\InvalidRequestException;

class ProductsController extends Controller
{
	public function index(Request $request)
	{
		// 创建一个查询构造器
		$builder = Product::query()->where('on_sale', true);
		// 判断是否提交 search 
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

	public function show(Product $product)
	{
		if(!$product->on_sale){
			throw new InvalidRequestException('商品未上架');
		}
		$favored = false;
		if($user = auth()->user()){
			$favored = boolval($user->favoriteProducts()->find($product->id));
		}
		return view('products.show', ['product' => $product, 'favored' => $favored]); 
	}

	// 收藏
	public function favor(Request $request, Product $product)
	{
		$user = $request->user();
		// 首先判断用户是否已经收藏了商品
		if($user->favoriteProducts()->find($product->id)){
			return [];
		}
		$user->favoriteProducts()->attach($product);// attach() 方法的参数可以是模型的 id，也可以是模型对象本身
		return [];
	}

	// 取消收藏
	public function disfavor(Request $request, Product $product)
	{
		$user = $request->user();
		$user->favoriteProducts()->detach($product); // detach() 和  attach() 参数一致
		return [];
	}

	// 收藏列表
	public function favorites()
	{
		$products = auth()->user()->favoriteProducts()->paginate(16);
		return view('products.favorites', ['products' => $products]);
	}
}
