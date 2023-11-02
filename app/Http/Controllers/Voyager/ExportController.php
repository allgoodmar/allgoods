<?php

namespace App\Http\Controllers\Voyager;

use App\Product;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use TCG\Voyager\Facades\Voyager;

class ExportController extends VoyagerBaseController
{
    public function __construct()
    {
        $this->fileNamePrefix = Str::slug(config('app.name')) . '-products-';
        $this->fileDir = Storage::path('export');
        if (!is_dir($this->fileDir)) {
            mkdir($this->fileDir, 0755, true);
        }
    }

    public function index(Request $request)
    {
        $this->authorize('browse_admin');
        $test = '';
        return Voyager::view('voyager::export.index', compact('test'));
    }

    public function productsStore (Request $request)
    {
        $this->authorize('browse_admin');

        $fileName = $this->fileNamePrefix . date('Y-m-d-H-i-s'). '.xlsx';
        $filePath = $this->fileDir . '/' . $fileName;

        // remove old files
        $files = glob($this->fileDir . '/' . $this->fileNamePrefix . '*');
        rsort($files);
        $deleteFiles = array_slice($files, 4);
        foreach($deleteFiles as $deleteFile) {
            unlink($deleteFile);
        }

        $writer = WriterEntityFactory::createXLSXWriter();
        $writer->openToFile($filePath);

        // write headings
        $headingsRowArray = [
            'ID',
            'SKU (Модель)',
            'Категории',
            'Бренд',
            'Название',

            'Цена регулярная',
            '%',
            'Цена со скидкой',
            'Цена в рассрочку',

            'Остаток',

            'Характеристики',
            'Описание',
            'Обзор ссылка',

            'Статус',

            'Оценка',
            'Отзывы',
            'К-во отзывов',
            'Order',
            'Скрыть в списке товаров',
            'Ссылка',
            'Окончания скидки',
        ];
        $firstRowArray = [];
        for ($i = 1; $i <= count($headingsRowArray); $i++) {
            $firstRowArray[] = 'Column' . $i;
        }
        $firstRow = WriterEntityFactory::createRowFromArray($firstRowArray);
        $writer->addRow($firstRow);
        $headingsRow = WriterEntityFactory::createRowFromArray($headingsRowArray);
        $writer->addRow($headingsRow);

        Product::with(['brand', 'attributes', 'attributeValues', 'categories'])->chunk(200, function($products) use ($writer) {
            // write products
            foreach($products as $product) {
                $otherAttributesRaw = [];
                foreach($product->attributes as $attribute) {
                    $otherAttributesRaw[$attribute->id] = [
                        'name' => $attribute->name,
                        'values' => [],
                    ];
                }
                foreach($product->attributeValues as $attributeValue) {
                    if (!isset($otherAttributesRaw[$attributeValue->attribute_id])) {
                        continue;
                    }
                    $otherAttributesRaw[$attributeValue->attribute_id]['values'][$attributeValue->id] = $attributeValue->name;
                }
                $otherAttributes = [];
                foreach($otherAttributesRaw as $otherAttributeRaw) {
                    $otherAttributes[] = $otherAttributeRaw['name'] . ':' . implode(';', $otherAttributeRaw['values']);
                }
                $otherAttributes = implode('|', $otherAttributes);

                $categories = $product->categories->pluck('id')->toArray();

                $categories = implode('|', $categories);

                $linkToProduct = "https://allgood.uz/product/".$product->id."-".$product->slug;

                // simple product
                $cells = [
                    WriterEntityFactory::createCell($product->id),
                    WriterEntityFactory::createCell($product->sku),
                    WriterEntityFactory::createCell($categories),
                    WriterEntityFactory::createCell($product->brand->name ?? ''),
                    WriterEntityFactory::createCell($product->name),

                    WriterEntityFactory::createCell($product->price),
                    WriterEntityFactory::createCell(''),
                    WriterEntityFactory::createCell($product->sale_price),
                    WriterEntityFactory::createCell($product->installment_price),

                    WriterEntityFactory::createCell($product->in_stock),

                    // WriterEntityFactory::createCell(''),
                    WriterEntityFactory::createCell($otherAttributes),
                    WriterEntityFactory::createCell($product->body),
                    WriterEntityFactory::createCell(''),

                    WriterEntityFactory::createCell($product->status),

                    WriterEntityFactory::createCell(''),
                    WriterEntityFactory::createCell(''),
                    WriterEntityFactory::createCell(''),
                    WriterEntityFactory::createCell($product->order),
                    WriterEntityFactory::createCell($product->is_hidden),
                    WriterEntityFactory::createCell($linkToProduct),
                ];

                $singleRow = WriterEntityFactory::createRow($cells);
                $writer->addRow($singleRow);
            }
        });

        $writer->close();

        return redirect()->route('voyager.export.index')->with([
            'message'    => 'Файл для скачивания создан',
            'alert-type' => 'success',
        ]);
    }

    public function productsStoreFull (Request $request)
    {
        $this->authorize('browse_admin');

        $fileName = $this->fileNamePrefix . date('Y-m-d-H-i-s'). '.xlsx';
        $filePath = $this->fileDir . '/' . $fileName;

        // remove old files
        $files = glob($this->fileDir . '/' . $this->fileNamePrefix . '*');
        rsort($files);
        $deleteFiles = array_slice($files, 4);
        foreach($deleteFiles as $deleteFile) {
            unlink($deleteFile);
        }

        $writer = WriterEntityFactory::createXLSXWriter();
        $writer->openToFile($filePath);

        // write headings
        $headingsRow = WriterEntityFactory::createRowFromArray(['ID', 'Название', 'SKU (Модель)', 'Бренд', 'Курс USD', 'USD Цена в рассрочку', 'USD Цена (Специальная цена)', 'USD Цена со скидкой  (Специальная цена)', 'Цена в рассрочку', 'Цена (Специальная цена)', 'Цена со скидкой  (Специальная цена)', 'Остаток', 'Опции товара (оставить пустым если товар без опций)', 'Характеристики', 'Статус', 'Order']);
        $writer->addRow($headingsRow);

        // get products
        $products = Product::with(['brand', 'attributes', 'attributeValues'])->get();

        // write products
        foreach($products as $product) {
            $otherAttributesRaw = [];
            foreach($product->attributes as $attribute) {
                $otherAttributesRaw[$attribute->id] = [
                    'name' => $attribute->name,
                    'values' => [],
                ];
            }
            foreach($product->attributeValues as $attributeValue) {
                $otherAttributesRaw[$attributeValue->attribute_id]['values'][$attributeValue->id] = $attributeValue->name;
            }
            $otherAttributes = [];
            foreach($otherAttributesRaw as $otherAttributeRaw) {
                $otherAttributes[] = $otherAttributeRaw['name'] . ':' . implode(';', $otherAttributeRaw['values']);
            }
            $otherAttributes = implode('|', $otherAttributes);
        }

        $writer->close();

        return redirect()->route('voyager.export.index')->with([
            'message'    => 'Файл для скачивания создан',
            'alert-type' => 'success',
        ]);
    }

    public function productsDownload(Request $request)
    {
        $this->authorize('browse_admin');

        $files = glob($this->fileDir . '/' . $this->fileNamePrefix . '*');
        if ($files) {
            rsort($files);
            return response()->download($files[0]);
        }

        return redirect()->route('voyager.export.index')->with([
            'message'    => 'Файл не найден',
            'alert-type' => 'error',
        ]);
    }

    // MERCHANT
    
    public function productsStoreForMerchantDownload(Request $request)
    {
        $files = glob($this->fileDir . '/' . $this->fileNamePrefix . '*');
        if ($files) {   
            rsort($files);
            return response()->download($files[0]);
        }
    }

    public function productsStoreForMerchantDownloadSecond(Request $request)
    {
        $files = glob($this->fileDir . '/' . $this->fileNamePrefix . '*');
        if ($files) {   
            rsort($files);
            return response()->download($files[0]);
        }
    }

    public function productsStoreForMerchant (Request $request, $id)
    {
        $fileName = $this->fileNamePrefix . date('Y-m-d-H-i-s'). '.xlsx';
        $filePath = $this->fileDir . '/' . $fileName;

        // remove old files
        $files = glob($this->fileDir . '/' . $this->fileNamePrefix . '*');
        rsort($files);
        $deleteFiles = array_slice($files, 4);
        foreach($deleteFiles as $deleteFile) {
            unlink($deleteFile);
        }

        $writer = WriterEntityFactory::createXLSXWriter();
        $writer->openToFile($filePath);

        // write headings
        $headingsRowArray = [
            'ID',
            'SKU (Модель)',
            'Категории',
            'Бренд',
            'Название',

            'Цена регулярная',
            '%',
            'Цена со скидкой',
            'Цена в рассрочку',

            'Остаток',

            'Характеристики',
            'Описание',
            'Обзор ссылка',

            'Статус',

            'Оценка',
            'Отзывы',
            'К-во отзывов',
            'Order',
            'Скрыть в списке товаров',
        ];
        $firstRowArray = [];
        for ($i = 1; $i <= count($headingsRowArray); $i++) {
            $firstRowArray[] = 'Column' . $i;
        }
        $firstRow = WriterEntityFactory::createRowFromArray($firstRowArray);
        $writer->addRow($firstRow);
        $headingsRow = WriterEntityFactory::createRowFromArray($headingsRowArray);
        $writer->addRow($headingsRow);

        Product::where('seller_id', $id)->with(['brand', 'attributes', 'attributeValues', 'categories'])->chunk(200, function($products) use ($writer) {
            // write products
            foreach($products as $product) {
                $otherAttributesRaw = [];
                foreach($product->attributes as $attribute) {
                    $otherAttributesRaw[$attribute->id] = [
                        'name' => $attribute->name,
                        'values' => [],
                    ];
                }
                foreach($product->attributeValues as $attributeValue) {
                    if (!isset($otherAttributesRaw[$attributeValue->attribute_id])) {
                        continue;
                    }
                    $otherAttributesRaw[$attributeValue->attribute_id]['values'][$attributeValue->id] = $attributeValue->name;
                }
                $otherAttributes = [];
                foreach($otherAttributesRaw as $otherAttributeRaw) {
                    $otherAttributes[] = $otherAttributeRaw['name'] . ':' . implode(';', $otherAttributeRaw['values']);
                }
                $otherAttributes = implode('|', $otherAttributes);

                $categories = $product->categories->pluck('id')->toArray();

                $categories = implode('|', $categories);

                // simple product
                $cells = [
                    WriterEntityFactory::createCell($product->id),
                    WriterEntityFactory::createCell($product->sku),
                    WriterEntityFactory::createCell($categories),
                    WriterEntityFactory::createCell($product->brand->name ?? ''),
                    WriterEntityFactory::createCell($product->name),

                    WriterEntityFactory::createCell($product->price),
                    WriterEntityFactory::createCell(''),
                    WriterEntityFactory::createCell($product->sale_price),
                    WriterEntityFactory::createCell($product->installment_price),

                    WriterEntityFactory::createCell($product->in_stock),

                    // WriterEntityFactory::createCell(''),
                    WriterEntityFactory::createCell($otherAttributes),
                    WriterEntityFactory::createCell($product->body),
                    WriterEntityFactory::createCell(''),

                    WriterEntityFactory::createCell($product->status),

                    WriterEntityFactory::createCell(''),
                    WriterEntityFactory::createCell(''),
                    WriterEntityFactory::createCell(''),
                    WriterEntityFactory::createCell($product->order),
                    WriterEntityFactory::createCell($product->is_hidden),
                ];

                $singleRow = WriterEntityFactory::createRow($cells);
                $writer->addRow($singleRow);
            }
        });

        $writer->close();

        $files = glob($this->fileDir . '/' . $this->fileNamePrefix . '*');
        if ($files) {
            rsort($files);
            return response()->download($files[0]);
        }

        return response()->json([
            'status' => 'true',
            'description' => 'Successfully downloaded'
        ]);
    }
}
