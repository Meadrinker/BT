<?php


use App\Http\Controllers\Controller;
use App\Http\Presenters\Admin\Product\ProductPresenter;
use App\Http\Requests\Admin\ProductAddRequest;
use App\Http\Requests\Admin\ProductEditRequest;
use App\Http\Requests\Admin\ProductImportRequest;
use App\Services\Producer\Exceptions\ProducerNotFoundException;
use App\Services\Product\Spec\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller {

    private ProductService $productService;

    public function __construct(ProductService $productService) {
        $this->productService = $productService;
    }

    public function getProduct(int $productId) {
        return \App\Http\Controllers\Admin\Product\apiResponse(200, 200, new ProductPresenter($this->productService->getById($productId)));
    }

    public function add(ProductAddRequest $request) {
        try {
            $this->productService->addProduct($request->getData());
            return \App\Http\Controllers\Admin\Product\apiResponse(200, 200);
        } catch (\Exception $exception) {
            Log::error($exception);
            return \App\Http\Controllers\Admin\Product\apiErrorResponse(500, 500);
        }
    }

    public function edit(int $productId, ProductEditRequest $request) {
        try {
            $this->productService->editProduct($productId, $request->getData());
            return \App\Http\Controllers\Admin\Product\apiResponse(200, 200);
        } catch (ProducerNotFoundException $exception) {
            return \App\Http\Controllers\Admin\Product\apiResponse(200, 404);
        } catch (\Exception $exception) {
            Log::error($exception);
            return \App\Http\Controllers\Admin\Product\apiErrorResponse(500, 500);
        }
    }

    public function importToProduct(ProductImportRequest $request) {
        if ($this->productService->createProductJobTask($request->getData())) {
            return \App\Http\Controllers\Admin\Product\apiResponse(200, 200);
        }
        return \App\Http\Controllers\Admin\Product\apiErrorResponse(422, 422);
    }

}
