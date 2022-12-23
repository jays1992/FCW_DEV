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
                        <button id="btnSaveItem"   class="btn topnavbt" tabindex="7"  disabled="disabled"> <i class="fa fa-save"></i> Save</button>
                        <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt" id="btnUndo"  disabled="disabled"><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove" {{($objRights->APPROVAL1||$objRights->APPROVAL2||$objRights->APPROVAL3||$objRights->APPROVAL4||$objRights->APPROVAL5) == 1 ? '' : 'disabled'}}><i class="fa fa-lock"></i> Approved</button>
                        <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                        <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                </div><!--col-10-->

            </div><!--row-->
    </div><!--topnav-->	`
   
    <div class="container-fluid filter">
      <form id="frm_mst_item" method="POST"  > 
        @CSRF  
        {{isset($objItem->ITEMID) ? method_field('PUT') : '' }}
      <div class="inner-form">
              <div class="row">
                <div class="col-lg-2 pl"><p>Item Code</p> </div>
                <div class="col-lg-2 pl">
                  <label>{{ $objItem->ICODE }}</label>
                  <span class="text-danger" id="ERROR_ICODE"></span> 
                </div>
                
                <div class="col-lg-1 pl"><p>Name</p></div>
                <div class="col-lg-3 pl" >
                <label>{{ $objItem->NAME }}</label>
                  <span class="text-danger" id="ERROR_NAME"></span> 
                </div>
                
                <div class="col-lg-1 pl"><p>Part No</p></div>
                <div class="col-lg-1 pl">
                <label>{{ $objItem->PARTNO }}</label>
                </div>
                
                <div class="col-lg-1 pl"><p>Drawing No</p></div>
                <div class="col-lg-1 pl">
                <label>{{ $objItem->DRAWINGNO }}</label>
                </div>
              </div>
              
              <div class="row">
                <div class="col-lg-2 pl"><p>Inventory Class</p></div>
                <div class="col-lg-2 pl" >
                  <input type="text" name="invcls_popup" id="invcls_popup" class="form-control mandatory" required readonly />
                  <input type="hidden" name="invcls_id" id="invcls_id" value="{{ $objItem->CLASSID_REF }}" />
                </div>
                
                <div class="col-lg-1 pl"><p>Main UoM</p></div>
                <div class="col-lg-2 pl" >
                  <input type="text" name="maiuomref_popup" id="maiuomref_popup" class="form-control mandatory" required readonly  />
                  <input type="hidden" name="maiuomref_id" id="maiuomref_id" value="{{ $objItem->MAIN_UOMID_REF }}" />
                </div>
                
                <div class="col-lg-1 pl col-md-offset-1"><p>ALT UOM</p></div>
                <div class="col-lg-2 pl" >
                  <input type="text" name="altuomref_popup" id="altuomref_popup" class="form-control mandatory" required  readonly />
                  <input type="hidden" name="altuomref_id" id="altuomref_id"  value="{{ $objItem->ALT_UOMID_REF }}" />
                </div>
              </div>

              <div class="row">
                <div class="col-lg-2 pl"><p>Item Type</p></div>
                <div class="col-lg-2 pl" >
                  <select disabled id="ITEM_TYPE" name="ITEM_TYPE" class="form-control mandatory" onChange="getItemType(this.value)" required >
                    <option value="">Select</option>
                    <option {{ isset($objItem->ITEM_TYPE) && $objItem->ITEM_TYPE =='A-Assets'?'selected="selected"':'' }} value="A-Assets">A-Assets</option>
                    <option {{ isset($objItem->ITEM_TYPE) && $objItem->ITEM_TYPE =='I-Inventory'?'selected="selected"':'' }} value="I-Inventory">I-Inventory</option>
                    <option {{ isset($objItem->ITEM_TYPE) && $objItem->ITEM_TYPE =='S-Service'?'selected="selected"':'' }} value="S-Service">S-Service</option>
                    <option {{ isset($objItem->ITEM_TYPE) && $objItem->ITEM_TYPE =='O-Other'?'selected="selected"':'' }} value="O-Other">O-Other</option>
                  </select>
                </div>
                
                <div class="col-lg-1 pl MATERIAL_TYPE_DIV" style="display:none;" ><p>Material Type</p></div>
                <div class="col-lg-2 pl MATERIAL_TYPE_DIV" style="display:none;" >
                  <select disabled id="MATERIAL_TYPE" name="MATERIAL_TYPE" class="form-control mandatory" required>
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
                <input disabled type="text" name="GLID_REF_POPUP" id="GLID_REF_POPUP" class="form-control mandatory" value="{{ isset($objOldGlList->GLCODE) && $objOldGlList->GLCODE !=''?$objOldGlList->GLCODE.' - '.$objOldGlList->GLNAME:'' }}" required readonly  />
                        <input type="hidden" name="GLID_REF" id="GLID_REF" value="{{ $objItem->GLID_REF }}" />
                        <span class="text-danger" id="ERROR_GLID_REF"></span>
                </div>

                <div class="col-lg-1 pl"><p>Item Description</p></div>
                <div class="col-lg-2 pl">
                  <label>{{ $objItem->ITEM_DESC }}</label>
                </div>
                
              </div>

              
              <div class="row">
                
                <div class="col-lg-2 pl"><p>Item Category</p></div>
                <div class="col-lg-2 pl" >
                  <input type="text" name="itecat_popup" id="itecat_popup" class="form-control mandatory" required  readonly />
                  <input type="hidden" name="itecat_id" id="itecat_id" value="{{ $objItem->ICID_REF }}" />
                </div>
                
                <div class="col-lg-1 pl "><p>Item Group</p></div>
                <div class="col-lg-2 pl" >
                  <input type="text" name="itegrp_popup" id="itegrp_popup" class="form-control mandatory" required  readonly />
                  <input type="hidden" name="itegrp_id" id="itegrp_id" value="{{ $objItem->ITEMGID_REF }}" />
                </div>
                
                <div class="col-lg-1 pl"><p>Sub Group</p></div>
                <div class="col-lg-2 pl">
                  <input type="text" name="itesubgrp_popup" id="itesubgrp_popup" value="{{$Objsubgroup->ISGCODE}} - {{$Objsubgroup->DESCRIPTIONS}}" class="form-control mandatory" required  readonly />
                  <input type="hidden" name="itesubgrp_id" id="itesubgrp_id" value="{{ $objItem->ISGID_REF }}" />
                </div>
                
                
                
              </div>
              
              <div class="row">
                
                
                <div class="col-lg-1 pl"><p>Default Store</p></div>
                <div class="col-lg-2 pl" >
                  <input type="text" name="defsto_popup" id="defsto_popup" class="form-control mandatory" required readonly />
                  <input type="hidden" name="defsto_id" id="defsto_id" value="{{ $objItem->STID_REF }}" />
                </div>
              
                <div class="col-lg-1 pl"><p>HSN Code</p></div>
                <div class="col-lg-1 pl">
                  <input type="text" name="hsn_popup" id="hsn_popup" class="form-control mandatory" required  readonly />
                  <input type="hidden" name="hsn_id" id="hsn_id" value="{{ $objItem->HSNID_REF }}" />
                </div>
                
                <div class="col-lg-2 pl"><p>Standard Custom Duty Rate %</p></div>
                <div class="col-lg-1 pl">
                  <label>{{ $objItem->CUSTOM_DUTY_RATE }}</label>
                </div>
                
                </div>
              
              
              <div class="row">
                

                <div class="col-lg-2 pl"><p>Inventory Valuation Method</p></div>
                  <div class="col-lg-2 pl">
                    <label name="IVM" id="IVM" class="form-control" >{{ $objItem->IVM }}</label>
                    
                  </div>
                
                
              
                <div class="col-lg-1 pl"><p>Business Unit</p></div>
                <div class="col-lg-2 pl">
                  <input type="text" name="busuni_popup" id="busuni_popup" class="form-control mandatory" required readonly />
                  <input type="hidden" name="busuni_id" id="busuni_id" value="{{ $objItem->BUID_REF }}" />
                </div>
                
                
                <div class="col-lg-1 pl"><p>Standard Rate</p></div>
                <div class="col-lg-1 pl">
                  
                  <label>{{ $objItem->STDCOST }}</label>
                  
                </div>
                
                <div class="col-lg-2 pl"><p>Standard SWS Rate %</p></div>
                <div class="col-lg-1 pl">
                  <label>{{ $objItem->STD_SWS_RATE }}</label>
                </div>

              
                
              </div>
              
              
              <div class="row">               
                        
                <div class="col-lg-2 pl"><p>Minimum Level</p></div>
                <div class="col-lg-1 pl">
                  <div class="col-lg-12 pl">
                  <label>{{ $objItem->MINLEVEL }}</label>
                  </div>
                </div>
                
                <div class="col-lg-1 pl"><p>Reorder Level</p></div>
                <div class="col-lg-1 pl">
                  <div class="col-lg-12 pl">
                  <label>{{ $objItem->REORDERLEVEL }}</label>
                  </div>
                </div>
                
                <div class="col-lg-1 pl"><p>Maximum Level</p></div>
                <div class="col-lg-1 pl">
                  <label>{{ $objItem->MAXLEVEL }}</label>
                </div>

                <div class="col-lg-1 pl"><p>Lead (Days)</p></div>
                <div class="col-lg-1 pl">
                  <label>{{ $objItem->LEAD_DAYS }}</label>
                </div>

                <div class="col-lg-1 pl"><p>Shelf Life (Month)</p></div>
                <div class="col-lg-1 pl">
                  <label>{{ $objItem->SHELF_LIFE }}</label>
                </div>
                
              </div>

              <div class="row">
                <div class="col-lg-2 pl"><p>Item Specification</p></div>
                <div class="col-lg-3 pl">
                  <label>{{ $objItem->ITEM_SPECI }}</label>
                </div>
              </div>

              <div class="row">
                <div class="col-lg-2 pl"><p>De-Activated</p></div>
                <div class="col-lg-1 pl pr">
                <input type="checkbox"   name="DEACTIVATED"  id="deactive-checkbox_0" {{$objItem->DEACTIVATED == 1 ? "checked" : ""}}
                 value='{{$objItem->DEACTIVATED == 1 ? 1 : 0}}' tabindex="7" disabled >
                </div>
                
                <div class="col-lg-2 pl"><p>Date of De-Activated</p></div>
                <div class="col-lg-2 pl">
                <div class="col-lg-8 pl">
                  <label>{{ (!is_null($objItem->DODEACTIVATED) && $objItem->DODEACTIVATED!='1900-01-01')? 
                    \Carbon\Carbon::parse($objItem->DODEACTIVATED)->format('Y-m-d') : ''   }}</label>
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
                            {{-- <th width="15%">Action</th> --}}
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
                                <input type="text" name={{"attrvalue_".$key}} id={{"hdnattrvalue_popup_".$key}} value ="{{$row->AVALUE}}" class="form-control" readonly/>
                              </td> 

                            {{-- <td align="center" ><button class="btn add" title="add" data-toggle="tooltip" disabled><i class="fa fa-plus"></i></button><button class="btn remove" title="Delete" data-toggle="tooltip" type="button" disabled ><i class="fa fa-trash" disabled></i></button></td> --}}
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
                          <label name="QCA" id="QCA" class="form-control mandatory">
                             {{ ($objItemCheckFlag->QCA==1)?'Yes':'No' }}
                          </label>
                        </div>

                        <div class="col-lg-2 pl"><p>Incentive Applicable</p></div>
                        <div class="col-lg-1 pl">
                          <label name="QCA" id="QCA" class="form-control mandatory">
                             {{ ($objItemCheckFlag->INCA==1)?'Yes':'No' }}
                          </label>
                        </div>
                      </div>
                      
                      <div class="row">
                        <div class="col-lg-2 pl"><p>Serial No Applicable</p></div>
                        <div class="col-lg-1 pl">
                          <label name="SRNOA" id="SRNOA" class="form-control mandatory"  >
                            {{ ($objItemCheckFlag->SRNOA==1)?'Yes':'No' }}
                          </label>
                        </div>

                        <div class="col-lg-2 pl"><p>BIN Required</p></div>
                        <div class="col-lg-1 pl">
                          <label name="QCA" id="QCA" class="form-control mandatory">
                             {{ ($objItemCheckFlag->BIN==1)?'Yes':'No' }}
                          </label>
                        </div>

                      </div>
                      <div class="row">
                        <div class="col-lg-2 pl"><p>Batch No / Lot No Applicable</p></div>
                        <div class="col-lg-1 pl">
                          <label name="BATCHNOA" id="BATCHNOA" class="form-control mandatory" >{{ ($objItemCheckFlag->BATCHNOA==1)?'Yes':'No' }}</label>
                        </div>

                        <div class="col-lg-2 pl"><p>Warranty Applicable</p></div>
                        <div class="col-lg-1 pl">
                          <label name="QCA" id="QCA" class="form-control mandatory">
                             {{ ($objItemCheckFlag->WARA==1)?'Yes':'No' }}
                          </label>
                        </div>

                      </div>
                      
                      <div class="row">
                        <div class="col-lg-2 pl"><p>Inventory Maintain</p></div>
                        <div class="col-lg-1 pl">
                          <label name="INVMANTAIN" id="INVMANTAIN" class="form-control mandatory"  >{{ ($objItemCheckFlag->INVMANTAIN==1)?'Yes':'No' }}</label>
                        </div>

                        <div class="col-lg-2 pl"><p>Warranty(Month)</p></div>
                        <div class="col-lg-1 pl">
                          <label name="WARA_MONTH" id="WARA_MONTH" class="form-control mandatory"  >{{isset($objItemCheckFlag->WARA_MONTH)?$objItemCheckFlag->WARA_MONTH:0}}</label>
                        </div>

                      </div>

                      <div class="row">
                        <div class="col-lg-2 pl"><p>Bar Code Applicable</p></div>
                        <div class="col-lg-1 pl">
                          <select disabled name="BARCODE_APPLICABLE" id="BARCODE_APPLICABLE" class="form-control mandatory"  >
                            <option {{isset($objItemCheckFlag->BARCODE_APPLICABLE) && $objItemCheckFlag->BARCODE_APPLICABLE=='1'?'selected="selected"':''}}  value="1">Yes</option>
                            <option {{isset($objItemCheckFlag->BARCODE_APPLICABLE) && $objItemCheckFlag->BARCODE_APPLICABLE=='0'?'selected="selected"':''}}  value="0" >No</option>
                          </select>
                        </div>
                        <div class="col-lg-2 pl"><p>Expiry Applicable</p></div>
                        <div class="col-lg-1 pl">
                          <label name="EXPIRY_APPLICABLE" id="EXPIRY_APPLICABLE" class="form-control mandatory"  >{{ ($objItemCheckFlag->EXPIRY_APPLICABLE==1)?'Yes':'No' }}</label>
                        </div>
                      </div>

                      <div class="row" id="serial_no_mode" style="{{isset($objItemCheckFlag->SRNOA) && $objItemCheckFlag->SRNOA =='1'?'':'display:none;'}}"  >
                        <div class="col-lg-2 pl"><p>Serial No Mode</p></div>
                        <div class="col-lg-1 pl">
                          <select disabled name="SERIALNO_MODE" id="SERIALNO_MODE" class="form-control mandatory"  >
                            <option  value="">Select</option>
                            <option {{isset($objItemCheckFlag->SERIALNO_MODE) && $objItemCheckFlag->SERIALNO_MODE=='MANUAL'?'selected="selected"':''}}  value="MANUAL">MANUAL</option>
                            <option {{isset($objItemCheckFlag->SERIALNO_MODE) && $objItemCheckFlag->SERIALNO_MODE=='AUTOMATIC'?'selected="selected"':''}}  value="AUTOMATIC" >AUTOMATIC</option>
                          </select>
                        </div>
                        <div id="automatic_mode" style="{{isset($objItemCheckFlag->SERIALNO_MODE) && $objItemCheckFlag->SERIALNO_MODE =='AUTOMATIC'?'':'display:none;'}}" >
                          <div class="col-lg-1 pl"><p>Prefix</p></div>
                          <div class="col-lg-1 pl">
                            <input disabled type="text" id="SERIALNO_PREFIX" name="SERIALNO_PREFIX" value="{{isset($objItemCheckFlag->SERIALNO_PREFIX)?$objItemCheckFlag->SERIALNO_PREFIX:''}}" class="form-control mandatory" autocomplete="off" >
                          </div>

                          <div class="col-lg-1 pl"><p>Start From</p></div>
                          <div class="col-lg-1 pl">
                            <input disabled type="text" id="SERIALNO_STARTS_FROM" name="SERIALNO_STARTS_FROM" value="{{isset($objItemCheckFlag->SERIALNO_STARTS_FROM)?$objItemCheckFlag->SERIALNO_STARTS_FROM:''}}" class="form-control mandatory" autocomplete="off" onkeypress="return isNumberKey(event,this)" >
                          </div>

                          <div class="col-lg-1 pl"><p>Suffix</p></div>
                          <div class="col-lg-1 pl">
                            <input disabled type="text" id="SERIALNO_SUFFIX" name="SERIALNO_SUFFIX" value="{{isset($objItemCheckFlag->SERIALNO_SUFFIX)?$objItemCheckFlag->SERIALNO_SUFFIX:''}}" class="form-control mandatory" autocomplete="off" >
                          </div>

                          <div class="col-lg-1 pl"><p>Max Length</p></div>
                          <div class="col-lg-1 pl">
                            <input disabled type="text" id="SERIALNO_MAX_LENGTH" name="SERIALNO_MAX_LENGTH" value="{{isset($objItemCheckFlag->SERIALNO_MAX_LENGTH)?$objItemCheckFlag->SERIALNO_MAX_LENGTH:''}}" class="form-control mandatory" autocomplete="off" onkeypress="return isNumberKey(event,this)" >
                          </div>
                        </div>

                      </div>
                      
                      <!--
                      <div class="row">
                        <div class="col-lg-2 pl"><p>TCS Applicable</p></div>
                        <div class="col-lg-1 pl">
                          <label name="TCS" id="TCS" class="form-control mandatory"  >{{ ($objItemCheckFlag->TCS==1)?'Yes':'No' }}</label>
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
                            </tr>
                        </thead>
                        <tbody>
                        @if(!empty($objItemTechSpecification))
                          @foreach($objItemTechSpecification as $tskey => $tsrow)
                            <tr  class="participantRow">
                            <td><label>{{$tsrow->TSTYPE}}</label>
                            <td><label>{{$tsrow->VALUE}}</label>
                            {{-- <td align="center" ><button class="btn add" title="add" data-toggle="tooltip" disabled><i class="fa fa-plus"></i></button><button class="btn remove" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td> --}}
                            </tr>
                          <tr></tr>
                          @endforeach 
                          @else
                          <tr  class="">
                            <td><input  class="form-control w-100" type="text" name="" id="" maxlength="50" disabled></td>
                            <td><input  class="form-control w-100" type="text" name="" id="" maxlength="100" disabled></td>
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
                            {{-- <th width="15%">Action</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                        @if(!empty($objItemUOMConvList))
                          @foreach($objItemUOMConvList as $iokey => $iorow)
                            <tr  class="participantRow">
                            <td>
                              <input  class="form-control w-100" type="text" name="" value="{{$iorow["from_label"]}}"  readonly />
                            </td>
                            <td>
                              <input  class="form-control w-100" type="text" name="" value="{{$iorow["from_qty"]}}"  readonly />
                            </td>
                            <td>
                              <input  class="form-control w-100" type="text" name="" value="{{$iorow["to_label"]}}"  readonly />
                            </td>
                            <td>
                              <input  class="form-control w-100" type="text" name="" value="{{$iorow["to_qty"]}}"  readonly />
                            </td>
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
                        <input type="text" disabled name="SAP_CUSTOMER_CODE" id="SAP_CUSTOMER_CODE" value="{{ $objItem->SAP_CUSTOMER_CODE }}" class="form-control" style="text-transform:uppercase" readonly>
                        </div>
                        <div class="col-lg-2 pl"><p>{{isset($TabSetting->FIELD2) && $TabSetting->FIELD2 !=''?$TabSetting->FIELD2:'Add. Customer Name'}}</p></div>
                        <div class="col-lg-2 pl">
                        <input type="text" disabled name="SAP_CUSTOMER_NAME" id="SAP_CUSTOMER_NAME" value="{{ $objItem->SAP_CUSTOMER_NAME }}" class="form-control" readonly>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-lg-2 pl"><p>{{isset($TabSetting->FIELD3) && $TabSetting->FIELD3 !=''?$TabSetting->FIELD3:'Add. Part Number'}}</p></div>
                        <div class="col-lg-2 pl">
                        <input type="text" disabled name="SAP_PART_NO" id="SAP_PART_NO" value="{{ $objItem->SAP_PART_NO }}" class="form-control" style="text-transform:uppercase" readonly>
                        </div>
                        <div class="col-lg-2 pl"><p>{{isset($TabSetting->FIELD4) && $TabSetting->FIELD4 !=''?$TabSetting->FIELD4:'Add. Part Description'}}</p></div>
                        <div class="col-lg-2 pl">
                        <input type="text" disabled name="SAP_PART_DESC" id="SAP_PART_DESC" value="{{ $objItem->SAP_PART_DESC }}" class="form-control" readonly>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-lg-2 pl"><p>{{isset($TabSetting->FIELD5) && $TabSetting->FIELD5 !=''?$TabSetting->FIELD5:'Add. Customer Part No'}}</p></div>
                        <div class="col-lg-2 pl">
                        <input type="text" disabled name="SAP_CUST_PARTNO" id="SAP_CUST_PARTNO" value="{{ $objItem->SAP_CUSTOMER_PARTNO }}" class="form-control" style="text-transform:uppercase" readonly>
                        </div>
                        <div class="col-lg-2 pl"><p>{{isset($TabSetting->FIELD6) && $TabSetting->FIELD6 !=''?$TabSetting->FIELD6:'Add. Market & Set Code'}}</p></div>
                        <div class="col-lg-2 pl">
                        <input type="text" disabled name="SAP_MARTKET_SETCODE" id="SAP_MARTKET_SETCODE" value="{{ $objItem->SAP_MARKET_SETCODE }}" class="form-control" style="text-transform:uppercase" readonly>
                        </div>
                      </div>
                      
 
                      
                      <div class="row">
                        <div class="col-lg-2 pl"><p>{{isset($TabSetting->FIELD7) && $TabSetting->FIELD7 !=''?$TabSetting->FIELD7:'Rounding Value/LOT Size Qty'}}</p></div>
                        <div class="col-lg-2 pl">
                        <input type="text" disabled name="LOTSIZEQTY" id="LOTSIZEQTY" value="{{ $objItem->ROUNDING_VALUE }}" class="form-control" readonly>
                        </div>
                        <div class="col-lg-2 pl"><p>{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</p></div>
                        <div class="col-lg-2 pl">
                        <input type="text" name="ALPS_PART_NO" id="ALPS_PART_NO" value="{{ $objItem->ALPS_PART_NO }}" class="form-control" readonly>
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-lg-2 pl"><p>{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</p></div>
                        <div class="col-lg-2 pl">
                        <input type="text" name="CUSTOMER_PART_NO" id="CUSTOMER_PART_NO" value="{{ $objItem->CUSTOMER_PART_NO }}" class="form-control" readonly>
                        </div>
                        <div class="col-lg-2 pl"><p>{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</p></div>
                        <div class="col-lg-2 pl">
                        <input type="text" name="OEM_PART_NO" id="OEM_PART_NO" value="{{ $objItem->OEM_PART_NO }}" class="form-control" readonly>
                        </div>
                      </div>
     
                      
                    </div>
                  </div>
                  
                  <div id="udf" class="tab-pane fade">
              <div class="table-responsive table-wrapper-scroll-y " style="margin-top:10px;height:350px;width:50%;">
                <table id="udffietable" class="display nowrap table table-striped table-bordered itemlist" style="height:auto !important;">
                  <thead id="thead1"  style="position: sticky;top: 0">
                    <tr >
                    <th>UDF Fields <input class="form-control" type="hidden" name="Row_Count4" id ="Row_Count4"> </th>
                    <th>Value / Comments</th>
                    </tr>
                  </thead>
                  <tbody>
                      @foreach($objItemUDF as $udfkey => $udfrow)
                        <tr  class="participantRow">
                          <td>
                            <input name={{"udffie_popup_".$udfkey}} id={{"txtudffie_popup_".$udfkey}} value="{{$udfrow->LABEL}}" class="form-control @if ($udfrow->ISMANDATORY==1) mandatory @endif" autocomplete="off" maxlength="100" disabled />
                          </td>
                          <td>
                            @php
                              
                              $dynamicid = "udfvalue_".$udfkey;
                              $chkvaltype = strtolower($udfrow->VALUETYPE); 

                            if($chkvaltype=='date'){

                              $strinp = '<input type="date" placeholder="dd/mm/yyyy" name="'.$dynamicid.'" id="'.$dynamicid.'" class="form-control" value="'.$udfrow->UDF_VALUE.'" disabled /> ';       

                            }else if($chkvaltype=='time'){

                                $strinp= '<input type="time" placeholder="h:i" name="'.$dynamicid.'" id="'.$dynamicid.'" class="form-control"  value="'.$udfrow->UDF_VALUE.'" disabled /> ';

                            }else if($chkvaltype=='numeric'){
                            $strinp = '<input type="text" name="'.$dynamicid. '" id="'.$dynamicid.'" class="form-control" value="'.$udfrow->UDF_VALUE.'"  disabled /> ';

                            }else if($chkvaltype=='text'){

                            $strinp = '<input type="text" name="'.$dynamicid. '" id="'.$dynamicid.'" class="form-control" value="'.$udfrow->UDF_VALUE.'"  disabled /> ';

                            }else if($chkvaltype=='boolean'){
                                $boolval = ''; 
                                if($udfrow->UDF_VALUE=='on' || $udfrow->UDF_VALUE=='1' ){
                                  $boolval="checked";
                                }
                                $strinp = '<input type="checkbox" name="'.$dynamicid. '" id="'.$dynamicid.'" class=""  '.$boolval.'  readonly /> ';

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

                              $strinp = '<select name="'.$dynamicid.'" id="'.$dynamicid.'" class="form-control"  disabled >'.$opts.'</select>' ;


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


@push('bottom-css')
<style>
 input[type="number"] {
    text-align: right !important;
    
}

/* .select2-container--default .select2-results__group{
   color: #0f69cc;
} */

/* .searchalldata input {
  border: 1px solid #ddd;
  border-radius: 3px;
padding:2px 10px;
}
    .sortlist table.table-bordered.dataTable  .searchalldata td:after{top:10px !important;} */

</style>
@endpush

@push('bottom-scripts')





  
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
         //var attrid = $('#hdnattrcode_popup_'+atkey).val();
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
  }
  else if(ItemType ==="A-Assets"){
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

$('.clsglref').dblclick(function(){
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
    event.preventDefault();

});

function searchGLCode() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("gl_codesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("gl_tab2");
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

function searchGLName() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("gl_namesearch");
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