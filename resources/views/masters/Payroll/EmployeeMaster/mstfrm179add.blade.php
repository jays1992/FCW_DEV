@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
  <div class="row">
      <div class="col-lg-2"><a href="{{route('master',[$FormId,'index'])}}" class="btn singlebt">Employee Master</a></div>
      <div class="col-lg-10 topnav-pd">
        <button class="btn topnavbt" id="btnAdd" disabled="disabled" ><i class="fa fa-plus"></i> Add</button>
        <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
        <button class="btn topnavbt" id="btnSave"  tabindex="3"  ><i class="fa fa-save"></i> Save</button>
        <button class="btn topnavbt" id="btnView" disabled="disabled" ><i class="fa fa-eye"></i> View</button>
        <button class="btn topnavbt" disabled="disabled"><i class="fa fa-print"></i> Print</button>
        <button class="btn topnavbt" id='btnUndo' ><i class="fa fa-undo"></i> Undo</button>
        <button class="btn topnavbt" id="btnCancel"  disabled="disabled" ><i class="fa fa-times"></i> Cancel</button>
        <button class="btn topnavbt" id="btnApprove"  disabled="disabled" ><i class="fa fa-lock"></i> Approved</button>
        <button class="btn topnavbt"  id="btnAttach"  disabled="disabled" ><i class="fa fa-link"></i> Attachment</button>
        <button class="btn topnavbt" id="btnExit"><i class="fa fa-power-off"></i> Exit</button>
      </div>
  </div>
</div>
   
<div class="container-fluid purchase-order-view filter">     
  <form id="frm_mst_add" method="POST" enctype="multipart/form-data" > 
    @CSRF
    <div class="inner-form">

      <div class="row">    
        <div class="col-lg-1 pl"><p>Employee Code</p></div>
        <div class="col-lg-2 pl">
          {{-- <input type="text" name="EMPCODE" id="EMPCODE" class="form-control" required autocomplete="off" maxlength="20" style="text-transform:uppercase" onkeypress="return AlphaNumaric(event,this)" /> --}}
          @if(!empty($objDD))
              @if($objDD->SYSTEM_GRSR == "1")
                  <input type="text" name="EMPCODE" id="EMPCODE" value="{{ $objDOCNO }}" class="form-control mandatory" tabindex="1" maxlength="10" autocomplete="off" readonly style="text-transform:uppercase" autofocus >
              @endif
              @if($objDD->MANUAL_SR == "1")
                  <input type="text" name="EMPCODE" id="EMPCODE" value="{{ old('EMPCODE') }}" class="form-control mandatory"  maxlength="{{$objDD->MANUAL_MAXLENGTH}}" tabindex="1" autocomplete="off" style="text-transform:uppercase" autofocus >
              @endif
          @endif 
          <span class="text-danger" id="ERROR_EMPCODE"></span> 
        </div>
		
        <div class="col-lg-1 pl"><p>Salutation</p></div>
        <div class="col-lg-2 pl">
          <select name="SAID_REF" id="SAID_REF" class="form-control" >
            <option value="" selected >Select</option>
            @foreach ($getSalutation as $val)
            <option value="{{ $val-> FID }}">{{ $val-> FNAME }}</option>
            @endforeach
          </select>
          <span class="text-danger" id="ERROR_SAID_REF"></span> 
        </div>

        <div class="col-lg-1 pl"><p>First Name</p></div>
        <div class="col-lg-2 pl">
        <input type="text" name="FNAME" id="FNAME" class="form-control" autocomplete="off" required   maxlength="50" />
          <span class="text-danger" id="ERROR_FNAME"></span> 
        </div>

        <div class="col-lg-1 pl"><p>Middle Name</p></div>
        <div class="col-lg-2 pl">
        <input type="text" name="MNAME" id="MNAME" class="form-control" autocomplete="off"  maxlength="50"  />
          <span class="text-danger" id="ERROR_MNAME"></span> 
        </div>
      </div>

      <div class="row">
        <div class="col-lg-1 pl"><p>Last / Surname</p></div>
        <div class="col-lg-2 pl">
        <input type="text" name="LNAME" id="LNAME" class="form-control" autocomplete="off" maxlength="50"  />
          <span class="text-danger" id="ERROR_LNAME"></span> 
        </div>
          	
        <div class="col-lg-1 pl"><p>Old Ref No</p></div>
        <div class="col-lg-2 pl">
        <input type="text" name="OLDREFNO" id="OLDREFNO" class="form-control" autocomplete="off" maxlength="50"  />
          <span class="text-danger" id="ERROR_OLDREFNO"></span> 
        </div>

        <div class="col-lg-1 pl"><p>Gender</p></div>
        <div class="col-lg-2 pl">
          <select name="GID_REF" id="GID_REF" class="form-control" required >
            <option value="" selected >Select</option>
            @foreach ($getGender as $val)
            <option value="{{ $val-> FID }}">{{ $val-> FNAME }}</option>
            @endforeach
          </select>
          <span class="text-danger" id="ERROR_GID_REF"></span> 
        </div>

        <div class="col-lg-1 pl"><p>Date of Birth</p></div>
        <div class="col-lg-2 pl">
        <input type="date" name="DOB" id="DOB" class="form-control" autocomplete="off" required />
          <span class="text-danger" id="ERROR_DOB"></span> 
        </div>
      </div>

        <div class="row">
        <div class="col-lg-1 pl"><p>Birth Place</p></div>
        <div class="col-lg-2 pl">
        <input type="text" name="DOBPLACE" id="DOBPLACE" class="form-control" autocomplete="off" maxlength="100"  />
          <span class="text-danger" id="ERROR_DOBPLACE"></span> 
        </div>

        <div class="col-lg-1 pl"><p>Married Status</p></div>
        <div class="col-lg-2 pl">
          <select name="FATHERNAME" id="FATHERNAME" class="form-control" required >
            <option value="" selected >Select</option>
            <option value="single">single</option>
            <option value="married">Married</option>
            <option value="separated">Separated</option>
          </select>
          <span class="text-danger" id="ERROR_DESGID_REF"></span> 
        </div>


        <div class="col-lg-1 pl"><p>Blood Group</p></div>
        <div class="col-lg-2 pl">
          <select name="BGID_REF" id="BGID_REF" class="form-control" required >
            <option value="" selected >Select</option>
            @foreach ($getBloodGroup as $val)
            <option value="{{ $val-> FID }}">{{ $val-> FNAME }}</option>
            @endforeach
          </select>
          <span class="text-danger" id="ERROR_BGID_REF"></span> 
        </div>


        <div class="col-lg-1 pl"><p>Designation</p></div>
        <div class="col-lg-2 pl">
          <select name="DESGID_REF" id="DESGID_REF" class="form-control" required >
            <option value="" selected >Select</option>
            @foreach ($getDesignation as $val)
            <option value="{{ $val-> FID }}">{{ $val-> FNAME }}</option>
            @endforeach
          </select>
          <span class="text-danger" id="ERROR_DESGID_REF"></span> 
        </div>

        
      </div>

      <div class="row">    	
        <div class="col-lg-1 pl"><p>Department</p></div>
        <div class="col-lg-2 pl">
          <select name="DEPID_REF" id="DEPID_REF" class="form-control" >
            <option value="" selected >Select</option>
            @foreach ($getDepartment as $val)
            <option value="{{ $val-> FID }}">{{ $val-> FNAME }}</option>
            @endforeach
          </select>
          <span class="text-danger" id="ERROR_BGID_DEPID_REF"></span> 
        </div>

        <div class="col-lg-1 pl"><p>Division</p></div>
        <div class="col-lg-2 pl">
          <select name="DIVID_REF" id="DIVID_REF" class="form-control" >
            <option value="" selected >Select</option>
            @foreach ($getDivision as $val)
            <option value="{{ $val-> FID }}">{{ $val-> FNAME }}</option>
            @endforeach
          </select>
          <span class="text-danger" id="ERROR_DIVID_REF"></span> 
        </div>

        <div class="col-lg-1 pl"><p>Post Level</p></div>
        <div class="col-lg-2 pl">
          <select name="PLID_REF" id="PLID_REF" class="form-control" >
            <option value="" selected >Select</option>
            @foreach ($getPostLevel as $val)
            <option value="{{ $val-> FID }}">{{ $val-> FNAME }}</option>
            @endforeach
          </select>
          <span class="text-danger" id="ERROR_PLID_REF"></span> 
        </div>

        <div class="col-lg-1 pl"><p>Date of Joining</p></div>
        <div class="col-lg-2 pl">
        <input type="date" name="DOJ" id="DOJ" class="form-control" autocomplete="off" maxlength="200"  />
          <span class="text-danger" id="ERROR_DOJ"></span> 
        </div>
      </div>

      <div class="row">    	
        <div class="col-lg-1 pl"><p>Emp Category</p></div>
        <div class="col-lg-2 pl">
          <select name="CATID_REF" id="CATID_REF" class="form-control" required >
            <option value="" selected >Select</option>
            @foreach ($getEmployeeCategory as $val)
            <option value="{{ $val-> FID }}">{{ $val-> FNAME }}</option>
            @endforeach
          </select>
          <span class="text-danger" id="ERROR_CATID_REF"></span> 
        </div>
        
        <div class="col-lg-1 pl"><p>Employee Type</p></div>
        <div class="col-lg-2 pl">
          <select name="ETYPEID_REF" id="ETYPEID_REF" class="form-control" required >
            <option value="" selected >Select</option>
            @foreach ($getEmployeeType as $val)
            <option value="{{ $val-> FID }}">{{ $val-> FNAME }}</option>
            @endforeach
          </select>
          <span class="text-danger" id="ERROR_ETYPEID_REF"></span> 
        </div>

        <div class="col-lg-1 pl"><p>Cost Centre</p></div>
        <div class="col-lg-2 pl">
          <select name="CCID_REF" id="CCID_REF" class="form-control" >
            <option value="" selected >Select</option>
            @foreach ($getCostCentre as $val)
            <option value="{{ $val-> FID }}">{{ $val-> FNAME }}</option>
            @endforeach
          </select>
          <span class="text-danger" id="ERROR_CCID_REF"></span> 
        </div>
         	
        <div class="col-lg-1 pl"><p>Grade</p></div>
        <div class="col-lg-2 pl">
          <select name="GRADEID_REF" id="GRADEID_REF" class="form-control" >
            <option value="" selected >Select</option>
            @foreach ($getGrade as $val)
            <option value="{{ $val-> FID }}">{{ $val-> FNAME }}</option>
            @endforeach
          </select>
          <span class="text-danger" id="ERROR_GRADEID_REF"></span> 
        </div>
      </div>

        <div class="row"> 
        <div class="col-lg-1 pl"><p>Branch</p></div>
        <div class="col-lg-2 pl">
          <select name="BRID_REF" id="BRID_REF" class="form-control" disabled >
            @foreach ($getBranch as $val)
            <option {{Auth::user()->BRID_REF == $val-> FID?'selected="selected"':''}} value="{{ $val-> FID }}">{{ $val-> FNAME }}</option>
            @endforeach
          </select>
          <span class="text-danger" id="ERROR_BRID_REF"></span> 
        </div>

        <div class="col-lg-1 pl"><p>Sales Person</p></div>
        <div class="col-lg-2 pl"> 
          <input type="checkbox" name="SALES_PERSON" id="SALES_PERSON" value="1" >
          <span class="text-danger" id="ERROR_SALES_PERSON"></span>
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
                <input type="checkbox" name="CRESIDENCE" id="CRESIDENCE" >
                <span class="text-danger" id="ERROR_CRESIDENCE"></span>
              </div>

              <div class="col-lg-2 "><p>Own / Parents Residence</p></div>
              <div class="col-lg-1 ">
                <input type="checkbox" name="CROWN" id="CROWN"  onchange="getPParentRented('CROWN')">
                <span class="text-danger" id="ERROR_CROWN"></span>
              </div>

              <div class="col-lg-1 "><p>Rented</p></div>
              <div class="col-lg-1 ">
                <input type="checkbox" name="CRRENTED" id="CRRENTED" onchange="getPParentRented('CRRENTED')">
                <span class="text-danger" id="ERROR_CRRENTED"></span>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-1 "><p>Add Line 1</p></div>
              <div class="col-lg-5 ">
                <textarea name="CRADD1" id="CRADD1" class="form-control" autocomplete="off" ></textarea>
                <span class="text-danger" id="ERROR_CRADD1"></span>
              </div>

              <div class="col-lg-1 "><p>Add Line 2</p></div>
              <div class="col-lg-5 ">
                <textarea name="CRADD2" id="CRADD2" class="form-control" autocomplete="off" ></textarea>
                <span class="text-danger" id="ERROR_CRADD2"></span>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-1"><p>Country</p></div>
              <div class="col-lg-2">
                  <input type="text" name="CTRYID_REF_POPUP" id="CTRYID_REF_POPUP" class="form-control" readonly />
                  <input type="hidden" name="CTRYID_REF" id="CTRYID_REF" />
                  <span class="text-danger" id="ERROR_CTRYID_REF"></span>
              </div>

              <div class="col-lg-1"><p>State</p></div>
              <div class="col-lg-2">
                  <input type="text" name="STID_REF_POPUP" id="STID_REF_POPUP" class="form-control"  readonly/>
                  <input type="hidden" name="STID_REF" id="STID_REF" />
                  <span class="text-danger" id="ERROR_STID_REF"></span>
              </div>

              
              <div class="col-lg-1"><p>City</p></div>
              <div class="col-lg-2">
                <input type="text" name="CITYID_REF_POPUP" id="CITYID_REF_POPUP" class="form-control" readonly tabindex="8" />
                <input type="hidden" name="CITYID_REF" id="CITYID_REF" />
                <span class="text-danger" id="ERROR_CITYID_REF"></span>
              </div>

              
            
            </div>

            <div class="row">
              <div class="col-lg-1"><p>District</p></div>
              <div class="col-lg-2">
                <input type="text" name="DISTID_REF_POPUP" id="DISTID_REF_POPUP" class="form-control" readonly tabindex="9" />
                <input type="hidden" name="DISTID_REF" id="DISTID_REF" />
                <span class="text-danger" id="ERROR_DISTID_REF"></span>
              </div>

              <div class="col-lg-1"><p>Pincode</p></div>
              <div class="col-lg-2">
                  <input type="text" name="CRPIN" id="CRPIN" class="form-control" autocomplete="off"  maxlength="10" onkeypress="return isNumberKey(event,this)" >
              </div>

              <div class="col-lg-1"><p>Landmark</p></div>
              <div class="col-lg-4">
                <input type="text" name="CRLM" id="CRLM" class="form-control" autocomplete="off"  maxlength="50" >
              </div>
            </div>

            <div class="row">
              <div class="col-lg-2 "><p>Permanant Residence</p></div>
              <div class="col-lg-1 ">
                <input type="checkbox" name="PRRESIDENCE" id="PRRESIDENCE"  value="1" >
                <span class="text-danger" id="ERROR_PRRESIDENCE"></span>
              </div>

              <div class="col-lg-2 "><p>Own / Parents Residence</p></div>
              <div class="col-lg-1 ">
                <input type="checkbox" name="PROWN" id="PROWN" value="1" >
                <span class="text-danger" id="ERROR_PROWN"></span>
              </div>

              <div class="col-lg-1 "><p>Rented</p></div>
              <div class="col-lg-1 ">
                <input type="checkbox" name="PRRENTED" id="PRRENTED" value="1" >
                <span class="text-danger" id="ERROR_PRRENTED"></span>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-1 "><p>Add Line 1</p></div>
              <div class="col-lg-5 ">
                <textarea name="PRADD1" id="PRADD1" class="form-control" autocomplete="off" maxlength="100" ></textarea>
                <span class="text-danger" id="ERROR_PRADD1"></span>
              </div>

              <div class="col-lg-1 "><p>Add Line 2</p></div>
              <div class="col-lg-5 ">
                <textarea name="PRADD2" id="PRADD2" class="form-control" autocomplete="off" maxlength="100" ></textarea>
                <span class="text-danger" id="ERROR_PRADD2"></span>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-1"><p>Country</p></div>
              <div class="col-lg-2">
                  <input type="text" name="CTRYID_REF_POPUP1" id="CTRYID_REF_POPUP1" class="form-control" readonly />
                  <input type="hidden" name="CTRYID_REF1" id="CTRYID_REF1" />
                  <span class="text-danger" id="ERROR_CTRYID_REF1"></span>
              </div>

              <div class="col-lg-1"><p>State</p></div>
              <div class="col-lg-2">
                  <input type="text" name="STID_REF_POPUP1" id="STID_REF_POPUP1" class="form-control" readonly/>
                  <input type="hidden" name="STID_REF1" id="STID_REF1" />
                  <span class="text-danger" id="ERROR_STID_REF1"></span>
              </div>

              
              <div class="col-lg-1"><p>City</p></div>
              <div class="col-lg-2">
                <input type="text" name="CITYID_REF_POPUP1" id="CITYID_REF_POPUP1" class="form-control" readonly />
                <input type="hidden" name="CITYID_REF1" id="CITYID_REF1" />
                <span class="text-danger" id="ERROR_CITYID_REF1"></span>
              </div>

            </div>

            <div class="row">
              <div class="col-lg-1"><p>District</p></div>
              <div class="col-lg-2">
                <input type="text" name="DISTID_REF_POPUP1" id="DISTID_REF_POPUP1" class="form-control" readonly  />
                <input type="hidden" name="DISTID_REF1" id="DISTID_REF1" />
                <span class="text-danger" id="ERROR_DISTID_REF1"></span>
              </div>

              <div class="col-lg-1"><p>Pincode</p></div>
              <div class="col-lg-2">
                  <input type="text" name="PRPIN" id="PRPIN" class="form-control" autocomplete="off"  maxlength="10" onkeypress="return isNumberKey(event,this)" >
              </div>

              <div class="col-lg-1"><p>Landmark</p></div>
              <div class="col-lg-4">
                <input type="text" name="PRLM" id="PRLM" class="form-control" autocomplete="off"  maxlength="50" >
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
                <tr  class="participantRow2">
                    <td><input type="text" name="FAMILY_NAME_0" id ="FAMILY_NAME_0"  maxlength="100" class="form-control w-100" autocomplete="off" ></td>
                    <td><input type="date" name="FAMILY_DOB_0" id ="FAMILY_DOB_0"   class="form-control w-100" autocomplete="off" ></td>
                    
                    <td>
                    <select name="FAMILY_GID_REF_0" id="FAMILY_GID_REF_0" class="form-control w-100" >
                      <option value="" >Select</option>
                      @foreach ($getGender as $val)
                      <option value="{{ $val-> FID }}">{{ $val-> FNAME }}</option>
                      @endforeach
                    </select>
                    </td>

                    <td>
                      <select name="FAMILY_RSID_REF_0" id="FAMILY_RSID_REF_0" class="form-control w-100" >
                        <option value="" >Select</option>
                        @foreach ($getRelationShip as $val)
                        <option value="{{ $val-> FID }}">{{ $val-> FNAME }}</option>
                        @endforeach
                      </select>
                    </td>

                    <td>
                      <select name="FAMILY_EARNING_0" id="FAMILY_EARNING_0" class="form-control w-100" >
                        <option value="">Select</option>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                      </select>
                    </td>

                    <td><input type="text" name="FAMILY_CONTACTNO_0" id ="FAMILY_CONTACTNO_0"  maxlength="20" class="form-control w-100" autocomplete="off" onkeypress="return isNumberKey(event,this)" ></td>
                    <td><input type="text" name="FAMILY_EMAIL_0" id ="FAMILY_EMAIL_0"  maxlength="50" class="form-control w-100" autocomplete="off" ></td>
                    <td align="center" >
                        <button class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></button>
                        <button class="btn remove" title="Delete" data-toggle="tooltip" disabled><i class="fa fa-trash" ></i></button>
                    </td>
                </tr>
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
                <th hidden><input class="form-control" type="hidden" name="Row_Count3" id ="Row_Count3"></th>
                <th>Year of Passing</th>
                <th>School / College / University / Institute</th>
                <th>Result</th>
                <th>Remarks</th>
                <th width="10%">Action</th>
                </tr>
              </thead>
              <tbody>
                <tr  class="participantRow3">
                    <td><input type="text" name="EDUCATION_DEGREE_0" id ="EDUCATION_DEGREE_0"  maxlength="100" class="form-control w-100" autocomplete="off" ></td>
                    <td><input type="text" name="EDUCATION_YOP_0" id ="EDUCATION_YOP_0"  maxlength="20" class="form-control w-100" autocomplete="off" ></td>
                    <td><input type="text" name="EDUCATION_UNIVERSITY_0" id ="EDUCATION_UNIVERSITY_0"  maxlength="100" class="form-control w-100" autocomplete="off" ></td>
                    <td><input type="text" name="EDUCATION_RESULT_0" id ="EDUCATION_RESULT_0"  maxlength="50" class="form-control w-100" autocomplete="off" ></td>
                    <td><input type="text" name="EDUCATION_REMARKS_0" id ="EDUCATION_REMARKS_0"  maxlength="100" class="form-control w-100" autocomplete="off" ></td>
                    <td align="center" >
                        <button class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></button>
                        <button class="btn remove" title="Delete" data-toggle="tooltip" disabled><i class="fa fa-trash" ></i></button>
                    </td>
                </tr>
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
                <tr  class="participantRow4">
                    <td><input type="text" name="EXPERIENCE_CNAME_0" id ="EXPERIENCE_CNAME_0"  maxlength="100" class="form-control w-100" autocomplete="off" ></td>
                    <td><input type="text" name="EXPERIENCE_FROMPD_0" id ="EXPERIENCE_FROMPD_0"  maxlength="30" class="form-control w-100" autocomplete="off" ></td>
                    <td><input type="text" name="EXPERIENCE_TOPD_0" id ="EXPERIENCE_TOPD_0"  maxlength="30" class="form-control w-100" autocomplete="off" ></td>
                    <td><input type="text" name="EXPERIENCE_LASTDESIG_0" id ="EXPERIENCE_LASTDESIG_0"  maxlength="100" class="form-control w-100" autocomplete="off" ></td>
                    <td><input type="text" name="EXPERIENCE_CTCPA_0" id ="EXPERIENCE_CTCPA_0"  maxlength="50" class="form-control w-100" autocomplete="off" ></td>
                    <td><input type="text" name="EXPERIENCE_REMARKS_0" id ="EXPERIENCE_REMARKS_0"  maxlength="100" class="form-control w-100" autocomplete="off" ></td>
                    <td align="center" >
                        <button class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></button>
                        <button class="btn remove" title="Delete" data-toggle="tooltip" disabled><i class="fa fa-trash" ></i></button>
                    </td>
                </tr>
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
                <tr  class="participantRow5">
                    <td><input type="text" name="REFERENCE_RNAME_0" id ="REFERENCE_RNAME_0"  maxlength="100" class="form-control w-100" autocomplete="off" ></td>
                    <td>
                    <select name="REFERENCE_GID_REF_0" id="REFERENCE_GID_REF_0" class="form-control w-100" >
                      <option value="" selected >Select</option>
                      @foreach ($getGender as $val)
                      <option value="{{ $val-> FID }}">{{ $val-> FNAME }}</option>
                      @endforeach
                    </select>
                    </td>
                    <td><input type="text" name="REFERENCE_COMPANY_0" id ="REFERENCE_COMPANY_0"  maxlength="100" class="form-control w-100" autocomplete="off" ></td>
                    <td><input type="text" name="REFERENCE_DESIG_0" id ="REFERENCE_DESIG_0"  maxlength="50" class="form-control w-100" autocomplete="off" ></td>
                    <td><input type="text" name="REFERENCE_MONO_0" id ="REFERENCE_MONO_0"  maxlength="20" class="form-control w-100" autocomplete="off" onkeypress="return isNumberKey(event,this)" ></td>
                    <td><input type="text" name="REFERENCE_EMAIL_0" id ="REFERENCE_EMAIL_0"  maxlength="50" class="form-control w-100" autocomplete="off" ></td>
                    <td align="center" >
                        <button class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></button>
                        <button class="btn remove" title="Delete" data-toggle="tooltip" disabled><i class="fa fa-trash" ></i></button>
                    </td>
                </tr>
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
                <tr  class="participantRow6">
                    <td><input type="text" name="EXTRACUR_NAME_0" id ="EXTRACUR_NAME_0"  maxlength="100" class="form-control w-100" autocomplete="off" ></td>
                    <td><input type="text" name="EXTRACUR_PERIOD_0" id ="EXTRACUR_PERIOD_0"  maxlength="50" class="form-control w-100" autocomplete="off" ></td>
                    <td><input type="text" name="EXTRACUR_LEVELS_0" id ="EXTRACUR_LEVELS_0"  maxlength="50" class="form-control w-100" autocomplete="off" ></td>
                    <td><input type="text" name="EXTRACUR_ACHIEVEMENT_0" id ="EXTRACUR_ACHIEVEMENT_0"  maxlength="100" class="form-control w-100" autocomplete="off" ></td>
                    <td align="center" >
                        <button class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></button>
                        <button class="btn remove" title="Delete" data-toggle="tooltip" disabled><i class="fa fa-trash" ></i></button>
                    </td>
                </tr>
              </tbody>
            </table>
          </div>

        </div>

        


        <div id="tab7" class="tab-pane fade">
          <div class="table-wrapper-scroll-x" style="margin-top:10px;">

            
            <div class="row">
              <div class="col-lg-1"><p>PAN NO</p></div>
              <div class="col-lg-2">
                  <input type="text" name="PANNO" id="PANNO" class="form-control" autocomplete="off"  maxlength="20" >
              </div>

              <div class="col-lg-1"><p>AADHAAR NO</p></div>
              <div class="col-lg-2">
                <input type="text" name="AADHARNO" id="AADHARNO" class="form-control" autocomplete="off"  maxlength="20" >
              </div>

              <div class="col-lg-1"><p>Election I Card No</p></div>
              <div class="col-lg-2">
                <input type="text" name="ELECTIONCNO" id="ELECTIONCNO" class="form-control" autocomplete="off"  maxlength="20" >
              </div>

              <div class="col-lg-1"><p>Place of Issue</p></div>
              <div class="col-lg-2">
                <input type="text" name="ELECTIONCPOI" id="ELECTIONCPOI" class="form-control" autocomplete="off"  maxlength="50" >
              </div>
            </div>

            <div class="row">
              <div class="col-lg-1"><p>Driving License No</p></div>
              <div class="col-lg-2">
                  <input type="text" name="DLNO" id="DLNO" class="form-control" autocomplete="off"  maxlength="20" >
              </div>

              <div class="col-lg-1"><p>Place of Issue</p></div>
              <div class="col-lg-2">
                <input type="text" name="DLPOI" id="DLPOI" class="form-control" autocomplete="off"  maxlength="50" >
              </div>

              <div class="col-lg-1"><p>Valid Upto</p></div>
              <div class="col-lg-2">
                <input type="date" name="DLVALIDUPTO" id="DLVALIDUPTO" class="form-control"  autocomplete="off"  >
              </div>
            </div>

            <div class="row">
              <div class="col-lg-1"><p>Passport No</p></div>
              <div class="col-lg-2">
                  <input type="text" name="PASSPORTNO" id="PASSPORTNO" class="form-control" autocomplete="off" maxlength="20" >
              </div>

              <div class="col-lg-1"><p>Place of Issue</p></div>
              <div class="col-lg-2"> 
                <input type="text" name="PASSPORTPOI" id="PASSPORTPOI" class="form-control" autocomplete="off"  maxlength="50" >
              </div>

              <div class="col-lg-1"><p>Valid Upto</p></div>
              <div class="col-lg-2">
                <input type="date" name="PASSPORTVUPTO" id="PASSPORTVUPTO" class="form-control"  autocomplete="off"  >
              </div>
            </div>

            <div class="row">
              <div class="col-lg-1"><p>PF No / UAN No</p></div>
              <div class="col-lg-2">
                  <input type="text" name="PFNO" id="PFNO" class="form-control" autocomplete="off"  maxlength="30" >
              </div>

              <div class="col-lg-1"><p>ESI NO</p></div>
              <div class="col-lg-2">
                  <input type="text" name="ESINO" id="ESINO" class="form-control" autocomplete="off" maxlength="30" >
              </div>

              <div class="col-lg-1"><p>Membership Name</p></div>
              <div class="col-lg-2">
                  <input type="text" name="MSHIPNAME" id="MSHIPNAME" class="form-control" autocomplete="off"  maxlength="50" >
              </div>

              <div class="col-lg-1"><p>No</p></div>
              <div class="col-lg-2">
                  <input type="text" name="MSHIPNO" id="MSHIPNO" class="form-control"  autocomplete="off" maxlength="20" >
              </div>
            </div>

            <div class="row">
              <div class="col-lg-1"><p>Contractor Name</p></div>
              <div class="col-lg-2">
                  <input type="text" name="CONTRACTRNAME" id="CONTRACTRNAME" class="form-control" autocomplete="off" maxlength="100" >
              </div>

              <div class="col-lg-1"><p>Nominee Name</p></div>
              <div class="col-lg-2">
                  <input type="text" name="NOMINEENAME" id="NOMINEENAME" class="form-control" autocomplete="off" maxlength="100" >
              </div>

              <div class="col-lg-1"><p>Relationship</p></div>
              <div class="col-lg-2">
                <select name="RSID_REF" id="RSID_REF" class="form-control" >
                  <option value="" >Select</option>
                  @foreach ($getRelationShip as $val)
                  <option value="{{ $val-> FID }}">{{ $val-> FNAME }}</option>
                  @endforeach
                </select>

              </div>

              <div class="col-lg-1"><p>Bank Name</p></div>
              <div class="col-lg-2">
                  <input type="text" name="BANK" id="BANK" class="form-control" autocomplete="off"  maxlength="100" >
              </div>
            </div>

            <div class="row">
              <div class="col-lg-1"><p>IFSC</p></div>
              <div class="col-lg-2">
                  <input type="text" name="IFSC" id="IFSC" class="form-control" autocomplete="off"  maxlength="30" >
              </div>

              <div class="col-lg-1"><p>Branch</p></div>
              <div class="col-lg-2">
                  <input type="text" name="BRANCH" id="BRANCH" class="form-control" autocomplete="off"  maxlength="50" >
              </div>

              <div class="col-lg-1"><p>Account Type</p></div>
              <div class="col-lg-2">
                 
                  <select name="ACTYPE" id="ACTYPE" class="form-control"  >
                    <option value="" selected >Select</option>
                    <option value='SAVING ACCOUNT'>SAVING ACCOUNT</option>
                    <option value='CURRENT ACCOUNT'>CURRENT ACCOUNT</option>
                    <option value='OD'>OD</option>
                    <option value='OTHERS'>OTHERS</option>
                  </select>
              </div>

              <div class="col-lg-1"><p>Account No</p></div>
              <div class="col-lg-2">
                  <input type="text" name="ACNO" id="ACNO" class="form-control" autocomplete="off"  maxlength="30" onkeypress="return isNumberKey(event,this)" >
              </div>
            </div>

            <div class="row">
              <div class="col-lg-1"><p>Branch Code</p></div>
              <div class="col-lg-2">
                  <input type="text" name="BRANCHCODE" id="BRANCHCODE" class="form-control" autocomplete="off"  maxlength="30" >
              </div>
            </div>


          </div>
        </div>

        <div id="tab8" class="tab-pane fade">
          <div class="table-wrapper-scroll-x" style="margin-top:10px;">

            
            <div class="row">
              <div class="col-lg-2"><p>Landline No</p></div>
              <div class="col-lg-2">
                  <input type="text" name="LLNO" id="LLNO" class="form-control" autocomplete="off"  maxlength="20" onkeypress="return isNumberKey(event,this)" >
              </div>

              <div class="col-lg-2"><p>Cell No 1</p></div>
              <div class="col-lg-2">
                <input type="text" name="MONO1" id="MONO1" class="form-control" autocomplete="off"  maxlength="20" onkeypress="return isNumberKey(event,this)" >
              </div>

              <div class="col-lg-2"><p>Cell No 2</p></div>
              <div class="col-lg-2">
                <input type="text" name="MONO2" id="MONO2" class="form-control" autocomplete="off"  maxlength="20" onkeypress="return isNumberKey(event,this)" >
              </div>

             
            </div>

            <div class="row">
              <div class="col-lg-2"><p>Email ID</p></div>
              <div class="col-lg-2">
                <input type="text" name="EMAIL" id="EMAIL" class="form-control" autocomplete="off"  maxlength="50" >
              </div>

              <div class="col-lg-2"><p>Personal Email ID</p></div>
              <div class="col-lg-2">
                  <input type="text" name="PEMAIL" id="PEMAIL" class="form-control"  autocomplete="off" maxlength="50" >
              </div>

              <div class="col-lg-2"><p>Emergency Contact Person</p></div>
              <div class="col-lg-2">
                <input type="text" name="EMERGCPNAME" id="EMERGCPNAME" class="form-control" autocomplete="off" maxlength="50" >
              </div>

              
            </div>

            <div class="row">
            <div class="col-lg-2"><p>Cell No</p></div>
              <div class="col-lg-2">
                <input type="text" name="EMERGCPMONO" id="EMERGCPMONO" class="form-control" autocomplete="off" maxlength="20" onkeypress="return isNumberKey(event,this)"   >
              </div>
            </div>

          </div>
        </div>



        <div id="tab9" class="tab-pane fade">
          <div class="table-wrapper-scroll-x" style="margin-top:10px;">

            
            <div class="row">
              <div class="col-lg-1"><p>Current Disease 1</p></div>
              <div class="col-lg-5">
                  <input type="text" name="CRDISEASE1" id="CRDISEASE1" class="form-control" autocomplete="off" maxlength="20" >
              </div>

              <div class="col-lg-1"><p>Current Disease 2</p></div>
              <div class="col-lg-5">
                <input type="text" name="CRDISEASE2" id="CRDISEASE2" class="form-control" autocomplete="off" maxlength="20" >
              </div>

              
            </div>

            <div class="row">
              <div class="col-lg-1"><p>Current Disease 3</p></div>
              <div class="col-lg-5">
                <input type="text" name="CRDISEASE3" id="CRDISEASE3" class="form-control" autocomplete="off" maxlength="20" >
              </div>

              <div class="col-lg-1"><p>Allergy</p></div>
              <div class="col-lg-2">
                <input type="text" name="ALLERGY" id="ALLERGY" class="form-control" autocomplete="off" maxlength="20" >
              </div>

              </div>

              <div class="row">

              <div class="col-lg-1"><p>Height</p></div>
              <div class="col-lg-2">
                  <input type="text" name="HEIGHT" id="HEIGHT" class="form-control" autocomplete="off"  maxlength="20" >
              </div>

              <div class="col-lg-1"><p>Weight</p></div>
              <div class="col-lg-2">
                <input type="text" name="WEIGHT" id="WEIGHT" class="form-control" autocomplete="off"  maxlength="20" >
              </div>

              <div class="col-lg-1"><p>Color</p></div>
              <div class="col-lg-2">
                <input type="text" name="COLOR" id="COLOR" class="form-control" autocomplete="off" maxlength="15"   >
              </div>

              <div class="col-lg-1"><p>Religion</p></div>
              <div class="col-lg-2">
                <input type="text" name="RELIGION" id="RELIGION" class="form-control" autocomplete="off" maxlength="15"   >
              </div>

              </div>

            <div class="row">

              
           
              <div class="col-lg-1"><p>Nationality</p></div>
              <div class="col-lg-2">
                  <input type="text" name="NANTIONALITY" id="NANTIONALITY" class="form-control" autocomplete="off"  maxlength="20" >
              </div>

              <div class="col-lg-1"><p>Hobbies</p></div>
              <div class="col-lg-2">
                <input type="text" name="HOBBIES" id="HOBBIES" class="form-control" autocomplete="off" maxlength="30" >
              </div>

              <div class="col-lg-1"><p>Vegetarian</p></div>
              <div class="col-lg-2">
                <input type="radio" name="VEGETARIAN"  value="1" checked  >
              </div>

              <div class="col-lg-1"><p>Non-Veg</p></div>
              <div class="col-lg-2">
              <input type="radio" name="VEGETARIAN"    value="0"   >
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
            <th class="ROW1" style="width: 10%" align="center">Select</th> 
            <th class="ROW2" style="width: 40%">Code</th>
            <th  class="ROW3"style="width: 40%">Name</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
            <td class="ROW2"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control"  id="country_codesearch" onkeyup="searchCountryCode()" /></td>
            <td class="ROW3"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control"  id="country_namesearch" onkeyup="searchCountryName()" /></td>
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
            <th class="ROW1" style="width: 10%" align="center">Select</th> 
            <th class="ROW2" style="width: 40%">Code</th>
            <th  class="ROW3"style="width: 40%">Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
          <td class="ROW2"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control"  id="dist_codesearch" onkeyup="searchDistCode()" /></td>
          <td class="ROW3"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control"  id="dist_namesearch" onkeyup="searchDistName()" /></td>
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
              <td class="ROW2"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control"  id="country_codesearch1" onkeyup="searchCountryCode1()" /></td>
              <td class="ROW3"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control"  id="country_namesearch1" onkeyup="searchCountryName1()" /></td>
            </tr>         
        </tbody>
      </table>


      <table id="country_tab21" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody id="country_body1">
        @foreach ($objCountryList as $index=>$CountryList)
        <tr >
          <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_CTRYID1_REF[]"  id="ctryidref1_{{ $CountryList->CTRYID }}" class="cls_ctryidref1" value="{{ $CountryList->CTRYID }}" ></td>
          <td class="ROW2" style="width: 39%">{{ $CountryList->CTRYCODE }}
          <input type="hidden" id="txtctryidref1_{{ $CountryList->CTRYID }}" data-desc="{{ $CountryList->CTRYCODE }} - {{ $CountryList->NAME }}" value="{{ $CountryList-> CTRYID }}"/>
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
            <th class="ROW1" style="width: 10%" align="center">Select</th> 
            <th class="ROW2" style="width: 40%">Code</th>
            <th  class="ROW3"style="width: 40%">Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
          <td class="ROW2"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control"  id="state_codesearch1" onkeyup="searchStateCode1()" /></td>
          <td class="ROW3"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control"  id="state_namesearch1" onkeyup="searchStateName1()" /></td>
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
            <th class="ROW1" style="width: 10%" align="center">Select</th> 
            <th class="ROW2" style="width: 40%">Code</th>
            <th  class="ROW3"style="width: 40%">Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
          <td class="ROW2"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="city_codesearch1" onkeyup="searchCityCode1()" /></td>
          <td class="ROW3"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="city_namesearch1" onkeyup="searchCityName1()" /></td>
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
            <th class="ROW1" style="width: 10%" align="center">Select</th> 
            <th class="ROW2" style="width: 40%">Code</th>
            <th  class="ROW3"style="width: 40%">Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
          <td class="ROW2"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="dist_codesearch1" onkeyup="searchDistCode1()" /></td>
          <td class="ROW3"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="dist_namesearch1" onkeyup="searchDistName1()" /></td>
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
<!-- btnSave -->

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

$('.cls_ctryidref').click(function(){
  var id = $(this).attr('id');
  var txtval =    $("#txt"+id+"").val();
  var texdesc =   $("#txt"+id+"").data("desc")

  $("#CTRYID_REF_POPUP").val(texdesc);
  $("#CTRYID_REF").val(txtval);

  getCountryWiseState(txtval);
  
  $("#CTRYID_REF_POPUP").blur(); 
  $("#STID_REF_POPUP").focus(); 
  
  $("#ctryidref_popup").hide();
  $("#country_codesearch").val('');
  $("#country_namesearch").val('');
  searchCountryCode();
  $(this).prop("checked",false);
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

function getCountryWiseState(CTRYID_REF){
    $("#state_body").html('loading...');
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
          
          $("#STID_REF_POPUP").val('');
          $("#STID_REF").val('');
			    $("#CITYID_REF_POPUP").val('');
          $("#CITYID_REF").val('');
          $("#city_body").html(''); 
          $("#DISTID_REF_POPUP").val('');
          $("#DISTID_REF").val('');
          $("#dist_body").html(''); 
          
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

function bindStateEvents(){
  $('.cls_stidref').click(function(){

    var id = $(this).attr('id');
    var txtval =    $("#txt"+id+"").val();
    var texdesc =   $("#txt"+id+"").data("desc")

    $("#STID_REF_POPUP").val(texdesc);
    $("#STID_REF").val(txtval);
	
	var CTRYID_REF	=	$("#CTRYID_REF").val();
	
	getStateWiseCity(CTRYID_REF,txtval);
	
	$("#STID_REF_POPUP").blur(); 
	$("#CITYID_REF_POPUP").focus(); 
	
    $("#stidref_popup").hide();
    
    $("#state_codesearch").val('');
    $("#state_namesearch").val('');
    searchStateCode();
    $(this).prop("checked",false);
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

function getStateWiseCity(CTRYID_REF,STID_REF){
    $("#city_body").html('loading...');
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
          
            $("#CITYID_REF_POPUP").val('');
            $("#CITYID_REF").val('');

            $("#DISTID_REF_POPUP").val('');
            $("#DISTID_REF").val('');
            $("#dist_body").html('');
            
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
  $("#cityidref_popup").show();
});

$("#CITYID_REF_POPUP").keyup(function(event){
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

		$("#CITYID_REF_POPUP").val(texdesc);
    $("#CITYID_REF").val(txtval);
    
    var CTRYID_REF	=	$("#CTRYID_REF").val();
    var STID_REF	=	$("#STID_REF").val();

	  getCityWiseDist(CTRYID_REF,STID_REF,txtval);
	
    $("#CITYID_REF_POPUP").blur(); 
	  $("#DISTID_REF_POPUP").focus(); 

		$("#cityidref_popup").hide();
		$("#city_codesearch").val('');
		$("#city_namesearch").val('');
		searchCityCode();
    $(this).prop("checked",false);
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

function getCityWiseDist(CTRYID_REF,STID_REF,CITYID_REF){
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
          
            $("#DISTID_REF_POPUP").val('');
            $("#DISTID_REF").val('');            
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
  $("#distidref_popup").show();
});

$("#DISTID_REF_POPUP").keyup(function(event){
  if(event.keyCode==13){
    $("#distidref_popup").show();
  }
});

$("#distidref_close").on("click",function(event){ 
  $("#distidref_popup").hide();
});

function bindDistEvents(){
	$('.cls_distidref').click(function(){

		var id = $(this).attr('id');
		var txtval =    $("#txt"+id+"").val();
		var texdesc =   $("#txt"+id+"").data("desc")

		$("#DISTID_REF_POPUP").val(texdesc);
		$("#DISTID_REF").val(txtval);

		$("#distidref_popup").hide();
		
    $("#dist_codesearch").val('');
    $("#dist_namesearch").val('');
		searchDistCode();
    $(this).prop("checked",false);
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

function searchDistName() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("dist_namesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("dist_tab2");
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

$('.cls_ctryidref1').click(function(){
  var id = $(this).attr('id');
  var txtval =    $("#txt"+id+"").val();
  var texdesc =   $("#txt"+id+"").data("desc")

  $("#CTRYID_REF_POPUP1").val(texdesc);
  $("#CTRYID_REF1").val(txtval);

  getCountryWiseState1(txtval);
  
  $("#CTRYID_REF_POPUP1").blur(); 
  $("#STID_REF_POPUP1").focus(); 
  
  $("#ctryidref_popup1").hide();
  $("#country_codesearch1").val('');
  $("#country_namesearch1").val('');
  searchCountryCode1();
  $(this).prop("checked",false);

  event.preventDefault();
});

function searchCountryCode1() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("country_codesearch1");
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

function searchCountryName1() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("country_namesearch1");
      filter = input.value.toUpperCase();
      table = document.getElementById("country_tab21");
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

function getCountryWiseState1(CTRYID_REF){
    $("#state_body").html('loading...');
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
          
          $("#STID_REF_POPUP1").val('');
          $("#STID_REF1").val('');
			    $("#CITYID_REF_POPUP1").val('');
          $("#CITYID_REF1").val('');
          $("#city_body1").html(''); 
          $("#DISTID_REF_POPUP1").val('');
          $("#DISTID_REF1").val('');
          $("#dist_body1").html(''); 
          
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
  $("#stidref_popup1").show();
});

$("#STID_REF_POPUP1").keyup(function(event){
  if(event.keyCode==13){
    $("#stidref_popup1").show();
  }
});

$("#stidref_close1").on("click",function(event){ 
  $("#stidref_popup1").hide();
});

function bindStateEvents1(){
  $('.cls_stidref').click(function(){

    var id = $(this).attr('id');
    var txtval =    $("#txt"+id+"").val();
    var texdesc =   $("#txt"+id+"").data("desc")

    $("#STID_REF_POPUP1").val(texdesc);
    $("#STID_REF1").val(txtval);
	
	var CTRYID_REF	=	$("#CTRYID_REF1").val();
	
	getStateWiseCity1(CTRYID_REF,txtval);
	
	$("#STID_REF_POPUP1").blur(); 
	$("#CITYID_REF_POPUP1").focus(); 
	
    $("#stidref_popup1").hide();

    $("#state_codesearch1").val('');
    $("#state_namesearch1").val('');
    searchStateCode1();

    $(this).prop("checked",false);

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

function searchStateName1() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("state_namesearch1");
      filter = input.value.toUpperCase();
      table = document.getElementById("state_tab21");
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

function getStateWiseCity1(CTRYID_REF,STID_REF){
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
          
            $("#CITYID_REF_POPUP1").val('');
            $("#CITYID_REF1").val('');

            $("#DISTID_REF_POPUP1").val('');
            $("#DISTID_REF1").val('');
            $("#dist_body1").html('');
            
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
  $("#cityidref_popup1").show();
});

$("#CITYID_REF_POPUP1").keyup(function(event){
  if(event.keyCode==13){
    $("#cityidref_popup1").show();
  }
});

$("#cityidref_close1").on("click",function(event){ 
  $("#cityidref_popup1").hide();
});

function bindCityEvents1(){
	$('.cls_cityidref').click(function(){

		var id = $(this).attr('id');
		var txtval =    $("#txt"+id+"").val();
		var texdesc =   $("#txt"+id+"").data("desc")

		$("#CITYID_REF_POPUP1").val(texdesc);
    $("#CITYID_REF1").val(txtval);
    
    var CTRYID_REF	=	$("#CTRYID_REF1").val();
    var STID_REF	=	$("#STID_REF1").val();

	  getCityWiseDist1(CTRYID_REF,STID_REF,txtval);
	
    $("#CITYID_REF_POPUP1").blur(); 
	  $("#DISTID_REF_POPUP1").focus(); 

		$("#cityidref_popup1").hide();
		
    $("#city_codesearch1").val('');
    $("#city_namesearch1").val('');
		searchCityCode1();
    $(this).prop("checked",false);
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

function searchCityName1() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("city_namesearch1");
      filter = input.value.toUpperCase();
      table = document.getElementById("city_tab21");
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

function getCityWiseDist1(CTRYID_REF,STID_REF,CITYID_REF){
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
          
            $("#DISTID_REF_POPUP1").val('');
            $("#DISTID_REF1").val('');            
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
  $("#distidref_popup1").show();
});

$("#DISTID_REF_POPUP1").keyup(function(event){
  if(event.keyCode==13){
    $("#distidref_popup1").show();
  }
});

$("#distidref_close1").on("click",function(event){ 
  $("#distidref_popup1").hide();
});

function bindDistEvents1(){
	$('.cls_distidref').click(function(){

		var id = $(this).attr('id');
		var txtval =    $("#txt"+id+"").val();
		var texdesc =   $("#txt"+id+"").data("desc")

		$("#DISTID_REF_POPUP1").val(texdesc);
		$("#DISTID_REF1").val(txtval);

		$("#distidref_popup1").hide();
		
    $("#dist_codesearch1").val('');
    $("#dist_namesearch1").val('');
		searchDistCode1();

    $(this).prop("checked",false);
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

function searchDistName1() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("dist_namesearch1");
      filter = input.value.toUpperCase();
      table = document.getElementById("dist_tab21");
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

//======================================SAVE FUNCTION==============================

$('#btnAdd').on('click', function() {
    var viewURL = '{{route("master",[$FormId,"add"])}}';
    window.location.href=viewURL;
});

$('#btnExit').on('click', function() {
  var viewURL = '{{route('home')}}';
  window.location.href=viewURL;
});

var formResponseMst = $( "#frm_mst_add" );
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

function validateSingleElemnet(element_id){
  var validator =$("#frm_mst_add" ).validate();
  if(validator.element( "#"+element_id+"" )){
    checkDuplicateCode();
  }
}

function checkDuplicateCode(){
  var getDataForm = $("#frm_mst_add");
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
    var formReqData = $("#frm_mst_add");
    if(formReqData.valid()){
      validateForm();
    }
});

window.fnSaveData = function (){
    event.preventDefault();

    $("#OkBtn1").hide();
    //var getDataForm = $("#frm_mst_add");
    //var formData = getDataForm.serialize();

    var formData = new FormData($("#frm_mst_add")[0]);
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url:'{{route("master",[$FormId,"save"])}}',
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
                $("#OkBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text(data.msg);
                $(".text-danger").hide();
                $("#frm_mst_add").trigger("reset");
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

$("#btnUndo").on("click", function(){
    $("#AlertMessage").text("Do you want to erase entered information in this record?");
    $("#alert").modal('show');
    $("#YesBtn").data("funcname","fnUndoYes");
    $("#YesBtn").show();
    $("#NoBtn").data("funcname","fnUndoNo");
    $("#NoBtn").show();    
    $("#OkBtn").hide();
    $("#OkBtn1").hide();
    $("#NoBtn").focus();
});

window.fnUndoYes = function (){
  window.location.href = "{{route('master',[$FormId,'add'])}}";
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
    $("#OkBtn1").hide();
    $(".text-danger").hide();
   // window.location.href = '{{route("master",[$FormId,"index"]) }}';
});

$("#OkBtn1").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn").hide();
    $("#OkBtn1").hide();
    $("#"+$(this).data('focusname')).focus();
    //window.location.href = '{{route("master",[$FormId,"index"]) }}';
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

$(document).ready(function(e){
  $('#Row_Count2').val("1");
  $("#example2").on('click', '.add', function() {
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
    $tr.closest('table').append($clone);         
    var rowCount = $('#Row_Count2').val();
    rowCount = parseInt(rowCount)+1;
    $('#Row_Count2').val(rowCount);
    $clone.find('.remove').removeAttr('disabled'); 
    event.preventDefault();
  });

  $("#example2").on('click', '.remove', function() {

    var rowCount = $('#Row_Count2').val();

    if (rowCount > 1) {
        $(this).closest('tbody').remove();     
    } 
    
    if (rowCount <= 1) { 
        $(document).find('.remove').prop('disabled', false);  
    }
    event.preventDefault();
  }); 
  

$('#Row_Count3').val("1");
  $("#example3").on('click', '.add', function() {
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
    $tr.closest('table').append($clone);         
    var rowCount = $('#Row_Count3').val();
    rowCount = parseInt(rowCount)+1;
    $('#Row_Count3').val(rowCount);
    $clone.find('.remove').removeAttr('disabled'); 
    event.preventDefault();
  });

  $("#example3").on('click', '.remove', function() {

    var rowCount = $('#Row_Count3').val();

    if (rowCount > 1) {
        $(this).closest('tbody').remove();     
    } 
    
    if (rowCount <= 1) { 
        $(document).find('.remove').prop('disabled', false);  
    }
    event.preventDefault();
  }); 


  $('#Row_Count4').val("1");
  $("#example4").on('click', '.add', function() {
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
    $tr.closest('table').append($clone);         
    var rowCount = $('#Row_Count4').val();
    rowCount = parseInt(rowCount)+1;
    $('#Row_Count4').val(rowCount);
    $clone.find('.remove').removeAttr('disabled'); 
    event.preventDefault();
  });

  $("#example4").on('click', '.remove', function() {

    var rowCount = $('#Row_Count4').val();

    if (rowCount > 1) {
        $(this).closest('tbody').remove();     
    } 
    
    if (rowCount <= 1) { 
        $(document).find('.remove').prop('disabled', false);  
    }
    event.preventDefault();
  });


  $('#Row_Count5').val("1");
  $("#example5").on('click', '.add', function() {
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
    $tr.closest('table').append($clone);         
    var rowCount = $('#Row_Count5').val();
    rowCount = parseInt(rowCount)+1;
    $('#Row_Count5').val(rowCount);
    $clone.find('.remove').removeAttr('disabled'); 
    event.preventDefault();
  });

  $("#example5").on('click', '.remove', function() {

    var rowCount = $('#Row_Count5').val();

    if (rowCount > 1) {
        $(this).closest('tbody').remove();     
    } 
    
    if (rowCount <= 1) { 
        $(document).find('.remove').prop('disabled', false);  
    }
    event.preventDefault();
  }); 


  $('#Row_Count6').val("1");
  $("#example6").on('click', '.add', function() {
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
    $tr.closest('table').append($clone);         
    var rowCount = $('#Row_Count6').val();
    rowCount = parseInt(rowCount)+1;
    $('#Row_Count6').val(rowCount);
    $clone.find('.remove').removeAttr('disabled'); 
    event.preventDefault();
  });

  $("#example6").on('click', '.remove', function() {

    var rowCount = $('#Row_Count6').val();

    if (rowCount > 1) {
        $(this).closest('tbody').remove();     
    } 
    
    if (rowCount <= 1) { 
        $(document).find('.remove').prop('disabled', false);  
    }
    event.preventDefault();
  });  
   
});

//=================================VALIDATION================================= 

function validateForm(){

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
      $("#YesBtn").data("funcname","fnSaveData");
      $("#YesBtn").focus();
      $("#OkBtn").hide();
      highlighFocusBtn('activeYes');
    }
}

function getPParentRented(id){
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

window.onload = function(){
      var strdd = <?php echo json_encode($objDD); ?>;
      if($.trim(strdd)==""){     
        $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn").show();
          $("#AlertMessage").text('Please contact to administrator for creating document numbering.');
          $("#alert").modal('show');
          $("#OkBtn").focus();
          highlighFocusBtn('activeOk');
      } 
    };

</script>

@endpush