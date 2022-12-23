@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[294,'index'])}}" class="btn singlebt">ESI Rules</a>
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
        {{isset($objResponse->TDSSLABID) ? method_field('PUT') : '' }}
       <div class="inner-form">
       <div id="Material" class="tab-pane fade in active">
             <div class="row">
               <input type="hidden" id="focusid" >
                 <div class=" table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:300px;" >
                   <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                     <thead id="thead1"  style="position: sticky;top: 0">
                   <tr>                          
                       <th rowspan="2"  width="3%">Financial Year <input class="form-control" type="hidden" name="Row_Count" id ="Row_Count"> </th>                         
                       <th rowspan="2" width="3%">Financial Year Description</th>
                       <th rowspan="2" width="3%">From Pay Period</th>
                       <th rowspan="2" width="3%">To Pay Period</th>
                       <th rowspan="2" width="3%">Cap Limit</th>
                       <th rowspan="2" width="3%">Employee ESI Rate</th>
                       <th rowspan="2" width="3%">Employer  ESI Rate</th>
                       <th rowspan="2" width="3%">Total ESI</th>
                       <th rowspan="2" width="3%">De-Activated</th>
                       <th rowspan="2" width="3%">Date of De-Activated</th>                       
                       <th rowspan="2" width="3%">Action</th>
                   </tr>                     
                       
               </thead>

                     <tbody>
                       <tr  class="participantRow">
                           <td>
                             @php
                               $EMP_ESI_RATE = $objResponse->EMP_ESI_RATE; 
                               $EMPR_ESI_RATE = $objResponse->EMPR_ESI_RATE;
                               $total =$EMP_ESI_RATE+ $EMPR_ESI_RATE;
                             @endphp
                             <select name="FYID_REF[]" id="FYID_REF_0" class="form-control mandatory" onchange="getFYearName(this.id,this.value)" tabindex="4" disabled>
                             <option value="" selected="">Select</option>
                             @foreach($objYearList as $val)
                             <option {{isset($objResponse->FYID_REF) && $objResponse->FYID_REF == $val-> YRID ?'selected="selected"':''}} value="{{ $val-> YRID }}">{{ $val->YRCODE }}</option>
                             @endforeach
                           </select>
                         </td>
                           <td><input type="text" id="YRDESCRIPTION_0" value="{{ $objFyDesList->YRDESCRIPTION }}" class="form-control" disabled  maxlength="100" readonly  ></td>
                           <td>
                             <select name="FR_PAYPID_REF[]" id="FR_PAYPID_REF_0" class="form-control mandatory" tabindex="4" disabled>
                               <option value="" selected="">Select</option>
                               @foreach($obPeriodList as $val)                               
                               <option {{isset($objResponse->FR_PAYPID_REF) && $objResponse->FR_PAYPID_REF == $val-> PAYPERIODID ?'selected="selected"':''}} value="{{ $val-> PAYPERIODID }}">{{ $val->PAY_PERIOD_CODE }}-{{ $val->PAY_PERIOD_DESC }}</option>
                               @endforeach
                             </select>                           
                           <td>                                
                             <select name="TO_PAYPID_REF[]" id="TO_PAYPID_REF_0" class="form-control mandatory" tabindex="4" disabled>
                               <option value="" selected="">Select</option>
                               @foreach($obPeriodList as $val)
                               <option {{isset($objResponse->TO_PAYPID_REF) && $objResponse->TO_PAYPID_REF == $val-> PAYPERIODID ?'selected="selected"':''}} value="{{ $val-> PAYPERIODID }}">{{ $val->PAY_PERIOD_CODE }}-{{ $val->PAY_PERIOD_DESC }}</option>
                               @endforeach
                             </select> 
                           <td><input  class="form-control" type="text" name="CAP_LIMIT[]" id ="CAP_LIMIT_0" value="{{ $objResponse->CAP_LIMIT }}"  autocomplete="off" onkeypress="return onlyNumberKey(event)" style="width: 99%" ondrop="return false;oncopy="return false" oncut="return false" onpaste="return false" disabled></td>
                           <td><input  class="form-control txtCal" type="text" name="EMP_ESI_RATE[]" id ="EMP_ESI_RATE_0" value="{{ $objResponse->EMP_ESI_RATE }}" onkeyup="totalVlaue(this.id,this.value)"  autocomplete="off" onkeypress="return onlyNumberKey(event)" style="width: 99%" ondrop="return false;oncopy="return false" oncut="return false" onpaste="return false" disabled></td>
                           <td><input  class="form-control txtCal" type="text" name="EMPR_ESI_RATE[]" id ="EMPR_ESI_RATE_0" value="{{ $objResponse->EMPR_ESI_RATE }}" onkeyup="totalVlaue(this.id,this.value)"  autocomplete="off" onkeypress="return onlyNumberKey(event)" style="width: 99%" ondrop="return false;"oncopy="return false" oncut="return false" onpaste="return false" disabled></td>
                           <td><input type="text" id="total_sum_value_0" value="{{ $total }}%" class="form-control"  maxlength="100" readonly ></td>
                           <td><input type="checkbox"   name="DEACTIVATED[]"  id="deactive-checkbox_0" {{$objResponse->DEACTIVATED == 1 ? "checked" : ""}} value='{{$objResponse->DEACTIVATED == 1 ? 1 : 0}}' tabindex="2" disabled ></td>
                           <td><input type="date" name="DODEACTIVATED[]" class="form-control" id="DODEACTIVATED" {{$objResponse->DEACTIVATED == 1 ? "" : "disabled"}} value="{{isset($objResponse->DODEACTIVATED) && $objResponse->DODEACTIVATED !="" && $objResponse->DODEACTIVATED !="1900-01-01" ? $objResponse->DODEACTIVATED:''}}" tabindex="3" placeholder="dd/mm/yyyy"  /></td>

                           <td align="center" >
                           <button disabled class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></button>
                           <button disabled class="btn remove" title="Delete" data-toggle="tooltip" disabled><i class="fa fa-trash" ></i></button>
                           </td>
                       </tr>
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
         <button class="btn alertbt" name='YesBtn' id="YesBtn" data-funcname="fnSaveData">
           <div id="alert-active" class="activeYes"></div>Yes
         </button>
         <button class="btn alertbt" name='NoBtn' id="NoBtn" data-funcname="fnUndoNo" >
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
 var SALARY_FR  =   $.trim($("[id*=SALARY_FR]").val());
 var FYID_REF          =   $.trim($("#FYID_REF").val());
 // var STID_REF          =   $.trim($("#STID_REF").val());
 $("#OkBtn1").hide();

 // if(FYID_REF ===""){
 //   $("#focusid").val('FYID_REF');
 //   $("#YesBtn").hide();
 //   $("#NoBtn").hide();
 //   $("#OkBtn1").hide();  
 //   $("#OkBtn").show();              
 //   $("#AlertMessage").text('Please enter Financial Year.');
 //   $("#alert").modal('show');
 //   $("#OkBtn").focus();
 //   return false;
 // }
//   else if(parseFloat(REJECTED_QTY) > parseFloat(QI_PICK_QTY)){
//   $("#FocusId").val('REJECTED_QTY');    
//   $("#YesBtn").hide();
//   $("#NoBtn").hide();
//   $("#OkBtn1").show();
//   $("#AlertMessage").text('Rejected Qty Should Less Then QI Pick Qty.');
//   $("#alert").modal('show');
//   $("#OkBtn1").focus();
//   return false;
// }  
 
 //else{
     event.preventDefault();
       var allblank1 = [];
       var allblank2 = [];
       var allblank3 = [];
       var allblank6 = [];
       var allblank7 = [];
       var allblank8 = [];

       var focustext1= "";
       var focustext2= "";
       var focustext3= "";
       var focustext6= "";
       var focustext7= "";
       var focustext8= "";
     
       $('#example2').find('.participantRow').each(function(){

       if($.trim($(this).find("[id*=FYID_REF]").val()) ==""){
         
         allblank1.push('false');
         focustext1 = $(this).find("[id*=FYID_REF]").attr('id');
       }
         else if($.trim($(this).find("[id*=FR_PAYPID_REF]").val()) ==""){
           allblank2.push('false');
           focustext2 = $(this).find("[id*=FR_PAYPID_REF]").attr('id');
         }

         else if($.trim($(this).find("[id*=TO_PAYPID_REF]").val()) ==""){
           allblank3.push('false');
           focustext3 = $(this).find("[id*=TO_PAYPID_REF]").attr('id');
         }

         else if($.trim($(this).find("[id*=CAP_LIMIT]").val()) ==""){
           allblank6.push('false');
           focustext6 = $(this).find("[id*=CAP_LIMIT]").attr('id');
         } 
         else if($.trim($(this).find("[id*=EMP_ESI_RATE]").val()) ==""){
           allblank7.push('false');
           focustext7 = $(this).find("[id*=EMP_ESI_RATE]").attr('id');
         }
         else if($.trim($(this).find("[id*=EMPR_ESI_RATE]").val()) ==""){
           allblank8.push('false');
           focustext8 = $(this).find("[id*=EMPR_ESI_RATE]").attr('id');
         }             

           });

     if(jQuery.inArray("false", allblank1) !== -1){
         $("#focusid").val(focustext1);
         $("#YesBtn").hide();
         $("#NoBtn").hide();
         $("#OkBtn1").hide();  
         $("#OkBtn").show();
         $("#AlertMessage").text('Please enter Financial Year.');
         $("#alert").modal('show');
         $("#OkBtn").focus();
         highlighFocusBtn('activeOk');
         return false;
       }
       else if(jQuery.inArray("false", allblank2) !== -1){
         $("#focusid").val(focustext2);
         $("#YesBtn").hide();
         $("#NoBtn").hide();
         $("#OkBtn1").hide();  
         $("#OkBtn").show();
         $("#AlertMessage").text('Please enter From Pay Period.');
         $("#alert").modal('show');
         $("#OkBtn").focus();
         highlighFocusBtn('activeOk');
         return false;
       }
       else if(jQuery.inArray("false", allblank3) !== -1){
         $("#focusid").val(focustext3);
         $("#YesBtn").hide();
         $("#NoBtn").hide();
         $("#OkBtn1").hide();  
         $("#OkBtn").show();
         $("#AlertMessage").text('Please enter To Pay Period.');
         $("#alert").modal('show');
         $("#OkBtn").focus();
         highlighFocusBtn('activeOk');
         return false;
       }    
       else if(jQuery.inArray("false", allblank6) !== -1){
         $("#focusid").val(focustext6);
         $("#YesBtn").hide();
         $("#NoBtn").hide();
         $("#OkBtn1").hide();  
         $("#OkBtn").show();
         $("#AlertMessage").text('Please enter Cap Limit.');
         $("#alert").modal('show');
         $("#OkBtn").focus();
         highlighFocusBtn('activeOk');
         return false;
       }
       else if(jQuery.inArray("false", allblank7) !== -1){
         $("#focusid").val(focustext7);
         $("#YesBtn").hide();
         $("#NoBtn").hide();
         $("#OkBtn1").hide();  
         $("#OkBtn").show();
         $("#AlertMessage").text('Please enter Employee ESI Rate	.');
         $("#alert").modal('show');
         $("#OkBtn").focus();
         highlighFocusBtn('activeOk');
         return false;
       }
       else if(jQuery.inArray("false", allblank8) !== -1){
         $("#focusid").val(focustext8);
         $("#YesBtn").hide();
         $("#NoBtn").hide();
         $("#OkBtn1").hide();  
         $("#OkBtn").show();
         $("#AlertMessage").text('Please enter Employer ESI Rate	.');
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

 //}

}


$("#Material").on('click', '.add', function() {
 
 var $tr = $(this).closest('table');
 var allTrs = $tr.find('.participantRow').last();
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
     // var name = el.attr('name') || null;
     // if(name) {
     //     var i = name.substr(name.length-1);
     //     var prefix1 = name.substr(0, (name.length-1));
     //     el.attr('name', prefix1+(+i+1));
     // }
 });
 $clone.find('input:text').val('');
 $clone.find('input:hidden').val('');
 $clone.find('.remove').removeAttr('disabled'); 

 $clone.find('input:checkbox').prop('checked',false);;

 $tr.closest('table').append($clone);   
 var rowCount = $('#Row_Count1').val();
   rowCount = parseInt(rowCount)+1;
   $('#Row_Count1').val(rowCount);
   event.preventDefault();
}); 

$("#Material").on('click', '.remove', function() {
 var rowCount = $(this).closest('table').find('.participantRow').length;
 
 if (rowCount > 1) {
   $(this).closest('.participantRow').remove();
   rowCount = parseInt(rowCount)-1;
       $('#Row_Count1').val(rowCount);
 }
 if (rowCount <= 1) {
   $(document).find('.remove').prop('disabled', true);
 }
 event.preventDefault();
});


$('#btnAdd').on('click', function() {
   var viewURL = '{{route("master",[294,"add"])}}';
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
         url:'{{route("master",[294,"codeduplicate"])}}',
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
         url:'{{ route("mastermodify",[294,"update"]) }}',
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

               //  window.location.href='{{ route("master",[294,"index"])}}';
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
            url:'{{route("mastermodify",[294,"Approve"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();                    
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
                    window.location.href='{{ route("master",[294,"index"])}}';

                }
                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });

    };// fnApproveData


 
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
 window.location.href = "{{route('master',[294,'index'])}}";

 });


 
 $("#OkBtn").click(function(){
   $("#alert").modal('hide');

 });////ok button


window.fnUndoYes = function (){
   
   //reload form
   window.location.href = "{{route('master',[294,'add'])}}";

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



 function getFYearName(id,value){      
 var textid         = id.split('_').pop();
 
 $.ajaxSetup({
         headers: {
             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         }
     });
 
     $.ajax({
         url:'{{route("master",[294,"getFYearName"])}}',
         type:'POST',
         data:{YRID:value},
         success:function(data) {
            $("#YRDESCRIPTION_"+textid).val(data);                
         },
         error:function(data){
           console.log("Error: Something went wrong.");
         },
     });	
}

function totalVlaue(id,value){        
 var textid         = id.split('_').pop();
 var EMP_ESI_RATE   = $.trim($("#EMP_ESI_RATE_"+textid).val()) !=''? parseFloat($.trim($("#EMP_ESI_RATE_"+textid).val())):0;
 var EMPR_ESI_RATE  = $.trim($("#EMPR_ESI_RATE_"+textid).val()) !=''? parseFloat($.trim($("#EMPR_ESI_RATE_"+textid).val())):0;
 totalval = EMP_ESI_RATE+EMPR_ESI_RATE;
 $('#total_sum_value_'+textid).val(totalval + '%');
}

 
</script>

<script>
 function onlyNumberKey(evt) {
     var ASCIICode = (evt.which) ? evt.which : evt.keyCode
     if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
         return false;
     return true;
 }
</script>
<script type="text/javascript">
  $(function () {
    
    $('input[type=checkbox][id=DEACTIVATED]').change(function() {
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
  
  $(function() { 
    //$("#DESCRIPTIONS").focus(); 
  });
  </script>

@endpush