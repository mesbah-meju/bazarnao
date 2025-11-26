<?php

namespace App\Services;

use App\Models\ProductStock;
use App\Utility\ProductUtility;

class ProductStockService
{
    public function store(array $data, $product)
    {
        $collection = collect($data);
        $options = ProductUtility::get_attribute_options($collection);
        $variant = '';
            $qty = $collection['current_stock'];
            $price = $collection['unit_price'];
            unset($collection['current_stock']);
            $data = $collection->merge(compact('qty', 'price'))->toArray();
            ProductStock::create($data);
    }
}
