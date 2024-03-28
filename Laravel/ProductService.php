<?php


use App\Models\Product;
use App\Models\ProductDictionary;
use App\Services\Product\Dto\AddProductDictionaryDto;
use App\Services\Product\Dto\AddProductDto;
use App\Services\Product\Dto\EditProductDictionaryDto;
use App\Services\Product\Dto\EditProductDto;
use App\Services\Product\Dto\ImportProductDictionaryDto;
use App\Services\Product\Dto\ImportProductDto;

interface ProductService {

    public function getById(int $id) : ?Product;

    public function getByCode(string $code);

    public function getByCodeAndProducer(string $code, int $producerId) : ?Product;

    public function addProduct(AddProductDto $productDto);

    public function editProduct(int $id, EditProductDto $productDto);

    public function createProductJobTask(ImportProductDto $productDto);

    public function createProductDictionaryJobTask(ImportProductDictionaryDto $productDto);

    public function getDictionaryById(int $productDictionaryId) : ?ProductDictionary;

    public function getDictionaryByCode(string $code);

    public function getDictionaryByCodeAndProducer(int $producerId, string $code) : ?ProductDictionary;

    public function addDictionary(AddProductDictionaryDto $dto);

    public function editDictionary(int $productDictionaryId, EditProductDictionaryDto $dto);

    public function deleteDictionary(int $productDictionaryId);

    public function saveProduct(?Product $product, int $producerId, string $code, int $weight, ?string $description, ?int $ean);

}
