<?php

namespace App\Http\Controllers;

use App\Models\CasesModel;
use App\Models\SystemLogModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManageCases extends Controller
{
    public function index()
    {
        $error_types = [
            "1" => "ความถูกต้อง (Accuracy)",
            "2" => "ความสมบูรณ์ (Completeness)",
            "3" => "ความเที่ยง (Consistency)",
            "4" => "ความตรงตามกาล (Timeliness)",
            "5" => "ความเป็นเอกลักษณ์ (Uniqueness)",
            "6" => "ความแม่นยำ (Orderliness)",
        ];
            $cases = CasesModel::orderBy('number')->paginate(20);
            $logs = SystemLogModel::where('target',"case")->get();
//            dd($logs);
            return view('manage_cases',['cases'=>$cases,'error_type'=>$error_types,'logs'=>$logs]);

    }
}
