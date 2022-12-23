@extends('layouts.app')
@section('content')

<div class="container-fluid topnav">
	<div class="row">
		<div class="col-lg-2"><a href="{{route('master',[5,'index'])}}" class="btn singlebt">Customer / Dealer Master</a></div>

		<div class="col-lg-10 topnav-pd">
		  <button  id="btnSelectedRows" class="btn topnavbt" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
      <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
      <button id="btnSaveItem"   class="btn topnavbt" tabindex="7" disabled="disabled"><i class="fa fa-save"></i> Save</button>
      <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
      <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
      <button class="btn topnavbt" id="btnUndo"  disabled="disabled"><i class="fa fa-undo"></i> Undo</button>
      <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
      <button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-lock"></i> Approved</button>
      <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
      <a href="{{route('home')}}" class="btn topnavbt"><i class="fa fa-power-off"></i> Exit</a>
		</div>
	</div>
</div>

<div class="container-fluid filter">
	<form id="add_form_data" method="POST"  > 
    @CSRF  
    {{isset($objMstCust->CID) ? method_field('PUT') : '' }}
		<div class="inner-form">

      <div class="row">
        <div class="col-lg-2 pl"><p>Type*</p></div>
				<div class="col-lg-2 pl">
				  <select name="TYPE" id="TYPE" class="form-control mandatory" tabindex="1" disabled >
								<option value="" >Select</option>
								<option {{isset($objMstCust->TYPE) && $objMstCust->TYPE =="CUSTOMER"?'selected="selected"':''}} value="CUSTOMER" >Customer</option>
                <option {{isset($objMstCust->TYPE) && $objMstCust->TYPE =="DEALER"?'selected="selected"':''}} value="DEALER" >Dealer</option>
          </select>
        </div>

        <div class="col-lg-2 pl"><p>Prospect</p></div>
				<div class="col-lg-2 pl">
          <input type="checkbox"   name="PROSPECT"  id="PROSPECT" {{$objMstCust->PROSPECT == 1 ? "checked" : ""}} value='{{$objMstCust->PROSPECT == 1 ? 1 : 0}}' disabled>
        </div>
        <label>	{{isset($objMstCust->PCODE) && $objMstCust->PCODE !=''?$objMstCust->PCODE:''}} {{isset($objMstCust->PSCTNAME) && $objMstCust->PSCTNAME !=''?'-'.$objMstCust->PSCTNAME:''}} </label>       
      </div>
    
			<div class="row">
				<div class="col-lg-2 pl"><p>Customer Code*</p></div>
				<div class="col-lg-2 pl">
          <label>	{{ $objMstCust->CCODE }} </label>
				</div>
			  
				<div class="col-lg-2 pl"><p>Name*</p></div>
				<div class="col-lg-2 pl"><label> {{ $objMstCust->NAME }} </label>				  
        </div>
				
				<div class="col-lg-2 pl"><p>Customer Legal Name*</p></div>
				<div class="col-lg-2 pl"><label> {{ $objMstCust->CUSTOMER_LEGAL_NAME }} </label>
				 
        </div>
			</div>
			
			<div class="row">
				<div class="col-lg-2 pl"><p>Customer Group*</p></div>
				<div class="col-lg-2 pl">
					<input type="text" name="CGID_REF_POPUP" id="CGID_REF_POPUP"  class="form-control mandatory" value="{{ $objCusGro->CGROUP }} - {{ $objCusGro->DESCRIPTIONS }}" required disabled tabindex="4" />
				</div>
			  
			  <div class="col-lg-2 pl"><p>OLD Ref Code</p></div>
			  <div class="col-lg-2 pl"><label>{{ $objMstCust->OLD_REFCODE }}</label>
			  </div>
			  
			  <div class="col-lg-2 pl"><p>GL*</p></div>
			  <div class="col-lg-2 pl">
				<input type="text" name="GLID_REF_POPUP" id="GLID_REF_POPUP" class="form-control mandatory" value="{{ $objOldGlList->GLCODE }} - {{ $objOldGlList->GLNAME }}" required disabled tabindex="6" />                
			  </div>
			  
			</div>
			
			<div class="row">
				<div class="col-lg-2 pl"><p>Registered Address Line 1*</p></div>
				<div class="col-lg-4 pl"><label>{{ $objMstCust->REGADDL1 }}</label>
        </div>
				
					<div class="col-lg-2 pl"><p>Registered Address Line 2</p></div>
				<div class="col-lg-4 pl"><label>{{ $objMstCust->REGADDL2 }}</label>
				</div>
			
			</div>
				
			<div class="row">
			
			<div class="col-lg-2 pl"><p>Country*</p></div>
			<div class="col-lg-2 pl">
				<input type="text" name="REGCTRYID_REF_POPUP" id="REGCTRYID_REF_POPUP" class="form-control mandatory" value="{{ $objRegCountry->CTRYCODE }} - {{ $objRegCountry->NAME }}" required disabled tabindex="9" />
                
			</div>
			
			<div class="col-lg-1 pl"><p>State*</p></div>
			<div class="col-lg-2 pl">
				<input type="text" name="REGSTID_REF_POPUP" id="REGSTID_REF_POPUP" class="form-control mandatory" value="{{ $objRegState->STCODE }} - {{ $objRegState->NAME }}" required readonly tabindex="10" />
			</div>
		
			<div class="col-lg-1 pl"><p>City*</p></div>
			<div class="col-lg-2 pl">
				<input type="text" name="REGCITYID_REF_POPUP" id="REGCITYID_REF_POPUP" class="form-control mandatory" value="{{ $objRegCity->CITYCODE }} - {{ $objRegCity->NAME }}"  required readonly tabindex="11" />
			</div>
			
				
			<div class="col-lg-1 pl"><p>Pincode</p></div>
			<div class="col-lg-1 pl"><label> {{ $objMstCust->REGPIN }} </label>
				
			</div>
		
    </div>

    <div class="row" id="div_commision" {{isset($objMstCust->TYPE) && $objMstCust->TYPE !== "DEALER"? 'hidden':''}}>
      <div class="col-lg-1 pl"><p>Commission(%)</p></div>
			<div class="col-lg-1 pl">
				<input type="text" name="COMMISION" id="COMMISION" class="form-control two-digits" value="{{ $objMstCust->COMMISION }}"   maxlength="6" tabindex="14"  autocomplete="off" />
			</div>
    </div>
    
    <div class="row">
      <div class="col-lg-2 pl"><p>De-Activated</p></div>
      <div class="col-lg-2 pl pr">
      <input type="checkbox"   name="DEACTIVATED"  id="deactive-checkbox_0" {{$objMstCust->DEACTIVATED == 1 ? "checked" : ""}}
       value='{{$objMstCust->DEACTIVATED == 1 ? 1 : 0}}' tabindex="7"  disabled>
      </div>
      
      <div class="col-lg-2 pl"><p>Date of De-Activated</p></div>
      <div class="col-lg-2 pl">
        <label>{{ (!is_null($objMstCust->DODEACTIVATED) && $objMstCust->DODEACTIVATED!='1900-01-01')? \Carbon\Carbon::parse($objMstCust->DODEACTIVATED)->format('Y-m-d') : ''   }}</label>
      </div>
    

    <div class="row"> 
      <div class="col-lg-2 pl"><p>Tax Calculation</p></div>
      <div class="col-lg-2 pl">
        <select name="TAX_CALCULATION" id="TAX_CALCULATION" class="form-control mandatory" disabled>
          <option {{isset($objMstCust->TAX_CALCULATION) && $objMstCust->TAX_CALCULATION =="BILL TO"?'selected="selected"':''}} value="BILL TO" >Bill To</option>
          <option {{isset($objMstCust->TAX_CALCULATION) && $objMstCust->TAX_CALCULATION =="SHIP TO"?'selected="selected"':''}} value="SHIP TO" >Ship To</option>
      </select>
      </div>
    </div>

    </div>
	</div>
		
		
	<div class="container-fluid">

		<div class="row">
			<ul class="nav nav-tabs">
				<li class="active"><a data-toggle="tab" href="#material">Contact</a></li>
				<li><a data-toggle="tab" href="#statutory">Statutory</a></li>
				<li><a data-toggle="tab" href="#poc">Point of Contact</a></li>
				<li><a data-toggle="tab" href="#bank">Bank</a></li>
				<li><a data-toggle="tab" href="#location">Buyer / Consignee</a></li>
        <li><a data-toggle="tab" href="#ALPSSpecific">{{isset($TabSetting->TAB_NAME) && $TabSetting->TAB_NAME !=''?$TabSetting->TAB_NAME:'Additional Info'}}</a></li>
				<li><a data-toggle="tab" href="#udf">UDF</a></li>
			</ul>

			<div class="tab-content">
				<div id="material" class="tab-pane fade in active">
					<div class="table-wrapper-scroll-x">
						<div class="row" style="margin-top:10px;">
								
							<div class="col-lg-2"><p>Corporate Address Line 1</p></div>
							<div class="col-lg-4"><label>{{ $objMstCust->CORPADDL1 }}</label>
								
							</div>
							
							<div class="col-lg-2"><p>Corporate Address Line 2</p></div>
							<div class="col-lg-4"><label>{{ $objMstCust->CORPADDL2 }}</label>
								
							</div>
							
						</div>
						
						<div class="row">
							<div class="col-lg-2"><p>Country</p></div>
							<div class="col-lg-2">	
								<input type="text" name="CORPCTRYID_REF_POPUP" id="CORPCTRYID_REF_POPUP" class="form-control" value="@isset($objCorpCountry){{ $objCorpCountry->CTRYCODE }} - {{ $objCorpCountry->NAME }} @endisset" disabled  />
								<input type="hidden" name="CORPCTRYID_REF" id="CORPCTRYID_REF" value="@isset($objCorpCountry){{ $objCorpCountry->CTRYID }}@endisset"/>
								<span class="text-danger" id="ERROR_CORPCTRYID_REF"></span>
							</div>
							
							<div class="col-lg-2"><p>State</p></div>
							<div class="col-lg-2">		
								<input type="text" name="CORPSTID_REF_POPUP" id="CORPSTID_REF_POPUP" value="@isset($objCorpState){{ $objCorpState->STCODE }} - {{ $objCorpState->NAME }}@endisset" class="form-control" disabled  />
								<input type="hidden" name="CORPSTID_REF" id="CORPSTID_REF" value="@isset($objCorpState){{ $objCorpState->STID }}@endisset" />
								<span class="text-danger" id="ERROR_CORPSTID_REF"></span>
							</div>
							
							<div class="col-lg-2"><p>City</p></div>
							<div class="col-lg-2">
								<input type="text" name="CORPCITYID_REF_POPUP" id="CORPCITYID_REF_POPUP" value="@isset($objCorpCity){{ $objCorpCity->CITYCODE }} - {{ $objCorpCity->NAME }}@endisset" class="form-control" readonly  />
								<input type="hidden" name="CORPCITYID_REF" id="CORPCITYID_REF" value="@isset($objCorpCity){{ $objCorpCity->CITYID }}@endisset" />
								<span class="text-danger" id="ERROR_CORPCITYID_REF"></span>
							</div>
					
						</div>
				
				
					<div class="row">
					
							
					<div class="col-lg-2"><p>Pincode</p></div>
					<div class="col-lg-2"><label>{{ $objMstCust->CORPPIN }}</label>
				
					</div>
					
						<div class="col-lg-2"><p>Email ID</p></div>
						<div class="col-lg-2"><label>{{ $objMstCust->EMAILID }}</label>
							
						</div>
						
						<div class="col-lg-2"><p>Website</p></div>
						<div class="col-lg-2"><label>{{ $objMstCust->WEBSITE }}</label>
							
						</div>
						
							
					</div>
					
					<div class="row">
					<div class="col-lg-2 "><p>Phone No</p></div>
						<div class="col-lg-2"><label>{{ $objMstCust->PHNO }}</label>
							
						</div>
						
						<div class="col-lg-2"><p>Mobile No</p></div>
						<div class="col-lg-2"><label>{{ $objMstCust->MONO }}</label>
							
						</div>
						
						<div class="col-lg-2"><p>Contact Person</p></div>
						<div class="col-lg-2 "><label>{{ $objMstCust->CPNAME }}</label>
							
						</div>
						
						
						
					</div>
					
					<div class="row">
					<div class="col-lg-2 "><p>Skype</p></div>
						<div class="col-lg-2"><label>{{ $objMstCust->SKYPEID }}</label>
						</div>
					</div>
					
			</div>
				</div>
				
				<div id="statutory" class="tab-pane fade"> 
				
					 <div class="table-wrapper-scroll-x" style="margin-top:10px;">
					
						<div class="row" >
					
						<div class="col-lg-2"><p>Industry Type</p></div>
						<div class="col-lg-2">
							<input type="text" name="INDSID_REF_POPUP" id="INDSID_REF_POPUP" class="form-control" value="@isset($objIndType){{ $objIndType->INDSCODE }} - {{ $objIndType->DESCRIPTIONS }}@endisset" disabled  />
						</div>
						
						<div class="col-lg-2"><p>Industry Vertical</p></div>
						<div class="col-lg-2">
							<input type="text" name="INDSVID_REF_POPUP" id="INDSVID_REF_POPUP" class="form-control"  value="@isset($objIndVer){{ $objIndVer->INDSVCODE }} - {{ $objIndVer->DESCRIPTIONS }}@endisset" disabled  />
						</div>
						
						<div class="col-lg-1"><p>Deals In</p></div>
						<div class="col-lg-2"><label>{{ $objMstCust->DEALSIN }}</label>
						</div>
						
						</div>
					
					<div class="row">
						
						<div class="col-lg-2"><p>GST Type*</p></div>
						<div class="col-lg-2">
							<select name="GSTTYPE" id="GSTTYPE" class="form-control mandatory" disabled >
								<option value="" selected >Select</option>
								
								@foreach ($objGstTypeList as $index=>$GstType)
								<option value="{{ $GstType-> GSTID }}" @if($objMstCust->GSTTYPE==$GstType-> GSTID) selected @endif) >{{ $GstType->GSTCODE }} - {{ $GstType->DESCRIPTIONS }}</option>
								@endforeach
	
							</select>
						</div>
						


						<div class="col-lg-2"><p>Default Currency*</p></div>
						<div class="col-lg-2">
							<select name="DEFCRID_REF" id="DEFCRID_REF"  class="form-control mandatory" disabled  >
								<option value="" selected >Select</option>
								@foreach ($objCurrencyList as $index=>$Currency)
                <option value="{{ $Currency-> CRID }}"  @if($objMstCust->DEFCRID_REF==$Currency-> CRID) selected @endif) >
                  {{ $Currency->CRCODE }} - {{ $Currency->CRDESCRIPTION }}</option>
								@endforeach
							</select>
						</div>
						
						<div class="col-lg-1"><p>GSTIN</p></div>
						<div class="col-lg-2"><label>{{ $objMstCust->GSTIN }}</label>
							
						</div>
						
						</div>
					
					
				
				<div class="row">
					<div class="col-lg-2"><p>Credit Limit</p></div>
						<div class="col-lg-2"><label>{{ $objMstCust->CREDITLIMIT }}</label>
						</div>
						
						<div class="col-lg-2"><p>CIN </p></div>
						<div class="col-lg-2"><label>{{ $objMstCust->CIN }}</label>
							
						</div>
						
						<div class="col-lg-1"><p>Credit Days </p></div>
						<div class="col-lg-1"><label>{{ $objMstCust->CREDITDAY }}</label>
						</div>
						
						
						

				</div>


				
				<div class="row">
				<div class="col-lg-2"><p>PAN No </p></div>
						<div class="col-lg-2"><label>{{ $objMstCust->PANNO }}</label>
						</div>
				<div class="col-lg-2"><p>MSME No </p></div>
						<div class="col-lg-2"><label>{{ $objMstCust->MSMENO }}</label>
						</div>
				<div class="col-lg-1"><p>Factory No </p></div>
						<div class="col-lg-2"><label>{{ $objMstCust->FACTORY_NO }}</label>
						</div>

				</div>

        <div class="row">
          <div class="col-lg-2"><p>TDS Applicable</p></div>
          <div class="col-lg-2">
            <input disabled type="checkbox" name="TDS_APPLICABLE" id="TDS_APPLICABLE" value="1" onChange="TdsApplicable()" {{isset($objMstCust->TDS_APPLICABLE) && $objMstCust->TDS_APPLICABLE ==1?'checked':''}} >
          </div>

          <div class="col-lg-2"><p>Exceptional for GST</p></div>
          <div class="col-lg-2">
            <input disabled type="checkbox" name="EXEGST" id="EXEGST" class="filter-none" value="1"   {{ isset($objMstCust->EXE_GST) && $objMstCust->EXE_GST =='1'?'checked':'' }} > 
          </div>
          
        </div>

        <div class="row TDS_ACTION" style="display:none;">
          <div class="col-lg-2"><p>Certificate Number</p></div>
          <div class="col-lg-2">
            <input disabled type="text" name="CERTIFICATE_NO" id="CERTIFICATE_NO" value="{{ $objMstCust->CERTIFICATE_NO }}" class="form-control"  maxlength="30"  autocomplete="off">
          </div>

          <div class="col-lg-2"><p>Expiry Date</p></div>
          <div class="col-lg-2">
            <input disabled type="date" name="EXPIRY_DT" id="EXPIRY_DT" value="{{ $objMstCust->EXPIRY_DT }}" class="form-control"   autocomplete="off">
          </div>
        </div>

        <div class="row TDS_ACTION" style="display:none;">
          <div class="col-lg-2"><p>Assessee Type</p></div>
          <div class="col-lg-2">
            <select disabled  name="ASSESSEEID_REF" id="ASSESSEEID_REF" class="form-control mandatory" autocomplete="off" onChange="getTdsCode(this.value)" >
            <option value="">Select</option>
              @if(!empty($objAssesseeTypeList))
              @foreach($objAssesseeTypeList as $key=>$val)
              <option {{isset($objMstCust->ASSESSEEID_REF) && $objMstCust->ASSESSEEID_REF ==$val->NOAID?'selected="selected"':''}} value="{{$val->NOAID}}">{{$val->NOA_CODE}} - {{$val->NOA_NAME}}</option>
              @endforeach
              @endif
            </select>
          </div>
          
          <div class="col-lg-2"><p>TDS Code</p></div>
          <div class="col-lg-2">
            <input disabled type="text" name="HOLDINGID_REF_POPUP" id="HOLDINGID_REF_POPUP" class="form-control mandatory" readonly  />
            <input type="hidden" name="HOLDINGID_REF" id="HOLDINGID_REF" value="{{ $objMstCust->HOLDINGID_REF }}" />
            <span class="text-danger" id="ERROR_HOLDINGID_REF"></span>
          </div>
        </div>
				
				
			</div>
			</div>
				
			<div id="poc" class="tab-pane fade">

				<div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:250px;margin-top:10px;" >
					 
					<table id="table1" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
					 
            <thead id="thead1"  style="position: sticky;top: 0">						
              <tr>
								<th>Person Name <input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1" value="{{ $objPOCCount }}"> </th>
								<th>Designation</th>
								<th>Mobile</th>
								<th>Email</th>
								<th>LL No</th>
								<th>Authority Level</th>
								<th>Birthday</th>
              </tr>
            </thead>
            <tbody>
              @if(!empty($objPOC))
                @foreach($objPOC as $key => $row)
                <tr  class="participantRow">
                  <td><input  class="form-control w-100" type="text" name="POC_NAME_{{ $key }}" id="POC_NAME_{{ $key }}" value="{{ $row->NAME }}" maxlength="100" disabled></td>
                  <td><input  class="form-control w-100" type="text" name="POC_DESIG_{{ $key }}" id="POC_DESIG_{{ $key }}" value="{{ $row->DESIG }}" maxlength="50" disabled ></td>
                  <td><input  class="form-control w-100" type="text" name="POC_MONO_{{ $key }}" id="POC_MONO_{{ $key }}" value="{{ $row->MONO }}"  maxlength="20" disabled></td>
                  <td><input  class="form-control w-100" type="text" name="POC_EMAIL_{{ $key }}" id="POC_EMAIL_{{ $key }}" value="{{ $row->EMAIL }}"  maxlength="50" disabled></td>
                  <td><input  class="form-control w-100" type="text" name="POC_LLNO_{{ $key }}" id="POC_LLNO_{{ $key }}" value="{{ $row->LLNO }}"  maxlength="20" disabled></td>
                  <td><input  class="form-control w-100" type="text" name="POC_AUTHLEVEL_{{ $key }}" id="POC_AUTHLEVEL_{{ $key }}" value="{{ $row->AUTHLEVEL }}"  maxlength="30" disabled ></td>
                  <td style="text-align:center;" ><input type="date" placeholder="dd/mm/yyyy"  name="POC_DOB_{{ $key }}" id="POC_DOB_{{ $key }}" value="{{ $row->DOB }}" class="form-control" disabled ></td>
                </tr>
                @endforeach
              @else
                <tr  class="participantRow">
                  <td><input  class="form-control w-100" type="text" name="POC_NAME_0" id="POC_NAME_0" maxlength="100" disabled></td>
                  <td><input  class="form-control w-100" type="text" name="POC_DESIG_0" id="POC_DESIG_0" maxlength="50" disabled ></td>
                  <td><input  class="form-control w-100" type="text" name="POC_MONO_0" id="POC_MONO_0" maxlength="20" disabled></td>
                  <td><input  class="form-control w-100" type="text" name="POC_EMAIL_0" id="POC_EMAIL_0" maxlength="50" disabled></td>
                  <td><input  class="form-control w-100" type="text" name="POC_LLNO_0" id="POC_LLNO_0" maxlength="20" disabled></td>
                  <td><input  class="form-control w-100" type="text" name="POC_AUTHLEVEL_0" id="POC_AUTHLEVEL_0" maxlength="30" disabled></td>
                  <td style="text-align:center;" ><input type="date" name="POC_DOB_0" id="POC_DOB_0" class="form-control" disabled></td>
                </tr>
              @endif
            </tbody>
          </table>
				</div>
			</div>
				
				<div id="bank" class="tab-pane fade">
				  <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar"  style="height:250px;margin-top:10px;" >
					
					<table id="table2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
            <thead id="thead1"  style="position: sticky;top: 0">
              <tr >
                <th>Bank Name <input class="form-control" type="hidden" name="Row_Count2" id ="Row_Count2" value="{{ $objBANKCount }}"></th>
                <th>IFSC</th>
                <th>Branch</th>
                <th>Account Type</th>
                <th>Account No</th>
              </tr>
            </thead>
            <tbody>
            @if(!empty($objBANK))
              @foreach($objBANK as $key => $row)
              <tr class="participantRow">
                <td><input  class="form-control w-100" type="text" name="BANK_NAME_{{$key}}" id="BANK_NAME_{{$key}}" maxlength="100" value="{{ $row->NAME }}" disabled></td>
                <td><input  class="form-control w-100" type="text" name="BANK_IFSC_{{$key}}" id="BANK_IFSC_{{$key}}" value="{{ $row->IFSC }}" maxlength="20" disabled></td>
                <td><input  class="form-control w-100" type="text" name="BANK_BRANCH_{{$key}}" id="BANK_BRANCH_{{$key}}" value="{{ $row->BRANCH }}" maxlength="100" disabled></td>
                <td><input  class="form-control w-100" type="text" name="BANK_ACTYPE_{{$key}}" id="BANK_ACTYPE_{{$key}}"  value="{{ $row->ACTYPE }}" maxlength="30" disabled></td>
                <td><input  class="form-control w-100" type="text" name="BANK_ACNO_{{$key}}" id="BANK_ACNO_{{$key}}" value="{{ $row->ACNO }}" maxlength="30" disabled ></td>
              </tr>
              @endforeach
            @else
              <tr class="participantRow">
                <td><input  class="form-control w-100" type="text" name="BANK_NAME_0" id="BANK_NAME_0" maxlength="100" disabled></td>
                <td><input  class="form-control w-100" type="text" name="BANK_IFSC_0" id="BANK_IFSC_0" maxlength="20" disabled></td>
                <td><input  class="form-control w-100" type="text" name="BANK_BRANCH_0" id="BANK_BRANCH_0" maxlength="100" disabled></td>
                <td><input  class="form-control w-100" type="text" name="BANK_ACTYPE_0" id="BANK_ACTYPE_0" maxlength="30" disabled></td>
                <td><input  class="form-control w-100" type="text" name="BANK_ACNO_0" id="BANK_ACNO_0" maxlength="30" disabled></td>
              </tr>
            @endif
            </tbody>
				  </table>
				 
			</div>
				</div>
				
				<div id="location" class="tab-pane fade">
				  <div class="table-responsive table-wrapper-scroll-y " style="height:260px;margin-top:10px;">
					
					<table id="table3" class="display nowrap table table-striped table-bordered itemlist itemlistscroll w-200" style="height:auto !important;">
            <thead id="thead1"  style="position: sticky;top: 0">
              <tr >
                <th>Buyer/Consignee Name <input class="form-control" type="hidden" name="Row_Count3" id ="Row_Count3" value="{{ $objLOCCount }}"></th>
                <th>Buyer/Consignee Address</th>
                <th>Country</th>
                <th>State</th>
                <th>City</th>
                <th>Pincode</th>
                <th>GSTIN</th>
                <th>Contact Person Name</th>
                <th>Designation</th>
                <th>Email ID</th>
                <th>Mobile No</th>
                <th>Special Instructions</th>
                <th>Bill To</th>
                <th>Default Billing</th>
                <th>Ship To</th>
                <th>Default Shipping</th>
              </tr>
            </thead>
            <tbody>
            @if(!empty($objLOC))
              @foreach($objLOC as $key => $row)
              <tr  class="participantRow">
                <td><input  class="form-control w-100" type="text" name="LOC_NAME_{{ $key }}" id="LOC_NAME_{{ $key }}" value="{{ $row->NAME }}"  maxlength="50" disabled></td>
                <td><input  class="form-control w-100" type="text" name="LOC_CADD_{{ $key }}" id="LOC_CADD_{{ $key }}" value="{{ $row->CADD }}" maxlength="200" disabled></td>

                <td><input  class="form-control w-100" type="text" name="LOC_CTRYID_REF_{{ $key }}" id="TXTLOC_CTRYID_REF_POPUP_{{ $key }}" value="{{ $row->COU_CTRYCODE }} - {{ $row->COU_NAME }}" maxlength="100" disabled></td>
                <td hidden><input  class="form-control w-100" type="text" name="HDNLOC_CTRYID_REF_{{ $key }}" id="HDNLOC_CTRYID_REF_POPUP_{{ $key }}" value="{{ $row->COU_CTRYID }}" maxlength="100" disabled></td>

                <td><input  class="form-control w-100" type="text" name="LOC_STID_REF_{{ $key }}" id="TXTLOC_STID_REF_POPUP_{{ $key }}" value="{{ $row->STA_STCODE }} - {{ $row->STA_NAME }}" maxlength="100" disabled></td>
                <td hidden><input  class="form-control w-100" type="text" name="HDNLOC_STID_REF_{{ $key }}" id="HDNLOC_STID_REF_POPUP_{{ $key }}" value="{{ $row->STA_STID }}" maxlength="100" disabled></td>

                <td><input  class="form-control w-100" type="text" name="LOC_CITYID_REF_{{ $key }}" id="TXTLOC_CITYID_REF_POPUP_{{ $key }}" value="{{ $row->CIT_CITYCODE }} - {{ $row->CIT_NAME }}" maxlength="100" disabled></td>
                <td hidden><input  class="form-control w-100" type="text" name="HDNLOC_CITYID_REF_{{ $key }}" value="{{ $row->CIT_CITYID }}" id="HDNLOC_CITYID_REF_POPUP_{{ $key }}" maxlength="100" disabled></td>


                <td><input  class="form-control w-100" type="text" name="LOC_PIN_{{ $key }}" id="LOC_PIN_{{ $key }}" maxlength="20" value="{{ $row->PIN }}" disabled></td>
                <td><input  class="form-control w-100" type="text" name="LOC_GSTIN_{{ $key }}" id="LOC_GSTIN_{{$key}}" maxlength="30" value="{{ $row->GSTIN }}"  disabled></td>
                <td><input  class="form-control w-100" type="text" name="LOC_CPNAME_{{ $key }}" id="LOC_CPNAME_{{$key}}" maxlength="30" value="{{ $row->CPNAME }}" disabled ></td>
                <td><input  class="form-control w-100" type="text" name="LOC_CPDESIGNATION_{{$key}}" id="LOC_CPDESIGNATION_{{$key}}" maxlength="20" value="{{ $row->CPDESIGNATION }}" disabled></td>
                <td><input  class="form-control w-100" type="text" name="LOC_EMAIL_{{$key}}" id="LOC_EMAIL_{{$key}}" maxlength="30" value="{{ $row->EMAIL }}" disabled></td>
                <td><input  class="form-control w-100" type="text" name="LOC_MONO_{{$key}}" id="LOC_MONO_{{$key}}" maxlength="20" value="{{ $row->MONO }}" disabled></td>
                <td><input  class="form-control w-100" type="text" name="LOC_SPINSTRACTION_{{$key}}" id="LOC_SPINSTRACTION_{{$key}}" maxlength="50" value="{{ $row->SPINSTRACTION }}" disabled ></td>
                
                <td style="text-align:center;" ><input type="checkbox" name="LOC_BILLTO_{{$key}}" id="LOC_BILLTO_{{$key}}" class="filter-none"  value="1" {{ ($row->BILLTO == 1 || $row->BILLTO=='on')?'checked':'' }} disabled></td>
                <td style="text-align:center;" ><input type="checkbox" name="LOC_DEFAULT_BILLTO_{{$key}}" id="LOC_DEFAULT_BILLTO_{{$key}}" class="filter-none"  value="1" {{ ($row->DEFAULT_BILLTO == 1 || $row->DEFAULT_BILLTO=='on')?'checked':'' }} disabled ></td>
                <td style="text-align:center;" ><input type="checkbox" name="LOC_SHIPTO_{{$key}}" id="LOC_SHIPTO_{{$key}}" class="filter-none"  value="1" {{ ($row->SHIPTO == 1 || $row->SHIPTO=='on')?'checked':'' }} disabled></td>
                <td style="text-align:center;" ><input type="checkbox" name="LOC_DEFAULT_SHIPTO_{{$key}}" id="LOC_DEFAULT_SHIPTO_{{$key}}" class="filter-none"  value="1" {{ ($row->DEFAULT_SHIPTO == 1 || $row->DEFAULT_SHIPTO=='on')?'checked':'' }} disabled></td>
                
              </tr>
              @endforeach

            @else 
            <tr  class="participantRow">
              <td><input  class="form-control w-100" type="text" name="LOC_NAME_0" id="LOC_NAME_0" maxlength="50" disabled></td>
              <td><input  class="form-control w-100" type="text" name="LOC_CADD_0" id="LOC_CADD_0" maxlength="200" disabled ></td>

              <td><input  class="form-control w-100" type="text" name="LOC_CTRYID_REF_0" id="TXTLOC_CTRYID_REF_POPUP_0" maxlength="100"  disabled ></td>
              <td hidden><input  class="form-control w-100" type="text" name="HDNLOC_CTRYID_REF_0" id="HDNLOC_CTRYID_REF_POPUP_0" maxlength="100"  disabled  ></td>

              <td><input  class="form-control w-100" type="text" name="LOC_STID_REF_0" id="TXTLOC_STID_REF_POPUP_0" maxlength="100"  disabled ></td>
              <td hidden><input  class="form-control w-100" type="text" name="HDNLOC_STID_REF_0" id="HDNLOC_STID_REF_POPUP_0" maxlength="100"  disabled ></td>

              <td><input  class="form-control w-100" type="text" name="LOC_CITYID_REF_0" id="TXTLOC_CITYID_REF_POPUP_0" maxlength="100"  disabled ></td>
              <td hidden><input  class="form-control w-100" type="text" name="HDNLOC_CITYID_REF_0" id="HDNLOC_CITYID_REF_POPUP_0" maxlength="100"  disabled ></td>


              <td><input  class="form-control w-100" type="text" name="LOC_PIN_0" id="LOC_PIN_0" maxlength="20"  disabled ></td>
              <td><input  class="form-control w-100" type="text" name="LOC_GSTIN_0" id="LOC_GSTIN_0" maxlength="30"  disabled ></td>
              <td><input  class="form-control w-100" type="text" name="LOC_CPNAME_0" id="LOC_CPNAME_0" maxlength="30"  disabled ></td>
              <td><input  class="form-control w-100" type="text" name="LOC_CPDESIGNATION_0" id="LOC_CPDESIGNATION_0" maxlength="20"  disabled ></td>
              <td><input  class="form-control w-100" type="text" name="LOC_EMAIL_0" id="LOC_EMAIL_0" maxlength="30" disabled  ></td>
              <td><input  class="form-control w-100" type="text" name="LOC_MONO_0" id="LOC_MONO_0" maxlength="20"  disabled ></td>
              <td><input  class="form-control w-100" type="text" name="LOC_SPINSTRACTION_0" id="LOC_SPINSTRACTION_0" maxlength="50" disabled  ></td>
              
              <td style="text-align:center;" ><input type="checkbox" name="LOC_BILLTO_0" id="LOC_BILLTO_0" class="filter-none"  value="1"  disabled ></td>
              <td style="text-align:center;" ><input type="checkbox" name="LOC_DEFAULT_BILLTO_0" id="LOC_DEFAULT_BILLTO_0" class="filter-none"  value="1" disabled  ></td>
              <td style="text-align:center;" ><input type="checkbox" name="LOC_SHIPTO_0" id="LOC_SHIPTO_0" class="filter-none"  value="1"  disabled ></td>
              <td style="text-align:center;" ><input type="checkbox" name="LOC_DEFAULT_SHIPTO_0" id="LOC_DEFAULT_SHIPTO_0" class="filter-none"  value="1" disabled  ></td>
              
            </tr>
            @endif  
            </tbody>
				  </table>
				
			</div>
				</div>


        <div id="ALPSSpecific" class="tab-pane fade">
                    
                    <div class="inner-form" style="margin-top:10px;">
                      <div class="row">
                        <div class="col-lg-2 pl"><p>{{isset($TabSetting->FIELD1) && $TabSetting->FIELD1 !=''?$TabSetting->FIELD1:'Add. Customer Code'}}</p></div>
                        <div class="col-lg-1 pl">
                        <input type="text" name="SAP_CUSTOMER_CODE" disabled id="SAP_CUSTOMER_CODE" value="{{ $objMstCust->SAP_CUSTOMER_CODE }}"  class="form-control" style="text-transform:uppercase">
                        </div>
                        <div class="col-lg-2 pl"><p>{{isset($TabSetting->FIELD2) && $TabSetting->FIELD2 !=''?$TabSetting->FIELD2:'Add. Customer Name'}}</p></div>
                        <div class="col-lg-2 pl">
                        <input type="text" name="SAP_CUSTOMER_NAME" disabled id="SAP_CUSTOMER_NAME" value="{{ $objMstCust->SAP_CUSTOMER_NAME }}" class="form-control">
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-lg-2 pl"><p>{{isset($TabSetting->FIELD3) && $TabSetting->FIELD3 !=''?$TabSetting->FIELD3:'Add. Account Group'}}</p></div>
                        <div class="col-lg-1 pl">
                        <input type="text" name="SAP_ACCOUNT_GROUP" disabled id="SAP_ACCOUNT_GROUP" value="{{ $objMstCust->SAP_ACCOUNT_GROUP }}" class="form-control" style="text-transform:uppercase">
                        </div>
                        <div class="col-lg-2 pl"><p>{{isset($TabSetting->FIELD4) && $TabSetting->FIELD4 !=''?$TabSetting->FIELD4:'Add. Customer Group Code'}}</p></div>
                        <div class="col-lg-2 pl">
                        <input type="text" name="SAP_CUSTOMER_GROUP_CODE" disabled id="SAP_CUSTOMER_GROUP_CODE" value="{{ $objMstCust->SAP_CUSTOMER_GCODE }}" class="form-control" style="text-transform:uppercase">
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-lg-2 pl"><p>{{isset($TabSetting->FIELD5) && $TabSetting->FIELD5 !=''?$TabSetting->FIELD5:'Add. Customer Group Name'}}</p></div>
                        <div class="col-lg-1 pl">
                        <input type="text" name="SAP_CUSTOMER_GROUP_NAME" disabled id="SAP_CUSTOMER_GROUP_NAME" value="{{ $objMstCust->SAP_CUSTOMER_GNAME }}" class="form-control" >
                        </div>
                        <div class="col-lg-2 pl"><p>{{isset($TabSetting->FIELD6) && $TabSetting->FIELD6 !=''?$TabSetting->FIELD6:'Add. Global Group Account Code'}}</p></div>
                        <div class="col-lg-2 pl">
                        <input type="text" name="SAP_GLOBAL_GROUP_CODE" disabled id="SAP_GLOBAL_GROUP_CODE" value="{{ $objMstCust->SAP_GLOBAL_GACODE }}"class="form-control" style="text-transform:uppercase">
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-lg-2 pl"><p>{{isset($TabSetting->FIELD7) && $TabSetting->FIELD7 !=''?$TabSetting->FIELD7:'Add. Global Group Account Name'}}</p></div>
                        <div class="col-lg-1 pl">
                        <input type="text" name="SAP_GLOBAL_GRUOUP_NAME" disabled id="SAP_GLOBAL_GRUOUP_NAME" value="{{ $objMstCust->SAP_GLOBAL_GANAME }}" class="form-control">
                        </div>
                        <div class="col-lg-2 pl"><p>{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Our Code in Customer book'}}</p></div>
                        <div class="col-lg-2 pl">
                        <input type="text" name="OUR_CODE" id="OUR_CODE" disabled value="{{ $objMstCust->OUR_CODE_INCBOOK }}" class="form-control" style="text-transform:uppercase">
                        </div>
                      </div>
                    </div>
                  </div>
				
			<div id="udf" class="tab-pane fade">
              <div class="table-responsive table-wrapper-scroll-y " style="margin-top:10px;height:280px;width:50%;">
                <table id="udffietable" class="display nowrap table table-striped table-bordered itemlist" style="height:auto !important;">
                  <thead id="thead1"  style="position: sticky;top: 0">
                    <tr >
                    <th>UDF Fields <input class="form-control" type="hidden" name="Row_Count4" id ="Row_Count4" value="{{ $objudfCount }}"> </th>
                    <th>Value / Comments</th>
                    </tr>
                  </thead>
                  <tbody>
                  @if(!empty($objUDF))
                  @foreach($objUDF as $udfkey => $udfrow)
                  <tr  class="participantRow">
                    <td>
                      <input name={{"udffie_popup_".$udfkey}} id={{"txtudffie_popup_".$udfkey}} value="{{$udfrow->LABEL}}" class="form-control" autocomplete="off" maxlength="100"  disabled  />
                    </td>

                    <td hidden>
                      <input type="text" name='{{"udffie_".$udfkey}}' id='{{"hdnudffie_popup_".$udfkey}}' value="{{$udfrow->UDFCMID}}" class="form-control" maxlength="100"  disabled />
                    </td>

                    <td hidden>
                      <input type="text" name={{"udffieismandatory_".$udfkey}} id={{"udffieismandatory_".$udfkey}} class="form-control" maxlength="100" value="{{$udfrow->ISMANDATORY}}"  disabled  />
                    </td>

                    <td id="{{"tdinputid_".$udfkey}}">
                      {{-- dynamic input --}}
                      @php
                        
                        $dynamicid = "udfvalue_".$udfkey;
                        $chkvaltype = strtolower($udfrow->VALUETYPE); 

                      if($chkvaltype=='date'){

                        $strinp = '<input type="date" placeholder="dd/mm/yyyy" name="'.$dynamicid.'" id="'.$dynamicid.'" class="form-control" value="'.$udfrow->VALUE.'"  disabled /> ';       

                      }else if($chkvaltype=='time'){

                          $strinp= '<input type="time" placeholder="h:i" name="'.$dynamicid.'" id="'.$dynamicid.'" class="form-control"  value="'.$udfrow->VALUE.'"  disabled /> ';

                      }else if($chkvaltype=='numeric'){
                      $strinp = '<input type="text" name="'.$dynamicid. '" id="'.$dynamicid.'" class="form-control" value="'.$udfrow->VALUE.'"  disabled /> ';

                      }else if($chkvaltype=='text'){

                      $strinp = '<input type="text" name="'.$dynamicid. '" id="'.$dynamicid.'" class="form-control" value="'.$udfrow->VALUE.'"  disabled /> ';

                      }else if($chkvaltype=='boolean'){
                          $boolval = ''; 
                          if($udfrow->VALUE=='on' || $udfrow->VALUE=='1' ){
                            $boolval="checked";
                          }
                          $strinp = '<input type="checkbox" name="'.$dynamicid. '" id="'.$dynamicid.'" class=""  '.$boolval.'  disabled /> ';

                      }else if($chkvaltype=='combobox'){
                        $strinp='';
                      $txtoptscombo =   strtoupper($udfrow->DESCRIPTIONS); ;
                      $strarray =  explode(',',$txtoptscombo);
                      $opts = '';
                      $chked='';
                        for ($i = 0; $i < count($strarray); $i++) {
                          $chked='';
                          if($strarray[$i]==$udfrow->VALUE){
                            $chked='selected="selected"';
                          }
                           $opts = $opts.'<option value="'.$strarray[$i].'"'.$chked.'  >'.$strarray[$i].'</option> ';
                        }

                        $strinp = '<select name="'.$dynamicid.'" id="'.$dynamicid.'" class="form-control" disabled >'.$opts.'</select>' ;


                      }
                      echo $strinp;
                      @endphp

                    </td>
                  
                   
                  </tr>
                  
                  @endforeach
                  @else
                  <tr  class="participantRow">
                    <td>
                      <input name="udffie_popup_0" id="txtudffie_popup_0" class="form-control" autocomplete="off" maxlength="100" />
                    </td>

                    <td hidden>
                      <input type="text" name="udffie_0" id="hdnudffie_popup_0" class="form-control" maxlength="100" />
                    </td>

                    <td hidden>
                      <input type="text" name="udffieismandatory_0" id="udffieismandatory_0" class="form-control" maxlength="100" />
                    </td>

                    <td id="tdinputid_0">
                      {{-- dynamic input --}}
                     </td>
                   
                    
                  </tr>
                  @endif
                  </tbody>
                </table>
              </div>
            </div>
				
				
				
				
				
			  </div>

		</div>
			
		</div>
						
		</form>
  </div><!--container-fluid filter-->
@endsection

@section('alert')
<div id="alert" class="modal"  role="dialog"  data-backdrop="static" >
  <div class="modal-dialog"   >
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
            <button class="btn alertbt" name='OkBtn1' id="OkBtn1" style="margin-left: 90px;display:none;">
              <div id="alert-active" class="activeOk"></div>OK</button>
        </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<div id="HOLDINGID_REF_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='HOLDINGID_REF_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>TDS Code</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="HOLDINGID_REF_tab1" class="display nowrap table  table-striped table-bordered" width="100%" style="font-size:12px;margin:0px;padding:0px;" >
        <thead>
          <tr>
            <th width="20%">Select</th>
            <th width="40%">Code</th>
            <th width="40%">Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td width="20%"></td>
          <td width="40%"><input type="text" id="HOLDINGID_REF_codesearch" onkeyup="searchTDSCode()"></td>
          <td width="40%"><input type="text" id="HOLDINGID_REF_namesearch" onkeyup="searchTDSName()"></td>
        </tr>
        </tbody>
      </table>

      <table id="HOLDINGID_REF_tab2" class="display nowrap table  table-striped table-bordered" width="100%" style="font-size:12px;margin:0px;padding:0px;">
        <tbody id="HOLDINGID_REF_body">
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<div id="cgidrefpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='cgidrefpopup_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Customer Group</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="cg_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th>Code</th>
            <th>Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td ><input type="text" id="cg_codesearch" onkeyup='colSearch("cg_tab2","cg_codesearch",0)'></td>
          <td><input type="text" id="cg_namesearch" onkeyup='colSearch("cg_tab2","cg_namesearch",1)'></td>
        </tr>
        </tbody>
      </table>
      
      <table id="cg_tab2" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody>
        @foreach ($objCustomerGroupList as $index=>$CgList)

        <tr id="cgidref_{{ $CgList->CGID }}" class="clscgidref">
          <td width="50%">{{ $CgList->CGROUP }}
          <input type="hidden" id="txtcgidref_{{ $CgList->CGID }}" data-desc="{{ $CgList->CGROUP }} - {{ $CgList->DESCRIPTIONS }}" value="{{ $CgList-> CGID }}"/>
          </td>
          <td>{{ $CgList->DESCRIPTIONS }}</td>
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

<div id="glrefpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='glrefpopup_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>GL</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="gl_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th>Code</th>
            <th>Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td ><input type="text" id="gl_codesearch" onkeyup="searchGLCode()"></td>
          <td><input type="text" id="gl_namesearch" onkeyup="searchGLName()"></td>
        </tr>
        </tbody>
      </table>
      
      <table id="gl_tab2" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody>
        @foreach ($objGlList as $index=>$GlList)

        <tr id="glref_{{ $GlList->GLID }}" class="clsglref">
          <td width="50%">{{ $GlList->GLCODE }}
          <input type="hidden" id="txtglref_{{ $GlList->GLID }}" data-desc="{{ $GlList->GLCODE }} - {{ $GlList->GLNAME }}" value="{{ $GlList-> GLID }}"/>
          </td>
          <td>{{ $GlList->GLNAME }}</td>
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


<div id="ctryidref_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='ctryidref_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Country</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="country_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th>Code</th>
            <th>Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td ><input type="text" id="country_codesearch" onkeyup="searchCountryCode()"></td>
          <td><input type="text" id="country_namesearch" onkeyup="searchCountryName()"></td>
        </tr>
        </tbody>
      </table>


      <table id="country_tab2" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody id="country_body">
        @foreach ($objCountryList as $index=>$CountryList)
        <tr id="ctryidref_{{ $CountryList->CTRYID }}" class="cls_ctryidref">
          <td width="50%">{{ $CountryList->CTRYCODE }}
          <input type="hidden" id="txtctryidref_{{ $CountryList->CTRYID }}" data-desc="{{ $CountryList->CTRYCODE }} - {{ $CountryList->NAME }}" value="{{ $CountryList-> CTRYID }}"/>
          </td>
          <td>{{ $CountryList->NAME }}</td>
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

<div id="stidref_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='stidref_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>State</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="state_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th>Code</th>
            <th>Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td ><input type="text" id="state_codesearch" onkeyup="searchStateCode()"></td>
          <td><input type="text" id="state_namesearch" onkeyup="searchStateName()"></td>
        </tr>
        </tbody>
      </table>

      <table id="state_tab2" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody id="state_body">
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<div id="cityidref_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='cityidref_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>City</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="city_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th>Code</th>
            <th>Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td ><input type="text" id="city_codesearch" onkeyup="searchCityCode()"></td>
          <td><input type="text" id="city_namesearch" onkeyup="searchCityName()"></td>
        </tr>
        </tbody>
      </table>

      <table id="city_tab2" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody id="city_body">
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<div id="cor_ctryidref_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='cor_ctryidref_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Country</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="cor_country_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th onclick='doSorting("cor_country_tab1","cor_country_tab2","cls_cor_ctryidref")'>Code</th>
            <th onclick='doSorting("cor_country_tab1","cor_country_tab2","cls_cor_ctryidref")'>Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td ><input type="text" id="cor_country_codesearch" onkeyup="searchCorCountryCode()"></td>
          <td><input type="text" id="cor_country_namesearch" onkeyup="searchCorCountryName()"></td>
        </tr>
        </tbody>
      </table>


      <table id="cor_country_tab2" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody id="cor_country_body">
        @foreach ($objCountryList as $index=>$CountryList)
        <tr id="cor_ctryidref_{{ $CountryList->CTRYID }}" class="cls_cor_ctryidref">
          <td width="50%">{{ $CountryList->CTRYCODE }}
          <input type="hidden" id="txtcor_ctryidref_{{ $CountryList->CTRYID }}" data-desc="{{ $CountryList->CTRYCODE }} - {{ $CountryList->NAME }}" value="{{ $CountryList-> CTRYID }}"/>
          </td>
          <td>{{ $CountryList->NAME }}</td>
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

<div id="cor_stidref_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='cor_stidref_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>State</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="cor_state_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th onclick='doSorting("cor_state_tab1","cor_state_tab2","cls_cor_stidref")'>Code</th>
            <th onclick='doSorting("cor_state_tab1","cor_state_tab2","cls_cor_stidref")'>Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td ><input type="text" id="cor_state_codesearch" onkeyup="searchCorStateCode()"></td>
          <td><input type="text" id="cor_state_namesearch" onkeyup="searchCorStateName()"></td>
        </tr>
        </tbody>
      </table>

      <table id="cor_state_tab2" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody id="cor_state_body">
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<div id="cor_cityidref_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='cor_cityidref_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>City</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="cor_city_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th onclick='doSorting("cor_city_tab1","cor_city_tab2","cls_cor_cityidref")'>Code</th>
            <th onclick='doSorting("cor_city_tab1","cor_city_tab2","cls_cor_cityidref")'>Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td ><input type="text" id="cor_city_codesearch" onkeyup="searchCorCityCode()"></td>
          <td><input type="text" id="cor_city_namesearch" onkeyup="searchCorCityName()"></td>
        </tr>
        </tbody>
      </table>

      <table id="cor_city_tab2" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody id="cor_city_body">
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<div id="indsidrefpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='indsidrefpopup_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Industry Type</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="indsidref_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th onclick='doSorting("indsidref_tab1","indsidref_tab2","clsindsidref")'>Code</th>
            <th onclick='doSorting("indsidref_tab1","indsidref_tab2","clsindsidref")'>Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td ><input type="text" id="indsidref_codesearch" onkeyup="searchITCode()"></td>
          <td><input type="text" id="indsidref_namesearch" onkeyup="searchITName()"></td>
        </tr>
        </tbody>
      </table>
      
      <table id="indsidref_tab2" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody>
        @foreach ($objIndTypeList as $index=>$IndType)

        <tr id="indsidref_{{ $IndType->INDSID }}" class="clsindsidref">
          <td width="50%">{{ $IndType->INDSCODE }}
          <input type="hidden" id="txtindsidref_{{ $IndType->INDSID }}" data-desc="{{ $IndType->INDSCODE }} - {{ $IndType->DESCRIPTIONS }}" value="{{ $IndType-> INDSID }}"/>
          </td>
          <td>{{ $IndType->DESCRIPTIONS }}</td>
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


<div id="indsvidrefpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='indsvidrefpopup_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Industry Vertical</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="indsvidref_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th onclick='doSorting("indsvidref_tab1","indsvidref_tab2","clsindsvidref")'>Code</th>
            <th onclick='doSorting("indsvidref_tab1","indsvidref_tab2","clsindsvidref")'>Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td ><input type="text" id="indsvidref_codesearch" onkeyup="searchIVCode()"></td>
          <td><input type="text" id="indsvidref_namesearch" onkeyup="searchIVName()"></td>
        </tr>
        </tbody>
      </table>
      
      <table id="indsvidref_tab2" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody>
        @foreach ($objIndVerList as $index=>$IndVer)

        <tr id="indsvidref_{{ $IndVer->INDSVID }}" class="clsindsvidref">
          <td width="50%">{{ $IndVer->INDSVCODE }}
          <input type="hidden" id="txtindsvidref_{{ $IndVer->INDSVID }}" data-desc="{{ $IndVer->INDSVCODE }} - {{ $IndVer->DESCRIPTIONS }}" value="{{ $IndVer-> INDSVID }}"/>
          </td>
          <td>{{ $IndVer->DESCRIPTIONS }}</td>
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

{{-- location country popup  --}}
<div id="loc_country_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='loc_country_popup_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Country</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="loc_country_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th onclick='doSorting("loc_country_tab1","loc_country_tab2","cls_loc_ctryidref")'>Code</th>
            <th onclick='doSorting("loc_country_tab1","loc_country_tab2","cls_loc_ctryidref")'>Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td ><input type="text" id="loc_country_codesearch"  onkeyup='colSearch("loc_country_tab2","loc_country_codesearch",0)'></td>
          <td><input type="text" id="loc_country_namesearch"  onkeyup='colSearch("loc_country_tab2","loc_country_namesearch",1)'></td>
        </tr>
        </tbody>
      </table>
      <table id="loc_country_tab2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <td hidden> 
            <input type="text" name="fieldid" id="hdn_locctryfieldid"/>
            <input type="text" name="fieldid2" id="hdn_locctryfieldid2"/>
           </td>
        </thead>
        <tbody id="loc_country_body">
        @foreach ($objCountryList as $index=>$CountryList)
        <tr id="loc_ctryidref_{{ $CountryList->CTRYID }}" class="cls_loc_ctryidref">
          <td width="50%">{{ $CountryList->CTRYCODE }}
          <input type="hidden" id="txtloc_ctryidref_{{ $CountryList->CTRYID }}" data-desc="{{ $CountryList->CTRYCODE }} - {{ $CountryList->NAME }}" value="{{ $CountryList-> CTRYID }}"/>
          </td>
          <td>{{ $CountryList->NAME }}</td>
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
{{-- location country popup end  --}}
{{-- location STATE popup  --}}
<div id="loc_state_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='loc_state_popup_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>State</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="loc_state_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th onclick='doSorting("loc_state_tab1","loc_state_tab2","cls_stidref")'>Code</th>
            <th onclick='doSorting("loc_state_tab1","loc_state_tab2","cls_stidref")'>Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td><input type="text" id="loc_state_codesearch"  onkeyup='colSearch("loc_state_tab2","loc_state_codesearch",0)'></td>
          <td><input type="text" id="loc_state_namesearch"  onkeyup='colSearch("loc_state_tab2","loc_state_namesearch",1)'></td>
        </tr>
        </tbody>
      </table>

      <table id="loc_state_tab2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          <tr id="none-select" class="searchalldata">           
            <td hidden> 
              <input type="text" name="fieldid" id="hdn_locstatefieldid"/>
              <input type="text" name="fieldid2" id="hdn_locstatefieldid2"/>
            </td>
          </tr>
        </thead>
        <tbody id="loc_state_body">
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
{{-- location STATE popup end  --}}
{{-- location city popup  --}}
<div id="loc_city_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='loc_city_popup_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>City</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="loc_city_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th onclick='doSorting("loc_city_tab1","loc_city_tab2","cls_stidref")'>Code</th>
            <th onclick='doSorting("loc_city_tab1","loc_city_tab2","cls_stidref")'>Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td><input type="text" id="loc_city_codesearch"  onkeyup='colSearch("loc_city_tab2","loc_city_codesearch",0)'></td>
          <td><input type="text" id="loc_city_namesearch"  onkeyup='colSearch("loc_city_tab2","loc_city_namesearch",1)'></td>
        </tr>
        </tbody>
      </table>

      <table id="loc_city_tab2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          <tr id="none-select" class="searchalldata">           
            <td hidden> 
              <input type="text" name="fieldid" id="hdn_loccityfieldid"/>
              <input type="text" name="fieldid2" id="hdn_loccityfieldid2"/>
            </td>
          </tr>
        </thead>
        <tbody id="loc_city_body">
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
{{-- location city popup end  --}}



<div id="udffiepopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='udffie_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>UDF Fields</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
      <table id="udffieexample23" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
        <tr>
              <th style="width: 50%">Label</th>
              <th>Type</th>
        </tr>
        </thead>
        <tbody>
        <tr>
        <td>
        <input type="text" id="udffiecodesearch" onkeyup="udffieFunction()">
        </td>
        <td>
        <input type="text" id="udffienamesearch" onkeyup="udffieNameFunction()">
        </td>
        </tr>
        </tbody>
        </table>
    <table id="udffieexample2345" class="display nowrap table  table-striped table-bordered" width="100%">
      <thead id="thead2">
        <tr id="none-select" class="searchalldata">
            
        <td hidden>
            <input type="hidden" name="fieldid" id="hdn_udffiefieldid"/>
            <input type="hidden" name="fieldid2" id="hdn_udffiefieldid2"/>
            <input type="hidden" name="fieldid2" id="hdn_udffiefieldid3"/>
            <input type="hidden" name="fieldid4" id="hdn_udffiefieldid4"/>
        </td>
        </tr>
        {{-- <tr>
          <th>Label</th>
          <th>Type</th>
        </tr> --}}
      </thead>
      <tbody>{{-- data-optscombo="{{ $UdffieRow->DESCRIPTIONS }}"      --}}
      @foreach ($objUdfForCustomer as $index=>$UdffieRow)
      <tr id="udffie_{{ $UdffieRow->UDFCMID }}" class="clsudffie">
        <td style="width: 50%">{{$UdffieRow->LABEL }}  <input type="hidden" id="txtudffie_{{ $UdffieRow->UDFCMID }}" value="{{ $UdffieRow-> UDFCMID }}"
          data-desc="{{ $UdffieRow->LABEL }}"  data-ismandatory="{{ $UdffieRow->ISMANDATORY }}"  data-valtype="{{ $UdffieRow->VALUETYPE }}"         data-optscombo="{{ $UdffieRow->DESCRIPTIONS }}"  />
        </td>
        <td >{{ $UdffieRow->VALUETYPE }} {{ $UdffieRow->ISMANDATORY }}
          </td>
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

@push('bottom-css')
@endpush

@push('bottom-scripts')
<script>
$(document).ready(function() {
  if($("#TDS_APPLICABLE").prop("checked") == true){
    $(".TDS_ACTION").show();
  }
  else{
    $(".TDS_ACTION").hide();
  }

  getTdsCode('<?php echo $objMstCust->ASSESSEEID_REF;?>');

});

function TdsApplicable(){
  $("#CERTIFICATE_NO").val('');
  $("#EXPIRY_DT").val('');
  $("#ASSESSEEID_REF").val('');
  $("#HOLDINGID_REF_POPUP").val('');
  $("#HOLDINGID_REF").val('');
  $("#HOLDINGID_REF_body").html('');

  if($("#TDS_APPLICABLE").prop("checked") == true){
    $(".TDS_ACTION").show();
  }
  else{
    $(".TDS_ACTION").hide();
  }
}

function getTdsCode(ASSESSEEID_REF){
  $("#HOLDINGID_REF_POPUP").val('');
  $("#HOLDINGID_REF").val('');

  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  $.ajax({
      url:'{{route("master",[5,"TdsCodeList"])}}',
      type:'POST',
      data:{ASSESSEEID_REF:ASSESSEEID_REF,VALUE:'<?php echo $objMstCust->HOLDINGID_REF;?>'},
      success:function(data) {
        $("#HOLDINGID_REF_body").html(data);
        bindTds();
        bindTds1();
      },
      error:function(data){
        console.log("Error: Something went wrong.");
        $("#HOLDINGID_REF_body").html('');
        
      },
  });	
}

$("#HOLDINGID_REF_POPUP").on("click",function(event){ 
  $("#HOLDINGID_REF_popup").show();
});

$("#HOLDINGID_REF_POPUP").keyup(function(event){
  if(event.keyCode==13){
    $("#HOLDINGID_REF_popup").show();
  }
});

$("#HOLDINGID_REF_close").on("click",function(event){ 
  $("#HOLDINGID_REF_popup").hide();
});

function bindTds(){
  $('.cls_HOLDINGID_REF').change(function(){

    var all_location_id = document.querySelectorAll('input[name="selectAll[]"]:checked');
    var aIds = [];
    var aIds1 = [];
    for(var x = 0, l = all_location_id.length; x < l;  x++){

      var id      = all_location_id[x].value;
      var txtval  = $("#txt"+id+"").val();
      var texdesc = $("#txt"+id+"").data("desc")

      aIds.push(txtval);
      aIds1.push(texdesc);

    }

    $("#HOLDINGID_REF_POPUP").val(aIds1);
    $("#HOLDINGID_REF").val(aIds);

  });
}

function bindTds1(){
  $('.cls_HOLDINGID_REF').find(function(){

    var all_location_id = document.querySelectorAll('input[name="selectAll[]"]:checked');
    var aIds = [];
    var aIds1 = [];
    for(var x = 0, l = all_location_id.length; x < l;  x++){

      var id      = all_location_id[x].value;
      var txtval  = $("#txt"+id+"").val();
      var texdesc = $("#txt"+id+"").data("desc")

      aIds.push(txtval);
      aIds1.push(texdesc);

    }

    $("#HOLDINGID_REF_POPUP").val(aIds1);
    $("#HOLDINGID_REF").val(aIds);

  });
}

function searchTDSCode() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("HOLDINGID_REF_codesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("HOLDINGID_REF_tab2");
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

function searchTDSName() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("HOLDINGID_REF_namesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("HOLDINGID_REF_tab2");
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

</script>
@endpush