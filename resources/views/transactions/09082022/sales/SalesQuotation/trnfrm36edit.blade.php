
@extends('layouts.app')
@section('content')  
   

    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('transaction',[36,'index'])}}" class="btn singlebt">Sales Quotation (SQ)</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
                        <button class="btn topnavbt" id="btnSaveSQ" ><i class="fa fa-floppy-o"></i> Save</button>
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
   
    <div class="container-fluid purchase-order-view">
        <form id="frm_trn_sq"  method="POST">   
            @csrf
            {{isset($objSQ->SQID[0]) ? method_field('PUT') : '' }}
            <div class="container-fluid filter">

                    <div class="inner-form">
                    <div class="row">
                            <div class="col-lg-2 pl"><p>With Lead</p></div>
                            <div class="col-lg-2 pl">
                                <input type="radio" {{$ActionStatus}}  name="LEAD" id="WITH_LEAD" {{$objSQ->QUOTATTION_TYPE =="PROSPECT" || $objSQ->QUOTATTION_TYPE =="CUSTOMER" ? "checked":""}} value="WITH_LEAD" class="form-radio" >
                            </div>                            
                            
                            <div class="col-lg-2 pl"><p>Without Lead</p></div>
                            <div class="col-lg-2 pl">
                            <input type="radio" name="LEAD" {{$ActionStatus}} {{$objSQ->QUOTATTION_TYPE =="DIRECT" ? "checked":""}} id="WITHOUT_LEAD" value="WITHOUT_LEAD" class="form-radio" >
                            </div>
                            <div class="col-lg-2 pl "><p>Lead No</p></div>
                            <div class="col-lg-2 pl"  >
                            <input type="text" {{$ActionStatus}} name="Leadpopup" id="txtLeadpopup" value="{{ isset($objSQ->LEAD_NO)?$objSQ->LEAD_NO:'' }}"  class="form-control mandatory"    readonly/>
                            <input type="hidden" name="LEAD_REF" id="LEAD_REF" value="{{ isset($objSQ->LEADID_REF)?$objSQ->LEADID_REF:'' }}"  class="form-control"  />                           
                            <input type="hidden" name="QUOTATIONTYPE" id="QUOTATIONTYPE" value="{{ isset($objSQ->QUOTATTION_TYPE)?$objSQ->QUOTATTION_TYPE:'' }}" class="form-control"  />                           
                            </div>     
                        </div>   
                        <div class="row">
                            <div class="col-lg-2 pl"><p>Quotation No *</p></div>
                            <div class="col-lg-2 pl">
                                <input type="text" {{$ActionStatus}} name="SQNO" id="SQNO" value="{{ isset($objSQ->SQNO)?$objSQ->SQNO:'' }}" class="form-control mandatory" maxlength="15" autocomplete="off" style="text-transform:uppercase" readonly >
                            </div>
                            
                            <div class="col-lg-2 pl"><p>Quotation Date *</p></div>
                            <div class="col-lg-2 pl">
                                <input type="hidden"  id="LASTSQDT" value="{{ isset($objSQ->SQDT)?$objSQ->SQDT:'' }}">
                                <input type="date"  {{$ActionStatus}} name="SQDT" id="SQDT" onchange="checkPeriodClosing(36,this.value,1)"  value="{{ isset($objSQ->SQDT)?$objSQ->SQDT:'' }}" class="form-control mandatory" autocomplete="off" placeholder="dd/mm/yyyy" >
                            </div>


                            <div class="col-lg-2 pl"><p id="customer_prospect"> {{$objSQ->QUOTATTION_TYPE =="PROSPECT" ? "Prospect *":"Customer *"}}</p></div>
                            <div class="col-lg-2 pl">
                                <input type="text" {{$ActionStatus}} name="SubGl_popup" id="txtgl_popup" value="{{$objSQ->QUOTATTION_TYPE =="PROSPECT" ?   $objSQ->PCODE. '-'.$objSQ->NAME: $objsubglcode->SGLCODE .'-'.$objsubglcode->SLNAME }}" class="form-control mandatory"  autocomplete="off" readonly/>
                                <input type="hidden" name="GLID_REF" id="GLID_REF" class="form-control" autocomplete="off" value="{{ isset($objSQ->GLID_REF)?$objSQ->GLID_REF:'' }}" />
                                <input type="hidden" name="SLID_REF" id="SLID_REF" class="form-control" autocomplete="off" value="{{ isset($objSQ->SLID_REF)?$objSQ->SLID_REF:'' }}" />
                                <input type="hidden" name="hdnmaterial" id="hdnmaterial" class="form-control" autocomplete="off" />
                            </div>
                     
                        </div>
                        <div class="row">                               
                            <div class="col-lg-2 pl "><p>Dealer</p></div>
                            <div class="col-lg-2 pl"  >
                            <input type="text"  {{$ActionStatus}}  name="Dealerpopup" id="txtDealerpopup" class="form-control mandatory"  autocomplete="off" value="{{ isset($objSQ->CUSTOMER_NAME)?$objSQ->CUSTOMER_NAME:'' }}"  readonly/>
                            <input type="hidden" name="DEALERID_REF" value="{{ isset($objSQ->DEALERID_REF)?$objSQ->DEALERID_REF:'' }}" id="DEALERID_REF" class="form-control" autocomplete="off" />                                
                            <input type="hidden" name="DEALER_COMMISSION" value="{{ isset($objSQ->COMMISION)?$objSQ->COMMISION:'' }}" id="DEALER_COMMISSION" class="form-control" autocomplete="off" />                                
                            </div>                            
                            <div class="col-lg-2 pl"><p>Dealer Commission</p></div>
                            <div class="col-lg-2 pl">
                                <input type="text" {{$ActionStatus}} value="{{ isset($objSQ->DEALER_COMMISSION_AMT)?$objSQ->DEALER_COMMISSION_AMT:'' }}"  name="DEALER_COMMISSION_AMT" id="DEALER_COMMISSION_AMT" readonly autocomplete="off" class="form-control" maxlength="100"  />
                            </div>
                            <div class="col-lg-2 pl "><p>Project</p></div>
                            <div class="col-lg-2 pl"  >
                            <input type="text" {{$ActionStatus}} value="{{ isset($objSQ->PROJECT_NAME)?$objSQ->PROJECT_NAME:'' }}"  name="Projectpopup" id="txtProjectpopup" class="form-control mandatory"  autocomplete="off"  readonly/>
                            <input type="hidden" name="PROJECTID_REF" value="{{ isset($objSQ->PROJECTID_REF)?$objSQ->PROJECTID_REF:'' }}" id="PROJECTID_REF" class="form-control" autocomplete="off" />                                                            
                            </div>    
                        </div>   


                        <div class="row">
                            <div class="col-lg-2 pl"><p>FC</p></div>
                            <div class="col-lg-2 pl">
                                <input type="checkbox" {{$ActionStatus}} name="FC" id="FC" class="form-checkbox" {{  isset($objSQ->FC) && $objSQ->FC == 1 ? 'checked' : ''}} >
                            </div>
                            
                            <div class="col-lg-2 pl "><p>Currency</p></div>
                            <div class="col-lg-2 pl" id="divcurrency" >
                                @if($objSQcurrency)
                                <input type="text" {{$ActionStatus}} name="CRID_popup" id="txtCRID_popup" class="form-control"  autocomplete="off" value="{{ $objSQcurrency }}"   />
                                @else
                                <input type="text" {{$ActionStatus}} name="CRID_popup" id="txtCRID_popup" class="form-control"  autocomplete="off"   disabled/>
                                @endif
                                <input type="hidden" name="CRID_REF" id="CRID_REF" class="form-control" autocomplete="off"   value="{{ isset($objSQ->CRID_REF)?$objSQ->CRID_REF:'' }}" />
                                
                            </div>
                            
                            <div class="col-lg-2 pl"><p>Conversion Factor</p></div>
                            <div class="col-lg-2 pl">
                                <input type="text" {{$ActionStatus}} name="CONVFACT" id="CONVFACT" class="form-control" maxlength="100" autocomplete="off" value="{{ isset($objSQ->CONVFACT)?$objSQ->CONVFACT:'' }}" {{ isset($objSQ->FC) && $objSQ->FC == 1 ? 'readonly' : 'disabled'}} />
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-lg-2 pl"><p>Quotation Validity From </p></div>
                            <div class="col-lg-2 pl">
                                <input type="date" {{$ActionStatus}} name="QVFDT" id="QVFDT" class="form-control mandatory" autocomplete="off" placeholder="dd/mm/yyyy" value="{{ isset($objSQ->QVFDT)?$objSQ->QVFDT:'' }}" >
                            </div>
                            
                            <div class="col-lg-2 pl"><p>Quotation Validity To </p></div>
                            <div class="col-lg-2 pl">
                                <input type="date" {{$ActionStatus}} name="QVTDT" id="QVTDT" class="form-control mandatory" autocomplete="off"  placeholder="dd/mm/yyyy" value="{{ isset($objSQ->QVTDT)?$objSQ->QVTDT:'' }}" >
                            </div>
                            
                            <div class="col-lg-2 pl"><p>Sales Person *</p></div>
                            <div class="col-lg-2 pl">
                                <input type="text" {{$ActionStatus}} name="SPID_popup" id="txtSPID_popup" class="form-control mandatory"  autocomplete="off" value="{{isset($objSPID[0])?$objSPID[0]:''}}"  readonly/>
                                <input type="hidden" name="SPID_REF" id="SPID_REF" class="form-control" autocomplete="off" value="{{ isset($objSQ->SPID_REF)?$objSQ->SPID_REF:'' }}" />
                            </div>
                            
                      
                            
                        </div>
                        <div class="row">
                            <div class="col-lg-2 pl"><p>Ref No </p></div>
                            <div class="col-lg-2 pl">
                                <input type="text" {{$ActionStatus}} name="REFNO" id="REFNO" class="form-control" maxlength="100" value="{{ isset($objSQ->REFNO)?$objSQ->REFNO:'' }}" autocomplete="off" style="text-transform:uppercase">
                            </div>
                            <div class="col-lg-2 pl"><p>Bill To </p></div>
                            <div class="col-lg-2 pl" id="div_billto">
                                <input type="text" {{$ActionStatus}} name="txtBILLTO" id="txtBILLTO" class="form-control"  autocomplete="off" value="{{isset($objBillAddress[0])?$objBillAddress[0]:''}}" readonly  />
                                <input type="hidden" name="BILLTO" id="BILLTO" class="form-control" autocomplete="off" value="{{ isset($objSQ->BILLTO)?$objSQ->BILLTO:'' }}" />
                            </div>
                           
                            <div class="col-lg-2 pl"><p>Ship To</p></div>
                            <div class="col-lg-2 pl" id="div_shipto">
                                <input type="text" {{$ActionStatus}} name="txtSHIPTO" id="txtSHIPTO" class="form-control"  autocomplete="off" value="{{isset($objBillAddress[0])?$objBillAddress[0]:''}}" readonly  />
                                <input type="hidden" name="SHIPTO" id="SHIPTO" class="form-control" autocomplete="off" value="{{ isset($objSQ->SHIPTO)?$objSQ->SHIPTO:'' }}" />
                                <input type="hidden" name="Tax_State" id="Tax_State" class="form-control" autocomplete="off" value=" {{isset($TAXSTATE[0])?$TAXSTATE[0]:''}}"   />
                            </div>
                            
                       
                        </div>
                        <div class="row">
                            <div class="col-lg-2 pl"><p>Remarks</p></div>
                            <div class="col-lg-2 pl">
                                <input type="text" {{$ActionStatus}} name="REMARKS" id="REMARKS" class="form-control" autocomplete="off" maxlength="200" value="{{ isset($objSQ->REMARKS)?$objSQ->REMARKS:'' }}"  >
                            </div>
                            <div class="col-lg-2 pl"><p>Direct Quotation </p></div>
                            <div class="col-lg-2 pl">
                                  <input type="checkbox" name="DirectSQ" id="DirectSQ" class="form-checkbox"  disabled/>
                            </div>
                           
                            <div class="col-lg-2 pl"><p>Total Value</p></div>
                            <div class="col-lg-2 pl">
                                <input type="text" name="TotalValue" id="TotalValue" class="form-control"  autocomplete="off" readonly  />
                            </div>
                        </div>
                    </div>

                    <div class="container-fluid">

                        <div class="row">
                            <ul class="nav nav-tabs">
                                <li class="active"><a data-toggle="tab" href="#Material">Material</a></li>
                                <li><a data-toggle="tab" href="#TC" id="TC_TAB" >T & C</a></li>
                                <li><a data-toggle="tab" href="#udf" id="UDF_TAB" >UDF</a></li>
                                <li><a data-toggle="tab" href="#CT" id="CT_TAB" >Calculation Template</a></li>
                            </ul>
                            
                            
                            
                            <div class="tab-content">

                                <div id="Material" class="tab-pane fade in active">
                                    <div class="table-responsive table-wrapper-scroll-y" style="height:500px;margin-top:10px;" >
                                        <table id="example2" class="display nowrap table table-striped table-bordered itemlist w-200" width="100%" style="height:auto !important;">
                                            <thead id="thead1"  style="position: sticky;top: 0">
                                                    
                                                    <tr>
                                                        <th colspan="3"></th>
                                                        <th colspan="4">Sales Enquiry</th>
                                                        <th colspan="4">Sales Quotation</th>
                                                        <th colspan="13"></th>
                                                    </tr>
                                                <tr>
                                                    <th rowspan="2">SE No<input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1"></th>
                                                    <th rowspan="2">Item Code</th>
                                                    <th rowspan="2">Item Name</th>
                                                    <th rowspan="2">Main UOM</th>
                                                    <th rowspan="2">Qty (Main UOM)</th>
                                                    <th rowspan="2">ALT UOM</th>
                                                    <th rowspan="2">Qty (Alt UOM)</th>
                                                    <th rowspan="2">Main UOM</th>
                                                    <th rowspan="2">Qty (Main UOM)</th>
                                                    <th rowspan="2">ALT UOM</th>
                                                    <th rowspan="2">Qty (Alt UOM)</th>
                                                    <th rowspan="2">Rate Per UoM</th>
                                                    <th colspan="2">Discount</th>
                                                    <th rowspan="2">Amount after discount</th>
                                                    <th rowspan="2">IGST Rate %</th>
                                                    <th rowspan="2">IGST Amount</th>
                                                    <th rowspan="2">CGST Rate %</th>
                                                    <th rowspan="2">CGST Amount</th>
                                                    <th rowspan="2">SGST Rate %</th>
                                                    <th rowspan="2">SGST Amount</th>
                                                    <th rowspan="2">Total GST Amount</th>
                                                    <th rowspan="2">Total after GST</th>
                                                    <th rowspan="2" width="3%">Action</th>
                                                </tr>
                                                
                                                    <tr>
                                                        <th>%</th>
                                                        <th>Amount</th>
                                                    </tr>
                                            </thead>
                                            <tbody>
                                            @if(!empty($objSQMAT))
                                                @foreach($objSQMAT as $key => $row)
                                                    <tr  class="participantRow">
                                                        <td hidden>
                                                        <input  class="form-control" type="hidden" name={{"SQMATID_".$key}} id ={{"SQMATID_".$key}} maxlength="100" value="{{ $row->SQMATID }}" autocomplete="off"   >
                                                        </td>
                                                        <td style="text-align:center;" >
                                                        <input style="width:100px;" type="text" {{$ActionStatus}} name={{"txtSE_popup_".$key}} id={{"txtSE_popup_".$key}} class="form-control"   autocomplete="off" value="{{$row->ENQNO}}"   readonly/></td>
                                                        <td hidden><input type="hidden"  name={{"SEQID_REF_".$key}} id={{"SEQID_REF_".$key}} class="form-control" value="{{ $row->SEQID_REF }}" autocomplete="off" /></td>
                                                        <td><input style="width:100px;" type="text" {{$ActionStatus}} name={{"popupITEMID_".$key}} id={{"popupITEMID_".$key}} class="form-control" value="{{$row->ICODE}}" autocomplete="off"  readonly/></td>
                                                        <td hidden><input type="hidden" name={{"ITEMID_REF_".$key}} id={{"ITEMID_REF_".$key}} class="form-control"  value="{{ $row->ITEMID_REF }}" autocomplete="off" /></td>
                                                        <td><input style="width:100px;" type="text" {{$ActionStatus}} name={{"ItemName_".$key}} id={{"ItemName_".$key}} class="form-control"  autocomplete="off" value="{{$row->ItemName}}" readonly/></td>
                                                        <td><input type="text" {{$ActionStatus}} name={{"SEMUOM_".$key}} id={{"SEMUOM_".$key}} class="form-control"  autocomplete="off" value="{{$row->SEMUOM}}" readonly/></td>
                                                        <td><input type="text" {{$ActionStatus}} name={{"SEMUOMQTY_".$key}} id={{"SEMUOMQTY_".$key}} class="form-control" maxlength="13" value="{{$row->SEMUOMQTY}}" autocomplete="off"  readonly/></td>
                                                        <td><input type="text" {{$ActionStatus}} name={{"SEAUOM_".$key}} id={{"SEAUOM_".$key}} class="form-control"  autocomplete="off"  value="{{$row->SEAUOM}}" readonly/></td>
                                                        <td><input type="text" {{$ActionStatus}} name={{"SEAUOMQTY_".$key}} id={{"SEAUOMQTY_".$key}} class="form-control" maxlength="13" value="{{$row->SEAUOMQTY}}" autocomplete="off"  readonly/></td>
                                                        <td><input type="text" {{$ActionStatus}} name={{"popupMUOM_".$key}} id={{"popupMUOM_".$key}} class="form-control"  autocomplete="off" value="{{$row->popupMUOM}}" readonly/></td>
                                                        <td hidden><input type="hidden" name={{"MAIN_UOMID_REF_".$key}} id={{"MAIN_UOMID_REF_".$key}} class="form-control" value="{{ $row->MAIN_UOMID_REF }}" autocomplete="off" /></td>
                                                        <td><input type="text" {{$ActionStatus}} name={{"SQ_QTY_".$key}} id={{"SQ_QTY_".$key}} class="form-control three-digits" maxlength="13" value="{{ $row->SQ_QTY }}"  autocomplete="off"  /></td>
                                                        <td hidden><input type="hidden" name={{"SQ_FQTY_".$key}} id={{"SQ_FQTY_".$key}} class="form-control three-digits" maxlength="13" value="1" autocomplete="off"   readonly/></td>
                                                        <td><input type="text" {{$ActionStatus}} name={{"popupAUOM_".$key}} id={{"popupAUOM_".$key}} class="form-control"  autocomplete="off" value="{{$row->popupAUOM}}" readonly/></td>
                                                        <td hidden><input type="hidden" name={{"ALT_UOMID_REF_".$key}} id={{"ALT_UOMID_REF_".$key}} class="form-control"  autocomplete="off" value="{{ $row->ALT_UOMID_REF }}"  readonly/></td>
                                                        <td><input type="text" {{$ActionStatus}} name={{"ALT_UOMID_QTY_".$key}} id={{"ALT_UOMID_QTY_".$key}} class="form-control three-digits" maxlength="13" value="{{$row->ALT_UOMID_QTY}}" autocomplete="off"  readonly/></td>
                                                        <td><input type="text" {{$ActionStatus}} name={{"RATEPUOM_".$key}} id={{"RATEPUOM_".$key}} class="form-control five-digits blurRate" maxlength="13" value="{{ $row->RATEPUOM }}"  autocomplete="off" /></td>
                                                        <td><input {{$AlpsStatus['disabled']}} type="text" name={{"DISCPER_".$key}} id={{"DISCPER_".$key}} class="form-control four-digits " maxlength="8" value="{{ $row->DISCPER }}"  autocomplete="off" style="width: 50px;" /></td>
                                                        <td><input {{$AlpsStatus['disabled']}} type="text" name={{"DISCOUNT_AMT_".$key}} id={{"DISCOUNT_AMT_".$key}} class="form-control two-digits" value="{{ $row->DISCOUNT_AMT }}" maxlength="15"  autocomplete="off"  /></td>
                                                        @php
                                                            $TaxAmt=number_format((($row->SQ_QTY*$row->RATEPUOM) - $row->DISCOUNT_AMT),2, '.', '');
                                                        @endphp
                                                        <td><input type="text" {{$ActionStatus}} name={{"DISAFTT_AMT_".$key}} id={{"DISAFTT_AMT_".$key}} class="form-control two-digits" maxlength="15" autocomplete="off" value="{{$TaxAmt}}" readonly/></td>
                                                        <td><input type="text" {{$ActionStatus}} name={{"IGST_".$key}} id={{"IGST_".$key}} class="form-control four-digits" maxlength="8" value="{{ $row->IGST }}" autocomplete="off"  readonly/></td>
                                                        @php
                                                            $IGSTAMT=number_format((((($row->SQ_QTY*$row->RATEPUOM) - $row->DISCOUNT_AMT)*$row->IGST)/100),2, '.', '');
                                                        @endphp
                                                        <td><input type="text" {{$ActionStatus}} name={{"IGSTAMT_".$key}} id={{"IGSTAMT_".$key}} class="form-control two-digits" maxlength="15" autocomplete="off" value="{{$IGSTAMT}}"  readonly/></td>
                                                        <td><input type="text" {{$ActionStatus}} name={{"CGST_".$key}} id={{"CGST_".$key}} class="form-control four-digits" maxlength="8" value="{{ $row->CGST }}" autocomplete="off"  readonly/></td>
                                                        @php
                                                            $CGSTAMT=number_format((((($row->SQ_QTY*$row->RATEPUOM) - $row->DISCOUNT_AMT)*$row->CGST)/100),2, '.', '');
                                                        @endphp
                                                        <td><input type="text" {{$ActionStatus}} name={{"CGSTAMT_".$key}} id={{"CGSTAMT_".$key}} class="form-control two-digits" maxlength="15" autocomplete="off" value="{{$CGSTAMT}}"  readonly/></td>
                                                        <td><input type="text" {{$ActionStatus}} name={{"SGST_".$key}} id={{"SGST_".$key}} class="form-control four-digits" maxlength="8" value="{{ $row->SGST }}" autocomplete="off"  readonly/></td>
                                                        @php
                                                            $SGSTAMT=number_format((((($row->SQ_QTY*$row->RATEPUOM) - $row->DISCOUNT_AMT)*$row->SGST)/100),2, '.', '');
                                                        @endphp
                                                        <td><input type="text" {{$ActionStatus}} name={{"SGSTAMT_".$key}} id={{"SGSTAMT_".$key}} class="form-control two-digits" maxlength="15" autocomplete="off" value="{{$SGSTAMT}}"  readonly/></td>
                                                        @php
                                                            $IGSTAMT=number_format((((($row->SQ_QTY*$row->RATEPUOM) - $row->DISCOUNT_AMT)*$row->IGST)/100),2, '.', '');
                                                            $CGSTAMT=number_format((((($row->SQ_QTY*$row->RATEPUOM) - $row->DISCOUNT_AMT)*$row->CGST)/100),2, '.', '');
                                                            $SGSTAMT=number_format((((($row->SQ_QTY*$row->RATEPUOM) - $row->DISCOUNT_AMT)*$row->SGST)/100),2, '.', '');
                                                            $TOTGST =number_format(($IGSTAMT+$CGSTAMT+$SGSTAMT),2, '.', '');
                                                        @endphp
                                                        <td><input type="text" {{$ActionStatus}} name={{"TGST_AMT_".$key}} id={{"TGST_AMT_".$key}} class="form-control two-digits" maxlength="15" autocomplete="off" value="{{$TOTGST}}"   readonly/></td>
                                                        @php
                                                            $TaxAmt=number_format((($row->SQ_QTY*$row->RATEPUOM) - $row->DISCOUNT_AMT),2, '.', '');
                                                            $IGSTAMT=number_format((((($row->SQ_QTY*$row->RATEPUOM) - $row->DISCOUNT_AMT)*$row->IGST)/100),2, '.', '');
                                                            $CGSTAMT=number_format((((($row->SQ_QTY*$row->RATEPUOM) - $row->DISCOUNT_AMT)*$row->CGST)/100),2, '.', '');
                                                            $SGSTAMT=number_format((((($row->SQ_QTY*$row->RATEPUOM) - $row->DISCOUNT_AMT)*$row->SGST)/100),2, '.', '');
                                                            $TOTAMT =number_format(($TaxAmt+$IGSTAMT+$CGSTAMT+$SGSTAMT),2, '.', '');
                                                        @endphp
                                                        <td><input type="text" {{$ActionStatus}} name={{"TOT_AMT_".$key}} id={{"TOT_AMT_".$key}} class="form-control two-digits" maxlength="15" autocomplete="off" value="{{$TOTAMT}}" readonly/></td>
                                                        <td align="center"><button class="btn add material" {{$ActionStatus}} title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button><button class="btn remove dmaterial" {{$ActionStatus}} title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
                                                    </tr>
                                                    <tr></tr>
                                                @endforeach 
                                            @endif 
                                            </tbody>
                                    </table>
                                    </div>	
                                </div>
                                
                                
                                
                                <div id="TC" class="tab-pane fade">
                                    
                                    
                                    <div class="row" style="margin-top:10px;margin-left:3px;" >	
                                        <div class="col-lg-1 pl"><p>T&C Template</p></div>
                                        <div class="col-lg-2 pl">
                                        <input type="text" {{$ActionStatus}} name="txtTNCID_popup" id="txtTNCID_popup" class="form-control"  autocomplete="off"  readonly/>
                                        @if(!empty($objSQTNC))
                                         <input type="hidden" name="TNCID_REF" id="TNCID_REF" class="form-control" value="{{$objSQTNC[0]->TNCID_REF}}" autocomplete="off" />
                                         @else
                                         <input type="hidden" name="TNCID_REF" id="TNCID_REF" class="form-control"  autocomplete="off" />
                                         @endif 
                                        </div>
                                    </div>
                                    
                                    
                                    <div class="table-responsive table-wrapper-scroll-y " style="margin-top:10px;height:240px;width:50%;">
                                        
                                        <table id="example3" class="display nowrap table table-striped table-bordered itemlist" style="height:auto !important;">
                                            <thead id="thead1"  style="position: sticky;top: 0">
                                            <tr >
                                                <th>Terms & Conditions Description<input class="form-control" type="hidden" name="Row_Count2" id ="Row_Count2"></th>
                                                <th>Value / Comment</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody id="tncbody">
                                            @if(!empty($objSQTNC))
                                                @foreach($objSQTNC as $Tkey => $Trow)
                                                    <tr  class="participantRow3">
                                                    <td><input type="text" {{$ActionStatus}} name={{"popupTNCDID_".$Tkey}} id={{"popupTNCDID_".$Tkey}} class="form-control"  autocomplete="off"  readonly/></td>
                                                    <td hidden><input type="hidden" name={{"TNCDID_REF_".$Tkey}} id={{"TNCDID_REF_".$Tkey}} class="form-control" value="{{$Trow->TNCDID_REF}}" autocomplete="off" /></td>
                                                    <td hidden><input type="hidden" name={{"TNCismandatory_".$Tkey}} id={{"TNCismandatory_".$Tkey}} class="form-control" autocomplete="off" /></td>
                                                    <td id={{"tdinputid_".$Tkey}}>
                                                    {{-- dynamic input --}} 
                                                    </td>
                                                        <td align="center" ><button class="btn add TNC" {{$ActionStatus}} title="add" data-toggle="tooltip"  type="button" disabled><i class="fa fa-plus"></i></button><button class="btn remove DTNC" {{$ActionStatus}} title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
                                                    </tr>
                                                <tr></tr>
                                                @endforeach 
                                                @else
                                                <tr  class="participantRow3">
                                                    <td><input type="text" {{$ActionStatus}} name="popupTNCDID_0" id="popupTNCDID_0" class="form-control"  autocomplete="off"  readonly/></td>
                                                    <td hidden><input type="hidden" name="TNCDID_REF_0" id="TNCDID_REF_0" class="form-control" autocomplete="off" /></td>
                                                    <td hidden><input type="hidden" name="TNCismandatory_0" id="TNCismandatory_0" class="form-control" autocomplete="off" /></td>
                                                    <td id="tdinputid_0">
                                                      {{-- dynamic input --}} 
                                                    </td>
                                                        <td align="center" ><button class="btn add TNC" {{$ActionStatus}} title="add" data-toggle="tooltip"  type="button" disabled><i class="fa fa-plus"></i></button><button class="btn remove DTNC" {{$ActionStatus}} title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
                                                    </tr>
                                                <tr></tr>
                                            @endif 
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                    

                                <div id="udf" class="tab-pane fade">
                                    <div class="table-responsive table-wrapper-scroll-y " style="margin-top:10px;height:500px;width:50%;">
                                        <table id="example4" class="display nowrap table table-striped table-bordered itemlist" style="height:auto !important;">
                                            <thead id="thead1"  style="position: sticky;top: 0">
                                            <tr >
                                                <th>UDF Fields<input class="form-control" type="hidden" name="Row_Count3" id ="Row_Count3"></th>
                                                <th>Value / Comments</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                        @if(!empty($objSQUDF))
                                            @foreach($objSQUDF as $Ukey => $Urow)
                                                <tr  class="participantRow4">
                                                    <td><input type="text" {{$ActionStatus}} name={{"popupUDFSQID_".$Ukey}} id={{"popupUDFSQID_".$Ukey}}  class="form-control"  autocomplete="off"  readonly/></td>
                                                    <td hidden><input type="hidden" name={{"UDFSQID_REF_".$Ukey}}  id={{"UDFSQID_REF_".$Ukey}} class="form-control" value="{{$Urow->UDFSQID_REF}}" autocomplete="off" /></td>
                                                    <td hidden><input type="hidden" name={{"UDFismandatory_".$Ukey}} id={{"UDFismandatory_".$Ukey}} class="form-control" autocomplete="off" /></td>
                                                    <td id={{"udfinputid_".$Ukey}}>
                                                    {{-- dynamic input --}} 
                                                    </td>
                                                    <td align="center" ><button class="btn add UDF" {{$ActionStatus}} title="add" data-toggle="tooltip" type="button" disabled><i class="fa fa-plus"></i></button><button class="btn remove DUDF" {{$ActionStatus}} title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
                                                </tr>
                                                <tr></tr>
                                            @endforeach 
                                        @else
                                            @foreach($objUdfSQData as $uindex=>$uRow)
                                              <tr  class="participantRow4">
                                                  <td><input type="text" {{$ActionStatus}} name={{"popupUDFSQID_".$uindex}} id={{"popupUDFSQID_".$uindex}} class="form-control" value="{{$uRow->LABEL}}" autocomplete="off"  readonly/></td>
                                                  <td hidden><input type="hidden" name={{"UDFSQID_REF_".$uindex}} id={{"UDFSQID_REF_".$uindex}} class="form-control" value="{{$uRow->UDFSQID}}" autocomplete="off"   /></td>
                                                  <td hidden><input type="hidden" name={{"UDFismandatory_".$uindex}} id={{"UDFismandatory_".$uindex}} value="{{$uRow->ISMANDATORY}}" class="form-control"   autocomplete="off" /></td>
                                                  <td id={{"udfinputid_".$uindex}} >
                                                    
                                                  </td>
                                                  <td align="center" ><button class="btn add UDF" {{$ActionStatus}} title="add" data-toggle="tooltip" type="button" disabled><i class="fa fa-plus"></i></button><button class="btn remove DUDF"  {{$ActionStatus}} title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
                                                  
                                              </tr>
                                              <tr></tr>
                                            @endforeach
                                        @endif 
                                        
                                    
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <div id="CT" class="tab-pane fade">
                                    <div class="row" style="margin-top:10px;margin-left:3px;" >	
                                        <div class="col-lg-2 pl"><p>Calculation Template</p></div>
                                        <div class="col-lg-2 pl">
                                        <input type="text" {{$ActionStatus}} name="txtCTID_popup" id="txtCTID_popup" class="form-control"  autocomplete="off"  readonly/>
                                        @if(!empty($objSQCAL))
                                         <input type="hidden" name="CTID_REF" id="CTID_REF" class="form-control" value="{{$objSQCAL[0]->CTID_REF}}" autocomplete="off" />
                                         @else
                                         <input type="hidden" name="CTID_REF" id="CTID_REF" class="form-control" autocomplete="off" />
                                        @endif
                                        </div>
                                    </div>
                                    <div class="table-responsive table-wrapper-scroll-y" style="height:240px;margin-top:10px;" >
                                        <table id="example5" class="display nowrap table table-striped table-bordered itemlist " width="100%" style="height:auto !important;">
                                            <thead id="thead1"  style="position: sticky;top: 0">
                                                <tr>
                                                    <th>Calculation Component<input class="form-control" type="hidden" name="Row_Count4" id ="Row_Count4"></th>
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
                                            @if(!empty($objSQCAL))
                                                @foreach($objSQCAL as $Ckey => $Crow)
                                                    <tr  class="participantRow5">
                                                        <td><input type="text" {{$ActionStatus}} name={{"popupTID_".$Ckey}} id={{"popupTID_".$Ckey}}  class="form-control"  autocomplete="off"  readonly/></td>
                                                        <td hidden><input type="hidden" name={{"TID_REF_".$Ckey}}  id={{"TID_REF_".$Ckey}}  class="form-control" autocomplete="off" value="{{$Crow->TID_REF}}" /></td>
                                                        <td><input type="text" {{$ActionStatus}} name={{"RATE_".$Ckey}}  id={{"RATE_".$Ckey}} class="form-control four-digits" maxlength="8" autocomplete="off" value="{{$Crow->RATE}}"  readonly/></td>
                                                        <td hidden><input type="hidden" name={{"BASIS_".$Ckey}} id={{"BASIS_".$Ckey}} class="form-control" autocomplete="off"  /></td>
                                                        <td hidden><input type="hidden" name={{"SQNO_".$Ckey}} id={{"SQNO_".$Ckey}} class="form-control" autocomplete="off" /></td>
                                                        <td hidden><input type="hidden" name={{"FORMULA_".$Ckey}} id={{"FORMULA_".$Ckey}} class="form-control" autocomplete="off" /></td>
                                                        <td><input type="text" {{$ActionStatus}} name={{"VALUE_".$Ckey}} id={{"VALUE_".$Ckey}} class="form-control two-digits" maxlength="15" autocomplete="off" value="{{$Crow->VALUE}}" readonly /></td>
                                                        <td style="text-align:center;" ><input type="checkbox" {{$ActionStatus}} class="filter-none" name={{"calGST_".$Ckey}} id={{"calGST_".$Ckey}} {{$Crow->GST == 1 ? 'checked' : ''}}   ></td>
                                                        
                                                        <td><input type="text" {{$ActionStatus}} name={{"calIGST_".$Ckey}} id={{"calIGST_".$Ckey}} class="form-control four-digits" maxlength="8" autocomplete="off" value="{{$Crow->IGST}}" readonly/></td>
                                                        <td><input type="text" {{$ActionStatus}} name={{"AMTIGST_".$Ckey}} id={{"AMTIGST_".$Ckey}} class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                        <td><input type="text" {{$ActionStatus}} name={{"calCGST_".$Ckey}} id={{"calCGST_".$Ckey}} class="form-control four-digits" maxlength="8" autocomplete="off" value="{{$Crow->CGST}}" readonly/></td>
                                                        <td><input type="text" {{$ActionStatus}} name={{"AMTCGST_".$Ckey}} id={{"AMTCGST_".$Ckey}} class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                        <td><input type="text" name={{"calSGST_".$Ckey}} id={{"calSGST_".$Ckey}} class="form-control four-digits" maxlength="8" autocomplete="off" value="{{$Crow->SGST}}" readonly/></td>
                                                        <td><input type="text" {{$ActionStatus}} name={{"AMTSGST_".$Ckey}} id={{"AMTSGST_".$Ckey}} class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                        <td><input type="text" {{$ActionStatus}} name={{"TOTGSTAMT_".$Ckey}} id={{"TOTGSTAMT_".$Ckey}} class="form-control two-digits"  maxlength="15" autocomplete="off"  readonly/></td>
                                                        <td style="text-align:center;"><input type="checkbox" class="filter-none" name={{"calACTUAL_".$Ckey}} id={{"calACTUAL_".$Ckey}} value="" {{$Crow->ACTUAL == 1 ? 'checked' : ''}}  ></td>
                                                        <td align="center" ><button class="btn add" {{$ActionStatus}} title="add" data-toggle="tooltip" type="button" disabled><i class="fa fa-plus"></i></button><button class="btn remove" {{$ActionStatus}} title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
                                                    </tr>
                                                    <tr></tr>
                                                @endforeach 
                                                @else
                                                <tr  class="participantRow5">
                                                    <td><input type="text" {{$ActionStatus}} name="popupTID_0" id="popupTID_0" class="form-control"  autocomplete="off"  readonly/></td>
                                                    <td hidden><input type="hidden"  name="TID_REF_0" id="TID_REF_0" class="form-control" autocomplete="off" /></td>
                                                    <td><input type="text" name="RATE_0" {{$ActionStatus}} id="RATE_0" class="form-control four-digits" maxlength="8" autocomplete="off"  readonly/></td>
                                                    <td hidden><input type="hidden" name="BASIS_0" id="BASIS_0" class="form-control" autocomplete="off" /></td>
                                                    <td hidden><input type="hidden" name="SQNO_0" id="SQNO_0" class="form-control" autocomplete="off" /></td>
                                                    <td><input type="text" {{$ActionStatus}} name="VALUE_0" id="VALUE_0" class="form-control two-digits" maxlength="15" autocomplete="off" readonly  /></td>
                                                    <td style="text-align:center;" ><input type="checkbox" {{$ActionStatus}} class="filter-none" name="calGST_0" id="calGST_0" value="" ></td>
                                                    <td><input type="text" {{$ActionStatus}} name="calIGST_0" id="calIGST_0" class="form-control four-digits" maxlength="8" autocomplete="off"  readonly/></td>
                                                    <td><input type="text" {{$ActionStatus}} name="AMTIGST_0" id="AMTIGST_0" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                    <td><input type="text" {{$ActionStatus}} name="calCGST_0" id="calCGST_0" class="form-control four-digits" maxlength="8" autocomplete="off"  readonly/></td>
                                                    <td><input type="text" {{$ActionStatus}} name="AMTCGST_0" id="AMTCGST_0" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                    <td><input type="text" {{$ActionStatus}} name="calSGST_0" id="calSGST_0" class="form-control four-digits" maxlength="8" autocomplete="off"  readonly/></td>
                                                    <td><input type="text" {{$ActionStatus}} name="AMTSGST_0" id="AMTSGST_0" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                    <td><input type="text" {{$ActionStatus}} name="TOTGSTAMT_0" id="TOTGSTAMT_0" class="form-control two-digits"  maxlength="15" autocomplete="off"  readonly/></td>
                                                    <td style="text-align:center;"><input type="checkbox" class="filter-none" name="calACTUAL_0" id="calACTUAL_0" value=""   ></td>
                                                    <td align="center" ><button class="btn add"  {{$ActionStatus}} title="add" data-toggle="tooltip" type="button" disabled><i class="fa fa-plus"></i></button><button class="btn remove" {{$ActionStatus}} title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
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
          </form>   
    </div><!--purchase-order-view-->

<!-- </div> -->

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

<!-- Alert -->


   
<!-- Dealer Popup starts here   -->
<div id="Dealer_popup" class="modal" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-md" style="width:60%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" id="Dealer_closePopup">&times;</button>
            </div>
            <div class="modal-body">
                <div class="tablename"><p>Dealer List</p></div>
                <div class="single single-select table-responsive table-wrapper-scroll-y my-custom-scrollbar">
                <input type="hidden" class="mainitem_tab1">
                    <table id="DealerOrder" class="display nowrap table table-striped table-bordered" style="width:100%;">
                        <thead>
                            <tr>
                            <th style="width:10%;">Select</th> 
                                <th style="width:40%;"> Code</th>
                                <th style="width:40%;"> Name</th>
             
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                            <th style="text-align:center; width:10%;">&#10004;</th>  
                                <td style="width:30%;"> 
                                    <input type="text" id="DealerNo" class="form-control" onkeyup="DealerDocFunction()"  />
                                </td>
                                <td style="width:30%;">
                                    <input type="text" id="DealerName" class="form-control" onkeyup="DealerNameFunction()"  />
                                </td>
                      
                          
                            </tr>
                        </tbody>
                    </table>
                    <table id="DealerOrderTable2" class="display nowrap table table-striped table-bordered">
                        <thead id="thead2">
                        <tr>
        
                <td id="Data_seach_dealer" colspan="4">please wait...</td>
          </tr>
                        </thead>
                        <tbody id="Dataresult_dealer">
                           
                        </tbody>
                    </table>
                </div>
                <div class="cl"></div>
            </div>
        </div>
    </div>
</div>

 
<!-- Project Popup starts here   -->
<div id="Project_popup" class="modal" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-md" style="width:60%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" id="Project_closePopup">&times;</button>
            </div>
            <div class="modal-body">
                <div class="tablename"><p>Project List</p></div>
                <div class="single single-select table-responsive table-wrapper-scroll-y my-custom-scrollbar">
                <input type="hidden" class="mainitem_tab1">
                    <table id="ProjectOrder" class="display nowrap table table-striped table-bordered" style="width:100%;">
                        <thead>
                            <tr>
                            <th style="width:10%;">Select</th> 
                                <th style="width:40%;"> Code</th>
                                <th style="width:40%;"> Name</th>
             
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                            <th style="text-align:center; width:10%;">&#10004;</th>  
                                <td style="width:30%;"> 
                                    <input type="text" id="ProjectNo" class="form-control" onkeyup="ProjectDocFunction()"  />
                                </td>
                                <td style="width:30%;">
                                    <input type="text" id="ProjectName" class="form-control" onkeyup="ProjectNameFunction()"  />
                                </td>
                      
                          
                            </tr>
                        </tbody>
                    </table>
                    <table id="ProjectOrderTable2" class="display nowrap table table-striped table-bordered">
                        <thead id="thead2">
                        <tr>
        
                <td id="Data_seach_project" colspan="4">please wait...</td>
          </tr>
                        </thead>
                        <tbody id="Dataresult_project">
                           
                        </tbody>
                    </table>
                </div>
                <div class="cl"></div>
            </div>
        </div>
    </div>
</div>




<!--Price List dropdown-->
<div id="STRpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='STR_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Price List</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="STRNOTable" class="display nowrap table  table-striped table-bordered" >
    <thead>
          <tr id="none-select" class="searchalldata" hidden>
            
            <td> 
            <input type="hidden"  id="hdn_STRid"/>
            <input type="hidden" id="hdn_STRid2"/>
            <input type="hidden" id="hdn_STRid3"/>
            </td>
          </tr>

      <tr>
        
        <th style="width:10%">Select</th> 
        <th style="width:40%">Price Type</th>
        <th style="width:40%">Price</th> 
      </tr>
    </thead>
    <tbody>

      <tr>
        <th style="width:10%"><span class="check_th">&#10004;</span></th>
        <td style="width:40%"><input type="text" id="STRcodesearch" class="form-control" onkeyup="STRCodeFunction()"></td>
        <td style="width:40%" class=""><input type="text" id="STRDTsearch" class="form-control" onkeyup="STRDTFunction()"></td>
      </tr>

    </tbody>
    </table>
      <table id="STRNOTable2" class="display nowrap table  table-striped table-bordered"  >
        <thead id="thead2">

        </thead>
        <tbody id="tbody_STR">     
        
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<!-- Price List To Dropdown -->


<!-- Bill To Dropdown -->
<div id="BillTopopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:60%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='BillToclosePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Bill To</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="BillToTable" class="display nowrap table  table-striped table-bordered" style="width:100%;">
    <thead>
    <tr>
            <th style="width:10%;">Select</th> 
            <th style="width:30%;">Name</th>
            <th style="width:60%;">Address</th>
    </tr>
    </thead>
    <tbody>
    <tr>
    <th style="text-align:center; width:10%;">&#10004;</th>
   <td style="width:30%;"> 
    <input type="text" id="BillTocodesearch" class="form-control" onkeyup="BillToCodeFunction()">
    </td>
    <td style="width:60%;">
    <input type="text" id="BillTonamesearch" class="form-control" onkeyup="BillToNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="BillToTable2" class="display nowrap table  table-striped table-bordered" width="100%">
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
  <div class="modal-dialog modal-md " style="width:60%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='ShipToclosePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Ship To</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="ShipToTable" class="display nowrap table  table-striped table-bordered" style="width:100%;">
    <thead>
    <tr>
            <th style="width:10%;">Select</th> 
            <th style="width:30%;">Name</th>
            <th style="width:60%;">Address</th>
    </tr>
    </thead>
    <tbody>
    <tr>
    <th style="text-align:center; width:10%;">&#10004;</th>
   <td style="width:30%;"> 
    <input type="text" id="ShipTocodesearch" class="form-control" onkeyup="ShipToCodeFunction()">
    </td>
    <td style="width:60%;">
    <input type="text" id="ShipTonamesearch" class="form-control" onkeyup="ShipToNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="ShipToTable2" class="display nowrap table  table-striped table-bordered" width="100%">
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
  <div class="modal-dialog modal-md" style="width:60%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='TNCID_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>T&C</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="TNCIDTable" class="display nowrap table  table-striped table-bordered"  style="width:100%;" >
    <thead>
    <tr>
    <th style="width:10%;">Select</th> 
    <th style="width:30%;">Code</th>
    <th style="width:60%;">Name</th>
    </tr>
    </thead>
    <tbody>
    <tr>
   
<th style="text-align:center; width:10%;">&#10004;</th>
   <td style="width:30%;"> 
    <input type="text" id="TNCcodesearch" class="form-control" onkeyup="TNCCodeFunction()">
    </td>
    <td style="width:60%;">
    <input type="text" id="TNCnamesearch" class="form-control" onkeyup="TNCNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="TNCIDTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
         
        </thead>
        <tbody>
        @foreach ($objTNCHeader as $tncindex=>$tncRow)
        <tr >
        <td style="text-align:center; width:10%"> <input type="checkbox" name="tnc[]" id="tncidcode_{{ $tncindex }}" class="clstncid" value="{{ $tncRow-> TNCID }}" ></td>
          <td style="width:30%">{{ $tncRow-> TNC_CODE }}
          <input type="hidden" id="txttncidcode_{{ $tncindex }}" data-desc="{{ $tncRow-> TNC_CODE }} - {{ $tncRow-> TNC_DESC }}"  
          value="{{ $tncRow-> TNCID }}"/></td><td style="width:60%">{{ $tncRow-> TNC_DESC }}</td>
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
  <div class="modal-dialog modal-md" style="width:60%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='CTID_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Calculation Template</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="CTIDTable" class="display nowrap table  table-striped table-bordered" style="width:100%;">
    <thead>
    <tr>
            <th style="width:10%;">Select</th> 
            <th style="width:30%;">Code</th>
            <th style="width:60%;">Name</th>
    </tr>
    </thead>
    <tbody>
    <tr>
    <th style="text-align:center; width:10%;">&#10004;</th>
   <td style="width:30%;"> 
    <input type="text" id="CTIDcodesearch" class="form-control" onkeyup="CTIDCodeFunction()">
    </td>
    <td style="width:60%;">
    <input type="text" id="CTIDnamesearch" class="form-control" onkeyup="CTIDNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="CTIDTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
         
        </thead>
        <tbody>
        @foreach ($objCalculationHeader as $calindex=>$calRow)
        <tr >
        <td style="text-align:center; width:10%"> <input type="checkbox" name="calculationtemplate[]" id="CTIDcode_{{ $calindex }}" class="clsctid" value="{{ $calRow-> CTID }}" ></td>
          <td style="width:30%;">{{ $calRow-> CTCODE }}
          <input type="hidden" id="txtCTIDcode_{{ $calindex }}" data-desc="{{ $calRow-> CTCODE }} - {{ $calRow-> CTDESCRIPTION }}"  
          value="{{ $calRow-> CTID }}"/></td><td style="width:60%;">{{ $calRow-> CTDESCRIPTION }}</td>
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

<!-- Alert -->
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

<!-- Sales Person Dropdown -->
<div id="SPIDpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:60%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='SPID_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Sales Person</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="SalesPersonTable" class="display nowrap table  table-striped table-bordered" style="width:100%;">
    <thead>
    <tr>
            <th style="width:10%;"></th>
            <th style="width:30%;">Code</th>
            <th style="width:60%;">Name</th>
    </tr>
    </thead>
    <tbody>
    <tr>
    <th style="text-align:center; width:10%;">&#10004;</th>
   <td style="width:30%;"> 
    <input type="text" id="SalesPersoncodesearch" class="form-control" onkeyup="SalesPersonCodeFunction()">
    </td>
    <td style="width:60%;">
    <input type="text" id="SalesPersonnamesearch" class="form-control" onkeyup="SalesPersonNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="SalesPersonTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          
        </thead>
        <tbody >     
        @foreach ($objSalesPerson as $spindex=>$spRow)
        <tr >
        <td style="text-align:center; width:10%"> <input type="checkbox" name="salesperson[]" id="spidcode_{{ $spindex }}" class="clsspid"  value="{{ $spRow-> EMPID }}" ></td>



          <td style="width:30%">{{ $spRow-> EMPCODE }}
          <input type="hidden" id="txtspidcode_{{ $spindex }}" data-desc="{{ $spRow-> EMPCODE }} - {{ $spRow-> FNAME }} {{ $spRow-> MNAME }} {{ $spRow-> LNAME }} "  value="{{ $spRow-> EMPID }}"/>
          </td>
          <td style="width:60%">{{ $spRow-> FNAME }} {{ $spRow-> MNAME }} {{ $spRow-> LNAME }}</td>
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
<!-- Sales Person Dropdown-->

<!-- Currency Dropdown -->
<div id="cridpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:60%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='crid_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Currency</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="CurrencyTable" class="display nowrap table  table-striped table-bordered" style="width:100%;">
    <thead>
    <tr>
            <th style="width:10%;">Select</th> 
            <th style="width:30%;">Code</th>
            <th style="width:60%;">Description</th>
    </tr>
    </thead>
    <tbody>
    <tr>
    <th style="text-align:center; width:10%;">&#10004;</th>
   <td style="width:30%;"> 
    <input type="text" id="currencycodesearch" class="form-control" onkeyup="CurrencyCodeFunction()">
    </td>
    <td style="width:60%;">
    <input type="text" id="currencynamesearch" class="form-control" onkeyup="CurrencyNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="CurrencyTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          <!-- <tr>
            <th>GLCode</th>
            <th>GLName</th>
          </tr> -->
          
        </thead>
        <tbody>
        @foreach ($objothcurrency as $crindex=>$crRow)
        <tr >
        <td style="text-align:center; width:10%"> <input type="checkbox" name="currencytype[]" id="cridcode_{{ $crindex }}" class="clscrid" value="{{ $crRow-> CRID }}" ></td>
          <td style="width:30%">{{ $crRow-> CRCODE }}
          <input type="hidden" id="txtcridcode_{{ $crindex }}" data-desc="{{ $crRow-> CRCODE }}-{{ $crRow-> CRDESCRIPTION }}"  value="{{ $crRow-> CRID }}"/>
          </td>
          <td style="width:60%">{{ $crRow-> CRDESCRIPTION }}</td>
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
<!-- Currency Dropdown-->


<!-- Sales Enquiry Dropdown -->
<div id="SEpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:60%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='SEclosePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Sales Enquiry</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="SalesEnquiryTable" class="display nowrap table  table-striped table-bordered"style="width:100%;" >
    <thead>
          <tr id="none-select" class="searchalldata" hidden>
            
            <td> <input type="hidden" name="fieldid" id="hdn_seid"/>
            <input type="hidden" name="fieldid2" id="hdn_seid2"/></td>
          </tr>
    <tr>
            <th style="width:10%;">Select</th> 
            <th style="width:30%;">Enquiry No</th>
            <th style="width:60%;">Enquiry Date</th>
    </tr>
    </thead>
    <tbody>
    <tr>
    <th style="text-align:center; width:10%;">&#10004;</th>
   <td style="width:30%;"> 
    <input type="text" id="SalesEnquirycodesearch" class="form-control" onkeyup="SalesEnquiryCodeFunction()">
    </td>
    <td style="width:60%;" >
    <input type="text" id="SalesEnquirynamesearch" class="form-control" onkeyup="SalesEnquiryNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="SalesEnquiryTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
        </thead>
        <tbody id="tbody_SE">     
        
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
            <input type="hidden" name="hdn_ItemID21" id="hdn_ItemID21" value="0"/>
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
        <td style="width:10%;"><input type="text" id="Itemcodesearch" class="form-control" onkeyup="ItemCodeFunction('{{$FormId}}')"></td>
        <td style="width:10%;"><input type="text" id="Itemnamesearch" class="form-control" onkeyup="ItemNameFunction('{{$FormId}}')"></td>
        <td style="width:8%;"><input type="text" id="ItemUOMsearch" class="form-control" onkeyup="ItemUOMFunction('{{$FormId}}')"></td>
        <td style="width:8%;"><input type="text" id="ItemQTYsearch" class="form-control" onkeyup="ItemQTYFunction()"></td>
        <td style="width:8%;"><input type="text" id="ItemGroupsearch" class="form-control" onkeyup="ItemGroupFunction('{{$FormId}}')"></td>
        <td style="width:8%;"><input type="text" id="ItemCategorysearch" class="form-control" onkeyup="ItemCategoryFunction('{{$FormId}}')"></td>
        <td style="width:8%;"><input type="text" id="ItemBUsearch" class="form-control" onkeyup="ItemBUFunction('{{$FormId}}')"></td>
        <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemAPNsearch" class="form-control" onkeyup="ItemAPNFunction('{{$FormId}}')"></td>
        <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemCPNsearch" class="form-control" onkeyup="ItemCPNFunction('{{$FormId}}')"></td>
        <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemOEMPNsearch" class="form-control" onkeyup="ItemOEMPNFunction('{{$FormId}}')"></td>
        <td style="width:8%;"><input type="text" id="ItemStatussearch" class="form-control" onkeyup="ItemStatusFunction()"></td>
      </tr>                    
    </tbody>
    </table>
      <table id="ItemIDTable2" class="display nowrap table  table-striped table-bordered" style="width:100%;"  >
        <thead id="thead2">

        </thead>
        <tbody id="tbody_ItemID" >     
          
          
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
    <table id="altuomTable" class="display nowrap table  table-striped table-bordered" >
    <thead>
    <tr id="none-select" class="searchalldata" hidden>
            
            <td> <input type="hidden" name="fieldid" id="hdn_altuom"/>
            <input type="hidden" name="fieldid2" id="hdn_altuom2"/>
            <input type="hidden" name="fieldid3" id="hdn_altuom3"/>
            <input type="hidden" name="fieldid4" id="hdn_altuom4"/></td>
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
        <td class="ROW2"><input type="text" id="altuomcodesearch" class="form-control" onkeyup="altuomCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="altuomnamesearch" class="form-control" onkeyup="altuomNameFunction()"></td>
      </tr>

    </tbody>
    </table>
      <table id="altuomTable2" class="display nowrap table  table-striped table-bordered" width="100%">
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

<!-- UDF Dropdown -->
<div id="UDFSQIDpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='UDFSQID_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>UDF Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="UDFSQIDTable" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
    <tr>
            <th>Label</th>
            <th>Value Type</th>
    </tr>
    <tr hidden>
            <input type="hidden" name="fieldid" id="hdn_UDFSQID"/>
    </tr>
    </thead>
    <tbody>
    <tr>
    <td>
    <input type="text" id="UDFSQIDcodesearch" onkeyup="UDFSQIDCodeFunction()">
    </td>
    <td>
    <input type="text" id="UDFSQIDnamesearch" onkeyup="UDFSQIDNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="UDFSQIDTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
        </thead>
        <tbody id="tbody_UDFSQID"> 
        @foreach ($objUdfSQData as $udfindex=>$udfRow)
        <tr id="UDFSQID_{{ $udfindex }}" class="clsudfsqid">
          <td width="50%">{{ $udfRow->LABEL }}
          <input type="hidden" id="txtudfsqid_{{ $udfindex }}" data-desc="{{ $udfRow->LABEL }}"  value="{{ $udfRow->UDFSQID }}"/>
          </td>
          <td id="udfvalue_{{ $udfindex }}">{{ $udfRow-> VALUETYPE }}
          <input type="hidden" id="txtudfvalue_{{ $udfindex }}" data-desc="{{ $udfRow->DESCRIPTIONS }}"  
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




<!-- Lead Popup starts here   -->
<div id="Lead_popup" class="modal" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-md" style="width:60%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" id="Lead_closePopup">&times;</button>
            </div>
            <div class="modal-body">
                <div class="tablename"><p>Lead List</p></div>
                <div class="single single-select table-responsive table-wrapper-scroll-y my-custom-scrollbar">
                <input type="hidden" class="mainitem_tab1">
                    <table id="LeadOrder" class="display nowrap table table-striped table-bordered" style="width:100%;">
                        <thead>
                            <tr>
                            <th style="width:10%;">Select</th> 
                                <th style="width:40%;"> Doc No</th>
                                <th style="width:40%;"> Doc Date</th>
             
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                            <th style="text-align:center; width:10%;">&#10004;</th>  
                                <td style="width:30%;"> 
                                    <input type="text" id="LeadNo" class="form-control" onkeyup="LeadDocFunction()"  />
                                </td>
                                <td style="width:30%;">
                                    <input type="text" id="LeadDT" class="form-control" onkeyup="LeadDTFunction()"  />
                                </td>
                      
                          
                            </tr>
                        </tbody>
                    </table>
                    <table id="LeadOrderTable2" class="display nowrap table table-striped table-bordered">
                        <thead id="thead2">
                        <tr>
        
                <td id="Data_seach" colspan="4">please wait...</td>
          </tr>
                        </thead>
                        <tbody id="Dataresult">
                           
                        </tbody>
                    </table>
                </div>
                <div class="cl"></div>
            </div>
        </div>
    </div>
</div>


@endsection



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

let udftid = "#UDFSQIDTable2";
      let udftid2 = "#UDFSQIDTable";
      let udfheaders = document.querySelectorAll(udftid2 + " th");

      // Sort the table element when clicking on the table headers
      udfheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(udftid, ".clsUDFSQID", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function UDFSQIDCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("UDFSQIDcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("UDFSQIDTable2");
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

  function UDFSQIDNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("UDFSQIDnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("UDFSQIDTable2");
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


$("#UDFSQID_closePopup").on("click",function(event){ 
     $("#UDFSQIDpopup").hide();
});

$('.clsUDFSQID').dblclick(function(){
    
        var id = $(this).attr('id');
        var txtid =    $("#txt"+id+"").val();
        var txtname =   $("#txt"+id+"").data("desc");
        var fieldid2 = $(this).find('[id*="udfvalue"]').attr('id');
        var txtvaluetype = $.trim($(this).find('[id*="udfvalue"]').text());
        var txtismandatory =  $("#txt"+fieldid2+"").val();
        var txtdescription =  $("#txt"+fieldid2+"").data("desc");
        
        var txtcol = $('#hdn_UDFSQID').val();
        $("#"+txtcol).val(txtname);
        $("#"+txtcol).parent().parent().find("[id*='UDFSQID_REF']").val(txtid);
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

        $("#UDFSQIDpopup").hide();
        $("#UDFSQIDcodesearch").val(''); 
        $("#UDFSQIDnamesearch").val(''); 
    
        event.preventDefault();
            
 });
 
//UDF Tab Ends
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
    showSelectedCheck($("#TNCID_REF").val(),"tnc");
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
        // var txtid= $('#hdn_fieldid').val();
        // var txt_id2= $('#hdn_fieldid2').val();
        
        $('#txtTNCID_popup').val(texdesc);
        $('#TNCID_REF').val(txtval);
        $("#TNCIDpopup").hide();
        $("#TNCcodesearch").val(''); 
        $("#TNCnamesearch").val(''); 
    
        //sub GL
        var customid = txtval;
        if(customid!=''){
          
          $('#tbody_tncdetails').html('<tr><td colspan="2">Please wait..</td></tr>');
          // $('#tncbody').html('<tr><td colspan="2">Please wait..</td></tr>');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url:'{{route("transaction",[36,"gettncdetails2"])}}',
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
                url:'{{route("transaction",[36,"gettncdetails3"])}}',
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
                url:'{{route("transaction",[36,"gettncdetails"])}}',
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

              strinp = '<input type="date" placeholder="dd/mm/yyyy" name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" autocomplete="off" /> ';       

            }else if(chkvaltype=='time'){
              strinp= '<input type="time" placeholder="h:i" name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" autocomplete="off" /> ';

            }else if(chkvaltype=='numeric'){
              strinp = '<input type="text" name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" autocomplete="off" /> ';

            }else if(chkvaltype=='text'){

              strinp = '<input type="text" name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" autocomplete="off" /> ';
            
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
    showSelectedCheck($("#CTID_REF").val(),"calculationtemplate");
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
                url:'{{route("transaction",[36,"getcalculationdetails2"])}}',
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
                url:'{{route("transaction",[36,"getcalculationdetails3"])}}',
                type:'POST',
                data:{'id':customid},
                success:function(data) {
                  $('#Row_Count4').val(data);
                    bindCTIDDetailsEvents();
                },
                error:function(data){
                  console.log("Error: Something went wrong.");
                  $('#Row_Count4').val('0');
                },
            });
            $.ajax({
                url:'{{route("transaction",[36,"getcalculationdetails"])}}',
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
                      var TaxableAmount = $(this).find('[id*="DISAFTT_AMT"]').val();
                      if (!isNaN(TaxableAmount) && TaxableAmount.length !== 0) {
                        netTaxableAmount += parseFloat(TaxableAmount);
                        }                      
                      
                      var GSTAmount = $(this).find('[id*="TGST_AMT"]').val();
                      if (!isNaN(GSTAmount) && GSTAmount.length !== 0) {
                        netGSTAmount += parseFloat(GSTAmount);
                        }
                      
                      var TotalAmount = $(this).find('[id*="TOT_AMT"]').val();
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
                $('#Material').find('.participantRow').each(function()
                {
                  tvalue = $(this).find('[id*="TOT_AMT"]').val();
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
                $('#TotalValue').val(totalvalue);
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
            txtamount = parseFloat(txtamount).toFixed(2);
            if(intRegex.test(txtrate)){
              txtrate = (txtrate +'.0000');
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
              if($.trim($('#Tax_State').val())=="OutofState")
              {              
              $("#"+txtcol).parent().parent().find("[id*='calIGST']").removeAttr('readonly');
              }
              else
              {
              $("#"+txtcol).parent().parent().find("[id*='calCGST']").removeAttr('readonly');
              $("#"+txtcol).parent().parent().find("[id*='calSGST']").removeAttr('readonly');
              }
            }     
            else
            {
              $("#"+txtcol).parent().parent().find("[id*='calGST']").removeAttr('checked');
            } 

            var totaltaxableamount = 0;
            $('#Material').find('.participantRow').each(function()
              {
                var amount1 = $(this).find('[id*="DISAFTT_AMT"]').val();

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
        
            event.preventDefault();
            
        });
  }
//Calculation Details Ends
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

  
    $('#txtgl_popup').click(function(event){
    var QUOTATIONTYPE = $("input[name='LEAD']:checked").val();
    if(QUOTATIONTYPE == "WITH_LEAD")
    {
      return false;
    }

    var CODE = ''; 
    var NAME = ''; 
    var FORMID = "{{$FormId}}";
    loadCustomer(CODE,NAME,FORMID);
    $("#customer_popus").show();
    event.preventDefault();
  });

  $("#customer_closePopup").click(function(event){
    $("#customer_popus").hide();
    $("#customercodesearch").val(''); 
    $("#customernamesearch").val(''); 
  
    event.preventDefault();
  });

  function bindSubLedgerEvents(){ 
      $(".clssubgl").click(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");
        var glid_ref =   $("#txt"+fieldid+"").data("desc2");
        var id = $(this).attr('id');
            var txtval =    $("#txt"+id+"").val();
            var texdesc =   $("#txt"+id+"").data("desc");
            var oldSLID =   $("#SLID_REF").val();
            var MaterialClone =  $('#hdnmaterial').val();
            $('#txtgl_popup').val(texdesc);
            $("#txtgl_popup").blur();
            $('#SLID_REF').val(txtval);
            $('#GLID_REF').val(glid_ref);
            if (txtval != oldSLID)
            { 
              $('#Material').html(MaterialClone);
              $('#TotalValue').val('0.00');
              var count11 = <?php echo json_encode($objCount1); ?>;
              $('#Row_Count1').val(count11);
              $('#Material').find('.participantRow').each(function(){
                $(this).find('input:text').val('');
                $(this).find('input:hidden').val('');
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
       
         
            var customid = txtval;
              if(customid!=''){
                $("#CREDITDAYS").val('');
                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  });
                  $.ajax({
                      url:'{{route("transaction",[36,"getcreditdays"])}}',
                      type:'POST',
                      data:{'id':customid},
                      success:function(data) {
                        $("#CREDITDAYS").val(data);                        
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                        $("#CREDITDAYS").val('');                        
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
                      url:'{{route("transaction",[36,"getBillTo"])}}',
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
                  $("#tbody_BillTo").html('');
                  $.ajax({
                      url:'{{route("transaction",[36,"getBillAddress"])}}',
                      type:'POST',
                      data:{'id':customid},
                      success:function(data) {
                        $("#tbody_BillTo").html(data);
                        BindBillAddress();
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                        $("#tbody_BillTo").html('');
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
                      url:'{{route("transaction",[36,"getShipTo"])}}',
                      type:'POST',
                      data:{'id':customid},
                      success:function(data) {
                        $("#div_shipto").html(data);
                        
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                        $("#txtSHIPTO").hide();
                        $("#txtSHIPTO1").show();
                      },
                  });
                  $("#tbody_ShipTo").html('');
                  $.ajax({
                      url:'{{route("transaction",[36,"getShipAddress"])}}',
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
                  $("#tbody_SE").html('');
                  $.ajax({
                      url:'{{route("transaction",[36,"getsalesenquiry"])}}',
                      type:'POST',
                      data:{'id':customid},
                      success:function(data) {
                        $("#tbody_SE").html(data);
                        BindSalesEnquiry();
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                        $("#tbody_SE").html('');
                      },
                  });       
              }
              event.preventDefault();
      });
  }
      

  //customer list  Ends
//------------------------


//------------------------

//------------------------
  //Sales Person Dropdown
  let sptid = "#SalesPersonTable2";
      let sptid2 = "#SalesPersonTable";
      let salespersonheaders = document.querySelectorAll(sptid2 + " th");

      // Sort the table element when clicking on the table headers
      salespersonheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(sptid, ".clsspid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function SalesPersonCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("SalesPersoncodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("SalesPersonTable2");
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

  function SalesPersonNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("SalesPersonnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("SalesPersonTable2");
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

  $('#txtSPID_popup').click(function(event){
    showSelectedCheck($("#SPID_REF").val(),"salesperson");
         $("#SPIDpopup").show();
      });

      $("#SPID_closePopup").click(function(event){
        $("#SPIDpopup").hide();
      });

      $(".clsspid").click(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");
        
        $('#txtSPID_popup').val(texdesc);
        $('#SPID_REF').val(txtval);
        $("#SPIDpopup").hide();
        
        $("#SalesPersoncodesearch").val(''); 
        $("#SalesPersonnamesearch").val(''); 
      
        event.preventDefault();
      });

      

  //Sales Person Dropdown Ends
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

    var QUOTATIONTYPE = $("#QUOTATIONTYPE").val();
    if(QUOTATIONTYPE.toUpperCase() == "CUSTOMER" || QUOTATIONTYPE.toUpperCase() == "PROSPECT")
    {
      return false;
    }

      var customid = $("#SLID_REF").val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $("#tbody_BillTo").html('');

        $.ajax({
            url:'{{route("transaction",[36,"getBillAddress"])}}',
            type:'POST',
            data:{'id':customid},
            success:function(data) {
              $("#tbody_BillTo").html(data);
              BindBillAddress();
            },
            error:function(data){
              console.log("Error: Something went wrong.");
              $("#tbody_BillTo").html('');
            },
        }); 


        showSelectedCheck($("#BILLTO").val(),"billto");
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

        var QUOTATIONTYPE = $("#QUOTATIONTYPE").val();
        if(QUOTATIONTYPE.toUpperCase() == "CUSTOMER" || QUOTATIONTYPE.toUpperCase() == "PROSPECT")
        {
          return false;
        }

        var customid = $("#SLID_REF").val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $("#tbody_ShipTo").html('');
        $.ajax({
            url:'{{route("transaction",[36,"getShipAddress"])}}',
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



        showSelectedCheck($("#SHIPTO").val(),"shipto");
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
          var texdesc =   $("#txt"+fieldid+"").data("desc2");
          var taxstate =  $("#txt"+fieldid+"").data("desc");
          var oldShipto =   $("#SHIPTO").val();
          var MaterialClone = $('#hdnmaterial').val();

          
         
          if (txtval != oldShipto)
          {
            $('#Material').html(MaterialClone);
              $('#TotalValue').val('0.00');
              var count11 = <?php echo json_encode($objCount1); ?>;
              $('#Row_Count1').val(count11);
              $('#Material').find('.participantRow').each(function(){
                $(this).find('input:text').val('');
                $(this).find('input:hidden').val('');
                var rowcount = $('#Row_Count1').val();
                if(rowcount > 1)
                {
                  $(this).closest('.participantRow').remove();
                  rowcount = parseInt(rowcount) - 1;
                  $('#Row_Count1').val(rowcount);
                }
              });
              
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
  //Currency Dropdown
  let crtid = "#CurrencyTable2";
      let crtid2 = "#CurrencyTable";
      let currencyheaders = document.querySelectorAll(crtid2 + " th");

      // Sort the table element when clicking on the table headers
      currencyheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(crtid, ".clscrid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function CurrencyCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("currencycodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("CurrencyTable2");
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

  function CurrencyNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("currencynamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("CurrencyTable2");
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

  $('#txtCRID_popup').click(function(event){
    showSelectedCheck($("#CRID_REF").val(),"currencytype");
         $("#cridpopup").show();
      });

      $("#crid_closePopup").click(function(event){
        $("#cridpopup").hide();
      });

      $(".clscrid").click(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");
        
        $('#txtCRID_popup').val(texdesc);
        $('#CRID_REF').val(txtval);
        $("#cridpopup").hide();
        var fcurrency = txtval;
        var dcurrency = <?php echo json_encode($objcurrency); ?>;
       // alert(dcurrency);
        var cconverter = <?php echo json_encode($objCurrencyconverter); ?>;
        $.each( cconverter, function( cckey, ccvalue ) {
          var fromdate = ccvalue.EFFDATE;
          var enddate = ccvalue.ENDDATE;
          var d = new Date(); 
         // var today = d.getFullYear() + "-" + (d.getMonth()+1) + "-" + d.getDate();
          var today = new Date().toJSON().slice(0,10).replace(/-/g,'-');
          //alert(enddate); 
            
            if (fcurrency == ccvalue.TOCRID_REF && dcurrency == ccvalue.FROMCRID_REF && fromdate <= today && enddate >= today)
            {
              //alert(ccvalue.FRAMOUNT);
              $('#CONVFACT').val(ccvalue.TOAMOUNT);
              $('#CONVFACT').prop('readonly','true');
              $('#CONVFACT').removeAttr('disabled');
              return false;
            }
            else
            {
              $('#CONVFACT').val('');
              $('#CONVFACT').removeAttr('readonly');
              $('#CONVFACT').prop('disabled','true');
        
            }
          });
        $("#currencycodesearch").val(''); 
        $("#currencynamesearch").val(''); 
     
        event.preventDefault();
      });

      

  //Currency Dropdown Ends
//------------------------

//------------------------
  //Sales Enquiry Dropdown
      let setid = "#SalesEnquiryTable2";
      let setid2 = "#SalesEnquiryTable";
      let salesenquiryheaders = document.querySelectorAll(setid2 + " th");

      // Sort the table element when clicking on the table headers
      salesenquiryheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(setid, ".clsseid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function SalesEnquiryCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("SalesEnquirycodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("SalesEnquiryTable2");
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

  function SalesEnquiryNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("SalesEnquirynamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("SalesEnquiryTable2");
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

  $('#Material').on('click','[id*="txtSE_popup"]',function(event){

        var customid = $('#SLID_REF').val();

        $("#tbody_SE").html('');
        $.ajax({
            url:'{{route("transaction",[36,"getsalesenquiry"])}}',
            type:'POST',
            data:{'id':customid},
            success:function(data) {
              $("#tbody_SE").html(data);
              BindSalesEnquiry();
            },
            error:function(data){
              console.log("Error: Something went wrong.");
              $("#tbody_SE").html('');
            },
        }); 

    //To check the selected one
    var index_id=$(this).attr('id').split('_')[2]
    var salesenqid='#SEQID_REF_'+index_id;
    var VALUE=$(salesenqid).val(); 
    showSelectedCheck(VALUE,"salesenquiry");



        $("#SEpopup").show();
        var id = $(this).attr('id');
        var id2 = $(this).parent().parent().find('[id*="SEQID_REF"]').attr('id');

        $('#hdn_seid').val(id);
        $('#hdn_seid2').val(id2);
      });

      $("#SEclosePopup").click(function(event){
        $("#SEpopup").hide();
      });

    function BindSalesEnquiry(){
      $(".clsseid").click(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");
        
        var txtid= $('#hdn_seid').val();
        var txt_id2= $('#hdn_seid2').val();

        $('#'+txtid).val(texdesc);
        $('#'+txt_id2).val(txtval);
        $("#SEpopup").hide();
        
        $("#SalesEnquirycodesearch").val(''); 
        $("#SalesEnquirynamesearch").val(''); 
    
        event.preventDefault();
      });
    }

      

  //Sales Quotation Dropdown Ends
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

function ItemCodeFunction(FORMID) {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("Itemcodesearch");
  filter = input.value.toUpperCase();

  if(filter.length == 0 && $("#DirectSQ").is(":checked") == true )
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
    var FORMID = "{{$FormId}}";
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID); 
  }
  else if(filter.length >= 3 && $("#DirectSQ").is(":checked") == true )
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
    var FORMID = "{{$FormId}}";
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

  if(filter.length == 0 && $("#DirectSQ").is(":checked") == true )
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
    var FORMID = "{{$FormId}}";
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID); 
  }
  else if(filter.length >= 3 && $("#DirectSQ").is(":checked") == true )
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
    var FORMID = "{{$FormId}}";
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

  if(filter.length == 0 && $("#DirectSQ").is(":checked") == true )
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
    var FORMID = "{{$FormId}}";
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID); 
  }
  else if(filter.length >= 3 && $("#DirectSQ").is(":checked") == true )
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
    var FORMID = "{{$FormId}}";
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
  if(filter.length == 0 && $("#DirectSQ").is(":checked") == true )
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
    var FORMID = "{{$FormId}}";
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID); 
  }
  else if(filter.length >= 3 && $("#DirectSQ").is(":checked") == true )
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
    var FORMID = "{{$FormId}}";
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
  if(filter.length == 0 && $("#DirectSQ").is(":checked") == true )
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
    var FORMID = "{{$FormId}}";
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID); 
  }
  else if(filter.length >= 3 && $("#DirectSQ").is(":checked") == true )
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
    var FORMID = "{{$FormId}}";
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
if(filter.length == 0 && $("#DirectSQ").is(":checked") == true )
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
    var FORMID = "{{$FormId}}";
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID); 
  }
  else if(filter.length >= 3 && $("#DirectSQ").is(":checked") == true )
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
    var FORMID = "{{$FormId}}";
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
  if(filter.length == 0 && $("#DirectSQ").is(":checked") == true )
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
    var FORMID = "{{$FormId}}";
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID); 
  }
  else if(filter.length >= 3 && $("#DirectSQ").is(":checked") == true )
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
    var FORMID = "{{$FormId}}";
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
  if(filter.length == 0 && $("#DirectSQ").is(":checked") == true )
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
    var FORMID = "{{$FormId}}";
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID); 
  }
  else if(filter.length >= 3 && $("#DirectSQ").is(":checked") == true )
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
    var FORMID = "{{$FormId}}";
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
  if(filter.length == 0 && $("#DirectSQ").is(":checked") == true )
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
    var FORMID = "{{$FormId}}";
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID);
  }
  else if(filter.length >= 3 && $("#DirectSQ").is(":checked") == true )
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
    var FORMID = "{{$FormId}}";
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
    
		$("#tbody_ItemID").html('');
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
    var SQDT=$("#SQDT").val(); 
		$.ajax({
          url:'{{route("transaction",[36,"getItemDetails"])}}',
          type:'POST',
          data:{'taxstate':taxstate,'CODE':CODE,'NAME':NAME,'MUOM':MUOM,'GROUP':GROUP,'CTGRY':CTGRY,'BUNIT':BUNIT,'APART':APART,'CPART':CPART,'OPART':OPART,'SQDT':SQDT},
          success:function(data) {
              $('.js-selectall').prop("disabled", true);  
              $("#tbody_ItemID").html(data);  
              bindItemEvents();
              event.preventDefault(); 
          },
          error:function(data){
            console.log("Error: Something went wrong.");
            $("#tbody_ItemID").html(''); 
            event.preventDefault();                       
			    },
		});

    event.preventDefault();
}
  //Item POPUP
//------------------------

      

  $('#Material').on('click','[id*="popupITEMID"]',function(event){

        var SalesEnquiryID = $(this).parent().parent().find('[id*="txtSE_popup"]').val();
        var se_text_id = $(this).parent().parent().find('[id*="txtSE_popup"]').attr('id');
        var taxstate = $('#Tax_State').val();
        var SLID_REF = $('#SLID_REF').val();
        var QUOTATIONTYPE = $("input[name='LEAD']:checked").val();
        if(SalesEnquiryID!='' && QUOTATIONTYPE=="WITHOUT_LEAD"){
                $("#tbody_ItemID").html('');
                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  });
                  var SQDT=$("#SQDT").val(); 
                  $.ajax({
                      url:'{{route("transaction",[36,"getItemDetailsEnquirywise"])}}',
                      type:'POST',
                      data:{'id':SalesEnquiryID, 'taxstate':taxstate,'SLID': SLID_REF,'SQDT':SQDT},
                      success:function(data) {
                        $("#tbody_ItemID").html(data);   
                        bindItemEvents();      
                        event.preventDefault();               
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                        $("#tbody_ItemID").html('');  
                        event.preventDefault();                      
                      },
                  }); 

              $("#ITEMIDpopup").show();
        }
        else
        {

          if($("#DirectSQ").is(":checked") == true &&  QUOTATIONTYPE=="WITHOUT_LEAD") {
                
            var CODE = ''; 
            var NAME = ''; 
            var MUOM = ''; 
            var GROUP = ''; 
            var CTGRY = ''; 
            var BUNIT = ''; 
            var APART = ''; 
            var CPART = ''; 
            var OPART = ''; 
            $("#tbody_ItemID").html('');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url:'{{route("transaction",[36,"getItemDetails"])}}',
                type:'POST',
                data:{'taxstate':taxstate,'CODE':CODE,'NAME':NAME,'MUOM':MUOM,'GROUP':GROUP,'CTGRY':CTGRY,'BUNIT':BUNIT,'APART':APART,'CPART':CPART,'OPART':OPART},
                success:function(data) {
                  $('.js-selectall').prop("disabled", true);  
                  $("#tbody_ItemID").html(data);                   
                  bindItemEvents();  
                  event.preventDefault();                  
                },
                error:function(data){
                  console.log("Error: Something went wrong.");
                  $("#tbody_ItemID").html('');   
                  event.preventDefault();                     
                },
            });
            $("#ITEMIDpopup").show();
          }
          else if(QUOTATIONTYPE=="WITHOUT_LEAD"){
            $("#FocusId").val(se_text_id);
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text('Please select SE No.');
            $("#alert").modal('show');
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk1');
          }
        }
        if(QUOTATIONTYPE=="WITH_LEAD")
        {
            var CODE = ''; 
            var NAME = ''; 
            var MUOM = ''; 
            var GROUP = ''; 
            var CTGRY = ''; 
            var BUNIT = ''; 
            var APART = ''; 
            var CPART = ''; 
            var OPART = ''; 
            var LEADID_REF = $("#LEAD_REF").val(); 
            if(LEADID_REF==''){ 
              $("#FocusId").val('txtLeadpopup');
              $("#ProceedBtn").focus();
              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn1").show();
              $("#AlertMessage").text('Please Select Lead No First.');
              $("#alert").modal('show');
              $("#OkBtn1").focus();
              return false;
            }
            $("#tbody_ItemID").html('');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var SQDT=$("#SQDT").val(); 
            $.ajax({
                url:'{{route("transaction",[36,"getItemDetails_lead"])}}',
                type:'POST',
                data:{'taxstate':taxstate,'CODE':CODE,'NAME':NAME,'MUOM':MUOM,'GROUP':GROUP,'CTGRY':CTGRY,'BUNIT':BUNIT,'APART':APART,'CPART':CPART,'OPART':OPART,'LEADID_REF':LEADID_REF,'SQDT':SQDT},
                success:function(data) {
                  $('.js-selectall').prop("disabled", true);  
                  $("#tbody_ItemID").html(data);                   
                  bindItemEvents();  
                  event.preventDefault();                  
                },
                error:function(data){
                  console.log("Error: Something went wrong.");
                  $("#tbody_ItemID").html('');   
                  event.preventDefault();                     
                },
            });
            $("#ITEMIDpopup").show();
        }

        
        var id = $(this).attr('id');
        var id2 = $(this).parent().parent().find('[id*="ITEMID_REF"]').attr('id');
        var id3 = $(this).parent().parent().find('[id*="ItemName"]').attr('id');
        var id4 = $(this).parent().parent().find('[id*="SEMUOM"]').attr('id');
        var id5 = $(this).parent().parent().find('[id*="SEMUOMQTY"]').attr('id');
        var id6 = $(this).parent().parent().find('[id*="SEAUOM"]').attr('id');
        var id7 = $(this).parent().parent().find('[id*="SEAUOMQTY"]').attr('id');
        var id8 = $(this).parent().parent().find('[id*="popupMUOM"]').attr('id');
        var id9 = $(this).parent().parent().find('[id*="MAIN_UOMID_REF"]').attr('id');
        var id10 = $(this).parent().parent().find('[id*="SQ_QTY"]').attr('id');
        var id11 = $(this).parent().parent().find('[id*="popupAUOM"]').attr('id');
        var id12 = $(this).parent().parent().find('[id*="ALT_UOMID_REF"]').attr('id');
        var id13 = $(this).parent().parent().find('[id*="ALT_UOMID_QTY"]').attr('id');
        var id14 = $(this).parent().parent().find('[id*="RATEPUOM"]').attr('id');
        var id15 = $(this).parent().parent().find('[id*="SQ_FQTY"]').attr('id');

        

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
        $('#hdn_ItemID16').val(SalesEnquiryID);
        var SalesEnq = [];
        $('#Material').find('.participantRow').each(function(){
          if($(this).find('[id*="ITEMID_REF"]').val() != '')
          {
            SalesEnq.push($(this).find('[id*="txtSE_popup"]').val());
          }
        });
        $('#hdn_ItemID17').val(SalesEnq.join(', '));
        var ItemID = [];
        $('#Material').find('.participantRow').each(function(){
          if($(this).find('[id*="ITEMID_REF"]').val() != '')
          {
            ItemID.push($(this).find('[id*="ITEMID_REF"]').val());
          }
        });
        $('#hdn_ItemID18').val(ItemID.join(', '));
        event.preventDefault();
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
          var texdesc =  $("#txt"+fieldid+"").data("desc");
          var fieldid2 = $(this).find('[id*="itemname"]').attr('id');
          var txtname =  $("#txt"+fieldid2+"").val();
          var fieldid3 = $(this).find('[id*="itemuom"]').attr('id');
          var txtmuomid =  $("#txt"+fieldid3+"").val();
          var txtauom =  $("#txt"+fieldid3+"").data("desc");
          var txtmuom =  $(this).find('[id*="itemuom"]').text();
          var fieldid4 = $(this).find('[id*="uomqty"]').attr('id');
          var txtauomid =  $("#txt"+fieldid4+"").val();
          var txtauomqty =  $("#txt"+fieldid4+"").data("desc");
          var txtmuomqty =  $(this).find('[id*="uomqty"]').text();
          var fieldid5 = $(this).find('[id*="irate"]').attr('id');
          var txtruom =  $("#txt"+fieldid5+"").val();
          var txtmqtyf = $("#txt"+fieldid5+"").data("desc");
          var fieldid6 = $(this).find('[id*="itax"]').attr('id');
          var txttax2 =  $("#txt"+fieldid6+"").val();
          var txttax1 = $("#txt"+fieldid6+"").data("desc");
          var fieldid7 = $(this).find('[id*="ise"]').attr('id');
          var txtenqno = $("#txt"+fieldid7+"").val();
          var totalvalue = 0.00;
          var txttaxamt1 = 0.00;
          var txttaxamt2 = 0.00;
          var txttottaxamt = 0.00;
          var txttotamtatax =0.00;
          

          txtruom = parseFloat(txtruom).toFixed(5); 
          txtauomqty = (parseFloat(txtmuomqty)/parseFloat(txtmqtyf))*parseFloat(txtauomqty);
          
          var txtamt = parseFloat((parseFloat(txtmuomqty)*parseFloat(txtruom))).toFixed(2);
          if(txttax1 == undefined || txttax1 == '')
            {
              txttax1 = 0.0000;
              txttaxamt1 = 0.00;
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
        if(intRegex.test(txtauomqty)){
            txtauomqty = (txtauomqty +'.000');
        }

        if(intRegex.test(txtmuomqty)){
          txtmuomqty = (txtmuomqty +'.000');
        }

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


        var SalesEnq2 = [];
        $('#Material').find('.participantRow').each(function(){
          if($(this).find('[id*="ITEMID_REF"]').val() != ''){

            if($("#DirectSQ").is(":checked") == true) {
              var seitem = $(this).find('[id*="ITEMID_REF"]').val();
            }
            else{
              var seitem = $(this).find('[id*="txtSE_popup"]').val()+'-'+$(this).find('[id*="ITEMID_REF"]').val();
            }


            SalesEnq2.push(seitem);
          }
        });


            


        
        var salesenquiry =  $('#hdn_ItemID17').val();
        var itemids =  $('#hdn_ItemID18').val();
    
            if($(this).find('[id*="chkId"]').is(":checked") == true) 
            {


              if($("#DirectSQ").is(":checked") == true) {
                var txtenqitem = txtval;
              }
              else{
                var txtenqitem = txtenqno+'-'+txtval;
              }

             

              if(jQuery.inArray(txtenqitem, SalesEnq2) !== -1){
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
                            txtval = '';
                            texdesc = '';
                            txtname = '';
                            txtmuom = '';
                            txtauom = '';
                            txtmuomid = '';
                            txtauomid = '';
                            txtauomqty='';
                            txtmuomqty='';
                            txtruom = '';
                            txtamt = '';
                            txttax1 = '';
                            txttax2 = '';
                            txtenqno = '';
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
                        $clone.find('[id*="SEMUOM"]').val(txtmuom);
                        $clone.find('[id*="SEMUOMQTY"]').val(txtmuomqty);
                        $clone.find('[id*="SEAUOM"]').val(txtauom);
                        $clone.find('[id*="SEAUOMQTY"]').val(txtauomqty);
                        $clone.find('[id*="popupMUOM"]').val(txtmuom);
                        $clone.find('[id*="MAIN_UOMID_REF"]').val(txtmuomid);
                        $clone.find('[id*="SQ_QTY"]').val(txtmuomqty);
                        $clone.find('[id*="SQ_FQTY"]').val(txtmuomqty);
                        $clone.find('[id*="popupAUOM"]').val(txtauom);
                        $clone.find('[id*="ALT_UOMID_REF"]').val(txtauomid);
                        $clone.find('[id*="ALT_UOMID_QTY"]').val(txtauomqty);
                        $clone.find('[id*="RATEPUOM"]').val(txtruom);
                        $clone.find('[id*="DISAFTT_AMT"]').val(txtamt);
                        $clone.find('[id*="TOT_AMT"]').val(txttotamtatax);
                        $clone.find('[id*="TGST_AMT"]').val(txttottaxamt);
                        if($('#Tax_State').val() == 'OutofState')
                        {
                          $clone.find('[id*="IGST"]').val(txttax1);
                          $clone.find('[id*="IGSTAMT"]').val(txttaxamt1);
                          $clone.find('[id*="SGST"]').prop('disabled',true); 
                          $clone.find('[id*="CGST"]').prop('disabled',true); 
                          $clone.find('[id*="SGSTAMT"]').prop('disabled',true); 
                          $clone.find('[id*="CGSTAMT"]').prop('disabled',true); 
                        }
                        else
                        {
                          $clone.find('[id*="CGST"]').val(txttax1);
                          $clone.find('[id*="IGST"]').prop('disabled',true); 
                          $clone.find('[id*="SGST"]').val(txttax2);
                          $clone.find('[id*="CGSTAMT"]').val(txttaxamt1);
                          $clone.find('[id*="SGSTAMT"]').val(txttaxamt2);
                          $clone.find('[id*="IGSTAMT"]').prop('disabled',true); 
                        }
                        
                        $tr.closest('table').append($clone);   
                        var rowCount = $('#Row_Count1').val();
                        rowCount = parseInt(rowCount)+1;
                        $('#Row_Count1').val(rowCount);
                        var tvalue = parseFloat(txttotamtatax).toFixed(2);
                        totalvalue = $('#TotalValue').val();
                        totalvalue =  parseFloat(totalvalue) + parseFloat(tvalue);
                        totalvalue = parseFloat(totalvalue).toFixed(2);
                        $('#TotalValue').val(totalvalue);
                        
                  if($clone.find('[id*="txtSE_popup"]').val() == '')
                  {
                    $clone.find('[id*="SEMUOM"]').val('');
                    $clone.find('[id*="SEMUOMQTY"]').val('');
                    $clone.find('[id*="SEAUOM"]').val('');
                    $clone.find('[id*="SEAUOMQTY"]').val('');
                  }
                  $(".blurRate").blur();
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
                      $('#'+txtid).val(texdesc);
                      $('#'+txt_id2).val(txtval);
                      $('#'+txt_id3).val(txtname);
                      $('#'+txt_id4).val(txtmuom);
                      $('#'+txt_id5).val(txtmuomqty);
                      $('#'+txt_id6).val(txtauom);
                      $('#'+txt_id7).val(txtauomqty);
                      $('#'+txt_id8).val(txtmuom);
                      $('#'+txt_id9).val(txtmuomid);
                      $('#'+txt_id10).val(txtmuomqty);
                      $('#'+txt_id11).val(txtauom);
                      $('#'+txt_id12).val(txtauomid);
                      $('#'+txt_id13).val(txtauomqty);
                      $('#'+txt_id14).val(txtruom);
                      $('#'+txt_id15).val(txtmuomqty);
                      $('#'+txtid).parent().parent().find('[id*="DISAFTT_AMT"]').val(txtamt);
                      $('#'+txtid).parent().parent().find('[id*="TOT_AMT"]').val(txttotamtatax);
                      $('#'+txtid).parent().parent().find('[id*="TGST_AMT"]').val(txttottaxamt);
                      if($('#Tax_State').val() == 'OutofState')
                        {
                          $('#'+txtid).parent().parent().find('[id*="IGST"]').val(txttax1);
                          $('#'+txtid).parent().parent().find('[id*="IGSTAMT"]').val(txttaxamt1);
                          $('#'+txtid).parent().parent().find('[id*="SGST"]').prop('disabled',true); 
                          $('#'+txtid).parent().parent().find('[id*="CGST"]').prop('disabled',true);
                          $('#'+txtid).parent().parent().find('[id*="SGSTAMT"]').prop('disabled',true); 
                          $('#'+txtid).parent().parent().find('[id*="CGSTAMT"]').prop('disabled',true); 
                        }
                        else
                        {
                          $('#'+txtid).parent().parent().find('[id*="CGST"]').val(txttax1);
                          $('#'+txtid).parent().parent().find('[id*="IGST"]').prop('disabled',true); 
                          $('#'+txtid).parent().parent().find('[id*="IGSTAMT"]').prop('disabled',true); 
                          $('#'+txtid).parent().parent().find('[id*="SGST"]').val(txttax2);
                          $('#'+txtid).parent().parent().find('[id*="SGSTAMT"]').val(txttaxamt2);
                          $('#'+txtid).parent().parent().find('[id*="CGSTAMT"]').val(txttaxamt1);
                        }
                        var tvalue = parseFloat(txttotamtatax).toFixed(2);
                        totalvalue = $('#TotalValue').val();
                        totalvalue =  parseFloat(totalvalue) + parseFloat(tvalue);
                        totalvalue = parseFloat(totalvalue).toFixed(2);
                        $('#TotalValue').val(totalvalue);
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
                      if($('#'+txtid).parent().parent().find('[id*="txtSE_popup"]').val() == '')
                      {
                        $('#'+txtid).parent().parent().find('[id*="SEMUOM"]').val('');
                        $('#'+txtid).parent().parent().find('[id*="SEMUOMQTY"]').val('');
                        $('#'+txtid).parent().parent().find('[id*="SEAUOM"]').val('');
                        $('#'+txtid).parent().parent().find('[id*="SEAUOMQTY"]').val('');
                      }
                      $(".blurRate").blur();
                      event.preventDefault();
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
          $('.js-selectall').prop("checked", false);
          $("#ITEMIDpopup").hide();
          event.preventDefault();
        });
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
        var txtmuom =  $(this).parent().parent().children('[id*="itemuom"]').text();
        var fieldid4 = $(this).parent().parent().children('[id*="uomqty"]').attr('id');
        var txtauomid =  $("#txt"+fieldid4+"").val();
        var txtauomqty =  $("#txt"+fieldid4+"").data("desc");
        var txtmuomqty =  $(this).parent().parent().children('[id*="uomqty"]').text();
        var fieldid5 = $(this).parent().parent().children('[id*="irate"]').attr('id');
        var txtruom =  $("#txt"+fieldid5+"").val();
        var txtmqtyf = $("#txt"+fieldid5+"").data("desc");
        var fieldid6 = $(this).parent().parent().children('[id*="itax"]').attr('id');
        var txttax2 =  $("#txt"+fieldid6+"").val();
        var txttax1 = $("#txt"+fieldid6+"").data("desc");
        var fieldid7 = $(this).parent().parent().children('[id*="ise"]').attr('id');
        var txtenqno = $("#txt"+fieldid7+"").val();
        var totalvalue = 0.00;
        var txttaxamt1 = 0.00;
        var txttaxamt2 = 0.00;
        var txttottaxamt = 0.00;
        var txttotamtatax =0.00;

        txtruom = parseFloat(txtruom).toFixed(5); 
        txtauomqty = (parseFloat(txtmuomqty)/parseFloat(txtmqtyf))*parseFloat(txtauomqty);
        
        var txtamt = parseFloat((parseFloat(txtmuomqty)*parseFloat(txtruom))).toFixed(2);
        if(txttax1 == undefined || txttax1 == '')
          {
            txttax1 = 0.0000;
             txttaxamt1 = 0.00;
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
         txttottaxamt = parseFloat((parseFloat(txttaxamt1)+parseFloat(txttaxamt2))).toFixed(2);
         txttotamtatax = parseFloat((parseFloat(txtamt)+parseFloat(txttottaxamt))).toFixed(2);
        if(intRegex.test(txtauomqty)){
            txtauomqty = (txtauomqty +'.000');
        }

        if(intRegex.test(txtmuomqty)){
          txtmuomqty = (txtmuomqty +'.000');
        }
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

        var SalesEnq2 = [];
        $('#Material').find('.participantRow').each(function(){
          if($(this).find('[id*="ITEMID_REF"]').val() != ''){

            if($("#DirectSQ").is(":checked") == true) {
              var seitem = $(this).find('[id*="ITEMID_REF"]').val();
            }
            else{
              var seitem = $(this).find('[id*="txtSE_popup"]').val()+'-'+$(this).find('[id*="ITEMID_REF"]').val();
            }

            SalesEnq2.push(seitem);
          }
        });
        
        var salesenquiry =  $('#hdn_ItemID17').val();
        var itemids =  $('#hdn_ItemID18').val();


            if($(this).is(":checked") == true) {
              


              if($("#DirectSQ").is(":checked") == true) {
                var txtenqitem = txtval;
              }
              else{
                var txtenqitem = txtenqno+'-'+txtval;
              }

              

              if(jQuery.inArray(txtenqitem, SalesEnq2) !== -1){
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
                txtval = '';
                texdesc = '';
                txtname = '';
                txtmuom = '';
                txtauom = '';
                txtmuomid = '';
                txtauomid = '';
                txtauomqty='';
                txtmuomqty='';
                txtruom = '';
                txtamt = '';
                txttax1 = '';
                txttax2 = '';
                txtenqno = '';
                $(".blurRate").blur();
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
                        $clone.find('[id*="SEMUOM"]').val(txtmuom);
                        $clone.find('[id*="SEMUOMQTY"]').val(txtmuomqty);
                        $clone.find('[id*="SEAUOM"]').val(txtauom);
                        $clone.find('[id*="SEAUOMQTY"]').val(txtauomqty);
                        $clone.find('[id*="popupMUOM"]').val(txtmuom);
                        $clone.find('[id*="MAIN_UOMID_REF"]').val(txtmuomid);
                        $clone.find('[id*="SQ_QTY"]').val(txtmuomqty);
                        $clone.find('[id*="SQ_FQTY"]').val(txtmuomqty);
                        $clone.find('[id*="popupAUOM"]').val(txtauom);
                        $clone.find('[id*="ALT_UOMID_REF"]').val(txtauomid);
                        $clone.find('[id*="ALT_UOMID_QTY"]').val(txtauomqty);
                        $clone.find('[id*="RATEPUOM"]').val(txtruom);
                        $clone.find('[id*="DISAFTT_AMT"]').val(txtamt);
                        $clone.find('[id*="TOT_AMT"]').val(txttotamtatax);
                        $clone.find('[id*="TGST_AMT"]').val(txttottaxamt);
                        if($('#Tax_State').val() == 'OutofState')
                        {
                          $clone.find('[id*="IGST"]').val(txttax1);
                          $clone.find('[id*="SGST"]').prop('disabled',true); 
                          $clone.find('[id*="CGST"]').prop('disabled',true); 
                          $clone.find('[id*="SGSTAMT"]').prop('disabled',true); 
                          $clone.find('[id*="CGSTAMT"]').prop('disabled',true);
                          $clone.find('[id*="IGSTAMT"]').val(txttaxamt1);
                        }
                        else
                        {
                          $clone.find('[id  *="CGST"]').val(txttax1);
                          $clone.find('[id*="IGST"]').prop('disabled',true); 
                          $clone.find('[id*="SGST"]').val(txttax2);
                          $clone.find('[id*="SGSTAMT"]').val(txttaxamt2);; 
                          $clone.find('[id*="CGSTAMT"]').val(txttaxamt1);;
                          $clone.find('[id*="IGSTAMT"]').prop('disabled',true);
                        }
                        $tr.closest('table').append($clone);   
                        var rowCount = $('#Row_Count1').val();
                          rowCount = parseInt(rowCount)+1;
                          $('#Row_Count1').val(rowCount);
                          var tvalue = parseFloat(txttotamtatax).toFixed(2);
                        totalvalue = $('#TotalValue').val();
                        totalvalue =  parseFloat(totalvalue) + parseFloat(tvalue);
                        totalvalue = parseFloat(totalvalue).toFixed(2);
                        $('#TotalValue').val(totalvalue);
                        
                        if($clone.find('[id*="txtSE_popup"]').val() == '')
                        {
                          $clone.find('[id*="SEMUOM"]').val('');
                          $clone.find('[id*="SEMUOMQTY"]').val('');
                          $clone.find('[id*="SEAUOM"]').val('');
                          $clone.find('[id*="SEAUOMQTY"]').val('');
                        } 
                        $(".blurRate").blur();
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
                      var txt_id9= $('#hdn_ItemID9').val();
                      var txt_id10= $('#hdn_ItemID10').val();
                      var txt_id11= $('#hdn_ItemID11').val();
                      var txt_id12= $('#hdn_ItemID12').val();
                      var txt_id13= $('#hdn_ItemID13').val();
                      var txt_id14= $('#hdn_ItemID14').val();
                      var txt_id15= $('#hdn_ItemID15').val();
                      $('#'+txtid).val(texdesc);
                      $('#'+txt_id2).val(txtval);
                      $('#'+txt_id3).val(txtname);
                      $('#'+txt_id4).val(txtmuom);
                      $('#'+txt_id5).val(txtmuomqty);
                      $('#'+txt_id6).val(txtauom);
                      $('#'+txt_id7).val(txtauomqty);
                      $('#'+txt_id8).val(txtmuom);
                      $('#'+txt_id9).val(txtmuomid);
                      $('#'+txt_id10).val(txtmuomqty);
                      $('#'+txt_id11).val(txtauom);
                      $('#'+txt_id12).val(txtauomid);
                      $('#'+txt_id13).val(txtauomqty);
                      $('#'+txt_id14).val(txtruom);
                      $('#'+txt_id15).val(txtmuomqty);
                      $('#'+txtid).parent().parent().find('[id*="DISAFTT_AMT"]').val(txtamt);
                      $('#'+txtid).parent().parent().find('[id*="TOT_AMT"]').val(txttotamtatax);
                      $('#'+txtid).parent().parent().find('[id*="TGST_AMT"]').val(txttottaxamt);
                      if($('#Tax_State').val() == 'OutofState')
                        {
                          $('#'+txtid).parent().parent().find('[id*="IGST"]').val(txttax1);
                          $('#'+txtid).parent().parent().find('[id*="IGSTAMT"]').val(txttaxamt1);
                          $('#'+txtid).parent().parent().find('[id*="SGST"]').prop('disabled',true); 
                          $('#'+txtid).parent().parent().find('[id*="CGST"]').prop('disabled',true); 
                          $('#'+txtid).parent().parent().find('[id*="CGSTAMT"]').prop('disabled',true);
                          $('#'+txtid).parent().parent().find('[id*="SGSTAMT"]').prop('disabled',true);
                        }
                        else
                        {
                          $('#'+txtid).parent().parent().find('[id*="CGST"]').val(txttax1);
                          $('#'+txtid).parent().parent().find('[id*="IGST"]').prop('disabled',true); 
                          $('#'+txtid).parent().parent().find('[id*="IGSTAMT"]').prop('disabled',true); 
                          $('#'+txtid).parent().parent().find('[id*="SGST"]').val(txttax2);
                          $('#'+txtid).parent().parent().find('[id*="CGSTAMT"]').val(txttaxamt1);
                          $('#'+txtid).parent().parent().find('[id*="SGSTAMT"]').val(txttaxamt1);
                        }
                        var tvalue = parseFloat(txttotamtatax).toFixed(2);
                        totalvalue = $('#TotalValue').val();
                        totalvalue =  parseFloat(totalvalue) + parseFloat(tvalue);
                        totalvalue = parseFloat(totalvalue).toFixed(2);
                        $('#TotalValue').val(totalvalue);
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
                      if($('#'+txtid).parent().parent().find('[id*="txtSE_popup"]').val() == '')
                        {
                          $('#'+txtid).parent().parent().find('[id*="SEMUOM"]').val('');
                          $('#'+txtid).parent().parent().find('[id*="SEMUOMQTY"]').val('');
                          $('#'+txtid).parent().parent().find('[id*="SEAUOM"]').val('');
                          $('#'+txtid).parent().parent().find('[id*="SEAUOMQTY"]').val('');
                        }
                      }
                      $(".blurRate").blur();
       }
       else if($(this).is(":checked") == false) 
       {
         var id = txtval;
         var r_count = $('#Row_Count1').val();
         $('#Material').find('.participantRow').each(function()
         {
           var itemid = $(this).find('[id*="ITEMID_REF"]').val();
           if(id == itemid)
           {
              var rowCount = $('#Row_Count1').val();
              if (rowCount > 1) {
                var totalvalue = $('#TotalValue').val();
                totalvalue = parseFloat(totalvalue - $(this).closest('.participantRow').find('[id*="TOT_AMT_"]').val()).toFixed(2);
                $('#TotalValue').val(totalvalue);
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
        $('.js-selectall').prop("checked", false);
        $("#ITEMIDpopup").hide();
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

      function altuomNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("altuomnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("altuomTable2");
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


//END GLOCAL FUCTION FOR CHECK

      

  $('#Material').on('click','[id*="popupAUOM"]',function(event){
        var ItemID = $(this).parent().parent().find('[id*="ITEMID_REF"]').val();
        var fieldid = $(this).parent().parent().find('[id*="ALT_UOMID_REF"]').attr('id');
        
        if(ItemID !=''){
                $("#tbody_altuom").html('');
                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  });
                  $.ajax({
                      url:'{{route("transaction",[36,"getAltUOM"])}}',
                      type:'POST',
                      data:{'id':ItemID,fieldid:fieldid},
                      success:function(data) {
                        
                        $("#tbody_altuom").html(data);   
                        bindAltUOM(); 
                        showSelectedCheck($("#"+fieldid).val(),"SELECT_"+fieldid)                     
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
        var id2 = $(this).parent().parent().find('[id*="ALT_UOMID_REF"]').attr('id');
        var id3 = $(this).parent().parent().find('[id*="SQ_QTY"]').attr('id');
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
        var mqty = $('#'+txtid).parent().parent().find('[id*="SQ_QTY"]').val();

        if(altuomid!=''){
              $('#'+txt_id4).val('');
                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  });
                  $.ajax({
                      url:'{{route("transaction",[36,"getaltuomqty"])}}',
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
 // $(".blurRate1").blur(); 
  //$(".blurRate1").focusout(); 
  var QUOTATIONTYPE = $("#QUOTATIONTYPE").val();
  if(QUOTATIONTYPE=="PROSPECT"){
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
                      url:'{{route("transaction",[36,"getShipTo"])}}',
                      type:'POST',
                      data:{'id':"",'type':"PROSPECT",'leadid_ref':$("#LEAD_REF").val()},
                      success:function(data) {
                        $("#div_shipto").html(data);
                        
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                        $("#txtSHIPTO").hide();
                        $("#txtSHIPTO1").show();
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
                      url:'{{route("transaction",[36,"getBillTo"])}}',
                      type:'POST',
                      data:{'id':"",'type':"PROSPECT",'leadid_ref':$("#LEAD_REF").val()},
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


                }



  $(".blurDiscount").blur(function(){
   //code code and more code
}).blur();


    var Material = $("#Material").html(); 
    $('#hdnmaterial').val(Material);
    var count1 = <?php echo json_encode($objCount1); ?>;
    var count2 = <?php echo json_encode($objCount2); ?>;
    var count3 = <?php echo json_encode($objCount3); ?>;
    var count4 = <?php echo json_encode($objCount4); ?>;
    $('#Row_Count1').val(count1);
    $('#Row_Count2').val(count2);
    $('#Row_Count3').val(count3);
    $('#Row_Count4').val(count4);
    var obj = <?php echo json_encode($objSQMAT); ?>;
    var objtnc = <?php echo json_encode($objSQTNC); ?>;  
    var tncheader = <?php echo json_encode($objTNCHeader); ?>;
    var tncdetails = <?php echo json_encode($objTNCDetails); ?>;
    var soudf = <?php echo json_encode($objSQUDF); ?>;
    var udfforso = <?php echo json_encode($objUdfSQData2); ?>;
    var calheader = <?php echo json_encode($objCalHeader); ?>;
    var caldetails = <?php echo json_encode($objCalDetails); ?>;
    var SOCal = <?php echo json_encode($objSQCAL); ?>;
    var taxstate = <?php echo json_encode($TAXSTATE); ?>;
    
    if(obj[0].ENQNO == null)
    {
      $('#DirectSQ').prop('checked','true');
      $('#Material').find('[id*="txtSE_popup"]').prop('disabled','true');
    }
    
    
    $.each( soudf, function( soukey, souvalue ) {
        $.each( udfforso, function( usokey, usovalue ) { 
            if(souvalue.UDFSQID_REF == usovalue.UDFSQID)
            {
                $('#popupUDFSQID_'+soukey).val(usovalue.LABEL);
            }
        
            if(souvalue.UDFSQID_REF == usovalue.UDFSQID)
            {        
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
                    $('#'+txt_id41).html(strinp2);   //set dynamic input
                    $('#'+dynamicid2).val(souvalue.SQUVALUE);
                    $('#UDFismandatory_'+soukey).val(usovalue.ISMANDATORY); // mandatory
                
            }
        });
    });
    $.each( objtnc, function( tnckey, tncvalue ) {
        $.each( tncheader, function( tnchkey, tnchvalue ) { 
            if(tncvalue.TNCID_REF == tnchvalue.TNCID)
            {
                $('#txtTNCID_popup').val(tnchvalue.TNC_CODE);
            }
        });
        $.each( tncdetails, function( tncdkey, tncdvalue ) { 
            if(tncvalue.TNCDID_REF == tncdvalue.TNCDID)
            {
                $('#popupTNCDID_'+tnckey).val(tncdvalue.TNC_NAME);
            }
            if(tncvalue.TNCDID_REF == tncdvalue.TNCDID)
            {        
                    var txtvaltype =   tncdvalue.VALUE_TYPE;
                    var txt_id4 = $('#tdinputid_'+tnckey).attr('id');
                    var strdyn = txt_id4.split('_');
                    var lastele =   strdyn[strdyn.length-1];
                    var dynamicid = "tncdetvalue_"+lastele;
                    
                    var chkvaltype =  txtvaltype.toLowerCase();
                    var strinp = '';

                    if(chkvaltype=='date'){

                    strinp = '<input type="date" placeholder="dd/mm/yyyy" name="'+dynamicid+ '" id="'+dynamicid+'" autocomplete="off" class="form-control"  > ';       

                    }
                    else if(chkvaltype=='time'){
                    strinp= '<input type="time" placeholder="h:i" name="'+dynamicid+ '" id="'+dynamicid+'" autocomplete="off" class="form-control"  > ';

                    }
                    else if(chkvaltype=='numeric'){
                    strinp = '<input type="text" name="'+dynamicid+ '" id="'+dynamicid+'" autocomplete="off" class="form-control"   > ';

                    }
                    else if(chkvaltype=='text'){

                    strinp = '<input type="text" name="'+dynamicid+ '" id="'+dynamicid+'" autocomplete="off" class="form-control"  > ';
                    
                    }
                    else if(chkvaltype=='boolean'){
                      if(tncvalue.VALUE == "1")
                      {
                        strinp = '<input type="checkbox" name="'+dynamicid+ '" id="'+dynamicid+'" class="" checked> ';
                      }
                      else{
                        strinp = '<input type="checkbox" name="'+dynamicid+ '" id="'+dynamicid+'" class="" > ';
                      }                    
                    }
                    else if(chkvaltype=='combobox'){

                    var txtoptscombo =   tncdvalue.DESCRIPTIONS;
                    var strarray = txtoptscombo.split(',');
                    var opts = '';

                    for (var i = 0; i < strarray.length; i++) {
                        opts = opts + '<option value="'+strarray[i]+'">'+strarray[i]+'</option> ';
                    }

                    strinp = '<select name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" required>'+opts+'</select>' ;
                   
                    }
                   
                    
                    $('#'+txt_id4).html('');  
                    $('#'+txt_id4).html(strinp);   //set dynamic input
                    $('#'+dynamicid).val(tncvalue.VALUE);
                    $('#TNCismandatory_'+tnckey).val(tncdvalue.IS_MANDATORY); // mandatory
                
            }
        });
    });
    $.each(SOCal, function( sockey, socvalue ) {
        $.each( calheader, function( calkey, calvalue ){ 
            if(socvalue.CTID_REF == calvalue.CTID)
            {
                $('#txtCTID_popup').val(calvalue.CTCODE);
            }
        });
        $.each( caldetails, function( caldkey, caldvalue ){ 
            if(socvalue.TID_REF == caldvalue.TID)
            {
                $('#popupTID_'+sockey).val(caldvalue.COMPONENT);
                $('#BASIS_'+sockey).val(caldvalue.BASIS);
                $('#SQNO_'+sockey).val(caldvalue.SQNO);
                $('#FORMULA_'+sockey).val(caldvalue.FORMULA);
                
            }
        });
        if(taxstate =="OutofState")
            { 
              $('#calIGST_'+sockey).removeAttr('readonly');
              var gstamt = parseFloat((socvalue.IGST*socvalue.VALUE)/100).toFixed(2);
              var totgst = parseFloat(gstamt).toFixed(2);
              $('#AMTIGST_'+sockey).val(gstamt);
              $('#TOTGSTAMT_'+sockey).val(totgst);
              var tvalue = 0.00;
              tvalue = parseFloat(tvalue) + parseFloat(socvalue.VALUE);
              tvalue = parseFloat(tvalue) + parseFloat(totgst);
            }
            else
            {
              $('#calCGST_'+sockey).removeAttr('readonly');
              $('#calSGST_'+sockey).removeAttr('readonly');
              var gstamt2 = parseFloat((socvalue.CGST*socvalue.VALUE)/100).toFixed(2);
              var gstamt3 = parseFloat((socvalue.SGST*socvalue.VALUE)/100).toFixed(2);
              var totgst2 = parseFloat(parseFloat(gstamt2)+parseFloat(gstamt3)).toFixed(2);
              $('#AMTCGST_'+sockey).val(gstamt2);
              $('#AMTSGST_'+sockey).val(gstamt3);
              $('#TOTGSTAMT_'+sockey).val(totgst2);
              var tvalue = 0.00;
              tvalue = parseFloat(tvalue) + parseFloat(socvalue.VALUE);
              tvalue = parseFloat(parseFloat(tvalue) + parseFloat(totgst2)).toFixed(2);
            }
          
          totalvalue += + tvalue;
          
    });
    var totalvalue = 0.00;
      var tvalue = 0.00;
      var ctvalue = 0.00;
      var ctgstvalue = 0.00;

      $('#Material').find('.participantRow').each(function()
      {
        tvalue = $(this).find('[id*="TOT_AMT"]').val();
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
      $('#TotalValue').val(totalvalue);
   
    
    var d = new Date(); 
    var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
    d.setDate(d.getDate() + 29);
    var todate = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
    
    
    
    $('#DirectSQ').change(function(){
      if ($(this).is(":checked") == true){
          $('#Material').find('[id*="txtSE_popup"]').prop('disabled','true')
          $('#Material').find('[id*="txtSE_popup"]').val('');
          $('#Material').find('[id*="SEQID_REF"]').val('');
          event.preventDefault();
      }
      else
      {
          $('#Material').find('[id*="txtSE_popup"]').removeAttr('disabled');
          event.preventDefault();
      }
    });
    
    $('#Material').on('keyup',"[id*='ALT_UOMID_QTY']",function()
    {
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.000')
      }
      event.preventDefault();
    });

    

    $('#Material').on('focusout',"[id*='SQ_QTY']",function()
    {
        var totalvalue = 0.00;
        var itemid = $(this).parent().parent().find('[id*="ITEMID_REF"]').val();
        var mqty = $(this).val();
        var altuomid = $(this).parent().parent().find('[id*="ALT_UOMID_REF"]').val();
        var txtid = $(this).parent().parent().find('[id*="ALT_UOMID_QTY"]').attr('id');
        var irate = $(this).parent().parent().find('[id*="RATEPUOM"]').val();
        $(this).parent().parent().find('[id*="IGSTAMT"]').val('0');
        $(this).parent().parent().find('[id*="CGSTAMT"]').val('0');
        $(this).parent().parent().find('[id*="SGSTAMT"]').val('0');
        var tamt = parseFloat(parseFloat(mqty)*parseFloat(irate)).toFixed(2);
        var dispercnt = $(this).parent().parent().find('[id*="DISCPER"]').val();
        var disamt = 0 ;      
        if (dispercnt != '' && dispercnt != '.0000')
        {
           disamt =  parseFloat((parseFloat(tamt)*parseFloat(dispercnt))/100).toFixed(2);
        }
        else if ($(this).parent().parent().find('[id*="DISCOUNT_AMT"]').val() != '' && $(this).parent().parent().find('[id*="DISCOUNT_AMT"]').val() != '0.00')
        {
           disamt = $(this).parent().parent().find('[id*="DISCOUNT_AMT"]').val();
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
        if(altuomid!=''){
              
                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  });
                  $.ajax({
                      url:'{{route("transaction",[36,"getaltuomqty"])}}',
                      type:'POST',
                      data:{'id':altuomid, 'itemid':itemid, 'mqty':mqty},
                      success:function(data) {
                        if(intRegex.test(data)){
                            data = (data +'.000');
                        }
                        $("#"+txtid).val(data);                        
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                        $("#"+txtid).val('');                        
                      },
                  }); 
                      
              }
      
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
      $(this).parent().parent().find('[id*="DISAFTT_AMT"]').val(tamt);
      $(this).parent().parent().find('[id*="TOT_AMT"]').val(totamt);
      $(this).parent().parent().find('[id*="TGST_AMT"]').val(taxamt);
      $(this).parent().parent().find('[id*="IGSTAMT"]').val(tp1amt);
      $(this).parent().parent().find('[id*="CGSTAMT"]').val(tp2amt);
      $(this).parent().parent().find('[id*="SGSTAMT"]').val(tp3amt);
      bindTotalValue();
      if($('#CTID_REF').val()!='')
      {
      bindGSTCalTemplate();
      }
      bindTotalValue();
      event.preventDefault();
    });

    $('#Material').on('focusout',"[id*='RATEPUOM']",function()
    {
        var mqty = $(this).parent().parent().find('[id*="SQ_QTY"]').val();
        var irate = $(this).val();
        var taxamt = $(this).parent().parent().find('[id*="TGST_AMT"]').val();
                
        var tamt = parseFloat(parseFloat(mqty)*parseFloat(irate)).toFixed(2);  
        var dispercnt = $(this).parent().parent().find('[id*="DISCPER"]').val();
        var disamt = 0 ;      
        if (dispercnt != '' && dispercnt != '.0000')
        {
           disamt =  parseFloat((parseFloat(tamt)*parseFloat(dispercnt))/100).toFixed(2);
        }
        else if ($(this).parent().parent().find('[id*="DISCOUNT_AMT"]').val() != '' && $(this).parent().parent().find('[id*="DISCOUNT_AMT"]').val() != '0.00')
        {
           disamt = $(this).parent().parent().find('[id*="DISCOUNT_AMT"]').val();
        }
        tamt = parseFloat(parseFloat(tamt) - parseFloat(disamt)).toFixed(2);       
        $(this).parent().parent().find('[id*="IGSTAMT"]').val('0');
        $(this).parent().parent().find('[id*="CGSTAMT"]').val('0');
        $(this).parent().parent().find('[id*="SGSTAMT"]').val('0'); 
        var tp1 = $(this).parent().parent().find('[id*="IGST_"]').val();
        var tp2 = $(this).parent().parent().find('[id*="CGST_"]').val();
        var tp3 = $(this).parent().parent().find('[id*="SGST_"]').val();
        var tp1amt = parseFloat((tamt * tp1)/100).toFixed(2);
        var tp2amt = parseFloat((tamt * tp2)/100).toFixed(2);
        var tp3amt = parseFloat((tamt * tp3)/100).toFixed(2);
        var taxamt = parseFloat(parseFloat(tp1amt) + parseFloat(tp2amt) + parseFloat(tp3amt)).toFixed(2); 
        var totamt = parseFloat(parseFloat(tamt) + parseFloat(taxamt)).toFixed(2);
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.00000')
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
      $(this).parent().parent().find('[id*="DISAFTT_AMT"]').val(tamt);
      $(this).parent().parent().find('[id*="TOT_AMT"]').val(totamt);
      $(this).parent().parent().find('[id*="TGST_AMT"]').val(taxamt);
      $(this).parent().parent().find('[id*="IGSTAMT"]').val(tp1amt);
      $(this).parent().parent().find('[id*="CGSTAMT"]').val(tp2amt);
      $(this).parent().parent().find('[id*="SGSTAMT"]').val(tp3amt);
      bindTotalValue();
      if($('#CTID_REF').val()!='')
      {
      bindGSTCalTemplate();
      }
      bindTotalValue();
      event.preventDefault();
    });   

    $('#Material').on('focusout',"[id*='DISCPER']",function()
    { 
      var mqty = $(this).parent().parent().find('[id*="SQ_QTY"]').val();
      var irate = $(this).parent().parent().find('[id*="RATEPUOM"]').val();
      var totamt = parseFloat(parseFloat(mqty)*parseFloat(irate)).toFixed(2);
      var dpert = $(this).val();
      var disamt = $(this).parent().parent().find('[id*="DISCOUNT_AMT"]').val();
     
      if (dpert != '' && dpert != '.0000')
      {
        var amtfd = parseFloat(parseFloat(totamt) - (parseFloat(totamt)*parseFloat(dpert))/100).toFixed(2);
        if(intRegex.test($(this).val())){
          $(this).val($(this).val()+'.0000')
        }
      var tp1 = $(this).parent().parent().find('[id*="IGST_"]').val();
      var tp2 = $(this).parent().parent().find('[id*="CGST_"]').val();
      var tp3 = $(this).parent().parent().find('[id*="SGST_"]').val();
      $(this).parent().parent().find('[id*="IGSTAMT"]').val('0');
      $(this).parent().parent().find('[id*="CGSTAMT"]').val('0');
      $(this).parent().parent().find('[id*="SGSTAMT"]').val('0');
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
      $(this).parent().parent().find('[id*="DISCOUNT_AMT"]').prop('readonly',true);
      $(this).parent().parent().find('[id*="DISAFTT_AMT"]').val(amtfd);
      $(this).parent().parent().find('[id*="TOT_AMT"]').val(netamt);
      $(this).parent().parent().find('[id*="TGST_AMT"]').val(taxamt);
      $(this).parent().parent().find('[id*="IGSTAMT"]').val(tp1amt);
      $(this).parent().parent().find('[id*="CGSTAMT"]').val(tp2amt);
      $(this).parent().parent().find('[id*="SGSTAMT"]').val(tp3amt);
      }
      else if (disamt != '' && disamt != '.00')
      {
        var amtfd = parseFloat(parseFloat(totamt) - parseFloat(disamt)).toFixed(2);
        if(intRegex.test($(this).val())){
          $(this).val($(this).val()+'.0000')
        }
      var tp1 = $(this).parent().parent().find('[id*="IGST_"]').val();
      var tp2 = $(this).parent().parent().find('[id*="CGST_"]').val();
      var tp3 = $(this).parent().parent().find('[id*="SGST_"]').val();
      $(this).parent().parent().find('[id*="IGSTAMT"]').val('0');
      $(this).parent().parent().find('[id*="CGSTAMT"]').val('0');
      $(this).parent().parent().find('[id*="SGSTAMT"]').val('0');
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
      $(this).parent().parent().find('[id*="DISCPER"]').prop('readonly',true);
      $(this).parent().parent().find('[id*="DISAFTT_AMT"]').val(amtfd);
      $(this).parent().parent().find('[id*="TOT_AMT"]').val(netamt);
      $(this).parent().parent().find('[id*="TGST_AMT"]').val(taxamt);
      $(this).parent().parent().find('[id*="IGSTAMT"]').val(tp1amt);
      $(this).parent().parent().find('[id*="CGSTAMT"]').val(tp2amt);
      $(this).parent().parent().find('[id*="SGSTAMT"]').val(tp3amt);
      }
      else{
        var amtfd = parseFloat(totamt).toFixed(2);
        var tp1 = $(this).parent().parent().find('[id*="IGST_"]').val();
        var tp2 = $(this).parent().parent().find('[id*="CGST_"]').val();
        var tp3 = $(this).parent().parent().find('[id*="SGST_"]').val();
        $(this).parent().parent().find('[id*="IGSTAMT"]').val('0');
        $(this).parent().parent().find('[id*="CGSTAMT"]').val('0');
        $(this).parent().parent().find('[id*="SGSTAMT"]').val('0');
        var tp1amt = parseFloat((amtfd * tp1)/100).toFixed(2);
        var tp2amt = parseFloat((amtfd * tp2)/100).toFixed(2);
        var tp3amt = parseFloat((amtfd * tp3)/100).toFixed(2);
        var taxamt = parseFloat(parseFloat(tp1amt) + parseFloat(tp2amt) + parseFloat(tp3amt)).toFixed(2);      
      var netamt = parseFloat(parseFloat(amtfd) + parseFloat(taxamt)).toFixed(2);
        if(intRegex.test($(this).val())){
          $(this).val($(this).val()+'.0000')
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
        $(this).parent().parent().find('[id*="DISCOUNT_AMT"]').removeAttr('disabled');
        $(this).parent().parent().find('[id*="DISAFTT_AMT"]').val(amtfd);
        $(this).parent().parent().find('[id*="TOT_AMT"]').val(netamt);
        $(this).parent().parent().find('[id*="TGST_AMT"]').val(taxamt);
        $(this).parent().parent().find('[id*="IGSTAMT"]').val(tp1amt);
        $(this).parent().parent().find('[id*="CGSTAMT"]').val(tp2amt);
        $(this).parent().parent().find('[id*="SGSTAMT"]').val(tp3amt);
      }
      bindTotalValue();
      if($('#CTID_REF').val()!='')
      {
      bindGSTCalTemplate();
      }
      bindTotalValue();
      event.preventDefault();
    });

    $('#Material').on('focusout',"[id*='DISCOUNT_AMT']",function(){
      var mqty = $(this).parent().parent().find('[id*="SQ_QTY"]').val();
      var irate = $(this).parent().parent().find('[id*="RATEPUOM"]').val();
      var totamt = parseFloat(parseFloat(mqty)*parseFloat(irate)).toFixed(2);
      var dpert = $(this).val();
      var dispercent = $(this).parent().parent().find('[id*="DISCPER"]').val();
      
      if (dpert != ''&& dpert != '.00')
      {
        var amtfd = parseFloat(totamt) - parseFloat(dpert);
        if(intRegex.test($(this).val())){
          $(this).val($(this).val()+'.00')
        }
        var tp1 = $(this).parent().parent().find('[id*="IGST_"]').val();
        var tp2 = $(this).parent().parent().find('[id*="CGST_"]').val();
        var tp3 = $(this).parent().parent().find('[id*="SGST_"]').val();
        $(this).parent().parent().find('[id*="IGSTAMT"]').val('0');
        $(this).parent().parent().find('[id*="CGSTAMT"]').val('0');
        $(this).parent().parent().find('[id*="SGSTAMT"]').val('0');
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
        $(this).parent().parent().find('[id*="DISCPER"]').prop('readonly',true);
        $(this).parent().parent().find('[id*="DISAFTT_AMT"]').val(amtfd);
        $(this).parent().parent().find('[id*="TOT_AMT"]').val(netamt);
        $(this).parent().parent().find('[id*="TGST_AMT"]').val(taxamt);
        $(this).parent().parent().find('[id*="IGSTAMT"]').val(tp1amt);
        $(this).parent().parent().find('[id*="CGSTAMT"]').val(tp2amt);
        $(this).parent().parent().find('[id*="SGSTAMT"]').val(tp3amt);
      }
      else if (dispercent != '' && dispercent != '.0000')
      {
        var amtfd = parseFloat(parseFloat(totamt) - (parseFloat(totamt)*parseFloat(dispercent))/100).toFixed(2);
        if(intRegex.test($(this).val())){
          $(this).val($(this).val()+'.00')
        }
      var tp1 = $(this).parent().parent().find('[id*="IGST_"]').val();
      var tp2 = $(this).parent().parent().find('[id*="CGST_"]').val();
      var tp3 = $(this).parent().parent().find('[id*="SGST_"]').val();
      $(this).parent().parent().find('[id*="IGSTAMT"]').val('0');
      $(this).parent().parent().find('[id*="CGSTAMT"]').val('0');
      $(this).parent().parent().find('[id*="SGSTAMT"]').val('0');
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
      $(this).parent().parent().find('[id*="DISCOUNT_AMT"]').prop('readonly',true);
      $(this).parent().parent().find('[id*="DISAFTT_AMT"]').val(amtfd);
      $(this).parent().parent().find('[id*="TOT_AMT"]').val(netamt);
      $(this).parent().parent().find('[id*="TGST_AMT"]').val(taxamt);
      $(this).parent().parent().find('[id*="IGSTAMT"]').val(tp1amt);
      $(this).parent().parent().find('[id*="CGSTAMT"]').val(tp2amt);
      $(this).parent().parent().find('[id*="SGSTAMT"]').val(tp3amt);
      }
      else{
        var amtfd = parseFloat(totamt).toFixed(2);
        var tp1 = $(this).parent().parent().find('[id*="IGST_"]').val();
        var tp2 = $(this).parent().parent().find('[id*="CGST_"]').val();
        var tp3 = $(this).parent().parent().find('[id*="SGST_"]').val();
        $(this).parent().parent().find('[id*="IGSTAMT"]').val('0');
        $(this).parent().parent().find('[id*="CGSTAMT"]').val('0');
        $(this).parent().parent().find('[id*="SGSTAMT"]').val('0');
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
        $(this).parent().parent().find('[id*="DISCPER"]').removeAttr('disabled');
        $(this).parent().parent().find('[id*="DISAFTT_AMT"]').val(amtfd);
        $(this).parent().parent().find('[id*="TOT_AMT"]').val(netamt);
        $(this).parent().parent().find('[id*="TGST_AMT"]').val(taxamt);
        $(this).parent().parent().find('[id*="IGSTAMT"]').val(tp1amt);
        $(this).parent().parent().find('[id*="CGSTAMT"]').val(tp2amt);
        $(this).parent().parent().find('[id*="SGSTAMT"]').val(tp3amt);
      }
      bindTotalValue();
      if($('#CTID_REF').val()!='')
      {
      bindGSTCalTemplate();
      }
      bindTotalValue();
      event.preventDefault();
    });



    $('#Material').on('focusout',"[id*='IGST']",function()
    {
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.0000')
      }
    });

    $('#Material').on('focusout',"[id*='IGST_AMT']",function()
    {
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.00')
      }
    });

    $('#Material').on('focusout',"[id*='CGST']",function()
    {
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.0000')
      }
    });

    $('#Material').on('focusout',"[id*='CGST_AMT']",function()
    {
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.00')
      }
    });

    $('#Material').on('focusout',"[id*='SGST']",function()
    {
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.0000')
      }
    });

    $('#Material').on('focusout',"[id*='SGST_AMT']",function()
    {
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.00')
      }
    });

    $('#Material').on('focusout',"[id*='TGST_AMT']",function()
    {
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.00')
      }
    });

    $('#Material').on('keyup',"[id*='TOT_AMT']",function()
    {
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.00')
      }
    });

    $('#CT').on('focusout',"[id*='calIGST_']",function()
    {
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.0000')
      }
      bindTotalValue();
    });

    $('#CT').on('focusout',"[id*='calSGST_']",function()
    {
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.0000')
      }
      bindTotalValue();
    });

    $('#CT').on('focusout',"[id*='calCGST_']",function()
    {
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.0000')
      }
      bindTotalValue();
    });
    

    $('#btnAdd').on('click', function() {
        var viewURL = '{{route("transaction",[36,"add"])}}';
                  window.location.href=viewURL;
    });
    $('#btnExit').on('click', function() {
      var viewURL = '{{route('home')}}';
                  window.location.href=viewURL;
    });
     

//SO Date Check
var lastsqdts = <?php echo json_encode($objlastSQDT[0]->SQDT); ?>;

var lastsqdt=moment(lastsqdts).format('MM/DD/YYYY');  


var today = new Date(); 
var sqdate =   ("0" + (today.getMonth() + 1)).slice(-2) + "/" +('0' + today.getDate()).slice(-2)  + "/" +  today.getFullYear()  ;

//SO Validity to Date Check
$('#QVFDT').change(function( event ) {
            var d = document.getElementById('QVFDT').value; 
            var date = new Date(d);
            var newdate = new Date(date);
            newdate.setDate(newdate.getDate() + 29);
            var sodate = newdate.getFullYear() + "-" + ("0" + (newdate.getMonth() + 1)).slice(-2) + "-" + ('0' + newdate.getDate()).slice(-2) ;
            $('#QVTDT').val(sodate);
            
        });


//SO Date Check
        
    
//delete row
    $("#Material").on('click', '.remove', function() {
        var rowCount = $(this).closest('table').find('.participantRow').length;
        if (rowCount > 1) {
        var totalvalue = $('#TotalValue').val();
        totalvalue = parseFloat(totalvalue - $(this).closest('.participantRow').find('[id*="TOT_AMT_"]').val()).toFixed(2);
        $('#TotalValue').val(totalvalue);
        $(this).closest('.participantRow').remove();   
        $(".blurRate").blur();  
        } 
        if (rowCount <= 1) { 
          $(".blurRate").blur();
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
        $tr.closest('table').append($clone);         
        var rowCount1 = $('#Row_Count1').val();
		    rowCount1 = parseInt(rowCount1)+1;
        $('#Row_Count1').val(rowCount1);
        $clone.find('.remove').removeAttr('disabled'); 
        
        $(".blurRate").blur();
        event.preventDefault();
    });

    $("#TC").on('click', '.add', function() {
        var $tr = $(this).closest('table');
        var allTrs = $tr.find('.participantRow3').last();
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

        $clone.find('input:text').val('');
        $clone.find('input:hidden').val('');
        $clone.find("[id*='tdinputid']").html('');
        $tr.closest('table').append($clone);         
        var rowCount2 = $('#Row_Count2').val();
		    rowCount2 = parseInt(rowCount2)+1;
        $('#Row_Count2').val(rowCount2);
        // $clone.find('.remove').removeAttr('disabled'); 
        
        event.preventDefault();
    });
    $("#TC").on('click', '.remove', function() {
        var rowCount2 = $(this).closest('table').find('.participantRow3').length;
        if (rowCount2 > 1) {
        $(this).closest('.participantRow3').remove();     
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
    $("#udf").on('click', '.add', function() {
        var $tr = $(this).closest('table');
        var allTrs = $tr.find('.participantRow4').last();
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

        $clone.find('input:text').val('');
        $clone.find('input:hidden').val('');
        $clone.find("[id*='udfinputid']").html('');
        $tr.closest('table').append($clone);         
        var rowCount3 = $('#Row_Count3').val();
		    rowCount3 = parseInt(rowCount3)+1;
        $('#Row_Count3').val(rowCount3);
        // $clone.find('.remove').removeAttr('disabled'); 
        
        event.preventDefault();
    });
    $("#udf").on('click', '.remove', function() {
        var rowCount3 = $(this).closest('table').find('.participantRow4').length;
        if (rowCount3 > 1) {
        $(this).closest('.participantRow4').remove();     
        } 
        if (rowCount3 <= 1) { 
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

    $("#CT").on('click', '.add', function() {
        var $tr = $(this).closest('table');
        var allTrs = $tr.find('.participantRow5').last();
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
        $clone.find('[id*="calGST"]').removeAttr('checked');
        if($clone.find('[id*="calGST"]').is(":checked") == false)
        {
          $clone.find('[id*="calIGST"]').prop('disabled','true');
          $clone.find('[id*="calCGST"]').prop('disabled','true');
          $clone.find('[id*="calSGST"]').prop('disabled','true');
          $clone.find('[id*="AMTIGST"]').prop('disabled','true');
          $clone.find('[id*="AMTCGST"]').prop('disabled','true');
          $clone.find('[id*="AMTSGST"]').prop('disabled','true');
        }
        $tr.closest('table').append($clone);         
        var rowCount4 = $('#Row_Count4').val();
		    rowCount4 = parseInt(rowCount4)+1;
        $('#Row_Count4').val(rowCount4);
        // $clone.find('.remove').removeAttr('disabled'); 
        
        event.preventDefault();
    });
    $("#CT").on('click', '.remove', function() {
        var rowCount4 = $(this).closest('table').find('.participantRow5').length;
        if (rowCount4 > 1) {
        $(this).closest('.participantRow5').remove();     
        } 
        if (rowCount4 <= 1) {          
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
      window.location.reload();
   }//fnUndoYes


   window.fnUndoNo = function (){
      $("#SONO").focus();
   }//fnUndoNo


   $("#FC").change(function() {
      if ($(this).is(":checked") == true){
          $(this).parent().parent().find('#txtCRID_popup').removeAttr('disabled');
          $(this).parent().parent().find('#txtCRID_popup').prop('readonly','true');
          $(this).parent().parent().find('#CONVFACT').removeAttr('disabled');
          $(this).parent().parent().find('#CONVFACT').prop('readonly','true');
          event.preventDefault();
      }
      else
      {
          $(this).parent().parent().find('#txtCRID_popup').prop('disabled','true');
          $(this).parent().parent().find('#txtCRID_popup').removeAttr('readonly');
          $(this).parent().parent().find('#CONVFACT').prop('disabled','true');
          $(this).parent().parent().find('#CONVFACT').removeAttr('readonly');
          $(this).parent().parent().find('#txtCRID_popup').val('');
          $(this).parent().parent().find('#CRID_REF').val('');
          $(this).parent().parent().find('#CONVFACT').val('');
          event.preventDefault();
      }
  });

  $("#CT").on('change',"[id*='calGST']",function() {
      if ($(this).is(":checked") == true){
        if($.trim($('#Tax_State').val())=="OutofState")
          {
            $(this).parent().parent().find('[id*="calIGST"]').removeAttr('disabled');
            $(this).parent().parent().find('[id*="calIGST"]').removeAttr('readonly');
            $(this).parent().parent().find('[id*="calCGST"]').prop('disabled','true');
            $(this).parent().parent().find('[id*="calSGST"]').prop('disabled','true');
            $(this).parent().parent().find('[id*="AMTIGST"]').removeAttr('disabled');
            $(this).parent().parent().find('[id*="AMTIGST"]').prop('readonly','true');
            $(this).parent().parent().find('[id*="AMTCGST"]').prop('disabled','true');
            $(this).parent().parent().find('[id*="AMTSGST"]').prop('disabled','true');
            $(this).parent().parent().find('[id*="calCGST"]').val('0');
            $(this).parent().parent().find('[id*="calSGST"]').val('0');
            $(this).parent().parent().find('[id*="AMTCGST"]').val('0');
            $(this).parent().parent().find('[id*="AMTSGST"]').val('0');
            bindTotalValue();
            event.preventDefault();
          }
          else
          {
            $(this).parent().parent().find('[id*="calIGST"]').prop('disabled','true');
            $(this).parent().parent().find('[id*="calCGST"]').removeAttr('readonly');
            $(this).parent().parent().find('[id*="calSGST"]').removeAttr('disabled');
            $(this).parent().parent().find('[id*="calCGST"]').removeAttr('disabled');
            $(this).parent().parent().find('[id*="calSGST"]').removeAttr('readonly');
            $(this).parent().parent().find('[id*="AMTIGST"]').prop('disabled','true');
            $(this).parent().parent().find('[id*="AMTSGST"]').removeAttr('disabled');
            $(this).parent().parent().find('[id*="AMTCGST"]').removeAttr('disabled');
            $(this).parent().parent().find('[id*="AMTCGST"]').prop('readonly','true');
            $(this).parent().parent().find('[id*="AMTSGST"]').prop('readonly','true');
            $(this).parent().parent().find('[id*="calIGST"]').val('0');
            $(this).parent().parent().find('[id*="AMTIGST"]').val('0');
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
      event.preventDefault();
  });
});


$( "#btnSaveSQ" ).click(function() {
  var formSalesOrder = $("#frm_trn_sq");
  if(formSalesOrder.valid()){
 
    $("#FocusId").val('');
    var SQNO           =   $.trim($("#SQNO").val());
    var SQDT           =   $.trim($("#SQDT").val());
    var GLID_REF       =   $.trim($("#GLID_REF").val());
    var SLID_REF       =   $.trim($("#SLID_REF").val());
    var QVFDT          =   $.trim($("#QVFDT").val());
    var QVTDT          =   $.trim($("#QVTDT").val());
    var SPID_REF       =   $.trim($("#SPID_REF").val());
  
    var LASTSQDT       =   $.trim($("#LASTSQDT").val());
    var LASTSQDT_SHOW  =  moment(LASTSQDT).format('DD/MM/YYYY');  
    var SQDT_MESSAGE   =   "Selected Date should be equal to or greater than "+LASTSQDT_SHOW;
    var d = new Date(); 
    var todaydate =   ("0" + (d.getMonth() + 1)).slice(-2) + "/" +('0' + d.getDate()).slice(-2)  + "/" +  d.getFullYear()  ;
    var TODAYDATES  =  moment(todaydate).format('YYYY-MM-DD');
    var LASTSQDT_SHOW_2  =  moment(todaydate).format('DD/MM/YYYY'); 
    var SQDT_MESSAGE_2   =   "Selected Date should be equal to or less than "+LASTSQDT_SHOW_2;

    var QUOTATIONTYPE   =   $("input[name='LEAD']:checked").val();
    var LEAD_REF        =   $.trim($("#LEAD_REF").val());

    if(QUOTATIONTYPE =="WITH_LEAD" && LEAD_REF ===""){
     $("#FocusId").val('txtLeadpopup');
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Select Lead No.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if(SQNO ===""){
     $("#FocusId").val('SQNO');
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Enter Quotation No.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if(SQDT ===""){
     $("#FocusId").val('SQDT');
     $("#SQDT").val(todaydate);  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Select Quotation Date.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 } 
 else if(SQDT !="" && SQDT<LASTSQDT){
        $("#FocusId").val('SQDT');   
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text(SQDT_MESSAGE);
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
    } 
    else if(SQDT !="" && SQDT>TODAYDATES){
        $("#FocusId").val('SQDT'); 
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text(SQDT_MESSAGE_2);
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
    } 
 else if(SLID_REF ===""){
     $("#FocusId").val('txtgl_popup');
     $("#SLID_REF").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Select Customer');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if(QVFDT ===""){
     $("#FocusId").val('QVFDT');
     $("#QVFDT").val(todaydate);  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please select SQ From Date.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 } 
 else if(QVTDT ===""){
     $("#FocusId").val('QVTDT');
     $("#QVTDT").val(todaydate);  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please select SQ To Date.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }  
 else if(SPID_REF ===""){
     $("#FocusId").val('txtSPID_popup');
     $("#SPID_REF").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please select Sales Person.');
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

    
       
        $('#Material').find('.participantRow').each(function(){
            if($.trim($(this).find("[id*=ITEMID_REF]").val())!="")
            {
                allblank.push('true');
                    if($.trim($(this).find("[id*=popupMUOM]").val())!=""){
                        allblank2.push('true');
                          if($.trim($(this).find('[id*="SQ_QTY"]').val()) != "" && $.trim($(this).find('[id*="SQ_QTY"]').val()) > "0.000")
                          {
                            allblank3.push('true');
                          }
                          else
                          {
                            allblank3.push('false');
                            focustext3 = $(this).find("[id*=SQ_QTY]").attr('id');
                          }  
                    }
                    else{
                        allblank2.push('false');
                        focustext2 = $(this).find("[id*=popupMUOM]").attr('id');
                    } 
            }
            else
            {
                allblank.push('false');
                focustext1 = $(this).find("[id*=popupITEMID]").attr('id');
            } 
            if($.trim($(this).find("[id*=RATEPUOM]").val())!="")
            {
              allblank4.push('true');
            }
            else
            {
              allblank4.push('true');
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
                                focustext7 = $(this).find("[id*=tncdetvalue]").attr('id');
                              } 
                        } 
                }
                else
                {
                    allblank6.push('false');
                } 
            });
        }
        $('#udf').find('.participantRow4').each(function(){
              if($.trim($(this).find("[id*=UDFSQID_REF]").val())!="")
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
                                focustext9 = $(this).find("[id*=udfvalue]").attr('id');
                              }
                        }  
                }                
        });
        if($('#CTID_REF').val() !="")
        {
            $('#CT').find('.participantRow5').each(function(){
              if($.trim($(this).find("[id*=TID_REF]").val())!="")
                {
                    allblank10.push('true');
                        if($(this).find("[id*=calGST]").is(":checked") == true)
                        {
                          if($.trim($('#Tax_State').val())!="WithinState")
                          {
                            if($.trim($(this).find("[id*=calIGST]").val()) >"0.0000" && $.trim($(this).find("[id*=calIGST]").val()) != "")
                            {
                              allblank11.push('true');
                            }
                            else
                            {
                              allblank11.push('false');
                              focustext11 = $(this).find("[id*=calIGST]").attr('id');
                            }
                          }
                          else
                          {
                            if($.trim($(this).find("[id*=calCGST]").val()) >"0.0000" && $.trim($(this).find("[id*=calCGST]").val()) != "")
                            {
                              allblank11.push('true');
                            }
                            else
                            {
                              allblank11.push('false');
                              focustext11 = $(this).find("[id*=calIGST]").attr('id');
                            }
                            if($.trim($(this).find("[id*=calSGST]").val()) >"0.0000" && $.trim($(this).find("[id*=calSGST]").val()) != "")
                            {
                              allblank11.push('true');
                            }
                            else
                            {
                              allblank11.push('false');
                              focustext11 = $(this).find("[id*=calIGST]").attr('id');
                            }
                          }
                        } 
                }
                else
                {
                    allblank10.push('false');
                } 
            });
        }        
        if(jQuery.inArray("false", allblank) !== -1){
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
              $("#FocusId").val(focustext2);
            $("#alert").modal('show');
            $("#AlertMessage").text('Main UOM under Sales Quotation section is missing in Material Tab.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk');
            }
            else if(jQuery.inArray("false", allblank3) !== -1){
            $("#FocusId").val(focustext3);
            $("#alert").modal('show');
            $("#AlertMessage").text('Main UOM Quantity under Sales Quotation section is missing in Material Tab.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk');
            }
            else if(jQuery.inArray("false", allblank4) !== -1){
            $("#alert").modal('show');
            $("#AlertMessage").text('Please enter Rate per UOM in Material Tab.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk');
            }
            else if(jQuery.inArray("false", allblank5) !== -1){
            $("#alert").modal('show');
            $("#AlertMessage").text('Please enter GST Rate / Value in Material Tab.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk');
            }
            else if(jQuery.inArray("false", allblank6) !== -1){
            $("#TC_TAB").click();
            $("#alert").modal('show');
            $("#AlertMessage").text('Please select Terms & Condition Description in T&C Tab.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk');
            }
            else if(jQuery.inArray("false", allblank7) !== -1){
            $("#TC_TAB").click();
            $("#FocusId").val(focustext7);
            $("#alert").modal('show');
            $("#AlertMessage").text('Please enter Value / Comment in T&C Tab.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk');
            }
            else if(jQuery.inArray("false", allblank9) !== -1){
            $("#UDF_TAB").click();
            $("#FocusId").val(focustext9);
            $("#alert").modal('show');
            $("#AlertMessage").text('Please enter  Value / Comment in UDF Tab.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk');
            }
            else if(jQuery.inArray("false", allblank10) !== -1){
            $("#CT_TAB").click();
            $("#alert").modal('show');
            $("#AlertMessage").text('Please select Calculation Component in Calculation Template Tab.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk');
            }
            else if(jQuery.inArray("false", allblank11) !== -1){
            $("#CT_TAB").click();
            $("#FocusId").val(focustext11);
            $("#alert").modal('show');
            $("#AlertMessage").text('Please Enter GST Rate / Value in Calculation Template Tab.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk');
            }
            else if(checkPeriodClosing(36,$("#SQDT").val(),0) ==0){
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
                    $("#AlertMessage").text('Do you want to save the record.');
                    $("#YesBtn").data("funcname","fnSaveData");  //set dynamic fucntion name
                    $("#YesBtn").focus();

                    $("#OkBtn").hide();
                    highlighFocusBtn('activeYes');

                }
            

        }

    }
});
$( "#btnApprove" ).click(function() {
  var formSalesOrder = $("#frm_trn_sq");
  if(formSalesOrder.valid()){
 
    $("#FocusId").val('');
    var SQNO           =   $.trim($("#SQNO").val());
    var SQDT           =   $.trim($("#SQDT").val());
    var GLID_REF       =   $.trim($("#GLID_REF").val());
    var SLID_REF       =   $.trim($("#SLID_REF").val());
    var QVFDT          =   $.trim($("#QVFDT").val());
    var QVTDT          =   $.trim($("#QVTDT").val());
    var SPID_REF       =   $.trim($("#SPID_REF").val());
  
    var LASTSQDT       =   $.trim($("#LASTSQDT").val());
    var LASTSQDT_SHOW  =  moment(LASTSQDT).format('DD/MM/YYYY');  
    var SQDT_MESSAGE   =   "Selected Date should be equal to or greater than "+LASTSQDT_SHOW;
    var d = new Date(); 
    var todaydate =   ("0" + (d.getMonth() + 1)).slice(-2) + "/" +('0' + d.getDate()).slice(-2)  + "/" +  d.getFullYear()  ;
    var TODAYDATES  =  moment(todaydate).format('YYYY-MM-DD');
    var LASTSQDT_SHOW_2  =  moment(todaydate).format('DD/MM/YYYY'); 
    var SQDT_MESSAGE_2   =   "Selected Date should be equal to or less than "+LASTSQDT_SHOW_2;

 if(SQNO ===""){
     $("#FocusId").val('SQNO');
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Enter Quotation No.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if(SQDT ===""){
     $("#FocusId").val('SQDT');
     $("#SQDT").val(today);  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Select Quotation Date.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 } 
 else if(SQDT !="" && SQDT<LASTSQDT){
        $("#FocusId").val('SQDT');   
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text(SQDT_MESSAGE);
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
    } 
    else if(SQDT !="" && SQDT>TODAYDATES){
        $("#FocusId").val('SQDT'); 
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text(SQDT_MESSAGE_2);
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
    } 
 else if(SLID_REF ===""){
     $("#FocusId").val('txtgl_popup');
     $("#SLID_REF").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Select Customer');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if(QVFDT ===""){
     $("#FocusId").val('QVFDT');
     $("#QVFDT").val(today);  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please select SQ From Date.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 } 
 else if(QVTDT ===""){
     $("#FocusId").val('QVTDT');
     $("#QVTDT").val(today);  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please select SQ To Date.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }  
 else if(SPID_REF ===""){
     $("#FocusId").val('txtSPID_popup');
     $("#SPID_REF").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please select Sales Person.');
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

    
       
        $('#Material').find('.participantRow').each(function(){
            if($.trim($(this).find("[id*=ITEMID_REF]").val())!="")
            {
                allblank.push('true');
                    if($.trim($(this).find("[id*=popupMUOM]").val())!=""){
                        allblank2.push('true');
                          if($.trim($(this).find('[id*="SQ_QTY"]').val()) != "" && $.trim($(this).find('[id*="SQ_QTY"]').val()) > "0.000")
                          {
                            allblank3.push('true');
                          }
                          else
                          {
                            allblank3.push('false');
                            focustext3 = $(this).find("[id*=SQ_QTY]").attr('id');
                          }  
                    }
                    else{
                        allblank2.push('false');
                        focustext2 = $(this).find("[id*=popupMUOM]").attr('id');
                    } 
            }
            else
            {
                allblank.push('false');
                focustext1 = $(this).find("[id*=popupITEMID]").attr('id');
            } 
            if($.trim($(this).find("[id*=RATEPUOM]").val())!="")
            {
              allblank4.push('true');
            }
            else
            {
              allblank4.push('true');
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
                                focustext7 = $(this).find("[id*=tncdetvalue]").attr('id');
                              } 
                        } 
                }
                else
                {
                    allblank6.push('false');
                } 
            });
        }
        $('#udf').find('.participantRow4').each(function(){
              if($.trim($(this).find("[id*=UDFSQID_REF]").val())!="")
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
                                focustext9 = $(this).find("[id*=udfvalue]").attr('id');
                              }
                        }  
                }                
        });
        if($('#CTID_REF').val() !="")
        {
            $('#CT').find('.participantRow5').each(function(){
              if($.trim($(this).find("[id*=TID_REF]").val())!="")
                {
                    allblank10.push('true');
                        if($(this).find("[id*=calGST]").is(":checked") == true)
                        {
                          if($.trim($('#Tax_State').val())!="WithinState")
                          {
                            if($.trim($(this).find("[id*=calIGST]").val()) >"0.0000" && $.trim($(this).find("[id*=calIGST]").val()) != "")
                            {
                              allblank11.push('true');
                            }
                            else
                            {
                              allblank11.push('false');
                              focustext11 = $(this).find("[id*=calIGST]").attr('id');
                            }
                          }
                          else
                          {
                            if($.trim($(this).find("[id*=calCGST]").val()) >"0.0000" && $.trim($(this).find("[id*=calCGST]").val()) != "")
                            {
                              allblank11.push('true');
                            }
                            else
                            {
                              allblank11.push('false');
                              focustext11 = $(this).find("[id*=calIGST]").attr('id');
                            }
                            if($.trim($(this).find("[id*=calSGST]").val()) >"0.0000" && $.trim($(this).find("[id*=calSGST]").val()) != "")
                            {
                              allblank11.push('true');
                            }
                            else
                            {
                              allblank11.push('false');
                              focustext11 = $(this).find("[id*=calIGST]").attr('id');
                            }
                          }
                        } 
                }
                else
                {
                    allblank10.push('false');
                } 
            });
        }        
        if(jQuery.inArray("false", allblank) !== -1){
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
              $("#FocusId").val(focustext2);
            $("#alert").modal('show');
            $("#AlertMessage").text('Main UOM under Sales Quotation section is missing in Material Tab.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk');
            }
            else if(jQuery.inArray("false", allblank3) !== -1){
            $("#FocusId").val(focustext3);
            $("#alert").modal('show');
            $("#AlertMessage").text('Main UOM Quantity under Sales Quotation section is missing in Material Tab.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk');
            }
            else if(jQuery.inArray("false", allblank4) !== -1){
            $("#alert").modal('show');
            $("#AlertMessage").text('Please enter Rate per UOM in Material Tab.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk');
            }
            else if(jQuery.inArray("false", allblank5) !== -1){
            $("#alert").modal('show');
            $("#AlertMessage").text('Please enter GST Rate / Value in Material Tab.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk');
            }
            else if(jQuery.inArray("false", allblank6) !== -1){
            $("#TC_TAB").click();
            $("#alert").modal('show');
            $("#AlertMessage").text('Please select Terms & Condition Description in T&C Tab.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk');
            }
            else if(jQuery.inArray("false", allblank7) !== -1){
            $("#TC_TAB").click();
            $("#FocusId").val(focustext7);
            $("#alert").modal('show');
            $("#AlertMessage").text('Please enter Value / Comment in T&C Tab.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk');
            }
            else if(jQuery.inArray("false", allblank9) !== -1){
            $("#UDF_TAB").click();
            $("#FocusId").val(focustext9);
            $("#alert").modal('show');
            $("#AlertMessage").text('Please enter  Value / Comment in UDF Tab.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk');
            }
            else if(jQuery.inArray("false", allblank10) !== -1){
            $("#CT_TAB").click();
            $("#alert").modal('show');
            $("#AlertMessage").text('Please select Calculation Component in Calculation Template Tab.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk');
            }
            else if(jQuery.inArray("false", allblank11) !== -1){
            $("#CT_TAB").click();
            $("#FocusId").val(focustext11);
            $("#alert").modal('show');
            $("#AlertMessage").text('Please Enter GST Rate / Value in Calculation Template Tab.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk');
            }
            else if(checkPeriodClosing(36,$("#SQDT").val(),0) ==0){
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

     var trnsoForm = $("#frm_trn_sq");
    var formData = trnsoForm.serialize();
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$("#btnSaveSQ").hide(); 
$(".buttonload").show(); 
$("#btnApprove").prop("disabled", true);
$.ajax({
    url:'{{ route("transactionmodify",[36,"update"])}}',
    type:'POST',
    data:formData,
    success:function(data) {
      $(".buttonload").hide(); 
      $("#btnSaveSQ").show();   
      $("#btnApprove").prop("disabled", false);
       
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
      $(".buttonload").hide(); 
      $("#btnSaveSQ").show();   
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

     var trnsoForm = $("#frm_trn_sq");
    var formData = trnsoForm.serialize();
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$("#btnApprove").hide(); 
$(".buttonload_approve").show();  
$("#btnSaveSQ").prop("disabled", true);
$.ajax({
    url:'{{ route("transactionmodify",[36,"Approve"])}}',
    type:'POST',
    data:formData,
    success:function(data) {
      $("#btnApprove").show();  
$(".buttonload_approve").hide();  
$("#btnSaveSQ").prop("disabled", false);
       
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
      $("#btnApprove").show();  
      $(".buttonload_approve").hide();  
      $("#btnSaveSQ").prop("disabled", false);
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
    window.location.href = '{{route("transaction",[36,"index"]) }}';
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


$(document).ready(function(){
  CalculationOnloadTime();
});

function CalculationOnloadTime(){
  $('#Material').find('.participantRow').each(function(){

var totalvalue = 0.00;
var itemid = $(this).find('[id*="ITEMID_REF"]').val();

var mqty = $(this).find('[id*="SQ_QTY"]').val();

  var altuomid = $(this).find('[id*="ALT_UOMID_REF"]').val();
  var txtid = $(this).find('[id*="ALT_UOMID_QTY"]').attr('id');
  var irate = $(this).find('[id*="RATEPUOM"]').val();
  $(this).find('[id*="IGSTAMT"]').val('0');
  $(this).find('[id*="CGSTAMT"]').val('0');
  $(this).find('[id*="SGSTAMT"]').val('0');
  var tamt = parseFloat(parseFloat(mqty)*parseFloat(irate)).toFixed(2);
  var dispercnt = $(this).find('[id*="DISCPER"]').val();
  var disamt = 0 ;      
  if (dispercnt != '' && dispercnt != '.0000')
  {
     disamt =  parseFloat((parseFloat(tamt)*parseFloat(dispercnt))/100).toFixed(2);
  }
  else if ($(this).find('[id*="DISCOUNT_AMT"]').val() != '' && $(this).find('[id*="DISCOUNT_AMT"]').val() != '0.00')
  {
     disamt = $(this).find('[id*="DISCOUNT_AMT"]').val();
  }

  tamt = parseFloat(parseFloat(tamt) - parseFloat(disamt)).toFixed(2);   
  var tp1 = $(this).find('[id*="IGST_"]').val();
  var tp2 = $(this).find('[id*="CGST_"]').val();
  var tp3 = $(this).find('[id*="SGST_"]').val();
  var tp1amt = parseFloat((tamt * tp1)/100).toFixed(2);
  var tp2amt = parseFloat((tamt * tp2)/100).toFixed(2);
  var tp3amt = parseFloat((tamt * tp3)/100).toFixed(2);
  var taxamt = parseFloat(parseFloat(tp1amt) + parseFloat(tp2amt) + parseFloat(tp3amt)).toFixed(2); 
  var totamt = parseFloat(parseFloat(tamt) + parseFloat(taxamt)).toFixed(2);

  if(altuomid!=''){
        
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url:'{{route("transaction",[36,"getaltuomqty"])}}',
                type:'POST',
                data:{'id':altuomid, 'itemid':itemid, 'mqty':mqty},
                success:function(data) {
                  if(intRegex.test(data)){
                      data = (data +'.000');
                  }
                  $("#"+txtid).val(data);                        
                },
                error:function(data){
                  console.log("Error: Something went wrong.");
                  $("#"+txtid).val('');                        
                },
            }); 
                
        }

        

if(intRegex.test($(this).find('[id*="sq_qty"]').val())){
  $(this).val($(this).find('[id*="SQ_QTY"]').val()+'.000');
  
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

$(this).find('[id*="DISAFTT_AMT"]').val(tamt);
$(this).find('[id*="TOT_AMT"]').val(totamt);
$(this).find('[id*="TGST_AMT"]').val(taxamt);
$(this).find('[id*="IGSTAMT"]').val(tp1amt);
$(this).find('[id*="CGSTAMT"]').val(tp2amt);
$(this).find('[id*="SGSTAMT"]').val(tp3amt);

bindTotalValueOnload();
if($('#CTID_REF').val()!=''){
  bindGSTCalTemplateOnload();
}
bindTotalValueOnload();

});

}

function bindTotalValueOnload(){
    var totalvalue = 0.00;
    var tvalue = 0.00;
    var ctvalue = 0.00;
    var ctgstvalue = 0.00;
    $('#Material').find('.participantRow').each(function()
    {
      tvalue = $(this).find('[id*="TOT_AMT"]').val();
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
    $('#TotalValue').val(totalvalue);

    return true;
}

function bindGSTCalTemplateOnload(){ 
  $('#CT').find('.participantRow5').each(function(){ 
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
      
      var IGST = $(this).find('[id*=calIGST]').val();
      var CGST = $(this).find('[id*=calCGST]').val();
      var SGST = $(this).find('[id*=calSGST]').val();
      if(IGST == '.0000'){
        IGST = $('#IGST_0').val();
      }
      if(CGST == '.0000'){
        CGST = $('#CGST_0').val();
      }
      if(SGST == '.0000'){
        SGST = $('#SGST_0').val();
      }
      
      $('#Material').find('.participantRow').each(function()
      {                       
        var TaxableAmount = $(this).find('[id*="DISAFTT_AMT"]').val();
        if (!isNaN(TaxableAmount) && TaxableAmount.length !== 0) {
          netTaxableAmount += parseFloat(TaxableAmount);
          }                      
        
        var GSTAmount = $(this).find('[id*="TGST_AMT"]').val();
        if (!isNaN(GSTAmount) && GSTAmount.length !== 0) {
          netGSTAmount += parseFloat(GSTAmount);
          }
        
        var TotalAmount = $(this).find('[id*="TOT_AMT"]').val();
        if (!isNaN(TotalAmount) && TotalAmount.length !== 0) {
          netTotalAmount += parseFloat(TotalAmount);
          }
      })
      
      
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
  $('#Material').find('.participantRow').each(function()
  {
    tvalue = $(this).find('[id*="TOT_AMT"]').val();
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
  $('#TotalValue').val(totalvalue);
  return true;
}



/*==================================Lead NO POPUP STARTS HERE====================================*/
let Lead = "#LeadOrderTable2";
      let Lead2 = "#LeadOrder";
      let Leadheaders = document.querySelectorAll(Lead2 + " th");
      // Sort the table element when clicking on the table headers
      Leadheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(Lead, ".clsspid_reasoncode1", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function LeadDocFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("LeadNo");
        filter = input.value.toUpperCase();
        table = document.getElementById("LeadOrderTable2");
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

  function LeadDTFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("LeadDT");
        filter = input.value.toUpperCase();
        table = document.getElementById("LeadOrderTable2");
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


  $("#Lead_closePopup").click(function(event){
        $("#Lead_popup").hide();
      });

  function bindLeadEvents(){
      $(".clsspid_lead").click(function(){  
        var fieldid        =   $(this).attr('id');
        var txtval         =   $("#txt"+fieldid+"").val();
        var texdesc        =   $("#txt"+fieldid+"").data("desc");
        var QUOTATIONTYPE  =   $("#txt"+fieldid+"").data("custtype");
        var texcode        =   $("#txt"+fieldid+"").data("code"); 
        var slid_ref       =   $("#txt"+fieldid+"").data("slid_ref"); 
        var glid_ref       =   $("#txt"+fieldid+"").data("glid_ref"); 
        var custtype       =   $("#txt"+fieldid+"").data("custtype"); 
        var slname         =   $("#txt"+fieldid+"").data("slname"); 
  
        $('#txtLeadpopup').val(texcode);
        $('#LEAD_REF').val(txtval);        
        $('#QUOTATIONTYPE').val(QUOTATIONTYPE);              
        $("#Lead_popup").hide(); 
        $('#txtgl_popup').val(slname);        
        $('#GLID_REF').val(glid_ref);        
        $('#SLID_REF').val(slid_ref);   
        if(custtype.toUpperCase() ==="CUSTOMER"){   
        $("#customer_prospect").html("Customer*");   
        }else{
          $("#customer_prospect").html("Prospect*");      

        }
          
        var customid = slid_ref;
              if(customid!='' && custtype.toUpperCase()=="CUSTOMER"){
                $("#CREDITDAYS").val('');
                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  });
                  $.ajax({
                      url:'{{route("transaction",[36,"getcreditdays"])}}',
                      type:'POST',
                      data:{'id':customid},
                      success:function(data) {
                        $("#CREDITDAYS").val(data);                        
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                        $("#CREDITDAYS").val('');                        
                      },
                  }); 


                  $("#tbody_BillTo").html('');
                  $.ajax({
                      url:'{{route("transaction",[36,"getBillAddress"])}}',
                      type:'POST',
                      data:{'id':customid},
                      success:function(data) {
                        $("#tbody_BillTo").html(data);
                        BindBillAddress();
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                        $("#tbody_BillTo").html('');
                      },
                  });  

                  $("#tbody_ShipTo").html('');
                  $.ajax({
                      url:'{{route("transaction",[36,"getShipAddress"])}}',
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

                }



                  
              

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
                      url:'{{route("transaction",[36,"getShipTo"])}}',
                      type:'POST',
                      data:{'id':customid,'type':custtype.toUpperCase(),'leadid_ref':txtval},
                      success:function(data) {
                        $("#div_shipto").html(data);
                        
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                        $("#txtSHIPTO").hide();
                        $("#txtSHIPTO1").show();
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
                      url:'{{route("transaction",[36,"getBillTo"])}}',
                      type:'POST',
                      data:{'id':customid,'type':custtype.toUpperCase(),'leadid_ref':txtval},
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
               



        clearGrid(); 
        event.preventDefault();
      });
  }

  

  $('#txtLeadpopup').on('click',function(event){   
            var QUOTATIONTYPE = $("input[name='LEAD']:checked").val();
            if(QUOTATIONTYPE != "WITH_LEAD")
            {
              $("#FocusId").val('WITH_LEAD');
              $("#ProceedBtn").focus();
              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn1").show();
              $("#AlertMessage").text('Please Select With Lead First.');
              $("#alert").modal('show');
              $("#OkBtn1").focus();
              return false;

            }
            
                $("#Dataresult").html('');
                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  });
                  $("#Data_seach").show();
                  $.ajax({
                      url:'{{route("transaction",[$FormId,"get_Lead"])}}',
                      type:'POST',
                      data:{},
                      success:function(data) {                                
                        $("#Data_seach").hide();
                        $("#Dataresult").html(data);   
                        showSelectedCheck($("#LEAD_REF").val(),"lead");
                        bindLeadEvents();                                        
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                        $("#Dataresult").html('');                        
                      },
                  }); 

                  showSelectedCheck($("#LEAD_REF").val(),"lead");
                  $("#Lead_popup").show();         
    });

/*==================================Lead NO POPUP ENDS HERE====================================*/

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

            var TCClone = $('#hdnTC').val();
            var CTClone = $('#hdnCT').val();      

              
              $('#TC').html(TCClone);
              $('#CT').html(CTClone);
              $('#TotalValue').val('0.00');
              $('#Row_Count1').val('1');
              $('#Row_Count2').val('1');
              $('#Row_Count4').val('1');
              
                if ($('#DirectSQ').is(":checked") == true){
                    $('#Material').find('[id*="txtSE_popup"]').prop('disabled','true')
                    $('#Material').find('[id*="txtSE_popup"]').val('');
                    $('#Material').find('[id*="SEQID_REF"]').val('');
                    event.preventDefault();
                }
                else
                {
                    $('#Material').find('[id*="txtSE_popup"]').removeAttr('disabled');
                    event.preventDefault();
                }
}


$('#WITHOUT_LEAD').change(function(){
        $("#customer_prospect").html("Customer*");   
        $("#txtBILLTO").val('');
        $("#BILLTO").val('');
        $("#txtBILLTO1").val('');
        $("#BILLTO1").val('');
        $("#txtSHIPTO").val('');
        $("#SHIPTO").val('');
        $("#txtSHIPTO1").val('');
        $("#SHIPTO1").val('');   
        $("#txtLeadpopup").val('');
        $("#LEAD_REF").val('');
        $("#QVFDT").val('');
        $("#QVTDT").val('');
        $("#txtgl_popup").val('');
        $("#GLID_REF").val('');
        $("#SLID_REF").val('');
        $("#txtSPID_popup").val('');
        $("#SPID_REF").val('');
        $("#REFNO").val('');
        $("#Tax_State").val('');
        $("#REMARKS").val('');
        $("#TotalValue").val('');
        //$("#frm_trn_sq")[0].reset();
        clearGrid();
        //$("#frm_trn_sq").find('input:hidden').val('');
        //$("#frm_trn_sq").find('input:text').val('');
        $("#WITHOUT_LEAD").prop("checked",true);
      }); 

      $('#WITH_LEAD').change(function(){
        $("#txtBILLTO").val('');
        $("#BILLTO").val('');
        $("#txtBILLTO1").val('');
        $("#BILLTO1").val('');
        $("#txtSHIPTO").val('');
        $("#SHIPTO").val('');
        $("#txtSHIPTO1").val('');
        $("#SHIPTO1").val('');
        $("#txtLeadpopup").val('');
        $("#LEAD_REF").val('');
        $("#QVFDT").val('');
        $("#QVTDT").val('');
        $("#txtgl_popup").val('');
        $("#GLID_REF").val('');
        $("#SLID_REF").val('');
        $("#txtSPID_popup").val('');
        $("#SPID_REF").val('');
        $("#REFNO").val('');
        $("#Tax_State").val('');
        $("#REMARKS").val('');
        $("#TotalValue").val('');
        clearGrid();
        $("#WITH_LEAD").prop("checked",true);

      }); 


            
//===================================Price Type Popup Starts here =========================================================
$('#Material').on('click','[id*="RATEPUOM"]',function(event){
$('#hdn_STRid').val($(this).attr('id'));
var fieldid = $(this).parent().parent().find('[id*="RATEPUOM"]').attr('id');
var ROWID=fieldid.split("_").pop(0); 
var ITEMID_REF=$("#ITEMID_REF_"+ROWID).val();
if(ITEMID_REF==='')
{
  $("#FocusId").val('popupITEMID_'+ROWID);
  $("#YesBtn").hide();
  $("#NoBtn").hide();
  $("#OkBtn").hide();
  $("#OkBtn1").show();
  $("#AlertMessage").text('Please Select Item Code First.');
  $("#alert").modal('show');
  $("#OkBtn1").focus();
  highlighFocusBtn('activeOk1');
  return false;
}
var click_button="clssSTRid";


  $("#STRpopup").show();
  showSelectedCheck($("#"+fieldid).val(),"SELECT");
  $("#tbody_STR").html('loading...');

  $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  })

  $.ajax({
      url:'{{route("transaction",[$FormId,"get_Price"])}}',
      type:'POST',
      data:{'fieldid':fieldid,'class_name':click_button,ITEMID_REF:ITEMID_REF},
      success:function(data) {
        $("#tbody_STR").html(data);
        BindPrice();

      },
      error:function(data){
        console.log("Error: Something went wrong.");
        $("#tbody_STR").html('');
      },
  });



});

$("#STR_closePopup").click(function(event){
  $("#STRpopup").hide();
});

function BindPrice(){

$(".clssSTRid").click(function(){
  var fieldid          = $(this).attr('id');
  var txtval           = $("#txt"+fieldid+"").val();
  var texdesc          = $("#txt"+fieldid+"").data("desc");

  var txtid     = $('#hdn_STRid').val();
  $('#'+txtid).val(texdesc); 
  $("#STRpopup").hide();
  $("#STRcodesearch").val(''); 
  $("#STRnamesearch").val(''); 
  $(".blurRate").blur();
  event.preventDefault();

});
}

let STRNOTable2 = "#STRNOTable2";
let STRNOTable = "#STRNOTable";
let QCPheaders1 = document.querySelectorAll(STRNOTable + " th");

QCPheaders1.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(STRNOTable2, ".clssSTRid", "td:nth-child(" + (i + 1) + ")");
  });
});

function STRCodeFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("STRcodesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("STRNOTable2");
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



function STRDTFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("STRDTsearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("STRNOTable2");
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

//====================================Price Section Ends here =========================================================



  
/*==================================Dealer POPUP STARTS HERE====================================*/
let Dealer = "#DealerOrderTable2";
      let Dealer2 = "#DealerOrder";
      let Dealerheaders = document.querySelectorAll(Dealer2 + " th");
      // Sort the table element when clicking on the table headers
      Dealerheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(Dealer, ".clsspid_reasoncode1", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function DealerDocFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("DealerNo");
        filter = input.value.toUpperCase();
        table = document.getElementById("DealerOrderTable2");
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

  function DealerNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("DealerName");
        filter = input.value.toUpperCase();
        table = document.getElementById("DealerOrderTable2");
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


  $("#Dealer_closePopup").click(function(event){
        $("#Dealer_popup").hide();
      });

  function bindDealerEvents(){
      $(".clsspid_dealer").click(function(){       

        var fieldid     = $(this).attr('id');
        var txtval      =    $("#txt"+fieldid+"").val();
        var texdesc     =   $("#txt"+fieldid+"").data("desc");
        var texcode     =   $("#txt"+fieldid+"").data("code"); 
        var commission  =   $("#txt"+fieldid+"").data("desc1"); 
    
        $('#txtDealerpopup').val(texcode);
        $('#DEALERID_REF').val(txtval);        
        $('#DEALER_COMMISSION').val(commission);   
        bindTotalValue();      
        $("#Dealer_popup").hide();   
        event.preventDefault();
      });
  }

  

  $('#txtDealerpopup').on('click',function(event){           
                $("#Dataresult_dealer").html('');
                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  });
                  $("#Data_seach_dealer").show();
                  $.ajax({
                      url:'{{route("transaction",[$FormId,"get_Dealer"])}}',
                      type:'POST',
                      data:{},
                      success:function(data) {                                
                        $("#Data_seach_dealer").hide();
                        $("#Dataresult_dealer").html(data);   
                        showSelectedCheck($("#DEALERID_REF").val(),"dealer");
                        bindDealerEvents();                                        
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                        $("#Dataresult_dealer").html('');                        
                      },
                  }); 

                  showSelectedCheck($("#DEALERID_REF").val(),"dealer");
                  $("#Dealer_popup").show();         
    });

/*==================================Dealer POPUP ENDS HERE====================================*/





    
/*==================================Project POPUP STARTS HERE====================================*/
let Project = "#ProjectOrderTable2";
      let Project2 = "#ProjectOrder";
      let Projectheaders = document.querySelectorAll(Project2 + " th");
      // Sort the table element when clicking on the table headers
      Projectheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(Project, ".clsspid_reasoncode1", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function ProjectDocFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("ProjectNo");
        filter = input.value.toUpperCase();
        table = document.getElementById("ProjectOrderTable2");
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

  function ProjectNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("ProjectName");
        filter = input.value.toUpperCase();
        table = document.getElementById("ProjectOrderTable2");
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


  $("#Project_closePopup").click(function(event){
        $("#Project_popup").hide();
      });

  function bindProjectEvents(){
      $(".clsspid_project").click(function(){       

        var fieldid     = $(this).attr('id');
        var txtval      =    $("#txt"+fieldid+"").val();
        var texdesc     =   $("#txt"+fieldid+"").data("desc");
        var texcode     =   $("#txt"+fieldid+"").data("code"); 
    
        $('#txtProjectpopup').val(texcode);
        $('#PROJECTID_REF').val(txtval); 
        $("#Project_popup").hide();   
        event.preventDefault();
      });
  }

  

  $('#txtProjectpopup').on('click',function(event){           
                $("#Dataresult_project").html('');
                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  });
                  $("#Data_seach_project").show();
                  $.ajax({
                      url:'{{route("transaction",[$FormId,"get_Project"])}}',
                      type:'POST',
                      data:{},
                      success:function(data) {                                
                        $("#Data_seach_project").hide();
                        $("#Dataresult_project").html(data);   
                        showSelectedCheck($("#PROJECTID_REF").val(),"project");
                        bindProjectEvents();                                        
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                        $("#Dataresult_project").html('');                        
                      },
                  }); 

                  showSelectedCheck($("#PROJECTID_REF").val(),"project");
                  $("#Project_popup").show();         
    });

/*==================================Project POPUP ENDS HERE====================================*/


function bindTotalValue()
    {
      var totalvalue = 0.00;
      var tvalue = 0.00;
      var ctvalue = 0.00;
      var ctgstvalue = 0.00;
      var dealer_commission = 0.00;
      $('#Material').find('.participantRow').each(function()
      {
        tvalue = $(this).find('[id*="TOT_AMT"]').val();
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
      var DealerPer     = $("#DEALER_COMMISSION").val(); 
 
      if(DealerPer != '' && DealerPer > 0 && totalvalue > 0){
        dealer_commission = (parseFloat(totalvalue) * parseFloat(DealerPer)/100).toFixed(2);
        $('#DEALER_COMMISSION_AMT').val(dealer_commission);
      }

      $('#TotalValue').val(totalvalue);
    }
</script>


@endpush