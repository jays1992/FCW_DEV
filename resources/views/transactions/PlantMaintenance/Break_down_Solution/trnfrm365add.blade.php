@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
  <div class="row">
    <div class="col-lg-2"><a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Breakdown Complaint Solution</a></div>

    <div class="col-lg-10 topnav-pd">
      <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
      <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
      <button class="btn topnavbt" id="btnSave" ><i class="fa fa-floppy-o"></i> Save</button>
      <button style="display:none" class="btn topnavbt buttonload"> <i class="fa fa-refresh fa-spin"></i> {{Session::get('save')}}</button>
      <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
      <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
      <button class="btn topnavbt" id="btnUndo"  ><i class="fa fa-undo"></i> Undo</button>
      <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
      <button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-thumbs-o-up"></i> Approved</button>
      <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
      <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>  
    </div>
  </div>
</div>


<form id="frm_trn_add" onsubmit="return validateForm()"  method="POST" class="needs-validation"  >  
 
  <div class="container-fluid purchase-order-view">
        
    @csrf
    <div class="container-fluid filter">

      <div class="inner-form">
      
        <div class="row">
            <div class="col-lg-2 pl"><p>Solution No</p></div>
            <div class="col-lg-2 pl">
            <input type="text" name="BDSL_NO" id="BDSL_NO" value="{{$docarray['DOC_NO']}}" {{$docarray['READONLY']}} class="form-control" maxlength="{{$docarray['MAXLENGTH']}}" autocomplete="off" style="text-transform:uppercase" >
            <script>docMissing(@json($docarray['FY_FLAG']));</script>
            
            </div>
            
            <div class="col-lg-2 pl"><p>Solution Date</p></div>
            <div class="col-lg-2 pl">
                <input type="date" name="BDSL_DATE" id="BDSL_DATE" onchange='checkPeriodClosing("{{$FormId}}",this.value,1),getDocNoByEvent("BDSL_NO",this,@json($doc_req))' value="{{ old('BDSL_DATE') }}" class="form-control mandatory" autocomplete="off" placeholder="dd/mm/yyyy" >
            </div>
            
            <div class="col-lg-2 pl"><p>Complaint Log No</p></div>
            <div class="col-lg-2 pl">
                    <input type="text" name="ComplaintLog_popup" id="txtComplaintLog_popup" class="form-control mandatory"  autocomplete="off"  readonly/>
                    <input type="hidden" name="BDCLID_REF" id="BDCLID_REF" class="form-control" autocomplete="off" />

                                                             
            </div>
        </div>

        <div class="row">
            <div class="col-lg-2 pl"><p>Complaint Log Date</p></div>
            <div class="col-lg-2 pl">
                <input type="date" name="BDCL_DATE" id="BDCL_DATE" class="form-control mandatory"  autocomplete="off" placeholder="dd/mm/yyyy" readonly/>
            </div>
            
            <div class="col-lg-2 pl"><p>Complaint By</p></div>
            <div class="col-lg-2 pl">
                <input type="text" name="COMPLAINT_BY" id="COMPLAINT_BY"  class="form-control mandatory" autocomplete="off" readonly >
            </div>
            
            <div class="col-lg-2 pl"><p>Department</p></div>
            <div class="col-lg-2 pl">
                <input type="text" name="DEPARTMENT_NAME" id="DEPARTMENT_NAME"  class="form-control mandatory" autocomplete="off" readonly >
                <input type="hidden" name="DEPARTMENTID_REF" id="DEPARTMENTID_REF"  class="form-control mandatory" autocomplete="off" readonly >
            </div>
        </div>

        <div class="row">
            <div class="col-lg-2 pl"><p>Priority</p></div>
            <div class="col-lg-2 pl">
                <input type="text" name="PRIORITY_NAME" id="PRIORITY_NAME" class="form-control mandatory"  autocomplete="off" readonly/>
                <input type="hidden" name="PRIORITYID_REF" id="PRIORITYID_REF" class="form-control mandatory"  autocomplete="off" readonly/>
            </div>
            
            <div class="col-lg-2 pl"><p>Machine / Genset Code</p></div>
            <div class="col-lg-2 pl">
                <input type="text" name="MACHINE_CODE" id="MACHINE_CODE"  class="form-control mandatory" autocomplete="off" readonly >
                <input type="hidden" name="MACHINEID_REF" id="MACHINEID_REF"  class="form-control mandatory" autocomplete="off" readonly >
            </div>
            
            <div class="col-lg-2 pl"><p>Description</p></div>
            <div class="col-lg-2 pl">
                <input type="text" name="MACHINE_NAME" id="MACHINE_NAME"  class="form-control mandatory" autocomplete="off" readonly >
            </div>
        </div>
        <div class="row">
            <div class="col-lg-2 pl"><p>Problem Log Detail</p></div>
            <div class="col-lg-8 pl">
                <input type="text" name="PROBLEM_LOG_DET" id="PROBLEM_LOG_DET"  class="form-control mandatory" autocomplete="off" readonly >
            </div>
        </div>

        <div class="row">
            <div class="col-lg-2 pl"><p>Breakdown Reason Code 1</p></div>
            <div class="col-lg-2 pl">
                <input type="text" name="REASON_CODE1" id="REASON_CODE1"  class="form-control mandatory" autocomplete="off" readonly >
                <input type="hidden" name="REASON1_REF" id="REASON1_REF"  class="form-control mandatory" autocomplete="off" readonly >
            </div>
            <div class="col-lg-2 pl"><p>Breakdown Reason Description 1</p></div>
            <div class="col-lg-6 pl">
                <input type="text" name="REASON_NAME1" id="REASON_NAME1"  class="form-control mandatory" autocomplete="off" readonly >
            </div>
        </div>
        <div class="row">
            <div class="col-lg-2 pl"><p>Breakdown Reason Code 2</p></div>
            <div class="col-lg-2 pl">
                <input type="text" name="REASON_CODE2" id="REASON_CODE2"  class="form-control mandatory" autocomplete="off" readonly >
                <input type="hidden" name="REASON2_REF" id="REASON2_REF"  class="form-control mandatory" autocomplete="off" readonly >
            </div>
            <div class="col-lg-2 pl"><p>Breakdown Reason Description 1</p></div>
            <div class="col-lg-6 pl">
                <input type="text" name="REASON_NAME2" id="REASON_NAME2"  class="form-control mandatory" autocomplete="off" readonly >
            </div>
        </div>


        <div class="row">
            <div class="col-lg-2 pl"><p>Remarks 1</p></div>
            <div class="col-lg-4 pl">
                <input type="text" name="REMARKS1" id="REMARKS1" class="form-control mandatory"  autocomplete="off"  />  
            </div>
            <div class="col-lg-2 pl"><p>Remarks 2</p></div>
            <div class="col-lg-4 pl">
                <input type="text" name="REMARKS2" id="REMARKS2" class="form-control mandatory"  autocomplete="off"  />
            </div>     
        </div>
        <div class="row">
            <div class="col-lg-2 pl"><p>Own Employee</p></div>
            <div class="col-lg-1 pl">
                <input type="checkbox" name="EMPLOYEE_TYPE" id="Chk_OwnEmployee" checked value="own_employee" />  
            </div>
            <div class="col-lg-2 pl"><p>Outside Resource</p></div>
            <div class="col-lg-1 pl">
                <input type="checkbox" name="EMPLOYEE_TYPE" id="Chk_OutsideResource" value="outside_employee"  />
            </div>     
        </div>
        <div class="row">
            <div class="col-lg-2 pl"><p>Outsider Name</p></div>
            <div class="col-lg-2 pl">
                <input type="text" name="Outsider_Name" id="Outsider_Name" class="form-control mandatory"  autocomplete="off"  />  
            </div>
            <div class="col-lg-2 pl"><p>Contact Number</p></div>
            <div class="col-lg-2 pl">
                <input type="text" name="Contact_Number" id="Contact_Number" class="form-control mandatory"  autocomplete="off"  />
            </div> 
            <div class="col-lg-2 pl"><p>Company Name</p></div>
            <div class="col-lg-2 pl">
                <input type="text" name="Company_Name" id="Company_Name" class="form-control mandatory"  autocomplete="off"  />
            </div>    
        </div>
        <div class="row">
            <div class="col-lg-2 pl"><p>Maintenance Person Name 1</p></div>
            <div class="col-lg-2 pl">
        <input type="text" name="EMP_REF1" id="EMP_REF1_popup" class="form-control mandatory" onclick="get_employee('EMP_REF1');"  autocomplete="off" readonly/>
        <input type="hidden" name="EMP_REF1" id="EMP_REF1" class="form-control" autocomplete="off" />
            </div>
            <div class="col-lg-2 pl"><p>Maintenance Start Date</p></div>
            <div class="col-lg-2 pl">
                <input type="date" name="Main_Start_Date" id="Main_Start_Date" class="form-control mandatory"  autocomplete="off" placeholder="dd/mm/yyyy"  />
            </div> 
            <div class="col-lg-2 pl"><p>Maintenance Start Time</p></div>
            <div class="col-lg-2 pl">
                <input type="time" name="Main_Start_Time" id="Main_Start_Time" class="form-control mandatory"  autocomplete="off"  />
            </div>    
        </div>
        <div class="row">
            <div class="col-lg-2 pl"><p>Maintenance Person Name 2</p></div>
            <div class="col-lg-2 pl">
        <input type="text" name="EMP_REF2" id="EMP_REF2_popup" class="form-control mandatory" onclick="get_employee('EMP_REF2');"  autocomplete="off" readonly/>
        <input type="hidden" name="EMP_REF2" id="EMP_REF2" class="form-control" autocomplete="off" />
            </div>
            <div class="col-lg-2 pl"><p>Fault Detect 1</p></div>
            <div class="col-lg-6 pl">
                <input type="text" name="Fault_Detect_1" id="Fault_Detect_1" class="form-control mandatory"  autocomplete="off"  />
            </div>     
        </div>
        <div class="row">
            <div class="col-lg-2 pl"><p>Maintenance Person Name 3</p></div>
            <div class="col-lg-2 pl">
    <input type="text" name="EMP_REF3" id="EMP_REF3_popup" class="form-control mandatory" onclick="get_employee('EMP_REF3');"  autocomplete="off" readonly/>
    <input type="hidden" name="EMP_REF3" id="EMP_REF3" class="form-control" autocomplete="off" />
            </div>
            <div class="col-lg-2 pl"><p>Fault Detect 2</p></div>
            <div class="col-lg-6 pl">
                <input type="text" name="Fault_Detect_2" id="Fault_Detect_2" class="form-control mandatory"  autocomplete="off"  />
            </div>     
        </div>
        <div class="row">	
            <div class="col-lg-2 pl"><p>Checklist No</p></div>
            <div class="col-lg-2 pl">
                <input type="text" name="CHECKLIST_popup" id="txtchecklist_popup" class="form-control mandatory"  autocomplete="off" readonly/>
                        <input type="hidden" name="CHECKLIST_REF" id="CHECKLIST_REF" class="form-control" autocomplete="off" />
            </div>
            <div class="col-lg-2 pl"><p>Checklist Description</p></div>
            <div class="col-lg-6 pl">
              <input type="text" name="ChecklistDesc_popup" id="ChecklistDesc_popup" class="form-control"  autocomplete="off"  readonly/>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-2 pl"><p>Action Taken 1</p></div>
            <div class="col-lg-4 pl">
                <input type="text" name="Action_Taken1" id="Action_Taken1" class="form-control mandatory"  autocomplete="off"  />  
            </div>
            <div class="col-lg-2 pl"><p>Action Taken 2</p></div>
            <div class="col-lg-4 pl">
                <input type="text" name="Action_Taken2" id="Action_Taken2" class="form-control mandatory"  autocomplete="off"  />
            </div>     
        </div>
        <div class="row">
            <div class="col-lg-2 pl"><p>Status</p></div>
            <div class="col-lg-2 pl">
                <select  class="form-control" name="drpstatus" id="drpstatus" > 
                  <option value="">Select</option>
                  <option value="Work In Progress">Work In Progress</option>
                  <option value="Completed">Completed</option>
                  <option value="Temporary Solved">Temporary Solved</option>
                </select>  
            </div>
            <div class="col-lg-2 pl"><p>Maintenance End Date</p></div>
            <div class="col-lg-2 pl">
                <input type="date" name="Main_End_Date" id="Main_End_Date" class="form-control mandatory"  autocomplete="off" placeholder="dd/mm/yyyy"  />
            </div> 
            <div class="col-lg-2 pl"><p>Maintenance End Time</p></div>
            <div class="col-lg-2 pl">
                <input type="time" name="Main_End_Time" id="Main_End_Time" class="form-control mandatory"  autocomplete="off"  />
            </div>     
        </div>
        <div class="row">
            <div class="col-lg-2 pl"><p>Remarks in case of BD Pending</p></div>
            <div class="col-lg-10 pl">
                <input type="text" name="REMARKS_PENDING" id="REMARKS_PENDING" class="form-control mandatory"  autocomplete="off"  />  
            </div>    
        </div>
      </div>

      <div class="container-fluid purchase-order-view">

        <div class="row">
          <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#Checklist">Checklist</a></li>
            <li><a data-toggle="tab" href="#Consumed">Spare Parts Consumed</a></li>
            <li><a data-toggle="tab" href="#Return">Spare Parts Return</a></li>
          </ul>             
          <div class="tab-content">

            <div id="Checklist" class="tab-pane fade in active">
                <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:400px;margin-top:10px;" >
                    <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                      <thead id="thead1"  style="position: sticky;top: 0">
                                                    
                        <tr>
                          <th>MP Code</th>
                          <th>MP Description</th>
                          <th>MSP Code</th>
                          <th>MSP Description</th>
                          <th>Standard Value</th>
                          <th>Actual</th>
                          <th>Remarks</th>
                         
                        </tr>
                      </thead>
                      <tbody id="tbody_checklist_grid">
                      <tr  class="participantRow" >
                         <td hidden><input type="text" name="checklistcount[]"  > </td>
           
                         <td hidden><input type="text" name="MCKLIST_DID[]" id="MCKLIST_DID_0" value="" > </td>
                                                                 
                         <td><input type="text" name="MP_CODE[]"  class="form-control" value=""  autocomplete="off"  readonly /></td>
          
                         <td><input type="text" name="MP_DESC[]"  class="form-control" value=""  autocomplete="off"  readonly /></td>
          
                         <td><input type="text" name="MSP_CODE[]"  class="form-control" value=""  autocomplete="off"  readonly /></td>
          
                         <td><input type="text" name="MSP_DESC[]"  class="form-control" value=""  autocomplete="off"  readonly /></td>
          
                           
            
           
                       <td><input type="text" name="STANDARD_VALUE[]"   value="" class="form-control three-digits" maxlength="15"  autocomplete="off"  readonly/></td>

                       <td><input type="text" name="ACTUAL_VALUE[]" id="ACTUAL_VALUE_0"  value="" class="form-control three-digits" maxlength="15"  autocomplete="off"   /></td>

                       <td><input type="text" name="REMARKS[]"  class="form-control" value=""  autocomplete="off"   /></td>


                   
                             
                  
                 
                         </tr>
                  
                      <tr></tr>
                      </tbody>
                    </table>
                </div>	
            </div>




            <div id="Consumed" class="tab-pane">
                <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:400px;margin-top:10px; " >
                    <table id="example3" class="display nowrap table table-striped table-bordered itemlist"  style="height:auto !important; width:30%">
                    <thead id="thead3"  style="position: sticky;top: 0">
                                                    
                    <tr>
                    <th width="70px">Sl. No. <input class="form-control" type="hidden" name="Row_Count" id ="Row_Count"></th>
                        <th>Spare Parts Consumed</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                      <tr class="participantRow3">
                            <td><input type="text" name="SL_NO1_0" id="SL_NO1_0" class="form-control" autocomplete="off" readonly value="1" readonly ></td>
                            <td><input type="text" name="SPARE_PARTS_CONSUMED_0" id="SPARE_PARTS_CONSUMED_0" class="form-control" autocomplete="off" ></td>
                            <td align="center" style="width: 100;">
                                <button class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></button>
                                <button class="btn remove" title="Delete" id="btnremove" data-toggle="tooltip" disabled=""><i class="fa fa-trash"></i></button>
                            </td>
                          </tr>
                      </tbody>
                    </table>
                </div>	
            </div>    


            <div id="Return" class="tab-pane">
                <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:400px;margin-top:10px; " >
                    <table id="example4" class="display nowrap table table-striped table-bordered itemlist"  style="height:auto !important; width:50%">
                      <thead id="thead4"  style="position: sticky;top: 0">
                                                    
                        <tr>
                          <th width="70px">Sl. No. <input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1"></th>
                          <th>Spare Parts Return</th>
                          <th>Return To</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                          <tr class="participantRow4">
                          <td><input type="text" name="SL_NO2_0" id="SL_NO2_0" class="form-control" autocomplete="off" readonly value="1" readonly ></td>
                            <td><input type="text" name="SPARE_PART_NAME_0" id="SPARE_PART_NAME_0" class="form-control" autocomplete="off" ></td>
                            <td><input type="text" name="SPARE_RETURN_TO_0" id="SPARE_RETURN_TO_0" class="form-control" autocomplete="off" ></td>
                            <td align="center" style="width: 100;">
                                <button class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></button>
                                <button class="btn remove" title="Delete" id="btnremove" data-toggle="tooltip" disabled=""><i class="fa fa-trash"></i></button>
                            </td>
                          </tr>
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


<!-- Breakdown Dropdown -->
<div id="ComplaintLog_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:60%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='Complaint_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Breakdown Complaint Log List</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="ComplaintTable" class="display nowrap table  table-striped table-bordered" style="width:100%;">
    <thead>
    <tr>
            <th style="width:10%;"></th>
            <th style="width:30%;">Code</th>
            <th style="width:60%;">Date</th>
    </tr>
    </thead>
    <tbody>
    <tr>
    <th style="text-align:center; width:10%;">&#10004;</th>
   <td style="width:30%;"> 
    <input type="text" id="Complaintcodesearch" class="form-control" onkeyup="ComplaintCodeFunction()">
    </td>
    <td style="width:60%;">
    <input type="text" id="Complaintnamesearch" class="form-control" onkeyup="ComplaintNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="ComplaintTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          
        </thead>
        <tbody >     
        @foreach ($objBDComplaints as $index=>$objBDComplaintsRow)
        <tr >
        <td style="text-align:center; width:10%"> <input type="checkbox" name="complaints[]" id="departmentidcode_{{ $index }}" class="clsspid_complaints"  value="{{ $objBDComplaintsRow->BDCL_ID }}" ></td>



          <td style="width:30%">{{ $objBDComplaintsRow->BDCL_NO }}
          <input type="hidden" id="txtdepartmentidcode_{{ $index }}"
           data-desc="{{ $objBDComplaintsRow->BDCL_NO }}"
           data-date="{{ $objBDComplaintsRow->BDCL_DATE }}"
           data-complaintby="{{ $objBDComplaintsRow->COMPLAINT_BY }}"
           data-complaintto="{{ $objBDComplaintsRow->COMPLAINT_TO }}"
           data-deptid_ref="{{ $objBDComplaintsRow->DEPID_REF }}"
           data-dept_name="{{ $objBDComplaintsRow->DEPARTMENT_CODE }}-{{ $objBDComplaintsRow->DEPARTMENT_NAME }}"
           data-priorityid_ref="{{ $objBDComplaintsRow->PRIORITYID_REF }}"
           data-priority_name="{{ $objBDComplaintsRow->PRIORITYCODE }}-{{ $objBDComplaintsRow->PRIORITY_DESC }}"
           data-machineid_ref="{{ $objBDComplaintsRow->MACHINEID_REF }}"
           data-machinecode="{{ $objBDComplaintsRow->MACHINE_NO }}"
           data-machinename="{{ $objBDComplaintsRow->MACHINE_DESC }}"
           data-prob_detail="{{ $objBDComplaintsRow->PLOG_DETAILS }}"
           data-reasonref1="{{ $objBDComplaintsRow->BD_REASONID_REF1 }}"
           data-reasonref2="{{ $objBDComplaintsRow->BD_REASONID_REF2 }}"
           data-rasoncode1="{{ $objBDComplaintsRow->REASON1_CODE }}"
           data-rasonname1="{{ $objBDComplaintsRow->REASON1_DESC }}"
           data-rasoncode2="{{ $objBDComplaintsRow->REASON2_CODE }}"
           data-rasonname2="{{ $objBDComplaintsRow->REASON2_DESC }}"
           data-remarks1="{{ $objBDComplaintsRow->REMARKS_1 }}"
           data-remarks2="{{ $objBDComplaintsRow->REMARKS_2 }}"
           
           
           value="{{ $objBDComplaintsRow->BDCL_ID }}"/>
          </td>
          <td style="width:60%">{{ $objBDComplaintsRow-> BDCL_DATE }} </td>
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
<!--  Departiment  Dropdown ends here -->




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
          <button class="btn alertbt" name='YesBtn' id="YesBtn" data-funcname="fnSaveData"><div id="alert-active" class="activeYes"></div>Yes</button>
          <button class="btn alertbt" name='NoBtn' id="NoBtn"   data-funcname="fnUndoNo" ><div id="alert-active" class="activeNo"></div>No</button>
          <button class="btn alertbt" name='OkBtn' id="OkBtn" style="display:none;margin-left: 90px;"><div id="alert-active" class="activeOk"></div>OK</button>
          <button class="btn alertbt" name='OkBtn1' id="OkBtn1" style="display:none;margin-left: 90px;" onclick="getFocus()"><div id="alert-active" class="activeOk1"></div>OK</button>
          <input type="hidden" id="FocusId" >
        </div>
		    <div class="cl"></div>
      </div>
    </div>
  </div>
</div>


<!-- POPUP -->
<div id="checklistpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md"  style="width:60%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='checklist_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Checklist Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="ChecklistTable" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
      <tr>
        <th style="width: 10%" align="center">Select</th> 
        <th  style="width: 40%">Code</th>
        <th style="width: 50%">Description</th>
      </tr>
    </thead>
    <tbody>
    <tr>
      <td style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
      <td style="width: 40%">
        <input type="text" autocomplete="off"  class="form-control" id="checklistcodesearch" onkeyup="ChecklistCodeFunction()"/>
      </td>
      <td style="width: 50%">
        <input type="text" autocomplete="off"  class="form-control" id="checklistdatesearch" onkeyup="ChecklistNameFunction()"/>
      </td>
    </tr>
    </tbody>
    </table>
      <table id="ChecklistTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          
        </thead>
        <tbody id="tbody_checklist">       
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- POPUP-->


<div id="Employee_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:60%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='Priority_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Employee List</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="EmployeeTable" class="display nowrap table  table-striped table-bordered" style="width:100%;">
    <thead>
    <tr>
            <th style="width:10%;"></th>
            <th style="width:30%;">Code</th>
            <th style="width:60%;">Name</th>
    </tr>
    </thead>
    <tbody>
    <tr>
    <th style="text-align:center; width:10%;">&#10004;</th>
   <td style="width:30%;"> 
    <input type="text" id="Employeecodesearch" class="form-control" onkeyup="EmployeeCodeFunction()">
    </td>
    <td style="width:60%;">
    <input type="text" id="Employeenamesearch" class="form-control" onkeyup="EmployeeNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="EmployeeTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          
        </thead>
        <tbody id="tbody_employee">

        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>


<!-- Machine Dropdown  -->
<div id="machine_popup" class="modal" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-md" style="width:60%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" id="item_closePopup1">&times;</button>
            </div>
            <div class="modal-body">
                <div class="tablename"><p>Machine / Generator List</p></div>
                <div class="single single-select table-responsive table-wrapper-scroll-y my-custom-scrollbar">
                <input type="hidden" class="mainitem_tab1">
                    <table id="MachineTable" class="display nowrap table table-striped table-bordered" style="width:100%;">
                        <thead>
                            <tr>
                            <th style="width:10%;">Select</th> 
                                <th style="width:30%;"> Code</th>
                                <th style="width:60%;"> Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                            <th style="text-align:center; width:10%;">&#10004;</th>

                       

                                <td style="width:30%;"> 
                                    <input type="text" id="Machinecodesearch" class="form-control" onkeyup="MachineCodeFunction()"  />
                                </td>
                                <td style="width:60%;">
                                    <input type="text" id="Machinenamesearch" class="form-control" onkeyup="MachineNameFunction()"  />
                                </td>
                          
                            </tr>
                        </tbody>
                    </table>
                    <table id="MachineTable2" class="display nowrap table table-striped table-bordered">
                        <thead id="thead2">
                        <tr>
        
                <td id="item_seach" colspan="4">please wait...</td>
          </tr>
                        </thead>
                        <tbody id="Machineresult">
                           
                        </tbody>
                    </table>
                </div>
                <div class="cl"></div>
            </div>
        </div>
    </div>
</div>
<!-- Item popup ends-->
@endsection

@push('bottom-css')
<style>
.text-danger{
  color:red !important;
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
  width: 100%;
  border: 1px solid #ddd;
  font-size: 11px;
}
#ItemIDTable th {
  text-align: center;
  padding: 5px;
  font-size: 11px;
  color: #0f69cc;
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
  width: 100%;
  border: 1px solid #ddd;
  font-size: 11px;
}
#ItemIDTable2 th{
  text-align: left;
  padding: 5px;
  font-size: 11px;
  color: #0f69cc;
  font-weight: 600;
}
#ItemIDTable2 td {
  text-align: left;
  padding: 5px;
  font-size: 11px;
  font-weight: 600;
}
#StoreTable {
  border-collapse: collapse;
  width: 950px;
  border: 1px solid #ddd;
  font-size: 11px;
}
#StoreTable th {
  text-align: center;
  padding: 5px;
  font-size: 11px;
  color: #0f69cc;
  font-weight: 600;
}
#StoreTable td {
  text-align: center;
  padding: 5px;
  font-size: 11px;
  font-weight: 600;
}
.qtytext{
  display: block;
  width: 100%;
  height: 24px;
  padding: 6px 6px;
  font-size: 14px;
  line-height: 1.42857143;
  color: #555;
  background-color: #fff;
  background-image: none;
  border: 1px solid #ccc;
}
</style>
@endpush

@push('bottom-scripts')
<script>
  
/*================================== BUTTON FUNCTION ================================*/
$('#btnAdd').on('click', function() {
  var viewURL = '{{route("transaction",[$FormId,"add"])}}';
  window.location.href=viewURL;
});

$('#btnExit').on('click', function() {
  var viewURL = '{{route('home')}}';
  window.location.href=viewURL;
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
  window.location.href = "{{route('transaction',[$FormId,'add'])}}";
}

$("#btnSave" ).click(function() {
    var formReqData = $("#frm_trn_add");
    if(formReqData.valid()){
      validateForm();
    }
});

$("#YesBtn").click(function(){
  $("#alert").modal('hide');
  var customFnName = $("#YesBtn").data("funcname");
  window[customFnName]();
});

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
  $("#"+$("#FocusId").val()).focus();
  $("#closePopup").click();
}

function highlighFocusBtn(pclass){
  $(".activeYes").hide();
  $(".activeNo").hide();
  $("."+pclass+"").show();
}



/*================================== VALIDATE FUNCTION =================================*/
function validateForm(){
  
  $("#FocusId").val('');

  var BDCL_DOCNO   = $.trim($("#BDSL_NO").val());
  var BDSL_DATE   = $.trim($("#BDSL_DATE").val());
  var employee_type=$('[name="EMPLOYEE_TYPE"]:checked').val();
  var BDCLID_REF    = $.trim($("#BDCLID_REF").val());
  var EMP_REF1    = $.trim($("#EMP_REF1").val());
  var Outsider_Name    = $.trim($("#Outsider_Name").val());
  var Contact_Number    = $.trim($("#Contact_Number").val());
  var Company_Name    = $.trim($("#Company_Name").val());
  var CHECKLIST_REF    = $.trim($("#CHECKLIST_REF").val());
  var drpstatus    = $.trim($("#drpstatus").val());
  var Main_Start_Date    = $.trim($("#Main_Start_Date").val());
  var Main_Start_Time    = $.trim($("#Main_Start_Time").val());
  var Main_End_Date    = $.trim($("#Main_End_Date").val());
  var Main_End_Time    = $.trim($("#Main_End_Time").val());


  
  if(BDCL_DOCNO ===""){
      $("#FocusId").val('BDCL_DOCNO');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Doc No is required.');
      $("#alert").modal('show')
      $("#OkBtn1").focus();
      return false;
  }
  else if(BDSL_DATE ===""){
      $("#FocusId").val('BDSL_DATE');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please select Date.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  }
  else if(BDCLID_REF ===""){
      $("#FocusId").val('txtComplaintLog_popup');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please select Complaint Log No.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(employee_type ==="own_employee" && EMP_REF1===""){
      $("#FocusId").val('EMP_REF1_popup');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please select Employee');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(employee_type ==="outside_employee" && Outsider_Name===""){
      $("#FocusId").val('Outsider_Name');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please Enter Outside Name');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(employee_type ==="outside_employee" && Contact_Number===""){
      $("#FocusId").val('Contact_Number');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please Enter Contact No.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(employee_type ==="outside_employee" && Company_Name===""){
      $("#FocusId").val('Company_Name');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please Enter Company Name.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(Main_Start_Date ===""){
      $("#FocusId").val('Main_Start_Date');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please Select Maintenance Start Date.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(Main_Start_Time ===""){
      $("#FocusId").val('Main_Start_Time');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please Select Maintenance Start Time.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(CHECKLIST_REF ===""){
      $("#FocusId").val('txtchecklist_popup');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please Select Checklist No.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(drpstatus ===""){
      $("#FocusId").val('drpstatus');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please Select Status');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(Main_End_Date ===""){
      $("#FocusId").val('Main_End_Date');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please Select Maintenance End Date.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(Main_End_Date ===""){
      $("#FocusId").val('Main_End_Date');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please Select Maintenance End Time.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  }else{



event.preventDefault();
var allblank = [];
$('#example2').find('.participantRow').each(function(){
      if($.trim($(this).find("[id*=ACTUAL_VALUE]").val())!=""){
       allblank.push('true');
           }
      else{
            allblank.push('false');
        } 

      }); }


    if(jQuery.inArray("false", allblank) !== -1){
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please Enter Actual Value in  Checklist Tab.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      }
      else if(checkPeriodClosing('{{$FormId}}',$("#BDSL_DATE").val(),0) ==0){
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text(period_closing_msg);
        $("#alert").modal('show');
        $("#OkBtn1").focus();
      }
    else{
      checkDuplicateCode();
    }
  

}
  



/*================================== CHECK DUPLICATE FUNCTION =================================*/
function checkDuplicateCode(){

  var trnFormReq  = $("#frm_trn_add");
  var formData    = trnFormReq.serialize();

  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  $.ajax({
      url:'{{route("transaction",[$FormId,"codeduplicate"])}}',
      type:'POST',
      data:formData,
      success:function(data) {
          if(data.exists) {
              $(".text-danger").hide();
              showError('ERROR_BDCL_DOCNO',data.msg);
              $("#BDCL_DOCNO").focus();
          }
          else{
            $("#alert").modal('show');
            $("#AlertMessage").text('Do you want to save to record.');
            $("#YesBtn").data("funcname","fnSaveData");
            $("#YesBtn").focus();
            $("#OkBtn").hide();
            highlighFocusBtn('activeYes');
          }                                
      },
      error:function(data){
        console.log("Error: Something went wrong.");
      },
  });
}

/*================================== Save FUNCTION =================================*/
window.fnSaveData = function (){

event.preventDefault();

var trnFormReq  = $("#frm_trn_add");
var formData    = trnFormReq.serialize();

$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});
$("#btnSave").hide(); 
$(".buttonload").show(); 
$("#btnApprove").prop("disabled", true);
$.ajax({
  url:'{{ route("transaction",[$FormId,"save"])}}',
  type:'POST',
  data:formData,
  success:function(data) {
    $(".buttonload").hide(); 
    $("#btnSave").show();   
    $("#btnApprove").prop("disabled", false);

    if(data.errors) {
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").show();
      $("#AlertMessage").text(data.msg);
      $("#alert").modal('show');
      $("#OkBtn").focus();
      $(".text-danger").show();
    }
    else if(data.success) {                   
      console.log("succes MSG="+data.msg);
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").show();
      $("#AlertMessage").text(data.msg);
      $("#alert").modal('show');
      $("#OkBtn").focus();
      $(".text-danger").hide();
    }
    
  },
  error:function(data){
      $(".buttonload").hide(); 
      $("#btnSave").show();   
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
/*================================== POPUP SHORTING FUNCTION =================================*/
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


  //COMPLATINT Dropdown starts here 
  let department = "#ComplaintTable2";
      let department2 = "#ComplaintTable";
      let departmentheaders = document.querySelectorAll(department2 + " th");

      // Sort the table element when clicking on the table headers
      departmentheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(department, ".clsspid_complaints", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function ComplaintCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Complaintcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("ComplaintTable2");
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

  function ComplaintNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Complaintnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("ComplaintTable2");
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

  $('#txtComplaintLog_popup').click(function(event){

    showSelectedCheck($("#BDCLID_REF").val(),"complaints");
         $("#ComplaintLog_popup").show();
      });

      $("#Complaint_closePopup").click(function(event){
        $("#ComplaintLog_popup").hide();
      });
      $(".clsspid_complaints").click(function(){
        var fieldid         = $(this).attr('id');
        var txtval          =    $("#txt"+fieldid+"").val();
        var texdesc         =   $("#txt"+fieldid+"").data("desc");
        var date            =   $("#txt"+fieldid+"").data("date");
        var complaintby     =   $("#txt"+fieldid+"").data("complaintby");
        var complaintto     =   $("#txt"+fieldid+"").data("complaintto");
        var deptid_ref      =   $("#txt"+fieldid+"").data("deptid_ref");
        var dept_name       =   $("#txt"+fieldid+"").data("dept_name");
        var priorityid_ref  =   $("#txt"+fieldid+"").data("priorityid_ref");
        var priority_name   =   $("#txt"+fieldid+"").data("priority_name");
        var machineid_ref   =   $("#txt"+fieldid+"").data("machineid_ref");
        var machinecode     =   $("#txt"+fieldid+"").data("machinecode");
        var machinename     =   $("#txt"+fieldid+"").data("machinename");
        var prob_detail     =   $("#txt"+fieldid+"").data("prob_detail");
        var reasonref1      =   $("#txt"+fieldid+"").data("reasonref1");
        var reasonref2      =   $("#txt"+fieldid+"").data("reasonref2");
        var rasonname1      =   $("#txt"+fieldid+"").data("rasonname1");
        var rasoncode1      =   $("#txt"+fieldid+"").data("rasoncode1");
        var rasonname2      =   $("#txt"+fieldid+"").data("rasonname2");
        var rasoncode2      =   $("#txt"+fieldid+"").data("rasoncode2");
        var remarks1        =   $("#txt"+fieldid+"").data("remarks1");
        var remarks2        =   $("#txt"+fieldid+"").data("remarks2");
 


        $('#txtComplaintLog_popup').val(texdesc);
        $('#BDCLID_REF').val(txtval);
        $('#BDCL_DATE').val(date);
        $('#COMPLAINT_BY').val(complaintby);
        $('#DEPARTMENT_NAME').val(dept_name);
        $('#DEPARTMENTID_REF').val(deptid_ref);
        $('#PRIORITY_NAME').val(priority_name);
        $('#PRIORITYID_REF').val(priorityid_ref);
        $('#MACHINE_CODE').val(machinecode);
        $('#MACHINE_NAME').val(machinename);
        $('#MACHINEID_REF').val(machineid_ref);
        $('#PROBLEM_LOG_DET').val(prob_detail);
        $('#REASON1_REF').val(reasonref1);
        $('#REASON_CODE1').val(rasoncode1);
        $('#REASON_NAME1').val(rasonname1);
        $('#REASON2_REF').val(reasonref2);
        $('#REASON_CODE2').val(rasoncode2);
        $('#REASON_NAME2').val(rasonname2);
        $('#REMARKS1').val(remarks1);
        $('#REMARKS2').val(remarks2);




        $("#ComplaintLog_popup").hide();        
        $("#Complaintcodesearch").val(''); 
        $("#Complaintnamesearch").val('');    
        event.preventDefault();
      });

  //Department Dropdown Ends here
  function loadEmployee(EMP_TYPE){
   
   $("#tbody_employee").html('');
   $.ajaxSetup({
     headers: {
       'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
     }
   });
 
   $.ajax({
     url:'{{route("transaction",[$FormId,"get_employee"])}}',
     type:'POST',
     data:{'EMP_TYPE':EMP_TYPE},
     success:function(data) {
       $("#tbody_employee").html(data); 
       bindEmployeeEvents(EMP_TYPE);
       showSelectedCheck($("#"+EMP_TYPE).val(),EMP_TYPE); 
     },
     error:function(data){
     console.log("Error: Something went wrong.");
     $("#tbody_employee").html('');                        
     },
   });
 }


function get_employee(id){
loadEmployee(id);  
$("#Employee_popup").show();
event.preventDefault();
}



    //Priority Dropdown starts here 
    let priority = "#EmployeeTable2";
      let priority2 = "#EmployeeTable";
      let priorityheaders = document.querySelectorAll(priority2 + " th");

      // Sort the table element when clicking on the table headers
      priorityheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(priority, ".clsspid_priority", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function EmployeeCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Employeecodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("EmployeeTable2");
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

  function EmployeeNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Employeenamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("EmployeeTable2");
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


      $("#Priority_closePopup").click(function(event){
        $("#Employee_popup").hide();
      });


      function bindEmployeeEvents(emptype_type){
var result=emptype_type.split('_');  
var EMP_TYPE= result[1]; 

var exist_vid=$("#VID_"+EMP_TYPE).val(); 

      $('.clsspid_priority').click(function(){    
      //  alert(emptype_type); 
          var id = $(this).attr('id');
          var txtval =    $("#txt"+id+"").val();
          var texdesc =   $("#txt"+id+"").data("desc");

      if(EMP_TYPE=='REF1'){
        var EMP2=$("#EMP_REF2").val();
        var EMP3=$("#EMP_REF3").val();

        if(txtval===EMP2 || txtval===EMP3){
              $("#Employee_popup").hide(); 
              $("#FocusId").val('VID_'+EMP_TYPE+'_popup');
              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn1").show();
              $("#AlertMessage").text('Please Select a different Employee');
              $("#alert").modal('show');
              $("#OkBtn1").focus();
              return false; 
              }else{
              $("#Employee_popup").hide(); 
              $('#'+emptype_type+'_popup').val(texdesc);
              $('#'+emptype_type).val(txtval);   
              event.preventDefault();
             }
              }else if(EMP_TYPE=='REF2'){
              var EMP1=$("#EMP_REF1").val();
              var EMP3=$("#EMP_REF3").val();
  
              if(txtval===EMP1 || txtval===EMP3){
                $("#Employee_popup").hide(); 
                $("#FocusId").val('VID_'+EMP_TYPE+'_popup');
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text('Please Select a different Employee');
                $("#alert").modal('show');
                $("#OkBtn1").focus();
                return false; 
                }else{
                $("#Employee_popup").hide(); 
   
                $('#'+emptype_type+'_popup').val(texdesc);
                $('#'+emptype_type).val(txtval);   
                event.preventDefault();
          }
          }else  if(EMP_TYPE=='REF3'){
              var EMP1=$("#EMP_REF1").val();
              var EMP2=$("#EMP_REF2").val();
              if(txtval===EMP1 || txtval===EMP2){
              $("#Employee_popup").hide(); 
              $("#FocusId").val('VID_'+EMP_TYPE+'_popup');
              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn1").show();
              $("#AlertMessage").text('Please Select a different Employee');
              $("#alert").modal('show');
              $("#OkBtn1").focus();
              return false; 
        }else{
            $("#Employee_popup").hide(); 
            $('#'+emptype_type+'_popup').val(texdesc);
            $('#'+emptype_type).val(txtval);   
            event.preventDefault();

        }
      }
          $("#Employee_popup").hide(); 

              event.preventDefault();
      });
}



  //employee Dropdown Ends here
  
//Checklist Starts
//------------------------
let chktid = "#ChecklistTale2";
      let chktid2 = "#ChecklistTable";
      let chkheaders = document.querySelectorAll(chktid2 + " th");

      // Sort the table element when clicking on the table headers
      chkheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(chktid, ".clschecklist", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function ChecklistCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("checklistcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("ChecklistTable2");
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

  function ChecklistNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("checklistdatesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("ChecklistTable2");
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



  $("#txtchecklist_popup").click(function(event){

var mid = $.trim( $("#MACHINEID_REF").val() ); 

if(mid =="" ){
  
    $("#FocusId").val('MACHINEID_REF');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please Select Machine No.');
      $("#alert").modal('show')
      $("#OkBtn1").focus();
      return false;
} 

$('#tbody_checklist').html('Loading...');
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$.ajax({
    url:'{{route("transaction",[365,"getchecklists"])}}',
    type:'POST',
    success:function(data) {
        $('#tbody_checklist').html(data);
        bindChecklistEvents();

        showSelectedCheck($("#CHECKLIST_REF").val(),'SELECT_CKLISTID_REF'); 
    },
    error:function(data){
        console.log("Error: Something went wrong.");
        $('#tbody_checklist').html('');
    },
});        
 $("#checklistpopup").show();
 event.preventDefault();
}); 


$("#checklist_closePopup").on("click",function(event){ 
    $("#checklistpopup").hide();
    event.preventDefault();
});

function bindChecklistEvents(){

$('.clschecklist').click(function(){

  

  var id = $(this).attr('id');
  var txtval =    $("#txt"+id+"").val();
  var texdesc =   $("#txt"+id+"").data("desc");
  var txtccname =   $("#txt"+id+"").data("ccname");
  
  var oldID =   $("#CHECKLIST_REF").val();
  
  $("#txtchecklist_popup").val(texdesc);
  $("#txtchecklist_popup").blur();
  $("#CHECKLIST_REF").val(txtval);
  $("#ChecklistDesc_popup").val(txtccname);

  //$("#checklistpopup").hide();
  $("#checklistcodesearch").val(''); 
  $("#checklistdatesearch").val(''); 
  
  ChecklistCodeFunction();
  ChecklistNameFunction();



  var vqid = txtval;
      if(vqid!=''){    
          $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          });

          $.ajax({
              url:'{{route("transaction",[$FormId,"get_checklist_data"])}}',
              type:'POST',
              data:{'id':vqid},
              success:function(data) {
     
                $('#tbody_checklist_grid').html(data);   
              },
              error:function(data){
                console.log("Error: Something went wrong.");
              //  $('#tbody_item').html('');
              },
          });




            
      }
      $("#checklistpopup").hide()
      event.preventDefault();






});
}
//Checklsit Ends





function getArraySum(a){
    var total=0;
    for(var i in a) { 
        total += a[i];
    }
    return total;
}

function isNumberDecimalKey(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
    return false;

    return true;
}



/*================================== ONLOAD FUNCTION ==================================*/
$(document).ready(function(e) {

 
    $('#example2').on('blur','[id*="ACTUAL_VALUE"]',function(){
            if(intRegex.test($(this).val())){
             $(this).val($(this).val()+'.00')
            }
            event.preventDefault();
        });

  var dt = new Date();
  var time = moment(dt).format("HH:mm");
$("#TIME").val(time); 




  var lastdt = <?php echo json_encode($objlastdt[0]->BDSL_DATE); ?>;
  var today = new Date(); 
  var sodate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;

  $('#BDSL_DATE').attr('min',lastdt);
  $('#BDSL_DATE').attr('max',sodate);


  var d = new Date(); 
  var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
  $('#BDSL_DATE').val(today);

});



function showSelectedCheck(hidden_value,selectAll){

var divid ="";

if(hidden_value !=""){

  var all_location_id = document.querySelectorAll('input[name="'+selectAll+'[]"]');
  //console.log(all_location_id); 
 
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


// Outsider_Name
// Contact_Number
// Company_Name


// Main_Person_Name1
// Main_Person_Name2
// Main_Person_Name3



$('#Chk_OwnEmployee').on('change',function()
{
  if($(this).is(':checked') == true)
  {
    $('#Chk_OutsideResource').prop('checked', false);

    $("#Outsider_Name").prop('disabled', true);  
    $("#Contact_Number").prop('disabled', true);  
    $("#Company_Name").prop('disabled', true);  

    $("#Outsider_Name").val('');  
    $("#Contact_Number").val('');  
    $("#Company_Name").val('');  

    $("#EMP_REF1_popup").prop('disabled', false);  
    $("#EMP_REF2_popup").prop('disabled', false);  
    $("#EMP_REF3_popup").prop('disabled', false);
  }
  else
  {
    $(this).prop('checked', false);  
  }
});

  $('#Chk_OutsideResource').on('change',function()
{
  if($(this).is(':checked') == true)
  {
    $('#Chk_OwnEmployee').prop('checked', false);

   $("#Outsider_Name").prop('disabled', false);  
    $("#Contact_Number").prop('disabled', false);  
    $("#Company_Name").prop('disabled', false);  

    $("#EMP_REF1_popup").prop('disabled', true);  
    $("#EMP_REF2_popup").prop('disabled', true);  
    $("#EMP_REF3_popup").prop('disabled', true);  

    $("#EMP_REF1").val('');  
    $("#EMP_REF2").val('');  
    $("#EMP_REF3").val('');  
    $("#EMP_REF1_popup").val('');  
    $("#EMP_REF2_popup").val('');  
    $("#EMP_REF3_popup").val('');  

   // $("#MACHINE_REF").val('');
  }
  else
  {
    $(this).prop('checked', false);
  
  }
});




$(document).ready(function(){
var employee_type=$('[name="EMPLOYEE_TYPE"]:checked').val();
if(employee_type==='own_employee'){
    $("#Outsider_Name").prop('disabled', true);  
    $("#Contact_Number").prop('disabled', true);  
    $("#Company_Name").prop('disabled', true);  

    $("#Outsider_Name").val('');  
    $("#Contact_Number").val('');  
    $("#Company_Name").val('');  

    $("#EMP_REF1_popup").prop('disabled', false);  
    $("#EMP_REF2_popup").prop('disabled', false);  
    $("#EMP_REF3_popup").prop('disabled', false);  
  }
  else{
   $("#Outsider_Name").prop('disabled', false);  
    $("#Contact_Number").prop('disabled', false);  
    $("#Company_Name").prop('disabled', false);  

    $("#EMP_REF1_popup").prop('disabled', true);  
    $("#EMP_REF2_popup").prop('disabled', true);  
    $("#EMP_REF3_popup").prop('disabled', true);  

    $("#EMP_REF1").val('');  
    $("#EMP_REF2").val('');  
    $("#EMP_REF3").val('');  
    $("#EMP_REF1_popup").val('');  
    $("#EMP_REF2_popup").val('');  
    $("#EMP_REF3_popup").val('');  


  }

});



/*================================== ADD/REMOVE FUNCTION ==================================*/
$("#Consumed").on('click','.add', function() {
  var $tr = $(this).closest('table');
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
  
  $clone.find('input').val('');

  $tr.closest('table').append($clone);         
  var rowCount1 = $('#Row_Count1').val();
  rowCount1 = parseInt(rowCount1)+1;
  $('#Row_Count1').val(rowCount1);
  serialNo('Consumed','participantRow3','SL_NO1');
  $clone.find('.remove').removeAttr('disabled');        
  event.preventDefault();
});

$("#Consumed").on('click', '.remove', function() {
  var rowCount = $(this).closest('table').find('.participantRow3').length;
  if (rowCount > 1) {
      $(this).closest('.participantRow3').remove();  
      var rowCount1 = $('#Row_Count1').val();
      rowCount1 = parseInt(rowCount1)-1;
      $('#Row_Count1').val(rowCount1);
      serialNo('Consumed','participantRow3','SL_NO1');
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


$("#Return").on('click','.add', function() {
  var $tr = $(this).closest('table');
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
  
  $clone.find('input').val('');

  $tr.closest('table').append($clone);         
  var rowCount2 = $('#Row_Count2').val();
  rowCount2 = parseInt(rowCount2)+1;
  $('#Row_Count2').val(rowCount2);
  serialNo('Return','participantRow4','SL_NO2');
  $clone.find('.remove').removeAttr('disabled');        

  event.preventDefault();
});

$("#Return").on('click', '.remove', function() {
  var rowCount = $(this).closest('table').find('.participantRow4').length;
  if (rowCount > 1) {
      $(this).closest('.participantRow4').remove();  
      var rowCount2 = $('#Row_Count2').val();
      rowCount2 = parseInt(rowCount2)-1;
      $('#Row_Count2').val(rowCount2);
      serialNo('Return','participantRow4','SL_NO2');
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

function serialNo(table_id,row_id,input_id){
  var i=1;
  $('#'+table_id).find('.'+row_id).each(function(){
    var TextId = $(this).find("[id*="+input_id+"]").attr('id');
    $("#"+TextId).val(i);
    i++;
  });
}




</script>
@endpush
