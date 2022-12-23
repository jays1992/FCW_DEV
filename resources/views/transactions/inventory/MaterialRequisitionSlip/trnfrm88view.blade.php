@extends('layouts.app')
@section('content')

<div class="container-fluid topnav">
    <div class="row">
        <div class="col-lg-2">
        <a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Material Requisition Slip</a>
        </div>

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
    </div>
</div>

<form id="transaction_form"  method="POST">   
    @csrf
    {{isset($objResponse->MRSID[0]) ? method_field('PUT') : '' }}
    <div class="container-fluid filter">
	<div class="inner-form">

		<div class="row">

			<div class="col-lg-1 pl"><p>MRS No</p></div>
			<div class="col-lg-2 pl">
          <input {{$ActionStatus}} type="text" name="MRS_NO" id="MRS_NO" value="{{isset($objResponse->MRS_NO) && $objResponse->MRS_NO !=''?$objResponse->MRS_NO:''}}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase" >
      </div>
			
			<div class="col-lg-1 pl"><p>MRS Date</p></div>
			<div class="col-lg-2 pl">
			    <input {{$ActionStatus}} type="date" name="MRS_DT" id="MRS_DT" value="{{isset($objResponse->MRS_DT) && $objResponse->MRS_DT !=''?$objResponse->MRS_DT:''}}" class="form-control mandatory"  placeholder="dd/mm/yyyy" >
      </div>

      <div class="col-lg-1 pl"><p>MRS Type</p></div>
			<div class="col-lg-2 pl" >
        <select {{$ActionStatus}} name="MRS_TYPE" id="MRS_TYPE" class="form-control" >
          <option {{isset($objResponse->MRS_TYPE) && $objResponse->MRS_TYPE =='DIRECT'?'selected="selected"':''}} value='DIRECT'>DIRECT</option>
          <option {{isset($objResponse->MRS_TYPE) && $objResponse->MRS_TYPE =='RGP'?'selected="selected"':''}}  value='RGP'>RGP</option>
          <option {{isset($objResponse->MRS_TYPE) && $objResponse->MRS_TYPE =='NRGP'?'selected="selected"':''}} value='NRGP'>NRGP</option>
        </select>
      </div>

      <div class="col-lg-1 pl"><p>Request User</p></div>
			<div class="col-lg-2 pl">
          <input {{$ActionStatus}} type="text" name="REQUEST_USER_popup" id="txtREQUEST_USER_popup" value="{{isset($objUserName->EMPCODE) && $objUserName->EMPCODE !=''?$objUserName->EMPCODE.' - '.$objUserName->FNAME.' '.$objUserName->MNAME.' '.$objUserName->LNAME:''}}" class="form-control mandatory"  autocomplete="off"  readonly/>
          <input type="hidden" name="REQUEST_USER" id="REQUEST_USER" class="form-control" autocomplete="off" value="{{isset($objResponse->REQUEST_USER) && $objResponse->REQUEST_USER !=''?$objResponse->REQUEST_USER:''}}" />
			</div>
		</div>
		
    <div class="row">
      <div class="col-lg-1 pl"><p>From Department</p></div>
      <div class="col-lg-2 pl">
          <input {{$ActionStatus}} type="text" name="DEPID_REF_popup" id="txtdep_popup" value="{{isset($objDepartmentName->DCODE) && $objDepartmentName->DCODE !=''?$objDepartmentName->DCODE.' - '.$objDepartmentName->NAME:''}}" class="form-control mandatory"   autocomplete="off" readonly/>
          <input type="hidden" name="DEPID_REF" id="DEPID_REF" value="{{isset($objResponse->DEPID_REF) && $objResponse->DEPID_REF !=''?$objResponse->DEPID_REF:''}}" class="form-control" autocomplete="off" />
      </div>

      <div class="col-lg-1 pl"><p>To Store</p></div>
      <div class="col-lg-2 pl">
          <input {{$ActionStatus}} type="text" name="STID_REF_popup" id="STID_REF_popup" value="{{isset($objStoreName->STCODE) && $objStoreName->STCODE !=''?$objStoreName->STCODE.' - '.$objStoreName->NAME:''}}" class="form-control mandatory"  autocomplete="off" readonly/>
          <input type="hidden" name="STID_REF" id="STID_REF" value="{{isset($objResponse->STID_REF) && $objResponse->STID_REF !=''?$objResponse->STID_REF:''}}" class="form-control" autocomplete="off" />
      </div>

      <div class="col-lg-1 pl"><p>Remarks</p></div>
      <div class="col-lg-5 pl">
        <input {{$ActionStatus}} type="text" name="REMARKS" id="REMARKS" value="{{isset($objResponse->REMARKS) && $objResponse->REMARKS !=''?$objResponse->REMARKS:''}}" class="form-control" autocomplete="off"  maxlength="200"  >
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
					<div class="table-responsive table-wrapper-scroll-y" style="height:280px;margin-top:10px;" >
						<table id="example2" class="display nowrap table table-striped table-bordered itemlist w-200" width="100%" style="height:auto !important;">
							<thead id="thead1"  style="position: sticky;top: 0">
								  <tr>
									<th>Item Code<input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1"></th>
									<th>Item Description</th>
									<th>Main UOM</th>
									<th>Qty</th>
                  <th>Item Specification</th>
                  <th>Priority</th>
									<th>Expected Date</th>
									<th>Action</th>
								  </tr>
							</thead>
							<tbody>
              @if(!empty($objMAT))
              @foreach($objMAT as $key => $row)
							<tr  class="participantRow">
                  <td><input {{$ActionStatus}} type="text" name={{"popupITEMID_".$key}} id={{"popupITEMID_".$key}} value="{{ $row->ICODE }}" class="form-control"  autocomplete="off"  readonly/></td>
                  <td hidden><input type="hidden" name={{"ITEMID_REF_".$key}} id={{"ITEMID_REF_".$key}} class="form-control"  value="{{ $row->ITEMID_REF }}" autocomplete="off" /></td>
                  <td><input {{$ActionStatus}} type="text" name={{"ItemName_".$key}} id={{"ItemName_".$key}} class="form-control" value="{{ $row->ITEM_NAME }}"  autocomplete="off"   readonly/></td>
                  <td><input {{$ActionStatus}} type="text" name={{"popupMUOM_".$key}} id={{"popupMUOM_".$key}} class="form-control" value="{{ $row->MAIN_UOM_CODE }}"   autocomplete="off"  readonly/></td>
                  <td hidden><input type="hidden" name={{"MAIN_UOMID_REF_".$key}} id={{"MAIN_UOMID_REF_".$key}}  class="form-control" value="{{ $row->MAIN_UOMID_REF }}"   autocomplete="off" /></td>
                  <td><input {{$ActionStatus}} type="text" name={{"SE_QTY_".$key}} id={{"SE_QTY_".$key}} class="form-control three-digits" maxlength="13" value="{{ $row->QTY }}" autocomplete="off"  /></td>
                  <td hidden><input type="hidden" name={{"SO_FQTY_".$key}} id={{"SO_FQTY_".$key}} class="form-control three-digits" maxlength="13"  autocomplete="off"  readonly/></td>
                  <td><input {{$ActionStatus}} type="text" name={{"Itemspec_".$key}} id={{"Itemspec_".$key}} class="form-control"  autocomplete="off" value="{{ $row->ITEM_SPECI }}"   /></td>
                  <td><input {{$ActionStatus}} type="text" name={{"PRIORITYID_".$key}} id={{"PRIORITYID_".$key}} class="form-control"  autocomplete="off" value="{{ $row->PRIORITYCODE }} - {{ $row->PRIORITYDESC }}"  readonly/></td>
                  
                  <td hidden><input type="hidden" name={{"PTID_REF_".$key}} id={{"PTID_REF_".$key}} class="form-control"  autocomplete="off" value="{{ $row->PRIORITYID_REF }}" readonly/></td>
                  <td><input {{$ActionStatus}}  class="form-control w-100" type="date" name={{"EDD_".$key}} id={{"EDD_".$key}} placeholder="dd/mm/yyyy" value="{{ $row->EXP_DATE }}" ></td>
           
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

              @if(isset($objUDF) && !empty($objUDF))

                @foreach($objUDF as $uindex=>$uRow)
                  <tr  class="participantRow3">
                      <td><input {{$ActionStatus}} type="text" name={{"popupSEID_".$uindex}} id={{"popupSEID_".$uindex}} class="form-control" value="{{$uRow->UDF}}" autocomplete="off"  readonly/></td>
                      <td hidden><input type="hidden" name={{"UDF_".$uindex}} id={{"UDF_".$uindex}} class="form-control" value="{{$uRow->UDF}}" autocomplete="off"   /></td>
                      <td hidden><input type="hidden" name={{"UDFismandatory_".$uindex}} id={{"UDFismandatory_".$uindex}} value="{{$uRow->UDF}}" class="form-control"   autocomplete="off" /></td>
                      <td id={{"udfinputid_".$uindex}} >
                        
                      </td>
                      <td align="center" ><button class="btn add UDF" title="add" data-toggle="tooltip" type="button" disabled><i class="fa fa-plus"></i></button><button class="btn remove DUDF" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
                      
                  </tr>
                  <tr></tr>
                @endforeach 

              @else


              @foreach($objUdfData as $uindex=>$uRow)
                <tr  class="participantRow3">
                    <td><input {{$ActionStatus}} type="text" name={{"popupSEID_".$uindex}} id={{"popupSEID_".$uindex}} class="form-control" value="{{$uRow->LABEL}}" autocomplete="off"  readonly/></td>
                    <td hidden><input type="hidden" name={{"UDF_".$uindex}} id={{"UDF_".$uindex}} class="form-control" value="{{$uRow->UDFMRSID}}" autocomplete="off"   /></td>
                    <td hidden><input type="hidden" name={{"UDFismandatory_".$uindex}} id={{"UDFismandatory_".$uindex}} value="{{$uRow->ISMANDATORY}}" class="form-control"   autocomplete="off" /></td>
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


<div id="dpidpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='dp_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Department</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="DpCodeTable" class="display nowrap table  table-striped table-bordered" >
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
        <td class="ROW2"><input type="text" id="dpcodesearch" class="form-control" autocomplete="off" onkeyup="DPCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="dpnamesearch" class="form-control" autocomplete="off" onkeyup="DPNameFunction()"></td>
      </tr>
    </tbody>
    </table>
      <table id="DpCodeTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2">          
        </thead>
        <tbody>
        @foreach ($objDepartmentList as $key=>$val)
        <tr >
          <td class="ROW1"> <input type="checkbox" name="SELECT_DEPID_REF[]" id="dpidcode_{{ $key }}" class="clsdpid" value="{{ $val-> DEPID }}" ></td>  
          <td class="ROW2">{{ $val-> DCODE }} <input type="hidden" id="txtdpidcode_{{ $key }}" data-desc="{{ $val-> DCODE }} - {{ $val-> NAME }}"  value="{{ $val-> DEPID }}"/></td>
          <td class="ROW3">{{ $val-> NAME }}</td>
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

<div id="REQUEST_USER_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='REQUEST_USER_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Request User</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="RequestUserTable" class="display nowrap table  table-striped table-bordered" >
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
        <td class="ROW2"><input type="text" id="RequestUsercodesearch" class="form-control" autocomplete="off" onkeyup="RequestUserCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="RequestUsernamesearch" class="form-control" autocomplete="off" onkeyup="RequestUserNameFunction()"></td>
      </tr>

    </tbody>
    </table>
      <table id="RequestUserTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2">
          
        </thead>
        <tbody >     
        @foreach ($objUserList as $key=>$val)
        <tr >
          <td class="ROW1"> <input type="checkbox" name="SELECT_REQUEST_USER[]" id="spidcode_{{ $key }}" class="clssrequestuser" value="{{ $val-> EMPID }}" ></td>  
          <td class="ROW2">{{ $val-> EMPCODE }} <input type="hidden" id="txtspidcode_{{ $key }}" data-desc="{{ $val-> EMPCODE }} - {{ $val-> FNAME }}  {{ $val-> LNAME }}"  value="{{ $val-> EMPID }}"/></td>
          <td class="ROW3">{{ $val-> FNAME }} {{ $val-> MNAME }} {{ $val-> LNAME }}</td>
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

<div id="stidpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='st_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>To Store</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="STCodeTable" class="display nowrap table  table-striped table-bordered" >
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
        <td class="ROW2"><input type="text" id="stcodesearch" class="form-control" autocomplete="off" onkeyup="STCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="stnamesearch" class="form-control" autocomplete="off" onkeyup="STNameFunction()"></td>
      </tr>

    </tbody>
    </table>
      <table id="STCodeTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2">
        </thead>
        <tbody>
        @foreach ($objStoreList as $key=>$val)
        <tr >
          <td class="ROW1"> <input type="checkbox" name="SELECT_STID_REF[]" id="stidcode_{{ $key }}" class="clsstid" value="{{ $val-> STID }}" ></td>   
          <td class="ROW2">{{ $val-> STCODE }} <input type="hidden" id="txtstidcode_{{ $key }}" data-desc="{{ $val-> STCODE }} - {{ $val-> NAME }}"  value="{{ $val-> STID }}"/></td>
          <td class="ROW3">{{ $val-> NAME }}</td>
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
                  <th style="width:8%;text-align:center;">&#10004;</th>
                  <td style="width:10%;"><input type="text" id="Itemcodesearch" class="form-control" autocomplete="off" onkeyup="ItemCodeFunction('{{$FormId}}')"></td>
                  <td style="width:10%;"><input type="text" id="Itemnamesearch" class="form-control" autocomplete="off" onkeyup="ItemNameFunction('{{$FormId}}')"></td>
                  <td style="width:8%;"><input type="text" id="ItemUOMsearch" class="form-control"  autocomplete="off" onkeyup="ItemUOMFunction('{{$FormId}}')"></td>
                  <td style="width:8%;"><input type="text" id="ItemQTYsearch" class="form-control" autocomplete="off" onkeyup="ItemQTYFunction()"></td>
                  <td style="width:8%;"><input type="text" id="ItemGroupsearch" class="form-control" autocomplete="off" onkeyup="ItemGroupFunction('{{$FormId}}')"></td>
                  <td style="width:8%;"><input type="text" id="ItemCategorysearch" class="form-control" autocomplete="off" onkeyup="ItemCategoryFunction('{{$FormId}}')"></td>
                  <td style="width:8%;"><input type="text" id="ItemBUsearch" class="form-control" autocomplete="off" onkeyup="ItemBUFunction('{{$FormId}}')"></td>
                  <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemAPNsearch" class="form-control" autocomplete="off" onkeyup="ItemAPNFunction('{{$FormId}}')"></td>
                  <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemCPNsearch" class="form-control" autocomplete="off" onkeyup="ItemCPNFunction('{{$FormId}}')"></td>
                  <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemOEMPNsearch" class="form-control" autocomplete="off" onkeyup="ItemOEMPNFunction('{{$FormId}}')"></td>
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

<div id="Prioritypopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='PriorityclosePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Priority</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="PriorityTable" class="display nowrap table  table-striped table-bordered" >
    <thead>
      <tr id="none-select" class="searchalldata" hidden>
            
            <td> <input type="hidden" name="fieldid" id="hdn_Priority"/>
            <input type="hidden" name="fieldid2" id="hdn_Priority2"/>
            </td>
      </tr>

      <th class="ROW1">Select</th> 
      <th class="ROW2">Code</th>
      <th class="ROW3">Name</th>
    </tr>
    </thead>
    <tbody>

    <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="Prioritycodesearch" class="form-control" autocomplete="off" onkeyup="PriorityCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="Prioritynamesearch" class="form-control" autocomplete="off" onkeyup="PriorityNameFunction()"></td>
      </tr>
    </tbody>
    </table>
      <table id="PriorityTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2">
        </thead>
        <tbody>
        @foreach ($objPriority as $ptindex=>$ptRow)
        <tr >
          <td class="ROW1"> <input type="checkbox" name="SELECT_PTID_REF[]" id="ptidcode_{{ $ptindex }}" class="clsptid" value="{{ $ptRow-> PRIORITYID }}" ></td>
          <td class="ROW2">{{ $ptRow-> PRIORITYCODE }} <input type="hidden" id="txtptidcode_{{ $ptindex }}" data-desc="{{ $ptRow-> PRIORITYCODE }} - {{$ptRow-> DESCRIPTIONS}}"  value="{{ $ptRow-> PRIORITYID }}"/></td>
          <td class="ROW3">{{ $ptRow-> DESCRIPTIONS }}</td>
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

<!-- Print -->
<div id="ReportView" class="modal" role="dialog"  data-backdrop="static"  >
  <div class="modal-dialog modal-md" style="width:90%; height:90%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='ReportViewclosePopup' >&times;</button>          
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Material Requisition Slip Print</p></div>
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
@endsection

@push('bottom-scripts')
<script>

//Department
let tid = "#DpCodeTable2";
let tid2 = "#DpCodeTable";
let headers = document.querySelectorAll(tid2 + " th");

headers.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(tid, ".clsdpid", "td:nth-child(" + (i + 1) + ")");
  });
});

function DPCodeFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("dpcodesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("DpCodeTable2");
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

function DPNameFunction() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("dpnamesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("DpCodeTable2");
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

  $('#txtdep_popup').click(function(event){
    showSelectedCheck($("#DEPID_REF").val(),"SELECT_DEPID_REF");
    $("#dpidpopup").show();
    event.preventDefault();
  });

  $("#dp_closePopup").click(function(event){
    $("#dpidpopup").hide();
    event.preventDefault();
  });

  $(".clsdpid").click(function(){
    var fieldid = $(this).attr('id');
    var txtval =    $("#txt"+fieldid+"").val();
    var texdesc =   $("#txt"+fieldid+"").data("desc");
    
    $('#txtdep_popup').val(texdesc);
    $('#DEPID_REF').val(txtval);
    $("#dpidpopup").hide();
    $("#dpcodesearch").val(''); 
    $("#dpnamesearch").val(''); 
    DPCodeFunction();
    event.preventDefault();
  });

  // Request User
let sptid = "#RequestUserTable2";
let sptid2 = "#RequestUserTable";
let requestuserheaders = document.querySelectorAll(sptid2 + " th");


requestuserheaders.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(sptid, ".clssrequestuser", "td:nth-child(" + (i + 1) + ")");
  });
});

function RequestUserCodeFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("RequestUsercodesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("RequestUserTable2");
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

function RequestUserNameFunction() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("RequestUsernamesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("RequestUserTable2");
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

$('#txtREQUEST_USER_popup').click(function(event){
    showSelectedCheck($("#REQUEST_USER").val(),"SELECT_REQUEST_USER");
    $("#REQUEST_USER_popup").show();
});

$("#REQUEST_USER_closePopup").click(function(event){
  $("#REQUEST_USER_popup").hide();
});

$(".clssrequestuser").click(function(){
  var fieldid = $(this).attr('id');
  var txtval =    $("#txt"+fieldid+"").val();
  var texdesc =   $("#txt"+fieldid+"").data("desc");
  
  $('#txtREQUEST_USER_popup').val(texdesc);
  $('#REQUEST_USER').val(txtval);
  $("#REQUEST_USER_popup").hide();
  
  $("#RequestUsercodesearch").val(''); 
  $("#RequestUsernamesearch").val(''); 
  RequestUserCodeFunction();
  event.preventDefault();
});

// Store
let sttid = "#STCodeTable2";
let sttid2 = "#STCodeTable";
let stheaders = document.querySelectorAll(sttid2 + " th");

stheaders.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(sttid, ".clsstid", "td:nth-child(" + (i + 1) + ")");
  });
});

function STCodeFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("stcodesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("STCodeTable2");
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

function STNameFunction() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("stnamesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("STCodeTable2");
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

$('#STID_REF_popup').click(function(event){
  showSelectedCheck($("#STID_REF").val(),"SELECT_STID_REF");
  $("#stidpopup").show();
});

$("#st_closePopup").click(function(event){
  $("#stidpopup").hide();
});

$(".clsstid").click(function(){
  var fieldid = $(this).attr('id');
  var txtval =    $("#txt"+fieldid+"").val();
  var texdesc =   $("#txt"+fieldid+"").data("desc");
  
  $('#STID_REF_popup').val(texdesc);
  $('#STID_REF').val(txtval);
  $("#stidpopup").hide();
  
  $("#stcodesearch").val(''); 
  $("#stnamesearch").val(''); 
  STCodeFunction();
  event.preventDefault();
});

  
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

function ItemCodeFunction(FORMID) {
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
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID); 
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
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID); 
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

function ItemNameFunction(FORMID) {
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
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID); 
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
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID); 
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

function ItemUOMFunction(FORMID) {
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
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID); 
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
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID); 
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

function ItemGroupFunction(FORMID) {
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
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID); 
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
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID); 
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

function ItemCategoryFunction(FORMID) {
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
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID); 
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
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID); 
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

function ItemBUFunction(FORMID) {
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
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID); 
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
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID); 
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

function ItemAPNFunction(FORMID) {
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
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID); 
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
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID); 
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

function ItemCPNFunction(FORMID) {
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
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID); 
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
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID);
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

function ItemOEMPNFunction(FORMID) {
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
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID);
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
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID);
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

function loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID){
	
	var url	=	'<?php echo asset('');?>transaction/'+FORMID+'/getItemDetails';

		$("#tbody_ItemID").html('');
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
		$.ajax({
			url:url,
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
  //Item POPUP
//------------------------
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

      function CustomerCodeFunction(FORMID) {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("customercodesearch");
        filter = input.value.toUpperCase();
        
      if(filter.length == 0)
        {
          var CODE = ''; 
          var NAME = ''; 
          loadCustomer(CODE,NAME,FORMID); 
        }
        else if(filter.length >= 3)
        {
          var CODE = filter; 
          var NAME = ''; 
          loadCustomer(CODE,NAME,FORMID); 
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

  function CustomerNameFunction(FORMID) {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("customernamesearch");
        filter = input.value.toUpperCase();
        if(filter.length == 0)
        {
          var CODE = ''; 
          var NAME = ''; 
          loadCustomer(CODE,NAME,FORMID);
        }
        else if(filter.length >= 3)
        {
          var CODE = ''; 
          var NAME = filter; 
          loadCustomer(CODE,NAME,FORMID);  
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
    
    function loadCustomer(CODE,NAME,FORMID){
      var url	=	'<?php echo asset('');?>transaction/'+FORMID+'/getsubledger';
        $("#tbody_subglacct").html('');
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
        $.ajax({
          url:url,
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
  //CUSTOMER LIST POPUP
//------------------------
//------------------------
  //Vendor Popup Start
  let vltid = "#CodeTable2";
    let vltid2 = "#CodeTable";
    let vlheaders = document.querySelectorAll(vltid2 + " th");

      // Sort the table element when clicking on the table headers
      vlheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(vltid, ".clsclid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function CodeFunction(FORMID) {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("codesearch");
        filter = input.value.toUpperCase();
        if(filter.length == 0)
        {
          var CODE = ''; 
          var NAME = ''; 
          loadVendor(CODE,NAME,FORMID); 
        }
        else if(filter.length >= 3)
        {
          var CODE = filter; 
          var NAME = ''; 
          loadVendor(CODE,NAME,FORMID); 
        }
        else
        {
          table = document.getElementById("CodeTable2");
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

  function NameFunction(FORMID) {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("namesearch");
        filter = input.value.toUpperCase();
        if(filter.length == 0)
        {
          var CODE = ''; 
          var NAME = ''; 
          loadVendor(CODE,NAME,FORMID);
        }
        else if(filter.length >= 3)
        {
          var CODE = ''; 
          var NAME = filter; 
          loadVendor(CODE,NAME,FORMID);  
        }
        else
        {
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
    }
  
  function loadVendor(CODE,NAME,FORMID){
      var url	=	'<?php echo asset('');?>transaction/'+FORMID+'/getsubledger';
        $("#tbody_subglacct").html('');
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
        $.ajax({
          url:url,
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
  
  //Vendor Popup Ends
//------------------------


  $('#Material').on('click','[id*="popupITEMID"]',function(event){

        var CODE = ''; 
        var NAME = ''; 
        var MUOM = ''; 
        var GROUP = ''; 
        var CTGRY = ''; 
        var BUNIT = ''; 
        var APART = ''; 
        var CPART = ''; 
        var OPART = ''; 
        var FORMID = "{{$FormId}}";
        loadItem('',CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID); 
        

        $("#ITEMIDpopup").show();
        var id = $(this).attr('id');
        var id2 = $(this).parent().parent().find('[id*="ITEMID_REF"]').attr('id');
        var id3 = $(this).parent().parent().find('[id*="ItemName"]').attr('id');
        var id4 = $(this).parent().parent().find('[id*="Itemspec"]').attr('id');
        var id5 = $(this).parent().parent().find('[id*="popupMUOM"]').attr('id');
        var id6 = $(this).parent().parent().find('[id*="MAIN_UOMID_REF"]').attr('id');
        var id7 = $(this).parent().parent().find('[id*="SE_QTY"]').attr('id');
       
        var id11 = $(this).parent().parent().find('[id*="SO_FQTY"]').attr('id');

        $('#hdn_ItemID').val(id);
        $('#hdn_ItemID2').val(id2);
        $('#hdn_ItemID3').val(id3);
        $('#hdn_ItemID4').val(id4);
        $('#hdn_ItemID5').val(id5);
        $('#hdn_ItemID6').val(id6);
        $('#hdn_ItemID7').val(id7);
        
        $('#hdn_ItemID11').val(id11);
        event.preventDefault();
      });

      $("#ITEMID_closePopup").click(function(event){
        $("#ITEMIDpopup").hide();
      });

    function bindItemEvents(){

      $('#ItemIDTable2').off(); 
      $('[id*="chkId"]').change(function(){
     
        var fieldid = $(this).parent().parent().attr('id');
        var txtval =   $("#txt"+fieldid+"").val();
        var texdesc =  $("#txt"+fieldid+"").data("desc");
        var fieldid2 = $(this).parent().parent().children('[id*="itemname"]').attr('id');
        var txtname =  $("#txt"+fieldid2+"").val();
        var txtspec =  $("#txt"+fieldid2+"").data("desc");
        var fieldid3 = $(this).parent().parent().children('[id*="itemuom"]').attr('id');
        var txtmuomid =  $("#txt"+fieldid3+"").val();
        var txtauom =  $("#txt"+fieldid3+"").data("desc");
        var txtmuom =  $(this).parent().parent().children('[id*="itemuom"]').text().trim();
        var fieldid4 = $(this).parent().parent().children('[id*="uomqty"]').attr('id');
        var txtauomid =  $("#txt"+fieldid4+"").val();
        var txtauomqty =  $("#txt"+fieldid4+"").data("desc");
        var txtmuomqty =  $(this).parent().parent().children('[id*="uomqty"]').text().trim();
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
                      $('.js-selectall1').prop('checked', false); 
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
                        $clone.find('[id*="Itemspec"]').val(txtspec);
                        $clone.find('[id*="popupMUOM"]').val(txtmuom);
                        $clone.find('[id*="MAIN_UOMID_REF"]').val(txtmuomid);
                        $clone.find('[id*="SE_QTY"]').val(txtmuomqty);
                        
                        
                        $tr.closest('table').append($clone);   
                        var rowCount = $('#Row_Count1').val();
                          rowCount = parseInt(rowCount)+1;
                          $('#Row_Count1').val(rowCount);

                        $('.js-selectall1').prop('checked', false); 
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
                      var txt_id11= $('#hdn_ItemID11').val();

                      $('#'+txtid).val(texdesc);
                      $('#'+txt_id2).val(txtval);
                      $('#'+txt_id3).val(txtname);
                      $('#'+txt_id4).val(txtspec);
                      $('#'+txt_id5).val(txtmuom);
                      $('#'+txt_id6).val(txtmuomid);
                      $('#'+txt_id7).val(txtmuomqty);
                     
                      $('#hdn_ItemID').val('');
                      $('#hdn_ItemID2').val('');
                      $('#hdn_ItemID3').val('');
                      $('#hdn_ItemID4').val('');
                      $('#hdn_ItemID5').val('');
                      $('#hdn_ItemID6').val('');
                      $('#hdn_ItemID7').val('');
                      $('#hdn_ItemID11').val('');
                      
                      }

                      $('.js-selectall1').prop('checked', false); 
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
                $('.js-selectall1').prop('checked', false); 
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
        ItemCodeFunction();
        event.preventDefault();
      });
    }


    let pttid = "#PriorityTable2";
let pttid2 = "#PriorityTable";
let ptheaders = document.querySelectorAll(pttid2 + " th");

ptheaders.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(pttid, ".clsptid", "td:nth-child(" + (i + 1) + ")");
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

$('#Material').on('click','[id*="PRIORITYID"]',function(event){
    
    var id = $(this).attr('id');
    var id2 = $(this).parent().parent().find('[id*="PTID_REF"]').attr('id');

    var fieldid = $(this).parent().parent().find('[id*="PTID_REF"]').attr('id');

    if($(this).val() !=""){
      showSelectedCheck($("#"+fieldid).val(),"SELECT_PTID_REF");
    }
    else{
      $(".clsptid").prop('checked', false);
    }

    $('#hdn_Priority').val(id);
    $('#hdn_Priority2').val(id2);

    $("#Prioritypopup").show();
});

$("#PriorityclosePopup").click(function(event){
  $("#Prioritypopup").hide();
});

$(".clsptid").click(function(){
  var fieldid = $(this).attr('id');
  var txtval =    $("#txt"+fieldid+"").val();
  var texdesc =   $("#txt"+fieldid+"").data("desc");

  var txtid = $('#hdn_Priority').val();
  var txt_id2 = $('#hdn_Priority2').val();
  $('#'+txtid).val(texdesc);
  $('#'+txt_id2).val(txtval);

  $("#Prioritypopup").hide();
  
  $("#Prioritycodesearch").val(''); 
  $("#Prioritynamesearch").val(''); 
  PriorityCodeFunction();
  event.preventDefault();
});
   
$(document).ready(function(e) {
var Material = $("#Material").html(); 
$('#hdnmaterial').val(Material);

var lastdt = <?php echo json_encode(isset($objResponse->MRS_DT)?$objResponse->MRS_DT:''); ?>;
var today = new Date(); 
var sodate = <?php echo json_encode(isset($objResponse->MRS_DT)?$objResponse->MRS_DT:''); ?>;
$('#MRS_DT').attr('min',lastdt);
$('#MRS_DT').attr('max',sodate);

//$('[id*="EDD"]').attr('min',sodate);


var seudf = <?php echo json_encode($objUdfData); ?>;
var count2 = <?php echo json_encode($objCountUDF); ?>;

$('#example3').find('.participantRow3').each(function(){
      var txt_id4 = $(this).find('[id*="udfinputid"]').attr('id');
      var udfid = $(this).find('[id*="UDF"]').val();

      $.each( seudf, function( seukey, seuvalue ) {
        if(seuvalue.UDFMRSID == udfid)
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

var soudf = <?php echo json_encode($objUDF); ?>;
var udfforse = <?php echo json_encode($objUdfData2); ?>;
$.each( soudf, function( soukey, souvalue ) {

    $.each( udfforse, function( usokey, usovalue ) { 
        if(souvalue.UDF == usovalue.UDFMRSID)
        {
            $('#popupSEID_'+soukey).val(usovalue.LABEL);
        }
    
        if(souvalue.UDF == usovalue.UDFMRSID){        
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
                $('#'+txt_id41).html(strinp2);   
                $('#'+dynamicid2).val(souvalue.COMMENT);
                $('#UDFismandatory_'+soukey).val(usovalue.ISMANDATORY);
            
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

$('#MRS_DT').change(function() {
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
  var d     = new Date(); 
  //var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
  var today = <?php echo json_encode(isset($objResponse->MRS_DT)?$objResponse->MRS_DT:''); ?>;

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
    window.location.reload();
}

window.fnUndoNo = function (){
    $("#ENQNO").focus();
}

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
   
    $('#transaction_form1').bootstrapValidator({
       
        fields: {
            txtlabel: {
                validators: {
                    notEmpty: {
                        message: 'The MRS No is required'
                    }
                }
            },            
        },
        submitHandler: function(validator, form, submitButton) {
            alert( "Handler for .submit() called." );
             event.preventDefault();
             $("#transaction_form").submit();
        }
    });
});


$( "#btnSaveSE" ).click(function() {
    var formReqData = $("#transaction_form");
    if(formReqData.valid()){
      validateForm('fnSaveData');
    }
});

$( "#btnApprove" ).click(function() {
    var formReqData = $("#transaction_form");
    if(formReqData.valid()){
      validateForm('fnApproveData');
    }
});


$("#YesBtn").click(function(){

$("#alert").modal('hide');
var customFnName = $("#YesBtn").data("funcname");
    window[customFnName]();

});

window.fnSaveData = function (){
event.preventDefault();
      var trnFormReq = $("#transaction_form");
      var formData = trnFormReq.serialize();
  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });
  $.ajax({
      url:'{{ route("transactionmodify",[$FormId,"update"])}}',
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
      var trnFormReq = $("#transaction_form");
      var formData = trnFormReq.serialize();
  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });
  $.ajax({
      url:'{{ route("transactionmodify",[$FormId,"Approve"])}}',
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

function validateForm(actionType){
 
  $("#FocusId").val('');
  var MRS_NO          =   $.trim($("#MRS_NO").val());
  var MRS_DT          =   $.trim($("#MRS_DT").val());
  var DEPID_REF       =   $.trim($("#DEPID_REF").val());
  var SLID_REF       =   $.trim($("#SLID_REF").val());
  var STID_REF       =   $.trim($("#STID_REF").val());
  var ENQBY          =   $.trim($("#ENQBY").val());
  var PRIORITYID_REF =   $.trim($("#PRIORITYID_REF").val());
  var REQUEST_USER       =   $.trim($("#REQUEST_USER").val());

  if(MRS_NO ===""){
     $("#FocusId").val('MRS_NO');
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Enter MRS No.');
     $("#alert").modal('show')
     $("#OkBtn1").focus();
     return false;
 }
 else if(MRS_DT ===""){
     $("#FocusId").val('MRS_DT');
     $("#MRS_DT").val(today);  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Select MRS Date.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }  
 else if(REQUEST_USER ===""){
     $("#FocusId").val('txtREQUEST_USER_popup');
     $("#REQUEST_USER").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Select Request User.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 } 
 else if(DEPID_REF ===""){
     $("#FocusId").val('txtdep_popup');
     $("#DEPID_REF").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please select department.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 } 
 else if(STID_REF ===""){
     $("#FocusId").val($("#STID_REF_popup"));
     $("#STID_REF").val(''); 
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Select From Department.');
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
                    if($.trim($(this).find('[id*="SE_QTY"]').val()) != "" && $.trim($(this).find('[id*="SE_QTY"]').val()) > 0.000)
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
                  allblank6.push('true');
              }
              else{
                  allblank6.push('false');
                  focustext6 = $(this).find("[id*=PRIORITYID]").attr('id');
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
        if($.trim($(this).find("[id*=UDF]").val())!="")
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
    $("#MAT_TAB").click();
    $("#FocusId").val(focustext1);
    $("#alert").modal('show');
    $("#AlertMessage").text('Please select item in material tab.');
    $("#YesBtn").hide(); 
    $("#NoBtn").hide();  
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank2) !== -1){
    $("#MAT_TAB").click();
    $("#FocusId").val(focustext2);
    $("#alert").modal('show');
    $("#AlertMessage").text('Main UOM is missing in in material tab.');
    $("#YesBtn").hide(); 
    $("#NoBtn").hide();  
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank3) !== -1){
    $("#MAT_TAB").click();
    $("#FocusId").val(focustext3);
    $("#alert").modal('show');
    $("#AlertMessage").text('Quantity cannot be zero or blank in material tab.');
    $("#YesBtn").hide(); 
    $("#NoBtn").hide();  
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank6) !== -1){
    $("#MAT_TAB").click();
    $("#FocusId").val(focustext6);
    $("#alert").modal('show');
    $("#AlertMessage").text('Please select priority in material tab.');
    $("#YesBtn").hide(); 
    $("#NoBtn").hide();  
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank8) !== -1){
    $("#MAT_TAB").click();
    $("#FocusId").val(focustext8);
    $("#alert").modal('show');
    $("#AlertMessage").text('Expected date  cannot be blank in material tab.');
    $("#YesBtn").hide(); 
    $("#NoBtn").hide();  
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank9) !== -1){
    $("#MAT_TAB").click();
    $("#FocusId").val(focustext9);
    $("#alert").modal('show');
    $("#AlertMessage").text('Expected date should not less then current date in material tab.');
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
    $("#AlertMessage").text('Please enter  Value / Comment in UDF Tab.');
    $("#YesBtn").hide(); 
    $("#NoBtn").hide();  
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk');
    }
    else{
          $("#alert").modal('show');
          $("#AlertMessage").text('Do you want to save to record.');
          $("#YesBtn").data("funcname",actionType);
          $("#YesBtn").focus();
          $("#OkBtn").hide();
          highlighFocusBtn('activeYes');
    }
  }
}

function LessDateValidateWithToday(value,existDate){

    if(value !=""){
        var today = new Date(<?php echo json_encode(isset($objResponse->MRS_DT)?$objResponse->MRS_DT:''); ?>); 
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

var objSO = <?php echo json_encode($objResponse); ?>;

$('#btnPdf').on('click', function() {
  var MRSID = objSO.MRSID;
  var Flag = 'P';
  var formData = 'MRS='+ MRSID + '&MRSID='+ MRSID + '&Flag='+ Flag ;
  var consultURL = '{{route("transaction",[88,"ViewReport",":rcdId"]) }}';
  consultURL = consultURL.replace(":rcdId",formData);
  window.location.href=consultURL;
  event.preventDefault();
}); 

$('#btnExcel').on('click', function() {
  var MRSID = objSO.MRSID;
  var Flag = 'E';
  var formData = 'MRS='+ MRSID + '&MRSID='+ MRSID + '&Flag='+ Flag ;
  var consultURL = '{{route("transaction",[88,"ViewReport",":rcdId"]) }}';
  consultURL = consultURL.replace(":rcdId",formData);
  window.location.href=consultURL;
  event.preventDefault();
});

$('#btnPrint').on('click', function() {
    var MRSID = objSO.MRSID;
    var Flag = 'H';
    var formData = 'MRS='+ MRSID + '&MRSID='+ MRSID + '&Flag='+ Flag ;
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url:'{{route("transaction",[88,"ViewReport"])}}',
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
    var MRSID = objSO.MRSID;
    var Flag = 'R';
    var formData = 'MRS='+ MRSID + '&MRSID='+ MRSID + '&Flag='+ Flag ;
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url:'{{route("transaction",[88,"ViewReport"])}}',
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

</script>
@endpush