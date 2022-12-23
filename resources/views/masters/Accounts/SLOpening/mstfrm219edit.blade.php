@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
  <div class="row">
    <div class="col-lg-2">
      <a href="{{route('master',[219,'index'])}}" class="btn singlebt">SL Opening</a>
    </div>

    <div class="col-lg-10 topnav-pd">
      <button href="#" id="btnSelectedRows" class="btn topnavbt" disabled="disabled" ><i class="fa fa-plus"></i> Add</button>
      <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
      <button <?php echo $OpeningStatus =='A'?'disabled':'';?> id="btnSave" {{isset($objRights->EDIT) && $objRights->EDIT != 1 ? 'disabled' : ''}}    class="btn topnavbt" tabindex="4"><i class="fa fa-save"></i> Save</button>
      <button class="btn topnavbt" id="btnView"  disabled="disabled"><i class="fa fa-eye"></i> View</button>
      <button class="btn topnavbt" disabled="disabled"><i class="fa fa-print"></i> Print</button>
      <button <?php echo $OpeningStatus =='A'?'disabled':'';?> class="btn topnavbt" id="btnUndo" ><i class="fa fa-undo"></i> Undo</button>
      <button class="btn topnavbt" id="btnCancel" disabled="disabled" ><i class="fa fa-times"></i> Cancel</button>
      <button <?php echo $OpeningStatus =='A'?'disabled':'';?> class="btn topnavbt" id="btnApprove" {{ (isset($objRights->APPROVAL1) || isset($objRights->APPROVAL2) || isset($objRights->APPROVAL3) || isset($objRights->APPROVAL4) || isset($objRights->APPROVAL5)) &&  ($objRights->APPROVAL1||$objRights->APPROVAL2||$objRights->APPROVAL3||$objRights->APPROVAL4||$objRights->APPROVAL5) == 1 ? '' : 'disabled'}} ><i class="fa fa-lock"></i> Approved</button>
      <button class="btn topnavbt" disabled="disabled" ><i class="fa fa-link" ></i> Attachment</button>
      <button class="btn topnavbt" id="btnExit"><i class="fa fa-power-off"></i> Exit</button>
    </div>
  </div>
</div>
   
<div class="container-fluid purchase-order-view filter">     
  <form id="frm_mst_edit" method="POST" onsubmit="return validateForm()" class="needs-validation"  > 
    @CSRF
    <div class="inner-form">
      <div class="row">
        <div class="col-lg-2 pl"><p>Date of Opening Balance</p></div>
        <div class="col-lg-2 pl">
          <input type="date" name="DOOB" id="DOOB" onchange="getOpendingData();" value="{{isset($OpeningDate) && $OpeningDate !=''?$OpeningDate:date('Y-m-d')}}" <?php echo isset($OpeningDate) && $OpeningDate !=''?'readonly':'';?>  class="form-control mandatory"  placeholder="dd-mm-yyyy" >                          
          <input type="hidden" name="user_approval_level" id="user_approval_level" value="{{ $user_approval_level }}"  />
        </div>
      </div>   
            
      <div class="row">
        <div class="col-lg-12 pl">
          <div class=" table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:400px;"  id="load_scroll" >
            <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
              <thead id="thead1"  style="position: sticky;top: 0">
                <tr>
                  <th colspan="4"></th>
                  <th colspan="2">Opening Balance</th>
                  <th colspan="2">Transaction Balance</th>
                  <th colspan="2">Closing Balance</th>
                </tr>  
                <tr>
                  <th>
                    SL Code      
                    <input type="hidden" id="focusid" >
                    <input type="hidden" id="errorid" >
                  </th>
                  <th>SL Name</th>
                  <th>GL Code</th>
                  <th>GL Name</th>
                  <th  hidden>OLD Debit</th>
                  <th>Debit Balance</th>
                  <th  hidden>OLD Credit</th>
                  <th>Credit Balance</th>
                  <th>Debit Balance</th>
                  <th>Credit Balance</th>
                  <th hidden>OLD Debit Balance</th>
                  <th>Debit Balance</th>
                  <th hidden>OLD Credit Balance</th>
                  <th>Credit Balance</th>
                </tr>
              </thead>
              <tbody id="load_data" >
                            
              </tbody>
            </table>
          </div>
          <div id="load_data_message"></div>
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
            <button class="btn alertbt" name='YesBtn' id="YesBtn" data-funcname="fnSaveData">
              <div id="alert-active" class="activeYes"></div>Yes
            </button>
            <button class="btn alertbt" name='NoBtn' id="NoBtn"   data-funcname="fnUndoNo" >
              <div id="alert-active" class="activeNo"></div>No
            </button>
            <button onclick="setfocus();" class="btn alertbt" name='OkBtn' id="OkBtn" style="display:none;margin-left: 90px;">
              <div id="alert-active" class="activeOk"></div>OK</button>

            <button class="btn alertbt" name='OkBtn1' id="OkBtn1" style="display:none;margin-left: 90px;">
                <div id="alert-active" class="activeOk1"></div>OK</button>

            <button class="btn alertbt" name='OkBtn2' id="OkBtn2" style="display:none;margin-left: 90px;">
                <div id="alert-active" class="activeOk2"></div>OK</button>
        </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('bottom-scripts')
<script>
var limit   = 10;
var start   = 0;
var indexno = 0;
var action  = 'inactive';

function load_country_data(limit, start,indexno){

  var DOOB    = $("#DOOB").val();

  $.ajaxSetup({
    headers:{
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $.ajax({
    url:'{{route("master",[219,"listing"])}}',
    method:"POST",
    data:{limit:limit, start:start,indexno:indexno,DOOB:DOOB,'OpeningStatus':'<?php echo $OpeningStatus;?>'},
    cache:false,
    success:function(data){
      $('.dataTables_empty').remove();
      $('#load_data_message').html('');
      $('#load_data').append(data);
     
      if(data == ''){
        $('#load_data_message').html("<br/><div>No Data Found</div>");
        action = 'active';
      }
      else{
        action = 'inactive';
      } 
      
    }
  });
}

function getOpendingData(){
  load_country_data(limit,start,indexno);
}

if(action == 'inactive'){
  action = 'active';
  
  <?php if($TotalData > 0){?> 
    load_country_data(<?php echo $TotalData;?>, start,indexno);
  <?php }else{?> 
    load_country_data(limit, start,indexno);
  <?php }?>
}

$("#load_scroll").scroll(function(){

  if($("#load_scroll").scrollTop() + $("#load_scroll").height() > $("#load_data").height() && action == 'inactive'){
    action  = 'active';
    start   = start + limit;
    indexno = $("#example2 .participantRow").length;

    setTimeout(function(){
      $('#load_data_message').html("<div>Please wait...</div>");
      load_country_data(limit, start,indexno);
    }, 1000);
  }
});

function setfocus(){
    var focusid=$("#focusid").val();
    $("#"+focusid).focus();
    $("#closePopup").click();
} 

function validateForm(UserAction){

  $("#OkBtn1").hide();
  $("#OkBtn2").hide();
  $("#focusid").val('');
    var DOOB  = $.trim($("#DOOB").val());
    var txtcostcode  =   $.trim($("[id*=txtcostcode]").val());
    var txtdesc     =   $.trim($("[id*=txtdesc]").val());

    if(DOOB ===""){
        $("#focusid").val('DOOB');
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").show();
        $("#AlertMessage").text('Please select Date of Opening Balance.');
        $("#alert").modal('show');
        $("#OkBtn").focus();
        highlighFocusBtn('activeOk');
        return false;
    }
    else{
        event.preventDefault();
        var ExistArray = []; 
        var allblank1 = [];  
        var allblank2 = []; 
        var allblank3 = []; 
        var allblank4 = []; 

        var texid1    = "";
        var texid2    = ""; 
        var texid3    = "";

        $("[id*=HDN_SGLDRBALANCE]").each(function(){
 
            if($.trim($(this).val()) ==="" ){
              allblank1.push('true');
              texid1 = $(this).attr('id');
            }else{
              allblank1.push('false');
            }


            if($.trim($(this).parent().parent().find('[id*="HDN_SGLCRBALANCE"]').val()) === "" ){
              allblank2.push('true');
              texid2 = $(this).parent().parent().find('[id*="HDN_SGLCRBALANCE"]').attr('id');
            }else{
              allblank2.push('false');
            }

            if( $.trim($(this).val())>'0.00' && $.trim($(this).parent().parent().find('[id*="HDN_SGLCRBALANCE"]').val())>'0.00'){
              allblank4.push('true');
            }else{
              allblank4.push('false');
            }

        });

        if(jQuery.inArray("true", allblank1) !== -1){
            $("#focusid").val(texid1);
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn").show();
            $("#AlertMessage").text('Please enter Opening Debit Balance.');
            $("#alert").modal('show');
            $("#OkBtn").focus();
            highlighFocusBtn('activeOk');
            return false;
          }
          else if(jQuery.inArray("true", allblank2) !== -1){
            $("#focusid").val(texid2);
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn").show();
            $("#AlertMessage").text('Please enter Opening Credit Balance.');
            $("#alert").modal('show');
            $("#OkBtn").focus();
            highlighFocusBtn('activeOk');
            return false;
          }
          else if(jQuery.inArray("true", allblank4) !== -1){
            $("#focusid").val(texid2);
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn").show();
            $("#AlertMessage").text('Please cheeck. Sub Leader can have either Opening Debit Balance OR Opening Credit Balance.');
            $("#alert").modal('show');
            $("#OkBtn").focus();
            highlighFocusBtn('activeOk');
            return false;
          }
          else{
              $("#alert").modal('show');
              $("#AlertMessage").text('Do you want to save to record.');
              $("#YesBtn").data("funcname",UserAction);  
              $("#YesBtn").focus();
              highlighFocusBtn('activeYes');
          }

    }
  
}

$('#example2').on('focusout',"[id*='HDN_SGLDRBALANCE']",function()
{
        var ope_debit = $.trim($(this).val());  

        if(ope_debit=="" || isNaN(ope_debit)){
            ope_debit=0.00;
        }

        if(intRegex.test(ope_debit)){
          $(this).val(ope_debit+'.00')
        }

      
        
        var old_ope_debit = $(this).parent().parent().find('[id*="OLDSGLDRBALANCE"]').val();
        
       
        var oldTotal = $(this).parent().parent().find('[id*="IDOLD_CLO_DBT_BAL"]').val();

        var  total_dbt = parseFloat(parseFloat(oldTotal)- parseFloat(old_ope_debit) ).toFixed(2);

        
        var  newtotal_dbt = parseFloat( parseFloat(total_dbt)+parseFloat(ope_debit) ).toFixed(2);
      
        $(this).parent().parent().find('[id*="HDN_SGLDR_CLOSING"]').val(newtotal_dbt);
            
}); 

$('#example2').on('focusout',"[id*='HDN_SGLCRBALANCE']",function()
{
        var ope_credit = $.trim($(this).val()); 

        if(ope_credit=="" || isNaN(ope_credit)){
            ope_credit=0.00;
        }

        if(intRegex.test(ope_credit)){
          $(this).val(ope_credit+'.00')
        }

      

        var old_ope_credit = $(this).parent().parent().find('[id*="OLDSGLCRBALANCE"]').val();

      
        var oldTotal2 = $(this).parent().parent().find('[id*="IDOLD_CLO_CR_BAL"]').val();
        
        var total_cr = parseFloat(parseFloat(oldTotal2)- parseFloat(old_ope_credit) ).toFixed(2);

       
        var newtotal_cr = parseFloat(parseFloat(total_cr) + parseFloat(ope_credit) ).toFixed(2);


        $(this).parent().parent().find('[id*="HDN_SGLCR_CLOSING"]').val(newtotal_cr);        
      
}); 

$(document).ready(function(e) {

    $("[id*='HDN_SGLDRBALANCE']").ForceNumericOnly();
    $("[id*='HDN_SGLCRBALANCE']").ForceNumericOnly();



});


  $('#btnAdd').on('click', function() {
      var viewURL = '{{route("master",[219,"add"])}}';
      window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });

  $("#OkBtn2").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn2").hide();
    window.location.href = '{{route("master",[219,"index"]) }}';
});

 var formDataMst = $( "#frm_mst_edit" );
     formDataMst.validate();

   
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

    });

    
window.fnSaveData = function (){
    
  $("#OkBtn2").hide();
    event.preventDefault();

    var getDataForm = $("#frm_mst_edit");
    var formData = getDataForm.serialize();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url:'{{route("mastermodify",[219,"update"])}}',
        type:'POST',
        data:formData,
        success:function(data) {
            
            if(data.errors) {
                $(".text-danger").hide();

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
                $("#OkBtn").hide();

                $("#AlertMessage").text(data.msg);
                $(".text-danger").hide();
                $("#alert").modal('show');
                $("#OkBtn2").show();
                $("#OkBtn2").focus();
                
            }
            
        },
        error:function(data){
          console.log("Error: Something went wrong.");
        },
    });

};


window.fnApproveData = function (){
    
    $("#OkBtn2").hide();
    event.preventDefault();

    var getDataForm = $("#frm_mst_edit");
    var formData = getDataForm.serialize();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url:'{{route("mastermodify",[219,"singleapprove"])}}',
        type:'POST',
        data:formData,
        success:function(data) {
            
            if(data.errors) {
                $(".text-danger").hide();

                if(data.exist=='norecord') {
                  $("#errorid").val('1'); 
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").show();

                  $("#AlertMessage").text(data.msg);

                  $("#alert").modal('show');
                  $("#OkBtn").focus();
                  $("#OkBtn2").hide();

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
                $("#OkBtn1").hide();

                $("#AlertMessage").text(data.msg);

                $(".text-danger").hide();
                
                $("#alert").modal('show');
                $("#OkBtn2").show();
                $("#OkBtn2").focus();

            }
            
        },
        error:function(data){
          console.log("Error: Something went wrong.");
        },
    });

};

    
$("#NoBtn").click(function(){

  $("#alert").modal('hide');
    var custFnName = $("#NoBtn").data("funcname");
    window[custFnName]();

});

$("#OkBtn").click(function(){

  $("#alert").modal('hide');
  $("#YesBtn").show();
  $("#NoBtn").show();
  $("#OkBtn").hide();
  $("#OkBtn2").hide();
  $(".text-danger").hide();
});

$("#OkBtn1").click(function(){

  $("#alert").modal('hide');
  $("#YesBtn").show();
  $("#NoBtn").show();
  $("#OkBtn").hide();
  $("#OkBtn1").hide();
  $("#OkBtn2").hide();

});

$("#btnUndo").click(function(){

    $("#AlertMessage").text("Do you want to erase entered information in this record?");
    $("#alert").modal('show');

    $("#YesBtn").data("funcname","fnUndoYes");
    $("#YesBtn").show();

    $("#NoBtn").data("funcname","fnUndoNo");
    $("#NoBtn").show();

    $("#OkBtn").hide();
    
    $("#OkBtn1").hide();
    $("#OkBtn2").hide();

    $("#NoBtn").focus();
    highlighFocusBtn('activeNo');

});

window.fnUndoYes = function (){
  window.location.reload();
}

window.fnUndoNo = function (){

}

function showError(pId,pVal){
  $("#"+pId+"").text(pVal);
  $("#"+pId+"").show();
}  

function highlighFocusBtn(pclass){
  $(".activeYes").hide();
  $(".activeNo").hide();
  
  $("."+pclass+"").show();
}

$('#example2').on('change',"[id*='CHKDEACTIVATED']",function(){
  
  if ($(this).is(":checked") == false){
    $(this).parent().parent().find('[id*="DODEACTIVATED"]').val('');
  }
  event.preventDefault();
});

$(document).ready(function(e){
  var today   = new Date(); 
  var sodate  = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2);
  $('#DOOB').attr('max',sodate);
});

function getDebitBal(id,value){

  var ROW_ID = id.split('_').pop();

  if(value > 0){
    $("#HDN_SGLCRBALANCE_"+ROW_ID).prop("disabled", true);
  }
  else{
    $("#HDN_SGLCRBALANCE_"+ROW_ID).prop("disabled", false);
  }

}
</script>
@endpush