@extends('layouts.app')
@section('content')
    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                  <a href="{{route('master',[161,'index'])}}" class="btn singlebt">Machine wise - Mould Info</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <button href="#" id="btnSelectedRows" class="btn topnavbt" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
                        <button id="btnSave"   class="btn topnavbt" tabindex="4"><i class="fa fa-save"></i> Save</button>
                        <button class="btn topnavbt" id="btnView"  disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt"  id="btnUndo" ><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled" ><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove"{{($objRights->APPROVAL1 || $objRights->APPROVAL2 || $objRights->APPROVAL3 || $objRights->APPROVAL4 || $objRights->APPROVAL5) == 1 ? '' : 'disabled'}}><i class="fa fa-lock"></i> Approved</button>
                        <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-link" ></i> Attachment</button>
                        <button class="btn topnavbt" id="btnExit"><i class="fa fa-power-off"></i> Exit</button>
                </div><!--col-10-->

            </div><!--row-->
    </div><!--topnav-->	
   
    <div class="container-fluid purchase-order-view filter">     
         <form id="frm_mst_edit" method="POST"  > 
          @CSRF
          {{isset($objResponse->MACHINEID) ? method_field('PUT') : '' }}
            <div class="inner-form">
              <div class="row">
                <div class="col-lg-2 pl"><p>Machine No </p></div>
                <div class="col-lg-2 pl">
                    <input type="text" name="MACHINE_popup" id="txtmachine_popup" value="{{ isset($ObjMachine[0]->MACHINE_NO) ? $ObjMachine[0]->MACHINE_NO :''}}" class="form-control mandatory clsclear"  autocomplete="off" readonly/>
                    <input type="hidden" name="MACHINEID_REF" id="MACHINEID_REF" value="{{ isset($ObjMachine[0]->MACHINEID) ? $ObjMachine[0]->MACHINEID :''}}" class="form-control clsclear" autocomplete="off" />
                    <input type="hidden" name="MWITEMID" id="MWITEMID"  value="{{ isset($ObjTBLMWIH[0]->MWITEMID) ? $ObjTBLMWIH[0]->MWITEMID :''}}" class="form-control clsclear" autocomplete="off" />
                </div>              
                <div class="col-lg-2 pl"><p>Machine Description</p></div>
                <div class="col-lg-2 pl">
                    <input type="text" name="MACHINENAME" id="MACHINENAME" value="{{ isset($ObjMachine[0]->MACHINE_DESC) ? $ObjMachine[0]->MACHINE_DESC :''}}"  class="form-control clsclear"   autocomplete="off" readonly/>
                </div>
              </div>    

              <div class="row">
                  <div class="col-lg-2 pl"><p>Mould Code</p></div>
                  <div class="col-lg-2 pl">
                    <div class="col-lg-7 pl">
                      <label> {{$objResponse->MOULD_CODE}} </label>
                      <input type="hidden" name="MOULD_CODE" id="MOULD_CODE" value="{{$objResponse->MOULD_CODE}}" />
                      <input type="hidden" name="user_approval_level" id="user_approval_level" value="{{ $user_approval_level }}"  />   
                      <span class="text-danger" id="ERROR_MOULD_CODE"></span> 
                    </div>
                  </div>

                  <div class="col-lg-2 pl"><p>Mould Description</p></div>
                  <div class="col-lg-3 pl">
                    <input type="text" name="MOULD_DESC" id="MOULD_DESC" value="{{$objResponse->MOULD_DESC}}" class="form-control mandatory"  maxlength="200" readonly/>
                  </div>
            </div>


            <div class="row">
              <div class="col-lg-2 pl"><p>Produce Item Code</p></div>
              <div class="col-lg-2 pl">                 
                    <input type="text" name="txtPRODITEMPOP_popup" id="txtPRODITEMPOP_popup"  value="{{ isset($ObjProdItem[0]->ICODE) ? $ObjProdItem[0]->ICODE :''}}" class="form-control clsclear"  autocomplete="off"  readonly />
                    <input type="hidden" name="PRODUCE_ITEMID_REF" id="hdnPRODITEMPOPID"        value="{{ isset($ObjProdItem[0]->ITEMID) ? $ObjProdItem[0]->ITEMID :''}}"  class="form-control clsclear" autocomplete="off" />
              </div>
              <div class="col-lg-2 pl"><p>Description</p></div>
              <div class="col-lg-3 pl">
                  <input type="text" name="ITEM_DESC" id="ITEM_DESC" class="form-control clsclear" value="{{ isset($ObjProdItem[0]->NAME) ? $ObjProdItem[0]->NAME :''}}"  autocomplete="off" readonly />
              </div>
          </div>


          <div class="row">
            <div class="col-lg-2 pl"><p>Expected Produce Qty</p></div>
            <div class="col-lg-2 pl">
                <input type="text" name="EXP_PRODUCE_QTY" id="EXP_PRODUCE_QTY" value="{{$objResponse->EXP_PRODUCE_QTY}}" class="form-control three-digits clsclear" maxlength="13"  autocomplete="off" />
            </div>

            <div class="col-lg-2 pl"><p>UOM</p></div>
            <div class="col-lg-2 pl">
                <input type="text" name="txtMainUOM_popup_0" id="txtMainUOM_popup_0"  value="{{ isset($ObjEXPUOM[0]->UOMCODE) ? $ObjEXPUOM[0]->UOMCODE :''}}-{{ isset($ObjEXPUOM[0]->DESCRIPTIONS) ? $ObjEXPUOM[0]->DESCRIPTIONS :''}}"  class="form-control mandatory clsclear"  autocomplete="off" readonly/>
                <input type="hidden" name="EXP_PRODUCE_UOMID_REF" id="MainUOM_REF_0" value="{{ isset($ObjEXPUOM[0]->UOMID) ? $ObjEXPUOM[0]->UOMID :''}}"  class="form-control clsclear" autocomplete="off" />
            </div>              
           
          </div>    

          <div class="row">
            <div class="col-lg-2 pl"><p>How many Qty produce in one stroke</p></div>
            <div class="col-lg-2 pl">
                <input type="text" name="PRODUCE_QTY" id="PRODUCE_QTY" value="{{$objResponse->PRODUCE_QTY}}" class="form-control three-digits clsclear" maxlength="13"  autocomplete="off" />
            </div>

            <div class="col-lg-2 pl"><p>UOM</p></div>
            <div class="col-lg-2 pl">
                <input type="text" name="txtMainUOM_popup_1" id="txtMainUOM_popup_1"  value="{{ isset($ObjPRODUOM[0]->UOMCODE) ? $ObjPRODUOM[0]->UOMCODE :''}}-{{ isset($ObjPRODUOM[0]->DESCRIPTIONS) ? $ObjPRODUOM[0]->DESCRIPTIONS :''}}"  class="form-control mandatory clsclear"  autocomplete="off" readonly/>
                <input type="hidden" name="PRODUCE_UOMID_REF" id="MainUOM_REF_1"        value="{{ isset($ObjPRODUOM[0]->UOMID) ? $ObjPRODUOM[0]->UOMID :''}}" class="form-control clsclear" autocomplete="off" />
            </div>      
          </div>

              
            {{-- deactive row --}}
            <div class="row">
              <div class="col-lg-2 pl"><p>De-Activated</p></div>
              <div class="col-lg-2 pl pr">
              <input type="checkbox"   name="DEACTIVATED"  id="deactive-checkbox_0" {{$objResponse->DEACTIVATED == 1 ? "checked" : ""}}
               value='{{$objResponse->DEACTIVATED == 1 ? 1 : 0}}' tabindex="2"  >
              </div>
              
              <div class="col-lg-2 pl"><p>Date of De-Activated</p></div>
              <div class="col-lg-2 pl">
                <input type="date" name="DODEACTIVATED" class="form-control" id="DODEACTIVATED" {{$objResponse->DEACTIVATED == 1 ? "" : "disabled"}} value="{{isset($objResponse->DODEACTIVATED) && $objResponse->DODEACTIVATED !="" && $objResponse->DODEACTIVATED !="1900-01-01" ? $objResponse->DODEACTIVATED:''}}" tabindex="3" placeholder="dd/mm/yyyy"  />
              </div>
            </div>
           {{-- deactive row end --}}

            <br/>
             
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
                <div id="alert-active1" class="activeOk1"></div>OK</button>  
        </div><!--btdiv-->
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
{{-- Alert end --}}
{{-- popup begin --}}
<div id="machinepopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width: 500px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='mach_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Machine Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="MachTable" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
    <tr>
      <th class="ROW1" style="width: 10%" align="center">Select</th> 
      <th class="ROW2" style="width: 40%" >Code</th>
      <th class="ROW3" style="width: 40%" >Desciption</th>
    </tr>
    </thead>
    <tbody>
      <tr>
        <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
        <td class="ROW2"  style="width: 40%">
          <input type="text" id="machinecodesearch" autocomplete="off"  class="form-control"  onkeyup="MachineCodeFunction()" />
        </td>
        <td class="ROW3"  style="width: 40%">
          <input type="text" id="machinedatesearch"  autocomplete="off"  class="form-control"   onkeyup="MachineNameFunction()" />
        </td>
      </tr>
    </tbody>
    </table>
      <table id="MachnineTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          
        </thead>
        <tbody id="tbody_machrow">       
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<!-- POPUP2-->
<div id="PRODITEMPOPpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='PRODITEMPOP_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Produce Items Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="PRODITEMPOPTable" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
          <tr id="none-select" class="searchalldata"  hidden>            
            <td > <input type="text" name="fieldid" id="hdn_PRODITEMPOPid"/>
              <input type="text" name="fieldid2" id="hdn_PRODITEMPOPid2"/>
              <input type="text" name="fieldid3" id="hdn_PRODITEMPOPid3"/>
            </td>
          </tr>
          <tr>
            <th class="ROW1" style="width: 10%" align="center">Select</th> 
            <th class="ROW2" style="width: 40%" >Code</th>
            <th class="ROW3" style="width: 40%" >Description</th>
          </tr>
    </thead>
    <tbody>
      <tr>
        <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
        <td class="ROW2"  style="width: 40%">
            <input type="text" id="PRODITEMPOPcodesearch" autocomplete="off"   class="form-control"   onkeyup="PRODITEMPOPCodeFunction()" />
        </td>
        <td class="ROW3"  style="width: 40%">
            <input type="text" id="PRODITEMPOPnamesearch" autocomplete="off"   class="form-control"   onkeyup="PRODITEMPOPNameFunction()" />
        </td>
      </tr>
    </tbody>
    </table>
      <table id="PRODITEMPOPTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">

        </thead>
        <tbody id="tbody_PRODITEMPOP">     
        
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- POPUP2 END-->
<!-- POPUP3 -->
<div id="MainUOMpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width: 500px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='MainUOM_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
      <div class="tablename"><p>Main UOM</p></div>
      <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="MainUOMTable" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
    <tr >            
      <td hidden>
         <input type="text" name="fieldid" id="hdn_MainUOMid"/>
        <input type="text" name="fieldid2" id="hdn_MainUOMid2"/>
        <input type="text" name="fieldid3" id="hdn_MainUOMid3"/>
      </td>
    </tr>  
    <tr>
      <th class="ROW1" style="width: 10%" align="center">Select</th> 
      <th class="ROW2" style="width: 40%" >Code</th>
      <th class="ROW3" style="width: 40%" >Desciption</th>
    </tr>
    </thead>
    <tbody>
      <tr>
        <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
        <td class="ROW2"  style="width: 40%">
          <input type="text" autocomplete="off"  class="form-control"  id="MainUOMcodesearch"  onkeyup="MainUOMCodeFunction()" />
        </td>
        <td class="ROW3"  style="width: 40%">
          <input type="text" autocomplete="off"  class="form-control"  id="MainUOMdatesearch"   onkeyup="MainUOMNameFunction()" />
        </td>
      </tr>
    </tbody>
    </table>
      <table id="MainUOMTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          
        </thead>
        <tbody id="tbody_MainUOM">       
        </tbody>
      </table>
    </div>
        <div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- POPUP3 END-->

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
      var viewURL = '{{route("master",[161,"add"])}}';
      window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });

 var formDataMst = $( "#frm_mst_edit" );
     formDataMst.validate();

    
    //validae single element
    function validateSingleElemnet(element_id){
      var validator =$("#frm_mst_edit" ).validate();
          validator.element( "#"+element_id+"" );
    }

    //validate
    $( "#btnSave" ).click(function() {

        $("#OkBtn").hide();
        $("#OkBtn1").hide();

        if(formDataMst.valid()){
            
            var MACHINEID_REF        =   $.trim($("#MACHINEID_REF").val());
            var MOULD_CODE           =   $.trim($("#MOULD_CODE").val());
            var MOULD_DESC           =   $.trim($("#MOULD_DESC").val());
            var PRODITEMPOPID        =   $.trim($("#hdnPRODITEMPOPID").val());
            var EXP_PRODUCE_QTY      =   $.trim($("#EXP_PRODUCE_QTY").val());
            var MainUOM_REF_0        =   $.trim($("#MainUOM_REF_0").val());
            
            var PRODUCE_QTY          =   $.trim($("#PRODUCE_QTY").val());
            var MainUOM_REF_1        =   $.trim($("#MainUOM_REF_1").val());
         
            if(MACHINEID_REF ===""){
                $("#MOULD_CODE").focus();        
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#AlertMessage").text('Please select Machine No.');
                $("#alert").modal('show');
                $("#OkBtn").show();
                $("#OkBtn").focus();
                return false;
            }
            else if(MOULD_CODE ===""){
                $("#MOULD_CODE").focus();        
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").show();
                $("#AlertMessage").text('Please enter Mould Code.');
                $("#alert").modal('show');
                $("#OkBtn").focus();
                return false;
            }
            else if(MOULD_DESC ===""){
               $("#MOULD_DESC").focus();        
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").show();
                $("#AlertMessage").text('Please enter Mould Description.');
                $("#alert").modal('show');
                $("#OkBtn").focus();
                return false;
            } 
            else if(PRODITEMPOPID ===""){
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").show();
                $("#AlertMessage").text('Please select Produce Item Code.');
                $("#alert").modal('show');
                $("#OkBtn").focus();
                return false;
            } 
            else if(EXP_PRODUCE_QTY ===""){
                $("#EXP_PRODUCE_QTY").focus();        
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").show();
                $("#AlertMessage").text('Please enter Expected Produce Qty.');
                $("#alert").modal('show');
                $("#OkBtn").focus();
                return false;
            } 
            else if(parseFloat(EXP_PRODUCE_QTY)<=0){
                $("#EXP_PRODUCE_QTY").focus();        
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").show();
                $("#AlertMessage").text('Expected Produce Qty should be greater than zero.');
                $("#alert").modal('show');
                $("#OkBtn").focus();
                return false;
            } 
            else if(MainUOM_REF_0 ===""){
               $("#MOULD_DESC").focus();        
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").show();
                $("#AlertMessage").text('Please select UOM of Expected Produce Qty.');
                $("#alert").modal('show');
                $("#OkBtn").focus();
                return false;
            } 
            else if(PRODUCE_QTY ===""){
               $("#PRODUCE_QTY").focus();        
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").show();
                $("#AlertMessage").text('Please enter value for Qty produce in one stroke.');
                $("#alert").modal('show');
                $("#OkBtn").focus();
                return false;
            } 
            else if(parseFloat(PRODUCE_QTY)<=0 ){
               $("#PRODUCE_QTY").focus();        
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").show();
                $("#AlertMessage").text('Qty produce in one stroke should be greater than zero.');
                $("#alert").modal('show');
                $("#OkBtn").focus();
                return false;
            } 
            else if(MainUOM_REF_1 ===""){
               $("#MOULD_DESC").focus();        
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").show();
                $("#AlertMessage").text('Please select UOM of Qty produce in one stroke.');
                $("#alert").modal('show');
                $("#OkBtn").focus();
                return false;
            } 
            else if( $("#deactive-checkbox_0").is(":checked") == true  &&  $("#DODEACTIVATED").val()==""  ){
         
              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#AlertMessage").text('Please select Date of De-Activated.');
              $("#alert").modal('show');
              $("#OkBtn").show();
              $("#OkBtn").focus();
              highlighFocusBtn('activeOk');
              return false;
            }
            else if( $("#deactive-checkbox_0").is(":checked") == false  &&  $("#DODEACTIVATED").val()!=""  ){
              
              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn1").hide();
              $("#AlertMessage").text('Please select the De-Activated check box.');
              $("#alert").modal('show');
              $("#OkBtn").show();
              $("#OkBtn").focus();
              highlighFocusBtn('activeOk');
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

    
    //validate and approve
    $("#btnApprove").click(function() {

        $("#OkBtn").hide();
        $("#OkBtn1").hide();

        
        if(formDataMst.valid()){
            //set function nane of yes and no btn 
            var MACHINEID_REF        =   $.trim($("#MACHINEID_REF").val());
            var MOULD_CODE           =   $.trim($("#MOULD_CODE").val());
            var MOULD_DESC           =   $.trim($("#MOULD_DESC").val());
            var PRODITEMPOPID        =   $.trim($("#hdnPRODITEMPOPID").val());
            var EXP_PRODUCE_QTY      =   $.trim($("#EXP_PRODUCE_QTY").val());
            var MainUOM_REF_0        =   $.trim($("#MainUOM_REF_0").val());
            
            var PRODUCE_QTY          =   $.trim($("#PRODUCE_QTY").val());
            var MainUOM_REF_1        =   $.trim($("#MainUOM_REF_1").val());
         
            if(MACHINEID_REF ===""){
                $("#MOULD_CODE").focus();        
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#AlertMessage").text('Please select Machine No.');
                $("#alert").modal('show');
                $("#OkBtn").show();
                $("#OkBtn").focus();
                return false;
            }
            else if(MOULD_CODE ===""){
                $("#MOULD_CODE").focus();        
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").show();
                $("#AlertMessage").text('Please enter Mould Code.');
                $("#alert").modal('show');
                $("#OkBtn").focus();
                return false;
            }
            else if(MOULD_DESC ===""){
               $("#MOULD_DESC").focus();        
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").show();
                $("#AlertMessage").text('Please enter Mould Description.');
                $("#alert").modal('show');
                $("#OkBtn").focus();
                return false;
            } 
            else if(PRODITEMPOPID ===""){
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").show();
                $("#AlertMessage").text('Please select Produce Item Code.');
                $("#alert").modal('show');
                $("#OkBtn").focus();
                return false;
            } 
            else if(EXP_PRODUCE_QTY ===""){
                $("#EXP_PRODUCE_QTY").focus();        
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").show();
                $("#AlertMessage").text('Please enter Expected Produce Qty.');
                $("#alert").modal('show');
                $("#OkBtn").focus();
                return false;
            } 
            else if(parseFloat(EXP_PRODUCE_QTY)<=0){
                $("#EXP_PRODUCE_QTY").focus();        
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").show();
                $("#AlertMessage").text('Expected Produce Qty should be greater than zero.');
                $("#alert").modal('show');
                $("#OkBtn").focus();
                return false;
            } 
            else if(MainUOM_REF_0 ===""){
               $("#MOULD_DESC").focus();        
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").show();
                $("#AlertMessage").text('Please select UOM of Expected Produce Qty.');
                $("#alert").modal('show');
                $("#OkBtn").focus();
                return false;
            } 
            else if(PRODUCE_QTY ===""){
               $("#PRODUCE_QTY").focus();        
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").show();
                $("#AlertMessage").text('Please enter value for Qty produce in one stroke.');
                $("#alert").modal('show');
                $("#OkBtn").focus();
                return false;
            } 
            else if(parseFloat(PRODUCE_QTY)<=0 ){
               $("#PRODUCE_QTY").focus();        
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").show();
                $("#AlertMessage").text('Qty produce in one stroke should be greater than zero.');
                $("#alert").modal('show');
                $("#OkBtn").focus();
                return false;
            } 
            else if(MainUOM_REF_1 ===""){
               $("#MOULD_DESC").focus();        
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").show();
                $("#AlertMessage").text('Please select UOM of Qty produce in one stroke.');
                $("#alert").modal('show');
                $("#OkBtn").focus();
                return false;
            } 
            else if( $("#deactive-checkbox_0").is(":checked") == true  &&  $("#DODEACTIVATED").val()==""  ){
         
              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#AlertMessage").text('Please select Date of De-Activated.');
              $("#alert").modal('show');
              $("#OkBtn").show();
              $("#OkBtn").focus();
              highlighFocusBtn('activeOk');
              return false;
            }
            else if( $("#deactive-checkbox_0").is(":checked") == false  &&  $("#DODEACTIVATED").val()!=""  ){
              
              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn1").hide();
              $("#AlertMessage").text('Please select the De-Activated check box.');
              $("#alert").modal('show');
              $("#OkBtn").show();
              $("#OkBtn").focus();
              highlighFocusBtn('activeOk');
              return false;
            }
            
            //--- 
            $("#alert").modal('show');
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

        $("#OkBtn").hide();
        $("#OkBtn1").hide();
    
        //validate and save data
        event.preventDefault();

        var getDataForm = $("#frm_mst_edit");
        var formData = getDataForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("mastermodify",[161,"update"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    if(data.errors.AC_SET_DESC){
                        showError('ERROR_NAME',data.errors.AC_SET_DESC);
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
                    $("#OkBtn1").show();

                    $("#AlertMessage").text(data.msg);

                    $(".text-danger").hide();
                    $("#alert").modal('show');
                    $("#OkBtn1").focus();

                }
                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });

    };// fnSaveData


    // save and approve 
    window.fnApproveData = function (){
        
        $("#OkBtn").hide();
        $("#OkBtn1").hide();

        //validate and save data
        event.preventDefault();

        var getDataForm = $("#frm_mst_edit");
        var formData = getDataForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("mastermodify",[161,"singleapprove"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

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
                    $("#OkBtn1").show();
                    $("#AlertMessage").text(data.msg);
                    $(".text-danger").hide();
                    $("#alert").modal('show');
                    $("#OkBtn1").focus();
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
        $(".text-danger").hide();

    }); ///ok button

    $("#OkBtn1").click(function(){

        $("#alert").modal('hide');
        $("#YesBtn").show();
        $("#NoBtn").show();
        $("#OkBtn").hide();
        $(".text-danger").hide();
        window.location.href = '{{route("master",[161,"index"]) }}';

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
      window.location.reload();

   }//fnUndoYes


   window.fnUndoNo = function (){

      //$("#PLCODE").focus();

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
	
	var today = new Date(); 
    var dodeactived_date = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
    $('#DODEACTIVATED').attr('min',dodeactived_date);

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

//------------------------
//Machine Starts
//------------------------
let sgltid = "#MachnineTable2";
      let sgltid2 = "#MachTable";
      let sglheaders = document.querySelectorAll(sgltid2 + " th");

      // Sort the table element when clicking on the table headers
      sglheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(sgltid, ".clsmachine", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function MachineCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("machinecodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("MachnineTable2");
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

  function MachineNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("machinedatesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("MachnineTable2");
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

  
$("#txtmachine_popup").focus(function(event){
  
    $('#tbody_machrow').html('Loading...');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url:'{{route("master",[161,"getmachines"])}}',
        type:'POST',
        success:function(data) {
            $('#tbody_machrow').html(data);
            bindMachineEvents();
        },
        error:function(data){
            console.log("Error: Something went wrong.");
            $('#tbody_machrow').html('');
        },
    });        
     $("#machinepopup").show();
     event.preventDefault();
}); 

$("#mach_closePopup").on("click",function(event){ 
    $("#machinepopup").hide();
    event.preventDefault();
});
function bindMachineEvents(){

    $('.clsmachine').click(function(){
 
        $(".clsclear").val('');

        var id = $(this).attr('id');
        var txtval =    $("#txt"+id+"").val();
        var texdesc =   $("#txt"+id+"").data("desc");
        var txtccname =   $("#txt"+id+"").data("ccname");
        var txtmwitemid =   $("#txt"+id+"").data("mwitemid");
        
        $("#txtmachine_popup").val(texdesc);
        $("#txtmachine_popup").blur();
        $("#MACHINEID_REF").val(txtval);
        $("#MACHINENAME").val(txtccname);
        $("#MWITEMID").val(txtmwitemid);
        
        $("#machinepopup").hide();
        $("#machinecodesearch").val(''); 
        $("#machinedatesearch").val(''); 
        
        MachineCodeFunction();
        event.preventDefault();
        $(this).prop("checked",false);

    });
  }
//Machine Ends
//------------------------

  //PRODITEMPOP Dropdown
  let sqtid = "#PRODITEMPOPTable2";
      let sqtid2 = "#PRODITEMPOPTable";
      let salesquotationheaders = document.querySelectorAll(sqtid2 + " th");

      // Sort the table element when clicking on the table headers
      salesquotationheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(sqtid, ".clssqid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function PRODITEMPOPCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("PRODITEMPOPcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("PRODITEMPOPTable2");
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

  function PRODITEMPOPNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("PRODITEMPOPnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("PRODITEMPOPTable2");
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

  $(document).on('focus','[id*="txtPRODITEMPOP_popup"]',function(event){

        var MACHINEID_REF = $("#MACHINEID_REF").val();
        var MWITEMID = $("#MWITEMID").val();
        if(MACHINEID_REF ===""){
            showAlert('Please select Machine No.');
            return false;
        }

          var id = $(this).attr('id');
          var id2 = $(this).parent().parent().find('[id*="PRODITEMPOPID"]').attr('id');      
          var id3 = $('#ITEM_DESC').attr('id');      

          $('#hdn_PRODITEMPOPid').val(id);
          $('#hdn_PRODITEMPOPid2').val(id2);
          $('#hdn_PRODITEMPOPid3').val(id3);
        
          $("#PRODITEMPOPpopup").show();
          //$("#tbody_PRODITEMPOP").html('');
          $("#tbody_PRODITEMPOP").html('Loading...');
          $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          })
          $.ajax({
              url:'{{route("master",[161,"getproditems"])}}',
              type:'POST',
              data:{'MACHINEID_REF':MACHINEID_REF,'MWITEMID':MWITEMID},
              success:function(data) {
                $("#tbody_PRODITEMPOP").html(data);
                BindPRODITEMPOPEvents();
              },
              error:function(data){
                console.log("Error: Something went wrong.");
                $("#tbody_PRODITEMPOP").html('');
              },
          });

      });

      $("#PRODITEMPOP_closePopup").click(function(event){
        $("#PRODITEMPOPpopup").hide();
      });

      function BindPRODITEMPOPEvents()
      {
          $(".clsPRODITEMPOP").click(function(){
            var fieldid = $(this).attr('id');
            var txtval =    $("#txt"+fieldid+"").val();
            var texdesc =   $("#txt"+fieldid+"").data("desc");
            var texdescdate =   $("#txt"+fieldid+"").data("descdate");

            //set values
            var txtid= $('#hdn_PRODITEMPOPid').val();
            var txt_id2= $('#hdn_PRODITEMPOPid2').val();
            var txt_id3= $('#hdn_PRODITEMPOPid3').val();

            $('#'+txtid).val(texdesc);
            $('#'+txt_id2).val(txtval);
            $('#'+txt_id3).val(texdescdate);


            var txtproduce_qty  =  $("#txt"+fieldid+"").data("produce_qty");
            var txtuom_id  =  $("#txt"+fieldid+"").data("uom_id");
            var txtuom_desc  =  $("#txt"+fieldid+"").data("uom_desc");

            $('#EXP_PRODUCE_QTY').val(txtproduce_qty);
            $('#MainUOM_REF_0').val(txtuom_id);
            $('#txtMainUOM_popup_0').val(txtuom_desc);

            $('#PRODUCE_QTY').val(txtproduce_qty);
            $('#MainUOM_REF_1').val(txtuom_id);
            $('#txtMainUOM_popup_1').val(txtuom_desc);

            
            $("#PRODITEMPOPpopup").hide();
            $("#PRODITEMPOPcodesearch").val(''); 
            $("#PRODITEMPOPnamesearch").val(''); 
            PRODITEMPOPCodeFunction();
            
            event.preventDefault();
            $(this).prop("checked",false);

          });
      }

//------------------------
//MainUOM Starts
//------------------------
let muomid = "#MainUOMTable2";
      let muomid2 = "#MainUOMTable";
      let muomheaders = document.querySelectorAll(muomid2 + " th");

      // Sort the table element when clicking on the table headers
      muomheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(muomid, ".clsMainUOM", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function MainUOMCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("MainUOMcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("MainUOMTable2");
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

  function MainUOMNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("MainUOMdatesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("MainUOMTable2");
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

$(document).on('focus','[id*="txtMainUOM_popup"]',function(event){  

    var id = $(this).attr('id');
    var id2 = $(this).parent().parent().find('[id*="MainUOM_REF"]').attr('id');      
   
    $('#hdn_MainUOMid').val(id);
    $('#hdn_MainUOMid2').val(id2);
        
  
    $('#tbody_MainUOM').html('Loading...');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url:'{{route("master",[161,"getMainUOM"])}}',
        type:'POST',
        success:function(data) {
            $('#tbody_MainUOM').html(data);
            bindMainUOMEvents();
        },
        error:function(data){
            console.log("Error: Something went wrong.");
            $('#tbody_MainUOM').html('');
        },
    });        
     $("#MainUOMpopup").show();
     event.preventDefault();
}); 

$("#MainUOM_closePopup").on("click",function(event){ 
    $("#MainUOMpopup").hide();
    event.preventDefault();
});
function bindMainUOMEvents(){

    $('.clsMainUOM').click(function(){
 
        var id = $(this).attr('id');
        var txtval =    $("#txt"+id+"").val();
        var texdesc =   $("#txt"+id+"").data("desc");

        //set value
        var txtid= $('#hdn_MainUOMid').val();
        var txt_id2= $('#hdn_MainUOMid2').val();
       
        $('#'+txtid).val(texdesc);
        $('#'+txt_id2).val(txtval);
       
        $("#MainUOMpopup").hide();
        $("#MainUOMcodesearch").val(''); 
        $("#MainUOMdatesearch").val(''); 
       
        MainUOMCodeFunction();
        event.preventDefault();
        $(this).prop("checked",false);

    });
  }
//MainUOM Ends
//------------------------
function showAlert(msg){
  $("#alert").modal('show');
  $("#AlertMessage").text(msg);
  $("#YesBtn").hide(); 
  $("#NoBtn").hide();  
  $("#OkBtn").show();
  $("#OkBtn").focus();
  highlighFocusBtn('activeOk');
}

$(document).on('keyup','.three-digits',function(){
    if($(this).val().indexOf('.')!=-1){         
        if($(this).val().split(".")[1].length > 3){                
          $(this).val('');
          $("#alert").modal('show');
          $("#AlertMessage").text('Enter value till three decimal only.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn").show();
          $("#OkBtn").focus();
          highlighFocusBtn('activeOk');
        }  
     }            
     return this; //for chaining
});

$(document).ready(function(e){

    $("[id*='EXP_PRODUCE_QTY']").ForceNumericOnly();
    $("[id*='PRODUCE_QTY']").ForceNumericOnly();


    $(document).on('blur',"[id*='EXP_PRODUCE_QTY']",function()
    {
        var qty2 = $.trim($(this).val());
        if(isNaN(qty2) || qty2=="" )
        {
          qty2 = 0;
        }  
        if(intRegex.test(qty2))
        {
          $(this).val((qty2 +'.000'));
        }
      
        event.preventDefault();
    });  

    $(document).on('blur',"[id*='PRODUCE_QTY']",function()
    {
        var qty2 = $.trim($(this).val());
        if(isNaN(qty2) || qty2=="" )
        {
          qty2 = 0;
        }  
        if(intRegex.test(qty2))
        {
          $(this).val((qty2 +'.000'));
        }
      
        event.preventDefault();
    });  

}); //ready

</script>
@endpush