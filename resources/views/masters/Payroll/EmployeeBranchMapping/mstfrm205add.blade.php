@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
  <div class="row">
      <div class="col-lg-2"><a href="{{route('master',[$FormId,'index'])}}" class="btn singlebt">Employee-Branch Mapping</a></div>
      <div class="col-lg-10 topnav-pd">
        <button class="btn topnavbt" id="btnAdd" disabled="disabled" ><i class="fa fa-plus"></i> Add</button>
        <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
        <button class="btn topnavbt" id="btnSave"  tabindex="3"  ><i class="fa fa-save"></i> Save</button>
        <button class="btn topnavbt" id="btnView" disabled="disabled" ><i class="fa fa-eye"></i> View</button>
        <button class="btn topnavbt" disabled="disabled"><i class="fa fa-print"></i> Print</button>
        <button class="btn topnavbt" id='btnUndo' ><i class="fa fa-undo"></i> Undo</button>
        <button class="btn topnavbt" id="btnCancel"  disabled="disabled" ><i class="fa fa-times"></i> Cancel</button>
        <button class="btn topnavbt" id="btnApprove"  disabled="disabled" ><i class="fa fa-lock"></i> Approved</button>
        <button class="btn topnavbt"  id="btnAttach"  disabled="disabled" ><i class="fa fa-link"></i> Attachment</button>
        <button class="btn topnavbt" id="btnExit"><i class="fa fa-power-off"></i> Exit</button>
      </div>
  </div>
</div>
   
<div class="container-fluid purchase-order-view filter">     
  <form id="frm_mst_add" method="POST" enctype="multipart/form-data" > 
    @CSRF
    <div class="inner-form">

      <div class="row">    
        <div class="col-lg-1 pl"><p>Doc No</p></div>
        <div class="col-lg-2 pl">
          {{-- <input type="text" name="DOC_NO" id="DOC_NO" class="form-control" required autocomplete="off" maxlength="15" style="text-transform:uppercase" onkeypress="return AlphaNumaric(event,this)" /> --}}
          @if(!empty($objDD))
              @if($objDD->SYSTEM_GRSR == "1")
                  <input type="text" name="DOC_NO" id="DOC_NO" value="{{ $objDOCNO }}" class="form-control mandatory" tabindex="1" maxlength="10" autocomplete="off" readonly style="text-transform:uppercase" autofocus >
              @endif
              @if($objDD->MANUAL_SR == "1")
                  <input type="text" name="DOC_NO" id="DOC_NO" value="{{ old('DOC_NO') }}" class="form-control mandatory"  maxlength="{{$objDD->MANUAL_MAXLENGTH}}" tabindex="1" autocomplete="off" style="text-transform:uppercase" autofocus >
              @endif
          @endif  
          <span class="text-danger" id="ERROR_DOC_NO"></span> 
        </div>
		
        <div class="col-lg-1 pl"><p>Date</p></div>
        <div class="col-lg-2 pl">
        <input type="date" name="DOC_DT" id="DOC_DT" value="{{date('Y-m-d')}}" class="form-control" autocomplete="off" required />
          <span class="text-danger" id="ERROR_DOC_DT"></span> 
        </div>
      </div>

      <div class="row">    

        <div class="col-lg-1 pl"><p>Branch</p></div>
        <div class="col-lg-2 pl">
          <input type="hidden" name="MAPBRID_REF" id="MAPBRID_REF" value="{{isset($getBranch->FID) && $getBranch->FID !=''?$getBranch->FID:''}}" class="form-control" required >
          <input type="text"  value="{{isset($getBranch->FID) && $getBranch->FID !=''?$getBranch->BRCODE.'-'.$getBranch->BRNAME:''}}" class="form-control" required readonly >
          <span class="text-danger" id="ERROR_MAPBRID_REF"></span>
        </div>

        <div class="col-lg-1 pl"><p>Branch Group</p></div>
        <div class="col-lg-2 pl">
        <input type="text" name="BRANCH_GROUP_NAME" id="BRANCH_GROUP_NAME" class="form-control" readonly  />
          <span class="text-danger" id="ERROR_BRANCH_GROUP_NAME"></span> 
        </div>

        <div class="col-lg-1 pl"><p>Company</p></div>
        <div class="col-lg-2 pl">
        <input type="text" name="COMPANY_NAME" id="COMPANY_NAME" class="form-control" readonly />
          <span class="text-danger" id="ERROR_COMPANY_NAME"></span> 
        </div>
      </div>

      <div class="row">
        <div class="col-lg-8 pl">
          <div class=" table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:300px;" >
            <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
              <thead id="thead1"  style="position: sticky;top: 0">
                <tr>
                <th hidden><input class="form-control" type="hidden" name="Row_Count" id ="Row_Count"> </th>
                <th hidden><input type="hidden" id="focusid" ></th>
                <th><input type="checkbox" id="select_all" >All</th>
                <th>Employee Code</th>
                <th>Employee Name</th>
                <th>De-Activated</th>
                <th>Date of De-Activated</th>
                </tr>
              </thead>
              <tbody>
                @if(!empty($getCustomer))
                @foreach($getCustomer as $key=>$row)
                <tr class="participantRow">
                    <td><input type="checkbox" name="DATA_ID[{{$key}}]" value="{{$row->DATA_ID}}" class="checkbox" ></td>
                    <td><input type="text" name="DATA_CODE_{{$key}}" id="DATA_CODE_{{$key}}" value="{{$row->DATA_CODE}}" class="form-control showEmp" readonly  style="width:100%;"  /></td>
                    <td><input  type="text" id ="DATA_DESCRIPTION_{{$key}}"  id ="DATA_DESCRIPTION_{{$key}}" value="{{$row->DATA_DESCRIPTION}}" class="form-control w-100" maxlength="200" readonly style="width:100%;" ></td>
                    <td><input  class="" type="checkbox" name="DEACTIVATED_{{$key}}" id ="DEACTIVATED_{{$key}}" value="1" onclick="DateEnableDisabled('{{$key}}')"  autocomplete="off" style="width:100%;" disabled ></td>
                    <td><input  class="form-control w-100" type="date" name="DODEACTIVATED_{{$key}}" id ="DODEACTIVATED_{{$key}}"  autocomplete="off" style="width:100%;" disabled  ></td>
                </tr>
                @endforeach
                @endif

              </tbody>
            </table>
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
            <button class="btn alertbt" name='OkBtn1' id="OkBtn1" style="display:none;margin-left: 90px;"><div id="alert-active" class="activeOk1"></div>OK</button>
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
$('#btnAdd').on('click', function() {
    var viewURL = '{{route("master",[$FormId,"add"])}}';
    window.location.href=viewURL;
});

$('#btnExit').on('click', function() {
  var viewURL = '{{route('home')}}';
  window.location.href=viewURL;
});

$("#YesBtn").click(function(){
    $("#alert").modal('hide');
    var customFnName = $("#YesBtn").data("funcname");
    window[customFnName]();
}); 

$("#btnSave" ).click(function() {
    var formReqData = $("#frm_mst_add");
    if(formReqData.valid()){

      var DOC_NO          =   $.trim($("#DOC_NO").val());
      if(DOC_NO ===""){
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").hide();  
        $("#OkBtn1").show();              
        $("#AlertMessage").text('Please enter Doc No.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
      }
      checkDuplicateCode1()
    }
});

$("#btnUndo").on("click", function(){
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
    $("#LABEL").focus();
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

//------------------------FORM VALIDATION------------------------//

var formResponseMst = $( "#frm_mst_add" );
formResponseMst.validate();

$("#DOC_NO").blur(function(){
  $(this).val($.trim( $(this).val() ));
  $("#ERROR_DOC_NO").hide();
  validateSingleElemnet("DOC_NO");
      
});


$( "#DOC_NO" ).rules( "add", {
    required: true,
    nowhitespace: true,
    //StringNumberRegex: true,
    messages: {
        required: "Required field.",
    }
});

$("#DOC_DT").blur(function(){
    $(this).val($.trim( $(this).val() ));
    $("#ERROR_DOC_DT").hide();
    validateSingleElemnet("DOC_DT");
});

$( "#DOC_DT" ).rules( "add", {
    required: true,
    LessDate: true,
    normalizer: function(value) {
        return $.trim(value);
    },
    messages: {
        required: "Required field."
    }
});

$("#MAPBRID_REF").blur(function(){
    $(this).val($.trim( $(this).val() ));
    $("#ERROR_MAPBRID_REF").hide();
    validateSingleElemnet("MAPBRID_REF");
});

$( "#MAPBRID_REF" ).rules( "add", {
    required: true,
    normalizer: function(value) {
        return $.trim(value);
    },
    messages: {
        required: "Required field."
    }
});


function validateSingleElemnet(element_id){
  var validator =$("#frm_mst_add" ).validate();
  if(validator.element( "#"+element_id+"" )){
    checkDuplicateCode();
  }
}

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
              $(".text-danger").hide();
              showError('ERROR_DOC_NO',data.msg);
              $("#DOC_NO").focus();
          }                         
      },
      error:function(data){
        console.log("Error: Something went wrong.");
      },
  });
}

function checkDuplicateCode1(){
  var getDataForm = $("#frm_mst_add");
  var formData = getDataForm.serialize();
  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });
  $.ajax({
      url:'{{route("master",[$FormId,"codeduplicate1"])}}',
      type:'POST',
      data:formData,
      success:function(data) {
          if(data.exists) {
              $(".text-danger").hide();
              showError('ERROR_MAPBRID_REF',data.msg);
          } 
          else{
            validateForm('fnSaveData','save');
          }                            
      },
      error:function(data){
        console.log("Error: Something went wrong.");
      },
  });
}

function validateForm(ActionType,ActionMsg){

  var DOC_NO      = $.trim($("#DOC_NO").val());
  var DOC_DT      = $.trim($("#DOC_DT").val());
  var MAPBRID_REF = $.trim($("#MAPBRID_REF").val());
  var CheckLength = $('.checkbox:checked').length;

  if(DOC_NO ===""){
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please enter Doc No.');
      $("#alert").modal('show')
      $("#OkBtn1").focus();
  }
  else if(DOC_DT ===""){
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please select Date.');
      $("#alert").modal('show')
      $("#OkBtn1").focus();
  }
  else if(MAPBRID_REF ===""){
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please select Branch.');
      $("#alert").modal('show')
      $("#OkBtn1").focus();
  }
  else if(CheckLength =="0"){
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please select Customer.');
      $("#alert").modal('show')
      $("#OkBtn1").focus();
  }
  else{
    $("#alert").modal('show');
    $("#AlertMessage").text('Do you want to '+ActionMsg+' to record.');
    $("#YesBtn").data("funcname",ActionType);
    $("#YesBtn").focus();
    $("#OkBtn").hide();
    highlighFocusBtn('activeYes');
  }
}

//------------------------SAVE FUNCTION------------------------//

window.fnSaveData = function (){
event.preventDefault();

    var formData = new FormData($("#frm_mst_add")[0]);
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url:'{{route("master",[$FormId,"save"])}}',
        type:'POST',
        enctype: 'multipart/form-data',
        contentType: false,     
        cache: false,           
        processData:false, 
        data:formData,
        success:function(data) {
            
            if(data.errors) {
                $(".text-danger").hide();

                if(data.errors.DOC_NO){
                    //showError('ERROR_DOC_NO',data.errors.DOC_NO);
                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn").hide();
                    $("#OkBtn1").show();
                    $("#AlertMessage").text("Doc No is "+data.errors.DOC_NO);
                    $("#alert").modal('show');
                    $("#OkBtn1").focus();
                    
                }
                if(data.errors.DOC_DT){
                    //showError('ERROR_DOC_DT',data.errors.DOC_DT);
                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn").hide();
                    $("#OkBtn1").show();
                    $("#AlertMessage").text("Doc No is "+data.errors.DOC_DT);
                    $("#alert").modal('show');
                    $("#OkBtn1").focus();
                }
                if(data.exist=='duplicate') {

                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").hide();
                  $("#OkBtn1").show();
                  $("#AlertMessage").text(data.msg);
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();

                }
                if(data.save=='invalid') {

                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").hide();
                  $("#OkBtn1").show();
                  $("#AlertMessage").text(data.msg);
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();

                }
            }
            if(data.success) {                   
              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn1").hide();
              $("#OkBtn").show();
              $("#AlertMessage").text(data.msg);
              $(".text-danger").hide();
              $("#alert").modal('show');
              $("#OkBtn").focus();
            }
            
        },
        error:function(data){
         // console.log("Error: Something went wrong.");
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

//------------------------USER DEFINE FUNCTION------------------------//

function selectBranch(MAPBRID_REF){
  
  $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
		
    $.ajax({
        url:'{{route("master",[$FormId,"getBranchCompanyName"])}}',
        type:'POST',
        data:{MAPBRID_REF:MAPBRID_REF},
        success:function(data) {
          $("#BRANCH_GROUP_NAME").val(data.branch);
          $("#COMPANY_NAME").val(data.company);
        },
        error:function(data){
          console.log("Error: Something went wrong.");
          $("#BRANCH_GROUP_NAME").val('');
          $("#COMPANY_NAME").val('');
        },
    });	
}


$(document).ready(function(){
    $("#select_all").change(function(){  //"select all" change
        $(".checkbox").prop('checked', $(this).prop("checked")); //change all ".checkbox" checked status
    });

    //".checkbox" change
    $('.checkbox').change(function(){
        //uncheck "select all", if one of the listed checkbox item is unchecked
        if(false == $(this).prop("checked")){ //if this item is unchecked
            $("#select_all").prop('checked', false); //change "select all" checked status to false
        }
        //check "select all" if all checkbox items are checked
        if ($('.checkbox:checked').length == $('.checkbox').length ){
            $("#select_all").prop('checked', true);
        }
    });
});

$.validator.addMethod("LessDate", function(value, element) {
  var today = new Date(); 
  var d = new Date(value); 
  today.setHours(0, 0, 0, 0) ;
  d.setHours(0, 0, 0, 0) ;

  if(this.optional(element) || d < today){
      return false;
  }
  else {
      return true;
  }
}, "Less date not allow");

function DateEnableDisabled(id){
  $('input[type=checkbox][name=DEACTIVATED_'+id+']').change(function() {
		if ($(this).prop("checked")) {
		  $(this).val('1');
		  $('#DODEACTIVATED_'+id).removeAttr('disabled');
		}
		else {
		  $(this).val('0');
      $("input").prop('required',true);
		  $('#DODEACTIVATED_'+id).prop('disabled', true);
		  $('#DODEACTIVATED_'+id).val('');
		  
		}
	});
}

$(document).ready(function(){
  var MAPBRID_REF="{{isset($getBranch->FID) && $getBranch->FID !=''?$getBranch->FID:''}}";
  selectBranch(MAPBRID_REF);

  window.onload = function(){
      var strdd = <?php echo json_encode($objDD); ?>;
      if($.trim(strdd)==""){     
        $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn1").hide();
          $("#OkBtn1").show();
          $("#AlertMessage").text('Please contact to administrator for creating document numbering.');
          $("#alert").modal('show');
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
      } 
    };

});
</script>

@endpush