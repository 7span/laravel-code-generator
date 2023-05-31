<?php

namespace App\GraphQL\Query\Role;

use Closure;
use App\Services\RoleService;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ResolveInfo;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use App\Models\Role;
use App\Traits\SelectFieldTrait;

class RoleCollectionQuery extends Query
{

    use SelectFieldTrait;

    protected $attributes = [
        'name' => 'role listing query',
    ];

    public function __construct(private RoleService $roleService)
    {
    }

    public function type(): Type
    {
        return GraphQL::paginate('roleType','Roles');
    }

    public function args(): array
    {
        return ['perPage' => [
                    'name' => 'perPage',
                    'type' => Type::Int(),'alias' => 'per_page'],'page' => [
                    'name' => 'page',
                    'type' => Type::Int(),'alias' => 'page'],];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {

        $fields = $getSelectFields();

        $args['select'] = $this->getFillableField(new Role(), $fields->getSelect());
        $args['with'] = $fields->getRelations();

        return $this->roleService->collection($args);
    }
}
