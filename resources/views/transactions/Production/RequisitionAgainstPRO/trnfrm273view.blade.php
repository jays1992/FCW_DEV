@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
	<div class="row">
		<div class="col-lg-2"><a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Requisition against PRO</a></div>

		<div class="col-lg-10 topnav-pd">
			<button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
			<button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
			<button class="btn topnavbt" id="btnSave" disabled="disabled"><i class="fa fa-save"></i> Save</button>
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


<form id="production_order_edit" onsubmit="return validateForm()"  method="POST" class="needs-validation"  >  
	<div class="container-fluid filter">
		<div class="inner-form">

			<div class="row">
				<div class="col-lg-2 pl"><p>RPR No</p></div>
				<div class="col-lg-2 pl">
          <input type="hidden" name="RPRID" id="RPRID" value="{{ isset($objResponse)?$objResponse->RPRID:'' }}" class="form-control" readonly  autocomplete="off"   >
					<input {{$ActionStatus}} type="text" name="RPRO_NO" id="RPRO_NO" value="{{ isset($objResponse)?$objResponse->RPR_NO:'' }}" class="form-control mandatory" readonly  autocomplete="off"  style="text-transform:uppercase" autofocus >
					<span class="text-danger" id="ERROR_RPRO_NO"></span>
				</div>
					  
				<div class="col-lg-2 pl"><p>RPR Date</p></div>
				<div class="col-lg-2 pl">
					<input {{$ActionStatus}} type="date" name="RPR_DT" id="RPR_DT" value="{{ isset($objResponse)?$objResponse->RPR_DT:'' }}" class="form-control mandatory"  placeholder="dd/mm/yyyy" maxlength="10" >
					<span class="text-danger" id="ERROR_RPR_DT"></span>
				</div>
			
			</div>  
      <div class="row">
        <div class="col-lg-2 pl"><p>PRO No</p></div>
        <div class="col-lg-2 pl">
          <input {{$ActionStatus}} type="text" name="pro_popup" id="txtpro_popup" class="form-control mandatory"  value="{{ $objPRO2->PRO_NO }}" autocomplete="off" disabled readonly/>
          <input type="hidden" name="PROID_REF" value="{{ $objPRO2->PROID }}" id="PROID_REF" class="form-control" autocomplete="off" />           
        </div>
        <div class="col-lg-2 pl"><p>PRO Title</p></div>
        <div class="col-lg-4 pl">
          <input {{$ActionStatus}} type="text" name="TITLE" id="TITLE" class="form-control" value="{{ $objPRO2->PRO_TITLE }}"  autocomplete="off" readonly>
          <span class="text-danger" id="ERROR_TITLE"></span>
        </div>
      </div>       
      <div class="row">
        <div class="col-lg-2 pl"><p>Production Stage</p></div>
        <div class="col-lg-2 pl">
          <input {{$ActionStatus}} type="text" name="stage_popup" id="txtpstage_popup" value="{{ $objPStage2->PSTAGE_CODE }}-{{ $objPStage2->DESCRIPTIONS }}" class="form-control mandatory"  autocomplete="off" readonly/>
          <input type="hidden" name="PSTAGEID_REF" id="PSTAGEID_REF" value="{{ $objPStage2->PSTAGEID }}" class="form-control"  />           
        </div>       
        <div class="col-lg-2 pl"><p>Store</p></div>
        <div class="col-lg-2 pl">
          <input {{$ActionStatus}} type="text" name="store_popup" id="txtstore_popup" value="{{ $objStore2->STCODE }}-{{ $objStore2->NAME }}"  class="form-control mandatory"  autocomplete="off" readonly/>
          <input type="hidden" name="STID_REF" id="STID_REF" value="{{ $objStore2->STID }}"  class="form-control"  />           
        </div>       
        <div class="col-lg-2 pl"><p>Requested By</p></div>
        <div class="col-lg-2 pl">
          <input {{$ActionStatus}} type="text" name="employee_popup" id="txtemployee_popup" value="{{ $objEmployee2->EMPCODE }}-{{ $objEmployee2->FNAME }} {{ $objEmployee2->LNAME }}" class="form-control mandatory"  autocomplete="off" readonly/>
          <input type="hidden" name="EMPID_REF" id="EMPID_REF"  value="{{ $objEmployee2->EMPID }}" class="form-control"  />           
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
						<div class="table-responsive table-wrapper-scroll-y" style="margin-top:10px;" >
							<table id="example2" class="display nowrap table table-striped table-bordered itemlist w-200" width="100%" style="height:auto !important;">
								<thead id="thead1"  style="position: sticky;top: 0">
									<tr>
										<th  hidden><input class="form-control" type="text" name="Row_Count1" id ="Row_Count1" value="{{count($objMAT)}}" style="width: 50px;"></th>
                    <th>Finished Good Item</th>
                    <th  hidden>FGI_REF</th>
                    <th>FGI Name</th>
                    <th>FGI UOM</th>
                    <th hidden>MAINITEM_UOMID_REF</th>

									  <th  hidden>SOID_REF</th>
                    <th  hidden>SQID_REF</th>
                    <th  hidden>SEID_REF</th>
										<th>Item Code </th>
                    <th hidden>ITEMID_REF </th>
                    <th>Item Name</th>

                    <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</th>
                    <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</th>
                    <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</th>

                    <th>Item UOM</th>
                    <th hidden>ITEM_UOMID_REF</th>
                    <th hidden>Item Specifications</th>
                   
                    <th hidden>Alt UOM (AU)</th>
                    <th>Balance Production Order Qty</th>
                    <th>Requisition Qty</th>
                    <th>Stock-in-hand</th>
                    <th>Remarks</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody id="tbodyid">
              
								@if(isset($objMAT) && !empty($objMAT))
								@foreach($objMAT as $key => $row)
                  @php
                    $mainitem_val = '';
                    
                    $SOID =  is_null($row->SOID_REF) || trim($row->SOID_REF)=="" ? '': trim($row->SOID_REF);
                    $SQID =  is_null($row->SQID_REF) || trim($row->SQID_REF)=="" ? '': trim($row->SQID_REF);
                    $SEID =  is_null($row->SEID_REF) || trim($row->SEID_REF)=="" ? '': trim($row->SEID_REF);
                    $ITEMID =  is_null($row->ITEMID_REF) || trim($row->ITEMID_REF)=="" ? '': trim($row->ITEMID_REF);                    
                    $mitem_id = $SOID."_".$SQID."_".$SEID."_".$ITEMID; 
                  @endphp
							
									<tr  class="participantRow">
										<td  hidden><input type="hidden" id="{{$key}}" > </td>

                    <td><input {{$ActionStatus}}  type="text"   name="popupFGI_{{$key}}" id="popupFGI_{{$key}}" value="{{$row->MAINITEM_CODE}}" class="form-control"  autocomplete="off"  readonly style="width:100px;" /></td>
                    <td   hidden><input type="text" name="FGI_REF_{{$key}}" id="FGI_REF_{{$key}}" value="{{$row->MAINITEMID_REF}}"  class="form-control" autocomplete="off" /></td>
                    <td><input {{$ActionStatus}} type="text"    name="FGIName_{{$key}}" id="FGIName_{{$key}}" value="{{$row->MAINITEM_NAME}}"  class="form-control"  autocomplete="off"  readonly style="width:200px;" /></td>
                    <td><input {{$ActionStatus}} type="text" name="popupMAINITEMUOM_{{$key}}" id="popupMAINITEMUOM_{{$key}}" value="{{$row->MAINITEM_UOMCODE}}-{{$row->MAINITEM_UOMDESC}}" class="form-control"  autocomplete="off"  readonly style="width:100px;"/></td>
                    <td  hidden><input type="text" name="MAINITEM_UOMID_REF_{{$key}}" id="MAINITEM_UOMID_REF_{{$key}}" value="{{$row->MAINITEMUOMID_REF}}"  readonly class="form-control"  autocomplete="off" /></td>

										
										<td    hidden><input type="text" name="SOID_REF_{{$key}}" id="SOID_REF_{{$key}}" value="{{$row->SOID_REF}}" class="form-control" autocomplete="off" /></td>
										<td    hidden><input type="text" name="SQID_REF_{{$key}}" id="SQID_REF_{{$key}}" value="{{$row->SQID_REF}}" class="form-control" autocomplete="off" /></td>
										<td    hidden><input type="text" name="SEID_REF_{{$key}}" id="SEID_REF_{{$key}}" value="{{$row->SEID_REF}}" class="form-control" autocomplete="off" /></td>
									  
										<td><input {{$ActionStatus}} type="text" name="popupITEMID_{{$key}}" id="popupITEMID_{{$key}}" value="{{$row->ICODE}}" class="form-control"  autocomplete="off"  readonly style="width:100px;" /></td>
										<td   hidden><input type="text" name="ITEMID_REF_{{$key}}" id="ITEMID_REF_{{$key}}" value="{{$row->ITEMID_REF}}" class="form-control" autocomplete="off" /></td>
									  
										<td><input {{$ActionStatus}} type="text" name="ItemName_{{$key}}" id="ItemName_{{$key}}" value="{{$row->ITEM_NAME}}" class="form-control"  autocomplete="off"  readonly style="width:200px;" /></td>
										
                    <td {{$AlpsStatus['hidden']}}><input  type="text" name="Alpspartno_{{$key}}" id="Alpspartno_{{$key}}" class="form-control"  autocomplete="off" value="{{ isset($row->ALPS_PART_NO)?$row->ALPS_PART_NO:'' }}" readonly/></td>
                    <td {{$AlpsStatus['hidden']}}><input  type="text" name="Custpartno_{{$key}}" id="Custpartno_{{$key}}" class="form-control"  autocomplete="off" value="{{ isset($row->CUSTOMER_PART_NO)?$row->CUSTOMER_PART_NO:'' }}" readonly/></td>
                    <td {{$AlpsStatus['hidden']}}><input  type="text" name="OEMpartno_{{$key}}"  id="OEMpartno_{{$key}}" class="form-control"  autocomplete="off" value="{{ isset($row->OEM_PART_NO)?$row->OEM_PART_NO:'' }}" readonly/></td>

                    
                    <td><input {{$ActionStatus}}	type="text"	name="popupMUOM_{{$key}}"	id="popupMUOM_{{$key}}"	value="{{$row->UOMCODE}}-{{$row->DESCRIPTIONS}}"	class="form-control"  autocomplete="off"  readonly style="width:100px;"/></td>
										<td  hidden><input	type="text"	name="MAIN_UOMID_REF_{{$key}}"	id="MAIN_UOMID_REF_{{$key}}"	value="{{$row->UOMID_REF}}"	class="form-control"  autocomplete="off" /></td>
								  
										<td><input {{$ActionStatus}} type="text"   name="QTY_{{$key}}" 		id="QTY_{{$key}}" 		value="{{$row->BAL_CHANGES_PD_OR_QTY}}" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" readonly  style="width:100px;"  /></td>
										
                    <td><input {{$ActionStatus}} type="text"   name="REQ_QTY_{{$key}}" id="REQ_QTY_{{$key}}"  value="{{number_format($row->REQ_QTY,3,".","")  }}" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" style="width:100px;" onKeyup="change_production_order(this.id,this.value)"  /></td>
                    <td><input type="text" name="STOCK_INHAND_{{$key}}" id="STOCK_INHAND_{{$key}}"  value="{{isset($row->TOTAL_STOCK) ? $row->TOTAL_STOCK:'0.00' }}" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" readonly  autocomplete="off"   /></td>
                    <td><input {{$ActionStatus}} type="text"   name="REMARKS_{{$key}}" id="REMARKS_{{$key}}" value="{{$row->REMARKS}}" class="form-control" maxlength="200"  autocomplete="off" style="width:200px;" /></td>

										<td align="center" >
										  <button {{$ActionStatus}} class="btn add material" title="add" data-toggle="tooltip" type="button" ><i class="fa fa-plus"></i></button>
										  <button {{$ActionStatus}} class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button>
										</td>

									</tr>
								  
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
								<td><input {{$ActionStatus}} type="text" name={{"popupSEID_".$uindex}} id={{"popupSEID_".$uindex}} class="form-control" value="{{$uRow->UDFRPRID_REF}}" autocomplete="off"  readonly/></td>
								<td hidden><input type="hidden" name={{"UDF_".$uindex}} id={{"UDF_".$uindex}} class="form-control" value="{{$uRow->UDFRPRID_REF}}" autocomplete="off"   /></td>
								<td hidden><input type="hidden" name={{"UDFismandatory_".$uindex}} id={{"UDFismandatory_".$uindex}} value="{{$uRow->UDFRPRID_REF}}" class="form-control"   autocomplete="off" /></td>
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
<!-- Production Stages Dropdown -->
<div id="pstage_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='pstage_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
      <div class="tablename"><p>Production Stages</p></div>
      <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="ProdStageTable" class="display nowrap table  table-striped table-bordered" >
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
      <td class="ROW2"><input type="text" id="pstage_code_search" class="form-control" autocomplete="off" onkeyup="PStageCodeFunction()"></td>
      <td class="ROW3"><input type="text" id="pstage_desc_search" class="form-control" autocomplete="off" onkeyup="PStageDescFunction()"></td>
    </tr>

    </tbody>
    </table>
      <table id="ProdStageTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2">
          
        </thead>
        <tbody>
        @foreach ($objPStage as $index=>$pstage_Row)
        <tr >

          <td class="ROW1"> <input type="checkbox" name="SELECT_PSTAGEID_REF[]" id="pstage_{{ $index }}" class="clspstage" value="{{ $pstage_Row->PSTAGEID }}" ></td>

          <td class="ROW2">{{ $pstage_Row-> PSTAGE_CODE }}
          <input type="hidden" id="txtpstage_{{ $index }}" data-desc="{{ $pstage_Row->PSTAGE_CODE }}-{{ $pstage_Row->DESCRIPTIONS }}"  value="{{ $pstage_Row->PSTAGEID }}"/>
          </td>
          <td class="ROW3">{{ $pstage_Row->DESCRIPTIONS }}</td>
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
<!-- Store Dropdown -->
<div id="store_popup" class="modal" role="dialog"  data-backdrop="static">
      <div class="modal-dialog modal-md column3_modal" >
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" id='store_closePopup' >&times;</button>
          </div>
        <div class="modal-body">
          <div class="tablename"><p>Stores</p></div>
          <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
        <table id="storeTable" class="display nowrap table  table-striped table-bordered">
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
          <td class="ROW2"><input type="text" id="store_code_search" class="form-control" autocomplete="off" onkeyup="storeCodeFunction()"></td>
          <td class="ROW3"><input type="text" id="store_name_search" class="form-control" autocomplete="off" onkeyup="storeNameFunction()"></td>
        </tr>

        </tbody>
        </table>
          <table id="storeTable2" class="display nowrap table  table-striped table-bordered">
            <thead id="thead2">
              
            </thead>
            <tbody>
            @foreach ($objStore as $index=>$store_Row)
            <tr >
            <td class="ROW1"> <input type="checkbox" name="SELECT_STID_REF[]" id="store_{{ $index }}" class="clsstore" value="{{ $store_Row->STID }}" ></td>
              <td class="ROW2">{{ $store_Row->STCODE }}
              <input type="hidden" id="txtstore_{{ $index }}" data-desc="{{ $store_Row->STCODE }}-{{ $store_Row->NAME }}"  value="{{ $store_Row->STID }}"/>
              </td>
              <td class="ROW3">{{ $store_Row->NAME }}</td>
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
<!-- employee Dropdown -->
<div id="employee_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='employee_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
      <div class="tablename"><p>Requested By</p></div>
      <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="employeeTable" class="display nowrap table  table-striped table-bordered" >
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
        <td class="ROW2"><input type="text" id="employee_code_search" class="form-control" autocomplete="off" onkeyup="employeeCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="employee_name_search" class="form-control" autocomplete="off" onkeyup="employeeNameFunction()"></td>
      </tr>

    </tbody>
    </table>
      <table id="employeeTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2">
          
        </thead>
        <tbody>
        @foreach ($objEmployee as $index=>$employee_Row)
        <tr >
          <td class="ROW1"> <input type="checkbox" name="SELECT_EMPID_REF[]" id="employee_{{ $index }}" class="clsemployee" value="{{ $employee_Row->EMPID }}" ></td>
          <td class="ROW2">{{ $employee_Row->EMPCODE }}
          <input type="hidden" id="txtemployee_{{ $index }}" data-desc="{{ $employee_Row->EMPCODE }}-{{ $employee_Row->FNAME }} {{ $employee_Row->LNAME }}"  value="{{ $employee_Row->EMPID }}"/>
          </td>
          <td class="ROW3" >{{ $employee_Row->FNAME }} {{ $employee_Row->LNAME }}</td>
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
<!-- FGI Dropdown-->
<div id="FGIIDpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:90%">
    <div class="modal-content" >
      <div class="modal-header"><button type="button" class="close" data-dismiss="modal" id='FGIID_closePopup' >&times;</button></div>
      <div class="modal-body">
	      <div class="tablename"><p>Finished Good Items Details</p></div>
	      <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
          <table id="FGITable" class="display nowrap table  table-striped table-bordered" style="width:100%" >
            <thead>
              <tr id="none-select" class="searchalldata" hidden >
                <td> 
                  <input type="text" name="fildfgi_id1" id="hdn_FGIID1"/>
                  <input type="text" name="fildfgi_id2" id="hdn_FGIID2"/>
                  <input type="text" name="fildfgi_id3" id="hdn_FGIID3"/>
                  <input type="text" name="fildfgi_id4" id="hdn_FGIID4"/>
                  <input type="text" name="fildfgi_id5" id="hdn_FGIID5"/>
                  <input type="text" name="fildfgi_id6" id="hdn_FGIID6"/>
                  <input type="text" name="fildfgi_id7" id="hdn_FGIID7"/>
                  <input type="text" name="fildfgi_id8" id="hdn_FGIID8"/>
                  <input type="text" name="fildfgi_id9" id="hdn_FGIID9"/>
                  <input type="text" name="fildfgi_id10" id="hdn_FGIID10"/>
                  <input type="text" name="fildfgi_id11" id="hdn_FGIID11"/>
                  <input type="text" name="fildfgi_id18" id="hdn_FGIID18"/>
                  <input type="text" name="fildfgi_id19" id="hdn_FGIID19"/>
                  <input type="text" name="fildfgi_id20" id="hdn_FGIID20"/>
                  <input type="text" name="hdn_FGIID21" id="hdn_FGIID21" value="0"/>
                  <input type="text" name="fildfgi_id22" id="hdn_FGIID22"/>
                  <input type="text" name="fildfgi_id23" id="hdn_FGIID23"/>
                  <input type="text" name="fildfgi_id24" id="hdn_FGIID24"/>
                  <input type="text" name="fildfgi_id25" id="hdn_FGIID25"/>
                </td>
              </tr>

              <tr>
                <th style="width:8%;" id="all-check">Select</th>
                <th style="width:10%;">Item Code</th>
                <th style="width:10%;">Name</th>
                <th style="width:8%;">Main UOM</th>
                <th style="width:8%;display:none;">Qty</th>
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
                <td style="width:8%;text-align:center;"><input type="checkbox" class="fgijs-selectall" data-target=".fgijs-selectall1" /></td>
                <td style="width:10%;"><input type="text" id="fgicodesearch" class="form-control" onkeyup="FGICodeFunction(event)"></td>
                <td style="width:10%;"><input type="text" id="fginamesearch" class="form-control" onkeyup="FGINameFunction(event)"></td>
                <td style="width:8%;"><input type="text" id="fgiUOMsearch" class="form-control" onkeyup="FGIUOMFunction(event)"></td>
                <td style="width:8%;display:none;"><input type="text" id="fgiQTYsearch" class="form-control" onkeyup="FGIQTYFunction(event)" readonly></td>
                <td style="width:8%;"><input type="text" id="fgiGroupsearch" class="form-control" onkeyup="FGIGroupFunction(event)"></td>
                <td style="width:8%;"><input type="text" id="fgiCategorysearch" class="form-control" onkeyup="FGICategoryFunction(event)"></td>
                <td style="width:8%;"><input type="text" id="fgiItemBUsearch" class="form-control" onkeyup="FGIItemBUFunction(event)" readonly></td>
                <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="fgiItemAPNsearch" class="form-control" onkeyup="FGIItemAPNFunction(event)"></td>
                <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="fgiItemCPNsearch" class="form-control" onkeyup="FGIItemCPNFunction(event)"></td>
                <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="fgiItemOEMPNsearch" class="form-control" onkeyup="FGIItemOEMPNFunction(event)"></td>
                <td style="width:8%;"><input  type="text" id="fgiStatussearch" class="form-control" onkeyup="FGIStatusFunction(event)" readonly></td>
              </tr>
            </tbody>
          </table>

          <table id="FGITable2" class="display nowrap table  table-striped table-bordered" style="width:100%" >
            <thead id="thead2"></thead>
            <tbody id="tbody_FGI"></tbody>
          </table>
        </div>
		    <div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<!-- item Dropdown-->
<div id="ITEMIDpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:90%">
    <div class="modal-content" >
      <div class="modal-header"><button type="button" class="close" data-dismiss="modal" id='ITEMID_closePopup' >&times;</button></div>
      <div class="modal-body">
	      <div class="tablename"><p>Item Details</p></div>
	      <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
          <table id="ItemIDTable" class="display nowrap table  table-striped table-bordered" style="width:100%" >
            <thead>
              <tr id="none-select" class="searchalldata" hidden >
                <td> 
                  <input type="text" name="fieldid1" id="hdn_ItemID1"/>
                  <input type="text" name="fieldid2" id="hdn_ItemID2"/>
                  <input type="text" name="fieldid3" id="hdn_ItemID3"/>
                  <input type="text" name="fieldid4" id="hdn_ItemID4"/>
                  <input type="text" name="fieldid5" id="hdn_ItemID5"/>
                  <input type="text" name="fieldid6" id="hdn_ItemID6"/>
                  <input type="text" name="fieldid7" id="hdn_ItemID7"/>
                  <input type="text" name="fieldid8" id="hdn_ItemID8"/>
                  <input type="text" name="fieldid9" id="hdn_ItemID9"/>
                  <input type="text" name="fieldid10" id="hdn_ItemID10"/>
                  <input type="text" name="fieldid11" id="hdn_ItemID11"/>
                  <input type="text" name="fieldid18" id="hdn_ItemID18"/>
                  <input type="text" name="fieldid19" id="hdn_ItemID19"/>
                  <input type="text" name="fieldid20" id="hdn_ItemID20"/>
                  <input type="text" name="hdn_ItemID21" id="hdn_ItemID21" value="0"/>
                  <input type="text" name="fieldid22" id="hdn_ItemID22"/>
                  <input type="text" name="fieldid23" id="hdn_ItemID23"/>
                  <input type="text" name="fieldid24" id="hdn_ItemID24"/>
                  <input type="text" name="fieldid25" id="hdn_ItemID25"/>
                  <input type="text" name="fieldid26" id="hdn_ItemID26"/>
                </td>
              </tr>

              <tr>
                <th style="width:8%;" id="all-check">Select</th>
                <th style="width:10%;">Item Code</th>
                <th style="width:10%;">Name</th>
                <th style="width:8%;">Main UOM</th>
                <th style="width:8%;">Balance Qty</th>
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
                <td style="width:10%;"><input type="text" id="Itemcodesearch" class="form-control" onkeyup="ItemCodeFunction(event)"></td>
                <td style="width:10%;"><input type="text" id="Itemnamesearch" class="form-control" onkeyup="ItemNameFunction(event)"></td>
                <td style="width:8%;"><input type="text" id="ItemUOMsearch" class="form-control" onkeyup="ItemUOMFunction(event)"></td>
                <td style="width:8%;"><input type="text" id="ItemQTYsearch" class="form-control" onkeyup="ItemQTYFunction(event)" readonly></td>
                <td style="width:8%;"><input type="text" id="ItemGroupsearch" class="form-control" onkeyup="ItemGroupFunction(event)"></td>
                <td style="width:8%;"><input type="text" id="ItemCategorysearch" class="form-control" onkeyup="ItemCategoryFunction(event)"></td>
                
                <td style="width:8%;"><input type="text" id="ItemBUsearch" class="form-control" onkeyup="ItemBUFunction(event)" readonly></td>
                <td style="width:8%;" {{$AlpsStatus['hidden']}}><input type="text" id="ItemAPNsearch" class="form-control" onkeyup="ItemAPNFunction(event)"></td>
                <td style="width:8%;" {{$AlpsStatus['hidden']}}><input type="text" id="ItemCPNsearch" class="form-control" onkeyup="ItemCPNFunction(event)"></td>
                <td style="width:8%;" {{$AlpsStatus['hidden']}}><input type="text" id="ItemOEMPNsearch" class="form-control" onkeyup="ItemOEMPNFunction(event)"></td>

                
                <td style="width:8%;"><input  type="text" id="ItemStatussearch" class="form-control" onkeyup="ItemStatusFunction(event)" readonly></td>
              </tr>
            </tbody>
          </table>

          <table id="ItemIDTable2" class="display nowrap table  table-striped table-bordered" style="width:100%" >
            <thead id="thead2"></thead>
            <tbody id="tbody_ItemID"></tbody>
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
.text-danger{
  color:red !important;
}

table {font-size: 13px;}
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
  window.location.reload();
}

$("#btnSave").click(function() {
  var formReqData = $("#production_order_edit");
  if(formReqData.valid()){
    validateForm('fnSaveData','update');
  }
});

$("#btnApprove").click(function(){
  var formReqData = $("#production_order_edit");
  if(formReqData.valid()){
    validateForm('fnApproveData','approve');
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
  if($.trim($("#FocusId").val())!=""){
    $("#"+$("#FocusId").val()).focus();
  }  
  //$("#"+$("#FocusId").val()).focus();
  $("#closePopup").click();
}

function highlighFocusBtn(pclass){
  $(".activeYes").hide();
  $(".activeNo").hide();
  $("."+pclass+"").show();
}

/*================================== Save FUNCTION =================================*/
window.fnSaveData = function (){

  event.preventDefault();

  var trnFormReq  = $("#production_order_edit");
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
    url:'{{ route("transaction",[$FormId,"update"])}}',
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

window.fnApproveData = function (){

  event.preventDefault();

  var trnFormReq  = $("#production_order_edit");
  var formData    = trnFormReq.serialize();

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  $("#btnApprove").hide(); 
  $(".buttonload_approve").show();  
  $("#btnSave").prop("disabled", true);
  $.ajax({
    url:'{{ route("transaction",[$FormId,"Approve"])}}',
    type:'POST',
    data:formData,
    success:function(data) {
      $("#btnApprove").show();  
      $(".buttonload_approve").hide();  
      $("#btnSave").prop("disabled", false);

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
        $("#btnApprove").show();  
        $(".buttonload_approve").hide();  
        $("#btnSave").prop("disabled", false);
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

/*================================== VALIDATE FUNCTION =================================*/
function validateForm(actionType,actionMsg){
  
  $("#FocusId").val('');
  var RPRO_NO      = $.trim($("#RPRO_NO").val());
  var RPR_DT      = $.trim($("#RPR_DT").val());
  var PROID_REF   = $.trim($("#PROID_REF").val());
  var PSTAGEID_REF   = $.trim($("#PSTAGEID_REF").val());  
  var STID_REF   = $.trim($("#STID_REF").val());  
  var EMPID_REF   = $.trim($("#EMPID_REF").val());  
  
  if(RPRO_NO ===""){
      $("#FocusId").val('RPRO_NO');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('RPR No is required.');
      $("#alert").modal('show')
      $("#OkBtn1").focus();
      return false;
  }  
  else if(RPR_DT ===""){
      $("#FocusId").val('RPR_DT');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please select RPR Date.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  }
  else if(PROID_REF ===""){
     // $("#FocusId").val('RPRO_NO');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please select PRO No.');
      $("#alert").modal('show')
      $("#OkBtn1").focus();
      return false;
  }  
  else if(PSTAGEID_REF ===""){
     // $("#FocusId").val('RPRO_NO');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please select Production Stage.');
      $("#alert").modal('show')
      $("#OkBtn1").focus();
      return false;
  }
  else if(STID_REF ===""){
     // $("#FocusId").val('RPRO_NO');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please select Store.');
      $("#alert").modal('show')
      $("#OkBtn1").focus();
      return false;
  }
  else if(EMPID_REF ===""){
     // $("#FocusId").val('RPRO_NO');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please select Requested By.');
      $("#alert").modal('show')
      $("#OkBtn1").focus();
      return false;
  }
  else{
    var allblank1   = [];
    var allblank2   = [];
    var allblank3   = [];
    var allblank4   = [];
    var allblank5   = [];
    var allblank6   = [];
    var allblank7   = [];
    var allblank8   = [];
    var focustext   = "";
      
    $('#example2').find('.participantRow').each(function(){

      if($.trim($(this).find("[id*=ITEMID_REF]").val()) ===""){
        allblank1.push('false');
        focustext = $(this).find("[id*=popupITEMID]").attr('id');
      }     
      else if($.trim($(this).find("[id*=QTY]").val()) ===""){
        allblank2.push('false');
        focustext = $(this).find("[id*=QTY]").attr('id');
      }
      else if($.trim($(this).find("[id*=REQ_QTY]").val()) ===""){
        allblank3.push('false');
        focustext = $(this).find("[id*=REQ_QTY]").attr('id');
      }
      else if(parseFloat($.trim($(this).find("[id*=REQ_QTY]").val())) > parseFloat($.trim($(this).find("[id*=QTY]").val())) ){

        allblank4.push('false');
        focustext = $(this).find("[id*=REQ_QTY]").attr('id');
      }
      else if(parseFloat($.trim($(this).find("[id*=REQ_QTY]").val())) <= 0.000 ){
        allblank5.push('false');
        focustext = $(this).find("[id*=REQ_QTY]").attr('id');
      }
      else{
        allblank1.push('true');
        allblank2.push('true');
        allblank3.push('true');
        allblank4.push('true');        
        allblank5.push('true');        
        focustext   = "";
      }

    });

    $('#example3').find('.participantRow3').each(function(){
      if($.trim($(this).find("[id*=UDF]").val())!=""){
        if($.trim($(this).find("[id*=UDFismandatory]").val())=="1"){
          if($.trim($(this).find('[id*="udfvalue"]').val()) != ""){
            allblank8.push('true');
          }
          else{
            allblank8.push('false');
          }
        }  
      }                
    });
    
    if(jQuery.inArray("false", allblank1) !== -1){
      $("#FocusId").val(focustext);
      $("#alert").modal('show');
      $("#AlertMessage").text('Please Select Item In Material');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank2) !== -1){
      $("#FocusId").val(focustext);
      $("#alert").modal('show');
      $("#AlertMessage").text('Qty cannot be left blank in material tab.');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank3) !== -1){
      $("#FocusId").val(focustext);
      $("#alert").modal('show');
      $("#AlertMessage").text('Requisition Qty cannot be left blank in material tab.');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank4) !== -1){
      $("#FocusId").val(focustext);
      $("#alert").modal('show');
      $("#AlertMessage").text('Requisition Qty can not be greater then Balance Production Order Qty in material tab.');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank5) !== -1){
      $("#FocusId").val(focustext);
      $("#alert").modal('show');
      $("#AlertMessage").text('Requisition Qty should be greater then 0 in material tab.');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank8) !== -1){
      $("#FocusId").val(focustext);
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
      $("#AlertMessage").text('Do you want to '+actionMsg+' to record.');
      $("#YesBtn").data("funcname",actionType);
      $("#YesBtn").focus();
      $("#OkBtn").hide();
      highlighFocusBtn('activeYes');
    }
  }
}

/*================================== CHECK DUPLICATE FUNCTION =================================*/
function checkDuplicateCode(){

  var trnFormReq  = $("#production_order_edit");
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
              showError('ERROR_RPRO_NO',data.msg);
              $("#RPRO_NO").focus();
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


/*================================== PRODUCTION STAGES POPUP FUNCTION =================================*/
let tpstageid = "#ProdStageTable2";
      let tpstageid2 = "#ProdStageTable";
      let pstageheaders = document.querySelectorAll(tpstageid2 + " th");

      // Sort the table element when clicking on the table pstageheaders
      pstageheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(tpstageid, ".clspstage", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function PStageCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("pstage_code_search");
        filter = input.value.toUpperCase();
        table = document.getElementById("ProdStageTable2");
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

  function PStageDescFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("pstage_desc_search");
        filter = input.value.toUpperCase();
        table = document.getElementById("ProdStageTable2");
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

      $('#txtpstage_popup').focus(function(event){
        showSelectedCheck($("#PSTAGEID_REF").val(),"SELECT_PSTAGEID_REF");
         $("#pstage_popup").show();
         event.preventDefault();
      });

      $("#pstage_closePopup").click(function(event){
        $("#pstage_popup").hide();
        event.preventDefault();
      });

      $(".clspstage").click(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");
        // var txtid= $('#hdn_fieldid').val();
        // var txt_id2= $('#hdn_fieldid2').val();
        
        $('#txtpstage_popup').val(texdesc);
        $('#PSTAGEID_REF').val(txtval);
        $("#pstage_popup").hide();
        $("#pstage_code_search").val(''); 
        $("#pstage_desc_search").val('');        
        event.preventDefault();
      });


/*================================== STORE POPUP FUNCTION =================================*/
let storeid = "#storeTable2";
      let storeid2 = "#storeTable";
      let storeheaders = document.querySelectorAll(storeid2 + " th");

      // Sort the table element when clicking on the table storeheaders
      storeheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(storeid, ".clsstore", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function storeCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("store_code_search");
        filter = input.value.toUpperCase();
        table = document.getElementById("storeTable2");
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

      function storeNameFunction() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("store_name_search");
            filter = input.value.toUpperCase();
            table = document.getElementById("storeTable2");
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

      $('#txtstore_popup').focus(function(event){
        showSelectedCheck($("#STID_REF").val(),"SELECT_STID_REF");
         $("#store_popup").show();
         event.preventDefault();
      });

      $("#store_closePopup").click(function(event){
        $("#store_popup").hide();
        event.preventDefault();
      });

      $(".clsstore").click(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");
        // var txstoreid= $('#hdn_fieldid').val();
        // var txt_id2= $('#hdn_fieldid2').val();
        
        $('#txtstore_popup').val(texdesc);
        $('#STID_REF').val(txtval);
        $("#store_popup").hide();
        $("#store_code_search").val(''); 
        $("#store_name_search").val(''); 
             
        event.preventDefault();
      });

/*================================== EMPLOYEE POPUP FUNCTION =================================*/
let employeeid = "#employeeTable2";
      let employeeid2 = "#employeeTable";
      let employeeheaders = document.querySelectorAll(employeeid2 + " th");

      // Sort the table element when clicking on the table employeeheaders
      employeeheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(employeeid, ".clsemployee", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function employeeCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("employee_code_search");
        filter = input.value.toUpperCase();
        table = document.getElementById("employeeTable2");
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

      function employeeNameFunction() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("employee_name_search");
            filter = input.value.toUpperCase();
            table = document.getElementById("employeeTable2");
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

      $('#txtemployee_popup').click(function(event){
        showSelectedCheck($("#EMPID_REF").val(),"SELECT_EMPID_REF");
         $("#employee_popup").show();
         event.preventDefault();
      });

      $("#employee_closePopup").click(function(event){
        $("#employee_popup").hide();
        event.preventDefault();
      });

      $(".clsemployee").click(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");
        // var txemployeeid= $('#hdn_fieldid').val();
        // var txt_id2= $('#hdn_fieldid2').val();
        
        $('#txtemployee_popup').val(texdesc);
        $('#EMPID_REF').val(txtval);
        $("#employee_popup").hide();
        $("#employee_code_search").val(''); 
        $("#employee_name_search").val(''); 
            
        event.preventDefault();
      });


/*================================== FGI DETAILS =================================*/

let fgiid = "#FGITable2";
let fgiid2 = "#FGITable";
let fgiidheaders = document.querySelectorAll(fgiid2 + " th");

fgiidheaders.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(fgiid, ".clsfgiid", "td:nth-child(" + (i + 1) + ")");
  });
});

function FGICodeFunction(e) {
  if(e.which == 13){
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("fgicodesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("FGITable2");
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

function FGINameFunction(e) {
  if(e.which == 13){
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("fginamesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("FGITable2");
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

function FGIUOMFunction(e) {
  if(e.which == 13){
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("fgiUOMsearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("FGITable2");
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

function FGIQTYFunction(e) {
  if(e.which == 13){
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("fgiQTYsearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("FGITable2");
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

function FGIGroupFunction(e) {
  if(e.which == 13){
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("fgiGroupsearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("FGITable2");
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

function FGICategoryFunction(e) {
  if(e.which == 13){
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("fgiCategorysearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("FGITable2");
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

function FGIItemBUFunction() {
	var input, filter, table, tr, td, i, txtValue;
	input = document.getElementById("fgiItemBUsearch");
	filter = input.value.toUpperCase();
	table = document.getElementById("FGITable2");
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

function FGIItemAPNFunction() {
	var input, filter, table, tr, td, i, txtValue;
	input = document.getElementById("fgiItemAPNsearch");
	filter = input.value.toUpperCase();
	table = document.getElementById("FGITable2");
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

function FGIItemCPNFunction() {
	var input, filter, table, tr, td, i, txtValue;
	input = document.getElementById("fgiItemCPNsearch");
	filter = input.value.toUpperCase();
	table = document.getElementById("FGITable2");
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

function FGIItemOEMPNFunction() {
	var input, filter, table, tr, td, i, txtValue;
	input = document.getElementById("fgiItemOEMPNsearch");
	filter = input.value.toUpperCase();
	table = document.getElementById("FGITable2");
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

function FGIStatusFunction(e) {
  if(e.which == 13){
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("fgiStatussearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("FGITable2");
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

$('#Material').on('focus','[id*="popupFGI"]',function(event){

var PROID_REF      =  $("#PROID_REF").val();
var STID_REF       =  $("#STID_REF").val();
var txtpro_popup   =  $("#txtpro_popup").attr('id');

if(PROID_REF ===""){
  $("#FocusId").val(txtpro_popup);
  $("#ProceedBtn").focus();
  $("#YesBtn").hide();
  $("#NoBtn").hide();
  $("#OkBtn1").show();
  $("#AlertMessage").text('Please select PRO No.');
  $("#alert").modal('show')
  $("#OkBtn1").focus();
}  
else if(STID_REF ===""){
  $("#FocusId").val('txtstore_popup');
  $("#ProceedBtn").focus();
  $("#YesBtn").hide();
  $("#NoBtn").hide();
  $("#OkBtn1").show();
  $("#AlertMessage").text('Please select Store.');
  $("#alert").modal('show')
  $("#OkBtn1").focus();
}  
else{

  $('.fgijs-selectall').prop('disabled', true);   

  $("#tbody_ItemID").html('');  //clear for variable confliction
  $("#tbody_FGI").html('loading...');
  $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  $.ajax({
      url:'{{route("transaction",[$FormId,"getFGIDetails"])}}',
      type:'POST',
      data:{'PROID_REF':PROID_REF,'STID_REF':STID_REF},
      success:function(data) {
        $("#tbody_FGI").html(data);    
        bindFGIEvents();   
        $('.fgijs-selectall').prop('disabled', false);                     
      },
      error:function(data){
        console.log("Error: Something went wrong.");
        $("#tbody_FGI").html('');                        
      },
  }); 
        
  $("#FGIIDpopup").show();

  var id1   = $(this).attr('id');
  var id2   = $(this).parent().parent().find('[id*="FGI_REF"]').attr('id');
  var id3   = $(this).parent().parent().find('[id*="FGIName"]').attr('id');
  var id4   = $(this).parent().parent().find('[id*="popupMAINITEMUOM"]').attr('id');
  var id5   = $(this).parent().parent().find('[id*="MAINITEM_UOMID_REF"]').attr('id');
  var id6   = ""; 
  var id7   = "";
  var id8   = "" 
  var id9   = $(this).parent().parent().find('[id*="SQID_REF"]').attr('id');
  var id10  = $(this).parent().parent().find('[id*="SEID_REF"]').attr('id');
  var id11  = $(this).parent().parent().find('[id*="SOID_REF"]').attr('id');

  $('#hdn_FGIID1').val(id1);
  $('#hdn_FGIID2').val(id2);
  $('#hdn_FGIID3').val(id3);
  $('#hdn_FGIID4').val(id4);
  $('#hdn_FGIID5').val(id5);
  $('#hdn_FGIID6').val(id6);
  $('#hdn_FGIID7').val(id7);
  $('#hdn_FGIID8').val(id8);
  $('#hdn_FGIID9').val(id9);
  $('#hdn_FGIID10').val(id10);
  $('#hdn_FGIID11').val(id11);

  var r_count = 0;
  var ItemID = [];
  $('#example2').find('.participantRow').each(function(){
    if($(this).find('[id*="FGI_REF"]').val() != '')
    {
      ItemID.push($(this).find('[id*="FGI_REF"]').val());
      r_count = parseInt(r_count)+1;
      $('#hdn_FGIID21').val(r_count); // row counter
    }
  });
  $('#hdn_FGIID19').val(ItemID.join(', '));

  var SOID = [];
  $('#example2').find('.participantRow').each(function(){
    if($(this).find('[id*="SOID_REF"]').val() != '')
    {
      SOID.push($(this).find('[id*="SOID_REF"]').val());
    }
  });
  $('#hdn_FGIID23').val(SOID.join(', '));

  var SQID = [];
  $('#example2').find('.participantRow').each(function(){
    if($(this).find('[id*="SQID_REF"]').val() != '')
    {
      SQID.push($(this).find('[id*="SQID_REF"]').val());
    }
  });
  $('#hdn_FGIID24').val(SQID.join(', '));

  var SEID = [];
  $('#example2').find('.participantRow').each(function(){
    if($(this).find('[id*="SEID_REF"]').val() != '')
    {
      SEID.push($(this).find('[id*="SEID_REF"]').val());
    }
  });
  $('#hdn_FGIID25').val(SEID.join(', '));
  // $('#example2').find('.participantRow').each(function(){
  //   var rowCount = $(this).closest('table').find('.participantRow').length;
  //   $('#Row_Count1').val(rowCount);
  //   //$(this).closest('.participantRow').find('input:text').val('');
  //   //$(this).closest('.participantRow').find('input:hidden').val('');
  //   if (rowCount > 1) {
  //     $(this).closest('.participantRow').remove();  
  //   } 
  // });

  event.preventDefault();

}

});

$("#FGIID_closePopup").click(function(event){
  $("#FGIIDpopup").hide();
});

function bindFGIEvents(){

  $('#FGITable2').off(); 
  
  //-------------------------
  $('.fgijs-selectall').change(function()
  { 

    if($(this).prop("checked")){
      var item_array = [];
      $('#FGITable2').find('.clsfgiid').each(function()
      {
          var fildfgi_id          =   $(this).attr('id');
          var MAINITEM_ID         =   $("#txt"+fildfgi_id+"").data("desc1"); 
          //var MAINITEM_CODE        =  JSON.parse($("#txt"+fildfgi_id+"").data("desc2")) ;
          //var MAINITEM_NAME       =  JSON.parse( $("#txt"+fildfgi_id+"").data("desc3")) ;
          var MAINITEM_UOMID    =   $("#txt"+fildfgi_id+"").data("desc4");    
          //var MAINITEM_UOMCODE  =   JSON.parse( $("#txt"+fildfgi_id+"").data("desc5") ) ;      
          var SQID_REF           =   $("#txt"+fildfgi_id+"").data("desc8");
          var SEID_REF           =   $("#txt"+fildfgi_id+"").data("desc9");
          var SOID_REF           =   $("#txt"+fildfgi_id+"").data("desc11");
          var PROID_REF           =   $("#txt"+fildfgi_id+"").data("proid");

          item_array.push(PROID_REF+'_'+SOID_REF+"_"+SEID_REF+'_'+SQID_REF+'_'+MAINITEM_ID+'_'+MAINITEM_UOMID);

      });
      get_all_item(item_array);
      $('#FGIIDpopup').hide();
      $('.fgijs-selectall').prop("checked", false);
      return false;
    }else{
      $('#FGIIDpopup').hide();
      return false;
    }
   
    
    return false;
    event.preventDefault();

  }); 
  
  //-------------------------

  $('[id*="chkfgiId"]').change(function(){

var fildfgi_id             =   $(this).parent().parent().attr('id');
var item_id             =   $("#txt"+fildfgi_id+"").data("desc1");
var item_code           =   $("#txt"+fildfgi_id+"").data("desc2");
var item_name           =   $("#txt"+fildfgi_id+"").data("desc3");
var item_main_uom_id    =   $("#txt"+fildfgi_id+"").data("desc4");
var item_main_uom_code  =   $("#txt"+fildfgi_id+"").data("desc5");
var item_qty            =   $("#txt"+fildfgi_id+"").data("desc6");
var item_unique_row_id  =   $("#txt"+fildfgi_id+"").data("desc7");
var item_sqid           =   $("#txt"+fildfgi_id+"").data("desc8");
var item_seid           =   $("#txt"+fildfgi_id+"").data("desc9");
var item_soid           =   $("#txt"+fildfgi_id+"").data("desc11");


if($(this).is(":checked") == true) {

  // $('#example2').find('.participantRow').each(function(){

  //   var SOID_REF    =   $(this).find('[id*="SOID_REF"]').val();
  //   var FGI_REF  =   $(this).find('[id*="FGI_REF"]').val();
  //   var SQID_REF    =   $(this).find('[id*="SQID_REF"]').val();
  //   var SEID_REF    =   $(this).find('[id*="SEID_REF"]').val();
  //   var exist_val   =   SOID_REF+"_"+SQID_REF+"_"+SEID_REF+"_"+FGI_REF;

  //   if(item_id){
  //     if(item_unique_row_id == exist_val){
  //       $("#FGIIDpopup").hide();
  //       $("#YesBtn").hide();
  //       $("#NoBtn").hide();
  //       $("#OkBtn").hide();
  //       $("#OkBtn1").show();
  //       $("#AlertMessage").text('Item already exists.');
  //       $("#alert").modal('show');
  //       $("#OkBtn1").focus();
  //       highlighFocusBtn('activeOk1');

  //       $('#hdn_FGIID1').val('');
  //       $('#hdn_FGIID2').val('');
  //       $('#hdn_FGIID3').val('');
  //       $('#hdn_FGIID4').val('');
  //       $('#hdn_FGIID5').val('');
  //       $('#hdn_FGIID6').val('');
  //       $('#hdn_FGIID7').val('');
  //       $('#hdn_FGIID8').val('');
  //       $('#hdn_FGIID9').val('');
  //       $('#hdn_FGIID10').val('');
  //       $('#hdn_FGIID11').val('');
         
  //       item_id             =   '';
  //       item_code           =   '';
  //       item_name           =   '';
  //       item_main_uom_id    =   '';
  //       item_main_uom_code  =   '';
  //       item_qty            =   '';
  //       item_unique_row_id  =   '';
  //       item_sqid           =   '';
  //       item_seid           =   '';
  //       item_soid           =   '';
  //       return false;
  //     }               
  //   } 
             
  // });

  if($('#hdn_FGIID1').val() == "" && item_id != ''){

    var $tr       =   $('.material').closest('table');
    var allTrs    =   $tr.find('.participantRow').last();
    var lastTr    =   allTrs[allTrs.length-1];
    var $clone    =   $(lastTr).clone();

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
    $clone.find('[id*="popupFGI"]').val(item_code);
    $clone.find('[id*="FGI_REF"]').val(item_id);
    $clone.find('[id*="FGIName"]').val(item_name);
    $clone.find('[id*="popupMAINITEMUOM"]').val(item_main_uom_code);
    $clone.find('[id*="MAINITEM_UOMID_REF"]').val(item_main_uom_id);
    //$clone.find('[id*="QTY"]').val(item_qty);
    //$clone.find('[id*="REQ_QTY"]').val(item_qty);
    $clone.find('[id*="SQID_REF"]').val(item_sqid);
    $clone.find('[id*="SEID_REF"]').val(item_seid);
    $clone.find('[id*="SOID_REF"]').val(item_soid);
    //clear sub item
    $clone.find('[id*="popupITEMID"]').val('');
    $clone.find('[id*="ITEMID_REF"]').val('');
    $clone.find('[id*="ItemName"]').val('');
    $clone.find('[id*="popupMUOM"]').val('');
    $clone.find('[id*="MAIN_UOMID_REF"]').val('');
    $clone.find('[id*="QTY"]').val('0.000');
    $clone.find('[id*="REMARKS"]').val('');

    $tr.closest('table').append($clone);   
    var rowCount = $('#Row_Count1').val();
    rowCount    = parseInt(rowCount)+1;
    $('#Row_Count1').val(rowCount);
    $("#FGIIDpopup").hide();
    
    event.preventDefault();

  }
  else{
   
    var txt_id1   =   $('#hdn_FGIID1').val();
    var txt_id2   =   $('#hdn_FGIID2').val();
    var txt_id3   =   $('#hdn_FGIID3').val();
    var txt_id4   =   $('#hdn_FGIID4').val();
    var txt_id5   =   $('#hdn_FGIID5').val();
    var txt_id6   =   $('#hdn_FGIID6').val();
    var txt_id7   =   $('#hdn_FGIID7').val();
    var txt_id8   =   $('#hdn_FGIID8').val();
    var txt_id9   =   $('#hdn_FGIID9').val();
    var txt_id10  =   $('#hdn_FGIID10').val();
    var txt_id11  =   $('#hdn_FGIID11').val();


       
    if($.trim(txt_id1)!=""){
      $('#'+txt_id1).val(item_code);
      $('#'+txt_id1).parent().parent().find('[id*="popupITEMID"]').val('');
      $('#'+txt_id1).parent().parent().find('[id*="ITEMID_REF"]').val('');
      $('#'+txt_id1).parent().parent().find('[id*="ItemName"]').val('');
      $('#'+txt_id1).parent().parent().find('[id*="popupMUOM"]').val('');
      $('#'+txt_id1).parent().parent().find('[id*="MAIN_UOMID_REF"]').val('');
      $('#'+txt_id1).parent().parent().find('[id*="QTY"]').val('0.000');
      $('#'+txt_id1).parent().parent().find('[id*="REMARKS"]').val('');

    }

    if($.trim(txt_id2)!=""){
      $('#'+txt_id2).val(item_id);
    }
    if($.trim(txt_id3)!=""){
      $('#'+txt_id3).val(item_name);
    }
    if($.trim(txt_id4)!=""){
      $('#'+txt_id4).val(item_main_uom_code);
    }
    if($.trim(txt_id5)!=""){
      $('#'+txt_id5).val(item_main_uom_id);
    }
    if($.trim(txt_id6)!=""){
     // $('#'+txt_id6).val(item_qty);
    }
    if($.trim(txt_id7)!=""){
      $('#'+txt_id7).val(0);
    }
    if($.trim(txt_id8)!=""){
     // $('#'+txt_id8).val(item_qty);
    }
    if($.trim(txt_id9)!=""){
      $('#'+txt_id9).val(item_sqid);
    }
    if($.trim(txt_id10)!=""){
      $('#'+txt_id10).val(item_seid);
    }
    if($.trim(txt_id11)!=""){
      $('#'+txt_id11).val(item_soid);
    }
    $('#hdn_FGIID1').val('');
    $('#hdn_FGIID2').val('');
    $('#hdn_FGIID3').val('');
    $('#hdn_FGIID4').val('');
    $('#hdn_FGIID5').val('');
    $('#hdn_FGIID6').val('');
    $('#hdn_FGIID7').val('');
    $('#hdn_FGIID8').val('');
    $('#hdn_FGIID9').val('');
    $('#hdn_FGIID10').val('');
    $('#hdn_FGIID11').val('');
    
  }
          
  $("#FGIIDpopup").hide();
  event.preventDefault();
}
else if($(this).is(":checked") == false){

  var id = item_id;
  var r_count = $('#Row_Count1').val();

  $('#example2').find('.participantRow').each(function(){
    var FGI_REF = $(this).find('[id*="FGI_REF"]').val();

    if(id == FGI_REF){
      var rowCount = $('#Row_Count1').val();

      if (rowCount > 1) {
        $(this).closest('.participantRow').remove(); 
        rowCount = parseInt(rowCount)-1;
        $('#Row_Count1').val(rowCount);
      }
      else {
        $(document).find('.dmaterial').prop('disabled', true);  
        $("#FGIIDpopup").hide();
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

$("#fgicodesearch").val(''); 
$("#fginamesearch").val(''); 
$("#fgiUOMsearch").val(''); 
$("#fgiGroupsearch").val(''); 
$("#fgiCategorysearch").val(''); 
$("#fgiStatussearch").val(''); 
$('.remove').removeAttr('disabled'); 

event.preventDefault();
});

} //bindFGIEvents

/*================================== ITEM DETAILS =================================*/


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

$('#Material').on('focus','[id*="popupITEMID"]',function(event){

var PROID_REF      =  $("#PROID_REF").val();
var STID_REF       =  $("#STID_REF").val();
var txtpro_popup   =  $("#txtpro_popup").attr('id');
var FGI_REF        =  $.trim( $(this).parent().parent().find('[id*="FGI_REF"]').val() );
var SQID_REF       = $(this).parent().parent().find('[id*="SQID_REF"]').val();
var SEID_REF       = $(this).parent().parent().find('[id*="SEID_REF"]').val();
var SOID_REF       = $(this).parent().parent().find('[id*="SOID_REF"]').val();

if(PROID_REF ===""){
  $("#FocusId").val(txtpro_popup);
  $("#ProceedBtn").focus();
  $("#YesBtn").hide();
  $("#NoBtn").hide();
  $("#OkBtn1").show();
  $("#AlertMessage").text('Please select PRO No.');
  $("#alert").modal('show')
  $("#OkBtn1").focus();

}else if(FGI_REF ===""){
  $("#FocusId").val('');
  $("#ProceedBtn").focus();
  $("#YesBtn").hide();
  $("#NoBtn").hide();
  $("#OkBtn1").show();
  $("#AlertMessage").text('Please select Finished Good Item.');
  $("#alert").modal('show')
  $("#OkBtn1").focus();
}  
else{

  $('.js-selectall').prop('disabled', true);   

  $("#tbody_FGI").html('');
  $("#tbody_ItemID").html('loading...');
  $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $.ajax({
      url:'{{route("transaction",[$FormId,"getItemDetails"])}}',
      type:'POST',
      data:{'PROID_REF':PROID_REF,'status':'A','FGI_REF':FGI_REF,'SQID_REF':SQID_REF,'SEID_REF':SEID_REF,'SOID_REF':SOID_REF,'STID_REF':STID_REF},
      success:function(data) {
        $("#tbody_ItemID").html(data);    
        bindItemEvents();   
        $('.js-selectall').prop('disabled', false);                     
      },
      error:function(data){
        console.log("Error: Something went wrong.");
        $("#tbody_ItemID").html('');                        
      },
  }); 
        
  $("#ITEMIDpopup").show();

  var id1   = $(this).attr('id');
  var id2   = $(this).parent().parent().find('[id*="ITEMID_REF"]').attr('id');
  var id3   = $(this).parent().parent().find('[id*="ItemName"]').attr('id');
  var id4   = $(this).parent().parent().find('[id*="popupMUOM"]').attr('id');
  var id5   = $(this).parent().parent().find('[id*="MAIN_UOMID_REF"]').attr('id');
  var id6   = $(this).parent().parent().find('[id*="QTY"]').attr('id');
  var id7   = "";
  var id8   = $(this).parent().parent().find('[id*="REQ_QTY"]').attr('id');
  var id9   = $(this).parent().parent().find('[id*="SQID_REF"]').attr('id');
  var id10  = $(this).parent().parent().find('[id*="SEID_REF"]').attr('id');
  var id11  = $(this).parent().parent().find('[id*="SOID_REF"]').attr('id');

  $('#hdn_ItemID1').val(id1);
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

  var r_count = 0;
  var ItemID = [];
  $('#example2').find('.participantRow').each(function(){
    if($(this).find('[id*="ITEMID_REF"]').val() != '')
    {
      ItemID.push($(this).find('[id*="ITEMID_REF"]').val());
      r_count = parseInt(r_count)+1;
      $('#hdn_ItemID21').val(r_count); // row counter
    }
  });
  $('#hdn_ItemID19').val(ItemID.join(', '));

  var SOID = [];
  $('#example2').find('.participantRow').each(function(){
    if($(this).find('[id*="SOID_REF"]').val() != '')
    {
      SOID.push($(this).find('[id*="SOID_REF"]').val());
    }
  });
  $('#hdn_ItemID23').val(SOID.join(', '));

  var SQID = [];
  $('#example2').find('.participantRow').each(function(){
    if($(this).find('[id*="SQID_REF"]').val() != '')
    {
      SQID.push($(this).find('[id*="SQID_REF"]').val());
    }
  });
  $('#hdn_ItemID24').val(SQID.join(', '));

  var SEID = [];
  $('#example2').find('.participantRow').each(function(){
    if($(this).find('[id*="SEID_REF"]').val() != '')
    {
      SEID.push($(this).find('[id*="SEID_REF"]').val());
    }
  });
  $('#hdn_ItemID25').val(SEID.join(', '));

  var FGIID = [];
  $('#example2').find('.participantRow').each(function(){
    if($(this).find('[id*="FGI_REF"]').val() != '')
    {
      FGIID.push($(this).find('[id*="FGI_REF"]').val());
    }
  });
  $('#hdn_ItemID26').val(FGIID.join(', '));

  event.preventDefault();

}

});

$("#ITEMID_closePopup").click(function(event){
$("#ITEMIDpopup").hide();
});


function bindItemEvents(){

$('#ItemIDTable2').off(); 
//-------------------------
$('.js-selectall').change(function()
{ 
  //select all checkbox
  var isChecked = $(this).prop("checked");
  var selector = $(this).data('target');
  $(selector).prop("checked", isChecked);
  //$$--------------------
  $('#ItemIDTable2').find('.clsitemid').each(function()
      {
          var fieldid             =   $(this).attr('id');
          var item_id             =   $("#txt"+fieldid+"").data("desc1");
          var item_code           =   $("#txt"+fieldid+"").data("desc2");
          var item_name           =   $("#txt"+fieldid+"").data("desc3");
          var item_main_uom_id    =   $("#txt"+fieldid+"").data("desc4");
          var item_main_uom_code  =   $("#txt"+fieldid+"").data("desc5");
          var item_qty            =   $("#txt"+fieldid+"").data("desc6");
          var item_unique_row_id  =   $("#txt"+fieldid+"").data("desc7");
          var item_sqid           =   $("#txt"+fieldid+"").data("desc8");
          var item_seid           =   $("#txt"+fieldid+"").data("desc9");
          var item_soid           =   $("#txt"+fieldid+"").data("desc11");
          var item_fgiid           =   $("#txt"+fieldid+"").data("itemfgiid");

          var apartno =  $("#addinfo"+fieldid+"").data("desc101");
          var cpartno =  $("#addinfo"+fieldid+"").data("desc102");
          var opartno =  $("#addinfo"+fieldid+"").data("desc103");
          var item_stock_in_hand  =   $("#txt"+fieldid+"").data("desc12");

         
          var rcount1 = parseInt($(this).closest('table').find('.clsitemid').length);
          var rcount2 = $('#hdn_ItemID21').val();
          var r_count2 = 0;
          
          var gridRow2 = [];
          $('#example2').find('.participantRow').each(function(){
            if($(this).find('[id*="ITEMID_REF"]').val() != '')
            {
              var subsitem = $(this).find('[id*="FGI_REF"]').val()+'_'+$(this).find('[id*="SOID_REF"]').val()+'_'+$(this).find('[id*="SQID_REF"]').val()+'_'+$(this).find('[id*="SEID_REF"]').val()+'_'+$(this).find('[id*="ITEMID_REF"]').val();
              gridRow2.push(subsitem);
              r_count2 = parseInt(r_count2) + 1;
            }
          });
      
          var slids =  $('#hdn_ItemID18').val();
          var itemids =  $('#hdn_ItemID19').val();
          var soids =  $('#hdn_ItemID23').val();
          var sqids =  $('#hdn_ItemID24').val();
          var seids =  $('#hdn_ItemID25').val();
          var fgiids =  $('#hdn_ItemID26').val();

          
  
          if($(this).find('[id*="chkId"]').is(":checked") == true) 
          {
            alert(item_stock_in_hand); 
            rcount1 = parseInt(rcount2)+parseInt(rcount1);
            if(parseInt(r_count2) >= parseInt(rcount1))
            {
              $('#hdn_ItemID1').val('');
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
              
              fieldid             =   "";
              item_id             =   "";
              item_code           =   "";
              item_name           =   "";
              item_main_uom_id    =   "";
              item_main_uom_code  =   "";
              item_qty            =   "";
              item_unique_row_id  =   "";
              item_sqid           =   "";
              item_seid           =   "";
              item_soid           =   "";
              item_fgiid           =   "";

              $('.js-selectall').prop("checked", false);
              $("#ITEMIDpopup").hide();
              return false;
            }
            
            var txtrowitem = item_fgiid+'_'+item_soid+'_'+item_sqid+'_'+item_seid+'_'+item_id;
            if(jQuery.inArray(txtrowitem, gridRow2) !== -1)
            {
                  $("#ITEMIDpopup").hide();
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").hide();
                  $("#OkBtn1").show();
                  $("#AlertMessage").text('Item already exists!');
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
                  $('#hdn_ItemID17').val('');
                  $('#hdn_ItemID18').val('');
                  $('#hdn_ItemID19').val('');
                  $('#hdn_ItemID20').val('');
                  $('#hdn_ItemID22').val('');
                  fieldid             =   "";
                  item_id             =   "";
                  item_code           =   "";
                  item_name           =   "";
                  item_main_uom_id    =   "";
                  item_main_uom_code  =   "";
                  item_qty            =   "";
                  item_unique_row_id  =   "";
                  item_sqid           =   "";
                  item_seid           =   "";
                  item_soid           =   "";
                  item_fgiid          =   "";
                  $('.js-selectall').prop("checked", false);
                  $("#ITEMIDpopup").hide();
                  return false;
            }

            // var slids =  $('#hdn_ItemID18').val();
            // var itemids =  $('#hdn_ItemID19').val();
            // var soids =  $('#hdn_ItemID23').val();
            // var sqids =  $('#hdn_ItemID24').val();
            // var seids =  $('#hdn_ItemID25').val();
            // var fgiids =  $('#hdn_ItemID26').val();

            // if(fgiids.indexOf(item_fgiid) != -1 && soids.indexOf(item_soid) != -1 && sqids.indexOf(item_sqid) != -1 && seids.indexOf(item_seid) != -1 && itemids.indexOf(item_id) != -1  )
            // {
            //               $("#ITEMIDpopup").hide();
            //               $("#YesBtn").hide();
            //               $("#NoBtn").hide();
            //               $("#OkBtn").hide();
            //               $("#OkBtn1").show();
            //               $("#AlertMessage").text('Item already exists.');
            //               $("#alert").modal('show');
            //               $("#OkBtn1").focus();
            //               highlighFocusBtn('activeOk1');
            //               $('#hdn_ItemID1').val('');
            //               $('#hdn_ItemID2').val('');
            //               $('#hdn_ItemID3').val('');
            //               $('#hdn_ItemID4').val('');
            //               $('#hdn_ItemID5').val('');
            //               $('#hdn_ItemID6').val('');
            //               $('#hdn_ItemID7').val('');
            //               $('#hdn_ItemID8').val('');
            //               $('#hdn_ItemID9').val('');
            //               $('#hdn_ItemID10').val('');
            //               $('#hdn_ItemID11').val('');
            //               $('#hdn_ItemID12').val('');
            //               $('#hdn_ItemID13').val('');
            //               $('#hdn_ItemID14').val('');
            //               $('#hdn_ItemID15').val('');
            //               $('#hdn_ItemID16').val('');
            //               $('#hdn_ItemID17').val('');
            //               $('#hdn_ItemID18').val('');
            //               $('#hdn_ItemID19').val('');
            //               $('#hdn_ItemID20').val('');
            //               $('#hdn_ItemID22').val('');
            //               fieldid             =   "";
            //               item_id             =   "";
            //               item_code           =   "";
            //               item_name           =   "";
            //               item_main_uom_id    =   "";
            //               item_main_uom_code  =   "";
            //               item_qty            =   "";
            //               item_unique_row_id  =   "";
            //               item_sqid           =   "";
            //               item_seid           =   "";
            //               item_soid           =   "";
            //               item_fgiid           =   "";
            //               $('.js-selectall').prop("checked", false);
            //               $("#ITEMIDpopup").hide();
            //               return false;
            // }
                if($('#hdn_ItemID1').val() == "" && item_id != '')
                {
                  var txtid= $('#hdn_ItemID1').val();
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
                  var txt_id22= $('#hdn_ItemID22').val();

                  var txt_id23= $('#hdn_ItemID23').val();
                  var txt_id24= $('#hdn_ItemID24').val();
                  var txt_id25= $('#hdn_ItemID25').val();
                  var txt_id26= $('#hdn_ItemID26').val();

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
                      $clone.find('[id*="popupITEMID"]').val(item_code);
                      $clone.find('[id*="ITEMID_REF"]').val(item_id);
                      $clone.find('[id*="ItemName"]').val(item_name);
                      $clone.find('[id*="popupMUOM"]').val(item_main_uom_code);
                      $clone.find('[id*="MAIN_UOMID_REF"]').val(item_main_uom_id);
                      $clone.find('[id*="QTY"]').val(item_qty);
                      $clone.find('[id*="REQ_QTY"]').val(item_qty);
                      $clone.find('[id*="SQID_REF"]').val(item_sqid);
                      $clone.find('[id*="SEID_REF"]').val(item_seid);
                      $clone.find('[id*="SOID_REF"]').val(item_soid);

                      $clone.find('[id*="Alpspartno"]').val(apartno);
                      $clone.find('[id*="Custpartno"]').val(cpartno);
                      $clone.find('[id*="OEMpartno"]').val(opartno);
                                             
                      $tr.closest('table').append($clone);   
                      var rowCount = $('#Row_Count1').val();
                      rowCount = parseInt(rowCount)+1;
                      $('#Row_Count1').val(rowCount);
                     
                      event.preventDefault();
                }
                else
                {
                                                            
                    var txt_id1   =   $('#hdn_ItemID1').val();
                    var txt_id2   =   $('#hdn_ItemID2').val();
                    var txt_id3   =   $('#hdn_ItemID3').val();
                    var txt_id4   =   $('#hdn_ItemID4').val();
                    var txt_id5   =   $('#hdn_ItemID5').val();
                    var txt_id6   =   $('#hdn_ItemID6').val();
                    var txt_id7   =   $('#hdn_ItemID7').val();
                    var txt_id8   =   $('#hdn_ItemID8').val();
                    var txt_id9   =   $('#hdn_ItemID9').val();
                    var txt_id10  =   $('#hdn_ItemID10').val();
                    var txt_id11  =   $('#hdn_ItemID11').val();
                    var rowid     =   txt_id1.split("_").pop(0);    
                  
                    $('#'+txt_id1).val(item_code);
                    $('#'+txt_id2).val(item_id);
                    $('#'+txt_id3).val(item_name);
                    $('#'+txt_id4).val(item_main_uom_code);
                    $('#'+txt_id5).val(item_main_uom_id);
                    $('#'+txt_id6).val(item_qty);
                    //$('#'+txt_id7).val(0);
                    $('#'+txt_id8).val(item_qty);
                    $('#'+txt_id9).val(item_sqid);
                    $('#'+txt_id10).val(item_seid);
                    $('#'+txt_id11).val(item_soid);
                    $('#STOCK_INHAND_'+rowid).val(item_stock_in_hand);

                    $('#'+txt_id2).parent().parent().find('[id*="Alpspartno"]').val(apartno);
                    $('#'+txt_id2).parent().parent().find('[id*="Custpartno"]').val(cpartno);
                    $('#'+txt_id2).parent().parent().find('[id*="OEMpartno"]').val(opartno);
              
                    $('#hdn_ItemID1').val('');
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

                    event.preventDefault();
                }

                $('.js-selectall').prop("checked", false);
                // $("#ITEMIDpopup").reload();
                $('#ITEMIDpopup').hide();
                event.preventDefault();
                
          }
          // else if($(this).is(":checked") == false) 
          // {
          //   
          // }
        $("#Itemcodesearch").val(''); 
        $("#Itemnamesearch").val(''); 
        $("#ItemUOMsearch").val(''); 
        $("#ItemGroupsearch").val(''); 
        $("#ItemCategorysearch").val(''); 
        $("#ItemStatussearch").val(''); 
        $('.remove').removeAttr('disabled'); 
       
        event.preventDefault();
      });
  //$$--------------------
  
  $('#ITEMIDpopup').hide();
  return false;
  event.preventDefault();

}); 

//-------------------------

$('[id*="chkId"]').change(function(){

  var fieldid             =   $(this).parent().parent().attr('id');
  var item_id             =   $("#txt"+fieldid+"").data("desc1");
  var item_code           =   $("#txt"+fieldid+"").data("desc2");
  var item_name           =   $("#txt"+fieldid+"").data("desc3");
  var item_main_uom_id    =   $("#txt"+fieldid+"").data("desc4");
  var item_main_uom_code  =   $("#txt"+fieldid+"").data("desc5");
  var item_qty            =   $("#txt"+fieldid+"").data("desc6");
  var item_unique_row_id  =   $("#txt"+fieldid+"").data("desc7");
  var item_sqid           =   $("#txt"+fieldid+"").data("desc8");
  var item_seid           =   $("#txt"+fieldid+"").data("desc9");
  var item_soid           =   $("#txt"+fieldid+"").data("desc11");
  var item_fgiid           =   $("#txt"+fieldid+"").data("itemfgiid");

  var item_stock_in_hand  =   $("#txt"+fieldid+"").data("desc12");

  var apartno =  $("#addinfo"+fieldid+"").data("desc101");
  var cpartno =  $("#addinfo"+fieldid+"").data("desc102");
  var opartno =  $("#addinfo"+fieldid+"").data("desc103");

  if($(this).is(":checked") == true) {

    $('#example2').find('.participantRow').each(function(){

      var SOID_REF    =   $(this).find('[id*="SOID_REF"]').val();
      var ITEMID_REF  =   $(this).find('[id*="ITEMID_REF"]').val();
      var SQID_REF    =   $(this).find('[id*="SQID_REF"]').val();
      var SEID_REF    =   $(this).find('[id*="SEID_REF"]').val();
      var SEID_REF    =   $(this).find('[id*="SEID_REF"]').val();
      var FGI_REF    =   $(this).find('[id*="FGI_REF"]').val();
      var exist_val   =   FGI_REF+"_"+SOID_REF+"_"+SQID_REF+"_"+SEID_REF+"_"+ITEMID_REF;

      if(item_id){
        if(item_unique_row_id == exist_val){
          $("#ITEMIDpopup").hide();
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn").hide();
          $("#OkBtn1").show();
          $("#AlertMessage").text('Item already exists!!');
          $("#alert").modal('show');
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk1');

          $('#hdn_ItemID1').val('');
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
           
          item_id             =   '';
          item_code           =   '';
          item_name           =   '';
          item_main_uom_id    =   '';
          item_main_uom_code  =   '';
          item_qty            =   '';
          item_unique_row_id  =   '';
          item_sqid           =   '';
          item_seid           =   '';
          item_soid           =   '';
          return false;
        }               
      } 
               
    });

    if($('#hdn_ItemID1').val() == "" && item_id != ''){

      var $tr       =   $('.material').closest('table');
      var allTrs    =   $tr.find('.participantRow').last();
      var lastTr    =   allTrs[allTrs.length-1];
      var $clone    =   $(lastTr).clone();

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
      $clone.find('[id*="popupITEMID"]').val(item_code);
      $clone.find('[id*="ITEMID_REF"]').val(item_id);
      $clone.find('[id*="ItemName"]').val(item_name);
      $clone.find('[id*="popupMUOM"]').val(item_main_uom_code);
      $clone.find('[id*="MAIN_UOMID_REF"]').val(item_main_uom_id);
      $clone.find('[id*="QTY"]').val(item_qty);
      $clone.find('[id*="REQ_QTY"]').val(item_qty);
      $clone.find('[id*="SQID_REF"]').val(item_sqid);
      $clone.find('[id*="SEID_REF"]').val(item_seid);
      $clone.find('[id*="SOID_REF"]').val(item_soid);

      $clone.find('[id*="Alpspartno"]').val(apartno);
      $clone.find('[id*="Custpartno"]').val(cpartno);
      $clone.find('[id*="OEMpartno"]').val(opartno);

      $tr.closest('table').append($clone);   
      var rowCount = $('#Row_Count1').val();
      rowCount    = parseInt(rowCount)+1;
      $('#Row_Count1').val(rowCount);
      $("#ITEMIDpopup").hide();
      
      event.preventDefault();

    }
    else{

      var txt_id1   =   $('#hdn_ItemID1').val();
      var txt_id2   =   $('#hdn_ItemID2').val();
      var txt_id3   =   $('#hdn_ItemID3').val();
      var txt_id4   =   $('#hdn_ItemID4').val();
      var txt_id5   =   $('#hdn_ItemID5').val();
      var txt_id6   =   $('#hdn_ItemID6').val();
      var txt_id7   =   $('#hdn_ItemID7').val();
      var txt_id8   =   $('#hdn_ItemID8').val();
      var txt_id9   =   $('#hdn_ItemID9').val();
      var txt_id10  =   $('#hdn_ItemID10').val();
      var txt_id11  =   $('#hdn_ItemID11').val();
      var rowid     =   txt_id1.split("_").pop(0);  
      $('#STOCK_INHAND_'+rowid).val(item_stock_in_hand  );
      if($.trim(txt_id1)!=""){
        $('#'+txt_id1).val(item_code);
      }
      if($.trim(txt_id2)!=""){
        $('#'+txt_id2).val(item_id);
      }
      if($.trim(txt_id3)!=""){
        $('#'+txt_id3).val(item_name);
      }
      if($.trim(txt_id4)!=""){
        $('#'+txt_id4).val(item_main_uom_code);
      }
      if($.trim(txt_id5)!=""){
        $('#'+txt_id5).val(item_main_uom_id);
      }
      if($.trim(txt_id6)!=""){
        $('#'+txt_id6).val(item_qty);
      }
      if($.trim(txt_id7)!=""){
        $('#'+txt_id7).val(0);
      }
      if($.trim(txt_id8)!=""){
        $('#'+txt_id8).val(item_qty);
      }
      if($.trim(txt_id9)!=""){
        $('#'+txt_id9).val(item_sqid);
      }
      if($.trim(txt_id10)!=""){
        $('#'+txt_id10).val(item_seid);
      }
      if($.trim(txt_id11)!=""){
        $('#'+txt_id11).val(item_soid);
      }

      $('#'+txt_id2).parent().parent().find('[id*="Alpspartno"]').val(apartno);
      $('#'+txt_id2).parent().parent().find('[id*="Custpartno"]').val(cpartno);
      $('#'+txt_id2).parent().parent().find('[id*="OEMpartno"]').val(opartno);

      $('#hdn_ItemID1').val('');
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

} //bindItemEvents

/*================================== ADD/REMOVE FUNCTION ==================================*/

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

  $tr.closest('table').append($clone);         
  var rowCount1 = $('#Row_Count1').val();
  rowCount1 = parseInt(rowCount1)+1;
  $('#Row_Count1').val(rowCount1);
  $clone.find('.remove').removeAttr('disabled'); 
  event.preventDefault();
});

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

/*================================== USER DEFINE FUNCTION ==================================*/

function isNumberDecimalKey(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
    return false;

    return true;
}


function change_production_order(id,value){

  var field_id  =   id.split("_")[2];
  var QTY       =   parseFloat($("#QTY_"+field_id).val()).toFixed(3);
  var REQ_QTY =   parseFloat(value).toFixed(3);

  var qty_val = $("#"+id).parent().parent().find('[id*="QTY_"]').val();

  if(isNaN(value) || $.trim(value)==""){
    value = 0;
  }
 
  if(parseFloat(REQ_QTY) > parseFloat(qty_val)){

    //$("#FocusId").val('REQ_QTY_'+field_id);
    $("#"+id).val('0.000');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Requisition Qty can not be greater then Balance Production Order Qty.');
    $("#alert").modal('show')
    $("#OkBtn1").focus();    

  }    


}


function change_production_qty(id,value){
  //console.log(id,value);
}
/*================================== ONLOAD FUNCTION ==================================*/
$(document).ready(function(e) {
  var Material = $("#Material").html(); 
  $('#hdnmaterial').val(Material);
  // var lastdt = <?php echo json_encode($objlastdt[0]->RPR_DT); ?>;
  // var today = new Date(); 
  // var sodate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
  // $('#RPR_DT').attr('min',lastdt);
  // $('#RPR_DT').attr('max',sodate);

  var lastdt = <?php echo json_encode($objlastdt[0]->RPR_DT); ?>;
  var rpr = <?php echo json_encode($objResponse); ?>;
  var today = new Date(); 
  var sodate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2);
  if(lastdt < rpr.RPR_DT)
  {
	$('#RPR_DT').attr('min',lastdt);
  }
  else
  {
	  $('#RPR_DT').attr('min',rpr.RPR_DT);
  }
  $('#RPR_DT').attr('max',sodate);

  

  // var d = new Date(); 
  // var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
  // $('#RPR_DT').val(today);
  


});



/*================================== UDF FUNCTION ==================================*/

$(document).ready(function(e) {
	
	var udfdata = <?php echo json_encode($objUdfData); ?>;
	var count2  = <?php echo json_encode($objCountUDF); ?>;

	$('#Row_Count2').val(count2);
	$('#example3').find('.participantRow3').each(function(){

	  var txt_id4 = $(this).find('[id*="udfinputid"]').attr('id');
	  var udfid   = $(this).find('[id*="UDF"]').val();

	  $.each( udfdata, function( seukey, seuvalue ) {
		if(seuvalue.UDFPROID == udfid){

		  var txtvaltype2 = seuvalue.VALUETYPE;
		  var strdyn2     = txt_id4.split('_');
		  var lastele2    = strdyn2[strdyn2.length-1];
		  var dynamicid2  = "udfvalue_"+lastele2;
			  
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
	
	
	var soudf = <?php echo json_encode($objUDF); ?>;
	var udfforse = <?php echo json_encode($objUdfData2); ?>;
	
	$.each( soudf, function( soukey, souvalue ) {

		$.each( udfforse, function( usokey, usovalue ) { 
		
			if(souvalue.UDF == usovalue.UDFPROID_REF){
				$('#popupSEID_'+soukey).val(usovalue.LABEL);
			}
    
			if(souvalue.UDF == usovalue.UDFPROID_REF){        
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
                $('#'+dynamicid2).val(souvalue.VALUE);
                $('#UDFismandatory_'+soukey).val(usovalue.ISMANDATORY);
            
			}
		});
  
	});
});

$('#Material').on('blur',"[id*='REQ_QTY']",function()
{
    var qty2 = $.trim($(this).val());
    if(isNaN(qty2) || qty2=="" )
    {
      qty2 = 0;
    }  
    if(intRegex.test(qty2))
    {
      $(this).val((qty2 +'.000'));
    }   
    event.preventDefault();
}); 

function clearGrid(){
  $('#example2').find('.participantRow').each(function(){
    var rowCount = $(this).closest('table').find('.participantRow').length;
    $('#Row_Count1').val(rowCount);
    $(this).closest('.participantRow').find('input:text').val('');
    $(this).closest('.participantRow').find('input:hidden').val('');
    if (rowCount > 1) {
		  $(this).closest('.participantRow').remove();  
    } 
  });
}


function get_all_item(stritemarray){

  var RPRID          =  $("#RPRID").val();
  var PROID_REF      =  $("#PROID_REF").val();

  $("#tbody_ItemID").html('');  //clear for variable confliction
  $("#tbody_FGI").html('');  //clear for variable confliction
  $("#tbodyid").empty();
  $("#tbodyid").html('loading data...');    

  $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $.ajax({
      url:'{{route("transaction",[$FormId,"getAllItem"])}}',
      type:'POST',
      data:{'PROID_REF':PROID_REF,'item_array':stritemarray,'RPRID':RPRID},
      success:function(data) {
        console.log('cc==',data.totalrows);
        $("#Row_Count1").val(data.totalrows);              
        $("#tbodyid").html(data.matrows);              
                        
      },
      error:function(data){
        console.log("Error: Something went wrong.");
        $("#tbodyid").html('');                        
      },
  }); 



  //var  item_array   = [];
  // $('#example2').find('.participantRow').each(function(){
  //   var SLID_REF    = $(this).find('[id*="SLID_REF"]').val();
  //   var SOID_REF    = $(this).find('[id*="SOID_REF"]').val();
  //   var ITEMID_REF  = $(this).find('[id*="ITEMID_REF"]').val();
  //   var ITEMID_CODE = $(this).find('[id*="popupITEMID"]').val();
  //   var PD_OR_QTY   = $(this).find('[id*="PD_OR_QTY"]').val();
  //   var SQID_REF    = $(this).find('[id*="SQID_REF"]').val();
  //   var SEID_REF    = $(this).find('[id*="SEID_REF"]').val();

  //   item_array.push(SLID_REF+'_'+SOID_REF+'_'+ITEMID_REF+'_'+ITEMID_CODE+'_'+PD_OR_QTY+'_'+SQID_REF+'_'+SEID_REF);
  // });

  // $("#material_item").html('loading..');
  // $.ajaxSetup({
  //     headers: {
  //       'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  //     }
  // });

  // $.ajax({
  //     url:'{{route("transaction",[$FormId,"get_materital_item"])}}',
  //     type:'POST',
  //     data:{
  //       'pro':item_array
  //       },
  //     success:function(data) {
  //       $("#material_item").html(data);                
  //     },
  //     error:function(data){
  //       console.log("Error: Something went wrong.");
  //       $("#material_item").html('');                        
  //     },
  // }); 


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
