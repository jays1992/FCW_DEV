<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin\TblMstUser;
use Auth;
use DB;
use Session;
use Carbon\Carbon;
use App\Helpers\Helper;
use App\Helpers\Utils;

class HomeController extends Controller{
   
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){

        $menu_data  =   array();
        $USERID_REF =   Auth::user()->USERID;
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');  

        if(Auth::user()->UCODE =='ADMIN'){
            $query="SELECT * 
            FROM VW_MENU1 
            WHERE CYID_REF = '$CYID_REF' AND BRID_REF = '$BRID_REF' AND USERID_REF = '$USERID_REF' AND DASHBOARD='1' AND heading='Transactions' 
            ORDER BY MODULE_SEQUENCE ASC,VT_SEQUENCE ASC, ranks ASC
            ";  
        }
        else{
            $query="SELECT * 
            FROM VW_MENU1 
            WHERE CYID_REF = '$CYID_REF' /*AND BRID_REF = '$BRID_REF'*/ AND USERID_REF = '$USERID_REF' AND DASHBOARD='1' AND heading='Transactions' 
            ORDER BY MODULE_SEQUENCE ASC,VT_SEQUENCE ASC, ranks ASC
            ";  
        }
        
        $menu_data = DB::select($query);

        $slider_data = DB::select("SELECT * 
        FROM TBL_MST_BANNER_IMAGE_HDR 
        WHERE CYID_REF = '$CYID_REF' AND BRID_REF = '$BRID_REF' AND STATUS='N'");

        return view('home',compact(['menu_data','slider_data']));
    }

}
