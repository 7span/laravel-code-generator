<?php

namespace Sevenspan\CodeGenerator\Http\Livewire;

use Livewire\Component;
use Sevenspan\CodeGenerator\Models\CodeGeneratorFileLog;

class LogTable extends Component
{ 
    public $logs = [];

    public function mount()
    {
        $this->loadLogs();
    }

    public function loadLogs()
    {
        $this->logs = CodeGeneratorFileLog::orderBy('created_at', 'desc')->get()->take(10);
    }

    public function render()
    {
        return view('code-generator::livewire.log-table', [
            'logs' => $this->logs
        ]);
    }
}