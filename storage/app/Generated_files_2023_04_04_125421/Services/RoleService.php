<?php

namespace App\Services;

use App\Models\Role;
use App\Traits\PaginationTrait;

class RoleService
{
    use  PaginationTrait;

    private $this->roleObj;

    public function __construct()
    {
        $this->roleObj = new Role;
    }

    public function collection($inputs = null)
    {
        $inputs = $this->paginationAttribute($inputs);

        $select = 'roles.*';
        if (isset($inputs['select'])) {
            $select = $inputs['select'];
        }

        $roles = $this->roleObj->select($select);
        if (isset($inputs['with'])) {
            $roles = $roles->with($inputs['with']);
        }


        $inputs['limit'] = $inputs['limit'] == -1 ? $roles->count() : $inputs['limit'];
        $roles = $roles->paginate($inputs['limit'], ['*'], 'page', $inputs['page']);

        return $roles;
    }

    public function store($inputs = null)
    {
        $this->roleObj->create($inputs);
        return true;

    }

    public function resource($id, $inputs = null)
    {
        return $this->roleObj->select('roles.*')
            ->where('roles.id', $id)
            ->first();

    }

    public function update($id, $inputs = null)
    {
        $role = $this->roleObj->resource($id);
        throw_if(empty($role), new NotFoundException(__('error.entityNotFound', ['entity' => 'role'])));

        $role->update($inputs);
        return true;
    }

    public function destroy(int $id)
    {
        $this->resource($id)->delete();
        return true;
    }





}
