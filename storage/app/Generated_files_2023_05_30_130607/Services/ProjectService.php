<?php

namespace App\Services;

use App\Models\Project;
use App\Traits\BaseModel;
use App\Traits\PaginationTrait;

class ProjectService
{
    use BaseModel, PaginationTrait;

    private $projectObj;

    public function __construct()
    {
        $this->projectObj = new Project;
    }

    public function collection(array $inputs)
    {
        $projects = $this->projectObj->getQB();

        return (isset($inputs['limit']) && $inputs['limit'] == '-1') ? $projects->get() : $projects->paginate();
    }

    public function store(array $inputs)
    {
        $this->projectObj->create($inputs);
        $data['message'] = __('entity.entityCreated', ['entity' => 'Project']);
        return $data;
    }
}
