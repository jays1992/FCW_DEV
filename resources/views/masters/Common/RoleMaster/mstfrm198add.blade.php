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
                        <button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-lock"></i>  Approved</button>
                        <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                        <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                </div>
            </div><!--row-->
    </div>
    <form id="frm_trn_se" onsubmit="return validateForm()"  method="POST" class="needs-validation"  >
    <div class="container-fluid filter">

	<div class="inner-form">
	
		<div class="row">
			<div class="col-lg-1 pl"><p>Role Code</p></div>
			<div class="col-lg-1 pl">
   
      {{-- @if($MAX_ROLE != "")
                <input type="text" name="RCODE" id="RCODE" value="{{ $MAX_ROLE }}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase" autofocus >
            @endif --}}
            @if(!empty($objDD))
                @if($objDD->SYSTEM_GRSR == "1")
                    <input type="text" name="RCODE" id="RCODE" value="{{ $objDOCNO }}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase" autofocus >
                @endif
                @if($objDD->MANUAL_SR == "1")
                    <input type="text" name="RCODE" id="RCODE" value="{{ old('RCODE') }}" class="form-control mandatory" minlength="3" maxlength="{{$objDD->MANUAL_MAXLENGTH}}" tabindex="1" autocomplete="off" style="text-transform:uppercase" autofocus >
                @endif
            @endif  
			</div>
			
			<div class="col-lg-1 pl col-md-offset-1"><p>Description</p></div>
			<div class="col-lg-3 pl pr">   
                <input type="text" name="DESCRIPTIONS" id="DESCRIPTIONS" class="form-control" autocomplete="off" />
                <span class="text-danger" id="ERROR_DESCRIPTIONS"></span> 

            </div>
			</div>
		</div>
   
      <div class="row">
        <div class="col-lg-1">
          <p>Module Name </p> 
        </div>  
        <div class="col-lg-11">
          <ul >
              @foreach ($ModuleList as $mod_row)
              <li style="list-style: none; padding: 6px 0; display: inline-table;padding-right: 15px;"><input style="margin-right: 5px;" type="checkbox" name="MODULE_NAME_{{$mod_row->MODULEID_REF}}"  id="MODULE_NAME_{{$mod_row->MODULEID_REF}}" value="{{$mod_row->MODULEID_REF}}">{{$mod_row->MODULENAME}} </li>     
              @endforeach
          </ul> 
        </div>       
      </div>  
      		
	</div>

	<div class="container-fluid">

		<div class="row">
      <div class="tab-content">
                                <div id="Material" class="tab-pane fade in active">
                                    <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:450px;margin-top:10px;" >
                                        <table id="roleTbl" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                                            <thead id="thead1"  style="position: sticky;top: 0">
                                                    <tr>
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
                                            </thead>
                                            <tbody>
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

     
$(document).ready(function(e) {
  var Material = $("#Material").html(); 
  $('#hdnmaterial').val(Material);

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
  window.location.href = "{{route('master',[198,'add'])}}";
}//fnUndoYes


window.fnUndoNo = function (){
    $("#AFSNO").focus();
}//fnUndoNo

});
</script>

@endpush

@push('bottom-scripts')
<script>

$(document).ready(function() {

  



  $("#btnSaveSE").on("submit", function( event ) {

    if ($("#frm_trn_se").valid()) {

        // Do something
        alert( "Handler for .submit() called." );
        event.preventDefault();
    }
});


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


 if(RCODE ===""){
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
          highlighFocusBtn('activeOk1');
          }
          else{
                checkDuplicateCode();
                // $("#alert").modal('show');
                // $("#AlertMessage").text('Do you want to save to record.');
                // $("#YesBtn").data("funcname","fnSaveData");  //set dynamic fucntion name
                // $("#YesBtn").focus();
                // $("#OkBtn").hide();
                // highlighFocusBtn('activeYes');
          }

}

$("#YesBtn").click(function(){

$("#alert").modal('hide');
var customFnName = $("#YesBtn").data("funcname");
    window[customFnName]();

}); //yes button

$("#btnSaveSE" ).click(function() {
    var formReqData = $("#frm_trn_se");
    if(formReqData.valid()){
      validateForm();
    }
});

window.fnSaveData = function (){

//validate and save data
event.preventDefault();

     var trnseForm = $("#frm_trn_se");
    var formData = trnseForm.serialize();
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$.ajax({
    url:'{{ route("master",[198,"save"])}}',
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
                        $("#AlertMessage").text('Please enter correct value in Label.');
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
            // $("#frm_mst_country").trigger("reset");

            $("#alert").modal('show');
            $("#OkBtn").focus();
            // window.location.href="{{ route('master',[90,'index']) }}";
        }
        else if(data.cancel) {                   
            console.log("cancel MSG="+data.msg);
            
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").show();

            $("#AlertMessage").text(data.msg);

            $(".text-danger").hide();
            // $("#frm_mst_country").trigger("reset");

            $("#alert").modal('show');
            $("#OkBtn1").focus();
            // window.location.href="{{ route('master',[90,'index']) }}";
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
function showAlert(msg){
  $("#alert").modal('show');
  $("#AlertMessage").text(msg);
  $("#YesBtn").hide(); 
  $("#NoBtn").hide();  
  $("#OkBtn1").show();
  $("#OkBtn1").focus();
  highlighFocusBtn('activeOk');
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

      function checkDuplicateCode(){

        var RCODE = $.trim($("#RCODE").val());       

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url:'{{route("master",[198,"codeduplicate"])}}',
            type:'POST',
            data:{'RCODE':RCODE},
            success:function(data) {
                if(data.exists) {
                    $(".text-danger").hide();
                    $("#FocusId").val($("#RCODE"));
                    $("#ProceedBtn").focus();
                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn1").show();
                    $("#AlertMessage").text('Duplicate Role Code.');
                    $("#alert").modal('show');
                    $("#OkBtn1").focus();
                    return false;
                }
                else{
                  $("#alert").modal('show');
                  $("#AlertMessage").text('Do you want to save to record.');
                  $("#YesBtn").data("funcname","fnSaveData");  //set dynamic fucntion name
                  $("#YesBtn").focus();
                  $("#YesBtn").show();
                  $("#NoBtn").show();
                  $("#OkBtn").hide();
                  $("#OkBtn1").hide();
                  highlighFocusBtn('activeYes');
                }                                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });
}

window.onload = function(){
    var strdd = <?php echo json_encode($objDD); ?>;
    if($.trim(strdd)==""){     
      $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please contact to administrator for creating document numbering.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        highlighFocusBtn('activeOk');
    } 
  };

  $('input[type=checkbox][id*="MODULE_NAME_"]').change(function() {
      if ($(this).prop("checked")) {
        loadsVouchersList($(this).val());
      }
      else {
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