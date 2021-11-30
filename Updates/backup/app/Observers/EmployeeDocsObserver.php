<?php

namespace App\Observers;

use App\Models\EmployeeDocs;

class EmployeeDocsObserver
{

    public function saving(EmployeeDocs $doc)
    {
        if (!isRunningInConsoleOrSeeding()) {
            $doc->last_updated_by = user()->id;
        }
    }

    public function creating(EmployeeDocs $doc)
    {
        if (!isRunningInConsoleOrSeeding()) {
            $doc->added_by = user()->id;
        }
    }

}
