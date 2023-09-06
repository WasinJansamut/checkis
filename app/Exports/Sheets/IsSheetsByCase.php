<?php

namespace App\Exports\Sheets;

use App\Exports\IsReportExport;
use App\Models\IsModel;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class IsSheetsByCase implements FromQuery, WithTitle,WithHeadings
{
private $case;
private $array_id;

public function __construct(string $case,array $array_id)
{
    $this->case = $case;
    $this->array_id = $array_id;
//$this->year  = $year;
}

public function headings():array
{
    $rows = IsModel::first();
    $header = array_keys($rows->toArray());
    return $header;

}

/**
* @return Builder
 *
*/
public function query()
{
    return IsModel::whereIn('id',$this->array_id);
       }
/**
* @return string
*/
public function title(): string
{
    return $this->case;
}
}
