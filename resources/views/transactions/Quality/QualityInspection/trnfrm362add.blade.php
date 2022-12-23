
@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
  <div class="row">
    <div class="col-lg-2"><a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Quality Inspection (QI)</a></div>
    
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
      <button class="btn topnavbt" id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
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
            <div class="col-lg-2 pl"><p>Quality Inspection No</p></div>
            <div class="col-lg-2 pl">
            <input type="text" name="QIGNO" id="QIGNO" value="{{$docarray['DOC_NO']}}" {{$docarray['READONLY']}} class="form-control" maxlength="{{$docarray['MAXLENGTH']}}" autocomplete="off" style="text-transform:uppercase" >
          <script>docMissing(@json($docarray['FY_FLAG']));</script>
            
            </div>
            
            <div class="col-lg-2 pl"><p>Quality Inspection Date</p></div>
            <div class="col-lg-2 pl">
                <input type="date" name="QIGDT" id="QIGDT" onchange='checkPeriodClosing("{{$FormId}}",this.value,1),getDocNoByEvent("QIGNO",this,@json($doc_req))' class="form-control mandatory" autocomplete="off" placeholder="dd/mm/yyyy" >
            </div>

            <div class="col-lg-2 pl"><p>Quality Check Process By</p></div>
			      <div class="col-lg-2 pl">
              <input type="text" name="QC_PROCESS_BY_popup" id="txtQC_PROCESS_BY_popup" class="form-control mandatory"  autocomplete="off"  readonly/>
              <input type="hidden" name="QC_PROCESS_BY" id="QC_PROCESS_BY" class="form-control" autocomplete="off" />
			      </div>

        </div>

        <div class="row">

            <div class="col-lg-2 pl"><p>External Lab Name</p></div>
			      <div class="col-lg-2 pl">
              <input type="text" name="EXT_LABNAME" id="EXT_LABNAME" class="form-control"  autocomplete="off"/>
			      </div>

            <div class="col-lg-2 pl"><p>Reference Doc No</p></div>
            <div class="col-lg-2 pl">
              <input type="text" name="REF_DOCNO" id="REF_DOCNO" class="form-control"  autocomplete="off"/>
			      </div>

            <div class="col-lg-2 pl"><p>GRN No</p></div>
            <div class="col-lg-2 pl">
            <input type="text" name="TEXT_GRNID_REF_DATA" id="TEXT_GRNID_REF_DATA" class="form-control mandatory"  autocomplete="off" readonly/>
            <input type="hidden" name="GRNID_REF" id="GRNID_REF" class="form-control" autocomplete="off" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-2 pl"><p>Item</p></div>
            <div class="col-lg-2 pl">
              <input type="text" name="popupITEMID" id="popupITEMID" class="form-control mandatory"  autocomplete="off" readonly/>
              <input type="hidden" name="ITEMID_REF" id="ITEMID_REF" class="form-control" autocomplete="off" />
              <input type="hidden" name="hdnmaterial" id="hdnmaterial" class="form-control" autocomplete="off" /> 

              <input type="hidden"  name="GEID_REF"  id="GEID_REF"  class="form-control" autocomplete="off" />
              <input type="hidden"  name="POID_REF"    id="POID_REF"    class="form-control" autocomplete="off" />
              <input type="hidden"  name="MRSID_REF"    id="MRSID_REF"    class="form-control" autocomplete="off" />
              <input type="hidden"  name="PIID_REF"    id="PIID_REF"    class="form-control" autocomplete="off" />
              <input type="hidden"  name="RFQID_REF"     id="RFQID_REF"     class="form-control" autocomplete="off" />   
              <input type="hidden"  name="VQID_REF"     id="VQID_REF"     class="form-control" autocomplete="off" />
              <input type="hidden"  name="IPOID_REF"     id="IPOID_REF"     class="form-control" autocomplete="off" />
              <input type="hidden"  name="BPOID_REF"     id="BPOID_REF"     class="form-control" autocomplete="off" />
                          

            </div>

          <div class="col-lg-2 pl"><p>GRN Received Qty</p></div>
          <div class="col-lg-2 pl">
            <input type="text" name="GRN_REC_QTY" id="GRN_REC_QTY" class="form-control"  autocomplete="off" readonly />
          </div>

          <div class="col-lg-2 pl"><p>UOM</p></div>
          <div class="col-lg-2 pl">
            <input type="text" name="TEXT_UOMID_REF_DATA" id="TEXT_UOMID_REF_DATA" class="form-control mandatory"  autocomplete="off" readonly/>
            <input type="hidden" name="UOMID_REF" id="UOMID_REF" class="form-control" autocomplete="off" />
          </div>


          </div>

          <div class="row">

            <div class="col-lg-2 pl"><p>QI Pick Qty</p></div>
            <div class="col-lg-2 pl">
              <input type="text" name="QI_PICK_QTY" id="QI_PICK_QTY" class="form-control"  autocomplete="off" onkeyup="validateQty(this.id)" onkeypress="return isNumberDecimalKey(event,this)" />
            </div>

            <div class="col-lg-2 pl"><p>Rejected Qty</p></div>
            <div class="col-lg-2 pl">
              <input type="text" name="REJECTED_QTY" id="REJECTED_QTY" class="form-control"  autocomplete="off" onkeyup="validateQty(this.id)" onkeypress="return isNumberDecimalKey(event,this)" />
            </div>

            <div class="col-lg-2 pl"><p>Store</p></div>
            <div class="col-lg-2 pl">
              <input type="text" name="REJECTED_STID_REF_popup" id="REJECTED_STID_REF_popup" class="form-control mandatory"  autocomplete="off" readonly onclick="store('REJECTED_STID_REF');" />
              <input type="hidden" name="REJECTED_STID_REF" id="REJECTED_STID_REF" class="form-control" autocomplete="off" />
              <input type="hidden" name="store_status" id="store_status" class="form-control" autocomplete="off" />
              
            </div>

          </div>

          <div class="row">

            <div class="col-lg-2 pl"><p>QC OK Qty</p></div>
            <div class="col-lg-2 pl">
              <input type="text" name="QC_OK_QTY" id="QC_OK_QTY" class="form-control"  autocomplete="off" onkeyup="validateQty(this.id)" onkeypress="return isNumberDecimalKey(event,this)" />
            </div>

            <div class="col-lg-2 pl"><p>Store</p></div>
            <div class="col-lg-2 pl">
              <input type="text" name="QC_OK_STID_REF_popup" id="QC_OK_STID_REF_popup" class="form-control mandatory"  autocomplete="off" readonly onclick="store('QC_OK_STID_REF');" />
              <input type="hidden" name="QC_OK_STID_REF" id="QC_OK_STID_REF" class="form-control" autocomplete="off" />
            </div>

            <div class="col-lg-2 pl"><p>Pending for QC</p></div>
            <div class="col-lg-2 pl">
              <input type="text" name="PENDING_QC_QTY" id="PENDING_QC_QTY" class="form-control"  autocomplete="off" readonly />
            </div>

          </div>

          <div class="row">

            <div class="col-lg-2 pl"><p>Store</p></div>
            <div class="col-lg-2 pl">
              <input type="text" name="PENDING_QC_STID_REF_popup" id="PENDING_QC_STID_REF_popup" class="form-control mandatory"  autocomplete="off" readonly onclick="store('PENDING_QC_STID_REF');" />
              <input type="hidden" name="PENDING_QC_STID_REF" id="PENDING_QC_STID_REF" class="form-control" autocomplete="off" />
            </div>

            <div class="col-lg-2 pl"><p>Remarks</p></div>
            <div class="col-lg-2 pl">
              <input type="text" name="REMARKS" id="REMARKS" class="form-control"  autocomplete="off"/>
            </div>

            <div class="col-lg-2 pl"><p>QC Accepted As</p></div>
            <div class="col-lg-2 pl">
              <select name="QC_ACCEPTED_AS" id="QC_ACCEPTED_AS" class="form-control"  autocomplete="off">
                <option value="Normal">Normal</option>
                <option value="Deviation">Deviation</option>
                <option value="Segregation">Segregation</option>
              </select>              
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
                          <th>Select</th>
                          <th>QC Parameter Code</th>
                          <th>QC Parameter Description</th>
                          <th>Unit Of Measurement (UOM)</th>
                          <th>Instrument Method</th>
                          <th>Standard Value</th>
                          <th>1</th>
                          <th>2</th>
                          <th>3</th>
                          <th>4</th>
                          <th>5</th>
                          <th>6</th>
                          <th>7</th>
                          <th>8</th>
                          <th>9</th>
                          <th>10</th>
                          <th>Average Total</th>
                          <th>Rejected  Yes / No</th>
                          <th>Rejection Reason</th>
                          <th>Rejection Remarks</th>
                          <th hidden><input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1" value="1"></th>
                        </tr>
                      </thead>
                     
                      <tbody>
                        <tr class="participantRow">
                          <td><input type="checkbox" name="SELECT_QCP[]"  id="SELECT_QCP_0" class="clssQCPID"  ></td>
                          <td><input type="text" name="txtQCPID_popup_0"   id="txtQCPID_popup_0" class="form-control" autocomplete="off" readonly/></td>
                          <td hidden><input type="text" name="QCPID_REF_0" id="QCPID_REF_0"      class="form-control" autocomplete="off" /></td>
                          
                          <td><input type="text" name="QCP_DES_0" id="QCP_DES_0" class="form-control"  autocomplete="off" readonly /></td>
                          <td hidden><input type="text" name="STD_TYPE_0" id="STD_TYPE_0" class="form-control"  autocomplete="off" readonly style="width:100px;" /></td>
                          
                          <td><input type="text" name="txtUOMID_popup_0"   id="txtUOMID_popup_0" class="form-control" autocomplete="off" readonly style="width:160px;" /></td>
                          <td hidden><input type="text" name="UOMID_REF_0" id="UOMID_REF_0"      class="form-control" autocomplete="off" /></td>
                          
                          <td><input type="text" name="txtINTMNTID_popup_0"   id="txtINTMNTID_popup_0" class="form-control" autocomplete="off" readonly style="width:100px;" /></td>
                          <td hidden><input type="text" name="INTMNTID_REF_0" id="INTMNTID_REF_0"      class="form-control" autocomplete="off" /></td>
                          
                          <td><input type="text" name="STD_VALUE_0" id="STD_VALUE_0" class="form-control"  autocomplete="off" readonly /></td>
                          
                          <td><input type="text" name="OBS_VALUE1_0" id="OBS_VALUE1_0" class="form-control"  autocomplete="off" style="width:50px;" onkeyup="AverageTotal(this.id)" onkeypress="return isNumberDecimalKey(event,this)" /></td>
                          <td><input type="text" name="OBS_VALUE2_0" id="OBS_VALUE2_0" class="form-control"  autocomplete="off" style="width:50px;" onkeyup="AverageTotal(this.id)" onkeypress="return isNumberDecimalKey(event,this)" /></td>
                          <td><input type="text" name="OBS_VALUE3_0" id="OBS_VALUE3_0" class="form-control"  autocomplete="off" style="width:50px;" onkeyup="AverageTotal(this.id)" onkeypress="return isNumberDecimalKey(event,this)" /></td>
                          <td><input type="text" name="OBS_VALUE4_0" id="OBS_VALUE4_0" class="form-control"  autocomplete="off" style="width:50px;" onkeyup="AverageTotal(this.id)" onkeypress="return isNumberDecimalKey(event,this)" /></td>
                          <td><input type="text" name="OBS_VALUE5_0" id="OBS_VALUE5_0" class="form-control"  autocomplete="off" style="width:50px;" onkeyup="AverageTotal(this.id)" onkeypress="return isNumberDecimalKey(event,this)" /></td>
                          <td><input type="text" name="OBS_VALUE6_0" id="OBS_VALUE6_0" class="form-control"  autocomplete="off" style="width:50px;" onkeyup="AverageTotal(this.id)" onkeypress="return isNumberDecimalKey(event,this)" /></td>
                          <td><input type="text" name="OBS_VALUE7_0" id="OBS_VALUE7_0" class="form-control"  autocomplete="off" style="width:50px;" onkeyup="AverageTotal(this.id)" onkeypress="return isNumberDecimalKey(event,this)" /></td>
                          <td><input type="text" name="OBS_VALUE8_0" id="OBS_VALUE8_0" class="form-control"  autocomplete="off" style="width:50px;" onkeyup="AverageTotal(this.id)" onkeypress="return isNumberDecimalKey(event,this)" /></td>
                          <td><input type="text" name="OBS_VALUE9_0" id="OBS_VALUE9_0" class="form-control"  autocomplete="off" style="width:50px;" onkeyup="AverageTotal(this.id)" onkeypress="return isNumberDecimalKey(event,this)" /></td>
                          <td><input type="text" name="OBS_VALUE10_0" id="OBS_VALUE10_0" class="form-control"  autocomplete="off" style="width:50px;" onkeyup="AverageTotal(this.id)" onkeypress="return isNumberDecimalKey(event,this)" /></td>

                          <td><input type="text" name="AVG_OBS_VALUE_0" id="AVG_OBS_VALUE_0" class="form-control"  autocomplete="off" onkeyup="AverageTotal(this.id)" /></td>
                          
                          <td>
                            <select name="REJECTED_0" id="REJECTED_0" class="form-control" autocomplete="off" readonly>
                              <option value="">Select</option>  
                              <option value="Yes">Yes</option>
                              <option value="No">No</option>           
                            </select>
                           </td>

                          <td><input type="text" name="txtRRID_popup_0"   id="txtRRID_popup_0" class="form-control" autocomplete="off" readonly/></td>
                          <td hidden><input type="text" name="RRID_REF_0" id="RRID_REF_0"      class="form-control" autocomplete="off" /></td>
                          
                          <td><input type="text" name="REJECTION_REMARKS_0" id="REJECTION_REMARKS_0" class="form-control"  autocomplete="off" /></td>

                        </tr>
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
    
                            <tbody>
                              @foreach($objUdf as $udfkey => $udfrow)
                              <tr  class="participantRow4">
                                <td>
                                  <input name={{"udffie_popup_".$udfkey}} id={{"txtudffie_popup_".$udfkey}} value="{{$udfrow->LABEL}}" class="form-control @if ($udfrow->ISMANDATORY==1) mandatory @endif" autocomplete="off" maxlength="100" disabled/>
                                </td>
                
                                <td hidden>
                                  <input type="text" name='{{"udffie_".$udfkey}}' id='{{"hdnudffie_popup_".$udfkey}}' value="{{$udfrow->UDFQIID}}" class="form-control" maxlength="100" />
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
<div id="alert" class="modal"  role="dialog"  data-backdrop="static">
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

<div id="QC_PROCESS_BY_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='QC_PROCESS_BY_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>QC Process By</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="RequestUserTable" class="display nowrap table  table-striped table-bordered" >
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
        <td class="ROW2"><input type="text" id="RequestUsercodesearch" class="form-control" onkeyup="RequestUserCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="RequestUsernamesearch" class="form-control" onkeyup="RequestUserNameFunction()"></td>
      </tr>

    </tbody>
    </table>
      <table id="RequestUserTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2">
          
        </thead>
        <tbody >     
        @foreach ($objUserList as $key=>$val)
        <tr >
          <td class="ROW1"> <input type="checkbox" name="SELECT_QC_PROCESS_BY[]" id="spidcode_{{ $key }}" class="clssrequestuser" value="{{ $val-> EMPID }}" ></td>  
          <td class="ROW2">{{ $val-> EMPCODE }} <input type="hidden" id="txtspidcode_{{ $key }}" data-desc="{{ $val-> EMPCODE }} - {{ $val-> FNAME }}  {{ $val-> LNAME }}"  value="{{ $val-> EMPID }}"/></td>
          <td class="ROW3">{{ $val-> FNAME }} {{ $val-> MNAME }} {{ $val-> LNAME }}</td>
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

<div id="ALERT_GRNID_REF_POPUP" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='CLOSE_GRNID_REF_POPUP' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>GRN NO</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="FIRST_GRNID_REF_TABLE" class="display nowrap table  table-striped table-bordered" >
    <thead>
    <tr>
      <th class="ROW1">Select</th> 
      <th class="ROW2">GRN NO</th>
      <th class="ROW3">DATE</th>
    </tr>
    </thead>
    <tbody>

    <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="CODE_GRNID_REF_SEARCH" class="form-control" onkeyup="CODE_GRNID_REF_FUNCTION()"></td>
        <td class="ROW3"><input type="text" id="NAME_GRNID_REF_SEARCH" class="form-control" onkeyup="NAME_GRNID_REF_FUNCTION()"></td>
      </tr>

    </tbody>
    </table>
      <table id="SECOND_GRNID_REF_TABLE" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2">
        </thead>
        <tbody>          
        @foreach ($objGRNList as $key=>$val)        
        <tr >
          <td class="ROW1"> <input type="checkbox" name="SELECT_GRNID_REF[]" id="ROW_GRNID_REF_ID_{{ $key }}" class="CLASS_GRNID_REF_ID" value="{{ $val['DOC_ID'] }}" ></td>   
          <td class="ROW2">{{ $val['DOC_CODE']  }} <input type="hidden" id="txtROW_GRNID_REF_ID_{{ $key }}" data-desc="{{ $val['DOC_CODE'] }}"  value="{{ $val['DOC_ID'] }}"/></td>
          <td class="ROW3">{{ $val['DOC_DESC']  }}</td>
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

<div id="QCPpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='QCP_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>QCP No</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="QCPTable" class="display nowrap table  table-striped table-bordered" >
    <thead>
          <tr id="none-select" class="searchalldata" hidden>
            
            <td> 
            <input type="hidden" id="hdn_QCPID1"/>
            <input type="hidden" id="hdn_QCPID2"/>
            <input type="hidden" id="hdn_QCPID3"/>
            <input type="hidden" id="hdn_QCPID4"/>
            <input type="hidden" id="hdn_QCPID5"/>
            </td>
          </tr>

      <tr>
        <th class="ROW1">Select</th> 
        <th class="ROW2">QCP NO</th>
        <th class="ROW3">Date</th>
      </tr>
    </thead>
    <tbody>

      <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="QCPcodesearch" class="form-control" onkeyup="QCPCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="QCPnamesearch" class="form-control" onkeyup="QCPNameFunction()"></td>
      </tr>

    </tbody>
    </table>
      <table id="QCPTable2" class="display nowrap table  table-striped table-bordered"  >
        <thead id="thead2">

        </thead>
        <tbody id="tbody_QCP">     
        
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<div id="RRpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='RR_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Rejection Reason</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="RRTable" class="display nowrap table  table-striped table-bordered" >
    <thead>
          <tr id="none-select" class="searchalldata" hidden>
            
            <td> 
            <input type="hidden" id="hdn_RRID1"/>
            <input type="hidden" id="hdn_RRID2"/>
            </td>
          </tr>

      <tr>
        <th class="ROW1">Select</th> 
        <th class="ROW2">Rejection Code</th>
        <th class="ROW3">Rejection Description</th>
      </tr>
    </thead>
    <tbody>

      <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="RRcodesearch" class="form-control" onkeyup="RRCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="RRnamesearch" class="form-control" onkeyup="RRNameFunction()"></td>
      </tr>

    </tbody>
    </table>
      <table id="RRTable2" class="display nowrap table  table-striped table-bordered"  >
        <thead id="thead2">

        </thead>
        <tbody id="tbody_RR">     
        
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>


<div id="UOMpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='UOM_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Unit Of Measurement (UOM)</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="UOMTable" class="display nowrap table  table-striped table-bordered" >
    <thead>
          <tr id="none-select" class="searchalldata" hidden>
            
            <td> 
            <input type="hidden" id="hdn_UOMID1"/>
            <input type="hidden" id="hdn_UOMID2"/>
            </td>
          </tr>

      <tr>
        <th class="ROW1">Select</th> 
        <th class="ROW2">UOM Code</th>
        <th class="ROW3">UOM Description</th>
      </tr>
    </thead>
    <tbody>

      <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="UOMcodesearch" class="form-control" onkeyup="UOMCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="UOMnamesearch" class="form-control" onkeyup="UOMNameFunction()"></td>
      </tr>

    </tbody>
    </table>
      <table id="UOMTable2" class="display nowrap table  table-striped table-bordered"  >
        <thead id="thead2">

        </thead>
        <tbody id="tbody_UOM">     
        
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>


<div id="INTMNTpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='INTMNT_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Instrument Method</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="INTMNTTable" class="display nowrap table  table-striped table-bordered" >
    <thead>
          <tr id="none-select" class="searchalldata" hidden>
            
            <td> 
            <input type="hidden" id="hdn_INTMNTID1"/>
            <input type="hidden" id="hdn_INTMNTID2"/>
            </td>
          </tr>

      <tr>
        <th class="ROW1">Select</th> 
        <th class="ROW2">Instrument Code</th>
        <th class="ROW3">Instrument Description</th>
      </tr>
    </thead>
    <tbody>

      <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="INTMNTcodesearch" class="form-control" onkeyup="INTMNTCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="INTMNTnamesearch" class="form-control" onkeyup="INTMNTNameFunction()"></td>
      </tr>

    </tbody>
    </table>
      <table id="INTMNTTable2" class="display nowrap table  table-striped table-bordered"  >
        <thead id="thead2">

        </thead>
        <tbody id="tbody_INTMNT">     
        
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
                <th style="width:8%;" {{$AlpsStatus['hidden']}}>{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</th>
                <th style="width:8%;" {{$AlpsStatus['hidden']}}>{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</th>
                <th style="width:8%;" {{$AlpsStatus['hidden']}}>{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</th>
                <th style="width:8%;">Status</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td style="width:8%;text-align:center;"></td>
                <td style="width:10%;"><input type="text" id="Itemcodesearch" class="form-control" onkeyup="ItemCodeFunction(event)"></td>
                <td style="width:10%;"><input type="text" id="Itemnamesearch" class="form-control" onkeyup="ItemNameFunction(event)"></td>
                <td style="width:8%;"><input type="text" id="ItemUOMsearch" class="form-control" onkeyup="ItemUOMFunction(event)"></td>
                <td style="width:8%;"><input type="text" id="ItemQTYsearch" class="form-control" onkeyup="ItemQTYFunction(event)" readonly></td>
                <td style="width:8%;"><input type="text" id="ItemGroupsearch" class="form-control" onkeyup="ItemGroupFunction(event)"></td>
                <td style="width:8%;"><input type="text" id="ItemCategorysearch" class="form-control" onkeyup="ItemCategoryFunction(event)"></td>
                <td style="width:8%;"><input type="text" id="ItemBUsearch" class="form-control" onkeyup="ItemBUFunction(event)" readonly></td>
                <td style="width:8%;" {{$AlpsStatus['hidden']}}><input type="text" id="ItemAPNsearch" class="form-control" onkeyup="ItemAPNFunction(event)"></td>
                <td style="width:8%;" {{$AlpsStatus['hidden']}}><input type="text" id="ItemCPNsearch" class="form-control" onkeyup="ItemCPNFunction(event)"></td>
                <td style="width:8%;" {{$AlpsStatus['hidden']}}><input type="text" id="ItemOEMPNsearch" class="form-control" onkeyup="ItemOEMPNFunction(event)"></td>
                <td style="width:8%;"><input  type="text" id="ItemStatussearch" class="form-control" onkeyup="ItemStatusFunction(event)" readonly></td>
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

<div id="stidpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='st_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>To Store</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="STCodeTable" class="display nowrap table  table-striped table-bordered" >
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
        <td class="ROW2"><input type="text" id="stcodesearch" class="form-control" onkeyup="STCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="stnamesearch" class="form-control" onkeyup="STNameFunction()"></td>
      </tr>

    </tbody>
    </table>
      <table id="STCodeTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2">
        </thead>
        <tbody>
        @foreach ($objStoreList as $key=>$val)
        <tr>
          <td class="ROW1"> <input type="checkbox" name="SELECT_STID_REF[]" id="stidcode_{{ $key }}" class="clsstid" value="{{ $val-> STID }}" ></td>   
          <td class="ROW2">{{ $val-> STCODE }} <input type="hidden" id="txtstidcode_{{ $key }}" data-desc="{{ $val-> STCODE }} - {{ $val-> NAME }}"  value="{{ $val-> STID }}"/></td>
          <td class="ROW3">{{ $val-> NAME }}</td>
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


//================================== QC PROCESS BY FUNCTION =================================
let sptid = "#RequestUserTable2";
let sptid2 = "#RequestUserTable";
let requestuserheaders = document.querySelectorAll(sptid2 + " th");


requestuserheaders.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(sptid, ".clssrequestuser", "td:nth-child(" + (i + 1) + ")");
  });
});

function RequestUserCodeFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("RequestUsercodesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("RequestUserTable2");
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

function RequestUserNameFunction() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("RequestUsernamesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("RequestUserTable2");
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

$('#txtQC_PROCESS_BY_popup').click(function(event){
    showSelectedCheck($("#QC_PROCESS_BY").val(),"SELECT_QC_PROCESS_BY");
    $("#QC_PROCESS_BY_popup").show();
});

$("#QC_PROCESS_BY_closePopup").click(function(event){
  $("#QC_PROCESS_BY_popup").hide();
});

$(".clssrequestuser").click(function(){
  var fieldid = $(this).attr('id');
  var txtval =    $("#txt"+fieldid+"").val();
  var texdesc =   $("#txt"+fieldid+"").data("desc");
  
  $('#txtQC_PROCESS_BY_popup').val(texdesc);
  $('#QC_PROCESS_BY').val(txtval);
  $("#QC_PROCESS_BY_popup").hide();
  
  $("#RequestUsercodesearch").val(''); 
  $("#RequestUsernamesearch").val(''); 
  RequestUserCodeFunction();
  event.preventDefault();
});

//================================== GRN FUNCTION =================================

let SECOND_GRNID_REF_TABLE  = "#SECOND_GRNID_REF_TABLE";
let FIRST_GRNID_REF_TABLE   = "#FIRST_GRNID_REF_TABLE";
let GRNID_REF_HEADERS = document.querySelectorAll(FIRST_GRNID_REF_TABLE + " th");

GRNID_REF_HEADERS.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(SECOND_GRNID_REF_TABLE, ".CLASS_GRNID_REF_ID", "td:nth-child(" + (i + 1) + ")");
  });
});

function CODE_GRNID_REF_FUNCTION() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("CODE_GRNID_REF_SEARCH");
  filter = input.value.toUpperCase();
  table = document.getElementById("SECOND_GRNID_REF_TABLE");
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

function NAME_GRNID_REF_FUNCTION() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("NAME_GRNID_REF_SEARCH");
      filter = input.value.toUpperCase();
      table = document.getElementById("SECOND_GRNID_REF_TABLE");
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

$('#TEXT_GRNID_REF_DATA').click(function(event){
  showSelectedCheck($("#GRNID_REF").val(),"SELECT_GRNID_REF");
  $("#ALERT_GRNID_REF_POPUP").show();
});

$("#CLOSE_GRNID_REF_POPUP").click(function(event){
  $("#ALERT_GRNID_REF_POPUP").hide();
});

$(".CLASS_GRNID_REF_ID").click(function(){
  var fieldid = $(this).attr('id');
  var txtval =    $("#txt"+fieldid+"").val();
  var texdesc =   $("#txt"+fieldid+"").data("desc");

  if (txtval != $("#GRNID_REF").val()){

    $("#popupITEMID").val('');
    $("#ITEMID_REF").val('');
    $("#GRN_REC_QTY").val('');
    $("#TEXT_UOMID_REF_DATA").val('');
    $("#UOMID_REF").val('');
    $("#QI_PICK_QTY").val('');
    $("#REJECTED_QTY").val('');
    $("#REJECTED_STID_REF_popup").val('');
    $("#REJECTED_STID_REF").val('');
    $("#QC_OK_QTY").val('');
    $("#QC_OK_STID_REF_popup").val('');
    $("#QC_OK_STID_REF").val('');
    $("#PENDING_QC_QTY").val('');
    $("#PENDING_QC_STID_REF_popup").val('');
    $("#PENDING_QC_STID_REF").val('');

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
  }

  
  $('#TEXT_GRNID_REF_DATA').val(texdesc);
  $('#GRNID_REF').val(txtval);
  $("#ALERT_GRNID_REF_POPUP").hide();
  
  $("#CODE_GRNID_REF_SEARCH").val(''); 
  $("#NAME_GRNID_REF_SEARCH").val('');

  event.preventDefault();
});


//================================== STORE =================================
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

function STNameFunction() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("stnamesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("STCodeTable2");
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

function store(id){
  $("#stidpopup").show();
  $("#store_status").val(id);
}

$("#st_closePopup").click(function(event){
  $("#stidpopup").hide();
});

$(".clsstid").click(function(){

var fieldid = $(this).attr('id');
var txtval =    $("#txt"+fieldid+"").val();
var texdesc =   $("#txt"+fieldid+"").data("desc");

var id      = $("#store_status").val();

$('#'+id+'_popup').val(texdesc);
$('#'+id).val(txtval);


$("#stidpopup").hide();
$("#stcodesearch").val(''); 
$("#stnamesearch").val(''); 
STCodeFunction();
event.preventDefault();

});

/*================================== QCP POPUP FUNCTION =================================*/

function getDocNo(ITEMID_REF){

  $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })

    $.ajax({
        url:'{{route("transaction",[$FormId,"getDocNo"])}}',
        type:'POST',
        data:{'id':ITEMID_REF},
        success:function(data) {
          $("#example2").html(data);
        },
        error:function(data){
          console.log("Error: Something went wrong.");
          $("#example2").html('');
        },
    });
}

//================================== RR POPUP FUNCTION =================================

let RRTable2 = "#RRTable2";
let RRTable = "#RRTable";
let RRheaders = document.querySelectorAll(RRTable + " th");

RRheaders.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(RRTable2, ".clssRRID", "td:nth-child(" + (i + 1) + ")");
  });
});

function RRCodeFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("RRcodesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("RRTable2");
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

function RRNameFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("RRnamesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("RRTable2");
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

$('#Material').on('click','[id*="txtRRID_popup"]',function(event){

  $('#hdn_RRID1').val($(this).attr('id'));
  $('#hdn_RRID2').val($(this).parent().parent().find('[id*="RRID_REF"]').attr('id'));
  
  var fieldid = $(this).parent().parent().find('[id*="RRID_REF"]').attr('id');

  $("#RRpopup").show();
  $("#tbody_RR").html('loading...');

  $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  })

  $.ajax({
      url:'{{route("transaction",[$FormId,"getRRNo"])}}',
      type:'POST',
      data:{'fieldid':fieldid},
      success:function(data) {
        $("#tbody_RR").html(data);
        BindRR();
        showSelectedCheck($("#"+fieldid).val(),"SELECT_"+fieldid);
      },
      error:function(data){
        console.log("Error: Something went wrong.");
        $("#tbody_RR").html('');
      },
  });

});

$("#RR_closePopup").click(function(event){
  $("#RRpopup").hide();
});

function BindRR(){
  $(".clssRRID").click(function(){

    var fieldid = $(this).attr('id');
    var txtval  = $("#txt"+fieldid+"").val();
    var texdesc = $("#txt"+fieldid+"").data("desc");

    var txtid     = $('#hdn_RRID1').val();
    var txt_id2   = $('#hdn_RRID2').val();
  
    $('#'+txtid).val(texdesc);
    $('#'+txt_id2).val(txtval);

    $("#RRpopup").hide();
    
    $("#RRcodesearch").val(''); 
    $("#RRnamesearch").val(''); 
    RRCodeFunction();
    event.preventDefault();
  });
}


//================================== ITEM DETAILS =================================

let itemtid = "#ItemIDTable2";
let itemtid2 = "#ItemIDTable";
let itemtidheaders = document.querySelectorAll(itemtid2 + " th");

itemtidheaders.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(itemtid, ".clsitemid", "td:nth-child(" + (i + 1) + ")");
  });
});

function ItemCodeFunction(e) {
  if(e.which == 13){
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
}

function ItemNameFunction(e) {
  if(e.which == 13){
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
}

function ItemUOMFunction(e) {
  if(e.which == 13){
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

function ItemCategoryFunction(e) {
  if(e.which == 13){
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
}

function ItemBUFunction(e) {
  if(e.which == 13){
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
}

function ItemAPNFunction(e) {
  if(e.which == 13){
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
}

function ItemCPNFunction(e) {
  if(e.which == 13){
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
}

function ItemOEMPNFunction(e) {
  if(e.which == 13){
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

$("#popupITEMID").click(function(event){

  var GRNID_REF = $("#GRNID_REF").val();

  if(GRNID_REF ===""){
    $("#FocusId").val('TEXT_GRNID_REF_DATA');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select GRN No.');
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
        data:{'GRNID_REF':GRNID_REF,'status':'A'},
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
    var id9   = $(this).parent().parent().find('[id*="VQID_REF"]').attr('id');
    var id10  = $(this).parent().parent().find('[id*="IPOID_REF"]').attr('id');
    var id11  = $(this).parent().parent().find('[id*="RFQID_REF"]').attr('id');
    var id12  = $(this).parent().parent().find('[id*="PIID_REF"]').attr('id');
    var id13  = $(this).parent().parent().find('[id*="BPOID_REF"]').attr('id');


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

  $("#Itemcodesearch").val('');
  $("#Itemnamesearch").val('');
  $("#ItemUOMsearch").val('');
  $("#ItemQTYsearch").val('');
  $("#ItemGroupsearch").val('');
  $("#ItemCategorysearch").val('');
  $("#ItemBUsearch").val('');
  $("#ItemAPNsearch").val('');
  $("#ItemCPNsearch").val('');
  $("#ItemOEMPNsearch").val('');

});

function bindItemEvents(){

  $('#ItemIDTable2').off(); 
 
  $('[id*="chkId"]').change(function(){

    var fieldid             =   $(this).parent().parent().attr('id');
    var item_id             =   $("#txt"+fieldid+"").data("desc1");
    var item_code           =   $("#txt"+fieldid+"").data("desc2");
    var item_name           =   $("#txt"+fieldid+"").data("desc3");
    var item_main_uom_id    =   $("#txt"+fieldid+"").data("desc4");
    var item_main_uom_code  =   $("#txt"+fieldid+"").data("desc5");
    var item_qty            =   $("#txt"+fieldid+"").data("desc6");
    var item_unique_row_id  =   $("#txt"+fieldid+"").data("desc7");
    var item_POID           =   $("#txt"+fieldid+"").data("desc8");
    var item_MRSID          =   $("#txt"+fieldid+"").data("desc9");
    var item_PIID           =   $("#txt"+fieldid+"").data("desc10");
    var item_RFQID          =   $("#txt"+fieldid+"").data("desc11");
    var item_soqty          =   $("#txt"+fieldid+"").data("desc12");
    var item_VQID           =   $("#txt"+fieldid+"").data("desc13");
    var item_GEID           =   $("#txt"+fieldid+"").data("desc15");
    var item_IPOID          =   $("#txt"+fieldid+"").data("desc16");
    var item_BPOID          =   $("#txt"+fieldid+"").data("desc17");

    if($(this).is(":checked") == true) {

      $('#example2').find('.participantRow').each(function(){

        $('#popupITEMID').val(item_code+'-'+item_name);
        $('#ITEMID_REF').val(item_id);
        $('#GRN_REC_QTY').val(item_qty);
        $('#QI_PICK_QTY').val(item_qty);
        $('#TEXT_UOMID_REF_DATA').val(item_main_uom_code);
        $('#UOMID_REF').val(item_main_uom_id);

        $('#GEID_REF').val(item_GEID);
        $('#POID_REF').val(item_POID);
        $('#MRSID_REF').val(item_MRSID);
        $('#PIID_REF').val(item_PIID);
        $('#RFQID_REF').val(item_RFQID);
        $('#VQID_REF').val(item_VQID);
        $('#IPOID_REF').val(item_IPOID);
        $('#BPOID_REF').val(item_BPOID);
     
        

        $("#REJECTED_QTY").val('');
        $("#REJECTED_STID_REF_popup").val('');
        $("#REJECTED_STID_REF").val('');
        $("#QC_OK_QTY").val('');
        $("#QC_OK_STID_REF_popup").val('');
        $("#QC_OK_STID_REF").val('');
        $("#PENDING_QC_QTY").val('');
        $("#PENDING_QC_STID_REF_popup").val('');
        $("#PENDING_QC_STID_REF").val('');
                                 
        $("#ITEMIDpopup").hide();

         
        event.preventDefault();
      });
    }
    

    $("#Itemcodesearch").val(''); 
    $("#Itemnamesearch").val(''); 
    $("#ItemUOMsearch").val(''); 
    $("#ItemGroupsearch").val(''); 
    $("#ItemCategorysearch").val(''); 
    $("#ItemStatussearch").val(''); 
    $('.remove').removeAttr('disabled'); 
    ItemCodeFunction(item_id);
    $("#ITEMIDpopup").hide();
    getDocNo(item_id);
    event.preventDefault();
  });

} 

//================================== ONLOAD FUNCTION ==================================

$(document).ready(function(e) {
  
  var lastdt = <?php echo json_encode($objlastdt[0]->QIGDT); ?>;
  var today = new Date(); 
  var prodate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;

  $('#QIGDT').attr('min',lastdt);
  $('#QIGDT').attr('max',prodate);


  var d = new Date(); 
  var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
  $('#QIGDT').val(today);

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
  
  $('#QIGNO').focusout(function(){
      var QIGNO   =   $.trim($(this).val());
      if(QIGNO ===""){               
               
              $("#FocusId").val('QIGNO');
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text('Please enter value in QIJ No.');
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
                        $("#FocusId").val('QIGNO');                  
                        console.log("cancel MSG="+data.msg);
                                      $("#YesBtn").hide();
                                      $("#NoBtn").hide();
                                      $("#OkBtn1").show();
                                      $("#AlertMessage").text(data.msg);
                                      $(".text-danger").hide();
                                      $("#QIGNO").val('');
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


 /*  $('#QIGDT').change(function( event ) {
    var today = new Date();     
    var d = new Date($(this).val()); 
    today.setHours(0, 0, 0, 0) ;
    d.setHours(0, 0, 0, 0) ;
    var sodate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
    if (d < today) {
        $(this).val(sodate);
        $("#alert").modal('show');
        $("#AlertMessage").text('QIJ Date cannot be less than Current date');
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

  }); */


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

  var QIGNO               = $.trim($("#QIGNO").val());
  var QIGDT               = $.trim($("#QIGDT").val());
  var QC_PROCESS_BY       = $.trim($("#QC_PROCESS_BY").val());
  var EXT_LABNAME         = $.trim($("#EXT_LABNAME").val());
  var REF_DOCNO           = $.trim($("#REF_DOCNO").val());
  var GRNID_REF           = $.trim($("#GRNID_REF").val());
  var ITEMID_REF          = $.trim($("#ITEMID_REF").val());
  var GRN_REC_QTY         = $.trim($("#GRN_REC_QTY").val());
  var UOMID_REF           = $.trim($("#UOMID_REF").val());
  var QI_PICK_QTY         = $.trim($("#QI_PICK_QTY").val());
  var REJECTED_QTY        = $.trim($("#REJECTED_QTY").val());
  var REJECTED_STID_REF   = $.trim($("#REJECTED_STID_REF").val());
  var QC_OK_QTY           = $.trim($("#QC_OK_QTY").val());
  var QC_OK_STID_REF      = $.trim($("#QC_OK_STID_REF").val());
  var PENDING_QC_QTY      = $.trim($("#PENDING_QC_QTY").val());
  var PENDING_QC_STID_REF = $.trim($("#PENDING_QC_STID_REF").val());
 
  if(QIGNO ===""){
    $("#FocusId").val('QIGNO');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Enter Quality Inspection No');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if(QIGDT ===""){
    $("#FocusId").val('QIGDT');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Select Quality Inspection Date');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if(QC_PROCESS_BY ===""){
    $("#FocusId").val('txtQC_PROCESS_BY_popup');    
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Select QC Process By');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  } 
  /* else if(EXT_LABNAME ===""){
    $("#FocusId").val('EXT_LABNAME');    
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Enter External Lab Name');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if(REF_DOCNO ===""){
    $("#FocusId").val('REF_DOCNO');    
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Enter Ref Doc No');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  } */
  else if(GRNID_REF ===""){
    $("#FocusId").val('TEXT_GRNID_REF_DATA');    
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Enter GRN No');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if(ITEMID_REF ===""){
    $("#FocusId").val('popupITEMID');    
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Select item.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if(GRN_REC_QTY ===""){
    $("#FocusId").val('GRN_REC_QTY');    
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please enter GRN Received Qty.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  } 
  else if(UOMID_REF ===""){
    $("#FocusId").val('TEXT_UOMID_REF_DATA');    
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('UOM is required.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if(QI_PICK_QTY ===""){
    $("#FocusId").val('QI_PICK_QTY');    
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please enter QI Pick Qty.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  if(parseFloat(QI_PICK_QTY) > parseFloat(GRN_REC_QTY)){
    $("#FocusId").val('QI_PICK_QTY');    
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('QI Pick Qty Should Equal OR Less Then GRN Received Qty.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if(REJECTED_QTY ===""){
    $("#FocusId").val('REJECTED_QTY');    
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please enter Rejected Qty.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if(parseFloat(REJECTED_QTY) > parseFloat(QI_PICK_QTY)){
    $("#FocusId").val('REJECTED_QTY');    
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Rejected Qty Should Less Then QI Pick Qty.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }  
  else if(REJECTED_STID_REF ==="" && REJECTED_QTY !='0'){
    $("#FocusId").val('REJECTED_STID_REF_popup');    
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Select Rejected Store.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if(QC_OK_QTY ===""){
    $("#FocusId").val('QC_OK_QTY');    
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please enter QC OK Qty.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if( (parseFloat(REJECTED_QTY)+parseFloat(QC_OK_QTY)+parseFloat(PENDING_QC_QTY)) > parseFloat(GRN_REC_QTY) ){
    $("#FocusId").val('QC_OK_QTY');    
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('(QC OK Qty + Rejected Qty + Pending for QC) Should not greater then QI Pick Qty.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if(QC_OK_STID_REF ==="" && QC_OK_QTY !='0'){
    $("#FocusId").val('QC_OK_STID_REF_popup');    
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Select QC OK Qty Store.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if(PENDING_QC_STID_REF ==="" && PENDING_QC_QTY !='0'){
    $("#FocusId").val('PENDING_QC_STID_REF_popup');    
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Select Pending  Store.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if(REJECTED_STID_REF ==QC_OK_STID_REF && REJECTED_QTY !='0'){
    $("#FocusId").val('QC_OK_STID_REF_popup');    
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Store Should Not Same So Please Select Different Store.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if(REJECTED_STID_REF ==PENDING_QC_STID_REF && REJECTED_QTY !='0'){
    $("#FocusId").val('PENDING_QC_STID_REF_popup');    
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Store Should Not Same So Please Select Different Store.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if(QC_OK_STID_REF ==PENDING_QC_STID_REF && PENDING_QC_QTY !='0'){
    $("#FocusId").val('PENDING_QC_STID_REF_popup');    
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Store Should Not Same So Please Select Different Store.');
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


    var all_location_id = document.querySelectorAll('input[name="SELECT_QCP[]"]:checked');
    var aIds = [];
    for(var x = 0, l = all_location_id.length; x < l;  x++){
      aIds.push(all_location_id[x].value);
    }

    $('#Material').find('.participantRow').each(function(){

      if($(this).find("[id*=SELECT_QCP]").is(":checked") == true){

        if($.trim($(this).find("[id*=QCPID_REF]").val()) ===""){
          allblank1.push('false');
          focustext1 = $(this).find("[id*=txtQCPID_popup]").attr('id');
          return false;
        }
        else if($.trim($(this).find("[id*=STD_VALUE]").val()) ===""){
          allblank2.push('false');
          focustext2 = $(this).find("[id*=STD_VALUE]").attr('id');
          return false;
        }
        else if($.trim($(this).find("[id*=AVG_OBS_VALUE]").val()) ===""){
          allblank3.push('false');
          focustext3 = $(this).find("[id*=AVG_OBS_VALUE]").attr('id');
          return false;
        }
        else if( $(this).find("[id*=REJECTED]").val() ===""){
          allblank4.push('false');
          focustext4 = $(this).find("[id*=REJECTED]").attr('id');
          return false;
        }
        else if( $(this).find("[id*=REJECTED]").val() =="Yes" && $.trim($(this).find("[id*=RRID_REF]").val()) ===""){
          allblank5.push('false');
          focustext5 = $(this).find("[id*=txtRRID_popup]").attr('id');
          return false;
        }

        else{
          allblank1.push('true');
          allblank2.push('true');
          allblank3.push('true');
          allblank4.push('true');
          allblank5.push('true');

          focustext1   = "";
          focustext2   = "";
          focustext3   = "";
          focustext4   = "";
          focustext5   = "";
          return true;
        }

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

    if(aIds.length < 1){
      $("#FocusId").val(focustext1);
      $("#alert").modal('show');
      $("#AlertMessage").text('Please Select Minimum 1 Record In Material');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank1) !== -1){
      $("#FocusId").val(focustext1);
      $("#alert").modal('show');
      $("#AlertMessage").text('QC Parameter Code Is Required In Material');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank2) !== -1){
      $("#FocusId").val(focustext2);
      $("#alert").modal('show');
      $("#AlertMessage").text('Standard Value Is Required In Material');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank3) !== -1){
      $("#FocusId").val(focustext3);
      $("#alert").modal('show');
      $("#AlertMessage").text('Average Total Is Required in Material');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank4) !== -1){
      $("#FocusId").val(focustext4);
      $("#alert").modal('show');
      $("#AlertMessage").text('Please Enter Rejected Yes / No In Material');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank5) !== -1){
      $("#FocusId").val(focustext5);
      $("#alert").modal('show');
      $("#AlertMessage").text('Rejection Reason Is Required In Material');
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
    else if(checkPeriodClosing('{{$FormId}}',$("#QIGDT").val(),0) ==0){
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

            if(data.errors.QIGNO){
                showError('ERROR_QIGNO',data.errors.QIGNO);
                        $("#YesBtn").hide();
                        $("#NoBtn").hide();
                        $("#OkBtn1").show();
                        $("#AlertMessage").text('Please enter correct value in QIJ NO.');
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

//================================== USER DEFINE FUNCTION ==================================

// function isNumberDecimalKey(evt){
//     var charCode = (evt.which) ? evt.which : event.keyCode
//     if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57) )
//     return false;

//     return true;
// }


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



function validateQty(TYPE){

  var GRN_REC_QTY     = parseFloat($.trim($("#GRN_REC_QTY").val()));
  var QI_PICK_QTY     = $.trim($("#QI_PICK_QTY").val());
  var REJECTED_QTY    = $.trim($("#REJECTED_QTY").val());
  var QC_OK_QTY       = $.trim($("#QC_OK_QTY").val());
  var PENDING_QC_QTY  = $.trim($("#PENDING_QC_QTY").val());


  if(TYPE =="QI_PICK_QTY" && QI_PICK_QTY !=""){

    if(parseFloat(QI_PICK_QTY) > GRN_REC_QTY){
      $("#QI_PICK_QTY").val('');
      $("#FocusId").val(TYPE);        
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('QI Pick Qty Should Equal OR Less Then GRN Received Qty.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
    }

  }

  else if(TYPE =="REJECTED_QTY" && REJECTED_QTY !=""){

    if(parseFloat(REJECTED_QTY) > parseFloat(GRN_REC_QTY)){
      $("#REJECTED_QTY").val('');
      $("#FocusId").val(TYPE);        
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Rejected Qty Should Less Then GRN Received Qty.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
    }
    else{
      var QcOk  = parseFloat(QI_PICK_QTY) - parseFloat(REJECTED_QTY);
      $("#QC_OK_QTY").val(QcOk);
      $("#PENDING_QC_QTY").val(0);
    }

  }


  else if(TYPE =="QC_OK_QTY" && QC_OK_QTY !=""){

    if(parseFloat(QC_OK_QTY) > parseFloat(GRN_REC_QTY)){
      $("#QC_OK_QTY").val('');
      $("#FocusId").val(TYPE);        
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Rejected Qty Should Less Then GRN Received Qty.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
    }
    else{
      var QcOk  = parseFloat(GRN_REC_QTY) - parseFloat(QC_OK_QTY);
      $("#REJECTED_QTY").val(QcOk);
      $("#PENDING_QC_QTY").val(0);
    }

  }
  else if(TYPE =="QC_OK_QTY" && QC_OK_QTY !=""){

    if( (parseFloat(REJECTED_QTY)+parseFloat(QC_OK_QTY)) > parseFloat(GRN_REC_QTY) ){
      $("#QC_OK_QTY").val('');
      $("#FocusId").val(TYPE);        
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('QC OK Qty Should not greater then GRN Received Qty.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
    }
    else{

      var Pending =  parseFloat(GRN_REC_QTY) - (parseFloat(QC_OK_QTY) + parseFloat(REJECTED_QTY))
      $("#PENDING_QC_QTY").val(Pending);

    }

  }

}

function AverageTotal(id){

  var ROW_ID        = id.split('_').pop();
  var STD_TYPE      = $("#STD_TYPE_"+ROW_ID).val();
  var STD_VALUE     = $("#STD_VALUE_"+ROW_ID).val();
  var AVG_OBS_VALUE = $("#AVG_OBS_VALUE_"+ROW_ID).val();
  var REJECTED      = $("#REJECTED_"+ROW_ID).val();

  var OBS_VALUE1    = $.trim($("#OBS_VALUE1_"+ROW_ID).val());
  var OBS_VALUE2    = $.trim($("#OBS_VALUE2_"+ROW_ID).val());
  var OBS_VALUE3    = $.trim($("#OBS_VALUE3_"+ROW_ID).val());
  var OBS_VALUE4    = $.trim($("#OBS_VALUE4_"+ROW_ID).val());
  var OBS_VALUE5    = $.trim($("#OBS_VALUE5_"+ROW_ID).val());
  var OBS_VALUE6    = $.trim($("#OBS_VALUE6_"+ROW_ID).val());
  var OBS_VALUE7    = $.trim($("#OBS_VALUE7_"+ROW_ID).val());
  var OBS_VALUE8    = $.trim($("#OBS_VALUE8_"+ROW_ID).val());
  var OBS_VALUE9    = $.trim($("#OBS_VALUE9_"+ROW_ID).val());
  var OBS_VALUE10   = $.trim($("#OBS_VALUE10_"+ROW_ID).val());

  var ARRAY_DATA    = [OBS_VALUE1,OBS_VALUE2,OBS_VALUE3,OBS_VALUE4,OBS_VALUE5,OBS_VALUE6,OBS_VALUE7,OBS_VALUE8,OBS_VALUE9,OBS_VALUE10];
  var ARRAY_DATA    = ARRAY_DATA.filter(item => item);
  var ARRAY_LENGTH  = ARRAY_DATA.length;

  var LOGICAL_DATA    = [OBS_VALUE1,OBS_VALUE2,OBS_VALUE3,OBS_VALUE4,OBS_VALUE5,OBS_VALUE6,OBS_VALUE7,OBS_VALUE8,OBS_VALUE9,OBS_VALUE10];
  var LOGICAL_DATA    = LOGICAL_DATA.filter(item => item);
  var LOGICAL_LENGTH  = LOGICAL_DATA.length;

  $("#REJECTED_"+ROW_ID).val('');
  
  if(STD_TYPE =="Text"){
    var LogYes=[];
    var LogNo=[];
    for($i=0;$i<LOGICAL_LENGTH;$i++){
      if(LOGICAL_DATA[$i].toLowerCase() =='yes'){
       LogYes.push(LOGICAL_DATA[$i]);
      }
      else{
      LogNo.push(LOGICAL_DATA[$i]);
      }

      if(LogYes.length > LogNo.length){
        $("#AVG_OBS_VALUE_"+ROW_ID).val('Yes');
      }else{
        $("#AVG_OBS_VALUE_"+ROW_ID).val('No'); 
      }

    }

      if(STD_VALUE.toLowerCase() == AVG_OBS_VALUE.toLowerCase()){
        $("#REJECTED_"+ROW_ID).val('No');
      }
      else{
        $("#REJECTED_"+ROW_ID).val('Yes');
      }
    

  }


  else if(STD_TYPE =="Logical"){ 
    var LogYes=[];
    var LogNo=[];
    var STD_VALUE1 = STD_VALUE;
    //alert(STD_VALUE1);
    for($i=0;$i<LOGICAL_LENGTH;$i++){
      if(LOGICAL_DATA[$i].toLowerCase() =='yes'){
       LogYes.push(LOGICAL_DATA[$i]);
      }
      else{
      LogNo.push(LOGICAL_DATA[$i]);
      }

      if(LogYes.length > LogNo.length){
        $("#AVG_OBS_VALUE_"+ROW_ID).val('Yes');
      }else{
        $("#AVG_OBS_VALUE_"+ROW_ID).val('No'); 
      }

      //alert(LogYes.length);

      if(LogYes.length > LogNo.length){
      $("#REJECTED_"+ROW_ID).val('No');
      }
      else{
        $("#REJECTED_"+ROW_ID).val('Yes');
      }
      

    }

  
  }


  else if(STD_TYPE =="Numeric Value"){

    $("#AVG_OBS_VALUE_"+ROW_ID).val('');

    var TOTAL_VALUE         = getArraySum(ARRAY_DATA)/ARRAY_LENGTH;
    var TOTAL_AVG_OBS_VALUE = parseFloat(TOTAL_VALUE).toFixed(2);

    if(ARRAY_LENGTH > 0){
      $("#AVG_OBS_VALUE_"+ROW_ID).val(TOTAL_AVG_OBS_VALUE);
    }


    if(parseFloat(TOTAL_AVG_OBS_VALUE) == parseFloat(STD_VALUE)){
      $("#REJECTED_"+ROW_ID).val('No');
    }
    else{
      $("#REJECTED_"+ROW_ID).val('Yes');
    }

  } 
  else if(STD_TYPE =="Range In Value"){

    $("#AVG_OBS_VALUE_"+ROW_ID).val('');

    var TOTAL_VALUE         = getArraySum(ARRAY_DATA)/ARRAY_LENGTH;
    var TOTAL_AVG_OBS_VALUE = parseFloat(TOTAL_VALUE).toFixed(2);
    
    if(ARRAY_LENGTH > 0){
      $("#AVG_OBS_VALUE_"+ROW_ID).val(TOTAL_AVG_OBS_VALUE);
    }

    var STD_VALUE        = STD_VALUE.split('-');
    var STD_VALU1        = STD_VALUE[0];
    var STD_VALU2        = STD_VALUE[1];

    if(parseFloat(TOTAL_VALUE) >= parseFloat(STD_VALU1) && parseFloat(TOTAL_VALUE) <= parseFloat(STD_VALU2) ){
      $("#REJECTED_"+ROW_ID).val('No');  
    }
    else{
      $("#REJECTED_"+ROW_ID).val('Yes');
    }

  } 

  else if(STD_TYPE =="Range Percent"){

    $("#AVG_OBS_VALUE_"+ROW_ID).val('');

    var TOTAL_VALUE         = getArraySum(ARRAY_DATA)/ARRAY_LENGTH;
    var TOTAL_AVG_OBS_VALUE = parseFloat(TOTAL_VALUE).toFixed(2);

    if(ARRAY_LENGTH > 0){
      $("#AVG_OBS_VALUE_"+ROW_ID).val(TOTAL_AVG_OBS_VALUE);
    }

    var STD_VALUE        = STD_VALUE.split('-');
    var STD_VALU1        = STD_VALUE[0];
    var STD_VALU2        = STD_VALUE[1];

    if(parseFloat(TOTAL_VALUE) >= parseFloat(STD_VALU1) && parseFloat(TOTAL_VALUE) <= parseFloat(STD_VALU2) ){
      $("#REJECTED_"+ROW_ID).val('No');  
    }
    else{
      $("#REJECTED_"+ROW_ID).val('Yes');
    }

  } 

}

function getArraySum(a){
  var total=0;
  for(var i in a) { 
      total += parseFloat(a[i]);
  }
  return total;
}
</script>
@endpush