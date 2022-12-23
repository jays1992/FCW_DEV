@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
  <div class="row">
    <div class="col-lg-2"><a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Production Return</a></div>

    <div class="col-lg-10 topnav-pd">
      <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
      <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
      <button class="btn topnavbt" id="btnSave" ><i class="fa fa-floppy-o"></i> Save</button>
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
</div>

<form id="frm_trn_edit"  method="POST">   
  @csrf
  {{isset($objResponse->PRRID[0]) ? method_field('PUT') : '' }}
  <div class="container-fluid filter">
	  <div class="inner-form">
		  <div class="row">
			  <div class="col-lg-2 pl"><p>Doc No*</p></div>
			  <div class="col-lg-2 pl">
          <input {{$ActionStatus}} type="text" name="PRR_DOCNO" id="PRR_DOCNO" value="{{isset($objResponse->PRR_NO) && $objResponse->PRR_NO !=''?$objResponse->PRR_NO:''}}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase"  >
        </div>
			
			  <div class="col-lg-2 pl"><p>Date*</p></div>
			  <div class="col-lg-2 pl">
			    <input {{$ActionStatus}} type="date" name="PRR_DT" id="PRR_DT" onchange="checkPeriodClosing('{{$FormId}}',this.value,1)" value="{{isset($objResponse->PRR_DT) && $objResponse->PRR_DT !=''?$objResponse->PRR_DT:''}}" class="form-control mandatory" >
        </div>

        <div class="col-lg-1 pl"><p>PRO NO</p></div>
        <div class="col-lg-2 pl">
        <input type="text" {{$ActionStatus}} name="Productionpopup" id="txtProductionpopup" class="form-control mandatory" value="{{isset($objPRO->PRO_NO) && $objPRO->PRO_NO !=''?$objPRO->PRO_NO:''}}"  autocomplete="off"  readonly/>
        <input type="hidden" name="PRID_REF" id="PRID_REF" class="form-control" value="{{isset($objResponse->PROID_REF) && $objResponse->PROID_REF !=''?$objResponse->PROID_REF:''}}" autocomplete="off" />
        </div>   

		  </div>
    </div>

    <div class="container-fluid">

<div class="row">
  <ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#Main_Material">Material</a></li>
    <li><a data-toggle="tab" href="#Material">Additional Material Return</a></li>
    <!--<li><a data-toggle="tab" href="#udf">UDF</a></li>-->
  </ul>

  <div class="tab-content">

  <div id="Main_Material" class="tab-pane fade in active">
  <div class="row"><div class="col-lg-4" style="padding-left: 15px;">Note:- 1 row mandatory in Material Tab </div></div>
    <div class="table-responsive table-wrapper-scroll-y" style="height:280px;margin-top:10px;" >
      <table id="Main_example2" class="display nowrap table table-striped table-bordered itemlist w-200" width="100%" style="height:auto !important;">
        <thead id="thead1"  style="position: sticky;top: 0">
            <tr>
            
            <th>Item Code <input class="form-control" type="hidden" name="Main_Row_Count1" id ="Main_Row_Count1" value="{{isset($Main_objMAT)?count($Main_objMAT):1}}" ></th>
            <th>Item Name</th>

            <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</th>
            <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</th>
            <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</th>

           <!-- <th>Item Specifications</th>-->
            <th>Issue Qty</th>
            <th>Store</th>
         
            <!--<th>Main UoM (MU)</th>
            <th>Stock-in-hand</th>-->
            <th>Return Qty(MU)</th>
            <th>Store Name</th>
            <th>Store Value</th>
          <!--  <th>Alt UOM (AU)</th>-->
            <th>Reason of Return Qty</th>
            <th>Remarks</th>
            <th>Action</th>
            </tr>
        </thead>
        <tbody>
        @if(!empty($Main_objMAT))
        @foreach($Main_objMAT as $key => $row)
        <?php
        $store_value  = 0;
        if($row->BATCH_QTY !=''){
          $exp        = explode(",",$row->BATCH_QTY);
          $total      = 0;
          foreach($exp as $val){
            $explode  = explode("_",$val);
            $quantity = isset($explode[1])?$explode[1]:0;
            $rate     = isset($explode[3])?$explode[3]:0;
            $amount   = $quantity*$rate;
            $total    = $total+$amount;   
          }
        }
        $store_value  = number_format($amount, 2, '.', '');;
        ?>
            <tr  class="Main_participantRow">
                <td><input {{$ActionStatus}}  type="text" name={{"Main_popupITEMID_".$key}} id={{"Main_popupITEMID_".$key}} class="form-control" value="{{ $row->ICODE }}"  autocomplete="off"  readonly/></td>
                <td hidden><input type="hidden" name={{"Main_ITEMID_REF_".$key}} id={{"Main_ITEMID_REF_".$key}} class="form-control"  value="{{ $row->ITEMID_REF }}" autocomplete="off" /></td>

                <td><input {{$ActionStatus}}  type="text" name={{"Main_ItemName_".$key}} id={{"Main_ItemName_".$key}} class="form-control" value="{{ $row->ITEM_NAME }}"  autocomplete="off"   readonly/></td>

                <td {{$AlpsStatus['hidden']}}><input  type="text" name="Main_Alpspartno_{{$key}}" id="Main_Alpspartno_{{$key}}" class="form-control"  autocomplete="off" value="{{ isset($row->ALPS_PART_NO)?$row->ALPS_PART_NO:'' }}" readonly/></td>
                <td {{$AlpsStatus['hidden']}}><input  type="text" name="Main_Custpartno_{{$key}}" id="Main_Custpartno_{{$key}}" class="form-control"  autocomplete="off" value="{{ isset($row->CUSTOMER_PART_NO)?$row->CUSTOMER_PART_NO:'' }}" readonly/></td>
                <td {{$AlpsStatus['hidden']}}><input  type="text" name="Main_OEMpartno_{{$key}}" id="Main_OEMpartno_{{$key}}" class="form-control"  autocomplete="off" value="{{ isset($row->OEM_PART_NO)?$row->OEM_PART_NO:'' }}" readonly/></td>


                <td hidden><input {{$ActionStatus}} type="text" name={{"Main_Itemspec_".$key}} id={{"Main_Itemspec_".$key}} value="{{ $row->ITEM_SPECI }}" class="form-control"  autocomplete="off"    /></td>

                
                <td><input {{$ActionStatus}} type="text" name={{"Main_SE_QTY_".$key}} id={{"Main_SE_QTY_".$key}} class="form-control three-digits" readonly onkeypress="return isNumberDecimalKey(event,this)" maxlength="13" value="{{ $row->ISSUEQTY}}" autocomplete="off"  /></td>

                <td align="center"><a {{$ActionStatus}} class="btn checkstore"  id="{{$key}}" ><i class="fa fa-clone"></i></a></td>
                  <td hidden ><input type="text" name="Main_TotalHiddenQty_{{$key}}" id="Main_TotalHiddenQty_{{$key}}" value="{{ $row->RETURNQTY}}" ></td>

                <td hidden ><input type="text" name="Main_HiddenRowId_{{$key}}" id="Main_HiddenRowId_{{$key}}" value="{{ $row->BATCH_QTY}}" ></td>
                          
              <td hidden><input type="text" name="Main_PO_PENDING_QTY_{{$key}}" id="Main_PO_PENDING_QTY_{{$key}}" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" readonly  /></td>                       

                      
                    
                  <td hidden><input {{$ActionStatus}} type="text" name={{"Main_popupMUOM_".$key}} id={{"Main_popupMUOM_".$key}} class="form-control" value="{{ $row->MAIN_UOM_CODE}}"  autocomplete="off"  readonly/></td>
                  <td hidden><input type="hidden" name={{"Main_MAIN_UOMID_REF_".$key}} id={{"Main_MAIN_UOMID_REF_".$key}} value="{{ $row->UOMID_REF}}" class="form-control"   autocomplete="off" /></td>
                    
                  <td hidden><input {{$ActionStatus}} type="text" name="Main_STOCK_INHAND_{{$key}}" id="Main_STOCK_INHAND_{{$key}}" value="" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" readonly  autocomplete="off"   /></td>
                  <td><input readonly {{$ActionStatus}} type="text" name="Main_RECEIVED_QTY_MU_{{$key}}" id="Main_RECEIVED_QTY_MU_{{$key}}" value="{{ $row->RETURNQTY }}" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off"   /></td>
                    
                  <td hidden><input {{$ActionStatus}} type="text" name="Main_popupALTUOM_{{$key}}" id="Main_popupALTUOM_{{$key}}" class="form-control" value=""  autocomplete="off"  readonly/></td>

                  <td hidden><input type="hidden" name="Main_ALT_UOMID_REF_{{$key}}" id="Main_ALT_UOMID_REF_{{$key}}" value="" class="form-control"  autocomplete="off" /></td>
                    
                  <td hidden><input type="text" name="Main_RECEIVED_QTY_AU_{{$key}}" id="Main_RECEIVED_QTY_AU_{{$key}}"  class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" readonly maxlength="13"  autocomplete="off"   /></td>
                    
                  <td hidden><input type="text" name="Main_SHORT_QTY_{{$key}}" id="Main_SHORT_QTY_{{$key}}"  class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off"  readonly  /></td>
                  <td><input {{$ActionStatus}} type="text" name="Main_STORE_NAME_{{$key}}" id="Main_STORE_NAME_{{$key}}" value="{{ isset($row->STORE_NAME)?$row->STORE_NAME:'' }}"  class="form-control w-100" autocomplete="off" readonly  ></td>
                  <td><input {{$ActionStatus}} type="text" name="Main_STORE_VALUE_{{$key}}" id="Main_STORE_VALUE_{{$key}}" value="{{isset($store_value)?$store_value:''}}" class="form-control w-100" autocomplete="off" readonly ></td>
                  <td><input {{$ActionStatus}} type="text" name="Main_REASON_RETURN_QTY_{{$key}}" id="Main_REASON_RETURN_QTY_{{$key}}" value="{{ $row->REASON_RETURN }}" class="form-control"   autocomplete="off"   /></td>
                  <td hidden><input type="hidden" name={{"Main_SO_FQTY_".$key}} id={{"Main_SO_FQTY_".$key}} class="form-control three-digits" maxlength="13"  autocomplete="off"  readonly/></td>
               
              
                  <td><input {{$ActionStatus}}  type="text" name="Main_REMARKS_{{$key}}" id="Main_REMARKS_{{$key}}" value="{{ $row->REMARKS }}" class="form-control w-100" autocomplete="off" ></td>
                  <td align="center" ><button class="btn Main_add Main_material" title="add" data-toggle="tooltip" type="button" ><i class="fa fa-plus"></i></button>
                  <button class="btn Main_remove Main_dmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
            </tr>
          <tr></tr>
          @endforeach 
          @endif
        </tbody>
      </table>
    </div>	
  </div>
 
    <div id="Material" class="tab-pane fade in">
      <div class="table-responsive table-wrapper-scroll-y" style="height:400px;margin-top:10px;" >
        <table id="example2" class="display nowrap table table-striped table-bordered itemlist w-200" width="100%" style="height:auto !important;">
          <thead id="thead1"  style="position: sticky;top: 0">    
          <tr>
              <th hidden><input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1" value="{{isset($objMAT)?count($objMAT):1}}" ></th>
              <th>Item Code</th>
              <th>Item Name</th>

              <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</th>
              <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</th>
              <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</th>

              <th>UOM</th>               
              <th>Store</th>
              <th>Return Qty</th>
              <th>Store Name</th>
              <th>Store Value</th>
              <th>Reason of Return</th>
              <th>Remarks</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
          @if(isset($objMAT) && !empty($objMAT))
            @foreach($objMAT as $key => $row)
            <?php
            $store_value  = 0;
            if($row->BATCH_QTY !=''){
              $exp        = explode(",",$row->BATCH_QTY);
              $total      = 0;
              foreach($exp as $val){
                $explode  = explode("_",$val);
                $quantity = isset($explode[1])?$explode[1]:0;
                $rate     = isset($explode[3])?$explode[3]:0;
                $amount   = $quantity*$rate;
                $total    = $total+$amount;   
              }
            }
            $store_value  = number_format($amount, 2, '.', '');;
            ?>
                <tr  class="participantRow">
                  <td><input {{$ActionStatus}}  type="text" name="popupITEMID_{{$key}}" id="popupITEMID_{{$key}}" value="{{ $row->ICODE }}" class="form-control"  autocomplete="off"  readonly  /></td>
                    
                    <td hidden><input type="hidden" name="ITEMID_REF_{{$key}}" id="ITEMID_REF_{{$key}}" value="{{ $row->ITEMID_REF }}" class="form-control" autocomplete="off" /></td>

                    <td><input {{$ActionStatus}} type="text" name="ItemName_{{$key}}" id="ItemName_{{$key}}" value="{{ $row->ITEM_NAME }}" class="form-control"  autocomplete="off"  readonly  /></td>

                    <td {{$AlpsStatus['hidden']}}><input  type="text" name="Alpspartno_{{$key}}" id="Alpspartno_{{$key}}" class="form-control"  autocomplete="off" value="{{ isset($row->ALPS_PART_NO)?$row->ALPS_PART_NO:'' }}" readonly/></td>
                    <td {{$AlpsStatus['hidden']}}><input  type="text" name="Custpartno_{{$key}}" id="Custpartno_{{$key}}" class="form-control"  autocomplete="off" value="{{ isset($row->CUSTOMER_PART_NO)?$row->CUSTOMER_PART_NO:'' }}" readonly/></td>
                    <td {{$AlpsStatus['hidden']}}><input  type="text" name="OEMpartno_{{$key}}"  id="OEMpartno_{{$key}}" class="form-control"  autocomplete="off" value="{{ isset($row->OEM_PART_NO)?$row->OEM_PART_NO:'' }}" readonly/></td>



                   <td hidden><input type="text" name="Itemspec_{{$key}}" id="Itemspec_{{$key}}" value="{{ $row->ITEM_SPECI }}" class="form-control"  autocomplete="off" readonly   /></td>  

                  <td><input {{$ActionStatus}} type="text" name="popupMUOM_{{$key}}" id="popupMUOM_{{$key}}" class="form-control" value="{{ $row->UOMCODE }}-{{ $row->DESCRIPTIONS }}"  autocomplete="off"  readonly /></td>

                <td hidden><input type="hidden" name="MAIN_UOMID_REF_{{$key}}" id="MAIN_UOMID_REF_{{$key}}" value="{{ $row->UOMID_REF }}" class="form-control"  autocomplete="off" /></td>

                <td hidden><input type="text" name="popupALTUOM_{{$key}}" id="popupALTUOM_{{$key}}" class="form-control"  autocomplete="off"  readonly /></td>
                <td hidden><input type="hidden" name="ALT_UOMID_REF_{{$key}}" id="ALT_UOMID_REF_{{$key}}" class="form-control"  autocomplete="off" /></td>

                <td align="center"><a {{$ActionStatus}} class="btn checkstore"  id="{{$key}}" onclick="clickStoreDetails(this.id,'OUT')" ><i class="fa fa-clone"></i></a></td>
                
                <td><input {{$ActionStatus}} type="text"   name="QTY_{{$key}}" id="QTY_{{$key}}" value="{{ $row->RETURNQTY }}" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" readonly  style="text-align:right;"  /></td>

                <td><input {{$ActionStatus}} type="text" name="STORE_NAME_{{$key}}" id="STORE_NAME_{{$key}}" value="{{ isset($row->STORE_NAME)?$row->STORE_NAME:'' }}"  class="form-control w-100" autocomplete="off" readonly  ></td>
                <td><input {{$ActionStatus}} type="text" name="STORE_VALUE_{{$key}}" id="STORE_VALUE_{{$key}}" value="{{isset($store_value)?$store_value:''}}" class="form-control w-100" autocomplete="off" readonly ></td>

                <td hidden ><input type="hidden" name="TotalHiddenQty_{{$key}}" id="TotalHiddenQty_{{$key}}" value="{{ $row->RETURNQTY }}" ></td>
                <td hidden ><input type="hidden" name="HiddenRowId_{{$key}}" id="HiddenRowId_{{$key}}" value="{{ $row->BATCH_QTY }}" ></td>                

                <td hidden><input {{$ActionStatus}} type="text"  type="text" name="RATE_{{$key}}" id="RATE_{{$key}}" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" style="text-align:right;"  /></td>  
       
                <td><input {{$ActionStatus}} type="text" name="REASON_{{$key}}" id="REASON_{{$key}}" value="{{ $row->REASON_RETURN }}" class="form-control"   autocomplete="off"   /></td>
                <td><input {{$ActionStatus}}  type="text" name="REMARKS_{{$key}}" id="REMARKS_{{$key}}" value="{{ $row->REMARKS }}" class="form-control" autocomplete="off" ></td>

                <td align="center" >
                  <button {{$ActionStatus}} class="btn add material" title="add" data-toggle="tooltip" type="button" ><i class="fa fa-plus"></i></button>
                  <button {{$ActionStatus}} class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button>
                </td>

                </tr>
                @endforeach 
                @else
                <tr  class="participantRow">
                      <td><input  type="text" name="popupITEMID_0" id="popupITEMID_0" class="form-control"  autocomplete="off"  readonly  /></td>
                      <td hidden><input type="hidden" name="ITEMID_REF_0" id="ITEMID_REF_0" class="form-control" autocomplete="off" /></td>

                      <td><input type="text" name="ItemName_0" id="ItemName_0" class="form-control"  autocomplete="off"  readonly  /></td>
                      <td hidden><input type="text" name="Itemspec_0" id="Itemspec_0" class="form-control"  autocomplete="off" readonly   /></td>  

                      <td><input type="text" name="popupMUOM_0" id="popupMUOM_0" class="form-control"  autocomplete="off"  readonly /></td>
                      <td hidden><input type="hidden" name="MAIN_UOMID_REF_0" id="MAIN_UOMID_REF_0" class="form-control"  autocomplete="off" /></td>

                      <td hidden><input type="text" name="popupALTUOM_0" id="popupALTUOM_0" class="form-control"  autocomplete="off"  readonly /></td>
                      <td hidden><input type="hidden" name="ALT_UOMID_REF_0" id="ALT_UOMID_REF_0" class="form-control"  autocomplete="off" /></td>

                      <td align="center"><a class="btn "  id="0" onclick="clickStoreDetails(this.id,'OUT')" ><i class="fa fa-clone"></i></a></td>
                      
                      <td><input type="text"   name="QTY_0" id="QTY_0" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" readonly style="text-align:right;"  /></td>
                      <td><input type="text" name="STORE_NAME_0" id="STORE_NAME_0"  class="form-control w-100" autocomplete="off" readonly  ></td>
                      <td><input type="text" name="STORE_VALUE_0" id="STORE_VALUE_0" class="form-control w-100" autocomplete="off" readonly ></td>

                      <td hidden ><input type="hidden" name="TotalHiddenQty_0" id="TotalHiddenQty_0" ></td>
                      <td hidden ><input type="hidden" name="HiddenRowId_0" id="HiddenRowId_0" ></td>

                      <td hidden><input type="text"  type="text" name="RATE_0" id="RATE_0" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" readonly style="text-align:right;" /></td>
                      <td hidden><input type="text"  name="VALUE_0" id="VALUE_0" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)"   autocomplete="off" readonly  style="text-align:right;" /></td>

                      <td><input  type="text" name="REASON_0" id="REASON_0" class="form-control" autocomplete="off" ></td>
                      <td><input  type="text" name="REMARKS_0" id="REMARKS_0" class="form-control" autocomplete="off" ></td>

                      <td align="center" >
                        <button class="btn add material" title="add" data-toggle="tooltip" type="button" ><i class="fa fa-plus"></i></button>
                        <button class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button>
                      </td>

                      </tr>


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
@endsection

@section('alert')

<!--Main Store Detail--->



<div id="Main_StoreModal" class="modal" role="dialog"  data-backdrop="static">
<div class="modal-dialog modal-md" style="width:80%;z-index:1">
<div class="modal-content">
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" id='Main_StoreModalClose' >&times;</button>
</div>
<div class="modal-body">
  <div class="tablename"><p>Store Details</p></div>
  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="Main_StoreTable" class="display nowrap table  table-striped table-bordered" style="width: 100%;" >
    
    </table>
  </div>
  <div class="cl"></div>
</div>
</div>
</div>
</div>



<!--Additional Store Detail-->
<div id="StoreModal" class="modal" role="dialog"  data-backdrop="static">
<div class="modal-dialog modal-md" style="width:80%;z-index:1">
<div class="modal-content">
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" id='StoreModalClose' >&times;</button>
</div>
<div class="modal-body">
  <div class="tablename"><p>Store Details</p></div>
  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="StoreTable" class="display nowrap table  table-striped table-bordered" style="width: 100%;" ></table>
  </div>
  <div class="cl"></div>
</div>
</div>
</div>
</div>





<!-- Production Popup starts here   -->
<div id="Production_popup" class="modal" role="dialog" data-backdrop="static">
<div class="modal-dialog modal-md" style="width:60%">
  <div class="modal-content">
      <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" id="GL_closePopup1">&times;</button>
      </div>
      <div class="modal-body">
          <div class="tablename"><p>Production No</p></div>
          <div class="single single-select table-responsive table-wrapper-scroll-y my-custom-scrollbar">
          <input type="hidden" class="mainitem_tab1">
              <table id="ProductionOrder" class="display nowrap table table-striped table-bordered" style="width:100%;">
                  <thead>
                      <tr>
                      <th style="width:10%;">Select</th> 
                          <th style="width:30%;"> Doc No</th>
                          <th style="width:30%;"> Doc Date</th>
                          <th style="width:30%;"> Title</th>
                      </tr>
                  </thead>
                  <tbody>
                      <tr>
                      <th style="text-align:center; width:10%;">&#10004;</th>  
                          <td style="width:30%;"> 
                              <input type="text" id="prno" class="form-control" onkeyup="PRNOFunction()"  />
                          </td>
                          <td style="width:30%;">
                              <input type="text" id="prdt" class="form-control" onkeyup="PRDTFunction()"  />
                          </td>
                          <td style="width:30%;">
                              <input type="text" id="prtitle" class="form-control" onkeyup="PRTitleFunction()"  />
                          </td>
                    
                      </tr>
                  </tbody>
              </table>
              <table id="ProductionOrderTable2" class="display nowrap table table-striped table-bordered">
                  <thead id="thead2">
                  <tr>
  
          <td id="Data_seach" colspan="4">please wait...</td>
    </tr>
                  </thead>
                  <tbody id="PROresult">
                     
                  </tbody>
              </table>
          </div>
          <div class="cl"></div>
      </div>
  </div>
</div>
</div>


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


<!--First Material Tab Starts Here -->

<div id="Main_ITEMIDpopup" class="modal" role="dialog"  data-backdrop="static">
<div class="modal-dialog modal-md" style="width:90%;">
<div class="modal-content">
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" id='Main_ITEMID_closePopup' >&times;</button>
</div>
<div class="modal-body">
<div class="tablename"><p>Item Details</p></div>
<div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
<table id="Main_ItemIDTable" class="display nowrap table  table-striped table-bordered" style="width:100%;" >
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
      </td>
</tr>

<tr>
      <th style="width:8%;" id="all-check">Select</th>
      <th style="width:10%;">Item Code</th>
      <th style="width:10%;">Name</th>
      <th style="width:8%;">Main UOM</th>
      <th style="width:8%;">Main QTY</th>
      <th style="width:8%;">Item Group</th>
      <th style="width:8%;">Item Category</th>
      <th style="width:8%;">Business Unit</th>
      <th style="width:8%;" {{$AlpsStatus['hidden']}}>{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</th>
      <th style="width:8%;" {{$AlpsStatus['hidden']}}>{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</th>
      <th style="width:8%;" {{$AlpsStatus['hidden']}}>{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</th>
      <th style="width:8%;">Status</th>
</tr>
</thead>
<tbody>
<tr>
<td style="width:8%;text-align:center;"><input type="checkbox" class="Main_js-selectall" data-target=".Main_js-selectall1" /></td>
<td style="width:10%;">
<input type="text" id="Main_Itemcodesearch" class="form-control" autocomplete="off" onkeyup="Main_ItemCodeFunction()">
</td>
<td style="width:10%;">
<input type="text" id="Main_Itemnamesearch" class="form-control" autocomplete="off" onkeyup="Main_ItemNameFunction()">
</td>
<td style="width:8%;">
<input type="text" id="Main_ItemUOMsearch" class="form-control" autocomplete="off" onkeyup="Main_ItemUOMFunction()">
</td>
<td style="width:8%;">
<input type="text" id="Main_ItemQTYsearch" class="form-control" autocomplete="off" onkeyup="Main_ItemQTYFunction()">
</td>
<td style="width:8%;">
<input type="text" id="Main_ItemGroupsearch" class="form-control" autocomplete="off" onkeyup="Main_ItemGroupFunction()">
</td>
<td style="width:8%;">
<input type="text" id="Main_ItemCategorysearch" class="form-control" autocomplete="off" onkeyup="Main_ItemCategoryFunction()">
</td>

<td style="width:8%;"><input type="text" id="Main_ItemBUsearch" class="form-control" autocomplete="off" onkeyup="ItemBUFunction()"></td>
<td style="width:8%;" {{$AlpsStatus['hidden']}}><input type="text" id="Main_ItemAPNsearch" class="form-control" autocomplete="off" onkeyup="Main_ItemAPNFunction()"></td>
<td style="width:8%;" {{$AlpsStatus['hidden']}}><input type="text" id="Main_ItemCPNsearch" class="form-control" autocomplete="off" onkeyup="Main_ItemCPNFunction()"></td>
<td style="width:8%;" {{$AlpsStatus['hidden']}}><input type="text" id="Main_ItemOEMPNsearch" class="form-control" autocomplete="off" onkeyup="Main_ItemOEMPNFunction()"></td>

<td style="width:8%;">
<input type="text" id="Main_ItemStatussearch" class="form-control" onkeyup="Main_ItemStatusFunction()">
</td>
</tr>
</tbody>
</table>
<table id="Main_ItemIDTable2" class="display nowrap table  table-striped table-bordered" style="width:100%;" >
  <thead id="thead2">

  </thead>
  <tbody id="Main_tbody_ItemID">     
    
  <div class="loader" id="item_loader" style="display:none;"></div>
  </tbody>
</table>
</div>
<div class="cl"></div>
</div>
</div>
</div>
</div>

<!--Second Material Tab Starts Here-->
<div id="ITEMIDpopup" class="modal" role="dialog"  data-backdrop="static">
<div class="modal-dialog modal-md" style="width:90%">
<div class="modal-content" >
<div class="modal-header"><button type="button" class="close" data-dismiss="modal" id='ITEMID_closePopup' >&times;</button></div>
<div class="modal-body">
  <div class="tablename"><p>Item Details</p></div>
  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="ItemIDTable" class="display nowrap table  table-striped table-bordered" style="width:100%" >
      <thead>
        <tr id="none-select" class="searchalldata" hidden>
          <td> 
            <input type="hidden" name="fieldid" id="hdn_ItemID"/>
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
            <input type="hidden" name="STOCK_TYPE" id="STOCK_TYPE"/>
            
          </td>
        </tr>

        <tr>
          <th style="width:8%;" id="all-check">Select</th>
          <th style="width:10%;">Item Code</th>
          <th style="width:10%;">Name</th>
          <th style="width:8%;">Main UOM</th>
          <th style="width:8%;">Rate</th>
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
          <th style="width:8%;text-align:center;">&#10004;</th>
          <td style="width:10%;"><input type="text" id="Itemcodesearch" class="form-control" autocomplete="off" onkeyup="ItemCodeFunction('{{$FormId}}')"></td>
          <td style="width:10%;"><input type="text" id="Itemnamesearch" class="form-control" autocomplete="off" onkeyup="ItemNameFunction('{{$FormId}}')"></td>
          <td style="width:8%;"><input type="text" id="ItemUOMsearch" class="form-control" autocomplete="off" onkeyup="ItemUOMFunction('{{$FormId}}')"></td>
          <td style="width:8%;"><input type="text" id="ItemQTYsearch" class="form-control" autocomplete="off" onkeyup="ItemQTYFunction()"></td>
          <td style="width:8%;"><input type="text" id="ItemGroupsearch" class="form-control" autocomplete="off" onkeyup="ItemGroupFunction('{{$FormId}}')"></td>
          <td style="width:8%;"><input type="text" id="ItemCategorysearch" class="form-control" autocomplete="off" onkeyup="ItemCategoryFunction('{{$FormId}}')"></td>
          <td style="width:8%;"><input type="text" id="ItemBUsearch" class="form-control" autocomplete="off" onkeyup="ItemBUFunction('{{$FormId}}')"></td>
          <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemAPNsearch" class="form-control" autocomplete="off" onkeyup="ItemAPNFunction('{{$FormId}}')"></td>
          <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemCPNsearch" class="form-control" autocomplete="off" onkeyup="ItemCPNFunction('{{$FormId}}')"></td>
          <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemOEMPNsearch" class="form-control" autocomplete="off" onkeyup="ItemOEMPNFunction('{{$FormId}}')"></td>
          <td style="width:8%;"><input type="text" id="ItemStatussearch" class="form-control" autocomplete="off" onkeyup="ItemStatusFunction()"></td>
        </tr>
      </tbody>
    </table>

    <table id="ItemIDTable2" class="display nowrap table  table-striped table-bordered" style="width:100%" >
      <thead id="thead2"></thead>
      <tbody id="tbody_ItemID"></tbody>
    </table>

  </div>
  <div class="cl"></div>
</div>
</div>
</div>
</div>




@endsection

@push('bottom-css')
<style>
.text-danger{
color:red !important;
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
width: 100%;
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
width: 100%;
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
}
#StoreTable {
border-collapse: collapse;
width: 950px;
border: 1px solid #ddd;
font-size: 11px;
}
#StoreTable th {
text-align: center;
padding: 5px;
font-size: 11px;
color: #0f69cc;
font-weight: 600;
}
#StoreTable td {
text-align: center;
padding: 5px;
font-size: 11px;
font-weight: 600;
}

#Main_StoreTable {
border-collapse: collapse;
width: 950px;
border: 1px solid #ddd;
font-size: 11px;
}
#Main_StoreTable th {
text-align: center;
padding: 5px;
font-size: 11px;
color: #0f69cc;
font-weight: 600;
}
#Main_StoreTable td {
text-align: center;
padding: 5px;
font-size: 11px;
font-weight: 600;
}

.qtytext{
display: block;
width: 100%;
height: 24px;
padding: 6px 6px;
font-size: 14px;
line-height: 1.42857143;
color: #555;
background-color: #fff;
background-image: none;
border: 1px solid #ccc;
}
</style>
@endpush

@push('bottom-scripts')
<script>
/*================================== BUTTON FUNCTION ================================*/
$('#btnAdd').on('click', function() {
  var viewURL = '{{route("transaction",[$FormId,"add"])}}';
  window.location.href=viewURL;
});

$('#btnExit').on('click', function() {
  var viewURL = '{{route('home')}}';
  window.location.href=viewURL;
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
}

$("#btnSave").click(function() {
  var formReqData = $("#frm_trn_edit");
  if(formReqData.valid()){
    validateForm('fnSaveData','update');
  }
});

$("#btnApprove").click(function(){
  var formReqData = $("#frm_trn_edit");
  if(formReqData.valid()){
    validateForm('fnApproveData','approve');
  }
});

$("#YesBtn").click(function(){
  $("#alert").modal('hide');
  var customFnName = $("#YesBtn").data("funcname");
  window[customFnName]();
});

$("#NoBtn").click(function(){
  $("#alert").modal('hide');
  $("#LABEL").focus();
});

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
  $(".text-danger").hide();
});

function showError(pId,pVal){
  $("#"+pId+"").text(pVal);
  $("#"+pId+"").show();
}

function getFocus(){
  $("#"+$("#FocusId").val()).focus();
  $("#closePopup").click();
}

function highlighFocusBtn(pclass){
  $(".activeYes").hide();
  $(".activeNo").hide();
  $("."+pclass+"").show();
}

/*================================== Save FUNCTION =================================*/
/*================================== Update FUNCTION =================================*/
window.fnSaveData = function (){

event.preventDefault();

var trnFormReq  = $("#frm_trn_edit");
var formData    = trnFormReq.serialize();

$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});
$("#btnSave").hide(); 
$(".buttonload").show(); 
$("#btnApprove").prop("disabled", true);
$.ajax({
  url:'{{ route("transaction",[$FormId,"update"])}}',
  type:'POST',
  data:formData,
  success:function(data) {
    $(".buttonload").hide(); 
    $("#btnSave").show();   
    $("#btnApprove").prop("disabled", false);

    if(data.errors) {
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").show();
      $("#AlertMessage").text(data.msg);
      $("#alert").modal('show');
      $("#OkBtn").focus();
      $(".text-danger").show();
    }
    else if(data.success) {                   
      console.log("succes MSG="+data.msg);
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").show();
      $("#AlertMessage").text(data.msg);
      $("#alert").modal('show');
      $("#OkBtn").focus();
      $(".text-danger").hide();
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

/*================================== Approve FUNCTION =================================*/
window.fnApproveData = function (){

  event.preventDefault();
var trnFormReq  = $("#frm_trn_edit");
var formData    = trnFormReq.serialize();

$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});
$("#btnApprove").hide(); 
$(".buttonload_approve").show();  
$("#btnSave").prop("disabled", true);
$.ajax({
  url:'{{ route("transaction",[$FormId,"Approve"])}}',
  type:'POST',
  data:formData,
  success:function(data) {
    $("#btnApprove").show();  
    $(".buttonload_approve").hide();  
    $("#btnSave").prop("disabled", false);

    if(data.errors) {
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").show();
      $("#AlertMessage").text(data.msg);
      $("#alert").modal('show');
      $("#OkBtn").focus();
      $(".text-danger").show();
    }
    else if(data.success) {                   
      console.log("succes MSG="+data.msg);
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").show();
      $("#AlertMessage").text(data.msg);
      $("#alert").modal('show');
      $("#OkBtn").focus();
      $(".text-danger").hide();
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
/*================================== VALIDATE FUNCTION =================================*/

function validateForm(actionType,actionMsg){

  $("#FocusId").val('');
var PRR_DOCNO   = $.trim($("#PRR_DOCNO").val());
var PRR_DT   = $.trim($("#PRR_DT").val());
var PRID_REF   = $.trim($("#PRID_REF").val());

if(PRR_DOCNO ===""){
$("#FocusId").val('PRR_DOCNO');
$("#ProceedBtn").focus();
$("#YesBtn").hide();
$("#NoBtn").hide();
$("#OkBtn1").show();
$("#AlertMessage").text('Doc No is required.');
$("#alert").modal('show')
$("#OkBtn1").focus();
return false;
}
else if(PRR_DT ===""){
$("#FocusId").val('PRR_DT');
$("#ProceedBtn").focus();
$("#YesBtn").hide();
$("#NoBtn").hide();
$("#OkBtn1").show();
$("#AlertMessage").text('Please select Date.');
$("#alert").modal('show');
$("#OkBtn1").focus();
return false;
} 

else if(PRID_REF ===""){
$("#FocusId").val('txtProductionpopup');
$("#ProceedBtn").focus();
$("#YesBtn").hide();
$("#NoBtn").hide();
$("#OkBtn1").show();
$("#AlertMessage").text('Please select PRO No');
$("#alert").modal('show');
$("#OkBtn1").focus();
return false;
} 
else{
event.preventDefault();
var allblank1   = [];
var allblank2   = [];
var allblank3   = [];
var allblank4   = [];
var allblank5   = [];
var focustext   = "";

$('#Main_example2').find('.Main_participantRow').each(function(){

if($.trim($(this).find("[id*=Main_ITEMID_REF]").val())!=""){
  allblank1.push('true');

  if($.trim($(this).find("[id*=Main_MAIN_UOMID_REF]").val())!=""){
    allblank2.push('true');

    if($.trim($(this).find('[id*="Main_RECEIVED_QTY_MU"]').val()) != ""){
      allblank3.push('true');
    }
    else{
      allblank3.push('false');
      focustext = $(this).find("[id*=Main_RECEIVED_QTY_MU]").attr('id');
    }  
  }
  else{
      allblank2.push('false');
      focustext = $(this).find("[id*=popupMUOM]").attr('id');
  }    
  
  
  if($.trim($(this).find('[id*="Main_REASON_RETURN_QTY"]').val()) != "" ){
        allblank4.push('true');
      }
      else{
        allblank4.push('false');
        focustext = $(this).find("[id*=Main_REASON_RETURN_QTY]").attr('id');
      }  
}
else{
  allblank1.push('false'); 
  focustext = $(this).find("[id*=popupITEMID]").attr('id');
}




});

if(jQuery.inArray("false", allblank1) !== -1){
$("#FocusId").val(focustext);
$("#alert").modal('show');
$("#AlertMessage").text('Please Select Item In Material');
$("#YesBtn").hide(); 
$("#NoBtn").hide();  
$("#OkBtn1").show();
$("#OkBtn1").focus();
highlighFocusBtn('activeOk');
}
else if(jQuery.inArray("false", allblank2) !== -1){
$("#FocusId").val(focustext);
$("#alert").modal('show');
$("#AlertMessage").text('Main UOM is missing in in material tab.');
$("#YesBtn").hide(); 
$("#NoBtn").hide();  
$("#OkBtn1").show();
$("#OkBtn1").focus();
highlighFocusBtn('activeOk');
}
else if(jQuery.inArray("false", allblank3) !== -1){
$("#FocusId").val(focustext);
$("#alert").modal('show');
$("#AlertMessage").text('Return Qty cannot be zero or blank in material tab.');
$("#YesBtn").hide(); 
$("#NoBtn").hide();  
$("#OkBtn1").show();
$("#OkBtn1").focus();
highlighFocusBtn('activeOk');
}
else if(jQuery.inArray("false", allblank4) !== -1){
$("#FocusId").val(focustext);
$("#alert").modal('show');
$("#AlertMessage").text('Please enter reason of return in material tab.');
$("#YesBtn").hide(); 
$("#NoBtn").hide();  
$("#OkBtn1").show();
$("#OkBtn1").focus();
highlighFocusBtn('activeOk');
}
else if(checkPeriodClosing('{{$FormId}}',$("#PRR_DT").val(),0) ==0){
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
     $("#AlertMessage").text('Do you want to '+actionMsg+' to record.');
     $("#YesBtn").data("funcname",actionType);     
     $("#YesBtn").focus();
     $("#OkBtn").hide();
     highlighFocusBtn('activeYes');
   }
 }
}



/*================================== POPUP SHORTING FUNCTION =================================*/
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


/*================================== ITEM DETAILS =================================*/
let itemtid = "#ItemIDTable2";
let itemtid2 = "#ItemIDTable";
let itemtidheaders = document.querySelectorAll(itemtid2 + " th");

itemtidheaders.forEach(function(element, i) {
element.addEventListener("click", function() {
w3.sortHTML(itemtid, ".clsitemid", "td:nth-child(" + (i + 1) + ")");
});
});

function ItemCodeFunction() {
var input, filter, table, tr, td, i, txtValue;
input = document.getElementById("Itemcodesearch");
filter = input.value.toUpperCase();

if(filter.length == 0)
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

loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,''); 
}
else if(filter.length >= 3)
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
loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,''); 
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

function ItemNameFunction() {
var input, filter, table, tr, td, i, txtValue;
input = document.getElementById("Itemnamesearch");
filter = input.value.toUpperCase();

if(filter.length == 0)
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
loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,''); 
}
else if(filter.length >= 3)
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
loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,''); 
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

function ItemUOMFunction() {
var input, filter, table, tr, td, i, txtValue;
input = document.getElementById("ItemUOMsearch");
filter = input.value.toUpperCase();  
if(filter.length == 0)
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
loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,''); 
}
else if(filter.length >= 3)
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
loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,''); 
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

function ItemGroupFunction() {
var input, filter, table, tr, td, i, txtValue;
input = document.getElementById("ItemGroupsearch");
filter = input.value.toUpperCase();
if(filter.length == 0)
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
loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,''); 
}
else if(filter.length >= 3)
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
loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,''); 
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

function ItemCategoryFunction() {
var input, filter, table, tr, td, i, txtValue;
input = document.getElementById("ItemCategorysearch");
filter = input.value.toUpperCase();
if(filter.length == 0)
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
loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,''); 
}
else if(filter.length >= 3)
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
loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,''); 
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

function ItemBUFunction() {
var input, filter, table, tr, td, i, txtValue;
input = document.getElementById("ItemBUsearch");
filter = input.value.toUpperCase();
if(filter.length == 0)
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
loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,''); 
}
else if(filter.length >= 3)
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
loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,''); 
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

function ItemAPNFunction() {
var input, filter, table, tr, td, i, txtValue;
input = document.getElementById("ItemAPNsearch");
filter = input.value.toUpperCase();
if(filter.length == 0)
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
loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,''); 
}
else if(filter.length >= 3)
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
loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,''); 
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

function ItemCPNFunction() {
var input, filter, table, tr, td, i, txtValue;
input = document.getElementById("ItemCPNsearch");
filter = input.value.toUpperCase();
if(filter.length == 0)
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
loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,''); 
}
else if(filter.length >= 3)
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
loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,'');
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

function ItemOEMPNFunction() {
var input, filter, table, tr, td, i, txtValue;
input = document.getElementById("ItemOEMPNsearch");
filter = input.value.toUpperCase();
if(filter.length == 0)
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
loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,'');
}
else if(filter.length >= 3)
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
loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,'');
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
td = tr[i].getElementsByTagName("td")[11];
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

function loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,STOCK_TYPE){

var STOCK_TYPE  = $("#STOCK_TYPE").val();

$("#tbody_ItemID").html('');
$.ajaxSetup({
headers: {
  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
}
});
$.ajax({
url:'{{route("transaction",[$FormId,"getItemDetails"])}}',
type:'POST',
data:{'taxstate':taxstate,'CODE':CODE,'NAME':NAME,'MUOM':MUOM,'GROUP':GROUP,'CTGRY':CTGRY,'BUNIT':BUNIT,'APART':APART,'CPART':CPART,'OPART':OPART},
success:function(data) {
$("#tbody_ItemID").html(data); 
bindItemEvents(STOCK_TYPE); 
},
error:function(data){
console.log("Error: Something went wrong.");
$("#tbody_ItemID").html('');                        
},
});

}

$('#Material').on('click','[id*="popupITEMID"]',function(event){

var CODE = ''; 
var NAME = ''; 
var MUOM = ''; 
var GROUP = ''; 
var CTGRY = ''; 
var BUNIT = ''; 
var APART = ''; 
var CPART = ''; 
var OPART = ''; 
var STOCK_TYPE = $(this).attr('id').split('_')[1] =="IN"?'_IN':'';
$("#STOCK_TYPE").val(STOCK_TYPE);

loadItem('',CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,STOCK_TYPE);
    
$("#ITEMIDpopup").show();

var id    = $(this).attr('id');
var id2   = $(this).parent().parent().find('[id*="ITEMID_REF'+STOCK_TYPE+'"]').attr('id');
var id3   = $(this).parent().parent().find('[id*="ItemName'+STOCK_TYPE+'"]').attr('id');
var id4   = $(this).parent().parent().find('[id*="Itemspec'+STOCK_TYPE+'"]').attr('id');
var id5   = $(this).parent().parent().find('[id*="popupMUOM'+STOCK_TYPE+'"]').attr('id');
var id6   = $(this).parent().parent().find('[id*="MAIN_UOMID_REF'+STOCK_TYPE+'"]').attr('id');
var id7   = $(this).parent().parent().find('[id*="RATE'+STOCK_TYPE+'"]').attr('id');
var id12  = $(this).parent().parent().find('[id*="TotalHiddenQty'+STOCK_TYPE+'"]').attr('id');
var id13  = $(this).parent().parent().find('[id*="HiddenRowId'+STOCK_TYPE+'"]').attr('id');
var id8   = $(this).parent().parent().find('[id*="popupALTUOM'+STOCK_TYPE+'"]').attr('id');
var id9   = $(this).parent().parent().find('[id*="ALT_UOMID_REF'+STOCK_TYPE+'"]').attr('id');


$('#hdn_ItemID').val(id);
$('#hdn_ItemID2').val(id2);
$('#hdn_ItemID3').val(id3);
$('#hdn_ItemID4').val(id4);
$('#hdn_ItemID5').val(id5);
$('#hdn_ItemID6').val(id6);
$('#hdn_ItemID7').val(id7);
$('#hdn_ItemID12').val(id12);
$('#hdn_ItemID13').val(id13);
$('#hdn_ItemID8').val(id8);
$('#hdn_ItemID9').val(id9);
event.preventDefault();
});

$("#ITEMID_closePopup").click(function(event){
$("#ITEMIDpopup").hide();
});

function bindItemEvents(STOCK_TYPE){

$('#ItemIDTable2').off(); 
$('.js-selectall1').prop('checked', false);

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

  var apartno =  $("#addinfo"+fieldid+"").data("desc101");
  var cpartno =  $("#addinfo"+fieldid+"").data("desc102");
  var opartno =  $("#addinfo"+fieldid+"").data("desc103");

  txtauomqty = (parseFloat(txtmuomqty)/parseFloat(txtmqtyf))*parseFloat(txtauomqty);

  var desc6         =  $("#txt"+fieldid+"").data("desc6");
  var AultUomQty    =  $("#txt"+fieldid+"").data("desc7");
  var PoPendingQty  =  $("#txt"+fieldid+"").data("desc8");
  
  if(intRegex.test(txtauomqty)){
      txtauomqty = (txtauomqty +'.000');
  }

  if(intRegex.test(txtmuomqty)){
    txtmuomqty = (txtmuomqty +'.000');
  }

  
      
      
  if($(this).is(":checked") == true) {

    $('#example2').find('.participantRow').each(function(){

      var itemid      = $(this).find('[id*="ITEMID_REF'+STOCK_TYPE+'"]').val();
      var exist_val   = itemid;

      if(txtval){
        if(desc6 == exist_val){
          $("#ITEMIDpopup").hide();
          $('.js-selectall1').prop('checked', false);
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
          $('#hdn_ItemID12').val('');
          $('#hdn_ItemID13').val('');
          $('#hdn_ItemID8').val('');
          $('#hdn_ItemID9').val('');
         
                   
          txtval = '';
          texdesc = '';
          txtname = '';
          txtspec = '';
          txtmuom = '';
          txtauom = '';
          txtmuomid = '';
          txtauomid = '';
          txtauomqty='';
          txtmuomqty='';
          txtruom = '';
          return false;
        }               
      } 
               
    });

    if($('#hdn_ItemID').val() == "" && txtval != ''){

      var txtid= $('#hdn_ItemID').val();
      var txt_id2= $('#hdn_ItemID2').val();
      var txt_id3= $('#hdn_ItemID3').val();
      var txt_id4= $('#hdn_ItemID4').val();
      var txt_id5= $('#hdn_ItemID5').val();
      var txt_id6= $('#hdn_ItemID6').val();
      var txt_id7= $('#hdn_ItemID7').val();
     

      var txt_id8= $('#hdn_ItemID8').val();
      var txt_id9= $('#hdn_ItemID9').val();
      

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
      $clone.find('[id*="popupITEMID'+STOCK_TYPE+'"]').val(texdesc);
      $clone.find('[id*="ITEMID_REF'+STOCK_TYPE+'"]').val(txtval);
      $clone.find('[id*="ItemName'+STOCK_TYPE+'"]').val(txtname);
      $clone.find('[id*="Itemspec'+STOCK_TYPE+'"]').val(txtspec);
      $clone.find('[id*="popupMUOM'+STOCK_TYPE+'"]').val(txtmuom);
      $clone.find('[id*="MAIN_UOMID_REF'+STOCK_TYPE+'"]').val(txtmuomid);
      $clone.find('[id*="RATE'+STOCK_TYPE+'"]').val(txtmuomqty);

      $clone.find('[id*="popupALTUOM'+STOCK_TYPE+'"]').val(txtauom);
      $clone.find('[id*="ALT_UOMID_REF'+STOCK_TYPE+'"]').val(txtauomid);
     
      
      $clone.find('[id*="TotalHiddenQty'+STOCK_TYPE+'"]').val('');
      $clone.find('[id*="HiddenRowId'+STOCK_TYPE+'"]').val('');

      $clone.find('[id*="Alpspartno"]').val(apartno);
      $clone.find('[id*="Custpartno"]').val(cpartno);
      $clone.find('[id*="OEMpartno"]').val(opartno);

      $clone.find('[id*="REMARKS"]').val('');
      
      $tr.closest('table').append($clone);   
      var rowCount = $('#Row_Count1').val();
        rowCount = parseInt(rowCount)+1;
        $('#Row_Count1').val(rowCount);
        
        $("#ITEMIDpopup").hide();
        $('.js-selectall1').prop('checked', false);
      event.preventDefault();
    }
    else{

      $('#'+$('#hdn_ItemID12').val()).val('');
      $('#'+$('#hdn_ItemID13').val()).val('');

      var txtid= $('#hdn_ItemID').val();
      
      var rowid=txtid.split("_").pop(0);

     
      var txt_id2= $('#hdn_ItemID2').val();
      var txt_id3= $('#hdn_ItemID3').val();
      var txt_id4= $('#hdn_ItemID4').val();
      var txt_id5= $('#hdn_ItemID5').val();
      var txt_id6= $('#hdn_ItemID6').val();
      var txt_id7= $('#hdn_ItemID7').val();
      var txt_id8= $('#hdn_ItemID8').val();
      var txt_id9= $('#hdn_ItemID9').val();
     

      $('#'+txtid).val(texdesc);
      $('#'+txt_id2).val(txtval);
      $('#'+txt_id3).val(txtname);
      $('#'+txt_id4).val(txtspec);
      $('#'+txt_id5).val(txtmuom);
      $('#'+txt_id6).val(txtmuomid);
      $('#'+txt_id7).val(txtmuomqty);

      $('#'+txt_id8).val(txtauom);
      $('#'+txt_id9).val(txtauomid);

      $('#'+txt_id2).parent().parent().find('[id*="Alpspartno"]').val(apartno);
      $('#'+txt_id2).parent().parent().find('[id*="Custpartno"]').val(cpartno);
      $('#'+txt_id2).parent().parent().find('[id*="OEMpartno"]').val(opartno);

      $("#QTY_"+rowid).val('');
      $("#STORE_NAME_"+rowid).val('');
   


      $('#hdn_ItemID').val('');
      $('#hdn_ItemID2').val('');
      $('#hdn_ItemID3').val('');
      $('#hdn_ItemID4').val('');
      $('#hdn_ItemID5').val('');
      $('#hdn_ItemID6').val('');
      $('#hdn_ItemID7').val('');
      
      
      $('#hdn_ItemID12').val('');
      $('#hdn_ItemID13').val('');

      $('#hdn_ItemID8').val('');
      $('#hdn_ItemID9').val('');
     

    }
            
    $("#ITEMIDpopup").hide();
    $('.js-selectall1').prop('checked', false);
    event.preventDefault();
  }
  else if($(this).is(":checked") == false){
      var id = txtval;
      var r_count = $('#Row_Count1').val();
      $('#example2').find('.participantRow').each(function()
      {
        var itemid = $(this).find('[id*="ITEMID_REF'+STOCK_TYPE+'"]').val();
        if(id == itemid)
        {
          var rowCount = $('#Row_Count1').val();
          if (rowCount > 1) {
            $(this).closest('.participantRow').remove(); 
            rowCount = parseInt(rowCount)-1;
          $('#Row_Count1').val(rowCount);
          }
          else 
          {
            $(document).find('.dmaterial').prop('disabled', true);  
            $("#ITEMIDpopup").hide();
            $('.js-selectall1').prop('checked', false);
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
 
  event.preventDefault();

});

}

/*================================== ADD/REMOVE FUNCTION ==================================*/

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
event.preventDefault();
});

$("#Material").on('click', '.remove', function() {
var rowCount = $(this).closest('table').find('.participantRow').length;
if (rowCount > 1) {
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
}
event.preventDefault();
});


function clickStoreDetails(ROW_ID,STOCK_TYPE){

  $("#FocusId").val('');

  var STOCK_TYPE_ID   =   STOCK_TYPE =="IN"?'_IN':'';
  var ITEMID_REF      =   $("#ITEMID_REF"+STOCK_TYPE_ID+"_"+ROW_ID).val();

  if(ITEMID_REF ===""){
    $("#FocusId").val("popupITEMID"+STOCK_TYPE_ID+"_"+ROW_ID);
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select item code.');
    $("#alert").modal('show')
    $("#OkBtn1").focus();
    return false;
  }
  else{
    getStoreDetails(ROW_ID,STOCK_TYPE,STOCK_TYPE_ID);
    $("#StoreModal").show();
    event.preventDefault();
  }
}

function getStoreDetails(ROW_ID,STOCK_TYPE,STOCK_TYPE_ID){

  var ITEMID_REF      = $("#ITEMID_REF"+STOCK_TYPE_ID+"_"+ROW_ID).val();
  var ITEMROWID       = $("#HiddenRowId"+STOCK_TYPE_ID+"_"+ROW_ID).val();
  var MAIN_UOMID_DES  = $("#popupMUOM"+STOCK_TYPE_ID+"_"+ROW_ID).val();
  var MAIN_UOMID_REF  = $("#MAIN_UOMID_REF"+STOCK_TYPE_ID+"_"+ROW_ID).val();
  var ST_ADJUST_TYPE  = STOCK_TYPE;

  $("#StoreTable").html('');
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $.ajax({
    url:'{{route("transaction",[$FormId,"getStoreDetails"])}}',
    type:'POST',
    data:{
      ROW_ID:ROW_ID,
      ITEMID_REF:ITEMID_REF,
      MAIN_UOMID_DES:MAIN_UOMID_DES,
      MAIN_UOMID_REF:MAIN_UOMID_REF,
      ST_ADJUST_TYPE:ST_ADJUST_TYPE,
      ITEMROWID:ITEMROWID,
      ACTION_TYPE:'EDIT'
    },
    success:function(data) {
      $("#StoreTable").html(data);                
    },
    error:function(data){
      console.log("Error: Something went wrong.");
      $("#StoreTable").html('');                        
    },
  }); 
}

$("#StoreModalClose").click(function(event){

  var NewIdArr    = [];
  var ROW_ID      = [];
  var Req         = [];
  var STOCK_TYPE  = [];
  var STORE_NAME  = [];
  var ST_AMOUNT   = [];

  $('#StoreTable').find('.participantRow33').each(function(){

    if($.trim($(this).find("[id*=UserQty]").val())!=""){  
      var UserQty       = parseFloat($.trim($(this).find("[id*=UserQty]").val()));
      var BatchId       = $.trim($(this).find("[id*=BATCHID]").val());
      var ROWID         = $.trim($(this).find("[id*=ROWID]").val());
      var TOTAL_STOCK   = parseFloat($.trim($(this).find("[id*=TOTAL_STOCK]").val()));
      var BATCHNOA      = $.trim($(this).find("[id*=BATCHNOA]").val());
      var STORE         = $.trim($(this).find("[id*=STORE_NAME]").val());
      var ST_RATE       = $.trim($(this).find("[id*=ST_RATE]").val()) !=''?parseFloat($.trim($(this).find("[id*=ST_RATE]").val())):0;

      if(jQuery.inArray(STORE, STORE_NAME) == -1){
        STORE_NAME.push(STORE);
      }

      if($(this).find("[id*=ST_AMOUNT]").val() !=''){
        ST_AMOUNT.push(parseFloat($(this).find("[id*=ST_AMOUNT]").val()));
      }

      STOCK_TYPE.push($(this).find("[id*=STOCK_TYPE]").val());
      ROW_ID.push(ROWID);
      NewIdArr.push(BatchId+"_"+UserQty+"_"+TOTAL_STOCK+"_"+ST_RATE);

      if(UserQty > 0 && ST_RATE == 0){
        Req.push('false');
      }
      else{
        Req.push('true');
      }

    } 

  });  

  var ST_ADJUST_TYPE  = STOCK_TYPE[0];
  var STOCK_TYPE_ID   = ST_ADJUST_TYPE =='IN'?'_IN':'';                     

  var ROW_ID    = ROW_ID[0];
  var QTY       = parseFloat($("#QTY"+STOCK_TYPE_ID+"_"+ROW_ID).val());
  var RATE      = parseFloat($("#RATE"+STOCK_TYPE_ID+"_"+ROW_ID).val());
  var VALUE     = (QTY*RATE);

  $("#HiddenRowId"+STOCK_TYPE_ID+"_"+ROW_ID).val(NewIdArr);
  $("#VALUE"+STOCK_TYPE_ID+"_"+ROW_ID).val(parseFloat(VALUE).toFixed(3));
  $("#STORE_NAME"+STOCK_TYPE_ID+"_"+ROW_ID).val(STORE_NAME);
  $("#STORE_VALUE"+STOCK_TYPE_ID+"_"+ROW_ID).val(parseFloat(getArraySum(ST_AMOUNT)).toFixed(2));

  if(jQuery.inArray("false", Req) !== -1){
    $("#alert").modal('show');
    $("#AlertMessage").text('Please enter rate in store.');
    $("#YesBtn").hide(); 
    $("#NoBtn").hide();  
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk');
  }
  else{
    $("#StoreModal").hide();
    $("#StoreTable").html(''); 
  }

});


function checkStoreQty(ROW_ID,itemid,userQty,key,stock){

  var STOCK_TYPE    = [];

  $('#StoreTable').find('.participantRow33').each(function(){
    var STOCK_TYPE_DATA = $(this).find("[id*=STOCK_TYPE]").val();
    STOCK_TYPE.push(STOCK_TYPE_DATA);
  });

  var ST_ADJUST_TYPE  = STOCK_TYPE[0];
  var STOCK_TYPE_ID   = ST_ADJUST_TYPE =='IN'?'_IN':'';

  var NewQtyArr = [];
  var NewIdArr  = [];

  $('#StoreTable').find('.participantRow33').each(function(){

    if($.trim($(this).find("[id*=UserQty]").val())!=""){  
      var UserQty      = parseFloat($.trim($(this).find("[id*=UserQty]").val()));
      var BatchId      = $.trim($(this).find("[id*=BATCHID]").val());

      NewQtyArr.push(UserQty);
      NewIdArr.push(BatchId+"_"+UserQty);
    }                
  });

  var TotalQty= getArraySum(NewQtyArr); 

  $("#TotalHiddenQty"+STOCK_TYPE_ID+"_"+ROW_ID).val(TotalQty);
  $("#HiddenRowId"+STOCK_TYPE_ID+"_"+ROW_ID).val(NewIdArr);
  $("#QTY"+STOCK_TYPE_ID+"_"+ROW_ID).val(TotalQty);  

  getStoreAmount(key);

}


function isNumberDecimalKey(evt){
var charCode = (evt.which) ? evt.which : event.keyCode
if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
return false;

return true;
}

/*================================== ONLOAD FUNCTION ==================================*/

$(document).ready(function(e) {

  var lastdt = <?php echo json_encode($objlastdt[0]->PRR_DT); ?>;
  var prr = <?php echo json_encode($objResponse); ?>;
//alert(so.SODT); 
  var today = new Date(); 
  var currentdate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2);
  if(lastdt < prr.PRR_DT)
  {
	$('#PRR_DT').attr('min',lastdt);
  }
  else
  {
	  $('#PRR_DT').attr('min',prr.PRR_DT);
  }
    $('#PRR_DT').attr('max',currentdate);

});


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


/*==================================PRODUCTION NO POPUP STARTS HERE====================================*/
let GL = "#ProductionOrderTable2";
let GL2 = "#ProductionOrder";
let GLheaders = document.querySelectorAll(GL2 + " th");
// Sort the table element when clicking on the table headers
GLheaders.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(GL, ".clsspid_reasoncode1", "td:nth-child(" + (i + 1) + ")");
  });
});

function PRNOFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("prno");
  filter = input.value.toUpperCase();
  table = document.getElementById("ProductionOrderTable2");
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

function PRDTFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("prdt");
  filter = input.value.toUpperCase();
  table = document.getElementById("ProductionOrderTable2");
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
function PRTitleFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("prtitle");
  filter = input.value.toUpperCase();
  table = document.getElementById("ProductionOrderTable2");
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

$("#GL_closePopup1").click(function(event){
  $("#Production_popup").hide();
});

function bindProductionEvents(){
$(".clsspid_prr").click(function(){       

  var fieldid = $(this).attr('id');
  var txtval =    $("#txt"+fieldid+"").val();
  var texdesc =   $("#txt"+fieldid+"").data("desc");
  var texcode =   $("#txt"+fieldid+"").data("code"); 
  $('#txtProductionpopup').val(texcode);
  $('#PRID_REF').val(txtval);        
  $("#Production_popup").hide();   
  $("#prno").val(''); 
  $("#prdt").val('');       
  
  //var hdnMaterial = $('#hdnmainmaterial').val();
  //$('#Main_Material').html(hdnMaterial);

  clearGrid(); 


  event.preventDefault();
});
}



$('#txtProductionpopup').on('click',function(event){     
          $("#PROresult").html('');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $("#Data_seach").show();
            $.ajax({
                url:'{{route("transaction",[$FormId,"get_production"])}}',
                type:'POST',
                data:{},
                success:function(data) {                                
                  $("#Data_seach").hide();
                  $("#PROresult").html(data);   
                  showSelectedCheck($("#PRID_REF").val(),"getgl");
                  bindProductionEvents();                                        
                },
                error:function(data){
                  console.log("Error: Something went wrong.");
                  $("#PROresult").html('');                        
                },
            }); 

            showSelectedCheck($("#REASONCODE1_REF").val(),"getgl");
            $("#Production_popup").show();         
});

/*==================================PRODUCTION NO POPUP ENDS HERE====================================*/



/*================================== UDF FUNCTION ==================================*/

// var udfdata = <?php// echo json_encode($objUdfData); ?>;
// var count2  = <?php //echo json_encode($objCountUDF); ?>;

// $('#Row_Count2').val(count2);
// $('#example3').find('.participantRow3').each(function(){

// var txt_id4 = $(this).find('[id*="udfinputid"]').attr('id');
// var udfid   = $(this).find('[id*="UDF"]').val();

// $.each( udfdata, function( seukey, seuvalue ) {
// if(seuvalue.UDFPROID == udfid){

// var txtvaltype2 = seuvalue.VALUETYPE;
// var strdyn2     = txt_id4.split('_');
// var lastele2    = strdyn2[strdyn2.length-1];
// var dynamicid2  = "udfvalue_"+lastele2;
    
// var chkvaltype2 =  txtvaltype2.toLowerCase();
// var strinp2 = '';

// if(chkvaltype2=='date'){
// strinp2 = '<input type="date" placeholder="dd/mm/yyyy" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"  > ';       
// }
// else if(chkvaltype2=='time'){
// strinp2= '<input type="time" placeholder="h:i" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"  > ';
// }
// else if(chkvaltype2=='numeric'){
// strinp2 = '<input type="text" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"   > ';
// }
// else if(chkvaltype2=='text'){
// strinp2 = '<input type="text" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"  > ';          
// }
// else if(chkvaltype2=='boolean'){            
//     strinp2 = '<input type="checkbox" name="'+dynamicid2+ '" id="'+dynamicid2+'" class="" > ';
// }
// else if(chkvaltype2=='combobox'){
// var txtoptscombo2 =   seuvalue.DESCRIPTIONS;
// var strarray2 = txtoptscombo2.split(',');
// var opts2 = '';
// for (var i = 0; i < strarray2.length; i++) {
//     opts2 = opts2 + '<option value="'+strarray2[i]+'">'+strarray2[i]+'</option> ';
// }
// strinp2 = '<select name="'+dynamicid2+ '" id="'+dynamicid2+'" class="form-control" required>'+opts2+'</select>' ;          
// }
// $('#'+txt_id4).html('');  
// $('#'+txt_id4).html(strinp2);
// }
// });
// });







// Store
let sttid = "#STCodeTable2";
let sttid2 = "#STCodeTable";
let stheaders = document.querySelectorAll(sttid2 + " th");

stheaders.forEach(function(element, i) {
element.addEventListener("click", function() {
w3.sortHTML(sttid, ".clsstid", "td:nth-child(" + (i + 1) + ")");
});
});

function STCodeFunction() {
var input, filter, table, tr, td, i, txtValue;
input = document.getElementById("stcodesearch");
filter = input.value.toUpperCase();
table = document.getElementById("STCodeTable2");
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

function STNameFunction() {
var input, filter, table, tr, td, i, txtValue;
input = document.getElementById("stnamesearch");
filter = input.value.toUpperCase();
table = document.getElementById("STCodeTable2");
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

$('#STID_REF_popup').click(function(event){
$("#stidpopup").show();
});

$("#st_closePopup").click(function(event){
$("#stidpopup").hide();
});

$(".clsstid").dblclick(function(){
var fieldid = $(this).attr('id');
var txtval =    $("#txt"+fieldid+"").val();
var texdesc =   $("#txt"+fieldid+"").data("desc");  
$('#STID_REF_popup').val(texdesc);
$('#STID_REF').val(txtval);
$("#stidpopup").hide();

$("#stcodesearch").val(''); 
$("#stnamesearch").val('');

event.preventDefault();
});




/*==================================================FIRST MATERIAL TAB SECTION STARTS HERE ===================================*/
/*=================================================================================*/
/*================================== ITEM DETAILS =================================*/
/*=================================================================================*/

let Main_itemtid = "#Main_ItemIDTable2";
let Main_itemtid2 = "#Main_ItemIDTable";
let Main_itemtidheaders = document.querySelectorAll(Main_itemtid2 + " th");

Main_itemtidheaders.forEach(function(element, i) {
element.addEventListener("click", function() {
w3.sortHTML(Main_itemtid, ".Main_clsitemid", "td:nth-child(" + (i + 1) + ")");
});
});

function Main_ItemCodeFunction() {
var input, filter, table, tr, td, i, txtValue;
input = document.getElementById("Itemcodesearch");
filter = input.value.toUpperCase();
table = document.getElementById("Main_ItemIDTable2");
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

function Main_ItemNameFunction() {
var input, filter, table, tr, td, i, txtValue;
input = document.getElementById("Itemnamesearch");
filter = input.value.toUpperCase();
table = document.getElementById("Main_ItemIDTable2");
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

function Main_ItemUOMFunction() {
var input, filter, table, tr, td, i, txtValue;
input = document.getElementById("ItemUOMsearch");
filter = input.value.toUpperCase();
table = document.getElementById("Main_ItemIDTable2");
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

function Main_ItemQTYFunction() {
var input, filter, table, tr, td, i, txtValue;
input = document.getElementById("ItemQTYsearch");
filter = input.value.toUpperCase();
table = document.getElementById("Main_ItemIDTable2");
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

function Main_ItemGroupFunction() {
var input, filter, table, tr, td, i, txtValue;
input = document.getElementById("ItemGroupsearch");
filter = input.value.toUpperCase();
table = document.getElementById("Main_ItemIDTable2");
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

function Main_ItemCategoryFunction() {
var input, filter, table, tr, td, i, txtValue;
input = document.getElementById("ItemCategorysearch");
filter = input.value.toUpperCase();
table = document.getElementById("Main_ItemIDTable2");
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

function Main_ItemBUFunction() {
var input, filter, table, tr, td, i, txtValue;
input = document.getElementById("ItemBUsearch");
filter = input.value.toUpperCase();
table = document.getElementById("Main_ItemIDTable2");
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

function Main_ItemAPNFunction() {
var input, filter, table, tr, td, i, txtValue;
input = document.getElementById("ItemAPNsearch");
filter = input.value.toUpperCase();
table = document.getElementById("Main_ItemIDTable2");
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

function Main_ItemCPNFunction() {
var input, filter, table, tr, td, i, txtValue;
input = document.getElementById("ItemCPNsearch");
filter = input.value.toUpperCase();
table = document.getElementById("Main_ItemIDTable2");
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

function Main_ItemOEMPNFunction() {
var input, filter, table, tr, td, i, txtValue;
input = document.getElementById("ItemOEMPNsearch");
filter = input.value.toUpperCase();
table = document.getElementById("Main_ItemIDTable2");
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

function Main_ItemStatusFunction() {
var input, filter, table, tr, td, i, txtValue;
input = document.getElementById("ItemStatussearch");
filter = input.value.toUpperCase();
table = document.getElementById("Main_ItemIDTable2");
tr = table.getElementsByTagName("tr");
for (i = 0; i < tr.length; i++) {
td = tr[i].getElementsByTagName("td")[11];
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

$('#Main_Material').on('click','[id*="Main_popupITEMID"]',function(event){

var PRID_REF    = $("#PRID_REF").val();
if(PRID_REF ===""){
$("#YesBtn").hide();
$("#NoBtn").hide();
$("#OkBtn1").show();
$("#AlertMessage").text('Please select PRO NO .');
$("#alert").modal('show')
$("#OkBtn1").focus();
return false;
}else{
//var POID_REF  = $(this).parent().parent().find('[id*="POID_REF"]').val();
$('#item_loader').show();
$("#Main_tbody_ItemID").html('');
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$.ajax({
    url:'{{route("transaction",[$FormId,"Main_getItemDetails"])}}',
    type:'POST',
    data:{'status':'A',PRID_REF:PRID_REF},
    success:function(data) {
      $('#item_loader').hide();
      $("#Main_tbody_ItemID").html(data);    
      Main_bindItemEvents();   
      //$('.js-selectall').prop('disabled', true);                     
    },
    error:function(data){
      console.log("Error: Something went wrong.");
      $('#item_loader').hide();
      $("#Main_tbody_ItemID").html('');                        
    },
}); 
  

$("#Main_ITEMIDpopup").show();
var id = $(this).attr('id');
var id2 = $(this).parent().parent().find('[id*="Main_ITEMID_REF"]').attr('id');
var id3 = $(this).parent().parent().find('[id*="Main_ItemName"]').attr('id');
var id4 = $(this).parent().parent().find('[id*="Main_Itemspec"]').attr('id');
var id5 = $(this).parent().parent().find('[id*="Main_popupMUOM"]').attr('id');
var id6 = $(this).parent().parent().find('[id*="Main_MAIN_UOMID_REF"]').attr('id');
var id7 = $(this).parent().parent().find('[id*="Main_SE_QTY"]').attr('id');
var id11 = $(this).parent().parent().find('[id*="Main_SO_FQTY"]').attr('id');

var id12 = $(this).parent().parent().find('[id*="Main_TotalHiddenQty"]').attr('id');
var id13 = $(this).parent().parent().find('[id*="Main_HiddenRowId"]').attr('id');

var id8 = $(this).parent().parent().find('[id*="Main_popupALTUOM"]').attr('id');
var id9 = $(this).parent().parent().find('[id*="Main_ALT_UOMID_REF"]').attr('id');
var id10 = $(this).parent().parent().find('[id*="Main_RECEIVED_QTY_AU"]').attr('id');
var id14 = $(this).parent().parent().find('[id*="Main_PO_PENDING_QTY"]').attr('id');



$('#hdn_ItemID').val(id);
$('#hdn_ItemID2').val(id2);
$('#hdn_ItemID3').val(id3);
$('#hdn_ItemID4').val(id4);
$('#hdn_ItemID5').val(id5);
$('#hdn_ItemID6').val(id6);
$('#hdn_ItemID7').val(id7);
$('#hdn_ItemID11').val(id11);
$('#hdn_ItemID12').val(id12);
$('#hdn_ItemID13').val(id13);

$('#hdn_ItemID8').val(id8);
$('#hdn_ItemID9').val(id9);
$('#hdn_ItemID10').val(id10);
$('#hdn_ItemID14').val(id14);

event.preventDefault();
}
});

$("#Main_ITEMID_closePopup").click(function(event){
$("#Main_ITEMIDpopup").hide();
});

function Main_bindItemEvents(){

$('#Main_ItemIDTable2').off(); 

$('.Main_js-selectall').change(function(){
  var isChecked = $(this).prop("checked");
  var selector = $(this).data('target');
  $(selector).prop("checked", isChecked);

  $('#Main_ItemIDTable2').find('.Main_clsitemid').each(function(){

    var fieldid = $(this).attr('id');
    var txtval =   $("#txt"+fieldid+"").val();
    var texdesc =  $("#txt"+fieldid+"").data("desc");
    var fieldid2 = $(this).children('[id*="itemname"]').attr('id');
    var txtname =  $("#txt"+fieldid2+"").val();
    var txtspec =  $("#txt"+fieldid2+"").data("desc");
    var fieldid3 = $(this).children('[id*="itemuom"]').attr('id');
    var txtmuomid =  $("#txt"+fieldid3+"").val();
    var txtauom =  $("#txt"+fieldid3+"").data("desc");
    var txtmuom =  $(this).children('[id*="itemuom"]').text().trim();
    var fieldid4 = $(this).children('[id*="uomqty"]').attr('id');
    var txtauomid =  $("#txt"+fieldid4+"").val();
    var txtauomqty =  $("#txt"+fieldid4+"").data("desc");
    var txtmuomqty =  $(this).children('[id*="uomqty"]').text().trim();
    var fieldid5 = $(this).children('[id*="irate"]').attr('id');
    var txtruom =  $("#txt"+fieldid5+"").val();
    var txtmqtyf = $("#txt"+fieldid5+"").data("desc");
    var fieldid6 = $(this).children('[id*="itax"]').attr('id');

    var apartno =  $("#addinfo"+fieldid+"").data("desc101");
    var cpartno =  $("#addinfo"+fieldid+"").data("desc102");
    var opartno =  $("#addinfo"+fieldid+"").data("desc103");

    txtauomqty = (parseFloat(txtmuomqty)/parseFloat(txtmqtyf))*parseFloat(txtauomqty);

    var desc6         =  $("#txt"+fieldid+"").data("desc6");
    var AultUomQty    =  $("#txt"+fieldid+"").data("desc7");
    var PoPendingQty  =  $("#txt"+fieldid+"").data("desc8");
   
    if(intRegex.test(txtauomqty)){
      txtauomqty = (txtauomqty +'.000');
    }

    if(intRegex.test(txtmuomqty)){
      txtmuomqty = (txtmuomqty +'.000');
    }

   
   
    if($(this).find('[id*="Main_chkId"]').is(":checked") == true){

      $('#Main_example2').find('.Main_participantRow').each(function(){

        var itemid = $(this).find('[id*="Main_ITEMID_REF"]').val();
        var exist_val = itemid;

        if(txtval){
          if(desc6 == exist_val){
            $("#Main_ITEMIDpopup").hide();
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
                 $('#hdn_ItemID11').val('');

                 $('#hdn_ItemID12').val('');
                 $('#hdn_ItemID13').val('');

                 $('#hdn_ItemID8').val('');
                 $('#hdn_ItemID9').val('');
                 $('#hdn_ItemID10').val('');
                 $('#hdn_ItemID14').val('');
                
                 txtval = '';
                 texdesc = '';
                 txtname = '';
                 txtspec = '';
                 txtmuom = '';
                 txtauom = '';
                 txtmuomid = '';
                 txtauomid = '';
                 txtauomqty='';
                 txtmuomqty='';
                 txtruom = '';
                 return false;
           }               
      }          
   });


   if($('#hdn_ItemID').val() == "" && txtval != ''){

     var txtid= $('#hdn_ItemID').val();
     var txt_id2= $('#hdn_ItemID2').val();
     var txt_id3= $('#hdn_ItemID3').val();
     var txt_id4= $('#hdn_ItemID4').val();
     var txt_id5= $('#hdn_ItemID5').val();
     var txt_id6= $('#hdn_ItemID6').val();
     var txt_id7= $('#hdn_ItemID7').val();
     var txt_id11= $('#hdn_ItemID11').val();

     var txt_id8= $('#hdn_ItemID8').val();
     var txt_id9= $('#hdn_ItemID9').val();
     var txt_id10= $('#hdn_ItemID10').val();
     var txt_id14= $('#hdn_ItemID14').val();

     var $tr = $('.Main_material').closest('table');
     var allTrs = $tr.find('.Main_participantRow').last();
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

     $clone.find('.Main_remove').removeAttr('disabled'); 
     $clone.find('[id*="Main_popupITEMID"]').val(texdesc);
     $clone.find('[id*="Main_ITEMID_REF"]').val(txtval);
     $clone.find('[id*="Main_ItemName"]').val(txtname);
     $clone.find('[id*="Main_Itemspec"]').val(txtspec);
     $clone.find('[id*="Main_popupMUOM"]').val(txtmuom);
     $clone.find('[id*="Main_MAIN_UOMID_REF"]').val(txtmuomid);
     $clone.find('[id*="Main_SE_QTY"]').val(txtmuomqty);

     $clone.find('[id*="Main_popupALTUOM"]').val(txtauom);
     $clone.find('[id*="Main_ALT_UOMID_REF"]').val(txtauomid);
     $clone.find('[id*="Main_RECEIVED_QTY_AU"]').val(AultUomQty);
     $clone.find('[id*="Main_PO_PENDING_QTY"]').val(PoPendingQty);

     $clone.find('[id*="Main_Alpspartno"]').val(apartno);
      $clone.find('[id*="Main_Custpartno"]').val(cpartno);
      $clone.find('[id*="Main_OEMpartno"]').val(opartno);
     
     $clone.find('[id*="Main_TotalHiddenQty"]').val('');
     $clone.find('[id*="Main_HiddenRowId"]').val('');

     $clone.find('[id*="Main_REMARKS"]').val('');
     
     $tr.closest('table').append($clone);   
     var rowCount = $('#Main_Row_Count1').val();
       rowCount = parseInt(rowCount)+1;
       $('#Main_Row_Count1').val(rowCount);
       
       $("#Main_ITEMIDpopup").hide();
     event.preventDefault();
   }
   else{

     $('#'+$('#hdn_ItemID12').val()).val('');
     $('#'+$('#hdn_ItemID13').val()).val('');

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
     var txt_id14= $('#hdn_ItemID14').val();

     $('#'+txtid).val(texdesc);
     $('#'+txt_id2).val(txtval);
     $('#'+txt_id3).val(txtname);
     $('#'+txt_id4).val(txtspec);
     $('#'+txt_id5).val(txtmuom);
     $('#'+txt_id6).val(txtmuomid);
     $('#'+txt_id7).val(txtmuomqty);

     $('#'+txt_id8).val(txtauom);
     $('#'+txt_id9).val(txtauomid);
     $('#'+txt_id10).val(AultUomQty);
     $('#'+txt_id14).val(PoPendingQty);

     $('#'+txt_id2).parent().parent().find('[id*="Main_Alpspartno"]').val(apartno);
     $('#'+txt_id2).parent().parent().find('[id*="Main_Custpartno"]').val(cpartno);
     $('#'+txt_id2).parent().parent().find('[id*="Main_OEMpartno"]').val(opartno);


     $('#hdn_ItemID').val('');
     $('#hdn_ItemID2').val('');
     $('#hdn_ItemID3').val('');
     $('#hdn_ItemID4').val('');
     $('#hdn_ItemID5').val('');
     $('#hdn_ItemID6').val('');
     $('#hdn_ItemID7').val('');
     $('#hdn_ItemID11').val('');
     
     $('#hdn_ItemID12').val('');
     $('#hdn_ItemID13').val('');

     $('#hdn_ItemID8').val('');
     $('#hdn_ItemID9').val('');
     $('#hdn_ItemID10').val('');
     $('#hdn_ItemID14').val('');

   }

                 
   $("#Main_ITEMIDpopup").hide();
   event.preventDefault();
  }
  else if($(this).find('[id*="Main_chkId"]').is(":checked") == false)
  {
    var id = txtval;
    var r_count = $('#Main_Row_Count1').val();
    $('#Main_example2').find('.Main_participantRow').each(function()
    {
      var itemid = $(this).find('[id*="Main_ITEMID_REF"]').val();
      if(id == itemid)
      {
         var rowCount = $('#Main_Row_Count1').val();
         if (rowCount > 1) {
           $(this).closest('.Main_participantRow').remove(); 
           rowCount = parseInt(rowCount)-1;
         $('#Main_Row_Count1').val(rowCount);
         }
         else 
         {
           $(document).find('.Main_dmaterial').prop('disabled', true);  
           $("#Main_ITEMIDpopup").hide();
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
  
   event.preventDefault();
 });

  $('.Main_js-selectall').prop("checked", false);   
  $("#Main_ITEMIDpopup").hide();

});



    $('[id*="Main_chkId"]').change(function(){
   
      var fieldid = $(this).parent().parent().attr('id');
      var txtval =   $("#txt"+fieldid+"").val();
      var texdesc =  $("#txt"+fieldid+"").data("desc");
      var fieldid2 = $(this).parent().parent().children('[id*="itemname"]').attr('id');
      var txtname =  $("#txt"+fieldid2+"").val();
      var txtspec =  $("#txt"+fieldid2+"").data("desc");
      var fieldid3 = $(this).parent().parent().children('[id*="itemuom"]').attr('id');
      var txtmuomid =  $("#txt"+fieldid3+"").val();
      var txtauom =  $("#txt"+fieldid3+"").data("desc");
      var txtmuom =  $(this).parent().parent().children('[id*="itemuom"]').text().trim();
      var fieldid4 = $(this).parent().parent().children('[id*="uomqty"]').attr('id');
      var txtauomid =  $("#txt"+fieldid4+"").val();
      var txtauomqty =  $("#txt"+fieldid4+"").data("desc");
      var txtmuomqty =  $(this).parent().parent().children('[id*="uomqty"]').text().trim();
      var fieldid5 = $(this).parent().parent().children('[id*="irate"]').attr('id');
      var txtruom =  $("#txt"+fieldid5+"").val();
      var txtmqtyf = $("#txt"+fieldid5+"").data("desc");
      var fieldid6 = $(this).parent().parent().children('[id*="itax"]').attr('id');

      var apartno =  $("#addinfo"+fieldid+"").data("desc101");
      var cpartno =  $("#addinfo"+fieldid+"").data("desc102");
      var opartno =  $("#addinfo"+fieldid+"").data("desc103");

      txtauomqty = (parseFloat(txtmuomqty)/parseFloat(txtmqtyf))*parseFloat(txtauomqty);

      var desc6         =  $("#txt"+fieldid+"").data("desc6");
       var AultUomQty    =  $("#txt"+fieldid+"").data("desc7");
      var PoPendingQty  =  $("#txt"+fieldid+"").data("desc8");
      
      if(intRegex.test(txtauomqty)){
          txtauomqty = (txtauomqty +'.000');
      }

      if(intRegex.test(txtmuomqty)){
        txtmuomqty = (txtmuomqty +'.000');
      }




 
     if($(this).is(":checked") == true) {

      $('#Main_example2').find('.Main_participantRow').each(function(){
       
        var itemid = $(this).find('[id*="Main_ITEMID_REF"]').val();
            
         var exist_val=itemid;

         if(txtval){
              if(desc6 == exist_val){
                $("#Main_ITEMIDpopup").hide();
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
                    $('#hdn_ItemID11').val('');

                    $('#hdn_ItemID12').val('');
                    $('#hdn_ItemID13').val('');

                    $('#hdn_ItemID8').val('');
                    $('#hdn_ItemID9').val('');
                    $('#hdn_ItemID10').val('');
                    $('#hdn_ItemID14').val('');
                   
                    txtval = '';
                    texdesc = '';
                    txtname = '';
                    txtspec = '';
                    txtmuom = '';
                    txtauom = '';
                    txtmuomid = '';
                    txtauomid = '';
                    txtauomqty='';
                    txtmuomqty='';
                    txtruom = '';
                    return false;
              }               
         }          
      });


      if($('#hdn_ItemID').val() == "" && txtval != ''){
       

        var txtid= $('#hdn_ItemID').val();
        var txt_id2= $('#hdn_ItemID2').val();
        var txt_id3= $('#hdn_ItemID3').val();
        var txt_id4= $('#hdn_ItemID4').val();
        var txt_id5= $('#hdn_ItemID5').val();
        var txt_id6= $('#hdn_ItemID6').val();
        var txt_id7= $('#hdn_ItemID7').val();
        var txt_id11= $('#hdn_ItemID11').val();

        var txt_id8= $('#hdn_ItemID8').val();
        var txt_id9= $('#hdn_ItemID9').val();
        var txt_id10= $('#hdn_ItemID10').val();
        var txt_id14= $('#hdn_ItemID14').val();

        var $tr = $('.Main_material').closest('table');
        var allTrs = $tr.find('.Main_participantRow').last();
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

        $clone.find('.Main_remove').removeAttr('disabled'); 
        $clone.find('[id*="Main_popupITEMID"]').val(texdesc);
        $clone.find('[id*="Main_ITEMID_REF"]').val(txtval);
        $clone.find('[id*="Main_ItemName"]').val(txtname);
        $clone.find('[id*="Main_Itemspec"]').val(txtspec);
        $clone.find('[id*="Main_popupMUOM"]').val(txtmuom);
        $clone.find('[id*="Main_MAIN_UOMID_REF"]').val(txtmuomid);
        $clone.find('[id*="Main_SE_QTY"]').val(txtmuomqty);


        $clone.find('[id*="Main_popupALTUOM"]').val(txtauom);
        $clone.find('[id*="Main_ALT_UOMID_REF"]').val(txtauomid);
        $clone.find('[id*="Main_RECEIVED_QTY_AU"]').val(AultUomQty);
        $clone.find('[id*="Main_PO_PENDING_QTY"]').val(PoPendingQty);

        $clone.find('[id*="Main_Alpspartno"]').val(apartno);
        $clone.find('[id*="Main_Custpartno"]').val(cpartno);
        $clone.find('[id*="Main_OEMpartno"]').val(opartno);
        
        $clone.find('[id*="Main_TotalHiddenQty"]').val('');
        $clone.find('[id*="Main_HiddenRowId"]').val('');

        $clone.find('[id*="Main_REMARKS"]').val('');
        
        $tr.closest('table').append($clone);   
        var rowCount = $('#Main_Row_Count1').val();
   
          rowCount = parseInt(rowCount)+1;
          $('#Main_Row_Count1').val(rowCount);
          
          $("#Main_ITEMIDpopup").hide();
        event.preventDefault();
      }
      else{

        $('#'+$('#hdn_ItemID12').val()).val('');
        $('#'+$('#hdn_ItemID13').val()).val('');

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
        var txt_id14= $('#hdn_ItemID14').val();

        $('#'+txtid).val(texdesc);
        $('#'+txt_id2).val(txtval);
        $('#'+txt_id3).val(txtname);
        $('#'+txt_id4).val(txtspec);
        $('#'+txt_id5).val(txtmuom);
        $('#'+txt_id6).val(txtmuomid);
        $('#'+txt_id7).val(txtmuomqty);
        
  
        $('#'+txt_id8).val(txtauom);
        $('#'+txt_id9).val(txtauomid);
        $('#'+txt_id10).val(AultUomQty);
        $('#'+txt_id14).val(PoPendingQty);

        $('#'+txt_id2).parent().parent().find('[id*="Main_Alpspartno"]').val(apartno);
        $('#'+txt_id2).parent().parent().find('[id*="Main_Custpartno"]').val(cpartno);
        $('#'+txt_id2).parent().parent().find('[id*="Main_OEMpartno"]').val(opartno);

        $('[id*="Main_RECEIVED_QTY_MU"]').val('');


        $('#hdn_ItemID').val('');
        $('#hdn_ItemID2').val('');
        $('#hdn_ItemID3').val('');
        $('#hdn_ItemID4').val('');
        $('#hdn_ItemID5').val('');
        $('#hdn_ItemID6').val('');
        $('#hdn_ItemID7').val('');
        $('#hdn_ItemID11').val('');
        
        $('#hdn_ItemID12').val('');
        $('#hdn_ItemID13').val('');

        $('#hdn_ItemID8').val('');
        $('#hdn_ItemID9').val('');
        $('#hdn_ItemID10').val('');
        $('#hdn_ItemID14').val('');

      }

                    
      $("#Main_ITEMIDpopup").hide();
      event.preventDefault();
     }
     else 
     {
      var id = txtval;
       var r_count = $('#Main_Row_Count1').val();
       $('#Main_example2').find('.Main_participantRow').each(function()
       {
         var itemid = $(this).find('[id*="Main_ITEMID_REF"]').val();        
     
         if(id == itemid)           {
            var rowCount = $('#Main_Row_Count1').val();          

            if (rowCount > 1) {
              $(this).closest('.Main_participantRow').remove(); 
              rowCount = parseInt(rowCount)-1;
            $('#Main_Row_Count1').val(rowCount);
            }
            else 
            {
              $(document).find('.Main_dmaterial').prop('disabled', true);  
              $("#Main_ITEMIDpopup").hide();
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
      $('.Main_remove').removeAttr('disabled'); 
     
      event.preventDefault();
    });
  }




/*================================== ADD/REMOVE FUNCTION for FIRST MATERIAL TAB ==================================*/

$("#Main_Material").on('click', '.Main_add', function() {
var $tr = $(this).closest('table');
var allTrs = $tr.find('.Main_participantRow').last();
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
var rowCount1 = $('#Main_Row_Count1').val();
rowCount1 = parseInt(rowCount1)+1;
$('#Main_Row_Count1').val(rowCount1);
$clone.find('.Main_remove').removeAttr('disabled'); 
event.preventDefault();
});

$("#Main_Material").on('click', '.Main_remove', function() {
var rowCount = $(this).closest('table').find('.Main_participantRow').length;
if (rowCount > 1) {
$(this).closest('.Main_participantRow').remove();     
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



/*=================================================================================*/
/*================================== STORE DETAILS ================================*/
/*=================================================================================*/

$("#Main_example2").on('click', '[class*="checkstore"]', function() {

  var ROW_ID      = $(this).attr('id');
  var ITEMID_REF  = $("#Main_ITEMID_REF_"+ROW_ID).val();

  if(ITEMID_REF ===""){
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select item code.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else{
    Main_getStoreDetails(ROW_ID);
    $("#Main_StoreModal").show();
    event.preventDefault();
  }

});

function Main_getStoreDetails(ROW_ID){

  //var RGP_NO          = $("#Main_RGP_NO_"+ROW_ID).val();
  var ITEMID_REF      = $("#Main_ITEMID_REF_"+ROW_ID).val();
  var ITEMROWID       = $("#Main_HiddenRowId_"+ROW_ID).val();
  var MAIN_UOMID_DES  = $("#Main_popupMUOM_"+ROW_ID).val();
  var MAIN_UOMID_REF  = $("#Main_MAIN_UOMID_REF_"+ROW_ID).val();
  var ALT_UOMID_DES   = $("#Main_popupALTUOM_"+ROW_ID).val();
  var ALT_UOMID_REF   = $("#Main_ALT_UOMID_REF_"+ROW_ID).val();

  $("#Main_StoreTable").html('');
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $.ajax({
  url:'{{route("transaction",[$FormId,"Main_getStoreDetails"])}}',
  type:'POST',
  data:{
    ROW_ID:ROW_ID,
    //RGP_NO:RGP_NO,
    ITEMID_REF:ITEMID_REF,
    MAIN_UOMID_DES:MAIN_UOMID_DES,
    MAIN_UOMID_REF:MAIN_UOMID_REF,
    ALT_UOMID_DES:ALT_UOMID_DES,
    ALT_UOMID_REF:ALT_UOMID_REF,
    ITEMROWID:ITEMROWID,
    ACTION_TYPE:'EDIT'
    },
  success:function(data) {
    $("#Main_StoreTable").html(data);                
  },
  error:function(data){
    console.log("Error: Something went wrong.");
    $("#Main_StoreTable").html('');                        
  },
  }); 
}


$("#Main_StoreModalClose").click(function(event){

  var Total_Stock_Inhand  = [];
  var NewIdArr            = [];
  var ROW_ID              = [];
  var Req                 = [];
  var STORE_NAME          = [];
  var ST_AMOUNT           = [];

  $('#Main_StoreTable').find('.Main_participantRow333').each(function(){

    Total_Stock_Inhand.push(parseFloat($.trim($(this).find("[id*=TOTAL_STOCK]").val())));

    if($.trim($(this).find("[id*=UserQty]").val())!=""){  
      var UserQty      = parseFloat($.trim($(this).find("[id*=UserQty]").val()));
      var BatchId      = $.trim($(this).find("[id*=BATCHID]").val());
      var ROWID        = $.trim($(this).find("[id*=ROWID]").val());
      var TOTAL_STOCK  = parseFloat($.trim($(this).find("[id*=TOTAL_STOCK]").val()));
      var BATCHNOA     = $.trim($(this).find("[id*=BATCHNOA]").val());
      var STORE        = $.trim($(this).find("[id*=STORE_NAME]").val());
      var ST_RATE      = $.trim($(this).find("[id*=ST_RATE]").val()) !=''?parseFloat($.trim($(this).find("[id*=ST_RATE]").val())):0;

      if(jQuery.inArray(STORE, STORE_NAME) == -1){
        STORE_NAME.push(STORE);
      }

      if($(this).find("[id*=ST_AMOUNT]").val() !=''){
        ST_AMOUNT.push(parseFloat($(this).find("[id*=ST_AMOUNT]").val()));
      }
      
      ROW_ID.push(ROWID);
      NewIdArr.push(BatchId+"_"+UserQty+"_"+TOTAL_STOCK+"_"+ST_RATE);

      if(UserQty > 0 && ST_RATE == 0){
        Req.push('false');
      }
      else{
        Req.push('true');
      }

    } 

  });                       

  var ROW_ID                  = ROW_ID[0];
  var Total_Stock_Inhand_Sum  = getArraySum(Total_Stock_Inhand);

  $("#Main_HiddenRowId_"+ROW_ID).val(NewIdArr);
  $("#Main_STOCK_INHAND_"+ROW_ID).val(parseFloat(Total_Stock_Inhand_Sum).toFixed(3));
  $("#Main_STORE_NAME_"+ROW_ID).val(STORE_NAME);
  $("#Main_STORE_VALUE_"+ROW_ID).val(parseFloat(getArraySum(ST_AMOUNT)).toFixed(2));

  if(jQuery.inArray("false", Req) !== -1){
    $("#alert").modal('show');
    $("#AlertMessage").text('Please enter rate in store.');
    $("#YesBtn").hide(); 
    $("#NoBtn").hide();  
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk');
  }
  else{
    $("#Main_StoreModal").hide();
    $("#Main_StoreTable").html(''); 
  }

});


function Main_checkStoreQty(ROW_ID,itemid,userQty,key,stock){

  var NewQtyArr = [];
  var NewIdArr  = [];

  $('#Main_StoreTable').find('.Main_participantRow333').each(function(){

    if($.trim($(this).find("[id*=UserQty]").val())!=""){  
      var UserQty      =  parseFloat($.trim($(this).find("[id*=UserQty]").val()));
      var BatchId      =  $.trim($(this).find("[id*=BATCHID]").val());

      NewQtyArr.push(UserQty);
      NewIdArr.push(BatchId+"_"+UserQty);
    }                
  });

  var TotalQty  = getArraySum(NewQtyArr); 
  var BillQty   = parseFloat($.trim($("#Main_SE_QTY_"+ROW_ID).val()));
  var ShortQty  = (BillQty-TotalQty);

  if(BillQty < TotalQty){
    $("#FocusId").val("Main_SE_QTY_"+ROW_ID);
    $("#Main_RECEIVED_QTY_MU_"+ROW_ID).val(''); 
    $("#UserQty_"+key).val('');  
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Return Qty should not be greater than Issue Qty .');
    $("#alert").modal('show')
    $("#OkBtn1").focus();
    return false;
  }
  else{
    $("#Main_TotalHiddenQty_"+ROW_ID).val(TotalQty);
    $("#Main_HiddenRowId_"+ROW_ID).val(NewIdArr);
    $("#Main_RECEIVED_QTY_MU_"+ROW_ID).val(TotalQty);  
    $("#Main_SHORT_QTY_"+ROW_ID).val(ShortQty);
  }

  getStoreAmount(key);
  
}

function getArraySum(a){
  var total=0;
  for(var i in a) { 
    total += a[i];
  }
  return total;
}

function isNumberDecimalKey(evt){
  var charCode = (evt.which) ? evt.which : event.keyCode
  if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
  return false;

  return true;
}

function clearGrid(){
  $('#Main_example2').find('.Main_participantRow').each(function(){
    var rowCount = $(this).closest('table').find('.Main_participantRow').length;
    $('#Main_Row_Count1').val(rowCount);
    $(this).closest('.Main_participantRow').find('input:text').val('');
    $(this).closest('.Main_participantRow').find('input:hidden').val('');
    if (rowCount > 1) {
      $(this).closest('.Main_participantRow').remove();  
    } 
  });
}

function getStoreAmount(id){
  var qty   = parseFloat($("#UserQty_"+id).val());
  var rate  = parseFloat($("#ST_RATE_"+id).val());

  amount    = parseFloat(qty*rate).toFixed(2);
  amount    = amount > 0 ?amount:'';
  $("#ST_AMOUNT_"+id).val(amount);
}
</script>
@endpush
