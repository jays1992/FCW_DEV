
@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('transaction',[168,'index'])}}" class="btn singlebt">Annual Forecast Sales Amendment (AFSA)</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
                        <button class="btn topnavbt" id="btnSaveSE" ><i class="fa fa-floppy-o"></i> Save</button>
                        <button style="display:none" class="btn topnavbt buttonload"> <i class="fa fa-refresh fa-spin"></i> {{Session::get('save')}}</button>
                        <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt" id="btnUndo"  ><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove" disabled><i class="fa fa-thumbs-o-up"></i> Approved</button>
                        <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                        <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                </div>
            </div><!--row-->
    </div>

<form id="frm_trn_se" onsubmit="return validateForm()"  method="POST" class="needs-validation"  >
    @csrf
    {{isset($objSE->SEQID[0]) ? method_field('PUT') : '' }}
    <div class="container-fluid filter">

<div class="inner-form">

  <div class="row">
    <div class="col-lg-1 pl"><p>AFSA No</p></div>
    <div class="col-lg-1 pl">
 
  
              <input type="text" name="AFSANO" id="AFSANO" value="{{ $ANumber  }}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase"  >
              <input type="hidden" name="AFSID_REF" id="AFSID_REF" value="{{ $objSE->AFSID }}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase"  >
           
   
      
    </div>
    
    <div class="col-lg-1 pl col-md-offset-1"><p>AFSA Date</p></div>
    <div class="col-lg-2 pl">
          <input type="date" name="AFSADT" id="AFSADT" value="{{ $objSE->AFSDT }}" class="form-control mandatory AFSDT"  placeholder="dd/mm/yyyy" >
          </div>
    
 
    
    <div class="col-lg-2 pl"><p>Reason to amend AFS</p></div>
    <div class="col-lg-4 pl">
    
      <input type="text" name="REASON_AFSA" id="REASON_AFSA" value="" class="form-control mandatory"  autocomplete="off"  style="text-transform:uppercase"  >
    </div>
  </div>
  <div class="row">
    <div class="col-lg-1 pl"><p>AFS No</p></div>
    <div class="col-lg-1 pl">
 
  
              <input type="text" name="AFSNO" id="AFSNO" value="{{ $objSE->AFSNO  }}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase"  >
           
   
      
    </div>
    
    <div class="col-lg-1 pl col-md-offset-1"><p>AFS Date</p></div>
    <div class="col-lg-2 pl">
          <input type="date" name="AFSDT" id="AFSDT" onchange="checkPeriodClosing(168,this.value,1)" value="{{ $objSE->AFSDT }}" class="form-control mandatory AFSDT"  placeholder="dd/mm/yyyy" >
          </div>
    
    <div class="col-lg-1 pl"><p> Department	</p></div>
    <div class="col-lg-2 pl">
    <input type="text" name="DEPTID_NAME" id="DEPTID_NAME" value="{{ $objSE->NAME }}"  class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase"  >
      <input type="hidden" name="DEPID_REF" id="DEPID_REF" value="{{ $objSE->DEPID_REF }}" class="form-control mandatory"  autocomplete="off"  style="text-transform:uppercase"  >
    </div>
    
    <div class="col-lg-1 pl"><p>Financial Year</p></div>
    <div class="col-lg-2 pl">
    <input type="text" name="FYID_NAME" id="FYID_NAME" value="{{ $objSE->FYDESCRIPTION }}"  class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase"  >
      <input type="hidden" name="FYID_REF" id="FYID_REF" value="{{ $objSE->FYID_REF }}" class="form-control mandatory"  autocomplete="off"  style="text-transform:uppercase"  >
    </div>
  </div>

  

  
</div>

<div class="container-fluid">

  <div class="row">
    <ul class="nav nav-tabs">
      <li class="active"><a data-toggle="tab" href="#Material">Material </a></li> 
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
                                          @if(!empty($objSEMAT))
                                @foreach($objSEMAT as $key => $row) 
                                              <tr  class="participantRow">
                                              <td style="text-align:center;">
                         
      
                         <input type="text" name={{"BUID_REF_".$key}} id={{"BUID_REF_".$key}}  value="{{$row->BUCODE}}" onClick="get_section($(this).attr('id'))" class="form-control mandatory" style="width:91px" readonly tabindex="1" />
                        
                       
                             
                         </td>
                         <td hidden> <input type="text" name={{"REF_BUID_".$key}} id={{"REF_BUID_".$key}} value="{{$row->BUID_REF}}" /></td>
                         
                      
                                                
                                                  <td><input type="text" name={{"popupITEMID_".$key}} id={{"popupITEMID_".$key}}  class="form-control" value="{{$row->ICODE}}"  autocomplete="off"  readonly/></td>
                                                  <td hidden><input type="hidden" name={{"ITEMID_REF_".$key}} id={{"ITEMID_REF_".$key}}  class="form-control" value="{{$row->ITEMID_REF}}" autocomplete="off" /></td>
                                                  <td><input type="text" name={{"ItemName_".$key}} id={{"ItemName_".$key}}  class="form-control" value="{{$row->NAME}}"  autocomplete="off"  readonly/></td>
                  <td style="text-align:center;">            
      
                         <input type="text" name={{"CID_REF_".$key}} id={{"CID_REF_".$key}}   onClick="get_customer($(this).attr('id'))" class="form-control mandatory" value="{{$row->CCODE}}" style="width:91px" readonly tabindex="1" />                       
                          </td>
                         <td hidden> <input type="text" name={{"CUSTOMERID_REF_".$key}} id={{"CUSTOMERID_REF_".$key}}  value="{{$row->CID_REF}}" /></td>
                                                  <td><input type="text" name={{"ItemPartno_".$key}} id={{"ItemPartno_".$key}}   class="form-control three-digits" value="{{$row->PARTNO}}"  autocomplete="off" style="width: 82px;" readonly/></td>
                                                  <td><input type="text" name={{"itemuom_".$key}} id={{"itemuom_".$key}}   class="form-control"  autocomplete="off" value="{{$row->UOMID_REF}}" readonly/></td>
                                                  <td><input type="text" name={{"Itemspec_".$key}} id={{"Itemspec_".$key}}  class="form-control"  value="{{$row->ITEMSPECI}}" autocomplete="off"  /></td>
                                      
                                                  <td><input type="text" name={{"APRIL_QTY_".$key}} id={{"APRIL_QTY_".$key}} class="form-control three-digits" value="{{$row->MONTH1_QTY == '.000' ? '' : $row->MONTH1_QTY}}" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                  <td><input type="text" name={{"APRIL_VALUE_".$key}} id={{"APRIL_VALUE_".$key}}  class="form-control three-digits" value="{{$row->MONTH1_VL == '.00' ? '' : $row->MONTH1_VL}}" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                  <td><input type="text" name={{"MAY_QTY_".$key}} id={{"MAY_QTY_".$key}}  class="form-control three-digits" value="{{$row->MONTH2_QTY == '.000' ? '' : $row->MONTH2_QTY}}" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                  <td><input type="text" name={{"MAY_VALUE_".$key}} id={{"MAY_VALUE_".$key}}   class="form-control three-digits" value="{{$row->MONTH2_VL == '.00' ? '' : $row->MONTH2_VL}}" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                  <td><input type="text" name={{"JUNE_QTY_".$key}} id={{"JUNE_QTY_".$key}}   class="form-control three-digits" value="{{$row->MONTH3_QTY == '.000' ? '' : $row->MONTH3_QTY}}" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                  <td><input type="text" name={{"JUNE_VALUE_".$key}} id={{"JUNE_VALUE_".$key}}  class="form-control three-digits"  value="{{$row->MONTH3_VL == '.00' ? '' : $row->MONTH3_VL}}" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                  <td><input type="text" name={{"JULY_QTY_".$key}} id={{"JULY_QTY_".$key}}  class="form-control three-digits" value="{{$row->MONTH4_QTY == '.000' ? '' : $row->MONTH4_QTY}}" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                  <td><input type="text" name={{"JULY_VALUE_".$key}} id={{"JULY_VALUE_".$key}}   class="form-control three-digits"value="{{$row->MONTH4_VL == '.00' ? '' : $row->MONTH4_VL}}" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                  <td><input type="text" name={{"AUGUST_QTY_".$key}} id={{"AUGUST_QTY_".$key}} class="form-control three-digits" value="{{$row->MONTH5_QTY == '.000' ? '' : $row->MONTH5_QTY}}" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                  <td><input type="text" name={{"AUGUST_VALUE_".$key}} id={{"AUGUST_VALUE_".$key}}  class="form-control three-digits" value="{{$row->MONTH5_VL == '.00' ? '' : $row->MONTH5_VL}}" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                  <td><input type="text" name={{"SEPTEMBER_QTY_".$key}} id={{"SEPTEMBER_QTY_".$key}}  class="form-control three-digits" value="{{$row->MONTH6_QTY == '.000' ? '' : $row->MONTH6_QTY}}" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                  <td><input type="text" name={{"SEPTEMBER_VALUE_".$key}} id={{"SEPTEMBER_VALUE_".$key}}  class="form-control three-digits" value="{{$row->MONTH6_VL == '.00' ? '' : $row->MONTH6_VL}}" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                  <td><input type="text" name={{"OCTOBER_QTY_".$key}} id={{"OCTOBER_QTY_".$key}}  class="form-control three-digits"  value="{{$row->MONTH7_QTY == '.000' ? '' : $row->MONTH7_QTY}}" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                  <td><input type="text" name={{"OCTOBER_VALUE_".$key}} id={{"OCTOBER_VALUE_".$key}}  class="form-control three-digits" value="{{$row->MONTH7_VL == '.00' ? '' : $row->MONTH7_VL}}"  maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                  <td><input type="text" name={{"NOVEMBER_QTY_".$key}} id={{"NOVEMBER_QTY_".$key}} class="form-control three-digits" value="{{$row->MONTH8_QTY == '.000' ? '' : $row->MONTH8_QTY}}" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                  <td><input type="text" name={{"NOVEMBER_VALUE_".$key}} id={{"NOVEMBER_VALUE_".$key}}  class="form-control three-digits" value="{{$row->MONTH8_VL == '.00' ? '' : $row->MONTH8_VL}}" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                  <td><input type="text" name={{"DECEMBER_QTY_".$key}} id={{"DECEMBER_QTY_".$key}} class="form-control three-digits" value="{{$row->MONTH9_QTY == '.000' ? '' : $row->MONTH9_QTY}}" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                  <td><input type="text" name={{"DECEMBER_VALUE_".$key}} id={{"DECEMBER_VALUE_".$key}}  class="form-control three-digits" value="{{$row->MONTH9_VL == '.00' ? '' : $row->MONTH9_VL}}" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                  <td><input type="text" name={{"JANUARY_QTY_".$key}} id={{"JANUARY_QTY_".$key}}  class="form-control three-digits" value="{{$row->MONTH10_QTY == '.000' ? '' : $row->MONTH10_QTY}}" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                  <td><input type="text" name={{"JANUARY_VALUE_".$key}} id={{"JANUARY_VALUE_".$key}}  class="form-control three-digits" value="{{$row->MONTH10_VL == '.00' ? '' : $row->MONTH10_VL}}" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                  <td><input type="text" name={{"FEBRUARY_QTY_".$key}} id={{"FEBRUARY_QTY_".$key}}  class="form-control three-digits" value="{{$row->MONTH11_QTY == '.000' ? '' : $row->MONTH11_QTY}}" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                  <td><input type="text" name={{"FEBRUARY_VALUE_".$key}} id={{"FEBRUARY_VALUE_".$key}}  class="form-control three-digits" value="{{$row->MONTH11_VL == '.00' ? '' : $row->MONTH11_VL}}" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                  <td><input type="text" name={{"MARCH_QTY_0".$key}} id={{"MARCH_QTY_0".$key}}  class="form-control three-digits" value="{{$row->MONTH12_QTY == '.000' ? '' : $row->MONTH12_QTY}}" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                  <td><input type="text" name={{"MARCH_VALUE_".$key}} id={{"MARCH_VALUE_".$key}}  class="form-control three-digits" value="{{$row->MONTH12_VL == '.00' ? '' : $row->MONTH12_VL}}" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                  <td><input type="text" name={{"FY_QTY_".$key}} id={{"FY_QTY_".$key}}  class="form-control three-digits" value="{{$row->FY_QTY == '.000' ? '' : $row->FY_QTY}}" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                  <td><input type="text" name={{"FY_VALUE_".$key}} id={{"FY_VALUE_".$key}}  class="form-control three-digits" value="{{$row->FY_VL == '.00' ? '' : $row->FY_VL}}" maxlength="13" style="width: 65px;" autocomplete="off"  /></td>
                                                                                                 





                        
                        <td align="center" ><button class="btn add material" title="add" data-toggle="tooltip" type="button" ><i class="fa fa-plus"></i></button>
                        <button class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
            

                             
                                             
                           
                                              </tr>
                                              <tr></tr>
                                              @endforeach 
                            @endif
                                          </tbody>
                                  </table>
                                  </div>	
                              </div>
                              
                           
              
                          </div>
                      </div>
  </div>
  
</div>

</div>
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

<!-- Alert -->
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
        <tr id="ctryidref_{{ $BusinessUnitList->BUID }}" class="sectionmaster_tab">
          <td width="50%">{{ $BusinessUnitList->BUCODE }}
          <input type="hidden" id="txtctryidref_{{ $BusinessUnitList->BUID }}" data-desc="{{ $BusinessUnitList->BUCODE }}" data-descname="{{ $BusinessUnitList->BUNAME }}" value="{{ $BusinessUnitList-> BUID }}"/>
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
        <tr id="ctryidref1_{{ $CustomerList->CID }}" class="customermaster_tab">
          <td width="50%">{{ $CustomerList->CCODE }}
          <input type="hidden" id="txtctryidref1_{{ $CustomerList->CID }}" data-desc="{{ $CustomerList->CCODE }}" data-descname="{{ $CustomerList->NAME }}" value="{{ $CustomerList-> CID }}"/>
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


<!-- Alert -->


<!-- Item Code Dropdown -->
<div id="ITEMIDpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='ITEMID_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Item Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="ItemIDTable" class="display nowrap table  table-striped table-bordered" >
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
            <th id="all-check" style="width:4%;" >Select</th>
            <th>Item Code</th>
            <th>Name</th>
            <th>Part No</th>
            <th>Main UOM</th>
            <th>Main QTY</th>
            <th>Item Group</th>
            <th>Item Category</th>
            <th>Status</th>
      </tr>
    </thead>
    <tbody>
    <tr>
    <td><input type="checkbox" class="js-selectall" data-target=".js-selectall1" /></td>
    <td>
    <input type="text" id="Itemcodesearch" onkeyup="ItemCodeFunction()">
    </td>
    <td>
    <input type="text" id="Itemnamesearch" onkeyup="ItemNameFunction()">
    </td>
    <td>
    <input type="text" id="Itempartsearch" onkeyup="ItemPartnoFunction()">
    </td>
    <td>
    <input type="text" id="ItemUOMsearch" onkeyup="ItemUOMFunction()">
    </td>
    <td>
    <input type="text" id="ItemQTYsearch" onkeyup="ItemQTYFunction()">
    </td>
    <td>
    <input type="text" id="ItemGroupsearch" onkeyup="ItemGroupFunction()">
    </td>
    <td>
    <input type="text" id="ItemCategorysearch" onkeyup="ItemCategoryFunction()">
    </td>
    <td>
    <input type="text" id="ItemStatussearch" onkeyup="ItemStatusFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="ItemIDTable2" class="display nowrap table  table-striped table-bordered" >
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
     var customid = $(this).parent().parent().find('#GLID_REF').val();
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
            var oldSLID =   $("#SLID_REF").val();
            var MaterialClone =  $('#hdnmaterial').val();

            $("#txtsubgl_popup").val(texdesc);
            $("#txtsubgl_popup").blur();
            $("#SLID_REF").val(txtval);
            if (txtval != oldSLID)
            { 
              $('#Material').html(MaterialClone);
              var count11 = <?php echo json_encode($objCount1); ?>;
              $('#Row_Count1').val(count11);
              $('#example2').find('.participantRow').each(function(){
                $(this).find('input:text').val('');
                var rowcount = $('#Row_Count1').val();
                if(rowcount > 1)
                {
                  $(this).closest('.participantRow').remove();
                  rowcount = parseInt(rowcount) - 1;
                  $('#Row_Count1').val(rowcount);
                }
              });
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
                            if(id) {
                                var i = id.substr(id.length-1);
                                var prefix = id.substr(0, (id.length-1));
                                el.attr('id', prefix+(+i+1));
                            }
                            var name = el.attr('name') || null;
                            if(name) {
                                var i = name.substr(name.length-1);
                                var prefix1 = name.substr(0, (name.length-1));
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
  //Item ID Dropdown Ends
//------------------------

//------------------------
//Packaging Type Dropdown
  let pttid = "#PackagingTable2";
      let pttid2 = "#PackagingTable";
      let ptheaders = document.querySelectorAll(pttid2 + " th");

      // Sort the table element when clicking on the table headers
      ptheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(pttid, ".clsptid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function PackagingCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Packagingcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("PackagingTable2");
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

  function PackagingNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Packagingnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("PackagingTable2");
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

  $('#Material').on('click','[id*="PACKSIZE"]',function(event){
         $("#Packagingpopup").show();
         var id = $(this).attr('id');
         var id2 = $(this).parent().parent().find('[id*="PTID_REF"]').attr('id');
         $('#hdn_Packaging').val(id);
         $('#hdn_Packaging2').val(id2);
      });

      $("#PackagingclosePopup").click(function(event){
        $("#Packagingpopup").hide();
      });

      $(".clsptid").dblclick(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");

        var txtid = $('#hdn_Packaging').val();
        var txt_id2 = $('#hdn_Packaging2').val();
        $('#'+txtid).val(texdesc);
        $('#'+txt_id2).val(txtval);
        if(txtval == '')
        {
          $('#'+txtid).parent().parent().find('[id*="PACKUOM"]').prop('disabled',true);
          $('#'+txtid).parent().parent().find('[id*="PACKUOM"]').removeAttr('readonly');
          $('#'+txtid).parent().parent().find('[id*="PACK_QTY"]').prop('disabled',true);
        }
        else
        {
          $('#'+txtid).parent().parent().find('[id*="PACKUOM"]').removeAttr('disabled');
          $('#'+txtid).parent().parent().find('[id*="PACK_QTY"]').removeAttr('disabled');
          $('#'+txtid).parent().parent().find('[id*="PACKUOM"]').prop('readonly',true);
        }
        $("#Packagingpopup").hide();
        
        $("#Packagingcodesearch").val(''); 
        $("#Packagingnamesearch").val(''); 
        PackagingCodeFunction();
        event.preventDefault();
      });      

  //Packaging Type Dropdown Ends
//------------------------

//------------------------
//UOM Type Dropdown
let uomtid = "#UOMTable2";
      let uomtid2 = "#UOMTable";
      let uomheaders = document.querySelectorAll(uomtid2 + " th");

      // Sort the table element when clicking on the table headers
      uomheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(uomtid, ".clsuomid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function UOMCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("UOMcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("UOMTable2");
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

  function UOMNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("UOMnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("UOMTable2");
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

  $('#Material').on('click','[id*="PACKUOM"]',function(event){
         $("#UOMpopup").show();
         var id = $(this).attr('id');
         var id2 = $(this).parent().parent().find('[id*="PACKUOMID_REF"]').attr('id');
         $('#hdn_UOM').val(id);
         $('#hdn_UOM2').val(id2);
      });

      $("#UOMclosePopup").click(function(event){
        $("#UOMpopup").hide();
      });

      $(".clsuomid").dblclick(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");

        var txtid = $('#hdn_UOM').val();
        var txt_id2 = $('#hdn_UOM2').val();
        $('#'+txtid).val(texdesc);
        $('#'+txt_id2).val(txtval);
        
        $("#UOMpopup").hide();
        
        $("#UOMcodesearch").val(''); 
        $("#UOMnamesearch").val(''); 
        UOMCodeFunction();
        event.preventDefault();
      });      

  //UOM Type Dropdown Ends
//------------------------

//------------------------
  //ALT UOM Dropdown
  let altutid = "#altuomTable2";
      let altutid2 = "#altuomTable";
      let altutidheaders = document.querySelectorAll(altutid2 + " th");

      // Sort the table element when clicking on the table headers
      altutidheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(altutid, ".clsaltuom", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function altuomCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("altuomcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("altuomTable2");
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

      function altuomNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("altuomnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("altuomTable2");
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

      

  $('#Material').on('click','[id*="popupAUOM"]',function(event){
        var ItemID = $(this).parent().parent().find('[id*="ITEMID_REF"]').val();
        
        if(ItemID !=''){
                $("#tbody_altuom").html('');
                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  });
                  $.ajax({
                      url:'{{route("transaction",[168,"getAltUOM"])}}',
                      type:'POST',
                      data:{'id':ItemID},
                      success:function(data) {
                        
                        $("#tbody_altuom").html(data);   
                        bindAltUOM();                     
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                        $("#tbody_altuom").html('');                        
                      },
                  }); 
        }
        else
        {
                $("#altuompopup").hide();
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text('Please Select Item First.');
                $("#alert").modal('show');
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk1');
                return false;
        }

        $("#altuompopup").show();
        var id = $(this).attr('id');
        var id2 = $(this).parent().parent().find('[id*="ALT_UOMID_REF"]').attr('id');
        var id3 = $(this).parent().parent().find('[id*="SE_QTY"]').attr('id');
        var id4 = $(this).parent().parent().find('[id*="ALT_UOMID_QTY"]').attr('id');
        
        $('#hdn_altuom').val(id);
        $('#hdn_altuom2').val(id2);
        $('#hdn_altuom3').val(id3);
        $('#hdn_altuom4').val(id4);
        event.preventDefault();
      });

      $("#altuom_closePopup").click(function(event){
        $("#altuompopup").hide();
      });

    function bindAltUOM(){

      $('#altuomTable2').off(); 

      $(".clsaltuom").dblclick(function(){
        var fieldid = $(this).attr('id');
        var txtval =   $("#txt"+fieldid+"").val();
        var texdesc =  $("#txt"+fieldid+"").data("desc");
        var txtid= $('#hdn_altuom').val();
        var txt_id2= $('#hdn_altuom2').val();
        var txt_id3= $('#hdn_altuom3').val();
        var txt_id4= $('#hdn_altuom4').val();
        $('#'+txtid).val(texdesc);
        $('#'+txt_id2).val(txtval);

        var itemid = $('#'+txtid).parent().parent().find('[id*="ITEMID_REF"]').val();
        var altuomid = txtval;
        var mqty = $('#'+txtid).parent().parent().find('[id*="SE_QTY"]').val();

        if(altuomid!=''){
              $('#'+txt_id4).val('');
                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  });
                  $.ajax({
                      url:'{{route("transaction",[168,"getaltuomqty"])}}',
                      type:'POST',
                      data:{'id':altuomid, 'itemid':itemid, 'mqty':mqty},
                      success:function(data) {
                        if(intRegex.test(data)){
                            data = (data +'.000');
                        }
                        $('#'+txt_id4).val(data);                        
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                        $('#'+txt_id4).val('');                        
                      },
                  }); 
                      
              }

        $("#altuompopup").hide();
        $("#altuomcodesearch").val(''); 
        $("#altuomnamesearch").val(''); 
        
        altuomCodeFunction();
        event.preventDefault();
      });
    }

      

  
$('#btnAdd').on('click', function() {
  var viewURL = '{{route("transaction",[168,"add"])}}';
              window.location.href=viewURL;
});

$('#btnExit').on('click', function() {
  var viewURL = '{{route('home')}}';
              window.location.href=viewURL;
});

$('#ENQDT').change(function() {
    var mindate  = $(this).val();
    $('[id*="EDD"]').attr('min',mindate);
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
          event.preventDefault();
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
      if(id) {
          var i = id.substr(id.length-1);
          var prefix = id.substr(0, (id.length-1));
          el.attr('id', prefix+(+i+1));
      }
      var name = el.attr('name') || null;
      if(name) {
          var i = name.substr(name.length-1);
          var prefix1 = name.substr(0, (name.length-1));
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
    window.location.reload();
}//fnUndoYes

window.fnUndoNo = function (){
    $("#ENQNO").focus();
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

    var d = new Date(); 
    var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;


    var d = new Date(); 
    var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;

    $('#AFSADT').val(today);
    $('#AFSADT').attr('min',today);



var count1 = <?php echo json_encode($objCount1); ?>;


$('#Row_Count1').val(count1);

var objSE = <?php echo json_encode($objSEMAT); ?>;







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
 var REASON_AFSA          =   $.trim($("#REASON_AFSA").val());

 if(REASON_AFSA ===""){
     $("#FocusId").val($("#REASON_AFSA"));
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please enter Reason to amend AFS value.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
else if(AFSNO ===""){
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
                $("#YesBtn").data("funcname","fnSaveData");  //set dynamic fucntion name
                $("#YesBtn").focus();
                $("#OkBtn").hide();
                highlighFocusBtn('activeYes');
          }

}

$("#btnSaveSE" ).click(function() {
    var formReqData = $("#frm_trn_se");
    if(formReqData.valid()){
      validateForm();
    }
});



$("#YesBtn").click(function(){

$("#alert").modal('hide');
var customFnName = $("#YesBtn").data("funcname");
    window[customFnName]();

}); //yes button

window.fnSaveData = function (){
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
$("#btnApprove").prop("disabled", true);
  $.ajax({
      url:'{{ route("transactionmodify",[168,"saveamendment"])}}',
      type:'POST',
      data:formData,
      success:function(data) {
        $(".buttonload").hide(); 
        $("#btnSaveSE").show();   
        $("#btnApprove").prop("disabled", false);
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
                ("#YesBtn").hide();
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
        $("#btnApprove").prop("disabled", false);
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

window.fnApproveData = function (){
event.preventDefault();
      var trnseForm = $("#frm_trn_se");
      var formData = trnseForm.serialize();
  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });
  $.ajax({
      url:'{{ route("transactionmodify",[168,"Approve"])}}',
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
            if(data.country=='norecord') {
                ("#YesBtn").hide();
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
    window.location.href = '{{route("transaction",[168,"index"]) }}';
});

$("#OkBtn1").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn").hide();
    $("#OkBtn1").hide();
    $("#"+$(this).data('focusname')).focus();
    // $("[id*=txtlabel]").focus();
    $(".text-danger").hide();
});

//
function showError(pId,pVal){
    $("#"+pId+"").text(pVal);
    $("#"+pId+"").show();
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




$( "#btnApprove" ).click(function() {
 
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

      
   
          else{
                $("#alert").modal('show');
                $("#AlertMessage").text('Do you want to save to record.');
                $("#YesBtn").data("funcname","fnApproveData");  
                $("#YesBtn").focus();
                $("#OkBtn").hide();
                highlighFocusBtn('activeYes');
          }
       
});


</script>


@endpush