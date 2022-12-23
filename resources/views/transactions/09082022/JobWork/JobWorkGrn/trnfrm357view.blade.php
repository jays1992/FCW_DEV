@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
	<div class="row">
		<div class="col-lg-2"><a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Job Work Challan</a></div>

		<div class="col-lg-10 topnav-pd">
			<button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
      <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
      <button class="btn topnavbt" id="btnSaveSE" disabled="disabled"><i class="fa fa-save" ></i> Save</button>
      <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
      <button class="btn topnavbt" id="btnPrint"><i class="fa fa-print"></i> Print</button>
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
          <div class="col-lg-2 pl"><p>GRN No</p></div>
          <div class="col-lg-2 pl">
            <input {{$ActionStatus}} type="text" name="GRNNO" id="GRNNO" value="{{ $objMstResponse->GRNNO }}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase"  >
            <input type="hidden" name="GRJID" id="GRJID" value="{{ $objMstResponse->GRJID }}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase"  >
          </div>
              
          <div class="col-lg-2 pl"><p>GRN Date</p></div>
          <div class="col-lg-2 pl">
            <input {{$ActionStatus}} type="date" name="GRNDT" id="GRNDT" value="{{ $objMstResponse->GRNDT }}" class="form-control mandatory" autocomplete="off" placeholder="dd/mm/yyyy" >
          </div>

          <div class="col-lg-2 pl"><p>GE No</p></div>
          <div class="col-lg-2 pl">
            <input {{$ActionStatus}} type="text" name="TEXT_GEJWOID_REF_DATA" id="TEXT_GEJWOID_REF_DATA" value="{{ $objMstResponse->GENO }}" class="form-control mandatory"  autocomplete="off" readonly/>
            <input type="hidden" name="GEJWOID_REF" id="GEJWOID_REF" value="{{ $objMstResponse->GEJWOID_REF }}" class="form-control" autocomplete="off" />
          </div> 
          
        </div>
                        
        <div class="row">

          <div class="col-lg-2 pl"><p>Vendor</p></div>
          <div class="col-lg-2 pl">
              <input {{$ActionStatus}} type="text" name="txtvendor_popup" id="txtvendor_popup" value="{{ $objvendorcode2->VCODE }} - {{ $objvendorcode2->NAME }}" class="form-control mandatory"  autocomplete="off" readonly/>
              <input type="hidden" name="VID_REF" id="VID_REF"  value="{{ $objMstResponse->VID_REF }} " class="form-control" autocomplete="off" />
              <input type="hidden" name="hdnmaterial" id="hdnmaterial" class="form-control" autocomplete="off" />                                                    
          </div>

          <div class="col-lg-2 pl"><p>Vendor Challan No</p></div>
          <div class="col-lg-2 pl">
            <input {{$ActionStatus}} type="text" name="VENDOR_CHALLAN_NO" id="VENDOR_CHALLAN_NO" value="{{$objMstResponse->VENDOR_CHALLANNO}}" class="form-control"  autocomplete="off" readonly  />
          </div>

          <div class="col-lg-2 pl"><p>Vendor Bill No</p></div>
          <div class="col-lg-2 pl">
              <input {{$ActionStatus}} type="text" name="VENDOR_BILL_NO" id="VENDOR_BILL_NO" value="{{$objMstResponse->VENDOR_BILLNO}}" class="form-control"  autocomplete="off" readonly  />
          </div>
          
        </div>

        <div class="row">

          <div class="col-lg-2 pl"><p>Remarks</p></div>
          <div class="col-lg-2 pl">
            <input {{$ActionStatus}} type="text" name="REMARKS" id="REMARKS" value="{{$objMstResponse->REMARKS}}" class="form-control"  autocomplete="off" />
          </div>
          <div class="col-lg-2 pl"><p>Total Value</p></div>
          <div class="col-lg-2 pl">
              <input  type="text" name="TotalValue" id="TotalValue" class="form-control"  autocomplete="off" readonly  />
          </div>

        </div>


      </div>



    <div class="container-fluid purchase-order-view">

        <div class="row">
            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#Material">Material</a></li>
                <li><a data-toggle="tab" href="#udf">UDF</a></li> 
            </ul>
            
            
            
            <div class="tab-content">

                <div id="Material" class="tab-pane fade in active">
                    <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:400px;margin-top:10px;" >
                        <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                            <thead id="thead1"  style="position: sticky;top: 0">
                                                
                        <tr>
                          <th>JWC No</th>
                          <th>Item Code </th>
                          <th>Item Name</th>
                          <th>UOM</th>
                          <th>Vendor Batch No</th>
                          <th>Our Lot No</th>
                          <th>Challan Qty</th>
                          <th>Store</th>
                          <th>Received Qty</th>
                          <th>Short Qty</th>
                          <th>Rate</th>
                          <th>Amount</th>
                          <th>Store Name</th>
                          <th>Job Work Rate</th>
                          <th>Job Work Amount</th>
                          <th>Remarks</th>
                          <th>Total</th>
                          <th>Action <input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1" value="{{count($objMAT)}}"></th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($objMAT) && !empty($objMAT))
                        @foreach($objMAT as $key => $row)
                        @php
                          $mainitem_val = '';

                          $JWCID_REF  =   is_null($row->JWCID_REF) || trim($row->JWCID_REF)=="" ? '': trim($row->JWCID_REF);
                          $JWOID_REF  =   is_null($row->JWOID_REF) || trim($row->JWOID_REF)=="" ? '': trim($row->JWOID_REF);
                          $PROID_REF  =   is_null($row->PROID_REF) || trim($row->PROID_REF)=="" ? '': trim($row->PROID_REF);
                          $SOID_REF   =   is_null($row->SOID_REF) || trim($row->SOID_REF)=="" ? '': trim($row->SOID_REF);
                          $SQID_REF   =   is_null($row->SQID_REF) || trim($row->SQID_REF)=="" ? '': trim($row->SQID_REF);
                          $SEID_REF   =   is_null($row->SEID_REF) || trim($row->SEID_REF)=="" ? '': trim($row->SEID_REF);
                          $ITEMID_REF =   is_null($row->ITEMID_REF) || trim($row->ITEMID_REF)=="" ? '': trim($row->ITEMID_REF);

                          $mitem_id   =   $JWCID_REF."_". $JWOID_REF."_".$PROID_REF."_".$SOID_REF."_".$SQID_REF."_".$SEID_REF."_".$ITEMID_REF; 

                        @endphp
							
									<tr  class="participantRow">
										<td hidden><input type="hidden" id="{{$key}}" > </td>

                    <td><input {{$ActionStatus}} type="text" name="txtJWCID_popup_{{$key}}"   id="txtJWCID_popup_{{$key}}" value="{{$row->JWCNO}}" class="form-control" autocomplete="off" readonly style="width:130px;" /></td>
                    <td hidden><input type="text" name="JWCID_REF_{{$key}}" id="JWCID_REF_{{$key}}"      value="{{$row->JWCID_REF}}" class="form-control" autocomplete="off" /></td>
                          
                    <td hidden><input type="text"  name="JWOID_REF_{{$key}}" id="JWOID_REF_{{$key}}"value="{{$row->JWOID_REF}}" class="form-control" autocomplete="off" /></td>
                    <td hidden><input type="text"  name="PROID_REF_{{$key}}" id="PROID_REF_{{$key}}"value="{{$row->PROID_REF}}" class="form-control" autocomplete="off" /></td>
										<td hidden><input type="text"  name="SOID_REF_{{$key}}"  id="SOID_REF_{{$key}}" value="{{$row->SOID_REF}}" class="form-control" autocomplete="off" /></td>
										<td hidden><input type="text"  name="SQID_REF_{{$key}}"  id="SQID_REF_{{$key}}" value="{{$row->SQID_REF}}" class="form-control" autocomplete="off" /></td>
										<td hidden><input type="text"  name="SEID_REF_{{$key}}"  id="SEID_REF_{{$key}}" value="{{$row->SEID_REF}}" class="form-control" autocomplete="off" /></td>
									  
										<td>       <input {{$ActionStatus}}  type="text" name="popupITEMID_{{$key}}" id="popupITEMID_{{$key}}" value="{{$row->ICODE}}" class="form-control"  autocomplete="off"  readonly style="width:130px;" /></td>
										<td hidden><input  type="text" name="ITEMID_REF_{{$key}}"  id="ITEMID_REF_{{$key}}"  value="{{$row->ITEMID_REF}}" class="form-control" autocomplete="off" /></td>
									  
										<td><input {{$ActionStatus}} type="text" name="ItemName_{{$key}}" id="ItemName_{{$key}}" value="{{$row->ITEM_NAME}}" class="form-control"  autocomplete="off"  readonly style="width:130px;" /></td>
								   
										<td><input {{$ActionStatus}}	type="text"	name="popupMUOM_{{$key}}"	id="popupMUOM_{{$key}}"	value="{{$row->UOMCODE}}-{{$row->DESCRIPTIONS}}"	class="form-control"  autocomplete="off"  readonly style="width:130px;" /></td>
										<td  hidden><input	type="text"	name="MAIN_UOMID_REF_{{$key}}"	id="MAIN_UOMID_REF_{{$key}}"	value="{{$row->UOMID_REF}}"	class="form-control"  autocomplete="off" /></td>
								  
                    <td><input {{$ActionStatus}} type="text"   name="VENDOR_BATCHNO_{{$key}}" id="VENDOR_BATCHNO_{{$key}}" value="{{$row->VENDOR_BATCHNO}}" class="form-control"   autocomplete="off" style="width:130px;"   /></td>
                    <td><input {{$ActionStatus}} type="text"   name="OURLOT_NO_{{$key}}" id="OURLOT_NO_{{$key}}" value="{{$row->OURLOT_NO}}" class="form-control"   autocomplete="off" style="width:130px;"   /></td>


                    <td><input {{$ActionStatus}} type="text"   name="QTY_{{$key}}" id="QTY_{{$key}}" value="{{$row->JWC_QTY}}" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" readonly  style="width:130px;text-align:right;"  /></td>
                    
                    <td align="center"><a {{$ActionStatus}} class="btn checkstore" onclick="getStore(this.id)"  id="{{$key}}" ><i class="fa fa-clone"></i></a></td>
                    <td hidden ><input type="hidden" name="TotalHiddenQty_{{$key}}" id="TotalHiddenQty_{{$key}}" value="{{ $row->RECEIVED_QTY}}" ></td>
                    <td hidden ><input type="hidden" name="HiddenRowId_{{$key}}" id="HiddenRowId_{{$key}}" value="{{ $row->BATCH_QTY_REF}}" ></td>
                    
                    <td><input {{$ActionStatus}} type="text"   name="PD_OR_QTY_{{$key}}" id="PD_OR_QTY_{{$key}}" value="{{$row->RECEIVED_QTY}}" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" readonly  onKeyup="get_materital_item()" style="width:130px;text-align:right;"  /></td>
                    <td><input {{$ActionStatus}} type="text"   name="BL_SOQTY_{{$key}}" id="BL_SOQTY_{{$key}}" value="{{ floatval($row->SHORT_QTY)}}" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" readonly  style="width:130px;text-align:right;"  /></td>

                    <td ><input {{$ActionStatus}} type="text"   name="ITEMRATE_{{$key}}" id="ITEMRATE_{{$key}}" class="form-control five-digits" value="{{ $row->RATE}}" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" style="width:130px;text-align:right;"   /></td>
                    <td ><input {{$ActionStatus}} type="text"   name="ITEMAMT_{{$key}}" id="ITEMAMT_{{$key}}" class="form-control two-digits"   value="0.00" readonly maxlength="13"  autocomplete="off" style="width:130px;text-align:right;"  /></td>

                    <td><input {{$ActionStatus}} type="text"   name="STORE_NAME_{{$key}}" id="STORE_NAME_{{$key}}" value="{{$row->STORE_NAME}}" class="form-control"   autocomplete="off" style="width:200px;" readonly /></td>
                       
                    <td ><input {{$ActionStatus}} type="text"   name="JWRATE_{{$key}}" id="JWRATE_{{$key}}" class="form-control five-digits" value="{{$row->JWRATE}}" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" style="width:130px;text-align:right;"   /></td>
                    <td ><input {{$ActionStatus}} type="text"   name="JWAMT_{{$key}}" id="JWAMT_{{$key}}" class="form-control two-digits"   value="0.00" readonly maxlength="13"  autocomplete="off" style="width:130px;text-align:right;"  /></td>

                    <td><input {{$ActionStatus}} type="text"   name="REMARKS_{{$key}}" id="REMARKS_{{$key}}" value="{{$row->REMARKS}}" class="form-control"   autocomplete="off" style="width:130px;"   /></td>
                    <td ><input {{$ActionStatus}} type="text"   name="TOT_AMT_{{$key}}" id="TOT_AMT_{{$key}}" class="form-control three-digits"   value="0.00" readonly maxlength="13"  autocomplete="off" style="width:130px;text-align:right;"  /></td>

                    <td hidden><input type="text"   name="MAINTROWID_{{$key}}" id="MAINTROWID_{{$key}}" class="form-control " value="{{$mitem_id}}"   /></td>
										<td align="center" ><span id="tempid_{{$key}}" style="display: none;">{{$mitem_id}}</span>
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
									$Row_Count5 =   count($material_array);
									echo'<table id="example8" class="display nowrap table table-striped table-bordered itemlist w-200" width="100%" style="height:auto !important;">
											<thead id="thead1"  style="position: sticky;top: 0">
                      <tr>
                        <th hidden ><input class="form-control" type="hidden" name="Row_Count5" id ="Row_Count5" value="'.$Row_Count5.'"></th>
                        <th>Item Code</th>
                        <th>Item Name</th>
                        <th>Input Item as per Job Work Order Qty</th>
                        <th>Actual consumption</th>
                        <th>Consumed Lot Nos</th>
                        <th>Remarks</th>
                      </tr>
											</thead>
											<tbody>';

                      foreach($material_array as $index=>$row_data){

                        echo '<tr  class="participantRow8">';

                        echo '<td><input '.$ActionStatus.' type="text" id="txtSUBITEM_popup_'.$index.'" value="'.$row_data['ICODE'].'" class="form-control" readonly /></td>';
                        echo '<td><input '.$ActionStatus.' type="text" id="SUBITEM_NAME_'.$index.'"     value="'.$row_data['NAME'].'"  class="form-control" readonly /></td>';
                        
                        echo '<td><input '.$ActionStatus.' type="text" name="REQ_STD_BOM_QTY_'.$index.'" id="REQ_STD_BOM_QTY_'.$index.'"  value="'.$row_data['STD_BOM_QTY'].'" readonly class="form-control"  /></td>';
                        echo '<td><input '.$ActionStatus.' type="text" name="REQ_JWC_QTY_'.$index.'" id="REQ_JWC_QTY_'.$index.'"      value="'.$row_data['ACTUAL_CON_QTY'].'" readonly     class="form-control three-digits"  readonly onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" /></td>';
                        
                        echo '<td><input '.$ActionStatus.' type="text" name="REQ_CONSUMED_LOTNO_'.$index.'" id="REQ_CONSUMED_LOTNO_'.$index.'"   value="'.$row_data['CONSUMED_LOTNO'].'" class="form-control" autocomplete="off"  /></td>';
                        echo '<td><input '.$ActionStatus.' type="text" name="REQ_REMARKS_'.$index.'" id="REQ_REMARKS_'.$index.'"    value="'.$row_data['REMARKS'].'" class="form-control" autocomplete="off" /></td>';
                       
                        echo '<td hidden><input type="hidden" name="REQ_JWO_REQID_'.$index.'"    id="REQ_JWO_REQID_'.$index.'"    value="'.$row_data['JWO_REQID'].'" /></td>';
                        echo '<td hidden><input type="hidden" name="REQ_JWCID_REF_'.$index.'"    id="REQ_JWCID_REF_'.$index.'"    value="'.$row_data['MAIN_JWCID'].'" /></td>';
                        echo '<td hidden><input type="hidden" name="REQ_JWOID_REF_'.$index.'"    id="REQ_JWOID_REF_'.$index.'"    value="'.$row_data['MAIN_JWOID'].'" /></td>';
                        echo '<td hidden><input type="hidden" name="REQ_PROID_REF_'.$index.'"    id="REQ_PROID_REF_'.$index.'"    value="'.$row_data['MAIN_PROID'].'" /></td>';
                        echo '<td hidden><input type="hidden" name="REQ_SOID_REF_'.$index.'"     id="REQ_SOID_REF_'.$index.'"     value="'.$row_data['MAIN_SOID'].'" /></td>';
                        echo '<td hidden><input type="hidden" name="REQ_SOITEMID_REF_'.$index.'" id="REQ_SOITEMID_REF_'.$index.'" value="'.$row_data['MAIN_ITEMID'].'" /></td>';
                        echo '<td hidden><input type="text"   name="REQ_ITEMID_REF_'.$index.'"   id="REQ_ITEMID_REF_'.$index.'"   value="'.$row_data['ITEMID_REF'].'" /></td>';
                        echo '<td hidden><input type="hidden" name="REQ_SQID_REF_'.$index.'"     id="REQ_SQID_REF_'.$index.'"     value="'.$row_data['MAIN_SQID'].'" /></td>';
                        echo '<td hidden><input type="hidden" name="REQ_SEID_REF_'.$index.'"     id="REQ_SEID_REF_'.$index.'"     value="'.$row_data['MAIN_SEID'].'" /></td>';
                  
                        echo '<td hidden><input id="main_item_rowid_'.$index.'" value="'.$row_data['MAIN_ITEM_ROWID'].'"  /></td>';
                        
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
                                
                                


              <div id="udf" class="tab-pane fade">
                  <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="margin-top:10px;height:280px;width:50%;">
                      <table id="example4" class="display nowrap table table-striped table-bordered itemlist" style="height:auto !important;">
                          <thead id="thead1"  style="position: sticky;top: 0">
                          <tr >
                              <th>UDF Fields<input class="form-control" type="hidden" name="Row_Count3" id ="Row_Count3" value="{{ $objCountUDF }}"></th>
                              <th>Value / Comments</th>
                          </tr>
                          </thead>

                          <tbody>
                            @foreach($objUdf as $udfkey => $udfrow)
                            <tr  class="participantRow4">
                              <td>
                                <input {{$ActionStatus}} name={{"udffie_popup_".$udfkey}} id={{"txtudffie_popup_".$udfkey}} value="{{$udfrow->LABEL}}" class="form-control @if ($udfrow->ISMANDATORY==1) mandatory @endif" autocomplete="off" maxlength="100" disabled/>
                              </td>
              
                              <td hidden>
                                <input type="text" name='{{"udffie_".$udfkey}}' id='{{"hdnudffie_popup_".$udfkey}}' value="{{$udfrow->UDFGRJID}}" class="form-control" maxlength="100" />
                              </td>
              
                              <td hidden>
                                <input type="text" name={{"udffieismandatory_".$udfkey}} id={{"udffieismandatory_".$udfkey}} class="form-control" maxlength="100" value="{{$udfrow->ISMANDATORY}}" />
                              </td>
              
                              <td id="{{"tdinputid_".$udfkey}}">
                                @php
            
                                    $dynamicid = "udfvalue_".$udfkey;
                                    $chkvaltype = strtolower($udfrow->VALUETYPE); 

                                  if($chkvaltype=='date'){

                                    $strinp = '<input '.$ActionStatus.' type="date" placeholder="dd/mm/yyyy" name="'.$dynamicid.'" id="'.$dynamicid.'" class="form-control" value="'.$udfrow->UDF_VALUE.'" /> ';       

                                  }else if($chkvaltype=='time'){

                                      $strinp= '<input '.$ActionStatus.' type="time" placeholder="h:i" name="'.$dynamicid.'" id="'.$dynamicid.'" class="form-control"  value="'.$udfrow->UDF_VALUE.'"/> ';

                                  }else if($chkvaltype=='numeric'){
                                  $strinp = '<input '.$ActionStatus.' type="text" name="'.$dynamicid. '" id="'.$dynamicid.'" class="form-control" value="'.$udfrow->UDF_VALUE.'"/> ';

                                  }else if($chkvaltype=='text'){

                                  $strinp = '<input '.$ActionStatus.' type="text" name="'.$dynamicid. '" id="'.$dynamicid.'" class="form-control" value="'.$udfrow->UDF_VALUE.'"/> ';

                                  }else if($chkvaltype=='boolean'){
                                      $boolval = ''; 
                                      if($udfrow->UDF_VALUE=='on' || $udfrow->UDF_VALUE=='1' ){
                                        $boolval="checked";
                                      }
                                      $strinp = '<input '.$ActionStatus.' type="checkbox" name="'.$dynamicid. '" id="'.$dynamicid.'" class=""  '.$boolval.' /> ';

                                  }else if($chkvaltype=='combobox'){
                                    $strinp='';
                                  $txtoptscombo =   strtolower($udfrow->DESCRIPTIONS); ;
                                  $strarray =  explode(',',$txtoptscombo);
                                  $opts = '';
                                  $chked='';
                                    for ($i = 0; $i < count($strarray); $i++) {
                                      $chked='';
                                      if($strarray[$i]==$udfrow->UDF_VALUE){
                                        $chked='selected="selected"';
                                      }
                                      $opts = $opts.'<option value="'.$strarray[$i].'"'.$chked.'  >'.$strarray[$i].'</option> ';
                                    }

                                    $strinp = '<select '.$ActionStatus.' name="'.$dynamicid.'" id="'.$dynamicid.'" class="form-control" >'.$opts.'</select>' ;


                                  }
                                  echo $strinp;
                                  @endphp
                              </td>
                            </tr>
                            @endforeach
                            
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
<div id="StoreModal" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:80%;z-index:1">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='StoreModalClose' >&times;</button>
      </div>
      <div class="modal-body">
	      <div class="tablename"><p>Store Details</p></div>
        <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
          <table id="StoreTable" class="display nowrap table  table-striped table-bordered" style="width: 100%;font-size:14px;" >
          </table>
        </div>
		    <div class="cl"></div>
      </div>
    </div>
  </div>
</div>

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


<div id="ALERT_GEJWOID_REF_POPUP" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='CLOSE_GEJWOID_REF_POPUP' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>GE NO</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="FIRST_GEJWOID_REF_TABLE" class="display nowrap table  table-striped table-bordered" >
    <thead>
    <tr>
      <th class="ROW1">Select</th> 
      <th class="ROW2">GE NO</th>
      <th class="ROW3">DATE</th>
    </tr>
    </thead>
    <tbody>

    <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="CODE_GEJWOID_REF_SEARCH" class="form-control" autocomplete="off" onkeyup="CODE_GEJWOID_REF_FUNCTION()"></td>
        <td class="ROW3"><input type="text" id="NAME_GEJWOID_REF_SEARCH" class="form-control" autocomplete="off" onkeyup="NAME_GEJWOID_REF_FUNCTION()"></td>
      </tr>

    </tbody>
    </table>
      <table id="SECOND_GEJWOID_REF_TABLE" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2">
        </thead>
        <tbody>
        @foreach ($objGEList as $key=>$val)
        <tr >
          <td class="ROW1"> <input type="checkbox" name="SELECT_GEJWOID_REF[]" id="ROW_GEJWOID_REF_ID_{{ $key }}" class="CLASS_GEJWOID_REF_ID" value="{{ $val-> DOC_ID }}" ></td>   
          <td class="ROW2">{{ $val-> DOC_CODE }} <input type="hidden" id="txtROW_GEJWOID_REF_ID_{{ $key }}" data-desc="{{ $val-> DOC_CODE }}" data-desc2="{{ $val-> VID_REF }}" data-desc3="{{ $val-> SGLCODE }} - {{ $val-> SLNAME }}" data-desc4="{{ $val-> VENDOR_CHALLANNO }}" data-desc5="{{ $val-> VENDOR_BILLNO }}"  value="{{ $val-> DOC_ID }}"/></td>
          <td class="ROW3">{{ $val-> DOC_DESC }}</td>
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


<div id="JWOpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='JWO_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>JWO No</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="JWOTable" class="display nowrap table  table-striped table-bordered" >
    <thead>
          <tr id="none-select" class="searchalldata" hidden>
            
            <td> 
            <input type="hidden"  id="hdn_JWOID"/>
            <input type="hidden" id="hdn_JWOID2"/>
            <input type="hidden" id="hdn_JWOID3"/>
            </td>
          </tr>

      <tr>
        <th class="ROW1">Select</th> 
        <th class="ROW2">JWO NO</th>
        <th class="ROW3">Date</th>
      </tr>
    </thead>
    <tbody>

      <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="JWOcodesearch" class="form-control" autocomplete="off" onkeyup="JWOCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="JWOnamesearch" class="form-control" autocomplete="off" onkeyup="JWONameFunction()"></td>
      </tr>

    </tbody>
    </table>
      <table id="JWOTable2" class="display nowrap table  table-striped table-bordered"  >
        <thead id="thead2">

        </thead>
        <tbody id="tbody_JWO">     
        
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
              <tr id="none-select" class="searchalldata" hidden>
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
                  <input type="text" name="fieldid12" id="hdn_ItemID12"/>
                  <input type="text" name="fieldid13" id="hdn_ItemID13"/>
                  <input type="text" name="fieldid18" id="hdn_ItemID18"/>
                  <input type="text" name="fieldid19" id="hdn_ItemID19"/>
                  <input type="text" name="fieldid20" id="hdn_ItemID20"/>
                  <input type="text" name="hdn_ItemID21" id="hdn_ItemID21" value="0"/>
                  <input type="text" name="fieldid22" id="hdn_ItemID22"/>
                  <input type="text" name="fieldid23" id="hdn_ItemID23"/>
                  <input type="text" name="fieldid24" id="hdn_ItemID24"/>
                  <input type="text" name="fieldid25" id="hdn_ItemID25"/>
                </td>
              </tr>

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
                <td style="width:8%;text-align:center;"><input type="checkbox" class="js-selectall" data-target=".js-selectall1" /></td>
                <td style="width:10%;"><input type="text" id="Itemcodesearch" class="form-control" autocomplete="off" onkeyup="ItemCodeFunction()"></td>
                <td style="width:10%;"><input type="text" id="Itemnamesearch" class="form-control" autocomplete="off" onkeyup="ItemNameFunction()"></td>
                <td style="width:8%;"><input type="text" id="ItemUOMsearch" class="form-control" autocomplete="off" onkeyup="ItemUOMFunction()"></td>
                <td style="width:8%;"><input type="text" id="ItemQTYsearch" class="form-control" autocomplete="off" onkeyup="ItemQTYFunction()"></td>
                <td style="width:8%;"><input type="text" id="ItemGroupsearch" class="form-control" autocomplete="off" onkeyup="ItemGroupFunction()"></td>
                <td style="width:8%;"><input type="text" id="ItemCategorysearch" class="form-control" autocomplete="off" onkeyup="ItemCategoryFunction()"></td>
                
                <td style="width:8%;"><input type="text" id="ItemBUsearch" class="form-control" autocomplete="off" onkeyup="ItemBUFunction()"></td>
                <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemAPNsearch" class="form-control" autocomplete="off" onkeyup="ItemAPNFunction()"></td>
                <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemCPNsearch" class="form-control" autocomplete="off" onkeyup="ItemCPNFunction()"></td>
                <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemOEMPNsearch" class="form-control" autocomplete="off" onkeyup="ItemOEMPNFunction()"></td>
                
                <td style="width:8%;"><input  type="text" id="ItemStatussearch" class="form-control" autocomplete="off" onkeyup="ItemStatusFunction()"></td>
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
/*================================== SORTING FUNCTION =================================*/
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


/*================================== GE FUNCTION =================================*/

let SECOND_GEJWOID_REF_TABLE  = "#SECOND_GEJWOID_REF_TABLE";
let FIRST_GEJWOID_REF_TABLE   = "#FIRST_GEJWOID_REF_TABLE";
let GEJWOID_REF_HEADERS = document.querySelectorAll(FIRST_GEJWOID_REF_TABLE + " th");

GEJWOID_REF_HEADERS.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(SECOND_GEJWOID_REF_TABLE, ".CLASS_GEJWOID_REF_ID", "td:nth-child(" + (i + 1) + ")");
  });
});

function CODE_GEJWOID_REF_FUNCTION() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("CODE_GEJWOID_REF_SEARCH");
  filter = input.value.toUpperCase();
  table = document.getElementById("SECOND_GEJWOID_REF_TABLE");
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

function NAME_GEJWOID_REF_FUNCTION() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("NAME_GEJWOID_REF_SEARCH");
      filter = input.value.toUpperCase();
      table = document.getElementById("SECOND_GEJWOID_REF_TABLE");
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

$('#TEXT_GEJWOID_REF_DATA').click(function(event){
  showSelectedCheck($("#GEJWOID_REF").val(),"SELECT_GEJWOID_REF");
  $("#ALERT_GEJWOID_REF_POPUP").show();
});

$("#CLOSE_GEJWOID_REF_POPUP").click(function(event){
  $("#ALERT_GEJWOID_REF_POPUP").hide();
});

$(".CLASS_GEJWOID_REF_ID").click(function(){
  var fieldid = $(this).attr('id');
  var txtval =    $("#txt"+fieldid+"").val();
  var texdesc =   $("#txt"+fieldid+"").data("desc");
  var texdesc2 =   $("#txt"+fieldid+"").data("desc2");
  var texdesc3 =   $("#txt"+fieldid+"").data("desc3");
  var texdesc4 =   $("#txt"+fieldid+"").data("desc4");
  var texdesc5 =   $("#txt"+fieldid+"").data("desc5");
  
  var oldVenID      = $("#GEJWOID_REF").val();

  if (txtval != oldVenID){ 

      $('#Row_Count1').val('1');

      $('#example2').find('.participantRow').each(function(){
        var rowcount = $(this).closest('table').find('.participantRow').length;
        $(this).find('input:text').val('');
        $(this).find('input:hidden').val('');
        if(rowcount > 1)
        {
          $(this).closest('.participantRow').remove();
          rowcount = parseInt(rowcount) - 1;
          $('#Row_Count1').val(rowcount);
        }
      });

      $("#material_item").html('');

  }


  $('#TEXT_GEJWOID_REF_DATA').val(texdesc);
  $('#GEJWOID_REF').val(txtval);

  $('#VID_REF').val(texdesc2);
  $('#txtvendor_popup').val(texdesc3);
  $('#VENDOR_CHALLAN_NO').val(texdesc4);
  $('#VENDOR_BILL_NO').val(texdesc5);


  $("#ALERT_GEJWOID_REF_POPUP").hide();
  
  $("#CODE_GEJWOID_REF_SEARCH").val(''); 
  $("#NAME_GEJWOID_REF_SEARCH").val('');

  event.preventDefault();
});

/*================================== JWC POPUP FUNCTION =================================*/

let JWOTable2 = "#JWOTable2";
let JWOTable = "#JWOTable";
let JWOheaders = document.querySelectorAll(JWOTable + " th");

JWOheaders.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(JWOTable2, ".clssJWOID", "td:nth-child(" + (i + 1) + ")");
  });
});

function JWOCodeFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("JWOcodesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("JWOTable2");
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

function JWONameFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("JWOnamesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("JWOTable2");
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

$('#Material').on('click','[id*="txtJWCID_popup"]',function(event){

  $('#hdn_JWOID').val($(this).attr('id'));
  $('#hdn_JWOID2').val($(this).parent().parent().find('[id*="JWCID_REF"]').attr('id'));

  var fieldid = $(this).parent().parent().find('[id*="JWCID_REF"]').attr('id');

  var GEJWOID_REF =  $("#GEJWOID_REF").val();
  var VID_REF     =  $("#VID_REF").val();

  if(GEJWOID_REF ===""){
    $("#FocusId").val('txtvendor_popup');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select GE No.');
    $("#alert").modal('show')
    $("#OkBtn1").focus();
  }
  else{

    $("#JWOpopup").show();
    $("#tbody_JWO").html('loading...');

    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })

    $.ajax({
        url:'{{route("transaction",[$FormId,"getDocNo"])}}',
        type:'POST',
        data:{'id':VID_REF,'fieldid':fieldid},
        success:function(data) {
          $("#tbody_JWO").html(data);
          BindSO();
          showSelectedCheck($("#"+fieldid).val(),"SELECT_"+fieldid);
        },
        error:function(data){
          console.log("Error: Something went wrong.");
          $("#tbody_JWO").html('');
        },
    });

    $(this).parent().parent().find('[id*="popupITEMID"]').val('');
    $(this).parent().parent().find('[id*="ITEMID_REF"]').val('');
    $(this).parent().parent().find('[id*="ItemName"]').val('');
    $(this).parent().parent().find('[id*="popupMUOM"]').val('');
    $(this).parent().parent().find('[id*="MAIN_UOMID_REF"]').val('');
    $(this).parent().parent().find('[id*="QTY"]').val('');
    $(this).parent().parent().find('[id*="BL_SOQTY"]').val('');
    $(this).parent().parent().find('[id*="PD_OR_QTY"]').val('');
    $("#material_item").html('');

  }

});

$("#JWO_closePopup").click(function(event){
  $("#JWOpopup").hide();
});

function BindSO(){
  $(".clssJWOID").click(function(){
    var fieldid = $(this).attr('id');
    var txtval =    $("#txt"+fieldid+"").val();
    var texdesc =   $("#txt"+fieldid+"").data("desc");

    var txtid= $('#hdn_JWOID').val();
    var txt_id2= $('#hdn_JWOID2').val();

    $('#'+txtid).val(texdesc);
    $('#'+txt_id2).val(txtval);
    $("#JWOpopup").hide();
    
    $("#JWOcodesearch").val(''); 
    $("#JWOnamesearch").val(''); 
    event.preventDefault();
  });
}

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

$('#Material').on('click','[id*="popupITEMID"]',function(event){

  var GEJWOID_REF     = $("#GEJWOID_REF").val();
  var VID_REF         = $("#VID_REF").val();
  var JWCID_REF       = $(this).parent().parent().find('[id*="JWCID_REF"]').val();
  var txtJWCID_popup  = $(this).parent().parent().find('[id*="txtJWCID_popup"]').attr('id');

  if(GEJWOID_REF ===""){
    $("#FocusId").val('TEXT_GEJWOID_REF_DATA');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select GE NO.');
    $("#alert").modal('show')
    $("#OkBtn1").focus();
  }
  else if(JWCID_REF ===""){
    $("#FocusId").val(txtJWCID_popup);
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select JWC No.');
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
        data:{'VID_REF':VID_REF,'JWCID_REF':JWCID_REF,'status':'A'},
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
    var id7   = $(this).parent().parent().find('[id*="BL_SOQTY"]').attr('id');
    var id8   = $(this).parent().parent().find('[id*="PD_OR_QTY"]').attr('id');
    var id9   = $(this).parent().parent().find('[id*="SQID_REF"]').attr('id');
    var id10  = $(this).parent().parent().find('[id*="SEID_REF"]').attr('id');
    var id11  = $(this).parent().parent().find('[id*="SOID_REF"]').attr('id');
    var id12  = $(this).parent().parent().find('[id*="PROID_REF"]').attr('id');
    var id13  = $(this).parent().parent().find('[id*="JWOID_REF"]').attr('id');


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
    $('#hdn_ItemID12').val(id12);
    $('#hdn_ItemID13').val(id13);

  }

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
        var item_jwoid          =   $("#txt"+fieldid+"").data("desc10");
        var item_soid           =   $("#txt"+fieldid+"").data("desc11");
        var item_soqty          =   $("#txt"+fieldid+"").data("desc12");
        var item_proid          =   $("#txt"+fieldid+"").data("desc13");
        var item_jwcid          =   $("#txt"+fieldid+"").data("desc15");
        var item_rate           =   $("#txt"+fieldid+"").data("desc16");


        var txtamt = parseFloat(0*parseFloat(item_rate)).toFixed(2);
        if(isNaN(txtamt) || txtamt=="" )
        {
          txtamt = 0.00;
        } 

        if($(this).find('[id*="chkId"]').is(":checked") == true){

          $('#example2').find('.participantRow').each(function(){

            var JWCID_REF   =   $(this).find('[id*="JWCID_REF"]').val();
            var JWOID_REF   =   $(this).find('[id*="JWOID_REF"]').val();
            var PROID_REF   =   $(this).find('[id*="PROID_REF"]').val();
            var SOID_REF    =   $(this).find('[id*="SOID_REF"]').val();
            var SQID_REF    =   $(this).find('[id*="SQID_REF"]').val();
            var SEID_REF    =   $(this).find('[id*="SEID_REF"]').val();
            var ITEMID_REF  =   $(this).find('[id*="ITEMID_REF"]').val();

            var exist_val   =   JWCID_REF+"_"+JWOID_REF+"_"+PROID_REF+"_"+SOID_REF+"_"+SQID_REF+"_"+SEID_REF+"_"+ITEMID_REF;

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
                item_soqty          =   '';
                item_proid          =   '';
                item_jwoid          =   '';
                item_jwcid          =   '';
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
            $clone.find('[id*="QTY"]').val(item_soqty);
            $clone.find('[id*="BL_SOQTY"]').val('');
            $clone.find('[id*="PD_OR_QTY"]').val('');
          
            $clone.find('[id*="MAINTROWID"]').val(item_unique_row_id); 
            $clone.find('[id*="SQID_REF"]').val(item_sqid);
            $clone.find('[id*="SEID_REF"]').val(item_seid);
            $clone.find('[id*="SOID_REF"]').val(item_soid);
            $clone.find('[id*="PROID_REF"]').val(item_proid);
            $clone.find('[id*="JWOID_REF"]').val(item_jwoid);
            $clone.find('[id*="ITEMRATE_"]').val(item_rate);
            $clone.find('[id*="ITEMAMT_"]').val(txtamt);
            $clone.find('[id*="TOT_AMT_"]').val(txtamt);

            $tr.closest('table').append($clone);   
            var rowCount = $('#Row_Count1').val();
            rowCount    = parseInt(rowCount)+1;
            $('#Row_Count1').val(rowCount);
            $("#ITEMIDpopup").hide();
            // get_materital_item();
            bindTotalValue();
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
            var txt_id12  =   $('#hdn_ItemID12').val();
            var txt_id13  =   $('#hdn_ItemID13').val();
          
            if($.trim(txt_id1)!=""){
              $('#'+txt_id1).val(item_code);
            }
            if($.trim(txt_id2)!=""){
              $('#'+txt_id2).val(item_id);
              $('#'+txt_id2).parent().parent().find('[id*="MAINTROWID"]').val(item_unique_row_id);
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
              $('#'+txt_id6).val(item_soqty);
            }
            if($.trim(txt_id7)!=""){
              $('#'+txt_id7).val('');
            }
            if($.trim(txt_id8)!=""){
              $('#'+txt_id8).val('');
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
            if($.trim(txt_id12)!=""){
              $('#'+txt_id12).val(item_proid);
            }
            if($.trim(txt_id12)!=""){
              $('#'+txt_id13).val(item_jwoid);
              $('#'+txt_id13).parent().parent().find('[id*="ITEMRATE_"]').val(item_rate);
              $('#'+txt_id13).parent().parent().find('[id*="ITEMAMT_"]').val(txtamt);
              $('#'+txt_id13).parent().parent().find('[id*="TOT_AMT_"]').val(txtamt);
            }
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
            // get_materital_item();
            bindTotalValue();
          }
                  
        
        }

      });
    
      $('.js-selectall').prop("checked", false);   
      $("#ITEMIDpopup").hide();
      
  });


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
    var item_jwoid          =   $("#txt"+fieldid+"").data("desc10");
    var item_soid           =   $("#txt"+fieldid+"").data("desc11");
    var item_soqty          =   $("#txt"+fieldid+"").data("desc12");
    var item_proid          =   $("#txt"+fieldid+"").data("desc13");
    var item_jwcid          =   $("#txt"+fieldid+"").data("desc15");
    var item_rate           =   $("#txt"+fieldid+"").data("desc16");


      var txtamt = parseFloat(0*parseFloat(item_rate)).toFixed(2);
      if(isNaN(txtamt) || txtamt=="" )
        {
          txtamt = 0.00;
        }

    if($(this).is(":checked") == true) {

      $('#example2').find('.participantRow').each(function(){

        var JWCID_REF   =   $(this).find('[id*="JWCID_REF"]').val();
        var JWOID_REF   =   $(this).find('[id*="JWOID_REF"]').val();
        var PROID_REF   =   $(this).find('[id*="PROID_REF"]').val();
        var SOID_REF    =   $(this).find('[id*="SOID_REF"]').val();
        var SQID_REF    =   $(this).find('[id*="SQID_REF"]').val();
        var SEID_REF    =   $(this).find('[id*="SEID_REF"]').val();
        var ITEMID_REF  =   $(this).find('[id*="ITEMID_REF"]').val();

        var exist_val   =   JWCID_REF+"_"+JWOID_REF+"_"+PROID_REF+"_"+SOID_REF+"_"+SQID_REF+"_"+SEID_REF+"_"+ITEMID_REF;

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
            item_soqty          =   '';
            item_proid           =   '';
            item_jwoid           =   '';
            item_jwcid           =   '';
          
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
        $clone.find('[id*="QTY"]').val(item_soqty);
        $clone.find('[id*="BL_SOQTY"]').val('');
        $clone.find('[id*="PD_OR_QTY"]').val('');
      
        $clone.find('[id*="MAINTROWID"]').val(item_unique_row_id); 
        $clone.find('[id*="SQID_REF"]').val(item_sqid);
        $clone.find('[id*="SEID_REF"]').val(item_seid);
        $clone.find('[id*="SOID_REF"]').val(item_soid);
        $clone.find('[id*="PROID_REF"]').val(item_proid);
        $clone.find('[id*="JWOID_REF"]').val(item_jwoid);
        $clone.find('[id*="ITEMRATE_"]').val(item_rate);
        $clone.find('[id*="ITEMAMT_"]').val(txtamt);
        $clone.find('[id*="TOT_AMT_"]').val(txtamt);

        $tr.closest('table').append($clone);   
        var rowCount = $('#Row_Count1').val();
        rowCount    = parseInt(rowCount)+1;
        $('#Row_Count1').val(rowCount);
        $("#ITEMIDpopup").hide();
        // get_materital_item();
        bindTotalValue();
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
        var txt_id12  =   $('#hdn_ItemID12').val();
        var txt_id13  =   $('#hdn_ItemID13').val();
      
        if($.trim(txt_id1)!=""){
          $('#'+txt_id1).val(item_code);
        }
        if($.trim(txt_id2)!=""){
          $('#'+txt_id2).val(item_id);
          $('#'+txt_id2).parent().parent().find('[id*="MAINTROWID"]').val(item_unique_row_id);
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
          $('#'+txt_id6).val(item_soqty);
        }
        if($.trim(txt_id7)!=""){
          $('#'+txt_id7).val('');
        }
        if($.trim(txt_id8)!=""){
          $('#'+txt_id8).val('');
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
        if($.trim(txt_id12)!=""){
          $('#'+txt_id12).val(item_proid);
        }
        if($.trim(txt_id12)!=""){
          $('#'+txt_id13).val(item_jwoid);
          $('#'+txt_id13).parent().parent().find('[id*="ITEMRATE_"]').val(item_rate);
          $('#'+txt_id13).parent().parent().find('[id*="ITEMAMT_"]').val(txtamt);
          $('#'+txt_id13).parent().parent().find('[id*="TOT_AMT_"]').val(txtamt);
        }
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
        // get_materital_item();
        bindTotalValue();
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
      bindTotalValue();
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

$("#Material").on('click','.add', function() {
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
        $clone.find('[id*="ITEMID_REF"]').val('');
        $tr.closest('table').append($clone);         
        var rowCount1 = $('#Row_Count1').val();
		    rowCount1 = parseInt(rowCount1)+1;
        $('#Row_Count1').val(rowCount1);
        $clone.find('.remove').removeAttr('disabled');        
        bindTotalValue();
        event.preventDefault();
    });

    $("#Material").on('click', '.remove', function() {
        var rowCount = $(this).closest('table').find('.participantRow').length;
        if (rowCount > 1) {
          var totalvalue = $('#TotalValue').val();
          totalvalue = parseFloat(totalvalue - $(this).closest('.participantRow').find('[id*="TOT_AMT_"]').val()).toFixed(2);
          $('#TotalValue').val(totalvalue);
            $(this).closest('.participantRow').remove();  
            var rowCount1 = $('#Row_Count1').val();
            rowCount1 = parseInt(rowCount1)-1;
            $('#Row_Count1').val(rowCount1);
            get_materital_item();
          doCalculation();
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
               
                doCalculation();
              return false;
              event.preventDefault();
        }
        bindTotalValue();
        event.preventDefault();
    });



$(document).ready(function(e) {

  var Material = $("#Material").html(); 
  $('#hdnmaterial').val(Material);

  // var lastGRNDT = <?php echo json_encode($objMstResponse->GRNDT); ?>;
  // var today = new Date(); 
  // var mrsdate = <?php echo json_encode($objMstResponse->GRNDT); ?>;
  // $('#GRNDT').attr('min',lastGRNDT);
  // $('#GRNDT').attr('max',mrsdate);

  var lastdt = <?php echo json_encode($objlastdt[0]->GRNDT); ?>;
  var grn = <?php echo json_encode($objMstResponse); ?>;
  var today = new Date(); 
  var sodate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2);
  if(lastdt < grn.GRNDT)
  {
	$('#GRNDT').attr('min',lastdt);
  }
  else
  {
	  $('#GRNDT').attr('min',grn.GRNDT);
  }
  $('#GRNDT').attr('max',sodate);

  $('#btnAdd').on('click', function() {
    var viewURL = '{{route("transaction",[$FormId,"add"])}}';
    window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });
  
  $('#GRNNO').focusout(function(){
      var GRNNO   =   $.trim($(this).val());
      if(GRNNO ===""){
                $("#FocusId").val('GRNNO');
               
                
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text('Please enter value in GRN NO.');
                $("#alert").modal('show');
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk1');
                
            } 
        else{ 
        var trnsoForm = $("#edit_trn_form");
        var formData = trnsoForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("transaction",[$FormId,"checkExist"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               if(data.exists) {
                    $(".text-danger").hide();
                    if(data.exists) {                   
                        console.log("cancel MSG="+data.msg);
                                      $("#YesBtn").hide();
                                      $("#NoBtn").hide();
                                      $("#OkBtn1").show();
                                      $("#AlertMessage").text(data.msg);
                                      $(".text-danger").hide();
                                      $("#GRNNO").val('');
                                      $("#alert").modal('show');
                                      $("#OkBtn1").focus();
                                      highlighFocusBtn('activeOk1');
                    }                 
                }                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });
    }

   
  });

  /*
  $('#GRNDT').change(function( event ) {
    var today = new Date();     
    var d = new Date($(this).val()); 
    today.setHours(0, 0, 0, 0) ;
    d.setHours(0, 0, 0, 0) ;
    var sodate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
    if (d < today) {
        $(this).val(sodate);
        $("#alert").modal('show');
        $("#AlertMessage").text('GRN Date cannot be less than Current date');
        $("#YesBtn").hide(); 
        $("#NoBtn").hide();  
        $("#OkBtn1").show();
        $("#OkBtn1").focus();
        highlighFocusBtn('activeOk1');
        event.preventDefault();
    }
    else
    {
        event.preventDefault();
    }

    
  });
  */

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

});
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

  var GRNNO       =   $.trim($("#GRNNO").val());
  var GRNDT       =   $.trim($("#GRNDT").val());
  var GEJWOID_REF =   $.trim($("#GEJWOID_REF").val());
  var checkCompany  = "{{$checkCompany}}";

  if(GRNNO ===""){
    $("#FocusId").val('GRNNO');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Enter GRN NO');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if(GRNDT ===""){
    $("#FocusId").val('GRNDT');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Select GRN Date');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if(GEJWOID_REF ===""){
    $("#FocusId").val('TEXT_GEJWOID_REF_DATA');    
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Select GE No');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }    
  else{

    event.preventDefault();

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
      
    var focustext1   = "";
    var focustext2   = "";
    var focustext3   = "";
    var focustext4   = "";
    var focustext5   = "";
    var focustext6   = "";
    var focustext7   = "";
    var focustext8   = "";
    var focustext9   = "";
    var focustext10   = "";
    var focustext11   = "";

    $('#Material').find('.participantRow').each(function(){

      if($.trim($(this).find("[id*=JWCID_REF]").val()) ===""){
        allblank1.push('false');
        focustext1 = $(this).find("[id*=txtJWCID_popup]").attr('id');
        return false;
      }
      else if($.trim($(this).find("[id*=ITEMID_REF]").val()) ===""){
        allblank2.push('false');
        focustext2 = $(this).find("[id*=popupITEMID]").attr('id');
        return false;
      }
      else if($.trim($(this).find("[id*=MAIN_UOMID_REF]").val()) ===""){
        allblank3.push('false');
        focustext3 = $(this).find("[id*=popupMUOM]").attr('id');
        return false;
      }
      else if($.trim($(this).find("[id*=QTY]").val()) ===""){
        allblank4.push('false');
        focustext4 = $(this).find("[id*=QTY]").attr('id');
        return false;
      }
      else if($.trim($(this).find("[id*=PD_OR_QTY]").val()) ==="" || parseFloat($.trim($(this).find("[id*=PD_OR_QTY]").val())) <=0){
        allblank5.push('false');
        focustext5 = $(this).find("[id*=PD_OR_QTY]").attr('id');
        return false;
      }
      else if($.trim($(this).find("[id*=PD_OR_QTY]").val()) ==="" || parseFloat($.trim($(this).find("[id*=PD_OR_QTY]").val())) <=0){
        allblank5.push('false');
        focustext5 = $(this).find("[id*=PD_OR_QTY]").attr('id');
        return false;
      }

      else if(parseFloat($.trim($(this).find("[id*=PD_OR_QTY]").val())) > parseFloat($.trim($(this).find("[id*=QTY]").val())) && checkCompany =='' ){
        allblank6.push('false');
        focustext6 = $(this).find("[id*=PD_OR_QTY]").attr('id');
        return false;
      }
      else if(parseFloat($.trim($(this).find("[id*=ITEMRATE]").val())) <=0 ){
        allblank10.push('false');
        focustext10 = $(this).find("[id*=ITEMRATE]").attr('id');
        return false;
      }
      else if(parseFloat($.trim($(this).find("[id*=JWRATE]").val())) <=0 ){
        allblank11.push('false');
        focustext11 = $(this).find("[id*=JWRATE]").attr('id');
        return false;
      }
      else{
        allblank1.push('true');
        allblank2.push('true');
        allblank3.push('true');
        allblank4.push('true');
        allblank5.push('true');
        allblank6.push('true');
        allblank7.push('true');
        allblank10.push('true');
        allblank11.push('true');

        focustext1   = "";
        focustext2   = "";
        focustext3   = "";
        focustext4   = "";
        focustext5   = "";
        focustext6   = "";
        focustext7   = "";
        focustext10   = "";
        focustext11   = "";
        return true;
      }

    });


    $("[id*=txtudffie_popup]").each(function(){
      if($.trim($(this).val())!=""){
        if($.trim($(this).parent().parent().find('[id*="udffieismandatory"]').val()) == "1"){

          if($.trim($(this).parent().parent().find('[id*="udfvalue"]').val()) == ""){
            allblank9.push('false');
            focustext9 = $(this).parent().parent().find('[id*="udfvalue"]').attr('id');
            return false;   
          }
          else{
            allblank9.push('true');
            focustext9   = "";
            return true;
          }

        } 
      }
    });


    if(jQuery.inArray("false", allblank1) !== -1){
      $("#FocusId").val(focustext1);
      $("#alert").modal('show');
      $("#AlertMessage").text('Please Enter JWC No In Material');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank2) !== -1){
      $("#FocusId").val(focustext2);
      $("#alert").modal('show');
      $("#AlertMessage").text('Please Select Item In Material');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank3) !== -1){
      $("#FocusId").val(focustext3);
      $("#alert").modal('show');
      $("#AlertMessage").text('UOM Is Missing in Material');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank4) !== -1){
      $("#FocusId").val(focustext4);
      $("#alert").modal('show');
      $("#AlertMessage").text('Challan Qty Should Not Blank In Material');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank5) !== -1){
      $("#FocusId").val(focustext5);
      $("#alert").modal('show');
      $("#AlertMessage").text('Received Qty should be greater than 0 In Material');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank6) !== -1){
      $("#FocusId").val(focustext6);
      $("#alert").modal('show');
      $("#AlertMessage").text('Received Qty Should Not be Greater Than Challan Qty In Material');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank10) !== -1){
      $("#FocusId").val(focustext10);
      $("#alert").modal('show');
      $("#AlertMessage").text('Rate Should Be Greater Than Zero.');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank11) !== -1){
      $("#FocusId").val(focustext11);
      $("#alert").modal('show');
      $("#AlertMessage").text('Job Work Rate Should Be Greater Than Zero.');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank9) !== -1){
        $("#FocusId").val(focustext9);
        $("#alert").modal('show');
        $("#AlertMessage").text('Please Enter Value / Comment In UDF');
        $("#YesBtn").hide(); 
        $("#NoBtn").hide();  
        $("#OkBtn1").show();
        $("#OkBtn1").focus();
        highlighFocusBtn('activeOk');
        return false;
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
    $("#btnSaveFormData").hide(); 
    $(".buttonload").show(); 
    $("#btnApprove").prop("disabled", true);
    $.ajax({
        url:'{{ route("transaction",[$FormId,"update"])}}',
        type:'POST',
        data:formData,
        success:function(data) {
          $(".buttonload").hide(); 
          $("#btnSaveFormData").show();   
          $("#btnApprove").prop("disabled", false);
          
          
            if(data.errors) {
                $(".text-danger").hide();

                if(data.errors.GRNNO){
                    showError('ERROR_GRNNO',data.errors.GRNNO);
                            $("#YesBtn").hide();
                            $("#NoBtn").hide();
                            $("#OkBtn1").show();
                            $("#AlertMessage").text('Please enter correct value in GRN NO.');
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
            $("#btnSaveFormData").show();   
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
    var trnsoForm = $("#edit_trn_form");
    var formData = trnsoForm.serialize();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $("#btnApprove").hide(); 
    $(".buttonload_approve").show();  
    $("#btnSaveFormData").prop("disabled", true);
    $.ajax({
        url:'{{ route("transaction",[$FormId,"Approve"])}}',
        type:'POST',
        data:formData,
        success:function(data) {
          $("#btnApprove").show();  
          $(".buttonload_approve").hide();  
          $("#btnSaveFormData").prop("disabled", false);
          
          
            if(data.errors) {
                $(".text-danger").hide();

                if(data.errors.GRNNO){
                    showError('ERROR_GRNNO',data.errors.GRNNO);
                            $("#YesBtn").hide();
                            $("#NoBtn").hide();
                            $("#OkBtn1").show();
                            $("#AlertMessage").text('Please enter correct value in GRN NO.');
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
            $("#btnApprove").show();  
            $(".buttonload_approve").hide();  
            $("#btnSaveFormData").prop("disabled", false);
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

/*================================== MATERIAL ITEM FUNCTION ==================================*/

function get_materital_item(){

var  item_array   = [];
$('#example2').find('.participantRow').each(function(){
  var JWCID_REF   = $(this).find('[id*="JWCID_REF"]').val();
  var JWOID_REF   = $(this).find('[id*="JWOID_REF"]').val();
  var PROID_REF   = $(this).find('[id*="PROID_REF"]').val();
  var SOID_REF    = $(this).find('[id*="SOID_REF"]').val();
  var ITEMID_REF  = $(this).find('[id*="ITEMID_REF"]').val();
  var ITEMID_CODE = $(this).find('[id*="popupITEMID"]').val();
  var PD_OR_QTY   = $(this).find('[id*="PD_OR_QTY"]').val();
  var SQID_REF    = $(this).find('[id*="SQID_REF"]').val();
  var SEID_REF    = $(this).find('[id*="SEID_REF"]').val();

  item_array.push(JWOID_REF+'_'+SOID_REF+'_'+ITEMID_REF+'_'+ITEMID_CODE+'_'+PD_OR_QTY+'_'+SQID_REF+'_'+SEID_REF+'_'+PROID_REF+'_'+JWCID_REF);
});

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

$('#Material').on('blur',"[id*='PD_OR_QTY']",function(){
    var qty2 = $.trim($(this).val());
    var rate = $(this).parent().parent().find("[id*='ITEMRATE']").val();
    var jwrate = $(this).parent().parent().find("[id*='JWRATE']").val();

    var itemamt = parseFloat((parseFloat(qty2)*parseFloat(rate))).toFixed(2);
    if(isNaN(itemamt) || itemamt=="" )
    {
      itemamt = 0.00;
    } 
    var jwamt = parseFloat((parseFloat(qty2)*parseFloat(jwrate))).toFixed(2);
    if(isNaN(jwamt) || jwamt=="" )
    {
      jwamt = 0.00;
    }
    var totamt = parseFloat((parseFloat(itemamt)+parseFloat(jwamt))).toFixed(2);
    if(isNaN(totamt) || totamt=="" )
    {
      totamt = 0.00;
    } 
    $(this).parent().parent().find("[id*='ITEMAMT']").val(itemamt);
    $(this).parent().parent().find("[id*='JWAMT']").val(jwamt);
    $(this).parent().parent().find("[id*='TOT_AMT_']").val(totamt);

    if(isNaN(qty2) || qty2=="" )
    {
      qty2 = 0.000;
    }  
    if(intRegex.test(qty2))
    {
      $(this).val((qty2 +'.000'));
    }
    bindTotalValue();
    event.preventDefault();
});

$('#Material').on('blur',"[id*='ITEMRATE']",function(){
    var rate = $(this).val();
    var qty2 = $(this).parent().parent().find("[id*='PD_OR_QTY']").val();
    var jwrate = $(this).parent().parent().find("[id*='JWRATE']").val();

    var itemamt = parseFloat((parseFloat(qty2)*parseFloat(rate))).toFixed(2);
    if(isNaN(itemamt) || itemamt=="" )
    {
      itemamt = 0.00;
    } 
    var jwamt = parseFloat((parseFloat(qty2)*parseFloat(jwrate))).toFixed(2);
    if(isNaN(jwamt) || jwamt=="" )
    {
      jwamt = 0.00;
    }
    var totamt = parseFloat((parseFloat(itemamt)+parseFloat(jwamt))).toFixed(2);
    if(isNaN(totamt) || totamt=="" )
    {
      totamt = 0.00;
    } 
    $(this).parent().parent().find("[id*='ITEMAMT']").val(itemamt);
    $(this).parent().parent().find("[id*='JWAMT']").val(jwamt);
    $(this).parent().parent().find("[id*='TOT_AMT_']").val(totamt);

    if(isNaN(rate) || rate=="" )
    {
      rate = 0.00000;
    }  
    if(intRegex.test(rate))
    {
      $(this).val((rate +'.00000'));
    }
    bindTotalValue();
    event.preventDefault();
});

$('#Material').on('blur',"[id*='JWRATE']",function(){
    var rate = $(this).parent().parent().find("[id*='ITEMRATE']").val();
    var qty2 = $(this).parent().parent().find("[id*='PD_OR_QTY']").val();
    var jwrate = $(this).val();

    var itemamt = parseFloat((parseFloat(qty2)*parseFloat(rate))).toFixed(2);
    if(isNaN(itemamt) || itemamt=="" )
    {
      itemamt = 0.00;
    } 
    var jwamt = parseFloat((parseFloat(qty2)*parseFloat(jwrate))).toFixed(2);
    if(isNaN(jwamt) || jwamt=="" )
    {
      jwamt = 0.00;
    }
    var totamt = parseFloat((parseFloat(itemamt)+parseFloat(jwamt))).toFixed(2);
    if(isNaN(totamt) || totamt=="" )
    {
      totamt = 0.00;
    } 
    $(this).parent().parent().find("[id*='ITEMAMT']").val(itemamt);
    $(this).parent().parent().find("[id*='JWAMT']").val(jwamt);
    $(this).parent().parent().find("[id*='TOT_AMT_']").val(totamt);

    if(isNaN(jwrate) || jwrate=="" )
    {
      jwrate = 0.00000;
    }  
    if(intRegex.test(jwrate))
    {
      $(this).val((jwrate +'.00000'));
    }
    bindTotalValue();
    event.preventDefault();
});

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

/*================================== STORE FUNCTION ==================================*/

function getStore(ROW_ID){

  var ITEMID_REF  = $("#ITEMID_REF_"+ROW_ID).val();

  if(ITEMID_REF ===""){
    $("#FocusId").val("popupITEMID_"+ROW_ID);    
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Select Item In Material.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  } 
  else{
    getStoreDetails(ROW_ID);
    $("#StoreModal").show();
  }

  event.preventDefault();
}

function getStoreDetails(ROW_ID){

  var ITEMID_REF  = $("#ITEMID_REF_"+ROW_ID).val();
  var ITEMROWID   = $("#HiddenRowId_"+ROW_ID).val();
  var UOMID_REF   = $("#MAIN_UOMID_REF_"+ROW_ID).val();
  var CHALLAN_QTY  = $("#QTY_"+ROW_ID).val();

  $("#StoreTable").html('');
  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  $.ajax({
      url:'{{route("transaction",[$FormId,"getStoreDetails"])}}',
      type:'POST',
      data:{ITEMID_REF:ITEMID_REF,ROW_ID:ROW_ID,ITEMROWID:ITEMROWID,ACTION_TYPE:'EDIT',UOMID_REF:UOMID_REF,CHALLAN_QTY:CHALLAN_QTY},
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
  $("#StoreModal").hide();
});


function checkStoreQty(ROW_ID,stockQty,userQty,key,CHALLAN_QTY){



  var NewQtyArr = [];
  var NewIdArr  = [];
  var NewStArr  = [];

  $('#StoreTable').find('.participantRow33').each(function(){

      if($.trim($(this).find("[id*=UserQty]").val())!=""){  
        var UserQty      = parseFloat($.trim($(this).find("[id*=UserQty]").val()));
        var BatchId      = $.trim($(this).find("[id*=BATCHID]").val());
        var StoreName    = $.trim($(this).find("[id*=STORENAME]").val());

        NewQtyArr.push(UserQty);
        NewIdArr.push(BatchId+"_"+UserQty);
      
        if($.inArray(StoreName, NewStArr) === -1) NewStArr.push(StoreName);

      }                
  });

  var TotalQty= getArraySum(NewQtyArr); 
  if(isNaN(TotalQty) || TotalQty=="" )
  {
    TotalQty = 0.000;
  }  
  if(intRegex.test(TotalQty))
  {
    TotalQty = TotalQty +'.000';
  }



  var checkCompany  = "{{$checkCompany}}";
  var perQty        = parseFloat((CHALLAN_QTY*25)/100);
  var penQty        = parseFloat(CHALLAN_QTY);
  var finalQty      = parseFloat(perQty+penQty).toFixed(3);

  if(TotalQty > CHALLAN_QTY && checkCompany ==''){
     $("#FocusId").val($(this));
     $("#UserQty_"+key).val('');
     $("#AltUserQty_"+key).val('');
     $("#RECEIVED_QTY_MU_"+ROW_ID).val('');
     $("#RECEIVED_QTY_AU_"+ROW_ID).val('');
     $("#SHORT_QTY_"+ROW_ID).val('');
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Received Quantity cannot be greater than Challan Quantity');
     $("#alert").modal('show')
     $("#OkBtn1").focus();
     return false;
  }
  else if( parseFloat(TotalQty) > finalQty && checkCompany !=''){
     $("#FocusId").val($(this));
     $("#UserQty_"+key).val('');
     $("#AltUserQty_"+key).val('');
     $("#RECEIVED_QTY_MU_"+ROW_ID).val('');
     $("#RECEIVED_QTY_AU_"+ROW_ID).val('');
     $("#SHORT_QTY_"+ROW_ID).val('');
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Received Quantity cannot be greater than Challan Quantity');
     $("#alert").modal('show')
     $("#OkBtn1").focus();
     return false;
  }




  $("#TotalHiddenQty_"+ROW_ID).val(TotalQty);
  $("#HiddenRowId_"+ROW_ID).val(NewIdArr);
  $("#PD_OR_QTY_"+ROW_ID).val(TotalQty);

  var qty2 = TotalQty;
  var rate = $("#ITEMRATE_"+ROW_ID).val();
  var jwrate = $("#JWRATE_"+ROW_ID).val();

  var itemamt = parseFloat((parseFloat(qty2)*parseFloat(rate))).toFixed(2);
  if(isNaN(itemamt) || itemamt=="" )
  {
  itemamt = 0.00;
  } 
  var jwamt = parseFloat((parseFloat(qty2)*parseFloat(jwrate))).toFixed(2);
  if(isNaN(jwamt) || jwamt=="" )
  {
  jwamt = 0.00;
  }
  var totamt = parseFloat((parseFloat(itemamt)+parseFloat(jwamt))).toFixed(2);
  if(isNaN(totamt) || totamt=="" )
  {
  totamt = 0.00;
  } 
  $("#ITEMAMT_"+ROW_ID).val(itemamt);
  $("#JWAMT_"+ROW_ID).val(jwamt);
  $("#TOT_AMT_"+ROW_ID).val(totamt);


  bindTotalValue();

  var item_soqty  = $("#QTY_"+ROW_ID).val();
  var item_qty    = TotalQty;
  var short_qty   =  parseFloat(item_soqty)-parseFloat(item_qty);

  $("#BL_SOQTY_"+ROW_ID).val(short_qty);
  $("#STORE_NAME_"+ROW_ID).val(NewStArr);

}

function getArraySum(a){
  var total=0;
  for(var i in a) { 
    total += a[i];
  }
  return total;
}

function bindTotalValue(){

  var totalvalue  = 0.00;
  var tvalue      = 0.00;

  $('#Material').find('.participantRow').each(function(){
  tvalue = $(this).find('[id*="TOT_AMT"]').val();

  totalvalue = parseFloat(totalvalue) + parseFloat(tvalue);
  totalvalue = parseFloat(totalvalue).toFixed(2);
  });


  $('#TotalValue').val(totalvalue);
}

function getMaterialCalculation(){

  $('#Material').find('.participantRow').each(function(){
    var rate = $(this).find("[id*='ITEMRATE']").val();
    var qty2 = $(this).find("[id*='PD_OR_QTY']").val();
    var jwrate = $(this).find("[id*='JWRATE']").val();

    var itemamt = parseFloat((parseFloat(qty2)*parseFloat(rate))).toFixed(2);
    if(isNaN(itemamt) || itemamt=="" )
    {
      itemamt = 0.00;
    } 
    var jwamt = parseFloat((parseFloat(qty2)*parseFloat(jwrate))).toFixed(2);
    if(isNaN(jwamt) || jwamt=="" )
    {
      jwamt = 0.00;
    }
    var totamt = parseFloat((parseFloat(itemamt)+parseFloat(jwamt))).toFixed(2);
    if(isNaN(totamt) || totamt=="" )
    {
      totamt = 0.00;
    } 
    $(this).find("[id*='ITEMAMT']").val(itemamt);
    $(this).find("[id*='JWAMT']").val(jwamt);
    $(this).find("[id*='TOT_AMT_']").val(totamt);

    if(isNaN(rate) || rate=="" )
    {
      rate = 0.00000;
    }  
    if(intRegex.test(rate))
    {
      $(this).val((rate +'.00000'));
    }
    bindTotalValue();
    
  });
  event.preventDefault();
}

$(document).ready(function(){
  getMaterialCalculation();
});
</script>
@endpush