
@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
  <div class="row">
    <div class="col-lg-2">
    <a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Job Work Order</a>
    </div>

    <div class="col-lg-10 topnav-pd">
      <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
      <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
      <button class="btn topnavbt" id="btnSaveFormData" ><i class="fa fa-floppy-o"></i> Save</button>
      <button style="display:none" class="btn topnavbt buttonload"> <i class="fa fa-refresh fa-spin"></i> {{Session::get('save')}}</button>
      <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
      <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
      <button class="btn topnavbt" id="btnUndo"  ><i class="fa fa-undo"></i> Undo</button>
      <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
      <button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-thumbs-o-up"></i> Approved</button>
      <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
      <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
    </div>
  </div>
</div>
   
<form id="add_trn_form" method="POST"  >

  <div class="container-fluid purchase-order-view">
        
    @csrf
    <div class="container-fluid filter">

      <div class="inner-form">
      
        <div class="row">
            <div class="col-lg-2 pl"><p>JWO No</p></div>
            <div class="col-lg-2 pl">
            <input type="text" name="JWONO" id="JWONO" value="{{$docarray['DOC_NO']}}" {{$docarray['READONLY']}} class="form-control" maxlength="{{$docarray['MAXLENGTH']}}" autocomplete="off" style="text-transform:uppercase" >
            <script>docMissing(@json($docarray['FY_FLAG']));</script>
            
            </div>
            
            <div class="col-lg-2 pl"><p>JWO Date</p></div>
            <div class="col-lg-2 pl">
                <input type="date" name="JWODT" id="JWODT" onchange='checkPeriodClosing("{{$FormId}}",this.value,1),getDocNoByEvent("JWONO",this,@json($doc_req))' value="{{ old('JWODT') }}" class="form-control mandatory" autocomplete="off" placeholder="dd/mm/yyyy" >
            </div>
            
            <div class="col-lg-2 pl"><p>Vendor</p></div>
            <div class="col-lg-2 pl">
                <input type="text" name="Vendor_popup" id="txtvendor_popup" class="form-control mandatory"  autocomplete="off" readonly/>
                <input type="hidden" name="VID_REF" id="VID_REF" class="form-control" autocomplete="off" />
                <input type="hidden" name="hdnmaterial" id="hdnmaterial" class="form-control" autocomplete="off" />                                                                
                <input type="hidden" name="hdnct" id="hdnct" class="form-control" autocomplete="off" />                                                                
            </div>
        </div>

        <div class="row">
            <div class="col-lg-2 pl"><p>Direct Job Work Order </p></div>
            <div class="col-lg-2 pl">
                  <input type="checkbox" name="DirectJWO" id="DirectJWO" class="form-checkbox"  />
                  <input type="text" name="hiddenDirect" id="hiddenDirect"  hidden/>
            </div>
            <div class="col-lg-2 pl"><p>Total Value</p></div>
            <div class="col-lg-2 pl">
                <input type="text" name="TotalValue" id="TotalValue" class="form-control"  autocomplete="off" readonly  />
            </div>                    
          </div>
                        
      </div>

      <div class="container-fluid purchase-order-view">

        <div class="row">
          <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#Material">Material</a></li>
            <li><a data-toggle="tab" href="#TC">T & C</a></li>
            <li><a data-toggle="tab" href="#udf">UDF</a></li>
            <li><a data-toggle="tab" href="#CT">Calculation Template</a></li>
          </ul>
                            
                            
                            
          <div class="tab-content">

            <div id="Material" class="tab-pane fade in active">
                <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:400px;margin-top:10px;" >
                    <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                      <thead id="thead1"  style="position: sticky;top: 0">
                                                    
                        <tr>
                          <th>PRO No</th>
                          <th>Item Code </th>
                          <th>Item Name</th>
                          <th>UOM</th>
                          <th>Produced Qty</th>
                          <th>Job Work Order Qty</th>
                          <th>EDA</th>
                          <th>Rate</th>
                          <th>Amount</th>
                          <th>Action <input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1" value="1"></th>
                        </tr>
                      </thead>

                      <tbody>
                        <tr  class="participantRow">
                          <td hidden><input type="hidden" id="0" > </td>
                          <td><input  type="text" name="txtPRO_popup_0" id="txtPRO_popup_0" class="form-control"  autocomplete="off"  readonly style="width:100px;" /></td>
                          <td  hidden><input type="text" name="PROID_REF_0" id="PROID_REF_0" class="form-control" autocomplete="off" /></td>
                          
                          <td  hidden><input type="text" name="SOID_REF_0" id="SOID_REF_0"class="form-control" autocomplete="off" /></td>
                          <td  hidden ><input type="text" name="SQID_REF_0" id="SQID_REF_0" class="form-control" autocomplete="off" /></td>
                          <td  hidden ><input type="text" name="SEID_REF_0" id="SEID_REF_0" class="form-control" autocomplete="off" /></td>
                        
                          <td><input  type="text" name="popupITEMID_0" id="popupITEMID_0" class="form-control"  autocomplete="off"  readonly style="width:100px;" /></td>
                          <td   hidden><input type="text" name="ITEMID_REF_0" id="ITEMID_REF_0" class="form-control" autocomplete="off" /></td>
                        
                          <td><input type="text" name="ItemName_0" id="ItemName_0" class="form-control"  autocomplete="off"  readonly style="width:200px;" /></td>
                    
                          <td><input type="text" name="popupMUOM_0" id="popupMUOM_0" class="form-control"  autocomplete="off"  readonly style="width:100px;"/></td>
                          <td   hidden><input type="text" name="MAIN_UOMID_REF_0" id="MAIN_UOMID_REF_0" class="form-control"  autocomplete="off" /></td>
                    
                          <td><input type="text"   name="QTY_0" id="QTY_0" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" readonly  style="width:100px;"  /></td>
                          <td hidden><input type="text"   name="BL_SOQTY_0" id="BL_SOQTY_0" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" readonly  style="width:100px;"  /></td>
                          <td><input type="text"   name="PD_OR_QTY_0" id="PD_OR_QTY_0" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" style="width:100px;" onKeyup="rate_amount(this.id),get_materital_item()"  /></td>
                        
                          <td><input type="date"   name="EDA_0" id="EDA_0" class="form-control"   autocomplete="off"   /></td>
                          <td><input type="text"   name="RATEPUOM_0" id="RATEPUOM_0" class="form-control three-digits" value="0.00" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" style="width:100px;" onKeyup="rate_amount(this.id)"  /></td>
                          <td><input type="text"   name="TOT_AMT_0" id="TOT_AMT_0" class="form-control three-digits"   value="0.00" readonly maxlength="13"  autocomplete="off" style="width:100px;" onKeyup="rate_amount(this.id)"  /></td>
                        
                          <td hidden><input type="text"   name="MAINTROWID_0" id="MAINTROWID_0" class="form-control " style="width:100px;"  /></td>
                          
                          <td align="center" >
                            <button class="btn add material" title="add" data-toggle="tooltip" type="button" ><i class="fa fa-plus"></i></button>
                            <button class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button>
                          </td>

                        </tr>
                      </tbody>
                    </table>

                    <div id="material_item"></div>

                  </div>	
                </div>
                                
                                
                                
                                <div id="TC" class="tab-pane fade">
                                    
                                    <div class="row" style="margin-top:10px;margin-left:3px;" >	
                                        <div class="col-lg-1 pl"><p>T&C Template</p></div>
                                        <div class="col-lg-2 pl">
                                        <input type="text" name="txtTNCID_popup" id="txtTNCID_popup" class="form-control"  autocomplete="off"  readonly/>
                                         <input type="hidden" name="TNCID_REF" id="TNCID_REF" class="form-control" autocomplete="off" />
                                        </div>
                                    </div>
                                    
                                    
                                    <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="margin-top:10px;height:240px;width:50%;">
                                        
                                        <table id="example3" class="display nowrap table table-striped table-bordered itemlist" style="height:auto !important;">
                                            <thead id="thead1"  style="position: sticky;top: 0">
                                            <tr >
                                                <th>Terms & Conditions Description<input class="form-control" type="hidden" name="Row_Count2" id ="Row_Count2"></th>
                                                <th>Value / Comment</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody id="tncbody">
                                    
                                            <tr  class="participantRow3">
                                             <td><input type="text" name="popupTNCDID_0" id="popupTNCDID_0" class="form-control"  autocomplete="off"  readonly/></td>
                                             <td hidden><input type="hidden" name="TNCDID_REF_0" id="TNCDID_REF_0" class="form-control" autocomplete="off" /></td>
                                             <td hidden><input type="hidden" name="TNCismandatory_0" id="TNCismandatory_0" class="form-control" autocomplete="off" /></td>
                                             <td id="tdinputid_0">
                                              {{-- dynamic input --}} 
                                             </td>
                                                <td align="center" ><button class="btn add TNC" title="add" data-toggle="tooltip"  type="button" disabled><i class="fa fa-plus"></i></button><button class="btn remove DTNC" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
                                            </tr>
                                        <tr></tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                    

                                <div id="udf" class="tab-pane fade">
                                    <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="margin-top:10px;height:280px;width:50%;">
                                        <table id="example4" class="display nowrap table table-striped table-bordered itemlist" style="height:auto !important;">
                                            <thead id="thead1"  style="position: sticky;top: 0">
                                            <tr >
                                                <th>UDF Fields<input class="form-control" type="hidden" name="Row_Count3" id ="Row_Count3" value="{{ $objCountUDF }}"></th>
                                                <th>Value / Comments</th>
                                            </tr>
                                            </thead>
                                            {{-- <tbody>
                                            @foreach($objUdfData as $uindex=>$uRow)
                                              <tr  class="participantRow4">
                                                  <td><input type="text" name={{"popupUDFSOID_".$uindex}} id={{"popupUDFSOID_".$uindex}} class="form-control" value="{{$uRow->LABEL}}" autocomplete="off"  readonly/></td>
                                                  <td hidden><input type="hidden" name={{"UDFSOID_REF_".$uindex}} id={{"UDFSOID_REF_".$uindex}} class="form-control" value="{{$uRow->UDFSSIID}}" autocomplete="off"   /></td>
                                                  <td hidden><input type="hidden" name={{"UDFismandatory_".$uindex}} id={{"UDFismandatory_".$uindex}} value="{{$uRow->ISMANDATORY}}" class="form-control"   autocomplete="off" /></td>
                                                  <td id={{"udfinputid_".$uindex}} >
                                                    
                                                  </td>                                                  
                                              </tr>
                                              <tr></tr>
                                            @endforeach  
                                            </tbody> --}}
                                            <tbody>
                                              @foreach($objUdf as $udfkey => $udfrow)
                                              <tr  class="participantRow4">
                                                <td>
                                                  <input name={{"udffie_popup_".$udfkey}} id={{"txtudffie_popup_".$udfkey}} value="{{$udfrow->LABEL}}" class="form-control @if ($udfrow->ISMANDATORY==1) mandatory @endif" autocomplete="off" maxlength="100" disabled/>
                                                </td>
                                
                                                <td hidden>
                                                  <input type="text" name='{{"udffie_".$udfkey}}' id='{{"hdnudffie_popup_".$udfkey}}' value="{{$udfrow->UDFJWOID}}" class="form-control" maxlength="100" />
                                                </td>
                                
                                                <td hidden>
                                                  <input type="text" name={{"udffieismandatory_".$udfkey}} id={{"udffieismandatory_".$udfkey}} class="form-control" maxlength="100" value="{{$udfrow->ISMANDATORY}}" />
                                                </td>
                                
                                                <td id="{{"tdinputid_".$udfkey}}">
                                                  @php
                                                    
                                                    $dynamicid = "udfvalue_".$udfkey;
                                                    $chkvaltype = strtolower($udfrow->VALUETYPE); 
                                
                                                  if($chkvaltype=='date'){
                                
                                                    $strinp = '<input type="date" placeholder="dd/mm/yyyy" name="'.$dynamicid.'" id="'.$dynamicid.'" class="form-control" value="" /> ';       
                                
                                                  }else if($chkvaltype=='time'){
                                
                                                      $strinp= '<input type="time" placeholder="h:i" name="'.$dynamicid.'" id="'.$dynamicid.'" class="form-control"  value=""/> ';
                                
                                                  }else if($chkvaltype=='numeric'){
                                                  $strinp = '<input type="text" name="'.$dynamicid. '" id="'.$dynamicid.'" class="form-control" value=""  autocomplete="off" /> ';
                                
                                                  }else if($chkvaltype=='text'){
                                
                                                  $strinp = '<input type="text" name="'.$dynamicid. '" id="'.$dynamicid.'" class="form-control" value=""  autocomplete="off" /> ';
                                
                                                  }else if($chkvaltype=='boolean'){
                                                      $strinp = '<input type="checkbox" name="'.$dynamicid. '" id="'.$dynamicid.'" class=""  /> ';
                                
                                                  }else if($chkvaltype=='combobox'){
                                                    $strinp='';
                                                  $txtoptscombo =   strtoupper($udfrow->DESCRIPTIONS); ;
                                                  $strarray =  explode(',',$txtoptscombo);
                                                  $opts = '';
                                                  $chked='';
                                                    for ($i = 0; $i < count($strarray); $i++) {
                                                       $opts = $opts.'<option value="'.$strarray[$i].'"  >'.$strarray[$i].'</option> ';
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
                                
                                <div id="CT" class="tab-pane fade">
                                  <div class="row" style="margin-top:10px;margin-left:3px;" >	
                                      <div class="col-lg-2 pl"><p>Calculation Template</p></div>
                                      <div class="col-lg-2 pl">
                                      <input type="text" name="txtCTID_popup" id="txtCTID_popup" class="form-control"  autocomplete="off"  readonly/>
                                       <input type="hidden" name="CTID_REF" id="CTID_REF" class="form-control" autocomplete="off" />
                                      </div>
                                  </div>
                                  <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:240px;" >
                                      <table id="example5" class="display nowrap table table-striped table-bordered itemlist " width="100%" style="height:auto !important;">
                                          <thead id="thead1"  style="position: sticky;top: 0">
                                              <tr>
                                                  <th>Calculation Component<input class="form-control" type="hidden" name="Row_Count4" id ="Row_Count4"></th>
                                                  <th>Rate</th>
                                                  <th>Value</th>
                                                  <th hidden>GST Applicable</th>
                                                  <th hidden>IGST Rate</th>
                                                  <th hidden>IGST Amount</th>
                                                  <th hidden>CGST Rate</th>
                                                  <th hidden>CGST Amount</th>
                                                  <th hidden>SGST Rate</th>
                                                  <th hidden>SGST Amount</th>
                                                  <th hidden>Total GST Amount</th>
                                                  <th hidden>As per Actual</th>
                                                  <th hidden width="8%">Action</th>
                                              </tr>
                                          </thead>
                                          <tbody id="tbody_ctid">
                                              <tr  class="participantRow5">
                                                  <td><input type="text" name="popupTID_0" id="popupTID_0" class="form-control"  autocomplete="off"  readonly/></td>
                                                  <td hidden><input type="hidden" name="TID_REF_0" id="TID_REF_0" class="form-control" autocomplete="off" /></td>
                                                  <td><input type="text" name="RATE_0" id="RATE_0" class="form-control four-digits" maxlength="8" autocomplete="off"  readonly/></td>
                                                  <td hidden><input type="hidden" name="BASIS_0" id="BASIS_0" class="form-control" autocomplete="off" /></td>
                                                  <td hidden><input type="hidden" name="SQNO_0" id="SQNO_0" class="form-control" autocomplete="off" /></td>
                                                  <td ><input type="text" name="VALUE_0" id="VALUE_0" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                  <td hidden style="text-align:center;" ><input type="checkbox" class="filter-none" name="calGST_0" id="calGST_0" value="" ></td>
                                                  <td hidden><input type="text" name="calIGST_0" id="calIGST_0" class="form-control four-digits" maxlength="8" autocomplete="off"  readonly/></td>
                                                  <td hidden><input type="text" name="AMTIGST_0" id="AMTIGST_0" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                  <td hidden><input type="text" name="calCGST_0" id="calCGST_0" class="form-control four-digits" maxlength="8" autocomplete="off"  readonly/></td>
                                                  <td hidden><input type="text" name="AMTCGST_0" id="AMTCGST_0" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                  <td hidden><input type="text" name="calSGST_0" id="calSGST_0" class="form-control four-digits" maxlength="8" autocomplete="off"  readonly/></td>
                                                  <td hidden><input type="text" name="AMTSGST_0" id="AMTSGST_0" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                  <td hidden><input type="text" name="TOTGSTAMT_0" id="TOTGSTAMT_0" class="form-control two-digits"  maxlength="15" autocomplete="off"  readonly/></td>
                                                  <td hidden style="text-align:center;"><input type="checkbox" class="filter-none" name="calACTUAL_0" id="calACTUAL_0" value=""   ></td>
                                                  <td align="center" hidden><button class="btn add" title="add" data-toggle="tooltip" type="button" disabled><i class="fa fa-plus"></i></button> <button class="btn remove" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
                                              </tr>
                                              <tr></tr>
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


<div id="PROpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='PRO_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>PRO No</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="PROTable" class="display nowrap table  table-striped table-bordered" >
    <thead>
          <tr id="none-select" class="searchalldata" hidden>
            
            <td> 
            <input type="hidden"  id="hdn_PROid"/>
            <input type="hidden" id="hdn_PROid2"/>
            <input type="hidden" id="hdn_PROid3"/>
            </td>
          </tr>

      <tr>
        <th class="ROW1">Select</th> 
        <th class="ROW2">PRO NO</th>
        <th class="ROW3">Date</th>
      </tr>
    </thead>
    <tbody>

      <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="PROcodesearch" class="form-control" onkeyup="PROCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="PROnamesearch" class="form-control" onkeyup="PRONameFunction()"></td>
      </tr>

    </tbody>
    </table>
      <table id="PROTable2" class="display nowrap table  table-striped table-bordered"  >
        <thead id="thead2">

        </thead>
        <tbody id="tbody_PRO">     
        
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<div id="MAINITEMpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='MAINITEM_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>MAIN Item Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="MAINITEMTable" class="display nowrap table  table-striped table-bordered" >
    <thead>
          <tr id="none-select" class="searchalldata" hidden>
            
            <td> 
            <input type="text"  id="hdn_MAINITEMid1"/>
            <input type="text"  id="hdn_MAINITEMid2"/>
            <input type="text"  id="hdn_MAINITEMid3"/>
            <input type="text"  id="hdn_MAINITEMid4"/>
            <input type="text"  id="hdn_MAINITEMid5"/>
            <input type="text"  id="hdn_MAINITEMid6"/>
            <input type="text"  id="hdn_MAINITEMid7"/>
            </td>
          </tr>

    <tr>
      <th class="ROW1">Select</th> 
      <th class="ROW2">Code</th>
      <th class="ROW3">Name</th>
    </tr>

    </thead>
    <tbody>
    <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="MAINITEMcodesearch" class="form-control" onkeyup="MAINITEMCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="MAINITEMnamesearch" class="form-control" onkeyup="MAINITEMNameFunction()"></td>
      </tr>
    </tbody>
    </table>
      <table id="MAINITEMTable2" class="display nowrap table  table-striped table-bordered"  >
        <thead id="thead2">

        </thead>
        <tbody id="tbody_MAINITEM">     
        
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<div id="SUBITEMpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='SUBITEM_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Sub Item Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="SUBITEMTable" class="display nowrap table  table-striped table-bordered" >
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
      <table id="SUBITEMTable2" class="display nowrap table  table-striped table-bordered"  >
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
                  <input type="text" name="fieldid18" id="hdn_ItemID18"/>
                  <input type="text" name="fieldid19" id="hdn_ItemID19"/>
                  <input type="text" name="fieldid20" id="hdn_ItemID20"/>
                  <input type="text" name="hdn_ItemID21" id="hdn_ItemID21" value="0"/>
                  <input type="text" name="fieldid22" id="hdn_ItemID22"/>
                  <input type="text" name="fieldid23" id="hdn_ItemID23"/>
                  <input type="text" name="fieldid24" id="hdn_ItemID24"/>
                  <input type="text" name="fieldid25" id="hdn_ItemID25"/>
                </td>
              </tr>

              <tr>
                <th style="width:8%;" id="all-check">Select</th>
                <th style="width:10%;">Item Code</th>
                <th style="width:10%;">Name</th>
                <th style="width:8%;">Main UOM</th>
                <th style="width:8%;">Qty</th>
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
                <td style="width:8%;text-align:center;"><input type="checkbox" class="js-selectall" data-target=".js-selectall1" /></td>
                <td style="width:10%;"><input type="text" id="Itemcodesearch" class="form-control" onkeyup="ItemCodeFunction()"></td>
                <td style="width:10%;"><input type="text" id="Itemnamesearch" class="form-control" onkeyup="ItemNameFunction()"></td>
                <td style="width:8%;"><input type="text" id="ItemUOMsearch" class="form-control" onkeyup="ItemUOMFunction()"></td>
                <td style="width:8%;"><input type="text" id="ItemQTYsearch" class="form-control" onkeyup="ItemQTYFunction()"></td>
                <td style="width:8%;"><input type="text" id="ItemGroupsearch" class="form-control" onkeyup="ItemGroupFunction()"></td>
                <td style="width:8%;"><input type="text" id="ItemCategorysearch" class="form-control" onkeyup="ItemCategoryFunction()"></td>
                
                <td style="width:8%;"><input type="text" id="ItemBUsearch" class="form-control" onkeyup="ItemBUFunction()"></td>
                <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemAPNsearch" class="form-control" onkeyup="ItemAPNFunction()"></td>
                <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemCPNsearch" class="form-control" onkeyup="ItemCPNFunction()"></td>
                <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemOEMPNsearch" class="form-control" onkeyup="ItemOEMPNFunction()"></td>
                
                <td style="width:8%;"><input  type="text" id="ItemStatussearch" class="form-control" onkeyup="ItemStatusFunction()"></td>
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

<!-- TNC Header Dropdown -->
<div id="TNCIDpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='TNCID_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>GL Account</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="TNCIDTable" class="display nowrap table  table-striped table-bordered" >
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
        <td class="ROW2"><input type="text" id="TNCcodesearch" class="form-control" onkeyup="TNCCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="TNCnamesearch" class="form-control" onkeyup="TNCNameFunction()"></td>
      </tr>

    </tbody>
    </table>
      <table id="TNCIDTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2">
         
        </thead>
        <tbody>
        @foreach ($objTNCHeader as $tncindex=>$tncRow)
        <tr >
        <td class="ROW1"> <input type="checkbox" name="SELECT_TNCID_REF[]" id="tncidcode_{{ $tncindex }}" class="clstncid" value="{{ $tncRow-> TNCID }}" ></td>
          <td class="ROW2">{{ $tncRow-> TNC_CODE }}
          <input type="hidden" id="txttncidcode_{{ $tncindex }}" data-desc="{{ $tncRow-> TNC_CODE }} - {{ $tncRow-> TNC_DESC }}"  
          value="{{ $tncRow-> TNCID }}"/></td><td class="ROW3">{{ $tncRow-> TNC_DESC }}</td>
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
<!-- TNC Header Dropdown-->

<!-- TNC Details Dropdown -->
<div id="tncdetpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='tncdet_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Terms & Condition Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="TNCDetTable" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
    <tr>
            <th>TNC Name</th>
            <th>Value Type</th>
    </tr>
    <tr hidden>
            <input type="hidden" name="fieldid" id="hdn_tncdet"/>
    </tr>
    </thead>
    <tbody>
    <tr>
    <td>
    <input type="text" id="tncdetcodesearch" onkeyup="TNCDetCodeFunction()">
    </td>
    <td>
    <input type="text" id="tncdetnamesearch" onkeyup="TNCDetNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="TNCDetTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          
        </thead>
        <tbody id="tbody_tncdetails">       
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- TNC Details Dropdown-->

<!-- Calculation Header Dropdown -->
<div id="CTIDpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='CTID_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Calculation Template</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="CTIDTable" class="display nowrap table  table-striped table-bordered" >
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
        <td class="ROW2"><input type="text" id="CTIDcodesearch" class="form-control" onkeyup="CTIDCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="CTIDnamesearch" class="form-control" onkeyup="CTIDNameFunction()"></td>
      </tr>
    </tbody>
    </table>
      <table id="CTIDTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2">
         
        </thead>
        <tbody>
        @foreach ($objCalculationHeader as $calindex=>$calRow)
        <tr >
        <td class="ROW1"> <input type="checkbox" name="SELECT_CTID_REF[]" id="CTIDcode_{{ $calindex }}" class="clsctid" value="{{ $calRow-> CTID }}" ></td>
          <td class="ROW2">{{ $calRow-> CTCODE }}
          <input type="hidden" id="txtCTIDcode_{{ $calindex }}" data-desc="{{ $calRow-> CTCODE }} - {{ $calRow-> CTDESCRIPTION }}"  
          value="{{ $calRow-> CTID }}"/></td><td class="ROW3">{{ $calRow-> CTDESCRIPTION }}</td>
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
<!-- Calculation Header Dropdown-->

<!-- Calculation Details Dropdown -->
<div id="ctiddetpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='ctiddet_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Terms & Condition Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="CTIDDetTable" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
    <tr>
            <th>Component</th>
            <th>Basis</th>
            <th>Rate</th>
            <th>Amount</th>
            <th>Formula</th>
    </tr>
    <tr hidden>
            <input type="hidden" name="fieldid" id="hdn_ctiddet"/>
    </tr>
    </thead>
    <tbody>
    <tr>
    <td>
    <input type="text" id="CTIDdetcodesearch" onkeyup="CTIDDetCodeFunction()">
    </td>
    <td>
    <input type="text" id="CTIDdetnamesearch" onkeyup="CTIDDetNameFunction()">
    </td>
    <td>
    <input type="text" id="CTIDdetratesearch" onkeyup="CTIDDetRateFunction()">
    </td>
    <td>
    <input type="text" id="CTIDdetamountsearch" onkeyup="CTIDDetAmountFunction()">
    </td>
    <td>
    <input type="text" id="CTIDdetformulasearch" onkeyup="CTIDDetFormulaFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="CTIDDetTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          
        </thead>
        <tbody id="tbody_ctiddetails">       
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Calculation Details Dropdown-->

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
        <td class="ROW2"><input type="text" id="vendorcodesearch" class="form-control" onkeyup="VendorCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="vendornamesearch" class="form-control" onkeyup="VendorNameFunction()"></td>
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
<!-- Vendor Dropdown -->
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


/*================================== TNC HEADER ==================================*/

let tnctid = "#TNCIDTable2";
let tnctid2 = "#TNCIDTable";
let tncheaders = document.querySelectorAll(tnctid2 + " th");

tncheaders.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(tnctid, ".clstncid", "td:nth-child(" + (i + 1) + ")");
  });
});

function TNCCodeFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("TNCcodesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("TNCIDTable2");
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

function TNCNameFunction() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("TNCnamesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("TNCIDTable2");
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

$('#txtTNCID_popup').focus(function(event){
  showSelectedCheck($("#TNCID_REF").val(),"SELECT_TNCID_REF");
    $("#TNCIDpopup").show();
    event.preventDefault();
});

$("#TNCID_closePopup").click(function(event){
  $("#TNCIDpopup").hide();
});

$(".clstncid").click(function(){
  var fieldid = $(this).attr('id');
  var txtval =    $("#txt"+fieldid+"").val();
  var texdesc =   $("#txt"+fieldid+"").data("desc");
  
  $('#txtTNCID_popup').val(texdesc);
  $('#TNCID_REF').val(txtval);
  $("#TNCIDpopup").hide();
  $("#TNCcodesearch").val(''); 
  $("#TNCnamesearch").val(''); 
  TNCCodeFunction();
  
  var customid = txtval;
  if(customid!=''){
    
    $('#tbody_tncdetails').html('<tr><td colspan="2">Please wait..</td></tr>');
    

      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
      $.ajax({
          url:'{{route("transaction",[$FormId,"gettncdetails2"])}}',
          type:'POST',
          data:{'id':customid},
          success:function(data) {
              $('#tncbody').html(data);
              bindTNCDetailsEvents();
          },
          error:function(data){
            console.log("Error: Something went wrong.");
            $('#tncbody').html('');
          },
      });            
      $.ajax({
          url:'{{route("transaction",[$FormId,"gettncdetails3"])}}',
          type:'POST',
          data:{'id':customid},
          success:function(data) {
              $('#Row_Count2').val(data);
              bindTNCDetailsEvents();
          },
          error:function(data){
            console.log("Error: Something went wrong.");
            $('#Row_Count2').val('0');
          },
      });
      $.ajax({
          url:'{{route("transaction",[$FormId,"gettncdetails"])}}',
          type:'POST',
          data:{'id':customid},
          success:function(data) {
              $('#tbody_tncdetails').html(data);
              bindTNCDetailsEvents();
          },
          error:function(data){
            console.log("Error: Something went wrong.");
            $('#tbody_tncdetails').html('');
          },
      });        
  }
  event.preventDefault();
});


/*================================== TNC DETAILS ==================================*/

let tncdettid = "#TNCDetTable2";
let tncdettid2 = "#TNCDetTable";
let tncdetheaders = document.querySelectorAll(tncdettid2 + " th");


tncdetheaders.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(tncdettid, ".clstncdet", "td:nth-child(" + (i + 1) + ")");
  });
});


function TNCDetCodeFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("tncdetcodesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("TNCDetTable2");
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

function TNCDetNameFunction() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("tncdetnamesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("TNCDetTable2");
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

$("#tncdet_closePopup").on("click",function(event){ 
  $("#tncdetpopup").hide();
});

function bindTNCDetailsEvents(){
  $('.clstncdet').dblclick(function(){

      var id = $(this).attr('id');
      var txtid =    $("#txt"+id+"").val();
      var txtname =   $("#txt"+id+"").data("desc");
      var fieldid2 = $(this).find('[id*="tncvalue"]').attr('id');
      var txtvaluetype = $.trim($(this).find('[id*="tncvalue"]').text().trim());
      var txtismandatory =  $("#txt"+fieldid2+"").val();
      var txtdescription =  $("#txt"+fieldid2+"").data("desc");
      
      var txtcol = $('#hdn_tncdet').val();
      $("#"+txtcol).val(txtname);
      $("#"+txtcol).parent().parent().find("[id*='TNCDID_REF']").val(txtid);
      $("#"+txtcol).parent().parent().find("[id*='TNCismandatory']").val(txtismandatory);
      
      var txt_id4 = $("#"+txtcol).parent().parent().find("[id*='tdinputid']").attr('id');  //<td> id 

      var strdyn = txt_id4.split('_');
      var lastele =   strdyn[strdyn.length-1];

      var dynamicid = "tncdetvalue_"+lastele;

      var chkvaltype =  txtvaluetype.toLowerCase();
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
        if(txtdescription !== undefined)
        {
          var strarray = txtdescription.split(',');
          
          var opts = '';

          for (var i = 0; i < strarray.length; i++) {
            opts = opts + '<option value="'+strarray[i]+'">'+strarray[i]+'</option> ';
          }

          strinp = '<select name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" required>'+opts+'</select>' ;
        }
      }

      $('#'+txt_id4).html('');  
      $('#'+txt_id4).html(strinp);   

      $("#tncdetpopup").hide();
      $("#tncdetcodesearch").val(''); 
      $("#tncdetnamesearch").val(''); 
      TNCDetCodeFunction();
      event.preventDefault();
      
  });
}

/*================================== CALCULATION HEADER ==================================*/

let cttid = "#CTIDTable2";
let cttid2 = "#CTIDTable";
let ctheaders = document.querySelectorAll(cttid2 + " th");

ctheaders.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(cttid, ".clsctid", "td:nth-child(" + (i + 1) + ")");
  });
});

function CTIDCodeFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("CTIDcodesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("CTIDTable2");
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

function CTIDNameFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("CTIDnamesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("CTIDTable2");
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

      
$('#CT').on('click',"[id='txtCTID_popup']",function(){
  showSelectedCheck($("#CTID_REF").val(),"SELECT_CTID_REF");
    $("#CTIDpopup").show();
    event.preventDefault();
  });

$("#CTID_closePopup").click(function(event){
  $("#CTIDpopup").hide();
});

$(".clsctid").click(function(){
  var fieldid = $(this).attr('id');
  var txtval =    $("#txt"+fieldid+"").val();
  var texdesc =   $("#txt"+fieldid+"").data("desc");
  
  
  $('#txtCTID_popup').val(texdesc);
  $('#CTID_REF').val(txtval);
  $("#CTIDpopup").hide();
  $("#CTIDcodesearch").val(''); 
  $("#CTIDnamesearch").val(''); 
  CTIDCodeFunction();
  
  var customid = txtval;
  if(customid!=''){
    
    $('#tbody_ctiddetails').html('<tr><td colspan="2">Please wait..</td></tr>');
    $('#tbody_ctid').html('<tr><td colspan="2">Please wait..</td></tr>');
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
      $.ajax({
          url:'{{route("transaction",[$FormId,"getcalculationdetails2"])}}',
          type:'POST',
          data:{'id':customid},
          success:function(data) {
              $('#tbody_ctid').html(data);
              bindCTIDDetailsEvents();
              bindGSTCalTemplate();
          },
          error:function(data){
            console.log("Error: Something went wrong.");
            $('#tbody_ctid').html('');
          },
      });
      $.ajax({
          url:'{{route("transaction",[$FormId,"getcalculationdetails3"])}}',
          type:'POST',
          data:{'id':customid},
          success:function(data) {
            $('#Row_Count4').val(data);
              bindCTIDDetailsEvents();
          },
          error:function(data){
            console.log("Error: Something went wrong.");
            $('#Row_Count4').val('0');
          },
      });
      $.ajax({
          url:'{{route("transaction",[$FormId,"getcalculationdetails"])}}',
          type:'POST',
          data:{'id':customid},
          success:function(data) {
              $('#tbody_ctiddetails').html(data);
              bindCTIDDetailsEvents();
          },
          error:function(data){
            console.log("Error: Something went wrong.");
            $('#tbody_ctiddetails').html('');
          },
      }); 
        
  }
  event.preventDefault();
});

function bindGSTCalTemplate(){ 
          $('#CT').find('.participantRow5').each(function()
            { 
                var basis = $(this).find('[id*="BASIS"]').val();
                var sqno = $(this).find('[id*="SQNO"]').val();
                var formula = $(this).find('[id*="FORMULA"]').val();
                var rate = $(this).find('[id*="RATE"]').val();
                var amountnet = $(this).find('[id*="VALUE"]').val();
                var netTaxableAmount = 0.00;
                var netGSTAmount = 0.00;
                var netTotalAmount = 0.00;
                var totamount = 0.00;
                var tamt = 0.00;
                var IGSTamt = 0.00;
                var CGSTamt = 0.00;
                var SGSTamt = 0.00;
                var TotGSTamt = 0.00;

                $('#Material').find('.participantRow').each(function()
                {                       
                  var TaxableAmount = $(this).find('[id*="TOT_AMT"]').val();
                  if (!isNaN(TaxableAmount) && TaxableAmount.length !== 0) {
                    netTaxableAmount += parseFloat(TaxableAmount);
                    }                      
                  
                  var GSTAmount = $(this).find('[id*="TGST_AMT"]').val();
                  if (!isNaN(GSTAmount) && GSTAmount.length !== 0) {
                    netGSTAmount += parseFloat(GSTAmount);
                    }
                  
                  var TotalAmount = $(this).find('[id*="TOT_AMT"]').val();
                  if (!isNaN(TotalAmount) && TotalAmount.length !== 0) {
                    netTotalAmount += parseFloat(TotalAmount);
                    }
                })
                var IGST = $('#IGST_0').val();
                var CGST = $('#CGST_0').val();
                var SGST = $('#SGST_0').val();
                
                  if(formula == '')
                  {
                    if(rate > 0)
                    { 
                      if(basis == 'Item Taxable Amount')
                      {
                        totamount = parseFloat((rate * netTaxableAmount)/100).toFixed(2);
                      }
                      if(basis == 'Item GST Amount')
                      {
                        totamount = parseFloat((rate * netGSTAmount)/100).toFixed(2);
                      }
                      if(basis == 'Amount After GST Item')
                      {
                        totamount = parseFloat((rate * netTotalAmount)/100).toFixed(2);
                      }
                    }
                    else
                    {
                      totamount = amountnet;
                    }
                  }
                  else
                  {
                    if(basis == 'Item Taxable Amount')
                    {
                      var basis1 = '( '+netTaxableAmount+' * '+rate+' ) / 100';
                      var basis2 = netTaxableAmount;
                      var rate1 = rate +' ) / 100';
                      if(formula.indexOf("BASIS*RATE") != -1){
                        var formula1 = formula.replace ("BASIS*RATE", basis1);
                        tamt = eval(formula1);
                        totamount = parseFloat((tamt * rate)/100).toFixed(2);
                      }
                      else if(formula.indexOf("BASIS") != -1){
                        var formula1 = formula.replace ("BASIS", basis2);
                        tamt = eval(formula1);
                        totamount = parseFloat((tamt * rate)/100).toFixed(2);
                      }
                      else if(formula.indexOf("RATE") != -1){
                        var formula1 = formula.replace ("RATE", rate1);
                        tamt = eval(formula1);
                        totamount = parseFloat(( tamt * rate)/100).toFixed(2);
                      }
                    }
                    if(basis == 'Item GST Amount')
                    {
                      var basis1 = '('+netGSTAmount+'*'+rate+')/100';
                      var basis2 = netGSTAmount;
                      var rate1 = rate+')/100';
                      if(formula.indexOf("BASIS*RATE") != -1){
                        var formula1 = formula.replace ("BASIS*RATE", basis1);
                        tamt = eval(formula1);
                        totamount = parseFloat((tamt * rate)/100).toFixed(2);
                      }
                      else if(formula.indexOf("BASIS") != -1){
                        var formula1 = formula.replace ("BASIS", basis2);
                        tamt = eval(formula1);
                        totamount = parseFloat((tamt * rate)/100).toFixed(2);
                      }
                      else if(formula.indexOf("RATE") != -1){
                        var formula1 = formula.replace ("RATE", rate1);
                        tamt = eval(formula1);
                        totamount = parseFloat(( tamt * rate)/100).toFixed(2);
                      }
                    }
                    if(basis == 'Amount After GST Item')
                    {
                      var basis1 = '( '+netTotalAmount+' * '+rate+' ) / 100';
                      var basis2 = netTotalAmount;
                      var rate1 = rate+' ) / 100';
                      if(formula.indexOf("BASIS*RATE") != -1){
                        var formula1 = formula.replace ("BASIS*RATE", basis1);
                        tamt = eval(formula1);
                        totamount = parseFloat((tamt * rate)/100).toFixed(2);
                      }
                      else if(formula.indexOf("BASIS") != -1){
                        var formula1 = formula.replace ("BASIS", basis2);
                        tamt = eval(formula1);
                        totamount = parseFloat((tamt * rate)/100).toFixed(2);
                      }
                      else if(formula.indexOf("RATE") != -1){
                        var formula1 = formula.replace ("RATE", rate1);
                        tamt = eval(formula1);
                        totamount = parseFloat(( tamt * rate)/100).toFixed(2);
                      }
                    }
                    
                  }
                  $(this).find('[id*="VALUE_"]').val(totamount);
                    IGSTamt = parseFloat((IGST * totamount)/100).toFixed(2);
                    CGSTamt = parseFloat((CGST * totamount)/100).toFixed(2);
                    SGSTamt = parseFloat((SGST * totamount)/100).toFixed(2);
                    TotGSTamt = parseFloat(parseFloat(IGSTamt)+parseFloat(CGSTamt)+parseFloat(SGSTamt)).toFixed(2);
                if($(this).find('[id*="calGST"]').is(":checked") != false)
                {
                  if (IGST != '')
                  {
                  $(this).find('[id*="calIGST_"]').val(IGST);
                  $(this).find('[id*="AMTIGST_"]').val(IGSTamt);
                  $(this).find('[id*="calIGST_"]').removeAttr('readonly');
                  }
                  else
                  {
                    $(this).find('[id*="calIGST_"]').val('0');
                    $(this).find('[id*="AMTIGST_"]').val('0');
                    $(this).find('[id*="calIGST_"]').prop('readonly',true);
                    
                  }
                  if (CGST != '')
                  {
                  $(this).find('[id*="calCGST_"]').val(CGST);
                  $(this).find('[id*="AMTCGST_"]').val(CGSTamt);
                  $(this).find('[id*="calCGST_"]').removeAttr('readonly');
                  }
                  else
                  {
                    $(this).find('[id*="calCGST_"]').val('0');
                    $(this).find('[id*="AMTCGST_"]').val('0');
                    $(this).find('[id*="calCGST_"]').prop('readonly',true);
                  }
                  if (SGST != '')
                  {
                  $(this).find('[id*="calSGST_"]').val(SGST);
                  $(this).find('[id*="AMTSGST_"]').val(SGSTamt);
                  $(this).find('[id*="calSGST_"]').removeAttr('readonly');
                  }
                  else
                  {
                    $(this).find('[id*="calSGST_"]').val('0');
                    $(this).find('[id*="AMTSGST_"]').val('0');
                    $(this).find('[id*="calSGST_"]').prop('readonly',true);
                  }
                  $(this).find('[id*="TOTGSTAMT_"]').val(TotGSTamt);
                }
                else
                {
                  $(this).find('[id*="calSGST_"]').val('0');
                  $(this).find('[id*="AMTSGST_"]').val('0');
                  $(this).find('[id*="calCGST_"]').val('0');
                  $(this).find('[id*="AMTCGST_"]').val('0');
                  $(this).find('[id*="calIGST_"]').val('0');
                  $(this).find('[id*="AMTIGST_"]').val('0');
                  $(this).find('[id*="TOTGSTAMT_"]').val('0');
                  $(this).find('[id*="calIGST_"]').prop('readonly',true);
                  $(this).find('[id*="calCGST_"]').prop('readonly',true);
                  $(this).find('[id*="calSGST_"]').prop('readonly',true);
                }
            });
            var totalvalue = 0.00;
            var tvalue = 0.00;
            var ctvalue = 0.00;
            var ctgstvalue = 0.00;
            $('#Material').find('.participantRow').each(function()
            {
              tvalue = $(this).find('[id*="TOT_AMT"]').val();
              totalvalue = parseFloat(totalvalue) + parseFloat(tvalue);
              totalvalue = parseFloat(totalvalue).toFixed(2);
            });
            if($('#CTID_REF').val() != '')
            {
              $('#CT').find('.participantRow5').each(function()
              {
                ctvalue = $(this).find('[id*="VALUE"]').val();
                ctgstvalue = $(this).find('[id*="TOTGSTAMT"]').val();
                totalvalue = parseFloat(totalvalue) + parseFloat(ctvalue);
                totalvalue = parseFloat(totalvalue) + parseFloat(ctgstvalue);
                totalvalue = parseFloat(totalvalue).toFixed(2);
              });
            }
            $('#TotalValue').val(totalvalue);
            event.preventDefault();
        }

      

/*================================== CALCULATION DETAILS ==================================*/


let ctiddettid = "#CTIDDetTable2";
let ctiddettid2 = "#CTIDDetTable";
let ctiddetheaders = document.querySelectorAll(ctiddettid2 + " th");

ctiddetheaders.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(ctiddettid, ".clsctiddet", "td:nth-child(" + (i + 1) + ")");
  });
});

function CTIDDetCodeFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("CTIDdetcodesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("CTIDDetTable2");
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

function CTIDDetNameFunction() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("CTIDdetnamesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("CTIDDetTable2");
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

function CTIDDetRateFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("CTIDdetratesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("CTIDDetTable2");
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

function CTIDDetAmountFunction() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("CTIDdetamountsearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("CTIDDetTable2");
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

function CTIDDetFormulaFunction() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("CTIDdetformulasearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("CTIDDetTable2");
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

$("#ctiddet_closePopup").on("click",function(event){ 
  $("#ctiddetpopup").hide();
});

function bindCTIDDetailsEvents(){

  $('.clsctiddet').dblclick(function(){    
      var id = $(this).attr('id');
      var txtid =    $("#txt"+id+"").val();
      var txtname =   $("#txt"+id+"").data("desc");
      var fieldid2 = $(this).find('[id*="ctidbasis"]').attr('id');
      var txtbasis = $.trim($(this).find('[id*="ctidbasis"]').text().trim());
      var txtactual =  $("#txt"+fieldid2+"").val();
      var txtgst =  $("#txt"+fieldid2+"").data("desc");
      var fieldid3 = $(this).find('[id*="ctidformula_"]').attr('id');
      var txtrate = $.trim($(this).find('[id*="ctidformula_"]').text().trim());
      var txtRFQno =  $("#txt"+fieldid3+"").val();
      var txtformula =  $("#txt"+fieldid3+"").data("desc");
      var txtamount = $.trim($(this).find('[id*="ctidamount_"]').text().trim());
      var txtcol = $('#hdn_ctiddet').val();
      if(intRegex.test(txtrate)){
        txtrate = (txtrate +'.00');
      }
      $("#"+txtcol).val(txtname);
      $("#"+txtcol).parent().parent().find("[id*='TID_REF']").val(txtid);
      $("#"+txtcol).parent().parent().find("[id*='RATE']").val(txtrate);
      $("#"+txtcol).parent().parent().find("[id*='BASIS']").val(txtbasis);
      
      $("#"+txtcol).parent().parent().find("[id*='FORMULA']").val(txtformula);
      $("#"+txtcol).parent().parent().find("[id*='SQNO']").val(txtRFQno); 

      if(txtactual == 1)
      {
        $("#"+txtcol).parent().parent().find("[id*='ACTUAL']").prop('checked','true');
      }     
      else
      {
        $("#"+txtcol).parent().parent().find("[id*='ACTUAL']").removeAttr('checked');
      }  

      if(txtgst == 1)
      {
        $("#"+txtcol).parent().parent().find("[id*='calGST']").prop('checked','true');
        $("#"+txtcol).parent().parent().find("[id*='calIGST']").removeAttr('readonly');
        $("#"+txtcol).parent().parent().find("[id*='AMTIGST']").removeAttr('readonly');
        $("#"+txtcol).parent().parent().find("[id*='calCGST']").removeAttr('readonly');
        $("#"+txtcol).parent().parent().find("[id*='calSGST']").removeAttr('readonly');
        $("#"+txtcol).parent().parent().find("[id*='AMTCGST']").removeAttr('readonly');
        $("#"+txtcol).parent().parent().find("[id*='AMTSGST']").removeAttr('readonly');
      }     
      else
      {
        $("#"+txtcol).parent().parent().find("[id*='calGST']").removeAttr('checked');
      } 

      var totaltaxableamount = 0;
      $('#Material').find('.participantRow').each(function()
        {
          var amount1 = $(this).find('[id*="TOT_AMT"]').val();

          totaltaxableamount += parseFloat(amount1);
        });
      if(txtrate > 0)
      {
        txtamount = 0;
        txtamount = parseFloat((totaltaxableamount*txtrate)/100).toFixed(2);
        if(intRegex.test(txtamount)){
        txtamount = (txtamount +'.00');
        }
        $("#"+txtcol).parent().parent().find("[id*='VALUE']").val(txtamount);
      }
      else
      {
        if(intRegex.test(txtamount)){
        txtamount = (txtamount +'.00');
        }
        $("#"+txtcol).parent().parent().find("[id*='VALUE']").val(txtamount);
      }
      
      $("#ctiddetpopup").hide();
      $("#CTIDdetcodesearch").val(''); 
      $("#CTIDdetnamesearch").val(''); 
      $("#CTIDdetratesearch").val(''); 
      $("#CTIDdetamountsearch").val(''); 
      $("#CTIDdetformulasearch").val(''); 
      CTIDDetCodeFunction();
      event.preventDefault();
      
  });
}

/*================================== VENDOR POPUP FUNCTION =================================*/

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
    url:'{{route("transaction",[$FormId,"getVendor"])}}',
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


$('#txtvendor_popup').click(function(event){
  

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
    
    $('#txtvendor_popup').val(texdesc);
    $('#VID_REF').val(txtval);

    $("#vendoridpopup").hide();
    $("#vendorcodesearch").val(''); 
    $("#vendornamesearch").val(''); 
    VendorCodeFunction();
    event.preventDefault();
  });

}


/*================================== PRO POPUP FUNCTION =================================*/

let PROTable2 = "#PROTable2";
let PROTable = "#PROTable";
let PROheaders = document.querySelectorAll(PROTable + " th");

PROheaders.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(PROTable2, ".clssPROid", "td:nth-child(" + (i + 1) + ")");
  });
});

function PROCodeFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("PROcodesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("PROTable2");
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

function PRONameFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("PROnamesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("PROTable2");
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

$('#Material').on('click','[id*="txtPRO_popup"]',function(event){

  $('#hdn_PROid').val($(this).attr('id'));
  $('#hdn_PROid2').val($(this).parent().parent().find('[id*="PROID_REF"]').attr('id'));

  var fieldid = $(this).parent().parent().find('[id*="PROID_REF"]').attr('id');

  var VID_REF      =  $("#VID_REF").val();

  if(VID_REF ===""){
    $("#FocusId").val('txtvendor_popup');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select vendor.');
    $("#alert").modal('show')
    $("#OkBtn1").focus();
  }
  else{

    $("#PROpopup").show();
    $("#tbody_PRO").html('loading...');

    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })

    $.ajax({
        url:'{{route("transaction",[$FormId,"getProNo"])}}',
        type:'POST',
        data:{'id':VID_REF,'fieldid':fieldid},
        success:function(data) {
          $("#tbody_PRO").html(data);
          BindSO();
          showSelectedCheck($("#"+fieldid).val(),"SELECT_"+fieldid);
        },
        error:function(data){
          console.log("Error: Something went wrong.");
          $("#tbody_PRO").html('');
        },
    });

    $(this).parent().parent().find('[id*="popupITEMID"]').val('');
    $(this).parent().parent().find('[id*="ITEMID_REF"]').val('');
    $(this).parent().parent().find('[id*="ItemName"]').val('');
    $(this).parent().parent().find('[id*="popupMUOM"]').val('');
    $(this).parent().parent().find('[id*="MAIN_UOMID_REF"]').val('');
    $(this).parent().parent().find('[id*="QTY"]').val('');
    $(this).parent().parent().find('[id*="BL_SOQTY"]').val('');
    $(this).parent().parent().find('[id*="PD_OR_QTY"]').val('');
    $("#material_item").html('');

  }

});

$("#PRO_closePopup").click(function(event){
  $("#PROpopup").hide();
});

function BindSO(){
  $(".clssPROid").click(function(){
    var fieldid = $(this).attr('id');
    var txtval =    $("#txt"+fieldid+"").val();
    var texdesc =   $("#txt"+fieldid+"").data("desc");

    var txtid= $('#hdn_PROid').val();
    var txt_id2= $('#hdn_PROid2').val();

    $('#'+txtid).val(texdesc);
    $('#'+txt_id2).val(txtval);
    $("#PROpopup").hide();
    
    $("#PROcodesearch").val(''); 
    $("#PROnamesearch").val(''); 
    PROCodeFunction();
    event.preventDefault();
  });
}

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

$('#Material').on('click','[id*="popupITEMID"]',function(event){

  var VID_REF       = $("#VID_REF").val();
  var PROID_REF     = $(this).parent().parent().find('[id*="PROID_REF"]').val();
  var txtPRO_popup  = $(this).parent().parent().find('[id*="txtPRO_popup"]').attr('id');

  if(VID_REF ===""){
    $("#FocusId").val('VID_REF');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select vendor.');
    $("#alert").modal('show')
    $("#OkBtn1").focus();
  }
  else if(PROID_REF ===""){
    if ($('#DirectJWO').is(":checked") == true)
    {
      $('.js-selectall').prop('disabled', true);
      $("#tbody_ItemID").html('loading...');
      $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      $.ajax({
          url:'{{route("transaction",[$FormId,"getItemDetails"])}}',
          type:'POST',
          data:{'VID_REF':VID_REF,'PROID_REF':PROID_REF,'status':'A'},
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
        var id6   = $(this).parent().parent().find('[id*="QTY"]').attr('id');
        var id7   = $(this).parent().parent().find('[id*="BL_SOQTY"]').attr('id');
        var id8   = $(this).parent().parent().find('[id*="PD_OR_QTY"]').attr('id');
        var id9   = $(this).parent().parent().find('[id*="SQID_REF"]').attr('id');
        var id10  = $(this).parent().parent().find('[id*="SEID_REF"]').attr('id');
        var id11  = $(this).parent().parent().find('[id*="SOID_REF"]').attr('id');
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
    }
    else
    {
      $("#FocusId").val(txtPRO_popup);
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please select PRO No.');
      $("#alert").modal('show')
      $("#OkBtn1").focus();
    }
  }
  else
  {
    $('.js-selectall').prop('disabled', true);
    $("#tbody_ItemID").html('loading...');
    $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $.ajax({
        url:'{{route("transaction",[$FormId,"getItemDetails"])}}',
        type:'POST',
        data:{'VID_REF':VID_REF,'PROID_REF':PROID_REF,'status':'A'},
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
    var id6   = $(this).parent().parent().find('[id*="QTY"]').attr('id');
    var id7   = $(this).parent().parent().find('[id*="BL_SOQTY"]').attr('id');
    var id8   = $(this).parent().parent().find('[id*="PD_OR_QTY"]').attr('id');
    var id9   = $(this).parent().parent().find('[id*="SQID_REF"]').attr('id');
    var id10  = $(this).parent().parent().find('[id*="SEID_REF"]').attr('id');
    var id11  = $(this).parent().parent().find('[id*="SOID_REF"]').attr('id');


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

  }

});

$("#ITEMID_closePopup").click(function(event){
  $("#ITEMIDpopup").hide();
});

function bindItemEvents(){

  $('#ItemIDTable2').off(); 
 
  $('.js-selectall').change(function(){
	
      var isChecked = $(this).prop("checked");
      var selector = $(this).data('target');
      $(selector).prop("checked", isChecked);
          
      $('#ItemIDTable2').find('.clsitemid').each(function(){

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
        var item_proid          =   $("#txt"+fieldid+"").data("desc10");
        var item_soid           =   $("#txt"+fieldid+"").data("desc11");
        var item_soqty          =   $("#txt"+fieldid+"").data("desc12");

        if($(this).find('[id*="chkId"]').is(":checked") == true){

          $('#example2').find('.participantRow').each(function(){

            var PROID_REF   =   $(this).find('[id*="PROID_REF"]').val();
            var SOID_REF    =   $(this).find('[id*="SOID_REF"]').val();
            var ITEMID_REF  =   $(this).find('[id*="ITEMID_REF"]').val();
            var SQID_REF    =   $(this).find('[id*="SQID_REF"]').val();
            var SEID_REF    =   $(this).find('[id*="SEID_REF"]').val();

            var exist_val   =   PROID_REF+"_"+SOID_REF+"_"+SQID_REF+"_"+SEID_REF+"_"+ITEMID_REF;

            if(item_id){
              if(item_unique_row_id == exist_val){
                $("#ITEMIDpopup").hide();
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text('Item already exists.');
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
                item_soqty          =   '';
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
            $clone.find('[id*="ItemName"]').val(item_name);
            $clone.find('[id*="popupMUOM"]').val(item_main_uom_code);
            $clone.find('[id*="MAIN_UOMID_REF"]').val(item_main_uom_id);
            $clone.find('[id*="QTY"]').val(item_soqty);
            $clone.find('[id*="BL_SOQTY"]').val(item_qty);
            $clone.find('[id*="PD_OR_QTY"]').val(item_qty);
          
            $clone.find('[id*="MAINTROWID"]').val(item_unique_row_id); 
            $clone.find('[id*="SQID_REF"]').val(item_sqid);
            $clone.find('[id*="SEID_REF"]').val(item_seid);
            $clone.find('[id*="SOID_REF"]').val(item_soid);

            $tr.closest('table').append($clone);   
            var rowCount = $('#Row_Count1').val();
            rowCount    = parseInt(rowCount)+1;
            $('#Row_Count1').val(rowCount);
            $("#ITEMIDpopup").hide();
           
              get_materital_item();

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
          
            if($.trim(txt_id1)!=""){
              $('#'+txt_id1).val(item_code);
            }
            if($.trim(txt_id2)!=""){
              $('#'+txt_id2).val(item_id);
              $('#'+txt_id2).parent().parent().find('[id*="MAINTROWID"]').val(item_unique_row_id);
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
              $('#'+txt_id6).val(item_soqty);
            }
            if($.trim(txt_id7)!=""){
              $('#'+txt_id7).val(item_qty);
            }
            if($.trim(txt_id8)!=""){
              $('#'+txt_id8).val(item_qty);
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
            get_materital_item();
          }
                  
        
        }
        
      });
    
      $('.js-selectall').prop("checked", false);   
      $("#ITEMIDpopup").hide();
      
  });



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
    var item_proid          =   $("#txt"+fieldid+"").data("desc10");
    var item_soid           =   $("#txt"+fieldid+"").data("desc11");
    var item_soqty          =   $("#txt"+fieldid+"").data("desc12");

    if($(this).is(":checked") == true) {

      $('#example2').find('.participantRow').each(function(){

        var PROID_REF   =   $(this).find('[id*="PROID_REF"]').val();
        var SOID_REF    =   $(this).find('[id*="SOID_REF"]').val();
        var ITEMID_REF  =   $(this).find('[id*="ITEMID_REF"]').val();
        var SQID_REF    =   $(this).find('[id*="SQID_REF"]').val();
        var SEID_REF    =   $(this).find('[id*="SEID_REF"]').val();

        var exist_val   =   PROID_REF+"_"+SOID_REF+"_"+SQID_REF+"_"+SEID_REF+"_"+ITEMID_REF;

        if(item_id){
          if(item_unique_row_id == exist_val){
            $("#ITEMIDpopup").hide();
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text('Item already exists.');
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
            item_soqty          =   '';
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
        $clone.find('[id*="ItemName"]').val(item_name);
        $clone.find('[id*="popupMUOM"]').val(item_main_uom_code);
        $clone.find('[id*="MAIN_UOMID_REF"]').val(item_main_uom_id);
        $clone.find('[id*="QTY"]').val(item_soqty);
        $clone.find('[id*="BL_SOQTY"]').val(item_qty);
        $clone.find('[id*="PD_OR_QTY"]').val(item_qty);
       
        $clone.find('[id*="MAINTROWID"]').val(item_unique_row_id); 
        $clone.find('[id*="SQID_REF"]').val(item_sqid);
        $clone.find('[id*="SEID_REF"]').val(item_seid);
        $clone.find('[id*="SOID_REF"]').val(item_soid);

        $tr.closest('table').append($clone);   
        var rowCount = $('#Row_Count1').val();
        rowCount    = parseInt(rowCount)+1;
        $('#Row_Count1').val(rowCount);
        $("#ITEMIDpopup").hide();
        get_materital_item();
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
       
        if($.trim(txt_id1)!=""){
          $('#'+txt_id1).val(item_code);
        }
        if($.trim(txt_id2)!=""){
          $('#'+txt_id2).val(item_id);
          $('#'+txt_id2).parent().parent().find('[id*="MAINTROWID"]').val(item_unique_row_id);
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
          $('#'+txt_id6).val(item_soqty);
        }
        if($.trim(txt_id7)!=""){
          $('#'+txt_id7).val(item_qty);
        }
        if($.trim(txt_id8)!=""){
          $('#'+txt_id8).val(item_qty);
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
        get_materital_item();
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

} 


/*================================== ADD/REMOVE FUNCTION ==================================*/

$("#Material").on('click','.add', function() {
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
        $clone.find('[id*="ITEMID_REF"]').val('');
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
          var totalvalue = $('#TotalValue').val();
          totalvalue = parseFloat(totalvalue - $(this).closest('.participantRow').find('[id*="TOT_AMT_"]').val()).toFixed(2);
          $('#TotalValue').val(totalvalue);
            $(this).closest('.participantRow').remove();  
            var rowCount1 = $('#Row_Count1').val();
            rowCount1 = parseInt(rowCount1)-1;
            $('#Row_Count1').val(rowCount1);
            get_materital_item();
          doCalculation();
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
               
                doCalculation();
              return false;
              event.preventDefault();
        }
        event.preventDefault();
    });


    $("#TC").on('click', '.add', function() {
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
        $clone.find("[id*='tdinputid']").html('');
        $clone.find('[id*="TNCDID_REF"]').val('');
        $clone.find('[id*="TNCismandatory"]').val('');
        $tr.closest('table').append($clone);         
        var rowCount2 = $('#Row_Count2').val();
		    rowCount2 = parseInt(rowCount2)+1;
        $('#Row_Count2').val(rowCount2);
        
        
        event.preventDefault();
    });
    $("#TC").on('click', '.remove', function() {
        var rowCount2 = $(this).closest('table').find('.participantRow3').length;
        if (rowCount2 > 1) {
            $(this).closest('.participantRow3').remove();    
            var rowCount2 = $('#Row_Count2').val();
            rowCount2 = parseInt(rowCount2)-1;
            $('#Row_Count2').val(rowCount2); 
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

    

    $("#CT").on('click', '.add', function() {
        var $tr = $(this).closest('table');
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

        $clone.find('input:text').val('');
        $clone.find('[id*="calGST"]').removeAttr('checked');
        if($clone.find('[id*="calGST"]').is(":checked") == false)
        {
          $clone.find('[id*="calIGST"]').prop('disabled','true');
          $clone.find('[id*="calCGST"]').prop('disabled','true');
          $clone.find('[id*="calSGST"]').prop('disabled','true');
          $clone.find('[id*="AMTIGST"]').prop('disabled','true');
          $clone.find('[id*="AMTCGST"]').prop('disabled','true');
          $clone.find('[id*="AMTSGST"]').prop('disabled','true');
        }
        $clone.find('[id*="TID_REF"]').val('');
        $clone.find('[id*="BASIS"]').val('');
        $clone.find('[id*="SQNO"]').val('');
        $tr.closest('table').append($clone);         
        var rowCount4 = $('#Row_Count4').val();
		    rowCount4 = parseInt(rowCount4)+1;
        $('#Row_Count4').val(rowCount4);
        
        
        event.preventDefault();
    });
    $("#CT").on('click', '.remove', function() {
        var rowCount4 = $(this).closest('table').find('.participantRow5').length;
        if (rowCount4 > 1) {
          $(this).closest('.participantRow5').remove();     
          var rowCount4 = $('#Row_Count4').val();
          rowCount4 = parseInt(rowCount4)-1;
          $('#Row_Count4').val(rowCount4);
        } 
        if (rowCount4 <= 1) {          
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

    $("#material_item").on('click', '.suba', function() {
        var $tr = $(this).closest('table');
        var allTrs = $tr.find('.participantRow8').last();
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
        var rowCount5 = $('#Row_Count5').val();
		    rowCount5 = parseInt(rowCount5)+1;
        $('#Row_Count5').val(rowCount5);
        event.preventDefault();
    });
    $("#material_item").on('click', '.subr', function() {
        var rowCount5 = $(this).closest('table').find('.participantRow8').length;
        if (rowCount5 > 1) {
          $(this).closest('.participantRow8').remove();     
          var rowCount5 = $('#Row_Count5').val();
          rowCount5 = parseInt(rowCount5)-1;
          $('#Row_Count5').val(rowCount5);
        } 
        if (rowCount5 <= 1) {          
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

    $("#example6").on('click', '.add', function() {
        var $tr = $(this).closest('table');
        var allTrs = $tr.find('.participantRow6').last();
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
        $tr.closest('table').append($clone);         
        var rowCount5 = $('#Row_Count5').val();
		    rowCount5 = parseInt(rowCount5)+1;
        $('#Row_Count5').val(rowCount5);
        $clone.find('.remove').removeAttr('disabled'); 
        
        event.preventDefault();
    });
    $("#example6").on('click', '.remove', function() {
        var rowCount5 = $(this).closest('table').find('.participantRow6').length;
        if (rowCount5 > 1) {
          $(this).closest('.participantRow6').remove();     
          var rowCount5 = $('#Row_Count5').val();
          rowCount5 = parseInt(rowCount5)-1;
          $('#Row_Count5').val(rowCount5);
        } 
        if (rowCount5 <= 1) {          
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



/*================================== ONLOAD FUNCTION ==================================*/
$(document).ready(function(e) {
  var Material = $("#Material").html(); 
  $('#hdnmaterial').val(Material);
  var lastdt = <?php echo json_encode($objlastdt[0]->JWODT); ?>;
  var today = new Date(); 
  var prodate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;

  $('#JWODT').attr('min',lastdt);
  $('#JWODT').attr('max',prodate);


  

  var d = new Date(); 
  var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
  $('#JWODT').val(today);

});



$(document).ready(function(e) {

   var today        = new Date(); 
   var currentdate  = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
   //$('#JWODT').attr('min',currentdate);
   //$('#JWODT').val(currentdate);

   $('[id*="EDA_"]').attr('min',currentdate);
   $('[id*="EDA_"]').val(currentdate);

    
    var CT = $("#CT").html(); 
    $('#hdnct').val(CT);
    $("#Row_Count1").val(1);
    $("#Row_Count5").val(1);

    $('#DirectJWO').change(function()
    {
      if ($(this).is(":checked") == true)
      {   
          $('#TotalValue').val('0.00');          
          $('#Material').find('.participantRow').each(function()
          {
            $(this).find('input:text').val('');
            $(this).find('input:hidden').val('');
            var rowcount = $('#Row_Count1').val();
            if(rowcount > 1)
            {
              $(this).closest('.participantRow').remove();
              rowcount = parseInt(rowcount) - 1;
              $('#Row_Count1').val(rowcount);
            }
          });
          $('#Material').find('.participantRow8').each(function()
          {
            $(this).find('input:text').val('');
            $(this).find('input:hidden').val('');
            var rowcount3 = $('#Row_Count5').val();
            if(rowcount3 > 1)
            {
              $(this).closest('.participantRow8').remove();
              rowcount3 = parseInt(rowcount3) - 1;
              $('#Row_Count5').val(rowcount3);
            }
          });
          $('#material_item').html('');
          $('#CT').find('.participantRow5').each(function()
          {
            $(this).find('input:text').val('');
            $(this).find('input:hidden').val('');
            var rowcount2 = $('#Row_Count4').val();
            if(rowcount2 > 1)
            {
              $(this).closest('.participantRow5').remove();
              rowcount2 = parseInt(rowcount2) - 1;
              $('#Row_Count4').val(rowcount2);
            }
          });
          $('#Row_Count1').val('1');
          $('#Row_Count5').val('1');
          $('#Row_Count4').val('1');
          
          $('#Material').find('[id*="txtPRO_popup"]').prop('disabled','true');
          $('#hiddenDirect').val('1');
          event.preventDefault();
      }
      else
      {          
          $('#TotalValue').val('0.00');
          $('#Material').find('.participantRow').each(function()
          {
            $(this).find('input:text').val('');
            $(this).find('input:hidden').val('');
            var rowcount = $('#Row_Count1').val();
            if(rowcount > 1)
            {
              $(this).closest('.participantRow').remove();
              rowcount = parseInt(rowcount) - 1;
              $('#Row_Count1').val(rowcount);
            }
          });
          $('#Material').find('.participantRow8').each(function()
          {
            $(this).find('input:text').val('');
            $(this).find('input:hidden').val('');
            var rowcount3 = $('#Row_Count5').val();
            if(rowcount3 > 1)
            {
              $(this).closest('.participantRow8').remove();
              rowcount3 = parseInt(rowcount3) - 1;
              $('#Row_Count5').val(rowcount3);
            }
          });
          $('#CT').find('.participantRow5').each(function()
          {
            $(this).find('input:text').val('');
            $(this).find('input:hidden').val('');
            var rowcount2 = $('#Row_Count4').val();
            if(rowcount2 > 1)
            {
              $(this).closest('.participantRow5').remove();
              rowcount2 = parseInt(rowcount2) - 1;
              $('#Row_Count4').val(rowcount2);
            }
          });
          $('#Row_Count1').val('1');
          $('#Row_Count5').val('1');
          $('#Row_Count4').val('1');
         
          $('#Material').find('[id*="txtPRO_popup"]').removeAttr('disabled');
          $('#hiddenDirect').val('0');
          event.preventDefault();
      }
    });

    $('#CT').on('focusout',"[id*='calSGST_']",function()
    {
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.00')
      }
      bindTotalValue();
    });

    $('#CT').on('focusout',"[id*='calCGST_']",function()
    {
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.00')
      }
      bindTotalValue();
    });

    $('#CT').on('focusout',"[id*='calIGST_']",function()
    {
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.00')
      }
      bindTotalValue();
    });

    $('#btnAdd').on('click', function() {
        var viewURL = '{{route("transaction",[$FormId,"add"])}}';
                  window.location.href=viewURL;
    });
    $('#btnExit').on('click', function() {
      var viewURL = '{{route('home')}}';
                  window.location.href=viewURL;
    });
  
     $('#JWONO').focusout(function(){
      var JWONO   =   $.trim($(this).val());
      if(JWONO ===""){
               
               
                
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text('Please enter value in JWO No.');
                $("#alert").modal('show');
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk1');
               
            } 
        else{ 
        var trnsoForm = $("#add_trn_form");
        var formData = trnsoForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("transaction",[$FormId,"checkExist"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               if(data.exists) {
                    $(".text-danger").hide();
                    if(data.exists) {                   
                        console.log("cancel MSG="+data.msg);
                                      $("#YesBtn").hide();
                                      $("#NoBtn").hide();
                                      $("#OkBtn1").show();
                                      $("#AlertMessage").text(data.msg);
                                      $(".text-danger").hide();
                                      $("#JWONO").val('');
                                      $("#alert").modal('show');
                                      $("#OkBtn1").focus();
                                      highlighFocusBtn('activeOk1');
                    }                 
                }                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });
    }
});

/*
$('#JWODT').change(function( event ) {
    var today = new Date();     
    var d = new Date($(this).val()); 
    today.setHours(0, 0, 0, 0) ;
    d.setHours(0, 0, 0, 0) ;
    var sodate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
    if (d < today) {
        $(this).val(sodate);
        $("#alert").modal('show');
        $("#AlertMessage").text('JWO Date cannot be less than Current date');
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
*/


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
      
 
      window.location.href = "{{route('transaction',[$FormId,'add'])}}";

   }





   $("#SOFC").change(function() {
      if ($(this).is(":checked") == true){
          $(this).parent().parent().find('#txtCRID_popup').removeAttr('disabled');
          $(this).parent().parent().find('#txtCRID_popup').prop('readonly','true');
          event.preventDefault();
      }
      else
      {
          $(this).parent().parent().find('#txtCRID_popup').prop('disabled','true');
          $(this).parent().parent().find('#txtCRID_popup').removeAttr('readonly');
          $(this).parent().parent().find('#txtCRID_popup').val('');
          $(this).parent().parent().find('#CRID_REF').val('');
          $(this).parent().parent().find('#CONVFACT').val('');
          event.preventDefault();
      }
  });

  $("#CT").on('change',"[id*='calGST']",function() {
      if ($(this).is(":checked") == true){
        if($.trim($('#Tax_State').val()) == 'OutofState')
          {
            $(this).parent().parent().find('[id*="calIGST"]').removeAttr('disabled');
            $(this).parent().parent().find('[id*="calIGST"]').removeAttr('readonly');
            $(this).parent().parent().find('[id*="calCGST"]').prop('disabled','true');
            $(this).parent().parent().find('[id*="calCGST"]').val('0');
            $(this).parent().parent().find('[id*="calSGST"]').prop('disabled','true');
            $(this).parent().parent().find('[id*="calSGST"]').val('0');
            $(this).parent().parent().find('[id*="AMTIGST"]').removeAttr('disabled');
            $(this).parent().parent().find('[id*="AMTIGST"]').removeAttr('readonly');
            $(this).parent().parent().find('[id*="AMTCGST"]').prop('disabled','true');
            $(this).parent().parent().find('[id*="AMTSGST"]').prop('disabled','true');
            $(this).parent().parent().find('[id*="AMTCGST"]').val('0');
            $(this).parent().parent().find('[id*="AMTSGST"]').val('0');
            $(this).parent().parent().find('[id*="TOTGSTAMT"]').val('0');
            bindTotalValue();
            event.preventDefault();
          }
          else
          {
            $(this).parent().parent().find('[id*="calIGST"]').prop('disabled','true');
            $(this).parent().parent().find('[id*="calCGST"]').removeAttr('disabled');
            $(this).parent().parent().find('[id*="calSGST"]').removeAttr('disabled');
            $(this).parent().parent().find('[id*="calCGST"]').removeAttr('readonly');
            $(this).parent().parent().find('[id*="calSGST"]').removeAttr('readonly');
            $(this).parent().parent().find('[id*="AMTIGST"]').prop('disabled','true');
            $(this).parent().parent().find('[id*="AMTCGST"]').removeAttr('disabled');
            $(this).parent().parent().find('[id*="AMTSGST"]').removeAttr('disabled');
            $(this).parent().parent().find('[id*="AMTCGST"]').removeAttr('readonly');
            $(this).parent().parent().find('[id*="AMTSGST"]').removeAttr('readonly');
            $(this).parent().parent().find('[id*="calIGST"]').val('0');
            $(this).parent().parent().find('[id*="AMTIGST"]').val('0');
            $(this).parent().parent().find('[id*="TOTGSTAMT"]').val('0');
            bindTotalValue();
            event.preventDefault();
          }
      }
      else
      {
          $(this).parent().parent().find('[id*="calIGST"]').prop('disabled','true');
          $(this).parent().parent().find('[id*="calCGST"]').prop('disabled','true');
          $(this).parent().parent().find('[id*="calSGST"]').prop('disabled','true');
          $(this).parent().parent().find('[id*="AMTIGST"]').prop('disabled','true');
          $(this).parent().parent().find('[id*="AMTCGST"]').prop('disabled','true');
          $(this).parent().parent().find('[id*="AMTSGST"]').prop('disabled','true');
          $(this).parent().parent().find('[id*="calIGST"]').val('0');
          $(this).parent().parent().find('[id*="calCGST"]').val('0');
          $(this).parent().parent().find('[id*="calSGST"]').val('0');
          $(this).parent().parent().find('[id*="AMTIGST"]').val('0');
          $(this).parent().parent().find('[id*="AMTCGST"]').val('0');
          $(this).parent().parent().find('[id*="AMTSGST"]').val('0');
          $(this).parent().parent().find('[id*="TOTGSTAMT"]').val('0');
          bindTotalValue();
          event.preventDefault();
      }
  });
  $("#CT").on('change',"[id*='calIGST_']",function() {
      var rate = $(this).val();
      var total = $(this).parent().parent().find('[id*="VALUE_"]').val();
      var gstamt = parseFloat((rate*total)/100).toFixed(2);
      var totgst = $(this).parent().parent().find('[id*="TOTGSTAMT_"]').val();
      totgst = parseFloat(parseFloat(gstamt)).toFixed(2);;
      $(this).parent().parent().find('[id*="AMTIGST_"]').val(gstamt);
      $(this).parent().parent().find('[id*="TOTGSTAMT_"]').val(totgst);
      bindTotalValue();
      event.preventDefault();
  });
  $("#CT").on('change',"[id*='calCGST_']",function() {
      var rate2 = $(this).val();
      var total2 = $(this).parent().parent().find('[id*="VALUE_"]').val();
      var gstamt2 = parseFloat((rate2*total2)/100).toFixed(2);
      var totgst2 = $(this).parent().parent().find('[id*="TOTGSTAMT_"]').val();
      var sgstamt = $(this).parent().parent().find('[id*="AMTSGST_"]').val();
      totgst2 = parseFloat(parseFloat(sgstamt) + parseFloat(gstamt2)).toFixed(2);
      $(this).parent().parent().find('[id*="AMTCGST_"]').val(gstamt2);
      $(this).parent().parent().find('[id*="TOTGSTAMT_"]').val(totgst2);
      bindTotalValue();
      event.preventDefault();
  }); 
  $("#CT").on('change',"[id*='calSGST_']",function() {
      var rate3 = $(this).val();
      var total3 = $(this).parent().parent().find('[id*="VALUE_"]').val();
      var gstamt3 = parseFloat((rate3*total3)/100).toFixed(2);
      var totgst3 = $(this).parent().parent().find('[id*="TOTGSTAMT_"]').val();
      var cgstamt = $(this).parent().parent().find('[id*="AMTCGST_"]').val();
      totgst3 = parseFloat(parseFloat(cgstamt) + parseFloat(gstamt3)).toFixed(2);;
      $(this).parent().parent().find('[id*="AMTSGST_"]').val(gstamt3);
      $(this).parent().parent().find('[id*="TOTGSTAMT_"]').val(totgst3);
      bindTotalValue();
      event.preventDefault();
  }); 


});
</script>

@endpush

@push('bottom-scripts')
<script>

var formTrans = $("#add_trn_form");
formTrans.validate();

$( "#btnSaveFormData" ).click(function() {
 
  if(formTrans.valid()){
    validateForm();
  }
});



function validateForm(){
 
    $("#FocusId").val('');

    var JWONO         =   $.trim($("#JWONO").val());
    var JWODT         =   $.trim($("#JWODT").val());
    var VID_REF       =   $.trim($("#VID_REF").val());

    if(JWONO ===""){
        $("#FocusId").val('JWONO');        
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please enter value in JWO NO.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
    }
    else if(JWODT ===""){
        $("#FocusId").val('JWODT');        
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please select JWO Date.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
    }
    else if(VID_REF ===""){
        $("#FocusId").val('txtvendor_popup');    
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please select Vendor.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
    }      
    else{
        event.preventDefault();

        var RackArray = []; 
        var allblank = [];
        var allblank1 = [];
        var allblank2 = [];
        var allblank3 = [];
        var allblank4 = [];
        var allblank5 = [];
        var allblank6 = [];
        var allblank7 = [];
        var allblank66 = [];
        var allblank77 = [];
        var allblank78 = [];
        var allblank8 = [];
        var allblank9 = [];
        var allblank10 = [];
        var allblank11 = [];
        var allblank12 = [];
        var allblank13 = [];
        var allblank15 = [];
        var allblank16 = [];
        var allblank17 = [];

        var focustext   = "";

        $('#Material').find('.participantRow').each(function(){

          if($.trim($(this).find("[id*=PROID_REF]").val()) ===""){
            allblank1.push('false');
            focustext = $(this).find("[id*=txtPRO_popup]").attr('id');
          }
          else if($.trim($(this).find("[id*=ITEMID_REF]").val()) ===""){
            allblank3.push('false');
            focustext = $(this).find("[id*=popupITEMID]").attr('id');
          }
          else if($.trim($(this).find("[id*=MAIN_UOMID_REF]").val()) ===""){
            allblank4.push('false');
            focustext = $(this).find("[id*=popupMUOM]").attr('id');
          }
          else if($.trim($(this).find("[id*=QTY]").val()) ===""){
            allblank5.push('false');
            focustext = $(this).find("[id*=QTY]").attr('id');
          }
          else if($.trim($(this).find("[id*=PD_OR_QTY]").val()) ==="" || parseFloat($.trim($(this).find("[id*=PD_OR_QTY]").val())) <=0){
            allblank66.push('false');
            focustext = $(this).find("[id*=PD_OR_QTY]").attr('id');
          }
          else if(parseFloat($.trim($(this).find("[id*=PD_OR_QTY]").val())).toFixed(3) > parseFloat($.trim($(this).find("[id*=BL_SOQTY_]").val())).toFixed(3) ){
            allblank77.push('false');
            focustext = $(this).find("[id*=PD_OR_QTY]").attr('id');
          }
          else if($.trim($(this).find("[id*=EDA]").val()) ===""){
            allblank78.push('false');
            focustext = $(this).find("[id*=EDA]").attr('id');
          }
          else{
            allblank1.push('true');
            allblank2.push('true');
            allblank3.push('true');
            allblank4.push('true');
            allblank5.push('true');
            allblank66.push('true');
            allblank77.push('true');
            allblank78.push('true');
            focustext   = "";
          }

        });


           



            if($('#TNCID_REF').val() !=""){
                $('#TC').find('.participantRow3').each(function(){
                  if($.trim($(this).find("[id*=TNCDID_REF]").val())!="")
                    {
                        allblank6.push('true');
                            if($.trim($(this).find("[id*=TNCismandatory]").val())=="1"){
                                  if($.trim($(this).find('[id*="tncdetvalue"]').val()) != "")
                                  {
                                    allblank7.push('true');
                                  }
                                  else
                                  {
                                    allblank7.push('false');
                                  } 
                            } 
                    }
                    else
                    {
                        allblank6.push('false');
                    } 
                });
            }

            
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


            if($('#CTID_REF').val() !=""){
                $('#CT').find('.participantRow5').each(function(){
                  if($.trim($(this).find("[id*=TID_REF]").val())!="")
                    {
                        
                            if($(this).find("[id*=calGST]").is(":checked") == true)
                            {
                              if($.trim($('#Tax_State').val())!="WithinState")
                              {
                                if($.trim($(this).find("[id*=calIGST]").val())!="0")
                                {
                                  allblank11.push('true');
                                }
                                else
                                {
                                  allblank11.push('false');
                                }
                              }
                              else
                              {
                                if($.trim($(this).find("[id*=calCGST]").val())!="0")
                                {
                                  allblank11.push('true');
                                }
                                else
                                {
                                  allblank11.push('false');
                                }
                                if($.trim($(this).find("[id*=calSGST]").val())!="0")
                                {
                                  allblank11.push('true');
                                }
                                else
                                {
                                  allblank11.push('false');
                                }
                              }
                            } 
                    }
                    else
                    {
                        allblank10.push('false');
                    } 
                });
            }

          if(jQuery.inArray("false", allblank3) !== -1){
            $("#FocusId").val(focustext);
            $("#alert").modal('show');
            $("#AlertMessage").text('Please Select Item In Material');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk');
          }
          else if(jQuery.inArray("false", allblank4) !== -1){
            $("#FocusId").val(focustext);
            $("#alert").modal('show');
            $("#AlertMessage").text('Main UOM is missing in in material tab.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk');
          }
          else if(jQuery.inArray("false", allblank5) !== -1){
            $("#FocusId").val(focustext);
            $("#alert").modal('show');
            $("#AlertMessage").text('Qty cannot be blank in material tab.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk');
          }
          else if(jQuery.inArray("false", allblank66) !== -1){
            $("#FocusId").val(focustext);
            $("#alert").modal('show');
            $("#AlertMessage").text('Job Work Order Qty should be greater than 0 in material tab.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk');
          }
          else if(jQuery.inArray("false", allblank77) !== -1){
            $("#FocusId").val(focustext);
            $("#alert").modal('show');
            $("#AlertMessage").text('Job Work Order Qty cannot be greater then Produced Qty in material tab.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk');
          }
          else if(jQuery.inArray("false", allblank78) !== -1){
            $("#FocusId").val(focustext);
            $("#alert").modal('show');
            $("#AlertMessage").text('EDA Should not be empty in material tab.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk');
          }
          else if(jQuery.inArray("false", allblank6) !== -1){
              $("#FocusId").val(focustext);
              $("#alert").modal('show');
              $("#AlertMessage").text('Please select Terms & Condition Description in T&C Tab.');
              $("#YesBtn").hide(); 
              $("#NoBtn").hide();  
              $("#OkBtn1").show();
              $("#OkBtn1").focus();
              highlighFocusBtn('activeOk');
              return false;
          }
          else if(jQuery.inArray("false", allblank7) !== -1){
              $("#FocusId").val(focustext);
              $("#alert").modal('show');
              $("#AlertMessage").text('Please enter Value / Comment in T&C Tab.');
              $("#YesBtn").hide(); 
              $("#NoBtn").hide();  
              $("#OkBtn1").show();
              $("#OkBtn1").focus();
              highlighFocusBtn('activeOk');
              return false;
          }
          else if(jQuery.inArray("false", allblank9) !== -1){
              $("#FocusId").val(focustext);
              $("#alert").modal('show');
              $("#AlertMessage").text('Please enter  Value / Comment in UDF Tab.');
              $("#YesBtn").hide(); 
              $("#NoBtn").hide();  
              $("#OkBtn1").show();
              $("#OkBtn1").focus();
              highlighFocusBtn('activeOk');
              return false;
          }
          else if(jQuery.inArray("false", allblank10) !== -1){
              $("#FocusId").val(focustext);
              $("#alert").modal('show');
              $("#AlertMessage").text('Please select Calculation Component in Calculation Template Tab.');
              $("#YesBtn").hide(); 
              $("#NoBtn").hide();  
              $("#OkBtn1").show();
              $("#OkBtn1").focus();
              highlighFocusBtn('activeOk');
              return false;
          }
          else if(jQuery.inArray("false", allblank11) !== -1){
              $("#FocusId").val(focustext);
              $("#alert").modal('show');
              $("#AlertMessage").text('Please Enter GST Rate / Value in Calculation Template Tab.');
              $("#YesBtn").hide(); 
              $("#NoBtn").hide();  
              $("#OkBtn1").show();
              $("#OkBtn1").focus();
              highlighFocusBtn('activeOk');
              return false;
          }
          else if(checkPeriodClosing('{{$FormId}}',$("#JWODT").val(),0) ==0){
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
              $("#YesBtn").data("funcname","fnSaveData");
              $("#OkBtn1").hide();
              $("#OkBtn").hide();
              $("#YesBtn").show();
              $("#NoBtn").show();
              $("#YesBtn").focus();
              highlighFocusBtn('activeYes');
          }
        

    }

}

$("#YesBtn").click(function(){

$("#alert").modal('hide');
var customFnName = $("#YesBtn").data("funcname");
    window[customFnName]();

});

window.fnSaveData = function (){


event.preventDefault();

    var trnsoForm = $("#add_trn_form");
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
    url:'{{ route("transaction",[$FormId,"save"])}}',
    type:'POST',
    data:formData,
    success:function(data) {
      $(".buttonload").hide(); 
      $("#btnSaveFormData").show();   
      $("#btnApprove").prop("disabled", false);
       
        if(data.errors) {
            $(".text-danger").hide();

            if(data.errors.JWONO){
                showError('ERROR_JWONO',data.errors.JWONO);
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


$("#NoBtn").click(function(){
    $("#alert").modal('hide');
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
    var FocusId = $("#FocusId").val();

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

function doCalculation(){
  $(".blurRate").blur();
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

/*================================== SUB MATERIAL MAIN ITEM POPUP FUNCTION =================================*/

let MAINITEMTable2 = "#MAINITEMTable2";
let MAINITEMTable = "#MAINITEMTable";
let MAINITEMheaders = document.querySelectorAll(MAINITEMTable + " th");

MAINITEMheaders.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(MAINITEMTable2, ".clsMAINITEMid", "td:nth-child(" + (i + 1) + ")");
  });
});

function MAINITEMCodeFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("MAINITEMcodesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("MAINITEMTable2");
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

function MAINITEMNameFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("MAINITEMnamesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("MAINITEMTable2");
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

$('#material_item').on('click','[id*="txtMAINITEM_popup"]',function(event){

  $('#hdn_MAINITEMid1').val($(this).attr('id'));
  $('#hdn_MAINITEMid2').val($(this).parent().parent().find('[id*="REQ_SOITEMID_REF_"]').attr('id'));


  var fieldid = $(this).attr('id');
  var  item_array   = [];
  $('#example2').find('.participantRow').each(function(){
    var PROID_REF    = $(this).find('[id*="PROID_REF"]').val();
    var SOID_REF    = $(this).find('[id*="SOID_REF"]').val();
    var ITEMID_REF  = $(this).find('[id*="ITEMID_REF"]').val();
    var ITEMID_CODE = $(this).find('[id*="popupITEMID"]').val();
    var PD_OR_QTY   = $(this).find('[id*="PD_OR_QTY"]').val();
    var SQID_REF    = $(this).find('[id*="SQID_REF"]').val();
    var SEID_REF    = $(this).find('[id*="SEID_REF"]').val();
    var hiddenDirect = $('#hiddenDirect').val();

    item_array.push(PROID_REF+'_'+SOID_REF+'_'+ITEMID_REF+'_'+ITEMID_CODE+'_'+PD_OR_QTY+'_'+SQID_REF+'_'+SEID_REF+'_'+hiddenDirect);
  });


  $("#MAINITEMpopup").show();
  $("#tbody_MAINITEM").html('');

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  })

  $.ajax({
      url:'{{route("transaction",[$FormId,"get_main_item"])}}',
      type:'POST',
      data:{item_array:item_array},
      success:function(data) {
        $("#tbody_MAINITEM").html(data);
        BindMAINITEM();
        // showSelectedCheck($("#"+fieldid).val(),"SELECT_"+fieldid);
      },
      error:function(data){
        console.log("Error: Something went wrong.");
        $("#tbody_MAINITEM").html('');
      },
  });

});

$("#MAINITEM_closePopup").click(function(event){
  $("#MAINITEMpopup").hide();
});

function BindMAINITEM(){
  $(".clsMAINITEMid").click(function(){
    var fieldid   =   $(this).attr('id');
    var texdesc2   =   $("#txt"+fieldid+"").data("desc1");
    var id         =   $("#txt"+fieldid+"").val();

    var txt_id1= $('#hdn_MAINITEMid1').val();
    var txt_id2= $('#hdn_MAINITEMid2').val();

    $('#'+txt_id2).val(id);
    $('#'+txt_id1).val(texdesc2);

    $("#MAINITEMpopup").hide();
    $("#MAINITEMcodesearch").val(''); 
    $("#MAINITEMnamesearch").val(''); 
    MAINITEMCodeFunction();
    event.preventDefault();
  });
}


/*================================== SUB MATERIAL SUB ITEM POPUP FUNCTION =================================*/

let SUBITEMTable2 = "#SUBITEMTable2";
let SUBITEMTable = "#SUBITEMTable";
let SUBITEMheaders = document.querySelectorAll(SUBITEMTable + " th");

SUBITEMheaders.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(SUBITEMTable2, ".clsSUBITEMid", "td:nth-child(" + (i + 1) + ")");
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

$('#material_item').on('click','[id*="txtSUBITEM_popup"]',function(event){

  $('#hdn_SUBITEMid1').val($(this).attr('id'));
  $('#hdn_SUBITEMid2').val($(this).parent().parent().find('[id*="SUBITEM_NAME_"]').attr('id'));
  $('#hdn_SUBITEMid3').val($(this).parent().parent().find('[id*="REQ_ITEMID_REF_"]').attr('id'));
  $('#hdn_SUBITEMid4').val($(this).parent().parent().find('[id*="REQ_BOM_QTY_"]').attr('id'));
  $('#hdn_SUBITEMid5').val($(this).parent().parent().find('[id*="REQ_INPUT_PD_OR_QTY_"]').attr('id'));
  $('#hdn_SUBITEMid6').val($(this).parent().parent().find('[id*="REQ_CHANGES_PD_OR_QTY_"]').attr('id'));

  var ITEMID = $(this).parent().parent().find('[id*="REQ_SOITEMID_REF_"]').val();
  

  $("#SUBITEMpopup").show();
  $("#tbody_SUBITEM").html('');

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  })

  $.ajax({
      url:'{{route("transaction",[$FormId,"get_sub_item"])}}',
      type:'POST',
      data:{'ITEMID':ITEMID},
      success:function(data) {
        $("#tbody_SUBITEM").html(data);
        BindSUBITEM();
        // showSelectedCheck($("#"+fieldid).val(),"SELECT_"+fieldid);
      },
      error:function(data){
        console.log("Error: Something went wrong.");
        $("#tbody_SUBITEM").html('');
      },
  });

});

$("#SUBITEM_closePopup").click(function(event){
  $("#SUBITEMpopup").hide();
});

function BindSUBITEM(){
  $(".clsSUBITEMid").click(function(){
    var fieldid   =   $(this).attr('id');
    var texdesc    =   $("#txt"+fieldid+"").data("desc1");
    var texdesc2   =   $("#txt"+fieldid+"").data("desc2");
    var id         =   $("#txt"+fieldid+"").val();

    var txt_id1= $('#hdn_SUBITEMid1').val();
    var txt_id2= $('#hdn_SUBITEMid2').val();
    var txt_id3= $('#hdn_SUBITEMid3').val();
    var txt_id4= $('#hdn_SUBITEMid4').val();
    var txt_id5= $('#hdn_SUBITEMid5').val();
    var txt_id6= $('#hdn_SUBITEMid6').val();

    $('#'+txt_id3).val(id);
    $('#'+txt_id1).val(texdesc);
    $('#'+txt_id2).val(texdesc2);
    $('#'+txt_id4).val('1.000');
    $('#'+txt_id5).val('1.000');
    $('#'+txt_id6).val('1.000');

    $("#SUBITEMpopup").hide();
    $("#SUBITEMcodesearch").val(''); 
    $("#SUBITEMnamesearch").val(''); 
    SUBITEMCodeFunction();
    event.preventDefault();
  });
}






/*================================== MATERIAL ITEM FUNCTION ==================================*/

function get_materital_item(){

  var  item_array   = [];
  $('#example2').find('.participantRow').each(function(){
    var PROID_REF    = $(this).find('[id*="PROID_REF"]').val();
    var SOID_REF    = $(this).find('[id*="SOID_REF"]').val();
    var ITEMID_REF  = $(this).find('[id*="ITEMID_REF"]').val();
    var ITEMID_CODE = $(this).find('[id*="popupITEMID"]').val();
    var PD_OR_QTY   = $(this).find('[id*="PD_OR_QTY"]').val();
    var SQID_REF    = $(this).find('[id*="SQID_REF"]').val();
    var SEID_REF    = $(this).find('[id*="SEID_REF"]').val();
    var hiddenDirect = $('#hiddenDirect').val();

    item_array.push(PROID_REF+'_'+SOID_REF+'_'+ITEMID_REF+'_'+ITEMID_CODE+'_'+PD_OR_QTY+'_'+SQID_REF+'_'+SEID_REF+'_'+hiddenDirect);
  });

  $("#material_item").html('loading..');
  $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  $.ajax({
      url:'{{route("transaction",[$FormId,"get_materital_item"])}}',
      type:'POST',
      data:{
        item_array:item_array
        },
      success:function(data) {
        $("#material_item").html(data);                
      },
      error:function(data){
        console.log("Error: Something went wrong.");
        $("#material_item").html('');                        
      },
  }); 


}


/*================================== USER DEFINE FUNCTION ==================================*/

function isNumberDecimalKey(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
    return false;

    return true;
}

function change_production_order(id,value){

  var field_id  =   id.split("_")[3];

  var PD_OR_QTY =   parseFloat(value).toFixed(3);


  var qty_val = $("#"+id).parent().parent().find('[id*="BL_SOQTY_"]').val();

  if(isNaN(value) || $.trim(value)==""){
    value = 0;
  }

  var mainitem_id  = $("#"+id+"").parent().parent().find('[id*="MAINTROWID"]').val();
 
  if(parseFloat(PD_OR_QTY) > parseFloat(qty_val)){

     
      $("#"+id).val('0.000');
      $('#example4').find('.participantRow4').each(function(){
        var unirowid_val = $(this).children().find('[id*="main_item_rowid"]').val();
        
        if(mainitem_id==unirowid_val){
          var new_po_qty = 0;
          var REQ_BOM_QTY = $.trim( $(this).children().find('[id*="REQ_BOM_QTY_"]').val() );
          if(isNaN(REQ_BOM_QTY) || REQ_BOM_QTY==""){
            REQ_BOM_QTY=0;
          }
          var newval = $("#"+id).val();
          new_po_qty =parseFloat( parseFloat(newval) * parseFloat(REQ_BOM_QTY) ).toFixed(3);       
          $(this).children().find('[id*="REQ_INPUT_PD_OR_QTY_"]').val(new_po_qty);
          $(this).children().find('[id*="REQ_CHANGES_PD_OR_QTY_"]').val(new_po_qty);
          $(this).children().find('[id*="MAIN_PD_OR_QTY_"]').val(newval);
          console.log("m==",mainitem_id,unirowid_val,"value=",newval);
        }
       

    });

    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Production order qty should not greater then Balance SO qty.');
    $("#alert").modal('show')
    $("#OkBtn1").focus();    

  }
  else{

    $('#example4').find('.participantRow4').each(function(){

      var unirowid_val = $(this).children().find('[id*="main_item_rowid"]').val();

      console.log("m2="+mainitem_id,unirowid_val,'m2 val====',value);

      if(mainitem_id==unirowid_val){
       
        var new_po_qty = 0;
        var REQ_BOM_QTY = $.trim( $(this).children().find('[id*="REQ_BOM_QTY_"]').val() );
        if(isNaN(REQ_BOM_QTY) || REQ_BOM_QTY==""){
          REQ_BOM_QTY=0;

        }else{
          new_po_qty =parseFloat( parseFloat(value) * parseFloat(REQ_BOM_QTY) ).toFixed(3);            
        }

        $(this).children().find('[id*="REQ_INPUT_PD_OR_QTY_"]').val(new_po_qty);
        $(this).children().find('[id*="REQ_CHANGES_PD_OR_QTY_"]').val(new_po_qty);
        $(this).children().find('[id*="MAIN_PD_OR_QTY_"]').val(value);
        
      }

    });
    
  }

}


$('#Material').on('blur',"[id*='PD_OR_QTY']",function(){
    var qty2 = $.trim($(this).val());
    if(isNaN(qty2) || qty2=="" )
    {
      qty2 = 0;
    }  
    if(intRegex.test(qty2))
    {
      $(this).val((qty2 +'.000'));
    }

    event.preventDefault();
});

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

function rate_amount(fieldid){

  var arr         = fieldid.split('_');
  var textid      = arr.slice(-1)[0]

  var QTY         = parseFloat($.trim($("#PD_OR_QTY_"+textid).val()));
  var RATEPUOM    = parseFloat($.trim($("#RATEPUOM_"+textid).val()));
  var TOT_AMT     = parseFloat(QTY*RATEPUOM).toFixed(2);

  $("#TOT_AMT_"+textid).val(TOT_AMT);

  bindTotalValue();
 
}


function bindTotalValue(){

  var totalvalue  = 0.00;
  var tvalue      = 0.00;
  var ctvalue     = 0.00;
  var ctgstvalue  = 0.00;

  $('#Material').find('.participantRow').each(function(){
    tvalue = $(this).find('[id*="TOT_AMT"]').val();

    totalvalue = parseFloat(totalvalue) + parseFloat(tvalue);
    totalvalue = parseFloat(totalvalue).toFixed(2);
  });

  if($('#CTID_REF').val() != '')
  {
    $('#CT').find('.participantRow5').each(function()
    {
      ctvalue = $(this).find('[id*="VALUE"]').val();
      ctgstvalue = $(this).find('[id*="TOTGSTAMT"]').val();
      totalvalue = parseFloat(totalvalue) + parseFloat(ctvalue);
      totalvalue = parseFloat(totalvalue) + parseFloat(ctgstvalue);
      totalvalue = parseFloat(totalvalue).toFixed(2);
    });
  }
  $('#TotalValue').val(totalvalue);
}
</script>


@endpush