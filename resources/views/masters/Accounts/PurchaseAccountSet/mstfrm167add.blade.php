@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[167,'index'])}}" class="btn singlebt">Purchase Account Set</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                <button class="btn topnavbt" id="btnAdd" disabled="disabled" ><i class="fa fa-plus"></i> Add</button>
                <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
                <button class="btn topnavbt" id="btnSave"  tabindex="5"  ><i class="fa fa-save"></i> Save</button>
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
                    <div class="col-lg-2 pl"><p>Account set code</p></div>
                    <div class="col-lg-2 pl">
                      <div class="col-lg-10 pl">
                          <input type="text" name="AC_SET_CODE" id="AC_SET_CODE" onkeypress="return AlphaNumaric(event,this)"  value="{{ old('AC_SET_CODE') }}" class="form-control mandatory" autocomplete="off" maxlength="20" tabindex="1" style="text-transform:uppercase" required/>
                          <span class="text-danger" id="ERROR_AC_SET_CODE"></span> 
                      </div>
                    </div>

                    <div class="col-lg-2 pl"><p>Account set Description</p></div>
                    <div class="col-lg-4 pl">
                      <input type="text" name="AC_SET_DESC" id="AC_SET_DESC" class="form-control mandatory" value="{{ old('AC_SET_DESC') }}" maxlength="50" tabindex="4"  />
                      <span class="text-danger" id="ERROR_AC_SET_DESC"></span> 
                    </div>
                </div>

              <div class="row">
                  <div class="col-lg-2 pl"><p>Purchase Account</p></div>
                  <div class="col-lg-2 pl">
                    <div class="col-lg-10 pl">
                            <input type="text" name="txtLISTPOP1_popup_0" id="txtLISTPOP1_popup_0" class="form-control mandatory"  autocomplete="off"  readonly  required/>
                            <input type="hidden" name="LISTPOP1ID_0" id="hdnLISTPOP1ID_0" class="form-control" autocomplete="off" />
                    </div>
                  </div>
                  <div class="col-lg-2 pl"><p>Description</p></div>
                  <div class="col-lg-3 pl">
                    <input  class="form-control" style="width: 100%" type="text" name="DESC2_0" id ="DESC2_0"  autocomplete="off" readonly>
                  </div>
              </div>

              <div class="row">
                <div class="col-lg-2 pl"><p>Payable Clearing</p></div>
                <div class="col-lg-2 pl">
                  <div class="col-lg-10 pl">
                    <input type="text" name="txtLISTPOP1_popup_1" id="txtLISTPOP1_popup_1" class="form-control "  autocomplete="off"  readonly  />
                    <input type="hidden" name="LISTPOP1ID_1" id="hdnLISTPOP1ID_1" class="form-control" autocomplete="off" />
                  </div>
                </div>
                <div class="col-lg-2 pl"><p>Description</p></div>
                <div class="col-lg-3 pl">
                    <input  class="form-control" style="width: 100%" type="text" name="DESC2_1" id ="DESC2_1"  autocomplete="off" readonly>
                </div>
              </div>

              <div class="row">
                <div class="col-lg-2 pl"><p>Inventory Account</p></div>
                <div class="col-lg-2 pl">
                  <div class="col-lg-10 pl">
                    <input type="text" name="txtLISTPOP1_popup_2" id="txtLISTPOP1_popup_2" class="form-control mandatory"  autocomplete="off"  readonly  required/>
                    <input type="hidden" name="LISTPOP1ID_2" id="hdnLISTPOP1ID_2" class="form-control" autocomplete="off" />
                  </div>
                </div>
                <div class="col-lg-2 pl"><p>Description</p></div>
                <div class="col-lg-3 pl">
                    <input  class="form-control" style="width: 100%" type="text" name="DESC2_2" id ="DESC2_2"  autocomplete="off" readonly>
                </div>
              </div>

              <div class="row">
                <div class="col-lg-2 pl"><p>Landed Cost Variance</p></div>
                <div class="col-lg-2 pl">
                  <div class="col-lg-10 pl">
                    <input type="text" name="txtLISTPOP1_popup_3" id="txtLISTPOP1_popup_3" class="form-control"  autocomplete="off"  readonly />
                    <input type="hidden" name="LISTPOP1ID_3" id="hdnLISTPOP1ID_3" class="form-control" autocomplete="off" />
                  </div>
                </div>
                <div class="col-lg-2 pl"><p>Description</p></div>
                <div class="col-lg-3 pl">
                    <input  class="form-control" style="width: 100%" type="text" name="DESC2_3" id ="DESC2_3"  autocomplete="off" readonly>
                </div>
              </div>

              <div class="row">
                <div class="col-lg-2 pl"><p>Transfer Clearing</p></div>
                <div class="col-lg-2 pl">
                  <div class="col-lg-10 pl">
                    <input type="text" name="txtLISTPOP1_popup_4" id="txtLISTPOP1_popup_4" class="form-control"  autocomplete="off"  readonly />
                    <input type="hidden" name="LISTPOP1ID_4" id="hdnLISTPOP1ID_4" class="form-control" autocomplete="off" />
                  </div>
                </div>
                <div class="col-lg-2 pl"><p>Description</p></div>
                <div class="col-lg-3 pl">
                    <input  class="form-control" style="width: 100%" type="text" name="DESC2_4" id ="DESC2_4"  autocomplete="off" readonly>
                </div>
              </div>

              <div class="row">
                <div class="col-lg-2 pl"><p>Stock Transfer A/C</p></div>
                <div class="col-lg-2 pl">
                  <div class="col-lg-10 pl">
                    <input type="text" name="txtLISTPOP1_popup_5" id="txtLISTPOP1_popup_5" class="form-control"  autocomplete="off"  readonly />
                    <input type="hidden" name="LISTPOP1ID_5" id="hdnLISTPOP1ID_5" class="form-control" autocomplete="off" />
                  </div>
                </div>
                <div class="col-lg-2 pl"><p>Description</p></div>
                <div class="col-lg-3 pl">
                    <input  class="form-control" style="width: 100%" type="text" name="DESC2_5" id ="DESC2_5"  autocomplete="off" readonly>
                </div>
              </div>

              <div class="row">
                <div class="col-lg-2 pl"><p>Consumption Account</p></div>
                <div class="col-lg-2 pl">
                  <div class="col-lg-10 pl">
                    <input type="text" name="txtLISTPOP1_popup_6" id="txtLISTPOP1_popup_6" class="form-control"  autocomplete="off"  readonly />
                    <input type="hidden" name="LISTPOP1ID_6" id="hdnLISTPOP1ID_6" class="form-control" autocomplete="off" />
                  </div>
                </div>
                <div class="col-lg-2 pl"><p>Description</p></div>
                <div class="col-lg-3 pl">
                    <input  class="form-control" style="width: 100%" type="text" name="DESC2_6" id ="DESC2_6"  autocomplete="off" readonly>
                </div>
              </div>

              <div class="row">
                <div class="col-lg-2 pl"><p>Rejected</p></div>
                <div class="col-lg-2 pl">
                  <div class="col-lg-10 pl">
                    <input type="text" name="txtLISTPOP1_popup_7" id="txtLISTPOP1_popup_7" class="form-control"  autocomplete="off"  readonly />
                    <input type="hidden" name="LISTPOP1ID_7" id="hdnLISTPOP1ID_7" class="form-control" autocomplete="off" />
                  </div>
                </div>
                <div class="col-lg-2 pl"><p>Description</p></div>
                <div class="col-lg-3 pl">
                    <input  class="form-control" style="width: 100%" type="text" name="DESC2_7" id ="DESC2_7"  autocomplete="off" readonly>
                </div>
              </div>

              <div class="row">
                <div class="col-lg-2 pl"><p>Shortage</p></div>
                <div class="col-lg-2 pl">
                  <div class="col-lg-10 pl">
                    <input type="text" name="txtLISTPOP1_popup_8" id="txtLISTPOP1_popup_8" class="form-control"  autocomplete="off"  readonly />
                    <input type="hidden" name="LISTPOP1ID_8" id="hdnLISTPOP1ID_8" class="form-control" autocomplete="off" />
                  </div>
                </div>
                <div class="col-lg-2 pl"><p>Description</p></div>
                <div class="col-lg-3 pl">
                    <input  class="form-control" style="width: 100%" type="text" name="DESC2_8" id ="DESC2_8"  autocomplete="off" readonly>
                </div>
              </div>
             
              <div class="row">
                <div class="col-lg-2 pl"><p>Inventory Adj.</p></div>
                <div class="col-lg-2 pl">
                  <div class="col-lg-10 pl">
                    <input type="text" name="txtLISTPOP1_popup_9" id="txtLISTPOP1_popup_9" class="form-control"  autocomplete="off"  readonly />
                    <input type="hidden" name="LISTPOP1ID_9" id="hdnLISTPOP1ID_9" class="form-control" autocomplete="off" />
                  </div>
                </div>
                <div class="col-lg-2 pl"><p>Description</p></div>
                <div class="col-lg-3 pl">
                    <input  class="form-control" style="width: 100%" type="text" name="DESC2_9" id ="DESC2_9"  autocomplete="off" readonly>
                </div>
              </div>

              <div class="row">
                <div class="col-lg-2 pl"><p>WIP Account</p></div>
                <div class="col-lg-2 pl">
                  <div class="col-lg-10 pl">
                    <input type="text" name="txtLISTPOP1_popup_10" id="txtLISTPOP1_popup_10" class="form-control"  autocomplete="off"  readonly />
                    <input type="hidden" name="LISTPOP1ID_10" id="hdnLISTPOP1ID_10" class="form-control" autocomplete="off" />
                  </div>
                </div>
                <div class="col-lg-2 pl"><p>Description</p></div>
                <div class="col-lg-3 pl">
                    <input  class="form-control" style="width: 100%" type="text" name="DESC2_10" id ="DESC2_10"  autocomplete="off" readonly>
                </div>
              </div>

              <div class="row">
                <div class="col-lg-2 pl"><p>Gain Loss A/C</p></div>
                <div class="col-lg-2 pl">
                  <div class="col-lg-10 pl">
                    <input type="text" name="txtLISTPOP1_popup_11" id="txtLISTPOP1_popup_11" class="form-control"  autocomplete="off"  readonly />
                    <input type="hidden" name="LISTPOP1ID_11" id="hdnLISTPOP1ID_11" class="form-control" autocomplete="off" />
                  </div>
                </div>
                <div class="col-lg-2 pl"><p>Description</p></div>
                <div class="col-lg-3 pl">
                    <input  class="form-control" style="width: 100%" type="text" name="DESC2_11" id ="DESC2_11"  autocomplete="off" readonly>
                </div>
              </div>
              
              <div class="row">
                <div class="col-lg-2 pl"><p>FA Account</p></div>
                <div class="col-lg-2 pl">
                  <div class="col-lg-10 pl">
                    <input type="text" name="txtLISTPOP1_popup_12" id="txtLISTPOP1_popup_12" class="form-control"  autocomplete="off"  readonly />
                    <input type="hidden" name="LISTPOP1ID_12" id="hdnLISTPOP1ID_12" class="form-control" autocomplete="off" />
                  </div>
                </div>
                <div class="col-lg-2 pl"><p>Description</p></div>
                <div class="col-lg-3 pl">
                    <input  class="form-control" style="width: 100%" type="text" name="DESC2_12" id ="DESC2_12"  autocomplete="off" readonly>
                </div>
              </div>
              
              <div class="row">
                <div class="col-lg-2 pl"><p>FA Clearing Account</p></div>
                <div class="col-lg-2 pl">
                  <div class="col-lg-10 pl">
                    <input type="text" name="txtLISTPOP1_popup_13" id="txtLISTPOP1_popup_13" class="form-control"  autocomplete="off"  readonly />
                    <input type="hidden" name="LISTPOP1ID_13" id="hdnLISTPOP1ID_13" class="form-control" autocomplete="off" />
                  </div>
                </div>
                <div class="col-lg-2 pl"><p>Description</p></div>
                <div class="col-lg-3 pl">
                    <input  class="form-control" style="width: 100%" type="text" name="DESC2_13" id ="DESC2_13"  autocomplete="off" readonly>
                </div>
              </div>
             
              <div class="row">
                <div class="col-lg-2 pl"><p>Depreciation Account</p></div>
                <div class="col-lg-2 pl">
                  <div class="col-lg-10 pl">
                    <input type="text" name="txtLISTPOP1_popup_14" id="txtLISTPOP1_popup_14" class="form-control"  autocomplete="off"  readonly />
                    <input type="hidden" name="LISTPOP1ID_14" id="hdnLISTPOP1ID_14" class="form-control" autocomplete="off" />
                  </div>
                </div>
                <div class="col-lg-2 pl"><p>Description</p></div>
                <div class="col-lg-3 pl">
                    <input  class="form-control" style="width: 100%" type="text" name="DESC2_14" id ="DESC2_14"  autocomplete="off" readonly>
                </div>
              </div>

              <div class="row">
                <div class="col-lg-2 pl"><p>Purchase Return Account</p></div>
                <div class="col-lg-2 pl">
                  <div class="col-lg-10 pl">
                    <input type="text" name="txtLISTPOP1_popup_15" id="txtLISTPOP1_popup_15" class="form-control"  autocomplete="off"  readonly />
                    <input type="hidden" name="LISTPOP1ID_15" id="hdnLISTPOP1ID_15" class="form-control" autocomplete="off" />
                  </div>
                </div>
                <div class="col-lg-2 pl"><p>Description</p></div>
                <div class="col-lg-3 pl">
                    <input  class="form-control" style="width: 100%" type="text" name="DESC2_15" id ="DESC2_15"  autocomplete="off" readonly>
                </div>
              </div>

              <div class="row">
                <div class="col-lg-2 pl"><p>Import Purchase Account</p></div>
                <div class="col-lg-2 pl">
                  <div class="col-lg-10 pl">
                    <input type="text" name="txtLISTPOP1_popup_16" id="txtLISTPOP1_popup_16" class="form-control"  autocomplete="off"  readonly />
                    <input type="hidden" name="LISTPOP1ID_16" id="hdnLISTPOP1ID_16" class="form-control" autocomplete="off" />
                  </div>
                </div>
                <div class="col-lg-2 pl"><p>Description</p></div>
                <div class="col-lg-3 pl">
                    <input  class="form-control" style="width: 100%" type="text" name="DESC2_16" id ="DESC2_16"  autocomplete="off" readonly>
                </div>
              </div>

              <div class="row">
                <div class="col-lg-2 pl"><p>Job Work Invoice Account</p></div>
                <div class="col-lg-2 pl">
                  <div class="col-lg-10 pl">
                    <input type="text" name="txtLISTPOP1_popup_17" id="txtLISTPOP1_popup_17" class="form-control"  autocomplete="off"  readonly />
                    <input type="hidden" name="LISTPOP1ID_17" id="hdnLISTPOP1ID_17" class="form-control" autocomplete="off" />
                  </div>
                </div>
                <div class="col-lg-2 pl"><p>Description</p></div>
                <div class="col-lg-3 pl">
                    <input  class="form-control" style="width: 100%" type="text" name="DESC2_17" id ="DESC2_17"  autocomplete="off" readonly>
                </div>
              </div>

              <div class="row">
                <div class="col-lg-2 pl"><p>Job Work Return Account</p></div>
                <div class="col-lg-2 pl">
                  <div class="col-lg-10 pl">
                    <input type="text" name="txtLISTPOP1_popup_18" id="txtLISTPOP1_popup_18" class="form-control"  autocomplete="off"  readonly />
                    <input type="hidden" name="LISTPOP1ID_18" id="hdnLISTPOP1ID_18" class="form-control" autocomplete="off" />
                  </div>
                </div>
                <div class="col-lg-2 pl"><p>Description</p></div>
                <div class="col-lg-3 pl">
                    <input  class="form-control" style="width: 100%" type="text" name="DESC2_18" id ="DESC2_18"  autocomplete="off" readonly>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-2 pl"><p>Custom Duty Account</p></div>
                <div class="col-lg-2 pl">
                  <div class="col-lg-10 pl">
                    <input type="text" name="txtLISTPOP1_popup_19" id="txtLISTPOP1_popup_19" class="form-control"  autocomplete="off"  readonly />
                    <input type="hidden" name="LISTPOP1ID_19" id="hdnLISTPOP1ID_19" class="form-control" autocomplete="off" />
                  </div>
                </div>
                <div class="col-lg-2 pl"><p>Description</p></div>
                <div class="col-lg-3 pl">
                    <input  class="form-control" style="width: 100%" type="text" name="DESC2_19" id ="DESC2_19"  autocomplete="off" readonly>
                </div>
              </div>
			  <div class="row">
                <div class="col-lg-2 pl"><p>Purchase Account(Inter State)</p></div>
                <div class="col-lg-2 pl">
                  <div class="col-lg-10 pl">
                    <input type="text" name="txtLISTPOP1_popup_20" id="txtLISTPOP1_popup_20" class="form-control"  autocomplete="off"  readonly />
                    <input type="hidden" name="LISTPOP1ID_20" id="hdnLISTPOP1ID_20" class="form-control" autocomplete="off" />
                  </div>
                </div>
                <div class="col-lg-2 pl"><p>Description</p></div>
                <div class="col-lg-3 pl">
                    <input  class="form-control" style="width: 100%" type="text" name="DESC2_20" id ="DESC2_20"  autocomplete="off" readonly>
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
{{-- Alert end --}}
<!-- POPUP2-->
<div id="LISTPOP1popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='LISTPOP1_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>General Leader Details</p></div>
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
            <th class="ROW2" style="width: 40%" >Code</th>
            <th class="ROW3" style="width: 40%" >Description</th>
          </tr>
    </thead>
    <tbody>
    <tr>
      <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
      <td class="ROW2"  style="width: 40%">
          <input type="text" autocomplete="off"  class="form-control" id="LISTPOP1codesearch" onkeyup="LISTPOP1CodeFunction()" />
      </td>
      <td class="ROW3"  style="width: 40%">
          <input type="text" autocomplete="off"  class="form-control" id="LISTPOP1namesearch" onkeyup="LISTPOP1NameFunction()" />
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
      var viewURL = '{{route("master",[167,"add"])}}';
      window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });

 var formResponseMst = $( "#frm_mst_add" );
     formResponseMst.validate();

    

    $("#AC_SET_CODE").blur(function(){
      $(this).val($.trim( $(this).val() ));
      $("#ERROR_AC_SET_CODE").hide();
      validateSingleElemnet("AC_SET_CODE");
         
    });

    $( "#AC_SET_CODE" ).rules( "add", {
        required: true,
        nowhitespace: true,
        StringNumberRegex: true, //from custom.js
        messages: {
            required: "Required field.",
        }
    });


    $("#AC_SET_DESC").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_NAME").hide();
        validateSingleElemnet("AC_SET_DESC");
    });

    $( "#AC_SET_DESC" ).rules( "add", {
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
          if(element_id=="AC_SET_CODE" || element_id=="AC_SET_CODE" ) {
            checkDuplicateCode();

          }

         }
    }

    // //check duplicate exist code
    function checkDuplicateCode(){
        
        //validate and save data
        var getDataForm = $("#AC_SET_CODE");
        var formData = getDataForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("master",[167,"codeduplicate"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
                if(data.exists) {
                    $(".text-danger").hide();
                    showError('ERROR_AC_SET_CODE',data.msg);
                    $("#AC_SET_CODE").focus();
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
            url:'{{route("master",[167,"save"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    if(data.errors.AC_SET_CODE){
                        showError('ERROR_AC_SET_CODE',data.errors.AC_SET_CODE);
                    }
                    if(data.errors.AC_SET_DESC){
                        showError('ERROR_NAME',data.errors.AC_SET_DESC);
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

                  //  window.location.href='{{ route("master",[167,"index"])}}';
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
        $("#AC_SET_CODE").focus();
        
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
      window.location.href = "{{route('master',[167,'add'])}}";

   }//fnUndoYes


   window.fnUndoNo = function (){
      $("#AC_SET_CODE").focus();
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



$(function() { $("#SALES_AC_POPUP").focus(); });
    
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

  $(document).on('click','[id*="txtLISTPOP1_popup"]',function(event){

        
          var id = $(this).attr('id');
          var id2 = $(this).parent().parent().find('[id*="LISTPOP1ID"]').attr('id');      
          var id3 = $(this).parent().parent().parent().find('[id*="DESC2"]').attr('id');      

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
              url:'{{route("master",[167,"getgl"])}}',
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
              
              var txtid= $('#hdn_LISTPOP1id').val();
              var txt_id2= $('#hdn_LISTPOP1id2').val();
              var txt_id3= $('#hdn_LISTPOP1id3').val();

              $('#'+txtid).val(texdesc);
              $('#'+txt_id2).val(txtval);
              $('#'+txt_id3).val(texdescdate);
              $('#'+txtid).parent().parent().find('[id*="REMARKS"]').val( $.trim($('#MODNAME').val()) );
              $('#'+txtid).parent().parent().find('[id*="VHEADING"]').prop('selectedIndex',0);
              

              $("#LISTPOP1popup").hide();
              
              $("#LISTPOP1codesearch").val(''); 
              $("#LISTPOP1namesearch").val(''); 
              LISTPOP1CodeFunction();
              $(this).prop("checked",false);
              event.preventDefault();
          });
      }
//------------------------

</script>

@endpush