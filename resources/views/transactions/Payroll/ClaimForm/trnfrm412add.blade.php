@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('transaction',[412,'index'])}}" class="btn singlebt">Claim Form</a>
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
         <form id="frm_mst_add" method="POST" onsubmit="return validateForm()" class="needs-validation" enctype="multipart/form-data"> 
         
          @CSRF
          <div class="inner-form">
              
                <div class="row">
                  <div class="col-lg-2 pl"><p>Doc No*</p></div>
                    <div class="col-lg-2 pl">
                  <input type="text" name="CLAIM_DOC_NO" id="CLAIM_DOC_NO" value="{{$docarray['DOC_NO']}}" {{$docarray['READONLY']}} class="form-control" maxlength="{{$docarray['MAXLENGTH']}}" autocomplete="off" style="text-transform:uppercase" >
                  <script>docMissing(@json($docarray['FY_FLAG']));</script>
                    <span class="text-danger" id="ERROR_CLAIM_DOC_NO_REF"></span>                             
                    </div>

                    <div class="col-lg-2 pl"><p>Date</p></div>
                    <div class="col-lg-2 pl">
                      <input type="date" name="CLAIM_DOC_DT" id="CLAIM_DOC_DT" onchange='checkPeriodClosing(412,this.value,1),getDocNoByEvent("CLAIM_DOC_NO",this,@json($doc_req))' class="form-control" maxlength="100">
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

                    <div class="col-lg-2 pl"><p>Period From Date</p></div>
                    <div class="col-lg-2 pl">
                      <input type="date" name="PERIOD_FR_DT" id="PERIOD_FR_DT" class="form-control"  maxlength="100" > 
                    </div>

                    <div class="col-lg-2 pl"><p>To Date</p></div>
                    <div class="col-lg-2 pl">
                      <input type="date" name="PERIOD_TO_DT" id="PERIOD_TO_DT" class="form-control"  maxlength="100" > 
                    </div>
                </div>

                <div class="row">
                <div class="col-lg-2 pl"><p>Department*</p></div>
                    <div class="col-lg-2 pl">
                    <select name="DEPID_REF" id="DEPID_REF" class="form-control mandatory" onchange="getDepartName(this.value)" tabindex="4">
                      <option value="" selected="">Select</option>
                      @foreach($DataDepat as $val)
                      <option value="{{$val->DEPID}}">{{$val->DCODE}}</option>
                      @endforeach
                    </select>
                    <input type="hidden" id="focusid" >
                    <span class="text-danger" id="ERROR_EMPID_REF"></span>                             
                  </div>

                  <div class="col-lg-2 pl"><p>Designation</p></div>
                    <div class="col-lg-2 pl">
                      <input type="text" name="DESGID_REF" id="DESGID_REF" class="form-control" readonly  maxlength="100" > 
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
                          <th rowspan="2" width="3%">GL Code</th>
                          <th rowspan="2" width="3%">GL Description</th>
                          <th rowspan="2" width="3%">Remarks / Purpose</th>
                          <th rowspan="2" width="3%">Claim Amount</th>
                          <th rowspan="2" width="3%">Sanction Amount</th>
                          <th rowspan="2" width="3%">Attach any document (Y/N)</th>
                          <th rowspan="2" width="3%">document File</th>
                          <th rowspan="2" width="3%">Action <input class="form-control" type="hidden" name="Row_Count" id ="Row_Count" value="1"></th>
                        </tr>                      
                          
                  </thead>
                        <tbody>
                          <tr  class="participantRow">
                              <td><select name="GLID_REF_0" id="GLID_REF_0" onchange="getGlCodeName(this.id,this.value)" class="form-control mandatory" style="width: 150px;" >
                                <option value="" selected="">Select</option>
                                @foreach($objgeneralledger as $val)
                                <option value="{{$val->GLID}}">{{$val->GLCODE}}</option>
                                @endforeach
                              </select> </td>
                              <td><input  class="form-control" type="text" name="GLCODE_DESC_0"  id="GLCODE_DESC_0"  readonly  maxlength="100" > </td>
                              <td><input  class="form-control" type="text" name="REMARKS_0"      id ="REMARKS_0"  autocomplete="off" style="width: 99%"></td>
                              <td><input  class="form-control" type="text" name="CLAIM_AMT_0"       id ="CLAIM_AMT_0" onkeypress="return onlyNumberKey(event)"  autocomplete="off" style="width: 99%"></td>
                              <td><input  class="form-control" type="text" name="SANCTION_AMT_0"       id ="SANCTION_AMT_0" onkeypress="return onlyNumberKey(event)" autocomplete="off" style="width: 99%"></td>
                              
                              <td>
                                <select name="ANY_ATTACHMENT_0" id="ANY_ATTACHMENT_0" onchange="getAttDocType(this)" class="form-control" >
                                  <option value="">Select</option>
                                  <option value="1">YES</option>
                                  <option value="0">NO</option>
                                </select>
                            </td>

                            <td><input  class="form-control" type="file" name="FILENAME[]" id ="ATTDOCFILE_0"></td>

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
    var CLAIM_DOC_NO                 =   $.trim($("[id*=CLAIM_DOC_NO]").val());
    var CLAIM_DOC_DT           =   $.trim($("#CLAIM_DOC_DT").val());
    var EMPID_REF                 =   $.trim($("#EMPID_REF").val());
    var DEPID_REF                 =   $.trim($("#DEPID_REF").val());

    $("#OkBtn1").hide();

    if(CLAIM_DOC_NO ===""){
      $("#focusid").val('CLAIM_DOC_NO');
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").hide();  
      $("#OkBtn").show();              
      $("#AlertMessage").text('Please enter Doc No.');
      $("#alert").modal('show');
      $("#OkBtn").focus();
      return false;
    }
    else if(CLAIM_DOC_DT ===""){
      $("#focusid").val('CLAIM_DOC_DT');
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").hide();  
      $("#OkBtn").show();              
      $("#AlertMessage").text('Please enter Date.');
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
    else if(DEPID_REF ===""){
      $("#focusid").val('DEPID_REF');
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").hide();  
      $("#OkBtn").show();              
      $("#AlertMessage").text('Please enter Department.');
      $("#alert").modal('show');
      $("#OkBtn").focus();
      return false;
    }
    
    else{
        event.preventDefault();
          var allblank1 = [];
          var focustext1= "";
        
          $('#example2').find('.participantRow').each(function(){
 
          if($.trim($(this).find("[id*=GLID_REF]").val()) ==""){
            
            allblank1.push('false');
            focustext1 = $(this).find("[id*=GLID_REF]").attr('id');
          }
        });

        if(jQuery.inArray("false", allblank1) !== -1){
            $("#focusid").val(focustext1);
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").hide();  
            $("#OkBtn").show();
            $("#AlertMessage").text('Please enter GL Code.');
            $("#alert").modal('show');
            $("#OkBtn").focus();
            highlighFocusBtn('activeOk');
            return false;
          }
          else if(checkPeriodClosing(412,$("#CLAIM_DOC_DT").val(),0) ==0){
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text(period_closing_msg);
            $("#alert").modal('show');
            $("#OkBtn1").focus();
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
      var viewURL = '{{route("transaction",[412,"add"])}}';
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
            url:'{{route("transaction",[412,"codeduplicate"])}}',
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
        event.preventDefault();
        //var getDataForm = $("#frm_mst_add");
        //var formData = getDataForm.serialize();

        var formData = new FormData($("#frm_mst_add")[0]);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("transaction",[412,"save"])}}',
            type:'POST',
            enctype: 'multipart/form-data',
            contentType: false,     
            cache: false,           
            processData:false, 
            data:formData,
            success:function(data) {

              if(data.success) {                   
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#OkBtn").hide();
                $("#AlertMessage").text(data.msg);
                $(".text-danger").hide();
                $("#frm_mst_add").trigger("reset");
                $("#alert").modal('show');
                $("#OkBtn1").focus();
              //  window.location.href='{{ route("transaction",[412,"index"])}}';
                }               
                if(data.errors) {
                  $(".text-danger").hide();                    
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn1").hide();
                  $("#OkBtn").show();
                  $("#AlertMessage").text(data.msg);
                  $("#alert").modal('show');
                  $("#OkBtn").focus();
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
    window.location.href = "{{route('transaction',[412,'index'])}}";

    });
    
    $("#OkBtn").click(function(){
      $("#alert").modal('hide');

    });////ok button


   window.fnUndoYes = function (){
      
      //reload form
      window.location.href = "{{route('transaction',[412,'add'])}}";

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



    function getEmpName(EMPID){
		$("#FNAME").val('');
		
		$.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
		
        $.ajax({
            url:'{{route("transaction",[412,"getEmpName"])}}',
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

  function getDepartName(DEPID){		
		$.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
		
        $.ajax({
            url:'{{route("transaction",[412,"getDepartDestion"])}}',
            type:'POST',
            data:{DEPID:DEPID},
            success:function(data) {
               $("#DESGID_REF").val(data);                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });	
  }
    function getGlCodeName(id,GLID){

    var ROW_ID = id.split('_').pop();
		
		$.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
		
        $.ajax({
            url:'{{route("transaction",[412,"getGlCodeName"])}}',
            type:'POST',
            data:{GLID:GLID},
            success:function(data) {
               $('#GLCODE_DESC_'+ROW_ID+'').val(data);                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });	
  }

  $(document).ready(function(e) {
      var d = new Date(); 
      var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
      $('#CLAIM_DOC_DT').val(today);
    });

    function getAttDocType(data){
      var TEXT_ID     = data.id;
      var TEXT_VALUE  = data.value;
      var ROW_ID      = TEXT_ID.split('_').pop();

      if(TEXT_VALUE == 0){
        $('#ATTDOCFILE_'+ROW_ID).prop('disabled',true);
      }
      else{
        $('#ATTDOCFILE_'+ROW_ID).prop('disabled',false);
      } 
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