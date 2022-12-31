@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
  <div class="row">
    <div class="col-lg-2"><a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Value Card Sale</a></div>
      <div class="col-lg-10 topnav-pd">
        <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
        <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
        <button class="btn topnavbt" id="btnSaveFormData" onclick="saveAction('update')" ><i class="fa fa-floppy-o"></i> Save</button>
        <button style="display:none" class="btn topnavbt buttonload"> <i class="fa fa-refresh fa-spin"></i> {{Session::get('save')}}</button>
        <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
        <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
        <button class="btn topnavbt" id="btnUndo"  ><i class="fa fa-undo"></i> Undo</button>
        <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
        <button style="display:none" class="btn topnavbt buttonload_approve" > <i class="fa fa-refresh fa-spin"></i> {{Session::get('approve')}}</button>
        <button class="btn topnavbt" id="btnApprove" onclick="saveAction('approve')" {{ (isset($objRights->APPROVAL1) || isset($objRights->APPROVAL2) || isset($objRights->APPROVAL3) || isset($objRights->APPROVAL4) || isset($objRights->APPROVAL5)) &&  ($objRights->APPROVAL1||$objRights->APPROVAL2||$objRights->APPROVAL3||$objRights->APPROVAL4||$objRights->APPROVAL5) == 1 ? '' : 'disabled'}} ><i class="fa fa-thumbs-o-up"></i> Approved</button>
        <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
        <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
    </div>
  </div>
</div>
 
<form id="form_data" method="POST"  >
 
    @csrf
    <div class="container-fluid filter">
      <div class="inner-form"> 
        
      <div class="row">
          <div class="col-lg-2 pl"><p>Doc No*</p></div>
          <div class="col-lg-2 pl">
              <input {{$ActionStatus}} type="hidden"  name="DOC_ID"     id="DOC_ID" value="{{isset($HDR->VCS_ID)?$HDR->VCS_ID:''}}" >
              <input {{$ActionStatus}} type="text"    name="DOC_NO"  id="DOC_NO"  value="{{isset($HDR->DOC_NO)?$HDR->DOC_NO:''}}"  class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase"  >
            </div>
                            
          <div class="col-lg-2 pl"><p>Doc Date*</p></div>
          <div class="col-lg-2 pl">
              <input {{$ActionStatus}} type="date" name="DOC_DATE" id="DOC_DATE" value="{{isset($HDR->DOC_DATE)?$HDR->DOC_DATE:''}}"  class="form-control" autocomplete="off" placeholder="dd/mm/yyyy" readonly >
            </div>

          <div class="col-lg-1 pl"><p>Existing Customer</p></div>
          <div class="col-lg-1 pl"> <input disabled type="radio" name="CUSTOMER_TYPE" value="EXIST" {{$ActionStatus}}  {{isset($HDR->CUSTOMER_TYPE) && $HDR->CUSTOMER_TYPE ==='EXIST'?'checked':''}} onchange="get_customer_type(this.value)" ></div>

          <div class="col-lg-1 pl"><p>New Customer</p></div>
          <div class="col-lg-1 pl"> <input disabled type="radio" name="CUSTOMER_TYPE" {{$ActionStatus}} value="NEW" {{isset($HDR->CUSTOMER_TYPE) && $HDR->CUSTOMER_TYPE ==='NEW'?'checked':''}} onchange="get_customer_type(this.value)" ></div>
          <input type="hidden" name="CUSTOMER_TYPE" value="{{isset($HDR->CUSTOMER_TYPE)?$HDR->CUSTOMER_TYPE:''}}" >
        </div>

        <div class="row">
          <div class="col-lg-2 pl"><p>Search</p></div>
          <div class="col-lg-2 pl"> 
            <input type="text" id="SEARCH_CUSTOMER" onclick="searchCustomerMaster()" {{$ActionStatus}} class="form-control" autocomplete="off" onkeypress="return isNumberKey(event,this)" readonly >
          </div>

          <div class="col-lg-1 pl"> 
            <i class="fa fa-search" onclick="searchCustomerMaster()" {{$ActionStatus}} style="cursor:pointer;margin-top:5px;"></i>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-2 pl"><p>Customer Name*</p></div>
          <div class="col-lg-2 pl"> 
            <input type="text" name="CUSTOMER_NAME" id="CUSTOMER_NAME" {{$ActionStatus}} value="{{isset($HDR->CUSTOMER_NAME)?$HDR->CUSTOMER_NAME:''}}" class="form-control" autocomplete="off" readonly>
            <input type="hidden" name="CUSTOMER_ID" id="CUSTOMER_ID" value="{{isset($HDR->CUSTOMER_ID)?$HDR->CUSTOMER_ID:''}}" class="form-control" autocomplete="off" >
          </div>

          <div class="col-lg-2 pl"><p>Date of Birth</p></div>
          <div class="col-lg-2 pl"> 
            <input type="date" name="DOB" id="DOB" {{$ActionStatus}} value="{{isset($HDR->DOB)?$HDR->DOB:''}}" class="form-control" autocomplete="off" >
          </div>

          <div class="col-lg-2 pl"><p>E-Mail Id*</p></div>
          <div class="col-lg-2 pl"> 
            <input type="text" name="EMAIL_ID" id="EMAIL_ID" {{$ActionStatus}} value="{{isset($HDR->EMAIL_ID)?$HDR->EMAIL_ID:''}}" class="form-control" autocomplete="off" >
          </div>

          
        </div>

        <div class="row">
          <div class="col-lg-2 pl"><p>Mobile No*</p></div>
          <div class="col-lg-2 pl"> 
            <input type="text" name="MOBILE_NO" id="MOBILE_NO" {{$ActionStatus}} value="{{isset($HDR->MOBILE_NO)?$HDR->MOBILE_NO:''}}" class="form-control" autocomplete="off" placeholder='Mobile No' maxlength="12"  onkeypress="return isNumberKey(event,this)" onkeyup="validate_mobile_no()" >
          </div>

          <div class="col-lg-2 pl"><p>Address*</p></div>
          <div class="col-lg-2 pl"> 
            <input type="text" name="ADDRESS" id="ADDRESS" {{$ActionStatus}} value="{{isset($HDR->ADDRESS)?$HDR->ADDRESS:''}}" class="form-control" autocomplete="off" >
          </div>

          <div class="col-lg-2 pl"><p>Anniversary Date</p></div>
          <div class="col-lg-2 pl"> 
            <input type="date" name="ANNIVERSARY_DATE" {{$ActionStatus}} id="ANNIVERSARY_DATE" value="{{isset($HDR->ANNIVERSARY_DATE)?$HDR->ANNIVERSARY_DATE:''}}" class="form-control" autocomplete="off" >
          </div>
        </div>

        <div class="row">
          <div class="col-lg-2 pl"><p>Country*</p></div>
          <div class="col-lg-2 pl"> 
            <input type="text" name="COUNTRY_NAME" id="COUNTRY_NAME" {{$ActionStatus}}  value="{{isset($HDR->COUNTRY_NAME)?$HDR->COUNTRY_NAME:''}}" class="form-control" autocomplete="off" onclick="getCountryMaster()" readonly >
            <input type="hidden" name="COUNTRY_ID" id="COUNTRY_ID" value="{{isset($HDR->COUNTRY_ID)?$HDR->COUNTRY_ID:''}}" class="form-control" autocomplete="off">
          </div>
        
          <div class="col-lg-2 pl"><p>State*</p></div>
          <div class="col-lg-2 pl"> 
            <input type="text" name="STATE_NAME" id="STATE_NAME" {{$ActionStatus}} value="{{isset($HDR->STATE_NAME)?$HDR->STATE_NAME:''}}" class="form-control" autocomplete="off" onclick="getStateMaster()" readonly >
            <input type="hidden" name="STATE_ID" id="STATE_ID" value="{{isset($HDR->STATE_ID)?$HDR->STATE_ID:''}}" class="form-control" autocomplete="off">
          </div>

          <div class="col-lg-2 pl"><p>City*</p></div>
          <div class="col-lg-2 pl"> 
            <input type="text" name="CITY_NAME" id="CITY_NAME" {{$ActionStatus}} value="{{isset($HDR->CITY_NAME)?$HDR->CITY_NAME:''}}" class="form-control" autocomplete="off" onclick="getCityMaster()" readonly >
            <input type="hidden" name="CITY_ID" id="CITY_ID" value="{{isset($HDR->CITY_ID)?$HDR->CITY_ID:''}}" class="form-control" autocomplete="off">
          </div>
        </div>

        <div class="row">
          <div class="col-lg-2 pl"><p>Pin Code*</p></div>
          <div class="col-lg-2 pl"> 
          <input type="text" name="PINCODE" id="PINCODE" {{$ActionStatus}} value="{{isset($HDR->PINCODE)?$HDR->PINCODE:''}}" class="form-control" autocomplete="off" maxlength="6" onkeypress="return isNumberKey(event,this)" >
          </div>

          <div class="col-lg-2 pl"><p>GST Type*</p></div>
          <div class="col-lg-2 pl"> 
            <select name="GST_TYPE" id="GST_TYPE" {{$ActionStatus}} class="form-control mandatory" autocomplete="off" >
              <option value="">Select</option>
              @foreach ($objGstTypeList as $index=>$GstType)
              <option {{isset($HDR->GST_TYPE) && $HDR->GST_TYPE == $GstType-> GSTID?'selected="selected"':''}} value="{{ $GstType-> GSTID }}">{{ $GstType->GSTCODE }} - {{ $GstType->DESCRIPTIONS }}</option>
              @endforeach
            </select>
          </div>
        
          <div class="col-lg-2 pl"><p>GSTIN</p></div>
          <div class="col-lg-2 pl"> 
          <input type="text" name="GST_IN" id="GST_IN" {{$ActionStatus}}  value="{{isset($HDR->GST_IN)?$HDR->GST_IN:''}}" class="form-control" autocomplete="off">
          </div>
        </div>

        <div class="row">
          <div class="col-lg-2 pl"><p>Landline No</p></div>
          <div class="col-lg-2 pl"> 
          <input type="text" name="LANDLINE_NO" id="LANDLINE_NO" {{$ActionStatus}} value="{{isset($HDR->LANDLINE_NO)?$HDR->LANDLINE_NO:''}}" class="form-control" autocomplete="off" onkeypress="return isNumberKey(event,this)" >
          </div>


        <div class="col-lg-2 pl"><p>Card No</p></div>
        <div class="col-lg-2 pl">      
          <input type="text" name="CARD_NAME" id="CARD_NAME" onclick="searchCardMaster()" class="form-control" value="{{isset($HDR->CARDNO)?$HDR->CARDNO:''}}" autocomplete="off" readonly>
          <input type="hidden" name="CARD_ID" id="CARD_ID" class="form-control" autocomplete="off" value="{{isset($HDR->CARDID_REF)?$HDR->CARDID_REF:''}}" >
        </div>

      <div class="col-lg-2 pl"><p>Validity Month</p></div>
      <div class="col-lg-2 pl">
      <input type="text" name="VALIDITY_MONTH" id="VALIDITY_MONTH" value="{{isset($HDR->VALIDITY_MONTH)?$HDR->VALIDITY_MONTH:''}}" {{$ActionStatus}} class="form-control" style="text-transform:uppercase">
      </div>

      
      
  </div>






  <div class="row">
      <div class="col-lg-2 pl"><p>Card Amount</p></div>
      <div class="col-lg-2 pl">
      <input type="text" name="TOTAL_CARD_AMOUNT" id="TOTAL_CARD_AMOUNT" {{$ActionStatus}}  readonly value="{{isset($HDR->CARD_AMT)?$HDR->CARD_AMT:''}}" class="form-control">
      </div>

      <div class="col-lg-2 pl"><p>Validity From</p></div>
      <div class="col-lg-2 pl">
      <input type="date" name="VALIDITY_START_FROM" {{$ActionStatus}}  id="VALIDITY_START_FROM" value="{{isset($HDR->VALIDITY_FROM)?$HDR->VALIDITY_FROM:''}}" class="form-control" >
      </div>

      <div class="col-lg-2 pl"><p>Validity Till</p></div>
      <div class="col-lg-2 pl">
      <input type="date" name="VALIDITY_START_TO" id="VALIDITY_START_TO" {{$ActionStatus}}  value="{{isset($HDR->VALIDITY_TO)?$HDR->VALIDITY_TO:''}}" class="form-control">
      </div>

      

      </div>

      <div class="row">
        <br/>
        <div class="col-lg-9 pl"></div>
        <div class="col-lg-1 pl"><p>Tax Amount</p></div>
        <div class="col-lg-2 pl">
        <input type="text" name="TOTAL_TAX_AMOUNT" id="TOTAL_TAX_AMOUNT"  {{$ActionStatus}}  readonly value="{{isset($HDR->TAX_AMT)?$HDR->TAX_AMT:''}}" class="form-control" style="text-transform:uppercase">
        </div>
      </div>
      
      <div class="row">


      <div class="col-lg-9 pl"></div>
        <div class="col-lg-1 pl"><p>Discount</p></div>
        <div class="col-lg-2 pl">
        <input type="text" name="TOTAL_DISCOUONT_AMOUNT" id="TOTAL_DISCOUONT_AMOUNT" {{$ActionStatus}}  readonly value="{{isset($HDR->DISCOUNT_AMT)?$HDR->DISCOUNT_AMT:''}}" class="form-control" style="text-transform:uppercase">
        </div>
        </div>
        <div class="row">
        <div class="col-lg-9 pl"></div>
        <div class="col-lg-1 pl"><p>Net Amount</p></div>
        <div class="col-lg-2 pl">
        <input type="text" name="TOTAL_NET_AMOUNT" id="TOTAL_NET_AMOUNT"  readonly value="{{isset($HDR->NET_AMT)?$HDR->NET_AMT:''}}"  {{$ActionStatus}}  class="form-control">
        </div>
        </div>
        <div class="row">
        <div class="col-lg-9 pl"></div>
        <div class="col-lg-1 pl"><p>Paid Amount</p></div>
        <div class="col-lg-2 pl">
        <input type="text" name="TOTAL_PAID_AMOUNT" id="TOTAL_PAID_AMOUNT"  readonly value="{{isset($HDR->PAID_AMT)?$HDR->PAID_AMT:''}}"  {{$ActionStatus}}  class="form-control">
        </div>
      </div>
   


      </div>

      <div class="container-fluid purchase-order-view">
        <div class="row">
          <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#Tax" id="TAX_TAB">Tax</a></li>
            <li><a data-toggle="tab" href="#Payment" id="PAYMENT_TAB">Payment</a></li>
            <li><a data-toggle="tab" href="#udf" id="UDF_TAB">UDF</a></li>
          </ul>
                                              
          <div class="tab-content">
           

          <div id="Tax" class="tab-pane fade in active">
            <div class="table-responsive table-wrapper-scroll-y" style="height:280px;margin-top:10px;">
              <table class="display nowrap table table-striped table-bordered itemlist w-200" style="height:auto !important;width:60%">
                <thead>
                  <tr>
                    <th>Tax Description</th>
                    <th>Tax (%)</th>
                    <th>Tax Amount</th>
                    <th>Action</th>
                  </tr>
                </thead>
							  <tbody id="tax_data">
                  @if(isset($TAX) && !empty($TAX))
                  @foreach($TAX as $key=>$row)
                  <tr class="taxRow">
                    <td><input  type="text" name="TAX_NAME[]"   id="TAX_NAME_{{$key}}"    value="{{isset($row->TAX_NAME)?$row->TAX_NAME:''}}"     class="form-control"  autocomplete="off" {{$ActionStatus}} /></td>
                    <td><input  type="text" name="TAX_PER[]"    id="TAX_PER_{{$key}}"     value="{{isset($row->TAX_PER)?$row->TAX_PER:''}}"       class="form-control"  autocomplete="off" onkeyup="getTaxAmount(this.id,this.value)" onkeypress="return isNumberDecimalKey(event,this)" {{$ActionStatus}} /></td>
                    <td><input  type="text" name="TAX_AMOUNT[]" id="TAX_AMOUNT_{{$key}}"  value="{{isset($row->TAX_AMOUNT)?$row->TAX_AMOUNT:''}}" class="form-control"  autocomplete="off" readonly {{$ActionStatus}} /></td>
                    <td align="center" >
                      <button class="btn add material" title="add" data-toggle="tooltip" type="button" {{$ActionStatus}} ><i class="fa fa-plus"></i></button>
                      <button class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button" {{$ActionStatus}} ><i class="fa fa-trash" ></i></button>
                    </td>
                  </tr>
                  @endforeach
                  @endif
                </tbody>
					    </table>
					  </div>	
				  </div>

          <div id="Payment" class="tab-pane fade in ">
            <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:280px;margin-top:10px; width: 786px"  >
              <table id="example3" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                <thead id="thead1"  style="position: sticky;top: 0">
                  <tr>
                    <th>Select Mode</th>
                    <th>No/Description</th>
                    <th>Amount</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @if(isset($PAY) && !empty($PAY))
                  @foreach($PAY as $key=>$row)
                  <tr class="participantRow2">
                    <td>
                      <select {{$ActionStatus}} name="PAYMENT_TYPE[]" id="PAYMENT_TYPE_{{$key}}" class="form-control" onchange="getDocType(this.id);">
                        <option  value="">Select</option>
                        <option {{isset($row->PAYMENT_TYPE) && $row->PAYMENT_TYPE =='Cash'?'selected="selected"':''}} value="Cash">Cash</option>
                        
                       <!-- <option {{isset($row->PAYMENT_TYPE) && $row->PAYMENT_TYPE =='Value Card'?'selected="selected"':''}} value="Value Card">Value Card</option>-->
                        <option {{isset($row->PAYMENT_TYPE) && $row->PAYMENT_TYPE =='Credit Card'?'selected="selected"':''}} value="Credit Card">Credit Card</option> 
                        <option {{isset($row->PAYMENT_TYPE) && $row->PAYMENT_TYPE =='FOC'?'selected="selected"':''}} value="FOC">FOC</option>
                        <option {{isset($row->PAYMENT_TYPE) && $row->PAYMENT_TYPE =='UPI'?'selected="selected"':''}} value="UPI">UPI</option>
                      </select>
                    </td>

                    <td><input        type="text"   name="DESCRIPTION[]"  id="DESCRIPTION_{{$key}}" value="{{isset($row->DESCRIPTION)?$row->DESCRIPTION:''}}" class="form-control"  autocomplete="off"  onclick="getValueCardMaster(this.id)" {{isset($row->PAYMENT_TYPE) && $row->PAYMENT_TYPE =='Value Card'?'readonly':''}} {{$ActionStatus}} /></td>
                    <td hidden><input type="hidden" name="VALUEID_REF[]"  id="VALUEID_REF_{{$key}}" value="{{isset($row->VALUEID_REF)?$row->VALUEID_REF:''}}" class="form-control"  autocomplete="off" {{$ActionStatus}} /></td>
                    <td><input        type="text"   name="PAID_AMT[]"     id="PAID_AMT_{{$key}}"    value="{{isset($row->PAID_AMT)?$row->PAID_AMT:''}}"       class="form-control two-digits" onkeyup="getPaymentAmount(this.id,this.value)" onkeypress="return isNumberDecimalKey(event,this)"  onfocusout="dataDec(this,'2')"   autocomplete="off" {{$ActionStatus}}  /></td>
                             
                    <td align="center" >
                      <button class="btn add ainvoice" title="add" data-toggle="tooltip" type="button" {{$ActionStatus}}><i class="fa fa-plus"></i></button>
                      <button class="btn remove dinvoice" title="Delete" data-toggle="tooltip" type="button" {{$ActionStatus}}><i class="fa fa-trash" ></i></button>
                    </td>
                  </tr>
                  @endforeach
                  @endif     
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
                        $strinp = '<input '.$ActionStatus.' type="date" placeholder="dd/mm/yyyy" name="'.$dynamicid.'" id="'.$dynamicid.'" value="'.$udf_value.'" class="form-control" value="" /> ';       
                      }
                      else if($chkvaltype=='time'){
                        $strinp= '<input '.$ActionStatus.' type="time" placeholder="h:i" name="'.$dynamicid.'" id="'.$dynamicid.'" value="'.$udf_value.'" class="form-control"  value=""/> ';
                      }
                      else if($chkvaltype=='numeric'){
                        $strinp = '<input '.$ActionStatus.' type="text" name="'.$dynamicid. '" id="'.$dynamicid.'" value="'.$udf_value.'" class="form-control" value=""  autocomplete="off" /> ';
                      }
                      else if($chkvaltype=='text'){
                        $strinp = '<input '.$ActionStatus.' type="text" name="'.$dynamicid. '" id="'.$dynamicid.'" value="'.$udf_value.'" class="form-control" value=""  autocomplete="off" /> ';
                      }
                      else if($chkvaltype=='boolean'){

                        $boolval = ''; 
                        if($udf_value =='on' || $udf_value  =='1'){
                          $boolval="checked";
                        }

                        $strinp = '<input '.$ActionStatus.' type="checkbox" name="'.$dynamicid. '" id="'.$dynamicid.'"  '.$boolval.' class=""  /> ';
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

                        $strinp = '<select '.$ActionStatus.' name="'.$dynamicid.'" id="'.$dynamicid.'" class="form-control" >'.$opts.'</select>' ;
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

//===========================================================================================================================================================

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
  $('#DOC_NO').val("{{$HDR->DOC_DATE}}");
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
loadTax(state_id);
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
$("#modal_th1").text('Code');
$("#modal_th2").text('Desc');
$("#modal").show();
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
  $("#modal_th1").text('Code');
  $("#modal_th2").text('Desc');
  $("#modal").show();
}

function bindCountryMaster(data){
  var code  = $("#"+data.id).data("code");
  var desc  = $("#"+data.id).data("desc");

  $("#COUNTRY_ID").val(data.value);
  $("#COUNTRY_NAME").val(code+' - '+desc);
  
  $("#text1").val(''); 
  $("#text2").val(''); 
  $("#STATE_NAME").val(''); 
  $("#CITY_NAME").val(''); 
  $("#STATE_ID").val(''); 
  $("#CITY_ID").val(''); 
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
    $("#modal_th1").text('Code');
    $("#modal_th2").text('Desc');
    $("#modal").show();

  }
}

function bindStateMaster(data){
  var code  = $("#"+data.id).data("code");
  var desc  = $("#"+data.id).data("desc");

  $("#STATE_ID").val(data.value);
  $("#STATE_NAME").val(code+' - '+desc);
  loadTax(data.value);
  
  $("#text1").val(''); 
  $("#text2").val(''); 
  $("#CITY_NAME").val(''); 
  $("#CITY_ID").val(''); 
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
    $("#modal_th1").text('Code');
    $("#modal_th2").text('Desc');
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
  $("#modal_th1").text('Code');
  $("#modal_th2").text('Desc');
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




function getValueCardMaster(id){
  var PAYMENT_TYPE=$("#PAYMENT_TYPE_"+id.split('_').pop(0)).val(); 
  if(PAYMENT_TYPE !="Value Card"){
    return false; 

  }

  var DOC_DATE    = $("#DOC_DATE").val(); 
  var CUSTOMER_ID = $("#CUSTOMER_ID").val(); 
  
  $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  $.ajax({
    url:'{{route("transaction",[$FormId,"getValueCardMaster"])}}',
    type:'POST',
    data:{DOC_DATE:DOC_DATE,CUSTOMER_ID:CUSTOMER_ID},

    success:function(data) {
      var html = '';
      if(data.length > 0){
        $.each(data, function(key, value) {
          html +='<tr>';
          html +='<td style="width:10%;text-align:center;" ><input type="checkbox" id="key_'+key+'" value="'+value.DATA_ID+'" onChange="bindValueCardMaster(this)" data-code="'+value.DATA_CODE+'" data-desc="'+value.DATA_DESC+'" data-rowid="'+id+'" ></td>';
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

  $("#modal_title").text('Value Card List');
  $("#modal_th1").text('Card No');
  $("#modal_th2").text('Balance');
  $("#modal").show();
} 


function bindValueCardMaster(data){
  var code  = $("#"+data.id).data("code");
  var desc  = $("#"+data.id).data("desc");
  var rowid  = $("#"+data.id).data("rowid");







    var CheckExist_valueid = [];

    $('#example3').find('.participantRow2').each(function(){

      if($(this).find('[id*="VALUEID_REF"]').val() != ''){

        var valueid  = $(this).find('[id*="VALUEID_REF"]').val();
    
          if(valueid!=''){
            CheckExist_valueid.push(valueid);
          }

      }
    });

    if($.inArray(data.value, CheckExist_valueid) !== -1 ){
      $(this).find('[id*="txtSTR_popup"]').val();
      $(this).find('[id*="STRID_REF"]').val();
      $("#VALUEID_REF_"+rowid.split('_').pop(0)).val('');
      $("#DESCRIPTION_"+rowid.split('_').pop(0)).val('');
      $("#FocusId").val("#DESCRIPTION_"+rowid);
      $("#alert").modal('show');
      $("#AlertMessage").text('Value Master already exist.');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
      $("#modal").hide(); 
      return false;
    }
    else{
    $("#VALUEID_REF_"+rowid.split('_').pop(0)).val(data.value);
    $("#DESCRIPTION_"+rowid.split('_').pop(0)).val(code);

    }
  
  $("#text1").val(''); 
  $("#text2").val(''); 
  $("#modal_body").html('');  
  var CheckExist_valueid = [];
  $("#modal").hide(); 
}



//===========================================================================================================================================================



function loadTax(CUSTOMER_STATEID){

$.ajaxSetup({
   headers: {
     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
   }
 });

 $.ajax({
   url:'{{route("transaction",[$FormId,"loadTax"])}}',
   type:'POST',
   data:{CUSTOMER_STATEID:CUSTOMER_STATEID},
   success:function(data) {
     var html = '';

     if(data.length > 0){
       $.each(data, function(key, value){

         var TAX_PER         = parseFloat(value.TAX_RATE);
         var PACKAGE_TOTAL   = $("#TOTAL_CARD_AMOUNT").val() !=''?parseFloat($("#TOTAL_CARD_AMOUNT").val()):0;
         var PACKAGE_TOTAL   = PACKAGE_TOTAL !=''?parseFloat(PACKAGE_TOTAL):0;
         var TOTAL_AMOUNT    = ((PACKAGE_TOTAL*TAX_PER)/100);

         html +='<tr class="taxRow">';
         html +='<td><input        type="text" name="TAX_NAME[]"  id="TAX_NAME_'+key+'" value="'+value.TAX_TYPE+'"  class="form-control"  autocomplete="off" /></td>';
         html +='<td><input        type="text" name="TAX_PER[]"  id="TAX_PER_'+key+'" value="'+value.TAX_RATE+'"  class="form-control"  autocomplete="off" onkeyup="getTaxAmount(this.id,this.value)" onkeypress="return isNumberDecimalKey(event,this)" /></td>';
         html +='<td><input        type="text" name="TAX_AMOUNT[]" id="TAX_AMOUNT_'+key+'" value="'+TOTAL_AMOUNT+'" class="form-control"  autocomplete="off" readonly /></td>';
         html +='<td align="center" >';
         html +='<button class="btn add material" title="add" data-toggle="tooltip" type="button" ><i class="fa fa-plus"></i></button>';
         html +='<button class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button>';
         html +='</td>';
         html +='</tr>';

       });
     }
     else{
       html +='<tr class="taxRow">';
       html +='<td><input        type="text" name="TAX_NAME[]"  id="TAX_NAME_0"   class="form-control"  autocomplete="off" /></td>';
       html +='<td><input        type="text" name="TAX_PER[]"  id="TAX_PER_0"   class="form-control"  autocomplete="off" onkeyup="getTaxAmount(this.id,this.value)" onkeypress="return isNumberDecimalKey(event,this)" /></td>';
       html +='<td><input        type="text" name="TAX_AMOUNT[]" id="TAX_AMOUNT_0"  class="form-control"  autocomplete="off" readonly /></td>';
       html +='<td align="center" >';
       html +='<button class="btn add material" title="add" data-toggle="tooltip" type="button" ><i class="fa fa-plus"></i></button>';
       html +='<button class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button>';
       html +='</td>';
       html +='</tr>';
     }

     $("#tax_data").html(html);
     get_total_amount('TAX_AMOUNT','TOTAL_TAX_AMOUNT');
   },
   error: function (request, status, error) {
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn").show();
     $("#AlertMessage").text(request.responseText);
     $("#alert").modal('show');
     $("#OkBtn").focus();
     highlighFocusBtn('activeOk');
     $("#package_data").html('');                       
   },
 });
}




$("#Tax").on('click', '.remove', function(){
    var rowCount = $(this).closest('table').find('.taxRow').length;
    if (rowCount > 1) {
    $(this).closest('.taxRow').remove();  
    get_total_amount('TAX_AMOUNT','TOTAL_TAX_AMOUNT');  
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

$("#Tax").on('click', '.add', function(){
  var $tr = $(this).closest('table');
  var allTrs = $tr.find('.taxRow').last();
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

$("#Payment").on('click', '.dinvoice', function(){
    var rowCount = $(this).closest('table').find('.participantRow2').length;
    if (rowCount > 1) {
    $(this).closest('.participantRow2').remove();   
    get_total_amount('PAID_AMT','TOTAL_PAID_AMOUNT');   
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

$("#Payment").on('click', '.ainvoice', function(){
  var $tr = $(this).closest('table');
  var allTrs = $tr.find('.participantRow2').last();
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
  $clone.find('.dinvoice').removeAttr('disabled'); 
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

for (var i = 0; i < document.getElementsByName('PAYMENT_TYPE[]').length; i++) {
  var payment_type = $.trim(document.getElementsByName('PAYMENT_TYPE[]')[i].value);
  if(payment_type ===""){
    flag_status.push('false');
    flag_focus    = document.getElementsByName('PAYMENT_TYPE[]')[i].id;
    flag_message  = 'Please select mode';
    flag_tab_type = 'PAYMENT_TAB';
  }
  else if($.trim(document.getElementsByName('DESCRIPTION[]')[i].value) ===""){
    flag_status.push('false');
    flag_focus    = document.getElementsByName('DESCRIPTION[]')[i].id;
    flag_message  = 'Please enter No/Description';
    flag_tab_type = 'PAYMENT_TAB';
  }
  else if($.trim(document.getElementsByName('PAID_AMT[]')[i].value) ===""){
    flag_status.push('false');
    flag_focus    = document.getElementsByName('PAID_AMT[]')[i].id;
    flag_message  = 'Please enter amount';
    flag_tab_type = 'PAYMENT_TAB';
  }
  else if(jQuery.inArray(payment_type, flag_exist) !== -1){
    flag_status.push('false');
    flag_focus    = document.getElementsByName('PAYMENT_TYPE[]')[i].id;
    flag_message  = 'This payment mode is already exist';
    flag_tab_type = 'PAYMENT_TAB';
  }
  flag_exist.push(payment_type);
}

for (var i = 0; i < document.getElementsByName('TAX_NAME[]').length; i++) {
  var taxname = $.trim(document.getElementsByName('TAX_NAME[]')[i].value);
  
  if(taxname !=""){
    if($.trim(document.getElementsByName('TAX_PER[]')[i].value) ===""){
      flag_status.push('false');
      flag_focus    = document.getElementsByName('TAX_PER[]')[i].id;
      flag_message  = 'Please enter tax';
      flag_tab_type = 'TAX_TAB';
    }
    else if(jQuery.inArray(taxname, flag_exist) !== -1){
      flag_status.push('false');
      flag_focus    = document.getElementsByName('TAX_NAME[]')[i].id;
      flag_message  = 'This tax is already exist';
      flag_tab_type = 'TAX_TAB';
    }

    flag_exist.push(taxname);
  }
}



if($.trim($("#DOC_NO").val()) ===""){
  $("#FocusId").val('DOC_NO');        
  $("#YesBtn").hide();
  $("#NoBtn").hide();
  $("#OkBtn1").show();
  $("#AlertMessage").text('Please enter document no.');
  $("#alert").modal('show');
  $("#OkBtn1").focus();
  return false;
}
else if($.trim($("#DOC_DATE").val()) ===""){
  $("#FocusId").val('DOC_DATE');        
  $("#YesBtn").hide();
  $("#NoBtn").hide();
  $("#OkBtn1").show();
  $("#AlertMessage").text('Please select document date.');
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
else if($('input[name="CUSTOMER_TYPE"]:checked').val() == "NEW" && exist_customer_by_mobile_no($.trim($("#MOBILE_NO").val()),@json(isset($HDR->CUSTOMER_ID)?$HDR->CUSTOMER_ID:'')) > 0 ){
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
else if($.trim($("#CARD_ID").val()) ===""){
  $("#FocusId").val('CARD_NAME');        
  $("#YesBtn").hide();
  $("#NoBtn").hide();
  $("#OkBtn1").show();
  $("#AlertMessage").text('Please Select Card No.');
  $("#alert").modal('show');
  $("#OkBtn1").focus();
  return false;
}
else if($.trim($("#VALIDITY_MONTH").val()) ===""){
  $("#FocusId").val('VALIDITY_MONTH');        
  $("#YesBtn").hide();
  $("#NoBtn").hide();
  $("#OkBtn1").show();
  $("#AlertMessage").text('Please Enter Validity Month.');
  $("#alert").modal('show');
  $("#OkBtn1").focus();
  return false;
}
else if($.trim($("#VALIDITY_START_FROM").val()) ===""){
  $("#FocusId").val('VALIDITY_START_FROM');        
  $("#YesBtn").hide();
  $("#NoBtn").hide();
  $("#OkBtn1").show();
  $("#AlertMessage").text('Please Select Validity From.');
  $("#alert").modal('show');
  $("#OkBtn1").focus();
  return false;
}
else if($.trim($("#VALIDITY_START_TO").val()) ===""){
  $("#FocusId").val('VALIDITY_START_TO');        
  $("#YesBtn").hide();
  $("#NoBtn").hide();
  $("#OkBtn1").show();
  $("#AlertMessage").text('Please Select Validity Till.');
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
else if(parseFloat($.trim($("#TOTAL_NET_AMOUNT").val())) !=parseFloat($.trim($("#TOTAL_PAID_AMOUNT").val()))){
  $("#PAYMENT_TAB").click();
  $("#FocusId").val('TOTAL_PAID_AMOUNT');        
  $("#YesBtn").hide();
  $("#NoBtn").hide();
  $("#OkBtn1").show();
  $("#AlertMessage").text('Paid amount should be equal of net amount.');
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
  var trnsoForm = $("#form_data");
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

function get_total_amount(TEXTNAME,TEXTID){
var total=0;
  var input = document.getElementsByName(TEXTNAME+'[]');
  var tsid=[];
  for (var i = 0; i < input.length; i++) {
      var a = input[i];

      var amount  = $.trim(a.value) !=''?parseFloat(a.value):0
      var total   = total+amount;
  }

  $("#"+TEXTID).val(parseFloat(total).toFixed(2));


  var packageAmount   = $("#TOTAL_CARD_AMOUNT").val();
  var discountAmount  = $("#TOTAL_DISCOUONT_AMOUNT").val();
  var taxAmount       = $("#TOTAL_TAX_AMOUNT").val();

  packageAmount       = packageAmount !=''?parseFloat(packageAmount):0;
  discountAmount      = discountAmount !=''?parseFloat(discountAmount):0;
  taxAmount           = taxAmount !=''?parseFloat(taxAmount):0;

  var afterDiscount   = (packageAmount-discountAmount);
  var totalAmount     = (afterDiscount+taxAmount);
  $("#TOTAL_NET_AMOUNT").val(parseFloat(totalAmount).toFixed(2));
  $("#PAID_AMT_0").val(parseFloat(totalAmount).toFixed(2));
  $("#TOTAL_PAID_AMOUNT").val(parseFloat(totalAmount).toFixed(2));
}


function getTaxAmount(textid,value){
  var textid          = textid.split('_').pop();
  var TAX_PER         = $.trim(value) !=''?parseFloat(value):0
  var PACKAGE_TOTAL   = $("#TOTAL_CARD_AMOUNT").val();
  var PACKAGE_TOTAL   = PACKAGE_TOTAL !=''?parseFloat(PACKAGE_TOTAL):0;
  var TOTAL_AMOUNT    = ((PACKAGE_TOTAL*TAX_PER)/100);

  $("#TAX_AMOUNT_"+textid).val(TOTAL_AMOUNT);
  get_total_amount('TAX_AMOUNT','TOTAL_TAX_AMOUNT');  
}

function getDocType(id){
  var rowid = id.split('_').pop(0);
  var PAYMENTTYPE = $("#PAYMENT_TYPE_"+rowid).val(); 

  if(PAYMENTTYPE=="Value Card"){
    $("#DESCRIPTION_"+rowid).prop("readonly", true);
    $("#PAID_AMT_"+rowid).val("");  
    $("#DESCRIPTION_"+rowid).val("");
  }else{
    $("#DESCRIPTION_"+rowid).prop("readonly", false);
    $("#VALUEID_REF_"+rowid).val("");
    $("#DESCRIPTION_"+rowid).val("");
    $("#PAID_AMT_"+rowid).val("");
  }
  get_total_amount('PAID_AMT','TOTAL_PAID_AMOUNT'); 
}

function dataDec(data,no){
  var text_value  = data.value !=''?parseFloat(data.value).toFixed(no):'';
  $("#"+data.id).val(text_value);
}

function getValueCardMaster(id){
  var PAYMENT_TYPE=$("#PAYMENT_TYPE_"+id.split('_').pop(0)).val(); 
  if(PAYMENT_TYPE !="Value Card"){
    return false; 

  }

  $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  $.ajax({
    url:'{{route("transaction",[$FormId,"getValueCardMaster"])}}',
    type:'POST',

    success:function(data) {
      var html = '';
      if(data.length > 0){
        $.each(data, function(key, value) {
          html +='<tr>';
          html +='<td style="width:10%;text-align:center;" ><input type="checkbox" id="key_'+key+'" value="'+value.DATA_ID+'" onChange="bindValueCardMaster(this)" data-code="'+value.DATA_CODE+'" data-desc="'+value.DATA_DESC+'" data-rowid="'+id+'" ></td>';
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

  $("#modal_title").text('Value Card List');
  $("#modal_th1").text('Code');
  $("#modal_th2").text('Description');
  $("#modal").show();
}

function bindValueCardMaster(data){

  var code    = $("#"+data.id).data("code");
  var desc    = $("#"+data.id).data("desc");
  var rowid   = $("#"+data.id).data("rowid");

  var CheckExist_valueid = [];

  $('#example3').find('.participantRow2').each(function(){

    if($(this).find('[id*="VALUEID_REF"]').val() != ''){

      var valueid  = $(this).find('[id*="VALUEID_REF"]').val();
  
        if(valueid!=''){
          CheckExist_valueid.push(valueid);
        }

    }
  });

  if($.inArray(data.value, CheckExist_valueid) !== -1 ){
    
    $("#VALUEID_REF_"+rowid.split('_').pop(0)).val('');
    $("#DESCRIPTION_"+rowid.split('_').pop(0)).val('');
    $("#FocusId").val("#DESCRIPTION_"+rowid);
    $("#alert").modal('show');
    $("#AlertMessage").text('Value Master already exist.');
    $("#YesBtn").hide(); 
    $("#NoBtn").hide();  
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk');
    $("#modal").hide(); 
    return false;
  }
  else{
    $("#VALUEID_REF_"+rowid.split('_').pop(0)).val(data.value);
    $("#DESCRIPTION_"+rowid.split('_').pop(0)).val(code);
  }
  
  $("#text1").val(''); 
  $("#text2").val(''); 
  $("#modal_body").html('');  
  var CheckExist_valueid = [];
  $("#modal").hide(); 
}

function getPaymentAmount(textid,value){
  
  var tvalue      = 0.00;
  var totalvalue  = 0.00;  
  $('#Payment').find('.participantRow2').each(function(){
    tvalue      = $(this).find('[id*="PAID_AMT"]').val() !=''?$(this).find('[id*="PAID_AMT"]').val():0;
    totalvalue  = parseFloat(totalvalue) + parseFloat(tvalue);
    totalvalue  = parseFloat(totalvalue).toFixed(2);
  });

  $('#TOTAL_PAID_AMOUNT').val(totalvalue);

  // if($("#PAYMENT_TYPE_"+textid.split("_").pop(0)).val()=="Value Card"){
  // Get_Balance(textid,value);
  // get_total_amount('PAID_AMT','TOTAL_PAID_AMOUNT');  
  // }
  // get_total_amount('PAID_AMT','TOTAL_PAID_AMOUNT');  
}

function Get_Balance(textid,value){

  var DESCRIPTION =$('#DESCRIPTION_'+textid.split("_").pop(0));  
  var CARDID_REF =$("#VALUEID_REF_"+textid.split("_").pop(0)).val(); 

  if($('#VALUEID_REF_'+textid.split("_").pop(0)).val() ==""){
        
    $("#FocusId").val('DESCRIPTION_'+textid.split("_").pop(0) );        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select value card.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    $("#PAID_AMT_"+textid.split("_").pop(0)).val('');
    return false;
  }

  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  var balance = $.ajax({type: 'POST',
  url:'{{route("transaction",[$FormId,"Get_Card_Balance"])}}',
  async: false,
  dataType: 'json',
  data: {CARDID_REF:CARDID_REF},
  done: function(response) {return response;}}).responseText;
  if(parseFloat(balance) < parseFloat(value)){      
    $("#FocusId").val('PAID_AMT_'+textid.split("_").pop(0) );        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Amount should not be greater than current balance.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    $("#PAID_AMT_"+textid.split("_").pop(0)).val('');
    return false;
  }  
}



function resetTab(){
  $('#Payment').find('.participantRow2').each(function(){
    var rowcount = $(this).closest('table').find('.participantRow2').length;
    $(this).find('input:text').val('');
    $(this).find('input:hidden').val('');
    $(this).find('input:checkbox').prop('checked', false);

    if(rowcount > 1){
      $(this).closest('.participantRow2').remove();
      rowcount = parseInt(rowcount) - 1;

    }
  });

  $('#TOTAL_PAID_AMOUNT').val('');
  get_total_amount('PAID_AMT','TOTAL_PAID_AMOUNT');


  $('#Tax').find('.taxRow').each(function(){
    var rowcount = $(this).closest('table').find('.taxRow').length;
    $(this).find('[id*="TAX_PER"]').val('');
    $(this).find('[id*="TAX_AMOUNT"]').val('');
   // $(this).find('input:hidden').val('');


    if(rowcount > 1){
      $(this).closest('.taxRow').remove();
      rowcount = parseInt(rowcount) - 1;
      
    }
  });

  $('#TOTAL_TAX_AMOUNT').val('');
 // get_total_amount('PAID_AMT','TOTAL_PAID_AMOUNT');


}



function searchCardMaster(){

$.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$.ajax({
  url:'{{route("transaction",[$FormId,"searchCard"])}}',
  type:'POST',
  success:function(data) {
    var html = '';

    if(data.length > 0){
      $.each(data, function(key, value) {
        html +='<tr>';
        html +='<td style="width:10%;text-align:center;" ><input type="checkbox" id="key_'+key+'" value="'+value.DOC_ID+'" onChange="bindCardMaster(this)" data-code="'+value.DOC_NO+'" data-desc="'+value.DOC_DATE+'" data-cardamount="'+value.AMOUNT+'" data-discount="'+value.DISCOUNT_AMT+'" data-netamount="'+value.NET_AMT+'" data-validitymonth="'+value.VALIDITY_MON+'"></td>';
        html +='<td style="width:45%;" >'+value.DOC_NO+'</td>';
        html +='<td style="width:45%;" >'+value.DOC_DATE+'</td>';
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

$("#modal_title").text('Value Card Master');
$("#modal_th1").text('Card No');
$("#modal_th2").text('Amount');
$("#modal").show();

}

function bindCardMaster(data){
var code          = $("#"+data.id).data("code");
var desc          = $("#"+data.id).data("desc");

var cardamount       = $("#"+data.id).data("cardamount");
var discount    = $("#"+data.id).data("discount");
var netamount  = $("#"+data.id).data("netamount");
var validitymonth      = $("#"+data.id).data("validitymonth");


$("#CARD_ID").val(data.value);
$("#CARD_NAME").val(code);
$("#TOTAL_DISCOUONT_AMOUNT").val(discount);
$("#TOTAL_CARD_AMOUNT").val(cardamount);
$("#TOTAL_NET_AMOUNT").val(netamount);
$("#VALIDITY_MONTH").val(validitymonth);


$("#text1").val(''); 
$("#text2").val(''); 
$("#modal_body").html('');  
$("#modal").hide(); 
resetTab();
}

function validate_mobile_no(){
  if($('input[name="CUSTOMER_TYPE"]:checked').val() == "NEW" && $.trim($("#MOBILE_NO").val()).length >= 10 ){
    if(exist_customer_by_mobile_no($.trim($("#MOBILE_NO").val()),@json(isset($HDR->CUSTOMER_ID)?$HDR->CUSTOMER_ID:'')) > 0 ){
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
</script>
@endpush