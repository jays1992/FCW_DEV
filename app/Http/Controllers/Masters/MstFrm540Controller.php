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

class MstFrm540Controller extends Controller{

    protected $form_id  = 540;
    protected $vtid_ref = 610;
    protected $view     = "masters.common.UserManagement.mstfrm540";
   
  
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
        $CYID_REF       =   Auth::user()->CYID_REF;    
        $NEW_PASSWORD   =   $request['NEW_PASSWORD'];
        $USERID_REF   =   $request['USERID_REF'];
        $data_result      = DB::update("UPDATE TBL_MST_USER SET PASSWORD='$NEW_PASSWORD' WHERE USERID='$USERID_REF' AND CYID_REF='$CYID_REF' ");
        if($data_result){
        
            return Response::json(['success' =>true,'msg' => 'Your password has been successfully changed.']);

        }else{
            return Response::json(['errors'=>true,'msg' =>  'Oops ! An error occurred, Please check. ']);
        }
        
        exit();   
     }


     
public function get_User(Request $request) {   
    $Status = "A";
    $CYID_REF = Auth::user()->CYID_REF;
    $BRID_REF = Session::get('BRID_REF');
    $FYID_REF = Session::get('FYID_REF');        
    $objUser = DB::select("SELECT USERID AS DOCID, UCODE AS DOCNO, DESCRIPTIONS  FROM TBL_MST_USER WHERE CYID_REF= $CYID_REF AND STATUS='A' AND BRID_REF=$BRID_REF AND (DEACTIVATED=0 or DEACTIVATED IS NULL)  ");
    //dd($objUser); 


    if(!empty($objUser)){        
        foreach ($objUser as $index=>$dataRow){   
            $row = '';
            $row = $row.'<tr ><td style="text-align:center; width:10%">';
            $row = $row.'<input type="checkbox" name="user[]"  id="usercode_'.$dataRow->DOCID.'" class="clsspid_user" 
            value="'.$dataRow->DOCID.'"/>             
            </td>           
            <td style="width:40%;">'.$dataRow->DOCNO;
            $row = $row.'<input type="hidden" id="txtusercode_'.$dataRow->DOCID.'" data-code="'.$dataRow->DOCNO.'-'.$dataRow->DESCRIPTIONS.'"   data-desc="'.$dataRow->DESCRIPTIONS.'" 
            value="'.$dataRow->DOCID.'"/></td>

            <td style="width:40%;">'.$dataRow->DESCRIPTIONS.'</td>
   

           </tr>';
            echo $row;
        }

        }else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
        }

        exit();



   }



      


}
