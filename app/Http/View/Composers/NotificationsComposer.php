<?php
namespace App\Http\View\Composers;

use Auth;
use DB;
use Session;
use Illuminate\Contracts\View\View;

class NotificationsComposer{
    public function compose(View $view){

        $USERID_REF =   Auth::user()->USERID;
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');  
        
        $query="SELECT PPLM_ID AS DOC_ID,PPLM_NO AS DOC_NO,PPLM_DATE AS DOC_DATE ,'product price' as FORM_NAME,'TBL_MST_PPLM' as TABLE_NAME,'PPLM_ID' AS COLUMN_NAME FROM TBL_MST_PPLM WHERE CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND STATUS='A' AND NOTIFY_STATUS='1'
        union
        SELECT SCHEMEID AS DOC_ID,SCHEME_NO AS DOC_NO,SCHEME_DATE AS DOC_DATE ,'scheme' as FORM_NAME,'TBL_MST_SCHEME_HDR' as TABLE_NAME,'SCHEMEID' AS COLUMN_NAME FROM TBL_MST_SCHEME_HDR WHERE CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND STATUS='A' AND NOTIFY_STATUS='1'
        union
        SELECT DISID AS DOC_ID,DISCODE AS DOC_NO,DOC_DATE AS DOC_DATE ,'discount' as FORM_NAME,'TBL_MST_DIS' as TABLE_NAME,'DISID' AS COLUMN_NAME FROM TBL_MST_DIS WHERE CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND STATUS='A' AND NOTIFY_STATUS='1'
        union
        SELECT PKMID AS DOC_ID,PKMCODE AS DOC_NO,PKMDATE AS DOC_DATE ,'package' as FORM_NAME,'TBL_MST_PACKAGE_MASTER' as TABLE_NAME,'PKMID' AS COLUMN_NAME FROM TBL_MST_PACKAGE_MASTER WHERE CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND STATUS='A' AND NOTIFY_STATUS='1'
        "; 
        
        $data_array = DB::select($query);
        $view->with('data_array',$data_array);
    }
}