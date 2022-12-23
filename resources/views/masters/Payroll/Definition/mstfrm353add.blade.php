@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[353,'index'])}}" class="btn singlebt">Definition</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                <button class="btn topnavbt" id="btnAdd" disabled="disabled" ><i class="fa fa-plus"></i> Add</button>
                <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
                <button class="btn topnavbt" id="btnSave"  tabindex="3"  ><i class="fa fa-save"></i> Save</button>
                <button class="btn topnavbt" id="btnView" disabled="disabled" ><i class="fa fa-eye"></i> View</button>
                <button class="btn topnavbt" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                <button class="btn topnavbt" id='btnUndo' ><i class="fa fa-undo"></i> Undo</button>
                <button class="btn topnavbt" id="btnCancel"  disabled="disabled" ><i class="fa fa-times"></i> Cancel</button>
                <button class="btn topnavbt" id="btnApprove"  disabled="disabled" ><i class="fa fa-lock"></i> Approved</button>
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
                    <div class=" table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:300px;" >
                      <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                        <thead id="thead1"  style="position: sticky;top: 0">
                          <tr>
                          <th>
                            Particular 
                              <input class="form-control" type="hidden" name="Row_Count" id ="Row_Count"> 
                              <input type="hidden" id="focusid" >
                          </th>
                          <th width="5%">Earning / Deduction</th>
                          <th width="5%">Earning / Deduction Head Code</th>
                          <th width="5%">Earning / Deduction Head Description</th>
                          <th width="5%">Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr  class="participantRow">
                              <td><input  class="form-control w-100" type="text" name="PARTICULAR_0" id ="PARTICULAR_0"  maxlength="100" autocomplete="off" style="text-transform:uppercase;width:100%;" ></td>
                              {{-- <td><input  class="form-control w-100" type="text" name="EARNING_DEDUCTION_0" id ="EARNING_DEDUCTION_0"  maxlength="100" autocomplete="off" style="text-transform:uppercase;width:100%;" ></td> --}}
                              <td>
                                <select name="EARNING_DEDUCTION_0" id="EARNING_DEDUCTION_0" class="form-control w-100" style="width: 200px;">
                                <option value="" selected="">Select</option>
                                <option value="Earning">Earning</option>
                                <option value="Deduction">Deduction</option>
                                </select>
                              </td>

                              <td><select class="form-control" name="EARNING_DEDUCTION_HEAD_0" id="EARNING_DEDUCTION_HEAD_0" style="width: 200px;" >
                                <option value="" selected="">Select</option>
                                @foreach($objHeadCodeList as $val)
                                <option value="{{$val->DEDUCTION_HEADID}}">{{$val->DEDUCTION_HEADCODE}}</option>
                                @endforeach
                                </select>
                              </td>
                               <td><input  class="form-control w-100" type="text" name="EARNING_DEDUCTION_DES_0" id ="EARNING_DEDUCTION_DES_0"  maxlength="100" autocomplete="off" style="text-transform:uppercase;width:100%;" ></td>
                              <td align="center" >
                                  <button class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></button>
                                  <button class="btn remove" title="Delete" data-toggle="tooltip" disabled><i class="fa fa-trash" ></i></button>
                              </td>
                          </tr>
                        </tbody>
                      </table>
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
            <button class="btn alertbt" name='OkBtn1' id="OkBtn1" style="display:none;margin-left: 90px;">
              <div id="alert-active" class="activeOk1"></div>OK</button>
            
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

function validateForm(){

    $("#focusid").val('');
    var PARTICULAR  =   $.trim($("[id*=PARTICULAR]").val());
    var EARNING_DEDUCTION  =   $.trim($("[id*=EARNING_DEDUCTION]").val());
    var EARNING_DEDUCTION_HEAD  =   $.trim($("[id*=EARNING_DEDUCTION_HEAD]").val());

    
    $("#OkBtn1").hide();

     if(PARTICULAR ===""){
        $("#focusid").val('PARTICULAR_0');
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").hide();  
        $("#OkBtn").show();
        $("#AlertMessage").text('Please enter Particular.');
        $("#alert").modal('show');
        $("#OkBtn").focus();
        highlighFocusBtn('activeOk');
        return false;
    }
    else if(EARNING_DEDUCTION ===""){
        $("#focusid").val('EARNING_DEDUCTION_0');
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").hide();  
        $("#OkBtn").show();
        $("#AlertMessage").text('Please enter Earning / Deduction.');
        $("#alert").modal('show');
        $("#OkBtn").focus();
        highlighFocusBtn('activeOk');
        return false;
    }
    else if(EARNING_DEDUCTION_HEAD ===""){
        $("#focusid").val('EARNING_DEDUCTION_HEAD_0');
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").hide();  
        $("#OkBtn").show();
        $("#AlertMessage").text('Please enterEarning / Deduction Head Code.');
        $("#alert").modal('show');
        $("#OkBtn").focus();
        highlighFocusBtn('activeOk');
        return false;
    }
    else{
        event.preventDefault();
        var RackArray = []; 
        var allblank1 = []; 
        var allblank5 = []; 
        var texid1    = "";
        var texid5    = "";
        
        $("[id*=PARTICULAR]").each(function(){
 
            if($.trim($(this).val()) ===""){
              allblank1.push('true');
              texid1 = $(this).attr('id');
            }
            else if ($.trim($(this).val()) ==="") {
              allblank5.push('true');
              texid5 = $(this).attr('id');
            }
            else{
              allblank1.push('false');
              allblank5.push('false');
            }

            RackArray.push($.trim($(this).val()));
           
        });

        if(jQuery.inArray("true", allblank1) !== -1){
            $("#focusid").val(texid1);
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").hide();  
            $("#OkBtn").show();
            $("#AlertMessage").text('Please enter Particular.');
            $("#alert").modal('show');
            $("#OkBtn").focus();
            highlighFocusBtn('activeOk');
            return false;
          }
          else if(jQuery.inArray("true", allblank5) !== -1){
            $("#focusid").val(texid5);
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").hide();  
            $("#OkBtn").show();
            $("#AlertMessage").text('Duplicate value.');
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
  
}

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
      var viewURL = '{{route("master",[353,"add"])}}';
      window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });

 var formResponseMst = $( "#frm_mst_add" );
     formResponseMst.validate();

        // $("#DESCRIPTIONS").blur(function(){
    //     $(this).val($.trim( $(this).val() ));
    //     $("#ERROR_DESCRIPTIONS").hide();
    //     validateSingleElemnet("DESCRIPTIONS");
    // });

    // $( "#DESCRIPTIONS" ).rules( "add", {
    //     required: true,
    //     //StringRegex: true,  //from custom.js
    //     normalizer: function(value) {
    //         return $.trim(value);
    //     },
    //     messages: {
    //         required: "Required field."
    //     }
    // });

    //validae single element
    function validateSingleElemnet(element_id){
      var validator =$("#frm_mst_add" ).validate();
         if(validator.element( "#"+element_id+"" )){
            //check duplicate code
          if(element_id=="PARTICULAR" || element_id=="attcode" ) {
            checkDuplicateCode();
          }

         }

         

    }

    // //check duplicate exist code
    function checkDuplicateCode(){
        
        //validate and save data
        var getDataForm = $("#frm_mst_add");
        var formData = getDataForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("master",[353,"codeduplicate"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
                if(data.exists) {
                    $(".text-danger").hide();
                    showError('ERROR_PARTICULAR',data.msg);
                    $("#PARTICULAR").focus();
                }                                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });
    }

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
            url:'{{route("master",[353,"save"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    if(data.errors.PARTICULAR){
                       // showError('ERROR_ATTCODE',data.errors.ATTCODE);
                        $("#YesBtn").hide();
                        $("#NoBtn").hide();
                        $("#OkBtn1").hide();
                        $("#OkBtn").show();
                        $("#AlertMessage").text("Attribute Code is "+data.errors.PARTICULAR);
                        $("#alert").modal('show');
                        $("#OkBtn").focus();
                    }
                   if(data.exist=='duplicate') {

                      $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn1").hide();
                      $("#OkBtn").show();

                      $("#AlertMessage").text(data.msg);

                      $("#alert").modal('show');
                      $("#OkBtn").focus();

                   }
                   if(data.save=='invalid') {

                      $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn1").hide();
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
                    $("#OkBtn1").show();
                    $("#OkBtn").hide();

                    $("#AlertMessage").text(data.msg);

                    $(".text-danger").hide();
                    $("#frm_mst_add").trigger("reset");

                    $("#alert").modal('show');
                    $("#OkBtn1").focus();

                  //  window.location.href='{{ route("master",[353,"index"])}}';
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
        $("#OkBtn1").hide();

        $(".text-danger").hide(); 
    }); ///ok button

    
    
    $("#btnUndo").click(function(){

        $("#AlertMessage").text("Do you want to erase entered information in this record?");
        $("#alert").modal('show');

        $("#YesBtn").data("funcname","fnUndoYes");
        $("#YesBtn").show();

        $("#NoBtn").data("funcname","fnUndoNo");
        $("#NoBtn").show();
        
        $("#OkBtn").hide();
        $("#OkBtn1").hide();
        $("#NoBtn").focus();
        highlighFocusBtn('activeNo');
        
    }); ////Undo button

    
    $("#OkBtn1").click(function(){

    $("#alert").modal('hide');
    $("#YesBtn").show();  //reset
    $("#NoBtn").show();   //reset
    $("#OkBtn").hide();
    $("#OkBtn1").hide();
    $(".text-danger").hide();
    window.location.href = "{{route('master',[353,'index'])}}";

    });


    
    $("#OkBtn").click(function(){
      $("#alert").modal('hide');

    });////ok button


   window.fnUndoYes = function (){
      
      //reload form
      window.location.href = "{{route('master',[353,'add'])}}";

   }//fnUndoYes

    function showError(pId,pVal){

      $("#"+pId+"").text(pVal);
      $("#"+pId+"").show();

    }//showError

    function highlighFocusBtn(pclass){
       $(".activeYes").hide();
       $(".activeNo").hide();
       
       $("."+pclass+"").show();
    }

   
    
</script>

@endpush