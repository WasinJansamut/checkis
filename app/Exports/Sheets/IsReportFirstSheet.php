<?php

namespace App\Exports\Sheets;

use App\Exports\IsReportExport;
use App\Models\IsModel;
use App\Models\jobsModel;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\FromCollection;


class IsReportFirstSheet implements FromView,WithTitle
{
    private $job;


    public function __construct($job)
    {
    $this->job = $job;

    }

    public function view(): View
    {
        return view('exports.first_sheet', [
            'job' => $this->job
        ]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return "Job details";
    }
}
