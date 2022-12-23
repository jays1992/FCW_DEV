@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
  <div class="row">
    <div class="col-lg-2"><a href="{{route('master',[$FormId,'index'])}}" class="btn singlebt">Discount Master</a></div>
      <div class="col-lg-10 topnav-pd">
        <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
        <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
        <button class="btn topnavbt" id="btnSaveFormData" onclick="saveAction('update')" ><i class="fa fa-floppy-o"></i> Save</button>
        <button style="display:none" class="btn topnavbt buttonload"> <i class="fa fa-refresh fa-spin"></i> {{Session::get('save')}}</button>
        <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
        <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
        <button class="btn topnavbt" id="btnUndo"  ><i class="fa fa-undo"></i> Undo</button>
        <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
        <button style="display:none" class="btn topnavbt buttonload_approve" > <i class="fa fa-refresh fa-spin"></i> {{Session::get('approve')}}</button>
        <button class="btn topnavbt" id="btnApprove" onclick="saveAction('approve')" {{ (isset($objRights->APPROVAL1) || isset($objRights->APPROVAL2) || isset($objRights->APPROVAL3) || isset($objRights->APPROVAL4) || isset($objRights->APPROVAL5)) &&  ($objRights->APPROVAL1||$objRights->APPROVAL2||$objRights->APPROVAL3||$objRights->APPROVAL4||$objRights->APPROVAL5) == 1 ? '' : 'disabled'}} ><i class="fa fa-thumbs-o-up"></i> Approved</button>
        <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
        <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
    </div>
  </div>
</div>
 
<form id="master_form" method="POST"  >
  <div class="container-fluid purchase-order-view">    
    @csrf
    <div class="container-fluid filter">
      <div class="inner-form">         
        <div class="row">
          <div class="col-lg-2 pl"><p>Promo Code</p></div>
          <div class="col-lg-2 pl">
            <input {{$ActionStatus}} type="hidden"  name="DISID"     id="DISID"    value="{{isset($HDR->DISID)?$HDR->DISID:''}}"      class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase"  >
            <input {{$ActionStatus}} type="text"    name="DISCODE"  id="DISCODE" value="{{isset($HDR->DISCODE)?$HDR->DISCODE:''}}"  class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase"  >
          </div>

          <div class="col-lg-2 pl"><p>Document Date*</p></div>
          <div class="col-lg-2 pl">
            <input {{$ActionStatus}} type="date" name="DOC_DATE" id="DOC_DATE" value="{{isset($HDR->DOC_DATE)?$HDR->DOC_DATE:''}}"  class="form-control" autocomplete="off" placeholder="dd/mm/yyyy" >
          </div>

          <div class="col-lg-2 pl"><p>Description</p></div>
          <div class="col-lg-2 pl">
            <input {{$ActionStatus}} type="text" name="DESCRIPTION" id="DESCRIPTION" value="{{isset($HDR->DESCRIPTION)?$HDR->DESCRIPTION:''}}" class="form-control" autocomplete="off" >                                                       
          </div>
            
        </div>

        <div class="row">
          <div class="col-lg-2 pl"><p>Discount Option*</p></div>
          <div class="col-lg-2 pl">
            <select {{$ActionStatus}} name="DIS_OPT" id="DIS_OPT" class="form-control" autocomplete="off" >
              <option {{isset($HDR->DIS_PERCENT) && $HDR->DIS_PERCENT =='Percentatge'?'selected="selected"':''}} value="Percentatge">Percentatge</option>
              <option {{isset($HDR->DIS_PERCENT) && $HDR->DIS_PERCENT =='Amount'?'selected="selected"':''}} value="Amount">Amount</option>
            </select>
          </div>

          <div class="col-lg-2 pl"><p>Discount (%)</p></div>
          <div class="col-lg-2 pl">
            <input {{$ActionStatus}} type="text" name="DIS_PERCENT" id="DIS_PERCENT" value="{{isset($HDR->DIS_PERCENT)?$HDR->DIS_PERCENT:''}}"  class="form-control" autocomplete="off" onkeypress="return isNumberDecimalKey(event,this)" >                                                       
          </div>

          <div class="col-lg-2 pl"><p>Discount (Amount)</p></div>
          <div class="col-lg-2 pl">
            <input {{$ActionStatus}} type="text" name="DIS_AMT" id="DIS_AMT" value="{{isset($HDR->DIS_AMT)?$HDR->DIS_AMT:''}}" class="form-control" autocomplete="off" onkeypress="return isNumberDecimalKey(event,this)" >                                                       
          </div>
        </div>

        <div class="row">
          <div class="col-lg-2 pl"><p>Valid sales invoice amount*</p></div>
          <div class="col-lg-2 pl">
            <input {{$ActionStatus}} type="text" name="VALID_SALES_INVOICE_AMT" id="VALID_SALES_INVOICE_AMT" value="{{isset($HDR->VALID_SALES_INVOICE_AMT)?$HDR->VALID_SALES_INVOICE_AMT:''}}" class="form-control" autocomplete="off" onkeypress="return isNumberDecimalKey(event,this)" >                                                       
          </div>

          <div class="col-lg-2 pl"><p>Valid From*</p></div>
          <div class="col-lg-2 pl">
            <input {{$ActionStatus}} type="date" name="VALID_FROM" id="VALID_FROM" value="{{isset($HDR->VALID_FROM)?$HDR->VALID_FROM:''}}" class="form-control" autocomplete="off" value="{{date('Y-m-d')}}" >
          </div>

          <div class="col-lg-2 pl"><p>Valid To*</p></div>
          <div class="col-lg-2 pl">
            <input {{$ActionStatus}} type="date" name="VALID_TO" id="VALID_TO" value="{{isset($HDR->VALID_TO)?$HDR->VALID_TO:''}}" class="form-control" autocomplete="off" value="{{date('Y-m-d')}}" >
          </div>
        </div>

        <div class="row">
          <div class="col-lg-2 pl"><p>De-Activated</p></div>
          <div class="col-lg-2 pl pr">
            <input {{$ActionStatus}} type="checkbox"   name="DEACTIVATED"  id="deactive-checkbox_0" {{$HDR->DEACTIVATED == 1 ? "checked" : ""}} value='{{$HDR->DEACTIVATED == 1 ? 1 : 0}}' tabindex="2"  >
          </div>
          
          <div class="col-lg-2 pl"><p>Date of De-Activated</p></div>
          <div class="col-lg-2 pl">
            <input {{$ActionStatus}} type="date" name="DODEACTIVATED" class="form-control" id="DODEACTIVATED" {{$HDR->DEACTIVATED == 1 ? "" : "disabled"}} value="{{isset($HDR->DODEACTIVATED) && $HDR->DODEACTIVATED !="" && $HDR->DODEACTIVATED !="1900-01-01" ? $HDR->DODEACTIVATED:''}}" tabindex="3" placeholder="dd/mm/yyyy"  />
          </div>
        </div>

      </div>

      <div class="container-fluid purchase-order-view">
        <div class="row">
          <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#udf">UDF</a></li>
          </ul>
                                              
          <div class="tab-content">

            <div id="udf" class="tab-pane fade in active">
              <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="margin-top:10px;height:280px;width:50%;">
                <table id="example4" class="display nowrap table table-striped table-bordered itemlist" style="height:auto !important;">
                  <thead id="thead1"  style="position: sticky;top: 0">
                    <tr>
                      <th>UDF Fields<input class="form-control" type="hidden" name="Row_Count3" id ="Row_Count3" value="{{count($objUdf)}}"></th>
                      <th>Value / Comments</th>
                    </tr>
                  </thead>                         
                  <tbody>
                    @foreach($objUdf as $udfkey => $udfrow)
                    <tr  class="participantRow4">
                      <td><input name={{"udffie_popup_".$udfkey}} id={{"txtudffie_popup_".$udfkey}} value="{{$udfrow->LABEL}}" class="form-control @if ($udfrow->ISMANDATORY==1) mandatory @endif" autocomplete="off" maxlength="100" disabled/></td>
                      <td hidden><input type="text" name='{{"udffie_".$udfkey}}' id='{{"hdnudffie_popup_".$udfkey}}' value="{{$udfrow->UDFID}}" class="form-control" maxlength="100" /></td>
                      <td hidden><input type="text" name={{"udffieismandatory_".$udfkey}} id={{"udffieismandatory_".$udfkey}} class="form-control" maxlength="100" value="{{$udfrow->ISMANDATORY}}" /></td>            
                      <td id="{{"tdinputid_".$udfkey}}">
                      @php
                      $dynamicid  = "udfvalue_".$udfkey;
                      $chkvaltype = strtolower($udfrow->VALUETYPE); 
                      $udf_value  = isset($udfrow->UDF_VALUE)?$udfrow->UDF_VALUE:'';
    
                      if($chkvaltype=='date'){
                        $strinp = '<input '.$ActionStatus.' type="date" placeholder="dd/mm/yyyy" name="'.$dynamicid.'" id="'.$dynamicid.'" value="'.$udf_value.'" class="form-control" value="" /> ';       
                      }
                      else if($chkvaltype=='time'){
                        $strinp= '<input '.$ActionStatus.' type="time" placeholder="h:i" name="'.$dynamicid.'" id="'.$dynamicid.'" value="'.$udf_value.'" class="form-control"  value=""/> ';
                      }
                      else if($chkvaltype=='numeric'){
                        $strinp = '<input '.$ActionStatus.' type="text" name="'.$dynamicid. '" id="'.$dynamicid.'" value="'.$udf_value.'" class="form-control" value=""  autocomplete="off" /> ';
                      }
                      else if($chkvaltype=='text'){
                        $strinp = '<input '.$ActionStatus.' type="text" name="'.$dynamicid. '" id="'.$dynamicid.'" value="'.$udf_value.'" class="form-control" value=""  autocomplete="off" /> ';
                      }
                      else if($chkvaltype=='boolean'){

                        $boolval = ''; 
                        if($udf_value =='on' || $udf_value  =='1'){
                          $boolval="checked";
                        }

                        $strinp = '<input '.$ActionStatus.' type="checkbox" name="'.$dynamicid. '" id="'.$dynamicid.'"  '.$boolval.' class=""  /> ';
                      }
                      else if($chkvaltype=='combobox'){
                        $strinp       ='';
                        $txtoptscombo = strtoupper($udfrow->DESCRIPTIONS); ;
                        $strarray     = explode(',',$txtoptscombo);
                        $opts         = '';
                        $chked        ='';

                        for ($i = 0; $i < count($strarray); $i++) {
                          $chked='';
                          if($strarray[$i]==$udf_value){
                            $chked='selected="selected"';
                          }

                          $opts = $opts.'<option value="'.$strarray[$i].'" '.$chked.'  >'.$strarray[$i].'</option> ';
                        }

                        $strinp = '<select '.$ActionStatus.' name="'.$dynamicid.'" id="'.$dynamicid.'" class="form-control" >'.$opts.'</select>' ;
                      }
                      echo $strinp;
                      @endphp
                      </td>
                    </tr>
                    @endforeach                             
                  </tbody>
                </table>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</form>
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
@endsection
@push('bottom-scripts')
<script>
var formTrans = $("#master_form");
formTrans.validate();

function saveAction(action){
  if(formTrans.valid()){
    validateForm(action);
  }
}

function validateForm(action){

  var flag_status   = [];
  var flag_focus    = '';
  var flag_message  = '';

  $("[id*=txtudffie_popup]").each(function(){
    if($.trim($(this).val())!=""){
      if($.trim($(this).parent().parent().find('[id*="udffieismandatory"]').val()) == "1"){
        if($.trim($(this).parent().parent().find('[id*="udfvalue"]').val()) != ""){
          flag_status.push('true');
        }
        else{
          flag_status.push('false');
          flag_focus    = $(this).parent().parent().find('[id*="udfvalue"]').attr('id');
          flag_message  = 'Please enter  Value / Comment in UDF Tab';
        }
      }             
    }             
  });

  if($.trim($("#DISCODE").val()) ===""){
    $("#FocusId").val('DISCODE');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please enter discount code.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($.trim($("#DOC_DATE").val()) ===""){
    $("#FocusId").val('DOC_DATE');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select document date.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  } 
  else if($.trim($("#DIS_OPT").val()) ==="Percentatge" && $.trim($("#DIS_PERCENT").val()) ===""){
    $("#FocusId").val('DIS_PERCENT');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please enter discount percent.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  } 
  else if($.trim($("#DIS_OPT").val()) ==="Amount" && $.trim($("#DIS_AMT").val()) ===""){
    $("#FocusId").val('DIS_AMT');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please enter discount amount.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  } 
  else if($.trim($("#VALID_SALES_INVOICE_AMT").val()) ===""){
    $("#FocusId").val('VALID_SALES_INVOICE_AMT');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please enter valid sales invoice amount.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  } 
  else if($.trim($("#VALID_FROM").val()) ===""){
    $("#FocusId").val('VALID_FROM');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select valid from.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  } 
  else if($.trim($("#VALID_TO").val()) ===""){
    $("#FocusId").val('VALID_TO');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select valid to.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($("#deactive-checkbox_0").is(":checked") == true && $.trim($("#DODEACTIVATED").val()) ===""){
    $("#FocusId").val('DODEACTIVATED');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select Date of De-Activated.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  } 
  else if(jQuery.inArray("false", flag_status) !== -1){
    $("#FocusId").val(flag_focus);        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text(flag_message);
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }   
  else{
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

function isNumberDecimalKey(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
    return false;

    return true;
}

$(function (){
	var today             = new Date(); 
  var dodeactived_date  = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
  $('#DODEACTIVATED').attr('min',dodeactived_date);

	$('input[type=checkbox][name=DEACTIVATED]').change(function(){
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
</script>
@endpush