@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Loan Disbursement</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
                        <button class="btn topnavbt" id="btnSaveSE" disabled="disabled"><i class="fa fa-save"></i> Save</button>
                        <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt" id="btnUndo"  disabled="disabled"><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-lock"></i> Approved</button>
                        <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                        <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                </div><!--col-10-->

            </div><!--row-->
    </div><!--topnav-->	
   
    <div class="container-fluid purchase-order-view filter">     
      <form id="frm_mst_edit" method="POST" onsubmit="return validateForm()" class="needs-validation"  > 
        @CSRF
        {{isset($objResponse->LEAVE_APPID) ? method_field('PUT') : '' }}
        <div class="inner-form">
            
              <div class="row">
                <div class="col-lg-2 pl"><p>Doc No*</p></div>
                <div class="col-lg-2 pl">
                  <input type="text" name="LOAN_DISB_DOCNO" id="LOAN_DISB_DOCNO" VALUE="{{isset($objResponse->LOAN_DISB_DOCNO)?$objResponse->LOAN_DISB_DOCNO:''}}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase" disabled>
                  <span class="text-danger" id="ERROR_LEAVE_APP_NO"></span> 
                </div>

                <div class="col-lg-2 pl"><p>LD Date*</p></div>
                  <div class="col-lg-2 pl">
                    <input type="date" name="LOAN_DISB_DOCDT" id="LOAN_DISB_DOCDT" value="{{ $objResponse->LOAN_DISB_DOCDT }}" class="form-control" disabled maxlength="100" > 
                  </div>

                <div class="col-lg-2 pl"><p>Pay Period Code*</p></div>
                  <div class="col-lg-2 pl">
                    <select name="PAYPID_REF" id="PAYPID_REF" class="form-control mandatory" onchange="getPayPrName(this.value)" tabindex="4" disabled>
                      <option value="" selected="">Select</option>
                      @foreach($objList as $val)
                      <option {{isset($objResponse->PAYPID_REF) && $objResponse->PAYPID_REF == $val-> PAYPERIODID ?'selected="selected"':''}} value="{{ $val-> PAYPERIODID }}">{{ $val->PAY_PERIOD_CODE }}</option>
                      @endforeach
                    </select>
                    <span class="text-danger" id="ERROR_PAYPERIODID_REF"></span>                             
                  </div>
                </div> 

                <div class="row">
                  <div class="col-lg-2 pl"><p>Description</p></div>
                  <div class="col-lg-2 pl">
                    <input type="text" id="PAY_PERIOD_DESC" value="{{ $objLvDesList->PAY_PERIOD_DESC }}" class="form-control" readonly  maxlength="100" disabled> 
                  </div>
                
                  <div class="col-lg-2 pl"><p>Employee Code *</p></div>
                  <div class="col-lg-2 pl">
                  <select name="EMPID_REF" id="EMPID_REF" class="form-control mandatory" onchange="getEmpName(this.value)" tabindex="4" disabled>
                    <option value="" selected="">Select</option>
                    @foreach($objEmpList as $val)
                    <option {{isset($objResponse->EMPID_REF) && $objResponse->EMPID_REF == $val-> EMPID ?'selected="selected"':''}} value="{{ $val-> EMPID }}">{{ $val->EMPCODE }}</option>
                    @endforeach
                  </select>
                  <input type="hidden" id="focusid" >
                  <span class="text-danger" id="ERROR_EMPID_REF"></span> 
                </div>
                
                <div class="col-lg-2 pl"><p>Name</p></div>
                  <div class="col-lg-2 pl">
                    <input type="text" id="FNAME" value="{{ $objEmpName->FNAME }}" class="form-control" readonly maxlength="100" disabled> 
                  </div>
              </div>


              <div class="row">
                <div class="col-lg-2 pl"><p>Loan Type Code*</p></div>
                <div class="col-lg-2 pl">
                  <select name="LOANTYPEID_REF" id="LOANTYPEID_REF" class="form-control mandatory" onchange="getLtypeCode(this.value)" tabindex="4" disabled>
                    <option value="" selected="">Select</option>
                    @foreach($objLtypeList as $val)
                    <option {{isset($objResponse->LOANTYPEID_REF) && $objResponse->LOANTYPEID_REF == $val-> LOANTYPEID ?'selected="selected"':''}} value="{{ $val-> LOANTYPEID }}">{{ $val->LOANTYPE_CODE }}</option>
                    @endforeach
                  </select>
                  <span class="text-danger" id="ERROR_PAYPERIODID_REF"></span>                             
                </div>
              
                <div class="col-lg-2 pl"><p>Description*</p></div>
                <div class="col-lg-2 pl">
                  <input type="text" id="LOANTYPE_DESC" value="{{ $objLoanTyName->LOANTYPE_DESC }}" class="form-control" readonly  maxlength="100" disabled>
                <input type="hidden" id="focusid" >
                <span class="text-danger" id="ERROR_EMPID_REF"></span>                             
              </div>
              
              <div class="col-lg-2 pl"><p>Disbursed Loan Amount</p></div>
                <div class="col-lg-2 pl">
                  <input type="text" name="LOAN_DISB_AMT" id="LOAN_DISB_AMT" value="{{ $objResponse->LOAN_DISB_AMT }}" class="form-control" onkeyup="disLAount(this.id)" onkeypress="return onlyNumberKey(event)"  maxlength="100" disabled>
                </div>
            </div>



            <div class="row">
              <div class="col-lg-2 pl"><p>No of Installments*</p></div>
              <div class="col-lg-2 pl">
                <input type="text" name="NO_OF_INSTALL" id="NO_OF_INSTALL" value="{{ $objResponse->NO_OF_INSTALL }}" class="form-control" onkeyup="disLAount(this.id)" onkeypress="return onlyNumberKey(event)"  maxlength="100" disabled>   
              </div>
            
              <div class="col-lg-2 pl"><p>EMI Amount*</p></div>
              <div class="col-lg-2 pl">
                <input type="text" name="EMI_AMT" id="EMI_AMT" value="{{ $objResponse->EMI_AMT }}" class="form-control" readonly  maxlength="100" disabled> 
              <input type="hidden" id="focusid" >
              <span class="text-danger" id="ERROR_EMPID_REF"></span>                             
            </div>
            
            <div class="col-lg-2 pl"><p>Start Deduction - Pay Period *</p></div>
              <div class="col-lg-2 pl">
                <select name="START_DEDUCT_PPID_REF" id="START_DEDUCT_PPID_REF" class="form-control mandatory" tabindex="4" disabled>
                  <option value="" selected="">Select</option>
                  @foreach($objList as $val)
                  <option {{isset($objResponse->START_DEDUCT_PPID_REF) && $objResponse->START_DEDUCT_PPID_REF == $val-> PAYPERIODID ?'selected="selected"':''}} value="{{ $val-> PAYPERIODID }}">{{ $val->PAY_PERIOD_CODE }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-2 pl"><p>Remarks</p></div>
              <div class="col-lg-2 pl">
                <input type="text" name="REMARKS" id="REMARKS" value="{{ $objResponse->REMARKS }}" class="form-control"  maxlength="100" disabled> 
              </div>
            </div>
        </div>
      </form>
  </div><!--purchase-order-view-->
  
@endsection
@section('alert')
<!-- Alert -->
<div id="alert" class="modal"  role="dialog"  data-backdrop="static">
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
          <button class="btn alertbt" name='OkBtn1' id="OkBtn1" style="display:none;margin-left: 90px;">
            <div id="alert-active" class="activeOk1"></div>OK</button>
          
      </div><!--btdiv-->
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


function setfocus(){
  var focusid=$("#focusid").val();
  $("#"+focusid).focus();
  $("#closePopup").click();
} 

function validateForm(){

  $("#focusid").val('');

  var LOAN_DISB_DOCNO             =   $.trim($("[id*=LOAN_DISB_DOCNO]").val());
    var LOAN_DISB_DOCDT           =   $.trim($("[id*=LOAN_DISB_DOCDT]").val());
    var PAYPID_REF                =   $.trim($("[id*=PAYPID_REF]").val());
    var EMPID_REF                 =   $.trim($("[id*=EMPID_REF]").val());
    var LOANTYPEID_REF            =   $.trim($("[id*=LOANTYPEID_REF]").val());
    var LOAN_DISB_AMT             =   $.trim($("[id*=LOAN_DISB_AMT]").val());
    var NO_OF_INSTALL             =   $.trim($("[id*=NO_OF_INSTALL]").val());
    var EMI_AMT                   =   $.trim($("[id*=EMI_AMT]").val());
    var START_DEDUCT_PPID_REF     =   $.trim($("[id*=START_DEDUCT_PPID_REF]").val());
    var MONO_INLEAVE2             =   $.trim($("[id*=MONO_INLEAVE2]").val());
    $("#OkBtn1").hide();

    if(LOAN_DISB_DOCNO ===""){
      $("#focusid").val('LOAN_DISB_DOCNO');
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").hide();  
      $("#OkBtn").show();              
      $("#AlertMessage").text('Please enter LA No.');
      $("#alert").modal('show');
      $("#OkBtn").focus();
      return false;
    }
    else if(LOAN_DISB_DOCDT ===""){
      $("#focusid").val('LOAN_DISB_DOCDT');
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").hide();  
      $("#OkBtn").show();              
      $("#AlertMessage").text('Please enter LA Date.');
      $("#alert").modal('show');
      $("#OkBtn").focus();
      return false;
    }

    else if(PAYPID_REF ===""){
      $("#focusid").val('PAYPID_REF');
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").hide();  
      $("#OkBtn").show();              
      $("#AlertMessage").text('Please enter Pay Period Code.');
      $("#alert").modal('show');
      $("#OkBtn").focus();
      return false;
    }
    else if(EMPID_REF ===""){
      $("#focusid").val('EMPID_REF');
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").hide();  
      $("#OkBtn").show();              
      $("#AlertMessage").text('Please enter Employee Code.');
      $("#alert").modal('show');
      $("#OkBtn").focus();
      return false;
    }
    else if(LOANTYPEID_REF ===""){
      $("#focusid").val('LOANTYPEID_REF');
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").hide();  
      $("#OkBtn").show();              
      $("#AlertMessage").text('Please enter Loan Type Code.');
      $("#alert").modal('show');
      $("#OkBtn").focus();
      return false;
    }
    else if(LOAN_DISB_AMT ===""){
      $("#focusid").val('LOAN_DISB_AMT');
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").hide();  
      $("#OkBtn").show();              
      $("#AlertMessage").text('Please enter Disbursed Loan Amount.');
      $("#alert").modal('show');
      $("#OkBtn").focus();
      return false;
    }
    else if(NO_OF_INSTALL ===""){
      $("#focusid").val('NO_OF_INSTALL');
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").hide();  
      $("#OkBtn").show();              
      $("#AlertMessage").text('Please enter Reason of Leave.');
      $("#alert").modal('show');
      $("#OkBtn").focus();
      return false;
    }
    else if(EMI_AMT ===""){
      $("#focusid").val('EMI_AMT');
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").hide();  
      $("#OkBtn").show();              
      $("#AlertMessage").text('Please enter Address During the leave.');
      $("#alert").modal('show');
      $("#OkBtn").focus();
      return false;
    }
    else if(START_DEDUCT_PPID_REF ===""){
      $("#focusid").val('START_DEDUCT_PPID_REF');
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").hide();  
      $("#OkBtn").show();              
      $("#AlertMessage").text('Please enter Contact no During the leave 1.');
      $("#alert").modal('show');
      $("#OkBtn").focus();
      return false;
    }
  else{
      event.preventDefault();
      $("#alert").modal('show');
      $("#AlertMessage").text('Do you want to save to record.');
      $("#YesBtn").data("funcname","fnSaveData");  
      $("#YesBtn").focus();
      highlighFocusBtn('activeYes');
  }

}

$('#btnAdd').on('click', function() {
    var viewURL = '{{route("transaction",[403,"add"])}}';
    window.location.href=viewURL;
});

$('#btnExit').on('click', function() {
  var viewURL = '{{route('home')}}';
  window.location.href=viewURL;
});

var formResponseMst = $( "#frm_mst_edit" );
   formResponseMst.validate();
  $("#DESCRIPTIONS").blur(function(){
      $(this).val($.trim( $(this).val() ));
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

  //validae single element
  function validateSingleElemnet(element_id){
    var validator =$("#frm_mst_edit" ).validate();
       if(validator.element( "#"+element_id+"" )){
          //check duplicate code
        if(element_id=="ATTCODE" || element_id=="attcode" ) {
          checkDuplicateCode();
        }

       }

  }

  // //check duplicate exist code
  function checkDuplicateCode(){
      
      //validate and save data
      var getDataForm = $("#frm_mst_edit");
      var formData = getDataForm.serialize();
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
      $.ajax({
          url:'{{route("transaction",[403,"codeduplicate"])}}',
          type:'POST',
          data:formData,
          success:function(data) {
              if(data.exists) {
                  $(".text-danger").hide();
                  showError('ERROR_ATTCODE',data.msg);
                  $("#ATTCODE").focus();
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
  
//validate and approve
$("#btnApprove").click(function() {
        
        if(formResponseMst.valid()){
            //set function nane of yes and no btn 
            $("#alert").modal('show');
            $("#AlertMessage").text('Do you want to save to record.');
            $("#YesBtn").data("funcname","fnApproveData");  //set dynamic fucntion name of approval
            $("#YesBtn").focus();
            highlighFocusBtn('activeYes');

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
          url:'{{ route("transactionmodify",[403,"update"]) }}',
          type:'POST',
          data:formData,
          success:function(data) {
             
              if(data.errors) {
                  $(".text-danger").hide();                    
                  if(data.errors.DESCRIPTIONS){
                     // showError('ERROR_DESCRIPTIONS',data.errors.DESCRIPTIONS);
                      $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn1").hide();
                      $("#OkBtn").show();
                      $("#AlertMessage").text("Attribute Description is "+data.errors.DESCRIPTIONS);
                      $("#alert").modal('show');
                      $("#OkBtn").focus();
                  }
                 if(data.exist=='duplicate') {

                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn1").hide();
                    $("#OkBtn").show();

                    $("#AlertMessage").text(data.msg);

                    $("#alert").modal('show');
                    $("#OkBtn").focus();

                 }
                 if(data.save=='invalid') {

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
                  $("#OkBtn1").show();
                  $("#OkBtn").hide();

                  $("#AlertMessage").text(data.msg);

                  $(".text-danger").hide();
                  $("#frm_mst_edit").trigger("reset");

                  $("#alert").modal('show');
                  $("#OkBtn1").focus();

                //  window.location.href='{{ route("transaction",[403,"index"])}}';
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
 

 
window.fnApproveData = function (){

event.preventDefault();
var trnsoForm = $("#frm_mst_edit");
var formData = trnsoForm.serialize();

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$.ajax({
    url:'{{ route("transactionmodify",[403,"Approve"])}}',
    type:'POST',
    data:formData,
    success:function(data) {
      
        if(data.errors) {
            $(".text-danger").hide();

            if(data.errors.PAYPERIODID_REF){
                showError('ERROR_PAYPERIODID_REF',data.errors.PAYPERIODID_REF);
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
  
  $("#OkBtn").click(function(){

      $("#alert").modal('hide');

      $("#YesBtn").show();  //reset
      $("#NoBtn").show();   //reset
      $("#OkBtn").hide();
      $("#OkBtn1").hide();
      $(".text-danger").hide();
      window.location.href = '{{route("transaction",[403,"index"]) }}'; 
  }); ///ok button

  
  
  $("#btnUndo").click(function(){

      $("#AlertMessage").text("Do you want to erase entered information in this record?");
      $("#alert").modal('show');

      $("#YesBtn").data("funcname","fnUndoYes");
      $("#YesBtn").show();

      $("#NoBtn").data("funcname","fnUndoNo");
      $("#NoBtn").show();
      
      $("#OkBtn").hide();
      $("#OkBtn1").hide();
      $("#NoBtn").focus();
      highlighFocusBtn('activeNo');
      
  }); ////Undo button

  
  $("#OkBtn1").click(function(){

  $("#alert").modal('hide');
  $("#YesBtn").show();  //reset
  $("#NoBtn").show();   //reset
  $("#OkBtn").hide();
  $("#OkBtn1").hide();
  $(".text-danger").hide();
  window.location.href = "{{route('transaction',[403,'index'])}}";

  });


  
  $("#OkBtn").click(function(){
    $("#alert").modal('hide');

  });////ok button


 window.fnUndoYes = function (){
    
    //reload form
    window.location.href = "{{route('transaction',[403,'add'])}}";

 }//fnUndoYes

  function showError(pId,pVal){

    $("#"+pId+"").text(pVal);
    $("#"+pId+"").show();

  }//showError

  function highlighFocusBtn(pclass){
     $(".activeYes").hide();
     $(".activeNo").hide();
     
     $("."+pclass+"").show();
  }  

  
  function getPayPrName(PAYPERIODID){
  $("#PAY_PERIOD_DESC").val('');
  
  $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
  
      $.ajax({
          url:'{{route("transaction",[403,"getPayPrName"])}}',
          type:'POST',
          data:{PAYPERIODID:PAYPERIODID},
          success:function(data) {
             $("#PAY_PERIOD_DESC").val(data);                
          },
          error:function(data){
            console.log("Error: Something went wrong.");
          },
      });	
}

function getLtypeCode(LOANTYPEID){
		$("#LOANTYPE_DESC").val('');
		
		$.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
		
        $.ajax({
            url:'{{route("transaction",[403,"getLtypeCode"])}}',
            type:'POST',
            data:{LOANTYPEID:LOANTYPEID},
            success:function(data) {
               $("#LOANTYPE_DESC").val(data);                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });	
  }

function getEmpName(EMPID){
  $("#FNAME").val('');
  
  $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
  
      $.ajax({
          url:'{{route("transaction",[403,"getEmpName"])}}',
          type:'POST',
          data:{EMPID:EMPID},
          success:function(data) {
             $("#FNAME").val(data);                
          },
          error:function(data){
            console.log("Error: Something went wrong.");
          },
      });	
}

function getLeaveTyName(id,LTID){

  var ROW_ID = id.split('_').pop();
  
  $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
  
      $.ajax({
          url:'{{route("transaction",[403,"getLeaveTyName"])}}',
          type:'POST',
          data:{LTID:LTID},
          success:function(data) {
             $('#LEAVETYPE_DESC_'+ROW_ID+'').val(data);                
          },
          error:function(data){
            console.log("Error: Something went wrong.");
          },
      });	
}

function disLAount(id){
  var LOAN_DISB_AMT      =   $.trim($("[id*=LOAN_DISB_AMT]").val());
  var NO_OF_INSTALL      =   $.trim($("[id*=NO_OF_INSTALL]").val());
    var EMI_AMT = (parseFloat(LOAN_DISB_AMT))/(parseFloat(NO_OF_INSTALL)).toFixed(2);
    if(isNaN(EMI_AMT)){ return 0;}
    $("#EMI_AMT").val(EMI_AMT);
}
</script>

<script>
  function onlyNumberKey(evt) {
      var ASCIICode = (evt.which) ? evt.which : evt.keyCode
      if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
          return false;
      return true;
  }
</script>

@endpush