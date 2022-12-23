
@extends('layouts.app')
@section('content')
<script>
      function undoButton(){
        $("#AlertMessage").text("Do you want to erase entered information in this record?");
        $("#alert").modal('show');

        $("#YesBtn").data("funcname","fnUndoYes");
        $("#YesBtn").show();

        $("#NoBtn").data("funcname","fnUndoNo");
        $("#NoBtn").show();
        
        $("#OkBtn").hide();
        $("#NoBtn").focus();
      }

      window.fnUndoYes = function (){
        window.location.reload();
      }

      window.fnUndoNo = function (){
        $("#SONO").focus();
      }
    </script>

    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('transaction',[43,'index'])}}" class="btn singlebt">Sales Challan (SC)</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
                        <button class="btn topnavbt" id="btnSaveSC" disabled="disabled"><i class="fa fa-floppy-o"></i> Save</button>
                        <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" id="btnPrint" ><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt" id="btnUndo"  disabled="disabled"><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-thumbs-o-up"></i> Approved</button>
                        <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                        <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                </div>
            </div>
    </div><!--topnav-->	
    <!-- multiple table-responsive table-wrapper-scroll-y my-custom-scrollbar -->
    <div class="container-fluid purchase-order-view">
        <form id="frm_trn_sc"  method="POST">   
            @csrf
            {{isset($objSC->SCID[0]) ? method_field('PUT') : '' }}
            <div class="container-fluid filter">
                    <div class="inner-form">
						<div class="row">
							<div class="col-lg-2 pl"><p>Challan Type*</p></div>
							<div class="col-lg-2 pl">
								<select name="CHALLANTYPE" id="CHALLANTYPE" class="form-control" >
									   <option value="SALES" {{ isset($objSC->CHALLANTYPE) && $objSC->CHALLANTYPE == 'SALES' ? 'selected' : ''}}>Sales</option>
									   <option value="TRANSFER" {{ isset($objSC->CHALLANTYPE) && $objSC->CHALLANTYPE == 'TRANSFER' ? 'selected' : ''}}>STOCK TRANSFER</option>
								</select>
							</div>
						</div>
                        <div class="row">
                            <div class="col-lg-2 pl"><p>Sales Challan No*</p></div>
                            <div class="col-lg-2 pl">
                                <input {{$InputStatus}} type="text" name="SCNO" id="SCNO" value="{{ isset($objSC->SCNO)?$objSC->SCNO:'' }}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase"  >
                            </div>
                            <div class="col-lg-2 pl"><p>Sales Challan Date*</p></div>
                            <div class="col-lg-2 pl">
                                <input {{$InputStatus}} type="date" name="SCDT" id="SCDT" value="{{ isset($objSC->SCDT)?$objSC->SCDT:'' }}" class="form-control mandatory" autocomplete="off" placeholder="dd/mm/yyyy" >
                            </div>                           
                            <div class="col-lg-2 pl"><p>Customer*</p></div>
                            <div class="col-lg-2 pl">
                                <input {{$InputStatus}} type="text" name="SubGl_popup" id="txtsubgl_popup" class="form-control mandatory" value="{{isset($objsubglcode->SGLCODE)?$objsubglcode->SGLCODE:''}} {{isset($objsubglcode->SLNAME)?'-'.$objsubglcode->SLNAME:''}}" autocomplete="off" readonly/>
                                <input type="hidden" name="SLID_REF" id="SLID_REF" class="form-control" autocomplete="off" value="{{ isset($objSC->SLID_REF)?$objSC->SLID_REF:'' }}" />
                                <input type="hidden" name="GLID_REF" id="GLID_REF" class="form-control" autocomplete="off" value="{{ isset($objSC->GLID_REF)?$objSC->GLID_REF:'' }}"  />
                                <input type="hidden" name="hdnMaterial" id="hdnMaterial" class="form-control" autocomplete="off" />
                            </div>
                        </div>    
                        
                        
                        <div class="row">
                            <div class="col-lg-2 pl"><p>Bill To </p></div>
                            <div class="col-lg-2 pl" id="div_billto">
                                <input {{$InputStatus}} type="text" name="txtBILLTO" id="txtBILLTO" class="form-control"  autocomplete="off" value="{{ isset($objBillAddress[0])?$objBillAddress[0]:'' }}" readonly  />
                                <input type="hidden" name="BILLTO" id="BILLTO" class="form-control" autocomplete="off" value="{{ isset($objSC->BILLTO)?$objSC->BILLTO:'' }}" />
                            </div>
                            <div class="col-lg-2 pl"><p>Ship To</p></div>
                            <div class="col-lg-2 pl" id="div_shipto">
                                <input {{$InputStatus}} type="text" name="txtSHIPTO" id="txtSHIPTO" class="form-control"  autocomplete="off" value="{{ isset($objShpAddress[0])?$objShpAddress[0]:'' }}" readonly  />
                                <input type="hidden" name="SHIPTO" id="SHIPTO" class="form-control" autocomplete="off" value="{{ isset($objSC->SHIPTO)?$objSC->SHIPTO:'' }}" />
                                <input type="hidden" name="Tax_State" id="Tax_State" class="form-control" autocomplete="off" value=" {{ isset($TAXSTATE[0])?$TAXSTATE[0]:'' }}" />
                            </div>
                            
                            <div class="col-lg-2 pl"><p>Remarks</p></div>
                            <div class="col-lg-2 pl">
                                <input {{$InputStatus}} type="text" name="REMARKS" id="REMARKS" autocomplete="off" class="form-control" maxlength="200" value="{{ isset($objSC->REMARKS)?$objSC->REMARKS:'' }}"   >
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-2 pl"><p>Transporter Code</p></div>
                            <div class="col-lg-2 pl">
                                @if(!empty($objTransporter2))
                                <input {{$InputStatus}} type="text" name="TRANSPORTERID_popup" id="TRANSPORTERID_popup" class="form-control"  autocomplete="off" value="{{ isset($objTransporter2->TRANSPORTER_CODE)?$objTransporter2->TRANSPORTER_CODE:'' }}"  readonly/>
                                @else
                                <input  {{$InputStatus}}type="text" name="TRANSPORTERID_popup" id="TRANSPORTERID_popup" class="form-control"  autocomplete="off" readonly/>
                                @endif 
                                <input type="hidden" name="TRANSPORTERID_REF" id="TRANSPORTERID_REF" class="form-control" autocomplete="off" value="{{ isset($objSC->TRANSPORTERID_REF)?$objSC->TRANSPORTERID_REF:'' }}" readonly/>                                
                            </div>                            
                            <div class="col-lg-2 pl"><p>Name</p></div>
                            <div class="col-lg-2 pl">
                                @if(!empty($objTransporter2))
                                <input {{$InputStatus}} type="text" name="TRANSPORTERNAME" id="TRANSPORTERNAME" class="form-control"  autocomplete="off" value="{{ isset($objTransporter2->TRANSPORTER_NAME)?$objTransporter2->TRANSPORTER_NAME:'' }}"  readonly/>
                                @else
                                <input {{$InputStatus}} type="text" name="TRANSPORTERNAME" id="TRANSPORTERNAME" class="form-control"  autocomplete="off" readonly/>
                                @endif 
                            </div>
                            <div class="col-lg-2 pl"><p>Transport Time</p></div>
                            <div class="col-lg-2 pl">
                                <input {{$InputStatus}} type="text" name="TRANSPORTTIME" id="TRANSPORTTIME" class="form-control" maxlength="30" value="{{ isset($objSC->TRANSPORT_TIME)?$objSC->TRANSPORT_TIME:'' }}"  autocomplete="off"   >
                            </div>
                        </div>

                        <div class="row">
                        <div class="col-lg-2 pl"><p>Type</p></div>
                            <div class="col-lg-2 pl">
                               <select {{$InputStatus}} name="TYPE" id="TYPE" class="form-control" >
                               <option value="SO"  {{ isset($objSC->TYPE) && $objSC->TYPE == 'SO' ? 'selected' : ''}}>Sales Order</option>
                               <option value="OSO"  {{isset($objSC->TYPE) && $objSC->TYPE == 'OSO' ? 'selected' : ''}}>Open Sales Order</option>
                               </select>
                              
                            </div>
                        <div class="col-lg-2 pl"><p>Shipping Instruction</p></div>
                            <div class="col-lg-4 pl">
                                <input {{$InputStatus}} type="text" name="SHIPPINGINSTRUCTION" id="SHIPPINGINSTRUCTION" class="form-control" maxlength="100" value="{{ isset($objSC->SHIPPING_INST)?$objSC->SHIPPING_INST:''  }}"  autocomplete="off"   >
                            </div>

                        </div>
                    </div>

                    <div class="container-fluid purchase-order-view">

                        <div class="row">
                        <ul class="nav nav-tabs">
                            <li class="active"><a data-toggle="tab" href="#Material">Material</a></li>
                            <li><a data-toggle="tab" href="#udf">UDF</a></li>
                            <!-- <li><a data-toggle="tab" href="#Store">Store</a></li> -->
                        
                        </ul>
                            <div class="tab-content">
                                <div id="Material" class="tab-pane fade in active">
                                    <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:280px;margin-top:10px;" >
                                        <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                                            <thead id="thead1"  style="position: sticky;top: 0">
                                                    
                                                    <tr>
                                                        <th colspan="{{$AlpsStatus['colspan']}}"></th>
                                                        <th colspan="5">Sales Order / SO Amendment</th>
                                                        <th colspan="7">Sales Challan</th>
                                                        <th colspan="1"></th>
                                                    </tr>
                                                    <tr>
                                                        <th width="10%">SO No / SOA No<input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1" value="{{$objCount1}}"></th>
                                                        <th width="10%">Item Code</th>
                                                        <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</th>
                                                        <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</th>
                                                        <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</th>
                                                        <th  width="15%">Item Name</th>
                                               
                                                        <th width="15%">Item Specification</th>
                                                        <th>Main UOM</th>
                                                        <th>Qty (Main UOM)</th>
                                                        <th>SO Pending Qty</th>
                                                        <th>ALT UOM</th>
                                                        <th>Qty (Alt UOM)</th>
                                                        <th>Store</th>
                                                        <th>Store Name</th>
                                                        <th>SO / OSO Rate</th>
                                                        <th>Main UOM</th>
                                                        <th>Qty (Main UOM)</th>
                                                        <th>ALT UOM</th>
                                                        <th>Qty (Alt UOM)</th>                                                       
                                                        <th  width="6%">Action</th>
                                                    </tr>
                                            </thead>
                                            <tbody>
                                            @if(!empty($objSCMAT))
                                                @foreach($objSCMAT as $key => $row)
                                 
                                                <tr  class="participantRow">
                                             <td hidden>
                                                        <input  class="form-control" type="hidden" name={{"SCMATID_".$key}} id ={{"SCMATID_".$key}} maxlength="100" value="{{ $row->SCMATID }}" autocomplete="off"   >
                                                    </td>
                                                    <td hidden><input type="hidden" name={{"DEALERID_REF_".$key}} id ={{"DEALERID_REF_".$key}} class="form-control" autocomplete="off" value="{{ $row->DEALERID_REF }}" /></td>
                                 
                                                    <td hidden><input type="hidden" name={{"SCHEMEID_REF_".$key}} id ={{"SCHEMEID_REF_".$key}} class="form-control" autocomplete="off" value="{{ $row->SCHEMEID_REF }}" /></td>
                                                    <td style="text-align:center;"> 

                                                    <input {{$InputStatus}} type="text" name={{"txtSO_popup_".$key}} id={{"txtSO_popup_".$key}} class="form-control"  autocomplete="off" value="{{$row->SONO}}"   readonly/></td>
                                                    <td hidden><input type="hidden" name={{"SOID_REF_".$key}} id={{"SOID_REF_".$key}} class="form-control" autocomplete="off" value="{{$row->SO}}" /></td>
                                                    <td hidden><input type="hidden" name={{"SQID_REF_".$key}} id={{"SQID_REF_".$key}} class="form-control" autocomplete="off" value="{{$row->SQID_REF}}" /></td>
                                                    <td hidden><input type="hidden" name={{"SEID_REF_".$key}} id={{"SEQID_REF_".$key}} class="form-control" autocomplete="off" value="{{$row->SEID_REF}}" /></td>                                   

                                                    <td><input {{$InputStatus}} type="text" name={{"popupITEMID_".$key}} id={{"popupITEMID_".$key}} class="form-control"  autocomplete="off" value="{{$row->ICODE}}"  readonly/></td>
                                                    
                                                    <td {{$AlpsStatus['hidden']}}><input {{$InputStatus}} type="text" name={{"ALPSPARTNO_".$key}} id={{"ALPSPARTNO_".$key}} class="form-control"  autocomplete="off" value="{{$row->ALPS_PARTNO}}" readonly/></td>
                                                    <td {{$AlpsStatus['hidden']}}><input {{$InputStatus}} type="text" name="Custpartno_{{$key}}" id="Custpartno_{{$key}}" class="form-control"  autocomplete="off" value="{{ isset($row->CUSTOMER_PART_NO)?$row->CUSTOMER_PART_NO:''}}" readonly/></td>
                                                    <td {{$AlpsStatus['hidden']}}><input {{$InputStatus}} type="text" name="OEMpartno_{{$key}}"  id="OEMpartno_{{$key}}" class="form-control"  autocomplete="off"  value="{{ isset($row->OEM_PART_NO)?$row->OEM_PART_NO:'' }}" readonly/></td>
                                                    
                                                    
                                                    <td hidden><input type="hidden" name={{"ITEMID_REF_".$key}} id={{"ITEMID_REF_".$key}} class="form-control" autocomplete="off" value="{{$row->ITEMID_REF}}" /></td>

                                                    <td><input {{$InputStatus}} type="text" name={{"ItemName_".$key}} id={{"ItemName_".$key}} class="form-control"  autocomplete="off" value="{{$row->ItemName}}"  readonly/></td>
                                       
                                                    <td><input {{$InputStatus}} type="text" name={{"ITEMSPECI_".$key}} id={{"ITEMSPECI_".$key}} class="form-control"  autocomplete="off"  readonly value="{{$row->ITEMSPECI}}"/></td>
                                                    <td><input {{$InputStatus}} type="text" name={{"SOMUOM_".$key}} id={{"SOMUOM_".$key}} class="form-control"  autocomplete="off" value="{{$row->SOMUOM}}" readonly/></td>
                                                    <td><input {{$InputStatus}} type="text" name={{"SOMUOMQTY_".$key}} id={{"SOMUOMQTY_".$key}} class="form-control three-digits" maxlength="13"  autocomplete="off" value="{{$row->SOMUOMQTY}}" readonly/></td>
                                                    <td><input {{$InputStatus}} type="text" name={{"SOPENDINGQTY_".$key}} id={{"SOPENDINGQTY_".$key}} class="form-control three-digits"  autocomplete="off" value="{{$row->SOPENDINGQTY}}" readonly/></td>
                                                    <td><input {{$InputStatus}} type="text" name={{"SOAUOM_".$key}} id={{"SOAUOM_".$key}} class="form-control"  autocomplete="off" value="{{$row->SOAUOM}}" readonly/></td>
                                                    <td><input {{$InputStatus}} type="text" name={{"SOAUOMQTY_".$key}} id={{"SOAUOMQTY_".$key}} class="form-control three-digits" maxlength="13" autocomplete="off" value="{{$row->SOAUOMQTY}}" readonly/></td>
                                                    
                                                    <td align="center" ><button {{$InputStatus}} class="btn" id={{"SCSTR_".$key}} name={{"SCSTR_".$key}} onclick="GetStore(this.id);" type="button"><i class="fa fa-clone"></i></button></td>

                                                    <td hidden ><input type="hidden" id={{"BATCHID_REF_".$key}} name={{"BATCHID_REF_".$key}}  class="form-control" autocomplete="off" value="{{$row->BATCHID_REF}}" /></td>
                                                    <td hidden ><input type="hidden" id={{"STORE_QTYS_".$key}} name={{"STORE_QTYS_".$key}}  class="form-control" autocomplete="off" value="{{$row->STORE_QTYS}}" /></td>
                                                    

                                                    <td><input {{$InputStatus}} type="text" name="STORE_NAME_{{$key}}" id="STORE_NAME_{{$key}}" value="{{ isset($row->STORE_NAME)?$row->STORE_NAME:'' }}"  class="form-control w-100" autocomplete="off" readonly style="width:200px;" ></td>
                                                    <td><input type="text" name="AVG_RATE_{{$key}}" id="AVG_RATE_{{$key}}" value="{{ isset($row->RATEPUOM)?$row->RATEPUOM:'' }}"  class="form-control w-100" autocomplete="off" readonly style="width:100px;" ></td>
                                                    <td><input {{$InputStatus}} type="text" name={{"popupMUOM_".$key}} id={{"popupMUOM_".$key}} class="form-control"  autocomplete="off" value="{{$row->popupMUOM}}" readonly/></td>
                                                    <td hidden><input type="hidden" name={{"MAINUOMID_REF_".$key}} id={{"MAINUOMID_REF_".$key}} class="form-control"  autocomplete="off" value="{{$row->MAINUOMID_REF}}" /></td>
                                                    <td><input {{$InputStatus}} type="text" name={{"CHALLAN_MAINQTY_".$key}} id={{"CHALLAN_MAINQTY_".$key}} class="form-control three-digits" maxlength="13" value="{{$row->CHALLAN_MAINQTY}}" autocomplete="off" readonly  /></td>
                                                    <td hidden><input type="hidden" name={{"SO_FQTY_".$key}} id={{"SO_FQTY_".$key}} class="form-control three-digits" maxlength="13"  autocomplete="off" value="1" readonly/></td>
                                                    <td><input {{$InputStatus}} type="text" name={{"popupAUOM_".$key}} id={{"popupAUOM_".$key}} class="form-control"  autocomplete="off" value="{{$row->popupAUOM}}"  readonly/></td>
                                                    <td hidden><input type="hidden" name={{"ALTUOMID_REF_".$key}} id={{"ALTUOMID_REF_".$key}} class="form-control"  autocomplete="off"  readonly value="{{$row->ALTUOMID_REF}}"/></td>
                                                    <td><input {{$InputStatus}} type="text" name={{"ALT_UOMID_QTY_".$key}} id={{"ALT_UOMID_QTY_".$key}} class="form-control three-digits" maxlength="13" value="{{$row->ALT_UOMID_QTY}}" autocomplete="off"  readonly/></td>
                                             
                                                    
                                                              
                                             
                                                    
                                                    <td align="center" ><button {{$InputStatus}} class="btn add" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button>
                                                    <button {{$InputStatus}} class="btn remove" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
                                                </tr>
                                                <tr></tr>
                                                @endforeach 
                                            @endif
                                            </tbody>
                                    </table>
                                    </div>	
                                </div>
                                
                                <div id="udf" class="tab-pane fade">
                                    <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="margin-top:10px;height:280px;width:50%;">
                                        <table id="example4" class="display nowrap table table-striped table-bordered itemlist" style="height:auto !important;">
                                            <thead id="thead1"  style="position: sticky;top: 0">
                                            <tr >
                                                <th>UDF Fields<input class="form-control" type="hidden" name="Row_Count2" id ="Row_Count2" value="objCount2"></th>
                                                <th>Value / Comments</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(!empty($objUdfSCData))
                                              @foreach($objUdfSCData as $uindex=>$uRow)
                                                <tr  class="participantRow4">
                                                    <td><input {{$InputStatus}} type="text" name={{"popupUDFSCID_".$uindex}} id={{"popupUDFSCID_".$uindex}} class="form-control" value="{{$uRow->LABEL}}" autocomplete="off"  readonly/></td>
                                                    <td hidden><input type="hidden" name={{"SC_UDFID_REF_".$uindex}} id={{"SC_UDFID_REF_".$uindex}} class="form-control" value="{{$uRow->UDF_SCID}}" autocomplete="off"   /></td>
                                                    <td hidden><input type="hidden" name={{"UDFismandatory_".$uindex}} id={{"UDFismandatory_".$uindex}} value="{{$uRow->ISMANDATORY}}" class="form-control"   autocomplete="off" /></td>
                                                    <td id={{"udfinputid_".$uindex}} >
                                                    </td>
                                                    <td align="center" ><button class="btn add UDF" title="add" data-toggle="tooltip" type="button" disabled><i class="fa fa-plus"></i></button>
                                                    <button class="btn remove DUDF" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
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
    </div><!--purchase-order-view-->

<!-- </div> -->

@endsection
@section('alert')


<!-- Store -->
<div id="storepopup" class="modal" role="dialog"  data-backdrop="static" >
  <div class="modal-dialog modal-md" style="width:1250px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='storeclosePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Store</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="storeTable" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
      <thead style="position: sticky;top: 0">
        <tr>
                <th>Batch / Lot No</th>
                <th>Store</th>
                <th>Main UoM (MU)</th>
                <th>Stock-in-hand</th>
                <th>Dispatch Qty (MU)</th>
                <th>Rate</th>
                <th>Alt UOM (AU)</th>
                <th>Dispatch Qty (AU)</th>
        </tr>
        <input type="hidden" id="hdnSOID" name="hdnSOID" />
        <input type="hidden" id="hdnSQID" name="hdnSQID" />
        <input type="hidden" id="hdnSEQID" name="hdnSEQID" />
        <tr>        
        <td id="Data_seach_Store" colspan="8">please wait...</td>
        </tr>

      </thead>
      <tbody id="tbody_store">
          
      </tbody>

      <tr  class="participantRowFotter">
            <td colspan="3" style="text-align:center;font-weight:bold;">TOTAL</td>    

            <td id="strSOTCK_total"   style="text-align:right;font-weight:bold;"></td>                                                                                  
            <td id="strDISPATCH_MAIN_QTY_total"       style="text-align:right;font-weight:bold;"></td>
            <td id="strRATE_total"       style="text-align:right;font-weight:bold;"></td>
            <td></td>
            <td id="DISPATCH_ALT_QTY_total"   style="text-align:right;font-weight:bold;"></td>
                                      
      </tr>



    </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Store-->

<!-- Transporter Dropdown -->
<div id="transportpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='transportclosePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Transporter</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="transportTable" class="display nowrap table  table-striped table-bordered">
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
        <td class="ROW2"><input type="text" id="transportcodesearch" class="form-control" onkeyup="transportCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="transportnamesearch" class="form-control" onkeyup="transportNameFunction()"></td>
      </tr>
    </tbody>
    </table>
      <table id="transportTable2" class="display nowrap table  table-striped table-bordered">
        <thead id="thead2">
        </thead>
        <tbody>
        @foreach ($objtransporter as $tindex=>$tRow)
          <tr>
              <td class="ROW1"> <input type="checkbox" name="SELECT_TRANSPORTERID[]" id="TRANSPORTERID_{{ $tindex }}" class="clstrid" value="{{ $tRow-> TRANSPORTERID }}" ></td>
              <td class="ROW2">{{ $tRow-> TRANSPORTER_CODE }}
                <input type="hidden" id="txtTRANSPORTERID_{{ $tindex }}" data-desc="{{ $tRow-> TRANSPORTER_CODE }}" data-desc2="{{ $tRow-> TRANSPORTER_NAME }}"  value="{{ $tRow-> TRANSPORTERID }}"/>
              </td>
              <td class="ROW3">{{ $tRow-> TRANSPORTER_NAME }}</td>
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
<!-- Transporter Dropdown-->



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
        <td class="ROW2"><input type="text" id="customercodesearch" class="form-control" onkeyup="CustomerCodeFunction('{{$FormId}}')"></td>
        <td class="ROW3"><input type="text" id="customernamesearch" class="form-control" onkeyup="CustomerNameFunction('{{$FormId}}')"></td>
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



<!-- Sales Order Dropdown -->
<div id="SOpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md "style="width:70%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='SOclosePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p id="popup_heading"></p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="SalesOrderTable" class="display nowrap table  table-striped table-bordered">
    <thead>
          <tr id="none-select" class="searchalldata" hidden>
            
            <td> <input type="hidden" name="fieldid" id="hdn_soid"/>
            <input type="hidden" name="fieldid2" id="hdn_soid2"/></td>
          </tr>
    <tr>
    <th style="width:10%;text-align:center;">Select</th> 
        <th style="width:15%;">Order No</th>
        <th style="width:15%;" >Order Date</th>
        <th style="width:15%;" >Customer PO No</th>
        <th style="width:15%;">Customer Amendment Ref No</th>
        <th style="width:30%;" >Remarks</th>
    </tr>
    </thead>
    <tbody>
      <tr>
        <th style="width:10%;text-align:center;"><span >&#10004;</span></th>
        <td style="width:15%;"><input type="text" id="SalesOrdercodesearch" class="form-control" onkeyup="SalesOrderCodeFunction()"></td>
        <td style="width:15%;"><input type="text" id="SalesOrdernamesearch" class="form-control" onkeyup="SalesOrderNameFunction()"></td>
        <td style="width:15%;"><input type="text" id="CustomerPONosearch" class="form-control" onkeyup="CustomerponoFunction()"></td>
        <td style="width:15%;"><input type="text" id="AmendmentNOsearch" class="form-control" onkeyup="CustomeramendmentFunction()"></td>
        <td style="width:30%;"><input type="text" id="Remarksearch" class="form-control" onkeyup="RemarksFunction()"></td>
      </tr>
    </tbody>
    </table>
      <table id="SalesOrderTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
        </thead>
        <tbody  id="tbody_SO">     
       
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Sales Quotation Dropdown-->

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
            <input type="hidden" name="fieldid18" id="hdn_ItemID18"/>
            <input type="hidden" name="fieldid19" id="hdn_ItemID19"/>
            <input type="hidden" name="fieldid20" id="hdn_ItemID20"/>
            <input type="hidden" name="fieldid21" id="hdn_ItemID21"/>
            <input type="hidden" name="fieldid22" id="hdn_ItemID22"/>
            <input type="hidden" name="fieldid25" id="hdn_ItemID25"/>
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
        <td style="width:8%;"><input type="text" id="ItemStatussearch" class="form-control" autocomplete="off" onkeyup="ItemStatusFunction()"></td>
      </tr>                            
    </tbody>
    </table>
      <table id="ItemIDTable2" class="display nowrap table  table-striped table-bordered" style="width:100%;"  >
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
    <table id="altuomTable" class="display nowrap table  table-striped table-bordered">
    <thead>
    <tr id="none-select" class="searchalldata" hidden>
            
            <td> <input type="hidden" name="fieldid" id="hdn_altuom"/>
            <input type="hidden" name="fieldid2" id="hdn_altuom2"/>
            <input type="hidden" name="fieldid3" id="hdn_altuom3"/>
            <input type="hidden" name="fieldid4" id="hdn_altuom4"/></td>
          </tr>
    <tr>
      <th class="ROW1">Select</th> 
      <th class="ROW2">UOM Code</th>
      <th class="ROW3">UOM Desc</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="altuomcodesearch" class="form-control" onkeyup="altuomCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="altuomnamesearch" class="form-control" onkeyup="altuomNameFunction()"></td>
    </tr>
    </tbody>
    </table>
      <table id="altuomTable2" class="display nowrap table  table-striped table-bordered">
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
    <table id="BillToTable" class="display nowrap table  table-striped table-bordered">
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
        <td class="ROW2"><input type="text" id="BillTocodesearch" class="form-control" onkeyup="BillToCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="BillTonamesearch" class="form-control" onkeyup="BillToNameFunction()"></td>
      </tr>
    </tbody>
    </table>
      <table id="BillToTable2" class="display nowrap table  table-striped table-bordered">
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
    <table id="ShipToTable" class="display nowrap table  table-striped table-bordered">
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
        <td class="ROW2"><input type="text" id="ShipTocodesearch" class="form-control" onkeyup="ShipToCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="ShipTonamesearch" class="form-control" onkeyup="ShipToNameFunction()"></td>
      </tr>
    </tbody>
    </table>
      <table id="ShipToTable2" class="display nowrap table  table-striped table-bordered">
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


@push('bottom-css')
<style>
#custom_dropdown, #frm_trn_sc_filter {
    display: inline-table;
    margin-left: 15px;
}
.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
    padding: 7px;
    line-height: 1.42857143;
    vertical-align: top;
    border-top: 1px solid #ddd;
}
.dataTables_wrapper .row:nth-child(1) .col-sm-6:nth-child(2){text-align:right;}
#filtercolumn{color: #555;
    background-color: #fff;
    background-image: none;
    border: 1px solid #ccc;
    }

/* .table-bordered.itemlist tr th {
    padding: 5px 5px;
    font-size: 13px;
    border: 1px solid#0f69cc !important;
    color: #0f69cc;
    background: #eff7fb;
    font-weight: 400;
    text-align: center;
    position: sticky;
    top: 0;
} */
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
#storeTable2 {
  border-collapse: collapse;
  width: 1450px;
  border: 1px solid #ddd;
  font-size: 11px;
}

#storeTable2 th{
    text-align: left;
    padding: 5px;
   
    font-size: 11px;
    
    color: #0f69cc;
    font-weight: 600;
}

#storeTable2 td {
  text-align: left;
    padding: 5px;
    font-size: 11px;
   
    font-weight: 600;
    width: 14%;
}
#storeTable th {
    text-align: center;
    padding: 5px;
   
    font-size: 11px;
   
    color: #0f69cc;
    font-weight: 600;
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



//Sub GL Account Starts
//------------------------
$("#txtsubgl_popup").click(function(event){      
    var CODE = ''; 
    var NAME = ''; 
    var FORMID = "{{$FormId}}";
    loadCustomer(CODE,NAME,FORMID);
    $("#customer_popus").show();
    event.preventDefault();
});

$("#customer_closePopup").on("click",function(event){ 
  $("#customer_popus").hide();
    $("#customercodesearch").val(''); 
    $("#customernamesearch").val(''); 
   
    event.preventDefault();
});

function bindSubLedgerEvents(){ 
  $(".clssubgl").click(function(){    
            var id = $(this).attr('id');
            var txtval =    $("#txt"+id+"").val();
            var texdesc =   $("#txt"+id+"").data("desc");
            var oldSLID =   $("#SLID_REF").val();
            var MaterialClone = $('#hdnMaterial').val();
            $("#txtsubgl_popup").val(texdesc);
            $("#txtsubgl_popup").blur();
            $("#SLID_REF").val(txtval);
            if (txtval != oldSLID)
            {
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
            $("#customer_popus").hide();
            $("#customercodesearch").val(''); 
            $("#customernamesearch").val(''); 
            var customid = txtval;
              if(customid!=''){
                
                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
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
                      url:'{{route("transaction",[43,"getBillTo"])}}',
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
                      url:'{{route("transaction",[43,"getShipTo"])}}',
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
                         
              }
              event.preventDefault();
        });
  }
//Sub GL Account Ends
//------------------------

//------------------------
  //Transporter Account
  let trtid = "#transportTable2";
      let trtid2 = "#transportTable";
      let trheaders = document.querySelectorAll(trtid2 + " th");

      // Sort the table element when clicking on the table headers
      trheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(trtid, ".clstrid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function transportCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("transportcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("transportTable2");
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

  function transportNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("transportnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("transportTable2");
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

  $('#TRANSPORTERID_popup').click(function(event){
         $("#transportpopup").show();
         event.preventDefault();
      });

      $("#transportclosePopup").click(function(event){
        $("#transportpopup").hide();
        event.preventDefault();
      });

      $(".clstrid").click(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");
        var txtname =   $("#txt"+fieldid+"").data("desc2");
        // var txtid= $('#hdn_fieldid').val();
        // var txt_id2= $('#hdn_fieldid2').val();
        
        $('#TRANSPORTERID_popup').val(texdesc);
        $('#TRANSPORTERID_REF').val(txtval);
        $('#TRANSPORTERNAME').val(txtname);
        $("#transportpopup").hide();
        $("#transportcodesearch").val(''); 
        $("#transportnamesearch").val(''); 
      
        
        event.preventDefault();
      });

      

  //Transporter Ends
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

  function BillToNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("BillTonamesearch");
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
  $('#div_billto').on('click','#txtBILLTO',function(event){
    var customid = $('#SLID_REF').val();
        $("#tbody_BillTo").html('');
                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  })
                  $.ajax({
                      url:'{{route("transaction",[43,"getBillAddress"])}}',
                      type:'POST',
                      data:{'id':customid},
                      success:function(data) {
                        $("#tbody_BillTo").html(data);       
                        BindShipAddress();                 
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                        $("#tbody_BillTo").html('');
                      },
                  });
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

  function ShipToNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("ShipTonamesearch");
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

  $('#div_shipto').on('click','#txtSHIPTO',function(event){
    var customid = $('#SLID_REF').val();
        $("#tbody_ShipTo").html('');
                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  })
                  $.ajax({
                      url:'{{route("transaction",[43,"getShipAddress"])}}',
                      type:'POST',
                      data:{'id':customid},
                      success:function(data) {
                        $("#tbody_ShipTo").html(data);       
                        BindShipAddress();                 
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                        $("#tbody_ShipTo").html('');
                      },
                  });
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
          var texdesc =   $("#txt"+fieldid+"").data("desc");
          
          $('#txtSHIPTO').val(texdesc);
          $('#SHIPTO').val(txtval);
          $("#ShipTopopup").hide();
          $("#ShipTocodesearch").val(''); 
          $("#ShipTonamesearch").val(''); 
        
          event.preventDefault();
        });
      }
  //Ship Address Ends
//------------------------
//------------------------
// Store Popup
let strtid = "#storeTable";
      let strheaders = document.querySelectorAll(strtid + " th");

      // Sort the table element when clicking on the table headers
      strheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(strtid, ".clsstrid", "td:nth-child(" + (i + 1) + ")");
        });
      });




function GetStore(id){  
var RawID             =   id.split('_').pop();
var ITEMID_REF        =   $.trim($("#ITEMID_REF_"+RawID).val());
var BATCHID_REF       =   $.trim($("#BATCHID_REF_"+RawID).val());
var STORE_QTYS        =   $.trim($("#STORE_QTYS_"+RawID).val());
var MAINUOMID_REF     =   $("#MAINUOMID_REF_"+RawID).val();
var ALTUOMID_REF      =   $("#ALTUOMID_REF_"+RawID).val();

if(ITEMID_REF ===""){
  $("#FocusId").val('popupITEMID_'+RawID);
  $("#ProceedBtn").focus();
  $("#YesBtn").hide();
  $("#NoBtn").hide();
  $("#OkBtn1").show();
  $("#AlertMessage").text('Please select item.');
  $("#alert").modal('show');
  $("#OkBtn1").focus();
  return false;
}
else{
  $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
    })
    $("#Data_seach_Store").show();
    $('#tbody_store').html('');
    $(".participantRowFotter").hide();
   $.ajax({
                url:'{{route("transaction",[$FormId,"getItemwiseStoreDetails"])}}',
                type:'POST',
                data:{ITEMID_REF:ITEMID_REF,BATCHID_REF:BATCHID_REF,STORE_QTYS:STORE_QTYS,ACTION_TYPE:'EDIT',MAINUOMID_REF:MAINUOMID_REF,ALTUOMID_REF:ALTUOMID_REF,RawID:RawID},
              
                success:function(data) {
                  $("#Data_seach_Store").hide();
                    $('#tbody_store').html(data);
                    $(".participantRowFotter").show();
                    bindStoreEvents();
                    getTotalRowValue();
                    event.preventDefault();  
                   },
                error:function(data){
                  $("#Data_seach_Store").hide();
                  console.log("Error: Something went wrong.");
                  $('#tbody_store').html('');
                  $(".participantRowFotter").show();
                  bindStoreEvents();
                  getTotalRowValue();
                  event.preventDefault();               
                },
            });  
        }
        $("#storepopup").show();
      }


$("#storeclosePopup").click(function(event){
  
var BATCHID      = [];
var QTY          = [];
var ALT_QTY      = [];
var STORE_NAME   = [];
var RawID        = '';

  $('#storeTable').find('.clsstrid').each(function(){
     RawID = $("#RawID").val();

    if($(this).find('[id*="strDISPATCH_MAIN_QTY"]').val() != '')
  {
    var BATCHID_REF     =     $(this).find('[id*="strBATCHID"]').val();
                              BATCHID.push(BATCHID_REF);
    var D_QTY           =     $(this).find('[id*="strDISPATCH_MAIN_QTY"]').val();
                              QTY.push(parseFloat(D_QTY));
    var A_QTY           =     $(this).find('[id*="DISPATCH_ALT_QTY"]').val();
                              ALT_QTY.push(parseFloat(A_QTY));
    var ST_NAME         =     $(this).find('[id*="STORE_REF"]').val();
    if(jQuery.inArray(ST_NAME, STORE_NAME) == -1){
                              STORE_NAME.push(ST_NAME);
  }
   

  } 
});  


var TotalQty= getArraySum(QTY); 
if(intRegex.test(TotalQty)){
  TotalQty = (TotalQty +'.000');
}

var TotalAltQty= getArraySum(ALT_QTY); 
if(intRegex.test(TotalAltQty)){
  TotalAltQty = (TotalAltQty +'.000');
}


  $("#CHALLAN_MAINQTY_"+RawID).val(TotalQty);
  $("#ALT_UOMID_QTY_"+RawID).val(TotalAltQty);
  $("#BATCHID_REF_"+RawID).val(BATCHID);
  $("#STORE_QTYS_"+RawID).val(QTY);
  $("#STORE_NAME_"+RawID).val(STORE_NAME);
  $("#storepopup").hide();  
    if(parseFloat($.trim($("#SOPENDINGQTY_"+RawID).val())) != "" && parseFloat($.trim($("#SOPENDINGQTY_"+RawID).val())) < parseFloat($.trim(TotalQty)) ){
      $("#storepopup").hide();
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#CHALLAN_MAINQTY_"+RawID).val('0.000');
      $("#NoBtn").hide();
      $("#OkBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Dispatch Quantity cannot be greater than SO Pending Quantity.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  }
});

      

		
	function bindStoreEvents(){
		
        $('#storeTable').on('keyup','[id*="strDISPATCH_MAIN_QTY"]',function(event){
			
			var dqty 		=	$(this).val();
			var stockqty 	= 	$(this).parent().parent().find('[id*="strSOTCK"]').val();
			
			if(parseFloat(dqty) > parseFloat(stockqty)){
				$(this).val('');
				$(this).parent().parent().find('[id*="DISPATCH_ALT_QTY"]').val('');
				$("#FocusId").val($(this));
				$("#ProceedBtn").focus();
				$("#YesBtn").hide();
				$("#NoBtn").hide();
				$("#OkBtn").hide();
				$("#OkBtn1").show();
				$("#AlertMessage").text('Dispatch Quantity cannot be greater than Stock In Hand.');
				$("#alert").modal('show');
				$("#OkBtn1").focus();
        getTotalRowValue();
				return false;
			}
			else{
			  var mqty = $(this).parent().parent().find('[id*="CONV_MAIN_QTY"]').val();
			  var aqty = $(this).parent().parent().find('[id*="CONV_ALT_QTY"]').val();
			  var daltqty = parseFloat((dqty * aqty)/mqty).toFixed(3);
			  $(this).parent().parent().find('[id*="DISPATCH_ALT_QTY"]').val(daltqty);
        getTotalRowValue(); 
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
	  

// Store Popup Ends


//------------------------

//------------------------
   //Sales Order Dropdown
   let sotid = "#SalesOrderTable2";
      let sotid2 = "#SalesOrderTable";
      let soheaders = document.querySelectorAll(sotid2 + " th");

      // Sort the table element when clicking on the table headers
      soheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(sotid, ".clssoid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function SalesOrderCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("SalesOrdercodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("SalesOrderTable2");
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

  function SalesOrderNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("SalesOrdernamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("SalesOrderTable2");
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

  function CustomerponoFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("CustomerPONosearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("SalesOrderTable2");
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

  function CustomeramendmentFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("AmendmentNOsearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("SalesOrderTable2");
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

  function RemarksFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Remarksearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("SalesOrderTable2");
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


  $('#Material').on('click','[id*="txtSO_popup"]',function(event){

    if($("#SLID_REF").val() ===""){
    $("#FocusId").val('txtsubgl_popup');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select Customer.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
    var TYPE=$('#TYPE :selected').val();
    if(TYPE=='SO') 
        {
        $("#popup_heading").html('Sales Order');
        }else{
        $("#popup_heading").html('Open Sales Order');
        }



                  var customid = $('#SLID_REF').val();
                  var BILLTO = $('#BILLTO').val();
                  var SHIPTO = $('#SHIPTO').val();
                  $("#tbody_SO").html('');
               
                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  })
                  $.ajax({
                      url:'{{route("transaction",[$FormId,"getsalesorder"])}}',
                      type:'POST',
                      data:{'id':customid,'BILLTO':BILLTO,'SHIPTO':SHIPTO,'TYPE':TYPE},
                      success:function(data) {
                        $("#tbody_SO").html(data);
                        BindSalesOrder();
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                        $("#tbody_SO").html('');
                      },
                  }); 



        $("#SOpopup").show();
        var id = $(this).attr('id');
        var id2 = $(this).parent().parent().find('[id*="SOID_REF"]').attr('id');

        $('#hdn_soid').val(id);
        $('#hdn_soid2').val(id2);

        $(this).parent().parent().find('[id*="popupITEMID"]').val('');
        $(this).parent().parent().find('[id*="ITEMID_REF"]').val('');
        $(this).parent().parent().find('[id*="ItemName"]').val('');
        $(this).parent().parent().find('[id*="SOPENDINGQTY"]').val('');
        $(this).parent().parent().find('[id*="ITEMSPECI"]').val('');
        $(this).parent().parent().find('[id*="SOMUOM"]').val('');
        $(this).parent().parent().find('[id*="SOMUOMQTY"]').val('');
        $(this).parent().parent().find('[id*="SOAUOM"]').val('');
        $(this).parent().parent().find('[id*="SOAUOMQTY"]').val('');
        $(this).parent().parent().find('[id*="popupMUOM"]').val('');
        $(this).parent().parent().find('[id*="MAINUOMID_REF"]').val('');
        $(this).parent().parent().find('[id*="CHALLAN_MAINQTY"]').val('');
        $(this).parent().parent().find('[id*="SO_FQTY"]').val('');
        $(this).parent().parent().find('[id*="popupAUOM"]').val('');
        $(this).parent().parent().find('[id*="ALTUOMID_REF"]').val('');
        $(this).parent().parent().find('[id*="ALT_UOMID_QTY"]').val('');
        $(this).parent().parent().find('[id*="STORE"]').val('');
        $(this).parent().parent().find('[id*="TotalHiddenQty"]').val('');
        $(this).parent().parent().find('[id*="HiddenRowId"]').val('');
        $(this).parent().parent().find('[id*="ALPSPARTNO"]').val('');

      });






      $("#SOclosePopup").click(function(event){
        $("#SOpopup").hide();
      });
      function BindSalesOrder(){
        $(".clssoid").click(function(){
          var fieldid     =   $(this).attr('id');
          var txtval      =   $("#txt"+fieldid+"").val();
          var texdesc     =   $("#txt"+fieldid+"").data("desc");
          var dealerid    =   $("#txt"+fieldid+"").data("desc1");
          var txtid       =   $('#hdn_soid').val();
          var txt_id2     =   $('#hdn_soid2').val();

          var DEALDER  = [];
        
          $('#example2').find('.participantRow').each(function(){
            if($(this).find('[id*="DEALERID_REF"]').val() != ''){
              var dealderid  = $(this).find('[id*="DEALERID_REF"]').val();
                if(dealderid!=''){
                      if(jQuery.inArray(dealderid, DEALDER) == -1){
                        DEALDER.push(dealderid);
                  }
                }
            }
          });

    if(DEALDER.length != 0 && dealerid != ''){

      // if(jQuery.inArray(dealerid, DEALDER) !== -1){
      //   alert("true");
      //       }else{
      //         alert("false");
      //       }
      if(DEALDER != dealerid){
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Kindly select the Sales order No with Same Dealer.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;            
      }
    }

          $('#'+txtid).val(texdesc);
          $('#'+txt_id2).val(txtval);
          $('#DEALERID_REF_'+txtid.split('_').pop()).val(dealerid);
          $("#SOpopup").hide();
          
          $("#SalesOrdercodesearch").val(''); 
          $("#SalesOrdernamesearch").val(''); 
         
          event.preventDefault();
        });
      }


  //Sales Order Dropdown Ends
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

  if($("#SOID_REF_"+(this.id).split('_').pop()).val() ===""){
    $("#FocusId").val('txtSO_popup_'+(this.id).split('_').pop());
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select SO No / OSO No.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }

    
var TYPE=$('#TYPE :selected').val();
  if(TYPE=='SO'){ 

    var SalesOrderID = $(this).parent().parent().find('[id*="SOID_REF"]').val();
    var taxstate = $('#Tax_State').val();
    if(SalesOrderID!=''){
            $("#tbody_ItemID").html('');
              $.ajaxSetup({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  }
              });
              $.ajax({
                  url:'{{route("transaction",[$FormId,"getItemDetailsSalesOrderwise"])}}',
                  type:'POST',
                  data:{'id':SalesOrderID, 'taxstate':taxstate},
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
    

    $("#ITEMIDpopup").show();
    var id = $(this).attr('id');
    var id2 = $(this).parent().parent().find('[id*="ITEMID_REF"]').attr('id');
    var id3 = $(this).parent().parent().find('[id*="ItemName"]').attr('id');
    var id4 = $(this).parent().parent().find('[id*="SOPENDINGQTY"]').attr('id');
    var id5 = $(this).parent().parent().find('[id*="ITEMSPECI"]').attr('id');
    var id6 = $(this).parent().parent().find('[id*="SOMUOM"]').attr('id');
    var id7 = $(this).parent().parent().find('[id*="SOMUOMQTY"]').attr('id');
    var id8 = $(this).parent().parent().find('[id*="SOAUOM"]').attr('id');
    var id9 = $(this).parent().parent().find('[id*="SOAUOMQTY"]').attr('id');
    var id10 = $(this).parent().parent().find('[id*="popupMUOM"]').attr('id');
    var id11 = $(this).parent().parent().find('[id*="MAINUOMID_REF"]').attr('id');
    var id12 = $(this).parent().parent().find('[id*="CHALLAN_MAINQTY"]').attr('id');
    var id13 = $(this).parent().parent().find('[id*="popupAUOM"]').attr('id');
    var id14 = $(this).parent().parent().find('[id*="ALTUOMID_REF"]').attr('id');
    var id15 = $(this).parent().parent().find('[id*="ALT_UOMID_QTY"]').attr('id');
    var id16 = $(this).parent().parent().find('[id*="SO_FQTY"]').attr('id');
    var id25 = $(this).parent().parent().find('[id*="ALPSPARTNO"]').attr('id');

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
    $('#hdn_ItemID17').val(SalesOrderID);
    $('#hdn_ItemID17').val(SalesOrderID);
    $('#hdn_ItemID25').val(id25);
    var r_count = 0;
    var SalesOrder = [];
    $('#example2').find('.participantRow').each(function(){
      if($(this).find('[id*="ITEMID_REF"]').val() != '')
      {
        SalesOrder.push($(this).find('[id*="txtSO_popup"]').val());
        r_count = parseInt(r_count)+1;
        $('#hdn_ItemID22').val(r_count);
      }
    });
    $('#hdn_ItemID18').val(SalesOrder.join(', '));
    var ItemID = [];
    $('#example2').find('.participantRow').each(function(){
      if($(this).find('[id*="ITEMID_REF"]').val() != '')
      {
        ItemID.push($(this).find('[id*="ITEMID_REF"]').val());
      }
    });

    $('#hdn_ItemID19').val(ItemID.join(', '));
    var EnquiryID = [];
    $('#example2').find('.participantRow').each(function(){
      if($(this).find('[id*="SEQID_REF"]').val() != '')
      {
        EnquiryID.push($(this).find('[id*="SEQID_REF"]').val());
      }
    });
    $('#hdn_ItemID20').val(EnquiryID.join(', '));
    var SQID = [];
    $('#example2').find('.participantRow').each(function(){
      if($(this).find('[id*="SQID_REF"]').val() != '')
      {
        SQID.push($(this).find('[id*="SQID_REF"]').val());
      }
    });
    $('#hdn_ItemID21').val(SQID.join(', '));
    event.preventDefault();


  }else{

    
    var SalesOrderID = $(this).parent().parent().find('[id*="SOID_REF"]').val();
    var taxstate = $('#Tax_State').val();
    if(SalesOrderID!=''){
            $("#tbody_ItemID").html('');
              $.ajaxSetup({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  }
              });
              $.ajax({
                  url:'{{route("transaction",[$FormId,"getItemDetailsOpenSalesOrderwise"])}}',
                  type:'POST',
                  data:{'id':SalesOrderID, 'taxstate':taxstate},
                  success:function(data) {
                    $("#tbody_ItemID").html(data);   
                    bindItemEvents_OSO();                     
                  },
                  error:function(data){
                    console.log("Error: Something went wrong.");
                    $("#tbody_ItemID").html('');                        
                  },
              }); 
    }
    

    $("#ITEMIDpopup").show();
    var id = $(this).attr('id');
    var id2 = $(this).parent().parent().find('[id*="ITEMID_REF"]').attr('id');
    var id3 = $(this).parent().parent().find('[id*="ItemName"]').attr('id');
    var id4 = $(this).parent().parent().find('[id*="SOPENDINGQTY"]').attr('id');
    var id5 = $(this).parent().parent().find('[id*="ITEMSPECI"]').attr('id');
    var id6 = $(this).parent().parent().find('[id*="SOMUOM"]').attr('id');
    var id7 = $(this).parent().parent().find('[id*="SOMUOMQTY"]').attr('id');
    var id8 = $(this).parent().parent().find('[id*="SOAUOM"]').attr('id');
    var id9 = $(this).parent().parent().find('[id*="SOAUOMQTY"]').attr('id');
    var id10 = $(this).parent().parent().find('[id*="popupMUOM"]').attr('id');
    var id11 = $(this).parent().parent().find('[id*="MAINUOMID_REF"]').attr('id');
    var id12 = $(this).parent().parent().find('[id*="CHALLAN_MAINQTY"]').attr('id');
    var id13 = $(this).parent().parent().find('[id*="popupAUOM"]').attr('id');
    var id14 = $(this).parent().parent().find('[id*="ALTUOMID_REF"]').attr('id');
    var id15 = $(this).parent().parent().find('[id*="ALT_UOMID_QTY"]').attr('id');
    var id16 = $(this).parent().parent().find('[id*="SO_FQTY"]').attr('id');
    var id25 = $(this).parent().parent().find('[id*="ALPSPARTNO"]').attr('id');

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
    $('#hdn_ItemID17').val(SalesOrderID);
    $('#hdn_ItemID17').val(SalesOrderID);
    $('#hdn_ItemID25').val(id25);
    var r_count = 0;
    var SalesOrder = [];
    $('#example2').find('.participantRow').each(function(){
      if($(this).find('[id*="ITEMID_REF"]').val() != '')
      {
        SalesOrder.push($(this).find('[id*="txtSO_popup"]').val());
        r_count = parseInt(r_count)+1;
        $('#hdn_ItemID22').val(r_count);
      }
    });
    $('#hdn_ItemID18').val(SalesOrder.join(', '));


    event.preventDefault();







  }



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
    var txtauomqty = 0.000;
    var txtmqtyf = 0.000;
    var fieldid = $(this).attr('id');
    var txtval =   $("#txt"+fieldid+"").val();
    var texdesc =  $("#txt"+fieldid+"").data("desc");
    var fieldid2 = $(this).find('[id*="itemname"]').attr('id');
    var txtname =  $("#txt"+fieldid2+"").val();
    var texspec =  $("#txt"+fieldid2+"").data("desc");



    var fieldid10 = $(this).find('[id*="alps"]').attr('id');
    var alpspartno =  $("#txt"+fieldid10+"").data("desc");

  

    var fieldid3 = $(this).find('[id*="itemuom"]').attr('id');
    var txtmuomid =  $("#txt"+fieldid3+"").val();
    var txtauom =  $("#txt"+fieldid3+"").data("desc");

    var apartno =  $("#txt"+fieldid3+"").data("desc2");
    var cpartno =  $("#txt"+fieldid3+"").data("desc3");
    var opartno =  $("#txt"+fieldid3+"").data("desc4");
    var ratepum =  $("#txt"+fieldid3+"").data("desc5");

    var fieldid4 = $(this).find('[id*="uomqty"]').attr('id');
    var txtauomid =  $("#txt"+fieldid4+"").val();
    txtauomqty =  $("#txt"+fieldid4+"").data("desc");
    var txtmuomqty =  $.trim($(this).find('[id*="uomqty"]').text());

    var fieldid5 = $(this).find('[id*="irate"]').attr('id');
    txtmqtyf = $("#txt"+fieldid5+"").data("desc");
    var txtpendingqty = $("#txt"+fieldid5+"").val();
    var fieldid6 = $(this).find('[id*="itax"]').attr('id');
    var txtsqid = $("#txt"+fieldid6+"").data("desc");
    var txtseqid = $("#txt"+fieldid6+"").val();
    var fieldid7 = $(this).find('[id*="ise"]').attr('id');
    var txtsono = $("#txt"+fieldid7+"").val();
    var txtmuom =  $("#txt"+fieldid7+"").data("desc");
    var rcount1 = parseInt($(this).closest('table').find('.clsitemid').length);
    var rcount2 = $('#hdn_ItemID22').val();
    var r_count2 = 0;
    if(rcount2 == '')
    {
      rcount2 = 0;
    }
    
    txtauomqty = (parseFloat(txtmuomqty)/parseFloat(txtmqtyf))*parseFloat(txtauomqty);
    
  // var intRegex = /^\d+$/;
  if(intRegex.test(txtauomqty)){
      txtauomqty = (txtauomqty +'.000');
  }
  if(intRegex.test(txtmuomqty)){
    txtmuomqty = (txtmuomqty +'.000');
  }
  var SalesOrder2 = [];
  $('#example2').find('.participantRow').each(function(){
    if($(this).find('[id*="ITEMID_REF"]').val() != '')
    {
      var soitem = $(this).find('[id*="txtSO_popup"]').val()+'-'+$(this).find('[id*="SQID_REF"]').val()+'-'+$(this).find('[id*="SEQID_REF"]').val()+'-'+$(this).find('[id*="ITEMID_REF"]').val();
      SalesOrder2.push(soitem);
      r_count2 = parseInt(r_count2) + 1;
    }
  });
  
  var salesorder =  $('#hdn_ItemID18').val();
  var itemids =  $('#hdn_ItemID19').val();
  var enquiryids =  $('#hdn_ItemID20').val();
  var quotationids =  $('#hdn_ItemID21').val();
  //var alpspartno =  $('#hdn_ItemID25').val();


      if($(this).find('[id*="chkId"]').is(":checked") == true) 
      {
        rcount1 = parseInt(rcount2)+parseInt(rcount1);
        if(parseInt(r_count2) >= parseInt(rcount1))
        {
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
              $('#hdn_ItemID21').val('');
              $('#hdn_ItemID25').val('');
              txtval = '';
              texdesc = '';
              txtname = '';
              txtmuom = '';
              txtauom = '';
              txtmuomid = '';
              txtauomid = '';
              txtauomqty='';
              txtmuomqty='';
              txtsqid = '';
              txtseqid = '';
              txtsono = '';
              alpspartno = '';
              $('.js-selectall').prop("checked", false);
              $("#ITEMIDpopup").hide();
              return false;
        }



        var SCHEME  = [];        
  $('#example2').find('.participantRow').each(function(){
    if($(this).find('[id*="SCHEMEID_REF"]').val() != ''){
      var schemeid  = $(this).find('[id*="SCHEMEID_REF"]').val();
        if(schemeid!=''){
              if(jQuery.inArray(schemeid, SCHEME) == -1){
                SCHEME.push(schemeid);
          }
        }
    }
  }); 



        var txtorderitem = txtsono+'-'+txtsqid+'-'+txtseqid+'-'+txtval;
        if(SCHEME.length === 0 && jQuery.inArray(txtorderitem, SalesOrder2) !== -1)
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
              $('#hdn_ItemID12').val('');
              $('#hdn_ItemID13').val('');
              $('#hdn_ItemID14').val('');
              $('#hdn_ItemID15').val('');
              $('#hdn_ItemID16').val('');
              $('#hdn_ItemID17').val('');
              $('#hdn_ItemID18').val('');
              $('#hdn_ItemID19').val('');
              $('#hdn_ItemID19').val('');
              $('#hdn_ItemID25').val('');
              txtval = '';
              texdesc = '';
              txtname = '';
              txtmuom = '';
              txtauom = '';
              txtmuomid = '';
              txtauomid = '';
              txtauomqty='';
              txtmuomqty='';
              txtsqid = '';
              txtseqid = '';
              txtsono = '';
              alpspartno = '';
              $('.js-selectall').prop("checked", false);
              $("#ITEMIDpopup").hide();
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
              var txt_id9= $('#hdn_ItemID9').val();
              var txt_id10= $('#hdn_ItemID10').val();
              var txt_id11= $('#hdn_ItemID11').val();
              var txt_id12= $('#hdn_ItemID12').val();
              var txt_id13= $('#hdn_ItemID13').val();
              var txt_id14= $('#hdn_ItemID14').val();
              var txt_id15= $('#hdn_ItemID15').val();
              var txt_id16= $('#hdn_ItemID16').val();
              var txt_id17= $('#hdn_ItemID17').val();
              var txt_id25= $('#hdn_ItemID25').val();

              var $tr = $('.participantRow').closest('table');
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

                  $clone.find('[id*="ALPSPARTNO"]').val(alpspartno);
                  $clone.find('[id*="Custpartno"]').val(cpartno);
                  $clone.find('[id*="OEMpartno"]').val(opartno);
                  $clone.find('[id*="AVG_RATE"]').val(ratepum);

                  $clone.find('[id*="ITEMID_REF"]').val(txtval);
                  $clone.find('[id*="ItemName"]').val(txtname);
                  $clone.find('[id*="SOPENDINGQTY"]').val(txtpendingqty);
                  $clone.find('[id*="ITEMSPECI"]').val(texspec);
                  $clone.find('[id*="SOMUOM"]').val(txtmuom);
                  $clone.find('[id*="SOMUOMQTY"]').val(txtmuomqty);
                  $clone.find('[id*="SOAUOM"]').val(txtauom);
                  $clone.find('[id*="SOAUOMQTY"]').val(txtauomqty);
                  $clone.find('[id*="popupMUOM"]').val(txtmuom);
                  $clone.find('[id*="MAINUOMID_REF"]').val(txtmuomid);
                $clone.find('[id*="CHALLAN_MAINQTY"]').val('');
                  $clone.find('[id*="SO_FQTY"]').val(txtmuomqty);
                  $clone.find('[id*="popupAUOM"]').val(txtauom);
                  $clone.find('[id*="ALTUOMID_REF"]').val(txtauomid);
                  $clone.find('[id*="ALT_UOMID_QTY"]').val('');
                  $clone.find('[id*="SQID_REF"]').val(txtsqid);
                  $clone.find('[id*="SEQID_REF"]').val(txtseqid);
                  $tr.closest('table').append($clone);   
                  var rowCount = $('#Row_Count1').val();
                  rowCount = parseInt(rowCount)+1;
                  $('#Row_Count1').val(rowCount);
                  if($clone.find('[id*="txtSO_popup"]').val() == '')
                  {
                    $clone.find('[id*="SOMUOM"]').val('');
                    $clone.find('[id*="SOMUOMQTY"]').val('');
                    $clone.find('[id*="SOAUOM"]').val('');
                    $clone.find('[id*="SOAUOMQTY"]').val('');
                    $clone.find('[id*="ALPSPARTNO"]').val('');
                    $clone.find('[id*="Custpartno"]').val('');
                    $clone.find('[id*="OEMpartno"]').val('');
                  }
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
                var txt_id12= $('#hdn_ItemID12').val();
                var txt_id13= $('#hdn_ItemID13').val();
                var txt_id14= $('#hdn_ItemID14').val();
                var txt_id15= $('#hdn_ItemID15').val();
                var txt_id16= $('#hdn_ItemID16').val();
                var txt_id16= $('#hdn_ItemID16').val();
                var txt_id25= $('#hdn_ItemID25').val();
                $('#'+txtid).val(texdesc);
                $('#'+txt_id2).val(txtval);
                $('#'+txt_id3).val(txtname);
                $('#'+txt_id4).val(txtpendingqty);
                $('#'+txt_id5).val(texspec);
                $('#'+txt_id6).val(txtmuom);
                $('#'+txt_id7).val(txtmuomqty);
                $('#'+txt_id8).val(txtauom);
                $('#'+txt_id9).val(txtauomqty);
                $('#'+txt_id10).val(txtmuom);
                $('#'+txt_id11).val(txtmuomid);
                $('#'+txt_id12).val('');
                $('#'+txt_id13).val(txtauom);
                $('#'+txt_id14).val(txtauomid);
                $('#'+txt_id15).val('');
                $('#'+txt_id16).val(txtmuomqty);
                $('#'+txt_id25).val(alpspartno);
                $('#'+txtid).parent().parent().find('[id*="SQID_REF"]').val(txtsqid);
                $('#'+txtid).parent().parent().find('[id*="SEQID_REF"]').val(txtseqid);

               
                $('#'+txtid).parent().parent().find('[id*="Custpartno"]').val(cpartno);
                $('#'+txtid).parent().parent().find('[id*="OEMpartno"]').val(opartno);
                $('#'+txtid).parent().parent().find('[id*="AVG_RATE"]').val(ratepum);

                


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
                $('#hdn_ItemID25').val('');
                if($('#'+txtid).parent().parent().find('[id*="txtSO_popup"]').val() == '')
                {
                  $('#'+txtid).parent().parent().find('[id*="SOMUOM"]').val('');
                  $('#'+txtid).parent().parent().find('[id*="SOMUOMQTY"]').val('');
                  $('#'+txtid).parent().parent().find('[id*="SOAUOM"]').val('');
                  $('#'+txtid).parent().parent().find('[id*="SOAUOMQTY"]').val('');
                  $('#'+txtid).parent().parent().find('[id*="ALPSPARTNO"]').val('');
                  
                  $('#'+txtid).parent().parent().find('[id*="Custpartno"]').val('');
                  $('#'+txtid).parent().parent().find('[id*="OEMpartno"]').val('');
                }
            }                  
      }
    $("#Itemcodesearch").val(''); 
    $("#Itemnamesearch").val(''); 
    $("#ItemUOMsearch").val(''); 
    $("#ItemGroupsearch").val(''); 
    $("#ItemCategorysearch").val(''); 
    $("#ItemStatussearch").val(''); 
    $('.remove').removeAttr('disabled'); 
  });
  $('.js-selectall').prop("checked", false);
  $('#ITEMIDpopup').hide();
  event.preventDefault();
});

$('[id*="chkId"]').change(function(){
  var txtauomqty = 0.000;
  var txtmqtyf = 0.000;
  var fieldid = $(this).parent().parent().attr('id');
  var txtval =   $("#txt"+fieldid+"").val();
  var texdesc =  $("#txt"+fieldid+"").data("desc");
  var fieldid2 = $(this).parent().parent().children('[id*="itemname"]').attr('id');
  var txtname =  $("#txt"+fieldid2+"").val();
  var texspec =  $("#txt"+fieldid2+"").data("desc");
  var fieldid3 = $(this).parent().parent().children('[id*="itemuom"]').attr('id');
  var txtmuomid =  $("#txt"+fieldid3+"").val();
  var txtauom =  $("#txt"+fieldid3+"").data("desc");

  var apartno =  $("#txt"+fieldid3+"").data("desc2");
  var cpartno =  $("#txt"+fieldid3+"").data("desc3");
  var opartno =  $("#txt"+fieldid3+"").data("desc4");
  var ratepum =  $("#txt"+fieldid3+"").data("desc5");

  


  // var txtmuom =  $(this).parent().parent().children('[id*="itemuom"]').text();
  var fieldid4 = $(this).parent().parent().children('[id*="uomqty"]').attr('id');
  var txtauomid =  $("#txt"+fieldid4+"").val();
  txtauomqty =  $("#txt"+fieldid4+"").data("desc");
  var txtmuomqty =  $.trim($(this).parent().parent().children('[id*="uomqty"]').text());
  var fieldid5 = $(this).parent().parent().children('[id*="irate"]').attr('id');
  txtmqtyf = $("#txt"+fieldid5+"").data("desc");
  var txtpendingqty = $("#txt"+fieldid5+"").val();
  var fieldid6 = $(this).parent().parent().children('[id*="itax"]').attr('id');
  var txtsqid = $("#txt"+fieldid6+"").data("desc");
  var txtseqid = $("#txt"+fieldid6+"").val();


  var fieldid7 = $(this).parent().parent().children('[id*="ise"]').attr('id');
  var txtsono = $("#txt"+fieldid7+"").val();
  var txtmuom = $("#txt"+fieldid7+"").data("desc");

  var fieldid8 = $(this).parent().parent().children('[id*="alps"]').attr('id');
   var alpspartno = $("#txt"+fieldid8+"").data("desc");

  
  txtauomqty = (parseFloat(txtmuomqty)/parseFloat(txtmqtyf))*parseFloat(txtauomqty);
  
  
  // var intRegex = /^\d+$/;
  if(intRegex.test(txtauomqty)){
      txtauomqty = (txtauomqty +'.000');
  }

  if(intRegex.test(txtmuomqty)){
    txtmuomqty = (txtmuomqty +'.000');
  }
  
  var SalesOrder2 = [];
  $('#example2').find('.participantRow').each(function(){
    if($(this).find('[id*="ITEMID_REF"]').val() != '')
    {
      var soitem = $(this).find('[id*="txtSO_popup"]').val()+'-'+$(this).find('[id*="SQID_REF"]').val()+'-'+$(this).find('[id*="SEQID_REF"]').val()+'-'+$(this).find('[id*="ITEMID_REF"]').val();
      SalesOrder2.push(soitem);
    }
  });
  
  var salesorder =  $('#hdn_ItemID18').val();
  var itemids =  $('#hdn_ItemID19').val();
  var enquiryids =  $('#hdn_ItemID20').val();
  var quotationids =  $('#hdn_ItemID21').val();

  if($(this).is(":checked") == true) 
  {



    var SCHEME  = [];        
  $('#example2').find('.participantRow').each(function(){
    if($(this).find('[id*="SCHEMEID_REF"]').val() != ''){
      var schemeid  = $(this).find('[id*="SCHEMEID_REF"]').val();
        if(schemeid!=''){
              if(jQuery.inArray(schemeid, SCHEME) == -1){
                SCHEME.push(schemeid);
          }
        }
    }
  }); 

    
    
    var txtorderitem = txtsono+'-'+txtsqid+'-'+txtseqid+'-'+txtval;


    if( SCHEME.length === 0 && jQuery.inArray(txtorderitem, SalesOrder2) !== -1)
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
          $('#hdn_ItemID12').val('');
          $('#hdn_ItemID13').val('');
          $('#hdn_ItemID14').val('');
          $('#hdn_ItemID15').val('');
          $('#hdn_ItemID16').val('');
          $('#hdn_ItemID17').val('');
          $('#hdn_ItemID18').val('');
          $('#hdn_ItemID19').val('');
          $('#hdn_ItemID20').val('');
          $('#hdn_ItemID25').val('');
          txtval = '';
          texdesc = '';
          txtname = '';
          alpspartno = '';
          txtmuom = '';
          alpspartno = '';
          txtauom = '';
          txtmuomid = '';
          txtauomid = '';
          txtauomqty='';
          txtmuomqty='';
          txtsqid = '';
          txtseqid = '';
          txtsono = '';
          $('.js-selectall').prop("checked", false);
          $("#ITEMIDpopup").hide();
          return false;
    }
    /*
    if(salesorder.indexOf(txtsono) != -1 && quotationids.indexOf(txtsqid) != -1 && enquiryids.indexOf(txtseqid) != -1 && itemids.indexOf(txtval) != -1)
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
                  $('#hdn_ItemID12').val('');
                  $('#hdn_ItemID13').val('');
                  $('#hdn_ItemID14').val('');
                  $('#hdn_ItemID15').val('');
                  $('#hdn_ItemID16').val('');
                  $('#hdn_ItemID17').val('');
                  $('#hdn_ItemID18').val('');
                  $('#hdn_ItemID19').val('');
                  $('#hdn_ItemID20').val('');
                  txtval = '';
                  texdesc = '';
                  txtname = '';
                  txtmuom = '';
                  txtauom = '';
                  txtmuomid = '';
                  txtauomid = '';
                  txtauomqty='';
                  txtmuomqty='';
                  txtsqid = '';
                  txtseqid = '';
                  txtsono = '';
                  $('.js-selectall').prop("checked", false);
                  $("#ITEMIDpopup").hide();
                  return false;
    }*/     
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
          var txt_id12= $('#hdn_ItemID12').val();
          var txt_id13= $('#hdn_ItemID13').val();
          var txt_id14= $('#hdn_ItemID14').val();
          var txt_id15= $('#hdn_ItemID15').val();
          var txt_id16= $('#hdn_ItemID16').val();
          var txt_id17= $('#hdn_ItemID17').val();
          var txt_id18= $('#hdn_ItemID18').val();
          var txt_id25= $('#hdn_ItemID25').val();

          var $tr = $('.participantRow').closest('table');
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

              $clone.find('[id*="ALPSPARTNO"]').val(alpspartno);
              $clone.find('[id*="Custpartno"]').val(cpartno);
              $clone.find('[id*="OEMpartno"]').val(opartno);
              $clone.find('[id*="AVG_RATE"]').val(ratepum);



              $clone.find('[id*="SOPENDINGQTY"]').val(txtpendingqty);
              $clone.find('[id*="ITEMSPECI"]').val(texspec);
              $clone.find('[id*="SOMUOM"]').val(txtmuom);
              $clone.find('[id*="SOMUOMQTY"]').val(txtmuomqty);
              $clone.find('[id*="SOAUOM"]').val(txtauom);
              $clone.find('[id*="SOAUOMQTY"]').val(txtauomqty);
              $clone.find('[id*="popupMUOM"]').val(txtmuom);
              $clone.find('[id*="MAINUOMID_REF"]').val(txtmuomid);
              $clone.find('[id*="CHALLAN_MAINQTY"]').val('');
              $clone.find('[id*="SO_FQTY"]').val(txtmuomqty);
              $clone.find('[id*="popupAUOM"]').val(txtauom);
              $clone.find('[id*="ALTUOMID_REF"]').val(txtauomid);
              $clone.find('[id*="ALT_UOMID_QTY"]').val('');
              $clone.find('[id*="SQID_REF"]').val(txtsqid);
              $clone.find('[id*="SEQID_REF"]').val(txtseqid);
              $tr.closest('table').append($clone);   
              var rowCount = $('#Row_Count1').val();
              rowCount = parseInt(rowCount)+1;
              $('#Row_Count1').val(rowCount);
              if($clone.find('[id*="txtSO_popup"]').val() == '')
              {
                $clone.find('[id*="SOMUOM"]').val('');
                $clone.find('[id*="SOMUOMQTY"]').val('');
                $clone.find('[id*="SOAUOM"]').val('');
                $clone.find('[id*="SOAUOMQTY"]').val('');

                $clone.find('[id*="ALPSPARTNO"]').val('');
                $clone.find('[id*="Custpartno"]').val('');
                $clone.find('[id*="OEMpartno"]').val('');

              }
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
            var txt_id12= $('#hdn_ItemID12').val();
            var txt_id13= $('#hdn_ItemID13').val();
            var txt_id14= $('#hdn_ItemID14').val();
            var txt_id15= $('#hdn_ItemID15').val();
            var txt_id16= $('#hdn_ItemID16').val();
            var txt_id17= $('#hdn_ItemID17').val();
            var txt_id18= $('#hdn_ItemID18').val();
            var txt_id25= $('#hdn_ItemID25').val();
            $('#'+txtid).val(texdesc);
            $('#'+txt_id2).val(txtval);
            $('#'+txt_id3).val(txtname);
            $('#'+txt_id4).val(txtpendingqty);
            $('#'+txt_id5).val(texspec);
            $('#'+txt_id6).val(txtmuom);
            $('#'+txt_id7).val(txtmuomqty);
            $('#'+txt_id8).val(txtauom);
            $('#'+txt_id9).val(txtauomqty);
            $('#'+txt_id10).val(txtmuom);
            $('#'+txt_id11).val(txtmuomid);
            $('#'+txt_id12).val('');
            $('#'+txt_id13).val(txtauom);
            $('#'+txt_id14).val(txtauomid);
            $('#'+txt_id15).val('');
            $('#'+txt_id16).val(txtmuomqty);
            $('#'+txt_id25).val(alpspartno);
            $('#'+txtid).parent().parent().find('[id*="SQID_REF"]').val(txtsqid);
            $('#'+txtid).parent().parent().find('[id*="SEQID_REF"]').val(txtseqid);

            $('#'+txtid).parent().parent().find('[id*="Custpartno"]').val(cpartno);
            $('#'+txtid).parent().parent().find('[id*="OEMpartno"]').val(opartno);

            $('#'+txtid).parent().parent().find('[id*="AVG_RATE"]').val(ratepum);

            

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
            $('#hdn_ItemID25').val('');
            if($('#'+txtid).parent().parent().find('[id*="txtSO_popup"]').val() == '')
            {
              $('#'+txtid).parent().parent().find('[id*="SOMUOM"]').val('');
              $('#'+txtid).parent().parent().find('[id*="SOMUOMQTY"]').val('');
              $('#'+txtid).parent().parent().find('[id*="SOAUOM"]').val('');
              $('#'+txtid).parent().parent().find('[id*="SOAUOMQTY"]').val('');
              $('#'+txtid).parent().parent().find('[id*="ALPSPARTNO"]').val('');
              $('#'+txtid).parent().parent().find('[id*="Custpartno"]').val('');
              $('#'+txtid).parent().parent().find('[id*="OEMpartno"]').val('');
            }
        }
        $('.js-selectall').prop("checked", false);
        $("#ITEMIDpopup").hide();
        return false;
  }
 else if($(this).is(":checked") == false) 
 {
  var id = txtval;
        var enqid = txtseqid;
        var sqid = txtsqid;
        var sono = txtsono;
        var r_count = $('#Row_Count1').val();
        $('#example2').find('.participantRow').each(function()
        {
          var itemid = $(this).find('[id*="ITEMID_REF"]').val();
          var enq = $(this).find('[id*="SEQID_REF"]').val();
          var quote = $(this).find('[id*="SQID_REF"]').val();
          var so = $(this).find('[id*="txtSO_popup"]').val();
          if(id == itemid && enqid == enq && sqid == quote && sono == so)
          {
              var rowCount = $('#Row_Count1').val();
              if (rowCount > 1) {
                $(this).closest('.participantRow').remove(); 
                rowCount = parseInt(rowCount)-1;
              $('#Row_Count1').val(rowCount);
              }
              else 
              {
                $(document).find('.remove').prop('disabled', true);  
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
                
          }
        });
        var chkcount = 0;
        $('#ItemIDTable2').find('.clsitemid').each(function()
        { 
          if($(this).find('[id*="chkId"]').is(":checked") == true)
          {
            chkcount = parseInt(chkcount)+1;
          }
        });
        if(chkcount == 0)
        {
          $('#ITEMIDpopup').hide();
        }
        event.preventDefault();
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

//OSO
//OSO

function bindItemEvents_OSO(){

$('#ItemIDTable2').off(); 

$('.js-selectall').change(function(){
  var isChecked = $(this).prop("checked");
  var selector = $(this).data('target');
  $(selector).prop("checked", isChecked);

  $('#ItemIDTable2').find('.clsitemid').each(function(){
    var txtauomqty = 0.000;
    var txtmqtyf = 0.000;
    var fieldid = $(this).attr('id');
    var txtval =   $("#txt"+fieldid+"").val();
    var texdesc =  $("#txt"+fieldid+"").data("desc");
    var fieldid2 = $(this).find('[id*="itemname"]').attr('id');
    var txtname =  $("#txt"+fieldid2+"").val();
    var texspec =  $("#txt"+fieldid2+"").data("desc");
    var fieldid10 = $(this).find('[id*="alps"]').attr('id');
    var alpspartno =  $("#txt"+fieldid10+"").data("desc");
    var fieldid3 = $(this).find('[id*="itemuom"]').attr('id');
    var txtmuomid =  $("#txt"+fieldid3+"").val();
    var txtauom =  $("#txt"+fieldid3+"").data("desc");

    var apartno =  $("#txt"+fieldid3+"").data("desc2");
    var cpartno =  $("#txt"+fieldid3+"").data("desc3");
    var opartno =  $("#txt"+fieldid3+"").data("desc4");
    var ratepum =  $("#txt"+fieldid3+"").data("desc5");
    
    

    var fieldid4 = $(this).find('[id*="uomqty"]').attr('id');
    var txtauomid =  $("#txt"+fieldid4+"").val();
    txtauomqty =  $("#txt"+fieldid4+"").data("desc");
    var txtmuomqty =  $.trim($(this).find('[id*="uomqty"]').text());

    var fieldid5 = $(this).find('[id*="irate"]').attr('id');
    txtmqtyf = $("#txt"+fieldid5+"").data("desc");
    var txtpendingqty = $("#txt"+fieldid5+"").val();
    var fieldid6 = $(this).find('[id*="itax"]').attr('id');
    var txtsqid = $("#txt"+fieldid6+"").data("desc");
    var txtseqid = $("#txt"+fieldid6+"").val();
    var fieldid7 = $(this).find('[id*="ise"]').attr('id');
    var txtsono = $("#txt"+fieldid7+"").val();
    var txtmuom =  $("#txt"+fieldid7+"").data("desc");
    var rcount1 = parseInt($(this).closest('table').find('.clsitemid').length);
    var rcount2 = $('#hdn_ItemID22').val();
    var r_count2 = 0;
    if(rcount2 == '')
    {
      rcount2 = 0;
    }
    
    txtauomqty = (parseFloat(txtmuomqty)/parseFloat(txtmqtyf))*parseFloat(txtauomqty);
    
  // var intRegex = /^\d+$/;
  if(intRegex.test(txtauomqty)){
      txtauomqty = (txtauomqty +'.000');
  }
  if(intRegex.test(txtmuomqty)){
    txtmuomqty = (txtmuomqty +'.000');
  }
  var SalesOrder2 = [];
  $('#example2').find('.participantRow').each(function(){
    if($(this).find('[id*="ITEMID_REF"]').val() != '')
    {
      var soitem = $(this).find('[id*="txtSO_popup"]').val()+'-'+$(this).find('[id*="ITEMID_REF"]').val();
      SalesOrder2.push(soitem);
      r_count2 = parseInt(r_count2) + 1;
    }
  });
  
  var salesorder =  $('#hdn_ItemID18').val();
  var itemids =  $('#hdn_ItemID19').val();
  var enquiryids =  $('#hdn_ItemID20').val();
  var quotationids =  $('#hdn_ItemID21').val();
  //var alpspartno =  $('#hdn_ItemID25').val();


      if($(this).find('[id*="chkId"]').is(":checked") == true) 
      {
        rcount1 = parseInt(rcount2)+parseInt(rcount1);
        if(parseInt(r_count2) >= parseInt(rcount1))
        {
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
              $('#hdn_ItemID21').val('');
              $('#hdn_ItemID25').val('');
              txtval = '';
              texdesc = '';
              txtname = '';
              txtmuom = '';
              txtauom = '';
              txtmuomid = '';
              txtauomid = '';
              txtauomqty='';
              txtmuomqty='';
              txtsqid = '';
              txtseqid = '';
              txtsono = '';
              alpspartno = '';
              $('.js-selectall').prop("checked", false);
              $("#ITEMIDpopup").hide();
              return false;
        }
        var txtorderitem = txtsono+'-'+txtval;
        if(jQuery.inArray(txtorderitem, SalesOrder2) !== -1)
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
              $('#hdn_ItemID12').val('');
              $('#hdn_ItemID13').val('');
              $('#hdn_ItemID14').val('');
              $('#hdn_ItemID15').val('');
              $('#hdn_ItemID16').val('');
              $('#hdn_ItemID17').val('');
              $('#hdn_ItemID18').val('');
              $('#hdn_ItemID19').val('');
              $('#hdn_ItemID19').val('');
              $('#hdn_ItemID25').val('');
              txtval = '';
              texdesc = '';
              txtname = '';
              txtmuom = '';
              txtauom = '';
              txtmuomid = '';
              txtauomid = '';
              txtauomqty='';
              txtmuomqty='';
              txtsqid = '';
              txtseqid = '';
              txtsono = '';
              alpspartno = '';
              $('.js-selectall').prop("checked", false);
              $("#ITEMIDpopup").hide();
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
              var txt_id9= $('#hdn_ItemID9').val();
              var txt_id10= $('#hdn_ItemID10').val();
              var txt_id11= $('#hdn_ItemID11').val();
              var txt_id12= $('#hdn_ItemID12').val();
              var txt_id13= $('#hdn_ItemID13').val();
              var txt_id14= $('#hdn_ItemID14').val();
              var txt_id15= $('#hdn_ItemID15').val();
              var txt_id16= $('#hdn_ItemID16').val();
              var txt_id17= $('#hdn_ItemID17').val();
              var txt_id25= $('#hdn_ItemID25').val();

              var $tr = $('.participantRow').closest('table');
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

                        $clone.find('[id*="ALPSPARTNO"]').val(alpspartno);
                        $clone.find('[id*="Custpartno"]').val(cpartno);
                        $clone.find('[id*="OEMpartno"]').val(opartno);
                        $clone.find('[id*="AVG_RATE"]').val(ratepum);

                        $clone.find('[id*="ITEMID_REF"]').val(txtval);
                        $clone.find('[id*="ItemName"]').val(txtname);
                        $clone.find('[id*="SOPENDINGQTY"]').val(txtpendingqty);
                        $clone.find('[id*="ITEMSPECI"]').val(texspec);
                        $clone.find('[id*="SOMUOM"]').val(txtmuom);
                        $clone.find('[id*="SOMUOMQTY"]').val('');
                        $clone.find('[id*="SOAUOM"]').val(txtauom);
                        $clone.find('[id*="SOAUOMQTY"]').val(txtauomqty);
                        $clone.find('[id*="popupMUOM"]').val(txtmuom);
                        $clone.find('[id*="MAINUOMID_REF"]').val(txtmuomid);
                        $clone.find('[id*="CHALLAN_MAINQTY"]').val('');
                        $clone.find('[id*="SO_FQTY"]').val('');
                        $clone.find('[id*="popupAUOM"]').val(txtauom);
                        $clone.find('[id*="ALTUOMID_REF"]').val(txtauomid);
                        $clone.find('[id*="ALT_UOMID_QTY"]').val(txtauomqty);
                        $clone.find('[id*="SQID_REF"]').val('');
                        $clone.find('[id*="SEQID_REF"]').val('');
                        $tr.closest('table').append($clone);   
                        var rowCount = $('#Row_Count1').val();
                        rowCount = parseInt(rowCount)+1;
                        $('#Row_Count1').val(rowCount);
                        if($clone.find('[id*="txtSO_popup"]').val() == '')
                        {
                          $clone.find('[id*="SOMUOM"]').val('');
                          $clone.find('[id*="SOMUOMQTY"]').val('');
                          $clone.find('[id*="SOAUOM"]').val('');
                          $clone.find('[id*="SOAUOMQTY"]').val('');
                          $clone.find('[id*="ALPSPARTNO"]').val('');
                          $clone.find('[id*="Custpartno"]').val('');
                          $clone.find('[id*="OEMpartno"]').val('');
                        }
     
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
                      var txt_id12= $('#hdn_ItemID12').val();
                      var txt_id13= $('#hdn_ItemID13').val();
                      var txt_id14= $('#hdn_ItemID14').val();
                      var txt_id15= $('#hdn_ItemID15').val();
                      var txt_id16= $('#hdn_ItemID16').val();
                      var txt_id16= $('#hdn_ItemID16').val();
                      var txt_id25= $('#hdn_ItemID25').val();
                      $('#'+txtid).val(texdesc);
                      $('#'+txt_id2).val(txtval);
                      $('#'+txt_id3).val(txtname);
                      $('#'+txt_id4).val(txtpendingqty);
                      $('#'+txt_id5).val(texspec);
                      $('#'+txt_id6).val(txtmuom);
                      $('#'+txt_id7).val('');
                      $('#'+txt_id8).val(txtauom);
                      $('#'+txt_id9).val(txtauomqty);
                      $('#'+txt_id10).val(txtmuom);
                      $('#'+txt_id11).val(txtmuomid);
                      $('#'+txt_id12).val('');
                      $('#'+txt_id13).val(txtauom);
                      $('#'+txt_id14).val(txtauomid);
                      $('#'+txt_id15).val(txtauomqty);
                      $('#'+txt_id16).val('');
                      $('#'+txt_id25).val(alpspartno);
          
                $('#'+txtid).parent().parent().find('[id*="SQID_REF"]').val(txtsqid);
                $('#'+txtid).parent().parent().find('[id*="SEQID_REF"]').val(txtseqid);

                $('#'+txtid).parent().parent().find('[id*="Custpartno"]').val(cpartno);
                $('#'+txtid).parent().parent().find('[id*="OEMpartno"]').val(opartno);
                $('#'+txtid).parent().parent().find('[id*="AVG_RATE"]').val(ratepum);

                

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
                $('#hdn_ItemID25').val('');
                if($('#'+txtid).parent().parent().find('[id*="txtSO_popup"]').val() == '')
                {
                  $('#'+txtid).parent().parent().find('[id*="SOMUOM"]').val('');
                  $('#'+txtid).parent().parent().find('[id*="SOMUOMQTY"]').val('');
                  $('#'+txtid).parent().parent().find('[id*="SOAUOM"]').val('');
                  $('#'+txtid).parent().parent().find('[id*="SOAUOMQTY"]').val('');
                  $('#'+txtid).parent().parent().find('[id*="ALPSPARTNO"]').val('');
                  $('#'+txtid).parent().parent().find('[id*="Custpartno"]').val('');
                  $('#'+txtid).parent().parent().find('[id*="OEMpartno"]').val('');
                }
            }                  
      }
    $("#Itemcodesearch").val(''); 
    $("#Itemnamesearch").val(''); 
    $("#ItemUOMsearch").val(''); 
    $("#ItemGroupsearch").val(''); 
    $("#ItemCategorysearch").val(''); 
    $("#ItemStatussearch").val(''); 
    $('.remove').removeAttr('disabled'); 
  });
  $('.js-selectall').prop("checked", false);
  $('#ITEMIDpopup').hide();
  event.preventDefault();
});

$('[id*="chkId"]').change(function(){
  var txtauomqty = 0.000;
  var txtmqtyf = 0.000;
  var fieldid = $(this).parent().parent().attr('id');
  var txtval =   $("#txt"+fieldid+"").val();
  var texdesc =  $("#txt"+fieldid+"").data("desc");
  
  var fieldid2 = $(this).parent().parent().children('[id*="itemname"]').attr('id');
  var txtname =  $("#txt"+fieldid2+"").val();
  var texspec =  $("#txt"+fieldid2+"").data("desc");
  var fieldid3 = $(this).parent().parent().children('[id*="itemuom"]').attr('id');
  var txtmuomid =  $("#txt"+fieldid3+"").val();
  var txtauom =  $("#txt"+fieldid3+"").data("desc");

  var apartno =  $("#txt"+fieldid3+"").data("desc2");
  var cpartno =  $("#txt"+fieldid3+"").data("desc3");
  var opartno =  $("#txt"+fieldid3+"").data("desc4");
  var ratepum =  $("#txt"+fieldid3+"").data("desc5");

  

  // var txtmuom =  $(this).parent().parent().children('[id*="itemuom"]').text();
  var fieldid4 = $(this).parent().parent().children('[id*="uomqty"]').attr('id');
  var txtauomid =  $("#txt"+fieldid4+"").val();
  txtauomqty =  $("#txt"+fieldid4+"").data("desc");

  var txtmuomqty =  $.trim($(this).parent().parent().children('[id*="uomqty"]').text());
  var fieldid5 = $(this).parent().parent().children('[id*="irate"]').attr('id');
  txtmqtyf = $("#txt"+fieldid5+"").data("desc");
  var txtpendingqty = $("#txt"+fieldid5+"").val();
  var fieldid6 = $(this).parent().parent().children('[id*="itax"]').attr('id');
  var txtsqid = $("#txt"+fieldid6+"").data("desc");
  var txtseqid = $("#txt"+fieldid6+"").val();


  var fieldid7 = $(this).parent().parent().children('[id*="ise"]').attr('id');
  var txtsono = $("#txt"+fieldid7+"").val();
  var txtmuom = $("#txt"+fieldid7+"").data("desc");

  var fieldid8 = $(this).parent().parent().children('[id*="alps"]').attr('id');
   var alpspartno = $("#txt"+fieldid8+"").data("desc");

  
  txtauomqty = (parseFloat(txtmuomqty)/parseFloat(txtmqtyf))*parseFloat(txtauomqty);
  
  
  // var intRegex = /^\d+$/;
  if(intRegex.test(txtauomqty)){
      txtauomqty = (txtauomqty +'.000');
  }

  if(intRegex.test(txtmuomqty)){
    txtmuomqty = (txtmuomqty +'.000');
  }
  
  var SalesOrder2 = [];
  $('#example2').find('.participantRow').each(function(){
    if($(this).find('[id*="ITEMID_REF"]').val() != '')
    {
      var soitem = $(this).find('[id*="txtSO_popup"]').val()+'-'+$(this).find('[id*="ITEMID_REF"]').val();
      SalesOrder2.push(soitem);
    }
  });
  
  var salesorder =  $('#hdn_ItemID18').val();
  var itemids =  $('#hdn_ItemID19').val();
  var enquiryids =  $('#hdn_ItemID20').val();
  var quotationids =  $('#hdn_ItemID21').val();

  if($(this).is(":checked") == true) 
  {
    
    var txtorderitem = txtsono+'-'+txtval;


    if(jQuery.inArray(txtorderitem, SalesOrder2) !== -1)
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
          $('#hdn_ItemID12').val('');
          $('#hdn_ItemID13').val('');
          $('#hdn_ItemID14').val('');
          $('#hdn_ItemID15').val('');
          $('#hdn_ItemID16').val('');
          $('#hdn_ItemID17').val('');
          $('#hdn_ItemID18').val('');
          $('#hdn_ItemID19').val('');
          $('#hdn_ItemID20').val('');
          $('#hdn_ItemID25').val('');
          txtval = '';
          texdesc = '';
          txtname = '';
          alpspartno = '';
          txtmuom = '';
          alpspartno = '';
          txtauom = '';
          txtmuomid = '';
          txtauomid = '';
          txtauomqty='';
          txtmuomqty='';
          txtsqid = '';
          txtseqid = '';
          txtsono = '';
          $('.js-selectall').prop("checked", false);
          $("#ITEMIDpopup").hide();
          return false;
    }
    /*
    if(salesorder.indexOf(txtsono) != -1 && quotationids.indexOf(txtsqid) != -1 && enquiryids.indexOf(txtseqid) != -1 && itemids.indexOf(txtval) != -1)
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
                  $('#hdn_ItemID12').val('');
                  $('#hdn_ItemID13').val('');
                  $('#hdn_ItemID14').val('');
                  $('#hdn_ItemID15').val('');
                  $('#hdn_ItemID16').val('');
                  $('#hdn_ItemID17').val('');
                  $('#hdn_ItemID18').val('');
                  $('#hdn_ItemID19').val('');
                  $('#hdn_ItemID20').val('');
                  txtval = '';
                  texdesc = '';
                  txtname = '';
                  txtmuom = '';
                  txtauom = '';
                  txtmuomid = '';
                  txtauomid = '';
                  txtauomqty='';
                  txtmuomqty='';
                  txtsqid = '';
                  txtseqid = '';
                  txtsono = '';
                  $('.js-selectall').prop("checked", false);
                  $("#ITEMIDpopup").hide();
                  return false;
    }*/     
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
          var txt_id12= $('#hdn_ItemID12').val();
          var txt_id13= $('#hdn_ItemID13').val();
          var txt_id14= $('#hdn_ItemID14').val();
          var txt_id15= $('#hdn_ItemID15').val();
          var txt_id16= $('#hdn_ItemID16').val();
          var txt_id17= $('#hdn_ItemID17').val();
          var txt_id18= $('#hdn_ItemID18').val();
          var txt_id25= $('#hdn_ItemID25').val();

          var $tr = $('.participantRow').closest('table');
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

              $clone.find('[id*="ALPSPARTNO"]').val(alpspartno);
              $clone.find('[id*="Custpartno"]').val(cpartno);
              $clone.find('[id*="OEMpartno"]').val(opartno);
              $clone.find('[id*="AVG_RATE"]').val(ratepum);

              $clone.find('[id*="SOPENDINGQTY"]').val(txtpendingqty);
              $clone.find('[id*="ITEMSPECI"]').val(texspec);
              $clone.find('[id*="SOMUOM"]').val(txtmuom);
              $clone.find('[id*="SOMUOMQTY"]').val(txtauomqty);
              $clone.find('[id*="SOAUOM"]').val(txtauom);
              $clone.find('[id*="SOAUOMQTY"]').val(txtauom);
              $clone.find('[id*="popupMUOM"]').val(txtmuom);
              $clone.find('[id*="MAINUOMID_REF"]').val(txtmuomid);
              $clone.find('[id*="CHALLAN_MAINQTY"]').val('');
              $clone.find('[id*="SO_FQTY"]').val('');
              $clone.find('[id*="popupAUOM"]').val(txtauom);
              $clone.find('[id*="ALTUOMID_REF"]').val(txtauomid);
              $clone.find('[id*="ALT_UOMID_QTY"]').val(txtauomqty);
              $clone.find('[id*="SQID_REF"]').val(txtsqid);
              $clone.find('[id*="SEQID_REF"]').val(txtseqid);
              $tr.closest('table').append($clone);   
              var rowCount = $('#Row_Count1').val();
              rowCount = parseInt(rowCount)+1;
              $('#Row_Count1').val(rowCount);
              if($clone.find('[id*="txtSO_popup"]').val() == '')
              {
                $clone.find('[id*="SOMUOM"]').val('');
                $clone.find('[id*="SOMUOMQTY"]').val('');
                $clone.find('[id*="SOAUOM"]').val('');
                $clone.find('[id*="SOAUOMQTY"]').val('');

                $clone.find('[id*="ALPSPARTNO"]').val('');
                $clone.find('[id*="Custpartno"]').val('');
                $clone.find('[id*="OEMpartno"]').val('');
              }
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
            var txt_id12= $('#hdn_ItemID12').val();
            var txt_id13= $('#hdn_ItemID13').val();
            var txt_id14= $('#hdn_ItemID14').val();
            var txt_id15= $('#hdn_ItemID15').val();
            var txt_id16= $('#hdn_ItemID16').val();
            var txt_id17= $('#hdn_ItemID17').val();
            var txt_id18= $('#hdn_ItemID18').val();
            var txt_id25= $('#hdn_ItemID25').val();
            $('#'+txtid).val(texdesc);
            $('#'+txt_id2).val(txtval);
            $('#'+txt_id3).val(txtname);
            $('#'+txt_id4).val(txtpendingqty);
            $('#'+txt_id5).val(texspec);
            $('#'+txt_id6).val(txtmuom);
            $('#'+txt_id7).val('');
            $('#'+txt_id8).val(txtauom);
            $('#'+txt_id9).val(txtauomqty);
            $('#'+txt_id10).val(txtmuom);
            $('#'+txt_id11).val(txtmuomid);
            $('#'+txt_id12).val('');
            $('#'+txt_id13).val(txtauom);
            $('#'+txt_id14).val(txtauomid);
            $('#'+txt_id15).val(txtauomqty);
            $('#'+txt_id16).val('');
            $('#'+txt_id25).val(alpspartno);
            $('#'+txtid).parent().parent().find('[id*="SQID_REF"]').val(txtsqid);
            $('#'+txtid).parent().parent().find('[id*="SEQID_REF"]').val(txtseqid);

            $('#'+txtid).parent().parent().find('[id*="Custpartno"]').val(cpartno);
            $('#'+txtid).parent().parent().find('[id*="OEMpartno"]').val(opartno);
            $('#'+txtid).parent().parent().find('[id*="AVG_RATE"]').val(ratepum);

            

            //$("#ITEMIDpopup").hide();

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
            $('#hdn_ItemID25').val('');
            if($('#'+txtid).parent().parent().find('[id*="txtSO_popup"]').val() == '')
            {
              $('#'+txtid).parent().parent().find('[id*="SOMUOM"]').val('');
              $('#'+txtid).parent().parent().find('[id*="SOMUOMQTY"]').val('');
              $('#'+txtid).parent().parent().find('[id*="SOAUOM"]').val('');
              $('#'+txtid).parent().parent().find('[id*="SOAUOMQTY"]').val('');

              $('#'+txtid).parent().parent().find('[id*="ALPSPARTNO"]').val('');
              $('#'+txtid).parent().parent().find('[id*="Custpartno"]').val('');
              $('#'+txtid).parent().parent().find('[id*="OEMpartno"]').val('');

            }
        }
        $('.js-selectall').prop("checked", false);
        $("#ITEMIDpopup").hide();
        return false;
  }
 else if($(this).is(":checked") == false) 
 {
  var id = txtval;
        var enqid = txtseqid;
        var sqid = txtsqid;
        var sono = txtsono;
        var r_count = $('#Row_Count1').val();
        $('#example2').find('.participantRow').each(function()
        {
          var itemid = $(this).find('[id*="ITEMID_REF"]').val();
          var enq = $(this).find('[id*="SEQID_REF"]').val();
          var quote = $(this).find('[id*="SQID_REF"]').val();
          var so = $(this).find('[id*="txtSO_popup"]').val();
          if(id == itemid && enqid == enq && sqid == quote && sono == so)
          {
              var rowCount = $('#Row_Count1').val();
              if (rowCount > 1) {
                $(this).closest('.participantRow').remove(); 
                rowCount = parseInt(rowCount)-1;
              $('#Row_Count1').val(rowCount);
              }
              else 
              {
                $(document).find('.remove').prop('disabled', true);  
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
                
          }
        });
        var chkcount = 0;
        $('#ItemIDTable2').find('.clsitemid').each(function()
        { 
          if($(this).find('[id*="chkId"]').is(":checked") == true)
          {
            chkcount = parseInt(chkcount)+1;
          }
        });
        if(chkcount == 0)
        {
          $('#ITEMIDpopup').hide();
        }
        event.preventDefault();
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

      function altuomNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("altuomnamesearch");
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

      

  $('#Material').on('click','[id*="popupAUOM"]',function(event){
        var ItemID = $(this).parent().parent().find('[id*="ITEMID_REF"]').val();
        
        if(ItemID !=''){
                $("#tbody_altuom").html('');
                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  });
                  $.ajax({
                      url:'{{route("transaction",[43,"getAltUOM"])}}',
                      type:'POST',
                      data:{'id':ItemID},
                      success:function(data) {
                        
                        $("#tbody_altuom").html(data);   
                        bindAltUOM();                     
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
        var id2 = $(this).parent().parent().find('[id*="ALTUOMID_REF"]').attr('id');
        var id3 = $(this).parent().parent().find('[id*="CHALLAN_MAINQTY"]').attr('id');
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
        var mqty = $('#'+txtid).parent().parent().find('[id*="CHALLAN_MAINQTY"]').val();

        if(altuomid!=''){
              $('#'+txt_id4).val('');
                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  });
                  $.ajax({
                      url:'{{route("transaction",[43,"getaltuomqty"])}}',
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
    $('#hdnMaterial').val(Material);
    
    var scudf = <?php echo json_encode($objSCUDF); ?>;
    var udfforsc = <?php echo json_encode($objUdfSCData2); ?>;
    
    $.each( scudf, function( scukey, scuvalue ) {
        $.each( udfforsc, function( usckey, uscvalue ) { 
            if(scuvalue.SC_UDFID_REF == uscvalue.UDF_SCID)
            {
                $('#popupUDFSCID_'+scukey).val(uscvalue.LABEL);
            }
        
            if(scuvalue.SC_UDFID_REF == uscvalue.UDF_SCID)
            {        
                    var txtvaltype2 =   uscvalue.VALUETYPE;
                    var txt_id41 = $('#udfinputid_'+scukey).attr('id');
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
                      if(scuvalue.VALUE == "on")
                      {
                        strinp2 = '<input {{$InputStatus}} type="checkbox" name="'+dynamicid2+ '" id="'+dynamicid2+'" class="" checked> ';
                      }
                      else{
                        strinp2 = '<input {{$InputStatus}} type="checkbox" name="'+dynamicid2+ '" id="'+dynamicid2+'" class="" > ';
                      }
                    }
                    else if(chkvaltype2=='combobox'){
                    var txtoptscombo2 =   uscvalue.DESCRIPTIONS;
                    var strarray2 = txtoptscombo2.split(',');
                    var opts2 = '';
                    for (var i = 0; i < strarray2.length; i++) {
                        opts2 = opts2 + '<option value="'+strarray2[i]+'">'+strarray2[i]+'</option> ';
                    }
                    strinp2 = '<select {{$InputStatus}} name="'+dynamicid2+ '" id="'+dynamicid2+'" class="form-control" required>'+opts2+'</select>' ;                   
                    }
                    $('#'+txt_id41).html('');  
                    $('#'+txt_id41).html(strinp2);   //set dynamic input
                    $('#'+dynamicid2).val(scuvalue.VALUE);
                    $('#UDFismandatory_'+scukey).val(uscvalue.ISMANDATORY); // mandatory
            }
        });
    });
    var count1 = <?php echo json_encode($objCount1); ?>;
    var count3 = <?php echo json_encode($objCountUDF); ?>;
    $("#Row_Count1").val(count1);
    $("#Row_Count2").val(count3);
    

    $(function() { $('[id*="SCNO"]').focus(); }); 
    var d = new Date(); 
    var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
    d.setDate(d.getDate() + 29);
    var todate = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
    //$('#SCDT').val(today);
    
    
    
    $('#Material').on('focusout',"[id*='ALT_UOMID_QTY']",function()
    {
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.000')
      }
      event.preventDefault();
    });

    // $('#Material').on('focusout',"[id*='CHALLAN_MAINQTY']",function()
    // {
    //     var itemid = $(this).parent().parent().find('[id*="ITEMID_REF"]').val();
    //     var mqty = $(this).val();
    //     var pendingqty = $(this).parent().parent().find('[id*="SOPENDINGQTY"]').val();
    //     if(mqty > pendingqty)
    //     {
    //       $("#FocusId").val($(this));
    //       $(this).val('');
    //       $("#ProceedBtn").focus();
    //       $("#YesBtn").hide();
    //       $("#NoBtn").hide();
    //       $("#OkBtn1").show();
    //       $("#AlertMessage").text('Challan Quantity cannot be greater than Pending Quantity.');
    //       $("#alert").modal('show');
    //       $("#OkBtn1").focus();
    //       highlighFocusBtn('activeOk1');
    //       return false;
    //     }
    //     var altuomid = $(this).parent().parent().find('[id*="ALTUOMID_REF"]').val();
    //     var txtid = $(this).parent().parent().find('[id*="ALT_UOMID_QTY"]').attr('id');
    //     if(altuomid!=''){
              
    //               $.ajaxSetup({
    //                   headers: {
    //                       'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //                   }
    //               });
    //               $.ajax({
    //                   url:'{{route("transaction",[43,"getaltuomqty"])}}',
    //                   type:'POST',
    //                   data:{'id':altuomid, 'itemid':itemid, 'mqty':mqty},
    //                   success:function(data) {
    //                     if(intRegex.test(data)){
    //                         data = (data +'.000');
    //                     }
    //                     $("#"+txtid).val(data);                        
    //                   },
    //                   error:function(data){
    //                     console.log("Error: Something went wrong.");
    //                     $("#"+txtid).val('');                        
    //                   },
    //               }); 
                      
    //           }
      
    //   if(intRegex.test($(this).val())){
    //     $(this).val($(this).val()+'.000');
    //   }
      
    //   event.preventDefault();
    // });
    

    $('#btnAdd').on('click', function() {
        var viewURL = '{{route("transaction",[43,"add"])}}';
                  window.location.href=viewURL;
    });
    $('#btnExit').on('click', function() {
      var viewURL = '{{route('home')}}';
                  window.location.href=viewURL;
    });
    

//SQ Date Check

var lastdt = <?php echo json_encode($objlastSCDT[0]->SCDT); ?>;
  var sc = <?php echo json_encode($objSC); ?>;
  var today = new Date(); 
  var sodate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2);
  if(lastdt < sc.SCDT)
  {
	$('#SCDT').attr('min',lastdt);
  }
  else
  {
	  $('#SCDT').attr('min',sc.SCDT);
  }
  $('#SCDT').attr('max',sodate);

//SC Date Check
        
    
//delete row

    $("#example2").on('click', '.remove', function() {
      
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
        $("#example2").on('click', '.add', function() {

          

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
        $clone.find('input:date').val(today);
        $tr.closest('table').append($clone);         
        var rowCount1 = $('#Row_Count1').val();
		    rowCount1 = parseInt(rowCount1)+1;
        $('#Row_Count1').val(rowCount1);
        $clone.find('.remove').removeAttr('disabled'); 
        event.preventDefault();
    });

     


   

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
        $clone.find('[id*="SOID_REF"]').val('');
        $clone.find('[id*="SQID_REF"]').val('');
        $clone.find('[id*="SEQID_REF"]').val('');
        $clone.find('[id*="MAINUOMID_REF"]').val('');
        $clone.find('[id*="SO_FQTY"]').val('');
        $clone.find('[id*="ALTUOMID_REF"]').val('');
        $clone.find('[id*="STORE"]').val('');
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
              event.preventDefault();
        }
      
    });
</script>

@endpush

@push('bottom-scripts')
<script>

$(document).ready(function() {

  // $("#btnSaveSC").on("submit", function( event ) {
  //   if ($("#frm_trn_sc").valid()) {
  //       // Do something
  //       alert( "Handler for .submit() called." );
  //       event.preventDefault();
  //   }
  // });


    $('#frm_trn_sc1').bootstrapValidator({
       
        fields: {
            txtlabel: {
                validators: {
                    notEmpty: {
                        message: 'The SQ NO is required'
                    }
                }
            },            
        },
        submitHandler: function(validator, form, submitButton) {
            alert( "Handler for .submit() called." );
             event.preventDefault();
             $("#frm_trn_sc").submit();
        }
    });
});
$( "#btnSaveSC" ).click(function() {
  var formSalesChallan = $("#frm_trn_sc");
  if(formSalesChallan.valid()){
  $("#FocusId").val('');
  var SCNO           =   $.trim($("#SCNO").val());
  var SCDT           =   $.trim($("#SCDT").val());
  var SLID_REF       =   $.trim($("#SLID_REF").val());

  var TYPE=$('#TYPE :selected').val();

  if(SCNO ===""){
     $("#FocusId").val($("#SCNO"));
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please enter value in SCNO.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if(SCDT ===""){
     $("#FocusId").val($("#SCDT"));
     $("#SCDT").val(today);  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please select SQ Date.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 } 
 else if(SLID_REF ===""){
     $("#FocusId").val($("#SLID_REF"));
     $("#SLID_REF").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please select Customer.');
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
    var allblank70 = [];
    var allblank71 = [];

        // $('#udfforsebody').find('.form-control').each(function () {
        $('#example2').find('.participantRow').each(function(){
            if($.trim($(this).find("[id*=ITEMID_REF]").val())!="")
            {
                allblank.push('true');
                    if($.trim($(this).find("[id*=popupMUOM]").val())!=""){
                        allblank2.push('true');
                          if($.trim($(this).find('[id*="CHALLAN_MAINQTY"]').val()) != "" && $.trim($(this).find('[id*="CHALLAN_MAINQTY"]').val()) > "0.000")
                          {
                            allblank3.push('true');
                            if($.trim($(this).find('[id*="STORE"]').val()) != "")
                            {
                              allblank7.push('true');
                            }
                            else
                            {
                              allblank7.push('false');
                            }
                          }
                          else
                          {
                            allblank3.push('false');
                          }  
                    }
                    else{
                        allblank2.push('false');
                    }
					
				

                    if($.trim($(this).find('[id*="CHALLAN_MAINQTY"]').val()) != "" && parseFloat($.trim($(this).find('[id*="CHALLAN_MAINQTY"]').val())) == parseFloat($.trim($(this).find('[id*="TotalHiddenQty"]').val())) ){
                      allblank70.push('true');
                    }
                    else{
                      allblank70.push('false');
                    } 

                    if($.trim($(this).find('[id*="CHALLAN_MAINQTY"]').val()) != "" && parseFloat($.trim($(this).find('[id*="SOPENDINGQTY"]').val())) >= parseFloat($.trim($(this).find('[id*="CHALLAN_MAINQTY"]').val())) ){
                      allblank71.push('true');
                    }
                    else{
                      allblank71.push('false');
                    } 


            }
            else
            {
                allblank.push('false');
            }
            if($.trim($(this).find("[id*=SOID_REF]").val())!="")
            {
                allblank4.push('true');
            }
            else
            {
                allblank4.push('false');
            }

        });
        
        $('#example4').find('.participantRow4').each(function(){
              if($.trim($(this).find("[id*=SC_UDFID_REF]").val())!="")
                {
                    allblank5.push('true');
                        if($.trim($(this).find("[id*=UDFismandatory]").val())=="1"){
                              if($(this).find('[id*="udfvalue"]').attr('type') == "checkbox")
                              {
                                if($.trim($(this).find('[id*="udfvalue"]').is(':checked')) != "false")
                                {
                                  allblank6.push('true');
                                }
                                else
                                {
                                  allblank6.push('false');
                                }
                              }
                              else
                              {
                                if($.trim($(this).find('[id*="udfvalue"]').val()) != "")
                                {
                                  allblank6.push('true');
                                }
                                else
                                {
                                  allblank6.push('false');
                                }
                              }
                        }  
                }                
        });
             
            if(jQuery.inArray("false", allblank4) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Please select Sales Order Number in Material Tab.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
            }
            if(jQuery.inArray("false", allblank4) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Please select Sales Order Number in Material Tab.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
            }
            else if(jQuery.inArray("false", allblank) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Please select item in Material Tab.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
            }
            else if(jQuery.inArray("false", allblank2) !== -1){
            $("#alert").modal('show');
            $("#AlertMessage").text('Main UOM under Sales Challan section is missing in Material Tab.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk');
            }
            else if(jQuery.inArray("false", allblank3) !== -1 && TYPE=='SO'){
            $("#alert").modal('show');
            $("#AlertMessage").text('Main UOM Quantity under Sales Challan section is missing in Material Tab.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk');
            }
            else if(jQuery.inArray("false", allblank7) !== -1){
            $("#alert").modal('show');
            $("#AlertMessage").text('Store details is missing in Material Tab.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk');
            }
            // else if(jQuery.inArray("false", allblank70) !== -1){
            //   $("#alert").modal('show');
            //   $("#AlertMessage").text('Sales Challan quantity should be equal of store dispatch quantity in material tab.');
            //   $("#YesBtn").hide(); 
            //   $("#NoBtn").hide();  
            //   $("#OkBtn1").show();
            //   $("#OkBtn1").focus();
            //   highlighFocusBtn('activeOk');
            // }
            else if(jQuery.inArray("false", allblank71) !== -1 && TYPE=='SO'){
              $("#alert").modal('show');
              $("#AlertMessage").text('Sales Challan quantity should be less or equal of pending quantity in material tab.');
              $("#YesBtn").hide(); 
              $("#NoBtn").hide();  
              $("#OkBtn1").show();
              $("#OkBtn1").focus();
              highlighFocusBtn('activeOk');
            }
            else if(jQuery.inArray("false", allblank6) !== -1){
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
  var formSalesChallan = $("#frm_trn_sc");
  if(formSalesChallan.valid()){
  $("#FocusId").val('');
  var SCNO           =   $.trim($("#SCNO").val());
  var SCDT           =   $.trim($("#SCDT").val());
  var SLID_REF       =   $.trim($("#SLID_REF").val());

  var TYPE=$('#TYPE :selected').val();

  if(SCNO ===""){
     $("#FocusId").val($("#SCNO"));
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please enter value in SCNO.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if(SCDT ===""){
     $("#FocusId").val($("#SCDT"));
     $("#SCDT").val(today);  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please select SQ Date.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 } 
 else if(SLID_REF ===""){
     $("#FocusId").val($("#SLID_REF"));
     $("#SLID_REF").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please select Customer.');
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
    var allblank70 = [];
    var allblank71 = [];

        // $('#udfforsebody').find('.form-control').each(function () {
        $('#example2').find('.participantRow').each(function(){
            if($.trim($(this).find("[id*=ITEMID_REF]").val())!="")
            {
                allblank.push('true');
                    if($.trim($(this).find("[id*=popupMUOM]").val())!=""){
                        allblank2.push('true');
                          if($.trim($(this).find('[id*="CHALLAN_MAINQTY"]').val()) != "" && $.trim($(this).find('[id*="CHALLAN_MAINQTY"]').val()) > "0.000")
                          {
                            allblank3.push('true');
                            if($.trim($(this).find('[id*="STORE"]').val()) != "")
                            {
                              allblank7.push('true');
                            }
                            else
                            {
                              allblank7.push('false');
                            }
                          }
                          else
                          {
                            allblank3.push('false');
                          }  
                    }
                    else{
                        allblank2.push('false');
                    }
					
				

                    if($.trim($(this).find('[id*="CHALLAN_MAINQTY"]').val()) != "" && parseFloat($.trim($(this).find('[id*="CHALLAN_MAINQTY"]').val())) == parseFloat($.trim($(this).find('[id*="TotalHiddenQty"]').val())) ){
                      allblank70.push('true');
                    }
                    else{
                      allblank70.push('false');
                    } 

                    if($.trim($(this).find('[id*="CHALLAN_MAINQTY"]').val()) != "" && parseFloat($.trim($(this).find('[id*="SOPENDINGQTY"]').val())) >= parseFloat($.trim($(this).find('[id*="CHALLAN_MAINQTY"]').val())) ){
                      allblank71.push('true');
                    }
                    else{
                      allblank71.push('false');
                    } 


            }
            else
            {
                allblank.push('false');
            }
            if($.trim($(this).find("[id*=SOID_REF]").val())!="")
            {
                allblank4.push('true');
            }
            else
            {
                allblank4.push('false');
            }

        });
        
        $('#example4').find('.participantRow4').each(function(){
              if($.trim($(this).find("[id*=SC_UDFID_REF]").val())!="")
                {
                    allblank5.push('true');
                        if($.trim($(this).find("[id*=UDFismandatory]").val())=="1"){
                              if($(this).find('[id*="udfvalue"]').attr('type') == "checkbox")
                              {
                                if($.trim($(this).find('[id*="udfvalue"]').is(':checked')) != "false")
                                {
                                  allblank6.push('true');
                                }
                                else
                                {
                                  allblank6.push('false');
                                }
                              }
                              else
                              {
                                if($.trim($(this).find('[id*="udfvalue"]').val()) != "")
                                {
                                  allblank6.push('true');
                                }
                                else
                                {
                                  allblank6.push('false');
                                }
                              }
                        }  
                }                
        });
             
            if(jQuery.inArray("false", allblank4) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Please select Sales Order Number in Material Tab.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
            }
            if(jQuery.inArray("false", allblank4) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Please select Sales Order Number in Material Tab.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
            }
            else if(jQuery.inArray("false", allblank) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Please select item in Material Tab.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
            }
            else if(jQuery.inArray("false", allblank2) !== -1){
            $("#alert").modal('show');
            $("#AlertMessage").text('Main UOM under Sales Challan section is missing in Material Tab.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk');
            }
            else if(jQuery.inArray("false", allblank3) !== -1 && TYPE=='SO'){
            $("#alert").modal('show');
            $("#AlertMessage").text('Main UOM Quantity under Sales Challan section is missing in Material Tab.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk');
            }
            else if(jQuery.inArray("false", allblank7) !== -1){
            $("#alert").modal('show');
            $("#AlertMessage").text('Store details is missing in Material Tab.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk');
            }
            // else if(jQuery.inArray("false", allblank70) !== -1){
            //   $("#alert").modal('show');
            //   $("#AlertMessage").text('Sales Challan quantity should be equal of store dispatch quantity in material tab.');
            //   $("#YesBtn").hide(); 
            //   $("#NoBtn").hide();  
            //   $("#OkBtn1").show();
            //   $("#OkBtn1").focus();
            //   highlighFocusBtn('activeOk');
            // }
            else if(jQuery.inArray("false", allblank71) !== -1 && TYPE=='SO'){
              $("#alert").modal('show');
              $("#AlertMessage").text('Sales Challan quantity should be less or equal of pending quantity in material tab.');
              $("#YesBtn").hide(); 
              $("#NoBtn").hide();  
              $("#OkBtn1").show();
              $("#OkBtn1").focus();
              highlighFocusBtn('activeOk');
            }
            else if(jQuery.inArray("false", allblank6) !== -1){
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
     var trnscForm = $("#frm_trn_sc");
    var formData = trnscForm.serialize();
  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });
$("#btnSaveSC").hide(); 
$(".buttonload").show(); 
$("#btnApprove").prop("disabled", true);
  $.ajax({
      url:'{{ route("transactionmodify",[43,"update"])}}',
      type:'POST',
      data:formData,
      success:function(data) {
        $(".buttonload").hide(); 
        $("#btnSaveSC").show();   
        $("#btnApprove").prop("disabled", false);
          if(data.errors) {
              $(".text-danger").hide();
              if(data.errors.SCNO){
                  showError('ERROR_SCNO',data.errors.SCNO);
                          $("#YesBtn").hide();
                          $("#NoBtn").hide();
                          $("#OkBtn1").show();
                          $("#AlertMessage").text('Please enter correct value in SCNO.');
                          $("#alert").modal('show');
                          $("#OkBtn1").focus();
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
        $("#btnSaveSC").show();   
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
     var trnscForm = $("#frm_trn_sc");
    var formData = trnscForm.serialize();
  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });
$("#btnApprove").hide(); 
$(".buttonload_approve").show();  
$("#btnSaveSC").prop("disabled", true);
  $.ajax({
      url:'{{ route("transactionmodify",[43,"Approve"])}}',
      type:'POST',
      data:formData,
      success:function(data) {
        $("#btnApprove").show();  
        $(".buttonload_approve").hide();  
        $("#btnSaveSC").prop("disabled", false);
          if(data.errors) {
              $(".text-danger").hide();
              if(data.errors.SCNO){
                  showError('ERROR_SCNO',data.errors.SCNO);
                          $("#YesBtn").hide();
                          $("#NoBtn").hide();
                          $("#OkBtn1").show();
                          $("#AlertMessage").text('Please enter correct value in SCNO.');
                          $("#alert").modal('show');
                          $("#OkBtn1").focus();
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
        $("#btnSaveSC").prop("disabled", false);
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
    $("#SCNO").focus();
});

//ok button
$("#OkBtn").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn").hide();
    $(".text-danger").hide();
    window.location.href = '{{route("transaction",[43,"index"]) }}';
});

$("#OkBtn1").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn").hide();
    $("#OkBtn1").hide();
    $("#"+$(this).data('focusname')).focus();
    //$("#SCNO").focus();
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

    function isNumberDecimalKey(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
    return false;

    return true;
}


$('#TYPE').on('change',function()
{
  
    // var MaterialClone = $('#hdnMaterial').val(); 
    
    // $('#Material').html(MaterialClone);


    // $('#Row_Count2').val('1');
    // $('#Row_Count1').val('1');
    // $('#example2').find('.participantRow').each(function(){
    //   $(this).find('input:text').val('');
    //   $(this).find('input:hidden').val('');
    //   var rowcount = $('#Row_Count1').val();
    //   if(rowcount > 1)
    //   {
    //     $(this).closest('.participantRow').remove();
    //     rowcount = parseInt(rowcount) - 1;
    //     $('#Row_Count1').val(rowcount);
    //   }
    // });
    

    resetTab();
 
});

function getStoreRateAvg(ITEMID_REF,rowid){

  $.ajaxSetup({
    headers:{
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  })

  var posts = $.ajax({
                url:'{{route("transaction",[$FormId,"getStoreRateAvg"])}}',
                type:'POST',
                async: false,
                dataType: 'json',
                data: {ITEMID_REF:ITEMID_REF},
                done: function(response) {return response;}
              }).responseText;

  $("#AVG_RATE_"+rowid).val(posts);
}

function resetTab(){
  $('#Material').find('.participantRow').each(function(){
    var rowcount = $(this).closest('table').find('.participantRow').length;
    $(this).find('input:text').val('');
    $(this).find('input:hidden').val('');
    $(this).find('input:checkbox').prop('checked', false);

    if(rowcount > 1){
      $(this).closest('.participantRow').remove();
      rowcount = parseInt(rowcount) - 1;
      $('#Row_Count1').val(rowcount);
    }
  });
}


function getTotalRowValue(){

var strSOTCK        = 0;
var strDISPATCH_MAIN_QTY = 0;
var strRATE      = 0; 
var DISPATCH_ALT_QTY  = 0;


$('#storepopup').find('.clsstrid').each(function(){
  strSOTCK  = $(this).find('[id*="strSOTCK"]').val() > 0? strSOTCK+parseFloat($(this).find('[id*="strSOTCK"]').val()):strSOTCK;
 
  strDISPATCH_MAIN_QTY = $(this).find('[id*="strDISPATCH_MAIN_QTY"]').val() > 0?strDISPATCH_MAIN_QTY+parseFloat($(this).find('[id*="strDISPATCH_MAIN_QTY"]').val()):strDISPATCH_MAIN_QTY;
  strRATE      = $(this).find('[id*="strRATE"]').val() > 0?strRATE+parseFloat($(this).find('[id*="strRATE"]').val()):strRATE;
  DISPATCH_ALT_QTY  = $(this).find('[id*="DISPATCH_ALT_QTY"]').val() > 0?DISPATCH_ALT_QTY+parseFloat($(this).find('[id*="DISPATCH_ALT_QTY"]').val()):DISPATCH_ALT_QTY;
  
});



strSOTCK          = strSOTCK > 0?parseFloat(strSOTCK).toFixed(3):'';
strDISPATCH_MAIN_QTY   = strDISPATCH_MAIN_QTY > 0?parseFloat(strDISPATCH_MAIN_QTY).toFixed(3):'';
strRATE        = strRATE > 0?parseFloat(strRATE).toFixed(5):'';
DISPATCH_ALT_QTY    = DISPATCH_ALT_QTY > 0?parseFloat(DISPATCH_ALT_QTY).toFixed(2):'';


$("#strSOTCK_total").text(strSOTCK);
$("#strDISPATCH_MAIN_QTY_total").text(strDISPATCH_MAIN_QTY);
$("#strRATE_total").text(strRATE);
$("#DISPATCH_ALT_QTY_total").text(DISPATCH_ALT_QTY);

}
</script>


@endpush