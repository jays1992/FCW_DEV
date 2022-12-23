
@extends('layouts.app')
@section('content')
    

    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('transaction',[168,'index'])}}" class="btn singlebt">Annual Forecast Sales (AFS)</a>
                </div>

                <div class="col-lg-10 topnav-pd">
                        <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
                        <button class="btn topnavbt" id="btnSaveSE" ><i class="fa fa-save"></i> Save</button>
                        <button style="display:none" class="btn topnavbt buttonload"> <i class="fa fa-refresh fa-spin"></i> {{Session::get('save')}}</button>
                        <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt" id="btnUndo"  ><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-lock"></i>  Approved</button>
                        <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                        <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                </div>
            </div>
    </div>

    <form id="frm_trn_se" onsubmit="return validateForm()"  method="POST" class="needs-validation"  >
    <div class="container-fluid filter">

	<div class="inner-form">
	
		<div class="row">
			<div class="col-lg-1 pl"><p>AFS No</p></div>
			<div class="col-lg-1 pl">
            <input type="text" name="AFSNO" id="AFSNO" value="{{$docarray['DOC_NO']}}" {{$docarray['READONLY']}} class="form-control" maxlength="{{$docarray['MAXLENGTH']}}" autocomplete="off" style="text-transform:uppercase" >
          <script>docMissing(@json($docarray['FY_FLAG']));</script>
			</div>
			
			<div class="col-lg-1 pl col-md-offset-1"><p>AFS Date</p></div>
			<div class="col-lg-2 pl">
      <input type="hidden" name="ASFDT" id="ASFDT" class="form-control" autocomplete="off" />
                <input type="hidden" name="hdnmaterial" id="hdnmaterial" class="form-control" autocomplete="off" />
            <input type="date" name="AFSDT" id="AFSDT" onchange='checkPeriodClosing(168,this.value,1),getDocNoByEvent("AFSNO",this,@json($doc_req))' value="{{ old('AFSDT') }}" class="form-control mandatory AFSDT"  placeholder="dd/mm/yyyy" >
           
            </div>
			
			<div class="col-lg-1 pl"><p> Department	</p></div> 
			<div class="col-lg-2 pl">
      <input type="text" name="DEPTID_NAME" id="DEPTID_NAME" value=""  class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase"  >
      <input type="hidden" name="DEPID_REF" id="DEPID_REF" value="" class="form-control mandatory"  autocomplete="off"  style="text-transform:uppercase"  >
			</div>
			
			<div class="col-lg-1 pl"><p>Financial Year</p></div>
			<div class="col-lg-2 pl">
      <input type="text" name="FYID_NAME" id="FYID_NAME" value=""  class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase"  >
      <input type="hidden" name="FYID_REF" id="FYID_REF" value="" class="form-control mandatory"  autocomplete="off"  style="text-transform:uppercase"  >


			</div>
		</div>

		

		
	</div>

	<div class="container-fluid">

		<div class="row">
			<ul class="nav nav-tabs">
				<li class="active"><a data-toggle="tab" href="#Material">Material</a></li> 
			</ul>

			
      <div class="tab-content">
                                <div id="Material" class="tab-pane fade in active">
                                    <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:280px;margin-top:10px;" >
                                        <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                                            <thead id="thead1"  style="position: sticky;top: 0">
                                                    
                                                    <tr>
                                                        <th colspan="7"></th>
                                                        <th colspan="2">April</th>
                                                        <th colspan="2">May</th>
                                                        <th colspan="2">June</th>
                                                        <th colspan="2">July</th>
                                                        <th colspan="2">August</th>
                                                        <th colspan="2">September</th>
                                                        <th colspan="2">October</th>
                                                        <th colspan="2">November</th>
                                                        <th colspan="2">December</th>
                                                        <th colspan="2">January</th>
                                                        <th colspan="2">February</th>
                                                        <th colspan="2">March</th>
                                                        <th colspan="2">Financial</th>
                                                        <th colspan=></th>
                                                        
                                                    
                                                    </tr>
                                                    <tr>
                                                        <th width="10%">Business Unit	<input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1"></th>
                                                        <th width="10%">Item Code</th>
                                                        <th  width="15%">Item Name</th>
                                                        <th>Customer</th>
                                                        <th>Part No</th>
                                                        <th>Main UOM</th>
                                                        <th width="15%">Item Specification</th>
                                                     
                                                        <th>Qty</th>
                                                        <th>Value</th>
                                                        <th>Qty</th>
                                                        <th>Value</th>
                                                        <th>Qty</th>
                                                        <th>Value</th>
                                                        <th>Qty</th>
                                                        <th>Value</th>
                                                        <th>Qty</th>
                                                        <th>Value</th>
                                                        <th>Qty</th>
                                                        <th>Value</th>
                                                        <th>Qty</th>
                                                        <th>Value</th>
                                                        <th>Qty</th>
                                                        <th>Value</th>
                                                        <th>Qty</th>
                                                        <th>Value</th>
                                                        <th>Qty</th>
                                                        <th>Value</th>
                                                        <th>Qty</th>
                                                        <th>Value</th>
                                                        <th>Qty</th>
                                                        <th>Value</th>
                                                        <th>Qty</th>
                                                        <th>Value</th>
                                                
                                                      
                                                        <th  width="6%">Action</th>
                                                    </tr>
                                            </thead>
                                            <tbody>
                                                <tr  class="participantRow">
                                                <td style="text-align:center;">
                           
        
                           <input type="text" name="BUID_REF_0" id="BUID_REF_0" onClick="get_section($(this).attr('id'))" class="form-control mandatory" style="width:91px" readonly tabindex="1" />
                          
                         
                               
                           </td>
                           <td hidden> <input type="text" name="REF_BUID_0" id="REF_BUID_0" />
                           <input type="text" name="rowscount[]"  /></td>
                    
                           
                        
                                  
                                                    <td><input type="text" name="popupITEMID_0" id="popupITEMID_0" class="form-control"  autocomplete="off"  readonly/></td>
                                                    <td hidden><input type="hidden" name="ITEMID_REF_0" id="ITEMID_REF_0" class="form-control" autocomplete="off" /></td>
                                                    <td><input type="text" name="ItemName_0" id="ItemName_0" class="form-control"  autocomplete="off"  readonly/></td>
                    <td style="text-align:center;">            
        
                           <input type="text" name="CID_REF_0" id="CID_REF_0" onClick="get_customer($(this).attr('id'))" class="form-control mandatory" style="width:91px" readonly tabindex="1" />                       
                            </td>
                           <td hidden> <input type="text" name="CUSTOMERID_REF_0" id="CUSTOMERID_REF_0" /></td>
                                                    <td><input type="text" name="ItemPartno_0" id="ItemPartno_0" class="form-control three-digits" maxlength="13" autocomplete="off" style="width: 82px;" readonly/></td>
                                                    <td><input type="text" name="itemuom_0" id="itemuom_0" class="form-control"  autocomplete="off"  readonly/></td>
                                                    <td><input type="text" name="Itemspec_0" id="Itemspec_0" class="form-control"  autocomplete="off"  /></td>
                                        
                                                    <td><input type="text" name="APRIL_QTY_0" id="APRIL_QTY_0" class="form-control three-digits" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                    <td><input type="text" name="APRIL_VALUE_0" id="APRIL_VALUE_0" class="form-control three-digits" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                    <td><input type="text" name="MAY_QTY_0" id="MAY_QTY_0" class="form-control three-digits" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                    <td><input type="text" name="MAY_VALUE_0" id="MAY_VALUE_0" class="form-control three-digits" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                    <td><input type="text" name="JUNE_QTY_0" id="JUNE_QTY_0" class="form-control three-digits" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                    <td><input type="text" name="JUNE_VALUE_0" id="JUNE_VALUE_0" class="form-control three-digits" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                    <td><input type="text" name="JULY_QTY_0" id="JULY_QTY_0" class="form-control three-digits" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                    <td><input type="text" name="JULY_VALUE_0" id="JULY_VALUE_0" class="form-control three-digits" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                    <td><input type="text" name="AUGUST_QTY_0" id="AUGUST_QTY_0" class="form-control three-digits" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                    <td><input type="text" name="AUGUST_VALUE_0" id="AUGUST_VALUE_0" class="form-control three-digits" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                    <td><input type="text" name="SEPTEMBER_QTY_0" id="SEPTEMBER_QTY_0" class="form-control three-digits" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                    <td><input type="text" name="SEPTEMBER_VALUE_0" id="SEPTEMBER_VALUE_0" class="form-control three-digits" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                    <td><input type="text" name="OCTOBER_QTY_0" id="OCTOBER_QTY_0" class="form-control three-digits" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                    <td><input type="text" name="OCTOBER_VALUE_0" id="OCTOBER_VALUE_0" class="form-control three-digits" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                    <td><input type="text" name="NOVEMBER_QTY_0" id="NOVEMBER_QTY_0" class="form-control three-digits" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                    <td><input type="text" name="NOVEMBER_VALUE_0" id="NOVEMBER_VALUE_0" class="form-control three-digits" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                    <td><input type="text" name="DECEMBER_QTY_0" id="DECEMBER_QTY_0" class="form-control three-digits" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                    <td><input type="text" name="DECEMBER_VALUE_0" id="DECEMBER_VALUE_0" class="form-control three-digits" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                    <td><input type="text" name="JANUARY_QTY_0" id="JANUARY_QTY_0" class="form-control three-digits" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                    <td><input type="text" name="JANUARY_VALUE_0" id="JANUARY_VALUE_0" class="form-control three-digits" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                    <td><input type="text" name="FEBRUARY_QTY_0" id="FEBRUARY_QTY_0" class="form-control three-digits" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                    <td><input type="text" name="FEBRUARY_VALUE_0" id="FEBRUARY_VALUE_0" class="form-control three-digits" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                    <td><input type="text" name="MARCH_QTY_0" id="MARCH_QTY_0" class="form-control three-digits" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                    <td><input type="text" name="MARCH_VALUE_0" id="MARCH_VALUE_0" class="form-control three-digits" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                    <td><input type="text" name="FY_QTY_0" id="FY_QTY_0" class="form-control three-digits" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                    <td><input type="text" name="FY_VALUE_0" id="FY_VALUE_0" class="form-control three-digits" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                                                                   





                          
									        <td align="center" ><button class="btn add material" title="add" data-toggle="tooltip" type="button" ><i class="fa fa-plus"></i></button>
                          <button class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
							

                               
                                               
                             
                                                </tr>
                                                <tr></tr>
                                            </tbody>
                                    </table>
                                    </div>	
                                </div>
                                
                             
                
                            </div>
                        </div>
		</div>
		
	</div>
	
</div>


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
        </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<!--Businessunit dropdown-->

<div id="sectionid_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='ctryidref_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Business Unit</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="country_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th>Business Unit Code</th>
            <th>Business Unit Name</th>
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
        @foreach ($objBusinessUnitList as $index=>$BusinessUnitList)
        <tr id="businesscode_{{ $BusinessUnitList->BUID }}" class="sectionmaster_tab">
          <td width="50%">{{ $BusinessUnitList->BUCODE }}
          <input type="hidden" id="txtbusinesscode_{{ $BusinessUnitList->BUID }}" data-desc="{{ $BusinessUnitList->BUCODE }}" data-descname="{{ $BusinessUnitList->BUNAME }}" value="{{ $BusinessUnitList-> BUID }}"/>
          </td>
          <td>{{ $BusinessUnitList->BUNAME }}</td>
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

<!--Customer dropdown-->

<div id="customerid_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='ctryidref1_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Customer List </p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="country_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th>Customer Code</th>
            <th>Customer Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td ><input type="text" id="customermaster_codesearch" onkeyup="searchCustomerCode()"></td>
          <td><input type="text" id="customermaster_namesearch" onkeyup="searchCustomerName()"></td>
        </tr>
        </tbody>
      </table>


      <table id="customermaster_tab" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody id="country_body">
        @foreach ($objCustomerList as $index=>$CustomerList)
        <tr id="customercode_{{ $CustomerList->CID }}" class="customermaster_tab">
          <td width="50%">{{ $CustomerList->CCODE }}
          <input type="hidden" id="txtcustomercode_{{ $CustomerList->CID }}" data-desc="{{ $CustomerList->CCODE }}" data-descname="{{ $CustomerList->NAME }}" value="{{ $CustomerList-> CID }}"/>
          </td>
          <td>{{ $CustomerList->NAME }}</td>
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

<!--DEPT dropdown-->


<div id="dept_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='dept_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Department List</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="country_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th>Department Code</th>
            <th>Department Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td ><input type="text" id="dept_codesearch" onkeyup="searchdeptCode()"></td>
          <td><input type="text" id="dept_namesearch" onkeyup="searchdeptName()"></td>
        </tr>
        </tbody>
      </table>


      <table id="dept_tab" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody id="dept_body">
        @foreach ($objDepartmentList as $index=>$DepartmentList)
        <tr id="deptcode_{{ $DepartmentList->DEPID }}" class="cls_dept">
          <td width="50%">{{ $DepartmentList->DCODE }}
          <input type="hidden" id="txtdeptcode_{{ $DepartmentList->DEPID }}" data-desc="{{ $DepartmentList->DCODE }}" data-descname="{{ $DepartmentList->NAME }}" value="{{ $DepartmentList-> DEPID }}"/>
          </td>
          <td>{{ $DepartmentList->NAME }}</td>
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
<!--FINANCIAL dropdown-->


<div id="fy_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='fy_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Financial Year List</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="fy_table1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th>Financial Year Code</th>
            <th>Financial Year Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td ><input type="text" id="fy_codesearch" onkeyup="searchfyCode()"></td>
          <td><input type="text" id="fy_namesearch" onkeyup="searchfyName()"></td>
        </tr>
        </tbody>
      </table>


      <table id="fy_table2" class="display nowrap table  table-striped table-bordered" width="100%">
      <thead id="thead2">
          
          </thead>
        <tbody id="fy_body">
        @foreach ($objFyearList as $index=>$FiancialList)
        <tr id="fycode_{{ $FiancialList->FYID }}" class="cls_fyear">
          <td width="50%">{{ $FiancialList->FYCODE }}
          <input type="hidden" id="txtfycode_{{ $FiancialList->FYID }}" data-desc="{{ $FiancialList->FYCODE }}" data-descname="{{ $FiancialList->FYDESCRIPTION }}" value="{{ $FiancialList->FYID }}"/>
          </td>
          <td>{{ $FiancialList->FYDESCRIPTION }}</td>
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



<!-- Item Code Dropdown -->
<div id="ITEMIDpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:90%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='ITEMID_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Item Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="ItemIDTable" class="display nowrap table  table-striped table-bordered"  style="width:100%" >
    <thead>
      <tr id="none-select" class="searchalldata" text>
            
            <td> <input type="hidden" name="fieldid" id="hdn_ItemID"/>
            <input type="hidden" name="fieldid2" id="hdn_ItemID2"/>
            <input type="hidden" name="fieldid3" id="hdn_ItemID3"/>
            <input type="hidden" name="fieldid4" id="hdn_ItemID4"/>
            <input type="hidden" name="fieldid5" id="hdn_ItemID5"/>
            <input type="hidden" name="fieldid6" id="hdn_ItemID6"/>

     
            </td>
      </tr>
     
      <tr>
            <th style="width:5%;" id="all-check" >Select</th>
            <th style="width:10%;">Item Code</th>
            <th style="width:8%;">Name</th>
            <th style="width:8%;">Part No</th>
            <th style="width:8%;">Main UOM</th>
            <th style="width:8%;">Main QTY</th>
            <th style="width:8%;">Item Group</th>
            <th style="width:8%;">Item Category</th>
            <th style="width:8%;">Business Unit</th>
            <th style="width:8%;">{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</th>
            <th style="width:8%;">{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</th>
            <th style="width:8%;">{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</th>
            <th style="width:5%;">Status</th>
      </tr>
    </thead>
    <tbody>
    <tr>
    <td style="width:5%;text-align:center;"><input type="checkbox" class="js-selectall" data-target=".js-selectall1" /></td>
    <td style="width:10%;">
    <input type="text" id="Itemcodesearch" class="form-control" onkeyup="ItemCodeFunction()">
    </td>
    <td style="width:8%;">
    <input type="text" id="Itemnamesearch" class="form-control" onkeyup="ItemNameFunction()">
    </td>
    <td style="width:8%;">
    <input type="text" id="Itempartsearch" class="form-control" onkeyup="ItemPartnoFunction()">
    </td>
    <td style="width:8%;">
    <input type="text" id="ItemUOMsearch" class="form-control" onkeyup="ItemUOMFunction()">
    </td>
    <td style="width:8%;">
    <input type="text" id="ItemQTYsearch"  class="form-control" onkeyup="ItemQTYFunction()">
    </td>
    <td style="width:8%;">
    <input type="text" id="ItemGroupsearch" class="form-control" onkeyup="ItemGroupFunction()">
    </td>
    <td style="width:8%;">
    <input type="text" id="ItemCategorysearch" class="form-control" onkeyup="ItemCategoryFunction()">
    </td>

    <td style="width:8%;"><input type="text" id="ItemBUsearch" class="form-control" onkeyup="ItemBUFunction()"></td>
    <td style="width:8%;"><input type="text" id="ItemAPNsearch" class="form-control" onkeyup="ItemAPNFunction()"></td>
    <td style="width:8%;"><input type="text" id="ItemCPNsearch" class="form-control" onkeyup="ItemCPNFunction()"></td>
    <td style="width:8%;"><input type="text" id="ItemOEMPNsearch" class="form-control" onkeyup="ItemOEMPNFunction()"></td>

    <td style="width:5%;">
    <input type="text" id="ItemStatussearch" class="form-control" onkeyup="ItemStatusFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="ItemIDTable2" class="display nowrap table  table-striped table-bordered" style="width:100%" >
        <thead id="thead2">

        </thead>
        <tbody id="tbody_ItemID">     
          
          
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Item Code Dropdown-->


@endsection


@push('bottom-css')
<style>
#custom_dropdown, #udfforsemst_filter {
    display: inline-table;
    margin-left: 15px;
}
.dataTables_wrapper .row:nth-child(1) .col-sm-6:nth-child(2){text-align:right;}
#filtercolumn{color: #555;
    background-color: #fff;
    background-image: none;
    border: 1px solid #ccc;
    }


#ItemIDcodesearch {
  background-image: url('/css/searchicon.png');
  background-position: 10px 10px;
  background-repeat: no-repeat;
  font-size: 11px;
  padding: 5px 5px 5px 5px;
  border: 1px solid #ddd;
  margin-bottom: 5px;
}
#ItemIDnamesearch {
  background-image: url('/css/searchicon.png');
  background-position: 10px 10px;
  background-repeat: no-repeat;
  font-size: 11px;
  padding: 5px 5px 5px 5px;
  border: 1px solid #ddd;
  margin-bottom: 5px;
}

#ItemIDTable {
  border-collapse: collapse;
  width: 950px;
  border: 1px solid #ddd;
  font-size: 11px;
}

#ItemIDTable th {
    text-align: center;
    padding: 5px;
    font-size: 11px;

    font-weight: 600;
}

#ItemIDTable td {
    text-align: center;
    padding: 5px;
    font-size: 11px;
    font-weight: 600;
}

#ItemIDTable2 {
  border-collapse: collapse;
  width: 1050px;
  border: 1px solid #ddd;
  font-size: 11px;
}

#ItemIDTable2 th{
    text-align: left;
    padding: 5px;
    font-size: 11px;

    font-weight: 600;
}

#ItemIDTable2 td {
  text-align: left;
    padding: 5px;
    font-size: 11px;
    font-weight: 600;
    width: 16%;
}

</style>
@endpush
@push('bottom-scripts')
<script>

//------------------------
  //GL Account
  let tid = "#GlCodeTable2";
      let tid2 = "#GlCodeTable";
      let headers = document.querySelectorAll(tid2 + " th");

      // Sort the table element when clicking on the table headers
      headers.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(tid, ".clsglid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function GLCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("glcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("GlCodeTable2");
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

  function GLNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("glnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("GlCodeTable2");
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

  $('#txtgl_popup').click(function(event){
         $("#glidpopup").show();
         event.preventDefault();
      });

      $("#gl_closePopup").click(function(event){
        $("#glidpopup").hide();
        event.preventDefault();
      });

      $(".clsglid").dblclick(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");
        
        $('#txtgl_popup').val(texdesc);
        $('#GLID_REF').val(txtval);
        $("#glidpopup").hide();
        $("#glcodesearch").val(''); 
        $("#glnamesearch").val(''); 
        GLCodeFunction();
        //sub GL
        var customid = txtval;
        if(customid!=''){
          $("#txtsubgl_popup").val('');
          $("#SLID_REF").val('');
          $('#tbody_subglacct').html('<tr><td colspan="2">Please wait..</td></tr>');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url:'{{route("transaction",[168,"getsubledger"])}}',
                type:'POST',
                data:{'id':customid},
                success:function(data) {
                    $('#tbody_subglacct').html(data);
                    bindSubLedgerEvents();
                },
                error:function(data){
                  console.log("Error: Something went wrong.");
                  $('#tbody_subglacct').html('');
                },
            });        
        }
        ////sub GL end
        event.preventDefault();
      });

      

  //GL Account Ends
//------------------------
//Sub GL Account Starts
//------------------------

      let sgltid = "#SubGLTable2";
      let sgltid2 = "#SubGLTable";
      let sglheaders = document.querySelectorAll(sgltid2 + " th");

      // Sort the table element when clicking on the table headers
      sglheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(sgltid, ".clssubgl", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function SubGLCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("subglcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("SubGLTable2");
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

  function SubGLNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("subglnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("SubGLTable2");
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

$("#txtsubgl_popup").click(function(event){
     $("#subglpopup").show();
     event.preventDefault();
  });

$("#subgl_closePopup").on("click",function(event){ 
    $("#subglpopup").hide();
    event.preventDefault();
});
function bindSubLedgerEvents(){
        $('.clssubgl').dblclick(function(){
    
            var id = $(this).attr('id');
            var txtval =    $("#txt"+id+"").val();
            var texdesc =   $("#txt"+id+"").data("desc");
            var oldSLID =   $("#AFSDT").val();
            var MaterialClone = $('#hdnmaterial').val();
            $("#txtsubgl_popup").val(texdesc);
            $("#txtsubgl_popup").blur();
            $("#SLID_REF").val(txtval);
            if (txtval != oldSLID)
            {
                $('#Material').html(MaterialClone);
                $('#Row_Count1').val('1');
            }
            $("#subglpopup").hide();
            $("#subglcodesearch").val(''); 
            $("#subglnamesearch").val(''); 
            SubGLCodeFunction();
            
              event.preventDefault();
        });
  }
//Sub GL Account Ends
//------------------------

//------------------------
  //Sales Person Dropdown
  let sptid = "#SalesPersonTable2";
      let sptid2 = "#SalesPersonTable";
      let salespersonheaders = document.querySelectorAll(sptid2 + " th");

      // Sort the table element when clicking on the table headers
      salespersonheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(sptid, ".clsspid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function SalesPersonCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("SalesPersoncodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("SalesPersonTable2");
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

  function SalesPersonNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("SalesPersonnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("SalesPersonTable2");
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

  $('#txtSPID_popup').click(function(event){
         $("#SPIDpopup").show();
      });

      $("#SPID_closePopup").click(function(event){
        $("#SPIDpopup").hide();
      });

      $(".clsspid").dblclick(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");
        
        $('#txtSPID_popup').val(texdesc);
        $('#SPID_REF').val(txtval);
        $("#SPIDpopup").hide();
        
        $("#SalesPersoncodesearch").val(''); 
        $("#SalesPersonnamesearch").val(''); 
        SalesPersonCodeFunction();
        event.preventDefault();
      });

      

  //Sales Person Dropdown Ends
//------------------------

//------------------------
  //Enquiry Media Dropdown
  let emtid = "#EMCodeTable2";
      let emtid2 = "#EMCodeTable";
      let emheaders = document.querySelectorAll(emtid2 + " th");

      // Sort the table element when clicking on the table headers
      emheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(emtid, ".clsemid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function EMCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("emcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("EMCodeTable2");
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

  function EMNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("emnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("EMCodeTable2");
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

  $('#EMID_popup').click(function(event){
         $("#emidpopup").show();
      });

      $("#em_closePopup").click(function(event){
        $("#emidpopup").hide();
      });

      $(".clsemid").dblclick(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");
        
        $('#EMID_popup').val(texdesc);
        $('#EMID_REF').val(txtval);
        $("#emidpopup").hide();
        
        $("#emcodesearch").val(''); 
        $("#emnamesearch").val(''); 
        EMCodeFunction();
        event.preventDefault();
      });

      

  //Enquiry Media Dropdown Ends
//------------------------

//------------------------
  //Priority Media Dropdown
  let prtid = "#PriorityTable2";
      let prtid2 = "#PriorityTable";
      let prheaders = document.querySelectorAll(prtid2 + " th");

      // Sort the table element when clicking on the table headers
      prheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(prtid, ".clsprid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function PriorityCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Prioritycodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("PriorityTable2");
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

  function PriorityNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Prioritynamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("PriorityTable2");
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

  $('#PRIORITYID_popup').click(function(event){
         $("#Prioritypopup").show();
      });

      $("#Priority_closePopup").click(function(event){
        $("#Prioritypopup").hide();
      });

      $(".clsprid").dblclick(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");
        
        $('#PRIORITYID_popup').val(texdesc);
        $('#PRIORITYID_REF').val(txtval);
        $("#Prioritypopup").hide();
        
        $("#Prioritycodesearch").val(''); 
        $("#Prioritynamesearch").val(''); 
        PriorityCodeFunction();
        event.preventDefault();
      });      

  //Priority Dropdown Ends
//------------------------

//------------------------
  //Item ID Dropdown
  let itemtid = "#ItemIDTable2";
      let itemtid2 = "#ItemIDTable";
      let itemtidheaders = document.querySelectorAll(itemtid2 + " th");

      // Sort the table element when clicking on the table headers
      itemtidheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(itemtid, ".clsitemid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function ItemCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Itemcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("ItemIDTable2");
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

      function ItemNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Itemnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("ItemIDTable2");
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


      function ItemPartnoFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Itempartsearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("ItemIDTable2");
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

      function ItemUOMFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("ItemUOMsearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("ItemIDTable2");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
          td = tr[i].getElementsByTagName("td")[3];
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
      function ItemQTYFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("ItemQTYsearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("ItemIDTable2");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
          td = tr[i].getElementsByTagName("td")[4];
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

      function ItemGroupFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("ItemGroupsearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("ItemIDTable2");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
          td = tr[i].getElementsByTagName("td")[5];
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

      function ItemCategoryFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("ItemCategorysearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("ItemIDTable2");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
          td = tr[i].getElementsByTagName("td")[6];
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

      function ItemBUFunction() {
	var input, filter, table, tr, td, i, txtValue;
	input = document.getElementById("ItemBUsearch");
	filter = input.value.toUpperCase();
	table = document.getElementById("ItemIDTable2");
	tr = table.getElementsByTagName("tr");
	for (i = 0; i < tr.length; i++) {
	  td = tr[i].getElementsByTagName("td")[7];
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

function ItemAPNFunction() {
	var input, filter, table, tr, td, i, txtValue;
	input = document.getElementById("ItemAPNsearch");
	filter = input.value.toUpperCase();
	table = document.getElementById("ItemIDTable2");
	tr = table.getElementsByTagName("tr");
	for (i = 0; i < tr.length; i++) {
	  td = tr[i].getElementsByTagName("td")[8];
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

function ItemCPNFunction() {
	var input, filter, table, tr, td, i, txtValue;
	input = document.getElementById("ItemCPNsearch");
	filter = input.value.toUpperCase();
	table = document.getElementById("ItemIDTable2");
	tr = table.getElementsByTagName("tr");
	for (i = 0; i < tr.length; i++) {
	  td = tr[i].getElementsByTagName("td")[9];
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

function ItemOEMPNFunction() {
	var input, filter, table, tr, td, i, txtValue;
	input = document.getElementById("ItemOEMPNsearch");
	filter = input.value.toUpperCase();
	table = document.getElementById("ItemIDTable2");
	tr = table.getElementsByTagName("tr");
	for (i = 0; i < tr.length; i++) {
	  td = tr[i].getElementsByTagName("td")[10];
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

      function ItemStatusFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("ItemStatussearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("ItemIDTable2");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
          td = tr[i].getElementsByTagName("td")[7];
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

  $('#Material').on('click','[id*="popupITEMID"]',function(event){
    var BU_NO = $(this).parent().parent().find('[id*="REF_BUID"]').val();
    if(BU_NO ===""){
          showAlert('Please select Business Unit.');
        }else{


                
                $("#tbody_ItemID").html('');
                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  });
                  $.ajax({
                      url:'{{route("transaction",[168,"getItemDetails"])}}',
                      type:'POST',
                      data:{'status':'A',BU_NO:BU_NO},
        
                      success:function(data) {
                        $("#tbody_ItemID").html(data);    
                        bindItemEvents();   
                        $('.js-selectall').prop('disabled', true);                     
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                        $("#tbody_ItemID").html('');                        
                      },
                  }); 
        

        $("#ITEMIDpopup").show();
        var id = $(this).attr('id');
        var id2 = $(this).parent().parent().find('[id*="ITEMID_REF"]').attr('id');
        var id3 = $(this).parent().parent().find('[id*="ItemName"]').attr('id');
        var id4 = $(this).parent().parent().find('[id*="ItemPartno"]').attr('id');
        var id5 = $(this).parent().parent().find('[id*="Itemspec"]').attr('id');
        var id6 = $(this).parent().parent().find('[id*="itemuom"]').attr('id');



  }

        $('#hdn_ItemID').val(id);
        $('#hdn_ItemID2').val(id2);
        $('#hdn_ItemID3').val(id3);
        $('#hdn_ItemID4').val(id4);
        $('#hdn_ItemID5').val(id5);
        $('#hdn_ItemID6').val(id6);
     
       
        event.preventDefault();
      });

      $("#ITEMID_closePopup").click(function(event){
        $("#ITEMIDpopup").hide();
      });

    function bindItemEvents(){

      $('#ItemIDTable2').off(); 

      $('[id*="chkId"]').change(function(){
  
     
        var fieldid = $(this).parent().parent().attr('id');
        var txtval =   $("#txt"+fieldid+"").val();
        var texdesc =  $("#txt"+fieldid+"").data("desc");
        var fieldid2 = $(this).parent().parent().children('[id*="itemname"]').attr('id');
        var txtname =  $("#txt"+fieldid2+"").val();
        var txtspec =  $("#txt"+fieldid2+"").data("desc");

   
        var fieldid7 = $(this).parent().parent().children('[id*="itempartno"]').attr('id');
        var txtpartno =  $("#txt"+fieldid7+"").val();

        var fieldid3 = $(this).parent().parent().children('[id*="itemuom"]').attr('id');
        var txtmuomid =  $("#txt"+fieldid3+"").val();

    


       
        
        

       

       if($(this).is(":checked") == true) 
       {
     

        $('#example2').find('.participantRow').each(function()
         {
     

           var itemid = $(this).find('[id*="ITEMID_REF"]').val();


    
           if(txtval)
           {
                if(txtval == itemid)
                {
                  $("#ITEMIDpopup").hide();
                      $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn").hide();
                      $("#OkBtn1").show();
                      $("#AlertMessage").text('Item already exists.');
                      $("#alert").modal('show');
                      $("#OkBtn1").focus();
                      highlighFocusBtn('activeOk1');
                      $('#hdn_ItemID').val('');
                      $('#hdn_ItemID2').val('');
                      $('#hdn_ItemID3').val('');
                      $('#hdn_ItemID4').val('');
                      $('#hdn_ItemID5').val('');
                      $('#hdn_ItemID6').val('');

                      txtval = '';
                      texdesc = '';
                      txtname = '';
                      txtpartno = '';
                      txtspec = '';   
                               
                      txtmuomid = '';
                      return false;
                      
                }   
               
           }          
        });
                      if($('#hdn_ItemID').val() == "" && txtval != '')
                      {
                        var txtid= $('#hdn_ItemID').val();
                        var txt_id2= $('#hdn_ItemID2').val();
                        var txt_id3= $('#hdn_ItemID3').val();
                        var txt_id4= $('#hdn_ItemID4').val();
                        var txt_id5= $('#hdn_ItemID5').val();
                        var txt_id6= $('#hdn_ItemID6').val();
               
   
                      
                        

                        var $tr = $('.material').closest('table');
                        var allTrs = $tr.find('.participantRow').last();
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

                        $clone.find('.remove').removeAttr('disabled'); 
                        $clone.find('[id*="popupITEMID"]').val(texdesc);
                        $clone.find('[id*="ITEMID_REF"]').val(txtval);
                        $clone.find('[id*="ItemName"]').val(txtname);
                        $clone.find('[id*="ItemPartno"]').val(txtpartno);
                        $clone.find('[id*="Itemspec"]').val(txtspec);
                        $clone.find('[id*="itemuom"]').val(txtmuomid);
                  

             
                        
                        $tr.closest('table').append($clone);   
                        var rowCount = $('#Row_Count1').val();
                          rowCount = parseInt(rowCount)+1;
                          $('#Row_Count1').val(rowCount);
                         
                        event.preventDefault();
                      }
                      else
                      {
                      var txtid= $('#hdn_ItemID').val();
                      var txt_id2= $('#hdn_ItemID2').val();
                      var txt_id3= $('#hdn_ItemID3').val();
                      var txt_id4= $('#hdn_ItemID4').val();
                      var txt_id5= $('#hdn_ItemID5').val();
                      var txt_id6= $('#hdn_ItemID6').val();

       
                      $('#'+txtid).val(texdesc);
                      $('#'+txt_id2).val(txtval);
                      $('#'+txt_id3).val(txtname);
                      $('#'+txt_id4).val(txtpartno);
                      $('#'+txt_id5).val(txtspec);
                      $('#'+txt_id6).val(txtmuomid);
  
               
                      
                      
                      $('#hdn_ItemID').val('');
                      $('#hdn_ItemID2').val('');
                      $('#hdn_ItemID3').val('');
                      $('#hdn_ItemID4').val('');
                      $('#hdn_ItemID5').val('');
                      $('#hdn_ItemID6').val('');

                      
                      }
                      event.preventDefault();
       }
       else if($(this).is(":checked") == false) 
       {
         var id = txtval;
         var r_count = $('#Row_Count1').val();
         $('#example2').find('.participantRow').each(function()
         {
           var itemid = $(this).find('[id*="ITEMID_REF"]').val();
           if(id == itemid)
           {
              var rowCount = $('#Row_Count1').val();
              if (rowCount > 1) {
                $(this).closest('.participantRow').remove(); 
                rowCount = parseInt(rowCount)-1;
              $('#Row_Count1').val(rowCount);
              }
              else 
              {
                $(document).find('.dmaterial').prop('disabled', true);  
                $("#ITEMIDpopup").hide();
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text('There is only 1 row. So cannot be remove.');
                $("#alert").modal('show');
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk1');
                return false;

              }
                event.preventDefault(); 
           }
        });
       }
        $("#Itemcodesearch").val(''); 
        $("#Itemnamesearch").val(''); 
        $("#Itempartnosearch").val(''); 
        $("#ItemUOMsearch").val(''); 
        $("#ItemGroupsearch").val(''); 
        $("#ItemCategorysearch").val(''); 
        $("#ItemStatussearch").val(''); 
        $('.remove').removeAttr('disabled'); 
      
        event.preventDefault();
      });
    }

      

  //Item ID Dropdown Ends
//------------------------


     
$(document).ready(function(e) {
var Material = $("#Material").html(); 
$('#hdnmaterial').val(Material);


$('#btnAdd').on('click', function() {
  var viewURL = '{{route("transaction",[168,"add"])}}';
              window.location.href=viewURL;
});

$('#btnExit').on('click', function() {
  var viewURL = '{{route('home')}}';
              window.location.href=viewURL;
});




//delete row
$("#Material").on('click', '.remove', function() {
    var rowCount = $(this).closest('table').find('.participantRow').length;
    if (rowCount > 1) {
    $(this).closest('.participantRow').remove();     
    } 
    if (rowCount <= 1) { 
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn").hide();
          $("#OkBtn1").show();
          $("#AlertMessage").text('There is only 1 row. So cannot be remove.');
          $("#alert").modal('show');
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk1');
          return false;
    }
    event.preventDefault();
});

//add row
$("#Material").on('click', '.add', function() {
  var $tr = $(this).closest('table');
  var allTrs = $tr.find('.participantRow').last();
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
  $clone.find('input:hidden').val('');
  var d = new Date(); 
  var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
  $clone.find('[id*="EDD"]').val(today);
  $tr.closest('table').append($clone);         
  var rowCount1 = $('#Row_Count1').val();
  rowCount1 = parseInt(rowCount1)+1;
  $('#Row_Count1').val(rowCount1);
  $clone.find('.remove').removeAttr('disabled'); 
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
});

    

window.fnUndoYes = function (){
    //reload form
  window.location.href = "{{route('transaction',[168,'add'])}}";
}//fnUndoYes


window.fnUndoNo = function (){
    $("#AFSNO").focus();
}//fnUndoNo

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

  
    $('#example2').on('blur','[id*="APRIL_QTY"]',function(){
            if(intRegex.test($(this).val())){
             $(this).val($(this).val()+'.000')
            }
            event.preventDefault();
        });
        
    $('#example2').on('blur','[id*="APRIL_VALUE"]',function(){
            if(intRegex.test($(this).val())){
             $(this).val($(this).val()+'.00')
            }
            event.preventDefault();
        });

        
    $('#example2').on('blur','[id*="MAY_QTY"]',function(){
            if(intRegex.test($(this).val())){
             $(this).val($(this).val()+'.000')
            }
            event.preventDefault();
        });
        
    $('#example2').on('blur','[id*="MAY_VALUE"]',function(){
            if(intRegex.test($(this).val())){
             $(this).val($(this).val()+'.00')
            }
            event.preventDefault();
        });

        
    $('#example2').on('blur','[id*="JUNE_QTY"]',function(){
            if(intRegex.test($(this).val())){
             $(this).val($(this).val()+'.000')
            }
            event.preventDefault();
        });
       
    $('#example2').on('blur','[id*="JUNE_VALUE"]',function(){
            if(intRegex.test($(this).val())){
             $(this).val($(this).val()+'.00')
            }
            event.preventDefault();
        });

        
    $('#example2').on('blur','[id*="JULY_QTY"]',function(){
            if(intRegex.test($(this).val())){
             $(this).val($(this).val()+'.000')
            }
            event.preventDefault();
        });
        
    $('#example2').on('blur','[id*="JULY_VALUE"]',function(){
            if(intRegex.test($(this).val())){
             $(this).val($(this).val()+'.00')
            }
            event.preventDefault();
        });

        
    $('#example2').on('blur','[id*="AUGUST_QTY"]',function(){
            if(intRegex.test($(this).val())){
             $(this).val($(this).val()+'.000')
            }
            event.preventDefault();
        });
        
    $('#example2').on('blur','[id*="AUGUST_VALUE"]',function(){
            if(intRegex.test($(this).val())){
             $(this).val($(this).val()+'.00')
            }
            event.preventDefault();
        });

        
    $('#example2').on('blur','[id*="SEPTEMBER_QTY"]',function(){
            if(intRegex.test($(this).val())){
             $(this).val($(this).val()+'.000')
            }
            event.preventDefault();
        });
        
    $('#example2').on('blur','[id*="SEPTEMBER_VALUE"]',function(){
            if(intRegex.test($(this).val())){
             $(this).val($(this).val()+'.00')
            }
            event.preventDefault();
        });

       
    $('#example2').on('blur','[id*="OCTOBER_QTY"]',function(){
            if(intRegex.test($(this).val())){
             $(this).val($(this).val()+'.000')
            }
            event.preventDefault();
        });
        
    $('#example2').on('blur','[id*="OCTOBER_VALUE"]',function(){
            if(intRegex.test($(this).val())){
             $(this).val($(this).val()+'.00')
            }
            event.preventDefault();
        });

        
    $('#example2').on('blur','[id*="NOVEMBER_QTY"]',function(){
            if(intRegex.test($(this).val())){
             $(this).val($(this).val()+'.000')
            }
            event.preventDefault();
        });
       
    $('#example2').on('blur','[id*="NOVEMBER_VALUE"]',function(){
            if(intRegex.test($(this).val())){
             $(this).val($(this).val()+'.00')
            }
            event.preventDefault();
        });

        
    $('#example2').on('blur','[id*="DECEMBER_QTY"]',function(){
            if(intRegex.test($(this).val())){
             $(this).val($(this).val()+'.000')
            }
            event.preventDefault();
        });
        
    $('#example2').on('blur','[id*="DECEMBER_VALUE"]',function(){
            if(intRegex.test($(this).val())){
             $(this).val($(this).val()+'.00')
            }
            event.preventDefault();
        });

       
    $('#example2').on('blur','[id*="JANUARY_QTY"]',function(){
            if(intRegex.test($(this).val())){
             $(this).val($(this).val()+'.000')
            }
            event.preventDefault();
        });
        
    $('#example2').on('blur','[id*="JANUARY_VALUE"]',function(){
            if(intRegex.test($(this).val())){
             $(this).val($(this).val()+'.00')
            }
            event.preventDefault();
        });

        
    $('#example2').on('blur','[id*="FEBRUARY_QTY"]',function(){
            if(intRegex.test($(this).val())){
             $(this).val($(this).val()+'.000')
            }
            event.preventDefault();
        });
        
    $('#example2').on('blur','[id*="FEBRUARY_VALUE"]',function(){
            if(intRegex.test($(this).val())){
             $(this).val($(this).val()+'.00')
            }
            event.preventDefault();
        });

        
    $('#example2').on('blur','[id*="MARCH_QTY"]',function(){
            if(intRegex.test($(this).val())){
             $(this).val($(this).val()+'.000')
            }
            event.preventDefault();
        });
        
    $('#example2').on('blur','[id*="MARCH_VALUE"]',function(){
            if(intRegex.test($(this).val())){
             $(this).val($(this).val()+'.00')
            }
            event.preventDefault();
        });

        
    $('#example2').on('blur','[id*="FY_QTY"]',function(){
            if(intRegex.test($(this).val())){
             $(this).val($(this).val()+'.000')
            }
            event.preventDefault();
        });
        
    $('#example2').on('blur','[id*="FY_VALUE"]',function(){
            if(intRegex.test($(this).val())){
             $(this).val($(this).val()+'.00')
            }
            event.preventDefault();
        });



  $("#btnSaveSE").on("submit", function( event ) {

    if ($("#frm_trn_se").valid()) {

        // Do something
        alert( "Handler for .submit() called." );
        event.preventDefault();
    }
});


    $('#frm_trn_se1').bootstrapValidator({
       
        fields: {
            txtlabel: {
                validators: {
                    notEmpty: {
                        message: 'The Enquiry Number is required'
                    }
                }
            },            
        },
        submitHandler: function(validator, form, submitButton) {
            alert( "Handler for .submit() called." );
             event.preventDefault();
             $("#frm_trn_se").submit();
        }
    });
});



function validateForm(){
 
 $("#FocusId").val('');
 var AFSNO          =   $.trim($("#AFSNO").val());
 var AFSDT          =   $.trim($("#AFSDT").val());
 var DEPID_REF          =   $.trim($("#DEPID_REF").val());
 var FYID_REF          =   $.trim($("#FYID_REF").val());


 if(AFSNO ===""){
     $("#FocusId").val($("#AFSNO"));
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please enter value in AFS Number.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if(AFSDT ===""){
     $("#FocusId").val($("#AFSDT"));
     $("#AFSDT").val();  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please select AFS Date.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }  

 else if(DEPID_REF ===""){
  $("#FocusId").val($("#DEPID_REF"));
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please select Department.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if(FYID_REF ===""){
  $("#FocusId").val($("#FYID_REF"));
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show(); 
     $("#AlertMessage").text('Please select Financial Year.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else{

    event.preventDefault();
    var allblank = [];
    var allblank2 = [];
    var allblank3 = [];
    

       
          $('#example2').find('.participantRow').each(function(){
          if($.trim($(this).find("[id*=BUID_REF]").val())!=""){
           allblank.push('true');
               }
          else{
                allblank.push('false');
            } 


    if($.trim($(this).find("[id*=popupITEMID]").val())!=""){

        allblank2.push('true');
    }
    else{
                allblank2.push('false');
            } 
    if($.trim($(this).find("[id*=CID_REF]").val())!=""){

        allblank3.push('true');

    }
    else{
                allblank3.push('false');
            } 

        }); }


        if(jQuery.inArray("false", allblank) !== -1){
          $("#alert").modal('show');
          $("#AlertMessage").text('Please select Business Unit in Material Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
          }
        else if(jQuery.inArray("false", allblank2) !== -1){
          $("#alert").modal('show');
          $("#AlertMessage").text('Please select Item in Material Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
          }  
        else if(jQuery.inArray("false", allblank3) !== -1){
          $("#alert").modal('show');
          $("#AlertMessage").text('Please select Customer in Material Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
          }  
          else if(checkPeriodClosing(168,$("#AFSDT").val(),0) ==0){
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text(period_closing_msg);
            $("#alert").modal('show');
            $("#OkBtn1").focus();
          }
      
   
          else{
                $("#alert").modal('show');
                $("#AlertMessage").text('Do you want to save to record.');
                $("#YesBtn").data("funcname","fnSaveData");  
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

$("#btnSaveSE" ).click(function() {
    var formReqData = $("#frm_trn_se");
    if(formReqData.valid()){
      validateForm();
    }
});

window.fnSaveData = function (){

//validate and save data
event.preventDefault();

     var trnseForm = $("#frm_trn_se");
    var formData = trnseForm.serialize();
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$("#btnSaveSE").hide(); 
$(".buttonload").show(); 
$.ajax({
    url:'{{ route("transaction",[168,"save"])}}',
    type:'POST',
    data:formData,
    success:function(data) {
      $(".buttonload").hide(); 
      $("#btnSaveSE").show();  
       
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
           if(data.country=='norecord') {

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
           

            $("#alert").modal('show');
            $("#OkBtn").focus();
            
        }
        else if(data.cancel) {                   
            console.log("cancel MSG="+data.msg);
            
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").show();

            $("#AlertMessage").text(data.msg);

            $(".text-danger").hide();
            

            $("#alert").modal('show');
            $("#OkBtn1").focus();
            
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
      $(".buttonload").hide(); 
      $("#btnSaveSE").show();  
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
function showAlert(msg){
  $("#alert").modal('show');
  $("#AlertMessage").text(msg);
  $("#YesBtn").hide(); 
  $("#NoBtn").hide();  
  $("#OkBtn1").show();
  $("#OkBtn1").focus();
  highlighFocusBtn('activeOk');
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
    window.location.href = '{{route("transaction",[168,"index"]) }}';
});

$("#OkBtn1").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn").hide();
    $("#OkBtn1").hide();
    $("#"+$(this).data('focusname')).focus();
   
    $(".text-danger").hide();
});


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
    
    $('.AFSDT').attr('min', minDate);
});

// Business Unit popup function
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
   var sectionid_ref="#BUID_REF_"+id_numbers; 
   var buid="#REF_BUID_"+id_numbers; 


   var id          =   $(this).attr('id');

 var txtval      =   $("#txt"+id+"").val();
 var texdesc     =   $("#txt"+id+"").data("desc");
 var texdescname =   $("#txt"+id+"").data("descname");
 




 $(buid).val(txtval);
 $(sectionid_ref).val(texdesc);
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


// Customer popup function
function get_customer(id){
   
   var result = id.split('_');
   var id_number=result[2];
   var popup_id='#'+id;
   $(".customermaster_tab").val(popup_id);    

 $("#customerid_popup").show();
 $("#CUSTOMERID_POPUP").keyup(function(event){
 if(event.keyCode==13){
   $("#customerid_popup").show();
 }
});

$("#ctryidref1_close").on("click",function(event){ 
 $("#customerid_popup").hide();
});

$('.customermaster_tab').dblclick(function(){

   var value= $(".customermaster_tab").val()
   var result = value.split('_');
   var id_numbers=result[2];
   var sectionid_ref="#CID_REF_"+id_numbers; 
   var customer_id="#CUSTOMERID_REF_"+id_numbers;




   var id          =   $(this).attr('id');

 var txtval      =   $("#txt"+id+"").val();
 var texdesc     =   $("#txt"+id+"").data("desc");
 var texdescname =   $("#txt"+id+"").data("descname");


 $(customer_id).val(txtval);
 $(sectionid_ref).val(texdesc);
 $("#customerid_popup").hide();

});
}

function searchCustomerCode() {
 var input, filter, table, tr, td, i, txtValue;
 input = document.getElementById("customermaster_codesearch");
 filter = input.value.toUpperCase();
 table = document.getElementById("customermaster_tab");
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

function searchCustomerName() {
     var input, filter, table, tr, td, i, txtValue;
     input = document.getElementById("customermaster_namesearch");
     filter = input.value.toUpperCase();
     table = document.getElementById("customermaster_tab");
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





// DEPT popup function

$("#DEPTID_NAME").on("click",function(event){ 

  $("#dept_popup").show();
});

$("#DEPTID_NAME").keyup(function(event){
  if(event.keyCode==13){
    $("#DEPTID_NAME").show();
  }
});

$("#dept_close").on("click",function(event){ 
  $("#dept_popup").hide();
});

$('.cls_dept').dblclick(function(){
  var id          =   $(this).attr('id');
  var txtval      =   $("#txt"+id+"").val();
  var texdesc     =   $("#txt"+id+"").data("desc");
  var texdescname =   $("#txt"+id+"").data("descname");

  $("#DEPTID_NAME").val(texdescname);
  $("#DEPID_REF").val(txtval);

 
  
  $("#DEPTID_NAME").blur(); 
  $("#STID_REF_POPUP").focus(); 
  
  $("#dept_popup").hide();

  event.preventDefault();
});


function searchdeptCode() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("dept_codesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("dept_tab");
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


function searchdeptName() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("dept_namesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("dept_tab");
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


// FINANCE popup function

$("#FYID_NAME").on("click",function(event){ 

$("#fy_popup").show();
});

$("#FYID_NAME").keyup(function(event){
if(event.keyCode==13){
  $("#FYID_NAME").show();
}
});

$("#fy_close").on("click",function(event){ 
$("#fy_popup").hide();
});

$('.cls_fyear').dblclick(function(){
var fieldid          =   $(this).attr('id');
var txtval      =   $("#txt"+fieldid+"").val();
var texdesc     =   $("#txt"+fieldid+"").data("desc");
var texdescname =   $("#txt"+fieldid+"").data("descname");


$("#FYID_NAME").val(texdescname);
$("#FYID_REF").val(txtval);



$("#FYID_NAME").blur(); 

$("#fy_popup").hide();

event.preventDefault();
});


let fyear = "#fy_table2";
      let fyearid2 = "#fy_table1";
      let fyearheaders = document.querySelectorAll(fyearid2 + " th");

      // Sort the table element when clicking on the table headers
      fyearheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(fyear, ".cls_fyear", "td:nth-child(" + (i + 1) + ")");
        });
      });

function searchfyCode() {
var input, filter, table, tr, td, i, txtValue;
input = document.getElementById("fy_codesearch");
filter = input.value.toUpperCase();
table = document.getElementById("fy_table2");
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


function searchfyName() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("fy_namesearch");
    filter = input.value.toUpperCase();
    table = document.getElementById("fy_table2");
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


@endpush