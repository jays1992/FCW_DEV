@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                  <a href="{{route('master',[228,'index'])}}" class="btn singlebt">Machine Wise Checklist</a>
                </div><!--col-2-->
                  <div class="col-lg-10 topnav-pd">
                  <button class="btn topnavbt" id="btnAdd" disabled="disabled" ><i class="fa fa-plus"></i> Add</button>
                  <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
                  <button class="btn topnavbt" id="btnSave"  tabindex="3"  ><i class="fa fa-floppy-o"></i> Save</button>
                  <button class="btn topnavbt" id="btnView" disabled="disabled" ><i class="fa fa-eye"></i> View</button>
                  <button class="btn topnavbt" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                  <button class="btn topnavbt" id='btnUndo' ><i class="fa fa-undo"></i> Undo</button>
                  <button class="btn topnavbt" id="btnCancel"  disabled="disabled" ><i class="fa fa-times"></i> Cancel</button>
                  <button class="btn topnavbt" id="btnApprove"  disabled="disabled" ><i class="fa fa-thumbs-o-up"></i> Approved</button>
                  <button class="btn topnavbt"  id="btnAttach"  disabled="disabled" ><i class="fa fa-link"></i> Attachment</button>
                  <button class="btn topnavbt" id="btnExit"><i class="fa fa-power-off"></i> Exit</button>
              </div><!--col-10-->

            </div><!--row-->
    </div><!--topnav-->	
   
    <div class="container-fluid purchase-order-view filter">   
         <form id="frm_mst_add" method="POST" onsubmit="return validateForm()" class="needs-validation"  > 
                @CSRF
               
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
  
                  <div class="row">
                    <div class="col-lg-2 pl"><p>CheckList No </p></div>
                    <div class="col-lg-2 pl">
                        <input type="text" name="CHECKLIST_popup" id="txtchecklist_popup" class="form-control mandatory"  autocomplete="off" readonly/>
                        <input type="hidden" name="CHECKLIST_REF" id="CHECKLIST_REF" class="form-control" autocomplete="off" />
                    </div>              
                    <div class="col-lg-2 pl"><p>CheckList Description</p></div>
                    <div class="col-lg-2 pl">
                        <input type="text" name="CHECKLISTNAME" id="CHECKLISTNAME" class="form-control"   autocomplete="off" readonly/>
                    </div>
                  </div>    
                    

                <div class="row">
                  <div class="col-lg-6 pl">
                    <div class=" table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:400px;width:1050px;" >
                      <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                        <thead id="thead1"  style="position: sticky;top: 0">
                          <tr>
                          <th style="width:150px;">MP Code <input class="form-control" type="hidden" name="Row_Count" id ="Row_Count"> 
                              <input type="hidden" id="focusid" >
                            </th>
                          <th style="width:200px;">MP Description</th>
                          <th hidden>vid</th>
                          <th style="width:150px;">MSP Code</th>
                          <th style="width:200px;">MSP Description</th>
                          <th style="width:100px;">Standard Value</th>
                          <th width="5%">Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr  class="participantRow">
                            <td><input type="text" name="txtFORM_popup_0" id="txtFORM_popup_0" class="form-control"  autocomplete="off"  readonly style="width:100%;" /></td>
                            <td  hidden><input type="text" name="FORMID_0" id="hdnFORMID_0" class="form-control" autocomplete="off" /></td>
                            <td><input  class="form-control" style="width: 100%" type="text" name="FORMNAME_0" id ="FORMNAME_0" style="width:200px;" autocomplete="off" readonly></td>
                            <td><input type="text" name="txtLISTPOP1_popup_0" id="txtLISTPOP1_popup_0" class="form-control"  autocomplete="off"  readonly style="width:100%;" /></td>
                            <td  hidden><input type="text" name="LISTPOP1ID_0" id="hdnLISTPOP1ID_0" class="form-control" autocomplete="off" /></td>
                            <td><input  class="form-control" style="width: 100%" type="text" name="DESC2_0" id ="DESC2_0"  autocomplete="off" readonly></td>                            
                            <td><input  class="form-control" style="width: 100%" type="text" name="STANDARD_VAL_0" id ="STANDARDID_VAL_0" maxlength="200" autocomplete="off" ></td>                            
                            <td align="center" >
                                <button class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></button>
                                <button class="btn remove" title="Delete" id="btnremove" data-toggle="tooltip" disabled><i class="fa fa-trash" ></i></button>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>

          </div>
        </form>
       
    </div><!--purchase-order-view-->
@endsection
@section('alert')
<!-- Alert -->
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
            <button class="btn alertbt" name='YesBtn' id="YesBtn" data-funcname="fnSaveData">
              <div id="alert-active" class="activeYes"></div>Yes
            </button>
            <button class="btn alertbt" name='NoBtn' id="NoBtn"   data-funcname="fnUndoNo" >
              <div id="alert-active" class="activeNo"></div>No
            </button>
            <button onclick="setfocus();"  class="btn alertbt" name='OkBtn' id="OkBtn" style="display:none;margin-left: 90px;">
            <div id="alert-active" class="activeOk"></div>OK</button>
            <button class="btn alertbt" name='OkBtn2' id="OkBtn2" style="display:none;margin-left: 90px;">
              <div id="alert-active" class="activeOk2"></div>OK</button>
            
        </div><!--btdiv-->
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Alert -->
<!-- POPUP -->
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
        <th class="ROW2" style="width: 40%">Code</th>
        <th  class="ROW3"style="width: 40%">Description</th>
      </tr>
    </thead>
    <tbody>
    <tr>
      <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
      <td class="ROW2"  style="width: 40%">
        <input type="text" autocomplete="off"  class="form-control" id="machinecodesearch" onkeyup="MachineCodeFunction()" />
      </td>
      <td class="ROW3"  style="width: 40%">
        <input type="text" autocomplete="off"  class="form-control" id="machinedatesearch" onkeyup="MachineNameFunction()" />
      </td>
    </tr>
    </tbody>
    </table>
      <table id="MachnineTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          
        </thead>
        <tbody id="tbody_subglacct">       
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- POPUP-->
<!-- POPUP -->
<div id="checklistpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width: 600px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='checklist_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Checklist Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="ChecklistTable" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
      <tr>
        <th class="ROW1" style="width: 10%" align="center">Select</th> 
        <th class="ROW2" style="width: 40%">Code</th>
        <th  class="ROW3"style="width: 40%">Description</th>
      </tr>
    </thead>
    <tbody>
    <tr>
      <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
      <td class="ROW2"  style="width: 40%">
        <input type="text" autocomplete="off"  class="form-control" id="checklistcodesearch" onkeyup="ChecklistCodeFunction()"/>
      </td>
      <td class="ROW3"  style="width: 40%">
        <input type="text" autocomplete="off"  class="form-control" id="checklistdatesearch" onkeyup="ChecklistNameFunction()"/>
      </td>
    </tr>
    </tbody>
    </table>
      <table id="ChecklistTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          
        </thead>
        <tbody id="tbody_checklist">       
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- POPUP-->
<!-- POPUP-->
<div id="LISTPOP1popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md " style="670px" >
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='LISTPOP1_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>MSP Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="LISTPOP1Table" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
          <tr id="none-select" class="searchalldata" hidden >            
            <td > <input type="text" name="fieldid" id="hdn_LISTPOP1id"/>
              <input type="text" name="fieldid2" id="hdn_LISTPOP1id2"/>
              <input type="text" name="fieldid3" id="hdn_LISTPOP1id3"/>
              <input type="text" name="fieldid4" id="hdn_LISTPOP1id4"/>
              
            </td>
          </tr>
          <tr>
            <th class="ROW1" style="width: 10%" align="center">Select</th> 
            <th class="ROW2" style="width: 40%">Code</th>
            <th  class="ROW3"style="width: 40%">Description</th>
          </tr>
    </thead>
    <tbody>
    <tr>
      <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
      <td class="ROW2"  style="width: 40%">
          <input type="text" class="form-control"  id="LISTPOP1codesearch" id="LISTPOP1codesearch" onkeyup="LISTPOP1CodeFunction()" />
      </td>
      <td class="ROW3"  style="width: 40%">
          <input type="text" class="form-control"  id="country_namesearch" id="LISTPOP1namesearch" onkeyup="LISTPOP1NameFunction()" />
      </td>
    </tr>
    </tbody>
    </table>
      <table id="LISTPOP1Table2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">

        </thead>
        <tbody id="tbody_LISTPOP1">     
        
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- POPUP END-->
<!-- FORMUP-->
<div id="FORMpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='FORM_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>MP Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="FORMTable" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
          <tr id="none-select" class="searchalldata" hidden >            
            <td > <input type="text" name="fieldid" id="hdn_FORMid"/>
              <input type="text" name="fieldid2" id="hdn_FORMid2"/>
              <input type="text" name="fieldid3" id="hdn_FORMid3"/>
            </td>
          </tr>
          <tr>
            <th class="ROW1" style="width: 10%" align="center">Select</th> 
            <th class="ROW2" style="width: 40%">Code</th>
            <th  class="ROW3"style="width: 40%">Description</th>
          </tr>
    </thead>
    <tbody>
    <tr>
      <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
      <td class="ROW2"  style="width: 40%">
         <input type="text" autocomplete="off"  class="form-control" id="FORMcodesearch" onkeyup="FORMCodeFunction()" />
      </td>
      <td class="ROW3"  style="width: 40%">
         <input type="text" autocomplete="off"  class="form-control" id="FORMnamesearch" onkeyup="FORMNameFunction()" />
      </td>
    </tr>
    </tbody>
    </table>
      <table id="FORMTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">

        </thead>
        <tbody id="tbody_FORM">     
        
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- FORMUP END-->


@endsection
<!-- btnSave -->

@push('bottom-scripts')
<script>

function setfocus(){
    var focusid=$.trim($("#focusid").val());
    if(focusid!="" && focus!=undefined){
      $("#"+focusid).focus();
    }
    $("#closePopup").click();
} 

//validae single element
function validateSingleElemnet(element_id){
      var validator =$("#frm_mst_add" ).validate();
         if(validator.element( "#"+element_id+"" )){
            //check duplicate code
          // if(element_id=="CHECKLIST_NO" || element_id=="CHECKLIST_NO" ) {
          //   checkDuplicateCode();

          // }

         }
    }

    // //check duplicate exist code
    function checkDuplicateCode(){
        
        var macid = $.trim($("#MACHINEID_REF").val());
        var chkid = $.trim($("#CHECKLIST_REF").val() );
        
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("master",[228,"codeduplicate"])}}',
            type:'POST',
            data:{'MACHINEID_REF':macid,'CHECKLIST_REF':chkid},
            success:function(data) {
              //console.log(data);
                if(data.exists) {
                    $(".text-danger").hide();
                    showError('ERROR_CHECKLIST_NO',data.msg);
                    $("#CHECKLIST_NO").focus();

                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn2").hide();

                    $("#OkBtn").show();
                    $("#AlertMessage").text(data.msg);
                    $("#alert").modal('show');
                    $("#OkBtn").focus();
                    highlighFocusBtn('activeOk');
                    
                } 
                                               
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });
    }

function validateForm(){

        event.preventDefault();

        var  MACHINEID_REF   = $.trim( $("#MACHINEID_REF").val());
        var  CHECKLIST_NO   = $.trim( $("#CHECKLIST_REF").val());
        

        if(MACHINEID_REF==""){
         
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn2").hide();

          $("#OkBtn").show();
          $("#AlertMessage").text('Please select Machine No.');
          $("#alert").modal('show');
          $("#OkBtn").focus();
          highlighFocusBtn('activeOk');
          return false;
        }

        if(CHECKLIST_NO==""){
         
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn2").hide();

          $("#OkBtn").show();
          $("#AlertMessage").text('Please select Checklist No.');
          $("#alert").modal('show');
          $("#OkBtn").focus();
          highlighFocusBtn('activeOk');
          return false;
        }
       
        
        var ExistArray = []; 
        var allblank1 = [];  
        var allblank2 = []; 
        var allblank3 = []; 
        var allblank4 = []; 

        var texid1    = "";
        var texid2    = ""; 
        var texid3    = "";
        var texid4    = "";

        $("[id*=hdnFORMID]").each(function(){
 
            if($.trim($(this).val()) ==="" ){
              allblank1.push('true');
              texid1 = $(this).attr('id');
            }else{
              allblank1.push('false');
            }


            if($.trim($(this).parent().parent().find('[id*="hdnLISTPOP1ID"]').val()) === "" ){
              allblank2.push('true');
              texid2 = $(this).parent().parent().find('[id*="hdnLISTPOP1ID"]').attr('id');
            }else{
              allblank2.push('false');
            }

            var record_data = $.trim($(this).val())+'_'+$.trim($(this).parent().parent().find('[id*="hdnLISTPOP1ID"]').val());
            if(ExistArray.indexOf(record_data) > -1) {
              allblank3.push('true');
              texid3 =  $(this).parent().parent().find('[id*="FORMNAME"]').attr('id');  
            }
            else{
              allblank3.push('false');
            }

            ExistArray.push(record_data);


            if($.trim($(this).parent().parent().find('[id*="STANDARDID_VAL"]').val()) === "" ){
              allblank4.push('true');
              texid4 = $(this).parent().parent().find('[id*="STANDARDID_VAL"]').attr('id');
            }else{
              allblank4.push('false');
            }
           
        });

          if(jQuery.inArray("true", allblank1) !== -1){
            $("#focusid").val(texid1);
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn").show();
            $("#AlertMessage").text('Please select MP Code.');
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
            $("#AlertMessage").text('Please select MSP Code.');
            $("#alert").modal('show');
            $("#OkBtn").focus();
            highlighFocusBtn('activeOk');
            return false;
          }
          else if(jQuery.inArray("true", allblank3) !== -1){
            $("#focusid").val(texid3);
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn").show();
            $("#AlertMessage").text('Duplicate row. Please check.');
            $("#alert").modal('show');
            $("#OkBtn").focus();
            highlighFocusBtn('activeOk');
            return false;
          }
          else if(jQuery.inArray("true", allblank4) !== -1){
            $("#focusid").val(texid4);
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn").show();
            $("#AlertMessage").text('Please enter Standard Value.');
            $("#alert").modal('show');
            $("#OkBtn").focus();
            highlighFocusBtn('activeOk');
            return false;
          }
          else{

              $("#OkBtn").hide();
              $("#OkBtn2").hide();
              $("#YesBtn").show();
              $("#NoBtn").show();
              $("#alert").modal('show');
              $("#AlertMessage").text('Do you want to save to record.');
              $("#YesBtn").data("funcname","fnSaveData");  
              $("#YesBtn").focus();
              highlighFocusBtn('activeYes');
          }

    
  
}


//------------------------
  //LISTPOP1 Dropdown
  let sqtid = "#LISTPOP1Table2";
      let sqtid2 = "#LISTPOP1Table";
      let salesquotationheaders = document.querySelectorAll(sqtid2 + " th");

      // Sort the table element when clicking on the table headers
      salesquotationheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(sqtid, ".clssqid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function LISTPOP1CodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("LISTPOP1codesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("LISTPOP1Table2");
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

  function LISTPOP1NameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("LISTPOP1namesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("LISTPOP1Table2");
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

  $('#example2').on('focus','[id*="txtLISTPOP1_popup"]',function(event){

        
          var id = $(this).attr('id');
          var id2 = $(this).parent().parent().find('[id*="LISTPOP1ID"]').attr('id');      
          var id3 = $.trim( $(this).parent().parent().find('[id*="DESC2"]').attr('id') ); 
          var id4 = $.trim( $(this).parent().parent().find('[id*="hdnFORMID"]').val() ); 

          if(id4 =="" ){
                
                $("#LISTPOP1popup").hide();
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").hide();
                $("#OkBtn").show();
                $("#AlertMessage").text('Please select MP Code.');
                $("#alert").modal('show');
                $("#OkBtn").focus();
                highlighFocusBtn('activeOk');
                          
                return false;
              }

          $('#hdn_LISTPOP1id').val(id);
          $('#hdn_LISTPOP1id2').val(id2);
          $('#hdn_LISTPOP1id3').val(id3);
          $('#hdn_LISTPOP1id4').val(id4);
        
          $("#LISTPOP1popup").show();
          //$("#tbody_LISTPOP1").html('');
          $("#tbody_LISTPOP1").html('Loading...');
          $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          })
          $.ajax({
              url:'{{route("master",[228,"getmainsubparam"])}}',
              type:'POST',
              success:function(data) {
                $("#tbody_LISTPOP1").html(data);
                BindLISTPOP1Events();
              },
              error:function(data){
                console.log("Error: Something went wrong.");
                $("#tbody_LISTPOP1").html('');
              },
          });

      });

      $("#LISTPOP1_closePopup").click(function(event){
        $("#LISTPOP1popup").hide();
      });

      function BindLISTPOP1Events()
      {
          $(".clsLISTPOP1id").click(function(){
              var fieldid = $(this).attr('id');
              var txtval =    $("#txt"+fieldid+"").val();
              var texdesc =   $("#txt"+fieldid+"").data("desc");
              var texdescdate =   $("#txt"+fieldid+"").data("descdate");
              
              var txtid= $('#hdn_LISTPOP1id').val();
              var txt_id2= $('#hdn_LISTPOP1id2').val();
              var txt_id3= $('#hdn_LISTPOP1id3').val();
              var txt_id4= $('#hdn_LISTPOP1id4').val();


              //-----------------------------
              var  buref =  txt_id4; //todo: set on focus
              var ArrData = [];
              $('#example2').find('.participantRow').each(function(){
                if($(this).find('[id*="hdnFORMID_"]').val() != '')
                {
                  var tmpitem = $(this).find('[id*="hdnFORMID_"]').val()+'-'+$(this).find('[id*="hdnLISTPOP1ID_"]').val();
                  ArrData.push(tmpitem);
                }
              });
              
              var recdata = buref+'-'+txtval;
              if(jQuery.inArray(recdata, ArrData) !== -1){
                $("#LISTPOP1popup").hide();
               
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn2").hide();
                $("#OkBtn").show();
                $("#AlertMessage").text('MSP and MSP Code already exists. Please check.');
                $("#alert").modal('show');
                $("#OkBtn").focus();
                highlighFocusBtn('activeOk1');
                $('#hdn_LISTPOP1id').val('');
                $('#hdn_LISTPOP1id2').val('');
                $('#hdn_LISTPOP1id3').val('');
              

                fieldid = '';
                txtval =   '';
                texdesc =   '';
                texdescdate =   '';
                
                txtid= '';
                txt_id2= '';
                txt_id3= '';
                $("#LISTPOP1codesearch").val(''); 
                $("#LISTPOP1namesearch").val(''); 
                LISTPOP1CodeFunction();
                $(this).prop("checked",false);
                return false;
                
              }

              //-----------------------------

              $('#'+txtid).val(texdesc);
              $('#'+txt_id2).val(txtval);
              $('#'+txt_id3).val(texdescdate);

              

              

              $("#LISTPOP1popup").hide();
              
              $("#LISTPOP1codesearch").val(''); 
              $("#LISTPOP1namesearch").val(''); 
              LISTPOP1CodeFunction();
              $(this).prop("checked",false);
              event.preventDefault();
          });
      }
//------------------------
//------------------------
  //FORM Dropdown
  let frmid = "#FORMTable2";
      let frmid2 = "#FORMTable";
      let frmheaders = document.querySelectorAll(frmid2 + " th");

      // Sort the table element when clicking on the table headers
      frmheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(frmid, ".clssqid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function FORMCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("FORMcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("FORMTable2");
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

  function FORMNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("FORMnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("FORMTable2");
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

  $('#example2').on('focus','[id*="txtFORM_popup"]',function(event){

        
      
          var id = $(this).attr('id');
          var id2 = $(this).parent().parent().find('[id*="FORMID"]').attr('id');      
          var id3 = $(this).parent().parent().find('[id*="FORMNAME"]').attr('id');      

          $('#hdn_FORMid').val(id);
          $('#hdn_FORMid2').val(id2);
          $('#hdn_FORMid3').val(id3);
        
          $("#FORMpopup").show();
          //$("#tbody_FORM").html('');
          $("#tbody_FORM").html('Loading...');
          $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          })
          $.ajax({
              url:'{{route("master",[228,"getmpdata"])}}',
              type:'POST',
              success:function(data) {
                $("#tbody_FORM").html(data);
                BindFORMEvents();
              },
              error:function(data){
                console.log("Error: Something went wrong.");
                $("#tbody_FORM").html('');
              },
          });

      });

      $("#FORM_closePopup").click(function(event){
        $("#FORMpopup").hide();
      });

      function BindFORMEvents()
      {
          $(".clsFORMid").click(function(){
              var fieldid = $(this).attr('id');
              var txtval =    $("#txt"+fieldid+"").val();
              var texdesc =   $("#txt"+fieldid+"").data("desc");
              var texdescdate =   $("#txt"+fieldid+"").data("descdate");
              
              var txtid= $('#hdn_FORMid').val();
              var txt_id2= $('#hdn_FORMid2').val();
              var txt_id3= $('#hdn_FORMid3').val();

              $('#'+txtid).val(texdesc);
              $('#'+txt_id2).val(txtval);
              $('#'+txt_id3).val(texdescdate);
             
              //clear row 
              $('#'+txtid).parent().parent().find('[id*="txtLISTPOP1_popup"]').val('');
              $('#'+txtid).parent().parent().find('[id*="hdnLISTPOP1ID"]').val('');
              $('#'+txtid).parent().parent().find('[id*="DESC2"]').val('');
              $('#'+txtid).parent().parent().find('[id*="STANDARDID_VAL"]').val('');
              

              $("#FORMpopup").hide();
              
              $("#FORMcodesearch").val(''); 
              $("#FORMnamesearch").val(''); 
              FORMCodeFunction();
              $(this).prop("checked",false);
              event.preventDefault();
          });
      }
//------------------------

$(document).ready(function(e) {

    $('#Row_Count').val("1");
  
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

    $("#example2").on('click', '.remove', function() {

        var rowCount = $('#Row_Count').val();

        if (rowCount > 1) {
            $(this).closest('tbody').remove();     
        } 
        
        if (rowCount <= 1) { 
            $(document).find('.remove').prop('disabled', false);  
        }
        event.preventDefault();
    });    

});


  $('#btnAdd').on('click', function() {
      var viewURL = '{{route("master",[228,"add"])}}';
      window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });

 var formResponseMst = $( "#frm_mst_add" );
     formResponseMst.validate();

    
    //  $("#CHECKLIST_NO").blur(function(){
    //   $(this).val($.trim( $(this).val() ));
    //   $("#ERROR_CHECKLIST_NO").hide();
    //  // validateSingleElemnet("CHECKLIST_NO");
         
    // });

    // $( "#CHECKLIST_NO" ).rules( "add", {
    //     required: true,
    //     nowhitespace: true,
    //     StringNumberRegex: true, //from custom.js
    //     messages: {
    //         required: "Required field.",
    //     }
    // });
    
    
    //validate
    $( "#btnSave" ).click(function() {
        if(formResponseMst.valid()){

            validateForm();

        }
    });

  
    
    $("#YesBtn").click(function(){

        $("#alert").modal('hide');
        var customFnName = $("#YesBtn").data("funcname");
            window[customFnName]();

    }); //yes button


   window.fnSaveData = function (){

        //validate and save data
        event.preventDefault();

        var getDataForm = $("#frm_mst_add");
        var formData = getDataForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("master",[228,"save"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                   
                   if(data.exist=='duplicate') {

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
                    //console.log("succes MSG="+data.msg);
                    
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
      
   } // fnSaveData



    
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
        

    }); ///ok button

    $("#OkBtn2").click(function(){
        $("#alert").modal('hide');
        $("#YesBtn").show();
        $("#NoBtn").show();
        $("#OkBtn2").hide();
        window.location.href = '{{route("master",[228,"index"]) }}';
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


   window.fnUndoNo = function (){
     // $("#txtmachine_popup").focus();
   }//fnUndoNo


    function showError(pId,pVal){

      $("#"+pId+"").text(pVal);
      $("#"+pId+"").show();

    }//showError

    function highlighFocusBtn(pclass){
       $(".activeYes").hide();
       $(".activeNo").hide();
       
       $("."+pclass+"").show();
    }

   function resetdata(){
      $('#example2').find('.participantRow').each(function(){

        var rowcount = $('#Row_Count').val();
        $(this).find('input:text').val('');
        $(this).find('input:hidden').val('');
        var rowid = $(this).find('[id*="DESC2"]').attr("id");
        if(rowid!="DESC2_0"){
          $(this).closest('tbody').remove();     
        }

      });
  }    

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
  
    $('#tbody_subglacct').html('Loading...');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url:'{{route("master",[228,"getmachines"])}}',
        type:'POST',
        success:function(data) {
            $('#tbody_subglacct').html(data);
            bindMachineEvents();
        },
        error:function(data){
            console.log("Error: Something went wrong.");
            $('#tbody_subglacct').html('');
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
           
            var oldID =   $("#MACHINEID_REF").val();
           
            $("#txtmachine_popup").val(texdesc);
            $("#txtmachine_popup").blur();
            $("#MACHINEID_REF").val(txtval);
            $("#MACHINENAME").val(txtccname);
           
            if (txtval != oldID)
            {
              $("#txtchecklist_popup").val('');
              $("#CHECKLIST_REF").val('');
              $("#CHECKLISTNAME").val('');
            }
            $("#machinepopup").hide();
            $("#machinecodesearch").val(''); 
            $("#machinedatesearch").val(''); 
           
            MachineCodeFunction();
            $(this).prop("checked",false);
            event.preventDefault();
        });
  }
//Machine Ends
//------------------------
//Checklist Starts
//------------------------
      let chktid = "#ChecklistTale2";
      let chktid2 = "#ChecklistTable";
      let chkheaders = document.querySelectorAll(chktid2 + " th");

      // Sort the table element when clicking on the table headers
      chkheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(chktid, ".clschecklist", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function ChecklistCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("checklistcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("ChecklistTable2");
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

  function ChecklistNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("checklistdatesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("ChecklistTable2");
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

  
$("#txtchecklist_popup").focus(function(event){

    var mid = $.trim( $("#MACHINEID_REF").val() ); 

    if(mid =="" ){
      
      $("#LISTPOP1popup").hide();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").hide();
      $("#OkBtn").show();
      $("#AlertMessage").text('Please select Machine No.');
      $("#alert").modal('show');
      $("#OkBtn").focus();
      highlighFocusBtn('activeOk');
                
      return false;
    } 
  
    $('#tbody_checklist').html('Loading...');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url:'{{route("master",[228,"getchecklists"])}}',
        type:'POST',
        success:function(data) {
            $('#tbody_checklist').html(data);
            bindChecklistEvents();
        },
        error:function(data){
            console.log("Error: Something went wrong.");
            $('#tbody_checklist').html('');
        },
    });        
     $("#checklistpopup").show();
     event.preventDefault();
}); 

$("#checklist_closePopup").on("click",function(event){ 
    $("#checklistpopup").hide();
    event.preventDefault();
});
function bindChecklistEvents(){

        $('.clschecklist').click(function(){

          
    
          var id = $(this).attr('id');
          var txtval =    $("#txt"+id+"").val();
          var texdesc =   $("#txt"+id+"").data("desc");
          var txtccname =   $("#txt"+id+"").data("ccname");
          
          var oldID =   $("#CHECKLIST_REF").val();
          
          $("#txtchecklist_popup").val(texdesc);
          $("#txtchecklist_popup").blur();
          $("#CHECKLIST_REF").val(txtval);
          $("#CHECKLISTNAME").val(txtccname);
          
          // if (txtval != oldID)
          // {
          //    resetdata();
          // }
          
          $("#checklistpopup").hide();
          $("#checklistcodesearch").val(''); 
          $("#checklistdatesearch").val(''); 
          
          ChecklistCodeFunction();
          ChecklistNameFunction();
          $(this).prop("checked",false);

          checkDuplicateCode(); 

          event.preventDefault();

        });
  }
//Checklsit Ends
//------------------------
    
</script>

@endpush