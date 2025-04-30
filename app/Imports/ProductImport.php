<?php

namespace App\Imports;

use App\Models\Brand;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ProductImport implements ToModel, WithHeadingRow, WithMultipleSheets, SkipsEmptyRows
{

    public function sheets(): array
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
        // Validasi relasi
        $category = Category::find($row['category_id']);
        $brand = Brand::find($row['brand_id']);
        $supplier = Supplier::find($row['supplier_id']);

        if (!$category || !$brand || !$supplier) {
            throw new \Exception("Invalid category, brand, or supplier ID");
        }

        // Proses thumbnail
        $thumbnailPath = null;
        if (!empty($row['thumbnail'])) {
            $thumbnailPath = $this->processThumbnail($row['thumbnail']);
        }

        return new Product([
            'name' => $row['name'],
            'slug' => Product::generateUniqueSlug($row['name']),
            'barcode' => $row['barcode'] ?? null,
            'thumbnail' => $thumbnailPath,
            'description' => $row['description'] ?? null,
            'weight' => $row['weight'] ?? 0,
            'purchase_price' => $row['purchase_price'] ?? 0,
            'selling_price' => $row['selling_price'],
            'is_active' => strtoupper($row['is_active']) === 'TRUE' ? 1 : 0,
            'is_popular' => strtoupper($row['is_popular']) === 'TRUE' ? 1 : 0,
            'stock' => $row['stock'],
            'category_id' => $row['category_id'],
            'brand_id' => $row['brand_id'],
            'supplier_id' => $row['supplier_id']
        ]);
    }

    /**
     * Process thumbnail from Excel (could be URL or local path)
     */
    protected function processThumbnail($thumbnailInput)
    {
        if (empty($thumbnailInput)) {
            return null;
        }

        $storagePath = 'product-thumbnails/'; // Sesuaikan dengan path yang diinginkan
        $filename = 'product_' . time() . '_' . Str::random(10);

        // Jika input adalah URL
        if (filter_var($thumbnailInput, FILTER_VALIDATE_URL)) {
            try {
                $contents = file_get_contents($thumbnailInput);
                $extension = pathinfo($thumbnailInput, PATHINFO_EXTENSION) ?: 'jpg';
                $fullPath = $storagePath . $filename . '.' . $extension;

                Storage::put('public/' . $fullPath, $contents);
                return $fullPath;
            } catch (\Exception $e) {
                return null;
            }
        }

        // Jika input adalah path lokal (misal: 'temp/product.jpg')
        $localPath = public_path($thumbnailInput);
        if (file_exists($localPath)) {
            $extension = pathinfo($localPath, PATHINFO_EXTENSION);
            $fullPath = $storagePath . $filename . '.' . $extension;

            Storage::put('public/' . $fullPath, file_get_contents($localPath));
            return $fullPath;
        }

        return null;
    }


    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'is_active' => 'required|in:TRUE,FALSE,true,false,1,0',
            'is_popular' => 'required|in:TRUE,FALSE,true,false,1,0',
            'barcode' => 'nullable|string|max:255|unique:products,barcode',
            'thumbnail' => 'nullable|string', // Bisa berupa URL atau path
        ];
    }
}
