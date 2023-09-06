<?php

namespace App\Http\Controllers;

use App\Models\SystemLogModel;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(){
        $rows = SystemLogModel::orderBy('id','desc')->paginate(20);

        return view('history',['rows'=>$rows]);
    }
}
