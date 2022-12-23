@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[$FormId,'index'])}}" class="btn singlebt">Asset Master</a>
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
      <form id="frm_mst_edit" method="POST" onsubmit="return validateForm()" class="needs-validation"> 
        @CSRF
        {{isset($objResponse->ASGID) ? method_field('PUT') : '' }}
       <div class="inner-form">
           
             <div class="row">
               <div class="col-lg-2 pl"><p>Asset Code*</p></div>
                 <div class="col-lg-2 pl">
                   <input type="text" name="ASSETCODE" id="ASSETCODE" value="{{ $HDR->ASSETCODE }}" class="form-control mandatory" tabindex="1" maxlength="100" autocomplete="off" disabled>
                   <span class="text-danger" id="ERROR_ASSTGROUPLCODE"></span>
                   <input type="hidden" id="focusid" >                             
                 </div>

                 <div class="col-lg-2 pl"><p>Asset Description</p></div>
                 <div class="col-lg-2 pl">
                   <input type="text" name="ASTDESCRIPTIONS" id="ASTDESCRIPTIONS" value="{{ $HDR->ASTDESCRIPTIONS }}" class="form-control"  maxlength="100" disabled> 
                 </div>

                 <div class="col-lg-2 pl"><p>Asset Category</p></div>
                 <div class="col-lg-2 pl">
                   <input type="text" name="CATEGORY" id="ASSETCAT" value="{{ $HDR->CATEGORY }}" class="form-control mandatory"  autocomplete="off" disabled readonly/>
                   <input type="hidden" name="ASCATID_REF" id="ASCATID_REF" value="{{ $HDR->ASCATID }}" class="form-control" autocomplete="off" disabled/>
                 </div>
             </div> 
             
             <div class="row">
               <div class="col-lg-2 pl"><p>Category Description</p></div>
                 <div class="col-lg-2 pl">
                   <input type="text" name="CATDESCRIPTIONS" id="CATADESCRIPTIONS" value="{{ $HDR->CATDESCRIPTIONS }}"  class="form-control" readonly  maxlength="100" disabled>  
                 </div>

                 <div class="col-lg-2 pl"><p>Asset Group</p></div>
                 <div class="col-lg-2 pl">
                   <input type="text" name="ASGCODE" id="ASGCODE"       value="{{ $HDR->ASGCODE }}" class="form-control" readonly  maxlength="100" disabled> 
                   <input type="hidden" name="ASGID_REF" id="ASGID_REF" value="{{ $HDR->ASGID }}" class="form-control" readonly  maxlength="100" disabled> 
                 </div>

                 <div class="col-lg-2 pl"><p>Asset Type</p></div>
                 <div class="col-lg-2 pl">
                   <input type="text" name="ASSETTYPE" id="ASSETTYPE"   value="{{ $HDR->ASSETTYPE }}" class="form-control" readonly  maxlength="100" disabled>
                   <input type="hidden" name="ASTID_REF" id="ASTID_REF" value="{{ $HDR->ASTID }}" class="form-control" readonly  maxlength="100" disabled> 
                 </div>
             </div>

             
             <div class="row">
               <div class="col-lg-2 pl"><p>Company Act</p></div>
                 <div class="col-lg-2 pl">
                   <input type="radio" NAME="DMETHOD_INCOMETAX" {{isset($HDR) && $HDR->DMETHOD_INCOMETAX =='1'?'checked':'' }} value="1" class="enable_tb" disabled>
                 </div>

               <div class="col-lg-2 pl"><p>Salvage Rate</p></div>
                 <div class="col-lg-2 pl">
                   <input name="SALAVERATE" id="SALAVERATE" value="{{ $HDR->SALAVERATE }}" class="form-control" onkeypress="return onlyNumberKey(event)"  maxlength="100" type="text" disabled> 
                 </div>

                 <div class="col-lg-2 pl"><p>Life of Asset (in Years)</p></div>
                 <div class="col-lg-2 pl">
                   <input type="text" name="LOASSET" id="LOASSET" value="{{ $HDR->LOASSET }}" class="form-control" onkeypress="return onlyNumberKey(event)"  maxlength="100" disabled> 
                 </div>                   
             </div>
             <div class="row">
               <div class="col-lg-2 pl"><p>WDV Method</p></div>
                 <div class="col-lg-2 pl">
                   <input type="radio" NAME="DMETHOD_INCOMETAX" {{isset($HDR) && $HDR->DMETHOD_INCOMETAX =='2'?'checked':'' }} value="2" disabled> 
                 </div>

               <div class="col-lg-2 pl"><p>WDV Rate</p></div>
                 <div class="col-lg-2 pl">
                   <input type="text" name="WDVRATE" id="WDVRATE" value="{{ $HDR->WDVRATE }}" class="form-control" onkeypress="return onlyNumberKey(event)"  maxlength="100" disabled> 
                 </div>
             </div>
             <br>
             <div class="row">
               <ul class="nav nav-tabs">
                 <li class="active"><a data-toggle="tab" href="#Material" id="tabing1">Material</a></li>
              </ul>
              Note:- 1 row mandatory in Material Tab
              <div id="Material" class="tab-pane fade in active">
               <div class="table-responsive table-wrapper-scroll-y" style="height:280px;margin-top:10px;" >
                 <table id="example2" class="display nowrap table table-striped table-bordered itemlist w-200" width="100%" style="height:auto !important;">
                   <thead id="thead1"  style="position: sticky;top: 0">
                       <tr>
                       <th>Item Code<input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1"></th>
                       <th>Item Name</th>
                       <th>Item Description</th>
                       <th>Main UOM</th>
                       <th>Action</th>
                       </tr>
                   </thead>
                   <tbody>
                    @if(!empty($MAT))
                    @php $n=1; @endphp
                    @foreach($MAT as $key => $row)
                      <tr  class="participantRow">
                        <td hidden><input type="hidden" name="ASSETID_REF" id="ASSETID_REF" value="{{ $row->ASSETID_REF }}" disabled /></td>
                        <td><input type="text" name={{"popupITEMID[]"}} id={{"popupITEMID_".$key}} value="{{ $row->ICODE }}" class="form-control"  autocomplete="off" disabled  readonly/></td>
                        <td hidden><input type="hidden" name={{"ITEMID_REF[]"}} id={{"ITEMID_REF_".$key}} class="form-control"  value="{{ $row->ITEMID_REF }}" disabled autocomplete="off" /></td>
                        <td><input type="text" name={{"ItemName[]"}} id={{"ItemName_".$key}} class="form-control" value="{{ $row->NAME }}"  autocomplete="off" disabled  readonly/></td>
                        <td><input type="text" name={{"Itemspec[]"}} id={{"Itemspec_".$key}} class="form-control"  autocomplete="off" value="{{ $row->ITEM_SPECI }}" disabled readonly  /></td>
                        <td><input type="text" name={{"popupMUOM[]"}} id={{"popupMUOM_".$key}} class="form-control" value="{{ $row->UOMCODE }} {{ $row->DESCRIPTIONS }}"  disabled autocomplete="off"  readonly/></td>
                        <td hidden><input type="hidden" name={{"UOMID_REF[]"}} id={{"UOMID_REF_".$key}}  class="form-control" value="{{ $row->UOMID_REF }}" disabled  autocomplete="off" /></td>
                        <td hidden><input type="hidden" name={{"SE_QTY[]"}} id={{"SE_QTY_".$key}} class="form-control three-digits" maxlength="13" autocomplete="off" disabled  /></td>
                        <td hidden><input type="hidden" name={{"SO_FQTY[]"}} id={{"SO_FQTY_".$key}} class="form-control three-digits" maxlength="13"  autocomplete="off" disabled readonly/></td>
                        <td hidden><input type="hidden" name={{"PRIORITYID[]"}} id={{"PRIORITYID_".$key}} class="form-control"  autocomplete="off" disabled readonly/></td>
                        <td hidden><input type="hidden" name={{"PTID_REF[]"}} id={{"PTID_REF_".$key}} class="form-control"  autocomplete="off" disabled readonly/></td>
                        <td hidden><input class="form-control w-100" type="hidden" name={{"EDD[]"}} id={{"EDD_".$key}} placeholder="dd/mm/yyyy" disabled></td>
      
                        <td align="center" ><button disabled class="btn add material" title="add" data-toggle="tooltip" type="button" ><i class="fa fa-plus"></i></button>
                        <button disabled class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
                  </tr>
                     <tr></tr>
                     @php $n++; @endphp
                      @endforeach 
                        @endif
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



<div id="asetmstpopup" class="modal" role="dialog"  data-backdrop="static">
<div class="modal-dialog modal-md" style="width: 600px;">
 <div class="modal-content">
   <div class="modal-header">
     <button type="button" class="close" data-dismiss="modal" id='emp_closePopup' >&times;</button>
   </div>
 <div class="modal-body">
 <div class="tablename"><p>Asset Category Details</p></div>
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
     <input type="text" autocomplete="off"  class="form-control" id="astcodesearch" onkeyup="AstCodeFunction()" />
   </td>
   <td class="ROW3"  style="width: 40%">
     <input type="text" autocomplete="off"  class="form-control" id="astdessearch" onkeyup="AstDesFunction()" />
   </td>
 </tr>
 </tbody>
 </table>
   <table id="AstTable2" class="display nowrap table  table-striped table-bordered" width="100%">
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



<div id="ITEMIDpopup" class="modal" role="dialog"  data-backdrop="static">
<div class="modal-dialog modal-md" style="width:90%;">
 <div class="modal-content">
   <div class="modal-header"><button type="button" class="close" data-dismiss="modal" id='ITEMID_closePopup' >&times;</button></div>
   <div class="tablename" style="margin:15px;"><p>Item Details</p></div>
     <div class="modal-body">
       
       <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
         <table id="ItemIDTable" class="display nowrap table  table-striped table-bordered" style="width:100%;" >
           <thead>
             <tr id="none-select" class="searchalldata" hidden>  
               <td> 
                 <input type="hidden" name="fieldid" id="hdn_ItemID"/>
                 <input type="hidden" name="fieldid2" id="hdn_ItemID2"/>
                 <input type="hidden" name="fieldid3" id="hdn_ItemID3"/>
                 <input type="hidden" name="fieldid4" id="hdn_ItemID4"/>
                 <input type="hidden" name="fieldid5" id="hdn_ItemID5"/>
                 <input type="hidden" name="fieldid6" id="hdn_ItemID6"/>
                 <input type="hidden" name="fieldid7" id="hdn_ItemID7"/>
                 <input type="hidden" name="fieldid8" id="hdn_ItemID8"/>
                 <input type="hidden" name="fieldid9" id="hdn_ItemID9"/>
                 <input type="hidden" name="fieldid10" id="hdn_ItemID10"/>
                 <input type="hidden" name="fieldid11" id="hdn_ItemID11"/>
                 <input type="hidden" name="fieldid12" id="hdn_ItemID12"/>
                 <input type="hidden" name="fieldid13" id="hdn_ItemID13"/>
                 <input type="hidden" name="fieldid14" id="hdn_ItemID14"/>
                 <input type="hidden" name="fieldid15" id="hdn_ItemID15"/>
                 <input type="hidden" name="fieldid16" id="hdn_ItemID16"/>
                 <input type="hidden" name="fieldid17" id="hdn_ItemID17"/>
               </td>
             </tr>
             
             <tr>
               <th style="width:8%;text-align:center;" id="all-check">Select</th>
               <th style="width:10%;">Item Code</th>
               <th style="width:10%;">Name</th>
               <th style="width:8%;">Main UOM</th>
               <th style="width:8%;">Status</th>
             </tr>
           </thead>
           <tbody>
             <tr>
               <th style="width:8%;text-align:center;">&#10004;</th>
               <td style="width:10%;"><input type="text" id="Itemcodesearch" class="form-control" autocomplete="off" onkeyup="ItemCodeFunction()"></td>
               <td style="width:10%;"><input type="text" id="Itemnamesearch" class="form-control" autocomplete="off" onkeyup="ItemNameFunction()"></td>
               <td style="width:8%;"><input type="text" id="ItemUOMsearch" class="form-control"  autocomplete="off" onkeyup="ItemUOMFunction()"></td>
               <td style="width:8%;"><input type="text" id="ItemStatussearch" class="form-control" autocomplete="off" onkeyup="ItemStatusFunction()"></td>
             </tr>
           </tbody>
         </table>
         
         <table id="ItemIDTable2" class="display nowrap table  table-striped table-bordered" style="width:100%;" >
           <thead id="thead2"></thead>
           <tbody id="tbody_ItemID"></tbody> 
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
 var ASSETCODE  =   $.trim($("[id*=ASSETCODE]").val());
 var ASTDESCRIPTIONS   =   $.trim($("#ASTDESCRIPTIONS").val());
 var CATEGORY   =   $.trim($("#ASSETCAT").val());

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
 else if(ASTDESCRIPTIONS ===""){
   $("#focusid").val('ASTDESCRIPTIONS');
   $("#YesBtn").hide();
   $("#NoBtn").hide();
   $("#OkBtn1").hide();  
   $("#OkBtn").show();              
   $("#AlertMessage").text('Please enter Asset Desciption.');
   $("#alert").modal('show');
   $("#OkBtn").focus();
   return false;
 }
 else if(CATEGORY ===""){
   $("#focusid").val('CATEGORY');
   $("#YesBtn").hide();
   $("#NoBtn").hide();
   $("#OkBtn1").hide();  
   $("#OkBtn").show();              
   $("#AlertMessage").text('Please enter Asset Category.');
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
       if($.trim($(this).find("[id*=popupITEMID]").val()) ==""){            
         allblank1.push('false');
         focustext1 = $(this).find("[id*=popupITEMID]").attr('id');
         textmsg = "Please enter Asset Type";
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
     // var name = el.attr('name') || null;
     // if(name){
     //   var nameLength = name.split('_').pop();
     //   var i = name.substr(name.length-nameLength.length);
     //   var prefix1 = name.substr(0, (name.length-nameLength.length));
     //   el.attr('name', prefix1+(+i+1));
     // }
   });

   $clone.find('input:text').val('');
   $clone.find('[id*="RFQID"]').val('');
   $clone.find('[id*="RFQID_REF"]').val('');
   $clone.find('[id*="PINO"]').val('');
   $clone.find('[id*="ITEMID_REF"]').val('');
   $tr.closest('table').append($clone);         
   var rowCount1 = $('#Row_Count1').val();
   rowCount1 = parseInt(rowCount1)+1;

   $('#Row_Count1').val(rowCount1);
   $clone.find('.remove').removeAttr('disabled'); 

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
 $("#ASSETCODE").blur(function(){
     $(this).val($.trim( $(this).val() ));
     $("#ERROR_ASSTGROUPLCODE").hide();
     validateSingleElemnet("ASSETCODE");
 });

 $( "#ASSETCODE" ).rules( "add", {
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
       if(element_id=="ASSETCODE" || element_id=="ASSETCODE" ) {
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
                 showError('ERROR_ASSTGROUPLCODE',data.msg);
                 $("#ASSETCODE").focus();
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

 
//------------------------
let sgltid = "#AstTable2";
   let sgltid2 = "#MachTable";
   let sglheaders = document.querySelectorAll(sgltid2 + " th");

   // Sort the table element when clicking on the table headers
   sglheaders.forEach(function(element, i) {
     element.addEventListener("click", function() {
       w3.sortHTML(sgltid, ".clsmachine", "td:nth-child(" + (i + 1) + ")");
     });
   });

   function AstCodeFunction() {
     var input, filter, table, tr, td, i, txtValue;
     input = document.getElementById("astcodesearch");
     filter = input.value.toUpperCase();
     table = document.getElementById("AstTable2");
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

function AstDesFunction() {
     var input, filter, table, tr, td, i, txtValue;
     input = document.getElementById("astdessearch");
     filter = input.value.toUpperCase();
     table = document.getElementById("AstTable2");
     tr = table.getElementsByTagName("tr");
     for (i = 0; i < tr.length; i++) {
       td = tr[i].getElementsByTagName("td")[2];
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

$("#ASSETCAT").focus(function(event){

$('#tbody_subglacct').html('Loading...');
$.ajaxSetup({
   headers: {
       'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
   }
});
$.ajax({
   url:'{{route("master",[409,"getemplCode"])}}',
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
 var CATADESCRIPTIONS =   $("#txt"+id+"").data("ccname");
 var ASGCODE =   $("#txt"+id+"").data("lvopbl");
 var ASSETTYPE =   $("#txt"+id+"").data("astype");
 var ASGID_REF =   $("#txt"+id+"").data("asgid");
 var ASTID_REF =   $("#txt"+id+"").data("astid");
 var ASCATID =   $("#txt"+id+"").data("ascatid");
 var oldID =   $("#EMPID_REF").val();

 $("#ASSETCAT").val(texdesc);
 $("#ASSETCAT").blur();
 $("#ASCATID_REF").val(txtval);
 $("#CATADESCRIPTIONS").val(CATADESCRIPTIONS);
 $("#ASGCODE").val(ASGCODE);
 $("#ASSETTYPE").val(ASSETTYPE);
 $("#ASGID_REF").val(ASGID_REF);
 $("#ASTID_REF").val(ASTID_REF);
 $("#ASCATID").val(ASCATID);
 

 if (txtval != oldID)
 {
   $("#txtchecklist_popup").val('');
   $("#CHECKLIST_REF").val('');
   $("#CHECKLISTNAME").val('');
 }
 $("#asetmstpopup").hide();
 $("#machinecodesearch").val(''); 
 $("#machinedatesearch").val(''); 
 $(this).prop("checked",false);
 event.preventDefault();
});
}


function ItemCodeFunction() {
var input, filter, table, tr, td, i, txtValue;
input = document.getElementById("Itemcodesearch");
filter = input.value.toUpperCase();

if(filter.length == 0)
{
 if ($('#Tax_State').length) 
 {
   var taxstate = $('#Tax_State').val();
 }
 else
 {
   var taxstate = '';
 }
 var CODE = ''; 
 var NAME = ''; 
 var MUOM = ''; 
 var GROUP = ''; 
 var CTGRY = ''; 
 var BUNIT = ''; 
 var APART = ''; 
 var CPART = ''; 
 var OPART = ''; 
 loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
}
else if(filter.length >= 3)
{
 if ($('#Tax_State').length) 
 {
   var taxstate = $('#Tax_State').val();
 }
 else
 {
   var taxstate = '';
 }
 var CODE = filter; 
 var NAME = ''; 
 var MUOM = ''; 
 var GROUP = ''; 
 var CTGRY = ''; 
 var BUNIT = ''; 
 var APART = ''; 
 var CPART = ''; 
 var OPART = ''; 
 loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
}
else
{
 table = document.getElementById("ItemIDTable2");
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
}


function ItemNameFunction() {
var input, filter, table, tr, td, i, txtValue;
input = document.getElementById("Itemnamesearch");
filter = input.value.toUpperCase();

if(filter.length == 0)
{
 if ($('#Tax_State').length) 
 {
   var taxstate = $('#Tax_State').val();
 }
 else
 {
   var taxstate = '';
 }
 var CODE = ''; 
 var NAME = ''; 
 var MUOM = ''; 
 var GROUP = ''; 
 var CTGRY = ''; 
 var BUNIT = ''; 
 var APART = ''; 
 var CPART = ''; 
 var OPART = ''; 
 loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
}
else if(filter.length >= 3)
{
 if ($('#Tax_State').length) 
 {
   var taxstate = $('#Tax_State').val();
 }
 else
 {
   var taxstate = '';
 }
 var CODE = ''; 
 var NAME = filter; 
 var MUOM = ''; 
 var GROUP = ''; 
 var CTGRY = ''; 
 var BUNIT = ''; 
 var APART = ''; 
 var CPART = ''; 
 var OPART = ''; 
 loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
}
else
{
 table = document.getElementById("ItemIDTable2");
 tr = table.getElementsByTagName("tr");
 for (i = 0; i < tr.length; i++) {
   td = tr[i].getElementsByTagName("td")[2];
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
}


function ItemUOMFunction() {
var input, filter, table, tr, td, i, txtValue;
input = document.getElementById("ItemUOMsearch");
filter = input.value.toUpperCase();  
if(filter.length == 0)
{
 if ($('#Tax_State').length) 
 {
   var taxstate = $('#Tax_State').val();
 }
 else
 {
   var taxstate = '';
 }
 var CODE = ''; 
 var NAME = ''; 
 var MUOM = ''; 
 var GROUP = ''; 
 var CTGRY = ''; 
 var BUNIT = ''; 
 var APART = ''; 
 var CPART = ''; 
 var OPART = ''; 
 loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
}
else if(filter.length >= 3)
{
 if ($('#Tax_State').length) 
 {
   var taxstate = $('#Tax_State').val();
 }
 else
 {
   var taxstate = '';
 }
 var CODE = ''; 
 var NAME = ''; 
 var MUOM = filter; 
 var GROUP = ''; 
 var CTGRY = ''; 
 var BUNIT = ''; 
 var APART = ''; 
 var CPART = ''; 
 var OPART = ''; 
 loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
}
else
{
 table = document.getElementById("ItemIDTable2");
 tr = table.getElementsByTagName("tr");
 for (i = 0; i < tr.length; i++) {
   td = tr[i].getElementsByTagName("td")[3];
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
}


function loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART){

$("#tbody_ItemID").html('');
$.ajaxSetup({
 headers: {
   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
 }
});
$.ajax({
 url:'{{route("master",[$FormId,"getItemDetails"])}}',
 type:'POST',
 data:{'taxstate':taxstate,'CODE':CODE,'NAME':NAME,'MUOM':MUOM,'GROUP':GROUP,'CTGRY':CTGRY,'BUNIT':BUNIT,'APART':APART,'CPART':CPART,'OPART':OPART},
 success:function(data) {
 $("#tbody_ItemID").html(data); 
 bindItemEvents(); 
 },
 error:function(data){
 console.log("Error: Something went wrong.");
 $("#tbody_ItemID").html('');                        
 },
});

}

$('#Material').on('click','[id*="popupITEMID"]',function(event){

   var CODE = ''; 
   var NAME = ''; 
   var MUOM = ''; 
   var GROUP = ''; 
   var CTGRY = ''; 
   var BUNIT = ''; 
   var APART = ''; 
   var CPART = ''; 
   var OPART = ''; 
   loadItem('',CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART);

   $("#ITEMIDpopup").show();
   var id = $(this).attr('id');
   var id2 = $(this).parent().parent().find('[id*="ITEMID_REF"]').attr('id');
   var id3 = $(this).parent().parent().find('[id*="ItemName"]').attr('id');
   var id4 = $(this).parent().parent().find('[id*="Itemspec"]').attr('id');
   var id5 = $(this).parent().parent().find('[id*="popupMUOM"]').attr('id');
   var id6 = $(this).parent().parent().find('[id*="MAIN_UOMID_REF"]').attr('id');
   var id7 = $(this).parent().parent().find('[id*="SE_QTY"]').attr('id');
   
   var id11 = $(this).parent().parent().find('[id*="SO_FQTY"]').attr('id');

   $('#hdn_ItemID').val(id);
   $('#hdn_ItemID2').val(id2);
   $('#hdn_ItemID3').val(id3);
   $('#hdn_ItemID4').val(id4);
   $('#hdn_ItemID5').val(id5);
   $('#hdn_ItemID6').val(id6);
   $('#hdn_ItemID7').val(id7);
  
   $('#hdn_ItemID11').val(id11);
   event.preventDefault();
 });

 $("#ITEMID_closePopup").click(function(event){
   $("#ITEMIDpopup").hide();
 });

function bindItemEvents(){

 $('#ItemIDTable2').off(); 
 $('.js-selectall1').prop('checked', false); 

 $('[id*="chkId"]').change(function(){

   var fieldid = $(this).parent().parent().attr('id');
   var txtval =   $("#txt"+fieldid+"").val();
   var texdesc =  $("#txt"+fieldid+"").data("desc");
   var fieldid2 = $(this).parent().parent().children('[id*="itemname"]').attr('id');
   var txtname =  $("#txt"+fieldid2+"").val();
   var txtspec =  $("#txt"+fieldid2+"").data("desc");
   var fieldid3 = $(this).parent().parent().children('[id*="itemuom"]').attr('id');
   var txtmuomid =  $("#txt"+fieldid3+"").val();
   var txtauom =  $("#txt"+fieldid3+"").data("desc");
   var txtmuom =  $(this).parent().parent().children('[id*="itemuom"]').text().trim();
   var fieldid4 = $(this).parent().parent().children('[id*="uomqty"]').attr('id');
   var txtauomid =  $("#txt"+fieldid4+"").val();
   var txtauomqty =  $("#txt"+fieldid4+"").data("desc");
   var txtmuomqty =  $(this).parent().parent().children('[id*="uomqty"]').text().trim();
   var fieldid5 = $(this).parent().parent().children('[id*="irate"]').attr('id');
   var txtruom =  $("#txt"+fieldid5+"").val();
   var txtmqtyf = $("#txt"+fieldid5+"").data("desc");
   var fieldid6 = $(this).parent().parent().children('[id*="itax"]').attr('id');
   
   
   txtauomqty = (parseFloat(txtmuomqty)/parseFloat(txtmqtyf))*parseFloat(txtauomqty);
   
   
   if(intRegex.test(txtauomqty)){
       txtauomqty = (txtauomqty +'.000');
   }

   if(intRegex.test(txtmuomqty)){
     txtmuomqty = (txtmuomqty +'.000');
   }
   
  if($(this).is(":checked") == true) 
  {
   $('#example2').find('.participantRow').each(function()
    {
      var itemid = $(this).find('[id*="ITEMID_REF"]').val();
      if(txtval)
      {
           if(txtval == itemid)
           {
                 $('.js-selectall1').prop('checked', false); 
                 $("#ITEMIDpopup").hide();
                 $("#YesBtn").hide();
                 $("#NoBtn").hide();
                 $("#OkBtn").hide();
                 $("#OkBtn1").show();
                 $("#AlertMessage").text('Item already exists.');
                 $("#alert").modal('show');
                 $("#OkBtn1").focus();
                 highlighFocusBtn('activeOk1');
                 $('#hdn_ItemID').val('');
                 $('#hdn_ItemID2').val('');
                 $('#hdn_ItemID3').val('');
                 $('#hdn_ItemID4').val('');
                 $('#hdn_ItemID5').val('');
                 $('#hdn_ItemID6').val('');
                 $('#hdn_ItemID7').val('');
                 
                 $('#hdn_ItemID11').val('');
                 txtval = '';
                 texdesc = '';
                 txtname = '';
                 txtspec = '';
                 txtmuom = '';
                 txtauom = '';
                 txtmuomid = '';
                 txtauomid = '';
                 txtauomqty='';
                 txtmuomqty='';
                 txtruom = '';
                 return false;
           }               
      }          
   });
                 if($('#hdn_ItemID').val() == "" && txtval != '')
                 {
                   var txtid= $('#hdn_ItemID').val();
                   var txt_id2= $('#hdn_ItemID2').val();
                   var txt_id3= $('#hdn_ItemID3').val();
                   var txt_id4= $('#hdn_ItemID4').val();
                   var txt_id5= $('#hdn_ItemID5').val();
                   var txt_id6= $('#hdn_ItemID6').val();
                   var txt_id7= $('#hdn_ItemID7').val();
                   
                   var txt_id11= $('#hdn_ItemID11').val();

                   var $tr = $('.material').closest('table');
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

                   $clone.find('.remove').removeAttr('disabled'); 
                   $clone.find('[id*="popupITEMID"]').val(texdesc);
                   $clone.find('[id*="ITEMID_REF"]').val(txtval);
                   $clone.find('[id*="ItemName"]').val(txtname);
                   $clone.find('[id*="Itemspec"]').val(txtspec);
                   $clone.find('[id*="popupMUOM"]').val(txtmuom);
                   $clone.find('[id*="MAIN_UOMID_REF"]').val(txtmuomid);
                   $clone.find('[id*="SE_QTY"]').val(txtmuomqty);
                   
                   
                   $tr.closest('table').append($clone);   
                   var rowCount = $('#Row_Count1').val();
                     rowCount = parseInt(rowCount)+1;
                     $('#Row_Count1').val(rowCount);
                    
                   $('.js-selectall1').prop('checked', false); 
                   $("#ITEMIDpopup").hide();
                   event.preventDefault();
                 }
                 else
                 {
                 var txtid= $('#hdn_ItemID').val();
                 var txt_id2= $('#hdn_ItemID2').val();
                 var txt_id3= $('#hdn_ItemID3').val();
                 var txt_id4= $('#hdn_ItemID4').val();
                 var txt_id5= $('#hdn_ItemID5').val();
                 var txt_id6= $('#hdn_ItemID6').val();
                 var txt_id7= $('#hdn_ItemID7').val();
                 
                 var txt_id11= $('#hdn_ItemID11').val();
                 $('#'+txtid).val(texdesc);
                 $('#'+txt_id2).val(txtval);
                 $('#'+txt_id3).val(txtname);
                 $('#'+txt_id4).val(txtspec);
                 $('#'+txt_id5).val(txtmuom);
                 $('#'+txt_id6).val(txtmuomid);
                 $('#'+txt_id7).val(txtmuomqty);
                 
                 $('#hdn_ItemID').val('');
                 $('#hdn_ItemID2').val('');
                 $('#hdn_ItemID3').val('');
                 $('#hdn_ItemID4').val('');
                 $('#hdn_ItemID5').val('');
                 $('#hdn_ItemID6').val('');
                 $('#hdn_ItemID7').val('');
                
                 $('#hdn_ItemID11').val('');
                 
                 }

                 $('.js-selectall1').prop('checked', false); 
                 $("#ITEMIDpopup").hide();
                 event.preventDefault();
  }
  else if($(this).is(":checked") == false) 
  {
    var id = txtval;
    var r_count = $('#Row_Count1').val();
    $('#example2').find('.participantRow').each(function()
    {
      var itemid = $(this).find('[id*="ITEMID_REF"]').val();
      if(id == itemid)
      {
         var rowCount = $('#Row_Count1').val();
         if (rowCount > 1) {
           $(this).closest('.participantRow').remove(); 
           rowCount = parseInt(rowCount)-1;
         $('#Row_Count1').val(rowCount);
         }
         else 
         {
           $(document).find('.dmaterial').prop('disabled', true);  
           $('.js-selectall1').prop('checked', false); 
           $("#ITEMIDpopup").hide();
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
      }
   });


   

  }
   $("#Itemcodesearch").val(''); 
   $("#Itemnamesearch").val(''); 
   $("#ItemUOMsearch").val(''); 
   $("#ItemGroupsearch").val(''); 
   $("#ItemCategorysearch").val(''); 
   $("#ItemStatussearch").val(''); 
   $('.remove').removeAttr('disabled'); 
  
   event.preventDefault();
 });
}

// End Item Code


$('input:radio').click(function() { 
$("#SALAVERATE").prop("disabled",true);
if($(this).hasClass('enable_tb')) {
   $("#SALAVERATE").prop("disabled",false);
}
if($(this).hasClass('enable_tbwdv')) {
      $("#WDVRATE").prop("disabled",false);
  }
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