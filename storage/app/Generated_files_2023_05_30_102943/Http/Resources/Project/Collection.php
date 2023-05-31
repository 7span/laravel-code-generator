<?php

namespace App\Http\Resources\Project;

use Illuminate\Http\Resources\Json\ResourceCollection;

class Resource extends ResourceCollection
{
    protected $model = 'App\Http\Resources\Project\Resource';

    public function toArray($request)
    {
        return $this->collection;
    }
}
