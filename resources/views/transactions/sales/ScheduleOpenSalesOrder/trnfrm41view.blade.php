
@extends('layouts.app')
@section('content')

    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('transaction',[41,'index'])}}" class="btn singlebt">Schedule against Open <br/>Sales Order</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
                        <button class="btn topnavbt" id="btnSaveSOS" disabled="disabled"><i class="fa fa-floppy-o"></i> Save</button>
                        <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt" id="btnUndo"  disabled="disabled"><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-thumbs-o-up"></i> Approved</button>
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
                            <div class="col-lg-1 pl"><p>SOS No</p></div>
                            <div class="col-lg-2 pl">
                                <input {{$InputStatus}} type="text" name="SOSNO" id="SOSNO" value="{{ isset($objSOS->SOSNO)?$objSOS->SOSNO:'' }}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase" autofocus >
                            </div>
                            <div class="col-lg-1 pl"><p>SOS Date</p></div>
                            <div class="col-lg-2 pl">
                                <input {{$InputStatus}} type="date" name="SOSDT" id="SOSDT" value="{{ isset($objSOS->SOSDT)?$objSOS->SOSDT:'' }}" class="form-control mandatory" autocomplete="off" placeholder="dd/mm/yyyy" >
                            </div>  

                            <div class="col-lg-1 pl"><p>Customer</p></div>
                            <div class="col-lg-2 pl">
                                  <input {{$InputStatus}} type="text" name="SubGl_popup" id="txtgl_popup" value="{{ isset($objsubglcode->SGLCODE)?$objsubglcode->SGLCODE:'' }} {{ isset($objsubglcode->SLNAME)?'-'.$objsubglcode->SLNAME:'' }}" class="form-control mandatory"  autocomplete="off" readonly/>
                                  <input type="hidden" name="GLID_REF" id="GLID_REF" value="{{ isset($objSOS->GLID_REF)?$objSOS->GLID_REF:'' }}" class="form-control" autocomplete="off" />
                                  <input type="hidden" name="SLID_REF" id="SLID_REF"  value="{{ isset($objSOS->SLID_REF)?$objSOS->SLID_REF:'' }}" class="form-control" autocomplete="off" />
                                  <input type="hidden" name="hdnmaterial" id="hdnmaterial" class="form-control" autocomplete="off" />
                                <input type="hidden" name="hdnmaterial2" id="hdnmaterial2" class="form-control" autocomplete="off" />
                                <input type="hidden" name="hdnmaterial3" id="hdnmaterial3" class="form-control" autocomplete="off" />
                            </div>
                                                      
                            <div class="col-lg-1 pl"><p>OSO No</p></div>
                            <div class="col-lg-2 pl">
                                <input {{$InputStatus}} type="text" name="OSOID_popup" id="OSOID_popup" class="form-control mandatory" value="{{ isset($objOSO->OSONO)?$objOSO->OSONO:'' }}" autocomplete="off" readonly/>
                                <input type="hidden" name="OSOID_REF" id="OSOID_REF" class="form-control" value="{{ isset($objSOS->OSOID_REF)?$objSOS->OSOID_REF:'' }}" autocomplete="off" />
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
                                                        <input  class="form-control" type="hidden" name={{"SOSMATID_".$key}} id ={{"SOSMATID_".$key}} maxlength="100" value="{{ $row->SOSMATID }}" autocomplete="off"   >
                                                    </td>
                                                    <td><input {{$InputStatus}} type="text" name={{"popupITEMID_".$key}} id={{"popupITEMID_".$key}} class="form-control"  value="{{ $row->ICODE }}" autocomplete="off"  readonly/></td>
                                                    <td hidden><input type="hidden" name={{"ITEMID_REF_".$key}} id={{"ITEMID_REF_".$key}} class="form-control" value="{{ $row->ITEMID_REF }}" autocomplete="off" /></td>
                                                    <td><input {{$InputStatus}} type="text" name={{"ItemName_".$key}} id={{"ItemName_".$key}} class="form-control" value="{{ $row->NAME }}"  autocomplete="off"  readonly/></td>
                                                    
                                                    <td {{$AlpsStatus['hidden']}}><input {{$InputStatus}} type="text" name="Alpspartno_{{$key}}" id="Alpspartno_{{$key}}" class="form-control"  autocomplete="off" value="{{ isset($row->ALPS_PART_NO)?$row->ALPS_PART_NO:''}}" readonly/></td>
                                                    <td {{$AlpsStatus['hidden']}}><input {{$InputStatus}} type="text" name="Custpartno_{{$key}}" id="Custpartno_{{$key}}" class="form-control"  autocomplete="off" value="{{ isset($row->CUSTOMER_PART_NO)?$row->CUSTOMER_PART_NO:''}}" readonly/></td>
                                                    <td {{$AlpsStatus['hidden']}}><input {{$InputStatus}} type="text" name="OEMpartno_{{$key}}"  id="OEMpartno_{{$key}}" class="form-control"  autocomplete="off"  value="{{ isset($row->OEM_PART_NO)?$row->OEM_PART_NO:'' }}" readonly/></td>

                                                                                                        
                                                    <td><input {{$InputStatus}} type="text" name={{"popupUOM_".$key}} id={{"popupUOM_".$key}} class="form-control" value="{{ $row->UOMCODE }}-{{ $row->DESCRIPTIONS }}"  autocomplete="off"  readonly/></td>
                                                    <td hidden><input type="hidden" name={{"UOMID_REF_".$key}} id={{"UOMID_REF_".$key}} class="form-control" value="{{ $row->UOMID_REF }}" autocomplete="off" readonly /></td>
                                                    <td><input {{$InputStatus}} type="text" name={{"ITEMSPECI_".$key}} id={{"ITEMSPECI_".$key}} class="form-control" maxlength="200" value="{{ $row->ITEMSPECI }}" autocomplete="off" readonly  /></td>
                                                    <td><input {{$InputStatus}} type="text" name={{"RATEPUOM_".$key}} id={{"RATEPUOM_".$key}} class="form-control five-digits" maxlength="13" value="{{ $row->RATE }}"  autocomplete="off" readonly /></td>
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
                                    <div class="col-lg-1 pl"><p>Item Code</p></div>
                                    <div class="col-lg-2 pl">
                                          <input {{$InputStatus}} type="text" name="SchITEM" id="SchITEM" class="form-control" value="{{ isset($objSOSSCH->ICODE)?$objSOSSCH->ICODE:'' }}"   autocomplete="off"  readonly/>
                                          <input type="hidden" name="Sch_ITEMID" id="Sch_ITEMID" class="form-control" value="{{ isset($objSOSSCH->ITEMID_REF)?$objSOSSCH->ITEMID_REF:'' }}" autocomplete="off" />
                                    </div>
                                    <div class="col-lg-1 pl"><p>Item Name</p></div>
                                    <div class="col-lg-2 pl">
                                          <input {{$InputStatus}} type="text" name="Sch_Item_Name" id="Sch_Item_Name" class="form-control" value="{{ isset($objSOSSCH->NAME)?$objSOSSCH->NAME:'' }}"  autocomplete="off"  readonly/>
                                    </div>
                                  </div>
                                  <div class="row" style="margin-left:3px;" >	
                                    <div class="col-lg-1 pl"><p>UoM</p></div>
                                    <div class="col-lg-2 pl">
                                    <input {{$InputStatus}} type="text" name="SchUOM" id="SchUOM" class="form-control" value="{{ isset($objSOSSCH->UOMCODE)?$objSOSSCH->UOMCODE:'' }} {{ isset($objSOSSCH->DESCRIPTIONS)?$objSOSSCH->DESCRIPTIONS:'' }}"  autocomplete="off"  readonly/>
                                          <input type="hidden" name="Sch_UOMID_REF" id="Sch_UOMID_REF" class="form-control" value="{{ isset($objSOSSCH->UOMID_REF)?$objSOSSCH->UOMID_REF:'' }}" autocomplete="off" />
                                    </div>
                                    <div class="col-lg-1 pl"><p>Schedule Qty</p></div>
                                    <div class="col-lg-1 pl">
                                      <input {{$InputStatus}} type="text" name="Sch_SOQTY" id="Sch_SOQTY" class="form-control three-digits" value="{{ isset($objSOSSCH->SOQTY)?$objSOSSCH->SOQTY:'' }}" autocomplete="off" />
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
                                        <td> <input {{$InputStatus}} type="date" name={{"SCHDT_".$skey}}  id={{"SCHDT_".$skey}} class="form-control " autocomplete="off" value="{{$srow->SCHDT}}" placeholder="dd/mm/yyyy" > </td>
                                        <td> <input {{$InputStatus}} type="text" name={{"SCHQTY_".$skey}} id={{"SCHQTY_".$skey}}   class="form-control three-digits" value="{{$srow->SCHQTY}}" autocomplete="off" /> </td>
                                        <td>
                                          <input {{$InputStatus}} type="text" name={{"PopupSHIPTO_".$skey}} id={{"PopupSHIPTO_".$skey}} class="form-control"  autocomplete="off" readonly  />
                                          
                                        </td>
                                        <td hidden><input type="hidden" name={{"txtSHIPTO_".$skey}} id={{"txtSHIPTO_".$skey}} class="form-control" value="{{$srow->SHIPTO}}"  autocomplete="off" /></td>
                                        <td align="center" >
                                          <button {{$InputStatus}} class="btn add" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button>
                                          <button {{$InputStatus}} class="btn remove" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button>
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







<!-- Sub GL Dropdown -->
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
        <td class="ROW2"><input type="text" id="customercodesearch" class="form-control" onkeyup="CustomerCodeFunction('41')"></td>
        <td class="ROW3"><input type="text" id="customernamesearch" class="form-control" onkeyup="CustomerNameFunction('41')"></td>
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
<!-- Sub GL Dropdown-->

<!-- OSO Dropdown -->
<div id="OSOpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='OSO_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>OSO No</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="OSOTable" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
    <tr>
      <th class="ROW1">Select</th> 
      <th class="ROW2">Open Sales Order No</th>
      <th class="ROW3">Open Sales Order Date</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="OSOcodesearch" class="form-control" onkeyup="OSOCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="OSOnamesearch" class="form-control" onkeyup="OSONameFunction()"></td>
      </tr>
    </tbody>
    </table>
      <table id="OSOTable2" class="display nowrap table  table-striped table-bordered" width="100%">
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
            <th style="width:15%;">Item Code</th>
            <th style="width:15%;">Name</th>
            <th style="width:15%;">UOM</th>
            <th style="width:15%;">Business Unit</th>
            <th style="width:15%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</th>
            <th style="width:10%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</th>
            <th style="width:10%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</th>
      </tr>
    </thead>
    <tbody>
    <tr>
    <th style="width:5%;text-align:center;"><span class="check_th">&#10004;</span></th>
    <td style="width:15%;"><input type="text" id="Itemcodesearch" class="form-control" autocomplete="off" onkeyup="ItemCodeFunction()"></td>
    <td style="width:15%;"><input type="text" id="Itemnamesearch" class="form-control" autocomplete="off" onkeyup="ItemNameFunction()"></td>
    <td style="width:15%;"><input type="text" id="ItemUOMsearch" class="form-control" autocomplete="off" onkeyup="ItemUOMFunction()"></td>
    <td style="width:15%;"><input type="text" id="ItemBUsearch" class="form-control" autocomplete="off" onkeyup="ItemBUFunction()"></td>
    <td style="width:15%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemAPNsearch" class="form-control" autocomplete="off" onkeyup="ItemAPNFunction()"></td>
    <td style="width:10%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemCPNsearch" class="form-control" autocomplete="off" onkeyup="ItemCPNFunction()"></td>
    <td style="width:10%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemOEMPNsearch" class="form-control" autocomplete="off" onkeyup="ItemOEMPNFunction()"></td>
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
        <td class="ROW2"><input type="text" id="ShipTocodesearch" class="form-control" onkeyup="ShipToCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="ShipTonamesearch" class="form-control" onkeyup="ShipToNameFunction()"></td>
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
  //GL Account

  

  //GL Account Ends
//------------------------
//Sub GL Account Starts
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
            event.preventDefault();
          },
          error:function(data){
            console.log("Error: Something went wrong.");
            $("#tbody_subglacct").html('');                        
          },
        });
    }

    $('#txtgl_popup').click(function(event){
      var CODE = ''; 
      var NAME = ''; 
      var FORMID = "41";
      loadCustomer(CODE,NAME,FORMID);
      $("#customer_popus").show();
      event.preventDefault();
    });

      $("#customer_closePopup").click(function(event){
        $("#customer_popus").hide();
        $("#customercodesearch").val(''); 
        $("#customernamesearch").val(''); 
        CustomerCodeFunction();
        event.preventDefault();
      });


      function bindSubLedgerEvents(){
        $('.clssubgl').click(function(){
    
            var id = $(this).attr('id');
            var txtval =    $("#txt"+id+"").val();
            var texdesc =   $("#txt"+id+"").data("desc");
            var glid_ref =   $("#txt"+id+"").data("desc2");

            var oldSLID =   $("#SLID_REF").val();
            var MaterialClone = $('#hdnmaterial').val();
            var ScheduleClone = $('#hdnmaterial2').val();
            
            $("#txtgl_popup").val(texdesc);
            $("#txtgl_popup").blur();
            $("#SLID_REF").val(txtval);
            $("#GLID_REF").val(glid_ref);



            if (txtval != oldSLID)
            {
                $('#Material').html(MaterialClone);
                $('#Schedule').html(ScheduleClone);
                $('#Row_Count1').val('1');
                $('#Row_Count2').val('1');
                $('#OSOID_popup').val('');
                $('#OSOID_REF').val('');
                $('#SchITEM').val('');
                $('#Sch_ITEMID').val('');
                $('#SchUOM').val('');
                $('#Sch_UOMID_REF').val('');
                $('#Sch_SOQTY').val('');
                var d = new Date(); 
                var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
                $('#Schedule').find('[id*="SCHDT"]').val(today);
                $('#Schedule').find('[id*="SCHDT"]').attr('min',today);
            }


            $("#customer_popus").hide();
            $("#customercodesearch").val(''); 
            $("#customernamesearch").val(''); 
           

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
      //sub GL
      var customid = $('#SLID_REF').val();
        if(customid!=''){
          $('#tbody_OSO').html('<tr><td colspan="2">Please wait..</td></tr>');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url:'{{route("transaction",[41,"getOpenSalesOrder"])}}',
                type:'POST',
                data:{'id':customid},
                success:function(data) {
                    $('#tbody_OSO').html(data);
                    bindOpenSalesOrder();
                    showSelectedCheck($("#OSOID_REF").val(),"SELECT_OSOID_REF");
                },
                error:function(data){
                  console.log("Error: Something went wrong.");
                  $('#tbody_OSO').html('');
                },
            });        
        }
        ////sub GL end
     $("#OSOpopup").show();
     event.preventDefault();
  });

$("#OSO_closePopup").on("click",function(event){ 
    $("#OSOpopup").hide();
    event.preventDefault();
});
function bindOpenSalesOrder(){
        $('.clsOSO').click(function(){
    
            var id = $(this).attr('id');
            var txtval =    $("#txt"+id+"").val();
            var texdesc =   $("#txt"+id+"").data("desc");
            var MaterialClone = $('#hdnmaterial').val();
            var ScheduleClone = $('#hdnmaterial2').val();
            $("#OSOID_popup").val(texdesc);
            // $("#OSOID_popup").blur();
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
                      url:'{{route("transaction",[41,"getOSOMaterial"])}}',
                      type:'POST',
                      data:{'id':customid},
                      success:function(data) {
                        $('#OSOMaterialBdy').html('');
                          $('#OSOMaterialBdy').html(data);
                          $('#SchITEM').val('');
                          $('#Sch_ITEMID').val('');
                          $('#Sch_Item_Name').val('');
                          $('#SchUOM').val('');
                          $('#Sch_UOMID_REF').val('');
                          $('#Sch_SOQTY').val('');
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
                          $('#Row_Count1').val('1');
                          $('#Row_Count2').val('1');
                          var d = new Date(); 
                          var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
                          $('#Schedule').find('[id*="SCHDT"]').val(today);
                          $('#Schedule').find('[id*="SCHDT"]').attr('min',today);
                          event.preventDefault();
                      },
                      error:function(data){
                        console.log("Error: There is no Item Available.");
                        $('#OSOMaterialBdy').html(MaterialClone);
                        $('#SchITEM').val('');
                        $('#Sch_ITEMID').val('');
                        $('#SchUOM').val('');
                        $('#Sch_UOMID_REF').val('');
                        $('#Sch_SOQTY').val('');
                        $('#Schedule').html(ScheduleClone);
                        $('#Row_Count1').val('1');
                          $('#Row_Count2').val('1');
                        var d = new Date(); 
                        var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
                        $('#Schedule').find('[id*="SCHDT"]').val(today);
                        $('#Schedule').find('[id*="SCHDT"]').attr('min',today);
                      },
                  });
                  $.ajax({
                      url:'{{route("transaction",[41,"getOSOMaterial2"])}}',
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

  $('#Schedule').on('click','#SchITEM',function(event){
        var OSOID = $('#OSOID_REF').val();
                $("#tbody_ItemID").html('');
                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  });
                  $.ajax({
                      url:'{{route("transaction",[41,"getItemDetails"])}}',
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

        $('#Sch_SOQTY').val('');
        $('#Schedule').find('.participantRow3').each(function(){
          $(this).find('input').val('');
          var rowcount2 = $('#Row_Count2').val();
          if(rowcount2 > 1)
          {
            $(this).closest('.participantRow3').remove();
            rowcount2 = parseInt(rowcount2) - 1;
            $('#Row_Count2').val(rowcount2);
          }
        });
        $('#Row_Count1').val('1');
        $('#Row_Count2').val('1');
        var d = new Date(); 
        var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
        $('#Schedule').find('[id*="SCHDT"]').val(today);
        $('#Schedule').find('[id*="SCHDT"]').attr('min',today);


      
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

  $('#example3').on('click','[id*="PopupSHIPTO"]',function(event){
    var fieldid = $(this).parent().parent().find('[id*="txtSHIPTO"]').attr('id');
    var customid = $('#SLID_REF').val();
    if(customid!=''){
          $("#tbody_ShipTo").html('');
          $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          });
          $.ajax({
              url:'{{route("transaction",[41,"getShipAddress"])}}',
              type:'POST',
              data:{'id':customid,fieldid:fieldid},
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
          var texdesc =   $(this).parent().parent().children('[id*="txtshipadd"]').text();

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
    var ScheduleTab = $("#example3").html();
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

    });
    var schitemid = $('#Sch_ITEMID').val();

   

    var schuomid = $('#Sch_UOMID_REF').val();


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
              url:'{{route("transaction",[41,"getShipAddressIDwise"])}}',
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

    $(function() { $('[id*="SOSNO"]').focus(); }); 
    var d = new Date(); 
    var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
   
    $('[id*="SCHDT"]').attr('min',today);
    

    $('#Material').on('focusout',"[id*='RATEPUOM']",function()
    {
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.00000')
      }
      event.preventDefault();
    });
    $('#Sch_SOQTY').focusout(function()
    {
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.000')
      }
      event.preventDefault();
    }); 

    $('#example3').on('focusout',"[id*='SCHQTY']",function()
    {
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.000')
      }
      event.preventDefault();
    });  

    $('#btnAdd').on('click', function() {
        var viewURL = '{{route("transaction",[41,"add"])}}';
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
var lastosodt = <?php echo json_encode($objlastSOSDT[0]->SOSDT); ?>;
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
        $("#NoBtn").focus();
    });

    

    window.fnUndoYes = function (){
      window.location.reload();
   }//fnUndoYes


   window.fnUndoNo = function (){
      $("#SOSNO").focus();
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
$( "#btnSaveSOS" ).click(function() {
    var formSalesOrder = $("#frm_trn_sos");
    if(formSalesOrder.valid()){ 
    $("#FocusId").val('');
    var SOSNO          =   $.trim($("#SOSNO").val());
    var SOSDT          =   $.trim($("#SOSDT").val());
    var GLID_REF       =   $.trim($("#GLID_REF").val());
    var SLID_REF       =   $.trim($("#SLID_REF").val());
    var OSOID_REF      =   $.trim($("#OSOID_REF").val());
    var Sch_ITEMID     =   $.trim($("#Sch_ITEMID").val());
    var Sch_SOQTY      =   $.trim($("#Sch_SOQTY").val());

    if(SOSNO ===""){
        $("#FocusId").val($("#SOSNO"));
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please enter value in SOSNO.');
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
        $("#AlertMessage").text('Please select SOS Date.');
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
     $("#AlertMessage").text('Please select Customer');
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
        $("#AlertMessage").text('Please select OSO No.');
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
                var tquantity = $('#Sch_SOQTY').val();
                  if(totalquantity != tquantity)
                  {
                    allblank4.push('false');
                  }
                  else
                  {
                    allblank4.push('true');
                  }
            }
            
                    
            if(jQuery.inArray("false", allblank) !== -1){
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
                $("#AlertMessage").text('UOM is missing in Material Tab.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
                }            
                else if(jQuery.inArray("false", allblank3) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Please enter Rate per UOM in Material Tab.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
                }
                else if(jQuery.inArray("false", allblank6) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Date cannot be blank in Schedule Tab.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
                }
                else if(jQuery.inArray("false", allblank7) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Quantity must be greater than Zero in Schedule Tab.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
                }
                else if(jQuery.inArray("false", allblank8) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Select Ship To Address in Schedule Tab.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
                }
                else if(jQuery.inArray("false", allblank4) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Quantity entered in grid is not equal to Schedule Quantity in Schedule Tab.');
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
    url:'{{ route("transactionmodify",[41,"update"])}}',
    type:'POST',
    data:formData,
    success:function(data) {
       
        if(data.errors) {
            $(".text-danger").hide();
            if(data.errors.SOSNO){
                showError('ERROR_SOSNO',data.errors.SOSNO);
                        $("#YesBtn").hide();
                        $("#NoBtn").hide();
                        $("#OkBtn1").show();
                        $("#AlertMessage").text('Please enter correct value in SOSNO.');
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

$( "#btnApprove" ).click(function() {
    var formSalesOrder = $("#frm_trn_sos");
    if(formSalesOrder.valid()){ 
    $("#FocusId").val('');
    var SOSNO          =   $.trim($("#SOSNO").val());
    var SOSDT          =   $.trim($("#SOSDT").val());
    var GLID_REF       =   $.trim($("#GLID_REF").val());
    var SLID_REF       =   $.trim($("#SLID_REF").val());
    var OSOID_REF      =   $.trim($("#OSOID_REF").val());
    var Sch_ITEMID     =   $.trim($("#Sch_ITEMID").val());
    var Sch_SOQTY      =   $.trim($("#Sch_SOQTY").val());

    if(SOSNO ===""){
        $("#FocusId").val($("#SOSNO"));
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please enter value in SOSNO.');
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
        $("#AlertMessage").text('Please select SOS Date.');
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
    else if(OSOID_REF ===""){
        $("#FocusId").val($("#OSOID_REF"));
        $("#OSOID_REF").val('');  
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please select OSO No.');
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
                $('#example3').find('.participantRow3').each(function(){
                  if($.trim($(this).find("[id*=SCHDT]").val())!="")
                  {
                      allblank6.push('true');
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
                var tquantity = $('#Sch_SOQTY').val();
                  if(totalquantity != tquantity)
                  {
                    allblank4.push('false');
                  }
                  else
                  {
                    allblank4.push('true');
                  }
            }
            
                    
            if(jQuery.inArray("false", allblank) !== -1){
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
                $("#AlertMessage").text('UOM is missing in Material Tab.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
                }            
                else if(jQuery.inArray("false", allblank3) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Please enter Rate per UOM in Material Tab.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
                }
                else if(jQuery.inArray("false", allblank6) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Date cannot be blank in Schedule Tab.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
                }
                else if(jQuery.inArray("false", allblank7) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Quantity must be greater than Zero in Schedule Tab.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
                }
                else if(jQuery.inArray("false", allblank8) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Select Ship To Address in Schedule Tab.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
                }
                else if(jQuery.inArray("false", allblank4) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Quantity entered in grid is not equal to Schedule Quantity in Schedule Tab.');
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
    url:'{{ route("transactionmodify",[41,"Approve"])}}',
    type:'POST',
    data:formData,
    success:function(data) {
       
        if(data.errors) {
            $(".text-danger").hide();
            if(data.errors.SOSNO){
                showError('ERROR_SOSNO',data.errors.SOSNO);
                        $("#YesBtn").hide();
                        $("#NoBtn").hide();
                        $("#OkBtn1").show();
                        $("#AlertMessage").text('Please enter correct value in SOSNO.');
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
    $("#SOSNO").focus();
});

//ok button
$("#OkBtn").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn").hide();
    $(".text-danger").hide();
    window.location.href = '{{route("transaction",[41,"index"]) }}';
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