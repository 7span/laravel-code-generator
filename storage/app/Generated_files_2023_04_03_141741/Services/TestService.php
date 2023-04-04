<?php

namespace App\Services;

use App\Models\Test;
use App\Traits\BaseModel;
use App\Traits\PaginationTrait;

class TestService
{
    use BaseModel, PaginationTrait;

    public function __construct()
    {
        private $this->testObj = new Test;
    }

    public function collection(array $inputs)
    {
        $tests = $this->testObj->getQB();

        return (isset($inputs['limit']) && $inputs['limit'] == '-1') ? $tests->get() : $tests->paginate();
    }

    public function store(array $inputs)
    {
        $this->testObj->create($inputs);
        $data['message'] = __('entity.entityCreated', ['entity' => 'Test']);
        return $data;
    }

    public function resource($id)
    {
        $test = $this->testObj->getQB()->findOrFail($id);

        return $test;
    }
}
