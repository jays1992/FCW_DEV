@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[222,'index'])}}" class="btn singlebt">Machine</a>
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
                  <div class="col-lg-2 pl"><p>Machine No</p></div>
                  <div class="col-lg-2 pl">
                    <div class="col-lg-7 pl">
                      {{-- <input type="text" name="MACHINE_NO" id="MACHINE_NO" onkeypress="return AlphaNumaric(event,this)"  value="{{ old('MACHINE_NO') }}" class="form-control mandatory" autocomplete="off" maxlength="20" style="text-transform:uppercase"  /> --}}
                      @if(!empty($objDD))
                            @if($objDD->SYSTEM_GRSR == "1")
                                <input type="text" name="MACHINE_NO" id="MACHINE_NO" value="{{ $objDOCNO }}" class="form-control mandatory" tabindex="1" maxlength="10" autocomplete="off" readonly style="text-transform:uppercase" autofocus >
                            @endif
                            @if($objDD->MANUAL_SR == "1")
                                <input type="text" name="MACHINE_NO" id="MACHINE_NO" value="{{ old('MACHINE_NO') }}" class="form-control mandatory"  maxlength="{{$objDD->MANUAL_MAXLENGTH}}" tabindex="1" autocomplete="off" style="text-transform:uppercase" autofocus >
                            @endif
                        @endif  
                      <span class="text-danger" id="ERROR_MACHINE_NO"></span> 
                    </div>
                  </div>

                  <div class="col-lg-2 pl"><p>Machine Description</p></div>
                  <div class="col-lg-3 pl">
                    <input type="text" name="MACHINE_DESC" id="MACHINE_DESC" class="form-control mandatory" value="{{ old('MACHINE_DESC') }}" maxlength="200"  />
                    <span class="text-danger" id="ERROR_MACHINE_DESC"></span> 
                  </div>
            </div>

            <div class="row">
              <div class="col-lg-2 pl"><p>Machine Type</p></div>
              <div class="col-lg-4 pl">
                <div class="col-lg-10 pl">
                  <label class="radio-inline" style="margin-right: 30px">
                      <input   type="radio" name="MACHINE_TYPE" id="RADIO_MACHINE_TYPE_MACHINE" value="Machine" checked/>    Machine
                  </label>
                  <label class="radio-inline">
                      <input   type="radio" name="MACHINE_TYPE" id="RADIO_MACHINE_TYPE_GENSET" value="Genset" />   Genset
                  </label>
                </div> 
              </div>
            </div>

            <div id="divmachine" >
            <div class="row">
                <div class="col-lg-2 pl"><p>Asset Code</p></div>
                <div class="col-lg-2 pl">                 
                      {{-- <input type="text" name="ASSETID_REF" id="ASSETID_REF" class="form-control "  autocomplete="off" />                  --}}
                      <input type="text" name="txtLISTPOP1_popup_0" id="txtLISTPOP1_popup_0" class="form-control CLSMACH"  autocomplete="off"  readonly />
                      <input type="hidden" name="LISTPOP1ID_0" id="hdnLISTPOP1ID_0" class="form-control CLSMACH" autocomplete="off" />
                </div>
                <div class="col-lg-2 pl"><p>Asset Description</p></div>
                <div class="col-lg-3 pl">
                    <input type="text" name="ASSET_DESC" id="ASSET_DESC" class="form-control CLSMACH"  autocomplete="off" readonly />
                </div>
            </div>


            <div class="row">
                <div class="col-lg-2 pl"><p>Asset Category</p></div>
                <div class="col-lg-2 pl">                 
                      <input type="text" name="ASSETCAT_DESC" id="ASSETCAT_DESC" class="form-control CLSMACH"  autocomplete="off" readonly/>                 
                      <input type="hidden" name="ASCATID_REF" id="ASCATID_REF" class="form-control CLSMACH"  autocomplete="off" readonly/>                 
                </div>
                <div class="col-lg-2 pl"><p>Asset Type</p></div>
                <div class="col-lg-3 pl">
                    <input type="text" name="ASSET_TYPE_DESC" id="ASSET_TYPE_DESC" class="form-control CLSMACH"  autocomplete="off" readonly/>
                    <input type="hidden" name="ASTID_REF" id="ASTID_REF" class="form-control CLSMACH"  autocomplete="off" readonly/>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-2 pl"><p>Vendor</p></div>
                <div class="col-lg-4 pl">                 
                      <input type="text" name="VENDOR" id="VENDOR" class="form-control CLSMACH"  autocomplete="off" maxlength="100"/>                 
                </div>
            </div>

            <div class="row">
                <div class="col-lg-2 pl"><p>Company Name</p></div>
                <div class="col-lg-2 pl">                 
                  <input type="text" name="COMPANY_NAME" id="COMPANY_NAME" class="form-control CLSMACH"  autocomplete="off" maxlength="100" />                 
                </div>
                <div class="col-lg-2 pl"><p>Brand</p></div>
                <div class="col-lg-3 pl">
                  <input type="text" name="BRAND" id="BRAND" class="form-control CLSMACH"  autocomplete="off" maxlength="100" />
                </div>
            </div>

            <div class="row">
                <div class="col-lg-2 pl"><p>Model No</p></div>
                <div class="col-lg-2 pl">                 
                  <input type="text" name="MODEL_NO" id="MODEL_NO" class="form-control CLSMACH"  autocomplete="off" maxlength="100" />                 
                </div>
                <div class="col-lg-2 pl"><p>Serial No</p></div>
                <div class="col-lg-3 pl">
                  <input type="text" name="SERIAL_NO" id="SERIAL_NO" class="form-control CLSMACH"  autocomplete="off" maxlength="100" />
                </div>
            </div>

            <div class="row">
                <div class="col-lg-2 pl"><p>Capacity</p></div>
                <div class="col-lg-2 pl">                 
                  <input type="text" name="CAPACITY" id="CAPACITY" class="form-control CLSMACH"  autocomplete="off" maxlength="100" />                 
                </div>
                <div class="col-lg-2 pl"><p>Technical Specification 1</p></div>
                <div class="col-lg-3 pl">
                  <input type="text" name="TECH_SPECI1" id="TECH_SPECI1" class="form-control CLSMACH"  autocomplete="off" maxlength="200" />
                </div>
            </div>

            <div class="row">
                <div class="col-lg-2 pl"><p>Technical Specification 2</p></div>
                <div class="col-lg-2 pl">                 
                  <input type="text" name="TECH_SPECI2" id="TECH_SPECI2" class="form-control CLSMACH"  autocomplete="off" maxlength="200" />                 
                </div>
                <div class="col-lg-2 pl"><p>Technical Specification 3</p></div>
                <div class="col-lg-3 pl">
                  <input type="text" name="TECH_SPECI3" id="TECH_SPECI3" class="form-control CLSMACH"  autocomplete="off" maxlength="200" />
                </div>
            </div>

            <div class="row">
              <div class="col-lg-2 pl"><p>Date of Purchase</p></div>
              <div class="col-lg-2 pl">
                <input type="date" name="DOPURCHASE" class="form-control CLSMACH" id="DOPURCHASE"  placeholder="dd/mm/yyyy"  />
              </div>
              
              <div class="col-lg-2 pl"><p>Date of Installation</p></div>
              <div class="col-lg-2 pl">
                <input type="date" name="DOINSTALLATION" class="form-control CLSMACH" id="DOINSTALLATION"  placeholder="dd/mm/yyyy"  />
              </div>
              
           </div>

            <div class="row">
              <div class="col-lg-2 pl"><p>Warranty upto</p></div>
              <div class="col-lg-2 pl">
                <input type="date" name="WARRANTY_UPTO" class="form-control CLSMACH" id="WARRANTY_UPTO"  placeholder="dd/mm/yyyy"  />
              </div>
              
           </div>

          <div class="row">
            <div class="col-lg-2 pl"><p>Service Status</p></div>
            <div class="col-lg-8 pl">
              <div class="col-lg-10 pl">
                <label class="radio-inline" style="margin-right: 30px;">
                    <input   type="radio" name="SERVICE_STATUS" id="RADIO_SERVICE_STATUS_MACHINE" value="Warranty" />    Warranty
                </label>
                <label class="radio-inline" style="margin-right: 30px">
                    <input   type="radio" name="SERVICE_STATUS" id="RADIO_SERVICE_STATUS_AMC" value="AMC" />   AMC
                </label>
                <label class="radio-inline" style="margin-right: 30px">
                    <input   type="radio" name="SERVICE_STATUS" id="RADIO_SERVICE_STATUS_INTERNAL" value="Internal" />   Internal
                </label>
                <label class="radio-inline">
                    <input   type="radio" name="SERVICE_STATUS" id="RADIO_SERVICE_STATUS_OUT" value="Out-of-Warranty" checked/>   Out-of-Warranty / AMC
                </label>
              </div> 
            </div>           
          </div>

          <div class="row">
            <div class="col-lg-2 pl"><p>Maintenance Instructions 1</p></div>
            <div class="col-lg-6 pl">                 
                  <input type="text" name="INSTRUCTIONS1" id="INSTRUCTIONS1" class="form-control  CLSMACH"  autocomplete="off" maxlength="200"/>                 
            </div>
          </div>

          <div class="row">
            <div class="col-lg-2 pl"><p>Maintenance Instructions 2</p></div>
            <div class="col-lg-6 pl">                 
                  <input type="text" name="INSTRUCTIONS2" id="INSTRUCTIONS2" class="form-control  CLSMACH"  autocomplete="off" maxlength="200"/>                 
            </div>
          </div>

          <div class="row">
            <div class="col-lg-2 pl"><p>Remarks</p></div>
            <div class="col-lg-6 pl">                 
                  <input type="text" name="REMARKS" id="REMARKS" class="form-control  CLSMACH"  autocomplete="off" maxlength="200"/>                 
            </div>
          </div>

        </div>
        <div id="divgenset" style="display: none;">
          <div class="row">
            <div class="col-lg-8">
              <p style="width:600px; background-color:grey;margin-left:0px">GENSET</p>
            </div>
          </div>  

          <div class="row">
              <div class="col-lg-1 pl"><p>Fuel Type</p></div>
              <div class="col-lg-2 pl">                 
                <select name="FUELID_REF" id="FUELID_REF" class="form-control ">
                  <option value="" selected="selected">--Please select--</option>
                  @foreach ($ObjFuelType as $index1=>$row1)
                    <option value="{{ $row1->FUELID }}">{{ $row1->FUEL_CODE }} - {{ $row1->FUEL_DESC }} </option>
                  @endforeach                  
                </select>                 
              </div>
              <div class="col-lg-2 pl"><p>Standard Consumption (Per Hour)</p></div>
              <div class="col-lg-2 pl">
                <input type="text" name="CONSUMPTION" id="CONSUMPTION" class="form-control"  autocomplete="off" maxlength="200" />
              </div>
              <div class="col-lg-1 pl"><p>UoM</p></div>
              <div class="col-lg-1 pl">                 
                <select name="UOMID_REF" id="UOMID_REF" class="form-control ">
                  <option value="" selected="selected">--Please select--</option>                  
                  @foreach ($ObjMainUOM as $index2=>$row2)
                  <option value="{{ $row2->UOMID }}">{{ $row2->UOMCODE }} - {{ $row2->DESCRIPTIONS }} </option>
                @endforeach   
                </select>                 
              </div>
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
<!-- POPUP2-->
<div id="LISTPOP1popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='LISTPOP1_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Asset Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="LISTPOP1Table" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
          <tr id="none-select" class="searchalldata"  hidden>            
            <td > <input type="text" name="fieldid" id="hdn_LISTPOP1id"/>
              <input type="text" name="fieldid2" id="hdn_LISTPOP1id2"/>
              <input type="text" name="fieldid3" id="hdn_LISTPOP1id3"/>
            </td>
          </tr>
          <tr>
            <th class="ROW1" style="width: 10%" align="center">Select</th> 
            <th class="ROW2" style="width: 40%">Code</th>
            <th  class="ROW3"style="width: 40%">Description</th>
          </tr>
    </thead>
    <tbody>
    <tr>
      <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
      <td class="ROW2"  style="width: 40%">
        <input type="text" autocomplete="off"  class="form-control" id="LISTPOP1codesearch" onkeyup="LISTPOP1CodeFunction()">
      </td>
      <td class="ROW3"  style="width: 40%">
        <input type="text" autocomplete="off"  class="form-control" id="LISTPOP1namesearch" onkeyup="LISTPOP1NameFunction()">
      </td>
    </tr>
    </tbody>
    </table>
      <table id="LISTPOP1Table2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">

        </thead>
        <tbody id="tbody_LISTPOP1">     
        
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- POPUP2 END-->

@endsection
<!-- btnSave -->

@push('bottom-scripts')
<script>


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

  
  $('#btnAdd').on('click', function() {
      var viewURL = '{{route("master",[222,"add"])}}';
      window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });

 var formResponseMst = $( "#frm_mst_add" );
     formResponseMst.validate();

    

    $("#MACHINE_NO").blur(function(){
      $(this).val($.trim( $(this).val() ));
      $("#ERROR_MACHINE_NO").hide();
      validateSingleElemnet("MACHINE_NO");
         
    });

    $( "#MACHINE_NO" ).rules( "add", {
        required: true,
        nowhitespace: true,
       // StringNumberRegex: true, //from custom.js
        messages: {
            required: "Required field.",
        }
    });


    // $("#MACHINE_DESC").blur(function(){
    //     $(this).val($.trim( $(this).val() ));
    //     $("#ERROR_NAME").hide();
    //     validateSingleElemnet("MACHINE_DESC");
    // });

    // $( "#MACHINE_DESC" ).rules( "add", {
    //     required: true,
    //     normalizer: function(value) {
    //         return $.trim(value);
    //     },
    //     messages: {
    //         required: "Required field."
    //     }
    // });

    //validae single element
    function validateSingleElemnet(element_id){
      var validator =$("#frm_mst_add" ).validate();
         if(validator.element( "#"+element_id+"" )){
            //check duplicate code
          if(element_id=="MACHINE_NO" || element_id=="MACHINE_NO" ) {
            checkDuplicateCode();

          }

         }
    }

    // //check duplicate exist code
    function checkDuplicateCode(){
        
        //validate and save data
        var getDataForm = $("#MACHINE_NO");
        var formData = getDataForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("master",[222,"codeduplicate"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
                if(data.exists) {
                    $(".text-danger").hide();
                    showError('ERROR_MACHINE_NO',data.msg);
                    $("#MACHINE_NO").focus();
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
            var MACHINE_NO           =   $.trim($("#MACHINE_NO").val());
            var MACHINE_DESC           =   $.trim($("#MACHINE_DESC").val());
            var DOPURCHASE           =   $.trim($("#DOPURCHASE").val());
            var FUELID_REF           =   $.trim($("#FUELID_REF").val());
            var CONSUMPTION           =   $.trim($("#CONSUMPTION").val());
            var UOMID_REF           =   $.trim($("#UOMID_REF").val());
         
            if(MACHINE_NO ===""){
                $("#MACHINE_NO").focus();        
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").show();
                $("#OkBtn1").hide();
                $("#AlertMessage").text('Please enter value in Machine No.');
                $("#alert").modal('show');
                $("#OkBtn").focus();
                return false;
            }
            else if(MACHINE_DESC ===""){
               $("#MACHINE_DESC").focus();        
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").show();
                $("#OkBtn1").hide();
                $("#AlertMessage").text('Please enter value in Machine Description.');
                $("#alert").modal('show');
                $("#OkBtn").focus();
                return false;
            } 
            

            
            if ($('#RADIO_MACHINE_TYPE_MACHINE').is(":checked") == true){
                    if(DOPURCHASE ===""){
                      $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn").show();
                      $("#OkBtn1").hide();
                      $("#AlertMessage").text('Please select Date of Purchase.');
                      $("#alert").modal('show');
                      $("#OkBtn").focus();
                      return false;
                  } 
            }

            if ($('#RADIO_MACHINE_TYPE_GENSET').is(":checked") == true){
                  if(FUELID_REF ===""){

                      $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn").show();
                      $("#OkBtn1").hide();
                      $("#AlertMessage").text('Please select Fuel Type.');
                      $("#alert").modal('show');
                      $("#OkBtn").focus();
                      return false;

                  }else if(CONSUMPTION ==="") {

                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn").show();
                    $("#OkBtn1").hide();
                    $("#AlertMessage").text('Please enter value in Standard Consumption (Per Hour).');
                    $("#alert").modal('show');
                    $("#OkBtn").focus();
                    return false;

                  }else if(UOMID_REF ==="") {

                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn").show();
                    $("#OkBtn1").hide();
                    $("#AlertMessage").text('Please select UoM of Genset.');
                    $("#alert").modal('show');
                    $("#OkBtn").focus();
                    return false;

                  }
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
        var MACHINE_NO          =   $.trim($("#MACHINE_NO").val());
          if(MACHINE_NO ===""){
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").hide();  
            $("#OkBtn").show();              
            $("#AlertMessage").text('Please enter Machine No.');
            $("#alert").modal('show');
            $("#OkBtn").focus();
            return false;
          }
    

        var getDataForm = $("#frm_mst_add");
        var formData = getDataForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("master",[222,"save"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    if(data.errors.MACHINE_NO){
                       // showError('ERROR_MACHINE_NO',data.errors.MACHINE_NO);
                        $("#YesBtn").hide();
                        $("#NoBtn").hide();
                        $("#OkBtn1").hide();
                        $("#OkBtn").show();
                        $("#AlertMessage").text("Machine No is "+data.errors.MACHINE_NO);
                        $("#alert").modal('show');
                        $("#OkBtn").focus();
                    }
                    if(data.errors.MACHINE_DESC){
                        //showError('ERROR_NAME',data.errors.MACHINE_DESC);
                        $("#YesBtn").hide();
                        $("#NoBtn").hide();
                        $("#OkBtn1").hide();
                        $("#OkBtn").show();
                        $("#AlertMessage").text("Machine Description is "+data.errors.MACHINE_DESC);
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

                  //  window.location.href='{{ route("master",[222,"index"])}}';
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
        $("#MACHINE_NO").focus();
        
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
    window.location.href = "{{route('master',[222,'index'])}}";

    }); 


    
    $("#OkBtn").click(function(){
      $("#alert").modal('hide');

    });////ok button


   window.fnUndoYes = function (){
      
      //reload form
      window.location.href = "{{route('master',[222,'add'])}}";

   }//fnUndoYes


   window.fnUndoNo = function (){
      $("#MACHINE_NO").focus();
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



//------------------------
  //LISTPOP1 Dropdown
  let sqtid = "#LISTPOP1Table2";
      let sqtid2 = "#LISTPOP1Table";
      let salesquotationheaders = document.querySelectorAll(sqtid2 + " th");

      // Sort the table element when clicking on the table headers
      salesquotationheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(sqtid, ".clssqid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function LISTPOP1CodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("LISTPOP1codesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("LISTPOP1Table2");
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

  function LISTPOP1NameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("LISTPOP1namesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("LISTPOP1Table2");
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

  $(document).on('focus','[id*="txtLISTPOP1_popup"]',function(event){

        
          var id = $(this).attr('id');
          var id2 = $(this).parent().parent().find('[id*="LISTPOP1ID"]').attr('id');      
          var id3 = $('#ASSET_DESC').attr('id');      

          $('#hdn_LISTPOP1id').val(id);
          $('#hdn_LISTPOP1id2').val(id2);
          $('#hdn_LISTPOP1id3').val(id3);
        
          $("#LISTPOP1popup").show();
          //$("#tbody_LISTPOP1").html('');
          $("#tbody_LISTPOP1").html('Loading...');
          $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          })
          $.ajax({
              url:'{{route("master",[222,"getasset"])}}',
              type:'POST',
              success:function(data) {
                $("#tbody_LISTPOP1").html(data);
                BindLISTPOP1Events();
              },
              error:function(data){
                console.log("Error: Something went wrong.");
                $("#tbody_LISTPOP1").html('');
              },
          });

      });

      $("#LISTPOP1_closePopup").click(function(event){
        $("#LISTPOP1popup").hide();
      });

      function BindLISTPOP1Events()
      {
          $(".clsLISTPOP1id").click(function(){
              var fieldid = $(this).attr('id');
              var txtval =    $("#txt"+fieldid+"").val();
              var texdesc =   $("#txt"+fieldid+"").data("desc");
              var texdescdate =   $("#txt"+fieldid+"").data("descdate");

              
              var txtASSETCAT_ID =  $("#txt"+fieldid+"").data("asset_cat_id");
              var txtASSETCAT_CODE =  $("#txt"+fieldid+"").data("asset_cat_code");
              var txtASSETCAT_DESC =  $("#txt"+fieldid+"").data("asset_cat_desc");
              
             

              var txtASTID_REF =  $("#txt"+fieldid+"").data("asset_type_id");
              var txtASSET_TYPE_CODE =  $("#txt"+fieldid+"").data("asset_type_code");
              var txtASSET_TYPE_DESC =  $("#txt"+fieldid+"").data("asset_type_desc");

              $('#'+txtid).val('');
              $('#'+txt_id2).val('');
              $('#'+txt_id3).val('');

              $('#ASSETCAT_DESC').val('');
              $('#ASCATID_REF').val('');
              
              $('#ASSET_TYPE_DESC').val('');
              $('#ASTID_REF').val('');
              
              //set value
              var txtid= $('#hdn_LISTPOP1id').val();
              var txt_id2= $('#hdn_LISTPOP1id2').val();
              var txt_id3= $('#hdn_LISTPOP1id3').val();

              $('#'+txtid).val(texdesc);
              $('#'+txt_id2).val(txtval);
              $('#'+txt_id3).val(texdescdate);

              $('#ASSETCAT_DESC').val(txtASSETCAT_CODE+ '-' + txtASSETCAT_DESC);
              $('#ASCATID_REF').val(txtASSETCAT_ID);

              
              $('#ASSET_TYPE_DESC').val(txtASSET_TYPE_CODE+ '-' + txtASSET_TYPE_DESC);
              $('#ASTID_REF').val(txtASTID_REF);



          
              $("#LISTPOP1popup").hide();
              
              $("#LISTPOP1codesearch").val(''); 
              $("#LISTPOP1namesearch").val(''); 
              LISTPOP1CodeFunction();
              $(this).prop("checked",false);
              event.preventDefault();
          });
      }
//------------------------

$(document).on('change',"[id*='RADIO_MACHINE_TYPE_GENSET']",function()
{
  clearMachineData()
  clearGensetData();
   if ($(this).is(":checked") == true){
        $("#divmachine").hide();
        $("#divgenset").show();

    }else{
      $("#divmachine").show();
      $("#divgenset").hide();
    }
  event.preventDefault();
});

$(document).on('change',"[id*='RADIO_MACHINE_TYPE_MACHINE']",function()
{
  clearMachineData()
  clearGensetData();
   if ($(this).is(":checked") == true){
        $("#divmachine").show();
        $("#divgenset").hide();
    }else{
      $("#divmachine").hide();
      $("#divgenset").show();
    }
  event.preventDefault();
});

function clearMachineData(){
  $(".CLSMACH").val('');
  $("#RADIO_SERVICE_STATUS_OUT").prop('checked',true);
}


function clearGensetData(){
    $("#FUELID_REF").prop('selectedIndex',0);
    $("#CONSUMPTION").val('');
    $("#UOMID_REF").prop('selectedIndex',0);
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