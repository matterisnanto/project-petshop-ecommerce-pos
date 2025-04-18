<?php

namespace App\Exports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class TemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new ProductsExport(),
            new CategoriesExport()
        ];
    }
}

class ProductsExport implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return collect([]);
    }

    public function headings(): array
    {
        return [
            'name',            // Nama produk
            'category_id',     // ID kategori
            'barcode',         // Kode barcode produk
            'stock',           // Jumlah stok
            'purchase_price',  // Harga beli
            'selling_price',   // Harga jual
            'is_active',       // Status aktif atau tidak (1/0)
            'thumbnail',       // Gambar utama produk
        ];
    }
    public function title(): string
    {
        return 'Products';
    }
}

class CategoriesExport implements FromCollection, WithHeadings, WithTitle
{
    
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Category::select('id', 'name')->get();
    }

    public function headings(): array
    {
        return [
            'id',
            'name',
        ];
    }

    public function title(): string
    {
        return 'Category';
    }
}