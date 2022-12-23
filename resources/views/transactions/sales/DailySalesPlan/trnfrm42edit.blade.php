@extends('layouts.app')
@section('content')
 
    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Daily Sales Plan (DSP)</a>
                </div><!--col-2-->
                <div class="col-lg-10 topnav-pd">
                        <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
                        <button class="btn topnavbt" id="btnSaveFormData" ><i class="fa fa-floppy-o"></i> Save</button>
                        <button style="display:none" class="btn topnavbt buttonload"> <i class="fa fa-refresh fa-spin"></i> {{Session::get('save')}}</button>
                        <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt" id="btnUndo"  ><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                        <button style="display:none" class="btn topnavbt buttonload_approve" > <i class="fa fa-refresh fa-spin"></i> {{Session::get('approve')}}</button>
                        <button class="btn topnavbt" id="btnApprove" {{ (isset($objRights->APPROVAL1) || isset($objRights->APPROVAL2) || isset($objRights->APPROVAL3) || isset($objRights->APPROVAL4) || isset($objRights->APPROVAL5)) &&  ($objRights->APPROVAL1||$objRights->APPROVAL2||$objRights->APPROVAL3||$objRights->APPROVAL4||$objRights->APPROVAL5) == 1 ? '' : 'disabled'}} ><i class="fa fa-thumbs-o-up"></i> Approved</button>
                        <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                        <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                </div>
            </div><!--row-->
    </div>
  <form id="form_data" method="POST"  > 
  @CSRF  
  {{isset($objMstResponse->DSPID) ? method_field('PUT') : '' }}
    
  <div class="container-fluid filter">

	<div class="inner-form">
	
		<div class="row">
			<div class="col-lg-2 pl"><p>Daily Sales Plan No*</p></div>
			<div class="col-lg-2 pl">
        <input {{$InputStatus}} type="text" name="DSPNO" id="DSPNO" value="{{ isset($objMstResponse->DSPNO)?$objMstResponse->DSPNO:'' }}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase"  >
			</div>
			
			<div class="col-lg-2 pl "><p>Daily Sales Plan Date*</p></div>
			<div class="col-lg-2 pl">
				    <input {{$InputStatus}} type="date" name="DSPDT" id="DSPDT" onchange="checkPeriodClosing('{{$FormId}}',this.value,1)" value="{{ isset($objMstResponse->DSPDT)?$objMstResponse->DSPDT:'' }}" class="form-control mandatory"  placeholder="dd/mm/yyyy" >
      </div>
			<div class="col-lg-2 pl"><p>Customer*</p></div>
			<div class="col-lg-2 pl">
                <input {{$InputStatus}} type="text" name="SubGl_popup" id="txtsubgl_popup" class="form-control mandatory" value="{{ isset($objslcode2->SGLCODE)?$objslcode2->SGLCODE:'' }} {{ isset($objslcode2->SLNAME)?'-'.$objslcode2->SLNAME:'' }}"  autocomplete="off" readonly/>
                <input type="hidden" name="SLID_REF" id="SLID_REF" value="{{ isset($objMstResponse->SLID_REF)?$objMstResponse->SLID_REF:'' }}" class="form-control" autocomplete="off" />
                <input type="hidden" name="GLID_REF" id="GLID_REF"  value="{{ isset($objMstResponse->GLID_REF)?$objMstResponse->GLID_REF:'' }}" class="form-control" autocomplete="off" />
                <input type="hidden" name="hdnmaterial" id="hdnmaterial" class="form-control" autocomplete="off" />   
			</div>
		</div>		
		
	</div>

	<div class="container-fluid">

		<div class="row">
			<ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#Material" id="MAT_TAB" >Material</a></li>
				<li><a data-toggle="tab" href="#udf" id="UDF_TAB" >UDF</a></li>
			</ul>
			
			
			
			<div class="tab-content">

				<div id="Material" class="tab-pane fade in active">
        <div class="row"><div class="col-lg-4" style="padding-left: 15px;">Note:- 1 row mandatory in Material Tab </div></div>
					<div class="table-responsive table-wrapper-scroll-y" style="height:500px;margin-top:10px;" >
						<table id="example2" class="display nowrap table table-striped table-bordered itemlist w-200" width="100%" style="height:auto !important;">
							<thead id="thead1"  style="position: sticky;top: 0">
								  <tr>
									<th>SO No<input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1" value="{{ $objList1Count }}"></th>
									<th>Item Code</th>
									<th>Item Name</th>
                  <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</th>
                  <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</th>
                  <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</th>
									<th>UOM</th>
									<th>Item Specification</th>
									<th>SO Qty</th>
									<th>Balance SO Qty</th>
									<th>Expected Dispatch Qty</th>
									<th>Action</th>
								  </tr>
							</thead>
							<tbody>
                @if(!empty($objList1))
                  @foreach($objList1 as $key => $row)
                  @php

                    $tmpSOID_REF = $row->SOID_REF;
                    $tmpSQA_REF = (empty($row->SQID_REF) || is_null($row->SQID_REF)) ? 0 : $row->SQID_REF ;
                    $tmpITEMID_REF = $row->ITEMID_REF;
                    $tmpSEQID_REF = (empty($row->SEID_REF) || is_null($row->SEID_REF))? 0 : $row->SEID_REF ;

                    $CustomId = $tmpSOID_REF.$tmpSQA_REF.$tmpITEMID_REF.$tmpSEQID_REF;  //custom id for unique row/item

                  @endphp
								  <tr  class="participantRow">
                    <td hidden><input type="text" name="recordId_{{ $key }}" id="txtrecordId_{{ $key }}" class="form-control" value="{{ $CustomId }}"  autocomplete="off"  readonly/></td>
                    
                    <td hidden><input type="text" name="SQA_REF_{{ $key }}" id="txtSQA_REF_{{ $key }}" value="{{ $row->SQID_REF }}" class="form-control"  autocomplete="off"  readonly/></td>
                    <td hidden><input type="text" name="SEQID_REF_{{ $key }}" id="txtSEQID_REF_{{ $key }}" value="{{ $row->SEID_REF }}"  class="form-control"  autocomplete="off"  readonly/></td>
                    
                    <td><input {{$InputStatus}} type="text" name="SOrd_popup_{{ $key }}" id="txtSOrd_popup_{{ $key }}" value="{{ $row->SONO }}" class="form-control"  autocomplete="off"  readonly/></td>
                    <td hidden><input type="text" name="SOrdID_{{ $key }}" id="HDNSOrdID_{{ $key }}" value="{{ $row->SOID_REF }}" class="form-control" autocomplete="off" /></td>
                    <td><input {{$InputStatus}} type="text" name="popupITEMID_{{ $key }}" id="popupITEMID_{{ $key }}" value="{{ $row->ICODE }}" class="form-control"  autocomplete="off"  readonly/></td>                    
                    <td hidden><input type="text" name="ITEMID_REF_{{ $key }}" id="ITEMID_REF_{{ $key }}" value="{{ $row->ITEMID_REF }}"  class="form-control" autocomplete="off" /></td>
                    <td><input {{$InputStatus}} type="text" name="ItemName_{{ $key }}" id="ItemName_{{ $key }}" class="form-control" value="{{ $row->NAME }}"   autocomplete="off"  readonly/></td>
                    
                    <td {{$AlpsStatus['hidden']}}><input {{$InputStatus}} type="text" name="Alpspartno_{{$key}}" id="Alpspartno_{{$key}}" class="form-control"  autocomplete="off" value="{{ isset($row->ALPS_PART_NO)?$row->ALPS_PART_NO:''}}" readonly/></td>
                    <td {{$AlpsStatus['hidden']}}><input {{$InputStatus}} type="text" name="Custpartno_{{$key}}" id="Custpartno_{{$key}}" class="form-control"  autocomplete="off" value="{{ isset($row->CUSTOMER_PART_NO)?$row->CUSTOMER_PART_NO:''}}" readonly/></td>
                    <td {{$AlpsStatus['hidden']}}><input {{$InputStatus}} type="text" name="OEMpartno_{{$key}}"  id="OEMpartno_{{$key}}" class="form-control"  autocomplete="off"  value="{{ isset($row->OEM_PART_NO)?$row->OEM_PART_NO:'' }}" readonly/></td>

                    <td><input {{$InputStatus}} type="text" name="popupMUOM_{{ $key }}" id="popupMUOM_{{ $key }}" value="{{ $row->UOMCODE }} - {{ $row->DESCRIPTIONS }}"  class="form-control"  autocomplete="off"  readonly/></td>
                    <td hidden><input type="hidden" name="MAIN_UOMID_REF_{{ $key }}" id="MAIN_UOMID_REF_{{ $key }}" value="{{ $row->UOMID_REF }}" class="form-control"  autocomplete="off" /></td>
                    <td><input {{$InputStatus}} type="text" name="Itemspec_{{ $key }}" id="Itemspec_{{ $key }}" value="{{ $row->ITEMSPECI }}"  class="form-control"  autocomplete="off"  /></td>
                    <td><input {{$InputStatus}} type="text" name="SO_QTY_{{ $key }}" id="SO_QTY_{{ $key }}" value="{{ $row->SO_QTY }}"  class="form-control three-digits" maxlength="13"  autocomplete="off"  readonly /></td>
                    <td ><input {{$InputStatus}} type="text" name="BAL_SOQTY_{{ $key }}" id="BAL_SOQTY_{{ $key }}" value="{{ $row->BAL_SOQTY }}" class="form-control three-digits" maxlength="13"  autocomplete="off"  readonly/></td>
                    <td ><input {{$InputStatus}} type="text" name="EXP_DIS_QTY_{{ $key }}" id="EXP_DIS_QTY_{{ $key }}" value="{{ $row->EXPDPQTY }}" class="form-control three-digits" maxlength="13"  autocomplete="off"  /></td>
                    <td align="center" >
                      <button {{$InputStatus}} class="btn add material" title="add" data-toggle="tooltip" type="button" ><i class="fa fa-plus"></i></button>
                      <button {{$InputStatus}} class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button>
                    </td>
                  </tr>
                  @endforeach
                @endif  
							</tbody>
					  </table>
					</div>	
				</div>

				<div id="udf" class="tab-pane fade">
					<div class="table-responsive table-wrapper-scroll-y " style="margin-top:10px;height:280px;width:50%;">
						<table id="example3" class="display nowrap table table-striped table-bordered itemlist" style="height:auto !important;">
							<thead id="thead1"  style="position: sticky;top: 0">
							  <tr >
								<th>UDF Fields<input class="form-control" type="hidden" name="Row_Count2" id ="Row_Count2" value="{{ $objudfCount }}" ></th>
								<th>Value / Comments</th>
							  </tr>
							</thead>
							<tbody>
              @foreach($objUDF as $udfkey => $udfrow)
                  <tr  class="participantRow2">
                    <td>
                      <input {{$InputStatus}} name={{"udffie_popup_".$udfkey}} id={{"txtudffie_popup_".$udfkey}} value="{{$udfrow->LABEL}}" class="form-control @if ($udfrow->ISMANDATORY==1) mandatory @endif" autocomplete="off" maxlength="100" disabled/>
                    </td>

                    <td hidden>
                      <input type="text" name='{{"udffie_".$udfkey}}' id='{{"hdnudffie_popup_".$udfkey}}' value="{{$udfrow->UDF_DSPID_REF}}" class="form-control" maxlength="100" />
                    </td>

                    <td hidden>
                      <input type="text" name={{"udffieismandatory_".$udfkey}} id={{"udffieismandatory_".$udfkey}} class="form-control" maxlength="100" value="{{$udfrow->ISMANDATORY}}" />
                    </td>

                    <td id="{{"tdinputid_".$udfkey}}">
                      @php
                        
                        $dynamicid = "udfvalue_".$udfkey;
                        $chkvaltype = strtolower($udfrow->VALUETYPE); 

                      if($chkvaltype=='date'){

                        $strinp = '<input '.$InputStatus.'  type="date" placeholder="dd/mm/yyyy" name="'.$dynamicid.'" id="'.$dynamicid.'" class="form-control" value="'.$udfrow->VALUE.'" /> ';       

                      }else if($chkvaltype=='time'){

                          $strinp= '<input '.$InputStatus.' type="time" placeholder="h:i" name="'.$dynamicid.'" id="'.$dynamicid.'" class="form-control"  value="'.$udfrow->VALUE.'"/> ';

                      }else if($chkvaltype=='numeric'){
                      $strinp = '<input '.$InputStatus.' type="text" name="'.$dynamicid. '" id="'.$dynamicid.'" class="form-control" value="'.$udfrow->VALUE.'"/> ';

                      }else if($chkvaltype=='text'){

                      $strinp = '<input '.$InputStatus.' type="text" name="'.$dynamicid. '" id="'.$dynamicid.'" class="form-control" value="'.$udfrow->VALUE.'"/> ';

                      }else if($chkvaltype=='boolean'){
                          $boolval = ''; 
                          if($udfrow->VALUE=='on' || $udfrow->VALUE=='1' ){
                            $boolval="checked";
                          }
                          $strinp = '<input '.$InputStatus.' type="checkbox" name="'.$dynamicid. '" id="'.$dynamicid.'" class=""  '.$boolval.' /> ';

                      }else if($chkvaltype=='combobox'){
                        $strinp='';
                      $txtoptscombo =   strtoupper($udfrow->DESCRIPTIONS); ;
                      $strarray =  explode(',',$txtoptscombo);
                      $opts = '';
                      $chked='';
                        for ($i = 0; $i < count($strarray); $i++) {
                          $chked='';
                          if($strarray[$i]==$udfrow->VALUE){
                            $chked='selected="selected"';
                          }
                           $opts = $opts.'<option value="'.$strarray[$i].'"'.$chked.'  >'.$strarray[$i].'</option> ';
                        }

                        $strinp = '<select '.$InputStatus.' name="'.$dynamicid.'" id="'.$dynamicid.'" class="form-control" >'.$opts.'</select>' ;


                      }
                      echo $strinp;
                      @endphp
                    </td>
                  </tr>
                  @endforeach
					   
							</tbody>
						</table>
					</div>
				</div>
				
			</div>
		</div>
		
	</div>
	
</div>

<!-- </div> -->
</form>
@endsection
@section('alert')
<div id="alert" class="modal"  role="dialog"  data-backdrop="static" >
  <div class="modal-dialog"  >
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='closePopup' >&times;</button>
        <h4 class="modal-title">System Alert Message</h4>
      </div>
      <div class="modal-body">
	      <h5 id="AlertMessage" ></h5>
        <div class="btdiv">
            <button class="btn alertbt" name='YesBtn' id="YesBtn" data-funcname="fnSaveData"><div id="alert-active" class="activeYes"></div>Yes</button>
            <button class="btn alertbt" name='NoBtn' id="NoBtn"   data-funcname="fnUndoNo" ><div id="alert-active" class="activeNo"></div>No</button>
            <button class="btn alertbt" name='OkBtn' id="OkBtn" style="display:none;margin-left: 90px;"><div id="alert-active" class="activeOk"></div>OK</button>
            <button onclick="getFocus()" class="btn alertbt" name='OkBtn1' id="OkBtn1" style="display:none;margin-left: 90px;"><div id="alert-active" class="activeOk1"></div>OK</button>
            <input type="hidden" id="FocusId" >
        </div>
		    <div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<!-- Alert -->
<!-- Customer  Dropdown -->
<div id="customer_popus" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='customer_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Customer</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="GlCodeTable" class="display nowrap table  table-striped table-bordered">
    <thead>
    <tr>
      <th class="ROW1">Select</th> 
      <th class="ROW2">Code</th>
      <th class="ROW3">Description</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="ROW1"><span class="check_th">&#10004;</span></td>
        <td class="ROW2"><input type="text" id="customercodesearch" class="form-control" onkeyup="CustomerCodeFunction('{{$FormId}}')"></td>
        <td class="ROW3"><input type="text" id="customernamesearch" class="form-control" onkeyup="CustomerNameFunction('{{$FormId}}')"></td>
    </tr>
    </tbody>
    </table>
      <table id="GlCodeTable2" class="display nowrap table  table-striped table-bordered">
        <thead id="thead2">
        </thead>
        <tbody id="tbody_subglacct">
        
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- CUSTOMER Dropdown-->
<!-- Sales Order Dropdown -->
<div id="SOrderPopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='SOrd_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Sales Order</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="SalesOrderTable" class="display nowrap table  table-striped table-bordered">
    <thead>
          <tr id="none-select" class="searchalldata" >
            
            <td> <input type="hidden" name="fieldid" id="hdn_sordid"/>
            <input type="hidden" name="fieldid2" id="hdn_sordid2"/></td>
          </tr>
          <tr>
            <th class="ROW1">Select</th> 
            <th class="ROW2">SO No</th>
            <th class="ROW3">SO Date</th>
          </tr>
    </thead>
    <tbody>
      <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="SalesOrdercodesearch" class="form-control" onkeyup="SalesOrderCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="SalesOrdernamesearch" class="form-control" onkeyup="SalesOrderNameFunction()"></td>
      </tr>
    </tbody>
    </table>
      <table id="SalesOrderTable2" class="display nowrap table  table-striped table-bordered">
        <thead id="thead2">

        </thead>
        <tbody id="tbody_SO">     
        
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Sales Order Dropdown-->
<!-- Item Code Dropdown -->
<div id="ITEMIDpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:90%;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='ITEMID_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Item Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="ItemIDTable" class="display nowrap table  table-striped table-bordered" style="width:100%;" >
    <thead>
      <tr id="none-select" class="searchalldata" text>
        <td hidden> 
            <input type="text" name="fieldid" id="hdn_ItemID"/>
            <input type="text" name="fieldid2" id="hdn_ItemID2"/>
            <input type="text" name="fieldid3" id="hdn_ItemID3"/>
            <input type="text" name="fieldid4" id="hdn_ItemID4"/>
            <input type="text" name="fieldid5" id="hdn_ItemID5"/>
            <input type="text" name="fieldid6" id="hdn_ItemID6"/>
            <input type="text" name="fieldid7" id="hdn_ItemID7"/>
            <input type="text" name="fieldid8" id="hdn_ItemID8"/>
            <input type="text" name="fieldid9" id="hdn_ItemID9"/>
            <input type="text" name="fieldid10" id="hdn_ItemID10"/>
            <input type="text" name="fieldid11" id="hdn_ItemID11"/>
            <input type="text" name="fieldid12" id="hdn_ItemID12"/>
        </td>
      </tr>
      <!-- <tr class="topnav"><button class="btn topnavbt" id="btnOk"><i class="fa fa-check"></i> OK</button></tr> -->
      <tr>
            <th style="width:8%;text-align:center;" id="all-check">Select</th>
            <th style="width:10%;">Item Code</th>
            <th style="width:10%;">Name</th>
            <th style="width:8%;">Main UOM</th>
            <th style="width:8%;">Item Specification</th>
            <th style="width:8%;">SO Qty</th>
            <th style="width:8%;">Balance SO Qty</th>
            <th style="width:8%;">Exp. Dis. Qty</th>
            <th style="width:8%;">Business Unit</th>
            <th style="width:8%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</th>
            <th style="width:8%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</th>
            <th style="width:8%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</th>
      </tr>
    </thead>
    <tbody>
    <tr>
    <td style="width:8%;text-align:center;"><input type="checkbox"  class="js-selectall" data-target=".js-selectall1" /></td>
    <td style="width:10%;"><input type="text" id="Itemcodesearch" class="form-control" autocomplete="off" onkeyup="ItemCodeFunction()" ></td>
    <td style="width:10%;"><input type="text" id="Itemnamesearch" class="form-control" autocomplete="off" onkeyup="ItemNameFunction()" ></td>
    <td style="width:8%;"><input type="text" id="ItemUOMsearch" class="form-control"   autocomplete="off" onkeyup="ItemUOMFunction()" ></td>
    <td style="width:8%;"><input type="text" id="ItemSpecsearch" class="form-control"  autocomplete="off" onkeyup="ItemSpecFunction()" ></td>
    <td style="width:8%;"><input type="text" id="ItemQTYsearch" class="form-control"   autocomplete="off" onkeyup="ItemQTYFunction()" ></td>
    <td style="width:8%;"><input type="text" id="ItemBalSoQtysearch" class="form-control" autocomplete="off" onkeyup="ItemBalSoQtyFunction()" ></td>
    <td style="width:8%;"><input type="text" id="ItemExpDisQtysearch" class="form-control" autocomplete="off" onkeyup="ItemExpDisQtyFunction()" ></td>
    <td style="width:8%;"><input type="text" id="ItemBUsearch" class="form-control" autocomplete="off" onkeyup="ItemBUFunction()"></td>
    <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemAPNsearch" class="form-control" autocomplete="off" onkeyup="ItemAPNFunction()"></td>
    <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemCPNsearch" class="form-control" autocomplete="off" onkeyup="ItemCPNFunction()"></td>
    <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemOEMPNsearch" class="form-control" autocomplete="off" onkeyup="ItemOEMPNFunction()"></td>
    
    </tr>
    </tbody>
    </table>
      <table id="ItemIDTable2" class="display nowrap table  table-striped table-bordered" style="width:100%;" >
        <thead id="thead2">

        </thead>
        <tbody id="tbody_ItemID">     
          
          
        </tbody>
      </table>
      <div class="loader" id="item_loader" style="display:none;"></div>
      <input type="hidden" id="FetchItem" >
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Item Code Dropdown-->


@endsection


@push('bottom-css')
<style>

#ItemIDcodesearch {
  background-image: url('/css/searchicon.png');
  background-position: 10px 10px;
  background-repeat: no-repeat;
  font-size: 11px;
  padding: 5px 5px 5px 5px;
  border: 1px solid #ddd;
  margin-bottom: 5px;
}
#ItemIDnamesearch {
  background-image: url('/css/searchicon.png');
  background-position: 10px 10px;
  background-repeat: no-repeat;
  font-size: 11px;
  padding: 5px 5px 5px 5px;
  border: 1px solid #ddd;
  margin-bottom: 5px;
}

#ItemIDTable {
  border-collapse: collapse;
  width: 950px;
  border: 1px solid #ddd;
  font-size: 11px;
}

#ItemIDTable th {
    text-align: center;
    padding: 5px;
   
    font-size: 11px;
    
    color: #0f69cc;
    font-weight: 600;
}

#ItemIDTable td {
    text-align: center;
    padding: 5px;
    font-size: 11px;
   
    font-weight: 600;
}

#ItemIDTable2 {
  border-collapse: collapse;
  width: 1050px;
  border: 1px solid #ddd;
  font-size: 11px;
}

#ItemIDTable2 th{
    text-align: left;
    padding: 5px;
   
    font-size: 11px;
   
    color: #0f69cc;
    font-weight: 600;
}

#ItemIDTable2 td {
  text-align: left;
    padding: 5px;
    font-size: 11px;
   
    font-weight: 600;
    width: 16%;
}

</style>
@endpush
@push('bottom-scripts')
<script>

//Sub GL Account Starts
//------------------------


$("#txtsubgl_popup").click(function(event){      
  var CODE = ''; 
    var NAME = ''; 
    var FORMID = "{{$FormId}}";
    loadCustomer(CODE,NAME,FORMID);
    $("#customer_popus").show();
    event.preventDefault();
  });

  $("#customer_closePopup").click(function(event){
    $("#customer_popus").hide();
    $("#customercodesearch").val(''); 
    $("#customernamesearch").val(''); 
   
    event.preventDefault();
  });

function bindSubLedgerEvents(){
        $('.clssubgl').click(function(){
    
            var id = $(this).attr('id');
            var txtval =    $("#txt"+id+"").val();
            var texdesc =   $("#txt"+id+"").data("desc");
            var glid    =   $("#txt"+id+"").data("desc2");
            var oldSLID =   $("#SLID_REF").val();
            var MaterialClone = $('#hdnmaterial').val();
            $("#txtsubgl_popup").val(texdesc);
            $("#txtsubgl_popup").blur();
            $("#SLID_REF").val(txtval);
            $("#GLID_REF").val(glid);
            if (txtval != oldSLID)
            {
                $('#Material').html(MaterialClone);
                $('#Row_Count1').val('1');
            }
            $("#customer_popus").hide();
            $("#customercodesearch").val(''); 
            $("#customernamesearch").val(''); 
           
              
            
            event.preventDefault();
        });
  }


  function CustomerCodeFunction(FORMID) {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("customercodesearch");
        filter = input.value.toUpperCase();
        
      if(filter.length == 0)
        {
          var CODE = ''; 
          var NAME = ''; 
          loadCustomer(CODE,NAME,FORMID); 
        }
        else if(filter.length >= 3)
        {
          var CODE = filter; 
          var NAME = ''; 
          loadCustomer(CODE,NAME,FORMID); 
        }
        else
        {
          table = document.getElementById("GlCodeTable2");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
          td = tr[i].getElementsByTagName("td")[0];
          if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
              tr[i].style.display = "";
            } else {
              tr[i].style.display = "none";
            }
          }       
        }
      }
    }

  function CustomerNameFunction(FORMID) {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("customernamesearch");
        filter = input.value.toUpperCase();
        if(filter.length == 0)
        {
          var CODE = ''; 
          var NAME = ''; 
          loadCustomer(CODE,NAME,FORMID);
        }
        else if(filter.length >= 3)
        {
          var CODE = ''; 
          var NAME = filter; 
          loadCustomer(CODE,NAME,FORMID);  
        }
        else
        {
          table = document.getElementById("GlCodeTable2");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
          td = tr[i].getElementsByTagName("td")[1];
          if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
              tr[i].style.display = "";
            } else {
              tr[i].style.display = "none";
            }
          }       
        }
      }
    }

  function loadCustomer(CODE,NAME,FORMID){
      var url	=	'<?php echo asset('');?>transaction/'+FORMID+'/getsubledger';
        $("#tbody_subglacct").html('');
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
        $.ajax({
          url:url,
          type:'POST',
          data:{'CODE':CODE,'NAME':NAME},
          success:function(data) {
          $("#tbody_subglacct").html(data); 
          bindSubLedgerEvents(); 

          },
          error:function(data){
          console.log("Error: Something went wrong.");
          $("#tbody_subglacct").html('');                        
          },
        });
    }


//Sub GL Account Ends
//------------------------
  //Sales Order Dropdown
  let sotid = "#SalesOrderTable2";
      let sotid2 = "#SalesOrderTable";
      let salesOrderheaders = document.querySelectorAll(sotid2 + " th");

      // Sort the table element when clicking on the table headers
      salesOrderheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(sotid, ".clssqid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function SalesOrderCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("SalesOrdercodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("SalesOrderTable2");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
          td = tr[i].getElementsByTagName("td")[0];
          if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
              tr[i].style.display = "";
            } else {
              tr[i].style.display = "none";
            }
          }       
        }
      }

  function SalesOrderNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("SalesOrdernamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("SalesOrderTable2");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
          td = tr[i].getElementsByTagName("td")[1];
          if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
              tr[i].style.display = "";
            } else {
              tr[i].style.display = "none";
            }
          }       
    }
  }

   $('#Material').on('click','[id*="txtSOrd_popup"]',function(event){
      
        if($.trim($("#SLID_REF").val())==""){
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text('Please select SL first.');
            $("#alert").modal('show');
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk1');
            return false;
        }


        var customid = $("#SLID_REF").val();
        if(customid!=''){
            $("#tbody_SO").html('');
            $("#tbody_SO").html('<tr><td colspan="2">Please wait...</td></tr>');
                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
            })
            $.ajax({
                url:'{{route("transaction",[$FormId,"getSalesOrder"])}}',
                type:'POST',
                data:{'id':customid},
                success:function(data) {
                  $("#tbody_SO").html(data);
                  BindSalesOrder();
                },
                error:function(data){
                  console.log("Error: Something went wrong.");
                  $("#tbody_SO").html('');
                },
            });
        }

        $("#SOrderPopup").show();
        var id = $(this).attr('id');
        var id2 = $(this).parent().parent().find('[id*="HDNSOrdID"]').attr('id');

        $('#hdn_sordid').val(id);
        $('#hdn_sordid2').val(id2);
      });

      $("#SOrd_closePopup").click(function(event){
        $("#SOrderPopup").hide();
      });

      function BindSalesOrder(){

      $(".clssordid").click(function(){
          var fieldid = $(this).attr('id');
          var txtval =    $("#txt"+fieldid+"").val();
          var texdesc =   $("#txt"+fieldid+"").data("desc");
          
          var txtid= $('#hdn_sordid').val();
          var txt_id2= $('#hdn_sordid2').val();

          $('#'+txtid).val(texdesc);
          $('#'+txt_id2).val(txtval);
          $("#SOrderPopup").hide();
          
          $("#SalesOrdercodesearch").val(''); 
          $("#SalesOrdernamesearch").val(''); 
        
          event.preventDefault();
        });
      }

  //Sales Order Dropdown Ends
//------------------------ 
//-----------so popup end
//------------------------
  //Item ID Dropdown
      let itemtid = "#ItemIDTable2";
      let itemtid2 = "#ItemIDTable";
      let itemtidheaders = document.querySelectorAll(itemtid2 + " th");

      // Sort the table element when clicking on the table headers
      itemtidheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(itemtid, ".clsitemid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function ItemCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Itemcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("ItemIDTable2");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
          td = tr[i].getElementsByTagName("td")[1];
          if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
              tr[i].style.display = "";
            } else {
              tr[i].style.display = "none";
            }
          }       
        }
      }

      function ItemNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Itemnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("ItemIDTable2");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
          td = tr[i].getElementsByTagName("td")[2];
          if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
              tr[i].style.display = "";
            } else {
              tr[i].style.display = "none";
            }
          }       
        }
      }

      function ItemUOMFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("ItemUOMsearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("ItemIDTable2");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
          td = tr[i].getElementsByTagName("td")[3];
          if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
              tr[i].style.display = "";
            } else {
              tr[i].style.display = "none";
            }
          }       
        }
      }

      function ItemSpecFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("ItemSpecsearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("ItemIDTable2");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
          td = tr[i].getElementsByTagName("td")[4];
          if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
              tr[i].style.display = "";
            } else {
              tr[i].style.display = "none";
            }
          }       
        }
      }

      function ItemQTYFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("ItemQTYsearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("ItemIDTable2");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
          td = tr[i].getElementsByTagName("td")[5];
          if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
              tr[i].style.display = "";
            } else {
              tr[i].style.display = "none";
            }
          }       
        }
      }

      function ItemBalSoQtyFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("ItemBalSoQtysearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("ItemIDTable2");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
          td = tr[i].getElementsByTagName("td")[6];
          if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
              tr[i].style.display = "";
            } else {
              tr[i].style.display = "none";
            }
          }       
        }
      }

      function ItemExpDisQtyFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("ItemExpDisQtysearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("ItemIDTable2");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
          td = tr[i].getElementsByTagName("td")[7];
          if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
              tr[i].style.display = "";
            } else {
              tr[i].style.display = "none";
            }
          }       
        }
      }

      function ItemBUFunction() {
	var input, filter, table, tr, td, i, txtValue;
	input = document.getElementById("ItemBUsearch");
	filter = input.value.toUpperCase();
	table = document.getElementById("ItemIDTable2");
	tr = table.getElementsByTagName("tr");
	for (i = 0; i < tr.length; i++) {
	  td = tr[i].getElementsByTagName("td")[7];
	  if (td) {
		txtValue = td.textContent || td.innerText;
		if (txtValue.toUpperCase().indexOf(filter) > -1) {
		  tr[i].style.display = "";
		} else {
		  tr[i].style.display = "none";
		}
	  }       
	}
}

function ItemAPNFunction() {
	var input, filter, table, tr, td, i, txtValue;
	input = document.getElementById("ItemAPNsearch");
	filter = input.value.toUpperCase();
	table = document.getElementById("ItemIDTable2");
	tr = table.getElementsByTagName("tr");
	for (i = 0; i < tr.length; i++) {
	  td = tr[i].getElementsByTagName("td")[8];
	  if (td) {
		txtValue = td.textContent || td.innerText;
		if (txtValue.toUpperCase().indexOf(filter) > -1) {
		  tr[i].style.display = "";
		} else {
		  tr[i].style.display = "none";
		}
	  }       
	}
}

function ItemCPNFunction() {
	var input, filter, table, tr, td, i, txtValue;
	input = document.getElementById("ItemCPNsearch");
	filter = input.value.toUpperCase();
	table = document.getElementById("ItemIDTable2");
	tr = table.getElementsByTagName("tr");
	for (i = 0; i < tr.length; i++) {
	  td = tr[i].getElementsByTagName("td")[9];
	  if (td) {
		txtValue = td.textContent || td.innerText;
		if (txtValue.toUpperCase().indexOf(filter) > -1) {
		  tr[i].style.display = "";
		} else {
		  tr[i].style.display = "none";
		}
	  }       
	}
}

function ItemOEMPNFunction() {
	var input, filter, table, tr, td, i, txtValue;
	input = document.getElementById("ItemOEMPNsearch");
	filter = input.value.toUpperCase();
	table = document.getElementById("ItemIDTable2");
	tr = table.getElementsByTagName("tr");
	for (i = 0; i < tr.length; i++) {
	  td = tr[i].getElementsByTagName("td")[10];
	  if (td) {
		txtValue = td.textContent || td.innerText;
		if (txtValue.toUpperCase().indexOf(filter) > -1) {
		  tr[i].style.display = "";
		} else {
		  tr[i].style.display = "none";
		}
	  }       
	}
}

      

  $('#Material').on('click','[id*="popupITEMID"]',function(event){
                
                var sord_id = $.trim($(this).parent().parent().find('[id*="HDNSOrdID_"]').val());
                if(sord_id==""){
                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn").hide();
                    $("#OkBtn1").show();
                    $("#AlertMessage").text('Please select SO No first.');
                    $("#alert").modal('show');
                    $("#OkBtn1").focus();
                    highlighFocusBtn('activeOk1');
                    return false;
                }
        if($.trim($("#FetchItem").val()) ==="")
        {
            $('#item_loader').show();

            $("#tbody_ItemID").html('');
              $.ajaxSetup({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  }
              });
              $.ajax({
                  url:'{{route("transaction",[$FormId,"getItemDetails"])}}',
                  type:'POST',
                  data:{'soid':sord_id},
                  success:function(data) {
                    if(data !=""){
                              $('#item_loader').hide();
                              $("#tbody_ItemID").html(data); 
                              $("#FetchItem").val('1')    
                              $('.js-selectall').prop('disabled', true); 
                              bindItemEvents();
                            }
                            else{
                              $("#FetchItem").val('')
                              $('#item_loader').hide();
                              $("#tbody_ItemID").html("<tr><td> Record not found.</td></tr>");  
                            }                    
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                        $('#item_loader').hide();
                        $("#tbody_ItemID").html('');                        
                      },
                  }); 
        }
        $("#ITEMIDpopup").show();
        var id = $(this).attr('id');
        var id2 = $(this).parent().parent().find('[id*="ITEMID_REF"]').attr('id');
        var id3 = $(this).parent().parent().find('[id*="ItemName"]').attr('id');
        var id4 = $(this).parent().parent().find('[id*="Itemspec"]').attr('id');
        var id5 = $(this).parent().parent().find('[id*="popupMUOM"]').attr('id');
        var id6 = $(this).parent().parent().find('[id*="MAIN_UOMID_REF"]').attr('id');
        var id7 = $(this).parent().parent().find('[id*="SO_QTY"]').attr('id');
        var id8 = $(this).parent().parent().find('[id*="BAL_SOQTY"]').attr('id');
        var id9 = $(this).parent().parent().find('[id*="EXP_DIS_QTY"]').attr('id');
        var id10 = $(this).parent().parent().find('[id*="txtrecordId"]').attr('id');
        
        var id11 = $(this).parent().parent().find('[id*="txtSQA_REF"]').attr('id');
        var id12 = $(this).parent().parent().find('[id*="txtSEQID_REF"]').attr('id');



        $('#hdn_ItemID').val(id);
        $('#hdn_ItemID2').val(id2);
        $('#hdn_ItemID3').val(id3);
        $('#hdn_ItemID4').val(id4);
        $('#hdn_ItemID5').val(id5);
        $('#hdn_ItemID6').val(id6);
        $('#hdn_ItemID7').val(id7);
        $('#hdn_ItemID8').val(id8);
        $('#hdn_ItemID9').val(id9);
        $('#hdn_ItemID10').val(id10);
        $('#hdn_ItemID11').val(id11);
        $('#hdn_ItemID12').val(id12);

        event.preventDefault();
  });

  $("#ITEMID_closePopup").click(function(event){
    $("#ITEMIDpopup").hide();
  });

    function bindItemEvents(){

      $('#ItemIDTable2').off(); 

      $('[id*="chkId"]').change(function(){
      // $(".clsitemid").dblclick(function(){
        var fieldid = $(this).parent().parent().attr('id');
        var txtval =   $("#txt"+fieldid+"").val();
        var texdesc =  $("#txt"+fieldid+"").data("desc");

       
        var recordid = $(this).parent().children('[id*="recordId"]').attr('id');
        var txtrecordidval =  $("#"+recordid+"").val();

        var icodeid = $(this).parent().children('[id*="itemcode"]').attr('id');
        var txticodeval =  $("#txt"+icodeid+"").val();


        var fieldid2 = $(this).parent().parent().children('[id*="itemname"]').attr('id');
        var txtname =  $("#txt"+fieldid2+"").val();
        var txtspec =  $("#txt"+fieldid2+"").data("desc");
        
        var fieldid3 = $(this).parent().parent().children('[id*="itemuom"]').attr('id');
        var txtmuomid =  $("#txt"+fieldid3+"").val();
       // var txtauom =  $("#txt"+fieldid3+"").data("desc");

       var apartno =  $("#txt"+fieldid3+"").data("desc2");
        var cpartno =  $("#txt"+fieldid3+"").data("desc3");
        var opartno =  $("#txt"+fieldid3+"").data("desc4");

        var txtmuom =  $(this).parent().parent().children('[id*="itemuom"]').text();

        var fieldid4 = $(this).parent().parent().children('[id*="itemspec"]').attr('id');
        var txtspec =  $("#txt"+fieldid4+"").val();

        var soqtyid = $(this).parent().parent().children('[id*="soqty"]').attr('id');
        var txtsoqtyval =  $("#txt"+soqtyid+"").val();

        var balsoqtyid = $(this).parent().parent().children('[id*="balsoqty"]').attr('id');
        var txtbalsoqtyval =  $("#txt"+balsoqtyid+"").val();

        var expdpqtyid = $(this).parent().parent().children('[id*="expdpqty"]').attr('id');
        var txtexpdpqtyval =  $("#txt"+expdpqtyid+"").val();

        var txtSQA_REFid = $(this).parent().children('[id*="SQA_REF"]').attr('id');
        var txtSQA_REFval =  $("#"+txtSQA_REFid+"").val();

        var txtSEQID_REFid = $(this).parent().children('[id*="SEQID_REF"]').attr('id');
        var txtSEQID_REFval =  $("#"+txtSEQID_REFid+"").val();

        
        //txtauomqty = (parseFloat(txtmuomqty)/parseFloat(txtmqtyf))*parseFloat(txtauomqty);
        
        // if(intRegex.test(txtauomqty)){
        //     txtauomqty = (txtauomqty +'.000');
        // }

        // if(intRegex.test(txtmuomqty)){
        //   txtmuomqty = (txtmuomqty +'.000');
        // }
        
       if($(this).is(":checked") == true) 
       {
            $('#example2').find('.participantRow').each(function()
            {
              var itemid = $(this).find('[id*="txtrecordId"]').val();
              if(txtrecordidval)
              {
                    if(txtrecordidval == itemid)
                    {
                        $("#ITEMIDpopup").hide();
                        $("#YesBtn").hide();
                        $("#NoBtn").hide();
                        $("#OkBtn").hide();
                        $("#OkBtn1").show();
                        $("#AlertMessage").text('Item already exists.');
                        $("#alert").modal('show');
                        $("#OkBtn1").focus();
                        highlighFocusBtn('activeOk1');
                        $('#hdn_ItemID').val('');
                        $('#hdn_ItemID2').val('');
                        $('#hdn_ItemID3').val('');
                        $('#hdn_ItemID4').val('');
                        $('#hdn_ItemID5').val('');
                        $('#hdn_ItemID6').val('');
                        $('#hdn_ItemID7').val('');
                        $('#hdn_ItemID8').val('');
                        $('#hdn_ItemID9').val('');
                        $('#hdn_ItemID10').val('');
                        $('#hdn_ItemID11').val('');
                        $('#hdn_ItemID12').val('');
                        // $('#hdn_ItemID11').val('');
                        txtval = '';
                        texdesc = '';
                        txtname = '';
                        txtspec = '';
                        txtmuom = '';
                        soqtyid = '';
                        txtsoqtyval = '';
                        balsoqtyid = '';
                        txtbalsoqtyval='';
                        expdpqtyid ='';
                        txtexpdpqtyval = '';
                        recordid='';
                        txtrecordidval='';
                      
                        txtSQA_REFid = '';
                        txtSQA_REFval =  '';

                        txtSEQID_REFid = '';
                        txtSEQID_REFval =  '';
                        
                        return false;
                    }               
              }          
            });  

            if($('#hdn_ItemID').val() == "" && txtrecordidval != '')
            {
                var txtid= $('#hdn_ItemID').val();
                var txt_id2= $('#hdn_ItemID2').val();
                var txt_id3= $('#hdn_ItemID3').val();
                var txt_id4= $('#hdn_ItemID4').val();
                var txt_id5= $('#hdn_ItemID5').val();
                var txt_id6= $('#hdn_ItemID6').val();
                var txt_id7= $('#hdn_ItemID7').val();
                var txt_id8= $('#hdn_ItemID8').val();
                var txt_id9= $('#hdn_ItemID9').val();
                var txt_id10= $('#hdn_ItemID10').val();
                var txt_id11= $('#hdn_ItemID11').val();
                var txt_id12= $('#hdn_ItemID12').val();
                // var txt_id11= $('#hdn_ItemID11').val();

                var $tr = $('.material').closest('table');
                var allTrs = $tr.find('.participantRow').last();
                var lastTr = allTrs[allTrs.length-1];
                var $clone = $(lastTr).clone();
                $clone.find('td').each(function(){
                    var el = $(this).find(':first-child');
                    var id = el.attr('id') || null;
                    if(id) {
                        var i = id.substr(id.length-1);
                        var prefix = id.substr(0, (id.length-1));
                        el.attr('id', prefix+(+i+1));
                    }
                    var name = el.attr('name') || null;
                    if(name) {
                        var i = name.substr(name.length-1);
                        var prefix1 = name.substr(0, (name.length-1));
                        el.attr('name', prefix1+(+i+1));
                    }
                });
                $clone.find('.remove').removeAttr('disabled'); 
                $clone.find('[id*="popupITEMID"]').val(texdesc);
                $clone.find('[id*="ITEMID_REF"]').val(txtval);
                $clone.find('[id*="ItemName"]').val(txtname);

                $clone.find('[id*="Alpspartno"]').val(apartno);
                $clone.find('[id*="Custpartno"]').val(cpartno);
                $clone.find('[id*="OEMpartno"]').val(opartno);

                $clone.find('[id*="Itemspec"]').val(txtspec);  
                $clone.find('[id*="popupMUOM"]').val(txtmuom);
                $clone.find('[id*="MAIN_UOMID_REF"]').val(txtmuomid);
                $clone.find('[id*="SO_QTY"]').val(txtsoqtyval);
                $clone.find('[id*="BAL_SOQTY"]').val(txtbalsoqtyval);
                $clone.find('[id*="EXP_DIS_QTY"]').val(txtexpdpqtyval);
                $clone.find('[id*="recordId"]').val(txtrecordidval);
                $clone.find('[id*="txtSQA_REF"]').val(txtSQA_REFval);
                $clone.find('[id*="txtSEQID_REF"]').val(txtSEQID_REFval);
             
                
                $tr.closest('table').append($clone);   
                var rowCount = $('#Row_Count1').val();
                  rowCount = parseInt(rowCount)+1;
                  $('#Row_Count1').val(rowCount);
                $("#ITEMIDpopup").hide();
                event.preventDefault();
            }
            else
            {
                var txtid= $('#hdn_ItemID').val();
                var txt_id2= $('#hdn_ItemID2').val();
                var txt_id3= $('#hdn_ItemID3').val();
                var txt_id4= $('#hdn_ItemID4').val();
                var txt_id5= $('#hdn_ItemID5').val();
                var txt_id6= $('#hdn_ItemID6').val();
                var txt_id7= $('#hdn_ItemID7').val();
                var txt_id8= $('#hdn_ItemID8').val();
                var txt_id9= $('#hdn_ItemID9').val();
                var txt_id10= $('#hdn_ItemID10').val();
                var txt_id11= $('#hdn_ItemID11').val();
                var txt_id12= $('#hdn_ItemID12').val();
              
                if($.trim(txtid)!=""){
                  $('#'+txtid).val(texdesc);
                }
                if($.trim(txt_id2)!=""){
                  $('#'+txt_id2).val(txtval);
                }
                if($.trim(txt_id3)!=""){
                  $('#'+txt_id3).val(txtname);
                }
                if($.trim(txt_id4)!=""){
                  $('#'+txt_id4).val(txtspec);
                }
                if($.trim(txt_id5)!=""){
                  $('#'+txt_id5).val(txtmuom);
                }
                
                if($.trim(txt_id6)!=""){
                  $('#'+txt_id6).val(txtmuomid);
                }
                
                if($.trim(txt_id7)!=""){
                  $('#'+txt_id7).val(txtsoqtyval);
                }
                if($.trim(txt_id8)!=""){
                  //$('#'+txt_id8).val(txtbalsoqtyval);
                  $('#'+txt_id8).val(txtbalsoqtyval);
                }
                if($.trim(txt_id9)!=""){
                  $('#'+txt_id9).val(txtexpdpqtyval);
                }
                
                if($.trim(txt_id10)!=""){
                  $('#'+txt_id10).val(txtrecordidval);
                }

                if($.trim(txt_id11)!=""){
                  $('#'+txt_id11).val(txtSQA_REFval);
                }

                if($.trim(txt_id12)!=""){
                  $('#'+txt_id12).val(txtSEQID_REFval);
                }
                
                $('#'+txtid).parent().parent().find('[id*="Alpspartno"]').val(apartno);
                $('#'+txtid).parent().parent().find('[id*="Custpartno"]').val(cpartno);
                $('#'+txtid).parent().parent().find('[id*="OEMpartno"]').val(opartno);
             
                // $('#'+txt_id10).val(txtauomqty);
                
                $("#ITEMIDpopup").hide();
                $('#hdn_ItemID').val('');
                $('#hdn_ItemID2').val('');
                $('#hdn_ItemID3').val('');
                $('#hdn_ItemID4').val('');
                $('#hdn_ItemID5').val('');
                $('#hdn_ItemID6').val('');
                $('#hdn_ItemID7').val('');
                $('#hdn_ItemID8').val('');
                $('#hdn_ItemID9').val('');
                $('#hdn_ItemID10').val('');
                $('#hdn_ItemID11').val('');
                $('#hdn_ItemID12').val('');
                     
            }
            event.preventDefault();
       }
       else if($(this).is(":checked") == false) 
       {
         var id = txtrecordidval;
         var r_count = $('#Row_Count1').val();
         $('#example2').find('.participantRow').each(function()
         {
           var itemid = $(this).find('[id*="txtrecordId"]').val();
           if(id == itemid)
           {
              var rowCount = $('#Row_Count1').val();
              if (rowCount > 1) {
                $(this).closest('.participantRow').remove(); 
                rowCount = parseInt(rowCount)-1;
              $('#Row_Count1').val(rowCount);
              }
              else 
              {
                $(document).find('.dmaterial').prop('disabled', true);  
                $("#ITEMIDpopup").hide();
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text('There is only 1 row. So cannot be remove.');
                $("#alert").modal('show');
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk1');
                return false;

              }
                event.preventDefault(); 
           }
        });
       }
        $("#Itemcodesearch").val(''); 
        $("#Itemnamesearch").val(''); 
        $("#ItemUOMsearch").val(''); 
        $("#ItemBalSoQtysearch").val(''); 
        $("#ItemExpDisQtysearch").val(''); 
        $("#ItemSpecsearch").val(''); 
        $('.remove').removeAttr('disabled'); 
       
        event.preventDefault();
      });
    }
  //Item ID Dropdown Ends
//------------------------

  var formItemMst = $( "#form_data" );
  formItemMst.validate();
  

$(document).ready(function(e) {


 // var Material = $("#Material").html(); 
  //$('#hdnmaterial').val(Material);

  var lastdspdt = <?php echo json_encode($objlastDSPDT[0]->DSPDT); ?>;
  var today = new Date(); 
  var sodate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
  $('#DSPDT').attr('min',lastdspdt);
  $('#DSPDT').attr('max',sodate);


  
 

      $('#EXP_DIS_QTY').focusout(function(e){
        var cvalue = $(this).val();
        if(intRegex.test(cvalue)){
          cvalue = cvalue +'.000';
            }
        $(this).val(cvalue);
      });

  //---UDF


});

$('#Material').on('focusout',"[id*='EXP_DIS_QTY']",function()
{
  if(intRegex.test($(this).val())){
    $(this).val($(this).val()+'.000')
    }

    var temp_so_qty       = parseFloat( $(this).parent().parent().find('[id*="SO_QTY"]').val()).toFixed(3);
    var  temp_bal_so_qty  = parseFloat( $(this).parent().parent().find('[id*="BAL_SOQTY"]').val()).toFixed(3); 
    var temp_exp_dis_qty = parseFloat( $(this).parent().parent().find('[id*="EXP_DIS_QTY"]').val() ).toFixed(3);

    var tot_qty = parseFloat(parseFloat(temp_bal_so_qty) + parseFloat(temp_exp_dis_qty)).toFixed(3);

    if(parseFloat(temp_exp_dis_qty)>parseFloat(temp_bal_so_qty)){
            $(this).val('');
            $("#alert").modal('show');
            $("#AlertMessage").text('Dispatch qty not alllowed. Please enter again.');
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            $("#OkBtn").hide();
            highlighFocusBtn('activeOk1');
    }
    event.preventDefault();
});

$('#Material').on('keyup', '.three-digits', function() {
    if ($(this).val().indexOf('.') != -1) {
        if ($(this).val().split(".")[1].length > 3) {
            $(this).val('');
            $("#alert").modal('show');
            $("#AlertMessage").text('Enter value till three decimal only.');
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            $("#OkBtn").hide();
            highlighFocusBtn('activeOk1');
        }
    }
    return this; //for chaining
});

$('#Material').on('blur', '.three-digits', function() {
    if ($(this).val().indexOf('.') != -1) {
        if ($(this).val().split(".")[1].length > 3) {
            $(this).val('');
            $("#alert").modal('show');
            $("#AlertMessage").text('Enter value till three decimal only.');
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            $("#OkBtn").hide();
            highlighFocusBtn('activeOk1');
        }
    }
    return this; //for chaining
});

$('#btnAdd').on('click', function() {
  var viewURL = '{{route("transaction",[$FormId,"add"])}}';
              window.location.href=viewURL;
});

$('#btnExit').on('click', function() {
  var viewURL = '{{route('home')}}';
              window.location.href=viewURL;
});

$('#DSPDT').change(function() {
    var mindate  = $(this).val();
    $('[id*="EDD"]').attr('min',mindate);
});   



$("#btnUndo").on("click", function() {
    $("#AlertMessage").text("Do you want to erase entered information in this record?");
    $("#alert").modal('show');

    $("#YesBtn").data("funcname","fnUndoYes");
    $("#YesBtn").show();

    $("#NoBtn").data("funcname","fnUndoNo");
    $("#NoBtn").show();
    
    $("#OkBtn").hide();
    $("#NoBtn").focus();
    event.preventDefault();
});

    

window.fnUndoYes = function (){
    //reload form
  //window.location.href = "{{route('transaction',[$FormId,'add'])}}";
  window.location.reload();
}//fnUndoYes


window.fnUndoNo = function (){

}//fnUndoNo

$("#btnSaveFormData").click(function() {
    
  if(formItemMst.valid()){

    if(commonvalidation()){

      if(checkPeriodClosing('{{$FormId}}',$("#DSPDT").val(),0) ==0){
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text(period_closing_msg);
        $("#alert").modal('show');
        $("#OkBtn1").focus();
      }
      else{
        event.preventDefault();
        $("#alert").modal('show');
        $("#AlertMessage").text('Do you want to save to Record.');
        $("#YesBtn").data("funcname","fnSaveData");  //set dynamic fucntion name
        $("#OkBtn1").hide();
        $("#OkBtn").hide();
        $("#YesBtn").show();
        $("#NoBtn").show();
        $("#YesBtn").focus();
        highlighFocusBtn('activeYes');
      }
      
      return false;  
    };

          // event.preventDefault();

          // var today = new Date(); 

          //     var DSPNO          =   $.trim($("#DSPNO").val());
          //     var DSPDT          =   $.trim($("#DSPDT").val());
          //     var GLID_REF       =   $.trim($("#GLID_REF").val());
          //     var SLID_REF       =   $.trim($("#SLID_REF").val());
          //     var EMID_REF       =   $.trim($("#EMID_REF").val());
          //     var ENQBY          =   $.trim($("#ENQBY").val());
          //     var PRIORITYID_REF =   $.trim($("#PRIORITYID_REF").val());
          //     var SPID_REF       =   $.trim($("#SPID_REF").val());

          //     if(DSPNO ===""){
          //         $("#FocusId").val($("#DSPNO"));
          //         $("#ProceedBtn").focus();
          //         $("#YesBtn").hide();
          //         $("#NoBtn").hide();
          //         $("#OkBtn1").show();
          //         $("#AlertMessage").text('Please enter value in DSP No.');
          //         $("#alert").modal('show');
          //         $("#OkBtn1").focus();
          //         return false;
          //     }
          //     else if(DSPDT ===""){
          //         $("#FocusId").val($("#DSPDT"));
          //         $("#DSPDT").val(today);  
          //         $("#ProceedBtn").focus();
          //         $("#YesBtn").hide();
          //         $("#NoBtn").hide();
          //         $("#OkBtn1").show();
          //         $("#AlertMessage").text('Please select DSP Date.');
          //         $("#alert").modal('show');
          //         $("#OkBtn1").focus();
          //         return false;

          //     }else if(GLID_REF ===""){
          //         $("#ProceedBtn").focus();
          //         $("#YesBtn").hide();
          //         $("#NoBtn").hide();
          //         $("#OkBtn1").show();
          //         $("#AlertMessage").text('Please select GL.');
          //         $("#alert").modal('show');
          //         $("#OkBtn1").focus();
          //         highlighFocusBtn('activeOk1');
          //         return false;

          //     }else if(SLID_REF ===""){
          //         $("#ProceedBtn").focus();
          //         $("#YesBtn").hide();
          //         $("#NoBtn").hide();
          //         $("#OkBtn1").show();
          //         $("#AlertMessage").text('Please select SL.');
          //         $("#alert").modal('show');
          //         $("#OkBtn1").focus();
          //         highlighFocusBtn('activeOk1');
          //         return false;
          //     } 
      
          //     var allblank1 = [];  
          //     var allblank2 = [];  
          //     var allblank3 = [];  
          //     var allblank4 = [];  
          //     var allblank5 = [];  
          //     var allblank6 = [];  
          //     var allblank7 = [];  
          //     var allblank9 = [];  
              

          //     $("[id*=HDNSOrdID]").each(function(){
          //       var strid = $(this).attr("id")
          //       if (strid.toLowerCase().indexOf("error") == -1){
          //         if( $.trim( $(this).val()) == "" )
          //         {
          //             allblank1.push('true');
          //         }else
          //         {
          //           allblank1.push('false');
          //         }
          //       }
          //     });

          //     $("[id*=ITEMID_REF]").each(function(){
          //       var strid = $(this).attr("id")
          //       if (strid.toLowerCase().indexOf("error") == -1){
          //         if( $.trim( $(this).val()) == "" )
          //         {
          //             allblank2.push('true');
          //         }else
          //         {
          //           allblank2.push('false');
          //         }
          //       }
          //     });


          //     $("[id*=EXP_DIS_QTY]").each(function(){
          //       var strid = $(this).attr("id")
          //       if (strid.toLowerCase().indexOf("error") == -1){

          //           if( $.trim( $(this).val()) == "" )
          //           {
          //               allblank3.push('true');
          //           }else
          //           {
          //             allblank3.push('false');
          //           }

          //           if( $.trim($(this).val()) != "" && !$.isNumeric($(this).val()) ){
          //             allblank4.push('true');
          //           }else
          //           {
          //             allblank4.push('false');
          //           }

          //           if( $.trim($(this).val()) != "" && $.isNumeric($(this).val()) ){
          //             if($(this).val()<0.000){
          //               allblank5.push('true');
          //             }else
          //             {
          //               allblank5.push('false');
          //             }
          //           }


          //       }
                 
          //     });

           
          //     $("[id*=txtudffie_popup]").each(function(){
          //               if($.trim($(this).val())!="")
          //               {
          //                   if($.trim($(this).parent().parent().find('[id*="udffieismandatory"]').val()) == "1")
          //                     {
          //                       if($.trim($(this).parent().parent().find('[id*="udfvalue"]').val()) != "")
          //                         {
          //                           allblank9.push('true');
          //                         }
          //                       else
          //                         {
          //                           allblank9.push('false');
          //                         }
          //                     }
                            
          //               }
                        
          //           });

          //     if(jQuery.inArray("true", allblank1) !== -1){
          //           $("#alert").modal('show');
          //           $("#AlertMessage").text('Please select SO No  in Material Tab.');
          //           $("#YesBtn").hide(); 
          //           $("#NoBtn").hide();  
          //           $("#OkBtn1").show();
          //           $("#OkBtn1").focus();
          //           highlighFocusBtn('activeOk1');
          //           return false;

          //       }else if(jQuery.inArray("true", allblank2) !== -1){
          //           $("#alert").modal('show');
          //           $("#AlertMessage").text('Please select Item in Material Tab.');
          //           $("#YesBtn").hide(); 
          //           $("#NoBtn").hide();  
          //           $("#OkBtn1").show();
          //           $("#OkBtn1").focus();
          //           highlighFocusBtn('activeOk1');
          //           return false;
                
          //       }else if(jQuery.inArray("true", allblank3) !== -1){
          //           $("#alert").modal('show');
          //           $("#AlertMessage").text('Please enter valid Expected Dispatch Qty in Material Tab.');
          //           $("#YesBtn").hide(); 
          //           $("#NoBtn").hide();  
          //           $("#OkBtn1").show();
          //           $("#OkBtn1").focus();
          //           highlighFocusBtn('activeOk1');
          //           return false;

          //       }else if(jQuery.inArray("true", allblank4) !== -1){
          //           $("#alert").modal('show');
          //           $("#AlertMessage").text('Please enter valid Expected Dispatch Qty in Material Tab.');
          //           $("#YesBtn").hide(); 
          //           $("#NoBtn").hide();  
          //           $("#OkBtn1").show();
          //           $("#OkBtn1").focus();
          //           highlighFocusBtn('activeOk1');
          //           return false;

          //       }else if(jQuery.inArray("true", allblank5) !== -1){
          //           $("#alert").modal('show');
          //           $("#AlertMessage").text('Expected Dispatch Qty value must be greater than 0.000 in Material Tab.');
          //           $("#YesBtn").hide(); 
          //           $("#NoBtn").hide();  
          //           $("#OkBtn1").show();
          //           $("#OkBtn1").focus();
          //           highlighFocusBtn('activeOk1');
          //           return false;
          //         }
          //         else if(jQuery.inArray("false", allblank9) !== -1){
          //               $("#alert").modal('show');
          //               $("#AlertMessage").text('Please enter Value / Comments in UDF Tab.');
          //               $("#YesBtn").hide(); 
          //               $("#NoBtn").hide();  
          //               $("#OkBtn1").show();
          //               $("#OkBtn1").focus();
          //               highlighFocusBtn('activeOk1');
          //               return false;
          //         }else{
          //           $("#alert").modal('show');
          //           $("#AlertMessage").text('Do you want to save to Record.');
          //           $("#YesBtn").data("funcname","fnSaveData");  //set dynamic fucntion name
          //           $("#OkBtn1").hide();
          //           $("#OkBtn").hide();
          //           $("#YesBtn").show();
          //           $("#NoBtn").show();
          //           $("#YesBtn").focus();
          //           highlighFocusBtn('activeYes');
          //         }   
          //return false;
    }        
//----------------------------
});//btnSaveFormData

//btnApprove
$( "#btnApprove" ).click(function() {

  if(formItemMst.valid()){
    if(commonvalidation()){  
      if(checkPeriodClosing('{{$FormId}}',$("#DSPDT").val(),0) ==0){
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text(period_closing_msg);
        $("#alert").modal('show');
        $("#OkBtn1").focus();
      }
      else{ 
        $("#alert").modal('show');
        $("#AlertMessage").text('Do you want to save and approve this record.');
        $("#YesBtn").data("funcname","fnApproveData");  //set dynamic fucntion name
        $("#OkBtn1").hide();
        $("#OkBtn").hide();
        $("#YesBtn").show();
        $("#NoBtn").show();
        $("#YesBtn").focus();
        highlighFocusBtn('activeYes');
      }

        return false;
    }    
} //if          
});//btnApprove



//********-----
window.fnApproveData = function (){

      //validate and save data
      var currentForm = $("#form_data");
      var formData = currentForm.serialize();
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
      $("#btnApprove").hide(); 
      $(".buttonload_approve").show();  
      $("#btnSaveFormData").prop("disabled", true);
      $.ajax({
          url:'{{route("transaction",[$FormId,"Approve"])}}',
          type:'POST',
          data:formData,
          success:function(data) {
            $("#btnApprove").show();  
            $(".buttonload_approve").hide();  
            $("#btnSaveFormData").prop("disabled", false);
          
            if(data.errors) {
                $(".text-danger").hide();

                console.log("error MSG="+data.msg);

                if(data.resp=='duplicate') {

                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").hide();
                  $("#OkBtn1").show();
                  $("#AlertMessage").text(data.msg);
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  highlighFocusBtn('activeOk1');
                  return false;

                }

              if(data.save=='invalid') {

                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").hide();
                  $("#OkBtn1").show();
                  $("#AlertMessage").text(data.msg);
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  return false;
              }

              if(data.form=='invalid') {

                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").hide();
                  $("#OkBtn1").show();
                  $("#AlertMessage").text("Invalid form data please required fields.");
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  return false;
              }
              
            }
            
            if(data.success) {                   

                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").hide();
                $("#OkBtn").show();  
                highlighFocusBtn('activeOk');
                
                $("#AlertMessage").text(data.msg);
                $("#alert").modal('show');

                $("#OkBtn").focus();
                $(".text-danger").hide();
            }
            
        },
        error:function(data){
            $("#btnApprove").show();  
            $(".buttonload_approve").hide();  
            $("#btnSaveFormData").prop("disabled", false);
          console.log("Error: Something went wrong.");
        },
      });

}// fnApproveata

//********-----

function commonvalidation(){
        
        event.preventDefault();
        var today = new Date(); 

              var DSPNO          =   $.trim($("#DSPNO").val());
              var DSPDT          =   $.trim($("#DSPDT").val());
              var GLID_REF       =   $.trim($("#GLID_REF").val());
              var SLID_REF       =   $.trim($("#SLID_REF").val());
              var EMID_REF       =   $.trim($("#EMID_REF").val());
              var ENQBY          =   $.trim($("#ENQBY").val());
              var PRIORITYID_REF =   $.trim($("#PRIORITYID_REF").val());
              var SPID_REF       =   $.trim($("#SPID_REF").val());

              if(DSPNO ===""){
                  $("#FocusId").val('DSPNO');
                  $("#ProceedBtn").focus();
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn1").show();
                  $("#AlertMessage").text('Please enter value in DSP No.');
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  return false;
              }
              else if(DSPDT ===""){
                  $("#FocusId").val('DSPDT');
                  $("#DSPDT").val(today);  
                  $("#ProceedBtn").focus();
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn1").show();
                  $("#AlertMessage").text('Please select DSP Date.');
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  return false;

              }
              else if(GLID_REF ===""){
                $("#FocusId").val('txtsubgl_popup');
                  $("#ProceedBtn").focus();
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn1").show();
                  $("#AlertMessage").text('Please select Customer.');
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  highlighFocusBtn('activeOk1');
                  return false;

              }
           
      
              var allblank1 = [];  
              var allblank2 = [];  
              var allblank3 = [];  
              var allblank4 = [];  
              var allblank5 = [];  
              var allblank6 = [];  
              var allblank7 = [];  
              var allblank9 = [];  


              var focustext1= "";
              var focustext2= "";
              var focustext3= "";
              var focustext4= "";
              var focustext5= "";
              var focustext6= "";
              var focustext7= "";
              var focustext8= "";
              var focustext9= "";
              var focustext10= "";
              var focustext11= "";
              var focustext12= "";
              

              $("[id*=txtSOrd_popup]").each(function(){
                var strid = $(this).attr("id");
                if (strid.toLowerCase().indexOf("error") == -1){
                  if( $.trim( $(this).val()) == "" )
                  {
                      allblank1.push('true');
                      focustext1 = $(this).attr("id");


                  }else
                  {
                    allblank1.push('false');
                   
                  }
                }
              });

              $("[id*=popupITEMID]").each(function(){
                var strid = $(this).attr("id")
                if (strid.toLowerCase().indexOf("error") == -1){
                  if( $.trim( $(this).val()) == "" )
                  {
                      allblank2.push('true');
                      focustext2 = $(this).attr("id");
                  }else
                  {
                    allblank2.push('false');
                    
                  }
                }
              });


              $("[id*=EXP_DIS_QTY]").each(function(){
                var strid = $(this).attr("id")
                if (strid.toLowerCase().indexOf("error") == -1){

                    if( $.trim( $(this).val()) == "" )
                    {
                        allblank3.push('true');
                        focustext3 = $(this).attr("id");
                    }else
                    {
                      allblank3.push('false');
                     
                    }

                    if( $.trim($(this).val()) != "" && !$.isNumeric($(this).val()) ){
                      allblank4.push('true');
                      focustext4 = $(this).attr("id");
                    }else
                    {
                      allblank4.push('false');
                     
                    }

                    if( $.trim($(this).val()) != "" && $.isNumeric($(this).val()) ){
                      if($(this).val()<0.000){
                        allblank5.push('true');
                        focustext5 = $(this).attr("id");
                      }else
                      {
                        allblank5.push('false');
                        
                      }
                    }


                }
                 
              });

           
              $("[id*=txtudffie_popup]").each(function(){
                        if($.trim($(this).val())!="")
                        {
                            if($.trim($(this).parent().parent().find('[id*="udffieismandatory"]').val()) == "1")
                              {
                                if($.trim($(this).parent().parent().find('[id*="udfvalue"]').val()) != "")
                                  {
                                    allblank9.push('true');
                                  }
                                else
                                  {
                                    allblank9.push('false');
                                    focustext9 = $(this).parent().parent().find('[id*="udfvalue"]').attr('id');
                                  }
                              }
                            
                        }
                        
                    });

              if(jQuery.inArray("true", allblank1) !== -1){
                    $("#MAT_TAB").click();
                    $("#FocusId").val(focustext1);
                    $("#alert").modal('show');
                    $("#AlertMessage").text('Please select SO No  in Material Tab.');
                    $("#YesBtn").hide(); 
                    $("#NoBtn").hide();  
                    $("#OkBtn1").show();
                    $("#OkBtn1").focus();
                    highlighFocusBtn('activeOk1');
                    return false;

                }else if(jQuery.inArray("true", allblank2) !== -1){
                    $("#MAT_TAB").click();
                    $("#FocusId").val(focustext2);
                    $("#alert").modal('show');
                    $("#AlertMessage").text('Please select Item in Material Tab.');
                    $("#YesBtn").hide(); 
                    $("#NoBtn").hide();  
                    $("#OkBtn1").show();
                    $("#OkBtn1").focus();
                    highlighFocusBtn('activeOk1');
                    return false;
                
                }else if(jQuery.inArray("true", allblank3) !== -1){
                    $("#MAT_TAB").click();
                    $("#FocusId").val(focustext3);
                    $("#alert").modal('show');
                    $("#AlertMessage").text('Please enter valid Expected Dispatch Qty in Material Tab.');
                    $("#YesBtn").hide(); 
                    $("#NoBtn").hide();  
                    $("#OkBtn1").show();
                    $("#OkBtn1").focus();
                    highlighFocusBtn('activeOk1');
                    return false;

                }else if(jQuery.inArray("true", allblank4) !== -1){
                    $("#MAT_TAB").click();
                    $("#FocusId").val(focustext4);
                    $("#alert").modal('show');
                    $("#AlertMessage").text('Please enter valid Expected Dispatch Qty in Material Tab.');
                    $("#YesBtn").hide(); 
                    $("#NoBtn").hide();  
                    $("#OkBtn1").show();
                    $("#OkBtn1").focus();
                    highlighFocusBtn('activeOk1');
                    return false;

                }else if(jQuery.inArray("true", allblank5) !== -1){
                    $("#MAT_TAB").click();
                    $("#FocusId").val(focustext5);
                    $("#alert").modal('show');
                    $("#AlertMessage").text('Expected Dispatch Qty value must be greater than 0.000 in Material Tab.');
                    $("#YesBtn").hide(); 
                    $("#NoBtn").hide();  
                    $("#OkBtn1").show();
                    $("#OkBtn1").focus();
                    highlighFocusBtn('activeOk1');
                    return false;
                  }
                  else if(jQuery.inArray("false", allblank9) !== -1){
                    $("#UDF_TAB").click();
                    $("#FocusId").val(focustext9);
                        $("#alert").modal('show');
                        $("#AlertMessage").text('Please enter Value / Comments in UDF Tab.');
                        $("#YesBtn").hide(); 
                        $("#NoBtn").hide();  
                        $("#OkBtn1").show();
                        $("#OkBtn1").focus();
                        highlighFocusBtn('activeOk1');
                        return false;
                  }

      return true;
}  //commonvalidation end 


$("#YesBtn").click(function(){
  $("#alert").modal('hide');
  var customFnName = $("#YesBtn").data("funcname");
      window[customFnName]();

}); //yes button

window.fnSaveData = function (){

//validate and save data
event.preventDefault();

     var trnseForm = $("#form_data");
    var formData = trnseForm.serialize();
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$("#btnSaveFormData").hide(); 
$(".buttonload").show(); 
$("#btnApprove").prop("disabled", true);
$.ajax({
    url:'{{ route("transaction",[$FormId,"update"])}}',
    type:'POST',
    data:formData,
    success:function(data) {
      $(".buttonload").hide(); 
      $("#btnSaveFormData").show();   
      $("#btnApprove").prop("disabled", false);
              
              if(data.errors) {
                  $(".text-danger").hide();

                  console.log("error MSG="+data.msg);

                  if(data.resp=='duplicate') {

                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn").hide();
                    $("#OkBtn1").show();
                    $("#AlertMessage").text(data.msg);
                    $("#alert").modal('show');
                    $("#OkBtn1").focus();
                    highlighFocusBtn('activeOk1');
                    return false;

                  }

                if(data.save=='invalid') {

                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn").hide();
                    $("#OkBtn1").show();
                    $("#AlertMessage").text(data.msg);
                    $("#alert").modal('show');
                    $("#OkBtn1").focus();
                    return false;
                }

                if(data.form=='invalid') {

                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn").hide();
                    $("#OkBtn1").show();
                    $("#AlertMessage").text("Invalid form data please required fields.");
                    $("#alert").modal('show');
                    $("#OkBtn1").focus();
                    return false;
                }
                
              }
              
              if(data.success) {                   

                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn1").hide();
                  $("#OkBtn").show();  
                  highlighFocusBtn('activeOk');
                  
                  $("#AlertMessage").text("Record saved successfully.");
                  $("#alert").modal('show');

                  $("#OkBtn").focus();
                  $(".text-danger").hide();
              }
              
          },
          error:function(data){
            $(".buttonload").hide(); 
            $("#btnSaveFormData").show();   
            $("#btnApprove").prop("disabled", false);
            console.log("Error: Something went wrong.");
          },
});

}

//no button
$("#NoBtn").click(function(){
    $("#alert").modal('hide');
    $("#LABEL").focus();
});

//ok button
$("#OkBtn").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn").hide();
    $(".text-danger").hide();
    window.location.href = '{{route("transaction",[$FormId,"index"]) }}';
});

$("#OkBtn1").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn").hide();
    $("#OkBtn1").hide();
    $("#"+$(this).data('focusname')).focus();
    // $("[id*=txtlabel]").focus();
    $(".text-danger").hide();
});

//
function showError(pId,pVal){
    $("#"+pId+"").text(pVal);
    $("#"+pId+"").show();
}
function getFocus(){
    var FocusId=$("#FocusId").val();
    $("#"+FocusId).focus();
    $("#closePopup").click();
}
function highlighFocusBtn(pclass){
       $(".activeYes").hide();
       $(".activeNo").hide();
       
       $("."+pclass+"").show();
    }


    $('#table3').on('blur','[id*="IDMRP"]',function(event){
  if(intRegex.test($(this).val())){
    $(this).val($(this).val() + '.00000') ;
  }
});

          

$("#Material").on('click', '.add', function() {
    
    var $tr = $(this).closest('table');
    var allTrs = $tr.find('.participantRow').last();
    var lastTr = allTrs[allTrs.length-1];
    var $clone = $(lastTr).clone();
    $clone.find('td').each(function(){
        var el = $(this).find(':first-child');
        var id = el.attr('id') || null;
        if(id) {
            var i = id.substr(id.length-1);
            var prefix = id.substr(0, (id.length-1));
            el.attr('id', prefix+(+i+1));
        }
        var name = el.attr('name') || null;
        if(name) {
            var i = name.substr(name.length-1);
            var prefix1 = name.substr(0, (name.length-1));
            el.attr('name', prefix1+(+i+1));
        }
    });
    $clone.find('input:text').val('');
    $clone.find('input:hidden').val('');
    $clone.find('.remove').removeAttr('disabled'); 

    $clone.find('input:checkbox').prop('checked',false);;

    $tr.closest('table').append($clone);   
    var rowCount = $('#Row_Count1').val();
      rowCount = parseInt(rowCount)+1;
      $('#Row_Count1').val(rowCount);
      event.preventDefault();
}); 

$("#Material").on('click', '.remove', function() {
    var rowCount = $(this).closest('table').find('.participantRow').length;
    
    if (rowCount > 1) {
      $(this).closest('.participantRow').remove();
      rowCount = parseInt(rowCount)-1;
          $('#Row_Count1').val(rowCount);
    }
    if (rowCount <= 1) {
      $(document).find('.remove').prop('disabled', true);
    }
    event.preventDefault();
});


</script>


@endpush