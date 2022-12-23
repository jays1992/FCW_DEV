@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                  <a href="{{route('master',[167,'index'])}}" class="btn singlebt">Purchase Account Set</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <button href="#" id="btnSelectedRows" class="btn topnavbt" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
                        <button   class="btn topnavbt" tabindex="4" disabled="disabled"><i class="fa fa-save"></i> Save</button>
                        <button class="btn topnavbt" id="btnView"  disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt"  id="btnUndo" disabled="disabled" ><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled" ><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-lock"></i> Approved</button>
                        <a href="#" class="btn topnavbt"  disabled="disabled"><i class="fa fa-link" ></i> Attachment</a>
                        <button class="btn topnavbt" id="btnExit"><i class="fa fa-power-off"></i> Exit</button>
                </div><!--col-10-->

            </div><!--row-->
    </div><!--topnav-->	
   
    <div class="container-fluid purchase-order-view filter">     
         <form id="frm_mst_edit" method="POST"  > 
          @CSRF
          {{isset($objResponse->PR_AC_SETID) ? method_field('PUT') : '' }}
          <div class="inner-form">
          
                            
             <div class="row">
                  <div class="col-lg-2 pl"><p>Account set code</p></div>
                  <div class="col-lg-2 pl">
                    <div class="col-lg-7 pl">
                    <label> {{$objResponse->AC_SET_CODE}} </label>
                    <input type="hidden" name="PR_AC_SETID" id="PR_AC_SETID" value="{{ $objResponse->PR_AC_SETID }}" />
                    <input type="hidden" name="AC_SET_CODE" id="AC_SET_CODE" value="{{ $objResponse->AC_SET_CODE }}" autocomplete="off"     />
                    <input type="hidden" name="user_approval_level" id="user_approval_level" value="{{ $user_approval_level }}"  />                
                        <span class="text-danger" id="ERROR_AC_SET_CODE"></span> 
                    </div>
                  </div>

                  <div class="col-lg-2 pl"><p>Account set code Description</p></div>
                  <div class="col-lg-3 pl">
                    <input type="text" name="AC_SET_DESC" id="AC_SET_DESC" class="form-control mandatory" value="{{ old('AC_SET_DESC',$objResponse->AC_SET_DESC) }}" maxlength="50" tabindex="4"   disabled />
                    <span class="text-danger" id="ERROR_AC_SET_DESC"></span> 
                  </div>
              </div>  
              
              <div class="row">
                <div class="col-lg-2 pl"><p>Purchase Account</p></div>
                <div class="col-lg-2 pl">
                  <div class="col-lg-10 pl">
                    <input type="text" name="txtLISTPOP1_popup_0" id="txtLISTPOP1_popup_0" disabled value="{{ isset($objPURCHASE_AC->GLCODE) && !is_null($objPURCHASE_AC->GLCODE) ? $objPURCHASE_AC->GLCODE : ''}}" class="form-control mandatory"  autocomplete="off"  readonly  required/>
                          <input type="hidden" name="LISTPOP1ID_0" id="hdnLISTPOP1ID_0" value="{{ isset($objPURCHASE_AC->GLID) && !is_null($objPURCHASE_AC->GLID)? $objPURCHASE_AC->GLID : ''}}"  class="form-control" autocomplete="off" />
                  </div>
                </div>
                <div class="col-lg-2 pl"><p>Description</p></div>
                <div class="col-lg-3 pl">
                  <input  class="form-control" style="width: 100%" type="text" name="DESC2_0" id ="DESC2_0" disabled  value="{{ isset($objPURCHASE_AC->GLNAME)  && !is_null($objPURCHASE_AC->GLNAME) ? $objPURCHASE_AC->GLNAME : ''}}"  autocomplete="off" readonly>
                </div>
            </div>

            <div class="row">
              <div class="col-lg-2 pl"><p>Payable Clearing</p></div>
              <div class="col-lg-2 pl">
                <div class="col-lg-10 pl">
                  <input type="text" name="txtLISTPOP1_popup_1" id="txtLISTPOP1_popup_1" disabled value="{{ isset($objPAYABLE_CLEARING->GLCODE) && !is_null($objPAYABLE_CLEARING->GLCODE) ? $objPAYABLE_CLEARING->GLCODE : ''}}" class="form-control "  autocomplete="off"  readonly  />
                  <input type="hidden" name="LISTPOP1ID_1" id="hdnLISTPOP1ID_1" class="form-control" autocomplete="off"  value="{{ isset($objPAYABLE_CLEARING->GLID) && !is_null($objPAYABLE_CLEARING->GLID)? $objPAYABLE_CLEARING->GLID : ''}}"  />
                </div>
              </div>
              <div class="col-lg-2 pl"><p>Description</p></div>
              <div class="col-lg-3 pl">
                  <input  class="form-control" style="width: 100%" type="text" name="DESC2_1" id ="DESC2_1"   autocomplete="off" readonly value="{{ isset($objPAYABLE_CLEARING->GLNAME)  && !is_null($objPAYABLE_CLEARING->GLNAME) ? $objPAYABLE_CLEARING->GLNAME : ''}}" >
              </div>
            </div>

            <div class="row">
              <div class="col-lg-2 pl"><p>Inventory Account</p></div>
              <div class="col-lg-2 pl">
                <div class="col-lg-10 pl">
                  <input type="text" name="txtLISTPOP1_popup_2" id="txtLISTPOP1_popup_2" disabled class="form-control mandatory"  autocomplete="off"  readonly  required  value="{{ isset($objINVENTORY_AC->GLCODE) && !is_null($objINVENTORY_AC->GLCODE) ? $objINVENTORY_AC->GLCODE : ''}}" />

                  <input type="hidden" name="LISTPOP1ID_2" id="hdnLISTPOP1ID_2" class="form-control" autocomplete="off" value="{{ isset($objINVENTORY_AC->GLID) && !is_null($objINVENTORY_AC->GLID)? $objINVENTORY_AC->GLID : ''}}" />
                </div>
              </div>
              <div class="col-lg-2 pl"><p>Description</p></div>
              <div class="col-lg-3 pl">
                  <input  class="form-control" style="width: 100%" type="text" name="DESC2_2" id ="DESC2_2"  autocomplete="off" readonly  value="{{ isset($objINVENTORY_AC->GLNAME)  && !is_null($objINVENTORY_AC->GLNAME) ? $objINVENTORY_AC->GLNAME : ''}}" >
              </div>
            </div>

            <div class="row">
              <div class="col-lg-2 pl"><p>Landed Cost Variance</p></div>
              <div class="col-lg-2 pl">
                <div class="col-lg-10 pl">
                  <input type="text" name="txtLISTPOP1_popup_3" id="txtLISTPOP1_popup_3" disabled  class="form-control"  autocomplete="off"  readonly  value="{{ isset($objADJUSTMENT_WRITEOFF->GLCODE) && !is_null($objADJUSTMENT_WRITEOFF->GLCODE) ? $objADJUSTMENT_WRITEOFF->GLCODE : ''}}"  />

                  <input type="hidden" name="LISTPOP1ID_3" id="hdnLISTPOP1ID_3" class="form-control" autocomplete="off" value="{{ isset($objADJUSTMENT_WRITEOFF->GLID) && !is_null($objADJUSTMENT_WRITEOFF->GLID)? $objADJUSTMENT_WRITEOFF->GLID : ''}}"/>
                </div>
              </div>
              <div class="col-lg-2 pl"><p>Description</p></div>
              <div class="col-lg-3 pl">
                  <input  class="form-control" style="width: 100%" type="text" name="DESC2_3" id ="DESC2_3"  autocomplete="off" readonly  value="{{ isset($objADJUSTMENT_WRITEOFF->GLNAME)  && !is_null($objADJUSTMENT_WRITEOFF->GLNAME) ? $objADJUSTMENT_WRITEOFF->GLNAME : ''}}" >
              </div>
            </div>

            <div class="row">
              <div class="col-lg-2 pl"><p>Transfer Clearing</p></div>
              <div class="col-lg-2 pl">
                <div class="col-lg-10 pl">
                  <input type="text" name="txtLISTPOP1_popup_4" id="txtLISTPOP1_popup_4" disabled  class="form-control"  autocomplete="off"  readonly value="{{ isset($objTRANSFER_CLEARING->GLCODE) && !is_null($objTRANSFER_CLEARING->GLCODE) ? $objTRANSFER_CLEARING->GLCODE : ''}}"  />

                  <input type="hidden" name="LISTPOP1ID_4" id="hdnLISTPOP1ID_4" class="form-control" autocomplete="off"  value="{{ isset($objTRANSFER_CLEARING->GLID) && !is_null($objTRANSFER_CLEARING->GLID)? $objTRANSFER_CLEARING->GLID : ''}}" />
                </div>
              </div>
              <div class="col-lg-2 pl"><p>Description</p></div>
              <div class="col-lg-3 pl">
                  <input  class="form-control" style="width: 100%" type="text" name="DESC2_4" id ="DESC2_4"  autocomplete="off" readonly  value="{{ isset($objTRANSFER_CLEARING->GLNAME)  && !is_null($objTRANSFER_CLEARING->GLNAME) ? $objTRANSFER_CLEARING->GLNAME : ''}}" >
              </div>
            </div>

            <div class="row">
              <div class="col-lg-2 pl"><p>Stock Transfer A/C</p></div>
              <div class="col-lg-2 pl">
                <div class="col-lg-10 pl">
                  <input type="text" name="txtLISTPOP1_popup_5" id="txtLISTPOP1_popup_5" disabled  class="form-control"  autocomplete="off"  readonly value="{{ isset($objSTOCK_TRANSFER_AC->GLCODE) && !is_null($objSTOCK_TRANSFER_AC->GLCODE) ? $objSTOCK_TRANSFER_AC->GLCODE : ''}}"  />

                  <input type="hidden" name="LISTPOP1ID_5" id="hdnLISTPOP1ID_5" class="form-control" autocomplete="off"  value="{{ isset($objSTOCK_TRANSFER_AC->GLID) && !is_null($objSTOCK_TRANSFER_AC->GLID)? $objSTOCK_TRANSFER_AC->GLID : ''}}" />
                </div>
              </div>
              <div class="col-lg-2 pl"><p>Description</p></div>
              <div class="col-lg-3 pl">
                  <input  class="form-control" style="width: 100%" type="text" name="DESC2_5" id ="DESC2_5"  autocomplete="off" readonly value="{{ isset($objSTOCK_TRANSFER_AC->GLNAME)  && !is_null($objSTOCK_TRANSFER_AC->GLNAME) ? $objSTOCK_TRANSFER_AC->GLNAME : ''}}" >
              </div>
            </div>

            <div class="row">
              <div class="col-lg-2 pl"><p>Consumption Account</p></div>
              <div class="col-lg-2 pl">
                <div class="col-lg-10 pl">
                  <input type="text" name="txtLISTPOP1_popup_6" id="txtLISTPOP1_popup_6" disabled  class="form-control"  autocomplete="off"  readonly value="{{ isset($objRM_CONSUMPTION->GLCODE) && !is_null($objRM_CONSUMPTION->GLCODE) ? $objRM_CONSUMPTION->GLCODE : ''}}"  />
                  <input type="hidden" name="LISTPOP1ID_6" id="hdnLISTPOP1ID_6" class="form-control" autocomplete="off" value="{{ isset($objRM_CONSUMPTION->GLID) && !is_null($objRM_CONSUMPTION->GLID)? $objRM_CONSUMPTION->GLID : ''}}" />
                </div>
              </div>
              <div class="col-lg-2 pl"><p>Description</p></div>
              <div class="col-lg-3 pl">
                  <input  class="form-control" style="width: 100%" type="text" name="DESC2_6" id ="DESC2_6"  autocomplete="off" readonly value="{{ isset($objRM_CONSUMPTION->GLNAME)  && !is_null($objRM_CONSUMPTION->GLNAME) ? $objRM_CONSUMPTION->GLNAME : ''}}" >
              </div>
            </div>

            <div class="row">
              <div class="col-lg-2 pl"><p>Rejected</p></div>
              <div class="col-lg-2 pl">
                <div class="col-lg-10 pl">
                  <input type="text" name="txtLISTPOP1_popup_7" id="txtLISTPOP1_popup_7"  disabled class="form-control"  autocomplete="off"  readonly  value="{{ isset($objREJECTED->GLCODE) && !is_null($objREJECTED->GLCODE) ? $objREJECTED->GLCODE : ''}}"  />
                  <input type="hidden" name="LISTPOP1ID_7" id="hdnLISTPOP1ID_7" class="form-control" autocomplete="off"  value="{{ isset($objREJECTED->GLID)  && !is_null($objREJECTED->GLID) ? $objREJECTED->GLID : ''}}"  />
                </div>
              </div>
              <div class="col-lg-2 pl"><p>Description</p></div>
              <div class="col-lg-3 pl">
                  <input  class="form-control" style="width: 100%" type="text" name="DESC2_7" id ="DESC2_7"  autocomplete="off" readonly  value="{{ isset($objREJECTED->GLNAME)  && !is_null($objREJECTED->GLNAME) ? $objREJECTED->GLNAME : ''}}" >
              </div>
            </div>

            <div class="row">
              <div class="col-lg-2 pl"><p>Shortage</p></div>
              <div class="col-lg-2 pl">
                <div class="col-lg-10 pl">
                  <input type="text" name="txtLISTPOP1_popup_8" id="txtLISTPOP1_popup_8"  disabled class="form-control"  autocomplete="off"  readonly  value="{{ isset($objSHORTAGE->GLCODE) && !is_null($objSHORTAGE->GLCODE) ? $objSHORTAGE->GLCODE : ''}}"   />

                  <input type="hidden" name="LISTPOP1ID_8" id="hdnLISTPOP1ID_8" class="form-control" autocomplete="off"   value="{{ isset($objSHORTAGE->GLID)  && !is_null($objSHORTAGE->GLID) ? $objSHORTAGE->GLID : ''}}" />
                </div>
              </div>
              <div class="col-lg-2 pl"><p>Description</p></div>
              <div class="col-lg-3 pl">
                  <input  class="form-control" style="width: 100%" type="text" name="DESC2_8" id ="DESC2_8"  autocomplete="off" readonly  value="{{ isset($objSHORTAGE->GLNAME)  && !is_null($objSHORTAGE->GLNAME) ? $objSHORTAGE->GLNAME : ''}}" >
              </div>
            </div>
           
            <div class="row">
              <div class="col-lg-2 pl"><p>Inventory Adj.</p></div>
              <div class="col-lg-2 pl">
                <div class="col-lg-10 pl">
                  <input type="text" name="txtLISTPOP1_popup_9" id="txtLISTPOP1_popup_9" disabled class="form-control"  autocomplete="off"  readonly value="{{ isset($objPHY_INVENTORY_ADJ->GLCODE) && !is_null($objPHY_INVENTORY_ADJ->GLCODE) ? $objPHY_INVENTORY_ADJ->GLCODE : ''}}"   />
                  <input type="hidden" name="LISTPOP1ID_9" id="hdnLISTPOP1ID_9" class="form-control" autocomplete="off"   value="{{ isset($objPHY_INVENTORY_ADJ->GLID)  && !is_null($objPHY_INVENTORY_ADJ->GLID) ? $objPHY_INVENTORY_ADJ->GLID : ''}}"  />
                </div>
              </div>
              <div class="col-lg-2 pl"><p>Description</p></div>
              <div class="col-lg-3 pl">
                  <input  class="form-control" style="width: 100%" type="text" name="DESC2_9" id ="DESC2_9"  autocomplete="off" readonly  value="{{ isset($objPHY_INVENTORY_ADJ->GLNAME)  && !is_null($objPHY_INVENTORY_ADJ->GLNAME) ? $objPHY_INVENTORY_ADJ->GLNAME : ''}}" >
              </div>
            </div>

            <div class="row">
              <div class="col-lg-2 pl"><p>WIP Account</p></div>
              <div class="col-lg-2 pl">
                <div class="col-lg-10 pl">
                  <input type="text" name="txtLISTPOP1_popup_10" id="txtLISTPOP1_popup_10" disabled  class="form-control"  autocomplete="off"  readonly   value="{{ isset($objWIP_AC->GLCODE) && !is_null($objWIP_AC->GLCODE) ? $objWIP_AC->GLCODE : ''}}"    />

                  <input type="hidden" name="LISTPOP1ID_10" id="hdnLISTPOP1ID_10" class="form-control" autocomplete="off"   value="{{ isset($objWIP_AC->GLID) && !is_null($objWIP_AC->GLID) ? $objWIP_AC->GLID : ''}}"     />
                </div>
              </div>
              <div class="col-lg-2 pl"><p>Description</p></div>
              <div class="col-lg-3 pl">
                  <input  class="form-control" style="width: 100%" type="text" name="DESC2_10" id ="DESC2_10"  autocomplete="off" readonly  value="{{ isset($objWIP_AC->GLNAME)  && !is_null($objWIP_AC->GLNAME) ? $objWIP_AC->GLNAME : ''}}" >
              </div>
            </div>

            <div class="row">
              <div class="col-lg-2 pl"><p>Gain Loss A/C</p></div>
              <div class="col-lg-2 pl">
                <div class="col-lg-10 pl">
                  <input type="text" name="txtLISTPOP1_popup_11" id="txtLISTPOP1_popup_11"  disabled class="form-control"  autocomplete="off"  readonly  value="{{ isset($objGAIN_LOSS_AC->GLCODE) && !is_null($objGAIN_LOSS_AC->GLCODE) ? $objGAIN_LOSS_AC->GLCODE : ''}}"   />

                  <input type="hidden" name="LISTPOP1ID_11" id="hdnLISTPOP1ID_11" class="form-control" autocomplete="off"  value="{{ isset($objGAIN_LOSS_AC->GLID) && !is_null($objGAIN_LOSS_AC->GLID) ? $objGAIN_LOSS_AC->GLID : ''}}"   />
                </div>
              </div>
              <div class="col-lg-2 pl"><p>Description</p></div>
              <div class="col-lg-3 pl">
                  <input  class="form-control" style="width: 100%" type="text" name="DESC2_11" id ="DESC2_11"  autocomplete="off" readonly  value="{{ isset($objGAIN_LOSS_AC->GLNAME)  && !is_null($objGAIN_LOSS_AC->GLNAME) ? $objGAIN_LOSS_AC->GLNAME : ''}}" >
              </div>
            </div>
            
            <div class="row">
              <div class="col-lg-2 pl"><p>FA Account</p></div>
              <div class="col-lg-2 pl">
                <div class="col-lg-10 pl">
                  <input type="text" name="txtLISTPOP1_popup_12" id="txtLISTPOP1_popup_12" disabled  class="form-control"  autocomplete="off"  readonly  value="{{ isset($objFA_AC->GLCODE) && !is_null($objFA_AC->GLCODE) ? $objFA_AC->GLCODE : ''}}"    />

                  <input type="hidden" name="LISTPOP1ID_12" id="hdnLISTPOP1ID_12" class="form-control" autocomplete="off"  value="{{ isset($objFA_AC->GLID) && !is_null($objFA_AC->GLID) ? $objFA_AC->GLID : ''}}"  />
                </div>
              </div>
              <div class="col-lg-2 pl"><p>Description</p></div>
              <div class="col-lg-3 pl">
                  <input  class="form-control" style="width: 100%" type="text" name="DESC2_12" id ="DESC2_12"  autocomplete="off" readonly  value="{{ isset($objFA_AC->GLNAME)  && !is_null($objFA_AC->GLNAME) ? $objFA_AC->GLNAME : ''}}" >
              </div>
            </div>
            
            <div class="row">
              <div class="col-lg-2 pl"><p>FA Clearing Account</p></div>
              <div class="col-lg-2 pl">
                <div class="col-lg-10 pl">
                  <input type="text" name="txtLISTPOP1_popup_13" id="txtLISTPOP1_popup_13" disabled  class="form-control"  autocomplete="off"  readonly  value="{{ isset($objFA_CLEANING_AC->GLCODE) && !is_null($objFA_CLEANING_AC->GLCODE) ? $objFA_CLEANING_AC->GLCODE : ''}}"   />

                  <input type="hidden" name="LISTPOP1ID_13" id="hdnLISTPOP1ID_13" class="form-control" autocomplete="off"  value="{{ isset($objFA_CLEANING_AC->GLID) && !is_null($objFA_CLEANING_AC->GLID) ? $objFA_CLEANING_AC->GLID : ''}}"  />
                </div>
              </div>
              <div class="col-lg-2 pl"><p>Description</p></div>
              <div class="col-lg-3 pl">
                  <input  class="form-control" style="width: 100%" type="text" name="DESC2_13" id ="DESC2_13"  autocomplete="off" readonly  value="{{ isset($objFA_CLEANING_AC->GLNAME)  && !is_null($objFA_CLEANING_AC->GLNAME) ? $objFA_CLEANING_AC->GLNAME : ''}}"  >
              </div>
            </div>
           
            <div class="row">
              <div class="col-lg-2 pl"><p>Depreciation Account</p></div>
              <div class="col-lg-2 pl">
                <div class="col-lg-10 pl">
                  <input type="text" name="txtLISTPOP1_popup_14" id="txtLISTPOP1_popup_14"  disabled class="form-control"  autocomplete="off"  readonly  value="{{ isset($objDEPR_AC->GLCODE) && !is_null($objDEPR_AC->GLCODE) ? $objDEPR_AC->GLCODE : ''}}"  />

                  <input type="hidden" name="LISTPOP1ID_14" id="hdnLISTPOP1ID_14" class="form-control" autocomplete="off"  value="{{ isset($objDEPR_AC->GLID) && !is_null($objDEPR_AC->GLID) ? $objDEPR_AC->GLID : ''}}"  />
                </div>
              </div>
              <div class="col-lg-2 pl"><p>Description</p></div>
              <div class="col-lg-3 pl">
                  <input  class="form-control" style="width: 100%" type="text" name="DESC2_14" id ="DESC2_14"  autocomplete="off" readonly  value="{{ isset($objDEPR_AC->GLNAME)  && !is_null($objDEPR_AC->GLNAME) ? $objDEPR_AC->GLNAME : ''}}" >
              </div>
            </div>

            <div class="row">
                <div class="col-lg-2 pl"><p>Purchase Return Account</p></div>
                <div class="col-lg-2 pl">
                  <div class="col-lg-10 pl">
                    <input type="text" name="txtLISTPOP1_popup_15" id="txtLISTPOP1_popup_15" disabled class="form-control"  autocomplete="off"  readonly value="{{ isset($objPURCHASE_RETURN_AC->GLCODE) && !is_null($objPURCHASE_RETURN_AC->GLCODE) ? $objPURCHASE_RETURN_AC->GLCODE : ''}}" />
                    <input type="hidden" name="LISTPOP1ID_15" id="hdnLISTPOP1ID_15" class="form-control" autocomplete="off" value="{{ isset($objPURCHASE_RETURN_AC->GLID) && !is_null($objPURCHASE_RETURN_AC->GLID) ? $objPURCHASE_RETURN_AC->GLID : ''}}"  />
                  </div>
                </div>
                <div class="col-lg-2 pl"><p>Description</p></div>
                <div class="col-lg-3 pl">
                    <input  class="form-control" style="width: 100%" type="text" name="DESC2_15" id ="DESC2_15" disabled  autocomplete="off" readonly value="{{ isset($objPURCHASE_RETURN_AC->GLNAME)  && !is_null($objPURCHASE_RETURN_AC->GLNAME) ? $objPURCHASE_RETURN_AC->GLNAME : ''}}" />
                </div>
              </div>

              <div class="row">
                <div class="col-lg-2 pl"><p>Import Purchase Account</p></div>
                <div class="col-lg-2 pl">
                  <div class="col-lg-10 pl">
                    <input type="text" name="txtLISTPOP1_popup_16" id="txtLISTPOP1_popup_16" disabled class="form-control"  autocomplete="off"  readonly value="{{ isset($objIMPORT_PURCHASE_AC->GLCODE) && !is_null($objIMPORT_PURCHASE_AC->GLCODE) ? $objIMPORT_PURCHASE_AC->GLCODE : ''}}" />
                    <input type="hidden" name="LISTPOP1ID_16" id="hdnLISTPOP1ID_16" class="form-control" autocomplete="off" value="{{ isset($objIMPORT_PURCHASE_AC->GLID) && !is_null($objIMPORT_PURCHASE_AC->GLID) ? $objIMPORT_PURCHASE_AC->GLID : ''}}"  />
                  </div>
                </div>
                <div class="col-lg-2 pl"><p>Description</p></div>
                <div class="col-lg-3 pl">
                    <input  class="form-control" style="width: 100%" type="text" name="DESC2_16" id ="DESC2_16" disabled  autocomplete="off" readonly value="{{ isset($objIMPORT_PURCHASE_AC->GLNAME)  && !is_null($objIMPORT_PURCHASE_AC->GLNAME) ? $objIMPORT_PURCHASE_AC->GLNAME : ''}}" />
                </div>
              </div>

              <div class="row">
                <div class="col-lg-2 pl"><p>Job Work Invoice Account</p></div>
                <div class="col-lg-2 pl">
                  <div class="col-lg-10 pl">
                    <input type="text" name="txtLISTPOP1_popup_17" id="txtLISTPOP1_popup_17" disabled class="form-control"  autocomplete="off"  readonly value="{{ isset($objJWI->GLCODE) && !is_null($objJWI->GLCODE) ? $objJWI->GLCODE : ''}}" />
                    <input type="hidden" name="LISTPOP1ID_17" id="hdnLISTPOP1ID_17" class="form-control" autocomplete="off" value="{{ isset($objJWI->GLID) && !is_null($objJWI->GLID) ? $objJWI->GLID : ''}}"  />
                  </div>
                </div>
                <div class="col-lg-2 pl"><p>Description</p></div>
                <div class="col-lg-3 pl">
                    <input  class="form-control" style="width: 100%" type="text" name="DESC2_17" id ="DESC2_17" disabled  autocomplete="off" readonly value="{{ isset($objJWI->GLNAME)  && !is_null($objJWI->GLNAME) ? $objJWI->GLNAME : ''}}" />
                </div>
              </div>

              <div class="row">
                <div class="col-lg-2 pl"><p>Job Work Return Account</p></div>
                <div class="col-lg-2 pl">
                  <div class="col-lg-10 pl">
                    <input type="text" name="txtLISTPOP1_popup_18" id="txtLISTPOP1_popup_18" disabled class="form-control"  autocomplete="off"  readonly  value="{{ isset($objJWR->GLCODE) && !is_null($objJWR->GLCODE) ? $objJWR->GLCODE : ''}}" />
                    <input type="hidden" name="LISTPOP1ID_18" id="hdnLISTPOP1ID_18" class="form-control" autocomplete="off"  value="{{ isset($objJWR->GLID) && !is_null($objJWR->GLID) ? $objJWR->GLID : ''}}"  />
                  </div>
                </div>
                <div class="col-lg-2 pl"><p>Description</p></div>
                <div class="col-lg-3 pl">
                    <input  class="form-control" style="width: 100%" type="text" name="DESC2_18" id ="DESC2_18"  disabled autocomplete="off" readonly  value="{{ isset($objJWR->GLNAME)  && !is_null($objJWR->GLNAME) ? $objJWR->GLNAME : ''}}" />
                </div>
              </div>
              <div class="row">
                <div class="col-lg-2 pl"><p>Custom Duty Account</p></div>
                <div class="col-lg-2 pl">
                  <div class="col-lg-10 pl">
                    <input type="text" name="txtLISTPOP1_popup_19" id="txtLISTPOP1_popup_19" class="form-control"  disabled autocomplete="off"  readonly  value="{{ isset($objCUSTOM->GLCODE) && !is_null($objCUSTOM->GLCODE) ? $objCUSTOM->GLCODE : ''}}" />
                    <input type="hidden" name="LISTPOP1ID_19" id="hdnLISTPOP1ID_19" class="form-control" autocomplete="off"  value="{{ isset($objCUSTOM->GLID) && !is_null($objCUSTOM->GLID) ? $objCUSTOM->GLID : ''}}"  />
                  </div>
                </div>
                <div class="col-lg-2 pl"><p>Description</p></div>
                <div class="col-lg-3 pl">
                    <input  class="form-control" style="width: 100%" type="text" name="DESC2_19" id ="DESC2_19" disabled autocomplete="off" readonly  value="{{ isset($objCUSTOM->GLNAME)  && !is_null($objCUSTOM->GLNAME) ? $objCUSTOM->GLNAME : ''}}" />
                </div>
              </div>
			  <div class="row">
                <div class="col-lg-2 pl"><p>Purchase Account(Inter State)</p></div>
                <div class="col-lg-2 pl">
                  <div class="col-lg-10 pl">
                    <input type="text" name="txtLISTPOP1_popup_20" id="txtLISTPOP1_popup_20" disabled value="{{ isset($objPURCHASEIS_AC->GLCODE) && !is_null($objPURCHASEIS_AC->GLCODE) ? $objPURCHASEIS_AC->GLCODE : ''}}" class="form-control mandatory"  autocomplete="off"  readonly  required/>
                          <input type="hidden" name="LISTPOP1ID_20" id="hdnLISTPOP1ID_20" value="{{ isset($objPURCHASEIS_AC->GLID) && !is_null($objPURCHASEIS_AC->GLID)? $objPURCHASEIS_AC->GLID : ''}}"  class="form-control" autocomplete="off" />
                  </div>
                </div>
                <div class="col-lg-2 pl"><p>Description</p></div>
                <div class="col-lg-3 pl">
                  <input  class="form-control" style="width: 100%" type="text" name="DESC2_0" id ="DESC2_0" disabled  value="{{ isset($objPURCHASEIS_AC->GLNAME)  && !is_null($objPURCHASEIS_AC->GLNAME) ? $objPURCHASEIS_AC->GLNAME : ''}}"  autocomplete="off" readonly>
                </div>
            </div>
          
             
              

              <div class="row">
                <div class="col-lg-2 pl"><p>De-Activated</p></div>
                <div class="col-lg-1 pl pr">
                <input type="checkbox"   name="DEACTIVATED"  id="deactive-checkbox_0" {{$objResponse->DEACTIVATED == 1 ? "checked" : ""}}
                 value='{{$objResponse->DEACTIVATED == 1 ? 1 : 0}}' tabindex="2"   disabled >
                </div>
                
                <div class="col-lg-2 pl"><p>Date of De-Activated</p></div>
                <div class="col-lg-2 pl">
                  <input type="date" name="DODEACTIVATED" class="form-control" id="DODEACTIVATED" {{$objResponse->DEACTIVATED == 1 ? "" : "disabled"}} value="{{isset($objResponse->DODEACTIVATED) && $objResponse->DODEACTIVATED !="" && $objResponse->DODEACTIVATED !="1900-01-01" ? $objResponse->DODEACTIVATED:''}}" tabindex="3" placeholder="dd/mm/yyyy"   disabled />
                </div>
             </div>
             <br/>
             
          </div>
        </form>
    </div><!--purchase-order-view-->


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
        </div><!--btdiv-->
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Alert -->
<!-- POPUP2-->
<div id="LISTPOP1popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='LISTPOP1_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>General Leader Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="LISTPOP1Table" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
          <tr id="none-select" class="searchalldata" hidden >            
            <td > <input type="text" name="fieldid" id="hdn_LISTPOP1id"/>
              <input type="text" name="fieldid2" id="hdn_LISTPOP1id2"/>
              <input type="text" name="fieldid3" id="hdn_LISTPOP1id3"/>
            </td>
          </tr>
          <tr>
                  <th>Code</th>
                  <th>Description</th>
          </tr>
    </thead>
    <tbody>
    <tr>
    <td>
    <input type="text" id="LISTPOP1codesearch" onkeyup="LISTPOP1CodeFunction()">
    </td>
    <td>
    <input type="text" id="LISTPOP1namesearch" onkeyup="LISTPOP1NameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="LISTPOP1Table2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">

        </thead>
        <tbody id="tbody_LISTPOP1">     
        
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- POPUP2 END-->
@endsection
<!-- btnSave -->

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

$('#btnAdd').on('click', function() {
      var viewURL = '{{route("master",[167,"add"])}}';
      window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });

 var formDataMst = $( "#frm_mst_edit" );
     formDataMst.validate();

     $("#AC_SET_DESC").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_NAME").hide();
        validateSingleElemnet("AC_SET_DESC");
    });

    $( "#AC_SET_DESC" ).rules( "add", {
        required: true,
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field."
        }
    });

    $("#DODEACTIVATED").blur(function(){
      $(this).val($.trim( $(this).val() ));
      $("#ERROR_DODEACTIVATED").hide();
      validateSingleElemnet("DODEACTIVATED");
    });

    $( "#DODEACTIVATED" ).rules( "add", {
        required: true,
        DateValidate:true,
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field"
        }
    });

    //validae single element
    function validateSingleElemnet(element_id){
      var validator =$("#frm_mst_edit" ).validate();
          validator.element( "#"+element_id+"" );
    }

    //validate
    $( "#btnSave" ).click(function() {

        if(formDataMst.valid()){
            //set function nane of yes and no btn 
            $("#alert").modal('show');
            $("#AlertMessage").text('Do you want to save to record.');
            $("#YesBtn").data("funcname","fnSaveData");  //set dynamic fucntion name
            $("#YesBtn").focus();
            highlighFocusBtn('activeYes');

        }

    });//btnSave

    
    //validate and approve
    $("#btnApprove").click(function() {
        
        if(formDataMst.valid()){
            //set function nane of yes and no btn 
            $("#alert").modal('show');
            $("#AlertMessage").text('Do you want to save to record.');
            $("#YesBtn").data("funcname","fnApproveData");  //set dynamic fucntion name of approval
            $("#YesBtn").focus();
            highlighFocusBtn('activeYes');

        }

    });//btnSave


    $("#YesBtn").click(function(){

      $("#alert").modal('hide');
      var customFnName = $("#YesBtn").data("funcname");
          window[customFnName]();

    }); //yes button

    
    window.fnSaveData = function (){
        
        //validate and save data
        event.preventDefault();

        var getDataForm = $("#frm_mst_edit");
        var formData = getDataForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("mastermodify",[167,"update"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    if(data.errors.AC_SET_DESC){
                        showError('ERROR_NAME',data.errors.AC_SET_DESC);
                    }
                   if(data.exist=='norecord') {

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
                    $("#frm_mst_edit").trigger("reset");

                    $("#alert").modal('show');
                    $("#OkBtn").focus();

                }
                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });

    };// fnSaveData


    // save and approve 
    window.fnApproveData = function (){
        
        //validate and save data
        event.preventDefault();

        var getDataForm = $("#frm_mst_edit");
        var formData = getDataForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("mastermodify",[167,"singleapprove"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    if(data.errors.AC_SET_DESC){
                        showError('ERROR_NAME',data.errors.AC_SET_DESC);
                    }
                   if(data.exist=='norecord') {

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
                    $("#frm_mst_edit").trigger("reset");

                    $("#alert").modal('show');
                    $("#OkBtn").focus();

                }
                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });

    };// fnApproveData

    //no button
    $("#NoBtn").click(function(){

      $("#alert").modal('hide');
      var custFnName = $("#NoBtn").data("funcname");
          window[custFnName]();

    }); //no button

   
    $("#OkBtn").click(function(){

        $("#alert").modal('hide');

        $("#YesBtn").show();
        $("#NoBtn").show();
        $("#OkBtn").hide();

        $(".text-danger").hide();
        window.location.href = '{{route("master",[167,"index"]) }}';

    }); ///ok button

    $("#btnUndo").click(function(){

        $("#AlertMessage").text("Do you want to erase entered information in this record?");
        $("#alert").modal('show');

        $("#YesBtn").data("funcname","fnUndoYes");
        $("#YesBtn").show();

        $("#NoBtn").data("funcname","fnUndoNo");
        $("#NoBtn").show();

        $("#OkBtn").hide();
        $("#NoBtn").focus();
        highlighFocusBtn('activeNo');

    }); ////Undo button

   
    $("#OkBtn").click(function(){
      
        $("#alert").modal('hide');

    });////ok button


   window.fnUndoYes = function (){
      
      //reload form
      window.location.reload();

   }//fnUndoYes


   window.fnUndoNo = function (){

      $("#PLCODE").focus();

   }//fnUndoNo


    //
    function showError(pId,pVal){

      $("#"+pId+"").text(pVal);
      $("#"+pId+"").show();

    }  

    function highlighFocusBtn(pclass){
       $(".activeYes").hide();
       $(".activeNo").hide();
       
       $("."+pclass+"").show();
    }

</script>
<script type="text/javascript">
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

$(function() { 
  //$("#DESCRIPTIONS").focus(); 
});



  //------------------------
  //LISTPOP1 Dropdown
  let sqtid = "#LISTPOP1Table2";
      let sqtid2 = "#LISTPOP1Table";
      let salesquotationheaders = document.querySelectorAll(sqtid2 + " th");

      // Sort the table element when clicking on the table headers
      salesquotationheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(sqtid, ".clssqid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function LISTPOP1CodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("LISTPOP1codesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("LISTPOP1Table2");
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

  function LISTPOP1NameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("LISTPOP1namesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("LISTPOP1Table2");
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

  $(document).on('focus','[id*="txtLISTPOP1_popup"]',function(event){

        
          var id = $(this).attr('id');
          var id2 = $(this).parent().parent().find('[id*="LISTPOP1ID"]').attr('id');      
          var id3 = $(this).parent().parent().parent().find('[id*="DESC2"]').attr('id');      

          $('#hdn_LISTPOP1id').val(id);
          $('#hdn_LISTPOP1id2').val(id2);
          $('#hdn_LISTPOP1id3').val(id3);
        
          $("#LISTPOP1popup").show();
          //$("#tbody_LISTPOP1").html('');
          $("#tbody_LISTPOP1").html('Loading...');
          $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          })
          $.ajax({
              url:'{{route("master",[167,"getgl"])}}',
              type:'POST',
              success:function(data) {
                $("#tbody_LISTPOP1").html(data);
                BindLISTPOP1Events();
              },
              error:function(data){
                console.log("Error: Something went wrong.");
                $("#tbody_LISTPOP1").html('');
              },
          });

      });

      $("#LISTPOP1_closePopup").click(function(event){
        $("#LISTPOP1popup").hide();
      });

      function BindLISTPOP1Events()
      {
          $(".clsLISTPOP1id").dblclick(function(){
              var fieldid = $(this).attr('id');
              var txtval =    $("#txt"+fieldid+"").val();
              var texdesc =   $("#txt"+fieldid+"").data("desc");
              var texdescdate =   $("#txt"+fieldid+"").data("descdate");
              
              var txtid= $('#hdn_LISTPOP1id').val();
              var txt_id2= $('#hdn_LISTPOP1id2').val();
              var txt_id3= $('#hdn_LISTPOP1id3').val();

              $('#'+txtid).val(texdesc);
              $('#'+txt_id2).val(txtval);
              $('#'+txt_id3).val(texdescdate);
              $('#'+txtid).parent().parent().find('[id*="REMARKS"]').val( $.trim($('#MODNAME').val()) );
              $('#'+txtid).parent().parent().find('[id*="VHEADING"]').prop('selectedIndex',0);
              

              $("#LISTPOP1popup").hide();
              
              $("#LISTPOP1codesearch").val(''); 
              $("#LISTPOP1namesearch").val(''); 
              LISTPOP1CodeFunction();
              event.preventDefault();
          });
      }
//------------------------

</script>


@endpush