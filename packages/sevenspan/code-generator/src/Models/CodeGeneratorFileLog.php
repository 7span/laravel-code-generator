<?php

namespace Sevenspan\CodeGenerator\Models;

use Illuminate\Database\Eloquent\Model;
use SevenSpan\CodeGenerator\Enums\FileGenerationStatus;

class CodeGeneratorFileLog extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'codegenerator_file_logs';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "file_type",       // Type of the file (e.g., Controller, Model, etc.)
        "file_path",       // Path where the file is generated
        "status",          // Status of the file generation (e.g., success, error)
        "message",         // Optional message or description
        "is_overwrite",    // Indicates if the file was overwritten
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'status' => FileGenerationStatus::class, // Cast the status attribute to the FileGenerationStatus enum
    ];
}
