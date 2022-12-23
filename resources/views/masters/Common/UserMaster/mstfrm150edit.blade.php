@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[150,'index'])}}" class="btn singlebt">User Master</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <button href="#" id="btnSelectedRows" class="btn topnavbt" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
                        <button id="btnSave"   class="btn topnavbt" tabindex="5"><i class="fa fa-save"></i> Save</button>
                        <button class="btn topnavbt" id="btnView"  disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt"  id="btnUndo" ><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled" ><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove"{{($objRights->APPROVAL1 || $objRights->APPROVAL2 || $objRights->APPROVAL3 || $objRights->APPROVAL4 || $objRights->APPROVAL5) == 1 ? '' : 'disabled'}}><i class="fa fa-lock"></i> Approved</button>
                        <a href="#" class="btn topnavbt"  disabled="disabled"><i class="fa fa-link" ></i> Attachment</a>
                        <button class="btn topnavbt" id="btnExit"><i class="fa fa-power-off"></i> Exit</button>
                </div><!--col-10-->

            </div><!--row-->
    </div><!--topnav-->	
   
    <div class="container-fluid purchase-order-view filter">     
         <form id="frm_mst_edit" method="POST" onsubmit="return validateForm()" class="needs-validation"  > 
          @CSRF
          {{isset($objResponse->USERID) ? method_field('PUT') : '' }}
          <div class="inner-form">
          
              
              <div class="row">
                  <div class="col-lg-2 pl"><p>User Code</p></div>
                  <div class="col-lg-2 pl">
                  
                    <label> {{$objResponse->UCODE}} </label>
                    <input type="hidden" name="USERID" id="USERID" value="{{ $objResponse->USERID }}" />
                    <input type="hidden" name="UCODE" id="UCODE" value="{{ $objResponse->UCODE }}" autocomplete="off"    />
                    <input type="hidden" name="user_approval_level" id="user_approval_level" value="{{ $user_approval_level }}"  />
                  
                </div>
                </div>

                <div class="row">
                
                  <div class="col-lg-2 pl"><p>User Description</p></div>
                  <div class="col-lg-5 pl">
                    <input type="text" name="DESCRIPTIONS" id="DESCRIPTIONS" class="form-control mandatory" value="{{ old('NAME',$objResponse->DESCRIPTIONS) }}" maxlength="200" tabindex="1"  />
                    <span class="text-danger" id="ERROR_DESCRIPTIONS"></span> 
                  </div>
                </div>


                <div class="row">
                  <div class="col-lg-2 pl"><p>User Password</p></div>
                  <div class="col-lg-2 pl">
                    <input type="password" name="PASSWORD" id="PASSWORD" class="form-control mandatory" value="{{ old('PASSWORD',$objResponse->PASSWORD) }}" maxlength="200" tabindex="3"  />
                    <span class="text-danger" id="ERROR_PASSWORD"></span> 
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-2 pl"><p>Confirm Password</p></div>
                  <div class="col-lg-2 pl">
                    <input type="password" name="CONFIRM_PASSWORD" id="CONFORM_PASSWORD" class="form-control mandatory" value="{{ old('CONFORM_PASSWORD',$objResponse->PASSWORD) }}"  maxlength="200" tabindex="4"  />
                    <span class="text-danger" id="ERROR_CONFORM_PASSWORD"></span> 
                  </div>
                </div>

              <div class="row">
                <div class="col-lg-2 pl"><p>De-Activated</p></div>
                <div class="col-lg-1 pl pr">
                <input type="checkbox"   name="DEACTIVATED"  id="deactive-checkbox_0" {{$objResponse->DEACTIVATED == 1 ? "checked" : ""}}
                 value='{{$objResponse->DEACTIVATED == 1 ? 1 : 0}}' tabindex="2"  >
                </div>
                
                <div class="col-lg-2 pl"><p>Date of De-Activated</p></div>
                <div class="col-lg-2 pl">
                  <input type="date" name="DODEACTIVATED" class="form-control" id="DODEACTIVATED" {{$objResponse->DEACTIVATED == 1 ? "" : "disabled"}} value="{{isset($objResponse->DODEACTIVATED) && $objResponse->DODEACTIVATED !="" && $objResponse->DODEACTIVATED !="1900-01-01" ? $objResponse->DODEACTIVATED:''}}" tabindex="3" placeholder="dd/mm/yyyy"  />
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
                          @if(!empty($objDataResponse))
                          <th width="5%">Action</th>
                          @endif   
                          </tr>
                        </thead>
                        <tbody>
                        @if(!empty($objDataResponse))
                        @php $n=1; $m=array(); @endphp
                        @foreach($objDataResponse as $key => $row)

                        @php 

                        if($row->DEACTIVATED ==1){
                          $m[]=1;
                        }

                        @endphp

                          <tr  class="participantRow">
                            <td>
                              <input type="text" name="POPUP_EMPID_{{$key}}" id="POPUP_EMPID_{{$key}}" class="form-control showEmp"  value="{{$row->EMPCODE}}" readonly  style="width:100%;"  />
                              </td>
                              <td hidden><input type="hidden" name={{"UDID_".$key}} id ={{"UDID_".$key}} value="{{$row->UDID}}" /></td>
                              <td hidden><input type="hidden" name={{"EMPID_REF_".$key}} id ={{"EMPID_REF_".$key}} value="{{$row->EMPID_REF}}" /></td>
                              <td><input  class="form-control w-100" type="text" name={{"EMP_NAME_".$key}} id ={{"EMP_NAME_".$key}} value="{{$row->FNAME}} {{$row->MNAME}} {{$row->LNAME}}" maxlength="200" readonly style="width:100%;" ></td>
                              <td><input  class="form-control w-100" type="date" name={{"STARTPD_".$key}} id ={{"STARTPD_".$key}} value="{{isset($row->STARTPD) && $row->STARTPD !="" && $row->STARTPD !="1900-01-01" ? $row->STARTPD:''}}"  autocomplete="off" style="width:100%;" ></td>
                              <td><input  class="form-control w-100" type="date" name={{"ENDPD_".$key}} id ={{"ENDPD_".$key}} value="{{isset($row->ENDPD) && $row->ENDPD !="" && $row->ENDPD !="1900-01-01" ? $row->ENDPD:''}}"   autocomplete="off" style="width:100%;"  ></td>
                              <td><input  class="" type="checkbox" name={{"EMPDEACTIVATED_".$key}} id ={{"EMPDEACTIVATED_".$key}}  value="1"  autocomplete="off" style="width:100%;"  {{$row->DEACTIVATED == 1 ? "checked" : ""}} ></td>
                              <td><input  class="form-control w-100" type="date" name={{"EMPDODEACTIVATED_".$key}} id ={{"EMPDODEACTIVATED_".$key}}  autocomplete="off" style="width:100%;" value="{{isset($row->DODEACTIVATED) && $row->DODEACTIVATED !="" && $row->DODEACTIVATED !="1900-01-01" ? $row->DODEACTIVATED:''}}" {{$row->DEACTIVATED == 1 ? "" : "disabled"}}  ></td>
                              <td><input  class="" type="checkbox" name={{"SUPPERUSER_".$key}} id ={{"SUPPERUSER_".$key}} value="1"  autocomplete="off" style="width:100%;" {{$row->SUPPERUSER == 1 ? "checked" : ""}}></td>

                              <td align="center" >
                                  <button class="btn add" title="add" data-toggle="tooltip" {{count($objDataResponse) == count($m)?'':'disabled'}} ><i class="fa fa-plus"></i></button>
                                  
                                  

                                 
                                  
                                  <!--
                                  <button class="btn remove" title="Delete" data-toggle="tooltip" {{isset($n) && $n ==1?'disabled':''}} disabled ><i class="fa fa-trash" ></i></button>
                                  -->
                              </td>
                          </tr>

                          @php $n++; @endphp
                          @endforeach 

                          @else

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
                              <td><input  class="" type="checkbox" name="SUPPERUSER_0" id ="SUPPERUSER_0" value="1"  autocomplete="off" style="width:100%;"  ></td>
                              
                              
                              <!--
                              <td align="center" >
                                  <button class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></button>
                                  <button class="btn remove" title="Delete" data-toggle="tooltip" disabled><i class="fa fa-trash" ></i></button>
                              </td>
                              -->
                          </tr>

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
  <div class="modal-dialog"  >
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
            <button onclick="setfocus();" class="btn alertbt" name='OkBtn' id="OkBtn" style="display:none;margin-left: 90px;">
              <div id="alert-active" class="activeOk"></div>OK</button>
        </div><!--btdiv-->
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Alert -->

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
            <td class="ROW1" style="width: 10%;" align="center"><span class="check_th">&#10004;</span></td>
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

function validateForm(ActionType){

    $("#focusid").val('');
    $("#errorid").val('');

    var POPUP_EMPID       =   $.trim($("[id=POPUP_EMPID_0]").val());
    var EMPID_REF           =   $.trim($("[id=EMPID_REF_0]").val());
    var STARTPD           =   $.trim($("[id=STARTPD_0]").val());
    var ENDPD             =   $.trim($("[id=ENDPD_0]").val());
    var EMPDEACTIVATED    =   $.trim($("[id=EMPDEACTIVATED_0]").val());
    var EMPDODEACTIVATED  =   $.trim($("[id=EMPDODEACTIVATED_0]").val());

    if(STARTPD ==="" && POPUP_EMPID !=""){
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

        var allblank11 = [];
        var focustext11= "";
        var textmsg = "";

      $('#example2').find('.participantRow').each(function(){
        if($('[id*="SUPPERUSER"]').is(":checked") != false){
          if($.trim($(this).find("[id*=EMPID_REF]").val()) ==""){
            allblank11.push('false');
            focustext11 = $(this).find("[id*=POPUP_EMPID]").attr('id');
            textmsg = 'Please enter Employee Code';
            }
          }
        });
   
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
          else if(jQuery.inArray("false", allblank11) !== -1){
            $("#focusid").val(focustext11);
            $("#errorid").val('1');
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn").show();
            $("#AlertMessage").text(textmsg);
            $("#alert").modal('show');
            $("#OkBtn").focus();
            highlighFocusBtn('activeOk');
            return false;
          } 
          else{
              $("#errorid").val('');
              $("#alert").modal('show');
              $("#AlertMessage").text('Do you want to save to record.');
              $("#YesBtn").data("funcname",ActionType);  
              $("#YesBtn").focus();
              highlighFocusBtn('activeYes');
          }

    }
  
}

$(document).ready(function(e) {

    var rcount = <?php echo json_encode($objCount); ?>;

    $('#Row_Count').val(rcount);

    $("#example2").on('click', '.add', function() {
        var $tr = $(this).closest('tbody');
        var allTrs = $tr.find('.participantRow').last();
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
            if(id) {
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

        $('.add').prop('disabled', true);
       

        $clone.find('[id*="EMPID_REF"]').val('');
        $clone.find('[id*="STARTPD"]').val('');
        $clone.find('[id*="EMPDEACTIVATED"]').prop("checked", false);
        $clone.find('[id*="ENDPD"]').val('');
        $clone.find('[id*="EMPDODEACTIVATED"]').val('');
        event.preventDefault();

    });

    $("#example2").on('click', '.remove', function() {

        var rowCount = $('#Row_Count').val();
        if (rowCount > 1) {
            $(this).closest('.participantRow').remove(); 
        } 
        if (rowCount <= 1) { 
            $(document).find('.remove').prop('disabled', true);  
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

 var formDataMst = $( "#frm_mst_edit" );
     formDataMst.validate();

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

    $( "#DODEACTIVATED" ).rules( "add", {
        required: true,
        DateValidate:true,
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field"
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
      var validator =$("#frm_mst_edit" ).validate();
          validator.element( "#"+element_id+"" );
    }

    //validate
    $( "#btnSave" ).click(function() {

        if(formDataMst.valid()){
           
            validateForm('fnSaveData');

        }

    });//btnSave

    
    //validate and approve
    $("#btnApprove").click(function() {
        
        if(formDataMst.valid()){
           
            validateForm('fnApproveData');

        }

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
            url:'{{route("mastermodify",[150,"update"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    if(data.errors.DESCRIPTIONS){
                        showError('ERROR_DESCRIPTIONS',data.errors.DESCRIPTIONS);
                    }
                    if(data.errors.PASSWORD){
                        showError('ERROR_PASSWORD',data.errors.PASSWORD);
                    }
                   if(data.exist=='norecord') {
                      $("#errorid").val('1');
                      $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn").show();

                      $("#AlertMessage").text(data.msg);

                      $("#alert").modal('show');
                      $("#OkBtn").focus();

                   }
                   if(data.save=='invalid') {
                      $("#errorid").val('1');
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

                }
                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });

    };// fnSaveData


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
            url:'{{route("mastermodify",[150,"singleapprove"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    if(data.errors.NAME){
                        showError('ERROR_DESCRIPTIONS',data.errors.NAME);
                    }
                   if(data.exist=='norecord') {
                      $("#errorid").val('1');
                      $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn").show();

                      $("#AlertMessage").text(data.msg);

                      $("#alert").modal('show');
                      $("#OkBtn").focus();

                   }
                   if(data.save=='invalid') {
                    $("#errorid").val('1');
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

                }
                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });

    };// fnApproveData

    //no button
    $("#NoBtn").click(function(){

      $("#alert").modal('hide');
      var custFnName = $("#NoBtn").data("funcname");
          window[custFnName]();

    }); //no button

   
    $("#OkBtn").click(function(){

        $("#alert").modal('hide');

        $("#YesBtn").show();
        $("#NoBtn").show();
        $("#OkBtn").hide();

        $(".text-danger").hide();

        if($("#errorid").val() ===""){
            window.location.href = '{{route("master",[150,"index"]) }}';
        }

        //window.location.href = '{{route("master",[150,"index"]) }}';

    }); ///ok button

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
      window.location.reload();

   }//fnUndoYes


   window.fnUndoNo = function (){

      $("#UCODE").focus();

   }//fnUndoNo


    //
    function showError(pId,pVal){

      $("#"+pId+"").text(pVal);
      $("#"+pId+"").show();

    }  

    function highlighFocusBtn(pclass){
       $(".activeYes").hide();
       $(".activeNo").hide();
       
       $("."+pclass+"").show();
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
  //$("#NAME").focus(); 
});

</script>


@endpush