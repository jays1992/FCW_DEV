@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
  <div class="row">
    <div class="col-lg-2"><a href="{{route('master',[72,'index'])}}" class="btn singlebt">Item Master</a></div>
    <div class="col-lg-10 topnav-pd">
      <a href="{{route('master',[72,'add'])}}" id="btnSelectedRows" class="btn topnavbt" {{isset($objRights->ADD) && $objRights->ADD != 1 ? 'disabled' : ''}} ><i class="fa fa-plus"></i> Add</a>
      <button class="btn topnavbt" id="btnEdit" {{isset($objRights->EDIT) && $objRights->EDIT != 1 ? 'disabled' : ''}} ><i class="fa fa-edit"></i> Edit</button>
      <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-save"></i> Save</button>
      <button class="btn topnavbt" id="btnView" {{isset($objRights->VIEW) && $objRights->VIEW != 1 ? 'disabled' : ''}} ><i class="fa fa-eye"></i> View</button>
      <button class="btn topnavbt" disabled="disabled"><i class="fa fa-print"></i> Print</button>
      <button class="btn topnavbt" disabled="readonly"><i class="fa fa-undo"></i> Undo</button>
      <button class="btn topnavbt" id="btnCancel" {{isset($objRights->CANCEL) && $objRights->CANCEL != 1 ? 'disabled' : ''}} ><i class="fa fa-times"></i> Cancel</button>
      <button class="btn topnavbt" id="btnApprove" {{ (isset($objRights->APPROVAL1) || isset($objRights->APPROVAL2) || isset($objRights->APPROVAL3) || isset($objRights->APPROVAL4) || isset($objRights->APPROVAL5)) &&  ($objRights->APPROVAL1||$objRights->APPROVAL2||$objRights->APPROVAL3||$objRights->APPROVAL4||$objRights->APPROVAL5) == 1 ? '' : 'disabled'}} ><i class="fa fa-lock"></i> Approved</button>
      <button class="btn topnavbt" id="btnCopy"><i class="fa fa-copy"></i> Copy</button>
      <button class="btn topnavbt"  id="btnAttach" {{isset($objRights->ATTECHMENT) && $objRights->ATTECHMENT != 1 ? 'disabled' : ''}} ><i class="fa fa-link"></i> Attachment</button>
      <button class="btn topnavbt" id="btnAmendment" {{isset($objRights->AMENDMENT) && $objRights->AMENDMENT == 1 ? '' : 'disabled'}}><i class="fa fa-thumbs-o-up"></i> Amendment</button>
      <button class="btn topnavbt" id="btnImport" {{isset($objRights->AMENDMENT) && $objRights->AMENDMENT != 1 ? 'disabled' : ''}} > <i class="fa fa-file"></i> Import</button>
      <a href="{{route('home')}}" class="btn topnavbt"><i class="fa fa-power-off"></i> Exit</a>
    </div>
  </div>
</div>
    
<div class="container-fluid purchase-order-view">

  <div>Showing 1 to 50 of <span id="BindRow">0</span> entries (filtered from <span id="TotalRow"> total {{$TotalRow}} entries)</div>
  <div class=" table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:500px;"  id="load_scroll" >

    
    <table id="custlist" class="display nowrap table table-striped table-bordered" style="width:100%;">
      <thead id="thead1" >

        <tr>
        <th><span style="position:relative;bottom:10px;left:5px;">&#10004;</span></th>
          <th>
            <input type="text" id="ICODE" class="form-control" autocomplete="off" onkeyup="searchData(event,this.value)" >
            <div class="input-group" ><span class="input-group-addon btn_search" style="height:50px;"><i class="fa fa-search" onclick="getListingData()" ></i></span></div>
          </th>

          <th>
            <input type="text" id="NAME" class="form-control" autocomplete="off" onkeyup="searchData(event,this.value)" > 
            <div class="input-group" ><span class="input-group-addon btn_search"><i class="fa fa-search" onclick="getListingData()" ></i></span></div>
          </th>

          <th>
            <input type="text" id="ITEM_DESC" class="form-control" autocomplete="off" onkeyup="searchData(event,this.value)" >
            <div class="input-group" ><span class="input-group-addon btn_search"><i class="fa fa-search" onclick="getListingData()" ></i></span></div>
          </th>

          <th></th>

          <th {{$AlpsStatus['hidden']}}>
            <input type="text" id="ALPS_PART_NO" class="form-control" autocomplete="off" onkeyup="searchData(event,this.value)" >
            <div class="input-group" ><span class="input-group-addon btn_search"><i class="fa fa-search" onclick="getListingData()" ></i></span></div>
          </th>

          <th {{$AlpsStatus['hidden']}}>
            <input type="text" id="CUSTOMER_PART_NO" class="form-control" autocomplete="off" onkeyup="searchData(event,this.value)" >
            <div class="input-group" ><span class="input-group-addon btn_search"><i class="fa fa-search" onclick="getListingData()" ></i></span></div>
          </th>

          <th {{$AlpsStatus['hidden']}}>
            <input type="text" id="OEM_PART_NO" class="form-control" autocomplete="off" onkeyup="searchData(event,this.value)" >
            <div class="input-group" ><span class="input-group-addon btn_search"><i class="fa fa-search" onclick="getListingData()" ></i></span></div>
          </th>

          
          <th></th>
          <th></th>
          <th hidden></th>
          <th hidden></th>
          <th>
            <select id="STATUS" class="form-control" autocomplete="off" onchange="searchData(event,this.value)" >
              <option value="">Select</option>
              <option value="A">Approve</option>
              <option value="N" >Not Approve</option>
              <option value="C">Cancel</option>
            </select>
            <div class="input-group" ><span class="input-group-addon btn_search"><i class="fa fa-search" onclick="getListingData()" ></i></span></div>
          </th>
        </tr>

        <tr>
        
        <th id="all-check" style="text-align:center;"><input type="checkbox" class="js-selectall" data-target=".js-selectall1"  /></th>
          <th>Item Code</th>
          <th>Item Name</th>
          <th>Item Description</th>
          <th>Business Unit</th>
          <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</th>
          <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</th>
          <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</th>
          <th>Created Date</th>
          <th>Created By</th>
          <th hidden>De-Activated</th>
          <th hidden>Date of De-Activated</th>
          <th>Status</th>
        </tr>
       
             
      </thead>
      <tbody id="load_data"></tbody>
        
    
        
      <tfoot hidden>
        <tr>
          <th >Select</th>
          <th>Item Code</th>
          <th>Item Name</th>
          <th>Item Description</th>
          <th>Business Unit</th>
          <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</th>
          <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</th>
          <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</th>
          <th>Created Date</th>
          <th>Created By</th>
          <th hidden>De-Activated</th>
          <th hidden>Date of De-Activated</th>
          <th>Status</th>    
        </tr>
      <tfoot>

     
    </table> 
                                                    
  </div>
</div>


@endsection
@section('alert')
<!-- Alert -->
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
            <button class="btn alertbt" name='YesBtn' id="YesBtn" data-funcname="fnSaveData" style="display:none;"> 
                <div id="alert-active" class="activeYes"></div> Yes</button>
            <button class="btn alertbt" name='NoBtn' id="NoBtn"  data-funcname="fnUndoNo"  style="display:none;">
                <div id="alert-active" class="activeNo"></div>No</button>
            <button class="btn alertbt" name='OkBtn' id="OkBtn" style="margin-left: 90px;">
                <div id="alert-active" class="activeOk"></div>OK</button>
            <button class="btn alertbt" name='OkBtn1' id="OkBtn1" style="margin-left: 90px;display:none;">
                <div id="alert-active" class="activeOk"></div>OK</button>
        </div><!--btdiv-->
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<div id="alert" class="modal process_alert"  role="dialog"  data-backdrop="static" >
  <div class="modal-dialog" >
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='closePopup' >&times;</button>
        <h4 class="modal-title">System Alert Message</h4>
      </div>
      <div class="modal-body">
        <div style="height:100px;">
          <h5>Please wait your request is under process...</h5>
          <div class='loader' style="display:none;margin-left:45%;"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Alert -->
@endsection

@push('bottom-css')
<style>
  #custom_dropdown, #custlist_filter {
      display: inline-table;
      margin-left: 15px;
  }
  .dataTables_wrapper .row:nth-child(1) .col-sm-6:nth-child(2){text-align:right;}
  #filtercolumn{color: #555;
      background-color: #fff;
      background-image: none;
      border: 1px solid #ccc;
      }

  .table>tfoot>tr>th {
      vertical-align: bottom;
      border-bottom: 2px solid #ddd;
      font-size: 13px;
      color: #000 !important;
      font-weight: 400;
      padding: 5px;
  }

  .btn_search{
    height: 32px !important;
    cursor:pointer;
  }

.loader {
  border: 16px solid #f3f3f3;
  border-radius: 50%;
  border-top: 16px solid #3498db;
  width: 20px;
  height: 20px;
  -webkit-animation: spin 2s linear infinite; /* Safari */
  animation: spin 2s linear infinite;
}
/* Safari */
@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

</style>
@endpush

@push('bottom-scripts')
<script>
var limit   = 50;
var start   = 0;
var indexno = 0;
var action  = 'inactive';

function load_country_data(limit, start,indexno){

  var ICODE             = $.trim($("#ICODE").val());
  var NAME              = $.trim($("#NAME").val());
  var ITEM_DESC         = $.trim($("#ITEM_DESC").val());
  var ALPS_PART_NO      = $.trim($("#ALPS_PART_NO").val());
  var CUSTOMER_PART_NO  = $.trim($("#CUSTOMER_PART_NO").val());
  var OEM_PART_NO       = $.trim($("#OEM_PART_NO").val());
  var STATUS            = $.trim($("#STATUS").val());

  $(".loader").show();
  $(".process_alert").modal('show');  

  $.ajaxSetup({
    headers:{
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $.ajax({
    url:'{{route("master",[72,"getListingData"])}}',
    method:"POST",
    data:{ICODE:ICODE,NAME:NAME,ITEM_DESC:ITEM_DESC,ALPS_PART_NO:ALPS_PART_NO,CUSTOMER_PART_NO:CUSTOMER_PART_NO,OEM_PART_NO:OEM_PART_NO,STATUS:STATUS,limit:limit, start:start,indexno:indexno},
    cache:false,
    success:function(data){
      $('.dataTables_empty').remove();
      $('#load_data_message').html('');
      $('#load_data').append(data);
      

      $(".process_alert").modal('hide'); 
      $(".loader").hide(); 
    
      if(data == ''){
        action = 'active';
      }
      else{
        action = 'inactive';
      } 
      
    }
  });

}

$("#load_scroll").scroll(function(){
  if($("#load_scroll").scrollTop() + $("#load_scroll").height() > $("#load_data").height() && action == 'inactive'){
    action  = 'active';
    start   = start + limit;
    indexno = $("#custlist .participantRow").length;
    $("#BindRow").text(indexno+limit);

    setTimeout(function(){
      load_country_data(limit, start,indexno);
    }, 1000);
  }
});

function searchData(e,v){
  var charCode = e.which;
  if(e.which == 13){
    getListingData();
  }
}

function getListingData(){

  var ICODE             = $.trim($("#ICODE").val());
  var NAME              = $.trim($("#NAME").val());
  var ITEM_DESC         = $.trim($("#ITEM_DESC").val());
  var ALPS_PART_NO      = $.trim($("#ALPS_PART_NO").val());
  var CUSTOMER_PART_NO  = $.trim($("#CUSTOMER_PART_NO").val());
  var OEM_PART_NO       = $.trim($("#OEM_PART_NO").val());
  var STATUS            = $.trim($("#STATUS").val());

  $(".loader").show();
  $(".process_alert").modal('show');  

  
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $.ajax({
    url:'{{route("master",[72,"getListingData"])}}',
    type:'POST',
    data:{ICODE:ICODE,NAME:NAME,ITEM_DESC:ITEM_DESC,ALPS_PART_NO:ALPS_PART_NO,CUSTOMER_PART_NO:CUSTOMER_PART_NO,OEM_PART_NO:OEM_PART_NO,STATUS:STATUS,limit:'50',start:'0',indexno:'0'},
    success:function(data) {
      $('#load_data').html(data);
      $(".process_alert").modal('hide'); 
      $(".loader").hide();
    
      indexno = $("#custlist .participantRow").length;
      $("#BindRow").text(indexno);
         
      if(data == ''){
        action = 'active';
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Record not found.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
      }
      else{
        action = 'inactive';
      }

      bindTable();

    },
    error:function(data){
      console.log("Error: Something went wrong.");
    },
  });
}

function bindTable(){
  if ( $.fn.dataTable.isDataTable( '#custlist' ) ) {
    table = $('#custlist').DataTable();
  }
  else {
    table = $('#custlist').DataTable( {
      "paging":   false,
      "ordering": true,
      "info":     false,
      "searching":false,
    } );
  }
}

$(document).ready(function(){
  getListingData();

  $('.js-selectall').on('change', function() {
    var isChecked = $(this).prop("checked");
    var selector = $(this).data('target');
    $(selector).prop("checked", isChecked);
  });




      $('#btnEdit').on('click', function() {

            var countryIdsData = getSeletectedCBox();
            var seletedRecord = countryIdsData.length;

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

              var recordId = countryIdsData[0];
                  var is_approve = $('#chkId'+recordId).data("rcdstatus");
                  console.log("is app=="+is_approve);  

                  if(is_approve==0){

                    var editURL = '{{route("master",[72,"edit",":rcdId"]) }}';
                        editURL = editURL.replace(":rcdId",recordId);
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

      });//edit 

      $('#btnCopy').on('click', function() {

        var countryIdsData = getSeletectedCBox();
        var seletedRecord = countryIdsData.length;

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
          var recordId = countryIdsData[0];
              var is_approve = $('#chkId'+recordId).data("rcdstatus");
              console.log("is app=="+is_approve);

              if(is_approve==0){
                    var editURL = '{{route("master",[72,"copy",":rcdId"]) }}';
                    editURL = editURL.replace(":rcdId",recordId);
                    window.location.href=editURL;
              }else if(is_approve==2){

                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").hide();
                  $("#OkBtn1").show();
                  $("#AlertMessage").text('You cannot copy cancel record.');
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
              }else{

                var editURL = '{{route("master",[72,"copy",":rcdId"]) }}';
                    editURL = editURL.replace(":rcdId",recordId);
                    window.location.href=editURL;
              } 
        }

        });//copy
    

    $('#btnView').on('click', function() {

      var viewsIdsData = getSeletectedCBox();
      var seletedRecord = viewsIdsData.length;

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

            var viweRecordId = viewsIdsData[0];
            var viewURL = '{{route("master",[72,"view",":rcdId"]) }}';
                viewURL = viewURL.replace(":rcdId",viweRecordId);
                window.location.href=viewURL;
      }

    });//edit function


    $('#btnApprove').on('click', function() {
            var apprIdsData = getSeletectedCBox();
            var seletedRecord = apprIdsData.length;
            var apprIdsDataID = getSeletectedCBoxID();

            if(seletedRecord==0){
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text('Please select a record.');
                $("#alert").modal('show');
                $("#OkBtn1").focus();

            }else if(seletedRecord>1){
              
              var recordId = apprIdsDataID;
            
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
                $("#OkBtn").hide();
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
                
            }else{

                $("#alert").modal('show');
                $("#AlertMessage").text('Do you want to approve the record.');
                $("#YesBtn").data("funcname","fnMultiApproveData");  //set dynamic fucntion name
                $("#YesBtn").show();
                $("#NoBtn").show();
                $("#YesBtn").focus();

                $("#OkBtn").hide();
                highlighFocusBtn('activeYes');

            }

            }else if(seletedRecord==1){

              var recordId = apprIdsData[0];
                var is_approve = $('#chkId'+recordId).data("rcdstatus");
                console.log("is app=="+is_approve);  

                if(is_approve==0){

                  var editURL = '{{route("master",[72,"edit",":rcdId"]) }}';
                      editURL = editURL.replace(":rcdId",recordId);
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

    $('#btnAmendment').on('click', function() {
                
      var sqIdsData = getSeletectedCBox();
      var seletedRecord = sqIdsData.length;
      
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

        var recordId = sqIdsData[0];
        var is_approve = $('#chkId'+recordId).data("rcdstatus");
        console.log("is app=="+is_approve);  

        if(is_approve==1){

          var editURL = '{{route("master",[72,"amendment",":rcdId"]) }}';
              editURL = editURL.replace(":rcdId",recordId);
              window.location.href=editURL;
        }else if(is_approve==2){

            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text('You can not amend cancel record.');
            $("#alert").modal('show');
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk1');
        }
        if(is_approve==0){
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn1").show();
          $("#AlertMessage").text(' Only Approved record can be amended.');
          $("#alert").modal('show');
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk1');
        } 
      }
    });

    $('#btnCancel').on('click', function() {
          var cancelIdsData = getSeletectedCBox();
            var seletedRecord = cancelIdsData.length;

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

              var recordId = cancelIdsData[0];
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
            var countryIdsData = getSeletectedCBox();
            var seletedRecord = countryIdsData.length;

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

                  var recordId = countryIdsData[0];
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
                    var attachmentURL = '{{route("master",[72,"attachment",":rcdId"]) }}';
                        attachmentURL = attachmentURL.replace(":rcdId",recordId);
                        window.location.href=attachmentURL;

                  } 
            }
    });//Attachment 

    $('#btnImport').on('click', function() {
        var impURL =  '{{route("master",[72,"importdata"]) }}';
        window.location.href=impURL;

    });//ImportData       


function getSeletectedCBox(){ 

  var all_location_id = document.querySelectorAll('input[name="selectAll[]"]:checked');
  var aIds = [];
  for(var x = 0, l = all_location_id.length; x < l;  x++){
    aIds.push(all_location_id[x].value);
  }

  return aIds;
}

function getSeletectedCBoxID(){  

  var all_location_id = document.querySelectorAll('input[name="selectAll[]"]:checked');
  var aIds = [];
  for(var x = 0, l = all_location_id.length; x < l;  x++){
  aIds.push({'ID': all_location_id[x].value});
  }

  return aIds;
}

window.fnMultiApproveData = function (){

//validate and save data
event.preventDefault();
var forApprIdsDataID = getSeletectedCBoxID();
var recordId = forApprIdsDataID;
            $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
            });
              $.ajax({
                  url:'{{ route("master",[72,"MultiApprove"])}}',
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
                            if(data.resp=='duplicate') {
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
                           // window.location.href="{{ route('master',[72,'index']) }}";
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
            var canidsData = getSeletectedCBox();
            var seletedRecord = canidsData.length;
            var recordId = canidsData[0];
            $.ajaxSetup({
                              headers: {
                                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                              }
                          });
                          $.ajax({
                            url:'{{ route("mastermodify",[72,"cancel"])}}',
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
   window.location.href = '{{route("master",[72,"index"]) }}';
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


}); //reday


</script>

@endpush
