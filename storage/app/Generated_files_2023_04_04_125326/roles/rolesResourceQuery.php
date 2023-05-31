<?php

namespace App\GraphQL\Query\Roles;

use Closure;
use App\Services\RolesService;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ResolveInfo;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class RolesResourceQuery extends Query
{
    protected $attributes = [
        'name' => 'roles detail query',
    ];

    public function __construct(private RolesService $rolesService)
    {
    }

    public function type(): Type
    {
        return GraphQL::type('roles');
    }

    public function args(): array
    {
        return [
            'id' => ['name' => 'id', 'type' => Type::int(), 'rules' => ['required']],
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return $this->rolesService->resource($args['id'], $args);
    }
}
