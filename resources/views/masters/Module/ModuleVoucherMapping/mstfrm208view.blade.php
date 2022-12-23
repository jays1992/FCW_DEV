@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[208,'index'])}}" class="btn singlebt">Module-Voucher Mapping</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                  <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                  <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
                  <button class="btn topnavbt" id="btnSaveSE" disabled="disabled"><i class="fa fa-save"></i> Save</button>
                  <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                  <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                  <button class="btn topnavbt" id="btnUndo"  disabled="disabled"><i class="fa fa-undo"></i> Undo</button>
                  <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                  <button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-lock"></i> Approved</button>
                  <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                  <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                </div><!--col-10-->

            </div><!--row-->
    </div><!--topnav-->	
   
    <div class="container-fluid purchase-order-view filter">     
         <form id="frm_mst_edit" method="POST" class="needs-validation"  > 
          @CSRF
        
          <div class="inner-form">
          
                <div class="row">
                    <div class="col-lg-2 pl"><p>Module Code</p></div>
                    <div class="col-lg-2 pl">
                        <input type="text" name="COSTCAT_popup" id="txtcostcat_popup" class="form-control mandatory" value="{{isset($objResponse[0]->MODULECODE) ? $objResponse[0]->MODULECODE : '' }}"  autocomplete="off" readonly/>
                        <input type="hidden" name="MODID_REF" id="MODID_REF" value="{{isset($objResponse[0]->MODULEID_REF) ? $objResponse[0]->MODULEID_REF : '' }}" class="form-control" autocomplete="off" />
                        <input type="hidden" name="user_approval_level" id="user_approval_level" value="{{ $user_approval_level }}"  />
                    </div>
                  
                    <div class="col-lg-2 pl"><p>Name</p></div>
                    <div class="col-lg-2 pl">
                        <input type="text" name="MODNAME" id="MODNAME" class="form-control" value="{{isset($objResponse[0]->MODULENAME) ? $objResponse[0]->MODULENAME : '' }}"  autocomplete="off" readonly/>
                    </div>
                </div>   

                


             <div class="row">
                  <div class="col-lg-6 pl">
                    <div class=" table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:500px;width:1050px" >
                      <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                        <thead id="thead1"  style="position: sticky;top: 0">
                          <tr>
                          <th hidden>vid</th>
                          <th>Voucher Code
                              <input class="form-control" type="hidden" name="Row_Count" id ="Row_Count"> 
                              <input type="hidden" id="focusid" >
                              <input type="hidden" id="errorid" >
                          </th>
                          <th width="200px">Voucher Type Description</th>
                          <th width="100px">Heading</th>
                          <th>Remarks</th>
                          <th>De-Activated</th>
                          <th>Date of De-Activated</th>
                          <th width="5%">Action</th>
                          </tr>
                        </thead>
                        <tbody>
                        @if(!empty($objResponse))
                        @php $n=1; @endphp
                        @foreach($objResponse as $key => $row)
                          @php
                            $deactivate_date = '';
                            if(isset($row->DODEACTIVATED) && $row->DODEACTIVATED !="" && $row->DODEACTIVATED !="1900-01-01" && !is_null($row->DODEACTIVATED)){
                              $deactivate_date = $row->DODEACTIVATED;
                            }   
                          @endphp
                          <tr  class="participantRow">
                              <td hidden><input  class="form-control w-100" type="text" name={{"MVMID_".$key}}   id ={{"HDNMVMID_".$key}} value="{{ $row->MVMID }}" ></td>
                              <td hidden ><input type="text" name={{"LISTPOP1ID_".$key}} id={{"hdnLISTPOP1ID_".$key}} value="{{ $row->VTID }}"class="form-control" autocomplete="off" /></td>
                              <td><input type="text" name={{"txtLISTPOP1_popup_".$key}} id={{"txtLISTPOP1_popup_".$key}}  value="{{ $row->VCODE }}" class="form-control CLS_LISTPOP1"  autocomplete="off"  readonly style="width:100px;" disabled/></td>
                              <td><input  class="form-control w-100" type="text" name={{"DESC2_".$key}}       id ={{"DESC2_".$key}}  value="{{ $row->DESCRIPTIONS }}"  style="width: 200px;"  autocomplete="off" readonly></td>
                              <td width="140px"> 
                                <select name={{"VHEADING_".$key}} id={{"VHEADING_".$key}} class="form-control" style="width: 100px;" disabled>
                                  <option value="" >--Please select--</option>
                                  <option value="Master" @if(strtolower($row->HEADING) == "master") selected="selected" @endif  >Master</option>
                                  <option value="Transactions" @if(strtolower($row->HEADING) == "transactions") selected="selected" @endif>Transactions</option>
                                  <option value="Report" @if(strtolower($row->HEADING) == "report") selected="selected" @endif>Report</option>
                                  <option value="MIS" @if(strtolower($row->HEADING) == "mis") selected="selected" @endif >MIS</option>
                                </select> 
                              </td>
                              <td><input  class="form-control w-100" type="text" name={{"REMARKS_".$key}}  id ={{"REMARKS_".$key}} value="{{isset($objResponse[0]->MODULENAME) ? $objResponse[0]->MODULENAME : '' }}" autocomplete="off" style="width:100%;" readonly  ></td>

                              <td align="center"><input type="checkbox" name={{"DEACTIVATED_".$key}}    id ={{"CHKDEACTIVATED_".$key}} value="{{$row->DEACTIVATED == 1 ? "1" : "0"}}" {{$row->DEACTIVATED == 1 ? "checked" : ""}} disabled></td>
                              <td><input  class="form-control" type="date" name={{"DODEACTIVATED_".$key}}  id ={{"DODEACTIVATED_".$key}}  value="{{ $deactivate_date }}" disabled></td>
                              <td align="center" >
                                  <button class="btn add" title="add" data-toggle="tooltip" disabled><i class="fa fa-plus"></i></button>
                                  <button class="btn remove" title="Delete" data-toggle="tooltip" {{isset($n) && $n ==1?'disabled':''}} disabled ><i class="fa fa-trash" ></i></button>
                              </td>
                          </tr>
                          @php $n++; @endphp
                          @endforeach 
                          @endif     
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
            <button onclick="setfocus();" class="btn alertbt" name='OkBtn' id="OkBtn" style="display:none;margin-left: 90px;">
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
<!-- POPUP2-->
<div id="LISTPOP1popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='LISTPOP1_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Voucher Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="LISTPOP1Table" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
          <tr id="none-select" class="searchalldata"  >            
            <td hidden> <input type="hidden" name="fieldid" id="hdn_LISTPOP1id"/>
              <input type="hidden" name="fieldid2" id="hdn_LISTPOP1id2"/>
              <input type="hidden" name="fieldid3" id="hdn_LISTPOP1id3"/>
            </td>
          </tr>
          <tr>
                  <th>Code</th>
                  <th>Description</th>
          </tr>
    </thead>
    <tbody>
    <tr>
    <td>
    <input type="text" id="LISTPOP1codesearch" onkeyup="LISTPOP1CodeFunction()">
    </td>
    <td>
    <input type="text" id="LISTPOP1namesearch" onkeyup="LISTPOP1NameFunction()">
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
<!-- POPUP2 END-->
@endsection
<!-- btnSave -->

@push('bottom-scripts')
<script>
function setfocus(){
    var focusid=$("#focusid").val();
    $("#"+focusid).focus();
    $("#closePopup").click();
} 

function validateForm(UserAction){

    $("#focusid").val('');
    var mcode  = $.trim($("#MODID_REF").val());

    
    if(mcode ===""){
        $("#focusid").val('MODID_REF');
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").show();
        $("#AlertMessage").text('Please select Module Code.');
        $("#alert").modal('show');
        $("#OkBtn").focus();
        highlighFocusBtn('activeOk');
        return false;
    }
    else{
        event.preventDefault();
        var ExistArray = []; 
        var allblank1 = [];  
        var allblank2 = []; 
        var allblank3 = [];        

        var texid1    = "";
        var texid2    = ""; 
        var texid3    = "";

        $("[id*=hdnLISTPOP1ID]").each(function(){
 
               if($.trim($(this).val()) ==="" ){
                allblank1.push('true');
                texid1 = $(this).attr('id');
              }else{
                allblank1.push('false');
              }

              if($.trim($(this).parent().parent().find('[id*="VHEADING"]').val()) === "" ){
                allblank2.push('true');
                texid2 = $(this).parent().parent().find('[id*="VHEADING"]').attr('id');
              }else{
                allblank2.push('false');
              }

              var record_data = $.trim($(this).val())+'_'+$.trim($(this).parent().parent().find('[id*="VHEADING"]').val());
              if(ExistArray.indexOf(record_data) > -1) {
                allblank3.push('true');
                texid3 =  $(this).parent().parent().find('[id*="txtLISTPOP1_popup"]').attr('id');  //focus popup
              }
              else{
                allblank3.push('false');
              }

              ExistArray.push(record_data);

        });

        if(jQuery.inArray("true", allblank1) !== -1){
          $("#focusid").val(texid1);
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn").show();
            $("#AlertMessage").text('Please select Voucher Code.');
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
            $("#AlertMessage").text('Please select Heading.');
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
          else{
              $("#alert").modal('show');
              $("#AlertMessage").text('Do you want to save to record.');
              $("#YesBtn").data("funcname",UserAction);  
              $("#YesBtn").focus();
              highlighFocusBtn('activeYes');
          }

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

  function LISTPOP1NameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("LISTPOP1namesearch");
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

  $('#example2').on('focus','[id*="txtLISTPOP1_popup"]',function(event){

        
        var modcode  = $.trim($("#MODID_REF").val());
        if(modcode ===""){
            $("#focusid").val('MODID_REF');
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn").show();
            $("#AlertMessage").text('Please select Module.');
            $("#alert").modal('show');
            $("#OkBtn").focus();
            highlighFocusBtn('activeOk');
            return false;
        }
        
          var id = $(this).attr('id');
          var id2 = $(this).parent().parent().find('[id*="LISTPOP1ID"]').attr('id');      
          var id3 = $(this).parent().parent().find('[id*="DESC2"]').attr('id');      

          $('#hdn_LISTPOP1id').val(id);
          $('#hdn_LISTPOP1id2').val(id2);
          $('#hdn_LISTPOP1id3').val(id3);
        
          $("#LISTPOP1popup").show();
          //$("#tbody_LISTPOP1").html('');
          $("#tbody_LISTPOP1").html('Loading...');
          $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          })
          $.ajax({
              url:'{{route("master",[208,"getvouchers"])}}',
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
          $(".clsLISTPOP1id").dblclick(function(){
              var fieldid = $(this).attr('id');
              var txtval =    $("#txt"+fieldid+"").val();
              var texdesc =   $("#txt"+fieldid+"").data("desc");
              var texdescdate =   $("#txt"+fieldid+"").data("descdate");
              
              var txtid= $('#hdn_LISTPOP1id').val();
              var txt_id2= $('#hdn_LISTPOP1id2').val();
              var txt_id3= $('#hdn_LISTPOP1id3').val();

              $('#'+txtid).val(texdesc);
              $('#'+txt_id2).val(txtval);
              $('#'+txt_id3).val(texdescdate);
              $('#'+txtid).parent().parent().find('[id*="REMARKS"]').val( $.trim($('#MODNAME').val()) );
              $('#'+txtid).parent().parent().find('[id*="VHEADING"]').prop('selectedIndex',0);
              

              $("#LISTPOP1popup").hide();
              
              $("#LISTPOP1codesearch").val(''); 
              $("#LISTPOP1namesearch").val(''); 
              LISTPOP1CodeFunction();
              event.preventDefault();
          });
      }
//------------------------

$(document).ready(function(e) {

    var rcount = <?php echo json_encode($objCount); ?>;

    $('#Row_Count').val(rcount);

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
        $clone.find('[id*="HDNCCID"]').val('0'); 
        $tr.closest('table').append($clone);
        var rowCount = $('#Row_Count').val();
        rowCount = parseInt(rowCount)+1;
        $('#Row_Count').val(rowCount);
        
        $clone.find('.remove').removeAttr('disabled'); 
        $clone.find('[id*="txtisgcode"]').removeAttr('readonly'); 
        $clone.find('[id*="txtdesc"]').val('');

        /*
        $clone.find('[id*="txtID"]').val('0'); 
        $clone.find('[id*="chkmdtry"]').prop("checked", false); 
        $clone.find('[id*="deactive-checkbox"]').prop("checked", false); 
        */
        event.preventDefault();

    });

    $("#example2").on('click', '.remove', function() {

        var rowCount = $('#Row_Count').val();
        if (rowCount > 1) {
            $(this).closest('.participantRow').remove(); 
        } 
        if (rowCount <= 1) { 
            $(document).find('.remove').prop('disabled', true);  
        }

        event.preventDefault();

    });

});


  $('#btnAdd').on('click', function() {
      var viewURL = '{{route("master",[208,"add"])}}';
      window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });

  $("#OkBtn2").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn2").hide();
    window.location.href = '{{route("master",[208,"index"]) }}';
});

 var formDataMst = $( "#frm_mst_edit" );
     formDataMst.validate();

   
    //validate
    $( "#btnSave" ).click(function() {

        if(formDataMst.valid()){
           
            validateForm('fnSaveData');

        }

    });//btnSave

    
    //validate and approve
    $("#btnApprove").click(function() {
        
        if(formDataMst.valid()){
           
            validateForm('fnApproveData');

        }

    });//btnSave


    $("#YesBtn").click(function(){

      $("#alert").modal('hide');
      var customFnName = $("#YesBtn").data("funcname");
          window[customFnName]();

    }); //yes button

    
    window.fnSaveData = function (){
        
        //validate and save data
        event.preventDefault();

        var getDataForm = $("#frm_mst_edit");
        var formData = getDataForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("mastermodify",[208,"update"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                   if(data.exist=='norecord') {
                      $("#errorid").val('1'); 
                      $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn").show();

                      $("#AlertMessage").text(data.msg);

                      $("#alert").modal('show');
                      $("#OkBtn").focus();

                   }
                   if(data.save=='invalid') {
                      $("#errorid").val('1'); 
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

    };// fnSaveData


    // save and approve 
    window.fnApproveData = function (){
        
        //validate and save data
        event.preventDefault();

        var getDataForm = $("#frm_mst_edit");
        var formData = getDataForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("mastermodify",[208,"singleapprove"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                   if(data.exist=='norecord') {
                      $("#errorid").val('1'); 
                      $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn").show();

                      $("#AlertMessage").text(data.msg);

                      $("#alert").modal('show');
                      $("#OkBtn").focus();

                   }
                   if(data.save=='invalid') {
                      $("#errorid").val('1'); 
                      $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn").show();

                      $("#AlertMessage").text(data.msg);

                      $("#alert").modal('show');
                      $("#OkBtn").focus();
                      $("#OkBtn2").show();
                      $("#OkBtn2").focus();

                   }
                }
                if(data.success) {                   
                    console.log("succes MSG="+data.msg);
                    
                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn").show();

                    $("#AlertMessage").text(data.msg);

                    $(".text-danger").hide();
                    $("#frm_mst_edit").trigger("reset");

                    $("#alert").modal('show');
                    $("#OkBtn").focus();

                }
                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });

    };// fnApproveData

    //no button
    $("#NoBtn").click(function(){

      $("#alert").modal('hide');
      var custFnName = $("#NoBtn").data("funcname");
          window[custFnName]();

    }); //no button

   
    $("#OkBtn").click(function(){

        $("#alert").modal('hide');

        $("#YesBtn").show();
        $("#NoBtn").show();
        $("#OkBtn").hide();

        $(".text-danger").hide();

        // if($("#errorid").val() ===""){
        //     window.location.href = '{{route("master",[208,"index"]) }}';
        // }

        //window.location.href = '{{route("master",[208,"index"]) }}';

    }); ///ok button

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

     // $("#GROUPCODE").focus();

   }//fnUndoNo


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

    $('#example2').on('change',"[id*='CHKDEACTIVATED']",function()
    {
      
      if ($(this).is(":checked") == false){
            $(this).parent().parent().find('[id*="DODEACTIVATED"]').val('');
        }
      event.preventDefault();
    });

</script>



@endpush