<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\ProjectService;
use App\Http\Controllers\Controller;
use App\Http\Resources\Project\Resource as ProjectResource;
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

    public function show(Project $project)
    {
        $projectObj = $this->projectService->resource($project->id);

        return new ProjectResource($projectObj);
    }

    public function update(Project $project, ProjectRequest $request)
    {
        $projectObj = $this->projectService->update($project->id, $request->validated());

        return isset($projectObj['errors']) ? $this->error($projectObj) : $this->success($projectObj);
    }
}
