
@extends('layouts.app')
@section('content')
<form id="frm_mst_data" onsubmit="return validateForm()"  method="POST" class="needs-validation"  >    

    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[166,'index'])}}" class="btn singlebt">Withholding (TDS) Tax Master</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
                        <button class="btn topnavbt" id="btnSaveSE" ><i class="fa fa-floppy-o"></i> Save</button>
                        <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt" id="btnUndo"  ><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-thumbs-o-up"></i> Approved</button>
                        <button class="btn topnavbt" id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                        <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                </div>

                <!-- <div class="col-lg-2">
                    <form>
                        <div class="form-group">
                            <input type="text" name="" class="form-control" placeholder="Search">
                        </div>
                    </form>
                </div> -->
                <!--col-2-->
            </div><!--row-->
    </div><!--topnav-->	
    <!-- multiple table-responsive table-wrapper-scroll-y my-custom-scrollbar -->
    <div class="container-fluid purchase-order-view">
    <div class=" table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:480px;" >
        
            @csrf
            <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                <thead id="thead1"  style="position: sticky;top: 0">
                    <tr >
                        <th>Code<input class="form-control" type="hidden" name="Row_Count" id ="Row_Count"> </th>
                        <th width="50%">Description</th>
                        <th width="51%">Section</th>
                        <th width="16%">Assessee Type</th>
                        <th width="16%">Base Type</th>
                        <th width="16%">Applicable From</th>
                        <th width="16%">TDS Rate(%)</th>
                        <th width="16%">Tds Exemption Limit</th>
                        <th width="16%">Surcharge Rate %</th>
                        <th width="16%">Surcharge Exemption Limit</th>
                        <th width="16%">Cess Rate %</th>
                        <th width="16%">Cess Exemption Limit</th>
                        <th width="16%">Special Cess Rate(%)</th>
                        <th width="16%">Special Cess Exemption Limit</th>
                        <th width="16%">TDS GL</th>
                        <th width="16%">Surcharge GL</th>
                        <th width="16%">Cess GL</th>
                        <th width="16%">Special Cess GL</th>
                        <th width="16%">Return Type</th>
                        <th width="16%">De-Activated</th>
                        <th width="16%">Date of De-Activated</th>
                        <th width="3%">Action</th>
                    </tr>
                </thead>
                <tbody id="udfforsebody" >
                    <tr  class="participantRow">
                        
                        <td><input  class="form-control" type="text" onkeypress="return AlphaNumaric(event,this)" name="CODE_0" id ="txtcode_0" maxlength="21" autocomplete="off" style="text-transform:uppercase; width:91px;" autofocus ></td>
                        <td><input  class="form-control" type="text" name="CODE_DESC_0" id ="code_desc_0" maxlength="100" autocomplete="off" style="width:91px;"  ></td>
                        <td>
                           
        
                        <input type="text" name="SECTIONID_POPUP_0" id="SECTIONID_POPUP_0" onClick="get_section($(this).attr('id'))" class="form-control mandatory" style="width:91px" readonly tabindex="1" />
                       
                      
                            
                        </td>
                        <td hidden> <input type="text" name="SECTIONID_REF_0" id="sectionid_ref_0" /></td>
                        
                        <td>

         
                           
                           <select class="form-control " name="ASSESSEEID_REF_0" id="assesseeid_ref_0" style="width:100px;" >
                           <option value="" selected >Select</option>
                           @foreach ($objNatureOfAsseesseeList as $index=>$AsseecceeList)
                              
                               <option value="{{ $AsseecceeList->NOAID }}"  >{{ $AsseecceeList->NOA_NAME }}</option>

                               @endforeach
                           </select>
                           
                       </td>
                       <td>
                           
                           <select class="form-control " name="BASE_TYPE_0" id="base_type_0" style="width:100px;" >
                               <option value="" selected >Select</option>
                               <option value="Net">Net</option>
                               <option value="Gross">Gross</option>
                        
                           </select>

                                
                       </td>
                       
                       <td style="text-align:center;" ><input type="date" name="APPLICABLE_FRDT_0" class="form-control APPLICABLE_FRDT" placeholder="dd/mm/yyyy" id="applicable_frdt_0" ></td>
                       <td><input  class="form-control three-digits" type="text" name="TDS_RATE_0" id ="tds_rate_0" maxlength="3" autocomplete="off" style="width:85px;"  ></td>
                       <td><input  class="form-control three digits" type="text" name="TDS_EXEMP_LIMIT_0" id ="tds_exemp_limit_0" maxlength="8" autocomplete="off"  ></td>
                       <td><input  class="form-control three digits" type="text" name="SURCHARGE_RAGE_0" id ="surcharge_rage_0" maxlength="3" autocomplete="off" style="width:85px;"  ></td>
                       <td><input  class="form-control three digits" type="text" name="SURCHARGE_EXEMP_LIMIT_0" id ="surcharge_exemp_limit_0" maxlength="8" autocomplete="off"  ></td>
                       <td><input  class="form-control three digits" type="text" name="CESS_RATE_0" id ="cess_rate_0" maxlength="3" autocomplete="off" style="width:85px;" ></td>
                       <td><input  class="form-control three digits" type="text" name="CESS_EXEMP_LIMIT_0" id ="cess_exemp_limit_0" maxlength="8" autocomplete="off"  ></td>
                       <td><input  class="form-control three digits" type="text" name="SP_CESS_RATE_0" id ="sp_cess_rate_0" maxlength="3" autocomplete="off" style="width:85px;"  ></td>
                       <td><input  class="form-control three digits" type="text" name="SP_CESS_EXEMP_LIMIT_0" id ="sp_cess_exemp_limit_0" maxlength="8" autocomplete="off"  ></td>
                       <td>
                           
                           <select class="form-control " name="TDS_GLID_REF_0" id="tds_glid_ref_0" style="width:100px;" >
                               <option value="" selected >Select</option>
                               @foreach ($objGenralLedgerList as $index=>$GenralLedgerList)
                              
                              <option value="{{ $GenralLedgerList->GLID }}"  >{{ $GenralLedgerList->GLNAME }}</option>

                              @endforeach
                           </select>
                           
                       </td>
                       <td>
                           
                           <select class="form-control " name="SURCHARGE_GLID_REF_0" id="surcharge_glid_ref_0" style="width:100px;" >
                               <option value="" selected >Select</option>
                               @foreach ($objGenralLedgerList as $index=>$GenralLedgerList)
                              
                              <option value="{{ $GenralLedgerList->GLID }}"  >{{ $GenralLedgerList->GLNAME }}</option>

                              @endforeach
                           </select>
                           
                       </td>
                       <td>
                           
                           <select class="form-control " name="CESS_GLID_REF_0" id="cess_glid_ref_0" style="width:100px;" >
                               <option value="" selected >Select</option>
                               @foreach ($objGenralLedgerList as $index=>$GenralLedgerList)
                              
                              <option value="{{ $GenralLedgerList->GLID }}"  >{{ $GenralLedgerList->GLNAME }}</option>

                              @endforeach
                           </select>
                           
                       </td>
                       <td>
                           
                           <select class="form-control " name="SP_CESS_GLID_REF_0" id="sp_cess_glid_ref_0"  style="width:100px;">
                               <option value="" selected >Select</option>
                               @foreach ($objGenralLedgerList as $index=>$GenralLedgerList)
                              
                              <option value="{{ $GenralLedgerList->GLID }}"  >{{ $GenralLedgerList->GLNAME }}</option>

                              @endforeach
                           </select>
                           
                       </td>
                       <td>
                           
                           <select class="form-control " name="RETURN_TYPE_0" id="return_type_0" >
                               <option value="" selected >Select</option>
                               <option value="26">26</option>
                               <option value="27">27</option>
           
                           </select>
                           
                       </td>
                        <td style="text-align:center;" ><input type="checkbox" name="DEACTIVATED_0"  id="deactive-checkbox_0" disabled></td>
                        <td style="text-align:center;" ><input type="date" name="DODEACTIVATED_0" class="form-control" placeholder="dd/mm/yyyy" id="decativateddate_0" disabled></td>                    
                        <td align="center" ><button class="btn add" title="add" data-toggle="tooltip">
                        <i class="fa fa-plus"></i></button>
                       <button class="btn remove" title="Delete" data-toggle="tooltip" disabled>
                        <i class="fa fa-trash" ></i></button>
                        </td>
                    </tr>
                    <tr>
                    </tr>       
                </tbody>
            </table>
              
        </div>
    </div><!--purchase-order-view-->

<!-- </div> -->
</form>
@endsection
@section('alert')
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


<!--Sales Account Popup-->
<div id="sectionid_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='ctryidref_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Section Master</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="country_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th>Section Code</th>
            <th>Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td ><input type="text" id="sectionmaster_codesearch" onkeyup="searchSectionMasteCode()"></td>
          <td><input type="text" id="sectionmaster_namesearch" onkeyup="searchSectionMasteName()"></td>
        </tr>
        </tbody>
      </table>


      <table id="sectionmaster_tab" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody id="country_body">
        @foreach ($objSectionMasterList as $index=>$SectionList)
        <tr id="ctryidref_{{ $SectionList->SECTIONID }}" class="sectionmaster_tab">
          <td width="50%">{{ $SectionList->SECTION_CODE }}
          <input type="hidden" id="txtctryidref_{{ $SectionList->SECTIONID }}" data-desc="{{ $SectionList->SECTION_CODE }}" data-descname="{{ $SectionList->SECTION_NAME }}" value="{{ $SectionList-> SECTIONID }}"/>
          </td>
          <td>{{ $SectionList->SECTION_NAME }}</td>
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


<!-- Alert -->
@endsection


@push('bottom-css')
<style>
#custom_dropdown, #udffordatamst_filter {
    display: inline-table;
    margin-left: 15px;
}
.dataTables_wrapper .row:nth-child(1) .col-sm-6:nth-child(2){text-align:right;}
#filtercolumn{color: #555;
    background-color: #fff;
    background-image: none;
    border: 1px solid #ccc;
    }

/*.table-bordered.itemlist tr th {
    padding: 5px 5px;
    font-size: 13px;
    border: 1px solid#0f69cc !important;
    color: #0f69cc;
    background: #eff7fb;
    font-weight: 400;
    text-align: center;
    position: sticky;
    top: 0;
}*/

</style>
@endpush
@push('bottom-scripts')
<script>

     
$(document).ready(function(e) {
    
    $("[id*='tds_rate']").ForceNumericOnly();
    $('#example2').on('blur','[id*="tds_rate"]',function(){
            if(intRegex.test($(this).val())){
             $(this).val($(this).val()+'.0000')
            }
            event.preventDefault();
        });

    $("[id*='tds_exemp_limit']").ForceNumericOnly();
    $('#example2').on('blur','[id*="tds_exemp_limit"]',function(){
            if(intRegex.test($(this).val())){
             $(this).val($(this).val()+'.00')
            }
            event.preventDefault();
        });
        $("[id*='surcharge_rage']").ForceNumericOnly();
    $('#example2').on('blur','[id*="surcharge_rage"]',function(){
            if(intRegex.test($(this).val())){
             $(this).val($(this).val()+'.0000')
            }
            event.preventDefault();
        });
        $("[id*='surcharge_exemp_limit']").ForceNumericOnly();
    $('#example2').on('blur','[id*="surcharge_exemp_limit"]',function(){
            if(intRegex.test($(this).val())){
             $(this).val($(this).val()+'.00')
            }
            event.preventDefault();
        });
        $("[id*='cess_rate']").ForceNumericOnly();
    $('#example2').on('blur','[id*="cess_rate"]',function(){
            if(intRegex.test($(this).val())){
             $(this).val($(this).val()+'.0000')
            }
            event.preventDefault();
        });
        $("[id*='cess_exemp_limit']").ForceNumericOnly();
    $('#example2').on('blur','[id*="cess_exemp_limit"]',function(){
            if(intRegex.test($(this).val())){
             $(this).val($(this).val()+'.00')
            }
            event.preventDefault();
        });
        $("[id*='sp_cess_rate']").ForceNumericOnly();
    $('#example2').on('blur','[id*="sp_cess_rate"]',function(){
            if(intRegex.test($(this).val())){
             $(this).val($(this).val()+'.0000')
            }
            event.preventDefault();
        });
        $("[id*='sp_cess_exemp_limit']").ForceNumericOnly();
    $('#example2').on('blur','[id*="sp_cess_exemp_limit"]',function(){
            if(intRegex.test($(this).val())){
             $(this).val($(this).val()+'.00')
            }
            event.preventDefault();
        });
    $('#Row_Count').val("1");
    $(function() { $('[id*="txtcode"]').focus(); });
    $('#btnAdd').on('click', function() {
      var viewURL = '{{route("master",[166,"add"])}}';
                  window.location.href=viewURL;
    });
    $('#btnExit').on('click', function() {
      var viewURL = '{{route('home')}}';
                  window.location.href=viewURL;
    });
    //to check the label duplicacy
    // $('[id*="txtlabel"]').blur(function(){

     $('#example2').on('blur','[id*="txtcode"]',function(){

    var code_value= $(this).val(); 
      var CODE   =   $.trim($(this).val());
      if(CODE ===""){
                $("#FocusId").val('CODE');
               
                $("#ProceedBtn").focus();
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text('Please enter value in Code.');
                $("#alert").modal('show');
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk1');
                // return false;
            } 
        else{ 
        var mstdataForm = $("#frm_mst_data");
        var formData = mstdataForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("master",[166,"checkCode"])}}',
            type:'POST',
            dataType: "json",
            data:{code_value:code_value},
            success:function(data) {
               if(data.exists) {
                    $(".text-danger").hide();
                    if(data.exists) {                   
                        console.log("cancel MSG="+data.msg);
                                      $("#YesBtn").hide();
                                      $("#NoBtn").hide();
                                      $("#OkBtn1").show();
                                      $("#AlertMessage").text(data.msg);
                                      $(".text-danger").hide();
                                      $("#frm_mst_data").trigger("reset");
                                      $("#alert").modal('show');
                                      $("#OkBtn1").focus();
                                      highlighFocusBtn('activeOk1');
                    }                 
                }                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });
    }
});
        
    
//delete row
    $("#example2").on('click', '.remove', function() {
    var rowCount = $('#Row_Count').val();
	//rowCount = parseInt(rowCount)-1;
    //$('#Row_Count').val(rowCount);
    if (rowCount > 1) {
    $(this).closest('tbody').remove();     
    } 
     
    if (rowCount <= 1) { 
    $(document).find('.remove').prop('disabled', false);  
    }
    event.preventDefault();
    });

         //add row
        // $(".add").click(function() { 
        $("#example2").on('click', '.add', function() {
        var $tr = $(this).closest('table');
        var allTrs = $tr.find('tbody').last();
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
        $tr.closest('table').append($clone);         
        var rowCount = $('#Row_Count').val();
		rowCount = parseInt(rowCount)+1;
        $('#Row_Count').val(rowCount);
        $clone.find('.remove').removeAttr('disabled'); 
        $clone.find('[id*="txtdesc"]').val('');
        $clone.find('[id*="chkmdtry"]').prop('checked', false);

        event.preventDefault();
    });

    $("#btnUndo").on("click", function() {
        $("#AlertMessage").text("Do you want to erase entered information in this record?");
        $("#alert").modal('show');
        $("#YesBtn").data("funcname","fnUndoYes");
        $("#YesBtn").show();
        $("#NoBtn").data("funcname","fnUndoNo");
        $("#NoBtn").show();        
        $("#OkBtn").hide();
        $("#NoBtn").focus();
        event.preventDefault();
    });

    

    window.fnUndoYes = function (){
      
      //reload form
      window.location.href = "{{route('master',[166,'add'])}}";

   }//fnUndoYes


   window.fnUndoNo = function (){

      $("#txtlabel").focus();

   }//fnUndoNo

   $("#example2").on('change', '[id*="drpvalue"]', function() {
    // $('[id*="drpvalue"]').on("change", function( event ) {
    if ($(this).find('option:selected').val() != "Combobox") {
        $(this).parent().parent().find('[id*="txtdesc"]').prop('disabled', true);
        $(this).parent().parent().find('[id*="txtdesc"]').val('');
        event.preventDefault();
    }
    else
    {
        $(this).parent().parent().find('[id*="txtdesc"]').prop('disabled', false);
        event.preventDefault();
    }
});


// growTextarea function: use for testing that the the javascript
// is also copied when row is cloned.  to confirm, 
// type several lines into Location, add a row, & repeat

    function growTextarea (i,elem) {
    var elem = $(elem);
    var resizeTextarea = function( elem ) {
        var scrollLeft = window.pageXOffset || (document.documentElement || document.body.parentNode || document.body).scrollLeft;
        var scrollTop  = window.pageYOffset || (document.documentElement || document.body.parentNode || document.body).scrollTop;  
        elem.css('height', 'auto').css('height', elem.prop('scrollHeight') );
        window.scrollTo(scrollLeft, scrollTop);
    };

    elem.on('input', function() {
        resizeTextarea( $(this) );
    });

    resizeTextarea( $(elem) );
    }

    $('.growTextarea').each(growTextarea);
});
</script>

@endpush

@push('bottom-scripts')
<script>

$(document).ready(function() {

  $("#btnSaveSE").on("submit", function( event ) {
    if ($("#frm_mst_data").valid()) {
        // Do something
        alert( "Handler for .submit() called." );
        event.preventDefault();
    }
});


    $('#frm_mst_data1').bootstrapValidator({
       
        fields: {
            txtlabel: {
                validators: {
                    notEmpty: {
                        message: 'The Label is required'
                    }
                }
            },            
        },
        submitHandler: function(validator, form, submitButton) {
            alert( "Handler for .submit() called." );
             event.preventDefault();
             $("#frm_mst_data").submit();
        }
    });
});
function validateForm(){
 
 $("#FocusId").val('');
 var CODE          =   $.trim($("[id*=txtcode]").val());
 var CODE_DESC      =   $.trim($("[id*=code_desc]").val());
 var SECTIONID_REF    =   $.trim($("[id*=sectionid_ref]").val());
 var ASSESSEEID_REF    =   $.trim($("[id*=assesseeid_ref]").val());
 var BASE_TYPE    =   $.trim($("[id*=base_type]").val());
 var APPLICABLE_FRDT    =   $.trim($("[id*=applicable_frdt]").val());
 var TDS_RATE    =   $.trim($("[id*=tds_rate]").val());
 var TDS_GLID_REF    =   $.trim($("[id*=tds_glid_ref]").val());
 var SURCHARGE_GLID_REF    =   $.trim($("[id*=surcharge_glid_ref]").val());
 var CESS_GLID_REF    =   $.trim($("[id*=cess_glid_ref]").val());
 var SP_CESS_GLID_REF    =   $.trim($("[id*=sp_cess_glid_ref]").val());
 var RETURN_TYPE    =   $.trim($("[id*=return_type]").val());
 var DEACTIVATED    =   $("[id*=deactive-checkbox]").is(":checked");
 var DODEACTIVATED  =   $("[id*=decativateddate]").val();
 

 var TDS_EXEMPT_LIMIT  =   $("[id*=tds_exemp_limit]").val();
 var SURCHARGE_RATE  =   $("[id*=surcharge_rage]").val();
 var SURCHARGE_EXEMP_LIMIT  =   $("[id*=surcharge_exemp_limit]").val();
 var CESS_RATE  =   $("[id*=cess_rate]").val();
 

 if(CODE ===""){
     $("#FocusId").val($("[id*=txtcode]"));
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please enter value in Code.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if(CODE_DESC ===""){
     $("#FocusId").val($("[id*=code_desc]"));
      $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please enter value in Description.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 } 
 else if(SECTIONID_REF ===""){
     $("#FocusId").val($("[id*=sectionid_ref]"));
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please select Section.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 } 
 else if(ASSESSEEID_REF ===""){
     $("#FocusId").val($("[id*=assesseeid_ref]"));
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please select  Assessee Type.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 } 
 else if(BASE_TYPE ===""){
     $("#FocusId").val($("[id*=base_type]"));
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please select Base Type.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 } 
 else if(APPLICABLE_FRDT ===""){
     $("#FocusId").val($("[id*=applicable_frdt]"));
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please select Applicable Date.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 } 
 else if(TDS_RATE ===""){
     $("#FocusId").val($("[id*=tds_rate]"));
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Enter TDS Rate.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 } 
 else if(TDS_GLID_REF ===""){
     $("#FocusId").val($("[id*=tds_glid_ref]"));
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please select TDS GL.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 } 
 else if(SURCHARGE_GLID_REF ==="" && SURCHARGE_RATE!=''){
     $("#FocusId").val($("[id*=surcharge_glid_ref]"));
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Select Surcharge GL.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 } 
 else if(CESS_GLID_REF ==="" && SURCHARGE_EXEMP_LIMIT!=''){
     $("#FocusId").val($("[id*=cess_glid_ref]"));
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Select Cess GL.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 } 
 else if(SP_CESS_GLID_REF ==="" && CESS_RATE!=''){
     $("#FocusId").val($("[id*=sp_cess_glid_ref]"));
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Select Special Cess GL.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 } 
 else if(RETURN_TYPE ===""){
     $("#FocusId").val($("[id*=return_type]"));
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Select Return Type.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 } 



 
 else{
    event.preventDefault();
    var allblank1 = [];
    var allblank2 = [];
    var allblank3 = [];
    var allblank4 = [];
    var allblank5 = [];
    var allblank6 = [];
    var allblank7 = [];
    var allblank8 = [];
    var allblank9 = [];
    var allblank10 = [];
    var allblank11 = [];
    var allblank12 = [];

    var SURCHARGE_RATE  =   $("[id*=surcharge_rage]").val();
    var SURCHARGE_EXEMP_LIMIT  =   $("[id*=surcharge_exemp_limit]").val();
    var CESS_RATE  =   $("[id*=cess_rate]").val();
        // $('#udfforsebody').find('.form-control').each(function () {
            $('#example2').find('.participantRow').each(function(){
            
            if($.trim($(this).find("[id*=txtcode]").val())!=""){
        
                //$(this).val('');
                allblank3.push('true');

            }
            else{
                        allblank3.push('false');
                    } 
    
      
            if($.trim($(this).find("[id*=code_desc]").val())!=""){
      
                //$(this).val('');
                allblank2.push('true');

            }
            else{
                        allblank2.push('false');
                    } 
 
 
            if($.trim($(this).find("[id*=sectionid_ref]").val())!=""){
                //$(this).val('');
                allblank1.push('true');

            }
            else{
                        allblank1.push('false');
                    } 
 
            if($.trim($(this).find("[id*=assesseeid_ref]").val())!=""){
                allblank4.push('true');

            }
            else{
                        allblank4.push('false');
                    } 
   
    
            if($.trim($(this).find("[id*=base_type]").val())!=""){
                allblank5.push('true');

            }
            else{
                        allblank5.push('false');
                    } 

     
            if($.trim($(this).find("[id*=applicable_frdt]").val())!=""){
                allblank6.push('true');

            }
            else{
                        allblank6.push('false');
                    } 
    

            if($.trim($(this).find("[id*=tds_rate]").val())!=""){
                allblank7.push('true');

            }
            else{
                        allblank7.push('false');
                    } 











          
     
            if($.trim($(this).find("[id*=tds_glid_ref]").val())!=""){
                allblank8.push('true');

            }
            else{
                        allblank8.push('false');
                    } 

       

            if(SURCHARGE_RATE!=''){

            if($.trim($(this).find("[id*=surcharge_glid_ref]").val())!=""){
                allblank9.push('true');

            }
            else{
                        allblank9.push('false');
                    } 

            }
       
            if(SURCHARGE_EXEMP_LIMIT!=''){
            if($.trim($(this).find("[id*=cess_glid_ref]").val())!=""){
                allblank10.push('true');

            }
            else{
                        allblank10.push('false');
                    } 
            }
    
            if(CESS_RATE!=''){
            if($.trim($(this).find("[id*=sp_cess_glid_ref]").val())!=""){
                allblank11.push('true');

            }
            else{
                        allblank11.push('false');
                    }
            }
     
  
            if($.trim($(this).find("[id*=return_type]").val())!=""){
                allblank12.push('true');

            }
            else{
                        allblank12.push('false');
                    } 
  
 });}

        if(jQuery.inArray("false", allblank3) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Please enter value in Code.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
            }
    
            else if(jQuery.inArray("false", allblank2) !== -1){
            $("#alert").modal('show');
            $("#AlertMessage").text('Please enter value in Description.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk');
            }
            else if(jQuery.inArray("false", allblank1) !== -1){
            $("#alert").modal('show');
            $("#AlertMessage").text('Please select Section.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk');
            }
            else if(jQuery.inArray("false", allblank4) !== -1){
            $("#alert").modal('show');
            $("#AlertMessage").text('Please select  Assessee Type.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk');
            }
            else if(jQuery.inArray("false", allblank5) !== -1){
            $("#alert").modal('show');
            $("#AlertMessage").text('Please select Base Type.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk');
            }
            else if(jQuery.inArray("false", allblank6) !== -1){
            $("#alert").modal('show');
            $("#AlertMessage").text('Please select Applicable Date.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk');
            }
            else if(jQuery.inArray("false", allblank7) !== -1){
            $("#alert").modal('show');
            $("#AlertMessage").text('Please select TDS Rate.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk');
            }
            else if(jQuery.inArray("false", allblank8) !== -1){
            $("#alert").modal('show');
            $("#AlertMessage").text('Please select TDS GL.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk');
            }
            else if(jQuery.inArray("false", allblank9) !== -1){
            $("#alert").modal('show');
            $("#AlertMessage").text('Please select Surcharge GL.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk');
            }
            else if(jQuery.inArray("false", allblank10) !== -1){
            $("#alert").modal('show');
            $("#AlertMessage").text('Please select Cess GL.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk');
            }
            else if(jQuery.inArray("false", allblank11) !== -1){
            $("#alert").modal('show');
            $("#AlertMessage").text('Please select Special Cess GL.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk');
            }
            else if(jQuery.inArray("false", allblank12) !== -1){
            $("#alert").modal('show');
            $("#AlertMessage").text('Please select Return Type.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk');
            }
            else{

                $("#alert").modal('show');
                $("#AlertMessage").text('Do you want to save to record.');
                $("#YesBtn").data("funcname","fnSaveData");  //set dynamic fucntion name
                $("#YesBtn").focus();

                $("#OkBtn").hide();
                highlighFocusBtn('activeYes');

            }
        

 }



$("#YesBtn").click(function(){

$("#alert").modal('hide');
var customFnName = $("#YesBtn").data("funcname");
    window[customFnName]();

}); //yes button

window.fnSaveData = function (){

//validate and save data
event.preventDefault();

     var mstdataForm = $("#frm_mst_data");
    var formData = mstdataForm.serialize();
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$.ajax({
    url:'{{ route("master",[166,"save"])}}',
    type:'POST',
    data:formData,
    success:function(data) {
       
        if(data.errors) {
            $(".text-danger").hide();

            if(data.errors.LABEL){
                showError('ERROR_LABEL',data.errors.LABEL);
                        $("#YesBtn").hide();
                        $("#NoBtn").hide();
                        $("#OkBtn1").show();
                        $("#AlertMessage").text('Please enter correct value in Label.');
                        $("#alert").modal('show');
                        $("#OkBtn1").focus();
            }
           if(data.reqdata=='norecord') {

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
            // $("#frm_mst_reqdata").trigger("reset");

            $("#alert").modal('show');
            $("#OkBtn").focus();
            // window.location.href="{{ route('master',[166,'index']) }}";
        }
        else if(data.cancel) {                   
            console.log("cancel MSG="+data.msg);
            
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").show();

            $("#AlertMessage").text(data.msg);

            $(".text-danger").hide();
            // $("#frm_mst_reqdata").trigger("reset");

            $("#alert").modal('show');
            $("#OkBtn1").focus();
            // window.location.href="{{ route('master',[166,'index']) }}";
        }
        else 
        {                   
            console.log("succes MSG="+data.msg);
            
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text(data.msg);
            $(".text-danger").hide();
            $("#alert").modal('show');
            $("#OkBtn1").focus();
        }
        
    },
    error:function(data){
        console.log("Error: Something went wrong.");
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Error: Something went wrong.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        highlighFocusBtn('activeOk1');
    },
});

}

//no button
$("#NoBtn").click(function(){
    $("#alert").modal('hide');
    $("#LABEL").focus();
});

//ok button
$("#OkBtn").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn").hide();
    $(".text-danger").hide();
    window.location.href = '{{route("master",[166,"index"]) }}';
});

$("#OkBtn1").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn").hide();
    $("#OkBtn1").hide();
    $("#"+$(this).data('focusname')).focus();
    $("[id*=txtlabel]").focus();
    $(".text-danger").hide();
});

//
function showError(pId,pVal){
    $("#"+pId+"").text(pVal);
    $("#"+pId+"").show();
}
function getFocus(){
    var FocusId=$("#FocusId").val();
    $("#"+FocusId).focus();
    $("#closePopup").click();
}
function highlighFocusBtn(pclass){
       $(".activeYes").hide();
       $(".activeNo").hide();
       
       $("."+pclass+"").show();
    }

    $(function(){
    var dtToday = new Date();
    
    var month = dtToday.getMonth() + 1;
    var day = dtToday.getDate();
    var year = dtToday.getFullYear();
    if(month < 10)
        month = '0' + month.toString();
    if(day < 10)
        day = '0' + day.toString();
    
    var minDate= year + '-' + month + '-' + day;
    
    $('.APPLICABLE_FRDT').attr('min', minDate);
});


// Sales Account popup function
function get_section(id){
   
    var result = id.split('_');
    var id_number=result[2];
    var popup_id='#'+id;
    $(".sectionmaster_tab").val(popup_id);    

  $("#sectionid_popup").show();




$("#SECTIONID_POPUP").keyup(function(event){
  if(event.keyCode==13){
    $("#sectionid_popup").show();
  }
});

$("#ctryidref_close").on("click",function(event){ 
  $("#sectionid_popup").hide();
});

$('.sectionmaster_tab').dblclick(function(){
    var value= $(".sectionmaster_tab").val()
    var result = value.split('_');
    var id_numbers=result[2];
    var sectionid_ref="#sectionid_ref_"+id_numbers; 



    var id          =   $(this).attr('id');

  var txtval      =   $("#txt"+id+"").val();
  var texdesc     =   $("#txt"+id+"").data("desc");
  var texdescname =   $("#txt"+id+"").data("descname");

 
  //$("#SALES_AC_DESC").val(texdescname);
  $(value).val(texdesc);
  $(sectionid_ref).val(txtval);
  $("#sectionid_popup").hide();
 
});
}

function searchSectionMasteCode() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("sectionmaster_codesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("sectionmaster_tab");
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

function searchSectionMasteName() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("sectionmaster_namesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("sectionmaster_tab");
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