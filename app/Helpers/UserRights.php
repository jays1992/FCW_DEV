<?php 

namespace App\Helpers;
use DB;

class UserRights{

    public static function getRights($USERID_REF,$CYID_REF,$BRID_REF,$FYID_REF,$VTID_REF){ 

        $objRights  =   DB::table('TBL_MST_USERROLMAP')
        ->where('TBL_MST_USERROLMAP.USERID_REF','=',$USERID_REF)
        ->where('TBL_MST_USERROLMAP.CYID_REF','=',$CYID_REF)
        ->where('TBL_MST_USERROLMAP.BRID_REF','=',$BRID_REF)
        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$VTID_REF)
        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
        ->first();

        return $objRights;
        
    }  
}