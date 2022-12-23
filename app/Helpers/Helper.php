<?php 

namespace App\Helpers;
use DB;
use Auth;
use Session;

class Helper
{
    
    public static function shout(string $string)
    {
        return strtoupper($string);
    }

    public static function getMstInventoryClass($cyidRef, $bridRef, $fyidRef)
    {     
        $ObjMstInventoryClass =  DB::select('SELECT CLASSID, CLASS_CODE, CLASS_DESC FROM TBL_MST_INVENTORY_CLASS WHERE  ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ? order by CLASS_DESC ASC', ['A']);

        return $ObjMstInventoryClass;
    }

    //Main UoM / ALT UOM
    public static function getMstUOM($cyidRef, $bridRef, $fyidRef)
    {     
        $ObjMstUOM =  DB::select('SELECT UOMID, UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
        WHERE  CYID_REF = ?  AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
        order by UOMCODE ASC', [$cyidRef, 'A' ]);

        return $ObjMstUOM;
    }
    

    //Item Group
    public static function getMstItemGroup($cyidRef, $bridRef, $fyidRef)
    { 
        $ObjMstItemGroup =  DB::select('SELECT ITEMGID, GROUPCODE, GROUPNAME FROM TBL_MST_ITEMGROUP  
        WHERE  CYID_REF = ?  AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
        order by GROUPCODE ASC', [$cyidRef, 'A' ]);

        return $ObjMstItemGroup;
    }

//Item Category
public static function getMstItemCategory($cyidRef, $bridRef, $fyidRef)
{ 
    $ObjMstItemCategory =  DB::select('SELECT ICID, ICCODE, DESCRIPTIONS FROM TBL_MST_ITEMCATEGORY  
        WHERE  CYID_REF = ?  AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
        order by ICCODE ASC', [$cyidRef, 'A' ]);
    
    return $ObjMstItemCategory;
}



////default store 
public static function getMstStore($cyidRef, $bridRef, $fyidRef)
{ 
    
    $ObjMstStore =  DB::select('SELECT STID, STCODE, NAME FROM TBL_MST_STORE  
    WHERE  CYID_REF = ? AND BRID_REF = ?   AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
    order by STCODE ASC', [$cyidRef, $bridRef, 'A' ]);

    return $ObjMstStore;
}


//HSN 
public static function getMstHSN($cyidRef, $bridRef, $fyidRef)
{ 

    $ObjMstHSN  =  DB::select('SELECT HSNID,HSNCODE, HSNDESCRIPTION FROM TBL_MST_HSN  
        WHERE  CYID_REF = ?  AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
        order by HSNCODE ASC', [$cyidRef, 'A' ]);

    return $ObjMstHSN;
}



//Business Unit 
public static function getMstBusinessUnit($cyidRef, $bridRef, $fyidRef)
{ 

    $ObjMstBusUnit =  DB::select('SELECT BUID, BUCODE, BUNAME FROM TBL_MST_BUSINESSUNIT  
            WHERE  CYID_REF = ?  AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
            order by BUCODE ASC', [$cyidRef, 'A' ]);

    return $ObjMstBusUnit;
}

 
public static function getTableData($tableName,$selectFields,$cyidRef, $bridRef, $fyidRef,$ordBy,$ordDir){
    $selFlds = implode(", ", $selectFields);

    $WhereCyid  =   $cyidRef !=NULL?" AND CYID_REF ='$cyidRef'":"";
    $WhereBrid  =   $bridRef !=NULL?" AND BRID_REF ='$bridRef'":"";
    $WhereFyid  =   $fyidRef !=NULL?" AND FYID_REF ='$fyidRef'":"";

    // $ObjTbl =  DB::select('SELECT '.$selFlds.' FROM '.$tableName.'  
    // WHERE  CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
    // order by '.$ordBy.' '.$ordDir.'', [$cyidRef, $bridRef, $fyidRef, 'A' ]);

    $ObjTbl =  DB::select("SELECT $selFlds FROM $tableName WHERE STATUS='A' $WhereCyid $WhereBrid 
    AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) ORDER BY $ordBy  $ordDir");

    return $ObjTbl;
}


public static function getTableData2($tableName,$selectFields,$cyidRef, $bridRef, $fyidRef,$whereCondition=NULL,$ordBy,$ordDir)
{
    $selFlds = implode(", ", $selectFields);

    $strWhere = !is_null($whereCondition)? ' AND '.$whereCondition : '';

    $WhereCyid  =   $cyidRef !=NULL?" AND CYID_REF ='$cyidRef'":"";
    $WhereBrid  =   $bridRef !=NULL?" AND BRID_REF ='$bridRef'":"";
    $WhereFyid  =   $fyidRef !=NULL?" AND FYID_REF ='$fyidRef'":"";


    // $ObjTbl =  DB::select('SELECT '.$selFlds.' FROM '.$tableName.'  
    //         WHERE  CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?  '.$strWhere.'
    //         order by '.$ordBy.' '.$ordDir.'', [$cyidRef, $bridRef, $fyidRef, 'A' ]);

    $ObjTbl =  DB::select("SELECT $selFlds FROM $tableName WHERE STATUS='A' $strWhere  $WhereCyid $WhereBrid 
    AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) ORDER BY $ordBy  $ordDir ");

    return $ObjTbl;
}

//Attribute
public static function getMstAttribute($cyidRef, $bridRef, $fyidRef)
{ 

    $ObjMstAttribute =  DB::select('SELECT ATTID, ATTCODE, DESCRIPTIONS FROM TBL_MST_ATTRIBUTE  
            WHERE  CYID_REF = ?  AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
            order by DESCRIPTIONS ASC', [$cyidRef, 'A' ]);

    return $ObjMstAttribute;
}

//Attribute Value
public static function getMstAttributeValue($attid_ref)
{ 

    $ObjMstAttrVal=  DB::select('SELECT ATTVID,ATTID_REF,VALUE FROM TBL_MST_ATTRIBUTE_VAL  
            WHERE  ATTID_REF = ? order by VALUE ASC', [$attid_ref]);

    return $ObjMstAttrVal;
}

    //UDF For Items
    public static function getUdfForItems($cyidRef)
    { 
    
        $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_ITEM")->select('*')
                        ->whereIn('PARENTID',function($query) use ($cyidRef)
                                    {       
                                    $query->select('UDFITEMID')->from('TBL_MST_UDFFOR_ITEM')
                                                    ->where('STATUS','=','A')
                                                    ->where('PARENTID','=',0)
                                                    ->where('DEACTIVATED','=',0)
                                                    ->where('CYID_REF','=',$cyidRef);
                                                                        
                        })->where('DEACTIVATED','=',0)
                        ->where('STATUS','<>','C')                    
                        ->where('CYID_REF','=',$cyidRef);
                                  
                    

            $objUdfItemData = DB::table('TBL_MST_UDFFOR_ITEM')
                ->where('STATUS','=','A')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$cyidRef)
                ->union($ObjUnionUDF)
                ->get();    

        return $objUdfItemData;
    }

    // UDF for customer master
	public static function getUdfForCustomer($cyidRef){ 
  
		$ObjUnionUDF = DB::table("TBL_MST_UDFFOR_CM")->select('*')
                    ->whereIn('PARENTID',function($query) use ($cyidRef)
                                {       
                                $query->select('UDFCMID')->from('TBL_MST_UDFFOR_CM')
                                                ->where('STATUS','=','A')
                                                ->where('PARENTID','=',0)
                                                ->where('DEACTIVATED','=',0)
                                                ->where('CYID_REF','=',$cyidRef);
                                                                    
                    })->where('DEACTIVATED','=',0)
                    ->where('STATUS','<>','C')                    
                    ->where('CYID_REF','=',$cyidRef);
                              
                   

        $objUdfItemData = DB::table('TBL_MST_UDFFOR_CM')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$cyidRef)
            ->union($ObjUnionUDF)
            ->get();    

		return $objUdfItemData;
    }
    
    // UDF for VENDOR master
	public static function getUdfForVendor($cyidRef){ 
  
		$ObjUnionUDF2 = DB::table("TBL_MST_UDFFOR_VENDOR")->select('*')
                    ->whereIn('PARENTID',function($query) use ($cyidRef)
                                {       
                                $query->select('UDFVID')->from('TBL_MST_UDFFOR_VENDOR')
                                                ->where('STATUS','=','A')
                                                ->where('PARENTID','=',0)
                                                ->where('DEACTIVATED','=',0)
                                                ->where('CYID_REF','=',$cyidRef);
                                                                 
                    })->where('DEACTIVATED','=',0)
                    ->where('STATUS','<>','C')                    
                    ->where('CYID_REF','=',$cyidRef);           
                   

        $objUdfData2 = DB::table('TBL_MST_UDFFOR_VENDOR')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$cyidRef)
            ->union($ObjUnionUDF2)
            ->get();    

		return $objUdfData2;
	}
	
	public static function getUdfForDSP($cyidRef){ 
  
		$ObjUnionUDF2 = DB::table("TBL_MST_UDF_DSP")->select('*')
                    ->whereIn('PARENTID',function($query) use ($cyidRef)
                                {       
                                $query->select('UDF_DSPID')->from('TBL_MST_UDF_DSP')
                                                ->where('STATUS','=','A')
                                                ->where('PARENTID','=',0)
                                                ->where('DEACTIVATED','=',0)
                                                ->where('CYID_REF','=',$cyidRef);
                    })->where('DEACTIVATED','=',0)
                    ->where('STATUS','<>','C')                    
                    ->where('CYID_REF','=',$cyidRef);

        $objUdfData2 = DB::table('TBL_MST_UDF_DSP')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$cyidRef)
            ->union($ObjUnionUDF2)
            ->get();    

		return $objUdfData2;
	}
	
	public static function getAddSetting($CYID_REF,$TABLE_NAME){ 

        return  DB::table('TBL_MST_ADDL_TAB_SETTING')
                ->where('CYID_REF','=',$CYID_REF)
                ->where('TABLE_NAME','=',$TABLE_NAME)
                ->first();

    }
	
	    public static function get_gldetail_PaymentReceipt($ID,$Module){
        if($Module=='Receipt'){
          $objRCPTACCOUNT = DB::table('TBL_TRN_RECEIPT_ACCOUNT')  
                ->leftJoin('TBL_MST_GENERALLEDGER', 'TBL_TRN_RECEIPT_ACCOUNT.GLID_REF','=','TBL_MST_GENERALLEDGER.GLID')              
                ->where('TBL_TRN_RECEIPT_ACCOUNT.RECEIPTID_REF','=',$ID)
                ->select('TBL_MST_GENERALLEDGER.GLNAME')
                ->get()
                ->first();
        if($objRCPTACCOUNT){
                return $GL=$objRCPTACCOUNT->GLNAME;
                }else{
                return Null;
                }
        }else if($Module=='Payment'){
            $objRCPTACCOUNT = DB::table('TBL_TRN_PAYMENT_ACCOUNT')  
            ->leftJoin('TBL_MST_GENERALLEDGER', 'TBL_TRN_PAYMENT_ACCOUNT.GLID_REF','=','TBL_MST_GENERALLEDGER.GLID')              
            ->where('TBL_TRN_PAYMENT_ACCOUNT.PAYMENTID_REF','=',$ID)
            ->select('TBL_MST_GENERALLEDGER.GLNAME')
            ->get()
            ->first();
            if($objRCPTACCOUNT){  
                 return $GL=$objRCPTACCOUNT->GLNAME;
                }else{
                return Null;
                }
        }
        else if($Module=='JV'){
            $objRCPTACCOUNT = DB::table('TBL_TRN_FJRV01_ACC')  
            ->leftJoin('TBL_MST_GENERALLEDGER', 'TBL_TRN_FJRV01_ACC.GLID_REF','=','TBL_MST_GENERALLEDGER.GLID')              
            ->where('TBL_TRN_FJRV01_ACC.JVID_REF','=',$ID)
            ->select('TBL_MST_GENERALLEDGER.GLNAME')    
            ->first();
        

            if(isset($objRCPTACCOUNT) && $objRCPTACCOUNT->GLNAME==''){
            $objRCPTACCOUNT = DB::table('TBL_TRN_FJRV01_ACC')  
            ->leftJoin('TBL_MST_SUBLEDGER', 'TBL_TRN_FJRV01_ACC.GLID_REF','=','TBL_MST_SUBLEDGER.SGLID')              
            ->where('TBL_TRN_FJRV01_ACC.JVID_REF','=',$ID)
            ->select('TBL_MST_SUBLEDGER.SLNAME')
            ->get()
            ->first();
            if($objRCPTACCOUNT){  
                return $GL=$objRCPTACCOUNT->SLNAME;
               }else{
               return Null;
               }

            }else{         
            if($objRCPTACCOUNT){  
                 return $GL=$objRCPTACCOUNT->GLNAME;
                }else{
                return Null;
                }
            }
        }
        else if($Module=='MJV'){
            $objRCPTACCOUNT = DB::table('TBL_TRN_MJRV01_ACC')  
            ->leftJoin('TBL_MST_GENERALLEDGER', 'TBL_TRN_MJRV01_ACC.GLID_REF','=','TBL_MST_GENERALLEDGER.GLID')              
            ->where('TBL_TRN_MJRV01_ACC.MJVID_REF','=',$ID)
            ->select('TBL_MST_GENERALLEDGER.GLNAME')    
            ->first();
        

            if(isset($objRCPTACCOUNT) && $objRCPTACCOUNT->GLNAME==''){
            $objRCPTACCOUNT = DB::table('TBL_TRN_MJRV01_ACC')  
            ->leftJoin('TBL_MST_SUBLEDGER', 'TBL_TRN_MJRV01_ACC.GLID_REF','=','TBL_MST_SUBLEDGER.SGLID')              
            ->where('TBL_TRN_MJRV01_ACC.MJVID_REF','=',$ID)
            ->select('TBL_MST_SUBLEDGER.SLNAME')
            ->get()
            ->first();
            if($objRCPTACCOUNT){  
                return $GL=$objRCPTACCOUNT->SLNAME;
               }else{
               return Null;
               }

            }else{         
            if($objRCPTACCOUNT){  
                 return $GL=$objRCPTACCOUNT->GLNAME;
                }else{
                return Null;
                }
            }
        }

    }
	
	public static function checkCompany($CYID_REF,$str){
        $COMPANY_NAME   =   DB::table('TBL_MST_COMPANY')->where('STATUS','=','A')
                            ->where('CYID','=',$CYID_REF)
                            ->select('TBL_MST_COMPANY.NAME')
                            ->first()->NAME;

        $result = strpos(strtolower($COMPANY_NAME),$str)!== false?'1':'';
        return $result;
    }
	
	public function getBalance($ID){
        $Status     =   "A";
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
		
		$TaxStatus  =   DB::select('SELECT DBO.FN_GLOCBL(?,?)  AS CR_AMT', [$ID,Date('Y-m-d')]);
        
        $TaxStatus2  =   DB::select('SELECT DBO.FN_GLODBL(?,?) AS DR_AMT', [$ID,Date('Y-m-d')]);
		
		
    
       $Balance=($TaxStatus2[0]->DR_AMT - $TaxStatus[0]->CR_AMT);
	
	   if($Balance == '' || $Balance==0){
                return  $Balance='0.00';              
            }
			else{
				return  $Balance;
			}
        
    }

    public function getBalance_Show($ID){
        $Status     =   "A";
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
        
        $TaxStatus  =   DB::select('SELECT DBO.FN_GLOCBL(?,?)  AS CR_AMT', [$ID,Date('Y-m-d')]);
        
        $TaxStatus2  =   DB::select('SELECT DBO.FN_GLODBL(?,?) AS DR_AMT', [$ID,Date('Y-m-d')]);
		
		
       $Balance=($TaxStatus2[0]->DR_AMT - $TaxStatus[0]->CR_AMT);
            if($Balance == '' || $Balance==0){
                return  $Balance='0.00';              
            }
            else if($Balance > 0 ){
                return $Balance= number_format(abs($Balance),2).' Dr';   
            }else if($Balance < 0 ){
                return $Balance= number_format(abs($Balance),2).' Cr';              
            }
			else{
            return  $Balance='0.00';
			}
        
    }
	
	public static function check_approval_level($data,$id){

        $FORMID     =   $data['FORMID'];
        $VTID_REF   =   $data['VTID_REF'];
        $USERID     =   $data['USERID'];
        $CYID_REF   =   $data['CYID_REF'];
        $BRID_REF   =   $data['BRID_REF'];
        $FYID_REF   =   $data['FYID_REF'];
        $result     =   1;

        $new_user   =   DB::select('EXEC SP_APPROVAL_LAVEL ?,?,?,?, ?', [$USERID, $VTID_REF, $CYID_REF,$BRID_REF,$FYID_REF]);
        $new_user   =   isset($new_user[0]->LAVELS) && $new_user[0]->LAVELS !=""?$new_user[0]->LAVELS:0;

        $ROW_DATA   =   DB::select("SELECT right(ACTIONNAME,1) AS LAVEL  
                        FROM TBL_TRN_AUDITTRAIL 
                        WHERE VID='$id' AND VTID_REF='$VTID_REF'  AND CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' 
                        AND ACTID = (select MAX(ACTID) from TBL_TRN_AUDITTRAIL WHERE VID='$id' AND VTID_REF='$VTID_REF'  AND CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF') 
                        AND ACTIONNAME LIKE '%APPROVAL%'");
		
        if(isset($ROW_DATA) && !empty($ROW_DATA)){
            $result =   $new_user >= $ROW_DATA[0]->LAVEL?1:0;
        }

        return $result;

    }
	
	public static function get_user_level($data){
        $FORMID     =   $data['FORMID'];
        $VTID_REF   =   $data['VTID_REF'];
        $USERID     =   $data['USERID'];
        $CYID_REF   =   $data['CYID_REF'];
        $BRID_REF   =   $data['BRID_REF'];
        $FYID_REF   =   $data['FYID_REF'];
        
        $USER_LEVEL =   DB::select("select dbo.FN_APRL('$VTID_REF','$CYID_REF','$BRID_REF','$FYID_REF') AS LAVELS");
        $USER_LEVEL =   isset($USER_LEVEL[0]->LAVELS) && $USER_LEVEL[0]->LAVELS !=""?"APPROVAL".$USER_LEVEL[0]->LAVELS:'';
        
        $DATA_STATUS    =   array(
            'USER_LEVEL'=>$USER_LEVEL,
            'ADD'=>'Added',
            'EDIT'=>'Edited',
            'APPROVAL1'=>'First Level Approved',
            'APPROVAL2'=>'Second Level Approved',
            'APPROVAL3'=>'Third Level Approved',
            'APPROVAL4'=>'Fourth Level Approved',
            'APPROVAL5'=>'Final Approved',
            'AMENDMENT'=>'Final Approved',
            'CANCEL'=>'Cancelled',
            'A'=>1,
            'C'=>2,
            'R'=>3,
            'N'=>0,
        );

        return $DATA_STATUS;
    }
	
	public static function getCalculationHeader($data){

        $FORMID     =   $data['FORMID'];
        $VTID_REF   =   $data['VTID_REF'];
        $HEADING    =   $data['HEADING'];
        $USERID     =   $data['USERID'];
        $CYID_REF   =   $data['CYID_REF'];
        $BRID_REF   =   $data['BRID_REF'];

        $Module     =   DB::table('VW_MENU')
                        ->where('formid','=',$FORMID)
                        ->where('vtid_ref','=',$VTID_REF)
                        ->where('heading','=',$HEADING)
                        ->where('userid_ref','=',$USERID)
                        ->where('cyid_ref','=',$CYID_REF)
                        ->where('brid_ref','=',$BRID_REF)
                        ->select('moduleid')
                        ->first();

        $ModuleId   =   isset($Module->moduleid) && $Module->moduleid !=''?$Module->moduleid:'';

        $ArrayData  =   DB::select("SELECT 
                        CTID, CTCODE, CTDESCRIPTION,MODULEID_REF
                        FROM TBL_MST_CALCULATION  
                        WHERE  CYID_REF = '$CYID_REF' AND BRID_REF = '$BRID_REF'   
                        AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) 
                        AND (MODULEID_REF IS NOT NULL OR MODULEID_REF !='')
                        AND STATUS = 'A'");

        $CalTmp =   array();
        if(!empty($ArrayData)){
            foreach($ArrayData as $key=>$val){
                $module_array   =   explode(',',$val->MODULEID_REF);
                
                if(in_array($ModuleId,$module_array)){
                    $CalTmp[]=$val;
                }

            }
        }

        return $CalTmp;

    }

}//class