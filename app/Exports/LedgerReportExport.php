<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class LedgerReportExport implements FromView
{
    public $accountTitles, $parentAccounts, $childSubs;

    public function __construct($accountTitles, $parentAccounts, $childSubs)
    {
        $this->accountTitles = $accountTitles;
        $this->parentAccounts = $parentAccounts;
        $this->childSubs = $childSubs;
    }

    public function view(): View
    {
        return view('reports.excel.ledger', [
            'accountTitles' => $this->accountTitles,
            'parentAccounts' => $this->parentAccounts,
            'childSubs' => $this->childSubs,
        ]);
    }
}
