
@extends('layouts.app')
@section('content')
    
    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('transaction',[44,'index'])}}" class="btn singlebt">Sales Invoice (SI)</a>
                </div><!--col-2-->
                <div class="col-lg-10 topnav-pd" id="btndiv">
                        <button class="btn topnavbt" id="btnAdd"  {{isset($objRights->ADD) && $objRights->ADD != 1 ? 'disabled' : ''}} ><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt" id="btnEdit" {{isset($objRights->EDIT) && $objRights->EDIT != 1 ? 'disabled' : ''}} ><i class="fa fa-pencil-square-o"></i> Edit</button>
                        <button class="btn topnavbt" id="btnSave" disabled="disabled"><i class="fa fa-floppy-o"></i> Save</button>
                        <button class="btn topnavbt" id="btnView" {{isset($objRights->VIEW) && $objRights->VIEW != 1 ? 'disabled' : ''}} ><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" id="btnPrint" ><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt" id="btnUndo"  disabled="disabled"><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" {{isset($objRights->CANCEL) && $objRights->CANCEL != 1 ? 'disabled' : ''}} ><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove"{{ (isset($objRights->APPROVAL1) || isset($objRights->APPROVAL2) || isset($objRights->APPROVAL3) || isset($objRights->APPROVAL4) || isset($objRights->APPROVAL5)) &&  ($objRights->APPROVAL1||$objRights->APPROVAL2||$objRights->APPROVAL3||$objRights->APPROVAL4||$objRights->APPROVAL5) == 1 ? '' : 'disabled'}} ><i class="fa fa-thumbs-o-up"></i> Approved</button>
                        <button class="btn topnavbt" id="btnAttach" {{isset($objRights->ATTECHMENT) && $objRights->ATTECHMENT != 1 ? 'disabled' : ''}} ><i class="fa fa-link"></i> Attachment</button>
                        <button class="btn topnavbt" id="btnEinvoice"{{ (isset($objRights->APPROVAL1) || isset($objRights->APPROVAL2) || isset($objRights->APPROVAL3) || isset($objRights->APPROVAL4) || isset($objRights->APPROVAL5)) &&  ($objRights->APPROVAL1||$objRights->APPROVAL2||$objRights->APPROVAL3||$objRights->APPROVAL4||$objRights->APPROVAL5) == 1 ? '' : 'disabled'}} ><i class="fa fa-thumbs-o-up"></i> E-Invoice</button>
                        <button class="btn topnavbt" style="display:none" id="btnEinvoice_tcs"{{ (isset($objRights->APPROVAL1) || isset($objRights->APPROVAL2) || isset($objRights->APPROVAL3) || isset($objRights->APPROVAL4) || isset($objRights->APPROVAL5)) &&  ($objRights->APPROVAL1||$objRights->APPROVAL2||$objRights->APPROVAL3||$objRights->APPROVAL4||$objRights->APPROVAL5) == 1 ? '' : 'disabled'}} ><i class="fa fa-thumbs-o-up"></i> E-Invoice</button>
                        <button class="btn topnavbt" id="btnExit"><i class="fa fa-power-off"></i> Exit</button>
                </div><!--col-10-->
            </div><!--row-->
    </div><!--topnav-->	
    <!-- multiple table-responsive table-wrapper-scroll-y my-custom-scrollbar -->
    <div class="container-fluid purchase-order-view">
        <div class="multiple table-responsive  ">
              <table id="frm_trn_si" class="display nowrap table table-striped table-bordered" width="100%">
            <thead id="thead1">
            <tr>
                <th id="all-check" style="width:50px;"><input type="checkbox" class="js-selectall" data-target=".js-selectall1" />Select</th>
                <th >Sales Invoice No</th>
                <th >Sales Invoice Date</th>
                <th>Customer Name</th>
                <th>Sale challan no</th>
                <th>Sale Invoice total value</th>
                <th>IRN Status</th>
                <th>Eway Bill Status</th>
                <th>Created Date</th>
                <th>Created By</th>
                <th>Status</th>

            </tr>
            </thead>
            <tbody> 
            @if(!empty($objDataList))           
            @foreach($objDataList as $key => $val)
            @php
             $DataStatus= isset($val->STATUS_DESC) && $val->STATUS_DESC!='' ? $val->STATUS_DESC : '';
              if(!Empty($val->STATUS) && $val->STATUS=="A"){ 
                $app_status = 1 ;         
                $DataStatus =  Str::contains(strtolower($val->STATUS_DESC), 'approved')==false ? 'Final Approved' : $val->STATUS_DESC;
              } 
              elseif($val->STATUS=="C"){                 
                $app_status = 2 ;              
                $DataStatus =  Str::contains(strtolower($val->STATUS_DESC), 'cancelled')==false ? 'Cancelled' : $val->STATUS_DESC;
              }
              elseif($val->STATUS=="R"){                 
                $app_status = 3 ;              
                $DataStatus =  Str::contains(strtolower($val->STATUS_DESC), 'closed')==false ? 'Closed' : $val->STATUS_DESC;
              }
              elseif($val->STATUS=="N"){  
                $app_status = 0 ; 
              }
            @endphp
            <tr>
              <td><input type="checkbox" id="chkId{{$val->SIID}}" value="{{$val->SIID}}" class="js-selectall1" data-rcdstatus="{{$app_status}}" data-docdate="{{isset($val->SIDT) && $val->SIDT !='' && $val->SIDT !='1900-01-01' ? date('d-m-Y',strtotime($val->SIDT)):''}}" ></td>
              <td>{{isset($val->SINO) && $val->SINO !=''?$val->SINO:''}}</td>
              <td>{{isset($val->SIDT) && $val->SIDT !='' && $val->SIDT !='1900-01-01' ? date('d-m-Y',strtotime($val->SIDT)):''}}</td>
              <td>{{isset($val->SLNAME) && $val->SLNAME !=''?$val->SLNAME:''}}</td>
              <td>{{isset($val->SCNO) && $val->SCNO !=''?$val->SCNO:''}}</td>
              <td>{{isset($val->TOTAL_AMOUNT) && $val->TOTAL_AMOUNT !=''?$val->TOTAL_AMOUNT:''}}</td>
              <td>{{isset($val->IRN_NO)?$val->IRN_NO:''}}</td>
              <td>{{isset($val->EWAY_BILLNO)?$val->EWAY_BILLNO:''}}</td>
              <td>{{isset($val->INDATE) && $val->INDATE !='' && $val->INDATE !='1900-01-01' ? date('d-m-Y',strtotime($val->INDATE)):''}}</td>
              <td>{{isset($val->CREATED_BY) && $val->CREATED_BY !=''?$val->CREATED_BY:''}}</td>
              <td>
                {{$DataStatus}}
                <input type="hidden" id="FINAL_APPROVAL_{{$val->SIID}}"   value="{{$val->FINAL_APPROVAL}}" >
                <input type="hidden" id="LAST_UPDATE_DATE_{{$val->SIID}}"   value="{{$val->LAST_UPDATE_DATE}}" >
              </td>
            </tr>
            @endforeach 
            @endif
            </tbody>
        </table>
        </div>
    </div><!--purchase-order-view-->
</div>
@endsection
@section('alert')
<div id="alert" class="modal" role="dialog" data-backdrop="static" >
  <div class="modal-dialog"  >
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">System Alert Message</h4>
      </div>
      <div class="modal-body">
      <h5 id="AlertMessage" ></h5>
      <div class="btdiv">    
            <button class="btn alertbt" name='YesBtn' id="YesBtn" data-funcname="fnSaveData">
            <div id="alert-active" class="activeYes"></div>Yes
            </button>
            <button class="btn alertbt" name='NoBtn' id="NoBtn"   data-funcname="fnUndoNo" >
            <div id="alert-active" class="activeNo"></div>No
            </button>
            <button class="btn alertbt" name='OkBtn' id="OkBtn" style="display:none;margin-left: 90px;">
            <div id="alert-active" class="activeOk"></div>OK</button>
            <button class="btn alertbt" name='OkBtn1' id="OkBtn1" style="display:none;margin-left: 90px;">
            <div id="alert-active" class="activeOk1"></div>OK</button>
        </div><!--btdiv-->
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Alert -->
<!-- Print -->
<div id="ReportView" class="modal" role="dialog"  data-backdrop="static"  >
  <div class="modal-dialog modal-md" style="width:90%; height:90%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='ReportViewclosePopup' >&times;</button>          
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Sales Invoice Print</p></div>
        <div class="row">
          <div class="frame-container col-lg-12 pl text-center" >
                <button class="btn topnavbt" id="btnReport">
                    Print
                </button>
                <button class="btn topnavbt" id="btnPdf">
                    PDF
                </button>
                <button class="btn topnavbt" id="btnExcel">
                    Excel
                </button>
          </div>
        </div>
        
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
          <div class="inner-form">
              <div class="row">
                  <div class="frame-container col-lg-12 pl " >                      
                      <iframe id="iframe_rpt" width="100%" height="1000" >
                      </iframe>
                  </div>
              </div>
          </div>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Print-->
@endsection

@push('bottom-css')
<style>
#custom_dropdown, #frm_trn_si_filter {
    display: inline-table;
    margin-left: 15px;
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
  $(document).ready(function(){

    $("#btndiv a").each(function(){
        if($(this).hasClass("disabled")){
            $(this).removeAttr("href");
        }
    });
    $('#btnAdd').on('click', function() {
      var viewURL = '{{route("transaction",[44,"add"])}}';
                  window.location.href=viewURL;
    });
    $('#btnExit').on('click', function() {
      var viewURL = '{{route('home')}}';
                  window.location.href=viewURL;
    });
        // Data table for serverside
         var trnfrmscTable =  $('#frm_trn_si').DataTable({
        }); //datatable

        // select all checkboxes
        $('.js-selectall').on('change', function() {
          var isChecked = $(this).prop("checked");
          var selector = $(this).data('target');
          $(selector).prop("checked", isChecked);
        });


      //get the selected row
      $('#btnSelectedRows').on('click', function() {
        var AccountsJsonString = JSON.stringify(getSeletectedCBox());
        //alert(AccountsJsonString);

      });

      $('#btnView').on('click', function() {
          var scIdsData = getSeletectedCBox();
            var seletedRecord = scIdsData.length;

            if(seletedRecord==0){

                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text('Please select a record.');
                $("#alert").modal('show');
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk1');

            }else if(seletedRecord>1){
              
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text('You cannot select multiple records.');
                $("#alert").modal('show');
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk1');
            }else if(seletedRecord==1){

                  var recordId = scIdsData[0];
                  var viewURL = '{{route("transaction",[44,"view",":rcdId"]) }}';
                  viewURL = viewURL.replace(":rcdId",recordId);
                  window.location.href=viewURL;
            }
      });

      $('#btnAttach').on('click', function() {
            var scIdsData = getSeletectedCBox();
            var seletedRecord = scIdsData.length;

            if(seletedRecord==0){

                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text('Please select a record.');
                $("#alert").modal('show');
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk1');
            }else if(seletedRecord>1){
              
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text('You cannot select multiple records.');
                $("#alert").modal('show');
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk1');
            }else if(seletedRecord==1){

                  var recordId = scIdsData[0];
                  var is_approve = $('#chkId'+recordId).data("rcdstatus");
                  
                  if(is_approve==2){
                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn1").show();
                    $("#AlertMessage").text('This record is already cancelled.');
                    $("#alert").modal('show');
                    $("#OkBtn1").focus();
                    highlighFocusBtn('activeOk1');
                  }else{
                    var attachmentURL = '{{route("transaction",[44,"attachment",":rcdId"]) }}';
                        attachmentURL = attachmentURL.replace(":rcdId",recordId);
                        window.location.href=attachmentURL;

                  }
            }
      });

$('#btnCancel').on('click', function() {
  var scIdsData = getSeletectedCBox();
  var seletedRecord = scIdsData.length;

  if(seletedRecord==0){
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select a record.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk1');
  }
  else if(seletedRecord > 1){
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('You cannot select multiple records.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk1');
  }
  else if(seletedRecord == 1){

    var recordId    = scIdsData[0];
    var is_approve  = $('#chkId'+recordId).data("rcdstatus");

    if(is_approve == 2){
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('This record is already cancelled.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk1');
    }
    else{

      if($("#FINAL_APPROVAL_"+recordId).val() =='1'){

        var LAST_UPDATE_DATE = $("#LAST_UPDATE_DATE_"+recordId).val();

        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });

        var hour  = $.ajax({
                    url:'{{ route("transaction",[44,"getHourBetweenTwoDate"])}}',
                    type:'POST',
                    async: false,
                    dataType: 'json',
                    data: {LAST_UPDATE_DATE:LAST_UPDATE_DATE},
                    done: function(response) {return response;}
                    }).responseText;

        if(parseFloat(hour) > 24){
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn1").show();
          $("#AlertMessage").text('You can not cancel aproved record after 24 hour.');
          $("#alert").modal('show');
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk1');
        }
        else if(checkPeriodClosing(44,$('#chkId'+recordId).data("docdate"),0) ==0){
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
          $("#AlertMessage").text('Do you want to cancel the record.');
          $("#YesBtn").data("funcname","fnCancelData"); 
          $("#YesBtn").focus();
          highlighFocusBtn('activeYes');
        }

      }
      else if(checkPeriodClosing(44,$('#chkId'+recordId).data("docdate"),0) ==0){
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
        $("#AlertMessage").text('Do you want to cancel the record.');
        $("#YesBtn").data("funcname","fnCancelData"); 
        $("#YesBtn").focus();
        highlighFocusBtn('activeYes');
      }
    }
  }               
});


      $('#btnEdit').on('click', function() {

            
            var scIdsData = getSeletectedCBox();
            var seletedRecord = scIdsData.length;

            if(seletedRecord==0){
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text('Please select a record.');
                $("#alert").modal('show');
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk1');
            }else if(seletedRecord>1){              
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text('You cannot select multiple records.');
                $("#alert").modal('show');
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk1');
            }else if(seletedRecord==1){

                  var recordId = scIdsData[0];
                  var is_approve = $('#chkId'+recordId).data("rcdstatus");
                  console.log("is app=="+is_approve);  

                  if(is_approve==0){

                    var editURL = '{{route("transaction",[44,"edit",":rcdId"]) }}';
                        editURL = editURL.replace(":rcdId",recordId);
                        check_approval_level(<?php echo json_encode($REQUEST_DATA);?>,recordId,editURL);
                  }else if(is_approve==2){

                      $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn1").show();
                      $("#AlertMessage").text('You cannot edit cancel record.');
                      $("#alert").modal('show');
                      $("#OkBtn1").focus();
                      highlighFocusBtn('activeOk1');
                  }else{
                    
                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn1").show();
                    $("#AlertMessage").text('You cannot edit approved record.');
                    $("#alert").modal('show');
                    $("#OkBtn1").focus();
                    highlighFocusBtn('activeOk1');
                  } 
            }
        });

$('#btnApprove').on('click', function(){

  var scIdsData     = getSeletectedCBox();
  var seletedRecord = scIdsData.length;
  var scIdsDataID   = getSeletectedCBoxID();
          
  if(seletedRecord==0){

    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select a record.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk1');
  }
  else if(seletedRecord>1){
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('You cannot select multiple records.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk1');
  }
          /*
          else if(seletedRecord>1){
            
            var recordId = scIdsDataID;
            
            var allblank = [];
            $.each(recordId,function(i, e){
              var is_approve = $('#chkId'+e.ID).data("rcdstatus");
                 if(is_approve==0){
                    allblank.push('true');
                 }
                 else{
                  allblank.push('false');
                 } 
            });
            
            if(jQuery.inArray("false", allblank) !== -1)
            {
                $("#alert").modal('show');
                $("#AlertMessage").text('Atleast 1 record is either Aprove or Cancel. Cannot proceed further.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk1');
                
            }else{

                $("#alert").modal('show');
                $("#AlertMessage").text('Do you want to approve the record.');
                $("#YesBtn").data("funcname","fnMultiApproveData");  //set dynamic fucntion name
                $("#YesBtn").focus();

                $("#OkBtn").hide();
                highlighFocusBtn('activeYes');

            }

          }*/
          else if(seletedRecord==1){

                var recordId = scIdsData[0];
                var is_approve = $('#chkId'+recordId).data("rcdstatus");
                console.log("is app=="+is_approve);  

                if(is_approve==0){

                  var editURL = '{{route("transaction",[44,"edit",":rcdId"]) }}';
                      editURL = editURL.replace(":rcdId",recordId);
                      check_approval_level(<?php echo json_encode($REQUEST_DATA);?>,recordId,editURL);
                }else if(is_approve==2){
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn1").show();
                  $("#AlertMessage").text('You cannot approve cancelled record.');
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  highlighFocusBtn('activeOk1');
                }else{
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn1").show();
                  $("#AlertMessage").text('You cannot approve Approved record.');
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  highlighFocusBtn('activeOk1');
                } 
          }
});


$('#btnEinvoice').on('click', function(){

  var scIdsData     = getSeletectedCBox();
  var seletedRecord = scIdsData.length;
  var scIdsDataID   = getSeletectedCBoxID();
        
  if(seletedRecord==0){
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select a record.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk1');
  }
  else if(seletedRecord > 1){
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('You cannot select multiple records.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk1');
  }      
  else if(seletedRecord==1){

    var recordId    = scIdsData[0];
    var is_approve  = $('#chkId'+recordId).data("rcdstatus");
    
    if(is_approve ==1){
      var editURL = '{{route("transaction",[44,"invoice",":rcdId"]) }}';
      editURL = editURL.replace(":rcdId",window.btoa(recordId));
      window.location.href=editURL;
    }
    else if(is_approve==2){
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('You cannot E-invoice cancelled record.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk1');
    }
    else{
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('You can E-invoice only Approved record.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk1');
    }
  }
});

$('#btnEinvoice_tcs').on('click', function(){

  var scIdsData     = getSeletectedCBox();
  var seletedRecord = scIdsData.length;
  var scIdsDataID   = getSeletectedCBoxID();
        
  if(seletedRecord==0){
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select a record.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk1');
  }
  else if(seletedRecord > 1){
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('You cannot select multiple records.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk1');
  }      
  else if(seletedRecord==1){

    var recordId    = scIdsData[0];
    var is_approve  = $('#chkId'+recordId).data("rcdstatus");
    
    if(is_approve ==1){
      var editURL = '{{route("transaction",[44,"invoice_section",":rcdId"]) }}';
      editURL = editURL.replace(":rcdId",window.btoa(recordId));
      window.location.href=editURL;
    }
    else if(is_approve==2){
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('You cannot E-invoice cancelled record.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk1');
    }
    else{
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('You can E-invoice only Approved record.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk1');
    }
  }
});


var selectedIds = {};
selectedIds = {pl:[], p2:[]};/* add property "pl" who's value is empty array*/

//get selected check boxes
function getSeletectedCBox(){       
        selectedIds=[];
            var checkedcollection = trnfrmscTable.$(".js-selectall1:checked", { "page": "all" });
              checkedcollection.each(function (index, elem) {
              selectedIds.push($(elem).val());
            });
            return selectedIds;          
      }

      function getSeletectedCBoxID(){       
        selectedIds=[];
            var checkedcollection = trnfrmscTable.$(".js-selectall1:checked", { "page": "all" });
              checkedcollection.each(function (index, elem) {
              selectedIds.push({'ID': $(elem).val()});
            });
            return selectedIds;          
      }


//ok button
$("#YesBtn").click(function(){
$("#alert").modal('hide');
var customFnName = $("#YesBtn").data("funcname");
    window[customFnName]();

}); 

window.fnMultiApproveData = function (){

//validate and save data
event.preventDefault();
var scIdsDataID = getSeletectedCBoxID();
var recordId = scIdsDataID;
            $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
            });
              $.ajax({
                  url:'{{ route("transaction",[44,"MultiApprove"])}}',
                  type:'POST',
                  dataType: 'json',
                  data: {'ID': JSON.stringify(recordId)},
                  success:function(data) {               
                        if(data.errors) {
                            $(".text-danger").hide();

                            if(data.errors.LABEL){
                              //    showError('Please enter correct value in Label.',data.errors.LABEL);
                                  console.log(data.errors.LABEL);
                                  $("#YesBtn").hide();
                                  $("#NoBtn").hide();
                                  $("#OkBtn").show();
                                  $("#AlertMessage").text('Please enter correct value in Label.');
                                  $("#alert").modal('show');
                                  $("#OkBtn").focus();
                                  highlighFocusBtn('activeOk');
                            }
                            if(data.errors.VALUETYPE){
                              //    showError('Please select value from ValueType.',data.errors.VALUETYPE);
                                console.log(data.errors.VALUETYPE);
                                $("#YesBtn").hide();
                                $("#NoBtn").hide();
                                $("#OkBtn").show();
                                $("#AlertMessage").text('Please select value from ValueType.');
                                $("#alert").modal('show');
                                $("#OkBtn").focus();
                                highlighFocusBtn('activeOk');
                            }
                            if(data.country=='duplicate') {
                              $("#YesBtn").hide();
                              $("#NoBtn").hide();
                              $("#OkBtn").show();
                              $("#AlertMessage").text(data.msg);
                              $("#alert").modal('show');
                              $("#OkBtn").focus();
                              highlighFocusBtn('activeOk');
                            }
                            if(data.save=='invalid') {
                              $("#YesBtn").hide();
                              $("#NoBtn").hide();
                              $("#OkBtn").show();
                              $("#AlertMessage").text(data.msg);
                              $("#alert").modal('show');
                              $("#OkBtn").focus();
                              highlighFocusBtn('activeOk');
                            }
                        }
                        if(data.approve) {                   
                            console.log("succes MSG="+data.msg);
                            $("#YesBtn").hide();
                            $("#NoBtn").hide();
                            $("#OkBtn").show();
                            $("#AlertMessage").text(data.msg);
                            $(".text-danger").hide();
                            $("#frm_mst_se").trigger("reset");
                            $("#alert").modal('show');
                            $("#OkBtn").focus();
                            highlighFocusBtn('activeOk');
                            // window.location.href="{{ route('master',[90,'index']) }}";
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

window.fnCancelData = function (){

//validate and save data
event.preventDefault();
            var scIdsData = getSeletectedCBox();
            var seletedRecord = scIdsData.length;
            var recordId = scIdsData[0];
            $.ajaxSetup({
                              headers: {
                                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                              }
                          });
                          $.ajax({
                            url:'{{ route("transactionmodify",[44,"cancel"])}}',
                            type:'POST',
                            data: JSON.stringify(recordId),
                            contentType: 'application/json; charset=utf-8',
                            dataType: 'json',
                            success:function(data) {               
                                  if(data.errors) {
                                      $(".text-danger").hide();

                                      if(data.errors.LABEL){
                                        //    showError('Please enter correct value in Label.',data.errors.LABEL);
                                            console.log(data.errors.LABEL);
                                            $("#YesBtn").hide();
                                            $("#NoBtn").hide();
                                            $("#OkBtn").show();
                                            $("#AlertMessage").text('Please enter correct value in Label.');
                                            $("#alert").modal('show');
                                            $("#OkBtn").focus();
                                      }
                                      if(data.errors.VALUETYPE){
                                        //    showError('Please select value from ValueType.',data.errors.VALUETYPE);
                                          console.log(data.errors.VALUETYPE);
                                          $("#YesBtn").hide();
                                          $("#NoBtn").hide();
                                          $("#OkBtn").show();
                                          $("#AlertMessage").text('Please select value from ValueType.');
                                          $("#alert").modal('show');
                                          $("#OkBtn").focus();
                                      }
                                      if(data.country=='duplicate') {
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
                                  if(data.cancel) {                   
                                      console.log("cancel MSG="+data.msg);
                                      $("#YesBtn").hide();
                                      $("#NoBtn").hide();
                                      $("#OkBtn").show();
                                      $("#AlertMessage").text(data.msg);
                                      $(".text-danger").hide();
                                      $("#frm_mst_se").trigger("reset");
                                      $("#alert").modal('show');
                                      $("#OkBtn").focus();
                                      highlighFocusBtn('activeOk');
                                      // window.location.href="{{ route('master',[90,'index']) }}";
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
                                      highlighFocusBtn('activeOk1');
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
    window.location.href = '{{route("transaction",[44,"index"]) }}';
});
$("#OkBtn1").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn").hide();
    $("#OkBtn1").hide();
    $(".text-danger").hide();
});
function highlighFocusBtn(pclass){
       $(".activeYes").hide();
       $(".activeNo").hide();
       
       $("."+pclass+"").show();
    }

$('#btnPdf').on('click', function() {
  var soIdsData = getSeletectedCBox();
    var seletedRecord = soIdsData.length;

      if(seletedRecord==0){
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn1").show();
          $("#AlertMessage").text('Please select a record.');
          $("#alert").modal('show');
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk1');
      }else if(seletedRecord>1){              
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn1").show();
          $("#AlertMessage").text('You cannot select multiple records.');
          $("#alert").modal('show');
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk1');
      }else if(seletedRecord==1)
      {
            var SONO = soIdsData[0];
            var Flag = 'P';
            var formData = 'SO='+ SONO + '&SINO='+ SONO + '&Flag='+ Flag ;
            var consultURL = '{{route("transaction",[44,"ViewReport",":rcdId"]) }}';
            consultURL = consultURL.replace(":rcdId",formData);
            window.location.href=consultURL;
            event.preventDefault();
      }
}); 

$('#btnExcel').on('click', function() {
    var soIdsData = getSeletectedCBox();
    var seletedRecord = soIdsData.length;
      if(seletedRecord==0){
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn1").show();
          $("#AlertMessage").text('Please select a record.');
          $("#alert").modal('show');
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk1');
      }else if(seletedRecord>1){              
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn1").show();
          $("#AlertMessage").text('You cannot select multiple records.');
          $("#alert").modal('show');
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk1');
      }else if(seletedRecord==1)
      {
        var SONO = soIdsData[0];
        var Flag = 'E';
        var formData = 'SO='+ SONO + '&SINO='+ SONO + '&Flag='+ Flag ;
        var consultURL = '{{route("transaction",[44,"ViewReport",":rcdId"]) }}';
        consultURL = consultURL.replace(":rcdId",formData);
        window.location.href=consultURL;
        event.preventDefault();
      }
});

$('#btnPrint').on('click', function() {
      var soIdsData = getSeletectedCBox();
      var seletedRecord = soIdsData.length;
      if(seletedRecord==0){
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn1").show();
          $("#AlertMessage").text('Please select a record.');
          $("#alert").modal('show');
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk1');
      }else if(seletedRecord>1){              
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn1").show();
          $("#AlertMessage").text('You cannot select multiple records.');
          $("#alert").modal('show');
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk1');
      }else if(seletedRecord==1)
      {
            var SONO = soIdsData[0];
            var Flag = 'H';
            var formData = 'SO='+ SONO + '&SINO='+ SONO + '&Flag='+ Flag ;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url:'{{route("transaction",[44,"ViewReport"])}}',
                type:'POST',
                data:formData,
                success:function(data) {
                    $('#ReportView').show();
                    var localS = data;
                    document.getElementById('iframe_rpt').src = "data:text/html;charset=utf-8," + escape(localS);
                    $('#btnPdf').show();
                    $('#btnExcel').show();
                    $('#btnPrint').show();
                },
                error:function(data){
                    console.log("Error: Something went wrong.");
                    var localS = "";
                    document.getElementById('iframe_rpt').src = "data:text/html;charset=utf-8," + escape(localS);
                    $('#btnPdf').hide();
                    $('#btnExcel').hide();
                    $('#btnPrint').hide();
                },
            });
            event.preventDefault();
        }
  });

  $('#btnReport').on('click', function() {
   var soIdsData = getSeletectedCBox();
    var seletedRecord = soIdsData.length;

      if(seletedRecord==0){
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn1").show();
          $("#AlertMessage").text('Please select a record.');
          $("#alert").modal('show');
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk1');
      }else if(seletedRecord>1){              
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn1").show();
          $("#AlertMessage").text('You cannot select multiple records.');
          $("#alert").modal('show');
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk1');
      }else if(seletedRecord==1)
      {
        var SONO = soIdsData[0];
            var Flag = 'R';
            var formData = 'SO='+ SONO + '&SINO='+ SONO + '&Flag='+ Flag ;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url:'{{route("transaction",[44,"ViewReport"])}}',
                type:'POST',
                data:formData,
                success:function(data) {
                    printWindow = window.open('');
                    printWindow.document.write(data);
                    printWindow.print();
                },
                error:function(data){
                    console.log("Error: Something went wrong.")
                    printWindow = window.open('');
                    printWindow.document.write("Error: Something went wrong.");
                    printWindow.print();
                },
            });
            event.preventDefault();
      }
});

$("#ReportViewclosePopup").click(function(event){
  $("#ReportView").hide();
  event.preventDefault();
});
    
});  //ready
</script>

@endpush