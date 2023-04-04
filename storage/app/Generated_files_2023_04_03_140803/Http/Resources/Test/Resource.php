<?php

namespace App\Http\Resources\Test;

use App\Traits\ResourceFilterable;
use Illuminate\Http\Resources\Json\JsonResource;

class Resource extends JsonResource
{
    use ResourceFilterable;
    protected $model = 'Test';

    public function toArray($request)
    {
        $data = $this->fields();
        return $data;
    }
}
