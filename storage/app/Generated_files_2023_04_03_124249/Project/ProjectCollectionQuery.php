<?php

namespace App\GraphQL\Query\Project;

use Closure;
use App\Services\ProjectService;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ResolveInfo;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use App\Models\Project;
use App\Traits\SelectFieldTrait;

class ProjectCollectionQuery extends Query
{
    protected $attributes = [
        'name' => 'project listing query',
    ];

    public function __construct(private ProjectService $projectService)
    {
    }

    public function type(): Type
    {
        return GraphQL::paginate('project','Projects');
    }

    public function args(): array
    {
        return ['perPage' => [
                    'name' => 'perPage',
                    'type' => Type::Int(),'alias' => 'per_page'],'page' => [
                    'name' => 'page',
                    'type' => Type::Int(),'alias' => 'page'],'workspaceId' => [
                    'name' => 'workspaceId',
                    'type' => Type::Int(),'alias' => 'workspace_id'],'campaignId' => [
                    'name' => 'campaignId',
                    'type' => Type::Int(),'alias' => 'campaign_id'],'formId' => [
                    'name' => 'formId',
                    'type' => Type::Int(),'alias' => 'form_id'],'search' => [
                    'name' => 'search',
                    'type' => Type::String(),'alias' => 'search'],];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {

        $fields = $getSelectFields();

        $args['select'] = $this->getFillableField(new Project(), $fields->getSelect());
        $args['with'] = $fields->getRelations();

        return $this->projectService->collection($args);
    }
}
