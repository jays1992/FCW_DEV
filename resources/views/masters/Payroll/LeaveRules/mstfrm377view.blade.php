
@extends('layouts.app')
@section('content')

    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[$FormId,'index'])}}" class="btn singlebt">Leave Rules</a>
                </div><!--col-2-->
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
            </div><!--row-->
    </div>

    <form id="frm_mst_edit" onsubmit="return validateForm()"  method="POST" class="needs-validation"  >
    <div class="container-fluid filter">

	<div class="inner-form">


  <div class="row">
            <div class="col-lg-2 pl"><p>Pay Period</p></div>
            <div class="col-lg-2 pl">
       
            <input type="text" {{$Action_Status}} name="PERIOD_popup_REF1" id="PERIOD_popup_REF1"  class="form-control mandatory" onclick="get_period('PERIOD_REF1');"  value="{{$objLeaveRule[0]->PAY_PERIOD_CODE }}{{ isset($objLeaveRule[0]->PAY_PERIOD_DESC)?'-'.$objLeaveRule[0]->PAY_PERIOD_DESC:''}}" autocomplete="off"  readonly/>
                <input type="hidden" name="PERIOD_REF1" id="PERIOD_REF1" value="{{isset($objLeaveRule[0]->FYID_REF) ? $objLeaveRule[0]->FYID_REF:'' }}" class="form-control" autocomplete="off" />
            
            </div>

        </div>

		
	</div>

	<div class="container-fluid">
		<div class="row">

    <ul class="nav nav-tabs">
           <li class="active"><a data-toggle="tab" href="#Material" id="tabing1">Leave Rule</a></li>
           <li><a data-toggle="tab" href="#DesignationSpecific" id="tabing2">Designation Specific</a></li>
        </ul>  
      <div class="tab-content">
      <div id="Material" class="tab-pane fade in active">
          <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:450px;margin-top:10px;" >
              <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="500" style="height:auto !important;">
                  <thead id="thead1"  style="position: sticky;top: 0">                                                    
                          <tr>                                     
                              <th width="15%">Leave Type Code	 <input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1" value="{{$objCount1}}"> </th>
                              <th>Description</th>
                              <th width="15%"> Max Leaves in <br> a year (Days)</th>
                              <th width="50%"> Max Cummulative leaves <br>(Cap Limit) (Days)</th>
                              <th> Carry forward to <br> next Payperiod</th>
                              <th> Half day Avail</th>
                              <th> Lapse in the <br> end of the year</th>
                              <th> Leave Encashment</th>
                              <th> Sandwitch</th>
                              <th> Avail non due leave</th>
                              <th> Designation Specific</th>
                              <th> Document Required</th>
                              <th> Gender Specific</th>
                              <th> Max Leaves <br> (in case gender specific)</th>
                              <th> Max Kids / Delivery <br> (in case of gender specific)</th>
                              <th>De-activated</th>
                              <th>Date of De-activated</th>                   
                              <th  width="6%">Action</th>                      
                  </thead>
                  <tbody>   
          @if(!empty($objLeaveRule))
          @foreach($objLeaveRule as $key => $row) 
          <tr  class="participantRow">    
            <td><input  type="text" {{$Action_Status}}  name={{"txtLT_popup_".$key}} id={{"txtLT_popup_".$key}} value="{{$row->LEAVETYPE_CODE}}" class="form-control"  autocomplete="off"  readonly/></td>
            <td  hidden><input type="text" {{$Action_Status}} name={{"LTID_REF_".$key}} id={{"LTID_REF_".$key}} value="{{$row->LTID_REF}}" class="form-control" autocomplete="off" /></td>                          
            <td><input type="text" {{$Action_Status}} style=" width: 171px;" name={{"LT_REF_".$key}} id={{"LT_REF_".$key}} value="{{$row->LEAVETYPE_DESC}}" class="form-control"  autocomplete="off"  readonly /></td>
              <td style="text-align:center;"><input type="text" {{$Action_Status}} class="form-control" name={{"MAX_LEAVE_YEAR_".$key}} id={{"MAX_LEAVE_YEAR_".$key}} value="{{$row->YEARLEAVE_MAX}}" onkeypress="return isNumberDecimalKey(event,this)"  ></td>
              <td style="text-align:center;"><input type="text" {{$Action_Status}} class="form-control" name={{"MAX_COMMULATIVE_LEAVE_".$key}} id={{"MAX_COMMULATIVE_LEAVE_".$key}} value="{{$row->CUMM_LEAVE_MAX}}" onkeypress="return isNumberDecimalKey(event,this)"  ></td>
              <td style="text-align:center;"><input type="checkbox"  {{$Action_Status}} name={{"CARRRY_FORWARD_LEAVE_".$key}} id={{"CARRRY_FORWARD_LEAVE_".$key}} {{$row->CARRY_FW == 1 ? 'checked' : ''}}    ></td>
              <td style="text-align:center;"><input type="checkbox" {{$Action_Status}}  name={{"HALF_DAY_LEAVE_".$key}} id={{"HALF_DAY_LEAVE_".$key}} {{$row->HALF_DAY == 1 ? 'checked' : ''}}   ></td>
              <td style="text-align:center;"><input type="checkbox" {{$Action_Status}}  name={{"LAPSE_LEAVE_".$key}} id={{"LAPSE_LEAVE_".$key}} {{$row->LAPES_EOY == 1 ? 'checked' : ''}}   ></td>
              <td style="text-align:center;"><input type="checkbox" {{$Action_Status}}  name={{"LEAVE_ENCASHMENT_".$key}} id={{"LEAVE_ENCASHMENT_".$key}} {{$row->ENCASHMENT == 1 ? 'checked' : ''}}   ></td>
              <td style="text-align:center;"><input type="checkbox" {{$Action_Status}}  name={{"SANDWITCH_LEAVE_".$key}} id={{"SANDWITCH_LEAVE_".$key}} {{$row->SANDWICH == 1 ? 'checked' : ''}}   ></td>
              <td style="text-align:center;"><input type="checkbox" {{$Action_Status}}  name={{"NON_DUE_LEAVE_".$key}} id={{"NON_DUE_LEAVE_".$key}} {{$row->NON_DUE_LEAVE == 1 ? 'checked' : ''}}    ></td>
              <td style="text-align:center;"><input type="checkbox" {{$Action_Status}}  name={{"DESIGNATION_SPECIFIC_".$key}} id={{"DESIGNATION_SPECIFIC_".$key}} {{$row->DESG_SPECI == 1 ? 'checked' : ''}}    ></td>
              <td style="text-align:center;"><input type="checkbox" {{$Action_Status}}  name={{"DOCUMENT_REQUIRED_".$key}} id={{"DOCUMENT_REQUIRED_".$key}} {{$row->REQUIRED_DOC == 1 ? 'checked' : ''}}    ></td>
              <td style="text-align:center;">                      
              <select name={{"GENDER_SPECIFIC_".$key}} id={{"GENDER_SPECIFIC_".$key}} {{$Action_Status}} class="form-control"  >
              <option value="">Select</option>
              <option value="Male" {{$row->GENDER_SPECI == 'Male' ? 'selected' : ''}} >Male</option>
              <option value="Female" {{$row->GENDER_SPECI == 'Female' ? 'selected' : ''}}>Female</option>
              <option value="Both" {{$row->GENDER_SPECI == 'Both' ? 'selected' : ''}}>Both</option>
              </select>   
            </td>
              <td style="text-align:center;"><input type="input" {{$Action_Status}}  name={{"MAX_LEAVE_GENDER_".$key}} id={{"MAX_LEAVE_GENDER_".$key}} value="{{$row->GENDER_SPECI_MAX}}" onkeypress="return isNumberDecimalKey(event,this)" class="form-control"  ></td>
              <td style="text-align:center;"><input type="input" {{$Action_Status}} name={{"MAX_LEAVE_KID_".$key}} id={{"MAX_LEAVE_KID_".$key}} value="{{$row->MAX_KIDS}}" onkeypress="return isNumberDecimalKey(event,this)" class="form-control" ></td>
              <td style="text-align:center;"><input type="checkbox" {{$Action_Status}}  name={{"DEACTIVATED_".$key}} class="DEACTIVATED" id={{"DEACTIVATED_".$key}} {{$row->DEACTIVATED == 1 ? 'checked' : ''}}   ></td>
              <td style="text-align:center;"><input type="date"  value="{{$row->DEACTIVATED == 1 ?$row->DODEACTIVATED: ''}}"  {{$Action_Status}} class="form-control"  name={{"DODEACTIVATED_".$key}} id={{"DODEACTIVATED_".$key}} ></td>             
                                    
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



            <div id="DesignationSpecific" class="tab-pane fade">
            <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:450px;margin-top:10px; width:60%;"  >
              <table id="example3" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                  <thead id="thead1"  style="position: sticky;top: 0">                                                    
                          <tr>                                     
                              <th>Leave Type Code <input class="form-control" type="hidden" name="Row_Count2" id ="Row_Count2" value="{{$objCount2}}"> </th>
                              <th>Description</th>
                              <th> Designation</th>
                              <th> Max Leaves in a year (Days)</th>
                              <th> Max Cummulative leaves (Cap Limit) (Days)</th>                     
                              <th  width="15%">Action</th>                      
                  </thead>
                  <tbody> 
                  @if(!empty($objDesignation_data))
                  @foreach($objDesignation_data as $key => $row)   
                  <tr  class="participantRow1">    
                    <td><input  type="text"   name={{"txtLT_popup1_".$key}} id={{"txtLT_popup1_".$key}} value="{{$row->LEAVETYPE_CODE}}" class="form-control"  autocomplete="off"  readonly/></td>
                    <td  hidden><input type="text"  name={{"LTID_REF1_".$key}} id={{"LTID_REF1_".$key}} value="{{$row->LTID_REF}}" class="form-control" autocomplete="off" /></td>                          
                    <td><input type="text"  name={{"LT_REF1_".$key}} id={{"LT_REF1_".$key}} value="{{$row->LEAVETYPE_DESC}}" class="form-control"  autocomplete="off"  readonly /></td>
                    <td><input  type="text" name={{"txtD_popup_".$key}} id={{"txtD_popup_".$key}} value="{{$row->DESGCODE.'-'.$row->DESCRIPTIONS}}" class="form-control"  autocomplete="off"  readonly/></td>
                    <td  hidden><input type="text"   name={{"DID_REF_".$key}} id={{"DID_REF_".$key}} value="{{$row->DESGID_REF}}" class="form-control" autocomplete="off" /></td>   
                    <td style="text-align:center;"><input type="input"   name={{"MAX_LEAVE_YEAR1_".$key}} id={{"MAX_LEAVE_YEAR1_".$key}} value="{{$row->YEARLEAVE_MAX}}" onkeypress="return isNumberDecimalKey(event,this)" class="form-control"  ></td>
                      <td style="text-align:center;"><input type="input"  name={{"MAX_COMMULATIVE_LEAVE1_".$key}} id={{"MAX_COMMULATIVE_LEAVE1_".$key}} value="{{$row->CUMM_LEAVE_MAX}}" onkeypress="return isNumberDecimalKey(event,this)" class="form-control" ></td>

                   
                                            
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



<!-- Payperiod  Dropdown -->
<div id="Period_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:60%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='Department_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Pay Period</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="PeroidTable" class="display nowrap table  table-striped table-bordered" style="width:100%;">
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
    <input type="text" id="Periodscodesearch" class="form-control" onkeyup="PeriodCodeFunction()">
    </td>
    <td style="width:60%;">
    <input type="text" id="Periodnamesearch" class="form-control" onkeyup="PeriodNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="PeroidTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          
        </thead>
        <tbody id="period_result">   
 
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<!--Leave Type  dropdown-->
<div id="LeaveTypepopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='LT_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Leave Type List</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="LeaveTypeTable" class="display nowrap table  table-striped table-bordered" >
    <thead>
          <tr id="none-select" class="searchalldata" hidden>
            
            <td> 
            <input type="hidden"  id="hdn_LTid"/>
            <input type="hidden" id="hdn_LTid2"/>
            <input type="hidden" id="hdn_LTid3"/>
            </td>
          </tr>

      <tr>
        <th class="ROW1">Select</th> 
        <th class="ROW2">Code</th>
        <th class="ROW3">Description</th>
      </tr>
    </thead>
    <tbody>

      <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="LTcodesearch" class="form-control" onkeyup="LeaveTypeCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="LTnamesearch" class="form-control" onkeyup="LeaveTypeNameFunction()"></td>
      </tr>

    </tbody>
    </table>
      <table id="LeaveTypeTable2" class="display nowrap table  table-striped table-bordered"  >
        <thead id="thead2">

        </thead>
        <tbody id="tbody_LT">     
        
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>




<!--Leave Type  dropdown-->
<div id="LeaveTypepopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='LT_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Leave Type List</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="LeaveTypeTable" class="display nowrap table  table-striped table-bordered" >
    <thead>
          <tr id="none-select" class="searchalldata" hidden>
            
            <td> 
            <input type="hidden"  id="hdn_LTid"/>
            <input type="hidden" id="hdn_LTid2"/>
            <input type="hidden" id="hdn_LTid3"/>
            </td>
          </tr>

      <tr>
        <th class="ROW1">Select</th> 
        <th class="ROW2">Code</th>
        <th class="ROW3">Description</th>
      </tr>
    </thead>
    <tbody>

      <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="LTcodesearch" class="form-control" onkeyup="LeaveTypeCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="LTnamesearch" class="form-control" onkeyup="LeaveTypeNameFunction()"></td>
      </tr>

    </tbody>
    </table>
      <table id="LeaveTypeTable2" class="display nowrap table  table-striped table-bordered"  >
        <thead id="thead2">

        </thead>
        <tbody id="tbody_LT">     
        
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>



<!--Leave Type  dropdown-->
<div id="DesignationTypepopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='D_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Designation List</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="DesignationTable" class="display nowrap table  table-striped table-bordered" >
    <thead>
          <tr id="none-select" class="searchalldata" hidden>
            
            <td> 
            <input type="hidden"  id="hdn_Did"/>
            <input type="hidden" id="hdn_Did2"/>
            <input type="hidden" id="hdn_Did3"/>
            <input type="hidden" id="hdn_Did4"/>
            </td>
          </tr>

      <tr>
        <th class="ROW1">Select</th> 
        <th class="ROW2">Code</th>
        <th class="ROW3">Description</th>
      </tr>
    </thead>
    <tbody>

      <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="Dcodesearch" class="form-control" onkeyup="DesignationCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="Dnamesearch" class="form-control" onkeyup="DesignationNameFunction()"></td>
      </tr>

    </tbody>
    </table>
      <table id="DesignationTable2" class="display nowrap table  table-striped table-bordered"  >
        <thead id="thead2">

        </thead>
        <tbody id="tbody_D">     
        
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


.filter input {
    float: none !important;
}

</style>
@endpush
@push('bottom-scripts')
<script>



 //==================== Payperiod dropdown code starts here ====================//

 
   //Payperiod Type dropdown Function starts here 
   let fuel = "#PeroidTable2";
      let fuel2 = "#PeroidTable";
      let fuelheaders = document.querySelectorAll(fuel2 + " th");

      // Sort the table element when clicking on the table headers
      fuelheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(fuel, ".clsspid_department", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function PeriodCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Periodscodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("PeroidTable2");
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

  function PeriodNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Periodnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("PeroidTable2");
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

  function get_period(id){ 
    
         $("#Period_popup").show();
         loadPeriod(id); 
         event.preventDefault();

  }



  function loadPeriod(PERIOD_TYPE){   

   $("#period_result").html('');
   $.ajaxSetup({
     headers: {
       'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
     }
   });
 
   $.ajax({
     url:'{{route("master",[$FormId,"getPeriod"])}}',
     type:'POST',
     data:{'PERIOD_TYPE':PERIOD_TYPE},
     success:function(data) {
       
       $("#period_result").html(data); 
       bindPeriod(PERIOD_TYPE);
      //showSelectedCheck($("#"+PERIOD_TYPE).val(),PERIOD_TYPE); 
     },
     error:function(data){
     console.log("Error: Something went wrong.");
     $("#period_result").html('');                        
     },
   });
 }


      $("#Department_closePopup").click(function(event){
        $("#Period_popup").hide();
      });



    function bindPeriod(id){
      showSelectedCheck($("#"+id).val(),id); 
      var result=id.split('_');  
      var PERIOID= result[1]; 

      $(".clsspid_period").click(function(){
        var fieldid         = $(this).attr('id');
        var txtval          =    $("#txt"+fieldid+"").val();
        var code         =   $("#txt"+fieldid+"").data("code");
        var desc            =   $("#txt"+fieldid+"").data("desc");   


        $('#PERIOD_popup_'+PERIOID).val(code);
        $('#PERIOD_'+PERIOID).val(txtval);
        $('#PERIOD_DESC_'+PERIOID).val(desc);       


        $("#Period_popup").hide();        
        $("#Periodscodesearch").val(''); 
        $("#Periodnamesearch").val('');    
        event.preventDefault();
      });
      }
//================================== Payperiod ends here   =================================

//================================== Earning Head Section  =================================

let LeaveTypeTable2 = "#LeaveTypeTable2";
let LeaveTypeTable = "#LeaveTypeTable";
let QCPheaders = document.querySelectorAll(LeaveTypeTable + " th");

QCPheaders.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(LeaveTypeTable2, ".clssLTid", "td:nth-child(" + (i + 1) + ")");
  });
});

function LeaveTypeCodeFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("LTcodesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("LeaveTypeTable2");
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

function LeaveTypeNameFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("LTnamesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("LeaveTypeTable2");
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

//====================================Leave type for First Tab=========================================================

$('#Material').on('click','[id*="txtLT_popup"]',function(event){
$('#hdn_LTid').val($(this).attr('id'));
$('#hdn_LTid2').val($(this).parent().parent().find('[id*="LTID_REF"]').attr('id'));
$('#hdn_LTid3').val($(this).parent().parent().find('[id*="LT_REF"]').attr('id'));
var fieldid = $(this).parent().parent().find('[id*="LTID_REF"]').attr('id');
var click_button="clssLTid";



  $("#LeaveTypepopup").show();
  $("#tbody_LT").html('loading...');

  $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  })

  $.ajax({
      url:'{{route("master",[$FormId,"get_LeaveType"])}}',
      type:'POST',
      data:{'fieldid':fieldid,'class_name':click_button},
      success:function(data) {
        $("#tbody_LT").html(data);
        BindLT();
        showSelectedCheck($("#"+fieldid).val(),"SELECT_"+fieldid);
      },
      error:function(data){
        console.log("Error: Something went wrong.");
        $("#tbody_LT").html('');
      },
  });



});

$("#LT_closePopup").click(function(event){
$("#LeaveTypepopup").hide();
});

function BindLT(){
$(".clssLTid").click(function(){
  var fieldid = $(this).attr('id');
  var txtval =    $("#txt"+fieldid+"").val();
  var texdesc =   $("#txt"+fieldid+"").data("desc");
  var texdesc1 =   $("#txt"+fieldid+"").data("desc1");

  var txtid   = $('#hdn_LTid').val();
  var txt_id2 = $('#hdn_LTid2').val();
  var txt_id3 = $('#hdn_LTid3').val();



  var get_id = txtid.split('_');
  var rowid=get_id[2];

  var CheckExist  = []; 
  CheckExist.push('true');

  $('#example2').find('.participantRow').each(function(){

    var LTID_REF = $(this).find('[id*="LTID_REF"]').val();

    if(txtval){
      if(txtval == LTID_REF){
        CheckExist.push('false');
        return false;
      }               
    }
  });

   old_value=$("#LTID_REF_"+rowid).val();
    if(txtval!='' && txtval===old_value){
    $('#'+txtid).val(texdesc);
    $('#'+txt_id2).val(txtval);
    $('#'+txt_id3).val(texdesc1);
    $("#LeaveTypepopup").hide();
    return false;   
    }else if(jQuery.inArray("false", CheckExist) !== -1){
    $(this).find('[id*="txtLT_popup"]').val();
    $(this).find('[id*="LTID_REF"]').val();
    $(this).find('[id*="LT_REF"]').val();

    $("#FocusId").val(txtid);
    $("#alert").modal('show');
    $("#AlertMessage").text('Leave Type already Exist.');
    $("#YesBtn").hide(); 
    $("#NoBtn").hide();  
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk');
    $("#LeaveTypepopup").hide();
    return false;
  }
  else{
    $('#'+txtid).val(texdesc);
    $('#'+txt_id2).val(txtval);
    $('#'+txt_id3).val(texdesc1);
    $('#MAX_LEAVE_YEAR_'+rowid).prop('checked', false);
    $('#MAX_COMMULATIVE_LEAVE_'+rowid).prop('checked', false);
    $('#CARRRY_FORWARD_LEAVE_'+rowid).prop('checked', false);
    $('#HALF_DAY_LEAVE_'+rowid).prop('checked', false);
    $('#LAPSE_LEAVE_'+rowid).prop('checked', false);
    $('#LEAVE_ENCASHMENT_'+rowid).prop('checked', false);
    $('#SANDWITCH_LEAVE_'+rowid).prop('checked', false);
    $('#NON_DUE_LEAVE_'+rowid).prop('checked', false);
    $('#DESIGNATION_SPECIFIC_'+rowid).prop('checked', false);
    $('#DOCUMENT_REQUIRED_'+rowid).prop('checked', false);
    $('#MAX_LEAVE_GENDER_'+rowid).val('');
    $('#MAX_LEAVE_KID_'+rowid).val('');
    $('#DEACTIVATED_'+rowid).prop('checked', false);
    $('#GENDER_SPECIFIC_'+rowid+'  option[value=""]').prop("selected", true);
  }

  $("#LeaveTypepopup").hide();
  $("#LTcodesearch").val(''); 
  $("#LTnamesearch").val(''); 
  LeaveTypeCodeFunction();
  event.preventDefault();

});
}

//==========================================Leave Type First Tab ends here======================================================================

//====================================Leave type for Second Tab================================================================================

$('#DesignationSpecific').on('click','[id*="txtLT_popup1"]',function(event){
$('#hdn_LTid').val($(this).attr('id'));
$('#hdn_LTid2').val($(this).parent().parent().find('[id*="LTID_REF1"]').attr('id'));
$('#hdn_LTid3').val($(this).parent().parent().find('[id*="LT_REF1"]').attr('id'));
var fieldid = $(this).parent().parent().find('[id*="LTID_REF1"]').attr('id');
var click_button="clssLTid1";



  $("#LeaveTypepopup").show();
  $("#tbody_LT").html('loading...');

  $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  })

  $.ajax({
      url:'{{route("master",[$FormId,"get_LeaveType"])}}',
      type:'POST',
      data:{'fieldid':fieldid,'class_name':click_button},
      success:function(data) {
        $("#tbody_LT").html(data);
        BindLT1();
        showSelectedCheck($("#"+fieldid).val(),"SELECT_"+fieldid);
      },
      error:function(data){
        console.log("Error: Something went wrong.");
        $("#tbody_LT").html('');
      },
  });



});

$("#LT_closePopup").click(function(event){
$("#LeaveTypepopup").hide();
});

function BindLT1(){
$(".clssLTid1").click(function(){
  var fieldid = $(this).attr('id');
  var txtval =    $("#txt"+fieldid+"").val();
  var texdesc =   $("#txt"+fieldid+"").data("desc");
  var texdesc1 =   $("#txt"+fieldid+"").data("desc1");

  var txtid   = $('#hdn_LTid').val();
  var txt_id2 = $('#hdn_LTid2').val();
  var txt_id3 = $('#hdn_LTid3').val();



  var get_id = txtid.split('_');
  var rowid=get_id[2];

  // var CheckExist  = []; 
  // CheckExist.push('true');

  // $('#example3').find('.participantRow1').each(function(){

  //   var LTID_REF = $(this).find('[id*="LTID_REF1"]').val();

  //   if(txtval){
  //     if(txtval == LTID_REF){
  //       CheckExist.push('false');
  //       return false;
  //     }               
  //   }
  // });

  //  old_value=$("#LTID_REF1_"+rowid).val();
  //     if(txtval!='' && txtval===old_value){
  //   $('#'+txtid).val(texdesc);
  //   $('#'+txt_id2).val(txtval);
  //   $('#'+txt_id3).val(texdesc1);
  //   $("#LeaveTypepopup").hide();
  //   return false;   
  //   }else if(jQuery.inArray("false", CheckExist) !== -1){
  //   $(this).find('[id*="txtLT_popup1"]').val();
  //   $(this).find('[id*="LTID_REF1"]').val();
  //   $(this).find('[id*="LT_REF1"]').val();

  //   $("#FocusId").val(txtid);
  //   $("#alert").modal('show');
  //   $("#AlertMessage").text('Leave Type already Exist.');
  //   $("#YesBtn").hide(); 
  //   $("#NoBtn").hide();  
  //   $("#OkBtn1").show();
  //   $("#OkBtn1").focus();
  //   highlighFocusBtn('activeOk');
  //   $("#LeaveTypepopup").hide();
  //   return false;
  // }
  // else{

    $('#'+txtid).val(texdesc);
    $('#'+txt_id2).val(txtval);
    $('#'+txt_id3).val(texdesc1);

    $('#txtD_popup_'+rowid).val('');
    $('#DID_REF_'+rowid).val('');
    $('#MAX_LEAVE_YEAR1_'+rowid).val('');
    $('#MAX_COMMULATIVE_LEAVE1_'+rowid).val('');

  //}

  $("#LeaveTypepopup").hide();
  $("#LTcodesearch").val(''); 
  $("#LTnamesearch").val(''); 
  LeaveTypeCodeFunction();
  event.preventDefault();

});
}



//==========================================Leave Type Second Tab ends here======================================================================





//=========================================Designation popup starts here ======================================================================
let DesignationTable2 = "#DesignationTable2";
let DesignationTable = "#DesignationTable";
let QCPheaders1 = document.querySelectorAll(DesignationTable + " th");

QCPheaders1.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(DesignationTable2, ".clssDid", "td:nth-child(" + (i + 1) + ")");
  });
});

function DesignationCodeFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("Dcodesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("DesignationTable2");
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

function DesignationNameFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("Dnamesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("DesignationTable2");
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

$('#DesignationSpecific').on('click','[id*="txtD_popup"]',function(event){
$('#hdn_Did').val($(this).attr('id'));
$('#hdn_Did2').val($(this).parent().parent().find('[id*="DID_REF"]').attr('id'));
$('#hdn_Did3').val($(this).parent().parent().find('[id*="D_REF"]').attr('id'));
$('#hdn_Did4').val($(this).parent().parent().find('[id*="LTID_REF1"]').val());


leavetypeid='';
var leavetypeid=$(this).parent().parent().find('[id*="LTID_REF1"]').attr('id');
var fieldid = $(this).parent().parent().find('[id*="DID_REF"]').attr('id');
var click_button="clssDid";
var leavetype=$('#'+leavetypeid).val(); 




if(leavetype===''){
  $("#FocusId").val(leavetypeid);
  $("#alert").modal('show');
  $("#AlertMessage").text('Please select Leave Type First.');
  $("#YesBtn").hide(); 
  $("#NoBtn").hide();  
  $("#OkBtn1").show();
  $("#OkBtn1").focus();
  highlighFocusBtn('activeOk');
  return false;

}



  $("#DesignationTypepopup").show();
  $("#tbody_D").html('loading...');

  $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  })

  $.ajax({
      url:'{{route("master",[$FormId,"get_Designation"])}}',
      type:'POST',
      data:{'fieldid':fieldid,'class_name':click_button},
      success:function(data) {
        $("#tbody_D").html(data);
        BindD();
        showSelectedCheck($("#"+fieldid).val(),"SELECT_"+fieldid);
      },
      error:function(data){
        console.log("Error: Something went wrong.");
        $("#tbody_D").html('');
      },
  });



});

$("#D_closePopup").click(function(event){
$("#DesignationTypepopup").hide();
});

function BindD(){
$(".clssDid").click(function(){
  var fieldid = $(this).attr('id');
  var txtval =    $("#txt"+fieldid+"").val();
  var texdesc =   $("#txt"+fieldid+"").data("desc");
  var texdesc1 =   $("#txt"+fieldid+"").data("desc1");



  var txtid   = $('#hdn_Did').val();
  var txt_id2 = $('#hdn_Did2').val();
  var txt_id3 = $('#hdn_Did3').val();
  var txt_id4 = $('#hdn_Did4').val();

  var get_id = txtid.split('_');
  var rowid=get_id[2];

  //var CheckExist  = []; 
  //CheckExist.push('true');

  // $('#example3').find('.participantRow1').each(function(){

  //   var DID_REF = $(this).find('[id*="DID_REF"]').val();

  //   if(txtval){
  //     if(txtval == DID_REF){
  //       CheckExist.push('false');
  //       return false;
  //     }               
  //   }
  // });



  var  buref =  txt_id4; //todo: set on focus
    var ArrData = [];
    $('#example3').find('.participantRow1').each(function(){
      if($(this).find('[id*="LTID_REF1_"]').val() != '')
      {
        var tmpitem = $(this).find('[id*="LTID_REF1_"]').val()+'-'+$(this).find('[id*="DID_REF_"]').val();

        ArrData.push(tmpitem);
      }
    });

    var recdata = buref+'-'+txtval;

   old_designationid=$("#DID_REF_"+rowid).val();
   old_leavetypeid=$("#LTID_REF1_"+rowid).val();

   old_value=old_designationid+'-'+old_leavetypeid;


   currentid=txtval+'-'+old_leavetypeid;
   

    if(txtval!='' && currentid===old_value){
    $('#'+txtid).val(texdesc);
    $('#'+txt_id2).val(txtval);
    $("#DesignationTypepopup").hide();
    return false;   
     }else if(jQuery.inArray(recdata, ArrData) !== -1){
   $(this).find('[id*="txtD_popup"]').val();
    $(this).find('[id*="DID_REF"]').val();
    $(this).find('[id*="D_REF1"]').val();

    $("#FocusId").val(txtid);
    $("#alert").modal('show');
    $("#AlertMessage").text('Designation already Exist.');
    $("#YesBtn").hide(); 
    $("#NoBtn").hide();  
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk');
    $("#DesignationTypepopup").hide();
    return false;
                
              }
  //else if(jQuery.inArray("false", CheckExist) !== -1){
  //   $(this).find('[id*="txtD_popup"]').val();
  //   $(this).find('[id*="DID_REF"]').val();
  //   $(this).find('[id*="D_REF1"]').val();

  //   $("#FocusId").val(txtid);
  //   $("#alert").modal('show');
  //   $("#AlertMessage").text('Earning Head already Exist.');
  //   $("#YesBtn").hide(); 
  //   $("#NoBtn").hide();  
  //   $("#OkBtn1").show();
  //   $("#OkBtn1").focus();
  //   highlighFocusBtn('activeOk');
  //   $("#DesignationTypepopup").hide();
  //   return false;
  // }
  else{

    $('#'+txtid).val(texdesc);
    $('#'+txt_id2).val(txtval);
    $('#MAX_LEAVE_YEAR1_'+rowid).val('');
    $('#MAX_COMMULATIVE_LEAVE1_'+rowid).val('');

  }

  $("#DesignationTypepopup").hide();
  $("#Dcodesearch").val(''); 
  $("#Dnamesearch").val(''); 
  DesignationCodeFunction();
  event.preventDefault();

});
}
//=========================================Designation popup ends here ======================================================================

     
$(document).ready(function(e) {
// var Material = $("#Material").html(); 
// $('#hdnmaterial').val(Material);


$('#btnAdd').on('click', function() {
  var viewURL = '{{route("master",[$FormId,"add"])}}';
              window.location.href=viewURL;
              
});

$('#btnExit').on('click', function() {
  var viewURL = '{{route('home')}}';
              window.location.href=viewURL;
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
  $clone.find('input:hidden').val('');
  $clone.find('input:checkbox').prop('checked', false);
  var d         = new Date(); 
  var today     = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;

  //var h         = new Date($("#EDA").val()); 
  //var headDate  = h.getFullYear() + "-" + ("0" + (h.getMonth() + 1)).slice(-2) + "-" + ('0' + h.getDate()).slice(-2) ;
  
  //$clone.find('[id*="EDD"]').val(headDate);

  
  $tr.closest('table').append($clone);         
  var rowCount1 = $('#Row_Count1').val();
  rowCount1 = parseInt(rowCount1)+1;
  $('#Row_Count1').val(rowCount1);
  $clone.find('.remove').removeAttr('disabled'); 
  event.preventDefault();
});





//For Second Tab
$("#DesignationSpecific").on('click', '.remove', function() {
    var rowCount = $(this).closest('table').find('.participantRow1').length;
    if (rowCount > 1) {
    $(this).closest('.participantRow1').remove();     
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



//add row
$("#DesignationSpecific").on('click', '.add', function() {
  var $tr = $(this).closest('table');
  var allTrs = $tr.find('.participantRow1').last();
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
  $clone.find('input:hidden').val('');
  $clone.find('input:checkbox').prop('checked', false);
  var d         = new Date(); 
  var today     = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;

  //var h         = new Date($("#EDA").val()); 
  //var headDate  = h.getFullYear() + "-" + ("0" + (h.getMonth() + 1)).slice(-2) + "-" + ('0' + h.getDate()).slice(-2) ;
  
  //$clone.find('[id*="EDD"]').val(headDate);

  
  $tr.closest('table').append($clone);         
  var rowCount1 = $('#Row_Count2').val();
  rowCount1 = parseInt(rowCount1)+1;
  $('#Row_Count2').val(rowCount1);
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
  window.location.href = "{{route('master',[$FormId,'add'])}}";
}//fnUndoYes


window.fnUndoNo = function (){
    $("#BASIS_DOC_NO").focus();
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
});
</script>

@endpush

@push('bottom-scripts')
<script>

$(document).ready(function() {

  



  $("#btnSaveSE").on("submit", function( event ) {

    if ($("#frm_mst_edit").valid()) {

        // Do something
        alert( "Handler for .submit() called." );
        event.preventDefault();
    }
});


    $('#frm_mst_edit1').bootstrapValidator({
       
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
             $("#frm_mst_edit").submit();
        }
    });
});


//=======================================// NUMBER DECIMAL CHECK //=============================================//

function isNumberDecimalKey(evt){
  var charCode = (evt.which) ? evt.which : event.keyCode
  if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
  return false;

  return true;
}


//=======================================================VALIDATION SECTION =========================================

function validateForm(){
 
 $("#FocusId").val('');
 var PERIOD_REF1          =   $.trim($("#PERIOD_REF1").val());

 if(PERIOD_REF1 ===""){
 $("#FocusId").val('PERIOD_popup_REF1');  
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select Pay Period.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
 }
 else{

event.preventDefault();

var RackArray = []; 
var allblank1 = [];
var allblank2 = [];
var allblank3 = [];
var allblank4 = [];
var allblank5 = [];
var allblank6 = [];
var allblank7 = [];
var allblank8 = [];
var allblank9 = [];
var allblank10 = [];
var allblank11 = [];

allblank1.push('true');
allblank2.push('true');
allblank3.push('true');
allblank4.push('true');
allblank5.push('true');
allblank6.push('true');
allblank7.push('true');
allblank8.push('true');
allblank9.push('true');
allblank10.push('true');
allblank11.push('true');

var focustext1= "";
var focustext2= "";
var focustext3= "";
var focustext4= "";
var focustext5= "";
var focustext6= "";
var focustext7= "";
var focustext8= "";
var focustext9= "";
var focustext10= "";
var focustext11= "";

$('#Material').find('.participantRow').each(function(){

  var LTID_REF           = $.trim($(this).find("[id*=LTID_REF]").val());
  var MAX_LEAVE_YEAR  = $.trim($(this).find("[id*=MAX_LEAVE_YEAR]").val());
  var MAX_COMMULATIVE_LEAVE  = $.trim($(this).find("[id*=MAX_COMMULATIVE_LEAVE]").val());
  var GENDER_SPECIFIC            = $.trim($(this).find("[id*=GENDER_SPECIFIC]").val());
  var MAX_LEAVE_GENDER             = $.trim($(this).find("[id*=MAX_LEAVE_GENDER]").val());
  var MAX_LEAVE_KID              = $.trim($(this).find("[id*=MAX_LEAVE_KID]").val());

  var DEACTIVATED              = $(this).find("[id*=DEACTIVATED]").is(":checked");
  var DODEACTIVATED              = $.trim($(this).find("[id*=DODEACTIVATED]").val());



  if(LTID_REF ===""){
    allblank1.push('false');
    focustext1 = $(this).find("[id*=txtLT_popup]").attr('id');

    
  }
  else if(MAX_LEAVE_YEAR ===""){
    allblank2.push('false');
    focustext2 = $(this).find("[id*=MAX_LEAVE_YEAR]").attr('id');
  }
  else if(MAX_COMMULATIVE_LEAVE ===""){
    allblank3.push('false');
    focustext3 = $(this).find("[id*=MAX_COMMULATIVE_LEAVE]").attr('id');
  }
  else if(GENDER_SPECIFIC ===""){
    allblank4.push('false');
    focustext4 = $(this).find("[id*=GENDER_SPECIFIC]").attr('id'); 
  }
  else if(MAX_LEAVE_GENDER ===""){
    allblank5.push('false');
    focustext5 = $(this).find("[id*=MAX_LEAVE_GENDER]").attr('id'); 
  }
  else if(MAX_LEAVE_KID ===""){
    allblank6.push('false');
    focustext6 = $(this).find("[id*=MAX_LEAVE_KID]").attr('id'); 
  }
  else if(DEACTIVATED == true && DODEACTIVATED ==="" ){

    allblank7.push('false');
    focustext7 = $(this).find("[id*=DODEACTIVATED]").attr('id'); 
  }



});

$('#DesignationSpecific').find('.participantRow1').each(function(){

var LTID_REF1           = $.trim($(this).find("[id*=LTID_REF1]").val());
var DID_REF  = $.trim($(this).find("[id*=DID_REF]").val());
var MAX_LEAVE_YEAR1  = $.trim($(this).find("[id*=MAX_LEAVE_YEAR1]").val());
var MAX_COMMULATIVE_LEAVE1            = $.trim($(this).find("[id*=MAX_COMMULATIVE_LEAVE1]").val());

if(LTID_REF1 ===""){
  allblank8.push('false');
  focustext8 = $(this).find("[id*=txtLT_popup1]").attr('id');

  
}
else if(DID_REF ===""){
  allblank9.push('false');
  focustext9 = $(this).find("[id*=txtD_popup]").attr('id');
}
else if(MAX_LEAVE_YEAR1 ===""){
  allblank10.push('false');
  focustext10 = $(this).find("[id*=MAX_LEAVE_YEAR1]").attr('id');
}
else if(MAX_COMMULATIVE_LEAVE1 ===""){
  allblank11.push('false');
  focustext11 = $(this).find("[id*=MAX_COMMULATIVE_LEAVE1]").attr('id'); 
}




});

if(jQuery.inArray("false", allblank1) !== -1){
  $('#tabing1').trigger('click');
  $("#FocusId").val(focustext1);
  $("#alert").modal('show');
  $("#AlertMessage").text('Please Select Leave Type in Leave Rule Tab');
  $("#YesBtn").hide(); 
  $("#NoBtn").hide();  
  $("#OkBtn1").show();
  $("#OkBtn1").focus();
  highlighFocusBtn('activeOk');
}
else if(jQuery.inArray("false", allblank2) !== -1){
  $('#tabing1').trigger('click');
  $("#FocusId").val(focustext2);
  $("#alert").modal('show');
  $("#AlertMessage").text('Please Enter Max Leaves in a year (Days) in Leave Rule Tab');
  $("#YesBtn").hide(); 
  $("#NoBtn").hide();  
  $("#OkBtn1").show();
  $("#OkBtn1").focus();
  highlighFocusBtn('activeOk');
}
else if(jQuery.inArray("false", allblank3) !== -1){
  $('#tabing').trigger('click');
  $("#FocusId").val(focustext3);
  $("#alert").modal('show');
  $("#AlertMessage").text('Please Enter Max Cummulative leaves (Cap Limit) (Days) in Leave Rule Tab ');
  $("#YesBtn").hide(); 
  $("#NoBtn").hide();  
  $("#OkBtn1").show();
  $("#OkBtn1").focus();
  highlighFocusBtn('activeOk');
}
else if(jQuery.inArray("false", allblank4) !== -1){
  $('#tabing1').trigger('click');
  $("#FocusId").val(focustext4);
  $("#alert").modal('show');
  $("#AlertMessage").text('Please Select Gender Specific in Leave Rule Tab');
  $("#YesBtn").hide(); 
  $("#NoBtn").hide();  
  $("#OkBtn1").show();
  $("#OkBtn1").focus();
  highlighFocusBtn('activeOk');
}
else if(jQuery.inArray("false", allblank5) !== -1){
  $('#tabing1').trigger('click');
  $("#FocusId").val(focustext5);
  $("#alert").modal('show');
  $("#AlertMessage").text('Please Enter Max Leaves (in case gender specific) in Leave Rule Tab');
  $("#YesBtn").hide(); 
  $("#NoBtn").hide();  
  $("#OkBtn1").show();
  $("#OkBtn1").focus();
  highlighFocusBtn('activeOk');
}
else if(jQuery.inArray("false", allblank6) !== -1){
  $('#tabing1').trigger('click');
  $("#FocusId").val(focustext6);
  $("#alert").modal('show');
  $("#AlertMessage").text('Please Enter Max Kids / Delivery (in case of gender specific) in Leave Rule Tab');
  $("#YesBtn").hide(); 
  $("#NoBtn").hide();  
  $("#OkBtn1").show();
  $("#OkBtn1").focus();
  highlighFocusBtn('activeOk');
}
else if(jQuery.inArray("false", allblank7) !== -1){
  $('#tabing1').trigger('click');
  $("#FocusId").val(focustext7);
  $("#alert").modal('show');
  $("#AlertMessage").text('Please Select Date of Deactivation in Leave Rule Tab');
  $("#YesBtn").hide(); 
  $("#NoBtn").hide();  
  $("#OkBtn1").show();
  $("#OkBtn1").focus();
  highlighFocusBtn('activeOk');
  return false;
}
else if(jQuery.inArray("false", allblank8) !== -1){
  $('#tabing2').trigger('click');
  $("#FocusId").val(focustext8);
  $("#alert").modal('show');
  $("#AlertMessage").text('Please Select Leave Rule In Designation Specific Tab');
  $("#YesBtn").hide(); 
  $("#NoBtn").hide();  
  $("#OkBtn1").show();
  $("#OkBtn1").focus();
  highlighFocusBtn('activeOk');
  return false;
}
else if(jQuery.inArray("false", allblank9) !== -1){
  $('#tabing2').trigger('click');
  $("#FocusId").val(focustext9);
  $("#alert").modal('show');
  $("#AlertMessage").text('Please Select Designation in Designation Specific Tab');
  $("#YesBtn").hide(); 
  $("#NoBtn").hide();  
  $("#OkBtn1").show();
  $("#OkBtn1").focus();
  highlighFocusBtn('activeOk');
  return false;
}
else if(jQuery.inArray("false", allblank10) !== -1){
  $('#tabing2').trigger('click');
  $("#FocusId").val(focustext10);
  $("#alert").modal('show');
  $("#AlertMessage").text('Please Enter Max Cummulative leaves (Cap Limit) (Days) in Designation Specific Tab');
  $("#YesBtn").hide(); 
  $("#NoBtn").hide();  
  $("#OkBtn1").show();
  $("#OkBtn1").focus();
  highlighFocusBtn('activeOk');
  return false;
}
else if(jQuery.inArray("false", allblank11) !== -1){
  $('#tabing2').trigger('click');
  $("#FocusId").val(focustext11);
  $("#alert").modal('show');
  $("#AlertMessage").text('Please Enter Max Leaves in a year (Days) in Designation Specific Tab');
  $("#YesBtn").hide(); 
  $("#NoBtn").hide();  
  $("#OkBtn1").show();
  $("#OkBtn1").focus();
  highlighFocusBtn('activeOk');
  return false;
}
else{
  $("#alert").modal('show');
  $("#AlertMessage").text('Do you want to update the record.');
  $("#YesBtn").data("funcname","fnSaveData");  //set dynamic fucntion name
  $("#YesBtn").focus();
  $("#YesBtn").show();
  $("#NoBtn").show();
  $("#OkBtn").hide();
  $("#OkBtn1").hide();
  highlighFocusBtn('activeYes');
}    
}

}

$("#YesBtn").click(function(){

$("#alert").modal('hide');
var customFnName = $("#YesBtn").data("funcname");
    window[customFnName]();

}); //yes button

$("#btnSaveSE" ).click(function() {
    var formReqData = $("#frm_mst_edit");
    if(formReqData.valid()){
      validateForm();
    }
});

window.fnSaveData = function (){

//validate and save data
event.preventDefault();

     var trnseForm = $("#frm_mst_edit");
    var formData = trnseForm.serialize();
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$.ajax({
    url:'{{ route("master",[$FormId,"save"])}}',
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
            // $("#frm_mst_country").trigger("reset");

            $("#alert").modal('show');
            $("#OkBtn").focus();
            // window.location.href="{{ route('master',[90,'index']) }}";
        }
        else if(data.cancel) {                   
            console.log("cancel MSG="+data.msg);
            
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").show();

            $("#AlertMessage").text(data.msg);

            $(".text-danger").hide();
            // $("#frm_mst_country").trigger("reset");

            $("#alert").modal('show');
            $("#OkBtn1").focus();
            // window.location.href="{{ route('master',[90,'index']) }}";
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
function showAlert(msg){
  $("#alert").modal('show');
  $("#AlertMessage").text(msg);
  $("#YesBtn").hide(); 
  $("#NoBtn").hide();  
  $("#OkBtn1").show();
  $("#OkBtn1").focus();
  highlighFocusBtn('activeOk');
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
    window.location.href = '{{route("master",[$FormId,"index"]) }}';
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








      function checkDuplicateCode(){

        var PERIOD_REF1 = $.trim($("#PERIOD_REF1").val());       

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url:'{{route("master",[$FormId,"codeduplicate"])}}',
            type:'POST',
            data:{'PERIOD_REF1':PERIOD_REF1},
            success:function(data) {
                if(data.exists) {
                    $(".text-danger").hide();
                    $("#FocusId").val($("#PERIOD_REF1"));
                    $("#ProceedBtn").focus();
                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn1").show();
                    $("#AlertMessage").text('The Pay Period Already Exist.');
                    $("#alert").modal('show');
                    $("#OkBtn1").focus();
                    return false;
                }
                else{
                  $("#alert").modal('show');
                  $("#AlertMessage").text('Do you want to save to record.');
                  $("#YesBtn").data("funcname","fnSaveData");  //set dynamic fucntion name
                  $("#YesBtn").focus();
                  $("#YesBtn").show();
                  $("#NoBtn").show();
                  $("#OkBtn").hide();
                  $("#OkBtn1").hide();
                  highlighFocusBtn('activeYes');
                }                                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });
}



  
    
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


$('#Material').on('click','.DEACTIVATED',function(event){
    var id=this.id;
    var get_id=id.split("_");
    var currentid=get_id[1];
  if ($('#'+this.id).is(":checked")===true){
    $("#DODEACTIVATED_"+currentid).attr('disabled',false);
  }else{
    $("#DODEACTIVATED_"+currentid).attr('disabled',true);
    $("#DODEACTIVATED_"+currentid).val('');
  }

});

</script>


@endpush