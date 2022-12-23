
@extends('layouts.app')
@section('content')
    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[202,'index'])}}" class="btn singlebt">Bill of Material (BOM)</a>
                </div><!--col-2-->
                <div class="col-lg-10 topnav-pd">
                        <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
                        <button class="btn topnavbt" id="btnSaveOSO" disabled="disabled"><i class="fa fa-save"></i> Save</button>
                        <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" id="btnPrint" ><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt" id="btnUndo"  disabled="disabled"><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove"  disabled="disabled"><i class="fa fa-thumbs-o-up"></i> Approved</button>
                        <button class="btn topnavbt" id="btnCopy"><i class="fa fa-copy"></i> Copy</button>
                        <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                        <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                </div>
            </div>
    </div><!--topnav-->	
    <!-- multiple table-responsive table-wrapper-scroll-y my-custom-scrollbar -->
    <div class="container-fluid purchase-order-view">
        <form id="frm_mst_bom"  method="POST">   
            @csrf
            {{isset($objBOM->BOMID[0]) ? method_field('PUT') : '' }}
            <div class="container-fluid filter">

            <div class="inner-form">
                        <div class="row">
                            <div class="col-lg-2 pl"><p>BOM No</p></div>
                            <div class="col-lg-2 pl">     

                              <input type="hidden" id="BOMID_COPY" value="{{ isset($objBOM->BOMID)?$objBOM->BOMID:'' }}">
                              <input type="hidden" id="STATUS" value="{{ isset($objBOM->STATUS)?$objBOM->STATUS:'' }}">    
                                                      
                                <input {{$InputStatus}} type="text" name="BOMNO" id="BOMNO" class="form-control mandatory" value="{{ isset($objBOM->BOM_NO)?$objBOM->BOM_NO:'' }}" readonly  autocomplete="off" style="text-transform:uppercase" >
                       
                            </div>
                            <div class="col-lg-2 pl"><p>Order Validity From </p></div>
                            <div class="col-lg-2 pl">
                                <input {{$InputStatus}} type="date" name="BOM_DT" id="BOM_DT" class="form-control mandatory" autocomplete="off" value="{{ isset($objBOM->BOM_DT)?$objBOM->BOM_DT:'' }}" placeholder="dd/mm/yyyy" >
                               
                            </div>    
                            <div class="col-lg-2 pl"><p>Product Code</p></div>
                            <div class="col-lg-2 pl">
                            <input {{$InputStatus}} type="text" name="ITEM_popup" id="txtitem_popup"  class="form-control mandatory" value="{{ isset($objBOM->ICODE)?$objBOM->ICODE:'' }}"   autocomplete="off" readonly/>
                            <input type="hidden" name="PRODUCT_CODE" id="PRODUCT_CODE" class="form-control" autocomplete="off" value="{{ isset($objBOM->ITEMID_REF)?$objBOM->ITEMID_REF:'' }}" />
                            </div>                        
                                      
                            
                        </div>                 
                        <div class="row">

                      
                            <div class="col-lg-2 pl"><p>Product Description	 </p></div>
                            <div class="col-lg-2 pl">
                            <input {{$InputStatus}} type="text" name="DESCRIPTION" id="DESCRIPTION" class="form-control mandatory" value="{{ isset($objBOM->NAME)?$objBOM->NAME:'' }}" readonly  autocomplete="off"  >
                            </div>       
                            <div class="col-lg-2 pl"><p>UOM	 </p></div>
                            <div class="col-lg-2 pl">
                            <input {{$InputStatus}} type="text" name="UOMID_REF_DESC" id="UOMID_REF_DESC" value="{{ isset($objBOM->UOMCODE)?$objBOM->UOMCODE:''}} {{ isset($objBOM->DESCRIPTIONS)?'-'.$objBOM->DESCRIPTIONS:''}}"  class="form-control mandatory" readonly  autocomplete="off"  >
                            <input type="hidden" name="UOMID_REF" id="UOMID_REF" value="{{ isset($objBOM->UOMID_REF)?$objBOM->UOMID_REF:'' }}" class="form-control mandatory" readonly  autocomplete="off"  >
                            </div>      
                            <div class="col-lg-2 pl"><p>Produce Qty	 </p></div>
                            <div class="col-lg-2 pl">
                                <input {{$InputStatus}} type="text" name="PRODUCEQTY" id="PRODUCEQTY" value="{{ isset($objBOM->PRODUCE_QTY)?$objBOM->PRODUCE_QTY:'' }}" class="form-control"  autocomplete="off"   />
                             </div>                  
                       
                        </div>
                        <div class="row">
                        <div class="col-lg-2 pl"><p>Alt Qty	 </p></div>
                            <div class="col-lg-2 pl">
                                <input type="text" {{$InputStatus}}  name="ALT_QTY" id="ALT_QTY" class="form-control" value="{{ isset($objBOM->TO_QTY)?$objBOM->TO_QTY:'' }}"  readonly autocomplete="off" maxlength="13"   />
                             </div>  
                         
                             <div class="col-lg-2 pl"><p>Production Stage</p></div>
                            <div class="col-lg-2 pl">
                                <input {{$InputStatus}} type="text" name="PRODUCTION_STAGE_POPUP" id="PRODUCTION_STAGE_POPUP" value="{{ isset($objBOM->PSTAGE_CODE)?$objBOM->PSTAGE_CODE:''}} {{isset($objBOM->PSTAGE_NAME)?'-'.$objBOM->PSTAGE_NAME:''}}" class="form-control mandatory"  autocomplete="off"  readonly/>
                                <input type="hidden" name="PRODUCTION_STAGE" id="PRODUCTION_STAGE" value="{{ isset($objBOM->PSTAGEID_REF)?$objBOM->PSTAGEID_REF:'' }}" class="form-control" autocomplete="off" />
                            </div>
                            <div class="col-lg-2 pl"><p>Design No</p></div>
                            <div class="col-lg-2 pl">
                                <input {{$InputStatus}} type="text" name="DESIGNNO" id="DESIGNNO" class="form-control" value="{{ isset($objBOM->DESIGN_NO)?$objBOM->DESIGN_NO:'' }}"  autocomplete="off" style="text-transform:uppercase"  />
                            </div>
                
                        
                        </div>
                        <div class="row">
                        <div class="col-lg-2 pl"><p>Drawing No </p></div>
                            <div class="col-lg-2 pl">
                                <input {{$InputStatus}} type="text" name="DRAWINGNO" id="DRAWINGNO" class="form-control" value="{{ isset($objBOM->DRAWING_NO)?$objBOM->DRAWING_NO:'' }}" maxlength="100" autocomplete="off" style="text-transform:uppercase">
                            </div>

                          <div class="col-lg-2 pl"><p>Part No </p></div>
                          <div class="col-lg-2 pl">
                              <input {{$InputStatus}} type="text" name="PART_NO" id="PART_NO" class="form-control" value="{{ isset($objBOM->PARTNO)?$objBOM->PARTNO:'' }}" autocomplete="off" readonly>
                          </div>
                     
                                               
                            <div class="col-lg-2 pl"><p>Remarks</p></div>
                            <div class="col-lg-2 pl" >
                                <input {{$InputStatus}} type="text" name="REMARKS" id="REMARKS" class="form-control" value="{{ isset($objBOM->REMARKS)?$objBOM->REMARKS:'' }}"  autocomplete="off"  />
                                <input type="hidden" name="hdn_rowid" id="hdn_rowid">     
                            </div>
                        </div> 
                        <div class="row">
                <div class="col-lg-2 pl" ><p>De-Activated</p></div>
                <div class="col-lg-2 pl">
                <input {{$InputStatus}} type="checkbox"   name="DEACTIVATED"  id="deactive-checkbox_0" {{ isset($objBOM->DEACTIVATED) && $objBOM->DEACTIVATED == 1 ? "checked" : ""}} value='{{ isset($objBOM->DEACTIVATED) && $objBOM->DEACTIVATED == 1 ? 1 : 0}}' tabindex="2"  >
                </div>
                
                <div class="col-lg-2 pl"><p>Date of De-Activated</p></div>
                <div class="col-lg-2 pl">
                  <input {{$InputStatus}} type="date" name="DODEACTIVATED" class="form-control" id="DODEACTIVATED" {{ isset($objBOM->DEACTIVATED) && $objBOM->DEACTIVATED == 1 ? "" : "disabled"}} value="{{isset($objBOM->DODEACTIVATED) && $objBOM->DODEACTIVATED !="" && $objBOM->DODEACTIVATED !="1900-01-01" ? $objBOM->DODEACTIVATED:''}}" tabindex="3" placeholder="dd/mm/yyyy"  />
                </div>
             </div>

                    </div>

                    <div class="container-fluid purchase-order-view">

<div class="row">
    <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#Material" id="MAT_TAB" >Material</a></li>
        <li><a data-toggle="tab" href="#Subtitute" id="SUB_TAB" >Subsitute</a></li>
        <li><a data-toggle="tab" href="#ByProduct" id="BP_TAB" >By Product</a></li>
        <li><a data-toggle="tab" href="#udf" id="UDF_TAB" >UDF</a></li>
        <li><a data-toggle="tab" href="#direct_cost" id="ODC_TAB" >Other Direct Cost	</a></li>
        <li><a data-toggle="tab" href="#instruction" id="SPI_TAB" >Special Production Instructions</a></li>
    </ul>
    <div class="tab-content">
        <div id="Material" class="tab-pane fade in active">
            <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:280px;margin-top:10px;" >
                <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                    <thead id="thead1"  style="position: sticky;top: 0">
                        <tr>
                            <th>Item Code<input class="form-control" type="hidden" name="Row_Count1" id="Row_Count1"></th>
                            <th>Item Name</th>

                            <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</th>
                            <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</th>
                            <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</th>

                            <th>Part No</th>
                            <th>UoM</th>
                            <th>Stock In Hand</th>

                            <th>Consume Qty</th>
                            <th>Loss of Production Percentage</th>
                            <th>Wastage/ Scrap Percentage</th>
							<th>Remarks</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>   
                    @if(!empty($objOSOMAT))
                      @foreach($objOSOMAT as $key => $row)     
                        <tr  class="participantRow">
                        <td hidden>
                        <input  class="form-control" type="hidden" name={{"BOM_MATID_".$key}} id ={{"BOM_MATID_".$key}} maxlength="100" value="{{ $row->BOM_MATID }}" autocomplete="off"   >
                                      </td>
                            <td><input {{$InputStatus}} type="text" name={{"popupITEMID_".$key}} id ={{"popupITEMID_".$key}} class="form-control" value="{{ $row->ICODE }}"  autocomplete="off"  readonly/></td>
                            <td hidden><input type="hidden" name={{"ITEMID_REF_".$key}} id ={{"ITEMID_REF_".$key}} value="{{ $row->ITEMID_REF }}"  class="form-control" autocomplete="off" />
                            <input type="hidden" name={{"ITEMSPECI_".$key}} id ={{"ITEMSPECI_".$key}}  class="form-control" autocomplete="off" />
                            <input type="hidden"name={{"RATEPUOM_".$key}} id ={{"RATEPUOM_".$key}}   class="form-control" autocomplete="off" />
                            <input {{$InputStatus}} type="text" name="rowscount1[]"  />
                            </td>
                            <td><input {{$InputStatus}} type="text" name={{"ItemName_".$key}} id ={{"ItemName_".$key}}  value="{{ $row->NAME }}"  class="form-control"  autocomplete="off"  readonly/></td>
                            
                            <td {{$AlpsStatus['hidden']}}><input  type="text" name="Alpspartno_{{$key}}" id="Alpspartno_{{$key}}" class="form-control"  autocomplete="off" value="{{ isset($row->ALPS_PART_NO)?$row->ALPS_PART_NO:'' }}" readonly/></td>
                            <td {{$AlpsStatus['hidden']}}><input  type="text" name="Custpartno_{{$key}}" id="Custpartno_{{$key}}" class="form-control"  autocomplete="off" value="{{ isset($row->CUSTOMER_PART_NO)?$row->CUSTOMER_PART_NO:'' }}" readonly/></td>
                            <td {{$AlpsStatus['hidden']}}><input  type="text" name="OEMpartno_{{$key}}"  id="OEMpartno_{{$key}}" class="form-control"  autocomplete="off" value="{{ isset($row->OEM_PART_NO)?$row->OEM_PART_NO:'' }}" readonly/></td>

                            
                            <td><input {{$InputStatus}} type="text" name={{"ItemPartno_".$key}} id ={{"ItemPartno_".$key}}  value="{{ $row->PARTNO }}"  class="form-control" maxlength="200" readonly autocomplete="off"  /></td>
                            <td><input {{$InputStatus}} type="text" name={{"popupUOM_".$key}} id ={{"popupUOM_".$key}}  value="{{ $row->UOMCODE.'-'.$row->DESCRIPTIONS }}"  class="form-control"  autocomplete="off"  readonly/></td>
                            <td hidden><input type="hidden" name={{"UOMID_REF_".$key}} id ={{"UOMID_REF_".$key}}  value="{{ $row->UOMID_REF }}"  class="form-control"  autocomplete="off" /></td>
                            
                            <td><input {{$InputStatus}} type="text" name={{"ItemStockih_".$key}} id={{"ItemStockih_".$key}} value="{{isset($row->STOCK_IN_HAND) && $row->STOCK_IN_HAND !=''?$row->STOCK_IN_HAND:0}}"  class="form-control"  autocomplete="off"  readonly/></td>

                            <td><input {{$InputStatus}} type="text" name={{"CONSUMEQTY_".$key}} id ={{"CONSUMEQTY_".$key}}  value="{{ $row->CONSUME_QTY }}"  class="form-control" maxlength="200" autocomplete="off"  /></td>
                            <td><input {{$InputStatus}} type="text" name={{"PRODUCTIONQTY_".$key}} id ={{"PRODUCTIONQTY_".$key}}  value="{{ $row->LOSS_PRODUCTION_QTY }}" class="form-control" maxlength="200" autocomplete="off"  /></td>
                            <td><input {{$InputStatus}} type="text" name={{"SCRAPQTY_".$key}} id ={{"SCRAPQTY_".$key}}  value="{{ $row->WASTEAGE_SCRAP_QTY }}"  class="form-control" maxlength="200" autocomplete="off"  /></td>
							<td><input {{$InputStatus}} type="text" name={{"REMARKS_".$key}} id ={{"REMARKS_".$key}}  value="{{ $row->REMARKS }}"  class="form-control" maxlength="200" autocomplete="off"  /></td>
                            <td align="center" ><button {{$InputStatus}} class="btn add material" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button><button {{$InputStatus}} class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
                        
                        </tr>
                        <tr></tr>
                        @endforeach 
                        @endif 
                    </tbody>
            </table>
            </div>	
        </div>

        <div id="Subtitute" class="tab-pane fade in ">
            <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:280px;margin-top:10px;" >
                <table id="example3" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                    <thead id="thead2"  style="position: sticky;top: 0">
                        <tr>
                            <th>Main Item Code<input class="form-control" type="hidden" name="Row_Count2" id ="Row_Count2"></th>
                            <th>Main Item Name</th>
                            <th>Subsitute Item Code</th>
                            <th>Subsitute Item Name</th>
                            <th>Part No</th>
                            <th>UoM</th>                                              
                            <th>Consume Qty</th>
                            <th>Loss of Production Percentage</th>
                            <th>Wastage/ Scrap Percentage</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @if(!empty($objSUB))
                      @foreach($objSUB as $key => $row)     
                        <tr  class="participantRow1">
                        <td hidden>
                        <input  class="form-control" type="hidden" name={{"BOM_SUBID_".$key}} id ={{"BOM_SUBID_".$key}} maxlength="100" value="{{ $row->BOM_SUBID }}" autocomplete="off"   >
                                      </td>
                            <td><input {{$InputStatus}} type="text" name={{"MainItemCode_".$key}} id ={{"MainItemCode_".$key}} value="{{ $row->ICODE2 }}"  onclick="get_item($(this).attr('id'))" class="form-control"  autocomplete="off"  readonly/></td>                                                
                            <td hidden><input type="hidden" name={{"MainItemId_Ref_".$key}} id ={{"MainItemId_Ref_".$key}} value="{{ $row->MAINITEMID_REF }}"  class="form-control" autocomplete="off" />
                            <input {{$InputStatus}} type="text" name="rowscount2[]"  />
                            </td>
                            <td><input {{$InputStatus}} type="text" name={{"MainItemName_".$key}} id ={{"MainItemName_".$key}} value="{{ $row->NAME2 }}"  class="form-control"  autocomplete="off"  readonly/></td>
                            <td><input {{$InputStatus}} type="text" name={{"MainItemCode1_".$key}} id ={{"MainItemCode1_".$key}} value="{{ $row->ICODE }}"  onclick="get_item_substitute($(this).attr('id'))" class="form-control"  autocomplete="off"  readonly/></td>                                                
                            <td hidden><input type="hidden" name={{"MainItemId1_Ref_".$key}} id ={{"MainItemId1_Ref_".$key}} value="{{ $row->SUBITEMID_REF }}"  class="form-control" autocomplete="off" /></td>
                            <td><input {{$InputStatus}} type="text" name={{"MainItemName1_".$key}} id ={{"MainItemName1_".$key}} value="{{ $row->NAME }}"  class="form-control"  autocomplete="off"  readonly/></td>
                                                             
                           
                            <td><input {{$InputStatus}} type="text" name={{"MainItemPartno1_".$key}} id ={{"MainItemPartno1_".$key}} value="{{ $row->PARTNO }}"  class="form-control"  autocomplete="off"  readonly/></td> 

                            <td><input {{$InputStatus}} type="text" name={{"MainItemuom1_".$key}} id ={{"MainItemuom1_".$key}} value="{{ $row->UOMCODE.'-'.$row->DESCRIPTIONS }}"  class="form-control"  autocomplete="off"  readonly/></td>
                            <td hidden><input name={{"Mainuom_ref1_".$key}} id ={{"Mainuom_ref1_".$key}} value="{{ $row->UOMID_REF }}"  type="hidden" class="form-control"  autocomplete="off" /></td>
                   
                            <td><input {{$InputStatus}} type="text" name={{"CONSUMEQTY1_".$key}} id ={{"CONSUMEQTY1_".$key}} value="{{ $row->CONSUME_QTY }}" class="form-control"  autocomplete="off"  /></td>
                            <td><input {{$InputStatus}} type="text" name={{"PRODUCTION1_".$key}} id ={{"PRODUCTION1_".$key}} value="{{ $row->LOSS_PRODUCTION_QTY }}"  class="form-control"  autocomplete="off"  /></td>
                            <td><input {{$InputStatus}} type="text" name={{"SCRAP1_".$key}} id ={{"SCRAP1_".$key}} value="{{ $row->WASTEAGE_SCRAP_QTY }}"  class="form-control"  autocomplete="off"  /></td>
                            <td align="center" ><button {{$InputStatus}} class="btn add" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button><button {{$InputStatus}} class="btn remove smaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
                        
                        </tr>
                        <tr></tr>
                        @endforeach 
              

                        @else 
                

                        <tr  class="participantRow1">
                            <td><input {{$InputStatus}} type="text" name="MainItemCode_0" id="MainItemCode_0" onclick="get_item($(this).attr('id'))" class="form-control"  autocomplete="off"  readonly/></td>                                                
                            <td hidden><input type="hidden" name="MainItemId_Ref_0" id="MainItemId_Ref_0" class="form-control" autocomplete="off" />
                            <input {{$InputStatus}} type="text" name="rowscount2[]"  />
                            </td>
                            <td><input {{$InputStatus}} type="text" name="MainItemName_0" id="MainItemName_0" class="form-control"  autocomplete="off"  readonly/></td>
                            <td><input {{$InputStatus}} type="text" name="MainItemCode1_0" id="MainItemCode1_0" onclick="get_item_substitute($(this).attr('id'))" class="form-control"  autocomplete="off"  readonly/></td>                                                
                            <td hidden><input type="hidden" name="MainItemId1_Ref_0" id="MainItemId1_Ref_0" class="form-control" autocomplete="off" /></td>
                            <td><input {{$InputStatus}} type="text" name="MainItemName1_0" id="MainItemName1_0" class="form-control"  autocomplete="off"  readonly/></td>
                                                              
                            
                            <td><input {{$InputStatus}} type="text" name="MainItemPartno1_0" id="MainItemPartno1_0" class="form-control"  autocomplete="off"  readonly/></td> 

                            <td><input {{$InputStatus}} type="text" name="MainItemuom1_0" id="MainItemuom1_0" class="form-control"  autocomplete="off"  readonly/></td>
                            <td hidden><input type="hidden" name="Mainuom_ref1_0" id="Mainuom_ref1_0" class="form-control"  autocomplete="off" /></td>
                    
                            <td><input {{$InputStatus}} type="text" name="CONSUMEQTY1_0" id="CONSUMEQTY1_0" class="form-control"  autocomplete="off"  /></td>
                            <td><input {{$InputStatus}} type="text" name="PRODUCTION1_0" id="PRODUCTION1_0" class="form-control"  autocomplete="off"  /></td>
                            <td><input {{$InputStatus}} type="text" name="SCRAP1_0" id="SCRAP1_0" class="form-control"  autocomplete="off"  /></td>
                            <td align="center" ><button {{$InputStatus}} class="btn add" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button><button {{$InputStatus}} class="btn remove smaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
                        
                        </tr>
                        @endif 
                    </tbody>
            </table>
            </div>	
        </div>
       <div id="ByProduct" class="tab-pane fade in ">
            <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:280px;margin-top:10px;" >
                <table id="example4" class="display nowrap table table-striped table-bordered itemlist" width="60%" style="height:auto !important; "  align="center">
                    <thead id="thead3"  style="position: sticky;top: 0">
                        <tr>
                            <th>Item Code<input class="form-control" type="hidden" name="Row_Count3" id ="Row_Count3"></th>
                            <th>Item Description</th>                                 
                            <th>Part NO</th>  
                            <th>UOM</th>
                            <th>Type</th>                               
                            <th>Produce Qty</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @if(!empty($objBYP))
                      @foreach($objBYP as $key => $row)  
                        <tr  class="participantRow2">
                        <td hidden>
                        <input  class="form-control" type="hidden" name={{"BOM_BYPID_".$key}} id ={{"BOM_BYPID_".$key}} maxlength="100" value="{{ $row->BOM_BYPID }}" autocomplete="off"   >
                                      </td>
                        <td><input {{$InputStatus}} type="text"  name={{"MainItemCode2_".$key}} id ={{"MainItemCode2_".$key}} value="{{ $row->ICODE }}"  onclick="get_item_byproduct($(this).attr('id'))" class="form-control"  autocomplete="off"  readonly/></td>                                                
                            <td hidden><input type="text" name={{"MainItemId2_Ref_".$key}} id ={{"MainItemId2_Ref_".$key}} value="{{ $row->ITEMID_REF }}"   class="form-control" autocomplete="off" />
                           
                            </td>
                            <td><input  {{$InputStatus}} type="text" name={{"MainItemName2_".$key}} id ={{"MainItemName2_".$key}} value="{{ $row->NAME }}"  class="form-control"  autocomplete="off"  readonly/></td>
                                                             
                           
                            <td><input {{$InputStatus}} type="text" name={{"MainItemPartno2_".$key}} id ={{"MainItemPartno2_".$key}} value="{{ $row->PARTNO }}"  class="form-control"  autocomplete="off"  readonly/></td> 
                            <td  hidden><input type="text" name="Mainuom2_Ref_{{$key}}" id="Mainuom2_Ref_{{$key}}" class="form-control" value="{{ $row->UOMID_REF }}"   autocomplete="off" /></td>
                                       
                            <td  ><input type="text" {{$InputStatus}} name={{"PACKUOM_".$key}} id ={{"PACKUOM_".$key}}  class="form-control" readonly  onclick="getUOM(this.id)"     value="{{ isset($row->UOMCODE1) ? $row->UOMCODE1.'-'.$row->DESCRIPTIONS1:''  }}"  /></td>
                            <td hidden ><input type="text" name={{"PACKUOMID_REF_".$key}} id ={{"PACKUOMID_REF_".$key}}  value="{{ $row->ALT_UOMID_REF }}"/></td>

                          <td>       
                          <select {{$InputStatus}} name="TYPE_0" id="TYPE_0" class="form-control mandatory">
                          <option value="">Select</option>
                          <option value="Scrap"  {{ isset($row->TYPE) && $row->TYPE=="Scrap" ? "selected":"" }} >Scrap</option>
                          <option value="Reusable" {{ isset($row->TYPE) && $row->TYPE=="Reusable" ? "selected":"" }}>Reusable</option>
                          <option value="Other" {{ isset($row->TYPE) && $row->TYPE=="Other" ? "selected":"" }}>Other</option>  
                          </select>  
                          </td>

                            <td style="width: 100px; text-align: center;" ><input {{$InputStatus}} type="text" name={{"PRODUCE_QTY2_".$key}} id ={{"PRODUCE_QTY2_".$key}} value="{{ $row->PRODUCE_QTY }}"  class="form-control" maxlength="200" autocomplete="off"  /></td>
                            <td align="center" ><button {{$InputStatus}} class="btn add " title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button><button {{$InputStatus}} class="btn remove bmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
                        
                        </tr>
                        <tr></tr>
                        @endforeach 
                 
                        @else
                        <tr  class="participantRow2">
                        <td><input {{$InputStatus}} type="text" name="MainItemCode2_0" id="MainItemCode2_0" onclick="get_item_byproduct($(this).attr('id'))" class="form-control"  autocomplete="off"  readonly/></td>                                                
                            <td hidden><input type="hidden" name="MainItemId2_Ref_0" id="MainItemId2_Ref_0" class="form-control" autocomplete="off" />
                            
                            </td>
                            <td><input {{$InputStatus}} type="text" name="MainItemName2_0" id="MainItemName2_0" class="form-control"  autocomplete="off"  readonly/></td>
                                                             
                           
                            <td><input {{$InputStatus}} type="text" name="MainItemPartno2_0" id="MainItemPartno2_0" class="form-control"  autocomplete="off"  readonly/></td> 
                            <td  hidden><input type="text" name="Mainuom2_Ref_0" id="Mainuom2_Ref_0" class="form-control" autocomplete="off" /></td>

                            <td  ><input type="text" name="PACKUOM_0" class="form-control" readonly  onclick="getUOM(this.id)"   id="PACKUOM_0"  value=""  /></td>
                                                    <td hidden ><input type="text" name="PACKUOMID_REF_0"    id="PACKUOMID_REF_0" value=""/></td>

                                                    <td>       
                                                    <select name="TYPE_0" id="TYPE_0" class="form-control mandatory">
                                                    <option value="">Select</option>
                                                    <option value="Scrap">Scrap</option>
                                                    <option value="Reusable">Reusable</option>
                                                    <option value="Other">Other</option>  
                                                    </select>  
                                                    </td>
                                                                                    
                            <td style="width: 100px; text-align: center;" ><input {{$InputStatus}} type="text" name="PRODUCE_QTY2_0" id="PRODUCE_QTY2_0" class="form-control" maxlength="200" autocomplete="off"  /></td>
                            <td align="center" ><button {{$InputStatus}} class="btn add " title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button><button {{$InputStatus}} class="btn remove bmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
                        
                        </tr>
                        <tr></tr>

                        @endif 
                    </tbody>
            </table>
            </div>	
        </div>
        

      
       <div id="udf" class="tab-pane fade">
            <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="margin-top:10px;height:280px;width:50%;">
                <table id="example5" class="display nowrap table table-striped table-bordered itemlist" style="height:auto !important;">
                    <thead id="thead4"  style="position: sticky;top: 0">
                    <tr >
                        <th>UDF Fields<input class="form-control" type="hidden" name="Row_Count4" id ="Row_Count4"></th>
                        <th>Value / Comments</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(!empty($objOSOUDF))
                                            @foreach($objOSOUDF as $Ukey => $Urow)
                                  <tr  class="participantRow3">
                                  <td hidden>
                                  <input  type="text" name="rowscount4[]"  />
                        <input  class="form-control" type="hidden" name={{"BOM_UDFID_".$Ukey}} id ={{"BOM_UDFID_".$Ukey}} maxlength="100" value="{{ $Urow->BOM_UDFID }}" autocomplete="off"   >
                                      </td>
                                   <td><input {{$InputStatus}} type="text" name={{"popupUDFBOMID_".$Ukey}} id={{"popupUDFBOMID_".$Ukey}}  class="form-control"  autocomplete="off"  readonly/></td>
                                   <td hidden><input type="hidden" name={{"UDFBOMID_REF_".$Ukey}}  id={{"UDFBOMID_REF_".$Ukey}} class="form-control" value="{{$Urow->UDFBOMID_REF}}" autocomplete="off" /></td>
                                    <td hidden><input type="hidden" name={{"UDFismandatory_".$Ukey}} id={{"UDFismandatory_".$Ukey}} class="form-control" autocomplete="off" /></td>
                                    <td id={{"udfinputid_".$Ukey}}>
                                       {{-- dynamic input --}} 
                                          </td>
                                          <td align="center" ><button class="btn add UDF" title="add" data-toggle="tooltip" type="button" disabled><i class="fa fa-plus"></i></button><button class="btn remove DUDF" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
                                                </tr>
                                                <tr></tr>

                                            @endforeach 
                                            @else
                                            @foreach($objUdfOSOData as $uindex=>$uRow)
                      <tr  class="participantRow3">
                          <td><input {{$InputStatus}} type="text" name={{"popupUDFBOMID_".$uindex}} id={{"popupUDFBOMID_".$uindex}} class="form-control" value="{{$uRow->LABEL}}" autocomplete="off"  readonly/></td>
                          <td hidden><input type="hidden" name={{"UDFBOMID_REF_".$uindex}} id={{"UDFBOMID_REF_".$uindex}} class="form-control" value="{{$uRow->UDFBOMID}}" autocomplete="off"   /></td>
                          <td hidden><input type="hidden" name={{"UDFismandatory_".$uindex}} id={{"UDFismandatory_".$uindex}} value="{{$uRow->ISMANDATORY}}" class="form-control"   autocomplete="off" />
                          <input type="text" name="rowscount4[]"  />
                          </td>
                          <td id={{"udfinputid_".$uindex}} >
                          </td>
                          <td align="center" ><button class="btn addudf UDF" title="add" data-toggle="tooltip" type="button" disabled><i class="fa fa-plus"></i></button><button class="btn removeudf DUDF" title="Delete" data-toggle="tooltip" type="button" ><i class="fa fa-trash" ></i></button></td>
                      </tr>
                      <tr></tr>
                    @endforeach  
                    @endif 

                    </tbody>
                </table>
            </div>
        </div>

<div id="direct_cost" class="tab-pane fade">
            <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="margin-top:10px;height:280px;width:50%;">
                <table id="example6" class="display nowrap table table-striped table-bordered itemlist" style="height:auto !important;">
                <thead id="thead5"  style="position: sticky;top: 0">
                        <tr>
                            <th>Cost Component(s)<input class="form-control" type="hidden" name="Row_Count5" id ="Row_Count5"></th>
                            <th>Value</th>                            
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @if(!empty($objOTH))
                      @foreach($objOTH as $key => $row)  
                        <tr  class="participantRow10">
                        <td hidden>
                        <input  class="form-control" type="hidden" name={{"BOM_OTHID_".$key}} id ={{"BOM_OTHID_".$key}} maxlength="100" value="{{ $row->BOM_OTHID }}" autocomplete="off"   >
                                      </td>
                            <td><input {{$InputStatus}} type="text" name={{"Componentname_".$key}} id ={{"Componentname_".$key}} value="{{ $row->CCOMPONENT_CODE.'-'.$row->DESCRIPTIONS }}"   onclick="get_item_component($(this).attr('id'))" class="form-control"  autocomplete="off"  readonly/></td>                                                
                            <td hidden><input type="hidden" name={{"Componentid_".$key}} id ={{"Componentid_".$key}} maxlength="100" value="{{ $row->CCOMPONENTID_REF }}"   class="form-control" autocomplete="off" />
                            <input type="text" name="rowscount5[]"  />
                            </td>
                           
                     
                            <td><input {{$InputStatus}} type="text" name={{"value_".$key}} id ={{"value_".$key}} maxlength="100" value="{{ $row->VALUE }}"  class="form-control"  autocomplete="off"  /></td>
                            <td align="center" ><button {{$InputStatus}} class="btn add dcmaterial" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button><button {{$InputStatus}} class="btn remove dcmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
                        
                        </tr>
                        <tr></tr>
                        @endforeach 
                        @else

                        <tr  class="participantRow10">
                            <td><input {{$InputStatus}} type="text" name="Componentname_0" id="Componentname_0" onclick="get_item_component($(this).attr('id'))" class="form-control"  autocomplete="off"  readonly/></td>                                                
                            <td hidden><input type="hidden" name="Componentid_0" id="Componentid_0" class="form-control" autocomplete="off" />
                            <input type="text" name="rowscount5[]"  />
                            </td>
                           
                     
                            <td><input {{$InputStatus}} type="text" name="value_0" id="value_0" class="form-control"  autocomplete="off"  /></td>
                            <td align="center" ><button {{$InputStatus}} class="btn add dcmaterial" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button><button {{$InputStatus}} class="btn remove dcmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
                        
                        </tr>
                        <tr></tr>


                        @endif 
                    </tbody>
                </table>
            </div>
        </div>

        
        <div id="instruction" class="tab-pane fade">
            <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="margin-top:10px;height:280px;width:50%;">
           <textarea {{$InputStatus}} name="instruction" id="instruction" cols="85" rows="8">{{ isset($objBOM->SPECIAL_INSTRUCTION)?$objBOM->SPECIAL_INSTRUCTION:'' }}</textarea>
            </div>
        </div>

        
    </div>
</div>
</div>
                </div>
        
    </div><!--purchase-order-view-->

<!-- </div> -->
</form>
@endsection
@section('alert')



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

<!-- production stage Dropdown -->
<div id="production_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='production_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Production Stage List</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="fy_table1" class="display nowrap table  table-striped table-bordered" >
        <thead>
          <th class="ROW1" >Select</th> 
          <th class="ROW2">Code</th>
          <th  class="ROW3">Name</th>
        </thead>
        <tbody>
  
        <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="fy_codesearch" class="form-control" onkeyup="searchProductionCode()"></td>
        <td class="ROW3"><input type="text" id="fy_namesearch" class="form-control" onkeyup="searchProductionName()"></td>
      </tr>
        </tbody>
      </table>


      <table id="fy_table2" class="display nowrap table  table-striped table-bordered" width="100%">
      <thead id="thead2">
          
          </thead>
        <tbody id="fy_body">
        @foreach ($production_stage as $productionindex=>$productionRow)
        <tr >
          <td class="ROW1" style="text-align: center"> <input type="checkbox" name="SELECT_PRODUCTION_STAGE[]"  id="fycode_{{ $productionindex }}" class="productcls" value="{{ $productionRow-> PSTAGEID }}" ></td>
          <td  class="ROW2">{{ $productionRow-> PSTAGE_CODE }}
          <input type="hidden" id="txtfycode_{{ $productionindex }}" data-desc="{{ $productionRow-> PSTAGE_CODE }} - {{ $productionRow-> DESCRIPTIONS }}"  
          value="{{ $productionRow-> PSTAGEID }}"/></td>
          <td class="ROW3">{{ $productionRow-> DESCRIPTIONS }}</td>
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

<!-- ITEM Dropdown For Header Section Product Code  -->
<div id="proidpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md " style="width:90%;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='gl_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Item List</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="ITEMProCodeTable" class="display nowrap table  table-striped table-bordered"style="width:100%;">
    <thead>
    <tr>
      <th style="width:5%;text-align:center;" id="all-check_prodcode_item">Select</th>
      <th style="width:10%;">Product Code</th>
      <th style="width:15%;">Product Description</th>
      <th style="width:10%;">UOM</th>
      <th style="width:10%;">Business Unit</th>
      <th style="width:10%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</th>
      <th style="width:10%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</th>
      <th style="width:10%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</th>
      <th style="width:10%;">Drawing No</th>
      <th style="width:10%;">Part No</th>
    </tr>
    </thead>
    <tbody>
    <tr>
      <td style="width:5%;text-align:center;"><span class="check_th">&#10004;</span></td>
      <td style="width:10%;"><input type="text" id="ItemProCodeSearch" class="form-control" onkeyup="ItemProCodeFunction('{{$FormId}}')" ></td>
      <td style="width:15%;"><input type="text" id="ItemProCodeNameSearch" class="form-control" onkeyup="ItemProCodeNameFunction('{{$FormId}}')" ></td>
      <td style="width:10%;"><input type="text" id="ItemProCodeUOMSearch"  class="form-control" onkeyup="ItemProCodeUOMFunction('{{$FormId}}')" ></td>
      <td style="width:10%;"><input type="text" id="ItemProCodeBUsearch" class="form-control" onkeyup="ItemProCodeBUFunction('{{$FormId}}')" ></td>
      <td style="width:10%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemProCodeAPNsearch" class="form-control" onkeyup="ItemProCodeAPNFunction('{{$FormId}}')" ></td>
      <td style="width:10%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemProCodeCPNsearch" class="form-control"  onkeyup="ItemProCodeCPNFunction('{{$FormId}}')" ></td>
      <td style="width:10%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemProCodeOEMPNsearch" class="form-control" onkeyup="ItemProCodeOEMPNFunction('{{$FormId}}')" ></td>
      <td style="width:10%;"><input type="text" id="ItemProCodeDrawingNoSearch" class="form-control" onkeyup="ItemProCodeDrawingNoFunction('{{$FormId}}')" ></td>
      <td style="width:10%;"><input type="text" id="ItemProCodePartNoSearch" class="form-control"  onkeyup="ItemProCodePartNoFunction('{{$FormId}}')"></td>
    </tr>
     </tbody>
    </table>
      <table id="ITEMProCodeTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2">
        </thead>
        <tbody id="tbody_prod_code" style="font-size:12px;" >  
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Item Header Section Product Code Dropdown ends-->

<!-- ITEM Dropdown For substitute tab Section  -->

<div id="mainitempopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md"  style="width:90%;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='item_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Main Item List</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="MItemTable" class="display nowrap table  table-striped table-bordered" style="width:100%;">
    <thead>
      <tr id="none-select" class="searchalldata" hidden>              
        <td>
          <input type="text" name="mfieldid" id="hdn_MItemID"/>
          <input type="text" name="mfieldid2" id="hdn_MItemID2"/>
          <input type="text" name="mfieldid3" id="hdn_MItemID3"/>
          <input type="text" name="mfieldid4" id="hdn_MItemID4"/>
          <input type="text" name="mfieldid5" id="hdn_MItemID5"/>
        </td>
      </tr>
      <tr>
        <th style="width:5%;text-align:center;" id="all-check_prodcode_item">Select</th>
        <th style="width:10%;">Product Code</th>
        <th style="width:15%;">Product Description</th>
        <th style="width:10%;">UOM</th>
        <th style="width:10%;">Business Unit</th>
        <th style="width:10%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</th>
        <th style="width:10%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</th>
        <th style="width:10%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</th>
        <th style="width:10%;">Drawing No</th>
        <th style="width:10%;">Part No</th>
      </tr>
    </thead>
    <tbody>
    <tr>
      <td style="width:5%;text-align:center;"><span class="check_th">&#10004;</span></td>
      <td style="width:10%;"><input type="text" id="MItemCodeSearch" class="form-control" onkeyup="MItemCodeFunction('{{$FormId}}')" ></td>
      <td style="width:15%;"><input type="text" id="MItemNameSearch" class="form-control" onkeyup="MItemNameFunction('{{$FormId}}')" ></td>
      <td style="width:10%;"><input type="text" id="MItemUOMSearch"  class="form-control" onkeyup="MItemUOMFunction('{{$FormId}}')" ></td>
      <td style="width:10%;"><input type="text" id="MItemBUsearch" class="form-control" onkeyup="MItemBUFunction('{{$FormId}}')" ></td>
      <td style="width:10%;" {{$AlpsStatus['hidden']}} ><input type="text" id="MItemAPNsearch" class="form-control" onkeyup="MItemAPNFunction('{{$FormId}}')" ></td>
      <td style="width:10%;" {{$AlpsStatus['hidden']}} ><input type="text" id="MItemCPNsearch" class="form-control"  onkeyup="MItemCPNFunction('{{$FormId}}')" ></td>
      <td style="width:10%;" {{$AlpsStatus['hidden']}} ><input type="text" id="MItemOEMPNsearch" class="form-control" onkeyup="MItemOEMPNFunction('{{$FormId}}')" ></td>
      <td style="width:10%;"><input type="text" id="MItemDrawingNoSearch" class="form-control" onkeyup="MItemDrawingNoFunction('{{$FormId}}')" ></td>
      <td style="width:10%;"><input type="text" id="MItemPartNoSearch" class="form-control"  onkeyup="MItemPartNoFunction('{{$FormId}}')"></td>
    </tr>
    </tbody>
    </table>
      <table id="MItemTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2">
        </thead>
        <tbody id="tbody_main_item" style="font-size:12px;">
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<!-- Item dropdown in substitute tab substitute items  Dropdown starts-->

<!-- ITEM Dropdown For substitute tab Section  -->

<div id="mainitempopup1" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:90%;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='item_closePopup1' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Substitute Item List</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="SubsItemCodeTable" class="display nowrap table  table-striped table-bordered" style="width:100%;">
    <thead>
      <tr id="none-select" class="searchalldata" hidden>              
        <td>
          <input type="text" name="Subsfieldid" id="hdn_SubsItemID"/>
          <input type="text" name="Subsfieldid2" id="hdn_SubsItemID2"/>
          <input type="text" name="Subsfieldid3" id="hdn_SubsItemID3"/>
          <input type="text" name="Subsfieldid4" id="hdn_SubsItemID4"/>
          <input type="text" name="Subsfieldid5" id="hdn_SubsItemID5"/>
          <input type="text" name="Subsfieldid6" id="hdn_SubsItemID6"/>
        </td>
      </tr>
      <tr>
          <th style="width:5%;text-align:center;" id="all-check_subs_item">Select</th>
          <th style="width:10%;">Product Code</th>
          <th style="width:15%;">Product Description</th>
          <th style="width:10%;">UOM</th>
          <th style="width:10%;">Business Unit</th>
          <th style="width:10%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</th>
          <th style="width:10%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</th>
          <th style="width:10%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</th>
          <th style="width:10%;">Drawing No</th>
          <th style="width:10%;">Part No</th>
      </tr>
    </thead>
    <tbody>
    <tr>
      <td style="width:5%;text-align:center;"><span class="check_th">&#10004;</span></td>
      <td style="width:10%;"><input type="text" id="SubsItemCodeSearch" class="form-control" onkeyup="SubsItemCodeFunction('{{$FormId}}')" ></td>
      <td style="width:15%;"><input type="text" id="SubsItemNameSearch" class="form-control" onkeyup="SubsItemNameFunction('{{$FormId}}')" ></td>
      <td style="width:10%;"><input type="text" id="SubsItemUOMSearch"  class="form-control" onkeyup="SubsItemUOMFunction('{{$FormId}}')" ></td>
      <td style="width:10%;"><input type="text" id="SubsItemBUsearch" class="form-control" onkeyup="SubsItemBUFunction('{{$FormId}}')" ></td>
      <td style="width:10%;" {{$AlpsStatus['hidden']}} ><input type="text" id="SubsItemAPNsearch" class="form-control" onkeyup="SubsItemAPNFunction('{{$FormId}}')" ></td>
      <td style="width:10%;" {{$AlpsStatus['hidden']}} ><input type="text" id="SubsItemCPNsearch" class="form-control"  onkeyup="SubsItemCPNFunction('{{$FormId}}')" ></td>
      <td style="width:10%;" {{$AlpsStatus['hidden']}} ><input type="text" id="SubsItemOEMPNsearch" class="form-control" onkeyup="SubsItemOEMPNFunction('{{$FormId}}')" ></td>
      <td style="width:10%;"><input type="text" id="SubsItemDrawingNoSearch" class="form-control" onkeyup="SubsItemDrawingNoFunction('{{$FormId}}')" ></td>
      <td style="width:10%;"><input type="text" id="SubsItemPartNoSearch" class="form-control"  onkeyup="SubsItemPartNoFunction('{{$FormId}}')"></td>

    </tr>
    </tbody>
    </table>
      <table id="SubsItemCodeTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2">
        </thead>
        <tbody id="tbody_subs_item" style="font-size:12px;" >
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Item dropdown in substitute tab substitute  Dropdown ends-->
<!--By product item section starts-->

<div id="mainitempopup2" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:90%;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='item_closePopup2' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Item List</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="ByProItemCodeTable" class="display nowrap table  table-striped table-bordered" style="width:100%;">
    <thead>
      <tr id="none-select" class="searchalldata" hidden >              
        <td>
          <input type="text" name="ByProfieldid" id="hdn_ByProItemID"/>
          <input type="text" name="ByProfieldid2" id="hdn_ByProItemID2"/>
          <input type="text" name="ByProfieldid3" id="hdn_ByProItemID3"/>
          <input type="text" name="ByProfieldid4" id="hdn_ByProItemID4"/>
          <input type="text" name="ByProfieldid5" id="hdn_ByProItemID5"/>
          <input type="text" name="ByProfieldid6" id="hdn_ByProItemID6"/>
        </td>
      </tr>
      <tr>
          <th style="width:5%;text-align:center;" id="all-check_by_pro_item">Select</th>
          <th style="width:10%;">Product Code</th>
          <th style="width:15%;">Product Description</th>
          <th style="width:10%;">UOM</th>
          <th style="width:10%;">Business Unit</th>
          <th style="width:10%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</th>
          <th style="width:10%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</th>
          <th style="width:10%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</th>
          <th style="width:10%;">Drawing No</th>
          <th style="width:10%;">Part No</th>
      </tr>
    </thead>
    <tbody>
    <tr>
      <td style="width:5%;text-align:center;"><span class="check_th">&#10004;</span></td>
      <td style="width:10%;"><input type="text" id="ByProItemCodeSearch" class="form-control" onkeyup="ByProItemCodeFunction('{{$FormId}}')" ></td>
      <td style="width:15%;"><input type="text" id="ByProItemNameSearch" class="form-control" onkeyup="ByProItemNameFunction('{{$FormId}}')" ></td>
      <td style="width:10%;"><input type="text" id="ByProItemUOMSearch"  class="form-control" onkeyup="ByProItemUOMFunction('{{$FormId}}')" ></td>
      <td style="width:10%;"><input type="text" id="ByProItemBUsearch" class="form-control" onkeyup="ByProItemBUFunction('{{$FormId}}')" ></td>
      <td style="width:10%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ByProItemAPNsearch" class="form-control" onkeyup="ByProItemAPNFunction('{{$FormId}}')" ></td>
      <td style="width:10%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ByProItemCPNsearch" class="form-control"  onkeyup="ByProItemCPNFunction('{{$FormId}}')" ></td>
      <td style="width:10%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ByProItemOEMPNsearch" class="form-control" onkeyup="ByProItemOEMPNFunction('{{$FormId}}')" ></td>
      <td style="width:10%;"><input type="text" id="ByProItemDrawingNoSearch" class="form-control" onkeyup="ByProItemDrawingNoFunction('{{$FormId}}')" ></td>
      <td style="width:10%;"><input type="text" id="ByProItemPartNoSearch" class="form-control"  onkeyup="ByProItemPartNoFunction('{{$FormId}}')"></td>

    </tr>
    </tbody>
    </table>
      <table id="ByProItemCodeTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead3">
         </thead>
        <tbody  id="tbody_by_pro" style="font-size:12px;" >
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<!--By product item section ends-->

<!-- Item Header Section  Dropdown ends-->

<!-- Item Code Dropdown -->

<div id="ITEMIDpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md"  style="width:90%;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='ITEMID_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Item Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="ItemIDTable" class="display nowrap table  table-striped table-bordered" style="width:100%;">
    <thead>
      <tr id="none-select" class="searchalldata" >            
            <td hidden> 
              <input type="text" name="fieldid" id="hdn_ItemID"/>
              <input type="text" name="fieldid2" id="hdn_ItemID2"/>
              <input type="text" name="fieldid3" id="hdn_ItemID3"/>
              <input type="text" name="fieldid4" id="hdn_ItemID4"/>
              <input type="text" name="fieldid5" id="hdn_ItemID5"/>
              <input type="text" name="fieldid6" id="hdn_ItemID6"/>
              <input type="text" name="fieldid7" id="hdn_ItemID7"/>
            </td>
      </tr>
      <tr>
            <th id="all-check" style="width:10%;text-align:center;" >Select</th>
            <th style="width:10%;">Item Code</th>
            <th style="width:8%;">Name</th>
            <th style="width:8%;">Part No</th>
            <th style="width:8%;">Main UOM</th>
            <th style="width:8%;">Main QTY</th>
            <th style="width:8%;">Item Group</th>
            <th style="width:8%;">Item Category</th>
            
            <th style="width:8%;">Business Unit</th>
            <th style="width:8%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</th>
            <th style="width:8%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</th>
            <th style="width:8%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</th>
           
      </tr>      
    </thead>
    <tbody>
    <tr>
      <td style="width:10%;text-align:center;"><span class="check_th">&#10004;</span></td>
      <td style="width:10%;"><input type="text" id="Itemcodesearch" class="form-control" onkeyup="ItemCodeFunction('{{$FormId}}')"></td>
      <td style="width:8%;"><input type="text" id="Itemnamesearch" class="form-control" onkeyup="ItemNameFunction('{{$FormId}}')"></td>
      <td style="width:8%;"><input type="text" id="Itempartsearch" class="form-control"  onkeyup="ItemPartnoFunction('{{$FormId}}')"></td>
      <td style="width:8%;"><input type="text" id="ItemUOMsearch" class="form-control" onkeyup="ItemUOMFunction('{{$FormId}}')"></td>
      <td style="width:8%;"><input type="text" id="ItemQTYsearch" class="form-control" onkeyup="ItemQTYFunction()"></td>
      <td style="width:8%;"><input type="text" id="ItemGroupsearch" class="form-control" onkeyup="ItemGroupFunction('{{$FormId}}')"></td>
      <td style="width:8%;"><input type="text" id="ItemCategorysearch" class="form-control" onkeyup="ItemCategoryFunction('{{$FormId}}')"></td>
      <td style="width:8%;"><input type="text" id="ItemBUsearch" class="form-control" onkeyup="ItemBUFunction('{{$FormId}}')"></td>
      <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemAPNsearch" class="form-control" onkeyup="ItemAPNFunction('{{$FormId}}')"></td>
      <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemCPNsearch" class="form-control" onkeyup="ItemCPNFunction('{{$FormId}}')"></td>
      <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemOEMPNsearch" class="form-control" onkeyup="ItemOEMPNFunction('{{$FormId}}')"></td>
      
    </tr>
    </tbody>
    </table>
      <table id="ItemIDTable2" class="display nowrap table  table-striped table-bordered" style="width:100%" >
        <thead id="thead2">
        </thead>
        <tbody id="tbody_ItemID" style="font-size:12px;">     
          
          
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Item Code Dropdown-->

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

<!-- UDF Dropdown -->
<div id="udfsoidpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='udfsoid_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>UDF Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="UDFSOIDTable" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
    <tr>
            <th>Label</th>
            <th>Value Type</th>
    </tr>
    <tr hidden>
            <input type="hidden" name="fieldid" id="hdn_UDFSOID"/>
    </tr>
    </thead>
    <tbody>
    <tr>
    <td>
    <input type="text" id="UDFSOIDcodesearch" onkeyup="UDFSOIDCodeFunction()">
    </td>
    <td>
    <input type="text" id="UDFSOIDnamesearch" onkeyup="UDFSOIDNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="UDFSOIDTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
        </thead>
        <tbody id="tbody_udfsoid"> 
        @foreach ($objUdfOSOData as $udfindex=>$udfRow)
        <tr id="udfsoid_{{ $udfindex }}" class="clsudfsoid">
          <td width="50%">{{ $udfRow->LABEL }}
          <input type="hidden" id="txtudfsoid_{{ $udfindex }}" data-desc="{{ $udfRow->LABEL }}"  value="{{ $udfRow->UDFBOMID }}"/>
          </td>
          <td id="udfvalue_{{ $udfindex }}">{{ $udfRow-> VALUETYPE }}
          <input type="hidden" id="txtudfvalue__{{ $udfindex }}" data-desc="{{ $udfRow->DESCRIPTIONS }}"  
          value="{{ $udfRow->ISMANDATORY }}"/></td>
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
@endsection

@push('bottom-scripts')
<script>
$(document).ready(function() {

  var count1 = <?php echo json_encode($objCount1); ?>;
  $('#Row_Count1').val(count1);

  var count2 = <?php echo json_encode($objCount2); ?>;
  $('#Row_Count2').val(count2);

  var count3 = <?php echo json_encode($objCount3); ?>;
  $('#Row_Count3').val(count3);

  var count4 = <?php echo json_encode($objCount4); ?>;
  $('#Row_Count4').val(count4);

  var count5 = <?php echo json_encode($objCount5); ?>;
  $('#Row_Count5').val(count5);

  var soudf = <?php echo json_encode($objOSOUDF); ?>;
    var udfforso = <?php echo json_encode($objUdfOSOData2); ?>;
    
    
    $.each( soudf, function( soukey, souvalue ) {
        $.each( udfforso, function( usokey, usovalue ) { 
            if(souvalue.SOUDFID_REF == usovalue.UDFID)
            {
                $('#popupUDFBOMID_'+soukey).val(usovalue.LABEL);
            }
        
            if(souvalue.SOUDFID_REF == usovalue.UDFID)
            {        
                    var txtvaltype2 =   usovalue.VALUETYPE;
                    var txt_id41 = $('#udfinputid_'+soukey).attr('id');
                    var strdyn2 = txt_id41.split('_');
                    var lastele2 =   strdyn2[strdyn2.length-1];
                    var dynamicid2 = "udfvalue_"+lastele2;
                    
                    var chkvaltype2 =  txtvaltype2.toLowerCase();
                    var strinp2 = '';

                    if(chkvaltype2=='date'){

                    strinp2 = '<input {{$InputStatus}} type="date" placeholder="dd/mm/yyyy" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"  > ';       

                    }
                    else if(chkvaltype2=='time'){
                    strinp2= '<input {{$InputStatus}} type="time" placeholder="h:i" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"  > ';

                    }
                    else if(chkvaltype2=='numeric'){
                    strinp2 = '<input {{$InputStatus}} type="text" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"   > ';

                    }
                    else if(chkvaltype2=='text'){

                    strinp2 = '<input {{$InputStatus}} type="text" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"  > ';
                    
                    }
                    else if(chkvaltype2=='boolean'){
                      if(souvalue.VALUE == "1")
                      {
                        strinp2 = '<input {{$InputStatus}} type="checkbox" name="'+dynamicid2+ '" id="'+dynamicid2+'" class="" checked> ';
                      }
                      else{
                        strinp2 = '<input {{$InputStatus}} type="checkbox" name="'+dynamicid2+ '" id="'+dynamicid2+'" class="" > ';
                      }
                    }
                    else if(chkvaltype2=='combobox'){

                    var txtoptscombo2 =   usovalue.DESCRIPTIONS;
                    var strarray2 = txtoptscombo2.split(',');
                    var opts2 = '';

                    for (var i = 0; i < strarray2.length; i++) {
                        opts2 = opts2 + '<option value="'+strarray2[i]+'">'+strarray2[i]+'</option> ';
                    }

                    strinp2 = '<select {{$InputStatus}} name="'+dynamicid2+ '" id="'+dynamicid2+'" class="form-control" required>'+opts2+'</select>' ;
                   
                    }
                   
                    
                    $('#'+txt_id41).html('');  
                    $('#'+txt_id41).html(strinp2);   //set dynamic input
                    $('#'+dynamicid2).val(souvalue.VALUE);
                    $('#UDFismandatory_'+soukey).val(usovalue.ISMANDATORY); // mandatory
                
            }
        });
    });


  $("[id*='CONSUMEQTY']").ForceNumericOnly();


$('#example2').on('blur','[id*="CONSUMEQTY"]',function(){

          if(intRegex.test($(this).val())){
           $(this).val($(this).val()+'.000')
          }
          event.preventDefault();
      });

      $("[id*='PRODUCTION']").ForceNumericOnly();
      $('#example2').on('blur','[id*="PRODUCTION"]',function(){
          if(intRegex.test($(this).val())){
           $(this).val($(this).val()+'.000')
          }
          event.preventDefault();
      });
      $("[id*='SCRAP']").ForceNumericOnly();
      $('#example2').on('blur','[id*="SCRAP"]',function(){
          if(intRegex.test($(this).val())){
           $(this).val($(this).val()+'.000')
          }
          event.preventDefault();
      });

      $("[id*='CONSUMEQTY1']").ForceNumericOnly();

$('#example3').on('blur','[id*="CONSUMEQTY1"]',function(){
          if(intRegex.test($(this).val())){
           $(this).val($(this).val()+'.000')
          }
          event.preventDefault();
      });
      $("[id*='PRODUCTION1']").ForceNumericOnly();
      $('#example3').on('blur','[id*="PRODUCTION1"]',function(){
          if(intRegex.test($(this).val())){
           $(this).val($(this).val()+'.000')
          }
          event.preventDefault();
      });
      $("[id*='SCRAP1']").ForceNumericOnly();
      $('#example3').on('blur','[id*="SCRAP1"]',function(){
          if(intRegex.test($(this).val())){
           $(this).val($(this).val()+'.000')
          }
          event.preventDefault();
      });
      $("[id*='PRODUCE_QTY2']").ForceNumericOnly();
      $('#example4').on('blur','[id*="PRODUCE_QTY2"]',function(){
          if(intRegex.test($(this).val())){
           $(this).val($(this).val()+'.000')
          }
          event.preventDefault();
      });
      $("[id^='value_']").ForceNumericOnly();
      $('#example6').on('blur','[id^="value_"]',function(){
          if(intRegex.test($(this).val())){
           $(this).val($(this).val()+'.00')
          }
          event.preventDefault();
      });
      $("[id*='PRODUCEQTY']").ForceNumericOnly();
      $('#PRODUCEQTY').on('blur',function(){
          if(intRegex.test($(this).val())){
           $(this).val($(this).val()+'.000')
          }
          event.preventDefault();
      });


});

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
      //Header pro code Item Section 
      let tid = "#ITEMProCodeTable2";
      let tid2 = "#ITEMProCodeTable";
      let headers = document.querySelectorAll(tid2 + " th");

      // Sort the table element when clicking on the table headers
      headers.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(tid, ".clsglid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function ItemProCodeFunction(FORMID) {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("ItemProCodeSearch");
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
        var DRAWNO = ''; 
        var PARTNO = ''; 
        loadItem_prod_code(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
        var DRAWNO = ''; 
        var PARTNO = ''; 
        loadItem_prod_code(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
      }
      else
      {
        table = document.getElementById("ITEMProCodeTable2");
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

    function ItemProCodeNameFunction(FORMID) {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("ItemProCodeNameSearch");
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
        var DRAWNO = ''; 
        var PARTNO = ''; 
        loadItem_prod_code(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
        var DRAWNO = ''; 
        var PARTNO = ''; 
        loadItem_prod_code(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
      }
      else
      {
        table = document.getElementById("ITEMProCodeTable2");
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

    function ItemProCodeUOMFunction(FORMID) {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("ItemProCodeUOMSearch");
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
        var DRAWNO = ''; 
        var PARTNO = ''; 
        loadItem_prod_code(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
        var DRAWNO = ''; 
        var PARTNO = ''; 
        loadItem_prod_code(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
      }
      else
      {
        table = document.getElementById("ITEMProCodeTable2");
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

    function ItemProCodeBUFunction(FORMID) {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("ItemProCodeBUsearch");
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
        var DRAWNO = ''; 
        var PARTNO = ''; 
        loadItem_prod_code(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
        var DRAWNO = ''; 
        var PARTNO = ''; 
        loadItem_prod_code(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
      }
      else
      {
        table = document.getElementById("ITEMProCodeTable2");
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

    function ItemProCodeAPNFunction(FORMID) {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("ItemProCodeAPNsearch");
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
        var DRAWNO = ''; 
        var PARTNO = ''; 
        loadItem_prod_code(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
        var CTGRY =''; 
        var BUNIT = ''; 
        var APART = filter; 
        var CPART = ''; 
        var OPART = ''; 
        var DRAWNO = ''; 
        var PARTNO = ''; 
        loadItem_prod_code(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
      }
      else
      {
        table = document.getElementById("ITEMProCodeTable2");
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
    function ItemProCodeCPNFunction(FORMID) {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("ItemProCodeCPNsearch");
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
        var DRAWNO = ''; 
        var PARTNO = ''; 
        loadItem_prod_code(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
        var CTGRY =''; 
        var BUNIT = ''; 
        var APART = ''; 
        var CPART = filter; 
        var OPART = ''; 
        var DRAWNO = ''; 
        var PARTNO = ''; 
        loadItem_prod_code(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
      }
      else
      {
        table = document.getElementById("ITEMProCodeTable2");
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

    function ItemProCodeOEMPNFunction(FORMID) {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("ItemProCodeOEMPNsearch");
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
        var DRAWNO = ''; 
        var PARTNO = ''; 
        loadItem_prod_code(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
        var CTGRY =''; 
        var BUNIT = ''; 
        var APART = ''; 
        var CPART =''; 
        var OPART = filter; 
        var DRAWNO = ''; 
        var PARTNO = ''; 
        loadItem_prod_code(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
      }
      else
      {
        table = document.getElementById("ITEMProCodeTable2");
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

    
    function ItemProCodeDrawingNoFunction(FORMID) {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("ItemProCodeDrawingNoSearch");
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
        var DRAWNO = ''; 
        var PARTNO = ''; 
        loadItem_prod_code(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
        var CTGRY =''; 
        var BUNIT = ''; 
        var APART = ''; 
        var CPART =''; 
        var OPART =''; 
        var DRAWNO = filter; 
        var PARTNO = ''; 
        loadItem_prod_code(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
      }
      else
      {
        table = document.getElementById("ITEMProCodeTable2");
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



    function ItemProCodePartNoFunction(FORMID) {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("ItemProCodePartNoSearch");
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
        var DRAWNO = ''; 
        var PARTNO = ''; 
        loadItem_prod_code(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
        var MUOM = '' ; 
        var GROUP = ''; 
        var CTGRY = ''; 
        var BUNIT = ''; 
        var APART = ''; 
        var CPART = ''; 
        var OPART = ''; 
        var DRAWNO =''; 
        var PARTNO = filter; 
        loadItem_prod_code(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
      }
      else
      {
        table = document.getElementById("ITEMProCodeTable2");
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

    
  function loadItem_prod_code(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO){
	
    var url	=	'<?php echo asset('');?>master/'+FORMID+'/getItemDetails_prod_code';

      $("#tbody_prod_code").html('loading...');
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      $.ajax({
        url:url,
        type:'POST',
        data:{'taxstate':taxstate,'CODE':CODE,'NAME':NAME,'MUOM':MUOM,'GROUP':GROUP,'CTGRY':CTGRY,'BUNIT':BUNIT,'APART':APART,'CPART':CPART,'OPART':OPART,'DRAWNO':DRAWNO,'PARTNO':PARTNO},
        success:function(data) {
        $("#tbody_prod_code").html(data); 
        bindProdCode();

        },
        error:function(data){
          console.log("Error: Something went wrong.");
          $("#tbody_prod_code").html('');                        
        },
      });

  }

  //PROD CODE popoup click
  $('#txtitem_popup').click(function(event){

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
    var FORMID = "{{$FormId}}";
    var DRAWNO = '';
    var PARTNO = '';
    loadItem_prod_code(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 

     $("#proidpopup").show();
    event.preventDefault();
  });

  $("#gl_closePopup").click(function(event){
    
    
    $("#ItemProCodeSearch").val(''); 
    $("#ItemProCodeNameSearch").val(''); 
    $("#ItemProCodeUOMSearch").val(''); 
    $("#ItemProCodeDrawingNoSearch").val(''); 
    $("#ItemProCodePartNoSearch").val(''); 
    $("#ItemProCodeBUsearch").val(''); 
    $("#ItemProCodeAPNsearch").val(''); 
    $("#ItemProCodeCPNsearch").val(''); 
    $("#ItemProCodeOEMPNsearch").val(''); 

    $("#proidpopup").hide();
    event.preventDefault();
  });

  function bindProdCode(){

$('#ITEMSCodeTable2').off(); 
$('[id*="chkIdProdCode"]').change(function(){
  var fieldid = $(this).parent().parent().attr('id');
  var txtval =    $("#txt"+fieldid+"").val();
  var name =   $("#txt"+fieldid+"").data("name");
  var code =   $("#txt"+fieldid+"").data("code");
  var drawingno =   $("#txt"+fieldid+"").data("drawingno");
  var uom =   $("#txt"+fieldid+"").data("uom");
  var uomno =   $("#txt"+fieldid+"").data("uomno");
  var partno =   $("#txt"+fieldid+"").data("partno");
  var toqty =   $("#txt"+fieldid+"").data("toqty");



  if(getExistProductCode('',txtval) > 0){
    $("#chkIdProdCode"+txtval).prop('checked', false);
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('The Product Code Is Already Exist.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else{
    $('#txtitem_popup').val(code);
    $('#PRODUCT_CODE').val(txtval);
    $("#UOMID_REF_DESC").val(uom); 
    $("#UOMID_REF").val(uomno); 
    $("#DRAWINGNO").val(drawingno); 
    $("#DESCRIPTION").val(name); 
    $("#PART_NO").val(partno); 
    $("#ALT_QTY").val(toqty); 
    
    $("#ItemProCodeSearch").val(''); 
    $("#ItemProCodeNameSearch").val(''); 
    $("#ItemProCodeUOMSearch").val(''); 
    $("#ItemProCodeDrawingNoSearch").val(''); 
    $("#ItemProCodePartNoSearch").val(''); 
    $("#ItemProCodeBUsearch").val(''); 
    $("#ItemProCodeAPNsearch").val(''); 
    $("#ItemProCodeCPNsearch").val(''); 
    $("#ItemProCodeOEMPNsearch").val(''); 
    $("#proidpopup").hide();
  }
  event.preventDefault();
  });
}
    

//ITEM Ends
//item prod code
// ------------------------------------- 


// PRODUCTION STAGE popup function

$("#PRODUCTION_STAGE_POPUP").on("click",function(event){ 
  $("#production_popup").show();
  showSelectedCheck($("#PRODUCTION_STAGE").val(),"SELECT_PRODUCTION_STAGE");
});

$("#PRODUCTION_STAGE_POPUP").keyup(function(event){
  if(event.keyCode==13){
    $("#PRODUCTION_STAGE_POPUP").show();
  }
});

$("#production_close").on("click",function(event){ 
  $("#production_popup").hide();
});

$('.productcls').click(function(){


  var fieldid          =   $(this).attr('id');
  var txtval      =   $("#txt"+fieldid+"").val();
  var texdesc     =   $("#txt"+fieldid+"").data("desc");
  var texdescname =   $("#txt"+fieldid+"").data("descname");


  $("#PRODUCTION_STAGE_POPUP").val(texdesc);
  $("#PRODUCTION_STAGE").val(txtval);

  $("#PRODUCTION_STAGE_POPUP").blur(); 

  $("#production_popup").hide();
  $("#fy_codesearch").val('');
  $("#fy_namesearch").val('');
  searchProductionCode();

  event.preventDefault();

});


let fyear = "#fy_table2";
      let fyearid2 = "#fy_table1";
      let fyearheaders = document.querySelectorAll(fyearid2 + " th");

      // Sort the table element when clicking on the table headers
      fyearheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(fyear, ".cls_fyear", "td:nth-child(" + (i + 1) + ")");
        });
      });


function searchProductionCode() {
var input, filter, table, tr, td, i, txtValue;
input = document.getElementById("fy_codesearch");
filter = input.value.toUpperCase();
table = document.getElementById("fy_table2");
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


function searchProductionName() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("fy_namesearch");
    filter = input.value.toUpperCase();
    table = document.getElementById("fy_table2");
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



//------------------------
//Item Dropdown for Material Tab
let itemtid = "#ItemIDTable2";
      let itemtid2 = "#ItemIDTable";
      let itemtidheaders = document.querySelectorAll(itemtid2 + " th");

      // Sort the table element when clicking on the table headers
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
          var OPART = ''; 
          var DRAWNO = '';
          var PARTNO = '';
          loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
          var DRAWNO = '';
          var PARTNO = '';
          loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
          var DRAWNO = '';
          var PARTNO = '';
          loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
          var DRAWNO = '';
          var PARTNO = '';
          loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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

      function ItemPartnoFunction(FORMID) {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Itempartsearch");
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
          var DRAWNO = '';
          var PARTNO = '';
          loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
          var NAME =''; 
          var MUOM = ''; 
          var GROUP = ''; 
          var CTGRY = ''; 
          var BUNIT = ''; 
          var APART = ''; 
          var CPART = ''; 
          var OPART = ''; 
          var DRAWNO = '';
          var PARTNO = filter;
          loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
          var DRAWNO = '';
          var PARTNO = '';
          loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
          var DRAWNO = '';
          var PARTNO = '';
          loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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


      function ItemQTYFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("ItemQTYsearch");
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
          var DRAWNO = '';
          var PARTNO = '';
          loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
          var MUOM =''; 
          var GROUP = filter; 
          var CTGRY = ''; 
          var BUNIT = ''; 
          var APART = ''; 
          var CPART = ''; 
          var OPART = ''; 
          var DRAWNO = '';
          var PARTNO = '';
          loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
          var DRAWNO = '';
          var PARTNO = '';
          loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
          var MUOM =''; 
          var GROUP =''; 
          var CTGRY = filter; 
          var BUNIT = ''; 
          var APART = ''; 
          var CPART = ''; 
          var OPART = ''; 
          var DRAWNO = '';
          var PARTNO = '';
          loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
          var DRAWNO = '';
          var PARTNO = '';
          loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
          var MUOM =''; 
          var GROUP =''; 
          var CTGRY =''; 
          var BUNIT = filter; 
          var APART = ''; 
          var CPART = ''; 
          var OPART = ''; 
          var DRAWNO = '';
          var PARTNO = '';
          loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
          var DRAWNO = '';
          var PARTNO = '';
          loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
          var MUOM =''; 
          var GROUP =''; 
          var CTGRY =''; 
          var BUNIT = ''; 
          var APART = filter; 
          var CPART = ''; 
          var OPART = ''; 
          var DRAWNO = '';
          var PARTNO = '';
          loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
          var DRAWNO = '';
          var PARTNO = '';
          loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
          var MUOM =''; 
          var GROUP =''; 
          var CTGRY =''; 
          var BUNIT = ''; 
          var APART =''; 
          var CPART = filter; 
          var OPART = ''; 
          var DRAWNO = '';
          var PARTNO = '';
          loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
          var DRAWNO = '';
          var PARTNO = '';
          loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
          var MUOM =''; 
          var GROUP =''; 
          var CTGRY =''; 
          var BUNIT = ''; 
          var APART =''; 
          var CPART =''; 
          var OPART = filter; 
          var DRAWNO = '';
          var PARTNO = '';
          loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
        }
        else
        {
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


//Material Tab
function loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO){
	
  var url	=	'<?php echo asset('');?>master/'+FORMID+'/getItemDetails_new';

    $("#tbody_ItemID").html('loading...');
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
    $.ajax({
      url:url,
      type:'POST',
      data:{'taxstate':taxstate,'CODE':CODE,'NAME':NAME,'MUOM':MUOM,'GROUP':GROUP,'CTGRY':CTGRY,'BUNIT':BUNIT,'APART':APART,'CPART':CPART,'OPART':OPART,'DRAWNO':DRAWNO,'PARTNO':PARTNO},
      success:function(data) {
        $("#tbody_ItemID").html(data);    
        bindItemEvents();   
        $('.js-selectall').prop('disabled', true);    
      },
      error:function(data){
        console.log("Error: Something went wrong.");
        $("#tbody_ItemID").html('');                       
      },
    });

}

  
//Material Tab
 

  $('#Material').on('click','[id*="popupITEMID"]',function(event){
    var rowid=this.id;
    var current_rowid=rowid.split("_");  
    $("#hdn_rowid").val(current_rowid[1]); 

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
    var FORMID = "{{$FormId}}";
    var DRAWNO = '';
    var PARTNO = '';

    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 

    $("#ITEMIDpopup").show();
    var id = $(this).attr('id');
    var id2 = $(this).parent().parent().find('[id*="ITEMID_REF"]').attr('id');
    var id3 = $(this).parent().parent().find('[id*="ItemName"]').attr('id');
    var id7 = $(this).parent().parent().find('[id*="ItemPartno"]').attr('id');
    var id4 = $(this).parent().parent().find('[id*="popupUOM"]').attr('id');
    var id5 = $(this).parent().parent().find('[id*="ITEMSPECI"]').attr('id');
    var id6 = $(this).parent().parent().find('[id*="RATEPUOM"]').attr('id');

    $('#hdn_ItemID').val(id);
    $('#hdn_ItemID2').val(id2);
    $('#hdn_ItemID3').val(id3);
    $('#hdn_ItemID4').val(id4);
    $('#hdn_ItemID5').val(id5);
    $('#hdn_ItemID6').val(id6);
    $('#hdn_ItemID7').val(id7);
    event.preventDefault();

       
  });



      $("#ITEMID_closePopup").click(function(event){
        $("#ITEMIDpopup").hide();
        $('.js-selectall').prop("checked", false);
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
  var txtmuom =  $(this).parent().parent().children('[id*="itemuom"]').text();
  var fieldid5 = $(this).parent().parent().children('[id*="irate"]').attr('id');
  var txtruom =  $("#txt"+fieldid5+"").val();
  var fieldid7 = $(this).parent().parent().children('[id*="itempartno"]').attr('id');
  var partno =  $("#txt"+fieldid7+"").val();
  var fieldid8 = $(this).parent().parent().children('[id*="itemstkihd"]').attr('id');
  var stockihd =  $("#txt"+fieldid8+"").val();
  var ITEMID = $(this).parent().parent().attr('id');
  var ITEMID_REF =  $("#txt"+ITEMID+"").val();
  var MAIN_UOMID = $(this).parent().parent().children('[id*="itemuom"]').attr('id');
  var MAIN_UOMID_REF =  $("#txt"+MAIN_UOMID+"").val();

  var apartno =  $("#addinfo"+fieldid+"").data("desc101");
  var cpartno =  $("#addinfo"+fieldid+"").data("desc102");
  var opartno =  $("#addinfo"+fieldid+"").data("desc103");

  getStockInHand(ITEMID_REF,MAIN_UOMID_REF);    

  if(intRegex.test(txtruom)){
    txtruom = (txtruom +'.00000');
  }
  txtruom = parseFloat(txtruom).toFixed(5);
  var SalesEnq2 = [];
  $('#example2').find('.participantRow').each(function(){
    if($(this).find('[id*="ITEMID_REF"]').val() != '')
    {
      var seitem = $(this).find('[id*="ITEMID_REF"]').val();
      SalesEnq2.push(seitem);
    }
  });
  
      if($(this).is(":checked") == true) 
      {
        if(jQuery.inArray(txtval, SalesEnq2) !== -1)
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
              txtval = '';
              texdesc = '';
              txtname = '';
              partno = '';
              stockihd = '';
              txtmuom = '';
              txtmuomid = '';
              txtruom = '';
              txtspec='';
              return false;
        }   
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
                  $clone.find('[id*="popupUOM"]').val(txtmuom);
                  $clone.find('[id*="UOMID_REF"]').val(txtmuomid);
                  $clone.find('[id*="ITEMSPECI"]').val(txtspec);
                  $clone.find('[id*="RATEPUOM"]').val(txtruom);
                  $clone.find('[id*="ItemPartno"]').val(partno);
                  $clone.find('[id*="ItemStockih"]').val(stockihd);


                  $clone.find('[id*="Alpspartno"]').val(apartno);
                  $clone.find('[id*="Custpartno"]').val(cpartno);
                  $clone.find('[id*="OEMpartno"]').val(opartno);

                  $clone.find('[id*="CONSUMEQTY"]').val('');
                  $clone.find('[id*="PRODUCTIONQTY"]').val('');
                  $clone.find('[id*="SCRAPQTY"]').val('');
                 
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

                $('#'+txtid).val(texdesc);
                $('#'+txt_id2).val(txtval);
                $('#'+txt_id3).val(txtname);
                $('#'+txt_id4).val(txtmuom);
                $('#'+txt_id5).val(txtspec);
                $('#'+txt_id6).val(txtruom);
                $('#'+txt_id7).val(partno);
                $('#'+txt_id8).val(stockihd);
                $('#'+txtid).parent().parent().find('[id*="UOMID_REF"]').val(txtmuomid);
                
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
                }                    
                
                $("#ITEMIDpopup").hide();
                event.preventDefault();
                

 }
 else if($(this).is(":checked") == false) 
 {
  //  var id = txtval;
  //  var r_count = $('#Row_Count1').val();
  //  $('#example2').find('.participantRow').each(function()
  //  {
  //    var itemid = $(this).find('[id*="ITEMID_REF"]').val();
  //    if(id == itemid)
  //    {
  //       var rowCount = $('#Row_Count1').val();
  //       if (rowCount > 1) {
  //         $(this).closest('.participantRow').remove(); 
  //         rowCount = parseInt(rowCount)-1;
  //       $('#Row_Count1').val(rowCount);
  //       }
  //       else 
  //       {
  //         $(document).find('.dmaterial').prop('disabled', true);  
  //         $("#ITEMIDpopup").hide();
  //         $("#YesBtn").hide();
  //         $("#NoBtn").hide();
  //         $("#OkBtn").hide();
  //         $("#OkBtn1").show();
  //         $("#AlertMessage").text('There is only 1 row. So cannot be remove.');
  //         $("#alert").modal('show');
  //         $("#OkBtn1").focus();
  //         highlighFocusBtn('activeOk1');
  //         return false;

  //       }
  //         event.preventDefault(); 
  //    }
  // });
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


function getStockInHand(ITEMID_REF,MAIN_UOMID_REF){
var ROWID=$("#hdn_rowid").val(); 

$.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  $.ajax({
    url:'{{route("master",[202,"getStockInhdDetails"])}}',
    type:'POST',
    data:{'ITEMID_REF':ITEMID_REF,'MAIN_UOMID_REF':MAIN_UOMID_REF},
    success:function(data) {
      $("#ItemStockih_"+ROWID).val(data);    
    },
    error:function(data){
      console.log("Error: Something went wrong.");
      $("#tbody_ItemID").html('');                       
    },
  });
}



  $( "#btnSaveOSO" ).click(function() {
  var formSalesOrder = $("#frm_mst_bom");
  if(formSalesOrder.valid()){
 
    $("#FocusId").val('');

var BOMNO           =   $.trim($("#BOMNO").val());
var BOM_DT          =   $.trim($("#BOM_DT").val());
var PRODUCT_CODE    =   $.trim($("#PRODUCT_CODE").val());
var PRODUCEQTY      =   $.trim($("#PRODUCEQTY").val());
var DODEACTIVATED   =   $.trim($("#DODEACTIVATED").val());

if(BOMNO ===""){
     $("#FocusId").val('BOMNO');
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Enter BOM No.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if(BOM_DT ===""){
     $("#FocusId").val('BOM_DT');
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Select Order Validity From.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if(PRODUCT_CODE ===""){
     $("#FocusId").val('txtitem_popup');
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Select Product Code.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if(getExistProductCode("{{isset($objBOM->BOMID)?$objBOM->BOMID:''}}",PRODUCT_CODE) > 0){
     $("#FocusId").val('txtitem_popup');
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('The Product Code Is Already Exist.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if(PRODUCEQTY ===""){
     $("#FocusId").val('PRODUCEQTY');
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Enter Produce Qty.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
else if ($('input[name="DEACTIVATED"]:checked').length != 0 &&  DODEACTIVATED =="" ) {
    $("#FocusId").val('DODEACTIVATED');
     $("#DESCRIPTIONS").val();  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Select Date of De-Activation.');
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
         
            }
            else
            {
                allblank.push('false');
                focustext1 = $(this).find("[id*=popupITEMID]").attr('id');
            }

            if($.trim($(this).find("[id*=CONSUMEQTY]").val())!="")
            {
                allblank2.push('true');           
         
            }
            else
            {
                allblank2.push('false');
                focustext2 = $(this).find("[id*=CONSUMEQTY]").attr('id');
            }

            if($.trim($(this).find("[id*=PRODUCTIONQTY]").val())!="")
            {
                allblank3.push('true');
            
         
            }
            else
            {
                allblank3.push('false');
                focustext3 = $(this).find("[id*=PRODUCTIONQTY]").attr('id');
            }

            if($.trim($(this).find("[id*=SCRAPQTY]").val())!="")
            {
                allblank4.push('true');
            
         
            }
            else
            {
                allblank4.push('false');
                focustext4 = $(this).find("[id*=SCRAPQTY]").attr('id');
            }
        });

        $('#example6').find('.participantRow10').each(function(){ 

            if($.trim($(this).find("[id*=Componentid]").val())!=""){
                  if($.trim($(this).find('[id*="value"]').val()) != "")
                  {
                    allblank5.push('true');
                  }
                  else
                  {
                    allblank5.push('false');
                    focustext5 = $(this).find("[id*=value]").attr('id');
                  } 
            }            
        
        });

                
        if(jQuery.inArray("false", allblank) !== -1){
                $("#MAT_TAB").click();
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
              $("#MAT_TAB").click();
                $("#FocusId").val(focustext2);
            $("#alert").modal('show');
            $("#AlertMessage").text('Please Enter Value in Consume Qty in Material Tab.');
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
               $("#YesBtn").data("funcname","fnSaveData");  //set dynamic fucntion name
               $("#YesBtn").focus();
               $("#OkBtn").hide();
               highlighFocusBtn('activeYes');
           }
}

    }
});

$( "#btnApprove" ).click(function() {
  var formSalesOrder = $("#frm_mst_bom");
  if(formSalesOrder.valid()){
 
    $("#FocusId").val('');

var BOMNO           =   $.trim($("#BOMNO").val());
var BOM_DT          =   $.trim($("#BOM_DT").val());
var PRODUCT_CODE          =   $.trim($("#PRODUCT_CODE").val());
var PRODUCEQTY       =   $.trim($("#PRODUCEQTY").val());
var DODEACTIVATED          =   $.trim($("#DODEACTIVATED").val());

if(BOMNO ===""){
    $("#FocusId").val($("#BOMNO"));
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please enter value in BOMNO.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
}
else if(BOM_DT ===""){
    $("#FocusId").val($("#BOM_DT"));
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select Date.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
}
else if(PRODUCT_CODE ===""){
    $("#FocusId").val($("#PRODUCT_CODE"));
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select Product Code.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
}
else if(getExistProductCode("{{isset($objBOM->BOMID)?$objBOM->BOMID:''}}",PRODUCT_CODE) > 0){
    $("#FocusId").val('txtitem_popup');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('The Product Code Is Already Exist.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
}
else if(PRODUCEQTY ===""){
    $("#FocusId").val($("#PRODUCEQTY"));
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please enter Produce Qty.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
}
else if ($('input[name="DEACTIVATED"]:checked').length != 0 &&  DODEACTIVATED =="" ) {
    $("#FocusId").val($("#DESCRIPTIONS"));
     $("#DESCRIPTIONS").val();  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Select Date of De-Activation.');
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
         
            }
            else
            {
                allblank.push('false');
                focustext1 = $(this).find("[id*=popupITEMID]").attr('id');
            }

            if($.trim($(this).find("[id*=CONSUMEQTY]").val())!="")
            {
                allblank2.push('true');           
         
            }
            else
            {
                allblank2.push('false');
                focustext2 = $(this).find("[id*=CONSUMEQTY]").attr('id');
            }

            if($.trim($(this).find("[id*=PRODUCTIONQTY]").val())!="")
            {
                allblank3.push('true');
            
         
            }
            else
            {
                allblank3.push('false');
                focustext3 = $(this).find("[id*=PRODUCTIONQTY]").attr('id');
            }

            if($.trim($(this).find("[id*=SCRAPQTY]").val())!="")
            {
                allblank4.push('true');
            
         
            }
            else
            {
                allblank4.push('false');
                focustext4 = $(this).find("[id*=SCRAPQTY]").attr('id');
            }
        });

        $('#example6').find('.participantRow10').each(function(){ 

            if($.trim($(this).find("[id*=Componentid]").val())!=""){
                  if($.trim($(this).find('[id*="value"]').val()) != "")
                  {
                    allblank5.push('true');
                  }
                  else
                  {
                    allblank5.push('false');
                    focustext5 = $(this).find("[id*=value]").attr('id');
                  } 
            }            
        
        });

                
        if(jQuery.inArray("false", allblank) !== -1){
                $("#MAT_TAB").click();
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
              $("#MAT_TAB").click();
                $("#FocusId").val(focustext2);
            $("#alert").modal('show');
            $("#AlertMessage").text('Please Enter Value in Consume Qty in Material Tab.');
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
                $("#AlertMessage").text('Do you want to Approve the record.');
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

//validate and save data
event.preventDefault();

     var trnosoForm = $("#frm_mst_bom");
    var formData = trnosoForm.serialize();
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$.ajax({
    url:'{{ route("mastermodify",[202,"update"])}}',
    type:'POST',
    data:formData,
    success:function(data) {
       
        if(data.errors) {
            $(".text-danger").hide();

            if(data.errors.SONO){
                showError('ERROR_SONO',data.errors.SONO);
                        $("#YesBtn").hide();
                        $("#NoBtn").hide();
                        $("#OkBtn1").show();
                        $("#AlertMessage").text('Please enter correct value in SONO.');
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

//validate and save data
event.preventDefault();

     var trnosoForm = $("#frm_mst_bom");
    var formData = trnosoForm.serialize();
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$.ajax({
    url:'{{ route("mastermodify",[202,"Approve"])}}',
    type:'POST',
    data:formData,
    success:function(data) {
       
        if(data.errors) {
            $(".text-danger").hide();

            if(data.errors.SONO){
                showError('ERROR_SONO',data.errors.SONO);
                        $("#YesBtn").hide();
                        $("#NoBtn").hide();
                        $("#OkBtn1").show();
                        $("#AlertMessage").text('Please enter correct value in SONO.');
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
    $("#SONO").focus();
});

//ok button
$("#OkBtn").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn").hide();
    $(".text-danger").hide();
    window.location.href = '{{route("master",[202,"index"]) }}';
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
$("#Subtitute").on('click', '.remove', function() {

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
              event.preventDefault();
        }
        event.preventDefault();
    });

$("#ByProduct").on('click', '.remove', function() {
  
        var rowCount = $(this).closest('table').find('.participantRow2').length;
        if (rowCount > 1) {
        $(this).closest('.participantRow2').remove();     
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
        $clone.find('[id*="ITEMID_REF"]').val('');
        $tr.closest('table').append($clone);         
        var rowCount1 = $('#Row_Count1').val();
		    rowCount1 = parseInt(rowCount1)+1;
        $('#Row_Count1').val(rowCount1);
        $clone.find('.remove').removeAttr('disabled'); 
        $("[id*='CONSUMEQTY']").ForceNumericOnly();
        $("[id*='PRODUCTION']").ForceNumericOnly();
        $("[id*='SCRAP']").ForceNumericOnly();
        event.preventDefault();
    });

    $("#Subtitute").on('click', '.add', function() {
 
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
        $clone.find('[id*="ITEMID_REF"]').val('');
        $tr.closest('table').append($clone);         
        var rowCount1 = $('#Row_Count2').val();
		    rowCount1 = parseInt(rowCount1)+1;
        $('#Row_Count2').val(rowCount1);
        $clone.find('.remove').removeAttr('disabled'); 
        $("[id*='CONSUMEQTY1']").ForceNumericOnly();
        $("[id*='PRODUCTION1']").ForceNumericOnly();
        $("[id*='SCRAP1']").ForceNumericOnly();
        event.preventDefault();
    });

    $("#ByProduct").on('click', '.add', function() {
        var $tr = $(this).closest('table');
        var allTrs = $tr.find('.participantRow2').last();
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
        var rowCount1 = $('#Row_Count3').val();
		    rowCount1 = parseInt(rowCount1)+1;
        $('#Row_Count3').val(rowCount1);
        $clone.find('.remove').removeAttr('disabled'); 
        $("[id*='PRODUCE_QTY2']").ForceNumericOnly();
        event.preventDefault();
    });

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
        $("[id^='value_']").ForceNumericOnly();
        event.preventDefault();
    });


      
// Main Item pop for subtitute tab
//------------------------
function loadItem_main_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO){
	
  var url	=	'<?php echo asset('');?>master/'+FORMID+'/getItemDetails_main_item';

    $("#tbody_main_item").html('loading...');
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
    $.ajax({
      url:url,
      type:'POST',
      data:{'taxstate':taxstate,'CODE':CODE,'NAME':NAME,'MUOM':MUOM,'GROUP':GROUP,'CTGRY':CTGRY,'BUNIT':BUNIT,'APART':APART,'CPART':CPART,'OPART':OPART,'DRAWNO':DRAWNO,'PARTNO':PARTNO},
      success:function(data) {
      $("#tbody_main_item").html(data); 
      bindMainItem();

      },
      error:function(data){
        console.log("Error: Something went wrong.");
        $("#tbody_main_item").html('');                        
      },
    });

}


function get_item(id){   
    
    var result = id.split('_');
    var id_number=result[1];
    var popup_id='#'+id;

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
    var FORMID = "{{$FormId}}";
    var DRAWNO = '';
    var PARTNO = '';
    loadItem_main_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
    
    $(".mainitem_tab").val(id_number);    
    $("#mainitempopup").show();

    var id =  "MainItemCode_"+id_number;
    var id2 =  "MainItemId_Ref_"+id_number;
    var id3 =  "MainItemName_"+id_number;

    $('#hdn_MItemID').val(id);
    $('#hdn_MItemID2').val(id2);
    $('#hdn_MItemID3').val(id3);
      
    
    $("#CUSTOMERID_POPUP").keyup(function(event){
    if(event.keyCode==13){
      $("#mainitempopup").show();
    }
  });

} 

//--new mainitem search
//--Search item popup for substitute Main items 

let mainitem_tid = "#MItemTable2";
      let mainitem_tid2 = "#MItemTable";
      let mainitem_headers = document.querySelectorAll(mainitem_tid2 + " th");

      // Sort the table element when clicking on the table headers
      headers.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(tid, ".clsglid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function MItemCodeFunction(FORMID) {
      
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("MItemCodeSearch");
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
          var DRAWNO = ''; 
          var PARTNO = ''; 
          loadItem_main_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
          var DRAWNO = ''; 
          var PARTNO = ''; 
          loadItem_main_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
        }
        else
        {
          table = document.getElementById("MItemTable2");
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

    function MItemNameFunction(FORMID) {
      
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("MItemNameSearch");
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
        var DRAWNO = ''; 
        var PARTNO = ''; 
        loadItem_main_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
        var DRAWNO = ''; 
        var PARTNO = ''; 
        loadItem_main_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
      }
      else
      {
        table = document.getElementById("MItemTable2");
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

    function MItemUOMFunction(FORMID) {
      
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("MItemUOMSearch");
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
        var DRAWNO = ''; 
        var PARTNO = ''; 
        loadItem_main_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
        var DRAWNO = ''; 
        var PARTNO = ''; 
        loadItem_main_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
      }
      else
      {
        table = document.getElementById("MItemTable2");
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

    function MItemBUFunction(FORMID) {
      
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("MItemBUsearch");
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
        var DRAWNO = ''; 
        var PARTNO = ''; 
        loadItem_main_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
        var DRAWNO = ''; 
        var PARTNO = ''; 
        loadItem_main_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
      }
      else
      {
        table = document.getElementById("MItemTable2");
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

    function MItemAPNFunction(FORMID) {
      
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("MItemAPNsearch");
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
        var DRAWNO = ''; 
        var PARTNO = ''; 
        loadItem_main_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
        var DRAWNO = ''; 
        var PARTNO = ''; 
        loadItem_main_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
      }
      else
      {
        table = document.getElementById("MItemTable2");
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

    function MItemCPNFunction(FORMID) {
      
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("MItemCPNsearch");
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
        var DRAWNO = ''; 
        var PARTNO = ''; 
        loadItem_main_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
        var DRAWNO = ''; 
        var PARTNO = ''; 
        loadItem_main_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
      }
      else
      {
        table = document.getElementById("MItemTable2");
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

    function MItemOEMPNFunction(FORMID) {
      
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("MItemOEMPNsearch");
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
        var DRAWNO = ''; 
        var PARTNO = ''; 
        loadItem_main_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
        var DRAWNO = ''; 
        var PARTNO = ''; 
        loadItem_main_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
      }
      else
      {
        table = document.getElementById("MItemTable2");
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

    function MItemDrawingNoFunction(FORMID) {
      
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("MItemDrawingNoSearch");
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
        var DRAWNO = ''; 
        var PARTNO = ''; 
        loadItem_main_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
        var OPART = ''; 
        var DRAWNO = filter; 
        var PARTNO = ''; 
        loadItem_main_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
      }
      else
      {
        table = document.getElementById("MItemTable2");
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

    function MItemPartNoFunction(FORMID) {
      
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("MItemPartNoSearch");
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
        var DRAWNO = ''; 
        var PARTNO = ''; 
        loadItem_main_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
        var OPART = ''; 
        var DRAWNO = ''; 
        var PARTNO = filter; 
        loadItem_main_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
      }
      else
      {
        table = document.getElementById("MItemTable2");
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


function bindMainItem(){

  $('[id*="chkIdMainItem"]').change(function(){
 

    var fieldid = $(this).parent().parent().attr('id');

    var txtval =    $("#txt"+fieldid+"").val();
    var name =   $("#txt"+fieldid+"").data("name");
    var code =   $("#txt"+fieldid+"").data("code");
    var drawingno =   $("#txt"+fieldid+"").data("drawingno");
    var uom =   $("#txt"+fieldid+"").data("uom");
    var uomno =   $("#txt"+fieldid+"").data("uomno");
    var partno =   $("#txt"+fieldid+"").data("partno");

    var txtid= $('#hdn_MItemID').val();
    var txt_id2= $('#hdn_MItemID2').val();
    var txt_id3= $('#hdn_MItemID3').val();

   
    
      var CheckExist = [];

      $('#example3').find('.participantRow1').each(function(){
      if($(this).find('[id*="MainItemId_Ref"]').val() != '') 
      {
        var itemid = $(this).find('[id*="MainItemId_Ref"]').val();
        CheckExist.push(itemid);
      }
      });

      if(jQuery.inArray(txtval, CheckExist) !== -1){
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

            // $(values).val('');
            // $(descriptions).val('');
            // $(code).val('');
            txtval =    '';
            name =   '';
            code =   '';
            drawingno =   '';
            uom =  '' ;
            uomno =  '';
            partno =  '';

            txtid= '';
            txt_id2= '';
            txt_id3= '';

            $("#mainitempopup").hide();
            return false;

      }else{

          $('#'+txtid).val(code);
          $('#'+txt_id2).val(txtval);
          $('#'+txt_id3).val(name);

      }

      txtval =    '';
      name =   '';
      code =   '';
      drawingno =   '';
      uom =  '' ;
      uomno =  '';
      partno =  '';
      txtid= '';
      txt_id2= '';
      txt_id3= '';
      $("#MItemCodeSearch").val(''); 
      $("#MItemNameSearch").val(''); 
      $("#MItemUOMSearch").val(''); 
      $("#MItemBUsearch").val(''); 
      $("#MItemAPNsearch").val(''); 
      $("#MItemCPNsearch").val(''); 
      $("#MItemOEMPNsearch").val(''); 
      $("#MItemDrawingNoSearch").val(''); 
      $("#MItemPartNoSearch").val(''); 

      $("#mainitempopup").hide();

  });

 

}

$("#item_closePopup").on("click",function(event){ 
    $("#MItemCodeSearch").val(''); 
    $("#MItemNameSearch").val(''); 
    $("#MItemUOMSearch").val(''); 
    $("#MItemBUsearch").val(''); 
    $("#MItemAPNsearch").val(''); 
    $("#MItemCPNsearch").val(''); 
    $("#MItemOEMPNsearch").val(''); 
    $("#MItemDrawingNoSearch").val(''); 
    $("#MItemPartNoSearch").val(''); 
    
    $("#mainitempopup").hide();
  });

//----------------------------
// Main Item pop for subtitute tab 
//get items for substitute tab @ substitute item

function get_item_substitute(id){   

      var result = id.split('_');
      var id_numbers=result[1];
      var popup_id='#'+id;

      $(".mainitem_tab1").val(id_numbers);    

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
      var FORMID = "{{$FormId}}";
      var DRAWNO = '';
      var PARTNO = '';
      loadItem_subs_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
        

      $("#mainitempopup1").show();

      var id="MainItemId1_Ref_"+id_numbers; 
      var id2="MainItemCode1_"+id_numbers;
      var id3="MainItemName1_"+id_numbers;
      var id4="MainItemuom1_"+id_numbers;
      var id5="Mainuom_ref1_"+id_numbers;
      var id6="MainItemPartno1_"+id_numbers;

      $('#hdn_SubsItemID').val(id);
      $('#hdn_SubsItemID2').val(id2);
      $('#hdn_SubsItemID3').val(id3);
      $('#hdn_SubsItemID4').val(id4);
      $('#hdn_SubsItemID5').val(id5);
      $('#hdn_SubsItemID6').val(id6);
        
     
}

function loadItem_subs_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO){
	
  var url	=	'<?php echo asset('');?>master/'+FORMID+'/getItemDetails_subs_item';

    $("#tbody_subs_item").html('loading...');
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
    $.ajax({
      url:url,
      type:'POST',
      data:{'taxstate':taxstate,'CODE':CODE,'NAME':NAME,'MUOM':MUOM,'GROUP':GROUP,'CTGRY':CTGRY,'BUNIT':BUNIT,'APART':APART,'CPART':CPART,'OPART':OPART,'DRAWNO':DRAWNO,'PARTNO':PARTNO},
      success:function(data) {
      $("#tbody_subs_item").html(data); 
      bindSubsItem();

      },
      error:function(data){
        console.log("Error: Something went wrong.");
        $("#tbody_subs_item").html('');                        
      },
    });

}

$("#item_closePopup1").on("click",function(event){ 

  $("#mainitempopup1").hide();

});


//--------------------------subsitem filter
//Search item popup for substitute  items 

      let tid1 = "#SubsItemCodeTable2";
      let tid3 = "#SubsItemCodeTable";
      let headers1 = document.querySelectorAll(tid3 + " th");

      // Sort the table element when clicking on the table headers
      headers1.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(tid1, ".clsglid", "td:nth-child(" + (i + 1) + ")");
        });
      });


      function SubsItemCodeFunction(FORMID) 
      {
      
          var input, filter, table, tr, td, i, txtValue;
          input = document.getElementById("SubsItemCodeSearch");
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
            var DRAWNO = ''; 
            var PARTNO = ''; 
            loadItem_subs_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
            var DRAWNO = ''; 
            var PARTNO = ''; 
            loadItem_subs_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
          }
          else
          {
            table = document.getElementById("SubsItemCodeTable2");
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

      function SubsItemNameFunction(FORMID) 
      {
      
          var input, filter, table, tr, td, i, txtValue;
          input = document.getElementById("SubsItemNameSearch");
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
            var DRAWNO = ''; 
            var PARTNO = ''; 
            loadItem_subs_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
            var DRAWNO = ''; 
            var PARTNO = ''; 
            loadItem_subs_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
          }
          else
          {
            table = document.getElementById("SubsItemCodeTable2");
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

      function SubsItemUOMFunction(FORMID) 
      {
      
          var input, filter, table, tr, td, i, txtValue;
          input = document.getElementById("SubsItemUOMSearch");
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
            var DRAWNO = ''; 
            var PARTNO = ''; 
            loadItem_subs_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
            var DRAWNO = ''; 
            var PARTNO = ''; 
            loadItem_subs_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
          }
          else
          {
            table = document.getElementById("SubsItemCodeTable2");
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

      function SubsItemBUFunction(FORMID) 
      {
      
          var input, filter, table, tr, td, i, txtValue;
          input = document.getElementById("SubsItemBUsearch");
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
            var DRAWNO = ''; 
            var PARTNO = ''; 
            loadItem_subs_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
            var DRAWNO = ''; 
            var PARTNO = ''; 
            loadItem_subs_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
          }
          else
          {
            table = document.getElementById("SubsItemCodeTable2");
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

      function SubsItemAPNFunction(FORMID) 
      {
      
          var input, filter, table, tr, td, i, txtValue;
          input = document.getElementById("SubsItemAPNsearch");
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
            var DRAWNO = ''; 
            var PARTNO = ''; 
            loadItem_subs_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
            var DRAWNO = ''; 
            var PARTNO = ''; 
            loadItem_subs_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
          }
          else
          {
            table = document.getElementById("SubsItemCodeTable2");
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

      function SubsItemCPNFunction(FORMID) 
      {
      
          var input, filter, table, tr, td, i, txtValue;
          input = document.getElementById("SubsItemCPNsearch");
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
            var DRAWNO = ''; 
            var PARTNO = ''; 
            loadItem_subs_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
            var DRAWNO = ''; 
            var PARTNO = ''; 
            loadItem_subs_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
          }
          else
          {
            table = document.getElementById("SubsItemCodeTable2");
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

      function SubsItemOEMPNFunction(FORMID) 
      {
      
          var input, filter, table, tr, td, i, txtValue;
          input = document.getElementById("SubsItemOEMPNsearch");
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
            var DRAWNO = ''; 
            var PARTNO = ''; 
            loadItem_subs_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
            var DRAWNO = ''; 
            var PARTNO = ''; 
            loadItem_subs_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
          }
          else
          {
            table = document.getElementById("SubsItemCodeTable2");
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

      function SubsItemDrawingNoFunction(FORMID) 
      {
      
          var input, filter, table, tr, td, i, txtValue;
          input = document.getElementById("SubsItemDrawingNoSearch");
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
            var DRAWNO = ''; 
            var PARTNO = ''; 
            loadItem_subs_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
            var OPART = ''; 
            var DRAWNO = filter; 
            var PARTNO = ''; 
            loadItem_subs_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
          }
          else
          {
            table = document.getElementById("SubsItemCodeTable2");
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

      
      function SubsItemPartNoFunction(FORMID) 
      {
      
          var input, filter, table, tr, td, i, txtValue;
          input = document.getElementById("SubsItemPartNoSearch");
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
            var DRAWNO = ''; 
            var PARTNO = ''; 
            loadItem_subs_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
            var OPART = ''; 
            var DRAWNO = ''; 
            var PARTNO = filter; 
            loadItem_subs_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
          }
          else
          {
            table = document.getElementById("SubsItemCodeTable2");
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


function bindSubsItem(){


    $('[id*="chkIdSubsItem"]').change(function(){

      var fieldid = $(this).parent().parent().attr('id');
      var txtval =    $("#txt"+fieldid+"").val();
      var name =   $("#txt"+fieldid+"").data("name");
      var code =   $("#txt"+fieldid+"").data("code");
      var drawingno =   $("#txt"+fieldid+"").data("drawingno");
      var uom =   $("#txt"+fieldid+"").data("uom");
      var uomno =   $("#txt"+fieldid+"").data("uomno");
      var partno =   $("#txt"+fieldid+"").data("partno");

      var txtid= $('#hdn_SubsItemID').val();
      var txt_id2= $('#hdn_SubsItemID2').val();
      var txt_id3= $('#hdn_SubsItemID3').val();
      var txt_id4= $('#hdn_SubsItemID4').val();
      var txt_id5= $('#hdn_SubsItemID5').val();
      var txt_id6= $('#hdn_SubsItemID6').val();

      var CheckExist = [];
      $('#example3').find('.participantRow1').each(function(){
        if($(this).find('[id*="MainItemId1_Ref_"]').val() != '')
        {
          var itemid = $(this).find('[id*="MainItemId1_Ref_"]').val();
          CheckExist.push(itemid);
        }
      });

      if(jQuery.inArray(txtval, CheckExist) !== -1){
              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn").hide();
              $("#OkBtn1").show();
              $("#AlertMessage").text('Item already exists.');
              $("#alert").modal('show');
              $("#OkBtn1").focus();
              highlighFocusBtn('activeOk1');

              $('#hdn_SubsItemID').val('');
              $('#hdn_SubsItemID2').val('');
              $('#hdn_SubsItemID3').val('');
              $('#hdn_SubsItemID4').val('');
              $('#hdn_SubsItemID5').val('');
              $('#hdn_SubsItemID6').val('');

              txtval =    '';
              name  =   '';
              code =   '';
              drawingno =   '';
              uom =  '' ;
              uomno =  '';
              partno =  '';

              $("#mainitempopup1").hide();
              return false;

      }else{

          // $(values).val(txtval);
          // $(descriptions).val(texdescname);
          // $(code).val(texdesc);
          // $(uom_id).val(uom);
          // $(uomno_id).val(uomno);
          // $(partno_id).val(partno);
          $('#'+txtid).val(txtval);
          $('#'+txt_id2).val(code);
          $('#'+txt_id3).val(name);
          $('#'+txt_id4).val(uom);
          $('#'+txt_id5).val(uomno);
          $('#'+txt_id6).val(partno);

      }

      $('#hdn_SubsItemID').val('');
      $('#hdn_SubsItemID2').val('');
      $('#hdn_SubsItemID3').val('');
      $('#hdn_SubsItemID4').val('');
      $('#hdn_SubsItemID5').val('');
      $('#hdn_SubsItemID6').val('');

      txtval =    '';
      name  =   '';
      code =   '';
      drawingno =   '';
      uom =  '' ;
      uomno =  '';
      partno =  '';

      $("#mainitempopup1").hide();

    });

} 
    

//--------------------------------------
//---------------------------------
//By product tab item section starts

function get_item_byproduct(id){   
  
      var result = id.split('_');
      var id_numbers=result[1];
      
      //$(".mainitem_tab2").val(id_numbers);       

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
      var FORMID = "{{$FormId}}";
      var DRAWNO = '';
      var PARTNO = '';
      loadItem_by_pro(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
        

      $("#mainitempopup2").show();

      var id="MainItemId2_Ref_"+id_numbers; 
      var id2="MainItemCode2_"+id_numbers;
      var id3="MainItemName2_"+id_numbers;
      var id4="MainItemPartno2_"+id_numbers;
      var id5="Mainuom2_Ref_"+id_numbers;


      $('#hdn_ByProItemID').val(id);
      $('#hdn_ByProItemID2').val(id2);
      $('#hdn_ByProItemID3').val(id3);
      $('#hdn_ByProItemID4').val(id4);
      $('#hdn_ByProItemID5').val(id5);
      
}

function loadItem_by_pro(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO){
	
  var url	=	'<?php echo asset('');?>master/'+FORMID+'/getItemDetails_by_pro';

    $("#tbody_by_pro").html('loading...');
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
    $.ajax({
      url:url,
      type:'POST',
      data:{'taxstate':taxstate,'CODE':CODE,'NAME':NAME,'MUOM':MUOM,'GROUP':GROUP,'CTGRY':CTGRY,'BUNIT':BUNIT,'APART':APART,'CPART':CPART,'OPART':OPART,'DRAWNO':DRAWNO,'PARTNO':PARTNO},
      success:function(data) {
      $("#tbody_by_pro").html(data); 
      bindByProItem();

      },
      error:function(data){
        console.log("Error: Something went wrong.");
        $("#tbody_by_pro").html('');                        
      },
    });

}

$("#item_closePopup2").on("click",function(event){ 
 $("#mainitempopup2").hide();
});

//Search item popup for byproduct

let tid7 = "#ByProItemCodeTable2";
let tid8 = "#ByProItemCodeTable";
let headers3 = document.querySelectorAll(tid8 + " th");

// Sort the table element when clicking on the table headers
headers3.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(tid1, ".clsglid", "td:nth-child(" + (i + 1) + ")");
  });
});


function ByProItemCodeFunction(FORMID) 
{

    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("ByProItemCodeSearch");
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
      var DRAWNO = ''; 
      var PARTNO = ''; 
      loadItem_by_pro(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
      var DRAWNO = ''; 
      var PARTNO = ''; 
      loadItem_by_pro(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
    }
    else
    {
      table = document.getElementById("ByProItemCodeTable2");
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

function ByProItemNameFunction(FORMID) 
{

    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("ByProItemNameSearch");
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
      var DRAWNO = ''; 
      var PARTNO = ''; 
      loadItem_by_pro(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
      var DRAWNO = ''; 
      var PARTNO = ''; 
      loadItem_by_pro(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
    }
    else
    {
      table = document.getElementById("ByProItemCodeTable2");
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

function ByProItemUOMFunction(FORMID) 
{

    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("ByProItemUOMSearch");
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
      var DRAWNO = ''; 
      var PARTNO = ''; 
      loadItem_by_pro(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
      var DRAWNO = ''; 
      var PARTNO = ''; 
      loadItem_by_pro(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
    }
    else
    {
      table = document.getElementById("ByProItemCodeTable2");
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

function ByProItemBUFunction(FORMID) 
{

    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("ByProItemBUsearch");
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
      var DRAWNO = ''; 
      var PARTNO = ''; 
      loadItem_by_pro(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
      var DRAWNO = ''; 
      var PARTNO = ''; 
      loadItem_by_pro(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
    }
    else
    {
      table = document.getElementById("ByProItemCodeTable2");
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


function ByProItemAPNFunction(FORMID) 
{

    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("ByProItemAPNsearch");
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
      var DRAWNO = ''; 
      var PARTNO = ''; 
      loadItem_by_pro(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
      var DRAWNO = ''; 
      var PARTNO = ''; 
      loadItem_by_pro(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
    }
    else
    {
      table = document.getElementById("ByProItemCodeTable2");
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


function ByProItemCPNFunction(FORMID) 
{

    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("ByProItemCPNsearch");
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
      var DRAWNO = ''; 
      var PARTNO = ''; 
      loadItem_by_pro(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
      var DRAWNO = ''; 
      var PARTNO = ''; 
      loadItem_by_pro(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
    }
    else
    {
      table = document.getElementById("ByProItemCodeTable2");
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


function ByProItemOEMPNFunction(FORMID) 
{

    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("ByProItemOEMPNsearch");
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
      var DRAWNO = ''; 
      var PARTNO = ''; 
      loadItem_by_pro(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
      var DRAWNO = ''; 
      var PARTNO = ''; 
      loadItem_by_pro(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
    }
    else
    {
      table = document.getElementById("ByProItemCodeTable2");
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

function ByProItemDrawingNoFunction(FORMID) 
{

    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("ByProItemDrawingNoSearch");
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
      var DRAWNO = ''; 
      var PARTNO = ''; 
      loadItem_by_pro(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
      var OPART = ''; 
      var DRAWNO = filter; 
      var PARTNO = ''; 
      loadItem_by_pro(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
    }
    else
    {
      table = document.getElementById("ByProItemCodeTable2");
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

function ByProItemPartNoFunction(FORMID) 
{

    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("ByProItemPartNoSearch");
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
      var DRAWNO = ''; 
      var PARTNO = ''; 
      loadItem_by_pro(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
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
      var OPART = ''; 
      var DRAWNO = ''; 
      var PARTNO = filter; 
      loadItem_by_pro(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
    }
    else
    {
      table = document.getElementById("ByProItemCodeTable2");
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


function bindByProItem(){


   $('[id*="chkIdByProItem"]').change(function(){
      

      var fieldid = $(this).parent().parent().attr('id');
      var txtval =    $("#txt"+fieldid+"").val();
      var name =   $("#txt"+fieldid+"").data("name");
      var code =   $("#txt"+fieldid+"").data("code");
      var partno =   $("#txt"+fieldid+"").data("partno");
      var uomid =   $("#txt"+fieldid+"").data("uomno");

      var txtid  = $('#hdn_ByProItemID').val();
      var txt_id2= $('#hdn_ByProItemID2').val();
      var txt_id3= $('#hdn_ByProItemID3').val();
      var txt_id4= $('#hdn_ByProItemID4').val();
      var txt_id5= $('#hdn_ByProItemID5').val();

      var CheckExist = []; 
      $('#example4').find('.participantRow2').each(function(){
            if($(this).find('[id*="MainItemId2_Ref"]').val() != '')
            {
              var itemid = $(this).find('[id*="MainItemId2_Ref"]').val();
              CheckExist.push(itemid);
            
            }
      });

      if(jQuery.inArray(txtval, CheckExist) !== -1){
        $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn").hide();
              $("#OkBtn1").show();
              $("#AlertMessage").text('Item already exists.');
              $("#alert").modal('show');
              $("#OkBtn1").focus();
              highlighFocusBtn('activeOk1');
              
              $('#hdn_ByProItemID').val('');
              $('#hdn_ByProItemID2').val('');
              $('#hdn_ByProItemID3').val('');
              $('#hdn_ByProItemID4').val('');
              $('#hdn_ByProItemID5').val('');
              $('#hdn_ByProItemID6').val('');

              txtval =    '';
              name  =   '';
              code =   '';
              partno =  '';
              uomid = '';
              $("#mainitempopup2").hide();
              return false;

      }else{

              $('#'+txtid).val(txtval);
              $('#'+txt_id2).val(code);
              $('#'+txt_id3).val(name);
              $('#'+txt_id4).val(partno);
              $('#'+txt_id5).val(uomid);
      }

      $('#hdn_ByProItemID').val('');
      $('#hdn_ByProItemID2').val('');
      $('#hdn_ByProItemID3').val('');
      $('#hdn_ByProItemID4').val('');
      $('#hdn_ByProItemID5').val('');
      $('#hdn_ByProItemID6').val('');

      txtval =    '';
      name  =   '';
      code =   '';
      partno =  '';
      uomid='';

      $("#mainitempopup2").hide();
      ///---------------------------

  });

}
//By product tab item section ends
//-----------------------------------

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
//direct cost tab component list ends

//----------------------------------

//BOM Date Check
$('#BOM_DT').change(function( event ) {
            var today = new Date();     
            var d = new Date($(this).val()); 
            today.setHours(0, 0, 0, 0) ;
            d.setHours(0, 0, 0, 0) ;
            var bomdate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
            if (d < today) {
                $(this).val(bomdate);
                $("#alert").modal('show');
                $("#AlertMessage").text('Date cannot be less than Current date');
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
//BOM Date Check

  $(function () {
	
	var today = new Date(); 
    var dodeactived_date = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
    $('#DODEACTIVATED').attr('min',dodeactived_date);

	$('input[type=checkbox][name=DEACTIVATED]').change(function() {
		if ($(this).prop("checked")) {
		  $(this).val('1');
		  $('#DODEACTIVATED').removeAttr('disabled');
		}
		else {
		  $(this).val('0');
		  $('#DODEACTIVATED').prop('disabled', true);
		  $('#DODEACTIVATED').val('');
		  
		}
	});

});

$('#btnExit').on('click', function() {
  var viewURL = '{{route('home')}}';
              window.location.href=viewURL;
});

$('#btnCopy').on('click', function() {

  var BOMID=$("#BOMID_COPY").val();
  var STATUS=$("#STATUS").val();
if(STATUS=='C'){
  $("#YesBtn").hide();
  $("#NoBtn").hide();
  $("#OkBtn").hide();
  $("#OkBtn1").show();
  $("#AlertMessage").text('You can not copy cancelled record.');
  $("#alert").modal('show');
  $("#OkBtn1").focus();
  return false;
} else{
        var copyURL = '{{route("master",[202,"copy",":rcdId"]) }}';
            copyURL = copyURL.replace(":rcdId",BOMID);
            window.location.href=copyURL;
      }


});//Copy 

$("#btnUndo").on("click", function() {

$("#AlertMessage").text("Do you want to erase entered information in this record?");
$("#alert").modal('show');
$("#YesBtn").data("funcname","fnUndoYes");
$("#YesBtn").show();
$("#NoBtn").data("funcname","fnUndoNo");
$("#NoBtn").show();
$("#OkBtn").hide();
$("#NoBtn").focus();
return false;
});



window.fnUndoYes = function (){
  window.location.reload();
}

window.fnUndoNo = function (){

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


function getExistProductCode(BOMID,ITEMID_REF){

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  var exist_data  = $.ajax({
                    url:'{{ route("master",[202,"getExistProductCode"])}}',
                    type:'POST',
                    async: false,
                    dataType: 'json',
                    data: {BOMID:BOMID,ITEMID_REF:ITEMID_REF},
                    done: function(response) {return response;}
                    }).responseText;

  return parseFloat(exist_data);
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