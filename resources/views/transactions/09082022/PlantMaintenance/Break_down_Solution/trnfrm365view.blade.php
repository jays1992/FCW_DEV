@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
	<div class="row">
		<div class="col-lg-2"><a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Breakdown Complaint Solution</a></div>

		<div class="col-lg-10 topnav-pd">
			<button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
      <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
      <button class="btn topnavbt" id="btnSaveSE" disabled="disabled"><i class="fa fa-save" ></i> Save</button>
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


<form id="edit_trn_form" method="POST"  >
  

 
 <div class="container-fluid purchase-order-view">
       
   @csrf
   <div class="container-fluid filter">

     <div class="inner-form">
     
       <div class="row">
           <div class="col-lg-2 pl"><p>Solution No</p></div>
           <div class="col-lg-2 pl">


           <input type="text" name="BDSL_NO" disabled id="BDSL_NO" value="{{ $objResponse->BDSL_NO }}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase" autofocus >
            <input type="hidden" name="BDSL_NO" disabled id="BDSL_NO" value="{{ $objResponse->BDSL_NO }}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase" autofocus >
           
           </div>
           
           <div class="col-lg-2 pl"><p>Solution Date</p></div>
           <div class="col-lg-2 pl">
               <input type="date" name="BDSL_DATE" id="BDSL_DATE" value="{{ $objResponse->BDSL_DATE }}" class="form-control mandatory" autocomplete="off" placeholder="dd/mm/yyyy" >
           </div>
           
           <div class="col-lg-2 pl"><p>Complaint Log No</p></div>
           <div class="col-lg-2 pl">
                   <input type="text" disabled name="ComplaintLog_popup" id="txtComplaintLog_popup" value="{{ $objResponseBDComplaints->BDCL_NO }}" class="form-control mandatory"  autocomplete="off"  readonly/>
                   <input type="hidden" name="BDCLID_REF" id="BDCLID_REF" class="form-control" value="{{ $objResponseBDComplaints->BDCL_ID }}" autocomplete="off" />

                                                            
           </div>
       </div>

       <div class="row">
           <div class="col-lg-2 pl"><p>Complaint Log Date</p></div>
           <div class="col-lg-2 pl">
               <input type="date" disabled name="BDCL_DATE" id="BDCL_DATE" class="form-control mandatory" value="{{ $objResponseBDComplaints->BDCL_DATE }}"  autocomplete="off" placeholder="dd/mm/yyyy" readonly/>
           </div>
           
           <div class="col-lg-2 pl"><p>Complaint By</p></div>
           <div class="col-lg-2 pl">
               <input type="text" disabled name="COMPLAINT_BY" id="COMPLAINT_BY"  class="form-control mandatory" value="{{ $objResponseBDComplaints->COMPLAINT_BY }}" autocomplete="off" readonly >
           </div>
           
           <div class="col-lg-2 pl"><p>Department</p></div>
           <div class="col-lg-2 pl">
               <input type="text" disabled name="DEPARTMENT_NAME" id="DEPARTMENT_NAME" value="{{ $objResponseBDComplaints->DEPARTMENT_CODE }}-{{ $objResponseBDComplaints->DEPARTMENT_NAME }}"  class="form-control mandatory" autocomplete="off" readonly >
               <input type="hidden" name="DEPARTMENTID_REF" id="DEPARTMENTID_REF" value="{{ $objResponseBDComplaints->DEPID_REF }}"  class="form-control mandatory" autocomplete="off" readonly >
           </div>
       </div>

       <div class="row">
           <div class="col-lg-2 pl"><p>Priority</p></div>
           <div class="col-lg-2 pl">
               <input type="text" disabled name="PRIORITY_NAME" id="PRIORITY_NAME" class="form-control mandatory" value="{{ $objResponseBDComplaints->PRIORITYCODE }}-{{ $objResponseBDComplaints->PRIORITY_DESC }}"  autocomplete="off" readonly/>
               <input type="hidden" name="PRIORITYID_REF" id="PRIORITYID_REF" class="form-control mandatory" value="{{ $objResponseBDComplaints->PRIORITYID_REF }}"  autocomplete="off" readonly/>
           </div>
           
           <div class="col-lg-2 pl"><p>Machine / Genset Code</p></div>
           <div class="col-lg-2 pl">
               <input type="text" disabled name="MACHINE_CODE" id="MACHINE_CODE"  class="form-control mandatory" value="{{ $objResponseBDComplaints->MACHINE_NO }}" autocomplete="off" readonly >
               <input type="hidden" name="MACHINEID_REF" id="MACHINEID_REF"  class="form-control mandatory" value="{{ $objResponseBDComplaints->MACHINEID_REF }}" autocomplete="off" readonly >
           </div>
           
           <div class="col-lg-2 pl"><p>Description</p></div>
           <div class="col-lg-2 pl">
               <input type="text" disabled name="MACHINE_NAME" id="MACHINE_NAME"  class="form-control mandatory" value="{{ $objResponseBDComplaints->MACHINE_DESC }}" autocomplete="off" readonly >
           </div>
       </div>
       <div class="row">
           <div class="col-lg-2 pl"><p>Problem Log Detail</p></div>
           <div class="col-lg-8 pl">
               <input type="text" disabled name="PROBLEM_LOG_DET" id="PROBLEM_LOG_DET"  class="form-control mandatory" value="{{ $objResponseBDComplaints->PLOG_DETAILS }}" autocomplete="off" readonly >
           </div>
       </div>

       <div class="row">
           <div class="col-lg-2 pl"><p>Breakdown Reason Code 1</p></div>
           <div class="col-lg-2 pl">
               <input type="text" disabled name="REASON_CODE1" id="REASON_CODE1"  class="form-control mandatory" value="{{ $objResponseBDComplaints->REASON1_CODE}}" autocomplete="off" readonly >
               <input type="hidden" name="REASON1_REF" id="REASON1_REF"  class="form-control mandatory" value="{{ $objResponseBDComplaints->BD_REASONID_REF1 }}" autocomplete="off" readonly >
           </div>
           <div class="col-lg-2 pl"><p>Breakdown Reason Description 1</p></div>
           <div class="col-lg-6 pl">
               <input type="text" disabled name="REASON_NAME1" id="REASON_NAME1"  class="form-control mandatory" value="{{ $objResponseBDComplaints->REASON1_DESC }}" autocomplete="off" readonly >
           </div>
       </div>
       <div class="row">
           <div class="col-lg-2 pl"><p>Breakdown Reason Code 2</p></div>
           <div class="col-lg-2 pl">
               <input type="text" disabled name="REASON_CODE2" id="REASON_CODE2"  class="form-control mandatory" value="{{ $objResponseBDComplaints->REASON2_CODE }}" autocomplete="off" readonly >
               <input type="hidden" name="REASON2_REF" id="REASON2_REF"  class="form-control mandatory" value="{{ $objResponseBDComplaints->BD_REASONID_REF2 }}" autocomplete="off" readonly >
           </div>
           <div class="col-lg-2 pl"><p>Breakdown Reason Description 1</p></div>
           <div class="col-lg-6 pl">
               <input type="text" disabled name="REASON_NAME2" id="REASON_NAME2"  class="form-control mandatory" value="{{ $objResponseBDComplaints->REASON2_DESC }}" autocomplete="off" readonly >
           </div>
       </div>


       <div class="row">
           <div class="col-lg-2 pl"><p>Remarks 1</p></div>
           <div class="col-lg-4 pl">
               <input type="text" disabled name="REMARKS1" id="REMARKS1" class="form-control mandatory"  value="{{ $objResponseBDComplaints->REMARKS_1 }}" autocomplete="off"  />  
           </div>
           <div class="col-lg-2 pl"><p>Remarks 2</p></div>
           <div class="col-lg-4 pl">
               <input type="text" disabled name="REMARKS2" id="REMARKS2" class="form-control mandatory" value="{{ $objResponseBDComplaints->REMARKS_2 }}"  autocomplete="off"  />
           </div>     
       </div>
       <div class="row">
           <div class="col-lg-2 pl"><p>Own Employee</p></div>
           <div class="col-lg-1 pl">
               <input type="checkbox" disabled name="EMPLOYEE_TYPE" id="Chk_OwnEmployee" value="own_employee" {{isset($objResponse) && $objResponse->OWN_EMPLOYEE=='1'?'checked':''}}   />  
           </div>
           <div class="col-lg-2 pl"><p>Outside Resource</p></div>
           <div class="col-lg-1 pl">
               <input type="checkbox" disabled name="EMPLOYEE_TYPE" id="Chk_OutsideResource" value="outside_employee" {{isset($objResponse) && $objResponse->OUTSIDE_RESOURCE=='1'?'checked':''}}  />
           </div>     
       </div>
       <div class="row">
           <div class="col-lg-2 pl"><p>Outsider Name</p></div>
           <div class="col-lg-2 pl">
               <input type="text" disabled name="Outsider_Name" id="Outsider_Name" class="form-control mandatory" value="{{ $objResponse->OUTSIDE_RNAME }}"   autocomplete="off"  />  
           </div>
           <div class="col-lg-2 pl"><p>Contact Number</p></div>
           <div class="col-lg-2 pl">
               <input type="text" name="Contact_Number" id="Contact_Number" class="form-control mandatory" value="{{ $objResponse->OUTSIDE_CONTACT_NO }}" autocomplete="off"  />
           </div> 
           <div class="col-lg-2 pl"><p>Company Name</p></div>
           <div class="col-lg-2 pl">
               <input type="text" disabled name="Company_Name" id="Company_Name" class="form-control mandatory" value="{{ $objResponse->OUTSIDE_COMPANY }}"  autocomplete="off"  />
           </div>    
       </div>
       <div class="row">
           <div class="col-lg-2 pl"><p>Maintenance Person Name 1</p></div>
           <div class="col-lg-2 pl">
       <input type="text" disabled name="EMP_REF1" id="EMP_REF1_popup" class="form-control mandatory" onclick="get_employee('EMP_REF1');" value="{{isset($objResponse) && $objResponse->MAINTENANCE_EMPID_REF1!=''?  $objResponse->EMP1_CODE.'-'.$objResponse->EMP1_NAME :''}}" autocomplete="off" readonly/>
       <input type="hidden" name="EMP_REF1" id="EMP_REF1" class="form-control" value="{{ $objResponse->MAINTENANCE_EMPID_REF1 }}" autocomplete="off" />
           </div>
           <div class="col-lg-2 pl"><p>Maintenance Start Date</p></div>
           <div class="col-lg-2 pl">
               <input type="date" name="Main_Start_Date" id="Main_Start_Date" class="form-control mandatory" value="{{ $objResponse->MAINTENANCE_START_DATE }}"  autocomplete="off" placeholder="dd/mm/yyyy"  />
           </div> 
           <div class="col-lg-2 pl"><p>Maintenance Start Time</p></div>
           <div class="col-lg-2 pl">
               <input type="time" disabled name="Main_Start_Time" id="Main_Start_Time" class="form-control mandatory" value="{{ $START_TIME }}"  autocomplete="off"  />
           </div>    
       </div>
       <div class="row">
           <div class="col-lg-2 pl"><p>Maintenance Person Name 2</p></div>
           <div class="col-lg-2 pl">
       <input type="text" disabled name="EMP_REF2" id="EMP_REF2_popup" class="form-control mandatory" value="{{isset($objResponse) && $objResponse->MAINTENANCE_EMPID_REF2!=''?  $objResponse->EMP2_CODE.'-'.$objResponse->EMP2_NAME :''}}" onclick="get_employee('EMP_REF2');"  autocomplete="off" readonly/>
       <input type="hidden" name="EMP_REF2" id="EMP_REF2" class="form-control" value="{{ $objResponse->MAINTENANCE_EMPID_REF2 }}" autocomplete="off" />
           </div>
           <div class="col-lg-2 pl"><p>Fault Detect 1</p></div>
           <div class="col-lg-6 pl">
               <input type="text" disabled name="Fault_Detect_1" id="Fault_Detect_1" class="form-control mandatory" value="{{ $objResponse->FAULT_DETECT1 }}"  autocomplete="off"  />
           </div>     
       </div>
       <div class="row">
           <div class="col-lg-2 pl"><p>Maintenance Person Name 3</p></div>
           <div class="col-lg-2 pl">
   <input type="text" name="EMP_REF3" disabled id="EMP_REF3_popup" value="{{isset($objResponse) && $objResponse->MAINTENANCE_EMPID_REF3!=''?  $objResponse->EMP3_CODE.'-'.$objResponse->EMP3_NAME :''}}" class="form-control mandatory" onclick="get_employee('EMP_REF3');"   autocomplete="off" readonly/>
   <input type="hidden" name="EMP_REF3" id="EMP_REF3" class="form-control" autocomplete="off" value="{{ $objResponse->MAINTENANCE_EMPID_REF3 }}" />
           </div>
           <div class="col-lg-2 pl"><p>Fault Detect 2</p></div>
           <div class="col-lg-6 pl">
               <input type="text" disabled name="Fault_Detect_2" id="Fault_Detect_2" class="form-control mandatory" value="{{ $objResponse->FAULT_DETECT2 }}"  autocomplete="off"  />
           </div>     
       </div>
       <div class="row">	
           <div class="col-lg-2 pl"><p>Checklist No</p></div>
           <div class="col-lg-2 pl">
               <input type="text" disabled name="CHECKLIST_popup" id="txtchecklist_popup" class="form-control mandatory" value="{{ $objResponsechecklists->CHECKLIST_NO }}"  autocomplete="off" readonly/>
                       <input type="hidden" name="CHECKLIST_REF" id="CHECKLIST_REF" class="form-control" value="{{ $objResponse->MCKLISTID_REF }}" autocomplete="off" />
           </div>
           <div class="col-lg-2 pl"><p>Checklist Description</p></div>
           <div class="col-lg-6 pl">
             <input type="text" disabled name="ChecklistDesc_popup" id="ChecklistDesc_popup" class="form-control" value="{{ $objResponsechecklists->CHECKLIST_DESC }}"  autocomplete="off"  readonly/>
           </div>
       </div>
       <div class="row">
           <div class="col-lg-2 pl"><p>Action Taken 1</p></div>
           <div class="col-lg-4 pl">
               <input type="text" disabled name="Action_Taken1" id="Action_Taken1" class="form-control mandatory" value="{{ $objResponse->ACTION_TAKEN1 }}"  autocomplete="off"  />  
           </div>
           <div class="col-lg-2 pl"><p>Action Taken 2</p></div>
           <div class="col-lg-4 pl">
               <input type="text" disabled name="Action_Taken2" id="Action_Taken2" class="form-control mandatory" value="{{ $objResponse->ACTION_TAKEN2 }}"  autocomplete="off"  />
           </div>     
       </div>
       <div class="row">
           <div class="col-lg-2 pl"><p>Status</p></div>
           <div class="col-lg-2 pl">
               <select  class="form-control" disabled name="drpstatus" id="drpstatus" > 
                 <option value="">Select</option>
                 <option value="Work In Progress" {{isset($objResponse) && $objResponse->BREAKDOWN_STATUS=='Work In Progress'?'selected':''}}>Work In Progress</option>
                 <option value="Completed" {{isset($objResponse) && $objResponse->BREAKDOWN_STATUS=='Completed'?'selected':''}}>Completed</option>
                 <option value="Temporary Solved" {{isset($objResponse) && $objResponse->BREAKDOWN_STATUS=='Temporary Solved'?'selected':''}}>Temporary Solved</option>
               </select>  
           </div>
           <div class="col-lg-2 pl"><p>Maintenance End Date</p></div>
           <div class="col-lg-2 pl">
               <input type="date" disabled name="Main_End_Date" id="Main_End_Date" value="{{ $objResponse->MAINTENANCE_END_DATE }}" class="form-control mandatory"  autocomplete="off" placeholder="dd/mm/yyyy"  />
           </div> 
           <div class="col-lg-2 pl"><p>Maintenance End Time</p></div>
           <div class="col-lg-2 pl">
               <input type="time" disabled name="Main_End_Time" id="Main_End_Time" value="{{ $END_TIME }}" class="form-control mandatory"  autocomplete="off"  />
           </div>     
       </div>
       <div class="row">
           <div class="col-lg-2 pl"><p>Remarks in case of BD Pending</p></div>
           <div class="col-lg-10 pl">
               <input type="text" disabled name="REMARKS_PENDING" id="REMARKS_PENDING" value="{{ $objResponse->REMARKS_PENDING }}" class="form-control mandatory"  autocomplete="off"  />  
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
                     
                     @if(!empty($CHECKLIST))
                    @foreach($CHECKLIST as $key => $row)
                     <tr  class="participantRow" >
                        <td hidden><input type="text" name="checklistcount[]"  > </td>
          
                        <td hidden><input type="text" name="MCKLIST_DID[]" id="'MCKLIST_DID_{{ $key }}" value="{{ $row->MCKLISTID_REF }}" > </td>
                                                                
                        <td><input type="text" name="MP_CODE[]"  class="form-control" value="{{ $row->MP_CODE }}"   autocomplete="off"  disabled /></td>
         
                        <td><input type="text" name="MP_DESC[]"  class="form-control" value="{{ $row->MP_DESC }}"  autocomplete="off"  disabled /></td>
         
                        <td><input type="text" name="MSP_CODE[]"  class="form-control" value="{{ $row->MSP_CODE }}"  autocomplete="off"  disabled /></td>
         
                        <td><input type="text" name="MSP_DESC[]"  class="form-control" value="{{ $row->MSP_DESC }}"  autocomplete="off"  disabled /></td>
         
                        <td><input type="text" name="STANDARD_VALUE[]"   value="{{ $row->STANDARD_VALUE }}" class="form-control three-digits" disabled maxlength="15"  autocomplete="off"  readonly/></td>

                        <td><input type="text" name="ACTUAL_VALUE[]" id="ACTUAL_VALUE_{{$key}}"  value="{{ $row->ACTUAL_VALUE }}" disabled class="form-control three-digits" maxlength="15"  autocomplete="off"   /></td>

                        <td><input type="text" name="REMARKS[]"  class="form-control" value="{{ $row->Chk_Remarks }}" disabled  autocomplete="off"   /></td>
                        </tr>
                 
                      <tr></tr>
                     @endforeach
                     @endif
                     </tbody>
                   </table>
               </div>	
           </div>

           <div id="Consumed" class="tab-pane">
              <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:400px;margin-top:10px; " >
                <table id="example3" class="display nowrap table table-striped table-bordered itemlist"  style="height:auto !important; width:30%">
                  <thead id="thead3"  style="position: sticky;top: 0">                       
                    <tr>
                      <th width="70px">Sl. No.</th>
                       <th>Spare Parts Consumed</th>
                       <th>Action</th>
                       <th hidden><input type="hidden" name="Row_Count1" id ="Row_Count1" class="form-control"  value="{{count($CONSUMELIST)}}" ></th>
                    </tr>
                  </thead>
                      <tbody>
                        @if(!empty($CONSUMELIST))
                        @foreach($CONSUMELIST as $key => $row)
                          <tr class="participantRow3">
                   
                           <td><input type="text" name="SL_NO1_{{$key}}" id="SL_NO1_{{$key}}" class="form-control" autocomplete="off" disabled value="{{$key+1}}" readonly ></td>
                           <td><input type="text" name="SPARE_PARTS_CONSUMED_{{$key}}" id="SPARE_PARTS_CONSUMED_{{$key}}" disabled value="{{$row->SPARE_PART_NAME}}" class="form-control" autocomplete="off" ></td>
                           <td align="center" style="width: 100;">
                               <button type="button" class="btn add" title="add" disabled data-toggle="tooltip"><i class="fa fa-plus"></i></button>
                               <button type="button" class="btn remove" title="Delete" disabled id="btnremove" data-toggle="tooltip" ><i class="fa fa-trash"></i></button>
                           </td>
                         </tr>
                         @endforeach
                         @else
                         <tr class="participantRow3">
                           <td><input type="text" name="SL_NO1_0" id="SL_NO1_0" class="form-control" autocomplete="off"  value="1" disabled ></td>
                           <td><input type="text" name="SPARE_PARTS_CONSUMED_0" id="SPARE_PARTS_CONSUMED_0" value="" disabled class="form-control" autocomplete="off" ></td>
                           <td align="center" style="width: 100;">
                               <button type="button" class="btn add" title="add" disabled data-toggle="tooltip"><i class="fa fa-plus"></i></button>
                               <button type="button" class="btn remove" title="Delete" disabled id="btnremove" data-toggle="tooltip" ><i class="fa fa-trash"></i></button>
                           </td>
                         </tr>
                         @endif
                     </tbody>
                   </table>
               </div>	
           </div>    


           <div id="Return" class="tab-pane">
               <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:400px;margin-top:10px; " >
                   <table id="example4" class="display nowrap table table-striped table-bordered itemlist"  style="height:auto !important; width:50%">
                     <thead id="thead4"  style="position: sticky;top: 0">
                                                   
                       <tr>
                         <th width="70px">Sl. No. </th>
                         <th>Spare Parts Return</th>
                         <th>Return To</th>
                         <th>Action</th>
                         <th hidden><input type="hidden" name="Row_Count2" id ="Row_Count2" class="form-control" value="{{count($RETURNLIST)}}" ></th>
                       </tr>
                     </thead>
                     <tbody>
                     @if(!empty($RETURNLIST))
                    @foreach($RETURNLIST as $key => $row)


                      <tr class="participantRow4">
                         <td><input type="text" name="SL_NO2_{{$key}}" id="SL_NO2_{{$key}}" class="form-control" autocomplete="off" disabled value="{{$key+1}}" readonly ></td>
                           <td><input type="text" name="SPARE_PART_NAME_{{$key}}" id="SPARE_PART_NAME_{{$key}}" disabled value="{{$row->SPARE_PART_NAME}}" class="form-control" autocomplete="off" ></td>
                           <td><input type="text" name="SPARE_RETURN_TO_{{$key}}" id="SPARE_RETURN_TO_{{$key}}"  disabled class="form-control" value="{{$row->SPARE_RETURN_TO}}" autocomplete="off" ></td>
                           <td align="center" style="width: 100;">
                               <button type="button" class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></button>
                               <button type="button" class="btn remove" title="Delete" id="btnremove" data-toggle="tooltip" ><i class="fa fa-trash"></i></button>
                           </td>
                         </tr>
                         @endforeach
                         @else
                         <tr class="participantRow4">
                         <td><input type="text" name="SL_NO2_0" id="SL_NO2_0" class="form-control" autocomplete="off"  value="1" disabled ></td>
                           <td><input type="text" name="SPARE_PART_NAME_0" id="SPARE_PART_NAME_0" disabled  class="form-control" autocomplete="off" ></td>
                           <td><input type="text" name="SPARE_RETURN_TO_0" id="SPARE_RETURN_TO_0" disabled class="form-control" value="" autocomplete="off" ></td>
                           <td align="center" style="width: 100;">
                               <button type="button" class="btn add" title="add" disabled data-toggle="tooltip"><i class="fa fa-plus"></i></button>
                               <button type="button" class="btn remove" title="Delete" disabled id="btnremove" data-toggle="tooltip" ><i class="fa fa-trash"></i></button>
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
 </div>

</div>


</form>

</form>
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

@endsection


@push('bottom-css')
<style>
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
  width: 1050px;
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
    width: 16%;
}
#CTIDDetTable2 {
  border-collapse: collapse;
  width: 1050px;
  border: 1px solid #ddd;
  font-size: 11px;
}

#CTIDDetTable2 th{
    text-align: left;
    padding: 5px;
    
    font-size: 11px;
    
    color: #0f69cc;
    font-weight: 600;
}

#CTIDDetTable2 td {
  text-align: left;
    padding: 5px;
    font-size: 11px;
    
    font-weight: 600;
    width: 20%;
}
</style>
@endpush
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
  var currentdate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;

  $('#BDSL_DATE').attr('min',lastdt);
  $('#BDSL_DATE').attr('max',currentdate);


  var d = new Date(); 
  var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
 // $('#BDSL_DATE').val(today);

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



$(document).ready(function(e) {
//row_count
$('#Row_Count').val("1");
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
    var rowCount = $('#Row_Count').val();
        rowCount = parseInt(rowCount)+1;
    $('#Row_Count').val(rowCount);
    $clone.find('[id*="SL_NO1"]').val(rowCount);
    $clone.find('.remove').removeAttr('disabled'); 


    event.preventDefault();
});

$("#example3").on('click', '.remove', function() {

    var rowCount = $('#Row_Count').val();
    $('#Row_Count').val(rowCount-1);

    if (rowCount > 1) {
        $(this).closest('tbody').remove();     
    } 
    
    if (rowCount <= 1) { 
        $(document).find('.remove').prop('disabled', false);  
    }
    event.preventDefault();
});    
//rowcount 1
$('#Row_Count1').val("1");
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
    var rowCount1 = $('#Row_Count1').val();
        rowCount1 = parseInt(rowCount1)+1;
    $('#Row_Count1').val(rowCount1);
    $clone.find('[id*="SL_NO2"]').val(rowCount1);
    $clone.find('.remove').removeAttr('disabled'); 


    event.preventDefault();
});

$("#example4").on('click', '.remove', function() {

    var rowCount1 = $('#Row_Count1').val();

    $('#Row_Count1').val(rowCount1-1);

    if (rowCount1 > 1) {
        $(this).closest('tbody').remove();     
    } 
    
    if (rowCount1 <= 1) { 
        $(document).find('.remove').prop('disabled', false);  
    }
    event.preventDefault();
});    

});



/*================================== TNC HEADER =================================*/
  
</script>

@endpush

@push('bottom-scripts')
<script>
var formTrans = $("#edit_trn_form");
formTrans.validate();

$( "#btnSaveFormData" ).click(function() {
  if(formTrans.valid()){
    validateForm("fnSaveData");
  }
});


$( "#btnApprove" ).click(function() {
 
  if(formTrans.valid()){
    validateForm("fnApproveData");
  }
});

function validateForm(saveAction){
  
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


 
    else{
      $("#alert").modal('show');
                $("#AlertMessage").text('Do you want to save to record.');
                $("#YesBtn").data("funcname",saveAction);
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
var customFnName = $("#YesBtn").data("funcname");
    window[customFnName]();

});

window.fnSaveData = function (){

    event.preventDefault();
    var trnsoForm = $("#edit_trn_form");
    var formData = trnsoForm.serialize();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url:'{{ route("transaction",[$FormId,"update"])}}',
        type:'POST',
        data:formData,
        success:function(data) {
          
            if(data.errors) {
                $(".text-danger").hide();

                if(data.errors.JWONO){
                    showError('ERROR_JWONO',data.errors.JWONO);
                            $("#YesBtn").hide();
                            $("#NoBtn").hide();
                            $("#OkBtn1").show();
                            $("#AlertMessage").text('Please enter correct value in VQ NO.');
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
    var trnsoForm = $("#edit_trn_form");
    var formData = trnsoForm.serialize();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url:'{{ route("transaction",[$FormId,"Approve"])}}',
        type:'POST',
        data:formData,
        success:function(data) {
          
            if(data.errors) {
                $(".text-danger").hide();

                if(data.errors.JWONO){
                    showError('ERROR_JWONO',data.errors.JWONO);
                            $("#YesBtn").hide();
                            $("#NoBtn").hide();
                            $("#OkBtn1").show();
                            $("#AlertMessage").text('Please enter correct value in VQ NO.');
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
    $("#JWONO").focus();
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

function showAlert(msg){
  $("#alert").modal('show');
  $("#AlertMessage").text(msg);
  $("#YesBtn").hide(); 
  $("#NoBtn").hide();  
  $("#OkBtn1").show();
  $("#OkBtn1").focus();
  highlighFocusBtn('activeOk');
}

function isNumberDecimalKey(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
    return false;

    return true;
}

function doCalculation(){
  $(".blurRate").blur();
}

function showAlert(msg){
  $("#alert").modal('show');
  $("#AlertMessage").text(msg);
  $("#YesBtn").hide();
  $("#NoBtn").hide();
  $("#OkBtn1").show();
  $("#OkBtn1").focus();
  highlighFocusBtn('activeOk');
  return false;
}


//$(document).ready(function(e) {

  //var today         =   new Date(); 
  //var currentdate   =   today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
//   var currentdate   =   <?php //echo json_encode($objMstResponse->JWODT); ?>;

//   $('[id*="EDA_"]').attr('min',currentdate);
//   $('[id*="EDA_"]').val(currentdate);

// });

</script>


@endpush