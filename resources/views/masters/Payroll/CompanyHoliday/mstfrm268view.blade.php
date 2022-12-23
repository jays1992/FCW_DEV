
@extends('layouts.app')
@section('content')
<!-- <form id="frm_mst_se" onsubmit="return validateForm()"  method="POST"  >     -->
 
    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[268,'index'])}}" class="btn singlebt">Company Holiday</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
                        <button class="btn topnavbt" id="btnSaveSE" disabled="disabled"><i class="fa fa-floppy-o"></i> Save</button>
                        <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt" id="btnUndo" disabled="disabled" ><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove" disabled="disabled" {{($objRights->APPROVAL1||$objRights->APPROVAL2||$objRights->APPROVAL3||$objRights->APPROVAL4||$objRights->APPROVAL5) == 1 ? '' : 'disabled'}}><i class="fa fa-thumbs-o-up"></i> Approved</button>
                        <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                        <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                        
                </div>
            </div><!--row-->
    </div><!--topnav-->	
    <!-- multiple table-responsive table-wrapper-scroll-y my-custom-scrollbar -->

     
<div class="container-fluid purchase-order-view filter">     
  <form id="frm_mst_comp_edit" method="POST"  > 
      @CSRF
      {{isset($objCondition->CP_HOLIDAYID) ? method_field('PUT') : '' }}
  <div class="inner-form">
    
  <div class="row">
    <div class="col-lg-2 pl"><p>Company Holiday Code*</p></div>
    <div class="col-lg-2 pl">
      @if(isset($objSON->SYSTEM_GRSR) && $objSON->SYSTEM_GRSR == "1")
      <input type="text" name="COMPANY_HOLIDAY_CODE" id="COMPANY_HOLIDAY_CODE" value="{{ $objCondition->COMPANY_HOLIDAY_CODE }}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase" disabled >
    @elseif(isset($objSON->MANUAL_SR) && $objSON->MANUAL_SR == "1")
      <input type="text" name="COMPANY_HOLIDAY_CODE" id="COMPANY_HOLIDAY_CODE" value="{{ old('COMPANY_HOLIDAY_CODE') }}" class="form-control mandatory" maxlength="{{isset($objSON->MANUAL_MAXLENGTH)?$objSON->MANUAL_MAXLENGTH:''}}" autocomplete="off" style="text-transform:uppercase" disabled >
    @else
      <input type="text" name="COMPANY_HOLIDAY_CODE" id="COMPANY_HOLIDAY_CODE" value="{{ $objCondition->COMPANY_HOLIDAY_CODE }}"  class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase"  disabled>
      @endif
    </div>
    <div class="col-lg-2 pl"><p>Company Holiday Date*</p></div>
      <div class="col-lg-2 pl">
      <input type="date" name="COMPANY_HOLIDAY_DATE" id="COMPANY_HOLIDAY_DATE" value="{{ $objCondition->COMPANY_HOLIDAY_DATE }}" class="form-control mandatory" autocomplete="off" placeholder="dd/mm/yyyy" disabled>
      </div> 
      
      <div class="col-lg-2 pl"><p>Financial Year*</p></div>
      <div class="col-lg-2 pl">
        <select name="FYID_REF" id="FYID_REF" class="form-control" disabled>
          <option value="" selected >Select</option>
          @foreach ($objFnlyearList as $key=>$val)
          <option {{isset($objCondition->FYID_REF) && $objCondition->FYID_REF == $val-> YRID ?'selected="selected"':''}} value="{{ $val-> YRID }}">{{ $val->YRCODE }}</option>
          @endforeach
        </select>
        <span class="text-danger" id="ERROR_FYID_REF"></span>                             
      </div>
  </div>
  
      <div class="row">
    <div class="table-responsive table-wrapper-scroll-y " style="height:330px;margin-top:10px;">
      Note:- 1 row mandatory in Tab
    <table id="example2" class="display nowrap table table-striped table-bordered itemlist itemlistscroll w-150" style="height:auto !important;">
      <thead id="thead1" style="position: sticky;top: 0; white-space:none;">
          <tr>
          <th width="27%" style="vertical-align:middle; font-family: 'Roboto',sans-serif; font-size:11px; font-weight: 600;">Holiday Date <input class="form-control" type="hidden" name="Row_Count" id ="Row_Count">  </th>
          <th width="16%"  style="vertical-align:middle; font-family: 'Roboto',sans-serif; font-size:11px; font-weight: 600;">Event of Holiday</th>
          <th width="51%" style="vertical-align:middle; font-family: 'Roboto',sans-serif; font-size:11px; font-weight: 600;">Type of Holiday</th>
          <th width="10%" style="vertical-align:middle; font-family: 'Roboto',sans-serif; font-size:11px; font-weight: 600;">Action</th>
          </tr>
        </thead>
        <tbody>
                      @if(!empty($objConditiontemp))
                      @foreach($objConditiontemp as $key => $row)
                      <td hidden><input  class="form-control" type="hidden" name="CP_HOLIDAYID_REF" id="CP_HOLIDAYID_REF" value="{{ $row->CP_HOLIDAYID_REF }}"></td>
                      <td hidden><input  class="form-control" type="hidden" name="CP_HOLIDAY_DET_ID_{{$key}}" id="CP_HOLIDAY_DET_ID_{{$key}}" maxlength="100" value="{{ $row->CP_HOLIDAY_DET_ID }}"></td>
          <tr class="participantRow">
          <td style="width:27%;"><input disabled class="form-control w-100" type="date" name="HOLIDAY_DATE_{{$key}}" id="HOLIDAY_DATE_{{$key}}" value="{{ $row->HOLIDAY_DATE }}" maxlength="200" autocomplete="off" style="text-transform:uppercase"></td>             
                      <td style="width:51%;"><input disabled class="form-control selvt" rows="1" name="HOLIDAY_EVENT_{{$key}}" id="drpvalue_{{$key}}" value="{{ $row->HOLIDAY_EVENT }}" maxlength="150" autocomplete="off" ></td> 
                      <td style="width:16%;">
                      <select class="form-control" name="HOLIDAYTYPEID_REF_{{$key}}" id="txtdesc_{{$key}}" disabled>
                      <option value="" selected >Select</option>
                      @foreach ($objHldTypeList as $key=>$val)
                          <option {{isset($row->HOLIDAYTYPEID_REF) && $row->HOLIDAYTYPEID_REF == $val-> HOLIDAYTYPEID ?'selected="selected"':''}} value="{{ $val-> HOLIDAYTYPEID }}">{{ $val->HOLIDAY_TYPE }}</option>
                          @endforeach
                      </select>
                      </td>
          <td style="width:10%;"><button disabled class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></button><button class="btn remove" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
          </tr>
          <tr></tr>

                      @endforeach 
                  @endif 
        </tbody>
      </table>
    </div>
  </div>
  </div>
  </form>
</div><!--purchase-order-view-->
@endsection
@section('alert')
<!-- Alert -->
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
          <button class="btn alertbt" name='YesBtn' id="YesBtn" data-funcname="fnSaveData">
          <div id="alert-active" class="activeYes"></div>Yes
          </button>
          <button class="btn alertbt" name='NoBtn' id="NoBtn"   data-funcname="fnUndoNo" >
          <div id="alert-active" class="activeNo"></div>No
          </button>
          <button class="btn alertbt" name='OkBtn' id="OkBtn" style="display:none;margin-left: 90px;">
          <div id="alert-active" class="activeOk"></div>OK</button>
          <button class="btn alertbt" name='OkBtn1' id="OkBtn1" onclick="getFocus()" style="display:none;margin-left: 90px;">
          <div id="alert-active" class="activeOk1"></div>OK</button>
          <input type="hidden" id="FocusId" >
      </div><!--btdiv-->
  <div class="cl"></div>
    </div>
  </div>
</div>
</div>
<!-- Alert -->



@endsection
<!-- btnSaveCountry -->
@push('bottom-css')
<style>
/* 

.select2-container__default .select2-results__group{
 color: #0f69cc;
} */


</style>
@endpush
@push('bottom-scripts')
<script>


    //delete row
  $("#example2").on('click', '.remove', function() {
  var rowCount = $(this).closest('table').find('tbody').length;
  if (rowCount > 1) {
  $(this).closest('tbody').remove();   
  } 
  if (rowCount <= 1) { 
  $(document).find('.remove').prop('disabled', false);  
  }
  event.preventDefault();
  });

  
function getFocus(){
var FocusId=$("#FocusId").val();
$("#"+FocusId).focus();
$("#closePopup").click();
}

//add row
      // $(".add").click(function() { 
      $("#example2").on('click', '.add', function() {
      var $tr = $(this).closest('table');
      var allTrs = $tr.find('tbody').last();      
      var lastTr = allTrs[allTrs.length-1];
      var $clone = $(lastTr).clone();
      $clone.find('td').each(function(){
          var el = $(this).find(':first-child');
          var id = el.attr('id') || null;
          if(id) {
              var i = id.substr(id.length-1);
              var prefix = id.substr(0, (id.length-1));
              el.attr('id', prefix+(+i+1));
          }
          var name = el.attr('name') || null;
          if(name) {
              var i = name.substr(name.length-1);
              var prefix1 = name.substr(0, (name.length-1));
              el.attr('name', prefix1+(+i+1));
          }
      });
      $clone.find('input:text').val('');
      $tr.closest('table').append($clone);         
      var rowCount = $('#Row_Count').val();
      rowCount = parseInt(rowCount)+1;
      $('#Row_Count').val(rowCount);
      $clone.find('.remove').removeAttr('disabled'); 
      $clone.find('[id*="txtdesc"]').val('');
      $clone.find('[id*="chkmdtry"]').prop('checked', false);
      event.preventDefault();
  });

// });
$(document).ready(function(e) {
var formConditionMst = $( "#frm_mst_comp_edit" );
formConditionMst.validate();

  $('#Row_Count').val("1");
$('#btnAdd').on('click', function() {
    var viewURL = '{{route("master",[268,"add"])}}';
                window.location.href=viewURL;
  });
  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
                window.location.href=viewURL;
  });

 


      //Terms & Condition code
      $("#COMPANY_HOLIDAY_CODE").blur(function(){
            $(this).val($.trim( $(this).val() ));
            $("#ERROR_COMPANY_HOLIDAY_CODE").hide();
            validateSingleElemnet("COMPANY_HOLIDAY_CODE");
              
          });

          $( "#COMPANY_HOLIDAY_CODE" ).rules( "add", {
              required: true,
              nowhitespace: true,
              StringNumberRegex: true, //from custom.js
              messages: {
                  required: "Required field.",
                  minlength: jQuery.validator.format("min {0} char")
              }
          });


         

});

  //validae single element
  function validateSingleElemnet(element_id){
    var validator =$("#frm_mst_comp_edit" ).validate();
       if(validator.element( "#"+element_id+"" )){
          //check duplicate code
        if(element_id=="LOBCOMPANY_HOLIDAY_CODE_NO" || element_id=="COMPANY_HOLIDAY_CODE" ) {
          checkDuplicateCode();
        }

       }
  }

  // //check duplicate Calculation code
  function checkDuplicateCode(){
      
      //validate and save data
      var conditionForm = $("#frm_mst_comp_edit");
      var formData = conditionForm.serialize();
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
      $.ajax({
          url:'{{route("master",[268,"codeduplicate"])}}',
          type:'POST',
          data:formData,
          success:function(data) {
              if(data.exists) {
                  $(".text-danger").hide();
                  showError('ERROR_COMPANY_HOLIDAY_CODE',data.msg);
                  $("#COMPANY_HOLIDAY_CODE").focus();
              }                                
          },
          error:function(data){
            console.log("Error: Something went wrong.");
          },
      });
  }

  //validate
  $( "#btnSave" ).click(function() {
      var formConditionMst = $("#frm_mst_comp_edit");
      if(formConditionMst.valid()){
              $("#FocusId").val('');
              var COMPANY_HOLIDAY_CODE          =   $.trim($("[id*=COMPANY_HOLIDAY_CODE]").val());
              var FYID_REF                      =   $.trim($("[id*=FYID_REF]").val());
              
              //var FOR_SALE          =   $("#chkforsale").is(":checked");
              //var FOR_PURCHASE      =   $("#chkforpurchase").is(":checked");
              if(COMPANY_HOLIDAY_CODE ===""){
                  $("#FocusId").val('COMPANY_HOLIDAY_CODE');
                  $("#ProceedBtn").focus();
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn1").show();
                  $("#OkBtn").hide();
                  $("#AlertMessage").text('Please enter value in Company Holiday Code.');
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  highlighFocusBtn('activeOk1');
                  return false;
              }
              if(FYID_REF ===""){
              $("#FocusId").val('FYID_REF');
              $("#ProceedBtn").focus();
              $("#FYID_REF").blur();
              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn1").show();
              $("#AlertMessage").text('Please Select Financial Year Option.');
              $("#alert").modal('show');
              $("#OkBtn1").focus();
              highlighFocusBtn('activeOk1');
              return false;
               }
              else
              {
                event.preventDefault();
                var allblank = [];
                var allblank2 = [];
                var allblank3 = [];
                    // $('#udfforsebody').find('.form-control').each(function () {
                    $("[id*=HOLIDAY_DATE]").each(function(){
                        if($(this).val()!="")
                        {
                            allblank3.push('true');
                            $('.selvt').each(function () {
                                var d_value = $(this).val();
                                if(d_value != ""){
                                    allblank.push('true');
                                    if(d_value != ""){
                                        if($(this).parent().parent().find('[id*="txtdesc"]').val() != "")
                                        {
                                        allblank2.push('true');
                                        }
                                        else{
                                        allblank2.push('false');
                                        }  
                                    }
                                }
                                else{
                                    allblank.push('false');
                                } 
                                
                                
                            });
                        }
                        else{
                                    allblank3.push('false');
                                } 
                    });

                    if(jQuery.inArray("false", allblank3) !== -1){
                            $("#alert").modal('show');
                            $("#AlertMessage").text('Please enter value in Holiday Date.');
                            $("#YesBtn").hide(); 
                            $("#NoBtn").hide();  
                            $("#OkBtn1").show();
                            $("#OkBtn1").focus();
                            $("#OkBtn").hide();
                            highlighFocusBtn('activeOk1');
                        }
                        else if(jQuery.inArray("false", allblank) !== -1){
                        $("#alert").modal('show');
                        $("#AlertMessage").text('Please select value in Event of Holiday.');
                        $("#YesBtn").hide(); 
                        $("#NoBtn").hide();  
                        $("#OkBtn1").show();
                        $("#OkBtn1").focus();
                        $("#OkBtn").hide();
                        highlighFocusBtn('activeOk');
                        }
                        else if(jQuery.inArray("false", allblank2) !== -1){
                        $("#alert").modal('show');
                        $("#AlertMessage").text('Please enter value in Type of Holiday.');
                        $("#YesBtn").hide(); 
                        $("#NoBtn").hide();  
                        $("#OkBtn1").show();
                        $("#OkBtn1").focus();
                        $("#OkBtn").hide();
                        highlighFocusBtn('activeOk');
                        }
                        else{

                            $("#alert").modal('show');
                            $("#AlertMessage").text('Do you want to save to record.');
                            $("#YesBtn").data("funcname","fnSaveData");  //set dynamic fucntion name
                            $("#YesBtn").focus();
                            $("#OkBtn1").hide();
                            $("#OkBtn").hide();
                            highlighFocusBtn('activeYes');

                        }
              }
      }  
  });//btnSaveCountry


  
  $("#YesBtn").click(function(){

      $("#alert").modal('hide');
      var customFnName = $("#YesBtn").data("funcname");
          window[customFnName]();

  }); //yes button


 window.fnSaveData = function (){

      //validate and save data
      event.preventDefault();

      var formConditionMst = $("#frm_mst_comp_edit");
      var formData = formConditionMst.serialize();
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
      $.ajax({
          url:'{{ route("mastermodify",[268,"update"]) }}',
          type:'POST',
          data:formData,
          success:function(data) {
             
              if(data.errors) {
                  $(".text-danger").hide();

                  if(data.errors.COMPANY_HOLIDAY_CODE){
                      showError('ERROR_COMPANY_HOLIDAY_CODE',data.errors.COMPANY_HOLIDAY_CODE);
                  }
                  
                 if(data.country=='duplicate') {

                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn1").show();
                    $("#OkBtn").hide();
                    $("#AlertMessage").text(data.msg);
                    $("#alert").modal('show');
                    $("#OkBtn1").focus();

                 }
                 if(data.save=='invalid') {

                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn1").show();
                    $("#OkBtn").hide();
                    $("#AlertMessage").text(data.msg);
                    $("#alert").modal('show');
                    $("#OkBtn1").focus();

                 }
              }
              if(data.success) {                   
                  console.log("succes MSG="+data.msg);                    
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").show();
                  $("#OkBtn1").hide();
                  $("#AlertMessage").text(data.msg);
                  $(".text-danger").hide();
                   $("#alert").modal('show');
                  $("#OkBtn").focus();

                //  window.location.href='{{ route("master",[4,"index"])}}';
              }
              
          },
          error:function(data){
            console.log("Error: Something went wrong.");
            
          },
      });
    
 } // fnSaveData

//validate and approve
$("#btnApprove").click(function() {        
    //set function nane of yes and no btn 
    $("#alert").modal('show');
    $("#AlertMessage").text('Do you want to save to record.');
    $("#YesBtn").data("funcname","fnApproveData");  //set dynamic fucntion name of approval
    $("#YesBtn").focus();
    highlighFocusBtn('activeYes');
  });//btnSave


  $("#YesBtn").click(function(){

    $("#alert").modal('hide');
    var customFnName = $("#YesBtn").data("funcname");
        window[customFnName]();

  }); //yes button


  $('#chkforsale').change(function()
  {
    if ($(this).is(':checked') == true) {
        $('#chkforpurchase').attr('disabled',true);
        $('#chkforpurchase').attr('checked',false);
        event.preventDefault();
    }
    else
    {
      $('#chkforpurchase').removeAttr('disabled');
      event.preventDefault();
    }
  });

  $('#chkforpurchase').change(function()
  {
    if ($(this).is(':checked') == true) {
        $('#chkforsale').attr('disabled',true);
        $('#chkforsale').attr('checked',false);
        event.preventDefault();
    }
    else
    {
      $('#chkforsale').removeAttr('disabled');
      event.preventDefault();
    }
  });

$('#example2').on("change",'[id*="decativateddate"]', function( event ) {
  var today = new Date();     //Mon Nov 25 2013 14:13:55 GMT+0530 (IST) 
  var d = new Date($(this).val()); 
  today.setHours(0, 0, 0, 0) ;
  d.setHours(0, 0, 0, 0) ;
  if (d < today) {
      $(this).val('');
      $("#alert").modal('show');
      $("#AlertMessage").text('Date cannot be less than Current date');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk1');
      event.preventDefault();
  }
  else
  {
      event.preventDefault();
  }           
});



  
  $("#NoBtn").click(function(){
  
    $("#alert").modal('hide');
    var custFnName = $("#NoBtn").data("funcname");
        window[custFnName]();

  }); //no button
 
  
  $("#OkBtn").click(function(){

      $("#alert").modal('hide');

      $("#YesBtn").show();  //reset
      $("#NoBtn").show();   //reset
      $("#OkBtn").hide();

      $(".text-danger").hide();
      window.location.href = '{{route("master",[268,"index"]) }}';
      
  });
  
  $("#OkBtn1").click(function(){

    $("#alert").modal('hide');

    $("#YesBtn").show();  //reset
    $("#NoBtn").show();   //reset
    $("#OkBtn").hide();

    $(".text-danger").hide();
    $("[id*='txtcmpt']").focus();
    
    });
   ///ok button

  
  
  $("#btnUndo").click(function(){

      $("#AlertMessage").text("Do you want to erase entered information in this record?");
      $("#alert").modal('show');

      $("#YesBtn").data("funcname","fnUndoYes");
      $("#YesBtn").show();

      $("#NoBtn").data("funcname","fnUndoNo");
      $("#NoBtn").show();
      
      $("#OkBtn").hide();
      $("#NoBtn").focus();
      highlighFocusBtn('activeNo');
      
  }); ////Undo button


  
  
  // save and approve 
  window.fnApproveData = function (){
      
      //validate and save data
      event.preventDefault();

      var getDataForm = $("#frm_mst_edit");
      var formData = getDataForm.serialize();
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
      $.ajax({
          url:'{{route("mastermodify",[268,"Approve"])}}',
          type:'POST',
          data:formData,
          success:function(data) {
             
              if(data.errors) {
                  $(".text-danger").hide();

                  if(data.errors.COMPANY_HOLIDAY_DATE){
                      showError('ERROR_COMPANY_HOLIDAY_DATE',data.errors.COMPANY_HOLIDAY_DATE);
                  }
                 if(data.exist=='norecord') {

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
                  $("#frm_mst_edit").trigger("reset");

                  $("#alert").modal('show');
                  $("#OkBtn").focus();

              }
              
          },
          error:function(data){
            console.log("Error: Something went wrong.");
          },
      });

  };// fnApproveData





 window.fnUndoYes = function (){
    
    //reload form
    window.location.href = "{{route('master',[268,'add'])}}";

 }//fnUndoYes


 window.fnUndoNo = function (){
    $("#txtctcode").focus();
 }//fnUndoNo

 function myFunction() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("glcodesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("example23");
      tr = table.getElementsByTagName("tr");
      for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[0];
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

function myNameFunction() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("glnamesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("example23");
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

function mybasisFunction() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("bscodesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("basisexample23");
      tr = table.getElementsByTagName("tr");
      for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[0];
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

function mybasisNameFunction() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("bsnamesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("basisexample23");
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


  function showError(pId,pVal){

    $("#"+pId+"").text(pVal);
    $("#"+pId+"").show();

  }//showError

  function highlighFocusBtn(pclass){
     $(".activeYes").hide();
     $(".activeNo").hide();
     
     $("."+pclass+"").show();
  }



  $(function() { $("#txtctcode").focus(); });

  // $(document).ready(function(){
  //  // Initialize select2
  //   $("[id*='drpbasis']").select2();
  // });
  
$(document).ready(function(e) {
var today = new Date(); 
var currentdate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
$('#COMPANY_HOLIDAY_DATE').val(currentdate);
});
  

</script>

@endpush