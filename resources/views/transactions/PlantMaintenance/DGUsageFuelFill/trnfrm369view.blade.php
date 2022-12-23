
@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
<div class="row">
		<div class="col-lg-2"><a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">DG Usage & Fuel Fill</a></div>

		<div class="col-lg-10 topnav-pd">
			<button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
      <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
      <button class="btn topnavbt" id="btnSaveSE" disabled="disabled"><i class="fa fa-save" ></i> Save</button>
      <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
      <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
      <button class="btn topnavbt" id="btnUndo"  disabled="disabled"><i class="fa fa-undo"></i> Undo</button>
      <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
      <button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-lock"></i> Approved</button>
      <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
      <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
		</div>
	</div>
</div>
  
<div class="container-fluid purchase-order-view filter">     
   
<form id="edit_trn_form" method="POST"  >
            @CSRF
            <div class="inner-form">
          <div class="row">
            <div class="col-lg-2 pl"><p>Document No</p></div>
            <div class="col-lg-2 pl">
         
                <input type="text" name="DGUFF_NO" id="DGUFF_NO" value="{{ $objResponse->DGUFF_NO }}" disabled class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase" autofocus >
     
            
            </div>
            
            <div class="col-lg-2 pl"><p>Document Date</p></div>
            <div class="col-lg-2 pl">
                <input type="date" name="DGUFF_DATE" id="DGUFF_DATE" value="{{ $objResponse->DGUFF_DATE }}" disabled class="form-control mandatory" autocomplete="off" placeholder="dd/mm/yyyy" >
            </div>      
         </div>


           <div class="row">
                  <div class="col-lg-2 pl"><p>Genset Code</p></div>
                  <div class="col-lg-2 pl">
                 
                
                  <input type="text" name="Machinepopup" id="txtMachinepopup" class="form-control mandatory" disabled value="{{ $objResponse->MACHINE_NO }}"  autocomplete="off"  readonly/>
                    <input type="hidden" name="MACHINE_REF" id="MACHINE_REF" class="form-control" disabled value="{{ $objResponse->MACHINEID_REF }}" autocomplete="off" />
                   
                     
                              
         
                    </div>
      

                  <div class="col-lg-2 pl"><p>Genset Description	</p></div>
                  <div class="col-lg-2 pl">
                    <input type="text" name="MACHINE_DESC" id="MACHINE_DESC" disabled class="form-control mandatory" value="{{ $objResponse->MACHINE_DESC }}" readonly maxlength="200"  />
                  
                  </div>
            </div>

            <div class="row">
            <div class="col-lg-2 pl"><p>Fuel Type</p></div>
              <div class="col-lg-2 pl">
              <input type="text" name="FuelType_popup" id="txtFuelType_popup" disabled class="form-control mandatory" value="{{ $objResponse->FUEL_CODE }}-{{ $objResponse->FUEL_DESC }}"  autocomplete="off"  readonly/>
                    <input type="hidden" name="FUELTYPE_REF" id="FUELTYPE_REF" disabled class="form-control" value="{{ $objResponse->FUELID_REF }}" autocomplete="off" />
              </div>
                <div class="col-lg-2 pl"><p>Standard Consumption (Per Hour)		</p></div>
              <div class="col-lg-2 pl">
              <input type="text" name="CONSUMPTION_PER_HOUR" id="CONSUMPTION_PER_HOUR" disabled class="form-control mandatory" value="{{ $objResponse->STANDARD_CONSUMPTION_PH }}"  maxlength="200"  />
              </div>
              
            </div>

            <div class="row">
            <div class="col-lg-2 pl"><p>UOM</p></div>
                <div class="col-lg-2 pl">                 
              
                <input type="text" name="UOM_popup" disabled id="txtUOM_popup" class="form-control mandatory" value="{{ $objResponse->UOMCODE }}-{{ $objResponse->DESCRIPTIONS }}"  autocomplete="off"  readonly/>
                    <input type="hidden" name="UOMID_REF" id="UOMID_REF" class="form-control" value="{{ $objResponse->UOMID_REF }}" autocomplete="off" />            
                </div>
    
      
            </div>
            <div class="row">
            <div class="col-lg-2 pl"><p>Usage</p></div>
            <div class="col-lg-1 pl">
                <input type="checkbox" name="TYPE" id="USAGE" disabled  value="usage" {{isset($objResponse) && $objResponse->USAGE=='1'?'checked':''}} />  
            </div>
            <div class="col-lg-1 pl"><p>Fuel Consumption</p></div>
            <div class="col-lg-1 pl">
                <input type="checkbox" name="TYPE" id="FUEL_CONSUMPTION" disabled  value="fuel_consumption" {{isset($objResponse) && $objResponse->FUEL_CONSUMPTION=='1'?'checked':''}}  />
            </div>     
            <div class="col-lg-1 pl"><p>Both</p></div>
            <div class="col-lg-1 pl">
                <input type="checkbox" name="TYPE" id="BOTH" value="both" disabled {{isset($objResponse) && $objResponse->BOTH=='1'?'checked':''}} />
            </div>     
        </div>


          <div class="row">
          <div class="col-lg-4 pl"><p>Usage</p></div>
           
            <div class="col-lg-4 pl"><p>Fuel Consumption</p></div>
            <div class="col-lg-2 pl">
            
            </div>
    
          </div>
         
          <div class="row">
          <div class="col-lg-2 pl"><p>From Date</p></div>
            <div class="col-lg-2 pl">
              <input type="date" name="FROMDATE" id="FROMDATE" disabled value="{{ $objResponse->USAGE_FROMDATE }}" class="form-control mandatory" autocomplete="off" placeholder="dd/mm/yyyy" >
            </div>
            <div class="col-lg-2 pl"><p>Opening Fuel</p></div>
            <div class="col-lg-2 pl">
              <input type="text" name="OPENDING_FUEL" id="OPENDING_FUEL" disabled class="form-control "  autocomplete="off" value="{{ $objResponse->OPENING_FUEL }}" maxlength="50" />
            </div>
       
          </div>


          <div class="row">
          <div class="col-lg-2 pl"><p>From time</p></div>
            <div class="col-lg-2 pl">
            <input type="time" name="FROMTIME" id="FROMTIME" disabled class="form-control mandatory" value="{{ $FROMTIME }}"  autocomplete="off"  />
            </div>
            <div class="col-lg-2 pl"><p>Filled Fuel</p></div>
            <div class="col-lg-2 pl">
              <input type="text" name="FILLED_FUEL" id="FILLED_FUEL" disabled class="form-control " value="{{ $objResponse->FILLED_FUEL }}"  autocomplete="off" maxlength="50" />
            </div>       
          </div>
          <div class="row">
          <div class="col-lg-2 pl"><p>To Date</p></div>
            <div class="col-lg-2 pl">
            <input type="date" name="TODATE" id="TODATE" disabled value="{{ $objResponse->USAGE_TODATE }}" class="form-control mandatory" autocomplete="off" placeholder="dd/mm/yyyy" >
            </div>
            <div class="col-lg-2 pl"><p>Closing Fuel</p></div>
            <div class="col-lg-2 pl">
              <input type="text" name="CLOSING_FUEL" id="CLOSING_FUEL" disabled readonly class="form-control " value="{{ $objResponse->CLOSING_FUEL }}"  autocomplete="off" maxlength="50" />
            </div>       
          </div>
          <div class="row">
          <div class="col-lg-2 pl"><p>To Time</p></div>
            <div class="col-lg-2 pl">
            <input type="time" name="TOTIME" id="TOTIME" class="form-control mandatory" disabled value="{{ $TOTIME }}"  autocomplete="off"  />
            </div>
            <div class="col-lg-2 pl"><p>Fuel Filled by</p></div>
            <div class="col-lg-2 pl">
              <input type="text" name="FUEL_FILLED_BY" id="FUEL_FILLED_BY" class="form-control " disabled value="{{ $objResponse->FUEL_FILLEDBY }}"  autocomplete="off" maxlength="50" />
            </div>       
          </div>
          <div class="row">
          <div class="col-lg-2 pl"><p>Genset Started by</p></div>
            <div class="col-lg-2 pl">
              <input type="text" name="GENSET_STARTED_BY" id="GENSET_STARTED_BY" disabled class="form-control " value="{{ $objResponse->GENSET_STARTEDBY }}"  autocomplete="off" maxlength="20" />
            </div>
            <div class="col-lg-2 pl"><p>Remarks</p></div>
            <div class="col-lg-2 pl">
              <input type="text" name="CONSUMPTION_REMARKS" id="CONSUMPTION_REMARKS" disabled class="form-control " value="{{ $objResponse->FUEL_REMARKS }}"  autocomplete="off" maxlength="50" />
            </div>       
          </div>
          <div class="row">
          <div class="col-lg-2 pl"><p>Genset Stopped by</p></div>
            <div class="col-lg-2 pl">
              <input type="text" name="GENSET_STOPPED_BY" id="GENSET_STOPPED_BY" disabled class="form-control " value="{{ $objResponse->GENSET_STOPPEDBY }}"  autocomplete="off" maxlength="20" />
            </div>
            <div class="col-lg-2 pl"><p>Meter (KWH) Started reading</p></div>
            <div class="col-lg-2 pl">
              <input type="text" name="KWH_START_CONSUMPTION" id="KWH_START_CONSUMPTION" disabled class="form-control " value="{{ $objResponse->FUEL_READING_START_KWH }}"  autocomplete="off" maxlength="50" />
            </div>       
          </div>
          <div class="row">
          <div class="col-lg-2 pl"><p>Meter (KWH) Start reading</p></div>
            <div class="col-lg-2 pl">
              <input type="text" name="KWH_START_USAGE" id="KWH_START_USAGE" class="form-control " disabled value="{{ $objResponse->USAGE_READING_START_KWH }}"  autocomplete="off" maxlength="20" />
            </div>
            <div class="col-lg-2 pl"><p>Meter (KWH) Ended Reading</p></div>
            <div class="col-lg-2 pl">
              <input type="text" name="KWH_END_CONSUMPTION" id="KWH_END_CONSUMPTION" class="form-control " disabled value="{{ $objResponse->FUEL_READING_END_KWH }}"  autocomplete="off" maxlength="50" />
            </div>       
          </div>
          <div class="row">
          <div class="col-lg-2 pl"><p>Meter (KWH) End Reading</p></div>
            <div class="col-lg-2 pl">
              <input type="text" name="KWH_END_USAGE" id="KWH_END_USAGE" class="form-control " disabled value="{{ $objResponse->USAGE_READING_END_KWH }}" autocomplete="off" maxlength="20" />
            </div>
            <div class="col-lg-2 pl"><p>Observation</p></div>
            <div class="col-lg-2 pl">
              <input type="text" name="OBSERVATION" id="OBSERVATION" class="form-control " disabled value="{{ $objResponse->OBSERVATION }}"  autocomplete="off" maxlength="50" />
            </div>       
          </div>
          <div class="row">

            <div class="col-lg-2 pl"><p>Remarks</p></div>
            <div class="col-lg-6 pl">
              <input type="text" name="REMARKS" id="REMARKS" class="form-control " disabled  value="{{ $objResponse->USAGE_REMARKS }}" autocomplete="off" maxlength="50" />
            </div>       
          </div>






        
          <br/>
          <br/>
      
        </div>
          </form>
      </div><!--purchase-order-view-->
@endsection
@section('alert')






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
  width: 950px;
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
  width: 1050px;
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
    width: 16%;
}
#CTIDDetTable2 {
  border-collapse: collapse;
  width: 1050px;
  border: 1px solid #ddd;
  font-size: 11px;
}

#CTIDDetTable2 th{
    text-align: left;
    padding: 5px;
    
    font-size: 11px;
    
    color: #0f69cc;
    font-weight: 600;
}

#CTIDDetTable2 td {
  text-align: left;
    padding: 5px;
    font-size: 11px;
    
    font-weight: 600;
    width: 20%;
}
</style>
@endpush
@push('bottom-scripts')
<script>

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







/*================================== TNC HEADER =================================*/
  
</script>

@endpush

@push('bottom-scripts')
<script>
var formTrans = $("#edit_trn_form");
formTrans.validate();

$( "#btnSaveFormData" ).click(function() {
  if(formTrans.valid()){
    validateForm("fnSaveData");
  }
});


$( "#btnApprove" ).click(function() {
 
  if(formTrans.valid()){
    validateForm("fnApproveData");
  }
});

function validateForm(saveAction){
  
  $("#FocusId").val('');
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
else{

               $("#alert").modal('show');
                $("#AlertMessage").text('Do you want to save to record.');
                $("#YesBtn").data("funcname",saveAction);
                $("#OkBtn1").hide();
                $("#OkBtn").hide();
                $("#YesBtn").show();
                $("#NoBtn").show();
                $("#YesBtn").focus();
                highlighFocusBtn('activeYes');
                }

}
  


$("#YesBtn").click(function(){

$("#alert").modal('hide');
var customFnName = $("#YesBtn").data("funcname");
    window[customFnName]();

});

window.fnSaveData = function (){

    event.preventDefault();
    var trnsoForm = $("#edit_trn_form");
    var formData = trnsoForm.serialize();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url:'{{ route("transaction",[$FormId,"update"])}}',
        type:'POST',
        data:formData,
        success:function(data) {
          
            if(data.errors) {
                $(".text-danger").hide();

                if(data.errors.JWONO){
                    showError('ERROR_JWONO',data.errors.JWONO);
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

window.fnApproveData = function (){

    event.preventDefault();
    var trnsoForm = $("#edit_trn_form");
    var formData = trnsoForm.serialize();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url:'{{ route("transaction",[$FormId,"Approve"])}}',
        type:'POST',
        data:formData,
        success:function(data) {
          
            if(data.errors) {
                $(".text-danger").hide();

                if(data.errors.JWONO){
                    showError('ERROR_JWONO',data.errors.JWONO);
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

$("#NoBtn").click(function(){
    $("#alert").modal('hide');
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
    $("#JWONO").focus();
    $(".text-danger").hide();
});


function showError(pId,pVal){
    $("#"+pId+"").text(pVal);
    $("#"+pId+"").show();
}
function getFocus(){
    var FocusId=$("#FocusId").val();
    $("#"+FocusId).focus();
    $("#closePopup").click();
}
function highlighFocusBtn(pclass){
       $(".activeYes").hide();
       $(".activeNo").hide();
       
       $("."+pclass+"").show();
    }

function showAlert(msg){
  $("#alert").modal('show');
  $("#AlertMessage").text(msg);
  $("#YesBtn").hide(); 
  $("#NoBtn").hide();  
  $("#OkBtn1").show();
  $("#OkBtn1").focus();
  highlighFocusBtn('activeOk');
}

function isNumberDecimalKey(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
    return false;

    return true;
}

function doCalculation(){
  $(".blurRate").blur();
}

function showAlert(msg){
  $("#alert").modal('show');
  $("#AlertMessage").text(msg);
  $("#YesBtn").hide();
  $("#NoBtn").hide();
  $("#OkBtn1").show();
  $("#OkBtn1").focus();
  highlighFocusBtn('activeOk');
  return false;
}


//$(document).ready(function(e) {

  //var today         =   new Date(); 
  //var currentdate   =   today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
//   var currentdate   =   <?php //echo json_encode($objMstResponse->JWODT); ?>;

//   $('[id*="EDA_"]').attr('min',currentdate);
//   $('[id*="EDA_"]').val(currentdate);

// });

</script>


@endpush