@extends('layouts.app')
@section('content')
    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[271,'index'])}}" class="btn singlebt">Routing Master</a>
                </div><!--col-2-->
                <div class="col-lg-10 topnav-pd">
                    <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                    <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
                    <button class="btn topnavbt" id="btnSave" ><i class="fa fa-save"></i> Save</button>
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

    <form id="frm_mst" onsubmit="return validateForm()"  method="POST" class="needs-validation"  >
    <div class="container-fluid filter">

	<div class="inner-form">

    <div class="row">
        <div class="col-lg-2 pl"><p>FG / SFG Item</p></div>
        <div class="col-lg-2 pl">                 
              <input type="text" name="txtFGITEMPOP_popup" id="txtFGITEMPOP_popup" class="form-control clsclear"  autocomplete="off"  readonly />
              <input type="hidden" name="ITEMID_REF" id="hdnFGITEMPOPID" class="form-control clsclear" autocomplete="off" />
        </div>
        <div class="col-lg-2 pl"><p>Description</p></div>
        <div class="col-lg-3 pl">
            <input type="text" name="ITEM_DESC" id="ITEM_DESC" class="form-control clsclear"  autocomplete="off" readonly />
        </div>
    </div>
		
	</div>

	<div class="container-fluid">    
      
		<div class="row">
			<ul class="nav nav-tabs">
				<li class="active"><a data-toggle="tab" href="#Material">Material</a></li> 
			</ul>

      <div class="tab-content">
      <div id="Material" class="tab-pane fade in active">
          <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:280px;margin-top:10px;" >
              <table id="exp2" class="display nowrap table table-striped table-bordered itemlist sorting-remove" width="100%" style="height:auto !important;">

                  <thead id="thead1"  style="position: sticky;top:">
                          <tr >
                              <th width="10%">Production Stage	<input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1" value="1"></th>
                              <th width="5%" hidden>PSTAGEID_REF</th>
                              <th width="25%">Stage Description</th>
                              <th width="10%">Inhouse Production</th>
                              <th width="10%">Job Worker</th>
                              <th width="10%">FG Stage</th>
                              <th width="5%">Action</th>
                          </tr>
                  </thead>
<tbody id="tbody1">
<tr  class="participantRow">
    <td style="text-align:center;">
      <input type="text" name="PStage_popup_0" id="txtPStage_popup_0" class="form-control mandatory" style="width:140px" readonly />
    </td>
    <td hidden>
      <input type="text" style="width: 100px;" name="PSTAGEID_REF_0" id="hdnPSTAGEIDREF_0" class="form-control"/> 
    </td> 
    <td >
      <input type="text" name="STAGE_DESC_0" id="STAGE_DESC_0" class="form-control" readonly style="width: 100%;"/> 
    </td> 
    <td style="text-align: center;"><input type="checkbox" name="INHOUSE_PRODUCTION_0" id="INHOUSE_PRODUCTION_0" value="1"  class="filter-none"  style="float:none;" ></td>
    <td style="text-align: center;"><input type="checkbox" name="FG_STAGE_0" id="JOB_WORKER_0" value="1" class="filter-none" style="float:none;" style="float:none;" ></td>
    <td style="text-align: center;"><input type="checkbox" name="FG_STAGE_0" id="FG_STAGE_0"   value="1" class="filter-none"  style="float:none;"  ></td>
    <td style="width: 100px;" align="center" style="display: inline-block" ><button class="btn add material" title="add" data-toggle="tooltip" type="button" ><i class="fa fa-plus"></i></button>
    <button class="btn remove dmaterial"  disabled title="Delete" data-toggle="tooltip" type="button" ><i class="fa fa-trash" ></i></button></td>

  </tr>

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
<!--dropdown begin-->
<!-- FGITEMPOUP-->
<div id="FGITEMPOP_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:750px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='FGITEMPOP_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>FG / SFG Item Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="FGITEMPOPTable" class="display nowrap table  table-striped table-bordered" style="width: 100%">
    <thead>
          <tr id="none-select" class="searchalldata"  hidden>            
            <td > <input type="text" name="fieldid" id="hdn_FGITEMPOPid"/>
              <input type="text" name="fieldid2" id="hdn_FGITEMPOPid2"/>
              <input type="text" name="fieldid3" id="hdn_FGITEMPOPid3"/>
            </td>
          </tr>
          <tr>
            <th class="ROW1" style="width: 10%" align="center">Select</th> 
            <th class="ROW2" style="width: 20%" >Code</th>
            <th class="ROW3" style="width: 40%" >Description</th>
            <th class="ROW4" style="width: 30%" >Type</th>
          </tr>          
    </thead>
    <tbody>
    <tr>
      <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
      <td class="ROW2" style="width: 20%">
        <input type="text" autocomplete="off"  class="form-control"  id="FGITEMPOPcodesearch"   onkeyup="FGITEMPOPCodeFunction()"/>
      </td>
      <td class="ROW3" style="width: 40%">
        <input type="text" autocomplete="off"  class="form-control"  id="FGITEMPOPnamesearch"  onkeyup="FGITEMPOPNameFunction()"/>
      </td>
      <td class="ROW4" style="width: 30%">
        <input type="text" autocomplete="off"  class="form-control"  id="FGITEMPOPmattypesearch"   onkeyup="FGITEMPOPMattypFunction()"/>
      </td>
    </tr>
    </tbody>
    </table>
      <table id="FGITEMPOPTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">

        </thead>
        <tbody id="tbody_FGITEMPOP">     
        
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- FGITEMPOUP END-->
<!-- Production Stage Dropdown -->
<div id="PStagepopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" >
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='PStage_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Production Stage </p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="PStageTable" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
      <tr>
        <th class="ROW1" style="width: 10%" align="center">Select</th> 
        <th class="ROW2" style="width: 40%" >Code</th>
        <th class="ROW3" style="width: 40%" >Name</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
        <td class="ROW2"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control"   id="PStagecodesearch"  onkeyup="PStageCodeFunction()" /></td>
        <td class="ROW3"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control"   id="PStagenamesearch"  onkeyup="PStageNameFunction()" /></td>
      </tr>
    </tbody>
    </table>
      <table id="PStageTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          <tr id="none-select" class="searchalldata"  hidden>            
            <td colspan="2" >
              <input type="text" name="fieldid" id="hdn_PStageid"/>
              <input type="text" name="fieldid2" id="hdn_PStageid2"/>
            </td>
          </tr>          
        </thead>
        <tbody id="tbody_PStage">  
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Production Stage Dropdown-->
@endsection

@push('bottom-css')
<style> 


</style>
@endpush
@push('bottom-scripts')
<script>
//FGITEMPOP Dropdown
      let sqtid = "#FGITEMPOPTable2";
      let sqtid2 = "#FGITEMPOPTable";
      let salesquotationheaders = document.querySelectorAll(sqtid2 + " th");

      // Sort the table element when clicking on the table headers
      salesquotationheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(sqtid, ".clssqid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function FGITEMPOPCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("FGITEMPOPcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("FGITEMPOPTable2");
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

  function FGITEMPOPNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("FGITEMPOPnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("FGITEMPOPTable2");
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
  function FGITEMPOPMattypFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("FGITEMPOPmattypesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("FGITEMPOPTable2");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
          td = tr[i].getElementsByTagName("td")[3];
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

  $(document).on('focus','[id*="txtFGITEMPOP_popup"]',function(event){

          var id = $(this).attr('id');
          var id2 = $(this).parent().parent().find('[id*="FGITEMPOPID"]').attr('id');      
          var id3 = $('#ITEM_DESC').attr('id');      

          $('#hdn_FGITEMPOPid').val(id);
          $('#hdn_FGITEMPOPid2').val(id2);
          $('#hdn_FGITEMPOPid3').val(id3);

          $("#FGITEMPOPcodesearch").val(''); 
          $("#FGITEMPOPnamesearch").val(''); 
          $("#FGITEMPOPmattypesearch").val(''); 
          FGITEMPOPCodeFunction();
        
          $("#FGITEMPOP_popup").show();
          $("#tbody_FGITEMPOP").html('Loading...');
          $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          })
          $.ajax({
              url:'{{route("master",[271,"getfgitems"])}}',
              type:'POST',
              success:function(data) {
                $("#tbody_FGITEMPOP").html(data);
                BindFGITEMPOPEvents();
              },
              error:function(data){
                console.log("Error: Something went wrong.");
                $("#tbody_FGITEMPOP").html('');
              },
          });

      });

      $("#FGITEMPOP_closePopup").click(function(event){
        $("#FGITEMPOP_popup").hide();
      });

      function BindFGITEMPOPEvents()
      {
          $(".clsFGITEMPOP").click(function(){
            var fieldid = $(this).attr('id');
            var txtval =    $("#txt"+fieldid+"").val();
            var texdesc =   $("#txt"+fieldid+"").data("desc");
            var texdescdate =   $("#txt"+fieldid+"").data("descdate");

            //set values
            var txtid= $('#hdn_FGITEMPOPid').val();
            var txt_id2= $('#hdn_FGITEMPOPid2').val();
            var txt_id3= $('#hdn_FGITEMPOPid3').val();

            $('#'+txtid).val(texdesc);
            $('#'+txt_id2).val(txtval);
            $('#'+txt_id3).val(texdescdate);


            $("#FGITEMPOP_popup").hide();
            $("#FGITEMPOPcodesearch").val(''); 
            $("#FGITEMPOPnamesearch").val(''); 
            $("#FGITEMPOPmattypesearch").val(''); 
            FGITEMPOPCodeFunction();
            
            event.preventDefault();
            $(this).prop("checked",false);

          });
      }

//------------------------
//------------------------
//Production Stage  Starts
//------------------------

    let pstagetdid = "#PStageTable2";
    let pstagetdid2 = "#PStageTable";
    let pstageheaders = document.querySelectorAll(pstagetdid2 + " th");

    // Sort the table element when clicking on the table headers
    pstageheaders.forEach(function(element, i) {
      element.addEventListener("click", function() {
          w3.sortHTML(pstagetdid, ".clsPStage", "td:nth-child(" + (i + 1) + ")");
      });
    });

    function PStageCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("PStagecodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("PStageTable2");
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

    function PStageNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("PStagenamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("PStageTable2");
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

    $('#exp2').on('focus','[id*="txtPStage_popup"]',function(event){


        var FGITEMPOPID     =   $.trim($("#hdnFGITEMPOPID").val());        
        if(FGITEMPOPID ===""){
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text('Please select FG / SFG Item.');
            $("#alert").modal('show');
            $("#OkBtn1").focus();
            return false;
        }

        $("#PStagecodesearch").val(''); 
        $("#PStagenamesearch").val(''); 
        PStageCodeFunction();

        var id = $(this).attr('id');
        var id2 = $("#"+id).parent().parent().find('[id*="hdnPSTAGEIDREF"]').attr('id');      
        
        $('#hdn_PStageid').val(id);
        $('#hdn_PStageid2').val(id2);
          
        $("#tbody_PStage").html('Loading...');
          $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          })
          $.ajax({
              url:'{{route("master",[271,"getprodstages"])}}',
              type:'POST',
              success:function(data) {
                $("#tbody_PStage").html(data);
                BindPStageEvents();
              },
              error:function(data){
                console.log("Error: Something went wrong.");
                $("#tbody_PStage").html('');
              },
          });

        $("#PStagepopup").show();
        event.preventDefault();
    });

    $("#PStage_closePopup").on("click",function(event){ 
        $("#PStagepopup").hide();
        event.preventDefault();
    });

    //---------
    function BindPStageEvents()
    {
        $(".clsPStage").click(function(){

            var fieldid = $(this).attr('id');
            var txtval =    $("#txt"+fieldid+"").val();
            var texdesc =   $("#txt"+fieldid+"").data("desc");
            var stage_desc =   $("#txt"+fieldid+"").data("stagedesc");

            var ArrData = [];
            $('#exp2').find('.participantRow').each(function(){
              if($(this).find('[id*="hdnPSTAGEIDREF"]').val() != '')
              {
                var tmpdata = $(this).find('[id*="hdnPSTAGEIDREF_"]').val();
                ArrData.push(tmpdata);
              }
            });
            
            if(jQuery.inArray(txtval, ArrData) !== -1){
              $("#ITEMIDpopup").hide();
              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn").hide();
              $("#AlertMessage").text('Production Stage already exists. Please check.');
              $("#alert").modal('show');
              $("#OkBtn1").show();
              $("#OkBtn1").focus();
              highlighFocusBtn('activeOk1');
              
              txtval = '';
              texdesc = '';
              stage_desc = '';    

              $("#PStagepopup").hide();
              $("#PStagecodesearch").val(''); 
              $("#PStagenamesearch").val(''); 
              PStageCodeFunction();         
              $(this).prop("checked",false);
              return false;              
            }

                        
            var txtid= $('#hdn_PStageid').val();
            var txt_id2= $('#hdn_PStageid2').val();
            
            //clear row
            $('#'+txtid).parent().parent().find('input:text').val('');
            $('#'+txtid).parent().parent().find('input:hidden').val('');
            //   $('#'+txtid).parent().parent().find('[id*="dpp_priority"]').prop("selectedIndex",0);
            
            $('#'+txtid).val(texdesc);
            $('#'+txt_id2).val(txtval);
            $('#'+txtid).parent().parent().find('[id*="STAGE_DESC"]').val(stage_desc);
                
            $("#PStagepopup").hide();
            $("#PStagecodesearch").val(''); 
            $("#PStagenamesearch").val(''); 
            PStageCodeFunction();
            $(this).prop("checked",false);
            event.preventDefault();
        });
    }

  
//Production Stage  Ends
//------------------------
//------------------------
     
$(document).ready(function(e) {
 
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
  $clone.find('input:checkbox').prop("checked",false);

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
    $("#OkBtn1").hide();
    $("#NoBtn").focus();
});

    

window.fnUndoYes = function (){
    //reload form
  window.location.href = "{{route('master',[271,'add'])}}";
}//fnUndoYes


window.fnUndoNo = function (){
    $("#MACHINEID_REF").focus();
}//fnUndoNo
   

}); //ready
</script>

@endpush

@push('bottom-scripts')
<script>

function validateForm(){
 
        $("#FocusId").val('');
        var FGITEMPOPID     =   $.trim($("#hdnFGITEMPOPID").val());        
        if(FGITEMPOPID ===""){
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text('Please select FG / SFG Item.');
            $("#alert").modal('show');
            $("#OkBtn1").focus();
            return false;
        }
        else{

                event.preventDefault();
                var allblank = [];
                var allblank2 = [];

                $('#exp2').find('.participantRow').each(function(){
                  
                  if($.trim($(this).find("[id*=hdnPSTAGEIDREF]").val())!=""){
                      allblank.push('true');
                  }
                  else{
                      allblank.push('false');
                  } 

                  
                  if( $(this).find('[id*="INHOUSE_PRODUCTION"]').is(":checked")== false &&  $(this).find('[id*="JOB_WORKER"]').is(":checked")== false &&  $(this).find('[id*="FG_STAGE"]').is(":checked")== false) 
                  {
                    allblank2.push('false');
                  }else
                  {
                    allblank2.push('true');
                  }
                  
                }); 
                //-------------
          }


        if(jQuery.inArray("false", allblank) !== -1){
          $("#alert").modal('show');
          $("#AlertMessage").text('Please select Production Stage in Material Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
          }
        
        else if(jQuery.inArray("false", allblank2) !== -1){
          $("#alert").modal('show');
          $("#AlertMessage").text('Please select Inhouse Production or Job Worker or FG Stage in Material Tab.');
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

$("#YesBtn").click(function(){

$("#alert").modal('hide');
var customFnName = $("#YesBtn").data("funcname");
    window[customFnName]();

}); //yes button

$("#btnSave" ).click(function() {
    var formReqData = $("#frm_mst");
    if(formReqData.valid()){
      validateForm();
    }
});

window.fnSaveData = function (){

//validate and save data
event.preventDefault();

     var trnseForm = $("#frm_mst");
    var formData = trnseForm.serialize();
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$.ajax({
    url:'{{ route("master",[271,"save"])}}',
    type:'POST',
    data:formData,
    success:function(data) {
       
        if(data.errors) {
            $(".text-danger").hide();
 
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
            
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").hide();
            $("#OkBtn").show();

            $("#AlertMessage").text(data.msg);
            $(".text-danger").hide();
            $("#alert").modal('show');
            $("#OkBtn").focus();
            
        }
        else 
        {                   
          
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
    window.location.href = '{{route("master",[271,"index"]) }}';
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
    if(focusid!="" && focus!=undefined){
      $("#"+focusid).focus();
    }
    $("#closePopup").click();
}
function highlighFocusBtn(pclass){
    $(".activeYes").hide();
    $(".activeNo").hide();
    
    $("."+pclass+"").show();
}


$('#exp2').on("change","[id*='INHOUSE_PRODUCTION']",function(event){

    var  jobwork = $(this).parent().parent().find('[id*="JOB_WORKER"]').attr('id');
    var  fgstage = $(this).parent().parent().find('[id*="FG_STAGE"]').attr('id');

    if($(this).is(':checked')==true) {
      $('#'+jobwork).prop('checked',false);
      $('#'+fgstage).prop('checked',false);
    }
});  

$('#exp2').on("change","[id*='JOB_WORKER']",function(event){

    var  inhpro = $(this).parent().parent().find('[id*="INHOUSE_PRODUCTION"]').attr('id');
    var  fgstg = $(this).parent().parent().find('[id*="FG_STAGE"]').attr('id');

    if($(this).is(':checked')==true) {
      $('#'+inhpro).prop('checked',false);
      $('#'+fgstg).prop('checked',false);
    }
});

$('#exp2').on("change","[id*='FG_STAGE']",function(event){

    var  inh2 = $(this).parent().parent().find('[id*="INHOUSE_PRODUCTION"]').attr('id');
    var  jb2 = $(this).parent().parent().find('[id*="JOB_WORKER"]').attr('id');

    if($(this).is(':checked')==true) {
      $('#'+inh2).prop('checked',false);
      $('#'+jb2).prop('checked',false);
    }
});

</script>
@endpush