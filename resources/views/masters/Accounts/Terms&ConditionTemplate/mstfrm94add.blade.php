@extends('layouts.app')
@section('content')

  <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[94,'index'])}}" class="btn singlebt">Terms & Condition Template</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
                        <button class="btn topnavbt" id="btnSave" ><i class="fa fa-floppy-o"></i> Save</button>
                        <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt" id="btnUndo"  ><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-thumbs-o-up"></i> Approved</button>
                        <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                        <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                </div><!--col-10-->

            </div><!--row-->
    </div><!--topnav-->	
   
<div class="container-fluid purchase-order-view filter">     
    <form id="frm_mst_condition" method="POST"  > 
          @CSRF
    <div class="inner-form">
              
    <div class="row">
			<div class="col-lg-2 pl"><p>TnC Template Code</p></div>
			<div class="col-lg-2 pl">
				<div class="col-lg-12 pl">
					<input type="text" name="TNC_CODE" id="txttnccode" class="form-control mandatory" autocomplete="off"  maxlength="50" style="text-transform:uppercase" >
          <span class="text-danger" id="ERROR_TNC_CODE"></span>
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-lg-2 pl"><p>TnC Template Description</p></div>
			<div class="col-lg-5 pl">
				<input type="text" name="TNC_DESC" id="txttncdesc" class="form-control"  maxlength="200" autocomplete="off"  >
			</div>
		</div>	
		
		<div class="row">
			<div class="col-lg-2 pl"><p>For Sale</p></div>
			<div class="col-lg-1 pl">
				<input type="checkbox" name="FOR_SALE" id="chkforsale"  >
			</div>
			
			<div class="col-lg-1">OR</div>
			
			<div class="col-lg-2 pl"><p>For Purchase</p></div>
			<div class="col-lg-1 pl">
				<input type="checkbox" name="FOR_PURCHASE" id="chkforpurchase">
			</div>
		</div>
		
		<div class="row">
			<div class="col-lg-2 pl"><p>De-Activated</p></div>
			<div class="col-lg-1 pl">
				<input type="checkbox" name="DE_ACTIVATED" value="" id="deactive" disabled>
			</div>
			
			<div class="col-lg-2 pl col-md-offset-1"><p>Date of De-Activated</p></div>
			<div class="col-lg-2 pl">
			<div class="col-lg-8 pl">
			<input type="text" name="DO_DEACTIVATED" id="decativated_date" class="form-control datepicker" placeholder="dd/mm/yyyy" autocomplete="off" disabled>
			</div>
			</div>
		</div>
		<div class="row">
			<div class="table-responsive table-wrapper-scroll-y " style="height:330px;margin-top:10px;">
			<table id="example2" class="display nowrap table table-striped table-bordered itemlist itemlistscroll w-150" style="height:auto !important;">
				<thead id="thead1" style="position: sticky;top: 0; white-space:none;">
					  <tr>
						<th width="27%" style="vertical-align:middle; font-family: 'Roboto',sans-serif; font-size:11px; font-weight: 600;">TNC Name <input class="form-control" type="hidden" name="Row_Count" id ="Row_Count">  </th>
						<th width="16%"  style="vertical-align:middle; font-size:11px; font-family: 'Roboto',sans-serif;">Value Type</th>
						<th width="51%" style="vertical-align:middle; font-size:11px; font-family: 'Roboto',sans-serif;">Description</th>
						<th width="16%" style="vertical-align:middle; font-size:11px; font-family: 'Roboto',sans-serif;">Is Mandatory</th>
						<th width="16%"  style="vertical-align:middle; font-size:11px; font-family: 'Roboto',sans-serif;"> De-Activated</th>
						<th width="16%"  style="vertical-align:middle; font-size:11px; font-family: 'Roboto',sans-serif;">Date of De-Activated</th>
						<th width="10%" style="vertical-align:middle; font-size:11px; font-family: 'Roboto',sans-serif;">Action</th>
					  </tr>
					</thead>
					<tbody>
						<tr  class="participantRow">
							<td style="width:27%;"><input  class="form-control w-100" type="text" name="TNC_NAME_0" id="txttncname_0" maxlength="200" autocomplete="off" style="text-transform:uppercase"></td>
							<td style="width:16%;"><select class="form-control selvt" name="VALUE_TYPE_0" id="drpvalue_0" >
								<option value="" selected>Select</option>
								<option value="Date">Date</option>
								<option value="Time">Time</option>
								<option value="Combobox">Combobox</option>
								<option value="Text">Text</option>
								<option value="Numeric">Numeric</option>
								<option value="Boolean">Boolean</option>
								</select></td>
              <td style="width:51%;">
              <textarea class="form-control" rows="1" name="DESCRIPTIONS_0" id="txtdesc_0" maxlength="200" autocomplete="off" ></textarea>
              </td>              
              <td style="width:16%;">
              <input type="checkbox" name="IS_MANDATORY_0" class="filter-none" id="chkmdtry_0" >
							</td>
              <td  style="text-align:center; width:16%;" ><input type="checkbox" name="DEACTIVATED_0" id="deactive-checkbox_0" class="filter-none"  disabled></td>
							<td style="width:16%;"><input type="date" name="DODEACTIVATED_0" id="decativateddate_0" class="form-control" placeholder="dd/mm/yyyy" autocomplete="off" disabled></td>
							<td style="width:10%;"><button class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></button><button class="btn remove" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
						</tr>
						<tr></tr>
					</tbody>
				</table>
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



@endsection
<!-- btnSaveCountry -->
@push('bottom-css')
<style>
/* 

.select2-container__default .select2-results__group{
   color: #0f69cc;
} */


</style>
@endpush
@push('bottom-scripts')
<script>


      //delete row
    $("#example2").on('click', '.remove', function() {
    var rowCount = $(this).closest('table').find('tbody').length;
    if (rowCount > 1) {
    $(this).closest('tbody').remove();   
    } 
    if (rowCount <= 1) { 
    $(document).find('.remove').prop('disabled', false);  
    }
    event.preventDefault();
    });

//add row
        // $(".add").click(function() { 
        $("#example2").on('click', '.add', function() {
        var $tr = $(this).closest('table');
        var allTrs = $tr.find('tbody').last();      
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
        var rowCount = $('#Row_Count').val();
		    rowCount = parseInt(rowCount)+1;
        $('#Row_Count').val(rowCount);
        $clone.find('.remove').removeAttr('disabled'); 
        $clone.find('[id*="txtdesc"]').val('');
        $clone.find('[id*="chkmdtry"]').prop('checked', false);
        event.preventDefault();
    });

// });
$(document).ready(function(e) {
  var formConditionMst = $( "#frm_mst_condition" );
  formConditionMst.validate();

    $('#Row_Count').val("1");
  $('#btnAdd').on('click', function() {
      var viewURL = '{{route("master",[94,"add"])}}';
                  window.location.href=viewURL;
    });
    $('#btnExit').on('click', function() {
      var viewURL = '{{route('home')}}';
                  window.location.href=viewURL;
    });

   


        //Terms & Condition code
        $("#txttnccode").blur(function(){
              $(this).val($.trim( $(this).val() ));
              $("#ERROR_TNC_CODE").hide();
              validateSingleElemnet("txttnccode");
                
            });

            $( "#txttnccode" ).rules( "add", {
                required: true,
                nowhitespace: true,
                StringNumberRegex: true, //from custom.js
                messages: {
                    required: "Required field.",
                    minlength: jQuery.validator.format("min {0} char")
                }
            });


           

});

    //validae single element
    function validateSingleElemnet(element_id){
      var validator =$("#frm_mst_condition" ).validate();
         if(validator.element( "#"+element_id+"" )){
            //check duplicate code
          if(element_id=="txttnccode" || element_id=="TNC_CODE" ) {
            checkDuplicateCode();
          }

         }
    }

    // //check duplicate Calculation code
    function checkDuplicateCode(){
        
        //validate and save data
        var conditionForm = $("#frm_mst_condition");
        var formData = conditionForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("master",[94,"codeduplicate"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
                if(data.exists) {
                    $(".text-danger").hide();
                    showError('ERROR_TNC_CODE',data.msg);
                    $("#txttnccode").focus();
                }                                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });
    }

    //validate
    $( "#btnSave" ).click(function() {
        var formConditionMst = $("#frm_mst_condition");
        if(formConditionMst.valid()){
                $("#FocusId").val('');
                var TNC_CODE          =   $.trim($("[id*=txttnccode]").val());
                var FOR_SALE          =   $("#chkforsale").is(":checked");
                var FOR_PURCHASE      =   $("#chkforpurchase").is(":checked");
                if(TNC_CODE ===""){
                    $("#FocusId").val('TNC_CODE');
                    $("#ProceedBtn").focus();
                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn1").show();
                    $("#OkBtn").hide();
                    $("#AlertMessage").text('Please enter value in Condition Template Code.');
                    $("#alert").modal('show');
                    $("#OkBtn1").focus();
                    highlighFocusBtn('activeOk1');
                    return false;
                }
                if(FOR_SALE != true && FOR_PURCHASE != true){
                $("#FocusId").val('FOR_SALE');
                $("#ProceedBtn").focus();
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text('Please tick the Sale or Purchase Option.');
                $("#alert").modal('show');
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk1');
                return false;
                 }
                else
                {
                  event.preventDefault();
                  var allblank = [];
                  var allblank2 = [];
                  var allblank3 = [];
                      // $('#udfforsebody').find('.form-control').each(function () {
                      $("[id*=txttncname]").each(function(){
                          if($(this).val()!="")
                          {
                              allblank3.push('true');
                              $('.selvt').each(function () {
                                  var d_value = $(this).val();
                                  if(d_value != ""){
                                      allblank.push('true');
                                      if(d_value == "Combobox"){
                                          if($(this).parent().parent().find('[id*="txtdesc"]').val() != "")
                                          {
                                          allblank2.push('true');
                                          }
                                          else{
                                          allblank2.push('false');
                                          }  
                                      }
                                  }
                                  else{
                                      allblank.push('false');
                                  } 
                                  
                                  
                              });
                          }
                          else{
                                      allblank3.push('false');
                                  } 
                      });

                      if(jQuery.inArray("false", allblank3) !== -1){
                              $("#alert").modal('show');
                              $("#AlertMessage").text('Please enter value in Template Name.');
                              $("#YesBtn").hide(); 
                              $("#NoBtn").hide();  
                              $("#OkBtn1").show();
                              $("#OkBtn1").focus();
                              $("#OkBtn").hide();
                              highlighFocusBtn('activeOk1');
                          }
                          else if(jQuery.inArray("false", allblank) !== -1){
                          $("#alert").modal('show');
                          $("#AlertMessage").text('Please select value in Value Type.');
                          $("#YesBtn").hide(); 
                          $("#NoBtn").hide();  
                          $("#OkBtn1").show();
                          $("#OkBtn1").focus();
                          $("#OkBtn").hide();
                          highlighFocusBtn('activeOk');
                          }
                          else if(jQuery.inArray("false", allblank2) !== -1){
                          $("#alert").modal('show');
                          $("#AlertMessage").text('Please enter value in Description.');
                          $("#YesBtn").hide(); 
                          $("#NoBtn").hide();  
                          $("#OkBtn1").show();
                          $("#OkBtn1").focus();
                          $("#OkBtn").hide();
                          highlighFocusBtn('activeOk');
                          }
                          else{

                              $("#alert").modal('show');
                              $("#AlertMessage").text('Do you want to save to record.');
                              $("#YesBtn").data("funcname","fnSaveData");  //set dynamic fucntion name
                              $("#YesBtn").focus();
                              $("#OkBtn1").hide();
                              $("#OkBtn").hide();
                              highlighFocusBtn('activeYes');

                          }
                }
        }  
    });//btnSaveCountry

  
    
    $("#YesBtn").click(function(){

        $("#alert").modal('hide');
        var customFnName = $("#YesBtn").data("funcname");
            window[customFnName]();

    }); //yes button


   window.fnSaveData = function (){

        //validate and save data
        event.preventDefault();

        var formConditionMst = $("#frm_mst_condition");
        var formData = formConditionMst.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("master",[94,"save"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    if(data.errors.CTCODE){
                        showError('ERROR_TNC_CODE',data.errors.TNC_CODE);
                    }
                    if(data.errors.CTDESCRIPTION){
                        showError('ERROR_TNC_DESC',data.errors.TNC_DESC);
                    }
                   if(data.country=='duplicate') {

                      $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn1").show();
                      $("#OkBtn").hide();
                      $("#AlertMessage").text(data.msg);
                      $("#alert").modal('show');
                      $("#OkBtn1").focus();

                   }
                   if(data.save=='invalid') {

                      $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn1").show();
                      $("#OkBtn").hide();
                      $("#AlertMessage").text(data.msg);
                      $("#alert").modal('show');
                      $("#OkBtn1").focus();

                   }
                }
                if(data.success) {                   
                    console.log("succes MSG="+data.msg);                    
                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn").show();
                    $("#OkBtn1").hide();
                    $("#AlertMessage").text(data.msg);
                    $(".text-danger").hide();
                     $("#alert").modal('show');
                    $("#OkBtn").focus();

                  //  window.location.href='{{ route("master",[4,"index"])}}';
                }
                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
              
            },
        });
      
   } // fnSaveData

    $('#chkforsale').change(function()
    {
      if ($(this).is(':checked') == true) {
          $('#chkforpurchase').attr('disabled',true);
          $('#chkforpurchase').attr('checked',false);
          event.preventDefault();
      }
      else
      {
        $('#chkforpurchase').removeAttr('disabled');
        event.preventDefault();
      }
    });

    $('#chkforpurchase').change(function()
    {
      if ($(this).is(':checked') == true) {
          $('#chkforsale').attr('disabled',true);
          $('#chkforsale').attr('checked',false);
          event.preventDefault();
      }
      else
      {
        $('#chkforsale').removeAttr('disabled');
        event.preventDefault();
      }
    });



$("#example2").on('change', '[id*="drpvalue"]', function() {
    if ($(this).find('option:selected').val() != "Combobox") {
        $(this).parent().parent().find('[id*="txtdesc"]').prop('disabled', true);
        $(this).parent().parent().find('[id*="txtdesc"]').val('');
        event.preventDefault();
    }
    else
    {
        $(this).parent().parent().find('[id*="txtdesc"]').prop('disabled', false);
        event.preventDefault();
    }
});

$('#example2').on("change",'[id*="decativateddate"]', function( event ) {
    var today = new Date();     //Mon Nov 25 2013 14:13:55 GMT+0530 (IST) 
    var d = new Date($(this).val()); 
    today.setHours(0, 0, 0, 0) ;
    d.setHours(0, 0, 0, 0) ;
    if (d < today) {
        $(this).val('');
        $("#alert").modal('show');
        $("#AlertMessage").text('Date cannot be less than Current date');
        $("#YesBtn").hide(); 
        $("#NoBtn").hide();  
        $("#OkBtn1").show();
        $("#OkBtn1").focus();
        highlighFocusBtn('activeOk1');
        event.preventDefault();
    }
    else
    {
        event.preventDefault();
    }           
});



    
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

        $(".text-danger").hide();
        window.location.href = '{{route("master",[94,"index"]) }}';
        
    });
    
    $("#OkBtn1").click(function(){

      $("#alert").modal('hide');

      $("#YesBtn").show();  //reset
      $("#NoBtn").show();   //reset
      $("#OkBtn").hide();

      $(".text-danger").hide();
      $("[id*='txtcmpt']").focus();
      
      });
     ///ok button

    
    
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


    
  


   window.fnUndoYes = function (){
      
      //reload form
      window.location.href = "{{route('master',[94,'add'])}}";

   }//fnUndoYes


   window.fnUndoNo = function (){
      $("#txtctcode").focus();
   }//fnUndoNo

   function myFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("glcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("example23");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
          td = tr[i].getElementsByTagName("td")[0];
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

  function myNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("glnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("example23");
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

  function mybasisFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("bscodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("basisexample23");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
          td = tr[i].getElementsByTagName("td")[0];
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

  function mybasisNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("bsnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("basisexample23");
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


    function showError(pId,pVal){

      $("#"+pId+"").text(pVal);
      $("#"+pId+"").show();

    }//showError

    function highlighFocusBtn(pclass){
       $(".activeYes").hide();
       $(".activeNo").hide();
       
       $("."+pclass+"").show();
    }



    $(function() { $("#txtctcode").focus(); });

    // $(document).ready(function(){
    //  // Initialize select2
    //   $("[id*='drpbasis']").select2();
    // });
    

</script>

@endpush