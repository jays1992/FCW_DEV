@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                  <a href="{{route('master',[223,'index'])}}" class="btn singlebt">Energy Meter</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                <button class="btn topnavbt" id="btnAdd" disabled="disabled" ><i class="fa fa-plus"></i> Add</button>
                <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
                <button class="btn topnavbt" id="btnSave"   ><i class="fa fa-save"></i> Save</button>
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
         <form id="frm_mst_add" method="POST"  > 
          @CSRF
          <div class="inner-form">

           <div class="row">
                  <div class="col-lg-2 pl"><p>Meter Code</p></div>
                  <div class="col-lg-2 pl">
                    <div class="col-lg-7 pl">
                      {{-- <input type="text" name="METER_CODE" id="METER_CODE" onkeypress="return AlphaNumaric(event,this)"  value="{{ old('METER_CODE') }}" class="form-control mandatory" autocomplete="off" maxlength="20" style="text-transform:uppercase"  /> --}}
                      @if(!empty($objDD))
                            @if($objDD->SYSTEM_GRSR == "1")
                                <input type="text" name="METER_CODE" id="METER_CODE" value="{{ $objDOCNO }}" class="form-control mandatory" tabindex="1" maxlength="10" autocomplete="off" readonly style="text-transform:uppercase" autofocus >
                            @endif
                            @if($objDD->MANUAL_SR == "1")
                                <input type="text" name="METER_CODE" id="METER_CODE" value="{{ old('METER_CODE') }}" class="form-control mandatory"  maxlength="{{$objDD->MANUAL_MAXLENGTH}}" tabindex="1" autocomplete="off" style="text-transform:uppercase" autofocus >
                            @endif
                        @endif  
                      <span class="text-danger" id="ERROR_METER_CODE"></span> 
                    </div>
                  </div>

                  <div class="col-lg-2 pl"><p>Meter Description</p></div>
                  <div class="col-lg-2 pl">
                    <input type="text" name="METER_DESC" id="METER_DESC" class="form-control mandatory" value="{{ old('METER_DESC') }}" maxlength="200"  />
                    <span class="text-danger" id="ERROR_METER_DESC"></span> 
                  </div>
            </div>

            <div class="row">
              <div class="col-lg-2 pl"><p>Starting Meter Reading (KWH)</p></div>
              <div class="col-lg-2 pl">                 
                    <input type="text" name="KWH" id="KWH" class="form-control mandatory"  autocomplete="off" maxlength="50"/>                 
              </div>
                <div class="col-lg-2 pl"><p>Starting Meter Reading (KVARH)</p></div>
                <div class="col-lg-2 pl">                 
                  <input type="text" name="KVARH" id="KVARH" class="form-control "  autocomplete="off" maxlength="50" />                 
                </div>
            </div>

            <div class="row">
                <div class="col-lg-2 pl"><p>Starting Meter Reading (KVAH)</p></div>
                <div class="col-lg-2 pl">                 
                  <input type="text" name="KVAH" id="KVAH" class="form-control "  autocomplete="off" maxlength="50" />                 
                </div>
                <div class="col-lg-2 pl"><p>Starting Meter Reading (MD)</p></div>
                <div class="col-lg-2 pl">
                  <input type="text" name="MD" id="MD" class="form-control "  autocomplete="off" maxlength="50" />
                </div>
            </div>

            <div class="row">
                <div class="col-lg-2 pl"><p>Power Factor</p></div>
                <div class="col-lg-2 pl">                 
                  <input type="text" name="POWER_FACTOR" id="POWER_FACTOR" class="form-control "  autocomplete="off" maxlength="100" />                 
                </div>
                <div class="col-lg-2 pl"><p>Date of Commissioning</p></div>
              <div class="col-lg-2 pl">
                <input type="date" name="DOCOMMISSION" class="form-control " id="DOCOMMISSION"  placeholder="dd/mm/yyyy"  />
              </div>
            </div>

            <div class="row">
              <div class="col-lg-2 pl"><p>Meter Company</p></div>
              <div class="col-lg-2 pl">                 
                <input type="text" name="METER_COMPANY" id="METER_COMPANY" class="form-control "  autocomplete="off" maxlength="100" />                 
              </div>
              <div class="col-lg-2 pl"><p>Brand</p></div>
              <div class="col-lg-2 pl">
                <input type="text" name="BRAND" id="BRAND" class="form-control "  autocomplete="off" maxlength="100" />
              </div>
            </div>



          <div class="row">
            <div class="col-lg-2 pl"><p>Model</p></div>
            <div class="col-lg-2 pl">
              <input type="text" name="MODEL" id="MODEL" class="form-control "  autocomplete="off" maxlength="100" />
            </div>
            <div class="col-lg-2 pl"><p>Serial No</p></div>
            <div class="col-lg-2 pl">
              <input type="text" name="SERIAL_NO" id="SERIAL_NO" class="form-control "  autocomplete="off" maxlength="20" />
            </div>
          </div>
         
          <div class="row">
            <div class="col-lg-2 pl"><p>Load Sanction</p></div>
            <div class="col-lg-2 pl">
              <input type="text" name="SANCTION_LOAD" id="SANCTION_LOAD" class="form-control "  autocomplete="off" maxlength="50" />
            </div>
            <div class="col-lg-2 pl"><p>Power Supply By</p></div>
            <div class="col-lg-2 pl">
              <input type="text" name="SUPPLY_BY" id="SUPPLY_BY" class="form-control "  autocomplete="off" maxlength="100" />
            </div>
          </div>

          <div class="row">
            <div class="col-lg-2 pl"><p>Remarks</p></div>
            <div class="col-lg-3 pl">
              <input type="text" name="REMARKS" id="REMARKS" class="form-control "  autocomplete="off" maxlength="100" />
            </div>
            
          </div>
          <br/>
          <br/>
      
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
{{-- Alert end --}}


@endsection
<!-- btnSave -->

@push('bottom-scripts')
<script>
  
  $('#btnAdd').on('click', function() {
      var viewURL = '{{route("master",[223,"add"])}}';
      window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });

 var formResponseMst = $( "#frm_mst_add" );
     formResponseMst.validate();

    

    $("#METER_CODE").blur(function(){
      $(this).val($.trim( $(this).val() ));
      $("#ERROR_METER_CODE").hide();
      validateSingleElemnet("METER_CODE");
         
    });

    $( "#METER_CODE" ).rules( "add", {
        required: true,
        nowhitespace: true,
        //StringNumberRegex: true, //from custom.js
        messages: {
            required: "Required field.",
        }
    });


    //validae single element
    function validateSingleElemnet(element_id){
      var validator =$("#frm_mst_add" ).validate();
         if(validator.element( "#"+element_id+"" )){
            //check duplicate code
          if(element_id=="METER_CODE" || element_id=="METER_CODE" ) {
            checkDuplicateCode();

          }

         }
    }

    // //check duplicate exist code
    function checkDuplicateCode(){
        
        //validate and save data
        var getDataForm = $("#METER_CODE");
        var formData = getDataForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("master",[223,"codeduplicate"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
                if(data.exists) {
                    $(".text-danger").hide();
                    showError('ERROR_METER_CODE',data.msg);
                    $("#METER_CODE").focus();
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

          $("#OkBtn1").hide();
            //set function nane of yes and no btn 
            //---
            $("#FocusId").val('');
            var METER_CODE           =   $.trim($("#METER_CODE").val());
            var METER_DESC           =   $.trim($("#METER_DESC").val());
            var KWH                   =   $.trim($("#KWH").val());
           
         
            if(METER_CODE ===""){
                $("#METER_CODE").focus();        
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").show();
                $("#OkBtn1").hide();
                $("#AlertMessage").text('Please enter value in Meter Code.');
                $("#alert").modal('show');
                $("#OkBtn").focus();
                return false;
            }
            else if(METER_DESC ===""){
               $("#METER_DESC").focus();        
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").show();
                $("#OkBtn1").hide();
                $("#AlertMessage").text('Please enter value in Meter Description.');
                $("#alert").modal('show');
                $("#OkBtn").focus();
                return false;
            } 
            else if(KWH ===""){
                $("#KWH").focus();        
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").show();
                $("#OkBtn1").hide();
                $("#AlertMessage").text('Please enter value in Starting Meter Reading (KWH).');
                $("#alert").modal('show');
                $("#OkBtn").focus();
                return false;
            } 
            
            //--- 


            $("#alert").modal('show');
            $("#AlertMessage").text('Do you want to save to record.');
            $("#YesBtn").data("funcname","fnSaveData");  //set dynamic fucntion name
            $("#YesBtn").focus();
            highlighFocusBtn('activeYes');
       
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

        var getDataForm = $("#frm_mst_add");
        var formData = getDataForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("master",[223,"save"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    if(data.errors.METER_CODE){
                        //showError('ERROR_METER_CODE',data.errors.METER_CODE);
                        $("#YesBtn").hide();
                        $("#NoBtn").hide();
                        $("#OkBtn1").hide();
                        $("#OkBtn").show();
                        $("#AlertMessage").text("Meter Code is "+data.errors.METER_CODE);
                        $("#alert").modal('show');
                        $("#OkBtn").focus();
                    }
                    if(data.errors.METER_DESC){
                        //showError('ERROR_NAME',data.errors.METER_DESC);
                        $("#YesBtn").hide();
                        $("#NoBtn").hide();
                        $("#OkBtn1").hide();
                        $("#OkBtn").show();
                        $("#AlertMessage").text("Meter Description is "+data.errors.METER_DESC);
                        $("#alert").modal('show');
                        $("#OkBtn").focus();
                    }
                   if(data.exist=='duplicate') {

                      $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn").show();
                      $("#OkBtn1").hide();

                      $("#AlertMessage").text(data.msg);

                      $("#alert").modal('show');
                      $("#OkBtn").focus();

                   }
                   if(data.save=='invalid') {

                      $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn").show();
                      $("#OkBtn1").hide();

                      $("#AlertMessage").text(data.msg);

                      $("#alert").modal('show');
                      $("#OkBtn").focus();

                   }
                }
                if(data.success) {                   
                    //console.log("succes MSG="+data.msg);
                    
                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn1").show();
                    $("#OkBtn").hide();

                    $("#AlertMessage").text(data.msg);

                    $(".text-danger").hide();
                    $("#frm_mst_add").trigger("reset");

                    $("#alert").modal('show');
                    $("#OkBtn1").focus();

                  //  window.location.href='{{ route("master",[223,"index"])}}';
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
        $("#METER_CODE").focus();
        
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
    window.location.href = "{{route('master',[223,'index'])}}";

    }); 


    
    $("#OkBtn").click(function(){
      $("#alert").modal('hide');

    });////ok button


   window.fnUndoYes = function (){
      
      //reload form
      window.location.href = "{{route('master',[223,'add'])}}";

   }//fnUndoYes


   window.fnUndoNo = function (){
      $("#METER_CODE").focus();
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



</script>

@endpush