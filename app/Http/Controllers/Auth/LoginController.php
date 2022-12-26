<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\Admin\TblMstUser;
use Auth;
use DB;
use Session;
use Response;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request) {


        $rules = [
            'UCODE' => 'required',
            'PASSWORD' => 'required',
          
        ];

      //  $this->validate($request,$rules);
      $UCODE    = $request['UCODE'];
      $PASSWORD = $request['PASSWORD'];
      $CYID_REF = $request['CYID_REF'];
      $BRID_REF =  $request['BRID_REF'];
      $FYID_REF =  $request['FYID_REF'];       

      $req_data = [
          'UCODE'    => $UCODE ,
          'PASSWORD' => $PASSWORD,
          'CYID_REF' => $CYID_REF,
          //'BRID_REF' => $BRID_REF,
          //'FYID_REF' => $FYID_REF,
         
      ]; 

      
    //   $req_data = [
    //     'UCODE'    => $request['UCODE'],
    //     'PASSWORD' => $request['PASSWORD'],
    //     'CYID_REF' => $request['CYID_REF'],
    //     'BRID_REF' => $request['BRID_REF'],
    //     'FYID_REF' => $request['FYID_REF'],
      
    // ]; 

    $messages = [
        'UCODE.required' => 'We need UCODE!',
        'PASSWORD.required' => 'We need PASSWORD!'
    ];
        
        $validator = Validator::make( $req_data, $rules, $messages);
    
        if ($validator->fails())
        {
            return Response::json(['errors' => $validator->errors()]);	
        }
     
            // Store your user in database 

            
                $req_data['DEACTIVATED'] = 0;   
                $req_data['STATUS'] = 'A';  
                
                  

                //DB::enableQueryLog();
                //print_r($req_data);
                $auth = TblMstUser::where( $req_data)->first();
                //print_r($auth);
                //DB::getQueryLog();
                //die;

                
                if( !empty($auth)){
                    
                    //$auth['BRID_REF']=$BRID_REF;
                    $auth['FYID_REF']=$FYID_REF;

                    Auth::login($auth);

                   
                    // Last login details


                    //entry in log / audittrail via stored procedure  
                    $FORMID_REF = 18;
                    $VID = 0;
                    $USERID = $auth['USERID'];
                    $ACTIONNAME = 'LOGIN';
                    $IPADDRESS = request()->ip();

                    $log_data = [
                        $FORMID_REF, $VID, $USERID, $ACTIONNAME, Date('Y-m-d'), Date('h:i:s.u'), $CYID_REF, $BRID_REF, $FYID_REF, $IPADDRESS
                    ];

                             
                    DB::statement('EXEC sp_mst_audittrail_in ?,?,?,?,?,?,?,?,?,?',  $log_data);  
                    
                    
                    $UID    =   Auth::user()->USERID;
                    $CID    =   Auth::user()->CYID_REF;
                    $BID    =   Auth::user()->BRID_REF;
                    $FID    =   Auth::user()->FYID_REF;

            
                    $data_arr   =   DB::select("select TOP 1 INDATE as LastDate FROM TBL_MST_AUDITTRAIL
                    where USERID='$UID' AND CYID_REF='$CID' /*AND BRID_REF=' $BID' AND FYID_REF='$FID'*/ order by ACTID DESC");
            
                    $branch_arr =   DB::select("select t2.NAME as company_name,t3.BRID as branch_id,t3.BRNAME as branch_name,t3.LOGO as branch_logo,t3.FRANCHISEE_LABEL as franchisee_label,t4.BG_DESC as branch_group 
                    from TBL_MST_USER_BRANCH_MAP t1 
                    inner join TBL_MST_COMPANY t2 on t1.CYID_REF=t2.CYID 
                    inner join TBL_MST_BRANCH t3 on t1.MAPBRID_REF=t3.BRID 
                    inner join TBL_MST_BRANCH_GROUP t4 on t3.BGID_REF=t4.BGID where t1.USERID_REF='$UID'");
            
                    $login_date     =   date('d-F-Y',strtotime($data_arr[0]->LastDate));
                    $login_time     =   date('H:i',strtotime($data_arr[0]->LastDate));
            
                    $company_name   =   $branch_arr[0]->company_name;
                    $branch_name   =   $branch_arr[0]->branch_name;
                    $branch_group   =   $branch_arr[0]->branch_group;
                    $branch_logo   =   $branch_arr[0]->branch_logo;
                    $franchisee_label=   $branch_arr[0]->franchisee_label;
                    $BID            =   $branch_arr[0]->branch_id;
                    

                    

                    
            
                    Session::put('login_date', $login_date);
                    Session::put('login_time', $login_time);
                    Session::put('company_name', $company_name);
                    Session::put('branch_name', $branch_name);
                    Session::put('branch_group', $branch_group);
                    Session::put('branch_logo', $branch_logo);
                    Session::put('franchisee_label', $franchisee_label);
                    
                    Session::put('BRID_REF', $BID);
                    Session::put('FYID_REF', $FID);

                    return Response::json(['success' => '1']);
                   
                }
                else
                {
                    return Response::json(['msg' => 'Invalid credentials.','errors'=>true,'login'=>'invalid']);
                }
         
        return Response::json(['errors' => $validator->errors()]);		
        exit();



        if (Auth::attempt(array('UCODE' => $username, 'password' => $password)))
        {
            echo '--done';
        }else{
            echo '--not ok';
        }

        if(!Auth::attempt($credentials))
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);

           ECHO "--OK"; 

    }


    public function getCompany(Request $request){
        
        $UCODE          =   $request['UCODE'];
        $company_list   =   DB::select("select DISTINCT t2.CYID,t2.NAME from TBL_MST_USER t1,TBL_MST_COMPANY t2 where (t2.DEACTIVATED=0 or t2.DEACTIVATED is null) AND (t1.DEACTIVATED=0 or t1.DEACTIVATED is null) AND t1.CYID_REF=t2.CYID and t1.UCODE='$UCODE' ORDER BY t2.NAME");
        
        if(!empty($company_list)){
            echo "<option value=''>Company</option>";
            foreach($company_list as $key=>$list){
                $selected=$key==0?'selected="selected"':'';
                echo "<option $selected value='{$list->CYID}'>{$list->NAME}</option>";
            }
        }
        else{
            echo "Invalid";
        }
        exit();

    }

    public function getBranch(Request $request){

      // $CYID_REF       =   $request['CYID_REF'];
        $UCODE       =   $request['UCODE'];
        // $branch_list   =   DB::select("select t2.BRID,t2.BRNAME from TBL_MST_USER t1
        // inner join TBL_MST_BRANCH t2 on t1.CYID_REF=t2.CYID_REF AND t1.BRID_REF=t2.BRID AND t1.UCODE='$UCODE' AND t1.CYID_REF='$CYID_REF' AND (t2.DEACTIVATED=0 or t2.DEACTIVATED is null) ORDER BY t2.BRNAME");
        
        $branch_list   =   DB::select("select t1.BRID,t1.BRNAME from ((SELECT * FROM TBL_MST_BRANCH 
		WHERE BRID IN (SELECT MAPBRID_REF FROM TBL_MST_USER_BRANCH_MAP WHERE USERID_REF=(SELECT USERID FROM TBL_MST_USER WHERE UCODE='$UCODE')))) AS T1
        LEFT join  TBL_MST_USER t2 on t1.CYID_REF=t2.CYID_REF AND t2.BRID_REF=T1.BRID AND t2.UCODE='$UCODE' AND (t2.DEACTIVATED=0 or t2.DEACTIVATED is null) ORDER BY T1.BRNAME");

        echo "<option value=''>Branch</option>";
        foreach($branch_list as $key=>$list){
            $selected=$key==0?'selected="selected"':'';
            echo "<option $selected value='{$list->BRID}'>{$list->BRNAME}</option>";
        }
        die;
        
    }

    public function getFyear(Request $request){

     //   $CYID_REF       =   $request['CYID_REF'];
        $UCODE       =   $request['UCODE'];

       // $fyear_list   =   DB::select("select distinct t3.FYID,t3.FYDESCRIPTION from TBL_MST_USER t1
        //inner join TBL_MST_BRANCH t2 on t1.CYID_REF=t2.CYID_REF AND t1.BRID_REF=t2.BRID AND t1.UCODE='$UCODE' AND t1.CYID_REF='$CYID_REF' AND (t2.DEACTIVATED=0 or t2.DEACTIVATED is null)
        //inner join TBL_MST_FYEAR t3 on t2.FYID_REF=t3.FYID");


        $fyear_list = DB::table("TBL_MST_FYEAR")
      //  ->where('CYID_REF','=',$CYID_REF)
        ->where('STATUS','=','A')
        ->whereRaw('DEACTIVATED = 0 OR DEACTIVATED IS NULL')
        ->select('FYID','FYDESCRIPTION')
        ->orderBy('FYID','ASC')
        ->get();
        
        echo "<option value=''>Financial Year</option>";
        foreach($fyear_list as $key=>$list){
            $selected=$key==0?'selected="selected"':'';
            echo "<option $selected value='{$list->FYID}'>{$list->FYDESCRIPTION}</option>";
        }
        die;
        
    }

    public function existUser(Request $request){

        $UCODE    =   $request['UCODE'];
       
        $data_json  =   TblMstUser::select('UCODE')->whereRaw("UCODE='$UCODE' COLLATE SQL_Latin1_General_CP1_CS_AS AND (DEACTIVATED=0 or DEACTIVATED is null)")->first();
        
        //print_r(json_decode($data_json,true));exit();

       return json_decode($data_json,true);exit();

    }

    public function existPass(Request $request){

        $UCODE      =   $request['UCODE'];
        $PASSWORD   =   $request['PASSWORD'];
       
        $data_json  =   TblMstUser::select('UCODE')->whereRaw("UCODE='$UCODE' AND PASSWORD='$PASSWORD' COLLATE SQL_Latin1_General_CP1_CS_AS AND (DEACTIVATED=0 or DEACTIVATED is null) ")->first();
        
        //print_r(json_decode($data_json,true));die;
        return json_decode($data_json,true);exit();

    }

    public function existBranch(Request $request){
    
        $UCODE  =   $request['UCODE'];

        $branch_list    =   DB::select("SELECT 
        t1.BRID,t1.BRNAME from ((SELECT * FROM TBL_MST_BRANCH 
        WHERE BRID IN (SELECT MAPBRID_REF FROM TBL_MST_USER_BRANCH_MAP WHERE USERID_REF=(SELECT USERID FROM TBL_MST_USER WHERE UCODE='$UCODE')))) AS T1
        LEFT join  TBL_MST_USER t2 on t1.CYID_REF=t2.CYID_REF AND t2.BRID_REF=T1.BRID AND t2.UCODE='$UCODE' AND (t2.DEACTIVATED=0 or t2.DEACTIVATED is null) ORDER BY T1.BRNAME
        ");
  
        return Response::json(count($branch_list));  
        die; 
    }
  






}
