<?php

namespace App\Http\Controllers;

use App\Models\IsModel;
use Illuminate\Http\Request;

class TestController extends Controller
{
    function index(){
//        $row = [
//            "staer"=>1,
//            "staward"=>5,
//            "br1"=>1,
//            "br2"=>2,
//            "br3"=>3,
//            "br4"=>4,
//            "br5"=>5,
//            "br6"=>"correct",
//            ];

        $isData = IsModel::where('hosp',10674)->whereYear('hdate',2021)->whereMonth('hdate',4)->get();
//        echo($isData);

        foreach($isData as $row){
            if(in_array((int)$row->staer,[1,6]) || (int)$row->staward == 5
            ){
                for($i = 1 ; $i <= 6 ; $i++){
//                    echo $row["br$i"];
                    if($row->{"br$i"} == 4 ){
                        echo "br$i"."\n";
                    }
                }
            }
        }



    }


}
