<?php

namespace Sevenspan\CodeGenerator\Models;

use Illuminate\Database\Eloquent\Model;

class CodeGeneratorFileLogs extends Model
{
    protected $table = 'codegenerator_file_logs';
    public $timestamps = false;
    protected $fillable = [
        "file_type",
        "file_path",
        "status",
        "message",
        "is_overwrite",
        "created_at",
    ];
}
