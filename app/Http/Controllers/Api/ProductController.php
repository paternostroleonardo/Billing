<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function search()
    {
        $results = Product::orderBy('item_code')
            ->when(request('q'), function ($query) {
                $query->where('item_code', 'like', '%' . request('q') . '%')
                    ->orWhere('description', 'like', '%' . request('q') . '%');
            })
            ->limit(6)
            ->get();

        return response()
            ->json(['results' => $results]);
    }
}
