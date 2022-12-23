@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[123,'index'])}}" class="btn singlebt">Financial Year Master</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                <button class="btn topnavbt" id="btnAdd" disabled="disabled" ><i class="fa fa-plus"></i> Add</button>
                <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
                <button class="btn topnavbt" id="btnSave"  tabindex="7"  ><i class="fa fa-floppy-o"></i> Save</button>
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
         <form id="frm_mst_add" method="POST"  > 
          @CSRF
          <div class="inner-form">
              
                <div class="row">
                  <div class="col-lg-2 pl"><p>FY Code</p></div>
                  <div class="col-lg-3 pl">
                        <input type="text" name="FYCODE" id="FYCODE" value="{{ old('FYCODE') }}" class="form-control mandatory" autocomplete="off" maxlength="6" tabindex="1" style="text-transform:uppercase" />
                        <span class="text-danger" id="ERROR_FYCODE"></span>
                  </div>
                
                  <div class="col-lg-2 pl col-md-offset-1"><p>FY Description</p></div>
                  <div class="col-lg-3 pl">
                    <input type="text" name="FYDESCRIPTION" id="FYDESCRIPTION" class="form-control mandatory" value="{{ old('FYDESCRIPTION') }}" maxlength="30" tabindex="2"  />
                    <span class="text-danger" id="ERROR_FYDESCRIPTION"></span> 
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-2 pl"><p>FY Start Month</p></div>
                  <div class="col-lg-3 pl">
                   
                      <select name="FYSTMONTH" id="FYSTMONTH" class="form-control mandatory" tabindex="3">
                        <option value="" selected="">Select</option>
                        @foreach($objMonthList as $MonthList)
                        <option value="{{$MonthList->MTCODE}}">{{$MonthList->MTCODE}}</option>
                        @endforeach
                      </select>
                      <span class="text-danger" id="ERROR_FYSTMONTH"></span> 
                   
                  </div>

                  <div class="col-lg-2 pl col-md-offset-1"><p>FY Start Year</p></div>
                  <div class="col-lg-3 pl">
                      <select name="FYSTYEAR" id="FYSTYEAR" class="form-control mandatory" tabindex="4">
                        <option value="" selected="">Select</option>
                        @foreach($objYearList as $YearList)
                        <option value="{{$YearList->YRCODE}}">{{$YearList->YRCODE}}</option>
                        @endforeach
                      </select>
                      <span class="text-danger" id="ERROR_FYSTYEAR"></span> 
                  </div>

                </div>

                <div class="row">
                  <div class="col-lg-2 pl"><p>FY End Month</p></div>
                  <div class="col-lg-3 pl">
                      <select name="FYENDMONTH" id="FYENDMONTH" class="form-control mandatory" tabindex="5">
                        <option value="" selected="">Select</option>
                        @foreach($objMonthList as $MonthList)
                        <option value="{{$MonthList->MTCODE}}">{{$MonthList->MTCODE}}</option>
                        @endforeach
                      </select>
                      <span class="text-danger" id="ERROR_FYENDMONTH"></span> 
                  </div>

                  <div class="col-lg-2 pl col-md-offset-1"><p>FY End Year</p></div>
                  <div class="col-lg-3 pl">
                      <select name="FYENDYEAR" id="FYENDYEAR" class="form-control mandatory" tabindex="6">
                      <option value="" selected="">Select</option>
                        @foreach($objYearList as $YearList)
                        <option value="{{$YearList->YRCODE}}">{{$YearList->YRCODE}}</option>
                        @endforeach
                      </select>
                      <span class="text-danger" id="ERROR_FYENDYEAR"></span> 
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
            <button class="btn alertbt" name='OkBtn' id="OkBtn" style="display:none;margin-left: 90px;">
                <div id="alert-active" class="activeOk"></div>OK</button>
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

  $('#btnAdd').on('click', function() {
      var viewURL = '{{route("master",[123,"add"])}}';
      window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });

 var formResponseMst = $( "#frm_mst_add" );
     formResponseMst.validate();

    $("#FYCODE").blur(function(){
      $(this).val($.trim( $(this).val() ));
      $("#ERROR_FYCODE").hide();
      validateSingleElemnet("FYCODE");
         
    });

    $( "#FYCODE" ).rules( "add", {
        required: true,
        nowhitespace: true,
        StringNumberRegex: true, //from custom.js
        messages: {
            required: "Required field.",
        }
    });

    $("#FYDESCRIPTION").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_FYDESCRIPTION").hide();
        validateSingleElemnet("FYDESCRIPTION");
    });

    $( "#FYDESCRIPTION" ).rules( "add", {
        required: true,
        //StringRegex: true,  //from custom.js
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field."
        }
    });

    $("#FYSTMONTH").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_FYSTMONTH").hide();
        validateSingleElemnet("FYSTMONTH");
    });

    $( "#FYSTMONTH" ).rules( "add", {
        required: true,
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field."
        }
    });

    $("#FYSTYEAR").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_FYSTYEAR").hide();
        validateSingleElemnet("FYSTYEAR");
    });

    $( "#FYSTYEAR" ).rules( "add", {
        required: true,
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field."
        }
    });

    $("#FYENDMONTH").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_FYENDMONTH").hide();
        validateSingleElemnet("FYENDMONTH");
    });

    $( "#FYENDMONTH" ).rules( "add", {
        required: true,
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field."
        }
    });

    $("#FYENDYEAR").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_FYENDYEAR").hide();
        validateSingleElemnet("FYENDYEAR");
    });

    $( "#FYENDYEAR" ).rules( "add", {
        required: true,
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
          if(element_id=="FYCODE" || element_id=="fycode" ) {
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
            url:'{{route("master",[123,"codeduplicate"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
                if(data.exists) {
                    $(".text-danger").hide();
                    showError('ERROR_FYCODE',data.msg);
                    $("#FYCODE").focus();
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

            //set function nane of yes and no btn 
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
            url:'{{route("master",[123,"save"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    if(data.errors.FYCODE){
                        showError('ERROR_FYCODE',data.errors.FYCODE);
                    }
                    if(data.errors.FYDESCRIPTION){
                        showError('ERROR_FYDESCRIPTION',data.errors.FYDESCRIPTION);
                    }
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
                    console.log("succes MSG="+data.msg);
                    
                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn").show();

                    $("#AlertMessage").text(data.msg);

                    $(".text-danger").hide();
                    $("#frm_mst_add").trigger("reset");

                    $("#alert").modal('show');
                    $("#OkBtn").focus();

                  //  window.location.href='{{ route("master",[123,"index"])}}';
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
        $("#FYCODE").focus();
        
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
      window.location.href = "{{route('master',[123,'add'])}}";

   }//fnUndoYes


   window.fnUndoNo = function (){
      $("#FYCODE").focus();
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



    $(function() { $("#FYCODE").focus(); });
    

</script>

@endpush