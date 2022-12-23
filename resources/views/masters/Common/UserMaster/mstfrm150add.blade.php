@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[150,'index'])}}" class="btn singlebt">User Master</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                <button class="btn topnavbt" id="btnAdd" disabled="disabled" ><i class="fa fa-plus"></i> Add</button>
                <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
                <button class="btn topnavbt" id="btnSave"  tabindex="5"  ><i class="fa fa-save"></i> Save</button>
                <button class="btn topnavbt" id="btnView" disabled="disabled" ><i class="fa fa-eye"></i> View</button>
                <button class="btn topnavbt" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                <button class="btn topnavbt" id='btnUndo' ><i class="fa fa-undo"></i> Undo</button>
                <button class="btn topnavbt" id="btnCancel"  disabled="disabled" ><i class="fa fa-times"></i> Cancel</button>
                <button class="btn topnavbt" id="btnApprove"  disabled="disabled" ><i class="fa fa-lock"></i> Approved</button>
                <button class="btn topnavbt"  id="btnAttach"  disabled="disabled" ><i class="fa fa-link"></i> Attachment</button>
                <button class="btn topnavbt" id="btnExit"><i class="fa fa-power-off"></i> Exit</button>

              </div><!--col-10-->

            </div><!--row-->
    </div><!--topnav-->	
   
    <div class="container-fluid purchase-order-view filter">     
         <form id="frm_mst_add" method="POST" onsubmit="return validateForm()" class="needs-validation"  > 
         
          @CSRF
          <div class="inner-form">
              
                <div class="row">
                  <div class="col-lg-2 pl"><p>User Code</p></div>
                  <div class="col-lg-2 pl">
                    <div class="col-lg-11 pl">
                      @if(!empty($objDD))
                          @if($objDD->SYSTEM_GRSR == "1")
                              <input type="text" name="UCODE" id="UCODE" value="{{ $objDOCNO }}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase" autofocus >
                          @endif
                          @if($objDD->MANUAL_SR == "1")
                              <input type="text" name="UCODE" id="UCODE" value="{{ old('UCODE') }}" class="form-control mandatory" minlength="3" maxlength="{{$objDD->MANUAL_MAXLENGTH}}" tabindex="1" autocomplete="off" style="text-transform:uppercase" autofocus >
                          @endif
                      @endif   
                        {{-- <input type="text" name="UCODE" id="UCODE" value="{{ old('UCODE') }}" class="form-control mandatory" autocomplete="off" maxlength="30" tabindex="1" style="text-transform:uppercase" onkeypress="return AlphaNumaric(event,this)" /> --}}
                        <span class="text-danger" id="ERROR_UCODE"></span> 
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-2 pl"><p>User Description</p></div>
                  <div class="col-lg-5 pl">
                    <input type="text" name="DESCRIPTIONS" id="DESCRIPTIONS" class="form-control mandatory" value="{{ old('DESCRIPTIONS') }}" maxlength="200" tabindex="2"  />
                    <span class="text-danger" id="ERROR_DESCRIPTIONS"></span> 
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-2 pl"><p>User Password</p></div>
                  <div class="col-lg-2 pl">
                    <input type="password" name="PASSWORD" id="PASSWORD" class="form-control mandatory" value="{{ old('PASSWORD') }}" maxlength="200" tabindex="3"  />
                    <span class="text-danger" id="ERROR_PASSWORD"></span> 
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-2 pl"><p>Confirm Password</p></div>
                  <div class="col-lg-2 pl">
                    <input type="password" name="CONFIRM_PASSWORD" id="CONFORM_PASSWORD" class="form-control mandatory"  maxlength="200" tabindex="4"  />
                    <span class="text-danger" id="ERROR_CONFORM_PASSWORD"></span> 
                  </div>
                </div>


                

                <div class="row">
                  <div class="col-lg-10 pl">
                    <div class=" table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:300px;" >
                      <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                        <thead id="thead1"  style="position: sticky;top: 0">
                          <tr>
                          <th>
                              Employee Code
                              <input class="form-control" type="hidden" name="Row_Count" id ="Row_Count"> 
                              <input type="hidden" id="focusid" >
                              <input type="hidden" id="errorid" >
                          </th>
                          <th>Employee Name</th>
                          <th>Start Period</th>
                          <th>End Period</th>
                          <th>De-Activated</th>
                          <th>Date of De-Activated</th>
                          <th>Supper User</th>
                          <!--
                          <th width="5%">Action</th>
                          -->
                          </tr>
                        </thead>
                        <tbody>
                          <tr  class="participantRow">
                              <td>
                              <input type="text" name="POPUP_EMPID_0" id="POPUP_EMPID_0" class="form-control showEmp" readonly  style="width:100%;"  />
                              </td>
                              <td hidden><input type="hidden" name="EMPID_REF_0" id="EMPID_REF_0" /></td>
                              <td><input  class="form-control w-100" type="text"  id ="EMP_NAME_0" maxlength="200" readonly style="width:100%;" ></td>
                              <td><input  class="form-control w-100" type="date" name="STARTPD_0" id ="STARTPD_0" autocomplete="off" style="width:100%;" ></td>
                              <td><input  class="form-control w-100" type="date" name="ENDPD_0" id ="ENDPD_0"  autocomplete="off" style="width:100%;"  ></td>
                              <td><input  class="" type="checkbox" name="EMPDEACTIVATED_0" id ="EMPDEACTIVATED_0" value="1"  autocomplete="off" style="width:100%;" disabled ></td>
                              <td><input  class="form-control w-100" type="date" name="EMPDODEACTIVATED_0" id ="EMPDODEACTIVATED_0"  autocomplete="off" style="width:100%;" disabled  ></td>
                              <td><input  class="" type="checkbox" name="SUPPERUSER_0" id ="SUPPERUSER_0" value="1"  autocomplete="off" style="width:100%;" ></td>
                              
                              <!--
                              <td align="center" >
                                  <button class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></button>
                                  <button class="btn remove" title="Delete" data-toggle="tooltip" disabled><i class="fa fa-trash" ></i></button>
                              </td>
                              -->
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
            <button onclick="setfocus();"  class="btn alertbt" name='OkBtn1' id="OkBtn1" style="display:none;margin-left: 90px;">
            <div id="alert-active" class="activeOk1"></div>OK</button>
            
        </div><!--btdiv-->
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<div id="emp_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='emp_popup_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Employee</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="emp_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th class="ROW1" style="width: 10%">Select</th> 
            <th class="ROW2" style="width: 40%">
            Code 
            <input type="hidden" id="field1" > 
            <input type="hidden" id="field2" >
            <input type="hidden" id="field3" >
            
            </th>
            <th class="ROW3"style="width: 40%">Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
          <td class="ROW2"  style="width: 40%"><input type="text" id="emp_codesearch"  class="form-control" autocomplete="off"  onkeyup="searchEmpCode()"></td>
          <td class="ROW3"  style="width: 40%"><input type="text" id="emp_namesearch"  class="form-control" autocomplete="off"  onkeyup="searchEmpName()"></td>
        </tr>
        </tbody>
      </table>
  
      <table id="emp_tab2" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody>
        @foreach ($objEmployee as $key=>$val)

        <tr >
          <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_EMPID_REF[]" id="emp_{{ $val->EMPID }}" class="cls_emp" value="{{$val->EMPID }}" ></td>
          <td  class="ROW2" style="width: 39%">{{ $val->EMPCODE }}
          <input type="hidden" id="txtemp_{{ $val->EMPID }}" data-desc="{{ $val->EMPCODE }}" data-emp="{{ $val->FNAME }} {{ $val->MNAME }} {{ $val->LNAME }}" value="{{ $val-> EMPID }}"/>
          </td>
          <td class="ROW3" style="width: 39%">{{ $val->FNAME }}</td>
        </tr>
        @endforeach
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


$('#example2').on('click','[id*="POPUP_EMPID"]',function(event){
    $('#field1').val($(this).attr('id'));
    $('#field2').val($(this).parent().parent().find('[id*="EMPID_REF"]').attr('id'));
    $('#field3').val($(this).parent().parent().find('[id*="EMP_NAME"]').attr('id'));

    var field1   =   $("#field1").val();
    var field2   =   $("#field2").val();
    var field3   =   $("#field3").val();

    $("#emp_popup").show();
    event.preventDefault();
});

$('#example2').on('change','[id*="EMPDEACTIVATED"]',function(event){
  if ($(this).prop("checked")) {
		  $(this).val('1');
      $(this).parent().parent().find('[id*="EMPDODEACTIVATED"]').removeAttr('disabled');
		}
		else {
		  $(this).val('0');
      $(this).parent().parent().find('[id*="EMPDODEACTIVATED"]').prop('disabled', true);
      $(this).parent().parent().find('[id*="EMPDODEACTIVATED"]').val('');		  
		}
    event.preventDefault();
});

$("#emp_popup_close").on("click",function(event){ 
  $("#emp_popup").hide();
  event.preventDefault();
});

$('.cls_emp').click(function(){
    var id = $(this).attr('id');
    var txtval =    $("#txt"+id+"").val();
    var texdesc =   $("#txt"+id+"").data("desc")
    var texemp =   $("#txt"+id+"").data("emp")

    var field1   =   $("#field1").val();
    var field2   =   $("#field2").val();
    var field3   =   $("#field3").val();

    $("#"+field1).val(texdesc);
    $("#"+field2).val(txtval);
    $("#"+field3).val(texemp);
    $("#"+field1).blur(); 
    $("#emp_popup").hide();

    $("#emp_codesearch").val(''); 
    $("#emp_namesearch").val(''); 
    searchEmpCode();
    event.preventDefault();

});

function searchEmpCode() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("emp_codesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("emp_tab2");
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

function searchEmpName() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("emp_namesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("emp_tab2");
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

  
  let emp_tab1 = "#emp_tab1";
  let emp_tab2 = "#emp_tab2";
  let emp_headers = document.querySelectorAll(emp_tab1 + " th");

  emp_headers.forEach(function(element, i) {
    element.addEventListener("click", function() {
      w3.sortHTML(emp_tab2, ".cls_emp", "td:nth-child(" + (i + 1) + ")");
    });
  });


function setfocus(){
    var focusid=$("#focusid").val();
    $("#"+focusid).focus();
    $("#closePopup").click();
} 

function validateLessDate(value){
  var today = new Date(); 
  var d = new Date(value); 
  today.setHours(0, 0, 0, 0) ;
  d.setHours(0, 0, 0, 0) ;

  if(d < today){
      return false;
  }
  else {
      return true;
  }
}

function validateForm(){

    $("#focusid").val('');
    $("#errorid").val('');

    var POPUP_EMPID       =   $.trim($("[id=POPUP_EMPID_0]").val());
    var EMPID_REF           =   $.trim($("[id=EMPID_REF_0]").val());
    var STARTPD           =   $.trim($("[id=STARTPD_0]").val());
    var ENDPD             =   $.trim($("[id=ENDPD_0]").val());
    var EMPDEACTIVATED    =   $.trim($("[id=EMPDEACTIVATED_0]").val());
    var EMPDODEACTIVATED  =   $.trim($("[id=EMPDODEACTIVATED_0]").val());

    $("#OkBtn1").hide();

    var UCODE          =   $.trim($("#UCODE").val());
    if(UCODE ===""){
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").show();              
      $("#AlertMessage").text('Please enter User Code.');
      $("#alert").modal('show');
      $("#OkBtn").focus();
      return false;

    }else if(STARTPD ==="" && POPUP_EMPID !=""){
        $("#focusid").val('STARTPD_0');
        $("#errorid").val('1');
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").show();
        $("#AlertMessage").text('Please select start period.');
        $("#alert").modal('show');
        $("#OkBtn").focus();
        highlighFocusBtn('activeOk');
        return false;
    }
    else if(POPUP_EMPID ==="" && STARTPD !=""){
        $("#focusid").val('POPUP_EMPID_0');
        $("#errorid").val('1');
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").show();
        $("#AlertMessage").text('Please select employee.');
        $("#alert").modal('show');
        $("#OkBtn").focus();
        highlighFocusBtn('activeOk');
        return false;
    }
    else if(POPUP_EMPID ==="" && ENDPD !=""){
        $("#focusid").val('POPUP_EMPID_0');
        $("#errorid").val('1');
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").show();
        $("#AlertMessage").text('Please select employee.');
        $("#alert").modal('show');
        $("#OkBtn").focus();
        highlighFocusBtn('activeOk');
        return false;
    }
    else if($("[id=EMPDEACTIVATED_0]").is(":checked") != false && POPUP_EMPID ==""){
        $("#focusid").val('POPUP_EMPID_0');
        $("#errorid").val('1');
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").show();
        $("#AlertMessage").text('Please select employee.');
        $("#alert").modal('show');
        $("#OkBtn").focus();
        highlighFocusBtn('activeOk');
        return false;
    }
    else if($("[id=EMPDEACTIVATED_0]").is(":checked") != false && EMPDODEACTIVATED ==""){
        $("#focusid").val('EMPDODEACTIVATED_0');
        $("#errorid").val('1');
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").show();
        $("#AlertMessage").text('Please select de-activated date.');
        $("#alert").modal('show');
        $("#OkBtn").focus();
        highlighFocusBtn('activeOk');
        return false;
    }
    else if($("[id=EMPDEACTIVATED_0]").is(":checked") != false && validateLessDate(EMPDODEACTIVATED)==false){
        $("#focusid").val('EMPDODEACTIVATED_0');
        $("#errorid").val('1');
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").show();
        $("#AlertMessage").text('Less de-activated date not allow.');
        $("#alert").modal('show');
        $("#OkBtn").focus();
        highlighFocusBtn('activeOk');
        return false;
    }
    else if($("[id=SUPPERUSER_0]").is(":checked") != false && EMPID_REF ==""){
        $("#focusid").val('POPUP_EMPID_0');
        $("#errorid").val('1');
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").show();
        $("#AlertMessage").text('Please Select Employee Code.');
        $("#alert").modal('show');
        $("#OkBtn").focus();
        highlighFocusBtn('activeOk');
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
        var allblank7 = []; 
        var texid1    = "";
        var texid2    = "";
        var texid3    = "";
        var texid4    = "";
        var texid5    = "";
        var texid6    = "";
        var texid7    = "";
   
        $("[id*=POPUP_EMPID]").each(function(){

          if($.trim($(this).parent().parent().find('[id*="STARTPD"]').val()) ==="" && $.trim($(this).val()) !=""){
            allblank1.push('true');
            texid1 = $(this).parent().parent().find('[id*="STARTPD"]').attr('id');
          }
          else if($.trim($(this).parent().parent().find('[id*="STARTPD"]').val()) !="" && $.trim($(this).val()) ===""){
            allblank2.push('true');
            texid2 = $(this).attr('id');
          }
          else if($.trim($(this).parent().parent().find('[id*="ENDPD_0"]').val()) !="" && $.trim($(this).val()) ===""){
            allblank3.push('true');
            texid3 = $(this).attr('id');
          }
          else if($(this).parent().parent().find('[id*="EMPDEACTIVATED"]').is(":checked") != false && $.trim($(this).val()) ===""){
            allblank4.push('true');
            texid4 = $(this).attr('id');
          }
          else if($(this).parent().parent().find('[id*="EMPDEACTIVATED"]').is(":checked") != false && $.trim($(this).parent().parent().find('[id*="EMPDODEACTIVATED"]').val()) ===""){
            allblank5.push('true');
            texid5 = $(this).parent().parent().find('[id*="EMPDODEACTIVATED"]').attr('id');
          }
          else if($(this).parent().parent().find('[id*="EMPDEACTIVATED"]').is(":checked") != false && validateLessDate($.trim($(this).parent().parent().find('[id*="EMPDODEACTIVATED"]').val()))==false){
            allblank6.push('true');
            texid6 = $(this).parent().parent().find('[id*="EMPDODEACTIVATED"]').attr('id');
          }
          else if (RackArray.indexOf($.trim($(this).val())) > -1) {
              allblank7.push('true');
              texid7 = $(this).attr('id');
          }
          else{
              allblank1.push('false');
              allblank2.push('false');
              allblank3.push('false');
              allblank4.push('false');
              allblank5.push('false');
              allblank6.push('false');
              allblank7.push('false');
          }

          RackArray.push($.trim($(this).val()));
           
        });

        
        if(jQuery.inArray("true", allblank1) !== -1){
            $("#focusid").val(texid1);
            $("#errorid").val('1');
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn").show();
            $("#AlertMessage").text('Please select start period.');
            $("#alert").modal('show');
            $("#OkBtn").focus();
            highlighFocusBtn('activeOk');
            return false;
          }
          else if(jQuery.inArray("true", allblank2) !== -1){
            $("#focusid").val(texid2);
            $("#errorid").val('1');
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn").show();
            $("#AlertMessage").text('Please select employee.');
            $("#alert").modal('show');
            $("#OkBtn").focus();
            highlighFocusBtn('activeOk');
            return false;
          }
          else if(jQuery.inArray("true", allblank3) !== -1){
            $("#focusid").val(texid3);
            $("#errorid").val('1');
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn").show();
            $("#AlertMessage").text('Please select employee.');
            $("#alert").modal('show');
            $("#OkBtn").focus();
            highlighFocusBtn('activeOk');
            return false;
          }
          else if(jQuery.inArray("true", allblank4) !== -1){
            $("#focusid").val(texid4);
            $("#errorid").val('1');
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn").show();
            $("#AlertMessage").text('Please select employee.');
            $("#alert").modal('show');
            $("#OkBtn").focus();
            highlighFocusBtn('activeOk');
            return false;
          }
          else if(jQuery.inArray("true", allblank5) !== -1){
            $("#focusid").val(texid5);
            $("#errorid").val('1');
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn").show();
            $("#AlertMessage").text('Please select de-activated date.');
            $("#alert").modal('show');
            $("#OkBtn").focus();
            highlighFocusBtn('activeOk');
            return false;
          }
          else if(jQuery.inArray("true", allblank6) !== -1){
            $("#focusid").val(texid6);
            $("#errorid").val('1');
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn").show();
            $("#AlertMessage").text('Less de-activated date not allow.');
            $("#alert").modal('show');
            $("#OkBtn").focus();
            highlighFocusBtn('activeOk');
            return false;
          }
          else if(jQuery.inArray("true", allblank7) !== -1){
            $("#focusid").val(texid7);
            $("#errorid").val('1');
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn").show();
            $("#AlertMessage").text('Employee allready allocated to other user.');
            $("#alert").modal('show');
            $("#OkBtn").focus();
            highlighFocusBtn('activeOk');
            return false;
          }
          else{
              $("#errorid").val('');
              $("#alert").modal('show');
              $("#AlertMessage").text('Do you want to save to record.');
              $("#YesBtn").data("funcname","fnSaveData");  
              $("#YesBtn").focus();
              highlighFocusBtn('activeYes');
          }

    }
  
}

$(document).ready(function(e) {

    $('#Row_Count').val("1");
  
    $("#example2").on('click', '.add', function() {
        var $tr = $(this).closest('table');
        var allTrs = $tr.find('tbody').last();
        var lastTr = allTrs[allTrs.length-1];
        var $clone = $(lastTr).clone();

        $clone.find('td').each(function(){
            var el = $(this).find(':first-child');
            var id = el.attr('id') || null;
            if(id) {
                var i = id.substr(id.length-1);
                var prefix = id.substr(0, (id.length-1));
                el.attr('id', prefix+(+i+1));
            }
            var name = el.attr('name') || null;
            if(name) {
                var i = name.substr(name.length-1);
                var prefix1 = name.substr(0, (name.length-1));
                el.attr('name', prefix1+(+i+1));
            }
        });

        $clone.find('input:text').val('');
        $tr.closest('table').append($clone);         
        var rowCount = $('#Row_Count').val();
		    rowCount = parseInt(rowCount)+1;
        $('#Row_Count').val(rowCount);
        $clone.find('.remove').removeAttr('disabled'); 
        $clone.find('[id*="STARTPD"]').val('');
        $clone.find('[id*="EMPDEACTIVATED"]').prop("checked", false);
        $clone.find('[id*="ENDPD"]').val('');
        $clone.find('[id*="EMPDODEACTIVATED"]').val('');
        event.preventDefault();

    });

    $("#example2").on('click', '.remove', function() {

        var rowCount = $('#Row_Count').val();

        if (rowCount > 1) {
            $(this).closest('tbody').remove();     
        } 
        
        if (rowCount <= 1) { 
            $(document).find('.remove').prop('disabled', false);  
        }
        event.preventDefault();
    });    

});


  $('#btnAdd').on('click', function() {
      var viewURL = '{{route("master",[150,"add"])}}';
      window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });

 var formResponseMst = $( "#frm_mst_add" );
     formResponseMst.validate();

    $("#UCODE").blur(function(){
      $(this).val($.trim( $(this).val() ));
      $("#ERROR_UCODE").hide();
      validateSingleElemnet("UCODE");
         
    });

    $( "#UCODE" ).rules( "add", {
        required: true,
        nowhitespace: true,
        //StringNumberRegex: true, //from custom.js
        messages: {
            required: "Required field.",
        }
    });

    $("#DESCRIPTIONS").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_DESCRIPTIONS").hide();
        validateSingleElemnet("DESCRIPTIONS");
    });

    $("#DESCRIPTIONS").keydown(function(){
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

    $("#PASSWORD").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_PASSWORD").hide();
        validateSingleElemnet("PASSWORD");
    });

    $("#PASSWORD").keydown(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_PASSWORD").hide();
        validateSingleElemnet("PASSWORD");
    });

    $( "#PASSWORD" ).rules( "add", {
        required: true,
        nowhitespace: true,
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field."
        }
    });

    $("#CONFORM_PASSWORD").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_CONFORM_PASSWORD").hide();
        validateSingleElemnet("CONFORM_PASSWORD");
    });

    $("#CONFORM_PASSWORD").keydown(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_CONFORM_PASSWORD").hide();
        validateSingleElemnet("CONFORM_PASSWORD");
    });

    $( "#CONFORM_PASSWORD" ).rules( "add", {
        required: true,
        nowhitespace: true,
        MatchPassword: true,
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field."
        }
    });


    $.validator.addMethod("MatchPassword", function(value, element) {
      if(this.optional(element) || $.trim(value) != $.trim($("#PASSWORD").val())){
          return false;
      }
      else {
          return true;
      }
      }, "Password Mismatch");

    //validae single element
    function validateSingleElemnet(element_id){
      var validator =$("#frm_mst_add" ).validate();
         if(validator.element( "#"+element_id+"" )){
            //check duplicate code
          if(element_id=="UCODE" || element_id=="ucode" ) {
            checkDuplicateCode();
          }

         }

         

    }

    // //check duplicate exist code
    function checkDuplicateCode(){
        
        //validate and save data
        var getDataForm = $("#frm_mst_add");
        var formData = getDataForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("master",[150,"codeduplicate"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
                if(data.exists) {
                    $(".text-danger").hide();
                    showError('ERROR_UCODE',data.msg);
                    $("#UCODE").focus();
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

        var getDataForm = $("#frm_mst_add");
        var formData = getDataForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("master",[150,"save"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    if(data.errors.UCODE){
                        showError('ERROR_UCODE',data.errors.UCODE);
                    }
                    if(data.errors.DESCRIPTIONS){
                        showError('ERROR_DESCRIPTIONS',data.errors.DESCRIPTIONS);
                    }
                    if(data.errors.PASSWORD){
                        showError('ERROR_PASSWORD',data.errors.PASSWORD);
                    }
                   if(data.exist=='duplicate') {
                      $("#errorid").val('1');
                      $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn1").hide();
                      $("#OkBtn").show();

                      $("#AlertMessage").text(data.msg);

                      $("#alert").modal('show');
                      $("#OkBtn").focus();

                   }
                   if(data.save=='invalid') {
                      $("#errorid").val('1');
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
                    $("#OkBtn").hide();
                    $("#OkBtn1").show();

                    $("#AlertMessage").text(data.msg);

                    $(".text-danger").hide();
                    $("#frm_mst_add").trigger("reset");

                    $("#alert").modal('show');
                    $("#OkBtn").focus();

                  //  window.location.href='{{ route("master",[150,"index"])}}';
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

        if($("#errorid").val() ===""){
          $("#EMPID_REF_0").val('');
          //window.location.href = '{{route("master",[150,"add"]) }}';
        }

        $(".text-danger").hide(); 
    }); ///ok button

        
    $("#OkBtn1").click(function(){

      $("#alert").modal('hide');
      $("#YesBtn").show();  //reset
      $("#NoBtn").show();   //reset
      $("#OkBtn").hide();
      $("#OkBtn1").hide();
      $(".text-danger").hide();
      window.location.href = "{{route('master',[150,'index'])}}";

    }); 
    
    $("#btnUndo").click(function(){

        $("#AlertMessage").text("Do you want to erase entered information in this record?");
        $("#alert").modal('show');

        $("#YesBtn").data("funcname","fnUndoYes");
        $("#YesBtn").show();

        $("#NoBtn").data("funcname","fnUndoNo");
        $("#NoBtn").show();
        
        $("#OkBtn").hide();
        $("#NoBtn").focus();
        highlighFocusBtn('activeNo');
        
    }); ////Undo button


    
    $("#OkBtn").click(function(){
      $("#alert").modal('hide');

    });////ok button


   window.fnUndoYes = function (){
      
      //reload form
      window.location.href = "{{route('master',[150,'add'])}}";

   }//fnUndoYes


   window.fnUndoNo = function (){
      $("#UCODE").focus();
   }//fnUndoNo


    function showError(pId,pVal){

      $("#"+pId+"").text(pVal);
      $("#"+pId+"").show();

    }//showError

    function highlighFocusBtn(pclass){
       $(".activeYes").hide();
       $(".activeNo").hide();
       
       $("."+pclass+"").show();
    }

    $(function() { 
      $("#UCODE").focus();  
    });

    window.onload = function(){
      var strdd = <?php echo json_encode($objDD); ?>;
      if($.trim(strdd)==""){     
        $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn").show();
          $("#AlertMessage").text('Please contact to administrator for creating document numbering.');
          $("#alert").modal('show');
          $("#OkBtn").focus();
          highlighFocusBtn('activeOk');
      } 
    };


$('#SUPPERUSER_0').on('click', function() {
    if($("[id=SUPPERUSER_0]").is(":checked") != true){
      $("#POPUP_EMPID_0").val('');
      $("#EMPID_REF_0").val('');
      $("#EMP_NAME_0").val('');      
      $("#STARTPD_0").val('');
      $("#ENDPD_0").val('');      
    }
});

    
</script>

@endpush