@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
  <div class="row">
    <div class="col-lg-2"><a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Knock Off</a></div>
    
    <div class="col-lg-10 topnav-pd">
      <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
      <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
      <button class="btn topnavbt" id="btnSaveFormData" ><i class="fa fa-floppy-o"></i> Save</button>
      <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
      <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
      <button class="btn topnavbt" id="btnUndo"  ><i class="fa fa-undo"></i> Undo</button>
      <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
      <button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-thumbs-o-up"></i> Approved</button>
      <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
      <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
    </div>
  </div>
</div>
   
<form id="transaction_data_form" method="POST"  >
  <div class="container-fluid purchase-order-view"> 
    @csrf
    <div class="container-fluid filter">

      <div class="row">
        <div class="col-lg-1 pl"><p>Knockoff No</p></div>
        <div class="col-lg-2 pl">
          @if(isset($objSON->SYSTEM_GRSR) && $objSON->SYSTEM_GRSR == "1")
            <input type="text" name="KNOCKOFF_NO" id="KNOCKOFF_NO" value="{{ isset($objDataNo)?$objDataNo:'' }}" value class="form-control mandatory" readonly  autocomplete="off"  style="text-transform:uppercase"  >
          @elseif(isset($objSON->MANUAL_SR) && $objSON->MANUAL_SR == "1")
            <input type="text" name="KNOCKOFF_NO" id="KNOCKOFF_NO" value="{{ old('KNOCKOFF_NO') }}" class="form-control mandatory" maxlength="{{$objSON->MANUAL_MAXLENGTH}}" autocomplete="off" style="text-transform:uppercase"  >
          @else
            <input type="text" name="MRS_NO" id="MRS_NO"  class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase" >
          @endif
        </div>
        
        <div class="col-lg-1 pl"><p>Knockoff Date</p></div>
        <div class="col-lg-2 pl">
          <input type="date" name="KNOCKOFF_DT" id="KNOCKOFF_DT" value="{{date('Y-m-d')}}" class="form-control mandatory"  placeholder="dd/mm/yyyy" >
        </div>

        <div class="col-lg-1 pl"><p>Vendor</p></div>
        <div class="col-lg-1 pl">
          <input type="radio" name="VENDOR_CUSTOMER" value="VENDOR" onchange="changeVendorCustomer()" checked  />
        </div>

        <div class="col-lg-1 pl"><p>Customer</p></div>
        <div class="col-lg-1 pl">
          <input type="radio" name="VENDOR_CUSTOMER" value="CUSTOMER" onchange="changeVendorCustomer()"  />
        </div>

		  </div>  

      <div class="row">
        
        <div class="col-lg-1 pl"><p>Customer/Vendor</p></div> 
        <div class="col-lg-2 pl">
          <input type="text" name="TXT_VID_CID_REF" id="TXT_VID_CID_REF" class="form-control"  readonly  />
          <input type="hidden" name="VID_CID_REF" id="VID_CID_REF"  class="form-control " />
          <input type="hidden" name="hdnKnowkOff" id="hdnKnowkOff" />
        </div>

        <div class="col-lg-1 pl"><p>Amount</p></div>
        <div class="col-lg-2 pl">
            <input type="text" name="tot_amt1" id="tot_amt1" class="form-control"  autocomplete="off" readonly  />
        </div>
      </div>
     
      <div class="row" id="KnowkOff" >

        <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:400px;"  >
          <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
            <thead id="thead1"  style="position: sticky;top: 0">                           
              <tr>
                <th>Select</th>
                <th>Document Type</th>
                <th>Document No</th>
                <th>Document Date</th>
                <th>Amount</th>
                <th>Balance Amount</th>
                <th>Amount Reconcillation</th>
              </tr>
            </thead>
                     
            <tbody id="knowkoff_data">
              <tr class="participantRow">
                <td><input type="checkbox"  name="DOC_ID[]"         id="DOC_ID_0"       class="clssQCPID"     disabled ></td>
                <td><input type="text"      name="DOC_TYPE_0"       id="DOC_TYPE_0"     class="form-control"  autocomplete="off" readonly /></td>
                <td><input type="text"      name="DOC_NO_0"         id="DOC_NO_0"       class="form-control"  autocomplete="off" readonly /></td>
                <td><input type="text"      name="DOC_DT_0"         id="DOC_DT_0"       class="form-control"  autocomplete="off" readonly /></td>
                <td><input type="text"      name="AMOUNT_0"         id="AMOUNT_0"       class="form-control"  autocomplete="off" readonly /></td>
                <td><input type="text"      name="BAL_AMOUNT_0"     id="BAL_AMOUNT_0"   class="form-control"  autocomplete="off" readonly ></td>
                <td><input type="text"      name="RECONCIL_AMT_0"   id="RECONCIL_AMT_0" class="form-control"  autocomplete="off" readonly /></td>
              </tr>
            </tbody>
          </table>
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

<div id="Custpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:60%">
    <div class="modal-content">
      <div class="modal-header"><button type="button" class="close" data-dismiss="modal" id='CustclosePopup' >&times;</button></div>
      <div class="modal-body">
	    <div class="tablename"><p>Customer / Vendor</p></div>
	    <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
        <table id="CustTable" class="display nowrap table  table-striped table-bordered" width="100%">
          <thead>
            <tr>
              <th style="width:10%;">Select</th>
              <th style="width:30%;">Code</th>
              <th style="width:60%;">Name</th>
            </tr>
          </thead>

          <tbody>
            <tr>
              <th style="text-align:center; width:10%;">&#10004;</th>
              <td style="width:30%;"><input type="text" id="Custcodesearch" class="form-control"  onkeyup="CustomerCodeFunction()"></td>
              <td  style="width:60%;"><input type="text" id="Custnamesearch" class="form-control" onkeyup="CustomerNameFunction()"></td>
            </tr>
          </tbody>
        </table>

        <table id="CustTable2" class="display nowrap table  table-striped table-bordered" width="100%">
          <thead id="thead2"></thead>
          <tbody id="tbody_Cust" style="font-size:13px;">
       
          </tbody>
        </table>
      </div>
		  <div class="cl"></div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('bottom-scripts')
<script>
$('#btnAdd').on('click', function(){
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

$("#YesBtn").click(function(){
  $("#alert").modal('hide');
  var customFnName = $("#YesBtn").data("funcname");
  window[customFnName]();
});

$("#NoBtn").click(function(){
  $("#alert").modal('hide');
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

function showAlert(msg){
  $("#alert").modal('show');
  $("#AlertMessage").text(msg);
  $("#YesBtn").hide(); 
  $("#NoBtn").hide();  
  $("#OkBtn1").show();
  $("#OkBtn1").focus();
  highlighFocusBtn('activeOk');
}

$(document).ready(function(e){
  var KnowkOff = $("#KnowkOff").html(); 
  $('#hdnKnowkOff').val(KnowkOff);
});

//================================SHORTING================================
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

//================================VENDOR CUSTOMER================================
let cltids = "#CustTable2";
let cltids2 = "#CustTable";
let custheaders = document.querySelectorAll(cltids2 + " th");

custheaders.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(cltids, ".clsglid", "td:nth-child(" + (i + 1) + ")");
  });
});

function CustomerCodeFunction(){
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("Custcodesearch");
  filter = input.value.toUpperCase();
        
  if(filter.length == 0){
      var CODE = ''; 
      var NAME = ''; 
      loadCustomer(CODE,NAME); 
  }
  else if(filter.length >= 3){
    var CODE = filter; 
    var NAME = ''; 
    loadCustomer(CODE,NAME); 
  }
  else{
      table = document.getElementById("CustTable2");
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

function CustomerNameFunction() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("Custnamesearch");
    filter = input.value.toUpperCase();
    if(filter.length == 0){
      var CODE = ''; 
      var NAME = ''; 
      loadCustomer(CODE,NAME);
    }
    else if(filter.length >= 3){
      var CODE = ''; 
      var NAME = filter; 
      loadCustomer(CODE,NAME);  
    }
    else{
      table = document.getElementById("CustTable2");
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
    
function loadCustomer(CODE,NAME){
  
  var VENDOR_CUSTOMER = $('input[name="VENDOR_CUSTOMER"]:checked').val();  

  $("#tbody_Cust").html('');
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $.ajax({
    url:'{{route("transaction",[$FormId,"getCustomerVendor"])}}',
    type:'POST',
    data:{'CODE':CODE,'NAME':NAME,'VENDOR_CUSTOMER':VENDOR_CUSTOMER},
    success:function(data) {
      $("#tbody_Cust").html(data); 
      bindCustomerVendor(); 
      showSelectedCheck($("#VID_CID_REF").val(),"SELECT_VID_CID_REF");

    },
    error:function(data){
    console.log("Error: Something went wrong.");
    $("#tbody_Cust").html('');                        
    },
  });
}


$('#TXT_VID_CID_REF').on('click',function(event){
  
  if($("input:radio[name='VENDOR_CUSTOMER']").is(":checked") == false) {
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Select Vendor / Customer.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else{
    var CODE = ''; 
    var NAME = ''; 
    loadCustomer(CODE,NAME);
    $("#Custpopup").show();    
    event.preventDefault();
  }

});

$("#CustclosePopup").click(function(event){
  $("#Custpopup").hide();
  event.preventDefault();
});

function bindCustomerVendor(){

  $(".clscustid").click(function(){
      var fieldid               =   $(this).attr('id');
      var txtval                =   $("#txt"+fieldid+"").val();
      var texdesc               =   $("#txt"+fieldid+"").data("desc")+'-'+$("#txt"+fieldid+"").data("desc2"); 
     
      $('#TXT_VID_CID_REF').val(texdesc);
      $('#VID_CID_REF').val(txtval);

      getKnowkOffDetails();

      $("#Custpopup").hide();
      $("#Custcodesearch").val(''); 
      $("#Custnamesearch").val(''); 
      event.preventDefault();
  });

}

//================================VALIDATE FORM================================
var formTrans = $("#transaction_data_form").validate();

$( "#btnSaveFormData" ).click(function() {
  if(formTrans.valid()){
    validateForm();
  }
});

function validateForm(){
 
  $("#FocusId").val('');

  var VID_CID_REF = $.trim($("#VID_CID_REF").val());
  var TOTAL_ITEM  = $.trim($("#tot_amt1").val());
  var KNOCKOFF_NO = $.trim($("#KNOCKOFF_NO").val());
  var KNOCKOFF_DT = $.trim($("#KNOCKOFF_DT").val());

  var all_location_id = document.querySelectorAll('input[name="DOC_ID[]"]:checked');
  var aIds = [];
  for(var x = 0, l = all_location_id.length; x < l;  x++){
    aIds.push(all_location_id[x].value);
  }

  if(KNOCKOFF_NO ===""){
    $("#FocusId").val('KNOCKOFF_NO');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Select Knockoff No.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if(KNOCKOFF_DT ===""){
    $("#FocusId").val('KNOCKOFF_DT');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Select Knockoff Date.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($("input:radio[name='VENDOR_CUSTOMER']").is(":checked") == false) {
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Select Vendor / Customer Type.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if(VID_CID_REF ===""){
    $("#FocusId").val('TXT_VID_CID_REF');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Select Customer/Vendor.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if(aIds.length < 1){    
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Select Invoice Record.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if(TOTAL_ITEM != 0){    
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Amount Should Be Zero.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else{
    $("#alert").modal('show');
    $("#AlertMessage").text('Do you want to save to record.');
    $("#YesBtn").data("funcname","fnSaveData");
    $("#OkBtn1").hide();
    $("#OkBtn").hide();
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#YesBtn").focus();
    highlighFocusBtn('activeYes');
  }

}

//================================SAVE DATA================================
window.fnSaveData = function (){

event.preventDefault();

    var trnsoForm = $("#transaction_data_form");
    var formData = trnsoForm.serialize();

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$.ajax({
    url:'{{ route("transaction",[$FormId,"save"])}}',
    type:'POST',
    data:formData,
    success:function(data) {
      
        if(data.errors) {
            $(".text-danger").hide();

            if(data.errors.QIGNO){
                showError('ERROR_QIGNO',data.errors.QIGNO);
                        $("#YesBtn").hide();
                        $("#NoBtn").hide();
                        $("#OkBtn1").show();
                        $("#AlertMessage").text('Please enter correct value in QIJ NO.');
                        $("#alert").modal('show');
                        $("#OkBtn1").focus();
            }
          if(data.country=='norecord') {

            $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn").show();

              $("#AlertMessage").text(data.msg);

              $("#alert").modal('show');
              $("#OkBtn").focus();

          }
          if(data.save=='invalid') {

              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn").show();

              $("#AlertMessage").text(data.msg);

              $("#alert").modal('show');
              $("#OkBtn").focus();

          }
        }
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
        else if(data.cancel) {                   
            console.log("cancel MSG="+data.msg);
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text(data.msg);
            $(".text-danger").hide();
            $("#alert").modal('show');
            $("#OkBtn1").focus();
        }
        else 
        {                   
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

//================================USER DEFINE FUNCTION================================
function changeVendorCustomer(){
  $("#TXT_VID_CID_REF").val('');
  $("#VID_CID_REF").val('');
  $('#KnowkOff').html($('#hdnKnowkOff').val());
}

function getKnowkOffDetails(){

  var VENDOR_CUSTOMER = $('input[name="VENDOR_CUSTOMER"]:checked').val();  
  var VID_CID_REF     = $('#VID_CID_REF').val();

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $.ajax({
    url:'{{route("transaction",[$FormId,"getKnowkOffDetails"])}}',
    type:'POST',
    data:{'VID_CID_REF':VID_CID_REF,'VENDOR_CUSTOMER':VENDOR_CUSTOMER},
    success:function(data) {
      $("#knowkoff_data").html(data); 
    },
    error:function(data){
    console.log("Error: Something went wrong.");
      $("#knowkoff_data").html('');                        
    },
  });

}

function getDocId(id){
  var ROW_ID = id.split('_').pop();

  if($("#DOC_ID_"+ROW_ID).is(":checked") == true){
     $("#RECONCIL_AMT_"+ROW_ID).attr("readonly", false); 
     $("#RECONCIL_AMT_"+ROW_ID).val($("#BAL_AMOUNT_"+ROW_ID).val());
     bindTotalValue();
  }
  else{
    $("#RECONCIL_AMT_"+ROW_ID).attr("readonly", true);
    $("#RECONCIL_AMT_"+ROW_ID).val('');
     bindTotalValue();
  }

}

$("#KnowkOff").on('focusout', "[id*='RECONCIL_AMT_']", function() {
  if($(this).val() != '' && $(this).val() != '.00' && $(this).val() > '0')
  {
    if(intRegex.test($(this).val())){
      $(this).val($(this).val()+'.00');
    }
    var balanceamt = $(this).parent().parent().find('[id*="BAL_AMOUNT_"]').val();
    if(parseFloat($(this).val()) > parseFloat(balanceamt))
    {
      $(this).val('');
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please Amount Reconcillation cannot be greater than Balance Amount.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
    }
  }

    bindTotalValue();
    event.preventDefault();
});

function bindTotalValue(){

  var totalvalue  = 0.00;
  var totalvalue2 = 0.00;
  var tvalue      = 0.00;
  var tvalue2     = 0.00;
 
  $('#KnowkOff').find('.participantRow').each(function(){
    if($(this).find('[id*="RECONCIL_AMT_"]').val() != ''){

      tvalue = $(this).find('[id*="RECONCIL_AMT_"]').val();

      if($(this).find('[id*="DOC_TYPE_"]').val() == 'PURCHASE_INVOICE' 
          || $(this).find('[id*="DOC_TYPE_"]').val() == 'SERVICE_PURCHASE_INVOICE'
          || $(this).find('[id*="DOC_TYPE_"]').val() == 'IMPORT_PURCHASE_INVOICE'
          || $(this).find('[id*="DOC_TYPE_"]').val() == 'AP_CREDIT_NOTE'
          || $(this).find('[id*="DOC_TYPE_"]').val() == 'AP_INVOICE'
          || $(this).find('[id*="DOC_TYPE_"]').val() == 'SALES_INVOICE'
          || $(this).find('[id*="DOC_TYPE_"]').val() == 'AR_DEBIT_NOTE'
		  || $(this).find('[id*="DOC_TYPE_"]').val() == 'MANUAL_JOURNAL_CREDIT' && $('input[name="VENDOR_CUSTOMER"]:checked').val() == 'VENDOR'
		  || $(this).find('[id*="DOC_TYPE_"]').val() == 'MANUAL_JOURNAL_DEBIT' && $('input[name="VENDOR_CUSTOMER"]:checked').val() == 'CUSTOMER'
        )
      {
        totalvalue = parseFloat(parseFloat(totalvalue) + parseFloat(tvalue)).toFixed(2);
      }
      else if($(this).find('[id*="DOC_TYPE_"]').val() == 'PURCHASE_RETURN' 
          || $(this).find('[id*="DOC_TYPE_"]').val() == 'AP_DEBIT_NOTE'
		  || $(this).find('[id*="DOC_TYPE_"]').val() == 'SERVICE_PURCHASE_INVOICE_DEBIT'
          || $(this).find('[id*="DOC_TYPE_"]').val() == 'DEBIT_NOTE_STOCK'
          || $(this).find('[id*="DOC_TYPE_"]').val() == 'SALES_RETURN'
          || $(this).find('[id*="DOC_TYPE_"]').val() == 'AR_CREDIT_NOTE'
          || $(this).find('[id*="DOC_TYPE_"]').val() == 'CREDIT_NOTE_STOCK'
		  || $(this).find('[id*="DOC_TYPE_"]').val() == 'PAYMENT'
		  || $(this).find('[id*="DOC_TYPE_"]').val() == 'RECEIPT'
		  || $(this).find('[id*="DOC_TYPE_"]').val() == 'MANUAL_JOURNAL_CREDIT' && $('input[name="VENDOR_CUSTOMER"]:checked').val() == 'CUSTOMER'
		  || $(this).find('[id*="DOC_TYPE_"]').val() == 'MANUAL_JOURNAL_DEBIT' && $('input[name="VENDOR_CUSTOMER"]:checked').val() == 'VENDOR'
        )
      {
        totalvalue = parseFloat(parseFloat(totalvalue) - parseFloat(tvalue)).toFixed(2);
      }
      else 
      {
      totalvalue = parseFloat(parseFloat(totalvalue) + parseFloat(tvalue)).toFixed(2);
      }
    }
  });

  $('#tot_amt1').val(totalvalue);
}

function isNumberDecimalKey(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57) )
    return false;

    return true;
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

$(document).ready(function(e) {
  var lastdt  = <?php echo json_encode($objlastdt[0]->KNOCKOFF_DT); ?>;
  var today   = new Date(); 
  var maxdate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;

  $('#KNOCKOFF_DT').attr('min',lastdt);
  $('#KNOCKOFF_DT').attr('max',maxdate);

});
</script>
@endpush