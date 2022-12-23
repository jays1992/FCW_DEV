<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\TblMstCountry;
use DB;
use Session;

class MastersController extends Controller
{
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request){
       // dd($request);
    //    switch ($request->formid) {
    //        case '4':
    //         //    print_r($request->name);
    //         //     $country_data = DB::table('TBL_MST_COUNTRY')
    //         //     ->where('name','like',"%$request->name%")
    //         //     ->orderBy('Name', 'ASC')
    //         //    //  ->get()
    //         //    //  ->paginate(10)
    //         //    ->Paginate(15)
    //         //    ->toArray();
    //         //    //echo '<pre>';
    //         //   // dump($country_data);
    //            return view('masters.country');
    //            break;

    //        case '5':
    //             return view('masters.state');
    //             break;
           
    //        default:
    //             return view('masters.notfound');
    //             break;
    //    }
        
       return view('masters.country');
        
    }


    public function getcountries(Request $request){

        // { "data": "CTRYID" },
        // { "data": "CTRYCODE" },
        // { "data": "NAME" },
        // { "data": "ISDCODE" },
        // { "data": "LANG" },
        // { "data": "CONTINENTAL" },
        // { "data": "CAPITAL" }

        $columns = array( 
            0 =>'NO', 
            1 =>'CTRYCODE',
            2 =>'NAME',
            3 =>'ISDCODE',
            4 =>'LANG',
            5 =>'CONTINENTAL',
            6 =>'CAPITAL',
        );  
        $totalData = TblMstCountry::count();            
        $totalFiltered = $totalData;
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        
        // echo "sr=".$request->input('search.value');
        // die;
        if(empty($request->input('search.value')))
        {            
            $countrydata = TblMstCountry::offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();
        }else {

            $search = $request->input('search.value'); 
            $filtercolumn = $request->input('filtercolumn');

            // $countrydata =  TblMstCountry::where('CTRYCODE','LIKE',"%{$search}%")
            //                 ->orWhere('NAME', 'LIKE',"%{$search}%")
            //                 ->orWhere('ISDCODE', 'LIKE',"%{$search}%")
            //                 ->orWhere('LANG', 'LIKE',"%{$search}%")
            //                 ->offset($start)
            //                 ->limit($limit)
            //                 ->orderBy($order,$dir)
            //                 ->get();

            // $totalFiltered = TblMstCountry::where('CTRYCODE','LIKE',"%{$search}%")
            //                 ->orWhere('NAME', 'LIKE',"%{$search}%")
            //                 ->orWhere('ISDCODE', 'LIKE',"%{$search}%")
            //                 ->orWhere('LANG', 'LIKE',"%{$search}%")
            //                  ->count();

            $countrydata =  TblMstCountry::where("$filtercolumn","LIKE","%$search%")
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            $totalFiltered = TblMstCountry::where("$filtercolumn","LIKE","%$search%")
                            ->count();
        }
        $data = array();
        if(!empty($countrydata))
        {
            foreach ($countrydata as $key=>$countryitem)
            {
                
                $nestedData['NO'] = '<input type="checkbox" value="'.$countryitem->CTRYID.'" class="js-selectall1">';
                $nestedData['NAME'] = $countryitem->NAME;
                $nestedData['CTRYCODE'] = $countryitem->CTRYCODE;
                $nestedData['ISDCODE'] = $countryitem->ISDCODE;
                $nestedData['LANG'] = $countryitem->LANG;
                $nestedData['CONTINENTAL'] = $countryitem->CONTINENTAL;
                $nestedData['CAPITAL'] = $countryitem->CAPITAL;
//                 $nestedData['action'] = '<a href="#" class="del"><span class="glyphicon glyphicon-trash"></span> 
// </a><a href="#" class="edit"><span class="glyphicon glyphicon-edit"></span></a>';
                $data[] = $nestedData;
            }

        }
        $json_data = array(
        "draw"            => intval($request->input('draw')),  
        "recordsTotal"    => intval($totalData),  
        "recordsFiltered" => intval($totalFiltered), 
        "data"            => $data   
        );            
        echo json_encode($json_data); 

    }
}
