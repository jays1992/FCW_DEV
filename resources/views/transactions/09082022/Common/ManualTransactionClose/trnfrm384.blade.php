
@extends('layouts.app')
@section('content')
    

    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('transaction',[384,'index'])}}" class="btn singlebt">Manual Transaction Close</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
                        <button class="btn topnavbt" id="btnSave" ><i class="fa fa-floppy-o"></i> Save</button>
                        <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt" id="btnUndo"  ><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-thumbs-o-up"></i> Approved</button>
                        <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                        <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                </div>
            </div>      
    </div><!--topnav-->	
    <!-- multiple table-responsive table-wrapper-scroll-y my-custom-scrollbar -->
<form id="frm_trn_so" onsubmit="return validateForm()"  method="POST" class="needs-validation"  >
    <div class="container-fluid purchase-order-view">
            @csrf
            <div class="container-fluid filter">
                    <div class="inner-form">
                        <div class="row">
                            <div class="col-lg-2 pl"><p>Transaction Type*</p></div>
                            <div class="col-lg-2 pl">
                                <select id="tran_type" name="tran_type" class="form-control " >
                                    <option value="" selected="selected">--Please select--</option>
                                    <option value="PO">Purchase Order</option>
									<option value="IPO">Import Purchase Order</option>
									<option value="SPO">Service Purchase Order</option>
									<option value="BPO">Blanket Purchase Order</option>
                                    <option value="SO" >Sales Order</option>
									<option value="SSO">Service Sales Order</option>
									<option value="OSO">Open Sales Order</option>
                                </select>
                            </div>
                            
                        </div>

                        <div class="row">
                          <div class="col-lg-2 pl"><p>Transaction No*</p></div>
                          <div class="col-lg-2 pl">
                            <input type="text" name="Trans_popup" id="Trans_popup" class="form-control mandatory"  autocomplete="off" readonly/>
                            <input type="hidden" name="TransID_REF" id="TransID_REF" class="form-control" autocomplete="off" />
                            <input type="hidden" name="hdnTransaction" id="hdnTransaction" class="form-control" autocomplete="off" />                                                                 
                          </div>
                        </div>

                        <div class="row">
                          <div class="col-lg-2 pl"><p>Remarks</p></div>
                          <div class="col-lg-6 pl">
                            <input type="text" name="REMARKS" id="REMARKS" class="form-control mandatory"  autocomplete="off" />                                                                
                          </div>
                        </div>

                    </div>
                </div>
    </div>
</form>
@endsection
@section('alert')


<!-- Transaction Dropdown -->
<div id="Transactionpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='TransactionclosePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Transaction Number</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="TransactionTable" class="display nowrap table  table-striped table-bordered">
    <thead>
    <tr>
      <th style="width:10%;text-align:center;">Select</th> 
      <th style="width:20%">Transaction No</th>
      <th style="width:20%">Transaction Date</th>
      <th style="width:30%">Customer / Vendor</th>
	  <th style="width:20%">Reference Number</th>
    </tr>
    </thead>
    <tbody>
      <tr>
        <th style="width:10%;text-align:center;"><span class="check_th">&#10004;</span></th>
        <td style="width:20%"><input type="text" id="trannosearch" class="form-control" onkeyup="TranNoFunction()"></td>
        <td style="width:20%"><input type="text" id="trandatesearch" class="form-control" onkeyup="TranDateFunction()"></td>
        <td style="width:30%"><input type="text" id="custvendsearch" class="form-control" onkeyup="CustVendFunction()"></td>
		<td style="width:20%"><input type="text" id="referencesearch" class="form-control" onkeyup="ReferenceFunction()"></td>
      </tr>
    </tbody>
    </table>
      <table id="TransactionTable2" class="display nowrap table  table-striped table-bordered">
        <thead id="thead2">
        </thead>
        <tbody id="tbody_Transaction">
       
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Bill To Dropdown-->



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

@endsection


@push('bottom-css')
<style>
#custom_dropdown, #frm_trn_so_filter {
    display: inline-table;
    margin-left: 15px;
}
.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
    padding: 7px;
    line-height: 1.42857143;
    vertical-align: top;
    border-top: 1px solid #ddd;
}
.dataTables_wrapper .row:nth-child(1) .col-sm-6:nth-child(2){text-align:right;}
#filtercolumn{color: #555;
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

//UDF Tab Starts
//------------------------

let udftid = "#TransactionTable2";
      let udftid2 = "#TransactionTable";
      let udfheaders = document.querySelectorAll(udftid2 + " th");

      // Sort the table element when clicking on the table headers
      udfheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(udftid, ".clstranid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function TranNoFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("trannosearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("TransactionTable2");
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

  function TranDateFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("trandatesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("TransactionTable2");
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

  function CustVendFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("custvendsearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("TransactionTable2");
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
  
  function ReferenceFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("referencesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("TransactionTable2");
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




    $('#tran_type').on('click',function(event){
        var tran_type = $("#tran_type").val();
        if(tran_type!='')
        {
          $("#tbody_Transaction").html('');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url:'{{route("transaction",[384,"getTransactionNo"])}}',
                type:'POST',
                data:{'tran_type':tran_type},
                success:function(data) {
                  $("#tbody_Transaction").html(data);    
                  bindTransactionNo();      
                  $("#Transactionpopup").show();              
                },
                error:function(data){
                  console.log("Error: Something went wrong.");
                  $("#tbody_Transaction").html('');         
                  $("#Transactionpopup").show();               
                },
            });
        }
         
         event.preventDefault();
      });

      $("#TransactionclosePopup").click(function(event){
        $("#Transactionpopup").hide();
      });

      
    function bindTransactionNo(){

      $('#TransactionTable2').off(); 

      $(".clstranid").click(function(){
        var fieldid = $(this).attr('id');
        var txtval =   $("#txt"+fieldid+"").val();
        var texdesc =  $("#txt"+fieldid+"").data("desc");
        
        $('#Trans_popup').val(texdesc);
        $('#TransID_REF').val(txtval);       

        $("#Transactionpopup").hide();
        $("#trannosearch").val(''); 
        $("#trandatesearch").val(''); 
        $("#custvendsearch").val(''); 
        
       
        event.preventDefault();
      });
    }

      

  //Alt UOM Dropdown Ends


$(document).ready(function(e) {
    // var Material = $("#Material").html(); 
    // $('#hdnmaterial').val(Material);
    
    

    $("#btnSave" ).click(function() {
        var formReqData = $("#frm_trn_so");
        if(formReqData.valid()){
          validateForm();
        }
    });

    function validateForm(){
 
      $("#FocusId").val('');
      var tran_type           =   $.trim($("#tran_type").val());
      var TransID_REF         =   $.trim($("#TransID_REF").val());

      if(tran_type ===""){
          $("#FocusId").val($("#tran_type"));
          $("#ProceedBtn").focus();
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn1").show();
          $("#AlertMessage").text('Please select Transaction Type.');
          $("#alert").modal('show');
          $("#OkBtn1").focus();
          return false;
      }
      else if(TransID_REF ===""){
          $("#FocusId").val($("#TransID_REF"));
          $("#TransID_REF").val('');  
          $("#ProceedBtn").focus();
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn1").show();
          $("#AlertMessage").text('Please select Transaction No.');
          $("#alert").modal('show');
          $("#OkBtn1").focus();
          return false;
      } 
      else{
          event.preventDefault();          
          $("#alert").modal('show');
          $("#AlertMessage").text('Do you want to save to record.');
          $("#YesBtn").data("funcname","fnSaveData");  //set dynamic fucntion name
          $("#YesBtn").focus();
          $("#OkBtn").hide();
          highlighFocusBtn('activeYes');                  
          }

      }
        
    
      $("#YesBtn").click(function(){

        $("#alert").modal('hide');
        var customFnName = $("#YesBtn").data("funcname");
            window[customFnName]();

        }); //yes button



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

    window.fnSaveData = function (){
      //validate and save data
      event.preventDefault();

          var trnsoForm = $("#frm_trn_so");
          var formData = trnsoForm.serialize();
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
      $.ajax({
          url:'{{ route("transaction",[384,"save"])}}',
          type:'POST',
          data:formData,
          success:function(data) {
            
              if(data.errors) {
                  $(".text-danger").hide();

                  if(data.errors.RONO){
                      showError('ERROR_RONO',data.errors.RONO);
                              $("#YesBtn").hide();
                              $("#NoBtn").hide();
                              $("#OkBtn1").show();
                              $("#AlertMessage").text('Please enter correct value in Transaction No.');
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

    

    window.fnUndoYes = function (){
      
      //reload form
      window.location.href = "{{route('transaction',[384,'add'])}}";

   }//fnUndoYes


   window.fnUndoNo = function (){

    

   }//fnUndoNo

   //no button
$("#NoBtn").click(function(){
    $("#alert").modal('hide');
    $("#RONO").focus();
});

//ok button
$("#OkBtn").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn").hide();
    $(".text-danger").hide();
    window.location.href = '{{route("transaction",[384,"index"]) }}';
});

$("#OkBtn1").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn").hide();
    $("#OkBtn1").hide();
    $("#"+$(this).data('focusname')).focus();
    $("#RONO").focus();
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
   


});
</script>

@endpush

