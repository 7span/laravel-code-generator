<?php

namespace App\Http\Resources\Test;

use Illuminate\Http\Resources\Json\ResourceCollection;

class Resource extends ResourceCollection
{
    protected $model = 'App\Http\Resources\Test\Resource';

    public function toArray($request)
    {
        return $this->collection;
    }
}
