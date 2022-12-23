@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[209,'index'])}}" class="btn singlebt">Form - Voucher Type Mapping</a>
                </div><!--col-2-->
                @if(empty($objExistData))
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
                @else
                  <label><p>Records already availbale. You can only edit the records.</p></label>
                @endif    
              </div><!--col-10-->

            </div><!--row-->
    </div><!--topnav-->	
   
    <div class="container-fluid purchase-order-view filter">   
      @if(empty($objExistData))
         <form id="frm_mst_add" method="POST" onsubmit="return validateForm()" class="needs-validation"  > 
         
          @CSRF
          <div class="inner-form">

                <div class="row">
                  <div class="col-lg-6 pl">
                    <div class=" table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:400px;width:1050px;" >
                      <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                        <thead id="thead1"  style="position: sticky;top: 0">
                          <tr>
                          <th >Form Code <input class="form-control" type="hidden" name="Row_Count" id ="Row_Count"> 
                              <input type="hidden" id="focusid" >
                            </th>
                          <th style="width:200px;">Form Name</th>
                          <th hidden>vid</th>
                          <th>Voucher Type Code</th>
                          <th style="width:300px;">Voucher Type Description</th>
                          <th width="5%">Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr  class="participantRow">
                            <td><input type="text" name="txtFORM_popup_0" id="txtFORM_popup_0" class="form-control"  autocomplete="off"  readonly style="width:100%;" /></td>
                            <td hidden ><input type="text" name="FORMID_0" id="hdnFORMID_0" class="form-control" autocomplete="off" /></td>
                            <td><input  class="form-control" style="width: 100%" type="text" name="FORMNAME_0" id ="FORMNAME_0" style="width:200px;" autocomplete="off" readonly></td>
                            <td><input type="text" name="txtLISTPOP1_popup_0" id="txtLISTPOP1_popup_0" class="form-control"  autocomplete="off"  readonly style="width:100%;" /></td>
                            <td hidden ><input type="text" name="LISTPOP1ID_0" id="hdnLISTPOP1ID_0" class="form-control" autocomplete="off" /></td>
                            <td><input  class="form-control" style="width: 100%" type="text" name="DESC2_0" id ="DESC2_0"  autocomplete="off" readonly></td>                            
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
        @endif
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

<!-- POPUP-->
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
            <th class="ROW1" style="width: 10%" align="center">Select</th> 
            <th class="ROW2" style="width: 40%">Code</th>
            <th class="ROW3" style="width: 40%">Description</th>
          </tr>
    </thead>
    <tbody>
    <tr>
    <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
    <td class="ROW2"  style="width: 40%">
    <input type="text" autocomplete="off"  class="form-control"  id="LISTPOP1codesearch" onkeyup="LISTPOP1CodeFunction()" />
    </td>
    <td  class="ROW3"  style="width: 40%">
    <input type="text" autocomplete="off"  class="form-control"  id="LISTPOP1namesearch" onkeyup="LISTPOP1NameFunction()" />
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
	  <div class="tablename"><p>Form Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="FORMTable" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
          <tr id="none-select" class="searchalldata"  >            
            <td hidden> <input type="hidden" name="fieldid" id="hdn_FORMid"/>
              <input type="hidden" name="fieldid2" id="hdn_FORMid2"/>
              <input type="hidden" name="fieldid3" id="hdn_FORMid3"/>
            </td>
          </tr>
          <tr>
            <th class="ROW1" style="width: 10%" align="center">Select</th> 
            <th class="ROW2" style="width: 40%">Code</th>
            <th class="ROW3" style="width: 40%">Description</th>
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
    var focusid=$("#focusid").val();
    $("#"+focusid).focus();
    $("#closePopup").click();
} 

function validateForm(){

    
        event.preventDefault();
        var ExistArray = []; 
        var allblank1 = [];  
        var allblank2 = []; 
        var allblank3 = []; 
        var texid1    = "";
        var texid2    = ""; 
        var texid3    = "";

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
           
        });

        if(jQuery.inArray("true", allblank1) !== -1){
          $("#focusid").val(texid1);
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn").show();
            $("#AlertMessage").text('Please select Form Code.');
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
            $("#AlertMessage").text('Please Voucher Type Code.');
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
              url:'{{route("master",[209,"getvouchers"])}}',
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

              $('#'+txtid).val(texdesc);
              $('#'+txt_id2).val(txtval);
              $('#'+txt_id3).val(texdescdate);

              

              $("#LISTPOP1popup").hide();
              
              $("#LISTPOP1codesearch").val(''); 
              $("#LISTPOP1namesearch").val(''); 
              LISTPOP1CodeFunction();
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
              url:'{{route("master",[209,"getforms"])}}',
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
             
              

              $("#FORMpopup").hide();
              
              $("#FORMcodesearch").val(''); 
              $("#FORMnamesearch").val(''); 
              FORMCodeFunction();
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
      var viewURL = '{{route("master",[209,"add"])}}';
      window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });

 var formResponseMst = $( "#frm_mst_add" );
     formResponseMst.validate();

    
    
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
            url:'{{route("master",[209,"save"])}}',
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
        window.location.href = '{{route("master",[209,"index"]) }}';
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
     // $("#txtcostcat_popup").focus();
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
    
</script>

@endpush