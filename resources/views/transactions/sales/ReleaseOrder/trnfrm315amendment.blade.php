@extends('layouts.app')
@section('content')
<!-- <form id="frm_trn_so" onsubmit="return validateForm()"  method="POST" class="needs-validation"  >     -->

    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('transaction',[38,'index'])}}" class="btn singlebt">Sales Order</a>
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
                        <button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-thumbs-o-up"></i> Approved</button>
                        <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                        <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                </div>
            </div>
    </div><!--topnav-->	
    <!-- multiple table-responsive table-wrapper-scroll-y my-custom-scrollbar -->
    <div class="container-fluid purchase-order-view">
        <form id="frm_trn_so"  method="POST">   
            @csrf
            {{isset($objSO->SOID[0]) ? method_field('PUT') : '' }}
            <div class="container-fluid filter">

                    <div class="inner-form">

                      <div class="inner-form">

                        <div class="row">
                          <div class="col-lg-2 pl"><p>SOA No</p></div>
                          <div class="col-lg-1 pl">
                            <input type="text" name="SOANO" id="SOANO" value="{{ $MAXSOANO }}" class="form-control mandatory" maxlength="15" autocomplete="off" style="text-transform:uppercase" autofocus readonly >
                            <input type="hidden" name="SOID_REF" id="SOID_REF" value="{{ $objSO->SOID }}" class="form-control" maxlength="15"  autofocus readonly >
                          </div>

                          <div class="col-lg-1 pl col-md-offset-1"><p>SOA Date</p></div>
                          <div class="col-lg-2 pl">
                              <input type="date" name="SOA_DT" id="SOADT"  class="form-control mandatory" autocomplete="off" placeholder="dd/mm/yyyy" >
                          </div>

                          <div class="col-lg-2 pl"><p>Customer Amendment Ref No</p></div>
                          <div class="col-lg-2 pl">
                            <input type="text" name="CUSTOMERAREFNO" id="CUSTOMERAREFNO" class="form-control " autocomplete="off" maxlength="20"  >
                          </div>


                        </div>

                        <div class="row">
                         
                          <div class="col-lg-2 pl "><p>Customer Amendment Date</p></div>
                          <div class="col-lg-2 pl">
                              <input type="date" name="CUSTOMERAREFDT" id="CUSTOMERAREFDT"  class="form-control " autocomplete="off" placeholder="dd/mm/yyyy" readonly>
                          </div>

                          <div class="col-lg-2 pl"><p>Reason to amend  SO</p></div>
                          <div class="col-lg-3 pl">
                              <input type="text" name="REASONOFSOA" id="REASONOFSOA" class="form-control mandatory" autocomplete="off" maxlength="200"  >
                          </div>

                        </div>

                        <div class="row">
                            <div class="col-lg-2 pl"><p>SO No</p></div>
                            <div class="col-lg-1 pl">
                                <input type="text" name="SONO" id="SONO" value="{{ $objSO->SONO }}" class="form-control mandatory" maxlength="15" autocomplete="off" style="text-transform:uppercase" autofocus readonly >
                            </div>
                            
                            <div class="col-lg-1 pl col-md-offset-1"><p>SO Date</p></div>
                            <div class="col-lg-2 pl">
                                <input type="date" name="SODT" id="SODT" value="{{ $objSO->SODT }}" class="form-control mandatory" autocomplete="off" placeholder="dd/mm/yyyy" readonly>
                            </div>
                            
                            <div class="col-lg-2 pl"><p>GL</p></div>
                            <div class="col-lg-1 pl">
                            <input type="text" name="GLID_popup" id="txtgl_popup" class="form-control mandatory" value="{{$objglcode2->GLCODE}}"  autocomplete="off" disabled />
                            <input type="hidden" name="GLID_REF" id="GLID_REF" class="form-control" autocomplete="off" value="{{ $objSO->GLID_REF }}" />
                               
                            </div>
                            
                            <div class="col-lg-1 pl"><p>SL</p></div>
                            <div class="col-lg-1 pl">
                                <input type="text" name="SubGl_popup" id="txtsubgl_popup" class="form-control mandatory" value="{{$objsubglcode->SGLCODE}}"  autocomplete="off" disabled/>
                                <input type="hidden" name="SLID_REF" id="SLID_REF" class="form-control" autocomplete="off"  value="{{ $objSO->SLID_REF }}"/>
                                <input type="hidden" name="hdnmaterial" id="hdnmaterial" class="form-control" autocomplete="off" />                                                                
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-lg-2 pl"><p>FC</p></div>
                            <div class="col-lg-1 pl">
                                <input type="checkbox" name="LBLSOFC" id="LBLSOFC" class="form-checkbox" value="{{$objSO->SOFC == 1 ? 1 : 0}}"  {{$objSO->SOFC == 1 ? 'checked' : ''}} disabled >
                                <input type="hidden" name="SOFC" id="SOFC" value="{{$objSO->SOFC == 1 ? 1 : 0}}" class="form-control">
                            </div>
                            
                            <div class="col-lg-1 pl col-md-offset-1"><p>Currency</p></div>
                            <div class="col-lg-2 pl" id="divcurrency" >
                                @if (count($objsocurrency) > 0)
                                <input type="text" name="CRID_popup" id="txtCRID_popup" class="form-control"  autocomplete="off" value="{{ $objsocurrency[0] }}"   disabled/>
                                @else
                                <input type="text" name="CRID_popup" id="txtCRID_popup" class="form-control"  autocomplete="off"   disabled/>
                                @endif
                                <input type="hidden" name="CRID_REF" id="CRID_REF" class="form-control" autocomplete="off"   value="{{ $objSO->CRID_REF }}" />
                                
                            </div>
                            
                            <div class="col-lg-2 pl"><p>Conversion Factor</p></div>
                            <div class="col-lg-2 pl">
                                <input type="text" name="CONVFACT" id="CONVFACT" class="form-control" maxlength="100" autocomplete="off" value="{{ $objSO->CONVFACT }}" readonly/>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-lg-2 pl"><p>Order Validity From </p></div>
                            <div class="col-lg-2 pl">
                                <input type="date" name="OVFDT" id="OVFDT" class="form-control mandatory" autocomplete="off" placeholder="dd/mm/yyyy" value="{{ $objSO->OVFDT }}" readonly>
                            </div>
                            
                            <div class="col-lg-1 pl"><p>To </p></div>
                            <div class="col-lg-2 pl">
                                <input type="date" name="OVTDT" id="OVTDT" class="form-control mandatory" autocomplete="off"  placeholder="dd/mm/yyyy" value="{{ $objSO->OVTDT }}" min="{{ $objSO->OVFDT }}" >
                            </div>
                            
                            <div class="col-lg-2 pl"><p>Customer PO No</p></div>
                            <div class="col-lg-2 pl">
                                <input type="text" name="CUSTOMERPONO" id="CUSTOMERPONO" class="form-control" autocomplete="off" style="text-transform:uppercase" value="{{ $objSO->CUSTOMERPONO }}">
                            </div>
                        </div>
                        
                        <div class="row">	
                            <div class="col-lg-2 pl"><p>Date </p></div>
                            <div class="col-lg-2 pl">
                                <input type="date" name="CUSTOMERDT" id="CUSTOMERDT" class="form-control " autocomplete="off" placeholder="dd/mm/yyyy" value="{{ $objSO->CUSTOMERDT }}">
                            </div>
                            
                            <div class="col-lg-1 pl"><p>Sales Person</p></div>
                            <div class="col-lg-2 pl">
                                <input type="text" name="SPID_popup" id="txtSPID_popup" class="form-control mandatory"  autocomplete="off" value="{{$objSPID[0]}}"  readonly/>
                                <input type="hidden" name="SPID_REF" id="SPID_REF" class="form-control" autocomplete="off" value="{{ $objSO->SPID_REF }}" />
                            </div>
                            
                            <div class="col-lg-2 pl"><p>Ref No </p></div>
                            <div class="col-lg-1 pl">
                                <input type="text" name="REFNO" id="REFNO" class="form-control" maxlength="100" value="{{ $objSO->REFNO }}" autocomplete="off" style="text-transform:uppercase">
                            </div>
                            
                            <div class="col-lg-1 pl"><p>Credit Days</p></div>
                            <div class="col-lg-1 pl">
                                <input type="text" name="CREDITDAYS" id="CREDITDAYS" class="form-control" autocomplete="off" value="{{ $objSO->CREDITDAYS }}">
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-lg-2 pl"><p>Bill To </p></div>
                            <div class="col-lg-2 pl" id="div_billto">
                                <input type="text" name="txtBILLTO" id="txtBILLTO" class="form-control"  autocomplete="off" value="{{$objBillAddress[0]}}" readonly disabled />
                                <input type="hidden" name="BILLTO" id="BILLTO" class="form-control" autocomplete="off" value="{{ $objSO->BILLTO }}" />
                            </div>
                           
                            <div class="col-lg-1 pl"><p>Ship To</p></div>
                            <div class="col-lg-2 pl" id="div_shipto">
                                <input type="text" name="txtSHIPTO" id="txtSHIPTO" class="form-control"  autocomplete="off" value="{{$objBillAddress[0]}}" readonly disabled />
                                <input type="hidden" name="SHIPTO" id="SHIPTO" class="form-control" autocomplete="off" value="{{ $objSO->SHIPTO }}" />
                                <input type="hidden" name="Tax_State" id="Tax_State" class="form-control" autocomplete="off" value=" {{$TAXSTATE[0]}}"   />
                            </div>
                            
                            <div class="col-lg-2 pl"><p>Remarks</p></div>
                            <div class="col-lg-3 pl">
                                <input type="text" name="REMARKS" id="REMARKS" class="form-control" autocomplete="off" maxlength="200" value="{{ $objSO->REMARKS }}"  >
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-2 pl"><p>Direct Sales Order</p></div>
                            <div class="col-lg-2 pl">
                                  <input type="checkbox" name="DirectSO" id="DirectSO" class="form-checkbox" disabled>
                                  <input type="hidden" name="HDNDirSO" id="HDNDirSO" class="form-control" readonly >
                            </div>
                           
                            <div class="col-lg-1 pl"><p>Total Value</p></div>
                            <div class="col-lg-2 pl">
                                <input type="text" name="TotalValue" id="TotalValue" class="form-control"  autocomplete="off" readonly  />
                            </div>
                        </div>
                    </div>

                    <div class="container-fluid">

                        <div class="row">
                            <ul class="nav nav-tabs">
                                <li class="active"><a data-toggle="tab" href="#Material">Material</a></li>
                                <li><a data-toggle="tab" href="#TC">T & C</a></li>
                                <li><a data-toggle="tab" href="#udf">UDF</a></li>
                                <li><a data-toggle="tab" href="#CT">Calculation Template</a></li>
                                <li><a data-toggle="tab" href="#PaymentSlabs">Payment Slabs</a></li>	
                            </ul>
                            
                            
                            
                            <div class="tab-content">

                                <div id="Material" class="tab-pane fade in active">
                                    <div class="table-responsive table-wrapper-scroll-y" style="height:280px;margin-top:10px;" >
                                        <table id="example2" class="display nowrap table table-striped table-bordered itemlist w-200" width="100%" style="height:auto !important;">
                                            <thead id="thead1"  style="position: sticky;top: 0">
                                                    
                                                    <tr>
                                                        <th colspan="4"></th>
                                                        <th colspan="4">Sales Quotation</th>
                                                        <th colspan="4">Sales Order</th>
                                                        <th colspan="13"></th>
                                                        
                                                    </tr>
                                                <tr>
                                                    <th rowspan="2">SQ<input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1" ></th>
                                                    <th rowspan="2">Item Code</th>
                                                    <th rowspan="2">Item Name</th>
                                                    <th rowspan="2">Item Specification</th>
                                                    <th rowspan="2">Main UOM</th>
                                                    <th rowspan="2">Qty (Main UOM)</th>
                                                    <th rowspan="2">ALT UOM</th>
                                                    <th rowspan="2">Qty (Alt UOM)</th>
                                                    <th rowspan="2">Main UOM</th>
                                                    <th rowspan="2" hidden>Consumed Qty</th>
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
                                            @if(!empty($objSOMAT))
                                                @foreach($objSOMAT as $key => $row)
                                                    <tr  class="participantRow">
                                                        <td hidden>
                                                        <input  class="form-control" type="hidden" name={{"SOMATID_".$key}} id ={{"SOMATID_".$key}} maxlength="100" value="{{ $row->SOMATID }}" autocomplete="off"   >
                                                        </td>
                                                        <td style="text-align:center;" >
                                                        <input type="text" name={{"txtSQ_popup_".$key}} id={{"txtSQ_popup_".$key}} class="form-control sqpopup"           value="{{ $row->SQNO }}" autocomplete="off" style="width: 100px" disabled readonly/></td>
                                                        <td hidden><input type="hidden" name={{"SQA_".$key}} id={{"SQA_".$key}} class="form-control"                      value="{{ $row->SQA }}" autocomplete="off" /></td>
                                                        <td hidden><input type="hidden" name={{"SEQID_REF_".$key}} id={{"SEQID_REF_".$key}} class="form-control"          value="{{ $row->SEQID_REF }}" autocomplete="off" /></td>
                                                        <td><input type="text" name={{"popupITEMID_".$key}} id={{"popupITEMID_".$key}} class="form-control itempopup"     value="{{ $row->ICODE }}"  autocomplete="off"  readonly disabled/></td>
                                                        <td hidden><input type="hidden" name={{"ITEMID_REF_".$key}} id={{"ITEMID_REF_".$key}} class="form-control"        value="{{ $row->ITEMID_REF }}" autocomplete="off" /></td>
                                                        <td><input type="text" name={{"ItemName_".$key}} id={{"ItemName_".$key}} class="form-control"  autocomplete="off" value="{{ $row->ItemName }}" readonly/></td>
                                                        <td><input type="text" name={{"Itemspec_".$key}} id={{"Itemspec_".$key}} class="form-control"  autocomplete="off" value="{{ $row->ITEMSPECI }}" /></td>
                                                        <td><input type="text" name={{"SQMUOM_".$key}} id={{"SQMUOM_".$key}} class="form-control"  autocomplete="off"     value="{{ $row->SQMUOM }}" readonly/></td>
                                                        <td><input type="text" name={{"SQMUOMQTY_".$key}} id={{"SQMUOMQTY_".$key}} class="form-control" maxlength="13"    value="{{ $row->SQMUOMQTY }}"  autocomplete="off"  readonly/></td>
                                                        <td><input type="text" name={{"SQAUOM_".$key}} id={{"SQAUOM_".$key}} class="form-control"  autocomplete="off"     value="{{ $row->SQAUOM }}"  readonly/></td>
                                                        <td><input type="text" name={{"SQAUOMQTY_".$key}} id={{"SQAUOMQTY_".$key}} class="form-control" maxlength="13"    value="{{ $row->SQAUOMQTY }}"  autocomplete="off"  readonly/></td>
                                                        <td><input type="text" name={{"popupMUOM_".$key}} id={{"popupMUOM_".$key}} class="form-control" autocomplete="off"value="{{ $row->popupMUOM }}"  readonly/></td>
                                                        <td hidden><input type="hidden" name={{"MAIN_UOMID_REF_".$key}} id={{"MAIN_UOMID_REF_".$key}} class="form-control" value="{{ $row->MAIN_UOMID_REF }}" autocomplete="off" /></td>
                                                        <td hidden><input type="text" name={{"TOTAL_CONSUMED_QTY_".$key}} id={{"TOTAL_CONSUMED_QTY_".$key}} class="form-control three-digits" maxlength="13" value="{{ $row->TOTAL_CONSUMED }}"  autocomplete="off" readonly /></td>
                                                        <td><input type="text" name={{"SO_QTY_".$key}} id={{"SO_QTY_".$key}} class="form-control three-digits" maxlength="13" value="{{ $row->SO_QTY }}"  autocomplete="off"  /></td>
                                                        <td hidden><input type="hidden" name={{"SO_FQTY_".$key}} id={{"SO_FQTY_".$key}} class="form-control three-digits" maxlength="13"  autocomplete="off"   readonly/></td>
                                                        <td><input type="text" name={{"popupAUOM_".$key}} id={{"popupAUOM_".$key}} class="form-control"  autocomplete="off" value="{{ $row->popupAUOM }}"  readonly/></td>
                                                        <td hidden><input type="hidden" name={{"ALT_UOMID_REF_".$key}} id={{"ALT_UOMID_REF_".$key}} class="form-control"  autocomplete="off" value="{{ $row->ALT_UOMID_REF }}"  readonly/></td>
                                                        <td><input type="text" name={{"ALT_UOMID_QTY_".$key}} id={{"ALT_UOMID_QTY_".$key}} class="form-control three-digits"  value="{{ $row->ALT_UOMID_QTY }}" maxlength="13" autocomplete="off"  readonly/></td>
                                                        <td><input type="text" name={{"RATEPUOM_".$key}} id={{"RATEPUOM_".$key}} class="form-control five-digits" maxlength="13" value="{{ $row->RATEPUOM }}"  autocomplete="off" /></td>
                                                        <td><input type="text" name={{"DISCPER_".$key}} id={{"DISCPER_".$key}} class="form-control four-digits" maxlength="8" value="{{ $row->DISCPER }}"  autocomplete="off" style="width: 50px;" /></td>
                                                        <td><input type="text" name={{"DISCOUNT_AMT_".$key}} id={{"DISCOUNT_AMT_".$key}} class="form-control two-digits" value="{{ $row->DISCOUNT_AMT }}" maxlength="15"  autocomplete="off"  /></td>
                                                        <td><input type="text" name={{"DISAFTT_AMT_".$key}} id={{"DISAFTT_AMT_".$key}} class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                        <td><input type="text" name={{"IGST_".$key}} id={{"IGST_".$key}} class="form-control four-digits" maxlength="8" value="{{ $row->IGST }}" autocomplete="off"  readonly/></td>
                                                        <td><input type="text" name={{"IGSTAMT_".$key}} id={{"IGSTAMT_".$key}} class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                        <td><input type="text" name={{"CGST_".$key}} id={{"CGST_".$key}} class="form-control four-digits" maxlength="8" value="{{ $row->CGST }}" autocomplete="off"  readonly/></td>
                                                        <td><input type="text" name={{"CGSTAMT_".$key}} id={{"CGSTAMT_".$key}} class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                        <td><input type="text" name={{"SGST_".$key}} id={{"SGST_".$key}} class="form-control four-digits" maxlength="8" value="{{ $row->SGST }}" autocomplete="off"  readonly/></td>
                                                        <td><input type="text" name={{"SGSTAMT_".$key}} id={{"SGSTAMT_".$key}} class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                        <td><input type="text" name={{"TGST_AMT_".$key}} id={{"TGST_AMT_".$key}} class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                        <td><input type="text" name={{"TOT_AMT_".$key}} id={{"TOT_AMT_".$key}} class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                        <td align="center"><button class="btn add material" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button> <button class="btn remove dmaterial @if($row->CAN_DELETE==false) notdelete @endif" title="Delete" data-toggle="tooltip" type="button" @if($row->CAN_DELETE==false) disabled   @endif><i class="fa fa-trash" ></i></button></td>
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
                                        <input type="text" name="txtTNCID_popup" id="txtTNCID_popup" class="form-control"  autocomplete="off"  readonly/>
                                        @if(!empty($objSOTNC))
                                         <input type="hidden" name="TNCID_REF" id="TNCID_REF" class="form-control" value="{{$objSOTNC[0]->TNCID_REF}}" autocomplete="off" />
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
                                            @if(!empty($objSOTNC))
                                                @foreach($objSOTNC as $Tkey => $Trow)
                                                    <tr  class="participantRow3">
                                                    <td><input type="text" name={{"popupTNCDID_".$Tkey}} id={{"popupTNCDID_".$Tkey}} class="form-control"  autocomplete="off"  readonly/></td>
                                                    <td hidden><input type="hidden" name={{"TNCDID_REF_".$Tkey}} id={{"TNCDID_REF_".$Tkey}} class="form-control" value="{{$Trow->TNCDID_REF}}" autocomplete="off" /></td>
                                                    <td hidden><input type="hidden" name={{"TNCismandatory_".$Tkey}} id={{"TNCismandatory_".$Tkey}} class="form-control" autocomplete="off" /></td>
                                                    <td id={{"tdinputid_".$Tkey}}>
                                                    {{-- dynamic input --}} 
                                                    </td>
                                                        <td align="center" ><button class="btn add TNC" title="add" data-toggle="tooltip"  type="button" disabled><i class="fa fa-plus"></i></button><button class="btn remove DTNC" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
                                                    </tr>
                                                <tr></tr>
                                                @endforeach 
                                                @else
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
                                            @endif 
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                    

                                <div id="udf" class="tab-pane fade">
                                    <div class="table-responsive table-wrapper-scroll-y " style="margin-top:10px;height:280px;width:50%;">
                                        <table id="example4" class="display nowrap table table-striped table-bordered itemlist" style="height:auto !important;">
                                            <thead id="thead1"  style="position: sticky;top: 0">
                                            <tr >
                                                <th>UDF Fields<input class="form-control" type="hidden" name="Row_Count3" id ="Row_Count3"></th>
                                                <th>Value / Comments</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                        @if(!empty($objSOUDF))
                                            @foreach($objSOUDF as $Ukey => $Urow)
                                                <tr  class="participantRow4">
                                                    <td><input type="text" name={{"popupUDFSOID_".$Ukey}} id={{"popupUDFSOID_".$Ukey}}  class="form-control"  autocomplete="off"  readonly/></td>
                                                    <td hidden><input type="hidden" name={{"UDFSOID_REF_".$Ukey}}  id={{"UDFSOID_REF_".$Ukey}} class="form-control" value="{{$Urow->UDFSOID_REF}}" autocomplete="off" /></td>
                                                    <td hidden><input type="hidden" name={{"UDFismandatory_".$Ukey}} id={{"UDFismandatory_".$Ukey}} class="form-control" autocomplete="off" /></td>
                                                    <td id={{"udfinputid_".$Ukey}}>
                                                    {{-- dynamic input --}} 
                                                    </td>
                                                    <td align="center" ><button class="btn add UDF" title="add" data-toggle="tooltip" type="button" disabled><i class="fa fa-plus"></i></button><button class="btn remove DUDF" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
                                                </tr>
                                                <tr></tr>
                                            @endforeach 
                                            @else
                                            @foreach($objUdfSOData as $uindex=>$uRow)
                                              <tr  class="participantRow4">
                                                  <td><input type="text" name={{"popupUDFSOID_".$uindex}} id={{"popupUDFSOID_".$uindex}} class="form-control" value="{{$uRow->LABEL}}" autocomplete="off"  readonly/></td>
                                                  <td hidden><input type="hidden" name={{"UDFSOID_REF_".$uindex}} id={{"UDFSOID_REF_".$uindex}} class="form-control" value="{{$uRow->UDFID}}" autocomplete="off"   /></td>
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
                                
                                <div id="CT" class="tab-pane fade">
                                    <div class="row" style="margin-top:10px;margin-left:3px;" >	
                                        <div class="col-lg-2 pl"><p>Calculation Template</p></div>
                                        <div class="col-lg-2 pl">
                                        <input type="text" name="txtCTID_popup" id="txtCTID_popup" class="form-control"  autocomplete="off"  readonly/>
                                        @if(!empty($objSOCAL))
                                         <input type="hidden" name="CTID_REF" id="CTID_REF" class="form-control" value="{{$objSOCAL[0]->CTID_REF}}" autocomplete="off" />
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
                                            @if(!empty($objSOCAL))
                                                @foreach($objSOCAL as $Ckey => $Crow)
                                                    <tr  class="participantRow5">
                                                        <td><input type="text" name={{"popupTID_".$Ckey}} id={{"popupTID_".$Ckey}}  class="form-control"  autocomplete="off"  readonly/></td>
                                                        <td hidden><input type="hidden" name={{"TID_REF_".$Ckey}}  id={{"TID_REF_".$Ckey}}  class="form-control" autocomplete="off" value="{{$Crow->TID_REF}}" /></td>
                                                        <td><input type="text" name={{"RATE_".$Ckey}}  id={{"RATE_".$Ckey}} class="form-control four-digits" maxlength="8" autocomplete="off" value="{{$Crow->RATE}}"  readonly/></td>
                                                        <td hidden><input type="hidden" name={{"BASIS_".$Ckey}} id={{"BASIS_".$Ckey}} class="form-control" autocomplete="off"  /></td>
                                                        <td hidden><input type="hidden" name={{"SQNO_".$Ckey}} id={{"SQNO_".$Ckey}} class="form-control" autocomplete="off" /></td>
                                                        <td hidden><input type="hidden" name={{"FORMULA_".$Ckey}} id={{"FORMULA_".$Ckey}} class="form-control" autocomplete="off" /></td>
                                                        <td><input type="text" name={{"VALUE_".$Ckey}} id={{"VALUE_".$Ckey}} class="form-control two-digits" maxlength="15" autocomplete="off" value="{{$Crow->VALUE}}" readonly/></td>
                                                        <td style="text-align:center;" ><input type="checkbox" class="filter-none" name={{"calGST_".$Ckey}} id={{"calGST_".$Ckey}} {{$Crow->GST == 1 ? 'checked' : ''}}   ></td>
                                                        
                                                        <td><input type="text" name={{"calIGST_".$Ckey}} id={{"calIGST_".$Ckey}} class="form-control four-digits" maxlength="8" autocomplete="off" value="{{$Crow->IGST}}" readonly/></td>
                                                        <td><input type="text" name={{"AMTIGST_".$Ckey}} id={{"AMTIGST_".$Ckey}} class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                        <td><input type="text" name={{"calCGST_".$Ckey}} id={{"calCGST_".$Ckey}} class="form-control four-digits" maxlength="8" autocomplete="off" value="{{$Crow->CGST}}" readonly/></td>
                                                        <td><input type="text" name={{"AMTCGST_".$Ckey}} id={{"AMTCGST_".$Ckey}} class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                        <td><input type="text" name={{"calSGST_".$Ckey}} id={{"calSGST_".$Ckey}} class="form-control four-digits" maxlength="8" autocomplete="off" value="{{$Crow->SGST}}" readonly/></td>
                                                        <td><input type="text" name={{"AMTSGST_".$Ckey}} id={{"AMTSGST_".$Ckey}} class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                        <td><input type="text" name={{"TOTGSTAMT_".$Ckey}} id={{"TOTGSTAMT_".$Ckey}} class="form-control two-digits"  maxlength="15" autocomplete="off"  readonly/></td>
                                                        <td style="text-align:center;"><input type="checkbox" class="filter-none" name={{"calACTUAL_".$Ckey}} id={{"calACTUAL_".$Ckey}} value="" {{$Crow->ACTUAL == 1 ? 'checked' : ''}}  ></td>
                                                        <td align="center" ><button class="btn add" title="add" data-toggle="tooltip" type="button" disabled><i class="fa fa-plus"></i></button><button class="btn remove" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
                                                    </tr>
                                                    <tr></tr>
                                                @endforeach 
                                                @else
                                                <tr  class="participantRow5">
                                                    <td><input type="text" name="popupTID_0" id="popupTID_0" class="form-control"  autocomplete="off"  readonly/></td>
                                                    <td hidden><input type="hidden" name="TID_REF_0" id="TID_REF_0" class="form-control" autocomplete="off" /></td>
                                                    <td><input type="text" name="RATE_0" id="RATE_0" class="form-control four-digits" maxlength="8" autocomplete="off"  readonly/></td>
                                                    <td hidden><input type="hidden" name="BASIS_0" id="BASIS_0" class="form-control" autocomplete="off" /></td>
                                                    <td hidden><input type="hidden" name="SQNO_0" id="SQNO_0" class="form-control" autocomplete="off" /></td>
                                                    <td><input type="text" name="VALUE_0" id="VALUE_0" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                    <td style="text-align:center;" ><input type="checkbox" class="filter-none" name="calGST_0" id="calGST_0" value="" ></td>
                                                    <td><input type="text" name="calIGST_0" id="calIGST_0" class="form-control four-digits" maxlength="8" autocomplete="off"  readonly/></td>
                                                    <td><input type="text" name="AMTIGST_0" id="AMTIGST_0" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                    <td><input type="text" name="calCGST_0" id="calCGST_0" class="form-control four-digits" maxlength="8" autocomplete="off"  readonly/></td>
                                                    <td><input type="text" name="AMTCGST_0" id="AMTCGST_0" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                    <td><input type="text" name="calSGST_0" id="calSGST_0" class="form-control four-digits" maxlength="8" autocomplete="off"  readonly/></td>
                                                    <td><input type="text" name="AMTSGST_0" id="AMTSGST_0" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                    <td><input type="text" name="TOTGSTAMT_0" id="TOTGSTAMT_0" class="form-control two-digits"  maxlength="15" autocomplete="off"  readonly/></td>
                                                    <td style="text-align:center;"><input type="checkbox" class="filter-none" name="calACTUAL_0" id="calACTUAL_0" value=""   ></td>
                                                    <td align="center" ><button class="btn add" title="add" data-toggle="tooltip" type="button" disabled><i class="fa fa-plus"></i></button><button class="btn remove" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
                                                </tr>
                                                <tr></tr>
                                            @endif 
                                            </tbody>
                                    </table>
                                    </div>	
                                </div>
                                
                                
                                <div id="PaymentSlabs" class="tab-pane fade">
                                    <div class="table-responsive table-wrapper-scroll-y " style="margin-top:10px;height:280px;width:50%;">
                                        <table id="example6" class="display nowrap table table-striped table-bordered itemlist" style="height:auto !important;">
                                            <thead id="thead1"  style="position: sticky;top: 0">
                                            <tr >
                                                <th>Day(s)<input class="form-control" type="hidden" name="Row_Count5" id ="Row_Count5"></th>
                                                <th>Due %</th>
                                                <th>Remarks</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(!empty($objSOPSLB))
                                                @foreach($objSOPSLB as $Pkey => $Prow)
                                                    <tr  class="participantRow6">
                                                        <td> <input type="text" class="form-control" id={{"PAY_DAYS_".$Pkey}} name={{"PAY_DAYS_".$Pkey}} value="{{$Prow->PAY_DAYS}}" autocomplete="off"  /> </td>
                                                        <td> <input type="text" class="form-control four-digits" id={{"DUE_".$Pkey}} name={{"DUE_".$Pkey}} value="{{$Prow->DUE}}" maxlength="8" autocomplete="off" /> </td>
                                                        <td> <input type="text" class="form-control" id={{"PSREMARKS_".$Pkey}} name={{"PSREMARKS_".$Pkey}} value="{{$Prow->REMARKS}}" autocomplete="off"  /> </td>
                                                        <td align="center" ><button class="btn add" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button><button class="btn remove" title="Delete" data-toggle="tooltip" type="button" ><i class="fa fa-trash" ></i></button></td>
                                                    </tr>
                                                    <tr></tr>
                                                @endforeach 
                                                @else
                                                <tr  class="participantRow6">
                                                        <td> <input type="text" class="form-control" id="PAY_DAYS_0" name="PAY_DAYS_0"  autocomplete="off" /> </td>
                                                        <td> <input type="text" class="form-control four-digits" id="DUE_0" name="DUE_0"  maxlength="8" autocomplete="off" /> </td>
                                                        <td> <input type="text" class="form-control" id="PSREMARKS_0" name="PSREMARKS_0" autocomplete="off"  /> </td>
                                                        <td> <input type="date" class="form-control" id="DUE_DATE_0" name="DUE_DATE_0" autocomplete="off"  readonly /> </td>
                                                        <td align="center" ><button class="btn add" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button><button class="btn remove" title="Delete" data-toggle="tooltip" type="button" ><i class="fa fa-trash" ></i></button></td>
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
        
    </div><!--purchase-order-view-->

<!-- </div> -->
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

<!-- Bill To Dropdown -->
<div id="BillTopopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='BillToclosePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>GL Account</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="BillToTable" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
    <tr>
            <th>Name</th>
            <th>Address</th>
    </tr>
    </thead>
    <tbody>
    <tr>
    <td>
    <input type="text" id="BillTocodesearch" onkeyup="BillToCodeFunction()">
    </td>
    <td>
    <input type="text" id="BillTonamesearch" onkeyup="BillToNameFunction()">
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
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='ShipToclosePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>GL Account</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="ShipToTable" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
    <tr>
            <th>Name</th>
            <th>Address</th>
    </tr>
    </thead>
    <tbody>
    <tr>
    <td>
    <input type="text" id="ShipTocodesearch" onkeyup="ShipToCodeFunction()">
    </td>
    <td>
    <input type="text" id="ShipTonamesearch" onkeyup="ShipToNameFunction()">
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
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='TNCID_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>GL Account</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="TNCIDTable" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
    <tr>
            <th>Code</th>
            <th>Name</th>
    </tr>
    </thead>
    <tbody>
    <tr>
    <td>
    <input type="text" id="TNCcodesearch" onkeyup="TNCCodeFunction()">
    </td>
    <td>
    <input type="text" id="TNCnamesearch" onkeyup="TNCNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="TNCIDTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
         
        </thead>
        <tbody>
        @foreach ($objTNCHeader as $tncindex=>$tncRow)
        <tr id="tncidcode_{{ $tncindex }}" class="clstncid">
          <td width="50%">{{ $tncRow-> TNC_CODE }}
          <input type="hidden" id="txttncidcode_{{ $tncindex }}" data-desc="{{ $tncRow-> TNC_CODE }} - {{ $tncRow-> TNC_DESC }}"  
          value="{{ $tncRow-> TNCID }}"/></td><td>{{ $tncRow-> TNC_DESC }}</td>
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
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='CTID_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Calculation Template</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="CTIDTable" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
    <tr>
            <th>Code</th>
            <th>Name</th>
    </tr>
    </thead>
    <tbody>
    <tr>
    <td>
    <input type="text" id="CTIDcodesearch" onkeyup="CTIDCodeFunction()">
    </td>
    <td>
    <input type="text" id="CTIDnamesearch" onkeyup="CTIDNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="CTIDTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
         
        </thead>
        <tbody>
        @foreach ($objCalculationHeader as $calindex=>$calRow)
        <tr id="CTIDcode_{{ $calindex }}" class="clsctid">
          <td width="50%">{{ $calRow-> CTCODE }}
          <input type="hidden" id="txtCTIDcode_{{ $calindex }}" data-desc="{{ $calRow-> CTCODE }} - {{ $calRow-> CTDESCRIPTION }}"  
          value="{{ $calRow-> CTID }}"/></td><td>{{ $calRow-> CTDESCRIPTION }}</td>
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

<!-- GLID Dropdown -->
<div id="glidpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='gl_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>GL Account</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="GlCodeTable" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
    <tr>
            <th>GLCode</th>
            <th>GLName</th>
    </tr>
    </thead>
    <tbody>
    <tr>
    <td>
    <input type="text" id="glcodesearch" onkeyup="GLCodeFunction()">
    </td>
    <td>
    <input type="text" id="glnamesearch" onkeyup="GLNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="GlCodeTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          <!-- <tr>
            <th>GLCode</th>
            <th>GLName</th>
          </tr> -->
          
        </thead>
        <tbody>
        @foreach ($objglcode as $index=>$glRow)
        <tr id="glidcode_{{ $index }}" class="clsglid">
          <td width="50%">{{ $glRow-> GLCODE }}
          <input type="hidden" id="txtglidcode_{{ $index }}" data-desc="{{ $glRow-> GLCODE }}"  value="{{ $glRow-> GLID }}"/>
          </td>
          <td>{{ $glRow-> GLNAME }}</td>
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
<!-- GLID Dropdown-->

<!-- Currency Dropdown -->
<div id="cridpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='crid_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Currency</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="CurrencyTable" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
    <tr>
            <th>Code</th>
            <th>Description</th>
    </tr>
    </thead>
    <tbody>
    <tr>
    <td>
    <input type="text" id="currencycodesearch" onkeyup="CurrencyCodeFunction()">
    </td>
    <td>
    <input type="text" id="currencynamesearch" onkeyup="CurrencyNameFunction()">
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
        <tr id="cridcode_{{ $crindex }}" class="clscrid">
          <td width="50%">{{ $crRow-> CRCODE }}
          <input type="hidden" id="txtcridcode_{{ $crindex }}" data-desc="{{ $crRow-> CRCODE }}"  value="{{ $crRow-> CRID }}"/>
          </td>
          <td>{{ $crRow-> CRDESCRIPTION }}</td>
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

<!-- Sub GL Dropdown -->
<div id="subglpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='subgl_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Sub GL Account</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="SubGLTable" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
    <tr>
            <th>Sub GLCode</th>
            <th>Sub GLName</th>
    </tr>
    </thead>
    <tbody>
    <tr>
    <td>
    <input type="text" id="subglcodesearch" onkeyup="SubGLCodeFunction()">
    </td>
    <td>
    <input type="text" id="subglnamesearch" onkeyup="SubGLNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="SubGLTable2" class="display nowrap table  table-striped table-bordered" width="100%">
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
<!-- Sub GL Dropdown-->

<!-- Sales Person Dropdown -->
<div id="SPIDpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='SPID_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Sales Person</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="SalesPersonTable" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
    <tr>
            <th>Emp Code</th>
            <th>Emp Name</th>
    </tr>
    </thead>
    <tbody>
    <tr>
    <td>
    <input type="text" id="SalesPersoncodesearch" onkeyup="SalesPersonCodeFunction()">
    </td>
    <td>
    <input type="text" id="SalesPersonnamesearch" onkeyup="SalesPersonNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="SalesPersonTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          
        </thead>
        <tbody >     
        @foreach ($objSalesPerson as $spindex=>$spRow)
        <tr id="spidcode_{{ $spindex }}" class="clsspid">
          <td width="50%">{{ $spRow-> EMPCODE }}
          <input type="hidden" id="txtspidcode_{{ $spindex }}" data-desc="{{ $spRow-> EMPCODE }} - {{ $spRow-> FNAME }} - {{ $spRow-> LNAME }}"  value="{{ $spRow-> EMPID }}"/>
          </td>
          <td>{{ $spRow-> FNAME }} {{ $spRow-> MNAME }} {{ $spRow-> LNAME }}</td>
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

<!-- Sales Quotation Dropdown -->
<div id="SQApopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='SQA_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Sales Quotation</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="SalesQuotationTable" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
          <tr id="none-select" class="searchalldata" hidden>
            
            <td> <input type="hidden" name="fieldid" id="hdn_sqid"/>
            <input type="hidden" name="fieldid2" id="hdn_sqid2"/></td>
          </tr>
    <tr>
            <th>Quotation No</th>
            <th>Quotation Date</th>
    </tr>
    </thead>
    <tbody>
    <tr>
    <td>
    <input type="text" id="SalesQuotationcodesearch" onkeyup="SalesQuotationCodeFunction()">
    </td>
    <td>
    <input type="text" id="SalesQuotationnamesearch" onkeyup="SalesQuotationNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="SalesQuotationTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">

        </thead>
        <tbody  id="tbody_SQ">     
        
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
            
            <td> <input type="text" name="fieldid" id="hdn_ItemID"/>
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
            <input type="text" name="fieldid14" id="hdn_ItemID14"/>
            <input type="text" name="fieldid15" id="hdn_ItemID15"/>
            <input type="text" name="fieldid16" id="hdn_ItemID16"/>
            <input type="text" name="fieldid17" id="hdn_ItemID17"/>
            <input type="text" name="fieldid18" id="hdn_ItemID18"/>
            <input type="text" name="fieldid19" id="hdn_ItemID19"/>
            <input type="text" name="fieldid20" id="hdn_ItemID20"/>
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
            <th style="width:8%;">ALPS Part No.</th>
            <th style="width:8%;">Customer Part No.</th>
            <th style="width:8%;">OEM Part No.</th>
            <th style="width:8%;">Status</th>
      </tr>
    </thead>
    <tbody>
    <tr>
    <td style="width:8%;text-align:center;"><input type="checkbox" class="js-selectall" data-target=".js-selectall1" /></td>
    <td style="width:10%;"><input type="text" id="Itemcodesearch" class="form-control" onkeyup="ItemCodeFunction()"></td>
    <td style="width:10%;"><input type="text" id="Itemnamesearch" class="form-control" onkeyup="ItemNameFunction()"></td>
    <td style="width:8%;"><input type="text" id="ItemUOMsearch" class="form-control" onkeyup="ItemUOMFunction()"></td>
    <td style="width:8%;"><input type="text" id="ItemQTYsearch" class="form-control" onkeyup="ItemQTYFunction()"></td>
    <td style="width:8%;"><input type="text" id="ItemGroupsearch" class="form-control" onkeyup="ItemGroupFunction()"></td>
    <td style="width:8%;"><input type="text" id="ItemCategorysearch" class="form-control" onkeyup="ItemCategoryFunction()"></td>
    <td style="width:8%;"><input type="text" id="ItemBUsearch" class="form-control" onkeyup="ItemBUFunction()"></td>
    <td style="width:8%;"><input type="text" id="ItemAPNsearch" class="form-control" onkeyup="ItemAPNFunction()"></td>
    <td style="width:8%;"><input type="text" id="ItemCPNsearch" class="form-control" onkeyup="ItemCPNFunction()"></td>
    <td style="width:8%;"><input type="text" id="ItemOEMPNsearch" class="form-control" onkeyup="ItemOEMPNFunction()"></td>
    <td style="width:8%;"><input type="text" id="ItemStatussearch" class="form-control" onkeyup="ItemStatusFunction()"></td>
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
<!-- Item Code Dropdown-->

<!-- ALT UOM Dropdown -->
<div id="altuompopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='altuom_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Sub GL Account</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="altuomTable" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
    <tr id="none-select" class="searchalldata" hidden>
            
            <td> <input type="hidden" name="fieldid" id="hdn_altuom"/>
            <input type="hidden" name="fieldid2" id="hdn_altuom2"/>
            <input type="hidden" name="fieldid3" id="hdn_altuom3"/>
            <input type="hidden" name="fieldid4" id="hdn_altuom4"/></td>
          </tr>
    <tr>
            <th>UOM Code</th>
            <th>UOM Desc</th>
    </tr>
    </thead>
    <tbody>
    <tr>
    <td>
    <input type="text" id="altuomcodesearch" onkeyup="altuomCodeFunction()">
    </td>
    <td>
    <input type="text" id="altuomnamesearch" onkeyup="altuomNameFunction()">
    </td>
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
        @foreach ($objUdfSOData as $udfindex=>$udfRow)
        <tr id="udfsoid_{{ $udfindex }}" class="clsudfsoid">
          <td width="50%">{{ $udfRow->LABEL }}
          <input type="hidden" id="txtudfsoid_{{ $udfindex }}" data-desc="{{ $udfRow->LABEL }}"  value="{{ $udfRow->UDFID }}"/>
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

@endsection


@push('bottom-css')
<style>
#custom_dropdown, #frm_trn_so_filter {
    display: inline-table;
    margin-left: 15px;
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

  function TNCNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("TNCnamesearch");
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

  $('#txtTNCID_popup').click(function(event){
         $("#TNCIDpopup").show();
         event.preventDefault();
      });

      $("#TNCID_closePopup").click(function(event){
        $("#TNCIDpopup").hide();
      });

      $(".clstncid").dblclick(function(){
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
        TNCCodeFunction();
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
                url:'{{route("transaction",[38,"gettncdetails2"])}}',
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
                url:'{{route("transaction",[38,"gettncdetails3"])}}',
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
                url:'{{route("transaction",[38,"gettncdetails"])}}',
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
            TNCDetCodeFunction();
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

  function CTIDNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("CTIDnamesearch");
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

  $('#txtCTID_popup').click(function(event){
         $("#CTIDpopup").show();
         event.preventDefault();
      });

      $("#CTID_closePopup").click(function(event){
        $("#CTIDpopup").hide();
      });

      $(".clsctid").dblclick(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");
        // var txtid= $('#hdn_fieldid').val();
        // var txt_id2= $('#hdn_fieldid2').val();
        
        $('#txtCTID_popup').val(texdesc);
        $('#CTID_REF').val(txtval);
        $("#CTIDpopup").hide();
        $("#CTIDcodesearch").val(''); 
        $("#CTIDnamesearch").val(''); 
        CTIDCodeFunction();
        //Details
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
                url:'{{route("transaction",[38,"getcalculationdetails2"])}}',
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
                url:'{{route("transaction",[38,"getcalculationdetails3"])}}',
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
                url:'{{route("transaction",[38,"getcalculationdetails"])}}',
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
                        $(this).find('[id*="calIGST_"]').val('0.0000');
                        $(this).find('[id*="AMTIGST_"]').val('0.00');
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
                        $(this).find('[id*="calCGST_"]').val('0.0000');
                        $(this).find('[id*="AMTCGST_"]').val('0.00');
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
                        $(this).find('[id*="calSGST_"]').val('0.0000');
                        $(this).find('[id*="AMTSGST_"]').val('0.00');
                        $(this).find('[id*="calSGST_"]').prop('readonly',true);
                      }
                      $(this).find('[id*="TOTGSTAMT_"]').val(TotGSTamt);
                    }
                    else
                    {
                      $(this).find('[id*="calSGST_"]').val('0.0000');
                      $(this).find('[id*="AMTSGST_"]').val('0.00');
                      $(this).find('[id*="calCGST_"]').val('0.0000');
                      $(this).find('[id*="AMTCGST_"]').val('0.00');
                      $(this).find('[id*="calIGST_"]').val('0.0000');
                      $(this).find('[id*="AMTIGST_"]').val('0.00');
                      $(this).find('[id*="TOTGSTAMT_"]').val('0.00');
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
              $("#"+txtcol).parent().parent().find("[id*='AMTIGST']").removeAttr('readonly');
              }
              else
              {
              $("#"+txtcol).parent().parent().find("[id*='calCGST']").removeAttr('readonly');
              $("#"+txtcol).parent().parent().find("[id*='calSGST']").removeAttr('readonly');
              $("#"+txtcol).parent().parent().find("[id*='AMTCGST']").removeAttr('readonly');
              $("#"+txtcol).parent().parent().find("[id*='AMTSGST']").removeAttr('readonly');
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
              
            if(txtrate > 0.0000)
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
            CTIDDetCodeFunction();
            event.preventDefault();
            
        });
  }
//Calculation Details Ends
//------------------------

//------------------------
  //GL Account
      let tid = "#GlCodeTable2";
      let tid2 = "#GlCodeTable";
      let headers = document.querySelectorAll(tid2 + " th");

      // Sort the table element when clicking on the table headers
      headers.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(tid, ".clsglid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function GLCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("glcodesearch");
        filter = input.value.toUpperCase();
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

  function GLNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("glnamesearch");
        filter = input.value.toUpperCase();
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

  $('#txtgl_popup').click(function(event){
         $("#glidpopup").show();
         event.preventDefault();
      });

      $("#gl_closePopup").click(function(event){
        $("#glidpopup").hide();
        event.preventDefault();
      });

      $(".clsglid").dblclick(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");
        // var txtid= $('#hdn_fieldid').val();
        // var txt_id2= $('#hdn_fieldid2').val();
        
        $('#txtgl_popup').val(texdesc);
        $('#GLID_REF').val(txtval);
        $("#glidpopup").hide();
        $("#glcodesearch").val(''); 
        $("#glnamesearch").val(''); 
        GLCodeFunction();
        //sub GL
        
        ////sub GL end
        event.preventDefault();
      });

      

  //GL Account Ends
//------------------------
//Sub GL Account Starts
//------------------------

      let sgltid = "#SubGLTable2";
      let sgltid2 = "#SubGLTable";
      let sglheaders = document.querySelectorAll(sgltid2 + " th");

      // Sort the table element when clicking on the table headers
      sglheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(sgltid, ".clssubgl", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function SubGLCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("subglcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("SubGLTable2");
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

  function SubGLNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("subglnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("SubGLTable2");
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

$("#txtsubgl_popup").click(function(event){
  var customid = $('#GLID_REF').val();
        if(customid!=''){
         
          $('#tbody_subglacct').html('<tr><td colspan="2">Please wait..</td></tr>');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url:'{{route("transaction",[38,"getsubledger"])}}',
                type:'POST',
                data:{'id':customid},
                success:function(data) {
                    $('#tbody_subglacct').html(data);
                    bindSubLedgerEvents();
                },
                error:function(data){
                  console.log("Error: Something went wrong.");
                  $('#tbody_subglacct').html('');
                },
            });        
        }
     $("#subglpopup").show();
     event.preventDefault();
  });

$("#subgl_closePopup").on("click",function(event){ 
    $("#subglpopup").hide();
    event.preventDefault();
});
function bindSubLedgerEvents(){
        $('.clssubgl').dblclick(function(){
    
            var id = $(this).attr('id');
            var txtval =    $("#txt"+id+"").val();
            var texdesc =   $("#txt"+id+"").data("desc");
            var oldSLID =   $("#SLID_REF").val();
            var MaterialClone =  $('#hdnmaterial').val();
            $("#txtsubgl_popup").val(texdesc);
            $("#txtsubgl_popup").blur();
            $("#SLID_REF").val(txtval);
            if (txtval != oldSLID)
            { 
              $('#Material').html(MaterialClone);
              $('#TotalValue').val('0.00');
              var count11 = <?php echo json_encode($objCount1); ?>;
              $('#Row_Count1').val(count11);
              $('#Material').find('.participantRow').each(function(){
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
            $("#subglpopup").hide();
            $("#subglcodesearch").val(''); 
            $("#subglnamesearch").val(''); 
            SubGLCodeFunction();
            var customid = txtval;
              if(customid!=''){
                $("#CREDITDAYS").val('');
                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  });
                  $.ajax({
                      url:'{{route("transaction",[38,"getcreditdays"])}}',
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
                      url:'{{route("transaction",[38,"getBillTo"])}}',
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
                      url:'{{route("transaction",[38,"getShipTo"])}}',
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
                      url:'{{route("transaction",[38,"getBillAddress"])}}',
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
         $("#BillTopopup").show();
         event.preventDefault();
      });

      $("#BillToclosePopup").click(function(event){
        $("#BillTopopup").hide();
        event.preventDefault();
      });

      function BindBillAddress(){
        $(".clsbillto").dblclick(function(){
          var fieldid = $(this).attr('id');
          var txtval =    $("#txt"+fieldid+"").val();
          var texdesc =   $("#txt"+fieldid+"").data("desc");
          
          $('#txtBILLTO').val(texdesc);
          $('#BILLTO').val(txtval);
          $("#BillTopopup").hide();
          $("#BillTocodesearch").val(''); 
          $("#BillTonamesearch").val(''); 
          BillToCodeFunction();        
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
                      url:'{{route("transaction",[38,"getShipAddress"])}}',
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
        $(".clsshipto").dblclick(function(){
          var fieldid = $(this).attr('id');
          var txtval =    $("#txt"+fieldid+"").val();
          var texdesc =   $(this).children('[id*="txtshipadd"]').text();
          var taxstate =  $("#txt"+fieldid+"").data("desc");
          var oldshipto =   $("#SHIPTO").val();
            var MaterialClone =  $('#hdnmaterial').val();
            if (txtval != oldshipto)
            { 
              $('#Material').html(MaterialClone);
              $('#TotalValue').val('0.00');
              var count11 = <?php echo json_encode($objCount1); ?>;
              $('#Row_Count1').val(count11);
              $('#Material').find('.participantRow').each(function(){
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
          $('#txtSHIPTO').val(texdesc);
          $('#SHIPTO').val(txtval);
          $('#Tax_State').val(taxstate);
          $("#ShipTopopup").hide();
          $("#ShipTocodesearch").val(''); 
          $("#ShipTonamesearch").val(''); 
          ShipToCodeFunction();        
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

  function CurrencyNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("currencynamesearch");
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

  $('#txtCRID_popup').click(function(event){
         $("#cridpopup").show();
      });

      $("#crid_closePopup").click(function(event){
        $("#cridpopup").hide();
      });

      $(".clscrid").dblclick(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");
        
        $('#txtCRID_popup').val(texdesc);
        $('#CRID_REF').val(txtval);
        $("#cridpopup").hide();
        var fcurrency = txtval;
        var dcurrency = <?php echo json_encode($objcurrency); ?>;
        var cconverter = <?php echo json_encode($objCurrencyconverter); ?>;
        $.each( cconverter, function( cckey, ccvalue ) {
          var fromdate = ccvalue.EFFDATE;
          var enddate = ccvalue.ENDDATE;
          var d = new Date(); 
          var today = d.getFullYear() + "-" + (d.getMonth()+1) + "-" + d.getDate();
         
            if (fcurrency == ccvalue.TOCRID_REF && dcurrency == ccvalue.FROMCRID_REF && fromdate <= today && enddate >= today)
            {
              $('#CONVFACT').val(ccvalue.FRAMOUNT);
              $('#CONVFACT').prop('readonly','true');
            }
            else
            {
              $('#CONVFACT').val('');
              $('#CONVFACT').removeAttr('readonly');
            }
          });
        $("#currencycodesearch").val(''); 
        $("#currencynamesearch").val(''); 
        CurrencyCodeFunction();
        event.preventDefault();
      });

      

  //Currency Dropdown Ends
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

  function SalesPersonNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("SalesPersonnamesearch");
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

  $('#txtSPID_popup').click(function(event){
         $("#SPIDpopup").show();
      });

      $("#SPID_closePopup").click(function(event){
        $("#SPIDpopup").hide();
      });

      $(".clsspid").dblclick(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");
        
        $('#txtSPID_popup').val(texdesc);
        $('#SPID_REF').val(txtval);
        $("#SPIDpopup").hide();
        
        $("#SalesPersoncodesearch").val(''); 
        $("#SalesPersonnamesearch").val(''); 
        SalesPersonCodeFunction();
        event.preventDefault();
      });

      

  //Sales Person Dropdown Ends
//------------------------

//------------------------
  //Sales Quotation Dropdown
      let sqtid = "#SalesQuotationTable2";
      let sqtid2 = "#SalesQuotationTable";
      let salesquotationheaders = document.querySelectorAll(sqtid2 + " th");

      // Sort the table element when clicking on the table headers
      salesquotationheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(sqtid, ".clssqid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function SalesQuotationCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("SalesQuotationcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("SalesQuotationTable2");
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

  function SalesQuotationNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("SalesQuotationnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("SalesQuotationTable2");
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

  $('#Material').on('click','[id*="txtSQ_popup"]',function(event){
    
    if($("#HDNDirSO").val()==1){
      $(this).attr('disabled',true);
      return false;
    }
    
    var customid = $('#SLID_REF').val();
    $("#tbody_SQ").html('');
                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  })
                  $.ajax({
                      url:'{{route("transaction",[38,"getsalesquotation"])}}',
                      type:'POST',
                      data:{'id':customid},
                      success:function(data) {
                        $("#tbody_SQ").html(data);
                        BindSalesQuotation();
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                        $("#tbody_SQ").html('');
                      },
                  });
        $("#SQApopup").show();
        var id = $(this).attr('id');
        var id2 = $(this).parent().parent().find('[id*="SQA"]').attr('id');

        $('#hdn_sqid').val(id);
        $('#hdn_sqid2').val(id2);
      });

      $("#SQA_closePopup").click(function(event){
        $("#SQApopup").hide();
      });
      function BindSalesQuotation(){
        $(".clssqid").dblclick(function(){
          var fieldid = $(this).attr('id');
          var txtval =    $("#txt"+fieldid+"").val();
          var texdesc =   $("#txt"+fieldid+"").data("desc");
          var txtid= $('#hdn_sqid').val();
          var txt_id2= $('#hdn_sqid2').val();
          $('#'+txtid).val(texdesc);
          $('#'+txt_id2).val(txtval);
          $("#SQApopup").hide();
          $("#SalesQuotationcodesearch").val(''); 
          $("#SalesQuotationnamesearch").val(''); 
          SalesQuotationCodeFunction();
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

      // Sort the table element when clicking on the table headers
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
        var SalesQuotationID = $(this).parent().parent().find('[id*="txtSQ_popup"]').val();


        if(SalesQuotationID=='' && $("#HDNDirSO").val()==0){
          $("#alert").modal('show');
          $("#AlertMessage").text('Please select SQ');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk1');
          return false;
        }

        var taxstate = $.trim($('#Tax_State').val());
        var SOID_REF = $.trim($('#SOID_REF').val()); 
        if(SalesQuotationID!=''){
                $("#tbody_ItemID").html('');
                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  });
                  $.ajax({
                      url:'{{route("transaction",[38,"getItemDetailsQuotationwiseAmend"])}}',
                      type:'POST',
                      data:{'id':SalesQuotationID, 'taxstate':taxstate,'SOID_REF':SOID_REF},
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
        else
        {
                $("#tbody_ItemID").html('');
                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  });
                  $.ajax({
                      url:'{{route("transaction",[38,"getItemDetailswithoutQuotationAmend"])}}',
                      type:'POST',
                      data:{'taxstate':taxstate,'SOID_REF':SOID_REF},
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

        $("#ITEMIDpopup").show();
        var id = $(this).attr('id');
        var id2 = $(this).parent().parent().find('[id*="ITEMID_REF"]').attr('id');
        var id3 = $(this).parent().parent().find('[id*="ItemName"]').attr('id');
        var id4 = $(this).parent().parent().find('[id*="Itemspec"]').attr('id');
        var id5 = $(this).parent().parent().find('[id*="SQMUOM"]').attr('id');
        var id6 = $(this).parent().parent().find('[id*="SQMUOMQTY"]').attr('id');
        var id7 = $(this).parent().parent().find('[id*="SQAUOM"]').attr('id');
        var id8 = $(this).parent().parent().find('[id*="SQAUOMQTY"]').attr('id');
        var id9 = $(this).parent().parent().find('[id*="popupMUOM"]').attr('id');
        var id10 = $(this).parent().parent().find('[id*="MAIN_UOMID_REF"]').attr('id');
        var id11 = $(this).parent().parent().find('[id*="SO_QTY"]').attr('id');
        var id12 = $(this).parent().parent().find('[id*="popupAUOM"]').attr('id');
        var id13 = $(this).parent().parent().find('[id*="ALT_UOMID_REF"]').attr('id');
        var id14 = $(this).parent().parent().find('[id*="ALT_UOMID_QTY"]').attr('id');
        var id15 = $(this).parent().parent().find('[id*="RATEPUOM"]').attr('id');
        var id16 = $(this).parent().parent().find('[id*="SO_FQTY"]').attr('id');

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
        $('#hdn_ItemID17').val(SalesQuotationID);
        var r_count = 0;
        var SalesEnq = [];
        $('#Material').find('.participantRow').each(function(){
          if($(this).find('[id*="ITEMID_REF"]').val() != '')
          {
            SalesEnq.push($(this).find('[id*="txtSQ_popup"]').val());
            r_count = parseInt(r_count)+1;
            $('#hdn_ItemID21').val(r_count);
          }
        });
        $('#hdn_ItemID18').val(SalesEnq.join(', '));
        var ItemID = [];
        $('#Material').find('.participantRow').each(function(){
          if($(this).find('[id*="ITEMID_REF"]').val() != '')
          {
            ItemID.push($(this).find('[id*="ITEMID_REF"]').val());
          }
        });
        $('#hdn_ItemID19').val(ItemID.join(', '));
        var EnquiryID = [];
        $('#Material').find('.participantRow').each(function(){
          if($(this).find('[id*="SEQID_REF"]').val() != '')
          {
            EnquiryID.push($(this).find('[id*="SEQID_REF"]').val());
          }
        });
        $('#hdn_ItemID20').val(EnquiryID.join(', '));
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
          var txttotalconsumed = $("#txt"+fieldid+"").data("totalconsumed");
          //$clone.find('.notdelete')
          var fieldid2 = $(this).find('[id*="itemname"]').attr('id');
          var txtname =  $("#txt"+fieldid2+"").val();
          var txtspec =  $("#txt"+fieldid2+"").data("desc");
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
          var txtenqid = $("#txt"+fieldid7+"").data("desc");
          var rcount1 = parseInt($(this).closest('table').find('.clsitemid').length);
          var rcount2 = $('#hdn_ItemID21').val();
          var r_count2 = 0;
          if(txtenqno == undefined)
          {
            txtenqno = '';
          }
          if(txtenqid == undefined)
          {
            txtenqid = '';
          }
          var totalvalue = 0.00;
          var txttaxamt1 = 0.00;
          var txttaxamt2 = 0.00;
          var txttottaxamt = 0.00;
          var txttotamtatax =0.00;

          txtruom = parseFloat(txtruom).toFixed(5);
          
          txtauomqty = (parseInt(txtmuomqty)/parseInt(txtmqtyf))*parseInt(txtauomqty);
          
          
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

       
        // var intRegex = /^\d+$/;
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
          if($(this).find('[id*="ITEMID_REF"]').val() != '')
          {
            var seitem = $(this).find('[id*="txtSQ_popup"]').val()+'-'+$(this).find('[id*="SEQID_REF"]').val()+'-'+$(this).find('[id*="ITEMID_REF"]').val();
            SalesEnq2.push(seitem);
            r_count2 = parseInt(r_count2) + 1;
          }
        });
        
        var salesenquiry =  $('#hdn_ItemID18').val();
        var itemids =  $('#hdn_ItemID19').val();
        var enquiryids =  $('#hdn_ItemID20').val();
    
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
                    txtenqid = '';
                    txttotalconsumed='';
                    $('.js-selectall').prop("checked", false);
                    return false;
              }
             
              var txtenqitem = txtenqno+'-'+txtenqid+'-'+txtval;
              if(jQuery.inArray(txtenqitem, SalesEnq2) !== -1)
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
                    txtruom = '';
                    txtamt = '';
                    txttax1 = '';
                    txttax2 = '';
                    txtenqno = '';
                    txtenqid = '';
                    txttotalconsumed='';
                    $('.js-selectall').prop("checked", false);
                    return false;
              }

              if(salesenquiry.indexOf(txtenqno) != -1 && itemids.indexOf(txtval) != -1 && enquiryids.indexOf(txtenqid) != -1)
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
                            txtruom = '';
                            txtamt = '';
                            txttax1 = '';
                            txttax2 = '';
                            txtenqno = '';
                            txtenqid = '';
                            txttotalconsumed='';
                            $('.js-selectall').prop("checked", false);
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


                        //$clone.find('.notdelete');
                       // if($clone.find('.notdelete').length==0){
                          $clone.find('.remove').removeAttr('disabled');  //test12
                          $clone.find('.remove').removeClass('notdelete'); 
                        //}


                        $clone.find('[id*="popupITEMID"]').val(texdesc);
                        $clone.find('[id*="ITEMID_REF"]').val(txtval);
                        $clone.find('[id*="SEQID_REF"]').val(txtenqid);
                        $clone.find('[id*="ItemName"]').val(txtname);
                        $clone.find('[id*="Itemspec"]').val(txtspec);
                        $clone.find('[id*="SQMUOM"]').val(txtmuom);
                        $clone.find('[id*="SQMUOMQTY"]').val(txtmuomqty);
                        $clone.find('[id*="SQAUOM"]').val(txtauom);
                        $clone.find('[id*="SQAUOMQTY"]').val(txtauomqty);
                        $clone.find('[id*="popupMUOM"]').val(txtmuom);
                        $clone.find('[id*="MAIN_UOMID_REF"]').val(txtmuomid);
                        $clone.find('[id*="SO_QTY"]').val(txtmuomqty);
                        $clone.find('[id*="SO_FQTY"]').val(txtmuomqty);
                        $clone.find('[id*="popupAUOM"]').val(txtauom);
                        $clone.find('[id*="ALT_UOMID_REF"]').val(txtauomid);
                        $clone.find('[id*="ALT_UOMID_QTY"]').val(txtauomqty);
                        $clone.find('[id*="RATEPUOM"]').val(txtruom);
                        $clone.find('[id*="DISAFTT_AMT"]').val(txtamt);
                        $clone.find('[id*="TOT_AMT"]').val(txttotamtatax);
                        $clone.find('[id*="TGST_AMT"]').val(txttottaxamt);
                        $clone.find('[id*="TOTAL_CONSUMED_QTY"]').val(txttotalconsumed);
                        if($.trim($('#Tax_State').val()) == 'OutofState')
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
                        
                      if($clone.find('[id*="txtSQ_popup"]').val() == '')
                      {
                        $clone.find('[id*="SQMUOM"]').val('');
                        $clone.find('[id*="SQMUOMQTY"]').val('');
                        $clone.find('[id*="SQAUOM"]').val('');
                        $clone.find('[id*="SQAUOMQTY"]').val('');
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
                      $('#'+txtid).val(texdesc);
                      $('#'+txt_id2).val(txtval);
                      $('#'+txt_id3).val(txtname);
                      $('#'+txt_id4).val(txtspec);
                      $('#'+txt_id5).val(txtmuom);
                      $('#'+txt_id6).val(txtmuomqty);
                      $('#'+txt_id7).val(txtauom);
                      $('#'+txt_id8).val(txtauomqty);
                      $('#'+txt_id9).val(txtmuom);
                      $('#'+txt_id10).val(txtmuomid);
                      $('#'+txt_id11).val(txtmuomqty);
                      $('#'+txt_id12).val(txtauom);
                      $('#'+txt_id13).val(txtauomid);
                      $('#'+txt_id14).val(txtauomqty);
                      $('#'+txt_id15).val(txtruom);
                      $('#'+txt_id16).val(txtmuomqty);
                      $('#'+txtid).parent().parent().find('[id*="SEQID_REF"]').val(txtenqid);
                      $('#'+txtid).parent().parent().find('[id*="DISAFTT_AMT"]').val(txtamt);
                      $('#'+txtid).parent().parent().find('[id*="TOT_AMT"]').val(txttotamtatax);
                      $('#'+txtid).parent().parent().find('[id*="TGST_AMT"]').val(txttottaxamt);
                      $('#'+txtid).parent().parent().find('[id*="TOTAL_CONSUMED_QTY"]').val(txttottaxamt);
                      if($.trim($('#Tax_State').val()) == 'OutofState')
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
                        if($('#'+txtid).parent().parent().find('[id*="txtSQ_popup"]').val() == '')
                        {
                          $('#'+txtid).parent().parent().find('[id*="SQMUOM"]').val('');
                          $('#'+txtid).parent().parent().find('[id*="SQMUOMQTY"]').val('');
                          $('#'+txtid).parent().parent().find('[id*="SQAUOM"]').val('');
                          $('#'+txtid).parent().parent().find('[id*="SQAUOMQTY"]').val('');
                        }

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
                      event.preventDefault();
                  }
                  
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
                      if($(this).closest('.participantRow').find('.notdelete').length==1){
                        return false;
                      }
                        var totalvalue = $('#TotalValue').val();
                        totalvalue = parseFloat(totalvalue - $(this).closest('.participantRow').find('[id*="TOT_AMT_"]').val()).toFixed(2);
                        $('#TotalValue').val(totalvalue);
                        $(this).closest('.participantRow').remove();  //test1
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
                      
                }
              });
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
      });

      $('[id*="chkId"]').change(function(){
        var fieldid = $(this).parent().parent().attr('id');
        var txtval =   $("#txt"+fieldid+"").val();
        var texdesc =  $("#txt"+fieldid+"").data("desc");
        var txttotalconsumed = $("#txt"+fieldid+"").data("totalconsumed");
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
        var txtenqid = $("#txt"+fieldid7+"").data("desc");
        if(txtenqno == undefined)
          {
            txtenqno = '';
          }
          if(txtenqid == undefined)
          {
            txtenqid = '';
          }
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
             txttaxamt1 = 0;
          }
          else
          {
             txttaxamt1 = parseFloat((parseFloat(txtamt)*parseFloat(txttax1))/100).toFixed(2);
          }
          if(txttax2 == undefined || txttax2 == '')
          {
            txttax2 = 0.0000;
             txttaxamt2 = 0;
          }
          else
          {
             txttaxamt2 = parseFloat((parseFloat(txtamt)*parseFloat(txttax2))/100).toFixed(2);
          }
        var txttottaxamt = parseFloat((parseFloat(txttaxamt1)+parseFloat(txttaxamt2))).toFixed(2);
        var txttotamtatax = parseFloat((parseFloat(txtamt)+parseFloat(txttottaxamt))).toFixed(2);
        // var intRegex = /^\d+$/;
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
          if($(this).find('[id*="ITEMID_REF"]').val() != '')
          {
            var seitem = $(this).find('[id*="txtSQ_popup"]').val()+'-'+$(this).find('[id*="SEQID_REF"]').val()+'-'+$(this).find('[id*="ITEMID_REF"]').val();
            SalesEnq2.push(seitem);
          }
        });
        
        var salesenquiry =  $('#hdn_ItemID18').val();
        var itemids =  $('#hdn_ItemID19').val();
        var enquiryids =  $('#hdn_ItemID20').val();
    
            if($(this).is(":checked") == true) 
            {
              var txtenqitem = txtenqno+'-'+txtenqid+'-'+txtval;
              if(jQuery.inArray(txtenqitem, SalesEnq2) !== -1)
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
                    txtruom = '';
                    txtamt = '';
                    txttax1 = '';
                    txttax2 = '';
                    txtenqno = '';
                    txtenqid = '';
                    txttotalconsumed = '';
                    return false;
              }

              if(salesenquiry.indexOf(txtenqno) != -1 && itemids.indexOf(txtval) != -1 && enquiryids.indexOf(txtenqid) != -1)
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
                            txtruom = '';
                            txtamt = '';
                            txttax1 = '';
                            txttax2 = '';
                            txtenqno = '';
                            txtenqid = '';
                            txttotalconsumed = '';
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

                        //test10
                        if($clone.find('.notdelete').length==0){   
                          $clone.find('.remove').removeAttr('disabled'); 
                          $clone.find('.remove').removeClass('notdelete'); 
                        }
                       // $clone.find('.remove').removeAttr('disabled'); 
                        $clone.find('[id*="popupITEMID"]').val(texdesc);
                        $clone.find('[id*="SEQID_REF"]').val(txtenqid);
                        $clone.find('[id*="ITEMID_REF"]').val(txtval);
                        $clone.find('[id*="ItemName"]').val(txtname);
                        $clone.find('[id*="Itemspec"]').val(txtspec);
                        $clone.find('[id*="SQMUOM"]').val(txtmuom);
                        $clone.find('[id*="SQMUOMQTY"]').val(txtmuomqty);
                        $clone.find('[id*="SQAUOM"]').val(txtauom);
                        $clone.find('[id*="SQAUOMQTY"]').val(txtauomqty);
                        $clone.find('[id*="popupMUOM"]').val(txtmuom);
                        $clone.find('[id*="MAIN_UOMID_REF"]').val(txtmuomid);
                        $clone.find('[id*="SO_QTY"]').val(txtmuomqty);
                        $clone.find('[id*="SO_FQTY"]').val(txtmuomqty);
                        $clone.find('[id*="popupAUOM"]').val(txtauom);
                        $clone.find('[id*="ALT_UOMID_REF"]').val(txtauomid);
                        $clone.find('[id*="ALT_UOMID_QTY"]').val(txtauomqty);
                        $clone.find('[id*="RATEPUOM"]').val(txtruom);
                        $clone.find('[id*="DISAFTT_AMT"]').val(txtamt);
                        $clone.find('[id*="TOT_AMT"]').val(txttotamtatax);
                        $clone.find('[id*="TGST_AMT"]').val(txttottaxamt);
                        $clone.find('[id*="TOTAL_CONSUMED_QTY"]').val(txttotalconsumed);
                        if($.trim($('#Tax_State').val()) == 'OutofState')
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
                          $clone.find('[id*="CGST"]').val(txttax1);
                          $clone.find('[id*="IGST"]').prop('disabled',true); 
                          $clone.find('[id*="SGST"]').val(txttax2);
                          $clone.find('[id*="SGSTAMT"]').val(txttaxamt2);; 
                          $clone.find('[id*="CGSTAMT"]').val(txttaxamt1);;
                          $clone.find('[id*="IGSTAMT"]').prop('disabled',true);
                        }
                        $tr.closest('table').append($clone);   //test13


                        var rowCount = $('#Row_Count1').val();
                          rowCount = parseInt(rowCount)+1;
                          $('#Row_Count1').val(rowCount);
                          var tvalue = parseFloat(txttotamtatax).toFixed(2);
                        totalvalue = $('#TotalValue').val();
                        totalvalue =  parseFloat(totalvalue) + parseFloat(tvalue);
                        totalvalue = parseFloat(totalvalue).toFixed(2);
                        $('#TotalValue').val(totalvalue);

                        if($clone.find('[id*="txtSQ_popup"]').val() == '')
                        {
                          $clone.find('[id*="SQMUOM"]').val('');
                          $clone.find('[id*="SQMUOMQTY"]').val('');
                          $clone.find('[id*="SQAUOM"]').val('');
                          $clone.find('[id*="SQAUOMQTY"]').val('');
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
                      $('#'+txtid).val(texdesc);
                      $('#'+txt_id2).val(txtval);
                      $('#'+txt_id3).val(txtname);
                      $('#'+txt_id4).val(txtspec);
                      $('#'+txt_id5).val(txtmuom);
                      $('#'+txt_id6).val(txtmuomqty);
                      $('#'+txt_id7).val(txtauom);
                      $('#'+txt_id8).val(txtauomqty);
                      $('#'+txt_id9).val(txtmuom);
                      $('#'+txt_id10).val(txtmuomid);
                      $('#'+txt_id11).val(txtmuomqty);
                      $('#'+txt_id12).val(txtauom);
                      $('#'+txt_id13).val(txtauomid);
                      $('#'+txt_id14).val(txtauomqty);
                      $('#'+txt_id15).val(txtruom);
                      $('#'+txt_id16).val(txtmuomqty);
                      $('#'+txtid).parent().parent().find('[id*="SEQID_REF"]').val(txtenqid);
                      $('#'+txtid).parent().parent().find('[id*="DISAFTT_AMT"]').val(txtamt);
                      $('#'+txtid).parent().parent().find('[id*="TOT_AMT"]').val(txttotamtatax);
                      $('#'+txtid).parent().parent().find('[id*="TGST_AMT"]').val(txttottaxamt);

                      
                      $('#'+txtid).parent().parent().find('[id*="TOTAL_CONSUMED_QTY"]').val(txttotalconsumed); //set total consumed qty


                      if($.trim($('#Tax_State').val()) == 'OutofState')
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
                      if($('#'+txtid).parent().parent().find('[id*="txtSQ_popup"]').val() == '')
                        {
                          $('#'+txtid).parent().parent().find('[id*="SQMUOM"]').val('');
                          $('#'+txtid).parent().parent().find('[id*="SQMUOMQTY"]').val('');
                          $('#'+txtid).parent().parent().find('[id*="SQAUOM"]').val('');
                          $('#'+txtid).parent().parent().find('[id*="SQAUOMQTY"]').val('');
                        }
                      }
                      event.preventDefault();
            }
            // else if($(this).is(":checked") == false) 
            // {
            //   var id = txtval;
            //   var r_count = $('#Row_Count1').val();
            //   $('#Material').find('.participantRow').each(function()
            //   {
            //     var itemid = $(this).find('[id*="ITEMID_REF"]').val();
            //     if(id == itemid)
            //     {
            //         var rowCount = $('#Row_Count1').val();
            //         if (rowCount > 1) {
            //           //if($(this).find('.notdelete').length==1){
            //           //  return false;
            //           //} 
            //           var totalvalue = $('#TotalValue').val();
            //           totalvalue = parseFloat(totalvalue - $(this).closest('.participantRow').find('[id*="TOT_AMT_"]').val()).toFixed(2);
            //           $('#TotalValue').val(totalvalue);
            //             $(this).closest('.participantRow').remove(); //test2
            //           rowCount = parseInt(rowCount)-1;
            //          $('#Row_Count1').val(rowCount);
            //         }
            //         else 
            //         {
            //           $(document).find('.dmaterial').prop('disabled', true);  
            //           $("#ITEMIDpopup").hide();
            //           $("#YesBtn").hide();
            //           $("#NoBtn").hide();
            //           $("#OkBtn").hide();
            //           $("#OkBtn1").show();
            //           $("#AlertMessage").text('There is only 1 row. So cannot be remove.');
            //           $("#alert").modal('show');
            //           $("#OkBtn1").focus();
            //           highlighFocusBtn('activeOk1');
            //           return false;

            //         }
            //           event.preventDefault(); 
            //     }
            //   });
            // }
            else if($(this).is(":checked") == false) 
            {
              var id = txtval;
              var enqid = txtenqid;
              var sqno = txtenqno;
              var r_count = $('#Row_Count1').val();
              $('#Material').find('.participantRow').each(function()
              {
                var itemid = $(this).find('[id*="ITEMID_REF"]').val();
                var enquiryid = $(this).find('[id*="SEQID_REF"]').val();
                var quotationno = $(this).find('[id*="txtSQ_popup"]').val();
                if(id == itemid && enqid == enquiryid && sqno == quotationno )
                {
                    var rowCount = $('#Row_Count1').val();
                    if (rowCount > 1) {
                       if($(this).find('.notdelete').length==1){
                         return false;
                       } 
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
        ItemCodeFunction();
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

      

  $('#Material').on('keydown','[id*="popupAUOM"]',function(event){
        var ItemID = $(this).parent().parent().find('[id*="ITEMID_REF"]').val();
        
        if(ItemID !=''){
                $("#tbody_altuom").html('');
                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  });
                  $.ajax({
                      url:'{{route("transaction",[38,"getAltUOM"])}}',
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
        var id2 = $(this).parent().parent().find('[id*="ALT_UOMID_REF"]').attr('id');
        var id3 = $(this).parent().parent().find('[id*="SO_QTY"]').attr('id');
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

      $(".clsaltuom").dblclick(function(){
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
        var mqty = $('#'+txtid).parent().parent().find('[id*="SO_QTY"]').val();

        if(altuomid!=''){
              $('#'+txt_id4).val('');
                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  });
                  $.ajax({
                      url:'{{route("transaction",[38,"getaltuomqty"])}}',
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
        
        altuomCodeFunction();
        event.preventDefault();
      });
    }

      

  //Alt UOM Dropdown Ends
//------------------------

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
        $clone.find('[id*="SQA"]').val('');
        $clone.find('[id*="SEQID_REF"]').val('');
        $clone.find('[id*="ITEMID_REF"]').val('');
        $tr.closest('table').append($clone);         
        var rowCount1 = $('#Row_Count1').val();
		    rowCount1 = parseInt(rowCount1)+1;
        $('#Row_Count1').val(rowCount1);
        $clone.find('.remove').removeAttr('disabled'); 
        $clone.find('.remove').removeClass('notdelete'); 
        
        $clone.find('.sqpopup').removeAttr('disabled'); 
        $clone.find('.itempopup').removeAttr('disabled'); 
        event.preventDefault();
    });

    $("#Material").on('click', '.remove', function() {
        
        if($(this).closest('.participantRow').find('.notdelete').length>0){
          $(this).closest('.participantRow').find('.notdelete').attr('disabled',true);
          console.log("not deleted");
           return false;  //not delete
        }
        var rowCount = $(this).closest('table').find('.participantRow').length;
        if (rowCount > 1) {
        var totalvalue = $('#TotalValue').val();
        totalvalue = parseFloat(totalvalue - $(this).closest('.participantRow').find('[id*="TOT_AMT_"]').val()).toFixed(2);
        $('#TotalValue').val(totalvalue);
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
        $clone.find("[id*='tdinputid']").html('');
        $clone.find('[id*="TNCDID_REF"]').val('');
        $clone.find('[id*="TNCismandatory"]').val('');
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
        var allTrs = $tr.find('tbody').last();
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
        $clone.find("[id*='udfinputid']").html('');
        $clone.find('[id*="UDFSQID_REF"]').val('');
        $clone.find('[id*="UDFismandatory"]').val('');
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
        $clone.find('[id*="TID_REF"]').val('');
        $clone.find('[id*="BASIS"]').val('');
        $clone.find('[id*="SQNO"]').val('');
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

    $("#PaymentSlabs").on('click', '.add', function() {
        var $tr = $(this).closest('table');
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

        $clone.find('input:text').val('');
        $tr.closest('table').append($clone);         
        var rowCount5 = $('#Row_Count5').val();
		    rowCount5 = parseInt(rowCount5)+1;
        $('#Row_Count5').val(rowCount5);
        $clone.find('.remove').removeAttr('disabled'); 
        
        event.preventDefault();
    });
    $("#PaymentSlabs").on('click', '.remove', function() {
        var rowCount5 = $(this).closest('table').find('.participantRow6').length;
        if (rowCount5 > 1) {
        $(this).closest('.participantRow6').remove();     
        } 
        if (rowCount5 <= 1) {          
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


$(document).ready(function(e) {
  var Material = $("#Material").html(); 
    $('#hdnmaterial').val(Material);
    var count1 = <?php echo json_encode($objCount1); ?>;
    var count2 = <?php echo json_encode($objCount2); ?>;
    var count3 = <?php echo json_encode($objCount3); ?>;
    var count4 = <?php echo json_encode($objCount4); ?>;
    var count5 = <?php echo json_encode($objCount5); ?>;
    $('#Row_Count1').val(count1);
    $('#Row_Count2').val(count2);
    $('#Row_Count3').val(count3);
    $('#Row_Count4').val(count4);
    $('#Row_Count5').val(count5);
    var obj = <?php echo json_encode($objSOMAT); ?>;
    var objtnc = <?php echo json_encode($objSOTNC); ?>;
    var sqcode = <?php echo json_encode($ObjSalesQuotationData); ?>;
    var item = <?php echo json_encode($objItems); ?>;
    var sqitem = <?php echo json_encode($objSQMAT); ?>;
    var uom = <?php echo json_encode($objUOM); ?>;
    var uom2 = <?php echo json_encode($objUOM); ?>;
    var uomconv = <?php echo json_encode($objItemUOMConv); ?>;
    var uomconv2 = <?php echo json_encode($objItemUOMConv); ?>;
    var tncheader = <?php echo json_encode($objTNCHeader); ?>;
    var tncdetails = <?php echo json_encode($objTNCDetails); ?>;
    var soudf = <?php echo json_encode($objSOUDF); ?>;
    var udfforso = <?php echo json_encode($objUdfSOData2); ?>;
    var calheader = <?php echo json_encode($objCalHeader); ?>;
    var caldetails = <?php echo json_encode($objCalDetails); ?>;
    var SOCal = <?php echo json_encode($objSOCAL); ?>;
    var taxstate = <?php echo json_encode($TAXSTATE); ?>;

    var totalvalue = 0.00;
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
              tvalue = parseFloat(tvalue).toFixed(2);
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
              tvalue = parseFloat(tvalue) + parseFloat(totgst2);
              tvalue = parseFloat(tvalue).toFixed(2);
            }
            totalvalue += + tvalue;
    });
    // totalvalue = parseFloat(totalvalue).toFixed(2);
    $('#TotalValue').val(totalvalue);

    $.each( soudf, function( soukey, souvalue ) {
        $.each( udfforso, function( usokey, usovalue ) { 
            if(souvalue.UDFSOID_REF == usovalue.UDFID)
            {
                $('#popupUDFSOID_'+soukey).val(usovalue.LABEL);
            }
        
            if(souvalue.UDFSOID_REF == usovalue.UDFID)
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
                    $('#'+dynamicid2).val(souvalue.SOUVALUE);
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
    
    $.each( obj, function( key, value ) {
        var sqid = value.SQA;
        var itemid = value.ITEMID_REF;
        var enqid = value.SEQID_REF;
        var amtbeforedis = parseFloat(value.RATEPUOM*value.SO_QTY).toFixed(2);
        var dipercent = value.DISCPER;
        var amtafterdis = parseFloat(amtbeforedis - (amtbeforedis*dipercent)/100).toFixed(2);
        if(intRegex.test(amtafterdis)){
            amtafterdis = amtafterdis +'.00';
        }
        
        $('#DISAFTT_AMT_'+key).val(amtafterdis);
        var igstpercent = value.IGST;
        var igstamount  = parseFloat(((amtafterdis*igstpercent)/100)).toFixed(2);
        var cgstpercent = value.CGST;
        var cgstamount  = parseFloat((amtafterdis*cgstpercent)/100).toFixed(2);
        var sgstpercent = value.SGST;
        var sgstamount  = parseFloat((amtafterdis*sgstpercent)/100).toFixed(2);
        var totgsamt = parseFloat(parseFloat(igstamount) + parseFloat(cgstamount) + parseFloat(sgstamount)).toFixed(2);
        var amtaftergst = parseFloat(parseFloat(amtafterdis) + parseFloat(totgsamt)).toFixed(2);
        amtaftergst = parseFloat(amtaftergst).toFixed(2);

        if(intRegex.test(totgsamt)){
            totgsamt = totgsamt +'.00';
        }
        if(intRegex.test(amtaftergst)){
            amtaftergst = amtaftergst +'.00';
        }
        totalvalue += + amtaftergst;
        
        $('#TOT_AMT_'+key).val(amtaftergst);
        $('#TGST_AMT_'+key).val(totgsamt);
        $('#IGSTAMT_'+key).val(igstamount);
        $('#CGSTAMT_'+key).val(cgstamount);
        $('#SGSTAMT_'+key).val(sgstamount);
        if($('#DISCPER_'+key).val() > '.0000')
        {
          $('#DISCOUNT_AMT_'+key).prop('disabled',true);
        }
        else
        {
          $('#DISCOUNT_AMT_'+key).removeAttr('disabled');
        }
        if($('#DISCOUNT_AMT_'+key).val() > '.0000')
        {
          $('#DISCPER_'+key).prop('disabled',true);
        }
        else
        {
          $('#DISCPER_'+key).removeAttr('disabled');
        }
        $.each( uom2, function( um2key, um2value ) {
                if(value.MAIN_UOMID_REF == um2value.UOMID)
                {
                    $('#popupMUOM_'+key).val(um2value.UOMCODE+'-'+um2value.DESCRIPTIONS);
                }
                if(value.ALT_UOMID_REF == um2value.UOMID)
                {
                    $('#popupAUOM_'+key).val(um2value.UOMCODE+'-'+um2value.DESCRIPTIONS);
                }
        });
        $.each( sqcode, function( sqkey, sqvalue ) {
        if (sqid ==sqvalue.SQID)
        {
            $('#txtSQ_popup_'+key).val(sqvalue.SQNO);
        }
        });

        /*
        $.each( item, function( ikey, ivalue ) {
        if(itemid == ivalue.ITEMID)
        {
            $('#popupITEMID_'+key).val(ivalue.ICODE);
            $('#ItemName_'+key).val(ivalue.NAME);
        }
        });
        */

        $.each( sqitem, function( sqmkey, sqmvalue ) {
        if(itemid == sqmvalue.ITEMID_REF && sqid == sqmvalue.SQID_REF && enqid == sqmvalue.SEQID_REF)
        {
            $('#SQMUOMQTY_'+key).val(sqmvalue.SQ_QTY);
            $.each( uom, function( umkey, umvalue ) {
                if(sqmvalue.MAIN_UOMID_REF == umvalue.UOMID)
                {
                    $('#SQMUOM_'+key).val(umvalue.UOMCODE);
                }
                if(sqmvalue.ALT_UOMID_REF == umvalue.UOMID)
                {
                    $('#SQAUOM_'+key).val(umvalue.UOMCODE);
                }
            });
            $.each( uomconv, function( umckey, umcvalue ) {
                if(itemid == umcvalue. ITEMID_REF  && sqmvalue.MAIN_UOMID_REF == umcvalue.FROM_UOMID_REF 
                    && sqmvalue.ALT_UOMID_REF == umcvalue.TO_UOMID_REF)
                {   
                    var altqty = (sqmvalue.SQ_QTY*umcvalue.TO_QTY)/umcvalue.FROM_QTY;
                    if(intRegex.test(altqty)){
                        altqty = altqty +'.000';
                    }
                    $('#SQAUOMQTY_'+key).val(altqty);
                }
               
            });
            

        }
        
        });
        $.each( uomconv2, function( umc2key, umc2value ) {
                if(itemid == umc2value. ITEMID_REF  &&  value.ALT_UOMID_REF == umc2value.TO_UOMID_REF)
                {   
                    var altqty2 = (value.SO_QTY*umc2value.TO_QTY)/umc2value.FROM_QTY;
                    if(intRegex.test(altqty2)){
                        altqty2 = altqty2 +'.000';
                    }
                    $('#ALT_UOMID_QTY_'+key).val(altqty2);
                }
               
        });
    });
    totalvalue = parseFloat(totalvalue).toFixed(2);
    $('#TotalValue').val(totalvalue);

    var d = new Date(); 
    var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;

    $('#SOADT').val(today);
    $('#SOADT').attr('min',today);

    $('#CUSTOMERAREFDT').attr('min',today);


    d.setDate(d.getDate() + 29);
    var todate = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
    $('#SODT').val(today);
    //$('#OVFDT').val(today);
   // $('#OVTDT').val(todate);
   // $('#CUSTOMERDT').val(today);
    $('#CREDITDAYS').ForceNumericOnly();
    $("[id*='SO_QTY']").ForceNumericOnly();
    $("[id*='SO_FQTY']").ForceNumericOnly();
    $("[id*='ALT_UOMID_QTY']").ForceNumericOnly();
    $("[id*='RATEPUOM']").ForceNumericOnly();
    $("[id*='DISCPER']").ForceNumericOnly();
    $("[id*='DISCOUNT_AMT']").ForceNumericOnly();
    $("[id*='DISAFTT_AMT']").ForceNumericOnly();
    $("[id*='IGST']").ForceNumericOnly();
    $("[id*='DUE']").ForceNumericOnly();
    $("[id*='SGST']").ForceNumericOnly();
    $("[id*='CGST']").ForceNumericOnly();
    $("[id*='IGST_AMT']").ForceNumericOnly();
    $("[id*='CGST_AMT']").ForceNumericOnly();
    $("[id*='SGST_AMT']").ForceNumericOnly();
    $("[id*='TGST_AMT']").ForceNumericOnly();
    $("[id*='TOT_AMT']").ForceNumericOnly();
    $("[id*='RATE']").ForceNumericOnly();
    $("[id*='VALUE']").ForceNumericOnly();
    $("[id*='AMTIGST']").ForceNumericOnly();
    $("[id*='AMTCGST']").ForceNumericOnly();
    $("[id*='AMTSGST']").ForceNumericOnly();
    $("[id*='TOTGSTAMT']").ForceNumericOnly();
    $("[id*='calIGST']").ForceNumericOnly();
    $("[id*='calCGST']").ForceNumericOnly();
    $("[id*='calSGST']").ForceNumericOnly();
    
    
    $('#Material').on('keyup',"[id*='ALT_UOMID_QTY']",function()
    {
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.000')
      }
      event.preventDefault();
    });
    function bindTotalValue()
    {
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
    }

    $('#Material').on('focusout',"[id*='SO_QTY']",function()
    {

      if($.trim($(this).val())==""){
          $("#alert").modal('show');
          $("#AlertMessage").text('Please enter Qty.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk1');
          return false;
      }

      var tot_consumed = parseFloat( $(this).parent().parent().find('[id*="TOTAL_CONSUMED_QTY"]').val() ).toFixed(3);
      var enteredqty = parseFloat( $(this).val()).toFixed(3);
     
      if(parseFloat(enteredqty)<parseFloat(tot_consumed)){
          $("#alert").modal('show');
          $("#AlertMessage").text('Qty value must be greater than '+tot_consumed+ ' used qty.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk1');
          return false;
      }

      var totalvalue = 0.00;
        var itemid = $(this).parent().parent().find('[id*="ITEMID_REF"]').val();
        var mqty = $(this).val();
        var altuomid = $(this).parent().parent().find('[id*="ALT_UOMID_REF"]').val();
        var txtid = $(this).parent().parent().find('[id*="ALT_UOMID_QTY"]').attr('id');
        var irate = $(this).parent().parent().find('[id*="RATEPUOM"]').val();
        $(this).parent().parent().find('[id*="IGSTAMT"]').val('0.00');
        $(this).parent().parent().find('[id*="CGSTAMT"]').val('0.00');
        $(this).parent().parent().find('[id*="SGSTAMT"]').val('0.00');
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
                      url:'{{route("transaction",[38,"getaltuomqty"])}}',
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
        var mqty = $(this).parent().parent().find('[id*="SO_QTY"]').val();
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
        var tp1 = $(this).parent().parent().find('[id*="IGST_"]').val();
        var tp2 = $(this).parent().parent().find('[id*="CGST_"]').val();
        var tp3 = $(this).parent().parent().find('[id*="SGST_"]').val();
        $(this).parent().parent().find('[id*="IGSTAMT"]').val('0');
        $(this).parent().parent().find('[id*="CGSTAMT"]').val('0');
        $(this).parent().parent().find('[id*="SGSTAMT"]').val('0');
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
      var mqty = $(this).parent().parent().find('[id*="SO_QTY"]').val();
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
      $(this).parent().parent().find('[id*="DISCOUNT_AMT"]').prop('disabled',true);
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

    $('#Material').on('focusout',"[id*='DISCOUNT_AMT']",function()
    {
      var mqty = $(this).parent().parent().find('[id*="SO_QTY"]').val();
      var irate = $(this).parent().parent().find('[id*="RATEPUOM"]').val();
      var totamt = parseFloat(parseFloat(mqty)*parseFloat(irate)).toFixed(2);
      var dpert = $(this).val();
      var dispercent = $(this).parent().parent().find('[id*="DISCPER"]').val();
      if (dpert != '' && dpert != '.00')
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
        $(this).parent().parent().find('[id*="DISCPER"]').prop('disabled',true);
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

    $("#CUSTOMERAREFNO").on("focusout",function(){
      if($.trim( $(this).val())!="" ){
          $("#CUSTOMERAREFDT").removeAttr("readonly");
          $("#CUSTOMERAREFDT").val('');
        }else{
          $("#CUSTOMERAREFDT").attr("readonly","readonly");
          $("#CUSTOMERAREFDT").val('');
        }
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

    $('#CT').on('focusout',"[id*='calSGST_']",function()
    {
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.0000')
      }
    });

    $('#CT').on('focusout',"[id*='calCGST_']",function()
    {
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.0000')
      }
    });

    $('#CT').on('focusout',"[id*='calIGST_']",function()
    {
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.0000')
      }
    });

    $('#btnAdd').on('click', function() {
        var viewURL = '{{route("transaction",[38,"add"])}}';
                  window.location.href=viewURL;
    });
    $('#btnExit').on('click', function() {
      var viewURL = '{{route('home')}}';
                  window.location.href=viewURL;
    });
     

//SO Date Check
$('#SODT').change(function( event ) {
            var today = new Date();     
            var d = new Date($(this).val()); 
            today.setHours(0, 0, 0, 0) ;
            d.setHours(0, 0, 0, 0) ;
            var sodate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
            if (d < today) {
                $(this).val(sodate);
                $("#alert").modal('show');
                $("#AlertMessage").text('SO Date cannot be less than Current date');
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
//SO Date Check

//SO Validity to Date Check
$('#OVFDT').change(function( event ) {
            var d = document.getElementById('OVFDT').value; 
            var date = new Date(d);
            var newdate = new Date(date);
            newdate.setDate(newdate.getDate() + 29);
            var sodate = newdate.getFullYear() + "-" + ("0" + (newdate.getMonth() + 1)).slice(-2) + "-" + ('0' + newdate.getDate()).slice(-2) ;
            $('#OVTDT').val(sodate);
            
        });

$('#PaymentSlabs').on('change','[id*="PAY_DAYS"]',function( event ) {
    var d = $(this).val(); 
    d = parseInt(d) - 1;
    var sdate =$('#SODT').val();
    var ddate = new Date(sdate);
    var newddate = new Date(ddate);
    newddate.setDate(newddate.getDate() + d);
    var soddate = newddate.getFullYear() + "-" + ("0" + (newddate.getMonth() + 1)).slice(-2) + "-" + ('0' + newddate.getDate()).slice(-2) ;
    $(this).parent().parent().find('[id*="DUE_DATE"]').val(soddate);
    
});
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
      window.location.reload();
   }//fnUndoYes


   window.fnUndoNo = function (){
      $("#SONO").focus();
   }//fnUndoNo


   $("#SOFC").change(function() {
      if ($(this).is(":checked") == true){
          $(this).parent().parent().find('#txtCRID_popup').removeAttr('disabled');
          $(this).parent().parent().find('#txtCRID_popup').prop('readonly','true');
          event.preventDefault();
      }
      else
      {
          $(this).parent().parent().find('#txtCRID_popup').prop('disabled','true');
          $(this).parent().parent().find('#txtCRID_popup').removeAttr('readonly');
          $(this).parent().parent().find('#txtCRID_popup').val('');
          $(this).parent().parent().find('#CRID_REF').val('');
          $(this).parent().parent().find('#CONVFACT').val('');
          event.preventDefault();
      }
  });

  $("#CT").on('change',"[id*='calGST']",function() {
      if ($(this).is(":checked") == true){
          if($.trim($('#Tax_State').val()) == 'OutofState')
          {
            $(this).parent().parent().find('[id*="calIGST"]').removeAttr('disabled');
            $(this).parent().parent().find('[id*="calIGST"]').removeAttr('readonly');
            $(this).parent().parent().find('[id*="calCGST"]').prop('readonly','true');
            $(this).parent().parent().find('[id*="calSGST"]').prop('readonly','true');
            $(this).parent().parent().find('[id*="AMTIGST"]').removeAttr('readonly');
            $(this).parent().parent().find('[id*="AMTIGST"]').removeAttr('readonly');
            $(this).parent().parent().find('[id*="AMTCGST"]').prop('readonly','true');
            $(this).parent().parent().find('[id*="AMTSGST"]').prop('readonly','true');
            $(this).parent().parent().find('[id*="calCGST"]').val('0');
            $(this).parent().parent().find('[id*="calSGST"]').val('0');
            $(this).parent().parent().find('[id*="AMTCGST"]').val('0');
            $(this).parent().parent().find('[id*="AMTSGST"]').val('0');
            $(this).parent().parent().find('[id*="TOTGSTAMT"]').val('0');
            bindTotalValue();
            event.preventDefault();
          }
          else
          {
            $(this).parent().parent().find('[id*="calIGST"]').prop('readonly','true');
            $(this).parent().parent().find('[id*="calCGST"]').removeAttr('readonly');
            $(this).parent().parent().find('[id*="calSGST"]').removeAttr('disabled');
            $(this).parent().parent().find('[id*="calCGST"]').removeAttr('disabled');
            $(this).parent().parent().find('[id*="calSGST"]').removeAttr('readonly');
            $(this).parent().parent().find('[id*="AMTIGST"]').prop('readonly','true');
            $(this).parent().parent().find('[id*="AMTCGST"]').removeAttr('readonly');
            $(this).parent().parent().find('[id*="AMTSGST"]').removeAttr('disabled');
            $(this).parent().parent().find('[id*="AMTCGST"]').removeAttr('disabled');
            $(this).parent().parent().find('[id*="AMTSGST"]').removeAttr('readonly');
            $(this).parent().parent().find('[id*="calIGST"]').val('0');
            $(this).parent().parent().find('[id*="AMTIGST"]').val('0');
            $(this).parent().parent().find('[id*="TOTGSTAMT"]').val('0');
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
      bindTotalValue();
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
      bindTotalValue();
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
      bindTotalValue();
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
</script>

@endpush

@push('bottom-scripts')
<script>

$(document).ready(function() {

    $('#frm_trn_so1').bootstrapValidator({
       
        fields: {
            txtlabel: {
                validators: {
                    notEmpty: {
                        message: 'The SO NO is required'
                    }
                }
            },            
        },
        submitHandler: function(validator, form, submitButton) {
            alert( "Handler for .submit() called." );
             event.preventDefault();
             $("#frm_trn_so").submit();
        }
    });
});
$( "#btnSaveSO" ).click(function() {
  var formSalesOrder = $("#frm_trn_so");
  if(formSalesOrder.valid()){
 
    $("#FocusId").val('');
    var SONO           =   $.trim($("#SONO").val());
    var SODT           =   $.trim($("#SODT").val());
    var GLID_REF       =   $.trim($("#GLID_REF").val());
    var SLID_REF       =   $.trim($("#SLID_REF").val());
    var OVFDT          =   $.trim($("#OVFDT").val());
    var OVTDT          =   $.trim($("#OVTDT").val());
    var CUSTOMERPONO   =   $.trim($("#CUSTOMERPONO").val());
    var CUSTOMERDT     =   $.trim($("#CUSTOMERDT").val());
    var SPID_REF       =   $.trim($("#SPID_REF").val());

    var SOADT           =   $.trim($("#SOADT").val());
    var CUSTOMERAREFNO     =   $.trim($("#CUSTOMERAREFNO").val());
    var CUSTOMERAREFDT     =   $.trim($("#CUSTOMERAREFDT").val());
    var REASONOFSOA     =   $.trim($("#REASONOFSOA").val());

    if(SONO ===""){
        $("#FocusId").val($("#SONO"));
        $("#SONO").val(''); 
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please enter value in SONO.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
    }
    else if(SOADT ===""){
        $("#FocusId").val($("#SOADT"));
        $("#SOADT").val('');  
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please select SOA Date.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
    } 
    else if(CUSTOMERAREFNO !="" &&  CUSTOMERAREFDT=="" ){
        $("#FocusId").val($("#CUSTOMERAREFDT"));
        $("#CUSTOMERAREFDT").val('');  
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please select Customer Amendment Date.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
    } 
    else if(REASONOFSOA ===""){
        $("#FocusId").val($("#REASONOFSOA"));
        $("#REASONOFSOA").val('');  
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please enter value for Reason to amend SO.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
    } 
    else if(SODT ===""){
        $("#FocusId").val($("#SODT"));
        $("#SODT").val('');  
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please select SO Date.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
    } 
    else if(OVFDT ===""){
        $("#FocusId").val($("#OVFDT"));
        $("#OVFDT").val('');  
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please select SO From Date.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
    } 
    else if(OVTDT ===""){
        $("#FocusId").val($("#OVTDT"));
        $("#OVTDT").val('');  
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please select SO To Date.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
    } 
    else if(GLID_REF ===""){
        $("#FocusId").val($("#GLID_REF"));
        $("#GLID_REF").val('');  
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please select GL Account.');
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
        $("#AlertMessage").text('Please select Sub GL Account.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
    }
    else if(SPID_REF ===""){
        $("#FocusId").val($("#SPID_REF"));
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
    else if(CUSTOMERPONO !=="" && CUSTOMERDT ===""){
        $("#FocusId").val($("#CUSTOMERDT"));
        $("#CUSTOMERDT").val(''); 
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please select date for Customer PO.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
    } 
    else{
        event.preventDefault();

        if(new Date(OVFDT)>new Date(OVTDT)){
          $("#FocusId").val($("#OVFDT"));
          $("#OVFDT").val('');  
          $("#ProceedBtn").focus();
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn1").show();
          $("#AlertMessage").text('SO From Date must be less than SO To Date.');
          $("#alert").modal('show');
          $("#OkBtn1").focus();
          return false;
        }


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
        var allblank13 = [];
        var allblank14 = [];

            // $('#udfforsebody').find('.form-control').each(function () {
            $('#Material').find('.participantRow').each(function(){

                if($.trim($(this).find("[id*=ITEMID_REF]").val())!="")
                {
                    allblank.push('true');
                        if($.trim($(this).find("[id*=popupMUOM]").val())!=""){
                            allblank2.push('true');
                              if($.trim($(this).find('[id*="SO_QTY"]').val()) != "")
                              {
                                allblank3.push('true');
                              }
                              else
                              {
                                allblank3.push('false');
                              }  

                              var consqty = parseFloat($.trim($(this).find('[id*="TOTAL_CONSUMED_QTY"]').val()) ).toFixed(3);
                              var mainqty = parseFloat($.trim($(this).find('[id*="SO_QTY"]').val()) ).toFixed(3);

                              if( parseFloat(mainqty) < parseFloat(consqty) ){
                                allblank14.push('true');
                              }else{
                                allblank14.push('false');
                              }


                        }
                        else{
                            allblank2.push('false');
                        } 

                        if($.trim($("#HDNDirSO").val())==0 && $.trim($(this).find('[id*="txtSQ_popup"]').val())=="" ){
                          allblank13.push('true');
                        }else{
                          allblank13.push('false');
                        }
                }
                else
                {
                    allblank.push('false');
                } 

                if($.trim($(this).find("[id*=RATEPUOM]").val())!="")
                {
                  allblank4.push('true');
                }
                else
                {
                  allblank4.push('true');
                }

                if($.trim($('#Tax_State').val())!="WithinState")
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
                                if($.trim($(this).find("[id*=calIGST]").val())!="")
                                {
                                  allblank11.push('true');
                                }
                                else
                                {
                                  allblank11.push('false');
                                }
                              }
                              else
                              {
                                if($.trim($(this).find("[id*=calCGST]").val())!="")
                                {
                                  allblank11.push('true');
                                }
                                else
                                {
                                  allblank11.push('false');
                                }
                                if($.trim($(this).find("[id*=calSGST]").val())!="")
                                {
                                  allblank11.push('true');
                                }
                                else
                                {
                                  allblank11.push('false');
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
            $('#PaymentSlabs').find('.participantRow6').each(function(){
                  if($.trim($(this).find("[id*=PAY_DAYS]").val())!="")
                    {
                      if($.trim($(this).find('[id*="DUE"]').val()) != "")
                      {
                        allblank12.push('true');
                      }
                      else
                      {
                        allblank12.push('false');
                      }       
                    }                
            });

                if(jQuery.inArray("true", allblank13) !== -1){
                    $("#alert").modal('show');
                    $("#AlertMessage").text('Please select SQ in Material Tab.');
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
                $("#AlertMessage").text('Main UOM under Sales Order section is missing in Material Tab.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
                }
                else if(jQuery.inArray("false", allblank3) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Main UOM Quantity under Sales Order section is missing in Material Tab.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
               }
               else if(jQuery.inArray("true", allblank14) !== -1){
                  $("#alert").modal('show');
                  $("#AlertMessage").text('Main Qty value must be greater than consumed Qty in Material Tab.');
                  $("#YesBtn").hide(); 
                  $("#NoBtn").hide();  
                  $("#OkBtn1").show();
                  $("#OkBtn1").focus();
                  highlighFocusBtn('activeOk1');
                  return false;
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
                $("#alert").modal('show');
                $("#AlertMessage").text('Please select Terms & Condition Description in T&C Tab.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
                }
                else if(jQuery.inArray("false", allblank7) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Please enter Value / Comment in T&C Tab.');
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
                else if(jQuery.inArray("false", allblank10) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Please select Calculation Component in Calculation Template Tab.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
                }
                else if(jQuery.inArray("false", allblank11) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Please Enter GST Rate / Value in Calculation Template Tab.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
                }
                else if(jQuery.inArray("false", allblank12) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Please Enter Due % in Payment Slabs Tab.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
                }
                else{

                    $("#alert").modal('show');
                    $("#AlertMessage").text('Do you want to save the record.');
                    $("#YesBtn").data("funcname","fnSaveData");  //set dynamic fucntion name
                    $("#YesBtn").focus();
                    $("#YesBtn").show(); 
                    $("#NoBtn").show();  
                    $("#OkBtn1").hide();
                    $("#OkBtn").hide();
                    highlighFocusBtn('activeYes');

                }
            

        }

    }
});

$( "#btnApprove" ).click(function() {
});

$("#YesBtn").click(function(){

$("#alert").modal('hide');
var customFnName = $("#YesBtn").data("funcname");
    window[customFnName]();

}); //yes button

window.fnSaveData = function (){

//validate and save data
event.preventDefault();

     var trnsoForm = $("#frm_trn_so");
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
    url:'{{ route("transactionmodify",[38,"saveamendment"])}}',
    type:'POST',
    data:formData,
    success:function(data) {
      $(".buttonload").hide(); 
      $("#btnSaveSO").show();   
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
    window.location.href = '{{route("transaction",[38,"index"]) }}';
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


if($.trim( $('#SQA_0').val())=="" || $.trim( $('#SQA_0').val())==0){
  $("#DirectSO").prop('checked',true);
  $("#HDNDirSO").val(1);
}else{
  $("#HDNDirSO").val(0);
}

</script>


@endpush