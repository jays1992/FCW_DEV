@extends('layouts.app')
@section('content')
    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[198,'index'])}}" class="btn singlebt">Role Master</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
                        <button class="btn topnavbt" id="btnSaveSE" ><i class="fa fa-save"></i> Save</button>
                        <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt" id="btnUndo"  ><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove" disabled="disabled" {{($objRights->APPROVAL1||$objRights->APPROVAL2||$objRights->APPROVAL3||$objRights->APPROVAL4||$objRights->APPROVAL5) == 1 ? '' : 'disabled'}}><i class="fa fa-lock"></i> Approved</button>
                        <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                        <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                </div>
            </div><!--row-->
    </div>

<form id="frm_trn_se" onsubmit="return validateForm()"  method="POST" class="needs-validation"  >
    @csrf
    {{isset($objSE->ROLLID[0]) ? method_field('PUT') : '' }}
    <div class="container-fluid filter">

  <div class="inner-form">

    <div class="row">
			<div class="col-lg-1 pl"><p>Role Code</p></div>
			<div class="col-lg-1 pl">
        <input type="text" name="RCODE" id="RCODE"  class="form-control mandatory" value="{{$objSE->RCODE}}"  autocomplete="off" readonly style="text-transform:uppercase" autofocus >
   		</div>
			
			<div class="col-lg-1 pl col-md-offset-1"><p>Description</p></div>
			<div class="col-lg-2 pl pr">
        <input type="text" name="DESCRIPTIONS" id="DESCRIPTIONS" value="{{$objSE->DESCRIPTIONS}}" class="form-control" autocomplete="off" />
        <span class="text-danger" id="ERROR_DESCRIPTIONS"></span> 
      </div>
		</div>

    <div class="row">
      <div class="col-lg-1 pl"><p>Amendment No</p></div>
      <div class="col-lg-2 pl">
        <input type="text" name="ANO" id="ANO" value="{{isset($objSE->ANO) && $objSE->ANO !=''?$objSE->ANO+1:1}}"  class="form-control" autocomplete="off" readonly />
      </div>

      <div class="col-lg-1 pl"><p>Amendment Date</p></div>
      <div class="col-lg-2 pl">
        <input type="date" name="AMENDMENT_DATE" id="AMENDMENT_DATE" value="{{$objSE->AMENDMENT_DATE}}"  class="form-control mandatory" autocomplete="off">
   	  </div>

      <div class="col-lg-1 pl"><p>Amendment Reason</p></div>
      <div class="col-lg-2 pl">
        <input type="text" name="AMENDMENT_REASON" id="AMENDMENT_REASON" value="{{$objSE->AMENDMENT_REASON}}"  class="form-control mandatory" autocomplete="off">
   	  </div>
	  </div>

	</div>

    <div class="row">
      <div class="col-lg-1"><p>Module Name </p></div>  
      <div class="col-lg-11">
        <ul >
            @foreach ($ModuleList as $mod_row)
             @php
                if(in_array($mod_row->MODULEID_REF,$SavedModArr)){ 
                    $strchk = "checked";
                  }else{
                    $strchk = "";                    
                  }                 
             @endphp
            <li style="list-style: none; padding: 6px 0; display: inline-table;padding-right: 15px;"><input  style="margin-right: 5px;" type="checkbox" name="MODULE_NAME_{{$mod_row->MODULEID_REF}}"  id="MODULE_NAME_{{$mod_row->MODULEID_REF}}" value="{{$mod_row->MODULEID_REF}}" @php echo $strchk; @endphp >{{$mod_row->MODULENAME}} </li>     
            @endforeach
        </ul> 
      </div>       
    </div>  


    <div class="row" style="margin-left: 1px;">
      <div class="col-lg-2 pl"><p>De-Activated</p></div>
      <div class="col-lg-1 pl pr">
      <input type="checkbox"   name="DEACTIVATED"  id="deactive-checkbox_0" {{$objSE->DEACTIVATED == 1 ? "checked" : ""}}
        value='{{$objSE->DEACTIVATED == 1 ? 1 : 0}}' tabindex="2"  >
      </div>
      
      <div class="col-lg-2 pl"><p>Date of De-Activated</p></div>
      <div class="col-lg-2 pl">
        <input type="date" name="DODEACTIVATED" class="form-control" id="DODEACTIVATED" {{$objSE->DEACTIVATED == 1 ? "" : "disabled"}} value="{{isset($objSE->DODEACTIVATED) && $objSE->DODEACTIVATED !="" && $objSE->DODEACTIVATED !="1900-01-01" ? $objSE->DODEACTIVATED:''}}" tabindex="3" placeholder="dd/mm/yyyy"  />
      </div>
    </div>
  
</div>

<div class="container-fluid">

  <div class="row">
    <div class="tab-content">
                              <div id="Material" class="tab-pane fade in active">
                                  <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:450px;margin-top:10px;" >
                                      <table id="roleTbl" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                                          <thead id="thead1"  style="position: sticky;top:0;">
                                                  <tr >         
                                                        <th>Module Name</th>                                            
                                                        <th >Voucher Type Code <input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1"> </th>
                                                        <th>Voucher Description</th>
                                                        <th > <input type="checkbox" id="selectADD" name="ADD"/>Add</th>
                                                        <th><input type="checkbox" id="selectEDIT" name="EDIT"/>Edit</th>
                                                        <th><input type="checkbox" id="selectCANCEL" name="CANCEL"/>Cancel</th>
                                                        <th><input type="checkbox" id="selectVIEW" name="VIEW"/>View</th>
                                                        <th><input type="checkbox" id="selectAPPROVAL1" name="APPROVAL1"/>Approval 1</th>
                                                        <th><input type="checkbox" id="selectAPPROVAL2" name="APPROVAL2"/>Approval 2</th>
                                                        <th><input type="checkbox" id="selectAPPROVAL3" name="APPROVAL3"/>Approval 3</th>
                                                        <th><input type="checkbox" id="selectAPPROVAL4" name="APPROVAL4"/>Approval 4</th>
                                                        <th><input type="checkbox" id="selectAPPROVAL5" name="APPROVAL5"/>Approval 5</th>
                                                        <th><input type="checkbox" id="selectPRINT" name="PRINT"/>Print</th>
                                                        <th><input type="checkbox" id="selectATTACHMENT" name="ATTACHMENT"/>Attachment</th>
                                                        <th><input type="checkbox" id="selectAMENDMENT" name="AMENDMENT"/>Amendment</th>
                                                        <th><input type="checkbox" id="selectAMOUNT" name="AMOUNT"/>Amount Matrix</th>
                                                       
                                                  </tr>
                                          </thead>
                                          <tbody>
                                          @if(!empty($objSEMAT))
                                @foreach($objSEMAT as $key => $row) 
                                <tr  class="participantRow modulename_{{ $row->MODULEID }}">

                                  <td><input type="text" name="MODULENAME_{{$row->VTID_REF}}" id="MODULENAME_{{$row->VTID_REF}}" value="{{ $row->MODULENAME }}"  class="form-control mandatory" style="width:200px;" readonly="" tabindex="1"> </td>
                                  <td ><input type="text" name="VTID_REF_POPUP_{{$row->VTID_REF}}" id="VTID_REF_POPUP_{{$row->VTID_REF}}"   value="{{$row->VCODE}}" class="form-control mandatory" style="width:91px" readonly="" tabindex="1"></td>
                                  <td hidden> <input type="text" name="VTID_REF_{{$row->VTID_REF}}" id="VTID_REF_{{$row->VTID_REF}}"  value="{{$row->VTID_REF}}"  ><input type="text" name="rowscount[]" value="{{$row->VTID_REF}}" /></td>
                                  <td><input type="text" name="VTID_DESCRITPIONS_{{$row->VTID_REF}}" id="VTID_DESCRITPIONS_{{$row->VTID_REF}}" value="{{$row->DESCRIPTIONS}}"   class="form-control mandatory" style="width:250px" readonly="" tabindex="1"></td>

                                  <td style="text-align:center; width: 81px;"><input type="checkbox" name="ADD_{{$row->VTID_REF}}"       id="ADD_{{$row->VTID_REF}}"       {{$row->ADD == 1 ? 'checked' : ''}} ></td>
                                  <td style="text-align:center; width: 81px;"><input type="checkbox" name="EDIT_{{$row->VTID_REF}}"      id="EDIT_{{$row->VTID_REF}}"      {{$row->EDIT == 1 ? 'checked' : ''}} ></td>
                                  <td style="text-align:center;width: 81px;"><input type="checkbox"  name="CANCEL_{{$row->VTID_REF}}"    id="CANCEL_{{$row->VTID_REF}}"    {{$row->CANCEL == 1 ? 'checked' : ''}}  ></td>
                                  <td style="text-align:center; width: 81px;"><input type="checkbox" name="VIEW_{{$row->VTID_REF}}"      id="VIEW_{{$row->VTID_REF}}"      {{$row->VIEW == 1 ? 'checked' : ''}} ></td>
                                  <td style="text-align:center; width: 81px;"><input type="checkbox" name="APPROVAL1_{{$row->VTID_REF}}" id="APPROVAL1_{{$row->VTID_REF}}" {{$row->APPROVAL1 == 1 ? 'checked' : ''}}  ></td>
                                  <td style="text-align:center; width: 81px;"><input type="checkbox" name="APPROVAL2_{{$row->VTID_REF}}" id="APPROVAL2_{{$row->VTID_REF}}" {{$row->APPROVAL2 == 1 ? 'checked' : ''}}   ></td>
                                  <td style="text-align:center; width: 81px;"><input type="checkbox" name="APPROVAL3_{{$row->VTID_REF}}" id="APPROVAL3_{{$row->VTID_REF}}" {{$row->APPROVAL3 == 1 ? 'checked' : ''}}  ></td>
                                  <td style="text-align:center; width: 81px;"><input type="checkbox" name="APPROVAL4_{{$row->VTID_REF}}" id="APPROVAL4_{{$row->VTID_REF}}" {{$row->APPROVAL4 == 1 ? 'checked' : ''}}  ></td>
                                  <td style="text-align:center; width: 81px;"><input type="checkbox" name="APPROVAL5_{{$row->VTID_REF}}" id="APPROVAL5_{{$row->VTID_REF}}" {{$row->APPROVAL5 == 1 ? 'checked' : ''}}  ></td>
                                  <td style="text-align:center; width: 81px;"><input type="checkbox" name="PRINT_{{$row->VTID_REF}}"      id="PRINT_{{$row->VTID_REF}}"     {{$row->PRINT == 1 ? 'checked' : ''}}  ></td>
                                  <td style="text-align:center; width: 81px;"><input type="checkbox" name="ATTACHMENT_{{$row->VTID_REF}}" id="ATTACHMENT_{{$row->VTID_REF}}" {{$row->ATTECHMENT == 1 ? 'checked' : ''}}   ></td>
                                  <td style="text-align:center; width: 81px;"><input type="checkbox" name="AMENDMENT_{{$row->VTID_REF}}"  id="AMENDMENT_{{$row->VTID_REF}}"  {{$row->AMENDMENT == 1 ? 'checked' : ''}} ></td>
                                  <td style="text-align:center; width: 81px;"><input type="checkbox" name="AMOUNTMATRIX_{{$row->VTID_REF}}" id="AMOUNTMATRIX_{{$row->VTID_REF}}" {{$row->AMOUNT_MATRIX == 1 ? 'checked' : ''}}  ></td>
                                             
                                 </tr>
                                              
                                @endforeach 
                              @endif
                                          </tbody>
                                  </table>
                                  </div>	
                              </div>
                          </div>
                      </div>
  </div>
 
</div>

</div>
<!-- </div> -->
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
            <button class="btn alertbt" name='YesBtn' id="YesBtn" data-funcname="fnSaveData">
            <div id="alert-active" class="activeYes"></div>Yes
            </button>
            <button class="btn alertbt" name='NoBtn' id="NoBtn"   data-funcname="fnUndoNo" >
            <div id="alert-active" class="activeNo"></div>No
            </button>
            <button class="btn alertbt" name='OkBtn' id="OkBtn" style="display:none;margin-left: 90px;">
            <div id="alert-active" class="activeOk"></div>OK</button>
            <button class="btn alertbt" name='OkBtn1' id="OkBtn1" style="display:none;margin-left: 90px;">
            <div id="alert-active" class="activeOk1"></div>OK</button>
        </div><!--btdiv-->
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<!-- Alert -->

<!-- Alert -->

@endsection


@push('bottom-css')
<style>
#custom_dropdown, #udfforsemst_filter {
    display: inline-table;
    margin-left: 15px;
}
.dataTables_wrapper .row:nth-child(1) .col-sm-6:nth-child(2){text-align:right;}
#filtercolumn{color: #555;
    background-color: #fff;
    background-image: none;
    border: 1px solid #ccc;
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
  width: 950px;
  border: 1px solid #ddd;
  font-size: 11px;
}

#ItemIDTable th {
    text-align: center;
    padding: 5px;
    font-size: 11px;
 
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

    font-weight: 600;
}

#ItemIDTable2 td {
  text-align: left;
    padding: 5px;
    font-size: 11px;
      font-weight: 600;
    width: 16%;
}

</style>
@endpush
@push('bottom-scripts')
<script>

  
$('#btnAdd').on('click', function() {
  var viewURL = '{{route("master",[198,"add"])}}';
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
    //reload form
    window.location.reload();
}//fnUndoYes

window.fnUndoNo = function (){
    $("#ENQNO").focus();
}//fnUndoNo

</script>

@endpush

@push('bottom-scripts')
<script>

$(document).ready(function() {

    $('#frm_trn_se1').bootstrapValidator({
       
        fields: {
            txtlabel: {
                validators: {
                    notEmpty: {
                        message: 'The Enquiry Number is required'
                    }
                }
            },            
        },
        submitHandler: function(validator, form, submitButton) {
            alert( "Handler for .submit() called." );
             event.preventDefault();
             $("#frm_trn_se").submit();
        }
    });
});


function validateForm(){
 
 $("#FocusId").val('');
 var RCODE          =   $.trim($("#RCODE").val());
 var DESCRIPTIONS          =   $.trim($("#DESCRIPTIONS").val());
 var DODEACTIVATED          =   $.trim($("#DODEACTIVATED").val());
 var AMENDMENT_DATE         =   $.trim($("#AMENDMENT_DATE").val());
 var AMENDMENT_REASON       =   $.trim($("#AMENDMENT_REASON").val());


 if(RCODE ===""){
     $("#FocusId").val($("#RCODE"));
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please enter value in Role Code.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if(DESCRIPTIONS ===""){
     $("#FocusId").val($("#DESCRIPTIONS"));
     $("#DESCRIPTIONS").val();  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Enter value in Description.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if(AMENDMENT_DATE ===""){
     $("#FocusId").val($("#AMENDMENT_DATE"));
     $("#DESCRIPTIONS").val();  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please select amendment date.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 } 
 else if(AMENDMENT_REASON ===""){
     $("#FocusId").val($("#AMENDMENT_REASON"));
     $("#DESCRIPTIONS").val();  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please select amendment season.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }     

 
 if ($('input[name="DEACTIVATED"]:checked').length != 0 &&  DODEACTIVATED =="" ) {
    $("#FocusId").val($("#DESCRIPTIONS"));
     $("#DESCRIPTIONS").val();  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Select Date of De-Activation.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }



 else{

        event.preventDefault();
        var allblank = [];    
        var selectedModule = false;
        $("[id*='MODULE_NAME_']").each(function(){
          if($(this).is(":checked")  == true )
            {
              selectedModule = true;
            }
        });

        if( selectedModule==false){
            $("#alert").modal('show');
            $("#AlertMessage").text('Please Select Module Name.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk1');
            return false;
        }   

          $('#roleTbl').find('.participantRow').each(function(){
            if($.trim($(this).find("[id*=VTID_REF]").val())!=""){
            allblank.push('true');
                }
            else{
                  allblank.push('false');
              } 
        }); }


        if(jQuery.inArray("false", allblank) !== -1){
          $("#alert").modal('show');
          $("#AlertMessage").text('Please select Voucher Type in Role Details Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
          }
      
   
          else{
                $("#alert").modal('show');
                $("#AlertMessage").text('Do you want to save to record.');
                $("#YesBtn").data("funcname","fnSaveData");  //set dynamic fucntion name
                $("#YesBtn").focus();
                $("#OkBtn").hide();
                highlighFocusBtn('activeYes');
          }

}

$("#btnSaveSE" ).click(function() {
    var formReqData = $("#frm_trn_se");
    if(formReqData.valid()){
      validateForm();
    }
});



$("#YesBtn").click(function(){

$("#alert").modal('hide');
var customFnName = $("#YesBtn").data("funcname");
    window[customFnName]();

}); //yes button

window.fnSaveData = function (){
event.preventDefault();
      var trnseForm = $("#frm_trn_se");
      var formData = trnseForm.serialize();
  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });
  $.ajax({
      url:'{{ route("mastermodify",[198,"saveamendment"])}}',
      type:'POST',
      data:formData,
      success:function(data) {
          if(data.errors) {
              $(".text-danger").hide();
              if(data.errors.LABEL){
                  showError('ERROR_LABEL',data.errors.LABEL);
                          $("#YesBtn").hide();
                          $("#NoBtn").hide();
                          $("#OkBtn1").show();
                          $("#OkBtn").hide();
                          $("#AlertMessage").text('Please enter correct value in Label.');
                          $("#alert").modal('show');
                          $("#OkBtn1").focus();
              }
            if(data.country=='norecord') {
                ("#YesBtn").hide();
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
      var trnseForm = $("#frm_trn_se");
      var formData = trnseForm.serialize();
  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });
  $.ajax({
      url:'{{ route("mastermodify",[198,"Approve"])}}',
      type:'POST',
      data:formData,
      success:function(data) {
          if(data.errors) {
              $(".text-danger").hide();
              if(data.errors.LABEL){
                  showError('ERROR_LABEL',data.errors.LABEL);
                          $("#YesBtn").hide();
                          $("#NoBtn").hide();
                          $("#OkBtn1").show();
                          $("#AlertMessage").text('Please enter correct value in Voucher Type.');
                          $("#alert").modal('show');
                          $("#OkBtn1").focus();
              }
            if(data.country=='norecord') {
                ("#YesBtn").hide();
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

//no button
$("#NoBtn").click(function(){
    $("#alert").modal('hide');
    $("#LABEL").focus();
});

//ok button
$("#OkBtn").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn").hide();
    $(".text-danger").hide();
    window.location.href = '{{route("master",[198,"index"]) }}';
});

$("#OkBtn1").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn").hide();
    $("#OkBtn1").hide();
    $("#"+$(this).data('focusname')).focus();
    // $("[id*=txtlabel]").focus();
    $(".text-danger").hide();
});

//
function showError(pId,pVal){
    $("#"+pId+"").text(pVal);
    $("#"+pId+"").show();
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


$( "#btnApprove" ).click(function() {

  $("#FocusId").val('');
 var RCODE          =   $.trim($("#RCODE").val());
 var DESCRIPTIONS   =   $.trim($("#DESCRIPTIONS").val());


 if(RCODE ===""){
     $("#FocusId").val($("#RCODE"));
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please enter value in Role Code.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if(DESCRIPTIONS ===""){
     $("#FocusId").val($("#DESCRIPTIONS"));
     $("#DESCRIPTIONS").val();  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Enter value in Description.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }  



 else{

    event.preventDefault();
    var allblank = [];    
    var selectedModule = false;
        $("[id*='MODULE_NAME_']").each(function(){
          if($(this).is(":checked")  == true )
            {
              selectedModule = true;
            }
        });

        if( selectedModule==false){
            $("#alert").modal('show');
            $("#AlertMessage").text('Please Select Module Name.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk1');
            return false;
        }   

        // $('#udfforsebody').find('.form-control').each(function () {
          $('#roleTbl').find('.participantRow').each(function(){
          if($.trim($(this).find("[id*=VTID_REF]").val())!=""){
           allblank.push('true');
               }
          else{
                allblank.push('false');
            } 


        }); }


        if(jQuery.inArray("false", allblank) !== -1){
          $("#alert").modal('show');
          $("#AlertMessage").text('Please select Voucher Type in Role Details Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
          }
   
          else{
             $("#alert").modal('show');
                $("#AlertMessage").text('Do you want to save to record.');
                $("#YesBtn").data("funcname","fnApproveData");  //set dynamic fucntion name
                $("#YesBtn").focus();
                $("#OkBtn").hide();
                highlighFocusBtn('activeYes');
          }

       
});

$(function () {
	
	var today = new Date(); 
    var dodeactived_date = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
    $('#DODEACTIVATED').attr('min',dodeactived_date);

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



$('#selectADD').click(function(){ 

    $('#roleTbl').find('.participantRow').each(function(){

      if ($('input[name="ADD"]:checked').length == 0){

      $(this).find("[id*=ADD]").attr('checked',false);
    }else{
      $(this).find("[id*=ADD]").attr('checked',true);

    }
 
  });
});

$('#selectEDIT').click(function(){ 

$('#roleTbl').find('.participantRow').each(function(){
  if ($('input[name="EDIT"]:checked').length == 0){

    $(this).find("[id*=EDIT]").attr('checked',false);
    }else{
    $(this).find("[id*=EDIT]").attr('checked',true);

  }
  });
});

$('#selectCANCEL').click(function(){ 

$('#roleTbl').find('.participantRow').each(function(){
  if ($('input[name="CANCEL"]:checked').length == 0){

  $(this).find("[id*=CANCEL]").attr('checked',false);
  }else{
  $(this).find("[id*=CANCEL]").attr('checked',true);

  }
  });
});

$('#selectVIEW').click(function(){ 

$('#roleTbl').find('.participantRow').each(function(){
  if ($('input[name="VIEW"]:checked').length == 0){

  $(this).find("[id*=VIEW]").attr('checked',false);
  }else{
  $(this).find("[id*=VIEW]").attr('checked',true);

  }
  });
});

$('#selectAPPROVAL1').click(function(){ 

$('#roleTbl').find('.participantRow').each(function(){
  if ($('input[name="APPROVAL1"]:checked').length == 0){

  $(this).find("[id*=APPROVAL1]").attr('checked',false);
  }else{
  $(this).find("[id*=APPROVAL1]").attr('checked',true);

  }
  });
});
$('#selectAPPROVAL2').click(function(){ 

$('#roleTbl').find('.participantRow').each(function(){
  if ($('input[name="APPROVAL2"]:checked').length == 0){

    $(this).find("[id*=APPROVAL2]").attr('checked',false);
    }else{
    $(this).find("[id*=APPROVAL2]").attr('checked',true);

    }
  });
});
$('#selectAPPROVAL3').click(function(){ 

$('#roleTbl').find('.participantRow').each(function(){
  if ($('input[name="APPROVAL3"]:checked').length == 0){

  $(this).find("[id*=APPROVAL3]").attr('checked',false);
  }else{
  $(this).find("[id*=APPROVAL3]").attr('checked',true);

  }
  });
});
$('#selectAPPROVAL4').click(function(){ 

$('#roleTbl').find('.participantRow').each(function(){
  if ($('input[name="APPROVAL4"]:checked').length == 0){

  $(this).find("[id*=APPROVAL4]").attr('checked',false);
  }else{
  $(this).find("[id*=APPROVAL4]").attr('checked',true);

  }
  });
});
$('#selectAPPROVAL5').click(function(){ 

$('#roleTbl').find('.participantRow').each(function(){
  if ($('input[name="APPROVAL5"]:checked').length == 0){

  $(this).find("[id*=APPROVAL5]").attr('checked',false);
  }else{
  $(this).find("[id*=APPROVAL5]").attr('checked',true);

  }
  });
});

$('#selectPRINT').click(function(){ 

$('#roleTbl').find('.participantRow').each(function(){
  if ($('input[name="PRINT"]:checked').length == 0){

    $(this).find("[id*=PRINT]").attr('checked',false);
    }else{
    $(this).find("[id*=PRINT]").attr('checked',true);

    }
  });
});
$('#selectATTACHMENT').click(function(){ 

$('#roleTbl').find('.participantRow').each(function(){
  if ($('input[name="ATTACHMENT"]:checked').length == 0){

  $(this).find("[id*=ATTACHMENT]").attr('checked',false);
  }else{
  $(this).find("[id*=ATTACHMENT]").attr('checked',true);

  }
  });
});
$('#selectAMENDMENT').click(function(){ 

$('#roleTbl').find('.participantRow').each(function(){
  if ($('input[name="AMENDMENT"]:checked').length == 0){

  $(this).find("[id*=AMENDMENT]").attr('checked',false);
  }else{
  $(this).find("[id*=AMENDMENT]").attr('checked',true);

  }
  });
});
$('#selectAMOUNT').click(function(){ 

$('#roleTbl').find('.participantRow').each(function(){
  if ($('input[name="AMOUNT"]:checked').length == 0){

    $(this).find("[id*=AMOUNTMATRIX]").attr('checked',false);
    }else{
    $(this).find("[id*=AMOUNTMATRIX]").attr('checked',true);

    }
  });
});


$('input[type=checkbox][id*="MODULE_NAME_"]').change(function() {
      if ($(this).prop("checked")) {
        //$(this).val('1');
        //$('#DODEACTIVATED').removeAttr('disabled');
        loadsVouchersList($(this).val());
      }
      else {
        // $(this).val('0');
        // $('#DODEACTIVATED').prop('disabled', true);
        // $('#DODEACTIVATED').val('');
        $('.modulename_'+$(this).val()).remove();
      }
  });

  function loadsVouchersList(modid){

      var module_id = modid;
     
      $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });

      $.ajax({
          url:'{{route("master",[198,"loadsVouchersList"])}}',
          type:'POST',
          data:{'module_id':module_id},
          success:function(data) {
            $("#roleTbl tbody").prepend(data);                
          },
          error:function(data){
            console.log("Error: Something went wrong.");
            $("#roleTbl tbody").html('');                        
          },
      }); 

  }


</script>
@endpush