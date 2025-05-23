<?php

namespace Sevenspan\CodeGenerator\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Sevenspan\CodeGenerator\Models\CodeGeneratorFileLog;

class LogTable extends Component
{
    use WithPagination;

    public function render()
    {
        return view('code-generator::livewire.log-table', [
            'logs' => CodeGeneratorFileLog::orderBy('created_at', 'desc')->paginate(10)
        ]);
    }
}
