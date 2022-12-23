@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
    <div class="row">
        <div class="col-lg-2">
        <a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Gate Entry (GE) against JWO</a>
        </div>

        <div class="col-lg-10 topnav-pd">
                <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
                <button class="btn topnavbt" id="btnSaveSE" ><i class="fa fa-floppy-o"></i> Save</button>
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
  <div class="container-fluid filter">
	  <div class="inner-form">
		  <div class="row">
        <div class="col-lg-2 pl"><p>GE No</p></div>
        <div class="col-lg-2 pl">
              <input type="text" name="GE_NO" id="GE_NO" value="{{$docarray['DOC_NO']}}" {{$docarray['READONLY']}} class="form-control" maxlength="{{$docarray['MAXLENGTH']}}" autocomplete="off" style="text-transform:uppercase" >
              <script>docMissing(@json($docarray['FY_FLAG']));</script>
              <span class="text-danger" id="ERROR_GE_NO"></span> 
        </div>
			
        <div class="col-lg-2 pl"><p>GE Date</p></div>
        <div class="col-lg-2 pl">
              <input type="date" name="GE_DT" id="GE_DT" onchange='checkPeriodClosing("{{$FormId}}",this.value,1),getDocNoByEvent("GE_NO",this,@json($doc_req))' value="{{ old('GE_DT') }}" class="form-control mandatory" autocomplete="off" >
        </div>

        <div class="col-lg-2 pl"><p>GE Time</p></div>
        <div class="col-lg-2 pl">
              <input type="time" name="GE_TM" id="GE_TM" value="{{date('H:i',strtotime(date('H:i:s'))+strtotime('05:30:00'))}}" class="form-control mandatory"  autocomplete="off"  >
        </div>
      </div>


      <div class="row">
        <div class="col-lg-2 pl"><p>Security Supervisor</p></div>
        <div class="col-lg-2 pl">
          <input type="text" name="SECURITY_SUP" id="SECURITY_SUP" maxlength="100" class="form-control" autocomplete="off"   >
        </div>

        <div class="col-lg-2 pl"><p>Security Guard</p></div>
        <div class="col-lg-2 pl">
          <input type="text" name="SECURITY_GUARD" id="SECURITY_GUARD" maxlength="100" class="form-control" autocomplete="off"   >
        </div>

        <div class="col-lg-2 pl"><p>Vendor Name</p></div>
        <div class="col-lg-2 pl">
          <input type="text"    name="VID_REF_Details" id="VID_REF_Details" class="form-control mandatory"  autocomplete="off" readonly/>
          <input type="hidden"  name="VID_REF"         id="VID_REF" class="form-control" autocomplete="off" />
        </div>
      </div>

     
      <div class="row">

        <div class="col-lg-2 pl"><p>JWO No</p></div>
        <div class="col-lg-2 pl">
            <input type="text" name="JWO_NO_Details" id="JWO_NO_Details" class="form-control mandatory"  autocomplete="off" readonly/>
            <input type="hidden" name="JWO_NO" id="JWO_NO" class="form-control" autocomplete="off" />
        </div>

        <div class="col-lg-2 pl"><p>Vendor Bill No</p></div>
        <div class="col-lg-2 pl">
              <input type="text" name="VENDOR_BILLNO" id="VENDOR_BILLNO" maxlength="20" onkeyup="deleteDate('VENDOR_BILLDT',this.value);"  class="form-control" autocomplete="off"   >
        </div>

        <div class="col-lg-2 pl"><p>Bill Date</p></div>
        <div class="col-lg-2 pl">
            <input type="date" name="VENDOR_BILLDT" id="VENDOR_BILLDT"  class="form-control" autocomplete="off"   >
        </div>

      </div>

    <div class="row">
     

      <div class="col-lg-2 pl"><p>Vendor Challan No</p></div>
        <div class="col-lg-2 pl">
          <input type="text" name="VENDOR_CHNO" id="VENDOR_CHNO" onkeyup="deleteDate('VENDOR_CHDT',this.value);" maxlength="20"  class="form-control" autocomplete="off"   >
        </div>

      <div class="col-lg-2 pl"><p>Challan Date</p></div>
			<div class="col-lg-2 pl">
				<input type="date" name="VENDOR_CHDT" id="VENDOR_CHDT"  class="form-control" autocomplete="off"   >
      </div>

      <div class="col-lg-2 pl"><p>Gate No</p></div>
      <div class="col-lg-2 pl">
        <input type="text" name="GATE_NO" id="GATE_NO" maxlength="15"  class="form-control" autocomplete="off"   >
      </div>

    </div>

    <div class="row">
      
      <div class="col-lg-2 pl"><p>Gate Register No</p></div>
      <div class="col-lg-2 pl">
        <input type="text" name="GATE_RGNO" id="GATE_RGNO" onkeyup="deleteDate('DATE',this.value);"  maxlength="15"  class="form-control" autocomplete="off"   >
      </div>

      <div class="col-lg-2 pl"><p>Date</p></div>
			<div class="col-lg-2 pl">
				  <input type="date" name="DATE" id="DATE"  class="form-control" autocomplete="off"   >
      </div>

      <div class="col-lg-2 pl"><p>Remarks</p></div>
      <div class="col-lg-2 pl">
        <input type="text" name="REMARKS" id="REMARKS" maxlength="200"  class="form-control" autocomplete="off"   >
      </div>
    </div>

	</div>

	<div class="container-fluid">

		<div class="row">
			<ul class="nav nav-tabs">
				<li class="active"><a data-toggle="tab" href="#Vehicle" id="VehicleTab">Vehicle</a></li>
        <li><a data-toggle="tab" href="#Unloading" id="UnloadingTab">Unloading</a></li>
				<li><a data-toggle="tab" href="#udf" id="udfTab" >UDF</a></li>
			</ul>
			
			<div class="tab-content">

				<div id="Vehicle" class="tab-pane fade in active">
          <div class="inner-form">
            <div class="row">
              <div class="col-lg-2 pl"><p>LR No</p></div>
              <div class="col-lg-2 pl">
                <input type="text" name="LR_NO" id="LR_NO" onkeyup="deleteDate('LR_DT',this.value);" maxlength="30" class="form-control" autocomplete="off" >
              </div>
          
              <div class="col-lg-2 pl"><p>LR Date</p></div>
              <div class="col-lg-2 pl">
                    <input type="date" name="LR_DT" id="LR_DT" class="form-control" autocomplete="off" >
              </div>

              <div class="col-lg-2 pl"><p>Primary Mode of Transport</p></div>
              <div class="col-lg-2 pl">
                <select  name="TRANSPORT_MODE" id="TRANSPORT_MODE" class="form-control" autocomplete="off" >
                  <option value="">Select</option>
                  <option value="By Road">By Road</option>
                  <option value="By Sea">By Sea</option>
                  <option value="By Air">By Air</option>
                </select>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-2 pl"><p>Vehicle No</p></div>
              <div class="col-lg-2 pl">
                <input type="text" name="VEHICLE_NO" id="VEHICLE_NO" maxlength="30" class="form-control" autocomplete="off" >
              </div>
          
              <div class="col-lg-2 pl"><p>Vehicle Category</p></div>
              <div class="col-lg-2 pl">
                <input type="text" name="VEHICLE_CAT" id="VEHICLE_CAT" maxlength="100" class="form-control" autocomplete="off" >
              </div>

              <div class="col-lg-2 pl"><p>Transporter</p></div>
              <div class="col-lg-2 pl">
                <input type="text" name="TRANSPORTER_Details" id="TRANSPORTER_Details" class="form-control"  autocomplete="off" readonly/>
                <input type="hidden" name="TRANSPORTER" id="TRANSPORTER" class="form-control" autocomplete="off" />
              </div>
            </div>

            <div class="row">
              <div class="col-lg-2 pl"><p>Driver Name</p></div>
              <div class="col-lg-2 pl">
                <input type="text" name="DRIVER_NAME" id="DRIVER_NAME" maxlength="100" class="form-control" autocomplete="off" >
              </div>
          
              <div class="col-lg-2 pl"><p>Weighment Machine</p></div>
              <div class="col-lg-2 pl">
                    <input type="text" name="WEIGHMENT_MACHINE" id="WEIGHMENT_MACHINE" class="form-control" autocomplete="off" >
              </div>

              <div class="col-lg-2 pl"><p>Weighment Slip No</p></div>
              <div class="col-lg-2 pl">
                <input type="text" name="WEIGHMENT_SLIP" id="WEIGHMENT_SLIP"  maxlength="10"  class="form-control"  autocomplete="off"/>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-2 pl"><p>Gross Weight (KGS)</p></div>
              <div class="col-lg-2 pl">
                <input type="text" name="GROSS_WEIGHT" id="GROSS_WEIGHT"  onkeyup="weightCalculation();" maxlength="50" onkeypress="return isNumberDecimalKey(event,this)" class="form-control" autocomplete="off" >
              </div>

              <div class="col-lg-2 pl"><p>Tare Weight (KGS)</p></div>
              <div class="col-lg-2 pl">
                    <input type="text" name="TARE_WEIGHT" id="TARE_WEIGHT" onkeyup="weightCalculation();" maxlength="50" onkeypress="return isNumberDecimalKey(event,this)" class="form-control" autocomplete="off" >
              </div>

              <div class="col-lg-2 pl"><p>Net Weight (KGS)</p></div>
              <div class="col-lg-2 pl">
                <input type="text" name="NET_WEIGHT" id="NET_WEIGHT" maxlength="50" onkeypress="return isNumberDecimalKey(event,this)" class="form-control" autocomplete="off" readonly  >
              </div>
            </div>


          </div>

				</div>

        <div id="Unloading" class="tab-pane fade">	
          <div class="inner-form">
            <div class="row">
                <div class="col-lg-2 pl"><p>Unloading Bay No</p></div>
                <div class="col-lg-2 pl">
                  <input type="text" name="UNL_BAYNO" id="UNL_BAYNO" maxlength="30" class="form-control" autocomplete="off" >
                </div>
            
                <div class="col-lg-2 pl"><p>Stock Custodian</p></div>
                <div class="col-lg-2 pl">
                      <input type="text" name="ST_CUSTODIAN" id="ST_CUSTODIAN" class="form-control" autocomplete="off" >
                </div>

                <div class="col-lg-2 pl"><p>Unload Method</p></div>
                <div class="col-lg-2 pl">
                  <input type="text" name="UNL_METHOD" id="UNL_METHOD"  maxlength="100"  class="form-control"  autocomplete="off"/>
                </div>
              </div>

              <div class="row">
                <div class="col-lg-2 pl"><p>Primary Packing Type</p></div>
                <div class="col-lg-2 pl">
                  
                  <input type="text" name="PTID_REF_Details" id="PTID_REF_Details" class="form-control"   autocomplete="off" readonly/>
                    <input type="hidden" name="PTID_REF" id="PTID_REF" class="form-control" autocomplete="off" />
                </div>
            
                <div class="col-lg-2 pl"><p>No of Packing</p></div>
                <div class="col-lg-2 pl">
                      <input type="text" name="PACKING_NO" id="PACKING_NO" onkeypress="return isNumberDecimalKey(event,this)" class="form-control" autocomplete="off" >
                </div>

                <div class="col-lg-2 pl"><p>Primary Packaging Condition</p></div>
                <div class="col-lg-2 pl">
                  <input type="text" name="PACKING_CONDITION" id="PACKING_CONDITION"  maxlength="200"  class="form-control"  autocomplete="off"/>
                </div>
              </div>
          </div>
				</div>

				<div id="udf" class="tab-pane fade">
					<div class="table-responsive table-wrapper-scroll-y " style="margin-top:10px;height:280px;width:50%;">
						<table id="example3" class="display nowrap table table-striped table-bordered itemlist" style="height:auto !important;">
							<thead id="thead1"  style="position: sticky;top: 0">
							  <tr >
								<th>UDF Fields<input class="form-control" type="hidden" name="Row_Count2" id ="Row_Count2"></th>
								<th>Value / Comments</th>
								<th>Action</th>
							  </tr>
							</thead>
							<tbody>
              @foreach($objUdfData as $uindex=>$uRow)
                <tr  class="participantRow3">
                    <td><input type="text" name={{"popupSEID_".$uindex}} id={{"popupSEID_".$uindex}} class="form-control" value="{{$uRow->LABEL}}" autocomplete="off"  readonly/></td>
                    <td hidden><input type="hidden" name={{"UDF_".$uindex}} id={{"UDF_".$uindex}} class="form-control" value="{{$uRow->UDFGEJID}}" autocomplete="off"   /></td>
                    <td hidden><input type="hidden" name={{"UDFismandatory_".$uindex}} id={{"UDFismandatory_".$uindex}} value="{{$uRow->ISMANDATORY}}" class="form-control"   autocomplete="off" /></td>
                    <td id={{"udfinputid_".$uindex}} >
                      
                    </td>
                    <td align="center" ><button class="btn add UDF" title="add" data-toggle="tooltip" type="button" disabled><i class="fa fa-plus"></i></button><button class="btn remove DUDF" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
                    
                </tr>
                <tr></tr>
              @endforeach 
						  
							</tbody>
						</table>
					</div>
				</div>
				
			</div>
		</div>
		
	</div>
	
</div>

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
        <td class="ROW2"><input type="text" id="vendorcodesearch" class="form-control" onkeyup="VendorCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="vendornamesearch" class="form-control" onkeyup="VendorNameFunction()"></td>
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

<div id="JWO_NO_Modal" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='JWO_NO_Modal_Close' >&times;</button>
      </div>
      <div class="modal-body">
	      <div class="tablename"><p>JWO No</p></div>
	      <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
          <table id="JWO_NO_Table" class="display nowrap table  table-striped table-bordered">
            <thead>
              <tr>
                <th class="ROW1">Select</th> 
                <th class="ROW2">JWO No</th>
                <th class="ROW3">JWO Date</th>
              </tr>
            </thead>
            <tbody>

              <tr>
                <th class="ROW1"><span class="check_th">&#10004;</span></th>
                <td class="ROW2"><input type="text" id="JWO_NO_Code_Search" class="form-control" onkeyup="JWO_NO_Code_Function()"></td>
                <td class="ROW3"><input type="text" id="JWO_NO_Name_Search" class="form-control" onkeyup="JWO_NO_Name_Function()"></td>
              </tr>

            </tbody>
            </table>
            <table id="JWO_NO_Table2" class="display nowrap table  table-striped table-bordered" >
              <thead id="thead2">          
              </thead>
              <tbody id="JWO_NO_Body" >
              </tbody>
            </table>
          </div>
		    <div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<div id="TRANSPORTER_Modal" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='TRANSPORTER_Modal_Close' >&times;</button>
      </div>
      <div class="modal-body">
	      <div class="tablename"><p> Transporter</p></div>
	      <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
          <table id="TRANSPORTER_Table" class="display nowrap table  table-striped table-bordered" >
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
                <td class="ROW2"><input type="text" id="TRANSPORTER_Code_Search" class="form-control" onkeyup="TRANSPORTER_Code_Function()"></td>
                <td class="ROW3"><input type="text" id="TRANSPORTER_Name_Search" class="form-control" onkeyup="TRANSPORTER_Name_Function()"></td>
              </tr>
            </tbody>
            </table>
            <table id="TRANSPORTER_Table2" class="display nowrap table  table-striped table-bordered">
              <thead id="thead2">          
              </thead>
              <tbody>
              @foreach ($objTransporterList as $key=>$val)
              <tr >
                <td class="ROW1"> <input type="checkbox" name="SELECT_TRANSPORTER[]" id="TRANSPORTER_TDID_{{ $key }}" class="TRANSPORTER_Row" value="{{ $val-> TRANSPORTERID }}" ></td>
                <td class="ROW2" >{{ $val-> TRANSPORTER_CODE }} <input type="hidden" id="txtTRANSPORTER_TDID_{{ $key }}" data-desc="{{ $val-> TRANSPORTER_CODE }} - {{ $val-> TRANSPORTER_NAME }}"  value="{{ $val-> TRANSPORTERID }}"/></td>
                <td class="ROW3">{{ $val-> TRANSPORTER_NAME }}</td>
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

<div id="PTID_REF_Modal" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='PTID_REF_Modal_Close' >&times;</button>
      </div>
      <div class="modal-body">
	      <div class="tablename"><p> Primary Packing Type</p></div>
	      <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
          <table id="PTID_REF_Table" class="display nowrap table  table-striped table-bordered" >
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
                <td class="ROW2"><input type="text" id="PTID_REF_Code_Search" class="form-control" onkeyup="PTID_REF_Code_Function()"></td>
                <td class="ROW3"><input type="text" id="PTID_REF_Name_Search" class="form-control" onkeyup="PTID_REF_Name_Function()"></td>
              </tr>

            </tbody>
            </table>
            <table id="PTID_REF_Table2" class="display nowrap table  table-striped table-bordered" >
              <thead id="thead2">          
              </thead>
              <tbody>
              @foreach ($objPackingList as $key=>$val)
              <tr >
                <td class="ROW1"> <input type="checkbox" name="SELECT_PTID_REF[]" id="PTID_REF_TDID_{{ $key }}" class="PTID_REF_Row" value="{{ $val-> PTID }}" ></td>
                <td class="ROW2" >{{ $val-> PTCODE }} <input type="hidden" id="txtPTID_REF_TDID_{{ $key }}" data-desc="{{ $val-> PTCODE }} - {{ $val-> PTNAME }}"  value="{{ $val-> PTID }}"/></td>
                <td class="ROW3">{{ $val-> PTNAME }}</td>
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
<style>
.errormsg{
  color:red;
}
.text-danger{
  color:red !important;
}
#custom_dropdown, #udfforsemst_filter {
    display: inline-table;
    margin-left: 15px;
}


#VID_REF_Code_Search {
  background-image: url('/css/searchicon.png');
  background-position: 10px 10px;
  background-repeat: no-repeat;
  font-size: 11px;
  padding: 5px 5px 5px 5px;
  border: 1px solid #ddd;
  margin-bottom: 5px;
}
#VID_REF_Name_Search {
  background-image: url('/css/searchicon.png');
  background-position: 10px 10px;
  background-repeat: no-repeat;
  font-size: 11px;
  padding: 5px 5px 5px 5px;
  border: 1px solid #ddd;
  margin-bottom: 5px;
}
#VID_REF_GL_Search {
  background-image: url('/css/searchicon.png');
  background-position: 10px 10px;
  background-repeat: no-repeat;
  font-size: 11px;
  padding: 5px 5px 5px 5px;
  border: 1px solid #ddd;
  margin-bottom: 5px;
}
#VID_REF_Country_Search {
  background-image: url('/css/searchicon.png');
  background-position: 10px 10px;
  background-repeat: no-repeat;
  font-size: 11px;
  padding: 5px 5px 5px 5px;
  border: 1px solid #ddd;
  margin-bottom: 5px;
}
#VID_REF_State_Search {
  background-image: url('/css/searchicon.png');
  background-position: 10px 10px;
  background-repeat: no-repeat;
  font-size: 11px;
  padding: 5px 5px 5px 5px;
  border: 1px solid #ddd;
  margin-bottom: 5px;
}
#VID_REF_City_Search {
  background-image: url('/css/searchicon.png');
  background-position: 10px 10px;
  background-repeat: no-repeat;
  font-size: 11px;
  padding: 5px 5px 5px 5px;
  border: 1px solid #ddd;
  margin-bottom: 5px;
}

#VID_REF_Table {
  border-collapse: collapse;
  width: 950px;
  border: 1px solid #ddd;
  font-size: 11px;
}

#VID_REF_Table th {
    text-align: center;
    padding: 5px;
    
    font-size: 11px;
    
    color: #0f69cc;
    font-weight: 600;
}

#VID_REF_Table td {
    text-align: center;
    padding: 5px;
    font-size: 11px;
  
    font-weight: 600;
}

#VID_REF_Table2 {
  border-collapse: collapse;
  width: 950px;
  border: 1px solid #ddd;
  font-size: 11px;
}

#VID_REF_Table2 th{
    text-align: left;
    padding: 5px;
   
    font-size: 11px;
    
    color: #0f69cc;
    font-weight: 600;
}

#VID_REF_Table2 td {
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
/*================================== SHORTING FUNCTION ==================================*/ 
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

/*================================== VENDOR FUNCTION ==================================*/ 
let vdtid = "#VendorCodeTable2";
let vdtid2 = "#VendorCodeTable";
let vdheaders = document.querySelectorAll(vdtid2 + " th");

      
vdheaders.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(vdtid, ".clsvendorid", "td:nth-child(" + (i + 1) + ")");
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
      bindVendEvents();
      showSelectedCheck($("#VID_REF").val(),"SELECT_VID_REF"); 
    },
    error:function(data){
    console.log("Error: Something went wrong.");
    $("#tbody_vendor").html('');                        
    },
  });
}

$('#VID_REF_Details').click(function(event){

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

function bindVendEvents(){

  $(".clsvendorid").click(function(){
    var fieldid = $(this).attr('id');
    var txtval =    $("#txt"+fieldid+"").val();
    var texdesc =   $("#txt"+fieldid+"").data("desc");
    
    $('#VID_REF_Details').val(texdesc);
    $('#VID_REF').val(txtval);
    $("#vendoridpopup").hide();
    $("#VID_REF_Code_Search").val(''); 
    $("#VID_REF_Name_Search").val(''); 

    JwoNoType('JWO');

    $('#JWO_NO_Details').val('');
    $('#JWO_NO').val('');
    $("#JWO_NO_Body").html('');
    $('#VENDOR_BILLNO').val('');
    $('#VENDOR_BILLDT').val('');
    $('#VENDOR_CHNO').val('');
    $('#VENDOR_CHDT').val('');

    $("#vendoridpopup").hide();
    $("#vendor_codesearch").val(''); 
    $("#vendor_namesearch").val(''); 
    VendorCodeFunction();
    event.preventDefault();
  });

}

function JwoNoType(type){
  $(".errormsg").remove();
  var VID_REF =$("#VID_REF").val();

  if(VID_REF ===""){
    $("#VID_REF_Details").focus();
    $("#VID_REF").after('<span  class="errormsg">Select vendor name.</span>');
  }
  else{

      $("#JWO_NO_Details").val('');
      $("#JWO_NO").val('');
      getJwoNo('JWO',VID_REF);
  }

}

/*================================== JWO FUNCTION ==================================*/ 

function getJwoNo(Type,VID_REF){
    $("#JWO_NO_Body").html('');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url:'{{route("transaction",[$FormId,"getJwoNo"])}}',
        type:'POST',
        data:{Type:Type,VID_REF:VID_REF},
        success:function(data) {
          $("#JWO_NO_Body").html(data);  
          bind_JWO_NO_Events();               
        },
        error:function(data){
          console.log("Error: Something went wrong.");
          $("#JWO_NO_Body").html('');                        
        },
    }); 

}

let JWO_NO_tid = "#JWO_NO_Table2";
let JWO_NO_tid2 = "#JWO_NO_Table";
let JWO_NO_headers = document.querySelectorAll(JWO_NO_tid2 + " th");

JWO_NO_headers.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(JWO_NO_tid, ".JWO_NO_Row", "td:nth-child(" + (i + 1) + ")");
  });
});

function JWO_NO_Code_Function() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("JWO_NO_Code_Search");
  filter = input.value.toUpperCase();
  table = document.getElementById("JWO_NO_Table2");
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

function JWO_NO_Name_Function() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("JWO_NO_Name_Search");
      filter = input.value.toUpperCase();
      table = document.getElementById("JWO_NO_Table2");
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

$('#JWO_NO_Details').focus(function(event){
  $("#JWO_NO_Modal").show();
  event.preventDefault();
});

$("#JWO_NO_Modal_Close").click(function(event){
  $("#JWO_NO_Modal").hide();
  event.preventDefault();
});

function bind_JWO_NO_Events(){
  $(".JWO_NO_CHECK").change(function(){

    var txtval   = [];
    var texdesc  = [];
    
    $('#JWO_NO_Table2').find('.JWO_NO_Row').each(function(){
      var text_id     = $.trim($(this).find("[id*=txtJWO_NO_TDID]").val());
      var text_attr   = $.trim($(this).find("[id*=txtJWO_NO_TDID]").attr('id'));
      var text_check  = $.trim($(this).find("[id*=txtJWO_NO_CHECK]").attr('id'));
      var text_des    =   $("#"+text_attr).data("desc");

      if($("#"+text_check).prop("checked") == true){
        txtval.push(text_id);
        texdesc.push(text_des);
      }
     
    });

    $('#JWO_NO_Details').val(texdesc);
    $('#JWO_NO').val(txtval);

    $("#JWO_NO_Code_Search").val(''); 
    $("#JWO_NO_Name_Search").val(''); 
    JWO_NO_Code_Function();
    event.preventDefault();

  });

}


/*================================== TRANSPORTER FUNCTION ==================================*/

let TRANSPORTER_tid = "#TRANSPORTER_Table2";
let TRANSPORTER_tid2 = "#TRANSPORTER_Table";
let TRANSPORTER_headers = document.querySelectorAll(TRANSPORTER_tid2 + " th");

TRANSPORTER_headers.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(TRANSPORTER_tid, ".TRANSPORTER_Row", "td:nth-child(" + (i + 1) + ")");
  });
});

function TRANSPORTER_Code_Function() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("TRANSPORTER_Code_Search");
  filter = input.value.toUpperCase();
  table = document.getElementById("TRANSPORTER_Table2");
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

function TRANSPORTER_Name_Function() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("TRANSPORTER_Name_Search");
      filter = input.value.toUpperCase();
      table = document.getElementById("TRANSPORTER_Table2");
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

$('#TRANSPORTER_Details').focus(function(event){
  showSelectedCheck($("#TRANSPORTER").val(),"SELECT_TRANSPORTER");
  $("#TRANSPORTER_Modal").show();
  event.preventDefault();
});

$("#TRANSPORTER_Modal_Close").click(function(event){
  $("#TRANSPORTER_Modal").hide();
  event.preventDefault();
});

$(".TRANSPORTER_Row").click(function(){
  
  var fieldid = $(this).attr('id');
  var txtval =    $("#txt"+fieldid+"").val();
  var texdesc =   $("#txt"+fieldid+"").data("desc");

  $('#TRANSPORTER_Details').val(texdesc);
  $('#TRANSPORTER').val(txtval);
  $("#TRANSPORTER_Modal").hide();
  $("#TRANSPORTER_Code_Search").val(''); 
  $("#TRANSPORTER_Name_Search").val(''); 
  TRANSPORTER_Code_Function();
  event.preventDefault();
});

/*================================== PACKING TYPE FUNCTION ==================================*/

let PTID_REF_tid = "#PTID_REF_Table2";
let PTID_REF_tid2 = "#PTID_REF_Table";
let PTID_REF_headers = document.querySelectorAll(PTID_REF_tid2 + " th");

PTID_REF_headers.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(PTID_REF_tid, ".PTID_REF_Row", "td:nth-child(" + (i + 1) + ")");
  });
});

function PTID_REF_Code_Function() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("PTID_REF_Code_Search");
  filter = input.value.toUpperCase();
  table = document.getElementById("PTID_REF_Table2");
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

function PTID_REF_Name_Function() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("PTID_REF_Name_Search");
      filter = input.value.toUpperCase();
      table = document.getElementById("PTID_REF_Table2");
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

$('#PTID_REF_Details').focus(function(event){
  showSelectedCheck($("#PTID_REF").val(),"SELECT_PTID_REF");
  $("#PTID_REF_Modal").show();
  event.preventDefault();
});

$("#PTID_REF_Modal_Close").click(function(event){
  $("#PTID_REF_Modal").hide();
  event.preventDefault();
});

$(".PTID_REF_Row").click(function(){
  
  var fieldid = $(this).attr('id');
  var txtval =    $("#txt"+fieldid+"").val();
  var texdesc =   $("#txt"+fieldid+"").data("desc");

  $('#PTID_REF_Details').val(texdesc);
  $('#PTID_REF').val(txtval);
  $("#PTID_REF_Modal").hide();
  $("#PTID_REF_Code_Search").val(''); 
  $("#PTID_REF_Name_Search").val(''); 
  PTID_REF_Code_Function();
  event.preventDefault();
});

$(document).ready(function(e) {
var Material = $("#Material").html(); 
$('#hdnmaterial').val(Material);
var lastdt = <?php echo json_encode($objlastdt[0]->GEDT); ?>;
var today = new Date(); 
var sodate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
$('#GE_DT').attr('min',lastdt);
$('#GE_DT').attr('max',sodate);

var seudf = <?php echo json_encode($objUdfData); ?>;
var count2 = <?php echo json_encode($objCountUDF); ?>;

$('#example3').find('.participantRow3').each(function(){

      var txt_id4 = $(this).find('[id*="udfinputid"]').attr('id');
      var udfid = $(this).find('[id*="UDF"]').val();
      $.each( seudf, function( seukey, seuvalue ) {
        if(seuvalue.UDFGEJID == udfid)
        {
          var txtvaltype2 =   seuvalue.VALUETYPE;
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
          var txtoptscombo2 =   seuvalue.DESCRIPTIONS;
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

$('#Row_Count1').val("1");
$('#Row_Count2').val(count2);

$(function() { $('#GE_NO').focus(); });

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

window.fnUndoNo = function (){
    $("#GE_NO").focus();
}


});
</script>

@endpush

@push('bottom-scripts')
<script>

$(document).ready(function() {

    var d = new Date(); 
    var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
    $('#GE_DT').val(today);

    
    $("#btnSaveSE").on("submit", function( event ) {
      if ($("#frm_trn_add").valid()) {
         
          alert( "Handler for .submit() called." );
          event.preventDefault();
      }
    });

    $('#frm_trn_add1').bootstrapValidator({
       
        fields: {
            txtlabel: {
                validators: {
                    notEmpty: {
                        message: 'The GE Number is required'
                    }
                }
            },            
        },
        submitHandler: function(validator, form, submitButton) {
            alert( "Handler for .submit() called." );
             event.preventDefault();
             $("#frm_trn_add").submit();
        }
    });
});

function validateForm(){
  $(".errormsg").remove();
  $("#FocusId").val('');
  var GE_NO         =   $.trim($("#GE_NO").val());
  var GE_DT         =   $.trim($("#GE_DT").val());
  var GE_TM         =   $.trim($("#GE_TM").val());
  var VID_REF       =   $.trim($("#VID_REF").val());
  var JWO_NO         =   $.trim($("#JWO_NO").val());
  var VENDOR_BILLNO =   $.trim($("#VENDOR_BILLNO").val());
  var VENDOR_BILLDT =   $.trim($("#VENDOR_BILLDT").val());
  var VENDOR_CHNO   =   $.trim($("#VENDOR_CHNO").val());
  var VENDOR_CHDT   =   $.trim($("#VENDOR_CHDT").val());
  var GATE_RGNO     =   $.trim($("#GATE_RGNO").val());
  var DATE          =   $.trim($("#DATE").val());
  var LR_NO         =   $.trim($("#LR_NO").val());
  var LR_DT         =   $.trim($("#LR_DT").val());
  var GROSS_WEIGHT  =   $.trim($("#GROSS_WEIGHT").val());
  var TARE_WEIGHT   =   $.trim($("#TARE_WEIGHT").val());
  var NET_WEIGHT    =   $.trim($("#NET_WEIGHT").val());

  if(GE_NO ===""){
     $("#FocusId").val($("#GE_NO"));
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('GE No is required.');
     $("#alert").modal('show')
     $("#OkBtn1").focus();
     return false;
  }
  else if(GE_DT ===""){
    $("#GE_DT").focus();
    $("#GE_DT").after('<span  class="errormsg">Select GE Date.</span>');
    return false;
  }
  else if(GE_TM ===""){
    $("#GE_TM").focus();
    $("#GE_TM").after('<span  class="errormsg">Select GE Time.</span>');
    return false;
  }  
  else if(VID_REF ===""){
    $("#VID_REF").focus();
    $("#VID_REF").after('<span  class="errormsg">Select vendor name.</span>');
    return false;
  }
  else if(JWO_NO ===""){
    $("#JWO_NO").focus();
    $("#JWO_NO").after('<span  class="errormsg">Select JWO No.</span>');
    return false;
  }
  else if($("#PO").prop("checked") == true && VENDOR_BILLNO ===""){
    $("#VENDOR_BILLNO").focus();
    $("#VENDOR_BILLNO").after('<span  class="errormsg">Select Vendor Bill No.</span>');
    return false;
  }
  else if(VENDOR_BILLNO !="" && VENDOR_BILLDT ===""){
    $("#VENDOR_BILLDT").focus();
    $("#VENDOR_BILLDT").after('<span  class="errormsg">Select bill date.</span>');
    return false;
  }
  else if(VENDOR_CHNO !="" && VENDOR_CHDT ===""){
    $("#VENDOR_CHDT").focus();
    $("#VENDOR_CHDT").after('<span  class="errormsg">Select challan date.</span>');
    return false;
  }
  else if(GATE_RGNO !="" && DATE ===""){
    $("#DATE").focus();
    $("#DATE").after('<span  class="errormsg">Select date.</span>');
    return false;
  }
  else if(LR_NO !="" && LR_DT ===""){
    $("#VehicleTab" ).trigger( "click" );
    $("#LR_DT").focus();
    $("#LR_DT").after('<span  class="errormsg">Select LR Date.</span>');
    return false;
  }
  else if(GROSS_WEIGHT !="" && TARE_WEIGHT ===""){
    $("#VehicleTab" ).trigger( "click" );
    $("#TARE_WEIGHT").focus();
    $("#TARE_WEIGHT").after('<span  class="errormsg">Please enter tare weight.</span>');
    return false;
  }
  else if(TARE_WEIGHT !="" && GROSS_WEIGHT ===""){
    $("#VehicleTab" ).trigger( "click" );
    $("#GROSS_WEIGHT").focus();
    $("#GROSS_WEIGHT").after('<span  class="errormsg">Please enter gross weight.</span>');
    return false;
  }
  else if(GROSS_WEIGHT !="" && /^[0-9.\s]+$/.test(GROSS_WEIGHT)==false){
    $("#VehicleTab" ).trigger( "click" );
    $("#GROSS_WEIGHT").focus();
    $("#GROSS_WEIGHT").after('<span  class="errormsg">Allow no and decimal.</span>');
    return false;
  }
  else if(TARE_WEIGHT !="" && /^[0-9.\s]+$/.test(TARE_WEIGHT)==false){
    $("#VehicleTab" ).trigger( "click" );
    $("#TARE_WEIGHT").focus();
    $("#TARE_WEIGHT").after('<span  class="errormsg">Allow no and decimal.</span>');
    return false;
  }
  else if(NET_WEIGHT !="" && /^[0-9.\s]+$/.test(NET_WEIGHT)==false){
    $("#VehicleTab" ).trigger( "click" );
    $("#NET_WEIGHT").focus();
    $("#NET_WEIGHT").after('<span  class="errormsg">Allow no and decimal.</span>');
    return false;
  }
 else{
    event.preventDefault();
    var allblank1 = [];

    $('#example3').find('.participantRow3').each(function(){
      if($.trim($(this).find("[id*=UDF]").val())!=""){
        if($.trim($(this).find("[id*=UDFismandatory]").val())=="1"){
              if($.trim($(this).find('[id*="udfvalue"]').val()) != "")
              {
                allblank1.push('true');
              }
              else
              {
                allblank1.push('false');
              }
        }  
      }                
    });

 
    if(jQuery.inArray("false", allblank1) !== -1){
      $("#udfTab" ).trigger( "click" );
      $("#AlertMessage").text('Please enter  Value / Comment in UDF Tab.');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      $("#alert").modal('show');
      highlighFocusBtn('activeOk');
    }
    else if(checkPeriodClosing('{{$FormId}}',$("#GE_DT").val(),0) ==0){
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
}

function checkDuplicateCode(){

    var trnFormReq = $("#frm_trn_add");
    var formData = trnFormReq.serialize();

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
                showError('ERROR_GE_NO',data.msg);
                $("#GE_NO").focus();
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

$("#YesBtn").click(function(){

$("#alert").modal('hide');
var customFnName = $("#YesBtn").data("funcname");
    window[customFnName]();

});


$("#btnSaveSE" ).click(function() {
    var formReqData = $("#frm_trn_add");
    if(formReqData.valid()){
      validateForm();
    }
});

window.fnSaveData = function (){


event.preventDefault();

     var trnFormReq = $("#frm_trn_add");
    var formData = trnFormReq.serialize();
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$("#btnSaveSE").hide(); 
$(".buttonload").show(); 
$("#btnApprove").prop("disabled", true);
$.ajax({
    url:'{{ route("transaction",[$FormId,"save"])}}',
    type:'POST',
    data:formData,
    success:function(data) {
      $(".buttonload").hide(); 
      $("#btnSaveSE").show();   
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
        $(".buttonload").hide(); 
        $("#btnSaveSE").show();   
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
    var FocusId=$("#FocusId").val();
    $("#"+FocusId).focus();
    $("#closePopup").click();
}
function highlighFocusBtn(pclass){
  $(".activeYes").hide();
  $(".activeNo").hide();
  
  $("."+pclass+"").show();
}

function isNumberDecimalKey(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
    return false;

    return true;
}

function weightCalculation(){
  $(".errormsg").remove();

  var GROSS_WEIGHT  = parseFloat($.trim($("#GROSS_WEIGHT").val()));
  var TARE_WEIGHT   = parseFloat($.trim($("#TARE_WEIGHT").val()));

  if(TARE_WEIGHT > GROSS_WEIGHT){
    $("#TARE_WEIGHT").focus();
    $("#TARE_WEIGHT").val('');
    $("#NET_WEIGHT").val('');
    $("#TARE_WEIGHT").after('<span  class="errormsg">Enter correct Tare weight.</span>');
  }
  else{
    if($.trim($("#GROSS_WEIGHT").val()) !="" && $.trim($("#TARE_WEIGHT").val()) !=""){
      $("#NET_WEIGHT").val((GROSS_WEIGHT-TARE_WEIGHT));
    }
  }

}

function deleteDate(id,data){
  if(data ===""){
    $("#"+id).val('');
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