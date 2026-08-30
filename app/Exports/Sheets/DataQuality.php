<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class DataQuality implements FromView, WithTitle, WithEvents
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

    public function registerEvents(): array
    {
        return [AfterSheet::class => function (AfterSheet $event) {
            $event->sheet->freezePane('A2');
            $event->sheet->getStyle('A1:' . $event->sheet->getHighestColumn() . '1')->getFont()->setBold(true);
        }];
    }
}
