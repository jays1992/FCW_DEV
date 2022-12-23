@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[165,'index'])}}" class="btn singlebt">Sales Account Set</a>
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
                      <div class="col-lg-7 pl">
                          <input type="text" name="AC_SET_CODE" id="AC_SET_CODE" onkeypress="return AlphaNumaric(event,this)" value="{{ old('AC_SET_CODE') }}" class="form-control mandatory" autocomplete="off" maxlength="10" tabindex="3" style="text-transform:uppercase" />
                          <span class="text-danger" id="ERROR_AC_SET_CODE"></span> 
                      </div>
                    </div>

                    <div class="col-lg-2 pl"><p>Account set code Description</p></div>
                    <div class="col-lg-3 pl">
                      <input type="text" name="AC_SET_DESC" id="AC_SET_DESC" class="form-control mandatory" value="{{ old('AC_SET_DESC') }}" maxlength="50" tabindex="4"  />
                      <span class="text-danger" id="ERROR_AC_SET_DESC"></span> 
                    </div>
                </div>

              <div class="row">
                  <div class="col-lg-2 pl"><p>Sales Account Code</p></div>
                  <div class="col-lg-2 pl">
                    <div class="col-lg-7 pl">
                        <input type="text" name="SALES_AC_POPUP" id="SALES_AC_POPUP" class="form-control mandatory" readonly tabindex="1" />
                        <input type="hidden" name="SALES_AC" id="SALES_AC" />
                        <span class="text-danger" id="ERROR_SALES_AC"></span>
                    </div>
                  </div>

                  <div class="col-lg-2 pl"><p>Sales Account Description</p></div>
                  <div class="col-lg-3 pl">
                      <input type="text" name="SALES_AC_DESC" id="SALES_AC_DESC" class="form-control" readonly  />
                  </div>
              </div>

              <div class="row">
                  <div class="col-lg-2 pl"><p>Sales Return Code</p></div>
                  <div class="col-lg-2 pl">
                    <div class="col-lg-7 pl">
                        <input type="text" name="SALES_RETURN_POPUP" id="SALES_RETURN_POPUP" class="form-control mandatory" readonly tabindex="1" />
                        <input type="hidden" name="SALES_RETURN" id="SALES_RETURN" />
                        <span class="text-danger" id="ERROR_SALES_RETURN"></span>
                    </div>
                  </div>

                  <div class="col-lg-2 pl"><p>Sales Return Description</p></div>
                  <div class="col-lg-3 pl">
                      <input type="text" name="SALES_RETURN_DESC" id="SALES_RETURN_DESC" class="form-control" readonly  />
                  </div>
              </div>

              <div class="row">
                    <div class="col-lg-2 pl"><p>Shortage Code</p></div>
                    <div class="col-lg-2 pl">
                      <div class="col-lg-7 pl">
                          <input type="text" name="SHORTAGE_POPUP" id="SHORTAGE_POPUP" class="form-control mandatory" readonly tabindex="2" />
                          <input type="hidden" name="SHORTAGE" id="SHORTAGE" />
                          <span class="text-danger" id="ERROR_SHORTAGE"></span>
                      </div>
                    </div>

                    <div class="col-lg-2 pl"><p>Shortage Description</p></div>
                    <div class="col-lg-3 pl">
                        <input type="text" name="SHORTAGE_DESC" id="SHORTAGE_DESC" class="form-control" readonly  />  
                    </div>
                </div>

                <div class="row">
                  <div class="col-lg-2 pl"><p>Cost of Good Sold Code</p></div>
                  <div class="col-lg-2 pl">
                    <div class="col-lg-7 pl">
                        <input type="text" name="COST_OF_GOOD_SOLD_POPUP" id="COST_OF_GOOD_SOLD_POPUP" class="form-control mandatory" readonly tabindex="1" />
                        <input type="hidden" name="COST_OF_GOOD_SOLD" id="COST_OF_GOOD_SOLD" />
                        <span class="text-danger" id="ERROR_COST_OF_GOOD_SOLD"></span>
                    </div>
                  </div>

                  <div class="col-lg-2 pl"><p> Cost of Good Sold Description</p></div>
                  <div class="col-lg-3 pl">
                      <input type="text" name="COST_OF_GOOD_SOLD_POPUP_DESC" id="COST_OF_GOOD_SOLD_DESC" class="form-control" readonly  />
                  </div>
              </div>

              <div class="row">
                  <div class="col-lg-2 pl"><p>Export Sale Account Code</p></div>
                  <div class="col-lg-2 pl">
                    <div class="col-lg-7 pl">
                        <input type="text" name="EXPORT_SALE_ACCT_POPUP" id="EXPORT_SALE_ACCT_POPUP" class="form-control mandatory" readonly tabindex="1" />
                        <input type="hidden" name="EXPORT_SALE_ACCT" id="EXPORT_SALE_ACCT" />
                        <span class="text-danger" id="ERROR_EXPORT_SALE_ACCT"></span>
                    </div>
                  </div>

                  <div class="col-lg-2 pl"><p>Export Sale Account Description</p></div>
                  <div class="col-lg-3 pl">
                      <input type="text" name="EXPORT_SALE_ACCT_POPUP_DESC" id="EXPORT_SALE_ACCT_DESC" class="form-control" readonly  />
                  </div>
              </div>
			  <div class="row">
                  <div class="col-lg-2 pl"><p>Cost of Good Sold Transfer Code</p></div>
                  <div class="col-lg-2 pl">
                    <div class="col-lg-7 pl">
                        <input type="text" name="COST_OF_GOOD_SOLD_EXPORT_POPUP" id="COST_OF_GOOD_SOLD_EXPORT_POPUP" class="form-control mandatory" readonly tabindex="1" />
                        <input type="hidden" name="COST_OF_GOOD_SOLD_EXPORT" id="COST_OF_GOOD_SOLD_EXPORT" />
                        <span class="text-danger" id="ERROR_COST_OF_GOOD_SOLD_EXPORT"></span>
                    </div>
                  </div>

                  <div class="col-lg-2 pl"><p> Cost of Good Sold Transfer Description</p></div>
                  <div class="col-lg-3 pl">
                      <input type="text" name="COST_OF_GOOD_SOLD_EXPORT_POPUP_DESC" id="COST_OF_GOOD_SOLD_EXPORT_DESC" class="form-control" readonly  />
                  </div>
              </div>
			  <div class="row">
                  <div class="col-lg-2 pl"><p>Sales Account Code(Inter State)</p></div>
                  <div class="col-lg-2 pl">
                    <div class="col-lg-7 pl">
                        <input type="text" name="SALESIS_AC_POPUP" id="SALESIS_AC_POPUP" class="form-control mandatory" readonly tabindex="1" />
                        <input type="hidden" name="SALESIS_AC" id="SALESIS_AC" />
                        <span class="text-danger" id="ERROR_SALESIS_AC"></span>
                    </div>
                  </div>

                  <div class="col-lg-2 pl"><p>Sales Account Description(Inter State)</p></div>
                  <div class="col-lg-3 pl">
                      <input type="text" name="SALESIS_AC_DESC" id="SALESIS_AC_DESC" class="form-control" readonly  />
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

<!--Sales Return Popup-->
<div id="sales_return_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='ctryidref_close1' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Sales Return</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="country_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th class="ROW1" style="width: 10%" align="center">Select</th> 
            <th class="ROW2" style="width: 40%" >Sales Return Code</th>
            <th class="ROW3" style="width: 40%" >Sales Return Description</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
          <td class="ROW2"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control"  id="country_codesearch" onkeyup="searchCountryCode()"/></td>
          <td class="ROW3"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control"  id="country_namesearch" onkeyup="searchCountryName()" /></td>
        </tr>
        </tbody>
      </table>


      <table id="country_tab2" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody id="country_body">
        @foreach ($objLedgerList as $index=>$LedgerList)
        <tr >
          <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_SRGLID_REF[]"  id="ctryidref_{{ $LedgerList->GLID }}" class="cls_ctryidref1" value="{{ $LedgerList->GLID }}" /></td>
          <td class="ROW2" style="width: 39%">{{ $LedgerList->GLCODE }}
            <input type="hidden" id="txtctryidref_{{ $LedgerList->GLID }}" data-desc="{{ $LedgerList->GLCODE }}" data-descname="{{ $LedgerList->GLNAME }}" value="{{ $LedgerList-> GLID }}"/>
          </td>
          <td class="ROW3" style="width: 39%">{{ $LedgerList->GLNAME }}</td>
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

<!--Shortage Popup-->
<div id="shortage_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='shortage_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Shortage</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="country_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th class="ROW1" style="width: 10%" align="center">Select</th> 
            <th class="ROW2" style="width: 40%" >Shortage Code</th>
            <th class="ROW3" style="width: 40%" >Shortage Description</th>
          </tr>
         /thead>
        <tbody>
        <tr>
          <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
          <td class="ROW2"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="shortage_codesearch" onkeyup="searchshortageCode()" /></td>
          <td class="ROW3"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control"  id="shortage_namesearch" onkeyup="searchshortageName()" /></td>
        </tr>
        </tbody>
      </table>


      <table id="shortage_tab" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody id="shortage_body">
        @foreach ($objLedgerList as $index=>$LedgerList)
        <tr >
          <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_SCGLID_REF[]"   id="ctryidref_{{ $LedgerList->GLID }}" class="cls_shortage" value="{{ $LedgerList->GLID }}" /></td>
          <td class="ROW2" style="width: 39%">{{ $LedgerList->GLCODE }}
            <input type="hidden" id="txtctryidref_{{ $LedgerList->GLID }}" data-desc="{{ $LedgerList->GLCODE }}" data-descname="{{ $LedgerList->GLNAME }}" value="{{ $LedgerList-> GLID }}"/>
          </td>
          <td class="ROW3" style="width: 39%">{{ $LedgerList->GLNAME }}</td>
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

<!--Shortage Popup-->
<div id="cost_of_good_sold_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='cost_of_good_sold_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Cost of Good Sold</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="country_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th class="ROW1" style="width: 10%" align="center">Select</th> 
            <th class="ROW2" style="width: 40%" >Cost of Good Sold Code</th>
            <th class="ROW3" style="width: 40%" >Cost of Good Sold Description</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
          <td class="ROW2"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control"  id="cost_codesearch" onkeyup="searchcostCode()"></td>
          <td class="ROW3"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control"  id="cost_namesearch" onkeyup="searchcostName()"></td>
        </tr>
        </tbody>
      </table>


      <table id="cost_tab" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody id="shortage_body">
        @foreach ($objLedgerList as $index=>$LedgerList)
        <tr >
          <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_CGSGLIDD_REF[]"  id="ctryidref_{{ $LedgerList->GLID }}" class="cls_cost_of_good_sold" value="{{ $LedgerList->GLID }}" /></td>
          <td class="ROW2" style="width: 39%">{{ $LedgerList->GLCODE }}
            <input type="hidden" id="txtctryidref_{{ $LedgerList->GLID }}" data-desc="{{ $LedgerList->GLCODE }}" data-descname="{{ $LedgerList->GLNAME }}" value="{{ $LedgerList-> GLID }}"/>
          </td>
          <td class="ROW3" style="width: 39%">{{ $LedgerList->GLNAME }}</td>
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

<!--cost_of_good_sold_export_popup-->
<div id="COST_OF_GOOD_SOLD_EXPORT_POPUP" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='cost_of_good_sold_export_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Cost of Good Sold Export</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="country_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th class="ROW1" style="width: 10%" align="center">Select</th> 
            <th class="ROW2" style="width: 40%" >Cost of Good Sold Export Code</th>
            <th class="ROW3" style="width: 40%" >Cost of Good Sold Export Description</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
          <td class="ROW2"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control"  id="coste_codesearch" onkeyup="searchcosteCode()"></td>
          <td class="ROW3"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control"  id="coste_namesearch" onkeyup="searchcosteName()"></td>
        </tr>
        </tbody>
      </table>


      <table id="cogs_tab" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody id="cogs_body">
        @foreach ($objLedgerList as $index=>$LedgerList)
        <tr >
          <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_CGSEGLIDD_REF[]"  id="ctryidref_{{ $LedgerList->GLID }}" class="cls_cost_of_good_sold_export" value="{{ $LedgerList->GLID }}" /></td>
          <td class="ROW2" style="width: 39%">{{ $LedgerList->GLCODE }}
            <input type="hidden" id="txtctryidref_{{ $LedgerList->GLID }}" data-desc="{{ $LedgerList->GLCODE }}" data-descname="{{ $LedgerList->GLNAME }}" value="{{ $LedgerList-> GLID }}"/>
          </td>
          <td class="ROW3" style="width: 39%">{{ $LedgerList->GLNAME }}</td>
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


<!--Export Sale Popup-->
<div id="EXPORT_SALE_ACCT_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='EXPORT_SALE_ACCT_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Export Sales Account</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="Export_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th class="ROW1" style="width: 10%" align="center">Select</th> 
            <th class="ROW2" style="width: 40%" >Export Sales Account Code</th>
            <th class="ROW3" style="width: 40%" >Export Sales Account Description</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
          <td class="ROW2"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control"  id="export_codesearch" onkeyup="searchexportCode()"></td>
          <td class="ROW3"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control"  id="export_namesearch" onkeyup="searchexportName()"></td>
        </tr>
        </tbody>
      </table>


      <table id="Export_tab" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody id="Export_body">
        @foreach ($objLedgerList as $index=>$LedgerList)
        <tr >
          <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_ESALIDD_REF[]"  id="ctryidref_{{ $LedgerList->GLID }}" class="cls_export_sale_acct" value="{{ $LedgerList->GLID }}" /></td>
          <td class="ROW2" style="width: 39%">{{ $LedgerList->GLCODE }}
            <input type="hidden" id="txtctryidref_{{ $LedgerList->GLID }}" data-desc="{{ $LedgerList->GLCODE }}" data-descname="{{ $LedgerList->GLNAME }}" value="{{ $LedgerList-> GLID }}"/>
          </td>
          <td class="ROW3" style="width: 39%">{{ $LedgerList->GLNAME }}</td>
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
<!--Export Sale Popup-->

<!--Sales Account Inter State Popup-->
<div id="salesisaccount_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='salesisaccount_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Sales Account(Inter State)</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="country_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th class="ROW1" style="width: 10%" align="center">Select</th> 
            <th class="ROW2" style="width: 40%" >Sales Account Code</th>
            <th class="ROW3" style="width: 40%" >Sales Account Description</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
          <td class="ROW2"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="salesisaccount_codesearch" onkeyup="searchissalesaccountCode()"/></td>
          <td class="ROW3"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="salesisaccount_namesearch" onkeyup="searchissalesaccountName()"/></td>
        </tr>
        </tbody>
      </table>


      <table id="salesisaccount_tab" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody id="salesisaccount_body">
        @foreach ($objLedgerList as $index=>$LedgerList)
        <tr >
          <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_SAGLID[]"  id="ctryidref_{{ $LedgerList->GLID }}" class="salesisaccount_tab" value="{{ $LedgerList->GLID }}" /></td>
          <td class="ROW2" style="width: 39%">{{ $LedgerList->GLCODE }}
          <input type="hidden" id="txtctryidref_{{ $LedgerList->GLID }}" data-desc="{{ $LedgerList->GLCODE }}" data-descname="{{ $LedgerList->GLNAME }}" value="{{ $LedgerList-> GLID }}"/>
          </td>
          <td class="ROW3" style="width: 39%">{{ $LedgerList->GLNAME }}</td>
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
<!--Sales Account Inter State Popup-->


<!--Sales Account Popup-->
<div id="salesaccount_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='ctryidref_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Sales Account</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="country_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th class="ROW1" style="width: 10%" align="center">Select</th> 
            <th class="ROW2" style="width: 40%" >Sales Account Code</th>
            <th class="ROW3" style="width: 40%" >Sales Account Description</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
          <td class="ROW2"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="salesaccount_codesearch" onkeyup="searchsalesaccountCode()"/></td>
          <td class="ROW3"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="salesaccount_namesearch" onkeyup="searchsalesaccountName()"/></td>
        </tr>
        </tbody>
      </table>


      <table id="salesaccount_tab" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody id="country_body">
        @foreach ($objLedgerList as $index=>$LedgerList)
        <tr >
          <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_SAGLID[]"  id="ctryidref_{{ $LedgerList->GLID }}" class="salesaccount_tab" value="{{ $LedgerList->GLID }}" /></td>
          <td class="ROW2" style="width: 39%">{{ $LedgerList->GLCODE }}
          <input type="hidden" id="txtctryidref_{{ $LedgerList->GLID }}" data-desc="{{ $LedgerList->GLCODE }}" data-descname="{{ $LedgerList->GLNAME }}" value="{{ $LedgerList-> GLID }}"/>
          </td>
          <td class="ROW3" style="width: 39%">{{ $LedgerList->GLNAME }}</td>
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
<!-- btnSave -->

@push('bottom-scripts')
<script>

// Sales Account popup function

$("#SALES_AC_POPUP").on("click",function(event){ 
  $("#salesaccount_popup").show();
});

$("#SALES_AC_POPUP").keyup(function(event){
  if(event.keyCode==13){
    $("#salesaccount_popup").show();
  }
});

$("#ctryidref_close").on("click",function(event){ 
  $("#salesaccount_popup").hide();
});

$('.salesaccount_tab').click(function(){


  var id          =   $(this).attr('id');


  var txtval      =   $("#txt"+id+"").val();
  var texdesc     =   $("#txt"+id+"").data("desc");
  var texdescname =   $("#txt"+id+"").data("descname");

  $("#SALES_AC_DESC").val(texdescname);
  $("#SALES_AC_POPUP").val(texdesc);
  $("#SALES_AC").val(txtval);

  getCountryWiseState(txtval);
  
  $("#SALES_AC_POPUP").blur(); 
  $("#STID_REF_POPUP").focus(); 
  
  $("#salesaccount_popup").hide();
  searchCountryCode();
  event.preventDefault();
  $(this).prop("checked",false);
});

// Sales Account Inter State popup function

$("#SALESIS_AC_POPUP").on("click",function(event){ 
  $("#salesisaccount_popup").show();
});

$("#SALESIS_AC_POPUP").keyup(function(event){
  if(event.keyCode==13){
    $("#salesisaccount_popup").show();
  }
});

$("#salesisaccount_close").on("click",function(event){ 
  $("#salesisaccount_popup").hide();
});

$('.salesisaccount_tab').click(function(){


  var id          =   $(this).attr('id');


  var txtval      =   $("#txt"+id+"").val();
  var texdesc     =   $("#txt"+id+"").data("desc");
  var texdescname =   $("#txt"+id+"").data("descname");

  $("#SALESIS_AC_DESC").val(texdescname);
  $("#SALESIS_AC_POPUP").val(texdesc);
  $("#SALESIS_AC").val(txtval);

  getCountryWiseState(txtval);
  
  $("#SALESIS_AC_POPUP").blur(); 
  $("#STID_REF_POPUP").focus(); 
  
  $("#salesisaccount_popup").hide();
  searchCountryCode();
  event.preventDefault();
  $(this).prop("checked",false);
});

function searchsalesisaccountCode() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("salesisaccount_codesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("salesisaccount_tab");
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

function searchsalesisaccountName() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("salesisaccount_namesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("salesisaccount_tab");
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


// Sales Return popup function

$("#SALES_RETURN_POPUP").on("click",function(event){ 
  $("#sales_return_popup").show();
});

$("#SALES_RETURN_POPUP").keyup(function(event){
  if(event.keyCode==13){
    $("#sales_return_popup").show();
  }
});

$("#ctryidref_close1").on("click",function(event){ 
  $("#sales_return_popup").hide();
});

$('.cls_ctryidref1').click(function(){
  var id          =   $(this).attr('id');
  var txtval      =   $("#txt"+id+"").val();
  var texdesc     =   $("#txt"+id+"").data("desc");
  var texdescname =   $("#txt"+id+"").data("descname");

  $("#SALES_RETURN_DESC").val(texdescname);
  $("#SALES_RETURN_POPUP").val(texdesc);
  $("#SALES_RETURN").val(txtval);


  
  $("#SALES_RETURN_POPUP").blur(); 
  $("#STID_REF_POPUP").focus(); 
  
  $("#sales_return_popup").hide();
  searchCountryCode();
  event.preventDefault();
  $(this).prop("checked",false);
});


function searchsalesaccountCode() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("salesaccount_codesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("salesaccount_tab");
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

function searchsalesaccountName() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("salesaccount_namesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("salesaccount_tab");
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

// Shortage popup function

$("#SHORTAGE_POPUP").on("click",function(event){ 
  $("#shortage_popup").show();
});

$("#SHORTAGE_POPUP").keyup(function(event){
  if(event.keyCode==13){
    $("#SHORTAGE_POPUP").show();
  }
});

$("#shortage_close").on("click",function(event){ 
  $("#shortage_popup").hide();
});

$('.cls_shortage').click(function(){
  var id          =   $(this).attr('id');
  var txtval      =   $("#txt"+id+"").val();
  var texdesc     =   $("#txt"+id+"").data("desc");
  var texdescname =   $("#txt"+id+"").data("descname");

  $("#SHORTAGE_DESC").val(texdescname);
  $("#SHORTAGE_POPUP").val(texdesc);
  $("#SHORTAGE").val(txtval);
 
  
  $("#SHORTAGE_POPUP").blur(); 
  $("#STID_REF_POPUP").focus(); 
  
  $("#shortage_popup").hide();

  event.preventDefault();
  $(this).prop("checked",false);
  
});


function searchshortageCode() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("shortage_codesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("shortage_tab");
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

function searchshortageName() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("shortage_namesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("shortage_tab");
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
// Export Sale Account popup function

$("#EXPORT_SALE_ACCT_POPUP").on("click",function(event){ 
  $("#EXPORT_SALE_ACCT_popup").show();
});

$("#EXPORT_SALE_ACCT_POPUP").keyup(function(event){
  if(event.keyCode==13){
    $("#EXPORT_SALE_ACCT_popup").show();
  }
});

$("#EXPORT_SALE_ACCT_close").on("click",function(event){ 
  $("#EXPORT_SALE_ACCT_popup").hide();
});

$('.cls_export_sale_acct').click(function(){
  var id          =   $(this).attr('id');
  var txtval      =   $("#txt"+id+"").val();
  var texdesc     =   $("#txt"+id+"").data("desc");
  var texdescname =   $("#txt"+id+"").data("descname");

  $("#EXPORT_SALE_ACCT_DESC").val(texdescname);
  $("#EXPORT_SALE_ACCT_POPUP").val(texdesc);
  $("#EXPORT_SALE_ACCT").val(txtval);

 
  
  $("#EXPORT_SALE_ACCT_POPUP").blur(); 
  $("#STID_REF_POPUP").focus(); 
  
  $("#EXPORT_SALE_ACCT_popup").hide();
  $(this).prop("checked",false);

  event.preventDefault();
});


function searchexportCode() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("export_codesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("Export_tab");
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

function searchexportName() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("export_namesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("Export_tab");
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

// End of Export Sale Account

// COST_OF_GOOD_SOLD_EXPORT popup function

$("#COST_OF_GOOD_SOLD_EXPORT_POPUP").on("click",function(event){ 
  $("#COST_OF_GOOD_SOLD_EXPORT_POPUP").show();
});

$("#COST_OF_GOOD_SOLD_EXPORT_POPUP").keyup(function(event){
  if(event.keyCode==13){
    $("#COST_OF_GOOD_SOLD_EXPORT_POPUP").show();
  }
});

$("#cost_of_good_sold_export_close").on("click",function(event){ 
  $("#COST_OF_GOOD_SOLD_EXPORT_POPUP").hide();
});

$('.cls_cost_of_good_sold_export').click(function(){
  var id          =   $(this).attr('id');
  var txtval      =   $("#txt"+id+"").val();
  var texdesc     =   $("#txt"+id+"").data("desc");
  var texdescname =   $("#txt"+id+"").data("descname");

  $("#COST_OF_GOOD_SOLD_EXPORT_DESC").val(texdescname);
  $("#COST_OF_GOOD_SOLD_EXPORT_POPUP").val(texdesc);
  $("#COST_OF_GOOD_SOLD_EXPORT").val(txtval);

 
  
  $("#COST_OF_GOOD_SOLD_EXPORT_POPUP").blur(); 
  $("#STID_REF_POPUP").focus(); 
  
  $("#COST_OF_GOOD_SOLD_EXPORT_POPUP").hide();
  $(this).prop("checked",false);

  event.preventDefault();
});


function searchcosteCode() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("coste_codesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("cogs_tab");
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

function searchcosteName() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("coste_namesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("cogs_tab");
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
// END COST_OF_GOOD_SOLD_EXPORT popup function
// COST_OF_GOOD_SOLD_ popup function

$("#COST_OF_GOOD_SOLD_POPUP").on("click",function(event){ 
  $("#cost_of_good_sold_popup").show();
});

$("#COST_OF_GOOD_SOLD_POPUP").keyup(function(event){
  if(event.keyCode==13){
    $("#COST_OF_GOOD_SOLD_POPUP").show();
  }
});

$("#cost_of_good_sold_close").on("click",function(event){ 
  $("#cost_of_good_sold_popup").hide();
});

$('.cls_cost_of_good_sold').click(function(){
  var id          =   $(this).attr('id');
  var txtval      =   $("#txt"+id+"").val();
  var texdesc     =   $("#txt"+id+"").data("desc");
  var texdescname =   $("#txt"+id+"").data("descname");

  $("#COST_OF_GOOD_SOLD_DESC").val(texdescname);
  $("#COST_OF_GOOD_SOLD_POPUP").val(texdesc);
  $("#COST_OF_GOOD_SOLD").val(txtval);

 
  
  $("#COST_OF_GOOD_SOLD_POPUP").blur(); 
  $("#STID_REF_POPUP").focus(); 
  
  $("#cost_of_good_sold_popup").hide();
  $(this).prop("checked",false);

  event.preventDefault();
});


function searchcostCode() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("cost_codesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("cost_tab");
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

function searchcostName() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("cost_namesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("cost_tab");
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

function searchCountryCode() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("country_codesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("country_tab2");
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

function searchCountryName() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("country_namesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("country_tab2");
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

function getCountryWiseState(SALES_AC){
    $("#state_body").html('');
		$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
		
    $.ajax({
        url:'{{route("master",[165,"getCountryWiseState"])}}',
        type:'POST',
        data:{SALES_AC:SALES_AC},
        success:function(data) {
          
          $("#State_Name").val('');
          $("#STID_REF_POPUP").val('');
          $("#STID_REF").val('');

          $("#City_Name").val('');
          $("#CITYID_REF_POPUP").val('');
          $("#CITYID_REF").val('');
          $("#city_body").html('');
        
          $("#state_body").html(data);
          bindStateEvents(); 

        },
        error:function(data){
          console.log("Error: Something went wrong.");
          $("#state_body").html('');
          
        },
    });	
  }


  // State popup function

$("#STID_REF_POPUP").on("click",function(event){ 
  $("#stidref_popup").show();
});

$("#STID_REF_POPUP").keyup(function(event){
  if(event.keyCode==13){
    $("#stidref_popup").show();
  }
});

$("#stidref_close").on("click",function(event){ 
  $("#stidref_popup").hide();
});











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

  let country_tab1 = "#country_tab1";
  let country_tab2 = "#country_tab2";
  let country_headers = document.querySelectorAll(country_tab1 + " th");

  country_headers.forEach(function(element, i) {
    element.addEventListener("click", function() {
      w3.sortHTML(country_tab2, ".cls_ctryidref", "td:nth-child(" + (i + 1) + ")");
    });
  });

  
  let state_tab1 = "#state_tab1";
  let state_tab2 = "#state_tab2";
  let state_headers = document.querySelectorAll(state_tab1 + " th");

  state_headers.forEach(function(element, i) {
    element.addEventListener("click", function() {
      w3.sortHTML(state_tab2, ".cls_stidref", "td:nth-child(" + (i + 1) + ")");
    });
  });

  let city_tab1 = "#city_tab1";
  let city_tab2 = "#city_tab2";
  let city_headers = document.querySelectorAll(city_tab1 + " th");

  city_headers.forEach(function(element, i) {
    element.addEventListener("click", function() {
      w3.sortHTML(city_tab2, ".cls_cityidref", "td:nth-child(" + (i + 1) + ")");
    });
  });


  $('#btnAdd').on('click', function() {
      var viewURL = '{{route("master",[165,"add"])}}';
      window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });

 var formResponseMst = $( "#frm_mst_add" );
     formResponseMst.validate();

    $("#SALES_AC_POPUP").blur(function(){
      $(this).val($.trim( $(this).val() ));
      $("#ERROR_SALES_AC").hide();
      validateSingleElemnet("SALES_AC_POPUP");
         
    });
 
    $( "#AC_SET_CODE" ).rules( "add", {
        required: true,
        nowhitespace: true,
        StringNumberRegex: true,
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
        var getDataForm = $("#frm_mst_add");
        var formData = getDataForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("master",[165,"codeduplicate"])}}',
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
            url:'{{route("master",[165,"save"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    if(data.errors.AC_SET_CODE){
                        showError('ERROR_AC_SET_CODE',data.errors.AC_SET_CODE);
                    }
                    if(data.errors.NAME){
                        showError('ERROR_NAME',data.errors.NAME);
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

                  //  window.location.href='{{ route("master",[165,"index"])}}';
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
      window.location.href = "{{route('master',[165,'add'])}}";

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