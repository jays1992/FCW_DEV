@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[171,'index'])}}" class="btn singlebt">Designation Master</a>
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
         <form id="frm_mst_add" method="POST"  > 
          @CSRF
          <div class="inner-form">
              
                <div class="row">
                  <div class="col-lg-2 pl"><p>Designation Code</p></div>
                  <div class="col-lg-2 pl">
                    <div class="col-lg-11 pl">
                        {{-- <input type="text" name="DESGCODE" id="DESGCODE" onkeypress="return AlphaNumaric(event,this)" value="{{ old('DESGCODE') }}" class="form-control mandatory" autocomplete="off" maxlength="20" tabindex="1" style="text-transform:uppercase" /> --}}
                        @if(!empty($objDD))
                            @if($objDD->SYSTEM_GRSR == "1")
                                <input type="text" name="DESGCODE" id="DESGCODE" value="{{ $objDOCNO }}" class="form-control mandatory" tabindex="1" maxlength="10" autocomplete="off" readonly style="text-transform:uppercase" autofocus >
                            @endif
                            @if($objDD->MANUAL_SR == "1")
                                <input type="text" name="DESGCODE" id="DESGCODE" value="{{ old('DESGCODE') }}" class="form-control mandatory"  maxlength="{{$objDD->MANUAL_MAXLENGTH}}" tabindex="1" autocomplete="off" style="text-transform:uppercase" autofocus >
                            @endif
                        @endif  
                        <span class="text-danger" id="ERROR_DESGCODE"></span> 
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-2 pl"><p>Description</p></div>
                  <div class="col-lg-5 pl">
                    <input type="text" name="DESCRIPTIONS" id="DESCRIPTIONS" class="form-control mandatory" value="{{ old('DESCRIPTIONS') }}" maxlength="200" tabindex="2"  />
                    <span class="text-danger" id="ERROR_DESCRIPTIONS"></span> 
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-2 pl"><p>Post Level Code</p></div>
                  <div class="col-lg-2 pl">
                    <div class="col-lg-11 pl">
                        <input type="text" name="PLID_REF_POPUP" id="PLID_REF_POPUP" readonly  value="{{ old('PLID_REF') }}" class="form-control mandatory" autocomplete="off" maxlength="120" tabindex="1" style="text-transform:uppercase" />
                        <input type="hidden" name="PLID_REF" id="PLID_REF" />
                        <span class="text-danger" id="ERROR_PLID_REF"></span> 
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



<!-- Alert -->
<!--POST LEVEL Popup-->
<div id="postlevel_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='postlevelidref_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Post Level List</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="postlevel_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th class="ROW1" style="width: 10%" align="center">Select</th> 
            <th class="ROW2" style="width: 40%">Post Level Code</th>
            <th  class="ROW3"style="width: 40%">Description</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
          <td class="ROW2"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="postlevel_codesearch" onkeyup="searchpostlevelCode()" /></td>
          <td class="ROW3"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="postlevel_namesearch" onkeyup="searchpostlevelName()" /></td>
        </tr>
        </tbody>
      </table>


      <table id="postlevel_tab" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody id="postlevel_body">
        @foreach ($PostLevelList as $index=>$PostList)
        <tr >
          <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_PLID_REF[]"  id="postidref_{{ $PostList->PLID }}" class="postlevel_tab" value="{{ $PostList->PLID }}" ></td>
          <td class="ROW2"  style="width: 40%">{{ $PostList->PLCODE }}
          <input type="hidden" id="txtpostidref_{{ $PostList->PLID }}" data-desc="{{ $PostList->PLCODE }}-{{ $PostList->DESCRIPTIONS }}" data-descname="{{ $PostList->DESCRIPTIONS }}" value="{{ $PostList-> PLID }}"/>
          </td>
          <td class="ROW3"  style="width: 40%">{{ $PostList->DESCRIPTIONS }}</td>
        </tr>
        @endforeach
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
@endsection
<!-- btnSave -->

@push('bottom-scripts')
<script>

  $('#btnAdd').on('click', function() {
      var viewURL = '{{route("master",[171,"add"])}}';
      window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });

 var formResponseMst = $( "#frm_mst_add" );
     formResponseMst.validate();

    $("#DESGCODE").blur(function(){
      $(this).val($.trim( $(this).val() ));
      $("#ERROR_DESGCODE").hide();
      validateSingleElemnet("DESGCODE");
         
    });

    $( "#DESGCODE" ).rules( "add", {
        required: true,
        nowhitespace: true,
        //StringNumberRegex: true, //from custom.js
        messages: {
            required: "Required field.",
        }
    });

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
    $( "#PLID_REF_POPUP" ).rules( "add", {
        required: true,
        //StringRegex: true,  //from custom.js
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field."
        }
    });

    $("#PLID_REF_POPUP").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_PLID_REF_POPUP").hide();
        validateSingleElemnet("PLID_REF_POPUP");
    });

    //validae single element
    function validateSingleElemnet(element_id){
      var validator =$("#frm_mst_add" ).validate();
         if(validator.element( "#"+element_id+"" )){
            //check duplicate code
          if(element_id=="DESGCODE" || element_id=="DESGCODE" ) {
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
            url:'{{route("master",[171,"codeduplicate"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
                if(data.exists) {
                    $(".text-danger").hide();
                    showError('ERROR_DESGCODE',data.msg);
                    $("#DESGCODE").focus();
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

              var DESGCODE          =   $.trim($("#DESGCODE").val());
              if(DESGCODE ===""){
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").hide();  
                $("#OkBtn").show();              
                $("#AlertMessage").text('Please enter Designation Code.');
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
    });//btnSave

  
    
    $("#YesBtn").click(function(){

        $("#alert").modal('hide');
        var customFnName = $("#YesBtn").data("funcname");
            window[customFnName]();

    }); //yes button


   window.fnSaveData = function (){

        //validate and save data
        event.preventDefault();
        $("#OkBtn1").hide();

        var getDataForm = $("#frm_mst_add");
        var formData = getDataForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("master",[171,"save"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    if(data.errors.PLCODE){
                       // showError('ERROR_PLCODE',data.errors.PLCODE);
                        $("#YesBtn").hide();
                        $("#NoBtn").hide();
                        $("#OkBtn1").hide();
                        $("#OkBtn").show();
                        $("#AlertMessage").text(" is "+data.errors.PLCODE);
                        $("#alert").modal('show');
                        $("#OkBtn").focus();
                    }
                    if(data.errors.DESCRIPTIONS){
                        //showError('ERROR_DESCRIPTIONS',data.errors.DESCRIPTIONS);
                        $("#YesBtn").hide();
                        $("#NoBtn").hide();
                        $("#OkBtn1").hide();
                        $("#OkBtn").show();
                        $("#AlertMessage").text(" is "+data.errors.DESCRIPTIONS);
                        $("#alert").modal('show');
                        $("#OkBtn").focus();
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
                      $("#OkBtn1").hide();

                      $("#AlertMessage").text(data.msg);

                      $("#alert").modal('show');
                      $("#OkBtn").focus();

                   }
                }
                if(data.success) {                   
                   // console.log("succes MSG="+data.msg);
                    
                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn1").show();
                    $("#OkBtn").hide();

                    $("#AlertMessage").text(data.msg);

                    $(".text-danger").hide();
                    $("#frm_mst_add").trigger("reset");

                    $("#alert").modal('show');
                    $("#OkBtn1").focus();

                  //  window.location.href='{{ route("master",[171,"index"])}}';
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
        $("#PLCODE").focus();
        
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
    window.location.href = "{{route('master',[171,'index'])}}";

    }); 


    
    $("#OkBtn").click(function(){
      $("#alert").modal('hide');

    });////ok button


   window.fnUndoYes = function (){
      
      //reload form
      window.location.href = "{{route('master',[171,'add'])}}";

   }//fnUndoYes


   window.fnUndoNo = function (){
      $("#PLCODE").focus();
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



    $(function() { $("#PLCODE").focus(); });




// POST LEVEL popup function

$("#PLID_REF_POPUP").on("click",function(event){ 
  $("#postlevel_popup").show();
});

$("#PLID_REF_POPUP").keyup(function(event){
  if(event.keyCode==13){
    $("#postlevel_popup").show();
  }
});

$("#postlevelidref_close").on("click",function(event){ 
  $("#postlevel_popup").hide();
});

$('.postlevel_tab').click(function(){


  var id          =   $(this).attr('id');


  var txtval      =   $("#txt"+id+"").val();
  var texdesc     =   $("#txt"+id+"").data("desc");
  var texdescname =   $("#txt"+id+"").data("descname");

  $("#PLID_REF_POPUP").val(texdesc);
 // $("#PLID_REF_POPUP").val(texdesc);
  $("#PLID_REF").val(txtval);

  $("#PLID_REF_POPUP").blur(); 
  
  $("#postlevel_popup").hide();
  $("#postlevel_codesearch").val('');
  $("#postlevel_namesearch").val('');
  searchpostlevelCode();
  $(this).prop("checked",false);
  event.preventDefault();
});



function searchpostlevelCode() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("postlevel_codesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("postlevel_tab");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[1];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}

function searchpostlevelName() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("postlevel_namesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("postlevel_tab");
      tr = table.getElementsByTagName("tr");
      for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[2];
        if (td) {
          txtValue = td.textContent || td.innerText;
          if (txtValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
          } else {
            tr[i].style.display = "none";
          }
        }       
  }
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


<script>
function AlphaNumaric(e, t) {
try {
if (window.event) {
var charCode = window.event.keyCode;
}
else if (e) {
var charCode = e.which;
}
else { return true; }
if ((charCode >= 48 && charCode <= 57) || (charCode >= 65 && charCode <= 90) || (charCode >= 97 && charCode <= 122))
return true;
else
return false;
}
catch (err) {
alert(err.Description);
}
}
</script>

@endpush