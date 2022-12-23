@extends('layouts.app') @section('content')

<div class="container-fluid topnav">
    <div class="row">
        <div class="col-lg-2">
            <a href="{{route('master',[196,'index'])}}" class="btn singlebt">Gratuity Master</a>
        </div>
        <!--col-2-->

        <div class="col-lg-10 topnav-pd">
            <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
            <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
            <button class="btn topnavbt" id="btnSave" tabindex="3"><i class="fa fa-save"></i> Save</button>
            <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
            <button class="btn topnavbt" disabled="disabled"><i class="fa fa-print"></i> Print</button>
            <button class="btn topnavbt" id="btnUndo"><i class="fa fa-undo"></i> Undo</button>
            <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
            <button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-lock"></i> Approved</button>
            <button class="btn topnavbt" id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
            <button class="btn topnavbt" id="btnExit"><i class="fa fa-power-off"></i> Exit</button>
        </div>
        <!--col-10-->
    </div>
    <!--row-->
</div>
<!--topnav-->

<div class="container-fluid purchase-order-view filter">
    <form id="frm_mst_add" method="POST">
        @CSRF
        <div class="inner-form">
            <div class="row">
                <div class="col-lg-2 pl"><p>Gratuity Code</p></div>
                <div class="col-lg-2 pl">
                    <div class="col-lg-11 pl">
                        {{-- <input
                            type="text"
                            name="GRATUITY_CODE"
                            id="GRATUITY_CODE"
                            onkeypress="return AlphaNumaric(event,this)"
                            value="{{ old('GRATUITY_CODE') }}"
                            class="form-control mandatory"
                            autocomplete="off"
                            maxlength="20"
                            tabindex="1"
                            style="text-transform: uppercase;"
                        /> --}}
                        @if(!empty($objDD))
                            @if($objDD->SYSTEM_GRSR == "1")
                                <input type="text" name="GRATUITY_CODE" id="GRATUITY_CODE" value="{{ $objDOCNO }}" class="form-control mandatory" tabindex="1" maxlength="10" autocomplete="off" readonly style="text-transform:uppercase" autofocus >
                            @endif
                            @if($objDD->MANUAL_SR == "1")
                                <input type="text" name="GRATUITY_CODE" id="GRATUITY_CODE" value="{{ old('GRATUITY_CODE') }}" class="form-control mandatory"  maxlength="{{$objDD->MANUAL_MAXLENGTH}}" tabindex="1" autocomplete="off" style="text-transform:uppercase" autofocus >
                            @endif
                        @endif  
                        <span class="text-danger" id="ERROR_GRATUITY_CODE"></span>
                    </div>
                </div>
                <div class="col-lg-2 pl"><p>Description</p></div>
                <div class="col-lg-5 pl">
                    <input type="text" name="GRATUITY_DESC" id="GRATUITY_DESC" class="form-control mandatory" value="{{ old('GRATUITY_DESC') }}" maxlength="200" tabindex="2" />
                    <span class="text-danger" id="ERROR_GRATUITY_DESC"></span>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-2 pl"><p>Gratuity Type</p></div>
                <input type="radio" name="GratuityType" id="gratuity_type1" value="1" style="margin-right: 10;" />
                <div class="col-lg-1 pl"><p>Gratuity Rate (%)</p></div>
                <div class="col-lg-2 pl">
                    <div class="col-lg-11 pl">
                        <input type="text" name="GRATUITY_RATE" id="GRATUITY_RATE" value="{{ old('GRATUITY_RATE') }}" class="form-control mandatory" autocomplete="off" maxlength="20" tabindex="1" />
                        <span class="text-danger" id="ERROR_GRATUITY_RATE"></span>
                    </div>
                </div>
                <div class="col-lg-2 pl"><p>Minimum Tenure in years for eligibility</p></div>
                <div class="col-lg-2 pl">
                    <div class="col-lg-11 pl">
                        <input type="text" name="MIN_YEAR" id="MIN_YEAR" value="{{ old('MIN_YEAR') }}" class="form-control mandatory" autocomplete="off" maxlength="20" tabindex="1" />
                        <span class="text-danger" id="ERROR_MIN_YEAR"></span>
                    </div>
                </div>
                <div class="col-lg-1 pl"><p>Max Gratuity</p></div>
                <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
                        <input type="text" name="MAX_GRATUITY" id="MAX_GRATUITY" value="{{ old('MAX_GRATUITY') }}" class="form-control mandatory" autocomplete="off" maxlength="20" tabindex="1" />
                        <span class="text-danger" id="ERROR_MAX_GRATUITY"></span>
                    </div>
                </div>
            </div>
            <div class="row" style="margin-left: 244px;">
                <div class="col-lg-2 pl"><p>No of days of Gratuity per annum</p></div>
                <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
                        <input type="text" name="GRATUITY_DAYS" id="GRATUITY_DAYS" value="{{ old('GRATUITY_DAYS') }}" class="form-control mandatory" autocomplete="off" maxlength="20" tabindex="1" />
                        <span class="text-danger" id="ERROR_GRATUITY_DAYS"></span>
                    </div>
                </div>
            </div>

            <div class="row" style="margin-left: 221px;">
                <input type="radio" name="GratuityType" id="gratuity_type2" value="2" style="margin-right: 10;" />
                <div class="col-lg-1 pl"><p>Fix Gratuity</p></div>
                <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
                        <input type="text" name="GRATUITY_FIX" id="GRATUITY_FIX" value="{{ old('GRATUITY_FIX') }}" class="form-control mandatory" autocomplete="off" maxlength="20" tabindex="1" style="width: 93px; margin-left: 21px;" />
                        <span class="text-danger" id="ERROR_GRATUITY_FIX"></span>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<!--purchase-order-view-->
@endsection @section('alert')
<!-- Alert -->
<div id="alert" class="modal" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" id="closePopup">&times;</button>
                <h4 class="modal-title">System Alert Message</h4>
            </div>
            <div class="modal-body">
                <h5 id="AlertMessage"></h5>
                <div class="btdiv">
                    <button class="btn alertbt" name="YesBtn" id="YesBtn" data-funcname="fnSaveData">
                        <div id="alert-active" class="activeYes"></div>
                        Yes
                    </button>
                    <button class="btn alertbt" name="NoBtn" id="NoBtn" data-funcname="fnUndoNo">
                        <div id="alert-active" class="activeNo"></div>
                        No
                    </button>
                    <button class="btn alertbt" name="OkBtn" id="OkBtn" style="display: none; margin-left: 90px;">
                        <div id="alert-active" class="activeOk"></div>
                        OK
                    </button>
                    <button class="btn alertbt" name="OkBtn1" id="OkBtn1" style="margin-left: 90px; display: none;">
                        <div id="alert-active" class="activeOk"></div>
                        OK
                    </button>
                </div>
                <!--btdiv-->
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

    $(document).ready(function(){

      $("#OT_RATE").ForceNumericOnly();
        $('#OT_RATE').on('blur',function(){
                if(intRegex.test($(this).val())){
                 $(this).val($(this).val()+'.00000')
                }
                event.preventDefault();
            });

    });

      $('#btnAdd').on('click', function() {
          var viewURL = '{{route("master",[196,"add"])}}';
          window.location.href=viewURL;
      });

      $('#btnExit').on('click', function() {
        var viewURL = '{{route('home')}}';
        window.location.href=viewURL;
      });

     var formResponseMst = $( "#frm_mst_add" );
         formResponseMst.validate();

        $("#GRATUITY_CODE").blur(function(){
          $(this).val($.trim( $(this).val() ));
          $("#ERROR_GRATUITY_CODE").hide();
          validateSingleElemnet("GRATUITY_CODE");

        });

        $( "#GRATUITY_CODE" ).rules( "add", {
            required: true,
            nowhitespace: true,
           // StringNumberRegex: true, //from custom.js
            messages: {
                required: "Required field.",
            }
        });

        $("#GRATUITY_DESC").blur(function(){
            $(this).val($.trim( $(this).val() ));
            $("#ERROR_GRATUITY_DESC").hide();
            validateSingleElemnet("GRATUITY_DESC");
        });

        $( "#GRATUITY_DESC" ).rules( "add", {
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
              if(element_id=="GRATUITY_CODE" || element_id=="GRATUITY_CODE" ) {
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
                url:'{{route("master",[196,"codeduplicate"])}}',
                type:'POST',
                data:formData,
                success:function(data) {
                    if(data.exists) {
                        $(".text-danger").hide();
                        showError('ERROR_GRATUITY_CODE',data.msg);
                        $("#GRATUITY_CODE").focus();
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

             var GRATUITY_CODE          =   $.trim($("#GRATUITY_CODE").val());
              if(GRATUITY_CODE ===""){
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").hide();  
                $("#OkBtn").show();              
                $("#AlertMessage").text('Please enter Gratuity Code.');
                $("#alert").modal('show');
                $("#OkBtn").focus();
                return false;
              }

              if ($('input[name="GratuityType"]:checked').length == 0) {
                     $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn1").hide();
                  $("#OkBtn").show();
                  $("#AlertMessage").text('Please select Gratuity Type.');
                  $("#alert").modal('show');
                  $("#OkBtn").focus();

                } else{



                        //set function nane of yes and no btn
                        $("#alert").modal('show');
                        $("#AlertMessage").text('Do you want to save to record.');
                        $("#YesBtn").data("funcname","fnSaveData");  //set dynamic fucntion name
                        $("#YesBtn").focus();
                        highlighFocusBtn('activeYes');
                }
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
                url:'{{route("master",[196,"save"])}}',
                type:'POST',
                data:formData,
                success:function(data) {

                    if(data.errors) {
                        $(".text-danger").hide();

                        if(data.errors.GRATUITY_CODE){
                          //  showError('ERROR_GRATUITY_CODE',data.errors.GRATUITY_CODE);
                            $("#YesBtn").hide();
                            $("#NoBtn").hide();
                            $("#OkBtn1").hide();
                            $("#OkBtn").show();
                            $("#AlertMessage").text("Gratuity Code is "+data.errors.GRATUITY_CODE);
                            $("#alert").modal('show');
                            $("#OkBtn").focus();
                        }
                        if(data.errors.GRATUITY_DESC){
                            //showError('ERROR_GRATUITY_DESC',data.errors.GRATUITY_DESC);
                            $("#YesBtn").hide();
                            $("#NoBtn").hide();
                            $("#OkBtn1").hide();
                            $("#OkBtn").show();
                            $("#AlertMessage").text("Description is required.");
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

                      //  window.location.href='{{ route("master",[196,"index"])}}';
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
            $("#GRATUITY_CODE").focus();

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
        $("#YesBtn").show();
        $("#NoBtn").show();
        $("#OkBtn").hide();
        $("#OkBtn1").hide();
        $(".text-danger").hide();
        window.location.href = "{{route('master',[196,'index'])}}";
    });

        $("#OkBtn").click(function(){
          $("#alert").modal('hide');

        });////ok button


       window.fnUndoYes = function (){

          //reload form
          window.location.href = "{{route('master',[196,'add'])}}";

       }//fnUndoYes


       window.fnUndoNo = function (){
          $("#GRATUITY_CODE").focus();
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



        $(function() { $("#GRATUITY_CODE").focus(); });

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

<script>
    function AlphaNumaric(e, t) {
        try {
            if (window.event) {
                var charCode = window.event.keyCode;
            } else if (e) {
                var charCode = e.which;
            } else {
                return true;
            }
            if ((charCode >= 48 && charCode <= 57) || (charCode >= 65 && charCode <= 90) || (charCode >= 97 && charCode <= 122)) return true;
            else return false;
        } catch (err) {
            alert(err.Description);
        }
    }

    $(document).ready(function () {
        $("#GRATUITY_RATE").ForceNumericOnly();
        $("#GRATUITY_RATE").on("blur", function () {
            if (intRegex.test($(this).val())) {
                $(this).val($(this).val() + ".0000");
            }
            event.preventDefault();
        });

        $("#MAX_GRATUITY").ForceNumericOnly();
        $("#MAX_GRATUITY").on("blur", function () {
            if (intRegex.test($(this).val())) {
                $(this).val($(this).val() + ".00");
            }
            event.preventDefault();
        });

        $("#GRATUITY_FIX").ForceNumericOnly();
        $("#GRATUITY_FIX").on("blur", function () {
            if (intRegex.test($(this).val())) {
                $(this).val($(this).val() + ".00");
            }
            event.preventDefault();
        });

        $("#gratuity_type1").click(function () {
            $("#GRATUITY_RATE").attr("required", "true");
            $("#MIN_YEAR").attr("required", "true");
            $("#MAX_GRATUITY").attr("required", "true");
            $("#GRATUITY_DAYS").attr("required", "true");

            $("#GRATUITY_FIX").val("");
            $("#GRATUITY_FIX").prop("disabled", true);

            $("#GRATUITY_RATE").prop("disabled", false);
            $("#MIN_YEAR").prop("disabled", false);
            $("#MAX_GRATUITY").prop("disabled", false);
            $("#GRATUITY_DAYS").prop("disabled", false);
        });

        $("#gratuity_type2").click(function () {
            $("#GRATUITY_FIX").attr("required", "true");

            $("#GRATUITY_RATE").prop("disabled", true);
            $("#MIN_YEAR").prop("disabled", true);
            $("#MAX_GRATUITY").prop("disabled", true);
            $("#GRATUITY_DAYS").prop("disabled", true);

            $("#GRATUITY_RATE").val("");
            $("#MIN_YEAR").val("");
            $("#MAX_GRATUITY").val("");
            $("#GRATUITY_DAYS").val("");

            $("#GRATUITY_FIX").prop("disabled", false);
        });
    });
</script>

@endpush
