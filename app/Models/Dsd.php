<?php

namespace App\Models;

use App\Traits\BaseModel;
use App\Traits\BootModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dsd extends Model
{
    use BaseModel, BootModel, HasFactory, SoftDeletes;

    protected $table = 'dsds';

    public $fillable = [
        'four',
        'three_id',
        'two',
        'one_1',
        'four',
        'three',
        'two',
        'one',
        'four',
        'three',
        'two',
        'one',
        'four',
        'three',
        'two',
        'one',
        'four',
        'three',
        'two',
        'one',
        'four',
        'three',
        'two',
        'one',
        'four',
        'three',
        'two',
        'one',
        'four',
        'three',
        'two',
        'one',
        'four',
        'three',
        'two',
        'one',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public $queryable = [
        'id'
    ];
    
    protected $relationship = [];

    protected $scopedFilters = [];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->created_by = auth()->id();
            $model->updated_by = auth()->id();
        });
    }
}
