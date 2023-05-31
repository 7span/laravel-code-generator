<?php

namespace App\GraphQL\Query\Role;

use Closure;
use App\Services\RoleService;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ResolveInfo;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class RoleResourceQuery extends Query
{
    protected $attributes = [
        'name' => 'role detail query',
    ];

    public function __construct(private RoleService $roleService)
    {
    }

    public function type(): Type
    {
        return GraphQL::type('role');
    }

    public function args(): array
    {
        return [
            'id' => ['name' => 'id', 'type' => Type::int(), 'rules' => ['required']],
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return $this->roleService->resource($args['id'], $args);
    }
}
