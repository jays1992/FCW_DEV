@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
  <div class="row">
    <div class="col-lg-2"><a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Energy Meter Consumption</a></div>

    <div class="col-lg-10 topnav-pd">
      <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
      <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
      <button class="btn topnavbt" id="btnSave" ><i class="fa fa-floppy-o"></i> Save</button>
      <button style="display:none" class="btn topnavbt buttonload"> <i class="fa fa-refresh fa-spin"></i> {{Session::get('save')}}</button>
      <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
      <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
      <button class="btn topnavbt" id="btnUndo"  ><i class="fa fa-undo"></i> Undo</button>
      <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
      <button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-thumbs-o-up"></i> Approved</button>
      <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
      <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>  
    </div>
  </div>
</div>

<div class="container-fluid purchase-order-view filter">     

<form id="frm_trn_add" onsubmit="return validateForm()"  method="POST" class="needs-validation"  >  
          @CSRF
          <div class="inner-form">
          <div class="row">
            <div class="col-lg-2 pl"><p>Document No</p></div>
            <div class="col-lg-2 pl">
            <input type="text" name="EMC_NO" id="EMC_NO" value="{{$docarray['DOC_NO']}}" {{$docarray['READONLY']}} class="form-control" maxlength="{{$docarray['MAXLENGTH']}}" autocomplete="off" style="text-transform:uppercase" >
          <script>docMissing(@json($docarray['FY_FLAG']));</script>
            
            </div>
            
            <div class="col-lg-2 pl"><p>Document Date</p></div>
            <div class="col-lg-2 pl">
                <input type="date" name="EMC_DATE" id="EMC_DATE" onchange='checkPeriodClosing("{{$FormId}}",this.value,1),getDocNoByEvent("EMC_NO",this,@json($doc_req))' value="{{ old('EMC_DATE') }}" class="form-control mandatory" autocomplete="off" placeholder="dd/mm/yyyy" >
            </div>
      
        </div>


           <div class="row">
                  <div class="col-lg-2 pl"><p>Meter Code</p></div>
                  <div class="col-lg-2 pl">
                 
                
                    <input type="text" name="EnergyMeter_popup" id="txtEnergyMeter_popup" class="form-control mandatory"  autocomplete="off"  readonly/>
                    <input type="hidden" name="ENERGYID_REF" id="ENERGYID_REF" class="form-control" autocomplete="off" />
                   
                     
                      <span class="text-danger" id="ERROR_METER_CODE"></span>                  
         
                    </div>
      

                  <div class="col-lg-2 pl"><p>Meter Description</p></div>
                  <div class="col-lg-2 pl">
                    <input type="text" name="METER_DESC" id="METER_DESC" class="form-control mandatory" value="{{ old('METER_DESC') }}" readonly maxlength="200"  />
                    <span class="text-danger" id="ERROR_METER_DESC"></span> 
                  </div>
            </div>

            <div class="row">
            <div class="col-lg-2 pl"><p>From Date</p></div>
              <div class="col-lg-2 pl">
                <input type="date" name="FROMDATE" class="form-control " id="FROMDATE"  placeholder="dd/mm/yyyy"  />
              </div>
                <div class="col-lg-2 pl"><p>To Date</p></div>
              <div class="col-lg-2 pl">
                <input type="date" name="TODATE" class="form-control " id="TODATE"  placeholder="dd/mm/yyyy"  />
              </div>
            </div>

            <div class="row">
            <div class="col-lg-2 pl"><p>Power Factor</p></div>
                <div class="col-lg-2 pl">                 
                  <input type="text" name="POWER_FACTOR" id="POWER_FACTOR" class="form-control " readonly autocomplete="off" maxlength="100" />                 
                </div>
              <div class="col-lg-2 pl"><p>Meter Company</p></div>
              <div class="col-lg-2 pl">                 
                <input type="text" name="METER_COMPANY" id="METER_COMPANY" class="form-control "  readonly autocomplete="off" maxlength="100" />                 
              </div>
      
            </div>



          <div class="row">
          <div class="col-lg-2 pl"><p>Brand</p></div>
              <div class="col-lg-2 pl">
                <input type="text" name="BRAND" id="BRAND" class="form-control " readonly  autocomplete="off" maxlength="100" />
              </div>
            <div class="col-lg-2 pl"><p>Model</p></div>
            <div class="col-lg-2 pl">
              <input type="text" name="MODEL" id="MODEL" class="form-control " readonly  autocomplete="off" maxlength="100" />
            </div>
    
          </div>
         
          <div class="row">
          <div class="col-lg-2 pl"><p>Serial No</p></div>
            <div class="col-lg-2 pl">
              <input type="text" name="SERIAL_NO" id="SERIAL_NO" class="form-control " readonly  autocomplete="off" maxlength="20" />
            </div>
            <div class="col-lg-2 pl"><p>Load Sanction</p></div>
            <div class="col-lg-2 pl">
              <input type="text" name="SANCTION_LOAD" id="SANCTION_LOAD" class="form-control " readonly  autocomplete="off" maxlength="50" />
            </div>
       
          </div>

          <div class="row">
            <div class="col-lg-2 pl"><p>Particulars</p></div>
            <div class="col-lg-2 pl"><p>Started</p></div>
            <div class="col-lg-2 pl"><p>Ended</p></div>
            <div class="col-lg-2 pl"><p>Consumed</p></div>      
       
          </div>

          
          <div class="row">
            <div class="col-lg-2 pl"><p>Meter Reading (KWH)</p></div>
            <div class="col-lg-2 pl">
              <input type="text" name="KWH_STARTED" id="KWH_STARTED" class="form-control " readonly autocomplete="off" maxlength="20" />
            </div>
           
            <div class="col-lg-2 pl">
              <input type="text" name="KWH_ENDED" id="KWH_ENDED" class="form-control "  autocomplete="off" maxlength="20" />
            </div>

            <div class="col-lg-2 pl">
              <input type="text" name="KWH_CONSUMED" id="KWH_CONSUMED" class="form-control " readonly autocomplete="off" maxlength="20" />
            </div>

          </div>

          <div class="row">
            <div class="col-lg-2 pl"><p>Meter Reading (KVARH)</p></div>
            <div class="col-lg-2 pl">
              <input type="text" name="KVARH_STARTED" id="KVARH_STARTED" class="form-control " readonly  autocomplete="off" maxlength="20" />
            </div>
           
            <div class="col-lg-2 pl">
              <input type="text" name="KVARH_ENDED" id="KVARH_ENDED" class="form-control "  autocomplete="off" maxlength="20" />
            </div>

            <div class="col-lg-2 pl">
              <input type="text" name="KVARH_CONSUMED" id="KVARH_CONSUMED" class="form-control "  readonly autocomplete="off" maxlength="20" />
            </div>

          </div>
          <div class="row">
            <div class="col-lg-2 pl"><p>Meter Reading (KVAH) </p></div>
            <div class="col-lg-2 pl"> 
              <input type="text" name="KVAH_STARTED" id="KVAH_STARTED" class="form-control " readonly  autocomplete="off" maxlength="20" />
            </div>
           
            <div class="col-lg-2 pl">
              <input type="text" name="KVAH_ENDED" id="KVAH_ENDED" class="form-control "  autocomplete="off" maxlength="20" />
            </div>

            <div class="col-lg-2 pl">
              <input type="text" name="KVAH_CONSUMED" id="KVAH_CONSUMED" class="form-control" readonly  autocomplete="off" maxlength="20" />
            </div>

          </div>


          <div class="row">
            <div class="col-lg-2 pl"><p>Meter Reading (MD)</p></div>
            <div class="col-lg-2 pl">
              <input type="text" name="MD_STARTED" id="MD_STARTED" class="form-control " readonly autocomplete="off" maxlength="20" />
            </div>
           
            <div class="col-lg-2 pl">
              <input type="text" name="MD_ENDED" id="MD_ENDED" class="form-control "  autocomplete="off" maxlength="20" />
            </div>

            <div class="col-lg-2 pl">
              <input type="text" name="MD_CONSUMED" id="MD_CONSUMED" class="form-control " readonly  autocomplete="off" maxlength="20" />
            </div>

          </div>


          <div class="row">
            <div class="col-lg-2 pl"><p>Meter Running Status</p></div>
            <div class="col-lg-2 pl">
            <select  class="form-control" name="drpstatus" id="drpstatus" > 
                  <option value="">Select</option>
                  <option value="Ok">Ok</option>
                  <option value="Stopped">Stopped</option>
                </select>  
            </div>
           

            <div class="col-lg-2 pl"><p>Remarks</p></div>
            <div class="col-lg-2 pl">
              <input type="text" name="REMARKS" id="REMARKS" class="form-control "  autocomplete="off" maxlength="20" />
            </div>

 

          </div>

        
          <br/>
          <br/>
      
        </div>
        </form>
    </div><!--purchase-order-view-->
@endsection

@section('alert')


<!-- Meter Code Dropdown -->
<div id="EnergyMeter_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:60%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='Meter_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Meter Code List</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="MeterTable" class="display nowrap table  table-striped table-bordered" style="width:100%;">
    <thead>
    <tr>
            <th style="width:10%;"></th>
            <th style="width:30%;">Code</th>
            <th style="width:60%;">Date</th>
    </tr>
    </thead>
    <tbody>
    <tr>
    <th style="text-align:center; width:10%;">&#10004;</th>
   <td style="width:30%;"> 
    <input type="text" id="Meterscodesearch" class="form-control" onkeyup="MeterCodeFunction()">
    </td>
    <td style="width:60%;">
    <input type="text" id="Meternamesearch" class="form-control" onkeyup="MeterNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="MeterTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          
        </thead>
        <tbody >   
        @if(!empty($objEnergy))
   
        @foreach ($objEnergy as $index=>$objEnergyRow)
        <tr >
    
          
        <td style="text-align:center; width:10%"> <input type="checkbox" name="complaints[]" id="departmentidcode_{{ $index }}" class="clsspid_meters"  value="{{ $objEnergyRow->ENERGYID }}" ></td>

          <td style="width:30%">{{ $objEnergyRow->METER_CODE }}
          <input type="hidden" id="txtdepartmentidcode_{{ $index }}"
           data-code="{{ $objEnergyRow->METER_CODE }}"
           data-desc="{{ $objEnergyRow->METER_DESC }}"
           data-power_factor="{{ $objEnergyRow->POWER_FACTOR }}"
           data-meter_company="{{ $objEnergyRow->METER_COMPANY }}"
           data-brand="{{ $objEnergyRow->BRAND }}"
           data-model="{{ $objEnergyRow->MODEL }}"
           data-serialno="{{ $objEnergyRow->SERIAL_NO }}"
           data-section_load="{{ $objEnergyRow->SANCTION_LOAD }}"


           
           value="{{ $objEnergyRow->ENERGYID }}"/>
          </td>
          <td style="width:60%">{{ $objEnergyRow-> METER_DESC }} </td>
        </tr>
        @endforeach  
        @else
        <td colspan="2">Record not found.</td>
        @endif
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!--  Meter  Dropdown ends here -->




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
  window.location.href = "{{route('transaction',[$FormId,'add'])}}";
}

$("#btnSave" ).click(function() {
    var formReqData = $("#frm_trn_add");
    if(formReqData.valid()){
      validateForm();
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



/*================================== VALIDATE FUNCTION =================================*/
function validateForm(){
  
  $("#FocusId").val('');

  var BDCL_DOCNO   = $.trim($("#EMC_NO").val());
  var EMC_DATE   = $.trim($("#EMC_DATE").val());
  var employee_type=$('[name="EMPLOYEE_TYPE"]:checked').val();
  var ENERGYID_REF    = $.trim($("#ENERGYID_REF").val());
  var FROMDATE    = $.trim($("#FROMDATE").val());
  var TODATE    = $.trim($("#TODATE").val());
  var KWH_ENDED    = $.trim($("#KWH_ENDED").val());
  var KVARH_ENDED    = $.trim($("#KVARH_ENDED").val());
  var KVAH_ENDED    = $.trim($("#KVAH_ENDED").val());
  var MD_ENDED    = $.trim($("#MD_ENDED").val());
  var drpstatus    = $.trim($("#drpstatus").val());
  
  if(BDCL_DOCNO ===""){
      $("#FocusId").val('BDCL_DOCNO');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Doc No is required.');
      $("#alert").modal('show')
      $("#OkBtn1").focus();
      return false;
  }
  else if(EMC_DATE ===""){
      $("#FocusId").val('EMC_DATE');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please select Date.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  }
  else if(ENERGYID_REF ===""){
      $("#FocusId").val('txtEnergyMeter_popup');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please Select Meter Code No.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(FROMDATE ===""){
      $("#FocusId").val('FROMDATE');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please Select From Date.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(TODATE ===""){
      $("#FocusId").val('TODATE');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please Select To Date.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 

  else if(drpstatus ===""){
      $("#FocusId").val('drpstatus');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please Select Meter Running Status.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 



  else if(KWH_ENDED ==="" && KVARH_ENDED==="" && KVAH_ENDED==="" && MD_ENDED===""){
      $("#FocusId").val('KWH_ENDED');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please Enter value in any of the ended input box.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(checkPeriodClosing('{{$FormId}}',$("#EMC_DATE").val(),0) ==0){
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text(period_closing_msg);
    $("#alert").modal('show');
    $("#OkBtn1").focus();
  }
else{


    checkDuplicateCode(); }




}
  



/*================================== CHECK DUPLICATE FUNCTION =================================*/
function checkDuplicateCode(){

  var trnFormReq  = $("#frm_trn_add");
  var formData    = trnFormReq.serialize();

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
              showError('ERROR_BDCL_DOCNO',data.msg);
              $("#BDCL_DOCNO").focus();
          }
          else{
            $("#alert").modal('show');
            $("#AlertMessage").text('Do you want to save to record.');
            $("#YesBtn").data("funcname","fnSaveData");
            $("#YesBtn").focus();
            $("#OkBtn").hide();
            highlighFocusBtn('activeYes');
          }                                
      },
      error:function(data){
        console.log("Error: Something went wrong.");
      },
  });
}

/*================================== Save FUNCTION =================================*/
window.fnSaveData = function (){

event.preventDefault();

var trnFormReq  = $("#frm_trn_add");
var formData    = trnFormReq.serialize();

$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});
$("#btnSave").hide(); 
$(".buttonload").show(); 
$("#btnApprove").prop("disabled", true);
$.ajax({
  url:'{{ route("transaction",[$FormId,"save"])}}',
  type:'POST',
  data:formData,
  success:function(data) {
    $(".buttonload").hide(); 
    $("#btnSave").show();   
    $("#btnApprove").prop("disabled", false);

    if(data.errors) {
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").show();
      $("#AlertMessage").text(data.msg);
      $("#alert").modal('show');
      $("#OkBtn").focus();
      $(".text-danger").show();
    }
    else if(data.success) {                   
      console.log("succes MSG="+data.msg);
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").show();
      $("#AlertMessage").text(data.msg);
      $("#alert").modal('show');
      $("#OkBtn").focus();
      $(".text-danger").hide();
    }
    
  },
  error:function(data){
      $(".buttonload").hide(); 
      $("#btnSave").show();   
      $("#btnApprove").prop("disabled", false);
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
/*================================== POPUP SHORTING FUNCTION =================================*/
"use strict";
var w3 = {};
w3.getElements = function (id) {
  if (typeof id == "object") {
    return [id];
  } else {
    return document.querySelectorAll(id);
  }
};
w3.sortHTML = function(id, sel, sortvalue) {
  var a, b, i, ii, y, bytt, v1, v2, cc, j;
  a = w3.getElements(id);
  for (i = 0; i < a.length; i++) {
    for (j = 0; j < 2; j++) {
      cc = 0;
      y = 1;
      while (y == 1) {
        y = 0;
        b = a[i].querySelectorAll(sel);
        for (ii = 0; ii < (b.length - 1); ii++) {
          bytt = 0;
          if (sortvalue) {
            v1 = b[ii].querySelector(sortvalue).innerText;
            v2 = b[ii + 1].querySelector(sortvalue).innerText;
          } else {
            v1 = b[ii].innerText;
            v2 = b[ii + 1].innerText;
          }
          v1 = v1.toLowerCase();
          v2 = v2.toLowerCase();
          if ((j == 0 && (v1 > v2)) || (j == 1 && (v1 < v2))) {
            bytt = 1;
            break;
          }
        }
        if (bytt == 1) {
          b[ii].parentNode.insertBefore(b[ii + 1], b[ii]);
          y = 1;
          cc++;
        }
      }
      if (cc > 0) {break;}
    }
  }
};


      //Meter Code Dropdown Function starts here 
      let meter = "#MeterTable2";
      let meter2 = "#MeterTable";
      let meterheaders = document.querySelectorAll(meter2 + " th");

      // Sort the table element when clicking on the table headers
      meterheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(meter, ".clsspid_meters", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function MeterCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Meterscodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("MeterTable2");
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

  function MeterNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Meternamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("MeterTable2");
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

  $('#txtEnergyMeter_popup').click(function(event){

    showSelectedCheck($("#ENERGYID_REF").val(),"complaints");
         $("#EnergyMeter_popup").show();
      });

      $("#Meter_closePopup").click(function(event){
        $("#EnergyMeter_popup").hide();
      });
      $(".clsspid_meters").click(function(){
        var fieldid         = $(this).attr('id');
        var txtval          =    $("#txt"+fieldid+"").val();
        var code         =   $("#txt"+fieldid+"").data("code");
        var desc            =   $("#txt"+fieldid+"").data("desc");
        var power_factor     =   $("#txt"+fieldid+"").data("power_factor");
        var meter_company     =   $("#txt"+fieldid+"").data("meter_company");
        var brand      =   $("#txt"+fieldid+"").data("brand");
        var model       =   $("#txt"+fieldid+"").data("model");
        var serialno  =   $("#txt"+fieldid+"").data("serialno");
        var section_load   =   $("#txt"+fieldid+"").data("section_load");

        $('#txtEnergyMeter_popup').val(code);
        $('#ENERGYID_REF').val(txtval);
        $('#METER_DESC').val(desc);
        $('#POWER_FACTOR').val(power_factor);
        $('#METER_COMPANY').val(meter_company);
        $('#BRAND').val(brand);
        $('#MODEL').val(model);
        $('#SERIAL_NO').val(serialno);
        $('#SANCTION_LOAD').val(section_load);





        $("#EnergyMeter_popup").hide();        
        $("#Meterscodesearch").val(''); 
        $("#Meternamesearch").val('');    
        event.preventDefault();
      });



  


function getArraySum(a){
    var total=0;
    for(var i in a) { 
        total += a[i];
    }
    return total;
}

function isNumberDecimalKey(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
    return false;

    return true;
}



/*================================== ONLOAD FUNCTION ==================================*/
function MeterRecords(){

  var Meter=$("#ENERGYID_REF").val();
  if(Meter===''){
      $("#FocusId").val('txtEnergyMeter_popup');
      $("#TODATE").val('');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please Select Meter Code First.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  }else{

var trnFormReq  = $("#frm_trn_add");
var formData    = trnFormReq.serialize();

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$.ajax({
    url:'{{route("transaction",[$FormId,"get_meter_details"])}}',
    type:'POST',
    data:formData,
    success:function(data) {
      
    if(data) {
            $("#KWH_STARTED").val(data.KWH); 
            $("#KVARH_STARTED").val(data.KVARH); 
            $("#KVAH_STARTED").val(data.KVAH); 
            $("#MD_STARTED").val(data.MD); 
        }else{
            $("#KWH_STARTED").val('0.00'); 
            $("#KVARH_STARTED").val('0.00'); 
            $("#KVAH_STARTED").val('0.00'); 
            $("#MD_STARTED").val('0.00'); 

        }
                                    
    },
    error:function(data){
      console.log("Error: Something went wrong.");
    },
});
  }
}



$(document).ready(function(e) {
  $('#KWH_ENDED').on('change',function(){
 
    var STARTED=parseInt($("#KWH_STARTED").val()); 
    var ENDED=parseInt($("#KWH_ENDED").val()); 
      if(ENDED > STARTED){
      var CONSUMED=$(this).val()-STARTED;
    
    if(intRegex.test(CONSUMED)){
          $("#KWH_CONSUMED").val(CONSUMED+'.00');
          }else{
          $("#KWH_CONSUMED").val(CONSUMED);
          } 
          if(intRegex.test($(this).val())){
             $(this).val($(this).val()+'.00')
            }
        event.preventDefault();

    }else{
      $("#FocusId").val('KWH_ENDED');
      $("#KWH_ENDED").val('');
      $("#KWH_CONSUMED").val('');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Ended Value should be greater than Started Value.');
      $("#alert").modal('show')
      $("#OkBtn1").focus();
      return false;
    }
 
        });

  //================================================================================================================================//
  $('#KVARH_ENDED').on('change',function(){
 
 var STARTED=parseInt($("#KVARH_STARTED").val()); 
 var ENDED=parseInt($("#KVARH_ENDED").val()); 
   if(ENDED > STARTED){
   var CONSUMED=$(this).val()-STARTED;
 
 if(intRegex.test(CONSUMED)){
       $("#KVARH_CONSUMED").val(CONSUMED+'.00');
       }else{
       $("#KVARH_CONSUMED").val(CONSUMED);
       } 
       if(intRegex.test($(this).val())){
          $(this).val($(this).val()+'.00')
         }
     event.preventDefault();

 }else{
   $("#FocusId").val('KVARH_ENDED');
   $("#KVARH_ENDED").val('');
   $("#KVARH_CONSUMED").val('');
   $("#ProceedBtn").focus();
   $("#YesBtn").hide();
   $("#NoBtn").hide();
   $("#OkBtn1").show();
   $("#AlertMessage").text('Ended Value should be greater than Started Value.');
   $("#alert").modal('show')
   $("#OkBtn1").focus();
   return false;
 }

     });


  //================================================================================================================================//
  $('#KVAH_ENDED').on('change',function(){
 
 var STARTED=parseInt($("#KVAH_STARTED").val()); 
 var ENDED=parseInt($("#KVAH_ENDED").val()); 
   if(ENDED > STARTED){
   var CONSUMED=$(this).val()-STARTED;
 
 if(intRegex.test(CONSUMED)){
       $("#KVAH_CONSUMED").val(CONSUMED+'.00');
       }else{
       $("#KVAH_CONSUMED").val(CONSUMED);
       } 
       if(intRegex.test($(this).val())){
          $(this).val($(this).val()+'.00')
         }
     event.preventDefault();

 }else{
   $("#FocusId").val('KVAH_ENDED');
   $("#KVAH_ENDED").val('');
   $("#KVAH_CONSUMED").val('');
   $("#ProceedBtn").focus();
   $("#YesBtn").hide();
   $("#NoBtn").hide();
   $("#OkBtn1").show();
   $("#AlertMessage").text('Ended Value should be greater than Started Value.');
   $("#alert").modal('show')
   $("#OkBtn1").focus();
   return false;
 }

     });


  //================================================================================================================================//
  $('#MD_ENDED').on('change',function(){
 
 var STARTED=parseInt($("#MD_STARTED").val()); 
 var ENDED=parseInt($("#MD_ENDED").val()); 
   if(ENDED > STARTED){
   var CONSUMED=$(this).val()-STARTED;
 
 if(intRegex.test(CONSUMED)){
       $("#MD_CONSUMED").val(CONSUMED+'.00');
       }else{
       $("#MD_CONSUMED").val(CONSUMED);
       } 
       if(intRegex.test($(this).val())){
          $(this).val($(this).val()+'.00')
         }
     event.preventDefault();

 }else{
   $("#FocusId").val('MD_ENDED');
   $("#MD_ENDED").val('');
   $("#MD_CONSUMED").val('');
   $("#ProceedBtn").focus();
   $("#YesBtn").hide();
   $("#NoBtn").hide();
   $("#OkBtn1").show();
   $("#AlertMessage").text('Ended Value should be greater than Started Value.');
   $("#alert").modal('show')
   $("#OkBtn1").focus();
   return false;
 }

     });








        

  var dt = new Date();
  var time = moment(dt).format("HH:mm");
$("#TIME").val(time); 




  var lastdt = <?php echo json_encode($objlastdt[0]->EMC_DATE); ?>;
  var today = new Date(); 
  var sodate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;

  $('#EMC_DATE').attr('min',lastdt);
  $('#EMC_DATE').attr('max',sodate);


  var d = new Date(); 
  var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
  $('#EMC_DATE').val(today);

});



function showSelectedCheck(hidden_value,selectAll){

var divid ="";

if(hidden_value !=""){

  var all_location_id = document.querySelectorAll('input[name="'+selectAll+'[]"]');
  //console.log(all_location_id); 
 
  for(var x = 0, l = all_location_id.length; x < l;  x++){
   
    var checkid=all_location_id[x].id;
    var checkval=all_location_id[x].value;
  
    if(hidden_value == checkval){
      divid = checkid;
    }

    $("#"+checkid).prop('checked', false);
    
  }
}

if(divid !=""){
  $("#"+divid).prop('checked', true);
}

}




$('#frm_trn_add').on('click','#TODATE',function(e){    
  if($('#FROMDATE').val()=='' )
{
      $("#FocusId").val('FROMDATE');
      $("#TODATE").val('');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please Select From Date First.');
      $("#alert").modal('show')
      $("#OkBtn1").focus();
      return false;
}

});
$('#frm_trn_add').on('change','#TODATE',function(e){    
  if($(this).val() <$('#FROMDATE').val() && $('#FROMDATE').val()!='' )
{
      $("#FocusId").val('TODATE');
      $("#TODATE").val('');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('To Date cannot be less than From Date.');
      $("#alert").modal('show')
      $("#OkBtn1").focus();
      return false;
}else{

  MeterRecords(); 
}

});


$('#frm_trn_add').on('change','#FROMDATE',function(e){    
  if($(this).val() >$('#TODATE').val() && $('#TODATE').val()!='' )
{
      $("#FocusId").val('FROMDATE');
      $("#FROMDATE").val('');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('From Date cannot be greater than To Date.');
      $("#alert").modal('show')
      $("#OkBtn1").focus();
      return false;
}

});



</script>
@endpush
