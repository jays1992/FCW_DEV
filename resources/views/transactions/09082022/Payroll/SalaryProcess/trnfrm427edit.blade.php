@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Salary Process</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <button href="#" id="btnSelectedRows" class="btn topnavbt" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
                        <button id="btnSave"   class="btn topnavbt" tabindex="4"><i class="fa fa-save"></i> Save</button>
                        <button class="btn topnavbt" id="btnView"  disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt"  id="btnUndo" ><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled" ><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove"{{($objRights->APPROVAL1 || $objRights->APPROVAL2 || $objRights->APPROVAL3 || $objRights->APPROVAL4 || $objRights->APPROVAL5) == 1 ? '' : 'disabled'}}><i class="fa fa-lock"></i> Approved</button>
                        <a href="#" class="btn topnavbt"  disabled="disabled"><i class="fa fa-link" ></i> Attachment</a>
                        <button class="btn topnavbt" id="btnExit"><i class="fa fa-power-off"></i> Exit</button>
                </div><!--col-10-->

            </div><!--row-->
    </div><!--topnav-->	
    <div class="container-fluid purchase-order-view filter">     
      <form id="frm_mst_edit" method="POST" onsubmit="return validateForm()" class="needs-validation"  > 
      
        @CSRF
        {{isset($objResponse->LEAVE_APPID) ? method_field('PUT') : '' }}
       <div class="inner-form">
           
             <div class="row">
               <div class="col-lg-2 pl"><p>Doc No*</p></div>
               <div class="col-lg-2 pl">
                 @if(!empty($objDDNO))
                     @if($objDDNO->SYSTEM_GRSR == "1")
                         <input type="text" name="DOCNO" id="DOCNO" value="{{ $objDPDOCNO }}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase" autofocus required>
                     @endif
                     @if($objDDNO->MANUAL_SR == "1")
                         <input type="text" name="DOCNO" id="DOCNO" value="{{ old('DOCNO') }}" class="form-control mandatory" maxlength="{{$objDDNO->MANUAL_MAXLENGTH}}" autocomplete="off" style="text-transform:uppercase" autofocus required>
                     @endif
                 @endif    
                 <span class="text-danger" id="ERROR_DOCNO"></span> 
               </div>

               <div class="col-lg-2 pl"><p>SP Date*</p></div>
                 <div class="col-lg-2 pl">
                   <input type="date" name="DOCDT" id="DOCDT" onchange="checkPeriodClosing('{{$FormId}}',this.value,1)" class="form-control"  maxlength="100" > 
                 </div>

               <div class="col-lg-2 pl"><p>Month*</p></div>
                 <div class="col-lg-2 pl">
                   <select name="MONTH_REF" id="MONTH_REF" class="form-control mandatory" tabindex="4">
                     <option value="" selected="">Select</option>
                     @foreach($monthList as $val)
                     <option value="{{$val->MTID}}">{{$val->MTCODE}}-{{$val->MTDESCRIPTION}}</option>
                     @endforeach
                   </select>
                   <span class="text-danger" id="ERROR_MONTH_REF"></span>                             
                 </div>
               </div> 

               <div class="row">
                 <div class="col-lg-2 pl"><p>Year *</p></div>
                 <div class="col-lg-2 pl">
                 <select name="YEAR_REF" id="YEAR_REF" class="form-control mandatory" tabindex="4">
                   <option value="" selected="">Select</option>
                   @foreach($yearList as $val)
                   <option value="{{$val->YRID}}">{{$val->YRCODE}}</option>
                   @endforeach
                 </select>
                 <input type="hidden" id="focusid" >
                 <span class="text-danger" id="ERROR_YEAR_REF"></span>                             
               </div>
               
               <div class="col-lg-2 pl"><p>Department*</p></div>
                 <div class="col-lg-2 pl">
                   <input type="checkbox" name="DEPARTMENT" id="DEPTEMP"> 
                 </div>


                 <div class="col-lg-2 pl"><p>Employee*</p></div>
                 <div class="col-lg-2 pl">
                   <input type="checkbox" name="EMPLOYEE" id="DEPTEMP" > 
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
           <input type="hidden" id="FocusId" >
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

function validateForm(){
 $("#focusid").val('');
 var DOCNO      =   $.trim($("[id*=DOCNO]").val());
 var DOCDT      =   $.trim($("[id*=DOCDT]").val());
 var MONTH_REF  =   $.trim($("[id*=MONTH_REF]").val());
 var YEAR_REF   =   $.trim($("[id*=YEAR_REF]").val());
 var DEPTEMP    =   ($('input[type=checkbox][id=DEPTEMP]:checked').length == 0);


 $("#OkBtn1").hide();
 if(DOCNO ===""){
   alertMsg('DOCNO','Please enter DOCNO.');
 }
 else if(DOCDT ===""){
   alertMsg('DOCDT','Please enter Date.');
 }

 else if(MONTH_REF ===""){
   alertMsg('MONTH_REF','Please enter Month.');
 }
 else if(YEAR_REF ===""){
   alertMsg('YEAR_REF','Please enter Year.');
 }
 else if(DEPTEMP) {
   alertMsg('DEPTEMP','Please enter Department & Employee.');
 }
 else if(checkPeriodClosing('{{$FormId}}',$("#DOCDT").val(),0) ==0){
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
     $("#AlertMessage").text('Do you want to save to record.');
     $("#YesBtn").data("funcname","fnSaveData");  
     $("#YesBtn").focus();
     highlighFocusBtn('activeYes');
 }

}

$('#btnAdd').on('click', function() {
   var viewURL = '{{route("transaction",[$FormId,"add"])}}';
   window.location.href=viewURL;
});

$('#btnExit').on('click', function() {
 var viewURL = '{{route('home')}}';
 window.location.href=viewURL;
});

var formResponseMst = $( "#frm_mst_edit" );
  formResponseMst.validate();
 $("#DOCNO").blur(function(){
     $(this).val($.trim( $(this).val() ));
     $("#ERROR_DOCNO").hide();
     validateSingleElemnet("DOCNO");
 });

 $( "#DOCNO" ).rules( "add", {
     required: true,
    normalizer: function(value) {
         return $.trim(value);
     },
     messages: {
         required: "Required field."
     }
 });

 function validateSingleElemnet(element_id){
   var validator =$("#frm_mst_edit" ).validate();
      if(validator.element( "#"+element_id+"" )){
       if(element_id=="DOCNO" || element_id=="DOCNO" ) {
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
         url:'{{route("transaction",[$FormId,"codeduplicate"])}}',
         type:'POST',
         data:formData,
         success:function(data) {
             if(data.exists) {
                 $(".text-danger").hide();
                 showError('ERROR_DOCNO',data.msg);
                 $("#DOCNO").focus();
             }                                
         },
         error:function(data){
           console.log("Error: Something went wrong.");
         },
     });
 }

 $( "#btnSave" ).click(function() {
     if(formResponseMst.valid()){
     validateForm();
     }
 });
 
 $("#YesBtn").click(function(){
     $("#alert").modal('hide');
     var customFnName = $("#YesBtn").data("funcname");
       window[customFnName]();
     }); 

 window.fnSaveData = function (){
     event.preventDefault();
     var getDataForm = $("#frm_mst_edit");
     var formData = getDataForm.serialize();
     $.ajaxSetup({
         headers: {
             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         }
     });
     $.ajax({
         url:'{{ route("transactionmodify",[403,"update"]) }}',
         type:'POST',
         data:formData,
         success:function(data) {
           if(data.success) {                   
             $("#YesBtn").hide();
             $("#NoBtn").hide();
             $("#OkBtn1").show();
             $("#OkBtn").hide();
             $("#AlertMessage").text(data.msg);
             $("#alert").modal('show');
             $("#OkBtn1").focus();
           }
           else{
             $("#YesBtn").hide();
             $("#NoBtn").hide();
             $("#OkBtn1").hide();
             $("#OkBtn").show();
             $("#AlertMessage").text(data.msg);
             $("#alert").modal('show');
             $("#OkBtn").focus();
           }
         },
         error:function(data){
           console.log("Error: Something went wrong.");
         },
     });
   
} 

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
 //window.location.href = "{{route('transaction',[$FormId,'index'])}}";
 });

 $("#OkBtn").click(function(){
   $("#alert").modal('hide');
 });


window.fnUndoYes = function (){
   window.location.href = "{{route('transaction',[$FormId,'add'])}}";
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

    $(document).ready(function(e) {
     var d = new Date(); 
     var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
     $('#DOCDT').val(today);
   });

 $('[id="DEPTEMP"]').change(function(){
   if(this.checked){
     $('[id="DEPTEMP"]').not(this).prop('checked', false);
   }    
 });

 function onlyNumberKey(evt) {
     var ASCIICode = (evt.which) ? evt.which : evt.keyCode
     if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
         return false;
     return true;
 }
 
</script>

@endpush