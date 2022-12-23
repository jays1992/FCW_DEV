
@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
  <div class="row">
    <div class="col-lg-2"><a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Job Work Challan</a></div>
    
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
            <div class="col-lg-2 pl"><p>JWC No</p></div>
            <div class="col-lg-2 pl">
            <input type="text" name="JWCNO" id="JWCNO" value="{{$docarray['DOC_NO']}}" {{$docarray['READONLY']}} class="form-control" maxlength="{{$docarray['MAXLENGTH']}}" autocomplete="off" style="text-transform:uppercase" >
            <script>docMissing(@json($docarray['FY_FLAG']));</script>
            
            </div>
            
            <div class="col-lg-2 pl"><p>JWC Date</p></div>
            <div class="col-lg-2 pl">
                <input type="date" name="JWCDT" id="JWCDT" onchange='checkPeriodClosing("{{$FormId}}",this.value,1),getDocNoByEvent("JWCNO",this,@json($doc_req))' value="{{ old('JWCDT') }}" class="form-control mandatory" autocomplete="off" placeholder="dd/mm/yyyy" >
            </div>
            
            <div class="col-lg-2 pl"><p>Vendor</p></div>
            <div class="col-lg-2 pl">
                <input type="text" name="Vendor_popup" id="txtvendor_popup" class="form-control mandatory"  autocomplete="off" readonly/>
                <input type="hidden" name="VID_REF" id="VID_REF" class="form-control" autocomplete="off" />
                <input type="hidden" name="hdnmaterial" id="hdnmaterial" class="form-control" autocomplete="off" />                                                                
                                                                    
            </div>
        </div>

        <div class="row">

          <div class="col-lg-2 pl"><p>Bill To </p></div>
          <div class="col-lg-2 pl" id="div_billto">
              <input type="text" name="txtBILLTO1" id="txtBILLTO1" class="form-control"  autocomplete="off" readonly  />
              <input type="hidden" name="BILLTO1" id="BILLTO1" class="form-control" autocomplete="off" />
          </div>
          
          <div class="col-lg-2 pl"><p>Ship To</p></div>
          <div class="col-lg-2 pl" id="div_shipto">
              <input type="text" name="txtSHIPTO1" id="txtSHIPTO1" class="form-control"  autocomplete="off" readonly  />
              <input type="hidden" name="SHIPTO1" id="SHIPTO1" class="form-control" autocomplete="off" />
              <input type="hidden" name="Tax_State1" id="Tax_State1" class="form-control" autocomplete="off" />
          </div>

          <div class="col-lg-2 pl"><p>Remarks</p></div>
          <div class="col-lg-2 pl">
              <input type="text" name="REMARKS" id="REMARKS" class="form-control"  autocomplete="off" />
          </div>

        </div>

        <div class="row">     
          <div class="col-lg-2 pl"><p>Total</p></div>
          <div class="col-lg-2 pl">
              <input type="text" name="TotalValue" id="TotalValue" class="form-control"  autocomplete="off" readonly />
          </div>
        </div>



                        
      </div>

      <div class="container-fluid purchase-order-view">

        <div class="row">
          <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#Material">Material</a></li>
            <li><a data-toggle="tab" href="#udf">UDF</a></li>
          </ul>
                            
                            
                            
          <div class="tab-content">

            <div id="Material" class="tab-pane fade in active">
                <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:400px;margin-top:10px;" >
                    <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                      <thead id="thead1"  style="position: sticky;top: 0">
                                                    
                        <tr>
                          <th>JWO No</th>
                          <th>Item Code </th>
                          <th>Item Name</th>
                          <th>UOM</th>
                          <th>Order Qty</th>
                          <th>Pending Qty</th>
                          <th>produce Qty</th>
                          <th>EDA</th>
                          <th>Action <input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1" value="1"></th>
                        </tr>
                      </thead>

                      <tbody>
                        <tr  class="participantRow">
                          <td hidden><input type="hidden" id="0" > </td>
                          <td><input type="text" name="txtJWOID_popup_0"   id="txtJWOID_popup_0" class="form-control" autocomplete="off" readonly style="width:100px;" /></td>
                          <td hidden><input type="text" name="JWOID_REF_0" id="JWOID_REF_0"      class="form-control" autocomplete="off" /></td>
                          
                          <td hidden><input type="text" name="PROID_REF_0" id="PROID_REF_0" class="form-control" autocomplete="off" /></td>
                          <td hidden><input type="text" name="SOID_REF_0"  id="SOID_REF_0"  class="form-control" autocomplete="off" /></td>
                          <td hidden><input type="text" name="SQID_REF_0"  id="SQID_REF_0"  class="form-control" autocomplete="off" /></td>
                          <td hidden><input type="text" name="SEID_REF_0"  id="SEID_REF_0"  class="form-control" autocomplete="off" /></td>
                        
                          <td><input  type="text" name="popupITEMID_0" id="popupITEMID_0" class="form-control"  autocomplete="off"  readonly style="width:100px;" /></td>
                          <td   hidden><input type="text" name="ITEMID_REF_0" id="ITEMID_REF_0" class="form-control" autocomplete="off" /></td>
                        
                          <td><input type="text" name="ItemName_0" id="ItemName_0" class="form-control"  autocomplete="off"  readonly style="width:100px;" /></td>
                    
                          <td><input type="text" name="popupMUOM_0" id="popupMUOM_0" class="form-control"  autocomplete="off"  readonly style="width:100px;"/></td>
                          <td   hidden><input type="text" name="MAIN_UOMID_REF_0" id="MAIN_UOMID_REF_0" class="form-control"  autocomplete="off" /></td>
                    
                          <td><input type="text"   name="QTY_0" id="QTY_0" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" readonly  style="width:100px;"  /></td>
                          <td><input type="text"   name="BL_SOQTY_0" id="BL_SOQTY_0" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" readonly  style="width:100px;"  /></td>
                          <td><input type="text"   name="PD_OR_QTY_0" id="PD_OR_QTY_0" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" style="width:100px;" readonly onKeyup="get_materital_item()"  /></td>
                        
                          <td><input type="date"   name="EDA_0" id="EDA_0" class="form-control"   autocomplete="off" style="width:120px;"   /></td>
                          <td hidden><input type="text"   name="RATEPUOM_0" id="RATEPUOM_0" class="form-control three-digits" value="0.00" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" style="width:100px;"   /></td>
                          <td hidden><input type="text"   name="TOT_AMT_0" id="TOT_AMT_0" class="form-control three-digits"   value="0.00" readonly maxlength="13"  autocomplete="off" style="width:100px;"  /></td>
                        
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
                                
                <div id="udf" class="tab-pane fade">
                    <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="margin-top:10px;height:280px;width:50%;">
                        <table id="example4" class="display nowrap table table-striped table-bordered itemlist" style="height:auto !important;">
                            <thead id="thead1"  style="position: sticky;top: 0">
                            <tr >
                                <th>UDF Fields<input class="form-control" type="hidden" name="Row_Count3" id ="Row_Count3" value="{{ $objCountUDF }}"></th>
                                <th>Value / Comments</th>
                            </tr>
                            </thead>
    
                            <tbody>
                              @foreach($objUdf as $udfkey => $udfrow)
                              <tr  class="participantRow4">
                                <td>
                                  <input name={{"udffie_popup_".$udfkey}} id={{"txtudffie_popup_".$udfkey}} value="{{$udfrow->LABEL}}" class="form-control @if ($udfrow->ISMANDATORY==1) mandatory @endif" autocomplete="off" maxlength="100" disabled/>
                                </td>
                
                                <td hidden>
                                  <input type="text" name='{{"udffie_".$udfkey}}' id='{{"hdnudffie_popup_".$udfkey}}' value="{{$udfrow->UDFJWCID}}" class="form-control" maxlength="100" />
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
          <table id="StoreTable" class="display nowrap table  table-striped table-bordered" style="width: 100%;font-size:14px;" >
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


<div id="JWOpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='JWO_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>JWO No</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="JWOTable" class="display nowrap table  table-striped table-bordered" >
    <thead>
          <tr id="none-select" class="searchalldata" hidden>
            
            <td> 
            <input type="hidden"  id="hdn_JWOID"/>
            <input type="hidden" id="hdn_JWOID2"/>
            <input type="hidden" id="hdn_JWOID3"/>
            </td>
          </tr>

      <tr>
        <th class="ROW1">Select</th> 
        <th class="ROW2">JWO NO</th>
        <th class="ROW3">Date</th>
      </tr>
    </thead>
    <tbody>

      <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="JWOcodesearch" class="form-control" onkeyup="JWOCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="JWOnamesearch" class="form-control" onkeyup="JWONameFunction()"></td>
      </tr>

    </tbody>
    </table>
      <table id="JWOTable2" class="display nowrap table  table-striped table-bordered"  >
        <thead id="thead2">

        </thead>
        <tbody id="tbody_JWO">     
        
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
                  <input type="text" name="fieldid12" id="hdn_ItemID12"/>
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
                <th style="width:8%;"  {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</th>
                <th style="width:8%;"  {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</th>
                <th style="width:8%;"  {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</th>
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

<!-- Bill To Dropdown -->
<div id="BillTopopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='BillToclosePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Bill To</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="BillToTable" class="display nowrap table  table-striped table-bordered" >
    <thead>
    <tr>
      <th class="ROW1">Select</th> 
      <th class="ROW2">Name</th>
      <th class="ROW3">Address</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="BillTocodesearch" class="form-control" onkeyup="BillToCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="BillTonamesearch" class="form-control" onkeyup="BillToNameFunction()"></td>
    </tr>
    </tbody>
    </table>
      <table id="BillToTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2">
         
        </thead>
        <tbody id="tbody_BillTo">
       
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Bill To Dropdown-->

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
      <table id="ShipToTable2" class="display nowrap table  table-striped table-bordered">
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

  $('.clsvendorid').click(function(){

    var id            =   $(this).attr('id');
    var txtval        =   $("#txt"+id+"").val();
    var texdesc       =   $("#txt"+id+"").data("desc");
    var oldVenID      =   $("#VID_REF").val();

    var MaterialClone = $('#hdnmaterial').val();
   
    $("#txtvendor_popup").val(texdesc);
    $("#txtvendor_popup").blur();
    $("#VID_REF").val(txtval);

    if (txtval != oldVenID){
        $('#Material').html(MaterialClone);
        $('#Row_Count1').val('1');
    }

    $("#vendoridpopup").hide();
    $("#vendorcodesearch").val(''); 
    $("#vendornamesearch").val('');

    VendorCodeFunction();

    var customid = txtval;
    if(customid!=''){
          
      $("#txtBILLTO").val('');
      $("#BILLTO").val('');
      $("#txtBILLTO1").val('');
      $("#BILLTO1").val('');

      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });

      $.ajax({
          url:'{{route("transaction",[$FormId,"getBillTo"])}}',
          type:'POST',
          data:{'id':customid},
          success:function(data) {
            $("#txtBILLTO1").hide();
            $("#div_billto").html(data);
          },
          error:function(data){
            console.log("Error: Something went wrong.");
            $("#txtBILLTO").hide();
            $("#txtBILLTO1").show();
          },
      });  

      $("#txtSHIPTO").val('');
      $("#SHIPTO").val('');
      $("#txtSHIPTO1").val('');
      $("#SHIPTO1").val('');

      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
      $.ajax({
          url:'{{route("transaction",[$FormId,"getShipTo"])}}',
          type:'POST',
          data:{'id':customid},
          success:function(data) {
            $("#txtSHIPTO1").hide();
            $("#div_shipto").html(data);
          },
          error:function(data){
            console.log("Error: Something went wrong.");
            $("#txtSHIPTO").hide();
            $("#txtSHIPTO1").show();
          },
      });  

      $("#tbody_BillTo").html('');
      $.ajax({
          url:'{{route("transaction",[$FormId,"getBillAddress"])}}',
          type:'POST',
          data:{'id':customid},
          success:function(data) {
            $("#tbody_BillTo").html(data);
            BindBillAddress();
          },
          error:function(data){
            console.log("Error: Something went wrong.");
            $("#tbody_BillTo").html('');
          },
      }); 

      $("#tbody_ShipTo").html('');
      $.ajax({
          url:'{{route("transaction",[$FormId,"getShipAddress"])}}',
          type:'POST',
          data:{'id':customid},
          success:function(data) {
            $("#tbody_ShipTo").html(data);       
            BindShipAddress();                 
          },
          error:function(data){
            console.log("Error: Something went wrong.");
            $("#tbody_ShipTo").html('');
          },
      });  

    }
    event.preventDefault();
  });
}


/*================================== BILL TO FUNCTION =================================*/

let billtoid = "#BillToTable2";
let billtoid2 = "#BillToTable";
let billtoheaders = document.querySelectorAll(billtoid2 + " th");
  
billtoheaders.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(billtoid, ".clsbillto", "td:nth-child(" + (i + 1) + ")");
  });
});

function BillToCodeFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("BillTocodesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("BillToTable2");
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

function BillToNameFunction() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("BillTonamesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("BillToTable2");
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

$('#div_billto').on('click','#txtBILLTO',function(event){
  showSelectedCheck($("#BILLTO").val(),"SELECT_BILLTO");
  $("#BillTopopup").show();
  event.preventDefault();
});

$("#BillToclosePopup").click(function(event){
  $("#BillTopopup").hide();
  event.preventDefault();
});

function BindBillAddress(){
  $(".clsbillto").click(function(){
    var fieldid = $(this).attr('id');
    var txtval =    $("#txt"+fieldid+"").val();
    var texdesc =   $("#txt"+fieldid+"").data("desc");
    $('#txtBILLTO').val(texdesc);
    $('#BILLTO').val(txtval);
    $("#BillTopopup").hide();
    $("#BillTocodesearch").val(''); 
    $("#BillTonamesearch").val(''); 
    BillToCodeFunction();        
    event.preventDefault();
  });
}

/*================================== SHIP TO FUNCTION =================================*/

let shiptoid = "#ShipToTable2";
let shiptoid2 = "#ShipToTable";
let shiptoheaders = document.querySelectorAll(shiptoid2 + " th");
  
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

$('#div_shipto').on('click','#txtSHIPTO',function(event){  
  showSelectedCheck($("#SHIPTO").val(),"SELECT_SHIPTO");
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
    var taxstate =  $("#txt"+fieldid+"").data("desc");
    var oldShipto =   $("#SHIPTO").val();
    var MaterialClone = $('#hdnmaterial').val();
    var CTClone = $('#hdnct').val();

    if (txtval != oldShipto)
    {
        $('#Material').html(MaterialClone);
        $('#CT').html(CTClone);

        $('#TotalValue').val('0.00');
        $('#Row_Count1').val('1');
        if ($('#DirectSO').is(":checked") == true){
              $('#Material').find('[id*="txtRFQ_popup"]').prop('disabled','true')
              event.preventDefault();
        }
        else
        {
            $('#Material').find('[id*="txtRFQ_popup"]').removeAttr('disabled');
            event.preventDefault();
        }
    }
    $('#txtSHIPTO').val(texdesc);
    $('#SHIPTO').val(txtval);
    $('#Tax_State').val(taxstate);
    $("#ShipTopopup").hide();
    $("#ShipTocodesearch").val(''); 
    $("#ShipTonamesearch").val(''); 
    ShipToCodeFunction();        
    event.preventDefault();
  });
}

/*================================== JWO POPUP FUNCTION =================================*/

let JWOTable2 = "#JWOTable2";
let JWOTable = "#JWOTable";
let JWOheaders = document.querySelectorAll(JWOTable + " th");

JWOheaders.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(JWOTable2, ".clssJWOID", "td:nth-child(" + (i + 1) + ")");
  });
});

function JWOCodeFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("JWOcodesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("JWOTable2");
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

function JWONameFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("JWOnamesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("JWOTable2");
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

$('#Material').on('click','[id*="txtJWOID_popup"]',function(event){

  $('#hdn_JWOID').val($(this).attr('id'));
  $('#hdn_JWOID2').val($(this).parent().parent().find('[id*="JWOID_REF"]').attr('id'));

  var fieldid = $(this).parent().parent().find('[id*="JWOID_REF"]').attr('id');

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

    $("#JWOpopup").show();
    $("#tbody_JWO").html('loading...');

    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })

    $.ajax({
        url:'{{route("transaction",[$FormId,"getDocNo"])}}',
        type:'POST',
        data:{'id':VID_REF,'fieldid':fieldid},
        success:function(data) {
          $("#tbody_JWO").html(data);
          BindSO();
          showSelectedCheck($("#"+fieldid).val(),"SELECT_"+fieldid);
        },
        error:function(data){
          console.log("Error: Something went wrong.");
          $("#tbody_JWO").html('');
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

$("#JWO_closePopup").click(function(event){
  $("#JWOpopup").hide();
});

function BindSO(){
  $(".clssJWOID").click(function(){
    var fieldid = $(this).attr('id');
    var txtval =    $("#txt"+fieldid+"").val();
    var texdesc =   $("#txt"+fieldid+"").data("desc");

    var txtid= $('#hdn_JWOID').val();
    var txt_id2= $('#hdn_JWOID2').val();

    $('#'+txtid).val(texdesc);
    $('#'+txt_id2).val(txtval);
    $("#JWOpopup").hide();
    
    $("#JWOcodesearch").val(''); 
    $("#JWOnamesearch").val(''); 
    JWOCodeFunction();
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

  var VID_REF         = $("#VID_REF").val();
  var JWOID_REF       = $(this).parent().parent().find('[id*="JWOID_REF"]').val();
  var txtJWOID_popup  = $(this).parent().parent().find('[id*="txtJWOID_popup"]').attr('id');

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
  else if(JWOID_REF ===""){
    $("#FocusId").val(txtJWOID_popup);
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select JWO No.');
    $("#alert").modal('show')
    $("#OkBtn1").focus();
  }
  else{

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
        data:{'VID_REF':VID_REF,'JWOID_REF':JWOID_REF,'status':'A'},
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
    var id12  = $(this).parent().parent().find('[id*="PROID_REF"]').attr('id');


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
        var item_jwoid          =   $("#txt"+fieldid+"").data("desc10");
        var item_soid           =   $("#txt"+fieldid+"").data("desc11");
        var item_soqty          =   $("#txt"+fieldid+"").data("desc12");
        var item_proid          =   $("#txt"+fieldid+"").data("desc13");

        if($(this).find('[id*="chkId"]').is(":checked") == true){

          $('#example2').find('.participantRow').each(function(){

            var JWOID_REF   =   $(this).find('[id*="JWOID_REF"]').val();
            var PROID_REF   =   $(this).find('[id*="PROID_REF"]').val();
            var SOID_REF    =   $(this).find('[id*="SOID_REF"]').val();
            var SQID_REF    =   $(this).find('[id*="SQID_REF"]').val();
            var SEID_REF    =   $(this).find('[id*="SEID_REF"]').val();
            var ITEMID_REF  =   $(this).find('[id*="ITEMID_REF"]').val();

            var exist_val   =   JWOID_REF+"_"+PROID_REF+"_"+SOID_REF+"_"+SQID_REF+"_"+SEID_REF+"_"+ITEMID_REF;

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
                $('#hdn_ItemID12').val('');
                
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
                item_proid          =   '';
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
            $clone.find('[id*="PROID_REF"]').val(item_proid);

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
            if($.trim(txt_id12)!=""){
              $('#'+txt_id12).val(item_proid);
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
           
          }
                  
        
        }
        
      });

      get_materital_item();
    
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
    var item_jwoid          =   $("#txt"+fieldid+"").data("desc10");
    var item_soid           =   $("#txt"+fieldid+"").data("desc11");
    var item_soqty          =   $("#txt"+fieldid+"").data("desc12");
    var item_proid          =   $("#txt"+fieldid+"").data("desc13");

    if($(this).is(":checked") == true) {

      $('#example2').find('.participantRow').each(function(){

        var JWOID_REF   =   $(this).find('[id*="JWOID_REF"]').val();
        var PROID_REF   =   $(this).find('[id*="PROID_REF"]').val();
        var SOID_REF    =   $(this).find('[id*="SOID_REF"]').val();
        var SQID_REF    =   $(this).find('[id*="SQID_REF"]').val();
        var SEID_REF    =   $(this).find('[id*="SEID_REF"]').val();
        var ITEMID_REF  =   $(this).find('[id*="ITEMID_REF"]').val();

        var exist_val   =   JWOID_REF+"_"+PROID_REF+"_"+SOID_REF+"_"+SQID_REF+"_"+SEID_REF+"_"+ITEMID_REF;

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
            $('#hdn_ItemID12').val('');
             
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
            item_proid           =   '';
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
        $clone.find('[id*="PROID_REF"]').val(item_proid);

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
        if($.trim(txt_id12)!=""){
          $('#'+txt_id12).val(item_proid);
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

    get_materital_item();
    
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

/*================================== ONLOAD FUNCTION ==================================*/

$(document).ready(function(e) {
  
  var lastdt = <?php echo json_encode($objlastdt[0]->JWCDT); ?>;
  var today = new Date(); 
  var prodate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;

  $('#JWCDT').attr('min',lastdt);
  $('#JWCDT').attr('max',prodate);

  

  var d = new Date(); 
  var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
  $('#JWCDT').val(today);

});

$(document).ready(function(e) {

  var today        = new Date(); 
  var currentdate  = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;

  $('[id*="EDA_"]').attr('min',currentdate);
  $('[id*="EDA_"]').val(currentdate);

  var Material = $("#Material").html(); 
  $('#hdnmaterial').val(Material);

  $("#Row_Count1").val(1);
  $("#Row_Count5").val(1);   

  $('#btnAdd').on('click', function() {
    var viewURL = '{{route("transaction",[$FormId,"add"])}}';
    window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });
  
  $('#JWCNO').focusout(function(){
      var JWCNO   =   $.trim($(this).val());
      if(JWCNO ===""){
               
               
                
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text('Please enter value in JWC No.');
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
                                      $("#JWCNO").val('');
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
  $('#JWCDT').change(function( event ) {
    var today = new Date();     
    var d = new Date($(this).val()); 
    today.setHours(0, 0, 0, 0) ;
    d.setHours(0, 0, 0, 0) ;
    var sodate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
    if (d < today) {
        $(this).val(sodate);
        $("#alert").modal('show');
        $("#AlertMessage").text('JWC Date cannot be less than Current date');
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

  var JWCNO         =   $.trim($("#JWCNO").val());
  var JWCDT         =   $.trim($("#JWCDT").val());
  var VID_REF       =   $.trim($("#VID_REF").val());

  if(JWCNO ===""){
    $("#FocusId").val('JWCNO');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Enter JWC NO');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if(JWCDT ===""){
    $("#FocusId").val('JWCDT');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Select JWC Date');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if(VID_REF ===""){
    $("#FocusId").val('txtvendor_popup');    
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Select Vendor');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }      
  else{

    event.preventDefault();

    var allblank1 = [];
    var allblank2 = [];
    var allblank3 = [];
    var allblank4 = [];
    var allblank5 = [];
    var allblank6 = [];
    var allblank7 = [];
    var allblank8 = [];
    var allblank9 = [];
      
    var focustext1   = "";
    var focustext2   = "";
    var focustext3   = "";
    var focustext4   = "";
    var focustext5   = "";
    var focustext6   = "";
    var focustext7   = "";
    var focustext8   = "";
    var focustext9   = "";

    $('#Material').find('.participantRow').each(function(){

      if($.trim($(this).find("[id*=JWOID_REF]").val()) ===""){
        allblank1.push('false');
        focustext1 = $(this).find("[id*=txtJWOID_popup]").attr('id');
        return false;
      }
      else if($.trim($(this).find("[id*=ITEMID_REF]").val()) ===""){
        allblank2.push('false');
        focustext2 = $(this).find("[id*=popupITEMID]").attr('id');
        return false;
      }
      else if($.trim($(this).find("[id*=MAIN_UOMID_REF]").val()) ===""){
        allblank3.push('false');
        focustext3 = $(this).find("[id*=popupMUOM]").attr('id');
        return false;
      }
      else if($.trim($(this).find("[id*=QTY]").val()) ===""){
        allblank4.push('false');
        focustext4 = $(this).find("[id*=QTY]").attr('id');
        return false;
      }
      else if($.trim($(this).find("[id*=PD_OR_QTY]").val()) ==="" || parseFloat($.trim($(this).find("[id*=PD_OR_QTY]").val())) <=0){
        allblank5.push('false');
        focustext5 = $(this).find("[id*=PD_OR_QTY]").attr('id');
        return false;
      }
      else if(parseFloat($.trim($(this).find("[id*=PD_OR_QTY]").val())) > parseFloat($.trim($(this).find("[id*=BL_SOQTY]").val())) ){
        allblank6.push('false');
        focustext6 = $(this).find("[id*=PD_OR_QTY]").attr('id');
        return false;
      }
      else if($.trim($(this).find("[id*=EDA]").val()) ===""){
        allblank7.push('false');
        focustext7 = $(this).find("[id*=EDA]").attr('id');
        return false;
      }
      else{
        allblank1.push('true');
        allblank2.push('true');
        allblank3.push('true');
        allblank4.push('true');
        allblank5.push('true');
        allblank6.push('true');
        allblank7.push('true');
        allblank8.push('true');
        allblank9.push('true');

        focustext1   = "";
        focustext2   = "";
        focustext3   = "";
        focustext4   = "";
        focustext5   = "";
        focustext6   = "";
        focustext7   = "";
        focustext8   = "";
        focustext9   = "";
        return true;
      }

    });

    $('#material_item').find('.participantRow8').each(function(){

      if(parseFloat($.trim($(this).find("[id*=TotalHiddenQty]").val())).toFixed(2) != parseFloat($.trim($(this).find("[id*=REQ_JWC_QTY]").val())).toFixed(2) ){
        allblank8.push('false');
        focustext8 = $(this).find("[id*=REQ_JWC_QTY]").attr('id');
        return false;
      }
      else{
        allblank8.push('true');
        focustext8   = "";
        return true;
      }

    });                                       


    $("[id*=txtudffie_popup]").each(function(){
      if($.trim($(this).val())!=""){
        if($.trim($(this).parent().parent().find('[id*="udffieismandatory"]').val()) == "1"){

          if($.trim($(this).parent().parent().find('[id*="udfvalue"]').val()) == ""){
            allblank9.push('false');
            focustext9 = $(this).parent().parent().find('[id*="udfvalue"]').attr('id');
            return false;   
          }
          else{
            allblank9.push('true');
            focustext9   = "";
            return true;
          }

        } 
      }
    });


    if(jQuery.inArray("false", allblank1) !== -1){
      $("#FocusId").val(focustext1);
      $("#alert").modal('show');
      $("#AlertMessage").text('Please Enter JWO No In Material');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank2) !== -1){
      $("#FocusId").val(focustext2);
      $("#alert").modal('show');
      $("#AlertMessage").text('Please Select Item In Material');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank3) !== -1){
      $("#FocusId").val(focustext3);
      $("#alert").modal('show');
      $("#AlertMessage").text('UOM Is Missing in Material');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank4) !== -1){
      $("#FocusId").val(focustext4);
      $("#alert").modal('show');
      $("#AlertMessage").text('Order Qty Should Not Blank In Material');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank5) !== -1){
      $("#FocusId").val(focustext5);
      $("#alert").modal('show');
      $("#AlertMessage").text('Produce Qty should be greater than 0 In Material');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank6) !== -1){
      $("#FocusId").val(focustext6);
      $("#alert").modal('show');
      $("#AlertMessage").text('Produce Qty Should Not Greater Then Pending Qty In Material');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank7) !== -1){
      $("#FocusId").val(focustext7);
      $("#alert").modal('show');
      $("#AlertMessage").text('EDA Should Not Empty In Material');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank8) !== -1){
      $("#FocusId").val(focustext8);
      $("#alert").modal('show');
      $("#AlertMessage").text('Store Qty Should Equal To JWC Qty In Material');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank9) !== -1){
        $("#FocusId").val(focustext9);
        $("#alert").modal('show');
        $("#AlertMessage").text('Please Enter Value / Comment In UDF');
        $("#YesBtn").hide(); 
        $("#NoBtn").hide();  
        $("#OkBtn1").show();
        $("#OkBtn1").focus();
        highlighFocusBtn('activeOk');
        return false;
    }
    else if(checkPeriodClosing('{{$FormId}}',$("#JWCDT").val(),0) ==0){
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

            if(data.errors.JWCNO){
                showError('ERROR_JWCNO',data.errors.JWCNO);
                        $("#YesBtn").hide();
                        $("#NoBtn").hide();
                        $("#OkBtn1").show();
                        $("#AlertMessage").text('Please enter correct value in JWC NO.');
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

/*================================== MATERIAL ITEM FUNCTION ==================================*/

function get_materital_item(){

  var  item_array   = [];
  $('#Material').find('.participantRow').each(function(){
    var JWOID_REF   = $(this).find('[id*="JWOID_REF"]').val();
    var PROID_REF   = $(this).find('[id*="PROID_REF"]').val();
    var SOID_REF    = $(this).find('[id*="SOID_REF"]').val();
    var ITEMID_REF  = $(this).find('[id*="ITEMID_REF"]').val();
    var ITEMID_CODE = $(this).find('[id*="popupITEMID"]').val();
    var PD_OR_QTY   = $(this).find('[id*="PD_OR_QTY"]').val();
    var SQID_REF    = $(this).find('[id*="SQID_REF"]').val();
    var SEID_REF    = $(this).find('[id*="SEID_REF"]').val();

    item_array.push(JWOID_REF+'_'+SOID_REF+'_'+ITEMID_REF+'_'+ITEMID_CODE+'_'+PD_OR_QTY+'_'+SQID_REF+'_'+SEID_REF+'_'+PROID_REF);

  });

  var taxstate = $.trim($('#Tax_State').val());

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
        item_array:item_array,'taxstate':taxstate
        },
      success:function(data) {
        $("#material_item").html(data);
        calculateItemTax();                
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

/*================================== STORE FUNCTION ==================================*/

function getStore(ROW_ID,ITEMID_REF){
  getStoreDetails(ITEMID_REF,ROW_ID);
  $("#StoreModal").show();
  event.preventDefault();
}

function getStoreDetails(ITEMID_REF,ROW_ID){

  var ITEMROWID = $("#HiddenRowId_"+ROW_ID).val();
  var UOMID_REF = $("#REQ_UOMID_REF_"+ROW_ID).val();
  
  $("#StoreTable").html('');
  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  $.ajax({
      url:'{{route("transaction",[$FormId,"getStoreDetails"])}}',
      type:'POST',
      data:{ITEMID_REF:ITEMID_REF,ROW_ID:ROW_ID,ITEMROWID:ITEMROWID,ACTION_TYPE:'ADD',UOMID_REF:UOMID_REF},
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
  $("#StoreModal").hide();
});


function checkStoreQty(ROW_ID,stockQty,userQty,key){

  if(userQty > stockQty){
    $("#UserQty_"+key).val('');
    $("#FocusId").val("#UserQty_"+key);
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Dispatch Qty should not greater then Stock-in-hand');
    $("#alert").modal('show')
    $("#OkBtn1").focus();
    return false;
  } 
  else{

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
      $("#TotalHiddenQty_"+ROW_ID).val(TotalQty);
      $("#HiddenRowId_"+ROW_ID).val(NewIdArr);
      $("#REQ_JWC_QTY_"+ROW_ID).val(parseFloat(TotalQty).toFixed(3));
      //$("#SE_QTY_"+ROW_ID).val(TotalQty); 
      calculateItemTax(); 
  }
}

function getArraySum(a){
    var total=0;
    for(var i in a) { 
        total += a[i];
    }
    return total;
}


function calculateItemTax(){

  var TotalValue =0;
  $('#material_item').find('.participantRow8').each(function(){

    var REQ_JWC_QTY = $.trim($(this).find("[id*=REQ_JWC_QTY]").val());
    var JWC_RATE    = $.trim($(this).find("[id*=JWC_RATE]").val());
    var JWC_AMOUNT  = $.trim($(this).find("[id*=JWC_AMOUNT]").val());
    var IGST        = $.trim($(this).find("[id*=IGST]").val());
    var CGST        = $.trim($(this).find("[id*=CGST]").val());
    var SGST        = $.trim($(this).find("[id*=SGST]").val());

    var REQ_JWC_QTY = REQ_JWC_QTY !="" && parseFloat(REQ_JWC_QTY) > 0 ? parseFloat(REQ_JWC_QTY):0;
    var JWC_RATE    = JWC_RATE !="" && parseFloat(JWC_RATE) > 0 ? parseFloat(JWC_RATE):0;
    var JWC_AMOUNT  = JWC_AMOUNT !="" && parseFloat(JWC_AMOUNT) > 0 ? parseFloat(JWC_AMOUNT):0;
    var IGST        = IGST !="" && parseFloat(IGST) > 0 ? parseFloat(IGST):0;
    var CGST        = CGST !="" && parseFloat(CGST) > 0 ? parseFloat(CGST):0;
    var SGST        = SGST !="" && parseFloat(SGST) > 0 ? parseFloat(SGST):0;

    JWC_AMOUNT      = REQ_JWC_QTY*JWC_RATE;

    IGST_AMT        = IGST > 0 ? (JWC_AMOUNT*IGST)/100:0;
    CGST_AMT        = CGST > 0 ? (JWC_AMOUNT*CGST)/100:0;
    SGST_AMT        = SGST > 0 ? (JWC_AMOUNT*SGST)/100:0;

    TGST_AMT        = parseFloat(IGST_AMT)+parseFloat(CGST_AMT)+parseFloat(SGST_AMT);
    TT_AMT          = parseFloat(JWC_AMOUNT)+parseFloat(TGST_AMT);

    $(this).find("[id*=JWC_AMOUNT]").val(parseFloat(JWC_AMOUNT).toFixed(2));
    $(this).find("[id*=IGST_AMT]").val(parseFloat(IGST_AMT).toFixed(4));
    $(this).find("[id*=CGST_AMT]").val(parseFloat(CGST_AMT).toFixed(4));
    $(this).find("[id*=SGST_AMT]").val(parseFloat(SGST_AMT).toFixed(4));
    $(this).find("[id*=TGST_AMT]").val(parseFloat(TGST_AMT).toFixed(4));
    $(this).find("[id*=TT_AMT]").val(parseFloat(TT_AMT).toFixed(2));

    TotalValue=TotalValue+TT_AMT;
                   
  });

  $("#TotalValue").val(parseFloat(TotalValue).toFixed(2));
  getTotalRowValue();

}

function getTotalRowValue(){

  var REQ_JWC_QTY = 0;
  var JWC_RATE    = 0;
  var JWC_AMOUNT  = 0;
  var IGST_AMT    = 0;
  var CGST_AMT    = 0;
  var SGST_AMT    = 0;
  var TGST_AMT    = 0;
  var TT_AMT      = 0;

  $('#material_item').find('.participantRow8').each(function(){
    REQ_JWC_QTY = $(this).find('[id*="REQ_JWC_QTY"]').val() > 0? REQ_JWC_QTY+parseFloat($(this).find('[id*="REQ_JWC_QTY"]').val()):REQ_JWC_QTY;
    JWC_RATE    = $(this).find('[id*="JWC_RATE"]').val() > 0?JWC_RATE+parseFloat($(this).find('[id*="JWC_RATE"]').val()):JWC_RATE;
    JWC_AMOUNT  = $(this).find('[id*="JWC_AMOUNT"]').val() > 0?JWC_AMOUNT+parseFloat($(this).find('[id*="JWC_AMOUNT"]').val()):JWC_AMOUNT;
    IGST_AMT    = $(this).find('[id*="IGST_AMT"]').val() > 0?IGST_AMT+parseFloat($(this).find('[id*="IGST_AMT"]').val()):IGST_AMT;
    CGST_AMT    = $(this).find('[id*="CGST_AMT"]').val() > 0?CGST_AMT+parseFloat($(this).find('[id*="CGST_AMT"]').val()):CGST_AMT;
    SGST_AMT    = $(this).find('[id*="SGST_AMT"]').val() > 0?SGST_AMT+parseFloat($(this).find('[id*="SGST_AMT"]').val()):SGST_AMT;
    TGST_AMT    = $(this).find('[id*="TGST_AMT"]').val() > 0?TGST_AMT+parseFloat($(this).find('[id*="TGST_AMT"]').val()):TGST_AMT;
    TT_AMT      = $(this).find('[id*="TT_AMT"]').val() > 0?TT_AMT+parseFloat($(this).find('[id*="TT_AMT"]').val()):TT_AMT;
  });

  REQ_JWC_QTY = REQ_JWC_QTY > 0?parseFloat(REQ_JWC_QTY).toFixed(3):'';
  JWC_RATE    = JWC_RATE > 0?parseFloat(JWC_RATE).toFixed(5):'';
  JWC_AMOUNT  = JWC_AMOUNT > 0?parseFloat(JWC_AMOUNT).toFixed(2):'';
  IGST_AMT    = IGST_AMT > 0?parseFloat(IGST_AMT).toFixed(2):'';
  CGST_AMT    = CGST_AMT > 0?parseFloat(CGST_AMT).toFixed(2):'';
  SGST_AMT    = SGST_AMT > 0?parseFloat(SGST_AMT).toFixed(2):'';
  TGST_AMT    = TGST_AMT > 0?parseFloat(TGST_AMT).toFixed(2):'';
  TT_AMT      = TT_AMT > 0?parseFloat(TT_AMT).toFixed(2):'';

  $("#REQ_JWC_QTY_total").text(REQ_JWC_QTY);
  $("#JWC_RATE_total").text(JWC_RATE);
  $("#JWC_AMOUNT_total").text(JWC_AMOUNT);
  $("#IGST_AMT_total").text(IGST_AMT);
  $("#CGST_AMT_total").text(CGST_AMT);
  $("#SGST_AMT_total").text(SGST_AMT);
  $("#TGST_AMT_total").text(TGST_AMT);
  $("#TT_AMT_total").text(TT_AMT);

}
</script>


@endpush