@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
  <div class="row">
    <div class="col-lg-2"><a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Dealer Commission</a></div>
    <div class="col-lg-10 topnav-pd">
      <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
      <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
      <button class="btn topnavbt" id="btnSave" ><i class="fa fa-floppy-o"></i> Save</button>
      <button style="display:none" class="btn topnavbt buttonload"> <i class="fa fa-refresh fa-spin"></i> {{Session::get('save')}}</button>
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

<form id="frm_trn_add" onsubmit="return validateForm()"  method="POST" class="needs-validation" >  
  <div class="container-fluid filter">
	  <div class="inner-form">

      <div class="row">
        <div class="col-lg-1 pl"><p>Doc No</p></div>
        <div class="col-lg-2 pl">

          <input type="text" name="DC_NO" id="DC_NO" value="{{$docarray['DOC_NO']}}" {{$docarray['READONLY']}} class="form-control" maxlength="{{$docarray['MAXLENGTH']}}" autocomplete="off" style="text-transform:uppercase" >
          <script>docMissing(@json($docarray['FY_FLAG']));</script>
        </div>
              
        <div class="col-lg-1 pl"><p>Doc Date</p></div>
        <div class="col-lg-2 pl">
          <input type="date" name="DC_DATE" id="DC_DATE" onchange='checkPeriodClosing("{{$FormId}}",this.value,1),getDocNoByEvent("DC_NO",this,@json($doc_req))' class="form-control mandatory" autocomplete="off" >
        </div>

        <div class="col-lg-1 pl"><p>Type</p></div>
        <div class="col-lg-2 pl">
          <select name="DC_TYPE" id="DC_TYPE" class="form-control" autocomplete="off" onchange="getDCType()" >
            <option value="Sales Order">Sales Order</option>
            <option value="Sales Invoice">Sales Invoice</option>
          </select>
        </div>

      </div>

      <div class="row">
       
      <div class="col-lg-1 pl"><p>From Date</p></div>
        <div class="col-lg-2 pl">
          <input type="date" name="FROM_DATE" id="FROM_DATE" class="form-control" autocomplete="off" >
        </div>

        <div class="col-lg-1 pl"><p>To Date</p></div>
        <div class="col-lg-2 pl">
          <input type="date" name="TO_DATE" id="TO_DATE" class="form-control" autocomplete="off" >
        </div>
        
        <div class="col-lg-1 pl"><p>Dealer</p></div>
        <div class="col-lg-2 pl">
          <input type="text" name="DEALER_NAME" id="DEALER_NAME" class="form-control mandatory"  autocomplete="off" readonly/>
          <input type="hidden" name="DEALER_REF" id="DEALER_REF" class="form-control" autocomplete="off" />
        </div>

        <div class="col-lg-1 pl"><p>Remarks</p></div>
        <div class="col-lg-2 pl">
          <input type="text" name="REMARKS" id="REMARKS" class="form-control" autocomplete="off" >
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
						  <table id="example2" class="display nowrap table table-striped table-bordered itemlist w-200" style="height:auto !important;width:50% !important;">
							  <thead id="thead1"  style="position: sticky;top: 0">
								  <tr>
                    <th id='th1'>Sales Order Number</th>
                    <th id='th2'>Sales Order Date</th>
                    <th id='th3'>Sales Order Amount</th>
                    <th>Customer Name</th>
                    <th>Commision Amount</th>
                    <th class="invoice_div" >Invoice</th>
								  </tr>
							  </thead>
							  <tbody id="material_data">
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
<div id="InvoiceModal" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:80%;z-index:1">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='InvoiceModalClose' >&times;</button>
      </div>
      <div class="modal-body">
	      <div class="tablename"><p>Invoice Details</p></div>
        <div class="table-responsive table-wrapper-scroll-y" style="height:280px;margin-top:10px;" >
          <table id="InvoiceTable" class="display nowrap table table-striped table-bordered itemlist w-200" style="width:100%;height:auto !important;">
							  <thead id="thead1"  style="position: sticky;top: 0">
								  <tr>
                    <th>Sales Order Invoice Number</th>
                    <th>Sales Order Invoice Date</th>
                    <th>Invoice Amount</th>
                    <th>Customer Name</th>
								  </tr>
							  </thead>
							  <tbody id="invoice_data">
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
        <td class="ROW2"><input type="text" id="customercodesearch" class="form-control" onkeyup="CustomerCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="customernamesearch" class="form-control" onkeyup="CustomerNameFunction()"></td>
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
@endsection

@push('bottom-css')
<style>
.text-danger{
  color:red !important;
}
#InvoiceTable {
  border-collapse: collapse;
  width: 950px;
  border: 1px solid #ddd;
  font-size: 11px;
}
#InvoiceTable th {
  text-align: center;
  padding: 5px;
  font-size: 11px;
  color: #0f69cc;
  font-weight: 600;
}
#InvoiceTable td {
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
  window.location.href = "{{route('transaction',[$FormId,'add'])}}";
}

$("#btnSave" ).click(function() {
    var formReqData = $("#frm_trn_add");
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
  $("#"+$("#FocusId").val()).focus();
  $("#closePopup").click();
}

function highlighFocusBtn(pclass){
  $(".activeYes").hide();
  $(".activeNo").hide();
  $("."+pclass+"").show();
}

/*================================== Save FUNCTION =================================*/
window.fnSaveData = function (){

  event.preventDefault();

  var trnFormReq  = $("#frm_trn_add");
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
    url:'{{ route("transaction",[$FormId,"save"])}}',
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

/*================================== VALIDATE FUNCTION =================================*/

function validateForm(){

  if($.trim($("#DC_NO").val()) ===""){
    $("#FocusId").val('DC_NO');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select doc no.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($.trim($("#DC_DATE").val()) ===""){
    $("#FocusId").val('DC_DATE');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select doc date.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($.trim($("#DC_TYPE").val()) ===""){
    $("#FocusId").val('DC_TYPE');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select type.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($.trim($("#FROM_DATE").val()) ===""){
    $("#FocusId").val('FROM_DATE');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select From Date.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }  
  else if($.trim($("#TO_DATE").val()) ===""){
    $("#FocusId").val('TO_DATE');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select To Date.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  } 
  else if($.trim($("#DEALER_REF").val()) ===""){
    $("#FocusId").val('DEALER_NAME');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select Dealer.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  } 
  else if(checkPeriodClosing('{{$FormId}}',$("#DC_DATE").val(),0) ==0){
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text(period_closing_msg);
    $("#alert").modal('show');
    $("#OkBtn1").focus();
  } 
  else{
      checkDuplicateCode();
  }
}

/*================================== CHECK DUPLICATE FUNCTION =================================*/
function checkDuplicateCode(){

  var trnFormReq  = $("#frm_trn_add");
  var formData    = trnFormReq.serialize();

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $.ajax({
    url:'{{route("transaction",[$FormId,"codeduplicate"])}}',
    type:'POST',
    data:formData,
    success:function(data) {
      if(data.exists) {
        $("#FocusId").val('DC_NO');
        $("#alert").modal('show');
        $("#AlertMessage").text(data.msg);
        $("#YesBtn").hide(); 
        $("#NoBtn").hide();  
        $("#OkBtn1").show();
        $("#OkBtn1").focus();
        highlighFocusBtn('activeOk');
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

function isNumberDecimalKey(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
    return false;

    return true;
}

/*================================== ONLOAD FUNCTION ==================================*/
$(document).ready(function(e) {
  var lastdt = <?php echo json_encode($objlastdt[0]->DC_DATE); ?>;
  var today = new Date(); 
  var sodate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;

  $('#DC_DATE').attr('min',lastdt);
  $('#DC_DATE').attr('max',sodate);

  var d = new Date(); 
  var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
  $('#DC_DATE').val(today);
});

//CUSTOMER
let cltid     = "#GlCodeTable2";
let cltid2    = "#GlCodeTable";
let clheaders = document.querySelectorAll(cltid2 + " th");

clheaders.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(cltid, ".clsglid", "td:nth-child(" + (i + 1) + ")");
  });
});

function CustomerCodeFunction() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("customercodesearch");
    filter = input.value.toUpperCase();
    
  if(filter.length == 0)
    {
      var CODE = ''; 
      var NAME = ''; 
      loadCustomer(CODE,NAME); 
    }
    else if(filter.length >= 3)
    {
      var CODE = filter; 
      var NAME = ''; 
      loadCustomer(CODE,NAME); 
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

function CustomerNameFunction() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("customernamesearch");
      filter = input.value.toUpperCase();
      if(filter.length == 0)
      {
        var CODE = ''; 
        var NAME = ''; 
        loadCustomer(CODE,NAME);
      }
      else if(filter.length >= 3)
      {
        var CODE = ''; 
        var NAME = filter; 
        loadCustomer(CODE,NAME);  
      }
      else
      {
        table = document.getElementById("GlCodeTable2");
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


$('#DEALER_NAME').click(function(event){

  var DC_TYPE     = $.trim($("#DC_TYPE").val()); 
  var DEALER_REF  = $.trim($("#DEALER_REF").val()); 
  var FROM_DATE   = $.trim($("#FROM_DATE").val()); 
  var TO_DATE     = $.trim($("#TO_DATE").val()); 

  if(DC_TYPE ===""){
    $("#FocusId").val('DC_TYPE');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select type.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if(FROM_DATE ===""){
    $("#FocusId").val('FROM_DATE');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select from date.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if(TO_DATE ===""){
    $("#FocusId").val('TO_DATE');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select to date.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else{
    var CODE = ''; 
    var NAME = ''; 
    loadCustomer(CODE,NAME);
    $("#customer_popus").show();
  }
  event.preventDefault();
});
  
function loadCustomer(CODE,NAME){

    $("#tbody_subglacct").html('');
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $.ajax({
      url:'{{route("transaction",[$FormId,"getsubledger"])}}',
      type:'POST',
      data:{'CODE':CODE,'NAME':NAME},
      success:function(data) {
      $("#tbody_subglacct").html(data); 
      bindSubLedgerEvents(); 
      showSelectedCheck($("#SLID_REF").val(),"SELECT_SLID_REF");

      },
      error:function(data){
      console.log("Error: Something went wrong.");
      $("#tbody_subglacct").html('');                        
      },
    });
}

$("#customer_closePopup").on("click",function(event){ 
    $("#customer_popus").hide();
    $("#customercodesearch").val(''); 
    $("#customernamesearch").val(''); 
   
    event.preventDefault();
});

function bindSubLedgerEvents(){
  $('.clssubgl').click(function(){

      var id      = $(this).attr('id');
      var txtval  = $("#txt"+id+"").val();
      var texdesc = $("#txt"+id+"").data("desc");
    
      $('#DEALER_NAME').val(texdesc);
      $('#DEALER_REF').val(txtval);
      getCommission(txtval);
    

    $("#customer_popus").hide();
    $("#customercodesearch").val(''); 
    $("#customernamesearch").val(''); 
    event.preventDefault();
  });
}

function getCommission(){

  var DC_TYPE     = $.trim($("#DC_TYPE").val()); 
  var DEALER_REF  = $.trim($("#DEALER_REF").val()); 
  var FROM_DATE   = $.trim($("#FROM_DATE").val()); 
  var TO_DATE     = $.trim($("#TO_DATE").val()); 
  var hidden_row  = DC_TYPE ==='Sales Order'?'':"hidden";
 
  $("#material_data").html('<tr><td colspan="6" style="text-align:center;">Please wait your request is under process ...</td></tr>');

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $.ajax({
    url:'{{route("transaction",[$FormId,"getCommission"])}}',
    type:'POST',
    data:{DC_TYPE:DC_TYPE,DEALER_REF:DEALER_REF,FROM_DATE:FROM_DATE,TO_DATE:TO_DATE},
    success:function(data) {
      var html = '';

      if(data.length > 0){
        $.each(data, function(key, value) {
          html +='<tr class="participantRow">';
          html +='<td hidden><input  type="text" name="DOC_ID[]" value="'+value.DOC_ID+'"  /></td>';
          html +='<td><input  type="text" name="DOC_NO[]" value="'+value.DOC_NO+'" class="form-control" autocomplete="off" readonly /></td>';
          html +='<td><input  type="text" name="DOC_DATE[]" value="'+value.DOC_DATE+'" class="form-control" autocomplete="off" readonly /></td>';
          html +='<td><input  type="text" name="AMOUNT[]" value="'+value.AMOUNT+'" class="form-control" autocomplete="off" readonly /></td>';
          html +='<td><input  type="text" name="CUSTOMER_NAME[]" value="'+value.CUSTOMER_NAME+'" class="form-control" autocomplete="off" readonly /></td>';
          html +='<td hidden><input  type="text" name="SLID_REF[]" value="'+value.SLID_REF+'"  /></td>';
          html +='<td><input  type="text" name="COMMISSION_AMOUNT[]" value="'+value.COMMISSION_AMOUNT+'" '+value.COMMISSION_STATUS+' class="form-control" autocomplete="off"/></td>';
          html +='<td align="center" class="invoice_div" '+hidden_row+' ><a class="btn" onclick="getInvoiceDetails('+value.DOC_ID+')"  ><i class="fa fa-file"></i></a></td>';
          html +='</tr>';
        });
      }
      else{
        html +='<tr><td colspan="6" style="text-align:center;">No data available in table</td></tr>';
      }

      $("#material_data").html(html);
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

function getDCType(){
  $("#DEALER_NAME").val('');
  $("#DEALER_REF").val('');
  
  if($("#DC_TYPE").val() ==='Sales Order'){
    $("#th1").text('Sales Order Number');
    $("#th2").text('Sales Order Date');
    $("#th3").text('Sales Order Amount');
    $(".invoice_div").show();
  }
  else{
    $("#th1").text('Sales Invoice Number');
    $("#th2").text('Sales Invoice Date');
    $("#th3").text('Sales Invoice Amount');
    $(".invoice_div").hide();
  }

  $("#material_data").html('<tr><td colspan="6" style="text-align:center;">No data available in table</td></tr>');
}

function getInvoiceDetails(id){
  var DC_TYPE     = $.trim($("#DC_TYPE").val()); 
  $("#invoice_data").html('<tr><td colspan="4" style="text-align:center;">Please wait your request is under process ...</td></tr>');
  $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  $.ajax({
    url:'{{route("transaction",[$FormId,"getInvoiceDetails"])}}',
    type:'POST',
    data:{id:id,DC_TYPE:DC_TYPE},
    success:function(data) {
      var html = '';

      if(data.length > 0){
        $.each(data, function(key, value) {
          html +='<tr class="participantRow">';
          html +='<td hidden><input  type="text" name="INVOICE_DOC_ID[]" value="'+value.DOC_ID+'"  /></td>';
          html +='<td><input  type="text" name="INVOICE_DOC_NO[]" value="'+value.DOC_NO+'" class="form-control" autocomplete="off" readonly /></td>';
          html +='<td><input  type="text" name="INVOICE_DOC_DATE[]" value="'+value.DOC_DATE+'" class="form-control" autocomplete="off" readonly /></td>';
          html +='<td><input  type="text" name="INVOICE_AMOUNT[]" value="'+value.AMOUNT+'" class="form-control" autocomplete="off" readonly /></td>';
          html +='<td><input  type="text" name="INVOICE_CUSTOMER_NAME[]" value="'+value.CUSTOMER_NAME+'" class="form-control" autocomplete="off" readonly /></td>';
          html +='</tr>';
        });
      }
      else{
        html +='<tr><td colspan="4" style="text-align:center;">No data available in table</td></tr>';
      }

      $("#invoice_data").html(html);
    },
    error: function (request, status, error) {
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").show();
      $("#AlertMessage").text(request.responseText);
      $("#alert").modal('show');
      $("#OkBtn").focus();
      highlighFocusBtn('activeOk');
      $("#material_data").html('<tr><td colspan="4" style="text-align:center;">No data available in table</td></tr>');                       
    },
  });

  $("#InvoiceModal").show();
}

$("#InvoiceModalClose").on("click",function(event){ 
  $("#InvoiceModal").hide();
});
</script>
@endpush
