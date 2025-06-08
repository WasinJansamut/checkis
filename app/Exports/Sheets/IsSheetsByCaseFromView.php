<?php

namespace App\Exports\Sheets;

use App\Exports\IsReportExport;
use App\Models\IsModel;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class IsSheetsByCaseFromView implements FromView, WithTitle
{
    private $title;
    private $array_id;
    private $isData;
    private $header;
    private $highlight_columns;
    private $emptyFields;
    private $sheetIndex;

    public function __construct(string $title, array $array_id, $isData, $header, $highlight_columns = [], $emptyFields = [], $sheetIndex = 0)
    {
        $this->title = $title;
        $this->array_id = $array_id;
        $this->isData = $isData;
        $this->header = $header;
        $this->highlight_columns = $highlight_columns;
        $this->emptyFields = $emptyFields;
        $this->sheetIndex = $sheetIndex;
    }


    public function view(): View
    {
        return view('exports.case', [
            'isData' => $this->isData,
            'header' => $this->header,
            'highlight_columns' => $this->highlight_columns,
            'emptyFields' => $this->emptyFields,
            'isFirstSheet' => $this->sheetIndex === 0, // ✅ ส่งเข้า blade
        ]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return $this->title;
    }
}
