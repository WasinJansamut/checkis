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
        $cases = CasesModel::orderBy('number')->paginate(20);
        $logs = SystemLogModel::where('target', "case")->get();
        // dd($logs);
        return view('manage_cases', ['cases' => $cases, 'logs' => $logs]);
    }
}
