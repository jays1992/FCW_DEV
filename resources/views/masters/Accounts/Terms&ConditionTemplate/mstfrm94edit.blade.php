
@extends('layouts.app')
@section('content')
<!-- <form id="frm_mst_se" onsubmit="return validateForm()"  method="POST"  >     -->
 
    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[94,'index'])}}" class="btn singlebt">Terms & Condition Template</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
                        <button class="btn topnavbt" id="btnSaveSE" ><i class="fa fa-floppy-o"></i> Save</button>
                        <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt" id="btnUndo"  ><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove" {{($objRights->APPROVAL1||$objRights->APPROVAL2||$objRights->APPROVAL3||$objRights->APPROVAL4||$objRights->APPROVAL5) == 1 ? '' : 'disabled'}}><i class="fa fa-thumbs-o-up"></i> Approved</button>
                        <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                        <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                        
                </div>
            </div><!--row-->
    </div><!--topnav-->	
    <!-- multiple table-responsive table-wrapper-scroll-y my-custom-scrollbar -->
    <div class="container-fluid purchase-order-view filter">   
      
    <form id="frm_mst_condition"  method="POST">  
    @CSRF
          {{isset($objCondition->TNCID) ? method_field('PUT') : '' }}
                <div class="inner-form">
                    
                    <div class="row">
                      <div class="col-lg-2 pl"><p>TnC Template Code</p></div>
                      <div class="col-lg-2 pl">
                        <div class="col-lg-12 pl">
                          <input type="text" name="TNC_CODE" id="txttnccode" class="form-control mandatory" value="{{ $objCondition->TNC_CODE }}"  maxlength="50"   autocomplete="off" tabindex="1" style="text-transform:uppercase" readonly >
                        </div>
                      </div>
                    </div>
                    
                    <div class="row">
                    <div class="col-lg-2 pl"><p>TnC Template Description</p></div>
                        <div class="col-lg-5 pl">
                            <input type="text" name="TNC_DESC" id="txttncdesc" value="{{ $objCondition->TNC_DESC }}" autocomplete="off" tabindex="2" class="form-control"  maxlength="200"     >
                            
                        </div>
                    </div>
                    <div class="row">
                      <div class="col-lg-2 pl"><p>For Sale</p></div>
                      <div class="col-lg-1 pl">
                        <input type="checkbox" name="FOR_SALE" id="chkforsale" tabindex="3"  {{$objCondition->FOR_SALE == 1 ? 'checked' : ''}} >
                      </div>
                      
                      <div class="col-lg-1">OR</div>
                      
                      <div class="col-lg-2 pl"><p>For Purchase</p></div>
                      <div class="col-lg-1 pl">
                        <input type="checkbox" name="FOR_PURCHASE" id="chkforpurchase" tabindex="4" {{$objCondition->FOR_PURCHASE == 1 ? 'checked' : ''}} >
                      </div>
                    </div>	
                            
                    <div class="row">
                        <div class="col-lg-2 pl"><p>De-Activated</p></div>
                        <div class="col-lg-1 pl">
                            <input type="checkbox" name="DE_ACTIVATED" id="deactive"  tabindex="5" {{$objCondition->DEACTIVATED == 1 ? 'checked' : ''}} >
                        </div>
                        
                        <div class="col-lg-2 pl col-md-offset-1"><p>Date of De-Activated</p></div>
                        <div class="col-lg-2 pl">
                        <div class="col-lg-8 pl">
                        <input type="date" name="DO_DEACTIVATED" id="decativated_date" value="{{ ($objCondition->DODEACTIVATED)=='1900-01-01'?'':$objCondition->DODEACTIVATED }}" tabindex="6"  class="form-control datepicker" placeholder="dd/mm/yyyy" disabled>
                        </div>
                        </div>
                    </div>
                    
                    
                    <div class="row">
                        <div class="table-responsive table-wrapper-scroll-y " style="height:330px;margin-top:10px;">
                        <table id="example2" class="display nowrap table table-striped table-bordered itemlist dataTable" style="height:auto !important;">
                                <thead id="thead1"   style="position: sticky;top: 0; white-space:none;">
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
                                @if(!empty($objConditiontemp))
                                    @foreach($objConditiontemp as $key => $row)
                                        <tr  class="participantRow">
                                            <td hidden>
                                            <input  class="form-control" type="hidden" name={{"TNCDID_".$key}} id ={{"txtID_".$key}} maxlength="100" value="{{ $row->TNCDID }}"    >
                                            </td>
                                            <td style="width:10%;"><input  class="form-control" type="text" name={{"TNC_NAME_".$key}} id={{"txttncname_".$key}} maxlength="200"  autocomplete="off" value="{{ $row->TNC_NAME }}" style="text-transform:uppercase" ></td>
                                            <td>
                                                <select class="form-control selvt" name={{"VALUE_TYPE_".$key}} id={{"drpvalue_".$key}} >
                                                    <option value="" selected>Select</option>
                                                    <option value="Date">Date</option>
                                                    <option value="Time">Time</option>
                                                    <option value="Combobox">Combobox</option>
                                                    <option value="Text">Text</option>
                                                    <option value="Numeric">Numeric</option>
                                                    <option value="Boolean">Boolean</option>
                                                </select>
                                            </td>
                                            <td style="width:51%;">
                                                <textarea class="form-control w-100" rows="1"  name={{"DESCRIPTIONS_".$key}} id={{"txtdesc_".$key}} maxlength="200" autocomplete="off">"{{ $row->DESCRIPTIONS }}"</textarea> </td>
                                            </td>              
                                            <td style="width:16%;">
                                            <input type="checkbox" name={{"IS_MANDATORY_".$key}} id={{"chkmdtry_".$key}}  {{$row->IS_MANDATORY == 1 ? 'checked' : ''}}  ></td>
                                            </td>
                                            <td  style="text-align:center; width:16%;" ><input type="checkbox" name={{"DEACTIVATED_".$key}}  id={{"deactive-checkbox_".$key}} {{$row->DEACTIVATED == 1 ? 'checked' : ''}} ></td>
                                            <td style="width:16%;"><input type="date" name={{"DODEACTIVATED_".$key}} class="form-control" placeholder="dd/mm/yyyy" id={{"decativateddate_".$key}} value="{{ ($row->DODEACTIVATED)=='1900-01-01'?'':$row->DODEACTIVATED }}" ></td>                    
                                            <td style="width:10%;"><button class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></button><button class="btn remove" title="Delete" data-toggle="tooltip" type="button" ><i class="fa fa-trash" ></i></button></td>
                                        </tr>
                                        <tr></tr>
                                    @endforeach 
                                @endif 
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
        </form> 
</div><!--purchase-order-view-->
<!-- </form>    -->
<!-- </div> -->

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


@endsection

@push('bottom-css')
<style>
* {
  box-sizing: border-box;
}

#glcodesearch {
  background-image: url('/css/searchicon.png');
  background-position: 10px 10px;
  background-repeat: no-repeat;
  font-size: 11px;
  padding: 5px 5px 5px 5px;
  border: 1px solid #ddd;
  margin-bottom: 12px;
}
#glnamesearch {
  background-image: url('/css/searchicon.png');
  background-position: 10px 10px;
  background-repeat: no-repeat;
  font-size: 11px;
  padding: 5px 5px 5px 5px;
  border: 1px solid #ddd;
  margin-bottom: 12px;
}

#example23 {
  border-collapse: collapse;
  /* width: 100%; */
  border: 1px solid #ddd;
  font-size: 11px;5
}

#example23 th{
  text-align: left;
  padding: 5px;
  width: 150px;
  color: #0f69cc;
  background: #bbe7fc;
}

#example23 td {
  text-align: left;
  padding: 5px;
  width: 150px;
}

#example23 tr {
  border-bottom: 1px solid #ddd;
}

#example23 tr.header, #example23 tr:hover {
  background-color: #f1f1f1;
}

#example2345 {
  border-collapse: collapse;
  /* width: 100%; */
  border: 1px solid #ddd;
  font-size: 11px;
}

#example2345 th {
  text-align: left;
  padding: 5px;
  width: 150px;
  color: #0f69cc;
  background: #bbe7fc;
}

#example2345 td {
  text-align: left;
  padding: 5px;
  width: 150px;
}

#example2345 tr {
  border-bottom: 1px solid #ddd;
}

#example2345 tr.header, #example2345 tr:hover {
  background-color: #f1f1f1;
}
#bscodesearch {
  background-image: url('/css/searchicon.png');
  background-position: 10px 10px;
  background-repeat: no-repeat;
  font-size: 11px;
  padding: 5px 5px 5px 5px;
  border: 1px solid #ddd;
  margin-bottom: 5px;
}
#bsnamesearch {
  background-image: url('/css/searchicon.png');
  background-position: 10px 10px;
  background-repeat: no-repeat;
  font-size: 11px;
  padding: 5px 5px 5px 5px;
  border: 1px solid #ddd;
  margin-bottom: 5px;
}

#basisexample23 {
  border-collapse: collapse;
  /* width: 100%; */
  border: 1px solid #ddd;
  font-size: 11px;
}

#basisexample23 th {
  text-align: left;
  padding: 5px;
  width: 150px;
  color: #0f69cc;
  background: #bbe7fc;
}

#basisexample23 td {
  text-align: left;
  padding: 5px;
  width: 150px;
}

#basisexample23 tr {
  border-bottom: 1px solid #ddd;
}

#basisexample23 tr.header, #basisexample23 tr:hover {
  background-color: #f1f1f1;
}

#basisexample2345 {
  border-collapse: collapse;
  /* width: 100%; */
  border: 1px solid #ddd;
  font-size: 11px;
}

#basisexample2345 th{
  text-align: left;
  padding: 5px;
  width: 150px;
  color: #0f69cc;
  background: #bbe7fc;
}

#basisexample2345 td {
  text-align: left;
  padding: 5px;
  width: 150px;
}

#basisexample2345 tr {
  border-bottom: 1px solid #ddd;
}

#basisexample2345 tr.header, #basisexample2345 tr:hover {
  background-color: #f1f1f1;
}
</style>
@endpush

@push('bottom-scripts')
<script>
 
//  var CalculationForm = $("#frm_mst_calculation");
//  CalculationForm.validate();

     
$(document).ready(function(e) {

var rcount = <?php echo json_encode($objCount); ?>;
$('#Row_Count').val(rcount);

//delete row
var obj = <?php echo json_encode($objConditiontemp); ?>;
var objmain = <?php echo json_encode($objCondition); ?>;
$.each( obj, function( key, value ) {
  $('#drpvalue_'+key).val(value.VALUE_TYPE);
    var deactivated = value.DEACTIVATED;
    var dvalue = value.VALUE_TYPE;
    var sale = objmain.FOR_SALE;
    var purchase = objmain.FOR_PURCHASE;
    if(dvalue != "Combobox")
    {
        $('#txtdesc_'+key).prop('disabled', true);
        $('#txtdesc_'+key).val('');
    }
    else{
        $('#txtdesc_'+key).removeAttr('disabled');
        $('#txtdesc_'+key).val(value.DESCRIPTIONS);
    }
    if(deactivated == "1" )
    {
        $('#decativateddate_'+key).removeAttr('disabled');
    }
    else{        
        $('#decativateddate_'+key).attr('disabled',true);
    }
    if(sale == "1" )
    {
        $('#chkforpurchase').attr('disabled',true);
    }
    else{        
        $('#chkforpurchase').removeAttr('disabled');
    }
    if(purchase == "1" )
    {
        $('#chkforsale').attr('disabled',true);
    }
    else{        
        $('#chkforsale').removeAttr('disabled');
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
                $("#AlertMessage").text('Deactived Date cannot be less than Current date in rows');
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

        $('#decativated_date').change(function( event ) {
            var today = new Date();     //Mon Nov 25 2013 14:13:55 GMT+0530 (IST) 
            var d = new Date($(this).val()); 
            today.setHours(0, 0, 0, 0) ;
            d.setHours(0, 0, 0, 0) ;
            if (d < today) {
                $(this).val('');
                $("#alert").modal('show');
                $("#AlertMessage").text('Deactivated Date cannot be less than Current date');
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


// $('#Row_Count').val(rcount);
$(function() { $('[id*="txttnccode"]').focus(); });

$('#btnAdd').on('click', function() {
      var viewURL = '{{route("master",[94,"add"])}}';
                  window.location.href=viewURL;
    });
$('#btnExit').on('click', function() {
      var viewURL = '{{route('home')}}';
                  window.location.href=viewURL;
    });
    

        $('#example2').on("change",'[id*="deactive-checkbox"]', function( event ) {
            if ($(this).is(':checked') == false) {
                $(this).parent().parent().find('[id*="decativateddate"]').attr('disabled',true);
                $(this).parent().parent().find('[id*="decativateddate"]').val('');
                event.preventDefault();
            }
            else
            {
                $(this).parent().parent().find('[id*="decativateddate"]').removeAttr('disabled');
                event.preventDefault();
            }
        });

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

        $('#deactive').change(function( event ) {
            if ($(this).is(':checked') == false) {
                $(this).parent().parent().find('#decativated_date').attr('disabled',true);
                $(this).parent().parent().find('#decativated_date').val('');
                event.preventDefault();
            }
            else
            {
                $(this).parent().parent().find('#decativated_date').removeAttr('disabled');
                event.preventDefault();
            }
        });

        $('#example2').on("change",'[id*="drpvalue"]', function( event ) {
            if ($(this).find('option:selected').val() != "Combobox") {
                $(this).parent().parent().find('[id*="txtdesc"]').prop('disabled', true);
                $(this).parent().parent().find('[id*="txtdesc"]').val('');
                event.preventDefault();
            }
            else
            {
                $(this).parent().parent().find('[id*="txtdesc"]').removeAttr('disabled');
                event.preventDefault();
            }
        });

    

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
    function validateSingleElemnet(element_id){
      var validator =$("#frm_mst_condition" ).validate();
         if(validator.element( "#"+element_id+"" )){
            //check duplicate code
          if(element_id=="txttnccode" || element_id=="TNC_CODE" ) {
            // checkDuplicateCode();
          }

         }
    }

// growTextarea function: use for testing that the the javascript
// is also copied when row is cloned.  to confirm, 
// type several lines into Location, add a row, & repeat

function growTextarea (i,elem) {
    var elem = $(elem);
    var resizeTextarea = function( elem ) {
        var scrollLeft = window.pageXOffset || (document.documentElement || document.body.parentNode || document.body).scrollLeft;
        var scrollTop  = window.pageYOffset || (document.documentElement || document.body.parentNode || document.body).scrollTop;  
        elem.css('height', 'auto').css('height', elem.prop('scrollHeight') );
        window.scrollTo(scrollLeft, scrollTop);
    };

    elem.on('input', function() {
        resizeTextarea( $(this) );
    });

    resizeTextarea( $(elem) );
    }

    $('.growTextarea').each(growTextarea);

    

});

$("#example2").on('click', '.remove', function() {
    var rowCount = $('#Row_Count').val();
    rowCount = parseInt(rowCount)-1;
    $('#Row_Count').val(rowCount);
    if (rowCount > 1) {
    $(this).closest('.participantRow').remove(); 
    } 
    if (rowCount <= 1) { 
    $(document).find('.remove').prop('disabled', true);  
    }
    event.preventDefault();
    });

//add row
      $("#example2").on('click', '.add', function() {
        var $tr = $(this).closest('tbody');
        var allTrs = $tr.find('.participantRow').last();
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
        
        $clone.find('[id*="txtdesc"]').val('');
        $clone.find('[id*="txtID"]').val('0'); 
        $clone.find('[id*="chkmdtry"]').prop("checked", false); 
        $clone.find('[id*="deactive-checkbox"]').prop("checked", false); 
        $clone.find('[id*="decativateddate"]').val('');
        event.preventDefault();
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

 


        window.fnUndoYes = function (){
        //reload form
        window.location.reload();
        }//fnUndoYes


        window.fnUndoNo = function (){
        $("#txtctcode").focus();
        }//fnUndoNo





// });
</script>

@endpush

@push('bottom-scripts')
<script>

$(document).ready(function() {

    $('#frm_mst_condition1').bootstrapValidator({
       
       fields: {
           txtlabel: {
               validators: {
                   notEmpty: {
                       message: 'The Condition Template Code is required'
                   }
               }
           },            
       },
       submitHandler: function(validator, form, submitButton) {
           alert( "Handler for .submit() called." );
            event.preventDefault();
            $("#frm_mst_condition").submit();
       }
   });
    
$( "#btnSaveSE" ).click(function() {
    var formConditionMst = $("#frm_mst_condition");
    if(formConditionMst.valid()){
            $("#FocusId").val('');
            var TNC_CODE          =   $.trim($("[id*=txttnccode]").val());
            var FOR_SALE          =   $("#chkforsale").is(":checked");
            var FOR_PURCHASE      =   $("#chkforpurchase").is(":checked");
            var deactive          =   $("#deactive").is(":checked"); 
            var date              =   $("#decativated_date").val(); 
            if(TNC_CODE ===""){
                $("#FocusId").val($("[id*=txttnccode]"));
                $("[id*=txttnccode]").val('');
                $("#ProceedBtn").focus();
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
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
            if(deactive == true && date ==""){
                $("#FocusId").val('date');
                $("#ProceedBtn").focus();
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text('Please select deactived date.');
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
                var allblank4 = [];
                var allblank5 = [];
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
                                if($(this).parent().parent().find('[id*="deactive-checkbox"]').is(":checked") != false)
                                {
                                    if($(this).parent().parent().find('[id*="decativateddate"]').val() != '')
                                    {
                                        allblank4.push('true');
                                    }
                                    else
                                    {
                                        allblank4.push('false');
                                    }
                                }
                                if($(this).parent().parent().find('[id*="decativateddate"]').val() != '')
                                {
                                    if($(this).parent().parent().find('[id*="deactive-checkbox"]').is(":checked") != false)
                                    {
                                        allblank5.push('true');
                                    }
                                    else
                                    {
                                        allblank5.push('false');
                                    }
                                }
                                
                            });
                        }
                        else{
                                allblank3.push('false');
                            } 
                    });

                    if(jQuery.inArray("false", allblank3) !== -1){
                            $("#alert").modal('show');
                            $("#AlertMessage").text('Please enter value in Label.');
                            $("#YesBtn").hide(); 
                            $("#NoBtn").hide();  
                            $("#OkBtn1").show();
                            $("#OkBtn1").focus();
                            highlighFocusBtn('activeOk');
                        }
                        else if(jQuery.inArray("false", allblank) !== -1){
                        $("#alert").modal('show');
                        $("#AlertMessage").text('Please select value in Value Type.');
                        $("#YesBtn").hide(); 
                        $("#NoBtn").hide();  
                        $("#OkBtn1").show();
                        $("#OkBtn1").focus();
                        highlighFocusBtn('activeOk1');
                        }
                        else if(jQuery.inArray("false", allblank2) !== -1){
                        $("#alert").modal('show');
                        $("#AlertMessage").text('Please enter value in Description.');
                        $("#YesBtn").hide(); 
                        $("#NoBtn").hide();  
                        $("#OkBtn1").show();
                        $("#OkBtn1").focus();
                        highlighFocusBtn('activeOk1');
                        }
                        else if(jQuery.inArray("false", allblank4) !== -1){
                        $("#alert").modal('show');
                        $("#AlertMessage").text('Please select deactivation date in row.');
                        $("#YesBtn").hide(); 
                        $("#NoBtn").hide();  
                        $("#OkBtn1").show();
                        $("#OkBtn1").focus();
                        highlighFocusBtn('activeOk1');
                        }
                        else if(jQuery.inArray("false", allblank5) !== -1){
                        $("#alert").modal('show');
                        $("#AlertMessage").text('Please tick deactivated checkbox or remove deactivation date in row.');
                        $("#YesBtn").hide(); 
                        $("#NoBtn").hide();  
                        $("#OkBtn1").show();
                        $("#OkBtn1").focus();
                        highlighFocusBtn('activeOk1');
                        }
                        else{
                                $("#alert").modal('show');
                                $("#AlertMessage").text('Do you want to save to record.');
                                $("#YesBtn").data("funcname","fnSaveData"); 
                                $("#YesBtn").focus();
                                highlighFocusBtn('activeYes');
                            }
            }
    }         
});


$("#btnApprove" ).click(function() {
    var formConditionMst = $("#frm_mst_condition");
    if(formConditionMst.valid()){
            $("#FocusId").val('');
            var TNC_CODE          =   $.trim($("[id*=txttnccode]").val());
            var FOR_SALE          =   $("#chkforsale").is(":checked");
            var FOR_PURCHASE      =   $("#chkforpurchase").is(":checked");
            var deactive          =   $("#deactive").is(":checked"); 
            var date              =   $("#decativated_date").val(); 
            if(TNC_CODE ===""){
                $("#FocusId").val('TNC_CODE');
                $("#ProceedBtn").focus();
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
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
            if(deactive == true && date ==""){
                $("#FocusId").val('date');
                $("#ProceedBtn").focus();
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text('Please select deactived date.');
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
                var allblank4 = [];
                var allblank5 = [];
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
                                if($(this).parent().parent().find('[id*="deactive-checkbox"]').is(":checked") != false)
                                {
                                    if($(this).parent().parent().find('[id*="decativateddate"]').val() != '')
                                    {
                                        allblank4.push('true');
                                    }
                                    else
                                    {
                                        allblank4.push('false');
                                    }
                                }
                                if($(this).parent().parent().find('[id*="decativateddate"]').val() != '')
                                {
                                    if($(this).parent().parent().find('[id*="deactive-checkbox"]').is(":checked") != false)
                                    {
                                        allblank5.push('true');
                                    }
                                    else
                                    {
                                        allblank5.push('false');
                                    }
                                }
                                
                            });
                        }
                        else{
                                allblank3.push('false');
                            } 
                    });

                    if(jQuery.inArray("false", allblank3) !== -1){
                            $("#alert").modal('show');
                            $("#AlertMessage").text('Please enter value in Label.');
                            $("#YesBtn").hide(); 
                            $("#NoBtn").hide();  
                            $("#OkBtn1").show();
                            $("#OkBtn1").focus();
                            highlighFocusBtn('activeOk');
                        }
                        else if(jQuery.inArray("false", allblank) !== -1){
                        $("#alert").modal('show');
                        $("#AlertMessage").text('Please select value in Value Type.');
                        $("#YesBtn").hide(); 
                        $("#NoBtn").hide();  
                        $("#OkBtn1").show();
                        $("#OkBtn1").focus();
                        highlighFocusBtn('activeOk1');
                        }
                        else if(jQuery.inArray("false", allblank2) !== -1){
                        $("#alert").modal('show');
                        $("#AlertMessage").text('Please enter value in Description.');
                        $("#YesBtn").hide(); 
                        $("#NoBtn").hide();  
                        $("#OkBtn1").show();
                        $("#OkBtn1").focus();
                        highlighFocusBtn('activeOk1');
                        }
                        else if(jQuery.inArray("false", allblank4) !== -1){
                        $("#alert").modal('show');
                        $("#AlertMessage").text('Please select deactivation date in row.');
                        $("#YesBtn").hide(); 
                        $("#NoBtn").hide();  
                        $("#OkBtn1").show();
                        $("#OkBtn1").focus();
                        highlighFocusBtn('activeOk1');
                        }
                        else if(jQuery.inArray("false", allblank5) !== -1){
                        $("#alert").modal('show');
                        $("#AlertMessage").text('Please tick deactivated checkbox or remove deactivation date in row.');
                        $("#YesBtn").hide(); 
                        $("#NoBtn").hide();  
                        $("#OkBtn1").show();
                        $("#OkBtn1").focus();
                        highlighFocusBtn('activeOk1');
                        }
                        else{
                                $("#alert").modal('show');
                                $("#AlertMessage").text('Do you want to save to record.');
                                $("#YesBtn").data("funcname","fnApproveData"); 
                                $("#YesBtn").focus();
                                highlighFocusBtn('activeYes');
                            }
            }
    }   
});

$("#YesBtn").click(function(){

$("#alert").modal('hide');
var customFnName = $("#YesBtn").data("funcname");
    window[customFnName]();

}); //yes button


window.fnSaveData = function (){

    event.preventDefault();

    var formConditionMst = $("#frm_mst_condition");
    var formData = formConditionMst.serialize();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url:'{{ route("mastermodify",[94,"update"]) }}',
        type:'POST',
        data:formData,
        success:function(data) {
        
            if(data.errors) {
                $(".text-danger").hide();

                if(data.errors.LABEL){
                    showError('ERROR_LABEL',data.errors.LABEL);
                            $("#YesBtn").hide();
                            $("#NoBtn").hide();
                            $("#OkBtn").show();
                            $("#AlertMessage").text('Please enter correct value in Label.');
                            $("#alert").modal('show');
                            $("#OkBtn").focus();
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
                console.log("success MSG="+data.msg);
                
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").show();

                $("#AlertMessage").text(data.msg);

                $(".text-danger").hide();
                // $("#frm_mst_country").trigger("reset");

                $("#alert").modal('show');
                $("#OkBtn").focus();
                highlighFocusBtn('activeOk');
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
                highlighFocusBtn('activeOk1');
                // window.location.href="{{ route('master',[90,'index']) }}";
            }
            else
            {
                console.log("duplicate MSG="+data.msg);
                
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();

                $("#AlertMessage").text(data.msg);

                $(".text-danger").hide();
                // $("#frm_mst_country").trigger("reset");

                $("#alert").modal('show');
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk1');
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
        url:'{{ route("mastermodify",[94,"Approve"]) }}',
        type:'POST',
        data:formData,
        success:function(data) {
        
            if(data.errors) {
                $(".text-danger").hide();

                if(data.errors.LABEL){
                    showError('ERROR_LABEL',data.errors.LABEL);
                $("#YesBtn").hide();
                            $("#NoBtn").hide();
                            $("#OkBtn").show();
                            $("#AlertMessage").text('Please enter value in Label.');
                            $("#alert").modal('show');
                            $("#OkBtn").focus();
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
                highlighFocusBtn('activeOk');
            }
            else
            {
                console.log("duplicate MSG="+data.msg);
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text(data.msg);
                $(".text-danger").hide();
                $("#alert").modal('show');
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk1');
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


$("#NoBtn").click(function(){

$("#alert").modal('hide');
var custFnName = $("#NoBtn").data("funcname");
    window[custFnName]();

}); //no button

//ok button
$("#OkBtn").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn").hide();
    $(".text-danger").hide();
    window.location.href = '{{route("master",[94,"index"]) }}';
});

$("#OkBtn1").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn").hide();
    $("#OkBtn1").hide();
    $(".text-danger").hide();
});




});




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
function getFocus(){
    var FocusId=$("#FocusId").val();
    $("#"+FocusId).focus();
    $("#closePopup").click();
}



</script>


@endpush