
@extends('layouts.app')
@section('content')
<!-- <form id="frm_trn_se" onsubmit="return validateForm()"  method="POST" class="needs-validation"  >     -->

    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('transaction',[35,'index'])}}" class="btn singlebt">Sales Enquiry</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
                        <button class="btn topnavbt" id="btnSaveSE" disabled="disabled"><i class="fa fa-floppy-o" ></i> Save</button>
                        <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" id="btnPrint" ><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt" id="btnUndo"  disabled="disabled"><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-thumbs-o-up"></i> Approved</button>
                        <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                        <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                </div>
            </div><!--row-->
    </div>

    <form id="frm_trn_se"  method="POST">   
    @csrf
    {{isset($objSE->SEQID[0]) ? method_field('PUT') : '' }}
    <div class="container-fluid filter">
	<div class="inner-form">
		<div class="row">
			<div class="col-lg-2 pl"><p>Sales Enquiry No *</p></div>
			<div class="col-lg-2 pl">
                <input {{$ActionStatus}} type="text" name="ENQNO" id="ENQNO" value="{{ isset($objSE->ENQNO)?$objSE->ENQNO:''}}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase"  >
           </div>
			
			<div class="col-lg-2 pl"><p>Sales Enquiry Date *</p></div>
			<div class="col-lg-2 pl">
				    <input {{$ActionStatus}} type="date" name="ENQDT" id="ENQDT" value="{{isset($objSE->ENQDT)?$objSE->ENQDT:'' }}" class="form-control mandatory"  placeholder="dd/mm/yyyy" >
      </div>
      <div class="col-lg-2 pl"><p>Customer *</p></div>
			<div class="col-lg-2 pl">
                <input {{$ActionStatus}} type="text" name="SubGl_popup" id="txtgl_popup" class="form-control mandatory"  autocomplete="off" value="{{isset($objsubglcode->SGLCODE)?$objsubglcode->SGLCODE:''}} {{isset($objsubglcode->SLNAME)?'-'.$objsubglcode->SLNAME:''}}" readonly/>
                <input type="hidden" name="GLID_REF" id="GLID_REF" value="{{ isset($objSE->GLID_REF)?$objSE->GLID_REF:'' }}" class="form-control" autocomplete="off" />
                <input type="hidden" name="SLID_REF"  id="SLID_REF" value="{{ isset($objSE->SLID_REF)?$objSE->SLID_REF:'' }}" class="form-control" autocomplete="off" />
                <input type="hidden" name="hdnmaterial" id="hdnmaterial" class="form-control" autocomplete="off" />
			</div>
		</div>
		<div class="row">
			<div class="col-lg-2 pl"><p>Enquiry media *</p></div>
			<div class="col-lg-2 pl">
                <input {{$ActionStatus}} type="text" name="EMID_popup" id="EMID_popup" class="form-control mandatory"  autocomplete="off" value="{{isset($objenquirymedia2->EMCODE)?$objenquirymedia2->EMCODE:''}} {{isset($objenquirymedia2->DESCRIPTIONS)?'-'.$objenquirymedia2->DESCRIPTIONS:''}}" readonly/>
                <input type="hidden" name="EMID_REF" id="EMID_REF" class="form-control" autocomplete="off" value="{{ isset($objSE->EMID_REF)?$objSE->EMID_REF:'' }}" />
			</div>
			
			<div class="col-lg-2 pl"><p>Enquired By *</p></div>
			<div class="col-lg-2 pl">
				<input {{$ActionStatus}} type="text" name="ENQBY" id="ENQBY" class="form-control" maxlength="100" autocomplete="off" value="{{ isset($objSE->ENQBY)?$objSE->ENQBY:'' }}" >
			</div>
			
			<div class="col-lg-2 pl"><p>Priority *</p></div>
			<div class="col-lg-2 pl">
                <input {{$ActionStatus}} type="text" name="PRIORITYID_popup" id="PRIORITYID_popup" class="form-control mandatory"  autocomplete="off" value="{{isset($objPriority2->PRIORITYCODE)?$objPriority2->PRIORITYCODE:''}} {{isset($objPriority2->DESCRIPTIONS)?'-'.$objPriority2->DESCRIPTIONS:''}}" readonly/>
                <input type="hidden" name="PRIORITYID_REF" id="PRIORITYID_REF" class="form-control" autocomplete="off"  value="{{ isset($objSE->PRIORITYID_REF)?$objSE->PRIORITYID_REF:'' }}" />
			</div>
		</div>		
		<div class="row">
			<div class="col-lg-2 pl"><p>Sales Person *</p></div>
			<div class="col-lg-2 pl">
                <input {{$ActionStatus}} type="text" name="SPID_popup" id="txtSPID_popup" class="form-control mandatory"  autocomplete="off" value="{{isset($objSPID[0])?$objSPID[0]:''}}" readonly/>
                <input type="hidden" name="SPID_REF" id="SPID_REF" class="form-control" autocomplete="off"  value="{{ isset($objSE->SPID_REF)?$objSE->SPID_REF:'' }}"/>
			</div>
			<div class="col-lg-2 pl"><p>Prospect Ref No</p></div>
			<div class="col-lg-2 pl">
				<input {{$ActionStatus}} type="text" name="PROSPECTREFNO" id="PROSPECTREFNO" class="form-control" autocomplete="off" maxlength="20"  value="{{ isset($objSE->PROSPECTREFNO)?$objSE->PROSPECTREFNO:'' }}"/>
			</div>			
			<div class="col-lg-2 pl"><p>Convertion Probability</p></div>
			<div class="col-lg-2 pl">
				<input {{$ActionStatus}} type="text" name="CONV_PROLTY" id="CONV_PROLTY" class="form-control four-digits" maxlength="8"   value="{{ isset($objSE->CONV_PROLTY)?$objSE->CONV_PROLTY:'' }}" />
			</div>
    </div>		
		<div class="row">			
			<div class="col-lg-2 pl"><p>Approx Value</p></div>
			<div class="col-lg-2 pl">
				<input {{$ActionStatus}} type="text" name="APPROXV" id="APPROXV" class="form-control two-digits" maxlength="15"    value="{{ isset($objSE->APPROXV)?$objSE->APPROXV:'' }}" />
			</div>
			
			<div class="col-lg-2 pl"><p>Remarks</p></div>
			<div class="col-lg-2 pl">
				<input {{$ActionStatus}} type="text" name="REMARKS" id="REMARKS" class="form-control" maxlength="200"    value="{{ isset($objSE->REMARKS)?$objSE->REMARKS:'' }}" />
			</div>
			
		</div>
		
	</div>

	<div class="container-fluid">

		<div class="row">
			<ul class="nav nav-tabs">
				<li class="active"><a data-toggle="tab" href="#Material">Material</a></li>
				<li><a data-toggle="tab" href="#udf">UDF</a></li>
			</ul>
			
			
			
			<div class="tab-content">

				<div id="Material" class="tab-pane fade in active">
          <div class="row"><div class="col-lg-4" style="padding-left: 15px;">Note:- 1 row mandatory in Material Tab </div></div>
					<div class="table-responsive table-wrapper-scroll-y" style="height:500px;margin-top:10px;" >
						<table id="example2" class="display nowrap table table-striped table-bordered itemlist w-200" width="100%" style="height:auto !important;">
							<thead id="thead1"  style="position: sticky;top: 0">
								  <tr>
									<th>Item Code<input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1"></th>
									<th>Item Name</th>
                  <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</th>
                  <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</th>
                  <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</th>
									<th>Main UOM</th>
									<th>Qty (Main UOM)</th>
									<th>ALT UOM</th>
									<th>Qty (Alt UOM)</th>
									<th>EDD</th>
									<th>Target Price (If any)</th>
									<th>Type of Packaging</th>
									<th>UOM</th>
									<th>Pack Qty</th>
									<th>Item Specification</th>
									<th>Action</th>
								  </tr>
							</thead>
							<tbody>
              @if(isset($objSEMAT) && !empty($objSEMAT))
                @foreach($objSEMAT as $key => $row)
								  <tr  class="participantRow">
                    <td><input {{$ActionStatus}} style="width:100px;" type="text" name={{"popupITEMID_".$key}} id={{"popupITEMID_".$key}} class="form-control" value="{{ isset($row->ICODE)?$row->ICODE:'' }}" autocomplete="off"  readonly/></td>
                    <td hidden><input type="hidden" name={{"ITEMID_REF_".$key}} id={{"ITEMID_REF_".$key}} class="form-control"  value="{{ isset($row->ITEMID_REF)?$row->ITEMID_REF:'' }}" autocomplete="off" /></td>
                    <td><input {{$ActionStatus}} type="text" name={{"ItemName_".$key}} id={{"ItemName_".$key}} class="form-control"  autocomplete="off" value="{{ isset($row->itemname)?$row->itemname:'' }}"  readonly/></td>
                    
                    <td {{$AlpsStatus['hidden']}}><input {{$ActionStatus}} type="text" name="Alpspartno_{{$key}}" id="Alpspartno_{{$key}}" class="form-control"  autocomplete="off" value="{{ isset($row->ALPS_PART_NO)?$row->ALPS_PART_NO:'' }}" readonly/></td>
                    <td {{$AlpsStatus['hidden']}}><input {{$ActionStatus}} type="text" name="Custpartno_{{$key}}" id="Custpartno_{{$key}}" class="form-control"  autocomplete="off" value="{{ isset($row->CUSTOMER_PART_NO)?$row->CUSTOMER_PART_NO:'' }}" readonly/></td>
                    <td {{$AlpsStatus['hidden']}}><input {{$ActionStatus}} type="text" name="OEMpartno_{{$key}}"  id="OEMpartno_{{$key}}" class="form-control"  autocomplete="off" value="{{ isset($row->OEM_PART_NO)?$row->OEM_PART_NO:'' }}" readonly/></td>

                    
                    <td><input {{$ActionStatus}} type="text" name={{"popupMUOM_".$key}} id={{"popupMUOM_".$key}} class="form-control"  autocomplete="off" value="{{ isset($row->popupMUOM)?$row->popupMUOM:'' }}" readonly/></td>
                    <td hidden><input type="hidden" name={{"MAIN_UOMID_REF_".$key}} id={{"MAIN_UOMID_REF_".$key}} class="form-control" value="{{ isset($row->MAIN_UOMID_REF)?$row->MAIN_UOMID_REF:'' }}"  autocomplete="off" /></td>
                    <td><input {{$ActionStatus}} type="text" name={{"SE_QTY_".$key}} id={{"SE_QTY_".$key}} class="form-control three-digits" maxlength="13" value="{{ isset($row->MAIN_QTY)?$row->MAIN_QTY:'' }}" autocomplete="off"  /></td>
                    <td hidden><input type="hidden" name={{"SO_FQTY_".$key}} id={{"SO_FQTY_".$key}} class="form-control three-digits" maxlength="13" value="1" autocomplete="off"  readonly/></td>
                    <td><input {{$ActionStatus}} type="text" name={{"popupAUOM_".$key}} id={{"popupAUOM_".$key}} class="form-control"  autocomplete="off" value="{{ isset($row->popupAUOM)?$row->popupAUOM:'' }}"  readonly/></td>
                    <td hidden><input type="hidden" name={{"ALT_UOMID_REF_".$key}} id={{"ALT_UOMID_REF_".$key}} class="form-control"  autocomplete="off"  value="{{ isset($row->ALTUOMID_REF)?$row->ALTUOMID_REF:'' }}"  readonly/></td>
                    <td><input {{$ActionStatus}} type="text" name={{"ALT_UOMID_QTY_".$key}} id={{"ALT_UOMID_QTY_".$key}} class="form-control three-digits" maxlength="13" value="{{ isset($row->ALT_UOMID_QTY)?$row->ALT_UOMID_QTY:'' }}" autocomplete="off"  readonly/></td>
                    <td><input {{$ActionStatus}}  class="form-control w-100" type="date" name={{"EDD_".$key}} id={{"EDD_".$key}} placeholder="dd/mm/yyyy" value="{{ isset($row->EDD)?$row->EDD:'' }}" ></td>
                    <td><input {{$ActionStatus}}  name={{"TARGETPRICE_".$key}} id={{"TARGETPRICE_".$key}} type="text" class="form-control two-digits" maxlength="15" value="{{ isset($row->TARGETPRICE)?$row->TARGETPRICE:'' }}"  autocomplete="off"  /></td>
                    <td><input {{$ActionStatus}} type="text" name={{"PACKSIZE_".$key}} id={{"PACKSIZE_".$key}} class="form-control"  autocomplete="off" value="{{ isset($row->PACKSIZE)?$row->PACKSIZE:'' }}"  readonly/></td>
                    <td hidden><input type="hidden" name={{"PTID_REF_".$key}} id={{"PTID_REF_".$key}} class="form-control"  autocomplete="off" value="{{ isset($row->PTID_REF)?$row->PTID_REF:'' }}" readonly/></td>
                    <td><input {{$ActionStatus}} type="text" name={{"PACKUOM_".$key}} id={{"PACKUOM_".$key}} class="form-control"  autocomplete="off" value="{{ isset($row->PACKUOM)?$row->PACKUOM:'' }}"   readonly /></td>
                    <td hidden><input type="text" name={{"PACKUOMID_REF_".$key}} id={{"PACKUOMID_REF_".$key}} class="form-control" value="{{ isset($row->PACKUOMID_REF)?$row->PACKUOMID_REF:'' }}"   autocomplete="off"  /></td>
                    <td><input {{$ActionStatus}} type="text" name={{"PACK_QTY_".$key}} id={{"PACK_QTY_".$key}} class="form-control three-digits"  value="{{ isset($row->PACK_QTY)?$row->PACK_QTY:'' }}"  autocomplete="off" readonly /></td>
                    <td><input {{$ActionStatus}} type="text" name={{"Itemspec_".$key}} id={{"Itemspec_".$key}} class="form-control"  autocomplete="off" value="{{ isset($row->ITEM_SPECI)?$row->ITEM_SPECI:'' }}"   /></td>
                    <td align="center" >
                      <button {{$ActionStatus}} class="btn add material" title="add" data-toggle="tooltip" type="button" ><i class="fa fa-plus"></i></button>
                      <button {{$ActionStatus}} class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button" ><i class="fa fa-trash" ></i></button>
                    </td>
								  </tr>
								<tr></tr>
                @endforeach 
              @endif
							</tbody>
					  </table>
					</div>	
				</div>

				<div id="udf" class="tab-pane fade">
					<div class="table-responsive table-wrapper-scroll-y " style="margin-top:10px;height:500px;width:50%;">
						<table id="example3" class="display nowrap table table-striped table-bordered itemlist" style="height:auto !important;">
							<thead id="thead1"  style="position: sticky;top: 0">
							  <tr >
								<th>UDF Fields<input class="form-control" type="hidden" name="Row_Count2" id ="Row_Count2"></th>
								<th>Value / Comments</th>
								<th>Action</th>
							  </tr>
							</thead>
							<tbody>
              @if(isset($objSEUDF) && !empty($objSEUDF))
              @foreach($objSEUDF as $uindex=>$uRow)
                <tr  class="participantRow3">
                    <td><input {{$ActionStatus}} type="text" name={{"popupSEID_".$uindex}} id={{"popupSEID_".$uindex}} class="form-control" value="{{isset($uRow->SEID_REF)?$uRow->SEID_REF:''}}" autocomplete="off"  readonly/></td>
                    <td hidden><input type="hidden" name={{"SEID_REF_".$uindex}} id={{"SEID_REF_".$uindex}} class="form-control" value="{{isset($uRow->SEID_REF)?$uRow->SEID_REF:''}}" autocomplete="off"   /></td>
                    <td hidden><input type="hidden" name={{"UDFismandatory_".$uindex}} id={{"UDFismandatory_".$uindex}} value="{{isset($uRow->SEID_REF)?$uRow->SEID_REF:''}}" class="form-control"   autocomplete="off" /></td>
                    <td id={{"udfinputid_".$uindex}} >
                      
                    </td>
                    <td align="center" ><button class="btn add UDF" title="add" data-toggle="tooltip" type="button" disabled><i class="fa fa-plus"></i></button><button class="btn remove DUDF" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
                    
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

<!-- </div> -->
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
            <button onclick="getFocus()" class="btn alertbt" name='OkBtn1' id="OkBtn1" style="display:none;margin-left: 90px;"><div id="alert-active" class="activeOk1"></div>OK</button>
            <input type="hidden" id="FocusId" >
        </div>
		    <div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<!-- Alert -->

<!-- Print -->
<div id="ReportView" class="modal" role="dialog"  data-backdrop="static"  >
  <div class="modal-dialog modal-md" style="width:90%; height:90%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='ReportViewclosePopup' >&times;</button>          
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Sales Enquiry Print</p></div>
        <div class="row">
          <div class="frame-container col-lg-12 pl text-center" >
                <button class="btn topnavbt" id="btnReport">
                    Print
                </button>
                <button class="btn topnavbt" id="btnPdf">
                    PDF
                </button>
                <button class="btn topnavbt" id="btnExcel">
                    Excel
                </button>
          </div>
        </div>
        
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
          <div class="inner-form">
              <div class="row">
                  <div class="frame-container col-lg-12 pl " >                      
                      <iframe id="iframe_rpt" width="100%" height="1000" >
                      </iframe>
                  </div>
              </div>
          </div>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Print-->


<!-- Alert -->
<!-- Customer  Dropdown -->
<div id="customer_popus" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='customer_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Customer</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="GlCodeTable" class="display nowrap table  table-striped table-bordered">
    <thead>
    <tr>
      <th class="ROW1">Select</th> 
      <th class="ROW2">Code</th>
      <th class="ROW3">Description</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="ROW1"><span class="check_th">&#10004;</span></td>
        <td class="ROW2"><input type="text" id="customercodesearch" class="form-control" autocomplete="off" onkeyup="CustomerCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="customernamesearch" class="form-control" autocomplete="off" onkeyup="CustomerNameFunction()"></td>
    </tr>
    </tbody>
    </table>
      <table id="GlCodeTable2" class="display nowrap table  table-striped table-bordered">
        <thead id="thead2">
        </thead>
        <tbody id="tbody_subglacct">
        
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- CUSTOMER Dropdown-->

<!-- Sales Person Dropdown -->
<div id="SPIDpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:60%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='SPID_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Sales Person</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="SalesPersonTable" class="display nowrap table  table-striped table-bordered" style="width:100%;">
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
    <input type="text" id="SalesPersoncodesearch" class="form-control" onkeyup="SalesPersonCodeFunction()">
    </td>
    <td style="width:60%;">
    <input type="text" id="SalesPersonnamesearch" class="form-control" onkeyup="SalesPersonNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="SalesPersonTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          
        </thead>
        <tbody >     
        @foreach ($objSalesPerson as $spindex=>$spRow)
        <tr >
        <td style="text-align:center; width:10%"> <input type="checkbox" name="salesperson[]" id="spidcode_{{ $spindex }}" class="clsspid"  value="{{ $spRow-> EMPID }}" ></td>



          <td style="width:30%">{{ $spRow-> EMPCODE }}
          <input type="hidden" id="txtspidcode_{{ $spindex }}" data-desc="{{ $spRow-> EMPCODE }} - {{ $spRow-> FNAME }} {{ $spRow-> MNAME }} {{ $spRow-> LNAME }} "  value="{{ $spRow-> EMPID }}"/>
          </td>
          <td style="width:60%">{{ $spRow-> FNAME }} {{ $spRow-> MNAME }} {{ $spRow-> LNAME }}</td>
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
<!-- Sales Person Dropdown-->
<!-- Enquiry Media Dropdown -->
<div id="emidpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:60%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='em_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Enquiry Media</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="EMCodeTable" class="display nowrap table  table-striped table-bordered" style="width:100%;">
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
    <input type="text" id="emcodesearch" class="form-control" autocomplete="off" onkeyup="EMCodeFunction()">
    </td>
    <td style="width:60%;">
    <input type="text" id="emnamesearch" class="form-control" autocomplete="off" onkeyup="EMNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="EMCodeTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          <!-- <tr>
            <th>GLCode</th>
            <th>GLName</th>
          </tr> -->
          
        </thead>
        <tbody>
        @foreach ($objenquirymedia as $emindex=>$emRow)
        <tr >
        <td style="text-align:center; width:10%"> <input type="checkbox" name="enquirymedia[]" id="emidcode_{{ $emindex }}" class="clsemid" value="{{ $emRow-> EMID }}" ></td>


 
          <td style="width:30%">{{ $emRow-> EMCODE }}
          <input type="hidden" id="txtemidcode_{{ $emindex }}" data-desc="{{ $emRow-> EMCODE }}-{{ $emRow-> DESCRIPTIONS }}"  value="{{ $emRow-> EMID }}"/>
          </td>
          <td style="width:60%">{{ $emRow-> DESCRIPTIONS }}</td>
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
<!-- Enquiry Media Dropdown-->
<!-- Priority Dropdown -->
<div id="Prioritypopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:60%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='Priority_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Priority</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="PriorityTable" class="display nowrap table  table-striped table-bordered" style="width:100%;">
    <thead>
    <tr>
            <th style="width:10%;"></th>
            <th style="width:30%;">Code</th>
            <th style="width:60%;">Description</th>
    </tr>
    </thead>
    <tbody>
    <tr>
    <th style="text-align:center; width:10%;">&#10004;</th>
   <td style="width:30%;"> 
    <input type="text" id="Prioritycodesearch" class="form-control" autocomplete="off" onkeyup="PriorityCodeFunction()">
    </td>
    <td>
    <input type="text" id="Prioritynamesearch" class="form-control" autocomplete="off" onkeyup="PriorityNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="PriorityTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          
        </thead>
        <tbody>
        @foreach ($objPriority as $prindex=>$prRow)
        <tr>


        <td style="text-align:center; width:10%"> <input type="checkbox" name="prioritycheck[]" id="pridcode_{{ $prindex }}" class="clsprid"  value="{{ $prRow-> PRIORITYID }}" ></td>

 
          <td style="width:30%">{{ $prRow-> PRIORITYCODE }}
          <input type="hidden" id="txtpridcode_{{ $prindex }}" data-desc="{{ $prRow-> PRIORITYCODE }}-{{ $prRow-> DESCRIPTIONS }}"  value="{{ $prRow-> PRIORITYID }}"/>
          </td>
          <td style="width:60%">{{ $prRow-> DESCRIPTIONS }}</td>
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
<!-- Priority Dropdown-->

<!-- Item Code Dropdown -->
<div id="ITEMIDpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:90%;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='ITEMID_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Item Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="ItemIDTable" class="display nowrap table  table-striped table-bordered" style="width:100%;" >
    <thead>
      <tr id="none-select" class="searchalldata" hidden>
            
            <td> <input type="hidden" name="fieldid" id="hdn_ItemID"/>
            <input type="hidden" name="fieldid2" id="hdn_ItemID2"/>
            <input type="hidden" name="fieldid3" id="hdn_ItemID3"/>
            <input type="hidden" name="fieldid4" id="hdn_ItemID4"/>
            <input type="hidden" name="fieldid5" id="hdn_ItemID5"/>
            <input type="hidden" name="fieldid6" id="hdn_ItemID6"/>
            <input type="hidden" name="fieldid7" id="hdn_ItemID7"/>
            <input type="hidden" name="fieldid8" id="hdn_ItemID8"/>
            <input type="hidden" name="fieldid9" id="hdn_ItemID9"/>
            <input type="hidden" name="fieldid10" id="hdn_ItemID10"/>
            <input type="hidden" name="fieldid11" id="hdn_ItemID11"/>
            <input type="hidden" name="fieldid12" id="hdn_ItemID12"/>
            <input type="hidden" name="fieldid13" id="hdn_ItemID13"/>
            <input type="hidden" name="fieldid14" id="hdn_ItemID14"/>
            <input type="hidden" name="fieldid15" id="hdn_ItemID15"/>
            <input type="hidden" name="fieldid16" id="hdn_ItemID16"/>
            <input type="hidden" name="fieldid17" id="hdn_ItemID17"/>
            </td>
      </tr>
      <!-- <tr class="topnav"><button class="btn topnavbt" id="btnOk"><i class="fa fa-check"></i> OK</button></tr> -->
      <tr>
            <th style="width:8%;text-align:center;" id="all-check">Select</th>
            <th style="width:10%;">Item Code</th>
            <th style="width:10%;">Name</th>
            <th style="width:8%;">Main UOM</th>
            <th style="width:8%;">Main QTY</th>
            <th style="width:8%;">Item Group</th>
            <th style="width:8%;">Item Category</th>
            <th style="width:8%;">Business Unit</th>
            <th style="width:8%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</th>
            <th style="width:8%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</th>
            <th style="width:8%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</th>
            <th style="width:8%;">Status</th>
      </tr>
    </thead>
    <tbody>
    <tr>
        <td style="width:8%;text-align:center;"></td>
        <td style="width:10%;"><input type="text" id="Itemcodesearch" class="form-control" autocomplete="off" onkeyup="ItemCodeFunction()"></td>
        <td style="width:10%;"><input type="text" id="Itemnamesearch" class="form-control" autocomplete="off" onkeyup="ItemNameFunction()"></td>
        <td style="width:8%;"><input type="text" id="ItemUOMsearch" class="form-control" autocomplete="off" onkeyup="ItemUOMFunction()"></td>
        <td style="width:8%;"><input type="text" id="ItemQTYsearch" class="form-control" autocomplete="off" onkeyup="ItemQTYFunction()"></td>
        <td style="width:8%;"><input type="text" id="ItemGroupsearch" class="form-control" autocomplete="off" onkeyup="ItemGroupFunction()"></td>
        <td style="width:8%;"><input type="text" id="ItemCategorysearch" class="form-control" autocomplete="off" onkeyup="ItemCategoryFunction()"></td>
        <td style="width:8%;"><input type="text" id="ItemBUsearch" class="form-control" autocomplete="off" onkeyup="ItemBUFunction()"></td>
        <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemAPNsearch" autocomplete="off" class="form-control" onkeyup="ItemAPNFunction()"></td>
        <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemCPNsearch" autocomplete="off" class="form-control" onkeyup="ItemCPNFunction()"></td>
        <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemOEMPNsearch" autocomplete="off" class="form-control" onkeyup="ItemOEMPNFunction()"></td>
        <td style="width:8%;"><input type="text" id="ItemStatussearch" class="form-control" autocomplete="off" onkeyup="ItemStatusFunction()"></td>
      </tr>
    </tbody>
    </table>
      <table id="ItemIDTable2" class="display nowrap table  table-striped table-bordered" style="width:100%;" >
        <thead id="thead2">

        </thead>
        <tbody id="tbody_ItemID">     
          
          
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Item Code Dropdown-->

<!-- Packaging Type Dropdown -->
<div id="Packagingpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:60%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='PackagingclosePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Type of Packaging</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="PackagingTable" class="display nowrap table  table-striped table-bordered" >
    <thead>
      <tr id="none-select" class="searchalldata" hidden>
            
            <td> <input type="hidden" name="fieldid" id="hdn_Packaging"/>
            <input type="hidden" name="fieldid2" id="hdn_Packaging2"/>
            </td>
      </tr>
     
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
    <input type="text" id="Packagingcodesearch" class="form-control"  autocomplete="off" onkeyup="PackagingCodeFunction()">
    </td>
    <td style="width:60%;">
    <input type="text" id="Packagingnamesearch" class="form-control"  autocomplete="off" onkeyup="PackagingNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="PackagingTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2">
        </thead>
        <tbody>
        @foreach ($objPackingType as $ptindex=>$ptRow)
        <tr >
        <td style="text-align:center; width:10%"> <input type="checkbox" name="packingtype[]" id="ptidcode_{{ $ptindex }}" class="clsptid" value="{{ $ptRow-> PTID }}" ></td>



          <td style="width:30%">{{ $ptRow-> PTCODE }}
          <input type="hidden" id="txtptidcode_{{ $ptindex }}" data-desc="{{ $ptRow-> PTCODE }} - {{$ptRow-> PTNAME}}"  value="{{ $ptRow-> PTID }}"/>
          </td>
          <td style="width:60%">{{ $ptRow-> PTNAME }}</td>
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
<!--Packaging Type Dropdown-->

<!-- ALT UOM Dropdown -->
<div id="altuompopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='altuom_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>ALT UOM</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="altuomTable" class="display nowrap table  table-striped table-bordered" >
    <thead>
    <tr id="none-select" class="searchalldata" hidden>
            
            <td> <input type="hidden" name="fieldid" id="hdn_altuom"/>
            <input type="hidden" name="fieldid2" id="hdn_altuom2"/>
            <input type="hidden" name="fieldid3" id="hdn_altuom3"/>
            <input type="hidden" name="fieldid4" id="hdn_altuom4"/></td>
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
        <td class="ROW2"><input type="text" id="altuomcodesearch" class="form-control" autocomplete="off" onkeyup="altuomCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="altuomnamesearch" class="form-control" autocomplete="off" onkeyup="altuomNameFunction()"></td>
      </tr>

    </tbody>
    </table>
      <table id="altuomTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          
        </thead>
        <tbody id="tbody_altuom">       
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- ALT UOM Dropdown-->

<!-- UOM Type Dropdown -->
<div id="UOMpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:60%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='UOMclosePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>UOM</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="UOMTable" class="display nowrap table  table-striped table-bordered" style="width:100%;" >
    <thead>
      <tr id="none-select" class="searchalldata" hidden>
            
            <td> <input type="hidden" name="fieldid" id="hdn_UOM"/>
            <input type="hidden" name="fieldid2" id="hdn_UOM2"/>
            </td>
      </tr>
      <!-- <tr class="topnav"><button class="btn topnavbt" id="btnOk"><i class="fa fa-check"></i> OK</button></tr> -->
      <tr>
            <th style="width:10%;"></th>
            <th style="width:30%;">Code</th>
            <th style="width:60%;">Description</th>
      </tr>
    </thead>
    <tbody>
    <tr>
    <th style="text-align:center; width:10%;">&#10004;</th>
   <td style="width:30%;"> 
    
    <input type="text" id="UOMcodesearch" class="form-control" onkeyup="UOMCodeFunction()">
    </td>
    <td>
    <input type="text" id="UOMnamesearch" class="form-control" onkeyup="UOMNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="UOMTable2" class="display nowrap table  table-striped table-bordered" width="100%" >
        <thead id="thead2">
        </thead>
        <tbody>
        @foreach ($objUOM as $uomindex=>$uomRow)
        <tr >

        <td style="text-align:center; width:10%"> <input type="checkbox" name="uomcheck[]" id="uomidcode_{{ $uomindex }}" class="clsuomid" value="{{ $uomRow-> UOMID }}" ></td>


        
          <td style="width:30%">{{ $uomRow-> UOMCODE }}
          <input type="hidden" id="txtuomidcode_{{ $uomindex }}" data-desc="{{ $uomRow-> UOMCODE }} - {{$uomRow-> DESCRIPTIONS}}"  value="{{ $uomRow-> UOMID }}"/>
          </td>
          <td wistyle="width:60%">{{ $uomRow-> DESCRIPTIONS }}</td>
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
<!--UOM Type Dropdown-->
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

</style>
@endpush
@push('bottom-scripts')
<script>

//------------------------
  //CUSTOMER LIST POPUP
  let cltid = "#GlCodeTable2";
      let cltid2 = "#GlCodeTable";
      let clheaders = document.querySelectorAll(cltid2 + " th");

      // Sort the table element when clicking on the table headers
      clheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(cltid, ".clsglid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function CustomerCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("customercodesearch");
        filter = input.value.toUpperCase();
        
      if(filter.length == 0)
        {
          var CODE = ''; 
          var NAME = ''; 
          loadCustomer(CODE,NAME); 
        }
        else if(filter.length >= 3)
        {
          var CODE = filter; 
          var NAME = ''; 
          loadCustomer(CODE,NAME); 
        }
        else
        {
          table = document.getElementById("GlCodeTable2");
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
    }

  function CustomerNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("customernamesearch");
        filter = input.value.toUpperCase();
        if(filter.length == 0)
        {
          var CODE = ''; 
          var NAME = ''; 
          loadCustomer(CODE,NAME);
        }
        else if(filter.length >= 3)
        {
          var CODE = ''; 
          var NAME = filter; 
          loadCustomer(CODE,NAME);  
        }
        else
        {
          table = document.getElementById("GlCodeTable2");
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
    }
    
    function loadCustomer(CODE,NAME){
    
        $("#tbody_subglacct").html('');
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
        $.ajax({
          url:'{{route("transaction",[$FormId,"getsubledger"])}}',
          type:'POST',
          data:{'CODE':CODE,'NAME':NAME},
          success:function(data) {
          $("#tbody_subglacct").html(data); 
          bindSubLedgerEvents(); 

          },
          error:function(data){
          console.log("Error: Something went wrong.");
          $("#tbody_subglacct").html('');                        
          },
        });
    }


  $('#txtgl_popup').click(function(event){
    var CODE = ''; 
    var NAME = ''; 
   
    loadCustomer(CODE,NAME);
    $("#customer_popus").show();
    event.preventDefault();
  });

      $("#customer_closePopup").click(function(event){
        $("#customer_popus").hide();
        $("#customercodesearch").val(''); 
        $("#customernamesearch").val(''); 
        event.preventDefault();
      });

    function bindSubLedgerEvents(){ 
      $(".clssubgl").click(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");
        var glid_ref =   $("#txt"+fieldid+"").data("desc2");
        
        $('#txtgl_popup').val(texdesc);
        $('#SLID_REF').val(txtval);
        $('#GLID_REF').val(glid_ref);
       
        $("#customer_popus").hide();
        $("#customercodesearch").val(''); 
        $("#customernamesearch").val(''); 
       
        //sub GL
        var customid = txtval;
        
        ////sub GL end
        event.preventDefault();
      });
    }

  //customer list  Ends
//------------------------


//------------------------

//------------------------
  //Sales Person Dropdown
  let sptid = "#SalesPersonTable2";
      let sptid2 = "#SalesPersonTable";
      let salespersonheaders = document.querySelectorAll(sptid2 + " th");

      // Sort the table element when clicking on the table headers
      salespersonheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(sptid, ".clsspid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function SalesPersonCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("SalesPersoncodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("SalesPersonTable2");
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

  function SalesPersonNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("SalesPersonnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("SalesPersonTable2");
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

  $('#txtSPID_popup').click(function(event){
    showSelectedCheck($("#SPID_REF").val(),"salesperson");
         $("#SPIDpopup").show();
      });

      $("#SPID_closePopup").click(function(event){
        $("#SPIDpopup").hide();
      });

      $(".clsspid").click(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");
        
        $('#txtSPID_popup').val(texdesc);
        $('#SPID_REF').val(txtval);
        $("#SPIDpopup").hide();
        
        $("#SalesPersoncodesearch").val(''); 
        $("#SalesPersonnamesearch").val(''); 
      
        event.preventDefault();
      });

      

  //Sales Person Dropdown Ends
//------------------------

//------------------------
  //Enquiry Media Dropdown
  let emtid = "#EMCodeTable2";
      let emtid2 = "#EMCodeTable";
      let emheaders = document.querySelectorAll(emtid2 + " th");

      // Sort the table element when clicking on the table headers
      emheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(emtid, ".clsemid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function EMCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("emcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("EMCodeTable2");
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

  function EMNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("emnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("EMCodeTable2");
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

  $('#EMID_popup').click(function(event){
    showSelectedCheck($("#EMID_REF").val(),"enquirymedia");
         $("#emidpopup").show();
      });

      $("#em_closePopup").click(function(event){
        $("#emidpopup").hide();
      });

      $(".clsemid").click(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");
        
        $('#EMID_popup').val(texdesc);
        $('#EMID_REF').val(txtval);
        $("#emidpopup").hide();
        
        $("#emcodesearch").val(''); 
        $("#emnamesearch").val(''); 
      
        event.preventDefault();
      });

      

  //Enquiry Media Dropdown Ends
//------------------------

//------------------------
  //Priority Media Dropdown
  let prtid = "#PriorityTable2";
      let prtid2 = "#PriorityTable";
      let prheaders = document.querySelectorAll(prtid2 + " th");

      // Sort the table element when clicking on the table headers
      prheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(prtid, ".clsprid", "td:nth-child(" + (i + 1) + ")");
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

  $('#PRIORITYID_popup').click(function(event){
    showSelectedCheck($("#PRIORITYID_REF").val(),"prioritycheck");
         $("#Prioritypopup").show();
      });

      $("#Priority_closePopup").click(function(event){
        $("#Prioritypopup").hide();
      });

      $(".clsprid").click(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");
        
        $('#PRIORITYID_popup').val(texdesc);
        $('#PRIORITYID_REF').val(txtval);
        $("#Prioritypopup").hide();
        
        $("#Prioritycodesearch").val(''); 
        $("#Prioritynamesearch").val(''); 
      
        event.preventDefault();
      });      

  //Priority Dropdown Ends
//------------------------

//------------------------
//Item ID Dropdown
let itemtid = "#ItemIDTable2";
let itemtid2 = "#ItemIDTable";
let itemtidheaders = document.querySelectorAll(itemtid2 + " th");

itemtidheaders.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(itemtid, ".clsitemid", "td:nth-child(" + (i + 1) + ")");
  });
});

function ItemCodeFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("Itemcodesearch");
  filter = input.value.toUpperCase();

  if(filter.length == 0)
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else if(filter.length >= 3)
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = filter; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else
  {
    table = document.getElementById("ItemIDTable2");
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
}

function ItemNameFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("Itemnamesearch");
  filter = input.value.toUpperCase();

  if(filter.length == 0)
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else if(filter.length >= 3)
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = filter; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else
  {
    table = document.getElementById("ItemIDTable2");
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
}

function ItemUOMFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("ItemUOMsearch");
  filter = input.value.toUpperCase();  
  if(filter.length == 0)
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else if(filter.length >= 3)
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = filter; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else
  {
    table = document.getElementById("ItemIDTable2");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[3];
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
}
function ItemQTYFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("ItemQTYsearch");
  filter = input.value.toUpperCase();        
  table = document.getElementById("ItemIDTable2");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[4];
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

function ItemGroupFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("ItemGroupsearch");
  filter = input.value.toUpperCase();
  if(filter.length == 0)
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else if(filter.length >= 3)
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = filter; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else
  {
    table = document.getElementById("ItemIDTable2");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[5];
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
}

function ItemCategoryFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("ItemCategorysearch");
  filter = input.value.toUpperCase();
  if(filter.length == 0)
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else if(filter.length >= 3)
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = filter; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else
  {
    table = document.getElementById("ItemIDTable2");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[6];
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
}

function ItemBUFunction() {
var input, filter, table, tr, td, i, txtValue;
input = document.getElementById("ItemBUsearch");
filter = input.value.toUpperCase();
if(filter.length == 0)
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else if(filter.length >= 3)
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = filter; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else
  {
    table = document.getElementById("ItemIDTable2");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[7];
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
}

function ItemAPNFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("ItemAPNsearch");
  filter = input.value.toUpperCase();
  if(filter.length == 0)
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else if(filter.length >= 3)
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = filter; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else
  {
    table = document.getElementById("ItemIDTable2");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[8];
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
}

function ItemCPNFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("ItemCPNsearch");
  filter = input.value.toUpperCase();
  if(filter.length == 0)
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else if(filter.length >= 3)
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = filter; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART);
  }
  else
  {
    table = document.getElementById("ItemIDTable2");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[9];
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
}

function ItemOEMPNFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("ItemOEMPNsearch");
  filter = input.value.toUpperCase();
  if(filter.length == 0)
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART);
  }
  else if(filter.length >= 3)
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = filter; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART);
  }
  else
  {
    table = document.getElementById("ItemIDTable2");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[10];
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
}

function ItemStatusFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("ItemStatussearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("ItemIDTable2");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[7];
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

function loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART){
	
	

		$("#tbody_ItemID").html('');
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
		$.ajax({
			url:'{{route("transaction",[$FormId,"getItemDetails"])}}',
			type:'POST',
			data:{'taxstate':taxstate,'CODE':CODE,'NAME':NAME,'MUOM':MUOM,'GROUP':GROUP,'CTGRY':CTGRY,'BUNIT':BUNIT,'APART':APART,'CPART':CPART,'OPART':OPART},
			success:function(data) {
        $("#tbody_ItemID").html(data); 
        bindItemEvents(); 
			},
			error:function(data){
        console.log("Error: Something went wrong.");
        $("#tbody_ItemID").html('');                        
			},
		});

}
 

  $('#Material').on('click','[id*="popupITEMID"]',function(event){

    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
      
        var CODE = ''; 
        var NAME = ''; 
        var MUOM = ''; 
        var GROUP = ''; 
        var CTGRY = ''; 
        var BUNIT = ''; 
        var APART = ''; 
        var CPART = ''; 
        var OPART = ''; 
      
        loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 

        $("#ITEMIDpopup").show();
        var id = $(this).attr('id');
        var id2 = $(this).parent().parent().find('[id*="ITEMID_REF"]').attr('id');
        var id3 = $(this).parent().parent().find('[id*="ItemName"]').attr('id');
        var id4 = $(this).parent().parent().find('[id*="Itemspec"]').attr('id');
        var id5 = $(this).parent().parent().find('[id*="popupMUOM"]').attr('id');
        var id6 = $(this).parent().parent().find('[id*="MAIN_UOMID_REF"]').attr('id');
        var id7 = $(this).parent().parent().find('[id*="SE_QTY"]').attr('id');
        var id8 = $(this).parent().parent().find('[id*="popupAUOM"]').attr('id');
        var id9 = $(this).parent().parent().find('[id*="ALT_UOMID_REF"]').attr('id');
        var id10 = $(this).parent().parent().find('[id*="ALT_UOMID_QTY"]').attr('id');
        var id11 = $(this).parent().parent().find('[id*="SO_FQTY"]').attr('id');

        $('#hdn_ItemID').val(id);
        $('#hdn_ItemID2').val(id2);
        $('#hdn_ItemID3').val(id3);
        $('#hdn_ItemID4').val(id4);
        $('#hdn_ItemID5').val(id5);
        $('#hdn_ItemID6').val(id6);
        $('#hdn_ItemID7').val(id7);
        $('#hdn_ItemID8').val(id8);
        $('#hdn_ItemID9').val(id9);
        $('#hdn_ItemID10').val(id10);
        $('#hdn_ItemID11').val(id11);
        event.preventDefault();
      });

      $("#ITEMID_closePopup").click(function(event){
        $("#ITEMIDpopup").hide();
      });

    function bindItemEvents(){

      $('#ItemIDTable2').off(); 

      $('[id*="chkId"]').change(function(){
      // $(".clsitemid").dblclick(function(){
        var fieldid = $(this).parent().parent().attr('id');
        var txtval =   $("#txt"+fieldid+"").val();
        var texdesc =  $("#txt"+fieldid+"").data("desc");
        var fieldid2 = $(this).parent().parent().children('[id*="itemname"]').attr('id');
        var txtname =  $("#txt"+fieldid2+"").val();
        var txtspec =  $("#txt"+fieldid2+"").data("desc");
        var fieldid3 = $(this).parent().parent().children('[id*="itemuom"]').attr('id');
        var txtmuomid =  $("#txt"+fieldid3+"").val();
        var txtauom =  $("#txt"+fieldid3+"").data("desc");

        var apartno =  $("#txt"+fieldid3+"").data("desc2");
        var cpartno =  $("#txt"+fieldid3+"").data("desc3");
        var opartno =  $("#txt"+fieldid3+"").data("desc4");

        var txtmuom =  $(this).parent().parent().children('[id*="itemuom"]').text();
        var fieldid4 = $(this).parent().parent().children('[id*="uomqty"]').attr('id');
        var txtauomid =  $("#txt"+fieldid4+"").val();
        var txtauomqty =  $("#txt"+fieldid4+"").data("desc");
        var txtmuomqty =  $(this).parent().parent().children('[id*="uomqty"]').text();
        var fieldid5 = $(this).parent().parent().children('[id*="irate"]').attr('id');
        var txtruom =  $("#txt"+fieldid5+"").val();
        var txtmqtyf = $("#txt"+fieldid5+"").data("desc");
        var fieldid6 = $(this).parent().parent().children('[id*="itax"]').attr('id');
        
        
        txtauomqty = (parseFloat(txtmuomqty)/parseFloat(txtmqtyf))*parseFloat(txtauomqty);
        
        
        if(intRegex.test(txtauomqty)){
            txtauomqty = (txtauomqty +'.000');
        }

        if(intRegex.test(txtmuomqty)){
          txtmuomqty = (txtmuomqty +'.000');
        }
        
       if($(this).is(":checked") == true) 
       {
        $('#example2').find('.participantRow').each(function()
         {
           var itemid = $(this).find('[id*="ITEMID_REF"]').val();
           if(txtval)
           {
                if(txtval == itemid)
                {
                  $("#ITEMIDpopup").hide();
                      $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn").hide();
                      $("#OkBtn1").show();
                      $("#AlertMessage").text('Item already exists.');
                      $("#alert").modal('show');
                      $("#OkBtn1").focus();
                      highlighFocusBtn('activeOk1');
                      $('#hdn_ItemID').val('');
                      $('#hdn_ItemID2').val('');
                      $('#hdn_ItemID3').val('');
                      $('#hdn_ItemID4').val('');
                      $('#hdn_ItemID5').val('');
                      $('#hdn_ItemID6').val('');
                      $('#hdn_ItemID7').val('');
                      $('#hdn_ItemID8').val('');
                      $('#hdn_ItemID9').val('');
                      $('#hdn_ItemID10').val('');
                      $('#hdn_ItemID11').val('');
                      txtval = '';
                      texdesc = '';
                      txtname = '';
                      txtspec = '';
                      txtmuom = '';
                      txtauom = '';
                      txtmuomid = '';
                      txtauomid = '';
                      txtauomqty='';
                      txtmuomqty='';
                      txtruom = '';
                      return false;
                }               
           }          
        });
                      if($('#hdn_ItemID').val() == "" && txtval != '')
                      {
                        var txtid= $('#hdn_ItemID').val();
                        var txt_id2= $('#hdn_ItemID2').val();
                        var txt_id3= $('#hdn_ItemID3').val();
                        var txt_id4= $('#hdn_ItemID4').val();
                        var txt_id5= $('#hdn_ItemID5').val();
                        var txt_id6= $('#hdn_ItemID6').val();
                        var txt_id7= $('#hdn_ItemID7').val();
                        var txt_id8= $('#hdn_ItemID8').val();
                        var txt_id9= $('#hdn_ItemID9').val();
                        var txt_id10= $('#hdn_ItemID10').val();
                        var txt_id11= $('#hdn_ItemID11').val();

                        var $tr = $('.material').closest('table');
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

                        $clone.find('.remove').removeAttr('disabled'); 
                        $clone.find('[id*="popupITEMID"]').val(texdesc);
                        $clone.find('[id*="ITEMID_REF"]').val(txtval);
                        $clone.find('[id*="ItemName"]').val(txtname);

                        $clone.find('[id*="Alpspartno"]').val(apartno);
                        $clone.find('[id*="Custpartno"]').val(cpartno);
                        $clone.find('[id*="OEMpartno"]').val(opartno);

                        $clone.find('[id*="Itemspec"]').val(txtspec);
                        $clone.find('[id*="popupMUOM"]').val(txtmuom);
                        $clone.find('[id*="MAIN_UOMID_REF"]').val(txtmuomid);
                        $clone.find('[id*="SE_QTY"]').val(txtmuomqty);
                        $clone.find('[id*="popupAUOM"]').val(txtauom);
                        $clone.find('[id*="ALT_UOMID_REF"]').val(txtauomid);
                        $clone.find('[id*="ALT_UOMID_QTY"]').val(txtauomqty);
                        
                        $tr.closest('table').append($clone);   
                        var rowCount = $('#Row_Count1').val();
                          rowCount = parseInt(rowCount)+1;
                          $('#Row_Count1').val(rowCount);

                          $("#ITEMIDpopup").hide();
                         
                        event.preventDefault();
                      }
                      else
                      {
                      var txtid= $('#hdn_ItemID').val();
                      var txt_id2= $('#hdn_ItemID2').val();
                      var txt_id3= $('#hdn_ItemID3').val();
                      var txt_id4= $('#hdn_ItemID4').val();
                      var txt_id5= $('#hdn_ItemID5').val();
                      var txt_id6= $('#hdn_ItemID6').val();
                      var txt_id7= $('#hdn_ItemID7').val();
                      var txt_id8= $('#hdn_ItemID8').val();
                      var txt_id9= $('#hdn_ItemID9').val();
                      var txt_id10= $('#hdn_ItemID10').val();
                      var txt_id11= $('#hdn_ItemID11').val();
                      $('#'+txtid).val(texdesc);
                      $('#'+txt_id2).val(txtval);
                      $('#'+txt_id3).val(txtname);
                      $('#'+txt_id4).val(txtspec);
                      $('#'+txt_id5).val(txtmuom);
                      $('#'+txt_id6).val(txtmuomid);
                      $('#'+txt_id7).val(txtmuomqty);
                      $('#'+txt_id8).val(txtauom);
                      $('#'+txt_id9).val(txtauomid);
                      $('#'+txt_id10).val(txtauomqty);

                      $('#'+txtid).parent().parent().find('[id*="Alpspartno"]').val(apartno);
                      $('#'+txtid).parent().parent().find('[id*="Custpartno"]').val(cpartno);
                      $('#'+txtid).parent().parent().find('[id*="OEMpartno"]').val(opartno);
                      
                      // $("#ITEMIDpopup").hide();
                      $('#hdn_ItemID').val('');
                      $('#hdn_ItemID2').val('');
                      $('#hdn_ItemID3').val('');
                      $('#hdn_ItemID4').val('');
                      $('#hdn_ItemID5').val('');
                      $('#hdn_ItemID6').val('');
                      $('#hdn_ItemID7').val('');
                      $('#hdn_ItemID8').val('');
                      $('#hdn_ItemID9').val('');
                      $('#hdn_ItemID10').val('');
                      $('#hdn_ItemID11').val('');
                      
                      }
                      $("#ITEMIDpopup").hide();
                      event.preventDefault();
       }
       else if($(this).is(":checked") == false) 
       {
         var id = txtval;
         var r_count = $('#Row_Count1').val();
         $('#example2').find('.participantRow').each(function()
         {
           var itemid = $(this).find('[id*="ITEMID_REF"]').val();
           if(id == itemid)
           {
              var rowCount = $('#Row_Count1').val();
              if (rowCount > 1) {
                $(this).closest('.participantRow').remove(); 
                rowCount = parseInt(rowCount)-1;
              $('#Row_Count1').val(rowCount);
              }
              else 
              {
                $(document).find('.dmaterial').prop('disabled', true);  
                $("#ITEMIDpopup").hide();
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
           }
        });
       }
        $("#Itemcodesearch").val(''); 
        $("#Itemnamesearch").val(''); 
        $("#ItemUOMsearch").val(''); 
        $("#ItemGroupsearch").val(''); 
        $("#ItemCategorysearch").val(''); 
        $("#ItemStatussearch").val(''); 
        $('.remove').removeAttr('disabled'); 
       
        event.preventDefault();
      });
    }

      

  //Item ID Dropdown Ends
//------------------------

//------------------------
//Packaging Type Dropdown
  let pttid = "#PackagingTable2";
      let pttid2 = "#PackagingTable";
      let ptheaders = document.querySelectorAll(pttid2 + " th");

      // Sort the table element when clicking on the table headers
      ptheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(pttid, ".clsptid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function PackagingCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Packagingcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("PackagingTable2");
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

  function PackagingNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Packagingnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("PackagingTable2");
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

  $('#Material').on('click','[id*="PACKSIZE"]',function(event){

    var index_id=$(this).attr('id').split('_')[1]
    var packagingid='#PTID_REF_'+index_id;
    var PACKAGING_VAL=$(packagingid).val(); 
    showSelectedCheck(PACKAGING_VAL,"packingtype");

         $("#Packagingpopup").show();
         var id = $(this).attr('id');
         var id2 = $(this).parent().parent().find('[id*="PTID_REF"]').attr('id');
         $('#hdn_Packaging').val(id);
         $('#hdn_Packaging2').val(id2);
      });

      $("#PackagingclosePopup").click(function(event){
        $("#Packagingpopup").hide();
      });

      $(".clsptid").click(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");

        var txtid = $('#hdn_Packaging').val();
        var txt_id2 = $('#hdn_Packaging2').val();
        $('#'+txtid).val(texdesc);
        $('#'+txt_id2).val(txtval);
        if(txtval == '')
        {
          $('#'+txtid).parent().parent().find('[id*="PACKUOM"]').prop('readonly',true);
          $('#'+txtid).parent().parent().find('[id*="PACKUOM"]').removeAttr('readonly');
          $('#'+txtid).parent().parent().find('[id*="PACK_QTY"]').prop('readonly',true);
        }
        else
        {
          $('#'+txtid).parent().parent().find('[id*="PACKUOM"]').removeAttr('readonly');
          $('#'+txtid).parent().parent().find('[id*="PACK_QTY"]').removeAttr('readonly');
          $('#'+txtid).parent().parent().find('[id*="PACKUOM"]').prop('readonly',true);
        }

        
        $("#Packagingpopup").hide();
        
        $("#Packagingcodesearch").val(''); 
        $("#Packagingnamesearch").val(''); 
        PackagingCodeFunction();
        event.preventDefault();
      });      

  //Packaging Type Dropdown Ends
//------------------------

//------------------------
//UOM Type Dropdown
let uomtid = "#UOMTable2";
      let uomtid2 = "#UOMTable";
      let uomheaders = document.querySelectorAll(uomtid2 + " th");

      // Sort the table element when clicking on the table headers
      uomheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(uomtid, ".clsuomid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function UOMCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("UOMcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("UOMTable2");
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

  function UOMNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("UOMnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("UOMTable2");
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

  $('#Material').on('click','[id*="PACKUOM"]',function(event){

    var index_id=$(this).attr('id').split('_')[1]
    var packageid='#PACKUOMID_REF_'+index_id;
    var UOM_VAL=$(packageid).val(); 
    showSelectedCheck(UOM_VAL,"uomcheck");


         $("#UOMpopup").show();
         var id = $(this).attr('id');
         var id2 = $(this).parent().parent().find('[id*="PACKUOMID_REF"]').attr('id');
         $('#hdn_UOM').val(id);
         $('#hdn_UOM2').val(id2);
      });

      $("#UOMclosePopup").click(function(event){
        $("#UOMpopup").hide();
      });

      $(".clsuomid").click(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");

        var txtid = $('#hdn_UOM').val();
        var txt_id2 = $('#hdn_UOM2').val();
        $('#'+txtid).val(texdesc);
        $('#'+txt_id2).val(txtval);
        
        $("#UOMpopup").hide();
        
        $("#UOMcodesearch").val(''); 
        $("#UOMnamesearch").val(''); 
      
        event.preventDefault();
      });      

  //UOM Type Dropdown Ends
//------------------------

//------------------------
  //ALT UOM Dropdown
  let altutid = "#altuomTable2";
      let altutid2 = "#altuomTable";
      let altutidheaders = document.querySelectorAll(altutid2 + " th");

      // Sort the table element when clicking on the table headers
      altutidheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(altutid, ".clsaltuom", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function altuomCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("altuomcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("altuomTable2");
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

      function altuomNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("altuomnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("altuomTable2");
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

      

  $('#Material').on('click','[id*="popupAUOM"]',function(event){
        var ItemID = $(this).parent().parent().find('[id*="ITEMID_REF"]').val();
        var fieldid = $(this).parent().parent().find('[id*="ALT_UOMID_REF"]').attr('id');

        if(ItemID !=''){
                $("#tbody_altuom").html('');
                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  });
                  $.ajax({
                      url:'{{route("transaction",[35,"getAltUOM"])}}',
                      type:'POST',
                      data:{'id':ItemID,fieldid:fieldid},
                      success:function(data) {
                        
                        $("#tbody_altuom").html(data);   
                        bindAltUOM();
                        showSelectedCheck($("#"+fieldid).val(),"SELECT_"+fieldid)                      
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                        $("#tbody_altuom").html('');                        
                      },
                  }); 
        }
        else
        {
                $("#altuompopup").hide();
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text('Please Select Item First.');
                $("#alert").modal('show');
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk1');
                return false;
        }

        $("#altuompopup").show();
        var id = $(this).attr('id');
        var id2 = $(this).parent().parent().find('[id*="ALT_UOMID_REF"]').attr('id');
        var id3 = $(this).parent().parent().find('[id*="SE_QTY"]').attr('id');
        var id4 = $(this).parent().parent().find('[id*="ALT_UOMID_QTY"]').attr('id');
        
        $('#hdn_altuom').val(id);
        $('#hdn_altuom2').val(id2);
        $('#hdn_altuom3').val(id3);
        $('#hdn_altuom4').val(id4);
        event.preventDefault();
      });

      $("#altuom_closePopup").click(function(event){
        $("#altuompopup").hide();
      });

    function bindAltUOM(){

      $('#altuomTable2').off(); 

      $(".clsaltuom").click(function(){
        var fieldid = $(this).attr('id');
        var txtval =   $("#txt"+fieldid+"").val();
        var texdesc =  $("#txt"+fieldid+"").data("desc");
        var txtid= $('#hdn_altuom').val();
        var txt_id2= $('#hdn_altuom2').val();
        var txt_id3= $('#hdn_altuom3').val();
        var txt_id4= $('#hdn_altuom4').val();
        $('#'+txtid).val(texdesc);
        $('#'+txt_id2).val(txtval);

        var itemid = $('#'+txtid).parent().parent().find('[id*="ITEMID_REF"]').val();
        var altuomid = txtval;
        var mqty = $('#'+txtid).parent().parent().find('[id*="SE_QTY"]').val();

        if(altuomid!=''){
              $('#'+txt_id4).val('');
                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  });
                  $.ajax({
                      url:'{{route("transaction",[35,"getaltuomqty"])}}',
                      type:'POST',
                      data:{'id':altuomid, 'itemid':itemid, 'mqty':mqty},
                      success:function(data) {
                        if(intRegex.test(data)){
                            data = (data +'.000');
                        }
                        $('#'+txt_id4).val(data);                        
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                        $('#'+txt_id4).val('');                        
                      },
                  }); 
                      
              }

        $("#altuompopup").hide();
        $("#altuomcodesearch").val(''); 
        $("#altuomnamesearch").val(''); 
        
      
        event.preventDefault();
      });
    }

      


      

  //Alt UOM Dropdown Ends
//------------------------
     
$(document).ready(function(e) {
var Material = $("#Material").html(); 
$('#hdnmaterial').val(Material);
var lastenqdt = <?php echo json_encode($objlastENQDT[0]->ENQDT); ?>;
var today = new Date(); 



var seudf = <?php echo json_encode($objUdfSEData); ?>;
var count2 = <?php echo json_encode($objCountUDF); ?>;

$('#example3').find('.participantRow3').each(function(){
      var txt_id4 = $(this).find('[id*="udfinputid"]').attr('id');
      var udfid = $(this).find('[id*="SEID_REF"]').val();
      $.each( seudf, function( seukey, seuvalue ) {
        if(seuvalue.UDFSEID == udfid)
        {
          var txtvaltype2 =   seuvalue.VALUETYPE;
          var strdyn2 = txt_id4.split('_');
          var lastele2 =   strdyn2[strdyn2.length-1];
          var dynamicid2 = "udfvalue_"+lastele2;
          
          var chkvaltype2 =  txtvaltype2.toLowerCase();
          var strinp2 = '';

          if(chkvaltype2=='date'){
          strinp2 = '<input {{$ActionStatus}} type="date" placeholder="dd/mm/yyyy" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"  > ';       
          }
          else if(chkvaltype2=='time'){
          strinp2= '<input {{$ActionStatus}} type="time" placeholder="h:i" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"  > ';
          }
          else if(chkvaltype2=='numeric'){
          strinp2 = '<input {{$ActionStatus}} type="text" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"   > ';
          }
          else if(chkvaltype2=='text'){
          strinp2 = '<input {{$ActionStatus}} type="text" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"  > ';          
          }
          else if(chkvaltype2=='boolean'){            
              strinp2 = '<input {{$ActionStatus}} type="checkbox" name="'+dynamicid2+ '" id="'+dynamicid2+'" class="" > ';
          }
          else if(chkvaltype2=='combobox'){
          var txtoptscombo2 =   seuvalue.DESCRIPTIONS;
          var strarray2 = txtoptscombo2.split(',');
          var opts2 = '';
          for (var i = 0; i < strarray2.length; i++) {
              opts2 = opts2 + '<option value="'+strarray2[i]+'">'+strarray2[i]+'</option> ';
          }
          strinp2 = '<select {{$ActionStatus}} name="'+dynamicid2+ '" id="'+dynamicid2+'" class="form-control" required>'+opts2+'</select>' ;          
          }
          $('#'+txt_id4).html('');  
          $('#'+txt_id4).html(strinp2);
        }
      });
    });
var count1 = <?php echo json_encode($objCount1); ?>;
var count2 = <?php echo json_encode($objCount2); ?>;
$('#Row_Count1').val(count1);
$('#Row_Count2').val(count2);


var soudf = <?php echo json_encode($objSEUDF); ?>;
var udfforse = <?php echo json_encode($objUdfSEData2); ?>;
$.each( soudf, function( soukey, souvalue ) {
    $.each( udfforse, function( usokey, usovalue ) { 
        if(souvalue.SEID_REF == usovalue.UDFSEID)
        {
            $('#popupSEID_'+soukey).val(usovalue.LABEL);
        }
    
        if(souvalue.SEID_REF == usovalue.UDFSEID)
        {        
                var txtvaltype2 =   usovalue.VALUETYPE;
                var txt_id41 = $('#udfinputid_'+soukey).attr('id');
                var strdyn2 = txt_id41.split('_');
                var lastele2 =   strdyn2[strdyn2.length-1];
                var dynamicid2 = "udfvalue_"+lastele2;
                
                var chkvaltype2 =  txtvaltype2.toLowerCase();
                var strinp2 = '';

                if(chkvaltype2=='date'){

                strinp2 = '<input {{$ActionStatus}} type="date" placeholder="dd/mm/yyyy" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"  > ';       

                }
                else if(chkvaltype2=='time'){
                strinp2= '<input {{$ActionStatus}} type="time" placeholder="h:i" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"  > ';

                }
                else if(chkvaltype2=='numeric'){
                strinp2 = '<input {{$ActionStatus}} type="text" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"   > ';

                }
                else if(chkvaltype2=='text'){

                strinp2 = '<input {{$ActionStatus}} type="text" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"  > ';
                
                }
                else if(chkvaltype2=='boolean'){
                    if(souvalue.SOUVALUE == "1")
                    {
                    strinp2 = '<input {{$ActionStatus}} type="checkbox" name="'+dynamicid2+ '" id="'+dynamicid2+'" class="" checked> ';
                    }
                    else{
                    strinp2 = '<input {{$ActionStatus}} type="checkbox" name="'+dynamicid2+ '" id="'+dynamicid2+'" class="" > ';
                    }
                }
                else if(chkvaltype2=='combobox'){

                var txtoptscombo2 =   usovalue.DESCRIPTIONS;
                var strarray2 = txtoptscombo2.split(',');
                var opts2 = '';

                for (var i = 0; i < strarray2.length; i++) {
                    opts2 = opts2 + '<option value="'+strarray2[i]+'">'+strarray2[i]+'</option> ';
                }

                strinp2 = '<select {{$ActionStatus}} name="'+dynamicid2+ '" id="'+dynamicid2+'" class="form-control" required>'+opts2+'</select>' ;
                
                }
                
                
                $('#'+txt_id41).html('');  
                $('#'+txt_id41).html(strinp2);   //set dynamic input
                $('#'+dynamicid2).val(souvalue.SEQUVALUE);
                $('#UDFismandatory_'+soukey).val(usovalue.ISMANDATORY); // mandatory
            
        }
    });
  
});




$('#CONV_PROLTY').focusout(function(e){
  var cvalue = $(this).val();
  if(intRegex.test(cvalue)){
    cvalue = cvalue +'.0000';
      }
  $(this).val(cvalue);
});
$('#APPROXV').focusout(function(e){
  var avalue = $(this).val();
  if(intRegex.test(avalue)){
    avalue = avalue +'.00';
      }
  $(this).val(avalue);
});

$('#Material').on('focusout',"[id*='ALT_UOMID_QTY']",function()
{
  if(intRegex.test($(this).val())){
    $(this).val($(this).val()+'.000')
    }
    event.preventDefault();
});
$('#Material').on('focusout',"[id*='TARGETPRICE']",function()
{
  if(intRegex.test($(this).val())){
    $(this).val($(this).val()+'.00')
    }
    event.preventDefault();
});
$('#Material').on('focusout',"[id*='PACK_QTY']",function()
{
  if(intRegex.test($(this).val())){
    $(this).val($(this).val()+'.000')
    }
    event.preventDefault();
});

$('#Material').on('focusout',"[id*='SE_QTY']",function()
{
    var itemid = $(this).parent().parent().find('[id*="ITEMID_REF"]').val();
    var mqty = $(this).val();
    var altuomid = $(this).parent().parent().find('[id*="ALT_UOMID_REF"]').val();
    var txtid = $(this).parent().parent().find('[id*="ALT_UOMID_QTY"]').attr('id');
    if(altuomid!=''){
          
              $.ajaxSetup({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  }
              });
              $.ajax({
                  url:'{{route("transaction",[35,"getaltuomqty"])}}',
                  type:'POST',
                  data:{'id':altuomid, 'itemid':itemid, 'mqty':mqty},
                  success:function(data) {
                    if(intRegex.test(data)){
                        data = (data +'.000');
                    }
                    $("#"+txtid).val(data);                        
                  },
                  error:function(data){
                    console.log("Error: Something went wrong.");
                    $("#"+txtid).val('');                        
                  },
              }); 
                  
          }
  if(intRegex.test($(this).val())){
    $(this).val($(this).val()+'.000');
  }
  event.preventDefault();
});

$('#btnAdd').on('click', function() {
  var viewURL = '{{route("transaction",[35,"add"])}}';
              window.location.href=viewURL;
});

$('#btnExit').on('click', function() {
  var viewURL = '{{route('home')}}';
              window.location.href=viewURL;
});

$('#ENQDT').change(function() {
    var mindate  = $(this).val();
    $('[id*="EDD"]').attr('min',mindate);
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
          event.preventDefault();
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
  var d = new Date(); 
  var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
  $clone.find('[id*="EDD"]').val(today);
  $tr.closest('table').append($clone);         
  var rowCount1 = $('#Row_Count1').val();
  rowCount1 = parseInt(rowCount1)+1;
  $('#Row_Count1').val(rowCount1);
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
    window.location.reload();
}//fnUndoYes

window.fnUndoNo = function (){
    $("#ENQNO").focus();
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
    var d = new Date(); 
    var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;


    $('#frm_trn_se1').bootstrapValidator({
       
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
             $("#frm_trn_se").submit();
        }
    });
});
$( "#btnSaveSE" ).click(function() {
    var formSalesEnquiry = $("#frm_trn_se");
    if(formSalesEnquiry.valid()){

        $("#FocusId").val('');
 var ENQNO          =   $.trim($("#ENQNO").val());
 var ENQDT          =   $.trim($("#ENQDT").val());
 var SLID_REF       =   $.trim($("#SLID_REF").val());
 var EMID_REF       =   $.trim($("#EMID_REF").val());
 var ENQBY          =   $.trim($("#ENQBY").val());
 var PRIORITYID_REF =   $.trim($("#PRIORITYID_REF").val());
 var SPID_REF       =   $.trim($("#SPID_REF").val());

 if(ENQNO ===""){
     $("#FocusId").val('ENQNO');
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Enter Sales Enquiry No.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if(ENQDT ===""){
     $("#FocusId").val('ENQDT');
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Select Sales Enquiry Date .');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if(SLID_REF ===""){
     $("#FocusId").val('txtgl_popup');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please select Customer.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if(EMID_REF ===""){
     $("#FocusId").val('EMID_popup');
     $("#EMID_REF").val(''); 
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Select Enquiry Media.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 } 
 else if(ENQBY ===""){
     $("#FocusId").val('ENQBY');
     $("#ENQBY").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Enter Enquired By.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 } 
 else if(PRIORITYID_REF ===""){
     $("#FocusId").val('PRIORITYID_popup');
     $("#PRIORITYID_REF").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Select Priority.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 } 
 else if(SPID_REF ===""){
     $("#FocusId").val('txtSPID_popup');
     $("#SPID_REF").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Select Sales Person.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 } 
 else{  
    event.preventDefault();
    var allblank = [];
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
    var allblank12 = [];

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
    var focustext12= "";

        // $('#udfforsebody').find('.form-control').each(function () {
          $('#example2').find('.participantRow').each(function(){
            if($.trim($(this).find("[id*=ITEMID_REF]").val())!="")
            {
                allblank.push('true');
                    if($.trim($(this).find("[id*=MAIN_UOMID_REF]").val())!=""){
                        allblank2.push('true');
                          if($.trim($(this).find('[id*="SE_QTY"]').val()) != "" && $.trim($(this).find('[id*="SE_QTY"]').val()) > "0.000")
                          {
                            allblank3.push('true');
                          }
                          else
                          {
                            allblank3.push('false');
                            focustext3 = $(this).find("[id*=SE_QTY]").attr('id');
                          }  
                    }
                    else{
                        allblank2.push('false');
                        focustext2 = $(this).find("[id*=popupMUOM]").attr('id');
                    }
                    if($.trim($(this).find("[id*=PTID_REF]").val())!=""){
                        if($.trim($(this).find('[id*="PACKUOMID_REF"]').val()) != "")
                          {
                            allblank6.push('true');
                          }
                          else
                          {
                            allblank6.push('false');
                            focustext6 = $(this).find("[id*=PACKUOM]").attr('id');
                          } 
                          
                          if($.trim($(this).find('[id*="PACK_QTY"]').val()) != "" && $.trim($(this).find('[id*="PACK_QTY"]').val()) != "0.000")
                          {
                            allblank7.push('true');
                          }
                          else
                          {
                            allblank7.push('false');
                            focustext7 = $(this).find("[id*=PACK_QTY]").attr('id');
                          }  

                          if($.trim($(this).find('[id*="PACK_QTY"]').val()) != "" && $.trim(parseFloat($(this).find('[id*="PACK_QTY"]').val())) >= 1 )
                          {
                            allblank7.push('true');
                          }
                          else
                          {
                            allblank7.push('false');
                            focustext7 = $(this).find("[id*=PACK_QTY]").attr('id');
                          }  



                    }
                    if($.trim($(this).find("[id*=EDD]").val())!=""){
                        allblank8.push('true');
                      }
                      else
                      {
                        allblank8.push('false');
                        focustext8 = $(this).find("[id*=EDD]").attr('id');
                      } 

                      if(LessDateValidateWithToday( $.trim($(this).find("[id*=EDD]").val()) )==true ){
                          allblank9.push('true');
                      }
                      else{
                        allblank9.push('false');
                        focustext9 = $(this).find("[id*=EDD]").attr('id');
                      }




            }
            else
            {
                allblank.push('false');
                focustext1 = $(this).find("[id*=popupITEMID]").attr('id');
            }
        });
        $('#example3').find('.participantRow3').each(function(){
              if($.trim($(this).find("[id*=SEID_REF]").val())!="")
                {
                    allblank4.push('true');
                        if($.trim($(this).find("[id*=UDFismandatory]").val())=="1"){
                              if($.trim($(this).find('[id*="udfvalue"]').val()) != "")
                              {
                                allblank5.push('true');
                              }
                              else
                              {
                                allblank5.push('false');
                                focustext5 = $(this).find("[id*=udfvalue]").attr('id');
                              }
                        }  
                }                
        });

        if(jQuery.inArray("false", allblank) !== -1){
          $("#FocusId").val(focustext1);
          $("#alert").modal('show');
          $("#AlertMessage").text('Please select item in Material Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
          }
          else if(jQuery.inArray("false", allblank2) !== -1){
          $("#FocusId").val(focustext2);
          $("#alert").modal('show');
          $("#AlertMessage").text('Main UOM is missing in Material Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
          }
          else if(jQuery.inArray("false", allblank3) !== -1){
          $("#FocusId").val(focustext3);
          $("#alert").modal('show');
          $("#AlertMessage").text('Main UOM Quantity cannot be zero or blank in Material Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
          }
          else if(jQuery.inArray("false", allblank8) !== -1){
            $("#FocusId").val(focustext8);
          $("#alert").modal('show');
          $("#AlertMessage").text('Expected Date of Delivery cannot be blank in Material Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
          }
          else if(jQuery.inArray("false", allblank9) !== -1){
            $("#FocusId").val(focustext9);
          $("#alert").modal('show');
          $("#AlertMessage").text('Expected Date should not less then current date in Material tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
          }
          else if(jQuery.inArray("false", allblank6) !== -1){
          $("#FocusId").val(focustext6);
          $("#alert").modal('show');
          $("#AlertMessage").text('UOM cannot be blank if Packaging Type is Selected in Material Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
          }
          else if(jQuery.inArray("false", allblank7) !== -1){
          $("#FocusId").val(focustext7);
          $("#alert").modal('show');
          $("#AlertMessage").text('Pack Quantity cannot be blank/Zero if Packaging Type is Selected in Material Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
          }
          else if(jQuery.inArray("false", allblank5) !== -1){
            $("#UDF_TAB").click();
            $("#FocusId").val(focustext5);
            $("#alert").modal('show');
            $("#AlertMessage").text('Please Enter  Value / Comment in UDF Tab.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk');
          }
                    else{
                          $("#alert").modal('show');
                          $("#AlertMessage").text('Do you want to save to record.');
                          $("#YesBtn").data("funcname","fnSaveData");  //set dynamic fucntion name
                          $("#YesBtn").focus();
                          $("#OkBtn").hide();
                          highlighFocusBtn('activeYes');
                    }
          }
    }
});

$( "#btnApprove" ).click(function() {
    var formSalesEnquiry = $("#frm_trn_se");
    if(formSalesEnquiry.valid()){

        $("#FocusId").val('');
 var ENQNO          =   $.trim($("#ENQNO").val());
 var ENQDT          =   $.trim($("#ENQDT").val());
 var SLID_REF       =   $.trim($("#SLID_REF").val());
 var EMID_REF       =   $.trim($("#EMID_REF").val());
 var ENQBY          =   $.trim($("#ENQBY").val());
 var PRIORITYID_REF =   $.trim($("#PRIORITYID_REF").val());
 var SPID_REF       =   $.trim($("#SPID_REF").val());

 if(ENQNO ===""){
     $("#FocusId").val('ENQNO');
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Enter Sales Enquiry No.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if(ENQDT ===""){
     $("#FocusId").val('ENQDT');
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Select Sales Enquiry Date .');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if(SLID_REF ===""){
     $("#FocusId").val('txtgl_popup');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please select Customer.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if(EMID_REF ===""){
     $("#FocusId").val('EMID_popup');
     $("#EMID_REF").val(''); 
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Select Enquiry Media.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 } 
 else if(ENQBY ===""){
     $("#FocusId").val('ENQBY');
     $("#ENQBY").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Enter Enquired By.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 } 
 else if(PRIORITYID_REF ===""){
     $("#FocusId").val('PRIORITYID_popup');
     $("#PRIORITYID_REF").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Select Priority.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 } 
 else if(SPID_REF ===""){
     $("#FocusId").val('txtSPID_popup');
     $("#SPID_REF").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Select Sales Person.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 } 
 else{  
    event.preventDefault();
    var allblank = [];
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
    var allblank12 = [];

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
    var focustext12= "";

       
          $('#example2').find('.participantRow').each(function(){
            if($.trim($(this).find("[id*=ITEMID_REF]").val())!="")
            {
                allblank.push('true');
                    if($.trim($(this).find("[id*=MAIN_UOMID_REF]").val())!=""){
                        allblank2.push('true');
                          if($.trim($(this).find('[id*="SE_QTY"]').val()) != "" && $.trim($(this).find('[id*="SE_QTY"]').val()) > "0.000")
                          {
                            allblank3.push('true');
                          }
                          else
                          {
                            allblank3.push('false');
                            focustext3 = $(this).find("[id*=SE_QTY]").attr('id');
                          }  
                    }
                    else{
                        allblank2.push('false');
                        focustext2 = $(this).find("[id*=popupMUOM]").attr('id');
                    }
                    if($.trim($(this).find("[id*=PTID_REF]").val())!=""){
                        if($.trim($(this).find('[id*="PACKUOMID_REF"]').val()) != "")
                          {
                            allblank6.push('true');
                          }
                          else
                          {
                            allblank6.push('false');
                            focustext6 = $(this).find("[id*=PACKUOM]").attr('id');
                          } 
                          
                          if($.trim($(this).find('[id*="PACK_QTY"]').val()) != "" && $.trim($(this).find('[id*="PACK_QTY"]').val()) != "0.000")
                          {
                            allblank7.push('true');
                          }
                          else
                          {
                            allblank7.push('false');
                            focustext7 = $(this).find("[id*=PACK_QTY]").attr('id');
                          }  

                          if($.trim($(this).find('[id*="PACK_QTY"]').val()) != "" && $.trim(parseFloat($(this).find('[id*="PACK_QTY"]').val())) >= 1 )
                          {
                            allblank7.push('true');
                          }
                          else
                          {
                            allblank7.push('false');
                            focustext7 = $(this).find("[id*=PACK_QTY]").attr('id');
                          }  



                    }
                    if($.trim($(this).find("[id*=EDD]").val())!=""){
                        allblank8.push('true');
                      }
                      else
                      {
                        allblank8.push('false');
                        focustext8 = $(this).find("[id*=EDD]").attr('id');
                      } 

                      if(LessDateValidateWithToday( $.trim($(this).find("[id*=EDD]").val()) )==true ){
                          allblank9.push('true');
                      }
                      else{
                        allblank9.push('false');
                        focustext9 = $(this).find("[id*=EDD]").attr('id');
                      }




            }
            else
            {
                allblank.push('false');
                focustext1 = $(this).find("[id*=popupITEMID]").attr('id');
            }
        });
        $('#example3').find('.participantRow3').each(function(){
              if($.trim($(this).find("[id*=SEID_REF]").val())!="")
                {
                    allblank4.push('true');
                        if($.trim($(this).find("[id*=UDFismandatory]").val())=="1"){
                              if($.trim($(this).find('[id*="udfvalue"]').val()) != "")
                              {
                                allblank5.push('true');
                              }
                              else
                              {
                                allblank5.push('false');
                                focustext5 = $(this).find("[id*=udfvalue]").attr('id');
                              }
                        }  
                }                
        });

        if(jQuery.inArray("false", allblank) !== -1){
          $("#FocusId").val(focustext1);
          $("#alert").modal('show');
          $("#AlertMessage").text('Please select item in Material Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
          }
          else if(jQuery.inArray("false", allblank2) !== -1){
          $("#FocusId").val(focustext2);
          $("#alert").modal('show');
          $("#AlertMessage").text('Main UOM is missing in Material Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
          }
          else if(jQuery.inArray("false", allblank3) !== -1){
          $("#FocusId").val(focustext3);
          $("#alert").modal('show');
          $("#AlertMessage").text('Main UOM Quantity cannot be zero or blank in Material Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
          }
          else if(jQuery.inArray("false", allblank8) !== -1){
            $("#FocusId").val(focustext8);
          $("#alert").modal('show');
          $("#AlertMessage").text('Expected Date of Delivery cannot be blank in Material Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
          }
          else if(jQuery.inArray("false", allblank9) !== -1){
            $("#FocusId").val(focustext9);
          $("#alert").modal('show');
          $("#AlertMessage").text('Expected Date should not less then current date in Material tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
          }
          else if(jQuery.inArray("false", allblank6) !== -1){
          $("#FocusId").val(focustext6);
          $("#alert").modal('show');
          $("#AlertMessage").text('UOM cannot be blank if Packaging Type is Selected in Material Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
          }
          else if(jQuery.inArray("false", allblank7) !== -1){
          $("#FocusId").val(focustext7);
          $("#alert").modal('show');
          $("#AlertMessage").text('Pack Quantity cannot be blank/Zero if Packaging Type is Selected in Material Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
          }
          else if(jQuery.inArray("false", allblank5) !== -1){
            $("#UDF_TAB").click();
            $("#FocusId").val(focustext5);
            $("#alert").modal('show');
            $("#AlertMessage").text('Please Enter  Value / Comment in UDF Tab.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk');
          }
                    else{
                          $("#alert").modal('show');
                          $("#AlertMessage").text('Do you want to save to record.');
                          $("#YesBtn").data("funcname","fnApproveData");  //set dynamic fucntion name
                          $("#YesBtn").focus();
                          $("#OkBtn").hide();
                          highlighFocusBtn('activeYes');
                    }
          }
    }
});


$("#YesBtn").click(function(){

$("#alert").modal('hide');
var customFnName = $("#YesBtn").data("funcname");
    window[customFnName]();

}); //yes button

window.fnSaveData = function (){
event.preventDefault();
      var trnseForm = $("#frm_trn_se");
      var formData = trnseForm.serialize();
  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });
  $.ajax({
      url:'{{ route("transactionmodify",[35,"update"])}}',
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
                ("#YesBtn").hide();
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
      var trnseForm = $("#frm_trn_se");
      var formData = trnseForm.serialize();
  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });
  $.ajax({
      url:'{{ route("transactionmodify",[35,"Approve"])}}',
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
                ("#YesBtn").hide();
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
    window.location.href = '{{route("transaction",[35,"index"]) }}';
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

var objSE = <?php echo json_encode($objSE); ?>;

$('#btnPdf').on('click', function() {
  var SONO = objSE.SEQID;
  var Flag = 'P';
  var formData = 'SO='+ SONO + '&ENQNO='+ SONO + '&Flag='+ Flag ;
  var consultURL = '{{route("transaction",[35,"ViewReport",":rcdId"]) }}';
  consultURL = consultURL.replace(":rcdId",formData);
  window.location.href=consultURL;
  event.preventDefault();
}); 

$('#btnExcel').on('click', function() {
  var SONO = objSE.SEQID;
  var Flag = 'E';
  var formData = 'SO='+ SONO + '&ENQNO='+ SONO + '&Flag='+ Flag ;
  var consultURL = '{{route("transaction",[35,"ViewReport",":rcdId"]) }}';
  consultURL = consultURL.replace(":rcdId",formData);
  window.location.href=consultURL;
  event.preventDefault();
});

$('#btnPrint').on('click', function() {
    var SONO = objSE.SEQID;
    var Flag = 'H';
    var formData = 'SO='+ SONO + '&ENQNO='+ SONO + '&Flag='+ Flag ;
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url:'{{route("transaction",[35,"ViewReport"])}}',
        type:'POST',
        data:formData,
        success:function(data) {
            $('#ReportView').show();
            var localS = data;
            document.getElementById('iframe_rpt').src = "data:text/html;charset=utf-8," + escape(localS);
            $('#btnPdf').show();
            $('#btnExcel').show();
            $('#btnPrint').show();
        },
        error:function(data){
            console.log("Error: Something went wrong.");
            var localS = "";
            document.getElementById('iframe_rpt').src = "data:text/html;charset=utf-8," + escape(localS);
            $('#btnPdf').hide();
            $('#btnExcel').hide();
            $('#btnPrint').hide();
        },
    });
    event.preventDefault();
  });

  $('#btnReport').on('click', function() {
    var SONO = objSE.SEQID;
    var Flag = 'R';
    var formData = 'SO='+ SONO + '&ENQNO='+ SONO + '&Flag='+ Flag ;
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url:'{{route("transaction",[35,"ViewReport"])}}',
        type:'POST',
        data:formData,
        success:function(data) {
          printWindow = window.open('');
          printWindow.document.write(data);
          printWindow.print();
        },
        error:function(data){
          console.log("Error: Something went wrong.")
          printWindow = window.open('');
          printWindow.document.write("Error: Something went wrong.");
          printWindow.print();
        },
    });
    event.preventDefault();
});

$("#ReportViewclosePopup").click(function(event){
  $("#ReportView").hide();
  event.preventDefault();
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

function LessDateValidateWithToday(value){
  if(value !=""){
      var today = new Date(); 
      var d = new Date(value);
      today.setHours(0, 0, 0, 0) ;
      d.setHours(0, 0, 0, 0) ;

      if(d < today){
          return false;
      }
      else {
        return true;
      }
  }
  else{
    return true;
  }
}

function showSelectedCheck(hidden_value,selectAll){

  var divid ="";

  if(hidden_value !=""){

    var all_location_id = document.querySelectorAll('input[name="'+selectAll+'[]"]');
  
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
</script>


@endpush