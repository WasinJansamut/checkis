<?php

namespace App\Exports;

use App\Exports\Sheets\DataQuality;
use App\Exports\Sheets\IsReportFirstSheet;
use App\Exports\Sheets\IsSheetsByCaseFromView;
use App\Exports\Sheets\SummarySheet;
use App\Exports\Sheets\IsSheetsByCase;
//use App\Exports\Sheets\SummarySheet;
use App\Http\Controllers\CheckingController;
use App\Models\IsModel;
use App\Models\jobsModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\Exportable;


//class IsReportExport implements FromCollection,WithHeadings,WithMultipleSheets
class IsReportExport implements WithMultipleSheets
{
    use Exportable;
    /**
     * @return \Illuminate\Support\Collection
     */
    private $hosp;
    private $start_date;
    private $end_date;
    private $jobid;
    private $datas;

    public function __construct(string $hosp, string $start_date, string $end_date, string $jobid, array $datas = [])
    {
        $this->hosp = $hosp;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->jobid = $jobid;
        $this->datas = $datas;
    }

    public function sheets(): array
    {

        $sheets = [];
        $data = [];

        foreach ($this->datas as $row) {
            $row_data = new \stdClass();
            $row_data->count = count($row['is_ids']);
            $row_data->name = $row['case_name'];
            $row_data->number = $row['case_number'];

            $data[] = $row_data;
        }
        $job = JobsModel::where('id', $this->jobid)->with('getHospName')->first();

        //build first page sheet
        $sheets[] = new IsReportFirstSheet($job);
        $sheets[] = new DataQuality($job);
        $sheets[] = new SummarySheet($data);

        if ($job->is_export_data == true) {
            $rows = IsModel::first();
            $header = array_keys($rows->toArray());

            //build another sheets
            foreach ($this->datas as $data) {
                if (count($data["is_ids"]) != 0) {
                    $case_name = $data["case_number"] . " " . $data["case_name"];
                    $sheets[] = new IsSheetsByCaseFromView($case_name, $data["is_ids"], $data["is_datas"], $header, $data['highlight_columns']);
                }
            }
        }

        return $sheets;
    }
}
