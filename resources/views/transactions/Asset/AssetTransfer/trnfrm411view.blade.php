@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Asset Transfer</a>
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
        {{isset($objResponse->LEAVE_APPID) ? method_field('PUT') : '' }}
       <div class="inner-form">
           
             <div class="row">
               <div class="col-lg-2 pl"><p>Doc No*</p></div>
               <div class="col-lg-2 pl">
                 <input type="text" name="LEAVE_APP_NO" id="LEAVE_APP_NO" value="{{ $objHRD->ASSETTRANCODE }}" class="form-control mandatory"  autocomplete="off" readonly disabled>
                 <span class="text-danger" id="ERROR_DPP_NO"></span> 
               </div>

               <div class="col-lg-2 pl"><p>Date*</p></div>
                 <div class="col-lg-2 pl">
                   <input type="date" name="LEAVE_APP_DT" value="{{ $objHRD->ASSETTRANDATE }}" id="LEAVE_APP_DT" class="form-control"  maxlength="100" disabled> 
                 </div>

               <div class="col-lg-2 pl"><p>Asset Code*</p></div>
                 <div class="col-lg-2 pl">
                   <input type="text" name="ASSETCODE" id="ASSETCODE" value="{{ $objHRD->ASSETCODE }}" class="form-control mandatory"  autocomplete="off" disabled readonly/>
                   <input type="hidden" name="ASSETID_REF" id="ASCATID" value="{{ $objHRD->ASSETID_REF }}" class="form-control" autocomplete="off" disabled />
                   <span class="text-danger" id="ERROR_PAYPERIODID_REF"></span>                             
                 </div>
               </div> 

               <div class="row">
                 <div class="col-lg-2 pl"><p>Asset Name</p></div>
                 <div class="col-lg-2 pl">
                   <input type="text" name="DESCRIPTIONS" id="DESCRIPTIONS" value="{{ $objHRD->ASTDESCRIPTIONS }}" class="form-control" readonly  maxlength="100" disabled> 
                 </div>
               
                 <div class="col-lg-2 pl"><p>Asset No*</p></div>
                 <div class="col-lg-2 pl">
                   <input type="text" name="ASSETNO" id="ASSETNO" value="{{ $objHRD->ASTCODE }}" class="form-control mandatory"  autocomplete="off" disabled readonly/>
                   <input type="hidden" name="ASTID_REF" id="ASTID_REF" value="{{ $objHRD->ASSETNOID_REF }}" class="form-control" autocomplete="off" disabled />
                 <input type="hidden" id="focusid" >
                 <span class="text-danger" id="ERROR_EMPID_REF"></span>                             
               </div>
               
               <div class="col-lg-2 pl"><p>Asset Location</p></div>
                 <div class="col-lg-2 pl">
                   <input type="text" name="ASTLOCATION" id="ASTLOCATION" value="{{ $objHRD->ASTSUBCODE }}" class="form-control" readonly maxlength="100" disabled> 
                 </div>
               </div>
               <div class="row">
                 <div class="col-lg-2 pl"><p>Sub Location</p></div>
                 <div class="col-lg-2 pl">
                   <input type="text" name="ASTSUBLOCATION" id="ASTSUBLOCATION" value="{{ $objHRD->ASTSUBDES }}" class="form-control" readonly maxlength="100" disabled> 
                   <input type="hidden" name="ALID_REF_FROM" id="ALID_REF_FROM" value="{{ $objHRD->ALID_REF_FROM }}" class="form-control" readonly maxlength="100" disabled >
                 </div>

                 <div class="col-lg-2 pl"><p>In case Asset with Employee</p></div>
                 <div class="col-lg-2 pl">
                   <input type="text" name="ASTEMPY" id="ASTEMPY"  value="{{ $objHRD->EMPCODE }}" class="form-control mandatory"  autocomplete="off" disabled readonly/>
                   <input type="hidden" name="ASASTCATID" id="ASASTCATID"  value="{{ $objHRD->WITH_EMPLOYEE }}" class="form-control" autocomplete="off" disabled />
                 </div>
                             
               <div class="col-lg-2 pl"><p>Asset Location*</p></div>
               <div class="col-lg-2 pl">
                 <select name="ASTSUBLOCTION" id="ASTSUBLOCTION" class="form-control mandatory" tabindex="4" disabled>
                   <option value="" selected="">Select</option>
                   @foreach($objDataList as $val)
                   <option {{isset($objResponse->ALID_REF_TO) && $objResponse->ALID_REF_TO == $val-> ASLID ?'selected="selected"':''}} value="{{ $val-> ASLID }}">{{ $val->ASLCODE }}</option>
                   @endforeach
                 </select>
               </div>
             </div>
               <div class="row">
               <div class="col-lg-2 pl"><p>Sub Location*</p></div>
               <div class="col-lg-2 pl">
                 <select name="ASTSUBLTION" id="ASTSUBLTION" class="form-control mandatory" tabindex="4" disabled>
                   <option value="" selected="">Select</option>
                   @foreach($objDataList as $val)
                   <option {{isset($objResponse->ASLID_REF_TO) && $objResponse->ASLID_REF_TO == $val-> ASLID ?'selected="selected"':''}} value="{{ $val-> ASLID }}">{{ $val->DESCRIPTIONS }}</option>
                   @endforeach
                 </select>
               <input type="hidden" id="focusid" >
               <span class="text-danger" id="ERROR_EMPID_REF"></span>                             
             </div>
             
             <div class="col-lg-2 pl"><p>In case Asset issue to an Employee</p></div>
               <div class="col-lg-2 pl">
                 <select name="ASTEMPLYEE" id="ASTEMPLYEE" class="form-control mandatory" tabindex="4" disabled>
                   <option value="" selected="">Select</option>
                   @foreach($objEmp as $val)
                   <option {{isset($objResponse->TO_EMPLOYEE) && $objResponse->TO_EMPLOYEE == $val-> EMPID ?'selected="selected"':''}} value="{{ $val-> EMPID }}">{{ $val->EMPCODE }}</option>
                   @endforeach
                 </select>
               </div>
           
             <div class="col-lg-2 pl"><p>Reason of Transfer*</p></div>
             <div class="col-lg-2 pl">
               <input type="text" name="REASON_TRANFR" id="REASON_TRANFR" value="{{ $objHRD->REASON }}" class="form-control"  maxlength="100" disabled> 
             </div>
           </div>
           
             <div class="row">
             <div class="col-lg-2 pl"><p>Remarks </p></div>
             <div class="col-lg-2 pl">
               <input type="text" name="REMARKS" id="REMARKS" value="{{ $objHRD->REMARKS }}" class="form-control"  maxlength="100" disabled> 
             <input type="hidden" id="focusid" >
             <span class="text-danger" id="ERROR_EMPID_REF"></span>                             
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

<div id="asetmstpopup" class="modal" role="dialog"  data-backdrop="static">
<div class="modal-dialog modal-md" style="width: 600px;">
 <div class="modal-content">
   <div class="modal-header">
     <button type="button" class="close" data-dismiss="modal" id='emp_closePopup' >&times;</button>
   </div>
 <div class="modal-body">
 <div class="tablename" id="titalname"><p>Asset Code Details</p></div>
 <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
 <table id="MachTable" class="display nowrap table  table-striped table-bordered" width="100%">
 <thead>
   <tr>
     <th class="ROW1" style="width: 10%" align="center">Select</th> 
     <th class="ROW2" style="width: 40%">Code</th>
     <th  class="ROW3"style="width: 40%">Description</th>
   </tr>
 </thead>
 <tbody>
 <tr>
   <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
   <td class="ROW2"  style="width: 40%">
     <input type="text" autocomplete="off"  class="form-control" id="search_astcode_1" onkeyup="searchAstCode(this.id,'AstTable2','1')" />
   </td>
   <td class="ROW3"  style="width: 40%">
     <input type="text" autocomplete="off"  class="form-control" id="search_astcode_2" onkeyup="searchAstCode(this.id,'AstTable2','2')" />
   </td>
 </tr>
 </tbody>
 </table>
   <table id="AstTable2" class="display nowrap table  table-striped table-bordered" width="100%" style="font-size: 13px;">
     <thead id="thead2">
       
     </thead>
     <tbody id="tbody_subglacct">       
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
 var ASSETCODE     =   $.trim($("[id*=ASSETCODE]").val());
 var ASSETNO       =   $.trim($("[id*=ASSETNO]").val());
 var ASTEMPY       =   $.trim($("[id*=ASTEMPY]").val());
 var ASTLOCATION   =   $.trim($("[id*=ASTLOCATION]").val());
 var ASTSUBLOCTION =   $.trim($("[id*=ASTSUBLOCTION]").val());
 var ASTSUBLTION   =   $.trim($("[id*=ASTSUBLTION]").val());

 $("#OkBtn1").hide();
 if(ASSETCODE ===""){
   $("#focusid").val('ASSETCODE');
   $("#YesBtn").hide();
   $("#NoBtn").hide();
   $("#OkBtn1").hide();  
   $("#OkBtn").show();              
   $("#AlertMessage").text('Please enter Asset Code.');
   $("#alert").modal('show');
   $("#OkBtn").focus();
   return false;
 }
 else if(ASSETNO ===""){
   $("#focusid").val('ASSETNO');
   $("#YesBtn").hide();
   $("#NoBtn").hide();
   $("#OkBtn1").hide();  
   $("#OkBtn").show();              
   $("#AlertMessage").text('Please enter Asset No.');
   $("#alert").modal('show');
   $("#OkBtn").focus();
   return false;
 }

 else if(ASTEMPY ===""){
   $("#focusid").val('ASTEMPY');
   $("#YesBtn").hide();
   $("#NoBtn").hide();
   $("#OkBtn1").hide();  
   $("#OkBtn").show();              
   $("#AlertMessage").text('Please enter In case Asset with Employee.');
   $("#alert").modal('show');
   $("#OkBtn").focus();
   return false;
 }
 else if(ASTSUBLOCTION ===""){
   $("#focusid").val('ASTSUBLOCTION');
   $("#YesBtn").hide();
   $("#NoBtn").hide();
   $("#OkBtn1").hide();  
   $("#OkBtn").show();              
   $("#AlertMessage").text('Please enter Asset Location.');
   $("#alert").modal('show');
   $("#OkBtn").focus();
   return false;
 }
 else if(ASTSUBLTION ===""){
   $("#focusid").val('ASTSUBLTION');
   $("#YesBtn").hide();
   $("#NoBtn").hide();
   $("#OkBtn1").hide();  
   $("#OkBtn").show();              
   $("#AlertMessage").text('Please enter Sub Location.');
   $("#alert").modal('show');
   $("#OkBtn").focus();
   return false;
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
 $("#LEAVE_APP_NO").blur(function(){
     $(this).val($.trim( $(this).val() ));
     $("#ERROR_DESCRIPTIONS").hide();
     validateSingleElemnet("LEAVE_APP_NO");
 });

 $( "#LEAVE_APP_NO" ).rules( "add", {
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
       if(element_id=="LEAVE_APP_NO" || element_id=="LEAVE_APP_NO" ) {
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
         url:'{{route("transaction",[$FormId,"codeduplicate"])}}',
         type:'POST',
         data:formData,
         success:function(data) {
             if(data.exists) {
                 $(".text-danger").hide();
                 showError('ERROR_LEAVE_APP_NO',data.msg);
                 $("#LEAVE_APP_NO").focus();
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
var getDataForm = $("#frm_mst_edit");
var formData    = getDataForm.serialize();
$.ajaxSetup({
 headers: {
   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
 }
});
     
  $.ajax({
    url:'{{ route("transactionmodify",[$FormId,"update"]) }}',
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
} // fnSaveData

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
 window.location.href = "{{route('transaction',[$FormId,'index'])}}";
 });


 
 $("#OkBtn").click(function(){
   $("#alert").modal('hide');
 });////ok button


window.fnUndoYes = function (){      
   //reload form
   window.location.href = "{{route('transaction',[$FormId,'add'])}}";
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

 let AstTable2 = "#AstTable2";
 let MachTable = "#MachTable";
 let headers     = document.querySelectorAll(AstTable2 + " th");
 headers.forEach(function(element, i) {
   element.addEventListener("click", function() {
     w3.sortHTML(MachTable, ".clsdpid", "td:nth-child(" + (i + 1) + ")");
   });
 });

 function searchAstCode(search_id,table_id,index_no) {
   var input, filter, table, tr, td, i, txtValue;
   input = document.getElementById(search_id);
   filter = input.value.toUpperCase();
   table = document.getElementById(table_id);
   tr = table.getElementsByTagName("tr");
   for (i = 0; i < tr.length; i++) {
     td = tr[i].getElementsByTagName("td")[index_no];
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

$("#ASSETCODE").click(function(event){  
$('#tbody_subglacct').html('Loading...');
$.ajaxSetup({
   headers: {
   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
   }
});
$.ajax({
   url:'{{route("transaction",[411,"getemplCode"])}}',
   type:'POST',
   success:function(data) {
   $('#tbody_subglacct').html(data);
   bindEmpEvents();
   },
   error:function(data){
   console.log("Error: Something went wrong.");
   $('#tbody_subglacct').html('');
   },
});
$("#asetmstpopup").show();
event.preventDefault();
}); 

$("#emp_closePopup").on("click",function(event){ 
$("#asetmstpopup").hide();
event.preventDefault();
});

function bindEmpEvents(){
$('.clsemp').click(function(){
 var id = $(this).attr('id');
 var txtval =    $("#txt"+id+"").val();
 var texdesc =   $("#txt"+id+"").data("desc");
 var DESCRIPTIONS =   $("#txt"+id+"").data("ccname");
 var ASCATID =   $("#txt"+id+"").data("ascatid");
//alert(txtval);
 $("#ASSETCODE").val(texdesc);
 $("#ASSETCODE").blur();
 $("#DESCRIPTIONS").val(DESCRIPTIONS);
 $("#ASCATID").val(txtval);
 $("#asetmstpopup").hide();
 $(this).prop("checked",false);
 event.preventDefault();
});
} 

$("#ASTEMPY").click(function(event){  
$('#tbody_subglacct').html('Loading...');
$.ajaxSetup({
   headers: {
   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
   }
});
$.ajax({
   url:'{{route("transaction",[411,"getastempCode"])}}',
   type:'POST',
   success:function(data) {
   $('#tbody_subglacct').html(data);
   bindAstEmpEvents();
   },
   error:function(data){
   console.log("Error: Something went wrong.");
   $('#tbody_subglacct').html('');
   },
});  
$("#asetmstpopup").show();
event.preventDefault();
}); 

$("#emp_closePopup").on("click",function(event){ 
$("#asetmstpopup").hide();
event.preventDefault();
});

function bindAstEmpEvents(){
$('.astclick').click(function(){
 var id = $(this).attr('id');
 var txtval =    $("#txt"+id+"").val();
 var texdesc =   $("#txt"+id+"").data("desc");
//alert(txtval);
 $("#ASTEMPY").val(texdesc);
 $("#ASTEMPY").blur();
 $("#ASASTCATID").val(txtval);
 $("#asetmstpopup").hide();
 $(this).prop("checked",false);
 event.preventDefault();
});
}

$(document).ready(function(e) {
var d = new Date(); 
var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
$('#LEAVE_APP_DT').val(today);

});

</script>

<script>
 function onlyNumberKey(evt) {
     var ASCIICode = (evt.which) ? evt.which : evt.keyCode
     if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
     return false;
     return true;
 }

 $("#ASSETNO").click(function(event){  
   $('#tbody_subglacct').html('Loading...');
   $.ajaxSetup({
       headers: {
       'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
       }
   });
   $.ajax({
       url:'{{route("transaction",[411,"getassetno"])}}',
       type:'POST',
       success:function(data) {
       $('#tbody_subglacct').html(data);
       bindAssetEvents();
       },
       error:function(data){
       console.log("Error: Something went wrong.");
       $('#tbody_subglacct').html('');
       },
   });        
   $("#asetmstpopup").show();
   event.preventDefault();
 }); 

$("#emp_closePopup").on("click",function(event){ 
$("#asetmstpopup").hide();
event.preventDefault();
});

function bindAssetEvents(){
 $('.astclick').click(function(){
   var id = $(this).attr('id');
   var txtval =    $("#txt"+id+"").val();
   var texdesc =   $("#txt"+id+"").data("desc");
   var texdescast =   $("#txt"+id+"").data("ccdes");
   var texdescsubast =   $("#txt"+id+"").data("ccsubdes");
   var texdastrefid =   $("#txt"+id+"").data("astrefid");
   //alert(texdastrefid);
   $("#ASSETNO").val(texdesc);
   $("#ASSETNO").blur();
   $("#ASTLOCATION").val(texdescast);
   $("#ASTSUBLOCATION").val(texdescsubast);
   $("#ASTID_REF").val(txtval);
   $("#ALID_REF_FROM").val(texdastrefid);
   $("#asetmstpopup").hide();
   $(this).prop("checked",false);
   event.preventDefault();
 });
}

</script>

@endpush