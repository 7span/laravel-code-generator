<?php

namespace App\Traits;

trait SelectFieldTrait
{
    public function getFillableField($model, $select = null)
    {
        return array_unique(array_merge($select, $model->getFillable()));
    }
}
