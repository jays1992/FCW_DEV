@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
  <div class="row">
    <div class="col-lg-2"><a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Break Down Complaint Log</a></div>

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
@csrf
  {{isset($objResponse->ST_ADJUSTID[0]) ? method_field('PUT') : '' }}


  <div class="container-fluid filter">
	  <div class="inner-form">

      <div class="row">
        <div class="col-lg-2 pl"><p>Doc No</p></div>
        <div class="col-lg-2 pl">
          <input type="text" name="BDCL_DOCNO" id="BDCL_DOCNO" value="{{$docarray['DOC_NO']}}" {{$docarray['READONLY']}} class="form-control" maxlength="{{$docarray['MAXLENGTH']}}" autocomplete="off" style="text-transform:uppercase" >
          <script>docMissing(@json($docarray['FY_FLAG']));</script>
          <span class="text-danger" id="ERROR_BDCL_DOCNO"></span>
        </div>
              
        <div class="col-lg-2 pl"><p>Date</p></div>
        <div class="col-lg-2 pl">
          <input type="date" name="BDCL_DATE" id="BDCL_DATE" onchange='checkPeriodClosing("{{$FormId}}",this.value,1),getDocNoByEvent("BDCL_DOCNO",this,@json($doc_req))' value="{{ old('BDCL_DATE') }}" class="form-control mandatory"  placeholder="dd/mm/yyyy" >
          <span class="text-danger" id="ERROR_BDCL_DATE"></span>
        </div>

        <div class="col-lg-2 pl"><p>Time</p></div>
        <div class="col-lg-2 pl">
          <input type="time" name="TIME" id="TIME" value="{{ old('TIME') }}" class="form-control mandatory"  placeholder="" >
          <span class="text-danger" id="ERROR_TIME"></span>
        </div>
      </div>    

      <div class="row">
              

               <div class="col-lg-2 pl"><p>Complaint By</p></div>
                <div class="col-lg-2 pl">
                <input type="text" name="COMPLAINT_BY" id="COMPLAINT_BY" maxlength="50" value="{{ old('COMPLAINT_BY') }}" class="form-control mandatory"  placeholder="" >
               <span class="text-danger" id="ERROR_COMPLAINT_BY"></span>
                </div>  

                <div class="col-lg-2 pl"><p>Department</p></div>
                <div class="col-lg-2 pl">
                    <input type="text" name="Department_popup" id="txtDepartment_popup" class="form-control mandatory"  autocomplete="off"  readonly/>
                    <input type="hidden" name="DEPARTMENTID_REF" id="DEPARTMENTID_REF" class="form-control" autocomplete="off" />
                    <span class="text-danger" id="ERROR_Department_popup"></span>
                </div>  


                <div class="col-lg-2 pl"><p>Priority</p></div>
                <div class="col-lg-2 pl">
                    <input type="text" name="Priority_popup" id="txtPriority_popup" class="form-control mandatory"  autocomplete="off"  readonly/>
                    <input type="hidden" name="PRIORITYID_REF" id="PRIORITYID_REF" class="form-control" autocomplete="off" />
                    <span class="text-danger" id="ERROR_Priority_popup"></span>
                </div>  

                </div>  

                <div class="row">
               <div class="col-lg-2 pl"><p>Complaint To</p></div>
                <div class="col-lg-2 pl">
                <input type="text" name="COMPLAINT_TO" id="COMPLAINT_TO" maxlength="50" value="{{ old('COMPLAINT_TO') }}" class="form-control mandatory"  placeholder="" >
            
                          <span class="text-danger" id="ERROR_COMPLAINT_TO"></span>
                </div>  

                <div class="col-lg-2 pl"><p>Complaint For</p></div>
                <div class="col-lg-2 pl">
              
                      <input type="checkbox" name="COMPLAINT_FOR"  id="chk_Machine" checked value="Machine"  />&nbsp;&nbsp;<label>   Machine </label>
                </div>
                <div class="col-lg-2 pl">
                      <input type="checkbox" name="COMPLAINT_FOR" id="chk_Generator" value="Genset"     /> &nbsp;&nbsp;<label>   Generator </label>
                </div>    

                </div>  



        

        <div class="row">
               <div class="col-lg-2 pl"><p>Machine / Genset  Code</p></div>
                <div class="col-lg-2 pl">
                <input type="text" name="Machinepopup" id="txtMachinepopup" class="form-control mandatory"  autocomplete="off"  readonly/>
                    <input type="hidden" name="MACHINE_REF" id="MACHINE_REF" class="form-control" autocomplete="off" />
                    <span class="text-danger" id="ERROR_txtMachinepopup"></span>
                </div>  

                <div class="col-lg-2 pl"><p>Problem Log Detail </p></div>
                <div class="col-lg-6 pl">
                <input type="text" name="PROBLEM_LOG_DET" id="PROBLEM_LOG_DET" maxlength="50" value="{{ old('PROBLEM_LOG_DET') }}" class="form-control mandatory"  placeholder="" >
               <span class="text-danger" id="ERROR_PROBLEM_LOG_DET"></span>
                </div>  

        </div>  



        <div class="row">
                <div class="col-lg-2 pl"><p>Breakdown Reason Code 1</p></div>
                <div class="col-lg-2 pl">
                    <input type="text" name="Reasoncode1_popup" id="txtReasoncode1_popup" class="form-control mandatory"  autocomplete="off"  readonly/>
                    <input type="hidden" name="REASONCODE1_REF" id="REASONCODE1_REF" class="form-control" autocomplete="off" />
                    <span class="text-danger" id="ERROR_Reasoncode1"></span>
                </div>  
                <div class="col-lg-2 pl"><p>Breakdown Reason Description 1</p></div>
                <div class="col-lg-6 pl">
                    <input type="text" name="BREAKDOWN1_REASON_DESC" id="BREAKDOWN1_REASON_DESC" class="form-control mandatory"  autocomplete="off"  readonly/>
     
                </div>  

                </div> 
                
                

        <div class="row">
        <div class="col-lg-2 pl"><p>Breakdown Reason Code 2</p></div>
                <div class="col-lg-2 pl">
                    <input type="text" name="Reasoncode2_popup" id="txtReasoncode2_popup" class="form-control mandatory"  autocomplete="off"  readonly/>
                    <input type="hidden" name="REASONCODE2_REF" id="REASONCODE2_REF" class="form-control" autocomplete="off" />
                    <span class="text-danger" id="ERROR_Reasoncode2"></span>
                </div>  
                <div class="col-lg-2 pl"><p>Breakdown Reason Description 2</p></div>
                <div class="col-lg-6 pl">
                    <input type="text" name="BREAKDOWN2_REASON_DESC" id="BREAKDOWN2_REASON_DESC" class="form-control mandatory"  autocomplete="off"  readonly/>
     
                </div>   

                </div>  


                <div class="row">
                <div class="col-lg-2 pl"><p>Remarks 1</p></div>
                <div class="col-lg-4 pl">
                    <input type="text" name="REMARKS1" id="REMARKS1" class="form-control mandatory"  autocomplete="off"  />
     
                </div>  
                </div>  
                <div class="row">
                <div class="col-lg-2 pl"><p>Remarks 2</p></div>
                <div class="col-lg-4 pl">
                    <input type="text" name="REMARKS2" id="REMARKS2" class="form-control mandatory"  autocomplete="off"  />
     
                </div>  

                </div>  











    </div>

	  <div class="container-fluid">

 
	  </div>
  </div>
</form>
@endsection

@section('alert')
<div id="StoreModal" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:80%;z-index:1">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='StoreModalClose' >&times;</button>
      </div>
      <div class="modal-body">
	      <div class="tablename"><p>Store Details</p></div>
        <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
          <table id="StoreTable" class="display nowrap table  table-striped table-bordered" style="width: 100%;" ></table>
        </div>
		    <div class="cl"></div>
      </div>
    </div>
  </div>
</div>


<!-- Department Dropdown -->
<div id="Department_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:60%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='Department_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Department List</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="DepartmentTable" class="display nowrap table  table-striped table-bordered" style="width:100%;">
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
    <input type="text" id="Departmentcodesearch" class="form-control" onkeyup="DepartmentCodeFunction()">
    </td>
    <td style="width:60%;">
    <input type="text" id="Departmentnamesearch" class="form-control" onkeyup="DepartmentNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="DepartmentTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          
        </thead>
        <tbody >     
        @foreach ($objDepartment as $index=>$objDepartmentRow)
        <tr >
        <td style="text-align:center; width:10%"> <input type="checkbox" name="department[]" id="departmentidcode_{{ $index }}" class="clsspid_department"  value="{{ $objDepartmentRow->DEPID }}" ></td>



          <td style="width:30%">{{ $objDepartmentRow->DCODE }}
          <input type="hidden" id="txtdepartmentidcode_{{ $index }}" data-desc="{{ $objDepartmentRow->DCODE }} - {{ $objDepartmentRow-> NAME }}"  value="{{ $objDepartmentRow->DEPID }}"/>
          </td>
          <td style="width:60%">{{ $objDepartmentRow-> NAME }} </td>
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


<!-- Department Dropdown -->
<div id="Priority_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:60%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='Priority_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Priority List</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="PriorityTable" class="display nowrap table  table-striped table-bordered" style="width:100%;">
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
    <input type="text" id="Prioritycodesearch" class="form-control" onkeyup="PriorityCodeFunction()">
    </td>
    <td style="width:60%;">
    <input type="text" id="Prioritynamesearch" class="form-control" onkeyup="PriorityNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="PriorityTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          
        </thead>
        <tbody >     
        @foreach ($objPriority as $index=>$PriorityRow)
        <tr >
        <td style="text-align:center; width:10%"> <input type="checkbox" name="priority[]" id="priorityidcode_{{ $index }}" class="clsspid_priority"  value="{{ $PriorityRow->PRIORITYID }}" ></td>



          <td style="width:30%">{{ $PriorityRow->PRIORITYCODE }}
          <input type="hidden" id="txtpriorityidcode_{{ $index }}" data-desc="{{ $PriorityRow->PRIORITYCODE }} - {{ $PriorityRow-> DESCRIPTIONS }}"  value="{{ $PriorityRow->PRIORITYID }}"/>
          </td>
          <td style="width:60%">{{ $PriorityRow-> DESCRIPTIONS }} </td>
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



<!-- Reason 1 Dropdown -->
<div id="Reasoncode1_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:60%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='Reasoncode1_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Breakdown Reason List 1 </p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="Reasoncode1Table" class="display nowrap table  table-striped table-bordered" style="width:100%;">
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
    <input type="text" id="Reasoncode1codesearch" class="form-control" onkeyup="Reasoncode1CodeFunction()">
    </td>
    <td style="width:60%;">
    <input type="text" id="Reasoncode1namesearch" class="form-control" onkeyup="Reasoncode1NameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="Reasoncode1Table2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          
        </thead>
        <tbody >     
        @foreach ($objReason as $index=>$objReasonRow)
        <tr >
        <td style="text-align:center; width:10%"> <input type="checkbox" name="reason1[]" id="reasoncode1code_{{ $index }}" class="clsspid_reasoncode1"  value="{{ $objReasonRow->BD_REASONID }}" ></td>



          <td style="width:30%">{{ $objReasonRow->BD_REASON_CODE }}
          <input type="hidden" id="txtreasoncode1code_{{ $index }}" data-desc=" {{ $objReasonRow->BD_REASON_DESC  }}" data-code="{{ $objReasonRow-> BD_REASON_CODE }}" value="{{ $objReasonRow->BD_REASONID }}"/>
          </td>
          <td style="width:60%">{{ $objReasonRow-> BD_REASON_DESC }} </td>
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
<!--  Reason 1  Dropdown ends here -->



<!-- Reason 2 Dropdown -->
<div id="Reasoncode2_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:60%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='Reasoncode2_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Breakdown Reason List 2 </p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="Reasoncode2Table" class="display nowrap table  table-striped table-bordered" style="width:100%;">
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
    <input type="text" id="Reasoncode2codesearch" class="form-control" onkeyup="Reasoncode2CodeFunction()">
    </td>
    <td style="width:60%;">
    <input type="text" id="Reasoncode2namesearch" class="form-control" onkeyup="Reasoncode2NameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="Reasoncode2Table2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          
        </thead>
        <tbody >     
        @foreach ($objReason as $index=>$objReasonRow)
        <tr >
        <td style="text-align:center; width:10%"> <input type="checkbox" name="reason2[]" id="reasoncode2code_{{ $index }}" class="clsspid_reasoncode2"  value="{{ $objReasonRow->BD_REASONID }}" ></td>



          <td style="width:30%">{{ $objReasonRow->BD_REASON_CODE }}
          <input type="hidden" id="txtreasoncode2code_{{ $index }}" data-desc=" {{ $objReasonRow->BD_REASON_DESC  }}" data-code="{{ $objReasonRow-> BD_REASON_CODE }}" value="{{ $objReasonRow->BD_REASONID }}"/>
          </td>
          <td style="width:60%">{{ $objReasonRow-> BD_REASON_DESC }} </td>
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
<!--  Reason 1  Dropdown ends here -->



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
  var BDCL_DOCNO   = $.trim($("#BDCL_DOCNO").val());
  var TIME   = $.trim($("#TIME").val());
  var BDCL_DATE   = $.trim($("#BDCL_DATE").val());
  var COMPLAINT_BY    = $.trim($("#COMPLAINT_BY").val());
  var COMPLAINT_TO    = $.trim($("#COMPLAINT_TO").val());
  var DEPARTMENTID_REF    = $.trim($("#DEPARTMENTID_REF").val());
  var PRIORITYID_REF    = $.trim($("#PRIORITYID_REF").val());
  var MACHINE_REF    = $.trim($("#MACHINE_REF").val());
  var PROBLEM_LOG_DET    = $.trim($("#PROBLEM_LOG_DET").val());
  var REASONCODE1_REF    = $.trim($("#REASONCODE1_REF").val());
  var REASONCODE2_REF    = $.trim($("#REASONCODE2_REF").val());

  
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
  else if(BDCL_DATE ===""){
      $("#FocusId").val('BDCL_DATE');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please select Date.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  }
  else if(TIME ===""){
      $("#FocusId").val('TIME');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please select Time');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(DEPARTMENTID_REF ===""){
      $("#FocusId").val('txtDepartment_popup');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please select Department');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(PRIORITYID_REF ===""){
      $("#FocusId").val('txtPriority_popup');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please select priority');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(COMPLAINT_BY ===""){
      $("#FocusId").val('COMPLAINT_BY');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please enter Complaint By');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(COMPLAINT_TO ===""){
      $("#FocusId").val('COMPLAINT_TO');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please enter Complaint To');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(MACHINE_REF ===""){
      $("#FocusId").val('txtMachinepopup');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please select Machine / Genset Code');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(PROBLEM_LOG_DET ===""){
      $("#FocusId").val('PROBLEM_LOG_DET');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please enter problem log detail');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(REASONCODE1_REF ===""){
      $("#FocusId").val('txtReasoncode1_popup');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please select break down reason code1');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(REASONCODE2_REF ===""){
      $("#FocusId").val('txtReasoncode2_popup');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please select break down reason code2');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  }
  else if(checkPeriodClosing('{{$FormId}}',$("#BDCL_DATE").val(),0) ==0){
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


  //Department Dropdown starts here 
  let department = "#DepartmentTable2";
      let department2 = "#DepartmentTable";
      let departmentheaders = document.querySelectorAll(department2 + " th");

      // Sort the table element when clicking on the table headers
      departmentheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(department, ".clsspid_department", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function DepartmentCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Departmentcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("DepartmentTable2");
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

  function DepartmentNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Departmentnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("DepartmentTable2");
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

  $('#txtDepartment_popup').click(function(event){
    showSelectedCheck($("#DEPARTMENTID_REF").val(),"department");
         $("#Department_popup").show();
      });

      $("#Department_closePopup").click(function(event){
        $("#Department_popup").hide();
      });
      $(".clsspid_department").click(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");
        $('#txtDepartment_popup').val(texdesc);
        $('#DEPARTMENTID_REF').val(txtval);
        $("#Department_popup").hide();        
        $("#Departmentcodesearch").val(''); 
        $("#Departmentnamesearch").val('');    
        event.preventDefault();
      });

  //Department Dropdown Ends here



    //Priority Dropdown starts here 
    let priority = "#PriorityTable2";
      let priority2 = "#PriorityTable";
      let priorityheaders = document.querySelectorAll(priority2 + " th");

      // Sort the table element when clicking on the table headers
      priorityheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(priority, ".clsspid_priority", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function PriorityCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Prioritycodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("PriorityTable2");
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

  function PriorityNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Prioritynamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("PriorityTable2");
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

  $('#txtPriority_popup').click(function(event){



    showSelectedCheck($("#PRIORITYID_REF").val(),"priority");
         $("#Priority_popup").show();
      });

      $("#Priority_closePopup").click(function(event){
        $("#Priority_popup").hide();
      });
      $(".clsspid_priority").click(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");
        $('#txtPriority_popup').val(texdesc);
        $('#PRIORITYID_REF').val(txtval);
        $("#Priority_popup").hide();        
        $("#Prioritycodesearch").val(''); 
        $("#Prioritynamesearch").val('');    
        event.preventDefault();
      });

  //Department Dropdown Ends here




      //Reason code 1 Dropdown starts here 
      let reasoncode1 = "#Reasoncode1Table2";
      let reasoncode12 = "#Reasoncode1Table";
      let reasoncode1headers = document.querySelectorAll(reasoncode12 + " th");

      // Sort the table element when clicking on the table headers
      reasoncode1headers.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(reasoncode1, ".clsspid_reasoncode1", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function Reasoncode1CodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Reasoncode1codesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("Reasoncode1Table2");
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

  function Reasoncode1NameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Reasoncode1namesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("Reasoncode1Table2");
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

  $('#txtReasoncode1_popup').click(function(event){
    showSelectedCheck($("#REASONCODE1_REF").val(),"reason1");
         $("#Reasoncode1_popup").show();
      });

      $("#Reasoncode1_closePopup").click(function(event){
        $("#Reasoncode1_popup").hide();
      });
      $(".clsspid_reasoncode1").click(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");
        var texcode =   $("#txt"+fieldid+"").data("code");

        $('#txtReasoncode1_popup').val(texcode);
        $('#BREAKDOWN1_REASON_DESC').val(texdesc);
        $('#REASONCODE1_REF').val(txtval);
        
        $("#Reasoncode1_popup").hide();        
        $("#Reasoncode1codesearch").val(''); 
        $("#Reasoncode1namesearch").val('');    
        event.preventDefault();
      });

  //reason 1 Dropdown Ends here






    //Reason code 2 Dropdown starts here 
    let reasoncode2 = "#Reasoncode2Table2";
      let reasoncode22 = "#Reasoncode2Table";
      let reasoncode2headers = document.querySelectorAll(reasoncode12 + " th");

      // Sort the table element when clicking on the table headers
      reasoncode2headers.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(reasoncode1, ".clsspid_reasoncode2", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function Reasoncode2CodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Reasoncode2codesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("Reasoncode2Table2");
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

  function Reasoncode2NameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Reasoncode2namesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("Reasoncode2Table2");
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

  $('#txtReasoncode2_popup').click(function(event){
    showSelectedCheck($("#REASONCODE2_REF").val(),"reason2");
         $("#Reasoncode2_popup").show();
      });

      $("#Reasoncode2_closePopup").click(function(event){
        $("#Reasoncode2_popup").hide();
      });
      $(".clsspid_reasoncode2").click(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");
        var texcode =   $("#txt"+fieldid+"").data("code");

        $('#txtReasoncode2_popup').val(texcode);
        $('#BREAKDOWN2_REASON_DESC').val(texdesc);
        $('#REASONCODE2_REF').val(txtval);
        
        $("#Reasoncode2_popup").hide();        
        $("#Reasoncode2codesearch").val(''); 
        $("#Reasoncode2namesearch").val('');    
        event.preventDefault();
      });

  //reason 2 Dropdown Ends here




      //Machine Dropdown starts here 


      let machine = "#MachineTable2";
      let machine2 = "#MachineTable";
      let machineheaders = document.querySelectorAll(machine2 + " th");

      // Sort the table element when clicking on the table headers
      machineheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(machine, ".clsspid_reasoncode1", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function MachineCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Machinecodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("MachineTable2");
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

  function MachineNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Machinenamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("MachineTable2");
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


  $("#item_closePopup1").click(function(event){
        $("#machine_popup").hide();
      });

  function bindMachineEvents(){




      $(".clsspid_machine").click(function(){

        

        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");
        var texcode =   $("#txt"+fieldid+"").data("code");
        //alert(texdesc); 

        $('#txtMachinepopup').val(texcode);
        $('#MACHINE_REF').val(txtval);
        
        $("#machine_popup").hide();   
        $("#Reasoncode1codesearch").val(''); 
        $("#Reasoncode1namesearch").val('');    
        
        event.preventDefault();
      });
  }

  //Machine Dropdown Ends here







  $('#txtMachinepopup').on('click',function(event){  
    var MACHINE_TYPE=$('[name="COMPLAINT_FOR"]:checked').val();
  
                $("#Machineresult").html('');


                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  });
                  $("#item_seach").show();
                  $.ajax({
                      url:'{{route("transaction",[364,"get_machine_details"])}}',
                      type:'POST',
                      data:{'MACHINE_TYPE':MACHINE_TYPE},
                      success:function(data) {                                
                        $("#item_seach").hide();
                        $("#Machineresult").html(data);   
                        showSelectedCheck($("#MACHINE_REF").val(),"machine");
                        bindMachineEvents();  
                                       
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                        $("#Machineresult").html('');                        
                      },
                  }); 

                  showSelectedCheck($("#REASONCODE1_REF").val(),"machine");
                  $("#machine_popup").show();                    
         
    });

  














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



  var dt = new Date();
  var time = moment(dt).format("HH:mm");
$("#TIME").val(time); 




  var lastdt = <?php echo json_encode($objlastdt[0]->BDCL_DATE); ?>;
  var today = new Date(); 
  var sodate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;

  $('#BDCL_DATE').attr('min',lastdt);
  $('#BDCL_DATE').attr('max',sodate);
  

  var d = new Date(); 
  var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
  $('#BDCL_DATE').val(today);

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






  $('#chk_Machine').on('change',function()
{


  if($(this).is(':checked') == true)
  {
    $('#chk_Generator').prop('checked', false);
    $("#txtMachinepopup").val('');
    $("#MACHINE_REF").val('');

  }
  else
  {
    $(this).prop('checked', false);

  
  }
});

  $('#chk_Generator').on('change',function()
{

  if($(this).is(':checked') == true)
  {

    $('#chk_Machine').prop('checked', false);
    $("#txtMachinepopup").val('');
    $("#MACHINE_REF").val('');
  }
  else
  {
    $(this).prop('checked', false);

  
  }
});
    

</script>
@endpush
