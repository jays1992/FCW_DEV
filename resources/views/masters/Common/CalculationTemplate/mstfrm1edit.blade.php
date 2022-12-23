
@extends('layouts.app')
@section('content')
<!-- <form id="frm_mst_se" onsubmit="return validateForm()"  method="POST"  >     -->
<form id="frm_mst_calculation"  method="POST">  
    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[1,'index'])}}" class="btn singlebt">Calculation Template</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
                        <button class="btn topnavbt" id="btnSaveSE" ><i class="fa fa-save"></i> Save</button>
                        <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt" id="btnUndo"  ><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove" {{($objRights->APPROVAL1||$objRights->APPROVAL2||$objRights->APPROVAL3||$objRights->APPROVAL4||$objRights->APPROVAL5) == 1 ? '' : 'disabled'}}><i class="fa fa-lock"></i> Approved</button>
                        <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                        <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                        
                </div>
            </div><!--row-->
    </div><!--topnav-->	
    <!-- multiple table-responsive table-wrapper-scroll-y my-custom-scrollbar -->
    <div class="container-fluid purchase-order-view filter">   
      
    
    @CSRF
          {{isset($objCalculation->CTID) ? method_field('PUT') : '' }}
                <div class="inner-form">
                    
                    <div class="row">
                        <div class="col-lg-2 pl"><p>Calculation Template Code</p></div>
                        <div class="col-lg-2 pl">
                            <div class="col-lg-12 pl">
                                <input type="text" name="CTCODE" id="txtctcode" value="{{ $objCalculation->CTCODE }}"  tabindex="1" class="form-control "    readonly style="text-transform:uppercase"  >
                            </div>
                        </div>

                        <div class="col-lg-2 pl"><p>Module</p></div>
                        <div class="col-lg-2 pl">
                            <input type="text" name="MODULE_Details" id="MODULE_Details" value="{{isset($objCalculation->MODULE_NAME)?$objCalculation->MODULE_NAME:''}}" class="form-control mandatory"  autocomplete="off" readonly/>
                            <input type="hidden" name="MODULE" id="MODULE" value="{{isset($objCalculation->MODULEID_REF)?$objCalculation->MODULEID_REF:''}}" class="form-control" autocomplete="off" />
                            <span class="text-danger" id="ERROR_MODULE"></span> 
                        </div>

                        <div class="col-lg-2 pl"><p>TYPE</p></div>
                        <div class="col-lg-2 pl">
                            <select name="TYPE" id="TYPE" class="form-control"  autocomplete="off" >
                              <option {{isset($objCalculation->TYPE) && $objCalculation->TYPE ==='OTHER'?'selected="selected"':''}} value='OTHER'>OTHER</option>
                              <option {{isset($objCalculation->TYPE) && $objCalculation->TYPE ==='DISCOUNT'?'selected="selected"':''}} value='DISCOUNT'>DISCOUNT</option>
                            </select>
                            <span class="text-danger" id="ERROR_MODULE"></span> 
                        </div>

                    </div>
                    
                    <div class="row">
                        <div class="col-lg-2 pl"><p>Calculation Template Description</p></div>
                        <div class="col-lg-5 pl">
                            <input type="text" name="CTDESCRIPTION" id="txtctdesc" value="{{ $objCalculation->CTDESCRIPTION }}" autocomplete="off" tabindex="2" class="form-control"  maxlength="200"     >
                            
                        </div>
                    </div>	
                            
                    <div class="row">
                        <div class="col-lg-2 pl"><p>De-Activated</p></div>
                        <div class="col-lg-1 pl">
                            <input type="checkbox" name="DEACTIVATED" id="deactive"  tabindex="3" {{$objCalculation->DEACTIVATED == 1 ? 'checked' : ''}} >
                        </div>
                        
                        <div class="col-lg-2 pl col-md-offset-1"><p>Date of De-Activated</p></div>
                        <div class="col-lg-2 pl">
                        <div class="col-lg-8 pl">
                        <input type="date" name="DODEACTIVATED" id="decativateddate" value="{{ $objCalculation->DODEACTIVATED }}" tabindex="4"  class="form-control datepicker" placeholder="dd/mm/yyyy" disabled>
                        </div>
                        </div>
                    </div>
                    
                    
                    <div class="row">
                        <div class="table-responsive table-wrapper-scroll-y " style="height:330px;margin-top:10px;">
                        <table id="example2" class="display nowrap table table-striped table-bordered itemlist dataTable" style="height:auto !important;">
                            <thead id="thead1"   style="position: sticky;top: 0; white-space:none;">
                                <tr>
                                    <th style="vertical-align:middle; font-family: 'Roboto',sans-serif; font-size:11px; font-weight: 600;">Calculation Component Name <input class="form-control" type="hidden" name="Row_Count" id ="Row_Count" value="{{$objCount}}">  </th>
                                    <th width="5%" style="vertical-align:middle; font-family: 'Roboto',sans-serif; font-size:11px; font-weight: 600;">SQ No</th>
                                    <th style="vertical-align:middle; font-family: 'Roboto',sans-serif; font-size:11px; font-weight: 600;">Basis</th>
                                    <th style="vertical-align:middle; font-family: 'Roboto',sans-serif; font-size:11px; font-weight: 600;">GL</th>
                                    <th width="5%" style="vertical-align:middle; font-family: 'Roboto',sans-serif; font-size:11px; font-weight: 600;">Formula</th>
                                    <th width="5%" style="vertical-align:middle; font-family: 'Roboto',sans-serif; font-size:11px; font-weight: 600;">Rate %</th>
                                    <th style="vertical-align:middle; font-family: 'Roboto',sans-serif; font-size:11px; font-weight: 600;">Formula</th>
                                    <th width="5%">Amount</th>
                                    <th style="vertical-align:middle; font-family: 'Roboto',sans-serif; font-size:11px; font-weight: 600;">GST Calc on</th>
                                    <th style="vertical-align:middle; font-family: 'Roboto',sans-serif; font-size:11px; font-weight: 600;">As per Actual</th>
                                    <th style="vertical-align:middle; font-family: 'Roboto',sans-serif; font-size:11px; font-weight: 600;">Landed Cost Included</th>
                                    <th width="5%" style="vertical-align:middle; font-family: 'Roboto',sans-serif; font-size:11px; font-weight: 600;">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(!empty($objCalculationtemp))
                                    @foreach($objCalculationtemp as $key => $row)
                                        <tr  class="participantRow">
                                            <td hidden>
                                            <input  class="form-control" type="hidden" name={{"TID_".$key}} id ={{"txtID_".$key}} maxlength="100" value="{{ $row->TID }}" autocomplete="off"   >
                                            </td>
                                            <td style="width:10%;"><input  class="form-control" type="text" name={{"COMPONENT_".$key}} id={{"txtcmpt_".$key}} maxlength="200"  value="{{ $row->COMPONENT }}" style="text-transform:uppercase" autocomplete="off" ></td>
                                            <td style="width:1%;"><input  class="form-control" type="text" name={{"SQNO_".$key}} id={{"txtsqno_".$key}} maxlength="4"  value="{{ $row->SQNO }}" readonly  ></td>
                                            <td style="width:15%;">
                                            <input name={{"BASIS_popup_".$key}} id={{"txtbasis_popup_".$key}} class="form-control selvt" autocomplete="off" readonly/>
                                            </td>
                                            <td style="width:15%;" hidden>
                                            <input type="hidden" name={{"BASIS_".$key}} id={{"hdnbasis_popup_".$key}} class="form-control" autocomplete="off" />
                                            </td>
                                            <td style="width:15%;">
                                            <input type="text" name={{"GLID_popup_".$key}} id={{"txtgl_popup_".$key}} class="form-control"  autocomplete="off"readonly/>
                                            </td>
                                            <td style="width:15%;" hidden>
                                            <input type="hidden" name={{"GLID_REF_".$key}} id={{"hdngl_popup_".$key}} class="form-control" autocomplete="off" />
                                            </td>   
                                            <td  style="text-align:center;" ><input type="checkbox" name={{"FORMULAYESNO_".$key}} id={{"chkfrm_".$key}} {{$row->FORMULAYESNO == 1 ? 'checked' : ''}}   style="float: revert;"  ></td>
                                            <td><input  class="form-control four-digits" type="text" name={{"RATEPERCENTATE_".$key}} id={{"txtprct_".$key}} maxlength="9" value="{{ $row->RATEPERCENTATE }}" autocomplete="off" style="text-align: right;"></td>
                                            <td style="width:15%;"><input  class="form-control" type="text" name={{"FORMULA_".$key}} id={{"txtfrm_".$key}} maxlength="200" value="{{ $row->FORMULA }}" autocomplete="off" disabled="disabled" ></td>
                                            <td style="width:10%;"><input  class="form-control two-digits" type="text" name={{"AMOUNT_".$key}} id={{"txtamt_".$key}} value="{{ $row->AMOUNT }}" autocomplete="off" style="text-align: right;"  ></td>
                                            <td style="text-align:center;" ><input type="checkbox" name={{"GST_".$key}} id={{"chkgst_".$key}}   style="float: revert;"  {{$row->GST == 1 ? 'checked' : ''}}></td>
                                            <td style="text-align:center;" ><input type="checkbox" name={{"ACTUAL_".$key}} id={{"chkact_".$key}}    style="float: revert;"  {{$row->ACTUAL == 1 ? 'checked' : ''}}></td>
                                            <td style="text-align:center;" ><input type="checkbox" name={{"LANDEDCOST_".$key}} id={{"chklndc_".$key}}    style="float: revert;"  {{$row->LANDEDCOST == 1 ? 'checked' : ''}}></td>
                                            <td align="center" ><button class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></button><button class="btn remove" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
                                        </tr>
                                        <tr></tr>
                                    @endforeach 
                                @endif 
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
        <!-- </form>  -->
</div><!--purchase-order-view-->
</form>   
<!-- </div> -->

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

<div id="MODULE_Modal" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='MODULE_Modal_Close' >&times;</button>
      </div>
      <div class="modal-body">
	      <div class="tablename"><p>Module</p></div>
	      <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
          <table id="MODULE_Table" class="display nowrap table  table-striped table-bordered">
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
                <td class="ROW2"><input type="text" id="MODULE_Code_Search" class="form-control" autocomplete="off" onkeyup="MODULE_Code_Function()"></td>
                <td class="ROW3"><input type="text" id="MODULE_Name_Search" class="form-control" autocomplete="off" onkeyup="MODULE_Name_Function()"></td>
              </tr>

            </tbody>
            </table>
            <table id="MODULE_Table2" class="display nowrap table  table-striped table-bordered" >
              <thead id="thead2">          
              </thead>
              <tbody id="MODULE_Body1" >
              <?php
              if(!empty($module)){
                foreach ($module as $key=>$val){
                  $checked="";
                  if(isset($objCalculation->MODULEID_REF) && $objCalculation->MODULEID_REF !=""){
                    $checked=   in_array($val-> MODULEID,explode(",",$objCalculation->MODULEID_REF))?"checked":'';
                  }
              ?>
              <tr id="MODULE_TDID_<?php echo $key;?>" class="MODULE_Row">
                  <td class="ROW1" ><input <?php echo $checked;?> type="checkbox" class="MODULE_CHECK" id="txtMODULE_CHECK_<?php echo $key;?>" value="<?php echo $val-> MODULEID;?>"></td>
                  <td class="ROW2" ><?php echo $val-> MODULECODE;?>
                  <input type="hidden" id="txtMODULE_TDID_<?php echo $key;?>" data-desc="<?php echo $val-> MODULECODE;?>"  value="<?php echo $val-> MODULEID;?>"/>
                  </td>
                  <td class="ROW3"><?php echo $val-> MODULENAME;?></td>
              </tr>
              <?php
              }
            }
            ?>

              </tbody>
            </table>
          </div>
		    <div class="cl"></div>
      </div>
    </div>
  </div>
</div>


<!-- GLID Dropdown -->
<div id="glidpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='gl_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>GL Account</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="example2345" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
    <tr>
      <th class="ROW1" style="width: 10%" align="center">Select</th> 
      <th class="ROW2" style="width: 40%" >GLCode</th>
      <th class="ROW3" style="width: 40%" >GLName</th>
    </tr>
    </thead>
    <tbody>
    {{-- <tr>
    <td>
    <input type="text" id="glcodesearch" onkeyup="myFunction()">
    </td>
    <td>
    <input type="text" id="glnamesearch" onkeyup="myNameFunction()">
    </td>
    </tr> --}}
    <tr>
      <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
      <td class="ROW2"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="glcodesearch" onkeyup="myFunction()" /></td>
      <td class="ROW3"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="glnamesearch" onkeyup="myNameFunction()"/></td>
    </tr>
    </tbody>
    </table>
      <table id="example23" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          <!-- <tr>
            <th>GLCode</th>
            <th>GLName</th>
          </tr> -->
          <tr id="none-select" class="searchalldata" hidden>
            
            <td> <input type="hidden" name="fieldid" id="hdn_fieldid"/>
            <input type="hidden" name="fieldid2" id="hdn_fieldid2"/></td>
          </tr>
        </thead>
        <tbody>
        @foreach ($objglcode as $index=>$glRow)
        {{-- <tr id="glidcode_{{ $index }}" class="clsglid">
          <td width="50%">{{ $glRow-> GLCODE }}
          <input type="hidden" id="txtglidcode_{{ $index }}" data-desc="{{ $glRow-> GLCODE }}"  value="{{ $glRow-> GLID }}"/>
          </td>
          <td width="50%">{{ $glRow-> GLNAME }}</td>
        </tr> --}}
        <tr >
          <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_GLID_REF[]"  id="glidcode_{{ $index }}" class="clsglid" value="{{ $index  }}" /></td>
          <td class="ROW2" style="width: 39%">{{ $glRow-> GLCODE }}
          <input type="hidden" id="txtglidcode_{{ $index }}" data-desc="{{ $glRow-> GLCODE }}"  value="{{ $glRow->GLID }}"/>
          </td>
          <td class="ROW3" style="width: 39%">{{ $glRow-> GLNAME }}</td>
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
<!-- GL Dropdown-->

<!-- Basis Dropdown -->
<div id="basispopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='basis_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Basis</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="basisexample2345" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
    {{-- <tr>
            <th width="50%"><p>Code</p></th>
            <th width="50%"><p>Desc</p></th>
    </tr> --}}
    <tr>
      <th class="ROW1" style="width: 10%" align="center">Select</th> 
      <th class="ROW2" style="width: 40%" >Code</th>
      <th class="ROW3" style="width: 40%" >Desc</th>
    </tr>
    </thead>
    <tbody>
    {{-- <tr>
    <td width="50%">
    <input type="text" id="bscodesearch" onkeyup="mybasisFunction()">
    </td>
    <td width="50%">
    <input type="text" id="bsnamesearch" onkeyup="mybasisNameFunction()">
    </td>
    </tr> --}}
    <tr>
      <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
      <td class="ROW2"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="bscodesearch" onkeyup="mybasisFunction()" /></td>
      <td class="ROW3"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="bsnamesearch" onkeyup="mybasisNameFunction()" /></td>
    </tr>
    </tbody>
    </table>
      <table id="basisexample23" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          <!-- <tr>
            <th>GLCode</th>
            <th>GLName</th>
          </tr> -->
          <tr id="none-select" class="searchalldata" hidden>
            
            <td> <input type="hidden" name="fieldid" id="hdn_basisfieldid"/>
            <input type="hidden" name="fieldid2" id="hdn_basisfieldid2"/></td>
          </tr>
        </thead>
        <tbody>
        {{-- <tr id="basis_0" class="clsbasisid">
          <td width="50%" > A
          <input type="hidden" id="txtbasis_0" data-desc="Item Taxable Amount"  value="Item Taxable Amount"/>
          </td>
          <td width="50%">Item Taxable Amount</td>
        </tr>
        <tr id="basis_1" class="clsbasisid">
          <td  width="50%"> B
          <input type="hidden" id="txtbasis_1" data-desc="Item GST Amount"  value="Item GST Amount"/>
          </td>
          <td width="50%">Item GST Amount</td>
        </tr>
        <tr id="basis_2" class="clsbasisid">
          <td width="50%"> C
          <input type="hidden" id="txtbasis_2" data-desc="Amount After GST Item"  value="Amount After GST Item"/>
          </td>
          <td width="50%">Amount After GST Item</td>
        </tr>
        <tr id="basis_3" class="clsbasisid">
          <td width="50%"> D
          <input type="hidden" id="txtbasis_3" data-desc="Individual"  value="Individual"/>
          </td>
          <td width="50%">Individual</td>
        </tr> --}}
        <tr >
          <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_BASICID_REF[]"  id="basis_0" class="clsbasisid" value="0" /></td>
          <td class="ROW2" style="width: 39%"> A
              <input type="hidden" id="txtbasis_0" data-desc="Item Taxable Amount"  value="Item Taxable Amount"/>
          </td>
          <td class="ROW3" style="width: 39%">Item Taxable Amount</td>
        </tr>
        <tr >
          <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_BASICID_REF[]"  id="basis_1" class="clsbasisid" value="1" /></td>
          <td class="ROW2" style="width: 39%"> B
              <input type="hidden" id="txtbasis_1" data-desc="Item GST Amount"  value="Item GST Amount"/>
          </td>
          <td class="ROW3" style="width: 39%">Item GST Amount</td>
        </tr>
        <tr >
          <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_BASICID_REF[]"  id="basis_2" class="clsbasisid" value="2" /></td>
          <td class="ROW2" style="width: 39%"> C
              <input type="hidden" id="txtbasis_2" data-desc="Amount After GST Item"  value="Amount After GST Item"/>
          </td>
          <td class="ROW3" style="width: 39%">Amount After GST Item</td>
        </tr>
        <tr >
          <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_BASICID_REF[]"  id="basis_3" class="clsbasisid" value="3" /></td>
          <td class="ROW2" style="width: 39%"> D
              <input type="hidden" id="txtbasis_3" data-desc="Individual"  value="Individual"/>
          </td>
          <td class="ROW3" style="width: 39%">Individual</td>
        </tr>
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Basis Dropdown-->
@endsection

@push('bottom-css')
<style>
* {
  box-sizing: border-box;
}
/*
#glcodesearch {
  background-image: url('/css/searchicon.png');
  background-position: 10px 10px;
  background-repeat: no-repeat;
  font-size: 11px;
  padding: 5px 5px 5px 5px;
  border: 1px solid #ddd;
  margin-bottom: 12px;
}
#glnamesearch {
  background-image: url('/css/searchicon.png');
  background-position: 10px 10px;
  background-repeat: no-repeat;
  font-size: 11px;
  padding: 5px 5px 5px 5px;
  border: 1px solid #ddd;
  margin-bottom: 12px;
}

#example23 {
  border-collapse: collapse;
 
  border: 1px solid #ddd;
  font-size: 11px;5
}

#example23 th{
  text-align: left;
  padding: 5px;
  width: 150px;
  color: #0f69cc;
  background: #bbe7fc;
}

#example23 td {
  text-align: left;
  padding: 5px;
  width: 150px;
}

#example23 tr {
  border-bottom: 1px solid #ddd;
}

#example23 tr.header, #example23 tr:hover {
  background-color: #f1f1f1;
}

#example2345 {
  border-collapse: collapse;

  border: 1px solid #ddd;
  font-size: 11px;
}

#example2345 th {
  text-align: left;
  padding: 5px;
  width: 150px;
  color: #0f69cc;
  background: #bbe7fc;
}

#example2345 td {
  text-align: left;
  padding: 5px;
  width: 150px;
}

#example2345 tr {
  border-bottom: 1px solid #ddd;
}

#example2345 tr.header, #example2345 tr:hover {
  background-color: #f1f1f1;
}
#bscodesearch {
  background-image: url('/css/searchicon.png');
  background-position: 10px 10px;
  background-repeat: no-repeat;
  font-size: 11px;
  padding: 5px 5px 5px 5px;
  border: 1px solid #ddd;
  margin-bottom: 5px;
}
#bsnamesearch {
  background-image: url('/css/searchicon.png');
  background-position: 10px 10px;
  background-repeat: no-repeat;
  font-size: 11px;
  padding: 5px 5px 5px 5px;
  border: 1px solid #ddd;
  margin-bottom: 5px;
}

#basisexample23 {
  border-collapse: collapse;

  border: 1px solid #ddd;
  font-size: 11px;
}

#basisexample23 th {
  text-align: left;
  padding: 5px;
  width: 150px;
  color: #0f69cc;
  background: #bbe7fc;
}

#basisexample23 td {
  text-align: left;
  padding: 5px;
  width: 150px;
}

#basisexample23 tr {
  border-bottom: 1px solid #ddd;
}

#basisexample23 tr.header, #basisexample23 tr:hover {
  background-color: #f1f1f1;
}

#basisexample2345 {
  border-collapse: collapse;

  border: 1px solid #ddd;
  font-size: 11px;
}

#basisexample2345 th{
  text-align: left;
  padding: 5px;
  width: 150px;
  color: #0f69cc;
  background: #bbe7fc;
}

#basisexample2345 td {
  text-align: left;
  padding: 5px;
  width: 150px;
}

#basisexample2345 tr {
  border-bottom: 1px solid #ddd;
}

#basisexample2345 tr.header, #basisexample2345 tr:hover {
  background-color: #f1f1f1;
}*/
</style>
@endpush

@push('bottom-scripts')
<script>
 
//  var CalculationForm = $("#frm_mst_calculation");
//  CalculationForm.validate();
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


// START MODULE PROGRAM

let MODULE_tid = "#MODULE_Table2";
let MODULE_tid2 = "#MODULE_Table";
let MODULE_headers = document.querySelectorAll(MODULE_tid2 + " th");

MODULE_headers.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(MODULE_tid, ".MODULE_Row", "td:nth-child(" + (i + 1) + ")");
  });
});

function MODULE_Code_Function() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("MODULE_Code_Search");
  filter = input.value.toUpperCase();
  table = document.getElementById("MODULE_Table2");
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

function MODULE_Name_Function() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("MODULE_Name_Search");
      filter = input.value.toUpperCase();
      table = document.getElementById("MODULE_Table2");
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

$('#MODULE_Details').click(function(event){
  $("#MODULE_Modal").show();
  event.preventDefault();
});

$("#MODULE_Modal_Close").click(function(event){
  $("#MODULE_Modal").hide();
  event.preventDefault();
});


$(".MODULE_CHECK").change(function(){

  var txtval   = [];
  var texdesc  = [];
  
  $('#MODULE_Table2').find('.MODULE_Row').each(function(){
    var text_id     = $.trim($(this).find("[id*=txtMODULE_TDID]").val());
    var text_attr   = $.trim($(this).find("[id*=txtMODULE_TDID]").attr('id'));
    var text_check  = $.trim($(this).find("[id*=txtMODULE_CHECK]").attr('id'));
    var text_des    = $("#"+text_attr).data("desc");

    if($("#"+text_check).prop("checked") == true){
      txtval.push(text_id);
      texdesc.push(text_des);
    }
    
  });

  $('#MODULE_Details').val(texdesc);
  $('#MODULE').val(txtval);

  $("#MODULE_Code_Search").val(''); 
  $("#MODULE_Name_Search").val(''); 
  
  event.preventDefault();

});

// END MODULE PROGRAM


      let tid = "#basisexample23";
      let tid2 = "#basisexample2345";
      let headers = document.querySelectorAll(tid2 + " th");

      // Sort the table element when clicking on the table headers
      headers.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(tid, ".clsbasisid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      let tidgl = "#example23";
      let tidgl2 = "#example2345";
      let headersgl = document.querySelectorAll(tidgl2 + " th");

      // Sort the table element when clicking on the table headers
      headersgl.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(tidgl, ".clsglid", "td:nth-child(" + (i + 1) + ")");
        });
      });



     
$(document).ready(function(e) {
  

  $("[id*='txtprct']").ForceNumericOnly();
        $("[id*='txtamt']").ForceNumericOnly();

//delete row
var obj = <?php echo json_encode($objCalculationtemp); ?>;
var glcode = <?php echo json_encode($objglcode); ?>;
$.each( obj, function( key, value ) {
    $('#txtbasis_popup_'+key).val(value.BASIS);
    $('#hdnbasis_popup_'+key).val(value.BASIS);
    $('#hdngl_popup_'+key).val(value.GLID_REF);
    var formulayesno = value.FORMULAYESNO;
    var rate = value.RATEPERCENTATE;
    var glid = value.GLID_REF;
    var amt = value.AMOUNT;
    $.each( glcode, function( glkey, glvalue ) {
      if (glid ==glvalue.GLID)
      {
        $('#txtgl_popup_'+key).val(glvalue.GLCODE);
      }
    });
    if(rate > 0)
    {
        $('#txtamt_'+key).attr('disabled',true);
    }
    else{        
        $('#txtamt_'+key).removeAttr('disabled');
    }
    if(amt > 0)
    {
        $('#txtprct_'+key).attr('disabled',true);
    }
    else{        
        $('#txtprct_'+key).removeAttr('disabled');
    }
    if(formulayesno == "1" )
    {
        $('#txtfrm_'+key).removeAttr('disabled');
    }
    else{        
        $('#txtfrm_'+key).attr('disabled',true);
    }
});


// $('#Row_Count').val(rcount);


$('#btnAdd').on('click', function() {
      var viewURL = '{{route("master",[1,"add"])}}';
                  window.location.href=viewURL;
    });
$('#btnExit').on('click', function() {
      var viewURL = '{{route('home')}}';
                  window.location.href=viewURL;
    });
    

    $('#example2').on("focusout",'[id*="txtprct"]', function( event ) {
        if($(this).val() != '') {
            $(this).parent().parent().find('[id*="txtamt"]').prop('disabled', true);
            $(this).parent().parent().find('[id*="txtamt"]').val('');
            if(intRegex.test($(this).val())){
              $(this).val($(this).val()+'.0000');
            }
        } else {
            $('[id*="txtamt"]').removeAttr('disabled');
        }
    });
    $('#example2').on("focusout",'[id*="txtamt"]', function( event ) {
        if($(this).val() != '') {
            $(this).parent().parent().find('[id*="txtprct"]').prop('disabled', true);
            $(this).parent().parent().find('[id*="txtprct"]').val('');
            if(intRegex.test($(this).val())){
              $(this).val($(this).val()+'.00');
            }
        } else {
            $(this).parent().parent().find('[id*="txtprct"]').removeAttr('disabled');
        }
    });
    $('#example2').on("change",'[id*="chkfrm"]', function( event ) {
            if ($(this).is(':checked') == false) {
                $(this).parent().parent().find('[id*="txtfrm"]').attr('disabled',true);
                $(this).parent().parent().find('[id*="txtfrm"]').val('');
               
                event.preventDefault();
            }
            else
            {
                $(this).parent().parent().find('[id*="txtfrm"]').removeAttr('disabled');
                event.preventDefault();
            }
    });
    $('#decativateddate').change(function( event ) {
            var today = new Date();     //Mon Nov 25 2013 14:13:55 GMT+0530 (IST) 
            var d = new Date($(this).val()); 
            today.setHours(0, 0, 0, 0) ;
            d.setHours(0, 0, 0, 0) ;
            if (d < today) {
                $(this).val('');
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

    $('#deactive').change(function( event ) {
            if ($(this).is(':checked') == false) {
                $('#decativateddate').attr('disabled',true);
                $('#decativateddate').val('');
                event.preventDefault();
            }
            else
            {
              $('#decativateddate').removeAttr('disabled');
                event.preventDefault();
            }
        });

    $("#txtctcode").blur(function(){
      $(this).val($.trim( $(this).val() ));
      $("#ERROR_CTCODE").hide();
      validateSingleElemnet("txtctcode");
         
    });

    $( "#txtctcode" ).rules( "add", {
        required: true,
        nowhitespace: true,
        StringNumberRegex: true, //from custom.js
        messages: {
            required: "Required field.",
            minlength: jQuery.validator.format("min {0} char")
        }
    });

    $("#MODULE_Details").blur(function(){
      $(this).val($.trim( $(this).val()));
      $("#ERROR_MODULE").hide();
      validateSingleElemnet("MODULE_Details"); 
    });

    $("#MODULE_Details").rules( "add", {
      required: true,
      messages: {
          required: "Required field.",
          minlength: jQuery.validator.format("min {0} char")
      }
    });

    function validateSingleElemnet(element_id){
      var validator =$("#frm_mst_calculation" ).validate();
         if(validator.element( "#"+element_id+"" )){
            //check duplicate code
          if(element_id=="txtctcode" || element_id=="CTCODE" ) {
            // checkDuplicateCode();
          }

         }
    }

// growTextarea function: use for testing that the the javascript
// is also copied when row is cloned.  to confirm, 
// type several lines into Location, add a row, & repeat

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

$("#example2").on('click', '.remove', function() {
    var rowCount = $('#Row_Count').val();
    if (rowCount > 1) {
    $(this).closest('.participantRow').remove(); 
    } 
    $('[id*="txtsqno"]').each(function(idx, elem){
          $(this).val(idx+1);
    });
    if (rowCount <= 1) { 
    $(document).find('.remove').prop('disabled', true);  
    }
    event.preventDefault();
    });

//add row
      $("#example2").on('click', '.add', function() {
        var $tr = $(this).closest('tbody');
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
        var rowCount = $('#Row_Count').val();
		    rowCount = parseInt(rowCount)+1;
        $('#Row_Count').val(rowCount);
        $clone.find('.remove').removeAttr('disabled'); 
        $clone.find('[id*="txtcmpt"]').val('');
        $clone.find('[id*="txtprct"]').val('');
        $clone.find('[id*="txtfrm"]').val('');
        $clone.find('[id*="txtfrm"]').attr('disabled',true);
        $clone.find('[id*="txtamt"]').val('');
        $clone.find('[id*="txtprct"]').removeAttr('disabled');
        $clone.find('[id*="txtamt"]').removeAttr('disabled');
        $('[id*="txtsqno"]').each(function(idx, elem){
          $clone.find('[id*="txtsqno"]').val(idx+1);
        }); 
        $clone.find('[id*="chkfrm"]').prop('checked', false);
        $clone.find('[id*="chkgst"]').prop('checked', false);
        $clone.find('[id*="chkact"]').prop('checked', false);
        $clone.find('[id*="chklndc"]').prop('checked', false);
        event.preventDefault();
    });

    $("#btnUndo").click(function(){
        $("#AlertMessage").text("Do you want to erase entered information in this record?");
        $("#alert").modal('show');
        $("#YesBtn").data("funcname","fnUndoYes");
        $("#YesBtn").show();
        $("#NoBtn").data("funcname","fnUndoNo");
        $("#NoBtn").show();
        $("#OkBtn").hide();
        $("#OkBtn1").hide();
        $("#NoBtn").focus();
        highlighFocusBtn('activeNo');
        }); ////Undo button

 


        window.fnUndoYes = function (){
        //reload form
        window.location.reload();
        }//fnUndoYes


        window.fnUndoNo = function (){
        $("#txtctcode").focus();
        }//fnUndoNo





// });
</script>

@endpush

@push('bottom-scripts')
<script>

$(document).ready(function() {

    $('#frm_mst_calculation1').bootstrapValidator({
       
       fields: {
           txtlabel: {
               validators: {
                   notEmpty: {
                       message: 'The Calculation Code is required'
                   }
               }
           },            
       },
       submitHandler: function(validator, form, submitButton) {
           alert( "Handler for .submit() called." );
            event.preventDefault();
            $("#frm_mst_calculation").submit();
       }
   });
    
$( "#btnSaveSE" ).click(function() {
    var formCalculationMst = $("#frm_mst_calculation");
    if(formCalculationMst.valid()){
            $("#FocusId").val('');
            var CTCODE          =   $.trim($("[id*=txtctcode]").val());
            var MODULE_Details  =   $.trim($("[id*=MODULE_Details]").val());
            if(CTCODE ===""){
                $("#FocusId").val('CTCODE');
                $("#ProceedBtn").focus();
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text('Please enter value in Calculation Code.');
                $("#alert").modal('show');
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk1');
                return false;
            }
            else if(MODULE_Details ===""){
                $("#FocusId").val('MODULE_Details');
                $("#ProceedBtn").focus();
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text('Please select module.');
                $("#alert").modal('show');
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk1');
                return false;
            }

            else
            {
              event.preventDefault();
                    var allblank = [];
                    var allblank2 = [];
                    var allblank3 = [];
                    var allblank4 = [];
                    var allblank5 = [];
                    var allblank6 = [];
                        // $('#udfforsebody').find('.form-control').each(function () {
                          $("[id*=txtcmpt]").each(function(){
                            if($(this).val()!="")
                            {
                                allblank3.push('true');
                                $('.selvt').each(function () {
                                    var d_value = $(this).val();
                                    if(d_value != ""){
                                        allblank.push('true');
                                        if($(this).parent().parent().find('[id*="txtgl_popup"]').val() != "")
                                        {
                                            allblank2.push('true');
                                        }
                                        else{
                                            allblank2.push('false');
                                        }
                                        if($(this).parent().parent().find('[id*="chkfrm"]').is(":checked") != false)
                                        {
                                            if($(this).parent().parent().find('[id*="txtfrm"]').val() != "")
                                            {
                                                allblank4.push('true');
                                                if($(this).parent().parent().find('[id*="txtprct"]').val() != "" && $(this).parent().parent().find('[id*="txtprct"]').val()!='.0000')
                                                {
                                                    allblank5.push('true');
                                                }
                                                else{
                                                    allblank5.push('false');
                                                } 
                                            }
                                            else{
                                                allblank4.push('false');
                                            } 
                                        } 
                                        if($(this).parent().parent().find('[id*="chkfrm"]').is(":checked") == false)
                                        {
                                          if($(this).parent().parent().find('[id*="txtfrm"]').val() == "")
                                            {
                                              if($(this).parent().parent().find('[id*="txtamt"]').val()!='')
                                              {
                                              allblank6.push('true');
                                              }
                                              else
                                              {
                                                if($(this).parent().parent().find('[id*="txtprct"]').val()!="" && $(this).parent().parent().find('[id*="txtprct"]').val()!='.0000')
                                                {
                                                  allblank5.push('true');
                                                }
                                                else
                                                {
                                                  allblank5.push('false');
                                                  allblank6.push('false');
                                                }
                                              }
                                              
                                            }
                                        }
                                    }
                                    else{
                                        allblank.push('false');
                                    } 
                                    
                                    
                                });
                            }
                            else{
                                        allblank3.push('false');
                                    } 
                        });

                        if(jQuery.inArray("false", allblank3) !== -1){
                                $("#alert").modal('show');
                                $("#AlertMessage").text('Please enter Calculation Component Name.');
                                $("#YesBtn").hide(); 
                                $("#NoBtn").hide();  
                                $("#OkBtn1").show();
                                $("#OkBtn1").focus();
                                highlighFocusBtn('activeOk1');
                            }
                            else if(jQuery.inArray("false", allblank) !== -1){
                            $("#alert").modal('show');
                            $("#AlertMessage").text('Please select value in Basis.');
                            $("#YesBtn").hide(); 
                            $("#NoBtn").hide();  
                            $("#OkBtn").hide();
                            $("#OkBtn1").show();
                            $("#OkBtn1").focus();
                            highlighFocusBtn('activeOk1');
                            }
                            else if(jQuery.inArray("false", allblank2) !== -1){
                            $("#alert").modal('show');
                            $("#AlertMessage").text('Please select value in GL Account.');
                            $("#YesBtn").hide(); 
                            $("#NoBtn").hide();  
                            $("#OkBtn").hide();
                            $("#OkBtn1").show();
                            $("#OkBtn1").focus();
                            highlighFocusBtn('activeOk1');
                            }
                            else if(jQuery.inArray("false", allblank4) !== -1){
                            $("#alert").modal('show');
                            $("#AlertMessage").text('Please enter Formula.');
                            $("#YesBtn").hide(); 
                            $("#NoBtn").hide();  
                            $("#OkBtn").hide();
                            $("#OkBtn1").show();
                            $("#OkBtn1").focus();
                            highlighFocusBtn('activeOk1');
                            }
                            else if(jQuery.inArray("false", allblank5) !== -1){
                            $("#alert").modal('show');
                            $("#AlertMessage").text('Please enter Rate value.');
                            $("#YesBtn").hide(); 
                            $("#NoBtn").hide();  
                            $("#OkBtn").hide();
                            $("#OkBtn1").show();
                            $("#OkBtn1").focus();
                            highlighFocusBtn('activeOk1');
                            }
                            else if(jQuery.inArray("false", allblank6) !== -1){
                            $("#alert").modal('show');
                            $("#AlertMessage").text('Please enter Amount.');
                            $("#YesBtn").hide(); 
                            $("#NoBtn").hide();  
                            $("#OkBtn").hide();
                            $("#OkBtn1").show();
                            $("#OkBtn1").focus();
                            highlighFocusBtn('activeOk1');
                            }
                            else{
                                    $("#alert").modal('show');
                                    $("#AlertMessage").text('Do you want to save to record.');
                                    $("#YesBtn").data("funcname","fnSaveData"); 
                                    $("#YesBtn").focus();
                                    $("#OkBtn1").hide();
                                    $("#OkBtn").hide();
                                    highlighFocusBtn('activeYes');
                                }
                }
    }       
});//btnSaveCalculationTemplate


$( "#btnApprove" ).click(function() {
    var formCalculationMst = $("#frm_mst_calculation");
    if(formCalculationMst.valid()){
            $("#FocusId").val('');
            var CTCODE          =   $.trim($("[id*=txtctcode]").val());
            if(CTCODE ===""){
                $("#FocusId").val('CTCODE');
                $("[id*=txtctcode]").blur(); 
                $("#ProceedBtn").focus();
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text('Please enter value in Calculation Code.');
                $("#alert").modal('show');
                $("#OkBtn1").focus();
                $("#OkBtn").hide();
                highlighFocusBtn('activeOk1');
                return false;
            }
            else
            {
              event.preventDefault();
              var allblank = [];
                    var allblank2 = [];
                    var allblank3 = [];
                    var allblank4 = [];
                    var allblank5 = [];
                    var allblank6 = [];
                        // $('#udfforsebody').find('.form-control').each(function () {
                          $("[id*=txtcmpt]").each(function(){
                            if($(this).val()!="")
                            {
                                allblank3.push('true');
                                $('.selvt').each(function () {
                                    var d_value = $(this).val();
                                    if(d_value != ""){
                                        allblank.push('true');
                                        if($(this).parent().parent().find('[id*="txtgl_popup"]').val() != "")
                                        {
                                            allblank2.push('true');
                                        }
                                        else{
                                            allblank2.push('false');
                                        }
                                        if($(this).parent().parent().find('[id*="chkfrm"]').is(":checked") != false)
                                        {
                                            if($(this).parent().parent().find('[id*="txtfrm"]').val() != "")
                                            {
                                                allblank4.push('true');
                                                if($(this).parent().parent().find('[id*="txtprct"]').val() != "" && $(this).parent().parent().find('[id*="txtprct"]').val()!='.0000')
                                                {
                                                    allblank5.push('true');
                                                }
                                                else{
                                                    allblank5.push('false');
                                                } 
                                            }
                                            else{
                                                allblank4.push('false');
                                            } 
                                        } 
                                        if($(this).parent().parent().find('[id*="chkfrm"]').is(":checked") == false)
                                        {
                                          if($(this).parent().parent().find('[id*="txtfrm"]').val() == "")
                                            {
                                              if($(this).parent().parent().find('[id*="txtamt"]').val()!='')
                                              {
                                              allblank6.push('true');
                                              }
                                              else
                                              {
                                                if($(this).parent().parent().find('[id*="txtprct"]').val()!="" && $(this).parent().parent().find('[id*="txtprct"]').val()!='.0000')
                                                {
                                                  allblank5.push('true');
                                                }
                                                else
                                                {
                                                  allblank5.push('false');
                                                  allblank6.push('false');
                                                }
                                              }
                                              
                                            }
                                        }
                                    }
                                    else{
                                        allblank.push('false');
                                    } 
                                    
                                    
                                });
                            }
                            else{
                                        allblank3.push('false');
                                    } 
                        });

                        if(jQuery.inArray("false", allblank3) !== -1){
                                $("#alert").modal('show');
                                $("#AlertMessage").text('Please enter Calculation Component Name.');
                                $("#YesBtn").hide(); 
                                $("#NoBtn").hide();  
                                $("#OkBtn1").show();
                                $("#OkBtn1").focus();
                                $("#OkBtn").hide();
                                highlighFocusBtn('activeOk1');
                            }
                            else if(jQuery.inArray("false", allblank) !== -1){
                            $("#alert").modal('show');
                            $("#AlertMessage").text('Please select value in Basis.');
                            $("#YesBtn").hide(); 
                            $("#NoBtn").hide();  
                            $("#OkBtn1").show();
                            $("#OkBtn1").focus();
                            $("#OkBtn").hide();
                            highlighFocusBtn('activeOk1');
                            }
                            else if(jQuery.inArray("false", allblank2) !== -1){
                            $("#alert").modal('show');
                            $("#AlertMessage").text('Please select value in GL Account.');
                            $("#YesBtn").hide(); 
                            $("#NoBtn").hide();  
                            $("#OkBtn1").show();
                            $("#OkBtn1").focus();
                            $("#OkBtn").hide();
                            highlighFocusBtn('activeOk1');
                            }
                            else if(jQuery.inArray("false", allblank4) !== -1){
                            $("#alert").modal('show');
                            $("#AlertMessage").text('Please enter Formula.');
                            $("#YesBtn").hide(); 
                            $("#NoBtn").hide();  
                            $("#OkBtn1").show();
                            $("#OkBtn1").focus();
                            $("#OkBtn").hide();
                            highlighFocusBtn('activeOk1');
                            }
                            else if(jQuery.inArray("false", allblank5) !== -1){
                            $("#alert").modal('show');
                            $("#AlertMessage").text('Please enter Rate value.');
                            $("#YesBtn").hide(); 
                            $("#NoBtn").hide();  
                            $("#OkBtn1").show();
                            $("#OkBtn1").focus();
                            $("#OkBtn").hide();
                            highlighFocusBtn('activeOk1');
                            }
                            else if(jQuery.inArray("false", allblank6) !== -1){
                            $("#alert").modal('show');
                            $("#AlertMessage").text('Please enter Amount.');
                            $("#YesBtn").hide(); 
                            $("#NoBtn").hide();  
                            $("#OkBtn1").show();
                            $("#OkBtn1").focus();
                            $("#OkBtn").hide();
                            highlighFocusBtn('activeOk1');
                            }
                            else{
                                    $("#alert").modal('show');
                                    $("#AlertMessage").text('Do you want to save to record.');
                                    $("#YesBtn").data("funcname","fnApproveData"); 
                                    $("#YesBtn").focus();
                                    $("#OkBtn1").hide();
                                    $("#OkBtn").hide();
                                    highlighFocusBtn('activeYes');
                                }
                }
    }
});//btnApproveCalculationTemplate

$("#YesBtn").click(function(){

$("#alert").modal('hide');
var customFnName = $("#YesBtn").data("funcname");
    window[customFnName]();

}); //yes button


window.fnSaveData = function (){

            event.preventDefault();

                            var CalculationForm = $("#frm_mst_calculation");
                            var formData = CalculationForm.serialize();
                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                }
                            });
                            $.ajax({
                                url:'{{ route("mastermodify",[1,"update"]) }}',
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
                                                    $("#OkBtn").hide();
                                        }
                                    if(data.country=='norecord') {

                                        $("#YesBtn").hide();
                                        $("#NoBtn").hide();
                                        $("#OkBtn1").show();
                                        $("#AlertMessage").text(data.msg);
                                        $("#alert").modal('show');
                                        $("#OkBtn1").focus();
                                        $("#OkBtn").hide();

                                    }
                                    if(data.save=='invalid') {

                                        $("#YesBtn").hide();
                                        $("#NoBtn").hide();
                                        $("#OkBtn1").show();
                                        $("#AlertMessage").text(data.msg);
                                        $("#alert").modal('show');
                                        $("#OkBtn1").focus();
                                        $("#OkBtn").hide();
                                    }
                                    }
                                    if(data.success) {                   
                                        console.log("success MSG="+data.msg);
                                        $("#YesBtn").hide();
                                        $("#NoBtn").hide();
                                        $("#OkBtn").show();
                                        $("#AlertMessage").text(data.msg);
                                        $(".text-danger").hide();
                                        $("#alert").modal('show');
                                        $("#OkBtn").focus();
                                        $("#OkBtn1").hide();
                                        highlighFocusBtn('activeOk');
                                        // window.location.href="{{ route('master',[90,'index']) }}";
                                    }
                                   else
                                    {
                                        console.log("duplicate MSG="+data.msg);
                                        $("#YesBtn").hide();
                                        $("#NoBtn").hide();
                                        $("#OkBtn1").show();
                                        $("#AlertMessage").text(data.msg);
                                        $(".text-danger").hide();
                                        $("#alert").modal('show');
                                        $("#OkBtn1").focus();
                                        $("#OkBtn").hide();
                                        highlighFocusBtn('activeOk1');
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
                                    $("#OkBtn").hide();
                                    highlighFocusBtn('activeOk1');
                                },
                            });
            //             }
        

            // }

}

window.fnApproveData = function (){

//validate and save data
event.preventDefault();

var CalculationForm = $("#frm_mst_calculation");
var formData = CalculationForm.serialize();
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$.ajax({
    url:'{{ route("mastermodify",[1,"Approve"]) }}',
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
                $("#AlertMessage").text('Please enter value in Label.');
                $("#alert").modal('show');
                $("#OkBtn1").focus();
                $("#OkBtn").hide();
            }
           if(data.country=='norecord') {

            $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn1").show();
              $("#AlertMessage").text(data.msg);
              $("#alert").modal('show');
              $("#OkBtn1").focus();
              $("#OkBtn").hide();
           }
           if(data.save=='invalid') {

              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn1").show();
              $("#AlertMessage").text(data.msg);
              $("#alert").modal('show');
              $("#OkBtn1").focus();
              $("#OkBtn").hide();
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
            $("#OkBtn1").hide();
            highlighFocusBtn('activeOk');
        }
        else
        {
            console.log("duplicate MSG="+data.msg);
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text(data.msg);
            $(".text-danger").hide();
            $("#alert").modal('show');
            $("#OkBtn1").focus();
            $("#OkBtn").hide();
            highlighFocusBtn('activeOk1');
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
        $("#OkBtn").hide();
        highlighFocusBtn('activeOk1');
    },
});

}


$("#NoBtn").click(function(){

$("#alert").modal('hide');
var custFnName = $("#NoBtn").data("funcname");
    window[custFnName]();

}); //no button

//ok button
$("#OkBtn").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn").hide();
    $("#OkBtn1").hide();
    $(".text-danger").hide();
    window.location.href = '{{route("master",[1,"index"]) }}';
});

$("#OkBtn1").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn").hide();
    $("#OkBtn1").hide();
    $(".text-danger").hide();
});




});

function myFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("glcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("example23");
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

  function myNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("glnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("example23");
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

  function mybasisFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("bscodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("basisexample23");
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

  function mybasisNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("bsnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("basisexample23");
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


//
function showError(pId,pVal){
    $("#"+pId+"").text(pVal);
    $("#"+pId+"").show();
}

function highlighFocusBtn(pclass){
       $(".activeYes").hide();
       $(".activeNo").hide();
       
       $("."+pclass+"").show();
    }
function getFocus(){
    var FocusId=$("#FocusId").val();
    $("#"+FocusId).focus();
    $("#closePopup").click();
}

$('#example2').on ("focus","[id*='txtgl_popup']",function(event){
         $("#glidpopup").show();
        var id = $(this).attr('id');
        var id2 = $(this).parent().parent().find('[id*="hdngl_popup"]').attr('id');
        $('#hdn_fieldid').val(id);
        $('#hdn_fieldid2').val(id2);
      });

      $("#gl_closePopup").on("click",function(event){
        $("#glidpopup").hide();
      });

      $(".clsglid").click(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc")
        var txtid= $('#hdn_fieldid').val();
        var txt_id2= $('#hdn_fieldid2').val();
        
        $('#'+txtid).val(texdesc);
        $('#'+txt_id2).val(txtval);
        $("#glidpopup").hide();
        $("#glcodesearch").val(''); 
        $("#glnamesearch").val(''); 
        $(this).prop("checked",false);
        myFunction();
      });

      $('#example2').on ("focus","[id*='txtbasis_popup']",function(event){
          $("#basispopup").show();
        var id = $(this).attr('id');
        var id2 = $(this).parent().parent().find('[id*="hdnbasis_popup"]').attr('id');
        $('#hdn_basisfieldid').val(id);
        $('#hdn_basisfieldid2').val(id2);        
      });

      $("#basis_closePopup").on("click",function(event){
        $("#basispopup").hide();
      });

      $(".clsbasisid").click(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc")
        var txtid= $('#hdn_basisfieldid').val();
        var txt_id2= $('#hdn_basisfieldid2').val();
      
        $('#'+txtid).val(texdesc);
        $('#'+txt_id2).val(txtval);
        $("#basispopup").hide();
        $("#bscodesearch").val(''); 
        $("#bsnamesearch").val('');
        mybasisFunction();
        $(this).prop("checked",false);
      });

</script>


@endpush