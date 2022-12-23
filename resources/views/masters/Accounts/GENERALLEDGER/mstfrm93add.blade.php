@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[93,'index'])}}" class="btn singlebt">General Ledger Master</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                <button class="btn topnavbt" id="btnAdd" disabled="disabled" ><i class="fa fa-plus"></i> Add</button>
                <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
                <button class="btn topnavbt" id="btnSave"  tabindex="19"  ><i class="fa fa-save"></i> Save</button>
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
         <form id="frm_mst_generalledger" method="POST"  > 
          @CSRF
          <div class="inner-form">
              
                <div class="row">
                  <div class="col-lg-1 pl"><p>GL Code</p></div>
                  <div class="col-lg-2 pl">
                    <input type="text" name="GLCODE" id="GLCODE" value="{{ old('GLCODE') }}" class="form-control mandatory" autocomplete="off" maxlength="20" tabindex="1" style="text-transform:uppercase" />
                    <span class="text-danger" id="ERROR_GLCODE"></span> 
                  </div>
               
                  <div class="col-lg-1 pl col-md-offset-1"><p>Name</p></div>
                  <div class="col-lg-4 pl">
                    <input type="text" name="GLNAME" id="GLNAME" class="form-control mandatory" value="{{ old('GLNAME') }}" maxlength="100" tabindex="2"  />
                    <span class="text-danger" id="ERROR_GLNAME"></span> 
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-1 pl"><p>Alias</p></div>
                  <div class="col-lg-2 pl">
                    <input type="text" name="ALIAS" id="ALIAS" value="{{ old('ALIAS') }}" class="form-control" autocomplete="off" maxlength="50" tabindex="3" />
                    <span class="text-danger" id="ERROR_ALIAS"></span> 
                  </div>
               
                  <div class="col-lg-1 pl col-md-offset-1"><p>Account Sub Group</p></div>
                  <div class="col-lg-2 pl">
                      <input type="text" name="ASGID_REF_POPUP" id="ASGID_REF_POPUP" class="form-control mandatory" readonly tabindex="4" />
                      <input type="hidden" name="ASGID_REF" id="ASGID_REF" />
                      <span class="text-danger" id="ERROR_ASGID_REF"></span> 
                  </div>
                </div>


                <div class="row">
                  <br/>
                </div>
                
                <div class="row">
                  <div class="col-lg-3 pl"><p>Checks Flag</p></div>
                </div>
                
                <div class="row">
                  <div class="col-lg-2 pl "><p>Cost Centre Applicable</p></div>
                  <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
                    <select name="CC" id="CC" class="form-control " tabindex="5" >
                      <option value="0">No</option>
                      <option value="1">Yes</option>
                    </select>
                    </div>
                  </div>	
                  
                  <div class="col-lg-2 pl col-md-offset-2"><p>Sub Ledger</p></div>
                  <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
                    <select name="SUBLEDGER" id="SUBLEDGER" class="form-control " tabindex="6" >
                      <option value="0">No</option>
                      <option value="1">Yes</option>
                    </select>
                    </div>
                  </div>	
                </div>
                
                <div class="row">
                  <div class="col-lg-2 pl"><p>Bank Account</p></div>
                  <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
                    <select name="BANKAC" id="BANKAC" class="form-control " tabindex="7" >
                      <option value="0">No</option>
                      <option value="1">Yes</option>
                    </select>
                    </div>
                  </div>	
                  
                  <div class="col-lg-2 pl col-md-offset-2"><p>Belong to GST</p></div>
                  <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
                    <select name="GST" id="GST" class="form-control " tabindex="8" >
                      <option value="0">No</option>
                      <option value="1">Yes</option>
                    </select>
                    </div>
                  </div>	
                </div>
                
                <div class="row">
                  <div class="col-lg-2 pl"><p>GST Calculate on this GL</p></div>
                  <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
                    <select name="GST_ON_THISGL" id="GST_ON_THISGL" class="form-control " tabindex="9" >
                      <option value="0">No</option>
                      <option value="1">Yes</option>
                    </select>
                    </div>
                  </div>	
                  
                  <div class="col-lg-2 pl col-md-offset-2"><p>Belong to TDS</p></div>
                  <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
                    <select name="TDS" id="TDS" class="form-control" tabindex="10" >
                      <option value="0">No</option>
                      <option value="1">Yes</option>
                    </select>
                    </div>
                  </div>	
                </div>
                
                <div class="row">
                  <div class="col-lg-2 pl"><p>Inventory Values are affected</p></div>
                  <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
                    <select name="IVAFFECTED" id="IVAFFECTED" class="form-control" tabindex="11" >
                      <option value="0">No</option>
                      <option value="1">Yes</option>
                    </select>
                    </div>
                  </div>	
                  
                  <div class="col-lg-2 pl col-md-offset-2"><p>Interest Calculation</p></div>
                  <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
                    <select name="ICALCULATION" id="ICALCULATION" class="form-control " tabindex="12" >
                      <option value="0">No</option>
                      <option value="1">Yes</option>
                    </select>
                    </div>
                  </div>	
                </div>
                
                <div class="row">
                  <div class="col-lg-2 pl"><p>Use for Payroll</p></div>
                  <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
                    <select name="UPAYROLL" id="UPAYROLL" class="form-control " tabindex="13" >
                      <option value="0">No</option>
                      <option value="1">Yes</option>
                    </select>
                    </div>
                  </div>	
                  
                  <div class="col-lg-2 pl col-md-offset-2"><p>Belong to VAT </p></div>
                  <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
                    <select name="VAT" id="VAT" class="form-control " tabindex="14" >
                      <option value="0">No</option>
                      <option value="1">Yes</option>
                    </select>
                    </div>
                  </div>	
                </div>
                
                <div class="row">
                  <div class="col-lg-2 pl "><p>Belong to Service Tax</p></div>
                  <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
                    <select name="TAX" id="TAX" class="form-control " tabindex="15" >
                      <option value="0">No</option>
                      <option value="1">Yes</option>
                    </select>
                    </div>
                  </div>	
                  
                  <div class="col-lg-2 pl col-md-offset-2"><p>Belong to Sale (Revenue)</p></div>
                  <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
                    <select name="SALE" id="SALE" class="form-control " tabindex="16"  >
                      <option value="0">No</option>
                      <option value="1">Yes</option>
                    </select>
                    </div>
                  </div>	
                </div>
                
                <div class="row">
                  <div class="col-lg-2 pl "><p>Belong to Purchase</p></div>
                  <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
                    <select name="PURCHASE" id="PURCHASE" class="form-control " tabindex="17" >
                      <option value="0">No</option>
                      <option value="1">Yes</option>
                    </select>
                    </div>
                  </div>	
                  
                  <div class="col-lg-2 pl col-md-offset-2"><p>Belong to TCS</p></div>
                  <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
                    <select name="TCS" id="TCS" class="form-control " tabindex="18"  >
                      <option value="0">No</option>
                      <option value="1">Yes</option>
                    </select>
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
        </div><!--btdiv-->
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Alert -->

<div id="agrefpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='agrefpopup_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Account Sub Group</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="ag_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th class="ROW1" style="width: 10%" align="center">Select</th> 
            <th class="ROW2" style="width: 40%" >Code</th>
            <th class="ROW3" style="width: 40%" >Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
          <td class="ROW2"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="ag_codesearch" onkeyup="searchGLCode()" /></td>
          <td class="ROW3"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="ag_namesearch" onkeyup="searchGLName()" /></td>
        </tr>
        </tbody>
      </table>
  
      <table id="ag_tab2" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody>
        @foreach ($objAccountSubGroupList as $index=>$AsgList)

        <tr >
          <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_ASGID_REF[]" id="agref_{{ $AsgList->ASGID }}" class="clsagref" value="{{ $AsgList->ASGID }}" ></td>
          <td class="ROW2" style="width: 39%">{{ $AsgList->ASGCODE }}
          <input type="hidden" id="txtagref_{{ $AsgList->ASGID }}" data-desc="{{ $AsgList->ASGCODE }} - {{ $AsgList->ASGNAME }}" value="{{ $AsgList-> ASGID }}"/>
          </td>
          <td class="ROW3" style="width: 39%">{{ $AsgList->ASGNAME }}</td>
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

$("#ASGID_REF_POPUP").on("click",function(event){ 
  $("#agrefpopup").show();
});

$("#ASGID_REF_POPUP").keyup(function(event){
  if(event.keyCode==13){
    $("#agrefpopup").show();
  }
});

$("#agrefpopup_close").on("click",function(event){ 
  $("#agrefpopup").hide();
});

$('.clsagref').click(function(){
    var id = $(this).attr('id');
    var txtval =    $("#txt"+id+"").val();
    var texdesc =   $("#txt"+id+"").data("desc")

    $("#ASGID_REF_POPUP").val(texdesc);
    $("#ASGID_REF").val(txtval);
    $("#ASGID_REF_POPUP").blur(); 
    $("#CC").focus(); 
    $("#agrefpopup").hide();

    $("#ag_codesearch").val(''); 
    $("#ag_namesearch").val(''); 
    searchGLCode();
    $(this).prop("checked",false);
    event.preventDefault();

});

function searchGLCode() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("ag_codesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("ag_tab2");
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

function searchGLName() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("ag_namesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("ag_tab2");
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

"use strict";
	var w3 = {};
  w3.getElements = function (id) {
    if (typeof id == "object") {
      return [id];
    } else {
      return document.querySelectorAll(id);
    }
  };
	w3.sortHTML = function(id, sel, sortvalue) {
    var a, b, i, ii, y, bytt, v1, v2, cc, j;
    a = w3.getElements(id);
    for (i = 0; i < a.length; i++) {
      for (j = 0; j < 2; j++) {
        cc = 0;
        y = 1;
        while (y == 1) {
          y = 0;
          b = a[i].querySelectorAll(sel);
          for (ii = 0; ii < (b.length - 1); ii++) {
            bytt = 0;
            if (sortvalue) {
              v1 = b[ii].querySelector(sortvalue).innerText;
              v2 = b[ii + 1].querySelector(sortvalue).innerText;
            } else {
              v1 = b[ii].innerText;
              v2 = b[ii + 1].innerText;
            }
            v1 = v1.toLowerCase();
            v2 = v2.toLowerCase();
            if ((j == 0 && (v1 > v2)) || (j == 1 && (v1 < v2))) {
              bytt = 1;
              break;
            }
          }
          if (bytt == 1) {
            b[ii].parentNode.insertBefore(b[ii + 1], b[ii]);
            y = 1;
            cc++;
          }
        }
        if (cc > 0) {break;}
      }
    }
  };

  
  let ag_tab1 = "#ag_tab1";
  let ag_tab2 = "#ag_tab2";
  let ag_headers = document.querySelectorAll(ag_tab1 + " th");

  ag_headers.forEach(function(element, i) {
    element.addEventListener("click", function() {
      w3.sortHTML(ag_tab2, ".clsagref", "td:nth-child(" + (i + 1) + ")");
    });
  });

  $('#btnAdd').on('click', function() {
      var viewURL = '{{route("master",[93,"add"])}}';
      window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });

 var formResponseMst = $( "#frm_mst_generalledger" );
     formResponseMst.validate();

    $("#GLCODE").blur(function(){
      $(this).val($.trim( $(this).val() ));
      $("#ERROR_GLCODE").hide();
      validateSingleElemnet("GLCODE");
         
    });

    $( "#GLCODE" ).rules( "add", {
        required: true,
        nowhitespace: true,
        StringNumberRegex: true, //from custom.js
        messages: {
            required: "Required field.",
        }
    });

    $("#GLNAME").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_GLNAME").hide();
        validateSingleElemnet("GLNAME");
    });

    $( "#GLNAME" ).rules( "add", {
        required: true,
        //StringRegex: true,  //from custom.js
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field."
        }
    });


    $("#ASGID_REF").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_ASGID_REF").hide();
        validateSingleElemnet("ASGID_REF");
    });

    $( "#ASGID_REF" ).rules( "add", {
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
      var validator =$("#frm_mst_generalledger" ).validate();
         if(validator.element( "#"+element_id+"" )){
            //check duplicate code
          if(element_id=="GLCODE" || element_id=="indscode" ) {
            checkDuplicateCode();
          }

         }
    }

    // //check duplicate country code
    function checkDuplicateCode(){
        
        //validate and save data
        var getDataForm = $("#frm_mst_generalledger");
        var formData = getDataForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("master",[93,"codeduplicate"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
                if(data.exists) {
                    $(".text-danger").hide();
                    showError('ERROR_GLCODE',data.msg);
                    $("#GLCODE").focus();
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

        var getDataForm = $("#frm_mst_generalledger");
        var formData = getDataForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("master",[93,"save"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    if(data.errors.GLCODE){
                        showError('ERROR_GLCODE',data.errors.GLCODE);
                    }
                    if(data.errors.GLNAME){
                        showError('ERROR_GLNAME',data.errors.GLNAME);
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
                    $("#frm_mst_generalledger").trigger("reset");

                    $("#alert").modal('show');
                    $("#OkBtn").focus();

                  //  window.location.href='{{ route("master",[93,"index"])}}';
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
        $("#GLCODE").focus();
        window.location.href='{{ route("master",[93,"index"])}}';
        
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
      window.location.href = "{{route('master',[93,'add'])}}";

   }//fnUndoYes


   window.fnUndoNo = function (){
      $("#GLCODE").focus();
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



    $(function() { $("#GLCODE").focus(); });
    

</script>

@endpush