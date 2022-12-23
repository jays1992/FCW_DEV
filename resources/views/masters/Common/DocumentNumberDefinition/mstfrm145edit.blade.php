@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[145,'index'])}}" class="btn singlebt">Document Number Definition</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <button href="#" id="btnSelectedRows" class="btn topnavbt" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
                        <button id="btnSave"   class="btn topnavbt" tabindex="4"><i class="fa fa-save"></i> Save</button>
                        <button class="btn topnavbt" id="btnView"  disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt"  id="btnUndo" ><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled" ><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove" {{ (isset($objRights->APPROVAL1) || isset($objRights->APPROVAL2) || isset($objRights->APPROVAL3) || isset($objRights->APPROVAL4) || isset($objRights->APPROVAL5)) &&  ($objRights->APPROVAL1||$objRights->APPROVAL2||$objRights->APPROVAL3||$objRights->APPROVAL4||$objRights->APPROVAL5) == 1 ? '' : 'disabled'}}><i class="fa fa-lock"></i> Approved</button>
                        <a href="#" class="btn topnavbt"  disabled="disabled"><i class="fa fa-link" ></i> Attachment</a>
                        <button class="btn topnavbt" id="btnExit"><i class="fa fa-power-off"></i> Exit</button>
                </div><!--col-10-->

            </div><!--row-->
    </div><!--topnav-->	
   
    <div class="container-fluid purchase-order-view filter">     
         <form id="frm_mst_edit" method="POST"  > 
          @CSRF
          {{isset($objResponse->DOCNODEFIID) ? method_field('PUT') : '' }}
          <div class="inner-form">
          
              
              <div class="row">
                  <div class="col-lg-2 pl"><p>Voucher Type</p></div>
                  <div class="col-lg-2 pl">
                  
                    <label>{{isset($objVtName->VCODE)?$objVtName->VCODE:''}}</label>
                    <input type="hidden" name="DOCNODEFIID" id="DOCNODEFIID" value="{{ $objResponse->DOCNODEFIID }}" />
                    <input type="hidden" name="VTID_REF" id="VTID_REF" value="{{ $objResponse->VTID_REF }}" autocomplete="off"  maxlength="20"   />
                    <input type="hidden" name="user_approval_level" id="user_approval_level" value="{{ $user_approval_level }}"  />
                  
                </div>

                <div class="col-lg-2 pl"><p>Voucher Type Description</p></div>
                  <div class="col-lg-3 pl">
                    <input type="text" class="form-control" id='vtdes' value="{{isset($objVtName->DESCRIPTIONS)?$objVtName->DESCRIPTIONS:''}}"  readonly >
                  </div>

                <div class="col-lg-1 pl"><p>Effective Date</p></div>
                  <div class="col-lg-2 pl">
                  <input type="date" name="EFFECTIVE_DT" class="form-control mandatory" id="EFFECTIVE_DT" value="{{isset($objResponse->EFFECTIVE_DT) && $objResponse->EFFECTIVE_DT !="" && $objResponse->EFFECTIVE_DT !="1900-01-01" ? $objResponse->EFFECTIVE_DT:''}}" tabindex="1" placeholder="dd/mm/yyyy"  />
                   
                    <span class="text-danger" id="ERROR_EFFECTIVE_DT"></span> 
                  </div>


            </div>

            <div class="row" >
              <div class="col-lg-2 pl"><p>Document Type</p></div>
              <div class="col-lg-2 pl">
                <div class="col-lg-11 pl">
                <select name="DOC_TYPE" id="DOC_TYPE" class="form-control" required >
                    <option value="">-- Please Select --</option>
                    <option value="master" {{ strtolower(trim($objResponse->DOC_TYPE))=="master" ? "selected":""}}>Master</option>
                    <option value="transactions"  {{ strtolower(trim($objResponse->DOC_TYPE))=="transactions" ? "selected":""}} >Transactions</option>
                </select>  
                </div>
              </div>      
          </div>
      

                
          

                
  <div class="row" >
      <div class="col-lg-2 pl"><p>Manual Series</p></div>
      <div class="col-lg-1 pl">
        <input type="checkbox" name="MANUAL_SR" id="MANUAL_SR" {{$objResponse->MANUAL_SR == 1 ? "checked" : ""}} value='1' onchange="SeriesType('MANUAL_SR');" >
      </div>
      <div class="col-lg-1 pl"><p>OR</p></div>
      <div class="col-lg-2 pl"><p>System generated</p></div>
      <div class="col-lg-1 pl">
        <input type="checkbox" name="SYSTEM_GRSR" id="SYSTEM_GRSR" {{$objResponse->SYSTEM_GRSR == 1 ? "checked" : ""}} value='1' onchange="SeriesType('SYSTEM_GRSR');" >
      </div>

      <div class="col-lg-1 pl"><p>Series Type</p></div>
      <div class="col-lg-1 pl">
        <select name="DOC_SERIES_TYPE" id="DOC_SERIES_TYPE" class="form-control" autocomplete="off"  >
          <option {{isset($objResponse->DOC_SERIES_TYPE) && $objResponse->DOC_SERIES_TYPE =='YEAR'?'selected="selected"':''}} value="YEAR">YEAR</option>
          <option {{isset($objResponse->DOC_SERIES_TYPE) && $objResponse->DOC_SERIES_TYPE =='MONTH'?'selected="selected"':''}} value="MONTH">MONTH</option>
        </select>
      </div>

      <div class="col-lg-1 pl"><p>Prefix Type</p></div>
      <div class="col-lg-2 pl">
        <select name="PREFIX_TYPE" id="PREFIX_TYPE" class="form-control" autocomplete="off"  >
          <option value="{{isset($objResponse->PREFIX_TYPE)?$objResponse->PREFIX_TYPE:''}}">{{isset($objResponse->PREFIX_TYPE)?$objResponse->PREFIX_TYPE:''}}</option>
        </select>
      </div>

    </div>


      <div class="row"><div class="col-lg-2 pl"><p style="text-decoration: underline;font-weight:bold;font-size:13px;">Pattern for Manual Series</p></div></div>
      
      <div class="row"  >
        <div class="col-lg-2 pl"><p>Maximum Alpha Numeric </p></div>
        <div class="col-lg-1 pl col-md-offset-1">
          <input type="text" name="MANUAL_MAXLENGTH" id="MANUAL_MAXLENGTH" value="{{ $objResponse->MANUAL_MAXLENGTH }}"  class="form-control mandatory" onkeypress="return isNumberKey(event,this)"  maxlength="9" disabled >
        </div>
      
    </div>

  <div class="row"><div class="col-lg-2 pl"><p style="text-decoration: underline;font-weight:bold;font-size:13px;">Pattern for Auto Series</p></div></div>

  <div class="row" >
    <div class="col-lg-2 pl"><p>Prefix Required</p></div>
    <div class="col-lg-1 pl">
      <input type="checkbox" name="PREFIX_RQ" id="PREFIX_RQ" {{$objResponse->PREFIX_RQ == 1 ? "checked" : ""}} value='1' onchange="PrefixRequired();" >
    </div>
    <div class="col-lg-1 pl">
      <input type="text" name="PREFIX" id="PREFIX" class="form-control" value="{{ $objResponse->PREFIX }}"  maxlength="4" onkeypress="return AlphaNumaric(event,this)" readonly >
    </div>
  
    <div class="col-lg-2 pl"><p>Is Separator Required after Prefix</p></div>
    <div class="col-lg-1 pl">
      <input type="checkbox" name="PRE_SEP_RQ" id="PRE_SEP_RQ" {{$objResponse->PRE_SEP_RQ == 1 ? "checked" : ""}} value='1' onchange="SeparatorRequiredAfterPrefix();" >
    </div>
    
    <div class="col-lg-1 pl"><p>Slash "/"</p></div>
    <div class="col-lg-1 pl">
      <input type="checkbox" name="PRE_SEP_SLASH" id="PRE_SEP_SLASH" {{$objResponse->PRE_SEP_SLASH == 1 ? "checked" : ""}} value='1' onchange="AfterPrefixType('PRE_SEP_SLASH');" disabled >
    </div>
    
    <div class="col-lg-1 pl"><p>OR</p></div>
    <div class="col-lg-1 pl"><p>Hyphen "-"</p></div>
    <div class="col-lg-1 pl">
      <input type="checkbox" name="PRE_SEP_HYPEN" id="PRE_SEP_HYPEN" {{$objResponse->PRE_SEP_HYPEN == 1 ? "checked" : ""}} value='1' onchange="AfterPrefixType('PRE_SEP_HYPEN');"   disabled >
    </div>
  </div>

<div class="row">
  <div class="col-lg-2 pl"><p>Number Series max digit</p></div>
  
  <div class="col-lg-1 pl col-md-offset-1">
    <input type="text" name="NO_MAX" id="NO_MAX" class="form-control mandatory" value="{{ $objResponse->NO_MAX }}"  maxlength="8"  onkeypress="return isNumberKey(event,this)" >
  </div>

  <div class="col-lg-2 pl"><p>Number Series Start from</p></div>
  <div class="col-lg-1 pl">
    <input type="text" name="NO_START" id="NO_START" class="form-control mandatory" value="{{ $objResponse->NO_START }}"  maxlength="8" onkeypress="return isNumberKey(event,this)" >
  </div>
  
  <div class="col-lg-2 pl"><p>New number in each FY</p></div>
  <div class="col-lg-1 pl">
    <input type="checkbox" name="NEWNO_FYEAR" id="NEWNO_FYEAR" {{$objResponse->NEWNO_FYEAR == 1 ? "checked" : ""}} value='1' >
  </div>
</div>
		
      <div class="row">
        <div class="col-lg-2 pl"><p>is Separator Required after number</p></div>
        
        <div class="col-lg-1 pl col-md-offset-1">
          <input type="checkbox" name="NO_SEP_RQ" id="NO_SEP_RQ" {{$objResponse->NO_SEP_RQ == 1 ? "checked" : ""}} value='1' onchange="SeparatorRequiredAfterNumber();" >
        </div>
      
          
        <div class="col-lg-1 pl"><p>Slash "/"</p></div>
        <div class="col-lg-1 pl">
          <input type="checkbox" name="NO_SEP_SLASH" id="NO_SEP_SLASH" {{$objResponse->NO_SEP_SLASH == 1 ? "checked" : ""}} value='1' onchange="AfterNumberType('NO_SEP_SLASH');"  disabled >
        </div>
        
        <div class="col-lg-1 pl"><p>OR</p></div>
        <div class="col-lg-1 pl"><p>Hyphen "-"</p></div>
        <div class="col-lg-1 pl">
          <input type="checkbox" name="NO_SEP_HYPEN" id="NO_SEP_HYPEN" {{$objResponse->NO_SEP_HYPEN == 1 ? "checked" : ""}} value='1' onchange="AfterNumberType('NO_SEP_HYPEN');"  disabled >
        </div>
      </div>
		
		<div class="row">
			<div class="col-lg-2 pl"><p>Suffix Required</p></div>
			<div class="col-lg-1 pl">
				<input type="checkbox" name="SUFFIX_RQ" id="SUFFIX_RQ" {{$objResponse->SUFFIX_RQ == 1 ? "checked" : ""}} value='1' onchange="SuffixRequired();" >
			</div>
			<div class="col-lg-1 pl">
				<input type="text" name="SUFFIX" id="SUFFIX" value="{{ $objResponse->SUFFIX }}" class="form-control"  maxlength="6" disabled >
			</div>
		
			
		</div>
             
              

              <div class="row">
                <div class="col-lg-2 pl"><p>De-Activated</p></div>
                <div class="col-lg-1 pl pr">
                <input type="checkbox"   name="DEACTIVATED"  id="deactive-checkbox_0" {{$objResponse->DEACTIVATED == 1 ? "checked" : ""}}
                 value='{{$objResponse->DEACTIVATED == 1 ? 1 : 0}}' tabindex="2"  >
                </div>
                
                <div class="col-lg-2 pl"><p>Date of De-Activated</p></div>
                <div class="col-lg-2 pl">
                  <input type="date" name="DODEACTIVATED" class="form-control" id="DODEACTIVATED" {{$objResponse->DEACTIVATED == 1 ? "" : "disabled"}} value="{{isset($objResponse->DODEACTIVATED) && $objResponse->DODEACTIVATED !="" && $objResponse->DODEACTIVATED !="1900-01-01" ? $objResponse->DODEACTIVATED:''}}" tabindex="3" placeholder="dd/mm/yyyy"  />
                </div>
             </div>

          </div>
        </form>
    </div><!--purchase-order-view-->


@endsection
@section('alert')
<!-- Alert -->
<div id="alert" class="modal"  role="dialog"  data-backdrop="static" >
  <div class="modal-dialog"  >
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


<div id="vtrefpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='vtrefpopup_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Voucher Type</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="vt_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
        <tr>
            <th>Code</th>
            <th>Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td ><input type="text" id="vt_codesearch" onkeyup="searchVTCode()"></td>
          <td><input type="text" id="vt_namesearch" onkeyup="searchVTName()"></td>
        </tr>
        </tbody>
      </table>
      
      <table id="vt_tab2" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody>
        @foreach ($objVtList as $index=>$VtList)

        <tr id="vtref_{{ $VtList->VTID }}" class="clsvtref">
          <td width="50%">{{ $VtList->VTCODE }}
          <input type="hidden" id="txtvtref_{{ $VtList->VTID }}" data-desc="{{ $VtList->VTCODE }}" data-descname="{{ $VtList->VTDESCRIPTIONS }}" value="{{ $VtList-> VTID }}"/>
          </td>
          <td>{{ $VtList->VTDESCRIPTIONS }}</td>
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
$("#VTID_REF_POPUP").on("click",function(event){ 
  $("#vtrefpopup").show();
});

$("#VTID_REF_POPUP").keyup(function(event){
  if(event.keyCode==13){
    $("#vtrefpopup").show();
  }
});

$("#vtrefpopup_close").on("click",function(event){ 
  $("#vtrefpopup").hide();
});

$('.clsvtref').dblclick(function(){
  var id          =   $(this).attr('id');
  var txtval      =   $("#txt"+id+"").val();
  var texdesc     =   $("#txt"+id+"").data("desc");
  var texdescname =   $("#txt"+id+"").data("descname");

  $("#vtdes").val(texdescname);
  $("#VTID_REF_POPUP").val(texdesc);
  $("#VTID_REF").val(txtval);
 
  $("#VTID_REF_POPUP").blur(); 
  $("#EFFECTIVE_DT").focus(); 
  
  $("#vtrefpopup").hide();
  searchVTCode();
  if(texdescname=='Payment Entry Form'){    
    $("#PREFIX_TYPE").html('<option value="B">BANK</option><option value="C">CASH</option>');    
  }else if(texdescname=='Receipt Entry Form'){
    $("#PREFIX_TYPE").html('<option value="B">BANK</option><option value="C">CASH</option>');
  }else  if(texdescname=='Service Purchase Invoice (SPI)'){ 
    $("#PREFIX_TYPE").html('<option value="DEBIT">DEBIT</option><option value="CREDIT">CREDIT</option>');  
  }else if(texdescname=='Sales Invoice (SI)'){
    $("#PREFIX_TYPE").html('<option value="EXPORT">EXPORT</option><option value="DOMESTIC">DOMESTIC</option>'); 
  }else if(texdescname=='Sales Return - SR'){
    $("#PREFIX_TYPE").html('<option value="SALES">SALES</option><option value="PURCHASE">PURCHASE</option>');
  }else if(texdescname=='Credit Note (Stock & Value) - CSV'){
    $("#PREFIX_TYPE").html('<option value="DEBIT">DEBIT</option><option value="CREDIT">CREDIT</option>'); 
  }else if(texdescname=='Debit Note (Stock & Value) - DSV'){
    $("#PREFIX_TYPE").html('<option value="DEBIT">DEBIT</option><option value="CREDIT">CREDIT</option>'); 
  }else{
    $("#PREFIX_TYPE").html(''); 
  }
  
  event.preventDefault();
});

function searchVTCode() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("vt_codesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("vt_tab2");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[0];
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

function searchVTName() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("vt_namesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("vt_tab2");
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

$('#btnAdd').on('click', function() {
      var viewURL = '{{route("master",[145,"add"])}}';
      window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });

 var formDataMst = $( "#frm_mst_edit" );
     formDataMst.validate();

     $("#EFFECTIVE_DT").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_EFFECTIVE_DT").hide();
        validateSingleElemnet("EFFECTIVE_DT");
    });

    $( "#EFFECTIVE_DT" ).rules( "add", {
      required: true,
        DateValidate:true,
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field"
        }
    });


    $("#MANUAL_MAXLENGTH").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_MANUAL_MAXLENGTH").hide();
        validateSingleElemnet("MANUAL_MAXLENGTH");
    });

    $( "#MANUAL_MAXLENGTH" ).rules( "add", {
        required: true,
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field"
        }
    });

    /*
    $("#PREFIX").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_PREFIX").hide();
        validateSingleElemnet("PREFIX");
    });

    $( "#PREFIX" ).rules( "add", {
        required: true,
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field"
        }
    });
    */

    $("#NO_MAX").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_NO_MAX").hide();
        validateSingleElemnet("NO_MAX");
    });

    $( "#NO_MAX" ).rules( "add", {
        required: true,
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field"
        }
    });
    
    $("#NO_START").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_NO_START").hide();
        validateSingleElemnet("NO_START");
    });

    $( "#NO_START" ).rules( "add", {
        required: true,
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field"
        }
    });
    
    $("#SUFFIX").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_SUFFIX").hide();
        validateSingleElemnet("SUFFIX");
    });

    $( "#SUFFIX" ).rules( "add", {
        required: true,
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field"
        }
    });

    $("#DODEACTIVATED").blur(function(){
      $(this).val($.trim( $(this).val() ));
      $("#ERROR_DODEACTIVATED").hide();
      validateSingleElemnet("DODEACTIVATED");
    });

    $( "#DODEACTIVATED" ).rules( "add", {
        required: true,
        DateValidate:true,
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field"
        }
    });

    //validae single element
    function validateSingleElemnet(element_id){
      var validator =$("#frm_mst_edit" ).validate();
          validator.element( "#"+element_id+"" );
    }

    //validate
    $( "#btnSave" ).click(function() {

        if(formDataMst.valid()){

              var DOC_TYPE          =   $.trim($("#DOC_TYPE").val());
              var MANUAL_MAXLENGTH  =   $.trim($("#MANUAL_MAXLENGTH").val());
              var SYS_GEN_LEN = 0;

              var PREFIX_LEN = 0;
              if( $("#PREFIX_RQ").prop("checked")==true ){
                PREFIX_LEN =  $.trim($("#PREFIX").val()).length;
              }

              var PRE_SEP_RQ_LEN = 0;
              if( $("#PRE_SEP_RQ").prop("checked")==true ){
                PRE_SEP_RQ_LEN =  1;
              }

              var NO_SEP_RQ_LEN = 0;
              if( $("#NO_SEP_RQ").prop("checked")==true ){
                NO_SEP_RQ_LEN =  1;
              }

              var NO_MAX_LEN = 0;
              if($("#SYSTEM_GRSR").prop("checked")==true){
                NO_MAX_LEN = $.trim($("#NO_MAX").val());
              }
              
              var SUFFIX_LEN = 0;
              if( $("#SUFFIX_RQ").prop("checked")==true ){
                SUFFIX_LEN =  $.trim($("#SUFFIX").val()).length;
              }

              TOTAL_SYSGEN_LEN = parseInt(PREFIX_LEN) + parseInt(PRE_SEP_RQ_LEN) + parseInt(NO_SEP_RQ_LEN)+ parseInt(NO_MAX_LEN)+parseInt(SUFFIX_LEN);
              

              if(DOC_TYPE ===""){
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text("Please select Document Type.");
                $("#alert").modal('show');
                $("#OkBtn1").focus();
                return false;

              }else if( DOC_TYPE=="master" &&  $("#MANUAL_SR").prop("checked")==true && parseInt(MANUAL_MAXLENGTH)>10){
               
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text("In Manual Series, Maximum Alpha Numeric can not be greater than 10 for Master document type.");
                $("#alert").modal('show');
                $("#OkBtn1").focus();
                return false;
              
              }else if( DOC_TYPE=="master" &&  $("#SYSTEM_GRSR").prop("checked")==true && parseInt(TOTAL_SYSGEN_LEN)>10){
               
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text("In System generated, Total length of System Generated No can not be greater than 10 for Master document type.");
                $("#alert").modal('show');
                $("#OkBtn1").focus();
                return false;
               
              }
              else if($("#SYSTEM_GRSR").prop("checked") == true && $("#PREFIX_RQ").prop("checked") == true && $("#PREFIX").val() ===""){
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text("In System generated, Prefix Is Required.");
                $("#alert").modal('show');
                $("#OkBtn1").focus();
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

    
    //validate and approve
    $("#btnApprove").click(function() {
      var DOC_TYPE          =   $.trim($("#DOC_TYPE").val());
              var MANUAL_MAXLENGTH  =   $.trim($("#MANUAL_MAXLENGTH").val());
              var SYS_GEN_LEN = 0;

              var PREFIX_LEN = 0;
              if( $("#PREFIX_RQ").prop("checked")==true ){
                PREFIX_LEN =  $.trim($("#PREFIX").val()).length;
              }

              var PRE_SEP_RQ_LEN = 0;
              if( $("#PRE_SEP_RQ").prop("checked")==true ){
                PRE_SEP_RQ_LEN =  1;
              }

              var NO_SEP_RQ_LEN = 0;
              if( $("#NO_SEP_RQ").prop("checked")==true ){
                NO_SEP_RQ_LEN =  1;
              }

              var NO_MAX_LEN = 0;
              if($("#SYSTEM_GRSR").prop("checked")==true){
                NO_MAX_LEN = $.trim($("#NO_MAX").val());
              }
              
              var SUFFIX_LEN = 0;
              if( $("#SUFFIX_RQ").prop("checked")==true ){
                SUFFIX_LEN =  $.trim($("#SUFFIX").val()).length;
              }

              TOTAL_SYSGEN_LEN = parseInt(PREFIX_LEN) + parseInt(PRE_SEP_RQ_LEN) + parseInt(NO_SEP_RQ_LEN)+ parseInt(NO_MAX_LEN)+parseInt(SUFFIX_LEN);
              

              if(DOC_TYPE ===""){
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text("Please select Document Type.");
                $("#alert").modal('show');
                $("#OkBtn1").focus();
                return false;

              }else if( DOC_TYPE=="master" &&  $("#MANUAL_SR").prop("checked")==true && parseInt(MANUAL_MAXLENGTH)>10){
               
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text("In Manual Series, Maximum Alpha Numeric can not be greater than 10 for Master document type.");
                $("#alert").modal('show');
                $("#OkBtn1").focus();
                return false;
              
              }else if( DOC_TYPE=="master" &&  $("#SYSTEM_GRSR").prop("checked")==true && parseInt(TOTAL_SYSGEN_LEN)>10){
               
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text("In System generated, Total length of System Generated No can not be greater than 10 for Master document type.");
                $("#alert").modal('show');
                $("#OkBtn1").focus();
                return false;
               
              }

        if(formDataMst.valid()){
            //set function nane of yes and no btn 
            $("#alert").modal('show');
            $("#YesBtn").show();
            $("#NoBtn").show();
            $("#OkBtn").hide();
            $("#OkBtn1").hide();
            $("#AlertMessage").text('Do you want to save to record.');
            $("#YesBtn").data("funcname","fnApproveData");  //set dynamic fucntion name of approval
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

        var getDataForm = $("#frm_mst_edit");
        var formData = getDataForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("mastermodify",[145,"update"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    if(data.errors.EFFECTIVE_DT){
                        showError('ERROR_EFFECTIVE_DT',data.errors.EFFECTIVE_DT);
                    }
                   if(data.exist=='norecord') {

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
                    $("#frm_mst_edit").trigger("reset");

                    $("#alert").modal('show');
                    $("#OkBtn").focus();

                }
                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });

    };// fnSaveData


    // save and approve 
    window.fnApproveData = function (){
        
        //validate and save data
        event.preventDefault();
        $("#OkBtn1").hide();

        var getDataForm = $("#frm_mst_edit");
        var formData = getDataForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("mastermodify",[145,"singleapprove"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    if(data.errors.EFFECTIVE_DT){
                        showError('ERROR_EFFECTIVE_DT',data.errors.EFFECTIVE_DT);
                    }
                   if(data.exist=='norecord') {

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
                    $("#frm_mst_edit").trigger("reset");

                    $("#alert").modal('show');
                    $("#OkBtn").focus();

                }
                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });

    };// fnApproveData

    //no button
    $("#NoBtn").click(function(){

      $("#alert").modal('hide');
      var custFnName = $("#NoBtn").data("funcname");
          window[custFnName]();

    }); //no button

   
    $("#OkBtn").click(function(){

        $("#alert").modal('hide');

        $("#YesBtn").show();
        $("#NoBtn").show();
        $("#OkBtn").hide();
        $("#OkBtn1").hide();

        $(".text-danger").hide();
        window.location.href = '{{route("master",[145,"index"]) }}';

    }); ///ok button

    $("#OkBtn1").click(function(){

        $("#alert").modal('hide');

        $("#YesBtn").show();
        $("#NoBtn").show();
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

   
    $("#OkBtn").click(function(){
      
        $("#alert").modal('hide');

    });////ok button


   window.fnUndoYes = function (){
      
      //reload form
      window.location.reload();

   }//fnUndoYes


   window.fnUndoNo = function (){

      $("#VTID_REF").focus();

   }//fnUndoNo


    //
    function showError(pId,pVal){

      $("#"+pId+"").text(pVal);
      $("#"+pId+"").show();

    }  

    function highlighFocusBtn(pclass){
       $(".activeYes").hide();
       $(".activeNo").hide();
       
       $("."+pclass+"").show();
    }

</script>
<script type="text/javascript">
$(function () {
	
	$('input[type=checkbox][name=DEACTIVATED]').change(function() {
		if ($(this).prop("checked")) {
		  $(this).val('1');
		  $('#DODEACTIVATED').removeAttr('disabled');
		}
		else {
		  $(this).val('0');
		  $('#DODEACTIVATED').prop('disabled', true);
		  $('#DODEACTIVATED').val('');
		  
		}
	});

});

$(function() { 

  if($("#SYSTEM_GRSR").prop("checked") == true && $("#PRE_SEP_RQ").prop("checked") == true){
    $("#PRE_SEP_SLASH").attr('disabled', false);
    $("#PRE_SEP_HYPEN").attr('disabled', false);
    $("#PREFIX").attr('readonly', false);
  }
  else{
    $("#PRE_SEP_SLASH").attr('disabled', true);
    $("#PRE_SEP_HYPEN").attr('disabled', true);
    $("#PREFIX").attr('readonly', true);
  }

  if($("#SYSTEM_GRSR").prop("checked") == true && $("#NO_SEP_RQ").prop("checked") == true){
    $("#NO_SEP_SLASH").attr('disabled', false);
    $("#NO_SEP_HYPEN").attr('disabled', false);
  }
  else{
    $("#NO_SEP_SLASH").attr('disabled', true);
    $("#NO_SEP_HYPEN").attr('disabled', true);
  }

  if($("#SYSTEM_GRSR").prop("checked") == true && $("#SUFFIX_RQ").prop("checked") == true){
    $("#SUFFIX").attr('disabled', false);
  }
  else{
    $("#SUFFIX").attr('disabled', true);
  }

  if($("#MANUAL_SR").prop("checked") == true){
    $("#MANUAL_MAXLENGTH").attr('disabled', false);
    $("#PREFIX_RQ").attr('disabled', true);
    $("#PREFIX_RQ").prop("checked", false);
    $("#PREFIX").val('');
    $("#PRE_SEP_RQ").attr('disabled', true);
    $("#PRE_SEP_RQ").prop("checked", false);
    $("#PRE_SEP_SLASH").attr('disabled', true);
    $("#PRE_SEP_SLASH").prop("checked", false);
    $("#PRE_SEP_HYPEN").attr('disabled', true);
    $("#PRE_SEP_HYPEN").prop("checked", false);
    $("#NO_MAX").attr('disabled', true);
    $("#NO_MAX").val('');
    $("#NO_START").attr('disabled', true);
    $("#NO_START").val('');
    $("#NEWNO_FYEAR").attr('disabled', true);
    $("#NEWNO_FYEAR").prop("checked", false);
    $("#NO_SEP_RQ").attr('disabled', true);
    $("#NO_SEP_RQ").prop("checked", false);
    $("#NO_SEP_SLASH").attr('disabled', true);
    $("#NO_SEP_SLASH").prop("checked", false);
    $("#NO_SEP_HYPEN").attr('disabled', true);
    $("#NO_SEP_HYPEN").prop("checked", false);
   
    $("#SUFFIX_RQ").attr('disabled', true);
    $("#SUFFIX_RQ").prop("checked", false);
    $("#NO_SEP_SLASH").attr('disabled', true);
    $("#NO_SEP_SLASH").prop("checked", false);
    $("#NO_SEP_HYPEN").attr('disabled', true);
    $("#NO_SEP_HYPEN").prop("checked", false);
  }
  else{
    $("#MANUAL_MAXLENGTH").attr('disabled', true);
    $("#MANUAL_MAXLENGTH").val('');
  }

});



function SeriesType(type){

if(type =="MANUAL_SR"){
  if($("#MANUAL_SR").prop("checked") == true){
    $("#SYSTEM_GRSR").prop("checked", false);
    $("#MANUAL_MAXLENGTH").val('');
    $("#MANUAL_MAXLENGTH").attr('disabled', false);
    AutoSeriesEnableDisable(true);
  }
  else{
    $("#SYSTEM_GRSR").prop("checked", true);
    $("#MANUAL_MAXLENGTH").val('');
    $("#MANUAL_MAXLENGTH").attr('disabled', true);
    AutoSeriesEnableDisable(false);
  }
}
else if(type =="SYSTEM_GRSR"){
  if($("#SYSTEM_GRSR").prop("checked") == true){
    $("#MANUAL_SR").prop("checked", false);
    $("#MANUAL_MAXLENGTH").val('');
    $("#MANUAL_MAXLENGTH").attr('disabled', true);
    AutoSeriesEnableDisable(false);
  }
  else{
    $("#MANUAL_SR").prop("checked", true);
    $("#MANUAL_MAXLENGTH").val('');
    $("#MANUAL_MAXLENGTH").attr('disabled', false);
    AutoSeriesEnableDisable(true);
  }
}

}

function PrefixRequired(){
  if($("#PREFIX_RQ").prop("checked") == true){
  $("#PREFIX").attr('readonly', false);
  $("#PREFIX").val('');
  }
  else{
  $("#PREFIX").attr('readonly', true);
  $("#PREFIX").val('');
  }
}

function SeparatorRequiredAfterPrefix(){
  if($("#PRE_SEP_RQ").prop("checked") == true){
  $("#PRE_SEP_SLASH").prop("checked", true);
  $("#PRE_SEP_SLASH").attr('disabled', false);
  $("#PRE_SEP_HYPEN").attr('disabled', false);
  }
  else{
  $("#PRE_SEP_SLASH").attr('disabled', true);
  $("#PRE_SEP_HYPEN").attr('disabled', true);
  $("#PRE_SEP_SLASH").prop("checked", false);
  $("#PRE_SEP_HYPEN").prop("checked", false);
  }
}

function AfterPrefixType(type){

  if(type =="PRE_SEP_SLASH"){
    if($("#PRE_SEP_SLASH").prop("checked") == true){
      $("#PRE_SEP_HYPEN").prop("checked", false);
    }
    else{
      $("#PRE_SEP_HYPEN").prop("checked", true);
    }
  }
  if(type =="PRE_SEP_HYPEN"){
    if($("#PRE_SEP_HYPEN").prop("checked") == true){
      $("#PRE_SEP_SLASH").prop("checked", false);
    }
    else{
      $("#PRE_SEP_SLASH").prop("checked", true);
    }
  }

}

function SeparatorRequiredAfterNumber(){
  if($("#NO_SEP_RQ").prop("checked") == true){
  $("#NO_SEP_SLASH").prop("checked", true);
  $("#NO_SEP_SLASH").attr('disabled', false);
  $("#NO_SEP_HYPEN").attr('disabled', false);
  }
  else{
  $("#NO_SEP_SLASH").attr('disabled', true);
  $("#NO_SEP_HYPEN").attr('disabled', true);
  $("#NO_SEP_SLASH").prop("checked", false);
  $("#NO_SEP_HYPEN").prop("checked", false);
  }
}

function AfterNumberType(type){

  if(type =="NO_SEP_SLASH"){
    if($("#NO_SEP_SLASH").prop("checked") == true){
      $("#NO_SEP_HYPEN").prop("checked", false);
    }
    else{
      $("#NO_SEP_HYPEN").prop("checked", true);
    }
  }
  if(type =="NO_SEP_HYPEN"){
    if($("#NO_SEP_HYPEN").prop("checked") == true){
      $("#NO_SEP_SLASH").prop("checked", false);
    }
    else{
      $("#NO_SEP_SLASH").prop("checked", true);
    }
  }
}

function SuffixRequired(){
  if($("#SUFFIX_RQ").prop("checked") == true){
  $("#SUFFIX").attr('disabled', false);
  $("#SUFFIX").val('');
  }
  else{
  $("#SUFFIX").attr('disabled', true);
  $("#SUFFIX").val('');
  }
}

function AutoSeriesEnableDisable(type){
  $("#PREFIX_RQ").attr('disabled', type);
  $("#PREFIX_RQ").prop("checked", false);
  $("#PREFIX").val('');
  $("#PRE_SEP_RQ").attr('disabled', type);
  $("#PRE_SEP_RQ").prop("checked", false);
  $("#PRE_SEP_SLASH").prop("checked", false);
  $("#PRE_SEP_HYPEN").prop("checked", false);
  $("#NO_MAX").attr('disabled', type);
  $("#NO_MAX").val('');
  $("#NO_START").attr('disabled', type);
  $("#NO_START").val('');
  $("#NEWNO_FYEAR").attr('disabled', type);
  $("#NEWNO_FYEAR").prop("checked", false);
  $("#NO_SEP_RQ").attr('disabled', type);
  $("#NO_SEP_RQ").prop("checked", false);
  $("#NO_SEP_SLASH").prop("checked", false);
  $("#NO_SEP_HYPEN").prop("checked", false);
  $("#SUFFIX_RQ").attr('disabled', type);
  $("#SUFFIX_RQ").prop("checked", false);
  $("#SUFFIX").val('');
  $("#MANUAL_MAXLENGTH").val('');
  

  if($("#MANUAL_SR").prop("checked") == true){
    $("#PRE_SEP_SLASH").attr('disabled', type);
    $("#PRE_SEP_HYPEN").attr('disabled', type);
    $("#NO_SEP_SLASH").attr('disabled', type);
    $("#NO_SEP_HYPEN").attr('disabled', type);

    $("#SUFFIX").attr('disabled', type);
    $("#SUFFIX").val('');
    $("#PREFIX").attr('readonly', type);
    $("#PREFIX").val('');
  }
  
}



</script>


@endpush