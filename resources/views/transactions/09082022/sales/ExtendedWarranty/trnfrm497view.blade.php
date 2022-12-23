@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
	<div class="row">
		<div class="col-lg-2"><a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Extended Warranty</a></div>

		<div class="col-lg-10 topnav-pd">
			<button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
			<button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
			<button class="btn topnavbt" id="btnSave" disabled="disabled"><i class="fa fa-save"></i> Save</button>
			<button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
			<button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
			<button class="btn topnavbt" id="btnUndo"  disabled="disabled"><i class="fa fa-undo"></i> Undo</button>
			<button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
			<button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-lock"></i> Approved</button>
			<button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
			<button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
		</div>
	</div>
</div>
 
<form id="frm_mst_edit" onsubmit="return validateForm(actionType)" method="POST"  class="needs-validation"> 
  @CSRF
    {{isset($objResponse->EWID) ? method_field('PUT') : '' }}
    <div class="container-fluid filter">
      <div class="inner-form">
        <div class="row">
          <div class="col-lg-2 pl"><p>Doc No*</p></div>
          <div class="col-lg-2 pl">
            <input {{$ActionStatus}} type="text" name="DOC_NO" id="DOC_NO" value="{{isset($objResponse->EW_NO) && $objResponse->EW_NO !=''?$objResponse->EW_NO:''}}" class="form-control mandatory"  autocomplete="off" readonly >
           {{-- <span class="text-danger" id="ERROR_DOC_NO"></span> --}}
          </div>
                
          <div class="col-lg-2 pl"><p>DOC Date</p></div>
          <div class="col-lg-2 pl">
            <input {{$ActionStatus}} type="date" name="DOC_DT" id="DOC_DT" value="{{isset($objResponse->DOC_DT) && $objResponse->DOC_DT !=''?$objResponse->DOC_DT:''}}" class="form-control mandatory" >
            <span class="text-danger" id="ERROR_DOC_DT"></span>
          </div> 
          
          <div class="col-lg-2 pl"><p>Customer*</p></div>
          <div class="col-lg-2 pl">
            <input {{$ActionStatus}} type="text" name="cust_popup" id="txtcust_popup" value="{{isset($objResponse->CCODE) && $objResponse->CCODE !=''?$objResponse->CCODE:''}}{{isset($objResponse->NAME) && $objResponse->NAME !=''?'-'.$objResponse->NAME:''}}" class="form-control mandatory"  autocomplete="off" readonly/>
            <input type="hidden" name="CUSTID_REF" id="CUSTID_REF" value="{{isset($objResponse->CUSTOMER_REF) && $objResponse->CUSTOMER_REF !=''?$objResponse->CUSTOMER_REF:''}}" class="form-control" autocomplete="off" />           
          </div>
        </div>    
      </div>

	  <div class="container-fluid">
      <div class="row">
        <ul class="nav nav-tabs">
          <li class="active"><a data-toggle="tab" href="#Material">Material</a></li>
        </ul>
			
			  <div class="tab-content">
				  <div id="Material" class="tab-pane fade in active">
					  <div class="table-responsive table-wrapper-scroll-y" style="margin-top:10px; height: 450px;" >
						  <table id="example2" class="display nowrap table table-striped table-bordered itemlist w-200" width="100%" style="height:auto !important;">
							  <thead id="thead1"  style="position: sticky;top: 0">
								  <tr>
                    <th>Invoice No</th>
                    <th>Inv.Dated</th>
                    <th>Item Code </th>
                    <th>Item Name</th>
                    <th>Extended Warranty/ In Months</th>
                    <th>From Date</th>
                    <th>To Date</th>
                    <th>Warranty Amount</th>
                    <th>Tax (%)</th>
                    <th>Tax</th>
                    <th>Total</th>
                    <th>Action</th>
								  </tr>
							  </thead>
							  <tbody id="tbodyid">
                  @if(!empty($MAT))
                  @foreach($MAT as $key => $row)
                  <?php 
                  $TOTALTAX=@number_format(isset($row->EXTW_TAX) && $row->EXTW_TAX !=''?$row->EXTW_TAX:'');
                    $EXTWAAMOUNT = (isset($row->EXTWA_AMOUNT) && $row->EXTWA_AMOUNT !=''?$row->EXTWA_AMOUNT:'');                    
                    $TAXAMT =  (((($EXTWAAMOUNT)*($TOTALTAX)/100)) && ((($EXTWAAMOUNT)*($TOTALTAX))/100) !=''?((($EXTWAAMOUNT)*($TOTALTAX))/100):'0.00').'.00';
                    ?>
								  <tr  class="participantRow">
                    <td><input {{$ActionStatus}} type="text" name="popupInvNo[]" id="popupInvNo_{{$key}}" value="{{isset($row->SINO) && $row->SINO !=''?$row->SINO:''}}" class="form-control"  autocomplete="off"  readonly style="width: 120px"/></td>
                    <td hidden><input type="hidden" name="INVNOID_REF[]" id="INVNOID_REF_{{$key}}" value="{{isset($row->EWINVNOID_REF) && $row->EWINVNOID_REF !=''?$row->EWINVNOID_REF:''}}" class="form-control" autocomplete="off" /></td>
                    
                    <td><input {{$ActionStatus}} type="date" name="INVDATE[]" id="INVDATE_{{$key}}" value="{{isset($row->SIDT) && $row->SIDT !=''?$row->SIDT:''}}" class="form-control"  autocomplete="off"  readonly style="width: 120px"/></td>
                    
                    <td hidden><input {{$ActionStatus}} type="text" name="INVIDNO[]" id="INVIDNO_{{$key}}" value="{{isset($row->SIID_REF) && $row->SIID_REF !=''?$row->SIID_REF:''}}" class="form-control" autocomplete="off" /></td>
                    <td hidden><input {{$ActionStatus}} type="text" name="SEID_REF[]" id="SEID_REF_{{$key}}" value="{{isset($row->SEID_REF) && $row->SEID_REF !=''?$row->SEID_REF:''}}" class="form-control" autocomplete="off" /></td>
                    
                    <td><input {{$ActionStatus}} type="text" name="popupITEMID[]" id="popupITEMID_{{$key}}" value="{{isset($row->ICODE) && $row->ICODE !=''?$row->ICODE:''}}" class="form-control"  autocomplete="off"  readonly style="width: 120px"/></td>
                    <td hidden><input type="text" name="ITEMID_REF[]" id="ITEMID_REF_{{$key}}" value="{{isset($row->ITEMID) && $row->ITEMID !=''?$row->ITEMID:''}}" class="form-control" autocomplete="off" /></td>
                    <td><input {{$ActionStatus}} type="text" name="ItemName[]" id="ItemName_{{$key}}" value="{{isset($row->NAME) && $row->NAME !=''?$row->NAME:''}}" class="form-control"  autocomplete="off"  readonly style="width: 150px"/></td>
                    
                    <td><input {{$ActionStatus}} type="text" name="EXTENDEDWNTMONTH[]" id="EXTENDEDWNTMONTH_{{$key}}" value="{{isset($row->EXTWA_MONTH) && $row->EXTWA_MONTH !=''?$row->EXTWA_MONTH:''}}" onkeyup="getExtnFromDate(this.id,this.value)" class="form-control"  onkeypress="return onlyNumberKey(event)" autocomplete="off" /></td>
                    
                    <td hidden><input type="hidden" name="popupMUOM[]" id="popupMUOM_{{$key}}" class="form-control"  autocomplete="off"  readonly/></td>
                    <td hidden><input {{$ActionStatus}} type="text" name="MAIN_UOMID_REF[]" id="MAIN_UOMID_REF_{{$key}}" class="form-control"  autocomplete="off" /></td>
                    
                    <td><input {{$ActionStatus}} type="date"   name="STARTFROM[]" id="STARTFROM_{{$key}}" value="{{isset($row->EW_STARTFROM) && $row->EW_STARTFROM !=''?$row->EW_STARTFROM:''}}" class="form-control" autocomplete="off"   /></td>
                    <td><input {{$ActionStatus}} type="date"   name="STARTTO[]" id="STARTTO_{{$key}}" value="{{isset($row->EW_STARTTO) && $row->EW_STARTTO !=''?$row->EW_STARTTO:''}}" class="form-control" autocomplete="off" /></td>
                    <td><input {{$ActionStatus}} type="text"   name="WARRANTYAMT[]" id="WARRANTYAMT_{{$key}}" value="{{isset($row->EXTWA_AMOUNT) && $row->EXTWA_AMOUNT !=''?$row->EXTWA_AMOUNT:''}}" class="form-control" maxlength="200" onkeyup="getWatnTaxDetails(this.id,this.value)"  onkeypress="return onlyNumberKey(event)" autocomplete="off" /></td>
                    
                    <td><input {{$ActionStatus}} type="text"   name="TOTALTAX[]" id="TOTALTAX_{{$key}}" value="{{isset($TOTALTAX) && $TOTALTAX !=''?$TOTALTAX:''}}" class="form-control" onkeyup="getWatnTaxDetails(this.id,this.value)" autocomplete="off" readonly style="width: 120px"/></td>
                    <td><input {{$ActionStatus}} type="text"   name="TAXAMT[]" id="TAXAMT_{{$key}}" value="{{isset($TAXAMT) && $TAXAMT !=''?$TAXAMT:''}}" class="form-control" autocomplete="off"  style="width: 120px" readonly/></td>
                    <td><input {{$ActionStatus}} type="text"   name="TOTALAMT[]" id="TOTALAMT_{{$key}}" value="{{isset($row->EXTOTAL_AMOUNT) && $row->EXTOTAL_AMOUNT !=''?$row->EXTOTAL_AMOUNT:''}}" class="form-control" maxlength="200"  autocomplete="off" readonly style="width: 100px" /></td>
                    
                    <td align="center" >
                      <button {{$ActionStatus}} class="btn add material" title="add" data-toggle="tooltip" type="button" ><i class="fa fa-plus"></i></button>
                      <button {{$ActionStatus}} class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button>
                    </td>
								  </tr>
                  @endforeach
                  @endif
							  </tbody>
					    </table>
              <div id="material_item">                
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
<!-- Customer Dropdown -->
<div id="cust_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='cust_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Customer Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="CustTable" class="display nowrap table  table-striped table-bordered" >
    <thead>
    <tr>
      <th class="ROW1">Select</th> 
      <th class="ROW2">No</th>
      <th class="ROW3">Date</th>
    </tr>
    </thead>
    <tbody>
    <tr>
      <th class="ROW1"><span class="check_th">&#10004;</span></th>
      <td class="ROW2"><input type="text" id="search_1" class="form-control" autocomplete="off" onkeyup="searchCode(this.id,'CustTable2','1')"></td>
      <td class="ROW3"><input type="text" id="search_2" class="form-control" autocomplete="off" onkeyup="searchCode(this.id,'CustTable2','2')"></td>
    </tr>
    </tbody>
    </table>
      <table id="CustTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2">          
        </thead>
        <tbody>
        @foreach ($objCUST as $index=>$cust_Row)
        <tr> 
        <td class="ROW1"> <input type="checkbox" name="SELECT_CUSTID_REF[]" id="custcode_{{ $index }}" class="clscustcode" value="{{ $cust_Row->SLID_REF }}" ></td>
          <td class="ROW2">{{ $cust_Row->CCODE }}
          <input type="hidden" id="txtcustcode_{{ $index }}" data-desc="{{ $cust_Row->CCODE }}-{{ $cust_Row->NAME }}"  value="{{ $cust_Row->SLID_REF }}" data-title="{{ $cust_Row->NAME }}"/>
          </td>
          <td class="ROW3">{{ $cust_Row->NAME }}</td>
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

<!-- inv Dropdown-->
<div id="INVpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content" >
      <div class="modal-header"><button type="button" class="close" data-dismiss="modal" id='INVID_closePopup' >&times;</button></div>
      <div class="modal-body">
	      <div class="tablename"><p>Invoice No Details</p></div>
	      <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
          <table id="INVTable" class="display nowrap table  table-striped table-bordered">
            <thead>
              <tr id="none-select" class="searchalldata" hidden >
                <td> 
                  <input type="text" name="fildinv_id1" id="hdn_INVID1"/>
                  <input type="text" name="fildinv_id2" id="hdn_INVID2"/>
                  <input type="text" name="fildinv_id3" id="hdn_INVID3"/>
                  <input type="text" name="fildinv_id4" id="hdn_INVID4"/>
                  <input type="text" name="fildinv_id6" id="hdn_INVID6"/>
                  <input type="text" name="fildinv_id7" id="hdn_INVID7"/>
                  <input type="text" name="fildinv_id9" id="hdn_INVID9"/>
                  <input type="text" name="fildinv_id10" id="hdn_INVID10"/>
                  <input type="text" name="fildinv_id18" id="hdn_INVID18"/>
                  <input type="text" name="fildinv_id19" id="hdn_INVID19"/>
                  <input type="text" name="fildinv_id20" id="hdn_INVID20"/>
                  <input type="text" name="hdn_INVID21" id="hdn_INVID21" value="0"/>
                  <input type="text" name="fildinv_id22" id="hdn_INVID22"/>
                  <input type="text" name="fildinv_id23" id="hdn_INVID23"/>
                  <input type="text" name="fildinv_id24" id="hdn_INVID24"/>
                  <input type="text" name="fildinv_id25" id="hdn_INVID25"/>
                </td>
              </tr>
              <tr>
                <th class="ROW1" id="all-check">Select</th> 
                <th class="ROW2">Invoice No</th>
                <th class="ROW3">Invoice Date</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <th class="ROW1"><span class="check_th">&#10004;</span></th>
                <td class="ROW2"><input type="text" id="invcodesearch" class="form-control" onkeyup="INVCodeFunction(event)"></td>
                <td class="ROW3"><input type="text" id="invnamesearch" class="form-control" onkeyup="INVNameFunction(event)"></td>
              </tr>
            </tbody>
          </table>
          <table id="INVTable2" class="display nowrap table  table-striped table-bordered">
            <thead id="thead2"></thead>
            <tbody id="tbody_INV"></tbody>
          </table>
        </div>
		    <div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<!-- item Dropdown-->
{{-- ITEM  --}}
<div id="ITEMIDpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:90%">
    <div class="modal-content" >
      <div class="modal-header"><button type="button" class="close" data-dismiss="modal" id='ITEMID_closePopup' >&times;</button></div>
      <div class="modal-body">
	      <div class="tablename"><p>Item Details</p></div>
	      <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
          <table id="ItemIDTable" class="display nowrap table  table-striped table-bordered" style="width:100%" >
            <thead>
              <tr id="none-select" class="searchalldata" hidden >
                <td> 
                  <input type="text" name="fieldid1" id="hdn_ItemID1"/>
                  <input type="text" name="fieldid2" id="hdn_ItemID2"/>
                  <input type="text" name="fieldid3" id="hdn_ItemID3"/>
                  <input type="text" name="fieldid6" id="hdn_ItemID6"/>
                  <input type="text" name="fieldid7" id="hdn_ItemID7"/>
                  <input type="text" name="fieldid9" id="hdn_ItemID9"/>
                  <input type="text" name="fieldid10" id="hdn_ItemID10"/>
                  <input type="text" name="fieldid18" id="hdn_ItemID18"/>
                  <input type="text" name="fieldid19" id="hdn_ItemID19"/>
                  <input type="text" name="fieldid20" id="hdn_ItemID20"/>
                  <input type="text" name="hdn_ItemID21" id="hdn_ItemID21" value="0"/>
                  <input type="text" name="fieldid22" id="hdn_ItemID22"/>
                  <input type="text" name="fieldid23" id="hdn_ItemID23"/>
                  <input type="text" name="fieldid24" id="hdn_ItemID24"/>
                  <input type="text" name="fieldid25" id="hdn_ItemID25"/>
                  <input type="text" name="fieldid26" id="hdn_ItemID26"/>
                </td>
              </tr>

              <tr>
                <th style="width:8%;" id="all-check">Select</th>
                <th style="width:10%;">Item Code</th>
                <th style="width:10%;">Name</th>
                <th style="width:8%;">Main UOM</th>
                <th style="width:8%;">Item Group</th>
                <th style="width:8%;">Item Category</th>
                <th style="width:8%;">Business Unit</th>
                <th style="width:8%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</th>
                <th style="width:8%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</th>
                <th style="width:8%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</th>
                <th style="width:8%;">Status</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td style="width:8%;text-align:center;"><input type="checkbox" class="js-selectall" data-target=".js-selectall1" /></td>
                <td style="width:10%;"><input type="text" id="Itemcodesearch" class="form-control" onkeyup="ItemCodeFunction(event)"></td>
                <td style="width:10%;"><input type="text" id="Itemnamesearch" class="form-control" onkeyup="ItemNameFunction(event)"></td>
                <td style="width:8%;"><input type="text" id="ItemUOMsearch" class="form-control" onkeyup="ItemUOMFunction(event)"></td>
                <td style="width:8%;"><input type="text" id="ItemGroupsearch" class="form-control" onkeyup="ItemGroupFunction(event)"></td>
                <td style="width:8%;"><input type="text" id="ItemCategorysearch" class="form-control" onkeyup="ItemCategoryFunction(event)"></td>
                
                <td style="width:8%;"><input type="text" id="ItemBUsearch" class="form-control" onkeyup="ItemBUFunction(event)" readonly></td>
                <td style="width:8%;" {{$AlpsStatus['hidden']}}><input type="text" id="ItemAPNsearch" class="form-control" onkeyup="ItemAPNFunction(event)"></td>
                <td style="width:8%;" {{$AlpsStatus['hidden']}}><input type="text" id="ItemCPNsearch" class="form-control" onkeyup="ItemCPNFunction(event)"></td>
                <td style="width:8%;" {{$AlpsStatus['hidden']}}><input type="text" id="ItemOEMPNsearch" class="form-control" onkeyup="ItemOEMPNFunction(event)"></td>

                
                <td style="width:8%;"><input  type="text" id="ItemStatussearch" class="form-control" onkeyup="ItemStatusFunction(event)" readonly></td>
              </tr>
            </tbody>
          </table>
          <table id="ItemIDTable2" class="display nowrap table  table-striped table-bordered" style="width:100%" >
            <thead id="thead2"></thead>
            <tbody id="tbody_ItemID"></tbody>
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

table {font-size: 13px;}
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
  window.location.href = "{{route('transaction',[$FormId,'add'])}}";
}

$("#btnSave" ).click(function() {
    var formReqData = $("#frm_mst_edit");
    if(formReqData.valid()){
      validateForm();
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
  if($.trim($("#FocusId").val())!=""){
    $("#"+$("#FocusId").val()).focus();
  }    
  $("#closePopup").click();
}

function highlighFocusBtn(pclass){
  $(".activeYes").hide();
  $(".activeNo").hide();
  $("."+pclass+"").show();
}

function submitData(type){
  var formReqData = $("#frm_mst_edit");
  if(formReqData.valid()){
    $("#alert").modal('show');
    $("#AlertMessage").text('Do you want to save to record.');
    $("#YesBtn").data("funcname",type);
    $("#YesBtn").focus();
    highlighFocusBtn('activeYes');
  }
}

window.fnSaveData = function (){
  submitForm('update');
};
window.fnApproveData = function (){
  submitForm('approve');
}

/*================================== Save FUNCTION =================================*/
           
function submitForm(requestType){
var getDataForm = $("#frm_mst_edit");
var formData = getDataForm.serialize() + "&requestType=" + requestType ;
//var formData = getDataForm.append(requestType);
$.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$.ajax({
    url:'{{route("transactionmodify",[$FormId,"update"])}}',
    type:'POST',
    data:formData,
    success:function(data) {
      if(data.success) {                   
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").show();
      $("#AlertMessage").text(data.msg);
      $(".text-danger").hide();
      $("#alert").modal('show');
      $("#OkBtn").focus();
      }     
      if(data.errors) {
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text(data.msg);
        $("#alert").modal('show');
        $("#OkBtn1").focus();
      }
    },
    error:function(data){
    console.log("Error: Something went wrong.");
    },
  });

}

/*================================== VALIDATE FUNCTION =================================*/

function alertMsg(id,msg){		
      $("#FocusId").val(id);
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text(msg);
      $("#alert").modal('show')
      $("#OkBtn1").focus();
      return false;
		}

function validateForm(actionType){  
  $("#FocusId").val('');
  var DOC_NO      = $.trim($("#DOC_NO").val());
  var DOC_DT      = $.trim($("#DOC_DT").val());
  var CUSTID_REF   = $.trim($("#CUSTID_REF").val());
  
      if(DOC_NO ===""){
        alertMsg('DOC_NO','Please enter Doc No.');
      }  
      else if(DOC_DT ===""){
        alertMsg('DOC_DT','Please enter Date.');
      }
      else if(CUSTID_REF ===""){
        alertMsg('txtcust_popup','Please select Customer.');
      }  
      else{
        event.preventDefault();
        var allblank1 = [];
        var focustext1= "";
        var textmsg = "";
      
    $('#example2').find('.participantRow').each(function(){

      if($.trim($(this).find("[id*=INVNOID_REF]").val()) ===""){
        allblank1.push('false');
        focustext1 = $(this).find("[id*=popupInvNo]").attr('id');
        textmsg = 'Please select Invoice No';
      }
      
      else if($.trim($(this).find("[id*=ITEMID_REF]").val()) ===""){
        allblank1.push('false');
        focustext1 = $(this).find("[id*=popupITEMID]").attr('id');
        textmsg = 'Please select Item Code';
      }
      
      else if($.trim($(this).find("[id*=EXTENDEDWNTMONTH]").val()) ===""){
        allblank1.push('false');
        focustext1 = $(this).find("[id*=EXTENDEDWNTMONTH]").attr('id');
        textmsg = 'Please enter Extended Warranty/ In Months';
      } 

      else if($.trim($(this).find("[id*=STARTFROM]").val()) ===""){
        allblank1.push('false');
        focustext1 = $(this).find("[id*=STARTFROM]").attr('id');
        textmsg = 'Please select Start From';
      } 

      else if($.trim($(this).find("[id*=STARTTO]").val()) ===""){
        allblank1.push('false');
        focustext1 = $(this).find("[id*=STARTTO]").attr('id');
        textmsg = 'Please select Start To';
      } 

      else if($.trim($(this).find("[id*=WARRANTYAMT]").val()) ===""){
        allblank1.push('false');
        focustext1 = $(this).find("[id*=WARRANTYAMT]").attr('id');
        textmsg = 'Please enter Warranty Amount';
      }   
      else{
        allblank1.push('true');
        focustext1   = "";
      }

    });

    if(jQuery.inArray("false", allblank1) !== -1){
      $("#FocusId").val(focustext1);
      $("#alert").modal('show');
      $("#AlertMessage").text(textmsg);
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else{
      checkDuplicateCode();
      $("#alert").modal('show');
      $("#AlertMessage").text('Do you want to save to record.');
      $("#YesBtn").data("funcname",actionType);  
      $("#YesBtn").focus();
      highlighFocusBtn('activeYes');      
  }

  }
}

/*================================== CHECK DUPLICATE FUNCTION =================================*/
function checkDuplicateCode(){

  var trnFormReq  = $("#DOC_NO").val();
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  $.ajax({
      url:'{{route("transaction",[$FormId,"codeduplicate"])}}',
      type:'POST',
      data:{'DOC_NO':trnFormReq},
      success:function(data) {
          if(data.exists) {
            $(".text-danger").hide();
            showError('ERROR_DOC_NO',data.msg);
            $("#DOC_NO").focus();
          }
          else{
            $("#alert").modal('show');
            $("#AlertMessage").text('Do you want to save to record.');
            $("#YesBtn").data("funcname","fnSaveData");
            $("#YesBtn").focus();
            $("#OkBtn").hide();
            highlighFocusBtn('activeYes');
          }                                
      },
      error:function(data){
        console.log("Error: Something went wrong.");
      },
  });
}


/*================================== POPUP ALL SEARCH FUNCTION =================================*/

    let CustTable2 = "#CustTable";
    let CustTable = "#CustTable";
    let headers     = document.querySelectorAll(CustTable2 + " th");
    headers.forEach(function(element, i) {
      element.addEventListener("click", function() {
        w3.sortHTML(CustTable, ".clsdpid", "td:nth-child(" + (i + 1) + ")");
      });
    });

    function searchCode(search_id,table_id,index_no) {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById(search_id);
      filter = input.value.toUpperCase();
      table = document.getElementById(table_id);
      tr = table.getElementsByTagName("tr");
      for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[index_no];
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

/*================================== PRONO POPUP FUNCTION =================================*/
      $('#txtcust_popup').click(function(event){
        showSelectedCheck($("#CUSTID_REF").val(),"SELECT_CUSTID_REF");
         $("#cust_popup").show();
         event.preventDefault();
      });

      $("#cust_closePopup").click(function(event){
        $("#cust_popup").hide();
        event.preventDefault();
      });

      $(".clscustcode").click(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");
        var textitle =   $("#txt"+fieldid+"").data("title");
        
        $('#txtcust_popup').val(texdesc);
        $('#CUSTID_REF').val(txtval);
        $('#TITLE').val(textitle);
        $("#cust_popup").hide();
        $("#custcodesearch").val(''); 
        $("#custnamesearch").val(''); 
          
        clearGrid();
        event.preventDefault();
      });

/*================================== inv DETAILS =================================*/

let invid = "#INVTable2";
let invid2 = "#INVTable";
let invidheaders = document.querySelectorAll(invid2 + " th");

invidheaders.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(invid, ".clsinvid", "td:nth-child(" + (i + 1) + ")");
  });
});

function INVCodeFunction(e) {
  if(e.which == 13){
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("invcodesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("INVTable2");
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

function INVNameFunction(e) {
  if(e.which == 13){
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("invnamesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("INVTable2");
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

function invUOMFunction(e) {
  if(e.which == 13){
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("invUOMsearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("INVTable2");
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

function INVQTYFunction(e) {
  if(e.which == 13){
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("invQTYsearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("INVTable2");
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

function INVGroupFunction(e) {
  if(e.which == 13){
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("invGroupsearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("INVTable2");
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

function INVCategoryFunction(e) {
  if(e.which == 13){
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("invCategorysearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("INVTable2");
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

function INVItemBUFunction() {
	var input, filter, table, tr, td, i, txtValue;
	input = document.getElementById("invItemBUsearch");
	filter = input.value.toUpperCase();
	table = document.getElementById("INVTable2");
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

function INVItemAPNFunction() {
	var input, filter, table, tr, td, i, txtValue;
	input = document.getElementById("invItemAPNsearch");
	filter = input.value.toUpperCase();
	table = document.getElementById("INVTable2");
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

function INVItemCPNFunction() {
	var input, filter, table, tr, td, i, txtValue;
	input = document.getElementById("invItemCPNsearch");
	filter = input.value.toUpperCase();
	table = document.getElementById("INVTable2");
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

function INVItemOEMPNFunction() {
	var input, filter, table, tr, td, i, txtValue;
	input = document.getElementById("invItemOEMPNsearch");
	filter = input.value.toUpperCase();
	table = document.getElementById("INVTable2");
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

function INVStatusFunction(e) {
  if(e.which == 13){
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("invStatussearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("INVTable2");
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

$('#Material').on('click','[id*="popupInvNo"]',function(event){

  var CUSTID_REF      =  $("#CUSTID_REF").val();
  var txtcust_popup   =  $("#txtcust_popup").attr('id');

  if(CUSTID_REF ===""){
    $("#FocusId").val(txtcust_popup);
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select Customer.');
    $("#alert").modal('show')
    $("#OkBtn1").focus();
  }  
  else{

    $('.invjs-selectall').prop('disabled', true);
    $("#tbody_ItemID").html('');  //clear for variable confliction
    $("#tbody_INV").html('loading...');
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $.ajax({
        url:'{{route("transaction",[$FormId,"getINVDetails"])}}',
        type:'POST',
        data:{'CUSTID_REF':CUSTID_REF},
        success:function(data) {
          $("#tbody_INV").html(data);    
          bindINVEvents();   
          $('.invjs-selectall').prop('disabled', false);                     
        },
        error:function(data){
          console.log("Error: Something went wrong.");
          $("#tbody_INV").html('');                        
        },
    }); 
          
    $("#INVpopup").show();

    var id1   = $(this).attr('id');
    var id2   = $(this).parent().parent().find('[id*="INVNOID_REF"]').attr('id');
    var id3   = $(this).parent().parent().find('[id*="INVDATE"]').attr('id');
    var id6   = ""; 
    var id7   = $(this).parent().parent().find('[id*="STARTFROM"]').attr('id');
    var id9   = $(this).parent().parent().find('[id*="INVIDNO"]').attr('id');
    var id10  = $(this).parent().parent().find('[id*="SEID_REF"]').attr('id');

    $('#hdn_INVID1').val(id1);
    $('#hdn_INVID2').val(id2);
    $('#hdn_INVID3').val(id3);
    $('#hdn_INVID6').val(id6);
    $('#hdn_INVID7').val(id7);
    $('#hdn_INVID9').val(id9);
    $('#hdn_INVID10').val(id10);

    var r_count = 0;
    var ItemID = [];
    $('#example2').find('.participantRow').each(function(){
      if($(this).find('[id*="INVNOID_REF"]').val() != '')
      {
        ItemID.push($(this).find('[id*="INVNOID_REF"]').val());
        r_count = parseInt(r_count)+1;
        $('#hdn_INVID21').val(r_count); // row counter
      }
    });
    $('#hdn_INVID19').val(ItemID.join(', '));

    var SQID = [];
    $('#example2').find('.participantRow').each(function(){
      if($(this).find('[id*="INVIDNO"]').val() != '')
      {
        SQID.push($(this).find('[id*="INVIDNO"]').val());
      }
    });
    $('#hdn_INVID24').val(SQID.join(', '));

    var SEID = [];
    $('#example2').find('.participantRow').each(function(){
      if($(this).find('[id*="SEID_REF"]').val() != '')
      {
        SEID.push($(this).find('[id*="SEID_REF"]').val());
      }
    });
    $('#hdn_INVID25').val(SEID.join(', '));
    event.preventDefault();
  }

});

$("#INVID_closePopup").click(function(event){
  $("#INVpopup").hide();
});

function bindINVEvents(){

  $('#INVTable2').off(); 
  
  //-------------------------
  $('.invjs-selectall').change(function()
  { 

    if($(this).prop("checked")){
      var item_array = [];
      $('#INVTable2').find('.clsinvid').each(function()
      {
          var fildinv_id          =   $(this).attr('id');
          var MAINITEM_ID         =   $("#txt"+fildinv_id+"").data("desc1"); 
          var MAINITEM_UOMID    =   $("#txt"+fildinv_id+"").data("desc4");    
          var INVIDNO           =   $("#txt"+fildinv_id+"").data("desc8");
          var SEID_REF           =   $("#txt"+fildinv_id+"").data("desc9");
          var CUSTID_REF           =   $("#txt"+fildinv_id+"").data("proid");
          item_array.push(CUSTID_REF+"_"+SEID_REF+'_'+INVIDNO+'_'+MAINITEM_ID+'_'+MAINITEM_UOMID);
      });
      get_all_item(item_array);
      $('#INVpopup').hide();
      $('.invjs-selectall').prop("checked", false);
      return false;
    }else{
      $('#INVpopup').hide();
      return false;
    }       
    return false;
    event.preventDefault();  
  }); 
  
  //-------------------------

  $('[id*="chkinvId"]').change(function(){

    var fildinv_id          =   $(this).parent().parent().attr('id');
    var item_id             =   $("#txt"+fildinv_id+"").data("desc1");
    var item_code           =   $("#txt"+fildinv_id+"").data("desc2");
    var item_name           =   $("#txt"+fildinv_id+"").data("desc3");
    var item_main_uom_id    =   $("#txt"+fildinv_id+"").data("desc4");
    var item_main_uom_code  =   $("#txt"+fildinv_id+"").data("desc5");
    var item_qty            =   $("#txt"+fildinv_id+"").data("desc6");
    var item_unique_row_id  =   $("#txt"+fildinv_id+"").data("desc7");
    var item_sqid           =   $("#txt"+fildinv_id+"").data("desc8");
    var item_seid           =   $("#txt"+fildinv_id+"").data("desc9");
    var item_soid           =   $("#txt"+fildinv_id+"").data("desc11");
    var from_date           =   $("#txt"+fildinv_id+"").data("desc3");

    if($(this).is(":checked") == true) {   
      if($('#hdn_INVID1').val() == "" && item_id != ''){

        var $tr       =   $('.material').closest('table');
        var allTrs    =   $tr.find('.participantRow').last();
        var lastTr    =   allTrs[allTrs.length-1];
        var $clone    =   $(lastTr).clone();

        $clone.find('td').each(function(){
        var el = $(this).find(':first-child');
        var id = el.attr('id') || null;
        if(id){
          var idLength = id.split('_').pop();
          var i = id.substr(id.length-idLength.length);
          var prefix = id.substr(0, (id.length-idLength.length));
          el.attr('id', prefix+(+i+1));
        }
        // var name = el.attr('name') || null;
        // if(name){
        //   var nameLength = name.split('_').pop();
        //   var i = name.substr(name.length-nameLength.length);
        //   var prefix1 = name.substr(0, (name.length-nameLength.length));
        //   el.attr('name', prefix1+(+i+1));
        // }
      });

        $clone.find('.remove').removeAttr('disabled'); 
        $clone.find('[id*="popupInvNo"]').val(item_code);
        $clone.find('[id*="INVNOID_REF"]').val(item_id);
        $clone.find('[id*="INVDATE"]').val(item_name);
        $clone.find('[id*="STARTFROM"]').val(from_date);
        $clone.find('[id*="INVIDNO"]').val(item_sqid);
        $clone.find('[id*="SEID_REF"]').val(item_seid);
        //clear sub item
        $clone.find('[id*="popupITEMID"]').val('');
        $clone.find('[id*="ITEMID_REF"]').val('');
        $clone.find('[id*="ItemName"]').val('');
        $clone.find('[id*="popupMUOM"]').val('');
        $clone.find('[id*="MAIN_UOMID_REF"]').val('');
        $clone.find('[id*="TOTALTAX"]').val('0.000');
        $clone.find('[id*="WARRANTYAMT"]').val('0.000');
        $clone.find('[id*="TAXAMT"]').val('0.000');
        $clone.find('[id*="TOTALAMT"]').val('0.000');
        $clone.find('[id*="REMARKS"]').val('');

        $tr.closest('table').append($clone);   
        var rowCount = $('#Row_Count1').val();
        rowCount    = parseInt(rowCount)+1;
        $('#Row_Count1').val(rowCount);
        $("#INVpopup").hide();
        
        event.preventDefault();

      }
      else{
       
        var txt_id1   =   $('#hdn_INVID1').val();
        var txt_id2   =   $('#hdn_INVID2').val();
        var txt_id3   =   $('#hdn_INVID3').val();
        var txt_id6   =   $('#hdn_INVID6').val();
        var txt_id7   =   $('#hdn_INVID7').val();
        var txt_id9   =   $('#hdn_INVID9').val();
        var txt_id10  =   $('#hdn_INVID10').val();
       
        if($.trim(txt_id1)!=""){
          $('#'+txt_id1).val(item_code);
          $('#'+txt_id1).parent().parent().find('[id*="popupITEMID"]').val('');
          $('#'+txt_id1).parent().parent().find('[id*="ITEMID_REF"]').val('');
          $('#'+txt_id1).parent().parent().find('[id*="ItemName"]').val('');
          $('#'+txt_id1).parent().parent().find('[id*="popupMUOM"]').val('');
          $('#'+txt_id1).parent().parent().find('[id*="MAIN_UOMID_REF"]').val('');
          $('#'+txt_id1).parent().parent().find('[id*="TOTALTAX"]').val('0.000');
          $('#'+txt_id1).parent().parent().find('[id*="WARRANTYAMT"]').val('0.000');
          $('#'+txt_id1).parent().parent().find('[id*="TAXAMT"]').val('0.000');
          $('#'+txt_id1).parent().parent().find('[id*="TOTALAMT"]').val('0.000');
          $('#'+txt_id1).parent().parent().find('[id*="REMARKS"]').val('');
        }
        if($.trim(txt_id2)!=""){
          $('#'+txt_id2).val(item_id);
        }
        if($.trim(txt_id3)!=""){
          $('#'+txt_id3).val(item_name);
        }
       
        if($.trim(txt_id6)!=""){
         // $('#'+txt_id6).val(item_qty);
        }
        if($.trim(txt_id7)!=""){
          $('#'+txt_id7).val(from_date);
        }
        
        if($.trim(txt_id9)!=""){
          $('#'+txt_id9).val(item_sqid);
        }
        if($.trim(txt_id10)!=""){
          $('#'+txt_id10).val(item_seid);
        }
        
        $('#hdn_INVID1').val('');
        $('#hdn_INVID2').val('');
        $('#hdn_INVID3').val('');
        $('#hdn_INVID4').val('');
        $('#hdn_INVID6').val('');
        $('#hdn_INVID7').val('');
        $('#hdn_INVID9').val('');
        $('#hdn_INVID10').val('');
        
      }
              
      $("#INVpopup").hide();
      event.preventDefault();
    }
    else if($(this).is(":checked") == false){

      var id = item_id;
      var r_count = $('#Row_Count1').val();

      $('#example2').find('.participantRow').each(function(){
        var INVNOID_REF = $(this).find('[id*="INVNOID_REF"]').val();

        if(id == INVNOID_REF){
          var rowCount = $('#Row_Count1').val();

          if (rowCount > 1) {
            $(this).closest('.participantRow').remove(); 
            rowCount = parseInt(rowCount)-1;
            $('#Row_Count1').val(rowCount);
          }
          else {
            $(document).find('.dmaterial').prop('disabled', true);  
            $("#INVpopup").hide();
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

    $("#invcodesearch").val(''); 
    $("#invnamesearch").val(''); 
    $("#invUOMsearch").val(''); 
    $("#invGroupsearch").val(''); 
    $("#invCategorysearch").val(''); 
    $("#invStatussearch").val(''); 
    $('.remove').removeAttr('disabled'); 
    
    event.preventDefault();
  });

} //bindinvEvents
/*================================== ITEM DETAILS =================================*/

let itemtid = "#ItemIDTable2";
let itemtid2 = "#ItemIDTable";
let itemtidheaders = document.querySelectorAll(itemtid2 + " th");

itemtidheaders.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(itemtid, ".clsitemid", "td:nth-child(" + (i + 1) + ")");
  });
});

function ItemCodeFunction(e) {
  if(e.which == 13){
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
}

function ItemNameFunction(e) {
  if(e.which == 13){
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
}

function ItemUOMFunction(e) {
  if(e.which == 13){
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
}

function ItemQTYFunction(e) {
  if(e.which == 13){
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("ItemQTYsearch");
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
}

function ItemGroupFunction(e) {
  if(e.which == 13){
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("ItemGroupsearch");
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
}

function ItemCategoryFunction(e) {
  if(e.which == 13){
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("ItemCategorysearch");
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
}

function ItemBUFunction(e) {
  if(e.which == 13){
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
}

function ItemAPNFunction(e) {
  if(e.which == 13){
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
}

function ItemCPNFunction(e) {
  if(e.which == 13){
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
}

function ItemOEMPNFunction(e) {
  if(e.which == 13){
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
}

function ItemStatusFunction(e) {
  if(e.which == 13){
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("ItemStatussearch");
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
}

$('#Material').on('click','[id*="popupITEMID"]',function(event){

  var CUSTID_REF      =  $("#CUSTID_REF").val();
  var txtcust_popup   =  $("#txtcust_popup").attr('id');
  var INVNOID_REF     =  $.trim( $(this).parent().parent().find('[id*="INVNOID_REF"]').val() );
  var INVIDNO       = $(this).parent().parent().find('[id*="INVIDNO"]').val();
  var SEID_REF       = $(this).parent().parent().find('[id*="SEID_REF"]').val();

  if(CUSTID_REF ===""){
    $("#FocusId").val(txtcust_popup);
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select Customer.');
    $("#alert").modal('show')
    $("#OkBtn1").focus();

  }else if(INVNOID_REF ===""){
    $("#FocusId").val('');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select Invoice No.');
    $("#alert").modal('show')
    $("#OkBtn1").focus();
  }  
  else{

    $('.js-selectall').prop('disabled', true);
    $("#tbody_INV").html('');
    $("#tbody_ItemID").html('loading...');
    $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $.ajax({
        url:'{{route("transaction",[$FormId,"getItemDetails"])}}',
        type:'POST',
        data:{'CUSTID_REF':CUSTID_REF,'status':'A','INVNOID_REF':INVNOID_REF,'INVIDNO':INVIDNO,'SEID_REF':SEID_REF},
        success:function(data) {
          $("#tbody_ItemID").html(data);    
          bindItemEvents();   
          $('.js-selectall').prop('disabled', false);                     
        },
        error:function(data){
          console.log("Error: Something went wrong.");
          $("#tbody_ItemID").html('');                        
        },
    }); 
          
    $("#ITEMIDpopup").show();

    var id1   = $(this).attr('id');
    var id2   = $(this).parent().parent().find('[id*="ITEMID_REF"]').attr('id');
    var id3   = $(this).parent().parent().find('[id*="ItemName"]').attr('id');
    var id6   = $(this).parent().parent().find('[id*="TOTALTAX"]').attr('id');
    var id7   = "";
    var id9   = $(this).parent().parent().find('[id*="INVIDNO"]').attr('id');
    var id10  = $(this).parent().parent().find('[id*="SEID_REF"]').attr('id');

    $('#hdn_ItemID1').val(id1);
    $('#hdn_ItemID2').val(id2);
    $('#hdn_ItemID3').val(id3);
    $('#hdn_ItemID6').val(id6);
    $('#hdn_ItemID7').val(id7);
    $('#hdn_ItemID9').val(id9);
    $('#hdn_ItemID10').val(id10);

    var r_count = 0;
    var ItemID = [];
    $('#example2').find('.participantRow').each(function(){
      if($(this).find('[id*="ITEMID_REF"]').val() != '')
      {
        ItemID.push($(this).find('[id*="ITEMID_REF"]').val());
        r_count = parseInt(r_count)+1;
        $('#hdn_ItemID21').val(r_count); // row counter
      }
    });
    $('#hdn_ItemID19').val(ItemID.join(', '));

    var SQID = [];
    $('#example2').find('.participantRow').each(function(){
      if($(this).find('[id*="INVIDNO"]').val() != '')
      {
        SQID.push($(this).find('[id*="INVIDNO"]').val());
      }
    });
    $('#hdn_ItemID24').val(SQID.join(', '));

    var SEID = [];
    $('#example2').find('.participantRow').each(function(){
      if($(this).find('[id*="SEID_REF"]').val() != '')
      {
        SEID.push($(this).find('[id*="SEID_REF"]').val());
      }
    });
    $('#hdn_ItemID25').val(SEID.join(', '));

    var INVID = [];
    $('#example2').find('.participantRow').each(function(){
      if($(this).find('[id*="INVNOID_REF"]').val() != '')
      {
        INVID.push($(this).find('[id*="INVNOID_REF"]').val());
      }
    });
    $('#hdn_ItemID26').val(INVID.join(', '));

    event.preventDefault();

  }

});

$("#ITEMID_closePopup").click(function(event){
  $("#ITEMIDpopup").hide();
});

function bindItemEvents(){

  $('#ItemIDTable2').off(); 
  //-------------------------
  $('.js-selectall').change(function()
  { 
    //select all checkbox
    var isChecked = $(this).prop("checked");
    var selector = $(this).data('target');
    $(selector).prop("checked", isChecked);
    //$$--------------------
    $('#ItemIDTable2').find('.clsitemid').each(function()
        {
            var fieldid             =   $(this).attr('id');
            var item_id             =   $("#txt"+fieldid+"").data("desc1");
            var item_code           =   $("#txt"+fieldid+"").data("desc2");
            var item_name           =   $("#txt"+fieldid+"").data("desc3");
            var item_main_uom_id    =   $("#txt"+fieldid+"").data("desc4");
            var item_main_uom_code  =   $("#txt"+fieldid+"").data("desc5");
            var item_qty            =   $("#txt"+fieldid+"").data("desc6");
            var item_unique_row_id  =   $("#txt"+fieldid+"").data("desc7");
            var item_sqid           =   $("#txt"+fieldid+"").data("desc8");
            var item_seid           =   $("#txt"+fieldid+"").data("desc9");
            var item_soid           =   $("#txt"+fieldid+"").data("desc11");
            var item_fgiid           =   $("#txt"+fieldid+"").data("itemfgiid");

            var apartno =  $("#addinfo"+fieldid+"").data("desc101");
            var cpartno =  $("#addinfo"+fieldid+"").data("desc102");
            var opartno =  $("#addinfo"+fieldid+"").data("desc103");

           
            var rcount1 = parseInt($(this).closest('table').find('.clsitemid').length);
            var rcount2 = $('#hdn_ItemID21').val();
            var r_count2 = 0;
            
            var gridRow2 = [];
            $('#example2').find('.participantRow').each(function(){
              if($(this).find('[id*="ITEMID_REF"]').val() != '')
              {
                var subsitem = $(this).find('[id*="PROID_REF"]').val()+'_'+$(this).find('[id*="SOID_REF"]').val()+'_'+$(this).find('[id*="INVIDNO"]').val()+'_'+$(this).find('[id*="SEID_REF"]').val()+'_'+$(this).find('[id*="ITEMID_REF"]').val();
                gridRow2.push(subsitem);
                r_count2 = parseInt(r_count2) + 1;
              }
            });
        
            var slids =  $('#hdn_ItemID18').val();
            var itemids =  $('#hdn_ItemID19').val();
            var soids =  $('#hdn_ItemID23').val();
            var sqids =  $('#hdn_ItemID24').val();
            var seids =  $('#hdn_ItemID25').val();
            var fgiids =  $('#hdn_ItemID26').val();
    
            if($(this).find('[id*="chkId"]').is(":checked") == true) 
            {
              rcount1 = parseInt(rcount2)+parseInt(rcount1);
              if(parseInt(r_count2) >= parseInt(rcount1))
              {
                $('#hdn_ItemID1').val('');
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
                $('#hdn_ItemID13').val('');
                $('#hdn_ItemID14').val('');
                $('#hdn_ItemID15').val('');
                $('#hdn_ItemID16').val('');
                $('#hdn_ItemID17').val('');
                $('#hdn_ItemID18').val('');
                $('#hdn_ItemID19').val('');
                $('#hdn_ItemID20').val('');
                $('#hdn_ItemID22').val('');
                
                fieldid             =   "";
                item_id             =   "";
                item_code           =   "";
                item_name           =   "";
                item_main_uom_id    =   "";
                item_main_uom_code  =   "";
                item_qty            =   "";
                item_unique_row_id  =   "";
                item_sqid           =   "";
                item_seid           =   "";
                item_soid           =   "";
                item_fgiid           =   "";

                $('.js-selectall').prop("checked", false);
                $("#ITEMIDpopup").hide();
                return false;
              }
              
              var txtrowitem = item_fgiid+'_'+item_soid+'_'+item_sqid+'_'+item_seid+'_'+item_id;
              if(jQuery.inArray(txtrowitem, gridRow2) !== -1)
              {
                    $("#ITEMIDpopup").hide();
                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn").hide();
                    $("#OkBtn1").show();
                    $("#AlertMessage").text('Item already exists!');
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
                    $('#hdn_ItemID13').val('');
                    $('#hdn_ItemID14').val('');
                    $('#hdn_ItemID15').val('');
                    $('#hdn_ItemID16').val('');
                    $('#hdn_ItemID17').val('');
                    $('#hdn_ItemID18').val('');
                    $('#hdn_ItemID19').val('');
                    $('#hdn_ItemID20').val('');
                    $('#hdn_ItemID22').val('');
                    fieldid             =   "";
                    item_id             =   "";
                    item_code           =   "";
                    item_name           =   "";
                    item_main_uom_id    =   "";
                    item_main_uom_code  =   "";
                    item_qty            =   "";
                    item_unique_row_id  =   "";
                    item_sqid           =   "";
                    item_seid           =   "";
                    item_soid           =   "";
                    item_fgiid          =   "";
                    $('.js-selectall').prop("checked", false);
                    $("#ITEMIDpopup").hide();
                    return false;
              }
              
                  if($('#hdn_ItemID1').val() == "" && item_id != '')
                  {
                    var txtid= $('#hdn_ItemID1').val();
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
                    var txt_id13= $('#hdn_ItemID13').val();
                    var txt_id14= $('#hdn_ItemID14').val();
                    var txt_id15= $('#hdn_ItemID15').val();
                    var txt_id16= $('#hdn_ItemID16').val();
                    var txt_id22= $('#hdn_ItemID22').val();

                    var txt_id23= $('#hdn_ItemID23').val();
                    var txt_id24= $('#hdn_ItemID24').val();
                    var txt_id25= $('#hdn_ItemID25').val();
                    var txt_id26= $('#hdn_ItemID26').val();

                    var $tr = $('.material').closest('table');
                    var allTrs = $tr.find('.participantRow').last();
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
                      // var name = el.attr('name') || null;
                      // if(name){
                      //   var nameLength = name.split('_').pop();
                      //   var i = name.substr(name.length-nameLength.length);
                      //   var prefix1 = name.substr(0, (name.length-nameLength.length));
                      //   el.attr('name', prefix1+(+i+1));
                      // }
                    });

                        $clone.find('.remove').removeAttr('disabled'); 
                        $clone.find('[id*="popupITEMID"]').val(item_code);
                        $clone.find('[id*="ITEMID_REF"]').val(item_id);
                        $clone.find('[id*="ItemName"]').val(item_name);
                        $clone.find('[id*="popupMUOM"]').val(item_main_uom_code);
                        $clone.find('[id*="MAIN_UOMID_REF"]').val(item_main_uom_id);
                        $clone.find('[id*="QTY"]').val(item_qty);
                        $clone.find('[id*="REQ_QTY"]').val(item_qty);
                        $clone.find('[id*="INVIDNO"]').val(item_sqid);
                        $clone.find('[id*="SEID_REF"]').val(item_seid);
                        $clone.find('[id*="SOID_REF"]').val(item_soid);

                        $clone.find('[id*="Alpspartno"]').val(apartno);
                        $clone.find('[id*="Custpartno"]').val(cpartno);
                        $clone.find('[id*="OEMpartno"]').val(opartno);
                                               
                        $tr.closest('table').append($clone);   
                        var rowCount = $('#Row_Count1').val();
                        rowCount = parseInt(rowCount)+1;
                        $('#Row_Count1').val(rowCount);
                       
                        event.preventDefault();
                  }
                  else
                  {
                                                              
                      var txt_id1   =   $('#hdn_ItemID1').val();
                      var txt_id2   =   $('#hdn_ItemID2').val();
                      var txt_id3   =   $('#hdn_ItemID3').val();
                      var txt_id4   =   $('#hdn_ItemID4').val();
                      var txt_id5   =   $('#hdn_ItemID5').val();
                      var txt_id6   =   $('#hdn_ItemID6').val();
                      var txt_id7   =   $('#hdn_ItemID7').val();
                      var txt_id8   =   $('#hdn_ItemID8').val();
                      var txt_id9   =   $('#hdn_ItemID9').val();
                      var txt_id10  =   $('#hdn_ItemID10').val();
                      var txt_id11  =   $('#hdn_ItemID11').val();
                    
                      $('#'+txt_id1).val(item_code);
                      $('#'+txt_id2).val(item_id);
                      $('#'+txt_id3).val(item_name);
                      $('#'+txt_id4).val(item_main_uom_code);
                      $('#'+txt_id5).val(item_main_uom_id);
                      $('#'+txt_id6).val(item_qty);
                      //$('#'+txt_id7).val(0);
                      $('#'+txt_id8).val(item_qty);
                      $('#'+txt_id9).val(item_sqid);
                      $('#'+txt_id10).val(item_seid);
                      $('#'+txt_id11).val(item_soid);

                      $('#'+txt_id2).parent().parent().find('[id*="Alpspartno"]').val(apartno);
                      $('#'+txt_id2).parent().parent().find('[id*="Custpartno"]').val(cpartno);
                      $('#'+txt_id2).parent().parent().find('[id*="OEMpartno"]').val(opartno);
                
                      $('#hdn_ItemID1').val('');
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

                      event.preventDefault();
                  }

                  $('.js-selectall').prop("checked", false);
                  // $("#ITEMIDpopup").reload();
                  $('#ITEMIDpopup').hide();
                  event.preventDefault();
                  
            }
            // else if($(this).is(":checked") == false) 
            // {
            //   
            // }
          $("#Itemcodesearch").val(''); 
          $("#Itemnamesearch").val(''); 
          $("#ItemUOMsearch").val(''); 
          $("#ItemGroupsearch").val(''); 
          $("#ItemCategorysearch").val(''); 
          $("#ItemStatussearch").val(''); 
          $('.remove').removeAttr('disabled'); 
         
          event.preventDefault();
        });
    //$$--------------------
    
    $('#ITEMIDpopup').hide();
    return false;
    event.preventDefault();

  }); 
  
  //-------------------------

  $('[id*="chkId"]').change(function(){

    var fieldid             =   $(this).parent().parent().attr('id');
    var item_id             =   $("#txt"+fieldid+"").data("desc1");
    var item_code           =   $("#txt"+fieldid+"").data("desc2");
    var item_name           =   $("#txt"+fieldid+"").data("desc3");
    var item_main_uom_id    =   $("#txt"+fieldid+"").data("desc4");
    var item_main_uom_code  =   $("#txt"+fieldid+"").data("desc5");
    var item_qty            =   $("#txt"+fieldid+"").data("desc6");
    var item_unique_row_id  =   $("#txt"+fieldid+"").data("desc7");
    var item_sqid           =   $("#txt"+fieldid+"").data("desc8");
    var item_seid           =   $("#txt"+fieldid+"").data("desc9");
    var item_soid           =   $("#txt"+fieldid+"").data("desc11");
    var item_fgiid           =   $("#txt"+fieldid+"").data("itemfgiid");

    var apartno =  $("#addinfo"+fieldid+"").data("desc101");
    var cpartno =  $("#addinfo"+fieldid+"").data("desc102");
    var opartno =  $("#addinfo"+fieldid+"").data("desc103");

    if($(this).is(":checked") == true) {

      $('#example2').find('.participantRow').each(function(){

        var SOID_REF    =   $(this).find('[id*="SOID_REF"]').val();
        var ITEMID_REF  =   $(this).find('[id*="ITEMID_REF"]').val();
        var INVIDNO    =   $(this).find('[id*="INVIDNO"]').val();
        var SEID_REF    =   $(this).find('[id*="SEID_REF"]').val();
        var PROID_REF    =   $(this).find('[id*="PROID_REF"]').val();
        var exist_val   =   INVIDNO+"_"+ITEMID_REF;
        //var exist_val   =   ITEMID_REF;

        if(item_id){
          if(item_unique_row_id == exist_val){
            $("#ITEMIDpopup").hide();
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text('Item already exists!!');
            $("#alert").modal('show');
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk1');

            $('#hdn_ItemID1').val('');
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
             
            item_id             =   '';
            item_code           =   '';
            item_name           =   '';
            item_main_uom_id    =   '';
            item_main_uom_code  =   '';
            item_qty            =   '';
            item_unique_row_id  =   '';
            item_sqid           =   '';
            item_seid           =   '';
            item_soid           =   '';
            return false;
          }               
        } 
                 
      });

      if($('#hdn_ItemID1').val() == "" && item_id != ''){

        var $tr       =   $('.material').closest('table');
        var allTrs    =   $tr.find('.participantRow').last();
        var lastTr    =   allTrs[allTrs.length-1];
        var $clone    =   $(lastTr).clone();

        $clone.find('td').each(function(){
        var el = $(this).find(':first-child');
        var id = el.attr('id') || null;
        if(id){
          var idLength = id.split('_').pop();
          var i = id.substr(id.length-idLength.length);
          var prefix = id.substr(0, (id.length-idLength.length));
          el.attr('id', prefix+(+i+1));
        }
        // var name = el.attr('name') || null;
        // if(name){
        //   var nameLength = name.split('_').pop();
        //   var i = name.substr(name.length-nameLength.length);
        //   var prefix1 = name.substr(0, (name.length-nameLength.length));
        //   el.attr('name', prefix1+(+i+1));
        // }
      });

        $clone.find('.remove').remoSP_EXT_WARRANTY_UPveAttr('disabled'); 
        $clone.find('[id*="popupITEMID"]').val(item_code);
        $clone.find('[id*="ITEMID_REF"]').val(item_id);
        $clone.find('[id*="ItemName"]').val(item_name);
        $clone.find('[id*="popupMUOM"]').val(item_main_uom_code);
        $clone.find('[id*="MAIN_UOMID_REF"]').val(item_main_uom_id);
        $clone.find('[id*="QTY"]').val(item_qty);
        $clone.find('[id*="REQ_QTY"]').val(item_qty);
        $clone.find('[id*="INVIDNO"]').val(item_sqid);
        $clone.find('[id*="SEID_REF"]').val(item_seid);
        $clone.find('[id*="SOID_REF"]').val(item_soid);

        $clone.find('[id*="Alpspartno"]').val(apartno);
        $clone.find('[id*="Custpartno"]').val(cpartno);
        $clone.find('[id*="OEMpartno"]').val(opartno);

        $tr.closest('table').append($clone);   
        var rowCount = $('#Row_Count1').val();
        rowCount    = parseInt(rowCount)+1;
        $('#Row_Count1').val(rowCount);
        $("#ITEMIDpopup").hide();
        
        event.preventDefault();

      }
      else{

        var txt_id1   =   $('#hdn_ItemID1').val();
        var txt_id2   =   $('#hdn_ItemID2').val();
        var txt_id3   =   $('#hdn_ItemID3').val();
        var txt_id4   =   $('#hdn_ItemID4').val();
        var txt_id5   =   $('#hdn_ItemID5').val();
        var txt_id6   =   $('#hdn_ItemID6').val();
        var txt_id7   =   $('#hdn_ItemID7').val();
        var txt_id8   =   $('#hdn_ItemID8').val();
        var txt_id9   =   $('#hdn_ItemID9').val();
        var txt_id10  =   $('#hdn_ItemID10').val();
        var txt_id11  =   $('#hdn_ItemID11').val();
       
        if($.trim(txt_id1)!=""){
          $('#'+txt_id1).val(item_code);
        }
        if($.trim(txt_id2)!=""){
          $('#'+txt_id2).val(item_id);
        }
        if($.trim(txt_id3)!=""){
          $('#'+txt_id3).val(item_name);
        }
        if($.trim(txt_id4)!=""){
          $('#'+txt_id4).val(item_main_uom_code);
        }
        if($.trim(txt_id5)!=""){
          $('#'+txt_id5).val(item_main_uom_id);
        }
        if($.trim(txt_id6)!=""){
          $('#'+txt_id6).val(item_qty);
        }
        if($.trim(txt_id7)!=""){
          $('#'+txt_id7).val(0);
        }
        if($.trim(txt_id8)!=""){
          $('#'+txt_id8).val(item_qty);
        }
        if($.trim(txt_id9)!=""){
          $('#'+txt_id9).val(item_sqid);
        }
        if($.trim(txt_id10)!=""){
          $('#'+txt_id10).val(item_seid);
        }
        if($.trim(txt_id11)!=""){
          $('#'+txt_id11).val(item_soid);
        }

        $('#'+txt_id2).parent().parent().find('[id*="Alpspartno"]').val(apartno);
        $('#'+txt_id2).parent().parent().find('[id*="Custpartno"]').val(cpartno);
        $('#'+txt_id2).parent().parent().find('[id*="OEMpartno"]').val(opartno);

        $('#hdn_ItemID1').val('');
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
        
      }
              
      $("#ITEMIDpopup").hide();
      event.preventDefault();
    }
    else if($(this).is(":checked") == false){

      var id = item_id;
      var r_count = $('#Row_Count1').val();

      $('#example2').find('.participantRow').each(function(){
        var ITEMID_REF = $(this).find('[id*="ITEMID_REF"]').val();

        if(id == ITEMID_REF){
          var rowCount = $('#Row_Count1').val();

          if (rowCount > 1) {
            $(this).closest('.participantRow').remove(); 
            rowCount = parseInt(rowCount)-1;
            $('#Row_Count1').val(rowCount);
          }
          else {
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
    $("#ItemGroupsearch").val(''); 
    $("#ItemCategorysearch").val(''); 
    $("#ItemStatussearch").val(''); 
    $('.remove').removeAttr('disabled'); 
   
    event.preventDefault();
  });

} //bindItemEvents

/*================================== ADD/REMOVE FUNCTION ==================================*/

$("#Material").on('click', '.add', function() {
  var $tr = $(this).closest('table');
  var allTrs = $tr.find('.participantRow').last();
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
	// var name = el.attr('name') || null;
	// if(name){
	// 	var nameLength = name.split('_').pop();
	// 	var i = name.substr(name.length-nameLength.length);
	// 	var prefix1 = name.substr(0, (name.length-nameLength.length));
	// 	el.attr('name', prefix1+(+i+1));
	// }
});

  $clone.find('input:text').val('');
  $clone.find('input:hidden').val('');

  $tr.closest('table').append($clone);         
  var rowCount1 = $('#Row_Count1').val();
  rowCount1 = parseInt(rowCount1)+1;
  $('#Row_Count1').val(rowCount1);
  $clone.find('.remove').removeAttr('disabled'); 
  event.preventDefault();
});

$("#Material").on('click', '.remove', function() {
    var rowCount = $(this).closest('table').find('.participantRow').length;
    if (rowCount > 1) {
		$(this).closest('.participantRow').remove();  
	 
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


/*================================== USER DEFINE FUNCTION ==================================*/
function isNumberDecimalKey(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
    return false;

    return true;
}

/*================================== ONLOAD FUNCTION ==================================*/
$(document).ready(function(e) {
  var Material = $("#Material").html(); 
  $('#hdnmaterial').val(Material);
  var today = new Date(); 
  var prodate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
  $('#DOC_DT').val(prodate);
});

function clearGrid(){
  $('#example2').find('.participantRow').each(function(){
    var rowCount = $(this).closest('table').find('.participantRow').length;
    $('#Row_Count1').val(rowCount);
    $(this).closest('.participantRow').find('input:text').val('');
    $(this).closest('.participantRow').find('input:hidden').val('');
    if (rowCount > 1) {
		  $(this).closest('.participantRow').remove();  
    } 
  });
}

function showSelectedCheck(hidden_value,selectAll){
  var divid ="";
  if(hidden_value !=""){
      var all_location_id = document.querySelectorAll('input[name="'+selectAll+'[]"]');      
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

function getWatnTaxDetails(id,txtval){
      var ROW_ID = id.split('_').pop();
      var TotalAmount = 0;
      var WARRANTYAMT   =   $('#WARRANTYAMT_'+ROW_ID+'').val();
      var TOTALTAX      =   $('#TOTALTAX_'+ROW_ID+'').val();
      var TaxAmt = parseFloat((parseFloat(WARRANTYAMT)*parseFloat(TOTALTAX))/100).toFixed(2);
      var FinalAmt = parseFloat((parseFloat(WARRANTYAMT)+parseFloat(TaxAmt))).toFixed(2);
      if(WARRANTYAMT==''){
      $('#TAXAMT_'+ROW_ID).val(TOTALTAX);
      }else if(TOTALTAX==''){
        $('#TAXAMT_'+ROW_ID).val(WARRANTYAMT);
      }else{
      $('#TAXAMT_'+ROW_ID).val(TaxAmt);
      $('#TOTALAMT_'+ROW_ID).val(FinalAmt);
      }
    }
  
  function getExtnFromDate(id,txtval){
    $(document).ready(function(e) {
      var extnwarMonth =  parseFloat(txtval);
      var fromdate = $(this).find('[id*="STARTFROM"]').val();
      var date = new Date(fromdate);
      var additionOfMonths = extnwarMonth;
      date.setMonth(date.getMonth() + additionOfMonths);
      console.log(date);
      var formatYmd = date.toISOString().slice(0, 10);

      var ROW_ID = id.split('_').pop();
      $('#STARTTO_'+ROW_ID).val(formatYmd);
    });
    }
  

function onlyNumberKey(evt) {
  var ASCIICode = (evt.which) ? evt.which : evt.keyCode
  if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
    return false;
  return true;
}
</script>
@endpush
