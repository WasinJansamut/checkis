<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithTitle;

class DataQuality implements FromView,WithTitle
{

    private $data;

    public function __construct($data)
    {
        $this->data = $data;

    }

    public function view(): View
    {
        return view('exports.dataQuality', [
            'datas' => $this->data
        ]);
    }


    public function title(): string
    {
        return "Data Quality";
    }
}
