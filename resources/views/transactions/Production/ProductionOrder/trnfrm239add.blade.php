@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
  <div class="row">
    <div class="col-lg-2"><a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Production Order (PRO)</a></div>

    <div class="col-lg-10 topnav-pd">
      <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
      <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
      <button class="btn topnavbt" id="btnSave" ><i class="fa fa-save"></i> Save</button>
      <button style="display:none" class="btn topnavbt buttonload"> <i class="fa fa-refresh fa-spin"></i> {{Session::get('save')}}</button>
      <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
      <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
      <button class="btn topnavbt" id="btnUndo"  ><i class="fa fa-undo"></i> Undo</button>
      <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
      <button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-lock"></i> Approved</button>
      <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
      <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
    </div>
  </div>
</div>


<form id="production_order_add" onsubmit="return validateForm()"  method="POST" class="needs-validation"  >  
  <div class="container-fluid filter">
	  <div class="inner-form">

      <div class="row">
        <div class="col-lg-1 pl"><p>PRO No*</p></div>
        <div class="col-lg-2 pl">
          <input type="text" name="PRO_NO" id="PRO_NO" value="{{$docarray['DOC_NO']}}" {{$docarray['READONLY']}} class="form-control" maxlength="{{$docarray['MAXLENGTH']}}" autocomplete="off" style="text-transform:uppercase" >
          <script>docMissing(@json($docarray['FY_FLAG']));</script>
          <span class="text-danger" id="ERROR_PRO_NO"></span>
        </div>
              
        <div class="col-lg-1 pl"><p>PRO Date*</p></div>
        <div class="col-lg-2 pl">
          <input type="date" name="PRO_DT" id="PRO_DT" value="{{ old('PRO_DT') }}" onchange='checkPeriodClosing("{{$FormId}}",this.value,1),getDocNoByEvent("PRO_NO",this,@json($doc_req))' class="form-control mandatory"  placeholder="dd/mm/yyyy" maxlength="10" >
          <span class="text-danger" id="ERROR_PRO_DT"></span>
        </div>

        <div class="col-lg-1 pl"><p>Title</p></div>
        <div class="col-lg-3 pl">
          <input type="text" name="PRO_TITLE" id="PRO_TITLE" class="form-control" maxlength="100" autocomplete="off">
          <span class="text-danger" id="ERROR_PRO_TITLE"></span>
        </div>

        <div class="col-lg-1 pl"><p>Direct</p></div>
        <div class="col-lg-1 pl">
          <input type="checkbox" name="Direct" id="Direct" value="1" autocomplete="off" onChange="direct()">
          <span class="text-danger" id="ERROR_PRO_TITLE"></span>
        </div>

        <input type="hidden" name="AllStatus" id="AllStatus" >

      </div>      
    </div>

	  <div class="container-fluid">

      <div class="row">
        <ul class="nav nav-tabs">
          <li class="active"><a data-toggle="tab" href="#Material">Material</a></li>
          <li><a data-toggle="tab" href="#udf">UDF</a></li>
        </ul>
			
			  <div class="tab-content">

				  <div id="Material" class="tab-pane fade in active">
					  <div class="table-responsive table-wrapper-scroll-y" style="margin-top:10px;" >
						  <table id="example2" class="display nowrap table table-striped table-bordered itemlist w-200" width="100%" style="height:auto !important;">
							  <thead id="thead1"  style="position: sticky;top: 0">
								  <tr>
                    <th hidden ><input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1" value="1"></th>
                    <th>Customer</th>
                    <th   hidden>SLID_REF</th>
                    <th>SO No</th>
                    <th  hidden>SOID_REF</th>
                    <th  hidden>SQID_REF</th>
                    <th  hidden>SEID_REF</th>
                    <th>Item Code </th>
                    <th hidden >ITEMID_REF </th>
                    <th>Item Name</th>
                    <th hidden>Item Specifications</th>

                    <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</th>
                    <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</th>
                    <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</th>

                    <th>UOM</th>
                    <th hidden>Alt UOM (AU)</th>
                    <th>Standard BOM Qty</th>
                    <th>SO Qty</th>
                    <th>Balance SO Qty</th>
                    <th>Production Order Qty</th>
                    <th hidden>MAINROW ID</th>
                    <th>Action</th>
								  </tr>
							  </thead>  
							  <tbody>
								  <tr  class="participantRow">
                    <td hidden><input type="hidden" id="0" > </td>
                    <td><input  type="text" name="txtSL_popup_0" id="txtSL_popup_0" class="form-control"  autocomplete="off"  readonly style="width:100px;" /></td>
                    <td  hidden><input type="text" name="SLID_REF_0" id="SLID_REF_0" class="form-control" autocomplete="off" /></td>

                    <td><input  type="text" name="txtSO_popup_0" id="txtSO_popup_0" class="form-control"  autocomplete="off"  readonly style="width:100px;" /></td>
                    <td   hidden><input type="text" name="SOID_REF_0" id="SOID_REF_0" class="form-control" autocomplete="off" /></td>
                    
                    <td  hidden ><input type="text" name="SQID_REF_0" id="SQID_REF_0" class="form-control" autocomplete="off" /></td>
                    <td   hidden><input type="text" name="SEID_REF_0" id="SEID_REF_0" class="form-control" autocomplete="off" /></td>
                  
                    <td><input  type="text" name="popupITEMID_0" id="popupITEMID_0" class="form-control"  autocomplete="off"  readonly style="width:100px;" /></td>
                    <td   hidden><input type="text" name="ITEMID_REF_0" id="ITEMID_REF_0" class="form-control" autocomplete="off" /></td>
                  
                    <td><input type="text" name="ItemName_0" id="ItemName_0" class="form-control"  autocomplete="off"  readonly style="width:200px;" /></td>

                    <td {{$AlpsStatus['hidden']}}><input type="text" name="Alpspartno_0" id="Alpspartno_0" class="form-control"  autocomplete="off"  readonly/></td>
                    <td {{$AlpsStatus['hidden']}}><input type="text" name="Custpartno_0" id="Custpartno_0" class="form-control"  autocomplete="off"  readonly/></td>
                    <td {{$AlpsStatus['hidden']}}><input type="text" name="OEMpartno_0"  id="OEMpartno_0" class="form-control"  autocomplete="off"   readonly/></td>

               
                    <td><input type="text" name="popupMUOM_0" id="popupMUOM_0" class="form-control"  autocomplete="off"  readonly style="width:100px;"/></td>
                    <td hidden><input type="text" name="MAIN_UOMID_REF_0" id="MAIN_UOMID_REF_0" class="form-control"  autocomplete="off" /></td>

                    <td hidden><input type="text"   name="BOMID_REF_0" id="BOMID_REF_0" class="form-control" /></td>
                    <td><input type="text"   name="BOMDATA_0" id="BOMDATA_0" class="form-control three-digits"  readonly  style="width:100px;"  /></td>

                    <td><input type="text"   name="QTY_0" id="QTY_0" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" readonly  style="width:100px;"  /></td>
                    <td><input type="text"   name="BL_SOQTY_0" id="BL_SOQTY_0" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" readonly  style="width:100px;"  /></td>
                    <td><input type="text"   name="PD_OR_QTY_0" id="PD_OR_QTY_0" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" style="width:100px;" onKeyup="change_production_order(this.id,this.value)"  /></td>
                    <td hidden><input type="text"   name="MAINTROWID_0" id="MAINTROWID_0" class="form-control " style="width:100px;"  /></td>
                    
                    <td align="center" >
                      <button  class="btn add material" title="add" data-toggle="tooltip" type="button" disabled ><i class="fa fa-plus"></i></button>
                      <button  class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button>
                    </td>

								  </tr>
							  </tbody>
					    </table>

              <div id="material_item">
                
              </div>


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
              @foreach($objUdfData as $uindex=>$uRow)
                <tr  class="participantRow3">
                    <td><input type="text" name={{"popupSEID_".$uindex}} id={{"popupSEID_".$uindex}} class="form-control" value="{{$uRow->LABEL}}" autocomplete="off"  readonly/></td>
                    <td hidden><input type="hidden" name={{"UDF_".$uindex}} id={{"UDF_".$uindex}} class="form-control" value="{{$uRow->UDFPROID}}" autocomplete="off"   /></td>
                    <td hidden><input type="hidden" name={{"UDFismandatory_".$uindex}} id={{"UDFismandatory_".$uindex}} value="{{$uRow->ISMANDATORY}}" class="form-control"   autocomplete="off" /></td>
                    <td id={{"udfinputid_".$uindex}} >
                      
                    </td>
                    <td align="center" ><button class="btn add UDF" title="add" data-toggle="tooltip" type="button" disabled><i class="fa fa-plus"></i></button><button class="btn remove DUDF" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
                    
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
    <tr id="none-select" class="searchalldata" hidden>
      <td> 
        <input type="hidden" id="hdn_slid"/>
        <input type="hidden" id="hdn_slid2"/>
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
        <td class="ROW1"><span class="check_th">&#10004;</span></td>
        <td class="ROW2"><input type="text" id="customercodesearch" class="form-control" onkeyup="CustomerCodeFunction('{{$FormId}}')"></td>
        <td class="ROW3"><input type="text" id="customernamesearch" class="form-control" onkeyup="CustomerNameFunction('{{$FormId}}')"></td>
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

<div id="SOpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='SO_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>SO No</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="SOTable" class="display nowrap table  table-striped table-bordered" >
    <thead>
      <tr id="none-select" class="searchalldata" hidden>
        
        <td> 
        <input type="hidden"  id="hdn_SOid"/>
        <input type="hidden" id="hdn_SOid2"/>
        <input type="hidden" id="hdn_SOid3"/>
        </td>
      </tr>
      <tr>
        <th class="ROW1">Select</th> 
        <th class="ROW2">Sales Order No</th>
        <th class="ROW3">Date</th>
      </tr>
    </thead>
    <tbody>
    <tr>
    <td class="ROW1"><span class="check_th">&#10004;</span></td>
    <td class="ROW2">
    <input type="text" id="SOcodesearch" onkeyup="SOCodeFunction()" class="form-control" >
    </td>
    <td class="ROW3">
    <input type="text" id="SOnamesearch" onkeyup="SONameFunction()" class="form-control" >
    </td>
    </tr>
    </tbody>
    </table>
      <table id="SOTable2" class="display nowrap table  table-striped table-bordered">
        <thead id="thead2">

        </thead>
        <tbody id="tbody_SO">     
        
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
	  <div class="tablename"><p>SUBITEM DETAILS</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="SUBITEMTable" class="display nowrap table  table-striped table-bordered">
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
      <td class="ROW1"><span class="check_th">&#10004;</span></td>
      <td class="ROW2">
      <input type="text" id="SUBITEMcodesearch" onkeyup="SUBITEMCodeFunction()" class="form-control" >
      </td>
      <td class="ROW3">
      <input type="text" id="SUBITEMnamesearch" onkeyup="SUBITEMNameFunction()" class="form-control" >
      </td>
    </tr>
    </tbody>
    </table>
      <table id="SUBITEMTable2" class="display nowrap table  table-striped table-bordered">
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
                  <input type="text" name="fieldid10" id="hdn_ItemID11"/>
                  <input type="text" name="fieldid10" id="hdn_ItemID12"/>
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
                <td style="width:8%;text-align:center;"><!--<input type="checkbox" class="js-selectall" data-target=".js-selectall1" />--></td>
                <td style="width:10%;"><input type="text" id="Itemcodesearch" class="form-control" autocomplete="off" onkeyup="ItemCodeFunction(event)"></td>
                <td style="width:10%;"><input type="text" id="Itemnamesearch" class="form-control" autocomplete="off" onkeyup="ItemNameFunction(event)"></td>
                <td style="width:8%;"><input type="text" id="ItemUOMsearch" class="form-control" autocomplete="off" onkeyup="ItemUOMFunction(event)"></td>
                <td style="width:8%;"><input type="text" id="ItemQTYsearch" class="form-control" autocomplete="off" onkeyup="ItemQTYFunction(event)" readonly></td>
                <td style="width:8%;"><input type="text" id="ItemGroupsearch" class="form-control" autocomplete="off" onkeyup="ItemGroupFunction(event)"></td>
                <td style="width:8%;"><input type="text" id="ItemCategorysearch" class="form-control" autocomplete="off" onkeyup="ItemCategoryFunction(event)"></td>
                <td style="width:8%;"><input type="text" id="ItemBUsearch" class="form-control" autocomplete="off" onkeyup="ItemBUFunction(event)" readonly></td>
                <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemAPNsearch" class="form-control" autocomplete="off" onkeyup="ItemAPNFunction(event)"></td>
                <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemCPNsearch" class="form-control" autocomplete="off" onkeyup="ItemCPNFunction(event)"></td>
                <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemOEMPNsearch" class="form-control" autocomplete="off" onkeyup="ItemOEMPNFunction(event)"></td>
                <td style="width:8%;"><input  type="text" id="ItemStatussearch" class="form-control" autocomplete="off" onkeyup="ItemStatusFunction(event)" readonly></td>
              </tr>
            </tbody>
          </table>

          <table id="ItemIDTable2" class="display nowrap table  table-striped table-bordered" style="width:100%" >
            <thead id="thead2"></thead>
            <tbody id="tbody_ItemID" style="font-size:13px;"></tbody>
          </table>
        </div>
		    <div class="cl"></div>
      </div>
    </div>
  </div>
</div>


<div id="ITEMIDpopup2" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width: 87%;margin-top: 7%;">
    <div class="modal-content" >
      <div class="modal-header"><button type="button" class="close" data-dismiss="modal" id='ITEMID_closePopup2' >&times;</button></div>
      <div class="modal-body">
	      <div class="tablename"><p>Sub Item Details</p></div>
	      <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
          <table id="ItemIDTable2" class="display nowrap table  table-striped table-bordered" style="width:100%" >
            <thead>
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
                <td style="width:8%;text-align:center;"> <input type="checkbox" id="select_all" /> </td>
                <td style="width:10%;"><input type="text" id="Itemcodesearch2" class="form-control" autocomplete="off" onkeyup="ItemCodeFunction2()"></td>
                <td style="width:10%;"><input type="text" id="Itemnamesearch2" class="form-control" autocomplete="off" onkeyup="ItemNameFunction2()"></td>
                <td style="width:8%;"><input type="text" id="ItemUOMsearch2" class="form-control" autocomplete="off" onkeyup="ItemUOMFunction2()"></td>
                <td style="width:8%;"><input type="text" id="ItemQTYsearch2" class="form-control" autocomplete="off" onkeyup="ItemQTYFunction2()"></td>
                <td style="width:8%;"><input type="text" id="ItemGroupsearch2" class="form-control" autocomplete="off" onkeyup="ItemGroupFunction2()"></td>
                <td style="width:8%;"><input type="text" id="ItemCategorysearch2" class="form-control" autocomplete="off" onkeyup="ItemCategoryFunction2()"></td>
                
                <td style="width:8%;"><input type="text" id="ItemBUsearch2" class="form-control" autocomplete="off" onkeyup="ItemBUFunction2()"></td>
                <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemAPNsearch2" class="form-control" autocomplete="off" onkeyup="ItemAPNFunction2()"></td>
                <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemCPNsearch2" class="form-control" autocomplete="off" onkeyup="ItemCPNFunction2()"></td>
                <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemOEMPNsearch2" class="form-control" autocomplete="off" onkeyup="ItemOEMPNFunction2()"></td>
                
                <td style="width:8%;"><input  type="text" id="ItemStatussearch2" class="form-control" autocomplete="off" onkeyup="ItemStatusFunction2()"></td>
              </tr>
            </tbody>
          </table>

          <table id="ItemIDTable22" class="display nowrap table  table-striped table-bordered" style="width:100%" >
            <thead id="thead2"></thead>
            <tbody id="tbody_ItemID2" style="font-size:13px;"></tbody>
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

/*
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
}*/
</style>
@endpush

@push('bottom-scripts')
<script>
/*================================== BUTTON FUNCTION ================================*/
var AllStatus="";

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
  window.location.href = "{{route('transaction',[$FormId,'add'])}}";
}

$("#btnSave" ).click(function() {
    var formReqData = $("#production_order_add");
    if(formReqData.valid()){
      validateForm();
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
  // if($("#"+$("#FocusId").val())!=undefined){
  //   $("#"+$("#FocusId").val()).focus();
  // }  
  $("#closePopup").click();
}

function highlighFocusBtn(pclass){
  $(".activeYes").hide();
  $(".activeNo").hide();
  $("."+pclass+"").show();
}

/*================================== Save FUNCTION =================================*/
window.fnSaveData = function (){

  event.preventDefault();

  var trnFormReq  = $("#production_order_add");
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
    url:'{{ route("transaction",[$FormId,"save"])}}',
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

/*================================== VALIDATE FUNCTION =================================*/
function validateForm(){
  
  $("#FocusId").val('');
  var PRO_NO      = $.trim($("#PRO_NO").val());
  var PRO_DT      = $.trim($("#PRO_DT").val());  
  
  if(PRO_NO ===""){
      $("#FocusId").val('PRO_NO');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('PRO No is required.');
      $("#alert").modal('show')
      $("#OkBtn1").focus();
      return false;
  }
  else if(PRO_DT ===""){
      $("#FocusId").val('PRO_DT');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please select PRO Date.');
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
    var allblank6   = [];
    var allblank7   = [];
    var allblank8   = [];
    var focustext   = "";
      
    $('#example2').find('.participantRow').each(function(){

      if($.trim($(this).find("[id*=SLID_REF]").val()) ==="" && $("#Direct").is(":checked") == false){
        allblank1.push('false');
        focustext = $(this).find("[id*=txtSL_popup]").attr('id');
      }
      else if($.trim($(this).find("[id*=SOID_REF]").val()) ==="" && $("#Direct").is(":checked") == false){
        allblank2.push('false');
        focustext = $(this).find("[id*=txtSO_popup]").attr('id');
      }
      else if($.trim($(this).find("[id*=ITEMID_REF]").val()) ===""){
        allblank3.push('false');
        focustext = $(this).find("[id*=popupITEMID]").attr('id');
      }
      else if($.trim($(this).find("[id*=MAIN_UOMID_REF]").val()) ===""){
        allblank4.push('false');
        focustext = $(this).find("[id*=popupMUOM]").attr('id');
      }
      else if($.trim($(this).find("[id*=QTY]").val()) ==="" && $("#Direct").is(":checked") == false){
        allblank5.push('false');
        focustext = $(this).find("[id*=QTY]").attr('id');
      }
      else if($.trim($(this).find("[id*=PD_OR_QTY]").val()) ==="" || parseFloat($.trim($(this).find("[id*=PD_OR_QTY]").val())) <=0){
        allblank6.push('false');
        focustext = $(this).find("[id*=PD_OR_QTY]").attr('id');
      }
      else if(parseFloat($.trim($(this).find("[id*=PD_OR_QTY]").val())).toFixed(3) > parseFloat($.trim($(this).find("[id*=BL_SOQTY_]").val())).toFixed(3) && $("#Direct").is(":checked") == false ){
        allblank7.push('false');
        focustext = $(this).find("[id*=PD_OR_QTY]").attr('id');
      }
      else{
        allblank1.push('true');
        allblank2.push('true');
        allblank3.push('true');
        allblank4.push('true');
        allblank5.push('true');
        allblank6.push('true');
        allblank7.push('true');
        focustext   = "";
      }

    });

    $('#example3').find('.participantRow3').each(function(){
      if($.trim($(this).find("[id*=UDF]").val())!=""){
        if($.trim($(this).find("[id*=UDFismandatory]").val())=="1"){
          if($.trim($(this).find('[id*="udfvalue"]').val()) != ""){
            allblank8.push('true');
          }
          else{
            allblank8.push('false');
          }
        }  
      }                
    });
    
    if(jQuery.inArray("false", allblank1) !== -1){
      $("#FocusId").val(focustext);
      $("#alert").modal('show');
      $("#AlertMessage").text('Please Select Customer In Material');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank2) !== -1){
      $("#FocusId").val(focustext);
      $("#alert").modal('show');
      $("#AlertMessage").text('Please Select So No In Material');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank3) !== -1){
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
    else if(jQuery.inArray("false", allblank6) !== -1){
      $("#FocusId").val(focustext);
      $("#alert").modal('show');
      $("#AlertMessage").text('Production Order Qty should be greater than 0 in material tab.');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank7) !== -1){
      $("#FocusId").val(focustext);
      $("#alert").modal('show');
      $("#AlertMessage").text('Production Order qty cannot be greater then balance so qty in material tab.');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank8) !== -1){
      $("#FocusId").val(focustext);
      $("#alert").modal('show');
      $("#AlertMessage").text('Please enter  Value / Comment in UDF Tab.');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(checkPeriodClosing('{{$FormId}}',$("#PRO_DT").val(),0) ==0){
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text(period_closing_msg);
      $("#alert").modal('show');
      $("#OkBtn1").focus();
    }
    else{
      checkDuplicateCode();
    }
  }
}

/*================================== CHECK DUPLICATE FUNCTION =================================*/
function checkDuplicateCode(){

  var trnFormReq  = $("#production_order_add");
  var formData    = trnFormReq.serialize();

  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  $.ajax({
      url:'{{route("transaction",[$FormId,"codeduplicate"])}}',
      type:'POST',
      data:formData,
      success:function(data) {
          if(data.exists) {
              $(".text-danger").hide();
              showError('ERROR_PRO_NO',data.msg);
              $("#PRO_NO").focus();
          }
          else{
            $("#alert").modal('show');
            $("#AlertMessage").text('Do you want to save to record.');
            $("#YesBtn").data("funcname","fnSaveData");
            $("#YesBtn").focus();
            $("#OkBtn").hide();
            highlighFocusBtn('activeYes');
          }                                
      },
      error:function(data){
        console.log("Error: Something went wrong.");
      },
  });
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


/*================================== CUSTOMER POPUP FUNCTION =================================*/

  let cltid = "#GlCodeTable2";
      let cltid2 = "#GlCodeTable";
      let clheaders = document.querySelectorAll(cltid2 + " th");

      // Sort the table element when clicking on the table headers
      clheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(cltid, ".clssubgl", "td:nth-child(" + (i + 1) + ")");
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

          },
          error:function(data){
          console.log("Error: Something went wrong.");
          $("#tbody_subglacct").html('');                        
          },
        });
    }


$('#Material').on('focus','[id*="txtSL_popup"]',function(event){

  $('#hdn_slid').val($(this).attr('id'));
  $('#hdn_slid2').val($(this).parent().parent().find('[id*="SLID_REF"]').attr('id')); 

  $(this).parent().parent().find('[id*="txtSO_popup"]').val('');
  $(this).parent().parent().find('[id*="SOID_REF"]').val('');
  $(this).parent().parent().find('[id*="SQID_REF"]').val('');
  $(this).parent().parent().find('[id*="SEID_REF"]').val('');
  
  $(this).parent().parent().find('[id*="popupITEMID"]').val('');
  $(this).parent().parent().find('[id*="ITEMID_REF"]').val('');
  $(this).parent().parent().find('[id*="ItemName"]').val('');
  $(this).parent().parent().find('[id*="popupMUOM"]').val('');
  $(this).parent().parent().find('[id*="MAIN_UOMID_REF"]').val('');
  $(this).parent().parent().find('[id*="QTY"]').val('');
  $(this).parent().parent().find('[id*="BL_SOQTY"]').val('');
  $(this).parent().parent().find('[id*="PD_OR_QTY"]').val('');
  $("#material_item").html('');
    
    var CODE = ''; 
    var NAME = ''; 
    var FORMID = "{{$FormId}}";
    loadCustomer(CODE,NAME,FORMID);
    $("#customer_popus").show();

  event.preventDefault();
});

$("#customer_closePopup").click(function(event){
  $("#customer_popus").hide();
});

function bindSubLedgerEvents(){
  $(".clssubgl").click(function(){
    var fieldid = $(this).attr('id');
    var txtval  = $("#txt"+fieldid+"").val();
    var texdesc = $("#txt"+fieldid+"").data("desc");
    
    var txtid   = $('#hdn_slid').val();
    var txt_id2 = $('#hdn_slid2').val();

    $('#'+txtid).val(texdesc);
    $('#'+txt_id2).val(txtval);
    $("#customer_popus").hide();
    $("#customercodesearch").val(''); 
    $("#customernamesearch").val(''); 
    event.preventDefault();
  });
}


/*================================== SO POPUP FUNCTION =================================*/

let SOTable2 = "#SOTable2";
let SOTable = "#SOTable";
let SOheaders = document.querySelectorAll(SOTable + " th");

SOheaders.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(SOTable2, ".clssSOid", "td:nth-child(" + (i + 1) + ")");
  });
});

function SOCodeFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("SOcodesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("SOTable2");
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

function SONameFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("SOnamesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("SOTable2");
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

$('#Material').on('focus','[id*="txtSO_popup"]',function(event){

  $('#hdn_SOid').val($(this).attr('id'));
  $('#hdn_SOid2').val($(this).parent().parent().find('[id*="SOID_REF"]').attr('id'));

  var SLID_REF      =  $(this).parent().parent().find('[id*="SLID_REF"]').val();
  var txtSL_popup   =  $(this).parent().parent().find('[id*="txtSL_popup"]').attr('id');

  if(SLID_REF ===""){
    $("#FocusId").val(txtSL_popup);
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please select customer.');
      $("#alert").modal('show')
      $("#OkBtn1").focus();
  }
  else{

    $("#SOpopup").show();
    $("#tbody_SO").html('loading...');

    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })

    $.ajax({
        url:'{{route("transaction",[$FormId,"getSOCodeNo"])}}',
        type:'POST',
        data:{'id':SLID_REF},
        success:function(data) {
          $("#tbody_SO").html(data);
          BindSO();
        },
        error:function(data){
          console.log("Error: Something went wrong.");
          $("#tbody_SO").html('');
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

$("#SO_closePopup").click(function(event){
  $("#SOpopup").hide();
});

function BindSO(){
  $(".clssSOid").click(function(){
    var fieldid = $(this).attr('id');
    var txtval =    $("#txt"+fieldid+"").val();
    var texdesc =   $("#txt"+fieldid+"").data("desc");

    var txtid= $('#hdn_SOid').val();
    var txt_id2= $('#hdn_SOid2').val();

    $('#'+txtid).val(texdesc);
    $('#'+txt_id2).val(txtval);
    $("#SOpopup").hide();
    
    $("#SOcodesearch").val(''); 
    $("#SOnamesearch").val(''); 
    SOCodeFunction();
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

function ItemCodeFunction(e){
  if(e.which == 13){
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("Itemcodesearch");
    filter = input.value.toUpperCase();

    if($("#Direct").prop("checked")==true ){
      var CODE  = $("#Itemcodesearch").val();
      var NAME  = $("#Itemnamesearch").val();
      var MUOM  = $("#ItemUOMsearch").val();
      var GROUP = $("#ItemGroupsearch").val(); 
      var CTGRY = $("#ItemCategorysearch").val(); 
      var BUNIT = $("#ItemBUsearch").val(); 
      var APART = $("#ItemAPNsearch").val();
      var CPART = $("#ItemCPNsearch").val(); 
      var OPART = $("#ItemOEMPNsearch").val();
      loadItem(CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
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
}

function ItemNameFunction(e) {
  if(e.which == 13){
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("Itemnamesearch");
    filter = input.value.toUpperCase();

    if($("#Direct").prop("checked")==true ){
      var CODE  = $("#Itemcodesearch").val();
      var NAME  = $("#Itemnamesearch").val();
      var MUOM  = $("#ItemUOMsearch").val();
      var GROUP = $("#ItemGroupsearch").val(); 
      var CTGRY = $("#ItemCategorysearch").val(); 
      var BUNIT = $("#ItemBUsearch").val(); 
      var APART = $("#ItemAPNsearch").val();
      var CPART = $("#ItemCPNsearch").val(); 
      var OPART = $("#ItemOEMPNsearch").val();
      loadItem(CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
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
}

function ItemUOMFunction(e) {
  if(e.which == 13){
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("ItemUOMsearch");
    filter = input.value.toUpperCase();  

    if($("#Direct").prop("checked")==true ){
      var CODE  = $("#Itemcodesearch").val();
      var NAME  = $("#Itemnamesearch").val();
      var MUOM  = $("#ItemUOMsearch").val();
      var GROUP = $("#ItemGroupsearch").val(); 
      var CTGRY = $("#ItemCategorysearch").val(); 
      var BUNIT = $("#ItemBUsearch").val(); 
      var APART = $("#ItemAPNsearch").val();
      var CPART = $("#ItemCPNsearch").val(); 
      var OPART = $("#ItemOEMPNsearch").val();
      loadItem(CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
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
}

function ItemQTYFunction(e) {
  if(e.which == 13){
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
}

function ItemGroupFunction(e) {
  if(e.which == 13){
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("ItemGroupsearch");
    filter = input.value.toUpperCase();

    if($("#Direct").prop("checked")==true){
      var CODE  = $("#Itemcodesearch").val();
      var NAME  = $("#Itemnamesearch").val();
      var MUOM  = $("#ItemUOMsearch").val();
      var GROUP = $("#ItemGroupsearch").val(); 
      var CTGRY = $("#ItemCategorysearch").val(); 
      var BUNIT = $("#ItemBUsearch").val(); 
      var APART = $("#ItemAPNsearch").val();
      var CPART = $("#ItemCPNsearch").val(); 
      var OPART = $("#ItemOEMPNsearch").val();
      loadItem(CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
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
}

function ItemCategoryFunction(e) {
  if(e.which == 13){
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("ItemCategorysearch");
    filter = input.value.toUpperCase();

    if($("#Direct").prop("checked")==true ){
      var CODE  = $("#Itemcodesearch").val();
      var NAME  = $("#Itemnamesearch").val();
      var MUOM  = $("#ItemUOMsearch").val();
      var GROUP = $("#ItemGroupsearch").val(); 
      var CTGRY = $("#ItemCategorysearch").val(); 
      var BUNIT = $("#ItemBUsearch").val(); 
      var APART = $("#ItemAPNsearch").val();
      var CPART = $("#ItemCPNsearch").val(); 
      var OPART = $("#ItemOEMPNsearch").val();
      loadItem(CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
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
}

function ItemBUFunction(e) {
  if(e.which == 13){
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("ItemBUsearch");
  filter = input.value.toUpperCase();

    if($("#Direct").prop("checked")==true ){
      var CODE  = $("#Itemcodesearch").val();
      var NAME  = $("#Itemnamesearch").val();
      var MUOM  = $("#ItemUOMsearch").val();
      var GROUP = $("#ItemGroupsearch").val(); 
      var CTGRY = $("#ItemCategorysearch").val(); 
      var BUNIT = $("#ItemBUsearch").val(); 
      var APART = $("#ItemAPNsearch").val();
      var CPART = $("#ItemCPNsearch").val(); 
      var OPART = $("#ItemOEMPNsearch").val();
      loadItem(CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
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
}

function ItemAPNFunction(e) {
  if(e.which == 13){
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("ItemAPNsearch");
    filter = input.value.toUpperCase();

    if($("#Direct").prop("checked")==true){
      var CODE  = $("#Itemcodesearch").val();
      var NAME  = $("#Itemnamesearch").val();
      var MUOM  = $("#ItemUOMsearch").val();
      var GROUP = $("#ItemGroupsearch").val(); 
      var CTGRY = $("#ItemCategorysearch").val(); 
      var BUNIT = $("#ItemBUsearch").val(); 
      var APART = $("#ItemAPNsearch").val();
      var CPART = $("#ItemCPNsearch").val(); 
      var OPART = $("#ItemOEMPNsearch").val();
      loadItem(CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
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
}

function ItemCPNFunction(e) {
  if(e.which == 13){
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("ItemCPNsearch");
    filter = input.value.toUpperCase();

    if($("#Direct").prop("checked")==true){
      var CODE  = $("#Itemcodesearch").val();
      var NAME  = $("#Itemnamesearch").val();
      var MUOM  = $("#ItemUOMsearch").val();
      var GROUP = $("#ItemGroupsearch").val(); 
      var CTGRY = $("#ItemCategorysearch").val(); 
      var BUNIT = $("#ItemBUsearch").val(); 
      var APART = $("#ItemAPNsearch").val();
      var CPART = $("#ItemCPNsearch").val(); 
      var OPART = $("#ItemOEMPNsearch").val();
      loadItem(CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART);
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
}

function ItemOEMPNFunction(e) {
  if(e.which == 13){
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("ItemOEMPNsearch");
    filter = input.value.toUpperCase();

    if($("#Direct").prop("checked")==true){
      var CODE  = $("#Itemcodesearch").val();
      var NAME  = $("#Itemnamesearch").val();
      var MUOM  = $("#ItemUOMsearch").val();
      var GROUP = $("#ItemGroupsearch").val(); 
      var CTGRY = $("#ItemCategorysearch").val(); 
      var BUNIT = $("#ItemBUsearch").val(); 
      var APART = $("#ItemAPNsearch").val();
      var CPART = $("#ItemCPNsearch").val(); 
      var OPART = $("#ItemOEMPNsearch").val();
      loadItem(CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART);
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
}

function ItemStatusFunction(e) {
  if(e.which == 13){
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
}

function loadItem(CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART){
	
  $("#tbody_ItemID").html('');
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  $.ajax({
    url:'{{route("transaction",[$FormId,"getItemDetails2"])}}',
    type:'POST',
    data:{'CODE':CODE,'NAME':NAME,'MUOM':MUOM,'GROUP':GROUP,'CTGRY':CTGRY,'BUNIT':BUNIT,'APART':APART,'CPART':CPART,'OPART':OPART},
    success:function(data) {
    $("#tbody_ItemID").html(data); 
    bindItemEvents(); 
    $('.js-selectall').prop('disabled', true);
    },
    error:function(data){
    console.log("Error: Something went wrong.");
    $("#tbody_ItemID").html('');                        
    },
  });

}

$('#Material').on('click','[id*="popupITEMID"]',function(event){

  var SLID_REF      =  $(this).parent().parent().find('[id*="SLID_REF"]').val();
  var txtSL_popup   =  $(this).parent().parent().find('[id*="txtSL_popup"]').attr('id');

  var SOID_REF      =  $(this).parent().parent().find('[id*="SOID_REF"]').val();
  var txtSO_popup   =  $(this).parent().parent().find('[id*="txtSO_popup"]').attr('id');

  if(SLID_REF ==="" && $("#Direct").is(":checked") == false){
    $("#FocusId").val(txtSL_popup);
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select customer.');
    $("#alert").modal('show')
    $("#OkBtn1").focus();
  }
  else if(SOID_REF ==="" && $("#Direct").is(":checked") == false){
    $("#FocusId").val(txtSO_popup);
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select SO No.');
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

    if($("#Direct").prop("checked")==true){
      var CODE = ''; 
      var NAME = ''; 
      var MUOM = ''; 
      var GROUP = ''; 
      var CTGRY = ''; 
      var BUNIT = ''; 
      var APART = ''; 
      var CPART = ''; 
      var OPART = ''; 
      loadItem(CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART);
    }
    else{
      $.ajax({
        url:'{{route("transaction",[$FormId,"getItemDetails"])}}',
        type:'POST',
        data:{'SLID_REF':SLID_REF,'SOID_REF':SOID_REF,'status':'A'},
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
    }
          
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
    var id11  = $(this).parent().parent().find('[id*="BOMID_REF"]').attr('id');
    var id12  = $(this).parent().parent().find('[id*="BOMDATA"]').attr('id');

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

    var r_count = 0;
    var SLRow = [];
    $('#example2').find('.participantRow').each(function(){
      if($(this).find('[id*="ITEMID_REF"]').val() != '')
      {
        SLRow.push($(this).find('[id*="SLID_REF"]').val());
        r_count = parseInt(r_count)+1;
        $('#hdn_ItemID21').val(r_count); // row counter
      }
    });
    $('#hdn_ItemID18').val(SLRow.join(', '));
    var ItemID = [];
    $('#example2').find('.participantRow').each(function(){
      if($(this).find('[id*="ITEMID_REF"]').val() != '')
      {
        ItemID.push($(this).find('[id*="ITEMID_REF"]').val());
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

    event.preventDefault();

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
      var item_slid           =   $("#txt"+fieldid+"").data("desc10");
      var item_soid           =   $("#txt"+fieldid+"").data("desc11");
      var item_soqty          =   $("#txt"+fieldid+"").data("desc12");
      var item_type           =   $("#txt"+fieldid+"").data("desc13");
      var item_blqty          =   $("#txt"+fieldid+"").data("desc14");
      var item_matid          =   $("#txt"+fieldid+"").data("desc15");

      var apartno =  $("#addinfo"+fieldid+"").data("desc101");
      var cpartno =  $("#addinfo"+fieldid+"").data("desc102");
      var opartno =  $("#addinfo"+fieldid+"").data("desc103");
           
      var rcount1 = parseInt($(this).closest('table').find('.clsitemid').length);
      var rcount2 = $('#hdn_ItemID21').val();
      var r_count2 = 0;
            
      var SLRow2 = [];
      $('#example2').find('.participantRow').each(function(){
        if($(this).find('[id*="ITEMID_REF"]').val() != '')
        {
          var slitem = $(this).find('[id*="SLID_REF"]').val()+'_'+$(this).find('[id*="SOID_REF"]').val()+'_'+$(this).find('[id*="SQID_REF"]').val()+'_'+$(this).find('[id*="SEID_REF"]').val()+'_'+$(this).find('[id*="ITEMID_REF"]').val();
          SLRow2.push(slitem);
          r_count2 = parseInt(r_count2) + 1;
        }
      });
        
      var slids =  $('#hdn_ItemID18').val();
      var itemids =  $('#hdn_ItemID19').val();
      var soids =  $('#hdn_ItemID23').val();
      var sqids =  $('#hdn_ItemID24').val();
      var seids =  $('#hdn_ItemID25').val();
    
      if($(this).find('[id*="chkId"]').is(":checked") == true){

        rcount1 = parseInt(rcount2)+parseInt(rcount1);
        if(parseInt(r_count2) >= parseInt(rcount1)){

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
          item_slid           =   "";
          item_soid           =   "";
          item_soqty          =   0;

          $('.js-selectall').prop("checked", false);
          $("#ITEMIDpopup").hide();
          return false;
        }
              
        var txtrowitem = item_slid+'_'+item_soid+'_'+item_sqid+'_'+item_seid+'_'+item_id;
        if(jQuery.inArray(txtrowitem, SLRow2) !== -1){

          $("#ITEMIDpopup").hide();
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
          item_slid           =   "";
          item_soid           =   "";
          item_soqty           =   0;
          $('.js-selectall').prop("checked", false);
          $("#ITEMIDpopup").hide();
          return false;
        }

        var slids =  $('#hdn_ItemID18').val();
        var itemids =  $('#hdn_ItemID19').val();
        var soids =  $('#hdn_ItemID23').val();
        var sqids =  $('#hdn_ItemID24').val();
        var seids =  $('#hdn_ItemID25').val();

        if(slids.indexOf(item_slid) != -1 && soids.indexOf(item_soid) != -1 && sqids.indexOf(item_sqid) != -1 && seids.indexOf(item_seid) != -1 && itemids.indexOf(item_id) != -1  ){
                            
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
          item_slid           =   "";
          item_soid           =   "";
          item_soqty           =   0;
          $('.js-selectall').prop("checked", false);
          $("#ITEMIDpopup").hide();
          return false;
        }
        
        if($('#hdn_ItemID1').val() == "" && item_id != ''){

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
          $clone.find('[id*="ItemName"]').val(item_name);
          $clone.find('[id*="popupMUOM"]').val(item_main_uom_code);
          $clone.find('[id*="MAIN_UOMID_REF"]').val(item_main_uom_id);
          $clone.find('[id*="QTY"]').val(item_soqty);
          $clone.find('[id*="BL_SOQTY"]').val(item_blqty);
          $clone.find('[id*="PD_OR_QTY"]').val(item_qty);                       
          $clone.find('[id*="MAINTROWID"]').val(item_unique_row_id);
          $clone.find('[id*="SQID_REF"]').val(item_sqid);
          $clone.find('[id*="SEID_REF"]').val(item_seid);
          $clone.find('[id*="BOMID_REF"]').val(item_matid);
          $clone.find('[id*="BOMDATA"]').val(item_soqty);

          $clone.find('[id*="Alpspartno"]').val(apartno);
          $clone.find('[id*="Custpartno"]').val(cpartno);
          $clone.find('[id*="OEMpartno"]').val(opartno);

          if(item_type =="SFG" && $("#Direct").is(":checked") == false){
            $clone.find('[id*="PD_OR_QTY"]').prop('readonly', true); 
          }
          else{
            $clone.find('[id*="PD_OR_QTY"]').prop('readonly', false); 
          }


          $tr.closest('table').append($clone);   
          var rowCount = $('#Row_Count1').val();
          rowCount = parseInt(rowCount)+1;
          $('#Row_Count1').val(rowCount);
                    
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

          $('#'+txt_id1).val(item_code);
          $('#'+txt_id2).val(item_id);
          $('#'+txt_id3).val(item_name);
          $('#'+txt_id4).val(item_main_uom_code);
          $('#'+txt_id5).val(item_main_uom_id);
          $('#'+txt_id6).val(item_soqty);
          $('#'+txt_id7).val(item_blqty);
          $('#'+txt_id8).val(item_qty);
          $('#'+txt_id9).val(item_sqid);
          $('#'+txt_id10).val(item_seid); 
          $('#'+txt_id11).val(item_matid);
          $('#'+txt_id12).val(item_soqty);
          $('#'+txt_id2).parent().parent().find('[id*="MAINTROWID"]'). val(item_unique_row_id);

          $('#'+txt_id2).parent().parent().find('[id*="Alpspartno"]').val(apartno);
          $('#'+txt_id2).parent().parent().find('[id*="Custpartno"]').val(cpartno);
          $('#'+txt_id2).parent().parent().find('[id*="OEMpartno"]').val(opartno);

          if(item_type =="SFG" && $("#Direct").is(":checked") == false){
            $('#'+txt_id8).prop('readonly', true); 
          }
          else{
            $('#'+txt_id8).prop('readonly', false); 
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

         
          event.preventDefault();
        }

        $('.js-selectall').prop("checked", false);
        $('#ITEMIDpopup').hide();
        event.preventDefault();            
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

    AllStatus="1";

    $("#AllStatus").val(AllStatus);

    in_all_case_change_production_qty();
    $('#ITEMIDpopup').hide();
    return false;
    event.preventDefault();
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
    var item_soqty          =   $("#txt"+fieldid+"").data("desc12");
    var item_type           =   $("#txt"+fieldid+"").data("desc13");
    var item_blqty          =   $("#txt"+fieldid+"").data("desc14");
    var item_matid          =   $("#txt"+fieldid+"").data("desc15");

    var apartno =  $("#addinfo"+fieldid+"").data("desc101");
    var cpartno =  $("#addinfo"+fieldid+"").data("desc102");
    var opartno =  $("#addinfo"+fieldid+"").data("desc103");

    if($(this).is(":checked") == true){

      $('#example2').find('.participantRow').each(function(){

        var SLID_REF    =   $(this).find('[id*="SLID_REF"]').val();
        var SOID_REF    =   $(this).find('[id*="SOID_REF"]').val();
        var ITEMID_REF  =   $(this).find('[id*="ITEMID_REF"]').val();
        var SQID_REF    =   $(this).find('[id*="SQID_REF"]').val();
        var SEID_REF    =   $(this).find('[id*="SEID_REF"]').val();
        var exist_val   =   SLID_REF+"_"+SOID_REF+"_"+SQID_REF+"_"+SEID_REF+"_"+ITEMID_REF;

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
            item_soqty           =   '';
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
        $clone.find('[id*="BL_SOQTY"]').val(item_blqty);
        $clone.find('[id*="PD_OR_QTY"]').val(item_qty);
        $clone.find('[id*="MAINTROWID"]').val(item_unique_row_id); 
        $clone.find('[id*="SQID_REF"]').val(item_sqid);
        $clone.find('[id*="SEID_REF"]').val(item_seid);
        $clone.find('[id*="BOMID_REF"]').val(item_matid);
        $clone.find('[id*="BOMDATA"]').val(item_soqty);

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
          $('#'+txt_id7).val(item_blqty);
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
          $('#'+txt_id11).val(item_matid);
        }
        if($.trim(txt_id12)!=""){
          $('#'+txt_id12).val(item_soqty);
        }

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

    AllStatus="";
    $("#AllStatus").val(AllStatus);

    get_materital_item();
    if($("#Direct").is(":checked") == true){
      getItemDetailsLevel2(item_id);
    }
    
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
		get_materital_item();
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

function SUBITEMNameFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("SUBITEMnamesearch");
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

$('#Material').on('click','[id*="txtSUBITEM_popup"]',function(event){

  $('#hdn_SUBITEMid1').val($(this).attr('id'));
  $('#hdn_SUBITEMid2').val($(this).parent().parent().find('[id*="SUBITEM_NAME"]').attr('id'));
  $('#hdn_SUBITEMid3').val($(this).parent().parent().find('[id*="REQ_ITEMID_REF"]').attr('id'));
  $('#hdn_SUBITEMid4').val($(this).parent().parent().find('[id*="REQ_MAIN_ITEMID_REF"]').attr('id'));
  $('#hdn_SUBITEMid5').val($(this).parent().parent().find('[id*="REQ_BOM_QTY"]').attr('id'));
  $('#hdn_SUBITEMid6').val($(this).parent().parent().find('[id*="REQ_INPUT_PD_OR_QTY"]').attr('id'));
  $('#hdn_SUBITEMid7').val($(this).parent().parent().find('[id*="REQ_CHANGES_PD_OR_QTY"]').attr('id'));

  var REQ_BOMID_REF       =  $(this).parent().parent().find('[id*="REQ_BOMID_REF"]').val();
  var REQ_ITEMID_REF      =  $(this).parent().parent().find('[id*="REQ_ITEMID_REF"]').val();
  var REQ_MAIN_ITEMID_REF =  $(this).parent().parent().find('[id*="REQ_MAIN_ITEMID_REF"]').val();
  var MAIN_PD_OR_QTY      =  $(this).parent().parent().find('[id*="MAIN_PD_OR_QTY"]').val();

  var REQ_ITEMID          =  REQ_MAIN_ITEMID_REF !=""?REQ_MAIN_ITEMID_REF:REQ_ITEMID_REF;

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
      data:{'REQ_BOMID_REF':REQ_BOMID_REF,REQ_ITEMID:REQ_ITEMID,MAIN_PD_OR_QTY:MAIN_PD_OR_QTY},
      success:function(data) {
        $("#tbody_SUBITEM").html(data);
        BindSUBITEM();
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
  $(".clssSUBITEMid").click(function(){
    var fieldid   =   $(this).attr('id');

    var texdesc1   =   $("#txt"+fieldid+"").data("desc1");
    var texdesc2   =   $("#txt"+fieldid+"").data("desc2");
    var texdesc3   =   $("#txt"+fieldid+"").data("desc3");
    var texdesc4   =   $("#txt"+fieldid+"").data("desc4");
    var texdesc5   =   $("#txt"+fieldid+"").data("desc5");
    var texdesc6   =   $("#txt"+fieldid+"").data("desc6");

    var txt_id1= $('#hdn_SUBITEMid1').val();
    var txt_id2= $('#hdn_SUBITEMid2').val();
    var txt_id3= $('#hdn_SUBITEMid3').val();
    var txt_id4= $('#hdn_SUBITEMid4').val();
    var txt_id5= $('#hdn_SUBITEMid5').val();
    var txt_id6= $('#hdn_SUBITEMid6').val();
    var txt_id7= $('#hdn_SUBITEMid7').val();

    $('#'+txt_id1).val(texdesc1);
    $('#'+txt_id2).val(texdesc2);
    $('#'+txt_id3).val(texdesc3);
    $('#'+txt_id4).val(texdesc4);
    $('#'+txt_id5).val(texdesc5);
    $('#'+txt_id6).val(texdesc6);
    $('#'+txt_id7).val(texdesc6);

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
    var SLID_REF    = $(this).find('[id*="SLID_REF"]').val();
    var SOID_REF    = $(this).find('[id*="SOID_REF"]').val();
    var ITEMID_REF  = $(this).find('[id*="ITEMID_REF"]').val();
    var ITEMID_CODE = $(this).find('[id*="popupITEMID"]').val();
    var PD_OR_QTY   = $(this).find('[id*="PD_OR_QTY"]').val();
    var SQID_REF    = $(this).find('[id*="SQID_REF"]').val();
    var SEID_REF    = $(this).find('[id*="SEID_REF"]').val();

    item_array.push(SLID_REF+'_'+SOID_REF+'_'+ITEMID_REF+'_'+ITEMID_CODE+'_'+PD_OR_QTY+'_'+SQID_REF+'_'+SEID_REF);
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
        item_array:item_array,AllStatus:AllStatus
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

  /*
  var field_id  =   id.split("_")[3];
  var PD_OR_QTY =   parseFloat(value).toFixed(3);
  var qty_val   =   $("#"+id).parent().parent().find('[id*="BL_SOQTY_"]').val();

  if(isNaN(value) || $.trim(value)==""){
    value = 0;
  }

  var mainitem_id  = $("#"+id+"").parent().parent().find('[id*="MAINTROWID"]').val();
 
  if(parseFloat(PD_OR_QTY) > parseFloat(qty_val) && $("#Direct").is(":checked") == false){
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

      var new_po_qty = 0;
      var REQ_BOM_QTY = $.trim( $(this).children().find('[id*="REQ_BOM_QTY_"]').val() );

      if(isNaN(REQ_BOM_QTY) || REQ_BOM_QTY==""){
        REQ_BOM_QTY = 0;
        new_po_qty  = parseFloat( parseFloat(value) * parseFloat(REQ_BOM_QTY) ).toFixed(3);
      }else{
        new_po_qty =parseFloat( parseFloat(value) * parseFloat(REQ_BOM_QTY) ).toFixed(3);          
      }

      $(this).children().find('[id*="REQ_INPUT_PD_OR_QTY_"]').val(new_po_qty);
      $(this).children().find('[id*="REQ_CHANGES_PD_OR_QTY_"]').val(new_po_qty);
      $(this).children().find('[id*="MAIN_PD_OR_QTY_"]').val(value);
      
    });

    get_direct_qty_cal(field_id);
    in_all_case_change_production_qty();
    
  }
  */
  get_materital_item();

}

function get_direct_qty_cal(field_id){
  if($("#Direct").is(":checked") == true){

    var MAIN_ITEM_CODE  = $("#popupITEMID_"+field_id).val();
    var aIds            = [];

    $('#example4').find('.participantRow4').each(function(){

      var txtMAIN_ITEMCODE      = $(this).find('[id*="txtMAIN_ITEMCODE"]').val();
      var txtSUBITEM_popup      = $(this).find('[id*="txtSUBITEM_popup"]').val();
      var REQ_CHANGES_PD_OR_QTY = $(this).find('[id*="REQ_CHANGES_PD_OR_QTY"]').val();

      if(txtMAIN_ITEMCODE ==  MAIN_ITEM_CODE){
        aIds.push(txtSUBITEM_popup+"_"+REQ_CHANGES_PD_OR_QTY);
      }
      
    });


    $.each( aIds, function( key, value ) {
      
      var itemcode  =   value.split("_")[0];
      var itemqty   =   value.split("_")[1];


      $('#example4').find('.participantRow4').each(function(){

        var txtMAIN_ITEMCODE  =  $(this).find('[id*="txtMAIN_ITEMCODE"]').val();
        var REQ_BOM_QTY       =  $(this).find('[id*="REQ_BOM_QTY"]').val();

        if(txtMAIN_ITEMCODE ==  itemcode){
          var new_chang_qty  = parseFloat( parseFloat(REQ_BOM_QTY) * parseFloat(itemqty) ).toFixed(3);

          $(this).find('[id*="REQ_INPUT_PD_OR_QTY_"]').val(new_chang_qty);
          $(this).find('[id*="REQ_CHANGES_PD_OR_QTY_"]').val(new_chang_qty);
        
        }

      });

    });

  }
}


function change_production_qty(id,value){
  //console.log(id,value);
}



/*================================== ONLOAD FUNCTION ==================================*/
$(document).ready(function(e) {
  var Material = $("#Material").html(); 
  $('#hdnmaterial').val(Material);
  var lastdt = <?php echo json_encode($objlastdt[0]->PRO_DT); ?>;
  var today = new Date(); 
  var prodate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;

  $('#PRO_DT').attr('min',lastdt);
  $('#PRO_DT').attr('max',prodate);
  

  var d = new Date(); 
  var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
  $('#PRO_DT').val(today);

});



/*================================== UDF FUNCTION ==================================*/

var udfdata = <?php echo json_encode($objUdfData); ?>;
var count2  = <?php echo json_encode($objCountUDF); ?>;

$('#Row_Count2').val(count2);
$('#example3').find('.participantRow3').each(function(){

  var txt_id4 = $(this).find('[id*="udfinputid"]').attr('id');
  var udfid   = $(this).find('[id*="UDF"]').val();

  $.each( udfdata, function( seukey, seuvalue ) {
    if(seuvalue.UDFPROID == udfid){

      var txtvaltype2 = seuvalue.VALUETYPE;
      var strdyn2     = txt_id4.split('_');
      var lastele2    = strdyn2[strdyn2.length-1];
      var dynamicid2  = "udfvalue_"+lastele2;
          
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

$('#Material').on('blur',"[id*='PD_OR_QTY']",function()
{
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



function direct(){
  if($("#Direct").is(":checked") == true){
    $("[id*='txtSL_popup']").prop('disabled', true);
    $("[id*='txtSO_popup']").prop('disabled', true);
  }
  else{
    $("[id*='txtSL_popup']").prop('disabled', false);
    $("[id*='txtSO_popup']").prop('disabled', false);
  } 

  $('#example2').find('.participantRow').each(function(){
    var rowcount = $(this).closest('table').find('.participantRow').length;
    $(this).find('input:text').val('');
    $(this).find('input:hidden').val('');
    if(rowcount > 1){
      $(this).closest('.participantRow').remove();
      rowcount = parseInt(rowcount) - 1;
      $('#Row_Count1').val(rowcount);
    }
  });

  $("#material_item").html('');

}


function in_all_case_change_production_qty(){

  if($("#Direct").is(":checked") == false && AllStatus =="1"){
    var rowcount=1;
    var value='';
    $('#example2').find('.participantRow').each(function(){

      if(rowcount == 1){
        value = parseFloat($(this).find('[id*="PD_OR_QTY_"]').val()).toFixed(3);
      }

      if(rowcount > 1){
        
        var BOMDATA   = parseFloat($(this).find('[id*="BOMDATA_"]').val()).toFixed(3);
        var NEW_QTY   = (BOMDATA * parseFloat(value).toFixed(3));

        $(this).find('[id*="QTY_"]').val(NEW_QTY);
        $(this).find('[id*="BL_SOQTY_"]').val(NEW_QTY);
        $(this).find('[id*="PD_OR_QTY_"]').val(NEW_QTY);
        
      }

      rowcount++;
    });

    get_materital_item();
    
  }

}

function getItemDetailsLevel2(ITEMID_REF){

  /*
  $("#tbody_ItemID2").html('loading...');
  $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $.ajax({
    url:'{{route("transaction",[$FormId,"getItemDetailsLevel2"])}}',
    type:'POST',
    data:{ITEMID_REF:ITEMID_REF},
    success:function(data) {
      $("#tbody_ItemID2").html(data); 
      $("#select_all").prop('checked', false);   
      $("#ITEMIDpopup2").show();               
    },
    error:function(data){
      console.log("Error: Something went wrong.");
      $("#tbody_ItemID2").html('');                        
    },
  });  
  */
}

function getSubItemId(){

  var all_location_id = document.querySelectorAll('input[name="selectAll[]"]:checked');
  var aIds = [];
  for(var x = 0, l = all_location_id.length; x < l;  x++){
    aIds.push(all_location_id[x].value);
  }

  $('.checkboxClass').change(function(){

    if(false == $(this).prop("checked")){ 
      $("#select_all").prop('checked', false); 
    }
  
    if($('.checkboxClass:checked').length == $('.checkboxClass').length ){
      $("#select_all").prop('checked', true);
    }

  });

  get_materital_item2(aIds); 
}

function get_materital_item2(aIds){

  var  item_array   = [];
  $('#example2').find('.participantRow').each(function(){
    var SLID_REF    = $(this).find('[id*="SLID_REF"]').val();
    var SOID_REF    = $(this).find('[id*="SOID_REF"]').val();
    var ITEMID_REF  = $(this).find('[id*="ITEMID_REF"]').val();
    var ITEMID_CODE = $(this).find('[id*="popupITEMID"]').val();
    var PD_OR_QTY   = $(this).find('[id*="PD_OR_QTY"]').val();
    var SQID_REF    = $(this).find('[id*="SQID_REF"]').val();
    var SEID_REF    = $(this).find('[id*="SEID_REF"]').val();

    item_array.push(SLID_REF+'_'+SOID_REF+'_'+ITEMID_REF+'_'+ITEMID_CODE+'_'+PD_OR_QTY+'_'+SQID_REF+'_'+SEID_REF);
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
      data:{item_array:item_array,AllStatus:AllStatus,aIds:aIds},
      success:function(data) {
        $("#material_item").html(data);                
      },
      error:function(data){
        console.log("Error: Something went wrong.");
        $("#material_item").html('');                        
      },
  }); 

}

$("#ITEMID_closePopup2").click(function(event){
  $("#ITEMIDpopup2").hide();
});

$("#select_all").change(function(){
  $(".checkboxClass").prop('checked', $(this).prop("checked"));
  getSubItemId();
});

let itemtidL2 = "#ItemIDTable22";
let itemtid22 = "#ItemIDTable2";
let itemtidL2headers = document.querySelectorAll(itemtid22 + " th");

itemtidL2headers.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(itemtidL2, ".clsitemid", "td:nth-child(" + (i + 1) + ")");
  });
});

function ItemCodeFunction2(){
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("Itemcodesearch2");
  filter = input.value.toUpperCase();
  table = document.getElementById("ItemIDTable22");
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

function ItemNameFunction2() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("Itemnamesearch2");
  filter = input.value.toUpperCase();
  table = document.getElementById("ItemIDTable22");
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

function ItemUOMFunction2(){
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("ItemUOMsearch2");
  filter = input.value.toUpperCase();  
  table = document.getElementById("ItemIDTable22");
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

function ItemQTYFunction2() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("ItemQTYsearch2");
  filter = input.value.toUpperCase();
  table = document.getElementById("ItemIDTable22");
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

function ItemGroupFunction2() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("ItemGroupsearch2");
  filter = input.value.toUpperCase();
  table = document.getElementById("ItemIDTable22");
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

function ItemCategoryFunction2(){
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("ItemCategorysearch2");
  filter = input.value.toUpperCase();
  table = document.getElementById("ItemIDTable22");
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

function ItemBUFunction2() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("ItemBUsearch2");
  filter = input.value.toUpperCase();
  table = document.getElementById("ItemIDTable22");
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

function ItemAPNFunction2() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("ItemAPNsearch2");
  filter = input.value.toUpperCase();
  table = document.getElementById("ItemIDTable22");
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

function ItemCPNFunction2() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("ItemCPNsearch2");
  filter = input.value.toUpperCase();
  table = document.getElementById("ItemIDTable22");
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

function ItemOEMPNFunction2() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("ItemOEMPNsearch2");
  filter = input.value.toUpperCase();
  table = document.getElementById("ItemIDTable22");
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

function ItemStatusFunction2() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("ItemStatussearch2");
  filter = input.value.toUpperCase();
  table = document.getElementById("ItemIDTable22");
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
</script>
@endpush
