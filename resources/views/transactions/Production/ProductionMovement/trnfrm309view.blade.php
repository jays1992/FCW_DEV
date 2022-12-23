@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
	<div class="row">
		<div class="col-lg-2"><a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Production & Movement<br/> (PNM)</a></div>

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


<form id="frm_trn_edit" onsubmit="return validateForm()"  method="POST" class="needs-validation"  >  
	<div class="container-fluid filter">
    <div class="inner-form" id="Header_Details" >

			<div class="row">
				<div class="col-lg-1 pl"><p>PNM No</p></div>
				<div class="col-lg-2 pl">
          <input type="hidden" name="PNMID" id="PNMID" value="{{ isset($objResponse)?$objResponse->PNMID:'' }}" class="form-control" readonly  autocomplete="off"   >
					<input {{$ActionStatus}} type="text" name="PNM_NO" id="PNM_NO" value="{{ isset($objResponse)?$objResponse->PNM_NO:'' }}" class="form-control mandatory" readonly  autocomplete="off"  style="text-transform:uppercase" autofocus >
					<span class="text-danger" id="ERROR_PNM_NO"></span>
				</div>
					  
				<div class="col-lg-1 pl"><p>PNM Date</p></div>
				<div class="col-lg-2 pl">
					<input {{$ActionStatus}} type="date" name="PNM_DT" id="PNM_DT" value="{{ isset($objResponse)?$objResponse->PNM_DT:'' }}" class="form-control mandatory"  placeholder="dd/mm/yyyy" maxlength="10" >
					<span class="text-danger" id="ERROR_PNM_DT"></span>
				</div>

        <div class="col-lg-1 pl"><p>PRO NO</p></div>
        <div class="col-lg-2 pl">
            <input {{$ActionStatus}} type="text" name="PROID_REF_popup" id="txt_PROID_REF_popup" value="{{ isset($objResponse)?$objResponse->PRO_NO:'' }}" class="form-control mandatory"  autocomplete="off" readonly/>
            <input type="hidden" name="PROID_REF" id="PROID_REF" value="{{ isset($objResponse)?$objResponse->PROID_REF:'' }}" class="form-control" autocomplete="off" />
        </div>

        <div class="col-lg-1 pl"><p>ITEM</p></div>
        <div class="col-lg-2 pl">
            <input {{$ActionStatus}} type="text" name="txt_ITEM_popup" id="txt_ITEM_popup" value="{{ isset($objResponse)?$objResponse->ICODE:'' }}-{{ isset($objResponse)?$objResponse->NAME:'' }}" class="form-control mandatory"  autocomplete="off" readonly/>
            <input type="hidden" name="ITEMID_REF" id="ITEMID_REF" value="{{ isset($objResponse)?$objResponse->ITEMID_REF:'' }}" class="form-control" autocomplete="off" />
        </div>
			</div>

      <div class="row">
        <div class="col-lg-1 pl"><p>UOM</p></div>
        <div class="col-lg-2 pl">
            <input {{$ActionStatus}} type="text" name="txt_UOM_popup" id="txt_UOM_popup" value="{{ isset($objResponse)?$objResponse->UOMCODE:'' }} - {{ isset($objResponse)?$objResponse->UOMDESC:'' }}" class="form-control mandatory"  autocomplete="off" readonly/>
            <input type="hidden" name="UOMID_REF" id="UOMID_REF" value="{{ isset($objResponse)?$objResponse->UOMID_REF:'' }}" class="form-control" autocomplete="off" />
        </div>

        <div class="col-lg-1 pl"><p>PNM QTY</p></div>
        <div class="col-lg-2 pl">
            <input {{$ActionStatus}} type="text" name="PNM_QTY" id="PNM_QTY" value="{{ isset($objResponse)?$objResponse->PNM_QTY:'' }}" class="form-control"  onkeyup="get_materital_item();"  autocomplete="off" />
            <input type="hidden" name="TOTAL_QTY" id="TOTAL_QTY" value="{{ isset($objResponse)?$objResponse->ACTUAL_QTY:'' }}" />
            <input type="hidden" name="SOID_REF" id="SOID_REF" value="{{ isset($objResponse)?$objResponse->SOID_REF:'' }}" />
            <input type="hidden" name="SQID_REF" id="SQID_REF" value="{{ isset($objResponse)?$objResponse->SQID_REF:'' }}" />
            <input type="hidden" name="SEID_REF" id="SEID_REF" value="{{ isset($objResponse)?$objResponse->SEID_REF:'' }}" />
        </div>
		<div class="col-lg-1 pl"><p>Tolerence</p></div>
        <div class="col-lg-2 pl">
            <input {{$ActionStatus}} type="checkbox" name="PARTIAL_PRODUCTION" id="PARTIAL_PRODUCTION" class="form-checkbox" {{isset($objResponse->PARTIAL_PRODUCTION) && $objResponse->PARTIAL_PRODUCTION == 1 ? 'checked' : ''}} />            
        </div>
		
		<div class="col-lg-1 pl"><p>Actual QTY</p></div>
        <div class="col-lg-2 pl">
           <input {{$ActionStatus}} type="text" name="ACTUAL_QTY" id="ACTUAL_QTY" value="{{ isset($objResponse)?$objResponse->ACTUAL_QTY:'' }}" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" onChange="getQtyCheck();" maxlength="13" autocomplete="off" {{isset($objResponse->PARTIAL_PRODUCTION) && $objResponse->PARTIAL_PRODUCTION == 1 ? '' : 'readonly'}}  />           
        </div>
      </div>
      <div class="row">
		<div class="col-lg-6 pl">

          <div class="col-lg-3 pl"><p>To Stage</p></div>
          <div class="col-lg-1 pl">
            <input {{$ActionStatus}} type="radio" NAME="MOVEMENT_STAGE_TYPE" {{isset($objResponse) && $objResponse->MOVEMENT_STAGE =='TO STAGE'?'checked':'' }}  onChange="getMovementState('To Stage')" value="TO STAGE" >
          </div>
          
          <div class="col-lg-3 pl"><p>To QC</p></div>
          <div class="col-lg-1 pl">
            <input {{$ActionStatus}} type="radio" NAME="MOVEMENT_STAGE_TYPE" {{isset($objResponse) && $objResponse->MOVEMENT_STAGE =='TO QC'?'checked':'' }} onChange="getMovementState('To QC')" value="TO QC"  >
          </div>

          <div class="col-lg-3 pl"><p>To Store</p></div>
          <div class="col-lg-1 pl">
            <input {{$ActionStatus}} type="radio" NAME="MOVEMENT_STAGE_TYPE" {{isset($objResponse) && $objResponse->MOVEMENT_STAGE =='TO STORE'?'checked':'' }} onChange="getMovementState('To Store')" value="TO STORE"  >
            <input type="hidden" name="MOVEMENT_STAGE" id="MOVEMENT_STAGE" value="{{ isset($objResponse)?$objResponse->MOVEMENT_STAGE:'' }}" >
          </div>
        
        </div>

        <div class="col-lg-1 pl"><p>From Stage</p></div>
        <div class="col-lg-2 pl">
            <input {{$ActionStatus}} type="text" name="PSTAGEID_REF_popup" id="txt_PSTAGEID_REF_popup" value="{{ isset($objResponse)?$objResponse->FROM_STAGE_CODE:'' }} - {{ isset($objResponse)?$objResponse->FROM_STAGE_DESC:'' }}" class="form-control mandatory"  autocomplete="off" readonly/>
            <input type="hidden" name="PSTAGEID_REF" id="PSTAGEID_REF" value="{{ isset($objResponse)?$objResponse->PSTAGEID_REF:'' }}" class="form-control" autocomplete="off" />
        </div>

        <div class="col-lg-1 pl"><p id="STAGE_NAME">To Stage</p></div>
        <div class="col-lg-2 pl">
            <?php if(isset($objResponse) && $objResponse->MOVEMENT_STAGE =="TO STORE"){?>
            <input {{$ActionStatus}} type="text" name="TO_STAGE_STORE_QC_popup" id="txt_TO_STAGE_STORE_QC_popup" value="{{ isset($objResponse)?$objResponse->TO_STORE_CODE:'' }} - {{ isset($objResponse)?$objResponse->TO_STORE_DESC:'' }}" class="form-control mandatory"  autocomplete="off" readonly/>
            <?php }else{?>
              <input {{$ActionStatus}} type="text" name="TO_STAGE_STORE_QC_popup" id="txt_TO_STAGE_STORE_QC_popup" value="{{ isset($objResponse)?$objResponse->TO_STAGE_CODE:'' }} - {{ isset($objResponse)?$objResponse->TO_STAGE_DESC:'' }}" class="form-control mandatory"  autocomplete="off" readonly/>
            <?php }?>
            
            <input type="hidden" name="TO_STAGE_STORE_QC" id="TO_STAGE_STORE_QC" value="{{ isset($objResponse)?$objResponse->TO_STAGE_STORE_QC:'' }}" class="form-control" autocomplete="off" />
        </div>
	  </div>
      <div class="row">

        <div class="col-lg-1 pl"><p>Total Cost</p></div>
        <div class="col-lg-2 pl">
            <input {{$ActionStatus}} type="text" name="TOTAL_COST" id="TOTAL_COST" value="{{ isset($objResponse)?$objResponse->TOTAL_COST:'' }}" class="form-control mandatory"  autocomplete="off" readonly/>
           
        </div>
       
      </div>




		</div>

		<div class="container-fluid">

			<div class="row">
				<ul class="nav nav-tabs">
					<li class="active"><a data-toggle="tab" href="#Material">Material</a></li>
          <li><a data-toggle="tab" href="#ByProduct" id="BP_TAB" >By Product</a></li>
          <li><a data-toggle="tab" href="#AdditionalMaterial" id="AD_TAB" >Additional Material Issue</a></li>
					<li><a data-toggle="tab" href="#udf">UDF</a></li>
          <li><a data-toggle="tab" href="#direct_cost" id="ODC_TAB" >Other Direct Cost	</a></li>
				</ul>
			
				<div class="tab-content">

					<div id="Material" class="tab-pane fade in active">
						<div class="table-responsive table-wrapper-scroll-y" style="margin-top:10px;" >
							<table id="example2" class="display nowrap table table-striped table-bordered itemlist w-200" width="100%" style="height:auto !important;">
								<thead id="thead1"  style="position: sticky;top: 0">
                  <tr>
                    <th hidden ><input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1" value="{{count($objMAT)}}" ></th>
                    <th>Machine</th>
                    <th>Shift</th>
                    <th>Operator</th>
                    <th>Batch No</th>
                    <th>Qty</th>
                    <th>Action</th>
								  </tr>
								</thead>
								<tbody>
								@if(isset($objMAT) && !empty($objMAT))
								@foreach($objMAT as $key => $row)

                  <tr  class="participantRow">
                    <td hidden> <input type="hidden" id="{{$key}}" > </td>
                    
                    <td><input {{$ActionStatus}} type="text" name="txt_MACHINE_popup_{{$key}}" id="txt_MACHINE_popup_{{$key}}" value="{{$row->MACHINE_NO}} - {{$row->MACHINE_DESC}}" class="form-control"  autocomplete="off"  readonly /></td>
                    <td hidden><input type="text" name="MACHINEID_REF_{{$key}}" id="MACHINEID_REF_{{$key}}" value="{{$row->MACHINEID_REF}}" class="form-control" autocomplete="off" /></td>

                    <td><input {{$ActionStatus}} type="text" name="txt_SHIFT_popup_{{$key}}"   id="txt_SHIFT_popup_{{$key}}" value="{{$row->SHIFT_CODE}} - {{$row->SHIFT_NAME}}" class="form-control"  autocomplete="off"  readonly /></td>
                    <td hidden><input type="text" name="SHIFTID_REF_{{$key}}"       id="SHIFTID_REF_{{$key}}"  value="{{$row->SHIFTID_REF}}" class="form-control" autocomplete="off" /></td>

                    <td><input {{$ActionStatus}} type="text" name="txt_EMP_popup_{{$key}}" id="txt_EMP_popup_{{$key}}" value="{{$row->EMPCODE}} - {{$row->FNAME}}" class="form-control"  autocomplete="off"  readonly /></td>
                    <td hidden><input type="text" name="EMPID_REF_{{$key}}" id="EMPID_REF_{{$key}}"  value="{{$row->EMPID_REF}}" class="form-control" autocomplete="off" /></td>
                    
                    <td><input {{$ActionStatus}} type="text" name="BATCH_{{$key}}" id="BATCH_{{$key}}"  value="{{$row->BATCH}}" class="form-control" autocomplete="off" onkeyup="checkBatchNo(this.id,this.value)" /></td>
                    <td><input {{$ActionStatus}} type="text" name="QTY_{{$key}}" id="QTY_{{$key}}"  value="{{$row->QTY}}" class="form-control" autocomplete="off" onkeyup="checkQty(this.id,this.value)" onkeypress="return isNumberDecimalKey(event,this)" /></td>

                    <td align="center" >
                      <button {{$ActionStatus}} class="btn add material" title="add" data-toggle="tooltip" type="button" ><i class="fa fa-plus"></i></button>
                      <button {{$ActionStatus}} class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button>
                    </td>
								  </tr>
								  
								@endforeach 
								@endif
								</tbody>
							</table>

							<div id="material_item">
								<?php
								if(!empty($material_array)){
									$Row_Count4 =   count($material_array);
									echo'<table id="example4" class="display nowrap table table-striped table-bordered itemlist w-200" width="100%" style="height:auto !important;">
											<thead id="thead1"  style="position: sticky;top: 0">
                      <tr>
                        <th hidden ><input class="form-control" type="hidden" name="Row_Count4" id ="Row_Count4" value="'.$Row_Count4.'"></th>
                        <th>Item Code</th>
                        <th>Item Description</th>
                        <th>UOM</th>
                        <th>Issued Qty</th>
                        <th>Lot No</th>
                        <th>Rate</th>
                        <th>Actual Consume Qty</th>
                        <th>Wastage/Scrap Qty</th>
                        <th>Balance Qty</th>
                        <th>Total</th>
                    </tr>
											</thead>
											<tbody>';

											foreach($material_array as $index=>$row_data){

                        $SFG_Wise_Readonly=$row_data->MATERIAL_TYPE=="SFG- Semi Finish Good"?"readonly":'';
            
                        echo '<tr  class="participantRow4">';
                        echo '<td><input '.$ActionStatus.' type="text" id="txtSUBITEM_popup_'.$index.'" class="form-control"  value="'.$row_data->ICODE.'"  readonly /></td>';
                        echo '<td hidden><input type="text" name="REQ_ITEMID_REF_'.$index.'" id="REQ_ITEMID_REF_'.$index.'" value="'.$row_data->ITEMID_REF.'" /></td>';
                        echo '<td><input '.$ActionStatus.' type="text" name="REQ_ITEM_DESCRIPTION_'.$index.'" id="REQ_ITEM_DESCRIPTION_'.$index.'" class="form-control" value="'.$row_data->ITEM_DESCRIPTION.'" readonly /></td>';

                        echo '<td><input '.$ActionStatus.' type="text" name="txtUOM_popup_'.$index.'"         id="txtUOM_popup_'.$index.'"   value="'.$row_data->UOMDESC.'" class="form-control" readonly /></td>';
                        echo '<td hidden><input type="text" name="REQ_UOMID_REF_'.$index.'" id="REQ_UOMID_REF_'.$index.'"  value="'.$row_data->UOMID_REF.'" class="form-control" /></td>';
                        
                        echo '<td><input '.$ActionStatus.' type="text" name="REQ_CONSUME_QTY_'.$index.'" id="REQ_CONSUME_QTY_'.$index.'" class="form-control" autocomplete="off" value="'.number_format($row_data->CONSUME_QTY,3,".","").'"  onkeypress="return isNumberDecimalKey(event,this)" readonly  /></td>';
                        echo '<td><input '.$ActionStatus.' type="text" name="REQ_LOT_NO_'.$index.'"   id="REQ_LOT_NO_'.$index.'" value="'.$row_data->BATCH.'" class="form-control" /></td>';
                        echo '<td><input '.$ActionStatus.' type="text" name="RATE_'.$index.'"   id="RATE_'.$index.'" value="'.number_format($row_data->RATE,5).'" class="form-control" readonly /></td>';
                        echo '<td><input '.$ActionStatus.' type="text" name="REQ_CHANGE_IN_CONSUME_QTY_'.$index.'" id="REQ_CHANGE_IN_CONSUME_QTY_'.$index.'" class="form-control three-digits" value="'.number_format($row_data->CHANGE_IN_CONSUME_QTY,3,".","").'" onkeypress="return isNumberDecimalKey(event,this)" onkeyup="calculateRateQty()" onfocusout="resetValue(this.id,this.value,3)" maxlength="13"  autocomplete="off" '.$SFG_Wise_Readonly.' /></td>';
                        echo '<td><input '.$ActionStatus.' type="text" name="TOTAL_AMOUNT_'.$index.'"   id="TOTAL_AMOUNT_'.$index.'"  class="form-control" readonly /></td>';
                        echo '</tr>';

											}
											
										echo '</tbody>';
									echo'</table>';
								}
								else{
									echo "Record not found.";
								}
								
								?>
							</div>

						</div>	
					</div>



          <div id="ByProduct" class="tab-pane fade in ">
              <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:280px;margin-top:10px;"  id="GetByProductItems">                                       
           
            </div>	
          </div>  


          <div id="AdditionalMaterial" class="tab-pane fade in ">
              <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:280px;margin-top:10px;"  id="GetAdditionalMaterialItems">                                       
             
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
								<td><input {{$ActionStatus}} type="text" name={{"popupSEID_".$uindex}} id={{"popupSEID_".$uindex}} class="form-control" value="{{$uRow->UDFPNMID_REF}}" autocomplete="off"  readonly/></td>
								<td hidden><input type="hidden" name={{"UDF_".$uindex}} id={{"UDF_".$uindex}} class="form-control" value="{{$uRow->UDFPNMID_REF}}" autocomplete="off"   /></td>
								<td hidden><input type="hidden" name={{"UDFismandatory_".$uindex}} id={{"UDFismandatory_".$uindex}} value="{{$uRow->UDFPNMID_REF}}" class="form-control"   autocomplete="off" /></td>
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

        <div id="direct_cost" class="tab-pane fade">
          <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="margin-top:10px;height:280px;width:50%;" id="other_direct_cost" >
            <?php
            $Row_Count5 =   !empty($objOTH)?count($objOTH):'1';

            echo'<table id="example6" class="display nowrap table table-striped table-bordered itemlist" style="height:auto !important;">
                    <thead id="thead5"  style="position: sticky;top: 0">
                        <tr>
                            <th hidden ><input class="form-control" type="hidden" name="Row_Count5" id ="Row_Count5" value="'.$Row_Count5.'"></th>
                            <th>Cost Component(s)</th>
                            <th>Value</th>                            
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>';
    
                    if(!empty($objOTH)){
                        foreach($objOTH as $key => $row){
    
                            echo'<tr class="participantRow10">
                                    <td hidden><input  class="form-control" type="hidden" name="BOM_OTHID_'.$key.'" id ="BOM_OTHID_'.$key.'" maxlength="100" value="'.$row->PNM_OTHID.'" autocomplete="off"></td>
                                    <td><input '.$ActionStatus.'  type="text" name="Componentname_'.$key.'" id="Componentname_'.$key.'" value="'.$row->CCOMPONENT_CODE.'-'.$row->DESCRIPTIONS.'"   onclick="get_item_component(this.id)" class="form-control"  autocomplete="off"  readonly/></td>                                                
                                    <td hidden><input type="hidden" name="Componentid_'.$key.'" id="Componentid_'.$key.'" maxlength="100" value="'.$row->CCOMPONENTID_REF.'"   class="form-control" autocomplete="off" /><input type="text" name="rowscount5[]"  /></td>
                                    <td><input '.$ActionStatus.'  type="text" name="value_'.$key.'" id="value_'.$key.'" maxlength="100" value="'.$row->VALUE.'"  class="form-control"  autocomplete="off" onkeyup="calculateRateQty()" onfocusout="resetValue(this.id,this.value,2)"  /></td>
                                    <td align="center" ><button '.$ActionStatus.'  class="btn add dcmaterial" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button><button '.$ActionStatus.'  class="btn remove dcmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
                                </tr>';
                        }
                    }
                    else{
                        echo'<tr class="participantRow10">
                                <td hidden><input  class="form-control" type="hidden" name="BOM_OTHID_0" id ="BOM_OTHID_0" maxlength="100"  autocomplete="off"></td>
                                <td><input  type="text" name="Componentname_0" id="Componentname_0"    onclick="get_item_component(this.id)" class="form-control"  autocomplete="off"  readonly/></td>                                                
                                <td hidden><input type="hidden" name="Componentid_0" id="Componentid_0" maxlength="100"   class="form-control" autocomplete="off" /><input type="text" name="rowscount5[]"  /></td>
                                <td><input  type="text" name="value_0" id="value_0" maxlength="100"   class="form-control"  autocomplete="off" onkeyup="calculateRateQty()" onfocusout="resetValue(this.id,this.value,2)"  /></td>
                                <td align="center" ><button  class="btn add dcmaterial" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button><button  class="btn remove dcmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
                            </tr>'; 
                    }
    
                   
    
                echo '</tbody>';
            echo'</table>';
          ?>
                
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


<!--Direct cost tab component popup starts-->

<!-- production stage Dropdown -->
<div id="component_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='component_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Component List</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="component_table1" class="display nowrap table  table-striped table-bordered" >

        <thead>
          <tr id="none-select" class="searchalldata" hidden>            
                <td > 
                  <input type="text" name="compfieldid" id="hdn_CompItemID"/>
                  <input type="text" name="compfieldid2" id="hdn_CompItemID2"/>
                  <input type="text" name="compfieldid3" id="hdn_CompItemID3"/>
                </td>
          </tr>
          <tr>
            <th class="ROW1" >Select</th> 
            <th class="ROW2" >Code</th>
            <th  class="ROW3">Name</th>
          </tr>
        </thead>
        <tbody>

        <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="component_codesearch" class="form-control" onkeyup="searchComponentCode()"></td>
        <td class="ROW3"><input type="text" id="component_namesearch" class="form-control" onkeyup="searchComponentName()"></td>
      </tr>
        </tbody>
      </table>


      <table id="component_table2" class="display nowrap table  table-striped table-bordered" width="100%">
      <thead id="thead2">
          
          </thead>
        <tbody id="component_body">
        @foreach ($component_list as $index=>$componentRow)
        <tr id="componentcode_{{ $index }}">
          <td  class="ROW1" style="text-align: center"> <input type="checkbox" name="SELECT_COMPLIST_REF[]"  id="chkcomponentcode_{{ $index }}" class="componentcls" value="{{ $componentRow->CCOMPONENTID }}" ></td>
          <td class="ROW2">{{ $componentRow-> CCOMPONENT_CODE }}
          <input type="hidden" id="txtcomponentcode_{{ $index }}" data-desc="{{ $componentRow-> CCOMPONENT_CODE }} - {{ $componentRow-> DESCRIPTIONS }}"  
          value="{{ $componentRow->CCOMPONENTID }}"/></td>
          <td  class="ROW3">{{ $componentRow-> DESCRIPTIONS }}</td>
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

<!--Direct cost tab component popup ends-->

<div id="PROID_REF_POPUP" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='PRO_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>PRO No</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="PROCodeTable" class="display nowrap table  table-striped table-bordered" >
    <thead>

    <tr>
      <th class="ROW1">Select</th> 
      <th class="ROW2">PRO No</th>
      <th class="ROW3">PRO Date</th>
    </tr>
    </thead>
    <tbody>

    <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="PROcodesearch" class="form-control" onkeyup="PROCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="PROnamesearch" class="form-control" onkeyup="PRONameFunction()"></td>
      </tr>

    </tbody>
    </table>
      <table id="PROCodeTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2">          
        </thead>
        <tbody>
        @foreach ($objPRO as $key=>$val)
        <tr>
          <td class="ROW1"> <input type="checkbox" name="SELECT_PROID_REF[]" id="PROidcode_{{ $key }}" class="clsPROid" value="{{ $val-> PROID }}" ></td>
          <td class="ROW2">{{ $val-> PRO_NO }} <input type="hidden" id="txtPROidcode_{{ $key }}" data-desc="{{ $val-> PRO_NO }}"  value="{{ $val-> PROID }}"/></td>
          <td class="ROW3">{{ $val-> PRO_DT }}</td>
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


<div id="FROM_STAGE_POPUP" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='FS_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>From Stage</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="FSCodeTable" class="display nowrap table  table-striped table-bordered" >
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
        <td class="ROW2"><input type="text" id="FScodesearch" class="form-control" onkeyup="FSCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="FSnamesearch" class="form-control" onkeyup="FSNameFunction()"></td>
      </tr>

    </tbody>
    </table>
      <table id="FSCodeTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2">          
        </thead>
        <tbody>
        @foreach ($objStage as $key=>$val)
        <tr >
          <td class="ROW1"> <input type="checkbox" name="SELECT_PSTAGEID_REF[]" id="FSidcode_{{ $key }}" class="clsFSid" value="{{ $val-> PSTAGEID }}" ></td>
          <td class="ROW2">{{ $val-> PSTAGE_CODE }} <input type="hidden" id="txtFSidcode_{{ $key }}" data-desc="{{ $val-> PSTAGE_CODE }} - {{ $val-> DESCRIPTIONS }}"  value="{{ $val-> PSTAGEID }}"/></td>
          <td class="ROW3">{{ $val-> DESCRIPTIONS }}</td>
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

<div id="MACHINE_POPUP" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header"><button type="button" class="close" data-dismiss="modal" id='MACHINE_CLOSE_POPUP' >&times;</button></div>
      <div class="modal-body">
        <div class="tablename"><p>MACHINE</p></div>
        <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
          <table id="MACHINE_TABLE" class="display nowrap table  table-striped table-bordered"  style="font-size:14px;">
            <thead>
              <tr id="none-select" class="searchalldata" hidden>
                <td> 
                  <input type="hidden" id="HIDDEN_MACHINE_ID"/>
                  <input type="hidden" id="HIDDEN_MACHINE_ID2"/>
                </td>
              </tr>
              
             
              <tr>
                <th class="ROW1">Select</th> 
                <th class="ROW2">Code</th>
                <th class="ROW3">NAME</th>
              </tr>
            </thead>

            <tbody>

              <tr>
                <th class="ROW1"><span class="check_th">&#10004;</span></th>
                <td class="ROW2"><input type="text" id="MACHINE_CODE_SEARCH" class="form-control" onkeyup="MACHINE_CODE_FUNCTION()"></td>
                <td class="ROW3"><input type="text" id="MACHINE_NAME_SEARCH" class="form-control" onkeyup="MACHINE_NAME_FUNCTION()"></td>
              </tr>
              
            </tbody>
          </table>

          <table id="MACHINE_TABLE2" class="display nowrap table  table-striped table-bordered"  style="font-size:14px;" >
            <thead id="thead2"></thead>
            <tbody id="TBODY_MACHINE">    
              @if(!empty(isset($objMACHINE) && $objMACHINE))
                @foreach($objMACHINE as $key=>$val)
                  <tr >
                    <td class="ROW1"> <input type="checkbox" name="SELECT_MACHINEID_REF[]" id="MACHINE_CODE_{{$val->ID}}"  class="CLASS_MACHINE" value="{{$val->ID}}" ></td>
                    <td class="ROW2" >{{$val->CODE}} </td>
                    <td class="ROW3">{{$val->DESC}}</td>
                    <td hidden><input type="text" id="txtMACHINE_CODE_{{$val->ID}}" data-desc="{{$val->CODE}} - {{$val->DESC}}" value="{{$val->ID}}"/></td>
                  </tr>
                @endforeach
              @else
              <tr><td colspan="2">Record not found.</td></tr>
              @endif 
            </tbody>
          </table>
        </div>
		    <div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<div id="SHIFT_POPUP" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header"><button type="button" class="close" data-dismiss="modal" id='SHIFT_CLOSE_POPUP' >&times;</button></div>
      <div class="modal-body">
        <div class="tablename"><p>SHIFT</p></div>
        <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
          <table id="SHIFT_TABLE" class="display nowrap table  table-striped table-bordered"  style="font-size:14px;">
            <thead>
              <tr id="none-select" class="searchalldata" hidden>
                <td> 
                  <input type="hidden" id="HIDDEN_SHIFT_ID"/>
                  <input type="hidden" id="HIDDEN_SHIFT_ID2"/>
                </td>
              </tr>
              
            
              <tr>
                <th class="ROW1">Select</th> 
                <th class="ROW2">Code</th>
                <th class="ROW3">NAME</th>
              </tr>
            </thead>

            <tbody>
             
              <tr>
                <th class="ROW1"><span class="check_th">&#10004;</span></th>
                <td class="ROW2"><input type="text" id="SHIFT_CODE_SEARCH" class="form-control" onkeyup="SHIFT_CODE_FUNCTION()"></td>
                <td class="ROW3"><input type="text" id="SHIFT_NAME_SEARCH" class="form-control" onkeyup="SHIFT_NAME_FUNCTION()"></td>
              </tr>

            </tbody>
          </table>

          <table id="SHIFT_TABLE2" class="display nowrap table  table-striped table-bordered" style="font-size:14px;" >
            <thead id="thead2"></thead>
            <tbody id="TBODY_SHIFT">    
              @if(!empty(isset($objSHIFT) && $objSHIFT))
                @foreach($objSHIFT as $key=>$val)
                  <tr >
                    <td class="ROW1"> <input type="checkbox" name="SELECT_SHIFTID_REF[]" id="SHIFT_CODE_{{$val->ID}}"  class="CLASS_SHIFT" value="{{$val->ID}}" ></td>
                    <td class="ROW2">{{$val->CODE}} </td>
                    <td class="ROW3">{{$val->DESC}}</td>
                    <td hidden><input type="text" id="txtSHIFT_CODE_{{$val->ID}}" data-desc="{{$val->CODE}} - {{$val->DESC}}" value="{{$val->ID}}"/></td>
                  </tr>
                @endforeach
              @else
              <tr><td colspan="2">Record not found.</td></tr>
              @endif 
            </tbody>
          </table>
        </div>
		    <div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<div id="EMP_OPEN_POPUP" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header"><button type="button" class="close" data-dismiss="modal" id='EMP_CLOSE_POPUP' >&times;</button></div>
      <div class="modal-body">
        <div class="tablename"><p>OPERATOR</p></div>
        <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
          <table id="EMP_TABLE" class="display nowrap table  table-striped table-bordered" style="font-size:14px;">
            <thead>
              <tr id="none-select" class="searchalldata" hidden>
                <td> 
                  <input type="hidden" id="HIDDEN_EMP_ID"/>
                  <input type="hidden" id="HIDDEN_EMP_ID2"/>
                </td>
              </tr>
              
              <tr>
                <th class="ROW1">Select</th> 
                <th class="ROW2">Code</th>
                <th class="ROW3">NAME</th>
              </tr>
            </thead>

            <tbody>
             
              <tr>
                <th class="ROW1"><span class="check_th">&#10004;</span></th>
                <td class="ROW2"><input type="text" id="EMP_CODE_SEARCH" class="form-control" onkeyup="EMP_CODE_FUNCTION()"></td>
                <td class="ROW3"><input type="text" id="EMP_NAME_SEARCH" class="form-control" onkeyup="EMP_NAME_FUNCTION()"></td>
              </tr>

            </tbody>
          </table>

          <table id="EMP_TABLE2" class="display nowrap table  table-striped table-bordered"  style="font-size:14px;" >
            <thead id="thead2"></thead>
            <tbody id="TBODY_EMP">    
              @if(!empty(isset($objEMP) && $objEMP))
                @foreach($objEMP as $key=>$val)
                  <tr >
                    <td class="ROW1"> <input type="checkbox" name="SELECT_EMPID_REF[]" id="EMP_CODE_{{$val->ID}}"  class="CLASS_EMP" value="{{$val->ID}}" ></td>
                    <td class="ROW2">{{$val->CODE}} </td>
                    <td class="ROW3">{{$val->DESC}}</td>
                    <td hidden><input type="text" id="txtEMP_CODE_{{$val->ID}}" data-desc="{{$val->CODE}} - {{$val->DESC}}" value="{{$val->ID}}"/></td>
                  </tr>
                @endforeach
              @else
              <tr><td colspan="2">Record not found.</td></tr>
              @endif 
            </tbody>
          </table>
        </div>
		    <div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<div id="SOpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='SO_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p id="POPUP_STAGE_NAME">To Stage</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="SOTable" class="display nowrap table  table-striped table-bordered" style="font-size:14px;" >
    <thead>
          <tr id="none-select" class="searchalldata" hidden>
            
            <td> 
            <input type="hidden"  id="hdn_SOid"/>
            <input type="hidden" id="hdn_SOid2"/>
            <input type="hidden" id="hdn_SOid3"/>
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
        <td class="ROW2"><input type="text" id="SOcodesearch" class="form-control" onkeyup="SOCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="SOnamesearch" class="form-control" onkeyup="SONameFunction()"></td>
      </tr>

    </tbody>
    </table>
      <table id="SOTable2" class="display nowrap table  table-striped table-bordered"  style="font-size:14px;" >
        <thead id="thead2">

        </thead>
        <tbody id="tbody_SO">     
        
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<div id="ITEMIDpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:90%">
    <div class="modal-content" >
      <div class="modal-header"><button type="button" class="close" data-dismiss="modal" id='ITEMID_closePopup' >&times;</button></div>
      <div class="modal-body">
	      <div class="tablename"><p>Item Details</p></div>
	      <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
          <table id="ItemIDTable" class="display nowrap table  table-striped table-bordered" style="width:100%" >
          <thead>
              <tr>
                <th style="width:8%;" id="all-check">Select</th>
                <th style="width:10%;">Item Code</th>
                <th style="width:10%;">Name</th>
                <th style="width:8%;">Main UOM</th>
                <th style="width:8%;">Qty</th>
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
                <td style="width:8%;"></td>
                <td style="width:10%;"><input type="text" id="Itemcodesearch" class="form-control" onkeyup="ItemCodeFunction()"></td>
                <td style="width:10%;"><input type="text" id="Itemnamesearch" class="form-control" onkeyup="ItemNameFunction()"></td>
                <td style="width:8%;"><input type="text" id="ItemUOMsearch" class="form-control" onkeyup="ItemUOMFunction()"></td>
                <td style="width:8%;"><input type="text" id="ItemQTYsearch" class="form-control" onkeyup="ItemQTYFunction()"></td>
                <td style="width:8%;"><input type="text" id="ItemGroupsearch" class="form-control" onkeyup="ItemGroupFunction()"></td>
                <td style="width:8%;"><input type="text" id="ItemCategorysearch" class="form-control" onkeyup="ItemCategoryFunction()"></td>
                
                <td style="width:8%;"><input type="text" id="ItemBUsearch" class="form-control" onkeyup="ItemBUFunction()"></td>
                <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemAPNsearch" class="form-control" onkeyup="ItemAPNFunction()"></td>
                <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemCPNsearch" class="form-control" onkeyup="ItemCPNFunction()"></td>
                <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemOEMPNsearch" class="form-control" onkeyup="ItemOEMPNFunction()"></td>

                <td style="width:8%;"><input  type="text" id="ItemStatussearch" class="form-control" onkeyup="ItemStatusFunction()"></td>
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
/*================================== READY FUNCTION ==================================*/

$(document).ready(function(e) {
  var Material = $("#Material").html(); 
  $('#hdnmaterial').val(Material);
  // var lastdt = <?php echo json_encode($objResponse->PNM_DT); ?>;
  // var today = new Date(); 
  // var prodate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
  // $('#PNM_DT').attr('min',lastdt);
  // $('#PNM_DT').attr('max',prodate);

  var lastdt = <?php echo json_encode($objlastdt[0]->PNM_DT); ?>;
  var pnm = <?php echo json_encode($objResponse); ?>;
  var today = new Date(); 
  var sodate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2);
  if(lastdt < pnm.PNM_DT)
  {
	$('#PNM_DT').attr('min',lastdt);
  }
  else
  {
	  $('#PNM_DT').attr('min',pnm.PNM_DT);
  }
  $('#PNM_DT').attr('max',sodate);

  

  var d = new Date(); 
  var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
  //$('#PNM_DT').val(today);

  //getMovementState('To Stage');

});

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
  var formReqData = $("#frm_trn_edit");
  if(formReqData.valid()){
    validateForm('fnSaveData','update');
  }
});

$("#btnApprove").click(function(){
  var formReqData = $("#frm_trn_edit");
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
	
	if($("#FocusId").val() !=''){
		var FocusId=$("#FocusId").val();
		$("#"+FocusId).focus();
	}
	
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

  var trnFormReq  = $("#frm_trn_edit");
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

  var trnFormReq  = $("#frm_trn_edit");
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
  var PNM_NO            = $.trim($("#PNM_NO").val());
  var PNM_DT            = $.trim($("#PNM_DT").val()); 
  var PROID_REF         = $.trim($("#PROID_REF").val());
  var ITEMID_REF        = $.trim($("#ITEMID_REF").val()); 
  var UOMID_REF         = $.trim($("#UOMID_REF").val());
  var PNM_QTY           = $.trim($("#PNM_QTY").val()); 
  var ACTUAL_QTY        = $.trim($("#ACTUAL_QTY").val());
  var PSTAGEID_REF      = $.trim($("#PSTAGEID_REF").val());
  var TO_STAGE_STORE_QC = $.trim($("#TO_STAGE_STORE_QC").val());
  
	if ($('#PARTIAL_PRODUCTION').is(":checked") == false){
		  $('#ACTUAL_QTY').val(PNM_QTY);
		  $('#ACTUAL_QTY').prop('readonly','true');
		  event.preventDefault();
	}
	else
	{
		  getQtyCheck();
		  $('#ACTUAL_QTY').removeAttr('readonly');
		  event.preventDefault();
	}

  if(PNM_NO ===""){
      $("#FocusId").val('PNM_NO');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('PRO No is required.');
      $("#alert").modal('show')
      $("#OkBtn1").focus();
      return false;
  }
  else if(PNM_DT ===""){
      $("#FocusId").val('PNM_DT');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please select PRO Date.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  }
  else if(PROID_REF ===""){
      $("#FocusId").val('txt_PROID_REF_popup');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please select PRO No.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  }  
  else if(ITEMID_REF ===""){
      $("#FocusId").val('txt_ITEM_popup');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please select item.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  }  
  else if(UOMID_REF ===""){
      $("#FocusId").val('txt_UOM_popup');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('UOM should not empty.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  }
  else if(PNM_QTY ===""){
      $("#FocusId").val('PNM_QTY');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please enter PNM Qty.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(PSTAGEID_REF ===""){
      $("#FocusId").val('txt_PSTAGEID_REF_popup');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please select from stage.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  }  
  else if(TO_STAGE_STORE_QC ===""){
      $("#FocusId").val('txt_TO_STAGE_STORE_QC_popup');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please select to stage.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  }   
  else{
    event.preventDefault();
    var RackArray   = []; 
    var allblank1   = [];
    var allblank2   = [];
    var allblank3   = [];
    var allblank4   = [];
    var allblank5   = [];
    var focustext   = "";
      
    $('#example2').find('.participantRow').each(function(){

      var exist=$.trim($(this).find("[id*=MACHINEID_REF]").val())+'-'+$.trim($(this).find("[id*=SHIFTID_REF]").val())+'-'+$.trim($(this).find("[id*=EMPID_REF]").val())+'-'+$.trim($(this).find("[id*=BATCH]").val());

      if($.trim($(this).find("[id*=BATCH]").val()) ===""){
        allblank1.push('false');
        focustext = $(this).find("[id*=BATCH]").attr('id');
      }
      else if($.trim($(this).find("[id*=QTY]").val()) ===""){
        allblank2.push('false');
        focustext = $(this).find("[id*=QTY]").attr('id');
      }
      else if(parseFloat($.trim($("#TOTAL_QTY").val())) != parseFloat($.trim($("#ACTUAL_QTY").val())) ){
        allblank3.push('false');
        focustext = $(this).find("[id*=ACTUAL_QTY]").attr('id');
      }
      else if(RackArray.indexOf(exist) > -1){
        allblank4.push('false');
        focustext = $(this).find("[id*=BATCH]").attr('id');
      }
      else{
        allblank1.push('true');
        allblank2.push('true');
        allblank3.push('true');
        allblank4.push('true');
        focustext   = "";
      }

      RackArray.push(exist);

    });

    $('#example3').find('.participantRow3').each(function(){
      if($.trim($(this).find("[id*=UDF]").val())!=""){
        if($.trim($(this).find("[id*=UDFismandatory]").val())=="1"){
          if($.trim($(this).find('[id*="udfvalue"]').val()) != ""){
            allblank5.push('true');
            focustext   = "";
          }
          else{
            allblank5.push('false');
            focustext   = "";
          }
        }  
      }                
    });
    
    if(jQuery.inArray("false", allblank1) !== -1){
      $("#FocusId").val(focustext);
      $("#alert").modal('show');
      $("#AlertMessage").text('Please enter Batch No In Material.');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank2) !== -1){
      $("#FocusId").val(focustext);
      $("#alert").modal('show');
      $("#AlertMessage").text('Please enter Qty In Material');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank3) !== -1){
      $("#FocusId").val(focustext);
      $("#alert").modal('show');
      $("#AlertMessage").text('Actual Production Qty does not equal to Material Qty and By Product Consume Qty.');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank4) !== -1){
      $("#FocusId").val(focustext);
      $("#alert").modal('show');
      $("#AlertMessage").text('Duplicate Batch No not allow.');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank5) !== -1){
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

  var trnFormReq  = $("#frm_trn_edit");
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
              showError('ERROR_PNM_NO',data.msg);
              $("#PNM_NO").focus();
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


/*================================== PRO POPUP FUNCTION =================================*/

let tid = "#PROCodeTable2";
let tid2 = "#PROCodeTable";
let headers = document.querySelectorAll(tid2 + " th");

headers.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(tid, ".clsPROid", "td:nth-child(" + (i + 1) + ")");
  });
});

function PROCodeFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("PROcodesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("PROCodeTable2");
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

function PRONameFunction() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("PROnamesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("PROCodeTable2");
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

  $('#txt_PROID_REF_popup').focus(function(event){
    showSelectedCheck($("#PROID_REF").val(),"SELECT_PROID_REF");
    $("#PROID_REF_POPUP").show();
    event.preventDefault();
  });

  $("#PRO_closePopup").click(function(event){
    $("#PROID_REF_POPUP").hide();
    event.preventDefault();
  });

  $(".clsPROid").click(function(){
    var fieldid = $(this).attr('id');
    var txtval =    $("#txt"+fieldid+"").val();
    var texdesc =   $("#txt"+fieldid+"").data("desc");

    var OLD_PROID = $.trim($('#PROID_REF').val());
    if(OLD_PROID != txtval){
      $('#txt_ITEM_popup').val('');
      $('#ITEMID_REF').val('');
      $('#txt_UOM_popup').val('');
      $('#UOMID_REF').val('');
      $('#PNM_QTY').val('');
      $('#TOTAL_QTY').val('');
      $('#SOID_REF').val('');
      $('#SQID_REF').val('');
      $('#SEID_REF').val('');
      $('#material_item').html('');
    }

    $('#txt_PROID_REF_popup').val(texdesc);
    $('#PROID_REF').val(txtval);
    $("#PROID_REF_POPUP").hide();

    $("#PROcodesearch").val(''); 
    $("#PROnamesearch").val(''); 

    PROCodeFunction();
    event.preventDefault();
  });

/*================================== FROM STAGE POPUP FUNCTION =================================*/

let FS = "#FSCodeTable2";
let FS2 = "#FSCodeTable";
let FSheaders = document.querySelectorAll(FS2 + " th");

FSheaders.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(FS, ".clsFSid", "td:nth-child(" + (i + 1) + ")");
  });
});

function FSCodeFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("FScodesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("FSCodeTable2");
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

function FSNameFunction() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("FSnamesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("FSCodeTable2");
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

  $('#txt_PSTAGEID_REF_popup').focus(function(event){
    showSelectedCheck($("#PSTAGEID_REF").val(),"SELECT_PSTAGEID_REF");
    $("#FROM_STAGE_POPUP").show();
    event.preventDefault();
  });

  $("#FS_closePopup").click(function(event){
    $("#FROM_STAGE_POPUP").hide();
    event.preventDefault();
  });

  $(".clsFSid").click(function(){
    var fieldid = $(this).attr('id');
    var txtval =    $("#txt"+fieldid+"").val();
    var texdesc =   $("#txt"+fieldid+"").data("desc");
    
    $('#txt_PSTAGEID_REF_popup').val(texdesc);
    $('#PSTAGEID_REF').val(txtval);
    $("#FROM_STAGE_POPUP").hide();
    $("#FScodesearch").val(''); 
    $("#FSnamesearch").val(''); 
    FSCodeFunction();
    event.preventDefault();
  });

/*================================== STAGE POPUP FUNCTION =================================*/

let SOTable2 = "#SOTable2";
let SOTable = "#SOTable";
let SOheaders = document.querySelectorAll(SOTable + " th");

SOheaders.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(SOTable2, ".clssSOid", "td:nth-child(" + (i + 1) + ")");
  });
});

function SOCodeFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("SOcodesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("SOTable2");
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

function SONameFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("SOnamesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("SOTable2");
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

function getMovementState(TYPE){
  $("#STAGE_NAME").text(TYPE);
  $("#POPUP_STAGE_NAME").text(TYPE);
  $("#MOVEMENT_STAGE").val(TYPE.toUpperCase());

  $("#txt_PSTAGEID_REF_popup").val('');
  $("#PSTAGEID_REF").val('');
  
  $("#txt_TO_STAGE_STORE_QC_popup").val('');
  $("#TO_STAGE_STORE_QC").val('');

  $("#tbody_ItemID").html('loading...');
    $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

  $.ajax({
      url:'{{route("transaction",[$FormId,"getSOCodeNo"])}}',
      type:'POST',
      data:{'TYPE':TYPE},
      success:function(data) {
        $("#tbody_SO").html(data);
        BindSO();
       
      },
      error:function(data){
        console.log("Error: Something went wrong.");
        $("#tbody_SO").html('');
      },
  });
}

$('#txt_TO_STAGE_STORE_QC_popup').focus(function(event){

  var FROM_STAGE  = $.trim($("#txt_PSTAGEID_REF_popup").val());
 
  if(FROM_STAGE === ""){
    $("#FocusId").val('txt_PSTAGEID_REF_popup');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select from stage.');
    $("#alert").modal('show')
    $("#OkBtn1").focus();
    return false;
  }
  else{
    $("#SOpopup").show();
    showSelectedCheck($("#TO_STAGE_STORE_QC").val(),"SELECT_TO_STAGE_STORE_QC");
    event.preventDefault();
  }

});

$("#SO_closePopup").click(function(event){
  $("#SOpopup").hide();
});

function BindSO(){
  $(".clssSOid").click(function(){

    var FROM_STAGE      = $.trim($("#PSTAGEID_REF").val());
    var MOVEMENT_STAGE  = $.trim($("#MOVEMENT_STAGE").val())

    var fieldid         = $(this).attr('id');
    var txtval          = $("#txt"+fieldid+"").val();
    var texdesc         = $("#txt"+fieldid+"").data("desc");

    if(MOVEMENT_STAGE =="TO STAGE" && FROM_STAGE == txtval){
      $("#SOpopup").hide();
      $("#txt_TO_STAGE_STORE_QC_popup").val('');
      $("#FocusId").val('txt_TO_STAGE_STORE_QC_popup');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please select different stage.');
      $("#alert").modal('show')
      $("#OkBtn1").focus();
      return false;
    }
    else if(MOVEMENT_STAGE =="TO QC" && FROM_STAGE == txtval){
      $("#SOpopup").hide();
      $("#txt_TO_STAGE_STORE_QC_popup").val('');
      $("#FocusId").val('txt_TO_STAGE_STORE_QC_popup');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please select different to stage .');
      $("#alert").modal('show')
      $("#OkBtn1").focus();
      return false;
    }
    else{

      $('#txt_TO_STAGE_STORE_QC_popup').val(texdesc);
      $('#TO_STAGE_STORE_QC').val(txtval);
      $("#SOpopup").hide();
      
      $("#SOcodesearch").val(''); 
      $("#SOnamesearch").val(''); 
      SOCodeFunction();
      event.preventDefault();

    }

  });
}


/*================================== MACHINE POPUP FUNCTION =================================*/

let MACHINE_VARID = "#MACHINE_TABLE2";
let MACHINE_VARID2 = "#MACHINE_TABLE";
let MACHINE_HEADERS = document.querySelectorAll(MACHINE_VARID2 + " th");

MACHINE_HEADERS.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(MACHINE_VARID, ".CLASS_MACHINE", "td:nth-child(" + (i + 1) + ")");
  });
});

function MACHINE_CODE_FUNCTION() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("MACHINE_CODE_SEARCH");
  filter = input.value.toUpperCase();
  table = document.getElementById("MACHINE_TABLE2");
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

function MACHINE_NAME_FUNCTION() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("MACHINE_NAME_SEARCH");
  filter = input.value.toUpperCase();
  table = document.getElementById("MACHINE_TABLE2");
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


$('#Material').on('focus','[id*="txt_MACHINE_popup"]',function(event){
  $('#HIDDEN_MACHINE_ID').val($(this).attr('id'));
  $('#HIDDEN_MACHINE_ID2').val($(this).parent().parent().find('[id*="MACHINEID_REF"]').attr('id'));

  var fieldid = $(this).parent().parent().find('[id*="MACHINEID_REF"]').attr('id');
  showSelectedCheck($("#"+fieldid).val(),"SELECT_MACHINEID_REF");
  
  $("#MACHINE_POPUP").show();
});

$("#MACHINE_CLOSE_POPUP").click(function(event){
  $("#MACHINE_POPUP").hide();
  $(".CLASS_MACHINE").prop('checked', false);
});

$(".CLASS_MACHINE").click(function(){
  var fieldid = $(this).attr('id');
  var txtval  = $("#txt"+fieldid+"").val();
  var texdesc = $("#txt"+fieldid+"").data("desc");
  
  var txtid   = $('#HIDDEN_MACHINE_ID').val();
  var txt_id2 = $('#HIDDEN_MACHINE_ID2').val();

  $('#'+txtid).val(texdesc);
  $('#'+txt_id2).val(txtval);
  $("#MACHINE_POPUP").hide();
  event.preventDefault();
});

/*================================== SHIFT POPUP FUNCTION =================================*/

let SHIFT_VARID = "#SHIFT_TABLE2";
let SHIFT_VARID2 = "#SHIFT_TABLE";
let SHIFT_HEADERS = document.querySelectorAll(SHIFT_VARID2 + " th");

SHIFT_HEADERS.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(SHIFT_VARID, ".CLASS_SHIFT", "td:nth-child(" + (i + 1) + ")");
  });
});

function SHIFT_CODE_FUNCTION() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("SHIFT_CODE_SEARCH");
  filter = input.value.toUpperCase();
  table = document.getElementById("SHIFT_TABLE2");
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

function SHIFT_NAME_FUNCTION() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("SHIFT_NAME_SEARCH");
  filter = input.value.toUpperCase();
  table = document.getElementById("SHIFT_TABLE2");
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


$('#Material').on('focus','[id*="txt_SHIFT_popup"]',function(event){

  $('#HIDDEN_SHIFT_ID').val($(this).attr('id'));
  $('#HIDDEN_SHIFT_ID2').val($(this).parent().parent().find('[id*="SHIFTID_REF"]').attr('id'));

  var fieldid = $(this).parent().parent().find('[id*="SHIFTID_REF"]').attr('id');
  showSelectedCheck($("#"+fieldid).val(),"SELECT_SHIFTID_REF");

  $("#SHIFT_POPUP").show();
});

$("#SHIFT_CLOSE_POPUP").click(function(event){
  $("#SHIFT_POPUP").hide();
  $(".CLASS_SHIFT").prop('checked', false);
});

$(".CLASS_SHIFT").click(function(){
  var fieldid = $(this).attr('id');
  var txtval  = $("#txt"+fieldid+"").val();
  var texdesc = $("#txt"+fieldid+"").data("desc");
  
  var txtid   = $('#HIDDEN_SHIFT_ID').val();
  var txt_id2 = $('#HIDDEN_SHIFT_ID2').val();

  $('#'+txtid).val(texdesc);
  $('#'+txt_id2).val(txtval);
  $("#SHIFT_POPUP").hide();
  event.preventDefault();
});

/*================================== EMP POPUP FUNCTION =================================*/

let EMP_VARID = "#EMP_TABLE2";
let EMP_VARID2 = "#EMP_TABLE";
let EMP_HEADERS = document.querySelectorAll(EMP_VARID2 + " th");

EMP_HEADERS.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(EMP_VARID, ".CLASS_EMP", "td:nth-child(" + (i + 1) + ")");
  });
});

function EMP_CODE_FUNCTION() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("EMP_CODE_SEARCH");
  filter = input.value.toUpperCase();
  table = document.getElementById("EMP_TABLE2");
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

function EMP_NAME_FUNCTION() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("EMP_NAME_SEARCH");
  filter = input.value.toUpperCase();
  table = document.getElementById("EMP_TABLE2");
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


$('#Material').on('focus','[id*="txt_EMP_popup"]',function(event){

  $('#HIDDEN_EMP_ID').val($(this).attr('id'));
  $('#HIDDEN_EMP_ID2').val($(this).parent().parent().find('[id*="EMPID_REF"]').attr('id'));

  var fieldid = $(this).parent().parent().find('[id*="EMPID_REF"]').attr('id');
  showSelectedCheck($("#"+fieldid).val(),"SELECT_EMPID_REF");

  $("#EMP_OPEN_POPUP").show();
});

$("#EMP_CLOSE_POPUP").click(function(event){
  $("#EMP_OPEN_POPUP").hide();
  $(".CLASS_EMP").prop('checked', false);
});

$(".CLASS_EMP").click(function(){
  var fieldid = $(this).attr('id');
  var txtval  = $("#txt"+fieldid+"").val();
  var texdesc = $("#txt"+fieldid+"").data("desc");
  
  var txtid   = $('#HIDDEN_EMP_ID').val();
  var txt_id2 = $('#HIDDEN_EMP_ID2').val();

  $('#'+txtid).val(texdesc);
  $('#'+txt_id2).val(txtval);
  $("#EMP_OPEN_POPUP").hide();
  event.preventDefault();
});


/*================================== ITEM DETAILS =================================*/

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





$('#Header_Details').on('focus','[id*="txt_ITEM_popup"]',function(event){

  var PROID_REF = $("#PROID_REF").val();

  if(PROID_REF ===""){
    $("#FocusId").val(txt_PROID_REF_popup);
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select PRO NO.');
    $("#alert").modal('show')
    $("#OkBtn1").focus();
  }
  else{

    $('.js-selectall').prop('disabled', true);   

    $("#tbody_ItemID").html('loading...');
    $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $.ajax({
        url:'{{route("transaction",[$FormId,"getItemDetails"])}}',
        type:'POST',
        data:{'PROID_REF':PROID_REF,'status':'A'},
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

    event.preventDefault();

  }

});

$("#ITEMID_closePopup").click(function(event){
  $("#ITEMIDpopup").hide();
});

function bindItemEvents(){

  $('#ItemIDTable2').off(); 

  $('[id*="chkId"]').change(function(){

    
   
    if($(this).is(":checked") == true) {

      var fieldid             =   $(this).parent().parent().attr('id');
      var item_id             =   $("#txt"+fieldid+"").data("desc1");
      var item_code           =   $("#txt"+fieldid+"").data("desc2");
      var item_name           =   $("#txt"+fieldid+"").data("desc3");
      var item_main_uom_id    =   $("#txt"+fieldid+"").data("desc4");
      var item_main_uom_code  =   $("#txt"+fieldid+"").data("desc5");
      var item_qty            =   $("#txt"+fieldid+"").data("desc6");
      var item_soid           =   $("#txt"+fieldid+"").data("desc7");
      var item_seid           =   $("#txt"+fieldid+"").data("desc8");
      var item_sqid           =   $("#txt"+fieldid+"").data("desc9");

      $("#txt_ITEM_popup").val(item_code+'-'+item_name);
      $("#ITEMID_REF").val(item_id);

      $("#txt_UOM_popup").val(item_main_uom_code);
      $("#UOMID_REF").val(item_main_uom_id);

      $("#PNM_QTY").val(item_qty);
      $("#SOID_REF").val(item_soid);
      $("#SQID_REF").val(item_sqid);
      $("#SEID_REF").val(item_seid);


      get_materital_item();
      GetByProduct(''); 
      GetAdditionalMaterial(''); 
    
              
      $("#ITEMIDpopup").hide();
      event.preventDefault();
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
		get_materital_item();
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


$("#PARTIAL_PRODUCTION").change(function() {
	
	var PD_OR_QTY     = $("#PNM_QTY").val();
		
      if ($(this).is(":checked") == false){
          $('#ACTUAL_QTY').val(PD_OR_QTY);
		  $('#ACTUAL_QTY').prop('readonly','true');
		  event.preventDefault();
      }
      else
      {
		  $('#ACTUAL_QTY').removeAttr('readonly');
		  getQtyCheck();
          event.preventDefault();
      }
      get_materital_item(); 
      event.preventDefault();
});

var objTOLERENCE = <?php echo json_encode($objTOLERENCE); ?>;
function getQtyCheck(){
	
  var tolerence		= objTOLERENCE.FIELD1;  

  var PD_OR_QTY     	= 	$("#PNM_QTY").val();
  var ACTUAL_QTY    	= 	$("#ACTUAL_QTY").val();
  
  if(intRegex.test(ACTUAL_QTY)){
	  ACTUAL_QTY = (ACTUAL_QTY +'.000');
  }
  
  if (tolerence > 0)
  {
	  var perQty        = parseFloat((PD_OR_QTY*tolerence)/100).toFixed(3);
  }
  else
  {
	  var perQty        = 0;
  }
  var penQty        = parseFloat(PD_OR_QTY);
  var finalQty      = parseFloat(perQty)+parseFloat(PD_OR_QTY);
  var finalQty2      = parseFloat(PD_OR_QTY)-parseFloat(perQty);
  var finalQty      = parseFloat(finalQty).toFixed(3);
  var finalQty2      = parseFloat(finalQty2).toFixed(3);
  
  
  
  if ($('#PARTIAL_PRODUCTION').is(":checked") == false){
		  $('#ACTUAL_QTY').val(PD_OR_QTY);
		  $('#ACTUAL_QTY').prop('readonly','true');
		  event.preventDefault();
  }
  else
  {
		if( parseFloat(ACTUAL_QTY) > finalQty){
			 $("#FocusId").val($("#ACTUAL_QTY"));
			 $("#ACTUAL_QTY").val('');
			 $("#ProceedBtn").focus();
			 $("#YesBtn").hide();
			 $("#NoBtn").hide();
			 $("#OkBtn1").show();
			 $("#AlertMessage").text('Check Actual Production Quantity.');
			 $("#alert").modal('show')
			 $("#OkBtn1").focus();
       get_materital_item();
			 return false;
		  }
		  else if(parseFloat(ACTUAL_QTY) < finalQty2){
			 $("#FocusId").val($("#ACTUAL_QTY"));
			 $("#ACTUAL_QTY").val('');
			 $("#ProceedBtn").focus();
			 $("#YesBtn").hide();
			 $("#NoBtn").hide();
			 $("#OkBtn1").show();
			 $("#AlertMessage").text('Check Actual Production Quantity.');
			 $("#alert").modal('show')
			 $("#OkBtn1").focus();
       get_materital_item();
			 return false;
		  }
		  else
		  {
        $("#ACTUAL_QTY").val(ACTUAL_QTY);

		  }
		  $('#ACTUAL_QTY').removeAttr('readonly');
      get_materital_item();
		  event.preventDefault();
  }
  
}


/*================================== MATERIAL ITEM FUNCTION ==================================*/

function get_materital_item(){

  var  item_array   = [];

  var PROID_REF     = $("#PROID_REF").val();
  var SOID_REF      = $("#SOID_REF").val();
  var ITEMID_REF    = $("#ITEMID_REF").val();
  var ITEMID_CODE   = $("#txt_ITEM_popup").val();
  var PD_OR_QTY     = $("#PNM_QTY").val();
  var SQID_REF      = $("#SQID_REF").val();
  var SEID_REF      = $("#SEID_REF").val();
  var ACTUAL_QTY    = $("#ACTUAL_QTY").val();


  if ($('#PARTIAL_PRODUCTION').is(":checked") == false){
    var PD_OR_QTY     = $("#PNM_QTY").val();
		  $('#ACTUAL_QTY').val(PD_OR_QTY);
		  $('#ACTUAL_QTY').prop('readonly','true');
		  event.preventDefault();
  }
  else
  {
    var PD_OR_QTY     = $("#ACTUAL_QTY").val();
		 // getQtyCheck();
		  $('#ACTUAL_QTY').removeAttr('readonly');
		  event.preventDefault();
  }

  item_array.push(PROID_REF+'_'+SOID_REF+'_'+ITEMID_REF+'_'+ITEMID_CODE+'_'+PD_OR_QTY+'_'+SQID_REF+'_'+SEID_REF);

  $("#material_item").html('loading..');
  $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  $.ajax({
      url:'{{route("transaction",[$FormId,"get_materital_item"])}}',
      type:'POST',
      data:{
        item_array:item_array
        },
      success:function(data) {
        $("#material_item").html(data);  
        getOtherDirectCost(ITEMID_REF);          
      },
      error:function(data){
        console.log("Error: Something went wrong.");
        $("#material_item").html('');                        
      },
  }); 


}

/*================================== USER DEFINE FUNCTION ==================================*/

function isNumberDecimalKey(evt){
  var charCode = (evt.which) ? evt.which : event.keyCode
  if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
  return false;

  return true;
}


/*================================== UDF FUNCTION ==================================*/

$(document).ready(function(e) {
	
	var udfdata = <?php echo json_encode($objUdfData); ?>;
	var count2  = <?php echo json_encode($objCountUDF); ?>;

	$('#Row_Count2').val(count2);
	$('#example3').find('.participantRow3').each(function(){

	  var txt_id4 = $(this).find('[id*="udfinputid"]').attr('id');
	  var udfid   = $(this).find('[id*="UDF"]').val();

	  $.each( udfdata, function( seukey, seuvalue ) {
		if(seuvalue.UDFPNMID == udfid){

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
		
			if(souvalue.UDF == usovalue.UDFPNMID_REF){
				$('#popupSEID_'+soukey).val(usovalue.LABEL);
			}
    
			if(souvalue.UDF == usovalue.UDFPNMID_REF){        
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

function checkQty(id,val){

var  qty_array   = [];
var  qty_array1  = [];

$('#example2').find('.participantRow').each(function(){

  if($.trim($(this).find('[id*="QTY"]').val()) !=""){
    qty_array.push(parseFloat($.trim($(this).find('[id*="QTY"]').val())));
  }

});

$('#example6').find('.participantRow6').each(function(){

  if($.trim($(this).find('[id*="PRODUCE_QTY2"]').val()) !=""){
    qty_array1.push(parseFloat($.trim($(this).find('[id*="PRODUCE_QTY2"]').val())));
  }
});
var total_qty = getArraySum(qty_array);
var total_qty1 = getArraySum(qty_array1);
var total_qty=total_qty+total_qty1;
$("#TOTAL_QTY").val(total_qty);

}

function checkBatchNo(id,val){

var  RackArray   = [];

$('#example2').find('.participantRow').each(function(){

  var exist=$.trim($(this).find("[id*=MACHINEID_REF]").val())+'-'+$.trim($(this).find("[id*=SHIFTID_REF]").val())+'-'+$.trim($(this).find("[id*=EMPID_REF]").val())+'-'+$.trim($(this).find("[id*=BATCH]").val());

  if(RackArray.indexOf(exist) > -1){
    $("#FocusId").val(id);
    $("#alert").modal('show');
    $("#AlertMessage").text('Duplicate Batch No not allow In Material.');
    $("#YesBtn").hide(); 
    $("#NoBtn").hide();  
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk');
  }

  if($.trim($(this).find('[id*="BATCH"]').val()) !=""){

    RackArray.push(exist);

  }

});

}

function getArraySum(a){
  var total=0;
  for(var i in a) { 
      total += a[i];
  }
  return total;
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


function calculateRateQty(){

var TOT_AMT = 0;
var ODC_AMT = 0;
$('#material_item').find('.participantRow4').each(function(){
  RATE  = parseFloat($(this).find('[id*="RATE"]').val());
  QTY   = $(this).find('[id*="REQ_CHANGE_IN_CONSUME_QTY"]').val() !=''?parseFloat($(this).find('[id*="REQ_CHANGE_IN_CONSUME_QTY"]').val()):0;
  TOTAL = parseFloat(RATE*QTY).toFixed(2);

  $(this).find('[id*="TOTAL_AMOUNT"]').val(TOTAL);

  TOT_AMT=parseFloat(TOT_AMT)+parseFloat(TOTAL);
});

$('#direct_cost').find('.participantRow10').each(function(){
  VALUE = $(this).find('[id*="value"]').val() !=''?$(this).find('[id*="value"]').val():0
  ODC_AMT=parseFloat(ODC_AMT)+parseFloat(VALUE);
});

var TOTAL_COST  = parseFloat(TOT_AMT)+parseFloat(ODC_AMT);

$("#TOTAL_COST").val(parseFloat(TOTAL_COST).toFixed(2));

}

function resetValue(id,value,no){
if(value !=""){
  $("#"+id).val(parseFloat(value).toFixed(no));
}
}

// Start Other Direct Cost
function get_item_component(id){  

var result = id.split('_');
var id_number=result[1];
var popup_id='#'+id;

var cid = "Componentid_"+id_number;
var cname = "Componentname_"+id_number;    
$('#hdn_CompItemID').val(cid);
$('#hdn_CompItemID2').val(cname);
$("#component_popup").show(); 

}

$("#component_close").on("click",function(event){ 
$("#component_popup").hide();
});

$('[id*="chkcomponentcode_"]').change(function(){

if( $(this).is(":checked") == true) {

var fieldid = $(this).parent().parent().attr('id');
var txtval =    $("#txt"+fieldid+"").val();
var txtdesc =   $("#txt"+fieldid+"").data("desc");

var id_numbers= $(this).val();
var CheckExist = []; 
$('#example6').find('.participantRow10').each(function(){ 
  if($(this).find('[id*="Componentid"]').val() != '')  {
    var itemid = $(this).find('[id*="Componentid"]').val();
    CheckExist.push(itemid);
  }
});


if(jQuery.inArray(txtval, CheckExist) !== -1){
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Component already exists.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        highlighFocusBtn('activeOk1');
       
        $('#component_codesearch').val('');
        $('#component_namesearch').val('');
        searchComponentCode();

        $(this).prop("checked",false);

        $("#component_popup").hide();
        return false;

}else{

   
    var txtid=  $('#hdn_CompItemID').val();
    var txt_id2=$('#hdn_CompItemID2').val();

    $('#'+txtid).val(txtval);
    $('#'+txt_id2).val(txtdesc);
    
    $('#component_codesearch').val('');
    $('#component_namesearch').val('');
    searchComponentCode();
    $(this).prop("checked",false);

}

$("#component_popup").hide();

}     

});

function searchComponentCode() {
var input, filter, table, tr, td, i, txtValue;
input = document.getElementById("component_codesearch");
filter = input.value.toUpperCase();
table = document.getElementById("component_table2");
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


function searchComponentName() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("component_namesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("component_table2");
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

$("#direct_cost").on('click', '.add', function() {
      var $tr = $(this).closest('table');
      var allTrs = $tr.find('.participantRow10').last();
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
      $clone.find('[id*="ITEMID_REF"]').val('');
      $tr.closest('table').append($clone);         
      var rowCount1 = $('#Row_Count5').val();
      rowCount1 = parseInt(rowCount1)+1;
      $('#Row_Count5').val(rowCount1);
      $clone.find('.remove').removeAttr('disabled'); 
      
      calculateRateQty();
      event.preventDefault();
  });

$("#direct_cost").on('click', '.remove', function() {

var rowCount = $(this).closest('table').find('.participantRow10').length;
if (rowCount > 1) {
$(this).closest('.participantRow10').remove();     
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
calculateRateQty();
event.preventDefault();
});

function getOtherDirectCost(ITEMID_REF){

$("#other_direct_cost").html('loading..');
$.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$.ajax({
    url:'{{route("transaction",[$FormId,"getOtherDirectCost"])}}',
    type:'POST',
    data:{ITEMID_REF:ITEMID_REF},success:function(data) {
      $("#other_direct_cost").html(data);  
      calculateRateQty();            
    },
    error:function(data){
      console.log("Error: Something went wrong.");
      $("#other_direct_cost").html('');                        
    },
}); 
}
// End Other Direct Cost




$(document).ready(function(){
  GetByProduct('EDIT'); 
  GetAdditionalMaterial('EDIT'); 
  calculateRateQty();    
 
});







//BY PRODUCT SECTION STARTS HERE 

function GetByProduct(TYPE){
  //alert(TYPE); 
  var ITEMID_REF=$("#ITEMID_REF").val(); 
  var PNMID='{{ isset($objResponse)?$objResponse->PNMID:'' }}';
  $("#GetByProductItems").html('loading..');
  $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  $.ajax({
      url:'{{route("transaction",[$FormId,"GetByProductItems"])}}',
      type:'POST',
      data:{
        ITEMID_REF:ITEMID_REF,TYPE:TYPE,PNMID:PNMID,ACTION_TYPE:'disabled'
        },
      success:function(data) {
        $("#GetByProductItems").html(data);  
        //getOtherDirectCost(ITEMID_REF);          
      },
      error:function(data){
        console.log("Error: Something went wrong.");
        $("#GetByProductItems").html('');                        
      },
  }); 
}

//BY ADDITIONAL MATERIAL SECTION STARTS HERE
function GetAdditionalMaterial(TYPE){
  var ITEMID_REF=$("#ITEMID_REF").val(); 
  var PROID_REF=$("#PROID_REF").val(); 
  var PNMID='{{ isset($objResponse)?$objResponse->PNMID:'' }}';
  $("#GetAdditionalMaterialItems").html('loading..');
  $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  $.ajax({
      url:'{{route("transaction",[$FormId,"GetAdditionalMaterialItems"])}}',
      type:'POST',
      data:{
        ITEMID_REF:ITEMID_REF,PROID_REF:PROID_REF,PNMID:PNMID,TYPE:TYPE,ACTION_TYPE:'disabled'
        },
        
      success:function(data) {
        $("#GetAdditionalMaterialItems").html(data);  
        //getOtherDirectCost(ITEMID_REF);          
      },
      error:function(data){
        console.log("Error: Something went wrong.");
        $("#GetAdditionalMaterialItems").html('');                        
      },
  }); 
}



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

  function getUOM(ROW_ID){ 
    var index_id=ROW_ID.split('_').pop();
    var UOM_VAL=$('#PACKUOMID_REF_'+index_id).val();
    showSelectedCheck(UOM_VAL,"uomcheck");
         $("#UOMpopup").show();
         var id = ROW_ID;
         var id2 ="PACKUOMID_REF_"+index_id;        
        
         $('#hdn_UOM').val(id);
         $('#hdn_UOM2').val(id2);
  }

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
</script>
@endpush
