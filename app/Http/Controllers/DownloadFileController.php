<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DownloadFileController extends Controller
{
    public function index(){
        $filePath = public_path("csv-file/Checking_report.xlsx");
        $headers = ['Content-Type: application/pdf'];
        $fileName = time().'.xlsx';
        return response()->download($filePath, $fileName, $headers);
    }

}
