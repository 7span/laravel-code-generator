<?php

namespace Sevenspan\CodeGenerator\Models;

use Illuminate\Database\Eloquent\Model;
use SevenSpan\CodeGenerator\Enums\FileGenerationStatus;

class CodeGeneratorFileLog extends Model
{
    protected $table = 'codegenerator_file_logs';
    public $timestamps = true;
    protected $fillable = [
        "file_type",
        "file_path",
        "status",
        "message",
        "is_overwrite",

    ];

    protected $casts = [
        'status' => FileGenerationStatus::class,
    ];
}
