<?php

namespace App\GraphQL\Query\Roles;

use Closure;
use App\Services\RolesService;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ResolveInfo;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use App\Models\Roles;
use App\Traits\SelectFieldTrait;

class RolesCollectionQuery extends Query
{

    use SelectFieldTrait;

    protected $attributes = [
        'name' => 'roles listing query',
    ];

    public function __construct(private RolesService $rolesService)
    {
    }

    public function type(): Type
    {
        return GraphQL::paginate('rolesType','Roless');
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

        $args['select'] = $this->getFillableField(new Roles(), $fields->getSelect());
        $args['with'] = $fields->getRelations();

        return $this->rolesService->collection($args);
    }
}
