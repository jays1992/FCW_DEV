@extends('layouts.app')
@section('content')
<form id="frm_mst_data" onsubmit="return validateForm()"  method="POST" class="needs-validation"  >
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
                <button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-thumbs-o-up"></i> Approved</button>
                <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
            </div>
        </div>
    </div>

    <div class="container-fluid filter ">
   
        <div class="inner-form">
            <div class="row">
                <div class="col-lg-2 pl"><p>Voucher Type</p></div>
                <div class="col-lg-2 pl">
                    <input type="text" name="VTID_REF_POPUP" id="VTID_REF_POPUP" class="form-control" readonly autocomplete="off"/>
                    <input type="hidden" name="VTID_REF" id="VTID_REF" />
                </div>
            </div>
        </div>

        <div class=" table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:480px;" >
            @csrf
            <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                <thead id="thead1"  style="position: sticky;top: 0">
                    <tr >
                        <th>Label <input class="form-control" type="hidden" name="Row_Count" id ="Row_Count"> </th>
                        <th>Value Type</th>
                        <th>Description</th>
                        <th>Is Mandatory</th>
                        <th>De-Activated</th>
                        <th>Date of De-Activated</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="udfforsebody" >
                    <tr  class="participantRow">
                        <td><input  class="form-control" type="text" name="LABEL_0" id ="txtlabel_0" maxlength="100" autocomplete="off" style="text-transform:uppercase" ></td>
                        
                        <td>
                           
                            <select class="form-control selvt" name="VALUETYPE_0" id="drpvalue_0" >
                                <option value="" selected >Select</option>
                                <option value="Date">Date</option>
                                <option value="Time">Time</option>
                                <option value="Combobox">Combobox</option>
                                <option value="Text">Text</option>
                                <option value="Numeric">Numeric</option>
                                <option value="Boolean">Boolean</option>
                            </select>
                            
                        </td>
                        <td><textarea class="form-control w-100" rows="1" name="DESCRIPTIONS_0" id="txtdesc_0" name=" " maxlength="200" autocomplete="off" ></textarea> </td>
                        <td style="text-align:center;" ><input type="checkbox" name="ISMANDATORY_0" id="chkmdtry_0"></td>
                        <td style="text-align:center;" ><input type="checkbox" name="DEACTIVATED_0"  id="deactive-checkbox_0" disabled></td>
                        <td style="text-align:center;" ><input type="date" name="DODEACTIVATED_0" class="form-control" placeholder="dd/mm/yyyy" id="decativateddate_0" disabled></td>                    
                        <td align="center" ><button class="btn add" title="add" data-toggle="tooltip">
                        <i class="fa fa-plus"></i></button>
                        <button class="btn remove" title="Delete" data-toggle="tooltip" disabled>
                        <i class="fa fa-trash" ></i></button>
                        </td>
                    </tr>
                    <tr>
                    </tr>       
                </tbody>
            </table>   
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
            <input type="hidden" id="FocusId" >
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
#custom_dropdown, #udffordatamst_filter {
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
    $('#Row_Count').val("1");

    $('#btnAdd').on('click', function() {
      var viewURL = '{{route("master",[$FormId,"add"])}}';
                  window.location.href=viewURL;
    });
    $('#btnExit').on('click', function() {
      var viewURL = '{{route('home')}}';
                  window.location.href=viewURL;
    });
    //to check the label duplicacy
    // $('[id*="txtlabel"]').blur(function(){
     $('#example2').on('blur','[id*="txtlabel"]',function(){
      var LABEL   =   $.trim($(this).val());
      if(LABEL ===""){
                $("#FocusId").val('LABEL');
                // $("[id*=txtlabel]").blur(); 
                $("#ProceedBtn").focus();
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text('Please enter value in Label.');
                $("#alert").modal('show');
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk1');
               
            } 
        else{ 
        var mstdataForm = $("#frm_mst_data");
        var formData = mstdataForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("master",[$FormId,"checkLabel"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               if(data.exists) {
                    $(".text-danger").hide();
                    if(data.exists) {                   
                        console.log("cancel MSG="+data.msg);
                                      $("#YesBtn").hide();
                                      $("#NoBtn").hide();
                                      $("#OkBtn1").show();
                                      $("#AlertMessage").text(data.msg);
                                      $(".text-danger").hide();
                                      $("#frm_mst_data").trigger("reset");
                                      $("#alert").modal('show');
                                      $("#OkBtn1").focus();
                                      highlighFocusBtn('activeOk1');
                    }                 
                }                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });
    }
});
        
    
//delete row
    $("#example2").on('click', '.remove', function() {
    var rowCount = $('#Row_Count').val();

	//rowCount = parseInt(rowCount)-1;
    //$('#Row_Count').val(rowCount);

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
            if(id) {
                var i = id.substr(id.length-1);
                var prefix = id.substr(0, (id.length-1));
                el.attr('id', prefix+(+i+1));
            }
            var name = el.attr('name') || null;
            if(name) {
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
        $clone.find('[id*="chkmdtry"]').prop('checked', false);

        event.preventDefault();

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
        event.preventDefault();
    });

    

    window.fnUndoYes = function (){
      
      //reload form
      window.location.href = "{{route('master',[$FormId,'add'])}}";

   }//fnUndoYes




   $("#example2").on('change', '[id*="drpvalue"]', function() {
    // $('[id*="drpvalue"]').on("change", function( event ) {
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

  $("#btnSaveSE").on("submit", function( event ) {

    alert('frm_mst_data');

    if ($("#frm_mst_data").valid()) {
        // Do something
        alert( "Handler for .submit() called." );
        event.preventDefault();
    }
});


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
});
function validateForm(){
 
 $("#FocusId").val('');

 var VTID_REF       =   $.trim($("#VTID_REF").val());
 var LABEL          =   $.trim($("[id*=txtlabel]").val());
 var VALUETYPE      =   $.trim($("[id*=drpvalue]").val());
 var DESCRIPTIONS   =   $.trim($("[id*=txtdesc]").val());
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
     $("#FocusId").val($("[id*=txtlabel]"));
     $("[id*=txtlabel]").val(''); 
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please enter value in Label.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if(VALUETYPE ===""){
     $("#FocusId").val($("[id*=drpvalue]"));
     $("[id*=drpvalue]").val(''); 
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please select value in Value Type.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 } 
 else if(VALUETYPE ==="Combobox" && $.trim(DESCRIPTIONS) ===""){
     $("#FocusId").val($("[id*=txtdesc]"));
     $("[id*=txtdesc]").val(''); 
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please enter value in Description.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 } 
 else{
    event.preventDefault();
    var allblank = [];
    var allblank2 = [];
    var allblank3 = [];
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
            highlighFocusBtn('activeOk');
            }
            else if(jQuery.inArray("false", allblank2) !== -1){
            $("#alert").modal('show');
            $("#AlertMessage").text('Please enter value in Description.');
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

}

$("#YesBtn").click(function(){

$("#alert").modal('hide');
var customFnName = $("#YesBtn").data("funcname");
    window[customFnName]();

}); //yes button

window.fnSaveData = function (){

//validate and save data
event.preventDefault();

     var mstdataForm = $("#frm_mst_data");
    var formData = mstdataForm.serialize();
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$.ajax({
    url:'{{ route("master",[$FormId,"save"])}}',
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
            // window.location.href="{{ route('master',[$FormId,'index']) }}";
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

//
function showError(pId,pVal){
    $("#"+pId+"").text(pVal);
    $("#"+pId+"").show();
}

function getFocus(){
  $("#"+$("#FocusId").val()).focus();
  $("#closePopup").click();
}
function highlighFocusBtn(pclass){
       $(".activeYes").hide();
       $(".activeNo").hide();
       
       $("."+pclass+"").show();
    }

</script>


@endpush