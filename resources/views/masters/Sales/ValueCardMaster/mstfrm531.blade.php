@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
  <div class="row">
    <div class="col-lg-2">
      <a href="{{route('master',[$FormId,'index'])}}" class="btn singlebt">Value Card Master</a>
    </div>
    <div class="col-lg-10 topnav-pd">
      <button class="btn topnavbt" id="btnAdd" {{isset($objRights->ADD) && $objRights->ADD != 1 ? 'disabled' : ''}} ><i class="fa fa-plus"></i> Add</button>
      <button class="btn topnavbt" id="btnEdit" {{isset($objRights->EDIT) && $objRights->EDIT != 1 ? 'disabled' : ''}} ><i class="fa fa-pencil-square-o"></i> Edit</button>
      <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-floppy-o"></i> Save</button>
      <button class="btn topnavbt" id="btnView" {{isset($objRights->VIEW) && $objRights->VIEW != 1 ? 'disabled' : ''}} ><i class="fa fa-eye"></i> View</button>
      <button class="btn topnavbt" disabled="disabled"><i class="fa fa-print"></i> Print</button>
      <button class="btn topnavbt" disabled="disabled"><i class="fa fa-undo"></i> Undo</button>
      <button class="btn topnavbt" id="btnCancel" {{isset($objRights->CANCEL) && $objRights->CANCEL != 1 ? 'disabled' : ''}} ><i class="fa fa-times"></i> Cancel</button>
      <button class="btn topnavbt" id="btnApprove" {{ (isset($objRights->APPROVAL1) || isset($objRights->APPROVAL2) || isset($objRights->APPROVAL3) || isset($objRights->APPROVAL4) || isset($objRights->APPROVAL5)) &&  ($objRights->APPROVAL1||$objRights->APPROVAL2||$objRights->APPROVAL3||$objRights->APPROVAL4||$objRights->APPROVAL5) == 1 ? '' : 'disabled'}} ><i class="fa fa-thumbs-o-up"></i> Approved</button>
      <button class="btn topnavbt"  id="btnAttach" {{isset($objRights->ATTECHMENT) && $objRights->ATTECHMENT != 1 ? 'disabled' : ''}} ><i class="fa fa-link"></i> Attachment</button>
      <button class="btn topnavbt" id="btnExit"><i class="fa fa-power-off"></i> Exit</button>
    </div>
  </div>
</div>
  
<div class="container-fluid">

  <div class="inner-form"> 
    <div class="row">
      <div class="col-lg-2 pl"><p>Franchise Name</p></div>
      <div class="col-lg-2 pl">
        <input type="text"   name="BRANCH_NAME" id="BRANCH_NAME"  class="form-control"  autocomplete="off"  onclick="getBranchMaster()" readonly/>
        <input type="hidden" name="BRANCH_ID" id="BRANCH_ID"      class="form-control"  autocomplete="off" />  
      </div>
    </div>
  </div>

  <div class="multiple table-responsive" id="listing_data">    
    <table id="listingmst" class="display nowrap table table-striped table-bordered" width="100%">
      <thead id="thead1">
        <tr>
          <th id="all-check" style="width:50px;"><input type="checkbox" class="js-selectall" data-target=".js-selectall1"  />Select</th>
          <th>Document No</th>
          <th>Document Date</th>
          <th>Card Number</th>
          <th>Card Amount</th>
          <th>Discount (%)</th>
          <th>Net Amount</th>
          <th>Balance Amount</th>
          <th>Validity Month</th>
          <th>Validity Till</th>
          <th>Created By</th>
          <th>Created Date</th>
          <th>Status</th>
        </tr>    
      </thead>
      <tbody id="listing_body" > 
        
        @if(!empty($objDataList))           
        @foreach($objDataList as $key => $val)
        @php
        $DataStatus="";
        if(!Empty($val->STATUS) && $val->STATUS=="A"){ 
          $app_status = 1 ;
          $DataStatus = "Approved";
        } 
        elseif($val->STATUS=="C"){ 
          $app_status = 2 ;
          $DataStatus = "Cancel";
        }
        else{ 
          $app_status = 0 ;
          $DataStatus = "Not Approved";
        }
        @endphp
        <tr>
          <td><input type="checkbox" name="selectAll[]" id="chkId{{$val->DOC_ID}}" value="{{$val->DOC_ID}}" class="js-selectall1" data-rcdstatus="{{$app_status}}"></td>
          <td>{{isset($val->DOC_NO) && $val->DOC_NO !=''?$val->DOC_NO:''}}</td>
          <td>{{isset($val->DOC_DATE) && $val->DOC_DATE !=''?$val->DOC_DATE:''}}</td>

          <td>{{isset($val->CARD_NO) && $val->CARD_NO !=''?$val->CARD_NO:''}}</td>
          <td>{{isset($val->AMOUNT) && $val->AMOUNT !=''?$val->AMOUNT:''}}</td>
          <td>{{isset($val->DISCOUNT_PERCENT) && $val->DISCOUNT_PERCENT !=''?$val->DISCOUNT_PERCENT:''}}</td>
          <td>{{isset($val->NET_AMOUNT) && $val->NET_AMOUNT !=''?$val->NET_AMOUNT:''}}</td>
          <td></td>
          <td>{{isset($val->VALIDITY_MON) && $val->VALIDITY_MON !=''?$val->VALIDITY_MON:''}}</td>
          <td>{{isset($val->VALIDITY_TILL) && $val->VALIDITY_TILL !=''?$val->VALIDITY_TILL:''}}</td>

          <td>{{isset($val->CREATEDBY) && $val->CREATEDBY !=''?$val->CREATEDBY:''}}</td>
          <td>{{isset($val->INDATE) && $val->INDATE !='' && $val->INDATE !='1900-01-01' ? date('d-m-Y',strtotime($val->INDATE)):''}}</td>
          <td>{{$DataStatus}}</td>
        </tr>
        @endforeach 
        @endif
       
      </tbody>
    </table>  
   

                                                  
  </div>
</div>

<form id="masterForm268Print" action="{{ route('mastergetlist',[$FormId,'printdata'])}}" method="POST" >
  @csrf
  <input type="hidden" name="records_ids" id="massPrintIds" value="">                            
</form>    
@endsection
@section('alert')
<div id="alert" class="modal"  role="dialog"  data-backdrop="static" >
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='closePopup' >&times;</button>
        <h4 class="modal-title">System Alert Message</h4>
      </div>
      <div class="modal-body">
	  <h5 id="AlertMessage" ></h5>
        <div class="btdiv">    
            <button class="btn alertbt" name='YesBtn' id="YesBtn" data-funcname="fnSaveData" style="display:none;"> 
                <div id="alert-active" class="activeYes"></div> Yes</button>
            <button class="btn alertbt" name='NoBtn' id="NoBtn"  data-funcname="fnUndoNo"  style="display:none;">
                <div id="alert-active" class="activeNo"></div>No</button>
            <button class="btn alertbt" name='OkBtn' id="OkBtn" style="margin-left: 90px;">
                <div id="alert-active" class="activeOk"></div>OK</button>
            <button class="btn alertbt" name='OkBtn1' id="OkBtn1" style="margin-left: 90px;display:none;">
                <div id="alert-active" class="activeOk"></div>OK</button>
        </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<div id="modal" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:50%;" >
    <div class="modal-content">
      <div class="modal-header"><button type="button" class="close" data-dismiss="modal" onclick="closeEvent('modal')" >&times;</button></div>
      <div class="modal-body">
	      <div class="tablename"><p id='modal_title'></p></div>
	      <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
          <table id="modal_table1" class="display nowrap table  table-striped table-bordered" >
            <thead>
              <tr>
                <th style="width:10%;">Select</th> 
                <th style="width:45%;" id='modal_th1'></th>
                <th style="width:45%;" id='modal_th2'></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <th style="width:10%;"></th>
                <td style="width:45%;"><input type="text" id="text1" class="form-control" autocomplete="off" onkeyup="searchData(1)"></td>
                <td style="width:45%;"><input type="text" id="text2" class="form-control" autocomplete="off" onkeyup="searchData(2)"></td>
              </tr>
            </tbody>
          </table>

          <table id="modal_table2" class="display nowrap table  table-striped table-bordered" >
            <tbody id="modal_body" style="font-size:14px;"></tbody>
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
  #custom_dropdown, #listingmst_filter {
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
$('#btnAdd').on('click', function() {
  var viewURL = '{{route("master",[$FormId,"add"])}}';
  window.location.href=viewURL;
});

$('#btnExit').on('click', function() {
  var viewURL = '{{route('home')}}';
  window.location.href=viewURL;
});
     
$(document).ready(function(){
  $('#listingmst').DataTable({}); 
  $('.js-selectall').on('change', function() {
    var isChecked = $(this).prop("checked");
    var selector = $(this).data('target');
    $(selector).prop("checked", isChecked);
  });


  $('#btnEdit').on('click', function() {

    var resultIdsData = getSeletectedCBox();
    var seletedRecord = resultIdsData.length;

            if(seletedRecord==0){

              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn").hide();
              $("#OkBtn1").show();
              $("#AlertMessage").text('Please select a record.');
              $("#alert").modal('show');
              $("#OkBtn1").focus();
              
              

            }else if(seletedRecord>1){

              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn").hide();
              $("#OkBtn1").show();
              $("#AlertMessage").text('You cannot select multiple records.');
              $("#alert").modal('show');
              $("#OkBtn1").focus();
              
            }else if(seletedRecord==1){

              var recordId = resultIdsData[0];
                  var is_approve = $('#chkId'+recordId).data("rcdstatus");
                  console.log("is app=="+is_approve);  

                  if(is_approve==0){

                    var editURL = '{{route("master",[$FormId,"edit",":rcdId"]) }}';
                        editURL = editURL.replace(":rcdId",window.btoa(recordId));
                        window.location.href=editURL;
                  }else if(is_approve==2){

                      $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn").hide();
                      $("#OkBtn1").show();
                      $("#AlertMessage").text('You cannot edit cancel record.');
                      $("#alert").modal('show');
                      $("#OkBtn1").focus();
                  }else{

                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn").hide();
                    $("#OkBtn1").show();
                    $("#AlertMessage").text('You cannot edit approved record.');
                    $("#alert").modal('show');
                    $("#OkBtn1").focus();
                  } 
            }

}); 
    

    $('#btnView').on('click', function() {

      var resultIdsData = getSeletectedCBox();
      var seletedRecord = resultIdsData.length;

      if(seletedRecord==0){
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn").hide();
          $("#OkBtn1").show();
          $("#AlertMessage").text('Please select a record.');
          $("#alert").modal('show');
          $("#OkBtn1").focus();

      }else if(seletedRecord>1){
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn").hide();
          $("#OkBtn1").show();
          $("#AlertMessage").text('You cannot select multiple records.');
          $("#alert").modal('show');
          $("#OkBtn1").focus();

      }else if(seletedRecord==1){

            var viweRecordId = resultIdsData[0];
            var viewURL = '{{route("master",[$FormId,"view",":rcdId"]) }}';
                viewURL = viewURL.replace(":rcdId",window.btoa(viweRecordId));
                window.location.href=viewURL;
      }

    });//edit function


    $('#btnApprove').on('click', function() {
            var resultIdsData = getSeletectedCBox();
            var seletedRecord = resultIdsData.length;
            var resultIdsDataID = getSeletectedCBoxID();

            if(seletedRecord==0){
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text('Please select a record.');
                $("#alert").modal('show');
                $("#OkBtn1").focus();

            }
            else if(seletedRecord>1){
              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn").hide();
              $("#OkBtn1").show();
              $("#AlertMessage").text('You cannot select multiple records.');
              $("#alert").modal('show');
              $("#OkBtn1").focus();
            }
            else if(seletedRecord==1){

              var recordId = resultIdsData[0];
                var is_approve = $('#chkId'+recordId).data("rcdstatus");
                console.log("is app=="+is_approve);  

                if(is_approve==0){

                  var editURL = '{{route("master",[$FormId,"edit",":rcdId"]) }}';
                      editURL = editURL.replace(":rcdId",window.btoa(recordId));
                      window.location.href=editURL;
                }else if(is_approve==2){
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").hide();
                  $("#OkBtn1").show();
                  $("#AlertMessage").text('You cannot approve cancelled record.');
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                }else{
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").hide();
                  $("#OkBtn1").show();
                  $("#AlertMessage").text('You cannot approve Approved record.');
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();

                } 
            }
    });//Approved 

    $('#btnCancel').on('click', function() {
          var resultIdsData = getSeletectedCBox();
            var seletedRecord = resultIdsData.length;

            if(seletedRecord==0){
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text('Please select a record.');
                $("#alert").modal('show');
                $("#OkBtn1").focus();

            }else if(seletedRecord>1){
              
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text('You cannot select multiple records.');
                $("#alert").modal('show');
                $("#OkBtn1").focus();

            }else if(seletedRecord==1){

              var recordId = resultIdsData[0];
                  var is_approve = $('#chkId'+recordId).data("rcdstatus");

                  if(is_approve==2){
                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn").hide();
                    $("#OkBtn1").show();
                    $("#AlertMessage").text('This record is already cancelled.');
                    $("#alert").modal('show');
                    $("#OkBtn1").focus();

                  }else{
                    event.preventDefault();
                    $("#YesBtn").show();
                    $("#NoBtn").show();
                    $("#OkBtn").hide();
                    $("#OkBtn1").hide();
                    $("#alert").modal('show');
                    $("#AlertMessage").text('Do you want to cancel the record.');
                    $("#YesBtn").data("funcname","fnCancelData"); 
                    $("#YesBtn").focus();
                    highlighFocusBtn("activeYes");
                  }     
            }
      });// Cancel



    $('#btnAttach').on('click', function() {
            var resultIdsData = getSeletectedCBox();
            var seletedRecord = resultIdsData.length;

            if(seletedRecord==0){

              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn").hide();
              $("#OkBtn1").show();
              $("#AlertMessage").text('Please select a record.');
              $("#alert").modal('show');
              $("#OkBtn1").focus();

            }else if(seletedRecord>1){
              
                 $("#AlertMessage").text('You cannot select multiple records.');
                 $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn").hide();
                    $("#OkBtn1").show();
                 $("#alert").modal('show');
                 $("#OkBtn1").focus();

            }else if(seletedRecord==1){

                  var recordId = resultIdsData[0];
                  var is_approve = $('#chkId'+recordId).data("rcdstatus");
                  
                  if(is_approve==2){
                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn").hide();
                    $("#OkBtn1").show();
                    $("#AlertMessage").text('This record is already cancelled.');
                    $("#alert").modal('show');
                    $("#OkBtn1").focus();

                  }else{
                    var attachmentURL = '{{route("master",[$FormId,"attachment",":rcdId"]) }}';
                        attachmentURL = attachmentURL.replace(":rcdId",recordId);
                        window.location.href=attachmentURL;

                  } 
            }
    });//Attachment 
      


      
// var selectedIds = {};
// selectedIds = {pl:[], p2:[]};
     
function getSeletectedCBox(){
  var selectedIds = [];
  var all_location_id = document.querySelectorAll('input[name="selectAll[]"]:checked');
  
  for(var x = 0, l = all_location_id.length; x < l;  x++){
    selectedIds.push(all_location_id[x].value);
  }
  return selectedIds;
}

function getSeletectedCBoxID(){       
  var selectedIds = [];
  var all_location_id = document.querySelectorAll('input[name="selectAll[]"]:checked');
  
  for(var x = 0, l = all_location_id.length; x < l;  x++){
    selectedIds.push({'ID': all_location_id[x].value});
  }

  return selectedIds;          
}

window.fnMultiApproveData = function (){

//validate and save data
event.preventDefault();
var resultIdsDataID = getSeletectedCBoxID();
var recordId = resultIdsDataID;
            $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
            });
              $.ajax({
                  url:'{{ route("master",[$FormId,"MultiApprove"])}}',
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
                            if(data.exist=='duplicate') {
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
                            window.location.href="{{ route('master',[$FormId,'index']) }}";
                        }               
                    },
                    error:function(data){
                      console.log("Error: Something went wrong.");
                      $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn").show();
                      $("#AlertMessage").text('Error: Something went wrong.');
                      $("#alert").modal('show');
                      $("#OkBtn").focus();
                    },
              });

}

window.fnCancelData = function (){

//validate and save data
event.preventDefault();
            var resultIdsData = getSeletectedCBox();
            var seletedRecord = resultIdsData.length;
            var recordId = resultIdsData[0];
        
            $.ajaxSetup({
                              headers: {
                                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                              }
                          });
                          $.ajax({
                            url:'{{ route("mastermodify",[$FormId,"cancel"])}}',
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
                                      if(data.exist=='duplicate') {
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
                                  $("#OkBtn").show();
                                  $("#AlertMessage").text('Error: Something went wrong.');
                                  $("#alert").modal('show');
                                  $("#OkBtn").focus();
                              },
                        });

}

    $('#OkBtn').on('click', function() {

      $("#alert").modal('hide');

    }); 


    $('#btnPrint').on('click', function() {

          var resultIdsData = getSeletectedCBox();
          var seletedRecord = resultIdsData.length;

          if(seletedRecord==0){
            
              $("#massPrintIds").val('');
              $("#AlertMessage").text('Please select a record.');
              $("#alert").modal('show');
              $("#OkBtn").focus();

          }else if(seletedRecord>1){
            
              var recordsIds = resultIdsData;
               $("#massPrintIds").val(recordsIds);
               $("#masterForm268Print").submit()

          }

    });//print function

    $("#massPrintIds").val(''); //reset printid 

    $("#NoBtn").click(function(){
    $("#alert").modal('hide');
    $("#LABEL").focus();
});

$("#YesBtn").click(function(){
$("#alert").modal('hide');
var customFnName = $("#YesBtn").data("funcname");
    window[customFnName]();

});

//ok button
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
    $(".text-danger").hide();
});
function highlighFocusBtn(pclass){
       $(".activeYes").hide();
       $(".activeNo").hide();
       
       $("."+pclass+"").show();
    }


});



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

let tid1    = "#modal_table1";
let tid2    = "#modal_table2";
let headers = document.querySelectorAll(tid1 + " th");

headers.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(tid2, ".clsipoid", "td:nth-child(" + (i + 1) + ")");
  });
});

function searchData(cno){
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById('text'+cno);
  filter = input.value.toUpperCase();
  table = document.getElementById("modal_table2");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[cno];
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

function closeEvent(id){
  $("#"+id).hide();
}

function getBranchMaster(){
  $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  $.ajax({
    url:'{{route("master",[$FormId,"getBranchMaster"])}}',
    type:'POST',
    success:function(data) {
      var html = '';

      if(data.length > 0){
        $.each(data, function(key, value) {
          html +='<tr>';
          html +='<td style="width:10%;text-align:center;" ><input type="checkbox" id="key_'+key+'" value="'+value.DATA_ID+'" onChange="bindBranchMaster(this)" data-code="'+value.DATA_CODE+'" data-desc="'+value.DATA_DESC+'" ></td>';
          html +='<td style="width:45%;" >'+value.DATA_CODE+'</td>';
          html +='<td style="width:45%;" >'+value.DATA_DESC+'</td>';
          html +='</tr>';
        });
      }
      else{
        html +='<tr><td colspan="3" style="text-align:center;">No data available in table</td></tr>';
      }

      $("#modal_body").html(html);
    },
    error: function (request, status, error) {
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").show();
      $("#AlertMessage").text(request.responseText);
      $("#alert").modal('show');
      $("#OkBtn").focus();
      highlighFocusBtn('activeOk');
      $("#material_data").html('<tr><td colspan="3" style="text-align:center;">No data available in table</td></tr>');                       
    },
  });

  $("#modal_title").text('Branch Master');
  $("#modal_th1").text('Branch Code');
  $("#modal_th2").text('Branch Name');
  $("#modal").show();
}

function bindBranchMaster(data){
  var code  = $("#"+data.id).data("code");
  var desc  = $("#"+data.id).data("desc");

  $("#BRANCH_ID").val(data.value);
  $("#BRANCH_NAME").val(code+' - '+desc);

  getListingData(data.value);
  
  $("#text1").val(''); 
  $("#text2").val(''); 
  $("#modal_body").html('');  
  $("#modal").hide(); 
}

function getListingData(FRANCHISE_ID){

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  
  $.ajax({
    url:'{{route("master",[$FormId,"getListingData"])}}',
    type:'POST',
    data:{FRANCHISE_ID:FRANCHISE_ID},
    success:function(data) {

      var html = '';

      html +='<table id="listingmst" class="display nowrap table table-striped table-bordered" width="100%">';
      html +='<thead id="thead1">';
      html +='<tr>';
      html +='<th id="all-check" style="width:50px;"><input type="checkbox" class="js-selectall" data-target=".js-selectall1"  />Select</th>';
      html +='<th>Document No</th>';
      html +='<th>Document Date</th>';
      html +='<th>Card Number</th>';
      html +='<th>Card Amount</th>';
      html +='<th>Discount (%)</th>';
      html +='<th>Net Amount</th>';
      html +='<th>Balance Amount</th>';
      html +='<th>Validity Month</th>';
      html +='<th>Validity Till</th>';
      html +='<th>Created By</th>';
      html +='<th>Created Date</th>';
      html +='<th>Status</th>';
      html +='</tr>';    
      html +='</thead>';
      html +='<tbody>';

      if(data.length > 0){
        $.each(data, function(key, value) {

          var DataStatus  ="";
          if(value.STATUS=="A"){ 
            var app_status = 1 ;
            var DataStatus = "Approved";
          } 
          else if(value.STATUS=="C"){ 
            var app_status = 2 ;
            var DataStatus = "Cancel";
          } 
          else{ 
            var app_status = 0 ;
            var DataStatus = "Not Approved";
          }

          html +='<tr>';
          html +='<td><input type="checkbox" name="selectAll[]" id="chkId'+value.DOC_ID+'" value="'+value.DOC_ID+'" class="js-selectall1" data-rcdstatus="'+app_status+'"></td>';
          html +='<td>'+value.DOC_NO+'</td>';
          html +='<td>'+value.DOC_DATE+'</td>';
          html +='<td>'+value.CARD_NO+'</td>';
          html +='<td>'+value.AMOUNT+'</td>';
          html +='<td>'+value.DISCOUNT_PERCENT+'</td>';
          html +='<td>'+value.NET_AMOUNT+'</td>';
          html +='<td></td>';
          html +='<td>'+value.VALIDITY_MON+'</td>';
          html +='<td>'+value.VALIDITY_TILL+'</td>';
          html +='<td>'+value.CREATEDBY+'</td>';
          html +='<td>'+value.CREATED_DATE+'</td>';
          html +='<td>'+DataStatus+'</td>';
          html +='</tr>';
        });

      }
      
      html +='</tbody>';
      html +='<table>';

      $("#listing_data").html(html);
      $('#listingmst').DataTable({}); 
    },
    error: function (request, status, error){
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").show();
      $("#AlertMessage").text(request.responseText);
      $("#alert").modal('show');
      $("#OkBtn").focus();
      highlighFocusBtn('activeOk');
      $("#listing_body").html('<tr><td colspan="3" style="text-align:center;">No data available in table</td></tr>');                       
    },
  });

}
</script>
@endpush


