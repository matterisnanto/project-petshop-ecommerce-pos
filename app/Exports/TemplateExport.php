<?php

namespace App\Exports;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Supplier;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class TemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new ProductsExport(),
            new CategoriesExport(),
            new BrandsExport(),
            new SuppliersExport()
        ];
    }
}

class ProductsExport implements FromCollection, WithHeadings, WithTitle, WithEvents
{
    public function collection()
    {
        return collect([]); // Empty collection for template
    }

    public function headings(): array
    {
        return [
            'name',
            'slug',
            'barcode',
            'thumbnail',
            'description',
            'weight',
            'purchase_price',
            'selling_price',
            'is_active',
            'is_popular',
            'stock',
            'category_id',
            'brand_id',
            'supplier_id'
        ];
    }
    public function title(): string
    {
        return 'Products';
    }

    public function registerEvents(): array
    {
        return [AfterSheet::class => function (AfterSheet $event) {
            // Get the sheet object
            $sheet = $event->sheet->getDelegate();

            // Get category IDs from the database
            $categoryIds = Category::pluck('id')->toArray();
            $brandIds = Brand::pluck('id')->toArray();
            $supplierIds = Supplier::pluck('id')->toArray();

            // Convert category IDs to a comma-separated string
            $categoryIdsString = implode(',', $categoryIds);
            $brandIdsString = implode(',', $brandIds);
            $supplierIdsString = implode(',', $supplierIds);

            // Set data validation for column B (category_id)
            $validation = $sheet->getDataValidation('L2:L1000'); // Adjust the range as needed
            $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $validation->setAllowBlank(false);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setErrorTitle('Input error');
            $validation->setError('Value is not in list.');
            $validation->setPromptTitle('Pick from list');
            $validation->setPrompt('Please pick a value from the drop-down list.');
            $validation->setFormula1('"' . $categoryIdsString . '"'); // Set the dropdown options
            // Set data validation for column B (category_id)
            $validation = $sheet->getDataValidation('M2:M1000'); // Adjust the range as needed
            $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $validation->setAllowBlank(false);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setErrorTitle('Input error');
            $validation->setError('Value is not in list.');
            $validation->setPromptTitle('Pick from list');
            $validation->setPrompt('Please pick a value from the drop-down list.');
            $validation->setFormula1('"' . $brandIdsString . '"'); // Set the dropdown options
            // Set data validation for column B (category_id)
            $validation = $sheet->getDataValidation('N2:N1000'); // Adjust the range as needed
            $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $validation->setAllowBlank(false);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setErrorTitle('Input error');
            $validation->setError('Value is not in list.');
            $validation->setPromptTitle('Pick from list');
            $validation->setPrompt('Please pick a value from the drop-down list.');
            $validation->setFormula1('"' . $supplierIdsString . '"'); // Set the dropdown options

            // Set data validation for column E (is_active)
            $validation = $sheet->getDataValidation('I2:I1000'); // Adjust the range as needed
            $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $validation->setAllowBlank(false);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setErrorTitle('Input error');
            $validation->setError('Value is not in list.');
            $validation->setPromptTitle('Pick from list');
            $validation->setPrompt('Please pick a value from the drop-down list.');
            $validation->setFormula1('"TRUE,FALSE"'); // Set the dropdown options

            $validation = $sheet->getDataValidation('J2:J1000'); // Adjust the range as needed
            $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $validation->setAllowBlank(false);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setErrorTitle('Input error');
            $validation->setError('Value is not in list.');
            $validation->setPromptTitle('Pick from list');
            $validation->setPrompt('Please pick a value from the drop-down list.');
            $validation->setFormula1('"TRUE,FALSE"'); // Set the dropdown options
        },];
    }
}

class CategoriesExport implements FromCollection, WithHeadings, WithTitle
{
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
        return 'Categories';
    }
}

class BrandsExport implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return Brand::select('id', 'name')->get();
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
        return 'Brands';
    }
}

class SuppliersExport implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return Supplier::select('id', 'name')->get();
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
        return 'Suppliers';
    }
}
