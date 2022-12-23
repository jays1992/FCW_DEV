@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Leave Approval</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
                        <button class="btn topnavbt" id="btnSaveSE" disabled="disabled"><i class="fa fa-save"></i> Save</button>
                        <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt" id="btnUndo"  disabled="disabled"><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-lock"></i> Approved</button>
                        <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                        <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                </div><!--col-10-->

            </div><!--row-->
    </div><!--topnav-->	
   
    <div class="container-fluid purchase-order-view filter">     
      <form id="frm_mst_edit" method="POST" onsubmit="return validateForm()" class="needs-validation"  > 
        @CSRF
        {{isset($objResponse->APPROVALID) ? method_field('PUT') : '' }}
        <div class="inner-form">
              
          <div class="row">
            <div class="col-lg-2 pl"><p>Employee Code*</p></div>
              <div class="col-lg-2 pl">
                <select name="EMPID_REF" id="EMPID_REF" class="form-control mandatory" onchange="getEmpCode(this.value)" tabindex="4" disabled>
                  <option value="" selected="">Select</option>
                  @foreach($objEmpList as $val)
                  <option {{isset($objResponse->EMPID_REF) && $objResponse->EMPID_REF == $val-> EMPID ?'selected="selected"':''}} value="{{ $val-> EMPID }}">{{ $val->EMPCODE }}</option>
                  @endforeach
                </select>
                <span class="text-danger" id="ERROR_PAYPERIODID_REF"></span>                             
              </div>

              <div class="col-lg-2 pl"><p>Name</p></div>
              <div class="col-lg-2 pl">
                <input type="text" id="FNAME" value="{{ $objLvDesList->FNAME }}" class="form-control" readonly  maxlength="100" disabled> 
              </div>
              <div class="col-lg-2 pl"><p>Leave Application No *</p></div>
              <div class="col-lg-2 pl">
                <input type="text" name="LEAVE_APPID_REF" id="txtitem_popup" value="{{ $ObjItem->LEAVE_APP_NO }}"  class="form-control mandatory" disabled autocomplete="off" readonly/>
                <input type="hidden" name="LEAVE_APPID" id="LEAVE_APPID" value="{{ $ObjItem->LEAVE_APPID }}" class="form-control" disabled autocomplete="off" />
                <input type="hidden" id="focusid" >
              <span class="text-danger" id="ERROR_EMPID_REF"></span>                             
            </div>
            </div>   
          
            <div class="row">
            <div class="col-lg-2 pl"><p>Leave Application Date</p></div>
              <div class="col-lg-2 pl">
                <input type="text" name="LEAVE_APP_DT" id="LEAVE_APP_DT" value="{{ $ObjItem->LEAVE_APP_DT }}" class="form-control" readonly  maxlength="100" disabled> 
              </div>

              <div class="col-lg-2 pl"><p>Pay Period Code</p></div>
              <div class="col-lg-2 pl">
                <input type="text" name="PAY_PERIOD_CODE" id="PAY_PERIOD_CODE" value="{{ $ObjItem->PAY_PERIOD_CODE }}" class="form-control" readonly  maxlength="100" disabled> 
              </div>

              <div class="col-lg-2 pl"><p>Description</p></div>
              <div class="col-lg-2 pl">
                <input type="text" name="PAY_PERIOD_DESC" id="PAY_PERIOD_DESC" value="{{ $ObjItem->PAY_PERIOD_DESC }}" class="form-control" readonly  maxlength="100" disabled> 
              </div>                    
          </div>

          <div class="row">
            <div class="col-lg-2 pl"><p>Leave Applied from Date</p></div>
              <div class="col-lg-2 pl">
                <input type="text" name="LEAVE_APP_FRDT" id="LEAVE_APP_FRDT" value="{{ $ObjItem->LEAVE_APP_FRDT }}" class="form-control" readonly  maxlength="100" disabled> 
              </div>

              <div class="col-lg-2 pl"><p>Leave Applied To Date</p></div>
              <div class="col-lg-2 pl">
                <input type="text" name="LEAVE_APP_TODT" id="LEAVE_APP_TODT" value="{{ $ObjItem->LEAVE_APP_TODT }}" class="form-control" readonly  maxlength="100" disabled> 
              </div>

              <div class="col-lg-2 pl"><p>Total Days</p></div>
              <div class="col-lg-2 pl">
                <input type="text" name="totalday" id="totalday" class="form-control" readonly  maxlength="100" disabled> 
              </div>                    
          </div>

          <div class="row">
            <div class="col-lg-2 pl"><p>Reason of Leave</p></div>
              <div class="col-lg-2 pl">
                <input type="text" name="REASON_LEAVE" id="REASON_LEAVE" value="{{ $ObjItem->REASON_LEAVE }}" class="form-control" readonly  maxlength="100" disabled> 
              </div>

              <div class="col-lg-2 pl"><p>Address During the leave </p></div>
              <div class="col-lg-2 pl">
                <input type="text" name="ADDRESS_LEAVE" id="ADDRESS_LEAVE" value="{{ $ObjItem->ADDRESS_LEAVE }}" class="form-control" readonly  maxlength="100" disabled> 
              </div>

              <div class="col-lg-2 pl"><p>Contact no During the leave </p></div>
              <div class="col-lg-2 pl">
                <input type="text" name="MONO_INLEAVE1" id="MONO_INLEAVE1" value="{{ $ObjItem->MONO_INLEAVE1 }}" class="form-control" readonly  maxlength="100" disabled> 
              </div>                    
          </div>
          <br>

          <div class="row">
          <div class="col-lg-2 pl"><p>Approved By </p></div>
              <div class="col-lg-2 pl">
                <input type="text" name="APPROVAL1_BY" id="APPROVAL1_BY" value="{{ $objResponse->APPROVAL1_BY }}"  class="form-control"  maxlength="100" disabled> 
              </div>

              <div class="col-lg-2 pl"><p>Date of Approval </p></div>
              <div class="col-lg-2 pl">
                <input type="date" name="APPROVAL1_DT" id="APPROVAL1_DT" value="{{ $objResponse->APPROVAL1_DT }}" class="form-control"  maxlength="100" disabled> 
              </div>

              <div class="col-lg-2 pl"><p>Approved</p></div>
              <div class="col-lg-2 pl">
                <input type="radio" name="APPROVAL1_ST" id="APPROVAL1_ST" value="{{ $objResponse->APPROVAL1_ST }}" {{isset($objResponse) && $objResponse->APPROVAL1_ST=='A'?'checked':''}} class="form-checkbox" disabled>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-2 pl"><p>Rejected</p></div>
              <div class="col-lg-2 pl">
              <input type="radio" name="APPROVAL1_ST" id="APPROVAL1_ST" value="{{ $objResponse->APPROVAL1_ST }}" {{isset($objResponse) && $objResponse->APPROVAL1_ST=='R'?'checked':''}} class="form-checkbox" disabled>
            </div>

            <div class="col-lg-2 pl"><p>Hold</p></div>
              <div class="col-lg-2 pl">
              <input type="radio" name="APPROVAL1_ST" id="APPROVAL1_ST" value="{{ $objResponse->APPROVAL1_ST }}" {{isset($objResponse) && $objResponse->APPROVAL1_ST=='H'?'checked':''}} class="form-checkbox" disabled>
            </div>

            <div class="col-lg-2 pl"><p>Partial Approved</p></div>
              <div class="col-lg-2 pl">
              <input type="radio" name="APPROVAL1_ST" id="APPROVAL1_ST" value="{{ $objResponse->APPROVAL1_ST }}" {{isset($objResponse) && $objResponse->APPROVAL1_ST=='P'?'checked':''}} class="form-checkbox" disabled>
            </div>
          </div>

          <div class="row">
          <div class="col-lg-2 pl"><p>Approved Days (Partial)</p></div>
              <div class="col-lg-2 pl">
                <input type="text" name="APPROVAL1_PARTIALDAYS" id="APPROVAL1_PARTIALDAYS" value="{{ $objResponse->APPROVAL1_PARTIALDAYS }}" onkeypress="return onlyNumberKey(event)" class="form-control"  maxlength="100" disabled> 
            </div>

            <div class="col-lg-2 pl"><p>Rejected / Partial Reason</p></div>
              <div class="col-lg-2 pl">
                <input type="text" name="APPROVAL1_REASON" id="APPROVAL1_REASON" value="{{ $objResponse->APPROVAL1_REASON }}" class="form-control"  maxlength="100" style="width: 430px;" disabled> 
            </div>
          </div>

        <div class="row">
          <div id="Material" class="tab-pane fade in active">
            <div class="row">
              <div class="col-lg-4" style="padding-left: 15px;"></div></div>
                <div class="table-responsive table-wrapper-scroll-y" style="height:500px;margin-top:10px;" >
                  <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                  <thead id="thead1"  style="position: sticky;top: 0">                      
                    <tr>                          
                    <th rowspan="2"  width="3%">Type of Leave <input class="form-control" type="hidden" name="Row_Count" id ="Row_Count" value="{{ $objCount }}"></th>                         
                    <th rowspan="2" width="3%">From Date</th>
                    <th rowspan="2" width="3%">To Date</th>
                    <th rowspan="2" width="3%">No of Days</th>
                    <th rowspan="2" width="3%">Remarks</th>
                    <th rowspan="2" width="3%">Leave Balance</th>
                  </tr>                      
                </thead>
                  <tbody>
                    @foreach($objDataResponse as $index=>$row)
                    <tr  class="participantRow">
                        <td>
                          <select name={{"LTID_REF_".$index}} id={{"LTID_REF_".$index}} class="form-control mandatory" tabindex="4" disabled>
                            <option value="" selected="">Select</option>
                            @foreach($objLvtypList as $val)
                            <option {{isset($row->LTID_REF) && $row->LTID_REF == $val-> LTID ?'selected="selected"':''}} value="{{ $val-> LTID }}">{{$val->LEAVETYPE_CODE}}- {{$val->LEAVETYPE_DESC}}</option>
                            @endforeach
                          </select>                               
                        <td>                              
                          <input  class="form-control" type="date" name={{"FROM_DT_".$index}} id={{"FROM_DT_".$index}} value="{{$row->FROM_DT}}" onchange="leaveFromDate(this.id)" autocomplete="off" style="width: 99%" disabled></td>
                        </td>
                        <td><input  class="form-control" type="date" name={{"TO_DT_".$index}} id={{"TO_DT_".$index}} value="{{$row->TO_DT}}" onchange="leaveFromDate(this.id)"  autocomplete="off" style="width: 99%" disabled></td></td>
                        <td><input type="text" name={{"totldays_".$index}} id={{"totldays_".$index}} class="form-control" readonly  maxlength="100" disabled> </td>
                        <td><input  class="form-control" type="text" name={{"REMARKS_".$index}} id={{"REMARKS_".$index}} value="{{$row->REMARKS}}" autocomplete="off" style="width: 99%" disabled></td>
                        <td><input  class="form-control" type="text" name={{"LEAVE_BLANCE_".$index}} id={{"LEAVE_BLANCE_".$index}} value="{{$row->LEAVE_BLANCE}}" onkeypress="return onlyNumberKey(event)" autocomplete="off" style="width: 99%" disabled></td>
                    </tr>
                    @endforeach 
                  </tbody>
                </table>
              </div>
          </div>
        </div>
    </div>
  </form>
</div><!--purchase-order-view-->

@endsection
@section('alert')
<!-- Alert -->
<div id="alert" class="modal"  role="dialog"  data-backdrop="static">
<div class="modal-dialog" >
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
      <button onclick="setfocus();"  class="btn alertbt" name='OkBtn' id="OkBtn" style="display:none;margin-left: 90px;">
      <div id="alert-active" class="activeOk"></div>OK</button>
      <button class="btn alertbt" name='OkBtn1' id="OkBtn1" style="display:none;margin-left: 90px;">
        <div id="alert-active" class="activeOk1"></div>OK</button>
      
  </div><!--btdiv-->
<div class="cl"></div>
</div>
</div>
</div>
</div>

<div id="proidpopup" class="modal" role="dialog"  data-backdrop="static">
<div class="modal-dialog modal-md " style="width:50%;">
<div class="modal-content">
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" id='gl_closePopup' >&times;</button>
</div>
<div class="modal-body">
<div class="tablename"><p>Leave Application</p></div>
<div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
<table id="ITEMProCodeTable" class="display nowrap table  table-striped table-bordered"style="width:100%;">
<thead>
<tr>
<th style="width:5%;text-align:center;" id="all-check_prodcode_item">Select</th>
<th style="width:10%;">Leave Application No</th>
</tr>
</thead>
<tbody>
<tr>
<td style="width:5%;text-align:center;"><span class="check_th">&#10004;</span></td>
<td style="width:10%;"><input type="text" id="ItemProCodeSearch" class="form-control" onkeyup="ItemProCodeFunction('{{$FormId}}')" ></td>
</tr>
</tbody>
</table>
<table id="ITEMProCodeTable2" class="display nowrap table  table-striped table-bordered" >
  <thead id="thead2">
  </thead>
  <tbody id="tbody_prod_code" style="font-size:12px;">  
  </tbody>
</table>
</div>
<div class="cl"></div>
</div>
</div>
</div>
</div>

<!-- Alert -->
@endsection
<!-- btnSave -->

@push('bottom-scripts')
<script>


function setfocus(){
var focusid=$("#focusid").val();
$("#"+focusid).focus();
$("#closePopup").click();
} 

function validateForm(){

$("#focusid").val('');
var EMPID_REF                 =   $.trim($("[id*=EMPID_REF]").val());
var txtitem_popup           =   $.trim($("#txtitem_popup").val());
$("#OkBtn1").hide();

if(EMPID_REF ===""){
$("#focusid").val('EMPID_REF');
$("#YesBtn").hide();
$("#NoBtn").hide();
$("#OkBtn1").hide();  
$("#OkBtn").show();              
$("#AlertMessage").text('Please enter Employee Code.');
$("#alert").modal('show');
$("#OkBtn").focus();
return false;
}
else if(txtitem_popup ===""){
$("#focusid").val('txtitem_popup');
$("#YesBtn").hide();
$("#NoBtn").hide();
$("#OkBtn1").hide();  
$("#OkBtn").show();              
$("#AlertMessage").text('Please enter Leave Application No.');
$("#alert").modal('show');
$("#OkBtn").focus();
return false;
}

else{
  event.preventDefault();
    var allblank1 = [];
    var focustext1= "";        
    $('#example2').find('.participantRow').each(function(){ 
    if($.trim($(this).find("[id*=LTID_REF]").val()) ==""){            
      allblank1.push('false');
      focustext1 = $(this).find("[id*=LTID_REF]").attr('id');
    }
});

  if(jQuery.inArray("false", allblank1) !== -1){
      $("#focusid").val(focustext1);
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").hide();  
      $("#OkBtn").show();
      $("#AlertMessage").text('Please select item in Material Tab.');
      $("#alert").modal('show');
      $("#OkBtn").focus();
      highlighFocusBtn('activeOk');
      return false;
    }
    else{
        $("#alert").modal('show');
        $("#AlertMessage").text('Do you want to save to record.');
        $("#YesBtn").data("funcname","fnSaveData");  
        $("#YesBtn").focus();
        highlighFocusBtn('activeYes');
    }

}

}

$('#btnAdd').on('click', function() {
var viewURL = '{{route("transaction",[404,"add"])}}';
window.location.href=viewURL;
});

$('#btnExit').on('click', function() {
var viewURL = '{{route('home')}}';
window.location.href=viewURL;
});

var formResponseMst = $( "#frm_mst_edit" );
formResponseMst.validate();
$("#DESCRIPTIONS").blur(function(){
  $(this).val($.trim( $(this).val() ));
  $("#ERROR_DESCRIPTIONS").hide();
  validateSingleElemnet("DESCRIPTIONS");
});

$( "#DESCRIPTIONS" ).rules( "add", {
  required: true,
  //StringRegex: true,  //from custom.js
  normalizer: function(value) {
      return $.trim(value);
  },
  messages: {
      required: "Required field."
  }
});

//validae single element
function validateSingleElemnet(element_id){
var validator =$("#frm_mst_edit" ).validate();
   if(validator.element( "#"+element_id+"" )){
      //check duplicate code
    if(element_id=="ATTCODE" || element_id=="attcode" ) {
      checkDuplicateCode();
    }

   }

}

// //check duplicate exist code
function checkDuplicateCode(){
  
  //validate and save data
  var getDataForm = $("#frm_mst_edit");
  var formData = getDataForm.serialize();
  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });
  $.ajax({
      url:'{{route("transaction",[404,"codeduplicate"])}}',
      type:'POST',
      data:formData,
      success:function(data) {
          if(data.exists) {
              $(".text-danger").hide();
              showError('ERROR_ATTCODE',data.msg);
              $("#ATTCODE").focus();
          }                                
      },
      error:function(data){
        console.log("Error: Something went wrong.");
      },
  });
}

//validate
$( "#btnSave" ).click(function() {
  if(formResponseMst.valid()){

      validateForm();

  }
});


//validate and approve
$("#btnApprove").click(function() {
    
    if(formResponseMst.valid()){
        //set function nane of yes and no btn 
        $("#alert").modal('show');
        $("#AlertMessage").text('Do you want to save to record.');
        $("#YesBtn").data("funcname","fnApproveData");  //set dynamic fucntion name of approval
        $("#YesBtn").focus();
        highlighFocusBtn('activeYes');

    }

});//btnSave



window.fnApproveData = function (){

event.preventDefault();
var trnsoForm = $("#frm_mst_edit");
var formData = trnsoForm.serialize();

$.ajaxSetup({
headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
}
});
$.ajax({
url:'{{ route("transactionmodify",[404,"Approve"])}}',
type:'POST',
data:formData,
success:function(data) {
  
    if(data.errors) {
        $(".text-danger").hide();

        if(data.errors.PAYPERIODID_REF){
            showError('ERROR_PAYPERIODID_REF',data.errors.PAYPERIODID_REF);
                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn1").show();
                    $("#AlertMessage").text('Please enter correct value in VQ NO.');
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

$("#YesBtn").click(function(){

  $("#alert").modal('hide');
  var customFnName = $("#YesBtn").data("funcname");
      window[customFnName]();

}); //yes button


window.fnSaveData = function (){

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
    url:'{{ route("transactionmodify",[404,"update"]) }}',
      type:'POST',
      data:formData,
      success:function(data) {
         
          if(data.errors) {
              $(".text-danger").hide();                    
              if(data.errors.DESCRIPTIONS){
                 // showError('ERROR_DESCRIPTIONS',data.errors.DESCRIPTIONS);
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn1").hide();
                  $("#OkBtn").show();
                  $("#AlertMessage").text("Attribute Description is "+data.errors.DESCRIPTIONS);
                  $("#alert").modal('show');
                  $("#OkBtn").focus();
              }
             if(data.exist=='duplicate') {

                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").hide();
                $("#OkBtn").show();

                $("#AlertMessage").text(data.msg);

                $("#alert").modal('show');
                $("#OkBtn").focus();

             }
             if(data.save=='invalid') {

                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").hide();
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
              $("#OkBtn1").show();
              $("#OkBtn").hide();

              $("#AlertMessage").text(data.msg);

              $(".text-danger").hide();
              $("#frm_mst_edit").trigger("reset");

              $("#alert").modal('show');
              $("#OkBtn1").focus();

            //  window.location.href='{{ route("transaction",[404,"index"])}}';
          }
          
      },
      error:function(data){
        console.log("Error: Something went wrong.");
      },
  });

} // fnSaveData


//delete row
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
}
event.preventDefault();
});


//add row

$("#Material").on('click','.add', function() {
    var $tr = $(this).closest('table');
    var allTrs = $tr.find('.participantRow').last();
    var lastTr = allTrs[allTrs.length-1];
    var $clone = $(lastTr).clone();

    $clone.find('td').each(function(){
      var el = $(this).find(':first-child');
      var id = el.attr('id') || null;
        if(id){
            var idLength = id.split('_').pop();
            var i = id.substr(id.length-idLength.length);
            var prefix = id.substr(0, (id.length-idLength.length));
            el.attr('id', prefix+(+i+1));
        }
        var name = el.attr('name') || null;
      if(name){
        var nameLength = name.split('_').pop();
        var i = name.substr(name.length-nameLength.length);
        var prefix1 = name.substr(0, (name.length-nameLength.length));
        el.attr('name', prefix1+(+i+1));
      }
    });

    $clone.find('input:text').val('');
    $clone.find('[id*="RFQID"]').val('');
    $clone.find('[id*="RFQID_REF"]').val('');
    $clone.find('[id*="PINO"]').val('');
    $clone.find('[id*="ITEMID_REF"]').val('');
    $tr.closest('table').append($clone);         
    var rowCount = $('#Row_Count').val();
    rowCount = parseInt(rowCount)+1;
    $('#Row_Count').val(rowCount);
    $clone.find('.remove').removeAttr('disabled');        
    event.preventDefault();
});

$("#Material").on('click', '.remove', function() {
    var rowCount = $(this).closest('table').find('.participantRow').length;
    if (rowCount > 1) {
        $(this).closest('.participantRow').remove();  
        var rowCount = $('#Row_Count').val();
        rowCount = parseInt(rowCount)-1;
        $('#Row_Count').val(rowCount);
    } 
    event.preventDefault();
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
  $("#OkBtn1").hide();

  $(".text-danger").hide();
  window.location.href = '{{route("transaction",[404,"index"]) }}';  
}); ///ok button



$("#btnUndo").click(function(){

  $("#AlertMessage").text("Do you want to erase entered information in this record?");
  $("#alert").modal('show');

  $("#YesBtn").data("funcname","fnUndoYes");
  $("#YesBtn").show();

  $("#NoBtn").data("funcname","fnUndoNo");
  $("#NoBtn").show();
  
  $("#OkBtn").hide();
  $("#OkBtn1").hide();
  $("#NoBtn").focus();
  highlighFocusBtn('activeNo');
  
}); ////Undo button


$("#OkBtn1").click(function(){

$("#alert").modal('hide');
$("#YesBtn").show();  //reset
$("#NoBtn").show();   //reset
$("#OkBtn").hide();
$("#OkBtn1").hide();
$(".text-danger").hide();
window.location.href = "{{route('transaction',[404,'index'])}}";

});



$("#OkBtn").click(function(){
$("#alert").modal('hide');

});////ok button


window.fnUndoYes = function (){

//reload form
window.location.href = "{{route('transaction',[404,'add'])}}";

}//fnUndoYes

function showError(pId,pVal){

$("#"+pId+"").text(pVal);
$("#"+pId+"").show();

}//showError

function highlighFocusBtn(pclass){
 $(".activeYes").hide();
 $(".activeNo").hide();
 
 $("."+pclass+"").show();
}  

//PROD CODE popoup click
$('#txtitem_popup').click(function(event){

if ($('#Tax_State').length) 
{
var taxstate = $('#Tax_State').val();
}
else
{
var taxstate = '';
}

var CODE = ''; 
var LEAVE_APP_DT = '';
var FORMID = "{{$FormId}}";
loadItem_prod_code(taxstate,CODE,FORMID,LEAVE_APP_DT); 

$("#proidpopup").show();
event.preventDefault();
});

$("#gl_closePopup").click(function(event){
$("#ItemProCodeSearch").val('');
$("#proidpopup").hide();
event.preventDefault();
});

function loadItem_prod_code(taxstate,CODE,FORMID,LEAVE_APP_DT){

var url	=	'<?php echo asset('');?>transaction/'+FORMID+'/getItemDetails_prod_code';

$("#tbody_prod_code").html('loading...');
$.ajaxSetup({
headers: {
  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
}
});
$.ajax({
url:url,
type:'POST',
data:{'taxstate':taxstate,'CODE':CODE,'LEAVE_APP_DT':LEAVE_APP_DT},
success:function(data) {
$("#tbody_prod_code").html(data); 
bindProdCode();

},
error:function(data){
  console.log("Error: Something went wrong.");
  $("#tbody_prod_code").html('');                        
},
});

}


function bindProdCode(){

$('#ITEMSCodeTable2').off(); 


$('[id*="chkIdProdCode"]').change(function(){

//var fieldid = $(this).attr('id');
var fieldid = $(this).parent().parent().attr('id');

var txtval          =    $("#txt"+fieldid+"").val();  
var LEAVE_APP_NO            =   $("#txt"+fieldid+"").data("code");
var LEAVE_APP_DT    =   $("#txt"+fieldid+"").data("lappdt");
var PAY_PERIOD_CODE =   $("#txt"+fieldid+"").data("paycode");
var PAY_PERIOD_DESC =   $("#txt"+fieldid+"").data("payid");
var LEAVE_APP_FRDT  =   $("#txt"+fieldid+"").data("lappfdt");
var LEAVE_APP_TODT  =   $("#txt"+fieldid+"").data("lapptdt");
var REASON_LEAVE    =   $("#txt"+fieldid+"").data("relv");
var ADDRESS_LEAVE   =   $("#txt"+fieldid+"").data("addlv");
var MONO_INLEAVE1   =   $("#txt"+fieldid+"").data("monoinlv1");

$('#txtitem_popup').val(LEAVE_APP_NO);
$('#LEAVE_APPID').val(txtval);
$("#proidpopup").hide();
$("#LEAVE_APP_DT").val(LEAVE_APP_DT);
$("#PAY_PERIOD_CODE").val(PAY_PERIOD_CODE); 
$("#PAY_PERIOD_DESC").val(PAY_PERIOD_DESC);
$("#LEAVE_APP_FRDT").val(LEAVE_APP_FRDT);
$("#LEAVE_APP_TODT").val(LEAVE_APP_TODT);
$("#REASON_LEAVE").val(REASON_LEAVE);
$("#ADDRESS_LEAVE").val(ADDRESS_LEAVE);
$("#MONO_INLEAVE1").val(MONO_INLEAVE1);

var lfromTodate   = new Date(LEAVE_APP_TODT) - new Date(LEAVE_APP_FRDT);
var totalDays = lfromTodate / (1000 * 60 * 60 * 24);
$("#totalday").val(totalDays);
$("#ItemProCodeSearch").val('');
event.preventDefault();
});
}

function getEmpCode(EMPID){
$.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  $.ajax({
      url:'{{route("transaction",[404,"getEmpCode"])}}',
      type:'POST',
      data:{EMPID:EMPID},
      success:function(data) {
         $("#FNAME").val(data);                
      },
      error:function(data){
        console.log("Error: Something went wrong.");
      },
  });	
}

function getLeaveTyName(id,LTID){

var ROW_ID = id.split('_').pop();

$.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  $.ajax({
      url:'{{route("transaction",[404,"getLeaveTyName"])}}',
      type:'POST',
      data:{LTID:LTID},
      success:function(data) {
         $('#LEAVETYPE_DESC_'+ROW_ID+'').val(data);                
      },
      error:function(data){
        console.log("Error: Something went wrong.");
      },
  });	
}

function getAplnNoName(LEAVE_APPID){

$.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  $.ajax({
      url:'{{route("transaction",[404,"getAplnNoName"])}}',
      type:'POST',
      data:{LEAVE_APPID:LEAVE_APPID},
      success:function(data) {
         $("#PAYPERIODID_REF").val(data); 
      },
      error:function(data){
        console.log("Error: Something went wrong.");
      },
  });	
}

function leaveFromDate(id){
var ROW_ID = id.split('_').pop();
var FROM_DT    =   $('#FROM_DT_'+ROW_ID+'').val();
var TO_DT    =   $('#TO_DT_'+ROW_ID+'').val();
var LEAVE_BLANCE    =   $('#LEAVE_BLANCE_'+ROW_ID+'').val();
if((new Date(TO_DT)) < (new Date(FROM_DT))){
    $("#FocusId").val('TO_DT');    
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('LA From Date Greater Then LA To Date.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    var TO_DT    =   $('#TO_DT_'+ROW_ID+'').val('');
    return false;
  } else{
  var lfromTodate   = new Date(TO_DT) - new Date(FROM_DT);
  var totalDays = (lfromTodate / (1000 * 60 * 60 * 24)+1);
  var totleave = LEAVE_BLANCE - totalDays;
  if(isNaN(totalDays)){ return 0;}
  $('#totldays_'+ROW_ID+'').val(totalDays);
  $('#LEAVE_BLANCE_'+ROW_ID+'').val(totleave);  
  }
}
$(document).ready(function() {
var LEAVE_APP_FRDT    =   $('#LEAVE_APP_FRDT').val();
var LEAVE_APP_TODT    =   $('#LEAVE_APP_TODT').val();
var lfromTodate   = new Date(LEAVE_APP_TODT) - new Date(LEAVE_APP_FRDT)
var totalDays = (lfromTodate / (1000 * 60 * 60 * 24)+1);
$("#totalday").val(totalDays);
$('#example2').find('.participantRow').each(function(){
var txt_id4 = $(this).find('[id*="FROM_DT"]').attr('id');
var ROW_ID = txt_id4.split('_').pop();
var FROM_DT   = $(this).find('[id*="FROM_DT"]').val();
var TO_DT   = $(this).find('[id*="TO_DT"]').val();
var LEAVE_BLANCE   = $(this).find('[id*="LEAVE_BLANCE"]').val();
if((new Date(TO_DT)) < (new Date(FROM_DT))){
    $("#FocusId").val('TO_DT');    
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('LA From Date Greater Then LA To Date.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    var TO_DT    =   $('#TO_DT_'+ROW_ID+'').val('');
    return false;
  } else{
var lfromTodate   = new Date(TO_DT) - new Date(FROM_DT)
var totalDays = (lfromTodate / (1000 * 60 * 60 * 24)+1);
var totleave = LEAVE_BLANCE - totalDays;
$('#totldays_'+ROW_ID+'').val(totalDays);
$('#LEAVE_BLANCE_'+ROW_ID+'').val(totleave);
  } 
}); 

});

</script>

<script>
function onlyNumberKey(evt) {
  var ASCIICode = (evt.which) ? evt.which : evt.keyCode
  if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
      return false;
  return true;
}
</script>

@endpush