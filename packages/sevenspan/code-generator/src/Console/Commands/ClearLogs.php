<?php 

namespace Sevenspan\CodeGenerator\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Sevenspan\CodeGenerator\Models\CodeGeneratorFileLog;

class ClearLogs extends Command
{
    protected $signature = 'codegenerator:clearlogs';
    protected $description = 'Deletes log entries older than configured retention days';

    public function handle(): void
    {
        $days = config('code-generator.log_retention_days', 2);

        //   Delete log entries older than the configured retention period
        $deleted = CodeGeneratorFileLog::where('created_at', '<', now()->subDays($days))->delete();

        $this->info("Deleted {$deleted} log entries older than {$days} days.");
    }
}