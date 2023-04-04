<?php

namespace App\GraphQL\Query\Project;

use Closure;
use App\Services\ProjectService;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ResolveInfo;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class ProjectResourceQuery extends Query
{
    protected $attributes = [
        'name' => 'project detail query',
    ];

    public function __construct(private ProjectService $projectService)
    {
    }

    public function type(): Type
    {
        return GraphQL::type('project');
    }

    public function args(): array
    {
        return [
            'id' => ['name' => 'id', 'type' => Type::int(), 'rules' => ['required']],
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return $this->projectService->resource($args['id'], $args);
    }
}
