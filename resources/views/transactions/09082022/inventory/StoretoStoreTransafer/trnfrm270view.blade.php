
@extends('layouts.app')
@section('content')
    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('transaction',[270,'index'])}}" class="btn singlebt">Store to Store Transfer</a>
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
    <div class="container-fluid purchase-order-view">
    <form id="frm_trn_store" method="POST">

        @csrf
        <div class="container-fluid filter">  
            <div class="inner-form">
                <div class="row">

                <div class="col-lg-2 pl"><p>Doc No</p></div>
                            <div class="col-lg-2 pl">
                          
                                <input {{$ActionStatus}} type="text" name="DOCNO" id="DOCNO" value="{{isset($objSTORETOTSOTE->STTOST_DOCNO)?$objSTORETOTSOTE->STTOST_DOCNO:''}}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase"  >
                                                                              
                       
                            </div>


                    <input type="hidden" name="STTOSTID" value="{{isset($objSTORETOTSOTE->STTOSTID)?$objSTORETOTSOTE->STTOSTID:''}}">

          
                    <div class="col-lg-2 pl"><p>Date</p></div>
                    <div class="col-lg-2 pl">
                    <input type="hidden" id="objlastSTSDT"  value="{{isset($objSTORETOTSOTE->STTOST_DOCDT)?$objSTORETOTSOTE->STTOST_DOCDT:''}}">
                                <input {{$ActionStatus}} type="date" name="STSDT" id="STSDT" value="{{isset($objSTORETOTSOTE->STTOST_DOCDT)?$objSTORETOTSOTE->STTOST_DOCDT:''}}" class="form-control mandatory" autocomplete="off" placeholder="dd/mm/yyyy" >
                    </div>

                    <div class="col-lg-2 pl"><p>Item Category</p></div>
                            <div class="col-lg-2 pl">
                                <input {{$ActionStatus}} type="text" name="ItemCatID_popup" id="txtItemCatID_popup" value="{{isset($objCategory->ICCODE)?$objCategory->ICCODE:''}} {{isset($objCategory->DESCRIPTIONS)?'-'.$objCategory->DESCRIPTIONS:''}}" class="form-control mandatory"  autocomplete="off"  readonly/>
                                <input type="hidden" name="ITEMCAT_REF" id="ITEMCAT_REF" value="{{isset($objSTORETOTSOTE->ICID_REF)?$objSTORETOTSOTE->ICID_REF:''}}" class="form-control" autocomplete="off" />
                                <input type="hidden" name="hdnmaterial" id="hdnmaterial" class="form-control" autocomplete="off" />
                            </div>    
                </div>  

                <div class="row">
                <div class="col-lg-2 pl"><p>From Store</p></div>
                            <div class="col-lg-2 pl">
                                <input {{$ActionStatus}} type="text" name="FromStore_popup" id="txtFromStore_popup"  class="form-control mandatory"  autocomplete="off" value="{{isset($objFromStore->STCODE)?$objFromStore->STCODE:''}} {{isset($objFromStore->NAME)?'-'.$objFromStore->NAME:''}}"  readonly/>
                                <input type="hidden" name="FROMSTOREID_REF" id="FROMSTOREID_REF" value="{{isset($objSTORETOTSOTE->FROM_STID_REF)?$objSTORETOTSOTE->FROM_STID_REF:''}}" class="form-control" autocomplete="off" />
                            </div>  
                <div class="col-lg-2 pl"><p>To Store</p></div>
                            <div class="col-lg-2 pl">
                                <input {{$ActionStatus}} type="text" name="ToStore_popup" id="txtToStore_popup" class="form-control mandatory"  autocomplete="off" value="{{isset($objToStore->STCODE)?$objToStore->STCODE:''}} {{isset($objToStore->NAME)?'-'.$objToStore->NAME:''}}"  readonly/>
                                <input type="hidden" name="TOSTOREID_REF" id="TOSTOREID_REF" class="form-control" value="{{isset($objSTORETOTSOTE->TO_STID_REF)?$objSTORETOTSOTE->TO_STID_REF:''}}" autocomplete="off" />
                            </div>  
                            <div class="col-lg-2 pl"><p>Total Value</p></div>
                            <div class="col-lg-2 pl">
                                <input {{$ActionStatus}} type="text" name="TotalValue" id="TotalValue" class="form-control"  autocomplete="off" readonly  />
                            </div>
            </div>
            </div>

          <div class="container-fluid purchase-order-view">
                <div class="row">

                <ul class="nav nav-tabs">
                            <li class="active"><a data-toggle="tab" href="#MaterialCustom">Material</a></li>
                       
                            <li><a data-toggle="tab" href="#store">STORE</a></li>
                        
                        </ul>
                    <div class="tab-content">
                        <div id="MaterialCustom" class="tab-pane fade in active">
						
                            <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height: 380px; margin-top: 10px;">
                                <table id="example3" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height: auto !important;">
                                    <thead id="thead2" style="position: sticky; top: 0;">
                                        <tr>
                                            <th>Item Group<input class="form-control" type="hidden" name="Row_Count2" id="Row_Count2" /></th>
                                         
                                            <th>Item Code</th>
                                            <th>Item Name</th>
                                            <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</th>
                                            <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</th>
                                            <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</th>
                                            <th>UOM</th>
                                            <th>Qty</th>
                                            <th>Batch / Lot</th>
                                           <!-- <th>Rate</th>
                                            <th>Value</th>-->
                                            <th>Remarks	</th>                                     
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @if(!empty($objSTORETOTSOTEMAT))
                                    @foreach($objSTORETOTSOTEMAT as $key=>$objSTORETOTSOTEMAT_list)
                                    @php
                                    $Total_Amounts= number_format($objSTORETOTSOTEMAT_list->QTY*$objSTORETOTSOTEMAT_list->RATE,2, '.', '');
                                    

                                    @endphp

                                        <tr class="participantRow1">
                                            <td><input {{$ActionStatus}} type="text" name={{"ITEMG_CODE_".$key}} id={{"ITEMG_CODE_".$key}}  value="{{$objSTORETOTSOTEMAT_list->GROUPCODE}}-{{$objSTORETOTSOTEMAT_list->GROUPNAME}}"  class="form-control" autocomplete="off" readonly /></td>
                                            <td hidden>
                                                <input type="text" name={{"ITEMGID_REF_".$key}} id={{"ITEMGID_REF_".$key}}  value="{{$objSTORETOTSOTEMAT_list->ITEMGID_REF}}" class="form-control" autocomplete="off" />
                                                <input type="text" name="rowscount1[]" />
                                            </td>
                                
                                            <td><input {{$ActionStatus}} type="text" name={{"MainItemCode1_".$key}} id={{"MainItemCode1_".$key}}  value="{{$objSTORETOTSOTEMAT_list->ICODE}}"  class="form-control" autocomplete="off" readonly /></td>
                                            <td hidden><input type="hidden" name={{"MainItemId1_Ref_".$key}} id={{"MainItemId1_Ref_".$key}}  class="form-control" value="{{$objSTORETOTSOTEMAT_list->ITEMID_REF}}" autocomplete="off" /></td>
                                            <td><input {{$ActionStatus}} type="text" name={{"MainItemName1_".$key}} id={{"MainItemName1_".$key}} value="{{$objSTORETOTSOTEMAT_list->NAME}}" class="form-control" autocomplete="off" readonly /></td>

                                            <td {{$AlpsStatus['hidden']}}><input type="text" name="Alpspartno_{{$key}}" id="Alpspartno_{{$key}}" class="form-control"  autocomplete="off" value="{{ isset($objSTORETOTSOTEMAT_list->ALPS_PART_NO)?$objSTORETOTSOTEMAT_list->ALPS_PART_NO:''}}" readonly/></td>
                                            <td {{$AlpsStatus['hidden']}}><input type="text" name="Custpartno_{{$key}}" id="Custpartno_{{$key}}" class="form-control"  autocomplete="off" value="{{ isset($objSTORETOTSOTEMAT_list->CUSTOMER_PART_NO)?$objSTORETOTSOTEMAT_list->CUSTOMER_PART_NO:''}}" readonly/></td>
                                            <td {{$AlpsStatus['hidden']}}><input type="text" name="OEMpartno_{{$key}}"  id="OEMpartno_{{$key}}" class="form-control"  autocomplete="off"  value="{{ isset($objSTORETOTSOTEMAT_list->OEM_PART_NO)?$objSTORETOTSOTEMAT_list->OEM_PART_NO:'' }}" readonly/></td>



                                            <td><input {{$ActionStatus}} type="text" name={{"UOM_".$key}} id={{"UOM_".$key}}  value="{{$objSTORETOTSOTEMAT_list->UOMCODE}}-{{$objSTORETOTSOTEMAT_list->DESCRIPTIONS}}"  class="form-control" readonly autocomplete="off" /></td>
                                            <td hidden><input type="text" name={{"UOM_REF_".$key}} id={{"UOM_REF_".$key}}  value="{{$objSTORETOTSOTEMAT_list->UOMID_REF}}" maxlength="12" class="form-control" readonly autocomplete="off" /></td>
                                            <td hidden><input type="text" name={{"ALTUOMID_REF_".$key}} id={{"ALTUOMID_REF_".$key}}  maxlength="12" class="form-control" readonly autocomplete="off" value="{{$objSTORETOTSOTEMAT_list->ALT_UOMID_REF}}" /></td>
                                            <td><input {{$ActionStatus}} type="text" name={{"ITEM_QTY_".$key}} id={{"ITEM_QTY_".$key}}  value="{{$objSTORETOTSOTEMAT_list->QTY}}"  maxlength="12" class="form-control" autocomplete="off" /></td>
                                            <td align="center" >
                                              <button {{$ActionStatus}} class="btn" {{ isset($objSTORETOTSOTEMAT_list->BATCHNOA) && $objSTORETOTSOTEMAT_list->BATCHNOA =='0' ? 'disabled':''}} id="SCSTR_0" name="SCSTR_0" type="button"><i class="fa fa-clone"></i></button>
                                            </td>


                                            <td hidden ><input type="hidden" name={{"TotalHiddenQty_".$key}} id={{"TotalHiddenQty_".$key}}  value="{{$objSTORETOTSOTEMAT_list->QTY}}"></td>
                                            <td hidden ><input type="hidden" name={{"HiddenRowId_".$key}} id={{"HiddenRowId_".$key}}   value="{{ $objSTORETOTSOTEMAT_list->BATCH_QTY}}"></td>                                                    
                                            <td hidden><input type="text" name={{"STORE_".$key}} id={{"STORE_".$key}}  class="form-control"  autocomplete="off"  readonly/></td>
                                           
                                            <td hidden><input type="text" name={{"RATE_".$key}} id={{"RATE_".$key}}  value="{{$objSTORETOTSOTEMAT_list->RATE}}" class="form-control" maxlength="12" autocomplete="off" readonly /></td>

                                            <td hidden><input type="text" name={{"VALUE_".$key}} id={{"VALUE_".$key}}  value="{{$Total_Amounts}}"  class="form-control" maxlength="12" autocomplete="off" value="0.00" readonly /></td>


                                           
                                            <td><input {{$ActionStatus}} type="text" name={{"REMARKS_".$key}} id={{"REMARKS_".$key}}  value="{{$objSTORETOTSOTEMAT_list->REMARKS}}" class="form-control"  autocomplete="off" /></td>
                                   
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

            
								
								
								<div id="store" class="tab-pane fade" >
                                    <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="margin-top:10px;height:280px;width:50%;">
                                        <table id="example5" class="display nowrap table table-striped table-bordered itemlist" style="height:auto !important;">
                                            <thead id="thead1"  style="position: sticky;top: 0">
                                            <tr >
                                                <th>Batch No<input class="form-control" type="hidden" name="Row_Count3" id ="Row_Count3" value="{{$objCount3}}"></th>
                                                <th>From Store</th>
                                                <th>To Store</th>
                                            
                                                <th>Main UOM</th>
                                                <!--<th>Stock In Hand</th>-->
                                                <th>Transfer Quantity</th>
                                                <th>Alt UOM</th>
                                              <!--  <th>Item</th> -->                               
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(!empty($objSTORE))
                                              @foreach($objSTORE as $key=>$sRow)
                                              <tr  class="participantRow5">
                                                  <td><input {{$ActionStatus}} type="text" name={{"BATCHNO_".$key}} id={{"BATCHNO_".$key}} class="form-control" value="{{$sRow->BATCH_CODE}}"  autocomplete="off"  /></td>
                                                 <td width="125"><input {{$ActionStatus}} type="text"  class="form-control" value="{{$objFromStore->STCODE}}-{{$objFromStore->NAME}}" autocomplete="off"   readonly /></td>
                                                 <td width="125"><input {{$ActionStatus}} type="text"  class="form-control" value="{{$objToStore->STCODE}}-{{$objToStore->NAME}}" autocomplete="off"   readonly /></td>
                                                 <td hidden><input type="text" name={{"STID_REF_".$key}} id={{"STID_REF_".$key}} class="form-control" value="" autocomplete="off"   readonly /></td>
                                                 <td hidden><input type="text"  name={{"BATCHID_REF_".$key}} id={{"BATCHID_REF_".$key}}  class="form-control"  autocomplete="off"  value="{{$sRow->BATCHID_REF}}" /></td>
                                                  <td hidden><input type="text" name={{"SERIALNO_".$key}} id={{"SERIALNO_".$key}} class="form-control" value=""   autocomplete="off" /></td>
                                                  <td><input {{$ActionStatus}} type="text" name={{"STRMUOMID_REF_".$key}} id={{"STRMUOMID_REF_".$key}} class="form-control" value="{{$sRow->MAIN_UOMID_REF}}"  autocomplete="off"  /></td>
                                                  <td hidden><input type="text" name={{"SOTCK_".$key}} id={{"SOTCK_".$key}} class="form-control" autocomplete="off" value=""   /></td>
                                                  <td><input {{$ActionStatus}} type="text" name={{"DISPATCH_MAIN_QTY_".$key}} id={{"DISPATCH_MAIN_QTY_".$key}} class="form-control"  value="{{$sRow->TRANSFER_QTY}}"  autocomplete="off" /></td>
                                                  <td><input {{$ActionStatus}} type="text" name={{"STRAUOMID_REF_".$key}} id={{"STRAUOMID_REF_".$key}} class="form-control" value="{{$sRow->ALT_UOMID_REF}}" autocomplete="off"   /></td>
                                                  <td hidden><input type="text" name={{"STRITEM_REF_".$key}} id={{"STRITEM_REF_".$key}} class="form-control" value="{{$sRow->ITEMID_REF}}"  autocomplete="off" /></td>
                                              
                                                  <!-- <td align="center" ><button class="btn add UDF" title="add" data-toggle="tooltip" type="button" disabled><i class="fa fa-plus"></i></button>
                                                  <button class="btn remove DUDF" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td> -->
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
	</form>
    </div>
    <!--purchase-order-view-->

    <!-- </div> -->

@endsection @section('alert')
<div id="alert" class="modal" role="dialog" data-backdrop="static" style="z-index:10000000;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" id="closePopup">&times;</button>
                <h4 class="modal-title">System Alert Message</h4>
            </div>
            <div class="modal-body">
                <h5 id="AlertMessage"></h5>
                <div class="btdiv">
                    <button class="btn alertbt" name="YesBtn" id="YesBtn" data-funcname="fnSaveData">
                        <div id="alert-active" class="activeYes"></div>
                        Yes
                    </button>
                    <button class="btn alertbt" name="NoBtn" id="NoBtn" data-funcname="fnUndoNo">
                        <div id="alert-active" class="activeNo"></div>
                        No
                    </button>
                    <button class="btn alertbt" name="OkBtn" id="OkBtn" style="display: none; margin-left: 90px;">
                        <div id="alert-active" class="activeOk"></div>
                        OK
                    </button>
                    <button class="btn alertbt" name="OkBtn1" id="OkBtn1" style="display: none; margin-left: 90px;">
                        <div id="alert-active" class="activeOk1"></div>
                        OK
                    </button>
                </div>
                <!--btdiv-->
                <div class="cl"></div>
            </div>
        </div>
    </div>
</div>

<!-- Alert -->




<!-- Item Category Dropdown -->
<div id="ItemCatpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:60%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='ItemID_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Item Category</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="ItemCategoryTable" class="display nowrap table  table-striped table-bordered" style="width:100%;">
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
    <input type="text" id="SalesPersoncodesearch" class="form-control" autocomplete="off" onkeyup="SalesPersonCodeFunction()">
    </td>
    <td style="width:60%;">
    <input type="text" id="SalesPersonnamesearch" class="form-control" autocomplete="off" onkeyup="SalesPersonNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="ItemCategoryTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          
        </thead>
        <tbody >     
        @foreach ($objItemCategory as $itemcatindex=>$itemcatRow)
        <tr >
        <td style="text-align:center; width:10%"> <input type="checkbox" name="salesperson[]" id="spidcode_{{ $itemcatindex }}" class="clsspid"  value="{{ $itemcatRow->ICID }}" ></td>



          <td style="width:30%">{{ $itemcatRow->ICCODE }}
          <input type="hidden" id="txtspidcode_{{ $itemcatindex }}" data-desc="{{ $itemcatRow->ICCODE }} - {{ $itemcatRow-> DESCRIPTIONS }}"  value="{{ $itemcatRow->ICID }}"/>
          </td>
          <td style="width:60%">{{ $itemcatRow-> DESCRIPTIONS }} </td>
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
	  <div class="tablename"><p>Store List</p></div>
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

<!-- To Store Dropdown -->
<div id="ToStore_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:60%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='ToStore_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Store List</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="ToStoreTable" class="display nowrap table  table-striped table-bordered" style="width:100%;">
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
    <input type="text" id="ToStorecodesearch" class="form-control" autocomplete="off" onkeyup="ToStoreCodeFunction()">
    </td>
    <td style="width:60%;">
    <input type="text" id="ToStorenamesearch" class="form-control" autocomplete="off" onkeyup="ToStoreNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="ToStoreTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          
        </thead>
        <tbody >     
        @foreach ($objStore as $index=>$objStoreRow)
        <tr >
        <td style="text-align:center; width:10%"> <input type="checkbox" name="tostore[]" id="tostoreidcode_{{ $index }}" class="clsspid_tostore"  value="{{ $objStoreRow->STID }}" ></td>



          <td style="width:30%">{{ $objStoreRow->STCODE }}
          <input type="hidden" id="txttostoreidcode_{{ $index }}" data-desc="{{ $objStoreRow->STCODE }} - {{ $objStoreRow-> NAME }}"  value="{{ $objStoreRow->STID }}"/>
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
<!-- To Store  Dropdown-->









<!-- ITEM Dropdown For substitute tab Section  -->
<div id="mainitempopup1" class="modal" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-md" style="width:90%">
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
                            <th style="width:12%;">Select</th> 
                                <th style="width:11%;">Item Code</th>
                                <th style="width:11%;">Item Name</th>
                                <th style="width:11%;">Item UOM</th>
                                <th style="width:11%;">Item Drawing No</th>
                                <th style="width:11%;">Business Unit</th>
                                <th style="width:11%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</th>
                                <th style="width:11%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</th>
                                <th style="width:11%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                            <th style="text-align:center; width:12%;">&#10004;</th>

                       

                                <td style="width:11%;"> 
                                    <input type="text" id="maincodesearch" class="form-control" autocomplete="off" onkeyup="ItemsCodeMainFunction()"  />
                                </td>
                                <td style="width:11%;">
                                    <input type="text" id="mainnamesearch" class="form-control" autocomplete="off" onkeyup="ItemsnameMainFunction()"  />
                                </td>
                                <td style="width:11%;">
                                    <input type="text" id="mainuomsearch" class="form-control" autocomplete="off"  onkeyup="ItemsuomMainFunction()"  />
                                </td>
                                <td style="width:11%;">
                                    <input type="text" id="maindrawingsearch" class="form-control" autocomplete="off" onkeyup="ItemsdrawingMainFunction()"  />
                                </td>

                                <td style="width:11%;"><input type="text" id="ItemBUsearch" class="form-control" autocomplete="off" onkeyup="ItemBUFunction()"></td>
                                <td style="width:11%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemAPNsearch" class="form-control" autocomplete="off" onkeyup="ItemAPNFunction()"></td>
                                <td style="width:11%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemCPNsearch" class="form-control" autocomplete="off" onkeyup="ItemCPNFunction()"></td>
                                <td style="width:11%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemOEMPNsearch" class="form-control" autocomplete="off" onkeyup="ItemOEMPNFunction()"></td>


                            </tr>
                        </tbody>
                    </table>
                    <table id="ITEMSCodeTable3" class="display nowrap table table-striped table-bordered">
                        <thead id="thead2">
                        <tr>
        
        <td id="item_seach" colspan="4">please wait...</td>
          </tr>
                        </thead>
                        <tbody id="Itemresult" style="font-size:13px;">
                           
                        </tbody>
                    </table>
                </div>
                <div class="cl"></div>
            </div>
        </div>
    </div>
</div>
<!-- Item popup ends-->

<!--vendor popup starts-->

<div id="itemgroup_popup" class="modal" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-md" style="width:60%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" id="vendor_close">&times;</button>
            </div>
            <div class="modal-body">
                <div class="tablename"><p>Item Group List</p></div>
                <div class="single single-select table-responsive table-wrapper-scroll-y my-custom-scrollbar">
                    <table id="vendor_table1" class="display nowrap table table-striped table-bordered" style="width:100%;">
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
                           <input type="text" id="vendor_codesearch" class="form-control" autocomplete="off"  onkeyup="searchVendorCode()" /></td>
                                <td><input type="text" id="vendor_namesearch" class="form-control"  autocomplete="off" onkeyup="searchVendorName()" /></td>
                            </tr>
                        </tbody>
                    </table>

                    <table id="vendor_table2" class="display nowrap table table-striped table-bordered" width="100%">
                        <thead id="thead2"></thead>
                        <tbody id="vendor_body">
                            @foreach ($objItemgroup as $index=>$RowItemGroup)
                            <tr>
                            <td style="text-align:center; width:10%"> <input type="checkbox"    id="vendorcode_{{$index}}" class="itemgroupclose" value="{{ $RowItemGroup->ITEMGID }}" name="itemgroupname[]" ></td>
                            <td hidden> <input type="text" class="vendorcls" ></td>


                   
                                <td style="width:30%">
                                    {{ $RowItemGroup-> GROUPCODE }} <input type="hidden" id="txtvendorcode_{{ $index }}" data-code="{{ $RowItemGroup->GROUPCODE }}" data-name="{{ $RowItemGroup->GROUPCODE }}-{{ $RowItemGroup->GROUPNAME }}" value="{{ $RowItemGroup->ITEMGID }}" >
                                </td>
                                <td style="width:60%">{{ $RowItemGroup->GROUPNAME }}</td>
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


<!-- Store -->
<div id="storepopup" class="modal" role="dialog"   data-backdrop="static" >
  <div class="modal-dialog modal-md" style="width:1250px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='storeclosePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Batch/Lot List</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="storeTable" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
      <thead style="position: sticky;top: 0">
        <tr>
                <th>Batch / Lot No</th>
                <th>Store</th>
                <th>Main UoM (MU)</th>
                <th>Stock-in-hand</th>
                <th>Transfer Qty (MU)</th>
                <th>Alt UOM (AU)</th>
                <th>Transfer Qty (AU)</th>
        </tr>

      </thead>
      <tbody id="tbody_store">
          
      </tbody>
    </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Store-->


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



// Store Popup
let strtid = "#storeTable";
      let strheaders = document.querySelectorAll(strtid + " th");

      // Sort the table element when clicking on the table headers
      strheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(strtid, ".clsstrid", "td:nth-child(" + (i + 1) + ")");
        });
      });

  $('#MaterialCustom').on('click','[id*="SCSTR"]',function(event){
    
        $('#tbody_store').html('<tr><td colspan="7">Please wait ...</td></tr>');
        var itemid = $(this).parent().parent().find('[id*="MainItemId1_Ref"]').val();
        var muomid = $(this).parent().parent().find('[id*="UOM_REF"]').val();
        var auomid = $(this).parent().parent().find('[id*="ALTUOMID_REF"]').val();
        var soqty = $(this).parent().parent().find('[id*="ITEM_QTY"]').val();
        var qtyid = $(this).parent().parent().find('[id*="ITEM_QTY"]').attr('id');
        var storeid = $(this).parent().parent().find('[id*="STORE"]').attr('id');

        if(soqty=='' && soqty!='0'){
        $("#alert").modal('show');
        $("#AlertMessage").text('Please enter item Qty first!.');
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();                  
        highlighFocusBtn('activeOk');
        return false;
      }
        






        var ITEMROWID = $(this).parent().parent().find('[id*="HiddenRowId"]').val();
       

        if(itemid != ''){
          var STORE_ID=$("#FROMSTOREID_REF").val();
          $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url:'{{route("transaction",[270,"getItemwiseStoreDetails"])}}',
                type:'POST',
                data:{'itemid':itemid,'muomid':muomid,'auomid':auomid,'soqty':soqty,'storeid':storeid,'qtyid':qtyid,ITEMROWID:ITEMROWID,STORE_ID:STORE_ID,ACTION_TYPE:'ADD'},
                success:function(data) {
                    $('#tbody_store').html(data);
                    bindStoreEvents();
                },
                error:function(data){
                  console.log("Error: Something went wrong.");
                  $('#tbody_store').html('');
                  bindStoreEvents();
                },
            });  
        }
        $("#storepopup").show();
      });

      $("#storeclosePopup").click(function(event){
        var txtid = $('#hdnstoreid').val();
        var MulStoreId = '';

        var NewQtyArr = [];
        var NewRateArr = [];
        var NewIdArr  = [];
     
        
        $('#storeTable').find('.clsstrid').each(function(){



          if($(this).find('[id*="strDISPATCH_MAIN_QTY"]').val() != '')
          {
          var batchno = $(this).find('[id*="strBATCHNO"]').val();
          var batchid_ref = $(this).find('[id*="strBT_REF"]').val();
          var stid = $(this).find('[id*="strSTID_REF"]').val();
          var muomid = $(this).find('[id*="MUOM_REF"]').val();
          var itemid = $(this).find('[id*="strITEMID_REF"]').val();
          var stock = $(this).find('[id*="strSOTCK"]').val();
          var dqty = $(this).find('[id*="strDISPATCH_MAIN_QTY"]').val();
          var rate = $(this).find('[id*="strrate"]').val();
       
          var auomid = $(this).find('[id*="AUOM_REF_"]').val();
          var txtid = $('#hdnstoreid').val();
          var txtid2 = $('#hdnqtyid').val();

          var totalrate = parseFloat((dqty * rate)).toFixed(5);
    

          var UserQty      = parseFloat(dqty);
          var UserRate      = parseFloat(totalrate);
          var BatchId      = $.trim($(this).find('[id*="strBATCHID"]').val());

          NewQtyArr.push(UserQty);
          NewRateArr.push(UserRate);
          NewIdArr.push(BatchId+"_"+UserQty);
   

          var SalesOrder5 = [];
          $('#example5').find('.participantRow5').each(function(){
            if($(this).find('[id*="STRITEM_REF"]').val() != '')
            {
              var soitem = $(this).find('[id*="STRITEM_REF"]').val()+'-'+$(this).find('[id*="STRMUOMID_REF"]').val()+'-'+$(this).find('[id*="BATCHNO"]').val()+'-'+$(this).find('[id*="STID_REF"]').val();
              SalesOrder5.push(soitem);
            }
          });

          //alert(SalesOrder5); 

          var StoreItem = itemid+'-'+muomid+'-'+batchno+'-'+stid;
         // alert(StoreItem); 
          if(jQuery.inArray(StoreItem, SalesOrder5) !== -1)
            {
              $('#example5').find('.participantRow5').each(function(){
              if($(this).find('[id*="STRITEM_REF"]').val() != '')
                {
                 
                  if(StoreItem == $(this).find('[id*="STRITEM_REF"]').val()+'-'+$(this).find('[id*="STRMUOMID_REF"]').val()+'-'+$(this).find('[id*="BATCHNO"]').val()+'-'+$(this).find('[id*="STID_REF"]').val())
                  {
                    $(this).find('[id*="SOTCK"]').val(stock);
                    $(this).find('[id*="DISPATCH_MAIN_QTY"]').val(dqty);
                    $(this).find('[id*="STRAUOMID_REF"]').val(auomid);
                    $("#storepopup").hide();
                    return false;
                  }
                }
              });


              if ($('#'+txtid).val().indexOf(stid) !== -1) {                
              } else {
                if($('#'+txtid).val() == '')
                {
                  $('#'+txtid).val(stid);
                }
                else
                {
                  $('#'+txtid).val($('#'+txtid).val()+','+stid);
                }
              }
              // $('#'+txtid2).val(dqty);
            }
          else
            {
            
            var $tr = $('.participantRow5').closest('table');
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

            $clone.find('[id*="BATCHNO"]').val(batchno);
            $clone.find('[id*="BATCHID_REF"]').val(batchid_ref);
            $clone.find('[id*="STID_REF"]').val(stid);
            $clone.find('[id*="SERIALNO"]').val('');
            $clone.find('[id*="STRMUOMID_REF"]').val(muomid);
            $clone.find('[id*="SOTCK"]').val(stock);
            $clone.find('[id*="DISPATCH_MAIN_QTY"]').val(dqty);
            $clone.find('[id*="STRAUOMID_REF"]').val(auomid);
            $clone.find('[id*="STRITEM_REF"]').val(itemid);
      
   
            $tr.closest('table').append($clone);   
            var rowCount3 = $('#Row_Count3').val();
            rowCount3 = parseInt(rowCount3)+1;
            $('#Row_Count3').val(rowCount3);    
         
            if ($('#'+txtid).val().indexOf(stid) !== -1) {                
              } else {
                if($('#'+txtid).val() == '')
                {
                  $('#'+txtid).val(stid);
                }
                else
                {
                  $('#'+txtid).val($('#'+txtid).val()+','+stid);
                }
              }
              // $('#'+txtid2).val(dqty);     
            }
          }
        });


        if (typeof txtid != "undefined") {
          
          var ROW_STR = txtid.split('_');
          var ROW_ID  = ROW_STR[1];  
          var TotalQty= getArraySum(NewQtyArr); 
          var tvalue= getArraySum(NewRateArr); 
          var totalvalue = parseFloat(tvalue).toFixed(5);
          var rateavg = parseFloat(totalvalue/TotalQty).toFixed(5);
          var current_store=$(hdnstoreid).val(); 
          var result = current_store.split('_');
          var id_number=result[1];
          
   

          $("#TotalHiddenQty_"+ROW_ID).val(TotalQty);
          $("#HiddenRowId_"+ROW_ID).val(NewIdArr);
          bindTotalValue();

          if($.trim($("#Total Transfer Quantity should be equal to Item Quantity."+ROW_ID).val()) != "" && parseFloat($.trim($("#Total Transfer Quantity should be equal to Item Quantity."+ROW_ID).val())) != parseFloat($.trim($("#TotalHiddenQty_"+ROW_ID).val())) ){
              $("#storepopup").hide();
              $("#ProceedBtn").focus();
              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn").hide(); 
              $("#OkBtn1").show();
              $("#AlertMessage").text('Total Transfer Quantity should be equal to Item Quantity.');
              $("#alert").modal('show');
              $("#OkBtn1").focus();
              return false;
          }{
            $("#VALUE_"+id_number).val(totalvalue);
            $("#RATE_"+id_number).val(rateavg);
          bindTotalValue();
          }
        }
        
        $("#storepopup").hide();
      });

      function getArraySum(a){
          var total=0;
          for(var i in a) { 
              total += a[i];
          }
          return total;
      }

      function bindTotalValue()
    {
      var totalvalue = 0.00;
      var tvalue = 0.00;
      var ctvalue = 0.00;
      var ctgstvalue = 0.00;
      $('#example3').find('.participantRow1').each(function()
      {
             
        tvalue = $(this).find('[id*="VALUE"]').val();
        totalvalue = parseFloat(totalvalue) + parseFloat(tvalue);
        totalvalue = parseFloat(totalvalue).toFixed(2);
      });

      $('#TotalValue').val(totalvalue);
    }


	function bindStoreEvents(){
    var store = <?php echo json_encode($objSTORE); ?>;
        $.each( store, function( scskey, scsvalue ) {    
              var batch =   scsvalue.BATCHID_REF;
              var store =   scsvalue.FROM_STID_REF;
              var item =   scsvalue.ITEMID_REF;
   
          $('#storeTable').find('.clsstrid').each(function(){
            var itemid = $(this).find('[id*="strITEMID_REF"]').val();
            var auomid = $(this).find('[id*="AUOM_REF"]').val();
         
            var storeid = $(this).find('[id*="strSTID_REF"]').val();
            var batchid = $(this).find('[id*="strBT_REF"]').val();  
            var txtid = $(this).find('[id*="strDISPATCH_MAIN_QTY"]').attr('id');
           // alert(scsvalue.DISPATCH_MAIN_QTY); 
           // alert(txtid); 
            
            var txtid2 = $(this).find('[id*="DISPATCH_ALT_QTY"]').attr('id');
              if(batchid == batch && storeid == store && itemid == item )
              {
                $('#'+txtid).val(scsvalue.TRANSFER_QTY);                
              }
              var mainqty = $('#'+txtid).val();
            
           
            
            });
          });
		
        $('#storeTable').on('keyup','[id*="strDISPATCH_MAIN_QTY"]',function(event){
          //alert("aa");
			
			var dqty 		=	$(this).val();
			var stockqty 	= 	$(this).parent().parent().find('[id*="strSOTCK"]').val();

			
			if(parseFloat(dqty) > parseFloat(stockqty)){
				$(this).val('');
				$(this).parent().parent().find('[id*="DISPATCH_ALT_QTY"]').val('');
				$("#FocusId").val($(this));
				$("#ProceedBtn").focus();
				$("#YesBtn").hide();
				$("#NoBtn").hide();
				$("#OkBtn").hide();
				$("#OkBtn1").show();
				$("#AlertMessage").text('Transfer Quantity cannot be greater than Stock In Hand.');
				$("#alert").modal('show');
				$("#OkBtn1").focus();
				return false;
			}
			else{
			  var mqty = $(this).parent().parent().find('[id*="CONV_MAIN_QTY"]').val();
			  var aqty = $(this).parent().parent().find('[id*="CONV_ALT_QTY"]').val();
			  var daltqty = parseFloat((dqty * aqty)/mqty).toFixed(3);
			  $(this).parent().parent().find('[id*="DISPATCH_ALT_QTY"]').val(daltqty);
			}
			
        });
    }
	  

// Store Popup Ends


    $(document).ready(function(e) {

      bindTotalValue();  



    // var d = new Date(); 
    // var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
    // $('#STSDT').val(today);

    var lastdt = <?php echo json_encode($objSTORETOTSOTE->STTOST_DOCDT); ?>;
    var sts = <?php echo json_encode($objSTORETOTSOTE); ?>;
    var today = new Date(); 
    var sodate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2);
    if(lastdt < sts.STTOST_DOCDT)
    {
    $('#STSDT').attr('min',lastdt);
    }
    else
    {
      $('#STSDT').attr('min',sts.STTOST_DOCDT);
    }
    $('#STSDT').attr('max',sodate);



      var Material = $("#MaterialCustom").html(); 
     // alert(Material); 
    $('#hdnmaterial').val(Material);
    $("#Row_Count3").val('1');

      




           
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
            var viewURL = '{{route("transaction",[270,"add"])}}';
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
            bindTotalValue();
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
            $clone.find('[id*="ITEMID_REF"]').val('');
            bindTotalValue();
            $tr.closest('table').append($clone);
  
            var rowCount1 = $('#Row_Count2').val();
    		    rowCount1 = parseInt(rowCount1)+1;
            $('#Row_Count2').val(rowCount1);
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
          window.location.href = "{{route('transaction',[270,'add'])}}";
       }//fnUndoYes


       window.fnUndoNo = function (){
         
       }//fnUndoNo







    });
</script>

@endpush @push('bottom-scripts')
<script>

    $( "#btnSave" ).click(function() {
      var formCustomDuty = $("#frm_trn_store");
      if(formCustomDuty.valid()){

     $("#FocusId").val('');

     var DOCNO           =   $.trim($("#DOCNO").val());
     var STSDT          =   $.trim($("#STSDT").val());
     var VALIDITY_FROM          =   $.trim($("#VALIDITY_FROM").val());
     var VFRDT   =   $.trim($("#VALIDITY_FROM").val());
     var VTODT   =   $.trim($("#VALIDITY_TO").val());
     var ITEMCAT_REF          =   $.trim($("#ITEMCAT_REF").val());
     var FROMSTOREID_REF       =   $.trim($("#FROMSTOREID_REF").val());
     var TOSTOREID_REF       =   $.trim($("#TOSTOREID_REF").val());


         //DATE VALIDATION SECTION 
    var objlastSTSDT       =   $.trim($("#objlastSTSDT").val());
    var objlastSTSDT_SHOW  =  moment(objlastSTSDT).format('DD/MM/YYYY');  
    var STSDT_MESSAGE   =   "Selected Date should be equal to or greater than "+objlastSTSDT_SHOW;
    var d = new Date(); 
    var todaydate =   ("0" + (d.getMonth() + 1)).slice(-2) + "/" +('0' + d.getDate()).slice(-2)  + "/" +  d.getFullYear()  ;
    var TODAYDATES  =  moment(todaydate).format('YYYY-MM-DD');
    var objlastSTSDT_SHOW_2  =  moment(todaydate).format('DD/MM/YYYY'); 
    var STSDT_MESSAGE_2   =   "Selected Date should be equal to or less than "+objlastSTSDT_SHOW_2;

     if(DOCNO ===""){
         $("#FocusId").val($("#DOCNO"));
         $("#ProceedBtn").focus();
         $("#YesBtn").hide();
         $("#NoBtn").hide();
         $("#OkBtn1").show();
         $("#AlertMessage").text('Please enter value in DOCNO.');
         $("#alert").modal('show');
         $("#OkBtn1").focus();
         return false;
     }
     else if(STSDT ===""){
         $("#FocusId").val($("#STSDT"));
         $("#ProceedBtn").focus();
         $("#YesBtn").hide();
         $("#NoBtn").hide();
         $("#OkBtn1").show();
         $("#AlertMessage").text('Please select Date.');
         $("#alert").modal('show');
         $("#OkBtn1").focus();
         return false;
     }
     else if(STSDT !="" && objlastSTSDT!="" && STSDT<objlastSTSDT){
        $("#FocusId").val($("#STSDT"));   
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text(STSDT_MESSAGE);
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
    } 
    else if(STSDT !=""  && STSDT>TODAYDATES){
        $("#FocusId").val($("#STSDT")); 
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text(STSDT_MESSAGE_2);
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
    }
    else if(ITEMCAT_REF===''){
        $("#FocusId").val($("#ITEMCAT_REF")); 
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text("Please Select Item Category.");
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
    }    
    else if(FROMSTOREID_REF===''){
        $("#FocusId").val($("#FROMSTOREID_REF")); 
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text("Please Select From Store.");
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
    }    
    else if(TOSTOREID_REF===''){
        $("#FocusId").val($("#TOSTOREID_REF")); 
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text("Please Select To Store.");
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
    }
     

        var allblank = [];
        var allblank2 = [];
        var allblank3 = [];
        var allblank4 = [];
        var allblank5 = [];

            // $('#udfforsebody').find('.form-control').each(function () {
            $('#example3').find('.participantRow1').each(function(){
                if($.trim($(this).find("[id*=ITEMGID_REF]").val())!="")
                {
                    allblank.push('true');


                }
                else
                {
                    allblank.push('false');
                }

                if($.trim($(this).find("[id*=MainItemCode1]").val())!="")
                {
                    allblank2.push('true');


                }
                else
                {
                    allblank2.push('false');
                }
                if($.trim($(this).find("[id*=ITEM_QTY]").val())!="")
                {
                    allblank3.push('true');


                }
                else
                {
                    allblank3.push('false');
                }
                
                
                if($.trim($(this).find("[id*=RATE]").val())!="")
                {
                    allblank4.push('true');


                }
                else
                {
                    allblank4.push('false');
                }
                
                if($.trim($(this).find("[id*=VALUE]").val())!="")
                {
                    allblank5.push('true');

                }
                else
                {
                    allblank5.push('false');
                }

            });



            if(jQuery.inArray("false", allblank) !== -1){
                    $("#alert").modal('show');
                    $("#AlertMessage").text('Please select item group first!.');
                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn1").show();
                    $("#OkBtn1").focus();
                    highlighFocusBtn('activeOk');
                    return false;
                }
                else if(jQuery.inArray("false", allblank2) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Please select Item.');
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
                return false;
                }
                else if(jQuery.inArray("false", allblank3) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Please enter Item Qty.');
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
                return false;
                }
                // else if(jQuery.inArray("false", allblank4) !== -1){
                // $("#alert").modal('show');
                // $("#AlertMessage").text('Item Rate should not be empty.');
                // $("#YesBtn").hide();
                // $("#NoBtn").hide();
                // $("#OkBtn1").show();
                // $("#OkBtn1").focus();
                // highlighFocusBtn('activeOk');
                // return false;
                // }
                // else if(jQuery.inArray("false", allblank5) !== -1){
                // $("#alert").modal('show');
                // $("#AlertMessage").text('Item Value should not be empty.');
                // $("#YesBtn").hide();
                // $("#NoBtn").hide();
                // $("#OkBtn1").show();
                // $("#OkBtn1").focus();
                // highlighFocusBtn('activeOk');
                // return false;
                // }


                else{
                    $("#alert").modal('show');
                    $("#AlertMessage").text('Do you want to save to record.');
                    $("#YesBtn").data("funcname","fnSaveData");  //set dynamic fucntion name
                    $("#YesBtn").focus();
                    $("#OkBtn").hide();
                    highlighFocusBtn('activeYes');
                }
     
    }
    });


    $( "#btnApprove" ).click(function() {
      var formCustomDuty = $("#frm_trn_store");
      if(formCustomDuty.valid()){

     $("#FocusId").val('');

     var DOCNO           =   $.trim($("#DOCNO").val());
     var STSDT          =   $.trim($("#STSDT").val());
     var VALIDITY_FROM          =   $.trim($("#VALIDITY_FROM").val());
     var VFRDT   =   $.trim($("#VALIDITY_FROM").val());
     var VTODT   =   $.trim($("#VALIDITY_TO").val());
     var ITEMCAT_REF          =   $.trim($("#ITEMCAT_REF").val());
     var FROMSTOREID_REF       =   $.trim($("#FROMSTOREID_REF").val());
     var TOSTOREID_REF       =   $.trim($("#TOSTOREID_REF").val());


         //DATE VALIDATION SECTION 
    var objlastSTSDT       =   $.trim($("#objlastSTSDT").val());
    var objlastSTSDT_SHOW  =  moment(objlastSTSDT).format('DD/MM/YYYY');  
    var STSDT_MESSAGE   =   "Selected Date should be equal to or greater than "+objlastSTSDT_SHOW;
    var d = new Date(); 
    var todaydate =   ("0" + (d.getMonth() + 1)).slice(-2) + "/" +('0' + d.getDate()).slice(-2)  + "/" +  d.getFullYear()  ;
    var TODAYDATES  =  moment(todaydate).format('YYYY-MM-DD');
    var objlastSTSDT_SHOW_2  =  moment(todaydate).format('DD/MM/YYYY'); 
    var STSDT_MESSAGE_2   =   "Selected Date should be equal to or less than "+objlastSTSDT_SHOW_2;

     if(DOCNO ===""){
         $("#FocusId").val($("#DOCNO"));
         $("#ProceedBtn").focus();
         $("#YesBtn").hide();
         $("#NoBtn").hide();
         $("#OkBtn1").show();
         $("#AlertMessage").text('Please enter value in DOCNO.');
         $("#alert").modal('show');
         $("#OkBtn1").focus();
         return false;
     }
     else if(STSDT ===""){
         $("#FocusId").val($("#STSDT"));
         $("#ProceedBtn").focus();
         $("#YesBtn").hide();
         $("#NoBtn").hide();
         $("#OkBtn1").show();
         $("#AlertMessage").text('Please select Date.');
         $("#alert").modal('show');
         $("#OkBtn1").focus();
         return false;
     }
     else if(STSDT !="" && objlastSTSDT!="" && STSDT<objlastSTSDT){
        $("#FocusId").val($("#STSDT"));   
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text(STSDT_MESSAGE);
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
    } 
    else if(STSDT !=""  && STSDT>TODAYDATES){
        $("#FocusId").val($("#STSDT")); 
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text(STSDT_MESSAGE_2);
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
    }
    else if(ITEMCAT_REF===''){
        $("#FocusId").val($("#ITEMCAT_REF")); 
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text("Please Select Item Category.");
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
    }    
    else if(FROMSTOREID_REF===''){
        $("#FocusId").val($("#FROMSTOREID_REF")); 
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text("Please Select From Store.");
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
    }    
    else if(TOSTOREID_REF===''){
        $("#FocusId").val($("#TOSTOREID_REF")); 
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text("Please Select To Store.");
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
    }
     

        var allblank = [];
        var allblank2 = [];
        var allblank3 = [];
        var allblank4 = [];
        var allblank5 = [];

            // $('#udfforsebody').find('.form-control').each(function () {
            $('#example3').find('.participantRow1').each(function(){
                if($.trim($(this).find("[id*=ITEMGID_REF]").val())!="")
                {
                    allblank.push('true');


                }
                else
                {
                    allblank.push('false');
                }

                if($.trim($(this).find("[id*=MainItemCode1]").val())!="")
                {
                    allblank2.push('true');


                }
                else
                {
                    allblank2.push('false');
                }
                if($.trim($(this).find("[id*=ITEM_QTY]").val())!="")
                {
                    allblank3.push('true');


                }
                else
                {
                    allblank3.push('false');
                }
                
                
                if($.trim($(this).find("[id*=RATE]").val())!="")
                {
                    allblank4.push('true');


                }
                else
                {
                    allblank4.push('false');
                }
                
                if($.trim($(this).find("[id*=VALUE]").val())!="")
                {
                    allblank5.push('true');

                }
                else
                {
                    allblank5.push('false');
                }

            });



            if(jQuery.inArray("false", allblank) !== -1){
                    $("#alert").modal('show');
                    $("#AlertMessage").text('Please select item group first!.');
                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn1").show();
                    $("#OkBtn1").focus();
                    highlighFocusBtn('activeOk');
                    return false;
                }
                else if(jQuery.inArray("false", allblank2) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Please select Item.');
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
                return false;
                }
                else if(jQuery.inArray("false", allblank3) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Please enter Item Qty.');
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
                return false;
                }
                else if(jQuery.inArray("false", allblank4) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Item Rate should not be empty.');
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
                return false;
                }
                else if(jQuery.inArray("false", allblank5) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Item Value should not be empty.');
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
                return false;
                }


                else{
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
     var trnscForm = $("#frm_trn_store");
    var formData = trnscForm.serialize();
  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });
  $("#btnApprove").hide(); 
  $(".buttonload_approve").show();  
  $("#btnSave").prop("disabled", true);
  $.ajax({
      url:'{{ route("transactionmodify",[270,"Approve"])}}',
      type:'POST',
      data:formData,
      success:function(data) {
        $("#btnApprove").show();  
        $(".buttonload_approve").hide();  
        $("#btnSave").prop("disabled", false);
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
          $("#btnApprove").show();  
          $(".buttonload_approve").hide();  
          $("#btnSave").prop("disabled", false);
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
         var trnosoForm = $("#frm_trn_store");
        var formData = trnosoForm.serialize();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $("#btnSave").hide(); 
    $(".buttonload").show(); 
    $("#btnApprove").prop("disabled", true);
    $.ajax({
        url:'{{ route("transaction",[270,"update"])}}',
        type:'POST',
        data:formData,
        success:function(data) {
          $(".buttonload").hide(); 
          $("#btnSave").show();   
          $("#btnApprove").prop("disabled", false);

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
            $(".buttonload").hide(); 
            $("#btnSave").show();   
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
        window.location.href = '{{route("transaction",[270,"index"]) }}';
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
  //Item Category Dropdown
  let sptid = "#ItemCategoryTable2";
      let sptid2 = "#ItemCategoryTable";
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
        table = document.getElementById("ItemCategoryTable2");
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
        table = document.getElementById("ItemCategoryTable2");
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

  $('#txtItemCatID_popup').click(function(event){
    $("#TotalValue").val('0.00');
    $('#Row_Count3').val('1');
    showSelectedCheck($("#ITEMCAT_REF").val(),"salesperson");
         $("#ItemCatpopup").show();
      });

      $("#ItemID_closePopup").click(function(event){
        $("#ItemCatpopup").hide();
      });

      $(".clsspid").click(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");       

        $("#txtItemCatID_popup").blur();

        var oldItemCatid =   $("#ITEMCAT_REF").val();
            var MaterialClone = $('#hdnmaterial').val();

   
            if (txtval != oldItemCatid)
            {
             
                $('#MaterialCustom').html(MaterialClone);
             //   $('#TotalValue').val('0.00');
               // $('#Row_Count1').val('1');
           
           
            }
            $('#txtItemCatID_popup').val(texdesc);
            $('#ITEMCAT_REF').val(txtval);
            $("#ItemCatpopup").hide();














        
        $("#SalesPersoncodesearch").val(''); 
        $("#SalesPersonnamesearch").val(''); 
      
        event.preventDefault();
      });

      

  //Item Category Dropdown Ends


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

  //To Store Dropdown starts here 
  let tostore = "#ToStoreTable2";
      let tostore2 = "#ToStoreTable";
      let tostoreheaders = document.querySelectorAll(tostore2 + " th");

      // Sort the table element when clicking on the table headers
      tostoreheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(tostore, ".clsspid_tostore", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function ToStoreCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("ToStorecodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("ToStoreTable2");
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

  function ToStoreNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("ToStorenamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("ToStoreTable2");
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

  $('#txtToStore_popup').click(function(event){
    showSelectedCheck($("#TOSTOREID_REF").val(),"tostore");
         $("#ToStore_popup").show();
      });

      $("#ToStore_closePopup").click(function(event){
        $("#ToStore_popup").hide();
      });

      $(".clsspid_tostore").click(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");

        var FromStoreid=$("#FROMSTOREID_REF").val();
        if(FromStoreid!=""){
        if(txtval==FromStoreid){
          $("#ToStore_popup").hide();
          $("#TOSTOREID_REF").val('');
          $('#txtToStore_popup').val('');
          $("#ProceedBtn").focus();
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn1").show();
          $("#AlertMessage").text('Oops! This Store is already selected in From Store');
          $("#alert").modal('show');
          // $("#OkBtn1").focus();
          highlighFocusBtn('activeOk1');
          return false;
          }
        }
        
        $('#txtToStore_popup').val(texdesc);
        $('#TOSTOREID_REF').val(txtval);
        $("#ToStore_popup").hide();
        
        $("#ToStorecodesearch").val(''); 
        $("#ToStorenamesearch").val(''); 
   
        event.preventDefault();
      });

      

  //from Store Dropdown Ends


    // Item popup starts

    $('#MaterialCustom').on('click','[id*="MainItemCode1"]',function(event){  
      var id=$(this).attr('id')
       var result = id.split('_');
       var id_number=result[1];
       var ITEMGID='#ITEMGID_REF_'+id_number;
       var ITEMGROUP_REF = $(ITEMGID).val();
       var ITEMCAT = $("#ITEMCAT_REF").val();
        if(ITEMGROUP_REF==''){
                    $(ITEMGID).focus();
                    // $("[id*=txtlabel]").blur();
                    $("#ProceedBtn").focus();
                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn1").show();
                    $("#AlertMessage").text('Please select item group first.');
                    $("#alert").modal('show');
                   // $("#OkBtn1").focus();
                    highlighFocusBtn('activeOk1');
                    return false;

                  }else if(ITEMGROUP_REF!=''){

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
                      url:'{{route("transaction",[270,"get_items"])}}',
                      type:'POST',
                      data:{'ITEMGID':ITEMGROUP_REF,'ITEMCAT':ITEMCAT},
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
      var id          =   $(this).attr('id');
     var txtval      =   $("#txt"+id+"").val();
     var texdesc     =   $("#txt"+id+"").data("code");
     var texdescname =   $("#txt"+id+"").data("name");
     var partno =   $("#txt"+id+"").data("pt");
     var uom =   $("#txt"+id+"").data("uom");
     var uomno =   $("#txt"+id+"").data("uomno");
     var alt_uomno =   $("#txt"+id+"").data("alt_uomno");
 
     var BATCHNOA =   $("#txt"+id+"").data("batchnoa");
     var alps_partno =   $("#txt"+id+"").data("alps_partno");
     var cutomer_partno =   $("#txt"+id+"").data("customer_partno");
     var oem_partno =   $("#txt"+id+"").data("oem_partno");


     if(BATCHNOA==0){
      $("#SCSTR_"+id_numbers).prop('disabled', true);
     }else{
      $("#SCSTR_"+id_numbers).prop('disabled', false);
     }
 





     var CheckExist_item = [];
                var CheckExist_vendor = [];

                $('#example3').find('.participantRow1').each(function(){

                if($(this).find('[id*="MainItemId1_Ref"]').val() != '')

                {
                var itemid = $(this).find('[id*="MainItemId1_Ref"]').val();
                var vendorid = $(this).find('[id*="ITEMGID_REF"]').val();

                  if(itemid!=''){
                CheckExist_item.push(itemid);
                  }
                  if(vendorid!=''){
                CheckExist_vendor.push(vendorid);
                  }

                }
                });


  if(jQuery.inArray(txtval, CheckExist_item) !== -1 && jQuery.inArray(current_vendorid, CheckExist_vendor) !== -1 ){
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

           $(values).val('');
          $(descriptions).val('');
          $(code).val('');
          $(uomname).val('');
          $(uomref).val('');
          $(alt_uomref).val('');
          $(partno_id).val('');

          $("#Alpspartno_"+id_numbers).val('');
          $("#Custpartno_"+id_numbers).val('');
          $("#OEMpartno_"+id_numbers).val('');

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

          $("#Alpspartno_"+id_numbers).val(alps_partno);
          $("#Custpartno_"+id_numbers).val(cutomer_partno);
          $("#OEMpartno_"+id_numbers).val(oem_partno);

         // var taxstate=''

        //  $("#mainitempopup1").hide();

            }

$("#mainitempopup1").hide();
 return false;

 });
    }




    //Item Group starts
    $('#MaterialCustom').on('click','[id*="ITEMG_CODE"]',function(event){
  
      var ITEMCAT=$("#ITEMCAT_REF").val();
      var FROMSTR=$("#FROMSTOREID_REF").val();
      if(ITEMCAT==''){
        $("#alert").modal('show');
        $("#AlertMessage").text('Please select item category first!.');
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();                  
        highlighFocusBtn('activeOk');
        return false;
      }else if(FROMSTR==''){
        $("#alert").modal('show');
        $("#AlertMessage").text('Please select from Store first!.');
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();                  
        highlighFocusBtn('activeOk');
        return false;
      }else{
        var id=$(this).attr('id');
       var result = id.split('_');
       var id_number=result[2];
       var popup_id='#'+id;

      $(".vendorcls").val(id_number);
          //To check the selected one
    
    var Itemgroupid='#ITEMGID_REF_'+id_number;
    var VALUE=$(Itemgroupid).val(); 
    //alert(VALUE); 

  
    showSelectedCheck(VALUE,"itemgroupname");


      $("#itemgroup_popup").show();
      $("#vendor_close").on("click",function(event){
      $("#itemgroup_popup").hide();
    });

    $('.itemgroupclose').click(function(){
       var id_numbers= $(".vendorcls").val()
        var ITEMGNAME="#ITEMG_CODE_"+id_numbers;
        var ITEMGID_REF="#ITEMGID_REF_"+id_numbers;
  
        var VENDOR_NAME="#VENDOR_NAME_"+id_numbers;
    //item fields
        var ITEMID_REF='#MainItemId1_Ref_'+id_numbers;
        var ITEM_CODE='#MainItemCode1_'+id_numbers;
        var ITEM_NAME='#MainItemName1_'+id_numbers;
        var TAX='#TAX_'+id_numbers;
        var id          =   $(this).attr('id');
        var txtval      =   $("#txt"+id+"").val();
        var CODE     =   $("#txt"+id+"").data("name");

        $(ITEMGID_REF).val(txtval);
        $(ITEMGNAME).val(CODE);
        
        $(this).parent().parent().find('[id*="TotalHiddenQty"]').val('');
        $(this).parent().parent().find('[id*="HiddenRowId"]').val('');
       // var ITEMGROUP_REF = txtval;
       //var ITEMCATEGORY=$("#ITEMCAT_REF").val(); 





        $(ITEMID_REF).val('');
        $(ITEM_CODE).val('');
        $(ITEM_NAME).val('');
        $(TAX).val('');



        $("#itemgroup_popup").hide();

        });
      }
    });

    let billtoid = "#vendor_table2";
      let billtoid2 = "#vendor_table1";
      let billtoheaders = document.querySelectorAll(billtoid2 + " th");

      // Sort the table element when clicking on the table headers
      billtoheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(billtoid, ".itemgroupclose", "td:nth-child(" + (i + 1) + ")");
        });
      });


    function searchVendorCode() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("vendor_codesearch");
    filter = input.value.toUpperCase();
    table = document.getElementById("vendor_table2");
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


    function searchVendorName() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("vendor_namesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("vendor_table2");
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


    //direct cost tab component list ends



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
      function ItemsdrawingMainFunction() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("maindrawingsearch");
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

      function ItemBUFunction() {
	var input, filter, table, tr, td, i, txtValue;
	input = document.getElementById("ItemBUsearch");
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

function ItemAPNFunction() {
	var input, filter, table, tr, td, i, txtValue;
	input = document.getElementById("ItemAPNsearch");
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

function ItemCPNFunction() {
	var input, filter, table, tr, td, i, txtValue;
	input = document.getElementById("ItemCPNsearch");
	filter = input.value.toUpperCase();
	table = document.getElementById("ITEMSCodeTable3");
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

function ItemOEMPNFunction() {
	var input, filter, table, tr, td, i, txtValue;
	input = document.getElementById("ItemOEMPNsearch");
	filter = input.value.toUpperCase();
	table = document.getElementById("ITEMSCodeTable3");
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
