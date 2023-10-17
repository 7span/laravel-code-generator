<?php

namespace App;

use App\Traits\BaseModel;
use App\Traits\BootModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Channel extends Model
{
    use BaseModel, BootModel, HasFactory, SoftDeletes;

    protected $table = 'channels';

    public $fillable = [
        'deleted_by',
        'created_by',
        'updated_by'
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

    

    public function user()
    {
        return $this->hasOne(User::class);
    }


}
