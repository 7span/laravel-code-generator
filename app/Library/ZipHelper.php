<?php

namespace App\Library;

class ZipHelper
{
    public static function makeZip($generatedFilesPath)
    {
        // Path for the Generated_files
        $rootPath = storage_path('app/' . $generatedFilesPath);

        // Initialize archive object
        $zip = new \ZipArchive();
        $zip->open($generatedFilesPath . '.zip', \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        // Create recursive directory iterator
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($rootPath),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file) {
            // Skip directories (they would be added automatically)
            if (! $file->isDir()) {
                // Get real and relative path for current file
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($rootPath) + 1);

                // Add current file to archive
                $zip->addFile($filePath, $relativePath);
            }
        }

        // Zip archive will be created only after closing object
        $zip->close();
    }
}
