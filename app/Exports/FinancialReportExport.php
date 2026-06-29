<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class FinancialReportExport implements FromView, ShouldAutoSize
{
    public function __construct(private array $reportData)
    {
    }

    public function view(): View
    {
        return view('reports.excel', $this->reportData);
    }
}
