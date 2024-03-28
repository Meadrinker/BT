<?php


use App\Http\Requests\CustomFormRequest;
use App\Services\Product\Dto\AddProductDto;

class ProductAddRequest extends CustomFormRequest {

    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'producer_id' => [
                'required',
                'integer'
            ],
            'code' => [
                'required',
                'string'
            ],
            'weight' => [
                'required',
                'integer',
                'max:' . \App\Http\Requests\Admin\env('PACK_PALLET_WEIGHT')
            ],
            'description' => [
                'nullable',
                'string',
                'max:255'
            ],
            'ean' => [
                'nullable',
                'integer'
            ]
        ];
    }

    public function messages() {
        return [
            'producer_id.required' => 'producer_id.required',
            'producer_id.integer' => 'producer_id.integer',
            'code.required' => 'code.required',
            'code.string' => 'code.string',
            'weight.required' => 'weight.required',
            'weight.integer' => 'weight.integer',
            'weight.max' => 'weight.max',
            'description.string' => 'description.string',
            'description.max' => 'description.max',
            'ean.integer' => 'ean.integer'
        ];
    }

    public function messagesData(string $key) {
        $data = [
            'producer_id.required' => [
                'code' => 'required',
                'message' => 'Producer id is required'
            ],
            'producer_id.integer' => [
                'code' => 'integer',
                'message' => 'Producer id must be an integer'
            ],
            'code.required' => [
                'code' => 'required',
                'message' => 'Code is required'
            ],
            'code.string' => [
                'code' => 'string',
                'message' => 'Code must be a string'
            ],
            'weight.required' => [
                'code' => 'required',
                'message' => 'Weight is required'
            ],
            'weight.integer' => [
                'code' => 'integer',
                'message' => 'Weight must be an integer'
            ],
            'weight.max' => [
                'code' => 'max',
                'message' => 'Weight must not be greater than 1000000',
                'value' => \App\Http\Requests\Admin\env('PACK_PALLET_WEIGHT')
            ],
            'description.string' => [
                'code' => 'string',
                'message' => 'Description must be a string'
            ],
            'description.max' => [
                'code' => 'max',
                'message' => 'Description must not be greater than 255 characters',
                'value' => 255
            ],
            'ean.integer' => [
                'code' => 'integer',
                'message' => 'EAN must be an integer'
            ],
        ];
        if (isset($data[$key])) {
            return $data[$key];
        }
        return $key;
    }

    public function getData() : AddProductDto {
        return new AddProductDto($this->get('producer_id'), $this->get('code'), $this->get('weight'),
            $this->get('description'), $this->get('ean'));
    }

}
