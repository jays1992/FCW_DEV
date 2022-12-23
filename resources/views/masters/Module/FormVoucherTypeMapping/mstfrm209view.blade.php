@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[209,'index'])}}" class="btn singlebt">Form - Voucher Type Mapping</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <button href="#" id="btnSelectedRows" class="btn topnavbt" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
                        <button id="btnSave" disabled="disabled"  class="btn topnavbt" tabindex="4"><i class="fa fa-save"></i> Save</button>
                        <button class="btn topnavbt" id="btnView"  disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt" disabled id="btnUndo" ><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled" ><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-lock"></i> Approved</button>
                        <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-link" ></i> Attachment</button>
                        <button class="btn topnavbt" id="btnExit"><i class="fa fa-power-off"></i> Exit</button>
                </div><!--col-10-->

            </div><!--row-->
    </div><!--topnav-->	
   
    <div class="container-fluid purchase-order-view filter">     
         <form id="frm_mst_edit" method="POST" class="needs-validation"  > 
          @CSRF
          
          <div class="inner-form">
          
             <div class="row">
                  <div class="col-lg-6 pl">
                    <div class=" table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:500px;width:1050px" >
                      <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                        <thead id="thead1"  style="position: sticky;top: 0">
                          <tr>
                          <th hidden>vid</th>
                          <th >Form Code </th>
                        <th style="width:200px;">Form Name</th>
                        <th hidden>vid</th>
                          <th>Voucher Code
                              <input class="form-control" type="hidden" name="Row_Count" id ="Row_Count"> 
                              <input type="hidden" id="focusid" >
                              <input type="hidden" id="errorid" >
                          </th>
                          <th style="width:300px;">Voucher Type Description</th>
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
                              <td hidden><input  class="form-control w-100" type="text" name={{"FVMID_".$key}}   id ={{"HDNFVMID_".$key}} value="{{ $row->FVMID }}" ></td>
                              
                              <td><input type="text" name={{"txtFORM_popup_".$key}} id={{"txtFORM_popup_".$key}} class="form-control" value="{{ $row->FORMCODE }}"  autocomplete="off" disabled readonly style="width:100%;" /></td>
                              <td hidden ><input type="text" name={{"FORMID_".$key}} id={{"hdnFORMID_".$key}} class="form-control" value="{{ $row->FORMID_REF }}"  autocomplete="off" /></td>
                              <td><input  class="form-control" style="width: 100%" type="text" name={{"FORMNAME_".$key}} id ={{"FORMNAME_".$key}} value="{{ $row->FORMNAME }}" disabled style="width:200px;" autocomplete="off" readonly></td>

                              <td hidden ><input type="text" name={{"LISTPOP1ID_".$key}} id={{"hdnLISTPOP1ID_".$key}} value="{{ $row->VTID_REF }}"class="form-control" autocomplete="off" /></td>
                              <td><input type="text" name={{"txtLISTPOP1_popup_".$key}} id={{"txtLISTPOP1_popup_".$key}}  value="{{ $row->VCODE }}" class="form-control "  disabled autocomplete="off"  readonly style="width:100px;" /></td>
                              <td><input  class="form-control w-100" type="text" name={{"DESC2_".$key}}       id ={{"DESC2_".$key}}  value="{{ $row->DESCRIPTIONS }}" disabled style="width:100%;"  autocomplete="off" readonly></td>
                              
                              <td align="center"><input type="checkbox" name={{"DEACTIVATED_".$key}}    id ={{"CHKDEACTIVATED_".$key}} value="{{$row->DEACTIVATED == 1 ? "1" : "0"}}" {{$row->DEACTIVATED == 1 ? "checked" : ""}} disabled ></td>
                              <td><input  class="form-control" type="date" name={{"DODEACTIVATED_".$key}}  id ={{"DODEACTIVATED_".$key}}  value="{{ $deactivate_date }}" disabled ></td>
                              <td align="center" >
                                  <button class="btn add" title="add" data-toggle="tooltip" disabled><i class="fa fa-plus"></i></button>
                                  <button class="btn remove" title="Delete" data-toggle="tooltip" disabled  ><i class="fa fa-trash" ></i></button>
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
<!-- POPUP-->

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

   
     
  
}


$(document).ready(function(e) {

    var rcount = <?php echo json_encode($objCount); ?>;

    $('#Row_Count').val(rcount);

    

});


  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });


    
    window.fnSaveData = function (){
        
      
    };// fnSaveData


    // save and approve 
    window.fnApproveData = function (){
        
      
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
        $("#OkBtn2").hide();

        $(".text-danger").hide();

        // if($("#errorid").val() ===""){
        //     window.location.href = '{{route("master",[209,"index"]) }}';
        // }

        //window.location.href = '{{route("master",[209,"index"]) }}';

    }); ///ok button

    $("#btnUndo").click(function(){

        $("#AlertMessage").text("Do you want to erase entered information in this record?");
        $("#alert").modal('show');

        $("#YesBtn").data("funcname","fnUndoYes");
        $("#YesBtn").show();

        $("#NoBtn").data("funcname","fnUndoNo");
        $("#NoBtn").show();

        $("#OkBtn").hide();
        $("#OkBtn2").hide();
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