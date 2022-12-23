@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[141,'index'])}}" class="btn singlebt">Bank / Cash Master</a>
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
                        <a href="#" class="btn topnavbt"  disabled="disabled"><i class="fa fa-link" ></i> Attachment</a>
                        <button class="btn topnavbt" id="btnExit"><i class="fa fa-power-off"></i> Exit</button>
                </div><!--col-10-->

            </div><!--row-->
    </div><!--topnav-->	
   
    <div class="container-fluid purchase-order-view filter">     
         <form id="frm_mst_edit" method="POST"  > 
          @CSRF
          {{isset($objResponse->BID) ? method_field('PUT') : '' }}
          <div class="inner-form">
        

                <div class="row">
                    <div class="col-lg-2 pl"><p>Bank Code</p></div>
                    <div class="col-lg-2 pl">
                    <label> {{$objResponse->BCODE}} </label>
                    <input type="hidden" name="BID" id="BID" value="{{ $objResponse->BID }}" />
                    <input type="hidden" name="BCODE" id="BCODE" value="{{ $objResponse->BCODE }}" autocomplete="off"  maxlength="20"   />
                    <input type="hidden" name="user_approval_level" id="user_approval_level" value="{{ $user_approval_level }}"  />
                    </div>
                    
                    <div class="col-lg-2 pl col-md-offset-2"><p>Name</p></div>
                    <div class="col-lg-4 pl">
                    <input type="text" name="NAME" id="NAME" class="form-control mandatory" value="{{ old('NAME',$objResponse->NAME) }}" maxlength="100" tabindex="2"  autocomplete="off" />
                    <span class="text-danger" id="ERROR_NAME"></span>  
                    </div>
                  </div>
                  
                  <div class="row">
                    <div class="col-lg-2 pl"><p>Branch</p></div>
                    <div class="col-lg-3 pl">
                      <input type="text" name="BRANCH" id="BRANCH" class="form-control mandatory" value="{{ old('BRANCH',$objResponse->BRANCH) }}"  maxlength="100" tabindex="3" >
                      <span class="text-danger" id="ERROR_BRANCH"></span> 
                    </div>
                    
                    <div class="col-lg-2 pl col-md-offset-1"><p>IFSC</p></div>
                    <div class="col-lg-2 pl">
                      <input type="text" name="IFSC" id="IFSC" class="form-control mandatory" value="{{ old('IFSC',$objResponse->IFSC) }}"  maxlength="30" tabindex="4" >
                      <span class="text-danger" id="ERROR_IFSC"></span> 
                    </div>
                  </div>
                  
                  <div class="row">
                    <div class="col-lg-2 pl"><p>Type (Bank/Cash)</p></div>
                    <div class="col-lg-2 pl">
                      <div class="col-lg-12 pl">
                        <select name="BANK_CASH" id="BANK_CASH" class="form-control mandatory" >
                          <option {{isset($objResponse->BANK_CASH) && strtoupper($objResponse->BANK_CASH) =="B"?'selected="selected"':''}} value='B'>BANK</option>
                          <option {{isset($objResponse->BANK_CASH) && strtoupper($objResponse->BANK_CASH) =="C"?'selected="selected"':''}}  value='C'>CASH</option>
                        </select>
                        <span class="text-danger" id="ERROR_BANK_CASH"></span> 
                      </div>
                    </div>                   
                    
                  </div>

                  <div class="row">
                    <div class="col-lg-2 pl"><p>Account Type</p></div>
                    <div class="col-lg-2 pl">
                      <div class="col-lg-12 pl">

                        <select name="ACTYPE" id="ACTYPE" class="form-control mandatory" tabindex="5" >
                          <option value="" selected >Select</option>
                          <option {{isset($objResponse->ACTYPE) && $objResponse->ACTYPE =="SAVING ACCOUNT"?'selected="selected"':''}}  value='SAVING ACCOUNT'>SAVING ACCOUNT</option>
                          <option {{isset($objResponse->ACTYPE) && $objResponse->ACTYPE =="CURRENT ACCOUNT"?'selected="selected"':''}} value='CURRENT ACCOUNT'>CURRENT ACCOUNT</option>
                          <option {{isset($objResponse->ACTYPE) && $objResponse->ACTYPE =="OD"?'selected="selected"':''}} value='OD'>OD</option>
                          <option {{isset($objResponse->ACTYPE) && $objResponse->ACTYPE =="OTHERS"?'selected="selected"':''}} value='OTHERS'>OTHERS</option>
                          <option {{isset($objResponse->ACTYPE) && $objResponse->ACTYPE =="FD"?'selected="selected"':''}} value='FD'>FD</option>
                        </select>
                        <span class="text-danger" id="ERROR_ACTYPE"></span> 
                      </div>
                    </div>
                    
                    <div class="col-lg-2 pl col-md-offset-2"><p>Account No</p></div>
                    <div class="col-lg-2 pl">
                      <div class="col-lg-12 pl">
                      <input type="text" name="ACNO" id="ACNO" class="form-control mandatory" value="{{ old('ACNO',$objResponse->ACNO) }}"  maxlength="30" tabindex="6" >
                      <span class="text-danger" id="ERROR_ACNO"></span> 
                      </div>
                    </div>
                  </div>
                  
                  <div class="row">
                    <div class="col-lg-2 pl"><p>OD Limit</p></div>
                    <div class="col-lg-1 pl">
                      <div class="col-lg-12 pl">
                      <input type="text" name="ODLIMIT" id="ODLIMIT" class="form-control " value="{{ old('ODLIMIT',$objResponse->ODLIMIT) }}"  maxlength="9" tabindex="7"  >
                      <span class="text-danger" id="ERROR_ODLIMIT"></span> 
                      </div>
                    </div>
                    
                    <div class="col-lg-2 pl col-md-offset-3"><p>Branch Timing</p></div>
                    <div class="col-lg-2 pl">
                      @php
                        $starr = explode(".",$objResponse->BRANCHTIME);
                      @endphp
                      <input type="time" name="BRANCHTIME" id="BRANCHTIME" class="form-control " value="{{isset($starr[0]) ? $starr[0] : '' }}"  maxlength="50" tabindex="8"  >
                      <span class="text-danger" id="ERROR_BRANCHTIME"></span> 
                    </div>
                  </div>
                  
                  
                  <div class="row">
                    <div class="col-lg-2 pl"><p>Address 1</p></div>
                    <div class="col-lg-4 pl">
                      <input type="text" name="ADD1" id="ADD1" class="form-control " value="{{ old('ADD1',$objResponse->ADD1) }}"  maxlength="200" tabindex="9" >
                      <span class="text-danger" id="ERROR_ADD1"></span> 
                    </div>
                    
                    <div class="col-lg-2 pl "><p>Address 2</p></div>
                    <div class="col-lg-4 pl">
                      <input type="text" name="ADD2" id="ADD2" class="form-control " value="{{ old('ADD2',$objResponse->ADD2) }}"  maxlength="200" tabindex="10"  >
                      <span class="text-danger" id="ERROR_ADD2"></span> 
                    </div>
                  </div>
                  
                  <div class="row">
                  <div class="col-lg-2 pl"><p>Country</p></div>
                    <div class="col-lg-2 pl">
                      <input type="text" name="CTRYID_REF_POPUP" id="CTRYID_REF_POPUP" class="form-control mandatory" readonly tabindex="11" value="{{isset($objCountryName->CTRYCODE)?$objCountryName->CTRYCODE. ' - ':''}}  {{isset($objCountryName->NAME)?$objCountryName->NAME:''}}" />
                      <input type="hidden" name="CTRYID_REF" id="CTRYID_REF" value="{{ old('CTRYID_REF',$objResponse->CTRYID_REF) }}" />
                      <span class="text-danger" id="ERROR_CTRYID_REF"></span>
                    
                    </div>

                    <div class="col-lg-2 pl col-md-offset-2"><p>State</p></div>
                    <div class="col-lg-2 pl">
                      <input type="text" name="STID_REF_POPUP" id="STID_REF_POPUP" class="form-control mandatory"  readonly tabindex="12" value="{{isset($objStateName->STCODE)?$objStateName->STCODE. ' - ':''}}  {{isset($objStateName->NAME)?$objStateName->NAME:''}}" />
                      <input type="hidden" name="STID_REF" id="STID_REF" value="{{ old('STID_REF',$objResponse->STID_REF) }}" />
                      <span class="text-danger" id="ERROR_STID_REF"></span>
                    </div>

                  </div>
                  
                  <div class="row">
                    
                  <div class="col-lg-2 pl"><p>City</p></div>
                    <div class="col-lg-2 pl">
                      <div class="col-lg-11 pl">
                      <input type="text" name="CITYID_REF_POPUP" id="CITYID_REF_POPUP" class="form-control mandatory" readonly tabindex="13" value="{{isset($objCityName->CITYCODE)?$objCityName->CITYCODE. ' - ':''}}  {{isset($objCityName->NAME)?$objCityName->NAME:''}}" />
                      <input type="hidden" name="CITYID_REF" id="CITYID_REF" value="{{ old('CITYID_REF',$objResponse->CITYID_REF) }}" />
                      <span class="text-danger" id="ERROR_CITYID_REF"></span>
                      </div>
                    </div>
                    
                    <div class="col-lg-2 pl col-md-offset-2"><p>PinCode</p></div>
                    <div class="col-lg-2 pl">
                      <div class="col-lg-11 pl">
                      <input type="text" name="PIN" id="PIN" class="form-control" value="{{ old('PIN',$objResponse->PIN) }}"  maxlength="20" tabindex="14"  >
                      <span class="text-danger" id="ERROR_PIN"></span> 
                      </div>
                    </div>
                    
                    
                  </div>

                    
                  
                  <div class="row">
                  
                    <div class="col-lg-2 pl "><p>Landmark</p></div>
                    <div class="col-lg-4 pl">
                      <input type="text" name="LANDMARK" id="LANDMARK" class="form-control" value="{{ old('LANDMARK',$objResponse->LANDMARK) }}"  maxlength="200" tabindex="15"   >
                      <span class="text-danger" id="ERROR_LANDMARK"></span> 
                    </div>
                    
                    <div class="col-lg-2 pl"><p>Date of Opening</p></div>
                    <div class="col-lg-2 pl">
                    <input type="date" name="DOO" class="form-control" id="DOO"  value="{{isset($objResponse->DOO) && $objResponse->DOO !="" && $objResponse->DOO !="1900-01-01" ? $objResponse->DOO:''}}" tabindex="16" placeholder="dd/mm/yyyy"  />
                    <span class="text-danger" id="ERROR_DOO"></span> 
                   
                    </div>
                  </div>
                  
                  <div class="row">
                    <div class="col-lg-2 pl"><p>Signing Authority</p></div>
                    <div class="col-lg-4 pl">
                      <input type="text" name="SIGNAUTH" id="SIGNAUTH" class="form-control" value="{{ old('SIGNAUTH',$objResponse->SIGNAUTH) }}"  maxlength="100"  tabindex="17" >
                      <span class="text-danger" id="ERROR_SIGNAUTH"></span> 
                    </div>

                    <div class="col-lg-2 pl"><p>GL</p></div>
                    <div class="col-lg-2 pl">
                      <input type="text" name="GLID_REF_POPUP" id="GLID_REF_POPUP" class="form-control mandatory" required readonly tabindex="18" value="{{isset($objGlName->GLCODE) && $objGlName->GLCODE !=''?$objGlName->GLCODE:''}} {{isset($objGlName->GLNAME) && $objGlName->GLNAME !=''?' - '.$objGlName->GLNAME:''}}" />
                      <input type="hidden" name="GLID_REF" id="GLID_REF" value="{{ old('GLID_REF',$objResponse->GLID_REF) }}" />
                      <span class="text-danger" id="ERROR_GLID_REF"></span> 
                    </div>
                    
                    
                    
                  </div>
				  <div class="row">
                    <div class="col-lg-2 pl"><p>Bank Charges GL</p></div>
                    <div class="col-lg-2 pl">
                        <input type="text" name="BANK_GLID_REF_POPUP" id="BANK_GLID_REF_POPUP" class="form-control mandatory" required readonly tabindex="18" value="{{isset($objBankGlName->GLCODE) && $objBankGlName->GLCODE !=''?$objBankGlName->GLCODE:''}} {{isset($objBankGlName->GLNAME) && $objBankGlName->GLNAME !=''?' - '.$objBankGlName->GLNAME:''}}" />
                      <input type="hidden" name="BANK_GLID_REF" id="BANK_GLID_REF" value="{{ old('BANK_GLID_REF',$objResponse->BANK_GLID_REF) }}" />
                      <span class="text-danger" id="BANK_ERROR_GLID_REF"></span>  
                    </div>
                  </div>
          
          
             
              

              <div class="row">
                <div class="col-lg-2 pl"><p>De-Activated</p></div>
                <div class="col-lg-1 pl pr">
                <input type="checkbox"   name="DEACTIVATED"  id="deactive-checkbox_0" {{$objResponse->DEACTIVATED == 1 ? "checked" : ""}}
                 value='{{$objResponse->DEACTIVATED == 1 ? 1 : 0}}' tabindex="2"  >
                </div>
                
                <div class="col-lg-2 pl"><p>Date of De-Activated</p></div>
                <div class="col-lg-2 pl">
                  <input type="date" name="DODEACTIVATED" class="form-control" id="DODEACTIVATED" {{$objResponse->DEACTIVATED == 1 ? "" : "disabled"}} value="{{isset($objResponse->DODEACTIVATED) && $objResponse->DODEACTIVATED !="" && $objResponse->DODEACTIVATED !="1900-01-01" ? $objResponse->DODEACTIVATED:''}}" tabindex="3" placeholder="dd/mm/yyyy"  />
                </div>
             </div>

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
                <div id="alert-active" class="activeOk1"></div>OK</button>
        </div><!--btdiv-->
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Alert -->

<!-- GL Alert -->
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
          <td  class="ROW2"  style="width: 40%"><input type="text" class="form-control" autocomplete="off" id="gl_codesearch" onkeyup="searchGLCode()"></td>
          <td  class="ROW3"  style="width: 40%"><input type="text" class="form-control" autocomplete="off" id="gl_namesearch" onkeyup="searchGLName()"></td>
        </tr>
        </tbody>
      </table>
      
      <table id="gl_tab2" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody>
        @foreach ($objGlList as $index=>$GlList)
        <tr >
          <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_GLID_REF[]" id="glref_{{ $GlList->GLID }}" class="clsglref" value="{{ $GlList->GLID}}" ></td>
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
            <td  class="ROW2"  style="width: 40%"><input type="text" id="country_codesearch" autocomplete="off"  class="form-control" onkeyup="searchCountryCode()" ></td>
            <td  class="ROW3"  style="width: 40%"><input type="text" id="country_namesearch"   class="form-control" onkeyup="searchCountryName()" autocomplete="off"></td>
          </tr>
        </tbody>
      </table>


      <table id="country_tab2" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody id="country_body">
        @foreach ($objCountryList as $index=>$CountryList)
        <tr >
          <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_CTRYID_REF[]"  id="ctryidref_{{$index}}" class="cls_ctryidref" value="{{ $CountryList->CTRYID }}" ></td>
          <td  class="ROW2" style="width: 39%">{{ $CountryList->CTRYCODE }}
          <input type="hidden" id="txtctryidref_{{$index}}" data-desc="{{ $CountryList->CTRYCODE }}- {{ $CountryList->NAME }}" data-descname="{{ $CountryList->NAME }}" value="{{ $CountryList-> CTRYID }}"/>
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
            <td class="ROW1" style="width: 12%" align="center"><span class="check_th">&#10004;</span></td>
            <td  class="ROW2" style="width: 39%"><input type="text" class="form-control" id="state_codesearch" onkeyup="searchStateCode()" /></td>
            <td  class="ROW3"  style="width: 39%"><input type="text" class="form-control" id="state_namesearch" onkeyup="searchStateName()" /></td>
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
            <td  class="ROW2" style="width: 40%" ><input type="text" class="form-control" autocomplete="off" id="city_codesearch" onkeyup="searchCityCode()"></td>
            <td  class="ROW3" style="width: 40%"><input type="text" class="form-control" autocomplete="off" id="city_namesearch" onkeyup="searchCityName()"></td>
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

<!-- BANK CHARGES GL Alert -->
<div id="chargesglrefpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='chargesglrefpopup_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>BANK CHARGES GL</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="chargesgl_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
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
          <td  class="ROW2"  style="width: 40%"><input type="text" class="form-control" autocomplete="off" id="bankgl_codesearch" onkeyup="banksearchGLCode()"></td>
          <td  class="ROW3"  style="width: 40%"><input type="text" class="form-control" autocomplete="off" id="bankgl_namesearch" onkeyup="banksearchGLName()"></td>
        </tr>
        </tbody>
      </table>
      
      <table id="chargesgl_tab2" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody>
        @foreach ($objGlList as $index=>$GlList)

        <tr >
          <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_GLID_REF[]" id="glref_{{ $GlList->GLID }}" class="bankclsglref" value="{{ $GlList->GLID}}" ></td>
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


@endsection
<!-- btnSave -->

@push('bottom-scripts')
<script>

// Country popup function

$("#CTRYID_REF_POPUP").on("click",function(event){ 
  $("#ctryidref_popup").show();
});

$("#CTRYID_REF_POPUP").keyup(function(event){
  if(event.keyCode==13){
    $("#ctryidref_popup").show();
  }
});

$("#ctryidref_close").on("click",function(event){ 
  $("#ctryidref_popup").hide();
});

$('.cls_ctryidref').click(function(){
  var id = $(this).attr('id');
  var txtval =    $("#txt"+id+"").val();
  var texdesc =   $("#txt"+id+"").data("desc")

  $("#CTRYID_REF_POPUP").val(texdesc);
  $("#CTRYID_REF").val(txtval);

  getCountryWiseState(txtval,'');
  
  $("#CTRYID_REF_POPUP").blur(); 
  $("#STID_REF_POPUP").focus(); 
  
  $("#ctryidref_popup").hide();
  searchCountryCode();
  event.preventDefault();
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

function getCountryWiseState(CTRYID_REF,dataStatus){
    $("#state_body").html('');
		$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
		
    $.ajax({
        url:'{{route("master",[141,"getCountryWiseState"])}}',
        type:'POST',
        data:{CTRYID_REF:CTRYID_REF},
        success:function(data) {
        if(dataStatus !="edit"){
          $("#STID_REF_POPUP").val('');
          $("#STID_REF").val('');
			    $("#CITYID_REF_POPUP").val('');
          $("#CITYID_REF").val('');
          $("#city_body").html(''); 
        }
          
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
  var CTRYID_REF  = $("#CTRYID_REF").val();
  getCountryWiseState(CTRYID_REF,'edit');
  $("#stidref_popup").show();
});

$("#STID_REF_POPUP").keyup(function(event){
  if(event.keyCode==13){
    var CTRYID_REF  = $("#CTRYID_REF").val();
    getCountryWiseState(CTRYID_REF,'edit');
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

    $("#STID_REF_POPUP").val(texdesc);
    $("#STID_REF").val(txtval);
	
	var CTRYID_REF	=	$("#CTRYID_REF").val();
	
	getStateWiseCity(CTRYID_REF,txtval,'');
	
	$("#STID_REF_POPUP").blur(); 
	$("#CITYID_REF_POPUP").focus(); 
	
    $("#stidref_popup").hide();
    searchStateCode();
    event.preventDefault();
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

function getStateWiseCity(CTRYID_REF,STID_REF,dataStatus){
    $("#city_body").html('');
		$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
		
    $.ajax({
        url:'{{route("master",[141,"getStateWiseCity"])}}',
        type:'POST',
        data:{CTRYID_REF:CTRYID_REF,STID_REF:STID_REF},
        success:function(data) {
          if(dataStatus !="edit"){
            $("#CITYID_REF_POPUP").val('');
            $("#CITYID_REF").val('');
          }
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

$("#CITYID_REF_POPUP").on("click",function(event){
  var CTRYID_REF  = $("#CTRYID_REF").val();
  var STID_REF  = $("#STID_REF").val();
  getStateWiseCity(CTRYID_REF,STID_REF,'edit'); 
  $("#cityidref_popup").show();
});

$("#CITYID_REF_POPUP").keyup(function(event){
  if(event.keyCode==13){
    var CTRYID_REF  = $("#CTRYID_REF").val();
    var STID_REF  = $("#STID_REF").val();
    getStateWiseCity(CTRYID_REF,STID_REF,'edit'); 
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

		$("#CITYID_REF_POPUP").val(texdesc);
    $("#CITYID_REF").val(txtval);
	
    $("#CITYID_REF_POPUP").blur(); 
	  $("#PIN").focus(); 

		$("#cityidref_popup").hide();
		
		searchCityCode();
		event.preventDefault();
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


/* BANK GL popup function */
$("#BANK_GLID_REF_POPUP").on("click",function(event){ 
  $("#chargesglrefpopup").show();
});

$("#BANK_GLID_REF_POPUP").keyup(function(event){
  if(event.keyCode==13){
    $("#chargesglrefpopup").show();
  }
});

$("#chargesglrefpopup_close").on("click",function(event){ 
  $("#chargesglrefpopup").hide();
});

$('.bankclsglref').click(function(){
    var id = $(this).attr('id');
    var txtval =    $("#txt"+id+"").val();
    var texdesc =   $("#txt"+id+"").data("desc")

    $("#BANK_GLID_REF_POPUP").val(texdesc);
    $("#BANK_GLID_REF").val(txtval);
    $("#BANK_GLID_REF_POPUP").blur(); 
    $("#REG_ADD1").focus(); 
    $("#chargesglrefpopup").hide();

    $("#bankgl_codesearch").val(''); 
    $("#bankgl_namesearch").val(''); 
    banksearchGLCode();
    event.preventDefault();

});

function banksearchGLCode() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("bankgl_codesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("chargesgl_tab2");
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

function banksearchGLName() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("bankgl_namesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("chargesgl_tab2");
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


/* GL popup function */
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
    $("#REG_ADD1").focus(); 
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
      var viewURL = '{{route("master",[141,"add"])}}';
      window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });

 var formDataMst = $( "#frm_mst_edit" );
     formDataMst.validate();

    $("#NAME").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_NAME").hide();
        validateSingleElemnet("NAME");

    });
    $("#NAME").keydown(function(){       
        $("#ERROR_NAME").hide();
    });

    $( "#NAME" ).rules( "add", {
        //required: true,
        //StringRegex: true,  //from custom.js
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field"
        }
    });
    $("#NAME").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_NAME").hide();
        validateSingleElemnet("NAME");
    });

    $( "#NAME" ).rules( "add", {
        //required: true,
        //StringRegex: true,  //from custom.js
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field."
        }
    });

    $("#BRANCH").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_BRANCH").hide();
        validateSingleElemnet("BRANCH");
    });

    $( "#BRANCH" ).rules( "add", {
        //required: true,
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field."
        }
    });

    $("#BANK_CASH").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_BANK_CASH").hide();
        validateSingleElemnet("BANK_CASH");
    });

    $( "#BANK_CASH" ).rules( "add", {
        //required: true,
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field."
        }
    });

    $("#IFSC").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_IFSC").hide();
        validateSingleElemnet("IFSC");
    });

    $( "#IFSC" ).rules( "add", {
       // required: true,
       nowhitespace: true,
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field."
        }
    });

    $("#ACTYPE").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_ACTYPE").hide();
        validateSingleElemnet("ACTYPE");
    });

    $( "#ACTYPE" ).rules( "add", {
        //required: true,
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field."
        }
    });

    $("#ACNO").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_ACNO").hide();
        validateSingleElemnet("ACNO");
    });

    $( "#ACNO" ).rules( "add", {
        //required: true,
        nowhitespace: true,
        OnlyNumberDec:true,
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field."
        }
    });

    $("#ODLIMIT").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_ODLIMIT").hide();
        validateSingleElemnet("ODLIMIT");
    });

    $( "#ODLIMIT" ).rules( "add", {
        //required: true,
        nowhitespace: true,
        OnlyNumberDec:true,
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field."
        }
    });

    $("#CTRYID_REF_POPUP").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_CTRYID_REF").hide();
        validateSingleElemnet("CTRYID_REF_POPUP");
    });

    $( "#CTRYID_REF_POPUP" ).rules( "add", {
        //required: true,
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field."
        }
    });

    $("#STID_REF_POPUP").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_STID_REF").hide();
        validateSingleElemnet("STID_REF_POPUP");
    });

    $( "#STID_REF_POPUP" ).rules( "add", {
       // required: true,
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field."
        }
    });

    $("#CITYID_REF_POPUP").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_CITYID_REF").hide();
        validateSingleElemnet("CITYID_REF_POPUP");
    });

    $( "#CITYID_REF_POPUP" ).rules( "add", {
       // required: true,
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field."
        }
    });

    $("#PIN").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_PIN").hide();
        validateSingleElemnet("PIN");
    });

    $( "#PIN" ).rules( "add", {
        OnlyNumberDec:true,
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field."
        }
    });
    

    $("#DODEACTIVATED").blur(function(){
      $(this).val($.trim( $(this).val() ));
      $("#ERROR_DODEACTIVATED").hide();
      validateSingleElemnet("DODEACTIVATED");
    });

    $( "#DODEACTIVATED" ).rules( "add", {
        required: true,
        DateValidate:true,
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field"
        }
    });

    $("#BANK_CASH").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_BANK_CASH").hide();
        validateSingleElemnet("BANK_CASH");
    });

    $( "#BANK_CASH" ).rules( "add", {
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
      var validator =$("#frm_mst_edit" ).validate();
          validator.element( "#"+element_id+"" );
    }



    //validate
    $( "#btnSave" ).click(function() {

        if(formDataMst.valid()){
            //set function nane of yes and no btn 
            var BCODE          =   $.trim($("#BCODE").val());
            if(BCODE ===""){
              $("#ProceedBtn").focus();
              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn").hide();  
              $("#OkBtn1").show();              
              $("#AlertMessage").text('Please enter Bank Code.');
              $("#alert").modal('show');
              $("#OkBtn1").focus();
              return false;
            }

            var BANK_CASH = $.trim($("#BANK_CASH").val());
            var NAME = $.trim($("#NAME").val());
            var BRANCH = $.trim($("#BRANCH").val());
            var IFSC = $.trim($("#IFSC").val());
            var ACTYPE = $.trim($("#ACTYPE").val());
            var ACNO = $.trim($("#ACNO").val());
            var CTRYID_REF_POPUP = $.trim($("#CTRYID_REF_POPUP").val());
            var STID_REF_POPUP = $.trim($("#STID_REF_POPUP").val());
            var CITYID_REF_POPUP = $.trim($("#CITYID_REF_POPUP").val());
            var GLID_REF_POPUP = $.trim($("#GLID_REF_POPUP").val());
			var BANK_GLID_REF_POPUP = $.trim($("#BANK_GLID_REF_POPUP").val());
            
            if(BANK_CASH==="B"){
                if(NAME ===""){
                  $("#ProceedBtn").focus();
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").hide();  
                  $("#OkBtn1").show();              
                  $("#AlertMessage").text('Please enter Name.');
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  return false;
                }
                else if(BRANCH ===""){
                  $("#ProceedBtn").focus();
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").hide();  
                  $("#OkBtn1").show();              
                  $("#AlertMessage").text('Please enter Branch.');
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  return false;
                }
                else if(IFSC ===""){
                  $("#ProceedBtn").focus();
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").hide();  
                  $("#OkBtn1").show();              
                  $("#AlertMessage").text('Please enter IFSC.');
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  return false;
                }
                else if(ACTYPE ===""){
                  $("#ProceedBtn").focus();
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").hide();  
                  $("#OkBtn1").show();              
                  $("#AlertMessage").text('Please select Account Type.');
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  return false;
                }
                else if(ACNO ===""){
                  $("#ProceedBtn").focus();
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").hide();  
                  $("#OkBtn1").show();              
                  $("#AlertMessage").text('Please enter Account No.');
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  return false;
                }
                else if(CTRYID_REF_POPUP ===""){
                  $("#ProceedBtn").focus();
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").hide();  
                  $("#OkBtn1").show();              
                  $("#AlertMessage").text('Please select Country.');
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  return false;
                }
                else if(STID_REF_POPUP ===""){
                  $("#ProceedBtn").focus();
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").hide();  
                  $("#OkBtn1").show();              
                  $("#AlertMessage").text('Please select State.');
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  return false;
                }
                else if(CITYID_REF_POPUP ===""){
                  $("#ProceedBtn").focus();
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").hide();  
                  $("#OkBtn1").show();              
                  $("#AlertMessage").text('Please select City.');
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  return false;
                }
                else if(GLID_REF_POPUP ===""){
                  $("#ProceedBtn").focus();
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").hide();  
                  $("#OkBtn").show();              
                  $("#AlertMessage").text('Please select GL.');
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  return false;
                }
				else if(BANK_GLID_REF_POPUP ===""){
                  $("#ProceedBtn").focus();
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").hide();  
                  $("#OkBtn").show();              
                  $("#AlertMessage").text('Please select Bank Charges GL.');
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  return false;
                }

            }
            else
            {
              if(NAME ===""){
                  $("#ProceedBtn").focus();
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").hide();  
                  $("#OkBtn1").show();              
                  $("#AlertMessage").text('Please enter Name.');
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  return false;
                }
                else if(GLID_REF_POPUP ===""){
                  $("#ProceedBtn").focus();
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").hide();  
                  $("#OkBtn1").show();              
                  $("#AlertMessage").text('Please select GL.');
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  return false;
                }
				else if(BANK_GLID_REF_POPUP ===""){
                  $("#ProceedBtn").focus();
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").hide();  
                  $("#OkBtn").show();              
                  $("#AlertMessage").text('Please select Bank Charges GL.');
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  return false;
                }


            }

            $("#YesBtn").show();
            $("#NoBtn").show();
            $("#OkBtn").hide();  
            $("#OkBtn1").hide();      
            $("#alert").modal('show');
            $("#AlertMessage").text('Do you want to save to record.');
            $("#YesBtn").data("funcname","fnSaveData");  //set dynamic fucntion name
            $("#YesBtn").focus();
            highlighFocusBtn('activeYes');

        }

    });//btnSave

    
    //validate and approve
    $("#btnApprove").click(function() {
        
        if(formDataMst.valid()){


          var BCODE          =   $.trim($("#BCODE").val());
            if(BCODE ===""){
              $("#ProceedBtn").focus();
              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn").hide();  
              $("#OkBtn1").show();              
              $("#AlertMessage").text('Please enter Bank Code.');
              $("#alert").modal('show');
              $("#OkBtn1").focus();
              return false;
            }

            var BANK_CASH = $.trim($("#BANK_CASH").val());
            var NAME = $.trim($("#NAME").val());
            var BRANCH = $.trim($("#BRANCH").val());
            var IFSC = $.trim($("#IFSC").val());
            var ACTYPE = $.trim($("#ACTYPE").val());
            var ACNO = $.trim($("#ACNO").val());
            var CTRYID_REF_POPUP = $.trim($("#CTRYID_REF_POPUP").val());
            var STID_REF_POPUP = $.trim($("#STID_REF_POPUP").val());
            var CITYID_REF_POPUP = $.trim($("#CITYID_REF_POPUP").val());
            var GLID_REF_POPUP = $.trim($("#GLID_REF_POPUP").val());
			var BANK_GLID_REF_POPUP = $.trim($("#BANK_GLID_REF_POPUP").val());
            
            if(BANK_CASH==="B"){
                if(NAME ===""){
                  $("#ProceedBtn").focus();
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").hide();  
                  $("#OkBtn1").show();              
                  $("#AlertMessage").text('Please enter Name.');
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  return false;
                }
                else if(BRANCH ===""){
                  $("#ProceedBtn").focus();
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").hide();  
                  $("#OkBtn1").show();              
                  $("#AlertMessage").text('Please enter Branch.');
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  return false;
                }
                else if(IFSC ===""){
                  $("#ProceedBtn").focus();
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").hide();  
                  $("#OkBtn1").show();              
                  $("#AlertMessage").text('Please enter IFSC.');
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  return false;
                }
                else if(ACTYPE ===""){
                  $("#ProceedBtn").focus();
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").hide();  
                  $("#OkBtn1").show();              
                  $("#AlertMessage").text('Please select Account Type.');
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  return false;
                }
                else if(ACNO ===""){
                  $("#ProceedBtn").focus();
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").hide();  
                  $("#OkBtn1").show();              
                  $("#AlertMessage").text('Please enter Account No.');
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  return false;
                }
                else if(CTRYID_REF_POPUP ===""){
                  $("#ProceedBtn").focus();
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").hide();  
                  $("#OkBtn1").show();              
                  $("#AlertMessage").text('Please select Country.');
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  return false;
                }
                else if(STID_REF_POPUP ===""){
                  $("#ProceedBtn").focus();
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").hide();  
                  $("#OkBtn1").show();              
                  $("#AlertMessage").text('Please select State.');
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  return false;
                }
                else if(CITYID_REF_POPUP ===""){
                  $("#ProceedBtn").focus();
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").hide();  
                  $("#OkBtn1").show();              
                  $("#AlertMessage").text('Please select City.');
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  return false;
                }
                else if(GLID_REF_POPUP ===""){
                  $("#ProceedBtn").focus();
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").hide();  
                  $("#OkBtn").show();              
                  $("#AlertMessage").text('Please select GL.');
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  return false;
                }
				else if(BANK_GLID_REF_POPUP ===""){
                  $("#ProceedBtn").focus();
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").hide();  
                  $("#OkBtn").show();              
                  $("#AlertMessage").text('Please select Bank Charges GL.');
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  return false;
                }

            }
            else
            {
              if(NAME ===""){
                  $("#ProceedBtn").focus();
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").hide();  
                  $("#OkBtn1").show();              
                  $("#AlertMessage").text('Please enter Name.');
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  return false;
                }
                else if(GLID_REF_POPUP ===""){
                  $("#ProceedBtn").focus();
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").hide();  
                  $("#OkBtn1").show();              
                  $("#AlertMessage").text('Please select GL.');
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  return false;
                }
				else if(BANK_GLID_REF_POPUP ===""){
                  $("#ProceedBtn").focus();
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").hide();  
                  $("#OkBtn").show();              
                  $("#AlertMessage").text('Please select Bank Charges GL.');
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  return false;
                }


            }
            
            //set function nane of yes and no btn 
            $("#YesBtn").show();
            $("#NoBtn").show();
            $("#OkBtn").hide();  
            $("#OkBtn1").hide();      
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
            url:'{{route("mastermodify",[141,"update"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    if(data.errors.NAME){
                        showError('ERROR_NAME',data.errors.NAME);
                    }
                   if(data.exist=='norecord') {

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
                    console.log("succes MSG="+data.msg);
                    
                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn1").hide();
                    $("#OkBtn").show();

                    $("#AlertMessage").text(data.msg);

                    $(".text-danger").hide();
                    $("#frm_mst_edit").trigger("reset");

                    $("#alert").modal('show');
                    $("#OkBtn").focus();

                }
                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });

    };// fnSaveData


    // save and approve 
    window.fnApproveData = function (){
        
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
            url:'{{route("mastermodify",[141,"singleapprove"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    if(data.errors.NAME){
                        showError('ERROR_NAME',data.errors.NAME);
                    }
                   if(data.exist=='norecord') {

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
                    console.log("succes MSG="+data.msg);
                    
                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn").show();
                    $("#OkBtn1").hide();

                    $("#AlertMessage").text(data.msg);

                    $(".text-danger").hide();
                    $("#frm_mst_edit").trigger("reset");

                    $("#alert").modal('show');
                    $("#OkBtn").focus();

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
        $("#OkBtn1").hide();

        $(".text-danger").hide();
        window.location.href = '{{route("master",[141,"index"]) }}';

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
    }); 

   
    $("#OkBtn").click(function(){
      
        $("#alert").modal('hide');

    });////ok button


   window.fnUndoYes = function (){
      
      //reload form
      window.location.reload();

   }//fnUndoYes


   window.fnUndoNo = function (){

      $("#BCODE").focus();

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

$(function() { 
  //$("#NAME").focus(); 
});

/* GL popup function */
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
    $("#REG_ADD1").focus(); 
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
</script>


@endpush