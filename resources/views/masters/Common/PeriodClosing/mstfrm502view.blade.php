@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
  <div class="row">
    <div class="col-lg-2"><a href="{{route('master',[$FormId,'index'])}}" class="btn singlebt">Period Closing</a></div>
      <div class="col-lg-10 topnav-pd">
        <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
        <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
        <button class="btn topnavbt" id="btnSaveFormData" disabled="disabled"><i class="fa fa-floppy-o"></i> Save</button>
        <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
        <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
        <button class="btn topnavbt" id="btnUndo"  disabled="disabled" ><i class="fa fa-undo"></i> Undo</button>
        <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
        <button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-thumbs-o-up"></i> Approved</button>
        <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
        <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
    </div>
  </div>
</div>
 
<div class="container-fluid purchase-order-view filter">     
  <form id="master_form" method="POST" onsubmit="return validateForm()" class="needs-validation">
   @CSRF
   <div class="inner-form">              
         <div class="row">
          <div class="col-lg-2 pl"><p>DOC No*</p></div>
          <div class="col-lg-2 pl">
              <input {{$ActionStatus}} type="text" name="DOC_NO" id="DOC_NO" value="{{isset($HDR->PERIODCL_NO)?$HDR->PERIODCL_NO:''}}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase"  >
          </div>
                            
          <div class="col-lg-2 pl"><p>DOC Date*</p></div>
          <div class="col-lg-2 pl">
            <input {{$ActionStatus}} type="date" name="DOC_DT" id="DOC_DT" value="{{isset($HDR->PERIODCL_DATE)?$HDR->PERIODCL_DATE:''}}" class="form-control mandatory" autocomplete="off" placeholder="dd/mm/yyyy" >
          </div>           

          <div class="col-lg-2 pl"><p>From Date*</p></div>
          <div class="col-lg-2 pl">
            <input {{$ActionStatus}} type="date" name="FROMDT" id="FROM_DT" value="{{isset($HDR->PERIODCL_FROM_DATE)?$HDR->PERIODCL_FROM_DATE:''}}" class="form-control mandatory" autocomplete="off" placeholder="dd/mm/yyyy" >
          </div>
        </div>
          
        <div class="row">
          <div class="col-lg-2 pl"><p>To Date</p></div>
          <div class="col-lg-2 pl">
            <input {{$ActionStatus}} type="date" name="TODT" id="TO_DT" value="{{isset($HDR->PERIODCL_TO_DATE)?$HDR->PERIODCL_TO_DATE:''}}" class="form-control mandatory" autocomplete="off" placeholder="dd/mm/yyyy" >
          </div>

          <div class="col-lg-2 pl"><p>Module*</p></div>
          <div class="col-lg-2 pl">
            <input {{$ActionStatus}} type="text" name="PDCLMODULE" id="PDCL_MODULE" onclick="getModule()" value="{{isset($HDR->MODULENAME)?$HDR->MODULENAME:''}}" class="form-control mandatory"  autocomplete="off" readonly/>
            <input type="hidden" name="MODULEIDREF" id="MODULEID_REF" value="{{isset($HDR->MODULEID)?$HDR->MODULEID:''}}" class="form-control" autocomplete="off" />  
          </div>

          <div class="col-lg-2 pl"><p>Month*</p></div>
          <div class="col-lg-2 pl">
            <input {{$ActionStatus}} type="text" name="PDCLMONTH" id="PDCL_MONTH" onclick="getMonths()" value="{{isset($HDR->MTDESCRIPTION)?$HDR->MTDESCRIPTION:''}}" class="form-control mandatory"  autocomplete="off" readonly/>
            <input type="hidden" name="MONTHID_REF" id="MONTHID_REF" value="{{isset($HDR->MTID)?$HDR->MTID:''}}" class="form-control" autocomplete="off" />  
          </div>
        </div>

        <div class="row">
          <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#Material" id="MAT_TAB" >Material</a></li>
          </ul>

          <div class="inner-form">
          <div class="row">
          <div class="col-lg-2 pl"><p>Form Name*</p></div>
            <div class="col-lg-2 pl">
              <input {{$ActionStatus}} type="text" name="PDCLNAME" id="PDCL_NAME" onclick="getFormName(this.id,this.value)" class="form-control mandatory"  autocomplete="off" readonly/>            
              <input type="hidden" name="MODULEID_REF" id="MODULEID_REF" class="form-control" autocomplete="off" />  
              <input type="hidden" name="PDCLNAME[]" id="PDCLNAME_REF" value="{{isset($HDR->MODULEID)?$HDR->MODULEID:''}}" class="form-control mandatory"  autocomplete="off"/>
              <input type="hidden" name="FORMNAMEID_REF" id="FORMNAMEID_REF" class="form-control" autocomplete="off" />
            </div>
          </div>
        </div>


            <div class="tab-content">
            <div id="Material" class="tab-pane fade in active">
              <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:280px;margin-top:10px;" >
                  <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                  <thead id="thead1"  style="position: sticky;top: 0">                      
                    <tr>                          
                    <th rowspan="2"  width="3%">Form Name </th>                         
                    <th rowspan="2" width="3%">Period Lock Type</th>
                    <th rowspan="2" width="3%">From Date</th>
                    <th rowspan="2" width="3%">To Date</th>
                    <th rowspan="2" width="3%">Days</th>
                  </tr>                  
                </thead>
                
                <tbody id="material_body2">
                  @if(isset($MAT) && !empty($MAT))
                    @foreach($MAT as $key => $row)


                    
                  <tr  class="participantRow">
                  
                  <td><input {{$ActionStatus}} type="text" name="PDCLNAME[]" id="PDCL_NAME_{{$key}}" value="{{isset($row->DESCRIPTIONS) && $row->DESCRIPTIONS !=''?$row->DESCRIPTIONS:''}}" class="form-control mandatory"  autocomplete="off" readonly/></td>
                  
                  <td hidden><input type="hidden" name="PDCLNAME_REF[]" id="PDCLNAME_REF_{{$key}}" value="{{isset($row->VTID) && $row->VTID !=''?$row->VTID:''}}" class="form-control mandatory"  autocomplete="off"/></td>

                  <td>
                    <select {{$ActionStatus}} name="LTID_REF[]" id="LTID_REF_{{$key}}" onchange="getPeriodLockTypes(this.id,this.value)" class="form-control mandatory" tabindex="4">
                    <option {{isset($row->PERIODCL_PDLOCK_TYPE) && $row->PERIODCL_PDLOCK_TYPE == 'NULL'?'selected="selected"':''}} value="NULL">Select</option>
                    <option {{isset($row->PERIODCL_PDLOCK_TYPE) && $row->PERIODCL_PDLOCK_TYPE == 'Always Lock'?'selected="selected"':''}} value="Always Lock">Always Lock</option>
                    <option {{isset($row->PERIODCL_PDLOCK_TYPE) && $row->PERIODCL_PDLOCK_TYPE == 'No of Days'?'selected="selected"':''}} value="No of Days">No of Days</option>
                    <option {{isset($row->PERIODCL_PDLOCK_TYPE) && $row->PERIODCL_PDLOCK_TYPE == 'For the Period'?'selected="selected"':''}}value="For the Period">For the Period (From - To)</option>
                    </select> 
                  </td>

                  <td><input {{$ActionStatus}} type="date" name="PDCLFROMDT[]" id="PDCLFROMDT_{{$key}}" value="{{isset($row->PERIODCL_MAT_FROM_DATE) && $row->PERIODCL_MAT_FROM_DATE !=''?$row->PERIODCL_MAT_FROM_DATE:''}}" class="form-control mandatory" autocomplete="off" placeholder="dd/mm/yyyy" readonly ></td>
                  
                  <td><input {{$ActionStatus}} type="date" name="PDCLTODT[]" id="PDCLTODT_{{$key}}" value="{{isset($row->PERIODCL_MAT_TO_DATE) && $row->PERIODCL_MAT_TO_DATE !=''?$row->PERIODCL_MAT_TO_DATE:''}}" class="form-control mandatory" autocomplete="off" placeholder="dd/mm/yyyy" readonly ></td>
                  
                  <td><input {{$ActionStatus}} type="text" name="PDCLDAYS[]" id="PDCLDAYS_{{$key}}" value="{{isset($row->PERIODCL_DAYS) && $row->PERIODCL_DAYS !=''?$row->PERIODCL_DAYS:''}}" onkeypress="return onlyNumberKey(event)" class="form-control mandatory"  autocomplete="off" readonly /></td>
                 
                </tr>
                @endforeach
                @endif
              </tbody>
              {{-- <tbody id="material_body"></tbody> --}}
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
  <div class="modal-dialog"  >
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
          <button class="btn alertbt" name='OkBtn1' id="OkBtn1" onclick="getFocus()" style="display:none;margin-left: 90px;"><div id="alert-active" class="activeOk1"></div>OK</button>
          <input type="hidden" id="FocusId" >
        </div>
		  <div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<div id="popupmodal" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='modal_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p id='title_name'></p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="IPOCodeTable" class="display nowrap table  table-striped table-bordered" >
    <thead>
    <tr>
      <th>Select</th> 
      <th><p id='th_code'></th>
      <th><p id='th_name'></th>
    </tr>
    </thead>
    <tbody>
    <tr>
      <td class="ROW1"><input type="checkbox" class="js-selectall" id="select_all" onchange="appentFormName()" data-target=".js-selectall1" disabled/></td>
      <td class="ROW2"><input type="text" id="ipocodesearch" class="form-control" autocomplete="off" onkeyup="searchData(this.id,1)"></td>
      <td class="ROW3"><input type="text" id="ipodatesearch" class="form-control" autocomplete="off" onkeyup="searchData(this.id,2)"></td>
      </tr>
    </tbody>
    </table>
      <table id="IPOCodeTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2"></thead>
        <tbody id="tbody_divpopp"></tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
@endsection

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

      
$("#modal_close").click(function(event){
  $("#popupmodal").hide();
  event.preventDefault();
});

function getModule(){
  $('#tbody_divpopp').html('Loading...');
      $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
      $.ajax({
          url:'{{route("master",[$FormId,"getModuleDetails"])}}',
          type:'POST',
          success:function(data) {
            var html = '';
            if(data.length > 0){
            $.each(data, function(key, value) {
              html +='<tr>';
              html +='<td style="text-align:center;"><input type="checkbox" name="SELECT_CUSTID_REF[]" id="subgl_'+value.MODULEID+'" class="clsmoduls" value="'+value.MODULEID+'"></td>';
              html +='<td>'+value.MODULECODE+'</td>';
              html +='<td>'+value.MODULENAME+'</td>';
              html +='<td hidden><input type="hidden" id="txtsubgl_'+value.MODULEID+'" data-desc="'+value.MODULENAME+'" value="'+value.MODULEID+'"/></td>';
              html +='</tr>';
            });
          }
          else{
            html +='<tr><td colspan="3" style="text-align:center;">No data available in table</td></tr>';
          }
          $('#tbody_divpopp').html(html);
              bindModul();
              //moduleFormRowReset()
              showSelectedCheck($("#MODULEID_REF").val(),"SELECT_CUSTID_REF");
          },

          error: function (request, status, error) {
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn").show();
          $("#AlertMessage").text(request.responseText);
          $("#alert").modal('show');
          $("#OkBtn").focus();
          highlighFocusBtn('activeOk');
          $("#material_data").html('<tr><td colspan="4" style="text-align:center;">No data available in table</td></tr>');                       
        },
      });
      
      //$('.js-selectall').prop('disabled', true);
      $("#title_name").text('Module Details');
      $("#th_code").text('Module Code');
      $("#th_name").text('Module Name');    
      $("#popupmodal").show();
      event.preventDefault();
    }

  function bindModul(){
      $('.clsmoduls').click(function(){
        loadTab();
      var idsmdl    = $(this).attr('id');
      var addId_Ref =    $("#txt"+idsmdl+"").val();
      var addCode =   $("#txt"+idsmdl+"").data("desc");

      $('#PDCL_MODULE').val(addCode);
      $('#MODULEID_REF').val(addId_Ref);
      $('#PDCLNAME_REF').val(addId_Ref);     
      $("#popupmodal").hide();
    });
  }
    
function getMonths(){
  $('#tbody_divpopp').html('Loading...');
      $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
      $.ajax({
          url:'{{route("master",[$FormId,"getMonthDetails"])}}',
          type:'POST',
          success:function(data) {

            var html = '';
            if(data.length > 0){
            $.each(data, function(key, value) {
              html +='<tr>';
              html +='<td class="ROW1"><input type="checkbox" name="SELECT_CUSTID_REF[]" id="subgl_'+value.MTID+'" class="clsmonth" value="'+value.MTID+'" ></td>';
              html +='<td class="ROW2">'+value.MTCODE+'</td>';
              html +='<td class="ROW3">'+value.MTDESCRIPTION+'</td>';
              html +='<td hidden><input type="hidden" id="txtsubgl_'+value.MTID+'" data-desc="'+value.MTCODE+'-'+value.MTDESCRIPTION+'" value="'+value.MTCODE+'"/></td>';
              html +='</tr>';
            });
          }
          else{
            html +='<tr><td colspan="3" style="text-align:center;">No data available in table</td></tr>';
          }
              $('#tbody_divpopp').html(html);
              bindMonth();
              showSelectedCheck($("#MONTHID_REF").val(),"SELECT_CUSTID_REF");
          },         

          error: function (request, status, error) {
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn").show();
          $("#AlertMessage").text(request.responseText);
          $("#alert").modal('show');
          $("#OkBtn").focus();
          highlighFocusBtn('activeOk');
          $("#material_data").html('<tr><td colspan="4" style="text-align:center;">No data available in table</td></tr>');                       
        },
      });
      $("#title_name").text('Month Details');
      $("#th_code").text('Month Code');
      $("#th_name").text('Month Name');    
      $("#popupmodal").show();
      event.preventDefault();
    }

  function bindMonth(){
      $('.clsmonth').click(function(){

      var idsmnt = $(this).attr('id');
      var txtval =    $("#txt"+idsmnt+"").val();
      var texdesc =   $("#txt"+idsmnt+"").data("desc");
      var texcname =   $("#txt"+idsmnt+"").data("cname");

      var FROM_DT     = $.trim($('[id*=FROM_DT]').val());
      var TO_DT       = $.trim($('[id*=TO_DT]').val());
      var month_code  = $.trim($('[id*=MONTHID_REF]').val());

    $('#tbody_divpopp').html('Loading...');
      $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
      $.ajax({
          url:'{{route("master",[$FormId,"getYear"])}}',
          type:'POST',
          data:{FROM_DT:FROM_DT,TO_DT:TO_DT,month_code:txtval},
          success:function(data) {
            $('#TOTAL_YEAR').val(data);
          },         
      });      

      $('#PDCL_MONTH').val(texdesc);
      $('#MONTHID_REF').val(txtval);    
      $("#PDCLFROM_DT").prop("disabled", false);
      $("#PDCLTO_DT").prop("disabled", false);
      $("#popupmodal").hide();
      });
    } 
    
function getFormName(){

  var ids= $.trim($('[id*=PDCLNAME_REF]').val());
  if(ids ===""){
    $("#FocusId").val(PDCL_MODULE);
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select Module.');
    $("#alert").modal('show')
    $("#OkBtn1").focus();
  } 
  else{
  $('#tbody_divpopp').html('Loading...');
      $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
      $.ajax({
          url:'{{route("master",[$FormId,"getFormNameDetails"])}}',
          type:'POST',
          data:{ids:ids},
          success:function(data) {
              $('#tbody_divpopp').html(data);
              bindFormName();
              $(".js-selectall").prop("disabled", false);
              showSelectedCheck($("#MODULE_NAME").val(),"VTID_REF");
          },
          error:function(data){
          console.log("Error: Something went wrong.");
          $('#tbody_divpopp').html('');
          },
      });
      $("#title_name").text('Form Name Details');
      $("#th_code").text('Form Code');
      $("#th_name").text('Form Name');    
      $("#popupmodal").show();
      event.preventDefault();
    }
  }
    
  function bindFormName(){
      $('.clsfname').click(function(){
      var idsfnm = $(this).attr('id');
      var addId_Ref =    $("#txt"+idsfnm+"").val();
      var addCode =   $("#txt"+idsfnm+"").data("desc");

      $('#PDCL_NAME').val(addCode);
      //$('#FORMNAMEID_REF').val(addId_Ref);
      //$("#popupmodal").hide();
      });
    }



function appentFormName(){
 
  var input1 = document.getElementsByName('VTID_REF[]');
  var input2 = document.getElementsByName('MODULE_NAME[]');

  var month       = $.trim($('[id*=MONTHID_REF]').val());
  var years       = $.trim($('[id*=TOTAL_YEAR]').val());
  var daysInMonth = new Date(years,month,1,-1).getDate();
  var min     = years+'-'+month+'-'+'01';
  var max   = years+'-'+month+'-'+daysInMonth;

  var html  = '';
  var check_status=false;

  if(input1.length > 0){
    for (var i = 0; i < input1.length; i++) {
        var a1 = input1[i];
        var a2 = input2[i];
        var y = years;

        //alert(y);

        if(a1.checked == true){

          check_status=true;
          
          html +='<tr  class="participantRow">';
          html +='<td hidden><input type="hidden" name="TOTAL_YEAR[]" id="TOTAL_YEAR_'+i+'" value="'+y+'" class="form-control mandatory"  autocomplete="off" readonly/></td>';
          html +='<td><input type="text" name="PDCLNAME[]" id="PDCL_NAME_'+i+'" value="'+a2.value+'"  class="form-control mandatory"  autocomplete="off" readonly/></td>';
          html +='<td hidden><input type="hidden" name="PDCLNAME_REF[]" id="PDCLNAME_REF_'+i+'" value="'+a1.value+'" class="form-control mandatory"  autocomplete="off"/></td>';
          html +='<td hidden><input type="hidden" name="FORMNAMEID_REF[]" id="FORMNAMEID_REF_'+i+'" class="form-control" autocomplete="off" /></td>';
          html +='<td><select name="LTID_REF[]" id="LTID_REF_'+i+'" onchange="getPeriodLockType(this.id,this.value)" class="form-control mandatory" tabindex="4"><option value="NULL">Select</option><option value="Always Lock">Always Lock </option> <option value="No of Days">No of Days </option><option value="For the Period"> For the Period (From - To) </option></select></td>';
          html +='<td><input type="date" name="PDCLFROMDT[]" id="PDCLFROM_DT_'+i+'" value="'+min+'" class="form-control period_date" autocomplete="off" placeholder="dd/mm/yyyy" readonly></td>';
          html +='<td><input type="date" name="PDCLTODT[]" id="PDCLTO_DT_'+i+'" value="'+min+'" class="form-control period_date" autocomplete="off" placeholder="dd/mm/yyyy" readonly></td>';
          html +='<td><input type="text" name="PDCLDAYS[]" id="PDCLDAYS_'+i+'" onkeypress="return onlyNumberKey(event)" class="form-control mandatory"  autocomplete="off" readonly/></td>';
          html +='</tr>';
        }
        else{
          $("#select_all").prop('checked', false);
        }

        if ($('.clsfname:checked').length == $('.clsfname').length ){
          $("#select_all").prop('checked', true);
        }

    }

    if(check_status ==true){
      $("#material_body").html(html);
      $('.period_date').attr('min',min);
      $('.period_date').attr('max',max);
    }
    else{
      loadTab();
    }
  }
}

$(".js-selectall").click(function () {
  $('input:checkbox').not(this).prop('checked', this.checked);
});


let tid = "#IPOCodeTable2";
let tid2 = "#IPOCodeTable";
let headers = document.querySelectorAll(tid2 + " th");

headers.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(tid, ".clsipoid", "td:nth-child(" + (i + 1) + ")");
  });
});

function searchData(txtid,no){
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById(txtid);
  filter = input.value.toUpperCase();
  table = document.getElementById("IPOCodeTable2");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[no];
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

var formTrans = $("#master_form");
formTrans.validate();

function saveAction(action){
  if(formTrans.valid()){
    validateForm(action);
  }
}

function alertMsg(id,msg){		
      $("#FocusId").val(id);
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text(msg);
      $("#alert").modal('show')
      $("#OkBtn1").focus();
      return false;
		}

function validateForm(action){
  $("#FocusId").val('');
  var DOC_NO     =   $.trim($("#DOC_NO").val());
  var DOC_DT     =   $.trim($("#DOC_DT").val());
  var FROM_DT  =   $.trim($("#FROM_DT").val());
  var TO_DT =   $.trim($("#TO_DT").val());
  var MODULEID_REF =   $.trim($("#MODULEID_REF").val());
  var MONTHID_REF =   $.trim($("#MONTHID_REF").val());

  if(DOC_NO ===""){
    alertMsg('DOC_NO','Please enter value in DOC No.');
  }
  else if(DOC_DT ===""){
    alertMsg('DOC_DT','Please enter select DOC Date.');
  } 
  else if(FROM_DT ===""){   
    alertMsg('FROM_DT','Please enter select From Date.');    
  }  
  else if(TO_DT ===""){
    alertMsg('TO_DT','Please enter select To Date.'); 
  } 
  
  else if(MODULEID_REF ===""){    
    alertMsg('PDCL_MODULE','Please enter select Module.');
  } 

  else if(MONTHID_REF ===""){
    alertMsg('PDCL_MONTH','Please select Month.');
  } 
  else{

    event.preventDefault();
        var allblank1 = [];
        var focustext1= "";
        var textmsg = "";
  
        $('#example2').find('.participantRow').each(function(){

        if($.trim($(this).find("[id*=PDCLNAME_REF]").val()) ===""){
          allblank1.push('false');
          focustext1 = $(this).find("[id*=PDCL_NAME]").attr('id');
          textmsg = 'Please select Invoice No';
        }
          else{
          allblank1.push('true');
          focustext1   = "";
        }

        });


      if(jQuery.inArray("false", allblank1) !== -1){
      $("#FocusId").val(focustext1);
      $("#alert").modal('show');
      $("#AlertMessage").text(textmsg);
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }else{
      $("#alert").modal('show');
      $("#AlertMessage").text('Do you want to '+action+' to record.');
      $("#YesBtn").data("funcname","fnSaveData");
      $("#YesBtn").data("action",action);
      $("#OkBtn1").hide();
      $("#OkBtn").hide();
      $("#YesBtn").show();
      $("#NoBtn").show();
      $("#YesBtn").focus();
      highlighFocusBtn('activeYes');
    }    
  }
}

$("#YesBtn").click(function(){
  $("#alert").modal('hide');
  var customFnName  = $("#YesBtn").data("funcname");
  var action        = $("#YesBtn").data("action");

  if(action ==="save"){
    window[customFnName]('{{route("master",[$FormId,"save"])}}');
  }
  else if(action ==="update"){
    window[customFnName]('{{route("master",[$FormId,"update"])}}');
  }
  else if(action ==="approve"){
    window[customFnName]('{{route("master",[$FormId,"Approve"])}}');
  }
  else{
    window.location.href = '{{route("master",[$FormId,"index"]) }}';
  }
});

window.fnSaveData = function (path){

  event.preventDefault();
  var trnsoForm = $("#master_form");
  var formData = trnsoForm.serialize();

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $("#btnSaveFormData").hide(); 
  $(".buttonload").show(); 
  $("#btnApprove").prop("disabled", true);

  $.ajax({
    url:path,
    type:'POST',
    data:formData,
    success:function(data) {
      $(".buttonload").hide(); 
      $("#btnSaveFormData").show();   
      $("#btnApprove").prop("disabled", false);
       
      if(data.success){                   
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
    error: function (request, status, error){
      $(".buttonload").hide(); 
      $("#btnSaveFormData").show();   
      $("#btnApprove").prop("disabled", false);
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text(request.responseText);
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
  var FocusId=$("#FocusId").val();
  $("#"+FocusId).focus();
  $("#closePopup").click();
}

function highlighFocusBtn(pclass){
  $(".activeYes").hide();
  $(".activeNo").hide();  
  $("."+pclass+"").show();
}

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


function getPeriodLockType(id,value){
  var ROW_ID = id.split('_').pop();
    if(value=='Always Lock'){
    $('#PDCLFROM_DT_'+ROW_ID+'').prop('readonly', true);
    $('#PDCLTO_DT_'+ROW_ID+'').prop('readonly', true);
    $('#PDCLDAYS_'+ROW_ID+'').prop('readonly', true);
    }
    else if(value=='For the Period'){
    $('#PDCLFROM_DT_'+ROW_ID+'').prop('readonly', false);
    $('#PDCLTO_DT_'+ROW_ID+'').prop('readonly', false);
    $('#PDCLDAYS_'+ROW_ID+'').prop('readonly', true);
    }
    else if(value=='No of Days'){
    $('#PDCLFROM_DT_'+ROW_ID+'').prop('readonly', true);
    $('#PDCLTO_DT_'+ROW_ID+'').prop('readonly', true);
    $('#PDCLDAYS_'+ROW_ID+'').prop('readonly', false);
    }
  }


  function getPeriodLockTypes(id,value){
  var ROW_ID = id.split('_').pop();
    if(value=='Always Lock'){
    $('#PDCLFROMDT_'+ROW_ID+'').prop('readonly', true);
    $('#PDCLTODT_'+ROW_ID+'').prop('readonly', true);
    $('#PDCLDAYS_'+ROW_ID+'').prop('readonly', true);
    }
    else if(value=='For the Period'){
    $('#PDCLFROMDT_'+ROW_ID+'').prop('readonly', false);
    $('#PDCLTODT_'+ROW_ID+'').prop('readonly', false);
    $('#PDCLDAYS_'+ROW_ID+'').prop('readonly', true);
    }
    else if(value=='No of Days'){
    $('#PDCLFROMDT_'+ROW_ID+'').prop('readonly', true);
    $('#PDCLTODT_'+ROW_ID+'').prop('readonly', true);
    $('#PDCLDAYS_'+ROW_ID+'').prop('readonly', false);
    }
  }



  $(document).ready(function () {

    $('#example2').find('.participantRow').each(function(){
      var ids  = $.trim($('[id*=PDCLFROMDT]').val());
      var ROW_ID = ids.split('_').pop();

      if(value=='Always Lock'){
      $('#PDCLFROMDT_'+ROW_ID+'').prop('readonly', false);
      $('#PDCLTODT_'+ROW_ID+'').prop('readonly', false);
      $('#PDCLDAYS_'+ROW_ID+'').prop('readonly', false);
      }
      else if(value=='For the Period'){
      $('#PDCLFROMDT_'+ROW_ID+'').prop('readonly', false);
      $('#PDCLTODT_'+ROW_ID+'').prop('readonly', false);
      $('#PDCLDAYS_'+ROW_ID+'').prop('readonly', true);
      }
      else if(value=='No of Days'){
      $('#PDCLFROMDT_'+ROW_ID+'').prop('readonly', true);
      $('#PDCLTODT_'+ROW_ID+'').prop('readonly', true);
      $('#PDCLDAYS_'+ROW_ID+'').prop('readonly', false);
      }

});

   
  
    





  });

$(function() { 
  loadTab(); 
});

function loadTab(){
  var html='';
  var i=0; 
  html +='<tr  class="participantRow">';
  html +='<td><input type="text" name="PDCLNAME[]" id="PDCL_NAME_'+i+'" class="form-control mandatory"  autocomplete="off" readonly/></td>';
  html +='<td hidden><input type="hidden" name="PDCLNAME_REF[]" id="PDCLNAME_REF_'+i+'"  class="form-control mandatory"  autocomplete="off"/></td>';
  html +='<td hidden><input type="hidden" name="FORMNAMEID_REF[]" id="FORMNAMEID_REF_'+i+'" class="form-control" autocomplete="off" /></td>';
  html +='<td><select name="LTID_REF[]" id="LTID_REF_'+i+'" onchange="getPeriodLockType(this.id,this.value)" class="form-control mandatory" tabindex="4"><option value="NULL">Select</option><option value="Always Lock">Always Lock </option> <option value="No of Days">No of Days </option><option value="For the Period"> For the Period (From - To) </option></select></td>';
  html +='<td><input type="date" name="PDCLFROMDT[]" id="PDCLFROM_DT_'+i+'" class="form-control mandatory" autocomplete="off" placeholder="dd/mm/yyyy" readonly></td>';
  html +='<td><input type="date" name="PDCLTODT[]" id="PDCLTO_DT_'+i+'" class="form-control mandatory" autocomplete="off" placeholder="dd/mm/yyyy" readonly></td>';
  html +='<td><input type="text" name="PDCLDAYS[]" id="PDCLDAYS_'+i+'" class="form-control mandatory"  autocomplete="off" readonly/></td>';
  html +='</tr>';
  $("#material_body").html(html);
}


function onlyNumberKey(evt) {
      var ASCIICode = (evt.which) ? evt.which : evt.keyCode
      if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
          return false;
      return true;
  }

</script>
@endpush