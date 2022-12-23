@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
        <div class="row">
            <div class="col-lg-2">
            <a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Additional Material Issue (AMI)</a>
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
                    <button style="display:none" class="btn topnavbt buttonload_approve" > <i class="fa fa-refresh fa-spin"></i> {{Session::get('approve')}}</button>
                    <button class="btn topnavbt" id="btnApprove" {{ (isset($objRights->APPROVAL1) || isset($objRights->APPROVAL2) || isset($objRights->APPROVAL3) || isset($objRights->APPROVAL4) || isset($objRights->APPROVAL5)) &&  ($objRights->APPROVAL1||$objRights->APPROVAL2||$objRights->APPROVAL3||$objRights->APPROVAL4||$objRights->APPROVAL5) == 1 ? '' : 'disabled'}}><i class="fa fa-thumbs-o-up"></i> Approved</button>
                    <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                    <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
            </div>
        </div>
</div>

<form id="trn_frm_mis"  method="POST">   
    @csrf
    {{isset($objResponse->MISPID[0]) ? method_field('PUT') : '' }}
    <div class="container-fluid filter">
	<div class="inner-form">

		<div class="row">

			<div class="col-lg-1 pl"><p>AMI No*</p></div>
			<div class="col-lg-2 pl">
          <input {{$ActionStatus}} type="text" name="MIS_NO" id="MIS_NO" value="{{isset($objResponse->MISP_NO) && $objResponse->MISP_NO !=''?$objResponse->MISP_NO:''}}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase"  >
      </div>
			
			<div class="col-lg-1 pl"><p>AMI Date*</p></div>
			<div class="col-lg-2 pl">
			    <input {{$ActionStatus}} type="date" name="MIS_DT" id="MIS_DT" onchange="checkPeriodClosing('{{$FormId}}',this.value,1)" value="{{isset($objResponse->MISP_DT) && $objResponse->MISP_DT !=''?$objResponse->MISP_DT:''}}" class="form-control mandatory" >
      </div>

      <div class="col-lg-2 pl"><p>Department*</p></div>
			<div class="col-lg-2 pl">
          <input {{$ActionStatus}} type="text" name="DEPID_REF_popup" id="txtdep_popup" value="{{isset($objDepartmentName->DCODE) && $objDepartmentName->DCODE !=''?$objDepartmentName->DCODE.' - '.$objDepartmentName->NAME:''}}" class="form-control mandatory"  autocomplete="off" readonly/>
          <input type="hidden" name="DEPID_REF" id="DEPID_REF" value="{{isset($objResponse->DEPID_REF) && $objResponse->DEPID_REF !=''?$objResponse->DEPID_REF:''}}" class="form-control" autocomplete="off" />
			</div>

		</div>

    
  </div>

	<div class="container-fluid">

		<div class="row">
			<ul class="nav nav-tabs">
				<li class="active"><a data-toggle="tab" href="#Material">Material</a></li>
				<!--<li><a data-toggle="tab" href="#udf">UDF</a></li>-->
			</ul>
      Note:- 1 row mandatory in Material Tab	
			
			
			<div class="tab-content">

				<div id="Material" class="tab-pane fade in active">
					<div class="table-responsive table-wrapper-scroll-y" style="height:280px;margin-top:10px;" >
						<table id="example2" class="display nowrap table table-striped table-bordered itemlist w-200" width="100%" style="height:auto !important;">
							<thead id="thead1"  style="position: sticky;top: 0">
              <tr>
                  <th>AMR No</th>
                  <th>PRO No</th>
									<th>Item Code <input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1"></th>
									<th>Item Name</th>

                  <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</th>
                  <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</th>
                  <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</th>

                  <th>Item Specifications</th>
                  <th>Store</th>
                  
                  <th>AMR Qty</th>
									<th>Main UoM (MU)</th>
                  <th>Stock-in-hand</th>
                  <th>Issued Qty (MU)</th>
                  <th>Alt UOM (AU)</th>
                  
                  <th>Reason of Short Qty Issued</th>
                  <th>Remarks</th>
									<th>Action</th>
								  </tr>
							</thead>
							<tbody>
              @if(!empty($objMAT))
              @foreach($objMAT as $key => $row)
							<tr  class="participantRow">

                  <td><input {{$ActionStatus}} style="width:100px;" type="text" name="txtRGP_popup_{{$key}}" id="txtRGP_popup_{{$key}}" value="{{ $row->MRSP_NO }}" class="form-control"  autocomplete="off"  readonly style="width:100px;" /></td>
                  <td><input type="text" name="PRO_NO_{{$key}}" id="PRO_NO_{{$key}}" value="{{ $row->PRO_NO }}" class="form-control" autocomplete="off" style="width:130px;" readonly   /></td>
                  <td hidden><input type="hidden" name="RGP_NO_{{$key}}" id="RGP_NO_{{$key}}" value="{{ $row->MRSPID_REF }}"  class="form-control" autocomplete="off" /></td>
                  
                  <td><input {{$ActionStatus}} style="width:100px;" type="text" name={{"popupITEMID_".$key}} id={{"popupITEMID_".$key}} class="form-control" value="{{ $row->ICODE }}"  autocomplete="off"  readonly/></td>
                  <td hidden><input type="hidden" name={{"ITEMID_REF_".$key}} id={{"ITEMID_REF_".$key}} class="form-control"  value="{{ $row->ITEMID_REF }}" autocomplete="off" /></td>
                  <td><input {{$ActionStatus}} style="width:200px;" type="text" name={{"ItemName_".$key}} id={{"ItemName_".$key}} class="form-control" value="{{ $row->ITEM_NAME }}"  autocomplete="off"   readonly/></td>
                  
                  <td {{$AlpsStatus['hidden']}}><input  type="text" name="Alpspartno_{{$key}}" id="Alpspartno_{{$key}}" class="form-control"  autocomplete="off" value="{{ isset($row->ALPS_PART_NO)?$row->ALPS_PART_NO:'' }}" readonly/></td>
                  <td {{$AlpsStatus['hidden']}}><input  type="text" name="Custpartno_{{$key}}" id="Custpartno_{{$key}}" class="form-control"  autocomplete="off" value="{{ isset($row->CUSTOMER_PART_NO)?$row->CUSTOMER_PART_NO:'' }}" readonly/></td>
                  <td {{$AlpsStatus['hidden']}}><input  type="text" name="OEMpartno_{{$key}}"  id="OEMpartno_{{$key}}" class="form-control"  autocomplete="off" value="{{ isset($row->OEM_PART_NO)?$row->OEM_PART_NO:'' }}" readonly/></td>

                  
                  <td><input {{$ActionStatus}} type="text" name={{"Itemspec_".$key}} id={{"Itemspec_".$key}} value="{{ $row->ITEM_SPECI }}" class="form-control"  autocomplete="off"    /></td>
                  
                  <td align="center"><a {{$ActionStatus}} class="btn checkstore"  id="{{$key}}" ><i class="fa fa-clone"></i></a></td>
                  <td hidden ><input type="hidden" name="TotalHiddenQty_{{$key}}" id="TotalHiddenQty_{{$key}}" value="{{ $row->ISSUED_QTY}}" ></td>
                  <td hidden ><input type="hidden" name="HiddenRowId_{{$key}}" id="HiddenRowId_{{$key}}" value="{{ $row->BATCH_QTY}}" ></td>
                  

                  <td hidden><input type="text" name="PO_PENDING_QTY_{{$key}}" id="PO_PENDING_QTY_{{$key}}" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" readonly  /></td>
                         

                  <td><input {{$ActionStatus}} type="text" name={{"SE_QTY_".$key}} id={{"SE_QTY_".$key}} class="form-control three-digits" readonly onkeypress="return isNumberDecimalKey(event,this)" maxlength="13" value="{{ $row->MRSP_QTY_BL}}" autocomplete="off"  /></td>
                  
                  <td><input {{$ActionStatus}} type="text" name={{"popupMUOM_".$key}} id={{"popupMUOM_".$key}} class="form-control" value="{{ $row->MAIN_UOM_CODE}}"  autocomplete="off"  readonly/></td>
                  <td hidden><input type="hidden" name={{"MAIN_UOMID_REF_".$key}} id={{"MAIN_UOMID_REF_".$key}} value="{{ $row->MAIN_UOMID_REF}}" class="form-control"   autocomplete="off" /></td>
                  
                  <td><input {{$ActionStatus}} type="text" name="STOCK_INHAND_{{$key}}" id="STOCK_INHAND_{{$key}}" value="{{ $row->STOCK_INHAND }}" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" readonly  autocomplete="off"   /></td>
                  <td><input {{$ActionStatus}} type="text" name="RECEIVED_QTY_MU_{{$key}}" id="RECEIVED_QTY_MU_{{$key}}" value="{{ $row->ISSUED_QTY }}" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off"   /></td>
                  
                  <td><input {{$ActionStatus}} type="text" name="popupALTUOM_{{$key}}" id="popupALTUOM_{{$key}}" class="form-control" value="{{ $row->AULT_UOM_CODE}}"  autocomplete="off"  readonly/></td>
                  <td hidden><input type="hidden" name="ALT_UOMID_REF_{{$key}}" id="ALT_UOMID_REF_{{$key}}" value="{{ $row->ALT_UOMID_REF}}" class="form-control"  autocomplete="off" /></td>

                  <td hidden><input type="text" name="RECEIVED_QTY_AU_{{$key}}" id="RECEIVED_QTY_AU_{{$key}}"  class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" readonly maxlength="13"  autocomplete="off"   /></td>
                  <td hidden><input type="text" name="SHORT_QTY_{{$key}}" id="SHORT_QTY_{{$key}}"  class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off"  readonly  /></td>
                          
                  <td hidden><input type="hidden" name={{"SO_FQTY_".$key}} id={{"SO_FQTY_".$key}} class="form-control three-digits" maxlength="13"  autocomplete="off"  readonly/></td>
                 
                  
                  <td><input {{$ActionStatus}} type="text" name="REASON_SHORT_QTY_{{$key}}" id="REASON_SHORT_QTY_{{$key}}" value="{{ $row->REASON_SHORT_QTY }}" class="form-control"   autocomplete="off"   /></td>
                  <td><input {{$ActionStatus}} style="width:200px;" type="text" name="REMARKS_{{$key}}" id="REMARKS_{{$key}}" value="{{ $row->REMARKS }}" class="form-control w-100" autocomplete="off" ></td>
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
              @foreach($objUDF as $uindex=>$uRow)
                <tr  class="participantRow3">
                    <td><input  {{$ActionStatus}} type="text" name={{"popupSEID_".$uindex}} id={{"popupSEID_".$uindex}} class="form-control" value="{{$uRow->UDF}}" autocomplete="off"  readonly/></td>
                    <td hidden><input type="hidden" name={{"UDF_".$uindex}} id={{"UDF_".$uindex}} class="form-control" value="{{$uRow->UDF}}" autocomplete="off"   /></td>
                    <td hidden><input type="hidden" name={{"UDFismandatory_".$uindex}} id={{"UDFismandatory_".$uindex}} value="{{$uRow->UDF}}" class="form-control"   autocomplete="off" /></td>
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

<div id="StoreModal" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:80%;z-index:1">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='StoreModalClose' >&times;</button>
      </div>
      <div class="modal-body">
	      <div class="tablename"><p>Store Details</p></div>
        <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
          <table id="StoreTable" class="display nowrap table  table-striped table-bordered" style="width: 100%;" >
          </table>
        </div>
		    <div class="cl"></div>
      </div>
    </div>
  </div>
</div>

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
          <td class="ROW2">{{ $val-> DCODE }}
          <input type="hidden" id="txtdpidcode_{{ $key }}" data-desc="{{ $val-> DCODE }} - {{ $val-> NAME }}"  value="{{ $val-> DEPID }}"/>
          </td>
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

<div id="RGPpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='RGP_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>AMR No</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="RGPTable" class="display nowrap table  table-striped table-bordered" >
    <thead>
          <tr id="none-select" class="searchalldata" hidden>
            
            <td> 
            <input type="hidden" id="hdn_sqid"/>
            <input type="hidden" id="hdn_sqid2"/>
            <input type="hidden" id="hdn_sqid3"/>
            </td>
          </tr>

    <tr>
      <th class="ROW1">Select</th> 
      <th class="ROW2">AMR No</th>
      <th class="ROW3">Date</th>
    </tr>

    </thead>
    <tbody>


    <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="RGPcodesearch" class="form-control" autocomplete="off" onkeyup="RGPCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="RGPnamesearch" class="form-control" autocomplete="off" onkeyup="RGPNameFunction()"></td>
      </tr>
    </tbody>
    </table>
      <table id="RGPTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2">

        </thead>
        <tbody id="tbody_RGP">     
        
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
            <th style="width:8%;" id="all-check">Select</th>
            <th style="width:10%;">Item Code</th>
            <th style="width:10%;">Name</th>
            <th style="width:8%;">Main UOM</th>
            <th style="width:8%;">Main QTY</th>
            <th style="width:8%;">Item Group</th>
            <th style="width:8%;">Item Category</th>
            <th style="width:8%;">Business Unit</th>
            <th style="width:8%;" {{$AlpsStatus['hidden']}}>{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</th>
            <th style="width:8%;" {{$AlpsStatus['hidden']}}>{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</th>
            <th style="width:8%;" {{$AlpsStatus['hidden']}}>{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</th>
            <th style="width:8%;">Status</th>
      </tr>
    </thead>
    <tbody>
    <tr>
    <td style="width:8%;text-align:center;"><input type="checkbox" class="js-selectall" data-target=".js-selectall1" /></td>
    <td style="width:10%;">
    <input type="text" id="Itemcodesearch" class="form-control" autocomplete="off" onkeyup="ItemCodeFunction(event)">
    </td>
    <td style="width:10%;">
    <input type="text" id="Itemnamesearch" class="form-control" autocomplete="off" onkeyup="ItemNameFunction(event)">
    </td>
    <td style="width:8%;">
    <input type="text" id="ItemUOMsearch" class="form-control" autocomplete="off" onkeyup="ItemUOMFunction(event)">
    </td>
    <td style="width:8%;">
    <input type="text" id="ItemQTYsearch" class="form-control" autocomplete="off" onkeyup="ItemQTYFunction(event)">
    </td>
    <td style="width:8%;">
    <input type="text" id="ItemGroupsearch" class="form-control" autocomplete="off" onkeyup="ItemGroupFunction(event)">
    </td>
    <td style="width:8%;">
    <input type="text" id="ItemCategorysearch" class="form-control" autocomplete="off" onkeyup="ItemCategoryFunction(event)">
    </td>

    <td style="width:8%;"><input type="text" id="ItemBUsearch" class="form-control" autocomplete="off" onkeyup="ItemBUFunction(event)"></td>
    <td style="width:8%;" {{$AlpsStatus['hidden']}}><input type="text" id="ItemAPNsearch" class="form-control" autocomplete="off" onkeyup="ItemAPNFunction(event)"></td>
    <td style="width:8%;" {{$AlpsStatus['hidden']}}><input type="text" id="ItemCPNsearch" class="form-control" autocomplete="off" onkeyup="ItemCPNFunction(event)"></td>
    <td style="width:8%;" {{$AlpsStatus['hidden']}}><input type="text" id="ItemOEMPNsearch" class="form-control" autocomplete="off" onkeyup="ItemOEMPNFunction(event)"></td>

    <td style="width:8%;">
    <input type="text" id="ItemStatussearch" class="form-control" autocomplete="off" onkeyup="ItemStatusFunction(event)">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="ItemIDTable2" class="display nowrap table  table-striped table-bordered" style="width:100%;" >
        <thead id="thead2">

        </thead>
        <tbody id="tbody_ItemID">     
          
        <div class="loader" id="item_loader" style="display:none;"></div>
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

/*=======================================================================================*/
/*================================== DEPARTMENT DETAILS =================================*/
/*=======================================================================================*/

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

    var oldDEPID_REF    =   $("#DEPID_REF").val();
    var MaterialClone =  $('#hdnmaterial').val();
    
    $('#txtdep_popup').val(texdesc);
    $('#DEPID_REF').val(txtval);

    if (txtval != oldDEPID_REF){ 
      $('#Material').html(MaterialClone);
      var count11 = <?php echo json_encode($objCount1); ?>;
      $('#Row_Count1').val(count11);
      $('#example2').find('.participantRow').each(function(){
        $(this).find('input:text').val('');
        var rowcount = $('#Row_Count1').val();
        if(rowcount > 1)
        {
          $(this).closest('.participantRow').remove();
          rowcount = parseInt(rowcount) - 1;
          $('#Row_Count1').val(rowcount);
        }
      });
    }

    $("#dpidpopup").hide();
    $("#dpcodesearch").val(''); 
    $("#dpnamesearch").val(''); 
   
    event.preventDefault();
  });

      
/*=================================================================================*/
/*================================== ITEM DETAILS =================================*/
/*=================================================================================*/

let itemtid = "#ItemIDTable2";
let itemtid2 = "#ItemIDTable";
let itemtidheaders = document.querySelectorAll(itemtid2 + " th");

itemtidheaders.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(itemtid, ".clsitemid", "td:nth-child(" + (i + 1) + ")");
  });
});

function ItemCodeFunction(e) {
  if(e.which == 13){
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
}

function ItemNameFunction(e) {
  if(e.which == 13){
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
}

function ItemUOMFunction(e) {
  if(e.which == 13){
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
}

function ItemQTYFunction(e) {
  if(e.which == 13){
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
}

function ItemGroupFunction(e) {
  if(e.which == 13){
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
}

function ItemCategoryFunction(e) {
  if(e.which == 13){
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
}

function ItemBUFunction(e) {
  if(e.which == 13){
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
}

function ItemAPNFunction(e) {
  if(e.which == 13){
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
}

function ItemCPNFunction(e) {
  if(e.which == 13){
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
}

function ItemOEMPNFunction(e) {
  if(e.which == 13){
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
}

function ItemStatusFunction(e) {
  if(e.which == 13){
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
}

$('#Material').on('click','[id*="popupITEMID"]',function(event){

    var RGP_NO    = $(this).parent().parent().find('[id*="RGP_NO"]').val();
    //var POID_REF  = $(this).parent().parent().find('[id*="POID_REF"]').val();
    $('#item_loader').show();
    $("#tbody_ItemID").html('');
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
      $.ajax({
          url:'{{route("transaction",[$FormId,"getItemDetails"])}}',
          type:'POST',
          data:{'status':'A',RGP_NO:RGP_NO},
          success:function(data) {
            $('#item_loader').hide();
            $("#tbody_ItemID").html(data);    
            bindItemEvents();   
            //$('.js-selectall').prop('disabled', true);                     
          },
          error:function(data){
            console.log("Error: Something went wrong.");
            $('#item_loader').hide();
            $("#tbody_ItemID").html('');                        
          },
      }); 
        

      $("#ITEMIDpopup").show();
      var id = $(this).attr('id');
      var id2 = $(this).parent().parent().find('[id*="ITEMID_REF"]').attr('id');
      var id3 = $(this).parent().parent().find('[id*="ItemName"]').attr('id');
      var id4 = $(this).parent().parent().find('[id*="Itemspec"]').attr('id');
      var id5 = $(this).parent().parent().find('[id*="popupMUOM"]').attr('id');
      var id6 = $(this).parent().parent().find('[id*="MAIN_UOMID_REF"]').attr('id');
      var id7 = $(this).parent().parent().find('[id*="SE_QTY"]').attr('id');
      var id11 = $(this).parent().parent().find('[id*="SO_FQTY"]').attr('id');

      var id12 = $(this).parent().parent().find('[id*="TotalHiddenQty"]').attr('id');
      var id13 = $(this).parent().parent().find('[id*="HiddenRowId"]').attr('id');

      var id8 = $(this).parent().parent().find('[id*="popupALTUOM"]').attr('id');
      var id9 = $(this).parent().parent().find('[id*="ALT_UOMID_REF"]').attr('id');
      var id10 = $(this).parent().parent().find('[id*="RECEIVED_QTY_AU"]').attr('id');
      var id14 = $(this).parent().parent().find('[id*="PO_PENDING_QTY"]').attr('id');

      

      $('#hdn_ItemID').val(id);
      $('#hdn_ItemID2').val(id2);
      $('#hdn_ItemID3').val(id3);
      $('#hdn_ItemID4').val(id4);
      $('#hdn_ItemID5').val(id5);
      $('#hdn_ItemID6').val(id6);
      $('#hdn_ItemID7').val(id7);
      $('#hdn_ItemID11').val(id11);
      $('#hdn_ItemID12').val(id12);
      $('#hdn_ItemID13').val(id13);

      $('#hdn_ItemID8').val(id8);
      $('#hdn_ItemID9').val(id9);
      $('#hdn_ItemID10').val(id10);
      $('#hdn_ItemID14').val(id14);

      event.preventDefault();
});

$("#ITEMID_closePopup").click(function(event){
  $("#ITEMIDpopup").hide();
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
      var texdesc =  $("#txt"+fieldid+"").data("desc");
      var fieldid2 = $(this).children('[id*="itemname"]').attr('id');
      var txtname =  $("#txt"+fieldid2+"").val();
      var txtspec =  $("#txt"+fieldid2+"").data("desc");
      var fieldid3 = $(this).children('[id*="itemuom"]').attr('id');
      var txtmuomid =  $("#txt"+fieldid3+"").val();
      var txtauom =  $("#txt"+fieldid3+"").data("desc");
      var txtmuom =  $(this).children('[id*="itemuom"]').text().trim();
      var fieldid4 = $(this).children('[id*="uomqty"]').attr('id');
      var txtauomid =  $("#txt"+fieldid4+"").val();
      var txtauomqty =  $("#txt"+fieldid4+"").data("desc");
      var txtmuomqty =  $(this).children('[id*="uomqty"]').text().trim();
      var fieldid5 = $(this).children('[id*="irate"]').attr('id');
      var txtruom =  $("#txt"+fieldid5+"").val();
      var txtmqtyf = $("#txt"+fieldid5+"").data("desc");
      var fieldid6 = $(this).children('[id*="itax"]').attr('id');

      var apartno =  $("#addinfo"+fieldid+"").data("desc101");
      var cpartno =  $("#addinfo"+fieldid+"").data("desc102");
      var opartno =  $("#addinfo"+fieldid+"").data("desc103");

      txtauomqty = (parseFloat(txtmuomqty)/parseFloat(txtmqtyf))*parseFloat(txtauomqty);

      var desc6         =  $("#txt"+fieldid+"").data("desc6");
      var AultUomQty    =  $("#txt"+fieldid+"").data("desc7");
      var PoPendingQty  =  $("#txt"+fieldid+"").data("desc8");
   
      if(intRegex.test(txtauomqty)){
          txtauomqty = (txtauomqty +'.000');
      }

      if(intRegex.test(txtmuomqty)){
        txtmuomqty = (txtmuomqty +'.000');
      }

   
   
      if($(this).find('[id*="chkId"]').is(":checked") == true){

   $('#example2').find('.participantRow').each(function(){
     var RGPNO = $(this).find('[id*="RGP_NO"]').val();
     var itemid = $(this).find('[id*="ITEMID_REF"]').val();
      
      var exist_val=RGPNO+'-'+itemid;

      if(txtval){
           if(desc6 == exist_val){
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

                 $('#hdn_ItemID12').val('');
                 $('#hdn_ItemID13').val('');

                 $('#hdn_ItemID8').val('');
                 $('#hdn_ItemID9').val('');
                 $('#hdn_ItemID10').val('');
                 $('#hdn_ItemID14').val('');
                
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


   if($('#hdn_ItemID').val() == "" && txtval != ''){

     var txtid= $('#hdn_ItemID').val();
     var txt_id2= $('#hdn_ItemID2').val();
     var txt_id3= $('#hdn_ItemID3').val();
     var txt_id4= $('#hdn_ItemID4').val();
     var txt_id5= $('#hdn_ItemID5').val();
     var txt_id6= $('#hdn_ItemID6').val();
     var txt_id7= $('#hdn_ItemID7').val();
     var txt_id11= $('#hdn_ItemID11').val();

     var txt_id8= $('#hdn_ItemID8').val();
     var txt_id9= $('#hdn_ItemID9').val();
     var txt_id10= $('#hdn_ItemID10').val();
     var txt_id14= $('#hdn_ItemID14').val();

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

     $clone.find('[id*="popupALTUOM"]').val(txtauom);
     $clone.find('[id*="ALT_UOMID_REF"]').val(txtauomid);
     $clone.find('[id*="RECEIVED_QTY_AU"]').val(AultUomQty);
     $clone.find('[id*="PO_PENDING_QTY"]').val(PoPendingQty);

    $clone.find('[id*="Alpspartno"]').val(apartno);
    $clone.find('[id*="Custpartno"]').val(cpartno);
    $clone.find('[id*="OEMpartno"]').val(opartno);
     
     $clone.find('[id*="TotalHiddenQty"]').val('');
     $clone.find('[id*="HiddenRowId"]').val('');

     $clone.find('[id*="REMARKS"]').val('');
     
     $tr.closest('table').append($clone);   
     var rowCount = $('#Row_Count1').val();
       rowCount = parseInt(rowCount)+1;
       $('#Row_Count1').val(rowCount);
       
       $("#ITEMIDpopup").hide();
     event.preventDefault();
   }
   else{

     $('#'+$('#hdn_ItemID12').val()).val('');
     $('#'+$('#hdn_ItemID13').val()).val('');

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
     var txt_id14= $('#hdn_ItemID14').val();

     $('#'+txtid).val(texdesc);
     $('#'+txt_id2).val(txtval);
     $('#'+txt_id3).val(txtname);
     $('#'+txt_id4).val(txtspec);
     $('#'+txt_id5).val(txtmuom);
     $('#'+txt_id6).val(txtmuomid);
     $('#'+txt_id7).val(txtmuomqty);

     $('#'+txt_id8).val(txtauom);
     $('#'+txt_id9).val(txtauomid);
     $('#'+txt_id10).val(AultUomQty);
     $('#'+txt_id14).val(PoPendingQty);

    $('#'+txt_id2).parent().parent().find('[id*="Alpspartno"]').val(apartno);
    $('#'+txt_id2).parent().parent().find('[id*="Custpartno"]').val(cpartno);
    $('#'+txt_id2).parent().parent().find('[id*="OEMpartno"]').val(opartno);


     $('#hdn_ItemID').val('');
     $('#hdn_ItemID2').val('');
     $('#hdn_ItemID3').val('');
     $('#hdn_ItemID4').val('');
     $('#hdn_ItemID5').val('');
     $('#hdn_ItemID6').val('');
     $('#hdn_ItemID7').val('');
     $('#hdn_ItemID11').val('');
     
     $('#hdn_ItemID12').val('');
     $('#hdn_ItemID13').val('');

     $('#hdn_ItemID8').val('');
     $('#hdn_ItemID9').val('');
     $('#hdn_ItemID10').val('');
     $('#hdn_ItemID14').val('');

   }

                 
   $("#ITEMIDpopup").hide();
   event.preventDefault();
  }
  else if($(this).find('[id*="chkId"]').is(":checked") == true)
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

  $('.js-selectall').prop("checked", false);   
  $("#ITEMIDpopup").hide();

});



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

      var apartno =  $("#addinfo"+fieldid+"").data("desc101");
      var cpartno =  $("#addinfo"+fieldid+"").data("desc102");
      var opartno =  $("#addinfo"+fieldid+"").data("desc103");

      txtauomqty = (parseFloat(txtmuomqty)/parseFloat(txtmqtyf))*parseFloat(txtauomqty);

      var desc6         =  $("#txt"+fieldid+"").data("desc6");
      var AultUomQty    =  $("#txt"+fieldid+"").data("desc7");
      var PoPendingQty  =  $("#txt"+fieldid+"").data("desc8");
      
      if(intRegex.test(txtauomqty)){
          txtauomqty = (txtauomqty +'.000');
      }

      if(intRegex.test(txtmuomqty)){
        txtmuomqty = (txtmuomqty +'.000');
      }

      
      
     if($(this).is(":checked") == true) {

      $('#example2').find('.participantRow').each(function(){
        var RGPNO = $(this).find('[id*="RGP_NO"]').val();
        var itemid = $(this).find('[id*="ITEMID_REF"]').val();
         
         var exist_val=RGPNO+'-'+itemid;

         if(txtval){
              if(desc6 == exist_val){
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

                    $('#hdn_ItemID12').val('');
                    $('#hdn_ItemID13').val('');

                    $('#hdn_ItemID8').val('');
                    $('#hdn_ItemID9').val('');
                    $('#hdn_ItemID10').val('');
                    $('#hdn_ItemID14').val('');
                   
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


      if($('#hdn_ItemID').val() == "" && txtval != ''){

        var txtid= $('#hdn_ItemID').val();
        var txt_id2= $('#hdn_ItemID2').val();
        var txt_id3= $('#hdn_ItemID3').val();
        var txt_id4= $('#hdn_ItemID4').val();
        var txt_id5= $('#hdn_ItemID5').val();
        var txt_id6= $('#hdn_ItemID6').val();
        var txt_id7= $('#hdn_ItemID7').val();
        var txt_id11= $('#hdn_ItemID11').val();

        var txt_id8= $('#hdn_ItemID8').val();
        var txt_id9= $('#hdn_ItemID9').val();
        var txt_id10= $('#hdn_ItemID10').val();
        var txt_id14= $('#hdn_ItemID14').val();

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

        $clone.find('[id*="popupALTUOM"]').val(txtauom);
        $clone.find('[id*="ALT_UOMID_REF"]').val(txtauomid);
        $clone.find('[id*="RECEIVED_QTY_AU"]').val(AultUomQty);
        $clone.find('[id*="PO_PENDING_QTY"]').val(PoPendingQty);

        $clone.find('[id*="Alpspartno"]').val(apartno);
        $clone.find('[id*="Custpartno"]').val(cpartno);
        $clone.find('[id*="OEMpartno"]').val(opartno);
        
        $clone.find('[id*="TotalHiddenQty"]').val('');
        $clone.find('[id*="HiddenRowId"]').val('');

        $clone.find('[id*="REMARKS"]').val('');
        
        $tr.closest('table').append($clone);   
        var rowCount = $('#Row_Count1').val();
          rowCount = parseInt(rowCount)+1;
          $('#Row_Count1').val(rowCount);
          
          $("#ITEMIDpopup").hide();
        event.preventDefault();
      }
      else{

        $('#'+$('#hdn_ItemID12').val()).val('');
        $('#'+$('#hdn_ItemID13').val()).val('');

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
        var txt_id14= $('#hdn_ItemID14').val();

        $('#'+txtid).val(texdesc);
        $('#'+txt_id2).val(txtval);
        $('#'+txt_id3).val(txtname);
        $('#'+txt_id4).val(txtspec);
        $('#'+txt_id5).val(txtmuom);
        $('#'+txt_id6).val(txtmuomid);
        $('#'+txt_id7).val(txtmuomqty);
  
        $('#'+txt_id8).val(txtauom);
        $('#'+txt_id9).val(txtauomid);
        $('#'+txt_id10).val(AultUomQty);
        $('#'+txt_id14).val(PoPendingQty);

        $('#'+txt_id2).parent().parent().find('[id*="Alpspartno"]').val(apartno);
        $('#'+txt_id2).parent().parent().find('[id*="Custpartno"]').val(cpartno);
        $('#'+txt_id2).parent().parent().find('[id*="OEMpartno"]').val(opartno);

        $('#hdn_ItemID').val('');
        $('#hdn_ItemID2').val('');
        $('#hdn_ItemID3').val('');
        $('#hdn_ItemID4').val('');
        $('#hdn_ItemID5').val('');
        $('#hdn_ItemID6').val('');
        $('#hdn_ItemID7').val('');
        $('#hdn_ItemID11').val('');
        
        $('#hdn_ItemID12').val('');
        $('#hdn_ItemID13').val('');

        $('#hdn_ItemID8').val('');
        $('#hdn_ItemID9').val('');
        $('#hdn_ItemID10').val('');
        $('#hdn_ItemID14').val('');

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

/*=================================================================================*/
/*================================== UDF DETAILS ==================================*/
/*=================================================================================*/

$(document).ready(function(e) {
  var Material = $("#Material").html(); 
  $('#hdnmaterial').val(Material);

  var lastdt = <?php echo json_encode($objlastdt[0]->MISP_DT); ?>;
  var mis = <?php echo json_encode($objResponse); ?>;
  var today = new Date(); 
  var sodate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2);
  if(lastdt < mis.MISP_DT)
  {
	$('#MIS_DT').attr('min',lastdt);
  }
  else
  {
	  $('#MIS_DT').attr('min',mis.MISP_DT);
  }
  $('#MIS_DT').attr('max',sodate);




  var seudf = <?php echo json_encode($objUdfData); ?>;
  var count2 = <?php echo json_encode($objCountUDF); ?>;

  $('#example3').find('.participantRow3').each(function(){
    var txt_id4 = $(this).find('[id*="udfinputid"]').attr('id');
    var udfid = $(this).find('[id*="UDF"]').val();

    $.each( seudf, function( seukey, seuvalue ) {
      if(seuvalue.UDFMISID == udfid){

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
        if(souvalue.UDF == usovalue.UDFMISID)
        {
            $('#popupSEID_'+soukey).val(usovalue.LABEL);
        }
    
        if(souvalue.UDF == usovalue.UDFMISID){        
                var txtvaltype2 =   usovalue.VALUETYPE;
                var txt_id41 = $('#udfinputid_'+soukey).attr('id');
                var strdyn2 = txt_id41.split('_');
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
                    if(souvalue.SOUVALUE == "1")
                    {
                    strinp2 = '<input type="checkbox" name="'+dynamicid2+ '" id="'+dynamicid2+'" class="" checked> ';
                    }
                    else{
                    strinp2 = '<input type="checkbox" name="'+dynamicid2+ '" id="'+dynamicid2+'" class="" > ';
                    }
                }
                else if(chkvaltype2=='combobox'){

                var txtoptscombo2 =   usovalue.DESCRIPTIONS;
                var strarray2 = txtoptscombo2.split(',');
                var opts2 = '';

                for (var i = 0; i < strarray2.length; i++) {
                    opts2 = opts2 + '<option value="'+strarray2[i]+'">'+strarray2[i]+'</option> ';
                }

                strinp2 = '<select name="'+dynamicid2+ '" id="'+dynamicid2+'" class="form-control" required>'+opts2+'</select>' ;
                
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

  $('#MIS_DT').change(function() {
      var mindate  = $(this).val();
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

    $('#trn_frm_mis1').bootstrapValidator({
       
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
             $("#trn_frm_mis").submit();
        }
    });
});

$( "#btnSaveSE" ).click(function() {
    var formReqData = $("#trn_frm_mis");
    if(formReqData.valid()){
      validateForm('fnSaveData');
    }
});

$( "#btnApprove" ).click(function() {
    var formReqData = $("#trn_frm_mis");
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
      var trnFormReq = $("#trn_frm_mis");
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
      url:'{{ route("transactionmodify",[$FormId,"update"])}}',
      type:'POST',
      data:formData,
      success:function(data) {
        $(".buttonload").hide(); 
        $("#btnSaveSE").show();   
        $("#btnApprove").prop("disabled", false);

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
          else{                   
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

window.fnApproveData = function (){
event.preventDefault();
      var trnFormReq = $("#trn_frm_mis");
      var formData = trnFormReq.serialize();
  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });
  $("#btnApprove").hide(); 
  $(".buttonload_approve").show();  
  $("#btnSaveSE").prop("disabled", true);
  $.ajax({
      url:'{{ route("transactionmodify",[$FormId,"Approve"])}}',
      type:'POST',
      data:formData,
      success:function(data) {
        $("#btnApprove").show();  
        $(".buttonload_approve").hide();  
        $("#btnSaveSE").prop("disabled", false);
         
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
          else{                   
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
          $("#btnApprove").show();  
          $(".buttonload_approve").hide();  
          $("#btnSaveSE").prop("disabled", false);
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
 var MIS_NO         =   $.trim($("#MIS_NO").val());
 var MIS_DT         =   $.trim($("#MIS_DT").val());
 var DEPID_REF        =   $.trim($("#DEPID_REF").val());
 var checkCompany   =   "{{$checkCompany}}";

 if(MIS_NO ===""){
      $("#FocusId").val($("#MIS_NO"));
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('MIS No is required.');
      $("#alert").modal('show')
      $("#OkBtn1").focus();
      return false;
  }
  else if(MIS_DT ===""){
      $("#FocusId").val($("#MIS_DT"));
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please select MIS Date.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  }
  else if(DEPID_REF ===""){
      $("#FocusId").val($("#DEPID_REF_popup"));
      $("#DEPID_REF").val('');  
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please select Department.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else{
    event.preventDefault();
    var allblank = [];
    var allblank1 = [];
    var allblank1_1 = [];
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
    var allblank13 = [];
    var allblank14 = [];

    var allblank15  = [];
    var item_array  = new Array();
    var i           = 0;
        
    $('#example2').find('.participantRow').each(function(){

      if($.trim($(this).find("[id*=RGP_NO]").val())!=""){
        allblank1.push('true');
      }
      else{
        allblank1.push('false');
      }


      if($.trim($(this).find("[id*=ITEMID_REF]").val())!=""){
          allblank.push('true');

          if($.trim($(this).find("[id*=MAIN_UOMID_REF]").val())!=""){
            allblank2.push('true');

            if(checkCompany ==''){
              if($.trim($(this).find('[id*="RECEIVED_QTY_MU"]').val()) != "" && parseFloat($.trim($(this).find('[id*="RECEIVED_QTY_MU"]').val())) <= parseFloat($.trim($(this).find('[id*="SE_QTY"]').val())) ){
                allblank13.push('true');
              }
              else{
                allblank13.push('false');
              }
            }
            else{
              allblank13.push('true');
            }


            if($.trim($(this).find('[id*="RECEIVED_QTY_MU"]').val()) != "" && parseFloat($.trim($(this).find('[id*="RECEIVED_QTY_MU"]').val())) < parseFloat($.trim($(this).find('[id*="SE_QTY"]').val())) && $.trim($(this).find('[id*="REASON_SHORT_QTY"]').val()) == "" ){
              allblank14.push('false');
            }
            else{
              allblank14.push('true');
            }  


            if($.trim($(this).find('[id*="RECEIVED_QTY_MU"]').val()) != "" && $.trim($(this).find('[id*="RECEIVED_QTY_MU"]').val()) > 0.000 ){
              allblank3.push('true');
            }
            else{
              allblank3.push('false');
            }  

            if($.trim($(this).find('[id*="RECEIVED_QTY_MU"]').val()) != "" && $.trim($(this).find('[id*="RECEIVED_QTY_MU"]').val()) == $.trim($(this).find('[id*="TotalHiddenQty"]').val()) ){
              allblank7.push('true');
            }
            else{
              allblank7.push('false');
            }  

          }
          else{
              allblank2.push('false');
          }

          item_array[i]={
            'MISRDT':MIS_DT,
            'ITEMID_REF':$(this).find('[id*="ITEMID_REF"]').val(),
            'MAIN_UOMID_REF':$(this).find('[id*="MAIN_UOMID_REF"]').val(),
            'RECEIVED_QTY_MU':$(this).find('[id*="RECEIVED_QTY_MU"]').val(),
            'MAIN_ITEM_CODE':$(this).find('[id*="popupITEMID"]').val(),
            'ITEMID_CODE':$(this).find('[id*="popupITEMID"]').val(),
            'ITEMID_NAME':$(this).find('[id*="ItemName"]').val(),
            };

          i++;

      }
      else{
        allblank.push('false');
      }
    });

    $('#example3').find('.participantRow3').each(function(){
      if($.trim($(this).find("[id*=UDF]").val())!=""){
        allblank4.push('true');
        if($.trim($(this).find("[id*=UDFismandatory]").val())=="1"){
          if($.trim($(this).find('[id*="udfvalue"]').val()) != ""){
            allblank5.push('true');
          }
          else{
            allblank5.push('false');
          }
        }  
      }                
    });

    if(jQuery.inArray("false", allblank1) !== -1){
      $("#alert").modal('show');
      $("#AlertMessage").text('Please Select MRS No In Material');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    // else if(jQuery.inArray("false", allblank1_1) !== -1){
    //   $("#alert").modal('show');
    //   $("#AlertMessage").text('Please Select PO No In Material');
    //   $("#YesBtn").hide(); 
    //   $("#NoBtn").hide();  
    //   $("#OkBtn1").show();
    //   $("#OkBtn1").focus();
    //   highlighFocusBtn('activeOk');
    // }
    else if(jQuery.inArray("false", allblank) !== -1){
      $("#alert").modal('show');
      $("#AlertMessage").text('Please Select Item In Material');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank2) !== -1){
      $("#alert").modal('show');
      $("#AlertMessage").text('Main UOM is missing in in material tab.');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank3) !== -1){
      $("#alert").modal('show');
      $("#AlertMessage").text('Issued Qty (MU) cannot be zero or blank in material tab.');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank7) !== -1){
      $("#alert").modal('show');
      $("#AlertMessage").text('Item quantity cannot be equal of selected store quantity in material tab.');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank13) !== -1){
      $("#alert").modal('show');
      $("#AlertMessage").text('Issue quantity should not greater then MRS Qty in material tab.');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank14) !== -1){
      $("#alert").modal('show');
      $("#AlertMessage").text('Please Enter Reason of Short Qty Issued in material tab.');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    /*
    else if(jQuery.inArray("false", allblank5) !== -1){
      $("#alert").modal('show');
      $("#AlertMessage").text('Please enter  Value / Comment in UDF Tab.');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    */
    else{

      var data      = JSON.parse(getTotalDateWiseStock(item_array));
      var alert_msg = data.message;
      allblank15.push(data.result);

      if(jQuery.inArray("false", allblank15) !== -1){
        $("#alert").modal('show');
        $("#AlertMessage").text(alert_msg);
        $("#YesBtn").hide(); 
        $("#NoBtn").hide();  
        $("#OkBtn1").show();
        $("#OkBtn1").focus();
        highlighFocusBtn('activeOk');
      }
      else if(checkPeriodClosing('{{$FormId}}',$("#MIS_DT").val(),0) ==0){
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
          $("#YesBtn").data("funcname",actionType);
          $("#YesBtn").focus();
          $("#OkBtn").hide();
          highlighFocusBtn('activeYes');
      }
      
    }
  }
}

/*=================================================================================*/
/*================================== STORE DETAILS ================================*/
/*=================================================================================*/

$("#example2").on('click', '[class*="checkstore"]', function() {
  var ROW_ID      =   $(this).attr('id');
  var ITEMID_REF  = $("#ITEMID_REF_"+ROW_ID).val();
  
  if(ITEMID_REF ===""){
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please select item code.');
      $("#alert").modal('show')
      $("#OkBtn1").focus();
      return false;
  }
  else{
      getStoreDetails(ROW_ID);
      $("#StoreModal").show();
      event.preventDefault();
  }

});

function getStoreDetails(ROW_ID){

var RGP_NO          = $("#RGP_NO_"+ROW_ID).val();
var ITEMID_REF      = $("#ITEMID_REF_"+ROW_ID).val();
var ITEMROWID       = $("#HiddenRowId_"+ROW_ID).val();
var MAIN_UOMID_DES  = $("#popupMUOM_"+ROW_ID).val();
var MAIN_UOMID_REF  = $("#MAIN_UOMID_REF_"+ROW_ID).val();
var ALT_UOMID_DES   = $("#popupALTUOM_"+ROW_ID).val();
var ALT_UOMID_REF   = $("#ALT_UOMID_REF_"+ROW_ID).val();

$("#StoreTable").html('');
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$.ajax({
    url:'{{route("transaction",[$FormId,"getStoreDetails"])}}',
    type:'POST',
    data:{
      ROW_ID:ROW_ID,
      RGP_NO:RGP_NO,
      ITEMID_REF:ITEMID_REF,
      MAIN_UOMID_DES:MAIN_UOMID_DES,
      MAIN_UOMID_REF:MAIN_UOMID_REF,
      ALT_UOMID_DES:ALT_UOMID_DES,
      ALT_UOMID_REF:ALT_UOMID_REF,
      ITEMROWID:ITEMROWID,
      ACTION_TYPE:'EDIT'
      },
    success:function(data) {
      $("#StoreTable").html(data);                
    },
    error:function(data){
      console.log("Error: Something went wrong.");
      $("#StoreTable").html('');                        
    },
}); 
}

$("#StoreModalClose").click(function(event){

var Total_Stock_Inhand=[];
var NewIdArr  = [];
var ROW_ID    =[];
var Req       =[];

$('#StoreTable').find('.participantRow33').each(function(){

    Total_Stock_Inhand.push(parseFloat($.trim($(this).find("[id*=TOTAL_STOCK]").val())));

    if($.trim($(this).find("[id*=UserQty]").val())!=""){  
      var UserQty      = parseFloat($.trim($(this).find("[id*=UserQty]").val()));
      var BatchId      = $.trim($(this).find("[id*=BATCHID]").val());
      var ROWID        = $.trim($(this).find("[id*=ROWID]").val());
      var TOTAL_STOCK  = parseFloat($.trim($(this).find("[id*=TOTAL_STOCK]").val()));
      var BATCHNOA     = $.trim($(this).find("[id*=BATCHNOA]").val());
      
      ROW_ID.push(ROWID);
      NewIdArr.push(BatchId+"_"+UserQty+"_"+TOTAL_STOCK);

      if(UserQty > 0 && BATCHNOA =="1"){
        Req.push('false');
      }
      else{
        Req.push('true');
      }

    } 


});                       

var ROW_ID  = ROW_ID[0];
var Total_Stock_Inhand_Sum  = getArraySum(Total_Stock_Inhand);

$("#HiddenRowId_"+ROW_ID).val(NewIdArr);
$("#STOCK_INHAND_"+ROW_ID).val(parseFloat(Total_Stock_Inhand_Sum).toFixed(3));
$("#StoreModal").hide();

});

function checkStoreQty(ROW_ID,itemid,altumid,userQty,key,stock){

if(parseFloat(userQty) > parseFloat(stock) ){
  $("#UserQty_"+key).val('');  
  $("#AltUserQty_"+key).val('');  
  $("#alert").modal('show');
  $("#AlertMessage").text('Issue quantity should not greater then Stock inhand.');
  $("#YesBtn").hide(); 
  $("#NoBtn").hide();  
  $("#OkBtn1").show();
  $("#OkBtn1").focus();
  highlighFocusBtn('activeOk');
}
else{

  $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $.ajax({
      url:'{{route("transaction",[$FormId,"changeAltUm"])}}',
      type:'POST',
      data:{altumid:altumid,itemid:itemid,mqty:userQty},
      success:function(data) {
        $("#AltUserQty_"+key).val(data);              
      },
      error:function(data){
        console.log("Error: Something went wrong.");            
      },
  }); 

  var NewQtyArr = [];
  var NewIdArr  = [];

  $('#StoreTable').find('.participantRow33').each(function(){

      if($.trim($(this).find("[id*=UserQty]").val())!=""){  
        var UserQty      = parseFloat($.trim($(this).find("[id*=UserQty]").val()));
        var BatchId      = $.trim($(this).find("[id*=BATCHID]").val());

        NewQtyArr.push(UserQty);
        NewIdArr.push(BatchId+"_"+UserQty);
      }                
  });

  var TotalQty= getArraySum(NewQtyArr); 
  var BillQty = parseFloat($.trim($("#SE_QTY_"+ROW_ID).val()));
  var ShortQty = (BillQty-TotalQty);

  $("#TotalHiddenQty_"+ROW_ID).val(TotalQty);
  $("#HiddenRowId_"+ROW_ID).val(NewIdArr);
  $("#RECEIVED_QTY_MU_"+ROW_ID).val(TotalQty);  
  $("#SHORT_QTY_"+ROW_ID).val(ShortQty);

}
  
}

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

/*=================================================================================*/
/*================================== MRS NO DETAILS ===============================*/
/*=================================================================================*/

let sqtid = "#RGPTable2";
let sqtid2 = "#RGPTable";
let salesquotationheaders = document.querySelectorAll(sqtid2 + " th");

salesquotationheaders.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(sqtid, ".clssqid", "td:nth-child(" + (i + 1) + ")");
  });
});

function RGPCodeFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("RGPcodesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("RGPTable2");
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

function RGPNameFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("RGPnamesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("RGPTable2");
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

$('#Material').on('click','[id*="txtRGP_popup"]',function(event){

  var id = $(this).attr('id');
  var id2 = $(this).parent().parent().find('[id*="RGP_NO"]').attr('id');
  var id3 = $(this).parent().parent().find('[id*="PRO_NO"]').attr('id');

  $('#hdn_sqid').val(id);
  $('#hdn_sqid2').val(id2);
  $('#hdn_sqid3').val(id3);

  var DEPID_REF    = $.trim($('#DEPID_REF').val());
  var fieldid = $(this).parent().parent().find('[id*="RGP_NO"]').attr('id');

  if(DEPID_REF ===""){
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select department.');
    $("#alert").modal('show')
    $("#OkBtn1").focus();
  }
  else{
    $("#RGPpopup").show();
    $("#tbody_RGP").html('');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
    $.ajax({
        url:'{{route("transaction",[$FormId,"getCodeNo"])}}',
        type:'POST',
        data:{'id':DEPID_REF,'fieldid':fieldid},
        success:function(data) {
          $("#tbody_RGP").html(data);
          BindRGP();
          showSelectedCheck($("#"+fieldid).val(),"SELECT_"+fieldid);
        },
        error:function(data){
          console.log("Error: Something went wrong.");
          $("#tbody_RGP").html('');
        },
    });

    $(this).parent().parent().find('[id*="txtPO_popup"]').val('');
    $(this).parent().parent().find('[id*="PRO_NO"]').val('');
    $(this).parent().parent().find('[id*="POID_REF"]').val('');
    $(this).parent().parent().find('[id*="popupITEMID"]').val('');
    $(this).parent().parent().find('[id*="ITEMID_REF"]').val('');
    $(this).parent().parent().find('[id*="ItemName"]').val('');
    $(this).parent().parent().find('[id*="popupMUOM"]').val('');
    $(this).parent().parent().find('[id*="MAIN_UOMID_REF"]').val('');
    $(this).parent().parent().find('[id*="TotalHiddenQty"]').val('');
    $(this).parent().parent().find('[id*="HiddenRowId"]').val('');
    $(this).parent().parent().find('[id*="SE_QTY"]').val('');
    $(this).parent().parent().find('[id*="SO_FQTY"]').val('');
    $(this).parent().parent().find('[id*="Itemspec"]').val('');
    $(this).parent().parent().find('[id*="REMARKS_"]').val('');

  }

});

$("#RGP_closePopup").click(function(event){
  $("#RGPpopup").hide();
});

function BindRGP(){
  $(".clssqid").click(function(){
    var fieldid = $(this).attr('id');
    var txtval =    $("#txt"+fieldid+"").val();
    var texdesc =   $("#txt"+fieldid+"").data("desc");
    var texdesc1 =   $("#txt"+fieldid+"").data("desc1");
    
    var txtid= $('#hdn_sqid').val();
    var txt_id2= $('#hdn_sqid2').val();
    var txt_id3= $('#hdn_sqid3').val();

    $('#'+txtid).val(texdesc);
    $('#'+txt_id2).val(txtval);
    $('#'+txt_id3).val(texdesc1);
    $("#RGPpopup").hide();
    
    $("#RGPcodesearch").val(''); 
    $("#RGPnamesearch").val(''); 
   
    event.preventDefault();
  });
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

function getTotalDateWiseStock(DOC_DT,ITEMID_REF,UOMID_REF){

  $.ajaxSetup({
    headers:{
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  })

  var posts = $.ajax({
                url:'{{route("transaction",[$FormId,"getTotalDateWiseStock"])}}',
                type:'POST',
                async: false,
                dataType: 'json',
                data: {DOC_DT:DOC_DT,ITEMID_REF:ITEMID_REF,UOMID_REF:UOMID_REF},
                done: function(response) {return response;}
              }).responseText;

  return posts;
}
</script>
@endpush