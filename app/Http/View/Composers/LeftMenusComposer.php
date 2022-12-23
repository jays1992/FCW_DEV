<?php

namespace App\Http\View\Composers;

use Auth;
use DB;
use Session;
use Illuminate\Contracts\View\View;

class LeftMenusComposer
{
    public function compose(View $view){

        $menu_data  =   array();
        $USERID_REF =   Auth::user()->USERID;
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');  
        
        
        $query="SELECT * 
        FROM VW_MENU1 
        WHERE CYID_REF = '$CYID_REF' /*AND BRID_REF = '$BRID_REF'*/ AND USERID_REF = '$USERID_REF' AND DASHBOARD='1'  
        ORDER BY VT_SEQUENCE ASC, ranks ASC
        "; 
        
        $db_menu = DB::select($query);

        foreach ($db_menu as $index => $row) {
            $menu_data[$row->formid]['moduleid']=$row->moduleid;
            $menu_data[$row->formid]['modulename']=$row->modulename;
            $menu_data[$row->formid]['formid']=$row->formid;
            $menu_data[$row->formid]['formname']=$row->formname;
            $menu_data[$row->formid]['heading']=$row->heading;
            $menu_data[$row->formid]['vtid_ref']=$row->vtid_ref;
            $menu_data[$row->formid]['cyid_ref']=$row->cyid_ref;
            $menu_data[$row->formid]['brid_ref']=$row->brid_ref;
            $menu_data[$row->formid]['fyid_ref']=$row->fyid_ref;
        }

        $view->with('menu_data',$menu_data);
		
		Session::put('save','Submitting');
		Session::put('approve','Approving');
		Session::put('report_button','Loading');
		$ssrs_config=["REPORT_URL"=>"http://103.139.58.23:8181//ReportServer/","INSTANCE_NAME"=>"/ECW","username"=>"Administrator","password"=>"VRt+wDPuDYLwxxC"];        
        Session::put('ssrs_config',$ssrs_config);
		
		$report_dynamic_cols    =DB::select("SELECT FIELD8,FIELD9,FIELD10 FROM TBL_MST_ADDL_TAB_SETTING WHERE TABLE_NAME='ITEM_TAB_SETTING'");
        Session::put('report_dynamic_cols',isset($report_dynamic_cols[0]) ? $report_dynamic_cols[0] : '');
        
		
		$smtp_config=["host"=>"smtp.rediffmailpro.com","port"=>587,"username"=>"chandresh@bsquare.in","password"=>"Chandresh@321","from"=>"chandresh@bsquare.in"];        
        Session::put('smtp_config',$smtp_config);

    }
}