<?php
namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\Admin\TblMstUser;
use Auth;
use DB;
use Session;
use Response;
use SimpleXMLElement;
use Spatie\ArrayToXml\ArrayToXml;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Helpers\Helper;
use App\Helpers\Utils;

class MstFrm530Controller extends Controller{

    protected $form_id  = 530;
    protected $vtid_ref = 600;
    protected $view     = "masters.common.UserManagement.mstfrm530";
   
  
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){  
        
        $UserDetails    =   array(
            'USER_CODE' =>  Auth::user()->UCODE,
            'USER_NAME' =>  Auth::user()->DESCRIPTIONS,
            'USER_PASSWORD' =>  Auth::user()->PASSWORD,
        ); 


                          
        $FormId         =   $this->form_id;
        return view($this->view.'add', compact(['FormId','UserDetails']));       
    }



    public function save(Request $request) {        
        $USERID_REF     =   Auth::user()->USERID;  
        $CYID_REF       =   Auth::user()->CYID_REF;    
        $NEW_PASSWORD   =   $request['NEW_PASSWORD'];
        $data_result      = DB::update("UPDATE TBL_MST_USER SET PASSWORD='$NEW_PASSWORD' WHERE USERID='$USERID_REF' AND CYID_REF='$CYID_REF' ");
        if($data_result){
            auth()->logout();
            return Response::json(['success' =>true,'msg' => 'Your password has been successfully changed.']);

        }else{
            return Response::json(['errors'=>true,'msg' =>  'Oops ! An error occurred, Please check. ']);
        }
        
        exit();   
     }


      


}
