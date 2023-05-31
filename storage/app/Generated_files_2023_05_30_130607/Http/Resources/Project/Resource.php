<?php

namespace App\Http\Resources\Project;

use App\Traits\ResourceFilterable;
use Illuminate\Http\Resources\Json\JsonResource;

class Resource extends JsonResource
{
    use ResourceFilterable;
    protected $model = 'Project';

    public function toArray($request)
    {
        $data = $this->fields();
        return $data;
    }
}
