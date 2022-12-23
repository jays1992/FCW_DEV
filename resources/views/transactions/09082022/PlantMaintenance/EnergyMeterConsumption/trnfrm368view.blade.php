
@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
<div class="row">
		<div class="col-lg-2"><a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Energy Meter Consumption</a></div>

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
         
                  <input type="text" name="EMC_NO" id="EMC_NO" disabled value="{{ $objResponse->EMC_NO }}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase" autofocus >
        
       
              
              </div>
              
              <div class="col-lg-2 pl"><p>Document Date</p></div>
              <div class="col-lg-2 pl">
                  <input type="date" name="EMC_DATE" id="EMC_DATE" disabled value="{{ $objResponse->EMC_DATE }}" class="form-control mandatory" autocomplete="off" placeholder="dd/mm/yyyy" >
              </div>
        
          </div>
  
  
             <div class="row">
                    <div class="col-lg-2 pl"><p>Meter Code</p></div>
                    <div class="col-lg-2 pl">
                   
                  
                      <input type="text"  name="EnergyMeter_popup" disabled id="txtEnergyMeter_popup" class="form-control mandatory" value="{{ $objResponse->METER_CODE }}"  autocomplete="off"  readonly/>
                      <input type="hidden" name="ENERGYID_REF" id="ENERGYID_REF" class="form-control" value="{{ $objResponse->ENERGYID_REF }}"  autocomplete="off" />
                     
                       
                        <span class="text-danger" id="ERROR_METER_CODE"></span>                  
           
                      </div>
        
  
                    <div class="col-lg-2 pl"><p>Meter Description</p></div>
                    <div class="col-lg-2 pl">
                      <input type="text" name="METER_DESC" id="METER_DESC" disabled class="form-control mandatory"  value="{{ $objResponse->METER_DESC }}" readonly maxlength="200"  />
                      <span class="text-danger" id="ERROR_METER_DESC"></span> 
                    </div>
              </div>
  
              <div class="row">
              <div class="col-lg-2 pl"><p>From Date</p></div>
                <div class="col-lg-2 pl">
                  <input type="date" name="FROMDATE" disabled class="form-control " id="FROMDATE" value="{{ $objResponse->FROM_DATE }}"  placeholder="dd/mm/yyyy"  />
                </div>
                  <div class="col-lg-2 pl"><p>To Date</p></div>
                <div class="col-lg-2 pl">
                  <input type="date" name="TODATE" disabled class="form-control " id="TODATE" value="{{ $objResponse->TO_DATE }}"  placeholder="dd/mm/yyyy"  />
                </div>
              </div>
  
              <div class="row">
              <div class="col-lg-2 pl"><p>Power Factor</p></div>
                  <div class="col-lg-2 pl">                 
                    <input type="text" name="POWER_FACTOR" disabled id="POWER_FACTOR" class="form-control " value="{{ $objResponse->POWER_FACTOR }}" readonly autocomplete="off" maxlength="100" />                 
                  </div>
                <div class="col-lg-2 pl"><p>Meter Company</p></div>
                <div class="col-lg-2 pl">                 
                  <input type="text" name="METER_COMPANY" disabled id="METER_COMPANY" class="form-control " value="{{ $objResponse->METER_COMPANY }}"  readonly autocomplete="off" maxlength="100" />                 
                </div>
        
              </div>
  
  
  
            <div class="row">
            <div class="col-lg-2 pl"><p>Brand</p></div>
                <div class="col-lg-2 pl">
                  <input type="text" name="BRAND" id="BRAND" disabled class="form-control " readonly value="{{ $objResponse->BRAND }}" autocomplete="off" maxlength="100" />
                </div>
              <div class="col-lg-2 pl"><p>Model</p></div>
              <div class="col-lg-2 pl">
                <input type="text" name="MODEL" id="MODEL" disabled class="form-control " readonly value="{{ $objResponse->MODEL }}" autocomplete="off" maxlength="100" />
              </div>
      
            </div>
           
            <div class="row">
            <div class="col-lg-2 pl"><p>Serial No</p></div>
              <div class="col-lg-2 pl">
                <input type="text" name="SERIAL_NO" id="SERIAL_NO" disabled class="form-control " value="{{ $objResponse->SERIAL_NO }}" readonly  autocomplete="off" maxlength="20" />
              </div>
              <div class="col-lg-2 pl"><p>Load Sanction</p></div>
              <div class="col-lg-2 pl">
                <input type="text" name="SANCTION_LOAD" id="SANCTION_LOAD" disabled class="form-control " value="{{ $objResponse->SANCTION_LOAD }}" readonly  autocomplete="off" maxlength="50" />
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
                <input type="text" name="KWH_STARTED" id="KWH_STARTED" disabled class="form-control " value="{{ $objResponse->METER_READING_START_KWH }}" readonly autocomplete="off" maxlength="20" />
              </div>
             
              <div class="col-lg-2 pl">
                <input type="text" name="KWH_ENDED" id="KWH_ENDED" disabled class="form-control " value="{{ $objResponse->METER_READING_END_KWH }}"  autocomplete="off" maxlength="20" />
              </div>
  
              <div class="col-lg-2 pl">
                <input type="text" name="KWH_CONSUMED" id="KWH_CONSUMED" disabled class="form-control " value="{{ $CONSUME1 }}" readonly autocomplete="off" maxlength="20" />
              </div>
  
            </div>
  
            <div class="row">
              <div class="col-lg-2 pl"><p>Meter Reading (KVARH)</p></div>
              <div class="col-lg-2 pl">
                <input type="text" name="KVARH_STARTED" id="KVARH_STARTED" disabled class="form-control " value="{{ $objResponse->METER_READING_START_KVARH }}" readonly  autocomplete="off" maxlength="20" />
              </div>
             
              <div class="col-lg-2 pl">
                <input type="text" name="KVARH_ENDED" id="KVARH_ENDED" disabled class="form-control " value="{{ $objResponse->METER_READING_END_KVARH }}"  autocomplete="off" maxlength="20" />
              </div>
  
              <div class="col-lg-2 pl">
                <input type="text" name="KVARH_CONSUMED" id="KVARH_CONSUMED" disabled class="form-control " value="{{ $CONSUME2 }}" readonly autocomplete="off" maxlength="20" />
              </div>
  
            </div>
            <div class="row">
              <div class="col-lg-2 pl"><p>Meter Reading (KVAH) </p></div>
              <div class="col-lg-2 pl"> 
                <input type="text" name="KVAH_STARTED" id="KVAH_STARTED" disabled class="form-control " value="{{ $objResponse->METER_READING_START_KVAH }}" readonly  autocomplete="off" maxlength="20" />
              </div>
             
              <div class="col-lg-2 pl">
                <input type="text" name="KVAH_ENDED" id="KVAH_ENDED" disabled class="form-control " value="{{ $objResponse->METER_READING_END_KVAH }}"  autocomplete="off" maxlength="20" />
              </div>
  
              <div class="col-lg-2 pl">
                <input type="text" name="KVAH_CONSUMED" id="KVAH_CONSUMED" disabled class="form-control" value="{{ $CONSUME3 }}" readonly  autocomplete="off" maxlength="20" />
              </div>
  
            </div>
  
  
            <div class="row">
              <div class="col-lg-2 pl"><p>Meter Reading (MD)</p></div>
              <div class="col-lg-2 pl">
                <input type="text" name="MD_STARTED" id="MD_STARTED" disabled class="form-control " value="{{ $objResponse->METER_READING_START_MD }}" readonly autocomplete="off" maxlength="20" />
              </div>
             
              <div class="col-lg-2 pl">
                <input type="text" name="MD_ENDED" id="MD_ENDED" disabled class="form-control "  value="{{ $objResponse->METER_READING_END_MD }}" autocomplete="off" maxlength="20" />
              </div>
  
              <div class="col-lg-2 pl">
                <input type="text" name="MD_CONSUMED" id="MD_CONSUMED" disabled class="form-control " value="{{ $CONSUME4 }}" readonly  autocomplete="off" maxlength="20" />
              </div>
  
            </div>
  
  
            <div class="row">
              <div class="col-lg-2 pl"><p>Meter Running Status</p></div>
              <div class="col-lg-2 pl">
              <select  class="form-control" disabled name="drpstatus" id="drpstatus" > 
                    <option value="">Select</option>
                    <option value="Ok" {{isset($objResponse) && $objResponse->RUNNING_STATUS=='Ok'?'selected':''}}>Ok</option>
                    <option value="Stopped"  {{isset($objResponse) && $objResponse->RUNNING_STATUS=='Stopped'?'selected':''}}>Stopped</option>
                  </select>  
              </div>
             
  
              <div class="col-lg-2 pl"><p>Remarks</p></div>
              <div class="col-lg-2 pl">
                <input type="text" name="REMARKS" id="REMARKS" disabled class="form-control " value="{{ $objResponse->REMARKS }}"  autocomplete="off" maxlength="20" />
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

        $('#txtEnergyMeter_popup').val(desc);
        $('#ENERGYID_REF').val(txtval);
        $('#METER_DESC').val(code);
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

var trnFormReq  = $("#edit_trn_form");
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






     $('#edit_trn_form').on('click','#TODATE',function(e){    
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
$('#edit_trn_form').on('change','#TODATE',function(e){    
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


$('#edit_trn_form').on('change','#FROMDATE',function(e){    
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




  var lastdt = <?php echo json_encode($objlastdt[0]->EMC_DATE); ?>;
  var today = new Date(); 
  var currentdate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;

  $('#EMC_DATE').attr('min',lastdt);
  $('#EMC_DATE').attr('max',currentdate);


  var d = new Date(); 
  var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
 // $('#EMC_DATE').val(today);




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