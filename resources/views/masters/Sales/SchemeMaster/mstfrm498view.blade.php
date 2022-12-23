@extends('layouts.app')
@section('content')

<div class="container-fluid topnav">
    <div class="row">
        <div class="col-lg-2">
        <a href="{{route('master',[$FormId,'index'])}}" class="btn singlebt">Scheme Master</a>
        </div>

        <div class="col-lg-10 topnav-pd">
                <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
                <button class="btn topnavbt" id="btnSaveSE" disabled="disabled"><i class="fa fa-floppy-o" ></i> Save</button>
                <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                <button class="btn topnavbt" id="btnUndo"  disabled="disabled"><i class="fa fa-undo"></i> Undo</button>
                <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                <button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-thumbs-o-up"></i> Approved</button>
                <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
        </div>
    </div>
</div>

<form id="frm_trn_edit"  method="POST">   
  @csrf
  <div class="container-fluid filter">
	  <div class="inner-form">

      <div class="row">
        <div class="col-lg-1 pl"><p>Scheme No</p></div>
        <div class="col-lg-2 pl">
          <input {{$ActionStatus}} type="text" name="SCHEME_NO" id="SCHEME_NO" value="{{isset($objResponse->SCHEME_NO)?$objResponse->SCHEME_NO:''}}" class="form-control mandatory" readonly  autocomplete="off"  style="text-transform:uppercase"  >
          <input type="hidden" name="SCHEMEID" id="SCHEMEID" value="{{isset($objResponse->SCHEMEID)?$objResponse->SCHEMEID:''}}" >
        </div>
              
        <div class="col-lg-1 pl"><p>Scheme Date</p></div>
        <div class="col-lg-2 pl">
          <input {{$ActionStatus}} type="date" name="SCHEME_DATE" id="SCHEME_DATE" value="{{isset($objResponse->SCHEME_DATE)?$objResponse->SCHEME_DATE:''}}" class="form-control mandatory"  placeholder="dd/mm/yyyy" >
        </div>

        <div class="col-lg-1 pl"><p>Scheme Name</p></div>
        <div class="col-lg-2 pl">
          <input {{$ActionStatus}} type="text" name="SCHEME_NAME" id="SCHEME_NAME" value="{{isset($objResponse->SCHEME_NAME)?$objResponse->SCHEME_NAME:''}}" class="form-control" autocomplete="off" >
        </div>

        <div class="col-lg-1 pl"><p>Scheme Type</p></div>
        <div class="col-lg-2 pl">
          <select {{$ActionStatus}} name="SCHEME_TYPE" id="SCHEME_TYPE" class="form-control" >
            <option {{isset($objResponse) && $objResponse->SCHEME_TYPE=='QUANTITY'?'selected="selected"':''}} value="QUANTITY">QUANTITY</option>
          </select>
        </div>

      </div>

      <div class="row">
        <div class="col-lg-1 pl"><p>Effective From Date</p></div>
        <div class="col-lg-2 pl">
          <input {{$ActionStatus}} type="date" name="EFF_FROM_DATE" id="EFF_FROM_DATE" value="{{isset($objResponse->EFF_FROM_DATE)?$objResponse->EFF_FROM_DATE:''}}" class="form-control" autocomplete="off" >
        </div>

        <div class="col-lg-1 pl"><p>Effective To Date</p></div>
        <div class="col-lg-2 pl">
          <input {{$ActionStatus}} type="date" name="EFF_TO_DATE" id="EFF_TO_DATE" value="{{isset($objResponse->EFF_TO_DATE)?$objResponse->EFF_TO_DATE:''}}" class="form-control" autocomplete="off" >
        </div>

        <div class="col-lg-1 pl"><p>Item Code</p></div>
        <div class="col-lg-2 pl">
          <input {{$ActionStatus}} type="text"    name="MAIN_ITEMID_NAME" id="MAIN_ITEMID_NAME" value="{{isset($objResponse->ICODE)?$objResponse->ICODE:''}}" class="form-control" onclick="getMainItem()" readonly >
          <input type="hidden"  name="MAIN_ITEMID_REF"  id="MAIN_ITEMID_REF" value="{{isset($objResponse->ITEMID_REF)?$objResponse->ITEMID_REF:''}}" class="form-control" >
          <input type="hidden"  id="HIDDEN_ITEM_FIELD"  class="form-control" >
        </div>

        <div class="col-lg-1 pl"><p>Item Name</p></div>
        <div class="col-lg-2 pl">
          <input {{$ActionStatus}} type="text" name="ITEM_NAME" id="ITEM_NAME"  value="{{isset($objResponse->ITEM_NAME)?$objResponse->ITEM_NAME:''}}" class="form-control" autocomplete="off" readonly >
        </div>
      </div>

      <div class="row">
        <div class="col-lg-1 pl"><p>Main UOM</p></div>
        <div class="col-lg-2 pl">
          <input {{$ActionStatus}} type="text" name="MAIN_UOM" id="MAIN_UOM" value="{{isset($objResponse->UOMCODE)?$objResponse->UOMCODE:''}}{{isset($objResponse->DESCRIPTIONS)?'-'.$objResponse->DESCRIPTIONS:''}}" class="form-control" readonly >
          <input type="hidden" name="MAIN_UOM_REF" id="MAIN_UOM_REF" value="{{isset($objResponse->UOMID_REF)?$objResponse->UOMID_REF:''}}" class="form-control" readonly >
        </div>

        <div class="col-lg-1 pl"><p>Qty</p></div>
        <div class="col-lg-2 pl">
          <input {{$ActionStatus}} type="text" name="MAIN_QTY" id="MAIN_QTY" value="{{isset($objResponse->QTY)?$objResponse->QTY:''}}" class="form-control" onkeypress="return isNumberKey(event,this)" >
        </div>

        <div class="col-lg-1 pl"><p>Remarks</p></div>
        <div class="col-lg-2 pl">
          <input {{$ActionStatus}} type="text" name="REMARKS" id="REMARKS" value="{{isset($objResponse->REMARKS)?$objResponse->REMARKS:''}}" class="form-control" >
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
					  <div class="table-responsive table-wrapper-scroll-y" style="height:280px;margin-top:10px;" >
						  <table id="example2" class="display nowrap table table-striped table-bordered itemlist w-200" width="100%" style="height:auto !important;">
							  <thead id="thead1"  style="position: sticky;top: 0">
                  <tr>
                    <th>Scheme Item Code</th>
                    <th>Item Name</th>
                    <th>UOM</th>
                    <th>Qty</th>
                    <th>Cost</th>
                    <th>Percentage</th>
                    <th>Percentage Based On</th>
                    <th>Action</th>
								  </tr>
							  </thead>
							  <tbody>
                  @if(isset($objMAT) && !empty($objMAT))
                  @foreach($objMAT as $key => $row)
							    <tr  class="participantRow">

                    <td><input {{$ActionStatus}}  type="text" name="popupITEMID[]" id="popupITEMID_{{$key}}" value="{{ $row->ICODE }}" onclick="getItem(this.id)" class="form-control"  autocomplete="off"  readonly/></td>
                    <td hidden><input type="hidden" name="ITEMID_REF[]" id="ITEMID_REF_{{$key}}" value="{{ $row->ITEMID_REF }}" class="form-control" autocomplete="off" /></td>
                    <td><input {{$ActionStatus}} type="text" name="ItemName[]" id="ItemName_{{$key}}" value="{{ $row->ITEM_NAME }}" class="form-control"  autocomplete="off"  readonly /></td>
                    <td><input {{$ActionStatus}} type="text" name="popupMUOM[]" id="popupMUOM_{{$key}}" class="form-control" value="{{ $row->UOMCODE }}-{{ $row->DESCRIPTIONS }}"  autocomplete="off"  readonly /></td>
                    <td hidden><input type="hidden" name="MAIN_UOMID_REF[]" id="MAIN_UOMID_REF_{{$key}}" value="{{ $row->UOMID_REF }}" class="form-control"  autocomplete="off" /></td>
                    <td><input {{$ActionStatus}} type="text"  name="QTY[]" id="QTY_{{$key}}" value="{{ $row->ITEM_QTY }}" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" readonly /></td>
                    
                    <td>
                      <select {{$ActionStatus}} name="COST[]" id="COST_{{$key}}" class="form-control" autocomplete="off" onchange="getCost(this)" >
                        <option {{isset($row->COST) && $row->COST=='FREE'?'selected="selected"':''}} value="FREE">FREE</option>
                        <option {{isset($row->COST) && $row->COST=='DISC'?'selected="selected"':''}} value="DISC">DISC</option>
                      </select>
                    </td>

                    <td><input {{$ActionStatus}} type="text" name="PER[]" id="PER_{{$key}}" value="{{isset($row->PER)?$row->PER:''}}" class="form-control"  autocomplete="off" onkeypress="return isNumberDecimalKey(event,this)" {{isset($row->COST) && $row->COST=='FREE'?'readonly':''}}   /></td>
                    
                    <td>
                      <select {{$ActionStatus}} name="PERCENTAGE_BASED_ON[]" id="PERCENTAGE_BASED_ON_{{$key}}" class="form-control" autocomplete="off">
                        <option value="">Select</option>
                        <option {{isset($row->PER_BASE) && $row->PER_BASE=='NA'?'selected="selected"':''}} value="NA">NA</option>
                        <option {{isset($row->PER_BASE) && $row->PER_BASE=='DP'?'selected="selected"':''}} value="DP">DP</option>
                        <option {{isset($row->PER_BASE) && $row->PER_BASE=='CP'?'selected="selected"':''}} value="CP">CP</option>
                        <option {{isset($row->PER_BASE) && $row->PER_BASE=='MRP'?'selected="selected"':''}} value="MRP">MRP</option>
                        <option {{isset($row->PER_BASE) && $row->PER_BASE=='MSP'?'selected="selected"':''}} value="MSP">MSP</option>
                      </select>
                    </td>
                    
                    <td align="center" >
                      <button {{$ActionStatus}} class="btn add material" title="add" data-toggle="tooltip" type="button" ><i class="fa fa-plus"></i></button>
                      <button {{$ActionStatus}} class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button" ><i class="fa fa-trash" ></i></button>
                    </td>
                  
                  </tr>
                  
                  @endforeach 
                  @endif
							  </tbody>
					    </table>
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

<div id="ITEMIDpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:90%">
    <div class="modal-content" >
      <div class="modal-header"><button type="button" class="close" data-dismiss="modal" id='ITEMID_closePopup' >&times;</button></div>
      <div class="modal-body">
	      <div class="tablename"><p>Item Details</p></div>
	      <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
          <table id="ItemIDTable" class="display nowrap table  table-striped table-bordered" style="width:100%" >
            <thead>
              <tr id="none-select" class="searchalldata" hidden>
                <td> 
                  <input type="hidden" id="hdn_ItemID"/>
                </td>
              </tr>

              <tr>
                <th style="width:9%;" id="all-check">Select</th>
                <th style="width:9%;">Item Code</th>
                <th style="width:10%;">Name</th>
                <th style="width:9%;">Main UOM</th>
                <th style="width:9%;">Rate</th>
                <th style="width:9%;">Item Group</th>
                <th style="width:9%;">Item Category</th>
                <th style="width:9%;">Business Unit</th>
                <th style="width:9%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</th>
                <th style="width:9%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</th>
                <th style="width:9%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <th style="width:9%;text-align:center;">&#10004;</th>
                <td style="width:9%;"><input type="text" id="Itemcodesearch" class="form-control" autocomplete="off" onkeyup="searchItem(event)"></td>
                <td style="width:10%;"><input type="text" id="Itemnamesearch" class="form-control" autocomplete="off" onkeyup="searchItem(event)"></td>
                <td style="width:9%;"><input type="text" id="ItemUOMsearch" class="form-control" autocomplete="off" onkeyup="searchItem(event)"></td>
                <td style="width:9%;"><input type="text" id="ItemQTYsearch" class="form-control" autocomplete="off" readonly></td>
                <td style="width:9%;"><input type="text" id="ItemGroupsearch" class="form-control" autocomplete="off" onkeyup="searchItem(event)"></td>
                <td style="width:9%;"><input type="text" id="ItemCategorysearch" class="form-control" autocomplete="off" onkeyup="searchItem(event)"></td>
                <td style="width:9%;"><input type="text" id="ItemBUsearch" class="form-control" autocomplete="off" onkeyup="searchItem(event)"></td>
                <td style="width:9%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemAPNsearch" class="form-control" autocomplete="off" onkeyup="searchItem(event)"></td>
                <td style="width:9%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemCPNsearch" class="form-control" autocomplete="off" onkeyup="searchItem(event)"></td>
                <td style="width:9%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemOEMPNsearch" class="form-control" autocomplete="off" onkeyup="searchItem(event)"></td>
              </tr>
            </tbody>
          </table>

          <table id="ItemIDTable2" class="display nowrap table  table-striped table-bordered" style="width:100%" >
            <thead id="thead2"></thead>
            <tbody id="tbody_ItemID">
              <div class="loader" style="display:none;"></div>
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

/*================================== BUTTON FUNCTION ================================*/
$('#btnAdd').on('click', function() {
  var viewURL = '{{route("master",[$FormId,"add"])}}';
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
  window.location.href = '{{route("master",[$FormId,"index"]) }}';
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
    url:'{{ route("master",[$FormId,"update"])}}',
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
    url:'{{ route("master",[$FormId,"Approve"])}}',
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
 
  if($.trim($("#SCHEME_NO").val()) ===""){
    $("#FocusId").val('SCHEME_NO');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please enter Scheme No.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($.trim($("#SCHEME_DATE").val()) ===""){
    $("#FocusId").val('SCHEME_DATE');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select scheme date.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($.trim($("#SCHEME_NAME").val()) ===""){
    $("#FocusId").val('SCHEME_NAME');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select scheme name.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($.trim($("#EFF_FROM_DATE").val()) ===""){
    $("#FocusId").val('EFF_FROM_DATE');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select scheme date.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($.trim($("#EFF_TO_DATE").val()) ===""){
    $("#FocusId").val('EFF_TO_DATE');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select scheme date.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($.trim($("#MAIN_ITEMID_REF").val()) ===""){
    $("#FocusId").val('MAIN_ITEMID_NAME');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select item.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }  
  else if($.trim($("#MAIN_QTY").val()) ===""){
    $("#FocusId").val('MAIN_QTY');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please enter qty.');
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
    var focustext   = "";
      
    $('#example2').find('.participantRow').each(function(){

      if($.trim($(this).find("[id*=ITEMID_REF]").val())!=""){
        allblank1.push('true');

        if($.trim($(this).find("[id*=MAIN_UOMID_REF]").val())!=""){
          allblank2.push('true');

          if($.trim($(this).find('[id*="QTY"]').val()) != "" && $.trim($(this).find('[id*="QTY"]').val()) > 0.000 ){
            allblank3.push('true');
          }
          else{
            allblank3.push('false');
            focustext = $(this).find("[id*=QTY]").attr('id');
          }  

          if($.trim($(this).find('[id*="PER_"]').val()) != "" ){
            allblank4.push('true');
          }
          else{
            allblank4.push('false');
            focustext = $(this).find("[id*=PER_]").attr('id');
          }  

          if($.trim($(this).find('[id*="PERCENTAGE_BASED_ON_"]').val()) != "" ){
            allblank5.push('true');
          }
          else{
            allblank5.push('false');
            focustext = $(this).find("[id*=PERCENTAGE_BASED_ON_]").attr('id');
          }  

          if($.trim($(this).find('[id*="COST"]').val()) !="FREE" && $.trim($(this).find('[id*="PERCENTAGE_BASED_ON_"]').val()) === "NA" ){
            allblank6.push('false');
            focustext = $(this).find("[id*=PERCENTAGE_BASED_ON_]").attr('id');
          }
          else if($.trim($(this).find('[id*="COST"]').val()) ==="FREE" && $.trim($(this).find('[id*="PERCENTAGE_BASED_ON_"]').val()) != "NA" ){
            allblank6.push('false');
            focustext = $(this).find("[id*=PERCENTAGE_BASED_ON_]").attr('id');
          }
          else{
            allblank6.push('true');
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
    else if(jQuery.inArray("false", allblank3) !== -1){
      $("#FocusId").val(focustext);
      $("#alert").modal('show');
      $("#AlertMessage").text('Qty cannot be zero or blank in material tab.');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank4) !== -1){
      $("#FocusId").val(focustext);
      $("#alert").modal('show');
      $("#AlertMessage").text('Please enter percentage in material tab.');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank5) !== -1){
      $("#FocusId").val(focustext);
      $("#alert").modal('show');
      $("#AlertMessage").text('Please select percentage Bbsed on in material tab.');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank6) !== -1){
      $("#FocusId").val(focustext);
      $("#alert").modal('show');
      $("#AlertMessage").text('NA option use with free option only in material tab.');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
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




  
/*================================== ITEM DETAILS =================================*/

//================================== ITEM DETAILS =================================
function getMainItem(id){
  loadItem();
  $("#HIDDEN_ITEM_FIELD").val('MAIN_ITEMID');
  $('.js-selectall1').prop('checked',false); 
  $("#ITEMIDpopup").show();
}

function getItem(id){
  loadItem();
  $("#HIDDEN_ITEM_FIELD").val('');
  $('#hdn_ItemID').val(id.split('_').pop());  
  $('.js-selectall1').prop('checked',false); 
  $("#ITEMIDpopup").show();
}

function searchItem(e) {
  if(e.which == 13){
    loadItem()
  }
}

function loadItem(){
    var taxstate    = ''; 
    var CODE        = $.trim($("#Itemcodesearch").val()); 
    var NAME        = $.trim($("#Itemnamesearch").val()); 
    var MUOM        = $.trim($("#ItemUOMsearch").val()); 
    var GROUP       = $.trim($("#ItemGroupsearch").val()); 
    var CTGRY       = $.trim($("#ItemCategorysearch").val()); 
    var BUNIT       = $.trim($("#ItemBUsearch").val()); 
    var APART       = $.trim($("#ItemAPNsearch").val()); 
    var CPART       = $.trim($("#ItemCPNsearch").val()); 
    var OPART       = $.trim($("#ItemOEMPNsearch").val()); 
  
  $("#tbody_ItemID").html('<tr><td colspan="11">Please wait your request is under process ...</td></tr>');

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $.ajax({
    url:'{{route("master",[$FormId,"loadItem"])}}',
    type:'POST',
    data:{'taxstate':taxstate,'CODE':CODE,'NAME':NAME,'MUOM':MUOM,'GROUP':GROUP,'CTGRY':CTGRY,'BUNIT':BUNIT,'APART':APART,'CPART':CPART,'OPART':OPART},
    success:function(data) {
      var html = '';

      if(data.length > 0){
        $.each(data, function(key, value) {
          html +='<tr class="clsitemid">';
          html +='<td style="width:9%;text-align:center;"><input type="checkbox" id="chkId'+key+'"  value="'+key+'" class="js-selectall1"  ></td>';
          html +='<td style="width:9%;">'+value.ICODE+'</td>';
          html +='<td style="width:10%;" id="itemname_'+key+'" >'+value.INAME+'</td>';
          html +='<td style="width:9%;" id="itemuom_'+key+'" >'+value.UOMCODE+'</td>';
          html +='<td style="width:9%;" id="uomqty_'+key+'" >'+value.STDCOST+'</td>';
          html +='<td style="width:9%;" id="irate_'+key+'">'+value.GROUPCODE+'</td>';
          html +='<td style="width:9%;" id="itax_'+key+'">'+value.ICCODE+'</td>';
          html +='<td style="width:9%;">'+value.BUCODE+'</td>';
          html +='<td style="width:9%;" {{$AlpsStatus['hidden']}} >'+value.ALPS_PART_NO+'</td>';
          html +='<td style="width:9%;" {{$AlpsStatus['hidden']}} >'+value.CUSTOMER_PART_NO+'</td>';
          html +='<td style="width:9%;" {{$AlpsStatus['hidden']}} >'+value.OEM_PART_NO+'</td>';
          
          html +='<td hidden>';
          html +='<input type="text" id="uniquerowid_'+key+'" value='+value.ITEMID+' >';
          html +='<input type="text" id="txt_item_id_'+key+'" value='+value.ITEMID+' >';
          html +='<input type="text" id="txt_item_code_'+key+'" value='+value.ICODE+' >';
          html +='<input type="text" id="txt_item_name_'+key+'" value='+value.INAME+' >';
          html +='<input type="text" id="txt_main_uom_id_'+key+'" value='+value.MAIN_UOMID_REF+' >';
          html +='<input type="text" id="txt_main_uom_code_'+key+'" value='+value.UOMCODE+' >';
          html +='<input type="text" id="txt_item_rate_'+key+'" value='+value.STDCOST+' >';
          html +='</td>';

          html +='</tr>';
        });
      }
      else{
        html +='<tr><td colspan="11"> Record not found.</td></tr>'; 
      }

      $("#tbody_ItemID").html(html);
      
      if($("#HIDDEN_ITEM_FIELD").val() =='MAIN_ITEMID'){
        bindMainItemEvents();
      }
      else{
        bindItemEvents(); 
      }
    },
    error: function (request, status, error) {
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").show();
      $("#AlertMessage").text(request.responseText);
      $("#alert").modal('show');
      $("#OkBtn").focus();
      highlighFocusBtn('activeOk');
      $("#tbody_ItemID").html('');                        
    },
  });
}

function bindMainItemEvents(){
  $('[id*="chkId"]').change(function(){
    var index         = $(this).val();
    var item_id       = $("#txt_item_id_"+index).val();
    var item_code     = $("#txt_item_code_"+index).val();
    var item_name     = $("#txt_item_name_"+index).val();
    var main_uom_id   = $("#txt_main_uom_id_"+index).val();
    var main_uom_code = $("#txt_main_uom_code_"+index).val();

    if($(this).is(":checked") == true){
      $("#MAIN_ITEMID_REF").val(item_id);
      $("#MAIN_ITEMID_NAME").val(item_code);
      $("#MAIN_UOM_REF").val(main_uom_id); 
      $("#ITEM_NAME").val(item_name);
      $("#MAIN_UOM").val(main_uom_code);
    }

    $("#ITEMIDpopup").hide();
  });
  resetItemPopup();
}

$("#ITEMID_closePopup").click(function(event){
  $("#ITEMIDpopup").hide();
  resetItemPopup();
});

function bindItemEvents(){
  $('#ItemIDTable2').off(); 
  $('[id*="chkId"]').change(function(){

    var index         = $(this).val();
    var item_id       = $("#txt_item_id_"+index).val();
    var item_code     = $("#txt_item_code_"+index).val();
    var item_name     = $("#txt_item_name_"+index).val();
    var main_uom_id   = $("#txt_main_uom_id_"+index).val();
    var main_uom_code = $("#txt_main_uom_code_"+index).val();
    var item_rate     = $("#txt_item_rate_"+index).val();
    var uniquerowid   = $("#uniquerowid_"+index).val();
    var row_id        = $('#hdn_ItemID').val();

    if($(this).is(":checked") == true){

      var checkExist  = false;
      $('#example2').find('.participantRow').each(function(){
        if(uniquerowid == $(this).find('[id*="ITEMID_REF_"]').val()){
          checkExist  = true;
        }            
      });

      if(checkExist ==true){
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
        return false;  
      }                            
      else{
        $('#ITEMID_REF_'+row_id).val(item_id);
        $('#popupITEMID_'+row_id).val(item_code);
        $('#ItemName_'+row_id).val(item_name);
        $('#MAIN_UOMID_REF_'+row_id).val(main_uom_id);
        $('#popupMUOM_'+row_id).val(main_uom_code);
      }

      $('#hdn_ItemID').val('');
      $("#ITEMIDpopup").hide();

    }

  });
  resetItemPopup();
}

function resetItemPopup(){
  $("#Itemcodesearch").val(''); 
  $("#Itemnamesearch").val(''); 
  $("#ItemUOMsearch").val(''); 
  $("#ItemQTYsearch").val(''); 
  $("#ItemGroupsearch").val(''); 
  $("#ItemCategorysearch").val(''); 
  $("#ItemBUsearch").val(''); 
  $("#ItemAPNsearch").val(''); 
  $("#ItemCPNsearch").val(''); 
  $("#ItemOEMPNsearch").val(''); 
  $("#ItemStatussearch").val(''); 
  $('.remove').removeAttr('disabled'); 
}

/*================================== ADD/REMOVE FUNCTION ==================================*/

$("#Material").on('click', '.add', function() {
  var $tr     = $(this).closest('table');
  var allTrs  = $tr.find('.participantRow').last();
  var lastTr  = allTrs[allTrs.length-1];
  var $clone  = $(lastTr).clone();

  $clone.find('td').each(function(){
    var el  = $(this).find(':first-child');
    var id  = el.attr('id') || null;

    if(id) {
      var i = id.substr(id.length-1);
      var prefix = id.substr(0, (id.length-1));
      el.attr('id', prefix+(+i+1));
    }

  });

  $clone.find('input:text').val('');
  $clone.find('input:hidden').val('');
  $clone.find('[id*="PER_"]').val('0');
  
  $tr.closest('table').append($clone);         
  var rowCount1 = $('#Row_Count1').val();
  rowCount1     = parseInt(rowCount1)+1;
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
    event.preventDefault();
  }
  event.preventDefault();
});

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
  var lastdt = <?php echo json_encode(isset($objResponse->SCHEME_DATE)?$objResponse->SCHEME_DATE:''); ?>;
  var today = new Date(); 
  var sodate = <?php echo json_encode(isset($objResponse->SCHEME_DATE)?$objResponse->SCHEME_DATE:''); ?>;
  $('#SCHEME_DATE').attr('min',lastdt);
  $('#SCHEME_DATE').attr('max',sodate);
});

function resetTab(){
  $('#Material').find('.participantRow').each(function(){
    var rowcount = $(this).closest('table').find('.participantRow').length;
    $(this).find('input:text').val('');
    $(this).find('input:hidden').val('');
    $(this).find('input:checkbox').prop('checked', false);

    if(rowcount > 1){
      $(this).closest('.participantRow').remove();
      rowcount = parseInt(rowcount) - 1;
      $('#Row_Count1').val(rowcount);
    }
  });

  $("#MAIN_ITEMID_NAME").val('');
  $("#MAIN_ITEMID_REF").val('');
  $("#MAIN_UOM").val('');
  $("#MAIN_UOM_REF").val('');
}

function getCost(data){
  var id    = data.id;
  var index = id.split('_').pop();

  if(data.value=='FREE'){
    $("#PER_"+index).prop('readonly',true);
  }
  else{
    $("#PER_"+index).prop('readonly',false);
  }

  $("#PER_"+index).val('0');
}
</script>
@endpush