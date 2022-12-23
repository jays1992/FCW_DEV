@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[140,'index'])}}" class="btn singlebt">Currency Conversion</a>
                </div><!--col-2-->

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

              </div><!--col-10-->

            </div><!--row-->
    </div><!--topnav-->	
   
    <div class="container-fluid purchase-order-view filter">     
         <form id="frm_mst_add" method="POST"  > 
          @CSRF
          <div class="inner-form">


            <div class="row">
              <div class="col-lg-1 pl"><p>Effective Date</p></div>
              <div class="col-lg-2 pl">
                    <input type="date" name="EFFDATE" id="EFFDATE" value="{{ old('EFFDATE') }}" class="form-control mandatory" autocomplete="off" tabindex="1" placeholder="dd/mm/yyyy" />
                    <span class="text-danger" id="ERROR_EFFDATE"></span>
              </div>
              
              <!--<div class="col-lg-1 pl col-md-offset-1"><p>End Date</p></div>
              <div class="col-lg-2 pl">
                    <input type="date" name="ENDDATE" id="ENDDATE"  value="{{ old('ENDDATE') }}" class="form-control mandatory" autocomplete="off" tabindex="2" placeholder="dd/mm/yyyy"  />
                    <span class="text-danger" id="ERROR_ENDDATE"></span> 
              </div>-->
            </div>

            <div class="row">
              <div class="col-lg-3 pl"><p>From</p></div>
              <div class="col-lg-3 pl col-md-offset-1"><p>To</p></div>
            </div>

            <div class="row">
			<div class="col-lg-1 pl"><p>Currency</p></div>
			<div class="col-lg-2 pl">
				<select name="FROMCRID_REF" id="FROMCRID_REF"  class="form-control mandatory" tabindex="3" onmousedown="(function(e){ e.preventDefault(); })(event, this)" >
					<option value="" selected >Select</option>
          @foreach($objCurList as $Cur)
          <option  value="{{$Cur->CRID}}" {{$dcurrency == $Cur->CRID ? 'selected':""}}>{{$Cur->CRCODE.' - '.$Cur->CRDESCRIPTION}}</option>
          @endforeach
				</select>
        <span class="text-danger" id="ERROR_FROMCRID_REF"></span>
			</div>
			
			<div class="col-lg-1 pl col-md-offset-1"><p>Currency</p></div>
			<div class="col-lg-2 pl ">
				<select name="TOCRID_REF" id="TOCRID_REF" class="form-control mandatory" tabindex="4" >
					<option value="" selected >Select</option>
          @foreach($objCurList1 as $Cur)
          <option value="{{$Cur->CRID}}">{{$Cur->CRCODE.' - '.$Cur->CRDESCRIPTION}}</option>
          @endforeach
				</select>
        <span class="text-danger" id="ERROR_TOCRID_REF"></span>
			</div>
		</div>	
		
		<div class="row">
			<div class="col-lg-1 pl"><p>Amount</p></div>
			<div class="col-lg-1 pl">
				<input type="text" name="FRAMOUNT" id="FRAMOUNT" readonly value="1" class="form-control mandatory"  maxlength="9" tabindex="5" >
        <span class="text-danger" id="ERROR_FRAMOUNT"></span>
      </div>
			
			<div class="col-lg-1 pl col-md-offset-2"><p>Amount</p></div>
			<div class="col-lg-1 pl ">
				<input type="text" name="TOAMOUNT" id="TOAMOUNT" class="form-control mandatory"  maxlength="9" tabindex="6" >
        <span class="text-danger" id="ERROR_TOAMOUNT"></span>
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
//date validate
$.validator.addMethod("ToDateValidate", function(value,element) {
var fdate=$("#EFFDATE").val();
var today = new Date(fdate); 
var d = new Date(value); 
today.setHours(0, 0, 0, 0) ;
d.setHours(0, 0, 0, 0) ;

if(this.optional(element) || d < today){
    return false;
}
else {
    return true;
}
}, "Less date not allow");

  $('#btnAdd').on('click', function() {
      var viewURL = '{{route("master",[140,"add"])}}';
      window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });

 var formResponseMst = $( "#frm_mst_add" );
     formResponseMst.validate();

    $("#EFFDATE").blur(function(){
      $(this).val($.trim( $(this).val() ));
      $("#ERROR_EFFDATE").hide();
      validateSingleElemnet("EFFDATE");
         
    });

    $( "#EFFDATE" ).rules( "add", {
        required: true,
        messages: {
            required: "Required field.",
        }
    });

    // $("#ENDDATE").blur(function(){
    //   $(this).val($.trim( $(this).val() ));
    //     $("#ERROR_ENDDATE").hide();
    //     validateSingleElemnet("ENDDATE");
    // });

    // $( "#ENDDATE" ).rules( "add", {
    //     required: true,
    //     ToDateValidate:true,
    //     normalizer: function(value) {
    //         return $.trim(value);
    //     },
    //     messages: {
    //         required: "Required field."
    //     }
    // });

    $("#FROMCRID_REF").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_FROMCRID_REF").hide();
        validateSingleElemnet("FROMCRID_REF");
    });

    $( "#FROMCRID_REF" ).rules( "add", {
        required: true,
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field."
        }
    });

    $("#TOCRID_REF").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_TOCRID_REF").hide();
        validateSingleElemnet("TOCRID_REF");
    });

    $( "#TOCRID_REF" ).rules( "add", {
        required: true,
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field."
        }
    });

    $("#FRAMOUNT").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_FRAMOUNT").hide();
        validateSingleElemnet("FRAMOUNT");
    });

    $( "#FRAMOUNT" ).rules( "add", {
        required: true,
        OnlyNumberDec:true,
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field."
        }
    });

    $("#TOAMOUNT").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_TOAMOUNT").hide();
        validateSingleElemnet("TOAMOUNT");
    });

    $( "#TOAMOUNT" ).rules( "add", {
        required: true,
        OnlyNumberDec:true,
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
         

         }
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
            url:'{{route("master",[140,"save"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    if(data.errors.EFFDATE){
                        showError('ERROR_EFFDATE',data.errors.EFFDATE);
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
                   if(data.save=='exist') {

                    console.log("succes MSG="+data.msg);                    
                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn").show();
                    $("#AlertMessage").text(data.msg);
                    $(".text-danger").hide();
                    $("#frm_mst_add").trigger("reset");
                    $("#alert").modal('show');
                    $("#OkBtn").focus();
                   window.location.href='{{ route("master",[140,"index"])}}';
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

                  //  window.location.href='{{ route("master",[140,"index"])}}';
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
        $("#EFFDATE").focus();
        
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

    $("#FROMCRID_REF").click(function(){
      return false;

    });////ok button


   window.fnUndoYes = function (){
      
      //reload form
      window.location.href = "{{route('master',[140,'add'])}}";

   }//fnUndoYes


   window.fnUndoNo = function (){
      $("#EFFDATE").focus();
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

    $(function() { 
      $("#EFFDATE").focus(); 
    
    });
    

</script>

@endpush