<?php

namespace App\Library;

use Illuminate\Support\Facades\Storage;

class Helper
{
    public static function generateBaseDirectory()
    {
        $generatedFilesPath = 'Generated_files_' . date('Y_m_d_His', time());
        $storage = Storage::disk('local')->exists($generatedFilesPath);
        if ($storage == false) {
            Storage::disk('local')->makeDirectory($generatedFilesPath);
        }

    }
}
