@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[$FormId,'index'])}}" class="btn singlebt">Asset Category</a>
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
       {{isset($objResponse->ALID) ? method_field('PUT') : '' }}
       <div class="inner-form">
       
             <div class="row">            
               <div class="col-lg-2 pl"><p>Asset Group Code*</p></div>
               <div class="col-lg-2 pl">
                 <select name="ASGID_REF" id="ASGID_REF" class="form-control mandatory" tabindex="4" disabled>
                   <option value="" selected="">Select</option>
                   @foreach($HDR as $val)
                   <option {{isset($objResponse->ASGID_REF) && $objResponse->ASGID_REF == $val-> ASGID ?'selected="selected"':''}} value="{{ $val-> ASGID }}">{{ $val->ASGCODE }}</option>
                   @endforeach
                 </select> 
                 <span class="text-danger" id="ERROR_FYID_REF"></span> 
                 <input type="hidden" id="focusid" >                              
               </div>

               <div class="col-lg-2 pl"><p>Asset Type</p></div>
               <div class="col-lg-2 pl">
                 <select name="ASTID_REF" id="ASTID_REF" class="form-control mandatory" tabindex="4" disabled>
                   <option value="" selected="">Select</option>
                   @foreach($HDR as $val)
                   <option {{isset($objResponse->ASTID_REF) && $objResponse->ASTID_REF == $val-> ASTID ?'selected="selected"':''}} value="{{ $val-> ASTID }}">{{ $val->ASSETTYPE }}</option>
                   @endforeach
                 </select>
               </div>                                
             </div>

          <div class="row">
           <div id="Material" class="tab-pane fade in active">
                 <div class=" table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:300px;" >
                   <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                     <thead id="thead1"  style="position: sticky;top: 0">
                   <tr>   
                     <th rowspan="2" width="3%">Asset Category Code</th> 
                     <th rowspan="2" width="3%">Asset Category Description</th>                      
                       <th colspan="2"  width="3%">In-Active </th>                         
                       <th rowspan="2" width="3%">Action</th>
                   </tr>                      
                       <tr>
                           <th>Yes / No</th>
                           <th>Date</th>
                       </tr>
               </thead>

                     <tbody>
                      
                       <tr  class="participantRow">
                         
                           <td><input disabled class="form-control" type="text" name="SUBLCODE[]"   id ={{"SUBLCODE_0"}}  value="{{ $HDRR->CATEGORY }}" autocomplete="off" style="width: 98%"></td>
                           <td><input disabled class="form-control" type="text" name="SUBLDES[]"   id ={{"SUBLDES_0"}}    value="{{ $HDRR->DESCRIPTIONS }}" autocomplete="off" style="width: 98%"></td>
                           <td><select disabled name="INACTIVE[]" id ={{"INACTIVE_0"}} class="form-control mandatory" tabindex="4">
                             <option value="" selected >Select</option>
                             <option {{isset($HDRR->ACTIVE) && $HDRR->ACTIVE == '1'?'selected="selected"':''}} value="1">Yes</option>
                             <option {{isset($HDRR->ACTIVE) && $HDRR->ACTIVE == '0'?'selected="selected"':''}} value="0">No</option>                              
                           </select>
                           <td>
                           <input disabled class="form-control" type="date" name="INACTIVEDATE[]" id ={{"INACTIVEDATE_0"}}  value="{{ $HDRR->ACTIVEDATE }}" autocomplete="off">
                           <td align="center" >
                               <button disabled class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></button>
                               <button disabled class="btn remove" title="Delete" data-toggle="tooltip"><i class="fa fa-trash" ></i></button>
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
   var ASGID_REF  =   $.trim($("[id*=ASGID_REF]").val());
   var ASTID_REF   =   $.trim($("#ASTID_REF").val());

   $("#OkBtn1").hide();

   if(ASGID_REF ===""){
     $("#focusid").val('ASGID_REF');
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").hide();  
     $("#OkBtn").show();              
     $("#AlertMessage").text('Please enter Asset Location Code.');
     $("#alert").modal('show');
     $("#OkBtn").focus();
     return false;
   }
   else if(ASTID_REF ===""){
     $("#focusid").val('ASTID_REF');
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").hide();  
     $("#OkBtn").show();              
     $("#AlertMessage").text('Please enter Desciption.');
     $("#alert").modal('show');
     $("#OkBtn").focus();
     return false;
   }

   else{
       event.preventDefault();
         var allblank1 = [];
         var focustext1= "";
         var textmsg = "";
         $('#example2').find('.participantRow').each(function(){
         if($.trim($(this).find("[id*=SUBLCODE]").val()) ==""){
           
           allblank1.push('false');
           focustext1 = $(this).find("[id*=SUBLCODE]").attr('id');
           textmsg = "Please enter Sub Location Code";
         }           
           else if($.trim($(this).find("[id*=SUBLDES]").val()) ==""){
             allblank1.push('false');
             focustext1 = $(this).find("[id*=SUBLDES]").attr('id');
             textmsg = "Please enter Sub Location Description";
           }
         });

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
             $("#YesBtn").data("funcname","fnSaveData");  
             $("#YesBtn").focus();
             highlighFocusBtn('activeYes');
         }
     }
   }
       


//add row
$("#example2").on('click', '.add', function() {
     var $tr = $(this).closest('tbody');
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
//   var name = el.attr('name') || null;
//   if(name){
//   var nameLength = name.split('_').pop();
//   var i = name.substr(name.length-nameLength.length);
//   var prefix1 = name.substr(0, (name.length-nameLength.length));
//   el.attr('name', prefix1+(+i+1));
// }
 });
 $clone.find('input:text').val('');
 $tr.closest('table').append($clone);
 var rowCount = $('#Row_Count').val();
 rowCount = parseInt(rowCount)+1;
 $('#Row_Count').val(rowCount);
 
 $clone.find('[id*="txtdesc"]').val('');
 $clone.find('[id*="txtID"]').val('0'); 
 $clone.find('[id*="chkmdtry"]').prop("checked", false); 
 $clone.find('[id*="deactive-checkbox"]').prop("checked", false); 
 $clone.find('[id*="decativateddate"]').val('');
 event.preventDefault();
 });

 $("#Material").on('click', '.remove', function() {
   var rowCount = $(this).closest('table').find('.participantRow').length;
   if (rowCount > 1) {
       $(this).closest('.participantRow').remove();  
       var rowCount1 = $('#Row_Count1').val();
       rowCount1 = parseInt(rowCount1)-1;
       $('#Row_Count1').val(rowCount1);
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
       event.preventDefault();
   }
   event.preventDefault();
   });


$(document).ready(function(e) {
var formResponseMst = $( "#frm_mst_edit" );
formResponseMst.validate();
var rcount = <?php echo json_encode($objCount); ?>;
 $('#Row_Count').val(rcount);
 //$('#Row_Count').val("1");
$('#btnAdd').on('click', function() {
   var viewURL = '{{route("master",[$FormId,"add"])}}';
               window.location.href=viewURL;
 });
 $('#btnExit').on('click', function() {
   var viewURL = '{{route('home')}}';
               window.location.href=viewURL;
 });         
});

 
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
             url:'{{route("master",[$FormId,"codeduplicate"])}}',
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
             url:'{{ route("mastermodify",[$FormId,"update"]) }}',
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
 
                   //  window.location.href='{{ route("master",[$FormId,"index"])}}';
                 }
                 
             },
             error:function(data){
               console.log("Error: Something went wrong.");
             },
         });
       
    } // fnSaveData
 
 
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
         url:'{{route("mastermodify",[$FormId,"Approve"])}}',
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
                 window.location.href='{{ route("master",[$FormId,"index"])}}';

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
     window.location.href = "{{route('master',[$FormId,'index'])}}";
 
     });
 
 
     
     $("#OkBtn").click(function(){
       $("#alert").modal('hide');
 
     });////ok button
 
 
    window.fnUndoYes = function (){
       
       //reload form
       window.location.href = "{{route('master',[$FormId,'add'])}}";
 
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

$(function() { 
 //$("#DESCRIPTIONS").focus(); 
});
</script>
 
 @endpush