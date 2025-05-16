<?php

namespace Sevenspan\CodeGenerator\Traits;

use Illuminate\Support\Facades\File;
use Sevenspan\CodeGenerator\Enums\CodeGeneratorFileLogStatus;

trait ManagesFileCreationAndOverwrite
{
    /**
     * Create or overwrite a file based on overwrite option.
     *
     * @param string $filePath
     * @param string $contents
     * @param string $fileType
     * @return array [status, message, is_overwrite]
     */
    public function createOrOverwriteFile(string $filePath, string $contents, string $fileType): array
    {
        $isOverwrite = false;
        $fileExists = File::exists($filePath);
        $shouldOverwrite = $this->option('overwrite');

        if (! $fileExists) {
            File::put($filePath, $contents);
            $message = "$fileType file has been created successfully at: {$filePath}";
            $status = CodeGeneratorFileLogStatus::SUCCESS;
            $this->info($message);
        } elseif ($shouldOverwrite) {
            File::put($filePath, $contents);
            $message = "$fileType file was overwritten successfully at: {$filePath}";
            $status = CodeGeneratorFileLogStatus::SUCCESS;
            $isOverwrite = true;
            $this->info($message);
        } else {
            $message = "$fileType file already exists at: {$filePath}";
            $status = CodeGeneratorFileLogStatus::ERROR;
            $this->warn($message);
        }

        return [$status, $message, $isOverwrite];
    }
}
