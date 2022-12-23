
@extends('layouts.app')
@section('content')
   

    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Vendor Quotation Comparision (VQC)</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                  <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                  <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
                  <button class="btn topnavbt" id="btnSaveFormData" ><i class="fa fa-save"></i> Save</button>
                  <button style="display:none" class="btn topnavbt buttonload"> <i class="fa fa-refresh fa-spin"></i> {{Session::get('save')}}</button>
                  <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                  <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                  <button class="btn topnavbt" id="btnUndo"  ><i class="fa fa-undo"></i> Undo</button>
                  <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                  <button style="display:none" class="btn topnavbt buttonload_approve" > <i class="fa fa-refresh fa-spin"></i> {{Session::get('approve')}}</button>
                  <button class="btn topnavbt" id="btnApprove" {{ (isset($objRights->APPROVAL1) || isset($objRights->APPROVAL2) || isset($objRights->APPROVAL3) || isset($objRights->APPROVAL4) || isset($objRights->APPROVAL5)) &&  ($objRights->APPROVAL1||$objRights->APPROVAL2||$objRights->APPROVAL3||$objRights->APPROVAL4||$objRights->APPROVAL5) == 1 ? '' : 'disabled'}} ><i class="fa fa-lock"></i> Approved</button>
                  <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                  <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                </div>
            </div>
    </div><!--topnav-->	
    <!-- multiple table-responsive table-wrapper-scroll-y my-custom-scrollbar -->

    
    <form id="frm_trn_add" method="POST"  >

    <div class="container-fluid purchase-order-view">
        
            @csrf
            <div class="container-fluid filter">

                    <div class="inner-form">
                    
                        <div class="row">
                            <div class="col-lg-2 pl"><p>VQC No*</p></div>
                            <div class="col-lg-2 pl">
                              <input type="text" name="VQC_NO" id="VQC_NO" value="{{ isset($objMstResponse->VQC_NO)?$objMstResponse->VQC_NO:'' }}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase"  >
                              <input type="hidden" name="VQCID" id="VQCID" value="{{ isset($objMstResponse->VQCID)?$objMstResponse->VQCID:'' }}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase"  >
                            </div>
                            
                            <div class="col-lg-2 pl"><p>VQC Date*</p></div>
                            <div class="col-lg-2 pl">
                                <input type="date" name="VQC_DT" id="VQC_DT" onchange="checkPeriodClosing('{{$FormId}}',this.value,1)" value="{{ isset($objMstResponse->VQC_DT)?$objMstResponse->VQC_DT:'' }}" class="form-control mandatory" autocomplete="off" placeholder="dd/mm/yyyy" >
                                <input type="hidden" id="LAST_DATE" value="{{ isset($objlastVQC_DT[0]->VQC_DT)?$objlastVQC_DT[0]->VQC_DT:'' }}">
                            </div>

                            
             
                        </div>
                       
              



                        <div class="row">
                  
                            
                  <div class="col-lg-2 pl"><p>Vendor 1*</p></div>
                  <div class="col-lg-2 pl">
                      <input type="text" name="VID_REF1" id="VID_REF1_popup" class="form-control mandatory" value="{{isset($objMstResponse->VID_REF1) && $objMstResponse->VID_REF1 !=''  ? $objMstResponse->VENDOR_CODE1.'-'.$objMstResponse->VENDOR_NAME1 : ''}}" onclick="get_vendor('VID_REF1');"  autocomplete="off" readonly/>
                      <input type="hidden" name="VID_REF1" id="VID_REF1" class="form-control" value="{{isset($objMstResponse->VID_REF1)?$objMstResponse->VID_REF1:''}}" autocomplete="off" />
                                                               
                                                                                   
                  </div>


                  <div class="col-lg-2 pl"><p>Vendor 2*</p></div>
                  <div class="col-lg-2 pl">
                  <input type="text" name="VID_REF2" id="VID_REF2_popup" class="form-control mandatory" value="{{isset($objMstResponse->VID_REF2) && $objMstResponse->VID_REF2 !=''  ? $objMstResponse->VENDOR_CODE2.'-'.$objMstResponse->VENDOR_NAME2 : ''}}" onclick="get_vendor('VID_REF2');"    autocomplete="off" readonly/>
                    <input type="hidden" name="VID_REF2" id="VID_REF2" value="{{isset($objMstResponse->VID_REF2)?$objMstResponse->VID_REF2:''}}" class="form-control" autocomplete="off" />
                                                    
                                                                       
                  </div>

                  <div class="col-lg-2 pl"><p>Vendor 3</p></div>
                  <div class="col-lg-2 pl">
                  <input type="text" name="VID_REF3" id="VID_REF3_popup" class="form-control mandatory" value="{{isset($objMstResponse->VID_REF3) && $objMstResponse->VID_REF3 !=''  ? $objMstResponse->VENDOR_CODE3.'-'.$objMstResponse->VENDOR_NAME3 : ''}}" onclick="get_vendor('VID_REF3');"  autocomplete="off" readonly/>
                  <input type="hidden" name="VID_REF3" id="VID_REF3" value="{{isset($objMstResponse->VID_REF3)?$objMstResponse->VID_REF3:''}}" class="form-control" autocomplete="off" />
                                                  
                                                  
                  </div>

              </div>
              



              <div class="row">
        
                  
        <div class="col-lg-2 pl"><p>Quotation No 1*</p></div>
        <div class="col-lg-2 pl">
            <input type="text" name="VQID_REF1" id="VQID_REF1_popup" value="{{isset($objMstResponse->VQ_NO1)?$objMstResponse->VQ_NO1:''}}" class="form-control mandatory" onclick="get_vendor_quotation('VQID_REF1');"  autocomplete="off" readonly/>
            <input type="hidden" name="VQID_REF1" id="VQID_REF1" value="{{isset($objMstResponse->VQID_REF1)?$objMstResponse->VQID_REF1:''}}" class="form-control" autocomplete="off" />
                                                              
                                                                     
        </div>


        <div class="col-lg-2 pl"><p>Quotation No 2*</p></div>
        <div class="col-lg-2 pl">
        <input type="text" name="VQID_REF2" id="VQID_REF2_popup" value="{{isset($objMstResponse->VQ_NO2)?$objMstResponse->VQ_NO2:''}}" class="form-control mandatory" onclick="get_vendor_quotation('VQID_REF2');"  autocomplete="off" readonly/>
            <input type="hidden" name="VQID_REF2" id="VQID_REF2" value="{{isset($objMstResponse->VQID_REF2)?$objMstResponse->VQID_REF2:''}}" class="form-control" autocomplete="off" />
                                                              
                                                                     
        </div>

        <div class="col-lg-2 pl"><p>Quotation No 3</p></div>
        <div class="col-lg-2 pl">
        <input type="text" name="VQID_REF3" id="VQID_REF3_popup" value="{{isset($objMstResponse->VQ_NO3)?$objMstResponse->VQ_NO3:''}}" class="form-control mandatory" onclick="get_vendor_quotation('VQID_REF3');"  autocomplete="off" readonly/>
            <input type="hidden" name="VQID_REF3" id="VQID_REF3" value="{{isset($objMstResponse->VQID_REF3)?$objMstResponse->VQID_REF3:''}}" class="form-control" autocomplete="off" />
                                                              
                                                                     
        </div>






                            
                        </div>
                    </div>

                    <div class="container-fluid purchase-order-view">

<div class="row">
    <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#Material">Material</a></li>
        <li><a data-toggle="tab" href="#TC">T & C</a></li>
        <li><a data-toggle="tab" href="#CT">Calculation Template</a></li>  
    </ul>
    
    
    
    <div class="tab-content">

        <div id="Material" class="tab-pane fade in active">
            <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:500px;margin-top:10px;" >
                <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                    <thead id="thead1"  style="position: sticky;top: 0">
                        <tr>                                                                                                    
                            <th rowspan="2">Item Code <input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1" value="1"></th>
                            <th rowspan="2">Item Name</th>                                           
                            <th rowspan="2" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</th>
                                                    <th rowspan="2" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</th>
                                                    <th rowspan="2" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</th>
                            <th rowspan="2" width="10%">UoM</th>    
                            <th colspan="3" width="20%">Vendor1</th>
                            <th colspan="3" width="20%">Vendor2</th>
                            <th colspan="3" width="20%">Vendor3</th>
                            <th rowspan="2" width="3%">Action</th>
                        </tr>
                            <tr>
                                <th>Qty</th>
                                <th>Rate</th>
                                <th>Amount</th>
                                <th>Qty</th>
                                <th>Rate</th>
                                <th>Amount</th>
                                <th>Qty</th>
                                <th>Rate</th>
                                <th>Amount</th>
                     
                            </tr>
                            
                    </thead>
                    <tbody>
                    <tbody id="tbody_item">
                    @if(!empty($objList1))
                    @foreach($objList1 as $key => $row)
                    <tr  class="participantRow{{ $row->QID_REF }}"  >
                 
                         <td hidden><input type="text" name="rowscount1[]"  > </td>
                         <td hidden><input type="text" name="VID_REF[]" id="VID_REF_{{ $row->QID_REF }}" value="{{$row->QID_REF}}" > </td>
                         <td hidden><input type="text" name="VQID_REF[]" id="VQID_REF_'.$index.'" value="" > </td>
                         <td hidden><input type="text" name="ITEMID_REF[]"   value="{{$row->ITEMID}}"> </td>                                                    
                         <td><input type="text" name="ITEMCODE[]"  class="form-control"value="{{$row->ICODE}}"  autocomplete="off"  readonly style="width:100px;" /></td>
                         <td><input type="text" name="ITEMNAME[]"  class="form-control" value="{{$row->NAME}}"  autocomplete="off"  readonly style="width:200px;" /></td>
                         <td {{$AlpsStatus['hidden']}}  ><input type="text" name="Alpspartno_0" id="Alpspartno_0" class="form-control"  autocomplete="off"   value="{{$row->ALPS_PART_NO}}" readonly  /></td>
                    <td {{$AlpsStatus['hidden']}}  ><input type="text" name="Custpartno_0" id="Custpartno_0" class="form-control" value="{{$row->CUSTOMER_PART_NO}}"  autocomplete="off"  readonly  /></td>
                      <td {{$AlpsStatus['hidden']}}  ><input type="text"  value="{{$row->OEM_PART_NO}}" name="OEMpartno_0" id="OEMpartno_0" class="form-control"  autocomplete="off"  readonly /></td>
                 
                           
                     <td><input type="text" name="UOMID[]"  class="form-control" value="{{$row->UOMCODE.'-'.$row->DESCRIPTIONS}}"  autocomplete="off"  readonly style="width:100px;"/></td>
                     <td hidden><input type="hidden" name="UOMID_REF[]"  value="{{$row->UOMID_REF}}"  class="form-control"  autocomplete="off" /></td>
                   
                       <td><input type="text" name="QTY1[]"   value="{{$row->V1_QTY}}" class="form-control three-digits" maxlength="15"  autocomplete="off"  readonly/></td>
                       <td><input type="text" name="RATE1[]"  value="{{$row->V1_RATE}}" class="form-control three-digits" maxlength="15"  autocomplete="off" readonly  /></td>
                       <td><input type="text" name="AMOUNT1[]"  value="{{$row->V1_RATE*$row->V1_QTY}}" class="form-control three-digits"  maxlength="15"  autocomplete="off" readonly /></td>

                       <td><input type="text" name="QTY2[]"  value="{{$row->V2_QTY}}" class="form-control three-digits" maxlength="15"  autocomplete="off"  readonly/></td>
                       <td><input type="text" name="RATE2[]"  value="{{$row->V2_RATE}}" class="form-control three-digits" maxlength="15"  autocomplete="off" readonly  /></td>
                       <td><input type="text" name="AMOUNT2[]"  value="{{$row->V2_RATE*$row->V2_QTY}}" class="form-control three-digits"  maxlength="15"  autocomplete="off" readonly /></td>

                       <td><input type="text" name="QTY3[]"  value="{{$row->V3_QTY}}" class="form-control three-digits" maxlength="15"  autocomplete="off"  readonly/></td>
                       <td><input type="text" name="RATE3[]"  value="{{$row->V3_RATE}}" class="form-control three-digits" maxlength="15"  autocomplete="off" readonly  /></td>
                       <td><input type="text" name="AMOUNT3[]"  value="{{$row->V3_RATE*$row->V3_QTY}}" class="form-control three-digits"  maxlength="15"  autocomplete="off" readonly /></td>
                   
                             
                     <td align="center" ><button class="btn add material" disabled title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button><button class="btn remove dmaterial" disabled title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
                 
                         </tr>

                        @endforeach
                        @endif
                    </tbody>
            </table>
            </div>	
        </div>
        
        
        
        <div id="TC" class="tab-pane fade">
            <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="margin-top:15px;height:240px;width:70%;">
                <table id="example3" class="display nowrap table table-striped table-bordered itemlist" style="height:auto !important;">
                    <thead id="thead1"  style="position: sticky;top: 0">                      
             <tr>                                                 
             <th>Terms & Conditions Description</th>
                 <th >Vendor1</th>
                 <th>Vendor2</th>
                 <th>Vendor3</th>
                 <th rowspan="2" style="width: 11%;">Action</th>
             </tr>
                 <tr>
                     <th>	</th>
                     <th>Value / Comment	</th>
                     <th>Value / Comment	</th>
                     <th>Value / Comment	</th>                                  
                 </tr>
                 
         </thead>

                    <tbody id="tncbody">
                    @if(!empty($objList2))
                    @foreach($objList2 as $key => $row)

                    <tr  class="participantRow3{{ $row->QID_REF }}"  >

                
                        <td hidden><input type="text" name="VID_REF_TNC[]" id="VID_REF_{{ $row->QID_REF }}" value="{{$row->QID_REF}}" > </td>
                        <td hidden><input type="text" name="rowscount2[]"  > </td>

                        <td hidden><input type="hidden" name="TNCID_REF[]" value="{{$row->TNCID_REF}}" class="form-control" autocomplete="off" /></td>
                        <td hidden><input type="hidden" name="VQID_REF_TNC[]"  class="form-control" autocomplete="off" /></td>
                        <td ><input type="text" name="TNC_DESC[]" readonly value="{{$row->TNC_NAME}}" class="form-control" autocomplete="off" /></td>
                        <td ><input type="text"  name="VALUE1[]" value="{{$row->V1_VALUE}}" class="form-control" autocomplete="off" readonly /></td>
                        <td ><input type="text"  name="VALUE2[]" value="{{$row->V2_VALUE}}" class="form-control" autocomplete="off" readonly /></td>
                        <td ><input type="text"  name="VALUE3[]" value="{{$row->V3_VALUE}}" class="form-control" autocomplete="off" readonly /></td>

                        </td>
                          <td align="center" ><button class="btn add TNC" title="add" data-toggle="tooltip"  type="button" disabled><i class="fa fa-plus"></i></button><button class="btn remove DTNC" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
                        </tr>
                        <tr></tr>

              
                    @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>



        
        <div id="CT" class="tab-pane fade">                                            
          <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:240px; width:72%; margin-top:15px">
<table id="example5" class="display nowrap table table-striped table-bordered itemlist " width="70%" style="height:auto !important;">
                  <thead id="thead1"  style="position: sticky;top: 0">                                     
             <tr>                                                                                         
                 <th>Calculation Component</th> 
                 <th colspan="2" width="20%">Vendor1</th>
                 <th colspan="2" width="20%">Vendor2</th>
                 <th colspan="2" width="20%">Vendor3</th>
                 <th rowspan="2" width="10%">Action</th>
             </tr>
                 <tr>                    
                     <th></th>
                     <th>Rate</th>
                     <th>Amount</th>                                   
                     <th>Rate</th>
                     <th>Amount</th>                         
                     <th>Rate</th>
                     <th>Amount</th>                                  
                 </tr>                                         
         </thead>
                  <tbody id="calc_body">
                  @if(!empty($objList3))
                    @foreach($objList3 as $key => $row)

                    <tr  class="participantRow5{{ $row->QID_REF }}" >

              <td hidden><input type="text" name="VID_REF_CAL[]" id="VID_REF_{{ $row->QID_REF }}" value="{{$row->QID_REF}}" > </td>
              <td hidden><input type="text" name="rowscount3[]"  > </td>

              <td hidden><input type="hidden"  name="VQID_REF_CAL[]"  class="form-control" autocomplete="off" /></td>
              <td hidden><input type="hidden"  name="CTID_REF[]"  value="{{$row->TID_REF}}" class="form-control" autocomplete="off" /></td>
              <td><input type="text"  name="COMPONENT[]"  value="{{$row->COMPONENT}}" class="form-control four-digits"  autocomplete="off"  readonly/></td>

              <td><input type="text" name="RATECAL1[]"  value="{{$row->V1_RATE}}" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
              <td><input type="text" name="AMOUNTCAL1[]" value="{{$row->V1_VALUE}}"  class="form-control four-digits" maxlength="15" autocomplete="off"  readonly/></td>

              <td><input type="text" name="RATECAL2[]"  value="{{$row->V2_RATE}}" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
              <td><input type="text" name="AMOUNTCAL2[]" value="{{$row->V2_VALUE}}"  class="form-control four-digits" maxlength="15" autocomplete="off"  readonly/></td>

              <td><input type="text" name="RATECAL3[]"  value="{{$row->V3_RATE}}" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
              <td><input type="text" name="AMOUNTCAL3[]" value="{{$row->V3_VALUE}}"  class="form-control four-digits" maxlength="15" autocomplete="off"  readonly/></td>



              </td>
              <td hidden style="text-align:center;"><input type="checkbox" class="filter-none" name="calACTUAL_0" id="calACTUAL_0" value=""   ></td>
              <td align="center" ><button class="btn add" title="add" data-toggle="tooltip" type="button" disabled><i class="fa fa-plus"></i></button> <button class="btn remove" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
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
            <button class="btn alertbt"  name='OkBtn1' id="OkBtn1" onclick="getFocus()" style="display:none;margin-left: 90px;">
            <div id="alert-active" class="activeOk1"></div>OK</button>
            <input type="hidden" id="FocusId" >
        </div><!--btdiv-->
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>




<!-- Vendor Dropdown -->
<div id="vendoridpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='vendor_close_popup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Vendor Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="VendorCodeTable" class="display nowrap table  table-striped table-bordered" >
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
        <td class="ROW2"><input type="text" id="vendorcodesearch" class="form-control" autocomplete="off" onkeyup="VendorCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="vendornamesearch" class="form-control" autocomplete="off" onkeyup="VendorNameFunction()"></td>
      </tr>
    </tbody>
    </table>
      <table id="VendorCodeTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2"> 
        </thead>
        <tbody id="tbody_vendor" >
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Vendor Dropdown-->



<!-- Start Vendor Quotation Dropdown -->
<div id="vendor_quotation_popup" class="modal" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-md" style="width:60%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" id="item_closePopup1">&times;</button>
            </div>
            <div class="modal-body">
                <div class="tablename"><p>Vendor Quotation List</p></div>
                <div class="single single-select table-responsive table-wrapper-scroll-y my-custom-scrollbar">
                <input type="hidden" class="mainitem_tab1">
                    <table id="VendorQuotationTable" class="display nowrap table table-striped table-bordered" style="width:100%;">
                        <thead>
                            <tr>
                            <th style="width:10%;">Select</th> 
                                <th style="width:30%;"> Code</th>
                                <th style="width:60%;"> Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                            <th style="text-align:center; width:10%;">&#10004;</th>

                       

                                <td style="width:30%;"> 
                                    <input type="text" id="VendorQuotationcodesearch" class="form-control" autocomplete="off" onkeyup="VendorQuotationCodeFunction()"  />
                                </td>
                                <td style="width:60%;">
                                    <input type="text" id="VendorQuotationnamesearch" class="form-control" autocomplete="off" onkeyup="VendorQuotationNameFunction()"  />
                                </td>
                          
                            </tr>
                        </tbody>
                    </table>
                    <table id="VendorQuotationTable2" class="display nowrap table table-striped table-bordered">
                        <thead id="thead2">
                        <tr>
        
                <td id="item_seach" colspan="4">please wait...</td>
          </tr>
                        </thead>
                        <tbody id="VendorQuotationResult">
                           
                        </tbody>
                    </table>
                </div>
                <div class="cl"></div>
            </div>
        </div>
    </div>
</div>
<!-- End Vendor Quotation Dropdown -->

<!-- Alert -->

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




//Vendor quotation starts here 
function get_vendor_quotation(id){ 


var result=id.split('_');

  var REF= result[1];

  //VID_REF1_popup

  var VID_REF=$("#VID_"+REF).val();    
  var VQID_REF=$("#VQID_"+REF).val();    

  if(VID_REF==''){
  $("#FocusId").val('VID_'+REF+'_popup');
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Pleae select vendor First.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }  
              $("#VendorQuotationResult").html('');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $("#item_seach").show();
                $.ajax({
                    url:'{{route("transaction",[62,"get_vendor_quotations"])}}',
                    type:'POST',
                    data:{'VID_REF':VID_REF,'DYNAMIC_NAME':id},
                    success:function(data) {                                
                      $("#item_seach").hide();
                      $("#VendorQuotationResult").html(data);                    
                      bindVendorQuotationEvent(id);  
                      showSelectedCheck(VQID_REF,id);
                  
                                     
                    },
                    error:function(data){
                      console.log("Error: Something went wrong.");
                      $("#VendorQuotationResult").html('');                        
                    },
                }); 
                //alert(VQID_REF);            
                $("#vendor_quotation_popup").show();                    
}

let machine = "#VendorQuotationTable2";
    let machine2 = "#VendorQuotationTable";
    let machineheaders = document.querySelectorAll(machine2 + " th");

    // Sort the table element when clicking on the table headers
    machineheaders.forEach(function(element, i) {
      element.addEventListener("click", function() {
        w3.sortHTML(machine, ".clsspid_reasoncode1", "td:nth-child(" + (i + 1) + ")");
      });
    });

    function VendorQuotationCodeFunction() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("VendorQuotationcodesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("VendorQuotationTable2");
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

function VendorQuotationNameFunction() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("VendorQuotationnamesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("VendorQuotationTable2");
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


$("#item_closePopup1").click(function(event){
      $("#vendor_quotation_popup").hide();
    });


function bindVendorQuotationEvent(id){
$(".clsspid_vq").click(function(){
var result=id.split('_');  
var VENDOR_TYPE= result[1]; 
var VID_REF=$('#VID_'+VENDOR_TYPE).val();
var fieldid = $(this).attr('id');
var txtval =    $("#txt"+fieldid+"").val();
var texdesc =   $("#txt"+fieldid+"").data("desc");
var texcode =   $("#txt"+fieldid+"").data("code");

$('#'+id+'_popup').val(texdesc);
$('#'+id).val(txtval);  

$('#example2').find('.participantRow'+VID_REF).each(function(){
                var vqid_exist=$.trim($(this).find("[id*=VID_REF]").val());                       
                if(vqid_exist!='' && VID_REF===vqid_exist){  
                  $('.participantRow'+VID_REF).remove();
                }                
                }); 

$('#example3').find('.participantRow3'+VID_REF).each(function(){
                var vqid_exist=$.trim($(this).find("[id*=VID_REF]").val());                       
                if(vqid_exist!='' && VID_REF===vqid_exist){  
                  $('.participantRow3'+VID_REF).remove();
                }                
                }); 
$('#example5').find('.participantRow5'+VID_REF).each(function(){
                var vqid_exist=$.trim($(this).find("[id*=VID_REF]").val());                       
                if(vqid_exist!='' && VID_REF===vqid_exist){  
                  $('.participantRow5'+VID_REF).remove();
                }                
                }); 


      var vqid = txtval;
      if(vqid!=''){    
          $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          });

          $.ajax({
              url:'{{route("transaction",[$FormId,"get_items"])}}',
              type:'POST',
              data:{'id':vqid,'VENDOR_TYPE':VENDOR_TYPE,'VID_REF':VID_REF},
              success:function(data) {
     
                $('#tbody_item').append(data);   
              },
              error:function(data){
                console.log("Error: Something went wrong.");
              //  $('#tbody_item').html('');
              },
          });

          $.ajax({
              url:'{{route("transaction",[$FormId,"get_terms_conditions"])}}',
              type:'POST',
              data:{'id':vqid,'VENDOR_TYPE':VENDOR_TYPE,'VID_REF':VID_REF},
              success:function(data) {

        

                  $('#tncbody').append(data);    
                 
              },
              error:function(data){
                console.log("Error: Something went wrong.");
               // $('#tncbody').html('');
              },
          });

          $.ajax({
              url:'{{route("transaction",[$FormId,"get_calculations_temp"])}}',
              type:'POST',
              data:{'id':vqid,'VENDOR_TYPE':VENDOR_TYPE,'VID_REF':VID_REF},
              success:function(data) {
       

                  $('#calc_body').append(data);       
              },
              error:function(data){
                console.log("Error: Something went wrong.");
               // $('#calc_body').html('');
              },
          });

            
      }
      $("#vendor_quotation_popup").hide()
      event.preventDefault();
});
}





//Vendor Starts
//------------------------

// START VENDOR CODE FUNCTION
let tid = "#VendorCodeTable2";
let tid2 = "#VendorCodeTable";
let headers = document.querySelectorAll(tid2 + " th");

    
headers.forEach(function(element, i) {
element.addEventListener("click", function() {
  w3.sortHTML(tid, ".clsvendorid", "td:nth-child(" + (i + 1) + ")");
});
});

function VendorCodeFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("vendorcodesearch");
  filter = input.value.toUpperCase();
  if(filter.length == 0)
  {
    var CODE = ''; 
    var NAME = ''; 
    loadVendor(CODE,NAME,VENDOR_TYPE); 
  }
  else if(filter.length >= 3)
  {
    var CODE = filter; 
    var NAME = ''; 
    loadVendor(CODE,NAME,VENDOR_TYPE); 
  }
  else
  {
    table = document.getElementById("VendorCodeTable2");
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

function VendorNameFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("vendornamesearch");
  filter = input.value.toUpperCase();
  if(filter.length == 0)
  {
    var CODE = ''; 
    var NAME = ''; 
    loadVendor(CODE,NAME,VENDOR_TYPE);
  }
  else if(filter.length >= 3)
  {
    var CODE = ''; 
    var NAME = filter; 
    loadVendor(CODE,NAME,VENDOR_TYPE);  
  }
  else
  {
    table = document.getElementById("VendorCodeTable2");
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

function loadVendor(CODE,NAME,VENDOR_TYPE){
 
$("#tbody_vendor").html('');
$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});

$.ajax({
  url:'{{route("transaction",[$FormId,"getVendor"])}}',
  type:'POST',
  data:{'CODE':CODE,'NAME':NAME,'VENDOR_TYPE':VENDOR_TYPE},
  success:function(data) {
    $("#tbody_vendor").html(data); 
    bindVendorEvents(VENDOR_TYPE);
    showSelectedCheck($("#"+VENDOR_TYPE).val(),VENDOR_TYPE); 
  },
  error:function(data){
  console.log("Error: Something went wrong.");
  $("#tbody_vendor").html('');                        
  },
});
}

//$('#txtvendor_popup').click(function(event){
function get_vendor(id){

var CODE = ''; 
var NAME = ''; 
loadVendor(CODE,NAME,id);  

$("#vendoridpopup").show();
event.preventDefault();
}
//});

$("#vendor_close_popup").click(function(event){
$("#vendoridpopup").hide();
event.preventDefault();
});





function bindVendorEvents(vendor_type){
var result=vendor_type.split('_');  
var VENDOR_TYPE= result[1]; 
var exist_vid=$("#VID_"+VENDOR_TYPE).val(); 

      $('.clsvendorid').click(function(){    
      //  alert(vendor_type); 
          var id = $(this).attr('id');
          var txtval =    $("#txt"+id+"").val();
          var texdesc =   $("#txt"+id+"").data("desc");


      if(VENDOR_TYPE=='REF1'){
        var VENDOR2=$("#VID_REF2").val();
        var VENDOR3=$("#VID_REF3").val();

        if(txtval===VENDOR2 || txtval===VENDOR3){
              $("#vendoridpopup").hide(); 
              $("#FocusId").val('VID_'+VENDOR_TYPE+'_popup');
              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn1").show();
              $("#AlertMessage").text('Please select a different vendor.');
              $("#alert").modal('show');
              $("#OkBtn1").focus();
              return false; 
              }else{
              $("#vendoridpopup").hide(); 
              if(txtval!='' && exist_vid!=txtval ){ 
              $("#VQID_"+VENDOR_TYPE+'_popup').val(''); 
              $("#VQID_"+VENDOR_TYPE).val(''); 
               $('#example2').find('.participantRow'+exist_vid).each(function(){   
                  $('.participantRow'+exist_vid).remove();
                }); 
                $('#example3').find('.participantRow3'+exist_vid).each(function(){
                $('.participantRow3'+exist_vid).remove();                        
                }); 
                $('#example5').find('.participantRow5'+exist_vid).each(function(){
                $('.participantRow5'+exist_vid).remove();
                }); 
              }
              $('#'+vendor_type+'_popup').val(texdesc);
              $('#'+vendor_type).val(txtval);   
              event.preventDefault();
             }
              }else if(VENDOR_TYPE=='REF2'){
              var VENDOR1=$("#VID_REF1").val();
              var VENDOR3=$("#VID_REF3").val();
              if(txtval===VENDOR1 || txtval===VENDOR3){
                $("#vendoridpopup").hide(); 
                $("#FocusId").val('VID_'+VENDOR_TYPE+'_popup');
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text('Please select a different vendor.');
                $("#alert").modal('show');
                $("#OkBtn1").focus();
                return false; 
                }else{
                $("#vendoridpopup").hide(); 
                if(txtval!='' && exist_vid!=txtval ){ 
                $("#VQID_"+VENDOR_TYPE+'_popup').val(''); 
                $("#VQID_"+VENDOR_TYPE).val(''); 
                $('#example2').find('.participantRow'+exist_vid).each(function(){   
                  $('.participantRow'+exist_vid).remove();
                }); 
                $('#example3').find('.participantRow3'+exist_vid).each(function(){
                $('.participantRow3'+exist_vid).remove();                        
                }); 
                $('#example5').find('.participantRow5'+exist_vid).each(function(){
                $('.participantRow5'+exist_vid).remove();
                }); 
                }
                $('#'+vendor_type+'_popup').val(texdesc);
                $('#'+vendor_type).val(txtval);   
                event.preventDefault();
          }
          }else  if(VENDOR_TYPE=='REF3'){
              var VENDOR1=$("#VID_REF1").val();
              var VENDOR2=$("#VID_REF2").val();
              if(txtval===VENDOR1 || txtval===VENDOR2){
              $("#vendoridpopup").hide(); 
              $("#FocusId").val('VID_'+VENDOR_TYPE+'_popup');
              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn1").show();
              $("#AlertMessage").text('Please select a different vendor.');
              $("#alert").modal('show');
              $("#OkBtn1").focus();
              return false; 
        }else{
            $("#vendoridpopup").hide(); 
            if(txtval!='' && exist_vid!=txtval ){ 
            $("#VQID_"+VENDOR_TYPE+'_popup').val(''); 
            $("#VQID_"+VENDOR_TYPE).val(''); 
            $('#example2').find('.participantRow'+exist_vid).each(function(){   
            $('.participantRow'+exist_vid).remove();
            }); 
            $('#example3').find('.participantRow3'+exist_vid).each(function(){
            $('.participantRow3'+exist_vid).remove();                        
            }); 
            $('#example5').find('.participantRow5'+exist_vid).each(function(){
            $('.participantRow5'+exist_vid).remove();
            }); 
            }
            $('#'+vendor_type+'_popup').val(texdesc);
            $('#'+vendor_type).val(txtval);   
            event.preventDefault();

        }
      }
          $("#vendoridpopup").hide(); 

              event.preventDefault();
      });
}
//Vendor Ends

//------------------------



 

//Sales Quotation comparision Dropdown Ends


</script>

@endpush

@push('bottom-scripts')
<script>



var formTrans = $("#frm_trn_add");
formTrans.validate();

$( "#btnSaveFormData" ).click(function() {
  //var formTrans = $("#frm_trn_add");
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
    var VQC_NO              =   $.trim($("#VQC_NO").val());
    var VQC_DT              =   $.trim($("#VQC_DT").val());
    var VID_REF1            =   $.trim($("#VID_REF1").val());
    var VID_REF2            =   $.trim($("#VID_REF2").val());

    var VQID_REF1           =   $.trim($("#VQID_REF1").val());
    var VQID_REF2           =   $.trim($("#VQID_REF2").val());

    
    var VID_REF       =   $.trim($("#VID_REF").val());

    if(VQC_NO ===""){
        $("#FocusId").val("VQC_NO");        
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please enter value in VQC_NO.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
    }
    else if(VQC_DT ===""){
        $("#FocusId").val("VQC_DT");        
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please select VQC Date.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
    } 
    else if(VID_REF1 ===""){
        $("#FocusId").val("VID_REF1_popup");        
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please select Vendor 1.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;        

    } 
    else if(VQID_REF1 ===""){
        $("#FocusId").val("VQID_REF1_popup");        
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please select Vendor Quotation 1.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;        

    } 
    else if(VID_REF2 ===""){
        $("#FocusId").val("VID_REF2_popup");        
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please select Vendor 2.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;        

    } 
    else if(VQID_REF2 ===""){
        $("#FocusId").val("VQID_REF2_popup");        
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please select Vendor Quotation 2.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;        

    } 
    else if(checkPeriodClosing('{{$FormId}}',$("#VQC_DT").val(),0) ==0){
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
                $("#YesBtn").data("funcname",saveAction);  //set dynamic fucntion name
                $("#OkBtn1").hide();
                $("#OkBtn").hide();
                $("#YesBtn").show();
                $("#NoBtn").show();
                $("#YesBtn").focus();
                highlighFocusBtn('activeYes');        
        

    }

}

$("#YesBtn").click(function(){

$("#alert").modal('hide');
var customFnName = $("#YesBtn").data("funcname");
    window[customFnName]();

}); //yes button

window.fnSaveData = function (){

    //validate and save data
    event.preventDefault();
    var trnsoForm = $("#frm_trn_add");
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

                if(data.errors.VQC_NO){
                    showError('ERROR_VQC_NO',data.errors.VQC_NO);
                            $("#YesBtn").hide();
                            $("#NoBtn").hide();
                            $("#OkBtn1").show();
                            $("#AlertMessage").text('Please enter correct value in VQ NO.');
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

    //validate and save data
    event.preventDefault();
    var trnsoForm = $("#frm_trn_add");
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

                if(data.errors.VQC_NO){
                    showError('ERROR_VQC_NO',data.errors.VQC_NO);
                            $("#YesBtn").hide();
                            $("#NoBtn").hide();
                            $("#OkBtn1").show();
                            $("#AlertMessage").text('Please enter correct value in VQ NO.');
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



//no button
$("#NoBtn").click(function(){
    $("#alert").modal('hide');
});

//ok button
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
    $("#VQC_NO").focus();
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

function showAlert(msg){
  $("#alert").modal('show');
  $("#AlertMessage").text(msg);
  $("#YesBtn").hide(); 
  $("#NoBtn").hide();  
  $("#OkBtn1").show();
  $("#OkBtn1").focus();
  highlighFocusBtn('activeOk');
}

function isNumberDecimalKey(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
    return false;

    return true;
}



$('input[type=checkbox][name=DIRECT_VQ]').change(function() {
      if($("#DIRECT_VQ").prop("checked")) {
          $("#DIRECT_VQ").val('1');
          $("#Row_Count1").val('1');

          $("#Material").html($('#hdnmaterial').val());
         // $("#CT").html($('#hdnct').val());
          
          $('.CLS_RFQ').attr('disabled', 'disabled');

          // $("[id*='RFQ_QTY']").ForceNumericOnly();

          // $('#RFQ_QTY').focusout(function(e){
          //     var cvalue = $(this).val();
          //     if(intRegex.test(cvalue)){
          //       cvalue = cvalue +'.000';
          //         }
          //     $(this).val(cvalue);
          //   });
      }
      else {
        $("#DIRECT_VQ").val('0');
        $("#Row_Count1").val('1');


        $("#Material").html($('#hdnmaterial').val());
       // $("#CT").html($('#hdnct').val());

        $('.CLS_RFQ').removeAttr('disabled');
        // $("[id*='RFQ_QTY']").ForceNumericOnly();
        // $('#RFQ_QTY').focusout(function(e){
        // var cvalue = $(this).val();
        //   if(intRegex.test(cvalue)){
        //     cvalue = cvalue +'.000';
        //       }
        //   $(this).val(cvalue);
        // });
      }      
  });

  function doCalculation(){
    $(".blurRate").blur();
    //$(".blurDISCPER").blur();
    //$(".blurDISCOUNT_AMT").blur();

  }

  function showAlert(msg){
    $("#alert").modal('show');
    $("#AlertMessage").text(msg);
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk');
    return false;
  }



  var lastdt = <?php echo json_encode($objlastVQC_DT[0]->VQC_DT); ?>;
var today = new Date(); 
var current_date = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
$('#VQC_DT').attr('min',lastdt);
$('#VQC_DT').attr('max',current_date);




   

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

</script>


@endpush