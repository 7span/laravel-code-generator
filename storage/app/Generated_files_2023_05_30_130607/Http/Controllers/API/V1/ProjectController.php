<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\ProjectService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Project\Request as ProjectRequest;
use App\Http\Resources\Project\Collection as ProjectCollection;

class ProjectController extends Controller
{
    private $projectService;

    public function __construct()
    {
        $this->projectService = new ProjectService;
    }

    public function index(ProjectRequest $request)
    {
        $projects = $this->projectService->collection($request->all());

        return new ProjectCollection($projects);
    }

    public function store(ProjectRequest $request)
    {
        $projectObj = $this->projectService->store($request->validated());

        return isset($projectObj['errors']) ? $this->error($projectObj) : $this->success($projectObj);
    }
}
