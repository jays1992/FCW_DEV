@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
    <div class="row">
        <div class="col-lg-2"><a href="{{route('master',[$FormId,'index'])}}" class="btn singlebt">UDF Master</a></div>

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
    </div>
</div>
   
<div class="container-fluid filter">
    <form id="frm_mst_data"  method="POST">
        @CSRF  
        <div class="inner-form">
            <div class="row">
                <div class="col-lg-2 pl"><p>Voucher Type</p></div>
                <div class="col-lg-2 pl">
                    <input {{$ActionStatus}} type="text" name="VTID_REF_POPUP" id="VTID_REF_POPUP" value="{{isset($voucher_details[0]->VOUCHER_TYPE_NAME)?$voucher_details[0]->VOUCHER_TYPE_NAME:''}}" class="form-control" readonly autocomplete="off"/>
                    <input type="hidden" name="VTID_REF" id="VTID_REF" value="{{isset($voucher_details[0]->VOUCHER_TYPE)?$voucher_details[0]->VOUCHER_TYPE:''}}"  />
                </div>
            </div>
        </div>

        <div class=" table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:480px;" >
            <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                <thead id="thead1"  style="position: sticky;top: 0">
                    <tr>
                        <th>Label<input class="form-control" type="hidden" name="Row_Count" id ="Row_Count"></th>
                        <th>Value Type</th>
                        <th>Description</th>
                        <th>Is Mandatory</th>
                        <th>De-Activated</th>
                        <th>Date of De-Activated</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($objUdfResponse))
                    @php $n=1; @endphp
                    @foreach($objUdfResponse as $key => $row)
                    <tr class="participantRow">
                        <td hidden><input  class="form-control" type="hidden" name={{"UDFID_".$key}} id ={{"txtID_".$key}} maxlength="100" value="{{ $row->UDFID }}" autocomplete="off"   ></td>
                        <td><input {{$ActionStatus}}  class="form-control" type="text" name={{"LABEL_".$key}} id ={{"txtlabel_".$key}} maxlength="100" value="{{ $row->LABEL }}"  autocomplete="off" style="text-transform:uppercase" ></td>
                        <td>
                            <select {{$ActionStatus}} class="form-control selvt" name={{"VALUETYPE_".$key}} id={{"drpvalue_".$key}} >
                                <option value="" selected >Select</option>
                                <option value="Date">Date</option>
                                <option value="Time">Time</option>
                                <option value="Combobox">Combobox</option>
                                <option value="Text">Text</option>
                                <option value="Numeric">Numeric</option>
                                <option value="Boolean">Boolean</option>
                            </select>
                        </td>
                        <td><textarea {{$ActionStatus !=''?'readonly':''}} class="form-control w-100" rows="1"  name={{"DESCRIPTIONS_".$key}} id={{"txtdesc_".$key}} maxlength="200" autocomplete="off">"{{ $row->DESCRIPTIONS }}"</textarea> </td>
                        <td style="text-align:center;" ><input {{$ActionStatus}} type="checkbox" name={{"ISMANDATORY_".$key}} id={{"chkmdtry_".$key}}  {{$row->ISMANDATORY == 1 ? 'checked' : ''}}  ></td>
                        <td style="text-align:center;" ><input {{$ActionStatus}} type="checkbox" name={{"DEACTIVATED_".$key}}  id={{"deactive-checkbox_".$key}} {{$row->DEACTIVATED == 1 ? 'checked' : ''}} {{isset($n) && $n ==1?'disabled':''}} ></td>
                        <td style="text-align:center;" ><input {{$ActionStatus}} type="date" name={{"DODEACTIVATED_".$key}} class="form-control" placeholder="dd/mm/yyyy" id={{"decativateddate_".$key}} value="{{ ($row->DODEACTIVATED)=='1900-01-01'?'':$row->DODEACTIVATED }}" ></td>                    
                        <td align="center" >
                            <button {{$ActionStatus}} class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></button>
                            <button {{$ActionStatus}} class="btn remove" title="Delete" data-toggle="tooltip"  {{isset($n) && $n ==1?'disabled':''}}  ><i class="fa fa-trash" ></i></button>
                        </td>
                    </tr>
                    @php $n++; @endphp
                    @endforeach 
                    @endif                         
                </tbody>
            </table>  
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
            <button class="btn alertbt" name='OkBtn' id="OkBtn" style="display:none;margin-left: 90px;">
              <div id="alert-active" class="activeOk"></div>OK</button>
            <button class="btn alertbt" name='OkBtn1' id="OkBtn1" onclick="getFocus()" style="display:none;margin-left: 90px;">
            <div id="alert-active" class="activeOk1"></div>OK</button>
        </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<div id="vtrefpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='vtrefpopup_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Voucher Type</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="vt_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th class="ROW1" style="width: 10%" align="center">Select</th> 
            <th class="ROW2" style="width: 40%">Code</th>
            <th  class="ROW3"style="width: 40%">Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
          <td class="ROW2" style="width: 40%" ><input type="text" class="form-control" autocomplete="off" id="vt_codesearch" onkeyup="searchVTCode()" /></td>
          <td class="ROW2" style="width: 40%" ><input type="text" class="form-control" autocomplete="off" id="vt_namesearch" onkeyup="searchVTName()" /></td>
        </tr>
        </tbody>
      </table>
      
      <table id="vt_tab2" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody>
        @foreach ($voucher_type as $index=>$VtList)

        <tr >
          <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_VTID_REF[]"  id="vtref_{{ $VtList->VTID }}" class="clsvtref" value="{{ $VtList->VTID }}" /></td>
          <td  class="ROW2" style="width: 39%">{{ $VtList->VTCODE }}
          <input type="hidden" id="txtvtref_{{ $VtList->VTID }}" data-desc="{{ $VtList->VTCODE }}" data-descname="{{ $VtList->VTDESCRIPTIONS }}" value="{{ $VtList-> VTID }}"/>
          </td>
          <td  class="ROW3" style="width: 39%">{{ $VtList->VTDESCRIPTIONS }}</td>
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

@push('bottom-css')
<style>
#custom_dropdown, #udfforeditmst_filter {
    display: inline-table;
    margin-left: 15px;
}
.dataTables_wrapper .row:nth-child(1) .col-sm-6:nth-child(2){text-align:right;}
#filtercolumn{color: #555;
    background-color: #fff;
    background-image: none;
    border: 1px solid #ccc;
    }



</style>
@endpush
@push('bottom-scripts')
<script>
 var formUDFFOREDITMst = $("#frm_mst_data");
    formUDFFOREDITMst.validate();


$("#VTID_REF_POPUP").on("click",function(event){ 
  $("#vtrefpopup").show();
});

$("#VTID_REF_POPUP").keyup(function(event){
  if(event.keyCode==13){
    $("#vtrefpopup").show();
  }
});

$("#vtrefpopup_close").on("click",function(event){ 
  $("#vtrefpopup").hide();
});

$('.clsvtref').click(function(){
  var id          =   $(this).attr('id');
  var txtval      =   $("#txt"+id+"").val();
  var texdesc     =   $("#txt"+id+"").data("desc");
  var texdescname =   $("#txt"+id+"").data("descname");

  $("#vtdes").val(texdescname);
  $("#VTID_REF_POPUP").val(texdesc+' - '+texdescname);
  $("#VTID_REF").val(txtval);
 
  $("#VTID_REF_POPUP").blur(); 
  $("#EFFECTIVE_DT").focus(); 
  
  $("#vtrefpopup").hide();
  searchVTCode();
  event.preventDefault();
});

function searchVTCode() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("vt_codesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("vt_tab2");
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

function searchVTName() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("vt_namesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("vt_tab2");
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
     
$(document).ready(function(e) {
//delete row
var obj = <?php echo json_encode($objUdfResponse); ?>;
$.each( obj, function( key, value ) {
    $('#drpvalue_'+key).val(value.VALUETYPE);
    var deactivated = value.DEACTIVATED;
    var dvalue = value.VALUETYPE;
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
});
// 
var rcount = <?php echo json_encode($objCount); ?>;
$('#Row_Count').val(rcount);


$('#btnAdd').on('click', function() {
      var viewURL = '{{route("master",[$FormId,"add"])}}';
                  window.location.href=viewURL;
    });
$('#btnExit').on('click', function() {
      var viewURL = '{{route('home')}}';
                  window.location.href=viewURL;
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

    $("#example2").on('click', '.remove', function() {
    var rowCount = $('#Row_Count').val();
    
    //rowCount = parseInt(rowCount)-1;
    //$('#Row_Count').val(rowCount);
    
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
        $clone.find('[id*="txtdesc"]').val('');
        $clone.find('[id*="txtID"]').val('0'); 
        $clone.find('[id*="chkmdtry"]').prop("checked", false); 
        $clone.find('[id*="deactive-checkbox"]').prop("checked", false); 

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

        $("#OkBtn").click(function(){
        $("#alert").modal('hide');
        });////ok button


        window.fnUndoYes = function (){
        //reload form
        window.location.reload();
        }//fnUndoYes


       

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
</script>

@endpush

@push('bottom-scripts')
<script>

$(document).ready(function() {



    $('#frm_mst_data1').bootstrapValidator({
       
        fields: {
            txtlabel: {
                validators: {
                    notEmpty: {
                        message: 'The Label is required'
                    }
                }
            },            
        },
        submitHandler: function(validator, form, submitButton) {
            alert( "Handler for .submit() called." );
             event.preventDefault();
             $("#frm_mst_data").submit();
        }
    });

$( "#btnSaveSE" ).click(function() {
    if(formUDFFOREDITMst.valid()){
            $("#FocusId").val('');
            var VTID_REF       =   $.trim($("#VTID_REF").val());
            var LABEL          =   $.trim($("[id*=txtlabel]").val());
            var VALUETYPE      =   $.trim($("[id*=drpvalue]").val());
            var DESCRIPTIONS    =   $.trim($("[id*=txtdesc]").val());
            var DEACTIVATED    =   $("[id*=deactive-checkbox]").is(":checked");
            var ISMANDATORY    =   $("[id*=chkmdtry]").is(":checked");
            var DODEACTIVATED  =   $("[id*=decativateddate]").val();

            if(VTID_REF ===""){
                $("#FocusId").val("VTID_REF_POPUP");
                $("[id*=txtlabel]").val(''); 
                $("#ProceedBtn").focus();
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text('Please select voucher type.');
                $("#alert").modal('show');
                $("#OkBtn1").focus();
                return false;
            }
            else if(LABEL ===""){
                //$("#FocusId").val('LABEL');
                //$("[id*=txtlabel]").blur();
                $("#FocusId").val($("[id*=txtlabel]"));
                $("[id*=txtlabel]").val(''); 
                $("#ProceedBtn").focus();
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text('Please enter value in Label.');
                $("#alert").modal('show');
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk1');
                return false;
            }
            if(VALUETYPE ===""){
                $("#FocusId").val($("[id*=drpvalue]"));
                $("[id*=drpvalue]").val(''); 
                //$("#FocusId").val('VALUETYPE');
                //$("[id*=drpvalue]").blur(); 
                $("#ProceedBtn").focus();
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text('Please select value in Value Type.');
                $("#alert").modal('show');
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk1');
                return false;
            } 
            if(VALUETYPE ==="Combobox" && $.trim(DESCRIPTIONS) ===""){
                //$("#FocusId").val('DESCRIPTIONS');
                //$("[id*=txtdesc]").blur(); 
                $("#FocusId").val($("[id*=txtdesc]"));
                $("[id*=txtdesc]").val(''); 
                $("#ProceedBtn").focus();
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text('Please enter value in Description.');
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
                    $("[id*=txtlabel]").each(function(){
                        if($.trim($(this).val())!="")
                        {
                            //$(this).val('');
                            allblank3.push('true');
                            $('.selvt').each(function () {
                                var d_value = $(this).val();
                                if(d_value != ""){
                                    allblank.push('true');
                                    if(d_value == "Combobox"){
                                        if($.trim($(this).parent().parent().find('[id*="txtdesc"]').val()) != "")
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
                                
                                if($(this).parent().parent().find('[id*="deactive-checkbox"]').is(":checked") == false)
                                {
                                    if($(this).parent().parent().find('[id*="decativateddate"]').val() != '')
                                    {
                                        $(this).parent().parent().find('[id*="decativateddate"]').prop('disabled', true);
                                        $(this).parent().parent().find('[id*="decativateddate"]').val('');
                                
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
                            highlighFocusBtn('activeOk1');
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
                        $("#AlertMessage").text('Please select deactivation date.');
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


$( "#btnApprove" ).click(function() {

    if(formUDFFOREDITMst.valid()){
            $("#FocusId").val('');
            var VTID_REF       =   $.trim($("#VTID_REF").val());
            var LABEL          =   $.trim($("[id*=txtlabel]").val());
            var VALUETYPE      =   $.trim($("[id*=drpvalue]").val());
            var DESCRIPTIONS    =   $.trim($("[id*=txtdesc]").val());
            var DEACTIVATED    =   $("[id*=deactive-checkbox]").is(":checked");
            var ISMANDATORY    =   $("[id*=chkmdtry]").is(":checked");
            var DODEACTIVATED  =   $("[id*=decativateddate]").val();

            if(VTID_REF ===""){
                $("#FocusId").val("VTID_REF_POPUP");
                $("[id*=txtlabel]").val(''); 
                $("#ProceedBtn").focus();
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text('Please select voucher type.');
                $("#alert").modal('show');
                $("#OkBtn1").focus();
                return false;
            }
            else if(LABEL ===""){
                //$("#FocusId").val('LABEL');
                //$("[id*=txtlabel]").blur();
                $("#FocusId").val($("[id*=txtlabel]"));
                $("[id*=txtlabel]").val(''); 
                $("#ProceedBtn").focus();
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text('Please enter value in Label.');
                $("#alert").modal('show');
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk1');
                return false;
            }
            if(VALUETYPE ===""){
                $("#FocusId").val($("[id*=drpvalue]"));
                $("[id*=drpvalue]").val(''); 
                //$("#FocusId").val('VALUETYPE');
                //$("[id*=drpvalue]").blur(); 
                $("#ProceedBtn").focus();
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text('Please select value in Value Type.');
                $("#alert").modal('show');
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk1');
                return false;
            } 
            if(VALUETYPE ==="Combobox" && $.trim(DESCRIPTIONS) ===""){
                //$("#FocusId").val('DESCRIPTIONS');
                //$("[id*=txtdesc]").blur(); 
                $("#FocusId").val($("[id*=txtdesc]"));
                $("[id*=txtdesc]").val(''); 
                $("#ProceedBtn").focus();
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text('Please enter value in Description.');
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
                    $("[id*=txtlabel]").each(function(){
                        if($.trim($(this).val())!="")
                        {
                            //$(this).val('');
                            allblank3.push('true');
                            $('.selvt').each(function () {
                                var d_value = $(this).val();
                                if(d_value != ""){
                                    allblank.push('true');
                                    if(d_value == "Combobox"){
                                        if($.trim($(this).parent().parent().find('[id*="txtdesc"]').val()) != "")
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
                                
                                if($(this).parent().parent().find('[id*="deactive-checkbox"]').is(":checked") == false)
                                {
                                    if($(this).parent().parent().find('[id*="decativateddate"]').val() != '')
                                    {
                                        $(this).parent().parent().find('[id*="decativateddate"]').prop('disabled', true);
                                        $(this).parent().parent().find('[id*="decativateddate"]').val('');
                                
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
                            highlighFocusBtn('activeOk1');
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
                        $("#AlertMessage").text('Please select deactivation date.');
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

                            var udfforeditForm = $("#frm_mst_data");
                            var formData = udfforeditForm.serialize();
                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                }
                            });
                            $.ajax({
                                url:'{{ route("mastermodify",[$FormId,"update"]) }}',
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
                                    if(data.reqdata=='norecord') {

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
                                        // $("#frm_mst_reqdata").trigger("reset");

                                        $("#alert").modal('show');
                                        $("#OkBtn").focus();
                                        highlighFocusBtn('activeOk');
                                        // window.location.href="{{ route('master',[$FormId,'index']) }}";
                                    }
                                    else if(data.cancel) {                   
                                        console.log("cancel MSG="+data.msg);
                                        
                                        $("#YesBtn").hide();
                                        $("#NoBtn").hide();
                                        $("#OkBtn1").show();

                                        $("#AlertMessage").text(data.msg);

                                        $(".text-danger").hide();
                                        // $("#frm_mst_reqdata").trigger("reset");

                                        $("#alert").modal('show');
                                        $("#OkBtn1").focus();
                                        highlighFocusBtn('activeOk1');
                                        // window.location.href="{{ route('master',[$FormId,'index']) }}";
                                    }
                                    else
                                    {
                                        console.log("duplicate MSG="+data.msg);
                                        
                                        $("#YesBtn").hide();
                                        $("#NoBtn").hide();
                                        $("#OkBtn1").show();

                                        $("#AlertMessage").text(data.msg);

                                        $(".text-danger").hide();
                                        // $("#frm_mst_reqdata").trigger("reset");

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
            //             }
        

            // }

}

window.fnApproveData = function (){

//validate and save data
event.preventDefault();

var udfforeditForm = $("#frm_mst_data");
var formData = udfforeditForm.serialize();
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$.ajax({
    url:'{{ route("mastermodify",[$FormId,"Approve"]) }}',
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
           if(data.reqdata=='norecord') {

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
    window.location.href = '{{route("master",[$FormId,"index"]) }}';
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