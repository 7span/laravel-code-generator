<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\TestService;
use App\Http\Controllers\Controller;
use App\Http\Resources\Test\Resource as TestResource;
use App\Http\Requests\Test\Request as TestRequest;
use App\Http\Resources\Test\Collection as TestCollection;

class TestController extends Controller
{
    public function __construct()
    {
        private $this->testService = new TestService;
    }

    public function index(TestRequest $request)
    {
        $tests = $this->testService->collection($request->all());

        return new TestCollection($tests);
    }

    public function store(TestRequest $request)
    {
        $testObj = $this->testService->store($request->validated());

        return isset($testObj['errors']) ? $this->error($testObj) : $this->success($testObj);
    }

    public function show(Test $test)
    {
        $testObj = $this->testService->resource($test->id);

        return new TestResource($testObj);
    }
}
