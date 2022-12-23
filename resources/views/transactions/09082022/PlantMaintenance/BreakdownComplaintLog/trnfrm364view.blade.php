@extends('layouts.app')
@section('content')

<div class="container-fluid topnav">
    <div class="row">
        <div class="col-lg-2">
        <a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Break Down Complaint Log</a>
        </div>

        <div class="col-lg-10 topnav-pd">
                <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
                <button class="btn topnavbt" id="btnSaveSE" disabled="disabled"><i class="fa fa-floppy-o" ></i> Save</button>
                <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                <button class="btn topnavbt" id="btnUndo"  disabled="disabled"><i class="fa fa-undo"></i> Undo</button>
                <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                <button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-thumbs-o-up"></i> Approved</button>
                <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
        </div>
    </div>
</div>

<form id="frm_trn_view" onsubmit="return validateForm()"  method="POST" class="needs-validation"  >  
  <div class="container-fluid filter">
	  <div class="inner-form">

      <div class="row">
        <div class="col-lg-2 pl"><p>Doc No</p></div>
        <div class="col-lg-2 pl">

          <input type="text" name="BDCL_DOCNO" id="BDCL_DOCNO" disabled  value="{{isset($objResponse->BDCL_NO) && $objResponse->BDCL_NO !=''?$objResponse->BDCL_NO:''}}" class="form-control mandatory" readonly  autocomplete="off"  style="text-transform:uppercase" autofocus >
   
          <span class="text-danger" id="ERROR_BDCL_DOCNO"></span>
        </div>
              
        <div class="col-lg-2 pl"><p>Date</p></div>
        <div class="col-lg-2 pl">
          <input type="date" name="BDCL_DATE" id="BDCL_DATE" disabled value="{{isset($objResponse->BDCL_DATE) && $objResponse->BDCL_DATE !=''?$objResponse->BDCL_DATE:''}}" class="form-control mandatory"  placeholder="dd/mm/yyyy" >
          <span class="text-danger" id="ERROR_BDCL_DATE"></span>
        </div>

        <div class="col-lg-2 pl"><p>Time</p></div>
        <div class="col-lg-2 pl">
          <input type="time" name="TIME" id="TIME" disabled value="{{isset($TIME) && $TIME !=''?$TIME:''}}" class="form-control mandatory"  placeholder="" >
          <span class="text-danger" id="ERROR_TIME"></span>
        </div>
      </div>    

      <div class="row">
              

               <div class="col-lg-2 pl"><p>Complaint By</p></div>
                <div class="col-lg-2 pl">
                <input type="text" name="COMPLAINT_BY" disabled id="COMPLAINT_BY" maxlength="50" value="{{isset($objResponse->COMPLAINT_BY) && $objResponse->COMPLAINT_BY !=''?$objResponse->COMPLAINT_BY:''}}" class="form-control mandatory"  placeholder="" >
               <span class="text-danger" id="ERROR_COMPLAINT_BY"></span>
                </div>  

                <div class="col-lg-2 pl"><p>Department</p></div>
                <div class="col-lg-2 pl">
                    <input type="text" disabled name="Department_popup" id="txtDepartment_popup" value="{{isset($objResponse->DEPARTMENT_CODE) && $objResponse->DEPARTMENT_CODE !=''?$objResponse->DEPARTMENT_CODE.'-'.$objResponse->DEPARTMENT_NAME:''}}"  class="form-control mandatory"  autocomplete="off"  readonly/>
                    <input type="hidden" name="DEPARTMENTID_REF" id="DEPARTMENTID_REF" value="{{isset($objResponse->DEPID_REF) && $objResponse->DEPID_REF !=''?$objResponse->DEPID_REF:''}}" class="form-control" autocomplete="off" />
                    <span class="text-danger" id="ERROR_Department_popup"></span>
                </div>  


                <div class="col-lg-2 pl"><p>Priority</p></div>
                <div class="col-lg-2 pl">
                    <input type="text" disabled name="Priority_popup" id="txtPriority_popup" value="{{isset($objResponse->PRIORITYCODE) && $objResponse->PRIORITYCODE !=''?$objResponse->PRIORITYCODE.'-'.$objResponse->PRIORITY_DESC:''}}" class="form-control mandatory"  autocomplete="off"  readonly/>
                    <input type="hidden" name="PRIORITYID_REF" id="PRIORITYID_REF" value="{{isset($objResponse->PRIORITYID_REF) && $objResponse->PRIORITYID_REF !=''?$objResponse->PRIORITYID_REF:''}}" class="form-control" autocomplete="off" />
                    <span class="text-danger" id="ERROR_Priority_popup"></span>
                </div>  

                </div>  

                <div class="row">
               <div class="col-lg-2 pl"><p>Complaint To</p></div>
                <div class="col-lg-2 pl">
                <input type="text" disabled name="COMPLAINT_TO" id="COMPLAINT_TO" value="{{isset($objResponse->COMPLAINT_TO) && $objResponse->COMPLAINT_TO !=''?$objResponse->COMPLAINT_TO:''}}" maxlength="50"  class="form-control mandatory"  placeholder="" >            
                  <span class="text-danger" id="ERROR_COMPLAINT_TO"></span>
                </div>  
                <div class="col-lg-2 pl"><p>Complaint For</p></div>
              <div class="col-lg-2 pl">       
              <input type="checkbox" disabled name="COMPLAINT_FOR"  id="chk_Machine"  {{isset($objResponse) && $objResponse->MACHINE=='1'?'checked':''}} value="Machine"  />&nbsp;&nbsp;<label>   Machine </label>
            </div>
            <div class="col-lg-2 pl">
                  <input type="checkbox" disabled name="COMPLAINT_FOR" id="chk_Generator" value="Genset" {{isset($objResponse) && $objResponse->GENERATOR=='1'?'checked':''}}      /> &nbsp;&nbsp;<label>   Generator </label>
            </div>    

            </div>  



        

        <div class="row">
               <div class="col-lg-2 pl"><p>Machine / Genset  Code</p></div>
                <div class="col-lg-2 pl">
                <input type="text" name="Machinepopup" id="txtMachinepopup" disabled value="{{isset($objResponse->MACHINE_NO) && $objResponse->MACHINE_NO !=''?$objResponse->MACHINE_NO.'-'.$objResponse->MACHINE_DESC:''}}" class="form-control mandatory"  autocomplete="off"  readonly/>
                    <input type="hidden" name="MACHINE_REF" id="MACHINE_REF" value="{{isset($objResponse->MACHINEID_REF) && $objResponse->MACHINEID_REF !=''?$objResponse->MACHINEID_REF:''}}" class="form-control" autocomplete="off" />
                    <span class="text-danger" id="ERROR_txtMachinepopup"></span>
                </div>  

                <div class="col-lg-2 pl"><p>Problem Log Detail </p></div>
                <div class="col-lg-6 pl">
                <input type="text" name="PROBLEM_LOG_DET" id="PROBLEM_LOG_DET" disabled maxlength="50" value="{{isset($objResponse->PLOG_DETAILS) && $objResponse->PLOG_DETAILS !=''?$objResponse->PLOG_DETAILS:''}}" class="form-control mandatory"  placeholder="" >
               <span class="text-danger" id="ERROR_PROBLEM_LOG_DET"></span>
                </div>  

        </div>  



        <div class="row">
                <div class="col-lg-2 pl"><p>Breakdown Reason Code 1</p></div>
                <div class="col-lg-2 pl">
                    <input type="text" name="Reasoncode1_popup" disabled id="txtReasoncode1_popup" value="{{isset($objResponse->REASON1_CODE) && $objResponse->REASON1_CODE !=''?$objResponse->REASON1_CODE:''}}" class="form-control mandatory"  autocomplete="off"  readonly/>
                    <input type="hidden" name="REASONCODE1_REF" id="REASONCODE1_REF" value="{{isset($objResponse->BD_REASONID_REF1) && $objResponse->BD_REASONID_REF1 !=''?$objResponse->BD_REASONID_REF1:''}}" class="form-control" autocomplete="off" />
                    <span class="text-danger" id="ERROR_Reasoncode1"></span>
                </div>  
                <div class="col-lg-2 pl"><p>Breakdown Reason Description 1</p></div>
                <div class="col-lg-6 pl">
                    <input type="text" name="BREAKDOWN1_REASON_DESC" disabled id="BREAKDOWN1_REASON_DESC" value="{{isset($objResponse->REASON1_DESC) && $objResponse->REASON1_DESC !=''?$objResponse->REASON1_DESC:''}}" class="form-control mandatory"  autocomplete="off"  readonly/>
     
                </div>  

                </div> 
                
                

        <div class="row">
        <div class="col-lg-2 pl"><p>Breakdown Reason Code 2</p></div>
                <div class="col-lg-2 pl">
                    <input type="text" name="Reasoncode2_popup" disabled id="txtReasoncode2_popup" value="{{isset($objResponse->REASON2_CODE) && $objResponse->REASON2_CODE !=''?$objResponse->REASON2_CODE:''}}" class="form-control mandatory"  autocomplete="off"  readonly/>
                    <input type="hidden" name="REASONCODE2_REF" id="REASONCODE2_REF" value="{{isset($objResponse->BD_REASONID_REF2) && $objResponse->BD_REASONID_REF2 !=''?$objResponse->BD_REASONID_REF2:''}}" class="form-control" autocomplete="off" />
                    <span class="text-danger" id="ERROR_Reasoncode2"></span>
                </div>  
                <div class="col-lg-2 pl"><p>Breakdown Reason Description 2</p></div>
                <div class="col-lg-6 pl">
                    <input type="text" name="BREAKDOWN2_REASON_DESC" disabled value="{{isset($objResponse->REASON2_DESC) && $objResponse->REASON2_DESC !=''?$objResponse->REASON2_DESC:''}}" id="BREAKDOWN2_REASON_DESC" class="form-control mandatory"  autocomplete="off"  readonly/>
     
                </div>   

                </div>  


                <div class="row">
                <div class="col-lg-2 pl"><p>Remarks 1</p></div>
                <div class="col-lg-4 pl">
                    <input type="text" name="REMARKS1" id="REMARKS1" disabled class="form-control mandatory" value="{{isset($objResponse->REMARKS_1) && $objResponse->REMARKS_1 !=''?$objResponse->REMARKS_1:''}}" autocomplete="off"  />
     
                </div>  
                </div>  
                <div class="row">
                <div class="col-lg-2 pl"><p>Remarks 2</p></div>
                <div class="col-lg-4 pl">
                    <input type="text" name="REMARKS2" id="REMARKS2" disabled class="form-control mandatory" value="{{isset($objResponse->REMARKS_2) && $objResponse->REMARKS_2 !=''?$objResponse->REMARKS_2:''}}" autocomplete="off"  />
     
                </div>  

                </div>  




    </div>

	  <div class="container-fluid">

 
	  </div>
  </div>
</form>

@endsection
@section('alert')
<div id="StoreModal" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:80%;z-index:1">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='StoreModalClose' >&times;</button>
      </div>
      <div class="modal-body">
	      <div class="tablename"><p>Store Details</p></div>
        <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
          <table id="StoreTable" class="display nowrap table  table-striped table-bordered" style="width: 100%;" ></table>
        </div>
		    <div class="cl"></div>
      </div>
    </div>
  </div>
</div>

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
          <button class="btn alertbt" name='YesBtn' id="YesBtn" data-funcname="fnSaveData"><div id="alert-active" class="activeYes"></div>Yes</button>
          <button class="btn alertbt" name='NoBtn' id="NoBtn"   data-funcname="fnUndoNo" ><div id="alert-active" class="activeNo"></div>No</button>
          <button class="btn alertbt" name='OkBtn' id="OkBtn" style="display:none;margin-left: 90px;"><div id="alert-active" class="activeOk"></div>OK</button>
          <button class="btn alertbt" name='OkBtn1' id="OkBtn1" style="display:none;margin-left: 90px;" onclick="getFocus()"><div id="alert-active" class="activeOk1"></div>OK</button>
          <input type="hidden" id="FocusId" >
        </div>
		    <div class="cl"></div>
      </div>
    </div>
  </div>
</div>

@endsection

@push('bottom-css')
<style>
.text-danger{
  color:red !important;
}
#ItemIDcodesearch {
  background-image: url('/css/searchicon.png');
  background-position: 10px 10px;
  background-repeat: no-repeat;
  font-size: 11px;
  padding: 5px 5px 5px 5px;
  border: 1px solid #ddd;
  margin-bottom: 5px;
}
#ItemIDnamesearch {
  background-image: url('/css/searchicon.png');
  background-position: 10px 10px;
  background-repeat: no-repeat;
  font-size: 11px;
  padding: 5px 5px 5px 5px;
  border: 1px solid #ddd;
  margin-bottom: 5px;
}
#ItemIDTable {
  border-collapse: collapse;
  width: 100%;
  border: 1px solid #ddd;
  font-size: 11px;
}
#ItemIDTable th {
  text-align: center;
  padding: 5px;
  font-size: 11px;
  color: #0f69cc;
  font-weight: 600;
}
#ItemIDTable td {
  text-align: center;
  padding: 5px;
  font-size: 11px;
  font-weight: 600;
}
#ItemIDTable2 {
  border-collapse: collapse;
  width: 100%;
  border: 1px solid #ddd;
  font-size: 11px;
}
#ItemIDTable2 th{
  text-align: left;
  padding: 5px;
  font-size: 11px;
  color: #0f69cc;
  font-weight: 600;
}
#ItemIDTable2 td {
  text-align: left;
  padding: 5px;
  font-size: 11px;
  font-weight: 600;
}
#StoreTable {
  border-collapse: collapse;
  width: 950px;
  border: 1px solid #ddd;
  font-size: 11px;
}
#StoreTable th {
  text-align: center;
  padding: 5px;
  font-size: 11px;
  color: #0f69cc;
  font-weight: 600;
}
#StoreTable td {
  text-align: center;
  padding: 5px;
  font-size: 11px;
  font-weight: 600;
}
.qtytext{
  display: block;
  width: 100%;
  height: 24px;
  padding: 6px 6px;
  font-size: 14px;
  line-height: 1.42857143;
  color: #555;
  background-color: #fff;
  background-image: none;
  border: 1px solid #ccc;
}
</style>
@endpush
@push('bottom-scripts')
<script>
/*================================== BUTTON FUNCTION ================================*/
$('#btnAdd').on('click', function() {
  var viewURL = '{{route("transaction",[$FormId,"add"])}}';
  window.location.href=viewURL;
});

$('#btnExit').on('click', function() {
  var viewURL = '{{route('home')}}';
  window.location.href=viewURL;
});

$("#btnUndo").on("click", function() {
  $("#AlertMessage").text("Do you want to erase entered information in this record?");
  $("#alert").modal('show');
  $("#YesBtn").data("funcname","fnUndoYes");
  $("#YesBtn").show();
  $("#NoBtn").data("funcname","fnUndoNo");
  $("#NoBtn").show();    
  $("#OkBtn").hide();
  $("#NoBtn").focus();
});

window.fnUndoYes = function (){
  window.location.reload();
}

$("#btnSave").click(function() {
  var formReqData = $("#frm_trn_view");
  if(formReqData.valid()){
    validateForm('fnSaveData');
  }
});

$("#btnApprove").click(function(){
  var formReqData = $("#frm_trn_view");
  if(formReqData.valid()){
    validateForm('fnApproveData');
  }
});

$("#YesBtn").click(function(){
  $("#alert").modal('hide');
  var customFnName = $("#YesBtn").data("funcname");
  window[customFnName]();
});

$("#NoBtn").click(function(){
  $("#alert").modal('hide');
  $("#LABEL").focus();
});

$("#OkBtn").click(function(){
  $("#alert").modal('hide');
  $("#YesBtn").show();
  $("#NoBtn").show();
  $("#OkBtn").hide();
  $(".text-danger").hide();
  window.location.href = '{{route("transaction",[$FormId,"index"]) }}';
});

$("#OkBtn1").click(function(){
  $("#alert").modal('hide');
  $("#YesBtn").show();
  $("#NoBtn").show();
  $("#OkBtn").hide();
  $("#OkBtn1").hide();
  $("#"+$(this).data('focusname')).focus();
  $(".text-danger").hide();
});

function showError(pId,pVal){
  $("#"+pId+"").text(pVal);
  $("#"+pId+"").show();
}

function getFocus(){
  $("#"+$("#FocusId").val()).focus();
  $("#closePopup").click();
}

function highlighFocusBtn(pclass){
  $(".activeYes").hide();
  $(".activeNo").hide();
  $("."+pclass+"").show();
}





</script>
@endpush