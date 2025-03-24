<?php

namespace App\Imports;

use App\Models\Product;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ProductImport implements ToModel, WithHeadingRow, WithMultipleSheets, SkipsEmptyRows
{

    public function sheets():array
    {
        return [
            0 => $this,
        ];
    }
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
       
        return new Product([
            'name' => $row['name'],
            'slug' => Product::generateUniqueSlug($row['name']),
            'category_id' => $row['category_id'],
            'stock' => $row['stock'],
            'selling_price' => $row['selling_price'],
            'is_active' => $row['is_active'],
            'barcode' => $row['barcode'],
            'brand_id' => $row['brand_id'],
            'thumbnail' => $row['thumbnail']
        ]);
    }

}