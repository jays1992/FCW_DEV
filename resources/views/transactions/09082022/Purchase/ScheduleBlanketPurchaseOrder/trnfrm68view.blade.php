
@extends('layouts.app')
@section('content')

    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('transaction',[68,'index'])}}" class="btn singlebt">Schedule against Blanket<br/> Purchase Order</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
                        <button class="btn topnavbt" id="btnSaveSOS" ><i class="fa fa-save"></i> Save</button>
                        <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt" id="btnUndo"  disabled><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove" disabled><i class="fa fa-lock"></i> Approved</button>
                        <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                        <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                </div>
            </div>
    </div><!--topnav-->	
    <div class="container-fluid purchase-order-view">
    <form id="frm_trn_sos"  method="POST">  
           @csrf
            <div class="container-fluid filter">
                    <div class="inner-form">
                        <div class="row">
                            <div class="col-lg-2 pl"><p>SBP No*</p></div>
                            <div class="col-lg-2 pl">
                                <input {{$ActionStatus}} type="text" name="SOSNO" id="SOSNO" value="{{ isset($objSOS->SBP_NO)?$objSOS->SBP_NO:'' }}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase"  >
                            </div>
                            <div class="col-lg-2 pl"><p>SBP Date*</p></div>
                            <div class="col-lg-2 pl">
                                <input {{$ActionStatus}} type="date" name="SOSDT" id="SOSDT" value="{{ isset($objSOS->SBP_DT)?$objSOS->SBP_DT:''}}" class="form-control mandatory" autocomplete="off" placeholder="dd/mm/yyyy" >
                            </div>                            
                            <div class="col-lg-2 pl"><p>Vendor*</p></div>
                            <div class="col-lg-2 pl">
                            <input {{$ActionStatus}} type="text" name="GLID_popup" id="txtgl_popup" class="form-control mandatory" value="{{ isset($objglcode2->VCODE)?$objglcode2->VCODE:'' }} {{ isset($objglcode2->NAME)?'-'.$objglcode2->NAME:'' }}" autocomplete="off" readonly/>
                            <input type="hidden" name="GLID_REF" id="GLID_REF" class="form-control" autocomplete="off"  value="{{ isset($objSOS->VID_REF)?$objSOS->VID_REF:'' }}" />
                            <input type="hidden" name="hdnmaterial" id="hdnmaterial" class="form-control" autocomplete="off" />
                                <input type="hidden" name="hdnmaterial2" id="hdnmaterial2" class="form-control" autocomplete="off" />
                            </div>                            
                            
                        </div>                 
                        <div class="row">
                            
                            <div class="col-lg-2 pl"><p>BPO No*</p></div>
                            <div class="col-lg-2 pl">
                                <input {{$ActionStatus}} type="text" name="OSOID_popup" id="OSOID_popup" class="form-control mandatory" value="{{ isset($objOSO->BPO_NO)?$objOSO->BPO_NO:'' }}" autocomplete="off" readonly/>
                                <input type="hidden" name="OSOID_REF" id="OSOID_REF" class="form-control" value="{{ isset($objSOS->BPOID_REF)?$objSOS->BPOID_REF:'' }}" autocomplete="off" />
                            </div>   
                            <div class="col-lg-2 pl"><p>Title</p></div>
                            <div class="col-lg-2 pl">
                                <input {{$ActionStatus}} type="text" name="TITLE" id="TITLE" class="form-control" value="{{ isset($objSOS->TITLE)?$objSOS->TITLE:'' }}" maxlength="100" autocomplete="off"/>
                            </div>                           
                        </div>
                    </div>

                    <div class="container-fluid purchase-order-view">

                        <div class="row">
                            <ul class="nav nav-tabs">
                                <li class="active"><a data-toggle="tab" href="#Material">Material</a></li>
                                <li><a data-toggle="tab" href="#Schedule">Schedule</a></li>
                            </ul>
                            <div class="tab-content">
                                <div id="Material" class="tab-pane fade in active">
                                    <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:280px;margin-top:10px;" >
                                        <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                                            <thead id="thead1"  style="position: sticky;top: 0">
                                                <tr>
                                                    <th>Item Code<input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1"></th>
                                                    <th>Item Name</th>
                                                    <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</th>
                                                    <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</th>
                                                    <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</th>
                                                    <th>UoM</th>
                                                    <th>Item Specifications</th>
                                                    <th>Rate Per UoM</th>
                                                    <!-- <th>Action</th> -->
                                                </tr>
                                            </thead>
                                            <tbody id="OSOMaterialBdy">
                                            @if(!empty($objSOSMAT))
                                                @foreach($objSOSMAT as $key => $row)
                                                <tr class="participantRow">
                                                    <td hidden>
                                                        <input  class="form-control" type="hidden" name={{"SOSMATID_".$key}} id ={{"SOSMATID_".$key}} maxlength="100" value="{{ $row->SBPMATID }}" autocomplete="off"   >
                                                    </td>
                                                    <td><input {{$ActionStatus}} type="text" name={{"popupITEMID_".$key}} id={{"popupITEMID_".$key}} class="form-control" value="{{ $row->ICODE }}"  autocomplete="off"  readonly/></td>
                                                    <td hidden><input type="hidden" name={{"ITEMID_REF_".$key}} id={{"ITEMID_REF_".$key}} class="form-control" value="{{ $row->ITEMID_REF }}" autocomplete="off" /></td>
                                                    <td><input {{$ActionStatus}} type="text" name={{"ItemName_".$key}} id={{"ItemName_".$key}} class="form-control" value="{{ $row->ITEM_NAME }}"  autocomplete="off"  readonly/></td>
                                                    <td {{$AlpsStatus['hidden']}} ><input {{$ActionStatus}} type="text" name={{"Alpspartno_".$key}} id={{"Alpspartno_".$key}} class="form-control" value="{{ $row->ALPS_PART_NO }}"  autocomplete="off"  readonly/></td>
                                                    <td {{$AlpsStatus['hidden']}} ><input {{$ActionStatus}} type="text" name={{"Custpartno_".$key}} id={{"Custpartno_".$key}} class="form-control" value="{{ $row->CUSTOMER_PART_NO }}"  autocomplete="off"  readonly/></td>
                                                    <td {{$AlpsStatus['hidden']}} ><input {{$ActionStatus}} type="text" name={{"OEMpartno_".$key}} id={{"OEMpartno_".$key}} class="form-control" value="{{ $row->OEM_PART_NO }}"  autocomplete="off"  readonly/></td>
                                                    <td><input {{$ActionStatus}} type="text" name={{"popupUOM_".$key}} id={{"popupUOM_".$key}} class="form-control"  value="{{ $row->MAIN_UOM_CODE }}" autocomplete="off"  readonly/></td>
                                                    <td hidden><input type="hidden" name={{"UOMID_REF_".$key}} id={{"UOMID_REF_".$key}} class="form-control" value="{{ $row->UOMID_REF }}" autocomplete="off" readonly /></td>
                                                    <td><input {{$ActionStatus}} type="text" name={{"ITEMSPECI_".$key}} id={{"ITEMSPECI_".$key}} class="form-control" maxlength="200" value="{{ $row->ITEMSPECI }}" autocomplete="off" readonly  /></td>
                                                    <td><input {{$ActionStatus}} type="text" name={{"RATEPUOM_".$key}} id={{"RATEPUOM_".$key}} class="form-control five-digits" maxlength="13" value="{{ $row->RATE }}"  autocomplete="off" readonly /></td>
                                                    <!-- <td align="center" ><button class="btn add material" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button><button class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td> -->
                                                </tr><tr></tr>
                                                @endforeach 
                                            @endif 
                                            </tbody>
                                    </table>
                                    </div>	
                                </div>
                                
                                
                                
                                <div id="Schedule" class="tab-pane fade">
                                  <div class="row" style="margin-top:10px;margin-left:3px;" >	
                                    <div class="col-lg-2 pl"><p>Item Code*</p></div>
                                    <div class="col-lg-2 pl">
                                          <input {{$ActionStatus}} type="text" name="SchITEM" id="SchITEM" class="form-control"  value="{{isset($objSOSSCH->ICODE)?$objSOSSCH->ICODE:''}}"  autocomplete="off"  readonly/>
                                          <input type="hidden" name="Sch_ITEMID" id="Sch_ITEMID" class="form-control" value="{{isset($objSOSSCH->ITEMID_REF)?$objSOSSCH->ITEMID_REF:''}}" autocomplete="off" />
                                    </div>
                                    <div class="col-lg-2 pl"><p>Item Name</p></div>
                                    <div class="col-lg-2 pl">
                                          <input {{$ActionStatus}} type="text" name="Sch_Item_Name" id="Sch_Item_Name" class="form-control" value="{{isset($objSOSSCH->ITEM_NAME)?$objSOSSCH->ITEM_NAME:''}}"  autocomplete="off"  readonly/>
                                    </div>
                                    @if(strpos($objCOMPANY->NAME,"ALPS") == false)   
                                      <div class="col-lg-2 pl" hidden><p>Alps Part Number</p></div>
                                      <div class="col-lg-2 pl" hidden>
                                            <input {{$ActionStatus}} type="text" name="Alps_Part_No" id="Alps_Part_No" class="form-control" value="{{isset($objSOSSCH->ALPS_PART_NO)?$objSOSSCH->ALPS_PART_NO:''}}" autocomplete="off"  readonly/>
                                      </div>
                                    @endif
                                  </div>
                                  <div class="row" style="margin-left:3px;" >	
                                  @if(strpos($objCOMPANY->NAME,"ALPS") == false)
                                    <div class="col-lg-2 pl" hidden><p>Customer Part Number</p></div>
                                    <div class="col-lg-2 pl" hidden>
                                          <input {{$ActionStatus}} type="text" name="Cust_Part_No" id="Cust_Part_No" class="form-control" value="{{isset($objSOSSCH->CUSTOMER_PART_NO)?$objSOSSCH->CUSTOMER_PART_NO:''}}"  autocomplete="off"  readonly/>
                                    </div>
                                  @endif
                                    <div class="col-lg-2 pl"><p>UoM</p></div>
                                    <div class="col-lg-2 pl">
                                    <input {{$ActionStatus}} type="text" name="SchUOM" id="SchUOM" class="form-control" value="{{isset($objSOSSCH->MAIN_UOM_CODE)?$objSOSSCH->MAIN_UOM_CODE:''}}"  autocomplete="off"  readonly/>
                                          <input type="hidden" name="Sch_UOMID_REF" id="Sch_UOMID_REF" class="form-control" value="{{isset($objSOSSCH->UOMID_REF)?$objSOSSCH->UOMID_REF:''}}" autocomplete="off" />
                                    </div>
                                    <div class="col-lg-2 pl"><p>Schedule Qty*</p></div>
                                    <div class="col-lg-2 pl">
                                      <input {{$ActionStatus}} type="text" name="Sch_SOQTY" id="Sch_SOQTY" class="form-control three-digits" value="{{isset($total_qty)?$total_qty:''}}" autocomplete="off" />
                                    </div>
                                  </div>
                                  <div class="table-responsive table-wrapper-scroll-y " style="margin-top:10px;height:240px;width:60%;">
                                    <table id="example3" class="display nowrap table table-striped table-bordered itemlist" style="height:auto !important;">
                                      <thead id="thead1"  style="position: sticky;top: 0">
                                        <tr >
                                        <th>Date<input class="form-control" type="hidden" name="Row_Count2" id ="Row_Count2"></th>
                                        <th>Qty</th>
                                        <th>Ship-To (Location)</th>
                                        <th>Action</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                    @if(!empty($objSCHDET))
                                        @foreach($objSCHDET as $skey => $srow)
                                      <tr  class="participantRow3">
                                        <td> <input {{$ActionStatus}} type="date" name={{"SCHDT_".$skey}}  id={{"SCHDT_".$skey}} class="form-control " autocomplete="off" value="{{$srow->DATE}}" placeholder="dd/mm/yyyy" > </td>
                                        <td> <input {{$ActionStatus}} type="text" name={{"SCHQTY_".$skey}} id={{"SCHQTY_".$skey}}   class="form-control three-digits" value="{{number_format(floatVal($srow->QTY),3,'.','') }}" autocomplete="off" /> </td>
                                        <td>
                                          <input {{$ActionStatus}} type="text" name={{"PopupSHIPTO_".$skey}} id={{"PopupSHIPTO_".$skey}} class="form-control"  autocomplete="off" readonly  />
                                          
                                        </td>
                                        <td hidden><input type="hidden" name={{"txtSHIPTO_".$skey}} id={{"txtSHIPTO_".$skey}} class="form-control" value="{{$srow->DELIVERYINS}}"  autocomplete="off" /></td>
                                        <td align="center" >
                                          <button {{$ActionStatus}} class="btn add" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button>
                                          <button {{$ActionStatus}} class="btn remove" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button>
                                        </td>
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
    </div>
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



<!-- GLID Dropdown -->
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
<!-- GLID Dropdown-->



<!-- Sub GL Dropdown -->
{{-- <div id="subglpopup" class="modal" role="dialog"  data-backdrop="static">
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
    <input type="text" id="subglcodesearch" autocomplete="off" onkeyup="SubGLCodeFunction()">
    </td>
    <td>
    <input type="text" id="subglnamesearch" autocomplete="off" onkeyup="SubGLNameFunction()">
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
</div> --}}
<!-- Sub GL Dropdown-->

<!-- OSO Dropdown -->
<div id="OSOpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='OSO_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Blanket Purchase Order</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="OSOTable" class="display nowrap table  table-striped table-bordered" >
    <thead>

    <tr>
      <th class="ROW1">Select</th> 
      <th class="ROW2">BPO No</th>
      <th class="ROW3">BPO Date</th>
    </tr>
    </thead>
    <tbody>

    <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="OSOcodesearch" class="form-control" autocomplete="off" onkeyup="OSOCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="OSOnamesearch" class="form-control" autocomplete="off" onkeyup="OSONameFunction()"></td>
      </tr>
    </tbody>
    </table>
      <table id="OSOTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2">
          
        </thead>
        <tbody id="tbody_OSO">       
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- OSO Dropdown-->


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
      <tr>
            <th style="width:10%;text-align:center;">Select</th>
            <th style="width:15%;">Item Code</th>
            <th style="width:15%;">Name</th>
            <th style="width:15%;">UOM</th>
            <th style="width:15%;">Business Unit</th>
            <th style="width:10%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</th>
            <th style="width:10%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</th>
            <th style="width:10%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</th>
      </tr>
    </thead>
    <tbody>
    <tr>
    <th style="width:10%;"><span style="margin-left: 22px;">&#10004;</span></th>
    <td style="width:15%;"><input type="text" id="Itemcodesearch" class="form-control" autocomplete="off" onkeyup="ItemCodeFunction()"></td>
    <td style="width:15%;"><input type="text" id="Itemnamesearch" class="form-control" autocomplete="off" onkeyup="ItemNameFunction()"></td>
    <td style="width:15%;"><input type="text" id="ItemUOMsearch" class="form-control" autocomplete="off" onkeyup="ItemUOMFunction()"></td>
    <td style="width:15%;"><input type="text" id="ItemBUsearch" class="form-control" autocomplete="off" onkeyup="ItemBUFunction()"></td>
    <td style="width:10%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemAPNsearch" class="form-control" autocomplete="off" onkeyup="ItemAPNFunction()"></td>
    <td style="width:10%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemCPNsearch" class="form-control" autocomplete="off" onkeyup="ItemCPNFunction()"></td>
    <td style="width:10%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemOEMPNsearch" class="form-control" autocomplete="off" onkeyup="ItemOEMPNFunction()"></td>
    </tr>
    </tbody>
    </table>
      <table id="ItemIDTable2" class="display nowrap table  table-striped table-bordered"  style="width:100%;" >
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
    <table id="ShipToTable" class="display nowrap table  table-striped table-bordered" >
    <thead>
    <tr>
        <input type="hidden" name="Field1" id="hdnshipfield1">
        <input type="hidden" name="Field2" id="hdnshipfield2">
    </tr>
    <tr>
      <th class="ROW1">Select</th> 
      <th class="ROW2">Name</th>
      <th class="ROW3">Address</th>
    </tr>
    </thead>
    <tbody>

    <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="ShipTocodesearch" class="form-control" autocomplete="off" onkeyup="ShipToCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="ShipTonamesearch" class="form-control" autocomplete="off" onkeyup="ShipToNameFunction()"></td>
      </tr>

    </tbody>
    </table>
      <table id="ShipToTable2" class="display nowrap table  table-striped table-bordered" >
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

@endsection


@push('bottom-css')
<style>
#custom_dropdown, #frm_trn_sos_filter {
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

.singlebt {
padding: 7px 6px;
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


//------------------------
  // START VENDOR CODE FUNCTION
let vendor_tid = "#VendorCodeTable2";
let vendor_tid2 = "#VendorCodeTable";
let vendor_headers = document.querySelectorAll(vendor_tid2 + " th");

      
vendor_headers.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(vendor_tid, ".clsvendorid", "td:nth-child(" + (i + 1) + ")");
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
      loadVendor(CODE,NAME); 
    }
    else if(filter.length >= 3)
    {
      var CODE = filter; 
      var NAME = ''; 
      loadVendor(CODE,NAME); 
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
      loadVendor(CODE,NAME);
    }
    else if(filter.length >= 3)
    {
      var CODE = ''; 
      var NAME = filter; 
      loadVendor(CODE,NAME);  
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

function loadVendor(CODE,NAME){
   
  $("#tbody_vendor").html('');
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $.ajax({
    url:'{{route("transaction",[68,"getVendor"])}}',
    type:'POST',
    data:{'CODE':CODE,'NAME':NAME},
    success:function(data) {
      $("#tbody_vendor").html(data); 
      bindVendorEvents();
      showSelectedCheck($("#VID_REF").val(),"SELECT_VID_REF"); 
    },
    error:function(data){
    console.log("Error: Something went wrong.");
    $("#tbody_vendor").html('');                        
    },
  });
}

$('#txtgl_popup').click(function(event){
  

  var CODE = ''; 
  var NAME = ''; 
  loadVendor(CODE,NAME);  

  $("#vendoridpopup").show();
  event.preventDefault();
});

$("#vendor_close_popup").click(function(event){
  $("#vendoridpopup").hide();
  event.preventDefault();
}); 



function bindVendorEvents(){

  $(".clsvendorid").click(function(){
        
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");
        
        
        $('#txtgl_popup').val(texdesc);
        $('#GLID_REF').val(txtval);

        $("#vendoridpopup").hide();
        $("#vendorcodesearch").val(''); 
        $("#vendornamesearch").val(''); 
     

        $('#OSOID_popup').val('');
        $('#OSOID_REF').val('');

        
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

          clearSchedule();
       
        event.preventDefault();
      });


}



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
      //sub GL
      var customid = $('#GLID_REF').val();
        if(customid!=''){
          $('#tbody_subglacct').html('<tr><td colspan="2">Please wait..</td></tr>');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url:'{{route("transaction",[68,"getsubledger"])}}',
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
        ////sub GL end
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
            var MaterialClone = $('#hdnmaterial').val();
            var ScheduleClone = $('#hdnmaterial2').val();
            $("#txtsubgl_popup").val(texdesc);
            $("#txtsubgl_popup").blur();
            $("#SLID_REF").val(txtval);
            if (txtval != oldSLID)
            {
              $('#Material').html(MaterialClone);
              var count11 = <?php echo json_encode($objCount1); ?>;
              $('#Row_Count1').val(count11);
              $('#OSOID_popup').val('');
              $('#OSOID_REF').val('');
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
              $('#Schedule').find('.participantRow3').each(function(){
                $(this).find('input:text').val('');
                var rowcount2 = $('#Row_Count2').val();
                if(rowcount2 > 1)
                {
                  $(this).closest('.participantRow3').remove();
                  rowcount2 = parseInt(rowcount2) - 1;
                  $('#Row_Count2').val(rowcount2);
                }
              });
              $('#SchITEM').val('');
              $('#Sch_ITEMID').val('');
              $('#Sch_Item_Name').val('');
              $('#SchUOM').val('');
              $('#Sch_UOMID_REF').val('');
              $('#Sch_SOQTY').val('');
              var d = new Date(); 
                var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
                $('#Schedule').find('[id*="SCHDT"]').val(today);
                $('#Schedule').find('[id*="SCHDT"]').attr('min',today);
            }
            $("#subglpopup").hide();
            $("#subglcodesearch").val(''); 
            $("#subglnamesearch").val(''); 
           
            event.preventDefault();
        });
  }
//Sub GL Account Ends
//------------------------

//OSO Starts
//------------------------

let OSOtid = "#OSOTable2";
      let OSOtid2 = "#OSOTable";
      let OSOheaders = document.querySelectorAll(OSOtid2 + " th");

      // Sort the table element when clicking on the table headers
      OSOheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(OSOtid, ".clsOSO", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function OSOCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("OSOcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("OSOTable2");
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

  function OSONameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("OSOnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("OSOTable2");
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

$("#OSOID_popup").click(function(event){
     
      var customid = $('#GLID_REF').val();
        if(customid!=''){
          $('#tbody_OSO').html('<tr><td colspan="2">Please wait..</td></tr>');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url:'{{route("transaction",[68,"getBPO"])}}',
                type:'POST',
                data:{'id':customid},
                success:function(data) {
                    $('#tbody_OSO').html(data);
                    bindBlanketPurchaseOrder();
                    showSelectedCheck($("#OSOID_REF").val(),"SELECT_OSOID_REF");
                },
                error:function(data){
                  console.log("Error: Something went wrong.");
                  $('#tbody_OSO').html('');
                },
            });        
        }
      
     $("#OSOpopup").show();
     event.preventDefault();
  });

$("#OSO_closePopup").on("click",function(event){ 
    $("#OSOpopup").hide();
    event.preventDefault();
});
function bindBlanketPurchaseOrder(){
        $('.clsOSO').click(function(){
    
            var id = $(this).attr('id');
            var txtval =    $("#txt"+id+"").val();
            var texdesc =   $("#txt"+id+"").data("desc");
            var MaterialClone = $('#hdnmaterial').val();
            var ScheduleClone = $('#hdnmaterial2').val();
            $("#OSOID_popup").val(texdesc);
            $("#OSOID_popup").blur();
            $("#OSOID_REF").val(txtval);
            
            var customid = txtval;
              if(customid!=''){
                // $('#OSOMaterialBdy').html('');

                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  });
                  $.ajax({
                      url:'{{route("transaction",[68,"getBPOMaterial"])}}',
                      type:'POST',
                      data:{'id':customid},
                      success:function(data) {
                         // $('#OSOMaterialBdy').html('');
                          $('#OSOMaterialBdy').html(data);
                          // $('#SchITEM').val('');
                          // $('#Sch_ITEMID').val('');
                          // $('#SchUOM').val('');
                          // $('#Sch_UOMID_REF').val('');
                          // $('#Sch_SOQTY').val('');
                          //$('#Schedule').html(ScheduleClone);
                          //$('#Row_Count1').val('1');
                          //$('#Row_Count2').val('1');
                          // var d = new Date(); 
                          // var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
                          // $('#Schedule').find('[id*="SCHDT"]').val(today);
                          // $('#Schedule').find('[id*="SCHDT"]').attr('min',today);
                          clearSchedule();

                          event.preventDefault();
                      },
                      error:function(data){
                        console.log("Error: There is no Item Available.");
                        $('#OSOMaterialBdy').html(MaterialClone);
                        $('#Row_Count1').val('1');

                        clearSchedule();

                        // $('#SchITEM').val('');
                        // $('#Sch_ITEMID').val('');
                        // $('#SchUOM').val('');
                        // $('#Sch_UOMID_REF').val('');
                        // $('#Sch_SOQTY').val('');
                        // $('#Schedule').html(ScheduleClone);
                        //  $('#Row_Count2').val('1');

                        var d = new Date(); 
                        var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
                        
                        $('#Schedule').find('[id*="SCHDT"]').val(today);
                        $('#Schedule').find('[id*="SCHDT"]').attr('min',today);
                      },
                  });
                  $.ajax({
                      url:'{{route("transaction",[68,"getBPOMaterial2"])}}',
                      type:'POST',
                      data:{'id':customid},
                      success:function(data) {
                          $('#Row_Count1').val(data);
                      },
                      error:function(data){
                        $('#Row_Count1').val('1');
                      },
                  });        
              }
            $("#OSOpopup").hide();
            $("#OSOcodesearch").val(''); 
            $("#OSOnamesearch").val(''); 
           
            event.preventDefault();
        });
  }
//OSO Ends
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

  $('#Schedule').on('click','#SchITEM', function(event){
        var OSOID = $('#OSOID_REF').val();
                $("#tbody_ItemID").html('');
                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  });
                  $.ajax({
                      url:'{{route("transaction",[68,"getItemDetails"])}}',
                      type:'POST',
                      data:{'id':OSOID},
                      success:function(data) {
                        $("#tbody_ItemID").html(data);    
                        bindItemEvents();   
                        showSelectedCheck($("#Sch_ITEMID").val(),"SELECT_Sch_ITEMID");
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                        $("#tbody_ItemID").html('');                        
                      },
                  }); 
        $("#ITEMIDpopup").show();        
        event.preventDefault();
      });

      $("#ITEMID_closePopup").click(function(event){
        $("#ITEMIDpopup").hide();
      });
      

    function bindItemEvents(){
      $('.clsitemid').click(function(){
        var fieldid = $(this).attr('id');
        var txtval =   $("#txt"+fieldid+"").val();
        var texdesc =  $("#txt"+fieldid+"").data("desc");
        var fieldid2 = $(this).parent().parent().children('[id*="itemname"]').attr('id');
        var txtname =  $("#txt"+fieldid2+"").val();
        var txtspec =  $("#txt"+fieldid2+"").data("desc");
        var fieldid3 = $(this).parent().parent().children('[id*="itemuom"]').attr('id');
        var txtmuomid =  $("#txt"+fieldid3+"").val();
        var txtmuom =  $("#txt"+fieldid3+"").data("desc");
        
        $('#SchITEM').val(texdesc);
        $('#Sch_ITEMID').val(txtval);

        $('#Sch_Item_Name').val(txtname);

        $('#SchUOM').val(txtmuom);
        $('#Sch_UOMID_REF').val(txtmuomid);
        
        $("#ITEMIDpopup").hide();
        $("#Itemcodesearch").val(''); 
        $("#Itemnamesearch").val(''); 
        $("#ItemUOMsearch").val(''); 
     
        event.preventDefault();
      });
    }
  //Item ID Dropdown Ends
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

  $('#Schedule').on('click','[id*="PopupSHIPTO"]',function(event){
    var customid = $('#GLID_REF').val();

    var fieldid = $(this).parent().parent().find('[id*="txtSHIPTO"]').attr('id');
    
    if(customid!=''){
          $("#tbody_ShipTo").html('Loading...');
          $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          });
          $.ajax({
              url:'{{route("transaction",[68,"getShipAddress"])}}',
              type:'POST',
              data:{'id':customid,'fieldid':fieldid},
              success:function(data) {
                $("#tbody_ShipTo").html(data);       
                BindShipAddress();   
                showSelectedCheck($("#"+fieldid).val(),"SELECT_"+fieldid);         
              },
              error:function(data){
                console.log("Error: Something went wrong.");
                $("#tbody_ShipTo").html('');
              },
          });
    } 

    //clear 
    $('#'+$(this).attr('id')).val('');
    $('#'+$(this).parent().parent().find('[id*="txtSHIPTO"]').attr('id')).val('');

    $('#hdnshipfield1').val($(this).attr('id'));
    $('#hdnshipfield2').val($(this).parent().parent().find('[id*="txtSHIPTO"]').attr('id'));
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
          var texdesc =   $(this).parent().parent().children('[id*="txtshipadd"]').text().trim();

          var id= $('#hdnshipfield1').val();
          var id2= $('#hdnshipfield2').val();
          
          $('#'+id).val(texdesc);
          $('#'+id2).val(txtval);
          $("#ShipTopopup").hide();
          $("#ShipTocodesearch").val(''); 
          $("#ShipTonamesearch").val(''); 
           
          event.preventDefault();
        });
      }
  //Ship Address Ends
//------------------------

$(document).ready(function(e) {
    var Material = $("#Material").html();
    var Schedule = $("#Schedule").html(); 
    $('#hdnmaterial').val(Material);
    $('#hdnmaterial2').val(Schedule);
    var count1 = <?php echo json_encode($objCount1); ?>;
    var count3 = <?php echo json_encode($objCount3); ?>;    
    $('#Row_Count1').val(count1);
    $('#Row_Count2').val(count3);
    var obj = <?php echo json_encode($objSOSMAT); ?>;
    var objsch = <?php echo json_encode($objSOSSCH); ?>;
    var schdet = <?php echo json_encode($objSCHDET); ?>;
    var item = <?php echo json_encode($objItems); ?>;
    var uom = <?php echo json_encode($objUOM); ?>;
    $.each( obj, function( key, value ) {
        var itemid = value.ITEMID_REF;
        $.each( item, function( ikey, ivalue ) {
        if(itemid == ivalue.ITEMID)
        {
            $('#popupITEMID_'+key).val(ivalue.ICODE);
            $('#ItemName_'+key).val(ivalue.NAME);
        }
        });
        $.each( uom, function( umkey, umvalue ) {
            if(value.UOMID_REF == umvalue.UOMID)
            {
                $('#popupUOM_'+key).val(umvalue.UOMCODE+'-'+umvalue.DESCRIPTIONS);
            }
        });
    });
    var schitemid = $('#Sch_ITEMID').val();
    $.each( item, function( ikey, ivalue ) {
    if(schitemid == ivalue.ITEMID)
    {
        $('#SchITEM').val(ivalue.ICODE);
        $('#Sch_Item_Name').val(ivalue.NAME);
    }
    });
    var schuomid = $('#Sch_UOMID_REF').val();
    $.each( uom, function( umkey, umvalue ) {
    if(schuomid == umvalue.UOMID)
        {
            $('#SchUOM').val(umvalue.UOMCODE+'-'+umvalue.DESCRIPTIONS);
        }
    });

    $('#example3').find('.participantRow3').each(function(){
        var id = $.trim($(this).find('[id*="txtSHIPTO_"]').val());
        var txtid = $(this).find('[id*="PopupSHIPTO"]').attr('id');
        if(id!=''){
            $(this).find('[id*="PopupSHIPTO_"]').val('');
          $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          });
          $.ajax({
              url:'{{route("transaction",[68,"getShipAddressIDwise"])}}',
              type:'POST',
              data:{'id':id},
              success:function(data) {
                $('#'+txtid).val(data);                 
              },
              error:function(data){
                console.log("Error: Something went wrong.");
                $('#'+txtid).val('');
              },
          });
    }

    });

    
    var d = new Date(); 
    var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
    // $('#SOSDT').val(today);
    // $('[id*="SCHDT"]').val(today);
    $('[id*="SCHDT"]').attr('min',today);
    

    $('#Material').on('focusout',"[id*='RATEPUOM']",function()
    {
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.00000')
      }
      event.preventDefault();
    });

    $('#Schedule').on('focusout','#Sch_SOQTY',function()
    {
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.000')
      }
      event.preventDefault();
    }); 

    $('#Schedule').on('focusout',"[id*='SCHQTY']",function()
    {
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.000')
      }
      event.preventDefault();
    });  

    $('#btnAdd').on('click', function() {
        var viewURL = '{{route("transaction",[68,"add"])}}';
                  window.location.href=viewURL;
    });
    $('#btnExit').on('click', function() {
      var viewURL = '{{route('home')}}';
                  window.location.href=viewURL;
    });
    //to check the label duplicacy
     $('#SOSNO').focusout(function(){
      var SOSNO   =   $.trim($(this).val());
      if(SOSNO ===""){
                $("#FocusId").val('SOSNO');
                $("#ProceedBtn").focus();
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text('Please enter value in SONO.');
                $("#alert").modal('show');
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk1');
            } 
});
//SOS Date Check
var lastosodt = <?php echo json_encode(isset($objlastSOSDT[0]->SBP_DT)?$objlastSOSDT[0]->SBP_DT:''); ?>;
var today = new Date(); 
var sosdate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
$('#SOSDT').attr('min',lastosodt);
$('#SOSDT').attr('max',sosdate);


    $("#example3").on('click', '.add', function() {
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
        $clone.find('[id*="txtSHIPTO"]').val('');
        var d = new Date(); 
        var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
        $clone.find('[id*="SCHDT"]').val(today);
        $clone.find('[id*="SCHDT"]').attr('min',today);  
        $tr.closest('table').append($clone);         
        var rowCount2 = $('#Row_Count2').val();
		    rowCount2 = parseInt(rowCount2)+1;
        $('#Row_Count2').val(rowCount2);
        $('.remove').removeAttr('disabled');
        event.preventDefault();
    });
    $("#example3").on('click', '.remove', function() {
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
    

      


    $("#btnUndo").on("click", function() {
        $("#AlertMessage").text("Do you want to erase entered information in this record?");
        $("#alert").modal('show');
        $("#YesBtn").data("funcname","fnUndoYes");
        $("#YesBtn").show();
        $("#NoBtn").data("funcname","fnUndoNo");
        $("#NoBtn").show();
        $("#OkBtn").hide();
        $("#OkBtn1").hide();
        $("#NoBtn").focus();
    });

    

    window.fnUndoYes = function (){
      window.location.reload();
   }//fnUndoYes


   window.fnUndoNo = function (){
     
   }//fnUndoNo
 
});
</script>

@endpush

@push('bottom-scripts')
<script>

$(document).ready(function() {

    $('#frm_trn_sos1').bootstrapValidator({
       
        fields: {
            txtlabel: {
                validators: {
                    notEmpty: {
                        message: 'The SOS NO is required'
                    }
                }
            },            
        },
        submitHandler: function(validator, form, submitButton) {
            alert( "Handler for .submit() called." );
             event.preventDefault();
             $("#frm_trn_sos").submit();
        }
    });
});

var formSalesOrder = $("#frm_trn_sos");
    formSalesOrder.validate();

$( "#btnSaveSOS" ).click(function() {
    if(formSalesOrder.valid()){ 
      validateForm("fnSaveData");
    }
});


$( "#btnApprove" ).click(function() { 
 if(formSalesOrder.valid()){
   validateForm("fnApproveData");
 }
});

function validateForm(saveAction)
{
 
  $("#FocusId").val('');
    var SOSNO          =   $.trim($("#SOSNO").val());
    var SOSDT          =   $.trim($("#SOSDT").val());
    var GLID_REF       =   $.trim($("#GLID_REF").val());
    var SLID_REF       =   $.trim($("#SLID_REF").val());
    var OSOID_REF      =   $.trim($("#OSOID_REF").val());
    var Sch_ITEMID     =   $.trim($("#Sch_ITEMID").val());
    var Sch_SOQTY      =   $.trim($("#Sch_SOQTY").val());
    var attachcount    =   $.trim($("#hdnattachment").val());

if(attachcount ==="0"){
    $("#FocusId").val($("#SONO"));
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please attach the Document before approve the Schedule Against Blanket Purchase Order.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
}
else if(SOSNO ===""){
        $("#FocusId").val($("#SOSNO"));
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please enter value in SBP No.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
    }
    else if(SOSDT ===""){
        $("#FocusId").val($("#SOSDT"));
        $("#SOSDT").val(today);  
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please select SBP Date.');
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
        $("#AlertMessage").text('Please select Vendor.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
    } 
    else if(OSOID_REF ===""){
        $("#FocusId").val($("#OSOID_REF"));
        $("#OSOID_REF").val('');  
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please select BPO No.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
    }
    else if(Sch_ITEMID ===""){
        $("#FocusId").val($("#Sch_ITEMID"));
        $("#Sch_ITEMID").val('');  
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please select Item in Schedule Tab');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
    }
    else if(Sch_SOQTY ===""){
        $("#FocusId").val($("#Sch_SOQTY"));
        $("#Sch_SOQTY").val('');  
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please Enter Schedule Quantity in Schedule Tab');
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
        var totalquantity = '0.000';
        var arrdate=[];

            // $('#udfforsebody').find('.form-control').each(function () {
            $('#example2').find('.participantRow').each(function(){
                if($.trim($(this).find("[id*=ITEMID_REF]").val())!="")
                {
                    allblank.push('true');
                }
                else
                {
                    allblank.push('false');
                }
            });
            
            if($('#Sch_ITEMID').val() !="" && $('#Sch_SOQTY').val() !="")
            {
                $('#Schedule').find('.participantRow3').each(function(){
                  if($.trim($(this).find("[id*=SCHDT]").val())!="")
                  {
                      allblank6.push('true');
                      arrdate.push($.trim($(this).find("[id*=SCHDT]").val()));
                  }
                  else
                  {
                      allblank6.push('false');
                  }
                  if($.trim($(this).find('[id*="SCHQTY"]').val()) != "" && $.trim($(this).find('[id*="SCHQTY"]').val()) > "0.000")
                  {
                    allblank7.push('true');
                  }
                  else
                  {
                    allblank7.push('false');
                  }
                  if($.trim($(this).find('[id*="txtSHIPTO"]').val()) != "")
                  {
                    allblank8.push('true');
                  }
                  else
                  {
                    allblank8.push('false');
                  }
                  
                  tvalue = $(this).find('[id*="SCHQTY"]').val();
                  totalquantity = parseFloat(totalquantity) + parseFloat(tvalue);
                  totalquantity = parseFloat(totalquantity).toFixed(3); 
                });

                var tquantity = parseFloat($('#Sch_SOQTY').val());
                  if(totalquantity != tquantity)
                  {
                    allblank4.push('false');
                  }
                  else
                  {
                    allblank4.push('true');
                  }
            }
            
            if(jQuery.inArray("true", allblank6) !== -1)
            {
                //--- check duplicate date
                  var map = {};
                  var result = false;
                  for(var i = 0; i < arrdate.length; i++) {
                    // check if object contains entry with this element as key
                    if(map[arrdate[i]]) {
                        result = true;
                        break;
                    }
                    map[arrdate[i]] = true;
                  }
                  if(result) {
                    $("#alert").modal('show');
                    $("#AlertMessage").text('Date can not be duplicate in Schedule Tab.');
                    $("#YesBtn").hide(); 
                    $("#NoBtn").hide();  
                    $("#OkBtn1").show();
                    $("#OkBtn1").focus();
                    highlighFocusBtn('activeOk');
                    return false;
                  } 
                //--- check duplicate date  end

                //--- date comparision begin
                  var result2 = false;
                  var dt1='';
                  var dt2='';
                  var j=0;
                  for(var i = 0; i < arrdate.length; i++) {
                    if(i<arrdate.length){
                      dt1 = arrdate[i];
                      j = i+1;
                      dt2 = arrdate[j];
                      if(new Date(dt1)>new Date(dt2) ) {
                        result2 = true;
                        break;
                      }
                    }
                  }
                  if(result2) {
                    $("#alert").modal('show');
                    $("#AlertMessage").text('Date should be entered in descending order in Schedule Tab.');
                    $("#YesBtn").hide(); 
                    $("#NoBtn").hide();  
                    $("#OkBtn1").show();
                    $("#OkBtn1").focus();
                    highlighFocusBtn('activeOk');
                    return false;
                  } 
                //---  //--- date comparision end
            }        

            if(jQuery.inArray("false", allblank) !== -1){
                    $("#alert").modal('show');
                    $("#AlertMessage").text('Please select item in Material Tab.');
                    $("#YesBtn").hide(); 
                    $("#NoBtn").hide();  
                    $("#OkBtn1").show();
                    $("#OkBtn1").focus();
                    highlighFocusBtn('activeOk');
                    return false;
                }
                else if(jQuery.inArray("false", allblank2) !== -1){
                  $("#alert").modal('show');
                  $("#AlertMessage").text('UOM is missing in Material Tab.');
                  $("#YesBtn").hide(); 
                  $("#NoBtn").hide();  
                  $("#OkBtn1").show();
                  $("#OkBtn1").focus();
                  highlighFocusBtn('activeOk');
                  return false;
                }            
                else if(jQuery.inArray("false", allblank3) !== -1){
                  $("#alert").modal('show');
                  $("#AlertMessage").text('Please enter Rate per UOM in Material Tab.');
                  $("#YesBtn").hide(); 
                  $("#NoBtn").hide();  
                  $("#OkBtn1").show();
                  $("#OkBtn1").focus();
                  highlighFocusBtn('activeOk');
                  return false;
                }
                else if(jQuery.inArray("false", allblank6) !== -1){
                  $("#alert").modal('show');
                  $("#AlertMessage").text('Date cannot be blank in Schedule Tab.');
                  $("#YesBtn").hide(); 
                  $("#NoBtn").hide();  
                  $("#OkBtn1").show();
                  $("#OkBtn1").focus();
                  highlighFocusBtn('activeOk');
                  return false;
                }
                else if(jQuery.inArray("false", allblank7) !== -1){
                  $("#alert").modal('show');
                  $("#AlertMessage").text('Quantity must be greater than Zero in Schedule Tab.');
                  $("#YesBtn").hide(); 
                  $("#NoBtn").hide();  
                  $("#OkBtn1").show();
                  $("#OkBtn1").focus();
                  highlighFocusBtn('activeOk');
                  return false;
                }
                else if(jQuery.inArray("false", allblank8) !== -1){
                  $("#alert").modal('show');
                  $("#AlertMessage").text('Select Ship To Address in Schedule Tab.');
                  $("#YesBtn").hide(); 
                  $("#NoBtn").hide();  
                  $("#OkBtn1").show();
                  $("#OkBtn1").focus();
                  highlighFocusBtn('activeOk');
                  return false;
                }
                else if(jQuery.inArray("false", allblank4) !== -1){
                  $("#alert").modal('show');
                  $("#AlertMessage").text('Quantity entered in grid is not equal to Schedule Quantity in Schedule Tab.');
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
                    $("#YesBtn").data("funcname",saveAction);  //set dynamic fucntion name
                    $("#YesBtn").show();
                    $("#NoBtn").show();
                    $("#YesBtn").focus();
                    $("#OkBtn").hide();
                    $("#OkBtn1").hide();
                    highlighFocusBtn('activeYes');
                }
          }

} //validateForm

$("#YesBtn").click(function(){
  $("#alert").modal('hide');
  var customFnName = $("#YesBtn").data("funcname");
      window[customFnName]();

}); //yes button

window.fnSaveData = function (){

//validate and save data
     var trnosoForm = $("#frm_trn_sos");
    var formData = trnosoForm.serialize();
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$.ajax({
    url:'{{ route("transactionmodify",[68,"update"])}}',
    type:'POST',
    data:formData,
    success:function(data) {
       
        if(data.errors) {
            $(".text-danger").hide();
            if(data.errors.SOSNO){
                showError('ERROR_SOSNO',data.errors.SBP_NO);
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text('Please enter correct value in SBP No.');
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
     var trnosoForm = $("#frm_trn_sos");
    var formData = trnosoForm.serialize();
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$.ajax({
    url:'{{ route("transactionmodify",[68,"Approve"])}}',
    type:'POST',
    data:formData,
    success:function(data) {
       
        if(data.errors) {
            $(".text-danger").hide();
            if(data.errors.SOSNO){
                showError('ERROR_SOSNO',data.errors.SBP_NO);
                        $("#YesBtn").hide();
                        $("#NoBtn").hide();
                        $("#OkBtn1").show();
                        $("#AlertMessage").text('Please enter correct value in SBP.');
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
    window.location.href = '{{route("transaction",[68,"index"]) }}';
});

$("#OkBtn1").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn").hide();
    $("#OkBtn1").hide();
    $("#"+$(this).data('focusname')).focus();
    $("#SOSNO").focus();
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

function clearSchedule()
{

    $('#SchITEM').val('');
    $('#Sch_ITEMID').val('');
    $('#Sch_Item_Name').val('');
    $('#SchUOM').val('');
    $('#Sch_UOMID_REF').val('');
    $('#Sch_SOQTY').val('');
    $('#TITLE').val('');


    $('#example3').find('.participantRow3').each(function(){
      var rowcount3 = $(this).closest('table').find('.participantRow3').length;
      $(this).find('input:text').val('');
      $(this).find('input:hidden').val('');
      if(rowcount3 > 1)
      {
        $(this).closest('.participantRow3').remove();
        rowcount3 = parseInt(rowcount3) - 1;
        $('#Row_Count2').val(rowcount3);
      }
    });

    var d = new Date(); 
    var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
    $('#Schedule').find('[id*="SCHDT"]').val(today);
    //$('#Schedule').find('[id*="SCHDT"]').attr('min',today);
  }

  $( function()
  {
    $('#Schedule').on('keyup','.three-digits',function(){
      if($(this).val().indexOf('.')!=-1){         
          if($(this).val().split(".")[1].length > 3){                
          $(this).val('');
          $("#alert").modal('show');
          $("#AlertMessage").text('Enter value till three decimal only.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          $("#OkBtn").hide();
          highlighFocusBtn('activeOk1');
          }  
        }            
        return this; //for chaining
    });
  });

</script>


@endpush