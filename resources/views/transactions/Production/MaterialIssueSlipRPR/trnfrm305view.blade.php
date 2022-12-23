@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
        <div class="row">
            <div class="col-lg-2">
            <a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Material Issue Slip against RPR</a>
            </div>

            <div class="col-lg-10 topnav-pd">
                    <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                    <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
                    <button class="btn topnavbt" id="btnSaveSE" disabled="disabled"><i class="fa fa-save"></i> Save</button>
                    <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                    <button class="btn topnavbt" id="btnPrint" ><i class="fa fa-print"></i> Print</button>
                    <button class="btn topnavbt" id="btnUndo"  disabled="disabled"><i class="fa fa-undo"></i> Undo</button>
                    <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                    <button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-lock"></i> Approved</button>
                    <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                    <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
            </div>
        </div>
</div>

<form id="frm_trn_view"  method="POST">   
  @csrf
  {{isset($objResponse->MISRID) ? method_field('PUT') : '' }}
  <div class="container-fluid filter">
<div class="inner-form">

  <div class="row">

    <div class="col-lg-1 pl"><p>MISR No*</p></div>
    <div class="col-lg-2 pl">
      <input type="hidden" name="MISRID" id="MISRID" value="{{isset($objResponse->MISRID) && $objResponse->MISRID !=''?$objResponse->MISRID:''}}" class="form-control"  >
        <input disabled type="text" name="MISRNO" id="MISRNO" value="{{isset($objResponse->MISRNO) && $objResponse->MISRNO !=''?$objResponse->MISRNO:''}}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase" autofocus >
    </div>
    
    <div class="col-lg-1 pl"><p>MISR Date*</p></div>
    <div class="col-lg-2 pl">
        <input disabled type="date" name="MISRDT" id="MISRDT" value="{{isset($objResponse->MISRDT) && $objResponse->MISRDT !=''?$objResponse->MISRDT:''}}" class="form-control mandatory" >
    </div>

    <div class="col-lg-2 pl"><p>RPR No*</p></div>
    <div class="col-lg-2 pl">
      <input disabled type="text" name="rpr_popup" id="txtrpr_popup" value="{{isset($objRPRDtl->RPR_NO) && $objRPRDtl->RPR_NO !=''?$objRPRDtl->RPR_NO:''}}"  class="form-control mandatory"  autocomplete="off" readonly disabled/>
      <input type="hidden" name="RPRID_REF" id="RPRID_REF" value="{{isset($objResponse->RPRID_REF) && $objResponse->RPRID_REF !=''?$objResponse->RPRID_REF:''}}" class="form-control" autocomplete="off" />           
    </div>  

  </div>
  <div class="row">          
    <div class="col-lg-2 pl"><p>From Store*</p></div>
    <div class="col-lg-2 pl">
      <input disabled type="text" name="store_popup" value="{{isset($objStoreDtl->STCODE) && $objStoreDtl->STCODE !=''?$objStoreDtl->STCODE.'-'.$objStoreDtl->NAME:''}}" id="txtstore_popup" class="form-control mandatory"  autocomplete="off" readonly/>
      <input type="hidden" name="STID_REF" value="{{isset($objResponse->FROM_STID_REF) && $objResponse->FROM_STID_REF !=''?$objResponse->FROM_STID_REF:''}}" id="STID_REF" class="form-control"  />           
    </div>       
    <div class="col-lg-2 pl"><p>To Production Stage*</p></div>
    <div class="col-lg-2 pl">
      <input disabled type="text" name="stage_popup" value="{{isset($objPStageDtl->PSTAGE_CODE) && $objPStageDtl->PSTAGE_CODE !=''?$objPStageDtl->PSTAGE_CODE.'-'.$objPStageDtl->DESCRIPTIONS:''}}" id="txtpstage_popup" class="form-control mandatory"  autocomplete="off" readonly/>
      <input type="hidden" name="PSTAGEID_REF" value="{{isset($objResponse->STAGEID_REF) && $objResponse->STAGEID_REF !=''?$objResponse->STAGEID_REF:''}}" id="PSTAGEID_REF" class="form-control"  />           
    </div>   
    <div class="col-lg-2 pl"><p>Issued By*</p></div>
    <div class="col-lg-2 pl">
      <input disabled type="text" name="employee_popup" value="{{isset($objEmployeeDtl->EMPCODE) && $objEmployeeDtl->EMPCODE !=''?$objEmployeeDtl->EMPCODE.'-'.$objEmployeeDtl->FNAME.' '.$objEmployeeDtl->LNAME:''}}" id="txtemployee_popup" class="form-control mandatory"  autocomplete="off" readonly/>
      <input type="hidden" name="EMPID_REF" value="{{isset($objResponse->ISSUED_BY) && $objResponse->ISSUED_BY !=''?$objResponse->ISSUED_BY:''}}"  id="EMPID_REF" class="form-control"  />           
    </div>       
  </div> 

  
</div>

<div class="container-fluid">

  <div class="row">
    <ul class="nav nav-tabs">
      <li class="active"><a data-toggle="tab" href="#Material">Material</a></li>
      <li><a data-toggle="tab" href="#udf">UDF</a></li>
    </ul>
    Note:- 1 row mandatory in Material Tab 
    
    
    <div class="tab-content">

      <div id="Material" class="tab-pane fade in active">
        <div class="table-responsive table-wrapper-scroll-y" style="height:280px;margin-top:10px;" >
          <table id="example2" class="display nowrap table table-striped table-bordered itemlist w-200" width="100%" style="height:auto !important;">
            <thead id="thead1"  style="position: sticky;top: 0">
            <tr>
                <th>Main Item Code</th>
                <th  hidden>FGI_REF</th>
                <th >Main Item Name</th>
                <th  hidden>Main Item UOM</th>
                <th  hidden>MAINITEM_UOMID_REF</th>

                <th   hidden >SOID_REF</th>
                <th   hidden >SQID_REF</th>
                <th   hidden >SEID_REF</th>
                <th   hidden >RPRID_REF</th>
                <th   hidden >PROID_REF</th>
                <th>Item Code <input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1"></th>
                <th  hidden >ITEMID_REF </th>
                <th  hidden >OLDITEM_ID_REF </th>
                <th  hidden>RPR_REQ_QTY </th>
                
                <th>Item Name</th>  
                
                <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</th>
                <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</th>
                <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</th>

                <th>UOM</th>
                <th  hidden >MAIN_UOMID_REF</th>
                <th>Store</th>
                <th  hidden >TotalHiddenQty</th>
                <th  hidden >HiddenRowId</th>
                <th >Bal Request Qty</th>
                <th>Stock-in-hand</th>
                <th>Issued Qty (MU)</th>
                <th>Alt UOM (AU)</th>
                <th   hidden>Alt UOMID</th>
                <th   hidden>RECEIVED_QTY_AU</th>
                <th   hidden>SHORT_QTY_0</th>
                <th>Reason of Short Qty Issued</th>
                <th>Remarks</th>
                <th>Action</th>
                </tr>
            </thead>
            <tbody id="tbodyid">
            @if(!empty($objMAT))
            @foreach($objMAT as $key => $row)
            <tr  class="participantRow">

              <td><input disabled  type="text" name="popupFGI_{{$key}}" id="popupFGI_{{$key}}" value="{{ $row->MAINITEMID_CODE }}" class="form-control"  autocomplete="off"  readonly style="width:100px;" /></td>
              <td hidden  ><input type="text" name="FGI_REF_{{$key}}" id="FGI_REF_{{$key}}" value="{{ $row->MAINITEMID_REF }}"  class="form-control" autocomplete="off" /></td>
              <td><input disabled type="text" name="FGIName_{{$key}}" id="FGIName_{{$key}}" value="{{ $row->MAINITEMID_NAME }}"  class="form-control"  autocomplete="off"  readonly style="width:200px;" /></td>
              <td  hidden ><input type="text" name="popupMAINITEMUOM_{{$key}}"  id="popupMAINITEMUOM_{{$key}}" value="{{ $row->MAINITEMID_UOMCODE }}-{{ $row->MAINITEMID_UOMDESC}}" class="form-control"  autocomplete="off"  readonly style="width:100px;"/></td>
              <td  hidden ><input type="text" name="MAINITEM_UOMID_REF_{{$key}}" id="MAINITEM_UOMID_REF_{{$key}}" value="{{ $row->MAINITEMUOMID_REF}}" readonly class="form-control"  autocomplete="off" /></td>


              <td    hidden  ><input type="text" name="SOID_REF_{{$key}}"  id="SOID_REF_{{$key}}"  value="{{ $row->SOID_REF }}" class="form-control"  /></td>
              <td    hidden  ><input type="text" name="SQID_REF_{{$key}}"  id="SQID_REF_{{$key}}"  value="{{ $row->SQID_REF }}"  class="form-control"  /></td>
              <td    hidden  ><input type="text" name="SEID_REF_{{$key}}"  id="SEID_REF_{{$key}}"  value="{{ $row->SEID_REF }}" class="form-control"  /></td>
              <td    hidden  ><input type="text" name="RPRID_REF_{{$key}}" id="RPRID_REF_{{$key}}" value="{{ $row->RPRID_REF }}" class="form-control"  /></td>
              <td    hidden  ><input type="text" name="PROID_REF_{{$key}}" id="PROID_REF_{{$key}}" value="{{ $row->PROID_REF }}" class="form-control"  /></td>

              <td>    <input disabled style="width:100px;" type="text" name={{"popupITEMID_".$key}} id={{"popupITEMID_".$key}} class="form-control"  value="{{ $row->ICODE }}" autocomplete="off"  readonly/></td>
              <td   hidden  ><input type="text" name={{"ITEMID_REF_".$key}} id={{"ITEMID_REF_".$key}} class="form-control"  value="{{ $row->ITEMID_REF }}" autocomplete="off" /></td>
              <td  hidden   ><input type="text" name={{"OLDITEM_ID_REF_".$key}} id={{"OLDITEM_ID_REF_".$key}} value="{{ $row->OLDITEMID_REF }}" class="form-control" autocomplete="off" /></td>
              <td  hidden   ><input type="text" name={{"RPR_REQ_QTY_".$key}} id={{"RPR_REQ_QTY_".$key}} value="{{ $row->ISSUED_QTY }}" class="form-control" /></td>
              
              <td><input disabled style="width:200px;" type="text" name={{"ItemName_".$key}} id={{"ItemName_".$key}} class="form-control" value="{{ $row->ITEM_NAME }}"  autocomplete="off"   readonly/></td>
             
              <td {{$AlpsStatus['hidden']}}><input  type="text" name="Alpspartno_{{$key}}" id="Alpspartno_{{$key}}" class="form-control"  autocomplete="off" value="{{ isset($row->ALPS_PART_NO)?$row->ALPS_PART_NO:'' }}" readonly/></td>
              <td {{$AlpsStatus['hidden']}}><input  type="text" name="Custpartno_{{$key}}" id="Custpartno_{{$key}}" class="form-control"  autocomplete="off" value="{{ isset($row->CUSTOMER_PART_NO)?$row->CUSTOMER_PART_NO:'' }}" readonly/></td>
              <td {{$AlpsStatus['hidden']}}><input  type="text" name="OEMpartno_{{$key}}"  id="OEMpartno_{{$key}}" class="form-control"  autocomplete="off" value="{{ isset($row->OEM_PART_NO)?$row->OEM_PART_NO:'' }}" readonly/></td>

              <td><input disabled type="text" name={{"popupMUOM_".$key}} id={{"popupMUOM_".$key}} class="form-control"  value="{{ $row->MAIN_UOM_CODE }}" autocomplete="off" style="width: 100px;" readonly/></td>
              <td hidden ><input type="text" name={{"MAIN_UOMID_REF_".$key}} id={{"MAIN_UOMID_REF_".$key}} class="form-control" value="{{ $row->UOMID_REF }}"   autocomplete="off" /></td>
                  
              <td align="center"><a disabled class="btn checkstore"  id="{{$key}}" ><i class="fa fa-clone"></i></a></td>
              <td    hidden ><input type="text" name="TotalHiddenQty_{{$key}}" id="TotalHiddenQty_{{$key}}" value="{{ $row->ISSUED_QTY}}" ></td>
              <td    hidden ><input type="text" name="HiddenRowId_{{$key}}" id="HiddenRowId_{{$key}}" value="{{ $row->BATCH_QTY}}" ></td>
                

              <td ><input disabled type="text" name="PENDING_QTY_{{$key}}" id="PENDING_QTY_{{$key}}" class="form-control three-digits" value="{{ $row->TOTAL_PENDING }}"  autocomplete="off" readonly  /></td>
                       
                
                <td><input disabled type="text" name="STOCK_INHAND_{{$key}}" id="STOCK_INHAND_{{$key}}" value="{{ $row->TOTAL_STOCK }}" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" readonly  autocomplete="off"   /></td>
                <td><input disabled type="text" name="RECEIVED_QTY_MU_{{$key}}" id="RECEIVED_QTY_MU_{{$key}}" value="{{ $row->ISSUED_QTY }}" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" readonly  /></td>
                
                <td><input disabled type="text" name="popupALTUOM_{{$key}}" id="popupALTUOM_{{$key}}" class="form-control" value="{{ $row->AULT_UOM_CODE }}"  autocomplete="off"  readonly/></td>
                <td hidden  ><input type="text" name="ALT_UOMID_REF_{{$key}}" id="ALT_UOMID_REF_{{$key}}" value="{{ $row->ALT_UOMID_REF }}" class="form-control"  autocomplete="off" /></td>

                <td  hidden  ><input type="text" name="RECEIVED_QTY_AU_{{$key}}" id="RECEIVED_QTY_AU_{{$key}}"  class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" readonly maxlength="13"  autocomplete="off"   /></td>
                <td hidden  ><input type="text" name="SHORT_QTY_{{$key}}" id="SHORT_QTY_{{$key}}" value="{{ $row->SHORT_QTY }}" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off"  readonly  /></td>
              
                <td><input disabled type="text" name="REASON_SHORT_QTY_{{$key}}" id="REASON_SHORT_QTY_{{$key}}" value="{{ $row->REASON_SHORT_QTY }}" class="form-control"   autocomplete="off"   /></td>
                <td><input disabled style="width:200px;" type="text" name="REMARKS_{{$key}}" id="REMARKS_{{$key}}" value="{{ $row->REMARKS }}" class="form-control w-100" autocomplete="off" ></td>
                
                <td align="center" ><button disabled class="btn add material" title="add" data-toggle="tooltip" type="button" ><i class="fa fa-plus"></i></button>
                <button disabled class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button" ><i class="fa fa-trash" ></i></button></td>
                </tr>
              <tr></tr>
              @endforeach 
              @endif
            </tbody>
          </table>
        </div>	
      </div>

      <div id="udf" class="tab-pane fade">
        <div class="table-responsive table-wrapper-scroll-y " style="margin-top:10px;height:280px;width:50%;">
          <table id="example3" class="display nowrap table table-striped table-bordered itemlist" style="height:auto !important;">
            <thead id="thead1"  style="position: sticky;top: 0">
              <tr >
              <th>UDF Fields<input class="form-control" type="hidden" name="Row_Count2" id ="Row_Count2"></th>
              <th>Value / Comments</th>
              <th>Action</th>
              </tr>
            </thead>
            <tbody>
            @foreach($objUDF as $uindex=>$uRow)
              <tr  class="participantRow3">
                  <td><input disabled type="text" name={{"popupSEID_".$uindex}} id={{"popupSEID_".$uindex}} class="form-control" value="{{$uRow->UDFMISRID_REF}}" autocomplete="off"  readonly/></td>
                  <td hidden><input type="hidden" name={{"UDF_".$uindex}} id={{"UDF_".$uindex}} class="form-control" value="{{$uRow->UDFMISRID_REF}}" autocomplete="off"   /></td>
                  <td hidden><input type="hidden" name={{"UDFismandatory_".$uindex}} id={{"UDFismandatory_".$uindex}} value="{{$uRow->UDFMISRID_REF}}" class="form-control"   autocomplete="off" /></td>
                  <td id={{"udfinputid_".$uindex}} >
                    
                  </td>
                  <td align="center" ><button disabled class="btn add UDF" title="add" data-toggle="tooltip" type="button" disabled><i class="fa fa-plus"></i></button><button disabled class="btn remove DUDF" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
                  
              </tr>
              <tr></tr>
            @endforeach 
            
           
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

<div id="StoreModal" class="modal" role="dialog"  data-backdrop="static">
<div class="modal-dialog modal-md" style="width:80%;z-index:1">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" id='StoreModalClose' >&times;</button>
    </div>
    <div class="modal-body">
      <div class="tablename"><p>Store Details</p></div>
      <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
        <table id="StoreTable" class="display nowrap table  table-striped table-bordered" style="width: 100%;" >
        </table>
      </div>
      <div class="cl"></div>
    </div>
  </div>
</div>
</div>

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
      </div>
  <div class="cl"></div>
    </div>
  </div>
</div>
</div>

<!-- RPR NO Dropdown -->
<div id="rpr_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:60%" >
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='pro_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>RPR NO</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="RprNoTable" class="display nowrap table  table-striped table-bordered" style="width:100%" >
    <thead>

    <tr>
      <th style="width:10%;">Select</th> 
      <th style="width:30%;">No</th>
      <th style="width:30%;">Date</th>
      <th style="width:30%;">Title</th>
    </tr>

    </thead>
    <tbody>

    <tr>
      <th style="width:10%;"><span class="check_th">&#10004;</span></th>
      <td style="width:30%;"><input type="text" id="rprnosearch" class="form-control" onkeyup="RprNoFunction()"></td>
      <td style="width:30%;"><input type="text" id="rprdatesearch" class="form-control" onkeyup="RprDateFunction()"></td>
      <td style="width:30%;"><input type="text" id="rprtitlesearch" class="form-control" onkeyup="RprTitleFunction()"></td>
    </tr>

    </tbody>
    </table>
      <table id="RprNoTable2" class="display nowrap table  table-striped table-bordered" style="width:100%" >
        <thead id="thead2">
          
        </thead>
        <tbody>
        @foreach ($objRPR as $index=>$rpr_Row)
        <tr> 
          <td style="width:10%;text-align:center;"> <input type="checkbox" name="SELECT_RPRID_REF[]" id="rprno_{{ $index }}" class="clsrprno" value="{{ $rpr_Row->RPRID }}" ></td>
          <td style="width:30%;">{{ $rpr_Row->RPR_NO }} <input type="hidden" id="txtrprno_{{ $index }}" data-desc="{{ $rpr_Row->RPR_NO }}"  value="{{ $rpr_Row->RPRID }}" /></td>
          <td style="width:30%;">{{ $rpr_Row->RPR_DT }}</td>
          <td style="width:30%;">{{ $rpr_Row->PRO_TITLE }}</td>
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

<!-- Production Stages Dropdown -->
<div id="pstage_popup" class="modal" role="dialog"  data-backdrop="static">
<div class="modal-dialog modal-md column3_modal">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" id='pstage_closePopup' >&times;</button>
    </div>
  <div class="modal-body">
    <div class="tablename"><p>From Production Stages</p></div>
    <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
  <table id="ProdStageTable" class="display nowrap table  table-striped table-bordered" >
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
    <td class="ROW2"><input type="text" id="pstage_code_search" class="form-control" onkeyup="PStageCodeFunction()"></td>
    <td class="ROW3"><input type="text" id="pstage_desc_search" class="form-control" onkeyup="PStageDescFunction()"></td>
  </tr>

  </tbody>
  </table>
    <table id="ProdStageTable2" class="display nowrap table  table-striped table-bordered" >
      <thead id="thead2">
        
      </thead>
      <tbody>
      @foreach ($objPStage as $index=>$pstage_Row)
      <tr >
        <td class="ROW1" > <input type="checkbox" name="SELECT_PSTAGEID_REF[]" id="pstage_{{ $index }}" class="clspstage" value="{{ $pstage_Row->PSTAGEID }}" ></td>
        <td class="ROW2" >{{ $pstage_Row-> PSTAGE_CODE }} <input type="hidden" id="txtpstage_{{ $index }}" data-desc="{{ $pstage_Row->PSTAGE_CODE }}-{{ $pstage_Row->DESCRIPTIONS }}"  value="{{ $pstage_Row->PSTAGEID }}"/>
        </td>
        <td class="ROW3">{{ $pstage_Row->DESCRIPTIONS }}</td>
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
<!-- Store Dropdown -->
<div id="store_popup" class="modal" role="dialog"  data-backdrop="static">
<div class="modal-dialog modal-md column3_modal" style="width: 40%">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" id='store_closePopup' >&times;</button>
    </div>
  <div class="modal-body">
    <div class="tablename"><p>Stores</p></div>
    <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
  <table id="storeTbl" class="display nowrap table  table-striped table-bordered" >
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
    <td class="ROW2"><input type="text" id="store_code_search" class="form-control" onkeyup="storeCodeFunction()"></td>
    <td class="ROW3"><input type="text" id="store_name_search" class="form-control" onkeyup="storeNameFunction()"></td>
  </tr>
  </tbody>
  </table>
    <table id="storeTbl2" class="display nowrap table  table-striped table-bordered" >
      <thead id="thead2">
        
      </thead>
      <tbody>
      @foreach ($objStore as $index=>$store_Row)
      <tr >
        <td class="ROW1"> <input type="checkbox" name="SELECT_STID_REF[]" id="store_{{ $index }}" class="clsstore" value="{{ $store_Row->STID }}" ></td>
        <td class="ROW2">{{ $store_Row->STCODE }} <input type="hidden" id="txtstore_{{ $index }}" data-desc="{{ $store_Row->STCODE }}-{{ $store_Row->NAME }}"  value="{{ $store_Row->STID }}"/></td>
        <td class="ROW3">{{ $store_Row->NAME }}</td>
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
<!-- employee Dropdown -->
<div id="employee_popup" class="modal" role="dialog"  data-backdrop="static">
<div class="modal-dialog modal-md column3_modal">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" id='employee_closePopup' >&times;</button>
    </div>
  <div class="modal-body">
    <div class="tablename"><p>Issued By</p></div>
    <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
  <table id="employeeTable" class="display nowrap table  table-striped table-bordered" >
  <thead>
  <tr>
    <th class="ROW1">Select</th> 
    <th class="ROW2">Code</th>
    <th class="ROW3">Name</th>
  </tr>
  </thead>
  <tbody>

  <tr>
    <th class="ROW1"><span class="check_th">&#10004;</span></th>
    <td class="ROW2"><input type="text" id="employee_code_search" class="form-control" onkeyup="employeeCodeFunction()"></td>
    <td class="ROW3"><input type="text" id="employee_name_search" class="form-control" onkeyup="employeeNameFunction()"></td>
  </tr>

  </tbody>
  </table>
    <table id="employeeTable2" class="display nowrap table  table-striped table-bordered" >
      <thead id="thead2">
        
      </thead>
      <tbody>
      @foreach ($objEmployee as $index=>$employee_Row)
      <tr >
        <td class="ROW1"> <input type="checkbox" name="SELECT_EMPID_REF[]" id="employee_{{ $index }}" class="clsemployee" value="{{ $employee_Row->EMPID }}" ></td>
        <td class="ROW2">{{ $employee_Row->EMPCODE }}
        <input type="hidden" id="txtemployee_{{ $index }}" data-desc="{{ $employee_Row->EMPCODE }}-{{ $employee_Row->FNAME }} {{ $employee_Row->LNAME }}"  value="{{ $employee_Row->EMPID }}"/>
        </td>
        <td class="ROW3">{{ $employee_Row->FNAME }} {{ $employee_Row->LNAME }}</td>
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


<!-- FGI/main items Dropdown-->
<div id="FGIIDpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:90%">
    <div class="modal-content" >
      <div class="modal-header"><button type="button" class="close" data-dismiss="modal" id='FGIID_closePopup' >&times;</button></div>
      <div class="modal-body">
	      <div class="tablename"><p>Main Items Details</p></div>
	      <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
          <table id="FGITable" class="display nowrap table  table-striped table-bordered" style="width:100%" >
            <thead>
              <tr id="none-select" class="searchalldata" hidden >
                <td> 
                  <input type="text" name="fildfgi_id1" id="hdn_FGIID1"/>
                  <input type="text" name="fildfgi_id2" id="hdn_FGIID2"/>
                  <input type="text" name="fildfgi_id3" id="hdn_FGIID3"/>
                  <input type="text" name="fildfgi_id4" id="hdn_FGIID4"/>
                  <input type="text" name="fildfgi_id5" id="hdn_FGIID5"/>
                  <input type="text" name="fildfgi_id6" id="hdn_FGIID6"/>
                  <input type="text" name="fildfgi_id7" id="hdn_FGIID7"/>
                  <input type="text" name="fildfgi_id8" id="hdn_FGIID8"/>
                  <input type="text" name="fildfgi_id9" id="hdn_FGIID9"/>
                  <input type="text" name="fildfgi_id10" id="hdn_FGIID10"/>
                  <input type="text" name="fildfgi_id11" id="hdn_FGIID11"/>
                  <input type="text" name="fildfgi_id18" id="hdn_FGIID18"/>
                  <input type="text" name="fildfgi_id19" id="hdn_FGIID19"/>
                  <input type="text" name="fildfgi_id20" id="hdn_FGIID20"/>
                  <input type="text" name="hdn_FGIID21" id="hdn_FGIID21" value="0"/>
                  <input type="text" name="fildfgi_id22" id="hdn_FGIID22"/>
                  <input type="text" name="fildfgi_id23" id="hdn_FGIID23"/>
                  <input type="text" name="fildfgi_id24" id="hdn_FGIID24"/>
                  <input type="text" name="fildfgi_id25" id="hdn_FGIID25"/>
                </td>
              </tr>

              <tr>
                <th style="width:10%;text-align:center;" id="all-check">Select</th>
                <th style="width:10%;">Item Code</th>
                <th style="width:10%;">Name</th>
                <th style="width:10%;display:none;">Main UOM</th>
                <th style="width:10%;display:none;">Qty</th>
                <th style="width:10%;">Item Group</th>
                <th style="width:10%;">Item Category</th>
                <th style="width:10%;">Business Unit</th>
                <th style="width:10%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</th>
                <th style="width:10%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</th>
                <th style="width:10%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</th>
                <th style="width:10%;">Status</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td style="width:10%;text-align:center;"><input type="checkbox" class="fgijs-selectall" data-target=".fgijs-selectall1" /></td>
                <td style="width:10%;"><input type="text" id="fgicodesearch" class="form-control" onkeyup="FGICodeFunction()"></td>
                <td style="width:10%;"><input type="text" id="fginamesearch" class="form-control" onkeyup="FGINameFunction()"></td>
                <td style="width:15%;display:none;" ><input type="text" id="fgiUOMsearch" class="form-control" onkeyup="FGIUOMFunction()"></td>
                <td style="width:10%;display:none;"><input type="text" id="fgiQTYsearch" class="form-control" onkeyup="FGIQTYFunction()"></td>
                <td style="width:10%;"><input type="text" id="fgiGroupsearch" class="form-control" onkeyup="FGIGroupFunction()"></td>
                <td style="width:10%;"><input type="text" id="fgiCategorysearch" class="form-control" onkeyup="FGICategoryFunction()"></td>
                
                <td style="width:10%;"><input type="text" id="fgiItemBUsearch" class="form-control" onkeyup="FGIItemBUFunction()"></td>
                <td style="width:10%;" {{$AlpsStatus['hidden']}} ><input type="text" id="fgiItemAPNsearch" class="form-control" onkeyup="FGIItemAPNFunction()"></td>
                <td style="width:10%;" {{$AlpsStatus['hidden']}} ><input type="text" id="fgiItemCPNsearch" class="form-control" onkeyup="FGIItemCPNFunction()"></td>
                <td style="width:10%;" {{$AlpsStatus['hidden']}} ><input type="text" id="fgiItemOEMPNsearch" class="form-control" onkeyup="FGIItemOEMPNFunction()"></td>
                
                <td style="width:10%;"><input  type="text" id="fgiStatussearch" class="form-control" onkeyup="FGIStatusFunction()"></td>
              </tr>
            </tbody>
          </table>

          <table id="FGITable2" class="display nowrap table  table-striped table-bordered" style="width:100%" >
            <thead id="thead2"></thead>
            <tbody id="tbody_FGI"></tbody>
          </table>
        </div>
		    <div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<!-- item Dropdown-->
<div id="ITEMIDpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md"  style="width:90%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='ITEMID_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Item Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="ItemIDTable" class="display nowrap table  table-striped table-bordered" style="width:100%" >
    <thead>
      <tr id="none-select" class="searchalldata" hidden >            
            <td>
              <input type="text" name="fieldid1" id="hdn_ItemID1"/>
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
              <input type="text" name="fieldid18" id="hdn_ItemID18"/>
              <input type="text" name="fieldid19" id="hdn_ItemID19"/>
              <input type="text" name="fieldid20" id="hdn_ItemID20"/>
              <input type="text" name="hdn_ItemID21" id="hdn_ItemID21" value="0"/>
              <input type="text" name="fieldid22" id="hdn_ItemID22"/>
              <input type="text" name="fieldid23" id="hdn_ItemID23"/>
              <input type="text" name="fieldid24" id="hdn_ItemID24"/>
              <input type="text" name="fieldid25" id="hdn_ItemID25"/>
              <input type="text" name="fieldid26" id="hdn_ItemID26"/>
              <input type="text" name="fieldid27" id="hdn_ItemID27"/>
              <input type="text" name="fieldid28" id="hdn_ItemID28"/>
            </td>
      </tr>
      
      <tr>
        <th style="width:5%;" id="all-check">Select</th>
        <th style="width:10%;">Item Code</th>
        <th style="width:10%;">Name</th>
        <th style="width:10%;">Main UOM</th>
        <th style="width:10%;">Balance Req Qty</th>
        <th style="width:10%;">Item Group</th>
        <th style="width:10%;">Item Category</th>
        <th style="width:10%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</th>
        <th style="width:10%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</th>
        <th style="width:10%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</th>
        <th style="width:5%;">Status</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td style="width:5%;"><input type="checkbox" class="js-selectall" data-target=".js-selectall1" /></td>
        <td style="width:10%;"><input type="text" id="Itemcodesearch" class="form-control" onkeyup="ItemCodeFunction()"></td>
        <td style="width:10%;"><input type="text" id="Itemnamesearch" class="form-control" onkeyup="ItemNameFunction()"></td>
        <td style="width:10%;"><input type="text" id="ItemUOMsearch" class="form-control" onkeyup="ItemUOMFunction()"></td>
        <td style="width:10%;"><input type="text" id="ItemQTYsearch" class="form-control" onkeyup="ItemQTYFunction()"></td>
        <td style="width:10%;"><input type="text" id="ItemGroupsearch" class="form-control" onkeyup="ItemGroupFunction()"></td>
        <td style="width:10%;"><input type="text" id="ItemCategorysearch" class="form-control" onkeyup="ItemCategoryFunction()"></td>
        <td style="width:10%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemAPNsearch" class="form-control" onkeyup="ItemAPNFunction()"></td>
        <td style="width:10%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemCPNsearch" class="form-control" onkeyup="ItemCPNFunction()"></td>
        <td style="width:10%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemOEMPNsearch" class="form-control" onkeyup="ItemOEMPNFunction()"></td>
        <td style="width:5%;"><input  type="text" id="ItemStatussearch" class="form-control" onkeyup="ItemStatusFunction()"></td>
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
{{-- substitute item --}}

<div id="SUBITEMpopup" class="modal" role="dialog"  data-backdrop="static">
<div class="modal-dialog modal-md column3_modal">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" id='SUBITEM_closePopup' >&times;</button>
    </div>
  <div class="modal-body">
  <div class="tablename"><p>SUBITEM DETAILS</p></div>
  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
  <table id="SUBITEMTable" class="display nowrap table  table-striped table-bordered" style="font-size:14px;" >
  <thead>
        <tr id="none-select" class="searchalldata" hidden>
          
          <td> 
          <input type="text"  id="hdn_SUBITEMid1"/>
          <input type="text"  id="hdn_SUBITEMid2"/>
          <input type="text"  id="hdn_SUBITEMid3"/>
          <input type="text"  id="hdn_SUBITEMid4"/>
          <input type="text"  id="hdn_SUBITEMid5"/>
          <input type="text"  id="hdn_SUBITEMid6"/>
          <input type="text"  id="hdn_SUBITEMid7"/>
          <input type="text"  id="hdn_SUBITEMid8"/>
          <input type="text"  id="hdn_SUBITEMid9"/>
          <input type="text"  id="hdn_SUBITEMid10"/>
          </td>
        </tr>
        <tr>
          <th class="ROW1">Select</th> 
          <th class="ROW2">Code</th>
          <th class="ROW3">Description</th>
        </tr>
  </thead>
  <tbody>


  <tr>
    <th class="ROW1"><span class="check_th">&#10004;</span></th>
    <td class="ROW2"><input type="text" id="SUBITEMcodesearch" class="form-control" onkeyup="SUBITEMCodeFunction()"></td>
    <td class="ROW3"><input type="text" id="SUBITEMnamesearch" class="form-control" onkeyup="SUBITEMNameFunction()"></td>
  </tr>

  </tbody>
  </table>
    <table id="SUBITEMTable2" class="display nowrap table  table-striped table-bordered"  style="font-size:14px;" >
      <thead id="thead2">

      </thead>
      <tbody id="tbody_SUBITEM">     
      
      </tbody>
    </table>
  </div>
  <div class="cl"></div>
    </div>
  </div>
</div>
</div>

<!-- Print -->
<div id="ReportView" class="modal" role="dialog"  data-backdrop="static"  >
  <div class="modal-dialog modal-md" style="width:90%; height:90%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='ReportViewclosePopup' >&times;</button>          
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Material Issue Slip against RPR Print</p></div>
        <div class="row">
          <div class="frame-container col-lg-12 pl text-center" >
                <button class="btn topnavbt" id="btnReport">
                    Print
                </button>
                <button class="btn topnavbt" id="btnPdf">
                    PDF
                </button>
                <button class="btn topnavbt" id="btnExcel">
                    Excel
                </button>
          </div>
        </div>
        
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
          <div class="inner-form">
              <div class="row">
                  <div class="frame-container col-lg-12 pl " >                      
                      <iframe id="iframe_rpt" width="100%" height="1500" >
                      </iframe>
                  </div>
              </div>
          </div>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Print-->

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

table {font-size:13px;}
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

/*=======================================================================================*/

/*================================== rprno POPUP FUNCTION =================================*/
let rprtid = "#RprNoTable2";
    let rprtid2 = "#RprNoTable";
    let rprheaders = document.querySelectorAll(rprtid2 + " th");

    // Sort the table element when clicking on the table headers
    rprheaders.forEach(function(element, i) {
      element.addEventListener("click", function() {
        w3.sortHTML(tid, ".clsrprno", "td:nth-child(" + (i + 1) + ")");
      });
    });

    function RprNoFunction() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("rprnosearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("RprNoTable2");
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

function RprDateFunction() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("rprdatesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("RprNoTable2");
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

function RprTitleFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("rprtitlesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("RprNoTable2");
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

    $('#txtrpr_popup').focus(function(event){
      showSelectedCheck($("#RPRID_REF").val(),"SELECT_RPRID_REF");
       $("#rpr_popup").show();
       event.preventDefault();
    });

    $("#pro_closePopup").click(function(event){
      $("#rpr_popup").hide();
      event.preventDefault();
    });

    $(".clsrprno").click(function(){
      var fieldid = $(this).attr('id');
      var txtval =    $("#txt"+fieldid+"").val();
      var texdesc =   $("#txt"+fieldid+"").data("desc");
      
      // var txtid= $('#hdn_fieldid').val();
      // var txt_id2= $('#hdn_fieldid2').val();
      
      $('#txtrpr_popup').val(texdesc);
      $('#RPRID_REF').val(txtval);
      $("#rpr_popup").hide();
      $("#rprnosearch").val(''); 
      $("#rprdatesearch").val(''); 
           
      clearGrid();
      event.preventDefault();
    });

/*================================== PRODUCTION STAGES POPUP FUNCTION =================================*/
let tpstageid = "#ProdStageTable2";
    let tpstageid2 = "#ProdStageTable";
    let pstageheaders = document.querySelectorAll(tpstageid2 + " th");

    // Sort the table element when clicking on the table pstageheaders
    pstageheaders.forEach(function(element, i) {
      element.addEventListener("click", function() {
        w3.sortHTML(tpstageid, ".clspstage", "td:nth-child(" + (i + 1) + ")");
      });
    });

    function PStageCodeFunction() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("pstage_code_search");
      filter = input.value.toUpperCase();
      table = document.getElementById("ProdStageTable2");
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

function PStageDescFunction() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("pstage_desc_search");
      filter = input.value.toUpperCase();
      table = document.getElementById("ProdStageTable2");
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

    $('#txtpstage_popup').focus(function(event){
      showSelectedCheck($("#PSTAGEID_REF").val(),"SELECT_PSTAGEID_REF");
       $("#pstage_popup").show();
       event.preventDefault();
    });

    $("#pstage_closePopup").click(function(event){
      $("#pstage_popup").hide();
      event.preventDefault();
    });

    $(".clspstage").click(function(){
      var fieldid = $(this).attr('id');
      var txtval =    $("#txt"+fieldid+"").val();
      var texdesc =   $("#txt"+fieldid+"").data("desc");
      // var txtid= $('#hdn_fieldid').val();
      // var txt_id2= $('#hdn_fieldid2').val();
      
      $('#txtpstage_popup').val(texdesc);
      $('#PSTAGEID_REF').val(txtval);
      $("#pstage_popup").hide();
      $("#pstage_code_search").val(''); 
      $("#pstage_desc_search").val(''); 
      PStageCodeFunction();
      PStageDescFunction();        
      event.preventDefault();
    });

/*==================================FROM STORE POPUP FUNCTION =================================*/
    let storeid = "#storeTbl2";
    let storeid2 = "#storeTbl";
    let storeheaders = document.querySelectorAll(storeid2 + " th");

    // Sort the table element when clicking on the table storeheaders
    storeheaders.forEach(function(element, i) {
      element.addEventListener("click", function() {
        w3.sortHTML(storeid, ".clsstore", "td:nth-child(" + (i + 1) + ")");
      });
    });

    function storeCodeFunction() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("store_code_search");
      filter = input.value.toUpperCase();
      table = document.getElementById("storeTbl2");
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

    function storeNameFunction() {
          var input, filter, table, tr, td, i, txtValue;
          input = document.getElementById("store_name_search");
          filter = input.value.toUpperCase();
          table = document.getElementById("storeTbl2");
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

    $('#txtstore_popup').focus(function(event){
      showSelectedCheck($("#STID_REF").val(),"SELECT_STID_REF");
       $("#store_popup").show();
       event.preventDefault();
    });

    $("#store_closePopup").click(function(event){
      $("#store_popup").hide();
      event.preventDefault();
    });

    $(".clsstore").click(function(){
      var fieldid = $(this).attr('id');
      var txtval =    $("#txt"+fieldid+"").val();
      var texdesc =   $("#txt"+fieldid+"").data("desc");
      // var txstoreid= $('#hdn_fieldid').val();
      // var txt_id2= $('#hdn_fieldid2').val();
      
      $('#txtstore_popup').val(texdesc);
      $('#STID_REF').val(txtval);
      $("#store_popup").hide();
      $("#store_code_search").val(''); 
      $("#store_name_search").val(''); 
      storeCodeFunction();
      storeNameFunction();        
      event.preventDefault();
    });

/*================================== EMPLOYEE POPUP FUNCTION =================================*/
    let employeeid = "#employeeTable2";
    let employeeid2 = "#employeeTable";
    let employeeheaders = document.querySelectorAll(employeeid2 + " th");

    // Sort the table element when clicking on the table employeeheaders
    employeeheaders.forEach(function(element, i) {
      element.addEventListener("click", function() {
        w3.sortHTML(employeeid, ".clsemployee", "td:nth-child(" + (i + 1) + ")");
      });
    });

    function employeeCodeFunction() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("employee_code_search");
      filter = input.value.toUpperCase();
      table = document.getElementById("employeeTable2");
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

    function employeeNameFunction() {
          var input, filter, table, tr, td, i, txtValue;
          input = document.getElementById("employee_name_search");
          filter = input.value.toUpperCase();
          table = document.getElementById("employeeTable2");
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

    $('#txtemployee_popup').focus(function(event){
      showSelectedCheck($("#EMPID_REF").val(),"SELECT_EMPID_REF");
       $("#employee_popup").show();
       event.preventDefault();
    });

    $("#employee_closePopup").click(function(event){
      $("#employee_popup").hide();
      event.preventDefault();
    });

    $(".clsemployee").click(function(){
      var fieldid = $(this).attr('id');
      var txtval =    $("#txt"+fieldid+"").val();
      var texdesc =   $("#txt"+fieldid+"").data("desc");
      // var txemployeeid= $('#hdn_fieldid').val();
      // var txt_id2= $('#hdn_fieldid2').val();
      
      $('#txtemployee_popup').val(texdesc);
      $('#EMPID_REF').val(txtval);
      $("#employee_popup").hide();
      $("#employee_code_search").val(''); 
      $("#employee_name_search").val(''); 
      employeeCodeFunction();
      employeeNameFunction();        
      event.preventDefault();
    });


/*================================== FGI DETAILS =================================*/

let fgiid = "#FGITable2";
let fgiid2 = "#FGITable";
let fgiidheaders = document.querySelectorAll(fgiid2 + " th");

fgiidheaders.forEach(function(element, i) {
element.addEventListener("click", function() {
  w3.sortHTML(fgiid, ".clsfgiid", "td:nth-child(" + (i + 1) + ")");
});
});

function FGICodeFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("fgicodesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("FGITable2");
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

function FGINameFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("fginamesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("FGITable2");
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

function FGIUOMFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("fgiUOMsearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("FGITable2");
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

function FGIQTYFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("fgiQTYsearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("FGITable2");
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

function FGIGroupFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("fgiGroupsearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("FGITable2");
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

function FGICategoryFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("fgiCategorysearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("FGITable2");
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

function FGIItemBUFunction() {
	var input, filter, table, tr, td, i, txtValue;
	input = document.getElementById("fgiItemBUsearch");
	filter = input.value.toUpperCase();
	table = document.getElementById("FGITable2");
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

function FGIItemAPNFunction() {
	var input, filter, table, tr, td, i, txtValue;
	input = document.getElementById("fgiItemAPNsearch");
	filter = input.value.toUpperCase();
	table = document.getElementById("FGITable2");
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

function FGIItemCPNFunction() {
	var input, filter, table, tr, td, i, txtValue;
	input = document.getElementById("fgiItemCPNsearch");
	filter = input.value.toUpperCase();
	table = document.getElementById("FGITable2");
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

function FGIItemOEMPNFunction() {
	var input, filter, table, tr, td, i, txtValue;
	input = document.getElementById("fgiItemOEMPNsearch");
	filter = input.value.toUpperCase();
	table = document.getElementById("FGITable2");
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

function FGIStatusFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("fgiStatussearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("FGITable2");
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

$('#Material').on('focus','[id*="popupFGI"]',function(event){

var RPRID_REF      =  $("#RPRID_REF").val();
var txtrpr_popup   =  $("#txtrpr_popup").attr('id');
var MISRID      =  $("#MISRID").val();

if(RPRID_REF ===""){
  $("#FocusId").val(txtrpr_popup);
  $("#ProceedBtn").focus();
  $("#YesBtn").hide();
  $("#NoBtn").hide();
  $("#OkBtn1").show();
  $("#AlertMessage").text('Please select RRO No.');
  $("#alert").modal('show')
  $("#OkBtn1").focus();
}  
else{

  $('.fgijs-selectall').prop('disabled', true);   

  $("#tbody_ItemID").html('');  //clear for variable confliction
  $("#tbody_FGI").html('loading...');
  $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $.ajax({
      url:'{{route("transaction",[$FormId,"getFGIDetails"])}}',
      type:'POST',
      data:{'RPRID_REF':RPRID_REF,'MISRID':MISRID},
      success:function(data) {
        $("#tbody_FGI").html(data);    
        bindFGIEvents();   
        $('.fgijs-selectall').prop('disabled', false);                     
      },
      error:function(data){
        console.log("Error: Something went wrong.");
        $("#tbody_FGI").html('');                        
      },
  }); 
        
  $("#FGIIDpopup").show();

  var id1   = $(this).attr('id');
  var id2   = $(this).parent().parent().find('[id*="FGI_REF"]').attr('id');
  var id3   = $(this).parent().parent().find('[id*="FGIName"]').attr('id');
  var id4   = $(this).parent().parent().find('[id*="popupMAINITEMUOM"]').attr('id');
  var id5   = $(this).parent().parent().find('[id*="MAINITEM_UOMID_REF"]').attr('id');
  var id6   = ""; 
  var id7   = "";
  var id8   = "" 
  var id9   = $(this).parent().parent().find('[id*="SQID_REF"]').attr('id');
  var id10  = $(this).parent().parent().find('[id*="SEID_REF"]').attr('id');
  var id11  = $(this).parent().parent().find('[id*="SOID_REF"]').attr('id');

  $('#hdn_FGIID1').val(id1);
  $('#hdn_FGIID2').val(id2);
  $('#hdn_FGIID3').val(id3);
  $('#hdn_FGIID4').val(id4);
  $('#hdn_FGIID5').val(id5);
  $('#hdn_FGIID6').val(id6);
  $('#hdn_FGIID7').val(id7);
  $('#hdn_FGIID8').val(id8);
  $('#hdn_FGIID9').val(id9);
  $('#hdn_FGIID10').val(id10);
  $('#hdn_FGIID11').val(id11);

  var r_count = 0;
  var ItemID = [];
  $('#example2').find('.participantRow').each(function(){
    if($(this).find('[id*="FGI_REF"]').val() != '')
    {
      ItemID.push($(this).find('[id*="FGI_REF"]').val());
      r_count = parseInt(r_count)+1;
      $('#hdn_FGIID21').val(r_count); // row counter
    }
  });
  $('#hdn_FGIID19').val(ItemID.join(', '));

  var SOID = [];
  $('#example2').find('.participantRow').each(function(){
    if($(this).find('[id*="SOID_REF"]').val() != '')
    {
      SOID.push($(this).find('[id*="SOID_REF"]').val());
    }
  });
  $('#hdn_FGIID23').val(SOID.join(', '));

  var SQID = [];
  $('#example2').find('.participantRow').each(function(){
    if($(this).find('[id*="SQID_REF"]').val() != '')
    {
      SQID.push($(this).find('[id*="SQID_REF"]').val());
    }
  });
  $('#hdn_FGIID24').val(SQID.join(', '));

  var SEID = [];
  $('#example2').find('.participantRow').each(function(){
    if($(this).find('[id*="SEID_REF"]').val() != '')
    {
      SEID.push($(this).find('[id*="SEID_REF"]').val());
    }
  });
  $('#hdn_FGIID25').val(SEID.join(', '));
  // $('#example2').find('.participantRow').each(function(){
  //   var rowCount = $(this).closest('table').find('.participantRow').length;
  //   $('#Row_Count1').val(rowCount);
  //   //$(this).closest('.participantRow').find('input:text').val('');
  //   //$(this).closest('.participantRow').find('input:hidden').val('');
  //   if (rowCount > 1) {
  //     $(this).closest('.participantRow').remove();  
  //   } 
  // });

  event.preventDefault();

}

});

$("#FGIID_closePopup").click(function(event){
$("#FGIIDpopup").hide();
});

function bindFGIEvents(){

$('#FGITable2').off(); 

//-------------------------
$('.fgijs-selectall').change(function()
{ 

  if($(this).prop("checked")){
    var item_array = [];
    $('#FGITable2').find('.clsfgiid').each(function()
    {
        var fildfgi_id          =   $(this).attr('id');
        var MAINITEM_ID         =   $("#txt"+fildfgi_id+"").data("desc1"); 
        //var MAINITEM_CODE        =  JSON.parse($("#txt"+fildfgi_id+"").data("desc2")) ;
        //var MAINITEM_NAME       =  JSON.parse( $("#txt"+fildfgi_id+"").data("desc3")) ;
        var MAINITEM_UOMID    =   $("#txt"+fildfgi_id+"").data("desc4");    
        //var MAINITEM_UOMCODE  =   JSON.parse( $("#txt"+fildfgi_id+"").data("desc5") ) ;      
        var SQID_REF           =   $("#txt"+fildfgi_id+"").data("desc8");
        var SEID_REF           =   $("#txt"+fildfgi_id+"").data("desc9");
        var SOID_REF           =   $("#txt"+fildfgi_id+"").data("desc11");
        var PROID_REF           =   $("#txt"+fildfgi_id+"").data("proid");
        var RPRID_REF           =   $("#txt"+fildfgi_id+"").data("rprid");

        item_array.push(PROID_REF+'_'+SOID_REF+"_"+SEID_REF+'_'+SQID_REF+'_'+MAINITEM_ID+'_'+MAINITEM_UOMID+"_"+RPRID_REF);

    });
    get_all_item(item_array);
    $('#FGIIDpopup').hide();
    $('.fgijs-selectall').prop("checked", false);
    return false;
  }else{
    $('#FGIIDpopup').hide();
    return false;
  }
     
  return false;
  event.preventDefault();
  

}); 

//-------------------------

$('[id*="chkfgiId"]').change(function(){

  var fildfgi_id             =   $(this).parent().parent().attr('id');
  var item_id             =   $("#txt"+fildfgi_id+"").data("desc1");
  var item_code           =   $("#txt"+fildfgi_id+"").data("desc2");
  var item_name           =   $("#txt"+fildfgi_id+"").data("desc3");
  var item_main_uom_id    =   $("#txt"+fildfgi_id+"").data("desc4");
  var item_main_uom_code  =   $("#txt"+fildfgi_id+"").data("desc5");
  var item_qty            =   $("#txt"+fildfgi_id+"").data("desc6");
  var item_unique_row_id  =   $("#txt"+fildfgi_id+"").data("desc7");
  var item_sqid           =   $("#txt"+fildfgi_id+"").data("desc8");
  var item_seid           =   $("#txt"+fildfgi_id+"").data("desc9");
  var item_soid           =   $("#txt"+fildfgi_id+"").data("desc11");
  var item_proid          =   $("#txt"+fildfgi_id+"").data("proid");
  var item_rprid          =   $("#txt"+fildfgi_id+"").data("rprid");

  if($(this).is(":checked") == true) {

    // $('#example2').find('.participantRow').each(function(){

    //   var SOID_REF    =   $(this).find('[id*="SOID_REF"]').val();
    //   var FGI_REF  =   $(this).find('[id*="FGI_REF"]').val();
    //   var SQID_REF    =   $(this).find('[id*="SQID_REF"]').val();
    //   var SEID_REF    =   $(this).find('[id*="SEID_REF"]').val();
    //   var exist_val   =   SOID_REF+"_"+SQID_REF+"_"+SEID_REF+"_"+FGI_REF;

    //   if(item_id){
    //     if(item_unique_row_id == exist_val){
    //       $("#FGIIDpopup").hide();
    //       $("#YesBtn").hide();
    //       $("#NoBtn").hide();
    //       $("#OkBtn").hide();
    //       $("#OkBtn1").show();
    //       $("#AlertMessage").text('Item already exists.');
    //       $("#alert").modal('show');
    //       $("#OkBtn1").focus();
    //       highlighFocusBtn('activeOk1');

    //       $('#hdn_FGIID1').val('');
    //       $('#hdn_FGIID2').val('');
    //       $('#hdn_FGIID3').val('');
    //       $('#hdn_FGIID4').val('');
    //       $('#hdn_FGIID5').val('');
    //       $('#hdn_FGIID6').val('');
    //       $('#hdn_FGIID7').val('');
    //       $('#hdn_FGIID8').val('');
    //       $('#hdn_FGIID9').val('');
    //       $('#hdn_FGIID10').val('');
    //       $('#hdn_FGIID11').val('');
           
    //       item_id             =   '';
    //       item_code           =   '';
    //       item_name           =   '';
    //       item_main_uom_id    =   '';
    //       item_main_uom_code  =   '';
    //       item_qty            =   '';
    //       item_unique_row_id  =   '';
    //       item_sqid           =   '';
    //       item_seid           =   '';
    //       item_soid           =   '';
    //       return false;
    //     }               
    //   } 
               
    // });

    if($('#hdn_FGIID1').val() == "" && item_id != ''){

      var $tr       =   $('.material').closest('table');
      var allTrs    =   $tr.find('.participantRow').last();
      var lastTr    =   allTrs[allTrs.length-1];
      var $clone    =   $(lastTr).clone();

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
      $clone.find('[id*="popupFGI"]').val(item_code);
      $clone.find('[id*="FGI_REF"]').val(item_id);
      $clone.find('[id*="FGIName"]').val(item_name);
      $clone.find('[id*="popupMAINITEMUOM"]').val(item_main_uom_code);
      $clone.find('[id*="MAINITEM_UOMID_REF"]').val(item_main_uom_id);
      //$clone.find('[id*="QTY"]').val(item_qty);
      //$clone.find('[id*="REQ_QTY"]').val(item_qty);
      $clone.find('[id*="SQID_REF"]').val(item_sqid);
      $clone.find('[id*="SEID_REF"]').val(item_seid);
      $clone.find('[id*="SOID_REF"]').val(item_soid);
      $clone.find('[id*="PROID_REF"]').val(item_proid);
      $clone.find('[id*="RPRID_REF"]').val(item_rprid);
      //clear sub item
      $clone.find('[id*="popupITEMID"]').val('');
      $clone.find('[id*="ITEMID_REF"]').val('');
      $clone.find('[id*="ItemName"]').val('');
      $clone.find('[id*="popupMUOM"]').val('');
      $clone.find('[id*="MAIN_UOMID_REF"]').val('');
      $clone.find('[id*="QTY"]').val('0.000');
      $clone.find('[id*="SHORT_QTY"]').val('');
      $clone.find('[id*="popupALTUOM"]').val('');
      $clone.find('[id*="ALT_UOMID_REF"]').val('');

      $clone.find('[id*="REMARKS"]').val('');

      $tr.closest('table').append($clone);   
      var rowCount = $('#Row_Count1').val();
      rowCount    = parseInt(rowCount)+1;
      $('#Row_Count1').val(rowCount);
      $("#FGIIDpopup").hide();
      
      event.preventDefault();

    }
    else{
     
      var txt_id1   =   $('#hdn_FGIID1').val();
      var txt_id2   =   $('#hdn_FGIID2').val();
      var txt_id3   =   $('#hdn_FGIID3').val();
      var txt_id4   =   $('#hdn_FGIID4').val();
      var txt_id5   =   $('#hdn_FGIID5').val();
      var txt_id6   =   $('#hdn_FGIID6').val();
      var txt_id7   =   $('#hdn_FGIID7').val();
      var txt_id8   =   $('#hdn_FGIID8').val();
      var txt_id9   =   $('#hdn_FGIID9').val();
      var txt_id10  =   $('#hdn_FGIID10').val();
      var txt_id11  =   $('#hdn_FGIID11').val();
     
      if($.trim(txt_id1)!=""){
        $('#'+txt_id1).val(item_code);
        $('#'+txt_id1).parent().parent().find('[id*="popupITEMID"]').val('');
        $('#'+txt_id1).parent().parent().find('[id*="ITEMID_REF"]').val('');
        $('#'+txt_id1).parent().parent().find('[id*="ItemName"]').val('');
        $('#'+txt_id1).parent().parent().find('[id*="popupMUOM"]').val('');
        $('#'+txt_id1).parent().parent().find('[id*="MAIN_UOMID_REF"]').val('');
        $('#'+txt_id1).parent().parent().find('[id*="QTY"]').val('0.000');
        $('#'+txt_id1).parent().parent().find('[id*="SHORT_QTY"]').val('');
        $('#'+txt_id1).parent().parent().find('[id*="popupALTUOM"]').val('');
        $('#'+txt_id1).parent().parent().find('[id*="ALT_UOMID_REF"]').val('');
        $('#'+txt_id1).parent().parent().find('[id*="REMARKS"]').val('');

        $('#'+txt_id1).parent().parent().find('[id*="PROID_REF"]').val(item_proid);
        $('#'+txt_id1).parent().parent().find('[id*="RPRID_REF"]').val(item_rprid);
        
      }
      if($.trim(txt_id2)!=""){
        $('#'+txt_id2).val(item_id);
      }
      if($.trim(txt_id3)!=""){
        $('#'+txt_id3).val(item_name);
      }
      if($.trim(txt_id4)!=""){
        $('#'+txt_id4).val(item_main_uom_code);
      }
      if($.trim(txt_id5)!=""){
        $('#'+txt_id5).val(item_main_uom_id);
      }
      if($.trim(txt_id6)!=""){
       // $('#'+txt_id6).val(item_qty);
      }
      if($.trim(txt_id7)!=""){
        $('#'+txt_id7).val(0);
      }
      if($.trim(txt_id8)!=""){
       // $('#'+txt_id8).val(item_qty);
      }
      if($.trim(txt_id9)!=""){
        $('#'+txt_id9).val(item_sqid);
      }
      if($.trim(txt_id10)!=""){
        $('#'+txt_id10).val(item_seid);
      }
      if($.trim(txt_id11)!=""){
        $('#'+txt_id11).val(item_soid);
      }
      $('#hdn_FGIID1').val('');
      $('#hdn_FGIID2').val('');
      $('#hdn_FGIID3').val('');
      $('#hdn_FGIID4').val('');
      $('#hdn_FGIID5').val('');
      $('#hdn_FGIID6').val('');
      $('#hdn_FGIID7').val('');
      $('#hdn_FGIID8').val('');
      $('#hdn_FGIID9').val('');
      $('#hdn_FGIID10').val('');
      $('#hdn_FGIID11').val('');
      
    }
            
    $("#FGIIDpopup").hide();
    event.preventDefault();
  }
  else if($(this).is(":checked") == false){

    var id = item_id;
    var r_count = $('#Row_Count1').val();

    $('#example2').find('.participantRow').each(function(){
      var FGI_REF = $(this).find('[id*="FGI_REF"]').val();

      if(id == FGI_REF){
        var rowCount = $('#Row_Count1').val();

        if (rowCount > 1) {
          $(this).closest('.participantRow').remove(); 
          rowCount = parseInt(rowCount)-1;
          $('#Row_Count1').val(rowCount);
        }
        else {
          $(document).find('.dmaterial').prop('disabled', true);  
          $("#FGIIDpopup").hide();
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

  $("#fgicodesearch").val(''); 
  $("#fginamesearch").val(''); 
  $("#fgiUOMsearch").val(''); 
  $("#fgiGroupsearch").val(''); 
  $("#fgiCategorysearch").val(''); 
  $("#fgiStatussearch").val(''); 
  $('.remove').removeAttr('disabled'); 
  FGICodeFunction();
  event.preventDefault();
});

} //bindFGIEvents
    
/*=================================================================================*/
/*================================== ITEM DETAILS =================================*/
/*=================================================================================*/

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


// function ItemBUFunction() {
// 	var input, filter, table, tr, td, i, txtValue;
// 	input = document.getElementById("ItemBUsearch");
// 	filter = input.value.toUpperCase();
// 	table = document.getElementById("ItemIDTable2");
// 	tr = table.getElementsByTagName("tr");
// 	for (i = 0; i < tr.length; i++) {
// 	  td = tr[i].getElementsByTagName("td")[7];
// 	  if (td) {
// 		txtValue = td.textContent || td.innerText;
// 		if (txtValue.toUpperCase().indexOf(filter) > -1) {
// 		  tr[i].style.display = "";
// 		} else {
// 		  tr[i].style.display = "none";
// 		}
// 	  }       
// 	}
// }

function ItemAPNFunction() {
	var input, filter, table, tr, td, i, txtValue;
	input = document.getElementById("ItemAPNsearch");
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

function ItemCPNFunction() {
	var input, filter, table, tr, td, i, txtValue;
	input = document.getElementById("ItemCPNsearch");
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

function ItemOEMPNFunction() {
	var input, filter, table, tr, td, i, txtValue;
	input = document.getElementById("ItemOEMPNsearch");
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


function ItemStatusFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("ItemStatussearch");
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

$('#Material').on('focus','[id*="popupITEMID"]',function(event){

  var RPRID_REF    = $("#RPRID_REF").val();
  var MISRID       = $("#MISRID").val();

  var FGI_REF        =  $.trim( $(this).parent().parent().find('[id*="FGI_REF"]').val() );  //main item id
  var SQID_REF       = $(this).parent().parent().find('[id*="SQID_REF"]').val();
  var SEID_REF       = $(this).parent().parent().find('[id*="SEID_REF"]').val();
  var SOID_REF       = $(this).parent().parent().find('[id*="SOID_REF"]').val();

  var ITEMID_REF       = $.trim($(this).parent().parent().find('[id*="ITEMID_REF"]').val() );

  if(RPRID_REF ===""){
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select RPR No.');
    $("#alert").modal('show')
    $("#OkBtn1").focus();
    return false;

  }else if(FGI_REF ===""){
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select Main Item.');
    $("#alert").modal('show')
    $("#OkBtn1").focus();
    return false;
  }

  if(ITEMID_REF==""){

      $('.js-selectall').prop('disabled', true);   
      $("#tbody_FGI").html('');  // for clear varibale confiliction
      $("#tbody_ItemID").html('loading...');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("transaction",[$FormId,"getItemDetails"])}}',
            type:'POST',
            data:{'status':'A','RPRID_REF':RPRID_REF,'FGI_REF':FGI_REF,'ITEMID_REF':ITEMID_REF,'mode':'edit','MISRID':MISRID},
            success:function(data) {
              $("#tbody_ItemID").html(data);    
              bindItemEvents();   
              $('.js-selectall').prop('disabled', false);                     
            },
            error:function(data){
              console.log("Error: Something went wrong.");
              $("#tbody_ItemID").html('');                        
            },
        }); 
          

        $("#ITEMIDpopup").show();
        
      var id1   = $(this).attr('id');
      var id2   = $(this).parent().parent().find('[id*="ITEMID_REF"]').attr('id');
      var id3   = $(this).parent().parent().find('[id*="ItemName"]').attr('id');
      var id4   = $(this).parent().parent().find('[id*="popupMUOM"]').attr('id');
      var id5   = $(this).parent().parent().find('[id*="MAIN_UOMID_REF"]').attr('id');
      var id6   = "";//$(this).parent().parent().find('[id*="QTY"]').attr('id');
      var id7   = "";
      var id8   = ""; //$(this).parent().parent().find('[id*="REQ_QTY"]').attr('id');
      var id9   = $(this).parent().parent().find('[id*="SQID_REF"]').attr('id');
      var id10  = $(this).parent().parent().find('[id*="SEID_REF"]').attr('id');
      var id11  = $(this).parent().parent().find('[id*="SOID_REF"]').attr('id');
      var id12  = $(this).parent().parent().find('[id*="RPRID_REF"]').attr('id');
      var id13  = $(this).parent().parent().find('[id*="PROID_REF"]').attr('id');
      var id14  = $(this).parent().parent().find('[id*="FGI_REF"]').attr('id');

      $('#hdn_ItemID1').val(id1);
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

      var r_count = 0;
      var ItemID = [];
      $('#example2').find('.participantRow').each(function(){
        if($(this).find('[id*="ITEMID_REF"]').val() != '')
        {
          ItemID.push($(this).find('[id*="ITEMID_REF"]').val());
          r_count = parseInt(r_count)+1;
          $('#hdn_ItemID21').val(r_count); // row counter
        }
      });
      $('#hdn_ItemID19').val(ItemID.join(', '));

      var SOID = [];
      $('#example2').find('.participantRow').each(function(){
        if($(this).find('[id*="SOID_REF"]').val() != '')
        {
          SOID.push($(this).find('[id*="SOID_REF"]').val());
        }
      });
      $('#hdn_ItemID23').val(SOID.join(', '));

      var SQID = [];
      $('#example2').find('.participantRow').each(function(){
        if($(this).find('[id*="SQID_REF"]').val() != '')
        {
          SQID.push($(this).find('[id*="SQID_REF"]').val());
        }
      });
      $('#hdn_ItemID24').val(SQID.join(', '));

      var SEID = [];
      $('#example2').find('.participantRow').each(function(){
        if($(this).find('[id*="SEID_REF"]').val() != '')
        {
          SEID.push($(this).find('[id*="SEID_REF"]').val());
        }
      });
      $('#hdn_ItemID25').val(SEID.join(', '));
        
      var RPRID = [];
      $('#example2').find('.participantRow').each(function(){
        if($(this).find('[id*="RPRID_REF"]').val() != '')
        {
          RPRID.push($(this).find('[id*="RPRID_REF"]').val());
        }
      });
      $('#hdn_ItemID26').val(RPRID.join(', '));

      var PROID = [];
      $('#example2').find('.participantRow').each(function(){
        if($(this).find('[id*="PROID_REF"]').val() != '')
        {
          PROID.push($(this).find('[id*="PROID_REF"]').val());
        }
      });
      $('#hdn_ItemID27').val(PROID.join(', '));

      var FGIID = [];
      $('#example2').find('.participantRow').each(function(){
        if($(this).find('[id*="FGI_REF"]').val() != '')
        {
          FGIID.push($(this).find('[id*="FGI_REF"]').val());
        }
      });
      $('#hdn_ItemID28').val(FGIID.join(', '));
  }  
  else{

      //substitute items
      $('#hdn_SUBITEMid1').val($(this).attr('id'));
      $('#hdn_SUBITEMid2').val($(this).parent().parent().find('[id*="ItemName"]').attr('id'));       
      $('#hdn_SUBITEMid3').val($(this).parent().parent().find('[id*="ITEMID_REF"]').attr('id'));
      //$('#hdn_SUBITEMid4').val($(this).parent().parent().find('[id*="OLDITEM_ID_REF"]').attr('id'));       
      $('#hdn_SUBITEMid5').val($(this).parent().parent().find('[id*="popupMUOM"]').attr('id'));       
      $('#hdn_SUBITEMid6').val($(this).parent().parent().find('[id*="MAIN_UOMID_REF"]').attr('id'));       
      $('#hdn_SUBITEMid7').val($(this).parent().parent().find('[id*="popupALTUOM"]').attr('id'));       
      $('#hdn_SUBITEMid8').val($(this).parent().parent().find('[id*="ALT_UOMID_REF"]').attr('id'));       
      $('#hdn_SUBITEMid9').val($(this).parent().parent().find('[id*="RECEIVED_QTY_AU"]').attr('id'));    

     
      var RPR_REQ_QTY   = $(this).parent().parent().find('[id*="RPR_REQ_QTY"]').val();     
      var ITEMID_REF    = $.trim($(this).parent().parent().find('[id*="ITEMID_REF"]').val() );
      var ITEMID_REF    = $.trim($(this).parent().parent().find('[id*="ITEMID_REF"]').val() );
      var PROID_REF     = $(this).parent().parent().find('[id*="PROID_REF"]').val();

      var fieldid = $(this).parent().parent().find('[id*="ITEMID_REF"]').attr('id');


      $("#SUBITEMpopup").show();
      $("#tbody_SUBITEM").html('');

      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      })

      $.ajax({
          url:'{{route("transaction",[$FormId,"getSUBITEMCodeNo"])}}',
          type:'POST',
          //data:{'REQ_BOMID_REF':REQ_BOMID_REF,REQ_ITEMID:REQ_ITEMID,MAIN_PD_OR_QTY:MAIN_PD_OR_QTY},
          data:{'status':'A','ITEMID_REF':ITEMID_REF,'RPR_REQ_QTY':RPR_REQ_QTY, 'fieldid':fieldid,   
          'FGI_REF':FGI_REF,        
          'SOID_REF':SOID_REF,
          'SQID_REF':SQID_REF,
          'SEID_REF':SEID_REF,
          'RPRID_REF':RPRID_REF,
          'PROID_REF':PROID_REF,
          'FMODE':'edit'
          },
          success:function(data) {
            $("#tbody_SUBITEM").html(data);
            BindSUBITEM();
            showSelectedCheck($("#"+fieldid).val(),"SELECT_"+fieldid);
          },
          error:function(data){
            console.log("Error: Something went wrong.");
            $("#tbody_SUBITEM").html('');
          },
      });
    //------------------------------------------------ 

  }
  event.preventDefault();
});

$("#ITEMID_closePopup").click(function(event){
$("#ITEMIDpopup").hide();
});


function bindItemEvents(){

$('#ItemIDTable2').off(); 
//-------------------------
$('.js-selectall').change(function()
{ 
  //select all checkbox
  var isChecked = $(this).prop("checked");
  var selector = $(this).data('target');
  $(selector).prop("checked", isChecked);
  //$$--------------------
  $('#ItemIDTable2').find('.clsitemid').each(function()
      {
          var fieldid             =   $(this).attr('id');
          var item_id             =   $("#txt"+fieldid+"").data("desc1");
          var item_code           =   $("#txt"+fieldid+"").data("desc2");
          var item_name           =   $("#txt"+fieldid+"").data("desc3");
          var item_main_uom_id    =   $("#txt"+fieldid+"").data("desc4");
          var item_main_uom_code  =   $("#txt"+fieldid+"").data("desc5");
          var item_qty            =   $("#txt"+fieldid+"").data("desc6");
          var item_unique_row_id  =   $("#txt"+fieldid+"").data("desc7");
          var item_sqid           =   $("#txt"+fieldid+"").data("desc8");
          var item_seid           =   $("#txt"+fieldid+"").data("desc9");
          var item_soid           =   $("#txt"+fieldid+"").data("desc10");
          var item_rprid          =   $("#txt"+fieldid+"").data("rpridref");
          var item_proid          =   $("#txt"+fieldid+"").data("proidref");
          var item_alt_uom_desc        =   $("#txt"+fieldid+"").data("alt_uom_desc");
          var item_alt_uom_id          =   $("#txt"+fieldid+"").data("alt_uom_id");
          var item_aultumquantity      =   $("#txt"+fieldid+"").data("aultumquantity");
          var item_pending_qty         =   $("#txt"+fieldid+"").data("pending_qty");
          var item_fgiid           =   $("#txt"+fieldid+"").data("itemfgiid");
          var item_rpr_reqqty      =   $("#txt"+fieldid+"").data("rpr_reqqty");  // in add case passed the RPR_REQ_QTY OF [TBL_TRN_PDRPR_MAT] in controller for getting RECEIVED_QTY_AU when user select substitute item popup

          var apartno =  $("#addinfo"+fieldid+"").data("desc101");
          var cpartno =  $("#addinfo"+fieldid+"").data("desc102");
          var opartno =  $("#addinfo"+fieldid+"").data("desc103");
        
          var rcount1 = parseInt($(this).closest('table').find('.clsitemid').length);
          var rcount2 = $('#hdn_ItemID21').val();
          var r_count2 = 0;
          
          var gridRow2 = [];
          $('#example2').find('.participantRow').each(function(){
            if($(this).find('[id*="ITEMID_REF"]').val() != '')
            {
              var slitem =$(this).find('[id*="SOID_REF"]').val()+'_'+$(this).find('[id*="SQID_REF"]').val()+'_'+$(this).find('[id*="SEID_REF"]').val()+'_'+$(this).find('[id*="ITEMID_REF"]').val()+'_'+$(this).find('[id*="RPRID_REF"]').val()+'_'+$(this).find('[id*="PROID_REF"]').val()+'_'+$(this).find('[id*="FGI_REF"]').val();
              gridRow2.push(slitem);
              r_count2 = parseInt(r_count2) + 1;
            }
          });
      
          var slids =  $('#hdn_ItemID18').val();
          var itemids =  $('#hdn_ItemID19').val();
          var soids =  $('#hdn_ItemID23').val();
          var sqids =  $('#hdn_ItemID24').val();
          var seids =  $('#hdn_ItemID25').val();
          var rprids =  $('#hdn_ItemID26').val();
          var proids =  $('#hdn_ItemID27').val();
          var fgiids =  $('#hdn_ItemID28').val();
  
          if($(this).find('[id*="chkId"]').is(":checked") == true) 
          {
            rcount1 = parseInt(rcount2)+parseInt(rcount1);
            if(parseInt(r_count2) >= parseInt(rcount1))
            {
              $('#hdn_ItemID1').val('');
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
              $('#hdn_ItemID22').val('');
              
              fieldid             =   "";
              item_id             =   "";
              item_code           =   "";
              item_name           =   "";
              item_main_uom_id    =   "";
              item_main_uom_code  =   "";
              item_qty            =   "";
              item_unique_row_id  =   "";
              item_sqid           =   "";
              item_seid           =   "";
              item_soid           =   "";
              item_rprid           =   "";
              item_proid           =   "";
              item_alt_uom_desc        =  "";
              item_alt_uom_id          =  "";
              item_aultumquantity      =  "";
              item_pending_qty         =  "";
              item_fgiid           =   "";
              item_rpr_reqqty           =   "";

              $('.js-selectall').prop("checked", false);
              $("#ITEMIDpopup").hide();
              return false;
            }
            
            var txtrowitem = item_soid+'_'+item_sqid+'_'+item_seid+'_'+item_id+"_"+item_rprid+"_"+item_proid+"_"+item_fgiid;
            if(jQuery.inArray(txtrowitem, gridRow2) !== -1)
            {
                  $("#ITEMIDpopup").hide();
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").hide();
                  $("#OkBtn1").show();
                  $("#AlertMessage").text('Item already exists!');
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
                  $('#hdn_ItemID22').val('');
                  fieldid             =   "";
                  item_id             =   "";
                  item_code           =   "";
                  item_name           =   "";
                  item_main_uom_id    =   "";
                  item_main_uom_code  =   "";
                  item_qty            =   "";
                  item_unique_row_id  =   "";
                  item_sqid           =   "";
                  item_seid           =   "";
                  item_soid           =   "";
                  item_rprid           =   "";
                  item_proid           =   "";
                  item_alt_uom_desc        =  "";
                  item_alt_uom_id          =  "";
                  item_aultumquantity      =  "";
                  item_pending_qty         =  "";
                  item_fgiid = "";
                  item_rpr_reqqty = "";
                  $('.js-selectall').prop("checked", false);
                  $("#ITEMIDpopup").hide();
                  return false;
            }

            // var slids =  $('#hdn_ItemID18').val();
            // var itemids =  $('#hdn_ItemID19').val();
            // var soids =  $('#hdn_ItemID23').val();
            // var sqids =  $('#hdn_ItemID24').val();
            // var seids =  $('#hdn_ItemID25').val();
            // var rprids =  $('#hdn_ItemID26').val();
            // var proids =  $('#hdn_ItemID27').val();
            // var fgiids =  $('#hdn_ItemID28').val();

            // if(soids.indexOf(item_soid) != -1 && sqids.indexOf(item_sqid) != -1 && seids.indexOf(item_seid) != -1 && itemids.indexOf(item_id) != -1 && rprids.indexOf(item_rprid) != -1 && proids.indexOf(item_proid) != -1 )
            // {
            //               $("#ITEMIDpopup").hide();
            //               $("#YesBtn").hide();
            //               $("#NoBtn").hide();
            //               $("#OkBtn").hide();
            //               $("#OkBtn1").show();
            //               $("#AlertMessage").text('Item already exists.');
            //               $("#alert").modal('show');
            //               $("#OkBtn1").focus();
            //               highlighFocusBtn('activeOk1');
            //               $('#hdn_ItemID1').val('');
            //               $('#hdn_ItemID2').val('');
            //               $('#hdn_ItemID3').val('');
            //               $('#hdn_ItemID4').val('');
            //               $('#hdn_ItemID5').val('');
            //               $('#hdn_ItemID6').val('');
            //               $('#hdn_ItemID7').val('');
            //               $('#hdn_ItemID8').val('');
            //               $('#hdn_ItemID9').val('');
            //               $('#hdn_ItemID10').val('');
            //               $('#hdn_ItemID11').val('');
            //               $('#hdn_ItemID12').val('');
            //               $('#hdn_ItemID13').val('');
            //               $('#hdn_ItemID14').val('');
            //               $('#hdn_ItemID15').val('');
            //               $('#hdn_ItemID16').val('');
            //               $('#hdn_ItemID17').val('');
            //               $('#hdn_ItemID18').val('');
            //               $('#hdn_ItemID19').val('');
            //               $('#hdn_ItemID20').val('');
            //               $('#hdn_ItemID22').val('');
            //               fieldid             =   "";
            //               item_id             =   "";
            //               item_code           =   "";
            //               item_name           =   "";
            //               item_main_uom_id    =   "";
            //               item_main_uom_code  =   "";
            //               item_qty            =   "";
            //               item_unique_row_id  =   "";
            //               item_sqid           =   "";
            //               item_seid           =   "";
            //               item_soid           =   "";
            //               item_rprid          =   "";
            //               item_proid          =   "";
            //               item_alt_uom_desc        =  "";
            //               item_alt_uom_id          =  "";
            //               item_aultumquantity      =  "";
            //               item_pending_qty         =  "";
                          
            //               $('.js-selectall').prop("checked", false);
            //               $("#ITEMIDpopup").hide();
            //               return false;
            // }
                if($('#hdn_ItemID1').val() == "" && item_id != '')
                {
                  var txtid= $('#hdn_ItemID1').val();
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
                  var txt_id22= $('#hdn_ItemID22').val();

                  var txt_id23= $('#hdn_ItemID23').val();
                  var txt_id24= $('#hdn_ItemID24').val();
                  var txt_id25= $('#hdn_ItemID25').val();
                  var txt_id26= $('#hdn_ItemID26').val();
                  var txt_id27= $('#hdn_ItemID27').val();
                  var txt_id28= $('#hdn_ItemID28').val();

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
                      $clone.find('[id*="popupITEMID"]').val(item_code);
                      $clone.find('[id*="ITEMID_REF"]').val(item_id);
                      $clone.find('[id*="OLDITEM_ID_REF_"]').val(item_id);
                      $clone.find('[id*="ItemName"]').val(item_name);
                      $clone.find('[id*="popupMUOM"]').val(item_main_uom_code);
                      $clone.find('[id*="MAIN_UOMID_REF"]').val(item_main_uom_id);
                     // $clone.find('[id*="QTY"]').val(item_qty);
                     // $clone.find('[id*="REQ_QTY"]').val(item_qty);
                      $clone.find('[id*="SQID_REF"]').val(item_sqid);
                      $clone.find('[id*="SEID_REF"]').val(item_seid);
                      $clone.find('[id*="SOID_REF"]').val(item_soid);
                      $clone.find('[id*="RPRID_REF"]').val(item_rprid);
                      $clone.find('[id*="PROID_REF"]').val(item_proid);
                      $clone.find('[id*="popupALTUOM"]').val(item_alt_uom_desc);
                      $clone.find('[id*="ALT_UOMID_REF"]').val(item_alt_uom_id);
                      $clone.find('[id*="PENDING_QTY"]').val(item_pending_qty);
                      $clone.find('[id*="RECEIVED_QTY_AU"]').val(item_aultumquantity);        
                      $clone.find('[id*="RPR_REQ_QTY"]').val(item_rpr_reqqty);        
                      $clone.find('[id*="TotalHiddenQty"]').val('');
                      $clone.find('[id*="HiddenRowId"]').val('');
                      $clone.find('[id*="REMARKS"]').val('');

                      $clone.find('[id*="Alpspartno"]').val(apartno);
                      $clone.find('[id*="Custpartno"]').val(cpartno);
                      $clone.find('[id*="OEMpartno"]').val(opartno);
                                            
                      $tr.closest('table').append($clone);   
                      var rowCount = $('#Row_Count1').val();
                      rowCount = parseInt(rowCount)+1;
                      $('#Row_Count1').val(rowCount);
                    
                      event.preventDefault();
                }
                else
                {
                                                            
                    var txt_id1   =   $('#hdn_ItemID1').val();
                    var txt_id2   =   $('#hdn_ItemID2').val();
                    var txt_id3   =   $('#hdn_ItemID3').val();
                    var txt_id4   =   $('#hdn_ItemID4').val();
                    var txt_id5   =   $('#hdn_ItemID5').val();
                    var txt_id6   =   $('#hdn_ItemID6').val();
                    var txt_id7   =   $('#hdn_ItemID7').val();
                    var txt_id8   =   $('#hdn_ItemID8').val();
                    var txt_id9   =   $('#hdn_ItemID9').val();
                    var txt_id10  =   $('#hdn_ItemID10').val();
                    var txt_id11  =   $('#hdn_ItemID11').val();
                    var txt_id12  =   $('#hdn_ItemID12').val();
                    var txt_id13  =   $('#hdn_ItemID13').val();
                   
                  
                    $('#'+txt_id1).val(item_code);
                    $('#'+txt_id2).val(item_id);
                    $('#'+txt_id3).val(item_name);
                    $('#'+txt_id4).val(item_main_uom_code);
                    $('#'+txt_id5).val(item_main_uom_id);
                   // $('#'+txt_id6).val(item_qty);
                    //$('#'+txt_id7).val(0);
                   // $('#'+txt_id8).val(item_qty);
                    $('#'+txt_id9).val(item_sqid);
                    $('#'+txt_id10).val(item_seid);
                    $('#'+txt_id11).val(item_soid);
                    $('#'+txt_id12).val(item_rprid);
                    $('#'+txt_id13).val(item_proid);

                    $('#'+txt_id2).parent().parent().find('[id*="OLDITEM_ID_REF_"]').val(item_id);
                    $('#'+txt_id2).parent().parent().find('[id*="popupALTUOM"]').val(item_alt_uom_desc);
                    $('#'+txt_id2).parent().parent().find('[id*="ALT_UOMID_REF"]').val(item_alt_uom_id);
                    $('#'+txt_id2).parent().parent().find('[id*="PENDING_QTY"]').val(item_pending_qty);
                    $('#'+txt_id2).parent().parent().find('[id*="RECEIVED_QTY_AU"]').val(item_aultumquantity);        
                    $('#'+txt_id2).parent().parent().find('[id*="RPR_REQ_QTY"]').val(item_rpr_reqqty);        
                    $('#'+txt_id2).parent().parent().find('[id*="TotalHiddenQty"]').val('');
                    $('#'+txt_id2).parent().parent().find('[id*="HiddenRowId"]').val('');
                    $('#'+txt_id2).parent().parent().find('[id*="REMARKS"]').val('');

                    $('#'+txt_id2).parent().parent().find('[id*="Alpspartno"]').val(apartno);
                    $('#'+txt_id2).parent().parent().find('[id*="Custpartno"]').val(cpartno);
                    $('#'+txt_id2).parent().parent().find('[id*="OEMpartno"]').val(opartno);
              
                    $('#hdn_ItemID1').val('');
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

                    event.preventDefault();
                }

                $('.js-selectall').prop("checked", false);
                // $("#ITEMIDpopup").reload();
                $('#ITEMIDpopup').hide();
                event.preventDefault();
                
          }
          // else if($(this).is(":checked") == false) 
          // {
          //   
          // }
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
  //$$--------------------
  
  $('#ITEMIDpopup').hide();
  return false;
  event.preventDefault();

}); 

//-------------------------

$('[id*="chkId"]').change(function(){

  var fieldid             =   $(this).parent().parent().attr('id');
  var item_id             =   $("#txt"+fieldid+"").data("desc1");
  var item_code           =   $("#txt"+fieldid+"").data("desc2");
  var item_name           =   $("#txt"+fieldid+"").data("desc3");
  var item_main_uom_id    =   $("#txt"+fieldid+"").data("desc4");
  var item_main_uom_code  =   $("#txt"+fieldid+"").data("desc5");
  var item_qty            =   $("#txt"+fieldid+"").data("desc6");
  var item_unique_row_id  =   $("#txt"+fieldid+"").data("desc7");
  var item_sqid           =   $("#txt"+fieldid+"").data("desc8");
  var item_seid           =   $("#txt"+fieldid+"").data("desc9");
  var item_soid           =   $("#txt"+fieldid+"").data("desc10");
  var item_rprid          =   $("#txt"+fieldid+"").data("rpridref");
  var item_proid          =   $("#txt"+fieldid+"").data("proidref");
  var item_alt_uom_desc        =   $("#txt"+fieldid+"").data("alt_uom_desc");
  var item_alt_uom_id          =   $("#txt"+fieldid+"").data("alt_uom_id");
  var item_aultumquantity      =   $("#txt"+fieldid+"").data("aultumquantity");
  var item_pending_qty         =   $("#txt"+fieldid+"").data("pending_qty"); 
  var item_fgiid              =   $("#txt"+fieldid+"").data("itemfgiid");   
  var item_rpr_reqqty           =   $("#txt"+fieldid+"").data("rpr_reqqty");   // in add case use RPR_REQ_QTY OF [TBL_TRN_PDRPR_MAT] for getting RECEIVED_QTY_AU  when user select substitute item popup

  var apartno =  $("#addinfo"+fieldid+"").data("desc101");
  var cpartno =  $("#addinfo"+fieldid+"").data("desc102");
  var opartno =  $("#addinfo"+fieldid+"").data("desc103");

  if($(this).is(":checked") == true) {

    $('#example2').find('.participantRow').each(function(){

      var SOID_REF    =   $(this).find('[id*="SOID_REF"]').val();
      var ITEMID_REF  =   $(this).find('[id*="ITEMID_REF"]').val();
      var SQID_REF    =   $(this).find('[id*="SQID_REF"]').val();
      var SEID_REF    =   $(this).find('[id*="SEID_REF"]').val();
      var RPRID_REF   =   $(this).find('[id*="RPRID_REF"]').val();
      var PROID_REF   =   $(this).find('[id*="PROID_REF"]').val();
      var FGI_REF     =   $(this).find('[id*="FGI_REF"]').val();
      var exist_val   =   SOID_REF+"_"+SQID_REF+"_"+SEID_REF+"_"+ITEMID_REF+"_"+RPRID_REF+"_"+PROID_REF+"_"+FGI_REF;

      if(item_id){
        if(item_unique_row_id == exist_val){
          $("#ITEMIDpopup").hide();
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn").hide();
          $("#OkBtn1").show();
          $("#AlertMessage").text('Item already exists!!');
          $("#alert").modal('show');
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk1');

          $('#hdn_ItemID1').val('');
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
          
          item_id             =   '';
          item_code           =   '';
          item_name           =   '';
          item_main_uom_id    =   '';
          item_main_uom_code  =   '';
          item_qty            =   '';
          item_unique_row_id  =   '';
          item_sqid           =   '';
          item_seid           =   '';
          item_soid           =   '';
          item_rprid           =   '';
          item_proid           =   '';
          item_alt_uom_desc        =  "";
          item_alt_uom_id          =  "";
          item_aultumquantity      =  "";
          item_pending_qty         =  "";
          item_rpr_reqqty         =  "";

          return false;
        }               
      } 
              
    });

    if($('#hdn_ItemID1').val() == "" && item_id != ''){

      var $tr       =   $('.material').closest('table');
      var allTrs    =   $tr.find('.participantRow').last();
      var lastTr    =   allTrs[allTrs.length-1];
      var $clone    =   $(lastTr).clone();

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
      $clone.find('[id*="popupITEMID"]').val(item_code);
      $clone.find('[id*="ITEMID_REF"]').val(item_id);
      $clone.find('[id*="OLDITEM_ID_REF_"]').val(item_id);
      $clone.find('[id*="ItemName"]').val(item_name);
      $clone.find('[id*="popupMUOM"]').val(item_main_uom_code);
      $clone.find('[id*="MAIN_UOMID_REF"]').val(item_main_uom_id);
      //$clone.find('[id*="QTY"]').val(item_qty);
      //$clone.find('[id*="REQ_QTY"]').val(item_qty);
      $clone.find('[id*="SQID_REF"]').val(item_sqid);
      $clone.find('[id*="SEID_REF"]').val(item_seid);
      $clone.find('[id*="SOID_REF"]').val(item_soid);
      $clone.find('[id*="RPRID_REF"]').val(item_rprid);
      $clone.find('[id*="PROID_REF"]').val(item_proid);
      $clone.find('[id*="popupALTUOM"]').val(item_alt_uom_desc);
      $clone.find('[id*="ALT_UOMID_REF"]').val(item_alt_uom_id);
      $clone.find('[id*="PENDING_QTY"]').val(item_pending_qty);
      $clone.find('[id*="RECEIVED_QTY_AU"]').val(item_aultumquantity);        
      $clone.find('[id*="RPR_REQ_QTY"]').val(item_rpr_reqqty);         
      $clone.find('[id*="TotalHiddenQty"]').val('');
      $clone.find('[id*="HiddenRowId"]').val('');
      $clone.find('[id*="REMARKS"]').val('');

      $clone.find('[id*="Alpspartno"]').val(apartno);
      $clone.find('[id*="Custpartno"]').val(cpartno);
      $clone.find('[id*="OEMpartno"]').val(opartno);

      $tr.closest('table').append($clone);   
      var rowCount = $('#Row_Count1').val();
      rowCount    = parseInt(rowCount)+1;
      $('#Row_Count1').val(rowCount);
      $("#ITEMIDpopup").hide();
      
      event.preventDefault();

    }
    else{

        var txt_id1   =   $('#hdn_ItemID1').val();
        var txt_id2   =   $('#hdn_ItemID2').val();
        var txt_id3   =   $('#hdn_ItemID3').val();
        var txt_id4   =   $('#hdn_ItemID4').val();
        var txt_id5   =   $('#hdn_ItemID5').val();
        var txt_id6   =   $('#hdn_ItemID6').val();
        var txt_id7   =   $('#hdn_ItemID7').val();
        var txt_id8   =   $('#hdn_ItemID8').val();
        var txt_id9   =   $('#hdn_ItemID9').val();
        var txt_id10  =   $('#hdn_ItemID10').val();
        var txt_id11  =   $('#hdn_ItemID11').val();
        var txt_id12  =   $('#hdn_ItemID12').val();
        var txt_id13  =   $('#hdn_ItemID13').val();
      
        if($.trim(txt_id1)!=""){
          $('#'+txt_id1).val(item_code);
        }
        if($.trim(txt_id2)!=""){
          $('#'+txt_id2).val(item_id);
          $('#'+txt_id2).parent().parent().find('[id*="OLDITEM_ID_REF_"]').val(item_id);
          $('#'+txt_id2).parent().parent().find('[id*="popupALTUOM"]').val(item_alt_uom_desc);
          $('#'+txt_id2).parent().parent().find('[id*="ALT_UOMID_REF"]').val(item_alt_uom_id);
          $('#'+txt_id2).parent().parent().find('[id*="PENDING_QTY"]').val(item_pending_qty);
          $('#'+txt_id2).parent().parent().find('[id*="RECEIVED_QTY_AU"]').val(item_aultumquantity);        
          $('#'+txt_id2).parent().parent().find('[id*="RPR_REQ_QTY"]').val(item_rpr_reqqty);        
          $('#'+txt_id2).parent().parent().find('[id*="TotalHiddenQty"]').val('');
          $('#'+txt_id2).parent().parent().find('[id*="HiddenRowId"]').val('');
          $('#'+txt_id2).parent().parent().find('[id*="REMARKS"]').val('');

          $('#'+txtid).parent().parent().find('[id*="Alpspartno"]').val(apartno);
          $('#'+txtid).parent().parent().find('[id*="Custpartno"]').val(cpartno);
          $('#'+txtid).parent().parent().find('[id*="OEMpartno"]').val(opartno);
          
        }
        if($.trim(txt_id3)!=""){
          $('#'+txt_id3).val(item_name);
        }
        if($.trim(txt_id4)!=""){
          $('#'+txt_id4).val(item_main_uom_code);
        }
        if($.trim(txt_id5)!=""){
          $('#'+txt_id5).val(item_main_uom_id);
        }
        if($.trim(txt_id6)!=""){
         // $('#'+txt_id6).val(item_qty);
        }
        if($.trim(txt_id7)!=""){
          $('#'+txt_id7).val(0);
        }
        if($.trim(txt_id8)!=""){
          //$('#'+txt_id8).val(item_qty);
        }
        if($.trim(txt_id9)!=""){
          $('#'+txt_id9).val(item_sqid);
        }
        if($.trim(txt_id10)!=""){
          $('#'+txt_id10).val(item_seid);
        }
        if($.trim(txt_id11)!=""){
          $('#'+txt_id11).val(item_soid);
        }
        if($.trim(txt_id12)!=""){
          $('#'+txt_id12).val(item_rprid);
        }
        if($.trim(txt_id13)!=""){
          $('#'+txt_id13).val(item_proid);
        }
        $('#hdn_ItemID1').val('');
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
      
    }
            
    $("#ITEMIDpopup").hide();
    event.preventDefault();
  }
  else if($(this).is(":checked") == false){

    var id = item_id;
    var r_count = $('#Row_Count1').val();

    $('#example2').find('.participantRow').each(function(){
      var ITEMID_REF = $(this).find('[id*="ITEMID_REF"]').val();

      if(id == ITEMID_REF){
        var rowCount = $('#Row_Count1').val();

        if (rowCount > 1) {
          $(this).closest('.participantRow').remove(); 
          rowCount = parseInt(rowCount)-1;
          $('#Row_Count1').val(rowCount);
        }
        else {
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

} //bindItemEvents


/*=================================================================================*/
/*================================== UDF DETAILS ==================================*/
/*=================================================================================*/

$(document).ready(function(e) {
var Material = $("#Material").html(); 
$('#hdnmaterial').val(Material);
var lastdt = <?php echo json_encode($objResponse->MISRDT); ?>;
var today = new Date(); 
var date2 = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
$('#MISRDT').attr('min',lastdt);
$('#MISRDT').attr('max',date2);
//$('[id*="EDD"]').attr('min',sodate);



var seudf = <?php echo json_encode($objUdfData); ?>;
var count2 = <?php echo json_encode($objCountUDF); ?>;

$('#example3').find('.participantRow3').each(function(){
    var txt_id4 = $(this).find('[id*="udfinputid"]').attr('id');
    var udfid = $(this).find('[id*="UDF"]').val();

    $.each( seudf, function( seukey, seuvalue ) {
      if(seuvalue.UDFMISRID_REF == udfid)
      {

        var txtvaltype2 =   seuvalue.VALUETYPE;
        var strdyn2 = txt_id4.split('_');
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
            strinp2 = '<input type="checkbox" name="'+dynamicid2+ '" id="'+dynamicid2+'" class="" > ';
        }
        else if(chkvaltype2=='combobox'){
        var txtoptscombo2 =   seuvalue.DESCRIPTIONS;
        var strarray2 = txtoptscombo2.split(',');
        var opts2 = '';
        for (var i = 0; i < strarray2.length; i++) {
            opts2 = opts2 + '<option value="'+strarray2[i]+'">'+strarray2[i]+'</option> ';
        }
        strinp2 = '<select name="'+dynamicid2+ '" id="'+dynamicid2+'" class="form-control" required>'+opts2+'</select>' ;          
        }
        $('#'+txt_id4).html('');  
        $('#'+txt_id4).html(strinp2);
      }
    });
  });

var count1 = <?php echo json_encode($objCount1); ?>;
var count2 = <?php echo json_encode($objCount2); ?>;
$('#Row_Count1').val(count1);
$('#Row_Count2').val(count2);
var objSE = <?php echo json_encode($objMAT); ?>;
var item = <?php echo json_encode($objItems); ?>;
var uom = <?php echo json_encode($objUOM); ?>;
var uomconv = <?php echo json_encode($objItemUOMConv); ?>;
/*
$.each(objSE, function(sekey,sevalue) {

  //$('#txtRGP_popup_'+sekey).val(sevalue.MRS_NO);

  $.each(item, function(itkey,itvalue) {

      if(sevalue.ITEMID_REF == itvalue.ITEMID){
          $('#popupITEMID_'+sekey).val(itvalue.ICODE);
          $('#ItemName_'+sekey).val(itvalue.NAME);
          $.each(uom, function(uomkey,uomvalue) {
            if(itvalue.MAIN_UOMID_REF == uomvalue.UOMID)
            {
              $('#popupMUOM_'+sekey).val(uomvalue.UOMCODE+'-'+uomvalue.DESCRIPTIONS);
              $('#MAIN_UOMID_REF_'+sekey).val(uomvalue.UOMID);
            }
          });

          $.each(uom, function(uomkey,uomvalue) {
            if(sevalue.ALT_UOMID_REF == uomvalue.UOMID)
            {
              $('#popupALTUOM_'+sekey).val(uomvalue.UOMCODE+'-'+uomvalue.DESCRIPTIONS);
              $('#ALT_UOMID_REF_'+sekey).val(uomvalue.UOMID);
            }             
          });
      }


  });

  


});
*/

var soudf = <?php echo json_encode($objUDF); ?>;
var udfforse = <?php echo json_encode($objUdfData2); ?>;
$.each( soudf, function( soukey, souvalue ) {

  $.each( udfforse, function( usokey, usovalue ) { 
      if(souvalue.UDFMISRID_REF == usovalue.UDFPMISID)
      {
          $('#popupSEID_'+soukey).val(usovalue.LABEL);
      }
  
      if(souvalue.UDFMISRID_REF == usovalue.UDFPMISID){        
              var txtvaltype2 =   usovalue.VALUETYPE;
              var txt_id41 = $('#udfinputid_'+soukey).attr('id');
              var strdyn2 = txt_id41.split('_');
              var lastele2 =   strdyn2[strdyn2.length-1];
              var dynamicid2 = "udfvalue_"+lastele2;
              
              var chkvaltype2 =  txtvaltype2.toLowerCase();
              var strinp2 = '';

              if(chkvaltype2=='date'){

              strinp2 = '<input disabled type="date" placeholder="dd/mm/yyyy" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"  > ';       

              }
              else if(chkvaltype2=='time'){
              strinp2= '<input disabled type="time" placeholder="h:i" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"  > ';

              }
              else if(chkvaltype2=='numeric'){
              strinp2 = '<input disabled type="text" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"   > ';

              }
              else if(chkvaltype2=='text'){

              strinp2 = '<input disabled type="text" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"  > ';
              
              }
              else if(chkvaltype2=='boolean'){
                  if(souvalue.SOUVALUE == "1")
                  {
                  strinp2 = '<input disabled type="checkbox" name="'+dynamicid2+ '" id="'+dynamicid2+'" class="" checked> ';
                  }
                  else{
                  strinp2 = '<input disabled type="checkbox" name="'+dynamicid2+ '" id="'+dynamicid2+'" class="" > ';
                  }
              }
              else if(chkvaltype2=='combobox'){

              var txtoptscombo2 =   usovalue.DESCRIPTIONS;
              var strarray2 = txtoptscombo2.split(',');
              var opts2 = '';

              for (var i = 0; i < strarray2.length; i++) {
                  opts2 = opts2 + '<option value="'+strarray2[i]+'">'+strarray2[i]+'</option> ';
              }

              strinp2 = '<select disabled name="'+dynamicid2+ '" id="'+dynamicid2+'" class="form-control" required>'+opts2+'</select>' ;
              
              }
              
              
              $('#'+txt_id41).html('');  
              $('#'+txt_id41).html(strinp2);   
              $('#'+dynamicid2).val(souvalue.VALUE);
              $('#UDFismandatory_'+soukey).val(usovalue.ISMANDATORY);
          
      }
  });

});


$(function() { $('#MISRNO').focus(); });

$('#btnAdd').on('click', function() {
var viewURL = '{{route("transaction",[$FormId,"add"])}}';
            window.location.href=viewURL;
});

$('#btnExit').on('click', function() {
var viewURL = '{{route('home')}}';
            window.location.href=viewURL;
});

$('#MISRDT').change(function() {
  var mindate  = $(this).val();
  //$('[id*="EDD"]').attr('min',mindate);
});   

//delete row
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
        event.preventDefault();
  }
  event.preventDefault();
});

//add row
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
var d = new Date(); 
var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;

// var h         = new Date($("#EDA").val()); 
// var headDate  = h.getFullYear() + "-" + ("0" + (h.getMonth() + 1)).slice(-2) + "-" + ('0' + h.getDate()).slice(-2) ;

// $clone.find('[id*="EDD"]').val(headDate);

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
});

window.fnUndoYes = function (){
  window.location.reload();
}

window.fnUndoNo = function (){
  $("#MISRNO").focus();
}

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
  var d = new Date(); 
  var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;

  // $("#EDA").on("change", function( event ) {
  //   $('[id*="EDD"]').val($(this).val());
  // });
 
  $('#frm_trn_view1').bootstrapValidator({
     
      fields: {
          txtlabel: {
              validators: {
                  notEmpty: {
                      message: 'The MRS No is required'
                  }
              }
          },            
      },
      submitHandler: function(validator, form, submitButton) {
          alert( "Handler for .submit() called." );
           event.preventDefault();
           $("#frm_trn_view").submit();
      }
  });
});


$( "#btnSaveSE" ).click(function() {
  var formReqData = $("#frm_trn_view");
  if(formReqData.valid()){
    validateForm('fnSaveData');
  }
});

$( "#btnApprove" ).click(function() {
  var formReqData = $("#frm_trn_view");
  if(formReqData.valid()){
    validateForm('fnApproveData');
  }
});


$("#YesBtn").click(function(){

$("#alert").modal('hide');
var customFnName = $("#YesBtn").data("funcname");
  window[customFnName]();

});

window.fnSaveData = function (){
event.preventDefault();
    var trnFormReq = $("#frm_trn_view");
    var formData = trnFormReq.serialize();
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$.ajax({
    url:'{{ route("transactionmodify",[$FormId,"update"])}}',
    type:'POST',
    data:formData,
    success:function(data) {
        if(data.errors) {
            $(".text-danger").hide();
            if(data.errors.LABEL){
                showError('ERROR_LABEL',data.errors.LABEL);
                        $("#YesBtn").hide();
                        $("#NoBtn").hide();
                        $("#OkBtn1").show();
                        $("#AlertMessage").text('Please enter correct value in Label.');
                        $("#alert").modal('show');
                        $("#OkBtn1").focus();
            }
          if(data.country=='norecord') {
              ("#YesBtn").hide();
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
event.preventDefault();
    var trnFormReq = $("#frm_trn_view");
    var formData = trnFormReq.serialize();
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$.ajax({
    url:'{{ route("transactionmodify",[$FormId,"Approve"])}}',
    type:'POST',
    data:formData,
    success:function(data) {
        if(data.errors) {
            $(".text-danger").hide();
            if(data.errors.LABEL){
                showError('ERROR_LABEL',data.errors.LABEL);
                        $("#YesBtn").hide();
                        $("#NoBtn").hide();
                        $("#OkBtn1").show();
                        $("#AlertMessage").text('Please enter correct value in Label.');
                        $("#alert").modal('show');
                        $("#OkBtn1").focus();
            }
          if(data.country=='norecord') {
              ("#YesBtn").hide();
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
  var FocusId=$("#FocusId").val();
  $("#"+FocusId).focus();
  $("#closePopup").click();
}

function highlighFocusBtn(pclass){
  $(".activeYes").hide();
  $(".activeNo").hide();
  
  $("."+pclass+"").show();
}

function validateForm(actionType){

$("#FocusId").val('');
var MISRNO         =   $.trim($("#MISRNO").val());
var MISRDT         =   $.trim($("#MISRDT").val());
var RPRID_REF     =   $.trim($("#RPRID_REF").val());

var STID_REF        =   $.trim($("#STID_REF").val());
var PSTAGEID_REF    =   $.trim($("#PSTAGEID_REF").val());
var EMPID_REF       =   $.trim($("#EMPID_REF").val());
var checkCompany   =   "{{$checkCompany}}";

if(MISRNO ===""){
    $("#FocusId").val($("#MISRNO"));
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('MISR No is required.');
    $("#alert").modal('show')
    $("#OkBtn1").focus();
    return false;
}
else if(MISRDT ===""){
    $("#FocusId").val($("#MISRDT"));
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select MISR Date.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
}
else if(RPRID_REF ===""){
    $("#FocusId").val($("#txtrpr_popup"));
    $("#RPRID_REF").val('');  
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select RPR No.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
} 
else if(STID_REF ===""){
    //$("#FocusId").val($("#txtrpr_popup"));
    $("#STID_REF").val('');  
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select From Store.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
} 
else if(PSTAGEID_REF ===""){
    //$("#FocusId").val($("#txtrpr_popup"));
    $("#PSTAGEID_REF").val('');  
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select To Production Stage.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
} 
else if(EMPID_REF ===""){
    //$("#FocusId").val($("#txtrpr_popup"));
    $("#EMPID_REF").val('');  
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select Issued By.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
}  
else{
  event.preventDefault();
  var allblank = [];
  var allblank1 = [];
  var allblank1_1 = [];
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

  var allblank15  = [];
  var item_array  = new Array();
  var i           = 0;
      
  
  $('#example2').find('.participantRow').each(function(){

    if($.trim($(this).find("[id*=ITEMID_REF]").val())!=""){
        allblank.push('true');

        if($.trim($(this).find("[id*=MAIN_UOMID_REF]").val())!=""){
          allblank2.push('true');

            if(checkCompany ==''){
              if($.trim($(this).find('[id*="RECEIVED_QTY_MU"]').val()) != "" && parseFloat($.trim($(this).find('[id*="RECEIVED_QTY_MU"]').val())) <= parseFloat($.trim($(this).find('[id*="PENDING_QTY"]').val())) ){
                allblank13.push('true');
              }
              else{
                allblank13.push('false');
              }
            }
            else{
              allblank13.push('true');
            }

          if($.trim($(this).find('[id*="RECEIVED_QTY_MU"]').val()) != "" && parseFloat($.trim($(this).find('[id*="RECEIVED_QTY_MU"]').val())) < parseFloat($.trim($(this).find('[id*="PENDING_QTY"]').val())) && $.trim($(this).find('[id*="REASON_SHORT_QTY"]').val()) == "" ){
            allblank14.push('false');
          }
          else{
            allblank14.push('true');
          }  


          if($.trim($(this).find('[id*="RECEIVED_QTY_MU"]').val()) != "" && $.trim($(this).find('[id*="RECEIVED_QTY_MU"]').val()) > 0.000 ){
            allblank3.push('true');
          }
          else{
            allblank3.push('false');
          }  

          if($.trim($(this).find('[id*="RECEIVED_QTY_MU"]').val()) != "" && $.trim($(this).find('[id*="RECEIVED_QTY_MU"]').val()) == $.trim($(this).find('[id*="TotalHiddenQty"]').val()) ){
            allblank7.push('true');
          }
          else{
            allblank7.push('false');
          }  

        }
        else{
            allblank2.push('false');
        }  

        item_array[i]={
            'MISRDT':MISRDT,
            'ITEMID_REF':$(this).find('[id*="ITEMID_REF"]').val(),
            'MAIN_UOMID_REF':$(this).find('[id*="MAIN_UOMID_REF"]').val(),
            'RECEIVED_QTY_MU':$(this).find('[id*="RECEIVED_QTY_MU"]').val(),
            'MAIN_ITEM_CODE':$(this).find('[id*="popupFGI"]').val(),
            'ITEMID_CODE':$(this).find('[id*="popupITEMID"]').val(),
            'ITEMID_NAME':$(this).find('[id*="ItemName"]').val(),
            };

          i++;

    }
    else{
      allblank.push('false');
    }
    });

    $('#example3').find('.participantRow3').each(function(){
    if($.trim($(this).find("[id*=UDF]").val())!=""){
      allblank4.push('true');
      if($.trim($(this).find("[id*=UDFismandatory]").val())=="1"){
        if($.trim($(this).find('[id*="udfvalue"]').val()) != ""){
          allblank5.push('true');
        }
        else{
          allblank5.push('false');
        }
      }  
    }                
    });

    if(jQuery.inArray("false", allblank) !== -1){
    $("#alert").modal('show');
    $("#AlertMessage").text('Please Select Item In Material');
    $("#YesBtn").hide(); 
    $("#NoBtn").hide();  
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk');
  }
  else if(jQuery.inArray("false", allblank2) !== -1){
    $("#alert").modal('show');
    $("#AlertMessage").text('Main UOM is missing in in material tab.');
    $("#YesBtn").hide(); 
    $("#NoBtn").hide();  
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk');
  }
  else if(jQuery.inArray("false", allblank3) !== -1){
    $("#alert").modal('show');
    $("#AlertMessage").text('Issued Qty (MU) cannot be zero or blank in material tab.');
    $("#YesBtn").hide(); 
    $("#NoBtn").hide();  
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk');
  }
  else if(jQuery.inArray("false", allblank7) !== -1){
    $("#alert").modal('show');
    $("#AlertMessage").text('Item quantity cannot be equal of selected store quantity in material tab.');
    $("#YesBtn").hide(); 
    $("#NoBtn").hide();  
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk');
  }
  else if(jQuery.inArray("false", allblank13) !== -1){
    $("#alert").modal('show');
    $("#AlertMessage").text('Issue quantity should not greater then Bal Requested Qty in material tab.');
    $("#YesBtn").hide(); 
    $("#NoBtn").hide();  
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk');
  }
  else if(jQuery.inArray("false", allblank14) !== -1){
    $("#alert").modal('show');
    $("#AlertMessage").text('Please Enter Reason of Short Qty Issued in material tab.');
    $("#YesBtn").hide(); 
    $("#NoBtn").hide();  
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk');
  }
  else if(jQuery.inArray("false", allblank5) !== -1){
    $("#alert").modal('show');
    $("#AlertMessage").text('Please enter  Value / Comment in UDF Tab.');
    $("#YesBtn").hide(); 
    $("#NoBtn").hide();  
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk');
  }
  else{

        var data      = JSON.parse(getTotalDateWiseStock(item_array));
        var alert_msg = data.message;
        allblank15.push(data.result);
          
        if(jQuery.inArray("false", allblank15) !== -1){
          $("#alert").modal('show');
          $("#AlertMessage").text(alert_msg);
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
        }
        else{ 
          $("#alert").modal('show');
          $("#AlertMessage").text('Do you want to save to record.');
          $("#YesBtn").data("funcname",actionType);
          $("#YesBtn").focus();
          $("#OkBtn").hide();
          highlighFocusBtn('activeYes');
        }

  }
}
}

/*=================================================================================*/
/*================================== STORE DETAILS ================================*/
/*=================================================================================*/

$("#example2").on('click', '[class*="checkstore"]', function() {
var ROW_ID      =   $(this).attr('id');
var ITEMID_REF  = $("#ITEMID_REF_"+ROW_ID).val();

if(ITEMID_REF ===""){
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select item code.');
    $("#alert").modal('show')
    $("#OkBtn1").focus();
    return false;
}
else{
    getStoreDetails(ROW_ID);
    $("#StoreModal").show();
    event.preventDefault();
}

});

function getStoreDetails(ROW_ID){

var RPRID_REF          = $("#RPRID_REF_"+ROW_ID).val();
var ITEMID_REF      = $("#ITEMID_REF_"+ROW_ID).val();
var ITEMROWID       = $("#HiddenRowId_"+ROW_ID).val();
var MAIN_UOMID_DES  = $("#popupMUOM_"+ROW_ID).val();
var MAIN_UOMID_REF  = $("#MAIN_UOMID_REF_"+ROW_ID).val();
var ALT_UOMID_DES   = $("#popupALTUOM_"+ROW_ID).val();
var ALT_UOMID_REF   = $("#ALT_UOMID_REF_"+ROW_ID).val();

var PROID_REF          = $("#PROID_REF_"+ROW_ID).val();
var SOID_REF          = $("#SOID_REF_"+ROW_ID).val();
var SQID_REF          = $("#SQID_REF_"+ROW_ID).val();
var SEID_REF          = $("#SEID_REF_"+ROW_ID).val();
var FGI_REF           = $("#FGI_REF_"+ROW_ID).val();

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
      'ROW_ID':ROW_ID,
      'RPRID_REF':RPRID_REF,
      'ITEMID_REF':ITEMID_REF,
      'MAIN_UOMID_DES':MAIN_UOMID_DES,
      'MAIN_UOMID_REF':MAIN_UOMID_REF,
      'ALT_UOMID_DES':ALT_UOMID_DES,
      'ALT_UOMID_REF':ALT_UOMID_REF,
      'ITEMROWID':ITEMROWID,
      'ACTION_TYPE':'EDIT',
      'PROID_REF':PROID_REF,
      'SOID_REF':SOID_REF,
      'SQID_REF':SQID_REF,
      'SEID_REF':SEID_REF,
      'FGI_REF':FGI_REF,
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

var Total_Stock_Inhand=[];
var NewIdArr  = [];
var ROW_ID    =[];
var Req       =[];

$('#StoreTable').find('.participantRow33').each(function(){

    Total_Stock_Inhand.push(parseFloat($.trim($(this).find("[id*=TOTAL_STOCK]").val())));

    if($.trim($(this).find("[id*=UserQty]").val())!=""){  
      var UserQty      = parseFloat($.trim($(this).find("[id*=UserQty]").val()));
      var BatchId      = $.trim($(this).find("[id*=BATCHID]").val());
      var ROWID        = $.trim($(this).find("[id*=ROWID]").val());
      var TOTAL_STOCK  = parseFloat($.trim($(this).find("[id*=TOTAL_STOCK]").val()));
      var BATCHNOA     = $.trim($(this).find("[id*=BATCHNOA]").val());
      
      ROW_ID.push(ROWID);
      NewIdArr.push(BatchId+"_"+UserQty+"_"+TOTAL_STOCK);

      if(UserQty > 0 && BATCHNOA =="1"){
        Req.push('false');
      }
      else{
        Req.push('true');
      }

    } 


});                       

var ROW_ID  = ROW_ID[0];
var Total_Stock_Inhand_Sum  = getArraySum(Total_Stock_Inhand);

$("#HiddenRowId_"+ROW_ID).val(NewIdArr);
$("#STOCK_INHAND_"+ROW_ID).val(parseFloat(Total_Stock_Inhand_Sum).toFixed(3));
$("#StoreModal").hide();

});

function checkStoreQty(ROW_ID,itemid,altumid,userQty,key,stock){

if(parseFloat(userQty) > parseFloat(stock) ){
$("#UserQty_"+key).val('');  
$("#AltUserQty_"+key).val('');  
$("#alert").modal('show');
$("#AlertMessage").text('Issue quantity should not greater then Stock inhand.');
$("#YesBtn").hide(); 
$("#NoBtn").hide();  
$("#OkBtn1").show();
$("#OkBtn1").focus();
highlighFocusBtn('activeOk');
}
else{

$.ajaxSetup({
  headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});

$.ajax({
    url:'{{route("transaction",[$FormId,"changeAltUm"])}}',
    type:'POST',
    data:{altumid:altumid,itemid:itemid,mqty:userQty},
    success:function(data) {
      $("#AltUserQty_"+key).val(data);              
    },
    error:function(data){
      console.log("Error: Something went wrong.");            
    },
}); 

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
var Qty2 = parseFloat($.trim($("#PENDING_QTY_"+ROW_ID).val()));
var ShortQty = parseFloat(parseFloat(Qty2)-parseFloat(TotalQty) ).toFixed(3);

$("#TotalHiddenQty_"+ROW_ID).val(TotalQty);
$("#HiddenRowId_"+ROW_ID).val(NewIdArr);
$("#RECEIVED_QTY_MU_"+ROW_ID).val(TotalQty);  
$("#SHORT_QTY_"+ROW_ID).val(ShortQty);

}

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


function get_all_item(stritemarray){

  var MISRID      =  $("#MISRID").val();

  $("#tbody_ItemID").html('');  //clear for variable confliction
  $("#tbody_FGI").html('');  //clear for variable confliction
  $("#tbodyid").empty();
  $("#tbodyid").html('loading data...');    

  $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $.ajax({
      url:'{{route("transaction",[$FormId,"getAllItem"])}}',
      type:'POST',
      data:{'MISRID':MISRID,'item_array':stritemarray},
      success:function(data) {
        console.log('cc==',data.totalrows);
        $("#Row_Count1").val(data.totalrows);              
        $("#tbodyid").html(data.matrows);              
                        
      },
      error:function(data){
        console.log("Error: Something went wrong.");
        $("#tbodyid").html('');                        
      },
  }); 


}


/*================================== SUB MATERIAL POPUP FUNCTION =================================*/

let SUBITEMTable2 = "#SUBITEMTable2";
let SUBITEMTable = "#SUBITEMTable";
let SUBITEMheaders = document.querySelectorAll(SUBITEMTable + " th");

SUBITEMheaders.forEach(function(element, i) {
element.addEventListener("click", function() {
  w3.sortHTML(SUBITEMTable2, ".clssSUBITEMid", "td:nth-child(" + (i + 1) + ")");
});
});

function SUBITEMCodeFunction() {
var input, filter, table, tr, td, i, txtValue;
input = document.getElementById("SUBITEMcodesearch");
filter = input.value.toUpperCase();
table = document.getElementById("SUBITEMTable2");
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

function SUBITEMNameFunction() {
var input, filter, table, tr, td, i, txtValue;
input = document.getElementById("SUBITEMnamesearch");
filter = input.value.toUpperCase();
table = document.getElementById("SUBITEMTable2");
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


$("#SUBITEM_closePopup").click(function(event){
$("#SUBITEMpopup").hide();
});

function BindSUBITEM(){

$(".clssSUBITEMid").click(function(){
  var fieldid   =   $(this).attr('id');

  var texdesc1   =   $("#txt"+fieldid+"").data("desc1");
  var texdesc2   =   $("#txt"+fieldid+"").data("desc2");
  var texdesc3   =   $("#txt"+fieldid+"").data("desc3");
  var texdesc4   =   $("#txt"+fieldid+"").data("desc4");
  var subitem_uomid   =   $("#txt"+fieldid+"").data("subitem_uomid");
  var subitem_uomdesc   =   $("#txt"+fieldid+"").data("subitem_uomdesc");
  var subitem_altuomid   =   $("#txt"+fieldid+"").data("subitem_altuomid");
  var subitem_altuomdesc   =   $("#txt"+fieldid+"").data("subitem_altuomdesc");
  var subitem_altuomqty   =   $("#txt"+fieldid+"").data("subitem_altuomqty");
  var subitem_uniqitemid   =   $("#txt"+fieldid+"").data("subitem_uniqitemid");
 // var texdesc5   =   $("#txt"+fieldid+"").data("desc5");
//  var texdesc6   =   $("#txt"+fieldid+"").data("desc6");

$('#example2').find('.participantRow').each(function(){

    var SOID_REF    =   $(this).find('[id*="SOID_REF"]').val();
    var ITEMID_REF  =   $(this).find('[id*="ITEMID_REF"]').val();
    var SQID_REF    =   $(this).find('[id*="SQID_REF"]').val();
    var SEID_REF    =   $(this).find('[id*="SEID_REF"]').val();
    var RPRID_REF   =   $(this).find('[id*="RPRID_REF"]').val();
    var PROID_REF   =   $(this).find('[id*="PROID_REF"]').val();
    var FGI_REF     =   $(this).find('[id*="FGI_REF"]').val();
    var exist_val   =   SOID_REF+"_"+SQID_REF+"_"+SEID_REF+"_"+ITEMID_REF+"_"+RPRID_REF+"_"+PROID_REF+"_"+FGI_REF;

    if(ITEMID_REF){
        if(subitem_uniqitemid == exist_val){
          
          $("#SUBITEMpopup").hide();
          $("#SUBITEMcodesearch").val(''); 
          $("#SUBITEMnamesearch").val(''); 
          SUBITEMCodeFunction();
          
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn").hide();
          $("#OkBtn1").show();
          $("#AlertMessage").text('Item already exists !');
          $("#alert").modal('show');
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk1');

          $('#hdn_SUBITEMid1').val('');
          $('#hdn_SUBITEMid2').val('');
          $('#hdn_SUBITEMid3').val('');
          $('#hdn_SUBITEMid4').val('');
          $('#hdn_SUBITEMid5').val('');
          $('#hdn_SUBITEMid6').val('');
          $('#hdn_SUBITEMid7').val('');
          $('#hdn_SUBITEMid8').val('');
          $('#hdn_SUBITEMid9').val('');
          $('#hdn_SUBITEMid10').val('');
                     
          return false;
        }               
    } 
            
  });

  var txt_id1= $('#hdn_SUBITEMid1').val();
  var txt_id2= $('#hdn_SUBITEMid2').val();
  var txt_id3= $('#hdn_SUBITEMid3').val();
  //var txt_id4= $('#hdn_SUBITEMid4').val();

  var txt_subitem_uomdesc= $('#hdn_SUBITEMid5').val();
  var txt_subitem_uomid= $('#hdn_SUBITEMid6').val();
  var txt_subitem_altuomdesc= $('#hdn_SUBITEMid7').val();
  var txt_subitem_altuomid= $('#hdn_SUBITEMid8').val();
  var txt_subitem_altuomqty= $('#hdn_SUBITEMid9').val();

  $('#'+txt_id1).val(texdesc1);
  $('#'+txt_id2).val(texdesc2);
  $('#'+txt_id3).val(texdesc3);
  //$('#'+txt_id4).val(texdesc4);

  $('#'+txt_id1).parent().parent().find('[id*="RECEIVED_QTY_MU"]').val('');
  $('#'+txt_id1).parent().parent().find('[id*="REASON_SHORT_QTY"]').val('');
  $('#'+txt_id1).parent().parent().find('[id*="REMARKS"]').val('');

  $('#'+txt_subitem_uomid).val(subitem_uomid);
  $('#'+txt_subitem_uomdesc).val(subitem_uomdesc);
  $('#'+txt_subitem_altuomid).val(subitem_altuomid);
  $('#'+txt_subitem_altuomdesc).val(subitem_altuomdesc);
  $('#'+txt_subitem_altuomqty).val(subitem_altuomqty);

  $("#SUBITEMpopup").hide();
  $("#SUBITEMcodesearch").val(''); 
  $("#SUBITEMnamesearch").val(''); 
  SUBITEMCodeFunction();

});
}
/*=================================================================================*/


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

var objSO = <?php echo json_encode($objResponse); ?>;

$('#btnPdf').on('click', function() {
  var SONO = objSO.MISRID;
  var Flag = 'P';
  var formData = 'SO='+ SONO + '&MISRID='+ SONO + '&Flag='+ Flag ;
  var consultURL = '{{route("transaction",[305,"ViewReport",":rcdId"]) }}';
  consultURL = consultURL.replace(":rcdId",formData);
  window.location.href=consultURL;
  event.preventDefault();
}); 

$('#btnExcel').on('click', function() {
  var SONO = objSO.MISRID;
  var Flag = 'E';
  var formData = 'SO='+ SONO + '&MISRID='+ SONO + '&Flag='+ Flag ;
  var consultURL = '{{route("transaction",[305,"ViewReport",":rcdId"]) }}';
  consultURL = consultURL.replace(":rcdId",formData);
  window.location.href=consultURL;
  event.preventDefault();
});

$('#btnPrint').on('click', function() {
    var SONO = objSO.MISRID;
    var Flag = 'H';
    var formData = 'SO='+ SONO + '&MISRID='+ SONO + '&Flag='+ Flag ;
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url:'{{route("transaction",[305,"ViewReport"])}}',
        type:'POST',
        data:formData,
        success:function(data) {
            $('#ReportView').show();
            var localS = data;
            document.getElementById('iframe_rpt').src = "data:text/html;charset=utf-8," + escape(localS);
            $('#btnPdf').show();
            $('#btnExcel').show();
            $('#btnPrint').show();
        },
        error:function(data){
            console.log("Error: Something went wrong.");
            var localS = "";
            document.getElementById('iframe_rpt').src = "data:text/html;charset=utf-8," + escape(localS);
            $('#btnPdf').hide();
            $('#btnExcel').hide();
            $('#btnPrint').hide();
        },
    });
    event.preventDefault();
  });

  $('#btnReport').on('click', function() {
    var SONO = objSO.MISRID;
    var Flag = 'R';
    var formData = 'SO='+ SONO + '&MISRID='+ SONO + '&Flag='+ Flag ;
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url:'{{route("transaction",[305,"ViewReport"])}}',
        type:'POST',
        data:formData,
        success:function(data) {
          printWindow = window.open('');
          printWindow.document.write(data);
          printWindow.print();
        },
        error:function(data){
          console.log("Error: Something went wrong.")
          printWindow = window.open('');
          printWindow.document.write("Error: Something went wrong.");
          printWindow.print();
        },
    });
    event.preventDefault();
});

$("#ReportViewclosePopup").click(function(event){
  $("#ReportView").hide();
  event.preventDefault();
});

function getTotalDateWiseStock(DOC_DT,ITEMID_REF,UOMID_REF){

  $.ajaxSetup({
    headers:{
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  })

  var posts = $.ajax({
                url:'{{route("transaction",[$FormId,"getTotalDateWiseStock"])}}',
                type:'POST',
                async: false,
                dataType: 'json',
                data: {DOC_DT:DOC_DT,ITEMID_REF:ITEMID_REF,UOMID_REF:UOMID_REF},
                done: function(response) {return response;}
              }).responseText;

  return posts;
}
</script>
@endpush