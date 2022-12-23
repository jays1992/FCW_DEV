@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
  <div class="row">
    <div class="col-lg-2"><a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Serial No & Barcode (OUT)</a></div>

    <div class="col-lg-10 topnav-pd">
      <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
      <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
      <button class="btn topnavbt" id="btnSave" ><i class="fa fa-floppy-o"></i> Save</button>
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
  </div>
</div>


<form id="frm_trn_edit" onsubmit="return validateForm()"  method="POST" class="needs-validation"  >  
  @csrf
  {{isset($objResponse->BRCOID[0]) ? method_field('PUT') : '' }}

  <div class="container-fluid filter">
	  <div class="inner-form">

      <div class="row">
      <div class="col-lg-2 pl"><p>Doc No*</p></div>
			  <div class="col-lg-2 pl">
          <input {{$ActionStatus}} type="text" name="BRC_OUT_NO" id="BRC_OUT_NO" value="{{isset($objResponse->BRC_OUT_NO) && $objResponse->BRC_OUT_NO !=''?$objResponse->BRC_OUT_NO:''}}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase"  >
        </div>
			
              
        <div class="col-lg-2 pl"><p>Date*</p></div>
        <div class="col-lg-2 pl">
        <input {{$ActionStatus}} type="date" name="BRC_OUT_DT" id="BRC_OUT_DT" value="{{isset($objResponse->BRC_OUT_DT) && $objResponse->BRC_OUT_DT !=''?$objResponse->BRC_OUT_DT:''}}" onchange="checkPeriodClosing('{{$FormId}}',this.value,1)" class="form-control mandatory" >
          <span class="text-danger" id="ERROR_BRC_OUT_DT"></span>

        </div>
        <div class="col-lg-2 pl"><p>Document Type*</p></div>
        <div class="col-lg-2 pl">
          <select name="DOCTYPE" id="DOCTYPE" class="form-control" onchange="getDocType();">
          <option  value="">Select Document Type</option>
          <option  value="SALES_CHALLAN" {{isset($objResponse->SOURCE_TYPE) && $objResponse->SOURCE_TYPE=='SALES_CHALLAN' ? 'selected':''}} >Sales Challan</option>
          <option  value="MISR" {{isset($objResponse->SOURCE_TYPE) && $objResponse->SOURCE_TYPE=='MISR' ? 'selected':''}} >Material Issue Slip against RPR</option>
          <option  value="JOB_WORK_CHALLAN" {{isset($objResponse->SOURCE_TYPE) && $objResponse->SOURCE_TYPE=='JOB_WORK_CHALLAN' ? 'selected':''}} >Job Work Challan</option>
          <option  value="RGP" {{isset($objResponse->SOURCE_TYPE) && $objResponse->SOURCE_TYPE=='RGP' ? 'selected':''}} >RGP</option>
          <option  value="PURCHASE_RETURN" {{isset($objResponse->SOURCE_TYPE) && $objResponse->SOURCE_TYPE=='PURCHASE_RETURN' ? 'selected':''}} >Purchase Return</option>
          <option  value="STTOSTTRANSAFER" {{isset($objResponse->SOURCE_TYPE) && $objResponse->SOURCE_TYPE=='STTOSTTRANSAFER' ? 'selected':''}} >Stock To Stock Transfer</option>
          <option  value="MIS" {{isset($objResponse->SOURCE_TYPE) && $objResponse->SOURCE_TYPE=='MIS' ? 'selected':''}} >Material Issue Slip</option>
          <option  value="NRGP" {{isset($objResponse->SOURCE_TYPE) && $objResponse->SOURCE_TYPE=='NRGP' ? 'selected':''}} >NRGP</option>
          <option  value="STOCKADJUSTMENT" {{isset($objResponse->SOURCE_TYPE) && $objResponse->SOURCE_TYPE=='STOCKADJUSTMENT' ? 'selected':''}} >Stock Adjustment</option>
          <option  value="ASSEMBLING" {{isset($objResponse->SOURCE_TYPE) && $objResponse->SOURCE_TYPE=='ASSEMBLING' ? 'selected':''}} >Assembling</option>
          <option  value="DISSEMBLING" {{isset($objResponse->SOURCE_TYPE) && $objResponse->SOURCE_TYPE=='DISSEMBLING' ? 'selected':''}} >Dissembling</option>
          </select>
          </div>
        </div>



      <div class="row">

      <div class="col-lg-2 pl"><p>Document No</p></div>
        <div class="col-lg-2 pl">
        <input type="text" {{$ActionStatus}} name="Documentpopup" id="txtDOCpopup" class="form-control mandatory" value="{{isset($objResponse->SOURCE_DOCNO) && $objResponse->SOURCE_DOCNO !=''?$objResponse->SOURCE_DOCNO:''}}"  autocomplete="off"  readonly/>
        <input type="hidden" name="DOCID_REF" id="DOCID_REF" class="form-control" value="{{isset($objResponse->DOCID_REF) && $objResponse->DOCID_REF !=''?$objResponse->DOCID_REF:''}}" autocomplete="off" />
        </div>   

        <div class="col-lg-2 pl"><p>Remarks</p></div>
        <div class="col-lg-6 pl">
        <input type="text" {{$ActionStatus}} name="HEADER_REMARKS" id="HEADER_REMARKS" class="form-control mandatory" value="{{isset($objResponse->REMARKS) && $objResponse->REMARKS !=''?$objResponse->REMARKS:''}}"  autocomplete="off"  />

        </div>   

      </div>



    </div>

	  <div class="container-fluid">
      <div class="row">
        <ul class="nav nav-tabs">
          <li class="active"><a data-toggle="tab" href="#Main_Material">Material</a></li> 
        </ul>			
			  <div class="tab-content">
        <div id="Main_Material" class="tab-pane fade in active">
        <div class="row"><div class="col-lg-4" style="padding-left: 15px;">Note:- 1 row mandatory in Material Tab </div></div>
					<div class="table-responsive table-wrapper-scroll-y" style="height:400px;margin-top:10px;" >
						<table id="Main_example2" class="display nowrap table table-striped table-bordered itemlist w-200" width="100%" style="height:auto !important;">
							<thead id="thead1"  style="position: sticky;top: 0">
								  <tr>
                  
									<th>Item Code <input class="form-control" type="hidden" name="Main_Row_Count1" id ="Main_Row_Count1" value="{{isset($objMAT)?count($objMAT):1}}" ></th>
									<th>Item Name</th>
                  <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</th>
                  <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</th>
                  <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</th>
                  <th>Main UoM (MU)</th>
                  <th>Out Qty (MU)</th>			
                  <th {{$checkCompany==''? '':'hidden'}}>Alt Qty (MU)</th>
								<!--=	<th>Serial No Applicable</th>
									<th>Barcode Applicable</th>	-->
									<th>Action</th>
								  </tr>
							</thead>
							<tbody>
              @if(!empty($objMAT))
              @foreach($objMAT as $key => $row)
								  <tr  class="Main_participantRow">
                         <td><input  type="text" {{$ActionStatus}} name={{"Main_popupITEMID_".$key}} id={{"Main_popupITEMID_".$key}}  class="form-control"  autocomplete="off" value="{{ $row->ICODE }}"  readonly/></td>
                          <td hidden><input type="hidden"  name={{"Main_ITEMID_REF_".$key}} id={{"Main_ITEMID_REF_".$key}} value="{{ $row->ITEMID_REF }}"  class="form-control" autocomplete="off" /></td>
                          <td><input type="text" {{$ActionStatus}} name={{"Main_ItemName_".$key}} id={{"Main_ItemName_".$key}}  class="form-control"  autocomplete="off"  readonly value="{{ $row->ITEM_NAME }}" /></td>
                          
                          <td {{$AlpsStatus['hidden']}}><input {{$ActionStatus}} type="text" name="Alpspartno_{{$key}}" id="Alpspartno_{{$key}}" class="form-control"  autocomplete="off" value="{{ isset($row->ALPS_PART_NO)?$row->ALPS_PART_NO:''}}" readonly/></td>
                          <td {{$AlpsStatus['hidden']}}><input {{$ActionStatus}} type="text" name="Custpartno_{{$key}}" id="Custpartno_{{$key}}" class="form-control"  autocomplete="off" value="{{ isset($row->CUSTOMER_PART_NO)?$row->CUSTOMER_PART_NO:''}}" readonly/></td>
                          <td {{$AlpsStatus['hidden']}}><input {{$ActionStatus}} type="text" name="OEMpartno_{{$key}}"  id="OEMpartno_{{$key}}" class="form-control"  autocomplete="off"  value="{{ isset($row->OEM_PART_NO)?$row->OEM_PART_NO:'' }}" readonly/></td>
                          
                          
                          <td hidden><input type="text" {{$ActionStatus}} name={{"Main_Itemspec_".$key}} id={{"Main_Itemspec_".$key}}  class="form-control"  autocomplete="off"   /></td>


                          <td ><input type="text" {{$ActionStatus}} name={{"Main_popupMUOM_".$key}} id={{"Main_popupMUOM_".$key}}  class="form-control"  autocomplete="off" value="{{ $row->UOMCODE }}-{{ $row->DESCRIPTIONS }}"  readonly/></td>
                          <td><input type="text" {{$ActionStatus}} name={{"Main_RECEIVED_QTY_".$key}} id={{"Main_RECEIVED_QTY_".$key}}  class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" readonly value="{{ $row->OUT_QTY }}"  /></td>
                          <td {{$checkCompany==''? '':'hidden'}}><input type="text" {{$ActionStatus}} name={{"Alt_RECEIVED_QTY_".$key}} id={{"Alt_RECEIVED_QTY_".$key}}  class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" onchange="get_materital_item();" maxlength="13"  autocomplete="off"  value="{{ isset($row->ALT_QTY) ? $row->ALT_QTY:'' }}"   /></td>
                          <td hidden><input type="hidden" name={{"Main_MAIN_UOMID_REF_".$key}} id={{"Main_MAIN_UOMID_REF_".$key}}  class="form-control"  autocomplete="off"   value="{{ $row->UOMID_REF }}"/></td>
                          <td hidden><input type="text" name={{"Main_RECEIVED_QTY_MU_".$key}} id={{"Main_RECEIVED_QTY_MU_".$key}} readonly  class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off"  value="" /></td>
                          <td hidden><input type="text" name={{"Main_popupALTUOM_".$key}} id={{"Main_popupALTUOM_".$key}}  class="form-control"  autocomplete="off"  readonly/></td>
                          <td hidden><input type="hidden" name={{"Main_ALT_UOMID_REF_".$key}} id={{"Main_ALT_UOMID_REF_".$key}}  class="form-control"  autocomplete="off" /></td>
                          <td hidden><input type="text" name={{"Main_RECEIVED_QTY_AU_".$key}} id={{"Main_RECEIVED_QTY_AU_".$key}} class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" readonly maxlength="13"  autocomplete="off"   /></td>
                          <td hidden><input type="hidden" name={{"Main_SO_FQTY_".$key}} id={{"Main_SO_FQTY_".$key}} class="form-control three-digits" maxlength="13"  autocomplete="off" value="" readonly/></td>
                          <td hidden><input type="checkbox" name={{"SERIAL_NO_".$key}} id={{"SERIAL_NO_".$key}}  onclick="return false"  style="margin-left: 50%;"></td>
                          <td hidden><input type="checkbox" name={{"BARCODE_".$key}} id={{"BARCODE_".$key}}   onclick="return false" style="margin-left: 50%;"></td>
                          <td align="center" ><button class="btn Main_add Main_material" {{$ActionStatus}} title="add" data-toggle="tooltip" type="button" ><i class="fa fa-plus"></i></button>
                          <button class="btn Main_remove Main_dmaterial" disabled title="Delete" {{$ActionStatus}} data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
								  </tr>
								<tr></tr>
                @endforeach 
                @endif
							</tbody>
					  </table>

            <div id="material_item">
            <?php
								if(!empty($objMATDetail)){
									$Row_Count5 =   count($objMATDetail);

                  $ALPS_PARTNO=isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''? $TabSetting->FIELD8:'Add. Info Part No';
                  $CUSTOMER_PARTNO=isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''? $TabSetting->FIELD9:'Add. Info Customer Part No';
                  $OEM_PARTNO=isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''? $TabSetting->FIELD10:'Add. Info OEM Part No';
            
                  echo'<table id="example8" class="display nowrap table table-striped table-bordered itemlist w-200" width="100%" style="height:auto !important;">
                          <thead id="thead1"  style="position: sticky;top: 0">
                              <tr>
                              <th hidden ><input class="form-control" type="hidden" name="Row_Count5" id ="Row_Count5" value="'.$Row_Count5.'"></th>
                                  <th>Item Code</th>
                                  <th>Item Name</th>
                                  <th '.$AlpsStatus['hidden'].' >'.$ALPS_PARTNO.'</th>
                                  <th '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PARTNO.'</th>
                                  <th '.$AlpsStatus['hidden'].' >'.$OEM_PARTNO.'</th>    
                                  <th>UOM</th>
                                  <th>Received Qty</th>
                                  <th '.$checkCompany.'>Unit</th>
                                  <th '.$checkCompany.'>Qty</th>                   
                                  <th '.$checkCompany.'>Balance Qty</th>                   
                                  <th>Serial No</th>
                                  <th '.$checkCompany.'>Action</th>     
                      
                              </tr>
                          </thead>
                          <tbody>';

											foreach($objMATDetail as $index=>$row_data){

                        $SERIALNO_MODE=isset($row_data->SERIALNO_MODE) && $row_data->SERIALNO_MODE == "AUTOMATIC" ? "readonly":""; 


                      $BALANCEQTY=isset($row_data->PENDING_QTY) && $row_data->INQTY != $row_data->PENDING_QTY ? $row_data->PENDING_QTY: "0.00";         
   
                           echo '<tr  class="participantRow8">';
                           echo '<td><input type="text" '.$ActionStatus.' id="txtSUBITEM_popup_'.$index.'" value="'.$row_data->ICODE.'" class="form-control" readonly  /></td>';
                           echo '<td><input type="text" '.$ActionStatus.' id="SUBITEM_NAME_'.$index.'"     value="'.$row_data->ITEM_NAME.'"  class="form-control" readonly  /></td>';


                           echo '<td '.$AlpsStatus['hidden'].'><input type="text" '.$ActionStatus.'    id="ALPS_PART_NO_'.$index.'"     value="'.$row_data->ALPS_PART_NO.'"  class="form-control" readonly  /></td>';
                           echo '<td '.$AlpsStatus['hidden'].'><input type="text" '.$ActionStatus.'    id="CUSTOMER_PART_NO_'.$index.'"  value="'.$row_data->CUSTOMER_PART_NO.'"     class="form-control" readonly  /></td>';
                           echo '<td '.$AlpsStatus['hidden'].'><input type="text" '.$ActionStatus.'  value="'.$row_data->OEM_PART_NO.'"    id="OEM_PART_NO_'.$index.'"       class="form-control" readonly  /></td>';


                           echo '<td hidden><input type="text" '.$ActionStatus.'  name="REQ_ITEMID_REF_'.$index.'"   id="REQ_ITEMID_REF_'.$index.'"   value="'.$row_data->ITEMID_REF.'" /></td>';                        
                      
                           echo '<td><input    type="text" '.$ActionStatus.' name="REQ_MAIN_UOM_'.$index.'"           id="REQ_MAIN_UOM_'.$index.'"     value="'.$row_data->UOMCODE.'-'.$row_data->DESCRIPTIONS.'"   class="form-control" readonly   /></td>';
   
                           echo '<td hidden><input type="hidden" '.$ActionStatus.' name="REQ_UOMID_REF_'.$index.'"    id="REQ_UOMID_REF_'.$index.'"    value="'.$row_data->UOMID_REF.'" /></td>';
   
                           echo '<td ><input type="text" '.$ActionStatus.' style="text-align:right;" name="REQ_RETURN_QTY_'.$index.'" class="form-control" readonly   id="REQ_RETURN_QTY_'.$index.'" 
                           value="'.number_format($row_data->OUT_QTY,2).'"  /></td>';
   
   
                           echo '<td '.$checkCompany.' ><input type="text" '.$ActionStatus.' name="PACKUOM_'.$index.'" class="form-control" readonly  onclick="getUOM(this.id,'.$row_data->ITEMID_REF.')"   id="PACKUOM_'.$index.'" value="'.$row_data->UOMCODE1.'-'.$row_data->DESCRIPTIONS1.'"   /></td>';
                           echo '<td hidden ><input type="text" '.$ActionStatus.' name="PACKUOMID_REF_'.$index.'"    id="PACKUOMID_REF_'.$index.'" value="'.$row_data->UNIT_REF.'" /></td>';                                    
   
                           echo '<td '.$checkCompany.' ><input type="text" '.$ActionStatus.' name="REQ_QTY_'.$index.'" readonly class="form-control"    id="REQ_QTY_'.$index.'"   value="'.$row_data->QTY.'"  /></td>';
   
                           echo '<td '.$checkCompany.' ><input type="text" '.$ActionStatus.' name="PENDING_QTY_'.$index.'" readonly class="form-control"    id="PENDING_QTY_'.$index.'"   value="'.$BALANCEQTY.'"  /></td>';
            

                           echo '<td><input type="text" '.$ActionStatus.' name="txtSR_popup_'.$index.'" class="form-control" readonly     id="txtSR_popup_'.$index.'"  value="'.$row_data->SERIALNUMBER.'"  /></td>';
                           echo '<td hidden ><input type="text" name="SERIALNO_REF_'.$index.'"    id="SERIALNO_REF_'.$index.'"  value="'.$row_data->SERIALNUMBER.'" /></td>';    

                          echo '<td hidden ><input type="text" '.$ActionStatus.' name="SERIAL_NO_APPLICABLE_'.$index.'" class="form-control"   id="SERIAL_NO_APPLICABLE_'.$index.'"   value="" /></td>';   
                          echo '<td hidden><input type="text" '.$ActionStatus.' name="BARCODE_APPLICABLE_'.$index.'" class="form-control"   id="BARCODE_APPLICABLE_'.$index.'"   value="" /></td>';  
                         
                          echo '<td '.$checkCompany.' style="width: 106px;" align="center" ><button class="btn Sub_add Sub_material" '.$ActionStatus.' title="add" data-toggle="tooltip" type="button" ><i class="fa fa-plus"></i></button>
                          <button class="btn Sub_remove Sub_dmaterial" '.$ActionStatus.' title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>';
                          echo '</tr>';
                       
											}
											
										echo '</tbody>';

    

									echo'</table>';
								}
								else{
									echo "Record not found.";
								}
								
								?>

            </div>
					</div>	
				</div>
       
       

			  </div>
		  </div>
	  </div>
  </div>
</form>
@endsection

@section('alert')


<!-- GRN Popup starts here   -->
<div id="DOC_popup" class="modal" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-md" style="width:60%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" id="GRN_closePopup">&times;</button>
            </div>
            <div class="modal-body">
                <div class="tablename"><p>Document No List</p></div>
                <div class="single single-select table-responsive table-wrapper-scroll-y my-custom-scrollbar">
                <input type="hidden" class="mainitem_tab1">
                    <table id="GRN" class="display nowrap table table-striped table-bordered" style="width:100%;">
                        <thead>
                            <tr>
                            <th style="width:10%;">Select</th> 
                                <th style="width:30%;"> Doc No</th>
                                <th style="width:30%;"> Doc Date</th>
                                <th style="width:30%;"> Customer/Vendor Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                            <th style="text-align:center; width:10%;">&#10004;</th>  
                                <td style="width:30%;"> 
                                    <input type="text" id="grn_no" class="form-control" onkeyup="GRNFunction()"  />
                                </td>
                                <td style="width:30%;">
                                    <input type="text" id="grn_dt" class="form-control" onkeyup="GRNDTFunction()"  />
                                </td>
                                <td style="width:30%;">
                                    <input type="text" id="grn_Vendor" class="form-control" onkeyup="GRNVendorFunction()"  />
                                </td>
                          
                            </tr>
                        </tbody>
                    </table>
                    <table id="GRNTable2" class="display nowrap table table-striped table-bordered">
                        <thead id="thead2">
                        <tr>        
                <td id="Data_seach" colspan="4">please wait...</td>
                   </tr>
                        </thead>
                        <tbody id="DOCresult">                           
                        </tbody>
                    </table>
                </div>
                <div class="cl"></div>
            </div>
        </div>
    </div>
</div>







<div id="alert" class="modal"  role="dialog"  data-backdrop="static" >
  <div class="modal-dialog" >
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
          <button class="btn alertbt" name='OkBtn1' id="OkBtn1" style="display:none;margin-left: 90px;" onclick="getFocus()"><div id="alert-active" class="activeOk1"></div>OK</button>
          <input type="hidden" id="FocusId" >
        </div>
		    <div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<!--Material Tab Starts Here -->

<div id="Main_ITEMIDpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:90%;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='Main_ITEMID_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Item Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="Main_ItemIDTable" class="display nowrap table  table-striped table-bordered" style="width:100%;" >
    <thead>
      <tr id="none-select" class="searchalldata" hidden>
            
            <td> <input type="hidden" name="fieldid" id="hdn_ItemID"/>
            <input type="hidden" name="fieldid2" id="hdn_ItemID2"/>
            <input type="hidden" name="fieldid3" id="hdn_ItemID3"/>
            <input type="hidden" name="fieldid4" id="hdn_ItemID4"/>
            <input type="hidden" name="fieldid5" id="hdn_ItemID5"/>
            <input type="hidden" name="fieldid6" id="hdn_ItemID6"/>
            <input type="hidden" name="fieldid7" id="hdn_ItemID7"/>
            <input type="hidden" name="fieldid8" id="hdn_ItemID8"/>
            <input type="hidden" name="fieldid9" id="hdn_ItemID9"/>
            <input type="hidden" name="fieldid10" id="hdn_ItemID10"/>
            <input type="hidden" name="fieldid11" id="hdn_ItemID11"/>

            <input type="hidden" name="fieldid15" id="hdn_ItemID15"/>
            <input type="hidden" name="fieldid16" id="hdn_ItemID16"/>
            <input type="hidden" name="fieldid17" id="hdn_ItemID17"/>

            <!--<input type="hidden" name="fieldid18" id="hdn_ItemID18"/>
            <input type="hidden" name="fieldid19" id="hdn_ItemID19"/>
            <input type="hidden" name="fieldid20" id="hdn_ItemID20"/>-->

            </td>
      </tr>
      
      <tr>
            <th style="width:8%;" id="all-check">Select</th>
            <th style="width:10%;">Item Code</th>
            <th style="width:10%;">Name</th>
            <th style="width:8%;">Main UOM</th>
            <th style="width:8%;">Main QTY</th>
            <th style="width:8%;">Item Group</th>
            <th style="width:8%;">Item Category</th>
            <th style="width:8%;">Business Unit</th>
            <th style="width:8%;" {{$AlpsStatus['hidden']}}>{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</th>
            <th style="width:8%;" {{$AlpsStatus['hidden']}}>{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</th>
            <th style="width:8%;" {{$AlpsStatus['hidden']}}>{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</th>
            <th style="width:8%;">Status</th>
      </tr>
    </thead>
    <tbody>
    <tr>
    <td style="width:8%;text-align:center;"><input type="checkbox" class="Main_js-selectall" data-target=".Main_js-selectall1" /></td>
    <td style="width:10%;">
    <input type="text" id="Main_Itemcodesearch" class="form-control" autocomplete="off" onkeyup="Main_ItemCodeFunction(event)">
    </td>
    <td style="width:10%;">
    <input type="text" id="Main_Itemnamesearch" class="form-control" autocomplete="off" onkeyup="Main_ItemNameFunction(event)">
    </td>
    <td style="width:8%;">
    <input type="text" id="Main_ItemUOMsearch" class="form-control" autocomplete="off" onkeyup="Main_ItemUOMFunction(event)">
    </td>
    <td style="width:8%;">
    <input type="text" id="Main_ItemQTYsearch" class="form-control" autocomplete="off" onkeyup="Main_ItemQTYFunction(event)" readonly>
    </td>
    <td style="width:8%;">
    <input type="text" id="Main_ItemGroupsearch" class="form-control" autocomplete="off" onkeyup="Main_ItemGroupFunction(event)">
    </td>
    <td style="width:8%;">
    <input type="text" id="Main_ItemCategorysearch" class="form-control" autocomplete="off" onkeyup="Main_ItemCategoryFunction(event)">
    </td>

    <td style="width:8%;"><input type="text" id="Main_ItemBUsearch" class="form-control" autocomplete="off" onkeyup="ItemBUFunction(event)" readonly></td>
    <td style="width:8%;" {{$AlpsStatus['hidden']}}><input type="text" id="Main_ItemAPNsearch" class="form-control" autocomplete="off" onkeyup="Main_ItemAPNFunction(event)"></td>
    <td style="width:8%;" {{$AlpsStatus['hidden']}}><input type="text" id="Main_ItemCPNsearch" class="form-control" autocomplete="off" onkeyup="Main_ItemCPNFunction(event)"></td>
    <td style="width:8%;" {{$AlpsStatus['hidden']}}><input type="text" id="Main_ItemOEMPNsearch" class="form-control" autocomplete="off" onkeyup="Main_ItemOEMPNFunction(event)"></td>

    <td style="width:8%;">
    <input type="text" id="Main_ItemStatussearch" class="form-control" onkeyup="Main_ItemStatusFunction(event)" readonly>
    </td>
    </tr>
    </tbody>
    </table>
      <table id="Main_ItemIDTable2" class="display nowrap table  table-striped table-bordered" style="width:100%;" >
        <thead id="thead2">

        </thead>
        <tbody id="Main_tbody_ItemID">     
          
        <div class="loader" id="item_loader" style="display:none;"></div>
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>



<!-- UOM Type Dropdown -->
<div id="UOMpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:60%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='UOMclosePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>UOM</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="UOMTable" class="display nowrap table  table-striped table-bordered" style="width:100%;" >
    <thead>
      <tr id="none-select" class="searchalldata" hidden>
            
            <td> <input type="hidden" name="fieldid" id="hdn_UOM"/>
            <input type="hidden" name="fieldid2" id="hdn_UOM2"/>
            </td>
      </tr>
     
      <tr>
            <th style="width:10%;"></th>
            <th style="width:30%;">Code</th>
            <th style="width:60%;">Description</th>
      </tr>
    </thead>
    <tbody>
    <tr>
    <th style="text-align:center; width:10%;">&#10004;</th>
   <td style="width:30%;"> 
    
    <input type="text" id="UOMcodesearch" class="form-control" onkeyup="UOMCodeFunction()">
    </td>
    <td>
    <input type="text" id="UOMnamesearch" class="form-control" onkeyup="UOMNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="UOMTable2" class="display nowrap table  table-striped table-bordered" width="100%" >
        <thead id="thead2">
        </thead>
        <tbody>
        @foreach ($objUOM as $uomindex=>$uomRow)
        <tr >

        <td style="text-align:center; width:10%"> <input type="checkbox" name="uomcheck[]" id="uomidcode_{{ $uomindex }}" class="clsuomid" value="{{ $uomRow-> UOMID }}" ></td>


        
          <td style="width:30%">{{ $uomRow-> UOMCODE }}
          <input type="hidden" id="txtuomidcode_{{ $uomindex }}" data-desc="{{ $uomRow-> UOMCODE }} - {{$uomRow-> DESCRIPTIONS}}"  value="{{ $uomRow-> UOMID }}"/>
          </td>
          <td wistyle="width:60%">{{ $uomRow-> DESCRIPTIONS }}</td>
        </tr>
        @endforeach
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!--UOM Type Dropdown-->


<!--Serial No  dropdown-->
<div id="STRpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='STR_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Serial No/Barcode</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="STRNOTable" class="display nowrap table  table-striped table-bordered" >
    <thead>
          <tr id="none-select" class="searchalldata" hidden>
            
            <td> 
            <input type="hidden"  id="hdn_STRid"/>
            <input type="hidden" id="hdn_STRid2"/>
            <input type="hidden" id="hdn_STRid3"/>
            </td>
          </tr>

      <tr>
        
        <th style="width:10%">Select</th> 
        <th style="width:30%">Serial No</th>
        <th style="width:30%" class="ROWQTY">Qty</th>
        <th style="width:30%">Date</th> 
      </tr>
    </thead>
    <tbody>

      <tr>
        <th style="width:10%"><span class="check_th">&#10004;</span></th>
        <td style="width:30%"><input type="text" id="STRcodesearch" class="form-control" onkeyup="STRCodeFunction()"></td>
        <td style="width:30%" class="ROWQTY"><input type="text" id="STRnamesearch" class="form-control" onkeyup="STRNameFunction()"></td>
        <td style="width:30%" class=""><input type="text" id="STRDTsearch" class="form-control" onkeyup="STRDTFunction()"></td>
      </tr>

    </tbody>
    </table>
      <table id="STRNOTable2" class="display nowrap table  table-striped table-bordered"  >
        <thead id="thead2">

        </thead>
        <tbody id="tbody_STR">     
        
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>






@endsection

@push('bottom-css')
<style>
.text-danger{
color:red !important;
}
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
width: 100%;
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
width: 100%;
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
}
#StoreTable {
border-collapse: collapse;
width: 950px;
border: 1px solid #ddd;
font-size: 11px;
}
#StoreTable th {
text-align: center;
padding: 5px;
font-size: 11px;
color: #0f69cc;
font-weight: 600;
}
#StoreTable td {
text-align: center;
padding: 5px;
font-size: 11px;
font-weight: 600;
}

#Main_StoreTable {
border-collapse: collapse;
width: 950px;
border: 1px solid #ddd;
font-size: 11px;
}
#Main_StoreTable th {
text-align: center;
padding: 5px;
font-size: 11px;
color: #0f69cc;
font-weight: 600;
}
#Main_StoreTable td {
text-align: center;
padding: 5px;
font-size: 11px;
font-weight: 600;
}

.qtytext{
display: block;
width: 100%;
height: 24px;
padding: 6px 6px;
font-size: 14px;
line-height: 1.42857143;
color: #555;
background-color: #fff;
background-image: none;
border: 1px solid #ccc;
}
</style>
@endpush

@push('bottom-scripts')
<script>
/*================================== BUTTON FUNCTION ================================*/
$('#btnAdd').on('click', function() {
  var viewURL = '{{route("transaction",[$FormId,"add"])}}';
  window.location.href=viewURL;
});

$('#btnExit').on('click', function() {
  var viewURL = '{{route('home')}}';
  window.location.href=viewURL;
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
});

window.fnUndoYes = function (){
  window.location.reload();
}

$("#btnSave").click(function() {
  var formReqData = $("#frm_trn_edit");
  if(formReqData.valid()){
    validateForm('fnSaveData','update');
  }
});

$("#btnApprove").click(function(){
  var formReqData = $("#frm_trn_edit");
  if(formReqData.valid()){
    validateForm('fnApproveData','approve');
  }
});

$("#YesBtn").click(function(){
  $("#alert").modal('hide');
  var customFnName = $("#YesBtn").data("funcname");
  window[customFnName]();
});

$("#NoBtn").click(function(){
  $("#alert").modal('hide');
  $("#LABEL").focus();
});

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
  $(".text-danger").hide();
});

function showError(pId,pVal){
  $("#"+pId+"").text(pVal);
  $("#"+pId+"").show();
}

function getFocus(){
  $("#"+$("#FocusId").val()).focus();
  $("#closePopup").click();
}

function highlighFocusBtn(pclass){
  $(".activeYes").hide();
  $(".activeNo").hide();
  $("."+pclass+"").show();
}

/*================================== Save FUNCTION =================================*/
/*================================== Update FUNCTION =================================*/
window.fnSaveData = function (){

event.preventDefault();

var trnFormReq  = $("#frm_trn_edit");
var formData    = trnFormReq.serialize();

$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});
$("#btnSave").hide(); 
$(".buttonload").show(); 
$("#btnApprove").prop("disabled", true);
$.ajax({
  url:'{{ route("transaction",[$FormId,"update"])}}',
  type:'POST',
  data:formData,
  success:function(data) {
    $(".buttonload").hide(); 
    $("#btnSave").show();   
    $("#btnApprove").prop("disabled", false);

    if(data.errors) {
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").show();
      $("#AlertMessage").text(data.msg);
      $("#alert").modal('show');
      $("#OkBtn").focus();
      $(".text-danger").show();
    }
    else if(data.success) {                   
      console.log("succes MSG="+data.msg);
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").show();
      $("#AlertMessage").text(data.msg);
      $("#alert").modal('show');
      $("#OkBtn").focus();
      $(".text-danger").hide();
    }
    
  },
  error:function(data){
      $(".buttonload").hide(); 
      $("#btnSave").show();   
      $("#btnApprove").prop("disabled", false);
      console.log("Error: Something went wrong.");
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Error: Something went wrong.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk1');
  },

});

}

/*================================== Approve FUNCTION =================================*/
window.fnApproveData = function (){

  event.preventDefault();
var trnFormReq  = $("#frm_trn_edit");
var formData    = trnFormReq.serialize();

$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});
$("#btnApprove").hide(); 
$(".buttonload_approve").show();  
$("#btnSave").prop("disabled", true);
$.ajax({
  url:'{{ route("transaction",[$FormId,"Approve"])}}',
  type:'POST',
  data:formData,
  success:function(data) {
    $("#btnApprove").show();  
    $(".buttonload_approve").hide();  
    $("#btnSave").prop("disabled", false);

    if(data.errors) {
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").show();
      $("#AlertMessage").text(data.msg);
      $("#alert").modal('show');
      $("#OkBtn").focus();
      $(".text-danger").show();
    }
    else if(data.success) {                   
      console.log("succes MSG="+data.msg);
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").show();
      $("#AlertMessage").text(data.msg);
      $("#alert").modal('show');
      $("#OkBtn").focus();
      $(".text-danger").hide();
    }
    
  },
  error:function(data){
      $("#btnApprove").show();  
      $(".buttonload_approve").hide();  
      $("#btnSave").prop("disabled", false);
      console.log("Error: Something went wrong.");
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Error: Something went wrong.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk1');
  },

});

}
/*================================== VALIDATE FUNCTION =================================*/

function validateForm(actionType,actionMsg){

  $("#FocusId").val('');
  var BRC_OUT_NO   = $.trim($("#BRC_OUT_NO").val());
  var BRC_OUT_DT   = $.trim($("#BRC_OUT_DT").val());
  var DOCID_REF   = $.trim($("#DOCID_REF").val());
  var DOCTYPE   = $.trim($("#DOCTYPE").val());

  var checkCompany  = "{{$checkCompany}}";
  
  if(BRC_OUT_NO ===""){
      $("#FocusId").val('BRC_OUT_NO');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Doc No is required.');
      $("#alert").modal('show')
      $("#OkBtn1").focus();
      return false;
  }
  else if(BRC_OUT_DT ===""){
      $("#FocusId").val('BRC_OUT_DT');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please select Date.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(DOCTYPE ===""){
      $("#FocusId").val('DOCTYPE');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please Select Document Type.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 

  else if(DOCID_REF ===""){
    $("#FocusId").val('txtDOCpopup');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please select Docuemnt No');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else{
    event.preventDefault();
    var allblank1   = [];
    var allblank2   = [];
    var allblank3   = [];
    var allblank4   = [];
    var allblank5   = [];
    var allblank6   = [];
    var allblank7   = [];

    var allblank8   = [];
    var allblank9   = [];
    var allblank10  = [];
  

    var focustext   = "";
    var focustext1  = "";
    var focustext2  = "";
    var focustext3  = "";
    var focustext4  = "";
    var focustext5  = "";

      
    $('#Main_example2').find('.Main_participantRow').each(function(){

      if($.trim($(this).find("[id*=Main_ITEMID_REF]").val())!=""){
        allblank1.push('true');

        if($.trim($(this).find("[id*=Main_MAIN_UOMID_REF]").val())!=""){
          allblank2.push('true');

          if($.trim($(this).find('[id*="Main_RECEIVED_QTY_MU"]').val()) != ""){
            allblank3.push('true');
          }
          else{
            allblank3.push('false');
            focustext = $(this).find("[id*=Main_RECEIVED_QTY_MU]").attr('id');
          }  
        }
        else{
            allblank2.push('false');
            focustext = $(this).find("[id*=popupMUOM]").attr('id');
        }    
        
        
      }
     
      else{
        allblank1.push('false'); 
        focustext = $(this).find("[id*=popupITEMID]").attr('id');
      }



       if($.trim($(this).find("[id*=Main_RECEIVED_QTY]").val()) !=""){
        allblank9.push($.trim($(this).find("[id*=Main_RECEIVED_QTY]").val()));   
        focustext4 = '';  
        }else{
          focustext4 = $(this).find("[id*=Main_RECEIVED_QTY]").attr('id');
        }


        if($.trim($(this).find("[id*=Alt_RECEIVED_QTY]").val()) !=""){
        allblank10.push($.trim($(this).find("[id*=Alt_RECEIVED_QTY]").val()));   
        focustext5 = '';  
        }else{
          focustext5 = $(this).find("[id*=Alt_RECEIVED_QTY]").attr('id');
        }


     

    });


 
  $('#material_item').find('.participantRow8').each(function(){
    if($.trim($(this).find("[id*=PACKUOM]").val()) ==="" && checkCompany==''){
        allblank4.push('false');
        focustext1 = $(this).find("[id*=PACKUOM]").attr('id');
        return false;
      }
      else if($.trim($(this).find("[id*=REQ_QTY]").val()) ==="" && checkCompany==''){       
        allblank5.push('false');
        focustext2 = $(this).find("[id*=REQ_QTY]").attr('id');
        return false;
      }
      else if($.trim($(this).find("[id*=txtSR_popup]").val()) ===""){       
        allblank6.push('false');
        focustext3 = $(this).find("[id*=txtSR_popup]").attr('id');
        return false;
      }


      else if($.trim($(this).find("[id*=txtSR_popup]").val()) !="" && checkCompany==''){
        allblank7.push($.trim($(this).find("[id*=txtSR_popup]").val()));  
      
      }

      if($.trim($(this).find("[id*=REQ_QTY]").val()) !=""){
        allblank8.push($.trim($(this).find("[id*=REQ_QTY]").val()));     
      }


});   

    if(jQuery.inArray("false", allblank1) !== -1){
      $("#FocusId").val(focustext);
      $("#alert").modal('show');
      $("#AlertMessage").text('Please Select Item In Material');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank2) !== -1){
      $("#FocusId").val(focustext);
      $("#alert").modal('show');
      $("#AlertMessage").text('Main UOM is missing in in material tab.');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(array_sum(allblank10) == 0 && checkCompany==''){
      $("#FocusId").val(focustext5);
      $("#alert").modal('show');
      $("#AlertMessage").text('Please Enter Alt Qty');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank4) !== -1){
      $("#FocusId").val(focustext1);
      $("#alert").modal('show');
      $("#AlertMessage").text('Please Select Unit');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank5) !== -1){
      $("#FocusId").val(focustext2);
      $("#alert").modal('show');
      $("#AlertMessage").text('Please enter Out Qty');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }else if(jQuery.inArray("false", allblank6) !== -1){
      $("#FocusId").val(focustext3);
      $("#alert").modal('show');
      $("#AlertMessage").text('Serial No should not be blank');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }else if(toFindDuplicates(allblank7)==true){
      $("#alert").modal('show');
      $("#AlertMessage").text('Serial No should not be duplicate');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    
    }else if(parseFloat(array_sum(allblank8)) != parseFloat(array_sum(allblank9)) && checkCompany==''){
      $("#alert").modal('show');
      $("#AlertMessage").text('Out Qty (MU) and Qty should be equal');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(checkPeriodClosing('{{$FormId}}',$("#BRC_OUT_DT").val(),0) ==0){
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
     $("#AlertMessage").text('Do you want to '+actionMsg+' to record.');
     $("#YesBtn").data("funcname",actionType);     
     $("#YesBtn").focus();
     $("#OkBtn").hide();
     highlighFocusBtn('activeYes');
   }
 }
}



/*================================== POPUP SHORTING FUNCTION =================================*/
"use strict";
var w3 = {};
w3.getElements = function (id) {
if (typeof id == "object") {
return [id];
} else {
return document.querySelectorAll(id);
}
};
w3.sortHTML = function(id, sel, sortvalue) {
var a, b, i, ii, y, bytt, v1, v2, cc, j;
a = w3.getElements(id);
for (i = 0; i < a.length; i++) {
for (j = 0; j < 2; j++) {
cc = 0;
y = 1;
while (y == 1) {
  y = 0;
  b = a[i].querySelectorAll(sel);
  for (ii = 0; ii < (b.length - 1); ii++) {
    bytt = 0;
    if (sortvalue) {
      v1 = b[ii].querySelector(sortvalue).innerText;
      v2 = b[ii + 1].querySelector(sortvalue).innerText;
    } else {
      v1 = b[ii].innerText;
      v2 = b[ii + 1].innerText;
    }
    v1 = v1.toLowerCase();
    v2 = v2.toLowerCase();
    if ((j == 0 && (v1 > v2)) || (j == 1 && (v1 < v2))) {
      bytt = 1;
      break;
    }
  }
  if (bytt == 1) {
    b[ii].parentNode.insertBefore(b[ii + 1], b[ii]);
    y = 1;
    cc++;
  }
}
if (cc > 0) {break;}
}
}
};



let GL = "#GRNTable2";
let GL2 = "#GRN";
let GLheaders = document.querySelectorAll(GL2 + " th");
// Sort the table element when clicking on the table headers
GLheaders.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(GL, ".clsspid_reasoncode1", "td:nth-child(" + (i + 1) + ")");
  });
});

function GRNFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("grn_no");
  filter = input.value.toUpperCase();
  table = document.getElementById("GRNTable2");
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

function GRNDTFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("grn_dt");
  filter = input.value.toUpperCase();
  table = document.getElementById("GRNTable2");
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
function GRNVendorFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("grn_Vendor");
  filter = input.value.toUpperCase();
  table = document.getElementById("GRNTable2");
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

$("#GRN_closePopup").click(function(event){
  $("#DOC_popup").hide();
});

function bindDocumentEvents(){
$(".clsspid_prr").click(function(){       

  var fieldid = $(this).attr('id');
  var txtval =    $("#txt"+fieldid+"").val();
  var texdesc =   $("#txt"+fieldid+"").data("desc");
  var texcode =   $("#txt"+fieldid+"").data("code"); 
  $('#txtDOCpopup').val(texcode);
  $('#DOCID_REF').val(txtval);        
  $("#DOC_popup").hide();   
  $("#grn_no").val(''); 
  $("#grn_dt").val('');       
  clearGrid(); 
  $("#material_item").html('');
  event.preventDefault();
});
}


$('#txtDOCpopup').click(function(event){
          var DOCTYPE = $("#DOCTYPE").val(); 
          if(DOCTYPE==""){
            $("#FocusId").val('DOCTYPE');
            $("#alert").modal('show');
            $("#AlertMessage").text('Please Select Document Type');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk');
            return false; 
          }
        showSelectedCheck($("#DOCID_REF").val(),"DOCUMENT_NO");
        $("#DOC_popup").show();
        event.preventDefault();
      });


  
      function getDocType(){

var DOCTYPE = $("#DOCTYPE").val(); 
clearGrid(); 
$("#material_item").html('');
$("#txtDOCpopup").val('');
$("#DOCID_REF").val('');
if(DOCTYPE==''){
  $("#DOCresult").html('No Records Found.');
  return false;
}
  $("#DOCresult").html('');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $("#Data_seach").show();
    $.ajax({
        url:'{{route("transaction",[$FormId,"GetDocument"])}}',
        type:'POST',
        data:{DOCTYPE:DOCTYPE},
        success:function(data) {                                
          $("#Data_seach").hide();
          $("#DOCresult").html(data);   
           bindDocumentEvents();                                        
        },
        error:function(data){
          console.log("Error: Something went wrong.");
          $("#DOCresult").html('');                        
        },
    }); 


}

/*==================================DOCUMENT NO POPUP ENDS HERE====================================*/





/*==================================================FIRST MATERIAL TAB SECTION STARTS HERE ===================================*/


let Main_itemtid = "#Main_ItemIDTable2";
let Main_itemtid2 = "#Main_ItemIDTable";
let Main_itemtidheaders = document.querySelectorAll(Main_itemtid2 + " th");

Main_itemtidheaders.forEach(function(element, i) {
element.addEventListener("click", function() {
w3.sortHTML(Main_itemtid, ".Main_clsitemid", "td:nth-child(" + (i + 1) + ")");
});
});

function Main_ItemCodeFunction(e) {
  if(e.which == 13){
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("Main_Itemcodesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("Main_ItemIDTable2");
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

function Main_ItemNameFunction(e) {
  if(e.which == 13){
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("Main_Itemnamesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("Main_ItemIDTable2");
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
}

function Main_ItemUOMFunction(e) {
  if(e.which == 13){
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("Main_ItemUOMsearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("Main_ItemIDTable2");
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
}

function Main_ItemQTYFunction(e) {
  if(e.which == 13){
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("Main_ItemQTYsearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("Main_ItemIDTable2");
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
}

function Main_ItemGroupFunction(e) {
  if(e.which == 13){
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("Main_ItemGroupsearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("Main_ItemIDTable2");
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
}

function Main_ItemCategoryFunction(e) {
  if(e.which == 13){
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("Main_ItemCategorysearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("Main_ItemIDTable2");
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
}

function Main_ItemBUFunction(e) {
  if(e.which == 13){
	var input, filter, table, tr, td, i, txtValue;
	input = document.getElementById("Main_ItemBUsearch");
	filter = input.value.toUpperCase();
	table = document.getElementById("Main_ItemIDTable2");
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
}

function Main_ItemAPNFunction(e) {
  if(e.which == 13){
	var input, filter, table, tr, td, i, txtValue;
	input = document.getElementById("Main_ItemAPNsearch");
	filter = input.value.toUpperCase();
	table = document.getElementById("Main_ItemIDTable2");
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
}

function Main_ItemCPNFunction(e) {
  if(e.which == 13){
	var input, filter, table, tr, td, i, txtValue;
	input = document.getElementById("Main_ItemCPNsearch");
	filter = input.value.toUpperCase();
	table = document.getElementById("Main_ItemIDTable2");
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
}

function Main_ItemOEMPNFunction(e) {
  if(e.which == 13){
	var input, filter, table, tr, td, i, txtValue;
	input = document.getElementById("Main_ItemOEMPNsearch");
	filter = input.value.toUpperCase();
	table = document.getElementById("Main_ItemIDTable2");
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
}

function Main_ItemStatusFunction(e) {
  if(e.which == 13){
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("Main_ItemStatussearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("Main_ItemIDTable2");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[11];
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

$('#Main_Material').on('click','[id*="Main_popupITEMID"]',function(event){
var DOCID_REF    = $("#DOCID_REF").val();
var docType      = $("#DOCTYPE").val();
// alert(DOCID_REF); 
if(DOCID_REF ===""){      
$("#FocusId").val('txtDOCpopup');
$("#YesBtn").hide();
$("#NoBtn").hide();
$("#OkBtn1").show();
$("#AlertMessage").text('Please select Document No.');
$("#alert").modal('show')
$("#OkBtn1").focus();
return false;
}else{
$('#item_loader').show();
$("#Main_tbody_ItemID").html('');
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$.ajax({
    url:'{{route("transaction",[$FormId,"Main_getItemDetails"])}}',
    type:'POST',
    data:{'status':'A',DOCID_REF:DOCID_REF,docType:docType},
    success:function(data) {
      $('#item_loader').hide();
      $("#Main_tbody_ItemID").html(data);    
      Main_bindItemEvents();   
      //$('.js-selectall').prop('disabled', true);                     
    },
    error:function(data){
      console.log("Error: Something went wrong.");
      $('#item_loader').hide();
      $("#Main_tbody_ItemID").html('');                        
    },
}); 
  

$("#Main_ITEMIDpopup").show();
var id = $(this).attr('id');
var id2 = $(this).parent().parent().find('[id*="Main_ITEMID_REF"]').attr('id');
var id3 = $(this).parent().parent().find('[id*="Main_ItemName"]').attr('id');
var id4 = $(this).parent().parent().find('[id*="Main_Itemspec"]').attr('id');
var id5 = $(this).parent().parent().find('[id*="Main_popupMUOM"]').attr('id');
var id6 = $(this).parent().parent().find('[id*="Main_MAIN_UOMID_REF"]').attr('id');
var id7 = $(this).parent().parent().find('[id*="Main_RECEIVED_QTY"]').attr('id');
var id11 = $(this).parent().parent().find('[id*="Main_SO_FQTY"]').attr('id');



var id8 = $(this).parent().parent().find('[id*="Main_popupALTUOM"]').attr('id');
var id9 = $(this).parent().parent().find('[id*="Main_ALT_UOMID_REF"]').attr('id');
var id10 = $(this).parent().parent().find('[id*="Main_RECEIVED_QTY_AU"]').attr('id');

var id15 = $(this).parent().parent().find('[id*="SERIAL_NO"]').attr('id');
var id16 = $(this).parent().parent().find('[id*="BARCODE"]').attr('id');




$('#hdn_ItemID').val(id);
$('#hdn_ItemID2').val(id2);
$('#hdn_ItemID3').val(id3);
$('#hdn_ItemID4').val(id4);
$('#hdn_ItemID5').val(id5);
$('#hdn_ItemID6').val(id6);
$('#hdn_ItemID7').val(id7);
$('#hdn_ItemID11').val(id11);


$('#hdn_ItemID8').val(id8);
$('#hdn_ItemID9').val(id9);
$('#hdn_ItemID10').val(id10);

$('#hdn_ItemID15').val(id15);
$('#hdn_ItemID16').val(id16);


event.preventDefault();
}
});

$("#Main_ITEMID_closePopup").click(function(event){
$("#Main_ITEMIDpopup").hide();
});

function Main_bindItemEvents(){

$('#Main_ItemIDTable2').off(); 

$('.Main_js-selectall').change(function(){
  var isChecked = $(this).prop("checked");
  var selector = $(this).data('target');
  $(selector).prop("checked", isChecked);

  $('#Main_ItemIDTable2').find('.Main_clsitemid').each(function(){

    var fieldid = $(this).attr('id');
    var txtval =   $("#txt"+fieldid+"").val();
    var texdesc =  $("#txt"+fieldid+"").data("desc");
    var fieldid2 = $(this).children('[id*="itemname"]').attr('id');
    var txtname =  $("#txt"+fieldid2+"").val();
    var txtspec =  $("#txt"+fieldid2+"").data("desc");
    var fieldid3 = $(this).children('[id*="itemuom"]').attr('id');
    var txtmuomid =  $("#txt"+fieldid3+"").val();
    var txtauom =  $("#txt"+fieldid3+"").data("desc");
    var txtmuom =  $(this).children('[id*="itemuom"]').text().trim();
    var fieldid4 = $(this).children('[id*="uomqty"]').attr('id');
    var txtauomid =  $("#txt"+fieldid4+"").val();
    var txtauomqty =  $("#txt"+fieldid4+"").data("desc");

    var apartno =  $("#txt"+fieldid3+"").data("desc2");
    var cpartno =  $("#txt"+fieldid3+"").data("desc3");
    var opartno =  $("#txt"+fieldid3+"").data("desc4");

    var txtmuomqty =  $(this).children('[id*="uomqty"]').text().trim();
    var fieldid5 = $(this).children('[id*="irate"]').attr('id');
    var txtruom =  $("#txt"+fieldid5+"").val();
    var txtmqtyf = $("#txt"+fieldid5+"").data("desc");
    var fieldid6 = $(this).children('[id*="itax"]').attr('id');

    txtauomqty = (parseFloat(txtmuomqty)/parseFloat(txtmqtyf))*parseFloat(txtauomqty);

    var desc6         =  $("#txt"+fieldid+"").data("desc6");
    var AultUomQty    =  $("#txt"+fieldid+"").data("desc7");
    var PoPendingQty  =  $("#txt"+fieldid+"").data("desc8");

    var serialno  =  $("#txt"+fieldid+"").data("desc9");
    var barcode  =  $("#txt"+fieldid+"").data("desc10");
   
    if(intRegex.test(txtauomqty)){
      txtauomqty = (txtauomqty +'.000');
    }

    if(intRegex.test(txtmuomqty)){
      txtmuomqty = (txtmuomqty +'.000');
    }

   
   
    if($(this).find('[id*="Main_chkId"]').is(":checked") == true){

      $('#Main_example2').find('.Main_participantRow').each(function(){

        var itemid = $(this).find('[id*="Main_ITEMID_REF"]').val();
        var exist_val = itemid;

        if(txtval){
          if(desc6 == exist_val){
            $("#Main_ITEMIDpopup").hide();
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
                 $('#hdn_ItemID11').val('');

                 $('#hdn_ItemID15').val('');
                 $('#hdn_ItemID16').val('');

    

                 $('#hdn_ItemID8').val('');
                 $('#hdn_ItemID9').val('');
                 $('#hdn_ItemID10').val('');

                
                 txtval = '';
                 texdesc = '';
                 txtname = '';
                 txtspec = '';
                 txtmuom = '';
                 txtauom = '';
                 txtmuomid = '';
                 txtauomid = '';
                 txtauomqty='';
                 txtmuomqty='';
                 txtruom = '';

                 serialno = '';
                 barcode = '';
                 return false;
           }               
      }          
   });


   if($('#hdn_ItemID').val() == "" && txtval != ''){

     var txtid= $('#hdn_ItemID').val();
     var txt_id2= $('#hdn_ItemID2').val();
     var txt_id3= $('#hdn_ItemID3').val();
     var txt_id4= $('#hdn_ItemID4').val();
     var txt_id5= $('#hdn_ItemID5').val();
     var txt_id6= $('#hdn_ItemID6').val();
     var txt_id7= $('#hdn_ItemID7').val();
     var txt_id11= $('#hdn_ItemID11').val();

     var txt_id8= $('#hdn_ItemID8').val();
     var txt_id9= $('#hdn_ItemID9').val();
     var txt_id10= $('#hdn_ItemID10').val();

     var txt_id15= $('#hdn_ItemID15').val();
     var txt_id16= $('#hdn_ItemID16').val();


     var $tr = $('.Main_material').closest('table');
     var allTrs = $tr.find('.Main_participantRow').last();
     var lastTr = allTrs[allTrs.length-1];
     var $clone = $(lastTr).clone();

     $clone.find('td').each(function(){
      var el = $(this).find(':first-child');
      var id = el.attr('id') || null;
      if(id){
      var idLength = id.split('_').pop();
      var i = id.substr(id.length-idLength.length);
      var prefix = id.substr(0, (id.length-idLength.length));
      el.attr('id', prefix+(+i+1));
      }
      var name = el.attr('name') || null;
      if(name){
      var nameLength = name.split('_').pop();
      var i = name.substr(name.length-nameLength.length);
      var prefix1 = name.substr(0, (name.length-nameLength.length));
      el.attr('name', prefix1+(+i+1));
      }
      });

     $clone.find('.Main_remove').removeAttr('disabled'); 
     $clone.find('[id*="Main_popupITEMID"]').val(texdesc);
     $clone.find('[id*="Main_ITEMID_REF"]').val(txtval);
     $clone.find('[id*="Main_ItemName"]').val(txtname);

     $clone.find('[id*="Alpspartno"]').val(apartno);
     $clone.find('[id*="Custpartno"]').val(cpartno);
     $clone.find('[id*="OEMpartno"]').val(opartno);

     $clone.find('[id*="Main_Itemspec"]').val(txtspec);
     $clone.find('[id*="Main_popupMUOM"]').val(txtmuom);
     $clone.find('[id*="Main_MAIN_UOMID_REF"]').val(txtmuomid);
     $clone.find('[id*="Main_RECEIVED_QTY"]').val(txtmuomqty);

     $clone.find('[id*="Main_popupALTUOM"]').val(txtauom);
     $clone.find('[id*="Main_ALT_UOMID_REF"]').val(txtauomid);
     $clone.find('[id*="Main_RECEIVED_QTY_AU"]').val(AultUomQty);

     $clone.find('[id*="SERIAL_NO"]').val(serialno);
     $clone.find('[id*="BARCODE"]').val(barcode);



     


     $clone.find('[id*="Main_REMARKS"]').val('');
     
     $tr.closest('table').append($clone);   
     var rowCount = $('#Main_Row_Count1').val();
       rowCount = parseInt(rowCount)+1;
       $('#Main_Row_Count1').val(rowCount);
       
       $("#Main_ITEMIDpopup").hide();
     event.preventDefault();
   }
   else{


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

     var txt_id15= $('#hdn_ItemID15').val();
     var txt_id16= $('#hdn_ItemID16').val();

     $('#'+txtid).val(texdesc);
     $('#'+txt_id2).val(txtval);
     $('#'+txt_id3).val(txtname);
     $('#'+txt_id4).val(txtspec);
     $('#'+txt_id5).val(txtmuom);
     $('#'+txt_id6).val(txtmuomid);
     $('#'+txt_id7).val(txtmuomqty); 
     $('#'+txt_id8).val(txtauom);
     $('#'+txt_id9).val(txtauomid);
     $('#'+txt_id10).val(AultUomQty);

     $('#'+txtid).parent().parent().find('[id*="Alpspartno"]').val(apartno);
     $('#'+txtid).parent().parent().find('[id*="Custpartno"]').val(cpartno);
     $('#'+txtid).parent().parent().find('[id*="OEMpartno"]').val(opartno);

    if(serialno==1){
      $('#'+txt_id15).prop('checked',true);
      }else{
      $('#'+txt_id15).prop('checked',false);
      }

      if(barcode==1){
      $('#'+txt_id16).prop('checked',true);
      }else{
      $('#'+txt_id16).prop('checked',false);
      }



     $('#hdn_ItemID').val('');
     $('#hdn_ItemID2').val('');
     $('#hdn_ItemID3').val('');
     $('#hdn_ItemID4').val('');
     $('#hdn_ItemID5').val('');
     $('#hdn_ItemID6').val('');
     $('#hdn_ItemID7').val('');
     $('#hdn_ItemID11').val('');

     $('#hdn_ItemID15').val('');
     $('#hdn_ItemID16').val('');
     


     $('#hdn_ItemID8').val('');
     $('#hdn_ItemID9').val('');
     $('#hdn_ItemID10').val('');


   }

                 
   $("#Main_ITEMIDpopup").hide();
   event.preventDefault();
  }
  else if($(this).find('[id*="Main_chkId"]').is(":checked") == false)
  {
    var id = txtval;
    var r_count = $('#Main_Row_Count1').val();
    $('#Main_example2').find('.Main_participantRow').each(function()
    {
      var itemid = $(this).find('[id*="Main_ITEMID_REF"]').val();
      if(id == itemid)
      {
         var rowCount = $('#Main_Row_Count1').val();
         if (rowCount > 1) {
           $(this).closest('.Main_participantRow').remove(); 
           rowCount = parseInt(rowCount)-1;
         $('#Main_Row_Count1').val(rowCount);
         }
         else 
         {
           $(document).find('.Main_dmaterial').prop('disabled', true);  
           $("#Main_ITEMIDpopup").hide();
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
   $("#ItemGroupsearch").val(''); 
   $("#ItemCategorysearch").val(''); 
   $("#ItemStatussearch").val(''); 
   $('.Main_remove').removeAttr('disabled'); 
  
   event.preventDefault();
 });

 get_materital_item();

  $('.Main_js-selectall').prop("checked", false);   
  $("#Main_ITEMIDpopup").hide();

});



    $('[id*="Main_chkId"]').change(function(){
   
      var fieldid = $(this).parent().parent().attr('id');
      var txtval =   $("#txt"+fieldid+"").val();
      var texdesc =  $("#txt"+fieldid+"").data("desc");
      var fieldid2 = $(this).parent().parent().children('[id*="itemname"]').attr('id');
      var txtname =  $("#txt"+fieldid2+"").val();
      var txtspec =  $("#txt"+fieldid2+"").data("desc");
      var fieldid3 = $(this).parent().parent().children('[id*="itemuom"]').attr('id');
      var txtmuomid =  $("#txt"+fieldid3+"").val();
      var txtauom =  $("#txt"+fieldid3+"").data("desc");

      var apartno =  $("#txt"+fieldid3+"").data("desc2");
      var cpartno =  $("#txt"+fieldid3+"").data("desc3");
      var opartno =  $("#txt"+fieldid3+"").data("desc4");


      var txtmuom =  $(this).parent().parent().children('[id*="itemuom"]').text().trim();
      var fieldid4 = $(this).parent().parent().children('[id*="uomqty"]').attr('id');
      var txtauomid =  $("#txt"+fieldid4+"").val();
      var txtauomqty =  $("#txt"+fieldid4+"").data("desc");
      var txtmuomqty =  $(this).parent().parent().children('[id*="uomqty"]').text().trim();
      var fieldid5 = $(this).parent().parent().children('[id*="irate"]').attr('id');
      var txtruom =  $("#txt"+fieldid5+"").val();
      var txtmqtyf = $("#txt"+fieldid5+"").data("desc");
      var fieldid6 = $(this).parent().parent().children('[id*="itax"]').attr('id');

      txtauomqty = (parseFloat(txtmuomqty)/parseFloat(txtmqtyf))*parseFloat(txtauomqty);

      var desc6         =  $("#txt"+fieldid+"").data("desc6");
       var AultUomQty    =  $("#txt"+fieldid+"").data("desc7");
      var PoPendingQty  =  $("#txt"+fieldid+"").data("desc8");

      var serialno  =  $("#txt"+fieldid+"").data("desc9");
      var barcode  =  $("#txt"+fieldid+"").data("desc10");
      
      if(intRegex.test(txtauomqty)){
          txtauomqty = (txtauomqty +'.000');
      }

      if(intRegex.test(txtmuomqty)){
        txtmuomqty = (txtmuomqty +'.000');
      }




 
     if($(this).is(":checked") == true) {

      $('#Main_example2').find('.Main_participantRow').each(function(){
       
        var itemid = $(this).find('[id*="Main_ITEMID_REF"]').val();
            
         var exist_val=itemid;

         if(txtval){
              if(desc6 == exist_val){
                $("#Main_ITEMIDpopup").hide();
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
                    $('#hdn_ItemID11').val('');

          

                    $('#hdn_ItemID8').val('');
                    $('#hdn_ItemID9').val('');
                    $('#hdn_ItemID10').val('');
    
                   
                    txtval = '';
                    texdesc = '';
                    txtname = '';
                    txtspec = '';
                    txtmuom = '';
                    txtauom = '';
                    txtmuomid = '';
                    txtauomid = '';
                    txtauomqty='';
                    txtmuomqty='';
                    txtruom = '';
                    
                    serialno = '';
                    barcode = '';
                    return false;
              }               
         }          
      });


      if($('#hdn_ItemID').val() == "" && txtval != ''){
       

        var txtid= $('#hdn_ItemID').val();
        var txt_id2= $('#hdn_ItemID2').val();
        var txt_id3= $('#hdn_ItemID3').val();
        var txt_id4= $('#hdn_ItemID4').val();
        var txt_id5= $('#hdn_ItemID5').val();
        var txt_id6= $('#hdn_ItemID6').val();
        var txt_id7= $('#hdn_ItemID7').val();
        var txt_id11= $('#hdn_ItemID11').val();
        var txt_id8= $('#hdn_ItemID8').val();
        var txt_id9= $('#hdn_ItemID9').val();
        var txt_id10= $('#hdn_ItemID10').val();

        var txt_id15= $('#hdn_ItemID15').val();
        var txt_id16= $('#hdn_ItemID16').val();


        var $tr = $('.Main_material').closest('table');
        var allTrs = $tr.find('.Main_participantRow').last();
        var lastTr = allTrs[allTrs.length-1];
        var $clone = $(lastTr).clone();

        $clone.find('td').each(function(){
        var el = $(this).find(':first-child');
        var id = el.attr('id') || null;
        if(id){
          var idLength = id.split('_').pop();
          var i = id.substr(id.length-idLength.length);
          var prefix = id.substr(0, (id.length-idLength.length));
          el.attr('id', prefix+(+i+1));
        }
        var name = el.attr('name') || null;
        if(name){
          var nameLength = name.split('_').pop();
          var i = name.substr(name.length-nameLength.length);
          var prefix1 = name.substr(0, (name.length-nameLength.length));
          el.attr('name', prefix1+(+i+1));
        }
      });

        $clone.find('.Main_remove').removeAttr('disabled'); 
        $clone.find('[id*="Main_popupITEMID"]').val(texdesc);
        $clone.find('[id*="Main_ITEMID_REF"]').val(txtval);
        $clone.find('[id*="Main_ItemName"]').val(txtname);



        $clone.find('[id*="Alpspartno"]').val(apartno);
        $clone.find('[id*="Custpartno"]').val(cpartno);
        $clone.find('[id*="OEMpartno"]').val(opartno);




        $clone.find('[id*="Main_Itemspec"]').val(txtspec);
        $clone.find('[id*="Main_popupMUOM"]').val(txtmuom);
        $clone.find('[id*="Main_MAIN_UOMID_REF"]').val(txtmuomid);
        $clone.find('[id*="Main_RECEIVED_QTY"]').val(txtmuomqty); 

        $clone.find('[id*="Main_popupALTUOM"]').val(txtauom);
        $clone.find('[id*="Main_ALT_UOMID_REF"]').val(txtauomid);
        $clone.find('[id*="Main_RECEIVED_QTY_AU"]').val(AultUomQty);

        $clone.find('[id*="SERIAL_NO"]').val(serialno);
        $clone.find('[id*="BARCODE"]').val(barcode);
        
     
        
        $tr.closest('table').append($clone);   
        var rowCount = $('#Main_Row_Count1').val();
   
          rowCount = parseInt(rowCount)+1;
          $('#Main_Row_Count1').val(rowCount);
          
          $("#Main_ITEMIDpopup").hide();
        event.preventDefault();
      }
      else{


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

        var txt_id15= $('#hdn_ItemID15').val();
        var txt_id16= $('#hdn_ItemID16').val();
  

        $('#'+txtid).val(texdesc);
        $('#'+txt_id2).val(txtval);
        $('#'+txt_id3).val(txtname);
        $('#'+txt_id4).val(txtspec);
        $('#'+txt_id5).val(txtmuom);
        $('#'+txt_id6).val(txtmuomid);
        $('#'+txt_id7).val(txtmuomqty);
        
  
        $('#'+txt_id8).val(txtauom);
        $('#'+txt_id9).val(txtauomid);
        $('#'+txt_id10).val(AultUomQty);

        $('#'+txtid).parent().parent().find('[id*="Alpspartno"]').val(apartno);
        $('#'+txtid).parent().parent().find('[id*="Custpartno"]').val(cpartno);
        $('#'+txtid).parent().parent().find('[id*="OEMpartno"]').val(opartno);

    

        if(serialno==1){
        $('#'+txt_id15).prop('checked',true);
        }else{
        $('#'+txt_id15).prop('checked',false);
        }

        if(barcode==1){
        $('#'+txt_id16).prop('checked',true);
        }else{
        $('#'+txt_id16).prop('checked',false);
        }

    


        $('[id*="Main_RECEIVED_QTY_MU"]').val('');
        $('#hdn_ItemID').val('');
        $('#hdn_ItemID2').val('');
        $('#hdn_ItemID3').val('');
        $('#hdn_ItemID4').val('');
        $('#hdn_ItemID5').val('');
        $('#hdn_ItemID6').val('');
        $('#hdn_ItemID7').val('');
        $('#hdn_ItemID11').val('');    
        $('#hdn_ItemID8').val('');
        $('#hdn_ItemID9').val('');
        $('#hdn_ItemID10').val('');

        $('#hdn_ItemID15').val('');
        $('#hdn_ItemID16').val('');
  

      }

                    
      $("#Main_ITEMIDpopup").hide();
      event.preventDefault();
     }
     else 
     {
      var id = txtval;
       var r_count = $('#Main_Row_Count1').val();
       $('#Main_example2').find('.Main_participantRow').each(function()
       {
         var itemid = $(this).find('[id*="Main_ITEMID_REF"]').val();        
     
         if(id == itemid)           {
            var rowCount = $('#Main_Row_Count1').val();          

            if (rowCount > 1) {
              $(this).closest('.Main_participantRow').remove(); 
              rowCount = parseInt(rowCount)-1;
            $('#Main_Row_Count1').val(rowCount);
            }
            else 
            {
              $(document).find('.Main_dmaterial').prop('disabled', true);  
              $("#Main_ITEMIDpopup").hide();
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
      $("#ItemGroupsearch").val(''); 
      $("#ItemCategorysearch").val(''); 
      $("#ItemStatussearch").val(''); 
      $('.Main_remove').removeAttr('disabled'); 
      get_materital_item();
      event.preventDefault();
    });
  }



  


/*================================== ADD/REMOVE FUNCTION for FIRST MATERIAL TAB ==================================*/

        $("#Main_Material").on('click', '.Main_add', function() {
        var $tr = $(this).closest('table');
        var allTrs = $tr.find('.Main_participantRow').last();
        var lastTr = allTrs[allTrs.length-1];
        var $clone = $(lastTr).clone();

        $clone.find('td').each(function(){
        var el = $(this).find(':first-child');
        var id = el.attr('id') || null;
        if(id){
        var idLength = id.split('_').pop();
        var i = id.substr(id.length-idLength.length);
        var prefix = id.substr(0, (id.length-idLength.length));
        el.attr('id', prefix+(+i+1));
        }
        var name = el.attr('name') || null;
        if(name){
        var nameLength = name.split('_').pop();
        var i = name.substr(name.length-nameLength.length);
        var prefix1 = name.substr(0, (name.length-nameLength.length));
        el.attr('name', prefix1+(+i+1));
        }
        });

        $clone.find('input:text').val('');
        $clone.find('input:hidden').val('');

        $clone.find('input:checkbox').prop('checked',false);

        $tr.closest('table').append($clone);         
        var rowCount1 = $('#Main_Row_Count1').val();
        rowCount1 = parseInt(rowCount1)+1;
        $('#Main_Row_Count1').val(rowCount1);
        $clone.find('.Main_remove').removeAttr('disabled'); 

        event.preventDefault();
        });

        $("#Main_Material").on('click', '.Main_remove', function() {
        var rowCount = $(this).closest('table').find('.Main_participantRow').length;
        if (rowCount > 1) {
        $(this).closest('.Main_participantRow').remove();  
                    get_materital_item();
          
        } 
        if (rowCount <= 1) { 
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
        });




        function getArraySum(a){
        var total=0;
        for(var i in a) { 
          total += a[i];
        }
        return total;
        }

        function isNumberDecimalKey(evt){
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
        return false;

        return true;
        }

        function clearGrid(){
        $('#Main_example2').find('.Main_participantRow').each(function(){
        var rowCount = $(this).closest('table').find('.Main_participantRow').length;
        $('#Main_Row_Count1').val(rowCount);
        $(this).closest('.Main_participantRow').find('input:text').val('');
        $(this).closest('.Main_participantRow').find('input:hidden').val('');
        if (rowCount > 1) {
        $(this).closest('.Main_participantRow').remove();  
        } 
        });
        }


/*================================== MATERIAL ITEM FUNCTION ==================================*/

          function get_materital_item(){
          var  item_array         = [];
          var checkCompany        = "{{$checkCompany}}";
          var action_type="EDIT"; 
          var recordid="{{$objResponse->BRCOID}}";
          $('#Main_Material').find('.Main_participantRow').each(function(){
            var Main_ITEMID_REF     = $(this).find('[id*="Main_ITEMID_REF"]').val();
            var Main_MAIN_UOMID_REF = $(this).find('[id*="Main_MAIN_UOMID_REF"]').val();
            var Main_RECEIVED_QTY   = $(this).find('[id*="Main_RECEIVED_QTY"]').val();
            var Alt_RECEIVED_QTY    = $(this).find('[id*="Alt_RECEIVED_QTY"]').val();

            


            if(Main_ITEMID_REF!=''){
          item_array.push(Main_ITEMID_REF+'_'+Main_MAIN_UOMID_REF+'_'+Main_RECEIVED_QTY+"_"+Alt_RECEIVED_QTY);
          }

          });

          //alert(item_array); 

          $("#material_item").html('loading..');
          $.ajaxSetup({
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          });

          $.ajax({
              url:'{{route("transaction",[$FormId,"get_materital_item"])}}',
              type:'POST',
              data:{
                item_array:item_array,checkCompany:checkCompany,action_type:action_type,recordid:recordid
                },
              success:function(data) {
                $("#material_item").html(data);
                              
              },
              error:function(data){
                console.log("Error: Something went wrong.");
                $("#material_item").html('');                        
              },
          });



          }



//UOM Type Dropdown
let uomtid = "#UOMTable2";
let uomtid2 = "#UOMTable";
let uomheaders = document.querySelectorAll(uomtid2 + " th");

// Sort the table element when clicking on the table headers
uomheaders.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(uomtid, ".clsuomid", "td:nth-child(" + (i + 1) + ")");
  });
});

function UOMCodeFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("UOMcodesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("UOMTable2");
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

function UOMNameFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("UOMnamesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("UOMTable2");
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

function getUOM(ROW_ID,ITEMID_REF){ 
   var index_id=ROW_ID.split('_').pop();
   var UOM_VAL=$('#PACKUOMID_REF_'+index_id).val();
   showSelectedCheck(UOM_VAL,"uomcheck");
   $("#UOMpopup").show();
   var id = ROW_ID;
   var id2 ="PACKUOMID_REF_"+index_id;        
  
   $('#hdn_UOM').val(id);
   $('#hdn_UOM2').val(id2);
}

$("#UOMclosePopup").click(function(event){
  $("#UOMpopup").hide();
});

$(".clsuomid").click(function(){
  var fieldid = $(this).attr('id');
  var txtval =    $("#txt"+fieldid+"").val();
  var texdesc =   $("#txt"+fieldid+"").data("desc");
  var txtid = $('#hdn_UOM').val();
  var txt_id2 = $('#hdn_UOM2').val();
  $('#'+txtid).val(texdesc);
  $('#'+txt_id2).val(txtval);        
  $("#UOMpopup").hide();        
  $("#UOMcodesearch").val(''); 
  $("#UOMnamesearch").val('');        
  event.preventDefault();
});      

//UOM Type Dropdown Ends



function toFindDuplicates(SlNo) {
let resultToReturn = false;
// call some function with callback function as argument
resultToReturn = SlNo.some((element, index) => {
  return SlNo.indexOf(element) !== index
});
if (resultToReturn) {
  return true;
    
  }
  else {
 return false;
      }
  }


//====================================Serial No Section Starts here =========================================================
$('#Main_Material').on('click','[id*="txtSR_popup"]',function(event){
  var checkCompany  = "{{$checkCompany}}";
  if(checkCompany==''){
    $(".ROWQTY").show(); 
  }else{
    $(".ROWQTY").hide(); 
  }

$('#hdn_STRid').val($(this).attr('id'));
$('#hdn_STRid2').val($(this).parent().parent().find('[id*="SERIALNO_REF"]').attr('id'));
$('#hdn_STRid3').val($(this).parent().parent().find('[id*="REQ_UOMID_REF"]').attr('id'));
var fieldid = $(this).parent().parent().find('[id*="SERIALNO_REF"]').attr('id');
var ROWID=fieldid.split("_"); 
var ITEMID_REF=$("#REQ_ITEMID_REF_"+ROWID[2]).val();
if(ITEMID_REF==='')
{
  $("#YesBtn").hide();
  $("#NoBtn").hide();
  $("#OkBtn").hide();
  $("#OkBtn1").show();
  $("#AlertMessage").text('Please Select Item Code First.');
  $("#alert").modal('show');
  $("#OkBtn1").focus();
  highlighFocusBtn('activeOk1');
  return false;
}
var click_button="clssSTRid";


  $("#STRpopup").show();
  $("#tbody_STR").html('loading...');

  $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  })

  $.ajax({
      url:'{{route("transaction",[$FormId,"get_STR"])}}',
      type:'POST',
      data:{'fieldid':fieldid,'class_name':click_button,ITEMID_REF:ITEMID_REF,checkCompany:checkCompany},
      success:function(data) {
        $("#tbody_STR").html(data);
        BindSTR();
        showSelectedCheck($("#"+fieldid).val(),"SELECT_"+fieldid);
      },
      error:function(data){
        console.log("Error: Something went wrong.");
        $("#tbody_STR").html('');
      },
  });



});

$("#STR_closePopup").click(function(event){
  $("#STRpopup").hide();
});

function BindSTR(){

$(".clssSTRid").click(function(){
  var fieldid          = $(this).attr('id');
  var txtval           = $("#txt"+fieldid+"").val();
  var texdesc          = $("#txt"+fieldid+"").data("desc");
  var texdesc1         = $("#txt"+fieldid+"").data("desc1");
  var qty              = $("#txt"+fieldid+"").data("desc2");
  var pendingqty       = $("#txt"+fieldid+"").data("desc3");


  var txtid     = $('#hdn_STRid').val();
  var txt_id2   = $('#hdn_STRid2').val();
  var txt_id3   = $('#hdn_STRid3').val();


  var rowid    = txtid.split('_').pop(0);
  var current_item  = $("#REQ_ITEMID_REF_"+rowid).val();  

  var CheckExist_str  = [];
  var CheckExist_item = [];




  $('#example8').find('.participantRow8').each(function(){

    if($(this).find('[id*="txtSTR_popup"]').val() != ''){

      var str_no  = $(this).find('[id*="SERIALNO_REF"]').val();
      var itemid  = $(this).find('[id*="REQ_ITEMID_REF"]').val();

        if(str_no!=''){
          CheckExist_str.push(str_no);
        }
        if(itemid!=''){
          CheckExist_item.push(itemid);
        }

    }
  });

  if($.inArray(txtval, CheckExist_str) !== -1 && $.inArray(current_item, CheckExist_item) !== -1 ){

    $('#'+txtid).val('');
    $('#'+txt_id2).val('');
    $('#'+txt_id3).val('');
    $('#REQ_QTY_'+rowid).val('');
    $('#PENDING_QTY_'+rowid).val('');

    $("#FocusId").val("txtSR_popup_"+rowid);
    $("#alert").modal('show');
    $("#AlertMessage").text('Serial No already Exist.');
    $("#YesBtn").hide(); 
    $("#NoBtn").hide();  
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk');
    $("#STRpopup").hide();
    GetTotalQty(current_item); 
    return false;
  }
  else{
    $('#'+txtid).val(texdesc);
    $('#'+txt_id2).val(txtval);
    $('#REQ_QTY_'+rowid).val(qty);
    $('#PENDING_QTY_'+rowid).val(pendingqty);
    GetTotalQty(current_item); 

  
    
  }

  var CheckExist_str  = [];
  var CheckExist_item = [];
  var CheckExist_uom  = [];



  $("#STRpopup").hide();
  $("#STRcodesearch").val(''); 
  $("#STRnamesearch").val(''); 
  event.preventDefault();

});
}

let STRNOTable2 = "#STRNOTable2";
let STRNOTable = "#STRNOTable";
let QCPheaders1 = document.querySelectorAll(STRNOTable + " th");

QCPheaders1.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(STRNOTable2, ".clssSTRid", "td:nth-child(" + (i + 1) + ")");
  });
});

function STRCodeFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("STRcodesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("STRNOTable2");
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

function STRNameFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("STRnamesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("STRNOTable2");
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

function STRDTFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("STRDTsearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("STRNOTable2");
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

//====================================Serial No Section Ends here =========================================================

  
function showSelectedCheck(hidden_value,selectAll){
var divid ="";
if(hidden_value !=""){
  var all_location_id = document.querySelectorAll('input[name="'+selectAll+'[]"]');
  //console.log(all_location_id); 
 
  for(var x = 0, l = all_location_id.length; x < l;  x++){   
    var checkid=all_location_id[x].id;
    var checkval=all_location_id[x].value;
  
    if(hidden_value == checkval){
      divid = checkid;
    }
    $("#"+checkid).prop('checked', false);   
  }
}

if(divid !=""){
  $("#"+divid).prop('checked', true);

}

}

function GetTotalQty(ITEMID){ 
  var totalvalue = 0.00;
  var tvalue = 0.00;
  var rowid_ref   = "";
  $('#Main_example2').find('.Main_participantRow').each(function(){
        if($.trim($(this).find("[id*=Main_ITEMID_REF]").val()) == ITEMID){
          rowid_ref = $(this).find("[id*=Main_ITEMID_REF]").attr('id');  
          }
  });  
  var rowid_ref=rowid_ref.split("_").pop(0);
    $('#material_item').find('.participantRow8').each(function(){
            if($(this).find('[id*="REQ_QTY"]').val() != ''){
                  if($.trim($(this).find("[id*=REQ_ITEMID_REF]").val()) === ITEMID){
                  tvalue = $(this).find('[id*="REQ_QTY"]').val();
                  totalvalue = parseFloat(totalvalue) + parseFloat(tvalue);
                  totalvalue = parseFloat(totalvalue).toFixed(2);
                  }
                 
             }
      });   

      $('#Main_RECEIVED_QTY_'+rowid_ref).val(totalvalue);
      
  }

function array_sum(someArray){
  var total = 0;
    for (var i = 0; i < someArray.length; i++) {
        total += someArray[i] << 0;
    }
    return total; 
    }



    /*================================== ADD/REMOVE FUNCTION for SECOND MATERIAL TAB ==================================*/

$("#material_item").on('click', '.Sub_add', function() {
    var $tr    = $(this).closest('.participantRow8');
    var $clone = $tr.clone();
    // $clone.find(':text').val('');
    $clone.find('[id*="REQ_QTY_"]').val('');
    $clone.find('[id*="PENDING_QTY"]').val('');
    $clone.find('[id*="txtSR_popup"]').val('');
    $clone.find('[id*="SERIALNO_REF"]').val('');
    $tr.after($clone);
    $clone.find('td').each(function(){
        var el = $(this).find(':first-child');
        var id = el.attr('id') || null;     
        if(id){
          var trcount = $('#example8 >tbody >tr').length-1;
          var idLength = id.split('_').pop();
          var i = id.substr(id.length-idLength.length);
          var prefix = id.substr(0, (id.length-idLength.length));
          el.attr('id', prefix+(+trcount+1));  
        }
        var name = el.attr('name') || null;      
        if(name){
          var nameLength = name.split('_').pop();
          var trcount =  $('#example8 >tbody >tr').length-1;
          var i = name.substr(name.length-nameLength.length);
          var prefix1 = name.substr(0, (name.length-nameLength.length));
          el.attr('name', prefix1+(+trcount+1));
        }
      });

    var rowCount1 = $('#Row_Count5').val();
    rowCount1 = parseInt(rowCount1)+1;
    $('#Row_Count5').val(rowCount1);
    $clone.find('.Sub_remove').removeAttr('disabled'); 
});

      $("#material_item").on('click', '.Sub_remove', function() {
      var rowCount = $(this).closest('table').find('.participantRow8').length;
      if (rowCount > 1) {
      $(this).closest('.participantRow8').remove();  
                  //get_materital_item();  
      } 
      if (rowCount <= 1) { 
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
      });



</script>
@endpush
