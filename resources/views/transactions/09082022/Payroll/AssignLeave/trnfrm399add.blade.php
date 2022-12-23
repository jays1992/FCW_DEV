@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('transaction',[399,'index'])}}" class="btn singlebt">Assign Leave</a>
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
                  <div class="col-lg-2 pl"><p>Pay Period Code*</p></div>
                    <div class="col-lg-2 pl">
                      <select name="PAYPERIODID_REF" id="PAYPERIODID_REF" class="form-control mandatory" onchange="getPayPrName(this.value)" tabindex="4">
                        <option value="" selected="">Select</option>
                        @foreach($objList as $val)
                        <option value="{{$val->PAYPERIODID}}">{{$val->PAY_PERIOD_CODE}}</option>
                        @endforeach
                      </select>
                      <span class="text-danger" id="ERROR_PAYPERIODID_REF"></span>                             
                    </div>

                    <div class="col-lg-2 pl"><p>Assign Leave Description</p></div>
                    <div class="col-lg-2 pl">
                      <input type="text" id="PAY_PERIOD_DESC" class="form-control" readonly  maxlength="100" > 
                    </div>
                    <div class="col-lg-2 pl"><p>Employee Code *</p></div>
                    <div class="col-lg-2 pl">
                    <select name="EMPID_REF" id="EMPID_REF" class="form-control mandatory" onchange="getEmpName(this.value)" tabindex="4">
                      <option value="" selected="">Select</option>
                      @foreach($objDataList as $val)
                      <option value="{{$val->EMPID}}">{{$val->EMPCODE}}</option>
                      @endforeach
                    </select>
                    <input type="hidden" id="focusid" >
                    <span class="text-danger" id="ERROR_EMPID_REF"></span>                             
                  </div>
                  </div>   
                
                  <div class="row">
                  <div class="col-lg-2 pl"><p>Name</p></div>
                    <div class="col-lg-2 pl">
                      <input type="text" name="FNAME" id="FNAME" class="form-control" readonly  maxlength="100" > 
                    </div>
                </div>

                <div id="Material" class="tab-pane fade in active">
                  <div class="row">
                    <div class="col-lg-4" style="padding-left: 15px;"></div></div>
                      <div class="table-responsive table-wrapper-scroll-y" style="height:500px;margin-top:10px;" >
                        Note:- 1 row mandatory in Tab
                        <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                        <thead id="thead1"  style="position: sticky;top: 0">                      
                          <tr>                          
                          <th rowspan="2"  width="3%">Date </th>                         
                          <th rowspan="2" width="3%">AS</th>
                          <th rowspan="2" width="3%">Leave Type Code</th>
                          <th rowspan="2" width="3%">Description</th>
                          <th rowspan="2" width="3%">Reason of Leave</th>
                          <th rowspan="2" width="3%">Remarks</th>
                          <th rowspan="2" width="3%">Action <input class="form-control" type="hidden" name="Row_Count" id ="Row_Count" value="1"></th>
                        </tr>                      
                          
                  </thead>
                        <tbody>
                          <tr  class="participantRow">
                              <td><input  class="form-control" type="date" name="ASSIGN_DT_0" id ="ASSIGN_DT_0"  autocomplete="off" style="width: 99%"></td>
                              <td>                              
                                <select name="ASSIGN_AS_0" id="ASSIGN_AS_0" class="form-control mandatory" tabindex="4">
                                  <option value="" selected="">Select</option>
                                  @foreach($objAttnceList as $val)
                                  <option value="{{$val->ATTENDANCE_STID}}">{{$val->ATTENDANCE_CODE}}- {{$val->ATTENDANCE_CODE_DESC}}</option>
                                  @endforeach
                                </select>
                              </td>
                              <td><select name="LTID_REF_0" id="LTID_REF_0" onchange="getLeaveTyName(this.id,this.value)" class="form-control mandatory" tabindex="4">
                                <option value="" selected="">Select</option>
                                @foreach($objLeaveList as $val)
                                <option value="{{$val->LTID}}">{{$val->LEAVETYPE_CODE}}</option>
                                @endforeach
                              </select> </td>
                              <td><input type="text" id="LEAVETYPE_DESC_0" class="form-control" readonly  maxlength="100" > </td>
                              <td><input  class="form-control" type="text" name="REASON_LEAVE_0" id ="REASON_LEAVE_0"  autocomplete="off" style="width: 99%"></td>
                              <td><input  class="form-control" type="text" name="REMARKS_0" id ="REMARKS_0"  autocomplete="off" style="width: 99%"></td>
                              <td align="center">
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
<div id="alert" class="modal"  role="dialog"  data-backdrop="static">
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
    var ASSIGN_DT                 =   $.trim($("[id*=ASSIGN_DT]").val());
    var PAYPERIODID_REF           =   $.trim($("#PAYPERIODID_REF").val());
    var EMPID_REF                 =   $.trim($("#EMPID_REF").val());
    $("#OkBtn1").hide();

    if(PAYPERIODID_REF ===""){
      $("#focusid").val('PAYPERIODID_REF');
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").hide();  
      $("#OkBtn").show();              
      $("#AlertMessage").text('Please enter Pay Period Code.');
      $("#alert").modal('show');
      $("#OkBtn").focus();
      return false;
    }
    else if(EMPID_REF ===""){
      $("#focusid").val('EMPID_REF');
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").hide();  
      $("#OkBtn").show();              
      $("#AlertMessage").text('Please enter Employee Code.');
      $("#alert").modal('show');
      $("#OkBtn").focus();
      return false;
    }
    
    else{
        event.preventDefault();
          var allblank1 = [];
          var allblank2 = [];
          var allblank3 = [];
          var allblank6 = [];
          var allblank7 = [];

          var focustext1= "";
          var focustext2= "";
          var focustext3= "";
          var focustext6= "";
          var focustext7= "";
        
          $('#example2').find('.participantRow').each(function(){
 
          if($.trim($(this).find("[id*=ASSIGN_DT]").val()) ==""){
            
            allblank1.push('false');
            focustext1 = $(this).find("[id*=ASSIGN_DT]").attr('id');
          }
            else if($.trim($(this).find("[id*=ASSIGN_AS]").val()) ==""){
              allblank2.push('false');
              focustext2 = $(this).find("[id*=ASSIGN_AS]").attr('id');
            }

            else if($.trim($(this).find("[id*=LTID_REF]").val()) ==""){
              allblank3.push('false');
              focustext3 = $(this).find("[id*=LTID_REF]").attr('id');
            }

            else if($.trim($(this).find("[id*=REASON_LEAVE]").val()) ==""){
              allblank6.push('false');
              focustext6 = $(this).find("[id*=REASON_LEAVE]").attr('id');
            } 
            else if($.trim($(this).find("[id*=REMARKS]").val()) ==""){
              allblank7.push('false');
              focustext7 = $(this).find("[id*=REMARKS]").attr('id');
            }             

              });

        if(jQuery.inArray("false", allblank1) !== -1){
            $("#focusid").val(focustext1);
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").hide();  
            $("#OkBtn").show();
            $("#AlertMessage").text('Please enter Date.');
            $("#alert").modal('show');
            $("#OkBtn").focus();
            highlighFocusBtn('activeOk');
            return false;
          }
          else if(jQuery.inArray("false", allblank2) !== -1){
            $("#focusid").val(focustext2);
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").hide();  
            $("#OkBtn").show();
            $("#AlertMessage").text('Please enter AS.');
            $("#alert").modal('show');
            $("#OkBtn").focus();
            highlighFocusBtn('activeOk');
            return false;
          }
          else if(jQuery.inArray("false", allblank3) !== -1){
            $("#focusid").val(focustext3);
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").hide();  
            $("#OkBtn").show();
            $("#AlertMessage").text('Please enter Leave Type Code.');
            $("#alert").modal('show');
            $("#OkBtn").focus();
            highlighFocusBtn('activeOk');
            return false;
          }    
          else if(jQuery.inArray("false", allblank6) !== -1){
            $("#focusid").val(focustext6);
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").hide();  
            $("#OkBtn").show();
            $("#AlertMessage").text('Please enter Reason of Leave.');
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

  $('#btnAdd').on('click', function() {
      var viewURL = '{{route("transaction",[399,"add"])}}';
      window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });

 var formResponseMst = $( "#frm_mst_add" );
     formResponseMst.validate();
    $("#DESCRIPTIONS").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_DESCRIPTIONS").hide();
        validateSingleElemnet("DESCRIPTIONS");
    });

    $( "#DESCRIPTIONS" ).rules( "add", {
        required: true,
        //StringRegex: true,  //from custom.js
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field."
        }
    });

    //validae single element
    function validateSingleElemnet(element_id){
      var validator =$("#frm_mst_add" ).validate();
         if(validator.element( "#"+element_id+"" )){
            //check duplicate code
          if(element_id=="ATTCODE" || element_id=="attcode" ) {
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
            url:'{{route("transaction",[399,"codeduplicate"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
                if(data.exists) {
                    $(".text-danger").hide();
                    showError('ERROR_ATTCODE',data.msg);
                    $("#ATTCODE").focus();
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
            url:'{{route("transaction",[399,"save"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();                    
                    if(data.errors.DESCRIPTIONS){
                       // showError('ERROR_DESCRIPTIONS',data.errors.DESCRIPTIONS);
                        $("#YesBtn").hide();
                        $("#NoBtn").hide();
                        $("#OkBtn1").hide();
                        $("#OkBtn").show();
                        $("#AlertMessage").text("Attribute Description is "+data.errors.DESCRIPTIONS);
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

                  //  window.location.href='{{ route("transaction",[399,"index"])}}';
                }
                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });
      
   } // fnSaveData


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
  var rowCount1 = $('#Row_Count').val();
  rowCount1 = parseInt(rowCount1)+1;
  $('#Row_Count').val(rowCount1);
  $clone.find('.remove').removeAttr('disabled'); 
  event.preventDefault();
});


    
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
    window.location.href = "{{route('transaction',[399,'index'])}}";

    });


    
    $("#OkBtn").click(function(){
      $("#alert").modal('hide');

    });////ok button


   window.fnUndoYes = function (){
      
      //reload form
      window.location.href = "{{route('transaction',[399,'add'])}}";

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

    window.onload = function(){
      var strdd = <?php echo json_encode($objDD); ?>;
      if($.trim(strdd)==""){     
        $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn").show();
          $("#AlertMessage").text('Please contact to administrator for creating document numbering.');
          $("#alert").modal('show');
          $("#OkBtn").focus();
          highlighFocusBtn('activeOk');
      } 
    };




    function getPayPrName(PAYPERIODID){
		$("#PAY_PERIOD_DESC").val('');
		
		$.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
		
        $.ajax({
            url:'{{route("transaction",[399,"getPayPrName"])}}',
            type:'POST',
            data:{PAYPERIODID:PAYPERIODID},
            success:function(data) {
               $("#PAY_PERIOD_DESC").val(data);                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });	
  }

  function getLeaveTyName(id,LTID){

    var ROW_ID = id.split('_').pop();
		
		$.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
		
        $.ajax({
            url:'{{route("transaction",[399,"getLeaveTyName"])}}',
            type:'POST',
            data:{LTID:LTID},
            success:function(data) {
               $('#LEAVETYPE_DESC_'+ROW_ID+'').val(data);                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });	
  }

  function getEmpName(EMPID){
		$("#FNAME").val('');
		
		$.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
		
        $.ajax({
            url:'{{route("transaction",[399,"getEmpName"])}}',
            type:'POST',
            data:{EMPID:EMPID},
            success:function(data) {
               $("#FNAME").val(data);                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });	
  }
    
</script>

<script>
    function onlyNumberKey(evt) {
        var ASCIICode = (evt.which) ? evt.which : evt.keyCode
        if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
            return false;
        return true;
    }
</script>

@endpush