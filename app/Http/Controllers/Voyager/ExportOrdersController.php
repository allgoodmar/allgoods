<?php

namespace App\Http\Controllers\Voyager;

use App\Order;
use App\OrderItem;
use App\Product;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use TCG\Voyager\Facades\Voyager;

class ExportOrdersController extends VoyagerBaseController
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
        return Voyager::view('voyager::exportorder.index', compact('test'));
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
            'created_at',
            'ID',
            'order_id',
            'name',
            'product_id',
            'quantity',
            'price',
            'total',
            'sku',
            'payment_method_id',
            'phone_number',
            'user_name',
            'user_address',
            'user_agent',
        ];
        $firstRowArray = [];
        for ($i = 1; $i <= count($headingsRowArray); $i++) {
            $firstRowArray[] = 'Column ' . $i;
        }
        $firstRow = WriterEntityFactory::createRowFromArray($firstRowArray);
        $writer->addRow($firstRow);
        $headingsRow = WriterEntityFactory::createRowFromArray($headingsRowArray);
        $writer->addRow($headingsRow);

        OrderItem::select(
            DB::raw("order_items.*"),
            DB::raw("products.sku"),
            DB::raw("products.barcode"),
            DB::raw("products.import_partner_id"),
            DB::raw("payment_methods.name as payment_method_name"),
            DB::raw("orders.phone_number"),
            DB::raw("orders.name as user_name"),
            DB::raw("orders.address_line_1 as user_address"),
            DB::raw("orders.user_agent")
        )->join("products", "products.id", "=", "order_items.product_id")
        ->join("orders", "orders.id", "=", "order_items.order_id")
        ->join("payment_methods", "payment_methods.id", "=", "orders.payment_method_id")
        ->chunk(10000, function($orders) use ($writer) {
            // write products
            foreach($orders as $item) {

                $created_at = date("d-m-Y", strtotime($item->created_at));
                // simple product
                $cells = [
                    WriterEntityFactory::createCell($created_at),
                    WriterEntityFactory::createCell($item->id),
                    WriterEntityFactory::createCell($item->order_id),
                    WriterEntityFactory::createCell($item->name),
                    WriterEntityFactory::createCell($item->product_id),
                    WriterEntityFactory::createCell($item->quantity),
                    WriterEntityFactory::createCell((int)$item->price),
                    WriterEntityFactory::createCell((int)$item->total),
                    WriterEntityFactory::createCell($item->sku ?? ""),
                    WriterEntityFactory::createCell($item->payment_method_name ?? ""),
                    WriterEntityFactory::createCell($item->phone_number ?? ""),
                    WriterEntityFactory::createCell($item->user_name ?? ""),
                    WriterEntityFactory::createCell($item->user_address ?? ""),
                    WriterEntityFactory::createCell($item->user_agent ?? ""),
                ];

                $singleRow = WriterEntityFactory::createRow($cells);
                $writer->addRow($singleRow);
            }
        });

        $writer->close();

        return redirect()->route('voyager.exportorders.index')->with([
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

        return redirect()->route('voyager.exportorders.index')->with([
            'message'    => 'Файл для скачивания создан',
            'alert-type' => 'success',
        ]);
    }

    public function productsDownload(Request $request)
    {
        // $this->authorize('browse_admin');

        $files = glob($this->fileDir . '/' . $this->fileNamePrefix . '*');
        if ($files) {
            rsort($files);
            return response()->download($files[0]);
        }

        return redirect()->route('voyager.exportorders.index')->with([
            'message'    => 'Файл не найден',
            'alert-type' => 'error',
        ]);
    }
}
