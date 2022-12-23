
@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
  <div class="row">
    <div class="col-lg-2">
    <a href="{{route('master',[$FormId,'index'])}}" class="btn singlebt">Entitlement Master</a>
    </div>

    <div class="col-lg-10 topnav-pd">
      <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
      <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
      <button class="btn topnavbt" id="btnSaveFormData" ><i class="fa fa-floppy-o"></i> Save</button>
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
  <form id="frm_mst_add" method="POST"  > 
    @CSRF
    <div class="inner-form">
        
      <div class="row">
        <div class="col-lg-2 pl"><p>Document No</p></div>
        <div class="col-lg-2 pl">
          @if(isset($objSON->SYSTEM_GRSR) && $objSON->SYSTEM_GRSR == "1")
            <input type="text" name="ENTITLEMENT_NO" id="ENTITLEMENT_NO" value="{{ $objAutoGenNo }}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase" >
          @elseif(isset($objSON->MANUAL_SR) && $objSON->MANUAL_SR == "1")
            <input type="text" name="ENTITLEMENT_NO" id="ENTITLEMENT_NO" class="form-control mandatory" maxlength="{{$objSON->MANUAL_MAXLENGTH}}" autocomplete="off" style="text-transform:uppercase" >
          @else
          <input type="text" name="ENTITLEMENT_NO" id="ENTITLEMENT_NO" class="form-control mandatory"  autocomplete="off" style="text-transform:uppercase" >
          @endif        
        </div>
        
        <div class="col-lg-2 pl"><p>Document Date</p></div>
            <div class="col-lg-2 pl">    
            <input type="date" name="ENTITLEMENT_DT" id="ENTITLEMENT_DT" class="form-control mandatory" autocomplete="off" >
            </div> 
       </div>

      <div class="row">
         <div class="col-lg-2 pl"><p>Employee Code</p></div>
         <div class="col-lg-2 pl">
              <input type="text" name="Machinepopup" id="txtEmployeepopup" class="form-control mandatory"  autocomplete="off"  readonly/>
              <input type="hidden" name="EMPID_REF" id="EMPID_REF" class="form-control" autocomplete="off" />       
        </div>
      <div class="col-lg-2 pl"><p>Name</p></div>
        <div class="col-lg-2 pl">
             <input type="text" name="EMP_DESC" id="EMP_DESC" class="form-control mandatory" value="{{ old('EMP_DESC') }}" readonly maxlength="200"  />
        </div>
     </div>

      <div class="row">
      <div class="col-lg-2 pl"><p>Department</p></div>
        <div class="col-lg-2 pl">
            <input type="text" name="DEPARTMENT_NAME" id="DEPARTMENT_NAME" class="form-control mandatory"  autocomplete="off"  readonly/>
            <input type="hidden" name="DEPID_REF" id="DEPID_REF" class="form-control" autocomplete="off" />      
        </div>
        <div class="col-lg-2 pl"><p>Division</p></div>
          <div class="col-lg-2 pl">
            <input type="text" name="DIVNAME" id="DIVNAME" class="form-control mandatory" value="{{ old('EMP_DESC') }}" readonly maxlength="200"  />
            <input type="hidden" name="DIVID_REF" id="DIVID_REF" class="form-control" autocomplete="off" />  
          </div>
      </div>

        <div class="row">
          <div class="col-lg-2 pl"><p>Designation</p></div>
            <div class="col-lg-2 pl">
                      <input type="text" name="DESIGNATION" id="DESIGNATION" class="form-control mandatory"  autocomplete="off"  readonly/>
                        <input type="hidden" name="DESIGID_REF" id="DESIGID_REF" class="form-control" autocomplete="off" />      
            </div>
          <div class="col-lg-2 pl"><p>Employee Type</p></div>
            <div class="col-lg-2 pl">

              <input type="text" name="EMPLOYEE_TYPE" id="EMPLOYEE_TYPE" class="form-control mandatory" value="{{ old('EMP_DESC') }}" readonly maxlength="200"  />
              <input type="hidden" name="ETYPEID_REF" id="ETYPEID_REF" class="form-control" autocomplete="off" />    

            </div>
        </div>

        <div class="row">
            <div class="col-lg-2 pl"><p>Announcement Period</p></div>
              <div class="col-lg-2 pl">
              <input type="text" name="PERIOD_popup_REF1" id="PERIOD_popup_REF1" class="form-control mandatory" onclick="get_period('PERIOD_REF1');" autocomplete="off"  readonly/>
                <input type="hidden" name="PERIOD_REF1" id="PERIOD_REF1" class="form-control" autocomplete="off" />
               </div>
            <div class="col-lg-2 pl"><p>Description</p></div>
              <div class="col-lg-2 pl">
                <input type="text" name="PERIOD_DESC_REF1" id="PERIOD_DESC_REF1" class="form-control mandatory" value="{{ old('EMPDESC_REF1') }}" readonly maxlength="200"  />
              </div>
        </div>

      <div class="row">
          <div class="col-lg-2 pl"><p>Entitlement Period</p></div> 
            <div class="col-lg-2 pl">
            <input type="text" name="PERIOD_popup_REF2" id="PERIOD_popup_REF2" class="form-control mandatory" onclick="get_period('PERIOD_REF2');" autocomplete="off"  readonly/>
              <input type="hidden" name="PERIOD_REF2" id="PERIOD_REF2" class="form-control" autocomplete="off" />
              </div>
          <div class="col-lg-2 pl"><p>Description</p></div>
            <div class="col-lg-2 pl">
              <input type="text" name="PERIOD_DESC_REF2" id="PERIOD_DESC_REF2" class="form-control mandatory" value="{{ old('PERIOD_DESC_REF2') }}" readonly maxlength="200"  />
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-2 pl"><p>Approval given by</p></div>
              <div class="col-lg-2 pl">        
                <input type="text" name="APPROVAL_GIVENBY" id="APPROVAL_GIVENBY" class="form-control" autocomplete="off" />       
              </div>
            <div class="col-lg-2 pl"><p>Ref No</p></div>
              <div class="col-lg-2 pl">
                <input type="text" name="REF_NO" id="REF_NO" class="form-control mandatory" value="{{ old('REF_NO') }}"  maxlength="200"  />
              </div>
      </div>
        
        <div class="row">
              <div class="col-lg-2 pl"><p>Salary Sturcture</p></div>
                  <div class="col-lg-2 pl">
                      <input type="text" name="SALARTYSTRUCTION_popup" id="txtsalarystructure_popup" class="form-control mandatory"  autocomplete="off" readonly/>
                      <input type="hidden" name="SALARYSTRUCTUREID_REF" id="SALARYSTRUCTUREID_REF" class="form-control" autocomplete="off" />
                  </div>
                  <div class="col-lg-2 pl"><p>Description</p></div>
                  <div class="col-lg-2 pl">
                        <input type="text" name="SALARY_DESC" id="SALARY_DESC" class="form-control mandatory" value="{{ old('EMP_DESC') }}" readonly maxlength="200"  />
                  </div>
        </div>




    </div>

    <div class="container-fluid purchase-order-view">
      <div class="row">

        <ul class="nav nav-tabs">
           <li class="active"><a data-toggle="tab" href="#EarningHead">Earning/Deduction Head</a></li>
        </ul>  
         
        <div class="tab-content">
          <div id="EarningHead" class="tab-pane fade in active">
            <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar"  >
              <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                <thead id="thead1"  style="position: sticky;top: 0"> 
                  <tr>
                    <th>Earning/Deduction Type</th>
                    <th>Earning/Deduction Code</th>
                    <th>Earning/Deduction Description</th>
                    <th>Earning/Deduction Head Type</th>
                    <th>Sq No</th>
                    <th>Amount / Formula</th>
                    <th>Formula</th>
                    <th>Amount</th>
                    <th>Remarks</th>
                    <th>Action <input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1" value="1"></th>
                  </tr>
                </thead>
                <tbody id="tbody_salarystructure">
                  <tr  class="participantRow1">

                    <td>
                      <input type="text" name="HEAD_TYPE_0" id="HEAD_TYPE_0" class="form-control" readonly  autocomplete="off"  >
  
                    </td>
                  
                    <td><input  type="text" name="EARNING_HEADID_CODE_0" id="EARNING_HEADID_CODE_0" class="form-control"  autocomplete="off"   readonly  /></td>
                    <td hidden><input type="text" name="EARNING_HEADID_REF_0" id="EARNING_HEADID_REF_0"   class="form-control"  autocomplete="off" /></td>
                    
                    <td><input type="text" name="EARNING_HEADID_DESC_0" id="EARNING_HEADID_DESC_0" class="form-control"  autocomplete="off"  readonly /></td>

                    <td><input  type="text" name="EARNING_TYPEID_CODE_0" id="EARNING_TYPEID_CODE_0" class="form-control"  autocomplete="off" readonly  /></td>
                    <td hidden><input type="text" name="EARNING_TYPEID_REF_0" id="EARNING_TYPEID_REF_0"   class="form-control"  autocomplete="off" /></td>

                    <td><input  type="text" name="SQ_NO_0" id="SQ_NO_0" value="" class="form-control"  autocomplete="off" readonly style="width:50px;" /></td>

                    <td>
                      <input type="text" name="AMT_FORMULA_0" id="FOR_TYPE_0" class="form-control"  autocomplete="off"  readonly >
      
                    </td>

                    <td><input type="text" name="FORMULA_0" id="FORMULA_0" class="form-control"  autocomplete="off" readonly /></td>
                    <td><input type="text" name="AMOUNT_0" id="AMOUNT_0" class="form-control" readonly autocomplete="off" onkeypress="return isNumberDecimalKey(event,this)" readonly  /></td>
                    <td><input type="text" name="REMARKS_0" id="REMARKS_0" class="form-control" readonly  autocomplete="off"/></td>

                    <td align="center" >
                      <button class="btn add material" title="add" disabled  data-toggle="tooltip" type="button" ><i class="fa fa-plus"></i></button>
                      <button class="btn remove dmaterial" title="Delete" disabled data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button>
                    </td>

                  </tr>
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



<!-- Employee Dropdown  -->
<div id="employee_popup" class="modal" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-md" style="width:60%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" id="Employee_popupclose">&times;</button>
            </div>
            <div class="modal-body">
                <div class="tablename"><p>Employee List</p></div>
                <div class="single single-select table-responsive table-wrapper-scroll-y my-custom-scrollbar">
                <input type="hidden" class="mainitem_tab1">
                    <table id="EmployeeTable" class="display nowrap table table-striped table-bordered" style="width:100%;">
                        <thead>
                            <tr>
                            <th style="width:10%;">Select</th> 
                                <th style="width:30%;"> Code</th>
                                <th style="width:60%;"> Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                            <th style="text-align:center; width:10%;">&#10004;</th>

                       

                                <td style="width:30%;"> 
                                    <input type="text" id="employeecodesearch" class="form-control" onkeyup="EmployeeCodeFunction()"  />
                                </td>
                                <td style="width:60%;">
                                    <input type="text" id="employeenamesearch" class="form-control" onkeyup="MachineNameFunction()"  />
                                </td>
                          
                            </tr>
                        </tbody>
                    </table>
                    <table id="EmployeeTable2" class="display nowrap table table-striped table-bordered">
                        <thead id="thead2">
                        <tr>
        
                <td id="item_seach" colspan="4">please wait...</td>
          </tr>
                        </thead>
                        <tbody id="Employeeresult">
                           
                        </tbody>
                    </table>
                </div>
                <div class="cl"></div>
            </div>
        </div>
    </div>
</div>





<!-- Payperiod  Dropdown -->
<div id="Period_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:60%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='Department_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Pay Period</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="PeroidTable" class="display nowrap table  table-striped table-bordered" style="width:100%;">
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
    <input type="text" id="Periodscodesearch" class="form-control" onkeyup="PeriodCodeFunction()">
    </td>
    <td style="width:60%;">
    <input type="text" id="Periodnamesearch" class="form-control" onkeyup="PeriodNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="PeroidTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          
        </thead>
        <tbody id="period_result">   
 
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!--  Pay Period  Dropdown ends here -->



<!-- POPUP -->
<div id="salarystructure_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md"  style="width:60%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='checklist_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Salary Structure</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="SalaryStructureTable" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
      <tr>
        <th style="width: 10%" align="center">Select</th> 
        <th  style="width: 40%">Code</th>
        <th style="width: 50%">Description</th>
      </tr>
    </thead>
    <tbody>
    <tr>
      <td style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
      <td style="width: 40%">
        <input type="text" autocomplete="off"  class="form-control" id="salarystructurecodesearch" onkeyup="SalaryStrCodeFunction()"/>
      </td>
      <td style="width: 50%">
        <input type="text" autocomplete="off"  class="form-control" id="salarystructurenamesearch" onkeyup="SalaryStrNameFunction()"/>
      </td>
    </tr>
    </tbody>
    </table>
      <table id="SalaryStructureTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          
        </thead>
        <tbody id="tbody_s_structure">       
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- POPUP-->



@endsection

@push('bottom-scripts')
<script>
//====================// ACTION BUTTON //====================//
$( "#btnSaveFormData" ).click(function() {
  var formTrans = $("#frm_mst_add").validate();
  if(formTrans.valid()){
    validateForm();
  }
});

$("#YesBtn").click(function(){
  $("#alert").modal('hide');
  var customFnName = $("#YesBtn").data("funcname");
  window[customFnName]();
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
  window.location.href = "{{route('master',[$FormId,'add'])}}";
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
  window.location.href = '{{route("master",[$FormId,"index"]) }}';
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
  var FocusId = $("#FocusId").val();

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

//====================// ADD REMOVE //====================//
$("#EarningHead").on('click','.add', function() {
    var $tr = $(this).closest('table');
    var allTrs = $tr.find('.participantRow1').last();
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

    $clone.find('input:text').val('');
    $tr.closest('table').append($clone);         
    var rowCount1 = $('#Row_Count1').val();
    rowCount1 = parseInt(rowCount1)+1;
    $('#Row_Count1').val(rowCount1);
    serialNo('EarningHead','participantRow1','SQ_NO');
    $clone.find('.remove').removeAttr('disabled');        
    
    event.preventDefault();
});

$("#EarningHead").on('click', '.remove', function() {
    var rowCount = $(this).closest('table').find('.participantRow1').length;
    if (rowCount > 1) {

        $(this).closest('.participantRow1').remove();  
        var rowCount1 = $('#Row_Count1').val();
        rowCount1 = parseInt(rowCount1)-1;
        $('#Row_Count1').val(rowCount1);
        serialNo('EarningHead','participantRow1','SQ_NO');
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

function serialNo(table_id,row_id,input_id){
  var i=1;
  $('#'+table_id).find('.'+row_id).each(function(){
    var TextId = $(this).find("[id*="+input_id+"]").attr('id');
    $("#"+TextId).val(i);
    i++;
  });
}

//====================// SHORTING //====================//
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




 //==================== Employee dropdown code ends here ====================//


  let machine = "#EmployeeTable2";
      let machine2 = "#EmployeeTable";
      let machineheaders = document.querySelectorAll(machine2 + " th");

      // Sort the table element when clicking on the table headers
      machineheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(machine, ".clsspid_reasoncode1", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function EmployeeCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("employeecodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("EmployeeTable2");
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

  function MachineNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("employeenamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("EmployeeTable2");
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


  $("#Employee_popupclose").click(function(event){
        $("#employee_popup").hide();
      });


  function bindEmployeeEvents(){
      $(".clsspid_employee").click(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");
        var texcode =   $("#txt"+fieldid+"").data("code");
        //alert(texdesc); 

        var depid =   $("#txt"+fieldid+"").data("depid");
        var depname =   $("#txt"+fieldid+"").data("depname");

        var desigid =   $("#txt"+fieldid+"").data("desigid");
        var designame =   $("#txt"+fieldid+"").data("designame");


        var divid =   $("#txt"+fieldid+"").data("divid");
        var divname =   $("#txt"+fieldid+"").data("divname");

        var etypeid =   $("#txt"+fieldid+"").data("etypeid");
        var employee_type =   $("#txt"+fieldid+"").data("employee_type");


        $('#txtEmployeepopup').val(texcode);
        $('#EMPID_REF').val(txtval);
        $('#EMP_DESC').val(texdesc);


        $('#DEPARTMENT_NAME').val(depname);
        $('#DEPID_REF').val(depid);

        $('#DIVNAME').val(divname);
        $('#DIVID_REF').val(divid);

        $('#DESIGNATION').val(designame);
        $('#DESIGID_REF').val(desigid);

        $('#EMPLOYEE_TYPE').val(employee_type);
        $('#ETYPEID_REF').val(etypeid);

        
        $("#employee_popup").hide();   
        $("#employeecodesearch").val(''); 
        $("#employeenamesearch").val('');    
        
        event.preventDefault();
      });
  }


  $('#txtEmployeepopup').on('click',function(event){   
                $("#Employeeresult").html('');
                var trnFormReq  = $("#frm_mst_add");
                var formData    = trnFormReq.serialize();
                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  });
                  $("#item_seach").show();
                  $.ajax({
                      url:'{{route("master",[$FormId,"get_employee"])}}',
                      type:'POST',
                      data:{'formData':formData},
                      success:function(data) {                                
                        $("#item_seach").hide();
                        $("#Employeeresult").html(data);   
                        showSelectedCheck($("#EMPID_REF").val(),"employee");
                        bindEmployeeEvents();  
                                       
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                        $("#Employeeresult").html('');                        
                      },
                  }); 

            
                  $("#employee_popup").show();                    
         
    });



 //==================== Employee dropdown code ends here ====================//

 //==================== Payperiod dropdown code starts here ====================//

 
   //Payperiod Type dropdown Function starts here 
   let fuel = "#PeroidTable2";
      let fuel2 = "#PeroidTable";
      let fuelheaders = document.querySelectorAll(fuel2 + " th");

      // Sort the table element when clicking on the table headers
      fuelheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(fuel, ".clsspid_department", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function PeriodCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Periodscodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("PeroidTable2");
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

  function PeriodNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Periodnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("PeroidTable2");
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

  function get_period(id){ 
    
         $("#Period_popup").show();
         loadPeriod(id); 
         event.preventDefault();

  }



  function loadPeriod(PERIOD_TYPE){   

   $("#period_result").html('');
   $.ajaxSetup({
     headers: {
       'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
     }
   });
 
   $.ajax({
     url:'{{route("master",[$FormId,"getPeriod"])}}',
     type:'POST',
     data:{'PERIOD_TYPE':PERIOD_TYPE},
     success:function(data) {
       
       $("#period_result").html(data); 
       bindPeriod(PERIOD_TYPE);
      //showSelectedCheck($("#"+PERIOD_TYPE).val(),PERIOD_TYPE); 
     },
     error:function(data){
     console.log("Error: Something went wrong.");
     $("#period_result").html('');                        
     },
   });
 }


      $("#Department_closePopup").click(function(event){
        $("#Period_popup").hide();
      });



    function bindPeriod(id){
      showSelectedCheck($("#"+id).val(),id); 
      var result=id.split('_');  
      var PERIOID= result[1]; 

      $(".clsspid_period").click(function(){
        var fieldid         = $(this).attr('id');
        var txtval          =    $("#txt"+fieldid+"").val();
        var code         =   $("#txt"+fieldid+"").data("code");
        var desc            =   $("#txt"+fieldid+"").data("desc");   


        $('#PERIOD_popup_'+PERIOID).val(code);
        $('#PERIOD_'+PERIOID).val(txtval);
        $('#PERIOD_DESC_'+PERIOID).val(desc);       


        $("#Period_popup").hide();        
        $("#Periodscodesearch").val(''); 
        $("#Periodnamesearch").val('');    
        event.preventDefault();
      });
      }


 //==================== Payperiod dropdown code end here ====================//
 //====================Salary Structure Popup starts here ====================//

 
//Salary Structure Starts
//------------------------
let chktid = "#ChecklistTale2";
      let chktid2 = "#SalaryStructureTable";
      let chkheaders = document.querySelectorAll(chktid2 + " th");

      // Sort the table element when clicking on the table headers
      chkheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(chktid, ".clschecklist", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function SalaryStrCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("salarystructurecodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("SalaryStructureTable2");
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

  function SalaryStrNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("salarystructurenamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("SalaryStructureTable2");
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



  $("#txtsalarystructure_popup").click(function(event){

$('#tbody_s_structure').html('Loading...');
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$.ajax({
    url:'{{route("master",[200,"get_salary_structure"])}}',
    type:'POST',
    success:function(data) {
        $('#tbody_s_structure').html(data);
        bindChecklistEvents();

        showSelectedCheck($("#SALARYSTRUCTUREID_REF").val(),'SELECT_CKLISTID_REF'); 
    },
    error:function(data){
        console.log("Error: Something went wrong.");
        $('#tbody_s_structure').html('');
    },
});        
 $("#salarystructure_popup").show();
 event.preventDefault();
}); 


$("#checklist_closePopup").on("click",function(event){ 
    $("#salarystructure_popup").hide();
    event.preventDefault();
});

function bindChecklistEvents(){

$('.clschecklist').click(function(){

  

  var id = $(this).attr('id');
  var txtval =    $("#txt"+id+"").val();
  var texdesc =   $("#txt"+id+"").data("desc");
  var txtccname =   $("#txt"+id+"").data("ccname");

  
  var oldID =   $("#SALARYSTRUCTUREID_REF").val();
  
  $("#txtsalarystructure_popup").val(texdesc);
  $("#txtsalarystructure_popup").blur();
  $("#SALARYSTRUCTUREID_REF").val(txtval);
  $("#SALARY_DESC").val(txtccname);

  //$("#salarystructure_popup").hide();
  $("#salarystructurecodesearch").val(''); 
  $("#salarystructurenamesearch").val(''); 
  
  SalaryStrCodeFunction();
  SalaryStrNameFunction();



  var vqid = txtval;
      if(vqid!=''){    
          $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          });

          $.ajax({
              url:'{{route("master",[$FormId,"get_salary_structure_data"])}}',
              type:'POST',
              data:{'id':vqid},
              success:function(data) {

            //    alert(data); 
     
                $('#tbody_salarystructure').html(data);   
              },
              error:function(data){
                console.log("Error: Something went wrong.");
              //  $('#tbody_item').html('');
              },
          });




            
      }
      $("#salarystructure_popup").hide()
      event.preventDefault();






});
}
//Salary Structure  Ends





function showSelectedCheck(hidden_value,selectAll){

  var divid ="";

  if(hidden_value !=""){

      var all_location_id = document.querySelectorAll('input[name="'+selectAll+'[]"]');
      
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

function select_formula(id,formula){
  var ROW_ID  = id.split('_').pop();

  $("#FORMULA_"+ROW_ID).val('');
  $("#AMOUNT_"+ROW_ID).val('');
  $("#REMARKS_"+ROW_ID).val('');

  if(formula =="FORMULA"){
    $("#FORMULA_"+ROW_ID).attr('readonly', false);
    $("#AMOUNT_"+ROW_ID).attr('readonly', true);
  }
  else{
    $("#AMOUNT_"+ROW_ID).attr('readonly', false);
    $("#FORMULA_"+ROW_ID).attr('readonly', true);
  }

}

//====================// NUMBER DECIMAL CHECK //====================//

function isNumberDecimalKey(evt){
  var charCode = (evt.which) ? evt.which : event.keyCode
  if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
  return false;

  return true;
}

//====================// VALIDATE FORM //====================//

function validateForm(){
 
  $("#FocusId").val('');

  var ENTITLEMENT_NO   = $.trim($("#ENTITLEMENT_NO").val());
  var ENTITLEMENT_DT = $.trim($("#ENTITLEMENT_DT").val());
  var EMPID_REF = $.trim($("#EMPID_REF").val());
  var PERIOD_REF1 = $.trim($("#PERIOD_REF1").val());
  var PERIOD_REF2 = $.trim($("#PERIOD_REF2").val());
  var APPROVAL_GIVENBY = $.trim($("#APPROVAL_GIVENBY").val());
  var SALARYSTRUCTUREID_REF = $.trim($("#SALARYSTRUCTUREID_REF").val());

  if(ENTITLEMENT_NO ===""){
    $("#FocusId").val('ENTITLEMENT_NO');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please enter document No.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if(ENTITLEMENT_DT ===""){
    $("#FocusId").val('ENTITLEMENT_DT');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select document date.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }     
  else if(EMPID_REF ===""){
    $("#FocusId").val('txtEmployeepopup');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select employee code.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }     
  else if(PERIOD_REF1 ===""){
    $("#FocusId").val('PERIOD_popup_REF1');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select announcement period.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }     
  else if(PERIOD_REF2 ===""){
    $("#FocusId").val('PERIOD_popup_REF2');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select entitlement period.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }     
  else if(APPROVAL_GIVENBY ===""){
    $("#FocusId").val('APPROVAL_GIVENBY');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please enter approval given by.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }     
  else if(SALARYSTRUCTUREID_REF ===""){
    $("#FocusId").val('txtsalarystructure_popup');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select salary structure.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }     
  else{

    event.preventDefault();

    var RackArray = []; 
    var allblank1 = [];
    var allblank2 = [];
    var allblank3 = [];
    var allblank4 = [];
    var allblank5 = [];
    var allblank6 = [];

    allblank1.push('true');
    allblank2.push('true');
    allblank3.push('true');
    allblank4.push('true');
    allblank5.push('true');
    allblank6.push('true');

    var focustext1= "";
    var focustext2= "";
    var focustext3= "";
    var focustext4= "";
    var focustext5= "";
    var focustext6= "";

    $('#EarningHead').find('.participantRow1').each(function(){

      var HEAD_TYPE           = $.trim($(this).find("[id*=HEAD_TYPE]").val());
      var EARNING_HEADID_REF  = $.trim($(this).find("[id*=EARNING_HEADID_REF]").val());
      var EARNING_TYPEID_REF  = $.trim($(this).find("[id*=EARNING_TYPEID_REF]").val());
      var FOR_TYPE            = $.trim($(this).find("[id*=FOR_TYPE]").val());
      var FORMULA             = $.trim($(this).find("[id*=FORMULA]").val());
      var AMOUNT              = $.trim($(this).find("[id*=AMOUNT]").val());

      if(HEAD_TYPE ===""){
        allblank1.push('false');
        focustext1 = $(this).find("[id*=HEAD_TYPE]").attr('id');
        
      }
      else if(EARNING_HEADID_REF ===""){
        allblank2.push('false');
        focustext2 = $(this).find("[id*=EARNING_HEADID_CODE]").attr('id');
      }
      else if(EARNING_TYPEID_REF ===""){
        allblank3.push('false');
        focustext3 = $(this).find("[id*=EARNING_TYPEID_CODE]").attr('id');
      }
      else if(FOR_TYPE ===""){
        allblank4.push('false');
        focustext4 = $(this).find("[id*=FOR_TYPE]").attr('id'); 
      }
      else if(FOR_TYPE =="FORMULA" && FORMULA ===""){
        allblank5.push('false');
        focustext5 = $(this).find("[id*=FORMULA]").attr('id'); 
      }
      else if(FOR_TYPE =="AMOUNT" && AMOUNT ===""){
        allblank6.push('false');
        focustext6 = $(this).find("[id*=AMOUNT]").attr('id'); 
      }

    });

    if(jQuery.inArray("false", allblank1) !== -1){
      $("#FocusId").val(focustext1);
      $("#alert").modal('show');
      $("#AlertMessage").text('Please Select Earning/Deduction Type');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank2) !== -1){
      $("#FocusId").val(focustext2);
      $("#alert").modal('show');
      $("#AlertMessage").text('Please Select Earning/Deduction Code');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank3) !== -1){
      $("#FocusId").val(focustext3);
      $("#alert").modal('show');
      $("#AlertMessage").text('Please Select Earning/Deduction Head Type');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank4) !== -1){
      $("#FocusId").val(focustext4);
      $("#alert").modal('show');
      $("#AlertMessage").text('Please Select Amount / Formula');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank5) !== -1){
      $("#FocusId").val(focustext5);
      $("#alert").modal('show');
      $("#AlertMessage").text('Please Enter Formula');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank6) !== -1){
      $("#FocusId").val(focustext6);
      $("#alert").modal('show');
      $("#AlertMessage").text('Please Enter Amount');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else{
      checkDuplicateCode();
    }
        
  }

}

//====================// CHECK DUPLICATE FOR DOCUMENT  //====================//

function checkDuplicateCode(){
    
    var getDataForm = $("#frm_mst_add");
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
              $("#FocusId").val('ENTITLEMENT_NO');        
              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn1").show();
              $("#AlertMessage").text('Document No already exist.');
              $("#alert").modal('show');
              $("#OkBtn1").focus();
              return false;  
            }
            else{
              checkDuplicateCode_Emp(); 
            }                                
        },
        error:function(data){
          console.log("Error: Something went wrong.");
        },
    });
  }



//====================// CHECK DUPLICATE FOR EMPLOYEE  //====================//

function checkDuplicateCode_Emp(){
    
    var getDataForm = $("#frm_mst_add");
    var formData = getDataForm.serialize();
  
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    
  
    $.ajax({
        url:'{{route("master",[$FormId,"codeduplicate_emp"])}}',
        type:'POST',
        data:formData,
        success:function(data) {
  
            if(data.exists) {
              $("#FocusId").val('ENTITLEMENT_NO');        
              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn1").show();
              $("#AlertMessage").text('Entitlement for this employee has been already exists.');
              $("#alert").modal('show');
              $("#OkBtn1").focus();
              return false;  
            }
            else{
              $("#alert").modal('show');
              $("#AlertMessage").text('Do you want to save to record.');
              $("#YesBtn").data("funcname","fnSaveData");
              $("#OkBtn1").hide();
              $("#OkBtn").hide();
              $("#YesBtn").show();
              $("#NoBtn").show();
              $("#YesBtn").focus();
              highlighFocusBtn('activeYes');
            }                                
        },
        error:function(data){
          console.log("Error: Something went wrong.");
        },
    });
  }

//====================// SAVE DETAILS //====================//

window.fnSaveData = function (){

  event.preventDefault();
  var trnsoForm = $("#frm_mst_add");
  var formData = trnsoForm.serialize();

  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  $.ajax({
    url:'{{ route("master",[$FormId,"save"])}}',
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
      else{                   
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


var d = new Date(); 
  var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
  $('#ENTITLEMENT_DT').val(today);

  $('#btnExit').on('click', function() {
  var viewURL = '{{route('home')}}';
              window.location.href=viewURL;
});
</script>
@endpush