
@extends('layouts.app')
@section('content')
    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[199,'index'])}}" class="btn singlebt">Calculation Basis</a>
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

    <form id="frm_mst_cb" onsubmit="return validateForm()"  method="POST" class="needs-validation"  >
    <div class="container-fluid filter">

	<div class="inner-form">


  <div class="row">
            <div class="col-lg-2 pl"><p>Document No</p></div>
            <div class="col-lg-2 pl">
            @if($objDD->SYSTEM_GRSR == "1")
                <input type="text" name="BASIS_DOC_NO" id="BASIS_DOC_NO" value="{{ $objDOCNO }}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase" autofocus >
            @endif
            @if($objDD->MANUAL_SR == "1")
                <input type="text" name="BASIS_DOC_NO" id="BASIS_DOC_NO" value="{{ old('BASIS_DOC_NO') }}" class="form-control mandatory" maxlength="{{$objDD->MANUAL_MAXLENGTH}}" autocomplete="off" style="text-transform:uppercase" autofocus >
            @endif
            
            </div>
            
            <div class="col-lg-2 pl"><p>Document Date</p></div>
            <div class="col-lg-2 pl">
                <input type="date" name="BASIS_DOC_DT" id="BASIS_DOC_DT" value="{{ old('BASIS_DOC_DT') }}" class="form-control mandatory" autocomplete="off" placeholder="dd/mm/yyyy" >
            </div>
      
        </div>

		
	</div>

	<div class="container-fluid">
		<div class="row">
      <div class="tab-content">
      <div id="Material" class="tab-pane fade in active">
          <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:450px;margin-top:10px;" >
              <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                  <thead id="thead1"  style="position: sticky;top: 0">                                                    
                          <tr>                                     
                              <th>Earning Head Code	 <input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1" value="1"> </th>
                              <th>Earning Head Description</th>
                              <th> PF</th>
                              <th> VPF</th>
                              <th> ESI</th>
                              <th> Bonus</th>
                              <th> OT</th>
                              <th> Gratuity</th>
                              <th> Welfare Fund</th>
                              <th> TDS</th>
                              <th> PT</th>
                              <th> LWP</th>
                              <th> Earned Leave</th>
                              <th>Incentive</th>
                              <th> Super Annuation</th>
                              <th>Other 1</th>
                              <th> Other 2</th>
                              <th>Other 3</th>
                              <th>Other 4</th>
                              <th>Other 5</th>
                              <th  width="6%">Action</th>                      
                  </thead>
                  <tbody>   
                  <tr  class="participantRow">    
                    <td><input  type="text" name="txtEH_popup_0" id="txtEH_popup_0" class="form-control"  autocomplete="off"  readonly/></td>
                    <td  hidden><input type="text" name="EHID_REF_0" id="EHID_REF_0" class="form-control" autocomplete="off" /></td>                          
                    <td><input type="text" name="EH_REF_0" id="EH_REF_0" class="form-control"  autocomplete="off"  readonly /></td>
                      <td style="text-align:center; width: 81px;"><input type="checkbox" name="PF_0" id="PF_0"  ></td>
                      <td style="text-align:center; width: 81px;"><input type="checkbox" name="VPF_0" id="VPF_0"  ></td>
                      <td style="text-align:center; width: 81px;"><input type="checkbox" name="ESI_0" id="ESI_0"  ></td>
                      <td style="text-align:center; width: 81px;"><input type="checkbox" name="Bonus_0" id="Bonus_0"  ></td>
                      <td style="text-align:center; width: 81px;"><input type="checkbox" name="OT_0" id="OT_0"  ></td>
                      <td style="text-align:center; width: 81px;"><input type="checkbox" name="Gratuity_0" id="Gratuity_0"  ></td>
                      <td style="text-align:center; width: 81px;"><input type="checkbox" name="Welfare_Fund_0" id="Welfare_Fund_0"  ></td>
                      <td style="text-align:center; width: 81px;"><input type="checkbox" name="TDS_0" id="TDS_0"  ></td>
                      <td style="text-align:center; width: 81px;"><input type="checkbox" name="PT_0" id="PT_0"  ></td>
                      <td style="text-align:center; width: 81px;"><input type="checkbox" name="LWP_0" id="LWP_0"  ></td>
                      <td style="text-align:center; width: 81px;"><input type="checkbox" name="Earned_Leave_0" id="Earned_Leave_0"  ></td>
                      <td style="text-align:center; width: 81px;"><input type="checkbox" name="Incentive_0" id="Incentive_0"  ></td>
                      <td style="text-align:center; width: 81px;"><input type="checkbox" name="Super_Anuation_0" id="Super_Anuation_0"  ></td>
                      <td style="text-align:center; width: 81px;"><input type="checkbox" name="OTHER1_0" id="OTHER1_0"  ></td>
                      <td style="text-align:center; width: 81px;"><input type="checkbox" name="OTHER2_0" id="OTHER2_0"  ></td>
                      <td style="text-align:center; width: 81px;"><input type="checkbox" name="OTHER3_0" id="OTHER3_0"  ></td>
                      <td style="text-align:center; width: 81px;"><input type="checkbox" name="OTHER4_0" id="OTHER4_0"  ></td>
                      <td style="text-align:center; width: 81px;"><input type="checkbox" name="OTHER5_0" id="OTHER5_0"  ></td>
                                            
                  <td align="center" ><button class="btn add material" title="add" data-toggle="tooltip" type="button" ><i class="fa fa-plus"></i></button>
                          <button class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td> 
                       </tr>
                <tr></tr>
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


<!--Eearning Head dropdown-->
<div id="EHpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='EH_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Earning Head List</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="EarningHeadTable" class="display nowrap table  table-striped table-bordered" >
    <thead>
          <tr id="none-select" class="searchalldata" hidden>
            
            <td> 
            <input type="hidden"  id="hdn_EHid"/>
            <input type="hidden" id="hdn_EHid2"/>
            <input type="hidden" id="hdn_EHid3"/>
            </td>
          </tr>

      <tr>
        <th class="ROW1">Select</th> 
        <th class="ROW2">Code</th>
        <th class="ROW3">Description</th>
      </tr>
    </thead>
    <tbody>

      <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="EHcodesearch" class="form-control" onkeyup="EarningHeadCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="EHnamesearch" class="form-control" onkeyup="EarningHeadNameFunction()"></td>
      </tr>

    </tbody>
    </table>
      <table id="EarningHeadTable2" class="display nowrap table  table-striped table-bordered"  >
        <thead id="thead2">

        </thead>
        <tbody id="tbody_EH">     
        
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


.filter input {
    float: none !important;
}

</style>
@endpush
@push('bottom-scripts')
<script>


//================================== Earning Head Section  =================================

let EarningHeadTable2 = "#EarningHeadTable2";
let EarningHeadTable = "#EarningHeadTable";
let QCPheaders = document.querySelectorAll(EarningHeadTable + " th");

QCPheaders.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(EarningHeadTable2, ".clssEHid", "td:nth-child(" + (i + 1) + ")");
  });
});

function EarningHeadCodeFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("EHcodesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("EarningHeadTable2");
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

function EarningHeadNameFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("EHnamesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("EarningHeadTable2");
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


$('#Material').on('click','[id*="txtEH_popup"]',function(event){
$('#hdn_EHid').val($(this).attr('id'));
$('#hdn_EHid2').val($(this).parent().parent().find('[id*="EHID_REF"]').attr('id'));
$('#hdn_EHid3').val($(this).parent().parent().find('[id*="EH_REF"]').attr('id'));
var fieldid = $(this).parent().parent().find('[id*="EHID_REF"]').attr('id');



  $("#EHpopup").show();
  $("#tbody_EH").html('loading...');

  $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  })

  $.ajax({
      url:'{{route("master",[199,"get_EarningHead"])}}',
      type:'POST',
      data:{'fieldid':fieldid},
      success:function(data) {
        $("#tbody_EH").html(data);
        BindEH();
        showSelectedCheck($("#"+fieldid).val(),"SELECT_"+fieldid);
      },
      error:function(data){
        console.log("Error: Something went wrong.");
        $("#tbody_EH").html('');
      },
  });



});

$("#EH_closePopup").click(function(event){
$("#EHpopup").hide();
});

function BindEH(){
$(".clssEHid").click(function(){
  var fieldid = $(this).attr('id');
  var txtval =    $("#txt"+fieldid+"").val();
  var texdesc =   $("#txt"+fieldid+"").data("desc");
  var texdesc1 =   $("#txt"+fieldid+"").data("desc1");

  var txtid   = $('#hdn_EHid').val();
  var txt_id2 = $('#hdn_EHid2').val();
  var txt_id3 = $('#hdn_EHid3').val();



  var get_id = txtid.split('_');
  var rowid=get_id[2];

  var CheckExist  = []; 
  CheckExist.push('true');

  $('#example2').find('.participantRow').each(function(){

    var EHID_REF = $(this).find('[id*="EHID_REF"]').val();

    if(txtval){
      if(txtval == EHID_REF){
        CheckExist.push('false');
        return false;
      }               
    }
  });

   old_value=$("#EHID_REF_"+rowid).val();
    if(txtval!='' && txtval===old_value){
    $('#'+txtid).val(texdesc);
    $('#'+txt_id2).val(txtval);
    $('#'+txt_id3).val(texdesc1);
    $("#EHpopup").hide();
    return false;   
    }else if(jQuery.inArray("false", CheckExist) !== -1){
    $(this).find('[id*="txtEH_popup"]').val();
    $(this).find('[id*="EHID_REF"]').val();
    $(this).find('[id*="EH_REF"]').val();

    $("#FocusId").val(txtid);
    $("#alert").modal('show');
    $("#AlertMessage").text('Earning Head already Exist.');
    $("#YesBtn").hide(); 
    $("#NoBtn").hide();  
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk');
    $("#EHpopup").hide();
    return false;
  }
  else{
    $('#'+txtid).val(texdesc);
    $('#'+txt_id2).val(txtval);
    $('#'+txt_id3).val(texdesc1);
    $('#PF_'+rowid).prop('checked', false);
    $('#VPF_'+rowid).prop('checked', false);
    $('#ESI_'+rowid).prop('checked', false);
    $('#Bonus_'+rowid).prop('checked', false);
    $('#OT_'+rowid).prop('checked', false);
    $('#Gratuity_'+rowid).prop('checked', false);
    $('#Welfare_Fund_'+rowid).prop('checked', false);
    $('#LWP_'+rowid).prop('checked', false);
    $('#TDS_'+rowid).prop('checked', false);
    $('#PT_'+rowid).prop('checked', false);
    $('#Earned_Leave_'+rowid).prop('checked', false);
    $('#Incentive_'+rowid).prop('checked', false);
    $('#Super_Anuation_'+rowid).prop('checked', false);
    $('#OTHER1_'+rowid).prop('checked', false);
    $('#OTHER2_'+rowid).prop('checked', false);
    $('#OTHER3_'+rowid).prop('checked', false);
    $('#OTHER4_'+rowid).prop('checked', false);
    $('#OTHER1_'+rowid).prop('checked', false);

  }

  $("#EHpopup").hide();
  $("#EHcodesearch").val(''); 
  $("#EHnamesearch").val(''); 
  EarningHeadCodeFunction();
  event.preventDefault();

});
}
     
$(document).ready(function(e) {
// var Material = $("#Material").html(); 
// $('#hdnmaterial').val(Material);


$('#btnAdd').on('click', function() {
  var viewURL = '{{route("master",[199,"add"])}}';
              window.location.href=viewURL;
              
});

$('#btnExit').on('click', function() {
  var viewURL = '{{route('home')}}';
              window.location.href=viewURL;
});




//delete row
$("#Material").on('click', '.remove', function() {
    var rowCount = $(this).closest('table').find('.participantRow').length;
    if (rowCount > 1) {
    $(this).closest('.participantRow').remove();     
    } 
    if (rowCount <= 1) { 
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn").hide();
          $("#OkBtn1").show();
          $("#AlertMessage").text('There is only 1 row. So cannot be remove.');
          $("#alert").modal('show');
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk1');
          return false;
    }
    event.preventDefault();

});



//add row
$("#Material").on('click', '.add', function() {
  var $tr = $(this).closest('table');
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
  $clone.find('input:hidden').val('');
  $clone.find('input:checkbox').prop('checked', false);
  var d         = new Date(); 
  var today     = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;

  //var h         = new Date($("#EDA").val()); 
  //var headDate  = h.getFullYear() + "-" + ("0" + (h.getMonth() + 1)).slice(-2) + "-" + ('0' + h.getDate()).slice(-2) ;
  
  //$clone.find('[id*="EDD"]').val(headDate);

  
  $tr.closest('table').append($clone);         
  var rowCount1 = $('#Row_Count1').val();
  rowCount1 = parseInt(rowCount1)+1;
  $('#Row_Count1').val(rowCount1);
  $clone.find('.remove').removeAttr('disabled'); 
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
});

    

window.fnUndoYes = function (){
    //reload form
  window.location.href = "{{route('master',[199,'add'])}}";
}//fnUndoYes


window.fnUndoNo = function (){
    $("#BASIS_DOC_NO").focus();
}//fnUndoNo

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

    if ($("#frm_mst_cb").valid()) {

        // Do something
        alert( "Handler for .submit() called." );
        event.preventDefault();
    }
});


    $('#frm_mst_cb1').bootstrapValidator({
       
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
             $("#frm_mst_cb").submit();
        }
    });
});




function validateForm(){
 
 $("#FocusId").val('');
 var BASIS_DOC_NO          =   $.trim($("#BASIS_DOC_NO").val());
 var BASIS_DOC_DT          =   $.trim($("#BASIS_DOC_DT").val());



 if(BASIS_DOC_NO ===""){
  $("#FocusId").val($("#BASIS_DOC_NO"));
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please enter Document No.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if(BASIS_DOC_DT ===""){
     $("#FocusId").val($("#BASIS_DOC_DT"));
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Enter Document Date.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }  



 else{





    event.preventDefault();
    var allblank = [];    
    var allblank1 = [];    

        // $('#udfforsebody').find('.form-control').each(function () {
          
          $('#example2').find('.participantRow').each(function(){
          if($.trim($(this).find("[id*=EHID_REF]").val())!=""){
           allblank.push('true');
               }
          else{
                allblank.push('false');
            } 


            if($(this).find("[id*=PF]").is(":checked")===true || $(this).find("[id*=VPF]").is(":checked")===true || $(this).find("[id*=ESI]").is(":checked")===true
            || $(this).find("[id*=Bonus]").is(":checked")===true || $(this).find("[id*=OT]").is(":checked")===true || $(this).find("[id*=Gratuity]").is(":checked")===true
            || $(this).find("[id*=Welfare_Fund]").is(":checked")===true  || $(this).find("[id*=TDS]").is(":checked")===true ||   $(this).find("[id*=LWP]").is(":checked")===true
            || $(this).find("[id*=Earned_Leave]").is(":checked")===true || $(this).find("[id*=Incentive]").is(":checked")===true || $(this).find("[id*=Super_Anuation]").is(":checked")===true
            || $(this).find("[id*=OTHER1]").is(":checked")===true || $(this).find("[id*=OTHER2]").is(":checked")===true || $(this).find("[id*=OTHER3]").is(":checked")===true
            || $(this).find("[id*=OTHER4]").is(":checked")===true || $(this).find("[id*=OTHER5]").is(":checked")===true
                           
        
            
            ){
           allblank1.push('true');
               }
          else{
                allblank1.push('false');
            } 



        }); }


        if(jQuery.inArray("false", allblank) !== -1){
          $("#alert").modal('show');
          $("#AlertMessage").text('Please select Earning Head.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk1');
          }
        else if(jQuery.inArray("false", allblank1) !== -1){
          $("#alert").modal('show');
          $("#AlertMessage").text('Please select atleast one option against selected Earning Head');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk1');
          }
          else{
                checkDuplicateCode();
    
          }

}

$("#YesBtn").click(function(){

$("#alert").modal('hide');
var customFnName = $("#YesBtn").data("funcname");
    window[customFnName]();

}); //yes button

$("#btnSaveSE" ).click(function() {
    var formReqData = $("#frm_mst_cb");
    if(formReqData.valid()){
      validateForm();
    }
});

window.fnSaveData = function (){

//validate and save data
event.preventDefault();

     var trnseForm = $("#frm_mst_cb");
    var formData = trnseForm.serialize();
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$.ajax({
    url:'{{ route("master",[199,"save"])}}',
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
    window.location.href = '{{route("master",[199,"index"]) }}';
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








      function checkDuplicateCode(){

        var BASIS_DOC_NO = $.trim($("#BASIS_DOC_NO").val());       

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url:'{{route("master",[199,"codeduplicate"])}}',
            type:'POST',
            data:{'BASIS_DOC_NO':BASIS_DOC_NO},
            success:function(data) {
                if(data.exists) {
                    $(".text-danger").hide();
                    $("#FocusId").val($("#BASIS_DOC_NO"));
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














  var lastdt = <?php echo json_encode($objlastdt[0]->BASIS_DOC_DT); ?>;
  var today = new Date(); 
  var sodate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;

  $('#BASIS_DOC_DT').attr('min',lastdt);
  $('#BASIS_DOC_DT').attr('max',sodate);


  var d = new Date(); 
  var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
  $('#BASIS_DOC_DT').val(today);








  
    
function showSelectedCheck(hidden_value,selectAll){

var divid ="";

if(hidden_value !=""){

  var all_location_id = document.querySelectorAll('input[name="'+selectAll+'[]"]');
  //console.log(all_location_id); 
 
  for(var x = 0, l = all_location_id.length; x < l;  x++){
   
    var checkid=all_location_id[x].id;
    var checkval=all_location_id[x].value;
  
    if(hidden_value == checkval){
      divid = checkid;
    }

    $("#"+checkid).prop('checked', false);
    
  }
}

if(divid !=""){
  $("#"+divid).prop('checked', true);
}

}

</script>


@endpush