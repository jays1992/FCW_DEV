@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[207,'index'])}}" class="btn singlebt">Cost Centre</a>
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
         <form id="frm_mst_edit" method="POST" onsubmit="return validateForm()" class="needs-validation"  > 
          @CSRF
          {{isset($objResponse[0]->CCID) ? method_field('PUT') : '' }}
          <div class="inner-form">
          
                <div class="row">
                    <div class="col-lg-2 pl"><p>CC Category Code</p></div>
                    <div class="col-lg-2 pl">
                        <input type="text" name="COSTCAT_popup" id="txtcostcat_popup" class="form-control mandatory" value="{{isset($objResponse[0]->CCCATCODE) ? $objResponse[0]->CCCATCODE : '' }}"  autocomplete="off" readonly/>
                        <input type="hidden" name="CCID_REF" id="CCID_REF" value="{{isset($objResponse[0]->CCCATID_REF) ? $objResponse[0]->CCCATID_REF : '' }}" class="form-control" autocomplete="off" />
                        <input type="hidden" name="user_approval_level" id="user_approval_level" value="{{ $user_approval_level }}"  />
                    </div>
                  
                    <div class="col-lg-2 pl"><p>Name</p></div>
                    <div class="col-lg-2 pl">
                        <input type="text" name="CCNAME" id="CCNAME" class="form-control" value="{{isset($objResponse[0]->CCCATNAME) ? $objResponse[0]->CCCATNAME : '' }}"  autocomplete="off" readonly/>
                    </div>
                </div>   

                


             <div class="row">
                  <div class="col-lg-6 pl">
                    <div class=" table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:300px;width:800px" >
                      <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                        <thead id="thead1"  style="position: sticky;top: 0">
                          <tr>
                          <th>Cost Centre Code
                              <input class="form-control" type="hidden" name="Row_Count" id ="Row_Count"> 
                              <input type="hidden" id="focusid" >
                              <input type="hidden" id="errorid" >
                          </th>
                          <th>Cost Centre Name</th>
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
                              <td hidden><input  class="form-control w-100" type="text" name={{"CCID_".$key}}   id ={{"HDNCCID_".$key}} value="{{ $row->CCID }}" ></td>
                              <td><input  class="form-control w-100" type="text" name={{"COSTCODE_".$key}}       id ={{"txtcostcode_".$key}}  value="{{ $row->CCCODE }}" disabled maxlength="20" autocomplete="off" style="text-transform:uppercase;width:100%;" onkeypress="return AlphaNumaric(event,this)" ></td>
                              <td><input  class="form-control w-100" type="text" name={{"DESCRIPTIONS_".$key}}   id ={{"txtdesc_".$key}} value="{{ $row->NAME }}" maxlength="100" disabled autocomplete="off" style="width:100%;" ></td>
                              <td align="center"><input type="checkbox" name={{"DEACTIVATED_".$key}}    id ={{"CHKDEACTIVATED_".$key}} value="{{$row->DEACTIVATED == 1 ? "1" : "0"}}" disabled {{$row->DEACTIVATED == 1 ? "checked" : ""}} ></td>
                              <td><input  class="form-control" type="date" name={{"DODEACTIVATED_".$key}}  id ={{"DODEACTIVATED_".$key}}  value="{{ $deactivate_date }}" disabled ></td>
                              <td align="center" >
                                  <button class="btn add" title="add" data-toggle="tooltip" disabled><i class="fa fa-plus"></i></button>
                                  <button class="btn remove" title="Delete" data-toggle="tooltip" disabled ><i class="fa fa-trash" ></i></button>
                              </td>
                          </tr>
                          @php $n++; @endphp
                          @endforeach 

                          @else
                          {{-- <tr  class="participantRow">
                              <td><input  class="form-control w-100" type="text" name="COSTCODE_0" id ="txtcostcode_0"  maxlength="20" autocomplete="off" style="text-transform:uppercase;width:100%;" onkeypress="return AlphaNumaric(event,this)" ></td>
                              <td><input  class="form-control w-100" type="text" name="DESCRIPTIONS_0" id ="txtdesc_0" maxlength="100" autocomplete="off" style="width:100%;" ></td>
                              <td><input  class="form-control w-100" type="text" name="DEACTIVATED_0" id ="DEACTIVATED_0" maxlength="100" autocomplete="off" style="width:100%;" ></td>
                              <td><input  class="form-control w-100" type="text" name="DODEACTIVATED_0" id ="DODEACTIVATED_0" maxlength="100" autocomplete="off" style="width:100%;" ></td>
                              <td align="center" >
                                  <button class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></button>
                                  <button class="btn remove" title="Delete" data-toggle="tooltip" disabled><i class="fa fa-trash" ></i></button>
                              </td>
                          </tr> --}}
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
    var catcode  = $.trim($("#CCID_REF").val());
    var txtcostcode  =   $.trim($("[id*=txtcostcode]").val());
    var txtdesc     =   $.trim($("[id*=txtdesc]").val());



    if(catcode ===""){
        $("#focusid").val('CCID_REF');
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").show();
        $("#AlertMessage").text('Please select CC Category Code.');
        $("#alert").modal('show');
        $("#OkBtn").focus();
        highlighFocusBtn('activeOk');
        return false;
    }
   
    if(txtcostcode ==="" && txtdesc ===""){
        $("#focusid").val('txtcostcode_0');
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").show();
        $("#AlertMessage").text('Please enter Cost Centre Code.');
        $("#alert").modal('show');
        $("#OkBtn").focus();
        highlighFocusBtn('activeOk');
        return false;
    }
    else if(txtcostcode !="" && txtdesc ===""){
        $("#focusid").val('txtdesc_0');
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").show();
        $("#AlertMessage").text('Please enter Cost Centre Name.');
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

        $("[id*=txtcostcode]").each(function(){
 
            if($.trim($(this).val()) ==="" ){
              allblank1.push('true');
              texid1 = $(this).attr('id');
            }else{
              allblank1.push('false');
            }


            if($.trim($(this).parent().parent().find('[id*="txtdesc"]').val()) === "" ){
              allblank2.push('true');
              texid2 = $(this).parent().parent().find('[id*="txtdesc"]').attr('id');
            }else{
              allblank2.push('false');
            }

            if(ExistArray.indexOf($.trim($(this).val())) > -1) {
              allblank3.push('true');
              texid3 = $(this).attr('id');
            }
            else{
              allblank3.push('false');
            }

            ExistArray.push($.trim($(this).val()));

           
        });

        if(jQuery.inArray("true", allblank1) !== -1){
          $("#focusid").val(texid1);
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn").show();
            $("#AlertMessage").text('Please enter Cost Centre Code.');
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
            $("#AlertMessage").text('Please enter Cost Centre Name.');
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
            $("#AlertMessage").text('Cost Centre Code can not be duplicate.');
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
      var viewURL = '{{route("master",[207,"add"])}}';
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
    window.location.href = '{{route("master",[207,"index"]) }}';
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
            url:'{{route("mastermodify",[207,"update"])}}',
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
            url:'{{route("mastermodify",[207,"singleapprove"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    if(data.errors.GROUPNAME){
                        showError('ERROR_GROUPNAME',data.errors.GROUPNAME);
                    }
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
        //     window.location.href = '{{route("master",[207,"index"]) }}';
        // }

        //window.location.href = '{{route("master",[207,"index"]) }}';

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