@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[$FormId,'index'])}}" class="btn singlebt">Employee Target Master</a>
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
                        <button class="btn topnavbt" id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                        <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                </div><!--col-10-->

            </div><!--row-->
    </div><!--topnav-->	
    
    <div class="container-fluid purchase-order-view filter">     
      <form id="frm_mst_edit" method="POST"> 
        @CSRF
        {{isset($objResponse->ID) ? method_field('PUT') : '' }}
       <div class="inner-form">
             <div class="row">
               <div class="col-lg-2 pl"><p>Employee Target Master Code</p></div>
               <div class="col-lg-2 pl">
                 <div class="col-lg-11 pl">
                   <input {{$ActionStatus}} type="text" name="EMPLOYEE_TARGETCODE" id="EMPLOYEE_TARGETCODE" value="{{isset($objResponse->EMPLOYEE_TARGETCODE) && $objResponse->EMPLOYEE_TARGETCODE !=''?$objResponse->EMPLOYEE_TARGETCODE:''}}"  class="form-control mandatory"  autocomplete="off" style="text-transform:uppercase" readonly >
                 </div>
               </div>

               <div class="col-lg-2 pl"><p>Employee Target Master Name</p></div>
               <div class="col-lg-2 pl">
                 <input {{$ActionStatus}} type="text" name="EMPLOYEE_TARGETNAME" id="EMPLOYEE_TARGETNAME" onclick="getEmpTargetName(this.id,'{{route('master',[$FormId,'getEmpTargetName'])}}','Employee Target Details')" value="{{isset($objResponse->EMPCODE) && $objResponse->EMPCODE !=''?$objResponse->EMPCODE:''}}-{{isset($objResponse->FNAME) && $objResponse->FNAME !=''?$objResponse->FNAME:''}}" class="form-control mandatory" readonly />
                 <input {{$ActionStatus}} type="hidden" name="EMPLOYEE_TARGET_REF" id="EMPLOYEE_TARGET_REF" value="{{isset($objResponse->EMPLOYEE_TARGETNAME) && $objResponse->EMPLOYEE_TARGETNAME !=''?$objResponse->EMPLOYEE_TARGETNAME:''}}" class="form-control" autocomplete="off" />
               </div>

               <div class="col-lg-2 pl"><p>Financial Year</p></div>
               <div class="col-lg-2 pl">
                 <input {{$ActionStatus}} type="text" name="FINALYEAR" id="FINALYEAR" onclick="getEmpTargetName(this.id,'{{route('master',[$FormId,'getFinancialYearCode'])}}','Financial Year Details')" value="{{ $objResponse->FYCODE }} - {{ $objResponse->FYDESCRIPTION }}" class="form-control mandatory"  autocomplete="off" readonly/>
                 <input type="hidden" name="FYID_REF1" id="FYID_REF1" value="{{isset($objResponse->FYID_REF) && $objResponse->FYID_REF !=''?$objResponse->FYID_REF:''}}" class="form-control" autocomplete="off" />
               </div>
             </div>

             <div class="row">
               <div class="col-lg-2 pl"><p>Total Amount</p></div>
               <div class="col-lg-2 pl">
                 <input {{$ActionStatus}} type="text" name="TARGET_AMOUNT" id="TARGET_AMOUNT" value="{{isset($objResponse->TARGET_AMOUNT) && $objResponse->TARGET_AMOUNT !=''?$objResponse->TARGET_AMOUNT:''}}" onkeypress="return onlyNumberKey(event)" class="form-control mandatory" readonly />
               </div>
               
               <div class="col-lg-2 pl"><p>Employee Target Type</p></div>
                <div class="col-lg-2 pl">
                  <select {{$ActionStatus}} name="EMPTARGETTYPE" id="EMPTARGETTYPE" class="form-control"  autocomplete="off">
                    <option value="">Select</option>
                    <option {{isset($objResponse->EMPLOYEE_TYPE) && $objResponse->EMPLOYEE_TYPE == 'DEMO'?'selected="selected"':''}} value="DEMO">Demo</option>
                    <option {{isset($objResponse->EMPLOYEE_TYPE) && $objResponse->EMPLOYEE_TYPE == 'EMPLOYEE'?'selected="selected"':''}} value="EMPLOYEE">Employee</option>
                    </select> 
                </div>

               <div class="col-lg-2 pl"><p>De-Activated</p></div>
                <div class="col-lg-1 pl pr">
                <input {{$ActionStatus}} type="checkbox"   name="DEACTIVATED"  id="deactive-checkbox_0" {{$objResponse->DEACTIVATED == 1 ? "checked" : ""}}
                value='{{$objResponse->DEACTIVATED == 1 ? 1 : 0}}' tabindex="2"  >
                </div>
              </div>

              <div class="row">
                <div class="col-lg-2 pl"><p>Date of De-Activated</p></div>
                <div class="col-lg-2 pl">
                  <input {{$ActionStatus}} type="date" name="DODEACTIVATED" class="form-control" id="DODEACTIVATED" {{$objResponse->DEACTIVATED == 1 ? "" : "disabled"}} value="{{isset($objResponse->DODEACTIVATED) && $objResponse->DODEACTIVATED !="" && $objResponse->DODEACTIVATED !="1900-01-01" ? $objResponse->DODEACTIVATED:''}}" tabindex="3" placeholder="dd/mm/yyyy"  />
                </div>
              </div>
             


             <div class="row">
               <ul class="nav nav-tabs">
                 <li class="active"><a data-toggle="tab" href="#Material">Details</a></li>
                 <li><a data-toggle="tab" href="#Product">Product</a></li>
               </ul>
               Note:- 1 row mandatory in Tab
               <div class="tab-content">
               <div id="Material" class="tab-pane fade in active">
                   <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:280px;margin-top:10px;" >
                     <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                       <thead id="thead1"  style="position: sticky;top: 0">                      
                         <tr>
                           <th colspan="20">Details Month Amount</th>
                         </tr>
                         <tr>
                         <?php for($i=1; $i<=12; $i++) { ?>                           
                         <th rowspan="2"  width="3%">{{$i}}-Month</th>                         
                         <?php } ?>  
                       </tr>                      
                         
                     </thead>
                       <tbody>
                         <tr  class="participantRow">
                         <?php for($i=1; $i<=12; $i++) { 
                           $Field_Amt  ='MONTH'.$i.'_AMT';
                          ?>
                           <td><input {{$ActionStatus}} class='form-control txtCal' type="text" name="MONTH{{$i}}_AMT[]"   id ="MONTH{{$i}}_AMT_{{$i}}" value="{{$MAT->$Field_Amt}}" onkeypress="return onlyNumberKey(event)" autocomplete="off" style="width: 99%"></td>
                         <?php } ?>
                         </tr>
                       </tbody>
                     </table>
                 </div>	
             </div>

                 <div id="Product" class="tab-pane fade in">
                     <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:280px;margin-top:10px;" >
                       <table id="example3" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                         <thead id="thead1"  style="position: sticky;top: 0">                      
                           <tr>
                             <th colspan="20">Product Month Quantity</th>
                           </tr>
                           <tr>                          
                           <th rowspan="2"  width="3%">Product Code</th>
                           <?php for($i=1; $i<=12; $i++) { ?>                         
                           <th rowspan="2"  width="3%">{{$i}}-Month</th>
                           <?php } ?>  
                           <th rowspan="2"  width="5%">Action</th>                        
                         </tr>                      
                           
                       </thead>
                         <tbody>
                          @if(isset($MAT1) && !empty($MAT1))
                          @foreach($MAT1 as $key => $row)
                           <tr  class="participantRow">
                             <td><input {{$ActionStatus}}  class="form-control" type="text" name="PRODCTCODE[]"   id ="PRODCTCODE_{{$key}}" onclick="getEmpTargetName(this.id,'{{route('master',[$FormId,'getProdctCode'])}}','Product Code Details')" value="{{isset($row->ICODE) && $row->ICODE !=''?$row->ICODE:''}} {{isset($row->NAME) && $row->NAME !=''?'- '.$row->NAME:''}}"  autocomplete="off" readonly style="width: 99%"></td>
                             <td hidden><input   class="form-control" type="hidden" name="ITEMID_REF[]" id ="HIDDEN_PRODCTCODE_{{$key}}" value="{{isset($row->ITEMID_REF) && $row->ITEMID_REF !=''?$row->ITEMID_REF:''}}"  autocomplete="off" readonly style="width: 99%"></td>
                             <?php for($i=1; $i<=12; $i++) { 
                               $Field_Qty  ='MONTH'.$i.'_QTY';
                               ?>
                             <td><input {{$ActionStatus}} class="form-control" type="text" name="MONTH{{$i}}_QTY[]"   id ="MONTH1_QTY_{{$i}}" value="{{$row->$Field_Qty}}"  onkeypress="return onlyNumberKey(event)" autocomplete="off" style="width: 99%"></td>
                             <?php } ?>
                             <td align="center">
                              <button {{$ActionStatus}} class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></button>
                              <button {{$ActionStatus}} class="btn remove" title="Delete" data-toggle="tooltip" disabled><i class="fa fa-trash" ></i></button>
                            </td>
                           </tr>
                           @endforeach
                          @endif
                         </tbody>
                       </table>
                     </div>	
                 </div>
             </div>
       </div>
     </div>
   </form>
 </div>

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
           <input type="hidden" id="focusid" >
         
     </div><!--btdiv-->
     <div class="cl"></div>
   </div>
 </div>
</div>
</div>

<!------------------------------- All Popup Modal ---------------------------------->
<div id="modalpopup" class="modal" role="dialog"  data-backdrop="static">
<div class="modal-dialog modal-md column3_modal">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" id='modalclosePopup' >&times;</button>
</div>
<div class="modal-body">
<div class="tablename"><p id="tital_Name"></p></div>
<div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
<table id="MachTable" class="display nowrap table  table-striped table-bordered">
<thead>
<tr>
<th class="ROW1">Select</th> 
<th class="ROW2">Code</th>
<th  class="ROW3">Description</th>
</tr>
</thead>
<tbody>
<tr>
<td class="ROW1"><span class="check_th">&#10004;</span></td>
<td class="ROW2">
<input type="text" autocomplete="off"  class="form-control" id="fyearcodesearch"  onkeyup='colSearch("fyeartab2","fyearcodesearch",1)' />
</td>
<td class="ROW3">
<input type="text" autocomplete="off"  class="form-control" id="fyearnamesearch"  onkeyup='colSearch("fyeartab2","fyearnamesearch",2)' />
</td>
</tr>
</tbody>
</table>
<table id="fyeartab2" class="display nowrap table  table-striped table-bordered">
<thead id="thead2">
</thead>
<tbody id="getData_tbody">       
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

/*************************************   All Popup  ************************** */

function getEmpTargetName(id,path,msg){

var ROW_ID = id.split('_').pop();
$('#getData_tbody').html('Loading...'); 

$.ajaxSetup({
headers: {
'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
}
});

$.ajax({
url:path,
type:'POST',
success:function(data) {
$('#getData_tbody').html(data);
bindEmpTargetEvents();
bindFyearEvents()
bindProdctEvents(ROW_ID)
},
error:function(data){
console.log("Error: Something went wrong.");
$('#getData_tbody').html('');
},
});

$("#tital_Name").text(msg);
$("#modalpopup").show();
event.preventDefault();
}

$("#modalclosePopup").on("click",function(event){ 
$("#modalpopup").hide();
event.preventDefault();
});


/*************************************   All Popup bind  ************************** */
function bindEmpTargetEvents(){
$('.clsemptrgt').click(function(){
var id = $(this).attr('id');
var txtval =    $("#txt"+id+"").val();
var texdesc =   $("#txt"+id+"").data("desc");
$("#EMPLOYEE_TARGETNAME").val(texdesc);
$("#EMPLOYEE_TARGET_REF").val(txtval);
$("#modalpopup").hide();
});
}

function bindFyearEvents(){
$('.clsfyear').click(function(){
var id = $(this).attr('id');
var txtval =    $("#txt"+id+"").val();
var texdesc =   $("#txt"+id+"").data("desc");
$("#FINALYEAR").val(texdesc);
$("#FYID_REF1").val(txtval);
$("#modalpopup").hide();
});
}

function bindProdctEvents(ROW_ID){
$('.clsprodct').click(function(){
var id = $(this).attr('id');
var txtval =    $("#txt"+id+"").val();
var texcode =   $("#txt"+id+"").data("code");

$('#PRODCTCODE_'+ROW_ID+'').val(texcode);
$('#HIDDEN_PRODCTCODE_'+ROW_ID+'').val(txtval);
$("#modalpopup").hide();
});
}

/*************************************   All Search Start  ************************** */

let input, filter, table, tr, td, i, txtValue;
function colSearch(ptable,ptxtbox,pcolindex) {
//var input, filter, table, tr, td, i, txtValue;
input = document.getElementById(ptxtbox);
filter = input.value.toUpperCase();
table = document.getElementById(ptable);
tr = table.getElementsByTagName("tr");
for (i = 0; i < tr.length; i++) {
 td = tr[i].getElementsByTagName("td")[pcolindex];
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
 
/************************************* All Search End  ************************** */


function setfocus(){
var focusid=$("#focusid").val();
$("#"+focusid).focus();
$("#closePopup").click();
}

function alertMsg(id,msg){
 $("#focusid").val(id);
 $("#YesBtn").hide();
 $("#NoBtn").hide();
 $("#OkBtn1").hide();  
 $("#OkBtn").show();              
 $("#AlertMessage").text(msg);
 $("#alert").modal('show');
 $("#OkBtn").focus();
 return false;
}

function validateForm(actionType){
   var EMPLOYEE_TARGETCODE        =   $.trim($("#EMPLOYEE_TARGETCODE").val());
   var EMPLOYEE_TARGETNAME        =   $.trim($("#EMPLOYEE_TARGETNAME").val());

   if(EMPLOYEE_TARGETCODE ===""){
     alertMsg('EMPLOYEE_TARGETCODE','Please enter Employee Target Master Code.');
   }
   else if(EMPLOYEE_TARGETNAME ===""){
     alertMsg('EMPLOYEE_TARGETNAME','Please enter Employee Target Master Name.');
   }
   else{
     event.preventDefault();
       var allblank1 = [];
       var focustext1= "";
       var textmsg = "";

       $('#example2').find('.participantRow').each(function(){
       if($.trim($(this).find("[id*=MONTH1_AMT]").val()) ==""){
         allblank1.push('false');
         focustext1 = $(this).find("[id*=MONTH1_AMT]").attr('id');
         textmsg = 'Please enter Month';
       }
       });

      //  $('#example3').find('.participantRow').each(function(){
      //    if($.trim($(this).find("[id*=HIDDEN_PRODCTCODE]").val()) ==""){
      //      allblank1.push('false');
      //      focustext1 = $(this).find("[id*=PRODCTCODE]").attr('id');
      //      textmsg = 'Please enter Product Code';
      //    }
      //    else if($.trim($(this).find("[id*=MONTH1_QTY]").val()) ==""){
      //      allblank1.push('false');
      //      focustext1 = $(this).find("[id*=MONTH1_QTY]").attr('id');
      //      textmsg = 'Please enter Month';
      //    }
      //  });

     if(jQuery.inArray("false", allblank1) !== -1){
         $("#focusid").val(focustext1);
         $("#YesBtn").hide();
         $("#NoBtn").hide();
         $("#OkBtn1").hide();  
         $("#OkBtn").show();
         $("#AlertMessage").text(textmsg);
         $("#alert").modal('show');
         $("#OkBtn").focus();
         highlighFocusBtn('activeOk');
         return false;
       } 
       else{
           $("#alert").modal('show');
           $("#AlertMessage").text('Do you want to save to record.');
           $("#YesBtn").data("funcname",actionType);  
           $("#YesBtn").focus();
           highlighFocusBtn('activeYes');
       }

 }
}


 $('#btnAdd').on('click', function() {
     var viewURL = '{{route("master",[$FormId,"add"])}}';
     window.location.href=viewURL;
 });

 $('#btnExit').on('click', function() {
   var viewURL = '{{route('home')}}';
   window.location.href=viewURL;
 });

var formResponseMst = $( "#frm_mst_edit" );
    formResponseMst.validate();

   function validateSingleElemnet(element_id){
     var validator =$("#frm_mst_edit" ).validate();
        if(validator.element( "#"+element_id+"" )){
         if(element_id=="EMPLOYEE_TARGETCODE" || element_id=="EMPLOYEE_TARGETCODE" ) {
           checkDuplicateCode();
         }
        }
     }

   function checkDuplicateCode(){
       var getDataForm = $("#frm_mst_edit");
       var formData = getDataForm.serialize();
       $.ajaxSetup({
           headers: {
             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
           }
       });
       $.ajax({
           url:'{{route("master",[$FormId,"codeduplicate"])}}',
           type:'POST',
           data:formData,
           success:function(data) {
             if(data.exists) {
               $(".text-danger").hide();
               showError('ERROR_EMPLOYEE_TARGETCODE',data.msg);
               $("#EMPLOYEE_TARGETCODE").focus();
               }                                
           },
           error:function(data){
             console.log("Error: Something went wrong.");
           },
       });
   }

   $("#btnSave" ).click(function() {
    var formReqData = $("#frm_mst_edit");
    if(formReqData.valid()){
      validateForm();
    }
  });

$("#YesBtn").click(function(){
    $("#alert").modal('hide');
    var customFnName = $("#YesBtn").data("funcname");
    window[customFnName]();
  });


  function submitData(type){
  var formReqData = $("#frm_mst_edit");
  if(formReqData.valid()){
    validateForm("fnSaveData");
  }
}

function submitDataAp(type){
  var formReqData = $("#frm_mst_edit");
  if(formReqData.valid()){
    validateForm("fnApproveData");
  }
}

window.fnSaveData = function (){
  submitForm('update');
};

window.fnApproveData = function (){
  submitForm('approve');
}

function submitForm(requestType){

var getDataForm = $("#frm_mst_edit");
var formData = getDataForm.serialize() + "&requestType=" + requestType ;
//var formData = getDataForm.append(requestType);
$.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$.ajax({
    url:'{{route("mastermodify",[$FormId,"update"])}}',
    type:'POST',
    data:formData,
    success:function(data) {
      if(data.success) {                   
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").show();
      $("#AlertMessage").text(data.msg);
      $(".text-danger").hide();
      $("#alert").modal('show');
      $("#OkBtn").focus();
      }     
      if(data.errors) {
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text(data.msg);
        $("#alert").modal('show');
        $("#OkBtn1").focus();
      }
    },
    error:function(data){
    console.log("Error: Something went wrong.");
    },
  });
}

     
//delete row
$("#Product").on('click', '.remove', function() {
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
$("#Product").on('click', '.add', function() {
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
// var name = el.attr('name') || null;
// if(name){
//   var nameLength = name.split('_').pop();
//   var i = name.substr(name.length-nameLength.length);
//   var prefix1 = name.substr(0, (name.length-nameLength.length));
//   el.attr('name', prefix1+(+i+1));
// }
});

$clone.find('input:text').val('');
$clone.find('input:hidden').val('');
$tr.closest('table').append($clone);         
var rowCount1 = $('#Row_Count').val();
rowCount1 = parseInt(rowCount1)+1;
$('#Row_Count').val(rowCount1);
$clone.find('.remove').removeAttr('disabled'); 
event.preventDefault();
});

   $("#NoBtn").click(function(){
     $("#alert").modal('hide');
     var custFnName = $("#NoBtn").data("funcname");
       window[custFnName]();
     });
  
   
   $("#OkBtn").click(function(){
       $("#alert").modal('hide');
       $("#YesBtn").show();
       $("#NoBtn").show();
       $("#OkBtn").hide();
       $("#OkBtn1").hide();
       $(".text-danger").hide(); 
       window.location.href = "{{route('master',[$FormId,'index'])}}";
   });
   
   
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
     });

   
       $("#OkBtn1").click(function(){
       $("#alert").modal('hide');
       $("#YesBtn").show();
       $("#NoBtn").show();
       $("#OkBtn").hide();
       $("#OkBtn1").hide();
       $(".text-danger").hide();
       window.location.href = "{{route('master',[$FormId,'index'])}}";
       });

       $("#OkBtn").click(function(){
         $("#alert").modal('hide');
       });

   window.fnUndoYes = function (){
     window.location.href = "{{route('master',[$FormId,'add'])}}";
   }

   function showError(pId,pVal){
     $("#"+pId+"").text(pVal);
     $("#"+pId+"").show();
     }

   function highlighFocusBtn(pclass){
      $(".activeYes").hide();
      $(".activeNo").hide();
      $("."+pclass+"").show();
   }  

 $(document).ready(function () {
    $("#example2").on('input', '.txtCal', function () {
       var calculated_total_sum = 0;
       $("#example2 .txtCal").each(function () {
           var get_textbox_value = $(this).val();
           if ($.isNumeric(get_textbox_value)) {
              calculated_total_sum += parseFloat(get_textbox_value);
              }                  
            });
           $("#TARGET_AMOUNT").val(calculated_total_sum);
       });
  });


$(document).ready(function(e) {
var d = new Date(); 
var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
$('#DOC_DT').val(today);
});
   
function onlyNumberKey(evt) {
   var ASCIICode = (evt.which) ? evt.which : evt.keyCode
   if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
       return false;
   return true;
}


$(function () {
  var today = new Date(); 
    var dodeactived_date = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
    $('#DODEACTIVATED').attr('min',dodeactived_date);

  $('input[type=checkbox][name=DEACTIVATED]').change(function() {
    if ($(this).prop("checked")) {
      $(this).val('1');
      $('#DODEACTIVATED').removeAttr('disabled');
    }
    else {
      $(this).val('0');
      $('#DODEACTIVATED').prop('disabled', true);
      $('#DODEACTIVATED').val('');
    }
  });
});
</script>

@endpush
