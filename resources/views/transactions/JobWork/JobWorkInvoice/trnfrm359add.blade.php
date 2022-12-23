
@extends('layouts.app')
@section('content')
    

    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Job Work Invoice (JWI)</a>
                </div>

                <div class="col-lg-10 topnav-pd">
                        <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
                        <button class="btn topnavbt" id="btnSave" ><i class="fa fa-save"></i> Save</button>
                        <button style="display:none" class="btn topnavbt buttonload"> <i class="fa fa-refresh fa-spin"></i> {{Session::get('save')}}</button>
                        <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt" id="btnUndo"  ><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-lock"></i>  Approved</button>
                        <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                        <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                </div>
            </div>
    </div>

    <form id="add_trn_form" onsubmit="return validateForm()"  method="POST" class="needs-validation"  >
    <div class="container-fluid filter">

	<div class="inner-form">
	
		<div class="row">
        <div class="col-lg-2 pl"><p>JWI Doc No</p></div>
        <div class="col-lg-2 pl">
            <input type="text" name="JWINO" id="JWINO" value="{{$docarray['DOC_NO']}}" {{$docarray['READONLY']}} class="form-control" maxlength="{{$docarray['MAXLENGTH']}}" autocomplete="off" style="text-transform:uppercase" >
          <script>docMissing(@json($docarray['FY_FLAG']));</script>
        </div>
        <div class="col-lg-2 pl"><p>JWI Doc Date</p></div>
        <div class="col-lg-2 pl">
                  <input type="date" name="JWIDT" id="JWIDT" onchange='checkPeriodClosing("{{$FormId}}",this.value,1),getDocNoByEvent("JWINO",this,@json($doc_req))' value="{{ old('JWIDT') }}" class="form-control mandatory"  placeholder="dd/mm/yyyy" />
        </div>
        <div class="col-lg-2 pl"><p>Department</p></div>
        <div class="col-lg-2 pl">
                <input type="text" name="txtdepartment" id="txtdepartment" class="form-control"  readonly  />
                <input type="hidden" name="DEPID_REF" id="DEPID_REF"  class="form-control " />
        </div>

      </div>

      <div class="row">

        <div class="col-lg-2 pl"><p>Vendor</p></div>
        <div class="col-lg-2 pl">
                <input type="text" name="txtvendor" id="txtvendor" class="form-control"  readonly  />
                <input type="hidden" name="VID_REF" id="VID_REF"  class="form-control " />
                <input type="hidden" name="hdnMaterial" id="hdnMaterial" class="form-control" autocomplete="off" />
               
                <input type="hidden" name="hdnTNC" id="hdnTNC" class="form-control" autocomplete="off" />
                <input type="hidden" name="hdnCalculation" id="hdnCalculation" class="form-control" autocomplete="off" />
                
                  
        </div>
   
        <div class="col-lg-2 pl"><p> Vendor Invoice No</p></div> 
        <div class="col-lg-2 pl">
                <input type="text" name="VENDOR_INNO" id="VENDOR_INNO" value="{{ old('VENDOR_INNO') }}" maxlength="30" class="form-control" autocomplete="off" />
        </div>
        <div class="col-lg-2 pl"><p> Date</p></div>
        <div class="col-lg-2 pl">
                <input type="date" name="VENDOR_INDT" id="VENDOR_INDT" value="{{ old('VENDOR_INDT') }}" class="form-control"  placeholder="dd/mm/yyyy"  />
        </div>

      </div>

      <div class="row">

        <div class="col-lg-2 pl"><p>Bill To </p></div>
        <div class="col-lg-2 pl" id="div_billto">
            <input type="text" name="txtBILLTO1" id="txtBILLTO1" class="form-control"  autocomplete="off" readonly  />
            <input type="hidden" name="BILLTO1" id="BILLTO1" class="form-control" autocomplete="off" />
        </div>
        
        <div class="col-lg-2 pl"><p>Ship To</p></div>
        <div class="col-lg-2 pl" id="div_shipto">
            <input type="text" name="txtSHIPTO1" id="txtSHIPTO1" class="form-control"  autocomplete="off" readonly  />
            <input type="hidden" name="SHIPTO1" id="SHIPTO1" class="form-control" autocomplete="off" />
            <input type="hidden" name="Tax_State1" id="Tax_State1" class="form-control" autocomplete="off" />
        </div>

        <div class="col-lg-2 pl"><p>Remarks</p></div>
        <div class="col-lg-2 pl">
            <input type="text" name="REMARKS" id="REMARKS" autocomplete="off" class="form-control" maxlength="200"  >
        </div>

    </div>
    <div class="row">
      
        <div class="col-lg-2 pl"><p>Total Amount</p></div>
        <div class="col-lg-2 pl">
            <input type="text" name="tot_amt" id="tot_amt" class="form-control"  autocomplete="off" readonly  />
        </div>
        
    </div>
    

	</div>

	<div class="container-fluid">
		<div class="row">
			<ul class="nav nav-tabs">
				<li class="active"><a data-toggle="tab" href="#Material">Material</a></li> 
        <li><a data-toggle="tab" href="#TC">T&C</a></li> 
        <li><a data-toggle="tab" href="#CT">Calculation Template</a></li>
       
        <li><a data-toggle="tab" href="#udf">UDF</a></li>
			</ul>
      <div class="tab-content">
        <div id="Material" class="tab-pane fade in active">
            <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:320px;margin-top:10px;" >
                <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                    <thead id="thead1"  style="position: sticky;top: 0">
                          <tr>
                              <th>GRN No<input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1"></th>
                              <th>Item Code</th>
                              <th>Item Name</th>
                              <th>Main UOM</th>
                              <th>Received Qty</th>
                              <th hidden>Alt UOM</th>
                              <th hidden>Received Qty</th>
                              <th>Bill Qty</th>
                              <th hidden>UOM of Bill Qty</th>
                              <th>Item Specifications</th>
                              <th>Rate as per MU</th>
                              <th>Discount %</th>
                              <th>Discount Amount</th>
                              <th>Amount after discount</th>
                              <th>IGST</th>
                              <th>IGST Amount</th>
                              <th>CGST</th>
                              <th>CGST Amount</th>
                              <th>SGST</th>
                              <th>SGST Amount</th>
                              <th>Total GST Amount</th>
                              <th>Total after GST</th>                           
                              <th>Action</th>
                          </tr>
                    </thead>
                    <tbody>
                          <tr  class="participantRow">
                              <td style="text-align:center;" >
                              <input type="text" name="txtGRN_NO_0" id="txtGRN_NO_0" class="form-control"  autocomplete="off"  readonly style="width:130px;" /></td>
                              <td hidden><input type="hidden" name="GRJID_REF_0" id="GRJID_REF_0" class="form-control" autocomplete="off" /></td>
                              
                              <td hidden><input type="text" name="JWCID_REF_0" id="JWCID_REF_0" class="form-control" autocomplete="off" /></td>
                              <td hidden><input type="text" name="JWOID_REF_0" id="JWOID_REF_0" class="form-control" autocomplete="off" /></td>
                              <td hidden><input type="text" name="PROID_REF_0" id="PROID_REF_0" class="form-control" autocomplete="off" /></td>
                              <td hidden><input type="text" name="SOID_REF_0"  id="SOID_REF_0"  class="form-control" autocomplete="off" /></td>
                              <td hidden><input type="text" name="SQID_REF_0"  id="SQID_REF_0"  class="form-control" autocomplete="off" /></td>
                              <td hidden><input type="text" name="SEID_REF_0"  id="SEID_REF_0"  class="form-control" autocomplete="off" /></td>
                        
                              
                              <td><input type="text" name="txtItem_0" id="txtItem_0" class="form-control" autocomplete="off" readonly style="width:130px;" /></td>
                              <td hidden><input type="hidden" name="ITEMID_REF_0" id="ITEMID_REF_0" class="form-control" autocomplete="off" /></td>
                              <td><input type="text" name="ItemName_0" id="ItemName_0" class="form-control"  autocomplete="off"  readonly style="width:130px;" /></td>
                              <td><input type="text" name="txtUOM_0" id="txtUOM_0" class="form-control" maxlength="15"  autocomplete="off"  readonly style="width:130px;" /></td>
                              <td hidden><input type="hidden" name="UOMID_REF_0" id="UOMID_REF_0" class="form-control" autocomplete="off" /></td>
                              <td><input type="text" name="RECEIVED_QTY_0" id="RECEIVED_QTY_0" class="form-control three-digits" maxlength="15"  autocomplete="off" readonly style="width:130px;text-align:right;" /></td>
                              
                              <td hidden><input type="text" name="txtALTUOM_0" id="txtALTUOM_0" class="form-control" maxlength="15"  autocomplete="off"  readonly/></td>
                              <td hidden><input type="hidden" name="ALT_UOMID_REF_0" id="ALT_UOMID_REF_0" class="form-control" autocomplete="off" /></td>
                              <td hidden><input type="text" name="RECEIVED_ALT_QTY_0" id="RECEIVED_ALT_QTY_0" class="form-control three-digits" maxlength="15"  autocomplete="off" readonly /></td>
                              
                              <td><input type="text" name="BILL_QTY_0" id="BILL_QTY_0" class="form-control three-digits" maxlength="15"  autocomplete="off" style="width:130px;text-align:right;" /></td>
                              
                              <td hidden><input type="text" name="txtBILLUOM_0" id="txtBILLUOM_0" class="form-control" maxlength="15"  autocomplete="off"  readonly/></td>
                              <td hidden><input type="hidden" name="BILL_UOMID_REF_0" id="BILL_UOMID_REF_0" class="form-control" autocomplete="off" /></td>
                              
                              <td><input type="text" name="ItemDesc_0" id="ItemDesc_0" class="form-control"  autocomplete="off"  /></td>
                              <td><input type="text" name="BILL_RATEPUOM_0" id="BILL_RATEPUOM_0" class="form-control five-digits blurRate" maxlength="13"  autocomplete="off" style="width:130px;text-align:right;" readonly  /></td>
                              <td><input type="text" name="DISC_PER_0" id="DISC_PER_0" class="form-control four-digits" maxlength="12"  autocomplete="off"  style="width:130px;text-align:right;" /></td>
                              <td><input type="text" name="DISC_AMT_0" id="DISC_AMT_0" class="form-control two-digits" maxlength="15"  autocomplete="off" style="width:130px;text-align:right;" /></td>
                              <td><input type="text" name="TAX_AMT_0" id="TAX_AMT_0" class="form-control two-digits" maxlength="15"  autocomplete="off" readonly style="width:130px;text-align:right;"  /></td>
                              <td><input type="text" name="IGST_0" id="IGST_0" class="form-control four-digits" maxlength="12"  autocomplete="off"  readonly style="width:130px;text-align:right;" /></td>
                              <td><input type="text" name="IGST_AMT_0" id="IGST_AMT_0" class="form-control two-digits" maxlength="15"  autocomplete="off" readonly style="width:130px;text-align:right;"  /></td>
                              <td><input type="text" name="CGST_0" id="CGST_0" class="form-control four-digits" maxlength="12"  autocomplete="off"  readonly style="width:130px;text-align:right;" /></td>
                              <td><input type="text" name="CGST_AMT_0" id="CGST_AMT_0" class="form-control two-digits" maxlength="15"  autocomplete="off" readonly style="width:130px;text-align:right;"  /></td>
                              <td><input type="text" name="SGST_0" id="SGST_0" class="form-control four-digits" maxlength="12"  autocomplete="off"  readonly style="width:130px;text-align:right;" /></td>
                              <td><input type="text" name="SGST_AMT_0" id="SGST_AMT_0" class="form-control two-digits" maxlength="15"  autocomplete="off" readonly style="width:130px;text-align:right;" /></td>
                              <td><input type="text" name="TGST_AMT_0" id="TGST_AMT_0" class="form-control two-digits" maxlength="15"  autocomplete="off" readonly  style="width:130px;text-align:right;"/></td>
                              <td><input type="text" name="TT_AMT_0" id="TT_AMT_0" class="form-control two-digits" maxlength="15"  autocomplete="off" readonly style="width:130px;text-align:right;" /></td>
                              <td align="center" ><button class="btn add material" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button>
                              <button class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
                          </tr>
                          <tr></tr>
                    </tbody>
                </table>
            </div>	
        </div> 
        <div id="TC" class="tab-pane fade">
            <div class="row" style="margin-top:10px;margin-left:3px;" >	
                <div class="col-lg-1 pl"><p>T&C Template</p></div>
                <div class="col-lg-2 pl">
                <input type="text" name="txtTNCID_popup" id="txtTNCID_popup" class="form-control"  autocomplete="off"  readonly/>
                  <input type="hidden" name="TNCID_REF" id="TNCID_REF" class="form-control" autocomplete="off" />
                </div>
            </div>
            <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="margin-top:10px;height:240px;width:50%;">
                <table id="example3" class="display nowrap table table-striped table-bordered itemlist" style="height:auto !important;">
                    <thead id="thead1"  style="position: sticky;top: 0">
                    <tr >
                        <th>Terms & Conditions Description<input class="form-control" type="hidden" name="Row_Count2" id ="Row_Count2"></th>
                        <th>Value / Comment</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody id="tncbody">
                    <tr  class="participantRow3">
                      <td><input type="text" name="popupTNCDID_0" id="popupTNCDID_0" class="form-control"  autocomplete="off"  readonly/></td>
                      <td hidden><input type="hidden" name="TNCDID_REF_0" id="TNCDID_REF_0" class="form-control" autocomplete="off" /></td>
                      <td hidden><input type="hidden" name="TNCismandatory_0" id="TNCismandatory_0" class="form-control" autocomplete="off" /></td>
                      <td id="tdinputid_0">
                      {{-- dynamic input --}} 
                      </td>
                        <td align="center" ><button class="btn add TNC" title="add" data-toggle="tooltip"  type="button" disabled><i class="fa fa-plus"></i></button><button class="btn remove DTNC" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
                    </tr>
                <tr></tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div id="CT" class="tab-pane fade">
            <div class="row" style="margin-top:10px;margin-left:3px;" >	
                <div class="col-lg-2 pl"><p>Calculation Template</p></div>
                <div class="col-lg-2 pl">
                <input type="text" name="txtCTID_popup" id="txtCTID_popup" class="form-control"  autocomplete="off"  readonly/>
                  <input type="hidden" name="CTID_REF" id="CTID_REF" class="form-control" autocomplete="off" />
                </div>
            </div>
            <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:240px;" >
                <table id="example5" class="display nowrap table table-striped table-bordered itemlist " width="100%" style="height:auto !important;">
                  <thead id="thead1"  style="position: sticky;top: 0">
                      <tr>
                          <th>Calculation Component<input class="form-control" type="hidden" name="Row_Count3" id ="Row_Count3"></th>
                          <th>Rate</th>
                          <th>Value</th>
                          <th>GST Applicable</th>
                          <th>IGST Rate</th>
                          <th>IGST Amount</th>
                          <th>CGST Rate</th>
                          <th>CGST Amount</th>
                          <th>SGST Rate</th>
                          <th>SGST Amount</th>
                          <th>Total GST Amount</th>
                          <th>As per Actual</th>
                          <th width="8%">Action</th>
                      </tr>
                  </thead>
                  <tbody id="tbody_ctid">
                      <tr  class="participantRow5">
                          <td><input type="text" name="popupTID_0" id="popupTID_0" class="form-control"  autocomplete="off"  readonly/></td>
                          <td hidden><input type="hidden" name="TID_REF_0" id="TID_REF_0" class="form-control" autocomplete="off" /></td>
                          <td><input type="text" name="RATE_0" id="RATE_0" class="form-control four-digits" maxlength="8" autocomplete="off"  readonly/></td>
                          <td hidden><input type="hidden" name="BASIS_0" id="BASIS_0" class="form-control" autocomplete="off" /></td>
                          <td hidden><input type="hidden" name="SQNO_0" id="SQNO_0" class="form-control" autocomplete="off" /></td>
                          <td><input type="text" name="VALUE_0" id="VALUE_0" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                          <td style="text-align:center;" ><input type="checkbox" class="filter-none" name="calGST_0" id="calGST_0" value="" ></td>
                          <td><input type="text" name="calIGST_0" id="calIGST_0" class="form-control four-digits" maxlength="8" autocomplete="off"  readonly/></td>
                          <td><input type="text" name="AMTIGST_0" id="AMTIGST_0" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                          <td><input type="text" name="calCGST_0" id="calCGST_0" class="form-control four-digits" maxlength="8" autocomplete="off"  readonly/></td>
                          <td><input type="text" name="AMTCGST_0" id="AMTCGST_0" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                          <td><input type="text" name="calSGST_0" id="calSGST_0" class="form-control four-digits" maxlength="8" autocomplete="off"  readonly/></td>
                          <td><input type="text" name="AMTSGST_0" id="AMTSGST_0" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                          <td><input type="text" name="TOTGSTAMT_0" id="TOTGSTAMT_0" class="form-control two-digits"  maxlength="15" autocomplete="off"  readonly/></td>
                          <td style="text-align:center;"><input type="checkbox" class="filter-none" name="calACTUAL_0" id="calACTUAL_0" value=""   ></td>
                          <td align="center" ><button class="btn add" title="add" data-toggle="tooltip" type="button" disabled><i class="fa fa-plus"></i></button><button class="btn remove" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
                      </tr>
                      <tr></tr>
                  </tbody>
                </table>
            </div>	
        </div>
        
        <div id="udf" class="tab-pane fade">
            <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="margin-top:10px;height:280px;width:50%;">
                <table id="example4" class="display nowrap table table-striped table-bordered itemlist" style="height:auto !important;">
                    <thead id="thead1"  style="position: sticky;top: 0">
                    <tr >
                        <th>UDF Fields<input class="form-control" type="hidden" name="Row_Count6" id ="Row_Count6"></th>
                        <th>Value / Comments</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($objUdfPBData)
                    @foreach($objUdfPBData as $uindex=>$uRow)
                      <tr  class="participantRow4">
                          <td><input type="text" name={{"popupUDFJWIID_".$uindex}} id={{"popupUDFJWIID_".$uindex}} class="form-control" value="{{$uRow->LABEL}}" autocomplete="off"  readonly/></td>
                          <td hidden><input type="hidden" name={{"UDFJWIID_REF_".$uindex}} id={{"UDFJWIID_REF_".$uindex}} class="form-control" value="{{$uRow->UDFJWIID}}" autocomplete="off"   /></td>
                          <td hidden><input type="hidden" name={{"UDFismandatory_".$uindex}} id={{"UDFismandatory_".$uindex}} value="{{$uRow->ISMANDATORY}}" class="form-control"   autocomplete="off" /></td>
                          <td id={{"udfinputid_".$uindex}} >
                          </td>
                          <td align="center" ><button class="btn add UDF" title="add" data-toggle="tooltip" type="button" disabled><i class="fa fa-plus"></i></button><button class="btn remove DUDF" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>                          
                      </tr>
                      <tr></tr>
                    @endforeach
                    @else
                      <tr  class="participantRow4">
                          <td><input type="text" name="popupUDFJWIID_0" id="popupUDFJWIID_0" class="form-control" value="" autocomplete="off"  readonly/></td>
                          <td hidden><input type="hidden" name="UDFJWIID_REF_0" id="UDFJWIID_REF_0" class="form-control" value="" autocomplete="off"   /></td>
                          <td hidden><input type="hidden" name="UDFismandatory_0" id="UDFismandatory_0" value="" class="form-control"   autocomplete="off" /></td>
                          <td id="udfinputid_0" >
                          </td>
                          <td align="center" ><button class="btn add UDF" title="add" data-toggle="tooltip" type="button" disabled><i class="fa fa-plus"></i></button><button class="btn remove DUDF" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>                          
                      </tr>
                      <tr></tr>
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
@endsection
@section('alert')


<!-- DEPID Dropdown -->
<div id="depidpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='dep_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Department</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="CodeTable" class="display nowrap table  table-striped table-bordered">
      <thead>
          <tr>
      <th class="ROW1">Select</th> 
      <th class="ROW2">Code</th>
      <th class="ROW3">Name</th>
    </tr>
      </thead>
      <tbody>

        <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="codesearch" class="form-control" autocomplete="off" onkeyup="CodeFunction()"></td>
        <td class="ROW3"><input type="text" id="namesearch" class="form-control" autocomplete="off" onkeyup="NameFunction()"></td>
      </tr>
      </tbody>
    </table>
    <table id="CodeTable2" class="display nowrap table  table-striped table-bordered" >
      <thead id="thead2">
      </thead>
      <tbody>
      @foreach ($objdepartment as $index=>$clRow)
        <tr >
          <td class="ROW1"> <input type="checkbox" name="SELECT_DEPID_REF[]" id="depidcode_{{ $index }}" class="clsdepid" value="{{ $clRow-> DEPID }}" ></td>
            <td class="ROW2">{{ $clRow-> DCODE }}
              <input type="hidden" id="txtdepidcode_{{ $index }}" data-desc="{{ $clRow-> DCODE }}" data-desc2="{{ $clRow-> NAME }}"  value="{{ $clRow-> DEPID }}"/>
            </td>
            <td class="ROW3" >{{ $clRow-> NAME }}</td>
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
<!-- DEPID Dropdown-->

<!--VENDOR dropdown-->

<div id="vendoridpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='vendor_close_popup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Vendor Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="VendorCodeTable" class="display nowrap table  table-striped table-bordered" >
    <thead>
    <tr>
      <th class="ROW1">Select</th> 
      <th class="ROW2">Code</th>
      <th class="ROW3">Description</th>
    </tr>
    </thead>
    <tbody>
    

    <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="vendorcodesearch" class="form-control" autocomplete="off" onkeyup="VendorCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="vendornamesearch" class="form-control" autocomplete="off" onkeyup="VendorNameFunction()"></td>
      </tr>
    </tbody>
    </table>
      <table id="VendorCodeTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2"> 
        </thead>
        <tbody id="tbody_vendor" >
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!--VENDOR dropdown-->

<!-- Bill To Dropdown -->
<div id="BillTopopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='BillToclosePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Bill To</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="BillToTable" class="display nowrap table  table-striped table-bordered" >
    <thead>

    <tr>
      <th class="ROW1">Select</th> 
      <th class="ROW2">Name</th>
      <th class="ROW3">Address</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="BillTocodesearch" class="form-control" autocomplete="off" onkeyup="BillToCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="BillTonamesearch" class="form-control" autocomplete="off" onkeyup="BillToNameFunction()"></td>
      </tr>
    </tbody>
    </table>
      <table id="BillToTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2">
         
        </thead>
        <tbody id="tbody_BillTo">
       
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Bill To Dropdown-->

<!-- Ship To Dropdown -->
<div id="ShipTopopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='ShipToclosePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Ship To</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="ShipToTable" class="display nowrap table  table-striped table-bordered" >
    <thead>
    <tr>
      <th class="ROW1">Select</th> 
      <th class="ROW2">Name</th>
      <th class="ROW3">Address</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="ShipTocodesearch" class="form-control" autocomplete="off" onkeyup="ShipToCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="ShipTonamesearch" class="form-control" autocomplete="off" onkeyup="ShipToNameFunction()"></td>
      </tr>
    </tbody>
    </table>
      <table id="ShipToTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2">
         
        </thead>
        <tbody id="tbody_ShipTo">
       
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Ship To Dropdown-->

<!-- TNC Header Dropdown -->
<div id="TNCIDpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='TNCID_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>T&C</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="TNCIDTable" class="display nowrap table  table-striped table-bordered" >
    <thead>

    <tr>
      <th class="ROW1">Select</th> 
      <th class="ROW2">Code</th>
      <th class="ROW3">Name</th>
    </tr>
    </thead>
    <tbody>

    <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="TNCcodesearch" class="form-control" onkeyup="TNCCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="TNCnamesearch" class="form-control" onkeyup="TNCNameFunction()"></td>
      </tr>

    </tbody>
    </table>
      <table id="TNCIDTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2">
         
        </thead>
        <tbody>
        @foreach ($objTNCHeader as $tncindex=>$tncRow)
        <tr >
          <td class="ROW1"> <input type="checkbox" name="SELECT_TNCID_REF[]" id="tncidcode_{{ $tncindex }}" class="clstncid" value="{{ $tncRow-> TNCID }}" ></td>
          <td class="ROW2">{{ $tncRow-> TNC_CODE }}
          <input type="hidden" id="txttncidcode_{{ $tncindex }}" data-desc="{{ $tncRow-> TNC_CODE }} - {{ $tncRow-> TNC_DESC }}"  
          value="{{ $tncRow-> TNCID }}"/></td><td class="ROW3" >{{ $tncRow-> TNC_DESC }}</td>
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
<!-- TNC Header Dropdown-->

<!-- TNC Details Dropdown -->
<div id="tncdetpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='tncdet_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Terms & Condition Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="TNCDetTable" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
    <tr>
            <th>TNC Name</th>
            <th>Value Type</th>
    </tr>
    <tr hidden>
            <input type="hidden" name="fieldid" id="hdn_tncdet"/>
    </tr>
    </thead>
    <tbody>
    <tr>
    <td>
    <input type="text" id="tncdetcodesearch" onkeyup="TNCDetCodeFunction()">
    </td>
    <td>
    <input type="text" id="tncdetnamesearch" onkeyup="TNCDetNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="TNCDetTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          
        </thead>
        <tbody id="tbody_tncdetails">       
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- TNC Details Dropdown-->

<!-- Calculation Header Dropdown -->
<div id="CTIDpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='CTID_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Calculation Template</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="CTIDTable" class="display nowrap table  table-striped table-bordered" >
    <thead>

    <tr>
      <th class="ROW1">Select</th> 
      <th class="ROW2">Code</th>
      <th class="ROW3">Name</th>
    </tr>

    </thead>
    <tbody>


    <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="CTIDcodesearch" class="form-control" onkeyup="CTIDCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="CTIDnamesearch" class="form-control" onkeyup="CTIDNameFunction()"></td>
      </tr>

    </tbody>
    </table>
      <table id="CTIDTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2">
         
        </thead>
        <tbody>
        @foreach ($objCalculationHeader as $calindex=>$calRow)
        <tr >
        <td class="ROW1"> <input type="checkbox" name="SELECT_CTID_REF[]" id="CTIDcode_{{ $calindex }}" class="clsctid" value="{{ $calRow-> CTID }}" ></td>
          <td class="ROW2">{{ $calRow-> CTCODE }}
          <input type="hidden" id="txtCTIDcode_{{ $calindex }}" data-desc="{{ $calRow-> CTCODE }} - {{ $calRow-> CTDESCRIPTION }}"  
          value="{{ $calRow-> CTID }}"/></td><td class="ROW3" >{{ $calRow-> CTDESCRIPTION }}</td>
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
<!-- Calculation Header Dropdown-->

<!-- Calculation Details Dropdown -->
<div id="ctiddetpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='ctiddet_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Terms & Condition Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="CTIDDetTable" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
    <tr>
            <th>Component</th>
            <th>Basis</th>
            <th>Rate</th>
            <th>Amount</th>
            <th>Formula</th>
    </tr>
    <tr hidden>
            <input type="hidden" name="fieldid" id="hdn_ctiddet"/>
    </tr>
    </thead>
    <tbody>
    <tr>
    <td>
    <input type="text" id="CTIDdetcodesearch" onkeyup="CTIDDetCodeFunction()">
    </td>
    <td>
    <input type="text" id="CTIDdetnamesearch" onkeyup="CTIDDetNameFunction()">
    </td>
    <td>
    <input type="text" id="CTIDdetratesearch" onkeyup="CTIDDetRateFunction()">
    </td>
    <td>
    <input type="text" id="CTIDdetamountsearch" onkeyup="CTIDDetAmountFunction()">
    </td>
    <td>
    <input type="text" id="CTIDdetformulasearch" onkeyup="CTIDDetFormulaFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="CTIDDetTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          
        </thead>
        <tbody id="tbody_ctiddetails">       
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Calculation Details Dropdown-->

<!-- Good Receipt Dropdown -->
<div id="GRNpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='GRN_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>GRN No</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="GRNTable" class="display nowrap table  table-striped table-bordered" >
    <thead>
          <tr id="none-select" class="searchalldata" hidden>
            
            <td> <input type="hidden" name="hdn_GRN" id="hdn_GRN"/>
            <input type="hidden" name="hdn_GRN2" id="hdn_GRN2"/></td>
          </tr>

    <tr>
      <th class="ROW1">Select</th> 
      <th class="ROW2">GRN No</th>
      <th class="ROW3">GRN Date</th>
    </tr>
    </thead>
    <tbody>
    
    <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="GRNcodesearch" class="form-control" autocomplete="off" onkeyup="GRNCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="GRNnamesearch" class="form-control" autocomplete="off" onkeyup="GRNNameFunction()"></td>
      </tr>

    </tbody>
    </table>
      <table id="GRNTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2">

        </thead>
        <tbody id="tbody_GRN">     
        
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Good Receipt Dropdown-->

<!-- Item Code Dropdown -->
<div id="ITEMIDpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:90%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='ITEMID_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Item Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="ItemIDTable" class="display nowrap table  table-striped table-bordered" style="width:100%" >
    <thead>
      <tr id="none-select" class="searchalldata" hidden>
            
            <td> 
                <input type="hidden" name="hdn_ItemID" id="hdn_ItemID"/>
                <input type="hidden" name="hdn_ItemID2" id="hdn_ItemID2"/>
                <input type="hidden" name="hdn_ItemID3" id="hdn_ItemID3"/>
                <input type="hidden" name="hdn_ItemID4" id="hdn_ItemID4"/>
                <input type="hidden" name="hdn_ItemID5" id="hdn_ItemID5"/>
                <input type="hidden" name="hdn_ItemID6" id="hdn_ItemID6"/>
                <input type="hidden" name="hdn_ItemID7" id="hdn_ItemID7"/>
                <input type="hidden" name="hdn_ItemID8" id="hdn_ItemID8"/>
                <input type="hidden" name="hdn_ItemID9" id="hdn_ItemID9"/>
                <input type="hidden" name="hdn_ItemID10" id="hdn_ItemID10"/>
                <input type="hidden" name="hdn_ItemID11" id="hdn_ItemID11"/>
                <input type="hidden" name="hdn_ItemID12" id="hdn_ItemID12"/>
                <input type="hidden" name="hdn_ItemID13" id="hdn_ItemID13"/>
                <input type="hidden" name="hdn_ItemID14" id="hdn_ItemID14"/>
                <input type="hidden" name="hdn_ItemID15" id="hdn_ItemID15"/>
                <input type="hidden" name="hdn_ItemID16" id="hdn_ItemID16"/>
                <input type="hidden" name="hdn_ItemID17" id="hdn_ItemID17"/>
                <input type="hidden" name="hdn_ItemID18" id="hdn_ItemID18"/>
                <input type="hidden" name="hdn_ItemID19" id="hdn_ItemID19"/>
                <input type="hidden" name="hdn_ItemID20" id="hdn_ItemID20"/>
                <input type="hidden" name="hdn_ItemID21" id="hdn_ItemID21" value="0"/>
                <input type="hidden" name="hdn_ItemID22" id="hdn_ItemID22"/>
                <input type="hidden" name="hdn_ItemID23" id="hdn_ItemID23"/>
                <input type="hidden" name="hdn_ItemID24" id="hdn_ItemID24"/>
                <input type="hidden" name="hdn_ItemID25" id="hdn_ItemID25"/>
                <input type="hidden" name="hdn_ItemID26" id="hdn_ItemID26"/>
                <input type="hidden" name="hdn_ItemID27" id="hdn_ItemID27"/>
            </td>
      </tr>
      <!-- <tr class="topnav"><button class="btn topnavbt" id="btnOk"><i class="fa fa-check"></i> OK</button></tr> -->
      <tr>
            <th style="width:8%;" id="all-check" >Select</th>
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
    <td style="width:8%;text-align:center;"><input type="checkbox" class="js-selectall" data-target=".js-selectall1" /></td>
    <td style="width:10%;">
    <input type="text" id="Itemcodesearch" class="form-control" autocomplete="off" onkeyup="ItemCodeFunction()">
    </td>
    <td style="width:10%;">
    <input type="text" id="Itemnamesearch" class="form-control" autocomplete="off" onkeyup="ItemNameFunction()">
    </td>
    <td style="width:8%;">
    <input type="text" id="ItemUOMsearch" class="form-control" autocomplete="off" onkeyup="ItemUOMFunction()">
    </td>
    <td style="width:8%;">
    <input type="text" id="ItemQTYsearch" class="form-control" autocomplete="off" onkeyup="ItemQTYFunction()">
    </td>
    <td style="width:8%;">
    <input type="text" id="ItemGroupsearch" class="form-control" autocomplete="off" onkeyup="ItemGroupFunction()">
    </td>
    <td style="width:8%;">
    <input type="text" id="ItemCategorysearch" class="form-control" autocomplete="off" onkeyup="ItemCategoryFunction()">
    </td>
    <td style="width:8%;">
    <input type="text" id="ItemBUsearch" class="form-control" autocomplete="off" onkeyup="ItemBUFunction()">
    </td>
    <td style="width:8%;" {{$AlpsStatus['hidden']}} >
    <input type="text" id="ItemAPNsearch" class="form-control" autocomplete="off" onkeyup="ItemAPNFunction()">
    </td>
    <td style="width:8%;" {{$AlpsStatus['hidden']}} >
    <input type="text" id="ItemCPNsearch" class="form-control" autocomplete="off" onkeyup="ItemCPNFunction()">
    </td>
    <td style="width:8%;" {{$AlpsStatus['hidden']}} >
    <input type="text" id="ItemOEMPNsearch" class="form-control" autocomplete="off" onkeyup="ItemOEMPNFunction()">
    </td>
    <td style="width:8%;">
    <input type="text" id="ItemStatussearch" class="form-control" autocomplete="off" onkeyup="ItemStatusFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="ItemIDTable2" class="display nowrap table  table-striped table-bordered" style="width:100%" >
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
.single button {
    background: #eff7fb;
    width: 30px;
    border: 1px;
    padding: 10px 0;
    margin: 5px 0;
    text-align: center;
    font-weight: bold;
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
    width: 18%;
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

//------------------------
  //Department Account
    let cltid = "#CodeTable2";
    let cltid2 = "#CodeTable";
    let clheaders = document.querySelectorAll(cltid2 + " th");

      // Sort the table element when clicking on the table headers
      clheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(cltid, ".clsclid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function CodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("codesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("CodeTable2");
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

  function NameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("namesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("CodeTable2");
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

  $('#txtdepartment').on('click',function(event){
    showSelectedCheck($("#DEPID_REF").val(),"SELECT_DEPID_REF");
    $("#depidpopup").show();    
    event.preventDefault();
  });

  $("#dep_closePopup").click(function(event){
    $("#depidpopup").hide();
    event.preventDefault();
  });

  $(".clsdepid").click(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc")+'-'+$("#txt"+fieldid+"").data("desc2"); 
        $('#txtdepartment').val(texdesc);
        $('#DEPID_REF').val(txtval);
        $("#depidpopup").hide();
        $("#codesearch").val(''); 
        $("#namesearch").val(''); 
        event.preventDefault();
    });
  //Department Account Ends
//------------------------

//------------------------
  // START VENDOR CODE FUNCTION
let vendor_tid = "#VendorCodeTable2";
let vendor_tid2 = "#VendorCodeTable";
let vendor_headers = document.querySelectorAll(vendor_tid2 + " th");

      
vendor_headers.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(vendor_tid, ".clsvendorid", "td:nth-child(" + (i + 1) + ")");
  });
});

function VendorCodeFunction() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("vendorcodesearch");
    filter = input.value.toUpperCase();
    if(filter.length == 0)
    {
      var CODE = ''; 
      var NAME = ''; 
      loadVendor(CODE,NAME); 
    }
    else if(filter.length >= 3)
    {
      var CODE = filter; 
      var NAME = ''; 
      loadVendor(CODE,NAME); 
    }
    else
    {
      table = document.getElementById("VendorCodeTable2");
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

function VendorNameFunction() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("vendornamesearch");
    filter = input.value.toUpperCase();
    if(filter.length == 0)
    {
      var CODE = ''; 
      var NAME = ''; 
      loadVendor(CODE,NAME);
    }
    else if(filter.length >= 3)
    {
      var CODE = ''; 
      var NAME = filter; 
      loadVendor(CODE,NAME);  
    }
    else
    {
      table = document.getElementById("VendorCodeTable2");
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

function loadVendor(CODE,NAME){
   
  $("#tbody_vendor").html('');
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $.ajax({
    url:'{{route("transaction",[$FormId,"getVendor"])}}',
    type:'POST',
    data:{'CODE':CODE,'NAME':NAME},
    success:function(data) {
      $("#tbody_vendor").html(data); 
      bindVendorEvents();
      showSelectedCheck($("#VID_REF").val(),"SELECT_VID_REF"); 
    },
    error:function(data){
    console.log("Error: Something went wrong.");
    $("#tbody_vendor").html('');                        
    },
  });
}

$('#txtvendor').click(function(event){
  

  var CODE = ''; 
  var NAME = ''; 
  loadVendor(CODE,NAME);  

  $("#vendoridpopup").show();
  event.preventDefault();
});

$("#vendor_close_popup").click(function(event){
  $("#vendoridpopup").hide();
  event.preventDefault();
}); 



function bindVendorEvents(){

  $(".clsvendorid").click(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");
        var oldVID_REF =   $("#VID_REF").val();
        var MaterialClone = $('#hdnMaterial').val();   
      
        var TNCClone = $('#hdnTNC').val();   
        var CalculationClone = $('#hdnCalculation').val();   
      
        $('#txtvendor').val(texdesc);
        $('#VID_REF').val(txtval);
        if (txtval != oldVID_REF)
        {
            $('#Material').html(MaterialClone);
           
            $('#TC').html(TNCClone);
            $('#CT').html(CalculationClone);
           
            $('#tot_amt').val('0.00');
            $('#Row_Count1').val('1');
            $('#Row_Count2').val('1');
            $('#Row_Count3').val('1');
           
        }
        $("#vendoridpopup").hide();
        $("#vendorcodesearch").val(''); 
        $("#vendornamesearch").val(''); 
       
        var customid = txtval;
              if(customid!=''){
                $("#Credit_days").val('');
                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  });
                  $.ajax({
                      url:'{{route("transaction",[$FormId,"getcreditdays"])}}',
                      type:'POST',
                      data:{'id':customid},
                      success:function(data) {
                        $("#Credit_days").val(data);     
                        
                        var d = data; 
                        d = parseInt(d) - 1;
                        var pdate =$('#JWIDT').val();
                        var ddate = new Date(pdate);
                        var newddate = new Date(ddate);
                        newddate.setDate(newddate.getDate() + d);
                        var piddate = newddate.getFullYear() + "-" + ("0" + (newddate.getMonth() + 1)).slice(-2) + "-" + ('0' + newddate.getDate()).slice(-2) ;
                        $("#DUE_DATE").val(piddate);
                        
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                        $("#Credit_days").val('');   
                        $("#DUE_DATE").val('');                      
                      },
                  }); 
                $("#txtBILLTO").val('');
                $("#BILLTO").val('');
                $("#txtBILLTO1").val('');
                $("#BILLTO1").val('');
                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  });
                  $.ajax({
                      url:'{{route("transaction",[$FormId,"getBillTo"])}}',
                      type:'POST',
                      data:{'id':customid},
                      success:function(data) {
                        $("#txtBILLTO1").hide();
                        $("#div_billto").html(data);
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                        $("#txtBILLTO").hide();
                        $("#txtBILLTO1").show();
                      },
                  });  

                $("#txtSHIPTO").val('');
                $("#SHIPTO").val('');
                $("#txtSHIPTO1").val('');
                $("#SHIPTO1").val('');
                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  });
                  $.ajax({
                      url:'{{route("transaction",[$FormId,"getShipTo"])}}',
                      type:'POST',
                      data:{'id':customid},
                      success:function(data) {
                        $("#txtSHIPTO1").hide();
                        $("#div_shipto").html(data);
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                        $("#txtSHIPTO").hide();
                        $("#txtSHIPTO1").show();
                      },
                  });  
                  $("#tbody_BillTo").html('');
                  $.ajax({
                      url:'{{route("transaction",[$FormId,"getBillAddress"])}}',
                      type:'POST',
                      data:{'id':customid},
                      success:function(data) {
                        $("#tbody_BillTo").html(data);
                        BindBillAddress();
                        showSelectedCheck($("#BILLTO").val(),"SELECT_BILLTO");
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                        $("#tbody_BillTo").html('');
                      },
                  });   
                  $("#tbody_ShipTo").html('');
                  $.ajax({
                      url:'{{route("transaction",[$FormId,"getShipAddress"])}}',
                      type:'POST',
                      data:{'id':customid},
                      success:function(data) {
                        $("#tbody_ShipTo").html(data);       
                        BindShipAddress();  
                        showSelectedCheck($("#SHIPTO").val(),"SELECT_SHIPTO");
                                       
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                        $("#tbody_ShipTo").html('');
                      },
                  });
                  
              }
              event.preventDefault();
    });

}




  
  //Vendor Account Ends
//------------------------

//------------------------
  //Goods Receipt Note Dropdown
  let grntid = "#GRNTable2";
      let grntid2 = "#GRNTable";
      let GRNheaders = document.querySelectorAll(grntid2 + " th");

      // Sort the table element when clicking on the table headers
      GRNheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(grntid, ".clsgrnid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function GRNCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("GRNcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("GRNTable2");
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

  function GRNNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("GRNnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("GRNTable2");
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

  $('#Material').on('click','[id*="txtGRN_NO_"]',function(event){
       
        var VID_REF = $.trim($('#VID_REF').val());
        var DEPID_REF = $.trim($('#DEPID_REF').val());


        if(DEPID_REF ===""){
              $("#FocusId").val('txtdepartment');        
              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn1").show();
              $("#AlertMessage").text('Please Select Department.');
              $("#alert").modal('show');
              $("#OkBtn1").focus();
            return false;
        }
        else if(VID_REF ===""){
              $("#FocusId").val('txtvendor');        
              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn1").show();
              $("#AlertMessage").text('Please Select Vender.');
              $("#alert").modal('show');
              $("#OkBtn1").focus();
            return false;
        }
        else{


          var customid = $('#VID_REF').val();
          var fieldid = $(this).parent().parent().find('[id*="GRJID_REF"]').attr('id');

          $("#tbody_GRN").html('');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })
            $.ajax({
                url:'{{route("transaction",[$FormId,"getgoodsreceiptnote"])}}',
                type:'POST',
                data:{'id':customid,'fieldid':fieldid},
                success:function(data) {
                  $("#tbody_GRN").html(data);
                  BindGoodsReceiptNote();
                  showSelectedCheck($("#"+fieldid).val(),"SELECT_"+fieldid);
                },
                error:function(data){
                  console.log("Error: Something went wrong.");
                  $("#tbody_GRN").html('');
                },
            });

            $("#GRNpopup").show();
            var id = $(this).attr('id');
            var id2 = $(this).parent().parent().find('[id*="GRJID_REF"]').attr('id');

            $('#hdn_GRN').val(id);
            $('#hdn_GRN2').val(id2);

        }



    });

    $("#GRN_closePopup").click(function(event){
      $("#GRNpopup").hide();
    });
      
    function BindGoodsReceiptNote(){
      $(".clsgrnid").click(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");
        
        var txtid= $('#hdn_GRN').val();
        var txt_id2= $('#hdn_GRN2').val();

        $('#'+txtid).val(texdesc);
        $('#'+txt_id2).val(txtval);
        $("#GRNpopup").hide();
        
        $("#GRNcodesearch").val(''); 
        $("#GRNnamesearch").val(''); 
       
        event.preventDefault();
      });
    }

      

  //Goods Receipt Note Dropdown Ends
//------------------------

//------------------------
  //Bill Address
  let billtoid = "#BillToTable2";
      let billtoid2 = "#BillToTable";
      let billtoheaders = document.querySelectorAll(billtoid2 + " th");

      // Sort the table element when clicking on the table headers
      billtoheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(billtoid, ".clsbillto", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function BillToCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("BillTocodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("BillToTable2");
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

  function BillToNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("BillTonamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("BillToTable2");
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
  $('#div_billto').on('click','#txtBILLTO',function(event){
    
         $("#BillTopopup").show();
         event.preventDefault();
      });

      $("#BillToclosePopup").click(function(event){
        $("#BillTopopup").hide();
        event.preventDefault();
      });

      function BindBillAddress(){
        $(".clsbillto").click(function(){
          var fieldid = $(this).attr('id');
          var txtval =    $("#txt"+fieldid+"").val();
          var texdesc =   $("#txt"+fieldid+"").data("desc");
          $('#txtBILLTO').val(texdesc);
          $('#BILLTO').val(txtval);
          $("#BillTopopup").hide();
          $("#BillTocodesearch").val(''); 
          $("#BillTonamesearch").val(''); 
           
          event.preventDefault();
        });
      }
  //Bill Address Ends
//------------------------

//------------------------
  //Ship Address
  let shiptoid = "#ShipToTable2";
      let shiptoid2 = "#ShipToTable";
      let shiptoheaders = document.querySelectorAll(shiptoid2 + " th");

      // Sort the table element when clicking on the table headers
      shiptoheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(shiptoid, ".clsshipto", "td:nth-child(" + (i + 1) + ")");
        });
      });

  function ShipToCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("ShipTocodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("ShipToTable2");
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

  function ShipToNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("ShipTonamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("ShipToTable2");
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

  $('#div_shipto').on('click','#txtSHIPTO',function(event){
         $("#ShipTopopup").show();
         event.preventDefault();
      });

      $("#ShipToclosePopup").click(function(event){
        $("#ShipTopopup").hide();
        event.preventDefault();
      });

      function BindShipAddress(){
        $(".clsshipto").click(function(){
          var fieldid = $(this).attr('id');
          var txtval =    $("#txt"+fieldid+"").val();
          var texdesc =   $(this).children('[id*="txtshipadd"]').text();
          var taxstate =  $("#txt"+fieldid+"").data("desc");
          var oldShipto =   $("#SHIPTO").val();
          var MaterialClone = $('#hdnMaterial').val();   
         
          var TNCClone = $('#hdnTNC').val();   
          var CalculationClone = $('#hdnCalculation').val();   
         

          if (txtval != oldShipto)
          {
            $('#Material').html(MaterialClone);
           
            $('#TC').html(TNCClone);
            $('#CT').html(CalculationClone);
           
            $('#tot_amt').val('0.00');
            $('#Row_Count1').val('1');
            $('#Row_Count2').val('1');
            $('#Row_Count3').val('1');
           
          }
          $('#txtSHIPTO').val(texdesc);
          $('#SHIPTO').val(txtval);
          $('#Tax_State').val(taxstate);
          $("#ShipTopopup").hide();
          $("#ShipTocodesearch").val(''); 
          $("#ShipTonamesearch").val(''); 
         
          event.preventDefault();
        });
      }
  //Ship Address Ends
//------------------------

//------------------------
  //TNC Header
  let tnctid = "#TNCIDTable2";
      let tnctid2 = "#TNCIDTable";
      let tncheaders = document.querySelectorAll(tnctid2 + " th");

      // Sort the table element when clicking on the table headers
      tncheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(tnctid, ".clstncid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function TNCCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("TNCcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("TNCIDTable2");
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

  function TNCNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("TNCnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("TNCIDTable2");
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
  $('#TC').on('click','#txtTNCID_popup',function(event){
    showSelectedCheck($("#TNCID_REF").val(),"SELECT_TNCID_REF");
         $("#TNCIDpopup").show();
         event.preventDefault();
      });

      $("#TNCID_closePopup").click(function(event){
        $("#TNCIDpopup").hide();
      });

      $(".clstncid").click(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");
        
        $('#txtTNCID_popup').val(texdesc);
        $('#TNCID_REF').val(txtval);
        $("#TNCIDpopup").hide();
        $("#TNCcodesearch").val(''); 
        $("#TNCnamesearch").val(''); 
        TNCCodeFunction();
       
        var customid = txtval;
        if(customid!=''){
          
          $('#tbody_tncdetails').html('<tr><td colspan="2">Please wait..</td></tr>');
          

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url:'{{route("transaction",[$FormId,"gettncdetails2"])}}',
                type:'POST',
                data:{'id':customid},
                success:function(data) {
                    $('#tncbody').html(data);
                    bindTNCDetailsEvents();
                },
                error:function(data){
                  console.log("Error: Something went wrong.");
                  $('#tncbody').html('');
                },
            });            
            $.ajax({
                url:'{{route("transaction",[$FormId,"gettncdetails3"])}}',
                type:'POST',
                data:{'id':customid},
                success:function(data) {
                    $('#Row_Count2').val(data);
                    bindTNCDetailsEvents();
                },
                error:function(data){
                  console.log("Error: Something went wrong.");
                  $('#Row_Count2').val('0');
                },
            });
            $.ajax({
                url:'{{route("transaction",[$FormId,"gettncdetails"])}}',
                type:'POST',
                data:{'id':customid},
                success:function(data) {
                    $('#tbody_tncdetails').html(data);
                    bindTNCDetailsEvents();
                },
                error:function(data){
                  console.log("Error: Something went wrong.");
                  $('#tbody_tncdetails').html('');
                },
            });        
        }
        event.preventDefault();
      });

      

  //TNC Header Ends
//------------------------

//TNC Details Starts
//------------------------

      let tncdettid = "#TNCDetTable2";
      let tncdettid2 = "#TNCDetTable";
      let tncdetheaders = document.querySelectorAll(tncdettid2 + " th");

      // Sort the table element when clicking on the table headers
      tncdetheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(tncdettid, ".clstncdet", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function TNCDetCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("tncdetcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("TNCDetTable2");
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

  function TNCDetNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("tncdetnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("TNCDetTable2");
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


$("#tncdet_closePopup").on("click",function(event){ 
     $("#tncdetpopup").hide();
});

function bindTNCDetailsEvents(){
        $('.clstncdet').dblclick(function(){
    
            var id = $(this).attr('id');
            var txtid =    $("#txt"+id+"").val();
            var txtname =   $("#txt"+id+"").data("desc");
            var fieldid2 = $(this).find('[id*="tncvalue"]').attr('id');
            var txtvaluetype = $.trim($(this).find('[id*="tncvalue"]').text());
            var txtismandatory =  $("#txt"+fieldid2+"").val();
            var txtdescription =  $("#txt"+fieldid2+"").data("desc");
            
            var txtcol = $('#hdn_tncdet').val();
            $("#"+txtcol).val(txtname);
            $("#"+txtcol).parent().parent().find("[id*='TNCDID_REF']").val(txtid);
            $("#"+txtcol).parent().parent().find("[id*='TNCismandatory']").val(txtismandatory);
            
            var txt_id4 = $("#"+txtcol).parent().parent().find("[id*='tdinputid']").attr('id');  //<td> id 

            var strdyn = txt_id4.split('_');
            var lastele =   strdyn[strdyn.length-1];

            var dynamicid = "tncdetvalue_"+lastele;

            var chkvaltype =  txtvaluetype.toLowerCase();
            var strinp = '';

            if(chkvaltype=='date'){

              strinp = '<input type="date" placeholder="dd/mm/yyyy" name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" /> ';       

            }else if(chkvaltype=='time'){
              strinp= '<input type="time" placeholder="h:i" name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" /> ';

            }else if(chkvaltype=='numeric'){
              strinp = '<input type="text" name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" /> ';

            }else if(chkvaltype=='text'){

              strinp = '<input type="text" name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" /> ';
            
            }else if(chkvaltype=='boolean'){

              strinp = '<input type="checkbox" name="'+dynamicid+ '" id="'+dynamicid+'" class="" /> ';
            
            }else if(chkvaltype=='combobox'){
              if(txtdescription !== undefined)
              {
                var strarray = txtdescription.split(',');
                
                var opts = '';

                for (var i = 0; i < strarray.length; i++) {
                  opts = opts + '<option value="'+strarray[i]+'">'+strarray[i]+'</option> ';
                }

                strinp = '<select name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" required>'+opts+'</select>' ;
              }
            }

            $('#'+txt_id4).html('');  
            $('#'+txt_id4).html(strinp);   //set dynamic input

            $("#tncdetpopup").hide();
            $("#tncdetcodesearch").val(''); 
            $("#tncdetnamesearch").val(''); 
            TNCDetCodeFunction();
            event.preventDefault();
            
        });
  }
//TNC Details Ends
//------------------------

//------------------------
  //Calculation Header
  let cttid = "#CTIDTable2";
      let cttid2 = "#CTIDTable";
      let ctheaders = document.querySelectorAll(cttid2 + " th");

      // Sort the table element when clicking on the table headers
      ctheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(cttid, ".clsctid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function CTIDCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("CTIDcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("CTIDTable2");
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

  function CTIDNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("CTIDnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("CTIDTable2");
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
  $('#CT').on('click','#txtCTID_popup',function(event){
    showSelectedCheck($("#CTID_REF").val(),"SELECT_CTID_REF");
         $("#CTIDpopup").show();
         event.preventDefault();
      });

      $("#CTID_closePopup").click(function(event){
        $("#CTIDpopup").hide();
      });

      $(".clsctid").click(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");
     
        
        $('#txtCTID_popup').val(texdesc);
        $('#CTID_REF').val(txtval);
        $("#CTIDpopup").hide();
        $("#CTIDcodesearch").val(''); 
        $("#CTIDnamesearch").val(''); 
        CTIDCodeFunction();
       
        var customid = txtval;
        if(customid!=''){
          
          $('#tbody_ctiddetails').html('<tr><td colspan="2">Please wait..</td></tr>');
          $('#tbody_ctid').html('<tr><td colspan="2">Please wait..</td></tr>');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url:'{{route("transaction",[$FormId,"getcalculationdetails2"])}}',
                type:'POST',
                data:{'id':customid},
                success:function(data) {
                    $('#tbody_ctid').html(data);
                    bindCTIDDetailsEvents();
                    bindGSTCalTemplate();
                },
                error:function(data){
                  console.log("Error: Something went wrong.");
                  $('#tbody_ctid').html('');
                },
            });
            $.ajax({
                url:'{{route("transaction",[$FormId,"getcalculationdetails3"])}}',
                type:'POST',
                data:{'id':customid},
                success:function(data) {
                  $('#Row_Count3').val(data);
                    bindCTIDDetailsEvents();
                },
                error:function(data){
                  console.log("Error: Something went wrong.");
                  $('#Row_Count3').val('0');
                },
            });
            $.ajax({
                url:'{{route("transaction",[$FormId,"getcalculationdetails"])}}',
                type:'POST',
                data:{'id':customid},
                success:function(data) {
                    $('#tbody_ctiddetails').html(data);
                    bindCTIDDetailsEvents();
                },
                error:function(data){
                  console.log("Error: Something went wrong.");
                  $('#tbody_ctiddetails').html('');
                },
            }); 
              
        }
        event.preventDefault();
      });
      function bindGSTCalTemplate(){ 
          $('#CT').find('.participantRow5').each(function()
            { 
                var basis = $(this).find('[id*="BASIS"]').val();
                var sqno = $(this).find('[id*="SQNO"]').val();
                var formula = $(this).find('[id*="FORMULA"]').val();
                var rate = $(this).find('[id*="RATE"]').val();
                var amountnet = $(this).find('[id*="VALUE"]').val();
                var netTaxableAmount = 0.00;
                var netGSTAmount = 0.00;
                var netTotalAmount = 0.00;
                var totamount = 0.00;
                var tamt = 0.00;
                var IGSTamt = 0.00;
                var CGSTamt = 0.00;
                var SGSTamt = 0.00;
                var TotGSTamt = 0.00;

                $('#Material').find('.participantRow').each(function()
                {                       
                  var TaxableAmount = $(this).find('[id*="TAX_AMT_"]').val();
                  if (!isNaN(TaxableAmount) && TaxableAmount.length !== 0) {
                    netTaxableAmount += parseFloat(TaxableAmount);
                    }                      
                  
                  var GSTAmount = $(this).find('[id*="TGST_AMT_"]').val();
                  if (!isNaN(GSTAmount) && GSTAmount.length !== 0) {
                    netGSTAmount += parseFloat(GSTAmount);
                    }
                  
                  var TotalAmount = $(this).find('[id*="TT_AMT_"]').val();
                  if (!isNaN(TotalAmount) && TotalAmount.length !== 0) {
                    netTotalAmount += parseFloat(TotalAmount);
                    }
                })
                var IGST = $('#IGST_0').val();
                var CGST = $('#CGST_0').val();
                var SGST = $('#SGST_0').val();
                
                  if(formula == '')
                  {
                    if(rate > 0)
                    { 
                      if(basis == 'Item Taxable Amount')
                      {
                        totamount = parseFloat((rate * netTaxableAmount)/100).toFixed(2);
                      }
                      if(basis == 'Item GST Amount')
                      {
                        totamount = parseFloat((rate * netGSTAmount)/100).toFixed(2);
                      }
                      if(basis == 'Amount After GST Item')
                      {
                        totamount = parseFloat((rate * netTotalAmount)/100).toFixed(2);
                      }
                    }
                    else
                    {
                      totamount = amountnet;
                    }
                  }
                  else
                  {
                    if(basis == 'Item Taxable Amount')
                    {
                      var basis1 = '( '+netTaxableAmount+' * '+rate+' ) / 100';
                      var basis2 = netTaxableAmount;
                      var rate1 = rate +' ) / 100';
                      if(formula.indexOf("BASIS*RATE") != -1){
                        var formula1 = formula.replace ("BASIS*RATE", basis1);
                        tamt = eval(formula1);
                        totamount = parseFloat((tamt * rate)/100).toFixed(2);
                      }
                      else if(formula.indexOf("BASIS") != -1){
                        var formula1 = formula.replace ("BASIS", basis2);
                        tamt = eval(formula1);
                        totamount = parseFloat((tamt * rate)/100).toFixed(2);
                      }
                      else if(formula.indexOf("RATE") != -1){
                        var formula1 = formula.replace ("RATE", rate1);
                        tamt = eval(formula1);
                        totamount = parseFloat(( tamt * rate)/100).toFixed(2);
                      }
                    }
                    if(basis == 'Item GST Amount')
                    {
                      var basis1 = '('+netGSTAmount+'*'+rate+')/100';
                      var basis2 = netGSTAmount;
                      var rate1 = rate+')/100';
                      if(formula.indexOf("BASIS*RATE") != -1){
                        var formula1 = formula.replace ("BASIS*RATE", basis1);
                        tamt = eval(formula1);
                        totamount = parseFloat((tamt * rate)/100).toFixed(2);
                      }
                      else if(formula.indexOf("BASIS") != -1){
                        var formula1 = formula.replace ("BASIS", basis2);
                        tamt = eval(formula1);
                        totamount = parseFloat((tamt * rate)/100).toFixed(2);
                      }
                      else if(formula.indexOf("RATE") != -1){
                        var formula1 = formula.replace ("RATE", rate1);
                        tamt = eval(formula1);
                        totamount = parseFloat(( tamt * rate)/100).toFixed(2);
                      }
                    }
                    if(basis == 'Amount After GST Item')
                    {
                      var basis1 = '( '+netTotalAmount+' * '+rate+' ) / 100';
                      var basis2 = netTotalAmount;
                      var rate1 = rate+' ) / 100';
                      if(formula.indexOf("BASIS*RATE") != -1){
                        var formula1 = formula.replace ("BASIS*RATE", basis1);
                        tamt = eval(formula1);
                        totamount = parseFloat((tamt * rate)/100).toFixed(2);
                      }
                      else if(formula.indexOf("BASIS") != -1){
                        var formula1 = formula.replace ("BASIS", basis2);
                        tamt = eval(formula1);
                        totamount = parseFloat((tamt * rate)/100).toFixed(2);
                      }
                      else if(formula.indexOf("RATE") != -1){
                        var formula1 = formula.replace ("RATE", rate1);
                        tamt = eval(formula1);
                        totamount = parseFloat(( tamt * rate)/100).toFixed(2);
                      }
                    }
                    
                  }
                  $(this).find('[id*="VALUE_"]').val(totamount);
                    IGSTamt = parseFloat((IGST * totamount)/100).toFixed(2);
                    CGSTamt = parseFloat((CGST * totamount)/100).toFixed(2);
                    SGSTamt = parseFloat((SGST * totamount)/100).toFixed(2);
                    TotGSTamt = parseFloat(parseFloat(IGSTamt)+parseFloat(CGSTamt)+parseFloat(SGSTamt)).toFixed(2);
                if($(this).find('[id*="calGST"]').is(":checked") != false)
                {
                  if (IGST != '')
                  {
                  $(this).find('[id*="calIGST_"]').val(IGST);
                  $(this).find('[id*="AMTIGST_"]').val(IGSTamt);
                  $(this).find('[id*="calIGST_"]').removeAttr('readonly');
                  }
                  else
                  {
                    $(this).find('[id*="calIGST_"]').val('0');
                    $(this).find('[id*="AMTIGST_"]').val('0');
                    $(this).find('[id*="calIGST_"]').prop('readonly',true);
                    
                  }
                  if (CGST != '')
                  {
                  $(this).find('[id*="calCGST_"]').val(CGST);
                  $(this).find('[id*="AMTCGST_"]').val(CGSTamt);
                  $(this).find('[id*="calCGST_"]').removeAttr('readonly');
                  }
                  else
                  {
                    $(this).find('[id*="calCGST_"]').val('0');
                    $(this).find('[id*="AMTCGST_"]').val('0');
                    $(this).find('[id*="calCGST_"]').prop('readonly',true);
                  }
                  if (SGST != '')
                  {
                  $(this).find('[id*="calSGST_"]').val(SGST);
                  $(this).find('[id*="AMTSGST_"]').val(SGSTamt);
                  $(this).find('[id*="calSGST_"]').removeAttr('readonly');
                  }
                  else
                  {
                    $(this).find('[id*="calSGST_"]').val('0');
                    $(this).find('[id*="AMTSGST_"]').val('0');
                    $(this).find('[id*="calSGST_"]').prop('readonly',true);
                  }
                  $(this).find('[id*="TOTGSTAMT_"]').val(TotGSTamt);
                }
                else
                {
                  $(this).find('[id*="calSGST_"]').val('0');
                  $(this).find('[id*="AMTSGST_"]').val('0');
                  $(this).find('[id*="calCGST_"]').val('0');
                  $(this).find('[id*="AMTCGST_"]').val('0');
                  $(this).find('[id*="calIGST_"]').val('0');
                  $(this).find('[id*="AMTIGST_"]').val('0');
                  $(this).find('[id*="TOTGSTAMT_"]').val('0');
                  $(this).find('[id*="calIGST_"]').prop('readonly',true);
                  $(this).find('[id*="calCGST_"]').prop('readonly',true);
                  $(this).find('[id*="calSGST_"]').prop('readonly',true);
                }
            });
            var totalvalue = 0.00;
            var tvalue = 0.00;
            var ctvalue = 0.00;
            var ctgstvalue = 0.00;
            var tttdsamt21 = 0.00;
              $('#Material').find('.participantRow').each(function()
              {
                  if($('#GST_Reverse').is(':checked') == true)
                  {
                    tvalue = $(this).find('[id*="TAX_AMT_"]').val();
                    totalvalue = parseFloat(totalvalue) + parseFloat(tvalue);
                    totalvalue = parseFloat(totalvalue).toFixed(2);
                  }
                  else
                  {
                    tvalue = $(this).find('[id*="TT_AMT_"]').val();
                    totalvalue = parseFloat(totalvalue) + parseFloat(tvalue);
                    totalvalue = parseFloat(totalvalue).toFixed(2);
                  }
              });
              if($('#CTID_REF').val() != '')
              {
                $('#CT').find('.participantRow5').each(function()
                {
                  ctvalue = $(this).find('[id*="VALUE"]').val();
                  ctgstvalue = $(this).find('[id*="TOTGSTAMT"]').val();
                  if($('#GST_Reverse').is(':checked') == true)
                  {
                    totalvalue = parseFloat(totalvalue) + parseFloat(ctvalue);
                    totalvalue = parseFloat(totalvalue).toFixed(2);
                  }
                  else
                  {
                    totalvalue = parseFloat(totalvalue) + parseFloat(ctvalue);
                    totalvalue = parseFloat(totalvalue) + parseFloat(ctgstvalue);
                    totalvalue = parseFloat(totalvalue).toFixed(2);
                  }
                });
              }
            
     

                $('#tot_amt').val(totalvalue);
                event.preventDefault();
        }

  //Calculation Header Ends
//------------------------

//Calculation Details Starts
//------------------------

      let ctiddettid = "#CTIDDetTable2";
      let ctiddettid2 = "#CTIDDetTable";
      let ctiddetheaders = document.querySelectorAll(ctiddettid2 + " th");

      // Sort the table element when clicking on the table headers
      ctiddetheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(ctiddettid, ".clsctiddet", "td:nth-child(" + (i + 1) + ")");
        });
      });

    function CTIDDetCodeFunction() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("CTIDdetcodesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("CTIDDetTable2");
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

    function CTIDDetNameFunction() {
          var input, filter, table, tr, td, i, txtValue;
          input = document.getElementById("CTIDdetnamesearch");
          filter = input.value.toUpperCase();
          table = document.getElementById("CTIDDetTable2");
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
    function CTIDDetRateFunction() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("CTIDdetratesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("CTIDDetTable2");
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

    function CTIDDetAmountFunction() {
          var input, filter, table, tr, td, i, txtValue;
          input = document.getElementById("CTIDdetamountsearch");
          filter = input.value.toUpperCase();
          table = document.getElementById("CTIDDetTable2");
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
    function CTIDDetFormulaFunction() {
          var input, filter, table, tr, td, i, txtValue;
          input = document.getElementById("CTIDdetformulasearch");
          filter = input.value.toUpperCase();
          table = document.getElementById("CTIDDetTable2");
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


$("#ctiddet_closePopup").on("click",function(event){ 
     $("#ctiddetpopup").hide();
});

function bindCTIDDetailsEvents(){
        $('.clsctiddet').dblclick(function(){    
            var id = $(this).attr('id');
            var txtid =    $("#txt"+id+"").val();
            var txtname =   $("#txt"+id+"").data("desc");
            var fieldid2 = $(this).find('[id*="ctidbasis"]').attr('id');
            var txtbasis = $.trim($(this).find('[id*="ctidbasis"]').text());
            var txtactual =  $("#txt"+fieldid2+"").val();
            var txtgst =  $("#txt"+fieldid2+"").data("desc");
            var fieldid3 = $(this).find('[id*="ctidformula_"]').attr('id');
            var txtrate = $.trim($(this).find('[id*="ctidformula_"]').text());
            var txtsqno =  $("#txt"+fieldid3+"").val();
            var txtformula =  $("#txt"+fieldid3+"").data("desc");
            var txtamount = $.trim($(this).find('[id*="ctidamount_"]').text());
            var txtcol = $('#hdn_ctiddet').val();
            if(intRegex.test(txtrate)){
              txtrate = (txtrate +'.00');
            }
            $("#"+txtcol).val(txtname);
            $("#"+txtcol).parent().parent().find("[id*='TID_REF']").val(txtid);
            $("#"+txtcol).parent().parent().find("[id*='RATE']").val(txtrate);
            $("#"+txtcol).parent().parent().find("[id*='BASIS']").val(txtbasis);
            
            $("#"+txtcol).parent().parent().find("[id*='FORMULA']").val(txtformula);
            $("#"+txtcol).parent().parent().find("[id*='SQNO']").val(txtsqno); 

            if(txtactual == 1)
            {
              $("#"+txtcol).parent().parent().find("[id*='ACTUAL']").prop('checked','true');
            }     
            else
            {
              $("#"+txtcol).parent().parent().find("[id*='ACTUAL']").removeAttr('checked');
            }  

            if(txtgst == 1)
            {
              $("#"+txtcol).parent().parent().find("[id*='calGST']").prop('checked','true');
              $("#"+txtcol).parent().parent().find("[id*='calIGST']").removeAttr('readonly');
              $("#"+txtcol).parent().parent().find("[id*='AMTIGST']").removeAttr('readonly');
              $("#"+txtcol).parent().parent().find("[id*='calCGST']").removeAttr('readonly');
              $("#"+txtcol).parent().parent().find("[id*='calSGST']").removeAttr('readonly');
              $("#"+txtcol).parent().parent().find("[id*='AMTCGST']").removeAttr('readonly');
              $("#"+txtcol).parent().parent().find("[id*='AMTSGST']").removeAttr('readonly');
            }     
            else
            {
              $("#"+txtcol).parent().parent().find("[id*='calGST']").removeAttr('checked');
            } 

            var totaltaxableamount = 0;
            $('#Material').find('.participantRow').each(function()
              {
                var amount1 = $(this).find('[id*="TAX_AMT_"]').val();

                totaltaxableamount += parseFloat(amount1);
              });
            if(txtrate > 0)
            {
              txtamount = 0;
              txtamount = parseFloat((totaltaxableamount*txtrate)/100).toFixed(2);
              if(intRegex.test(txtamount)){
              txtamount = (txtamount +'.00');
              }
              $("#"+txtcol).parent().parent().find("[id*='VALUE']").val(txtamount);
            }
            else
            {
              if(intRegex.test(txtamount)){
              txtamount = (txtamount +'.00');
              }
              $("#"+txtcol).parent().parent().find("[id*='VALUE']").val(txtamount);
            }
            
            $("#ctiddetpopup").hide();
            $("#CTIDdetcodesearch").val(''); 
            $("#CTIDdetnamesearch").val(''); 
            $("#CTIDdetratesearch").val(''); 
            $("#CTIDdetamountsearch").val(''); 
            $("#CTIDdetformulasearch").val(''); 
            CTIDDetCodeFunction();
            event.preventDefault();
            
        });
  }
//Calculation Details Ends
//------------------------
//------------------------
  //HSN Account
    let hsntid = "#hsnCodeTable2";
    let hsntid2 = "#hsnCodeTable";
    let hsnheaders = document.querySelectorAll(hsntid2 + " th");

      // Sort the table element when clicking on the table headers
      hsnheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(hsntid, ".clshsnid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function hsnCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("hsncodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("hsnCodeTable2");
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

  function hsnNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("hsnnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("hsnCodeTable2");
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

  $('#Material').on('focus','[id*="txtHSN"]',function(event){
    $("#hsn_popup").show();
    var id = $(this).attr('id');
    var id2 = $(this).parent().parent().find('[id*="HSNID_REF"]').attr('id');
    var id3 = $(this).parent().parent().find('[id*="IGST_"]').attr('id');
    var id4 = $(this).parent().parent().find('[id*="CGST_"]').attr('id');
    var id5 = $(this).parent().parent().find('[id*="SGST_"]').attr('id');
    $('#hdn_hsnID').val(id);
    $('#hdn_hsnID2').val(id2);
    $('#hdn_hsnID3').val(id3);
    $('#hdn_hsnID4').val(id4);
    $('#hdn_hsnID5').val(id5);
    event.preventDefault();
  });

  $("#hsn_closePopup").click(function(event){
    $("#hsn_popup").hide();
    event.preventDefault();
  });
  
  $(".clshsnid").dblclick(function(){
      var fieldid = $(this).attr('id');
      var txtid =    $("#txt"+fieldid+"").val();
      var txtval =   $("#txt"+fieldid+"").data("desc");
      var txtdesc =   $("#txt"+fieldid+"").data("desc2");

      var txt_id1= $('#hdn_hsnID').val();
      var txt_id2= $('#hdn_hsnID2').val();
      $('#'+txt_id1).val(txtval);
      $('#'+txt_id2).val(txtid);
      $("#hsn_popup").hide();
      $("#hsncodesearch").val(''); 
      $("#hsnnamesearch").val(''); 
      hsnCodeFunction(); 
      var taxstate = $('#Tax_State').val();     
      var customid = txtid;
        $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
        });
        $.ajax({
              url:'{{route("transaction",[$FormId,"gettaxCode"])}}',
              type:'POST',
              data:{'id':customid,'taxstate':taxstate},
              success:function(data) {                
                  $('#hdn_hsnID6').val(data);
              },
              error:function(data){
                console.log("Error: Something went wrong.");
              },
        });
        $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          });
          $.ajax({
              url:'{{route("transaction",[$FormId,"gettax"])}}',
              type:'POST',
              data:{'id':customid,'taxstate':taxstate},
              success:function(data) {
                if(taxstate == 'WithinState')
                {
                  var txt_id3= $('#hdn_hsnID3').val();
                  var txt_id4= $('#hdn_hsnID4').val();
                  $('#'+txt_id3).val('0.0000');
                  $('#'+txt_id3).parent().parent().find('[id*="IGST_AMT"]').val('0.00');
                  $('#'+txt_id4).val(data);
                  var taxamount = $('#'+txt_id3).parent().parent().find('[id*="TAX_AMT"]').val();
                  var amt1 = parseFloat((parseFloat(data)*parseFloat(taxamount))/100).toFixed(2);
                  $('#'+txt_id4).parent().parent().find('[id*="CGST_AMT"]').val(amt1);
                  var TaxCode1 = $('#hdn_hsnID6').val();
                    $.ajaxSetup({
                          headers: {
                              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                          }
                    });
                    $.ajax({
                          url:'{{route("transaction",[$FormId,"gettax2"])}}',
                          type:'POST',
                          data:{'id':customid,'taxstate':taxstate,'TaxCode1':TaxCode1},
                          success:function(data) {                
                                var txt_id5= $('#hdn_hsnID5').val();
                                $('#'+txt_id5).val(data);
                                var taxamount2 = $('#'+txt_id5).parent().parent().find('[id*="TAX_AMT"]').val();
                                var amt2 = parseFloat((parseFloat(data)*parseFloat(taxamount2))/100).toFixed(2);
                                $('#'+txt_id4).parent().parent().find('[id*="SGST_AMT"]').val(amt2);
                                var amt4 = $('#'+txt_id5).parent().parent().find('[id*="CGST_AMT"]').val();
                                var amt3 = parseFloat(parseFloat(taxamount2)+parseFloat(amt2)+parseFloat(amt4)).toFixed(2);
                                $('#'+txt_id4).parent().parent().find('[id*="TT_AMT"]').val(amt3);
                          },
                          error:function(data){
                            console.log("Error: Something went wrong.");
                          },
                    });
                }
                else if(taxstate == 'OutofState')
                {
                  var txt_id3= $('#hdn_hsnID3').val();
                  var txt_id4= $('#hdn_hsnID4').val();
                  var txt_id5= $('#hdn_hsnID5').val();
                  $('#'+txt_id4).val('0.0000');
                  $('#'+txt_id4).parent().parent().find('[id*="CGST_AMT"]').val('0.00');
                  $('#'+txt_id5).val('0.0000');
                  $('#'+txt_id4).parent().parent().find('[id*="SGST_AMT"]').val('0.00');
                  $('#'+txt_id3).val(data);
                  var taxamount = $('#'+txt_id3).parent().parent().find('[id*="TAX_AMT"]').val();
                  var amt1 = parseFloat((parseFloat(data)*parseFloat(taxamount))/100).toFixed(2);
                  $('#'+txt_id4).parent().parent().find('[id*="IGST_AMT"]').val(amt1);
                  var amt3 = parseFloat(parseFloat(taxamount)+parseFloat(amt1)).toFixed(2);
                  $('#'+txt_id4).parent().parent().find('[id*="TT_AMT"]').val(amt3);
                }
              },
              error:function(data){
                console.log("Error: Something went wrong.");
              },
          });
    var totalamount = 0.00;
    $('#Material').find('.participantRow').each(function()
    {
        if($(this).find('[id*="TT_AMT"]').val() != '')
        {
          var ttamt21 = $(this).find('[id*="TT_AMT"]').val();
          totalamount = parseFloat(parseFloat(totalamount)+parseFloat(ttamt21)).toFixed(2);
        }
    });

    $('#TC').find('.participantRow3').each(function()
    {
        if($(this).find('[id*="TOT_TD_AMT"]').val() != '')
        {
          var tttdsamt21 = $(this).find('[id*="TOT_TD_AMT"]').val();
          totalamount = parseFloat(parseFloat(totalamount)+parseFloat(tttdsamt21)).toFixed(2);
        }
    });
    $('#tot_amt').val(totalamount);
              
      event.preventDefault();
  });
  //HSN Account Ends
//------------------------
//------------------------
  //Item ID Dropdown
  let itemtid = "#ItemIDTable2";
      let itemtid2 = "#ItemIDTable";
      let itemtidheaders = document.querySelectorAll(itemtid2 + " th");

      // Sort the table element when clicking on the table headers
      itemtidheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(itemtid, ".clsitemid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function ItemCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Itemcodesearch");
        filter = input.value.toUpperCase();
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

      function ItemNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Itemnamesearch");
        filter = input.value.toUpperCase();
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

      function ItemUOMFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("ItemUOMsearch");
        filter = input.value.toUpperCase();
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

      function ItemCategoryFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("ItemCategorysearch");
        filter = input.value.toUpperCase();
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

      function ItemBUFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("ItemBUsearch");
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

      function ItemAPNFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("ItemAPNsearch");
        filter = input.value.toUpperCase();
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

      function ItemCPNFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("ItemCPNsearch");
        filter = input.value.toUpperCase();
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

      function ItemOEMPNFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("ItemOEMPNsearch");
        filter = input.value.toUpperCase();
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

      function ItemStatusFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("ItemStatussearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("ItemIDTable2");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
          td = tr[i].getElementsByTagName("td")[11];
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

  $('#Material').on('click','[id*="txtItem"]',function(event){
        var GRNID = $(this).parent().parent().find('[id*="GRJID_REF_"]').val();
        var txtGRN_NO = $(this).parent().parent().find('[id*="txtGRN_NO_"]').attr('id');
        var taxstate = $.trim($('#Tax_State').val());
        var VID_REF = $.trim($('#VID_REF').val());
        var DEPID_REF = $.trim($('#DEPID_REF').val());


        if(DEPID_REF ===""){
              $("#FocusId").val('txtdepartment');        
              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn1").show();
              $("#AlertMessage").text('Please Select Department.');
              $("#alert").modal('show');
              $("#OkBtn1").focus();
            return false;
        }
        else if(VID_REF ===""){
              $("#FocusId").val('txtvendor');        
              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn1").show();
              $("#AlertMessage").text('Please Select Vender.');
              $("#alert").modal('show');
              $("#OkBtn1").focus();
            return false;
        }
        else if(GRNID ===""){
              $("#FocusId").val(txtGRN_NO);        
              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn1").show();
              $("#AlertMessage").text('Please Select GRN No.');
              $("#alert").modal('show');
              $("#OkBtn1").focus();
            return false;
        }
        else{

        
                $("#tbody_ItemID").html('');
                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  });
                  $.ajax({
                      url:'{{route("transaction",[$FormId,"getItemDetailsGRNwise"])}}',
                      type:'POST',
                      data:{'id':GRNID, 'taxstate':taxstate, 'vendorid':VID_REF},
                      success:function(data) {
                        $("#tbody_ItemID").html(data);   
                        bindItemEvents(); 
                        $('.js-selectall').prop("checked", false);                    
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                        $("#tbody_ItemID").html('');                        
                      },
                  }); 

                  $("#ITEMIDpopup").show();
        }

        
        var id = $(this).attr('id');
        var id2 = $(this).parent().parent().find('[id*="ITEMID_REF"]').attr('id');
        var id3 = $(this).parent().parent().find('[id*="ItemName"]').attr('id');
        var id4 = $(this).parent().parent().find('[id*="txtUOM"]').attr('id');
        var id5 = $(this).parent().parent().find('[id*="UOMID_REF_"]').attr('id');
        var id6 = $(this).parent().parent().find('[id*="RECEIVED_QTY_"]').attr('id');
        var id7 = $(this).parent().parent().find('[id*="txtALTUOM_"]').attr('id');
        var id8 = $(this).parent().parent().find('[id*="ALT_UOMID_REF_"]').attr('id');
        var id9 = $(this).parent().parent().find('[id*="RECEIVED_ALT_QTY_"]').attr('id');
        var id10 = $(this).parent().parent().find('[id*="BILL_QTY_"]').attr('id');
        var id11 = $(this).parent().parent().find('[id*="txtBILLUOM_"]').attr('id');
        var id12 = $(this).parent().parent().find('[id*="BILL_UOMID_REF_"]').attr('id');
        var id13 = $(this).parent().parent().find('[id*="ItemDesc_"]').attr('id');
        var id14 = $(this).parent().parent().find('[id*="BILL_RATEPUOM_"]').attr('id');
        var id15 = $(this).parent().parent().find('[id*="DISC_PER_"]').attr('id');
        var id16 = $(this).parent().parent().find('[id*="DISC_AMT_"]').attr('id');

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
        $('#hdn_ItemID12').val(id12);
        $('#hdn_ItemID13').val(id13);
        $('#hdn_ItemID14').val(id14);
        $('#hdn_ItemID15').val(id15);
        $('#hdn_ItemID16').val(id16);

      });

      $("#ITEMID_closePopup").click(function(event){
        $("#ITEMIDpopup").hide();
        $('.js-selectall').prop("checked", false);
      });



function bindItemEvents(){

  $('#ItemIDTable2').off(); 

  $('.js-selectall').change(function(){
        var isChecked = $(this).prop("checked");
        var selector = $(this).data('target');
        $(selector).prop("checked", isChecked);
        
        
        $('#ItemIDTable2').find('.clsitemid').each(function(){
          var fieldid = $(this).attr('id');
          var txtval =   $("#txt"+fieldid+"").val();
          var txtcode =  $("#txt"+fieldid+"").data("desc");
          var fieldid2 = $(this).find('[id*="itemname"]').attr('id');
          var txtname =  $("#txt"+fieldid2+"").val();
          var txtspec =  $("#txt"+fieldid2+"").data("desc");
          var fieldid3 = $(this).find('[id*="itemuom"]').attr('id');
          var txtmuomid =  $("#txt"+fieldid3+"").val();
          var txtauom =  $("#txt"+fieldid3+"").data("desc");
          var txtmuom =  $(this).find('[id*="itemuom"]').text().trim();
          var fieldid4 = $(this).find('[id*="uomqty"]').attr('id');
          var txtauomid =  $("#txt"+fieldid4+"").val();
          var txtmuomqty =  $(this).find('[id*="uomqty"]').text().trim();
          var fieldid5 = $(this).find('[id*="irate"]').attr('id');
          var txtruom =  $("#txt"+fieldid5+"").val();
          
          var fieldid6 = $(this).find('[id*="itax"]').attr('id');
          var txttax2 =  $("#txt"+fieldid6+"").val();
          var txttax1 = $("#txt"+fieldid6+"").data("desc");
          var fieldid7 = $(this).find('[id*="ise"]').attr('id');


          var txtpoid = $("#txt"+fieldid7+"").val();
          var txtgeid = $("#txt"+fieldid7+"").data("desc");
          var txtmrsid = $("#txt"+fieldid7+"").data("desc1");
          var txtpiid = $("#txt"+fieldid7+"").data("desc2");
          var txtrfqid = $("#txt"+fieldid7+"").data("desc3");
          var txtvqid = $("#txt"+fieldid7+"").data("desc4");
          var txtipoid = $("#txt"+fieldid7+"").data("desc5");

          var item_id             =   txtval;
          var item_unique_row_id  =   $("#txt"+fieldid7+"").data("desc0");
          var ITEM_SEID_REF       =   $("#txt"+fieldid7+"").data("desc1");
          var ITEM_SQID_REF       =   $("#txt"+fieldid7+"").data("desc2");
          var ITEM_SOID_REF       =   $("#txt"+fieldid7+"").data("desc3");
          var ITEM_PROID_REF       =   $("#txt"+fieldid7+"").data("desc4");
          var ITEM_JWOID_REF       =   $("#txt"+fieldid7+"").data("desc5");
          var ITEM_JWCID_REF       =   $("#txt"+fieldid7+"").data("desc6");
          var ITEM_GRJID_REF       =   $("#txt"+fieldid7+"").data("desc7");
          var ITEM_JWRATE          =   $("#txt"+fieldid7+"").data("desc8");

          

          var rcount1 = parseInt($(this).closest('table').find('.clsitemid').length);
          var rcount2 = $('#hdn_ItemID21').val();
          var r_count2 = 0;

          var txtreceivedqtymu    =  $("#txt"+fieldid+"").data("desc1");
          var txtreceivedqtyau    =  $("#txt"+fieldid+"").data("desc2");
          if(intRegex.test(txtreceivedqtymu)){
            txtreceivedqtymu = (txtreceivedqtymu +'.000');
          }

          if(intRegex.test(txtreceivedqtyau)){
            txtreceivedqtyau = (txtreceivedqtyau +'.000');
          }

          if(txtpoid == undefined)
          {
            txtpoid = '';
          }
          if(txtgeid == undefined)
          {
            txtgeid = '';
          }
          var totalvalue = 0.00;
          var totalvalue2 = 0.00;
          var txttaxamt1 = 0.00;
          var txttaxamt2 = 0.00;
          var txttottaxamt = 0.00;
          var txttotamtatax =0.00;

          txtruom = parseFloat(txtruom).toFixed(5);

          var txtamt = parseFloat((parseFloat(txtreceivedqtymu)*parseFloat(txtruom))).toFixed(2);

          if(txttax1 == undefined || txttax1 == '')
          {
            txttax1     = 0.0000;
            txttaxamt1  = 0.00;
          }
          else
          {
             txttaxamt1 = parseFloat((parseFloat(txtamt)*parseFloat(txttax1))/100).toFixed(2);
          }
          if(txttax2 == undefined || txttax2 == '')
          {
            txttax2 = 0.0000;
             txttaxamt2 = 0.00;
          }
          else
          {
             txttaxamt2 = parseFloat((parseFloat(txtamt)*parseFloat(txttax2))/100).toFixed(2);
          }
          
          var txttottaxamt = parseFloat((parseFloat(txttaxamt1)+parseFloat(txttaxamt2))).toFixed(2);
          var txttotamtatax = parseFloat((parseFloat(txtamt)+parseFloat(txttottaxamt))).toFixed(2);  

          if(intRegex.test(txtruom)){
            txtruom = (txtruom +'.00000');
          }

        

          if(intRegex.test(txtamt)){
            txtamt = (txtamt +'.00');
          }

          if(intRegex.test(txttax1)){
            txttax1 = (txttax1 +'.0000');
          }
          if(intRegex.test(txttax2)){
            txttax2 = (txttax2 +'.0000');
          }
          if(intRegex.test(txttaxamt1)){
            txttaxamt1 = (txttaxamt1 +'.00');
          }
          if(intRegex.test(txttaxamt2)){
            txttaxamt2 = (txttaxamt2 +'.00');
          }

          if(intRegex.test(txttottaxamt)){
            txttottaxamt = (txttottaxamt +'.00');
          }
          if(intRegex.test(txttotamtatax)){
            txttotamtatax = (txttotamtatax +'.00');
          }


          

    
        if($(this).find('[id*="chkId"]').is(":checked") == true){


          $('#example2').find('.participantRow').each(function(){

              var GRJID_REF   =   $(this).find('[id*="GRJID_REF"]').val();
              var JWCID_REF   =   $(this).find('[id*="JWCID_REF"]').val();
              var JWOID_REF   =   $(this).find('[id*="JWOID_REF"]').val();
              var PROID_REF   =   $(this).find('[id*="PROID_REF"]').val();
              var SOID_REF    =   $(this).find('[id*="SOID_REF"]').val();
              var SQID_REF    =   $(this).find('[id*="SQID_REF"]').val();
              var SEID_REF    =   $(this).find('[id*="SEID_REF"]').val();
              var ITEMID_REF  =   $(this).find('[id*="ITEMID_REF"]').val();

              var exist_val   =   GRJID_REF+"_"+JWCID_REF+"_"+JWOID_REF+"_"+PROID_REF+"_"+SOID_REF+"_"+SQID_REF+"_"+SEID_REF+"_"+ITEMID_REF;

              if(item_id){
                if(item_unique_row_id == exist_val){
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
                  $('#hdn_ItemID12').val('');
                  $('#hdn_ItemID13').val('');
                  $('#hdn_ItemID14').val('');
                  $('#hdn_ItemID15').val('');
                  $('#hdn_ItemID16').val('');
                  
                  txtval = '';
                  txtcode = '';
                  txtname = '';
                  txtspec = '';
                  txtauom = '';
                  txtmuom = '';
                  txtmuomid = '';
                  txtauomid = '';
                  txtmuomqty='';
                  txtruom = '';
                  txtamt = '';
                  txttax1 = '';
                  txttax2 = '';
                  txtpoid = '';
                  txtgeid = '';
                  txtipoid = '';
                  txtvqid = '';
                  txtrfqid = '';
                  txtpiid = '';
                  txtmrsid = '';
                  txtreceivedqtymu = '';
                  txtreceivedqtyau = '';
                  $(".blurRate").blur();
                  $('.js-selectall').prop("checked", false);
                  return false;
                  event.preventDefault();
                
                  return false;
                }               
              } 
                      
          });


          if($('#hdn_ItemID').val() == "" && txtval != ''){

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
            var txt_id12= $('#hdn_ItemID12').val();
            var txt_id13= $('#hdn_ItemID13').val();
            var txt_id14= $('#hdn_ItemID14').val();
            var txt_id15= $('#hdn_ItemID15').val();
            var txt_id16= $('#hdn_ItemID16').val();

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
            $clone.find('[id*="txtItem"]').val(txtcode);
            $clone.find('[id*="ITEMID_REF"]').val(txtval);

           

            $clone.find('[id*="ItemName"]').val(txtname);
            $clone.find('[id*="ItemDesc"]').val(txtspec);
            $clone.find('[id*="txtUOM_"]').val(txtmuom);
            $clone.find('[id*="UOMID_REF_"]').val(txtmuomid);
            $clone.find('[id*="RECEIVED_QTY_"]').val(txtreceivedqtymu);
            $clone.find('[id*="txtALTUOM_"]').val(txtauom);
            $clone.find('[id*="ALT_UOMID_REF_"]').val(txtauomid);
            $clone.find('[id*="RECEIVED_ALT_QTY_"]').val(txtreceivedqtyau);
            $clone.find('[id*="BILL_QTY_"]').val(txtreceivedqtymu);
            $clone.find('[id*="txtBILLUOM_"]').val(txtmuom);
            $clone.find('[id*="BILL_UOMID_REF_"]').val(txtmuomid);
            $clone.find('[id*="BILL_RATEPUOM_"]').val(txtruom);
            $clone.find('[id*="DISC_PER_"]').val('0.0000');
            $clone.find('[id*="DISC_AMT_"]').val('0.00');
            $clone.find('[id*="TAX_AMT_"]').val(txtamt);
            $clone.find('[id*="TT_AMT_"]').val(txttotamtatax);
            $clone.find('[id*="TGST_AMT_"]').val(txttottaxamt);

            $clone.find('[id*="JWCID_REF_"]').val(ITEM_JWCID_REF);
            $clone.find('[id*="JWOID_REF_"]').val(ITEM_JWOID_REF);
            $clone.find('[id*="PROID_REF_"]').val(ITEM_PROID_REF);
            $clone.find('[id*="SOID_REF_"]').val(ITEM_SOID_REF);
            $clone.find('[id*="SQID_REF_"]').val(ITEM_SQID_REF);
            $clone.find('[id*="SEID_REF_"]').val(ITEM_SEID_REF);
            $clone.find('[id*="BILL_RATEPUOM_"]').val(ITEM_JWRATE);
            

            if($.trim($('#Tax_State').val()) == 'OutofState')
            {                          
              $clone.find('[id*="IGST_AMT_"]').val(txttaxamt1);
              $clone.find('[id*="IGST_"]').val(txttax1);
              $clone.find('[id*="SGST_"]').prop('disabled',true); 
              $clone.find('[id*="CGST_"]').prop('disabled',true); 
              $clone.find('[id*="SGST_AMT_"]').prop('disabled',true); 
              $clone.find('[id*="CGST_AMT_"]').prop('disabled',true); 
              $clone.find('[id*="SGST_AMT_"]').val('0.00');
              $clone.find('[id*="CGST_AMT_"]').val('0.00');
              $clone.find('[id*="SGST_"]').val('0.0000');
              $clone.find('[id*="CGST_"]').val('0.0000');
            }
            else
            {                          
              $clone.find('[id*="CGST_AMT_"]').val(txttaxamt1);
              $clone.find('[id*="SGST_AMT_"]').val(txttaxamt2);
              $clone.find('[id*="IGST_AMT_"]').prop('disabled',true);                           
              $clone.find('[id*="IGST_AMT_"]').val('0.00');
              $clone.find('[id*="CGST_"]').val(txttax1);
              $clone.find('[id*="IGST_"]').val('0.0000');
              $clone.find('[id*="IGST_"]').prop('disabled',true); 
              $clone.find('[id*="SGST_"]').val(txttax2);
            }
                        
            $tr.closest('table').append($clone);   
            var rowCount = $('#Row_Count1').val();
            rowCount = parseInt(rowCount)+1;
            $('#Row_Count1').val(rowCount);
            if($('#GST_Reverse').is(':checked') == true)
            {
              var tvalue2 = parseFloat(txtamt).toFixed(2);
              totalvalue2 = $('#tot_amt').val();
              totalvalue2 =  parseFloat(totalvalue2) + parseFloat(tvalue2);
              totalvalue2 = parseFloat(totalvalue2).toFixed(2);
              $('#tot_amt').val(totalvalue2);
            }
            else
            {
              var tvalue = parseFloat(txttotamtatax).toFixed(2);
              totalvalue = $('#tot_amt').val();
              totalvalue =  parseFloat(totalvalue) + parseFloat(tvalue);
              totalvalue = parseFloat(totalvalue).toFixed(2);
              $('#tot_amt').val(totalvalue);
            }
            $(".blurRate").blur();
            // $("#ITEMIDpopup").hide();
            event.preventDefault();
          }
          else{

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
            var txt_id12= $('#hdn_ItemID12').val();
            var txt_id13= $('#hdn_ItemID13').val();
            var txt_id14= $('#hdn_ItemID14').val();
            var txt_id15= $('#hdn_ItemID15').val();
            var txt_id16= $('#hdn_ItemID16').val();

            $('#'+txtid).val(txtcode);
            $('#'+txt_id2).val(txtval);
            $('#'+txt_id3).val(txtname);
            $('#'+txt_id4).val(txtmuom);
            $('#'+txt_id5).val(txtmuomid);
            $('#'+txt_id6).val(txtreceivedqtymu);
            $('#'+txt_id7).val(txtauom);
            $('#'+txt_id8).val(txtauomid);
            $('#'+txt_id9).val(txtreceivedqtyau);
            $('#'+txt_id10).val(txtreceivedqtymu);
            $('#'+txt_id11).val(txtmuom);
            $('#'+txt_id12).val(txtmuomid);
            $('#'+txt_id13).val(txtspec);
            $('#'+txt_id14).val(txtruom);
            $('#'+txt_id15).val('0.0000');
            $('#'+txt_id16).val('0.00');

         

            $('#'+txtid).parent().parent().find('[id*="TAX_AMT_"]').val(txtamt);
            $('#'+txtid).parent().parent().find('[id*="TT_AMT_"]').val(txttotamtatax);
            $('#'+txtid).parent().parent().find('[id*="TGST_AMT"]').val(txttottaxamt);

            $('#'+txtid).parent().parent().find('[id*="JWCID_REF_"]').val(ITEM_JWCID_REF);
            $('#'+txtid).parent().parent().find('[id*="JWOID_REF_"]').val(ITEM_JWOID_REF);
            $('#'+txtid).parent().parent().find('[id*="PROID_REF_"]').val(ITEM_PROID_REF);
            $('#'+txtid).parent().parent().find('[id*="SOID_REF_"]').val(ITEM_SOID_REF);
            $('#'+txtid).parent().parent().find('[id*="SQID_REF_"]').val(ITEM_SQID_REF);
            $('#'+txtid).parent().parent().find('[id*="SEID_REF_"]').val(ITEM_SEID_REF);
            $('#'+txtid).parent().parent().find('[id*="BILL_RATEPUOM_"]').val(ITEM_JWRATE);

            

            if($.trim($('#Tax_State').val()) == 'OutofState')
            { 
              $('#'+txtid).parent().parent().find('[id*="SGST_"]').val('0.0000');
              $('#'+txtid).parent().parent().find('[id*="CGST_"]').val('0.0000');    
              $('#'+txtid).parent().parent().find('[id*="IGST_"]').val(txttax1);                   
              $('#'+txtid).parent().parent().find('[id*="IGST_AMT_"]').val(txttaxamt1);                          
              $('#'+txtid).parent().parent().find('[id*="SGST_"]').prop('disabled',true); 
              $('#'+txtid).parent().parent().find('[id*="CGST_"]').prop('disabled',true);
              $('#'+txtid).parent().parent().find('[id*="SGST_AMT_"]').prop('disabled',true); 
              $('#'+txtid).parent().parent().find('[id*="CGST_AMT_"]').prop('disabled',true);                           
              $('#'+txtid).parent().parent().find('[id*="SGST_AMT_"]').val('0.00');
              $('#'+txtid).parent().parent().find('[id*="CGST_AMT_"]').val('0.00');
              
            }
            else
            {   
              $('#'+txtid).parent().parent().find('[id*="CGST_"]').val(txttax1);
              $('#'+txtid).parent().parent().find('[id*="IGST_"]').prop('disabled',true); 
              $('#'+txtid).parent().parent().find('[id*="IGST_AMT_"]').prop('disabled',true); 
              $('#'+txtid).parent().parent().find('[id*="SGST_"]').val(txttax2);
              $('#'+txtid).parent().parent().find('[id*="IGST_"]').val('0.0000');                       
              $('#'+txtid).parent().parent().find('[id*="SGST_AMT_"]').val(txttaxamt2);
              $('#'+txtid).parent().parent().find('[id*="CGST_AMT_"]').val(txttaxamt1);                          
              $('#'+txtid).parent().parent().find('[id*="IGST_AMT_"]').val('0.00');
              
            }
            if($('#GST_Reverse').is(':checked') == true)
            {
              var tvalue2 = parseFloat(txtamt).toFixed(2);
              totalvalue2 = $('#tot_amt').val();
              totalvalue2 =  parseFloat(totalvalue2) + parseFloat(tvalue2);
              totalvalue2 = parseFloat(totalvalue2).toFixed(2);
              $('#tot_amt').val(totalvalue2);
            }
            else
            {
              var tvalue = parseFloat(txttotamtatax).toFixed(2);
              totalvalue = $('#tot_amt').val();
              totalvalue =  parseFloat(totalvalue) + parseFloat(tvalue);
              totalvalue = parseFloat(totalvalue).toFixed(2);
              $('#tot_amt').val(totalvalue);
            }
                       
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
            $('#hdn_ItemID12').val('');
            $('#hdn_ItemID13').val('');
            $('#hdn_ItemID14').val('');
            $('#hdn_ItemID15').val('');
            $('#hdn_ItemID16').val('');
            $('#hdn_ItemID17').val('');
            $('#hdn_ItemID18').val('');
            $('#hdn_ItemID19').val('');
            $('#hdn_ItemID20').val('');
            $('#hdn_ItemID22').val('');
            $('#hdn_ItemID23').val('');
            $('#hdn_ItemID24').val('');
            $('#hdn_ItemID25').val('');
            $('#hdn_ItemID26').val('');
            $('#hdn_ItemID27').val('');

            $(".blurRate").blur();
            // $("#ITEMIDpopup").hide();
            event.preventDefault();
          }
                  
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

      $("#ITEMIDpopup").hide();
      event.preventDefault();
    });

 

    $('[id*="chkId"]').change(function(){

      var fieldid = $(this).parent().parent().attr('id');       
      var txtval =   $("#txt"+fieldid+"").val();
      var txtcode =  $("#txt"+fieldid+"").data("desc");
      var fieldid2 = $(this).parent().parent().children('[id*="itemname"]').attr('id');
      var txtname =  $("#txt"+fieldid2+"").val();
      var txtspec =  $("#txt"+fieldid2+"").data("desc");
      var fieldid3 = $(this).parent().parent().children('[id*="itemuom"]').attr('id');
      var txtmuomid =  $("#txt"+fieldid3+"").val();
      var txtauom =  $("#txt"+fieldid3+"").data("desc");
      var txtmuom =  $(this).parent().parent().children('[id*="itemuom"]').text().trim();
      var fieldid4 = $(this).parent().parent().children('[id*="uomqty"]').attr('id');
      var txtauomid =  $("#txt"+fieldid4+"").val();
      var txtmuomqty =  $(this).parent().parent().children('[id*="uomqty"]').text().trim();
      var fieldid5 = $(this).parent().parent().children('[id*="irate"]').attr('id');
      var txtruom =  $("#txt"+fieldid5+"").val();
      var fieldid6 = $(this).parent().parent().children('[id*="itax"]').attr('id');
      var txttax2 =  $("#txt"+fieldid6+"").val();
      var txttax1 = $("#txt"+fieldid6+"").data("desc");
      var fieldid7 = $(this).parent().parent().children('[id*="ise"]').attr('id');
      var txtpoid = $("#txt"+fieldid7+"").val();
      var txtgeid = $("#txt"+fieldid7+"").data("desc");
      var txtmrsid = $("#txt"+fieldid7+"").data("desc1");
      var txtpiid = $("#txt"+fieldid7+"").data("desc2");
      var txtrfqid = $("#txt"+fieldid7+"").data("desc3");
      var txtvqid = $("#txt"+fieldid7+"").data("desc4");
      var txtipoid = $("#txt"+fieldid7+"").data("desc5");

      var item_id             =   txtval;
      var item_unique_row_id  =   $("#txt"+fieldid7+"").data("desc0");
      var ITEM_SEID_REF       =   $("#txt"+fieldid7+"").data("desc1");
      var ITEM_SQID_REF       =   $("#txt"+fieldid7+"").data("desc2");
      var ITEM_SOID_REF       =   $("#txt"+fieldid7+"").data("desc3");
      var ITEM_PROID_REF       =   $("#txt"+fieldid7+"").data("desc4");
      var ITEM_JWOID_REF       =   $("#txt"+fieldid7+"").data("desc5");
      var ITEM_JWCID_REF       =   $("#txt"+fieldid7+"").data("desc6");
      var ITEM_GRJID_REF       =   $("#txt"+fieldid7+"").data("desc7");
      var ITEM_JWRATE          =   $("#txt"+fieldid7+"").data("desc8");

      var txtreceivedqtymu    =  $("#txt"+fieldid+"").data("desc1");
      var txtreceivedqtyau    =  $("#txt"+fieldid+"").data("desc2");
      var r_count2 = 0;
      if(intRegex.test(txtreceivedqtymu)){
          txtreceivedqtymu = (txtreceivedqtymu +'.000');
      }

      if(intRegex.test(txtreceivedqtyau)){
        txtreceivedqtyau = (txtreceivedqtyau +'.000');
      }

      if(txtpoid == undefined)
      {
        txtpoid = '';
      }
      if(txtgeid == undefined)
      {
        txtgeid = '';
      }
      var totalvalue = 0.00;
      var totalvalue2 = 0.00;
      var txttaxamt1 = 0.00;
      var txttaxamt2 = 0.00;
      var txttottaxamt = 0.00;
      var txttotamtatax =0.00;

      txtruom = parseFloat(txtruom).toFixed(5);

      var txtamt = parseFloat((parseFloat(txtreceivedqtymu)*parseFloat(txtruom))).toFixed(2);

      if(txttax1 == undefined || txttax1 == '')
      {
        txttax1     = 0.0000;
        txttaxamt1  = 0.00;
      }
      else
      {
          txttaxamt1 = parseFloat((parseFloat(txtamt)*parseFloat(txttax1))/100).toFixed(2);
      }
      if(txttax2 == undefined || txttax2 == '')
      {
        txttax2 = 0.0000;
          txttaxamt2 = 0.00;
      }
      else
      {
          txttaxamt2 = parseFloat((parseFloat(txtamt)*parseFloat(txttax2))/100).toFixed(2);
      }
      
      var txttottaxamt = parseFloat((parseFloat(txttaxamt1)+parseFloat(txttaxamt2))).toFixed(2);
      var txttotamtatax = parseFloat((parseFloat(txtamt)+parseFloat(txttottaxamt))).toFixed(2);

      if(intRegex.test(txtruom)){
        txtruom = (txtruom +'.00000');
      }

      if(intRegex.test(txtamt)){
        txtamt = (txtamt +'.00');
      }

      if(intRegex.test(txttax1)){
        txttax1 = (txttax1 +'.0000');
      }
      if(intRegex.test(txttax2)){
        txttax2 = (txttax2 +'.0000');
      }
      if(intRegex.test(txttaxamt1)){
        txttaxamt1 = (txttaxamt1 +'.00');
      }
      if(intRegex.test(txttaxamt2)){
        txttaxamt2 = (txttaxamt2 +'.00');
      }

      if(intRegex.test(txttottaxamt)){
        txttottaxamt = (txttottaxamt +'.00');
      }
      if(intRegex.test(txttotamtatax)){
        txttotamtatax = (txttotamtatax +'.00');
      }

      if($(this).is(":checked") == true) {

       

        $('#example2').find('.participantRow').each(function(){

          var GRJID_REF   =   $(this).find('[id*="GRJID_REF"]').val();
          var JWCID_REF   =   $(this).find('[id*="JWCID_REF"]').val();
          var JWOID_REF   =   $(this).find('[id*="JWOID_REF"]').val();
          var PROID_REF   =   $(this).find('[id*="PROID_REF"]').val();
          var SOID_REF    =   $(this).find('[id*="SOID_REF"]').val();
          var SQID_REF    =   $(this).find('[id*="SQID_REF"]').val();
          var SEID_REF    =   $(this).find('[id*="SEID_REF"]').val();
          var ITEMID_REF  =   $(this).find('[id*="ITEMID_REF"]').val();

          var exist_val   =   GRJID_REF+"_"+JWCID_REF+"_"+JWOID_REF+"_"+PROID_REF+"_"+SOID_REF+"_"+SQID_REF+"_"+SEID_REF+"_"+ITEMID_REF;

          if(item_id){
            if(item_unique_row_id == exist_val){
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
              $('#hdn_ItemID12').val('');
              $('#hdn_ItemID13').val('');
              $('#hdn_ItemID14').val('');
              $('#hdn_ItemID15').val('');
              $('#hdn_ItemID16').val('');
              
              txtval = '';
              txtcode = '';
              txtname = '';
              txtspec = '';
              txtauom = '';
              txtmuom = '';
              txtmuomid = '';
              txtauomid = '';
              txtmuomqty='';
              txtruom = '';
              txtamt = '';
              txttax1 = '';
              txttax2 = '';
              txtpoid = '';
              txtgeid = '';
              txtipoid = '';
              txtvqid = '';
              txtrfqid = '';
              txtpiid = '';
              txtmrsid = '';
              txtreceivedqtymu = '';
              txtreceivedqtyau = '';
              $(".blurRate").blur();
              $('.js-selectall').prop("checked", false);
              return false;
              event.preventDefault();
            
              return false;
            }               
          } 
                  
        });

        if($('#hdn_ItemID1').val() == "" && item_id != ''){

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
                        $clone.find('[id*="txtItem_"]').val(txtcode);
                        $clone.find('[id*="ITEMID_REF"]').val(txtval);

                        

                        $clone.find('[id*="ItemName"]').val(txtname);
                        $clone.find('[id*="ItemDesc"]').val(txtspec);
                        $clone.find('[id*="txtUOM_"]').val(txtmuom);
                        $clone.find('[id*="UOMID_REF_"]').val(txtmuomid);
                        $clone.find('[id*="RECEIVED_QTY_"]').val(txtreceivedqtymu);
                        $clone.find('[id*="txtALTUOM_"]').val(txtauom);
                        $clone.find('[id*="ALT_UOMID_REF_"]').val(txtauomid);
                        $clone.find('[id*="RECEIVED_ALT_QTY_"]').val(txtreceivedqtyau);
                        $clone.find('[id*="BILL_QTY_"]').val(txtreceivedqtymu);
                        $clone.find('[id*="txtBILLUOM_"]').val(txtmuom);
                        $clone.find('[id*="BILL_UOMID_REF_"]').val(txtmuomid);
                        $clone.find('[id*="BILL_RATEPUOM_"]').val(txtruom);
                        $clone.find('[id*="DISC_PER_"]').val('0.0000');
                        $clone.find('[id*="DISC_AMT_"]').val('0.00');
                        $clone.find('[id*="TAX_AMT_"]').val(txtamt);
                        $clone.find('[id*="TT_AMT_"]').val(txttotamtatax);
                        $clone.find('[id*="TGST_AMT_"]').val(txttottaxamt);

                        $clone.find('[id*="JWCID_REF_"]').val(ITEM_JWCID_REF);
                        $clone.find('[id*="JWOID_REF_"]').val(ITEM_JWOID_REF);
                        $clone.find('[id*="PROID_REF_"]').val(ITEM_PROID_REF);
                        $clone.find('[id*="SOID_REF_"]').val(ITEM_SOID_REF);
                        $clone.find('[id*="SQID_REF_"]').val(ITEM_SQID_REF);
                        $clone.find('[id*="SEID_REF_"]').val(ITEM_SEID_REF);
                        $clone.find('[id*="BILL_RATEPUOM_"]').val(ITEM_JWRATE);


                        if($.trim($('#Tax_State').val()) == 'OutofState')
                        { 
                          $clone.find('[id*="IGST_"]').val(txttax1);
                          $clone.find('[id*="IGST_AMT_"]').val(txttaxamt1);
                          $clone.find('[id*="SGST_"]').prop('disabled',true); 
                          $clone.find('[id*="CGST_"]').prop('disabled',true); 
                          $clone.find('[id*="SGST_AMT_"]').prop('disabled',true); 
                          $clone.find('[id*="CGST_AMT_"]').prop('disabled',true);   
                          $clone.find('[id*="SGST_"]').val('0.0000');
                          $clone.find('[id*="CGST_"]').val('0.0000');                        
                          $clone.find('[id*="SGST_AMT_"]').val('0.00');
                          $clone.find('[id*="CGST_AMT_"]').val('0.00');                          
                        }
                        else
                        { 
                          $clone.find('[id*="CGST_"]').val(txttax1);
                          $clone.find('[id*="IGST_"]').prop('disabled',true); 
                          $clone.find('[id*="SGST_"]').val(txttax2);
                          $clone.find('[id*="IGST_"]').val('0.0000');                      
                          $clone.find('[id*="CGST_AMT_"]').val(txttaxamt1);
                          $clone.find('[id*="SGST_AMT_"]').val(txttaxamt2);
                          $clone.find('[id*="IGST_AMT_"]').prop('disabled',true);                           
                          $clone.find('[id*="IGST_AMT_"]').val('0.00');                          
                        }
                        
                        $tr.closest('table').append($clone);   
                        var rowCount = $('#Row_Count1').val();
                        rowCount = parseInt(rowCount)+1;
                        $('#Row_Count1').val(rowCount);
                        if($('#GST_Reverse').is(':checked') == true)
                        {
                          var tvalue2 = parseFloat(txtamt).toFixed(2);
                          totalvalue2 = $('#tot_amt').val();
                          totalvalue2 =  parseFloat(totalvalue2) + parseFloat(tvalue2);
                          totalvalue2 = parseFloat(totalvalue2).toFixed(2);
                          $('#tot_amt').val(totalvalue2);
                        }
                        else
                        {
                          var tvalue = parseFloat(txttotamtatax).toFixed(2);
                          totalvalue = $('#tot_amt').val();
                          totalvalue =  parseFloat(totalvalue) + parseFloat(tvalue);
                          totalvalue = parseFloat(totalvalue).toFixed(2);
                          $('#tot_amt').val(totalvalue);
                        }

                        $(".blurRate").blur();
                        $("#ITEMIDpopup").hide();
                        event.preventDefault();

        }
        else{

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
                        var txt_id12= $('#hdn_ItemID12').val();
                        var txt_id13= $('#hdn_ItemID13').val();
                        var txt_id14= $('#hdn_ItemID14').val();
                        var txt_id15= $('#hdn_ItemID15').val();
                        var txt_id16= $('#hdn_ItemID16').val();
                        $('#'+txtid).val(txtcode);
                        $('#'+txt_id2).val(txtval);
                        $('#'+txt_id3).val(txtname);
                        $('#'+txt_id4).val(txtmuom);
                        $('#'+txt_id5).val(txtmuomid);
                        $('#'+txt_id6).val(txtreceivedqtymu);
                        $('#'+txt_id7).val(txtauom);
                        $('#'+txt_id8).val(txtauomid);
                        $('#'+txt_id9).val(txtreceivedqtyau);
                        $('#'+txt_id10).val(txtreceivedqtymu);
                        $('#'+txt_id11).val(txtmuom);
                        $('#'+txt_id12).val(txtmuomid);
                        $('#'+txt_id13).val(txtspec);
                        $('#'+txt_id14).val(txtruom);
                        $('#'+txt_id15).val('0.0000');
                        $('#'+txt_id16).val('0.00');

                        
                        $('#'+txtid).parent().parent().find('[id*="TAX_AMT_"]').val(txtamt);
                        $('#'+txtid).parent().parent().find('[id*="TT_AMT_"]').val(txttotamtatax);
                        $('#'+txtid).parent().parent().find('[id*="TGST_AMT"]').val(txttottaxamt);

                        $('#'+txtid).parent().parent().find('[id*="JWCID_REF_"]').val(ITEM_JWCID_REF);
                        $('#'+txtid).parent().parent().find('[id*="JWOID_REF_"]').val(ITEM_JWOID_REF);
                        $('#'+txtid).parent().parent().find('[id*="PROID_REF_"]').val(ITEM_PROID_REF);
                        $('#'+txtid).parent().parent().find('[id*="SOID_REF_"]').val(ITEM_SOID_REF);
                        $('#'+txtid).parent().parent().find('[id*="SQID_REF_"]').val(ITEM_SQID_REF);
                        $('#'+txtid).parent().parent().find('[id*="SEID_REF_"]').val(ITEM_SEID_REF);
                        $('#'+txtid).parent().parent().find('[id*="BILL_RATEPUOM_"]').val(ITEM_JWRATE);
                        

                        if($.trim($('#Tax_State').val()) == 'OutofState')
                          { 
                            $('#'+txtid).parent().parent().find('[id*="IGST_"]').val(txttax1);                          
                            $('#'+txtid).parent().parent().find('[id*="IGST_AMT_"]').val(txttaxamt1);
                            $('#'+txtid).parent().parent().find('[id*="SGST_"]').prop('disabled',true); 
                            $('#'+txtid).parent().parent().find('[id*="CGST_"]').prop('disabled',true);
                            $('#'+txtid).parent().parent().find('[id*="SGST_AMT_"]').prop('disabled',true); 
                            $('#'+txtid).parent().parent().find('[id*="CGST_AMT_"]').prop('disabled',true);  
                            $('#'+txtid).parent().parent().find('[id*="SGST"]').val('0.0000');
                            $('#'+txtid).parent().parent().find('[id*="CGST_"]').val('0.0000');                           
                            $('#'+txtid).parent().parent().find('[id*="SGST_AMT_"]').val('0.00');
                            $('#'+txtid).parent().parent().find('[id*="CGST_AMT_"]').val('0.00');                            
                          }
                          else
                          { 
                            $('#'+txtid).parent().parent().find('[id*="IGST_"]').val('0.0000');
                            $('#'+txtid).parent().parent().find('[id*="CGST_"]').val(txttax1);
                            $('#'+txtid).parent().parent().find('[id*="IGST_"]').prop('disabled',true); 
                            $('#'+txtid).parent().parent().find('[id*="IGST_AMT_"]').prop('disabled',true); 
                            $('#'+txtid).parent().parent().find('[id*="SGST_"]').val(txttax2);                           
                            $('#'+txtid).parent().parent().find('[id*="SGST_AMT_"]').val(txttaxamt2);
                            $('#'+txtid).parent().parent().find('[id*="CGST_AMT_"]').val(txttaxamt1);                            
                            $('#'+txtid).parent().parent().find('[id*="IGST_AMT_"]').val('0.00');                            
                          }
                          if($('#GST_Reverse').is(':checked') == true)
                          {
                            var tvalue2 = parseFloat(txtamt).toFixed(2);
                            totalvalue2 = $('#tot_amt').val();
                            totalvalue2 =  parseFloat(totalvalue2) + parseFloat(tvalue2);
                            totalvalue2 = parseFloat(totalvalue2).toFixed(2);
                            $('#tot_amt').val(totalvalue2);
                          }
                          else
                          {
                            var tvalue = parseFloat(txttotamtatax).toFixed(2);
                            totalvalue = $('#tot_amt').val();
                            totalvalue =  parseFloat(totalvalue) + parseFloat(tvalue);
                            totalvalue = parseFloat(totalvalue).toFixed(2);
                            $('#tot_amt').val(totalvalue);
                          }
                        

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
                        $('#hdn_ItemID12').val('');
                        $('#hdn_ItemID13').val('');
                        $('#hdn_ItemID14').val('');
                        $('#hdn_ItemID15').val('');
                        $('#hdn_ItemID16').val('');
        }

        $(".blurRate").blur();     
        $("#ITEMIDpopup").hide();
        event.preventDefault();
      }
      else if($(this).is(":checked") == false){

        var id = item_id;
        var r_count = $('#Row_Count1').val();

        $('#example2').find('.participantRow').each(function(){
          var ITEMID_REF = $(this).find('[id*="ITEMID_REF"]').val();

          if(id == ITEMID_REF){
            var rowCount = $('#Row_Count1').val();

            if (rowCount > 1) {
              $(this).closest('.participantRow').remove(); 
              rowCount = parseInt(rowCount)-1;
              $('#Row_Count1').val(rowCount);
            }
            else {
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
            $(".blurRate").blur();
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
     
$(document).ready(function(e) {
var Material = $("#Material").html(); 
$('#hdnMaterial').val(Material);

var TNC = $("#TC").html(); 
$('#hdnTNC').val(TNC);
var Calculation = $("#CT").html(); 
$('#hdnCalculation').val(Calculation);


var objlastdt = <?php echo json_encode($objlastdt[0]->JWIDT); ?>;
var today = new Date(); 
var ardate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
$('#JWIDT').attr('min',objlastdt);
$('#JWIDT').attr('max',ardate);
$('#JWIDT').val(ardate);

var apudf = <?php echo json_encode($objUdfPBData); ?>;
var count2 = <?php echo json_encode($objCountUDF); ?>;
$("#Row_Count1").val(1);
$("#Row_Count3").val(1);
$("#Row_Count4").val(1);
$("#Row_Count5").val(1);
$("#Row_Count6").val(1);
$("#Row_Count2").val(count2);
$('#udf').find('.participantRow4').each(function(){
  var txt_id4 = $(this).find('[id*="udfinputid"]').attr('id');
  var udfid = $(this).find('[id*="UDFJWIID_REF"]').val();
  $.each( apudf, function( apukey, apuvalue ) {
    if(apuvalue.UDFJWIID == udfid)
    {
      var txtvaltype2 =   apuvalue.VALUETYPE;
      var strdyn2 = txt_id4.split('_');
      var lastele2 =   strdyn2[strdyn2.length-1];
      var dynamicid2 = "udfvalue_"+lastele2;
      
      var chkvaltype2 =  txtvaltype2.toLowerCase();
      var strinp2 = '';

      if(chkvaltype2=='date'){
      strinp2 = '<input type="date" placeholder="dd/mm/yyyy" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"  > ';       
      }
      else if(chkvaltype2=='time'){
      strinp2= '<input type="time" placeholder="h:i" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"  > ';
      }
      else if(chkvaltype2=='numeric'){
      strinp2 = '<input type="text" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"   > ';
      }
      else if(chkvaltype2=='text'){
      strinp2 = '<input type="text" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"  > ';          
      }
      else if(chkvaltype2=='boolean'){            
          strinp2 = '<input type="checkbox" name="'+dynamicid2+ '" id="'+dynamicid2+'" class="" > ';
      }
      else if(chkvaltype2=='combobox'){
      var txtoptscombo2 =   apuvalue.DESCRIPTIONS;
      var strarray2 = txtoptscombo2.split(',');
      var opts2 = '';
      for (var i = 0; i < strarray2.length; i++) {
          opts2 = opts2 + '<option value="'+strarray2[i]+'">'+strarray2[i]+'</option> ';
      }
      strinp2 = '<select name="'+dynamicid2+ '" id="'+dynamicid2+'" class="form-control" required>'+opts2+'</select>' ;          
      }
      $('#'+txt_id4).html('');  
      $('#'+txt_id4).html(strinp2);
    }
  });
});

$('#btnAdd').on('click', function() {
  var viewURL = '{{route("transaction",[$FormId,"add"])}}';
              window.location.href=viewURL;
});

$('#btnExit').on('click', function() {
  var viewURL = '{{route('home')}}';
              window.location.href=viewURL;
});

function bindTotalValue()
    {
      var totalvalue = 0.00;
      var tvalue = 0.00;
      var ctvalue = 0.00;
      var ctgstvalue = 0.00;
      var tttdsamt21 = 0.00;
      $('#Material').find('.participantRow').each(function()
      {
        if($('#GST_Reverse').is(':checked') == true)
        {
          tvalue = $(this).find('[id*="TAX_AMT_"]').val();
          totalvalue = parseFloat(totalvalue) + parseFloat(tvalue);
          totalvalue = parseFloat(totalvalue).toFixed(2);
        }
        else
        {
          tvalue = $(this).find('[id*="TT_AMT_"]').val();
          totalvalue = parseFloat(totalvalue) + parseFloat(tvalue);
          totalvalue = parseFloat(totalvalue).toFixed(2);
        }        
      });
      if($('#CTID_REF').val() != '')
      {
        $('#CT').find('.participantRow5').each(function()
        {
          ctvalue = $(this).find('[id*="VALUE"]').val();
          ctgstvalue = $(this).find('[id*="TOTGSTAMT"]').val();
          if($('#GST_Reverse').is(':checked') == true)
          {
            totalvalue = parseFloat(totalvalue) + parseFloat(ctvalue);
            totalvalue = parseFloat(totalvalue).toFixed(2);
          }
          else
          {
            totalvalue = parseFloat(totalvalue) + parseFloat(ctvalue);
            totalvalue = parseFloat(totalvalue) + parseFloat(ctgstvalue);
            totalvalue = parseFloat(totalvalue).toFixed(2);
          }
        });
      }

      $('#tot_amt').val(totalvalue);
  }

  function bindGSTCalTemplate()
  { 
          $('#CT').find('.participantRow5').each(function()
            { 
                var basis = $(this).find('[id*="BASIS"]').val();
                var sqno = $(this).find('[id*="SQNO"]').val();
                var formula = $(this).find('[id*="FORMULA"]').val();
                var rate = $(this).find('[id*="RATE"]').val();
                var amountnet = $(this).find('[id*="VALUE"]').val();
                var netTaxableAmount = 0.00;
                var netGSTAmount = 0.00;
                var netTotalAmount = 0.00;
                var totamount = 0.00;
                var tamt = 0.00;
                var IGSTamt = 0.00;
                var CGSTamt = 0.00;
                var SGSTamt = 0.00;
                var TotGSTamt = 0.00;

                $('#Material').find('.participantRow').each(function()
                {  
                  if($(this).find('[id*="TAX_AMT_"]').val() != '' && $(this).find('[id*="TAX_AMT_"]').val() != '.00') {                   
                  var TaxableAmount = $(this).find('[id*="TAX_AMT_"]').val();
                  if (!isNaN(TaxableAmount) && TaxableAmount.length !== 0) {
                    netTaxableAmount += parseFloat(TaxableAmount);
                    }                      
                  }
                  else
                  {
                    var TaxableAmount = 0;
                    netTaxableAmount += 0;
                  }
                  if($(this).find('[id*="TGST_AMT_"]').val() != '' && $(this).find('[id*="TGST_AMT_"]').val() != '.00') {                   
                    var GSTAmount = $(this).find('[id*="TGST_AMT_"]').val();
                    if (!isNaN(GSTAmount) && GSTAmount.length !== 0) {
                      netGSTAmount += parseFloat(GSTAmount);
                    }                      
                  }
                  else
                  {
                    var GSTAmount = 0;
                    netGSTAmount += 0;
                  }
                  if($(this).find('[id*="TT_AMT_"]').val() != '' && $(this).find('[id*="TT_AMT_"]').val() != '.00') {                   
                    var TotalAmount = $(this).find('[id*="TT_AMT_"]').val();
                    if (!isNaN(TotalAmount) && TotalAmount.length !== 0) {
                      netTotalAmount += parseFloat(TotalAmount);
                    }                      
                  }
                  else
                  {
                    var TotalAmount = 0;
                    netTotalAmount += 0;
                  }
                })
                var IGST = $('#IGST_0').val();
                var CGST = $('#CGST_0').val();
                var SGST = $('#SGST_0').val();
                
                  if(formula == '')
                  {
                    if(rate > 0)
                    { 
                      if(basis == 'Item Taxable Amount')
                      {
                        if(netTaxableAmount != '0')
                        {
                          totamount = parseFloat((rate * netTaxableAmount)/100).toFixed(2);
                        }
                        else
                        {
                          totamount = 0;
                        }
                      }
                      if(basis == 'Item GST Amount')
                      {
                        if(netGSTAmount != '0')
                        {
                          totamount = parseFloat((rate * netGSTAmount)/100).toFixed(2);
                        }
                        else
                        {
                          totamount = 0;
                        }
                      }
                      if(basis == 'Amount After GST Item')
                      {
                        if(netTotalAmount != '0')
                        {
                          totamount = parseFloat((rate * netTotalAmount)/100).toFixed(2);
                        }
                        else
                        {
                          totamount = 0;
                        }
                      }
                    }
                    else
                    {
                      totamount = amountnet;
                    }
                  }
                  else
                  {
                    if(basis == 'Item Taxable Amount')
                    {
                      var basis1 = '( '+netTaxableAmount+' * '+rate+' ) / 100';
                      var basis2 = netTaxableAmount;
                      var rate1 = rate +' ) / 100';
                      if(formula.indexOf("BASIS*RATE") != -1){
                        var formula1 = formula.replace ("BASIS*RATE", basis1);
                        tamt = eval(formula1);
                        totamount = parseFloat((tamt * rate)/100).toFixed(2);
                      }
                      else if(formula.indexOf("BASIS") != -1){
                        var formula1 = formula.replace ("BASIS", basis2);
                        tamt = eval(formula1);
                        totamount = parseFloat((tamt * rate)/100).toFixed(2);
                      }
                      else if(formula.indexOf("RATE") != -1){
                        var formula1 = formula.replace ("RATE", rate1);
                        tamt = eval(formula1);
                        totamount = parseFloat(( tamt * rate)/100).toFixed(2);
                      }
                    }
                    if(basis == 'Item GST Amount')
                    {
                      var basis1 = '('+netGSTAmount+'*'+rate+')/100';
                      var basis2 = netGSTAmount;
                      var rate1 = rate+')/100';
                      if(formula.indexOf("BASIS*RATE") != -1){
                        var formula1 = formula.replace ("BASIS*RATE", basis1);
                        tamt = eval(formula1);
                        totamount = parseFloat((tamt * rate)/100).toFixed(2);
                      }
                      else if(formula.indexOf("BASIS") != -1){
                        var formula1 = formula.replace ("BASIS", basis2);
                        tamt = eval(formula1);
                        totamount = parseFloat((tamt * rate)/100).toFixed(2);
                      }
                      else if(formula.indexOf("RATE") != -1){
                        var formula1 = formula.replace ("RATE", rate1);
                        tamt = eval(formula1);
                        totamount = parseFloat(( tamt * rate)/100).toFixed(2);
                      }
                    }
                    if(basis == 'Amount After GST Item')
                    {
                      var basis1 = '( '+netTotalAmount+' * '+rate+' ) / 100';
                      var basis2 = netTotalAmount;
                      var rate1 = rate+' ) / 100';
                      if(formula.indexOf("BASIS*RATE") != -1){
                        var formula1 = formula.replace ("BASIS*RATE", basis1);
                        tamt = eval(formula1);
                        totamount = parseFloat((tamt * rate)/100).toFixed(2);
                      }
                      else if(formula.indexOf("BASIS") != -1){
                        var formula1 = formula.replace ("BASIS", basis2);
                        tamt = eval(formula1);
                        totamount = parseFloat((tamt * rate)/100).toFixed(2);
                      }
                      else if(formula.indexOf("RATE") != -1){
                        var formula1 = formula.replace ("RATE", rate1);
                        tamt = eval(formula1);
                        totamount = parseFloat(( tamt * rate)/100).toFixed(2);
                      }
                    }
                    
                  }
                  $(this).find('[id*="VALUE_"]').val(totamount);
                    IGSTamt = parseFloat((IGST * totamount)/100).toFixed(2);
                    CGSTamt = parseFloat((CGST * totamount)/100).toFixed(2);
                    SGSTamt = parseFloat((SGST * totamount)/100).toFixed(2);
                    TotGSTamt = parseFloat(parseFloat(IGSTamt)+parseFloat(CGSTamt)+parseFloat(SGSTamt)).toFixed(2);
                if($(this).find('[id*="calGST"]').is(":checked") != false)
                {
                  if (IGST != '')
                  {
                    $(this).find('[id*="calIGST_"]').val(IGST);
                    $(this).find('[id*="AMTIGST_"]').val(IGSTamt);
                    $(this).find('[id*="calIGST_"]').removeAttr('readonly');
                  }
                  else
                  {
                    $(this).find('[id*="calIGST_"]').val('0');
                    $(this).find('[id*="AMTIGST_"]').val('0');
                    $(this).find('[id*="calIGST_"]').prop('readonly',true);
                    
                  }
                  if (CGST != '')
                  {
                    $(this).find('[id*="calCGST_"]').val(CGST);
                    $(this).find('[id*="AMTCGST_"]').val(CGSTamt);
                    $(this).find('[id*="calCGST_"]').removeAttr('readonly');
                  }
                  else
                  {
                    $(this).find('[id*="calCGST_"]').val('0');
                    $(this).find('[id*="AMTCGST_"]').val('0');
                    $(this).find('[id*="calCGST_"]').prop('readonly',true);
                  }
                  if (SGST != '')
                  {
                    $(this).find('[id*="calSGST_"]').val(SGST);
                    $(this).find('[id*="AMTSGST_"]').val(SGSTamt);
                    $(this).find('[id*="calSGST_"]').removeAttr('readonly');
                  }
                  else
                  {
                    $(this).find('[id*="calSGST_"]').val('0');
                    $(this).find('[id*="AMTSGST_"]').val('0');
                    $(this).find('[id*="calSGST_"]').prop('readonly',true);
                  }
                  $(this).find('[id*="TOTGSTAMT_"]').val(TotGSTamt);
                }
                else
                {
                  $(this).find('[id*="calSGST_"]').val('0');
                  $(this).find('[id*="AMTSGST_"]').val('0');
                  $(this).find('[id*="calCGST_"]').val('0');
                  $(this).find('[id*="AMTCGST_"]').val('0');
                  $(this).find('[id*="calIGST_"]').val('0');
                  $(this).find('[id*="AMTIGST_"]').val('0');
                  $(this).find('[id*="TOTGSTAMT_"]').val('0');
                  $(this).find('[id*="calIGST_"]').prop('readonly',true);
                  $(this).find('[id*="calCGST_"]').prop('readonly',true);
                  $(this).find('[id*="calSGST_"]').prop('readonly',true);
                }
            });
            var totalvalue = 0.00;
            var tvalue = 0.00;
            var ctvalue = 0.00;
            var ctgstvalue = 0.00;
            $('#Material').find('.participantRow').each(function()
            {
              tvalue = $(this).find('[id*="TT_AMT_"]').val();
              totalvalue = parseFloat(totalvalue) + parseFloat(tvalue);
              totalvalue = parseFloat(totalvalue).toFixed(2);
            });
            if($('#CTID_REF').val() != '')
            {
              $('#CT').find('.participantRow5').each(function()
              {
                ctvalue = $(this).find('[id*="VALUE"]').val();
                ctgstvalue = $(this).find('[id*="TOTGSTAMT"]').val();
                totalvalue = parseFloat(totalvalue) + parseFloat(ctvalue);
                totalvalue = parseFloat(totalvalue) + parseFloat(ctgstvalue);
                totalvalue = parseFloat(totalvalue).toFixed(2);
              });
            }
            $('#tot_amt').val(totalvalue);
            event.preventDefault();
    }

$('#GST_Reverse').on('change', function() 
{
    // bindTotalValue();
    if($('#CTID_REF').val()!='')
    {
      bindGSTCalTemplate();
    }
    bindTotalValue();
    event.preventDefault();
});

$("#Material").on('focusout', "[id*='BILL_QTY']", function() 
{
  var totalamount = 0.00;
  if($(this).val() != '' && $(this).val() != '.00' && $(this).val() > '0')
  { 
    var receivedqty = $(this).parent().parent().find('[id*="RECEIVED_QTY"]').val(); 
    if(parseFloat(receivedqty) < parseFloat($(this).val()))   
    {
        $(this).val('');
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Bill Quantity cannot be greater than Received Quantity.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        highlighFocusBtn('activeOk1');
        return false;
    }
    else
    {
        var irate = $(this).parent().parent().find('[id*="BILL_RATEPUOM"]').val();
        var mqty = $(this).val();
        $(this).parent().parent().find('[id*="IGST_AMT_"]').val('0');
        $(this).parent().parent().find('[id*="CGST_AMT_"]').val('0');
        $(this).parent().parent().find('[id*="SGST_AMT_"]').val('0');
        var tamt = parseFloat(parseFloat(mqty)*parseFloat(irate)).toFixed(2);
        var dispercnt = $(this).parent().parent().find('[id*="DISC_PER_"]').val();
        var disamt = 0 ;      
        if (dispercnt != '' && dispercnt != '.0000')
        {
           disamt =  parseFloat((parseFloat(tamt)*parseFloat(dispercnt))/100).toFixed(2);
        }
        else if ($(this).parent().parent().find('[id*="DISC_AMT_"]').val() != '' && $(this).parent().parent().find('[id*="DISC_AMT_"]').val() != '0.00')
        {
           disamt = $(this).parent().parent().find('[id*="DISC_AMT_"]').val();
        }
        tamt = parseFloat(parseFloat(tamt) - parseFloat(disamt)).toFixed(2);   
        var tp1 = $(this).parent().parent().find('[id*="IGST_"]').val();
        var tp2 = $(this).parent().parent().find('[id*="CGST_"]').val();
        var tp3 = $(this).parent().parent().find('[id*="SGST_"]').val();
        var tp1amt = parseFloat((tamt * tp1)/100).toFixed(2);
        var tp2amt = parseFloat((tamt * tp2)/100).toFixed(2);
        var tp3amt = parseFloat((tamt * tp3)/100).toFixed(2);
        var taxamt = parseFloat(parseFloat(tp1amt) + parseFloat(tp2amt) + parseFloat(tp3amt)).toFixed(2); 
        var totamt = parseFloat(parseFloat(tamt) + parseFloat(taxamt)).toFixed(2);
        
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.000');
      }
      if(intRegex.test(tamt)){
        tamt = tamt +'.00';
      }
      if(intRegex.test(totamt)){
        totamt = totamt +'.00';
      }
      if(intRegex.test(taxamt)){
        taxamt = taxamt +'.00';
      }
      if(intRegex.test(tp1amt)){
        tp1amt = tp1amt +'.00';
      }
      if(intRegex.test(tp2amt)){
        tp2amt = tp2amt +'.00';
      }
      if(intRegex.test(tp3amt)){
        tp3amt = tp3amt +'.00';
      }
      $(this).parent().parent().find('[id*="TAX_AMT_"]').val(tamt);
      $(this).parent().parent().find('[id*="TT_AMT_"]').val(totamt);
      $(this).parent().parent().find('[id*="TGST_AMT_"]').val(taxamt);
      $(this).parent().parent().find('[id*="IGST_AMT_"]').val(tp1amt);
      $(this).parent().parent().find('[id*="CGST_AMT_"]').val(tp2amt);
      $(this).parent().parent().find('[id*="SGST_AMT_"]').val(tp3amt);
      bindTotalValue();
      if($('#CTID_REF').val()!='')
      {
      bindGSTCalTemplate();
      }
      bindTotalValue();
      event.preventDefault();
    }
  }
  else
  {
      $(this).val('');
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Bill Quantity must be greater than Zero.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk1');
      return false;
  }
});

$("#Material").on('focusout', "[id*='BILL_RATEPUOM_']", function() {
  var totalamount = 0.00;
  if($(this).val() != '' && $(this).val() != '.00000' && $(this).val() > '0')
  { 
        var mqty = $(this).parent().parent().find('[id*="BILL_QTY_"]').val();
        var irate = $(this).val();
        $(this).parent().parent().find('[id*="IGST_AMT_"]').val('0');
        $(this).parent().parent().find('[id*="CGST_AMT_"]').val('0');
        $(this).parent().parent().find('[id*="SGST_AMT_"]').val('0');
        var tamt = parseFloat(parseFloat(mqty)*parseFloat(irate)).toFixed(2);
        var dispercnt = $(this).parent().parent().find('[id*="DISC_PER_"]').val();
        var disamt = 0 ;      
        if (dispercnt != '' && dispercnt != '.0000')
        {
           disamt =  parseFloat((parseFloat(tamt)*parseFloat(dispercnt))/100).toFixed(2);
        }
        else if ($(this).parent().parent().find('[id*="DISC_AMT_"]').val() != '' && $(this).parent().parent().find('[id*="DISC_AMT_"]').val() != '0.00')
        {
           disamt = $(this).parent().parent().find('[id*="DISC_AMT_"]').val();
        }
        tamt = parseFloat(parseFloat(tamt) - parseFloat(disamt)).toFixed(2);   
        var tp1 = $(this).parent().parent().find('[id*="IGST_"]').val();
        var tp2 = $(this).parent().parent().find('[id*="CGST_"]').val();
        var tp3 = $(this).parent().parent().find('[id*="SGST_"]').val();
        var tp1amt = parseFloat((tamt * tp1)/100).toFixed(2);
        var tp2amt = parseFloat((tamt * tp2)/100).toFixed(2);
        var tp3amt = parseFloat((tamt * tp3)/100).toFixed(2);
        var taxamt = parseFloat(parseFloat(tp1amt) + parseFloat(tp2amt) + parseFloat(tp3amt)).toFixed(2); 
        var totamt = parseFloat(parseFloat(tamt) + parseFloat(taxamt)).toFixed(2);
        
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.000');
      }
      if(intRegex.test(tamt)){
        tamt = tamt +'.00';
      }
      if(intRegex.test(totamt)){
        totamt = totamt +'.00';
      }
      if(intRegex.test(taxamt)){
        taxamt = taxamt +'.00';
      }
      if(intRegex.test(tp1amt)){
        tp1amt = tp1amt +'.00';
      }
      if(intRegex.test(tp2amt)){
        tp2amt = tp2amt +'.00';
      }
      if(intRegex.test(tp3amt)){
        tp3amt = tp3amt +'.00';
      }
      $(this).parent().parent().find('[id*="TAX_AMT_"]').val(tamt);
      $(this).parent().parent().find('[id*="TT_AMT_"]').val(totamt);
      $(this).parent().parent().find('[id*="TGST_AMT_"]').val(taxamt);
      $(this).parent().parent().find('[id*="IGST_AMT_"]').val(tp1amt);
      $(this).parent().parent().find('[id*="CGST_AMT_"]').val(tp2amt);
      $(this).parent().parent().find('[id*="SGST_AMT_"]').val(tp3amt);

      bindTotalValue();
      if($('#CTID_REF').val()!='')
      {
      bindGSTCalTemplate();
      }
      bindTotalValue();
      event.preventDefault();
  }
  // else
  // {
  //     $(this).val('');
  //     $("#YesBtn").hide();
  //     $("#NoBtn").hide();
  //     $("#OkBtn").hide();
  //     $("#OkBtn1").show();
  //     $("#AlertMessage").text('Amount must be greater than Zero.');
  //     $("#alert").modal('show');
  //     $("#OkBtn1").focus();
  //     highlighFocusBtn('activeOk1');
  //     return false;
  // }
});


$("#Material").on('focusout', "[id*='DISC_PER']", function() 
{
    var mqty = $(this).parent().parent().find('[id*="BILL_QTY_"]').val();
    var irate = $(this).parent().parent().find('[id*="BILL_RATEPUOM_"]').val();
    var totamt = parseFloat(parseFloat(mqty)*parseFloat(irate)).toFixed(2);
    var dpert = $(this).val();
    
    if (dpert != '' && dpert != '.0000')
    {
      var disamt = parseFloat((parseFloat(totamt)*parseFloat(dpert))/100).toFixed(2);
      var amtfd = parseFloat(parseFloat(totamt) - (parseFloat(totamt)*parseFloat(dpert))/100).toFixed(2);
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.0000')
      }
      var tp1 = $(this).parent().parent().find('[id*="IGST_"]').val();
      var tp2 = $(this).parent().parent().find('[id*="CGST_"]').val();
      var tp3 = $(this).parent().parent().find('[id*="SGST_"]').val();
      $(this).parent().parent().find('[id*="IGST_AMT_"]').val('0');
      $(this).parent().parent().find('[id*="CGST_AMT_"]').val('0');
      $(this).parent().parent().find('[id*="SGST_AMT_"]').val('0');
      var tp1amt = parseFloat((amtfd * tp1)/100).toFixed(2);
      var tp2amt = parseFloat((amtfd * tp2)/100).toFixed(2);
      var tp3amt = parseFloat((amtfd * tp3)/100).toFixed(2);
      
      var taxamt = parseFloat(parseFloat(tp1amt) + parseFloat(tp2amt) + parseFloat(tp3amt)).toFixed(2);      
      var netamt = parseFloat(parseFloat(amtfd) + parseFloat(taxamt)).toFixed(2);
      if(intRegex.test(amtfd)){
        amtfd = amtfd +'.00';
      }
      if(intRegex.test(taxamt)){
      taxamt = taxamt +'.00';
      }
      if(intRegex.test(tp1amt)){
        tp1amt = tp1amt +'.00';
      }
      if(intRegex.test(tp2amt)){
        tp2amt = tp2amt +'.00';
      }
      if(intRegex.test(tp3amt)){
        tp3amt = tp3amt +'.00';
      }
      if(intRegex.test(netamt)){
        netamt = netamt +'.00';
      }
      $(this).parent().parent().find('[id*="DISC_AMT_"]').val(disamt);
      $(this).parent().parent().find('[id*="TAX_AMT_"]').val(amtfd);
      $(this).parent().parent().find('[id*="TT_AMT"]').val(netamt);
      $(this).parent().parent().find('[id*="TGST_AMT"]').val(taxamt);
      $(this).parent().parent().find('[id*="IGST_AMT_"]').val(tp1amt);
      $(this).parent().parent().find('[id*="CGST_AMT_"]').val(tp2amt);
      $(this).parent().parent().find('[id*="SGST_AMT_"]').val(tp3amt);
    }
    else
    {
      if($(this).parent().parent().find('[id*="DISC_AMT_"]').val() != '' && $(this).parent().parent().find('[id*="DISC_AMT_"]').val() != '.00')
      {
        var disamt = $(this).parent().parent().find('[id*="DISC_AMT_"]').val();
        var disper = parseFloat((parseFloat(disamt)*100)/parseFloat(totamt)).toFixed(4);
        var amtfd = parseFloat(parseFloat(totamt) - parseFloat(disamt)).toFixed(2);
        var tp1 = $(this).parent().parent().find('[id*="IGST_"]').val();
        var tp2 = $(this).parent().parent().find('[id*="CGST_"]').val();
        var tp3 = $(this).parent().parent().find('[id*="SGST_"]').val();
        $(this).parent().parent().find('[id*="IGST_AMT_"]').val('0');
        $(this).parent().parent().find('[id*="CGST_AMT_"]').val('0');
        $(this).parent().parent().find('[id*="SGST_AMT_"]').val('0');
        var tp1amt = parseFloat((amtfd * tp1)/100).toFixed(2);
        var tp2amt = parseFloat((amtfd * tp2)/100).toFixed(2);
        var tp3amt = parseFloat((amtfd * tp3)/100).toFixed(2);
        
        var taxamt = parseFloat(parseFloat(tp1amt) + parseFloat(tp2amt) + parseFloat(tp3amt)).toFixed(2);      
        var netamt = parseFloat(parseFloat(amtfd) + parseFloat(taxamt)).toFixed(2);
        if(intRegex.test(amtfd)){
          amtfd = amtfd +'.00';
        }
        if(intRegex.test(taxamt)){
        taxamt = taxamt +'.00';
        }
        if(intRegex.test(tp1amt)){
          tp1amt = tp1amt +'.00';
        }
        if(intRegex.test(tp2amt)){
          tp2amt = tp2amt +'.00';
        }
        if(intRegex.test(tp3amt)){
          tp3amt = tp3amt +'.00';
        }
        if(intRegex.test(netamt)){
          netamt = netamt +'.00';
        }
        $(this).parent().parent().find('[id*="DISC_PER"]').val(disper);
        $(this).parent().parent().find('[id*="TAX_AMT_"]').val(amtfd);
        $(this).parent().parent().find('[id*="TT_AMT"]').val(netamt);
        $(this).parent().parent().find('[id*="TGST_AMT"]').val(taxamt);
        $(this).parent().parent().find('[id*="IGST_AMT_"]').val(tp1amt);
        $(this).parent().parent().find('[id*="CGST_AMT_"]').val(tp2amt);
        $(this).parent().parent().find('[id*="SGST_AMT_"]').val(tp3amt);
      }
      else
      {
        var amtfd = parseFloat(totamt).toFixed(2);
        var tp1 = $(this).parent().parent().find('[id*="IGST_"]').val();
        var tp2 = $(this).parent().parent().find('[id*="CGST_"]').val();
        var tp3 = $(this).parent().parent().find('[id*="SGST_"]').val();
        $(this).parent().parent().find('[id*="IGST_AMT_"]').val('0');
        $(this).parent().parent().find('[id*="CGST_AMT_"]').val('0');
        $(this).parent().parent().find('[id*="SGST_AMT_"]').val('0');
        var tp1amt = parseFloat((amtfd * tp1)/100).toFixed(2);
        var tp2amt = parseFloat((amtfd * tp2)/100).toFixed(2);
        var tp3amt = parseFloat((amtfd * tp3)/100).toFixed(2);
        var taxamt = parseFloat(parseFloat(tp1amt) + parseFloat(tp2amt) + parseFloat(tp3amt)).toFixed(2);      
        var netamt = parseFloat(parseFloat(amtfd) + parseFloat(taxamt)).toFixed(2);
        if(intRegex.test($(this).val())){
          $(this).val($(this).val()+'.00')
        }
        if(intRegex.test(amtfd)){
          amtfd = amtfd +'.00';
        }
        if(intRegex.test(taxamt)){
        taxamt = taxamt +'.00';
        }
        if(intRegex.test(tp1amt)){
          tp1amt = tp1amt +'.00';
        }
        if(intRegex.test(tp2amt)){
          tp2amt = tp2amt +'.00';
        }
        if(intRegex.test(tp3amt)){
          tp3amt = tp3amt +'.00';
        }
        if(intRegex.test(netamt)){
          netamt = netamt +'.00';
        }
        $(this).parent().parent().find('[id*="DISC_AMT_"]').val('0.00');
        $(this).parent().parent().find('[id*="DISC_PER"]').val('0.0000');
        $(this).parent().parent().find('[id*="TAX_AMT_"]').val(amtfd);
        $(this).parent().parent().find('[id*="TT_AMT_"]').val(netamt);
        $(this).parent().parent().find('[id*="TGST_AMT_"]').val(taxamt);
        $(this).parent().parent().find('[id*="IGST_AMT_"]').val(tp1amt);
        $(this).parent().parent().find('[id*="CGST_AMT_"]').val(tp2amt);
        $(this).parent().parent().find('[id*="SGST_AMT_"]').val(tp3amt);
      }
    }
    bindTotalValue();
    if($('#CTID_REF').val()!='')
    {
    bindGSTCalTemplate();
    }
    bindTotalValue();
    event.preventDefault();  
});

$("#Material").on('focusout', "[id*='DISC_AMT_']", function() 
{
  var mqty = $(this).parent().parent().find('[id*="BILL_QTY_"]').val();
  var irate = $(this).parent().parent().find('[id*="BILL_RATEPUOM_"]').val();
  var totamt = parseFloat(parseFloat(mqty)*parseFloat(irate)).toFixed(2);

  if($(this).val() != '' && $(this).val() != '.00')
  {
    if(intRegex.test($(this).val())){
      $(this).val($(this).val() +'.00');
    }
      var disamt = $(this).val();
      var disper = parseFloat((parseFloat(disamt)*100)/parseFloat(totamt)).toFixed(4);
      var amtfd = parseFloat(parseFloat(totamt) - parseFloat(disamt)).toFixed(2);
      var tp1 = $(this).parent().parent().find('[id*="IGST_"]').val();
      var tp2 = $(this).parent().parent().find('[id*="CGST_"]').val();
      var tp3 = $(this).parent().parent().find('[id*="SGST_"]').val();
      $(this).parent().parent().find('[id*="IGST_AMT_"]').val('0');
      $(this).parent().parent().find('[id*="CGST_AMT_"]').val('0');
      $(this).parent().parent().find('[id*="SGST_AMT_"]').val('0');
      var tp1amt = parseFloat((amtfd * tp1)/100).toFixed(2);
      var tp2amt = parseFloat((amtfd * tp2)/100).toFixed(2);
      var tp3amt = parseFloat((amtfd * tp3)/100).toFixed(2);
      
      var taxamt = parseFloat(parseFloat(tp1amt) + parseFloat(tp2amt) + parseFloat(tp3amt)).toFixed(2);      
      var netamt = parseFloat(parseFloat(amtfd) + parseFloat(taxamt)).toFixed(2);
      if(intRegex.test(amtfd)){
        amtfd = amtfd +'.00';
      }
      if(intRegex.test(taxamt)){
      taxamt = taxamt +'.00';
      }
      if(intRegex.test(tp1amt)){
        tp1amt = tp1amt +'.00';
      }
      if(intRegex.test(tp2amt)){
        tp2amt = tp2amt +'.00';
      }
      if(intRegex.test(tp3amt)){
        tp3amt = tp3amt +'.00';
      }
      if(intRegex.test(netamt)){
        netamt = netamt +'.00';
      }
      $(this).parent().parent().find('[id*="DISC_PER"]').val(disper);
      $(this).parent().parent().find('[id*="TAX_AMT_"]').val(amtfd);
      $(this).parent().parent().find('[id*="TT_AMT"]').val(netamt);
      $(this).parent().parent().find('[id*="TGST_AMT"]').val(taxamt);
      $(this).parent().parent().find('[id*="IGST_AMT_"]').val(tp1amt);
      $(this).parent().parent().find('[id*="CGST_AMT_"]').val(tp2amt);
      $(this).parent().parent().find('[id*="SGST_AMT_"]').val(tp3amt);
  }
  else
  {
    if($(this).parent().parent().find('[id*="DISC_PER"]').val() != '' && $(this).parent().parent().find('[id*="DISC_PER"]').val() != '.00')
    {
      var disper = $(this).parent().parent().find('[id*="DISC_PER"]').val();
      var disamt = parseFloat((parseFloat(disamt)*parseFloat(totamt))/100).toFixed(2);
      var amtfd = parseFloat(parseFloat(totamt) - parseFloat(disamt)).toFixed(2);
      var tp1 = $(this).parent().parent().find('[id*="IGST_"]').val();
      var tp2 = $(this).parent().parent().find('[id*="CGST_"]').val();
      var tp3 = $(this).parent().parent().find('[id*="SGST_"]').val();
      $(this).parent().parent().find('[id*="IGST_AMT_"]').val('0');
      $(this).parent().parent().find('[id*="CGST_AMT_"]').val('0');
      $(this).parent().parent().find('[id*="SGST_AMT_"]').val('0');
      var tp1amt = parseFloat((amtfd * tp1)/100).toFixed(2);
      var tp2amt = parseFloat((amtfd * tp2)/100).toFixed(2);
      var tp3amt = parseFloat((amtfd * tp3)/100).toFixed(2);
      
      var taxamt = parseFloat(parseFloat(tp1amt) + parseFloat(tp2amt) + parseFloat(tp3amt)).toFixed(2);      
      var netamt = parseFloat(parseFloat(amtfd) + parseFloat(taxamt)).toFixed(2);
      if(intRegex.test(amtfd)){
        amtfd = amtfd +'.00';
      }
      if(intRegex.test(taxamt)){
      taxamt = taxamt +'.00';
      }
      if(intRegex.test(tp1amt)){
        tp1amt = tp1amt +'.00';
      }
      if(intRegex.test(tp2amt)){
        tp2amt = tp2amt +'.00';
      }
      if(intRegex.test(tp3amt)){
        tp3amt = tp3amt +'.00';
      }
      if(intRegex.test(netamt)){
        netamt = netamt +'.00';
      }
      $(this).parent().parent().find('[id*="DISC_AMT_"]').val(disamt);
      $(this).parent().parent().find('[id*="TAX_AMT_"]').val(amtfd);
      $(this).parent().parent().find('[id*="TT_AMT"]').val(netamt);
      $(this).parent().parent().find('[id*="TGST_AMT"]').val(taxamt);
      $(this).parent().parent().find('[id*="IGST_AMT_"]').val(tp1amt);
      $(this).parent().parent().find('[id*="CGST_AMT_"]').val(tp2amt);
      $(this).parent().parent().find('[id*="SGST_AMT_"]').val(tp3amt);
    }
    else
    {
      var amtfd = parseFloat(totamt).toFixed(2);
      var tp1 = $(this).parent().parent().find('[id*="IGST_"]').val();
      var tp2 = $(this).parent().parent().find('[id*="CGST_"]').val();
      var tp3 = $(this).parent().parent().find('[id*="SGST_"]').val();
      $(this).parent().parent().find('[id*="IGST_AMT_"]').val('0');
      $(this).parent().parent().find('[id*="CGST_AMT_"]').val('0');
      $(this).parent().parent().find('[id*="SGST_AMT_"]').val('0');
      var tp1amt = parseFloat((amtfd * tp1)/100).toFixed(2);
      var tp2amt = parseFloat((amtfd * tp2)/100).toFixed(2);
      var tp3amt = parseFloat((amtfd * tp3)/100).toFixed(2);
      var taxamt = parseFloat(parseFloat(tp1amt) + parseFloat(tp2amt) + parseFloat(tp3amt)).toFixed(2);      
      var netamt = parseFloat(parseFloat(amtfd) + parseFloat(taxamt)).toFixed(2);
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.00')
      }
      if(intRegex.test(amtfd)){
        amtfd = amtfd +'.00';
      }
      if(intRegex.test(taxamt)){
      taxamt = taxamt +'.00';
      }
      if(intRegex.test(tp1amt)){
        tp1amt = tp1amt +'.00';
      }
      if(intRegex.test(tp2amt)){
        tp2amt = tp2amt +'.00';
      }
      if(intRegex.test(tp3amt)){
        tp3amt = tp3amt +'.00';
      }
      if(intRegex.test(netamt)){
        netamt = netamt +'.00';
      }
      $(this).parent().parent().find('[id*="DISC_AMT_"]').val('0.00');
      $(this).parent().parent().find('[id*="DISC_PER"]').val('0.0000');
      $(this).parent().parent().find('[id*="TAX_AMT_"]').val(amtfd);
      $(this).parent().parent().find('[id*="TT_AMT_"]').val(netamt);
      $(this).parent().parent().find('[id*="TGST_AMT_"]').val(taxamt);
      $(this).parent().parent().find('[id*="IGST_AMT_"]').val(tp1amt);
      $(this).parent().parent().find('[id*="CGST_AMT_"]').val(tp2amt);
      $(this).parent().parent().find('[id*="SGST_AMT_"]').val(tp3amt);
    }
  }
  bindTotalValue();
  if($('#CTID_REF').val()!='')
  {
  bindGSTCalTemplate();
  }
  bindTotalValue();
  event.preventDefault();  
});



$("#CT").on('change',"[id*='calGST']",function() {
      if ($(this).is(":checked") == true){
        if($.trim($('#Tax_State').val()) == 'OutofState')
          {
            $(this).parent().parent().find('[id*="calIGST"]').removeAttr('disabled');
            $(this).parent().parent().find('[id*="calIGST"]').removeAttr('readonly');
            $(this).parent().parent().find('[id*="calCGST"]').prop('disabled','true');
            $(this).parent().parent().find('[id*="calCGST"]').val('0');
            $(this).parent().parent().find('[id*="calSGST"]').prop('disabled','true');
            $(this).parent().parent().find('[id*="calSGST"]').val('0');
            $(this).parent().parent().find('[id*="AMTIGST"]').removeAttr('disabled');
            $(this).parent().parent().find('[id*="AMTIGST"]').removeAttr('readonly');
            $(this).parent().parent().find('[id*="AMTCGST"]').prop('disabled','true');
            $(this).parent().parent().find('[id*="AMTSGST"]').prop('disabled','true');
            $(this).parent().parent().find('[id*="AMTCGST"]').val('0');
            $(this).parent().parent().find('[id*="AMTSGST"]').val('0');
            $(this).parent().parent().find('[id*="TOTGSTAMT"]').val('0');
            bindTotalValue();
            event.preventDefault();
          }
          else
          {
            $(this).parent().parent().find('[id*="calIGST"]').prop('disabled','true');
            $(this).parent().parent().find('[id*="calCGST"]').removeAttr('disabled');
            $(this).parent().parent().find('[id*="calSGST"]').removeAttr('disabled');
            $(this).parent().parent().find('[id*="calCGST"]').removeAttr('readonly');
            $(this).parent().parent().find('[id*="calSGST"]').removeAttr('readonly');
            $(this).parent().parent().find('[id*="AMTIGST"]').prop('disabled','true');
            $(this).parent().parent().find('[id*="AMTCGST"]').removeAttr('disabled');
            $(this).parent().parent().find('[id*="AMTSGST"]').removeAttr('disabled');
            $(this).parent().parent().find('[id*="AMTCGST"]').removeAttr('readonly');
            $(this).parent().parent().find('[id*="AMTSGST"]').removeAttr('readonly');
            $(this).parent().parent().find('[id*="calIGST"]').val('0');
            $(this).parent().parent().find('[id*="AMTIGST"]').val('0');
            $(this).parent().parent().find('[id*="TOTGSTAMT"]').val('0');
            bindTotalValue();
            event.preventDefault();
          }
      }
      else
      {
          $(this).parent().parent().find('[id*="calIGST"]').prop('disabled','true');
          $(this).parent().parent().find('[id*="calCGST"]').prop('disabled','true');
          $(this).parent().parent().find('[id*="calSGST"]').prop('disabled','true');
          $(this).parent().parent().find('[id*="AMTIGST"]').prop('disabled','true');
          $(this).parent().parent().find('[id*="AMTCGST"]').prop('disabled','true');
          $(this).parent().parent().find('[id*="AMTSGST"]').prop('disabled','true');
          $(this).parent().parent().find('[id*="calIGST"]').val('0');
          $(this).parent().parent().find('[id*="calCGST"]').val('0');
          $(this).parent().parent().find('[id*="calSGST"]').val('0');
          $(this).parent().parent().find('[id*="AMTIGST"]').val('0');
          $(this).parent().parent().find('[id*="AMTCGST"]').val('0');
          $(this).parent().parent().find('[id*="AMTSGST"]').val('0');
          $(this).parent().parent().find('[id*="TOTGSTAMT"]').val('0');
          bindTotalValue();
          event.preventDefault();
      }
  });
  $("#CT").on('change',"[id*='calIGST_']",function() {
      var rate = $(this).val();
      var total = $(this).parent().parent().find('[id*="VALUE_"]').val();
      var gstamt = parseFloat((rate*total)/100).toFixed(2);
      var totgst = $(this).parent().parent().find('[id*="TOTGSTAMT_"]').val();
      totgst = parseFloat(parseFloat(gstamt)).toFixed(2);;
      $(this).parent().parent().find('[id*="AMTIGST_"]').val(gstamt);
      $(this).parent().parent().find('[id*="TOTGSTAMT_"]').val(totgst);
      bindTotalValue();
      if(intRegex.test($(this).val())){
        $(this).val($(this).val() +'.0000');
      }
      event.preventDefault();
  });
  $("#CT").on('change',"[id*='calCGST_']",function() {
      var rate2 = $(this).val();
      var total2 = $(this).parent().parent().find('[id*="VALUE_"]').val();
      var gstamt2 = parseFloat((rate2*total2)/100).toFixed(2);
      var totgst2 = $(this).parent().parent().find('[id*="TOTGSTAMT_"]').val();
      var sgstamt = $(this).parent().parent().find('[id*="AMTSGST_"]').val();
      totgst2 = parseFloat(parseFloat(sgstamt) + parseFloat(gstamt2)).toFixed(2);
      $(this).parent().parent().find('[id*="AMTCGST_"]').val(gstamt2);
      $(this).parent().parent().find('[id*="TOTGSTAMT_"]').val(totgst2);
      bindTotalValue();
      if(intRegex.test($(this).val())){
        $(this).val($(this).val() +'.0000');
      }
      event.preventDefault();
  }); 
  $("#CT").on('change',"[id*='calSGST_']",function() {
      var rate3 = $(this).val();
      var total3 = $(this).parent().parent().find('[id*="VALUE_"]').val();
      var gstamt3 = parseFloat((rate3*total3)/100).toFixed(2);
      var totgst3 = $(this).parent().parent().find('[id*="TOTGSTAMT_"]').val();
      var cgstamt = $(this).parent().parent().find('[id*="AMTCGST_"]').val();
      totgst3 = parseFloat(parseFloat(cgstamt) + parseFloat(gstamt3)).toFixed(2);;
      $(this).parent().parent().find('[id*="AMTSGST_"]').val(gstamt3);
      $(this).parent().parent().find('[id*="TOTGSTAMT_"]').val(totgst3);
      bindTotalValue();
      if(intRegex.test($(this).val())){
        $(this).val($(this).val() +'.0000');
      }
      event.preventDefault();
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
  $clone.find('input:text').removeAttr('disabled');
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
  window.location.href = "{{route('transaction',[$FormId,'add'])}}";
}//fnUndoYes




});
</script>

@endpush

@push('bottom-scripts')
<script>

$(document).ready(function() {

  $("#btnSave").on("submit", function( event ) {

    if ($("#add_trn_form").valid()) {
        // Do something
        alert( "Handler for .submit() called." );
        event.preventDefault();
    }
});


    $('#add_trn_form1').bootstrapValidator({       
        fields: {
            txtlabel: {
                validators: {
                    notEmpty: {
                        message: 'The Document Number is required'
                    }
                }
            },            
        },
        submitHandler: function(validator, form, submitButton) {
            alert( "Handler for .submit() called." );
             event.preventDefault();
             $("#add_trn_form").submit();
        }
    });
});



function validateForm(){
 
  $("#FocusId").val('');

  var JWINO          =   $.trim($("#JWINO").val());
  var JWIDT          =   $.trim($("#JWIDT").val());
  var DEPID_REF      =   $.trim($("#DEPID_REF").val());
  var VID_REF        =   $.trim($("#VID_REF").val());
  var VENDOR_INNO    =   $.trim($("#VENDOR_INNO").val());
  var VENDOR_INDT    =   $.trim($("#VENDOR_INDT").val());

  if(JWINO ===""){
    $("#FocusId").val('JWINO');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Select Doc No.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if(JWIDT ===""){
    $("#FocusId").val('JWIDT');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Select Doc Date.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }  
  else if(DEPID_REF ===""){
      $("#FocusId").val('txtdepartment');        
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please Select Department.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
     return false;
 }
 else if(VID_REF ===""){
      $("#FocusId").val('txtvendor');        
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please Select Vender.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
     return false;
 }
 else if(VENDOR_INNO ===""){
     $("#FocusId").val('VENDOR_INNO');        
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please Enter Vendor Invoice No.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();

     return false;
 }
 else if(VENDOR_INDT ===""){
      $("#FocusId").val('VENDOR_INDT');        
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please Select Vendor Invoice Date.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();

     return false;
 }
 else{
    event.preventDefault();
    var allblank  = [];
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

    var focustext    = "";
    var focustext1   = "";
    var focustext2   = "";
    var focustext3   = "";
    var focustext4   = "";
    var focustext5   = "";
    var focustext6   = "";
    var focustext7   = "";
    var focustext8   = "";
    var focustext9   = "";
    var focustext10  = "";
    var focustext11  = "";
    var focustext12  = "";

        $('#Material').find('.participantRow').each(function()
        {    
            if($.trim($(this).find("[id*=GRJID_REF_]").val())!="")
            {

                  allblank.push('true');
                  if($.trim($(this).find("[id*=ITEMID_REF_]").val())!="")
                  {
                        allblank2.push('true');
                        if($.trim($(this).find("[id*=BILL_QTY_]").val())!="" && parseFloat($(this).find("[id*=BILL_QTY_]").val()) > 0 && 
                              $(this).find("[id*=BILL_QTY_]").val() != '.00')
                        {
                              allblank3.push('true');
                        }
                        else
                        {
                              allblank3.push('false');
                        }

                        if($.trim($(this).find("[id*=BILL_RATEPUOM_]").val())!="")
                        {
                          allblank4.push('true');
                        }
                        else
                        {
                          allblank4.push('false');
                          focustext4 = $(this).find("[id*=BILL_RATEPUOM]").attr('id');
                        }

                        if($.trim($(this).find("[id*=BILL_RATEPUOM_]").val())!="" && parseFloat($(this).find("[id*=BILL_RATEPUOM_]").val()) > 0 )
                        {
                          allblank4.push('true');
                        }
                        else
                        {
                          allblank4.push('false');
                          focustext4 = $(this).find("[id*=BILL_RATEPUOM]").attr('id');
                        }

                        if($.trim($('#Tax_State').val())=="WithinState")
                        {
                          if($.trim($(this).find("[id*=IGST]").val())!="")
                          {
                            allblank5.push('true');
                          }
                          else
                          {
                            allblank5.push('true');
                          }
                        }
                        else
                        {
                          if($.trim($(this).find("[id*=CGST]").val())!="")
                          {
                            allblank5.push('true');
                          }
                          else
                          {
                            allblank5.push('true');
                          }
                          if($.trim($(this).find("[id*=SGST]").val())!="")
                          {
                            allblank5.push('true');
                          }
                          else
                          {
                            allblank5.push('true');
                          }
                        }
                  }
                  else
                  {
                        allblank2.push('false');
                        focustext2 = $(this).find("[id*=txtItem]").attr('id');
                  }
            }
            else
            {
                  allblank.push('false');
                  focustext = $(this).find("[id*=txtGRN_NO]").attr('id');
            }
        });
        if($('#TNCID_REF').val() !="")
        {
            $('#TC').find('.participantRow3').each(function(){
              if($.trim($(this).find("[id*=TNCDID_REF]").val())!="")
                {
                    allblank6.push('true');
                        if($.trim($(this).find("[id*=TNCismandatory]").val())=="1"){
                              if($.trim($(this).find('[id*="tncdetvalue"]').val()) != "")
                              {
                                allblank7.push('true');
                              }
                              else
                              {
                                allblank7.push('false');
                              } 
                        } 
                }
                else
                {
                    allblank6.push('false');
                } 
            });
        }
        if($('#CTID_REF').val() !="")
        {
            $('#CT').find('.participantRow5').each(function(){
              if($.trim($(this).find("[id*=TID_REF]").val())!="")
                {
                  allblank8.push('true');
                        if($(this).find("[id*=calGST]").is(":checked") == true)
                        {
                          if($.trim($('#Tax_State').val())!="WithinState")
                          {
                            if($.trim($(this).find("[id*=calIGST]").val())!="0")
                            {
                              allblank9.push('true');
                            }
                            else
                            {
                              allblank9.push('false');
                            }
                          }
                          else
                          {
                            if($.trim($(this).find("[id*=calCGST]").val())!="0")
                            {
                              allblank9.push('true');
                            }
                            else
                            {
                              allblank9.push('false');
                            }
                            if($.trim($(this).find("[id*=calSGST]").val())!="0")
                            {
                              allblank9.push('true');
                            }
                            else
                            {
                              allblank9.push('false');
                            }
                          }
                        } 
                }
                else
                {
                    allblank8.push('false');
                } 
            });
        }

        $('#udf').find('.participantRow4').each(function(){
              if($.trim($(this).find("[id*=UDFJWIID_REF_]").val())!="")
                {
                    allblank8.push('true');
                        if($.trim($(this).find("[id*=UDFismandatory]").val())=="1"){
                              if($.trim($(this).find('[id*="udfvalue"]').val()) != "")
                              {
                                allblank11.push('true');
                              }
                              else
                              {
                                allblank11.push('false');
                              }
                        }  
                }                
        });

    }
        
        if(jQuery.inArray("false", allblank) !== -1){
          $("#FocusId").val(focustext);
          $("#alert").modal('show');
          $("#AlertMessage").text('Please select GRN in Material Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
          }
        else if(jQuery.inArray("false", allblank2) !== -1){
          $("#FocusId").val(focustext2);
          $("#alert").modal('show');
          $("#AlertMessage").text('Please select Item in Material Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
          }
        else if(jQuery.inArray("false", allblank3) !== -1){
          $("#FocusId").val(focustext3);
          $("#FocusId").val($("#tot_amt"));
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn1").show();
          $("#AlertMessage").text('Bill Quantity must be greater than Zero in Material Tab.');
          $("#alert").modal('show');
          $("#OkBtn1").focus();
          return false;
          } 
        else if(jQuery.inArray("false", allblank4) !== -1){
          $("#FocusId").val(focustext4);
          $("#FocusId").val($("#tot_amt"));
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn1").show();
          $("#AlertMessage").text('Rate must be greater than Zero in Material Tab.');
          $("#alert").modal('show');
          $("#OkBtn1").focus();
          return false;
          }
        else if(jQuery.inArray("false", allblank5) !== -1){
          $("#FocusId").val($("#tot_amt"));
          $("#ProceedBtn").focus();
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn1").show();
          $("#AlertMessage").text('Please enter GST Rate in Material Tab.');
          $("#alert").modal('show');
          $("#OkBtn1").focus();
          return false;
          }
        else if(jQuery.inArray("false", allblank7) !== -1){
          $("#FocusId").val($("#tot_amt"));
          $("#ProceedBtn").focus();
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn1").show();
          $("#AlertMessage").text('Please enter Value / Comment in T&C Tab.');
          $("#alert").modal('show');
          $("#OkBtn1").focus();
          return false;
          } 
        else if(jQuery.inArray("false", allblank9) !== -1){
          $("#FocusId").val($("#tot_amt"));
          $("#ProceedBtn").focus();
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn1").show();
          $("#AlertMessage").text('Please enter GST Rate in Calculation Tab.');
          $("#alert").modal('show');
          $("#OkBtn1").focus();
          return false;
          }

        else if(jQuery.inArray("false", allblank11) !== -1){
          $("#FocusId").val($("#tot_amt"));
          $("#ProceedBtn").focus();
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn1").show();
          $("#AlertMessage").text('Please enter Value / Comment in UDF Tab.');
          $("#alert").modal('show');
          $("#OkBtn1").focus();
          return false;
          }
          else if(checkPeriodClosing('{{$FormId}}',$("#JWIDT").val(),0) ==0){
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn").hide();
          $("#OkBtn1").show();
          $("#AlertMessage").text(period_closing_msg);
          $("#alert").modal('show');
          $("#OkBtn1").focus();
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

$("#YesBtn").click(function(){

$("#alert").modal('hide');
var customFnName = $("#YesBtn").data("funcname");
    window[customFnName]();

}); //yes button

$("#btnSave" ).click(function() {
    var formReqData = $("#add_trn_form");
    if(formReqData.valid()){
      validateForm();
    }
});

window.fnSaveData = function (){

//validate and save data
event.preventDefault();

     var trnseForm = $("#add_trn_form");
    var formData = trnseForm.serialize();
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
            // window.location.href="{{ route('transaction',[90,'index']) }}";
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
            // window.location.href="{{ route('transaction',[90,'index']) }}";
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
function showAlert(msg){
  $("#alert").modal('show');
  $("#AlertMessage").text(msg);
  $("#YesBtn").hide(); 
  $("#NoBtn").hide();  
  $("#OkBtn1").show();
  $("#OkBtn1").focus();
  highlighFocusBtn('activeOk');
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
  var FocusId = $("#FocusId").val();
  $("#"+FocusId).focus();
  $("#closePopup").click();
}

function highlighFocusBtn(pclass){
    $(".activeYes").hide();
    $(".activeNo").hide();
    $("."+pclass+"").show();
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