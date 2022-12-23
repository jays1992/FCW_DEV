
@extends('layouts.app')
@section('content')
    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('transaction',[311,'index'])}}" class="btn singlebt">Physical Stock Entry</a>
                </div><!--col-2-->
                <div class="col-lg-10 topnav-pd">
                        <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
                        <button class="btn topnavbt" id="btnSaveOSO" disabled="disabled"><i class="fa fa-floppy-o"></i> Save</button>
                        <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt" id="btnUndo"  disabled="disabled"><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove" {{($objRights->APPROVAL1||$objRights->APPROVAL2||$objRights->APPROVAL3||$objRights->APPROVAL4||$objRights->APPROVAL5) == 1 ? 'disabled' : 'disabled'}}><i class="fa fa-thumbs-o-up"></i> Approved</button>
                        <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                        <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                </div>
            </div>
    </div><!--topnav-->	
    <!-- multiple table-responsive table-wrapper-scroll-y my-custom-scrollbar -->
    <form id="frm_trn_pse" method="POST">
     <div class="container-fluid purchase-order-view">


        @csrf
        <div class="container-fluid filter">  
            <div class="inner-form">
                <div class="row">

                <div class="col-lg-2 pl"><p>Doc No</p></div>
                            <div class="col-lg-2 pl">
                          
                                <input {{$ActionStatus}} type="text" name="DOCNO" id="DOCNO" value="{{isset($objPSE->PSENO)?$objPSE->PSENO:''}}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase"  >
                                                                              
                       
                            </div>


                    <input type="hidden" name="PSEID" value="{{isset($objPSE->PSEID)?$objPSE->PSEID:''}}">

          
                    <div class="col-lg-2 pl"><p>Date</p></div>
                    <div class="col-lg-2 pl">
                    <input type="hidden" id="objlastPSEDT"  value="{{isset($objPSE->PSEDT)?$objPSE->PSEDT:''}}">
                                <input {{$ActionStatus}} type="date" name="PSEDT" id="PSEDT" value="{{isset($objPSE->PSEDT)?$objPSE->PSEDT:''}}" class="form-control mandatory" autocomplete="off" placeholder="dd/mm/yyyy" >
                    </div>

                    <div class="col-lg-2 pl"><p>Time</p></div>
        <div class="col-lg-2 pl">
          <input type="time" name="TIME" id="TIME" {{$ActionStatus}}  value="{{$TIME}}" class="form-control mandatory"  placeholder="" >
          <span class="text-danger" id="ERROR_TIME"></span>
        </div>
                </div>  

                <div class="row">
                <div class="col-lg-2 pl"><p>From Store</p></div>
                            <div class="col-lg-2 pl">
                                <input type="text" name="FromStore_popup" {{$ActionStatus}}  id="txtFromStore_popup" class="form-control mandatory"  autocomplete="off" value="{{isset($objPSE->STCODE)?$objPSE->STCODE:''}}-{{isset($objPSE->NAME)?$objPSE->NAME:''}}"  readonly/>
                                <input type="hidden" name="FROMSTOREID_REF" id="FROMSTOREID_REF" value="{{isset($objPSE->STID_REF)?$objPSE->STID_REF:''}}" class="form-control" autocomplete="off" />
                            </div>  


                       


                <div class="col-lg-2 pl"><p>Actual Physical Stock taking By</p></div>
                            <div class="col-lg-4 pl">
                            <input type="text" name="TAKING_BY" id="TAKING_BY" {{$ActionStatus}} value="{{isset($objPSE->ACTUAL_STOCK_TAKEN_BY)?$objPSE->ACTUAL_STOCK_TAKEN_BY:''}}" class="form-control mandatory"  autocomplete="off" >
                            </div>  
                         
            </div>


            <div class="row">
            <div class="col-lg-2 pl"><p>Item Group</p></div>
                            <div class="col-lg-2 pl">
                                <input type="text" name="ItemGID_popup" {{$ActionStatus}} id="txtItemGID_popup" value="{{isset($objPSE->GROUPCODE)?$objPSE->GROUPCODE:''}}-{{isset($objPSE->GROUPNAME)?$objPSE->GROUPNAME:''}}" class="form-control mandatory"  autocomplete="off"  readonly/>
                                <input type="hidden" name="ITEMGID_REF" id="ITEMGID_REF" value="{{isset($objPSE->ITEMGID_REF)?$objPSE->ITEMGID_REF:''}}" class="form-control" autocomplete="off" />
                                <input type="hidden" name="hdnmaterial" id="hdnmaterial" class="form-control" autocomplete="off" />
                               </div>  

          
             </div>

          <div class="container-fluid purchase-order-view">
                <div class="row">              
                    <div class="tab-content">
                        <div id="MaterialCustom" class="tab-pane fade in active">						
                            <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height: 380px; margin-top: 10px;">
                                <table id="example3" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height: auto !important;">
                                    <thead id="thead2" style="position: sticky; top: 0;">
                                        <tr>
                                        <th>Item Code <input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1" value="{{count($objPSEMAT)}}"></th>
                                            <th>Item Name</th>
                                            <th>UOM</th>
                                            <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</th>
                                            <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</th>
                                            <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</th>
                                            <th>Stock-in-hand</th>
                                            <th>Physical Stock</th>
                                            <th>Variance</th>                                     
                                            <th>Reason of Discrepancy	</th>                                     
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @if(!empty($objPSEMAT))
                                    @foreach($objPSEMAT as $key=>$objPSEMAT_list)

                                    @php
                      $variance= number_format($objPSEMAT_list->PHYSICAL_STOCK_QTY-$objPSEMAT_list->STOCK_IN_HAND_QTY,2, '.', '');
                                    

                                    @endphp                           

                                        <tr class="participantRow1">
                                            <td><input {{$ActionStatus}} type="text" name="MainItemCode1_{{$key}}" value="{{$objPSEMAT_list->ICODE}}" id="MainItemCode1_{{$key}}"  class="form-control" autocomplete="off" readonly /></td>
                                            <td hidden><input type="hidden" name="MainItemId1_Ref_{{$key}}" id="MainItemId1_Ref_{{$key}}" class="form-control" value="{{$objPSEMAT_list->ITEMID_REF}}" autocomplete="off" />
                                            <input type="text" name="rowscount1[]" /></td>
                                            <td><input {{$ActionStatus}} type="text" name="MainItemName1_{{$key}}" id="MainItemName1_{{$key}}" value="{{$objPSEMAT_list->NAME}}" class="form-control" autocomplete="off" readonly /></td>
                                            <td><input {{$ActionStatus}} type="text" name="UOM_{{$key}}" id="UOM_{{$key}}" value="{{$objPSEMAT_list->UOMCODE}}-{{$objPSEMAT_list->DESCRIPTIONS}}"  class="form-control" readonly autocomplete="off" /></td>
                                            <td {{$AlpsStatus['hidden']}}><input type="text" name="Alpspartno_{{$key}}" id="Alpspartno_{{$key}}" class="form-control"  autocomplete="off" value="{{ isset($objPSEMAT_list->ALPS_PART_NO)?$objPSEMAT_list->ALPS_PART_NO:''}}" readonly/></td>
                                            <td {{$AlpsStatus['hidden']}}><input type="text" name="Custpartno_{{$key}}" id="Custpartno_{{$key}}" class="form-control"  autocomplete="off" value="{{ isset($objPSEMAT_list->CUSTOMER_PART_NO)?$objPSEMAT_list->CUSTOMER_PART_NO:''}}" readonly/></td>
                                            <td {{$AlpsStatus['hidden']}}><input type="text" name="OEMpartno_{{$key}}"  id="OEMpartno_{{$key}}" class="form-control"  autocomplete="off"  value="{{ isset($objPSEMAT_list->OEM_PART_NO)?$objPSEMAT_list->OEM_PART_NO:'' }}" readonly/></td>
                                            <td hidden><input type="text" name="UOM_REF_{{$key}}" id="UOM_REF_{{$key}}" value="{{$objPSEMAT_list->UOMID_REF}}" maxlength="12" class="form-control" readonly autocomplete="off" /></td>
                                            <td><input {{$ActionStatus}} type="text" name="STOCK_IN_HAND_{{$key}}" id="STOCK_IN_HAND_{{$key}}" value="{{$objPSEMAT_list->STOCK_IN_HAND_QTY}}"  maxlength="20" readonly class="form-control" autocomplete="off" /></td>
                                            <td><input type="text" {{$ActionStatus}} name="PHYSICAL_{{$key}}" id="PHYSICAL_{{$key}}" value="{{$objPSEMAT_list->PHYSICAL_STOCK_QTY}}" class="form-control" maxlength="20" autocomplete="off"  /></td>
                                            <td><input type="text" {{$ActionStatus}}  name="VARIANCE_{{$key}}" id="VARIANCE_{{$key}}" value="{{$variance}}" class="form-control" maxlength="20" autocomplete="off"  /></td>
                                            <td><input {{$ActionStatus}} type="text" name="REASON_{{$key}}" id="REASON_{{$key}}" value="{{$objPSEMAT_list->REASON_DISCREPANCY}}" class="form-control"  autocomplete="off" /></td>
                                            <td align="center">
                                                <button {{$ActionStatus}} class="btn add" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button>
                                                <button {{$ActionStatus}} class="btn remove smaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash"></i></button>
                                            </td>
                                        </tr>
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
    </div>

	</form>
   
    <!--purchase-order-view-->

    <!-- </div> -->

@endsection @section('alert')

<div id="alert" class="modal"  role="dialog"  data-backdrop="static" >
  <div class="modal-dialog" >
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
          <button class="btn alertbt" name='OkBtn1' id="OkBtn1" style="display:none;margin-left: 90px;" onclick="getFocus()"><div id="alert-active" class="activeOk1"></div>OK</button>
          <input type="hidden" id="FocusId" >
        </div>
		    <div class="cl"></div>
      </div>
    </div>
  </div>
</div>


<!-- Alert -->




<!-- Item Group Dropdown -->
<div id="ItemGrouppopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:60%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='ItemID_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Item Group</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="ItemGroupTable" class="display nowrap table  table-striped table-bordered" style="width:100%;">
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
    <input type="text" id="ItemGroupcodesearch" class="form-control" autocomplete="off" onkeyup="ItemGroupCodeFunction()">
    </td>
    <td style="width:60%;">
    <input type="text" id="ItemGroupnamesearch" class="form-control" autocomplete="off" onkeyup="ItemGroupNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="ItemGroupTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          
        </thead>
        <tbody >     
        @foreach ($objItemGroup as $index=>$itemcatRow)
        <tr >
        <td style="text-align:center; width:10%"> <input type="checkbox" name="salesperson[]" id="spidcode_{{ $index }}" class="clsspid"  value="{{ $itemcatRow->ITEMGID }}" ></td>



          <td style="width:30%">{{ $itemcatRow->GROUPCODE }}
          <input type="hidden" id="txtspidcode_{{ $index }}" data-desc="{{ $itemcatRow->GROUPCODE }} - {{ $itemcatRow-> GROUPNAME }}"  value="{{ $itemcatRow->ITEMGID }}"/>
          </td>
          <td style="width:60%">{{ $itemcatRow->GROUPNAME }} </td>
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
<!-- Item Category Dropdown-->



<!-- Item From Store Dropdown -->
<div id="FromStore_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:60%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='FromStore_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Store</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="FromStoreTable" class="display nowrap table  table-striped table-bordered" style="width:100%;">
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
    <input type="text" id="FromStorecodesearch" class="form-control" autocomplete="off" onkeyup="FromStoreCodeFunction()">
    </td>
    <td style="width:60%;">
    <input type="text" id="FromStorenamesearch" class="form-control" autocomplete="off" onkeyup="FromStoreNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="FromStoreTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          
        </thead>
        <tbody >     
        @foreach ($objStore as $index=>$objStoreRow)
        <tr >
        <td style="text-align:center; width:10%"> <input type="checkbox" name="fromstore[]" id="fromstoreidcode_{{ $index }}" class="clsspid_fromstore"  value="{{ $objStoreRow->STID }}" ></td>



          <td style="width:30%">{{ $objStoreRow->STCODE }}
          <input type="hidden" id="txtfromstoreidcode_{{ $index }}" data-desc="{{ $objStoreRow->STCODE }} - {{ $objStoreRow-> NAME }}"  value="{{ $objStoreRow->STID }}"/>
          </td>
          <td style="width:60%">{{ $objStoreRow-> NAME }} </td>
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
<!-- From Store  Dropdown ends here -->




<!-- ITEM Dropdown For substitute tab Section  -->
<div id="mainitempopup1" class="modal" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-md" style="width:60%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" id="item_closePopup1">&times;</button>
            </div>
            <div class="modal-body">
                <div class="tablename"><p>Item List</p></div>
                <div class="single single-select table-responsive table-wrapper-scroll-y my-custom-scrollbar">
                <input type="hidden" class="mainitem_tab1">
                    <table id="ITEMCodeTable1" class="display nowrap table table-striped table-bordered" style="width:100%;">
                        <thead>
                            <tr>
                            <th style="width:10%;">Select</th> 
                                <th style="width:15%;">Item Code</th>
                                <th style="width:15%;">Item Name</th>
                                <th style="width:15%;">Item UOM</th>
                                <th style="width:15%;" {{$AlpsStatus['hidden']}}>{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</th>
                                <th style="width:15%;" {{$AlpsStatus['hidden']}}>{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</th>
                                <th style="width:15%;" {{$AlpsStatus['hidden']}}>{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                            <th style="text-align:center; width:10%;">&#10004;</th>
                                <td style="width:15%;"> 
                                    <input type="text" id="maincodesearch" class="form-control" autocomplete="off" onkeyup="ItemsCodeMainFunction()"  />
                                </td>
                                <td style="width:15%;">
                                    <input type="text" id="mainnamesearch" class="form-control" autocomplete="off" onkeyup="ItemsnameMainFunction()"  />
                                </td>
                                <td style="width:15%;">
                                    <input type="text" id="mainuomsearch" class="form-control" autocomplete="off"  onkeyup="ItemsuomMainFunction()"  />
                                </td>
                                <td style="width:15%;" {{$AlpsStatus['hidden']}}>
                                    <input type="text" id="alpspart" class="form-control" autocomplete="off" onkeyup="AlpspartMainFunction()"  />
                                </td>
                                <td style="width:15%;" {{$AlpsStatus['hidden']}}>
                                    <input type="text" id="customerpart" class="form-control" autocomplete="off" onkeyup="CustomerpartMainFunction()"  />
                                </td>
                                <td style="width:15%;" {{$AlpsStatus['hidden']}}>
                                    <input type="text" id="oempart" class="form-control" autocomplete="off" onkeyup="OempartMainFunction()"  />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table id="ITEMSCodeTable3" class="display nowrap table table-striped table-bordered">
                        <thead id="thead2">
                        <tr>
        
        <td id="item_seach" colspan="4">Please Wait...</td>
          </tr>
                        </thead>
                        <tbody id="Itemresult">
                           
                        </tbody>
                    </table>
                </div>
                <div class="cl"></div>
            </div>
        </div>
    </div>
</div>
<!-- Item popup ends-->


@endsection @push('bottom-css')
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
@endpush @push('bottom-scripts')
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
      //Item Dropdown for Material Tab
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
            input = document.getElementById("FromStorecodesearch");
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
            input = document.getElementById("FromStorenamesearch");
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

          function ItemPartnoFunction() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("Itempartsearch");
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



          
//------------------------
  //Item Category Dropdown
  let sptid = "#ItemGroupTable2";
      let sptid2 = "#ItemGroupTable";
      let salespersonheaders = document.querySelectorAll(sptid2 + " th");

      // Sort the table element when clicking on the table headers
      salespersonheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(sptid, ".clsspid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function ItemGroupCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("ItemGroupcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("ItemGroupTable2");
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

  function ItemGroupNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("ItemGroupnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("ItemGroupTable2");
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

  $('#txtItemGID_popup').click(function(event){
    $("#TotalValue").val('0.00');
    $('#Row_Count3').val('1');
    showSelectedCheck($("#ITEMGID_REF").val(),"salesperson");

    
    
         $("#ItemGrouppopup").show();
      });

      $("#ItemID_closePopup").click(function(event){
        $("#ItemGrouppopup").hide();
      });

      $(".clsspid").click(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");       

        $("#txtItemGID_popup").blur();

        var oldItemCatid =   $("#ITEMGID_REF").val();
    

   
            if (txtval != oldItemCatid)
            {


              $('#example3').find('.participantRow1').each(function(){
            var rowcount = $(this).closest('table').find('.participantRow1').length;
            $(this).find('input:text').val('');
            $(this).find('input:checkbox').prop('checked', false); 
          
            if(rowcount > 1)
            {
              $(this).closest('.participantRow1').remove();
              rowcount = parseInt(rowcount) - 1;
              $('#Row_Count1').val(rowcount);
            }
          });
            
             
                
           
           
            }
            $('#txtItemGID_popup').val(texdesc);
            $('#ITEMGID_REF').val(txtval);
            $("#ItemGrouppopup").hide();














        
        $("#ItemGroupcodesearch").val(''); 
        $("#ItemGroupnamesearch").val(''); 
      
        event.preventDefault();
      });

      

  //Item Category Dropdown Ends




  
    // Item popup starts

    $('#MaterialCustom').on('click','[id*="MainItemCode1"]',function(event){  
      var id=$(this).attr('id')
       var result = id.split('_');
       var id_number=result[1];
    
       var ITEMGROUP_REF = $("#ITEMGID_REF").val();
       var STID_REF = $("#FROMSTOREID_REF").val();

         if(STID_REF==''){
                    $("#FocusId").val('txtFromStore_popup'); 
                    $("#ProceedBtn").focus();
                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn1").show();
                    $("#AlertMessage").text('Please Select Store.');
                    $("#alert").modal('show');                 
                    highlighFocusBtn('activeOk1');
                    return false;
                  }
             else if(ITEMGROUP_REF==''){
                  $("#FocusId").val('txtItemGID_popup');          
                  $("#ProceedBtn").focus();
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn1").show();
                  $("#AlertMessage").text('Please Select Item Group.');
                  $("#alert").modal('show');                
                  highlighFocusBtn('activeOk1');
                  return false;

              }else if(ITEMGROUP_REF!='' && STID_REF!=''){

                  var result = id.split('_');
                  var id_number=result[1];             
                  var popup_id='#'+id;
                  $(".mainitem_tab1").val(id_number);
                $("#Itemresult").html('');

                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  });
                  $("#item_seach").show();
                  $.ajax({
                      url:'{{route("transaction",[311,"get_items"])}}',
                      type:'POST',
                      data:{'ITEMGID':ITEMGROUP_REF,'STID':STID_REF},
                      success:function(data) {
                        $("#item_seach").hide();
                        $("#Itemresult").html(data);   
                        bindItemEvents();                     
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                        $("#Itemresult").html('');                        
                      },
                  }); 

                  $("#mainitempopup1").show();        }                  
                  $("#item_closePopup1").on("click",function(event){
                  $("#mainitempopup1").hide();
                  });  
    });

//Bindevent for item 

    function bindItemEvents(){

    $('.item_click').click(function(){    
      var id_numbers= $(".mainitem_tab1").val()
      var vendorid='#ITEMGID_REF_'+id_numbers;
      var current_vendorid=$(vendorid).val();
      var values="#MainItemId1_Ref_"+id_numbers;
      var code="#MainItemCode1_"+id_numbers;
      var descriptions="#MainItemName1_"+id_numbers;
      var uomname="#UOM_"+id_numbers;
      var uomref="#UOM_REF_"+id_numbers;
      var alt_uomref="#ALTUOMID_REF_"+id_numbers;

      var partno_id="#MainItemPartno1_"+id_numbers;


      var alps_part="#Alpspartno_"+id_numbers;
      var customer_part="#Custpartno_"+id_numbers;
      var oem_part="#OEMpartno_"+id_numbers;
      var stock="#STOCK_IN_HAND_"+id_numbers;

      


      var id          =   $(this).attr('id');
     var txtval      =   $("#txt"+id+"").val();
     var texdesc     =   $("#txt"+id+"").data("code");
     var texdescname =   $("#txt"+id+"").data("name");
     var partno =   $("#txt"+id+"").data("pt");
     var uom =   $("#txt"+id+"").data("uom");
     var uomno =   $("#txt"+id+"").data("uomno");
     var alt_uomno =   $("#txt"+id+"").data("alt_uomno");

     var alps_partno =   $("#txt"+id+"").data("alps_partno");
     var cutomer_partno =   $("#txt"+id+"").data("cutomer_partno");
    var oem_partno =   $("#txt"+id+"").data("oem_partno");
    var stockinhand =   $("#txt"+id+"").data("stockinhand");


 
                         
                                                                                                                                                    


     var CheckExist_item = [];
                var CheckExist_vendor = [];

                $('#example3').find('.participantRow1').each(function(){

                if($(this).find('[id*="MainItemId1_Ref"]').val() != '')

                {
                var itemid = $(this).find('[id*="MainItemId1_Ref"]').val();
         

                  if(itemid!=''){
                CheckExist_item.push(itemid);
                  }
           

                }
                });


  if(jQuery.inArray(txtval, CheckExist_item) !== -1 ){
    $("#YesBtn").hide();
           $("#NoBtn").hide();
           $("#OkBtn").hide();
           $("#OkBtn1").show();
           $("#AlertMessage").text('Item already exists.');
           $("#alert").modal('show');
           $("#OkBtn1").focus();
           highlighFocusBtn('activeOk1');
      

           $(values).val('');
          $(descriptions).val('');
          $(code).val('');
          $(uomname).val('');
          $(uomref).val('');
          $(alt_uomref).val('');
          $(partno_id).val('');
          $(alps_partno).val('');
          $(cutomer_partno).val('');
          $(oem_partno).val('');
          $(stockinhand).val('');
 
          $("#mainitempopup1").hide();
          return false;



            }else{

          $(values).val(txtval);
          $(descriptions).val(texdescname);
          $(code).val(texdesc);
          $(uomname).val(uom);
          $(uomref).val(uomno);
          $(alt_uomref).val(alt_uomno);
          $(partno_id).val(partno);
          $(alps_part).val(alps_partno);
          $(customer_part).val(cutomer_partno);
          $(oem_part).val(oem_partno);
          $(stock).val(stockinhand);

          $("#PHYSICAL_"+id_numbers).val('');
          $("#VARIANCE_"+id_numbers).val('');
          $("#REASON_"+id_numbers).val('');

         // var taxstate=''

        //  $("#mainitempopup1").hide();

            }

$("#mainitempopup1").hide();
 return false;

 });
    }




    //Search item popup

    let tid1 = "#ITEMSCodeTable3";
          let tid3 = "#ITEMCodeTable1";
          let headers1 = document.querySelectorAll(tid3 + " th");

          // Sort the table element when clicking on the table headers
          headers1.forEach(function(element, i) {
            element.addEventListener("click", function() {
              w3.sortHTML(tid1, ".clsglid", "td:nth-child(" + (i + 1) + ")");
            });
          });

    function ItemsCodeMainFunction() {

            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("maincodesearch");
            filter = input.value.toUpperCase();
            table = document.getElementById("ITEMSCodeTable3");
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

      function ItemsnameMainFunction() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("mainnamesearch");
            filter = input.value.toUpperCase();
            table = document.getElementById("ITEMSCodeTable3");
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
      function ItemsuomMainFunction() {
            var input, filter, table, tr, td, i, txtValue;
               input = document.getElementById("mainuomsearch");
            filter = input.value.toUpperCase();
            table = document.getElementById("ITEMSCodeTable3");
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
      function AlpspartMainFunction() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("alpspart");
            filter = input.value.toUpperCase();
            table = document.getElementById("ITEMSCodeTable3");
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
      function CustomerpartMainFunction() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("customerpart");
            filter = input.value.toUpperCase();
            table = document.getElementById("ITEMSCodeTable3");
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
      function OempartMainFunction() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("oempart");
            filter = input.value.toUpperCase();
            table = document.getElementById("ITEMSCodeTable3");
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





      


  $('#MaterialCustom').on('change','[id*="PHYSICAL"]',function(event){  
   var id=this.id; 
   var rowid=id.split("_");
   var rowids=rowid[1];
   var physical=$("#PHYSICAL_"+rowids).val();   
   var STOCK=$("#STOCK_IN_HAND_"+rowids).val();
   if(STOCK===''){
    $("#FocusId").val('STOCK_IN_HAND_'+rowids); 
      $("#ProceedBtn").focus();
      $("#PHYSICAL_"+rowids).val('')
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Stock In Hand Should not be Empty .');
      $("#alert").modal('show');                 
      highlighFocusBtn('activeOk1');
      return false;
   }else if(STOCK!=''){
if(physical!='' && physical!=0){
   var variance=parseFloat(physical-STOCK).toFixed(2);
 if(intRegex.test(variance)){
            $("#VARIANCE_"+rowids).val(variance+'.00');
              if(intRegex.test($(this).val())){
                $(this).val($(this).val()+'.00')
                }
           }else{       
  $("#VARIANCE_"+rowids).val(variance);
       if(intRegex.test($(this).val())){
          $(this).val($(this).val()+'.00')
         }
       }       
}else{
  $("#PHYSICAL_"+rowids).val(''); 
  $("#VARIANCE_"+rowids).val(''); 

}
    }
     event.preventDefault();
  });




      function getArraySum(a){
          var total=0;
          for(var i in a) { 
              total += a[i];
          }
          return total;
      }





    $(document).ready(function(e) {
      $("[id*='PHYSICAL']").ForceNumericOnly();
      var d = new Date(); 
    var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
    $('#PSEDT').val(today);



     

      $("[id*='NORMAL_BCD']").ForceNumericOnly();




            $("[id*='ITEM_QTY']").ForceNumericOnly();
            $('#example3').on('blur','[id*="ITEM_QTY"]',function(){
                if(intRegex.test($(this).val())){
                 $(this).val($(this).val()+'.0000')
                }
                event.preventDefault();
            });





        
        var d = new Date();
        var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
        d.setDate(d.getDate() + 29);
        var todate = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;





        $('#btnAdd').on('click', function() {
            var viewURL = '{{route("transaction",[311,"add"])}}';
                      window.location.href=viewURL;
        });
        $('#btnExit').on('click', function() {
          var viewURL = '{{route('home')}}';
                      window.location.href=viewURL;
        });
    

    //SO Date Check
    $('#CD_DT').change(function( event ) {
                var today = new Date();
                var d = new Date($(this).val());
                today.setHours(0, 0, 0, 0) ;
                d.setHours(0, 0, 0, 0) ;
                var sodate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
                if (d < today) {
                    $(this).val(sodate);
                    $("#alert").modal('show');
                    $("#AlertMessage").text('Date cannot be less than Current date');
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

            $(function(){
        var dtToday = new Date();

        var month = dtToday.getMonth() + 1;
        var day = dtToday.getDate();
        var year = dtToday.getFullYear();
        if(month < 10)
            month = '0' + month.toString();
        if(day < 10)
            day = '0' + day.toString();

        var minDate= year + '-' + month + '-' + day;

        $('#CD_DT').attr('min', minDate);
    });

    //SO Date Check




        //delete row
        $("#MaterialCustom").on('click', '.remove', function() {
    var rowCount = $(this).closest('table').find('.participantRow1').length;
    if (rowCount > 1) {
    $(this).closest('.participantRow1').remove();     
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
    }
    event.preventDefault();
});





    //add row




        //add row
$("#MaterialCustom").on('click', '.add', function() {
  var $tr = $(this).closest('table');
  var allTrs = $tr.find('.participantRow1').last();
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
            return false;
        });



        window.fnUndoYes = function (){
          //reload form
          window.location.href = "{{route('transaction',[311,'add'])}}";
       }//fnUndoYes


       window.fnUndoNo = function (){
         
       }//fnUndoNo







    });
</script>

@endpush @push('bottom-scripts')
<script>

    $( "#btnSave" ).click(function() {
      var formpse = $("#frm_trn_pse");
      if(formpse.valid()){

        $("#FocusId").val('');

     var DOCNO           =   $.trim($("#DOCNO").val());
     var PSEDT          =   $.trim($("#PSEDT").val());
     var VALIDITY_FROM          =   $.trim($("#VALIDITY_FROM").val());
     var VFRDT   =   $.trim($("#VALIDITY_FROM").val());
     var VTODT   =   $.trim($("#VALIDITY_TO").val());
     var ITEMGID_REF          =   $.trim($("#ITEMGID_REF").val());
     var FROMSTOREID_REF       =   $.trim($("#FROMSTOREID_REF").val());
     var TAKING_BY       =   $.trim($("#TAKING_BY").val());
     var TIME       =   $.trim($("#TIME").val());
   


         //DATE VALIDATION SECTION 
    var objlastPSEDT       =   $.trim($("#objlastPSEDT").val());
    var objlastPSEDT_SHOW  =  moment(objlastPSEDT).format('DD/MM/YYYY');  
    var PSEDT_MESSAGE   =   "Selected Date should be equal to or greater than "+objlastPSEDT_SHOW;
    var d = new Date(); 
    var todaydate =   ("0" + (d.getMonth() + 1)).slice(-2) + "/" +('0' + d.getDate()).slice(-2)  + "/" +  d.getFullYear()  ;
    var TODAYDATES  =  moment(todaydate).format('YYYY-MM-DD');
    var objlastPSEDT_SHOW_2  =  moment(todaydate).format('DD/MM/YYYY'); 
    var PSEDT_MESSAGE_2   =   "Selected Date should be equal to or less than "+objlastPSEDT_SHOW_2;

     if(DOCNO ===""){     
         $("#FocusId").val('DOCNO');
         $("#ProceedBtn").focus();
         $("#YesBtn").hide();
         $("#NoBtn").hide();
         $("#OkBtn1").show();
         $("#AlertMessage").text('Please enter value in DOCNO.');
         $("#alert").modal('show');
         $("#OkBtn1").focus();
         return false;
     }
     else if(PSEDT ===""){
         $("#FocusId").val('PSEDT');
         $("#ProceedBtn").focus();
         $("#YesBtn").hide();
         $("#NoBtn").hide();
         $("#OkBtn1").show();
         $("#AlertMessage").text('Please select Date.');
         $("#alert").modal('show');
         $("#OkBtn1").focus();
         return false;
     }
     else if(PSEDT !="" && objlastPSEDT!="" && PSEDT<objlastPSEDT){
        $("#FocusId").val('PSEDT');
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text(PSEDT_MESSAGE);
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
    } 
    else if(PSEDT !=""  && PSEDT>TODAYDATES){      
        $("#FocusId").val('PSEDT');
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text(PSEDT_MESSAGE_2);
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
    }
 
    else if(TIME===''){
        $("#FocusId").val('TIME');
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text("Please Select Time.");
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
    }    
    else if(FROMSTOREID_REF===''){
        $("#FocusId").val('txtFromStore_popup');
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text("Please Select From Store.");
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
    }    
    else if(TAKING_BY===''){
        $("#FocusId").val('TAKING_BY');
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text("Please enter Actual Physical Stock taking By.");
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
    }    
    else if(ITEMGID_REF===''){
        $("#FocusId").val('txtItemGID_popup'); 
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text("Please Select Item Group.");
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
    }   
  
     

    
var RackArray = []; 
var allblank1 = [];
var allblank2 = [];


allblank1.push('true');
allblank2.push('true');


var focustext1= "";
var focustext2= "";


$('#MaterialCustom').find('.participantRow1').each(function(){

  var ItemId           = $.trim($(this).find("[id*=MainItemCode1]").val());
  var physical           = $.trim($(this).find("[id*=PHYSICAL]").val());




  if(ItemId ===""){
    allblank1.push('false');
    focustext1 = $(this).find("[id*=MainItemCode1]").attr('id');

    
  }
  
  else if(physical ==="" ){

    allblank2.push('false');
    focustext2 = $(this).find("[id*=PHYSICAL]").attr('id'); 
  }



});



if(jQuery.inArray("false", allblank1) !== -1){
  $('#tabing1').trigger('click');
  $("#FocusId").val(focustext1);
  $("#alert").modal('show');
  $("#AlertMessage").text('Please Select Item');
  $("#YesBtn").hide(); 
  $("#NoBtn").hide();  
  $("#OkBtn1").show();
  $("#OkBtn1").focus();
  highlighFocusBtn('activeOk');
}   
else if(jQuery.inArray("false", allblank2) !== -1){
$('#tabing1').trigger('click');
$("#FocusId").val(focustext2);
$("#alert").modal('show');
$("#AlertMessage").text('Please Enter Physical Stock Value.');
$("#YesBtn").hide();
$("#NoBtn").hide();
$("#OkBtn1").show();
$("#OkBtn1").focus();
highlighFocusBtn('activeOk');
return false;
}else{
    $("#alert").modal('show');
    $("#AlertMessage").text('Do you want to update the record.');
    $("#YesBtn").data("funcname","fnSaveData");  //set dynamic fucntion name
    $("#YesBtn").focus();
    $("#OkBtn").hide();
    highlighFocusBtn('activeYes');
}
     
    }
    });


$( "#btnApprove" ).click(function() {
  var formpse = $("#frm_trn_pse");
  if(formpse.valid()){
    $("#FocusId").val('');

var DOCNO           =   $.trim($("#DOCNO").val());
var PSEDT          =   $.trim($("#PSEDT").val());
var VALIDITY_FROM          =   $.trim($("#VALIDITY_FROM").val());
var VFRDT   =   $.trim($("#VALIDITY_FROM").val());
var VTODT   =   $.trim($("#VALIDITY_TO").val());
var ITEMGID_REF          =   $.trim($("#ITEMGID_REF").val());
var FROMSTOREID_REF       =   $.trim($("#FROMSTOREID_REF").val());
var TAKING_BY       =   $.trim($("#TAKING_BY").val());
var TIME       =   $.trim($("#TIME").val());



    //DATE VALIDATION SECTION 
var objlastPSEDT       =   $.trim($("#objlastPSEDT").val());
var objlastPSEDT_SHOW  =  moment(objlastPSEDT).format('DD/MM/YYYY');  
var PSEDT_MESSAGE   =   "Selected Date should be equal to or greater than "+objlastPSEDT_SHOW;
var d = new Date(); 
var todaydate =   ("0" + (d.getMonth() + 1)).slice(-2) + "/" +('0' + d.getDate()).slice(-2)  + "/" +  d.getFullYear()  ;
var TODAYDATES  =  moment(todaydate).format('YYYY-MM-DD');
var objlastPSEDT_SHOW_2  =  moment(todaydate).format('DD/MM/YYYY'); 
var PSEDT_MESSAGE_2   =   "Selected Date should be equal to or less than "+objlastPSEDT_SHOW_2;

if(DOCNO ===""){     
    $("#FocusId").val('DOCNO');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please enter value in DOCNO.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
}
else if(PSEDT ===""){
    $("#FocusId").val('PSEDT');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select Date.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
}
else if(PSEDT !="" && objlastPSEDT!="" && PSEDT<objlastPSEDT){
   $("#FocusId").val('PSEDT');
   $("#ProceedBtn").focus();
   $("#YesBtn").hide();
   $("#NoBtn").hide();
   $("#OkBtn1").show();
   $("#AlertMessage").text(PSEDT_MESSAGE);
   $("#alert").modal('show');
   $("#OkBtn1").focus();
   return false;
} 
else if(PSEDT !=""  && PSEDT>TODAYDATES){      
   $("#FocusId").val('PSEDT');
   $("#ProceedBtn").focus();
   $("#YesBtn").hide();
   $("#NoBtn").hide();
   $("#OkBtn1").show();
   $("#AlertMessage").text(PSEDT_MESSAGE_2);
   $("#alert").modal('show');
   $("#OkBtn1").focus();
   return false;
}

else if(TIME===''){
   $("#FocusId").val('TIME');
   $("#ProceedBtn").focus();
   $("#YesBtn").hide();
   $("#NoBtn").hide();
   $("#OkBtn1").show();
   $("#AlertMessage").text("Please Select Time.");
   $("#alert").modal('show');
   $("#OkBtn1").focus();
   return false;
}    
else if(FROMSTOREID_REF===''){
   $("#FocusId").val('txtFromStore_popup');
   $("#ProceedBtn").focus();
   $("#YesBtn").hide();
   $("#NoBtn").hide();
   $("#OkBtn1").show();
   $("#AlertMessage").text("Please Select From Store.");
   $("#alert").modal('show');
   $("#OkBtn1").focus();
   return false;
}    
else if(TAKING_BY===''){
   $("#FocusId").val('TAKING_BY');
   $("#ProceedBtn").focus();
   $("#YesBtn").hide();
   $("#NoBtn").hide();
   $("#OkBtn1").show();
   $("#AlertMessage").text("Please enter Actual Physical Stock taking By.");
   $("#alert").modal('show');
   $("#OkBtn1").focus();
   return false;
}    
else if(ITEMGID_REF===''){
   $("#FocusId").val('txtItemGID_popup'); 
   $("#ProceedBtn").focus();
   $("#YesBtn").hide();
   $("#NoBtn").hide();
   $("#OkBtn1").show();
   $("#AlertMessage").text("Please Select Item Group.");
   $("#alert").modal('show');
   $("#OkBtn1").focus();
   return false;
}   




var RackArray = []; 
var allblank1 = [];
var allblank2 = [];


allblank1.push('true');
allblank2.push('true');


var focustext1= "";
var focustext2= "";


$('#MaterialCustom').find('.participantRow1').each(function(){

var ItemId           = $.trim($(this).find("[id*=MainItemCode1]").val());
var physical           = $.trim($(this).find("[id*=PHYSICAL]").val());




if(ItemId ===""){
allblank1.push('false');
focustext1 = $(this).find("[id*=MainItemCode1]").attr('id');


}

else if(physical ==="" ){

allblank2.push('false');
focustext2 = $(this).find("[id*=PHYSICAL]").attr('id'); 
}



});



if(jQuery.inArray("false", allblank1) !== -1){
$('#tabing1').trigger('click');
$("#FocusId").val(focustext1);
$("#alert").modal('show');
$("#AlertMessage").text('Please Select Item');
$("#YesBtn").hide(); 
$("#NoBtn").hide();  
$("#OkBtn1").show();
$("#OkBtn1").focus();
highlighFocusBtn('activeOk');
}   
else if(jQuery.inArray("false", allblank2) !== -1){
$('#tabing1').trigger('click');
$("#FocusId").val(focustext2);
$("#alert").modal('show');
$("#AlertMessage").text('Please Enter Physical Stock Value.');
$("#YesBtn").hide();
$("#NoBtn").hide();
$("#OkBtn1").show();
$("#OkBtn1").focus();
highlighFocusBtn('activeOk');
return false;
}else{
    $("#alert").modal('show');
    $("#AlertMessage").text('Do you want to approve the record.');
    $("#YesBtn").data("funcname","fnApproveData");  //set dynamic fucntion name
    $("#YesBtn").focus();
    $("#OkBtn").hide();
    highlighFocusBtn('activeYes');
}

    }
    });

    
window.fnApproveData = function (){
event.preventDefault();
     var trnscForm = $("#frm_trn_pse");
    var formData = trnscForm.serialize();
  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });
  $.ajax({
      url:'{{ route("transactionmodify",[311,"Approve"])}}',
      type:'POST',
      data:formData,
      success:function(data) {
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


    $("#YesBtn").click(function(){

    $("#alert").modal('hide');
    var customFnName = $("#YesBtn").data("funcname");
        window[customFnName]();

    }); //yes button

    window.fnSaveData = function (){

    //validate and save data
         var trnosoForm = $("#frm_trn_pse");
        var formData = trnosoForm.serialize();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url:'{{ route("transaction",[311,"update"])}}',
        type:'POST',
        data:formData,
        success:function(data) {

            if(data.errors) {
                $(".text-danger").hide();
                if(data.errors.DOCNO){
                    showError('ERROR_DOCNO',data.errors.DOCNO);
                            $("#YesBtn").hide();
                            $("#NoBtn").hide();
                            $("#OkBtn1").show();
                            $("#AlertMessage").text('Please enter correct value in DOCNO.');
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
        window.location.href = '{{route("transaction",[311,"index"]) }}';
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



//------------------------
  

  //From Store Dropdown starts here 
  let fromstore = "#FromStoreTable2";
      let fromstore2 = "#FromStoreTable";
      let fromstoreheaders = document.querySelectorAll(fromstore2 + " th");

      // Sort the table element when clicking on the table headers
      fromstoreheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(fromstore, ".clsspid_fromstore", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function FromStoreCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("FromStorecodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("FromStoreTable2");
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

  function FromStoreNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("FromStorenamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("FromStoreTable2");
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

  $('#txtFromStore_popup').click(function(event){
    showSelectedCheck($("#FROMSTOREID_REF").val(),"fromstore");
         $("#FromStore_popup").show();
      });

      $("#FromStore_closePopup").click(function(event){
        $("#FromStore_popup").hide();
      });

      $(".clsspid_fromstore").click(function(){
       
        
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");

        var ToStoreid=$("#TOSTOREID_REF").val();
        if(ToStoreid!=""){
        if(txtval==ToStoreid){
          $("#FromStore_popup").hide();
          $("#FROMSTOREID_REF").val('');
          $('#txtFromStore_popup').val('');
          $("#ProceedBtn").focus();
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn1").show();
          $("#AlertMessage").text('Oops! This Store is already selected in To Store');
          $("#alert").modal('show');
          // $("#OkBtn1").focus();
          highlighFocusBtn('activeOk1');
          return false;
          }
        }
        
        $('#txtFromStore_popup').val(texdesc);
        $('#FROMSTOREID_REF').val(txtval);
        $("#FromStore_popup").hide();
        
        $("#FromStorecodesearch").val(''); 
        $("#FromStorenamesearch").val(''); 
   
        event.preventDefault();
      });

      

  //from Store Dropdown Ends

  //from Store Dropdown Ends




      //Search item popup for substitute Main items
      $('#btnExit').on('click', function() {
      var viewURL = '{{route('home')}}';
                  window.location.href=viewURL;
    });
    function AlphaNumaric(e, t) {
    try {
    if (window.event) {
    var charCode = window.event.keyCode;
    }
    else if (e) {
    var charCode = e.which;
    }
    else { return true; }
    if ((charCode >= 48 && charCode <= 57) || (charCode >= 65 && charCode <= 90) || (charCode >= 97 && charCode <= 122))
    return true;
    else
    return false;
    }
    catch (err) {
    alert(err.Description);
    }
    }



    
function showSelectedCheck(hidden_value,selectAll){

var divid ="";

if(hidden_value !=""){

  var all_location_id = document.querySelectorAll('input[name="'+selectAll+'[]"]');
  //console.log(all_location_id); 
 
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

function isNumberDecimalKey(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
    return false;

    return true;
}
</script>

@endpush
