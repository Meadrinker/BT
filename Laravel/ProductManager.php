<?php


use App\Models\JobTask;
use App\Models\Product;
use App\Models\ProductDictionary;
use App\Services\Admin\Command\Product\Validator;
use App\Services\Admin\Command\Product\ValidatorDictionary;
use App\Services\FileStorage\Spec\FileStorageService;
use App\Services\JobTasks\Spec\JobTaskService;
use App\Services\Product\Dto\AddProductDictionaryDto;
use App\Services\Product\Dto\AddProductDto;
use App\Services\Product\Dto\EditProductDictionaryDto;
use App\Services\Product\Dto\EditProductDto;
use App\Services\Product\Dto\ImportProductDictionaryDto;
use App\Services\Product\Dto\ImportProductDto;
use App\Services\Product\Exceptions\ProductDictionaryAlreadyExistsException;
use App\Services\Product\Exceptions\ProductDictionaryNotFoundException;
use App\Services\Product\Exceptions\ProductNotFoundException;
use App\Services\Product\Spec\ProductService;
use Illuminate\Database\QueryException;

class ProductManager implements ProductService {

    private JobTaskService $jobTaskService;
    private FileStorageService $fileStorageService;

    public function __construct(JobTaskService $jobTaskService, FileStorageService $fileStorageService) {
        $this->jobTaskService = $jobTaskService;
        $this->fileStorageService = $fileStorageService;
    }

    public function getById(int $id) : ?Product {
        return Product::where('id', '=', $id)
            ->first();
    }

    public function getByCode(string $code) {
        $products = Product::where('code', '=', $code)->get();
        if (count($products) != 0) {
            return $products;
        }
        $productsDictionary = $this->getDictionaryByCode($code);
        if ($productsDictionary !== null) {
            $productsIds = [];
            foreach ($productsDictionary as $productDictionary) {
                $productsIds[] = $productDictionary->product_id;
            }
            return Product::whereIn('id', $productsIds)->get();
        }
        return [];
    }

    public function getByCodeAndProducer(string $code, int $producerId) : ?Product {
        $product = Product::where('code', '=', $code)
            ->where('producer_id', '=', $producerId)
            ->first();
        if ($product !== null) {
            return $product;
        }
        $productDictionary = $this->getDictionaryByCodeAndProducer($producerId, $code);
        if ($productDictionary !== null) {
            return $productDictionary->product;
        }
        return null;
    }

    public function addProduct(AddProductDto $productDto) {
        $product = new Product();
        $product->code = $this->clearProductCode($productDto->getCode());
        $product->producer_id = $productDto->getProducerId();
        $product->weight = $productDto->getWeight();
        $product->description = $productDto->getDescription();
        $product->ean = $productDto->getEan();
        $product->save();
    }

    public function editProduct(int $id, EditProductDto $productDto) {
        $product = $this->getById($id);
        if ($product === null) {
            throw new ProductNotFoundException('Product not found');
        }
        $product->code = $this->clearProductCode($productDto->getCode());
        $product->weight = $productDto->getWeight();
        $product->description = $productDto->getDescription();
        $product->ean = $productDto->getEan();
        $product->save();
    }

    public function createProductJobTask(ImportProductDto $productDto) {
        $file = fopen($this->fileStorageService->shareFilePath($productDto->getFilePath()), 'r');
        if ($file === false) {
            return false;
        }
        $this->jobTaskService->addTask($productDto->getUserId(), $productDto->getFilePath(),
            $productDto->getOriginalFileName(), JobTask::ADMIN_PRODUCTS_TYPE, $productDto->getLang(),
            $productDto->getImportJobTaskId());
        return true;
    }

    public function createProductDictionaryJobTask(ImportProductDictionaryDto $productDto) {
        $file = fopen($this->fileStorageService->shareFilePath($productDto->getFilePath()), 'r');
        if ($file === false) {
            return false;
        }
        $this->jobTaskService->addTask($productDto->getUserId(), $productDto->getFilePath(),
            $productDto->getOriginalFileName(), JobTask::ADMIN_PRODUCTS_DICTIONARY_TYPE, $productDto->getLang(),
            $productDto->getImportJobTaskId());
        return true;
    }

    public function getDictionaryById(int $productDictionaryId): ?ProductDictionary {
        return ProductDictionary::where('id', '=', $productDictionaryId)->first();
    }

    public function getDictionaryByCode(string $code) {
        return ProductDictionary::where('code', '=', $code)->get();
    }

    public function getDictionaryByCodeAndProducer(int $producerId, string $code) : ?ProductDictionary {
        return ProductDictionary::join('product', 'product_dictionary.product_id', '=', 'product.id')
            ->where('product_dictionary.code', '=', $code)
            ->where('product.producer_id', '=', $producerId)
            ->first();
    }

    public function addDictionary(AddProductDictionaryDto $dto) {
        $product = Product::where('producer_id', '=', $dto->getProducerId())
            ->where('code', '=', $dto->getCode())->first();
        if ($product === null) {
            throw new ProductNotFoundException();
        }

        $productDictionary = new ProductDictionary();
        $productDictionary->product_id = $product->id;
        $productDictionary->code = $dto->getDictionaryCode();

        try {
            $productDictionary->save();
        } catch (QueryException $exception) {
            $errorCode = $exception->errorInfo[1];
            if ($errorCode == 1062){
                throw new ProductDictionaryAlreadyExistsException();
            }
            throw $exception;
        }
    }

    public function editDictionary(int $productDictionaryId, EditProductDictionaryDto $dto) {
        $productDictionary = ProductDictionary::where('id', '=', $productDictionaryId)->first();
        if ($productDictionary === null) {
            throw new ProductDictionaryNotFoundException();
        }

        $product = Product::where('producer_id', '=', $dto->getProducerId())
            ->where('code', '=', $dto->getCode())->first();
        if ($product === null) {
            throw new ProductNotFoundException();
        }

        $productDictionary->product_id = $product->id;
        $productDictionary->code = $dto->getCodeDictionary();

        try {
            $productDictionary->save();
        } catch (QueryException $exception) {
            $errorCode = $exception->errorInfo[1];
            if ($errorCode == 1062){
                throw new ProductDictionaryAlreadyExistsException();
            }
            throw $exception;
        }
    }

    public function deleteDictionary(int $productDictionaryId) {
        $productDictionary = ProductDictionary::where('id', '=', $productDictionaryId);
        if ($productDictionary === null) {
            throw new ProductDictionaryNotFoundException();
        }
        $productDictionary->delete();
    }

    public function saveProduct(?Product $product, int $producerId, string $code, int $weight, ?string $description, ?int $ean) {
        if ($product === null) {
            $product = new Product();
            $product->producer_id = $producerId;
            $product->code = $code;
        }
        $product->weight = $weight;
        $product->description = $description;
        $product->ean = $ean;
        $product->save();
    }

    private function clearProductCode($code) {
        $code = preg_replace('/[^a-zA-Z0-9]/', '', $code);
        return mb_strtoupper(trim($code));
    }
}
