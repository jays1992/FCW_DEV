@extends('layouts.app')
@section('content')
    

    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[238,'index'])}}" class="btn singlebt">Machine Wise Item Info</a>
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

    <form id="frm_trn_se" onsubmit="return validateForm()"  method="POST" class="needs-validation"  >
    <div class="container-fluid filter">

	<div class="inner-form">
	
		<div class="row">
      <div class="col-lg-2 pl"><p>Machine No </p></div>
      <div class="col-lg-2 pl">
          <input type="text" name="MACHINE_popup" id="txtmachine_popup" class="form-control mandatory"  autocomplete="off" readonly/>
          <input type="hidden" name="MACHINEID_REF" id="MACHINEID_REF" class="form-control" autocomplete="off" />
      </div>              
      <div class="col-lg-2 pl"><p>Machine Description</p></div>
      <div class="col-lg-2 pl">
          <input type="text" name="MACHINENAME" id="MACHINENAME" class="form-control"   autocomplete="off" readonly/>
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
                              <th width="10%">Item Code	<input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1" value="1"></th>
                              <th hidden>Item_ID</th>
                              <th  width="15%">Item Name</th>
                              <th>Main UOM</th>
                              <th  hidden>MAIN_UOMID_REF</th>
                              <th style="width:100px !important;">Produce Qty</th>
                              <th style="width:150px !important;">Cycle Time</th>                          
                              <th >Number of <br>Operators Required</th>                          
                              <th style="width:250px !important;">Remarks</th>                          
                              <th  style="width:100px !important;">Action</th>
                          </tr>
                  </thead>
<tbody id="tbody1">
<tr  class="participantRow">
    <td><input type="text" name="popupITEMID_0" id="popupITEMID_0" class="form-control"  autocomplete="off" style="width:150px;"  readonly/></td>
    <td hidden><input type="text" name="ITEMID_REF_0" id="ITEMID_REF_0" class="form-control" autocomplete="off" /></td>
    
    <td><input type="text" name="ItemName_0" id="ItemName_0" class="form-control"  autocomplete="off"  readonly style="width: 100%;"/></td>
    <td><input type="text" name="popupMUOM_0" id="popupMUOM_0" class="form-control"  autocomplete="off"  readonly/></td>
    <td  hidden ><input type="text" name="MAIN_UOMID_REF_0" id="MAIN_UOMID_REF_0" class="form-control"  autocomplete="off" /></td>
    
    <td><input type="text" name="PRODUCE_QTY_0" id="PRODUCE_QTY_0" class="form-control three-digits" style="width:130px;" maxlength="13" autocomplete="off"  /></td>
    <td><input type="text" name="CYCLE_TIME_0" id="CYCLE_TIME_0" class="form-control"  maxlength="30" autocomplete="off"  /> </td>
    <td><input type="text" name="REQ_OPERATORS_NO_0" id="REQ_OPERATORS_NO_0" class="form-control" style="width:100%;" maxlength="4" autocomplete="off"  /> </td>
    <td><input type="text" name="REMARKS_0" id="REMARKS_0" class="form-control" style="width:100%;" maxlength="200" autocomplete="off"  /> </td>

    <td align="center" style="display: inline-block" ><button class="btn add material" title="add" data-toggle="tooltip" type="button" ><i class="fa fa-plus"></i></button>
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
<div id="machinepopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width: 600px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='mach_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Machine Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="MachTable" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
      <tr>
        <th class="ROW1" style="width: 10%" align="center">Select</th> 
        <th class="ROW2" style="width: 40%" >Code</th>
        <th class="ROW3" style="width: 40%" >Description</th>
      </tr>
    </thead>
    <tbody>
    <tr>
      <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
      <td class="ROW2"  style="width: 40%">
        <input type="text"  autocomplete="off"  class="form-control"  id="machinecodesearch"  onkeyup="MachineCodeFunction()"/>
      </td>
      <td class="ROW3"  style="width: 40%">
        <input type="text"  autocomplete="off"  class="form-control"  id="machinedatesearch"    onkeyup="MachineNameFunction()"/>
      </td>
    </tr>
    </tbody>
    </table>
      <table id="MachnineTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          
        </thead>
        <tbody id="tbody_machrow">       
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<!-- Item Code Dropdown -->
<div id="ITEMIDpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:60%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='ITEMID_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Item Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="ItemIDTable" class="display nowrap table  table-striped table-bordered" style="width: 100%">
    <thead>
      <tr id="none-select" class="searchalldata" hidden>
            
            <td> <input type="text" name="fieldid" id="hdn_ItemID"/>
            <input type="text" name="fieldid2" id="hdn_ItemID2"/>
            <input type="text" name="fieldid3" id="hdn_ItemID3"/>
            <input type="text" name="fieldid4" id="hdn_ItemID4"/>
            <input type="text" name="fieldid5" id="hdn_ItemID5"/>
            <input type="text" name="fieldid6" id="hdn_ItemID6"/>
            <input type="text" name="fieldid7" id="hdn_ItemID7"/>
            <input type="text" name="fieldid8" id="hdn_ItemID8"/>
            <input type="text" name="fieldid9" id="hdn_ItemID9"/>
            <input type="text" name="fieldid10" id="hdn_ItemID10"/>
            <input type="text" name="fieldid11" id="hdn_ItemID11"/>
            <input type="text" name="fieldid12" id="hdn_ItemID12"/>
            <input type="text" name="fieldid13" id="hdn_ItemID13"/>
            <input type="text" name="fieldid14" id="hdn_ItemID14"/>
            <input type="text" name="fieldid15" id="hdn_ItemID15"/>
            <input type="text" name="fieldid16" id="hdn_ItemID16"/>
            <input type="text" name="fieldid17" id="hdn_ItemID17"/>
            <input type="text" name="fieldid18" id="hdn_ItemID18"/>
            <input type="text" name="fieldid19" id="hdn_ItemID19"/>
            <input type="text" name="fieldid20" id="hdn_ItemID20"/>
            <input type="text" name="hdn_ItemID21" id="hdn_ItemID21" value="0"/>
            <input type="text" name="fieldid22" id="hdn_ItemID22"/>
            <input type="text" name="fieldid23" id="hdn_ItemID23"/>
            <input type="text" name="fieldid24" id="hdn_ItemID24"/>
            <input type="text" name="fieldid25" id="hdn_ItemID25"/>
            </td>
      </tr>
      <!-- <tr class="topnav"><button class="btn topnavbt" id="btnOk"><i class="fa fa-check"></i> OK</button></tr> -->
      <tr>
            <th style="width:10%;text-align: center;" id="all-check" style="width:4%;" >Select</th>
            <th style="width:30%" >Item Code</th>
            <th style="width:30%" >Name</th>
            <th style="width:30%" >Main UOM</th>
      </tr>
    </thead>
    <tbody>
    <tr>
      <td style="width:10%;text-align: center;"><input type="checkbox"  class="js-selectall" data-target=".js-selectall1" /></td>
    <td style="width:30%">
    <input type="text" id="Itemcodesearch" class="form-control" onkeyup="ItemCodeFunction()">
    </td>
    <td style="width:30%">
    <input type="text" id="Itemnamesearch" class="form-control" onkeyup="ItemNameFunction()">
    </td>
    <td style="width:30%">
    <input type="text" id="ItemUOMsearch" class="form-control"  onkeyup="ItemUOMFunction()">
    </td>
    
    </tr>
    </tbody>
    </table>
      <table id="ItemIDTable2" class="display nowrap table  table-striped table-bordered" style="width: 100%;" >
        <thead id="thead2">

        </thead>
        <tbody id="tbody_ItemID">               
          
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Item Code Dropdown-->
<!-- POPUP END-->
@endsection

@push('bottom-css')
<style>
  


</style>
@endpush
@push('bottom-scripts')
<script>

//------------------------
//Machine Starts
//------------------------
let sgltid = "#MachnineTable2";
      let sgltid2 = "#MachTable";
      let sglheaders = document.querySelectorAll(sgltid2 + " th");

      // Sort the table element when clicking on the table headers
      sglheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(sgltid, ".clsmachine", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function MachineCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("machinecodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("MachnineTable2");
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

  function MachineNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("machinedatesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("MachnineTable2");
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

  
$("#txtmachine_popup").focus(function(event){
  
    $('#tbody_machrow').html('Loading...');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url:'{{route("master",[238,"getmachines"])}}',
        type:'POST',
        success:function(data) {
            $('#tbody_machrow').html(data);
            bindMachineEvents();
        },
        error:function(data){
            console.log("Error: Something went wrong.");
            $('#tbody_machrow').html('');
        },
    });        
     $("#machinepopup").show();
     event.preventDefault();
}); 

$("#mach_closePopup").on("click",function(event){ 
    $("#machinepopup").hide();
    event.preventDefault();
});
function bindMachineEvents(){

        $('.clsmachine').click(function(){

         
          var id = $(this).attr('id');
          var txtval =    $("#txt"+id+"").val();
          var texdesc =   $("#txt"+id+"").data("desc");
          var txtccname =   $("#txt"+id+"").data("ccname");
          $('#tbody_machrow').html('Checking machine data...');
        //---------------------------
        $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
        $.ajax({
            url:'{{route("master",[238,"checkmachine"])}}',
            type:'POST',
            data:{'machineid':txtval},
            success:function(data) {
              if(data.exists) {
                    showAlert("Machine already created. For creating new machine please first cancel the previous machine.");
                    $("#machinepopup").hide();
                    $("#machinecodesearch").val(''); 
                    $("#machinedatesearch").val(''); 
                    MachineCodeFunction();
                    $(this).prop("checked",false);
              }
              if(data.notexists) {
                $("#txtmachine_popup").val(texdesc);
                $("#txtmachine_popup").blur();
                $("#MACHINEID_REF").val(txtval);
                $("#MACHINENAME").val(txtccname);
                
                $("#machinepopup").hide();
                $("#machinecodesearch").val(''); 
                $("#machinedatesearch").val(''); 
                MachineCodeFunction();
                $(this).prop("checked",false);

                }           

                
            },
            error:function(data){
                console.log("Error: Something went wrong.");
                $('#tbody_machrow').html('');
            },
        });  

        //---------------------------
          event.preventDefault();

        });
  }
//Machine Ends
//------------------------
//------------------------
  //Item ID Dropdown
  let itemtid = "#ItemIDTable2";
      let itemtid2 = "#ItemIDTable";
      let itemtidheaders = document.querySelectorAll(itemtid2 + " th");

      // Sort the table element when clicking on the table headers
      itemtidheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(itemtid, ".clsitemid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function ItemCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Itemcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("ItemIDTable2");
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

      function ItemNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Itemnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("ItemIDTable2");
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


      function ItemPartnoFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Itempartsearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("ItemIDTable2");
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

      function ItemUOMFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("ItemUOMsearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("ItemIDTable2");
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
      

  $('#Material').on('focus','[id*="popupITEMID"]',function(event){

    var MACHINEID_REF = $("#MACHINEID_REF").val();

    if(MACHINEID_REF ===""){
          showAlert('Please select Machine No.');
          return false;
    }
    else
    {
                
        $("#tbody_ItemID").html('Loading...');
          $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          });
          $.ajax({
              url:'{{route("master",[238,"getItemDetails"])}}',
              type:'POST',
              data:{},
              success:function(data) {
                $("#tbody_ItemID").html(data);    
                bindItemEvents();   
                $('.js-selectall').prop('disabled', true);                      
              },
              error:function(data){
                console.log("Error: Something went wrong.");
                $("#tbody_ItemID").html('');                        
              },
          }); 
        

        $("#ITEMIDpopup").show();
        var id = $(this).attr('id');
        var id2 = $(this).parent().parent().find('[id*="ITEMID_REF"]').attr('id');
        var id3 = $(this).parent().parent().find('[id*="ItemName"]').attr('id');
       // var id4 = $(this).parent().parent().find('[id*="ItemPartno"]').attr('id');
       // var id5 = $(this).parent().parent().find('[id*="Itemspec"]').attr('id');
        var id6 = $(this).parent().parent().find('[id*="itemuom"]').attr('id');
       // var id7 = $(this).parent().parent().find('[id*="SLID_REF"]').attr('id');

        //----------------------
        $("#ITEMIDpopup").show();
        var id = $(this).attr('id');
        var id2 = $(this).parent().parent().find('[id*="ITEMID_REF"]').attr('id');
        var id3 = $(this).parent().parent().find('[id*="ItemName"]').attr('id');
        var id4 = $(this).parent().parent().find('[id*="popupMUOM"]').attr('id');
        var id5 = $(this).parent().parent().find('[id*="MAIN_UOMID_REF"]').attr('id');
      

        $('#hdn_ItemID').val(id);
        $('#hdn_ItemID2').val(id2);
        $('#hdn_ItemID3').val(id3);
        $('#hdn_ItemID4').val(id4);
        $('#hdn_ItemID5').val(id5);
        $('#hdn_ItemID6').val(id6);
      

        var r_count = 0;

        
        var ItemID = [];
        $('#exp2').find('.participantRow').each(function(){
          if($(this).find('[id*="ITEMID_REF"]').val() != '')
          {
            ItemID.push($(this).find('[id*="ITEMID_REF"]').val());
          }
        });
        $('#hdn_ItemID19').val(ItemID.join(', '));

        
        event.preventDefault();

    }  //ELSE

        event.preventDefault();

  }); //item focus

  $("#ITEMID_closePopup").click(function(event){
    $("#ITEMIDpopup").hide();
  });





//-----------------------------------
function bindItemEvents()
    {

      $('#ItemIDTable2').off(); 

      $('.js-selectall').change(function()
      { 
        //select all checkbox
        var isChecked = $(this).prop("checked");
        var selector = $(this).data('target');
        $(selector).prop("checked", isChecked);
        
        
        $('#ItemIDTable2').find('.clsitemid').each(function(){
          var fieldid = $(this).attr('id');
          var txtval =   $("#txt"+fieldid+"").val();
          var texdesc =  $("#txt"+fieldid+"").data("desc");
          var fieldid2 = $(this).find('[id*="itemname"]').attr('id');
          var txtname =  $("#txt"+fieldid2+"").val();
          var txtspec =  $("#txt"+fieldid2+"").data("desc");
          var fieldid3 = $(this).find('[id*="itemuom"]').attr('id');
          var txtmuomid =  $("#txt"+fieldid3+"").val();
          var txtmuom =  $(this).find('[id*="itemuom"]').text();

          
          var rcount1 = parseInt($(this).closest('table').find('.clsitemid').length);

          var rcount2 = $('#hdn_ItemID20').val();
          var r_count2 = 0;
         
        var GridRow2 = [];
        $('#exp2').find('.participantRow').each(function(){
          if($(this).find('[id*="ITEMID_REF"]').val() != '')
          {
            var   rowitem = $(this).find('[id*="ITEMID_REF"]').val();
            GridRow2.push(rowitem);
            r_count2 = parseInt(r_count2) + 1;
          }
        });
        
       
        var itemids =  $('#hdn_ItemID19').val();
       
            if($(this).find('[id*="chkId"]').is(":checked") == true) 
            {
              rcount1 = parseInt(rcount2)+parseInt(rcount1);
              if(parseInt(r_count2) >= parseInt(rcount1))
              {
                $('#hdn_ItemID').val('');
                    $('#hdn_ItemID2').val('');
                    $('#hdn_ItemID3').val('');
                    $('#hdn_ItemID4').val('');
                    $('#hdn_ItemID5').val('');
                    $('#hdn_ItemID6').val('');
                    $('#hdn_ItemID7').val('');
                    $('#hdn_ItemID8').val('');
                    $('#hdn_ItemID9').val('');
                    $('#hdn_ItemID10').val('');
                    $('#hdn_ItemID11').val('');
                    $('#hdn_ItemID12').val('');
                    $('#hdn_ItemID13').val('');
                    $('#hdn_ItemID14').val('');
                    $('#hdn_ItemID15').val('');
                    $('#hdn_ItemID16').val('');
                    $('#hdn_ItemID17').val('');
                    $('#hdn_ItemID18').val('');
                    $('#hdn_ItemID19').val('');
                    $('#hdn_ItemID20').val('');
                    $('#hdn_ItemID22').val('');
                    $('#hdn_ItemID23').val('');
                    txtval = '';
                    texdesc = '';
                    txtname = '';
                    txtmuom = '';
                    txtauom = '';
                    txtmuomid = '';


                    $('.js-selectall').prop("checked", false);
                    $("#ITEMIDpopup").hide();
                    return false;
              }
              var txtrow_item = txtval;
              if(jQuery.inArray(txtrow_item, GridRow2) !== -1)
              {
                    $("#ITEMIDpopup").hide();
                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn").hide();
                    $("#OkBtn1").show();
                    $("#AlertMessage").text('Item already exists.');
                    $("#alert").modal('show');
                    $("#OkBtn1").focus();
                    highlighFocusBtn('activeOk1');
                    $('#hdn_ItemID').val('');
                    $('#hdn_ItemID2').val('');
                    $('#hdn_ItemID3').val('');
                    $('#hdn_ItemID4').val('');
                    $('#hdn_ItemID5').val('');
                    $('#hdn_ItemID6').val('');
                    $('#hdn_ItemID7').val('');
                    $('#hdn_ItemID8').val('');
                    $('#hdn_ItemID9').val('');
                    $('#hdn_ItemID10').val('');
                    $('#hdn_ItemID11').val('');
                    $('#hdn_ItemID12').val('');
                    $('#hdn_ItemID13').val('');
                    $('#hdn_ItemID14').val('');
                    $('#hdn_ItemID15').val('');
                    $('#hdn_ItemID16').val('');
                    $('#hdn_ItemID17').val('');
                    $('#hdn_ItemID18').val('');
                    $('#hdn_ItemID19').val('');
                    $('#hdn_ItemID20').val('');
                    $('#hdn_ItemID22').val('');
                    $('#hdn_ItemID23').val('');
                    txtval = '';
                    texdesc = '';
                    txtname = '';
                    txtmuom = '';
                    txtauom = '';
                    txtmuomid = '';

                    $('.js-selectall').prop("checked", false);
                    $("#ITEMIDpopup").hide();
                    return false;
              }

              if(itemids.indexOf(txtval) != -1  )
              {
                            $("#ITEMIDpopup").hide();
                            $("#YesBtn").hide();
                            $("#NoBtn").hide();
                            $("#OkBtn").hide();
                            $("#OkBtn1").show();
                            $("#AlertMessage").text('Item already exists.');
                            $("#alert").modal('show');
                            $("#OkBtn1").focus();
                            highlighFocusBtn('activeOk1');
                            $('#hdn_ItemID').val('');
                            $('#hdn_ItemID2').val('');
                            $('#hdn_ItemID3').val('');
                            $('#hdn_ItemID4').val('');
                            $('#hdn_ItemID5').val('');
                            $('#hdn_ItemID6').val('');
                            $('#hdn_ItemID7').val('');
                            $('#hdn_ItemID8').val('');
                            $('#hdn_ItemID9').val('');
                            $('#hdn_ItemID10').val('');
                            $('#hdn_ItemID11').val('');
                            $('#hdn_ItemID12').val('');
                            $('#hdn_ItemID13').val('');
                            $('#hdn_ItemID14').val('');
                            $('#hdn_ItemID15').val('');
                            $('#hdn_ItemID16').val('');
                            $('#hdn_ItemID17').val('');
                            $('#hdn_ItemID18').val('');
                            $('#hdn_ItemID19').val('');
                            $('#hdn_ItemID20').val('');
                            $('#hdn_ItemID22').val('');
                            $('#hdn_ItemID23').val('');
                            txtval = '';
                            texdesc = '';
                            txtname = '';
                            txtmuom = '';
                            txtauom = '';
                            txtmuomid = '';

                            $('.js-selectall').prop("checked", false);
                            $("#ITEMIDpopup").hide();
                            return false;
              }
                  
                  if($('#hdn_ItemID').val() == "" && txtval != '')
                  {
                    

                    var $tr = $('.material').closest('table');
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

                        $clone.find('.remove').removeAttr('disabled'); 
                        $clone.find('[id*="popupITEMID"]').val(texdesc);
                        $clone.find('[id*="ITEMID_REF"]').val(txtval);
                        $clone.find('[id*="ItemName"]').val(txtname);
                        $clone.find('[id*="popupMUOM"]').val(txtmuom);
                        $clone.find('[id*="MAIN_UOMID_REF"]').val(txtmuomid);
                       
                        $tr.closest('table').append($clone);   
                        var rowCount = $('#Row_Count1').val();
                        rowCount = parseInt(rowCount)+1;
                        $('#Row_Count1').val(rowCount);

                     

                      applyForceNum();
                      event.preventDefault();
                  }
                  else
                  {
                      var txtid= $('#hdn_ItemID').val();
                      var txt_id2= $('#hdn_ItemID2').val();
                      var txt_id3= $('#hdn_ItemID3').val();
                      var txt_id4= $('#hdn_ItemID4').val();
                      var txt_id5= $('#hdn_ItemID5').val();
                      var txt_id6= $('#hdn_ItemID6').val();
                      var txt_id7= $('#hdn_ItemID7').val();
                      

                      $('#'+txtid).val(texdesc);
                      $('#'+txt_id2).val(txtval);
                      $('#'+txt_id3).val(txtname);
                      $('#'+txt_id4).val(txtmuom);
                      $('#'+txt_id5).val(txtmuomid);
                     
                      
                        // $("#ITEMIDpopup").hide();
                        $('#hdn_ItemID').val('');
                        $('#hdn_ItemID2').val('');
                        $('#hdn_ItemID3').val('');
                        $('#hdn_ItemID4').val('');
                        $('#hdn_ItemID5').val('');
                        $('#hdn_ItemID6').val('');
                        $('#hdn_ItemID7').val('');
                        $('#hdn_ItemID8').val('');
                        $('#hdn_ItemID9').val('');
                        $('#hdn_ItemID10').val('');
                        $('#hdn_ItemID11').val('');
                        $('#hdn_ItemID12').val('');
                        $('#hdn_ItemID13').val('');
                        $('#hdn_ItemID14').val('');
                        $('#hdn_ItemID15').val('');
                        $('#hdn_ItemID16').val('');
                        $('#hdn_ItemID22').val('');
                        $('#hdn_ItemID23').val('');
                        $('#hdn_ItemID24').val('');
                        event.preventDefault();
                  }

                  $('.js-selectall').prop("checked", false);
                  // $("#ITEMIDpopup").reload();
                  $('#ITEMIDpopup').hide();
                  event.preventDefault();
                  
            }
            // else if($(this).is(":checked") == false) 
            // {
            //  UNCHECKED
            // }
          $("#Itemcodesearch").val(''); 
          $("#Itemnamesearch").val(''); 
          $("#ItemUOMsearch").val(''); 
          $("#ItemGroupsearch").val(''); 
          $("#ItemCategorysearch").val(''); 
          $("#ItemStatussearch").val(''); 
          $('.remove').removeAttr('disabled'); 
          ItemCodeFunction();
          event.preventDefault();
        });

        $('#ITEMIDpopup').hide();
        return false;
        event.preventDefault();


    }); //binditem event

    //single check box selected from item popup
    $('[id*="chkId"]').change(function()
    {
        var fieldid = $(this).parent().parent().attr('id');
        var txtval =   $("#txt"+fieldid+"").val();
        var texdesc =  $("#txt"+fieldid+"").data("desc");
        var fieldid2 = $(this).parent().parent().children('[id*="itemname"]').attr('id');
        var txtname =  $("#txt"+fieldid2+"").val();
        var txtspec =  $("#txt"+fieldid2+"").data("desc");
        var fieldid3 = $(this).parent().parent().children('[id*="itemuom"]').attr('id');
        var txtmuomid =  $("#txt"+fieldid3+"").val();
        var txtmuom =  $(this).parent().parent().children('[id*="itemuom"]').text();

        
        var GridRow2 = [];
        $('#exp2').find('.participantRow').each(function(){
          if($(this).find('[id*="ITEMID_REF"]').val() != '')
          {
            var rowitem =$(this).find('[id*="ITEMID_REF"]').val();
            GridRow2.push(rowitem);
          }
        });
        
       
        var itemids =  $('#hdn_ItemID19').val();
       
            if($(this).is(":checked") == true) 
            {
              var txtrow_item = txtval;
              if(jQuery.inArray(txtrow_item, GridRow2) !== -1)
              {
                    $("#ITEMIDpopup").hide();
                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn").hide();
                    $("#OkBtn1").show();
                    $("#AlertMessage").text('Item already exists.');
                    $("#alert").modal('show');
                    $("#OkBtn1").focus();
                    highlighFocusBtn('activeOk1');
                    $('#hdn_ItemID').val('');
                    $('#hdn_ItemID2').val('');
                    $('#hdn_ItemID3').val('');
                    $('#hdn_ItemID4').val('');
                    $('#hdn_ItemID5').val('');
                    $('#hdn_ItemID6').val('');
                    $('#hdn_ItemID7').val('');
                    $('#hdn_ItemID8').val('');
                    $('#hdn_ItemID9').val('');
                    $('#hdn_ItemID10').val('');
                    $('#hdn_ItemID11').val('');
                    $('#hdn_ItemID12').val('');
                    $('#hdn_ItemID13').val('');
                    $('#hdn_ItemID14').val('');
                    $('#hdn_ItemID15').val('');
                    $('#hdn_ItemID16').val('');
                    $('#hdn_ItemID17').val('');
                    $('#hdn_ItemID18').val('');
                    $('#hdn_ItemID19').val('');
                    $('#hdn_ItemID20').val('');
                    $('#hdn_ItemID22').val('');
                    txtval = '';
                    texdesc = '';
                    txtname = '';
                    txtmuom = '';
                    txtmuomid = '';
                  
                    $('.js-selectall').prop("checked", false);
                    $("#ITEMIDpopup").hide();
                    return false;
              }

                 
                      if($('#hdn_ItemID').val() == "" && txtval != '')
                      {
                        var txtid= $('#hdn_ItemID').val();
                        var txt_id2= $('#hdn_ItemID2').val();
                        var txt_id3= $('#hdn_ItemID3').val();
                        var txt_id4= $('#hdn_ItemID4').val();
                        var txt_id5= $('#hdn_ItemID5').val();
                        var txt_id6= $('#hdn_ItemID6').val();
                        var txt_id7= $('#hdn_ItemID7').val();
                        var txt_id8= $('#hdn_ItemID8').val();
                        var txt_id9= $('#hdn_ItemID9').val();
                        var txt_id10= $('#hdn_ItemID10').val();
                        var txt_id11= $('#hdn_ItemID11').val();
                        var txt_id12= $('#hdn_ItemID12').val();
                        var txt_id13= $('#hdn_ItemID13').val();
                        var txt_id14= $('#hdn_ItemID14').val();
                        var txt_id15= $('#hdn_ItemID15').val();
                        var txt_id16= $('#hdn_ItemID16').val();
                        var txt_id22= $('#hdn_ItemID22').val();

                        var $tr = $('.material').closest('table');
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

                        $clone.find('.remove').removeAttr('disabled'); 
                        
                        $clone.find('[id*="popupITEMID"]').val(texdesc);
                        $clone.find('[id*="ITEMID_REF"]').val(txtval);
                        $clone.find('[id*="ItemName"]').val(txtname);
                        $clone.find('[id*="popupMUOM"]').val(txtmuom);
                        $clone.find('[id*="MAIN_UOMID_REF"]').val(txtmuomid);
                        

                        $tr.closest('table').append($clone);   
                        var rowCount = $('#Row_Count1').val();
                            rowCount = parseInt(rowCount)+1;
                        $('#Row_Count1').val(rowCount);

                        
                        applyForceNum();
                        event.preventDefault();
                      }
                      else
                      {
                          var txtid= $('#hdn_ItemID').val();
                          var txt_id2= $('#hdn_ItemID2').val();
                          var txt_id3= $('#hdn_ItemID3').val();
                          var txt_id4= $('#hdn_ItemID4').val();
                          var txt_id5= $('#hdn_ItemID5').val();
                          var txt_id6= $('#hdn_ItemID6').val();
                          var txt_id7= $('#hdn_ItemID7').val();
                          

                          $('#'+txtid).val(texdesc);
                          $('#'+txt_id2).val(txtval);
                          $('#'+txt_id3).val(txtname);
                          $('#'+txt_id4).val(txtmuom);
                          $('#'+txt_id5).val(txtmuomid);
                         
                      
                          $('#hdn_ItemID').val('');
                          $('#hdn_ItemID2').val('');
                          $('#hdn_ItemID3').val('');
                          $('#hdn_ItemID4').val('');
                          $('#hdn_ItemID5').val('');
                          $('#hdn_ItemID6').val('');
                          $('#hdn_ItemID7').val('');
                          $('#hdn_ItemID8').val('');
                          $('#hdn_ItemID9').val('');
                          $('#hdn_ItemID10').val('');
                          $('#hdn_ItemID11').val('');
                          $('#hdn_ItemID12').val('');
                          $('#hdn_ItemID13').val('');
                          $('#hdn_ItemID14').val('');
                          $('#hdn_ItemID15').val('');
                          $('#hdn_ItemID16').val('');
                          $('#hdn_ItemID22').val('');
                            
                      }
                      $('.js-selectall').prop("checked", false);
                      $("#ITEMIDpopup").hide();
                      return false;
                      //event.preventDefault();
            }
            else if($(this).is(":checked") == false) 
            {
                // CHECKBOX UNCHECKED
                
            }
        $("#Itemcodesearch").val(''); 
        $("#Itemnamesearch").val(''); 
        $("#ItemUOMsearch").val(''); 
        $("#ItemGroupsearch").val(''); 
        $("#ItemCategorysearch").val(''); 
        $("#ItemStatussearch").val(''); 
        $('.remove').removeAttr('disabled'); 
        ItemCodeFunction();
        event.preventDefault();
      });
    }
//Item ID Dropdown Ends
//------------------------
     
$(document).ready(function(e) {

  var Material = $("#Material").html(); 
  $('#hdnmaterial').val(Material);

 
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

  $tr.closest('table').append($clone);         
  var rowCount1 = $('#Row_Count1').val();
  rowCount1 = parseInt(rowCount1)+1;
  $('#Row_Count1').val(rowCount1);
  $clone.find('.remove').removeAttr('disabled'); 

  applyForceNum();

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
  window.location.href = "{{route('master',[238,'add'])}}";
}//fnUndoYes


window.fnUndoNo = function (){
    $("#MACHINEID_REF").focus();
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

   

}); //ready
</script>

@endpush

@push('bottom-scripts')
<script>

$(document).ready(function() {
  applyForceNum();

});



function validateForm(){
 
        $("#FocusId").val('');
        var MACHINEID_REF     =   $.trim($("#MACHINEID_REF").val());        
        if(MACHINEID_REF ===""){
            $("#FocusId").val($("#MACHINEID_REF"));
            $("#ProceedBtn").focus();
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text('Please select Machine No.');
            $("#alert").modal('show');
            $("#OkBtn1").focus();
            return false;
        }
        else{

                event.preventDefault();
                var allblank = [];
                var allblank2 = [];
                var allblank3 = [];
                var allblank4 = [];
                var allblank5 = [];

                $('#exp2').find('.participantRow').each(function(){
                  
                  if($.trim($(this).find("[id*=popupITEMID]").val())!=""){
                      allblank.push('true');
                  }
                  else{
                      allblank.push('false');
                  } 


                  var prqty  = $.trim( $(this).find('[id*="PRODUCE_QTY"]').val() );
                  if(isNaN(prqty) || prqty=="" || parseFloat(prqty)<=0)
                  {
                    prqty = "";
                  }

                  if(prqty!=""){
                      allblank2.push('true');
                  }
                  else{
                      allblank2.push('false');
                  } 

                  var cytime  = $.trim( $(this).find('[id*="CYCLE_TIME"]').val() );
                  if(cytime!=""){
                      allblank3.push('true');
                  }
                  else{
                      allblank3.push('false');
                  } 

                  
                  var rono  = $.trim( $(this).find('[id*="REQ_OPERATORS_NO"]').val() );
                  if(isNaN(rono) || rono=="" || parseInt(rono)<=0)
                  {
                    rono = "";
                  }

                  if(rono!=""){
                      allblank4.push('true');
                  }
                  else{
                      allblank4.push('false');
                  } 
                

                  
                }); 

                //-------------
          }


        if(jQuery.inArray("false", allblank) !== -1){
          $("#alert").modal('show');
          $("#AlertMessage").text('Please select  Item in Material Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
          }
        
        else if(jQuery.inArray("false", allblank2) !== -1){
          $("#alert").modal('show');
          $("#AlertMessage").text('Produce Qty shoud be greater than zero in Material Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
          }  
        else if(jQuery.inArray("false", allblank3) !== -1){
          $("#alert").modal('show');
          $("#AlertMessage").text('Please enter value for Cycle Time in Material Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
          }  
        else if(jQuery.inArray("false", allblank4) !== -1){
          $("#alert").modal('show');
          $("#AlertMessage").text('Number of Operators Required shoud be greater than zero in Material Tab.');
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
    url:'{{ route("master",[238,"save"])}}',
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
    window.location.href = '{{route("master",[238,"index"]) }}';
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



$('#Material').on('blur',"[id*='PRODUCE_QTY']",function()
{
    var qty2 = $.trim($(this).val());
    if(isNaN(qty2) || qty2=="" )
    {
      qty2 = 0;
    }  
    if(intRegex.test(qty2))
    {
      $(this).val((qty2 +'.000'));
    }
   
    event.preventDefault();
});  

$('#Material').on('blur',"[id*='REQ_OPERATORS_NO']",function()
{
    var qty3 = $.trim($(this).val());
    if(isNaN(qty3) || qty3=="" )
    {
      qty3 = 0;
    }  

    $(this).val(Math.floor(qty3));
    event.preventDefault();
});  

function applyForceNum(){

  $("[id*='PRODUCE_QTY']").ForceNumericOnly();
  $("[id*='REQ_OPERATORS_NO']").ForceNumericOnly();

}

$( function() {
 $('#exp2').on('keyup','.two-digits',function(){
   if($(this).val().indexOf('.')!=-1){         
       if($(this).val().split(".")[1].length > 2){                
		   $(this).val('');
		   $("#alert").modal('show');
			$("#AlertMessage").text('Enter value till two decimal only.');
			$("#YesBtn").hide(); 
			$("#NoBtn").hide();  
			$("#OkBtn1").show();
			$("#OkBtn1").focus();
			$("#OkBtn").hide();
			highlighFocusBtn('activeOk1');
       }  
    }            
    return this; //for chaining
 });
});

$( function() {
	
 $('#exp2').on('keyup','.three-digits',function(){
   if($(this).val().indexOf('.')!=-1){         
       if($(this).val().split(".")[1].length > 3){                
		   $(this).val('');
		   $("#alert").modal('show');
			$("#AlertMessage").text('Enter value till three decimal only.');
			$("#YesBtn").hide(); 
			$("#NoBtn").hide();  
			$("#OkBtn1").show();
			$("#OkBtn1").focus();
			$("#OkBtn").hide();
			highlighFocusBtn('activeOk1');
       }  
    }            
    return this; //for chaining
 });
});

$( function() {
 $('#exp2').on('keyup','.five-digits',function(){
   if($(this).val().indexOf('.')!=-1){         
       if($(this).val().split(".")[1].length > 5){                
		   $(this).val('');
		   $("#alert").modal('show');
			$("#AlertMessage").text('Enter value till five decimal only.');
			$("#YesBtn").hide(); 
			$("#NoBtn").hide();  
			$("#OkBtn1").show();
			$("#OkBtn1").focus();
			$("#OkBtn").hide();
			highlighFocusBtn('activeOk1');
       }  
    }            
    return this; //for chaining
 });
});

$( function() {
 $('#exp2').on('keyup','.four-digits',function(){
   if($(this).val().indexOf('.')!=-1){         
       if($(this).val().split(".")[1].length > 4){                
		   $(this).val('');
		   $("#alert").modal('show');
			$("#AlertMessage").text('Enter value till four decimal only.');
			$("#YesBtn").hide(); 
			$("#NoBtn").hide();  
			$("#OkBtn1").show();
			$("#OkBtn1").focus();
			$("#OkBtn").hide();
			highlighFocusBtn('activeOk1');
       }  
    }            
    return this; //for chaining
 });

});

</script>
@endpush