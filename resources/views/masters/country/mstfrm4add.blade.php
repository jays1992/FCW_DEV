@extends('layouts.app')
@section('content')

  <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[4,'index'])}}" class="btn singlebt">Country Master</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <a href="#" id="btnSelectedRows" class="btn topnavbt" disabled="disabled"><i class="fa fa-plus"></i> Add</a>
                        <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
                        <button id="btnSaveCountry"   class="btn topnavbt" tabindex="7"><i class="fa fa-save"></i> Save</button>
                        <button class="btn topnavbt" id="btnView"  disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt"  id="btnUndo" ><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-lock"></i> Approved</button>
                        <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-link" ></i> Attachment</button>
                        <a href="{{route('home')}}" class="btn topnavbt"><i class="fa fa-power-off"></i> Exit</a>
                </div><!--col-10-->

            </div><!--row-->
    </div><!--topnav-->	
   
    <div class="container-fluid purchase-order-view filter">     
         <form id="frm_mst_country" method="POST"  > 
          @CSRF
          <div class="inner-form">
              
                <div class="row">
                  <div class="col-lg-2 pl"><p>Country Code</p></div>
                  <div class="col-lg-3 pl">
                    @if(!empty($objDD))
                        @if(isset($objDD->SYSTEM_GRSR) && $objDD->SYSTEM_GRSR == "1")
                            <input type="text" name="CTRYCODE" id="CTRYCODE" value="{{ $objDOCNO }}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase" autofocus >
                        @endif
                        @if(isset($objDD->MANUAL_SR) && $objDD->MANUAL_SR == "1")
                            <input type="text" name="CTRYCODE" id="CTRYCODE" value="{{ old('CTRYCODE') }}" class="form-control mandatory" minlength="3" maxlength="{{$objDD->MANUAL_MAXLENGTH}}" tabindex="1" autocomplete="off" style="text-transform:uppercase" autofocus >
                        @endif
                    @endif    
                    <span class="text-danger" id="ERROR_CTRYCODE"></span> 
                    {{-- <input type="text" name="CTRYCODE" id="CTRYCODE" value="{{ old('CTRYCODE') }}" class="form-control mandatory" minlength="3" autocomplete="off" maxlength="3" tabindex="1" style="text-transform:uppercase" />
                    --}}
                  </div>
                
                  <div class="col-lg-2 pl col-md-offset-1"><p>Country Name</p></div>
                  <div class="col-lg-3 pl">
                    <input type="text" name="COUNTRY_NAME" id="COUNTRY_NAME" class="form-control mandatory" value="{{ old('COUNTRY_NAME') }}" maxlength="100" tabindex="2"  />
                    <span class="text-danger" id="ERROR_COUNTRY_NAME"></span> 
                  </div>
                </div>
          
              <div class="row">
                <div class="col-lg-2 pl"><p>ISD Code</p></div>
                <div class="col-lg-3 pl">
                    <input type="text" name="ISDCODE" id="ISDCODE" class="form-control" value="{{ old('ISDCODE') }}" maxlength="4"  tabindex="3" >
                </div>
                
                <div class="col-lg-2 pl col-md-offset-1"><p>Language</p></div>
                <div class="col-lg-3 pl ">
                  <input type="text" name="LANG" id="LANG" class="form-control" value="{{ old('LANG') }}"  maxlength="50"  tabindex="4" >
                </div>
              </div>

              <div class="row">
                <div class="col-lg-2 pl"><p>Continental</p></div>
                <div class="col-lg-3 pl">
                  <input type="text" name="CONTINENTAL" id="CONTINENTAL" class="form-control" value="{{ old('CONTINENTAL') }}" maxlength="50"  tabindex="5" >
                </div>
                
                <div class="col-lg-2 pl col-md-offset-1"><p>Capital</p></div>
                <div class="col-lg-3 pl">
                  <input type="text" name="CAPITAL" id="CAPITAL" class="form-control"  value="{{ old('CAPITAL') }}"   maxlength="50"  tabindex="6" >
                </div>
              </div>

          </div>
        </form>
    </div><!--purchase-order-view-->
@endsection
@section('alert')
<!-- Alert -->
<div id="alert" class="modal"  role="dialog"  data-backdrop="static" >
  <div class="modal-dialog" style="position:relative;top:82px;left:273px;"  >
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
<!-- Alert -->
@endsection
<!-- btnSaveCountry -->

@push('bottom-scripts')
<script>

 var formCountryMst = $( "#frm_mst_country" );
     formCountryMst.validate();

   
    //country code
    $("#CTRYCODE").blur(function(){
      $(this).val($.trim( $(this).val() ));
      $("#ERROR_CTRYCODE").hide();
      validateSingleElemnet("CTRYCODE");
         
    });

   
    $( "#CTRYCODE" ).rules( "add", {
        required: true,
        nowhitespace: true,
        //StringNumberRegex: true, //from custom.js
        messages: {
            required: "Required field.",
            minlength: jQuery.validator.format("min {0} char")
        }
    });

    //country name
    $("#COUNTRY_NAME").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_COUNTRY_NAME").hide();
        validateSingleElemnet("COUNTRY_NAME");      

    });
    $("#COUNTRY_NAME").keydown(function(){
        $("#ERROR_COUNTRY_NAME").hide();
        validateSingleElemnet("COUNTRY_NAME");      

    });

    $( "#COUNTRY_NAME" ).rules( "add", {
        required: true,
        StringRegex: true,  //from custom.js
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Country name is required."
        }
    });

    //ISD CODE
    $( "#ISDCODE" ).rules( "add", {
        required: false,
        nowhitespace: true,
        OnlyNumberRegex: true, //from custom.js
    });

    //validae single element
    function validateSingleElemnet(element_id){
      var validator =$("#frm_mst_country" ).validate();
         if(validator.element( "#"+element_id+"" )){
            //check duplicate code
          if(element_id=="CTRYCODE" || element_id=="ctrycode" ) {
            checkDuplicateCode();
          }

         }
    }

    // //check duplicate country code
    function checkDuplicateCode(){
        
        //validate and save data
        var countryForm = $("#frm_mst_country");
        var formData = countryForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("master",[4,"codeduplicate"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
                if(data.exists) {
                    $(".text-danger").hide();
                    showError('ERROR_CTRYCODE',data.msg);
                    $("#CTRYCODE").focus();
                }                                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });
    }

    //validate
    $( "#btnSaveCountry" ).click(function() {
        if(formCountryMst.valid()){

          $("#OkBtn1").hide();

          var CTRYCODE          =   $.trim($("#CTRYCODE").val());
          if(CTRYCODE ===""){
              $("#ProceedBtn").focus();
              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn").show();
              $("#AlertMessage").text('Please enter Country Code.');
              $("#alert").modal('show');
              $("#OkBtn").focus();
              return false;
          }

            //set function nane of yes and no btn 
            $("#alert").modal('show');
            $("#AlertMessage").text('Do you want to save to record.');
            $("#YesBtn").data("funcname","fnSaveData");  //set dynamic fucntion name
            $("#YesBtn").focus();
            highlighFocusBtn('activeYes');
       
        }
    });//btnSaveCountry

  
    
    $("#YesBtn").click(function(){

        $("#alert").modal('hide');
        var customFnName = $("#YesBtn").data("funcname");
            window[customFnName]();

    }); //yes button


   window.fnSaveData = function (){

          $("#OkBtn1").hide();
        //validate and save data
        event.preventDefault();

        var countryForm = $("#frm_mst_country");
        var formData = countryForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("master",[4,"save"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    if(data.errors.CTRYCODE){
                        //showError('ERROR_CTRYCODE',data.errors.CTRYCODE);
                        $("#YesBtn").hide();
                        $("#NoBtn").hide();
                        $("#OkBtn1").hide();
                        $("#OkBtn").show();
                        $("#AlertMessage").text("Country code is required.");
                        $("#alert").modal('show');
                        $("#OkBtn").focus();
                    }
                    if(data.errors.COUNTRY_NAME){
                        //showError('ERROR_COUNTRY_NAME',data.errors.COUNTRY_NAME);
                        $("#YesBtn").hide();
                        $("#NoBtn").hide();
                        $("#OkBtn1").hide();
                        $("#OkBtn").show();
                        $("#AlertMessage").text("Country name is required.");
                        $("#alert").modal('show');
                        $("#OkBtn").focus();
                    }
                   if(data.country=='duplicate') {

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
                      $("#OkBtn").show();
                      $("#OkBtn1").hide();

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
                    $("#OkBtn1").show();

                    $("#AlertMessage").text(data.msg);

                    $(".text-danger").hide();
                    $("#frm_mst_country").trigger("reset");

                    $("#alert").modal('show');
                    $("#OkBtn1").focus();

                  //  window.location.href='{{ route("master",[4,"index"])}}';
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

        $(".text-danger").hide();
        $("#CTRYCODE").focus();
        
    }); ///ok button

    $("#OkBtn1").click(function(){

      $("#alert").modal('hide');
      $("#YesBtn").show();  //reset
      $("#NoBtn").show();   //reset
      $("#OkBtn").hide();
      $("#OkBtn1").hide();
      $(".text-danger").hide();
      //$("#STCODE").focus();
      window.location.href = "{{route('master',[4,'add'])}}";

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


    
    $("#OkBtn").click(function(){
      $("#alert").modal('hide');

    });////ok button


   window.fnUndoYes = function (){
      
      //reload form
      window.location.href = "{{route('master',[4,'add'])}}";

   }//fnUndoYes


   window.fnUndoNo = function (){
      $("#CTRYCODE").focus();
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



    $(function() { $("#CTRYCODE").focus(); });

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