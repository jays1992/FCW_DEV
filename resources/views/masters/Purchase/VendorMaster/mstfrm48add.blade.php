@extends('layouts.app')
@section('content')

<div class="container-fluid topnav">
	<div class="row">
		<div class="col-lg-2"><a href="{{route('master',[48,'index'])}}" class="btn singlebt">Vendor Master</a></div>

		<div class="col-lg-10 topnav-pd">
		  <button  id="btnSelectedRows" class="btn topnavbt" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
		  <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
		  <button id="btnSaveItem"   class="btn topnavbt" tabindex="7"><i class="fa fa-save"></i> Save</button>
		  <button class="btn topnavbt" id="btnView"  disabled="disabled"><i class="fa fa-eye"></i> View</button>
		  <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-print"></i> Print</button>
		  <button class="btn topnavbt"  id="btnUndo" ><i class="fa fa-undo"></i> Undo</button>
		  <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
		  <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-lock"></i> Approved</button>
		  <button  class="btn topnavbt"  disabled="disabled"><i class="fa fa-link" ></i> Attachment</button>
		  <a href="{{route('home')}}" class="btn topnavbt"><i class="fa fa-power-off"></i> Exit</a>
		</div>
	</div>
</div>

<div class="container-fluid filter">
	<form id="add_form_data" method="POST"  > 
		@CSRF  
		<div class="inner-form">
    
			<div class="row">
				<div class="col-lg-2 pl"><p>Vendor Code</p></div>
				<div class="col-lg-2 pl">
					{{-- <input type="text" name="VCODE" id="VCODE" class="form-control mandatory"  maxlength="20" required tabindex="1" autocomplete="off" style="text-transform:uppercase" autofocus > --}}
          @if(!empty($objDD))
              @if($objDD->SYSTEM_GRSR == "1")
                  <input type="text" name="VCODE" id="VCODE" value="{{ $objDOCNO }}" class="form-control mandatory" tabindex="1" maxlength="10" autocomplete="off" readonly style="text-transform:uppercase" autofocus >
              @endif
              @if($objDD->MANUAL_SR == "1")
                  <input type="text" name="VCODE" id="VCODE" value="{{ old('VCODE') }}" class="form-control mandatory"  maxlength="{{$objDD->MANUAL_MAXLENGTH}}" tabindex="1" autocomplete="off" style="text-transform:uppercase" autofocus >
              @endif
          @endif  
					<span class="text-danger" id="ERROR_VCODE"></span>
				</div>
			  
				<div class="col-lg-2 pl"><p>Name</p></div>
				<div class="col-lg-2 pl">
				  <input type="text" name="NAME" id="NAME" class="form-control mandatory"  maxlength="100" required tabindex="2" autocomplete="off">
          <span class="text-danger" id="ERROR_NAME"></span>
        </div>
				
				<div class="col-lg-2 pl"><p>Vendor Legal Name</p></div>
				<div class="col-lg-2 pl">
				  <input type="text" name="VENDOR_LEGAL_NAME" id="VENDOR_LEGAL_NAME" class="form-control mandatory" required  maxlength="100" tabindex="3" autocomplete="off">
          <span class="text-danger" id="ERROR_VENDOR_LEGAL_NAME"></span>
        </div>
			</div>
			
			<div class="row">
				<div class="col-lg-2 pl"><p>Vendor Group</p></div>
				<div class="col-lg-2 pl">
					<input type="text" name="VGID_REF_POPUP" id="VGID_REF_POPUP" class="form-control mandatory" required readonly tabindex="4" />
					<input type="hidden" name="VGID_REF" id="VGID_REF" />
					<span class="text-danger" id="ERROR_VGID_REF"></span>
				</div>
			  
			  <div class="col-lg-2 pl"><p>OLD Ref Code</p></div>
			  <div class="col-lg-2 pl">
				<input type="text" name="OLD_REFCODE" id="OLD_REFCODE" class="form-control"  maxlength="20" tabindex="5" autocomplete="off">
			  </div>
			  
			  <div class="col-lg-2 pl"><p>GL</p></div>
			  <div class="col-lg-2 pl">
				<input type="text" name="GLID_REF_POPUP" id="GLID_REF_POPUP" class="form-control mandatory" required readonly tabindex="6" />
                <input type="hidden" name="GLID_REF" id="GLID_REF" />
                <span class="text-danger" id="ERROR_GLID_REF"></span>
			  </div>
			  
			</div>
			
			<div class="row">
				<div class="col-lg-2 pl"><p>Registered Address Line 1</p></div>
				<div class="col-lg-2 pl">
					<input type="text" name="REGADDL1" id="REGADDL1" class="form-control mandatory"  maxlength="200" required tabindex="7" autocomplete="off">
          <span class="text-danger" id="ERROR_REGADDL1"></span>
        </div>
				
					<div class="col-lg-2 pl"><p>Registered Address Line 2</p></div>
				<div class="col-lg-2 pl">
					<input type="text" name="REGADDL2" id="REGADDL2" class="form-control"  maxlength="200" tabindex="8" autocomplete="off">
				</div>

        <div class="col-lg-2 pl"><p>CHA</p></div>
				<div class="col-lg-2 pl">
					<input type="checkbox" name="CHA" id="CHA" value="1"  autocomplete="off">
				</div>
			
			</div>
				
			<div class="row">
			
			<div class="col-lg-2 pl"><p>Country</p></div>
			<div class="col-lg-2 pl">
				<input type="text" name="REGCTRYID_REF_POPUP" id="REGCTRYID_REF_POPUP" class="form-control mandatory" required readonly tabindex="9" />
                <input type="hidden" name="REGCTRYID_REF" id="REGCTRYID_REF" />
                <span class="text-danger" id="ERROR_REGCTRYID_REF"></span>
			</div>
			
			<div class="col-lg-1 pl"><p>State</p></div>
			<div class="col-lg-2 pl">
				<input type="text" name="REGSTID_REF_POPUP" id="REGSTID_REF_POPUP" class="form-control mandatory" required readonly tabindex="10" />
                <input type="hidden" name="REGSTID_REF" id="REGSTID_REF" />
                <span class="text-danger" id="ERROR_REGSTID_REF"></span>
			</div>
		
			<div class="col-lg-1 pl"><p>City</p></div>
			<div class="col-lg-2 pl">
				<input type="text" name="REGCITYID_REF_POPUP" id="REGCITYID_REF_POPUP" class="form-control mandatory" required readonly tabindex="11" />
                <input type="hidden" name="REGCITYID_REF" id="REGCITYID_REF" />
                <span class="text-danger" id="ERROR_REGCITYID_REF"></span>
			</div>
			
				
			<div class="col-lg-1 pl"><p>Pincode</p></div>
			<div class="col-lg-1 pl">
				<input type="text" name="REGPIN" id="REGPIN" class="form-control "  maxlength="10" tabindex="12"  autocomplete="off" >
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
				<li><a data-toggle="tab" href="#location">Location</a></li>
        <li><a data-toggle="tab" href="#ALPSSpecific">{{isset($TabSetting->TAB_NAME) && $TabSetting->TAB_NAME !=''?$TabSetting->TAB_NAME:'Additional Info'}}</a></li>
				<li><a data-toggle="tab" href="#udf">UDF</a></li>
			</ul>

			<div class="tab-content">
				<div id="material" class="tab-pane fade in active">
					<div class="table-wrapper-scroll-x">
						<div class="row" style="margin-top:10px;">
								
							<div class="col-lg-2"><p>Corporate Address Line 1</p></div>
							<div class="col-lg-4">
								<input type="text" name="CORPADDL1" id="CORPADDL1" class="form-control "  maxlength="200" autocomplete="off" >
							</div>
							
							<div class="col-lg-2"><p>Corporate Address Line 2</p></div>
							<div class="col-lg-4">
								<input type="text" name="CORPADDL2" id="CORPADDL2" class="form-control"  maxlength="200" autocomplete="off">
							</div>
							
						</div>
						
						<div class="row">
							<div class="col-lg-2"><p>Country</p></div>
							<div class="col-lg-2">	
								<input type="text" name="CORPCTRYID_REF_POPUP" id="CORPCTRYID_REF_POPUP" class="form-control" readonly  />
								<input type="hidden" name="CORPCTRYID_REF" id="CORPCTRYID_REF" />
								<span class="text-danger" id="ERROR_CORPCTRYID_REF"></span>
							</div>
							
							<div class="col-lg-2"><p>State</p></div>
							<div class="col-lg-2">		
								<input type="text" name="CORPSTID_REF_POPUP" id="CORPSTID_REF_POPUP" class="form-control" readonly  />
								<input type="hidden" name="CORPSTID_REF" id="CORPSTID_REF" />
								<span class="text-danger" id="ERROR_CORPSTID_REF"></span>
							</div>
							
							<div class="col-lg-2"><p>City</p></div>
							<div class="col-lg-2">
								<input type="text" name="CORPCITYID_REF_POPUP" id="CORPCITYID_REF_POPUP" class="form-control" readonly  />
								<input type="hidden" name="CORPCITYID_REF" id="CORPCITYID_REF" />
								<span class="text-danger" id="ERROR_CORPCITYID_REF"></span>
							</div>
					
						</div>
				
				
					<div class="row">
					
							
					<div class="col-lg-2"><p>Pincode</p></div>
					<div class="col-lg-2">
						<input type="text" name="CORPPIN" id="CORPPIN" class="form-control "  maxlength="10" autocomplete="off" >
					</div>
					
						<div class="col-lg-2"><p>Email ID</p></div>
						<div class="col-lg-2">
							<input type="text" name="EMAILID" id="EMAILID" class="form-control "  maxlength="50" autocomplete="off" >
						</div>
						
						<div class="col-lg-2"><p>Website</p></div>
						<div class="col-lg-2">
							<input type="text" name="WEBSITE" id="WEBSITE" class="form-control "  maxlength="50" autocomplete="off" >
						</div>
						
					</div>
					
					<div class="row">
					<div class="col-lg-2 "><p>Phone No</p></div>
						<div class="col-lg-2">
							<input type="text" name="PHNO" id="PHNO" class="form-control "  maxlength="15"  autocomplete="off">
						</div>
						
						<div class="col-lg-2"><p>Mobile No</p></div>
						<div class="col-lg-2">
							<input type="text" name="MONO" id="MONO" class="form-control "  maxlength="15"  autocomplete="off">
						</div>
						
						<div class="col-lg-2"><p>Contact Person</p></div>
						<div class="col-lg-2 ">
							<input type="text" name="CPNAME" id="CPNAME"  class="form-control"  maxlength="50" autocomplete="off" >
						</div>
						
						
						
					</div>
					
					<div class="row">
					<div class="col-lg-2 "><p>Skype</p></div>
						<div class="col-lg-2">
							<input type="text" name="SKYPEID" id="SKYPEID" class="form-control"  maxlength="30" autocomplete="off" >
						</div>
					</div>
					
			</div>
				</div>
				
				<div id="statutory" class="tab-pane fade"> 
				
					 <div class="table-wrapper-scroll-x" style="margin-top:10px;">
					
						<div class="row" >
					
						<div class="col-lg-2"><p>Industry Type</p></div>
						<div class="col-lg-2">
							<input type="text" name="INDSID_REF_POPUP" id="INDSID_REF_POPUP" class="form-control" readonly  />
							<input type="hidden" name="INDSID_REF" id="INDSID_REF" />
							<span class="text-danger" id="ERROR_INDSID_REF"></span>
						</div>
						
						<div class="col-lg-2"><p>Industry Vertical</p></div>
						<div class="col-lg-2">
							<input type="text" name="INDSVID_REF_POPUP" id="INDSVID_REF_POPUP" class="form-control" readonly  />
							<input type="hidden" name="INDSVID_REF" id="INDSVID_REF" />
							<span class="text-danger" id="ERROR_INDSVID_REF"></span>
						</div>
						
						<div class="col-lg-1"><p>Deals In</p></div>
						<div class="col-lg-2">
							<input type="text" name="DEALSIN" id="DEALSIN" class="form-control  "  maxlength="50" autocomplete="off" >
						</div>
						
						</div>
					
					<div class="row">
						
						<div class="col-lg-2"><p>GST Type</p></div>
						<div class="col-lg-2">
							<select name="GSTTYPE" id="GSTTYPE" class="form-control mandatory" required >
								<option value="" selected >Select</option>
								
								@foreach ($objGstTypeList as $index=>$GstType)
								<option value="{{ $GstType-> GSTID }}">{{ $GstType->GSTCODE }} - {{ $GstType->DESCRIPTIONS }}</option>
								@endforeach
	
							</select>
						</div>
						


						<div class="col-lg-2"><p>Default Currency</p></div>
						<div class="col-lg-2">
							<select name="DEFCRID_REF" id="DEFCRID_REF"  class="form-control mandatory" required  >
								<option value="" selected >Select</option>
								@foreach ($objCurrencyList as $index=>$Currency)
								<option value="{{ $Currency-> CRID }}">{{ $Currency->CRCODE }} - {{ $Currency->CRDESCRIPTION }}</option>
								@endforeach
							</select>
						</div>
						
						<div class="col-lg-1"><p>GSTIN</p></div>
						<div class="col-lg-2">
							<input type="text" name="GSTIN" id="GSTIN" class="form-control  "  maxlength="15" autocomplete="off" >
						</div>
						
						</div>
					
					
				
				<div class="row">
					  <div class="col-lg-2"><p>Credit Limit</p></div>
            <div class="col-lg-2">
              <input type="text" name="CREDITLIMIT" id="CREDITLIMIT" class="form-control " style="text-align: right"  maxlength="18"  autocomplete="off">
            </div>
						
						<div class="col-lg-2"><p>CIN </p></div>
						<div class="col-lg-2">
							<input type="text" name="CIN" id="CIN" class="form-control  "  maxlength="30"  autocomplete="off">
						</div>
						
						<div class="col-lg-1"><p>Credit Days </p></div>
						<div class="col-lg-1">
							<input type="text" name="CREDITDAY" id="CREDITDAY" class="form-control " style="text-align: right"  maxlength="4"  autocomplete="off">
						</div>

				</div>
				
				<div class="row">
			    	<div class="col-lg-2"><p>PAN No </p></div>
						<div class="col-lg-2">
							<input type="text" name="PANNO" id="PANNO" class="form-control  "  maxlength="10"  autocomplete="off">
						</div>
      
        
        <div class="col-lg-2 "><p>Exceptional for GST</p></div>
        <div class="col-lg-2"><input type="checkbox" name="EXE_GST" id="EXE_GST" class="filter-none"  value="1" > </div>
				</div>
        <div class="row">
        <div class="col-lg-2"><p>MSME No </p></div>
          <div class="col-lg-2">
            <input type="text" name="MSME_NO" id="MSME_NO" class="form-control  "  maxlength="30"  autocomplete="off" style="text-transform:uppercase">
          </div>
          <div class="col-lg-2"><p>Factory No</p></div>
          <div class="col-lg-2">
            <input type="text" name="FACTORY_ACT_NO" id="FACTORY_ACT_NO" class="form-control  "  maxlength="30"  autocomplete="off" style="text-transform:uppercase">
          </div>
						</div>


        <div class="row">
          <div class="col-lg-2"><p>TDS Applicable</p></div>
          <div class="col-lg-2">
            <input type="checkbox" name="TDS_APPLICABLE" id="TDS_APPLICABLE" value="1" onChange="TdsApplicable()" >
          </div>
        </div>

        <div class="row TDS_ACTION" style="display:none;">
          <div class="col-lg-2"><p>Certificate Number</p></div>
          <div class="col-lg-2">
            <input type="text" name="CERTIFICATE_NO" id="CERTIFICATE_NO" class="form-control"  maxlength="30"  autocomplete="off">
          </div>

          <div class="col-lg-2"><p>Expiry Date</p></div>
          <div class="col-lg-2">
            <input type="date" name="EXPIRY_DT" id="EXPIRY_DT" class="form-control"   autocomplete="off">
          </div>
        </div>

        <div class="row TDS_ACTION" style="display:none;">
          <div class="col-lg-2"><p>Assessee Type</p></div>
          <div class="col-lg-2">
            <select  name="ASSESSEEID_REF" id="ASSESSEEID_REF" class="form-control mandatory" autocomplete="off" onChange="getTdsCode(this.value)" >
            <option value="">Select</option>
              @if(!empty($objAssesseeTypeList))
              @foreach($objAssesseeTypeList as $key=>$val)
              <option value="{{$val->NOAID}}">{{$val->NOA_CODE}} - {{$val->NOA_NAME}}</option>
              @endforeach
              @endif
            </select>
          </div>

          <div class="col-lg-2"><p>TDS Code</p></div>
          <div class="col-lg-2">
            <input type="text" name="HOLDINGID_REF_POPUP" id="HOLDINGID_REF_POPUP" class="form-control mandatory" readonly  />
            <input type="hidden" name="HOLDINGID_REF" id="HOLDINGID_REF" />
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
								<th>Person Name <input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1"> </th>
								<th>Designation</th>
								<th>Mobile</th>
								<th>Email</th>
								<th>LL No</th>
								<th>Authority Level</th>
								<th>Birthday</th>
								<th style="text-align: center">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr  class="participantRow">
								<td><input  class="form-control w-100" type="text" name="POC_NAME_0" id="POC_NAME_0" maxlength="100" autocomplete="off"  ></td>
								<td><input  class="form-control w-100" type="text" name="POC_DESIG_0" id="POC_DESIG_0" maxlength="50"  autocomplete="off" ></td>
								<td><input  class="form-control w-100" type="text" name="POC_MONO_0" id="POC_MONO_0" maxlength="15"  autocomplete="off" ></td>
								<td><input  class="form-control w-100" type="text" name="POC_EMAIL_0" id="POC_EMAIL_0" maxlength="50"  autocomplete="off" ></td>
								<td><input  class="form-control w-100" type="text" name="POC_LLNO_0" id="POC_LLNO_0" maxlength="20"  autocomplete="off" ></td>
								<td><input  class="form-control w-100" type="text" name="POC_AUTHLEVEL_0" id="POC_AUTHLEVEL_0" maxlength="30"  autocomplete="off" ></td>
								<td style="text-align:center;" ><input type="date" name="POC_DOB_0" id="POC_DOB_0" class="form-control"  autocomplete="off" ></td>
								<td align="center" >
									<a class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></a>
									<button class="btn remove" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" disabled></i></button>
								</td>
              </tr>
            </tbody>
          </table>
				</div>
			</div>
				
				<div id="bank" class="tab-pane fade">
				  <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar"  style="height:250px;margin-top:10px;" >
					
					<table id="table2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
            <thead id="thead1"  style="position: sticky;top: 0">
              <tr >
                <th>Bank Name <input class="form-control" type="hidden" name="Row_Count2" id ="Row_Count2"></th>
                <th>IFSC</th>
                <th>Branch</th>
                <th>Account Type</th>
                <th>Account No</th>
                <th>Default Bank</th>
                <th style="text-align: center">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr class="participantRow">
                <td><input  class="form-control w-100" type="text" name="BANK_NAME_0" id="BANK_NAME_0" maxlength="100"  autocomplete="off" ></td>
                <td><input  class="form-control w-100" type="text" name="BANK_IFSC_0" id="BANK_IFSC_0" maxlength="20" autocomplete="off"  ></td>
                <td><input  class="form-control w-100" type="text" name="BANK_BRANCH_0" id="BANK_BRANCH_0" maxlength="100" autocomplete="off"  ></td>
                <td>
                  <select name="BANK_ACTYPE_0" id="BANK_ACTYPE_0" class="form-control"  >
                    <option value="" selected >Select</option>
                    @foreach ($account_type_data as $key=>$val)
                      <option value="{{$val}}">{{ $val}}</option>
                    @endforeach      
                  </select>
                </td>
                <td><input  class="form-control w-100" type="text" name="BANK_ACNO_0" id="BANK_ACNO_0" maxlength="30"  autocomplete="off" ></td>
                <td align="center" ><input type="checkbox" name="BYDEFALUT_0" id="BYDEFALUT_0" class="filter-none"  value="1" ></td>
                <td align="center" >
                  <a class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></a>
                  <button class="btn remove" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" disabled></i></button>
                </td>
              </tr>
          
            </tbody>
				  </table>
				 
			</div>
				</div>
				
				<div id="location" class="tab-pane fade">
				  <div class="table-responsive table-wrapper-scroll-y " style="height:260px;margin-top:10px;">
					
					<table id="table3" class="display nowrap table table-striped table-bordered itemlist itemlistscroll w-200" style="height:auto !important;">
            <thead id="thead1"  style="position: sticky;top: 0">
              <tr >
                <th>Location Name <input class="form-control" type="hidden" name="Row_Count3" id ="Row_Count3"></th>
                <th>Location Address</th>
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
                <th style="text-align: center">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr  class="participantRow">
                <td><input  class="form-control w-100" type="text" name="LOC_NAME_0" id="LOC_NAME_0" maxlength="50"  autocomplete="off" ></td>
                <td><input  class="form-control w-100" type="text" name="LOC_LADD_0" id="LOC_LADD_0" maxlength="200"  autocomplete="off" ></td>

                <td><input  class="form-control w-100" type="text" name="LOC_CTRYID_REF_0" id="TXTLOC_CTRYID_REF_POPUP_0" maxlength="100" readonly></td>
                <td hidden><input  class="form-control w-100" type="text" name="HDNLOC_CTRYID_REF_0" id="HDNLOC_CTRYID_REF_POPUP_0" maxlength="100" ></td>

                <td><input  class="form-control w-100" type="text" name="LOC_STID_REF_0" id="TXTLOC_STID_REF_POPUP_0" maxlength="100" readonly></td>
                <td hidden><input  class="form-control w-100" type="text" name="HDNLOC_STID_REF_0" id="HDNLOC_STID_REF_POPUP_0" maxlength="100" readonly></td>

                <td><input  class="form-control w-100" type="text" name="LOC_CITYID_REF_0" id="TXTLOC_CITYID_REF_POPUP_0" maxlength="100" readonly></td>
                <td hidden><input  class="form-control w-100" type="text" name="HDNLOC_CITYID_REF_0" id="HDNLOC_CITYID_REF_POPUP_0" maxlength="100" readonly></td>


                <td><input  class="form-control w-100" type="text" name="LOC_PIN_0" id="LOC_PIN_0" maxlength="20" autocomplete="off"  ></td>
                <td><input  class="form-control w-100" type="text" name="LOC_GSTIN_0" id="LOC_GSTIN_0" maxlength="15"  autocomplete="off" ></td>
                <td><input  class="form-control w-100" type="text" name="LOC_CPNAME_0" id="LOC_CPNAME_0" maxlength="30"  autocomplete="off" ></td>
                <td><input  class="form-control w-100" type="text" name="LOC_CPDESIGNATION_0" id="LOC_CPDESIGNATION_0" maxlength="20"  autocomplete="off" ></td>
                <td><input  class="form-control w-100" type="text" name="LOC_EMAIL_0" id="LOC_EMAIL_0" maxlength="50"  autocomplete="off" ></td>
                <td><input  class="form-control w-100" type="text" name="LOC_MONO_0" id="LOC_MONO_0" maxlength="15"  autocomplete="off" ></td>
                <td><input  class="form-control w-100" type="text" name="LOC_SPINSTRACTION_0" id="LOC_SPINSTRACTION_0" maxlength="50"  autocomplete="off" ></td>
                
                <td style="text-align:center;" ><input type="checkbox" name="LOC_BILLTO_0" id="LOC_BILLTO_0" class="filter-none"  value="1" ></td>
                <td style="text-align:center;" ><input type="checkbox" name="LOC_DEFAULT_BILLTO_0" id="LOC_DEFAULT_BILLTO_0" class="filter-none"  value="1" ></td>
                <td style="text-align:center;" ><input type="checkbox" name="LOC_SHIPTO_0" id="LOC_SHIPTO_0" class="filter-none"  value="1" ></td>
                <td style="text-align:center;" ><input type="checkbox" name="LOC_DEFAULT_SHIPTO_0" id="LOC_DEFAULT_SHIPTO_0" class="filter-none"  value="1" ></td>
                
                <td align="center" >
                  <a class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></a>
                  <button class="btn remove" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button>
                </td>
              </tr>
            </tbody>
				  </table>
				
			</div>
				</div>
        <div id="ALPSSpecific" class="tab-pane fade">
                    
                    <div class="inner-form" style="margin-top:10px;">
                      <div class="row">
                        <div class="col-lg-2 pl"><p>{{isset($TabSetting->FIELD1) && $TabSetting->FIELD1 !=''?$TabSetting->FIELD1:'Add. Vendor Code'}}</p></div>
                        <div class="col-lg-1 pl">
                        <input type="text" name="SAP_VENDOR_CODE" id="SAP_VENDOR_CODE" value="" class="form-control" style="text-transform:uppercase">
                        </div>
                        <div class="col-lg-2 pl"><p>{{isset($TabSetting->FIELD2) && $TabSetting->FIELD2 !=''?$TabSetting->FIELD2:'Add. Vendor Name1'}}</p></div>
                        <div class="col-lg-2 pl">
                        <input type="text" name="SAP_VENDOR_NAME1" id="SAP_VENDOR_NAME1" value="" class="form-control">
                        </div>
                        <div class="col-lg-2 pl"><p>{{isset($TabSetting->FIELD3) && $TabSetting->FIELD3 !=''?$TabSetting->FIELD3:'Add. Vendor Name2'}}</p></div>
                        <div class="col-lg-2 pl">
                        <input type="text" name="SAP_VENDOR_NAME2" id="SAP_VENDOR_NAME2" value="" class="form-control">
                        </div>
                      </div>


                      <div class="row">
                        <div class="col-lg-2 pl"><p>{{isset($TabSetting->FIELD4) && $TabSetting->FIELD4 !=''?$TabSetting->FIELD4:'Add. Vendor Name3'}}</p></div>
                        <div class="col-lg-1 pl">
                        <input type="text" name="SAP_VENDOR_NAME3" id="SAP_VENDOR_NAME3" value="" class="form-control" >
                        </div>
                        <div class="col-lg-2 pl"><p>{{isset($TabSetting->FIELD5) && $TabSetting->FIELD5 !=''?$TabSetting->FIELD5:'Add. Corporate Group'}}</p></div>
                        <div class="col-lg-2 pl">
                        <input type="text" name="SAP_CORPORATE_GROUP" id="SAP_CORPORATE_GROUP" value="" class="form-control">
                        </div>
                        <div class="col-lg-2 pl"><p>{{isset($TabSetting->FIELD6) && $TabSetting->FIELD6 !=''?$TabSetting->FIELD6:'Add. Account Group'}}</p></div>
                        <div class="col-lg-2 pl">
                        <input type="text" name="SAP_ACCOUNT_GROUP" id="SAP_ACCOUNT_GROUP" value="" class="form-control" >
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-lg-2 pl"><p>{{isset($TabSetting->FIELD7) && $TabSetting->FIELD7 !=''?$TabSetting->FIELD7:'Add. Account Group Name'}}</p></div>
                        <div class="col-lg-1 pl">
                        <input type="text" name="SAP_ACCOUNT_GROUP_NAME" id="SAP_ACCOUNT_GROUP_NAME" value="" class="form-control" >
                        </div>
                        <div class="col-lg-2 pl"><p>{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Trading Partner'}}</p></div>
                        <div class="col-lg-2 pl">
                        <input type="text" name="SAP_TRADING_PARTNER" id="SAP_TRADING_PARTNER" value="" class="form-control">
                        </div>
                        <div class="col-lg-2 pl"><p>{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Trading Partner Name'}} </p></div>
                        <div class="col-lg-2 pl">
                        <input type="text" name="SAP_TRADING_PARTNER_NAME" id="SAP_TRADING_PARTNER_NAME" value="" class="form-control">
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-lg-2 pl"><p>{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Invoicing Party'}}</p></div>
                        <div class="col-lg-1 pl">
                        <input type="text" name="SAP_INVOCING_PARTY" id="SAP_INVOCING_PARTY" value="" class="form-control" >
                        </div>
                        <div class="col-lg-2 pl"><p>{{isset($TabSetting->FIELD11) && $TabSetting->FIELD11 !=''?$TabSetting->FIELD11:'Our Code In Vendor Book'}}</p></div>
                        <div class="col-lg-2 pl">
                        <input type="text" name="OUR_CODE_VBOOK" id="OUR_CODE_VBOOK" value="" class="form-control">
                        </div>
          
                      </div>
                     
                   
           
                      
 
                      
     
     
                      
                    </div>
                  </div>
        
				
			<div id="udf" class="tab-pane fade">
              <div class="table-responsive table-wrapper-scroll-y " style="margin-top:10px;height:320px;width:50%;">
                <table id="udffietable" class="display nowrap table table-striped table-bordered itemlist" style="height:auto !important;">
                  <thead id="thead1"  style="position: sticky;top: 0">
                    <tr >
                    <th>UDF Fields <input class="form-control" type="hidden" name="Row_Count4" id ="Row_Count4" value="{{ $objudfCount }}"> </th>
                    <th>Value / Comments</th>
                    </tr>
                  </thead>
                  <tbody>
                  @foreach($objUdfForVendor as $udfkey => $udfrow)
                  <tr  class="participantRow">
                    <td>
                      <input name={{"udffie_popup_".$udfkey}} id={{"txtudffie_popup_".$udfkey}} value="{{$udfrow->LABEL}}" class="form-control @if ($udfrow->ISMANDATORY==1) mandatory @endif" autocomplete="off" maxlength="100" disabled/>
                    </td>

                    <td hidden>
                      <input type="text" name='{{"udffie_".$udfkey}}' id='{{"hdnudffie_popup_".$udfkey}}' value="{{$udfrow->UDFVID}}" class="form-control" maxlength="100" />
                    </td>

                    <td hidden>
                      <input type="text" name={{"udffieismandatory_".$udfkey}} id={{"udffieismandatory_".$udfkey}} class="form-control" maxlength="100" value="{{$udfrow->ISMANDATORY}}" />
                    </td>

                    <td id="{{"tdinputid_".$udfkey}}">
                      @php
                        
                        $dynamicid = "udfvalue_".$udfkey;
                        $chkvaltype = strtolower($udfrow->VALUETYPE); 

                      if($chkvaltype=='date'){

                        $strinp = '<input type="date" placeholder="dd/mm/yyyy" name="'.$dynamicid.'" id="'.$dynamicid.'" class="form-control" value="" /> ';       

                      }else if($chkvaltype=='time'){

                          $strinp= '<input type="time" placeholder="h:i" name="'.$dynamicid.'" id="'.$dynamicid.'" class="form-control"  value=""/> ';

                      }else if($chkvaltype=='numeric'){
                      $strinp = '<input type="text" name="'.$dynamicid. '" id="'.$dynamicid.'" class="form-control" value=""  autocomplete="off" /> ';

                      }else if($chkvaltype=='text'){

                      $strinp = '<input type="text" name="'.$dynamicid. '" id="'.$dynamicid.'" class="form-control" value=""  autocomplete="off" /> ';

                      }else if($chkvaltype=='boolean'){
                          $strinp = '<input type="checkbox" name="'.$dynamicid. '" id="'.$dynamicid.'" class=""  /> ';

                      }else if($chkvaltype=='combobox'){
                        $strinp='';
                      $txtoptscombo =   strtoupper($udfrow->DESCRIPTIONS); ;
                      $strarray =  explode(',',$txtoptscombo);
                      $opts = '';
                      $chked='';
                        for ($i = 0; $i < count($strarray); $i++) {
                           $opts = $opts.'<option value="'.$strarray[$i].'"  >'.$strarray[$i].'</option> ';
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
						
		</form>
  </div><!--container-fluid filter-->
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
            <th width="20%" align="center">Select</th>
            <th width="40%">Code</th>
            <th width="40%">Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td width="20%" align="center"><span class="check_th">&#10004;</span></td>
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
	  <div class="tablename"><p>Vendor Group</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="cg_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th class="ROW1" style="width: 10%" align="center">Select</th> 
            <th class="ROW2" style="width: 40%">Code</th>
            <th  class="ROW3"style="width: 40%">Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
          <td  class="ROW2"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="cg_codesearch" onkeyup='colSearch("cg_tab2","cg_codesearch",1)' /></td>
          <td  class="ROW3"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="cg_namesearch" onkeyup='colSearch("cg_tab2","cg_namesearch",2)' /></td>
        </tr>
        </tbody>
      </table>
      
      <table id="cg_tab2" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody>
        @foreach ($objVendorGroupList as $index=>$rowList1)

        <tr >
          <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_VGID_REF[]" id="cgidref_{{ $rowList1->VGID }}" class="clscgidref" value="{{ $rowList1->VGID }}" ></td>
          <td class="ROW2" style="width: 39%">{{ $rowList1->VGCODE }}
          <input type="hidden" id="txtcgidref_{{ $rowList1->VGID }}" data-desc="{{ $rowList1->VGCODE }} - {{ $rowList1->DESCRIPTIONS }}" value="{{ $rowList1-> VGID }}"/>
          </td>
          <td class="ROW3" style="width: 39%">{{ $rowList1->DESCRIPTIONS }}</td>
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
            <th class="ROW1" style="width: 10%" align="center">Select</th> 
            <th class="ROW2" style="width: 40%">Code</th>
            <th  class="ROW3"style="width: 40%">Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
          <td class="ROW2" style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="gl_codesearch" onkeyup="searchGLCode()"></td>
          <td class="ROW3" style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="gl_namesearch" onkeyup="searchGLName()"></td>
        </tr>
        </tbody>
      </table>
      
      <table id="gl_tab2" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody>
        @foreach ($objGlList as $index=>$GlList)

        <tr >
          <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_GLID_REF[]" id="glref_{{ $GlList->GLID }}" class="clsglref" value="{{ $GlList->GLID }}" ></td>
          <td  class="ROW2" style="width: 39%">{{ $GlList->GLCODE }}
          <input type="hidden" id="txtglref_{{ $GlList->GLID }}" data-desc="{{ $GlList->GLCODE }} - {{ $GlList->GLNAME }}" value="{{ $GlList-> GLID }}"/>
          </td>
          <td class="ROW3" style="width: 39%">{{ $GlList->GLNAME }}</td>
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
            <th class="ROW1" style="width: 10%" align="center">Select</th> 
            <th class="ROW2" style="width: 40%">Code</th>
            <th  class="ROW3"style="width: 40%">Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
          <td class="ROW2"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control"  id="country_codesearch" onkeyup="searchCountryCode()"></td>
          <td class="ROW3"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control"  id="country_namesearch" onkeyup="searchCountryName()"></td>
        </tr>
        </tbody>
      </table>


      <table id="country_tab2" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody id="country_body">
        @foreach ($objCountryList as $index=>$CountryList)
        <tr >
          <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_CTRYID_REF[]"  id="ctryidref_{{ $CountryList->CTRYID }}" class="cls_ctryidref" value="{{ $CountryList->CTRYID }}" ></td>
          <td class="ROW2" style="width: 39%">{{ $CountryList->CTRYCODE }}
          <input type="hidden" id="txtctryidref_{{ $CountryList->CTRYID }}" data-desc="{{ $CountryList->CTRYCODE }} - {{ $CountryList->NAME }}" value="{{ $CountryList-> CTRYID }}"/>
          </td>
          <td  class="ROW3" style="width: 39%">{{ $CountryList->NAME }}</td>
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
            <th class="ROW1" style="width: 10%" align="center">Select</th> 
            <th class="ROW2" style="width: 40%">Code</th>
            <th  class="ROW3"style="width: 40%">Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
          <td class="ROW2"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="state_codesearch" onkeyup="searchStateCode()" /></td>
          <td class="ROW3"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="state_namesearch" onkeyup="searchStateName()" /></td>
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
            <th class="ROW1" style="width: 10%" align="center">Select</th> 
            <th class="ROW2" style="width: 40%">Code</th>
            <th  class="ROW3"style="width: 40%">Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
          <td class="ROW2"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="city_codesearch" onkeyup="searchCityCode()" /></td>
          <td class="ROW3"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="city_namesearch" onkeyup="searchCityName()" /></td>
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
            <th class="ROW1" style="width: 10%" align="center">Select</th> 
            <th class="ROW2" style="width: 40%" >Code</th>
            <th class="ROW3" style="width: 40%" >Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
          <td class="ROW2"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="cor_country_codesearch" onkeyup="searchCorCountryCode()" /></td>
          <td class="ROW3"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="cor_country_namesearch" onkeyup="searchCorCountryName()" /></td>
        </tr>
        </tbody>
      </table>


      <table id="cor_country_tab2" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody id="cor_country_body">
        @foreach ($objCountryList as $index=>$CountryList)
        <tr >
          <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_CORCTRYID_REF[]"  id="cor_ctryidref_{{ $CountryList->CTRYID }}" class="cls_cor_ctryidref" value="{{ $CountryList->CTRYID }}" ></td>
          <td class="ROW2" style="width: 39%">{{ $CountryList->CTRYCODE }}
          <input type="hidden" id="txtcor_ctryidref_{{ $CountryList->CTRYID }}" data-desc="{{ $CountryList->CTRYCODE }} - {{ $CountryList->NAME }}" value="{{ $CountryList->CTRYID }}"/>
          </td>
          <td class="ROW3" style="width: 39%">{{ $CountryList->NAME }}</td>
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
            <th class="ROW1" style="width: 10%" align="center">Select</th> 
            <th class="ROW2" style="width: 40%" >Code</th>
            <th class="ROW3" style="width: 40%" >Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
          <td class="ROW2"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="cor_state_codesearch" onkeyup="searchCorStateCode()" /></td>
          <td class="ROW3"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="cor_state_namesearch" onkeyup="searchCorStateName()" /></td>
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
            <th class="ROW1" style="width: 10%" align="center">Select</th> 
            <th class="ROW2" style="width: 40%" >Code</th>
            <th class="ROW3" style="width: 40%" >Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
          <td class="ROW2"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control"  id="cor_city_codesearch" onkeyup="searchCorCityCode()" /></td>
          <td class="ROW3"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control"  id="cor_city_namesearch" onkeyup="searchCorCityName()" /></td>
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
            <th class="ROW1" style="width: 10%" align="center">Select</th> 
            <th class="ROW2" style="width: 40%" >Code</th>
            <th class="ROW3" style="width: 40%" >Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
          <td class="ROW2"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="indsidref_codesearch" onkeyup="searchITCode()" /></td>
          <td class="ROW3"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="indsidref_namesearch" onkeyup="searchITName()" /></td>
        </tr>
        </tbody>
      </table>
      
      <table id="indsidref_tab2" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody>
        @foreach ($objIndTypeList as $index=>$IndType)

        <tr >
          <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_INDSID_REF[]"  id="indsidref_{{ $IndType->INDSID }}" class="clsindsidref" value="{{ $IndType->INDSID }}" ></td>
          <td class="ROW2" style="width: 39%">{{ $IndType->INDSCODE }}
          <input type="hidden" id="txtindsidref_{{ $IndType->INDSID }}" data-desc="{{ $IndType->INDSCODE }} - {{ $IndType->DESCRIPTIONS }}" value="{{ $IndType-> INDSID }}"/>
          </td>
          <td class="ROW3" style="width: 39%">{{ $IndType->DESCRIPTIONS }}</td>
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
            <th class="ROW1" style="width: 10%" align="center">Select</th> 
            <th class="ROW2" style="width: 40%" >Code</th>
            <th class="ROW3" style="width: 40%" >Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
          <td class="ROW2"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="indsvidref_codesearch" onkeyup="searchIVCode()" /></td>
          <td class="ROW3"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="indsvidref_namesearch" onkeyup="searchIVName()" /></td>
        </tr>
        </tbody>
      </table>
      
      <table id="indsvidref_tab2" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody>
        @foreach ($objIndVerList as $index=>$IndVer)

        <tr >
          <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_INDSVID_REF[]" id="indsvidref_{{ $IndVer->INDSVID }}" class="clsindsvidref" value="{{ $IndVer->INDSVID }}" ></td>
          <td class="ROW2" style="width: 39%">{{ $IndVer->INDSVCODE }}
          <input type="hidden" id="txtindsvidref_{{ $IndVer->INDSVID }}" data-desc="{{ $IndVer->INDSVCODE }} - {{ $IndVer->DESCRIPTIONS }}" value="{{ $IndVer-> INDSVID }}"/>
          </td>
          <td class="ROW3" style="width: 39%">{{ $IndVer->DESCRIPTIONS }}</td>
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
            <th class="ROW1" style="width: 10%" align="center">Select</th> 
            <th class="ROW2" style="width: 40%" >Code</th>
            <th class="ROW3" style="width: 40%" >Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
          <td class="ROW2"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="loc_country_codesearch"  onkeyup='colSearch("loc_country_tab2","loc_country_codesearch",1)' /></td>
          <td class="ROW3"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="loc_country_namesearch"  onkeyup='colSearch("loc_country_tab2","loc_country_namesearch",2)' /></td>
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
        <tr >
          <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_LOCCTRYID_REF[]" id="loc_ctryidref_{{ $CountryList->CTRYID }}" class="cls_loc_ctryidref" value="{{ $CountryList->CTRYID }}" ></td>
          <td class="ROW2" style="width: 39%">{{ $CountryList->CTRYCODE }}
          <input type="hidden" id="txtloc_ctryidref_{{ $CountryList->CTRYID }}" data-desc="{{ $CountryList->CTRYCODE }} - {{ $CountryList->NAME }}" value="{{ $CountryList-> CTRYID }}"/>
          </td>
          <td class="ROW3" style="width: 39%">{{ $CountryList->NAME }}</td>
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
            <th class="ROW1" style="width: 10%" align="center">Select</th> 
            <th class="ROW2" style="width: 40%" >Code</th>
            <th class="ROW3" style="width: 40%" >Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
          <td class="ROW2"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="loc_state_codesearch"   onkeyup='colSearch("loc_state_tab2","loc_state_codesearch",1)' /></td>
          <td class="ROW3"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="loc_state_namesearch"  onkeyup='colSearch("loc_state_tab2","loc_state_namesearch",2)' /></td>
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
            <th class="ROW1" style="width: 10%" align="center">Select</th> 
            <th class="ROW2" style="width: 40%" >Code</th>
            <th class="ROW3" style="width: 40%" >Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
          <td class="ROW2"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="loc_city_codesearch"  onkeyup='colSearch("loc_city_tab2","loc_city_codesearch",1)' /></td>
          <td class="ROW3"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="loc_city_namesearch"  onkeyup='colSearch("loc_city_tab2","loc_city_namesearch",2)' /></td>
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
      <tbody>
      @foreach ($objUdfForVendor as $index=>$UdffieRow)
      <tr id="udffie_{{ $UdffieRow->UDFVID }}" class="clsudffie">
        <td style="width: 50%">{{$UdffieRow->LABEL }}  <input type="hidden" id="txtudffie_{{ $UdffieRow->UDFVID }}" value="{{ $UdffieRow-> UDFVID }}"
          data-desc="{{ $UdffieRow->LABEL }}"  data-ismandatory="{{ $UdffieRow->ISMANDATORY }}"  data-valtype="{{ $UdffieRow->VALUETYPE }}" data-optscombo="{{ $UdffieRow->DESCRIPTIONS }}"  />
        </td>
        <td >{{ $UdffieRow->VALUETYPE }} 
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
$(document).ready(function(e) {
  $('#PHNO').ForceNumericOnly();
  $('#MONO').ForceNumericOnly();
  $('[id*="POC_MONO"]').ForceNumericOnly();
  $('[id*="LOC_MONO"]').ForceNumericOnly();
});

// CG popup function
$("#VGID_REF_POPUP").on("click",function(event){ 
  $("#cgidrefpopup").show();
});

$("#VGID_REF_POPUP").keyup(function(event){
  if(event.keyCode==13){
    $("#cgidrefpopup").show();
  }
});

$("#cgidrefpopup_close").on("click",function(event){ 
  $("#cgidrefpopup").hide();
});

$('.clscgidref').click(function(){
    var id = $(this).attr('id');
    var txtval =    $("#txt"+id+"").val();
    var texdesc =   $("#txt"+id+"").data("desc")

    $("#VGID_REF_POPUP").val(texdesc);
    $("#VGID_REF").val(txtval);
	
    $("#VGID_REF_POPUP").blur(); 
    $("#OLD_REFCODE").focus(); 
    $("#cgidrefpopup").hide();

   // $("#cg_codesearch").val(''); 
    //$("#cg_namesearch").val(''); 
    // searchCGCode();

    colSearchClear("cg_tab1","clscgidref");
    colSearch("cg_tab2","cg_codesearch",1);
    
    event.preventDefault();

});

let input, filter, table, tr, td, i, txtValue;
function colSearch(ptable,ptxtbox,pcolindex) {
  //var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById(ptxtbox);
  filter = input.value.toUpperCase();
  table = document.getElementById(ptable);
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[pcolindex];
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
function colSearchClear(ptable1,pclsname) {
  //clear text box value
  $('#'+ptable1+' input[type="text"]').each(function () {
      $(this).val('');
   });
  
  //clear row 
  $('.'+pclsname).each(function () {
      $(this).removeAttr("style");
   });
}


 function searchCGCode() {
//   var input, filter, table, tr, td, i, txtValue;
//   input = document.getElementById("cg_codesearch");
//   filter = input.value.toUpperCase();
//   table = document.getElementById("cg_tab2");
//   tr = table.getElementsByTagName("tr");
//   for (i = 0; i < tr.length; i++) {
//     td = tr[i].getElementsByTagName("td")[0];
//     if (td) {
//       txtValue = td.textContent || td.innerText;
//       if (txtValue.toUpperCase().indexOf(filter) > -1) {
//         tr[i].style.display = "";
//       } else {
//         tr[i].style.display = "none";
//       }
//     }       
//   }
 }

 function searchCGName() {
//       var input, filter, table, tr, td, i, txtValue;
//       input = document.getElementById("cg_namesearch");
//       filter = input.value.toUpperCase();
//       table = document.getElementById("cg_tab2");
//       tr = table.getElementsByTagName("tr");
//       for (i = 0; i < tr.length; i++) {
//         td = tr[i].getElementsByTagName("td")[1];
//         if (td) {
//           txtValue = td.textContent || td.innerText;
//           if (txtValue.toUpperCase().indexOf(filter) > -1) {
//             tr[i].style.display = "";
//           } else {
//             tr[i].style.display = "none";
//           }
//         }       
//   }
 }

// GL popup function

$("#GLID_REF_POPUP").on("click",function(event){ 
  $("#glrefpopup").show();
});

$("#GLID_REF_POPUP").keyup(function(event){
  if(event.keyCode==13){
    $("#glrefpopup").show();
  }
});

$("#glrefpopup_close").on("click",function(event){ 
  $("#glrefpopup").hide();
});

$('.clsglref').click(function(){
    var id = $(this).attr('id');
    var txtval =    $("#txt"+id+"").val();
    var texdesc =   $("#txt"+id+"").data("desc")

    $("#GLID_REF_POPUP").val(texdesc);
    $("#GLID_REF").val(txtval);
	
    $("#GLID_REF_POPUP").blur(); 
    $("#REGADDL1").focus(); 
    $("#glrefpopup").hide();

    $("#gl_codesearch").val(''); 
    $("#gl_namesearch").val(''); 
    searchGLCode();
    event.preventDefault();

});

function searchGLCode() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("gl_codesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("gl_tab2");
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

function searchGLName() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("gl_namesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("gl_tab2");
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

// Country popup function
$("#REGCTRYID_REF_POPUP").on("click",function(event){ 
  $("#ctryidref_popup").show();
});


$("#ctryidref_close").on("click",function(event){ 
  $("#ctryidref_popup").hide();
});

$('.cls_ctryidref').click(function(){
  var id = $(this).attr('id');
  var txtval =    $("#txt"+id+"").val();
  var texdesc =   $("#txt"+id+"").data("desc")

  $("#REGCTRYID_REF_POPUP").val(texdesc);
  $("#REGCTRYID_REF").val(txtval);

  
  
 //  $("#TXTLOC_CTRYID_REF_POPUP_0").val(texdesc);
 //  $("#HDNLOC_CTRYID_REF_POPUP_0").val(txtval);
    

  getCountryWiseState(txtval);
  
  $("#REGCTRYID_REF_POPUP").blur(); 
  $("#REGSTID_REF_POPUP").focus(); 
  $("#ctryidref_popup").hide();
  $(this).prop("checked",false);

  colSearchClear("country_tab1","cls_ctryidref");
  searchCountryCode();
  
});

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

function getCountryWiseState(CTRYID_REF){
    $("#state_body").html('');
		$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
		
    $.ajax({
        url:'{{route("master",[48,"getCountryWiseState"])}}',
        type:'POST',
        data:{CTRYID_REF:CTRYID_REF},
        success:function(data) {
          
            $("#REGSTID_REF_POPUP").val('');
            $("#REGSTID_REF").val('');
			      $("#REGCITYID_REF_POPUP").val('');
            $("#REGCITYID_REF").val('');
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

$("#REGSTID_REF_POPUP").on("click",function(event){ 
  $("#stidref_popup").show();
});

$("#REGSTID_REF_POPUP").keyup(function(event){
  if(event.keyCode==13){
    $("#stidref_popup").show();
  }
});

$("#stidref_close").on("click",function(event){ 
  $("#stidref_popup").hide();
});

function bindStateEvents(){

  $('.cls_stidref').click(function(){

    var id = $(this).attr('id');
    var txtval =    $("#txt"+id+"").val();
    var texdesc =   $("#txt"+id+"").data("desc")

    $("#REGSTID_REF_POPUP").val(texdesc);
    $("#REGSTID_REF").val(txtval);
	
	var CTRYID_REF	=	$("#REGCTRYID_REF").val();
	
	getStateWiseCity(CTRYID_REF,txtval);
	
	$("#REGSTID_REF_POPUP").blur(); 
	$("#REGCITYID_REF_POPUP").focus(); 
	
    $("#stidref_popup").hide();
    colSearchClear("state_tab1","cls_stidref");
    searchStateCode();
    searchStateName();

    $(this).prop("checked",false);
    
  });
}

function searchStateCode() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("state_codesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("state_tab2");
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

function searchStateName() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("state_namesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("state_tab2");
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

function getStateWiseCity(CTRYID_REF,STID_REF){
    $("#city_body").html('');
		$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
		
    $.ajax({
        url:'{{route("master",[48,"getStateWiseCity"])}}',
        type:'POST',
        data:{CTRYID_REF:CTRYID_REF,STID_REF:STID_REF},
        success:function(data) {
          
            $("#REGCITYID_REF_POPUP").val('');
            $("#REGCITYID_REF").val('');

            $("#city_body").html(data);
            bindCityEvents(); 
			
        },
        error:function(data){
          console.log("Error: Something went wrong.");
          $("#city_body").html('');
          
        },
    });	
  }

// Citiy popup function

$("#REGCITYID_REF_POPUP").on("click",function(event){ 
  $("#cityidref_popup").show();
});

$("#REGCITYID_REF_POPUP").keyup(function(event){
  if(event.keyCode==13){
    $("#cityidref_popup").show();
  }
});

$("#cityidref_close").on("click",function(event){ 
  $("#cityidref_popup").hide();
});

function bindCityEvents(){

	$('.cls_cityidref').click(function(){

		var id = $(this).attr('id');
		var txtval =    $("#txt"+id+"").val();
		var texdesc =   $("#txt"+id+"").data("desc")

		$("#REGCITYID_REF_POPUP").val(texdesc);
		$("#REGCITYID_REF").val(txtval);

		$("#cityidref_popup").hide();
		colSearchClear("city_tab1","cls_cityidref");
    searchCityCode();
    searchCityName();

    $(this).prop("checked",false);

	});
}

function searchCityCode() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("city_codesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("city_tab2");
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

function searchCityName() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("city_namesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("city_tab2");
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

// Corporate country popup function

$("#CORPCTRYID_REF_POPUP").on("click",function(event){ 
  $("#cor_ctryidref_popup").show();
});

$("#CORPCTRYID_REF_POPUP").keyup(function(event){
  if(event.keyCode==13){
    $("#cor_ctryidref_popup").show();
  }
});

$("#cor_ctryidref_close").on("click",function(event){ 
  $("#cor_ctryidref_popup").hide();
});

$('.cls_cor_ctryidref').click(function(){
  var id = $(this).attr('id');
  var txtval =    $("#txt"+id+"").val();
  var texdesc =   $("#txt"+id+"").data("desc")

  $("#CORPCTRYID_REF_POPUP").val(texdesc);
  $("#CORPCTRYID_REF").val(txtval);

  getCorCountryWiseState(txtval);
  
  $("#CORPCTRYID_REF_POPUP").blur(); 
  $("#CORPSTID_REF_POPUP").focus(); 
  
  $("#cor_ctryidref_popup").hide();
  searchCorCountryCode();
  searchCorCountryName();

  $(this).prop("checked",false);
  event.preventDefault();
});

function searchCorCountryCode() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("cor_country_codesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("cor_country_tab2");
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

function searchCorCountryName() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("cor_country_namesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("cor_country_tab2");
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

function getCorCountryWiseState(CTRYID_REF){
    $("#cor_state_body").html('');
		$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
		
    $.ajax({
        url:'{{route("master",[48,"getCorCountryWiseState"])}}',
        type:'POST',
        data:{CTRYID_REF:CTRYID_REF},
        success:function(data) {
          
            $("#CORPSTID_REF_POPUP").val('');
            $("#CORPSTID_REF").val('');
			$("#CORPCITYID_REF_POPUP").val('');
            $("#CORPCITYID_REF").val('');
			$("#cor_city_body").html('');
            $("#cor_state_body").html(data);
            bindCorStateEvents(); 

        },
        error:function(data){
          console.log("Error: Something went wrong.");
          $("#state_body").html('');
          
        },
    });	
  }

// Corporate state popup function

$("#CORPSTID_REF_POPUP").on("click",function(event){ 
  $("#cor_stidref_popup").show();
});

$("#CORPSTID_REF_POPUP").keyup(function(event){
  if(event.keyCode==13){
    $("#cor_stidref_popup").show();
  }
});

$("#cor_stidref_close").on("click",function(event){ 
  $("#cor_stidref_popup").hide();
});

function bindCorStateEvents(){
  $('.cls_cor_stidref').click(function(){
    var id = $(this).attr('id');
    var txtval =    $("#txt"+id+"").val();
    var texdesc =   $("#txt"+id+"").data("desc")

    $("#CORPSTID_REF_POPUP").val(texdesc);
    $("#CORPSTID_REF").val(txtval);
	
	var CTRYID_REF	=	$("#CORPCTRYID_REF").val();
	
	getCorStateWiseCity(CTRYID_REF,txtval);
	
	$("#CORPSTID_REF_POPUP").blur(); 
	$("#CORPCITYID_REF_POPUP").focus(); 
	
    $("#cor_stidref_popup").hide();
    searchCorStateCode();
    searchCorStateName();
    $(this).prop("checked",false);

    event.preventDefault();
  });
}

function searchCorStateCode() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("cor_state_codesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("cor_state_tab2");
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

function searchCorStateName() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("cor_state_namesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("cor_state_tab2");
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

function getCorStateWiseCity(CTRYID_REF,STID_REF){
    $("#cor_city_body").html('');
		$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
		
    $.ajax({
        url:'{{route("master",[48,"getCorStateWiseCity"])}}',
        type:'POST',
        data:{CTRYID_REF:CTRYID_REF,STID_REF:STID_REF},
        success:function(data) {
          
            $("#CORPCITYID_REF_POPUP").val('');
            $("#CORPCITYID_REF").val('');

            $("#cor_city_body").html(data);
            bindCorCityEvents(); 
			
        },
        error:function(data){
          console.log("Error: Something went wrong.");
          $("#cor_city_body").html('');
          
        },
    });	
  }

// Citiy popup function

$("#CORPCITYID_REF_POPUP").on("click",function(event){ 
  $("#cor_cityidref_popup").show();
});

$("#CORPCITYID_REF_POPUP").keyup(function(event){
  if(event.keyCode==13){
    $("#cor_cityidref_popup").show();
  }
});

$("#cor_cityidref_close").on("click",function(event){ 
  $("#cor_cityidref_popup").hide();
});

function bindCorCityEvents(){

	$('.cls_cor_cityidref').click(function(){

		var id = $(this).attr('id');
		var txtval =    $("#txt"+id+"").val();
		var texdesc =   $("#txt"+id+"").data("desc")

		$("#CORPCITYID_REF_POPUP").val(texdesc);
		$("#CORPCITYID_REF").val(txtval);

		$("#cor_cityidref_popup").hide();
		
		searchCorCityCode();
    $(this).prop("checked",false);
		event.preventDefault();
	});
}

function searchCorCityCode() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("cor_city_codesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("cor_city_tab2");
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

function searchCorCityName() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("cor_city_namesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("cor_city_tab2");
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

// Industry type popup function

$("#INDSID_REF_POPUP").on("click",function(event){ 
  $("#indsidrefpopup").show();
});

$("#INDSID_REF_POPUP").keyup(function(event){
  if(event.keyCode==13){
    $("#indsidrefpopup").show();
  }
});

$("#indsidrefpopup_close").on("click",function(event){ 
  $("#indsidrefpopup").hide();
});

$('.clsindsidref').click(function(){
    var id = $(this).attr('id');
    var txtval =    $("#txt"+id+"").val();
    var texdesc =   $("#txt"+id+"").data("desc")

    $("#INDSID_REF_POPUP").val(texdesc);
    $("#INDSID_REF").val(txtval);
	
    $("#INDSID_REF_POPUP").blur(); 
    $("#INDSVID_REF_POPUP").focus(); 
    $("#indsidrefpopup").hide();

    $("#indsidref_codesearch").val(''); 
    $("#indsidref_namesearch").val(''); 
    searchITCode();
    searchITName();
    $(this).prop("checked",false);
    event.preventDefault();

});

function searchITCode() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("indsidref_codesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("indsidref_tab2");
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

function searchITName() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("indsidref_namesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("indsidref_tab2");
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

// Industry vertical popup function

$("#INDSVID_REF_POPUP").on("click",function(event){ 
  $("#indsvidrefpopup").show();
});

$("#INDSVID_REF_POPUP").keyup(function(event){
  if(event.keyCode==13){
    $("#indsvidrefpopup").show();
  }
});

$("#indsvidrefpopup_close").on("click",function(event){ 
  $("#indsvidrefpopup").hide();
});

$('.clsindsvidref').click(function(){
    var id = $(this).attr('id');
    var txtval =    $("#txt"+id+"").val();
    var texdesc =   $("#txt"+id+"").data("desc")

    $("#INDSVID_REF_POPUP").val(texdesc);
    $("#INDSVID_REF").val(txtval);
	
    $("#INDSVID_REF_POPUP").blur(); 
    $("#DEALSIN").focus(); 
    $("#indsvidrefpopup").hide();

    $("#indsvidref_codesearch").val(''); 
    $("#indsvidref_namesearch").val(''); 
    searchIVCode();
    $(this).prop("checked",false);
    event.preventDefault();

});

function searchIVCode() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("indsvidref_codesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("indsvidref_tab2");
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

function searchIVName() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("indsvidref_namesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("indsvidref_tab2");
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

// Add remove table row point of contact

$('#table1').on('click','.add',function() {

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
  $clone.find('.remove').removeAttr('disabled'); 
  $clone.find('input:text').removeAttr('required'); 

  $tr.closest('table').append($clone);   
  var rowCount = $('#Row_Count1').val();
  rowCount = parseInt(rowCount)+1;
  $('#Row_Count1').val(rowCount);


});

$("#table1").on('click', '.remove', function() {
  var rowCount = $(this).closest('table').find('tbody').length;
  if (rowCount > 1) {
    $(this).closest('tbody').remove();
  }
  if (rowCount <= 1) {
    $(document).find('.remove').prop('disabled', false);
  }
});

// Add remove table row bank

$("#table2").on('click', '.add', function() {
    
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
    $clone.find('.remove').removeAttr('disabled'); 
    $tr.closest('table').append($clone);   
    var rowCount = $('#Row_Count2').val();
      rowCount = parseInt(rowCount)+1;
      $('#Row_Count2').val(rowCount);

}); 

$("#table2").on('click', '.remove', function() {
    var rowCount = $(this).closest('table').find('tbody').length;
    if (rowCount > 1) {
      $(this).closest('tbody').remove();
    }
    if (rowCount <= 1) {
      $(document).find('.remove').prop('disabled', false);
    }
});

// Add remove table row location

$("#table3").on('click', '.add', function() {
    
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
    $clone.find('.remove').removeAttr('disabled'); 

    $clone.find('input:checkbox').prop('checked',false);;

    $tr.closest('table').append($clone);   
    var rowCount = $('#Row_Count3').val();
      rowCount = parseInt(rowCount)+1;
      $('#Row_Count3').val(rowCount);

}); 

$("#table3").on('click', '.remove', function() {
    var rowCount = $(this).closest('table').find('tbody').length;
    if (rowCount > 1) {
      $(this).closest('tbody').remove();
    }
    if (rowCount <= 1) {
      $(document).find('.remove').prop('disabled', false);
    }
});

</script>


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
      let tid1 = "#cg_tab1";
      let tid2 = "#cg_tab2";
      let cg_headers = document.querySelectorAll(tid1 + " th");

      // Sort the table element when clicking on the table headers
      cg_headers.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(tid2, ".clscgidref", "td:nth-child(" + (i + 1) + ")");
        });
      });
      
      //GL
      let tidgl1 = "#gl_tab1";
      let tidgl2 = "#gl_tab2";
      let gl_headers = document.querySelectorAll(tidgl1 + " th");

      // Sort the table element when clicking on the table headers
      gl_headers.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(tidgl2, ".clsglref", "td:nth-child(" + (i + 1) + ")");
        });
      });
     
     //COUNTRY
      let tidcon1 = "#country_tab1";
      let tidcon2 = "#country_tab2";
      let con_headers = document.querySelectorAll(tidcon1 + " th");

      // Sort the table element when clicking on the table headers
      con_headers.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(tidcon2, ".cls_ctryidref", "td:nth-child(" + (i + 1) + ")");
        });
      });

      //STATE
      let tidsta1 = "#state_tab1";
      let tidsta2 = "#state_tab2";
      let sta_headers = document.querySelectorAll(tidsta1 + " th");

      // Sort the table element when clicking on the table headers
      sta_headers.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(tidsta2, ".cls_stidref", "td:nth-child(" + (i + 1) + ")");
        });
      });

      
      //CITY
      let tidcit1 = "#city_tab1";
      let tidcit2 = "#city_tab2";
      let cit_headers = document.querySelectorAll(tidcit1 + " th");

      // Sort the table element when clicking on the table headers
      cit_headers.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(tidcit2, ".cls_cityidref", "td:nth-child(" + (i + 1) + ")");
        });
      });

      let tidp1 = '';
      let tidp2 = '';
      let clsname = '';          
      let p_headers = '';
      function doSorting(ptable1,ptable2,pclass){


           tidp1 = "#"+ptable1;
           tidp2 = "#"+ptable2;
           clsname = "."+pclass;          
           p_headers = document.querySelectorAll(tidp1 + " th");

          // Sort the table element when clicking on the table headers
          p_headers.forEach(function(element, i) {
            element.addEventListener("click", function() {
              w3.sortHTML(tidp2, clsname, "td:nth-child(" + (i + 1) + ")");
            });
          });

      }

      //------------

      let tidudffie = "#udffieexample2345";
      let tidudffie2 = "#udffieexample23";
      let headersudffie = document.querySelectorAll(tidudffie2 + " th");

      // Sort the table element when clicking on the table headers
      headersudffie.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(tidudffie, ".clsudffie", "td:nth-child(" + (i + 1) + ")");
        });
      });

      

  //loc country code
  $('#table3').on ("focus",'[id*="TXTLOC_CTRYID_REF_POPUP"]',function(event){
        $("#loc_country_popup").show();
        var id = $(this).attr('id');
        var id2 = $(this).parent().parent().find('[id*="HDNLOC_CTRYID_REF_POPUP"]').attr('id');
        $('#hdn_locctryfieldid').val(id);
        $('#hdn_locctryfieldid2').val(id2);        

  });

  $("#loc_country_popup_close").on("click",function(event){
        $("#loc_country_popup").hide();
  });

  $('#loc_country_tab2').on("click",".cls_loc_ctryidref",function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc")

        var txtid= $('#hdn_locctryfieldid').val();
        var txt_id2= $('#hdn_locctryfieldid2').val();
        
        $('#'+txtid).val(texdesc);
        $('#'+txt_id2).val(txtval);

        $('#'+txtid).blur();  

        //clear STATE
        $('#'+txtid).parent().parent().find('[id*="TXTLOC_STID_REF_POPUP"]').val('');
        $('#'+txtid).parent().parent().find('[id*="HDNLOC_STID_REF_POPUP"]').val('');
        //clear CITY
        $('#'+txtid).parent().parent().find('[id*="TXTLOC_CITYID_REF_POPUP"]').val('');
        $('#'+txtid).parent().parent().find('[id*="HDNLOC_CITYID_REF_POPUP"]').val('');

        $("#loc_country_popup").hide();
        $(this).prop("checked",false);
   
});
//loc country end  


//loc state 
$('#table3').on ("focus","[id*='TXTLOC_STID_REF_POPUP']",function(event){

        var id3 = $(this).parent().parent().find('[id*="HDNLOC_CTRYID_REF_POPUP"]').attr('id');
        var parentCustid = $('#'+id3+'').val();  //location country id
        // $("#loc_state_popup").show();

        if(parentCustid=='') {

            $("#alert").modal('show');
            $("#AlertMessage").text('Please select Country first.');
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk1');

        } else if(parentCustid!=''){

          var id = $(this).attr('id');
          var id2 = $(this).parent().parent().find('[id*="HDNLOC_STID_REF_POPUP"]').attr('id');
          $('#hdn_locstatefieldid').val(id);
          $('#hdn_locstatefieldid2').val(id2);    

          $("#loc_state_popup").show();

          //----------
          // $("#itesubgrp_popup").val('');
          // $("#itesubgrp_id").val('');
          $('#loc_state_body').html('<tr><td colspan="2">Please wait..</td></tr>');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url:'{{route("master",[48,"getCountryWiseState"])}}',
                type:'POST',
                data:{'CTRYID_REF':parentCustid},
                success:function(data) {
                    $('#loc_state_body').html(data);
                    bindStateEvent();
                },
                error:function(data){
                  console.log("Error: Something went wrong.");
                  $('#loc_state_body').html('');
                },
            });   
        }      

});

  $("#loc_state_popup_close").on("click",function(event){
        $("#loc_state_popup").hide();
  });

  function bindStateEvent(){

    $('#loc_state_tab2').off();  //unbind all previous events

     $('#loc_state_tab2').on("click",".cls_stidref",function(){

        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc")

        var txtid= $('#hdn_locstatefieldid').val();
        var txt_id2= $('#hdn_locstatefieldid2').val();
  
        var selected_data  = [];
        
        $("#table3 .participantRow").each(function () {

          $('#'+txtid).val(texdesc);
          $('#'+txt_id2).val(txtval);
          $('#'+txtid).blur();        

        }); //loop on row

        $("#loc_state_popup").hide();
          
        //clear CITY
        $('#'+txtid).parent().parent().find('[id*="TXTLOC_CITYID_REF_POPUP"]').val('');
        $('#'+txtid).parent().parent().find('[id*="HDNLOC_CITYID_REF_POPUP"]').val('');
          
        
      });//click
     
  } ///bindStateEvent
  //loc state end

//----------------
//loc city begin 
$('#table3').on ("focus","[id*='TXTLOC_CITYID_REF_POPUP']",function(event){

  var countryid = $(this).parent().parent().find('[id*="HDNLOC_CTRYID_REF_POPUP"]').attr('id');  //country
  var parentCountryid = $('#'+countryid+'').val();  //location country id

  var id3 = $(this).parent().parent().find('[id*="HDNLOC_STID_REF_POPUP"]').attr('id'); 
  var parentStatetid = $('#'+id3+'').val();  //location STATE id

// $("#loc_state_popup").show();

if(parentCountryid=='') {

  $("#alert").modal('show');
  $("#AlertMessage").text('Please select Country first.');
  $("#YesBtn").hide();
  $("#NoBtn").hide();
  $("#OkBtn1").show();
  $("#OkBtn1").focus();
  highlighFocusBtn('activeOk1');

}else if(parentStatetid=='') {

    $("#alert").modal('show');
    $("#AlertMessage").text('Please select State first.');
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk1');

} else if(parentCountryid!='' && parentStatetid!=''){

  var id = $(this).attr('id');
  var id2 = $(this).parent().parent().find('[id*="HDNLOC_CITYID_REF_POPUP"]').attr('id');
    $('#hdn_loccityfieldid').val(id);
    $('#hdn_loccityfieldid2').val(id2);    

    $("#loc_city_popup").show();

  //----------
  // $("#itesubgrp_popup").val('');
  // $("#itesubgrp_id").val('');
  $('#loc_city_body').html('<tr><td colspan="2">Please wait..</td></tr>');

      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
      $.ajax({
          url:'{{route("master",[48,"getStateWiseCity"])}}',
          type:'POST',
          data:{'CTRYID_REF':parentCountryid,'STID_REF':parentStatetid},
          success:function(data) {
              $('#loc_city_body').html(data);
              bindCityEvent();
          },
          error:function(data){
            console.log("Error: Something went wrong.");
            $('#loc_city_body').html('');
          },
      });   
    }      

  });

  $("#loc_city_popup_close").on("click",function(event){
      $("#loc_city_popup").hide();
  });

  function bindCityEvent(){

    $('#loc_city_tab2').off();  //unbind all previous events

    $('#loc_city_tab2').on("click",".cls_cityidref",function(){

    var fieldid = $(this).attr('id');
    var txtval =    $("#txt"+fieldid+"").val();
    var texdesc =   $("#txt"+fieldid+"").data("desc")

    var txtid= $('#hdn_loccityfieldid').val();
    var txt_id2= $('#hdn_loccityfieldid2').val();

    var selected_data  = [];

    $("#table3 .participantRow").each(function () {

      $('#'+txtid).val(texdesc);
      $('#'+txt_id2).val(txtval);
      $('#'+txtid).blur();        

    }); //loop on row

      $("#loc_city_popup").hide();

    });// click

  } ///bindCityEvent
//loc city end
//----------------







  function attr2Function() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("attr2codesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("attrcodetable");
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

  //------------------------
  //----------------------
  //------------------------
  //udf field
  
  
  
//udf field function start
  $('#udffietable').on ("focus","[id*='txtudffie_popup']",function(event){
          $("#udffiepopup").show();
        var id = $(this).attr('id');
        var id2 = $(this).parent().parent().find('[id*="hdnudffie_popup"]').attr('id');
        var id3 = $(this).parent().parent().find('[id*="udffieismandatory"]').attr('id');
        var id4 = $(this).parent().parent().find('[id*="tdinputid"]').attr('id');      //id - of creating dynamic input

        $('#hdn_udffiefieldid').val(id);
        $('#hdn_udffiefieldid2').val(id2);      
        $('#hdn_udffiefieldid3').val(id3);      
        $('#hdn_udffiefieldid4').val(id4);      
  });

  $("#udffie_closePopup").on("click",function(event){
    $("#udffiepopup").hide();
  });

  $(".clsudffie").dblclick(function(){

    var fieldid = $(this).attr('id');
    var txtval =    $("#txt"+fieldid+"").val();
    var texdesc =   $("#txt"+fieldid+"").data("desc");
    var txtismandatory =   $("#txt"+fieldid+"").data("ismandatory");
    var txtvaltype =   $("#txt"+fieldid+"").data("valtype");
    //var txtoptscombo =   $("#txt"+fieldid+"").data("data-optscombo");

    //------------------
    var selected_data  = [];
        $("[id*=hdnudffie_popup]").each(function(){
            if( $.trim( $(this).val() ) !== "" )
            {
              selected_data.push($(this).val());
            }
        });

        if(jQuery.inArray(txtval, selected_data) !== -1){
            $("#alert").modal('show');
            $("#AlertMessage").text('Already selected. Please select another UDF field.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk1');
            $("#udffiepopup").hide();
            $("#udffienamesearch").val(''); 
            $("#udffiecodesearch").val(''); 
            udffieFunction();
            event.preventDefault();
            return false;
        }                 
    //-------------------

    var txtid= $('#hdn_udffiefieldid').val();
    var txt_id2= $('#hdn_udffiefieldid2').val();
    var txt_id3 = $('#hdn_udffiefieldid3').val();
    var txt_id4 = $('#hdn_udffiefieldid4').val();  //<td> id 

    var strdyn = txt_id4.split('_');
    var lastele =   strdyn[strdyn.length-1];

    var dynamicid = "udfvalue_"+lastele;

    var chkvaltype =  txtvaltype.toLowerCase();
    var strinp = '';

    if(chkvaltype=='date'){

      strinp = '<input type="date" placeholder="dd/mm/yyyy" name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" /> ';       

    }else if(chkvaltype=='time'){
      strinp= '<input type="time" placeholder="h:i" name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" /> ';

    }else if(chkvaltype=='numeric'){
      strinp = '<input type="text" name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" autocomplete="off" /> ';

    }else if(chkvaltype=='text'){

      strinp = '<input type="text" name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" autocomplete="off" /> ';
    
    }else if(chkvaltype=='boolean'){

      strinp = '<input type="checkbox" name="'+dynamicid+ '" id="'+dynamicid+'" class="" /> ';
    
    }else if(chkvaltype=='combobox'){

      var txtoptscombo =   $("#txt"+fieldid+"").data("optscombo");
      var strarray = txtoptscombo.split(',');
      var opts = '';

      for (var i = 0; i < strarray.length; i++) {
        opts = opts + '<option value="'+strarray[i]+'">'+strarray[i]+'</option> ';
      }

      strinp = '<select name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" required>'+opts+'</select>' ;

    }

    $('#'+txt_id4).html('');  
    $('#'+txt_id4).html(strinp);   //set dynamic input
    
    $('#'+txtid).val(texdesc);  // lable
    $('#'+txt_id2).val(txtval);  // udfitemid
    $('#'+txt_id3).val(txtismandatory); // mandatory

    $("#udffiepopup").hide();
    $("#udffienamesearch").val(''); 
        $("#udffiecodesearch").val(''); 
        udffieFunction();
        event.preventDefault();
  });

  function udffieFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("udffiecodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("udffieexample2345");
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

  function udffieNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("udffienamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("udffieexample2345");
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

  //check blank
  $('#udffietable').on ("blur","[id*='udfvalue']",function(event){

        var ismand = $(this).parent().parent().find('[id*="udffieismandatory"]').val();
        var txtval = $.trim( $(this).val() );

        if(ismand==1 && txtval==""){
                $("#alert").modal('show');
                $("#AlertMessage").text('Please enter value.');
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk1');
                $(this).focus();
                return false;
        }

   
  });
 
  
  //technical specifiation
  $('#table2').on("blur",'[id*="TSVALUE"]', function( event ) {
    
        $("#table2 .participantRow").each(function () {
          var txt_tstype = $.trim($(this).find('[id*="TSTYPE"]').val() );
          var txt_tsvalue = $.trim($(this).find('[id*="TSVALUE"]').val() );
          if(txt_tstype!==''){
            if(txt_tsvalue=='')
            {
                    $("#alert").modal('show');
                    $("#AlertMessage").text('Please enter TS value');
                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn1").show();
                    $("#OkBtn1").focus();
                    highlighFocusBtn('activeOk1');
                    return false;
            }
          }
        }); //loop on row
  });
  
  //----------------------


/* form validation */

var formItemMst = $( "#add_form_data" );
  formItemMst.validate();

$("#VCODE").blur(function(){
	$(this).val($.trim( $(this).val() ));
	$("#ERROR_VCODE").hide();
	validateSingleElemnet("VCODE"); 
});

$("#VCODE").rules( "add",{
	required: true,
	nowhitespace: true,
	//StringNumberRegex: true,
	messages: {
		required: "Required field.",
	}
});


$("#EMAILID").blur(function(){
  $(this).val($.trim( $(this).val() ));
  $("#ERROR_EMAILID").hide();
  validateSingleElemnet("EMAILID");
});

$("#EMAILID").rules( "add",{
	required: false,
	nowhitespace: true,
	EmailValidate: true,
	messages: {
		required: "Required field.",
	}
});



function validateSingleElemnet(element_id){
	var validator =$("#add_form_data" ).validate();
	
	if(validator.element( "#"+element_id+"" )){
		
		if(element_id=="VCODE" || element_id=="VCODE" ) {
			checkDuplicateCode();
		}
		
	 }
}

function checkDuplicateCode(){
	var codedata = $("#VCODE").val(); 
	
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	
	$.ajax({
		url:'{{route("master",[48,"codeduplicate"])}}',
		type:'POST',
		data:{'VCODE': codedata},
		success:function(data) {
			if(data.exists) {
				$(".text-danger").hide();
				showError('ERROR_VCODE',data.msg);
				$("#VCODE").focus();
			}                                
		},
		error:function(data){
		  console.log("Error: Something went wrong.");
		},
	});
}

$( "#btnSaveItem" ).click(function() {

	if(formItemMst.valid()){
    event.preventDefault();

      var VCODE          =   $.trim($("#VCODE").val());
      if(VCODE ===""){
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").hide();  
        $("#OkBtn1").show();  
        $("#AlertMessage").text('Please enter Vendor Code.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
      }
    
      if($.trim($("#GSTTYPE").val() )=='')
      {
        $("#alert").modal('show');
        $("#AlertMessage").text('Please Select GST TYPE in Statutory Tab.');
        $("#YesBtn").hide(); 
        $("#NoBtn").hide();  
        $("#OkBtn1").show();
        $("#OkBtn1").focus();
        highlighFocusBtn('activeOk1'); 
        return false;
      }
      else{
        
        if($.trim( $("#GSTTYPE option:selected").val() )==1 && $.trim($("#GSTIN").val()) == ''){
         
          $("#alert").modal('show');
          $("#AlertMessage").text('Please enter value for GSTIN in Statutory Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk1'); 
          return false;

        }
      }

      

      if($.trim($("#DEFCRID_REF").val() )=='')
      {
        $("#alert").modal('show');
        $("#AlertMessage").text('Please Select Default Currency in Statutory Tab.');
        $("#YesBtn").hide(); 
        $("#NoBtn").hide();  
        $("#OkBtn1").show();
        $("#OkBtn1").focus();
        highlighFocusBtn('activeOk1'); 
        return false;
      }

    if($("#TDS_APPLICABLE").prop("checked") == true &&  $.trim($("#ASSESSEEID_REF").val()) ===''){
      $("#alert").modal('show');
      $("#AlertMessage").text('Please select assessee type in Statutory Tab.');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk1'); 
      return false;
    }
    else if($("#TDS_APPLICABLE").prop("checked") == true &&  $.trim($("#HOLDINGID_REF").val()) ===''){
      $("#alert").modal('show');
      $("#AlertMessage").text('Please select TDS Code in Statutory Tab.');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk1'); 
      return false;
    } 
    
    
                var allblank1 = [];  //address
                var allblank2 = [];   // country
                var allblank3 = [];  //state
                var allblank4 = [];  //country
                var allblank5 = [];  //pincode
                var allblank6 = [];  //pincode
                var allblank9 = [];  
                var allblank10 = [];  
                var allblank11 = [];  
                var allblank12 = [];  
                var allblank13 = [];  
                var allblank14 = [];  
                var allblank15 = [];  

                var billtofound = false;
                
                var defbillfound = false;
                var defbillcount = 0;
                
                var shiptofound = false;
                var defshipfound = false;
                var defshipcount = 0;

                var defbankfound = false;
                var defbankcount = 0;
                
                $("[id*=BANK_NAME]").each(function(){
                    
                    if( $.trim( $(this).val() ) !== "" &&  $.trim( $(this).parent().parent().find('[id*="BANK_IFSC"]').val() ) == "" )
                    {
                      allblank12.push('true');
                    }else
                    {
                      allblank12.push('false');
                    }

                    if( $.trim( $(this).val() ) !== "" &&  $.trim( $(this).parent().parent().find('[id*="BANK_BRANCH"]').val() ) == "" )
                    {
                      allblank13.push('true');
                    }else
                    {
                      allblank13.push('false');
                    }

                    if( $.trim( $(this).val() ) !== "" &&  $.trim( $(this).parent().parent().find('[id*="BANK_ACTYPE"]').val() ) == "" )
                    {
                      allblank14.push('true');
                    }else
                    {
                      allblank14.push('false');
                    }

                    if( $.trim( $(this).val() ) !== "" &&  $.trim( $(this).parent().parent().find('[id*="BANK_ACNO"]').val() ) == "" )
                    {
                      allblank15.push('true');
                    }else
                    {
                      allblank15.push('false');
                    }
                
                });

                $("[id*=BYDEFALUT]").each(function(){
                  if($(this).is(":checked")  == true )
                    {
                      defbankfound = true;
                      defbankcount = defbankcount + 1;
                    }
                });

                if( defbankfound==true && defbankcount>1){
                    $("#alert").modal('show');
                    $("#AlertMessage").text('Please Select Default Bank Single Time.');
                    $("#YesBtn").hide(); 
                    $("#NoBtn").hide();  
                    $("#OkBtn1").show();
                    $("#OkBtn1").focus();
                    highlighFocusBtn('activeOk1');
                  return false;
                }   


                $("[id*=LOC_LADD]").each(function(){
                    if( $.trim( $(this).val() ) == "" )
                    {
                        allblank1.push('true');
                    }else
                    {
                      allblank1.push('false');
                    }
                });

                $("[id*=TXTLOC_CTRYID_REF_POPUP]").each(function(){
                    if( $.trim( $(this).val() ) == "" )
                    {
                      allblank2.push('true');
                    }else
                    {
                      allblank2.push('false');
                    }
                });

                $("[id*=TXTLOC_STID_REF_POPUP]").each(function(){
                    if( $.trim( $(this).val() ) == "" )
                    {
                      allblank3.push('true');
                    }else
                    {
                      allblank3.push('false');
                    }
                });

                $("[id*=TXTLOC_CITYID_REF_POPUP]").each(function(){
                    if( $.trim( $(this).val() ) == "" )
                    {
                      allblank4.push('true');
                    }else
                    {
                      allblank4.push('false');
                    }
                });

                $("[id*=LOC_PIN]").each(function(){
                    if( $.trim( $(this).val() ) == "" )
                    {
                      allblank5.push('true');
                    }else
                    {
                      allblank5.push('false');
                    }
                });

                $("[id*=LOC_NAME]").each(function(){
                    if( $.trim( $(this).val() ) == "" )
                    {
                      allblank6.push('true');
                    }else
                    {
                      allblank6.push('false');
                    }
                });

                $("[id*=LOC_BILLTO]").each(function(){
                   if($(this).is(":checked")  == false &&   $(this).parent().parent().find('[id*="LOC_SHIPTO"]').is(":checked")== false)
                    {
                      allblank11.push('true');
                    }else
                    {
                      allblank11.push('false');
                    }
                });

                $("[id*=LOC_BILLTO]").each(function(){
                    if($(this).is(":checked")  == true )
                    {
                        billtofound = true;
                    }
                });

                
                $("[id*=LOC_DEFAULT_BILLTO]").each(function(){
                    if($(this).is(":checked")  == true )
                    {
                      defbillfound = true;
                      defbillcount = defbillcount + 1;
                    }
                });

                $("[id*=LOC_SHIPTO]").each(function(){
                  if($(this).is(":checked")  == true )
                    {
                        shiptofound = true;
                    }
                });
              
                $("[id*=LOC_DEFAULT_SHIPTO]").each(function(){
                  if($(this).is(":checked")  == true )
                    {
                      defshipfound = true;
                      defshipcount = defshipcount + 1;
                    }
                });

                $("[id*=txtudffie_popup]").each(function(){
                    if($.trim($(this).val())!="")
                    {
                        if($.trim($(this).parent().parent().find('[id*="udffieismandatory"]').val()) == "1")
                          {
                            if($.trim($(this).parent().parent().find('[id*="udfvalue"]').val()) != "")
                              {
                                allblank10.push('true');
                              }
                            else
                              {
                                allblank10.push('false');
                              }
                          }
                        
                    }
                    
                });

                  if(jQuery.inArray("true", allblank12) !== -1){
                      $("#alert").modal('show');
                      $("#AlertMessage").text('Please enter value for IFSC in Bank Tab.');
                      $("#YesBtn").hide(); 
                      $("#NoBtn").hide();  
                      $("#OkBtn1").show();
                      $("#OkBtn1").focus();
                      highlighFocusBtn('activeOk1');
                      return false;

                  }else if(jQuery.inArray("true", allblank13) !== -1){
                      $("#alert").modal('show');
                      $("#AlertMessage").text('Please enter value for Branch in Bank Tab.');
                      $("#YesBtn").hide(); 
                      $("#NoBtn").hide();  
                      $("#OkBtn1").show();
                      $("#OkBtn1").focus();
                      highlighFocusBtn('activeOk1');
                      return false;
                      
                  }else if(jQuery.inArray("true", allblank14) !== -1){
                      $("#alert").modal('show');
                      $("#AlertMessage").text('Please select Account Type in Bank Tab.');
                      $("#YesBtn").hide(); 
                      $("#NoBtn").hide();  
                      $("#OkBtn1").show();
                      $("#OkBtn1").focus();
                      highlighFocusBtn('activeOk1');
                      return false;
                  
                  }else if(jQuery.inArray("true", allblank15) !== -1){
                      $("#alert").modal('show');
                      $("#AlertMessage").text('Please enter value for Account No in Bank Tab.');
                      $("#YesBtn").hide(); 
                      $("#NoBtn").hide();  
                      $("#OkBtn1").show();
                      $("#OkBtn1").focus();
                      highlighFocusBtn('activeOk1');
                      return false;
                  
                  }else if(jQuery.inArray("true", allblank1) !== -1){
                      $("#alert").modal('show');
                      $("#AlertMessage").text('Please enter value for address in Location Tab.');
                      $("#YesBtn").hide(); 
                      $("#NoBtn").hide();  
                      $("#OkBtn1").show();
                      $("#OkBtn1").focus();
                      highlighFocusBtn('activeOk1');
                      return false;

                  }else if(jQuery.inArray("true", allblank6) !== -1){
                      $("#alert").modal('show');
                      $("#AlertMessage").text('Please enter value for Location Name in Location Tab.');
                      $("#YesBtn").hide(); 
                      $("#NoBtn").hide();  
                      $("#OkBtn1").show();
                      $("#OkBtn1").focus();
                      highlighFocusBtn('activeOk1');
                      return false;        

                  }else if(jQuery.inArray("true", allblank2) !== -1){
                      $("#alert").modal('show');
                      $("#AlertMessage").text('Please select Country in Location Tab.');
                      $("#YesBtn").hide(); 
                      $("#NoBtn").hide();  
                      $("#OkBtn1").show();
                      $("#OkBtn1").focus();
                      highlighFocusBtn('activeOk1');
                      return false;  

                  }else if(jQuery.inArray("true", allblank3) !== -1){
                      $("#alert").modal('show');
                      $("#AlertMessage").text('Please select State in Location Tab.');
                      $("#YesBtn").hide(); 
                      $("#NoBtn").hide();  
                      $("#OkBtn1").show();
                      $("#OkBtn1").focus();
                      highlighFocusBtn('activeOk1');
                      return false;   

                  }else if(jQuery.inArray("true", allblank4) !== -1){
                      $("#alert").modal('show');
                      $("#AlertMessage").text('Please select City in Location Tab.');
                      $("#YesBtn").hide(); 
                      $("#NoBtn").hide();  
                      $("#OkBtn1").show();
                      $("#OkBtn1").focus();
                      highlighFocusBtn('activeOk1');
                      return false;    
                  
                  }else if(jQuery.inArray("true", allblank11) !== -1){
                      $("#alert").modal('show');
                      $("#AlertMessage").text('Please select Bill To or Ship To for every row in Location Tab.');
                      $("#YesBtn").hide(); 
                      $("#NoBtn").hide();  
                      $("#OkBtn1").show();
                      $("#OkBtn1").focus();
                      highlighFocusBtn('activeOk1');
                      return false;

                  }else if( billtofound == false){
                      $("#alert").modal('show');
                      $("#AlertMessage").text('Please Select Bill To in Location Tab.');
                      $("#YesBtn").hide(); 
                      $("#NoBtn").hide();  
                      $("#OkBtn1").show();
                      $("#OkBtn1").focus();
                      highlighFocusBtn('activeOk1');
                      return false;

                  }else if(!defbillfound){
                      $("#alert").modal('show');
                      $("#AlertMessage").text('Please Select Default Bill To in Location Tab.');
                      $("#YesBtn").hide(); 
                      $("#NoBtn").hide();  
                      $("#OkBtn1").show();
                      $("#OkBtn1").focus();
                      highlighFocusBtn('activeOk1');
                      return false;

                  }else if(!shiptofound){
                      $("#alert").modal('show');
                      $("#AlertMessage").text('Please Select Ship To in Location Tab.');
                      $("#YesBtn").hide(); 
                      $("#NoBtn").hide();  
                      $("#OkBtn1").show();
                      $("#OkBtn1").focus();
                      highlighFocusBtn('activeOk1');
                      return false;

                  }else if(!defshipcount){
                      $("#alert").modal('show');
                      $("#AlertMessage").text('Please Select Default Ship To in Location Tab.');
                      $("#YesBtn").hide(); 
                      $("#NoBtn").hide();  
                      $("#OkBtn1").show();
                      $("#OkBtn1").focus();
                      highlighFocusBtn('activeOk1');
                      return false;

                  }else if(jQuery.inArray("false", allblank10) !== -1){
                        $("#alert").modal('show');
                        $("#AlertMessage").text('Please enter Value / Comments in UDF Tab.');
                        $("#YesBtn").hide(); 
                        $("#NoBtn").hide();  
                        $("#OkBtn1").show();
                        $("#OkBtn1").focus();
                        highlighFocusBtn('activeOk1');
                        return false;
                  }// blank if    


              if( billtofound==true && defbillcount>1){
                  $("#alert").modal('show');
                  $("#AlertMessage").text('Please Select Default Billing Single Time.');
                  $("#YesBtn").hide(); 
                  $("#NoBtn").hide();  
                  $("#OkBtn1").show();
                  $("#OkBtn1").focus();
                  highlighFocusBtn('activeOk1');
                return false;
              }      

              if( shiptofound==true && defshipcount>1){
                  $("#alert").modal('show');
                  $("#AlertMessage").text('Please Select Default Shipping Single Time.');
                  $("#YesBtn").hide(); 
                  $("#NoBtn").hide();  
                  $("#OkBtn1").show();
                  $("#OkBtn1").focus();
                  highlighFocusBtn('activeOk1');
                return false;
              }   


            
              $("#alert").modal('show');
              $("#AlertMessage").text('Do you want to save to record.');
              $("#YesBtn").data("funcname","fnSaveData");  //set dynamic fucntion name
              $("#OkBtn1").hide();
              $("#OkBtn").hide();
              $("#YesBtn").show();
              $("#NoBtn").show();
              $("#YesBtn").focus();
              highlighFocusBtn('activeYes');

            return false;

  }            
//----------------------------
});//btnSaveItem

  //bill to 
  $('#table3').on("change","[id*='LOC_BILLTO']",function(event){
    var  defbilid = $(this).parent().parent().find('[id*="LOC_DEFAULT_BILLTO"]').attr('id');

      if($(this).is(':checked')==false) {
        $('#'+defbilid+'').prop('checked',false);
      }
  });  

  //default bill to 
  $('#table3').on("change","[id*='LOC_DEFAULT_BILLTO']",function(event){

    var dbid = $(this).parent().parent().find('[id*="LOC_BILLTO"]').attr('id');
      if($(this).is(':checked')) {
        $('#'+dbid+'').prop('checked',true);
      }
      
  });  

    //ship to
    $('#table3').on("change","[id*='LOC_SHIPTO']",function(event){
      var  defshipid = $(this).parent().parent().find('[id*="LOC_DEFAULT_SHIPTO"]').attr('id');
        if($(this).is(':checked')==false) {
          $('#'+defshipid+'').prop('checked',false);
        } 

    });  

    //default ship to 
    $('#table3').on("change","[id*='LOC_DEFAULT_SHIPTO']",function(event){

     var dsid = $(this).parent().parent().find('[id*="LOC_SHIPTO"]').attr('id');

      if($(this).is(':checked')) {
        $('#'+dsid+'').prop('checked',true);
      }
    }); 
    
  $("#YesBtn").click(function(){

        $("#alert").modal('hide');
        var customFnName = $("#YesBtn").data("funcname");
            window[customFnName]();

  }); //yes button
   
  $("#OkBtn1").click(function(){

        $("#alert").modal('hide');
  }); //yes button


  window.fnSaveData = function (){

        //validate and save data
        event.preventDefault();

        var currentForm = $("#add_form_data");
        var formData = currentForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("master",[48,"save"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    console.log("error MSG="+data.msg);

                    if(data.resp=='duplicate') {

                      $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn").hide();
                      $("#OkBtn1").show();
                      $("#AlertMessage").text(data.msg);
                      $("#alert").modal('show');
                      $("#OkBtn1").focus();
                      highlighFocusBtn('activeOk1');
                      return false;

                    }

                   if(data.save=='invalid') {

                      $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn").hide();
                      $("#OkBtn1").show();
                      $("#AlertMessage").text(data.msg);
                      $("#alert").modal('show');
                      $("#OkBtn1").focus();
                      return false;
                   }

                   if(data.form=='invalid') {

                      $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn").hide();
                      $("#OkBtn1").show();
                      $("#AlertMessage").text("Invalid form data please enter required fields.");
                      $("#alert").modal('show');
                      $("#OkBtn1").focus();
                      return false;
                   }
                   
                }
                
                if(data.success) {                   

                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn1").hide();
                    $("#OkBtn").show();  // ok button for reload the page
                    highlighFocusBtn('activeOk1');
                    
                    $("#AlertMessage").text(data.msg);
                    $("#alert").modal('show');

                    $("#OkBtn").focus();
                    $(".text-danger").hide();
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
         //reload form
         window.location.href = "{{route('master',[48,'add'])}}";
        
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

    $("#OkBtn").click(function(){
      $("#alert").modal('hide');

    });////ok button


   window.fnUndoYes = function (){
      
      //reload form
      window.location.href = "{{route('master',[48,'add'])}}";

   }//fnUndoYes


   window.fnUndoNo = function (){
      //$("#CTRYCODE").focus();
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

//GSTIN Check//

$('#location').on('focusout','[id*="LOC_GSTIN"]',function(){
    var GSTNo = $(this).val();
    if ($(this).val().length != 15) {
      $("#FocusId").val($(this));
      $(this).val('');  
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Invalid GSTIN.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
    }
    var gstinformat = new RegExp('^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$');

    if (gstinformat.test(GSTNo)) 
    {
      return true;
    } 
    else 
    {
      $("#FocusId").val($(this));
      $(this).val('');   
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Invalid GSTIN. It should be in this "11AAAAA1111Z1A1" format.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
    }

});

$('#GSTIN').on('focusout',function(){
    var GSTNo = $(this).val();
    if ($(this).val().length != 15) {
      $("#FocusId").val($('#GSTIN'));
      $('#GSTIN').val('');  
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Invalid GSTIN ');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
    }
    var gstinformat = new RegExp('^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$');

    if (gstinformat.test(GSTNo)) 
    {
      return true;
    } 
    else 
    {
      $("#FocusId").val($('#GSTIN'));
      $('#GSTIN').val('');  
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Invalid GSTIN. It should be in this "11AAAAA1111Z1A1" format.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
    }

});

  //GSTIN Check//

  //PAN No Check//

$('#PANNO').on('focusout',function(){
    var PANNO = $(this).val();
    if ($(this).val().length != 10) {
      $("#FocusId").val($('#PANNO'));
      $('#PANNO').val('');  
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Invalid PAN number');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
    }
    var PANinformat = new RegExp('^[A-Za-z]{5}[0-9]{4}[A-Za-z]{1}$');

    if (PANinformat.test(PANNO)) 
    {
      return true;
    } 
    else 
    {
      $("#FocusId").val($('#PANNO'));
      $('#PANNO').val('');  
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Invalid PAN number.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
    }

});

  //PAN No Check//

  //EMAIL Check//

$('#location').on('focusout','[id*="LOC_EMAIL"]',function(){
    var Email = $(this).val();
    
    var emailformat = new RegExp(/^[+a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i);

    if (emailformat.test(Email)) 
    {
      return true;
    } 
    else 
    {
      $("#FocusId").val($(this));
      $(this).val('');   
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Invalid EMAIL.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
    }

});

$('#poc').on('focusout','[id*="POC_EMAIL"]',function(){
    var Email = $(this).val();
    
    var emailformat = new RegExp(/^[+a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i);

    if (emailformat.test(Email)) 
    {
      return true;
    } 
    else 
    {
      $("#FocusId").val($(this));
      $(this).val('');   
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Invalid EMAIL.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
    }

});

$('#EMAILID').on('focusout',function(){
    var GSTNo = $(this).val();
    
    var emailformat = new RegExp(/^[+a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i);

    if (emailformat.test(GSTNo)) 
    {
      return true;
    } 
    else 
    {
      $("#FocusId").val($(this));
      $(this).val('');  
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Invalid EMAIL.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
    }

});

  //EMAIL Check//


  //Registered Address Line 1
  $("#REGADDL1").on('blur',function(){
    
     if($.trim($("#LOC_LADD_0").val())==''){
        $("#LOC_LADD_0").val($(this).val());
      }
  });  

  $(function() { 
    //ready
    $("#vCODE").focus(); 

    $("#Row_Count1").val(1);
    $("#Row_Count2").val(1);
    $("#Row_Count3").val(1);
    //$("#Row_Count4").val(1);

    $("[id*='TXT_TO_QTY']").ForceNumericOnly();

   $("#PARTNO").ForceNumericOnly();
    $("#DRAWINGNO").ForceNumericOnly();
    $("#STDCOST").ForceNumericOnly();
    $("#SCDRate").ForceNumericOnly();
    $("#SSRate").ForceNumericOnly();
    $("#MINLEVEL").ForceNumericOnly();
    $("#REORDERLEVEL").ForceNumericOnly();
    $("#MAXLEVEL").ForceNumericOnly();
    $("#CREDITLIMIT").ForceNumericOnly();
    $("#CREDITDAY").ForceNumericOnly();

     
   
    


     //--table1 end

      // $("#MAIN_UOMID_REF").on('change',function(){
      //   var muoval =  $(this).val();
      //   if(muoval==''){
      //     $("[id*='txt_from_uom']").val('');
      //   }else{
      //     $("[id*='txt_from_uom']").val($("#MAIN_UOMID_REF option:selected").text());
      //   }
      //   $("[id*='hdntxt_from_uomid']").val( $(this).val());
      
      // });

      // $("#MAIN_UOMID_REF").on('change',function(){
      //   var muoval =  $(this).val();
      //   if(muoval==''){
      //     $("[id*='txt_from_uom']").val('');
      //   }else{
      //     $("[id*='txt_from_uom']").val($("#MAIN_UOMID_REF option:selected").text());
      //   }
      //   $("[id*='hdntxt_from_uomid']").val( $(this).val());
        
      
      // });





    $("#udffietable").on('click', '.add', function() {
    
        var $tr = $(this).closest('table');
        var allTrs = $tr.find('tbody').last();
        var lastTr = allTrs[allTrs.length-1];
        var $clone = $(lastTr).clone();
        //dynamic <td> id for dynamic input
        $clone.find('td').each(function(){
            var id = $(this).attr('id') || null;
            if(id) {
                var i = id.substr(id.length-1);
                var prefix = id.substr(0, (id.length-1));
                $(this).attr('id', prefix+(+i+1));
            }

        });  
        $clone.find("[id*='tdinputid']").html('');  //clear dynamic

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
      
        $clone.find("[id*='txtudffie_popup']").val('');
        $clone.find("[id*='hdnudffie_popup']").val('');
        $clone.find("[id*='udffieismandatory']").val('0');

        $clone.find('.remove').removeAttr('disabled'); 
        $tr.closest('table').append($clone);   
        var rowCount = $('#Row_Count4').val();
          rowCount = parseInt(rowCount)+1;
          $('#Row_Count4').val(rowCount);
          event.preventDefault();
    }); //add row

      $("#udffietable").on('click', '.remove', function() {
        var rowCount = $(this).closest('table').find('tbody').length;
          if (rowCount > 1) {
            $(this).closest('tbody').remove();
          }
          if (rowCount <= 1) {
            $(document).find('.remove').prop('disabled', false);
          }
      });//remove row
      //--udffield end   /



      

    //---------------------------  
  }); //ready


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
      url:'{{route("master",[48,"TdsCodeList"])}}',
      type:'POST',
      data:{ASSESSEEID_REF:ASSESSEEID_REF,VALUE:''},
      success:function(data) {
        $("#HOLDINGID_REF_body").html(data);
        bindTds();
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

window.onload = function(){
      var strdd = <?php echo json_encode($objDD); ?>;
      if($.trim(strdd)==""){     
        $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn1").show();
          $("#OkBtn").hide();
          $("#AlertMessage").text('Please contact to administrator for creating document numbering.');
          $("#alert").modal('show');
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk1');
      } 
    };

</script>

@endpush