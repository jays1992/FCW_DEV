@extends('layouts.app')
@section('content')

<div class="container-fluid topnav">
    <div class="row">
        <div class="col-lg-2">
        <a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Bank Reconcilation</a>
        </div>

        <div class="col-lg-10 topnav-pd">
                <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
                <button class="btn topnavbt" id="btnSaveSE" disabled="disabled"><i class="fa fa-floppy-o" ></i> Save</button>
                <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                <button class="btn topnavbt" id="btnPrint" ><i class="fa fa-print"></i> Print</button>
                <button class="btn topnavbt" id="btnUndo"  disabled="disabled"><i class="fa fa-undo"></i> Undo</button>
                <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                <button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-thumbs-o-up"></i> Approved</button>
                <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
        </div>
    </div>
</div>

<form id="transaction_form" onsubmit="return validateForm()"  method="POST" class="needs-validation"  >  
  <div class="container-fluid filter">
	  <div class="inner-form">

		  <div class="row">
			  <div class="col-lg-1 pl"><p>Doc No</p></div>
			  <div class="col-lg-2 pl">
          <input  type="hidden" name="BANK_RECONCILEID" id="BANK_RECONCILEID" value="{{isset($objResponse->BANK_RECONCILEID) && $objResponse->BANK_RECONCILEID !=''?$objResponse->BANK_RECONCILEID:''}}" class="form-control mandatory"  >
          <input {{$ActionStatus}} type="text" name="DOC_NO" id="DOC_NO" value="{{isset($objResponse->BANK_RECONCILE_CODE) && $objResponse->BANK_RECONCILE_CODE !=''?$objResponse->BANK_RECONCILE_CODE:''}}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase"  >
          <span class="text-danger" id="ERROR_DOC_NO"></span>
			  </div>
			
			  <div class="col-lg-1 pl"><p>Doc Date</p></div>
			  <div class="col-lg-2 pl">
				  <input {{$ActionStatus}} type="date" name="DOC_DT" id="DOC_DT"  value="{{isset($objResponse->BANK_RECONCILE_DATE) && $objResponse->BANK_RECONCILE_DATE !=''?$objResponse->BANK_RECONCILE_DATE:''}}" class="form-control"  placeholder="dd/mm/yyyy" >
        </div>

        <div class="col-lg-1 pl"><p>Bank</p></div>
			  <div class="col-lg-2 pl">
          <input {{$ActionStatus}} type="text" name="TEXT_BID_REF" id="TEXT_BID_REF" value="{{isset($objResponse->BANK_NAME) && $objResponse->BANK_NAME !=''?$objResponse->BANK_NAME:''}}" class="form-control" autocomplete="off" readonly onclick="showHideModal('show')" >
          <input type="hidden" name="BID_REF" id="BID_REF"  value="{{isset($objResponse->CASH_BANK_ID) && $objResponse->CASH_BANK_ID !=''?$objResponse->CASH_BANK_ID:''}}" class="form-control" autocomplete="off" >
          <input type="hidden" name="GLID_REF" id="GLID_REF" value="{{isset($objResponse->GLID_REF) && $objResponse->GLID_REF !=''?$objResponse->GLID_REF:''}}" class="form-control" autocomplete="off" >
			  </div>

        <div class="col-lg-1 pl"><p>Mode</p></div>
			  <div class="col-lg-2 pl">
          <select {{$ActionStatus}} name="MODE" id="MODE" class="form-control" autocomplete="off" onchange="getDataArray()" >
            <option {{isset($objResponse->BANK_RECONCILE_MODE) && $objResponse->BANK_RECONCILE_MODE =='ALL'?'selected="selected"':''}} value="ALL">ALL</option>
            <option {{isset($objResponse->BANK_RECONCILE_MODE) && $objResponse->BANK_RECONCILE_MODE =='NON RECONCILE'?'selected="selected"':''}} value="NON RECONCILE">NON RECONCILE</option>
          </select>
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
					  <div class="table-responsive table-wrapper-scroll-y" style="height:350px;margin-top:10px;" >
						  <table id="example2" class="display nowrap table table-striped table-bordered itemlist w-200" width="100%" style="height:auto !important;">
							  <thead id="thead1"  style="position: sticky;top: 0">
								  <tr>
                    <th>Date</th>
                    <th>Doc No</th>
                    <th>Particular</th>
                    <th>Voucher Type</th>
                    <th>Transaction Type</th>
                    <th>Instrument Number </th>
                    <th>Instrument Date</th>
                    <th>Bank Date</th>
                    <th>Debit Amount</th>
                    <th>Credit Amount</th>
								  </tr>
							  </thead>
							  <tbody id="material_body">
							  </tbody>
					    </table>
					  </div>
                  
            <table class="display nowrap table table-striped table-bordered itemlist w-200"  style="margin-top:10px;width:30%;float:right;">
              <thead>
                <tr>
                  <th style="width:50%;text-align:left;background-color: #eee;">Balance as per company book</th>
                  <th id="TEXT_COMPANY_BL" style="width:40%;text-align:right;background-color: #eee;"></th>
                  <th id="TEXT_COMPANY_BL_CRDR" style="width:10%;background-color: #eee;"></th>
                </tr>
                <tr>
                  <th style="width:50%;text-align:left;background-color: #eee;">Balance as per Bank</th>
                  <th id="TEXT_BANK_BL" style="width:40%;text-align:right;background-color: #eee;"></th>
                  <th id="TEXT_BANK_BL_CRDR" style="width:10%;background-color: #eee;"></th>
                </tr>
                <tr>
                  <th style="width:50%;text-align:left;background-color: #eee;">Diff Amount</th>
                  <th id="TEXT_TOTAL_DF" style="width:40%;text-align:right;background-color: #eee;"></th>
                  <th id="TEXT_TOTAL_DF_CRDR" style="width:10%;background-color: #eee;"></th>
                </tr>
              </thead>
            </table>

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


<div id="BANK_MODAL" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:80%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" onclick="showHideModal('hide')" >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Bank Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="BANK_TABLE1" class="display nowrap table  table-striped table-bordered" >
    <thead>
    <tr>
      <th style="width:10%;text-align:center;">Select</th> 
      <th style="width:10%;">Bank Code</th>
      <th style="width:20%;">Bank Name</th>
      <th style="width:20%;">IFSC Code</th>
      <th style="width:20%;">Account No</th>
      <th style="width:20%;">Account Type</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <th style="width:10%;text-align:center;"><span class="check_th">&#10004;</span></th>
        <td style="width:10%;"><input type="text" id="search_item_1" class="form-control" autocomplete="off" onkeyup="searchModalItem(this.id,'BANK_TABLE2','1')"></td>
        <td style="width:20%;"><input type="text" id="search_item_2" class="form-control" autocomplete="off" onkeyup="searchModalItem(this.id,'BANK_TABLE2','2')"></td>
        <td style="width:20%;"><input type="text" id="search_item_3" class="form-control" autocomplete="off" onkeyup="searchModalItem(this.id,'BANK_TABLE2','3')"></td>
        <td style="width:20%;"><input type="text" id="search_item_4" class="form-control" autocomplete="off" onkeyup="searchModalItem(this.id,'BANK_TABLE2','4')"></td>
        <td style="width:20%;"><input type="text" id="search_item_5" class="form-control" autocomplete="off" onkeyup="searchModalItem(this.id,'BANK_TABLE2','5')"></td>
      </tr>
    </tbody>
    </table>
      <table id="BANK_TABLE2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2">          
        </thead>
        <tbody style="font-size:13px;">
        @foreach ($bankMaster as $key=>$val)
        <tr>
          <td style="width:10%;text-align:center;"> <input type="checkbox" name="SELECT_BID_REF[]" class="checkbox" value="{{ $val-> BID }}" onChange="bindBank(this.value,'{{ $val-> NAME }} - {{ $val-> ACNO }}','{{ $val-> GLID_REF }}')" ></td>
          <td style="width:10%;">{{ $val-> BCODE }}</td>
          <td style="width:20%;">{{ $val-> NAME }}</td>
          <td style="width:20%;">{{ $val-> IFSC }}</td>
          <td style="width:20%;">{{ $val-> ACNO }}</td>
          <td style="width:20%;">{{ $val-> ACTYPE }}</td>
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
@endsection

@push('bottom-css')
<style>
.text-danger{
  color:red !important;
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

$('#btnAdd').on('click', function() {
  window.location.href='{{route("transaction",[$FormId,"add"])}}';
});

$('#btnExit').on('click', function() {
  window.location.href='{{route('home')}}';
});

$("#btnUndo").on("click", function(){
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

$("#YesBtn").click(function(){
  $("#alert").modal('hide');
  var customFnName = $("#YesBtn").data("funcname");
  window[customFnName]();
});

$("#btnSaveSE" ).click(function() {
  var formReqData = $("#transaction_form");
  if(formReqData.valid()){
    validateForm('fnSaveData');
  }
});

$( "#btnApprove" ).click(function() {
    var formReqData = $("#transaction_form");
    if(formReqData.valid()){
      validateForm('fnApproveData');
    }
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
  var FocusId = $("#FocusId").val();
  $("#"+FocusId).focus();
  $("#closePopup").click();
}

function highlighFocusBtn(pclass){
  $(".activeYes").hide();
  $(".activeNo").hide();
  $("."+pclass+"").show();
}

function validateForm(actionType){

  var BANK_DATE =  '';
  $('#example2').find('.participantRow').each(function(){
    if($(this).find("[id*=BANK_DATE]").val() !=""){
      BANK_DATE = $(this).find("[id*=BANK_DATE]").val();
    }
  });

  var DOC_NO  = $.trim($("#DOC_NO").val());
  var DOC_DT  = $.trim($("#DOC_DT").val());
  var BID_REF = $.trim($("#BID_REF").val());
  var MODE    = $.trim($("#MODE").val());
  
  if(DOC_NO ===""){
    $("#FocusId").val('DOC_NO');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Doc No is required.');
    $("#alert").modal('show')
    $("#OkBtn1").focus();
  }
  else if(DOC_DT ===""){
    $("#FocusId").val('DOC_DT');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Doc Date is required.');
    $("#alert").modal('show')
    $("#OkBtn1").focus();
  }
  else if(BID_REF ===""){
    $("#FocusId").val('TEXT_BID_REF');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Bank is required.');
    $("#alert").modal('show')
    $("#OkBtn1").focus();
  } 
  else if(MODE ===""){
    $("#FocusId").val('MODE');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Mode is required.');
    $("#alert").modal('show')
    $("#OkBtn1").focus();
  } 
  else if(BANK_DATE ===""){
    $("#FocusId").val('MODE');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select bank date in material tab.');
    $("#alert").modal('show')
    $("#OkBtn1").focus();
  } 
  else{
    $("#alert").modal('show');
    $("#AlertMessage").text('Do you want to save to record.');
    $("#YesBtn").data("funcname",actionType);
    $("#YesBtn").focus();
    $("#OkBtn").hide();
    highlighFocusBtn('activeYes');
  }
}

window.fnSaveData = function (){
  event.preventDefault();
  var trnFormReq = $("#transaction_form");
  var formData = trnFormReq.serialize();

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $("#btnSaveSE").hide(); 
  $(".buttonload").show(); 
  $("#btnApprove").prop("disabled", true);

  $.ajax({
    url:'{{ route("transaction",[$FormId,"update"])}}',
    type:'POST',
    data:formData,
    success:function(data){
      $(".buttonload").hide(); 
      $("#btnSaveSE").show();   
      $("#btnApprove").prop("disabled", false);
       
      if(data.success) {                   
        console.log("succes MSG="+data.msg);
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").show();
        $("#AlertMessage").text(data.msg);
        $(".text-danger").hide();
        $("#alert").modal('show');
        $("#OkBtn").focus(); 
      }
      else{                   
        console.log("succes MSG="+data.msg);
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text(data.msg);
        $(".text-danger").hide();
        $("#alert").modal('show');
        $("#OkBtn1").focus();
      }  
    },
    error:function(data){
      $(".buttonload").hide(); 
      $("#btnSaveSE").show();   
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

window.fnApproveData = function (){
event.preventDefault();
      var trnFormReq = $("#transaction_form");
      var formData = trnFormReq.serialize();
  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });
  $("#btnApprove").hide(); 
  $(".buttonload_approve").show();  
  $("#btnSaveSE").prop("disabled", true);
  $.ajax({
      url:'{{ route("transactionmodify",[$FormId,"Approve"])}}',
      type:'POST',
      data:formData,
      success:function(data) {
        $("#btnApprove").show();  
        $(".buttonload_approve").hide();  
        $("#btnSaveSE").prop("disabled", false);
         
          if(data.success) {                   
              console.log("succes MSG="+data.msg);
              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn").show();
              $("#AlertMessage").text(data.msg);
              $(".text-danger").hide();
              $("#alert").modal('show');
              $("#OkBtn").focus();
          }
          else{                   
              console.log("succes MSG="+data.msg);
              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn1").show();
              $("#AlertMessage").text(data.msg);
              $(".text-danger").hide();
              $("#alert").modal('show');
              $("#OkBtn1").focus();
          }
      },
      error:function(data){
          $("#btnApprove").show();  
          $(".buttonload_approve").hide();  
          $("#btnSaveSE").prop("disabled", false);
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

function showHideModal(type){
  if(type=='show'){
    $("#BANK_MODAL").show();
  }
  else{
    $("#BANK_MODAL").hide();
  }
}

let BANK_TABLE1 = "#BANK_TABLE1";
let BANK_TABLE2 = "#BANK_TABLE2";
let headers     = document.querySelectorAll(BANK_TABLE1 + " th");

headers.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(BANK_TABLE2, ".clsdpid", "td:nth-child(" + (i + 1) + ")");
  });
});

function searchModalItem(search_id,table_id,index_no) {
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

function bindBank(id,desc,glid){
  $('#TEXT_BID_REF').val(desc);
  $('#BID_REF').val(id);
  $('#GLID_REF').val(glid);
  $('.checkbox').prop('checked', false);
  showHideModal('hide');
  getDataArray();
  event.preventDefault();
}

/* function getDataArray(){
  var MODE              = $("#MODE").val();
  var BID_REF           = $("#BID_REF").val();
  var GLID_REF          = $("#GLID_REF").val();
  var BANK_RECONCILEID  = "{{isset($objResponse->BANK_RECONCILEID) && $objResponse->BANK_RECONCILEID !=''?$objResponse->BANK_RECONCILEID:''}}";
  var ACTION_TYPE       = "VIEW";
  var DOC_DT			= $("#DOC_DT").val();

  $("#material_body").html('');

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  })

  $.ajax({
    url:'{{route("transaction",[$FormId,"getDataArray"])}}',
    type:'POST',
    data:{MODE:MODE,BID_REF:BID_REF,GLID_REF:GLID_REF,BANK_RECONCILEID:BANK_RECONCILEID,ACTION_TYPE:ACTION_TYPE,DOC_DT:DOC_DT},
    success:function(data) {
      $("#material_body").html(data);

      var TEXT_COMPANY_BL       = parseFloat($("#TOTAL_DF").val()).toFixed(2);
      var TEXT_BANK_BL          = parseFloat('0.00').toFixed(2)
      var TEXT_TOTAL_DF         = parseFloat($("#TOTAL_DF").val()).toFixed(2);
      var TEXT_COMPANY_BL_CRDR  = TEXT_COMPANY_BL >= 0 ?'CR':'DR';
      var TEXT_BANK_BL_CRDR     = TEXT_BANK_BL >= 0 ?'CR':'DR';
      var TEXT_TOTAL_DF_CRDR    = TEXT_TOTAL_DF >= 0 ?'CR':'DR';

      $("#TEXT_COMPANY_BL").text(TEXT_COMPANY_BL);
      $("#TEXT_BANK_BL").text(TEXT_BANK_BL);
      $("#TEXT_TOTAL_DF").text(TEXT_TOTAL_DF);
      $("#TEXT_COMPANY_BL_CRDR").text(TEXT_COMPANY_BL_CRDR);
      $("#TEXT_BANK_BL_CRDR").text(TEXT_BANK_BL_CRDR);
      $("#TEXT_TOTAL_DF_CRDR").text(TEXT_TOTAL_DF_CRDR);
      balanceCalculation();
      
    },
    error:function(data){
      console.log("Error: Something went wrong.");
      $("#material_body").html('');
    },
  });
}

function balanceCalculation(){

  var TOTAL_DEBIT_AMOUNT  = 0;
  var TOTAL_CREDIT_AMOUNT = 0;

  $('#example2').find('.participantRow').each(function(){

    var DEBIT_AMOUNT  = parseFloat($.trim($(this).find("[id*=DEBIT_AMOUNT]").val()));
    var CREDIT_AMOUNT = parseFloat($.trim($(this).find("[id*=CREDIT_AMOUNT]").val()));

    if($(this).find("[id*=BANK_DATE]").val() !=""){
      TOTAL_DEBIT_AMOUNT  = TOTAL_DEBIT_AMOUNT+DEBIT_AMOUNT;
      TOTAL_CREDIT_AMOUNT = TOTAL_CREDIT_AMOUNT+CREDIT_AMOUNT;
    }

  });


  var TOTAL_DF        = parseFloat($("#TOTAL_DF").val()).toFixed(2);
  var DEBITOPENING        = parseFloat($("#DEBIT_OPENING").val()).toFixed(2);
  var CREDITOPENING        = parseFloat($("#CREDIT_OPENING").val()).toFixed(2);
  var BALANCE_OF_BANK = parseFloat(TOTAL_CREDIT_AMOUNT)-parseFloat(TOTAL_DEBIT_AMOUNT)-parseFloat(DEBITOPENING)+parseFloat(CREDITOPENING);
  BALANCE_OF_BANK = parseFloat(BALANCE_OF_BANK).toFixed(2);
  
  var TEXT_TOTAL_DF   = parseFloat(TOTAL_DF)-parseFloat(BALANCE_OF_BANK);
  TEXT_TOTAL_DF = parseFloat(TEXT_TOTAL_DF).toFixed(2);

  $("#TEXT_BANK_BL").text(BALANCE_OF_BANK);
  $("#TEXT_TOTAL_DF").text(TEXT_TOTAL_DF);

  var TEXT_COMPANY_BL_CRDR  = TOTAL_DF >= 0 ?'CR':'DR';
  var TEXT_BANK_BL_CRDR     = BALANCE_OF_BANK >= 0 ?'CR':'DR';
  var TEXT_TOTAL_DF_CRDR    = TEXT_TOTAL_DF >= 0 ?'CR':'DR';

  $("#TEXT_COMPANY_BL_CRDR").text(TEXT_COMPANY_BL_CRDR);
  $("#TEXT_BANK_BL_CRDR").text(TEXT_BANK_BL_CRDR);
  $("#TEXT_TOTAL_DF_CRDR").text(TEXT_TOTAL_DF_CRDR);
} */

function getDataArray(){
  var MODE              = $("#MODE").val();
  var BID_REF           = $("#BID_REF").val();
  var GLID_REF          = $("#GLID_REF").val();
  var BANK_RECONCILEID  = "{{isset($objResponse->BANK_RECONCILEID) && $objResponse->BANK_RECONCILEID !=''?$objResponse->BANK_RECONCILEID:''}}";
  var ACTION_TYPE       = "VIEW";
  var DOC_DT			= $("#DOC_DT").val();

  $("#material_body").html('');

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  })

  $.ajax({
    url:'{{route("transaction",[$FormId,"getDataArray"])}}',
    type:'POST',
    data:{MODE:MODE,BID_REF:BID_REF,GLID_REF:GLID_REF,BANK_RECONCILEID:BANK_RECONCILEID,ACTION_TYPE:ACTION_TYPE,DOC_DT:DOC_DT},
    success:function(data) {
      $("#material_body").html(data);

       var TEXT_COMPANY_BL       = parseFloat($("#TOTAL_DF").val()).toFixed(2);
      var TEXT_BANK_BL          = parseFloat($("#TOTAL_DF2").val()).toFixed(2);
      var TEXT_TOTAL_DF         = parseFloat('0.00').toFixed(2);
      var TEXT_COMPANY_BL_CRDR  = TEXT_COMPANY_BL >= 0 ?'CR':'DR';
      var TEXT_BANK_BL_CRDR     = TEXT_BANK_BL >= 0 ?'CR':'DR';
      var TEXT_TOTAL_DF_CRDR    = TEXT_TOTAL_DF >= 0 ?'CR':'DR';

      $("#TEXT_COMPANY_BL").text(Math.abs(TEXT_COMPANY_BL));
      $("#TEXT_BANK_BL").text(Math.abs(TEXT_BANK_BL));
      $("#TEXT_TOTAL_DF").text(Math.abs(TEXT_TOTAL_DF));
      $("#TEXT_COMPANY_BL_CRDR").text(TEXT_COMPANY_BL_CRDR);
      $("#TEXT_BANK_BL_CRDR").text(TEXT_BANK_BL_CRDR);
      $("#TEXT_TOTAL_DF_CRDR").text(TEXT_TOTAL_DF_CRDR);
      balanceCalculation();
      
    },
    error:function(data){
      console.log("Error: Something went wrong.");
      $("#material_body").html('');
    },
  });
}

function balanceCalculation(){

  var TOTAL_DEBIT_AMOUNT  = 0;
  var TOTAL_CREDIT_AMOUNT = 0;
  var BALANCE_OF_BANK = 0;
  var TEXT_TOTAL_DF=0;

  $('#example2').find('.participantRow').each(function(){

    var DEBIT_AMOUNT  = parseFloat($.trim($(this).find("[id*=DEBIT_AMOUNT]").val()));
    var CREDIT_AMOUNT = parseFloat($.trim($(this).find("[id*=CREDIT_AMOUNT]").val()));

    if($(this).find("[id*=BANK_DATE]").val() !==""){
      TOTAL_DEBIT_AMOUNT  = TOTAL_DEBIT_AMOUNT+DEBIT_AMOUNT;
      TOTAL_CREDIT_AMOUNT = TOTAL_CREDIT_AMOUNT+CREDIT_AMOUNT;
    }

  });

  var TOTAL_DF        	  = parseFloat($("#TOTAL_DF").val()).toFixed(2);
  var TOTAL_DF2       	  = parseFloat($("#TOTAL_DF2").val()).toFixed(2);
  

  if($("#TEXT_BANK_BL_CRDR").text() == 'CR')
  {
	  BALANCE_OF_BANK = (parseFloat(TOTAL_DF2) + parseFloat(TOTAL_CREDIT_AMOUNT)) - parseFloat(TOTAL_DEBIT_AMOUNT); 
  }
  else
  {
	  BALANCE_OF_BANK = (parseFloat(TOTAL_DF2) - parseFloat(TOTAL_DEBIT_AMOUNT)) + parseFloat(TOTAL_CREDIT_AMOUNT); 
  }  
  
  BALANCE_OF_BANK = parseFloat(BALANCE_OF_BANK).toFixed(2);
  $("#TEXT_BANK_BL").text(Math.abs(BALANCE_OF_BANK));
  var TEXT_BANK_BL_CRDR     = BALANCE_OF_BANK >= 0 ?'CR':'DR';
  $("#TEXT_BANK_BL_CRDR").text(TEXT_BANK_BL_CRDR);
  
  if($("#TEXT_COMPANY_BL_CRDR").text() == 'CR' && $("#TEXT_BANK_BL_CRDR").text() == 'CR')
  {
		TEXT_TOTAL_DF   = parseFloat(TOTAL_DF) - parseFloat(BALANCE_OF_BANK);
  }
  else if($("#TEXT_COMPANY_BL_CRDR").text() == 'DR' && $("#TEXT_BANK_BL_CRDR").text() == 'DR' && parseFloat(TOTAL_DF) > parseFloat(BALANCE_OF_BANK))
  {
		TEXT_TOTAL_DF   = parseFloat(BALANCE_OF_BANK) -  parseFloat(TOTAL_DF);
  }
  else if($("#TEXT_COMPANY_BL_CRDR").text() == 'DR' && $("#TEXT_BANK_BL_CRDR").text() == 'DR' && parseFloat(TOTAL_DF) < parseFloat(BALANCE_OF_BANK))
  {
		TEXT_TOTAL_DF   = parseFloat(TOTAL_DF) - parseFloat(BALANCE_OF_BANK);
  }
  else
  {
		TEXT_TOTAL_DF   = parseFloat(TOTAL_DF) - parseFloat(BALANCE_OF_BANK);
  }	  
	  
  TEXT_TOTAL_DF = parseFloat(TEXT_TOTAL_DF).toFixed(2);
  
  
  
  $("#TEXT_TOTAL_DF").text(Math.abs(TEXT_TOTAL_DF));

  var TEXT_COMPANY_BL_CRDR  = TOTAL_DF2 >= 0 ?'CR':'DR'; 
  
  var TEXT_TOTAL_DF_CRDR    = TEXT_TOTAL_DF > 0 ?'CR':'DR';
  
  if(TEXT_TOTAL_DF == 0)
  {
	  TEXT_TOTAL_DF_CRDR = '';
  }

   $("#TEXT_COMPANY_BL_CRDR").text(TEXT_COMPANY_BL_CRDR);
  
  $("#TEXT_TOTAL_DF_CRDR").text(TEXT_TOTAL_DF_CRDR);
}

$(document).ready(function(e) {
  var lastdt  = <?php echo json_encode($objlastdt[0]->BANK_RECONCILE_DATE); ?>;
  var today   = new Date(); 
  var sodate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
  
  $('#DOC_DT').attr('min',lastdt);
  $('#DOC_DT').attr('max',sodate);
  getDataArray();
});
</script>
@endpush