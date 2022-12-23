@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
  <div class="row">
      <div class="col-lg-2">
      <a href="{{route('master',[$FormId,'index'])}}" class="btn singlebt">Employee Master</a>
      </div>

      <div class="col-lg-10 topnav-pd">
      <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
          <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
          <button class="btn topnavbt" id="btnSaveSE" disabled="disabled"><i class="fa fa-save"></i> Save</button>
          <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
          <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
          <button class="btn topnavbt" id="btnUndo"  disabled="disabled"><i class="fa fa-undo"></i> Undo</button>
          <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
          <button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-lock"></i> Approved</button>
          <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
          <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
      </div>

  </div>
</div>
   
<div class="container-fluid purchase-order-view filter">     
  <form id="frm_mst_edit" method="POST"  enctype="multipart/form-data" > 
  @CSRF
  {{isset($objResponse->EMPID) ? method_field('PUT') : '' }}
  <div class="inner-form">

  <div class="row">    
        <div class="col-lg-1 pl"><p>Emp Code</p></div>
        <div class="col-lg-2 pl">
          <input disabled type="text" name="EMPCODE" id="EMPCODE" value="{{isset($objResponse->EMPCODE) && $objResponse->EMPCODE !=''?$objResponse->EMPCODE:''}}" class="form-control" required autocomplete="off" maxlength="20" style="text-transform:uppercase" onkeypress="return AlphaNumaric(event,this)" readonly />
          <span class="text-danger" id="ERROR_EMPCODE"></span> 
        </div>

        <div class="col-lg-1 pl"><p>Salutation</p></div>
        <div class="col-lg-2 pl">
          <select name="SAID_REF" id="SAID_REF" class="form-control" disabled>
            <option value="" selected >Select</option>
            @foreach ($getSalutation as $val)
            <option {{isset($objResponse->SAID_REF) && $objResponse->SAID_REF == $val-> FID?'selected="selected"':''}} value="{{ $val-> FID }}">{{ $val-> FNAME }}</option>
            @endforeach
          </select>
          <span class="text-danger" id="ERROR_SAID_REF"></span> 
        </div>
		
        <div class="col-lg-1 pl"><p>First Name</p></div>
        <div class="col-lg-2 pl">
        <input disabled type="text" name="FNAME" id="FNAME" value="{{isset($objResponse->FNAME) && $objResponse->FNAME !=''?$objResponse->FNAME:''}}" class="form-control" autocomplete="off" required   maxlength="50" />
          <span class="text-danger" id="ERROR_FNAME"></span> 
        </div>

        <div class="col-lg-1 pl"><p>Middle Name</p></div>
        <div class="col-lg-2 pl">
        <input disabled type="text" name="MNAME" id="MNAME" value="{{isset($objResponse->MNAME) && $objResponse->MNAME !=''?$objResponse->MNAME:''}}" class="form-control" autocomplete="off"  maxlength="50"  />
          <span class="text-danger" id="ERROR_MNAME"></span> 
        </div>
      </div>

      <div class="row">
        <div class="col-lg-1 pl"><p>Last / Surname</p></div>
        <div class="col-lg-2 pl">
        <input disabled type="text" name="LNAME" id="LNAME" value="{{isset($objResponse->LNAME) && $objResponse->LNAME !=''?$objResponse->LNAME:''}}" class="form-control" autocomplete="off" maxlength="50"  />
          <span class="text-danger" id="ERROR_LNAME"></span> 
        </div>
          	
        <div class="col-lg-1 pl"><p>Old Ref No</p></div>
        <div class="col-lg-2 pl">
        <input disabled type="text" name="OLDREFNO" id="OLDREFNO" value="{{isset($objResponse->OLDREFNO) && $objResponse->OLDREFNO !=''?$objResponse->OLDREFNO:''}}" class="form-control" autocomplete="off" maxlength="50" onkeypress="return isNumberKey(event,this)"  />
          <span class="text-danger" id="ERROR_OLDREFNO"></span> 
        </div>

        <div class="col-lg-1 pl"><p>Gender</p></div>
        <div class="col-lg-2 pl">
          <select name="GID_REF" id="GID_REF" class="form-control" required disabled>
            <option value="" selected >Select</option>
            @foreach ($getGender as $val)
            <option {{isset($objResponse->GID_REF) && $objResponse->GID_REF == $val-> FID?'selected="selected"':''}} value="{{ $val-> FID }}">{{ $val-> FNAME }}</option>
            @endforeach
          </select>
          <span class="text-danger" id="ERROR_GID_REF"></span> 
        </div>

        <div class="col-lg-1 pl"><p>Date of Birth</p></div>
        <div class="col-lg-2 pl">
        <input disabled type="date" name="DOB" id="DOB" value="{{isset($objResponse->DOB) && $objResponse->DOB !=''?date('Y-m-d',strtotime($objResponse->DOB)):''}}" class="form-control" autocomplete="off" required />
          <span class="text-danger" id="ERROR_DOB"></span> 
        </div>
      </div>

      <div class="row">
        <div class="col-lg-1 pl"><p>Birth Place</p></div>
        <div class="col-lg-2 pl">
        <input disabled type="text" name="DOBPLACE" id="DOBPLACE" value="{{isset($objResponse->DOBPLACE) && $objResponse->DOBPLACE !=''?$objResponse->DOBPLACE:''}}" class="form-control" autocomplete="off" maxlength="100"  />
          <span class="text-danger" id="ERROR_DOBPLACE"></span> 
        </div>

        <div class="col-lg-1 pl"><p>Married Status</p></div>
        <div class="col-lg-2 pl">
          <select name="FATHERNAME" id="FATHERNAME" class="form-control" required disabled>
            <option value="" selected >Select</option>
            <option {{isset($objResponse->FATHERNAME) && $objResponse->FATHERNAME == 'single'?'selected="selected"':''}} value="single">Single</option>
            <option {{isset($objResponse->FATHERNAME) && $objResponse->FATHERNAME == 'married'?'selected="selected"':''}} value="married">Married</option>
            <option {{isset($objResponse->FATHERNAME) && $objResponse->FATHERNAME == 'separated'?'selected="selected"':''}}value="separated">Separated</option>
          </select>
          <span class="text-danger" id="ERROR_DESGID_REF"></span> 
        </div>

        <div class="col-lg-1 pl"><p>Blood Group</p></div>
        <div class="col-lg-2 pl">
          <select name="BGID_REF" id="BGID_REF" class="form-control" required disabled>
            <option value="" selected >Select</option>
            @foreach ($getBloodGroup as $val)
            <option {{isset($objResponse->BGID_REF) && $objResponse->BGID_REF == $val-> FID?'selected="selected"':''}} value="{{ $val-> FID }}">{{ $val-> FNAME }}</option>
            @endforeach
          </select>
          <span class="text-danger" id="ERROR_BGID_REF"></span> 
        </div>

        <div class="col-lg-1 pl"><p>Designation</p></div>
        <div class="col-lg-2 pl">
          <select name="DESGID_REF" id="DESGID_REF" class="form-control" required disabled>
            <option value="" selected >Select</option>
            @foreach ($getDesignation as $val)
            <option {{isset($objResponse->DESGID_REF) && $objResponse->DESGID_REF == $val-> FID?'selected="selected"':''}} value="{{ $val-> FID }}">{{ $val-> FNAME }}</option>
            @endforeach
          </select>
          <span class="text-danger" id="ERROR_DESGID_REF"></span> 
        </div>
      </div>

        <div class="row">
        <div class="col-lg-1 pl"><p>Department</p></div>
        <div class="col-lg-2 pl">
          <select name="DEPID_REF" id="DEPID_REF" class="form-control" disabled>
            <option value="" selected >Select</option>
            @foreach ($getDepartment as $val)
            <option {{isset($objResponse->DEPID_REF) && $objResponse->DEPID_REF == $val-> FID?'selected="selected"':''}} value="{{ $val-> FID }}">{{ $val-> FNAME }}</option>
            @endforeach
          </select>
          <span class="text-danger" id="ERROR_BGID_DEPID_REF"></span> 
        </div>

        <div class="col-lg-1 pl"><p>Division</p></div>
        <div class="col-lg-2 pl">
          <select name="DIVID_REF" id="DIVID_REF" class="form-control" disabled>
            <option value="" selected >Select</option>
            @foreach ($getDivision as $val)
            <option {{isset($objResponse->DIVID_REF) && $objResponse->DIVID_REF == $val-> FID?'selected="selected"':''}} value="{{ $val-> FID }}">{{ $val-> FNAME }}</option>
            @endforeach
          </select>
          <span class="text-danger" id="ERROR_DIVID_REF"></span> 
        </div>

        <div class="col-lg-1 pl"><p>Post Level</p></div>
        <div class="col-lg-2 pl">
          <select name="PLID_REF" id="PLID_REF" class="form-control" disabled>
            <option value="" selected >Select</option>
            @foreach ($getPostLevel as $val)
            <option {{isset($objResponse->PLID_REF) && $objResponse->PLID_REF == $val-> FID?'selected="selected"':''}} value="{{ $val-> FID }}">{{ $val-> FNAME }}</option>
            @endforeach
          </select>
          <span class="text-danger" id="ERROR_PLID_REF"></span> 
        </div>
      
        <div class="col-lg-1 pl"><p>Date of Joining</p></div>
        <div class="col-lg-2 pl">
        <input disabled type="date" name="DOJ" id="DOJ" value="{{isset($objResponse->DOJ) && $objResponse->DOJ !=''?date('Y-m-d',strtotime($objResponse->DOJ)):''}}" class="form-control" autocomplete="off" maxlength="200"  />
          <span class="text-danger" id="ERROR_DOJ"></span> 
        </div>
      </div>
        	
      <div class="row">
        <div class="col-lg-1 pl"><p>Emp Category</p></div>
        <div class="col-lg-2 pl">
          <select name="CATID_REF" id="CATID_REF" class="form-control" required disabled>
            <option value="" selected >Select</option>
            @foreach ($getEmployeeCategory as $val)
            <option {{isset($objResponse->CATID_REF) && $objResponse->CATID_REF == $val-> FID?'selected="selected"':''}} value="{{ $val-> FID }}">{{ $val-> FNAME }}</option>
            @endforeach
          </select>
          <span class="text-danger" id="ERROR_CATID_REF"></span> 
        </div>
        
        <div class="col-lg-1 pl"><p>Emp Type</p></div>
        <div class="col-lg-2 pl">
          <select name="ETYPEID_REF" id="ETYPEID_REF" class="form-control" required disabled>
            <option value="" selected >Select</option>
            @foreach ($getEmployeeType as $val)
            <option {{isset($objResponse->ETYPEID_REF) && $objResponse->ETYPEID_REF == $val-> FID?'selected="selected"':''}} value="{{ $val-> FID }}">{{ $val-> FNAME }}</option>
            @endforeach
          </select>
          <span class="text-danger" id="ERROR_ETYPEID_REF"></span> 
        </div>

        <div class="col-lg-1 pl"><p>Cost Centre</p></div>
        <div class="col-lg-2 pl">
          <select name="CCID_REF" id="CCID_REF" class="form-control" disabled>
            <option value="" selected >Select</option>
            @foreach ($getCostCentre as $val)
            <option {{isset($objResponse->CCID_REF) && $objResponse->CCID_REF == $val-> FID?'selected="selected"':''}} value="{{ $val-> FID }}">{{ $val-> FNAME }}</option>
            @endforeach
          </select>
          <span class="text-danger" id="ERROR_CCID_REF"></span> 
        </div>
      
        <div class="col-lg-1 pl"><p>Grade</p></div>
        <div class="col-lg-2 pl">
          <select name="GRADEID_REF" id="GRADEID_REF" class="form-control" disabled>
            <option value="" selected >Select</option>
            @foreach ($getGrade as $val)
            <option {{isset($objResponse->GRADEID_REF) && $objResponse->GRADEID_REF == $val-> FID?'selected="selected"':''}} value="{{ $val-> FID }}">{{ $val-> FNAME }}</option>
            @endforeach
          </select>
          <span class="text-danger" id="ERROR_GRADEID_REF"></span> 
        </div>
      </div>

     <div class="row"> 
        <div class="col-lg-1 pl"><p>Branch</p></div>
        <div class="col-lg-2 pl">
          <select name="BRID_REF" id="BRID_REF" class="form-control" disabled>
            <option value="" selected >Select</option>
            @foreach ($getBranch as $val)
            <option {{isset($objResponse->BRID_REF) && $objResponse->BRID_REF == $val-> FID?'selected="selected"':''}} value="{{ $val-> FID }}">{{ $val-> FNAME }}</option>
            @endforeach
          </select>
          <span class="text-danger" id="ERROR_BRID_REF"></span> 
        </div>

        <div class="col-lg-1 pl"><p>Sales Person</p></div>
        <div class="col-lg-2 pl"> 
          <input disabled type="checkbox" name="SALES_PERSON" id="SALES_PERSON" {{isset($objResponse->SALES_PERSON) && $objResponse->SALES_PERSON =='1'?'checked':''}} value="1" >
          <span class="text-danger" id="ERROR_SALES_PERSON"></span>
        </div>
          
        <div class="col-lg-2 pl"><p>De-Activated</p></div>
        <div class="col-lg-1 pl pr">
        <input disabled type="checkbox"   name="DEACTIVATED"  id="deactive-checkbox_0" {{$objResponse->DEACTIVATED == 1 ? "checked" : ""}}
          value='{{$objResponse->DEACTIVATED == 1 ? 1 : 0}}' tabindex="2"  >
        </div>
        
        <div class="col-lg-2 pl"><p>Date of De-Activated</p></div>
        <div class="col-lg-2 pl">
          <input disabled type="date" name="DODEACTIVATED" class="form-control" id="DODEACTIVATED" {{$objResponse->DEACTIVATED == 1 ? "" : "disabled"}} value="{{isset($objResponse->DODEACTIVATED) && $objResponse->DODEACTIVATED !="" && $objResponse->DODEACTIVATED !="1900-01-01" ? $objResponse->DODEACTIVATED:''}}" tabindex="3" placeholder="dd/mm/yyyy"  />
        </div>
      </div>

      <div class="row">
        <ul class="nav nav-tabs">
          <li class="active"><a data-toggle="tab" href="#tab1">Address</a></li>
          <li class=""><a data-toggle="tab" href="#tab2">Family Members</a></li>
          <li class=""><a data-toggle="tab" href="#tab3">Education</a></li>
          <li class=""><a data-toggle="tab" href="#tab4">Experience</a></li>
          <li class=""><a data-toggle="tab" href="#tab5">Reference</a></li>
          <li class=""><a data-toggle="tab" href="#tab6">Extra curricular Activity</a></li>
          <li class=""><a data-toggle="tab" href="#tab7">Legal</a></li>
          <li class=""><a data-toggle="tab" href="#tab8">Contact Detail</a></li>
          <li class=""><a data-toggle="tab" href="#tab9">Others</a></li>
        </ul>

        <div class="tab-content">

        <div id="tab1" class="tab-pane fade in active">
          <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="margin-top:10px;">
          
            <div class="row">
              <div class="col-lg-2 "><p>Current Residence</p></div>
              <div class="col-lg-1 ">
                <input disabled type="checkbox" name="CRESIDENCE" id="CRESIDENCE" {{isset($objTab1->CRESIDENCE) && $objTab1->CRESIDENCE =='1'?'checked':''}} value="1" >
                <span class="text-danger" id="ERROR_CRESIDENCE"></span>
              </div>

              <div class="col-lg-2 "><p>Own / Parents Residence</p></div>
              <div class="col-lg-1 ">
                <input disabled type="checkbox" name="CROWN" id="CROWN" {{isset($objTab1->CROWN) && $objTab1->CROWN =='1'?'checked':''}} value="1" onchange="getCParentRented('CROWN')" >
                <span class="text-danger" id="ERROR_CROWN"></span>
              </div>

              <div class="col-lg-1 "><p>Rented</p></div>
              <div class="col-lg-1 ">
                <input disabled type="checkbox" name="CRRENTED" id="CRRENTED" {{isset($objTab1->CRRENTED) && $objTab1->CRRENTED =='1'?'checked':''}} value="1" onchange="getCParentRented('CRRENTED')" >
                <span class="text-danger" id="ERROR_CRRENTED"></span>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-1 "><p>Add Line 1</p></div>
              <div class="col-lg-5 ">
                <textarea disabled name="CRADD1" id="CRADD1" class="form-control" autocomplete="off" >{{isset($objTab1->CRADD1) && $objTab1->CRADD1 !=''?$objTab1->CRADD1:''}}</textarea>
                <span class="text-danger" id="ERROR_CRADD1"></span>
              </div>

              <div class="col-lg-1 "><p>Add Line 2</p></div>
              <div class="col-lg-5 ">
                <textarea disabled name="CRADD2" id="CRADD2" class="form-control" autocomplete="off" >{{isset($objTab1->CRADD2) && $objTab1->CRADD2 !=''?$objTab1->CRADD2:''}}</textarea>
                <span class="text-danger" id="ERROR_CRADD2"></span>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-1"><p>Country</p></div>
              <div class="col-lg-2">
                  <input disabled type="text" name="CTRYID_REF_POPUP" id="CTRYID_REF_POPUP" value="{{isset($objCountryName->CTRYCODE)?$objCountryName->CTRYCODE. ' - ':''}}  {{isset($objCountryName->NAME)?$objCountryName->NAME:''}}" class="form-control" readonly />
                  <input type="hidden" name="CTRYID_REF" id="CTRYID_REF" value="{{isset($objTab1->CRCTRYID_REF) && $objTab1->CRCTRYID_REF !=''?$objTab1->CRCTRYID_REF:''}}" />
                  <span class="text-danger" id="ERROR_CTRYID_REF"></span>
              </div>

              <div class="col-lg-1"><p>State</p></div>
              <div class="col-lg-2">
                  <input  disabled type="text" name="STID_REF_POPUP" id="STID_REF_POPUP" value="{{isset($objStateName->STCODE)?$objStateName->STCODE. ' - ':''}}  {{isset($objStateName->NAME)?$objStateName->NAME:''}}" class="form-control"  readonly/>
                  <input type="hidden" name="STID_REF" id="STID_REF" value="{{isset($objTab1->CRSTID_REF) && $objTab1->CRSTID_REF !=''?$objTab1->CRSTID_REF:''}}" />
                  <span class="text-danger" id="ERROR_STID_REF"></span>
              </div>

              
              <div class="col-lg-1"><p>City</p></div>
              <div class="col-lg-2">
                <input disabled type="text" name="CITYID_REF_POPUP" id="CITYID_REF_POPUP" value="{{isset($objCityName->CITYCODE)?$objCityName->CITYCODE. ' - ':''}}  {{isset($objCityName->NAME)?$objCityName->NAME:''}}" class="form-control" readonly tabindex="8" />
                <input type="hidden" name="CITYID_REF" id="CITYID_REF" value="{{isset($objTab1->CRCITYID_REF) && $objTab1->CRCITYID_REF !=''?$objTab1->CRCITYID_REF:''}}" />
                <span class="text-danger" id="ERROR_CITYID_REF"></span>
              </div>

              
            
            </div>

            <div class="row">
              <div class="col-lg-1"><p>District</p></div>
              <div class="col-lg-2">
                <input disabled type="text" name="DISTID_REF_POPUP" id="DISTID_REF_POPUP" value="{{isset($objDisticName->DISTCODE)?$objDisticName->DISTCODE. ' - ' :''}}  {{isset($objDisticName->NAME)?$objDisticName->NAME:''}}" class="form-control" readonly tabindex="9" />
                <input type="hidden" name="DISTID_REF" id="DISTID_REF" value="{{isset($objTab1->CRDISTID_REF) && $objTab1->CRDISTID_REF !=''?$objTab1->CRDISTID_REF:''}}" />
                <span class="text-danger" id="ERROR_DISTID_REF"></span>
              </div>

              <div class="col-lg-1"><p>Pincode</p></div>
              <div class="col-lg-2">
                  <input disabled type="text" name="CRPIN" id="CRPIN" value="{{isset($objTab1->CRPIN) && $objTab1->CRPIN !=''?$objTab1->CRPIN:''}}" class="form-control" autocomplete="off"  maxlength="10" onkeypress="return isNumberKey(event,this)" >
              </div>

              <div class="col-lg-1"><p>Landmark</p></div>
              <div class="col-lg-4">
                <input disabled type="text" name="CRLM" id="CRLM" value="{{isset($objTab1->CRLM) && $objTab1->CRLM !=''?$objTab1->CRLM:''}}" class="form-control" autocomplete="off"  maxlength="50" >
              </div>
            </div>

            <div class="row">
              <div class="col-lg-2 "><p>Permanant Residence</p></div>
              <div class="col-lg-1 ">
                <input disabled type="checkbox" name="PRRESIDENCE" id="PRRESIDENCE" {{isset($objTab1->PRRESIDENCE) && $objTab1->PRRESIDENCE =='1'?'checked':''}}  value="1" >
                <span class="text-danger" id="ERROR_PRRESIDENCE"></span>
              </div>

              <div class="col-lg-2 "><p>Own / Parents Residence</p></div>
              <div class="col-lg-1 ">
                <input disabled type="checkbox" name="PROWN" id="PROWN" {{isset($objTab1->PROWN) && $objTab1->PROWN =='1'?'checked':''}} value="1" onchange="getPParentRented('PROWN')" >
                <span class="text-danger" id="ERROR_PROWN"></span>
              </div>

              <div class="col-lg-1 "><p>Rented</p></div>
              <div class="col-lg-1 ">
                <input disabled type="checkbox" name="PRRENTED" id="PRRENTED" {{isset($objTab1->PRRENTED) && $objTab1->PRRENTED =='1'?'checked':''}} value="1" onchange="getPParentRented('PRRENTED')" >
                <span class="text-danger" id="ERROR_PRRENTED"></span>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-1 "><p>Add Line 1</p></div>
              <div class="col-lg-5 ">
                <textarea disabled name="PRADD1" id="PRADD1" class="form-control" autocomplete="off" maxlength="100" >{{isset($objTab1->PRADD1) && $objTab1->PRADD1 !=''?$objTab1->PRADD1:''}}</textarea>
                <span class="text-danger" id="ERROR_PRADD1"></span>
              </div>

              <div class="col-lg-1 "><p>Add Line 2</p></div>
              <div class="col-lg-5 ">
                <textarea disabled name="PRADD2" id="PRADD2" class="form-control" autocomplete="off" maxlength="100" >{{isset($objTab1->PRADD2) && $objTab1->PRADD2 !=''?$objTab1->PRADD2:''}}</textarea>
                <span class="text-danger" id="ERROR_PRADD2"></span>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-1"><p>Country</p></div>
              <div class="col-lg-2">
                  <input disabled type="text" name="CTRYID_REF_POPUP1" id="CTRYID_REF_POPUP1" value="{{isset($objCountryName->CTRYCODE)?$objCountryName->CTRYCODE. ' - ':''}}  {{isset($objCountryName->NAME)?$objCountryName->NAME:''}}" class="form-control" readonly />
                  <input type="hidden" name="CTRYID_REF1" id="CTRYID_REF1" value="{{isset($objTab1->PRCTRYID_REF) && $objTab1->PRCTRYID_REF !=''?$objTab1->PRCTRYID_REF:''}}" />
                  <span class="text-danger" id="ERROR_CTRYID_REF1"></span>
              </div>

              <div class="col-lg-1"><p>State</p></div>
              <div class="col-lg-2">
                  <input disabled type="text" name="STID_REF_POPUP1" id="STID_REF_POPUP1" value="{{isset($objStateName->STCODE)?$objStateName->STCODE. ' - ':''}}  {{isset($objStateName->NAME)?$objStateName->NAME:''}}" class="form-control" readonly/>
                  <input type="hidden" name="STID_REF1" id="STID_REF1" value="{{isset($objTab1->PRSTID_REF) && $objTab1->PRSTID_REF !=''?$objTab1->PRSTID_REF:''}}" />
                  <span class="text-danger" id="ERROR_STID_REF1"></span>
              </div>

              
              <div class="col-lg-1"><p>City</p></div>
              <div class="col-lg-2">
                <input disabled type="text" name="CITYID_REF_POPUP1" id="CITYID_REF_POPUP1" value="{{isset($objCityName->CITYCODE)?$objCityName->CITYCODE. ' - ':''}}  {{isset($objCityName->NAME)?$objCityName->NAME:''}}" class="form-control" readonly />
                <input type="hidden" name="CITYID_REF1" id="CITYID_REF1" value="{{isset($objTab1->PRCITYID_REF) && $objTab1->PRCITYID_REF !=''?$objTab1->PRCITYID_REF:''}}" />
                <span class="text-danger" id="ERROR_CITYID_REF1"></span>
              </div>

            </div>

            <div class="row">
              <div class="col-lg-1"><p>District</p></div>
              <div class="col-lg-2">
                <input disabled type="text" name="DISTID_REF_POPUP1" id="DISTID_REF_POPUP1" value="{{isset($objDisticName->DISTCODE)?$objDisticName->DISTCODE. ' - ' :''}}  {{isset($objDisticName->NAME)?$objDisticName->NAME:''}}" class="form-control" readonly  />
                <input type="hidden" name="DISTID_REF1" id="DISTID_REF1" value="{{isset($objTab1->PRDISTID_REF) && $objTab1->PRDISTID_REF !=''?$objTab1->PRDISTID_REF:''}}" />
                <span class="text-danger" id="ERROR_DISTID_REF1"></span>
              </div>

              <div class="col-lg-1"><p>Pincode</p></div>
              <div class="col-lg-2">
                  <input disabled type="text" name="PRPIN" id="PRPIN" value="{{isset($objTab1->PRPIN) && $objTab1->PRPIN !=''?$objTab1->PRPIN:''}}" class="form-control" autocomplete="off"  maxlength="10" onkeypress="return isNumberKey(event,this)" >
              </div>

              <div class="col-lg-1"><p>Landmark</p></div>
              <div class="col-lg-4">
                <input disabled type="text" name="PRLM" id="PRLM" value="{{isset($objTab1->PRLM) && $objTab1->PRLM !=''?$objTab1->PRLM:''}}" class="form-control" autocomplete="off"  maxlength="50" >
              </div>
            </div>



          </div>
        </div>




        <div id="tab2" class="tab-pane fade">

          <div class=" table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:300px;" >
            <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
              <thead id="thead1"  style="position: sticky;top: 0">
                <tr>
                <th>Name of Member</th>
                <th hidden><input class="form-control" type="hidden" name="Row_Count2" id ="Row_Count2"></th>
                <th>DOB</th>
                <th>Gender</th>
                <th>Relationship</th>
                <th>Earning</th>
                <th>Contact No</th>
                <th>Email ID</th>
                <th width="5%">Action</th>
                </tr>
              </thead>
              <tbody>
                @if(!empty($objTab2))
                @php $n2=1; @endphp
                @foreach($objTab2 as $key2=>$tab2)
                <tr  class="participantRow2">
                    <td><input disabled type="text" name="FAMILY_NAME_{{$key2}}" id ="FAMILY_NAME_{{$key2}}" value="{{isset($tab2->NAME) && $tab2->NAME !=''?$tab2->NAME:''}}"  maxlength="100" class="form-control w-100" autocomplete="off" ></td>
                    <td><input disabled type="date" name="FAMILY_DOB_{{$key2}}" id ="FAMILY_DOB_{{$key2}}" value="{{isset($tab2->DOB) && $tab2->DOB !=''?date('Y-m-d',strtotime($tab2->DOB)):''}}"   class="form-control w-100" autocomplete="off" ></td>
                    
                    <td>
                    <select disabled name="FAMILY_GID_REF_{{$key2}}" id="FAMILY_GID_REF_{{$key2}}" class="form-control w-100" >
                      <option value="" >Select</option>
                      @foreach ($getGender as $val)
                      <option {{isset($tab2->GID_REF) && $tab2->GID_REF==$val-> FID?'selected="selected"':''}} value="{{ $val-> FID }}">{{ $val-> FNAME }}</option>
                      @endforeach
                    </select>
                    </td>

                    <td>
                      <select disabled name="FAMILY_RSID_REF_{{$key2}}" id="FAMILY_RSID_REF_{{$key2}}" class="form-control w-100" >
                        <option value="" >Select</option>
                        @foreach ($getRelationShip as $val)
                        <option {{isset($tab2->RSID_REF) && $tab2->RSID_REF==$val-> FID?'selected="selected"':''}} value="{{ $val-> FID }}">{{ $val-> FNAME }}</option>
                        @endforeach
                      </select>
                    </td>

                    <td>
                      <select disabled name="FAMILY_EARNING_{{$key2}}" id="FAMILY_EARNING_{{$key2}}" class="form-control w-100" >
                        <option value="">Select</option>
                        <option {{isset($tab2->EARNING) && $tab2->EARNING=='1'?'selected="selected"':''}} value="1">Yes</option>
                        <option {{isset($tab2->EARNING) && $tab2->EARNING=='0'?'selected="selected"':''}} value="0">No</option>
                      </select>
                    </td>

                    <td><input disabled type="text" name="FAMILY_CONTACTNO_{{$key2}}" id ="FAMILY_CONTACTNO_{{$key2}}" value="{{isset($tab2->CONTACTNO) && $tab2->CONTACTNO !=''?$tab2->CONTACTNO:''}}"  maxlength="20" class="form-control w-100" autocomplete="off" onkeypress="return isNumberKey(event,this)"  ></td>
                    <td><input disabled type="text" name="FAMILY_EMAIL_{{$key2}}" id ="FAMILY_EMAIL_{{$key2}}" value="{{isset($tab2->EMAIL) && $tab2->EMAIL !=''?$tab2->EMAIL:''}}"  maxlength="50" class="form-control w-100" autocomplete="off" ></td>
                    <td align="center" >
                        <button class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></button>
                        <button class="btn remove" title="Delete" data-toggle="tooltip" {{isset($n2) && $n2 ==1?'disabled':''}} ><i class="fa fa-trash" ></i></button>
                    </td>
                </tr>
                @php $n2++; @endphp
                @endforeach
                @else
                <tr  class="participantRow2">
                    <td><input disabled type="text" name="FAMILY_NAME_0" id ="FAMILY_NAME_0"  maxlength="100" class="form-control w-100" autocomplete="off" ></td>
                    <td><input disabled type="date" name="FAMILY_DOB_0" id ="FAMILY_DOB_0"   class="form-control w-100" autocomplete="off" ></td>
                    
                    <td>
                    <select disabled name="FAMILY_GID_REF_0" id="FAMILY_GID_REF_0" class="form-control w-100" >
                      <option value="" >Select</option>
                      @foreach ($getGender as $val)
                      <option value="{{ $val-> FID }}">{{ $val-> FNAME }}</option>
                      @endforeach
                    </select>
                    </td>

                    <td>
                      <select disabled name="FAMILY_RSID_REF_0" id="FAMILY_RSID_REF_0" class="form-control w-100" >
                        <option value="" >Select</option>
                        @foreach ($getRelationShip as $val)
                        <option value="{{ $val-> FID }}">{{ $val-> FNAME }}</option>
                        @endforeach
                      </select>
                    </td>

                    <td>
                      <select disabled name="FAMILY_EARNING_0" id="FAMILY_EARNING_0" class="form-control w-100" >
                        <option value="">Select</option>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                      </select>
                    </td>

                    <td><input disabled type="text" name="FAMILY_CONTACTNO_0" id ="FAMILY_CONTACTNO_0"  maxlength="20" class="form-control w-100" autocomplete="off" onkeypress="return isNumberKey(event,this)" ></td>
                    <td><input disabled type="text" name="FAMILY_EMAIL_0" id ="FAMILY_EMAIL_0"  maxlength="50" class="form-control w-100" autocomplete="off" ></td>
                    <td align="center" >
                        <button class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></button>
                        <button class="btn remove" title="Delete" data-toggle="tooltip" disabled><i class="fa fa-trash" ></i></button>
                    </td>
                </tr>
                @endif
              </tbody>
            </table>
          </div>

        </div>


        <div id="tab3" class="tab-pane fade">
          <div class=" table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:300px;" >
            <table id="example3" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
              <thead id="thead1"  style="position: sticky;top: 0">
                <tr>
                <th>Course / Certificate / Degree</th>
                <th hidden><input class="form-control" type="hidden" name="Row_Count3" id ="Row_Count3" ></th>
                <th>Year of Passing</th>
                <th>School / College / University / Institute</th>
                <th>Result</th>
                <th>Remarks</th>
                <th width="10%">Action</th>
                </tr>
              </thead>
              <tbody>
                @if(!empty($objTab3))
                @php $n3=1; @endphp
                @foreach($objTab3 as $key3=>$tab3)
                <tr  class="participantRow3">
                    <td><input disabled type="text" name="EDUCATION_DEGREE_{{$key3}}" id ="EDUCATION_DEGREE_{{$key3}}" value="{{isset($tab3->DEGREE) && $tab3->DEGREE !=''?$tab3->DEGREE:''}}"  maxlength="100" class="form-control w-100" autocomplete="off" ></td>
                    <td><input disabled type="text" name="EDUCATION_YOP_{{$key3}}" id ="EDUCATION_YOP_{{$key3}}" value="{{isset($tab3->YOP) && $tab3->YOP !=''?$tab3->YOP:''}}"  maxlength="20" class="form-control w-100" autocomplete="off" ></td>
                    <td><input disabled type="text" name="EDUCATION_UNIVERSITY_{{$key3}}" id ="EDUCATION_UNIVERSITY_{{$key3}}" value="{{isset($tab3->UNIVERSITY) && $tab3->UNIVERSITY !=''?$tab3->UNIVERSITY:''}}"  maxlength="100" class="form-control w-100" autocomplete="off" ></td>
                    <td><input disabled type="text" name="EDUCATION_RESULT_{{$key3}}" id ="EDUCATION_RESULT_{{$key3}}" value="{{isset($tab3->RESULT) && $tab3->RESULT !=''?$tab3->RESULT:''}}"  maxlength="50" class="form-control w-100" autocomplete="off" ></td>
                    <td><input disabled type="text" name="EDUCATION_REMARKS_{{$key3}}" id ="EDUCATION_REMARKS_{{$key3}}" value="{{isset($tab3->REMARKS) && $tab3->REMARKS !=''?$tab3->REMARKS:''}}"  maxlength="100" class="form-control w-100" autocomplete="off" ></td>
                    <td align="center" >
                        <button disabled class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></button>
                        <button disabled class="btn remove" title="Delete" data-toggle="tooltip" {{isset($n3) && $n3 ==1?'disabled':''}}><i class="fa fa-trash" ></i></button>
                    </td>
                </tr>
                @php $n3++; @endphp
                @endforeach
                @else
                <tr  class="participantRow3">
                    <td><input disabled type="text" name="EDUCATION_DEGREE_0" id ="EDUCATION_DEGREE_0"  maxlength="100" class="form-control w-100" autocomplete="off" ></td>
                    <td><input disabled type="text" name="EDUCATION_YOP_0" id ="EDUCATION_YOP_0"  maxlength="20" class="form-control w-100" autocomplete="off" ></td>
                    <td><input disabled type="text" name="EDUCATION_UNIVERSITY_0" id ="EDUCATION_UNIVERSITY_0"  maxlength="100" class="form-control w-100" autocomplete="off" ></td>
                    <td><input disabled type="text" name="EDUCATION_RESULT_0" id ="EDUCATION_RESULT_0"  maxlength="50" class="form-control w-100" autocomplete="off" ></td>
                    <td><input disabled type="text" name="EDUCATION_REMARKS_0" id ="EDUCATION_REMARKS_0"  maxlength="100" class="form-control w-100" autocomplete="off" ></td>
                    <td align="center" >
                        <button disabled class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></button>
                        <button disabled class="btn remove" title="Delete" data-toggle="tooltip" disabled><i class="fa fa-trash" ></i></button>
                    </td>
                </tr>
                @endif
              </tbody>
            </table>
          </div>

        </div>


        <div id="tab4" class="tab-pane fade">
          <div class=" table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:300px;" >
            <table id="example4" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
              <thead id="thead1"  style="position: sticky;top: 0">
                <tr>
                <th hidden><input class="form-control" type="hidden" name="Row_Count4" id ="Row_Count4"></th>
                <th>Company Name</th>
                <th>From Period</th>
                <th>To Period</th>
                <th>Last Designation</th>
                <th>CTC Per Annum</th>
                <th>Reason of Leaving / remarks</th>
                <th width="10%">Action</th>
                </tr>
              </thead>
              <tbody>
                @if(!empty($objTab4))
                @php $n4=1; @endphp
                @foreach($objTab4 as $key4=>$tab4)
                <tr  class="participantRow4">
                    <td><input disabled type="text" name="EXPERIENCE_CNAME_{{$key4}}" id ="EXPERIENCE_CNAME_{{$key4}}" value="{{isset($tab4->CNAME) && $tab4->CNAME !=''?$tab4->CNAME:''}}"  maxlength="100" class="form-control w-100" autocomplete="off" ></td>
                    <td><input disabled type="text" name="EXPERIENCE_FROMPD_{{$key4}}" id ="EXPERIENCE_FROMPD_{{$key4}}" value="{{isset($tab4->FROMPD) && $tab4->FROMPD !=''?$tab4->FROMPD:''}}"  maxlength="30" class="form-control w-100" autocomplete="off" ></td>
                    <td><input disabled type="text" name="EXPERIENCE_TOPD_{{$key4}}" id ="EXPERIENCE_TOPD_{{$key4}}" value="{{isset($tab4->TOPD) && $tab4->TOPD !=''?$tab4->TOPD:''}}"  maxlength="30" class="form-control w-100" autocomplete="off" ></td>
                    <td><input disabled type="text" name="EXPERIENCE_LASTDESIG_{{$key4}}" id ="EXPERIENCE_LASTDESIG_{{$key4}}" value="{{isset($tab4->LASTDESIG) && $tab4->LASTDESIG !=''?$tab4->LASTDESIG:''}}"  maxlength="100" class="form-control w-100" autocomplete="off" ></td>
                    <td><input disabled type="text" name="EXPERIENCE_CTCPA_{{$key4}}" id ="EXPERIENCE_CTCPA_{{$key4}}" value="{{isset($tab4->CTCPA) && $tab4->CTCPA !=''?$tab4->CTCPA:''}}"  maxlength="50" class="form-control w-100" autocomplete="off" ></td>
                    <td><input disabled type="text" name="EXPERIENCE_REMARKS_{{$key4}}" id ="EXPERIENCE_REMARKS_{{$key4}}" value="{{isset($tab4->REMARKS) && $tab4->REMARKS !=''?$tab4->REMARKS:''}}"  maxlength="100" class="form-control w-100" autocomplete="off" ></td>
                    <td align="center" >
                        <button disabled class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></button>
                        <button disabled class="btn remove" title="Delete" data-toggle="tooltip" {{isset($n4) && $n4 ==1?'disabled':''}}><i class="fa fa-trash" ></i></button>
                    </td>
                </tr>
                @php $n4++; @endphp
                @endforeach
                @else
                <tr  class="participantRow4">
                    <td><input disabled type="text" name="EXPERIENCE_CNAME_0" id ="EXPERIENCE_CNAME_0"  maxlength="100" class="form-control w-100" autocomplete="off" ></td>
                    <td><input disabled type="text" name="EXPERIENCE_FROMPD_0" id ="EXPERIENCE_FROMPD_0"  maxlength="30" class="form-control w-100" autocomplete="off" ></td>
                    <td><input disabled type="text" name="EXPERIENCE_TOPD_0" id ="EXPERIENCE_TOPD_0"  maxlength="30" class="form-control w-100" autocomplete="off" ></td>
                    <td><input disabled type="text" name="EXPERIENCE_LASTDESIG_0" id ="EXPERIENCE_LASTDESIG_0"  maxlength="100" class="form-control w-100" autocomplete="off" ></td>
                    <td><input disabled type="text" name="EXPERIENCE_CTCPA_0" id ="EXPERIENCE_CTCPA_0"  maxlength="50" class="form-control w-100" autocomplete="off" ></td>
                    <td><input disabled type="text" name="EXPERIENCE_REMARKS_0" id ="EXPERIENCE_REMARKS_0"  maxlength="100" class="form-control w-100" autocomplete="off" ></td>
                    <td align="center" >
                        <button disabled class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></button>
                        <button class="btn remove" title="Delete" data-toggle="tooltip" disabled><i class="fa fa-trash" ></i></button>
                    </td>
                </tr>
                @endif
              </tbody>
            </table>
          </div>

        </div>

        <div id="tab5" class="tab-pane fade">
          <div class=" table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:300px;" >
            <table id="example5" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
              <thead id="thead1"  style="position: sticky;top: 0">
                <tr>
                <th hidden><input class="form-control" type="hidden" name="Row_Count5" id ="Row_Count5"></th>
                <th>Name</th>
                <th>Gender</th>
                <th>Company</th>
                <th>Designation</th>
                <th>Cell No</th>
                <th>Email ID</th>
                <th width="10%">Action</th>
                </tr>
              </thead>
              <tbody>
                @if(!empty($objTab5))
                @php $n5=1; @endphp
                @foreach($objTab5 as $key5=>$tab5)
                <tr  class="participantRow5">
                    <td><input disabled type="text" name="REFERENCE_RNAME_{{$key5}}" id ="REFERENCE_RNAME_{{$key5}}" value="{{isset($tab5->RNAME) && $tab5->RNAME !=''?$tab5->RNAME:''}}"  maxlength="100" class="form-control w-100" autocomplete="off" ></td>
                    <td>
                    <select disabled name="REFERENCE_GID_REF_{{$key5}}" id="REFERENCE_GID_REF_{{$key5}}" class="form-control w-100" >
                      <option value="" selected >Select</option>
                      @foreach ($getGender as $val)
                      <option {{isset($tab5->GID_REF) && $tab5->GID_REF == $val-> FID?'selected="selected"':''}} value="{{ $val-> FID }}">{{ $val-> FNAME }}</option>
                      @endforeach
                    </select>
                    </td>
                    <td><input disabled type="text" name="REFERENCE_COMPANY_{{$key5}}" id ="REFERENCE_COMPANY_{{$key5}}" value="{{isset($tab5->COMPANY) && $tab5->COMPANY !=''?$tab5->COMPANY:''}}"  maxlength="100" class="form-control w-100" autocomplete="off" ></td>
                    <td><input disabled type="text" name="REFERENCE_DESIG_{{$key5}}" id ="REFERENCE_DESIG_{{$key5}}" value="{{isset($tab5->DESIG) && $tab5->DESIG !=''?$tab5->DESIG:''}}"  maxlength="50" class="form-control w-100" autocomplete="off" ></td>
                    <td><input disabled type="text" name="REFERENCE_MONO_{{$key5}}" id ="REFERENCE_MONO_{{$key5}}" value="{{isset($tab5->MONO) && $tab5->MONO !=''?$tab5->MONO:''}}"  maxlength="20" class="form-control w-100" autocomplete="off" ></td>
                    <td><input disabled type="text" name="REFERENCE_EMAIL_{{$key5}}" id ="REFERENCE_EMAIL_{{$key5}}" value="{{isset($tab5->EMAIL) && $tab5->EMAIL !=''?$tab5->EMAIL:''}}"  maxlength="50" class="form-control w-100" autocomplete="off" ></td>
                    <td align="center" >
                        <button disabled class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></button>
                        <button disabled class="btn remove" title="Delete" data-toggle="tooltip" {{isset($n5) && $n5 ==1?'disabled':''}}><i class="fa fa-trash" ></i></button>
                    </td>
                </tr>
                @php $n5++; @endphp
                @endforeach
                @else
                <tr  class="participantRow5">
                    <td><input disabled type="text" name="REFERENCE_RNAME_0" id ="REFERENCE_RNAME_0"  maxlength="100" class="form-control w-100" autocomplete="off" ></td>
                    <td>
                    <select disabled name="REFERENCE_GID_REF_0" id="REFERENCE_GID_REF_0" class="form-control w-100" >
                      <option value="" selected >Select</option>
                      @foreach ($getGender as $val)
                      <option value="{{ $val-> FID }}">{{ $val-> FNAME }}</option>
                      @endforeach
                    </select>
                    </td>
                    <td><input disabled type="text" name="REFERENCE_COMPANY_0" id ="REFERENCE_COMPANY_0"  maxlength="100" class="form-control w-100" autocomplete="off" ></td>
                    <td><input disabled type="text" name="REFERENCE_DESIG_0" id ="REFERENCE_DESIG_0"  maxlength="50" class="form-control w-100" autocomplete="off" ></td>
                    <td><input disabled type="text" name="REFERENCE_MONO_0" id ="REFERENCE_MONO_0"  maxlength="20" class="form-control w-100" autocomplete="off" ></td>
                    <td><input disabled type="text" name="REFERENCE_EMAIL_0" id ="REFERENCE_EMAIL_0"  maxlength="50" class="form-control w-100" autocomplete="off" ></td>
                    <td align="center" >
                        <button disabled class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></button>
                        <button class="btn remove" title="Delete" data-toggle="tooltip" disabled><i class="fa fa-trash" ></i></button>
                    </td>
                </tr>
                @endif
              </tbody>
            </table>
          </div>

        </div>


        <div id="tab6" class="tab-pane fade">
          <div class=" table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:300px;" >
            <table id="example6" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
              <thead id="thead1"  style="position: sticky;top: 0">
                <tr>
                <th hidden><input class="form-control" type="hidden" name="Row_Count6" id ="Row_Count6"></th>
                <th>Activity Name</th>
                <th>Period</th>
                <th>Level</th>
                <th>Achievement</th>
                <th width="10%">Action</th>
                </tr>
              </thead>
              <tbody>
              @if(!empty($objTab6))
              @php $n6=1; @endphp
                @foreach($objTab6 as $key6=>$tab6)
                <tr  class="participantRow6">
                    <td><input disabled type="text" name="EXTRACUR_NAME_{{$key6}}" id ="EXTRACUR_NAME_{{$key6}}" value="{{isset($tab6->NAME) && $tab6->NAME !=''?$tab6->NAME:''}}" maxlength="100" class="form-control w-100" autocomplete="off" ></td>
                    <td><input disabled type="text" name="EXTRACUR_PERIOD_{{$key6}}" id ="EXTRACUR_PERIOD_{{$key6}}" value="{{isset($tab6->PERIOD) && $tab6->PERIOD !=''?$tab6->PERIOD:''}}"  maxlength="50" class="form-control w-100" autocomplete="off" ></td>
                    <td><input disabled type="text" name="EXTRACUR_LEVELS_{{$key6}}" id ="EXTRACUR_LEVELS_{{$key6}}" value="{{isset($tab6->LEVELS) && $tab6->LEVELS !=''?$tab6->LEVELS:''}}"  maxlength="50" class="form-control w-100" autocomplete="off" ></td>
                    <td><input disabled type="text" name="EXTRACUR_ACHIEVEMENT_{{$key6}}" id ="EXTRACUR_ACHIEVEMENT_{{$key6}}" value="{{isset($tab6->ACHIEVEMENT) && $tab6->ACHIEVEMENT !=''?$tab6->ACHIEVEMENT:''}}" maxlength="100" class="form-control w-100" autocomplete="off" ></td>
                    <td align="center" >
                        <button disabled class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></button>
                        <button disabled class="btn remove" title="Delete" data-toggle="tooltip" {{isset($n6) && $n6 ==1?'disabled':''}}><i class="fa fa-trash" ></i></button>
                    </td>
                </tr>
                @php $n6++; @endphp
                @endforeach
                @else
                <tr  class="participantRow6">
                    <td><input disabled type="text" name="EXTRACUR_NAME_0" id ="EXTRACUR_NAME_0"  maxlength="100" class="form-control w-100" autocomplete="off" ></td>
                    <td><input disabled type="text" name="EXTRACUR_PERIOD_0" id ="EXTRACUR_PERIOD_0"  maxlength="50" class="form-control w-100" autocomplete="off" ></td>
                    <td><input disabled type="text" name="EXTRACUR_LEVELS_0" id ="EXTRACUR_LEVELS_0"  maxlength="50" class="form-control w-100" autocomplete="off" ></td>
                    <td><input disabled type="text" name="EXTRACUR_ACHIEVEMENT_0" id ="EXTRACUR_ACHIEVEMENT_0"  maxlength="100" class="form-control w-100" autocomplete="off" ></td>
                    <td align="center" >
                        <button disabled class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></button>
                        <button class="btn remove" title="Delete" data-toggle="tooltip" disabled><i class="fa fa-trash" ></i></button>
                    </td>
                </tr>
                @endif
              </tbody>
            </table>
          </div>

        </div>

        


        <div id="tab7" class="tab-pane fade">
          <div class="table-wrapper-scroll-x" style="margin-top:10px;">

            
            <div class="row">
              <div class="col-lg-1"><p>PAN NO</p></div>
              <div class="col-lg-2">
                  <input disabled type="text" name="PANNO" id="PANNO" value="{{isset($objTab7->PANNO) && $objTab7->PANNO !=''?$objTab7->PANNO:''}}" class="form-control" autocomplete="off"  maxlength="20" >
              </div>

              <div class="col-lg-1"><p>AADHAAR NO</p></div>
              <div class="col-lg-2">
                <input disabled type="text" name="AADHARNO" id="AADHARNO" value="{{isset($objTab7->AADHARNO) && $objTab7->AADHARNO !=''?$objTab7->AADHARNO:''}}" class="form-control" autocomplete="off"  maxlength="20" >
              </div>

              <div class="col-lg-1"><p>Election I Card No</p></div>
              <div class="col-lg-2">
                <input disabled type="text" name="ELECTIONCNO" id="ELECTIONCNO" value="{{isset($objTab7->ELECTIONCNO) && $objTab7->ELECTIONCNO !=''?$objTab7->ELECTIONCNO:''}}" class="form-control" autocomplete="off"  maxlength="20" >
              </div>

              <div class="col-lg-1"><p>Place of Issue</p></div>
              <div class="col-lg-2">
                <input disabled type="text" name="ELECTIONCPOI" id="ELECTIONCPOI" value="{{isset($objTab7->ELECTIONCPOI) && $objTab7->ELECTIONCPOI !=''?$objTab7->ELECTIONCPOI:''}}" class="form-control" autocomplete="off"  maxlength="50" >
              </div>
            </div>

            <div class="row">
              <div class="col-lg-1"><p>Driving License No</p></div>
              <div class="col-lg-2">
                  <input disabled type="text" name="DLNO" id="DLNO" value="{{isset($objTab7->DLNO) && $objTab7->DLNO !=''?$objTab7->DLNO:''}}" class="form-control" autocomplete="off"  maxlength="20" >
              </div>

              <div class="col-lg-1"><p>Place of Issue</p></div>
              <div class="col-lg-2">
                <input disabled type="text" name="DLPOI" id="DLPOI" value="{{isset($objTab7->DLPOI) && $objTab7->DLPOI !=''?$objTab7->DLPOI:''}}" class="form-control" autocomplete="off"  maxlength="50" >
              </div>

              <div class="col-lg-1"><p>Valid Upto</p></div>
              <div class="col-lg-2">
                <input disabled type="date" name="DLVALIDUPTO" id="DLVALIDUPTO" value="{{isset($objTab7->DLVALIDUPTO) && $objTab7->DLVALIDUPTO !=''?date('Y-m-d',strtotime($objTab7->DLVALIDUPTO)):''}}" class="form-control"  autocomplete="off"  >
              </div>
            </div>

            <div class="row">
              <div class="col-lg-1"><p>Passport No</p></div>
              <div class="col-lg-2">
                  <input disabled type="text" name="PASSPORTNO" id="PASSPORTNO" value="{{isset($objTab7->PASSPORTNO) && $objTab7->PASSPORTNO !=''?$objTab7->PASSPORTNO:''}}" class="form-control" autocomplete="off" maxlength="20" >
              </div>

              <div class="col-lg-1"><p>Place of Issue</p></div>
              <div class="col-lg-2"> 
                <input disabled type="text" name="PASSPORTPOI" id="PASSPORTPOI" value="{{isset($objTab7->PASSPORTPOI) && $objTab7->PASSPORTPOI !=''?$objTab7->PASSPORTPOI:''}}" class="form-control" autocomplete="off"  maxlength="50" >
              </div>

              <div class="col-lg-1"><p>Valid Upto</p></div>
              <div class="col-lg-2">
                <input disabled type="date" name="PASSPORTVUPTO" id="PASSPORTVUPTO" value="{{isset($objTab7->PASSPORTVUPTO) && $objTab7->PASSPORTVUPTO !=''?date('Y-m-d',strtotime($objTab7->PASSPORTVUPTO)):''}}" class="form-control"  autocomplete="off"  >
              </div>
            </div>

            <div class="row">
              <div class="col-lg-1"><p>PF No / UAN No</p></div>
              <div class="col-lg-2">
                  <input disabled type="text" name="PFNO" id="PFNO" value="{{isset($objTab7->PFNO) && $objTab7->PFNO !=''?$objTab7->PFNO:''}}" class="form-control" autocomplete="off"  maxlength="30" >
              </div>

              <div class="col-lg-1"><p>ESI NO</p></div>
              <div class="col-lg-2">
                  <input disabled type="text" name="ESINO" id="ESINO" value="{{isset($objTab7->ESINO) && $objTab7->ESINO !=''?$objTab7->ESINO:''}}" class="form-control" autocomplete="off" maxlength="30" >
              </div>

              <div class="col-lg-1"><p>Membership Name</p></div>
              <div class="col-lg-2">
                  <input disabled type="text" name="MSHIPNAME" id="MSHIPNAME" value="{{isset($objTab7->MSHIPNAME) && $objTab7->MSHIPNAME !=''?$objTab7->MSHIPNAME:''}}" class="form-control" autocomplete="off"  maxlength="50" >
              </div>

              <div class="col-lg-1"><p>No</p></div>
              <div class="col-lg-2">
                  <input disabled type="text" name="MSHIPNO" id="MSHIPNO" value="{{isset($objTab7->MSHIPNO) && $objTab7->MSHIPNO !=''?$objTab7->MSHIPNO:''}}" class="form-control"  autocomplete="off" maxlength="20" >
              </div>
            </div>

            <div class="row">
              <div class="col-lg-1"><p>Contractor Name</p></div>
              <div class="col-lg-2">
                  <input disabled type="text" name="CONTRACTRNAME" id="CONTRACTRNAME" value="{{isset($objTab7->CONTRACTRNAME) && $objTab7->CONTRACTRNAME !=''?$objTab7->CONTRACTRNAME:''}}" class="form-control" autocomplete="off" maxlength="100" >
              </div>

              <div class="col-lg-1"><p>Nominee Name</p></div>
              <div class="col-lg-2">
                  <input disabled type="text" name="NOMINEENAME" id="NOMINEENAME" value="{{isset($objTab7->NOMINEENAME) && $objTab7->NOMINEENAME !=''?$objTab7->NOMINEENAME:''}}" class="form-control" autocomplete="off" maxlength="100" >
              </div>

              <div class="col-lg-1"><p>Relationship</p></div>
              <div class="col-lg-2">
                <select disabled name="RSID_REF" id="RSID_REF" class="form-control" >
                  <option value="" >Select</option>
                  @foreach ($getRelationShip as $val)
                  <option {{isset($objTab7->RSID_REF) && $objTab7->RSID_REF == $val-> FID?'selected="selected"':''}} value="{{ $val-> FID }}">{{ $val-> FNAME }}</option>
                  @endforeach
                </select>

              </div>

              <div class="col-lg-1"><p>Bank Name</p></div>
              <div class="col-lg-2">
                  <input disabled type="text" name="BANK" id="BANK" value="{{isset($objTab7->BANK) && $objTab7->BANK !=''?$objTab7->BANK:''}}" class="form-control" autocomplete="off"  maxlength="100" >
              </div>
            </div>

            <div class="row">
              <div class="col-lg-1"><p>IFSC</p></div>
              <div class="col-lg-2">
                  <input disabled type="text" name="IFSC" id="IFSC" value="{{isset($objTab7->IFSC) && $objTab7->IFSC !=''?$objTab7->IFSC:''}}" class="form-control" autocomplete="off"  maxlength="30" >
              </div>

              <div class="col-lg-1"><p>Branch</p></div>
              <div class="col-lg-2">
                  <input disabled type="text" name="BRANCH" id="BRANCH" value="{{isset($objTab7->BRANCH) && $objTab7->BRANCH !=''?$objTab7->BRANCH:''}}" class="form-control" autocomplete="off"  maxlength="50" >
              </div>

              <div class="col-lg-1"><p>Account Type</p></div>
              <div class="col-lg-2">
                 
                  <select disabled name="ACTYPE" id="ACTYPE" class="form-control"  >
                    <option value="" selected >Select</option>
                    <option {{isset($objTab7->ACTYPE) && $objTab7->ACTYPE == 'SAVING ACCOUNT'?'selected="selected"':''}}  value='SAVING ACCOUNT'>SAVING ACCOUNT</option>
                    <option {{isset($objTab7->ACTYPE) && $objTab7->ACTYPE == 'CURRENT ACCOUNT'?'selected="selected"':''}} value='CURRENT ACCOUNT'>CURRENT ACCOUNT</option>
                    <option {{isset($objTab7->ACTYPE) && $objTab7->ACTYPE == 'OD'?'selected="selected"':''}} value='OD'>OD</option>
                    <option {{isset($objTab7->ACTYPE) && $objTab7->ACTYPE == 'OTHERS'?'selected="selected"':''}} value='OTHERS'>OTHERS</option>
                  </select>
              </div>

              <div class="col-lg-1"><p>Account No</p></div>
              <div class="col-lg-2">
                  <input disabled type="text" name="ACNO" id="ACNO" value="{{isset($objTab7->ACNO) && $objTab7->ACNO !=''?$objTab7->ACNO:''}}" class="form-control" autocomplete="off"  maxlength="30" onkeypress="return isNumberKey(event,this)" >
              </div>
            </div>

            <div class="row">
              <div class="col-lg-1"><p>Branch Code</p></div>
              <div class="col-lg-2">
                  <input disabled type="text" name="BRANCHCODE" id="BRANCHCODE" value="{{isset($objTab7->BRANCHCODE) && $objTab7->BRANCHCODE !=''?$objTab7->BRANCHCODE:''}}" class="form-control" autocomplete="off"  maxlength="30" >
              </div>
            </div>


          </div>
        </div>

        <div id="tab8" class="tab-pane fade">
          <div class="table-wrapper-scroll-x" style="margin-top:10px;">

            
            <div class="row">
              <div class="col-lg-2"><p>Landline No</p></div>
              <div class="col-lg-2">
                  <input disabled type="text" name="LLNO" id="LLNO" value="{{isset($objTab8->LLNO) && $objTab8->LLNO !=''?$objTab8->LLNO:''}}" class="form-control" autocomplete="off"  maxlength="20" onkeypress="return isNumberKey(event,this)" >
              </div>

              <div class="col-lg-2"><p>Cell No 1</p></div>
              <div class="col-lg-2">
                <input disabled type="text" name="MONO1" id="MONO1" value="{{isset($objTab8->MONO1) && $objTab8->MONO1 !=''?$objTab8->MONO1:''}}"   class="form-control" autocomplete="off"  maxlength="20" onkeypress="return isNumberKey(event,this)" >
              </div>

              <div class="col-lg-2"><p>Cell No 2</p></div>
              <div class="col-lg-2">
                <input disabled type="text" name="MONO2" id="MONO2" value="{{isset($objTab8->MONO2) && $objTab8->MONO2 !=''?$objTab8->MONO2:''}}"  class="form-control" autocomplete="off"  maxlength="20" onkeypress="return isNumberKey(event,this)" >
              </div>

             
            </div>

            <div class="row">
              <div class="col-lg-2"><p>Email ID</p></div>
              <div class="col-lg-2">
                <input disabled type="text" name="EMAIL" id="EMAIL" value="{{isset($objTab8->EMAIL) && $objTab8->EMAIL !=''?$objTab8->EMAIL:''}}"  class="form-control" autocomplete="off"  maxlength="50" >
              </div>

              <div class="col-lg-2"><p>Personal Email ID</p></div>
              <div class="col-lg-2">
                  <input disabled type="text" name="PEMAIL" id="PEMAIL" value="{{isset($objTab8->PEMAIL) && $objTab8->PEMAIL !=''?$objTab8->PEMAIL:''}}"  class="form-control"  autocomplete="off" maxlength="50" >
              </div>

              <div class="col-lg-2"><p>Emergency Contact Person</p></div>
              <div class="col-lg-2">
                <input disabled type="text" name="EMERGCPNAME" id="EMERGCPNAME" value="{{isset($objTab8->EMERGCPNAME) && $objTab8->EMERGCPNAME !=''?$objTab8->EMERGCPNAME:''}}"  class="form-control" autocomplete="off" maxlength="50" >
              </div>

              
            </div>

            <div class="row">
            <div class="col-lg-2"><p>Cell No</p></div>
              <div class="col-lg-2">
                <input disabled type="text" name="EMERGCPMONO" id="EMERGCPMONO" value="{{isset($objTab8->EMERGCPMONO) && $objTab8->EMERGCPMONO !=''?$objTab8->EMERGCPMONO:''}}"  class="form-control" autocomplete="off" maxlength="20" onkeypress="return isNumberKey(event,this)"   >
              </div>
            </div>

          </div>
        </div>



        <div id="tab9" class="tab-pane fade">
          <div class="table-wrapper-scroll-x" style="margin-top:10px;">

            
            <div class="row">
              <div class="col-lg-1"><p>Current Disease 1</p></div>
              <div class="col-lg-5">
                  <input disabled type="text" name="CRDISEASE1" id="CRDISEASE1" value="{{isset($objTab9->CRDISEASE1) && $objTab9->CRDISEASE1 !=''?$objTab9->CRDISEASE1:''}}" class="form-control" autocomplete="off" maxlength="20" >
              </div>

              <div class="col-lg-1"><p>Current Disease 2</p></div>
              <div class="col-lg-5">
                <input disabled type="text" name="CRDISEASE2" id="CRDISEASE2" value="{{isset($objTab9->CRDISEASE2) && $objTab9->CRDISEASE2 !=''?$objTab9->CRDISEASE2:''}}" class="form-control" autocomplete="off" maxlength="20" >
              </div>

              
            </div>

            <div class="row">
              <div class="col-lg-1"><p>Current Disease 3</p></div>
              <div class="col-lg-5">
                <input disabled type="text" name="CRDISEASE3" id="CRDISEASE3" value="{{isset($objTab9->CRDISEASE3) && $objTab9->CRDISEASE3 !=''?$objTab9->CRDISEASE3:''}}" class="form-control" autocomplete="off" maxlength="20" >
              </div>

              <div class="col-lg-1"><p>Allergy</p></div>
              <div class="col-lg-2">
                <input disabled type="text" name="ALLERGY" id="ALLERGY" value="{{isset($objTab9->ALLERGY) && $objTab9->ALLERGY !=''?$objTab9->ALLERGY:''}}" class="form-control" autocomplete="off" maxlength="20" >
              </div>

              </div>

              <div class="row">

              <div class="col-lg-1"><p>Height</p></div>
              <div class="col-lg-2">
                  <input disabled type="text" name="HEIGHT" id="HEIGHT" value="{{isset($objTab9->HEIGHT) && $objTab9->HEIGHT !=''?$objTab9->HEIGHT:''}}" class="form-control" autocomplete="off"  maxlength="20" >
              </div>

              <div class="col-lg-1"><p>Weight</p></div>
              <div class="col-lg-2">
                <input disabled type="text" name="WEIGHT" id="WEIGHT" value="{{isset($objTab9->WEIGHT) && $objTab9->WEIGHT !=''?$objTab9->WEIGHT:''}}" class="form-control" autocomplete="off"  maxlength="20" >
              </div>

              <div class="col-lg-1"><p>Color</p></div>
              <div class="col-lg-2">
                <input disabled type="text" name="COLOR" id="COLOR" value="{{isset($objTab9->COLOR) && $objTab9->COLOR !=''?$objTab9->COLOR:''}}" class="form-control" autocomplete="off" maxlength="15"   >
              </div>

              <div class="col-lg-1"><p>Religion</p></div>
              <div class="col-lg-2">
                <input disabled type="text" name="RELIGION" id="RELIGION" value="{{isset($objTab9->RELIGION) && $objTab9->RELIGION !=''?$objTab9->RELIGION:''}}" class="form-control" autocomplete="off" maxlength="15"   >
              </div>

              </div>

            <div class="row">

              
           
              <div class="col-lg-1"><p>Nationality</p></div>
              <div class="col-lg-2">
                  <input disabled type="text" name="NANTIONALITY" id="NANTIONALITY"  value="{{isset($objTab9->NANTIONALITY) && $objTab9->NANTIONALITY !=''?$objTab9->NANTIONALITY:''}}"class="form-control" autocomplete="off"  maxlength="20" >
              </div>

              <div class="col-lg-1"><p>Hobbies</p></div>
              <div class="col-lg-2">
                <input disabled type="text" name="HOBBIES" id="HOBBIES" value="{{isset($objTab9->HOBBIES) && $objTab9->HOBBIES !=''?$objTab9->HOBBIES:''}}" class="form-control" autocomplete="off" maxlength="30" >
              </div>

              <div class="col-lg-1"><p>Vegetarian</p></div>
              <div class="col-lg-2">
                <input disabled type="radio" name="VEGETARIAN" {{isset($objTab9->VEGETARIAN) && $objTab9->VEGETARIAN =='1'?'checked':''}}  value="1"  >
              </div>

              <div class="col-lg-1"><p>Non-Veg</p></div>
              <div class="col-lg-2">
              <input disabled type="radio" name="VEGETARIAN" {{isset($objTab9->VEGETARIAN) && $objTab9->VEGETARIAN =='0'?'checked':''}}    value="0"   >
              </div>

              </div>

    
          </div>
        </div>

  </div>
</div>


    </div>
  </form>
</div>
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


<!-- =================================CURRENT DETAILS=================================   -->

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

<div id="distidref_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='distidref_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>District</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="dist_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th>Code</th>
            <th>Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td ><input type="text" id="dist_codesearch" onkeyup="searchDistCode()"></td>
          <td><input type="text" id="dist_namesearch" onkeyup="searchDistName()"></td>
        </tr>
        </tbody>
      </table>

      <table id="dist_tab2" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody id="dist_body">
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<!-- =================================PERMANENT DETAILS=================================   -->

<div id="ctryidref_popup1" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='ctryidref_close1' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Country</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="country_tab11" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th>Code</th>
            <th>Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td ><input type="text" id="country_codesearch1" onkeyup="searchCountryCode1()"></td>
          <td><input type="text" id="country_namesearch1" onkeyup="searchCountryName1()"></td>
        </tr>
        </tbody>
      </table>


      <table id="country_tab21" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody id="country_body1">
        @foreach ($objCountryList as $index=>$CountryList)
        <tr id="ctryidref1_{{ $CountryList->CTRYID }}" class="cls_ctryidref1">
          <td width="50%">{{ $CountryList->CTRYCODE }}
          <input type="hidden" id="txtctryidref1_{{ $CountryList->CTRYID }}" data-desc="{{ $CountryList->CTRYCODE }} - {{ $CountryList->NAME }}" value="{{ $CountryList-> CTRYID }}"/>
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

<div id="stidref_popup1" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='stidref_close1' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>State</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="state_tab11" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th>Code</th>
            <th>Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td ><input type="text" id="state_codesearch1" onkeyup="searchStateCode1()"></td>
          <td><input type="text" id="state_namesearch1" onkeyup="searchStateName1()"></td>
        </tr>
        </tbody>
      </table>

      <table id="state_tab21" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody id="state_body1">
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<div id="cityidref_popup1" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='cityidref_close1' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>City</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="city_tab11" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th>Code</th>
            <th>Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td ><input type="text" id="city_codesearch1" onkeyup="searchCityCode1()"></td>
          <td><input type="text" id="city_namesearch1" onkeyup="searchCityName1()"></td>
        </tr>
        </tbody>
      </table>

      <table id="city_tab21" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody id="city_body1">
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<div id="distidref_popup1" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='distidref_close1' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>District</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="dist_tab11" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th>Code</th>
            <th>Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td ><input type="text" id="dist_codesearch1" onkeyup="searchDistCode1()"></td>
          <td><input type="text" id="dist_namesearch1" onkeyup="searchDistName1()"></td>
        </tr>
        </tbody>
      </table>

      <table id="dist_tab21" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody id="dist_body1">
        </tbody>
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
//=================================POPUP ORDER BY ================================= 
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

//=================================CURRENT COUNTRY =================================
let country_tab1 = "#country_tab1";
let country_tab2 = "#country_tab2";
let country_headers = document.querySelectorAll(country_tab1 + " th");

country_headers.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(country_tab2, ".cls_ctryidref", "td:nth-child(" + (i + 1) + ")");
  });
});

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

$('.cls_ctryidref').dblclick(function(){
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

function searchCountryName() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("country_namesearch");
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

function getCountryWiseState(CTRYID_REF,dataStatus){
    $("#state_body").html('');
		$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
		
    $.ajax({
        url:'{{route("master",[$FormId,"getCountryWiseState"])}}',
        type:'POST',
        data:{CTRYID_REF:CTRYID_REF},
        success:function(data) {
        if(dataStatus !="edit"){
          $("#STID_REF_POPUP").val('');
          $("#STID_REF").val('');
			    $("#CITYID_REF_POPUP").val('');
          $("#CITYID_REF").val('');
          $("#city_body").html(''); 
          $("#DISTID_REF_POPUP").val('');
          $("#DISTID_REF").val('');
          $("#dist_body").html(''); 
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


//=================================CURRENT STATE =================================

let state_tab1 = "#state_tab1";
let state_tab2 = "#state_tab2";
let state_headers = document.querySelectorAll(state_tab1 + " th");

state_headers.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(state_tab2, ".cls_stidref", "td:nth-child(" + (i + 1) + ")");
  });
});

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
  $('.cls_stidref').dblclick(function(){

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

function searchStateName() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("state_namesearch");
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

function getStateWiseCity(CTRYID_REF,STID_REF,dataStatus){
    $("#city_body").html('');
		$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
		
    $.ajax({
        url:'{{route("master",[$FormId,"getStateWiseCity"])}}',
        type:'POST',
        data:{CTRYID_REF:CTRYID_REF,STID_REF:STID_REF},
        success:function(data) {
          if(dataStatus !="edit"){
            $("#CITYID_REF_POPUP").val('');
            $("#CITYID_REF").val('');

            $("#DISTID_REF_POPUP").val('');
            $("#DISTID_REF").val('');
            $("#dist_body").html('');
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


//=================================CURRENT CITY =================================

let city_tab1 = "#city_tab1";
let city_tab2 = "#city_tab2";
let city_headers = document.querySelectorAll(city_tab1 + " th");

city_headers.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(city_tab2, ".cls_cityidref", "td:nth-child(" + (i + 1) + ")");
  });
});

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
	$('.cls_cityidref').dblclick(function(){

		var id = $(this).attr('id');
		var txtval =    $("#txt"+id+"").val();
		var texdesc =   $("#txt"+id+"").data("desc")

		$("#CITYID_REF_POPUP").val(texdesc);
    $("#CITYID_REF").val(txtval);
    
    var CTRYID_REF	=	$("#CTRYID_REF").val();
    var STID_REF	=	$("#STID_REF").val();

	  getCityWiseDist(CTRYID_REF,STID_REF,txtval,'');
	
    $("#CITYID_REF_POPUP").blur(); 
	  $("#DISTID_REF_POPUP").focus(); 

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

function searchCityName() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("city_namesearch");
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

function getCityWiseDist(CTRYID_REF,STID_REF,CITYID_REF,dataStatus){
    $("#dist_body").html('');
		$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
		
    $.ajax({
        url:'{{route("master",[$FormId,"getCityWiseDist"])}}',
        type:'POST',
        data:{CTRYID_REF:CTRYID_REF,STID_REF:STID_REF,CITYID_REF:CITYID_REF},
        success:function(data) {
          if(dataStatus !="edit"){
            $("#DISTID_REF_POPUP").val('');
            $("#DISTID_REF").val('');  
          }          
            $("#dist_body").html(data);
            bindDistEvents(); 
			
        },
        error:function(data){
          console.log("Error: Something went wrong.");
          $("#dist_body").html('');
          
        },
    });	
  }

//=================================CURRENT DISTRICT =================================

let dist_tab1 = "#dist_tab1";
  let dist_tab2 = "#dist_tab2";
  let dist_headers = document.querySelectorAll(dist_tab1 + " th");

  dist_headers.forEach(function(element, i) {
    element.addEventListener("click", function() {
      w3.sortHTML(dist_tab2, ".cls_distidref", "td:nth-child(" + (i + 1) + ")");
    });
  });

$("#DISTID_REF_POPUP").on("click",function(event){ 
  var CTRYID_REF  = $("#CTRYID_REF").val();
  var STID_REF  = $("#STID_REF").val();
  var CITYID_REF  = $("#CITYID_REF").val();
  getCityWiseDist(CTRYID_REF,STID_REF,CITYID_REF,'edit'); 
  $("#distidref_popup").show();
});

$("#DISTID_REF_POPUP").keyup(function(event){
  if(event.keyCode==13){
    var CTRYID_REF  = $("#CTRYID_REF").val();
    var STID_REF  = $("#STID_REF").val();
    var CITYID_REF  = $("#CITYID_REF").val();
    getCityWiseDist(CTRYID_REF,STID_REF,CITYID_REF,'edit'); 
    $("#distidref_popup").show();
  }
});

$("#distidref_close").on("click",function(event){ 
  $("#distidref_popup").hide();
});

function bindDistEvents(){
	$('.cls_distidref').dblclick(function(){

		var id = $(this).attr('id');
		var txtval =    $("#txt"+id+"").val();
		var texdesc =   $("#txt"+id+"").data("desc")

		$("#DISTID_REF_POPUP").val(texdesc);
		$("#DISTID_REF").val(txtval);

		$("#distidref_popup").hide();
		
		searchDistCode();
		event.preventDefault();
	});
}


function searchDistCode() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("dist_codesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("dist_tab2");
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

function searchDistName() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("dist_namesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("dist_tab2");
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


//=================================PERMANENT COUNTRY =================================
let country_tab11 = "#country_tab11";
let country_tab21 = "#country_tab21";
let country_headers1 = document.querySelectorAll(country_tab11 + " th");

country_headers1.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(country_tab21, ".cls_ctryidref", "td:nth-child(" + (i + 1) + ")");
  });
});

$("#CTRYID_REF_POPUP1").on("click",function(event){ 
  $("#ctryidref_popup1").show();
});

$("#CTRYID_REF_POPUP1").keyup(function(event){
  if(event.keyCode==13){
    $("#ctryidref_popup1").show();
  }
});

$("#ctryidref_close1").on("click",function(event){ 
  $("#ctryidref_popup1").hide();
});

$('.cls_ctryidref1').dblclick(function(){
  var id = $(this).attr('id');
  var txtval =    $("#txt"+id+"").val();
  var texdesc =   $("#txt"+id+"").data("desc")

  $("#CTRYID_REF_POPUP1").val(texdesc);
  $("#CTRYID_REF1").val(txtval);

  getCountryWiseState1(txtval,'');
  
  $("#CTRYID_REF_POPUP1").blur(); 
  $("#STID_REF_POPUP1").focus(); 
  
  $("#ctryidref_popup1").hide();
  searchCountryCode1();
  event.preventDefault();
});

function searchCountryCode1() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("country_codesearch1");
  filter = input.value.toUpperCase();
  table = document.getElementById("country_tab21");
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

function searchCountryName1() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("country_namesearch1");
      filter = input.value.toUpperCase();
      table = document.getElementById("country_tab21");
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

function getCountryWiseState1(CTRYID_REF,dataStatus){
    $("#state_body").html('');
		$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
		
    $.ajax({
        url:'{{route("master",[$FormId,"getCountryWiseState"])}}',
        type:'POST',
        data:{CTRYID_REF:CTRYID_REF},
        success:function(data) {
        if(dataStatus !="edit"){
          $("#STID_REF_POPUP1").val('');
          $("#STID_REF1").val('');
			    $("#CITYID_REF_POPUP1").val('');
          $("#CITYID_REF1").val('');
          $("#city_body1").html(''); 
          $("#DISTID_REF_POPUP1").val('');
          $("#DISTID_REF1").val('');
          $("#dist_body1").html(''); 
        }
          
          $("#state_body1").html(data);
          bindStateEvents1(); 

        },
        error:function(data){
          console.log("Error: Something went wrong.");
          $("#state_body1").html('');
          
        },
    });	
  }

//=================================PERMANENT STATE =================================

let state_tab11 = "#state_tab11";
let state_tab21 = "#state_tab21";
let state_headers1 = document.querySelectorAll(state_tab11 + " th");

state_headers1.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(state_tab21, ".cls_stidref", "td:nth-child(" + (i + 1) + ")");
  });
});

$("#STID_REF_POPUP1").on("click",function(event){ 
  var CTRYID_REF  = $("#CTRYID_REF1").val();
  getCountryWiseState1(CTRYID_REF,'edit');
  $("#stidref_popup1").show();
});

$("#STID_REF_POPUP1").keyup(function(event){
  if(event.keyCode==13){
    var CTRYID_REF  = $("#CTRYID_REF1").val();
    getCountryWiseState1(CTRYID_REF,'edit');
    $("#stidref_popup1").show();
  }
});

$("#stidref_close1").on("click",function(event){ 
  $("#stidref_popup1").hide();
});

function bindStateEvents1(){
  $('.cls_stidref').dblclick(function(){

    var id = $(this).attr('id');
    var txtval =    $("#txt"+id+"").val();
    var texdesc =   $("#txt"+id+"").data("desc")

    $("#STID_REF_POPUP1").val(texdesc);
    $("#STID_REF1").val(txtval);
	
	var CTRYID_REF	=	$("#CTRYID_REF1").val();
	
	getStateWiseCity1(CTRYID_REF,txtval,'');
	
	$("#STID_REF_POPUP1").blur(); 
	$("#CITYID_REF_POPUP1").focus(); 
	
    $("#stidref_popup1").hide();
    searchStateCode1();
    event.preventDefault();
  });
}

function searchStateCode1() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("state_codesearch1");
  filter = input.value.toUpperCase();
  table = document.getElementById("state_tab21");
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

function searchStateName1() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("state_namesearch1");
      filter = input.value.toUpperCase();
      table = document.getElementById("state_tab21");
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

function getStateWiseCity1(CTRYID_REF,STID_REF,dataStatus){
    $("#city_body1").html('');
		$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
		
    $.ajax({
        url:'{{route("master",[$FormId,"getStateWiseCity"])}}',
        type:'POST',
        data:{CTRYID_REF:CTRYID_REF,STID_REF:STID_REF},
        success:function(data) {
          if(dataStatus !="edit"){
            $("#CITYID_REF_POPUP1").val('');
            $("#CITYID_REF1").val('');

            $("#DISTID_REF_POPUP1").val('');
            $("#DISTID_REF1").val('');
            $("#dist_body1").html('');
          }
            $("#city_body1").html(data);
            bindCityEvents1(); 
			
        },
        error:function(data){
          console.log("Error: Something went wrong.");
          $("#city_body1").html('');
          
        },
    });	
  }


//=================================PERMANENT CITY =================================

let city_tab11 = "#city_tab11";
let city_tab21 = "#city_tab21";
let city_headers1 = document.querySelectorAll(city_tab11 + " th");

city_headers1.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(city_tab21, ".cls_cityidref", "td:nth-child(" + (i + 1) + ")");
  });
});

$("#CITYID_REF_POPUP1").on("click",function(event){ 
  var CTRYID_REF  = $("#CTRYID_REF1").val();
  var STID_REF  = $("#STID_REF1").val();
  getStateWiseCity1(CTRYID_REF,STID_REF,'edit');
  $("#cityidref_popup1").show();
});

$("#CITYID_REF_POPUP1").keyup(function(event){
  if(event.keyCode==13){
    var CTRYID_REF  = $("#CTRYID_REF1").val();
    var STID_REF  = $("#STID_REF1").val();
    getStateWiseCity1(CTRYID_REF,STID_REF,'edit');
    $("#cityidref_popup1").show();
  }
});

$("#cityidref_close1").on("click",function(event){ 
  $("#cityidref_popup1").hide();
});

function bindCityEvents1(){
	$('.cls_cityidref').dblclick(function(){

		var id = $(this).attr('id');
		var txtval =    $("#txt"+id+"").val();
		var texdesc =   $("#txt"+id+"").data("desc")

		$("#CITYID_REF_POPUP1").val(texdesc);
    $("#CITYID_REF1").val(txtval);
    
    var CTRYID_REF	=	$("#CTRYID_REF1").val();
    var STID_REF	=	$("#STID_REF1").val();

	  getCityWiseDist1(CTRYID_REF,STID_REF,txtval,'');
	
    $("#CITYID_REF_POPUP1").blur(); 
	  $("#DISTID_REF_POPUP1").focus(); 

		$("#cityidref_popup1").hide();
		
		searchCityCode1();
		event.preventDefault();
	});
}


function searchCityCode1() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("city_codesearch1");
  filter = input.value.toUpperCase();
  table = document.getElementById("city_tab21");
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

function searchCityName1() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("city_namesearch1");
      filter = input.value.toUpperCase();
      table = document.getElementById("city_tab21");
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

function getCityWiseDist1(CTRYID_REF,STID_REF,CITYID_REF,dataStatus){
    $("#dist_body1").html('');
		$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
		
    $.ajax({
        url:'{{route("master",[$FormId,"getCityWiseDist"])}}',
        type:'POST',
        data:{CTRYID_REF:CTRYID_REF,STID_REF:STID_REF,CITYID_REF:CITYID_REF},
        success:function(data) {
        if(dataStatus !="edit"){
            $("#DISTID_REF_POPUP1").val('');
            $("#DISTID_REF1").val('');   
        }         
            $("#dist_body1").html(data);
            bindDistEvents1(); 
			
        },
        error:function(data){
          console.log("Error: Something went wrong.");
          $("#dist_body1").html('');
          
        },
    });	
  }

//=================================CURRENT DISTRICT =================================

let dist_tab11 = "#dist_tab11";
  let dist_tab21 = "#dist_tab21";
  let dist_headers1 = document.querySelectorAll(dist_tab1 + " th");

  dist_headers1.forEach(function(element, i) {
    element.addEventListener("click", function() {
      w3.sortHTML(dist_tab2, ".cls_distidref1", "td:nth-child(" + (i + 1) + ")");
    });
  });

$("#DISTID_REF_POPUP1").on("click",function(event){ 
  var CTRYID_REF  = $("#CTRYID_REF1").val();
  var STID_REF  = $("#STID_REF1").val();
  var CITYID_REF  = $("#CITYID_REF1").val();
  getCityWiseDist1(CTRYID_REF,STID_REF,CITYID_REF,'edit'); 
  $("#distidref_popup1").show();
});

$("#DISTID_REF_POPUP1").keyup(function(event){
  if(event.keyCode==13){
    var CTRYID_REF  = $("#CTRYID_REF1").val();
    var STID_REF  = $("#STID_REF1").val();
    var CITYID_REF  = $("#CITYID_REF1").val();
    getCityWiseDist1(CTRYID_REF,STID_REF,CITYID_REF,'edit');
    $("#distidref_popup1").show();
  }
});

$("#distidref_close1").on("click",function(event){ 
  $("#distidref_popup1").hide();
});

function bindDistEvents1(){
	$('.cls_distidref').dblclick(function(){

		var id = $(this).attr('id');
		var txtval =    $("#txt"+id+"").val();
		var texdesc =   $("#txt"+id+"").data("desc")

		$("#DISTID_REF_POPUP1").val(texdesc);
		$("#DISTID_REF1").val(txtval);

		$("#distidref_popup1").hide();
		
		searchDistCode1();
		event.preventDefault();
	});
}


function searchDistCode1() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("dist_codesearch1");
  filter = input.value.toUpperCase();
  table = document.getElementById("dist_tab21");
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

function searchDistName1() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("dist_namesearch1");
      filter = input.value.toUpperCase();
      table = document.getElementById("dist_tab21");
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

//======================================SAVE FUNCTION==============================

$('#btnAdd').on('click', function() {
    var viewURL = '{{route("master",[$FormId,"add"])}}';
    window.location.href=viewURL;
});

$('#btnExit').on('click', function() {
  var viewURL = '{{route('home')}}';
  window.location.href=viewURL;
});

var formResponseMst = $( "#frm_mst_edit" );
formResponseMst.validate();

$("#EMPCODE").blur(function(){
  $(this).val($.trim( $(this).val() ));
  $("#ERROR_EMPCODE").hide();
  validateSingleElemnet("EMPCODE");
      
});
$( "#EMPCODE" ).rules( "add", {
    required: true,
    nowhitespace: true,
   // StringNumberRegex: true,
    messages: {
        required: "Required field.",
    }
});

$("#CRESIDENCE").blur(function(){
  $(this).val($.trim( $(this).val() ));
  $("#ERROR_CRESIDENCE").hide();
  validateSingleElemnet("CRESIDENCE");
      
});
$( "#CRESIDENCE" ).rules( "add", {
    required: true,
    nowhitespace: true,
   // StringNumberRegex: true,
    messages: {
        required: "Required field.",
    }
});

$("#FNAME").blur(function(){
    $(this).val($.trim( $(this).val() ));
    $("#ERROR_FNAME").hide();
    validateSingleElemnet("FNAME");
});

$( "#FNAME" ).rules( "add", {
    required: true,
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

function validateSingleElemnet(element_id){
  var validator =$("#frm_mst_edit" ).validate();
  if(validator.element( "#"+element_id+"" )){
    //checkDuplicateCode();
  }
}

function checkDuplicateCode(){
  var getDataForm = $("#frm_mst_edit");
  var formData = getDataForm.serialize();
  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });
  $.ajax({
      url:'{{route("master",[$FormId,"codeduplicate"])}}',
      type:'POST',
      data:formData,
      success:function(data) {
          if(data.exists) {
              $(".text-danger").hide();
              showError('ERROR_EMPCODE',data.msg);
              $("#EMPCODE").focus();
          }                             
      },
      error:function(data){
        console.log("Error: Something went wrong.");
      },
  });
}

$("#YesBtn").click(function(){
    $("#alert").modal('hide');
    var customFnName = $("#YesBtn").data("funcname");
    window[customFnName]();
}); 

$("#btnSave" ).click(function() {
    var formReqData = $("#frm_mst_edit");
    if(formReqData.valid()){
      validateForm('fnSaveData');
    }
});

window.fnSaveData = function (){
    event.preventDefault();

    var formData = new FormData($("#frm_mst_edit")[0]);
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url:'{{route("master",[$FormId,"update"])}}',
        type:'POST',
        enctype: 'multipart/form-data',
        contentType: false,     
        cache: false,           
        processData:false, 
        data:formData,
        success:function(data) {
            
            if(data.errors) {
                $(".text-danger").hide();

                if(data.errors.EMPCODE){
                    showError('ERROR_EMPCODE',data.errors.EMPCODE);
                }
                if(data.errors.FNAME){
                    showError('ERROR_FNAME',data.errors.FNAME);
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
              $("#alert").modal('show');
              $("#OkBtn").focus();
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


$("#btnApprove" ).click(function() {
    var formReqData = $("#frm_mst_edit");
    if(formReqData.valid()){
      validateForm('fnApproveData');
    }
});

window.fnApproveData = function (){
    event.preventDefault();
    var formData = new FormData($("#frm_mst_edit")[0]);

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url:'{{route("mastermodify",[$FormId,"Approve"])}}',
        type:'POST',
        enctype: 'multipart/form-data',
        contentType: false,     
        cache: false,           
        processData:false, 
        data:formData,
        success:function(data) {
            
          if(data.errors) {
                $(".text-danger").hide();

                if(data.errors.EMPCODE){
                    showError('ERROR_EMPCODE',data.errors.EMPCODE);
                }
                if(data.errors.FNAME){
                    showError('ERROR_FNAME',data.errors.FNAME);
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
              $("#alert").modal('show');
              $("#OkBtn").focus();
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

};

$("#btnUndo").on("click", function(){
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
  window.location.reload();
}

$("#NoBtn").click(function(){
    $("#alert").modal('hide');
    $("#LABEL").focus();
});

$("#OkBtn").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn").hide();
    $(".text-danger").hide();
    window.location.href = '{{route("master",[$FormId,"index"]) }}';
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


//=================================ADD REMOVE TABLE ROW================================= 
$(document).ready(function(e) {

  $('#Row_Count2').val('<?php echo count($objTab2); ?>');
  $("#example2").on('click', '.add', function() {
    var $tr = $(this).closest('tbody');
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
	  var name = el.attr('name') || null;
	if(name){
	  var nameLength = name.split('_').pop();
	  var i = name.substr(name.length-nameLength.length);
	  var prefix1 = name.substr(0, (name.length-nameLength.length));
	  el.attr('name', prefix1+(+i+1));
	}
});

    $clone.find('input:text').val('');
    $clone.find('select').val('');
    $tr.closest('table').append($clone);
    var Row_Count2 = $('#Row_Count2').val();
    Row_Count2 = parseInt(Row_Count2)+1;
    $('#Row_Count2').val(Row_Count2);
    
    $clone.find('.remove').removeAttr('disabled'); 
    event.preventDefault();

});

 

$("#example2").on('click', '.remove', function() {

    var Row_Count2 = $('#Row_Count2').val();
    if (Row_Count2 > 1) {
        $(this).closest('.participantRow2').remove(); 
    } 
    if (Row_Count2 <= 1) { 
        $(document).find('.remove').prop('disabled', true);  
    }

    event.preventDefault();

});

//=================================TAB 3 ADD REMOVE =================================

$('#Row_Count3').val('<?php echo count($objTab3); ?>');
  $("#example3").on('click', '.add', function() {
    var $tr = $(this).closest('tbody');
    var allTrs = $tr.find('.participantRow3').last();
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
    $clone.find('select').val('');
    $tr.closest('table').append($clone);
    var Row_Count3 = $('#Row_Count3').val();
    Row_Count3 = parseInt(Row_Count3)+1;
    $('#Row_Count3').val(Row_Count3);
    
    $clone.find('.remove').removeAttr('disabled'); 
    event.preventDefault();

});

$("#example3").on('click', '.remove', function() {

    var Row_Count3 = $('#Row_Count3').val();
    if (Row_Count3 > 1) {
        $(this).closest('.participantRow3').remove(); 
    } 
    if (Row_Count3 <= 1) { 
        $(document).find('.remove').prop('disabled', true);  
    }

    event.preventDefault();

});


//=================================TAB 4 ADD REMOVE =================================

$('#Row_Count4').val('<?php echo count($objTab4); ?>');
  $("#example4").on('click', '.add', function() {
    var $tr = $(this).closest('tbody');
    var allTrs = $tr.find('.participantRow4').last();
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
    $clone.find('select').val('');
    $tr.closest('table').append($clone);
    var Row_Count4 = $('#Row_Count4').val();
    Row_Count4 = parseInt(Row_Count4)+1;
    $('#Row_Count4').val(Row_Count4);
    
    $clone.find('.remove').removeAttr('disabled'); 
    event.preventDefault();

});

$("#example4").on('click', '.remove', function() {

    var Row_Count4 = $('#Row_Count4').val();
    if (Row_Count4 > 1) {
        $(this).closest('.participantRow4').remove(); 
    } 
    if (Row_Count4 <= 1) { 
        $(document).find('.remove').prop('disabled', true);  
    }

    event.preventDefault();

});


//=================================TAB 5 ADD REMOVE =================================

$('#Row_Count5').val('<?php echo count($objTab5); ?>');
  $("#example5").on('click', '.add', function() {
    var $tr = $(this).closest('tbody');
    var allTrs = $tr.find('.participantRow5').last();
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
    $clone.find('select').val('');
    $tr.closest('table').append($clone);
    var Row_Count5 = $('#Row_Count5').val();
    Row_Count5 = parseInt(Row_Count5)+1;
    $('#Row_Count5').val(Row_Count5);
    
    $clone.find('.remove').removeAttr('disabled'); 
    event.preventDefault();

});

$("#example5").on('click', '.remove', function() {

    var Row_Count5 = $('#Row_Count5').val();
    if (Row_Count5 > 1) {
        $(this).closest('.participantRow5').remove(); 
    } 
    if (Row_Count5 <= 1) { 
        $(document).find('.remove').prop('disabled', true);  
    }

    event.preventDefault();

});

//=================================TAB 6 ADD REMOVE =================================

$('#Row_Count6').val('<?php echo count($objTab6); ?>');
  $("#example6").on('click', '.add', function() {
    var $tr = $(this).closest('tbody');
    var allTrs = $tr.find('.participantRow6').last();
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
    $clone.find('select').val('');
    $tr.closest('table').append($clone);
    var Row_Count6 = $('#Row_Count6').val();
    Row_Count6 = parseInt(Row_Count6)+1;
    $('#Row_Count6').val(Row_Count6);
    
    $clone.find('.remove').removeAttr('disabled'); 
    event.preventDefault();

});

$("#example6").on('click', '.remove', function() {

    var Row_Count6 = $('#Row_Count6').val();
    if (Row_Count6 > 1) {
        $(this).closest('.participantRow6').remove(); 
    } 
    if (Row_Count6 <= 1) { 
        $(document).find('.remove').prop('disabled', true);  
    }

    event.preventDefault();

});

});
//=================================VALIDATION================================= 

function validateForm(ActionType){

    var CRESIDENCE  = $("#CRESIDENCE").prop("checked");
    var CROWN       = $("#CROWN").prop("checked");
    var CRRENTED    = $("#CRRENTED").prop("checked");
    var CRADD1      = $("#CRADD1").val();

    var PRRESIDENCE  = $("#PRRESIDENCE").prop("checked");
    var PROWN       = $("#PROWN").prop("checked");
    var PRRENTED    = $("#PRRENTED").prop("checked");
    var PRADD1      = $("#PRADD1").val();


    if(CRESIDENCE == true && CROWN == false && CRRENTED == false){
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please Select Current Own / Parents Residence Or Rented');
        $("#alert").modal('show')
        $("#OkBtn1").focus();
    }
    else if(CRESIDENCE == true && CRADD1 ===""){
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please Select Current Residence Address.');
        $("#alert").modal('show')
        $("#OkBtn1").focus();
    }
    else if(PRRESIDENCE == true && PROWN == false && PRRENTED == false){
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please Select Permanant Own / Parents Residence Or Rented');
        $("#alert").modal('show')
        $("#OkBtn1").focus();
    }
    else if(PRRESIDENCE == true && PRADD1 ===""){
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please Select Permanant Residence Address.');
        $("#alert").modal('show')
        $("#OkBtn1").focus();
    }
    else{
      $("#alert").modal('show');
      $("#AlertMessage").text('Do you want to save to record.');
      $("#YesBtn").data("funcname",ActionType);
      $("#YesBtn").focus();
      $("#OkBtn").hide();
      highlighFocusBtn('activeYes');
    }
}

$(function () {
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

function getCParentRented(id){
  if(id ==="CROWN"){
    if($("#CROWN").prop("checked") ==true){
        $('#CROWN').prop('checked', true);
        $('#CRRENTED').prop('checked', false);
    }
    else{
      $('#CROWN').prop('checked', false);
      $('#CRRENTED').prop('checked', true);
    }
  }
  else{
    if($("#CRRENTED").prop("checked") ==true){
      $('#CRRENTED').prop('checked', true);
      $('#CROWN').prop('checked', false);
    }
    else{
      $('#CROWN').prop('checked', true);
      $('#CRRENTED').prop('checked', false);
    }
  }
}

function getPParentRented(id){
  if(id ==="PROWN"){
    if($("#PROWN").prop("checked") ==true){
        $('#PROWN').prop('checked', true);
        $('#PRRENTED').prop('checked', false);
    }
    else{
      $('#PROWN').prop('checked', false);
      $('#PRRENTED').prop('checked', true);
    }
  }
  else{
    if($("#PRRENTED").prop("checked") ==true){
      $('#PRRENTED').prop('checked', true);
      $('#PROWN').prop('checked', false);
    }
    else{
      $('#PROWN').prop('checked', true);
      $('#PRRENTED').prop('checked', false);
    }
  }
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
</script>
@endpush