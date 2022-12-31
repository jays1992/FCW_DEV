@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
  <div class="row">
    <div class="col-lg-2"><a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Job Estimation</a></div>
    <div class="col-lg-10 topnav-pd">
      <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
      <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
      <button class="btn topnavbt" id="btnSaveFormData" onclick="saveAction('save')" ><i class="fa fa-floppy-o"></i> Save</button>
      <button style="display:none" class="btn topnavbt buttonload"> <i class="fa fa-refresh fa-spin"></i> {{Session::get('save')}}</button>
      <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
      <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
      <button class="btn topnavbt" id="btnUndo"  ><i class="fa fa-undo"></i> Undo</button>
      <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
      <button class="btn topnavbt" id="btnApprove" disabled="disabled" onclick="saveAction('approve')"><i class="fa fa-thumbs-o-up"></i> Approved</button>
      <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
      <button class="btn topnavbt" id="btnExit" onclick="return  window.location.href='{{route('home')}}'" ><i class="fa fa-power-off"></i> Exit</button>
    </div>
  </div>
</div>

<form id="transaction_form" method="POST" >
  <div class="container-fluid filter"> 
    @csrf
    <div class="inner-form"> 
      <div class="row">
        <div class="col-lg-2 pl"><p>Job No*</p></div>
        <div class="col-lg-2 pl">
          <input type="text" name="DOC_NO" id="DOC_NO" value="{{$docarray['DOC_NO']}}" {{$docarray['READONLY']}} class="form-control" maxlength="{{$docarray['MAXLENGTH']}}" autocomplete="off" style="text-transform:uppercase" >
          <script>docMissing(@json($docarray['FY_FLAG']));</script>
        </div>
                          
        <div class="col-lg-2 pl"><p>Job Date*</p></div>
        <div class="col-lg-2 pl">
          <input type="date" name="DOC_DATE" id="DOC_DATE" value="{{date('Y-m-d')}}"  class="form-control" autocomplete="off" placeholder="dd/mm/yyyy" onchange="getValidityDate()" >
        </div>

        <div class="col-lg-1 pl" style="width:100px;"><p>Existing Customer</p></div>
        <div class="col-lg-1 pl"> <input type="radio" name="CUSTOMER_TYPE" value="EXIST" checked onchange="get_customer_type(this.value)" ></div>

        <div class="col-lg-1 pl"><p>New<br/>Customer</p></div>
        <div class="col-lg-1 pl"> <input type="radio" name="CUSTOMER_TYPE" value="NEW" onchange="get_customer_type(this.value)" ></div>
      </div>

      <div class="row">
        <div class="col-lg-2 pl"><p>Search</p></div>
        <div class="col-lg-2 pl"> 
          <input type="text" id="SEARCH_CUSTOMER" onclick="searchCustomerMaster()" class="form-control" autocomplete="off"  onkeypress="return isNumberKey(event,this)" readonly >
        </div>

        <div class="col-lg-1 pl"> 
          <i class="fa fa-search" onclick="searchCustomerMaster()" style="cursor:pointer;margin-top:5px;"></i>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-2 pl"><p>Foreign Currency</p></div>
        <div class="col-lg-1 pl">
            <input type="checkbox" name="FC" id="FC" class="form-checkbox" >
        </div>                            
        <div class="col-lg-2 pl col-md-offset-1"><p>Currency</p></div>
        <div class="col-lg-2 pl" id="divcurrency" >
            <input type="text" name="CRID_popup" id="txtCRID_popup" class="form-control"  autocomplete="off"  disabled/>
            <input type="hidden" name="CRID_REF" id="CRID_REF" class="form-control" autocomplete="off" />                                
        </div>                            
        <div class="col-lg-2 pl"><p>Conversion Factor</p></div>
        <div class="col-lg-2 pl">
            <input type="text" name="CONVFACT" id="CONVFACT" autocomplete="off" onkeyup="MultiCurrency_Conversion('TOTAL')" class="form-control" readonly  maxlength="100" />
        </div>
    </div>   

      <div class="row">
        <div class="col-lg-2 pl"><p>Customer Name*</p></div>
        <div class="col-lg-2 pl"> 
          <input type="text" name="CUSTOMER_NAME" id="CUSTOMER_NAME" class="form-control" autocomplete="off" readonly>
          <input type="hidden" name="CUSTOMER_ID" id="CUSTOMER_ID" class="form-control" autocomplete="off" >
        </div>

        <div class="col-lg-2 pl"><p> Date of Birth</p></div>
        <div class="col-lg-2 pl"> 
          <input type="date" name="DOB" id="DOB" max="{{date('Y-m-d')}}" class="form-control" autocomplete="off" >
        </div>

        <div class="col-lg-2 pl"><p>E-Mail Id*</p></div>
        <div class="col-lg-2 pl"> 
          <input type="text" name="EMAIL_ID" id="EMAIL_ID" class="form-control" autocomplete="off" >
        </div>

      </div>

      <div class="row">
        <div class="col-lg-2 pl"><p>Mobile No*</p></div>
        <div class="col-lg-2 pl"> 
          <input type="text" name="MOBILE_NO" id="MOBILE_NO" class="form-control" autocomplete="off" placeholder='Mobile No' maxlength="12"  onkeypress="return isNumberKey(event,this)" onkeyup="validate_mobile_no()" >
        </div>

        <div class="col-lg-2 pl"><p>Address*</p></div>
        <div class="col-lg-2 pl"> 
          <input type="text" name="ADDRESS" id="ADDRESS" class="form-control" autocomplete="off" >
        </div>

        <div class="col-lg-2 pl"><p>Anniversary Date</p></div>
        <div class="col-lg-2 pl"> 
          <input type="date" name="ANNIVERSARY_DATE" id="ANNIVERSARY_DATE" class="form-control" autocomplete="off" >
        </div>
      </div>

      <div class="row">
        <div class="col-lg-2 pl"><p>Country*</p></div>
        <div class="col-lg-2 pl"> 
          <input type="text" name="COUNTRY_NAME" id="COUNTRY_NAME" class="form-control" autocomplete="off" onclick="getCountryMaster()" readonly value="{{isset($country_state_city[0]->COUNTRY_NAME)?$country_state_city[0]->COUNTRY_NAME:''}}" >
          <input type="hidden" name="COUNTRY_ID" id="COUNTRY_ID" class="form-control" autocomplete="off" value="{{isset($country_state_city[0]->COUNTRY_ID)?$country_state_city[0]->COUNTRY_ID:''}}" >
        </div>
       
        <div class="col-lg-2 pl"><p>State*</p></div>
        <div class="col-lg-2 pl"> 
          <input type="text" name="STATE_NAME" id="STATE_NAME" class="form-control" autocomplete="off" onclick="getStateMaster()" readonly value="{{isset($country_state_city[0]->STATE_NAME)?$country_state_city[0]->STATE_NAME:''}}" >
          <input type="hidden" name="STATE_ID" id="STATE_ID" class="form-control" autocomplete="off" value="{{isset($country_state_city[0]->STATE_ID)?$country_state_city[0]->STATE_ID:''}}">
        </div>

        <div class="col-lg-2 pl"><p>City*</p></div>
        <div class="col-lg-2 pl"> 
          <input type="text" name="CITY_NAME" id="CITY_NAME" class="form-control" autocomplete="off" onclick="getCityMaster()" readonly  value="{{isset($country_state_city[0]->CITY_NAME)?$country_state_city[0]->CITY_NAME:''}}" >
          <input type="hidden" name="CITY_ID" id="CITY_ID" class="form-control" autocomplete="off" value="{{isset($country_state_city[0]->CITY_ID)?$country_state_city[0]->CITY_ID:''}}" >
        </div>
      </div>

      <div class="row">
        <div class="col-lg-2 pl"><p>Pin Code*</p></div>
        <div class="col-lg-2 pl"> 
        <input type="text" name="PINCODE" id="PINCODE" class="form-control" autocomplete="off" maxlength="6" onkeypress="return isNumberKey(event,this)" >
        </div>

        <div class="col-lg-2 pl"><p>GST Type*</p></div>
        <div class="col-lg-2 pl"> 
          <select name="GST_TYPE" id="GST_TYPE" class="form-control mandatory" autocomplete="off" >
						<option value="">Select</option>
            @foreach ($objGstTypeList as $index=>$GstType)
            <option value="{{ $GstType-> GSTID }}">{{ $GstType->GSTCODE }} - {{ $GstType->DESCRIPTIONS }}</option>
            @endforeach
					</select>
        </div>
       
        <div class="col-lg-2 pl"><p>GSTIN</p></div>
        <div class="col-lg-2 pl"> 
        <input type="text" name="GST_IN" id="GST_IN" class="form-control" autocomplete="off">
        </div>
      </div>

      <div class="row">
        <div class="col-lg-2 pl"><p>Landline No</p></div>
        <div class="col-lg-2 pl"> 
        <input type="text" name="LANDLINE_NO" id="LANDLINE_NO" class="form-control" autocomplete="off" onkeypress="return isNumberKey(event,this)" >
        </div>

        <div class="col-lg-2 pl"><p>Vehicle Reg No*</p></div>
        <div class="col-lg-2 pl"> 
        <input type="text" name="VEHICLE_REG_NO" id="VEHICLE_REG_NO" class="form-control" autocomplete="off">
        </div>
       
        <div class="col-lg-2 pl"><p>Vehicle Make*</p></div>
        <div class="col-lg-2 pl"> 
          <input type="text" name="VEHICLE_MAKE_NAME" id="VEHICLE_MAKE_NAME" class="form-control" autocomplete="off" onclick="getVehicleMakeMaster()" readonly >
          <input type="hidden" name="VEHICLE_MAKE_ID" id="VEHICLE_MAKE_ID" class="form-control" autocomplete="off">
        </div>
      </div>

      <div class="row">
        <div class="col-lg-2 pl"><p>Registration Year*</p></div>
        <div class="col-lg-2 pl"> 
          <input type="text" name="REG_YEAR" id="REG_YEAR" class="form-control" autocomplete="off" maxlength="4" onkeypress="return isNumberKey(event,this)">
        </div>

        <div class="col-lg-2 pl"><p>Supervisor Name</p></div>
        <div class="col-lg-2 pl"> 
        <input type="text" name="SUPERVISOR_NAME" id="SUPERVISOR_NAME" class="form-control" autocomplete="off">
        </div>

        <div class="col-lg-2 pl"><p>In Time*</p></div>
        <div class="col-lg-2 pl"> 
        <input type="time" name="IN_TIME" id="IN_TIME" class="form-control" autocomplete="off" onchange="validateTime()" >
        </div>
      </div>

      <div class="row">
        
       
        <div class="col-lg-2 pl"><p>OUT Time*</p></div>
        <div class="col-lg-2 pl"> 
          <input type="time" name="OUT_TIME" id="OUT_TIME" class="form-control" autocomplete="off" onchange="validateTime()" >
        </div>

        <div class="col-lg-2 pl"><p>Total</p></div>
        <div class="col-lg-2 pl"> 
          <input type="text" name="TOTAL" id="TOTAL" class="form-control" autocomplete="off" readonly >
        </div>

        <div id="multi_currency_section" style="display:none">
        <div class="col-lg-2 pl"  ><p id="currency_section"></p></div>
        <div class="col-lg-2 pl">
            <input type="text"  name="TotalValue_Conversion" id="TotalValue_Conversion" class="form-control"  autocomplete="off" readonly  />
        </div>
        </div>
                            
      </div>
    </div>

    <div class="container-fluid">
      <div class="row">
        <ul class="nav nav-tabs">
          <li class="active"><a data-toggle="tab" href="#Material" id="MAT_TAB">Details</a></li>
          <li><a data-toggle="tab" href="#udf" id="UDF_TAB">UDF</a></li>
        </ul>
                                            
        <div class="tab-content">
          <div id="Material" class="tab-pane fade in active">
            <div class="table-responsive table-wrapper-scroll-y" style="height:280px;margin-top:10px;" >
              <table id="example2" class="display nowrap table table-striped table-bordered itemlist w-200" style="height:auto !important;width:50%">
                <thead id="thead1"  style="position: sticky;top: 0">
                  <tr>
                    <th>Package</th>
                    <th>Amount</th>
                    <th>Action</th>
                  </tr>
                </thead>
							  <tbody>
								  <tr class="participantRow">
                    <td><input type="text" name="PACKAGE_NAME[]" id="PACKAGE_NAME_0"  class="form-control"  autocomplete="off" onclick="getPackageMaster(this.id)" readonly  /></td>
                    <td hidden><input type="text" name="PACKAGE_ID[]" id="PACKAGE_ID_0" class="form-control"  autocomplete="off" /></td>
                    <td><input type="text" name="AMOUNT[]" id="AMOUNT_0" readonly onkeypress="return isNumberDecimalKey(event,this)" onkeyup="get_total_amount()"  class="form-control"  autocomplete="off" readonly /></td>
                    <td align="center" ><button class="btn add material" title="add" data-toggle="tooltip" type="button" ><i class="fa fa-plus"></i></button>
                    <button class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
								  </tr>
							  </tbody>
					    </table>
					  </div>	
				  </div>

          <div id="udf" class="tab-pane fade">
            <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="margin-top:10px;height:280px;width:50%;">
              <table id="example4" class="display nowrap table table-striped table-bordered itemlist" style="height:auto !important;">
                <thead id="thead1"  style="position: sticky;top: 0">
                  <tr>
                    <th>UDF Fields<input class="form-control" type="hidden" name="Row_Count3" id ="Row_Count3" value="{{count($objUdf)}}"></th>
                    <th>Value / Comments</th>
                  </tr>
                </thead>                         
                <tbody>
                  @foreach($objUdf as $udfkey => $udfrow)
                  <tr  class="participantRow4">
                    <td><input name={{"udffie_popup_".$udfkey}} id={{"txtudffie_popup_".$udfkey}} value="{{$udfrow->LABEL}}" class="form-control @if ($udfrow->ISMANDATORY==1) mandatory @endif" autocomplete="off" maxlength="100" disabled/></td>
                    <td hidden><input type="text" name='{{"udffie_".$udfkey}}' id='{{"hdnudffie_popup_".$udfkey}}' value="{{$udfrow->UDFID}}" class="form-control" maxlength="100" /></td>
                    <td hidden><input type="text" name={{"udffieismandatory_".$udfkey}} id={{"udffieismandatory_".$udfkey}} class="form-control" maxlength="100" value="{{$udfrow->ISMANDATORY}}" /></td>            
                    <td id="{{"tdinputid_".$udfkey}}">
                    @php
                    $dynamicid  = "udfvalue_".$udfkey;
                    $chkvaltype = strtolower($udfrow->VALUETYPE); 
                    $udf_value  = isset($udfrow->UDF_VALUE)?$udfrow->UDF_VALUE:'';
  
                    if($chkvaltype=='date'){
                      $strinp = '<input type="date" placeholder="dd/mm/yyyy" name="'.$dynamicid.'" id="'.$dynamicid.'" value="'.$udf_value.'" class="form-control" value="" /> ';       
                    }
                    else if($chkvaltype=='time'){
                      $strinp= '<input type="time" placeholder="h:i" name="'.$dynamicid.'" id="'.$dynamicid.'" value="'.$udf_value.'" class="form-control"  value=""/> ';
                    }
                    else if($chkvaltype=='numeric'){
                      $strinp = '<input type="text" name="'.$dynamicid. '" id="'.$dynamicid.'" value="'.$udf_value.'" class="form-control" value=""  autocomplete="off" /> ';
                    }
                    else if($chkvaltype=='text'){
                      $strinp = '<input type="text" name="'.$dynamicid. '" id="'.$dynamicid.'" value="'.$udf_value.'" class="form-control" value=""  autocomplete="off" /> ';
                    }
                    else if($chkvaltype=='boolean'){

                      $boolval = ''; 
                      if($udf_value =='on' || $udf_value  =='1'){
                        $boolval="checked";
                      }

                      $strinp = '<input type="checkbox" name="'.$dynamicid. '" id="'.$dynamicid.'"  '.$boolval.' class=""  /> ';
                    }
                    else if($chkvaltype=='combobox'){
                      $strinp       ='';
                      $txtoptscombo = strtoupper($udfrow->DESCRIPTIONS); ;
                      $strarray     = explode(',',$txtoptscombo);
                      $opts         = '';
                      $chked        ='';

                      for ($i = 0; $i < count($strarray); $i++) {
                        $chked='';
                        if($strarray[$i]==$udf_value){
                          $chked='selected="selected"';
                        }
                        
                        $opts = $opts.'<option value="'.$strarray[$i].'" '.$chked.'  >'.$strarray[$i].'</option> ';
                      }

                      $strinp = '<select name="'.$dynamicid.'" id="'.$dynamicid.'" class="form-control" >'.$opts.'</select>' ;
                    }
                    echo $strinp;
                    @endphp
                    </td>
                  </tr>
                  @endforeach                             
                </tbody>
              </table>
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
          <button class="btn alertbt" name='YesBtn' id="YesBtn" data-funcname="fnSaveData"><div id="alert-active" class="activeYes"></div>Yes</button>
          <button class="btn alertbt" name='NoBtn' id="NoBtn"   data-funcname="fnUndoNo" ><div id="alert-active" class="activeNo"></div>No</button>
          <button class="btn alertbt" name='OkBtn' id="OkBtn" style="display:none;margin-left: 90px;"><div id="alert-active" class="activeOk"></div>OK</button>
          <button class="btn alertbt" name='OkBtn1' id="OkBtn1" onclick="getFocus()" style="display:none;margin-left: 90px;"><div id="alert-active" class="activeOk1"></div>OK</button>
          <input type="hidden" id="FocusId" >
        </div>
		  <div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<div id="modal" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:50%;" >
    <div class="modal-content">
      <div class="modal-header"><button type="button" class="close" data-dismiss="modal" onclick="closeEvent('modal')" >&times;</button></div>
      <div class="modal-body">
	      <div class="tablename"><p id='modal_title'></p></div>
	      <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
          <table id="modal_table1" class="display nowrap table  table-striped table-bordered" >
            <thead>
              <tr>
                <th style="width:10%;">Select</th> 
                <th style="width:45%;" id='modal_th1'></th>
                <th style="width:45%;" id='modal_th2'></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <th style="width:10%;"></th>
                <td style="width:45%;"><input type="text" id="text1" class="form-control" autocomplete="off" onkeyup="searchData(1)"></td>
                <td style="width:45%;"><input type="text" id="text2" class="form-control" autocomplete="off" onkeyup="searchData(2)"></td>
              </tr>
            </tbody>
          </table>

          <table id="modal_table2" class="display nowrap table  table-striped table-bordered" >
            <tbody id="modal_body" style="font-size:14px;"></tbody>
          </table>
        </div>
		    <div class="cl"></div>
      </div>
    </div>
  </div>
</div>


<!-- Currency Dropdown -->
<div id="cridpopup" class="modal" role="dialog"  data-backdrop="static">
<div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='crid_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Currency</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="CurrencyTable" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
    <tr>
      <th class="ROW1">Select</th> 
      <th class="ROW2">Code</th>
      <th class="ROW3">Description</th>
    </tr>
    </thead>
    <tbody>
      <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="currencycodesearch" class="form-control" onkeyup="CurrencyCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="currencynamesearch" class="form-control" onkeyup="CurrencyNameFunction()"></td>
      </tr>
    </tbody>
    </table>
      <table id="CurrencyTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          <!-- <tr>
            <th>GLCode</th>
            <th>GLName</th>
          </tr> -->
          
        </thead>
        <tbody>
        @foreach ($objothcurrency as $crindex=>$crRow)
        <tr>
          <td class="ROW1"> <input type="checkbox" name="SELECT_CRID[]" id="cridcode_{{ $crindex }}" class="clscrid" value="{{ $crRow-> CRID }}" ></td>
          <td class="ROW2">{{ $crRow-> CRCODE }}
            <input type="hidden" id="txtcridcode_{{ $crindex }}" data-desc="{{ $crRow-> CRCODE }}" data-desc2="{{ $crRow-> CRDESCRIPTION }}"  value="{{ $crRow-> CRID }}"/>
          </td>
          <td class="ROW3">{{ $crRow-> CRDESCRIPTION }}</td>
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
<!-- Currency Dropdown-->  
						
						
						
@endsection
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

let tid1    = "#modal_table1";
let tid2    = "#modal_table2";
let headers = document.querySelectorAll(tid1 + " th");

headers.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(tid2, ".clsipoid", "td:nth-child(" + (i + 1) + ")");
  });
});

function searchData(cno){
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById('text'+cno);
  filter = input.value.toUpperCase();
  table = document.getElementById("modal_table2");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[cno];
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

function closeEvent(id){
  $("#"+id).hide();
}

function get_customer_type(value){
  $("#SEARCH_CUSTOMER").val('');
  $("#SEARCH_CUSTOMER").prop('readonly',true);
  $("#CUSTOMER_NAME").prop('readonly',true);
  if(value ==='NEW'){
    $("#SEARCH_CUSTOMER").prop('readonly',true);
    $("#CUSTOMER_NAME").prop('readonly',false);
  }

  $('input:text').val('');
  $('input:hidden').val('');
  $('#DOC_NO').val("{{$docarray['DOC_NO']}}");
}




function searchCustomerMaster(){

  if($('input[name="CUSTOMER_TYPE"]:checked').val() == "EXIST"){

    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
      url:'{{route("transaction",[$FormId,"searchCustomer"])}}',
      type:'POST',
      success:function(data) {
        var html = '';

        if(data.length > 0){
          $.each(data, function(key, value) {
            html +='<tr>';
            html +='<td style="width:10%;text-align:center;" ><input type="checkbox" id="key_'+key+'" value="'+value.DATA_ID+'" onChange="bindCustomerMaster(this)" data-code="'+value.DATA_CODE+'" data-desc="'+value.DATA_DESC+'" data-f1="'+value.REGADDL1+'" data-f2="'+value.COUNTRY_ID+'" data-f3="'+value.COUNTRY_NAME+'" data-f4="'+value.STATE_ID+'" data-f5="'+value.STATE_NAME+'" data-f6="'+value.CITY_ID+'" data-f7="'+value.CITY_NAME+'" data-f8="'+value.REGPIN+'" data-f9="'+value.EMAILID+'" data-f10="'+value.PHNO+'" data-f11="'+value.MONO+'" data-f12="'+value.GSTTYPE+'" data-f13="'+value.GSTIN+'" ></td>';
            html +='<td style="width:45%;" >'+value.DATA_CODE+' - '+value.DATA_DESC+'</td>';
            html +='<td style="width:45%;" >'+value.MONO+'</td>';
            html +='</tr>';
          });
        }
        else{
          html +='<tr><td colspan="3" style="text-align:center;">No data available in table</td></tr>';
        }

        $("#modal_body").html(html);
      },
      error: function (request, status, error) {
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").show();
        $("#AlertMessage").text(request.responseText);
        $("#alert").modal('show');
        $("#OkBtn").focus();
        highlighFocusBtn('activeOk');
        $("#material_data").html('<tr><td colspan="3" style="text-align:center;">No data available in table</td></tr>');                       
      },
    });

    $("#modal_title").text('Customer Master');
    $("#modal_th1").text('Customer Code & Name');
    $("#modal_th2").text('Mobile No');
    $("#modal").show();
  }

}

function bindCustomerMaster(data){
  var code          = $("#"+data.id).data("code");
  var desc          = $("#"+data.id).data("desc");

  var address       = $("#"+data.id).data("f1");
  var country_id    = $("#"+data.id).data("f2");
  var country_name  = $("#"+data.id).data("f3");
  var state_id      = $("#"+data.id).data("f4");
  var state_name    = $("#"+data.id).data("f5");
  var city_id       = $("#"+data.id).data("f6");
  var city_name     = $("#"+data.id).data("f7");
  var pincode       = $("#"+data.id).data("f8");
  var email         = $("#"+data.id).data("f9");
  var phno          = $("#"+data.id).data("f10");
  var mobile_no     = $("#"+data.id).data("f11");
  var gst_type      = $("#"+data.id).data("f12");
  var gstin         = $("#"+data.id).data("f13");

  $("#CUSTOMER_ID").val(data.value);
  $("#CUSTOMER_NAME").val(code+' - '+desc);

  $("#ADDRESS").val(address);
  $("#COUNTRY_ID").val(country_id);
  $("#COUNTRY_NAME").val(country_name);
  $("#STATE_ID").val(state_id);
  $("#STATE_NAME").val(state_name);
  $("#CITY_ID").val(city_id);
  $("#CITY_NAME").val(city_name);
  $("#PINCODE").val(pincode);
  $("#EMAIL_ID").val(email);
  $("#LANDLINE_NO").val(phno);
  $("#MOBILE_NO").val(mobile_no);
  $("#GST_TYPE").val(gst_type);
  $("#GST_IN").val(gstin);

  $("#text1").val(''); 
  $("#text2").val(''); 
  $("#modal_body").html('');  
  $("#modal").hide(); 
}

function getCountryMaster(){
  $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  $.ajax({
    url:'{{route("transaction",[$FormId,"getCountryMaster"])}}',
    type:'POST',
    success:function(data) {
      var html = '';

      if(data.length > 0){
        $.each(data, function(key, value) {
          html +='<tr>';
          html +='<td style="width:10%;text-align:center;" ><input type="checkbox" id="key_'+key+'" value="'+value.DATA_ID+'" onChange="bindCountryMaster(this)" data-code="'+value.DATA_CODE+'" data-desc="'+value.DATA_DESC+'" ></td>';
          html +='<td style="width:45%;" >'+value.DATA_CODE+'</td>';
          html +='<td style="width:45%;" >'+value.DATA_DESC+'</td>';
          html +='</tr>';
        });
      }
      else{
        html +='<tr><td colspan="3" style="text-align:center;">No data available in table</td></tr>';
      }

      $("#modal_body").html(html);
    },
    error: function (request, status, error) {
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").show();
      $("#AlertMessage").text(request.responseText);
      $("#alert").modal('show');
      $("#OkBtn").focus();
      highlighFocusBtn('activeOk');
      $("#material_data").html('<tr><td colspan="3" style="text-align:center;">No data available in table</td></tr>');                       
    },
  });

  $("#modal_title").text('Country Master');
  $("#modal_th1").text('Country Code');
  $("#modal_th2").text('Country Name');
  $("#modal").show();
}

function bindCountryMaster(data){
  var code  = $("#"+data.id).data("code");
  var desc  = $("#"+data.id).data("desc");

  $("#COUNTRY_ID").val(data.value);
  $("#COUNTRY_NAME").val(code+' - '+desc);

  $("#STATE_NAME").val(''); 
  $("#STATE_ID").val(''); 
  $("#CITY_NAME").val(''); 
  $("#CITY_ID").val(''); 
  
  $("#text1").val(''); 
  $("#text2").val(''); 
  $("#modal_body").html('');  
  $("#modal").hide(); 
}

function getStateMaster(){

  var COUNTRY_ID  = $("#COUNTRY_ID").val();
 
  if(COUNTRY_ID ===""){
    $("#FocusId").val('COUNTRY_NAME');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select country.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  } 
  else{

    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
      url:'{{route("transaction",[$FormId,"getStateMaster"])}}',
      type:'POST',
      data:{COUNTRY_ID:COUNTRY_ID},
      success:function(data) {
        var html = '';

        if(data.length > 0){
          $.each(data, function(key, value) {
            html +='<tr>';
            html +='<td style="width:10%;text-align:center;" ><input type="checkbox" id="key_'+key+'" value="'+value.DATA_ID+'" onChange="bindStateMaster(this)" data-code="'+value.DATA_CODE+'" data-desc="'+value.DATA_DESC+'" ></td>';
            html +='<td style="width:45%;" >'+value.DATA_CODE+'</td>';
            html +='<td style="width:45%;" >'+value.DATA_DESC+'</td>';
            html +='</tr>';
          });
        }
        else{
          html +='<tr><td colspan="3" style="text-align:center;">No data available in table</td></tr>';
        }

        $("#modal_body").html(html);
      },
      error: function (request, status, error) {
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").show();
        $("#AlertMessage").text(request.responseText);
        $("#alert").modal('show');
        $("#OkBtn").focus();
        highlighFocusBtn('activeOk');
        $("#material_data").html('<tr><td colspan="3" style="text-align:center;">No data available in table</td></tr>');                       
      },
    });

    $("#modal_title").text('State Master');
    $("#modal_th1").text('State Code');
    $("#modal_th2").text('State Name');
    $("#modal").show();

  }
}

function bindStateMaster(data){
  var code  = $("#"+data.id).data("code");
  var desc  = $("#"+data.id).data("desc");

  $("#STATE_ID").val(data.value);
  $("#STATE_NAME").val(code+' - '+desc);

  $("#CITY_NAME").val(''); 
  $("#CITY_ID").val('');
  
  $("#text1").val(''); 
  $("#text2").val(''); 
  $("#modal_body").html('');  
  $("#modal").hide(); 
}

function getCityMaster(){

  var COUNTRY_ID  = $("#COUNTRY_ID").val();
  var STATE_ID    = $("#STATE_ID").val();

  if(COUNTRY_ID ===""){
    $("#FocusId").val('COUNTRY_NAME');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select country.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  } 
  else if(STATE_ID ===""){
    $("#FocusId").val('STATE_NAME');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select state.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  } 
  else{

    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
      url:'{{route("transaction",[$FormId,"getCityMaster"])}}',
      type:'POST',
      data:{COUNTRY_ID:COUNTRY_ID,STATE_ID:STATE_ID},
      success:function(data) {
        var html = '';

        if(data.length > 0){
          $.each(data, function(key, value) {
            html +='<tr>';
            html +='<td style="width:10%;text-align:center;" ><input type="checkbox" id="key_'+key+'" value="'+value.DATA_ID+'" onChange="bindCityMaster(this)" data-code="'+value.DATA_CODE+'" data-desc="'+value.DATA_DESC+'" ></td>';
            html +='<td style="width:45%;" >'+value.DATA_CODE+'</td>';
            html +='<td style="width:45%;" >'+value.DATA_DESC+'</td>';
            html +='</tr>';
          });
        }
        else{
          html +='<tr><td colspan="3" style="text-align:center;">No data available in table</td></tr>';
        }

        $("#modal_body").html(html);
      },
      error: function (request, status, error) {
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").show();
        $("#AlertMessage").text(request.responseText);
        $("#alert").modal('show');
        $("#OkBtn").focus();
        highlighFocusBtn('activeOk');
        $("#material_data").html('<tr><td colspan="3" style="text-align:center;">No data available in table</td></tr>');                       
      },
    });

    $("#modal_title").text('City Master');
    $("#modal_th1").text('City Code');
    $("#modal_th2").text('City Name');
    $("#modal").show();

  }
}

function bindCityMaster(data){
  var code  = $("#"+data.id).data("code");
  var desc  = $("#"+data.id).data("desc");

  $("#CITY_ID").val(data.value);
  $("#CITY_NAME").val(code+' - '+desc);

  $("#text1").val(''); 
  $("#text2").val(''); 
  $("#modal_body").html('');  
  $("#modal").hide(); 
}

function getVehicleMakeMaster(){
  $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  $.ajax({
    url:'{{route("transaction",[$FormId,"getVehicleMakeMaster"])}}',
    type:'POST',
    success:function(data) {
      var html = '';

      if(data.length > 0){
        $.each(data, function(key, value) {
          html +='<tr>';
          html +='<td style="width:10%;text-align:center;" ><input type="checkbox" id="key_'+key+'" value="'+value.DATA_ID+'" onChange="bindVehicleMakeMaster(this)" data-code="'+value.DATA_CODE+'" data-desc="'+value.DATA_DESC+'" ></td>';
          html +='<td style="width:45%;" >'+value.DATA_CODE+'</td>';
          html +='<td style="width:45%;" >'+value.DATA_DESC+'</td>';
          html +='</tr>';
        });
      }
      else{
        html +='<tr><td colspan="3" style="text-align:center;">No data available in table</td></tr>';
      }

      $("#modal_body").html(html);
    },
    error: function (request, status, error) {
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").show();
      $("#AlertMessage").text(request.responseText);
      $("#alert").modal('show');
      $("#OkBtn").focus();
      highlighFocusBtn('activeOk');
      $("#material_data").html('<tr><td colspan="3" style="text-align:center;">No data available in table</td></tr>');                       
    },
  });

  $("#modal_title").text('Vehicle Make Master');
  $("#modal_th1").text('Vehicle Code');
  $("#modal_th2").text('Vehicle Name');
  $("#modal").show();
}

function bindVehicleMakeMaster(data){
  var code  = $("#"+data.id).data("code");
  var desc  = $("#"+data.id).data("desc");

  $("#VEHICLE_MAKE_ID").val(data.value);
  $("#VEHICLE_MAKE_NAME").val(code+' - '+desc);
  
  $("#text1").val(''); 
  $("#text2").val(''); 
  $("#modal_body").html('');  
  $("#modal").hide(); 
}

function getPackageMaster(textid){

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $.ajax({
      url:'{{route("transaction",[$FormId,"getPackageMaster"])}}',
      type:'POST',
      success:function(data) {
        var html = '';

        if(data.length > 0){
          $.each(data, function(key, value) {

            html +='<tr>';
            html +='<td style="width:10%;text-align:center;" ><input type="checkbox" id="key_'+key+'" value="'+value.DATA_ID+'" onChange="bindPackageMaster(this)" data-code="'+value.DATA_CODE+'" data-desc="'+value.DATA_DESC+'" data-price="'+value.PRICE+'" data-textid="'+textid+'" ></td>';
            html +='<td style="width:45%;" >'+value.DATA_CODE+'</td>';
            html +='<td style="width:45%;" >'+value.DATA_DESC+'</td>';
            html +='</tr>';

          });
        }
        else{
          html +='<tr><td colspan="3" style="text-align:center;">No data available in table</td></tr>';
        }

        $("#modal_body").html(html);
      },
      error: function (request, status, error) {
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").show();
        $("#AlertMessage").text(request.responseText);
        $("#alert").modal('show');
        $("#OkBtn").focus();
        highlighFocusBtn('activeOk');
        $("#material_data").html('<tr><td colspan="3" style="text-align:center;">No data available in table</td></tr>');                       
      },
    });

    $("#modal_title").text('Package Master');
    $("#modal_th1").text('Code');
    $("#modal_th2").text('Desc');
    $("#modal").show();
}

function bindPackageMaster(data){
  
  var textid          = $("#"+data.id).data("textid");
  var textid          = textid.split('_').pop();
  var code            = $("#"+data.id).data("code");
  var desc            = $("#"+data.id).data("desc");
  var price            = $("#"+data.id).data("price");
  
  $("#PACKAGE_ID_"+textid).val(data.value);
  $("#PACKAGE_NAME_"+textid).val(code+' - '+desc);

  $("#AMOUNT_"+textid).val(get_package_amount(data.value));
  get_total_amount();

  $("#text1").val(''); 
  $("#text2").val(''); 
  $("#modal_body").html('');  
  $("#modal").hide(); 
}

$("#Material").on('click', '.remove', function(){
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

$("#Material").on('click', '.add', function(){
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

  });

  $clone.find('input:text').val('');
  $clone.find('input:hidden').val('');

  $tr.closest('table').append($clone);         
  $clone.find('.remove').removeAttr('disabled'); 
  event.preventDefault();
});


function saveAction(action){
  validateForm(action);
}

function validateForm(action){

  var flag_exist    = [];
  var flag_status   = [];
  var flag_focus    = '';
  var flag_message  = '';
  var flag_tab_type = '';

  $("[id*=txtudffie_popup]").each(function(){
    if($.trim($(this).val())!=""){
      if($.trim($(this).parent().parent().find('[id*="udffieismandatory"]').val()) == "1"){
        if($.trim($(this).parent().parent().find('[id*="udfvalue"]').val()) != ""){
          flag_status.push('true');
        }
        else{
          flag_status.push('false');
          flag_focus    = $(this).parent().parent().find('[id*="udfvalue"]').attr('id');
          flag_message  = 'Please enter  Value / Comment in UDF Tab';
          flag_tab_type = 'UDF_TAB';
        }
      }             
    }             
  });

  var input = document.getElementsByName('PACKAGE_ID[]');
  for (var i = 0; i < input.length; i++) {

    var package_id = $.trim(document.getElementsByName('PACKAGE_ID[]')[i].value);
  
    if(package_id ===""){
      flag_exist.push(package_id);
      flag_status.push('false');
      flag_focus    = document.getElementsByName('PACKAGE_NAME[]')[i].id;
      flag_message  = 'Please select package';
      flag_tab_type = 'MAT_TAB';
    }
    else if($.trim(document.getElementsByName('AMOUNT[]')[i].value) ===""){
      flag_status.push('false');
      flag_focus    = document.getElementsByName('AMOUNT[]')[i].id;
      flag_message  = 'Please select amount';
      flag_tab_type = 'MAT_TAB';
    }
    else if(jQuery.inArray(package_id, flag_exist) !== -1){
      flag_status.push('false');
      flag_focus    = document.getElementsByName('PACKAGE_ID[]')[i].id;
      flag_message  = 'This package is already exist';
      flag_tab_type = 'MAT_TAB';
    }

    flag_exist.push(package_id);
  }

  if($.trim($("#DOC_NO").val()) ===""){
    $("#FocusId").val('DOC_NO');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Enter Job No.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($.trim($("#DOC_DATE").val()) ===""){
    $("#FocusId").val('DOC_DATE');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Select Job Date.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($.trim($("#CUSTOMER_NAME").val()) ===""){
    $("#FocusId").val('CUSTOMER_NAME');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Enter Customer Name.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($.trim($("#DOB").val()) !="" && $.trim($("#ANNIVERSARY_DATE").val()) !="" && $.trim($("#DOB").val()) >= $.trim($("#ANNIVERSARY_DATE").val())){
    $("#FocusId").val('ANNIVERSARY_DATE');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Anniversary Date should be greater than Date of Birth.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($.trim($("#EMAIL_ID").val()) ===""){
    $("#FocusId").val('EMAIL_ID');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Enter E-Mail Id.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if(IsEmail($.trim($("#EMAIL_ID").val()))==false){
    $("#FocusId").val('EMAIL_ID');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Enter Correct E-Mail Id.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($.trim($("#MOBILE_NO").val()) ===""){
    $("#FocusId").val('MOBILE_NO');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Enter Mobile No.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($('input[name="CUSTOMER_TYPE"]:checked').val() == "NEW" && exist_customer_by_mobile_no($.trim($("#MOBILE_NO").val()),'') > 0 ){
    $("#FocusId").val('MOBILE_NO');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Mobile No Already Exist.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($.trim($("#ADDRESS").val()) ===""){
    $("#FocusId").val('ADDRESS');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Enter Address.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($.trim($("#COUNTRY_NAME").val()) ===""){
    $("#FocusId").val('COUNTRY_NAME');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Select Country.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($.trim($("#STATE_NAME").val()) ===""){
    $("#FocusId").val('STATE_NAME');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Select State.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($.trim($("#CITY_NAME").val()) ===""){
    $("#FocusId").val('CITY_NAME');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Select City.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($.trim($("#PINCODE").val()) ===""){
    $("#FocusId").val('PINCODE');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Enter Pin Code.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($.trim($("#GST_TYPE").val()) ===""){
    $("#FocusId").val('GST_TYPE');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Enter GST Type.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($.trim($("#VEHICLE_REG_NO").val()) ===""){
    $("#FocusId").val('VEHICLE_REG_NO');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Enter Vehicle Reg No.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($.trim($("#VEHICLE_MAKE_NAME").val()) ===""){
    $("#FocusId").val('VEHICLE_MAKE_NAME');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Enter Vehicle Make.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($.trim($("#REG_YEAR").val()) ===""){
    $("#FocusId").val('REG_YEAR');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Enter Registration Year.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($.trim($("#IN_TIME").val()) ===""){
    $("#FocusId").val('IN_TIME');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Enter In Time.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($.trim($("#OUT_TIME").val()) ===""){
    $("#FocusId").val('OUT_TIME');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Enter OUT Time.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if(jQuery.inArray("false", flag_status) !== -1){
    $("#"+flag_tab_type).click();
    $("#FocusId").val(flag_focus);        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text(flag_message);
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }   
  else{
    $("#alert").modal('show');
    $("#AlertMessage").text('Do you want to '+action+' to record.');
    $("#YesBtn").data("funcname","fnSaveData");
    $("#YesBtn").data("action",action);
    $("#OkBtn1").hide();
    $("#OkBtn").hide();
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#YesBtn").focus();
    highlighFocusBtn('activeYes');
  }
}

$("#YesBtn").click(function(){
  $("#alert").modal('hide');
  var customFnName  = $("#YesBtn").data("funcname");
  var action        = $("#YesBtn").data("action");

  if(action ==="save"){
    window[customFnName]('{{route("transaction",[$FormId,"save"])}}');
  }
  else if(action ==="update"){
    window[customFnName]('{{route("transaction",[$FormId,"update"])}}');
  }
  else if(action ==="approve"){
    window[customFnName]('{{route("transaction",[$FormId,"Approve"])}}');
  }
  else{
    window.location.href = '{{route("transaction",[$FormId,"index"]) }}';
  }
});

window.fnSaveData = function (path){

  event.preventDefault();
  var trnsoForm = $("#transaction_form");
  var formData = trnsoForm.serialize();

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $("#btnSaveFormData").hide(); 
  $(".buttonload").show(); 
  $("#btnApprove").prop("disabled", true);

  $.ajax({
    url:path,
    type:'POST',
    data:formData,
    success:function(data) {
      $(".buttonload").hide(); 
      $("#btnSaveFormData").show();   
      $("#btnApprove").prop("disabled", false);
       
      if(data.success){                   
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").show();
        $("#AlertMessage").text(data.msg);
        $(".text-danger").hide();
        $("#alert").modal('show');
        $("#OkBtn").focus();
      }
      else{                   
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text(data.msg);
        $(".text-danger").hide();
        $("#alert").modal('show');
        $("#OkBtn1").focus();
      } 
    },
    error: function (request, status, error){
      $(".buttonload").hide(); 
      $("#btnSaveFormData").show();   
      $("#btnApprove").prop("disabled", false);
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text(request.responseText);
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk1');
    },
  });
}

$("#NoBtn").click(function(){
  $("#alert").modal('hide');
});

$("#OkBtn").click(function(){
  $("#alert").modal('hide');
  $("#YesBtn").show();
  $("#NoBtn").show();
  $("#OkBtn").hide();
  $(".text-danger").hide();
  window.location.href = '{{route("transaction",[$FormId,"index"]) }}';
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

function isNumberDecimalKey(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
    return false;

    return true;
}

function isNumberKey(e,t){
    try {
        if (window.event) {
            var charCode = window.event.keyCode;
        }
        else if (e) {
            var charCode = e.which;
        }
        else { return true; }
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {         
        return false;
        }
         return true;

    }
    catch (err) {
        alert(err.Description);
    }
}

function get_total_amount(){
  var total=0;
  var input = document.getElementsByName('AMOUNT[]');
  var tsid=[];
  for (var i = 0; i < input.length; i++) {
      var a = input[i];

      var amount  = $.trim(a.value) !=''?parseFloat(a.value):0
      var total   = total+amount;

  }

  $("#TOTAL").val(parseFloat(total).toFixed(2));
  MultiCurrency_Conversion('TOTAL'); 
}

$(function() { 
  get_total_amount();
});

function validateTime(){

  var startTime = $("#IN_TIME").val()+":00";
  var endTime   = $("#OUT_TIME").val()+":00";

  // var startTime = "05:01:20";
  // var endTime = "09:00:00";

  if($.trim($("#IN_TIME").val()) !='' && $.trim($("#OUT_TIME").val()) !=''){
    var regExp = /(\d{1,2})\:(\d{1,2})\:(\d{1,2})/;
    if(parseInt(endTime .replace(regExp, "$1$2$3")) > parseInt(startTime .replace(regExp, "$1$2$3"))){
      return true;
    }
    else{
      $("#OUT_TIME").val('');
      $("#FocusId").val('OUT_TIME');        
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Out time should be greate then In time.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
    }
  }
}

$(document).ready(function()
{
var dt = new Date();
var time = dt.getHours() + ":" + dt.getMinutes();
$("#IN_TIME").val(time); 
}
)


function validate_mobile_no(){
  if($('input[name="CUSTOMER_TYPE"]:checked').val() == "NEW" && $.trim($("#MOBILE_NO").val()).length >= 10 ){
    if(exist_customer_by_mobile_no($.trim($("#MOBILE_NO").val()),'') > 0 ){
      $("#FocusId").val('MOBILE_NO');        
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Mobile No Already Exist.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
    }
  }
}

function get_package_amount(PACKAGE_REF){
  var posts = $.ajax({
			url:'{{route("transaction",[$FormId,"get_package_amount"])}}',
			type:'POST',
			async: false,
			dataType: 'json',
			data: {PACKAGE_REF:PACKAGE_REF},
			done: function(response) {return response;}
		  }).responseText;

  return posts;
}



//Currency Dropdown
let crtid = "#CurrencyTable2";
      let crtid2 = "#CurrencyTable";
      let currencyheaders = document.querySelectorAll(crtid2 + " th");

      // Sort the table element when clicking on the table headers
      currencyheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(crtid, ".clscrid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function CurrencyCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("currencycodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("CurrencyTable2");
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

  function CurrencyNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("currencynamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("CurrencyTable2");
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

  $('#txtCRID_popup').click(function(event){
    showSelectedCheck($("#CRID_REF").val(),"SELECT_CRID");
         $("#cridpopup").show();
      });

      $("#crid_closePopup").click(function(event){
        $("#cridpopup").hide();
      });

      $(".clscrid").click(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc")+'-'+$("#txt"+fieldid+"").data("desc2");      
        
        $('#txtCRID_popup').val(texdesc);    
        $('#CRID_REF').val(txtval);
        $("#cridpopup").hide();
        $('#CONVFACT').val(GetConvFector(txtval));
        $("#currencycodesearch").val(''); 
        $("#currencynamesearch").val(''); 
        MultiCurrency_Conversion('TOTAL'); 
        event.preventDefault();
      });

      

      					
   $("#FC").change(function() {
      if ($(this).is(":checked") == true){
          $(this).parent().parent().find('#txtCRID_popup').removeAttr('disabled');
          $(this).parent().parent().find('#txtCRID_popup').prop('readonly','true');
          $('#CONVFACT').prop('readonly',false);
         
      }
      else
      {
          $(this).parent().parent().find('#txtCRID_popup').prop('disabled','true');
          $(this).parent().parent().find('#txtCRID_popup').removeAttr('readonly');
          $(this).parent().parent().find('#txtCRID_popup').val('');
          $(this).parent().parent().find('#CRID_REF').val('');
          $(this).parent().parent().find('#CONVFACT').val('');
          $('#CONVFACT').prop('readonly',true);
         
      }
	  MultiCurrency_Conversion('TOTAL'); 
  });

  //Currency Dropdown Ends		

  function showSelectedCheck(hidden_value,selectAll){

var divid ="";

if(hidden_value !=""){

    var all_location_id = document.querySelectorAll('input[name="'+selectAll+'[]"]');
    
    for(var x = 0, l = all_location_id.length; x < l;  x++){
    
        var checkid=all_location_id[x].id;
        var checkval=all_location_id[x].value;
    
        if(hidden_value == checkval){
        divid = checkid;
        }

        $("#"+checkid).prop('checked', false);
        
    }
}

if(divid !=""){
    $("#"+divid).prop('checked', true);
}
}

</script>
@endpush