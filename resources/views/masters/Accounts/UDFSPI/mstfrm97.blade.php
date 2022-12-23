
@extends('layouts.app')
@section('content')
    
    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[97,'index'])}}" class="btn singlebt">UDF for Service Purchase<br/> Invoice</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                  <a href="{{route('master',[97,'add'])}}" id="btnSelectedRows" class="btn topnavbt" {{isset($objRights->ADD) && $objRights->ADD != 1 ? 'disabled' : ''}}><i class="fa fa-plus"></i> Add</a>
                  <button class="btn topnavbt" id="btnEdit" {{isset($objRights->EDIT)  && $objRights->EDIT != 1 ? 'disabled' : ''}}><i class="fa fa-edit"></i> Edit</button>
                  <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-save"></i> Save</button>
                  <button class="btn topnavbt" id="btnView" {{isset($objRights->VIEW) && $objRights->VIEW != 1 ? 'disabled' : ''}}><i class="fa fa-eye"></i> View</button>
                  <button class="btn topnavbt" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                  <button class="btn topnavbt" disabled="disabled"><i class="fa fa-undo"></i> Undo</button>
                  <button class="btn topnavbt" id="btnCancel" {{isset($objRights->CANCEL) && $objRights->CANCEL != 1 ? 'disabled' : ''}}><i class="fa fa-times"></i> Cancel</button>            
                  <button class="btn topnavbt" id="btnApprove" {{ (isset($objRights->APPROVAL1) || isset($objRights->APPROVAL2) || isset($objRights->APPROVAL3) || isset($objRights->APPROVAL4) || isset($objRights->APPROVAL5)) &&  ($objRights->APPROVAL1||$objRights->APPROVAL2||$objRights->APPROVAL3||$objRights->APPROVAL4||$objRights->APPROVAL5) == 1 ? '' : 'disabled'}} ><i class="fa fa-thumbs-o-up"></i> Approved</button>
                  <button class="btn topnavbt"  id="btnAttach" {{isset($objRights->ATTECHMENT) && $objRights->ATTECHMENT != 1 ? 'disabled' : ''}}><i class="fa fa-link"></i> Attachment</button>
                  <a href="{{route('home')}}" class="btn topnavbt"><i class="fa fa-power-off"></i> Exit</a>
                </div>
            </div><!--row-->
    </div><!--topnav-->	
    <!-- multiple table-responsive table-wrapper-scroll-y my-custom-scrollbar -->

    <div class="container-fluid purchase-order-view">
        <div class="multiple table-responsive  ">
              <table id="udfforlistingmst" class="display nowrap table table-striped table-bordered" width="100%">
            <thead id="thead1">
            <tr>
                        <th id="all-check" style="width:50px;"><input type="checkbox" class="js-selectall" data-target=".js-selectall1" />Select</th>
                        <th width="24%">Label</th>
                        <th width="16%">Value Type</th>
                        <th width="41%">Description</th>
                        <th width="16%">Is Mandatory</th>
                        <th width="16%">De-Activated</th>
                        <th width="16%">Date of De-Activated</th>
                        <th width="13%">Status</th>

            </tr>
            </thead>
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
@endsection

@push('bottom-css')
<style>
#custom_dropdown, #udfforlistingmst_filter {
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
      var viewURL = '{{route("master",[97,"add"])}}';
                  window.location.href=viewURL;
    });
    $('#btnExit').on('click', function() {
      var viewURL = '{{route('home')}}';
                  window.location.href=viewURL;
    });
        // Data table for serverside
         var mstudfforListTable =  $('#udfforlistingmst').DataTable({
            "processing": true,
            "serverSide": true,
            initComplete: function() {
                      $('.dataTables_filter input').unbind();
                      $('.dataTables_filter input').bind('keyup', function(e){
                          var code = e.keyCode || e.which;
                          if (code == 13) { 
                            mstudfforListTable.search(this.value).draw();
                          }
                      });
                   },
            "ajax":{

                     "url": "{{ route('mastergetlist',[97,'getListing']) }}",
                     "dataType": "json",
                     "type": "POST",
                      "data": function ( d ) {
                                  d._token= "{{csrf_token()}}";
                                  d.filtercolumn = $('#filtercolumn option:selected').val();
                              }
                   },
            "columns": [

                { "data": "NO" },
                { "data": "LABEL" },
                { "data": "VALUETYPE" },
                { "data": "DESCRIPTIONS" },
                { "data": "ISMANDATORY" },
                { "data": "DEACTIVATED" },
                { "data": "DODEACTIVATED" },
                { "data": "STATUS" }

            ],
            "order": [[ 1, "asc" ]],
            "lengthMenu": [ 12, 18, 27, 50, 100 ],
            "iDisplayLength": 12,
            aoColumnDefs: [
            {
               bSortable: false,
               aTargets: [ 0]
            },
          ] 
       
        }); //datatable

      //custom dropdown for filter  
      var htmlDD =  '<div id="custom_dropdown"><div id="udfforlistingmst_dropdown" class="dataTables_filter">'
        +'<select id="filtercolumn" name="filtercolumn">'
        +'<option value="ALL" > All </option>'
        +'<option value="LABEL" > Label </option>'
        +'<option value="VALUETYPE"> ValueType </option>'
        +'<option value="DESCRIPTIONS"> Description </option>'
        +'<option value="ISMANDATORY"> Is Mandatory </option>'
        +'<option value="DEACTIVATED"> Deactivated </option>'
        +'<option value="DODEACTIVATED"> Date of Deactivation </option>'
        +'<option value="STATUS"> Status</option>'
        +'</select></div></div>';
      
          $( htmlDD ).insertBefore( "#udfforlistingmst_wrapper #udfforlistingmst_filter" ); 

      
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
          var udflistIdsData = getSeletectedCBox();
            var seletedRecord = udflistIdsData.length;

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

                  var recordId = udflistIdsData[0];
                  var viewURL = '{{route("master",[97,"view",":rcdId"]) }}';
                  viewURL = viewURL.replace(":rcdId",recordId);
                  window.location.href=viewURL;
            }
      });

      $('#btnAttach').on('click', function() {
            var udflistIdsData = getSeletectedCBox();
            var seletedRecord = udflistIdsData.length;

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

                  var recordId = udflistIdsData[0];
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
                    var attachmentURL = '{{route("master",[97,"attachment",":rcdId"]) }}';
                        attachmentURL = attachmentURL.replace(":rcdId",recordId);
                        window.location.href=attachmentURL;

                  }
            }
      });

      $('#btnCancel').on('click', function() {
            var udflistIdsData = getSeletectedCBox();
            var seletedRecord = udflistIdsData.length;

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
                  var recordId = udflistIdsData[0];
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
                    event.preventDefault();
                    $("#alert").modal('show');
                    $("#AlertMessage").text('Do you want to cancel the record.');
                    $("#YesBtn").data("funcname","fnCancelData"); 
                    $("#YesBtn").focus();
                    highlighFocusBtn('activeYes');
                  }        
            }
      });


      $('#btnEdit').on('click', function() {

            
            var udflistIdsData = getSeletectedCBox();
            var seletedRecord = udflistIdsData.length;

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

                  var recordId = udflistIdsData[0];
                  var is_approve = $('#chkId'+recordId).data("rcdstatus");
                  console.log("is app=="+is_approve);  

                  if(is_approve==0){

                    var editURL = '{{route("master",[97,"edit",":rcdId"]) }}';
                        editURL = editURL.replace(":rcdId",recordId);
                        window.location.href=editURL;
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

        $('#btnApprove').on('click', function() {

          var udflistIdsData = getSeletectedCBox();
          var seletedRecord = udflistIdsData.length;
          var udflistIdsDataID = getSeletectedCBoxID();
          

          if(seletedRecord==0){

                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text('Please select a record.');
                $("#alert").modal('show');
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk1');
          }else if(seletedRecord>1){
            
            var recordId = udflistIdsDataID;
            
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

          }else if(seletedRecord==1){

                var recordId = udflistIdsData[0];
                var is_approve = $('#chkId'+recordId).data("rcdstatus");
                console.log("is app=="+is_approve);  

                if(is_approve==0){

                  var editURL = '{{route("master",[97,"edit",":rcdId"]) }}';
                      editURL = editURL.replace(":rcdId",recordId);
                      window.location.href=editURL;
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


var selectedIds = {};
selectedIds = {pl:[], p2:[]};/* add property "pl" who's value is empty array*/

//get selected check boxes
function getSeletectedCBox(){       
        selectedIds=[];
            var checkedcollection = mstudfforListTable.$(".js-selectall1:checked", { "page": "all" });
              checkedcollection.each(function (index, elem) {
              selectedIds.push($(elem).val());
            });
            return selectedIds;          
      }

      function getSeletectedCBoxID(){       
        selectedIds=[];
            var checkedcollection = mstudfforListTable.$(".js-selectall1:checked", { "page": "all" });
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
var udflistIdsDataID = getSeletectedCBoxID();
var recordId = udflistIdsDataID;
            $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
            });
              $.ajax({
                  url:'{{ route("master",[97,"MultiApprove"])}}',
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
                            if(data.reqdata=='duplicate') {
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
                            // window.location.href="{{ route('master',[97,'index']) }}";
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
            var udflistIdsData = getSeletectedCBox();
            var seletedRecord = udflistIdsData.length;
            var recordId = udflistIdsData[0];
            $.ajaxSetup({
                              headers: {
                                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                              }
                          });
                          $.ajax({
                            url:'{{ route("mastermodify",[97,"cancel"])}}',
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
                                      if(data.reqdata=='duplicate') {
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
                                      // window.location.href="{{ route('master',[97,'index']) }}";
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
    window.location.href = '{{route("master",[97,"index"]) }}';
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
    
});  //ready
</script>

@endpush