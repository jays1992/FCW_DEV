
@extends('layouts.app')
@section('content')
<!-- <form id="frm_trn_ro" onsubmit="return validateForm()"  method="POST" class="needs-validation"  >     -->



    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('transaction',[315,'index'])}}" class="btn singlebt">Release Order</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
                        <button class="btn topnavbt" id="btnSaveSO" ><i class="fa fa-floppy-o"></i> Save</button>
                        <button style="display:none" class="btn topnavbt buttonload"> <i class="fa fa-refresh fa-spin"></i> {{Session::get('save')}}</button>
                        <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt" id="btnUndo"  ><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                        <button style="display:none" class="btn topnavbt buttonload_approve" > <i class="fa fa-refresh fa-spin"></i> {{Session::get('approve')}}</button>
                        <button class="btn topnavbt" id="btnApprove" {{ (isset($objRights->APPROVAL1) || isset($objRights->APPROVAL2) || isset($objRights->APPROVAL3) || isset($objRights->APPROVAL4) || isset($objRights->APPROVAL5)) &&  ($objRights->APPROVAL1||$objRights->APPROVAL2||$objRights->APPROVAL3||$objRights->APPROVAL4||$objRights->APPROVAL5) == 1 ? '' : 'disabled'}} ><i class="fa fa-thumbs-o-up"></i> Approved</button>
                        <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                        <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                </div>
            </div>
    </div><!--topnav-->	
    <!-- multiple table-responsive table-wrapper-scroll-y my-custom-scrollbar -->
<form id="frm_trn_ro" onsubmit="return validateForm()"  method="POST" class="needs-validation"  >
    <div class="container-fluid purchase-order-view">
        
            @csrf
            <div class="container-fluid filter">

              <div class="inner-form">
                    
                <div class="row">
                    <div class="col-lg-2 pl"><p>Release Order No</p></div>
                    <div class="col-lg-2 pl">
						<input type="hidden" name="ROID" id="ROID" value="{{ isset($objRO->ROID)?$objRO->ROID:0 }}" />
                        <input {{$InputStatus}} type="text" name="RONO" id="RONO" value="{{ isset($objRO->RONO)?$objRO->RONO:'' }}" class="form-control mandatory" maxlength="150" autocomplete="off" style="text-transform:uppercase" autofocus  >
                    </div>
                    
                    <div class="col-lg-2 pl"><p>Release Order Date</p></div>
                    <div class="col-lg-2 pl">
                        <input {{$InputStatus}} type="date" name="RODT" id="RODT" onchange="checkPeriodClosing(315,this.value,1)"  value="{{ isset($objRO->RODT)?$objRO->RODT:'' }}" class="form-control mandatory" autocomplete="off" placeholder="dd/mm/yyyy" >
                    </div>
                    
                    <div class="col-lg-2 pl"><p>Customer</p></div>
                    <div class="col-lg-2 pl">
                        <input {{$InputStatus}} type="text" name="SubGl_popup" id="txtsubgl_popup" class="form-control mandatory" value="{{ isset($objsubglcode->SGLCODE)?$objsubglcode->SGLCODE:'' }} {{ isset($objsubglcode->SLNAME)?$objsubglcode->SLNAME:'' }}"  autocomplete="off" readonly/>
                        <input type="hidden" name="SLID_REF" id="SLID_REF" class="form-control" autocomplete="off"  value="{{ isset($objRO->SLID_REF)?$objRO->SLID_REF:'' }}"/>
                        <input type="hidden" name="hdnmaterial" id="hdnmaterial" class="form-control" autocomplete="off" />
                        <input type="hidden" name="hdnTC" id="hdnTC" class="form-control" autocomplete="off" /> 
                        <input type="hidden" name="hdnISP" id="hdnISP" class="form-control" autocomplete="off" />  
                        <input type="hidden" name="hdnISP2" id="hdnISP2" class="form-control" autocomplete="off" />                                                              
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-2 pl"><p>Sales Order No</p></div>
                    <div class="col-lg-2 pl">
                        <input {{$InputStatus}} type="text" name="SONO_txt" id="SONO_txt" class="form-control mandatory" value="{{ isset($objsocode->SONO)?$objsocode->SONO:'' }}" autocomplete="off" style="text-transform:uppercase" readonly/>
                        <input type="hidden" name="SOID_REF" id="SOID_REF" class="form-control mandatory" autocomplete="off"  value="{{ isset($objRO->SOID_REF)?$objRO->SOID_REF:'' }}" />
                    </div>  
                    <div class="col-lg-2 pl"><p>Order Validity From </p></div>
                    <div class="col-lg-2 pl">
                        <input {{$InputStatus}} type="date" name="OVFRM_DT" id="OVFRM_DT" class="form-control mandatory" autocomplete="off" value="{{ isset($objRO->OVFRM_DT)?$objRO->OVFRM_DT:'' }}"  placeholder="dd/mm/yyyy" readonly>
                    </div>                            
                    <div class="col-lg-2 pl"><p>Order Validity To </p></div>
                    <div class="col-lg-2 pl">
                        <input {{$InputStatus}} type="date" name="OVTOM_DT" id="OVTOM_DT" class="form-control mandatory" autocomplete="off" value="{{ isset($objRO->OVTOM_DT)?$objRO->OVTOM_DT:'' }}"  placeholder="dd/mm/yyyy" readonly>
                    </div> 
                </div>  
                
                <div class="row">
                    <div class="col-lg-2 pl"><p>Customer PO No</p></div>
                    <div class="col-lg-2 pl">
                        <input {{$InputStatus}} type="text" name="CUSTOMERPONO" id="CUSTOMERPONO" class="form-control" autocomplete="off" value="{{ isset($objRO->CUSTOMERPONO)?$objRO->CUSTOMERPONO:'' }}" style="text-transform:uppercase" readonly>
                    </div>
                    <div class="col-lg-2 pl"><p>Customer PO Date </p></div>
                    <div class="col-lg-2 pl">
                        <input {{$InputStatus}} type="date" name="CUSTOMERDT" id="CUSTOMERDT" class="form-control " autocomplete="off" value="{{ isset($objRO->CUSTOMERDT)?$objRO->CUSTOMERDT:'' }}" placeholder="dd/mm/yyyy" readonly />
                    </div>
                    <div class="col-lg-2 pl"><p>Foreign Currency</p></div>
                    <div class="col-lg-2 pl">
                        <input {{$InputStatus}} type="checkbox" name="FC" id="FC" class="form-checkbox" {{isset($objRO->FC) && $objRO->FC  == 1 ? 'checked' : ''}}  disabled/>
                        <input type="hidden" name="hdn_FC" id="hdn_FC" class="form-control mandatory" value="{{ isset($objRO->FC)?$objRO->FC:'' }}" autocomplete="off"  />
                    </div> 
                </div>
                
                <div class="row">
                    <div class="col-lg-2 pl"><p>Currency</p></div>
                    <div class="col-lg-2 pl" id="divcurrency" >
                        @if ($objrocurrency)
                        <input {{$InputStatus}} type="text" name="CRID_popup" id="txtCRID_popup" class="form-control"  autocomplete="off" value="{{ isset($objrocurrency[0])?$objrocurrency[0]:'' }}"   disabled/>
                        @else
                        <input {{$InputStatus}} type="text" name="CRID_popup" id="txtCRID_popup" class="form-control"  autocomplete="off"   disabled/>
                        @endif
                        <input type="hidden" name="CRID_REF" id="CRID_REF" class="form-control" autocomplete="off"   value="{{ isset($objRO->CRID_REF)?$objRO->CRID_REF:'' }}" disabled/>
                    </div>
                    <div class="col-lg-2 pl"><p>Conversion Factor</p></div>
                    <div class="col-lg-2 pl">
                        <input {{$InputStatus}} type="text" name="CONVERSION_FACT" id="CONVERSION_FACT" class="form-control" maxlength="100" autocomplete="off" value="{{ isset($objRO->CONVERSION_FACT)?$objRO->CONVERSION_FACT:'' }}" disabled />
                    </div>
                    <div class="col-lg-2 pl"><p>Bill To </p></div>
                    <div class="col-lg-2 pl" id="div_billto">
                        <input {{$InputStatus}} type="text" name="txtBILLTO" id="txtBILLTO" class="form-control"  autocomplete="off" value="{{ isset($objBillAddress[0])?$objBillAddress[0]:'' }}" readonly  />
                        <input type="hidden" name="BILLTO" id="BILLTO" class="form-control" autocomplete="off" value="{{ isset($objRO->BILLTO_CLID_REF)?$objRO->BILLTO_CLID_REF:'' }}" />
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-lg-2 pl"><p>Ship To</p></div>
                    <div class="col-lg-2 pl" id="div_shipto">
                        <input {{$InputStatus}} type="text" name="txtSHIPTO" id="txtSHIPTO" class="form-control"  autocomplete="off" value="{{ isset($objShpAddress[0])?$objShpAddress[0]:'' }}" readonly  />
                        <input type="hidden" name="SHIPTO" id="SHIPTO" class="form-control" autocomplete="off" value="{{ isset($objRO->SHIPTO_CLID_REF)?$objRO->SHIPTO_CLID_REF:'' }}" />
                        <input type="hidden" name="Tax_State" id="Tax_State" class="form-control" autocomplete="off" value=" {{ isset($TAXSTATE[0])?$TAXSTATE[0]:'' }}"   />
                    </div>
                    <div class="col-lg-2 pl"><p>Remarks</p></div>
                    <div class="col-lg-2 pl">
                        <input {{$InputStatus}} type="text" name="REMARKS" id="REMARKS" class="form-control" autocomplete="off" maxlength="200" value="{{ isset($objRO->REMARKS)?$objRO->REMARKS:'' }}"  >
                    </div>
                    <div class="col-lg-2 pl"><p>Item Group </p></div>                        
                      <div class="col-lg-2 pl" >                              
                        <input type="text" name="TXT_ITEM_GROUP" id="TXT_ITEM_GROUP" class="form-control"  autocomplete="off" readonly  />
                        <input type="hidden" name="ITEM_GROUP" id="ITEM_GROUP" class="form-control" autocomplete="off" />
                      </div>
                </div>

                <div class="row">
                <div class="col-lg-2 pl "><p>Deliver Date</p></div>
                    <div class="col-lg-2 pl">
                        <input type="date" name="DELIVER_DATE" id="DELIVER_DATE" value="{{ isset($objRO->DELIVER_DATE)?$objRO->DELIVER_DATE:'' }}" class="form-control mandatory" autocomplete="off" placeholder="dd/mm/yyyy" >
                    </div>
                </div>

            </div>

                    <div class="container-fluid purchase-order-view">

                        <div class="row">
                            <ul class="nav nav-tabs">
                                <li class="active"><a data-toggle="tab" href="#Material">Material</a></li>
                                
                                <li><a data-toggle="tab" href="#TC">T & C</a></li>
                                <li><a data-toggle="tab" href="#udf">UDF</a></li>
                            </ul>
                            <div class="tab-content">

                                <div id="Material" class="tab-pane fade in active">
                                    <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:280px;margin-top:10px;" >
                                        <table id="example2" class="display nowrap table table-striped table-bordered itemlist"  style="width:100%;height:auto !important;">
                                            <thead id="thead1"  style="position: sticky;top: 0">
                                                <tr>
                                                    <th colspan="3"><input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1" value="{{$objCount1}}"></th>
                                                    <th colspan="4">Sales Order</th>
                                                </tr>
                                                <tr>
                                                    <th rowspan="2" style=" width:15%;">Item Code</th>
                                                    <th rowspan="2" style=" width:15%;">Item Name</th>
                                                    <th rowspan="2" style=" width:10%;">Item Specification</th>
                                                    <th style=" width:15%;">Main UOM</th>
                                                    <th style=" width:15%;">Qty(Main UOM)</th>
                                                    <th style=" width:15%;">ALT UOM</th>
                                                    <th style=" width:15%;">Qty(Alt UOM)</th> 
                                                </tr>
                                            </thead>
                                            <tbody id="tbody_mat">
                                                <tr  class="participantRow">
                                                    <td style=" width:15%;"><input type="text" name="popupITEMID_0" id="popupITEMID_0" class="form-control"  autocomplete="off"  readonly/></td>
                                                    <td hidden><input type="hidden" name="ITEMID_REF_0" id="ITEMID_REF_0" class="form-control" autocomplete="off" /></td>
                                                    <td hidden><input type="hidden" name="SEID_REF_0" id="SEID_REF_0" class="form-control" autocomplete="off" /></td>
                                                    <td hidden><input type="hidden" name="SQID_REF_0" id="SQID_REF_0" class="form-control" autocomplete="off" /></td>
                                                    <td hidden><input type="hidden" name="SOID_REF_0" id="SOID_REF_0" class="form-control" autocomplete="off" /></td>
                                                    <td style=" width:15%;"><input type="text" name="ItemName_0" id="ItemName_0" class="form-control"  autocomplete="off"  readonly/></td>
                                                    <td style=" width:10%; align:center;" align="center" ><button class="btn" id="BtnItemspec_0" name="BtnItemspec_0" onclick="" type="button"><i class="fa fa-clone"></i></button></td>
                                                    <td style=" width:15%;"><input type="text" name="MAIN_UOM_0" id="MAIN_UOM_0" class="form-control"  autocomplete="off"  readonly/></td>
                                                    <td style=" width:15%;" hidden><input type="text" name="MAIN_UOMID_REF_0" id="MAIN_UOMID_REF_0" class="form-control"  autocomplete="off"  readonly/></td>
                                                    <td style=" width:15%;"><input type="text" name="SOQTYM_0" id="SOQTYM_0" class="form-control" maxlength="13"  autocomplete="off"  readonly/></td>
                                                    <td style=" width:15%;"><input type="text" name="ALT_UOM_0" id="ALT_UOM_0" class="form-control"  autocomplete="off"  readonly/></td>
                                                    <td style=" width:15%;" hidden><input type="text" name="ALT_UOMID_REF_0" id="ALT_UOMID_REF_0" class="form-control"  autocomplete="off"  readonly/></td>
                                                    <td style=" width:15%;"><input type="text" name="SOQTYA_0" id="SOQTYA_0" class="form-control" maxlength="13" autocomplete="off"  readonly/></td>
                                                    <td hidden><input type="hidden" name="HID_SPC_0" id="HID_SPC_0" class="form-control" autocomplete="off" /></td>
                                                  </tr>
                                                <tr></tr>
                                            </tbody>
                                    </table>
                                  </div>	
                                </div>                               
                              


                                <div id="Specification" class="tab-pane fade">
                                    <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:280px;margin-top:10px;" >
                                        <table id="example6" class="display nowrap table table-striped table-bordered itemlist"  style="width:100%;height:auto !important;">
                                            <thead id="thead1"  style="position: sticky;top: 0">
                                                <tr>
                                                  <th>ItemID<input class="form-control" type="hidden" name="Row_Count4" id ="Row_Count4"></th>
                                                  <th>Enquiry</th>
                                                  <th>Quotation</th> 
                                                  <th>Order</th>
                                                  <th>Specification</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody_spec">
                                              @if(!empty($objROISP))
                                                @foreach($objROISP as $ikey => $irow)
                                                <tr  class="participantRow5">
                                                  <td><input {{$InputStatus}} type="text" name={{"IITEMID_REF_".$ikey}} id={{"IITEMID_REF_".$ikey}} class="form-control" value="{{ $irow->ITEMID_REF }}" autocomplete="off" /></td>
                                                  <td><input {{$InputStatus}} type="text" name={{"ISEID_REF_".$ikey}} id={{"ISEID_REF_".$ikey}} class="form-control" value="{{ $irow->SEID_REF }}" autocomplete="off" /></td>
                                                  <td><input {{$InputStatus}} type="text" name={{"ISQID_REF_".$ikey}} id={{"ISQID_REF_".$ikey}} class="form-control" value="{{ $irow->SQID_REF }}" autocomplete="off" /></td>
                                                  <td><input {{$InputStatus}} type="text" name={{"ISOID_REF_".$ikey}} id={{"ISOID_REF_".$ikey}} class="form-control" value="{{ $irow->SOID_REF }}" autocomplete="off" /></td>
                                                  <td hidden><input {{$InputStatus}} type="hidden" name={{"Specification_ID_".$ikey}} id={{"Specification_ID_".$ikey}} class="form-control"  value="{{ $irow->Specification_ID }}" autocomplete="off" /></td>
                                                  <td><input {{$InputStatus}} type="text" name={{"Specification_Name_".$ikey}} id={{"Specification_Name_".$ikey}} class="form-control"  value="{{ $irow->Specification_Name }}" autocomplete="off" /></td>
                                                  <td><input {{$InputStatus}} type="text" name={{"Itemspec_".$ikey}} id={{"Itemspec_".$ikey}} class="form-control"  value="{{ $irow->ITEM_SPECI }}" autocomplete="off" /></td>
                                                  </tr>
                                                @endforeach 
                                                @else
                                                <tr  class="participantRow5">
                                                  <td><input {{$InputStatus}} type="text" name="IITEMID_REF_0" id="IITEMID_REF_0" class="form-control" autocomplete="off" /></td>
                                                  <td><input {{$InputStatus}} type="text" name="ISEID_REF_0" id="ISEID_REF_0" class="form-control" autocomplete="off" /></td>
                                                  <td><input {{$InputStatus}} type="text" name="ISQID_REF_0" id="ISQID_REF_0" class="form-control" autocomplete="off" /></td>
                                                  <td><input {{$InputStatus}} type="text" name="ISOID_REF_0" id="ISOID_REF_0" class="form-control" autocomplete="off" /></td>
                                                  <td hidden><input {{$InputStatus}} type="hidden" name="Specification_ID_0" id="Specification_ID_0" class="form-control"  autocomplete="off" /></td>
                                                  <td><input {{$InputStatus}} type="text" name="Specification_Name_0" id="Specification_Name_0" class="form-control"  autocomplete="off" /></td>
                                                  <td><input {{$InputStatus}} type="text" name="Itemspec_0" id="Itemspec_0" class="form-control"  autocomplete="off" /></td>
                                              </tr>
                                          @endif 
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
                                <div id="udf" class="tab-pane fade">
                                    <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="margin-top:10px;height:280px;width:50%;">
                                        <table id="example4" class="display nowrap table table-striped table-bordered itemlist" style="height:auto !important;">
                                            <thead id="thead1"  style="position: sticky;top: 0">
                                            <tr >
                                                <th>UDF Fields<input class="form-control" type="hidden" name="Row_Count3" id ="Row_Count3"></th>
                                                <th>Value / Comments</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($objUdfROData as $uindex=>$uRow)
                                              <tr  class="participantRow4">
                                                  <td><input type="text" name={{"popupUDFROID_".$uindex}} id={{"popupUDFROID_".$uindex}} class="form-control" value="{{$uRow->LABEL}}" autocomplete="off"  readonly/></td>
                                                  <td hidden><input type="hidden" name={{"UDFROID_REF_".$uindex}} id={{"UDFROID_REF_".$uindex}} class="form-control" value="{{$uRow->UDFROID}}" autocomplete="off"   /></td>
                                                  <td hidden><input type="hidden" name={{"UDFismandatory_".$uindex}} id={{"UDFismandatory_".$uindex}} value="{{$uRow->ISMANDATORY}}" class="form-control"   autocomplete="off" /></td>
                                                  <td id={{"udfinputid_".$uindex}}>
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
        
    </div><!--purchase-order-view-->

<!-- </div> -->
</form>
@endsection
@section('alert')

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
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='SO_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Sales Order</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="SalesOrderTable" class="display nowrap table  table-striped table-bordered">
    <thead>
          <tr id="none-select" class="searchalldata" hidden>
            
            <td> <input type="hidden" name="fieldid" id="hdn_sqid"/>
            <input type="hidden" name="fieldid2" id="hdn_sqid2"/></td>
          </tr>
    <tr>
      <th class="ROW1">Select</th> 
      <th class="ROW2">Order No.</th>
      <th class="ROW3">Order Date</th>
    </tr>
    </thead>
    <tbody>
      <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="SalesOrdercodesearch" class="form-control" onkeyup="SalesOrderCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="SalesOrdernamesearch" class="form-control" onkeyup="SalesOrderNameFunction()"></td>
      </tr>
    </tbody>
    </table>
      <table id="SalesOrderTable2" class="display nowrap table  table-striped table-bordered">
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
<!-- Sales Order Dropdown-->

<!-- UDF Dropdown -->
<div id="udfroidpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='udfroid_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>UDF Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="UDFROIDTable" class="display nowrap table  table-striped table-bordered" width="100%">
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
    <input type="text" id="UDFROIDcodesearch" onkeyup="UDFROIDCodeFunction()">
    </td>
    <td>
    <input type="text" id="UDFROIDnamesearch" onkeyup="UDFROIDNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="UDFROIDTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
        </thead>
        <tbody id="tbody_udfroid"> 
        @foreach ($objUdfROData as $udfindex=>$udfRow)
        <tr id="udfroid_{{ $udfindex }}" class="clsudfroid">
          <td width="50%">{{ $udfRow->LABEL }}
          <input type="hidden" id="txtudfroid_{{ $udfindex }}" data-desc="{{ $udfRow->LABEL }}"  value="{{ $udfRow->UDFROID }}"/>
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
<!-- UDF Dropdown-->

<!--Item Specification Popup-->

<!--
<div id="spftionidpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='spftion_close_popup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Item Specification</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="SpcftionCodeTable" class="display nowrap table  table-striped table-bordered" >
    <thead>
    <tr>
      <th  class="ROW1">Select</th>
      <th  class="ROW2">Item</th>
      <th  class="ROW3">Description</th>      
    </tr>
    </thead>   
    </table>
      <table id="SpcftionCodeTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2"> 
        </thead>

        <tbody id="tbody_spcfions" >
          @foreach($objROISP as $ikey => $irow)
          <tr  class="participantRow5">
            <td><input {{$InputStatus}} type="text" name={{"Specification_ID_".$ikey}} id={{"Specification_ID_".$ikey}} class="form-control"  value="{{ $irow->Specification_ID }}" autocomplete="off" /></td>
            <td hidden><input {{$InputStatus}} type="hidden" name={{"Specification_Name_".$ikey}} id={{"Specification_Name_".$ikey}} class="form-control"  value="{{ $irow->Specification_Name }}" autocomplete="off" /></td>
            <td hidden><input {{$InputStatus}} type="hidden" name={{"Itemspec_".$ikey}} id={{"Itemspec_".$ikey}} class="form-control"  value="{{ $irow->ITEM_SPECI }}" autocomplete="off" /></td>
            </tr>
            @endforeach
            @foreach($ObjSpc as $ikey => $irow)
            <tr class="participantRow5">
              <td><input type="checkbox" name="spcfchecked" id="spcfchecked" value="{{ $irow->SPECIFICATIONID }}"></td>
              <td><input {{$InputStatus}} type="text" name={{"Specification_ID_".$ikey}} id={{"Specification_ID_".$ikey}} class="form-control"  value="{{ $irow->SPECIFICATIONID }}" autocomplete="off" /></td>
              <td><input {{$InputStatus}} type="text" name={{"Specification_Name_".$ikey}} id={{"Specification_Name_".$ikey}} class="form-control"  value="{{ $irow->SPECIFICATIONNAME }}" autocomplete="off" /></td>
              <td><input {{$InputStatus}} type="text" name={{"Itemspec_".$ikey}} id={{"Itemspec_".$ikey}} class="form-control"  value="{{ $irow->SPECIFICATIONDESC }}" autocomplete="off" /></td>
              </tr>
              @endforeach
          
        </tbody>
      </table>
      <div class="text-center">
        <button class="btn savebutton" id="BtnISPSave" onclick="saveSpc()" title="Save" type="button" style="width:50px;"><i class="fa fa-save" ></i></button>            
    </div>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
-->

<div id="spftionidpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='spftion_close_popup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Item Specification</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="SpcftionCodeTable" class="display nowrap table  table-striped table-bordered" >
    <thead>
    <tr>
      <th  class="ROW1">Select</th>
      <th  class="ROW2">Item</th>
      <th  class="ROW3">Description</th>      
    </tr>
    </thead>   
    </table>
      <table id="SpcftionCodeTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2"> 
        </thead>
        <tbody id="tbody_spcfion" >
        </tbody>
      </table>
      <div class="text-center">
        <button class="btn savebutton" id="BtnISPSave" onclick="saveSpc()" title="Save" type="button" style="width:50px;"><i class="fa fa-save" ></i></button>            
    </div>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>


<!--Item Specification Popup-->

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
        </div><!--btdiv-->
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<!-- Alert -->

@endsection


@push('bottom-css')
<style>
#custom_dropdown, #frm_trn_ro_filter {
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
.single button {
    background: #eff7fb;
    width: 25px;
    border: 1px solid#0f69cc;
    padding: 10px 0;
    margin: 5px 0;
    text-align: center;
    color: #0f69cc;
    font-weight: bold;
}

.savebutton {
    background: #eff7fb;
    width: 50px;
    border: 1px solid#0f69cc;
    padding: 10px 0;
    margin: 5px 0;
    text-align: center;
    color: #0f69cc;
    font-weight: bold;
}
.column3_modal .ROW2 {
    width: 44% !important;
}

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

//UDF Tab Starts
//------------------------

let udftid = "#UDFSOIDTable2";
      let udftid2 = "#UDFSOIDTable";
      let udfheaders = document.querySelectorAll(udftid2 + " th");

      // Sort the table element when clicking on the table headers
      udfheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(udftid, ".clsudfsoid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function UDFSOIDCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("UDFSOIDcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("UDFSOIDTable2");
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

  function UDFSOIDNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("UDFSOIDnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("UDFSOIDTable2");
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
$("#udfsoid_closePopup").on("click",function(event){ 
     $("#udfsoidpopup").hide();
});

$('.clsudfsoid').dblclick(function(){
    
        var id = $(this).attr('id');
        var txtid =    $("#txt"+id+"").val();
        var txtname =   $("#txt"+id+"").data("desc");
        var fieldid2 = $(this).find('[id*="udfvalue"]').attr('id');
        var txtvaluetype = $.trim($(this).find('[id*="udfvalue"]').text());
        var txtismandatory =  $("#txt"+fieldid2+"").val();
        var txtdescription =  $("#txt"+fieldid2+"").data("desc");
        
        var txtcol = $('#hdn_UDFSOID').val();
        $("#"+txtcol).val(txtname);
        $("#"+txtcol).parent().parent().find("[id*='UDFSOID_REF']").val(txtid);
        $("#"+txtcol).parent().parent().find("[id*='UDFismandatory']").val(txtismandatory);
        
        var txt_id4 = $("#"+txtcol).parent().parent().find("[id*='udfinputid']").attr('id');  //<td> id 

        var strdyn = txt_id4.split('_');
        var lastele =   strdyn[strdyn.length-1];

        var dynamicid = "udfvalue_"+lastele;

        var chkvaltype2 =  txtvaluetype.toLowerCase();
        var strinp = '';

        if(chkvaltype2=='date'){

          strinp = '<input type="date" placeholder="dd/mm/yyyy" name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" autocomplete="off" /> ';       

        }else if(chkvaltype2=='time'){
          strinp= '<input type="time" placeholder="h:i" name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" autocomplete="off" /> ';

        }else if(chkvaltype2=='numeric'){
          strinp = '<input type="text" name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" autocomplete="off" /> ';

        }else if(chkvaltype2=='text'){

          strinp = '<input type="text" name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" autocomplete="off" /> ';
        
        }else if(chkvaltype2=='boolean'){

          strinp = '<input type="checkbox" name="'+dynamicid+ '" id="'+dynamicid+'" class="" /> ';
        
        }else if(chkvaltype2=='combobox'){
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

        $("#udfsoidpopup").hide();
        $("#UDFSOIDcodesearch").val(''); 
        $("#UDFSOIDnamesearch").val(''); 
        UDFSOIDCodeFunction();
        event.preventDefault();
            
 });
 
//UDF Tab Ends
//------------------------
    
//Sub GL Account Starts
//------------------------
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
      
$("#txtsubgl_popup").click(function(event)
{
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
    var MaterialClone = $('#hdnmaterial').val();
    var TCClone = $('#hdnTC').val();
    var ISPClone = $('#hdnISP').val();
    var ISPClone2 = $('#hdnISP2').val();
    $("#txtsubgl_popup").val(texdesc);
    $("#txtsubgl_popup").blur();
    $("#SLID_REF").val(txtval);
    if (txtval != oldSLID)
    {
      $("#OVFRM_DT").val('');
          $("#OVTOM_DT").val('');
          $("#CUSTOMERPONO").val('');
          $("#CUSTOMERDT").val('');
          $("#SONO_txt").val('');
          $("#SOID_REF").val('');
          $("#txtCRID_popup").val('');
          $("#CRID_REF").val('');
          $("#CONVERSION_FACT").val('');
          $("#REMARKS").val('');
          $("#txtBILLTO").val('');
          $("#BILLTO").val('');
          $("#txtBILLTO1").val('');
          $("#BILLTO1").val('');
          $("#txtSHIPTO").val('');
          $("#SHIPTO").val('');
          $("#txtSHIPTO1").val('');
          $("#SHIPTO1").val('');
        $('#Material').html(MaterialClone);
        $('#TC').html(TCClone);
        $('#Specification').html(ISPClone);
        $('#ISPpopup').html(ISPClone2);
        $('#Row_Count1').val('1');
        $('#Row_Count2').val('1');
        $('#Row_Count4').val('1');  
    }
    $("#customer_popus").hide();
    $("#customercodesearch").val(''); 
    $("#customernamesearch").val(''); 
    
    
      event.preventDefault();
});
}
  
//Sub GL Account Ends
//------------------------



//------------------------
  //Sales Order Dropdown
      let sotid = "#SalesOrderTable2";
      let sotid2 = "#SalesOrderTable";
      let salesOrderheaders = document.querySelectorAll(sotid2 + " th");

      // Sort the table element when clicking on the table headers
      salesOrderheaders.forEach(function(element, i) {
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

      function SalesOrderNameFunction() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("SalesOrdernamesearch");
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

      $('#SONO_txt').click(function(event)
      {
        var SLID_REF = $("#SLID_REF").val();
        if(SLID_REF!='')
        {
          $("#tbody_SO").html('');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url:'{{route("transaction",[315,"getsalesorder"])}}',
                type:'POST',
                data:{'SLID_REF':SLID_REF},
                success:function(data) {
                  $("#tbody_SO").html(data);    
                  BindSalesOrder();                    
                },
                error:function(data){
                  console.log("Error: Something went wrong.");
                  $("#tbody_SO").html('');                        
                },
            });
        }
          $("#SOpopup").show();      
      });

      $("#SO_closePopup").click(function(event){
        $("#SOpopup").hide();
      });

      function BindSalesOrder(){
      $(".clssoid").click(function()
      {
        var id = $(this).attr('id');
        var txtval =    $("#txt"+id+"").val();
        var texdesc =   $("#txt"+id+"").data("desc");
        var oldSOID =   $("#SOID_REF").val();
        var MaterialClone = $('#hdnmaterial').val();
        var TCClone = $('#hdnTC').val();
        var ISPClone = $('#hdnISP').val();
        var ISPClone2 = $('#hdnISP2').val();
        $('#SONO_txt').val(texdesc);
        $('#SOID_REF').val(txtval);
        if (txtval != oldSOID)
        {
            $('#Material').html(MaterialClone);
            $('#TC').html(TCClone);
            $('#Specification').html(ISPClone);
            $('#ISPpopup').html(ISPClone2);
            $('#Row_Count1').val('1');
            $('#Row_Count2').val('1');
            $('#Row_Count4').val('1');  
        }
        $("#SOpopup").hide();

        var customid = txtval;
        if(customid!='')
        {
          $("#OVFRM_DT").val('');
          $("#OVTOM_DT").val('');
          $("#CUSTOMERPONO").val('');
          $("#CUSTOMERDT").val('');
          $("#txtCRID_popup").val('');
          $("#CRID_REF").val('');
          $("#CONVERSION_FACT").val('');
          $("#REMARKS").val('');
          $("#txtBILLTO").val('');
          $("#BILLTO").val('');
          $("#txtBILLTO1").val('');
          $("#BILLTO1").val('');
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
                url:'{{route("transaction",[315,"getsodata"])}}',
                type:'POST',
                data:{'id':customid},
                success:function(data) {
                  var result = JSON.parse(data);
                  $("#OVFRM_DT").val(result["data"][0].OVFDT); 
                  $("#OVTOM_DT").val(result["data"][0].OVTDT); 
                  $("#CUSTOMERPONO").val(result["data"][0].CUSTOMERPONO);                         
                  $("#CUSTOMERDT").val(result["data"][0].CUSTOMERDT);
                  if(result["data"][0].SOFC == 1)
                  {
                    $("#FC").prop('checked',true);
                    $('#hdn_FC').val('1');
                  } 
                  else
                  {
                    $("#FC").prop('checked',false);
                    $('#hdn_FC').val('0');
                  }
                  $("#txtCRID_popup").val(result["data"][0].CRCODE); 
                  $("#CRID_REF").val(result["data"][0].CRID_REF); 
                  $("#CONVERSION_FACT").val(result["data"][0].CONVFACT); 
                  $("#REMARKS").val(result["data"][0].REMARKS); 
                },
                error:function(data){
                  console.log("Error: Something went wrong.");
                  $("#OVFRM_DT").val(''); 
                  $("#OVTOM_DT").val('');                         
                },
            });
            $.ajax({
                url:'{{route("transaction",[315,"getBillTo"])}}',
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
            $.ajax({
                url:'{{route("transaction",[315,"getShipTo"])}}',
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
            $.ajax({
                url:'{{route("transaction",[315,"getItemDetails"])}}',
                type:'POST',
                data:{'id':customid,'ROID':''},
                success:function(data) {
                  $("#tbody_mat").html('');
                  $("#tbody_mat").html(data);

                  var i=0;
                  $('#Material').find('.participantRow').each(function(){
                    if(i==0){
                      var ITEM_GROUP = $.trim($(this).parent().parent().find("[id*=grpCode]").val());
                      $("#TXT_ITEM_GROUP").val(ITEM_GROUP);
                    }
                    i++;       
                  });
                },
                error:function(data){
                  console.log("Error: Something went wrong.");
                  var MaterialClone = $('#hdnmaterial').val();
                  $("#tbody_mat").html(MaterialClone);  
                  $('#Row_Count1').val('1');                   
                },
            });
            $.ajax({
                url:'{{route("transaction",[315,"getItemCount"])}}',
                type:'POST',
                data:{'id':customid},
                success:function(data) {
                  $('#Row_Count1').val(data);
                },
                error:function(data){
                  console.log("Error: Something went wrong.");
                  $('#Row_Count1').val('1');                   
                },
            });
            $.ajax({
                url:'{{route("transaction",[315,"getTNCData"])}}',
                type:'POST',
                data:{'id':customid},
                success:function(data) {
                  if(data != '')
                  {
                    var result = JSON.parse(data);
                    $("#txtTNCID_popup").val(result["data"][0].TNC_CODE+'-'+result["data"][0].TNC_DESC); 
                    $("#TNCID_REF").val(result["data"][0].TNCID_REF); 
                  }
                  else
                  {
                    $("#txtTNCID_popup").val(''); 
                    $("#TNCID_REF").val(''); 
                  }
                },
                error:function(data){
                  console.log("Error: Something went wrong.");
                  $("#txtTNCID_popup").val(''); 
                  $("#TNCID_REF").val('');                         
                },
            });
            $.ajax({
                url:'{{route("transaction",[315,"getTNCDetails"])}}',
                type:'POST',
                data:{'id':customid},
                success:function(data) {
                  $("#tncbody").html('');
                  $("#tncbody").html(data);
                },
                error:function(data){
                  console.log("Error: Something went wrong.");
                  var TCClone = $('#hdnTC').val();
                  $("#tncbody").html(TCClone);  
                  $('#Row_Count2').val('1');                   
                },
            });
            $.ajax({
                url:'{{route("transaction",[315,"getTNCCount"])}}',
                type:'POST',
                data:{'id':customid},
                success:function(data) {
                  $("#Row_Count2").val(data);
                },
                error:function(data){
                  console.log("Error: Something went wrong.");
                  var TCClone = $('#hdnTC').val();
                  $('#Row_Count2').val('1');                   
                },
            });
        }
        $("#SalesOrdercodesearch").val(''); 
        $("#SalesOrdernamesearch").val(''); 
       
        event.preventDefault();
      });
    }

      

  //Sales Order Dropdown Ends
//------------------------

      let tid = "#SpcftionCodeTable2";
      let tid2 = "#SpcftionCodeTable";
      let headers = document.querySelectorAll(tid2 + " th");            
      headers.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(tid, ".clsvendorid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function loadSpftion(SPECIFICATIONID,SPECIFICATIONNAME,SPECIFICATIONDESC,IDSPF,IDSPF_VAL){
        
        $("#tbody_spcfion").html('');
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });

        $.ajax({
          url:'{{route("transaction",[315,"getItesmSpection"])}}',
          type:'POST',
          data:{'SPECIFICATIONID':SPECIFICATIONID,'SPECIFICATIONNAME':SPECIFICATIONNAME,'SPECIFICATIONDESC':SPECIFICATIONDESC,'IDSPF':IDSPF,'IDSPF_VAL':IDSPF_VAL},
          success:function(data) {
            $("#tbody_spcfion").html(data);
            bindSpFtionEvents();
          },
          error:function(data){
          console.log("Error: Something went wrong.");
          $("#tbody_spcfion").html('');                        
          },
        });
      }

$('#Material').on('click','[id*="BtnItemspec"]',function(event){  

  var IDSPF = $.trim($(this).parent().parent().find("[id*=IDSPF]").attr('id')); 

  var IDSPF_VAL = $.trim($(this).parent().parent().find("[id*=IDSPF]").val()); 

  if(IDSPF ===""){
    $("#FocusId").val($("#IDSPF"));
    $("#IDSPF").val('');  
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select Sales Order No.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else{
    var SPECIFICATIONID = ''; 
    var SPECIFICATIONNAME = ''; 
    var SPECIFICATIONDESC = ''; 
    loadSpftion(SPECIFICATIONID,SPECIFICATIONNAME,SPECIFICATIONDESC,IDSPF,IDSPF_VAL);  
    $("#spftionidpopup").show();
    event.preventDefault();

  }

});

      $("#spftion_close_popup").click(function(event){
        $("#spftionidpopup").hide();
        event.preventDefault();
      });
      function bindSpFtionEvents(){
        $('.clsvendorid').click(function(){
            var id = $(this).attr('id');
            var txtval =    $("#txt"+id+"").val();
            var texdesc =   $("#txt"+id+"").data("desc");          
            $("#BtnItemspec_0").val(texdesc);
            $("#BtnItemspec_0").blur();
            $("#spftionidpopup").hide();
              event.preventDefault();
        });
  }


$("#spftionidpopup").on('click', '.add', function() {
    var $tr = $(this).closest('table');
    var allTrs = $tr.find('.participantRow5').last();
    var lastTr = allTrs[allTrs.length-1];
    var $clone = $(lastTr).clone();
    $clone.find('td').each(function(){
        var id = $(this).attr('id') || null;
        if(id) {
            var i = id.substr(id.length-1);
            var prefix = id.substr(0, (id.length-1));
            $(this).attr('id', prefix+(+i+1));
        }

    }); 

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

    $clone.find('[id*="ItemSpecification"]').val('');
    $tr.closest('table').append($clone);  
    // $clone.find('.remove').removeAttr('disabled'); 
    
    event.preventDefault();
});
$("#spftionidpopup").on('click', '#remove', function() {
    var rowCount2 = $(this).closest('table').find('.participantRow5').length;
    if (rowCount2 > 1) {
    $(this).closest('.participantRow5').remove();     
    } 
    if (rowCount2 <= 1) { 
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

$('#spftionidpopup').on('click','#spftion_close_popup',function()
{
  
  $('#spftionidpopup').hide();
  var ISPClone2 = $('#hdnISP2').val();
  $("#spftionidpopup").html(ISPClone2); 
  event.preventDefault();
});

$('#spftionidpopup').on('click','#BtnISPSave',function()
{ 
  
    
  var ISP12= [];
  $('#Specification').find('.participantRow6').each(function(){
    if($(this).find('[id*="IITEMID_REF"]').val() != '')
    {
      var ispitem = $(this).find('[id*="IITEMID_REF"]').val()+'-'+$(this).find('[id*="ISOID_REF"]').val()
                  +'-'+$(this).find('[id*="ISQID_REF"]').val()+'-'+$(this).find('[id*="ISEID_REF"]').val();
      ISP12.push(ispitem);
    }
  });

  $('#spftionidpopup').find('.participantRow5').each(function()
  {

    var ItemID = $(this).find('[id*="hdnItemid"]').val();
    var SOID_REF = $(this).find('[id*="hdnSOID"]').val();
    var SQID_REF = $(this).find('[id*="hdnSQID"]').val();
    var SEQID_REF = $(this).find('[id*="hdnSEQID"]').val();
    var Specification = $(this).find('[id*="ItemSpecification"]').val();
    

    var SPFICTIONID = $(this).find('[id*="SPFICTIONID"]').val();
    var SPFICTIONNAME = $(this).find('[id*="SPFICTIONNAME"]').val();
    var SPFICTIONDES = $(this).find('[id*="SPFICTIONDES"]').val();    
      $("#TXT_SPFICTIONID").val(SPFICTIONID);
      $("#TXT_SPFICTIONNAME").val(SPFICTIONNAME);
      $("#TXT_SPFICTIONDES").val(SPFICTIONDES); 

      var ispitem2 = ItemID+'-'+SOID_REF+'-'+SQID_REF+'-'+SEQID_REF;
      if(jQuery.inArray(ispitem2, ISP12) !== -1)
      {
        $('#Specification').find('.participantRow6').each(function(){
        if($(this).find('[id*="IITEMID_REF"]').val() != '')
          {
            if(ispitem2 == $(this).find('[id*="IITEMID_REF"]').val()+'-'+$(this).find('[id*="ISOID_REF"]').val()
                      +'-'+$(this).find('[id*="ISQID_REF"]').val()+'-'+$(this).find('[id*="ISEID_REF"]').val())
                      ISP12.push(ispitem2);
            {
              $(this).find('[id*="Itemspec"]').val(Specification);
            }
          }
        });
      }
      else
      {
          var $tr = $('.participantRow6').closest('table');
          var allTrs = $tr.find('.participantRow6').last();
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

          $clone.find('[id*="IITEMID_REF"]').val(ItemID);
          $clone.find('[id*="ISOID_REF"]').val(SOID_REF);
          $clone.find('[id*="ISQID_REF"]').val(SQID_REF);
          $clone.find('[id*="ISEID_REF"]').val(SEQID_REF);
          $clone.find('[id*="Itemspec"]').val(Specification);
          $tr.closest('table').append($clone);   
          var rowCount3 = $('#Row_Count4').val();
          rowCount3 = parseInt(rowCount3)+1;
          $('#Row_Count4').val(rowCount3);
      }
  });
  $('#Specification').find('.participantRow6').each(function()
  {
    if($(this).find('[id*="IITEMID_REF"]').val() == '')
    {
      $(this).closest("tr").remove();
    }
  });
  $('#spftionidpopup').hide();
  var ISPClone2 = $('#hdnISP2').val();
  $("#spftionidpopup").html(ISPClone2); 
  event.preventDefault();
});
    



function saveSpc(){

var Specifct  = [];
var indexID   = '';

$('#SpcftionCodeTable2').find('.participantRow5').each(function(){     

  if ($(this).find('[id*="spcfchecked"]').is(':checked')) {

    var data = {};
    var SPFICTIONID = $(this).find('[id*="SPFICTIONID"]').val();
    var SPFICTIONNAME = $(this).find('[id*="SPFICTIONNAME"]').val();
    var SPFICTIONDES = $(this).find('[id*="SPFICTIONDES"]').val(); 
    var HID_SPC = $(this).find('[id*="HID_SPC"]').val(); 
    data.SPFID = SPFICTIONID;
    data.SPFNAME = SPFICTIONNAME;
    data.SPFDES = SPFICTIONDES;

    Specifct.push(data);
    indexID = HID_SPC;
   
  }   
 
});

var Spf_data = JSON.stringify(Specifct);

$("#"+indexID).val(Spf_data);

}
    

$(document).ready(function(e) {
    var Material = $("#Material").html(); 
    $('#hdnmaterial').val(Material);
    var TC = $("#TC").html(); 
    $('#hdnTC').val(TC);
    var ISP = $("#Specification").html(); 
    $('#hdnISP').val(ISP);
    var ISP2 = $("#spftionidpopup").html(); 
    $('#hdnISP2').val(ISP2);
    var soudf = <?php echo json_encode($objUdfROData); ?>;
    $("#Row_Count3").val(count3);
    $("#Row_Count5").val(1);
    $('#udf').find('.participantRow4').each(function(){
      var txt_id4 = $(this).find('[id*="udfinputid"]').attr('id');
      var udfid = $(this).find('[id*="UDFROID_REF"]').val();
      $.each( soudf, function( soukey, souvalue ) {
        if(souvalue.UDFROID == udfid)
        {
          var txtvaltype2 =   souvalue.VALUETYPE;
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
          var txtoptscombo2 =   souvalue.DESCRIPTIONS;
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

    $(function() { $('[id*="RONO"]').focus(); }); 
    var d = new Date(); 
    var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
    d.setDate(d.getDate() + 29);
    var todate = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
    $('#RODT').val(today);
    
    // $('#CUSTOMERDT').val(today);
    

    $('#btnAdd').on('click', function() {
        var viewURL = '{{route("transaction",[315,"add"])}}';
                  window.location.href=viewURL;
    });
    $('#btnExit').on('click', function() {
      var viewURL = '{{route('home')}}';
                  window.location.href=viewURL;
    });
    //to check the label duplicacy
     $('#RONO').focusout(function(){
      var RONO   =   $.trim($(this).val());
      if(RONO ===""){
                $("#FocusId").val('RONO');
                // $("[id*=txtlabel]").blur(); 
                $("#ProceedBtn").focus();
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text('Please enter value in RONO.');
                $("#alert").modal('show');
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk1');
                // return false;
            } 
        else{ 
        var trnsoForm = $("#frm_trn_ro");
        var formData = trnsoForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("transaction",[315,"checkso"])}}',
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
                                      $("#RONO").val('');
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

//SO Date Check
// $('#RODT').change(function( event ) {
//             var today = new Date();     
//             var d = new Date($(this).val()); 
//             today.setHours(0, 0, 0, 0) ;
//             d.setHours(0, 0, 0, 0) ;
//             var sodate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
//             if (d < today) {
//                 $(this).val(sodate);
//                 $("#alert").modal('show');
//                 $("#AlertMessage").text('RO Date cannot be less than Current date');
//                 $("#YesBtn").hide(); 
//                 $("#NoBtn").hide();  
//                 $("#OkBtn1").show();
//                 $("#OkBtn1").focus();
//                 highlighFocusBtn('activeOk1');
//                 event.preventDefault();
//             }
//             else
//             {
//                 event.preventDefault();
//             }

           
//         });
//SO Date Check



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
      window.location.href = "{{route('transaction',[315,'add'])}}";

   }//fnUndoYes


   window.fnUndoNo = function (){

      $("#RONO").focus();

   }//fnUndoNo


   


});
</script>

@endpush

@push('bottom-scripts')
<script>

$(document).ready(function() {


var lastdt =<?php echo json_encode($lastdt[0]->RODT); ?>;
  var ro = <?php echo json_encode($objRO); ?>;
  var today = new Date(); 
  var sodate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2);
  if(lastdt < ro.RODT)
  {
	$('#RODT').attr('min',lastdt);
  }
  else
  {
	  $('#RODT').attr('min',ro.RODT);
  }
  $('#RODT').attr('max',sodate);





    $('#frm_trn_ro1').bootstrapValidator({
       
        fields: {
            txtlabel: {
                validators: {
                    notEmpty: {
                        message: 'The RO NO is required'
                    }
                }
            },            
        },
        submitHandler: function(validator, form, submitButton) {
            alert( "Handler for .submit() called." );
             event.preventDefault();
             $("#frm_trn_ro").submit();
        }
    });
});
$( "#btnSaveSO" ).click(function() {
  var formSalesOrder = $("#frm_trn_ro");
  if(formSalesOrder.valid()){
 
    $("#FocusId").val('');
    var RONO           =   $.trim($("#RONO").val());
    var RODT           =   $.trim($("#RODT").val());
    var SLID_REF       =   $.trim($("#SLID_REF").val());
    var SOID_REF       =   $.trim($("#SOID_REF").val());

    if(RONO ===""){
        $("#FocusId").val($("#RONO"));
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please enter value in RONO.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
    }
    else if(RODT ===""){
        $("#FocusId").val($("#RODT"));
        $("#RODT").val(today);  
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please select SO Date.');
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
    else if(SOID_REF ===""){
        $("#FocusId").val($("#SOID_REF"));
        $("#SOID_REF").val('');  
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please select Sales Order.');
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
        var allblank12 = [];

            $('#Specification').find('.participantRow5').each(function(){
                  if($.trim($(this).find("[id*=IITEMID_REF]").val())!="")
                    {
                        if($.trim($(this).find('[id*="Itemspec"]').val()) != "")
                        {
                          allblank1.push('true');
                        }
                        else
                        {
                          allblank1.push('false');
                        }
                    }                
            });
            
            $('#udf').find('.participantRow4').each(function(){
                  if($.trim($(this).find("[id*=UDFSOID_REF]").val())!="")
                    {
                        allblank8.push('true');
                            if($.trim($(this).find("[id*=UDFismandatory]").val())=="1"){
                                  if($.trim($(this).find('[id*="udfvalue"]').val()) != "")
                                  {
                                    allblank9.push('true');
                                  }
                                  else
                                  {
                                    allblank9.push('false');
                                  }
                            }  
                    }                
            });

            if(jQuery.inArray("false", allblank1) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Please enter  Specifications.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
                }
            
            else if(jQuery.inArray("false", allblank9) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Please enter  Value / Comment in UDF Tab.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
                }
                else if(checkPeriodClosing(315,$("#RODT").val(),0) ==0){
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

  }
});
$( "#btnApprove" ).click(function() {
  var formSalesOrder = $("#frm_trn_ro");
  if(formSalesOrder.valid()){
 
    $("#FocusId").val('');
    var RONO           =   $.trim($("#RONO").val());
    var RODT           =   $.trim($("#RODT").val());
    var SLID_REF       =   $.trim($("#SLID_REF").val());
    var SOID_REF       =   $.trim($("#SOID_REF").val());

    if(RONO ===""){
        $("#FocusId").val($("#RONO"));
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please enter value in RONO.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
    }
    else if(RODT ===""){
        $("#FocusId").val($("#RODT"));
        $("#RODT").val(today);  
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please select SO Date.');
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
    else if(SOID_REF ===""){
        $("#FocusId").val($("#SOID_REF"));
        $("#SOID_REF").val('');  
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please select Sales Order.');
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
        var allblank12 = [];

            $('#Specification').find('.participantRow5').each(function(){
                  if($.trim($(this).find("[id*=IITEMID_REF]").val())!="")
                    {
                        if($.trim($(this).find('[id*="Itemspec"]').val()) != "")
                        {
                          allblank1.push('true');
                        }
                        else
                        {
                          allblank1.push('false');
                        }
                    }                
            });
            
            $('#udf').find('.participantRow4').each(function(){
                  if($.trim($(this).find("[id*=UDFSOID_REF]").val())!="")
                    {
                        allblank8.push('true');
                            if($.trim($(this).find("[id*=UDFismandatory]").val())=="1"){
                                  if($.trim($(this).find('[id*="udfvalue"]').val()) != "")
                                  {
                                    allblank9.push('true');
                                  }
                                  else
                                  {
                                    allblank9.push('false');
                                  }
                            }  
                    }                
            });

            if(jQuery.inArray("false", allblank1) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Please enter  Specifications.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
                }            
                else if(jQuery.inArray("false", allblank9) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Please enter  Value / Comment in UDF Tab.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
                }
                else if(checkPeriodClosing(315,$("#RODT").val(),0) ==0){
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").hide();
                  $("#OkBtn1").show();
                  $("#AlertMessage").text(period_closing_msg);
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                }
                else
                {
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
   // window.location.href = "{{route('transaction',[315,'index'])}}";

}); //yes button

window.fnSaveData = function (){   
//validate and save data
event.preventDefault();

     var trnsoForm = $("#frm_trn_ro");
    var formData = trnsoForm.serialize();
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$("#btnSaveSO").hide(); 
$(".buttonload").show(); 
$("#btnApprove").prop("disabled", true);

$.ajax({
    url:'{{ route("transactionmodify",[315,"update"])}}',
    type:'POST',
    data:formData,
    success:function(data) {  
      $(".buttonload").hide(); 
      $("#btnSaveSO").show();   
      $("#btnApprove").prop("disabled", false);     
        if(data.errors) {
            $(".text-danger").hide();

            if(data.errors.RONO){
                showError('ERROR_RONO',data.errors.RONO);
                        $("#YesBtn").hide();
                        $("#NoBtn").hide();
                        $("#OkBtn1").show();
                        $("#AlertMessage").text('Please enter correct value in RONO.');
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
      $("#btnSaveSO").show();   
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

//validate and save data
event.preventDefault();

     var trnsoForm = $("#frm_trn_ro");
    var formData = trnsoForm.serialize();
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$("#btnApprove").hide(); 
$(".buttonload_approve").show();  
$("#btnSaveSO").prop("disabled", true);
$.ajax({
    url:'{{ route("transactionmodify",[315,"Approve"])}}',
    type:'POST',
    data:formData,
    success:function(data) {
      $("#btnApprove").show();  
      $(".buttonload_approve").hide();  
      $("#btnSaveSO").prop("disabled", false);
       
        if(data.errors) {
            $(".text-danger").hide();

            if(data.errors.RONO){
                showError('ERROR_RONO',data.errors.RONO);
                        $("#YesBtn").hide();
                        $("#NoBtn").hide();
                        $("#OkBtn1").show();
                        $("#AlertMessage").text('Please enter correct value in RONO.');
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
      $("#btnSaveSO").prop("disabled", false);
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
    $("#RONO").focus();
});

//ok button
$("#OkBtn").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn").hide();
    $(".text-danger").hide();
    window.location.href = '{{route("transaction",[315,"index"]) }}';
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
    window.location.href = "{{route('transaction',[315,'index'])}}";
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


$(document).ready(function() { 
<?php if(isset($objRO->SOID_REF) && $objRO->SOID_REF !=""){ ?>


$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$.ajax({
    url:'{{route("transaction",[315,"getItemDetailsEdit"])}}',
    type:'POST',
    data:{'id':'{{ isset($objRO->SOID_REF)?$objRO->SOID_REF:'' }}','ROID':'{{ isset($objRO->ROID)?$objRO->ROID:'' }}'},
    success:function(data) {
      $("#tbody_mat").html('');
      $("#tbody_mat").html(data);
      var i=0;
      $('#Material').find('.participantRow').each(function(){
        if(i==0){
          var ITEM_GROUP = $.trim($(this).parent().parent().find("[id*=grpCode]").val());
          $("#TXT_ITEM_GROUP").val(ITEM_GROUP);
        }
        i++;       
      });
    },
    error:function(data){
      console.log("Error: Something went wrong.");
      var MaterialClone = $('#hdnmaterial').val();
      $("#tbody_mat").html(MaterialClone);  
      $('#Row_Count1').val('1');                   
    },
});         
<?php }?>
});

</script>


@endpush