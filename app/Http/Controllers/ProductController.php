<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\Product;

class ProductController extends Controller
{
    function index()
    {
//         $products = [];
//         $productCache = Cache::get('products');
//         if ($productCache) {
//             $products = $productCache;
//         } else {
//             $products = Http::get('https://fakestoreapi.com/products')->json();
//             Cache::put('products', $products, 3600);
//             //return $products;
//         }
//
//
//         foreach($products as $product){
//             $product = [
//                 'title' => $product['title'],
//                 'price' => $product['price'],
//                 'description' => $product['description'],
//                 'category' => $product['category'],
//                 'image' => $product['image'],
//                 'rating' => $product['rating']['rate'],
//                 'rating_count' => $product['rating']['count'],
//             ];
//             Product::create($product);
//         }

        $products = Product::paginate(10);

        return $products;
    }

    function product($id)
    {
        $product = Product::find($id);
        return $product;
    }
    public function search(Request $request)
    {
        $sort_by = $request->sort_key;
        $name = $request->name;
        $min = $request->min;
        $max = $request->max;
        $products = Product::query();

        if ($name != null && $name != "") {
            $products->where(function ($query) use ($name) {
                foreach (explode(' ', trim($name)) as $word) {
                    $query->where('title', 'like', '%'.$word.'%');
                }
            });
        }

        if ($min != null && $min != "" && is_numeric($min)) {
            $products->where('price', '>=', $min);
        }

        if ($max != null && $max != "" && is_numeric($max)) {
            $products->where('price', '<=', $max);
        }

        switch ($sort_by) {
            case 'price_low_to_high':
                $products->orderBy('price', 'asc');
                break;

            case 'price_high_to_low':
                $products->orderBy('price', 'desc');
                break;

            case 'new_arrival':
                $products->orderBy('created_at', 'desc');
                break;
            case 'top_rated':
                $products->orderBy('rating_count', 'desc');
                break;
            default:
                $products->orderBy('created_at', 'desc');
                break;
        }

        return $products->paginate(10);
    }
}
