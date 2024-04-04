<?php

namespace App\Traits;

trait PaginationTrait
{
    public function paginationAttribute($inputs, $data)
    {
        $inputs['limit'] = isset($inputs['limit']) ? $inputs['limit'] : config('site.pagination.limit');

        return $inputs['limit'] == '-1' ? $data->get() : $data->paginate($inputs['limit']);
    }
}
