@extends('layouts.app')
@section('content')
  <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[72,'index'])}}" class="btn singlebt">Item Master</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <button  id="btnSelectedRows" class="btn topnavbt" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
                        <button id="btnSaveItem"   class="btn topnavbt" tabindex="7"><i class="fa fa-save"></i> Save</button>
                        <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt" id="btnUndo"  ><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove" {{($objRights->APPROVAL1||$objRights->APPROVAL2||$objRights->APPROVAL3||$objRights->APPROVAL4||$objRights->APPROVAL5) == 1 ? '' : 'disabled'}}><i class="fa fa-lock"></i> Approved</button>
                        <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                        <a href="{{route('home')}}" class="btn topnavbt"><i class="fa fa-power-off"></i> Exit</a>
                </div><!--col-10-->

            </div><!--row-->
    </div><!--topnav-->	`
   
    <div class="container-fluid filter">
      <form id="frm_mst_item"   > 
        @CSRF  
        {{isset($objItem->ITEMID) ? method_field('PUT') : '' }}
      <div class="inner-form">
              <div class="row">
                <div class="col-lg-2 pl"><p>Item Code</p> </div>
                <div class="col-lg-2 pl">
                  <input type="text" name="ICODE" id="ICODE" class="form-control" value="{{ $objItem->ICODE }}"  readonly >
                  <input type="hidden" name="RECORDID" id="RECORDID" class="form-control" value="{{ $objItem->ITEMID }}"  readonly >
                  <span class="text-danger" id="ERROR_ICODE"></span> 
                </div>
                
                <div class="col-lg-1 pl"><p>Name</p></div>
                <div class="col-lg-3 pl" >
                  <input type="text" name="NAME" id="NAME" class="form-control mandatory" value="{{ $objItem->NAME }}"   maxlength="200"   >
                  <span class="text-danger" id="ERROR_NAME"></span> 
                </div>
                
                <div class="col-lg-1 pl"><p>Part No</p></div>
                <div class="col-lg-1 pl">
                  <input type="text" name="PARTNO" id="PARTNO" class="form-control " value="{{ $objItem->PARTNO }}"  maxlength="20"  autocomplete="off"/>
                </div>
                
                <div class="col-lg-1 pl"><p>Drawing No</p></div>
                <div class="col-lg-1 pl">
                  <input type="text" name="DRAWINGNO" id="DRAWINGNO" class="form-control " value="{{ $objItem->DRAWINGNO }}" maxlength="20" autocomplete="off">
                </div>
              </div>
              
              <div class="row">
                <div class="col-lg-2 pl"><p>Inventory Class</p></div>
                <div class="col-lg-2 pl" >
                  <input type="text" name="invcls_popup" id="invcls_popup" class="form-control mandatory"  readonly />
                  <input type="hidden" name="invcls_id" id="invcls_id" value="{{ $objItem->CLASSID_REF }}" />
                </div>
                
                <div class="col-lg-1 pl"><p>Main UoM</p></div>
                <div class="col-lg-2 pl" >
                  <input type="text" name="maiuomref_popup" id="maiuomref_popup" class="form-control mandatory"  readonly  />
                  <input type="hidden" name="maiuomref_id" id="maiuomref_id" value="{{ $objItem->MAIN_UOMID_REF }}" />
                </div>
                
                <div class="col-lg-1 pl col-md-offset-1"><p>ALT UOM</p></div>
                <div class="col-lg-2 pl" >
                  <input type="text" name="altuomref_popup" id="altuomref_popup" class="form-control mandatory"   readonly />
                  <input type="hidden" name="altuomref_id" id="altuomref_id"  value="{{ $objItem->ALT_UOMID_REF }}" />
                </div>
              </div>

              <div class="row">
                <div class="col-lg-2 pl"><p>Item Type</p></div>
                <div class="col-lg-2 pl" >
                  <select id="ITEM_TYPE" name="ITEM_TYPE" class="form-control mandatory" onChange="getItemType(this.value)"  >
                    <option value="">Select</option>
                    <option {{ isset($objItem->ITEM_TYPE) && $objItem->ITEM_TYPE =='A-Assets'?'selected="selected"':'' }} value="A-Assets">A-Assets</option>
                    <option {{ isset($objItem->ITEM_TYPE) && $objItem->ITEM_TYPE =='I-Inventory'?'selected="selected"':'' }} value="I-Inventory">I-Inventory</option>
                    <option {{ isset($objItem->ITEM_TYPE) && $objItem->ITEM_TYPE =='S-Service'?'selected="selected"':'' }} value="S-Service">S-Service</option>
                    <option {{ isset($objItem->ITEM_TYPE) && $objItem->ITEM_TYPE =='O-Other'?'selected="selected"':'' }} value="O-Other">O-Other</option>
                  </select>
                </div>
                
                <div class="col-lg-1 pl MATERIAL_TYPE_DIV" style="display:none;" ><p>Material Type</p></div>
                <div class="col-lg-2 pl MATERIAL_TYPE_DIV" style="display:none;" >
                  <select id="MATERIAL_TYPE" name="MATERIAL_TYPE" class="form-control mandatory" >
                    <option value="">Select</option>
                    <option {{ isset($objItem->MATERIAL_TYPE) && $objItem->MATERIAL_TYPE =='FG-Finish Good'?'selected="selected"':'' }} value="FG-Finish Good">FG-Finish Good</option>
                    <option {{ isset($objItem->MATERIAL_TYPE) && $objItem->MATERIAL_TYPE =='SFG- Semi Finish Good'?'selected="selected"':'' }} value="SFG- Semi Finish Good">SFG- Semi Finish Good</option>
                    <option {{ isset($objItem->MATERIAL_TYPE) && $objItem->MATERIAL_TYPE =='RM-Raw Material'?'selected="selected"':'' }} value="RM-Raw Material">RM-Raw Material</option>
                    <option {{ isset($objItem->MATERIAL_TYPE) && $objItem->MATERIAL_TYPE =='PM-Packing Material'?'selected="selected"':'' }} value="PM-Packing Material">PM-Packing Material</option>
                    <option {{ isset($objItem->MATERIAL_TYPE) && $objItem->MATERIAL_TYPE =='TG-Trading Good'?'selected="selected"':'' }} value="TG-Trading Good">TG-Trading Good</option>
                    <option {{ isset($objItem->MATERIAL_TYPE) && $objItem->MATERIAL_TYPE =='O-Other'?'selected="selected"':'' }} value="O-Other">O-Other</option>
                  </select>
                </div>

                <div class="col-lg-1 pl GLID_REF_DIV" style="display:none;"><p>GL</p></div>
                <div class="col-lg-2 pl GLID_REF_DIV" style="display:none;">
                <input type="text" name="GLID_REF_POPUP" id="GLID_REF_POPUP" class="form-control mandatory" value="{{ isset($objOldGlList->GLCODE) && $objOldGlList->GLCODE !=''?$objOldGlList->GLCODE.' - '.$objOldGlList->GLNAME:'' }}"  readonly  />
                        <input type="hidden" name="GLID_REF" id="GLID_REF" value="{{ $objItem->GLID_REF }}" />
                        <span class="text-danger" id="ERROR_GLID_REF"></span>
                </div>

                <div class="col-lg-1 pl"><p>Item Description</p></div>
                <div class="col-lg-2 pl">
                  <input type="text" name="ITEM_DESC" id="ITEM_DESC" class="form-control " value="{{ $objItem->ITEM_DESC }}" maxlength="200"  >
                </div>
                
              </div>
              
              <div class="row">
                
                <div class="col-lg-2 pl"><p>Item Category</p></div>
                <div class="col-lg-2 pl" >
                  <input type="text" name="itecat_popup" id="itecat_popup" class="form-control mandatory"   readonly />
                  <input type="hidden" name="itecat_id" id="itecat_id" value="{{ $objItem->ICID_REF }}" />
                </div>
                
                <div class="col-lg-1 pl "><p>Item Group</p></div>
                <div class="col-lg-2 pl" >
                  <input type="text" name="itegrp_popup" id="itegrp_popup" class="form-control mandatory"   readonly />
                  <input type="hidden" name="itegrp_id" id="itegrp_id" value="{{ $objItem->ITEMGID_REF }}" />
                </div>
                
                <div class="col-lg-1 pl"><p>Sub Group</p></div>
                <div class="col-lg-2 pl">
                  <input type="text" name="itesubgrp_popup" id="itesubgrp_popup" value="{{$Objsubgroup->ISGCODE}} - {{$Objsubgroup->DESCRIPTIONS}}" class="form-control mandatory"   readonly />
                  <input type="hidden" name="itesubgrp_id" id="itesubgrp_id" value="{{ $objItem->ISGID_REF }}" />
                </div>
                
                
                
              </div>
              
              <div class="row">
                
                
                <div class="col-lg-1 pl"><p>Default Store</p></div>
                <div class="col-lg-2 pl" >
                  <input type="text" name="defsto_popup" id="defsto_popup" class="form-control mandatory"  readonly />
                  <input type="hidden" name="defsto_id" id="defsto_id" value="{{ $objItem->STID_REF }}" />
                </div>
              
                <div class="col-lg-1 pl"><p>HSN Code</p></div>
                <div class="col-lg-1 pl">
                  <input type="text" name="hsn_popup" id="hsn_popup" class="form-control mandatory"   readonly />
                  <input type="hidden" name="hsn_id" id="hsn_id" value="{{ $objItem->HSNID_REF }}" />
                </div>
                
                <div class="col-lg-2 pl"><p>Standard Custom Duty Rate %</p></div>
                <div class="col-lg-1 pl">
                  <input type="text" name="SCDRate" id="SCDRate" class="form-control rightalign" maxlength="8" value="{{ $objItem->CUSTOM_DUTY_RATE }}" autocomplete="off"  >
                </div>
                
                </div>
              
              
              <div class="row">
                

                <div class="col-lg-2 pl"><p>Inventory Valuation Method</p></div>
                  <div class="col-lg-2 pl">
                    <select name="IVM" id="IVM" class="form-control"  >
                      <option value="FIFO">FIFO</option>
                      <option value="Weighted Average">Weighted Average</option>
                    </select>
                  </div>
                
                
              
                <div class="col-lg-1 pl"><p>Business Unit</p></div>
                <div class="col-lg-2 pl">
                  <input type="text" name="busuni_popup" id="busuni_popup" class="form-control mandatory"  readonly />
                  <input type="hidden" name="busuni_id" id="busuni_id" value="{{ $objItem->BUID_REF }}" />
                </div>
                
                
                <div class="col-lg-1 pl"><p>Standard Rate</p></div>
                <div class="col-lg-1 pl">
                  
                  <input type="text" name="STDCOST" id="STDCOST" class="form-control rightalign "  maxlength="18" value="{{ $objItem->STDCOST }}" autocomplete="off" >
                  
                </div>
                
                <div class="col-lg-2 pl"><p>Standard SWS Rate %</p></div>
                <div class="col-lg-1 pl">
                  <input type="text" name="SSRate" id="SSRate" class="form-control rightalign"  maxlength="8" value="{{ $objItem->STD_SWS_RATE }}" autocomplete="off">
                </div>

              
                
              </div>
              
              
              <div class="row">
                
                        
                <div class="col-lg-2 pl"><p>Minimum Level</p></div>
                <div class="col-lg-1 pl">
                  <div class="col-lg-12 pl">
                  <input type="text" name="MINLEVEL" id="MINLEVEL" class="form-control rightalign"  maxlength="16" value="{{ $objItem->MINLEVEL }}" autocomplete="off">
                  </div>
                </div>
                
                <div class="col-lg-1 pl"><p>Reorder Level</p></div>
                <div class="col-lg-1 pl">
                  <div class="col-lg-12 pl">
                  <input type="text" name="REORDERLEVEL" id="REORDERLEVEL" class="form-control rightalign "  maxlength="16" value="{{ $objItem->REORDERLEVEL }}" autocomplete="off">
                  </div>
                </div>
                
                
                
                <div class="col-lg-1 pl"><p>Maximum Level</p></div>
                <div class="col-lg-1 pl">
                  
                  <input type="text" name="MAXLEVEL" id="MAXLEVEL" class="form-control rightalign" maxlength="16" value="{{ $objItem->MAXLEVEL }}" autocomplete="off">
                  
                </div>

                <div class="col-lg-1 pl"><p>Lead (Days)</p></div>
                <div class="col-lg-1 pl">
                  <input type="text" name="LEAD_DAYS" id="LEAD_DAYS" value="{{ $objItem->LEAD_DAYS }}" onkeypress="return isNumberKey(event,this)" class="form-control rightalign"  maxlength="4" autocomplete="off" >
                </div>

                <div class="col-lg-1 pl"><p>Shelf Life (Month)</p></div>
                <div class="col-lg-1 pl">
                  <input type="text" name="SHELF_LIFE" id="SHELF_LIFE" value="{{ $objItem->SHELF_LIFE }}" onkeypress="return isNumberKey(event,this)" class="form-control rightalign"  maxlength="4" autocomplete="off" >
                </div>
                
                
                
                
              </div>

              <div class="row">

                <div class="col-lg-2 pl"><p>Item Specification</p></div>
                <div class="col-lg-3 pl">
                  <input type="text" name="ITEM_SPECI" id="ITEM_SPECI" class="form-control "  maxlength="200" value="{{ $objItem->ITEM_SPECI }}" >
                </div>
              </div>

              <div class="row">
                <div class="col-lg-2 pl"><p>De-Activated</p></div>
                <div class="col-lg-1 pl pr">
                <input type="checkbox"   name="DEACTIVATED"  id="deactive-checkbox_0" {{$objItem->DEACTIVATED == 1 ? "checked" : ""}}
                 value='{{$objItem->DEACTIVATED == 1 ? 1 : 0}}'  >
                </div>
                
                <div class="col-lg-2 pl"><p>Date of De-Activated</p></div>
                <div class="col-lg-2 pl">
                <div class="col-lg-8 pl">
                <input type="date" name="DODEACTIVATED" class="form-control" id="DODEACTIVATED" placeholder="dd/mm/yyyy" 
                {{$objItem->DEACTIVATED == 1 ? "" : "disabled"}}
                   tabindex="8" value="{{ (!is_null($objItem->DODEACTIVATED) && $objItem->DODEACTIVATED!='1900-01-01')? 
                   \Carbon\Carbon::parse($objItem->DODEACTIVATED)->format('Y-m-d') : ''   }}" />
                </div>
                </div>
              </div>
                
            </div>

            <div class="container-fluid">

              <div class="row">
                <ul class="nav nav-tabs">
                  <li class="active"><a data-toggle="tab" href="#Attribute">Attribute</a></li>
                  <li><a data-toggle="tab" href="#CheckFlag">Check Flag</a></li>
                  <li><a data-toggle="tab" href="#TechnicalSpecification">Technical Specification</a></li>
                  <li><a data-toggle="tab" href="#UOMConversion">UOM Conversion</a></li>
                  <li><a data-toggle="tab" href="#ALPSSpecific">{{isset($TabSetting->TAB_NAME) && $TabSetting->TAB_NAME !=''?$TabSetting->TAB_NAME:'Additional Info'}}</a></li>
                  <li><a data-toggle="tab" href="#udf">UDF</a></li>
                </ul>
                
                <div class="tab-content">
                  {{-- one line mandatory --}}
                  <div id="Attribute" class="tab-pane fade in active">
                    <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:250px;width:60%;" >
                      <table id="table1" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                        <thead id="thead1"  style="position: sticky;top: 0">
                            <tr>
                            <th>Attribute Code
                              <input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1" value="{{$objattCount}}">
                            </th>
                            <th>Description</th>
                            <th>Value</th>
                            <th width="15%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        @if(!empty($objItemAttribute))
                          @foreach($objItemAttribute as $key => $row)
                            <tr  class="participantRow">
                              <td>
                                <input name={{"attrcode_popup_".$key}} id={{"txtattrcode_popup_".$key}} value ="{{$row->ATTID_REF}}" class="form-control" autocomplete="off" required readonly/>
                              </td>
                              <td hidden>
                                <input type="text" name={{"attrcode_".$key}} id={{"hdnattrcode_popup_".$key}} value ="{{$row->ATTID_REF}}" class="form-control" />
                              </td>  
                              <td >
                                <input  class="form-control w-100" type="text" name={{"attrdesciption_".$key}} value ="{{$row->ATTID_REF}}" id={{"txtattrdesciption_".$key}}  maxlength="50" readonly>
                              </td>
                              <td>
                                <input type="text" name={{"attrvalue_popup_".$key}} id={{"txtattrvalue_popup_".$key}} value ="{{$row->AVALUE}}" class="form-control w-100" autocomplete="off" required readonly/>
                              </td>
                              <td hidden>
                                <input type="text" name={{"attrvalue_".$key}} id={{"hdnattrvalue_popup_".$key}} value ="{{$row->AVALUE}}" class="form-control" />
                              </td> 

                            <td align="center" ><button class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></button><button class="btn remove" title="Delete" data-toggle="tooltip" type="button" @if($loop->iteration-1==0) disabled @endif><i class="fa fa-trash" ></i></button></td>
                            </tr>
                          @endforeach 
                        @endif  
                        </tbody>
                      </table>
                    </div>	
                  </div>
                  
                  <div id="CheckFlag" class="tab-pane fade">
                    
                    <div class="inner-form" style="margin-top:10px;">
                      <div class="row">
                        <div class="col-lg-2 pl"><p>QC Applicable</p></div>
                        <div class="col-lg-1 pl">
                          <select name="QCA" id="QCA" class="form-control mandatory"  >
                            <option {{isset($objFlage->QCA) && $objFlage->QCA =='1'?'selected="selected"':''}}  value="1">Yes</option>
                            <option {{isset($objFlage->QCA) && $objFlage->QCA=='0'?'selected="selected"':''}}  value="0" >No</option>
                          </select>
                        </div>

                        <div class="col-lg-2 pl"><p>Incentive Applicable</p></div>
                        <div class="col-lg-1 pl">
                          <select name="INCA" id="INCA" class="form-control mandatory"  >
                            <option {{isset($objFlage->INCA) && $objFlage->INCA =='1'?'selected="selected"':''}}  value="1">Yes</option>
                            <option {{isset($objFlage->INCA) && $objFlage->INCA=='0'?'selected="selected"':''}}  value="0" >No</option>
                          </select>
                        </div>

                      </div>
                      
                      <div class="row">
                        <div class="col-lg-2 pl"><p>Serial No Applicable</p></div>
                        <div class="col-lg-1 pl">
                          <select name="SRNOA" id="SRNOA" class="form-control mandatory"  >
                            <option  {{isset($objFlage->SRNOA) && $objFlage->SRNOA=='1'?'selected="selected"':''}}  value="1">Yes</option>
                            <option {{isset($objFlage->SRNOA) && $objFlage->SRNOA=='0'?'selected="selected"':''}}  value="0">No</option>
                          </select>
                        </div>

                        <div class="col-lg-2 pl"><p>BIN Required</p></div>
                        <div class="col-lg-1 pl">
                          <select name="BIN" id="BIN" class="form-control mandatory"  >
                            <option {{isset($objFlage->BIN) && $objFlage->BIN =='1'?'selected="selected"':''}}  value="1">Yes</option>
                            <option {{isset($objFlage->BIN) && $objFlage->BIN=='0'?'selected="selected"':''}}  value="0" >No</option>
                          </select>
                        </div>

                      </div>
                      <div class="row">
                        <div class="col-lg-2 pl"><p>Batch No / Lot No Applicable</p></div>
                        <div class="col-lg-1 pl">
                          <select name="BATCHNOA" id="BATCHNOA" class="form-control mandatory"  >
                            <option  {{isset($objFlage->BATCHNOA) && $objFlage->BATCHNOA=='1'?'selected="selected"':''}}  value="1">Yes</option>
                            <option {{isset($objFlage->BATCHNOA) && $objFlage->BATCHNOA=='0'?'selected="selected"':''}}  value="0">No</option>
                          </select>
                        </div>

                        <div class="col-lg-2 pl"><p>Warranty Applicable</p></div>
                        <div class="col-lg-1 pl">
                          <select name="WARA" id="WARA" class="form-control mandatory" onchange="getWarranty(this.value)"  >
                            <option {{isset($objFlage->WARA) && $objFlage->WARA =='1'?'selected="selected"':''}}  value="1">Yes</option>
                            <option {{isset($objFlage->WARA) && $objFlage->WARA=='0'?'selected="selected"':''}}  value="0" >No</option>
                          </select>
                        </div>

                      </div>
                      
                      <div class="row">
                        <div class="col-lg-2 pl"><p>Inventory Maintain</p></div>
                        <div class="col-lg-1 pl">
                          <select name="INVMANTAIN" id="INVMANTAIN" class="form-control mandatory"  >
                            <option  {{isset($objFlage->INVMANTAIN) && $objFlage->INVMANTAIN=='1'?'selected="selected"':''}}  value="1" >Yes</option>
                            <option {{isset($objFlage->INVMANTAIN) && $objFlage->INVMANTAIN=='0'?'selected="selected"':''}}  value="0" >No</option>
                          </select>
                        </div>

                        <div class="col-lg-2 pl"><p>Warranty(Month)</p></div>
                        <div class="col-lg-1 pl">
                          <input type="text" name="WARA_MONTH" id="WARA_MONTH" value="{{isset($objFlage->WARA_MONTH)?$objFlage->WARA_MONTH:0}}" onkeypress="return isNumberKey(event,this)" class="form-control mandatory" {{isset($objFlage->WARA) && $objFlage->WARA =='1'?'':'readonly'}} >
                        </div>

                      </div>


                      <div class="row">
                        <div class="col-lg-2 pl"><p>Bar Code Applicable</p></div>
                        <div class="col-lg-1 pl">
                          <select name="BARCODE_APPLICABLE" id="BARCODE_APPLICABLE" class="form-control mandatory"  >
                            <option {{isset($objFlage->BARCODE_APPLICABLE) && $objFlage->BARCODE_APPLICABLE=='1'?'selected="selected"':''}}  value="1">Yes</option>
                            <option {{isset($objFlage->BARCODE_APPLICABLE) && $objFlage->BARCODE_APPLICABLE=='0'?'selected="selected"':''}}  value="0" >No</option>
                          </select>
                        </div>
                        <div class="col-lg-2 pl"><p>Expiry Applicable</p></div>
                        <div class="col-lg-1 pl">
                          <select name="EXPIRY_APPLICABLE" id="EXPIRY_APPLICABLE" class="form-control mandatory"  >
                            <option  {{isset($objFlage->EXPIRY_APPLICABLE) && $objFlage->EXPIRY_APPLICABLE=='1'?'selected="selected"':''}}  value="1" >Yes</option>
                            <option {{isset($objFlage->EXPIRY_APPLICABLE) && $objFlage->EXPIRY_APPLICABLE=='0'?'selected="selected"':''}}  value="0" >No</option>
                          </select>
                        </div>
                      </div>

                      <div class="row" id="serial_no_mode" style="{{isset($objFlage->SRNOA) && $objFlage->SRNOA =='1'?'':'display:none;'}}"  >
                        <div class="col-lg-2 pl"><p>Serial No Mode</p></div>
                        <div class="col-lg-1 pl">
                          <select name="SERIALNO_MODE" id="SERIALNO_MODE" class="form-control mandatory"  >
                            <option  value="">Select</option>
                            <option {{isset($objFlage->SERIALNO_MODE) && $objFlage->SERIALNO_MODE=='MANUAL'?'selected="selected"':''}}  value="MANUAL">MANUAL</option>
                            <option {{isset($objFlage->SERIALNO_MODE) && $objFlage->SERIALNO_MODE=='AUTOMATIC'?'selected="selected"':''}}  value="AUTOMATIC" >AUTOMATIC</option>
                          </select>
                        </div>
                        <div id="automatic_mode" style="{{isset($objFlage->SERIALNO_MODE) && $objFlage->SERIALNO_MODE =='AUTOMATIC'?'':'display:none;'}}" >
                          <div class="col-lg-1 pl"><p>Prefix</p></div>
                          <div class="col-lg-1 pl">
                            <input type="text" id="SERIALNO_PREFIX" name="SERIALNO_PREFIX" value="{{isset($objFlage->SERIALNO_PREFIX)?$objFlage->SERIALNO_PREFIX:''}}" class="form-control mandatory" autocomplete="off" >
                          </div>

                          <div class="col-lg-1 pl"><p>Start From</p></div>
                          <div class="col-lg-1 pl">
                            <input type="text" id="SERIALNO_STARTS_FROM" name="SERIALNO_STARTS_FROM" value="{{isset($objFlage->SERIALNO_STARTS_FROM)?$objFlage->SERIALNO_STARTS_FROM:''}}" class="form-control mandatory" autocomplete="off" onkeypress="return isNumberKey(event,this)" >
                          </div>

                          <div class="col-lg-1 pl"><p>Suffix</p></div>
                          <div class="col-lg-1 pl">
                            <input type="text" id="SERIALNO_SUFFIX" name="SERIALNO_SUFFIX" value="{{isset($objFlage->SERIALNO_SUFFIX)?$objFlage->SERIALNO_SUFFIX:''}}" class="form-control mandatory" autocomplete="off" >
                          </div>

                          <div class="col-lg-1 pl"><p>Max Length</p></div>
                          <div class="col-lg-1 pl">
                            <input type="text" id="SERIALNO_MAX_LENGTH" name="SERIALNO_MAX_LENGTH" value="{{isset($objFlage->SERIALNO_MAX_LENGTH)?$objFlage->SERIALNO_MAX_LENGTH:''}}" class="form-control mandatory" autocomplete="off" onkeypress="return isNumberKey(event,this)" >
                          </div>
                        </div>

                      </div>


                      
                      <!--
                      <div class="row">
                        <div class="col-lg-2 pl"><p>TCS Applicable</p></div>
                        <div class="col-lg-1 pl">
                          <select name="TCS" id="TCS" class="form-control mandatory"  >
                            <option  value="1">Yes</option>
                            <option  value="0" selected>No</option>
                          </select>
                        </div>
                      </div>
                      -->
                      
                    </div>
                  </div>
                  
                  <div id="TechnicalSpecification" class="tab-pane fade">
                    <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:250px;width:50%;margin-top:10px;" >
                      <table id="table2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                        <thead id="thead1"  style="position: sticky;top: 0">
                            <tr>
                            <th>TS Type <input class="form-control" type="hidden" name="Row_Count2" id ="Row_Count2" value="{{$objspecCount}}"> </th>
                            <th>Value</th>
                            <th width="15%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        @if(!empty($objItemTechSpecification))
                          @foreach($objItemTechSpecification as $tskey => $tsrow)
                            <tr  class="participantRow">
                            <td><input  class="form-control w-100" type="text" name={{"TSTYPE_".$tskey}} id={{"TSTYPE_".$tskey}} value="{{$tsrow->TSTYPE}}" maxlength="50" ></td>
                            <td><input  class="form-control w-100" type="text" name={{"TSVALUE_".$tskey}} id={{"TSVALUE_".$tskey}} value="{{$tsrow->VALUE}}" maxlength="100" ></td>
                            <td align="center" ><button class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></button><button class="btn remove" title="Delete" data-toggle="tooltip" type="button"  @if( ($loop->iteration-1)==0) disabled @endif><i class="fa fa-trash" ></i></button></td>
                            </tr>
                          <tr></tr>
                          @endforeach 
                        @else
                        <tr  class="participantRow">
                          <td><input  class="form-control w-100" type="text" name="TSTYPE_0" id="TSTYPE_0" maxlength="50" ></td>
                          <td><input  class="form-control w-100" type="text" name="TSVALUE_0" id="TSVALUE_0" maxlength="100" ></td>
                          <td align="center" ><a class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></a><button class="btn remove" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
                          </tr>
                        @endif 
                      </tbody>
                      </table>
                    </div>	
                  </div>
                  
                  <div id="UOMConversion" class="tab-pane fade">
                    <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:250px;width:50%;margin-top:10px;" >
                      <table id="table3" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                        <thead id="thead1"  style="position: sticky;top: 0">
                            <tr>
                            <th width="25%" >From UOM <input class="form-control" type="hidden" name="Row_Count3" id ="Row_Count3" value="{{$objuomCount}}">  </th>
                            <th width="10%">Qty</th>
                            <th width="25%" >To UOM</th>
                            <th  width="10%">Qty</th>
                            <th width="15%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        @if(!empty($objItemUOMConv))
                          @foreach($objItemUOMConv as $iokey => $iorow)
                            <tr  class="participantRow">
                            <td>
                              <input  class="form-control w-100" type="text" name={{"txt_from_uom_".$iokey}} id={{"txt_from_uom_".$iokey}}  value="{{$iorow->FROM_UOMID_REF}}"  maxlength="50" required readonly />
                            </td>
                            <td hidden>
                            <input  class="form-control w-100" type="text" name={{"hdntxt_from_uomid_".$iokey}} id={{"hdntxt_from_uomid_".$iokey}} value="{{$iorow->FROM_UOMID_REF}}" maxlength="50" />
                            </td>
                            <td>
                              <input  class="form-control w-100" type="text" name={{"FROM_QTY_".$iokey}} ID={{"TXT_FROM_QTY_".$iokey}} maxlength="5" value="{{$iorow->FROM_QTY}}" readonly >
                            </td>
                            <td>
                              <input name={{"touom_popup_".$iokey}} id={{"txttouom_popup_".$iokey}} class="form-control" autocomplete="off" value="{{$iorow->TO_UOMID_REF}}"  readonly />
                            </td>
                            <td hidden>
                              <input type="text" name={{"touom_".$iokey}} id={{"hdntouom_popup_".$iokey}} class="form-control" value="{{$iorow->TO_UOMID_REF}}" />
                            </td>
                            
                            <td><input  class="form-control w-100 rightalign" type="text" name={{"TO_QTY_".$iokey}} id={{"TXT_TO_QTY_".$iokey}} value="{{$iorow->TO_QTY}}" autocomplete="off" ></td>
                            <td align="center" ><button class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></button><button class="btn remove" title="Delete" data-toggle="tooltip" type="button" @if( ($loop->iteration-1)==0) disabled @endif><i class="fa fa-trash" ></i></button></td>
                            </tr>
                          @endforeach 
                        @endif 
                        </tbody>
                      </table>
                    </div>	
                  </div>
                  <div id="ALPSSpecific" class="tab-pane fade">
                    
                    <div class="inner-form" style="margin-top:10px;">
                      <div class="row">
                        <div class="col-lg-2 pl"><p>{{isset($TabSetting->FIELD1) && $TabSetting->FIELD1 !=''?$TabSetting->FIELD1:'Add. Customer Code'}}</p></div>
                        <div class="col-lg-2 pl">
                        <input type="text" name="SAP_CUSTOMER_CODE" id="SAP_CUSTOMER_CODE" value="{{ $objItem->SAP_CUSTOMER_CODE }}" class="form-control" style="text-transform:uppercase">
                        </div>
                        <div class="col-lg-2 pl"><p>{{isset($TabSetting->FIELD2) && $TabSetting->FIELD2 !=''?$TabSetting->FIELD2:'Add. Customer Name'}}</p></div>
                        <div class="col-lg-2 pl">
                        <input type="text" name="SAP_CUSTOMER_NAME" id="SAP_CUSTOMER_NAME" value="{{ $objItem->SAP_CUSTOMER_NAME }}" class="form-control">
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-lg-2 pl"><p>{{isset($TabSetting->FIELD3) && $TabSetting->FIELD3 !=''?$TabSetting->FIELD3:'Add. Part Number'}}</p></div>
                        <div class="col-lg-2 pl">
                        <input type="text" name="SAP_PART_NO" id="SAP_PART_NO" value="{{ $objItem->SAP_PART_NO }}" class="form-control" style="text-transform:uppercase">
                        </div>
                        <div class="col-lg-2 pl"><p>{{isset($TabSetting->FIELD4) && $TabSetting->FIELD4 !=''?$TabSetting->FIELD4:'Add. Part Description'}}</p></div>
                        <div class="col-lg-2 pl">
                        <input type="text" name="SAP_PART_DESC" id="SAP_PART_DESC" value="{{ $objItem->SAP_PART_DESC }}" class="form-control">
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-lg-2 pl"><p>{{isset($TabSetting->FIELD5) && $TabSetting->FIELD5 !=''?$TabSetting->FIELD5:'Add. Customer Part No'}}</p></div>
                        <div class="col-lg-2 pl">
                        <input type="text" name="SAP_CUST_PARTNO" id="SAP_CUST_PARTNO" value="{{ $objItem->SAP_CUSTOMER_PARTNO }}" class="form-control" style="text-transform:uppercase">
                        </div>
                        <div class="col-lg-2 pl"><p>{{isset($TabSetting->FIELD6) && $TabSetting->FIELD6 !=''?$TabSetting->FIELD6:'Add. Market & Set Code'}}</p></div>
                        <div class="col-lg-2 pl">
                        <input type="text" name="SAP_MARTKET_SETCODE" id="SAP_MARTKET_SETCODE" value="{{ $objItem->SAP_MARKET_SETCODE }}" class="form-control" style="text-transform:uppercase">
                        </div>
                      </div>
                      
 
                      
                      <div class="row">
                        <div class="col-lg-2 pl"><p>{{isset($TabSetting->FIELD7) && $TabSetting->FIELD7 !=''?$TabSetting->FIELD7:'Rounding Value/LOT Size Qty'}}</p></div>
                        <div class="col-lg-2 pl">
                        <input type="text" name="LOTSIZEQTY" id="LOTSIZEQTY" value="{{ $objItem->ROUNDING_VALUE }}" class="form-control">
                        </div>
                        <div class="col-lg-2 pl"><p>{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</p></div>
                        <div class="col-lg-2 pl">
                        <input type="text" name="ALPS_PART_NO" id="ALPS_PART_NO" value="{{ $objItem->ALPS_PART_NO }}" class="form-control">
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-lg-2 pl"><p>{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</p></div>
                        <div class="col-lg-2 pl">
                        <input type="text" name="CUSTOMER_PART_NO" id="CUSTOMER_PART_NO" value="{{ $objItem->CUSTOMER_PART_NO }}" class="form-control">
                        </div>
                        <div class="col-lg-2 pl"><p>{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</p></div>
                        <div class="col-lg-2 pl">
                        <input type="text" name="OEM_PART_NO" id="OEM_PART_NO" value="{{ $objItem->OEM_PART_NO }}" class="form-control">
                        </div>
                      </div>
     
                      
                    </div>
                  </div>


               <div id="udf" class="tab-pane fade">
              <div class="table-responsive table-wrapper-scroll-y " style="margin-top:10px;height:350px;width:50%;">
                <table id="udffietable" class="display nowrap table table-striped table-bordered itemlist" style="height:auto !important;">
                  <thead id="thead1"  style="position: sticky;top: 0">
                    <tr >
                    <th>UDF Fields <input class="form-control" type="hidden" name="Row_Count4" id ="Row_Count4" value="{{ $objudfCount }}"> </th>
                    <th>Value / Comments</th>
                    </tr>
                  </thead>
                  <tbody>
                      @foreach($objItemUDF as $udfkey => $udfrow)
                        <tr  class="participantRow">
                          <td>
                            <input name={{"udffie_popup_".$udfkey}} id={{"txtudffie_popup_".$udfkey}} value="{{$udfrow->LABEL}}" class="form-control @if ($udfrow->ISMANDATORY==1) mandatory @endif" autocomplete="off" maxlength="100" disabled/>
                          </td>

                          <td hidden>
                            <input type="text" name='{{"udffie_".$udfkey}}' id='{{"hdnudffie_popup_".$udfkey}}' value="{{$udfrow->UDFITEMID_REF}}" class="form-control" maxlength="100" />
                          </td>

                          <td hidden>
                            <input type="text" name={{"udffieismandatory_".$udfkey}} id={{"udffieismandatory_".$udfkey}} class="form-control" maxlength="100" value="{{$udfrow->ISMANDATORY}}" />
                          </td>

                          <td id="{{"tdinputid_".$udfkey}}">
                            {{-- dynamic input --}}
                            @php
                              
                              $dynamicid = "udfvalue_".$udfkey;
                              $chkvaltype = strtolower($udfrow->VALUETYPE); 

                            if($chkvaltype=='date'){

                              $strinp = '<input type="date" placeholder="dd/mm/yyyy" name="'.$dynamicid.'" id="'.$dynamicid.'" class="form-control" value="'.$udfrow->UDF_VALUE.'" /> ';       

                            }else if($chkvaltype=='time'){

                                $strinp= '<input type="time" placeholder="h:i" name="'.$dynamicid.'" id="'.$dynamicid.'" class="form-control"  value="'.$udfrow->UDF_VALUE.'"/> ';

                            }else if($chkvaltype=='numeric'){
                            $strinp = '<input type="text" name="'.$dynamicid. '" id="'.$dynamicid.'" class="form-control" value="'.$udfrow->UDF_VALUE.'"/> ';

                            }else if($chkvaltype=='text'){

                            $strinp = '<input type="text" name="'.$dynamicid. '" id="'.$dynamicid.'" class="form-control" value="'.$udfrow->UDF_VALUE.'"/> ';

                            }else if($chkvaltype=='boolean'){
                                $boolval = ''; 
                                if($udfrow->UDF_VALUE=='on' || $udfrow->UDF_VALUE=='1' ){
                                  $boolval="checked";
                                }
                                $strinp = '<input type="checkbox" name="'.$dynamicid. '" id="'.$dynamicid.'" class=""  '.$boolval.' /> ';

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

                              $strinp = '<select name="'.$dynamicid.'" id="'.$dynamicid.'" class="form-control" >'.$opts.'</select>' ;


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
          </form>
  </div><!--container-fluid filter-->
@endsection
@section('alert')
<!-- Alert -->
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
            <button class="btn alertbt" name='OkBtn1' id="OkBtn1" style="margin-left: 90px;display:none;" onclick="getFocus()" >
              <div id="alert-active" class="activeOk"></div>OK</button>
              <input type="hidden" id="FocusId" >
        </div><!--btdiv-->
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Alert -->

<div id="glrefpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='glrefpopup_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>GL</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="gl_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th class="ROW1" style="width: 10%" align="center">Select</th> 
            <th class="ROW2" style="width: 40%" >Code</th>
            <th class="ROW3" style="width: 40%" >Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
          <td class="ROW2"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control"   id="gl_codesearch" onkeyup="searchGLCode()" /></td>
          <td class="ROW3"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control"   id="gl_namesearch" onkeyup="searchGLName()" /></td>
        </tr>
        </tbody>
      </table>
      
      <table id="gl_tab2" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody>
        @foreach ($objGlList as $index=>$GlList)

        <tr >
          <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_GLID_REF[]" id="glref_{{ $GlList->GLID }}" class="clsglref" value="{{ $GlList->GLID }}" ></td>
          <td class="ROW2" style="width: 39%">{{ $GlList->GLCODE }}
          <input type="hidden" id="txtglref_{{ $GlList->GLID }}" data-desc="{{ $GlList->GLCODE }} - {{ $GlList->GLNAME }}" value="{{ $GlList-> GLID }}"/>
          </td>
          <td class="ROW3" style="width: 39%">{{ $GlList->GLNAME }}</td>
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

<!-- Inventory Class -->
<div id="invclapopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='invclapopup_close' >&times;</button>
      </div>
  <div class="modal-body">
	  <div class="tablename"><p>Inventory Class</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="example2345" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
      <tr>
        <th class="ROW1" style="width: 10%" align="center">Select</th> 
        <th class="ROW2" style="width: 40%" >Code</th>
        <th class="ROW3" style="width: 40%" >Name</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
        <td class="ROW2"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control"   id="invclcodesearch" onkeyup="myFunction()" /></td>
        <td class="ROW3"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control"   id="invclnamesearch" onkeyup="myNameFunction()" /></td>
      </tr>
    </tbody>
    </table>
      <table id="example23" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          <!-- <tr>
            <th>Code</th>
            <th>Name</th>
          </tr> -->
          <tr id="none-select" class="searchalldata" hidden>
            <td><input type="text" id="invclasearch" > </td>
            <td><input type="text" > </td>
          </tr>
        </thead>
        <tbody>
        @foreach ($ObjMstInventoryClass as $index=>$InvClaRow)
        <tr >
          <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_CLASSID_REF[]" id="invclacode_{{ $InvClaRow->CLASSID }}" class="clsinventory" value="{{ $InvClaRow-> CLASSID }}" ></td>
          <td class="ROW2" style="width: 39%">{{ $InvClaRow-> CLASS_CODE }}
              <input type="hidden" id="txtinvclacode_{{ $InvClaRow-> CLASSID }}" data-desc="{{ $InvClaRow-> CLASS_CODE }} - {{ $InvClaRow-> CLASS_DESC }}" value="{{ $InvClaRow-> CLASSID }}"/>
          </td>
          <td class="ROW3" style="width: 39%">{{ $InvClaRow->CLASS_DESC }}</td>
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
<!-- Inventory Class -->

<!-- maiuomref_popup Class -->
<div id="maiuomrefpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='maiuomrefpopup_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Main UoM</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="muomtable1" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
      <tr>
        <th class="ROW1" style="width: 10%" align="center">Select</th> 
        <th class="ROW2" style="width: 40%" >Code</th>
        <th class="ROW3" style="width: 40%" >Name</th>
      </tr>
    </thead>
    <tbody>
    <tr>
      <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
      <td class="ROW2"  style="width: 40%">
            <input type="text" autocomplete="off"  class="form-control" id="muomcodesearch" onkeyup="mymuomFunction()">
        </td>
        <td class="ROW3"  style="width: 40%">
          <input type="text" autocomplete="off"  class="form-control" id="muomnamesearch" onkeyup="mymuomNameFunction()">
        </td>
    </tr>
    </tbody>
    </table>
      <table id="muomtable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          <!-- <tr>
            <th>Code</th>
            <th>Name</th>
          </tr>-->
          <tr hidden>
            <td ><input type="text" id="maiuomref_search" > </td>
            <td><input type="text" > </td>
          </tr>
        </thead>
        <tbody>
        @foreach ($ObjMstUOM as $index=>$UOMRow)

        <tr >
          <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_UOMID_REF[]"  id="maiuomref_{{ $UOMRow->UOMID }}" class="clsmaiuomref" value="{{ $UOMRow->UOMID }}" ></td>
          <td class="ROW2" style="width: 39%">{{ $UOMRow->UOMCODE }}
          <input type="hidden" id="txtmaiuomref_{{ $UOMRow->UOMID }}" data-desc="{{ $UOMRow->UOMCODE }} - {{ $UOMRow->DESCRIPTIONS }}" value="{{ $UOMRow-> UOMID }}"/>
          </td>
          <td class="ROW3" style="width: 39%">{{ $UOMRow->DESCRIPTIONS }}</td>
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
<!-- maiuomref_popup Class -->

<!-- altuomref_popup -->
<div id="altuomrefpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='altuomrefpopup_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Alt UoM</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="auomtable1" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
      <tr>
        <th class="ROW1" style="width: 10%" align="center">Select</th> 
        <th class="ROW2" style="width: 40%" >Code</th>
        <th class="ROW3" style="width: 40%" >Name</th>
      </tr>
    </thead>
    <tbody>
    <tr>
      <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
      <td class="ROW2"  style="width: 40%">
        <input type="text"  autocomplete="off"  class="form-control"  id="auomcodesearch" onkeyup="myauomFunction()" />
      </td>
      <td class="ROW3"  style="width: 40%">
        <input type="text"  autocomplete="off"  class="form-control"  id="auomnamesearch" onkeyup="myauomNameFunction()" />
      </td>
    </tr>
    </tbody>
    </table>
      <table id="auomtable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          <!-- <tr>
            <th>Code</th>
            <th>Name</th>
          </tr> -->
          <tr hidden>
            <td><input type="text" id="altuomref_search" > </td>
            <td><input type="text" ></td>
          </tr>
        </thead>
        <tbody>
        @foreach ($ObjMstUOM as $index=>$UOMRow)

        <tr>
          <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_ALTUOMID_REF[]"  id="altuomref_{{ $UOMRow->UOMID }}" class="clsaltuomref" value="{{ $UOMRow->UOMID  }}" /></td>
          <td class="ROW2" style="width: 39%">{{ $UOMRow->UOMCODE }}
           <input type="hidden" id="txtaltuomref_{{ $UOMRow->UOMID }}" data-desc="{{ $UOMRow->UOMCODE }} - {{ $UOMRow->DESCRIPTIONS }}" value="{{ $UOMRow-> UOMID }}"/>
          </td>
          <td class="ROW3" style="width: 39%">{{ $UOMRow->DESCRIPTIONS }}</td>
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
<!-- altuomref_popup -->

<!-- item group -->
<div id="itegrppopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='itegrppopup_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Item Group</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="itmgrp1" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
      <tr>
        <th class="ROW1" style="width: 10%" align="center">Select</th> 
        <th class="ROW2" style="width: 40%" >Code</th>
        <th class="ROW3" style="width: 40%" >Name</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
        <td class="ROW2"  style="width: 40%">
            <input type="text" autocomplete="off"  class="form-control"  id="itmgrp1codesearch" onkeyup="itmgrp1Function()"/>
        </td>
        <td class="ROW3"  style="width: 40%">
            <input type="text" autocomplete="off"  class="form-control"  id="itmgrp1namesearch" onkeyup="itmgrp1NameFunction()"/>
        </td>
      </tr>
    </tbody>
    </table>
      <table id="itmgrp2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <!-- <tr>
            <th>Code</th>
            <th>Name</th>
          </tr>-->
          <tr hidden>
            <td><input type="text" id="altuomref_search" > </td>
            <td><input type="text" ></td>
          </tr>
        </thead>
        <tbody>
        @foreach ($ObjMstItemGroup as $index=>$ItemGroupRow)
          <tr >
            <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_ITEMGID_REF[]"  id="itegrp_{{ $ItemGroupRow->ITEMGID }}" class="clsitegrp" value="{{ $ItemGroupRow->ITEMGID }}" /></td>
            <td class="ROW2" style="width: 39%">{{ $ItemGroupRow->GROUPCODE }}
            <input type="hidden" id="txtitegrp_{{ $ItemGroupRow->ITEMGID }}" data-desc="{{ $ItemGroupRow->GROUPCODE }} - {{ $ItemGroupRow->GROUPNAME }}" value="{{ $ItemGroupRow-> ITEMGID }}"/>
            </td>
            <td class="ROW3" style="width: 39%">{{ $ItemGroupRow->GROUPNAME }}</td>
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
<!-- item group -->


<!-- item subgroup -->
<div id="itesubgrppopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='itesubgrppopup_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Item Sub Group</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="itmsubgrp1" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
      <tr>
        <th class="ROW1" style="width: 10%" align="center">Select</th> 
        <th class="ROW2" style="width: 40%" >Code</th>
        <th class="ROW3" style="width: 40%" >Name</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
        <td class="ROW2"  style="width: 40%">
            <input type="text" autocomplete="off"  class="form-control"  id="itmsubgrp1codesearch" onkeyup="itmsubgrp1Function()" />
        </td>
        <td class="ROW3"  style="width: 40%">
            <input type="text" autocomplete="off"  class="form-control"  id="itmsubgrp1namesearch" onkeyup="itmsubgrp1NameFunction()" />
        </td>
      </tr>
    </tbody>
    </table>
      <table id="itmsubgrp2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          <!-- <tr>
            <th>Code</th>
            <th>Name</th>
          </tr> -->
          <tr hidden>
            <td><input type="text" id="itesubgrp_search" > </td>
            <td><input type="text" ></td>
          </tr>
        </thead>
        <tbody id="tbody_itesubgrp">
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- item subgroup -->

<!-- itecat_popup -->
<div id="itecatpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='itecatpopup_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Item Category</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="itmcat1" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
      <tr>
        <th class="ROW1" style="width: 10%" align="center">Select</th> 
        <th class="ROW2" style="width: 40%" >Code</th>
        <th class="ROW3" style="width: 40%" >Name</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
        <td class="ROW2"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="itmcat1codesearch" onkeyup="itmcat1Function()" /></td>
        <td class="ROW3"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="itmcat1namesearch" onkeyup="itmcat1NameFunction()"/> </td>
      </tr>
    </tbody>
    </table>
      <table id="itmcat2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          <!-- <tr>
            <th>Code</th>
            <th>Name</th>
          </tr> -->
          <tr hidden>
            <td><input type="text" id="itecat_search" > </td>
            <td><input type="text" ></td>
          </tr>
        </thead>
        <tbody>
        @foreach ($ObjMstItemCategory as $index=>$ItemCatRow)
        <tr >
          <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_ICID_REF[]" id="itecat_{{ $ItemCatRow->ICID }}" class="clsitecat" value="{{ $ItemCatRow->ICID  }}" /></td>
          <td class="ROW2" style="width: 39%">{{ $ItemCatRow->ICCODE }}
            <input type="hidden" id="txtitecat_{{ $ItemCatRow->ICID }}" data-desc="{{ $ItemCatRow->ICCODE }} - {{ $ItemCatRow->DESCRIPTIONS }}" value="{{ $ItemCatRow-> ICID }}"/>
          </td>
          <td class="ROW3" style="width: 39%">{{ $ItemCatRow->DESCRIPTIONS }}</td>
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
<!-- itecat_popup -->

<!-- defsto_popup -->
<div id="defstopopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='defstopopup_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Default Store</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="defstd1" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
      <tr>
        <th class="ROW1" style="width: 10%" align="center">Select</th> 
        <th class="ROW2" style="width: 40%" >Code</th>
        <th class="ROW3" style="width: 40%" >Name</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
        <td class="ROW2"  style="width: 40%">
          <input type="text" autocomplete="off"  class="form-control"  id="defstd1codesearch" onkeyup="defstd1Function()" />
        </td>
        <td class="ROW3"  style="width: 40%">
          <input type="text" autocomplete="off"  class="form-control"  id="defstd1namesearch" onkeyup="defstd1NameFunction()" />
        </td>
      </tr>
    </tbody>
    </table>
      <table id="defstd2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          <!-- <tr>
            <th>Code</th>
            <th>Name</th>
          </tr> -->
          <tr hidden>
            <td><input type="text" id="defsto_search" > </td>
            <td><input type="text" ></td>
          </tr>
        </thead>
        <tbody>
        @foreach ($ObjMstStore as $index=>$StoreRow)
        <tr >
          <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_STID_REF[]"  id="defsto_{{ $StoreRow->STID }}" class="clsdefsto" value="{{ $StoreRow->STID }}" /></td>
          <td class="ROW2" style="width: 39%">{{ $StoreRow->STCODE }}
            <input type="hidden" id="txtdefsto_{{ $StoreRow->STID }}" data-desc="{{ $StoreRow->STCODE }} - {{ $StoreRow->NAME }}" value="{{ $StoreRow-> STID }}"/>
          </td>
          <td class="ROW3" style="width: 39%">{{ $StoreRow->NAME }}</td>
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
<!-- defsto_popup -->

<!-- hsn_popup -->
<div id="hsnpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='hsnpopup_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>HSN Code</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="hsncd1" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
      <tr>
        <th class="ROW1" style="width: 10%" align="center">Select</th> 
        <th class="ROW2" style="width: 40%" >Code</th>
        <th class="ROW3" style="width: 40%" >Name</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
        <td class="ROW2"  style="width: 40%">
          <input type="text" autocomplete="off"  class="form-control"  id="hsncd1codesearch" onkeyup="hsncd1Function()" />
        </td>
        <td class="ROW3"  style="width: 40%">
          <input type="text" autocomplete="off"  class="form-control"  id="hsncd1namesearch" onkeyup="hsncd1NameFunction()"/>
        </td>
      </tr>
    </tbody>
    </table>
      <table id="hsncd2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          <!-- <tr>
            <th>Code</th>
            <th>Name</th>
          </tr> -->
          <tr hidden>
            <td><input type="text" id="hsn_search" > </td>
            <td><input type="text" ></td>
          </tr>
        </thead>
        <tbody>
        @foreach ($ObjMstHSN as $index=>$HSNRow)
        <tr >
          <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_HSNID_REF[]" id="hsn_{{ $HSNRow->HSNID }}" class="clshsn" value="{{ $HSNRow->HSNID }}" /></td>
          <td class="ROW2" style="width: 39%">{{ $HSNRow->HSNCODE }}
          <input type="hidden" id="txthsn_{{ $HSNRow->HSNID }}" data-desc="{{ $HSNRow->HSNCODE }} - {{ $HSNRow->HSNDESCRIPTION }}" value="{{ $HSNRow-> HSNID }}"/>
          </td>
          <td class="ROW3" style="width: 39%">{{ $HSNRow->HSNDESCRIPTION }}</td>
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
<!-- hsn_popup -->

<!-- busuni_popup -->
<div id="busunipopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='busunipopup_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Bussiness Unit</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="bsnunt1" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
      <tr>
        <th class="ROW1" style="width: 10%" align="center">Select</th> 
        <th class="ROW2" style="width: 40%" >Code</th>
        <th class="ROW3" style="width: 40%" >Name</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
        <td  class="ROW2"  style="width: 40%">
            <input type="text" autocomplete="off"  class="form-control"  id="bsnunt1codesearch" onkeyup="bsnunt1Function()" />
        </td>
        <td  class="ROW3"  style="width: 40%">
            <input type="text" autocomplete="off"  class="form-control"  id="bsnunt1namesearch" onkeyup="bsnunt1NameFunction()" />
        </td>
      </tr>
    </tbody>
    </table>
      <table id="bsnunt2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          <!-- <tr>
            <th>Code</th>
            <th>Name</th>
          </tr> -->
          <tr hidden>
            <td><input type="text" id="busuni_search" > </td>
            <td><input type="text" ></td>
          </tr>
        </thead>
        <tbody>
        @foreach ($ObjMstBusinessUnit as $index=>$BUnitRow)
        <tr >
          <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_BUID_REF[]" id="busuni_{{ $BUnitRow->BUID }}" class="clsbusuni" value="{{ $BUnitRow->BUID }}" /></td>
          <td class="ROW2" style="width: 39%">{{ $BUnitRow->BUCODE }}
          <input type="hidden" id="txtbusuni_{{ $BUnitRow->BUID }}" data-desc="{{ $BUnitRow->BUCODE }} - {{ $BUnitRow->BUNAME }}" value="{{ $BUnitRow-> BUID }}"/>
          </td>
          <td class="ROW3" style="width: 39%">{{ $BUnitRow->BUNAME }}</td>
        </tr>
        @endforeach
        <tr id="busuni_0" class="clsbusuni">
          <td colspan="3">  Clear  <i class="fa fa-trash"></i>
          <input type="hidden" id="busuni_0" data-desc="" value="">
          </td>          
        </tr>
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- busuni_popup -->


<!-- attrcode Dropdown -->
<div id="attrcodepopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='attrcode_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Attribute Code</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="attrcodetable2" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
      <tr>
        <th class="ROW1" style="width: 10%" align="center">Select</th> 
        <th class="ROW2" style="width: 40%" >Code</th>
        <th class="ROW3" style="width: 40%" >Name</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
        <td class="ROW2"  style="width: 40%">
            <input type="text" autocomplete="off"  class="form-control"  id="attr2codesearch" onkeyup="attr2Function()" />
        </td>
        <td class="ROW3"  style="width: 40%">
            <input type="text" autocomplete="off"  class="form-control"  id="attr2namesearch" onkeyup="attr2NameFunction()" />
        </td>
      </tr>
    </tbody>
    </table>
    <table id="attrcodetable" class="display nowrap table  table-striped table-bordered" width="100%">
      <thead id="thead2">
        <tr id="none-select" class="searchalldata" hidden>
            
          <td> <input type="text" name="fieldid" id="hdn_attrcodefieldid"/>
          <input type="text" name="fieldid2" id="hdn_attrcodefieldid2"/>
          <input type="text" name="fieldid3" id="hdn_attrcodefieldid3"/>
        </td>
        </tr>
        <!-- <tr>
          <th>Code</th>
          <th>Name</th>
        </tr> -->
        <tr hidden>
          <td><input type="text" id="attrcode_search" > </td>
          <td><input type="text" ></td>
        </tr>
      </thead>
      <tbody>
      @foreach ($ObjMstAttribute  as $index=>$AttRow)
      <tr >
        <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_ATTID_REF[]"  id="attrcode_{{ $AttRow->ATTID }}" class="clsattrcode" value="{{ $AttRow->ATTID }}" /></td>
        <td class="ROW2" style="width: 39%">{{ $AttRow->ATTCODE }}
        <input type="hidden" id="txtattrcode_{{ $AttRow->ATTID }}" data-desc="{{ $AttRow->ATTCODE }} - {{ $AttRow->DESCRIPTIONS }}" value="{{ $AttRow-> ATTID }}" data-attrdesc="{{ $AttRow->DESCRIPTIONS }}" value="{{ $AttRow->DESCRIPTIONS }}"  />
        </td>
        <td class="ROW3" style="width: 39%">{{ $AttRow->DESCRIPTIONS }}</td>
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
<!-- attrcode Dropdown-->

<!-- attrvalue Dropdown -->
<div id="attrvaluepopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='attrvalue_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Value</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="attrvalueTable2" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
    <tr>
            <!-- <th>Code</th> -->
            <th class="ROW1" style="width: 10%" align="center">Select</th>
            <th class="ROW2" style="width: 90%">Name</th>
    </tr>
    </thead>
    <tbody>
    <tr>
    <!-- <td>
    <input type="text" id="attrvaluecodesearch" onkeyup="attr2Function()">
    </td> -->
    <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
    <td class="ROW2"  style="width: 90%">
        <input type="text" autocomplete="off"  class="form-control" id="attrvaluenamesearch" onkeyup="attrvalueNameFunction()" />
    </td>
    </tr>
    </tbody>
    </table>
    <table id="attrvalueTable" class="display nowrap table  table-striped table-bordered" width="100%">
      <thead id="thead2">
        <tr id="none-select" class="searchalldata" hidden>           
          <td> 
            <input type="text" name="fieldid" id="hdn_attrvaluefieldid"/>
            <input type="text" name="fieldid2" id="hdn_attrvaluefieldid2"/>
          </td>
        </tr>
        <!-- <tr>
            <th>Name</th>
        </tr> -->
        <tr hidden>
          <td><input type="text" id="attrvalue_search" > </td>
        </tr>
      </thead>
      <tbody id="tbody_attrvalue">
      </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- attrvalue Dropdown-->


<!-- touom Dropdown -->
<div id="touompopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='touom_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>To UOM</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="touomexample23" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
      <tr>
        <th class="ROW1" style="width: 10%" align="center">Select</th> 
        <th class="ROW2" style="width: 40%" >Code</th>
        <th class="ROW3" style="width: 40%" >Name</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
        <td class="ROW2"  style="width: 40%">
            <input type="text" autocomplete="off"  class="form-control"  id="touomcodesearch" onkeyup="touomFunction()" />
        </td>
        <td class="ROW3"  style="width: 40%">
            <input type="text" autocomplete="off"  class="form-control"  id="touomnamesearch" onkeyup="touomNameFunction()"/>
        </td>
      </tr>
    </tbody>
    </table>
    <table id="touomexample2345" class="display nowrap table  table-striped table-bordered" width="100%">
      <thead id="thead2">
        <tr id="none-select" class="searchalldata" hidden>
            
          <td> <input type="text" name="fieldid" id="hdn_touomfieldid"/>
          <input type="text" name="fieldid2" id="hdn_touomfieldid2"/></td>
        </tr>
        <!-- <tr>
          <th>Code</th>
          <th>Name</th>
        </tr> -->
        <tr hidden>
          <td><input type="text" id="touom_search" > </td>
          <td><input type="text" ></td>
        </tr>
      </thead>
      <tbody>
      @foreach ($ObjMstUOM as $index=>$UOMRow)
      <tr >
        <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_TOUOMID_REF[]" id="touom_{{ $UOMRow->UOMID }}" class="clstouom" value="{{ $UOMRow->UOMID }}" /></td>
        <td class="ROW2" style="width: 39%" >{{ $UOMRow->UOMCODE }}
        <input type="hidden" id="txttouom_{{ $UOMRow->UOMID }}" data-desc="{{ $UOMRow->UOMCODE }} - {{ $UOMRow->DESCRIPTIONS }}" value="{{ $UOMRow-> UOMID }}"/>
        </td>
        <td class="ROW3" style="width: 39%">{{ $UOMRow->DESCRIPTIONS }}</td>
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
<!-- touom Dropdown-->

<!-- udffie Dropdown -->
<div id="udffiepopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='udffie_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>UDF Fields</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="udffieexample23" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
    <tr>
          <th>Label</th>
          <th>Type</th>
    </tr>
    </thead>
    <tbody>
    <tr>
    <td>
    <input type="text" id="udffiecodesearch" onkeyup="udffieFunction()">
    </td>
    <td>
    <input type="text" id="udffienamesearch" onkeyup="udffieNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
    <table id="udffieexample2345" class="display nowrap table  table-striped table-bordered" width="100%">
      <thead id="thead2">
        <tr id="none-select" class="searchalldata" hidden>
            
        <td>
            <input type="hidden" name="fieldid" id="hdn_udffiefieldid"/>
            <input type="hidden" name="fieldid2" id="hdn_udffiefieldid2"/>
            <input type="hidden" name="fieldid2" id="hdn_udffiefieldid3"/>
            <input type="hidden" name="fieldid4" id="hdn_udffiefieldid4"/>
        </td>
        </tr>
        <!-- <tr>
          <th>Label</th>
          <th>Type</th>
        </tr> -->
      </thead>
      <tbody>{{-- data-optscombo="{{ $UdffieRow->DESCRIPTIONS }}"      --}}
      @foreach ($objUdfForItems as $index=>$UdffieRow)
      <tr id="udffie_{{ $UdffieRow->UDFITEMID }}" class="clsudffie">
        <td >{{$UdffieRow->LABEL }}  <input type="hidden" id="txtudffie_{{ $UdffieRow->UDFITEMID }}" value="{{ $UdffieRow-> UDFITEMID }}"
          data-desc="{{ $UdffieRow->LABEL }}"  data-ismandatory="{{ $UdffieRow->ISMANDATORY }}"  data-valtype="{{ $UdffieRow->VALUETYPE }}"         data-optscombo="{{ $UdffieRow->DESCRIPTIONS }}"  />
        </td>
        <td >{{ $UdffieRow->VALUETYPE }} {{ $UdffieRow->ISMANDATORY }}
          </td>
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
<!-- udffie Dropdown-->

@endsection

@push('bottom-css')

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
      let tid = "#muomtable2";
      let tid2 = "#muomtable1";
      let headers = document.querySelectorAll(tid2 + " th");

      // Sort the table element when clicking on the table headers
      headers.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(tid, ".clsmaiuomref", "td:nth-child(" + (i + 1) + ")");
        });
      });

      let tidauom = "#auomtable2";
      let tidauom2 = "#auomtable1";
      let headersauom = document.querySelectorAll(tidauom2 + " th");

      // Sort the table element when clicking on the table headers
      headersauom.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(tidauom, ".clsaltuomref", "td:nth-child(" + (i + 1) + ")");
        });
      });

      let tiditmgrp = "#itmgrp2";
      let tiditmgrp2 = "#itmgrp1";
      let headersitmgrp = document.querySelectorAll(tiditmgrp2 + " th");

      // Sort the table element when clicking on the table headers
      headersitmgrp.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(tiditmgrp, ".clsitegrp", "td:nth-child(" + (i + 1) + ")");
        });
      });

      let tidsubitmgrp = "#itmsubgrp2";
      let tidsubitmgrp2 = "#itmsubgrp1";
      let headerssubitmgrp = document.querySelectorAll(tidsubitmgrp2 + " th");

      // Sort the table element when clicking on the table headers
      headerssubitmgrp.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(tidsubitmgrp, ".clsitesubgrp", "td:nth-child(" + (i + 1) + ")");
        });
      });

      let tiditmcat = "#itmcat2";
      let tiditmcat2 = "#itmcat1";
      let headersitmcat = document.querySelectorAll(tiditmcat2 + " th");

      // Sort the table element when clicking on the table headers
      headersitmcat.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(tiditmcat, ".clsitecat", "td:nth-child(" + (i + 1) + ")");
        });
      });

      let tidhsncd = "#hsncd2";
      let tidhsncd2 = "#hsncd1";
      let headershsncd = document.querySelectorAll(tidhsncd2 + " th");

      // Sort the table element when clicking on the table headers
      headershsncd.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(tidhsncd, ".clshsn", "td:nth-child(" + (i + 1) + ")");
        });
      });

      let tiddefstd = "#defstd2";
      let tiddefstd2 = "#defstd1";
      let headersdefstd = document.querySelectorAll(tiddefstd2 + " th");

      // Sort the table element when clicking on the table headers
      headersdefstd.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(tiddefstd, ".clsdefsto", "td:nth-child(" + (i + 1) + ")");
        });
      });

      let tidbsnunt = "#bsnunt2";
      let tidbsnunt2 = "#bsnunt1";
      let headersbsnunt = document.querySelectorAll(tidbsnunt2 + " th");

      // Sort the table element when clicking on the table headers
      headersbsnunt.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(tidbsnunt, ".clsbusuni", "td:nth-child(" + (i + 1) + ")");
        });
      });

      let tidattr2 = "#attrcodetable";
      let tidattr22 = "#attrcodetable2";
      let headersattr2 = document.querySelectorAll(tidattr22 + " th");

      // Sort the table element when clicking on the table headers
      headersattr2.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(tidattr2, ".clsattrcode", "td:nth-child(" + (i + 1) + ")");
        });
      });

      let tidattrvalue = "#attrvalueTable";
      let tidattrvalue2 = "#attrvalueTable2";
      let headersattrvalue = document.querySelectorAll(tidattrvalue2 + " th");

      // Sort the table element when clicking on the table headers
      headersattrvalue.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(tidattrvalue, ".clsattrvalue", "td:nth-child(" + (i + 1) + ")");
        });
      });

      let tidtouom = "#touomexample2345";
      let tidtouom2 = "#touomexample23";
      let headerstouom = document.querySelectorAll(tidtouom2 + " th");

      // Sort the table element when clicking on the table headers
      headerstouom.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(tidtouom, ".clstouom", "td:nth-child(" + (i + 1) + ")");
        });
      });

      let tidudffie = "#udffieexample2345";
      let tidudffie2 = "#udffieexample23";
      let headersudffie = document.querySelectorAll(tidudffie2 + " th");

      // Sort the table element when clicking on the table headers
      headersudffie.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(tidudffie, ".clsudffie", "td:nth-child(" + (i + 1) + ")");
        });
      });

      let tidgl = "#example23";
      let tidgl2 = "#example2345";
      let headersgl = document.querySelectorAll(tidgl2 + " th");

      // Sort the table element when clicking on the table headers
      headersgl.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(tidgl, ".clsinventory", "td:nth-child(" + (i + 1) + ")");
        });
      });

$("#invcls_popup").focus(function(event){
   // if(event.keyCode==13){
     $("#invclapopup").show();
     // event.preventDefault();
      //  }
        event.preventDefault();
  });

  $("#invclapopup_close").on("click",function(event){
    $("#invclapopup").hide();
  });

  $('.clsinventory').click(function(){
      var id = $(this).attr('id');
      var txtval =    $("#txt"+id+"").val();
      var texdesc =   $("#txt"+id+"").data("desc")
      
      $("#invcls_popup").val(texdesc);
      $("#invcls_id").val(txtval);

       $("#invcls_popup").blur();

      $("#invclapopup").hide();
      $("#invclcodesearch").val(''); 
      $("#invclnamesearch").val(''); 
      myFunction();
      $(this).prop("checked",false);
      event.preventDefault();
  });

  function myFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("invclcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("example23");
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

  function myNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("invclnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("example23");
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

  //--------2
  $("#maiuomref_popup").focus(function(event){
    //if(event.keyCode==13){
     $("#maiuomrefpopup").show();
     event.preventDefault();
    //}
  });

  $("#maiuomrefpopup_close").on("click",function(event){ 
    $("#maiuomrefpopup").hide();
  });

  $('.clsmaiuomref').click(function(){
      var id = $(this).attr('id');
      var txtval =    $("#txt"+id+"").val();
      var texdesc =   $("#txt"+id+"").data("desc")
      
      $("#maiuomref_popup").val(texdesc);
      $("#maiuomref_id").val(txtval);

      $("[id*='txt_from_uom']").val(texdesc);
      $("[id*='hdntxt_from_uomid']").val( txtval);

      $("[id*='txttouom_popup']").val('');
      $("[id*='hdntouom_popup']").val('');
      $("[id*='TXT_TO_QTY']").val('');      
      
      
      $("#maiuomref_popup").blur(); 

      $("#maiuomrefpopup").hide();
      $("#muomcodesearch").val(''); 
      $("#muomnamesearch").val(''); 
      $(this).prop("checked",false);
      mymuomFunction();
  });

  function mymuomFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("muomcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("muomtable2");
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

  function mymuomNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("muomnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("muomtable2");
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

  //--------3
  $("#altuomref_popup").focus(function(event){
    //if(event.keyCode==13){
     $("#altuomrefpopup").show();
     event.preventDefault();
    //}
  });

  $("#altuomrefpopup_close").on("click",function(event){ 
    $("#altuomrefpopup").hide();
  });

  $('.clsaltuomref').click(function(){
      var id = $(this).attr('id');
      var txtval =    $("#txt"+id+"").val();
      var texdesc =   $("#txt"+id+"").data("desc")
      
      var maiuomref_id_val = $.trim($("#maiuomref_id").val());


      if(maiuomref_id_val==''){
              $("#alert").modal('show');
              $("#AlertMessage").text('Please select Main UoM');
              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn").hide();
              $("#OkBtn1").show();
              $("#OkBtn1").focus();
              highlighFocusBtn('activeOk1');

      }
      // else if(txtval == maiuomref_id_val)
      // {

      //       $("#alert").modal('show');
      //       $("#AlertMessage").text('Main UoM and ALT UOM can not be same.');
      //       $("#YesBtn").hide();
      //       $("#NoBtn").hide();
      //       $("#OkBtn").hide();
      //       $("#OkBtn1").show();
      //       $("#OkBtn1").focus();
      //       $("#altuomref_popup").val('');
      //       $("#altuomref_id").val('');
      //       highlighFocusBtn('activeOk1');

      // }
      else{

            $("#altuomref_popup").val(texdesc);
            $("#altuomref_id").val(txtval);
            $("#altuomref_popup").blur();
      }
    
      $("#altuomrefpopup").hide();
      $("#auomcodesearch").val(''); 
      $("#auomnamesearch").val(''); 
      $(this).prop("checked",false);
      myauomFunction();
  });

  function myauomFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("auomcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("auomtable2");
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

  function myauomNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("auomnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("auomtable2");
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

  //--------4
  $("#itegrp_popup").focus(function(event){
    //if(event.keyCode==13){
     $("#itegrppopup").show();
    var id = $('#itesubgrp_popup').attr('id');
    var id2 = $('#itesubgrp_id').attr('id');
    $('#hdn_fieldid').val(id);
    $('#hdn_fieldid2').val(id2);
    event.preventDefault();
    //}
  });

  $("#itegrppopup_close").on("click",function(event){ 
    $("#itegrppopup").hide();
  });

  $('.clsitegrp').click(function(){
      var id = $(this).attr('id');
      var txtval =    $("#txt"+id+"").val();
      var texdesc =   $("#txt"+id+"").data("desc")
      
      $("#itegrppopup").hide();
      
      $("#itegrp_popup").val(texdesc);
      $("#itegrp_popup").blur();
      $("#itegrp_id").val(txtval);

      //sub group
      var customid = txtval;
        if(customid!=''){
          $("#itesubgrp_popup").val('');
          $("#itesubgrp_id").val('');
          $('#tbody_itesubgrp').html('<tr><td colspan="2">Loading..</td></tr>');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url:'{{route("master",[72,"getsubgroup"])}}',
                type:'POST',
                data:{'id':customid},
                success:function(data) {
                    $('#tbody_itesubgrp').html(data);
                    bindSubGroupEvents();
                },
                error:function(data){
                  console.log("Error: Something went wrong.");
                  $('#tbody_itesubgrp').html('');
                },
            });        
        }
        ////sub group end
      $("#itmgrp1codesearch").val(''); 
      $("#itmgrp1namesearch").val(''); 
      $(this).prop("checked",false);
      itmgrp1Function();
      event.preventDefault();
  });

  function itmgrp1Function() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("itmgrp1codesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("itmgrp2");
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

  function itmgrp1NameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("itmgrp1namesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("itmgrp2");
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


  //--------5
  $("#itesubgrp_popup").focus(function(event){
    //if(event.keyCode==13){
     $("#itesubgrppopup").show();
     event.preventDefault();
    //}
  });

  $("#itesubgrppopup_close").on("click",function(event){ 
    $("#itesubgrppopup").hide();
  });

  function bindSubGroupEvents(){
        $('.clsitesubgrp').click(function(){
    
            var id = $(this).attr('id');
            var txtval =    $("#txt"+id+"").val();
            var texdesc =   $("#txt"+id+"").data("desc")
            
            $("#itesubgrp_popup").val(texdesc);
            $("#itesubgrp_popup").blur();
            $("#itesubgrp_id").val(txtval);
            $("#itesubgrppopup").hide();
            $("#itmsubgrp1codesearch").val(''); 
            $("#itmsubgrp1namesearch").val(''); 
            itmsubgrp1Function();
            $(this).prop("checked",false);
            event.preventDefault();
        });
  }

  function itmsubgrp1Function() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("itmsubgrp1codesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("itmsubgrp2");
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

  function itmsubgrp1NameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("itmsubgrp1namesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("itmsubgrp2");
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


  //--------6
  $("#itecat_popup").focus(function(event){
    //if(event.keyCode==13){
     $("#itecatpopup").show();
      event.preventDefault();
    //}
  });

  $("#itecatpopup_close").on("click",function(event){ 
    $("#itecatpopup").hide();
  });

  $('.clsitecat').click(function(){
      var id = $(this).attr('id');
      var txtval =    $("#txt"+id+"").val();
      var texdesc =   $("#txt"+id+"").data("desc")
      
      $("#itecat_popup").val(texdesc);
      $("#itecat_id").val(txtval);
    
      $("#itecat_popup").blur();
      $("#itecatpopup").hide();
      $("#itmcat1codesearch").val(''); 
      $("#itmcat1namesearch").val(''); 
      itmcat1Function();
      $(this).prop("checked",false);
      event.preventDefault();
  });

  function itmcat1Function() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("itmcat1codesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("itmcat2");
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

  function itmcat1NameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("itmcat1namesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("itmcat2");
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

  //--------7
  $("#defsto_popup").focus(function(event){
    //if(event.keyCode==13){
     $("#defstopopup").show();
     event.preventDefault();
    //}
  });

  $("#defstopopup_close").on("click",function(event){ 
    $("#defstopopup").hide();
  });

  $('.clsdefsto').click(function(){
      var id = $(this).attr('id');
      var txtval =    $("#txt"+id+"").val();
      var texdesc =   $("#txt"+id+"").data("desc")
      
      $("#defsto_popup").val(texdesc);
      $("#defsto_id").val(txtval);
    
      $("#defsto_popup").blur();
      $("#defstopopup").hide();
      $("#defstd1codesearch").val(''); 
      $("#defstd1namesearch").val(''); 
      defstd1Function();
      $(this).prop("checked",false);
      event.preventDefault();
  });

  function defstd1Function() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("defstd1codesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("defstd2");
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

  function defstd1NameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("defstd1namesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("defstd2");
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

    //--------8
    $("#hsn_popup").focus(function(event){
      //if(event.keyCode==13){
      $("#hsnpopup").show();
      event.preventDefault();
       //$("#hsn_search").focus();
      //}
    });

    $("#hsnpopup_close").on("click",function(event){ 
      $("#hsnpopup").hide();
    });

    $('.clshsn').click(function(){
        var id = $(this).attr('id');
        var txtval =    $("#txt"+id+"").val();
        var texdesc =   $("#txt"+id+"").data("desc")
        
        $("#hsn_popup").val(texdesc);
        $("#hsn_id").val(txtval);
      
        $("#hsn_popup").blur();
        $("#hsnpopup").hide();
        $("#hsncd1codesearch").val(''); 
        $("#hsncd1namesearch").val(''); 
        hsncd1Function();
        $(this).prop("checked",false);
        event.preventDefault();
    });
    function hsncd1Function() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("hsncd1codesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("hsncd2");
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

  function hsncd1NameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("hsncd1namesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("hsncd2");
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

    //--------9
    $("#busuni_popup").focus(function(event){
     // if(event.keyCode==13){
      $("#busunipopup").show();
      event.preventDefault();
     // $("#busuni_search").focus();
     // }
    });

    $("#busunipopup_close").on("click",function(event){ 
      $("#busunipopup").hide();
    });

    $('.clsbusuni').click(function(){
        var id = $(this).attr('id');
        var txtval =    $("#txt"+id+"").val();
        var texdesc =   $("#txt"+id+"").data("desc")
        
        

        var ITEMNAME = $('#NAME').val();
        
        //check in alps case 
        var objcompany = <?php echo json_encode(strtolower($objCOMPANY)); ?>;     
        if (objcompany.indexOf("alps")!=-1)
        {
            checkDuplicateNameAlps(txtval);
        }

        //replace old value after check
        $("#busuni_popup").val(texdesc);
        $("#busuni_id").val(txtval);

        $("#busuni_popup").blur();
        $("#busunipopup").hide();
        $("#bsnunt1codesearch").val(''); 
        $("#bsnunt1namesearch").val(''); 
        bsnunt1Function();
        $(this).prop("checked",false);
        event.preventDefault();
    });
    function bsnunt1Function() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("bsnunt1codesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("bsnunt2");
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

  function bsnunt1NameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("bsnunt1namesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("bsnunt2");
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


  //attribute code
  $('#table1').on ("focus","[id*='txtattrcode_popup']",function(event){
        
        $("#attrcodepopup").show();
        var id = $(this).attr('id');
        var id2 = $(this).parent().parent().find('[id*="hdnattrcode_popup"]').attr('id');
        var id3 = $(this).parent().parent().find('[id*="txtattrdesciption"]').attr('id');

        $('#hdn_attrcodefieldid').val(id);
        $('#hdn_attrcodefieldid2').val(id2);        
        $('#hdn_attrcodefieldid3').val(id3);        
  });

  $("#attrcode_closePopup").on("click",function(event){
        $("#attrcodepopup").hide();
  });

  $('#attrcodetable').on("click",".clsattrcode",function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc")
        var txtattrdesc =   $("#txt"+fieldid+"").data("attrdesc")

        var txtid= $('#hdn_attrcodefieldid').val();
        var txt_id2= $('#hdn_attrcodefieldid2').val();
        var txt_id3= $('#hdn_attrcodefieldid3').val();
        
        $('#'+txtid).val(texdesc);
        $('#'+txt_id2).val(txtval);
        $('#'+txt_id3).val(txtattrdesc);  
        $('#'+txtid).blur();  

        //clear 
        $('#'+txtid).parent().parent().find('[id*="txtattrvalue_popup"]').val('');
        $('#'+txtid).parent().parent().find('[id*="hdnattrvalue_popup"]').val('');

        $("#attrcodepopup").hide();
        $("#attr2codesearch").val(''); 
        $("#attr2namesearch").val(''); 
        attr2Function();
        $(this).prop("checked",false);
        event.preventDefault();

});
//attribute code end  


//attribute vaule
$('#table1').on ("focus","[id*='txtattrvalue_popup']",function(event){
          $("#attrvaluepopup").show();
        var id = $(this).attr('id');
        var id2 = $(this).parent().parent().find('[id*="hdnattrvalue_popup"]').attr('id');

        var id3 = $(this).parent().parent().find('[id*="hdnattrcode_popup"]').attr('id');

        $('#hdn_attrvaluefieldid').val(id);
        $('#hdn_attrvaluefieldid2').val(id2);    


        var id3value = $('#'+id3+'').val();  //hdnattrcode_popup

  
        if(id3value!=''){
        //----------
          // $("#itesubgrp_popup").val('');
          // $("#itesubgrp_id").val('');
          $('#tbody_attrvalue').html('<tr><td colspan="2">Please wait..</td></tr>');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url:'{{route("master",[72,"getAttValData"])}}',
                type:'POST',
                data:{'id':id3value},
                success:function(data) {
                    $('#tbody_attrvalue').html(data);
                    bindAttrValueEvents();
                },
                error:function(data){
                  console.log("Error: Something went wrong.");
                  $('#tbody_attrvalue').html('');
                },
            });   
        }      

    });

  $("#attrvalue_closePopup").on("click",function(event){
        $("#attrvaluepopup").hide();
  });

  function bindAttrValueEvents(){

    $('#attrvalueTable').off();  //unbind all previous

     $('#attrvalueTable').on("click",".clsattrvalue",function(){

        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc")

        var txtid= $('#hdn_attrvaluefieldid').val();
        var txt_id2= $('#hdn_attrvaluefieldid2').val();
  
        var selected_data  = [];
        
        $("#table1 .participantRow").each(function () {

          $('#'+txtid).val(texdesc);
          $('#'+txt_id2).val(txtval);
          $('#'+txtid).blur();

          var attr_code_id = $(this).find('[id*="hdnattrcode_popup"]').val();
          var attr_value_id = $(this).find('[id*="hdnattrvalue_popup"]').val();

          if(attr_code_id==''){
            console.log('attr_code_id blank'); 
                $("#alert").modal('show');
                $("#AlertMessage").text('Please select Attribute code.');
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk1');
                $("#attrvaluepopup").hide();
                return false;
          }

          if(attr_code_id!='' && attr_value_id!='')
          {
                var both_value = attr_code_id+''+attr_value_id;
                if(jQuery.inArray(both_value, selected_data) !== -1){
                    $("#alert").modal('show');
                    $("#AlertMessage").text('Attribute code and value must be unique.');
                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn1").show();
                    $("#OkBtn1").focus();
                    highlighFocusBtn('activeOk1');
                    $("#attrvaluepopup").hide();
                    $('#'+txtid).val('');
                    $('#'+txt_id2).val('');
                    return false;
                }else{
                  selected_data.push(attr_code_id+''+attr_value_id);              
                }
          }

        }); //loop on row

        $("#attrvaluepopup").hide();
        $("#attrvaluenamesearch").val(''); 
        attrvalueNameFunction();
      });//dbl click
      event.preventDefault();
  } ///bindAttrValueEvents

  function attr2Function() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("attr2codesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("attrcodetable");
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

  function attr2NameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("attr2namesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("attrcodetable");
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

  function attrvalueNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("attrvaluenamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("attrvalueTable");
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
  //attribute value end  

  //------------------------
  //uom converion
  $('#table3').on ("focus","[id*='txttouom_popup']",function(event){
          $("#touompopup").show();
        var id = $(this).attr('id');
        var id2 = $(this).parent().parent().find('[id*="hdntouom_popup"]').attr('id');

        $('#hdn_touomfieldid').val(id);
        $('#hdn_touomfieldid2').val(id2);        
  });

  $("#touom_closePopup").on("click",function(event){
        $("#touompopup").hide();
  });


 // $('#table3').on("dblclick",".clstouom",function(){

  $(".clstouom").click(function(){

        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc")
        
        var txtid= $('#hdn_touomfieldid').val();
        var txt_id2= $('#hdn_touomfieldid2').val();

        $('#'+txtid).val(texdesc);
        $('#'+txt_id2).val(txtval);
        
        //////---------------------
        var selected_data  = [];
        
        $("#table3 .participantRow").each(function () {

          $('#'+txtid).val(texdesc);
          $('#'+txt_id2).val(txtval);

          var from_uom_id = $(this).find('[id*="hdntxt_from_uomid"]').val();
          var to_uom_id = $(this).find('[id*="hdntouom_popup"]').val();

          if(from_uom_id==''){
                $("#alert").modal('show');
                $("#AlertMessage").text('Please select Main UoM');
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk1');
                $("#touompopup").hide();
                $('#'+txtid).val('');
                $('#'+txt_id2).val('');
                return false;
          }

          if(from_uom_id!='')
          {
            if(from_uom_id==to_uom_id){
                    $("#alert").modal('show');
                    $("#AlertMessage").text("From UOM and To UOM can not be same");
                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn1").show();
                    $("#OkBtn1").focus();
                    highlighFocusBtn('activeOk1');
                    $("#touompopup").hide();
                    $('#'+txtid).val('');
                    $('#'+txt_id2).val('');
                    return false;
            }

                  if(to_uom_id!='')
                  {
                    var both_value = from_uom_id+''+to_uom_id;
                    if(jQuery.inArray(both_value, selected_data) !== -1){
                        $("#alert").modal('show');
                        $("#AlertMessage").text('Duplicate coversion row.');
                        $("#YesBtn").hide();
                        $("#NoBtn").hide();
                        $("#OkBtn1").show();
                        $("#OkBtn1").focus();
                        highlighFocusBtn('activeOk1');
                        $("#touompopup").hide();
                        $('#'+txtid).val('');
                        $('#'+txt_id2).val('');
                        return false;
                    }else{
                      selected_data.push(from_uom_id+''+to_uom_id);              
                    }
                  }  


          }

        }); //loop on row  
        /////---------------------  

        $("#touompopup").hide();
        $("#touomcodesearch").val(''); 
        $("#touomnamesearch").val(''); 
        touomFunction();
        $(this).prop("checked",false);
        event.preventDefault();
  });

  function touomFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("touomcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("touomexample2345");
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

  function touomNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("touomnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("touomexample2345");
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
  //uom converion end
  //----------------------
  //------------------------
  //udf field
  $('#udffietable').on ("focus","[id*='txtudffie_popup']",function(event){
          $("#udffiepopup").show();
        var id = $(this).attr('id');
        var id2 = $(this).parent().parent().find('[id*="hdnudffie_popup"]').attr('id');
        var id3 = $(this).parent().parent().find('[id*="udffieismandatory"]').attr('id');
        var id4 = $(this).parent().parent().find('[id*="tdinputid"]').attr('id');      //id - of creating dynamic input

        $('#hdn_udffiefieldid').val(id);
        $('#hdn_udffiefieldid2').val(id2);      
        $('#hdn_udffiefieldid3').val(id3);      
        $('#hdn_udffiefieldid4').val(id4);      
  });

  $("#udffie_closePopup").on("click",function(event){
    $("#udffiepopup").hide();
  });

  //$('#udffieexample2345').on ("dblclick",".clsudffie",function(event){
  $(".clsudffie").dblclick(function(){

    var udffound=false;

    var fieldid = $(this).attr('id');
    var txtval =    $("#txt"+fieldid+"").val();
    var texdesc =   $("#txt"+fieldid+"").data("desc");
    var txtismandatory =   $("#txt"+fieldid+"").data("ismandatory");
    var txtvaltype =   $("#txt"+fieldid+"").data("valtype");
    //var txtoptscombo =   $("#txt"+fieldid+"").data("data-optscombo");

    var txtid= $('#hdn_udffiefieldid').val();
    var txt_id2= $('#hdn_udffiefieldid2').val();
    var txt_id3 = $('#hdn_udffiefieldid3').val();
    var txt_id4 = $('#hdn_udffiefieldid4').val();  //<td> id 


    //--------
    $("[id*=hdnudffie_popup]").each(function(){

      console.log('no='+$(this).val());
      if($(this).val()==txtval){
            $("#alert").modal('show');
            $("#AlertMessage").text('Duplicate UDF field.');
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk1');
            $("#udffiepopup").hide();
            udffound=true;
            return false;
      }
        
    });

    //--------  
   
  if(!udffound){


    var strdyn = txt_id4.split('_');
    var lastele =   strdyn[strdyn.length-1];

    var dynamicid = "udfvalue_"+lastele;

    var chkvaltype =  txtvaltype.toLowerCase();
    var strinp = '';

    if(chkvaltype=='date'){

      strinp = '<input type="date" placeholder="dd/mm/yyyy" name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" /> ';       

    }else if(chkvaltype=='time'){
      strinp= '<input type="time" placeholder="h:i" name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" /> ';

    }else if(chkvaltype=='numeric'){
      strinp = '<input type="text" name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" /> ';

    }else if(chkvaltype=='text'){

      strinp = '<input type="text" name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" /> ';
    
    }else if(chkvaltype=='boolean'){

      strinp = '<input type="checkbox" name="'+dynamicid+ '" id="'+dynamicid+'" class="" /> ';
    
    }else if(chkvaltype=='combobox'){

      var txtoptscombo =   $("#txt"+fieldid+"").data("optscombo");
      var strarray = txtoptscombo.split(',');
      var opts = '';

      for (var i = 0; i < strarray.length; i++) {
        opts = opts + '<option value="'+strarray[i]+'">'+strarray[i]+'</option> ';
      }

      strinp = '<select name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" required>'+opts+'</select>' ;

    }

    $('#'+txt_id4).html('');  
    $('#'+txt_id4).html(strinp);   //set dynamic input
    
    $('#'+txtid).val(texdesc);  // lable
    $('#'+txt_id2).val(txtval);  // udfitemid
    $('#'+txt_id3).val(txtismandatory); // mandatory


    $("#udffiepopup").hide();
    $("#udffienamesearch").val(''); 
    $("#udffiecodesearch").val(''); 
    udffieFunction();

  }
    

  });

  function udffieFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("udffiecodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("udffieexample2345");
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

  function udffieNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("udffienamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("udffieexample2345");
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

  //check blank
  $('#udffietable').on ("blur","[id*='udfvalue']",function(event){

        var ismand = $(this).parent().parent().find('[id*="udffieismandatory"]').val();
        var txtval = $.trim( $(this).val() );

        if(ismand==1 && txtval==""){
                $("#alert").modal('show');
                $("#AlertMessage").text('Please enter value.');
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk1');
                $(this).focus();
                return false;
        }

   
  });
  //udf end
  
  //technical specifiation
  $('#table2').on("blur",'[id*="TSVALUE"]', function( event ) {
    
        $("#table2 .participantRow").each(function () {
          var txt_tstype = $.trim($(this).find('[id*="TSTYPE"]').val() );
          var txt_tsvalue = $.trim($(this).find('[id*="TSVALUE"]').val() );
          if(txt_tstype!==''){
            if(txt_tsvalue=='')
            {
                    $("#alert").modal('show');
                    $("#AlertMessage").text('Please enter TS value');
                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn1").show();
                    $("#OkBtn1").focus();
                    highlighFocusBtn('activeOk1');
                    return false;
            }
          }
        }); //loop on row
  });
  
  //----------------------



  var formItemMst = $( "#frm_mst_item" );
      formItemMst.validate();
  

    function validateSingleElemnet(element_id){
      var validator =$("#frm_mst_item" ).validate();
         if(validator.element( "#"+element_id+"" )){
            //check duplicate code
          // if(element_id=="ICODE" || element_id=="icode" ) {
          //   checkDuplicateCode();
          // }

          if(element_id=="NAME" || element_id=="name" ) {
            //do not chack on alps case
            var objcompany = <?php echo json_encode(strtolower($objCOMPANY)); ?>;    
            if (objcompany.indexOf("alps")==-1)
            {
              checkDuplicateName();
            }
            
          }

         }
    }

   
   

    // //check duplicate  code
    function checkDuplicateCode(){
      
        //validate and save data
        //var objForm = $("#frm_mst_item");
        var codedata = $("#ICODE").val(); 
        var rcdid = $("#RECORDID").val(); 
        //var formData = objForm.serialize();
        //var codedata2 = codedata.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("master",[72,"codedupeditmode"])}}',
            type:'POST',
            data:{'ICODE': codedata,'RECORDID': rcdid},
            success:function(data) {
                if(data.exists) {
                    $(".text-danger").hide();
                    showError('ERROR_ICODE',data.msg);
                 
                    
                }                                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
              
            },
        });

    }

    // //check duplicate  NAME
    function checkDuplicateName(){
      
        var namedata = $("#NAME").val(); 
        var rcdid = $("#RECORDID").val(); 

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("master",[72,"namedupeditmode"])}}',
            type:'POST',
            data:{'NAME': namedata,'RECORDID': rcdid},
            success:function(data) {
                if(data.exists) {
                    $(".text-danger").hide();
                    showError('ERROR_NAME',data.msg);
                    $("#NAME").focus();
        
                }                                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
        
            },
        });
        
    }

    //check duplicate apls  NAME
    function checkDuplicateNameAlps(buid){
        
        var namedata = $("#NAME").val(); 
        var olditemid = $("#RECORDID").val(); 
        var oldbuid = $("#busuni_id").val();         

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("master",[72,"nameduplicatealpseditmode"])}}',
            type:'POST',
            data:{'NAME': namedata,'BUID_REF':buid,'olditemid':olditemid,'oldbuid':oldbuid},
            success:function(data) {
                if(data.exists) {
                    $(".text-danger").hide();
                    showError('ERROR_NAME',data.msg);
                    $("#NAME").focus();
                }                                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });
    }


function getFocus(){
  var FocusId = $("#FocusId").val();
  $("#"+FocusId).focus();
  $("#closePopup").click();
}

function validateForm(actionType,actionMsg){
  $("#FocusId").val('');
      
  if($.trim($("#ICODE").val()) ===""){
    $("#FocusId").val('ICODE');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    $("#alert").modal('show');
    $("#AlertMessage").text('Please enter Item Code.');
    return false;
  }
  else if($.trim($("#NAME").val()) ===""){
    $("#FocusId").val('NAME');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    $("#alert").modal('show');
    $("#AlertMessage").text('Please enter Name.');
    return false;
  }  
  else if($.trim($("#invcls_id").val()) ===""){
    $("#FocusId").val('invcls_popup');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    $("#alert").modal('show');
    $("#AlertMessage").text('Please select Inventory Class.');
    return false;
  } 
  else if($.trim($("#maiuomref_id").val()) ===""){
    $("#FocusId").val('maiuomref_popup');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    $("#alert").modal('show');
    $("#AlertMessage").text('Please select Main UoM.');
    return false;
  } 
  else if($.trim($("#altuomref_id").val()) ===""){
    $("#FocusId").val('altuomref_popup');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    $("#alert").modal('show');
    $("#AlertMessage").text('Please select ALT UOM.');
    return false;
  }
  else if($.trim($("#ITEM_TYPE").val()) ===""){
    $("#FocusId").val('ITEM_TYPE');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    $("#alert").modal('show');
    $("#AlertMessage").text('Please select Item Type.');
    return false;
  }
  else if($.trim($("#itecat_id").val()) ===""){
    $("#FocusId").val('itecat_popup');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    $("#alert").modal('show');
    $("#AlertMessage").text('Please select Item Category.');
    return false;
  }
  else if($.trim($("#itegrp_id").val()) ===""){
    $("#FocusId").val('itegrp_popup');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    $("#alert").modal('show');
    $("#AlertMessage").text('Please select Item Group.');
    return false;
  }
  else if($.trim($("#itesubgrp_id").val()) ===""){
    $("#FocusId").val('itesubgrp_popup');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    $("#alert").modal('show');
    $("#AlertMessage").text('Please select Sub Group.');
    return false;
  }
  else if($.trim($("#defsto_id").val()) ===""){
    $("#FocusId").val('defsto_popup');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    $("#alert").modal('show');
    $("#AlertMessage").text('Please select Default Store.');
    return false;
  }
  else if($.trim($("#hsn_id").val()) ===""){
    $("#FocusId").val('hsn_popup');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    $("#alert").modal('show');
    $("#AlertMessage").text('Please select HSN Code.');
    return false;
  }
  else if('{{$checkCompany}}' !='' && $.trim($("#busuni_id").val()) ===""){
    $("#FocusId").val('busuni_popup');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    $("#alert").modal('show');
    $("#AlertMessage").text('Please select Business Unit.');
    return false;
  }
  else if($("#deactive-checkbox_0").is(":checked") == true && $.trim($("#DODEACTIVATED").val()) ===""){
    $("#FocusId").val('DODEACTIVATED');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    $("#alert").modal('show');
    $("#AlertMessage").text('Please select Date of De-Activated.');
    return false;
  }
  else{
    event.preventDefault();
    var allblank2 = [];
    var allblank3 = [];
    var allblank4 = [];
    var allblank5 = [];
    var allblank6 = [];
    var allblank7 = [];
    var allblank8 = [];
    var allblank9 = [];
                   
    $("[id*=txtattrcode_popup_]").each(function(){
        if($.trim($(this).val())!="")
        {
            allblank3.push('true');
            if($.trim($(this).parent().parent().find('[id*="txtattrvalue_popup_"]').val()) != "")
              {
                allblank2.push('true');
              }
            else
              {
                allblank2.push('false');
              } 
        }
        else
        {
            allblank3.push('false');
        } 
    });

    $("[id*=TSTYPE]").each(function(){
        if($.trim($(this).val())!="")
        {
            allblank4.push('true');
            if($.trim($(this).parent().parent().find('[id*="TSVALUE"]').val()) != "")
              {
                allblank5.push('true');
              }
            else
              {
                allblank5.push('false');
              } 
        }
        
    });

    $("[id*=txt_from_uom]").each(function(){
      if($.trim($(this).val())!=""){
          allblank6.push('true');

          if($.trim($("#maiuomref_id").val()) != $.trim($("#altuomref_id").val())){

            if($.trim($(this).parent().parent().find('[id*="hdntouom_popup"]').val()) != ""){
              allblank7.push('true');
            }
            else{
              allblank7.push('false');
            }

            if($.trim($(this).parent().parent().find('[id*="TXT_TO_QTY"]').val()) != ""){
              allblank8.push('true');
            }
            else{
              allblank8.push('false');
            }
            
      
          }
          else{
            allblank7.push('true');
            allblank8.push('true');
          }
      }
      else{
        allblank6.push('false');
      } 
    });

    $("[id*=txtudffie_popup]").each(function(){
        if($.trim($(this).val())!="")
        {
            if($.trim($(this).parent().parent().find('[id*="udffieismandatory"]').val()) == "1")
              {
                if($.trim($(this).parent().parent().find('[id*="udfvalue"]').val()) != "")
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

    
    if($.trim($("#SRNOA").val()) ==="1" && $.trim($("#SERIALNO_MODE").val()) ===""){
      $("#FocusId").val('SERIALNO_MODE');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      $("#alert").modal('show');
      $("#AlertMessage").text('Please Select Serial No Mode in Check Flag Tab.');
      return false;
    }
    else if($.trim($("#SRNOA").val()) ==="1" && $.trim($("#SERIALNO_MODE").val()) ==="AUTOMATIC" && $.trim($("#SERIALNO_PREFIX").val()) ===""){
      $("#FocusId").val('SERIALNO_PREFIX');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      $("#alert").modal('show');
      $("#AlertMessage").text('Please Enter Prefix in Check Flag Tab.');
      return false;
    } 
    else if($.trim($("#SRNOA").val()) ==="1" && $.trim($("#SERIALNO_MODE").val()) ==="AUTOMATIC" && $.trim($("#SERIALNO_STARTS_FROM").val()) ===""){
      $("#FocusId").val('SERIALNO_STARTS_FROM');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      $("#alert").modal('show');
      $("#AlertMessage").text('Please Enter Start From in Check Flag Tab.');
      return false;
    } 
    else if($.trim($("#SRNOA").val()) ==="1" && $.trim($("#SERIALNO_MODE").val()) ==="AUTOMATIC" && $.trim($("#SERIALNO_SUFFIX").val()) ===""){
      $("#FocusId").val('SERIALNO_SUFFIX');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      $("#alert").modal('show');
      $("#AlertMessage").text('Please Enter Suffix in Check Flag Tab.');
      return false;
    }  
    else if($.trim($("#SRNOA").val()) ==="1" && $.trim($("#SERIALNO_MODE").val()) ==="AUTOMATIC" && $.trim($("#SERIALNO_MAX_LENGTH").val()) ===""){
      $("#FocusId").val('SERIALNO_MAX_LENGTH');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      $("#alert").modal('show');
      $("#AlertMessage").text('Please Enter Max Length in Check Flag Tab.');
      return false;
    } 
    else if(jQuery.inArray("false", allblank4) !== -1){
      $("#alert").modal('show');
      $("#AlertMessage").text('Please enter type in Technical Specification Tab.');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk1');
    }
    else if(jQuery.inArray("false", allblank5) !== -1){
      $("#alert").modal('show');
      $("#AlertMessage").text('Please enter Value in Technical Specification Tab.');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk1');
    }
    else if(jQuery.inArray("false", allblank6) !== -1){
      $("#alert").modal('show');
      $("#AlertMessage").text('Please Select From UOM in UOM Conversion Tab.');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk1');
    }
    else if(jQuery.inArray("false", allblank7) !== -1){
      $("#alert").modal('show');
      $("#AlertMessage").text('Please Select To UOM  in UOM Conversion Tab.');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk1');
      return false;
    }
    else if(jQuery.inArray("false", allblank8) !== -1){
      $("#alert").modal('show');
      $("#AlertMessage").text('Please Select Qty  in UOM Conversion Tab.');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk1');
      return false;
    }
    else if(jQuery.inArray("false", allblank9) !== -1){
      $("#alert").modal('show');
      $("#AlertMessage").text('Please enter Value / Comments in UDF Tab.');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk1');
    }
    else{
      $("#alert").modal('show');
      $("#AlertMessage").text('Do you want to '+actionMsg+' to record.');
      $("#YesBtn").data("funcname",actionType);
      $("#OkBtn1").hide();
      $("#OkBtn").hide();
      $("#YesBtn").show();
      $("#NoBtn").show();
      $("#YesBtn").focus();
      highlighFocusBtn('activeYes');
    } 

  }

}

$("#btnSaveItem").click(function(){
  if(formItemMst.valid()){
    validateForm('fnSaveData','save');       
  }
});
    
$("#btnApprove").click(function(){
  if(formItemMst.valid()){
    validateForm('fnApproveData','approve');    
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

        var currentForm = $("#frm_mst_item");
        var formData = currentForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("master",[72,"update"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    console.log("error MSG="+data.msg);

                    if(data.resp=='duplicate') {

                      $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn").hide();
                      $("#OkBtn1").show();
                      $("#AlertMessage").text(data.msg);
                      $("#alert").modal('show');
                      $("#OkBtn1").focus();
                      highlighFocusBtn('activeOk1');
                      return false;

                    }

                   if(data.save=='invalid') {

                      $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn").hide();
                      $("#OkBtn1").show();
                      $("#AlertMessage").text(data.msg);
                      $("#alert").modal('show');
                      $("#OkBtn").focus();
                      return false;
                   }

                   if(data.form=='invalid') {

                      $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn").hide();
                      $("#OkBtn1").show();
                      $("#AlertMessage").text("Invalid form data please required fields.");
                      $("#alert").modal('show');
                      $("#OkBtn").focus();
                      return false;
                   }



                   
                }
                
                if(data.success) {                   
                    console.log("success MSG="+data.msg);
                    
                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn1").hide();
                    $("#OkBtn").show();
                    $("#OkBtn").focus();
                    highlighFocusBtn('activeOk1');

                    $("#AlertMessage").text(data.msg);
                    $("#alert").modal('show');

                    $(".text-danger").hide();

                  //  window.location.href='{{ route("master",[72,"index"])}}';

                  
                }
                if(data.errors) {                   
                    console.log("server validation MSG="+data.msg);
                    
                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn").hide();
                    $("#OkBtn1").show();
                    $("#OkBtn1").focus();
                    highlighFocusBtn('activeOk1');

                    $("#AlertMessage").text("Error: Please enter all required fields.");
                    $("#alert").modal('show');

                }


                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });
      
  } // fnSaveData

window.fnApproveData = function (){

//validate and save data
event.preventDefault();

var currentForm = $("#frm_mst_item");
var formData = currentForm.serialize();
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$.ajax({
    url:'{{route("master",[72,"singleapprove"])}}',
    type:'POST',
    data:formData,
    success:function(data) {
       
        if(data.errors) {
            $(".text-danger").hide();

            console.log("error MSG="+data.msg);

            if(data.resp=='duplicate') {

              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn").hide();
              $("#OkBtn1").show();
              $("#AlertMessage").text(data.msg);
              $("#alert").modal('show');
              $("#OkBtn1").focus();
              highlighFocusBtn('activeOk1');
              return false;

            }

           if(data.save=='invalid') {

              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn").hide();
              $("#OkBtn1").show();
              $("#AlertMessage").text(data.msg);
              $("#alert").modal('show');
              $("#OkBtn").focus();
              return false;
           }

           if(data.form=='invalid') {

              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn").hide();
              $("#OkBtn1").show();
              $("#AlertMessage").text("Invalid form data please required fields.");
              $("#alert").modal('show');
              $("#OkBtn").focus();
              return false;
           }



           
        }
        
        if(data.success) {                   
            console.log("success MSG="+data.msg);
            
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").hide();
            $("#OkBtn").show();
            $("#OkBtn").focus();
            highlighFocusBtn('activeOk1');

            $("#AlertMessage").text(data.msg);
            $("#alert").modal('show');

            $(".text-danger").hide();

          //  window.location.href='{{ route("master",[72,"index"])}}';

          
        }
        if(data.errors) {                   
            console.log("server validation MSG="+data.msg);
            
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn").hide();
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk1');

            $("#AlertMessage").text("Error: Please enter all required fields.");
            $("#alert").modal('show');

        }


        
    },
    error:function(data){
      console.log("Error: Something went wrong.");
    },
});

} // fnApproveata



    
    $("#NoBtn").click(function(){
    
      $("#alert").modal('hide');
      var custFnName = $("#NoBtn").data("funcname");
          window[custFnName]();
          event.preventDefault();
    }); //no button
   
    
    $("#OkBtn").click(function(){

        $("#alert").modal('hide');

        $("#YesBtn").show();  //reset
        $("#NoBtn").show();   //reset
        $("#OkBtn").hide();
        $("#OkBtn1").hide();

        $(".text-danger").hide();
       
        window.location.href='{{ route("master",[72,"index"])}}';
    }); ///ok button

    $("#OkBtn1").click(function(){

      $("#alert").modal('hide');

      $("#YesBtn").show();  //reset
      $("#NoBtn").show();   //reset
      $("#OkBtn1").hide();
      $("#OkBtn").hide();
      $(".text-danger").hide();
    
      }); ///ok button

    
    
    $("#btnUndo").click(function(){

        $("#AlertMessage").text("Do you want to erase entered information in this record?");
        $("#alert").modal('show');

        $("#YesBtn").data("funcname","fnUndoYes");
        $("#YesBtn").show();

        $("#NoBtn").data("funcname","fnUndoNo");
        $("#NoBtn").show();
        
        $("#OkBtn").hide();
        $("#OkBtn1").hide();
        $("#NoBtn").focus();
        highlighFocusBtn('activeNo');
        event.preventDefault();
    }); ////Undo button





   window.fnUndoYes = function (){
      
      //reload form
      window.location.reload();

   }//fnUndoYes


   window.fnUndoNo = function (){
     
   }//fnUndoNo


    function showError(pId,pVal){

      $("#"+pId+"").text(pVal);
      $("#"+pId+"").show();

    }//showError

    function highlighFocusBtn(pclass){
       $(".activeYes").hide();
       $(".activeNo").hide();
       
       $("."+pclass+"").show();
    }

  $(function() { 

    //ready
    

    $("[id*='TXT_TO_QTY']").ForceNumericOnly();

    //$("#PARTNO").ForceNumericOnly();
    //$("#DRAWINGNO").ForceNumericOnly();
    $("#STDCOST").ForceNumericOnly();
    $("#SCDRate").ForceNumericOnly();
    $("#SSRate").ForceNumericOnly();
    $("#MINLEVEL").ForceNumericOnly();
    $("#REORDERLEVEL").ForceNumericOnly();
    $("#MAXLEVEL").ForceNumericOnly();

    $('input[type=checkbox][name=DEACTIVATED]').change(function() {
        if ($(this).prop("checked")) {
          $(this).val('1');
          $('#DODEACTIVATED').removeAttr('disabled');
        }
        else {
          $(this).val('0');
          $('#DODEACTIVATED').prop('disabled', true);
          $('#DODEACTIVATED').val('dd/mm/yyyy');
          
        }
    });

    $('input').keypress(function(e) {
      if (e.which == '13') {
        e.preventDefault();        
      }
    });

     $("#ITEMGID_REF_old").on('change',function(){
        var customid =  $(this).val();
        if(customid!=''){
        //----------
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url:'{{route("master",[72,"getsubgroup"])}}',
                type:'POST',
                data:{'id':customid},
                success:function(data) {
                    if(data.exists) {
                        $(".text-danger").hide();
                        showError('ERROR_CTRYCODE',data.msg);
                        $("#CTRYCODE").focus();
                    }                                
                },
                error:function(data){
                  console.log("Error: Something went wrong.");
                },
            });        
        //---------
        }
      });
   
    $("#table1").on('click', '.add', function(event) {
      
       event.preventDefault();
       
        // if(event!=13){
        //   alert("--in ");
        // }else{

        //   alert("--else ");
        // }
    
        var $tr = $(this).closest('tbody');
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
        $clone.find('.remove').removeAttr('disabled'); 
        $clone.find('input:text').removeAttr('required'); 
        $tr.closest('table').append($clone);   
         var rowCount = $('#Row_Count1').val();
          rowCount = parseInt(rowCount)+1;
          $('#Row_Count1').val(rowCount);
          event.preventDefault();
    }); //add row

    $("#table1").on('click', '.remove', function() {
      var rowCount = $('#Row_Count1').val();
        if (rowCount > 1) {
          $(this).closest('.participantRow').remove();
        }
        if (rowCount <= 1) {
          $(document).find('.remove').prop('disabled', false);
        }
        event.preventDefault();
    });//remove row

    //--table1 end

    $("#table2").on('click', '.add', function(event) {
      event.preventDefault();
        var $tr = $(this).closest('table');
        var allTrs = $tr.find('tbody').last();
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
        $clone.find('.remove').removeAttr('disabled'); 
        $tr.closest('table').append($clone);   
        var rowCount = $('#Row_Count2').val();
          rowCount = parseInt(rowCount)+1;
          $('#Row_Count2').val(rowCount);

    }); //add row

    $("#table2").on('click', '.remove', function(event) {
          event.preventDefault();
          var rowCount = $(this).closest('table').find('tbody').length;
          if (rowCount > 1) {
            $(this).closest('tbody').remove();
          }
          if (rowCount <= 1) {
            $(document).find('.remove').prop('disabled', false);
          }
      });//remove row
      //--table2 end

    
    

     
    //UOM CONVERSION    
    $("#table3").on('click', '.add', function() {
    
        var $tr = $(this).closest('tbody');
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

       // $clone.find('input:text').val(''); txttouom_popup
       
       var toqty =   $.trim( $clone.find("[id*='TXT_TO_QTY']").val() );
       
        if(toqty==''){
          $("#alert").modal('show');
            $("#AlertMessage").text('Please enter Qty');
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk1');
            return false;
        }

          $clone.find("[id*='txttouom_popup']").val('');
          $clone.find("[id*='hdntouom_popup']").val('');
      

       // $("[id*='hdntxt_from_uomid']").val( $(this).val());
        $clone.find("[id*='TXT_TO_QTY']").val('');
        $clone.find('.remove').removeAttr('disabled'); 
        $clone.find('input:text').removeAttr('required'); 
        $tr.closest('table').append($clone);   
        var rowCount = $('#Row_Count3').val();
          rowCount = parseInt(rowCount)+1;
          $('#Row_Count3').val(rowCount);
          event.preventDefault();
    }); //add row

      $("#table3").on('click', '.remove', function() {
        var rowCount = $('#Row_Count3').val();
          if (rowCount > 1) {
            $(this).closest('.participantRow').remove();
          }
          if (rowCount <= 1) {
            $(document).find('.remove').prop('disabled', false);
          }
          event.preventDefault();
      });//remove row

      //to quantity chek with TO UOM
      $('#table3').on('blur','[id*="TXT_TO_QTY"]',function(){
        var tqty = $(this).val();
        var tuom = $(this).parent().parent().find('[id*="txttouom_popup"]').val();
        if (tqty != '' && tuom == '')
        {
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn").hide();
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk1');
          $("#AlertMessage").text('Please first select To UOM');
          $("#alert").modal('show');
          $(this).val('');
          $(".text-danger").hide();
        }
      });

      //--table3 end   //UOM CONVERSION 

      //udffield   
    $("#udffietable").on('click', '.add', function() {
    
      var $tr = $(this).closest('tbody');
        var allTrs = $tr.find('.participantRow').last();
        var lastTr = allTrs[allTrs.length-1];
        var $clone = $(lastTr).clone();
        //dynamic <td> id for dynamic input
        $clone.find('td').each(function(){
            var id = $(this).attr('id') || null;
            if(id) {
                var i = id.substr(id.length-1);
                var prefix = id.substr(0, (id.length-1));
                $(this).attr('id', prefix+(+i+1));
            }

        });  
        $clone.find("[id*='tdinputid']").html('');  //clear dynamic

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
      
        $clone.find("[id*='txtudffie_popup']").val('');
        $clone.find("[id*='hdnudffie_popup']").val('');
        $clone.find("[id*='udffieismandatory']").val('0');

        $clone.find('.remove').removeAttr('disabled'); 
        $tr.closest('table').append($clone);   
        var rowCount = $('#Row_Count4').val();
          rowCount = parseInt(rowCount)+1;
          $('#Row_Count4').val(rowCount);
          event.preventDefault();
    }); //add row

      $("#udffietable").on('click', '.remove', function() {
        var rowCount = $('#Row_Count4').val();
          if (rowCount > 1) {
            $(this).closest('.participantRow').remove();
          }
          if (rowCount <= 1) {
            $(document).find('.remove').prop('disabled', false);
          }
          event.preventDefault();
      });//remove row
      //--udffield end   /



      

    //---------------------------  
  }); //ready

</script>


  
  <script>
  $(document).ready(function() {
     // $('#example, #example2, #example3, #example4').DataTable();
    var obj = <?php echo json_encode($objItem); ?>;
    $('#IVM').val(obj.IVM);
    var invclass = <?php echo json_encode($ObjMstInventoryClass); ?>;
    $.each( invclass, function( key, value ) {
      var cid = value.CLASSID;
      var c_id = obj.CLASSID_REF;
      if (cid == c_id)
      {
        $('#invcls_popup').val(value.CLASS_CODE+' - '+value.CLASS_DESC);
      }
    
    });
    var mainuom = <?php echo json_encode($ObjMstUOM); ?>;
    $.each( mainuom, function(ckey, cvalue) {
      var cid = cvalue.UOMID;
      var c_id = obj.MAIN_UOMID_REF;
      var a_id = obj.ALT_UOMID_REF;
      if (cid == c_id)
      {
        $('#maiuomref_popup').val(cvalue.UOMCODE +' - '+ cvalue.DESCRIPTIONS);
      }
      if (cid == a_id)
      {
        $('#altuomref_popup').val(cvalue.UOMCODE +' - '+ cvalue.DESCRIPTIONS);
      }
    
    });
    var itmgrp = <?php echo json_encode($ObjMstItemGroup); ?>;
    $.each( itmgrp, function( ikey, ivalue ) {
      var cid = ivalue.ITEMGID;
      var c_id = obj.ITEMGID_REF;
      if (cid == c_id)
      {
        $('#itegrp_popup').val(ivalue.GROUPCODE+' - '+ivalue.GROUPNAME);
      }
    
    });
    var itmgrp = <?php echo json_encode($ObjMstItemGroup); ?>;
    $.each( itmgrp, function( ikey, ivalue ) {
      var cid = ivalue.ITEMGID;
      var c_id = obj.ITEMGID_REF;
      if (cid == c_id)
      {
        $('#itegrp_popup').val(ivalue.GROUPCODE+' - '+ivalue.GROUPNAME);
      }
    
    });
    var itmcat = <?php echo json_encode($ObjMstItemCategory); ?>;
    $.each( itmcat, function( ickey, icvalue ) {
      var cid = icvalue.ICID;
      var c_id = obj.ICID_REF;
      if (cid == c_id)
      {
        $('#itecat_popup').val(icvalue.ICCODE+' - '+icvalue.DESCRIPTIONS);
      }
    
    });
    var dftstr = <?php echo json_encode($ObjMstStore); ?>;
    $.each( dftstr, function( dkey, dvalue ) {
      var cid = dvalue.STID;
      var c_id = obj.STID_REF;
      if (cid == c_id)
      {
        $('#defsto_popup').val(dvalue.STCODE+' - '+dvalue.NAME);
      }
    
    });

    var hsncd = <?php echo json_encode($ObjMstHSN); ?>;
    $.each( hsncd, function( hkey, hvalue ) {
      var cid = hvalue.HSNID;
      var c_id = obj.HSNID_REF;
      if (cid == c_id)
      {
        $('#hsn_popup').val(hvalue.HSNCODE+' - '+hvalue.HSNDESCRIPTION);
      }
    
    });

    $("[id*='LOTSIZEQTY']").ForceNumericOnly();
        $('#LOTSIZEQTY').on('blur',function(){
            if(intRegex.test($(this).val())){
             $(this).val($(this).val()+'.000')
            }
            event.preventDefault();
        });

    var bsunt = <?php echo json_encode($ObjMstBusinessUnit); ?>;
    $.each( bsunt, function( bkey, bvalue ) {
      var cid = bvalue.BUID;
      var c_id = obj.BUID_REF;
      if (cid == c_id)
      {
        $('#busuni_popup').val(bvalue.BUCODE+' - '+bvalue.BUNAME);
      }
    
    });

    var attrid = <?php echo json_encode($objItemAttribute); ?>;
    var attrcd = <?php echo json_encode($ObjMstAttribute); ?>;
    $.each( attrid, function( atkey, atvalue ) {
      $.each( attrcd, function( tkey, tvalue ) {
        var cid = tvalue.ATTID;
        var c_id = atvalue.ATTID_REF;
        if (cid == c_id)
        {
          $('#txtattrcode_popup_'+atkey).val(tvalue.ATTCODE+' - '+tvalue.DESCRIPTIONS);
          $('#txtattrdesciption_'+atkey).val(tvalue.DESCRIPTIONS);
          // $('#hdnattrvalue_popup_'+atkey).val(atvalue.AVALUE);
          /////var attrid = $('#hdnattrcode_popup_'+atkey).val();
          var attrid = $('#hdnattrvalue_popup_'+atkey).val();
          $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url:'{{route("master",[72,"getAttValDatasingle"])}}',
                type:'POST',
                data:{'id':attrid},
                success:function(data) {
                    $('#txtattrvalue_popup_'+atkey).val(data);
                    // bindAttrValueEvents();
                },
                error:function(data){
                  console.log("Error: Something went wrong.");
                  $('#txtattrvalue_popup_'+atkey).val('');
                },
            });

        }
      });
    });

    var chkflag = <?php echo json_encode($objItemCheckFlag); ?>;
    $('#QCA').val(chkflag.QCA);
    $('#SRNOA').val(chkflag.SRNOA);
    $('#BATCHNOA').val(chkflag.BATCHNOA);
    $('#INVMANTAIN').val(chkflag.INVMANTAIN);
    $('#TCS').val(chkflag.TCS);

    var IUOM = <?php echo json_encode($objItemUOMConv); ?>;
    var uomv = <?php echo json_encode($ObjMstUOM); ?>;
    $.each( IUOM, function( uokey, uovalue ) {
      $.each( uomv, function( uvkey, uvvalue ) {
          var cid = uovalue.FROM_UOMID_REF;
          var c_id = uvvalue.UOMID;
          var to_id = uovalue.TO_UOMID_REF;
          if (cid == c_id)
          {
            $('#txt_from_uom_'+uokey).val(uvvalue.UOMCODE+' - '+uvvalue.DESCRIPTIONS);
          }
          if (to_id == c_id)
          {
            $('#txttouom_popup_'+uokey).val(uvvalue.UOMCODE+' - '+uvvalue.DESCRIPTIONS);
          }
      });
    });

    var itemudf = <?php echo json_encode($objItemUDF); ?>;
    //console.log(itemudf);
    var udfitem = <?php echo json_encode($objUdfForItems); ?>;
    // $.each(itemudf,function(udfk,udfvalue) {
     
    //     return false;
    //   var cid = udfvalue.UDFITEMID_REF;
    //   $.each(udfitem,function(uitemkey,uitemvalue) {
          
    //       var c_id = uitemvalue.UDFITEMID;
    //       if (cid == c_id)
    //       {
    //         $('#txtudffie_popup_'+udfk).val(uitemvalue.LABEL);
    //         var txtvaltype =   uitemvalue.VALUETYPE;
    //         var txt_id4 = $('[id*="tdinputid"]').attr('id');
    //         var strdyn = txt_id4.split('_');
    //         var lastele =   strdyn[strdyn.length-1];

    //         var dynamicid = "udfvalue_"+lastele;

    //         var chkvaltype =  txtvaltype.toLowerCase();
    //         var  storedvalue = udfvalue.UDF_VALUE;
            
    //         var strinp = '';

    //         if(chkvaltype=='date'){

    //           strinp = '<input type="date" placeholder="dd/mm/yyyy" name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" value="'+storedvalue+'" /> ';       

    //         }else if(chkvaltype=='time'){
    //           strinp= '<input type="time" placeholder="h:i" name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" value="'+storedvalue+'"  /> ';

    //         }else if(chkvaltype=='numeric'){
    //           strinp = '<input type="text" name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" value="'+storedvalue+'"  /> ';

    //         }else if(chkvaltype=='text'){

    //           strinp = '<input type="text" name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" value="'+storedvalue+'"  /> ';
            
    //         }else if(chkvaltype=='boolean'){
    //           var checkedval = '';
    //           if(storedvalue.toLowerCase()=="on"){
    //             checkedval='checked';
    //           }
    //           strinp = '<input type="checkbox" name="'+dynamicid+ '" id="'+dynamicid+'" class="" '+checkedval+'  /> ';
            
    //         }else if(chkvaltype=='combobox'){

    //           var txtoptscombo =   $("#txt"+fieldid+"").data("optscombo");
    //           var strarray = txtoptscombo.split(',');
    //           var opts = '';

    //           for (var i = 0; i < strarray.length; i++) {
    //             opts = opts + '<option value="'+strarray[i]+'">'+strarray[i]+'</option> ';
    //           }

    //           strinp = '<select name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" required>'+opts+'</select>' ;

    //         }

    //         // $('#'+txt_id4).html('');  
    //         $('#'+txt_id4).html(strinp);   //set dynamic input
    //         $('#'+txt_id3).val(uitemvalue.ISMANDATORY); // mandatory
           
    //       }
          
    //   });
      
    // });

  });
  </script>

<script>
$(document).ready(function() {
  var ItemType='<?php echo $objItem->ITEM_TYPE;?>';

  if(ItemType ==="S-Service"){
    $(".GLID_REF_DIV").show();
    $(".MATERIAL_TYPE_DIV").hide();
  }
  else if(ItemType ==="A-Assets"){
    $(".GLID_REF_DIV").hide();
    $(".MATERIAL_TYPE_DIV").hide();
  }
  else{
    $(".GLID_REF_DIV").hide();
    $(".MATERIAL_TYPE_DIV").show();
  }

});

function getItemType(ItemType){
  $("#MATERIAL_TYPE").val('');
  $("#GLID_REF_POPUP").val('');
  $("#GLID_REF").val('');

  if(ItemType ==="S-Service"){
    $(".GLID_REF_DIV").show();
    $(".MATERIAL_TYPE_DIV").hide();
  }else if(ItemType ==="A-Assets"){
    $(".GLID_REF_DIV").hide();
    $(".MATERIAL_TYPE_DIV").hide();
  }
  else{
    $(".GLID_REF_DIV").hide();
    $(".MATERIAL_TYPE_DIV").show();
  }
}

$("#GLID_REF_POPUP").on("click",function(event){ 
  $("#glrefpopup").show();
});

$("#GLID_REF_POPUP").keyup(function(event){
  if(event.keyCode==13){
    $("#glrefpopup").show();
  }
});

$("#glrefpopup_close").on("click",function(event){ 
  $("#glrefpopup").hide();
});

$('.clsglref').click(function(){
    var id = $(this).attr('id');
    var txtval =    $("#txt"+id+"").val();
    var texdesc =   $("#txt"+id+"").data("desc")

    $("#GLID_REF_POPUP").val(texdesc);
    $("#GLID_REF").val(txtval);
	
    $("#GLID_REF_POPUP").blur(); 
    $("#REGADDL1").focus(); 
    $("#glrefpopup").hide();

    $("#gl_codesearch").val(''); 
    $("#gl_namesearch").val(''); 
    searchGLCode();
    $(this).prop("checked",false);
    event.preventDefault();

});

function searchGLCode() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("gl_codesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("gl_tab2");
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

function searchGLName() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("gl_namesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("gl_tab2");
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

// Changes 23/03/2022
function isNumberKey(e,t){
    try {
        if (window.event) {
            var charCode = window.event.keyCode;
        }
        else if (e) {
            var charCode = e.which;
        }
        else { return true; }
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {         
        return false;
        }
         return true;

    }
    catch (err) {
        alert(err.Description);
    }
}

$("#SRNOA").change(function(){
  $("#serial_no_mode").hide();
  $("#SERIALNO_MODE").val('');
  $("#SERIALNO_PREFIX").val('');
  $("#SERIALNO_STARTS_FROM").val('');
  $("#SERIALNO_SUFFIX").val('');
  $("#SERIALNO_MAX_LENGTH").val('');

  if($(this).val() =='1'){
    $("#serial_no_mode").show();
  }  
});

$("#SERIALNO_MODE").change(function(){
  var compnaycheck='{{$check_company}}'; 
  $("#automatic_mode").hide();
  $("#SERIALNO_PREFIX").val('');
  $("#SERIALNO_STARTS_FROM").val('');
  $("#SERIALNO_SUFFIX").val('');
  $("#SERIALNO_MAX_LENGTH").val('');

  if($(this).val() =='AUTOMATIC'){
    $("#automatic_mode").show();
  }  
  if($(this).val() =='AUTOMATIC' && compnaycheck==''){
    $("#automatic_mode").show();
    $("#SERIALNO_SUFFIX").val($("#ICODE").val());
  }  
});


$("#ICODE").change(function(){
  var compnaycheck='{{$check_company}}';
  if(compnaycheck=='' && $('#SERIALNO_MODE').val()=='AUTOMATIC'){
    $("#automatic_mode").show();
    $("#SERIALNO_SUFFIX").val(this.value);
  }  
});

function getWarranty(data){
  $("#WARA_MONTH").val(0);
  $("#WARA_MONTH").prop('readonly',true);
  if(data ==='1'){
    $("#WARA_MONTH").prop('readonly',false);
  }
}
</script>

@endpush