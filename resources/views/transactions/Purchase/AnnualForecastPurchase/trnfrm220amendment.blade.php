
@extends('layouts.app')
@section('content')
<!-- <form id="frm_trn_se" onsubmit="return validateForm()"  method="POST" class="needs-validation"  >     -->

    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                  <a href="{{route('transaction',[220,'index'])}}" class="btn singlebt">Annual Forecast Purchase (AFP)</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
                        <button class="btn topnavbt" id="btnSaveSE" ><i class="fa fa-edit"></i> Save</button>
                        <button style="display:none" class="btn topnavbt buttonload"> <i class="fa fa-refresh fa-spin"></i> {{Session::get('save')}}</button>
                        <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt" id="btnUndo"  ><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove" disabled><i class="fa fa-lock"></i> Approved</button>
                        <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                        <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                </div>
            </div><!--row-->
    </div>

<form id="frm_trn_se" onsubmit="return validateForm()"  method="POST" class="needs-validation"  >
    @csrf
    {{isset($objSE->SEQID[0]) ? method_field('PUT') : '' }}
    <div class="container-fluid filter">

<div class="inner-form">

  <div class="row">
    <div class="col-lg-1 pl"><p>AFPA No</p></div>
    <div class="col-lg-1 pl">
      <input type="text" name="AFSANO" id="AFSANO" value="{{ $ANumber  }}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase" autofocus >
      <input type="text" name="AFSNO" id="AFSNO" value="{{ $objSE->AFP_NO }}" class="form-control mandatory"  autocomplete="off" readonly >
      <input type="hidden" name="AFSID_REF" id="AFSID_REF" value="{{ $objSE->AFPID }}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase" autofocus >
    </div>    
    <div class="col-lg-1 pl col-md-offset-1"><p>AFPA Date</p></div>
    <div class="col-lg-2 pl">
          <input type="date" name="AFSADT" id="AFSADT"  onchange="checkPeriodClosing(220,this.value,1)" value="{{ $objSE->AFP_DT }}" class="form-control mandatory AFSDT"  placeholder="dd/mm/yyyy" >
          </div>
    
 
    
    <div class="col-lg-2 pl"><p>Reason to amend AFP</p></div>
    <div class="col-lg-4 pl">
    
      <input type="text" name="REASON_AFSA" id="REASON_AFSA" value="" class="form-control mandatory"  autocomplete="off"  style="text-transform:uppercase" autofocus >
    </div>
  </div>


  <div class="row">
    <div class="col-lg-1 pl"><p>AFP No</p></div>
    <div class="col-lg-1 pl">
              <input type="text" name="AFSNO" id="AFSNO" value="{{ $objSE->AFP_NO  }}" class="form-control mandatory" disabled  autocomplete="off" readonly style="text-transform:uppercase" autofocus >
           
    </div>
    
    <div class="col-lg-1 pl col-md-offset-1"><p>AFP Date</p></div>
    <div class="col-lg-2 pl">
          <input type="date" name="AFSDT" id="AFSDT" value="{{ $objSE->AFP_DT }}" class="form-control mandatory " disabled placeholder="dd/mm/yyyy" >
          </div>
    
    <div class="col-lg-1 pl"><p> Department	</p></div>
    <div class="col-lg-2 pl">
    <input type="text" name="DEPTID_NAME" id="DEPTID_NAME" value="{{ $objSE->NAME }}"  class="form-control mandatory" disabled autocomplete="off" readonly style="text-transform:uppercase" autofocus >
      <input type="hidden" name="DEPID_REF" id="DEPID_REF" value="{{ $objSE->DEPID_REF }}" class="form-control mandatory"  autocomplete="off"  style="text-transform:uppercase" autofocus >
    </div>
    
    <div class="col-lg-1 pl"><p>Financial Year</p></div>
    <div class="col-lg-2 pl">
    <input type="text" name="FYID_NAME" id="FYID_NAME" value="{{ $objSE->FYDESCRIPTION }}" disabled class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase" autofocus >
      <input type="hidden" name="FYID_REF" id="FYID_REF" value="{{ $objSE->FYID_REF }}" class="form-control mandatory"  autocomplete="off"  style="text-transform:uppercase" autofocus >
    </div>
  </div>

  

  
</div>

<div class="container-fluid">

  <div class="row">
    <ul class="nav nav-tabs">
      <li class="active"><a data-toggle="tab" href="#Material">Material </a></li> 
    </ul>

    
    <div class="tab-content">
                              <div id="Material" class="tab-pane fade in active">
                                  <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:280px;margin-top:10px;" >
                                      <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                                          <thead id="thead1"  style="position: sticky;top: 0">
                                                  
                                                  <tr>
                                                    <th colspan="7"></th>
                                                    <th colspan="9">April</th>
                                                    <th colspan="9">May</th>
                                                    <th colspan="9">June</th>
                                                    <th colspan="9">July</th>
                                                    <th colspan="9">August</th>
                                                    <th colspan="9">September</th>
                                                    <th colspan="9">October</th>
                                                    <th colspan="9">November</th>
                                                    <th colspan="9">December</th>
                                                    <th colspan="9">January</th>
                                                    <th colspan="9">February</th>
                                                    <th colspan="9">March</th>
                                                    {{-- <th colspan="7">Financial</th> --}}
                                                    <th ></th>                
                                                      
                                                  
                                                  </tr>
                                                  <tr>
                                                      <th width="10%">Business Unit	<input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1"></th>
                                                      <th width="10%">Item Code</th>
                                                      <th  width="15%">Item Name</th>
                                                      <th>Customer</th>
                                                      <th>Part No</th>
                                                      <th>Main UOM</th>
                                                      <th width="15%">Item Specification</th>
                                                   
                                                      {{-- april --}}
                                                      <th>Opening</th>
                                                      <th>Sales</th>
                                                      <th>Purchase<br>(Sales-Opening)</th>
                                                      <th>Add Purchase</th>
                                                      <th>To Be procured</th>
                                                      <th>Inventory</th>
                                                      <th>Number of<br>Inventory days</th>
                                                      <th>Rate</th>
                                                      <th>Purchase<br>Value</th>
                                                      {{-- may --}}
                                                      <th>Opening</th>
                                                      <th>Sales</th>
                                                      <th>Purchase<br>(Sales-Opening)</th>
                                                      <th>Add Purchase</th>
                                                      <th>To Be procured</th>
                                                      <th>Inventory</th>
                                                      <th>Number of<br>Inventory days</th>
                                                      <th>Rate</th>
                                                      <th>Purchase<br>Value</th>
                                                       {{-- jun --}}
                                                       <th>Opening</th>
                                                      <th>Sales</th>
                                                      <th>Purchase<br>(Sales-Opening)</th>
                                                      <th>Add Purchase</th>
                                                      <th>To Be procured</th>
                                                      <th>Inventory</th>
                                                      <th>Number of<br>Inventory days</th>
                                                      <th>Rate</th>
                                                      <th>Purchase<br>Value</th>
                                                      
                                                      {{-- july --}}
                                                       <th>Opening</th>
                                                      <th>Sales</th>
                                                      <th>Purchase<br>(Sales-Opening)</th>
                                                      <th>Add Purchase</th>
                                                      <th>To Be procured</th>
                                                      <th>Inventory</th>
                                                      <th>Number of<br>Inventory days</th>
                                                      <th>Rate</th>
                                                      <th>Purchase<br>Value</th>

                                                      {{-- aug --}}
                                                      <th>Opening</th>
                                                      <th>Sales</th>
                                                      <th>Purchase<br>(Sales-Opening)</th>
                                                      <th>Add Purchase</th>
                                                      <th>To Be procured</th>
                                                      <th>Inventory</th>
                                                      <th>Number of<br>Inventory days</th> 
                                                      <th>Rate</th>
                                                      <th>Purchase<br>Value</th>

                                                      {{-- sep --}}
                                                      <th>Opening</th>
                                                      <th>Sales</th>
                                                      <th>Purchase<br>(Sales-Opening)</th>
                                                      <th>Add Purchase</th>
                                                      <th>To Be procured</th>
                                                      <th>Inventory</th>
                                                      <th>Number of<br>Inventory days</th>
                                                      <th>Rate</th>
                                                      <th>Purchase<br>Value</th>

                                                      {{-- oct --}}
                                                      <th>Opening</th>
                                                      <th>Sales</th>
                                                      <th>Purchase<br>(Sales-Opening)</th>
                                                      <th>Add Purchase</th>
                                                      <th>To Be procured</th>
                                                      <th>Inventory</th>
                                                      <th>Number of<br>Inventory days</th>
                                                      <th>Rate</th>
                                                      <th>Purchase<br>Value</th>

                                                      {{-- nov --}}
                                                      <th>Opening</th>
                                                      <th>Sales</th>
                                                      <th>Purchase<br>(Sales-Opening)</th>
                                                      <th>Add Purchase</th>
                                                      <th>To Be procured</th>
                                                      <th>Inventory</th>
                                                      <th>Number of<br>Inventory days</th>
                                                      <th>Rate</th>
                                                      <th>Purchase<br>Value</th>

                                                      {{-- dec --}}
                                                      <th>Opening</th>
                                                      <th>Sales</th>
                                                      <th>Purchase<br>(Sales-Opening)</th>
                                                      <th>Add Purchase</th>
                                                      <th>To Be procured</th>
                                                      <th>Inventory</th>
                                                      <th>Number of<br>Inventory days</th>
                                                      <th>Rate</th>
                                                      <th>Purchase<br>Value</th>

                                                       {{-- jan --}}
                                                       <th>Opening</th>
                                                       <th>Sales</th>
                                                       <th>Purchase<br>(Sales-Opening)</th>
                                                       <th>Add Purchase</th>
                                                       <th>To Be procured</th>
                                                       <th>Inventory</th>
                                                       <th>Number of<br>Inventory days</th>
                                                       <th>Rate</th>
                                                      <th>Purchase<br>Value</th>

                                                        {{-- feb --}}
                                                      <th>Opening</th>
                                                      <th>Sales</th>
                                                      <th>Purchase<br>(Sales-Opening)</th>
                                                      <th>Add Purchase</th>
                                                      <th>To Be procured</th>
                                                      <th>Inventory</th>
                                                      <th>Number of<br>Inventory days</th> 
                                                      <th>Rate</th>
                                                      <th>Purchase<br>Value</th>

                                                       {{-- mar --}}
                                                       <th>Opening</th>
                                                       <th>Sales</th>
                                                       <th>Purchase<br>(Sales-Opening)</th>
                                                       <th>Add Purchase</th>
                                                       <th>To Be procured</th>
                                                       <th>Inventory</th>
                                                       <th>Number of<br>Inventory days</th>
                                                       <th>Rate</th>
                                                      <th>Purchase<br>Value</th>

                                                      <th  width="6%">Action</th>
                                                  </tr>
                                          </thead>
                                          <tbody>
                                          @if(!empty($objSEMAT))
                                @foreach($objSEMAT as $key => $row) 
                                              <tr  class="participantRow">
                                              <td style="text-align:center;">
                         
      
                         <input type="text" name={{"BUID_REF_".$key}} id={{"BUID_REF_".$key}}  value="{{$row->BUCODE}}" onClick="get_section($(this).attr('id'))" class="form-control mandatory" style="width:91px" readonly tabindex="1" />
                        
                       
                             
                         </td>
                         <td hidden> <input type="text" name={{"REF_BUID_".$key}} id={{"REF_BUID_".$key}} value="{{$row->BUID_REF}}" /></td>
                         
                      
                                                 <!-- <td style="text-align:center;" >
                                                  <input type="text" name="txtSO_popup_0" id="txtSO_popup_0" class="form-control"  autocomplete="off"  readonly/>
                                                  <td hidden><input type="hidden" name="SOID_REF_0" id="SOID_REF_0" class="form-control" autocomplete="off" /></td>
                                                  <td hidden><input type="hidden" name="SQID_REF_0" id="SQID_REF_0" class="form-control" autocomplete="off" /></td>
                                                  <td hidden><input type="hidden" name="SEQID_REF_0" id="SEQID_REF_0" class="form-control" autocomplete="off" /></td></td>
                                                  -->
                                                  <td><input type="text" name={{"popupITEMID_".$key}} id={{"popupITEMID_".$key}}  class="form-control" value="{{$row->ICODE}}"  autocomplete="off"  readonly/></td>
                                                  <td hidden><input type="hidden" name={{"ITEMID_REF_".$key}} id={{"ITEMID_REF_".$key}}  class="form-control" value="{{$row->ITEMID_REF}}" autocomplete="off" /></td>
                                                  <td><input type="text" name={{"ItemName_".$key}} id={{"ItemName_".$key}}  class="form-control" value="{{$row->NAME}}"  autocomplete="off"  readonly/></td>
                                                    <td style="text-align:center;">            
                                        
                                <input type="text" name={{"CID_REF_".$key}} id={{"CID_REF_".$key}}  class="form-control mandatory" value="{{$row->CID_REF}}" style="width:91px" readonly tabindex="1" />                       
                                                            </td>
                               <td hidden> <input type="text" name={{"CUSTOMERID_REF_".$key}} id={{"CUSTOMERID_REF_".$key}}  value="{{$row->CID_REF}}" /></td>
  <td><input type="text" name={{"ItemPartno_".$key}} id={{"ItemPartno_".$key}}   class="form-control three-digits" value="{{$row->PARTNO}}"  autocomplete="off" style="width: 82px;" readonly/></td>
  <td hidden><input type="text" name={{"itemuom_".$key}} id={{"itemuom_".$key}}   class="form-control"  autocomplete="off" value="{{$row->UOMID_REF}}" readonly/></td>
  <td><input type="text" name={{"itemmain_uom".$key}} id={{"itemmain_uom".$key}} value="{{ $row->UOMCODE }} - {{ $row->DESCRIPTIONS }}" class="form-control"  autocomplete="off"  readonly/></td>

  <td><input type="text" name={{"Itemspec_".$key}} id={{"Itemspec_".$key}}  class="form-control"  value="{{$row->ITEMSPECI}}" autocomplete="off"  /></td>
                                      
                          {{-- <td><input type="text" name={{"APRIL_QTY_".$key}} id={{"APRIL_QTY_".$key}} class="form-control three-digits" value="{{$row->MONTH1_QTY == '.000' ? '' : $row->MONTH1_QTY}}" maxlength="13" style="width: 65px;" autocomplete="off"  /></td> --}}
  <td><input type="text" name={{"MONTH1_OP_".$key}} id={{"MONTH1_OP_".$key}} class="form-control three-digits" value="{{$row->MONTH1_OP}}" readonly  style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH1_SL_".$key}} id={{"MONTH1_SL_".$key}} class="form-control three-digits" value="{{$row->MONTH1_SL}}" readonly  style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH1_PR_".$key}} id={{"MONTH1_PR_".$key}} class="form-control three-digits" value="{{$row->MONTH1_PR}}" readonly  style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH1_AP_".$key}} id={{"MONTH1_AP_".$key}} class="form-control three-digits" value="{{$row->MONTH1_AP}}"          maxlength="13" style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH1_TP_".$key}} id={{"MONTH1_TP_".$key}} class="form-control three-digits" value="{{$row->MONTH1_TP}}"  readonly  style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH1_IV_".$key}} id={{"MONTH1_IV_".$key}} class="form-control three-digits" value="{{$row->MONTH1_IV}}" readonly   style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH1_ND_".$key}} id={{"MONTH1_ND_".$key}} class="form-control three-digits" value="{{$row->MONTH1_ND}}" readonly   style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH1_RT_".$key}} id={{"MONTH1_RT_".$key}} class="form-control five-digits"  value="{{$row->MONTH1_RT}}"  maxlength="13" style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH1_PV_".$key}} id={{"MONTH1_PV_".$key}} class="form-control three-digits" value="{{$row->MONTH1_PV}}"  readonly      maxlength="13" style="width: 100px;" autocomplete="off"  /></td>
                                                            
  
  <td><input type="text" name={{"MONTH2_OP_".$key}}  id={{"MONTH2_OP_".$key}}  class="form-control three-digits" value="{{$row->MONTH2_OP}}"  readonly  style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH2_SL_".$key}}  id={{"MONTH2_SL_".$key}}  class="form-control three-digits" value="{{$row->MONTH2_SL}}"   readonly  style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH2_PR_".$key}}  id={{"MONTH2_PR_".$key}}  class="form-control three-digits" value="{{$row->MONTH2_PR}}"    readonly  style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH2_AP_".$key}}  id={{"MONTH2_AP_".$key}}  class="form-control three-digits"  value="{{$row->MONTH2_AP}}"                             maxlength="13" style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH2_TP_".$key}}  id={{"MONTH2_TP_".$key}}  class="form-control three-digits"  value="{{$row->MONTH2_TP}}"      readonly  style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH2_IV_".$key}}  id={{"MONTH2_IV_".$key}}  class="form-control three-digits"  value="{{$row->MONTH2_IV}}"  readonly   style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH2_ND_".$key}}  id={{"MONTH2_ND_".$key}}  class="form-control three-digits"  value="{{$row->MONTH2_ND}}"  readonly   style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH2_RT_".$key}}  id={{"MONTH2_RT_".$key}}  class="form-control five-digits"   value="{{$row->MONTH2_RT}}"    maxlength="13" style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH2_PV_".$key}}  id={{"MONTH2_PV_".$key}}  class="form-control three-digits"  value="{{$row->MONTH2_PV}}"  readonly      maxlength="13" style="width: 100px;" autocomplete="off"  /></td>
                                                   
  <td><input type="text" name={{"MONTH3_OP_".$key}}  id={{"MONTH3_OP_".$key}}  class="form-control three-digits" value="{{$row->MONTH3_OP}}"  readonly  style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH3_SL_".$key}}  id={{"MONTH3_SL_".$key}}  class="form-control three-digits" value="{{$row->MONTH3_SL}}"   readonly  style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH3_PR_".$key}}  id={{"MONTH3_PR_".$key}}  class="form-control three-digits" value="{{$row->MONTH3_PR}}"    readonly  style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH3_AP_".$key}}  id={{"MONTH3_AP_".$key}}  class="form-control three-digits"  value="{{$row->MONTH3_AP}}"                             maxlength="13" style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH3_TP_".$key}}  id={{"MONTH3_TP_".$key}}  class="form-control three-digits"  value="{{$row->MONTH3_TP}}"      readonly  style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH3_IV_".$key}}  id={{"MONTH3_IV_".$key}}  class="form-control three-digits"  value="{{$row->MONTH3_IV}}"  readonly   style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH3_ND_".$key}}  id={{"MONTH3_ND_".$key}}  class="form-control three-digits"  value="{{$row->MONTH3_ND}}"  readonly   style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH3_RT_".$key}}  id={{"MONTH3_RT_".$key}}  class="form-control five-digits"   value="{{$row->MONTH3_RT}}"    maxlength="13" style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH3_PV_".$key}}  id={{"MONTH3_PV_".$key}}  class="form-control three-digits"  value="{{$row->MONTH3_PV}}"  readonly      maxlength="13" style="width: 100px;" autocomplete="off"  /></td>
                                                
  <td><input type="text" name={{"MONTH4_OP_".$key}}  id={{"MONTH4_OP_".$key}}  class="form-control three-digits" value="{{$row->MONTH4_OP}}"  readonly  style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH4_SL_".$key}}  id={{"MONTH4_SL_".$key}}  class="form-control three-digits" value="{{$row->MONTH4_SL}}"   readonly  style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH4_PR_".$key}}  id={{"MONTH4_PR_".$key}}  class="form-control three-digits" value="{{$row->MONTH4_PR}}"    readonly  style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH4_AP_".$key}}  id={{"MONTH4_AP_".$key}}  class="form-control three-digits"  value="{{$row->MONTH4_AP}}"                             maxlength="13" style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH4_TP_".$key}}  id={{"MONTH4_TP_".$key}}  class="form-control three-digits"  value="{{$row->MONTH4_TP}}"      readonly  style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH4_IV_".$key}}  id={{"MONTH4_IV_".$key}}  class="form-control three-digits"  value="{{$row->MONTH4_IV}}"  readonly   style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH4_ND_".$key}}  id={{"MONTH4_ND_".$key}}  class="form-control three-digits"  value="{{$row->MONTH4_ND}}"  readonly   style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH4_RT_".$key}}  id={{"MONTH4_RT_".$key}}  class="form-control five-digits"   value="{{$row->MONTH4_RT}}"    maxlength="13" style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH4_PV_".$key}}  id={{"MONTH4_PV_".$key}}  class="form-control three-digits"  value="{{$row->MONTH4_PV}}"  readonly      maxlength="13" style="width: 100px;" autocomplete="off"  /></td>
                                                      
  <td><input type="text" name={{"MONTH5_OP_".$key}}  id={{"MONTH5_OP_".$key}}  class="form-control three-digits" value="{{$row->MONTH5_OP}}"  readonly  style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH5_SL_".$key}}  id={{"MONTH5_SL_".$key}}  class="form-control three-digits" value="{{$row->MONTH5_SL}}"   readonly  style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH5_PR_".$key}}  id={{"MONTH5_PR_".$key}}  class="form-control three-digits" value="{{$row->MONTH5_PR}}"    readonly  style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH5_AP_".$key}}  id={{"MONTH5_AP_".$key}}  class="form-control three-digits"  value="{{$row->MONTH5_AP}}"                             maxlength="13" style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH5_TP_".$key}}  id={{"MONTH5_TP_".$key}}  class="form-control three-digits"  value="{{$row->MONTH5_TP}}"      readonly  style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH5_IV_".$key}}  id={{"MONTH5_IV_".$key}}  class="form-control three-digits"  value="{{$row->MONTH5_IV}}"  readonly   style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH5_ND_".$key}}  id={{"MONTH5_ND_".$key}}  class="form-control three-digits"  value="{{$row->MONTH5_ND}}"  readonly   style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH5_RT_".$key}}  id={{"MONTH5_RT_".$key}}  class="form-control five-digits"   value="{{$row->MONTH5_RT}}"    maxlength="13" style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH5_PV_".$key}}  id={{"MONTH5_PV_".$key}}  class="form-control three-digits"  value="{{$row->MONTH5_PV}}"  readonly      maxlength="13" style="width: 100px;" autocomplete="off"  /></td>
    
  <td><input type="text" name={{"MONTH6_OP_".$key}}  id={{"MONTH6_OP_".$key}}  class="form-control three-digits" value="{{$row->MONTH6_OP}}"  readonly  style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH6_SL_".$key}}  id={{"MONTH6_SL_".$key}}  class="form-control three-digits" value="{{$row->MONTH6_SL}}"   readonly  style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH6_PR_".$key}}  id={{"MONTH6_PR_".$key}}  class="form-control three-digits" value="{{$row->MONTH6_PR}}"    readonly  style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH6_AP_".$key}}  id={{"MONTH6_AP_".$key}}  class="form-control three-digits"  value="{{$row->MONTH6_AP}}"                             maxlength="13" style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH6_TP_".$key}}  id={{"MONTH6_TP_".$key}}  class="form-control three-digits"  value="{{$row->MONTH6_TP}}"      readonly  style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH6_IV_".$key}}  id={{"MONTH6_IV_".$key}}  class="form-control three-digits"  value="{{$row->MONTH6_IV}}"  readonly   style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH6_ND_".$key}}  id={{"MONTH6_ND_".$key}}  class="form-control three-digits"  value="{{$row->MONTH6_ND}}"  readonly   style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH6_RT_".$key}}  id={{"MONTH6_RT_".$key}}  class="form-control five-digits"   value="{{$row->MONTH6_RT}}"    maxlength="13" style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH6_PV_".$key}}  id={{"MONTH6_PV_".$key}}  class="form-control three-digits"  value="{{$row->MONTH6_PV}}"  readonly      maxlength="13" style="width: 100px;" autocomplete="off"  /></td>
    

  <td><input type="text" name={{"MONTH7_OP_".$key}}  id={{"MONTH7_OP_".$key}}  class="form-control three-digits" value="{{$row->MONTH7_OP}}"  readonly  style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH7_SL_".$key}}  id={{"MONTH7_SL_".$key}}  class="form-control three-digits" value="{{$row->MONTH7_SL}}"   readonly  style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH7_PR_".$key}}  id={{"MONTH7_PR_".$key}}  class="form-control three-digits" value="{{$row->MONTH7_PR}}"    readonly  style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH7_AP_".$key}}  id={{"MONTH7_AP_".$key}}  class="form-control three-digits"  value="{{$row->MONTH7_AP}}"                             maxlength="13" style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH7_TP_".$key}}  id={{"MONTH7_TP_".$key}}  class="form-control three-digits"  value="{{$row->MONTH7_TP}}"      readonly  style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH7_IV_".$key}}  id={{"MONTH7_IV_".$key}}  class="form-control three-digits"  value="{{$row->MONTH7_IV}}"  readonly   style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH7_ND_".$key}}  id={{"MONTH7_ND_".$key}}  class="form-control three-digits"  value="{{$row->MONTH7_ND}}"  readonly   style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH7_RT_".$key}}  id={{"MONTH7_RT_".$key}}  class="form-control five-digits"   value="{{$row->MONTH7_RT}}"    maxlength="13" style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH7_PV_".$key}}  id={{"MONTH7_PV_".$key}}  class="form-control three-digits"  value="{{$row->MONTH7_PV}}"  readonly      maxlength="13" style="width: 100px;" autocomplete="off"  /></td>
    

  <td><input type="text" name={{"MONTH8_OP_".$key}}  id={{"MONTH8_OP_".$key}}  class="form-control three-digits" value="{{$row->MONTH8_OP}}"  readonly  style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH8_SL_".$key}}  id={{"MONTH8_SL_".$key}}  class="form-control three-digits" value="{{$row->MONTH8_SL}}"   readonly  style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH8_PR_".$key}}  id={{"MONTH8_PR_".$key}}  class="form-control three-digits" value="{{$row->MONTH8_PR}}"    readonly  style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH8_AP_".$key}}  id={{"MONTH8_AP_".$key}}  class="form-control three-digits"  value="{{$row->MONTH8_AP}}"                             maxlength="13" style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH8_TP_".$key}}  id={{"MONTH8_TP_".$key}}  class="form-control three-digits"  value="{{$row->MONTH8_TP}}"      readonly  style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH8_IV_".$key}}  id={{"MONTH8_IV_".$key}}  class="form-control three-digits"  value="{{$row->MONTH8_IV}}"  readonly   style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH8_ND_".$key}}  id={{"MONTH8_ND_".$key}}  class="form-control three-digits"  value="{{$row->MONTH8_ND}}"  readonly   style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH8_RT_".$key}}  id={{"MONTH8_RT_".$key}}  class="form-control five-digits"   value="{{$row->MONTH8_RT}}"    maxlength="13" style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH8_PV_".$key}}  id={{"MONTH8_PV_".$key}}  class="form-control three-digits"  value="{{$row->MONTH8_PV}}"  readonly      maxlength="13" style="width: 100px;" autocomplete="off"  /></td>
    

  <td><input type="text" name={{"MONTH9_OP_".$key}}  id={{"MONTH9_OP_".$key}}  class="form-control three-digits" value="{{$row->MONTH9_OP}}"  readonly  style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH9_SL_".$key}}  id={{"MONTH9_SL_".$key}}  class="form-control three-digits" value="{{$row->MONTH9_SL}}"   readonly  style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH9_PR_".$key}}  id={{"MONTH9_PR_".$key}}  class="form-control three-digits" value="{{$row->MONTH9_PR}}"    readonly  style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH9_AP_".$key}}  id={{"MONTH9_AP_".$key}}  class="form-control three-digits"  value="{{$row->MONTH9_AP}}"                             maxlength="13" style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH9_TP_".$key}}  id={{"MONTH9_TP_".$key}}  class="form-control three-digits"  value="{{$row->MONTH9_TP}}"      readonly  style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH9_IV_".$key}}  id={{"MONTH9_IV_".$key}}  class="form-control three-digits"  value="{{$row->MONTH9_IV}}"  readonly   style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH9_ND_".$key}}  id={{"MONTH9_ND_".$key}}  class="form-control three-digits"  value="{{$row->MONTH9_ND}}"  readonly   style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH9_RT_".$key}}  id={{"MONTH9_RT_".$key}}  class="form-control five-digits"   value="{{$row->MONTH9_RT}}"    maxlength="13" style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH9_PV_".$key}}  id={{"MONTH9_PV_".$key}}  class="form-control three-digits"  value="{{$row->MONTH9_PV}}"  readonly      maxlength="13" style="width: 100px;" autocomplete="off"  /></td>
    
  <td><input type="text" name={{"MONTH10_OP_".$key}}  id={{"MONTH10_OP_".$key}}  class="form-control three-digits" value="{{$row->MONTH10_OP}}"  readonly  style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH10_SL_".$key}}  id={{"MONTH10_SL_".$key}}  class="form-control three-digits" value="{{$row->MONTH10_SL}}"   readonly  style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH10_PR_".$key}}  id={{"MONTH10_PR_".$key}}  class="form-control three-digits" value="{{$row->MONTH10_PR}}"    readonly  style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH10_AP_".$key}}  id={{"MONTH10_AP_".$key}}  class="form-control three-digits"  value="{{$row->MONTH10_AP}}"                             maxlength="13" style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH10_TP_".$key}}  id={{"MONTH10_TP_".$key}}  class="form-control three-digits"  value="{{$row->MONTH10_TP}}"      readonly  style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH10_IV_".$key}}  id={{"MONTH10_IV_".$key}}  class="form-control three-digits"  value="{{$row->MONTH10_IV}}"  readonly   style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH10_ND_".$key}}  id={{"MONTH10_ND_".$key}}  class="form-control three-digits"  value="{{$row->MONTH10_ND}}"  readonly   style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH10_RT_".$key}}  id={{"MONTH10_RT_".$key}}  class="form-control five-digits"   value="{{$row->MONTH10_RT}}"    maxlength="13" style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH10_PV_".$key}}  id={{"MONTH10_PV_".$key}}  class="form-control three-digits"  value="{{$row->MONTH10_PV}}"  readonly      maxlength="13" style="width: 100px;" autocomplete="off"  /></td>
    
  <td><input type="text" name={{"MONTH11_OP_".$key}}  id={{"MONTH11_OP_".$key}}  class="form-control three-digits" value="{{$row->MONTH11_OP}}"  readonly  style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH11_SL_".$key}}  id={{"MONTH11_SL_".$key}}  class="form-control three-digits" value="{{$row->MONTH11_SL}}"   readonly  style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH11_PR_".$key}}  id={{"MONTH11_PR_".$key}}  class="form-control three-digits" value="{{$row->MONTH11_PR}}"    readonly  style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH11_AP_".$key}}  id={{"MONTH11_AP_".$key}}  class="form-control three-digits"  value="{{$row->MONTH11_AP}}"                             maxlength="13" style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH11_TP_".$key}}  id={{"MONTH11_TP_".$key}}  class="form-control three-digits"  value="{{$row->MONTH11_TP}}"      readonly  style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH11_IV_".$key}}  id={{"MONTH11_IV_".$key}}  class="form-control three-digits"  value="{{$row->MONTH11_IV}}"  readonly   style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH11_ND_".$key}}  id={{"MONTH11_ND_".$key}}  class="form-control three-digits"  value="{{$row->MONTH11_ND}}"  readonly   style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH11_RT_".$key}}  id={{"MONTH11_RT_".$key}}  class="form-control five-digits"   value="{{$row->MONTH11_RT}}"    maxlength="13" style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH11_PV_".$key}}  id={{"MONTH11_PV_".$key}}  class="form-control three-digits"  value="{{$row->MONTH11_PV}}"  readonly      maxlength="13" style="width: 100px;" autocomplete="off"  /></td>
    
  <td><input type="text" name={{"MONTH12_OP_".$key}}  id={{"MONTH12_OP_".$key}}  class="form-control three-digits" value="{{$row->MONTH12_OP}}"  readonly  style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH12_SL_".$key}}  id={{"MONTH12_SL_".$key}}  class="form-control three-digits" value="{{$row->MONTH12_SL}}"   readonly  style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH12_PR_".$key}}  id={{"MONTH12_PR_".$key}}  class="form-control three-digits" value="{{$row->MONTH12_PR}}"    readonly  style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH12_AP_".$key}}  id={{"MONTH12_AP_".$key}}  class="form-control three-digits"  value="{{$row->MONTH12_AP}}"                             maxlength="13" style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH12_TP_".$key}}  id={{"MONTH12_TP_".$key}}  class="form-control three-digits"  value="{{$row->MONTH12_TP}}"      readonly  style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH12_IV_".$key}}  id={{"MONTH12_IV_".$key}}  class="form-control three-digits"  value="{{$row->MONTH12_IV}}"  readonly   style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH12_ND_".$key}}  id={{"MONTH12_ND_".$key}}  class="form-control three-digits"  value="{{$row->MONTH12_ND}}"  readonly   style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH12_RT_".$key}}  id={{"MONTH12_RT_".$key}}  class="form-control five-digits"   value="{{$row->MONTH12_RT}}"    maxlength="13" style="width: 100px;" autocomplete="off"  /></td>
  <td><input type="text" name={{"MONTH12_PV_".$key}}  id={{"MONTH12_PV_".$key}}  class="form-control three-digits"  value="{{$row->MONTH12_PV}}"  readonly      maxlength="13" style="width: 100px;" autocomplete="off"  /></td>
    
                                                    



                        
                        <td align="center" ><button class="btn add material" title="add" data-toggle="tooltip" type="button" ><i class="fa fa-plus"></i></button>
                        <button class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
            

                             
                                             
                           
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
<!-- </div> -->
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
<!--Businessunit dropdown-->

<div id="sectionid_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='ctryidref_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Business Unit</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="country_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th>Business Unit Code</th>
            <th>Business Unit Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td ><input type="text" id="sectionmaster_codesearch" onkeyup="searchSectionMasteCode()"></td>
          <td><input type="text" id="sectionmaster_namesearch" onkeyup="searchSectionMasteName()"></td>
        </tr>
        </tbody>
      </table>


      <table id="sectionmaster_tab" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody id="country_body">
        @foreach ($objBusinessUnitList as $index=>$BusinessUnitList)
        <tr id="ctryidref_{{ $BusinessUnitList->BUID }}" class="sectionmaster_tab">
          <td width="50%">{{ $BusinessUnitList->BUCODE }}
          <input type="hidden" id="txtctryidref_{{ $BusinessUnitList->BUID }}" data-desc="{{ $BusinessUnitList->BUCODE }}" data-descname="{{ $BusinessUnitList->BUNAME }}" value="{{ $BusinessUnitList-> BUID }}"/>
          </td>
          <td>{{ $BusinessUnitList->BUNAME }}</td>
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

<!--Customer dropdown-->

<div id="customerid_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='ctryidref1_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Customer List </p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="country_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th>Customer Code</th>
            <th>Customer Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td ><input type="text" id="customermaster_codesearch" onkeyup="searchCustomerCode()"></td>
          <td><input type="text" id="customermaster_namesearch" onkeyup="searchCustomerName()"></td>
        </tr>
        </tbody>
      </table>


      <table id="customermaster_tab" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody id="country_body">
        @foreach ($objCustomerList as $index=>$CustomerList)
        <tr id="ctryidref1_{{ $CustomerList->CID }}" class="customermaster_tab">
          <td width="50%">{{ $CustomerList->CCODE }}
          <input type="hidden" id="txtctryidref1_{{ $CustomerList->CID }}" data-desc="{{ $CustomerList->CCODE }}" data-descname="{{ $CustomerList->NAME }}" value="{{ $CustomerList-> CID }}"/>
          </td>
          <td>{{ $CustomerList->NAME }}</td>
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

<!--DEPT dropdown-->


<div id="dept_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='dept_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Department List</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="country_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th>Department Code</th>
            <th>Department Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td ><input type="text" id="dept_codesearch" onkeyup="searchdeptCode()"></td>
          <td><input type="text" id="dept_namesearch" onkeyup="searchdeptName()"></td>
        </tr>
        </tbody>
      </table>


      <table id="dept_tab" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody id="dept_body">
        @foreach ($objDepartmentList as $index=>$DepartmentList)
        <tr id="deptcode_{{ $DepartmentList->DEPID }}" class="cls_dept">
          <td width="50%">{{ $DepartmentList->DCODE }}
          <input type="hidden" id="txtdeptcode_{{ $DepartmentList->DEPID }}" data-desc="{{ $DepartmentList->DCODE }}" data-descname="{{ $DepartmentList->NAME }}" value="{{ $DepartmentList-> DEPID }}"/>
          </td>
          <td>{{ $DepartmentList->NAME }}</td>
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
<!--FINANCIAL dropdown-->


<div id="fy_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='fy_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Financial Year List</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="fy_table1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th>Financial Year Code</th>
            <th>Financial Year Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td ><input type="text" id="fy_codesearch" onkeyup="searchfyCode()"></td>
          <td><input type="text" id="fy_namesearch" onkeyup="searchfyName()"></td>
        </tr>
        </tbody>
      </table>


      <table id="fy_table2" class="display nowrap table  table-striped table-bordered" width="100%">
      <thead id="thead2">
          
          </thead>
        <tbody id="fy_body">
        @foreach ($objFyearList as $index=>$FiancialList)
        <tr id="fycode_{{ $FiancialList->FYID }}" class="cls_fyear">
          <td width="50%">{{ $FiancialList->FYCODE }}
          <input type="hidden" id="txtfycode_{{ $FiancialList->FYID }}" data-desc="{{ $FiancialList->FYCODE }}" data-descname="{{ $FiancialList->FYDESCRIPTION }}" value="{{ $FiancialList->FYID }}"/>
          </td>
          <td>{{ $FiancialList->FYDESCRIPTION }}</td>
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


<!-- Alert -->


<!-- Item Code Dropdown -->
<div id="ITEMIDpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='ITEMID_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Item Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="ItemIDTable" class="display nowrap table  table-striped table-bordered" >
    <thead>
      <tr id="none-select" class="searchalldata" hidden>
            
            <td> <input type="hidden" name="fieldid" id="hdn_ItemID"/>
            <input type="hidden" name="fieldid2" id="hdn_ItemID2"/>
            <input type="hidden" name="fieldid3" id="hdn_ItemID3"/>
            <input type="hidden" name="fieldid4" id="hdn_ItemID4"/>
            <input type="hidden" name="fieldid5" id="hdn_ItemID5"/>
            <input type="hidden" name="fieldid6" id="hdn_ItemID6"/>
            <input type="hidden" name="fieldid7" id="hdn_ItemID7"/>

     
            </td>
      </tr>
      <!-- <tr class="topnav"><button class="btn topnavbt" id="btnOk"><i class="fa fa-check"></i> OK</button></tr> -->
      <tr>
            <th id="all-check" style="width:4%;" >Select</th>
            <th>Item Code</th>
            <th>Name</th>
            <th>Part No</th>
            <th>Main UOM</th>
            <th>Main QTY</th>
            <th>Item Group</th>
            <th>Item Category</th>
            <th>Status</th>
      </tr>
    </thead>
    <tbody>
    <tr>
    <td><input type="checkbox" class="js-selectall" data-target=".js-selectall1" /></td>
    <td>
    <input type="text" id="Itemcodesearch" onkeyup="ItemCodeFunction()">
    </td>
    <td>
    <input type="text" id="Itemnamesearch" onkeyup="ItemNameFunction()">
    </td>
    <td>
    <input type="text" id="Itempartsearch" onkeyup="ItemPartnoFunction()">
    </td>
    <td>
    <input type="text" id="ItemUOMsearch" onkeyup="ItemUOMFunction()">
    </td>
    <td>
    <input type="text" id="ItemQTYsearch" onkeyup="ItemQTYFunction()">
    </td>
    <td>
    <input type="text" id="ItemGroupsearch" onkeyup="ItemGroupFunction()">
    </td>
    <td>
    <input type="text" id="ItemCategorysearch" onkeyup="ItemCategoryFunction()">
    </td>
    <td>
    <input type="text" id="ItemStatussearch" onkeyup="ItemStatusFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="ItemIDTable2" class="display nowrap table  table-striped table-bordered" >
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

@endsection


@push('bottom-css')
<style>
#custom_dropdown, #udfforsemst_filter {
    display: inline-table;
    margin-left: 15px;
}
.dataTables_wrapper .row:nth-child(1) .col-sm-6:nth-child(2){text-align:right;}
#filtercolumn{color: #555;
    background-color: #fff;
    background-image: none;
    border: 1px solid #ccc;
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
  width: 950px;
  border: 1px solid #ddd;
  font-size: 11px;
}

#ItemIDTable th {
    text-align: center;
    padding: 5px;
    font-size: 11px;
 
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

    font-weight: 600;
}

#ItemIDTable2 td {
  text-align: left;
    padding: 5px;
    font-size: 11px;
      font-weight: 600;
    width: 16%;
}

</style>
@endpush
@push('bottom-scripts')
<script>

//------------------------
  //GL Account
  let tid = "#GlCodeTable2";
      let tid2 = "#GlCodeTable";
      let headers = document.querySelectorAll(tid2 + " th");

      // Sort the table element when clicking on the table headers
      headers.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(tid, ".clsglid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function GLCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("glcodesearch");
        filter = input.value.toUpperCase();
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

  function GLNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("glnamesearch");
        filter = input.value.toUpperCase();
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

  $('#txtgl_popup').focus(function(event){
         $("#glidpopup").show();
         event.preventDefault();
      });

      $("#gl_closePopup").click(function(event){
        $("#glidpopup").hide();
        event.preventDefault();
      });

      $(".clsglid").dblclick(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");
        // var txtid= $('#hdn_fieldid').val();
        // var txt_id2= $('#hdn_fieldid2').val();
        
        $('#txtgl_popup').val(texdesc);
        $('#GLID_REF').val(txtval);
        $("#glidpopup").hide();
        $("#glcodesearch").val(''); 
        $("#glnamesearch").val(''); 
        GLCodeFunction();
        //sub GL
        var customid = txtval;
        if(customid!=''){
          $("#txtsubgl_popup").val('');
          $("#SLID_REF").val('');
          $('#tbody_subglacct').html('<tr><td colspan="2">Please wait..</td></tr>');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url:'{{route("transaction",[220,"getsubledger"])}}',
                type:'POST',
                data:{'id':customid},
                success:function(data) {
                    $('#tbody_subglacct').html(data);
                    bindSubLedgerEvents();
                },
                error:function(data){
                  console.log("Error: Something went wrong.");
                  $('#tbody_subglacct').html('');
                },
            });        
        }
        ////sub GL end
        event.preventDefault();
      });

      

  //GL Account Ends
//------------------------
//Sub GL Account Starts
//------------------------

      let sgltid = "#SubGLTable2";
      let sgltid2 = "#SubGLTable";
      let sglheaders = document.querySelectorAll(sgltid2 + " th");

      // Sort the table element when clicking on the table headers
      sglheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(sgltid, ".clssubgl", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function SubGLCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("subglcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("SubGLTable2");
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

  function SubGLNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("subglnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("SubGLTable2");
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

$("#txtsubgl_popup").focus(function(event){
     $("#subglpopup").show();
     var customid = $(this).parent().parent().find('#GLID_REF').val();
        if(customid!=''){
          $("#txtsubgl_popup").val('');
          $("#SLID_REF").val('');
          $('#tbody_subglacct').html('<tr><td colspan="2">Please wait..</td></tr>');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url:'{{route("transaction",[220,"getsubledger"])}}',
                type:'POST',
                data:{'id':customid},
                success:function(data) {
                    $('#tbody_subglacct').html(data);
                    bindSubLedgerEvents();
                },
                error:function(data){
                  console.log("Error: Something went wrong.");
                  $('#tbody_subglacct').html('');
                },
            });        
        }
     event.preventDefault();
  });

$("#subgl_closePopup").on("click",function(event){ 
    $("#subglpopup").hide();
    event.preventDefault();
});
function bindSubLedgerEvents(){
        $('.clssubgl').dblclick(function(){
    
            var id = $(this).attr('id');
            var txtval =    $("#txt"+id+"").val();
            var texdesc =   $("#txt"+id+"").data("desc");
            var oldSLID =   $("#SLID_REF").val();
            var MaterialClone =  $('#hdnmaterial').val();

            $("#txtsubgl_popup").val(texdesc);
            $("#txtsubgl_popup").blur();
            $("#SLID_REF").val(txtval);
            if (txtval != oldSLID)
            { 
              $('#Material').html(MaterialClone);
              var count11 = <?php echo json_encode($objCount1); ?>;
              $('#Row_Count1').val(count11);
              $('#example2').find('.participantRow').each(function(){
                $(this).find('input:text').val('');
                var rowcount = $('#Row_Count1').val();
                if(rowcount > 1)
                {
                  $(this).closest('.participantRow').remove();
                  rowcount = parseInt(rowcount) - 1;
                  $('#Row_Count1').val(rowcount);
                }
              });
            }
            $("#subglpopup").hide();
            $("#subglcodesearch").val(''); 
            $("#subglnamesearch").val(''); 
            SubGLCodeFunction();
            
              event.preventDefault();
        });
  }
//Sub GL Account Ends
//------------------------

//------------------------
  //Sales Person Dropdown
  let sptid = "#SalesPersonTable2";
      let sptid2 = "#SalesPersonTable";
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
        table = document.getElementById("SalesPersonTable2");
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

  function SalesPersonNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("SalesPersonnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("SalesPersonTable2");
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

  $('#txtSPID_popup').focus(function(event){
         $("#SPIDpopup").show();
      });

      $("#SPID_closePopup").click(function(event){
        $("#SPIDpopup").hide();
      });

      $(".clsspid").dblclick(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");
        
        $('#txtSPID_popup').val(texdesc);
        $('#SPID_REF').val(txtval);
        $("#SPIDpopup").hide();
        
        $("#SalesPersoncodesearch").val(''); 
        $("#SalesPersonnamesearch").val(''); 
        SalesPersonCodeFunction();
        event.preventDefault();
      });

      

  //Sales Person Dropdown Ends
//------------------------

//------------------------
  //Enquiry Media Dropdown
  let emtid = "#EMCodeTable2";
      let emtid2 = "#EMCodeTable";
      let emheaders = document.querySelectorAll(emtid2 + " th");

      // Sort the table element when clicking on the table headers
      emheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(emtid, ".clsemid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function EMCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("emcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("EMCodeTable2");
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

  function EMNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("emnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("EMCodeTable2");
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

  $('#EMID_popup').focus(function(event){
         $("#emidpopup").show();
      });

      $("#em_closePopup").click(function(event){
        $("#emidpopup").hide();
      });

      $(".clsemid").dblclick(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");
        
        $('#EMID_popup').val(texdesc);
        $('#EMID_REF').val(txtval);
        $("#emidpopup").hide();
        
        $("#emcodesearch").val(''); 
        $("#emnamesearch").val(''); 
        EMCodeFunction();
        event.preventDefault();
      });

      

  //Enquiry Media Dropdown Ends
//------------------------

//------------------------
  //Priority Media Dropdown
  let prtid = "#PriorityTable2";
      let prtid2 = "#PriorityTable";
      let prheaders = document.querySelectorAll(prtid2 + " th");

      // Sort the table element when clicking on the table headers
      prheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(prtid, ".clsprid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function PriorityCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Prioritycodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("PriorityTable2");
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

  function PriorityNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Prioritynamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("PriorityTable2");
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

  $('#PRIORITYID_popup').focus(function(event){
         $("#Prioritypopup").show();
      });

      $("#Priority_closePopup").click(function(event){
        $("#Prioritypopup").hide();
      });

      $(".clsprid").dblclick(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");
        
        $('#PRIORITYID_popup').val(texdesc);
        $('#PRIORITYID_REF').val(txtval);
        $("#Prioritypopup").hide();
        
        $("#Prioritycodesearch").val(''); 
        $("#Prioritynamesearch").val(''); 
        PriorityCodeFunction();
        event.preventDefault();
      });      

  //Priority Dropdown Ends
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

  $('#Material').on('focus','[id*="popupITEMID"]',function(event){

    var BU_NO = $(this).parent().parent().find('[id*="REF_BUID"]').val();
    
    if(BU_NO ===""){
          showAlert('Please select Business Unit.');
        }else{


                
                $("#tbody_ItemID").html('');
                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  });
                  $.ajax({
                      url:'{{route("transaction",[220,"getItemDetails"])}}',
                      type:'POST',
                      data:{'status':'A',BU_NO:BU_NO},
        
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
        

        $("#ITEMIDpopup").show();
        var id = $(this).attr('id');
        var id2 = $(this).parent().parent().find('[id*="ITEMID_REF"]').attr('id');
        var id3 = $(this).parent().parent().find('[id*="ItemName"]').attr('id');
        var id4 = $(this).parent().parent().find('[id*="ItemPartno"]').attr('id');
        var id5 = $(this).parent().parent().find('[id*="Itemspec"]').attr('id');
        var id6 = $(this).parent().parent().find('[id*="itemuom"]').attr('id');
        var id7 = $(this).parent().parent().find('[id*="REF_BUID"]').attr('id');

  }

        $('#hdn_ItemID').val(id);
        $('#hdn_ItemID2').val(id2);
        $('#hdn_ItemID3').val(id3);
        $('#hdn_ItemID4').val(id4);
        $('#hdn_ItemID5').val(id5);
        $('#hdn_ItemID6').val(id6);
        $('#hdn_ItemID7').val(id7);
     
       
        event.preventDefault();
      });

      $("#ITEMID_closePopup").click(function(event){
        $("#ITEMIDpopup").hide();
      });

      function bindItemEvents(){

$('#ItemIDTable2').off(); 

$('[id*="chkId"]').change(function(){

// $(".clsitemid").dblclick(function(){
  var fieldid = $(this).parent().parent().attr('id');
  var txtval =   $("#txt"+fieldid+"").val();
  var texdesc =  $("#txt"+fieldid+"").data("desc");
  var fieldid2 = $(this).parent().parent().children('[id*="itemname"]').attr('id');
  var txtname =  $("#txt"+fieldid2+"").val();
  var txtspec =  $("#txt"+fieldid2+"").data("desc");


  var fieldid7 = $(this).parent().parent().children('[id*="itempartno"]').attr('id');
  var txtpartno =  $("#txt"+fieldid7+"").val();

  var fieldid3 = $(this).parent().parent().children('[id*="itemuom"]').attr('id');
  var txtmuomid =  $("#txt"+fieldid3+"").val();
  var txtmainuom =  $("#txt"+fieldid3+"").data("desc");

  var txtsapcustcode =  $("#txt"+fieldid+"").data("sapcustcode");
  var txtsapcustname =  $("#txt"+fieldid+"").data("sapcustname");
  var txtsappartno =  $("#txt"+fieldid+"").data("sappartno");

  var txtitemopenqty =  $("#txt"+fieldid+"").data("itemopenqty");
  var txtSMONTH1_QTY =  $("#txt"+fieldid+"").data("smonth1_qty");  //sale qty

  var pur_sal_open = parseFloat( parseFloat(txtSMONTH1_QTY) - parseFloat(txtitemopenqty) ).toFixed(3);

  var txtAddPur1 = 0;
  var txtItemRate =  $("#txt"+fieldid+"").data("itemrate");

  
  var txtSMONTH2_QTY =  $("#txt"+fieldid+"").data("smonth2_qty");
  var txtSMONTH3_QTY =  $("#txt"+fieldid+"").data("smonth3_qty");
  var txtSMONTH4_QTY =  $("#txt"+fieldid+"").data("smonth4_qty");
  var txtSMONTH5_QTY =  $("#txt"+fieldid+"").data("smonth5_qty");
  var txtSMONTH6_QTY =  $("#txt"+fieldid+"").data("smonth6_qty");

  var txtSMONTH7_QTY =  $("#txt"+fieldid+"").data("smonth7_qty");
  var txtSMONTH8_QTY =  $("#txt"+fieldid+"").data("smonth8_qty");
  var txtSMONTH9_QTY =  $("#txt"+fieldid+"").data("smonth9_qty");
  var txtSMONTH10_QTY =  $("#txt"+fieldid+"").data("smonth10_qty");
  var txtSMONTH11_QTY =  $("#txt"+fieldid+"").data("smonth11_qty");
  var txtSMONTH12_QTY =  $("#txt"+fieldid+"").data("smonth12_qty");

  var  buref =  $("#"+$('#hdn_ItemID7').val()).val();

  // var fieldid8 = $(this).parent().parent().children('[id*="txtitem_openval_"]').attr('id');
  // var txtitemopenval1 =  $("#txt"+fieldid8+"").val();

  
 

 if($(this).is(":checked") == true) 
 {

  var ArrData = [];
  $('#example2').find('.participantRow').each(function(){
    if($(this).find('[id*="ITEMID_REF"]').val() != '')
    {
      var tmpitem = $(this).find('[id*="REF_BUID_"]').val()+'-'+$(this).find('[id*="ITEMID_REF"]').val();
      ArrData.push(tmpitem);
    }
  });
  
  var recdata = buref+'-'+txtval;
  if(jQuery.inArray(recdata, ArrData) !== -1){
    $("#ITEMIDpopup").hide();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Item already exists. Please check.');
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

    txtval = '';
    texdesc = '';
    txtname = '';
    txtpartno = '';
    txtspec = '';   
              
    txtmuomid = '';
    return false;
    
  }

  // $('#example2').find('.participantRow').each(function()
  //  {
  //    var itemid = $(this).find('[id*="ITEMID_REF"]').val();
  //    if(txtval)
  //    {
  //         if(txtval == itemid)
  //         {
  //             $("#ITEMIDpopup").hide();
  //               $("#YesBtn").hide();
  //               $("#NoBtn").hide();
  //               $("#OkBtn").hide();
  //               $("#OkBtn1").show();
  //               $("#AlertMessage").text('Item already exists.');
  //               $("#alert").modal('show');
  //               $("#OkBtn1").focus();
  //               highlighFocusBtn('activeOk1');
  //               $('#hdn_ItemID').val('');
  //               $('#hdn_ItemID2').val('');
  //               $('#hdn_ItemID3').val('');
  //               $('#hdn_ItemID4').val('');
  //               $('#hdn_ItemID5').val('');
  //               $('#hdn_ItemID6').val('');
  //               $('#hdn_ItemID7').val('');

  //               txtval = '';
  //               texdesc = '';
  //               txtname = '';
  //               txtpartno = '';
  //               txtspec = '';   
                         
  //               txtmuomid = '';
  //               return false;
                
  //         }   
         
  //    }          
  // });
                if($('#hdn_ItemID').val() == "" && txtval != '')
                {
                  var txtid= $('#hdn_ItemID').val();
                  var txt_id2= $('#hdn_ItemID2').val();
                  var txt_id3= $('#hdn_ItemID3').val();
                  var txt_id4= $('#hdn_ItemID4').val();
                  var txt_id5= $('#hdn_ItemID5').val();
                  var txt_id6= $('#hdn_ItemID6').val();
                  var txt_id7= $('#hdn_ItemID7').val();
         

                  var $tr = $('.material').closest('table');
                  var allTrs = $tr.find('.participantRow').last();
                  var lastTr = allTrs[allTrs.length-1];
                  var $clone = $(lastTr).clone();
                  $clone.find('td').each(function(){
                      var el = $(this).find(':first-child');
                      var id = el.attr('id') || null;
                      if(id) {
                          var i = id.substr(id.length-1);
                          var prefix = id.substr(0, (id.length-1));
                          el.attr('id', prefix+(+i+1));
                      }
                      var name = el.attr('name') || null;
                      if(name) {
                          var i = name.substr(name.length-1);
                          var prefix1 = name.substr(0, (name.length-1));
                          el.attr('name', prefix1+(+i+1));
                      }
                  });
                  $clone.find('.remove').removeAttr('disabled'); 
                  $clone.find('[id*="popupITEMID"]').val(texdesc);
                  $clone.find('[id*="ITEMID_REF"]').val(txtval);
                  $clone.find('[id*="ItemName"]').val(txtname);

                  $clone.find('[id*="ItemPartno"]').val(txtsappartno);
                  $clone.find('[id*="Itemspec"]').val(txtspec);
                  $clone.find('[id*="itemuom"]').val(txtmuomid);
                  $clone.find('[id*="itemmain_uom"]').val(txtmainuom);
                  $clone.find('[id*="CID_REF"]').val(txtsapcustname);
                  $clone.find('[id*="CUSTOMERID_REF"]').val(txtsapcustcode);

                  
                  $clone.find('[id*="MONTH"]').val(0);  //clear months data values
                  
                  $clone.find('[id*="MONTH1_OP_"]').val(txtitemopenqty);
                  $clone.find('[id*="MONTH1_SL_"]').val(txtSMONTH1_QTY);
                  $clone.find('[id*="MONTH1_PR"]').val(pur_sal_open);


                  $clone.find('[id*="MONTH1_AP_"]').val(txtAddPur1);
                  $clone.find('[id*="MONTH1_RT_"]').val(txtItemRate);


                  $clone.find('[id*="MONTH2_SL_"]').val(txtSMONTH2_QTY);
                  $clone.find('[id*="MONTH3_SL_"]').val(txtSMONTH3_QTY);
                  $clone.find('[id*="MONTH4_SL_"]').val(txtSMONTH4_QTY);
                  $clone.find('[id*="MONTH5_SL_"]').val(txtSMONTH5_QTY);
                  $clone.find('[id*="MONTH6_SL_"]').val(txtSMONTH6_QTY);

                  $clone.find('[id*="MONTH7_SL_"]').val(txtSMONTH7_QTY);
                  $clone.find('[id*="MONTH8_SL_"]').val(txtSMONTH8_QTY);
                  $clone.find('[id*="MONTH9_SL_"]').val(txtSMONTH9_QTY);
                  $clone.find('[id*="MONTH10_SL_"]').val(txtSMONTH10_QTY);
                  $clone.find('[id*="MONTH11_SL_"]').val(txtSMONTH11_QTY);
                  $clone.find('[id*="MONTH12_SL_"]').val(txtSMONTH12_QTY);
                  
            

       
                  
                  $tr.closest('table').append($clone);   
                  var rowCount = $('#Row_Count1').val();
                    rowCount = parseInt(rowCount)+1;
                    $('#Row_Count1').val(rowCount);
                   

                  event.preventDefault();
                }
                else
                {
                var txtid= $('#hdn_ItemID').val();
                var txt_id2= $('#hdn_ItemID2').val();
                var txt_id3= $('#hdn_ItemID3').val();
                var txt_id4= $('#hdn_ItemID4').val();
                var txt_id5= $('#hdn_ItemID5').val();
                var txt_id6= $('#hdn_ItemID6').val();
                var txt_id7= $('#hdn_ItemID7').val();

 
                $('#'+txtid).val(texdesc);
                $('#'+txt_id2).val(txtval);
                $('#'+txt_id3).val(txtname);
                $('#'+txt_id4).val(txtsappartno);
                $('#'+txt_id5).val(txtspec);
                $('#'+txt_id6).val(txtmuomid);

                $('#'+txtid).parent().parent().find('[id*="itemmain_uom"]').val(txtmainuom);

                $('#'+txtid).parent().parent().find('[id*="CID_REF"]').val(txtsapcustname);
                $('#'+txtid).parent().parent().find('[id*="CUSTOMERID_REF"]').val(txtsapcustcode);

                $('#'+txtid).parent().parent().find('[id*="MONTH"]').val(0);  //clear months data values

                $('#'+txtid).parent().parent().find('[id*="MONTH1_OP_"]').val(txtitemopenqty);
                
                $('#'+txtid).parent().parent().find('[id*="MONTH1_SL_"]').val(txtSMONTH1_QTY);
                $('#'+txtid).parent().parent().find('[id*="MONTH1_RT_"]').val(txtItemRate);
                $('#'+txtid).parent().parent().find('[id*="MONTH1_PR"]').val(pur_sal_open);
               
                $('#'+txtid).parent().parent().find('[id*="MONTH1_AP_"]').val(txtAddPur1);
                


                $('#'+txtid).parent().parent().find('[id*="MONTH2_SL_"]').val(txtSMONTH2_QTY);
                $('#'+txtid).parent().parent().find('[id*="MONTH3_SL_"]').val(txtSMONTH3_QTY);
                $('#'+txtid).parent().parent().find('[id*="MONTH4_SL_"]').val(txtSMONTH4_QTY);
                $('#'+txtid).parent().parent().find('[id*="MONTH5_SL_"]').val(txtSMONTH5_QTY);
                $('#'+txtid).parent().parent().find('[id*="MONTH6_SL_"]').val(txtSMONTH6_QTY);

                $('#'+txtid).parent().parent().find('[id*="MONTH7_SL_"]').val(txtSMONTH7_QTY);
                $('#'+txtid).parent().parent().find('[id*="MONTH8_SL_"]').val(txtSMONTH8_QTY);
                $('#'+txtid).parent().parent().find('[id*="MONTH9_SL_"]').val(txtSMONTH9_QTY);
                $('#'+txtid).parent().parent().find('[id*="MONTH10_SL_"]').val(txtSMONTH10_QTY);
                $('#'+txtid).parent().parent().find('[id*="MONTH11_SL_"]').val(txtSMONTH11_QTY);
                $('#'+txtid).parent().parent().find('[id*="MONTH12_SL_"]').val(txtSMONTH12_QTY);
                
                 
                $('#hdn_ItemID').val('');
                $('#hdn_ItemID2').val('');
                $('#hdn_ItemID3').val('');
                $('#hdn_ItemID4').val('');
                $('#hdn_ItemID5').val('');
                $('#hdn_ItemID6').val('');
                $('#hdn_ItemID7').val('');

                
                }
                $("#ITEMIDpopup").hide();
                event.preventDefault();
 }
 else if($(this).is(":checked") == false) 
 {
   var id = txtval;
   var r_count = $('#Row_Count1').val();
   $('#example2').find('.participantRow').each(function()
   {
     var itemid = $(this).find('[id*="ITEMID_REF"]').val();
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
  $("#Itempartnosearch").val(''); 
  $("#ItemUOMsearch").val(''); 
  $("#ItemGroupsearch").val(''); 
  $("#ItemCategorysearch").val(''); 
  $("#ItemStatussearch").val(''); 
  $('.remove').removeAttr('disabled'); 
  ItemCodeFunction();
  event.preventDefault();
});
}

    // function bindItemEvents222(){

    //   $('#ItemIDTable2').off(); 

    //   $('[id*="chkId"]').change(function(){
  
    //   // $(".clsitemid").dblclick(function(){
    //     var fieldid = $(this).parent().parent().attr('id');
    //     var txtval =   $("#txt"+fieldid+"").val();
    //     var texdesc =  $("#txt"+fieldid+"").data("desc");
    //     var fieldid2 = $(this).parent().parent().children('[id*="itemname"]').attr('id');
    //     var txtname =  $("#txt"+fieldid2+"").val();
    //     var txtspec =  $("#txt"+fieldid2+"").data("desc");

   
    //     var fieldid7 = $(this).parent().parent().children('[id*="itempartno"]').attr('id');
    //     var txtpartno =  $("#txt"+fieldid7+"").val();

    //     var fieldid3 = $(this).parent().parent().children('[id*="itemuom"]').attr('id');
    //     var txtmuomid =  $("#txt"+fieldid3+"").val();

    //     var txtsapcustcode =  $("#txt"+fieldid+"").data("sapcustcode");
    //     var txtsapcustname =  $("#txt"+fieldid+"").data("sapcustname");
    //     var txtsappartno =  $("#txt"+fieldid+"").data("sappartno");

    //     var txtitemopenqty =  $("#txt"+fieldid+"").data("itemopenqty");
    //     var txtSMONTH1_QTY =  $("#txt"+fieldid+"").data("smonth1_qty");  //sale qty

    //     var pur_sal_open = parseFloat( parseFloat(txtSMONTH1_QTY) - parseFloat(txtitemopenqty) ).toFixed(3);

    //     var txtAddPur1 = 0;
    //     var txtItemRate =  $("#txt"+fieldid+"").data("itemrate");

        
    //     var txtSMONTH2_QTY =  $("#txt"+fieldid+"").data("smonth2_qty");
    //     var txtSMONTH3_QTY =  $("#txt"+fieldid+"").data("smonth3_qty");
    //     var txtSMONTH4_QTY =  $("#txt"+fieldid+"").data("smonth4_qty");
    //     var txtSMONTH5_QTY =  $("#txt"+fieldid+"").data("smonth5_qty");
    //     var txtSMONTH6_QTY =  $("#txt"+fieldid+"").data("smonth6_qty");

    //     var txtSMONTH7_QTY =  $("#txt"+fieldid+"").data("smonth7_qty");
    //     var txtSMONTH8_QTY =  $("#txt"+fieldid+"").data("smonth8_qty");
    //     var txtSMONTH9_QTY =  $("#txt"+fieldid+"").data("smonth9_qty");
    //     var txtSMONTH10_QTY =  $("#txt"+fieldid+"").data("smonth10_qty");
    //     var txtSMONTH11_QTY =  $("#txt"+fieldid+"").data("smonth11_qty");
    //     var txtSMONTH12_QTY =  $("#txt"+fieldid+"").data("smonth12_qty");

    //     var  buref =  $("#"+$('#hdn_ItemID7').val()).val();
        
       

    //    if($(this).is(":checked") == true) 
    //    {

    //     var ArrData = [];
    //     $('#example2').find('.participantRow').each(function(){
    //       if($(this).find('[id*="ITEMID_REF"]').val() != '')
    //       {
    //         var tmpitem = $(this).find('[id*="REF_BUID_"]').val()+'-'+$(this).find('[id*="ITEMID_REF"]').val();
    //         ArrData.push(tmpitem);
    //       }
    //     });
        
    //     var recdata = buref+'-'+txtval;
    //     if(jQuery.inArray(recdata, ArrData) !== -1){
    //       $("#ITEMIDpopup").hide();
    //       $("#YesBtn").hide();
    //       $("#NoBtn").hide();
    //       $("#OkBtn").hide();
    //       $("#OkBtn1").show();
    //       $("#AlertMessage").text('Item already exists. Please check.');
    //       $("#alert").modal('show');
    //       $("#OkBtn1").focus();
    //       highlighFocusBtn('activeOk1');
    //       $('#hdn_ItemID').val('');
    //       $('#hdn_ItemID2').val('');
    //       $('#hdn_ItemID3').val('');
    //       $('#hdn_ItemID4').val('');
    //       $('#hdn_ItemID5').val('');
    //       $('#hdn_ItemID6').val('');
    //       $('#hdn_ItemID7').val('');

    //       txtval = '';
    //       texdesc = '';
    //       txtname = '';
    //       txtpartno = '';
    //       txtspec = '';   
                    
    //       txtmuomid = '';
    //       return false;
          
    //     }
         

    
     

    //     // $('#example2').find('.participantRow').each(function()
    //     //  {
    //     //    var itemid = $(this).find('[id*="ITEMID_REF"]').val();

    //     //    if(txtval)
    //     //    {
    //     //         if(txtval == itemid)
    //     //         {
    //     //           $("#ITEMIDpopup").hide();
    //     //               $("#YesBtn").hide();
    //     //               $("#NoBtn").hide();
    //     //               $("#OkBtn").hide();
    //     //               $("#OkBtn1").show();
    //     //               $("#AlertMessage").text('Item already exists.');
    //     //               $("#alert").modal('show');
    //     //               $("#OkBtn1").focus();
    //     //               highlighFocusBtn('activeOk1');
    //     //               $('#hdn_ItemID').val('');
    //     //               $('#hdn_ItemID2').val('');
    //     //               $('#hdn_ItemID3').val('');
    //     //               $('#hdn_ItemID4').val('');
    //     //               $('#hdn_ItemID5').val('');
    //     //               $('#hdn_ItemID6').val('');

    //     //               txtval = '';
    //     //               texdesc = '';
    //     //               txtname = '';
    //     //               txtpartno = '';
    //     //               txtspec = '';   
                               
    //     //               txtmuomid = '';
    //     //               return false;
                      
    //     //         }   
               
    //     //    }          
    //     // });

    //                   if($('#hdn_ItemID').val() == "" && txtval != '')
    //                   {
    //                     var txtid= $('#hdn_ItemID').val();
    //                     var txt_id2= $('#hdn_ItemID2').val();
    //                     var txt_id3= $('#hdn_ItemID3').val();
    //                     var txt_id4= $('#hdn_ItemID4').val();
    //                     var txt_id5= $('#hdn_ItemID5').val();
    //                     var txt_id6= $('#hdn_ItemID6').val();
    //                     var txt_id7= $('#hdn_ItemID7').val();
               
   
                      
                        

    //                     var $tr = $('.material').closest('table');
    //                     var allTrs = $tr.find('.participantRow').last();
    //                     var lastTr = allTrs[allTrs.length-1];
    //                     var $clone = $(lastTr).clone();
    //                     $clone.find('td').each(function(){
    //                         var el = $(this).find(':first-child');
    //                         var id = el.attr('id') || null;
    //                         if(id) {
    //                             var i = id.substr(id.length-1);
    //                             var prefix = id.substr(0, (id.length-1));
    //                             el.attr('id', prefix+(+i+1));
    //                         }
    //                         var name = el.attr('name') || null;
    //                         if(name) {
    //                             var i = name.substr(name.length-1);
    //                             var prefix1 = name.substr(0, (name.length-1));
    //                             el.attr('name', prefix1+(+i+1));
    //                         }
    //                     });
    //                     $clone.find('.remove').removeAttr('disabled'); 
    //                     $clone.find('[id*="popupITEMID"]').val(texdesc);
    //                     $clone.find('[id*="ITEMID_REF"]').val(txtval);
    //                     $clone.find('[id*="ItemName"]').val(txtname);

    //                      $clone.find('[id*="ItemPartno"]').val(txtsappartno);
    //                     $clone.find('[id*="Itemspec"]').val(txtspec);
    //                     $clone.find('[id*="itemuom"]').val(txtmuomid);
    //                     $clone.find('[id*="CID_REF"]').val(txtsapcustname);
    //                     $clone.find('[id*="CUSTOMERID_REF"]').val(txtsapcustcode);

                        
    //                     $clone.find('[id*="MONTH"]').val(0);  //clear months data values

    //                     $clone.find('[id*="MONTH1_OP_"]').val(txtitemopenqty);
    //                     $clone.find('[id*="MONTH1_SL_"]').val(txtSMONTH1_QTY);
    //                     $clone.find('[id*="MONTH1_PR"]').val(pur_sal_open);


    //                     $clone.find('[id*="MONTH1_AP_"]').val(txtAddPur1);
    //                     $clone.find('[id*="MONTH1_RT_"]').val(txtItemRate);


    //                     $clone.find('[id*="MONTH2_SL_"]').val(txtSMONTH2_QTY);
    //                     $clone.find('[id*="MONTH3_SL_"]').val(txtSMONTH3_QTY);
    //                     $clone.find('[id*="MONTH4_SL_"]').val(txtSMONTH4_QTY);
    //                     $clone.find('[id*="MONTH5_SL_"]').val(txtSMONTH5_QTY);
    //                     $clone.find('[id*="MONTH6_SL_"]').val(txtSMONTH6_QTY);

    //                     $clone.find('[id*="MONTH7_SL_"]').val(txtSMONTH7_QTY);
    //                     $clone.find('[id*="MONTH8_SL_"]').val(txtSMONTH8_QTY);
    //                     $clone.find('[id*="MONTH9_SL_"]').val(txtSMONTH9_QTY);
    //                     $clone.find('[id*="MONTH10_SL_"]').val(txtSMONTH10_QTY);
    //                     $clone.find('[id*="MONTH11_SL_"]').val(txtSMONTH11_QTY);
    //                     $clone.find('[id*="MONTH12_SL_"]').val(txtSMONTH12_QTY);
                                      

             
                        
    //                     $tr.closest('table').append($clone);   
    //                     var rowCount = $('#Row_Count1').val();
    //                       rowCount = parseInt(rowCount)+1;
    //                       $('#Row_Count1').val(rowCount);
                         
    //                     event.preventDefault();
    //                   }
    //                   else
    //                   {
    //                   var txtid= $('#hdn_ItemID').val();
    //                   var txt_id2= $('#hdn_ItemID2').val();
    //                   var txt_id3= $('#hdn_ItemID3').val();
    //                   var txt_id4= $('#hdn_ItemID4').val();
    //                   var txt_id5= $('#hdn_ItemID5').val();
    //                   var txt_id6= $('#hdn_ItemID6').val();
    //                   var txt_id7= $('#hdn_ItemID7').val();

       
    //                   $('#'+txtid).val(texdesc);
    //                   $('#'+txt_id2).val(txtval);
    //                   $('#'+txt_id3).val(txtname);
    //                   $('#'+txt_id4).val(txtpartno);
    //                   $('#'+txt_id5).val(txtspec);
    //                   $('#'+txt_id6).val(txtmuomid);

    //                   $('#'+txtid).parent().parent().find('[id*="CID_REF"]').val(txtsapcustname);
    //                   $('#'+txtid).parent().parent().find('[id*="CUSTOMERID_REF"]').val(txtsapcustcode);

    //                   $('#'+txtid).parent().parent().find('[id*="MONTH"]').val(0);  //clear months data values

    //                   $('#'+txtid).parent().parent().find('[id*="MONTH1_OP_"]').val(txtitemopenqty);
                      
    //                   $('#'+txtid).parent().parent().find('[id*="MONTH1_SL_"]').val(txtSMONTH1_QTY);
    //                   $('#'+txtid).parent().parent().find('[id*="MONTH1_RT_"]').val(txtItemRate);
    //                   $('#'+txtid).parent().parent().find('[id*="MONTH1_PR"]').val(pur_sal_open);
                     
    //                   $('#'+txtid).parent().parent().find('[id*="MONTH1_AP_"]').val(txtAddPur1);
                      


    //                   $('#'+txtid).parent().parent().find('[id*="MONTH2_SL_"]').val(txtSMONTH2_QTY);
    //                   $('#'+txtid).parent().parent().find('[id*="MONTH3_SL_"]').val(txtSMONTH3_QTY);
    //                   $('#'+txtid).parent().parent().find('[id*="MONTH4_SL_"]').val(txtSMONTH4_QTY);
    //                   $('#'+txtid).parent().parent().find('[id*="MONTH5_SL_"]').val(txtSMONTH5_QTY);
    //                   $('#'+txtid).parent().parent().find('[id*="MONTH6_SL_"]').val(txtSMONTH6_QTY);

    //                   $('#'+txtid).parent().parent().find('[id*="MONTH7_SL_"]').val(txtSMONTH7_QTY);
    //                   $('#'+txtid).parent().parent().find('[id*="MONTH8_SL_"]').val(txtSMONTH8_QTY);
    //                   $('#'+txtid).parent().parent().find('[id*="MONTH9_SL_"]').val(txtSMONTH9_QTY);
    //                   $('#'+txtid).parent().parent().find('[id*="MONTH10_SL_"]').val(txtSMONTH10_QTY);
    //                   $('#'+txtid).parent().parent().find('[id*="MONTH11_SL_"]').val(txtSMONTH11_QTY);
    //                   $('#'+txtid).parent().parent().find('[id*="MONTH12_SL_"]').val(txtSMONTH12_QTY);
                      
               
                      
    //                   // $("#ITEMIDpopup").hide();
    //                   $('#hdn_ItemID').val('');
    //                   $('#hdn_ItemID2').val('');
    //                   $('#hdn_ItemID3').val('');
    //                   $('#hdn_ItemID4').val('');
    //                   $('#hdn_ItemID5').val('');
    //                   $('#hdn_ItemID6').val('');

                      
    //                   }
    //                   event.preventDefault();
    //    }
    //    else if($(this).is(":checked") == false) 
    //    {
    //      var id = txtval;
    //      var r_count = $('#Row_Count1').val();
    //      $('#example2').find('.participantRow').each(function()
    //      {
    //        var itemid = $(this).find('[id*="ITEMID_REF"]').val();
    //        if(id == itemid)
    //        {
    //           var rowCount = $('#Row_Count1').val();
    //           if (rowCount > 1) {
    //             $(this).closest('.participantRow').remove(); 
    //             rowCount = parseInt(rowCount)-1;
    //           $('#Row_Count1').val(rowCount);
    //           }
    //           else 
    //           {
    //             $(document).find('.dmaterial').prop('disabled', true);  
    //             $("#ITEMIDpopup").hide();
    //             $("#YesBtn").hide();
    //             $("#NoBtn").hide();
    //             $("#OkBtn").hide();
    //             $("#OkBtn1").show();
    //             $("#AlertMessage").text('There is only 1 row. So cannot be remove.');
    //             $("#alert").modal('show');
    //             $("#OkBtn1").focus();
    //             highlighFocusBtn('activeOk1');
    //             return false;

    //           }
    //             event.preventDefault(); 
    //        }
    //     });
    //    }
    //     $("#Itemcodesearch").val(''); 
    //     $("#Itemnamesearch").val(''); 
    //     $("#Itempartnosearch").val(''); 
    //     $("#ItemUOMsearch").val(''); 
    //     $("#ItemGroupsearch").val(''); 
    //     $("#ItemCategorysearch").val(''); 
    //     $("#ItemStatussearch").val(''); 
    //     $('.remove').removeAttr('disabled'); 
    //     ItemCodeFunction();
    //     event.preventDefault();
    //   });
    // }

      

  //Item ID Dropdown Ends
  //Item ID Dropdown Ends
//------------------------

//------------------------
//Packaging Type Dropdown
  let pttid = "#PackagingTable2";
      let pttid2 = "#PackagingTable";
      let ptheaders = document.querySelectorAll(pttid2 + " th");

      // Sort the table element when clicking on the table headers
      ptheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(pttid, ".clsptid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function PackagingCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Packagingcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("PackagingTable2");
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

  function PackagingNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Packagingnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("PackagingTable2");
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

  $('#Material').on('focus','[id*="PACKSIZE"]',function(event){
         $("#Packagingpopup").show();
         var id = $(this).attr('id');
         var id2 = $(this).parent().parent().find('[id*="PTID_REF"]').attr('id');
         $('#hdn_Packaging').val(id);
         $('#hdn_Packaging2').val(id2);
      });

      $("#PackagingclosePopup").click(function(event){
        $("#Packagingpopup").hide();
      });

      $(".clsptid").dblclick(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");

        var txtid = $('#hdn_Packaging').val();
        var txt_id2 = $('#hdn_Packaging2').val();
        $('#'+txtid).val(texdesc);
        $('#'+txt_id2).val(txtval);
        if(txtval == '')
        {
          $('#'+txtid).parent().parent().find('[id*="PACKUOM"]').prop('disabled',true);
          $('#'+txtid).parent().parent().find('[id*="PACKUOM"]').removeAttr('readonly');
          $('#'+txtid).parent().parent().find('[id*="PACK_QTY"]').prop('disabled',true);
        }
        else
        {
          $('#'+txtid).parent().parent().find('[id*="PACKUOM"]').removeAttr('disabled');
          $('#'+txtid).parent().parent().find('[id*="PACK_QTY"]').removeAttr('disabled');
          $('#'+txtid).parent().parent().find('[id*="PACKUOM"]').prop('readonly',true);
        }
        $("#Packagingpopup").hide();
        
        $("#Packagingcodesearch").val(''); 
        $("#Packagingnamesearch").val(''); 
        PackagingCodeFunction();
        event.preventDefault();
      });      

  //Packaging Type Dropdown Ends
//------------------------

//------------------------
//UOM Type Dropdown
let uomtid = "#UOMTable2";
      let uomtid2 = "#UOMTable";
      let uomheaders = document.querySelectorAll(uomtid2 + " th");

      // Sort the table element when clicking on the table headers
      uomheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(uomtid, ".clsuomid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function UOMCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("UOMcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("UOMTable2");
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

  function UOMNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("UOMnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("UOMTable2");
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

  $('#Material').on('focus','[id*="PACKUOM"]',function(event){
         $("#UOMpopup").show();
         var id = $(this).attr('id');
         var id2 = $(this).parent().parent().find('[id*="PACKUOMID_REF"]').attr('id');
         $('#hdn_UOM').val(id);
         $('#hdn_UOM2').val(id2);
      });

      $("#UOMclosePopup").click(function(event){
        $("#UOMpopup").hide();
      });

      $(".clsuomid").dblclick(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");

        var txtid = $('#hdn_UOM').val();
        var txt_id2 = $('#hdn_UOM2').val();
        $('#'+txtid).val(texdesc);
        $('#'+txt_id2).val(txtval);
        
        $("#UOMpopup").hide();
        
        $("#UOMcodesearch").val(''); 
        $("#UOMnamesearch").val(''); 
        UOMCodeFunction();
        event.preventDefault();
      });      

  //UOM Type Dropdown Ends
//------------------------

//------------------------
  //ALT UOM Dropdown
  let altutid = "#altuomTable2";
      let altutid2 = "#altuomTable";
      let altutidheaders = document.querySelectorAll(altutid2 + " th");

      // Sort the table element when clicking on the table headers
      altutidheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(altutid, ".clsaltuom", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function altuomCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("altuomcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("altuomTable2");
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

      function altuomNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("altuomnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("altuomTable2");
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

      

  $('#Material').on('focus','[id*="popupAUOM"]',function(event){
        var ItemID = $(this).parent().parent().find('[id*="ITEMID_REF"]').val();
        
        if(ItemID !=''){
                $("#tbody_altuom").html('');
                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  });
                  $.ajax({
                      url:'{{route("transaction",[220,"getAltUOM"])}}',
                      type:'POST',
                      data:{'id':ItemID},
                      success:function(data) {
                        
                        $("#tbody_altuom").html(data);   
                        bindAltUOM();                     
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                        $("#tbody_altuom").html('');                        
                      },
                  }); 
        }
        else
        {
                $("#altuompopup").hide();
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text('Please Select Item First.');
                $("#alert").modal('show');
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk1');
                return false;
        }

        $("#altuompopup").show();
        var id = $(this).attr('id');
        var id2 = $(this).parent().parent().find('[id*="ALT_UOMID_REF"]').attr('id');
        var id3 = $(this).parent().parent().find('[id*="SE_QTY"]').attr('id');
        var id4 = $(this).parent().parent().find('[id*="ALT_UOMID_QTY"]').attr('id');
        
        $('#hdn_altuom').val(id);
        $('#hdn_altuom2').val(id2);
        $('#hdn_altuom3').val(id3);
        $('#hdn_altuom4').val(id4);
        event.preventDefault();
      });

      $("#altuom_closePopup").click(function(event){
        $("#altuompopup").hide();
      });

    function bindAltUOM(){

      $('#altuomTable2').off(); 

      $(".clsaltuom").dblclick(function(){
        var fieldid = $(this).attr('id');
        var txtval =   $("#txt"+fieldid+"").val();
        var texdesc =  $("#txt"+fieldid+"").data("desc");
        var txtid= $('#hdn_altuom').val();
        var txt_id2= $('#hdn_altuom2').val();
        var txt_id3= $('#hdn_altuom3').val();
        var txt_id4= $('#hdn_altuom4').val();
        $('#'+txtid).val(texdesc);
        $('#'+txt_id2).val(txtval);

        var itemid = $('#'+txtid).parent().parent().find('[id*="ITEMID_REF"]').val();
        var altuomid = txtval;
        var mqty = $('#'+txtid).parent().parent().find('[id*="SE_QTY"]').val();

        if(altuomid!=''){
              $('#'+txt_id4).val('');
                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  });
                  $.ajax({
                      url:'{{route("transaction",[220,"getaltuomqty"])}}',
                      type:'POST',
                      data:{'id':altuomid, 'itemid':itemid, 'mqty':mqty},
                      success:function(data) {
                        if(intRegex.test(data)){
                            data = (data +'.000');
                        }
                        $('#'+txt_id4).val(data);                        
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                        $('#'+txt_id4).val('');                        
                      },
                  }); 
                      
              }

        $("#altuompopup").hide();
        $("#altuomcodesearch").val(''); 
        $("#altuomnamesearch").val(''); 
        
        altuomCodeFunction();
        event.preventDefault();
      });
    }

      

  
$('#btnAdd').on('click', function() {
  var viewURL = '{{route("transaction",[220,"add"])}}';
              window.location.href=viewURL;
});

$('#btnExit').on('click', function() {
  var viewURL = '{{route('home')}}';
              window.location.href=viewURL;
});

$('#ENQDT').change(function() {
    var mindate  = $(this).val();
    $('[id*="EDD"]').attr('min',mindate);
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
      if(id) {
          var i = id.substr(id.length-1);
          var prefix = id.substr(0, (id.length-1));
          el.attr('id', prefix+(+i+1));
      }
      var name = el.attr('name') || null;
      if(name) {
          var i = name.substr(name.length-1);
          var prefix1 = name.substr(0, (name.length-1));
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

  applyForceNum();
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
    //reload form
    window.location.reload();
}//fnUndoYes

window.fnUndoNo = function (){
    $("#ENQNO").focus();
}//fnUndoNo

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

</script>

@endpush

@push('bottom-scripts')
<script>

$(document).ready(function() {
  applyForceNum();

  var d = new Date(); 
  var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
  
  var last_DT = <?php echo json_encode($objlast_DT); ?>;
  
  if(Date(last_DT)>=Date(today)){
    mindate = today;
  }else{
    mindate = last_DT;
  }
  
  if(Date(last_DT)>=Date(today)){
    mxdate = last_DT;
  }else{
    mxdate = today;
  }

  
  $('#AFSADT').attr("min",today);
  $('#AFSADT').attr("max",today);
  $('#AFSADT').val(today);

  
var count1 = <?php echo json_encode($objCount1); ?>;


$('#Row_Count1').val(count1);

var objSE = <?php echo json_encode($objSEMAT); ?>;


//var item = <?php echo json_encode($objItems); ?>;




    $('#frm_trn_se1').bootstrapValidator({
       
        fields: {
            txtlabel: {
                validators: {
                    notEmpty: {
                        message: 'The Enquiry Number is required'
                    }
                }
            },            
        },
        submitHandler: function(validator, form, submitButton) {
            alert( "Handler for .submit() called." );
             event.preventDefault();
             $("#frm_trn_se").submit();
        }
    });
});

function validateForm(){
       $("#FocusId").val('');
        var AFSNO          =   $.trim($("#AFSANO").val());
        var AFSDT          =   $.trim($("#AFSADT").val());
        var DEPID_REF          =   $.trim($("#DEPID_REF").val());
        var FYID_REF          =   $.trim($("#FYID_REF").val());
        var REASONAFSA          =   $.trim($("#REASON_AFSA").val());


        if(AFSNO ===""){
            $("#FocusId").val($("#AFSNO"));
            $("#ProceedBtn").focus();
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text('Please enter value in AFPA Number.');
            $("#alert").modal('show');
            $("#OkBtn1").focus();
            return false;
        }
        else if(AFSDT ===""){

            $("#REASON_AFSA").focus();
            $("#AFSDT").val();  
            $("#ProceedBtn").focus();
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text('Please select AFPA Date.');
            $("#alert").modal('show');
            $("#OkBtn1").focus();
            return false;
        }  
        else if(REASONAFSA ===""){
            $("#FocusId").val($("#REASONAFSA"));
            $("#AFSDT").val();  
            $("#ProceedBtn").focus();
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text('Please enter data for Reason to amend AFP.');
            $("#alert").modal('show');
            $("#OkBtn1").focus();
            return false;
        }  

        else if(DEPID_REF ===""){
          $("#FocusId").val($("#DEPID_REF"));
            $("#ProceedBtn").focus();
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text('Please select Department.');
            $("#alert").modal('show');
            $("#OkBtn1").focus();
            return false;
        }
        else if(FYID_REF ===""){
          $("#FocusId").val($("#FYID_REF"));
            $("#ProceedBtn").focus();
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").show(); 
            $("#AlertMessage").text('Please select Financial Year.');
            $("#alert").modal('show');
            $("#OkBtn1").focus();
            return false;
        }
        else{

            event.preventDefault();
            var allblank = [];
            var allblank2 = [];
            var allblank3 = [];
            
            
                $('#example2').find('.participantRow').each(function(){
                  if($.trim($(this).find("[id*=BUID_REF]").val())!=""){
                  allblank.push('true');
                  }
                  else{
                    allblank.push('false');
                  } 

                  if($.trim($(this).find("[id*=popupITEMID]").val())!=""){
                      allblank2.push('true');
                  }
                  else{
                      allblank2.push('false');
                  } 

                //-----------   
                }); 
        }


        if(jQuery.inArray("false", allblank) !== -1){
          $("#alert").modal('show');
          $("#AlertMessage").text('Please select Business Unit in Material Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
          }
        else if(jQuery.inArray("false", allblank2) !== -1){
          $("#alert").modal('show');
          $("#AlertMessage").text('Please select Item in Material Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
          } 
          else if(checkPeriodClosing('{{$FormId}}',$("#AFSDT").val(),0) ==0){
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
                $("#YesBtn").data("funcname","fnSaveData");  //set dynamic fucntion name
                $("#YesBtn").focus();
                $("#OkBtn").hide();
                highlighFocusBtn('activeYes');
          }

}

$("#btnSaveSE" ).click(function() {
    var formReqData = $("#frm_trn_se");
    if(formReqData.valid()){
      validateForm();
    }
});



$("#YesBtn").click(function(){

$("#alert").modal('hide');
var customFnName = $("#YesBtn").data("funcname");
    window[customFnName]();

}); //yes button

window.fnSaveData = function (){
event.preventDefault();
      var trnseForm = $("#frm_trn_se");
      var formData = trnseForm.serialize();
  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });
  $("#btnSaveSE").hide(); 
  $(".buttonload").show(); 
  $("#btnApprove").prop("disabled", true);
  $.ajax({
      url:'{{ route("transactionmodify",[220,"saveamendment"])}}',
      type:'POST',
      data:formData,
      success:function(data) {
        $(".buttonload").hide(); 
        $("#btnSaveSE").show();   
        $("#btnApprove").prop("disabled", false);
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
        $(".buttonload").hide(); 
        $("#btnSaveSE").show();   
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

window.fnApproveData = function (){
event.preventDefault();
      var trnseForm = $("#frm_trn_se");
      var formData = trnseForm.serialize();
  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });
  $.ajax({
      url:'{{ route("transactionmodify",[220,"Approve"])}}',
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

//no button
$("#NoBtn").click(function(){
    $("#alert").modal('hide');
    $("#LABEL").focus();
});

//ok button
$("#OkBtn").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn").hide();
    $(".text-danger").hide();
    window.location.href = '{{route("transaction",[220,"index"]) }}';
});

$("#OkBtn1").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn").hide();
    $("#OkBtn1").hide();
    $("#"+$(this).data('focusname')).focus();
    // $("[id*=txtlabel]").focus();
    $(".text-danger").hide();
});

//
function showError(pId,pVal){
    $("#"+pId+"").text(pVal);
    $("#"+pId+"").show();
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
    // Business Unit popup function
function get_section(id){
   
   var result = id.split('_');
   var id_number=result[2];
   var popup_id='#'+id;
   $(".sectionmaster_tab").val(popup_id);    

 $("#sectionid_popup").show();
 $("#SECTIONID_POPUP").keyup(function(event){
 if(event.keyCode==13){
   $("#sectionid_popup").show();
 }
});

$("#ctryidref_close").on("click",function(event){ 
 $("#sectionid_popup").hide();
});

$('.sectionmaster_tab').dblclick(function(){

   var value= $(".sectionmaster_tab").val()
   var result = value.split('_');
   var id_numbers=result[2];
   var sectionid_ref="#BUID_REF_"+id_numbers; 
   var buid="#REF_BUID_"+id_numbers; 


   var id          =   $(this).attr('id');

 var txtval      =   $("#txt"+id+"").val();
 var texdesc     =   $("#txt"+id+"").data("desc");
 var texdescname =   $("#txt"+id+"").data("descname");
 



 //$("#SALES_AC_DESC").val(texdescname);
  //clear row
 $(buid).parent().parent().find('input:text').val('');  //clear text
 $(buid).parent().parent().find('input:hidden').val(''); //clear hidden

 $(buid).val(txtval);
 $(sectionid_ref).val(texdesc);
 $("#sectionid_popup").hide();

});
}

function searchSectionMasteCode() {
 var input, filter, table, tr, td, i, txtValue;
 input = document.getElementById("sectionmaster_codesearch");
 filter = input.value.toUpperCase();
 table = document.getElementById("sectionmaster_tab");
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

function searchSectionMasteName() {
     var input, filter, table, tr, td, i, txtValue;
     input = document.getElementById("sectionmaster_namesearch");
     filter = input.value.toUpperCase();
     table = document.getElementById("sectionmaster_tab");
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


// Customer popup function
function get_customer(id){
   
   var result = id.split('_');
   var id_number=result[2];
   var popup_id='#'+id;
   $(".customermaster_tab").val(popup_id);    

 $("#customerid_popup").show();
 $("#CUSTOMERID_POPUP").keyup(function(event){
 if(event.keyCode==13){
   $("#customerid_popup").show();
 }
});

$("#ctryidref1_close").on("click",function(event){ 
 $("#customerid_popup").hide();
});

$('.customermaster_tab').dblclick(function(){

   var value= $(".customermaster_tab").val()
   var result = value.split('_');
   var id_numbers=result[2];
   var sectionid_ref="#CID_REF_"+id_numbers; 
   var customer_id="#CUSTOMERID_REF_"+id_numbers;




   var id          =   $(this).attr('id');

 var txtval      =   $("#txt"+id+"").val();
 var texdesc     =   $("#txt"+id+"").data("desc");
 var texdescname =   $("#txt"+id+"").data("descname");

 //$("#SALES_AC_DESC").val(texdescname);
 $(customer_id).val(txtval);
 $(sectionid_ref).val(texdesc);
 $("#customerid_popup").hide();

});
}

function searchCustomerCode() {
 var input, filter, table, tr, td, i, txtValue;
 input = document.getElementById("customermaster_codesearch");
 filter = input.value.toUpperCase();
 table = document.getElementById("customermaster_tab");
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

function searchCustomerName() {
     var input, filter, table, tr, td, i, txtValue;
     input = document.getElementById("customermaster_namesearch");
     filter = input.value.toUpperCase();
     table = document.getElementById("customermaster_tab");
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

// DEPT popup function

$("#DEPTID_NAME").on("click",function(event){ 

$("#dept_popup").show();
});

$("#DEPTID_NAME").keyup(function(event){
if(event.keyCode==13){
  $("#DEPTID_NAME").show();
}
});

$("#dept_close").on("click",function(event){ 
$("#dept_popup").hide();
});

$('.cls_dept').dblclick(function(){
var id          =   $(this).attr('id');
var txtval      =   $("#txt"+id+"").val();
var texdesc     =   $("#txt"+id+"").data("desc");
var texdescname =   $("#txt"+id+"").data("descname");

$("#DEPTID_NAME").val(texdescname);
$("#DEPID_REF").val(txtval);



$("#DEPTID_NAME").blur(); 
$("#STID_REF_POPUP").focus(); 

$("#dept_popup").hide();

event.preventDefault();
});


function searchdeptCode() {
var input, filter, table, tr, td, i, txtValue;
input = document.getElementById("dept_codesearch");
filter = input.value.toUpperCase();
table = document.getElementById("dept_tab");
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


function searchdeptName() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("dept_namesearch");
    filter = input.value.toUpperCase();
    table = document.getElementById("dept_tab");
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


// FINANCE popup function

$("#FYID_NAME").on("click",function(event){ 

$("#fy_popup").show();
});

$("#FYID_NAME").keyup(function(event){
if(event.keyCode==13){
$("#FYID_NAME").show();
}
});

$("#fy_close").on("click",function(event){ 
$("#fy_popup").hide();
});

$('.cls_fyear').dblclick(function(){
var fieldid          =   $(this).attr('id');
var txtval      =   $("#txt"+fieldid+"").val();
var texdesc     =   $("#txt"+fieldid+"").data("desc");
var texdescname =   $("#txt"+fieldid+"").data("descname");


$("#FYID_NAME").val(texdescname);
$("#FYID_REF").val(txtval);



$("#FYID_NAME").blur(); 

$("#fy_popup").hide();

event.preventDefault();
});


let fyear = "#fy_table2";
    let fyearid2 = "#fy_table1";
    let fyearheaders = document.querySelectorAll(fyearid2 + " th");

    // Sort the table element when clicking on the table headers
    fyearheaders.forEach(function(element, i) {
      element.addEventListener("click", function() {
        w3.sortHTML(fyear, ".cls_fyear", "td:nth-child(" + (i + 1) + ")");
      });
    });

function searchfyCode() {
var input, filter, table, tr, td, i, txtValue;
input = document.getElementById("fy_codesearch");
filter = input.value.toUpperCase();
table = document.getElementById("fy_table2");
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


function searchfyName() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("fy_namesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("fy_table2");
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




$( "#btnApprove" ).click(function() {
 
  $("#FocusId").val('');
        var AFSNO          =   $.trim($("#AFSNO").val());
        var AFSDT          =   $.trim($("#AFSDT").val());
        var DEPID_REF          =   $.trim($("#DEPID_REF").val());
        var FYID_REF          =   $.trim($("#FYID_REF").val());


        if(AFSNO ===""){
            $("#FocusId").val($("#AFSNO"));
            $("#ProceedBtn").focus();
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text('Please enter value in AFP Number.');
            $("#alert").modal('show');
            $("#OkBtn1").focus();
            return false;
        }
        else if(AFSDT ===""){
            $("#FocusId").val($("#AFSDT"));
            $("#AFSDT").val();  
            $("#ProceedBtn").focus();
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text('Please select AFP Date.');
            $("#alert").modal('show');
            $("#OkBtn1").focus();
            return false;
        }  

        else if(DEPID_REF ===""){
          $("#FocusId").val($("#DEPID_REF"));
            $("#ProceedBtn").focus();
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text('Please select Department.');
            $("#alert").modal('show');
            $("#OkBtn1").focus();
            return false;
        }
        else if(FYID_REF ===""){
          $("#FocusId").val($("#FYID_REF"));
            $("#ProceedBtn").focus();
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").show(); 
            $("#AlertMessage").text('Please select Financial Year.');
            $("#alert").modal('show');
            $("#OkBtn1").focus();
            return false;
        }
        else{

            event.preventDefault();
            var allblank = [];
            var allblank2 = [];
            var allblank3 = [];
            
            
                $('#example2').find('.participantRow').each(function(){
                  if($.trim($(this).find("[id*=BUID_REF]").val())!=""){
                  allblank.push('true');
                  }
                  else{
                    allblank.push('false');
                  } 

                  if($.trim($(this).find("[id*=popupITEMID]").val())!=""){
                      allblank2.push('true');
                  }
                  else{
                      allblank2.push('false');
                  } 

                //-----------   
                }); 
        }

        if(jQuery.inArray("false", allblank) !== -1){
          $("#alert").modal('show');
          $("#AlertMessage").text('Please select Business Unit in Material Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
          }
        else if(jQuery.inArray("false", allblank2) !== -1){
          $("#alert").modal('show');
          $("#AlertMessage").text('Please select Item in Material Tab.');
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
       
});


// April begin
$('#Material').on('blur',"[id*='MONTH1_AP_']",function()
{
      var A1 =  parseFloat($(this).parent().parent().find('[id*="MONTH1_OP"]').val()).toFixed(3); //OPENNING 
      if(isNaN(A1))
      {
        A1=0;
      }

      var B1 =  parseFloat($(this).parent().parent().find('[id*="MONTH1_SL"]').val()).toFixed(3); //Sales 
      if(isNaN(B1))
      {
        B1=0;
      }
    
      var ap = $.trim($(this).val());
      if(isNaN(ap) || ap=="" )
      {
        ap = 0;
      }  
      if(intRegex.test(ap))
      {
        $(this).val((ap +'.000'));
      }
      var D1 = $(this).val();  //Add Purchase

      var C1 =  parseFloat($(this).parent().parent().find('[id*="MONTH1_PR"]').val()).toFixed(3); //Purchase (Sales-Opening) 
      if(isNaN(C1))
      {
        C1=0;
      }
         
      var E1 = parseFloat( parseFloat(D1) + parseFloat(C1) ).toFixed(3) ;    
      if(isNaN(E1)){
        E1=0;
      }
      $(this).parent().parent().find('[id*="MONTH1_TP"]').val(E1); //To Be procured 

      //INVENTORY =A1+C1+D1-B1
      var inventory = parseFloat( parseFloat(A1)+ parseFloat(C1)+ parseFloat(D1) ) - parseFloat(B1);
      if(isNaN(C1))
      {
        inventory=0;
      }
      inventory = parseFloat(inventory).toFixed(3)
      $(this).parent().parent().find('[id*="MONTH1_IV"]').val( parseFloat(inventory).toFixed(3) ); 
      $(this).parent().parent().find('[id*="MONTH2_OP"]').val(inventory); //inventory become opening value for next month


      var invdays = parseFloat(parseFloat(inventory)/20 ).toFixed(3);
      if(isNaN(invdays))
      {
        invdays=0;
      }
      invdays = Math.ceil(invdays);
      $(this).parent().parent().find('[id*="MONTH1_ND"]').val(invdays); //inventory days
      
      var rate1 = parseFloat($(this).parent().parent().find('[id*="MONTH1_RT"]').val()).toFixed(3); //Rate
      if(isNaN(rate1))
      {
        rate1=0;
      }

      var purval1 = parseFloat(parseFloat(E1) * parseFloat(rate1) ).toFixed(3);
      $(this).parent().parent().find('[id*="MONTH1_PV"]').val(purval1); 
    event.preventDefault();
});  

$('#Material').on('blur',"[id*='MONTH1_RT']",function()
{
      
      var rate1 = $.trim($(this).val());
      if(isNaN(rate1) || rate1=="" )
      {
        rate1 = 0;
      }  
      if(intRegex.test(rate1))
      {
        $(this).val((rate1 +'.00000'));
      }
      var r1 = $(this).val();  //rate
      var tp =  parseFloat($(this).parent().parent().find('[id*="MONTH1_TP"]').val()).toFixed(3); 

      var pval1 = parseFloat(parseFloat(tp) * parseFloat(r1) ).toFixed(3);
      $(this).parent().parent().find('[id*="MONTH1_PV"]').val(pval1); 
     
      //set inventory 
      var iv =  parseFloat($(this).parent().parent().find('[id*="MONTH1_IV"]').val()).toFixed(3);
      $(this).parent().parent().find('[id*="MONTH2_OP"]').val(iv); //inventory become opening value for next month

      event.preventDefault();
}); 
// April end


//------------------May
$('#Material').on('blur',"[id*='MONTH2_AP_']",function()
{
      var A2 =  parseFloat($(this).parent().parent().find('[id*="MONTH2_OP"]').val()).toFixed(3); //OPENNING 
      if(isNaN(A2))
      {
        A2=0;
      }

      var B2 =  parseFloat($(this).parent().parent().find('[id*="MONTH2_SL"]').val()).toFixed(3); //Sales 
      if(isNaN(B2))
      {
        B2=0;
      }
    
      var ap2= $.trim($(this).val());
      if(isNaN(ap2) || ap2=="" )
      {
        ap2 = 0;
      }  
      if(intRegex.test(ap2))
      {
        $(this).val((ap2 +'.000'));
      }
      var D2 = $(this).val();  //Add Purchase

      var C2 =  parseFloat($(this).parent().parent().find('[id*="MONTH2_PR"]').val()).toFixed(3); //Purchase (Sales-Opening) 
      if(isNaN(C2))
      {
        C2=0;
      }
         
      var E2 = parseFloat( parseFloat(D2) + parseFloat(C2) ).toFixed(3) ;    
      if(isNaN(E2)){
        E2=0;
      }
      $(this).parent().parent().find('[id*="MONTH2_TP"]').val(E2); //To Be procured 

      //INVENTORY =A2+C2+D2-B2
      var inventory2 = parseFloat( parseFloat(A2)+ parseFloat(C2)+ parseFloat(D2) ) - parseFloat(B2);
      if(isNaN(C2))
      {
        inventory2=0;
      }
      inventory2 = parseFloat(inventory2).toFixed(3)
      $(this).parent().parent().find('[id*="MONTH2_IV"]').val( parseFloat(inventory2).toFixed(3) ); 
      $(this).parent().parent().find('[id*="MONTH3_OP"]').val(inventory2); //inventory become opening value for next month


      var invdays2 = parseFloat(parseFloat(inventory2)/20 ).toFixed(3);
      if(isNaN(invdays2))
      {
        invdays2=0;
      }
      invdays2 = Math.ceil(invdays2);
      $(this).parent().parent().find('[id*="MONTH2_ND"]').val(invdays2); //inventory days
      
      var rate2 = parseFloat($(this).parent().parent().find('[id*="MONTH2_RT"]').val()).toFixed(3); //Rate
      if(isNaN(rate2))
      {
        rate2=0;
      }

      var purval2 = parseFloat(parseFloat(E2) * parseFloat(rate2) ).toFixed(3);
      $(this).parent().parent().find('[id*="MONTH2_PV"]').val(purval2); 
    event.preventDefault();
});  

$('#Material').on('blur',"[id*='MONTH2_RT']",function()
{
      
      var rate2 = $.trim($(this).val());
      if(isNaN(rate2) || rate2=="" )
      {
        rate2 = 0;
      }  
      if(intRegex.test(rate2))
      {
        $(this).val((rate2 +'.00000'));
      }
      var r2 = $(this).val();  //rate
      var tp2 =  parseFloat($(this).parent().parent().find('[id*="MONTH2_TP"]').val()).toFixed(3); 

      var pval2 = parseFloat(parseFloat(tp2) * parseFloat(r2) ).toFixed(3);
      $(this).parent().parent().find('[id*="MONTH2_PV"]').val(pval2); 
     
      //set inventory 
      var iv2 =  parseFloat($(this).parent().parent().find('[id*="MONTH2_IV"]').val()).toFixed(3);
      $(this).parent().parent().find('[id*="MONTH3_OP"]').val(iv2); //inventory become opening value for next month

      event.preventDefault();
}); 
//-------------------------- May end

//------------------June
$('#Material').on('blur',"[id*='MONTH3_AP_']",function()
{
      var A2 =  parseFloat($(this).parent().parent().find('[id*="MONTH3_OP"]').val()).toFixed(3); //OPENNING 
      if(isNaN(A2))
      {
        A2=0;
      }

      var B2 =  parseFloat($(this).parent().parent().find('[id*="MONTH3_SL"]').val()).toFixed(3); //Sales 
      if(isNaN(B2))
      {
        B2=0;
      }
    
      var ap2= $.trim($(this).val());
      if(isNaN(ap2) || ap2=="" )
      {
        ap2 = 0;
      }  
      if(intRegex.test(ap2))
      {
        $(this).val((ap2 +'.000'));
      }
      var D2 = $(this).val();  //Add Purchase

      var C2 =  parseFloat($(this).parent().parent().find('[id*="MONTH3_PR"]').val()).toFixed(3); //Purchase (Sales-Opening) 
      if(isNaN(C2))
      {
        C2=0;
      }
         
      var E2 = parseFloat( parseFloat(D2) + parseFloat(C2) ).toFixed(3) ;    
      if(isNaN(E2)){
        E2=0;
      }
      $(this).parent().parent().find('[id*="MONTH3_TP"]').val(E2); //To Be procured 

      //INVENTORY =A2+C2+D2-B2
      var inventory2 = parseFloat( parseFloat(A2)+ parseFloat(C2)+ parseFloat(D2) ) - parseFloat(B2);
      if(isNaN(C2))
      {
        inventory2=0;
      }
      inventory2 = parseFloat(inventory2).toFixed(3)
      $(this).parent().parent().find('[id*="MONTH3_IV"]').val( parseFloat(inventory2).toFixed(3) ); 
      $(this).parent().parent().find('[id*="MONTH4_OP"]').val(inventory2); //inventory become opening value for next month


      var invdays2 = parseFloat(parseFloat(inventory2)/20 ).toFixed(3);
      if(isNaN(invdays2))
      {
        invdays2=0;
      }
      invdays2 = Math.ceil(invdays2);
      $(this).parent().parent().find('[id*="MONTH3_ND"]').val(invdays2); //inventory days
      
      var rate2 = parseFloat($(this).parent().parent().find('[id*="MONTH3_RT"]').val()).toFixed(3); //Rate
      if(isNaN(rate2))
      {
        rate2=0;
      }

      var purval2 = parseFloat(parseFloat(E2) * parseFloat(rate2) ).toFixed(3);
      $(this).parent().parent().find('[id*="MONTH3_PV"]').val(purval2); 
    event.preventDefault();
});  

$('#Material').on('blur',"[id*='MONTH3_RT']",function()
{
      
      var rate2 = $.trim($(this).val());
      if(isNaN(rate2) || rate2=="" )
      {
        rate2 = 0;
      }  
      if(intRegex.test(rate2))
      {
        $(this).val((rate2 +'.00000'));
      }
      var r2 = $(this).val();  //rate
      var tp2 =  parseFloat($(this).parent().parent().find('[id*="MONTH3_TP"]').val()).toFixed(3); 

      var pval2 = parseFloat(parseFloat(tp2) * parseFloat(r2) ).toFixed(3);
      $(this).parent().parent().find('[id*="MONTH3_PV"]').val(pval2); 
     
      //set inventory 
      var iv2 =  parseFloat($(this).parent().parent().find('[id*="MONTH3_IV"]').val()).toFixed(3);
      $(this).parent().parent().find('[id*="MONTH4_OP"]').val(iv2); //inventory become opening value for next month

      event.preventDefault();
}); 
//-------------------------- June end

//------------------July
$('#Material').on('blur',"[id*='MONTH4_AP_']",function()
{
      var A2 =  parseFloat($(this).parent().parent().find('[id*="MONTH4_OP"]').val()).toFixed(3); //OPENNING 
      if(isNaN(A2))
      {
        A2=0;
      }

      var B2 =  parseFloat($(this).parent().parent().find('[id*="MONTH4_SL"]').val()).toFixed(3); //Sales 
      if(isNaN(B2))
      {
        B2=0;
      }
    
      var ap2= $.trim($(this).val());
      if(isNaN(ap2) || ap2=="" )
      {
        ap2 = 0;
      }  
      if(intRegex.test(ap2))
      {
        $(this).val((ap2 +'.000'));
      }
      var D2 = $(this).val();  //Add Purchase

      var C2 =  parseFloat($(this).parent().parent().find('[id*="MONTH4_PR"]').val()).toFixed(3); //Purchase (Sales-Opening) 
      if(isNaN(C2))
      {
        C2=0;
      }
         
      var E2 = parseFloat( parseFloat(D2) + parseFloat(C2) ).toFixed(3) ;    
      if(isNaN(E2)){
        E2=0;
      }
      $(this).parent().parent().find('[id*="MONTH4_TP"]').val(E2); //To Be procured 

      //INVENTORY =A2+C2+D2-B2
      var inventory2 = parseFloat( parseFloat(A2)+ parseFloat(C2)+ parseFloat(D2) ) - parseFloat(B2);
      if(isNaN(C2))
      {
        inventory2=0;
      }
      inventory2 = parseFloat(inventory2).toFixed(3)
      $(this).parent().parent().find('[id*="MONTH4_IV"]').val( parseFloat(inventory2).toFixed(3) ); 
      $(this).parent().parent().find('[id*="MONTH5_OP"]').val(inventory2); //inventory become opening value for next month


      var invdays2 = parseFloat(parseFloat(inventory2)/20 ).toFixed(3);
      if(isNaN(invdays2))
      {
        invdays2=0;
      }
      invdays2 = Math.ceil(invdays2);
      $(this).parent().parent().find('[id*="MONTH4_ND"]').val(invdays2); //inventory days
      
      var rate2 = parseFloat($(this).parent().parent().find('[id*="MONTH4_RT"]').val()).toFixed(3); //Rate
      if(isNaN(rate2))
      {
        rate2=0;
      }

      var purval2 = parseFloat(parseFloat(E2) * parseFloat(rate2) ).toFixed(3);
      $(this).parent().parent().find('[id*="MONTH4_PV"]').val(purval2); 
    event.preventDefault();
});  

$('#Material').on('blur',"[id*='MONTH4_RT']",function()
{
      
      var rate2 = $.trim($(this).val());
      if(isNaN(rate2) || rate2=="" )
      {
        rate2 = 0;
      }  
      if(intRegex.test(rate2))
      {
        $(this).val((rate2 +'.00000'));
      }
      var r2 = $(this).val();  //rate
      var tp2 =  parseFloat($(this).parent().parent().find('[id*="MONTH4_TP"]').val()).toFixed(3); 

      var pval2 = parseFloat(parseFloat(tp2) * parseFloat(r2) ).toFixed(3);
      $(this).parent().parent().find('[id*="MONTH4_PV"]').val(pval2); 
     
      //set inventory 
      var iv2 =  parseFloat($(this).parent().parent().find('[id*="MONTH4_IV"]').val()).toFixed(3);
      $(this).parent().parent().find('[id*="MONTH5_OP"]').val(iv2); //inventory become opening value for next month

      event.preventDefault();
}); 
//-------------------------- July end


//------------------August
$('#Material').on('blur',"[id*='MONTH5_AP_']",function()
{
      var A2 =  parseFloat($(this).parent().parent().find('[id*="MONTH5_OP"]').val()).toFixed(3); //OPENNING 
      if(isNaN(A2))
      {
        A2=0;
      }

      var B2 =  parseFloat($(this).parent().parent().find('[id*="MONTH5_SL"]').val()).toFixed(3); //Sales 
      if(isNaN(B2))
      {
        B2=0;
      }
    
      var ap2= $.trim($(this).val());
      if(isNaN(ap2) || ap2=="" )
      {
        ap2 = 0;
      }  
      if(intRegex.test(ap2))
      {
        $(this).val((ap2 +'.000'));
      }
      var D2 = $(this).val();  //Add Purchase

      var C2 =  parseFloat($(this).parent().parent().find('[id*="MONTH5_PR"]').val()).toFixed(3); //Purchase (Sales-Opening) 
      if(isNaN(C2))
      {
        C2=0;
      }
         
      var E2 = parseFloat( parseFloat(D2) + parseFloat(C2) ).toFixed(3) ;    
      if(isNaN(E2)){
        E2=0;
      }
      $(this).parent().parent().find('[id*="MONTH5_TP"]').val(E2); //To Be procured 

      //INVENTORY =A2+C2+D2-B2
      var inventory2 = parseFloat( parseFloat(A2)+ parseFloat(C2)+ parseFloat(D2) ) - parseFloat(B2);
      if(isNaN(C2))
      {
        inventory2=0;
      }
      inventory2 = parseFloat(inventory2).toFixed(3)
      $(this).parent().parent().find('[id*="MONTH5_IV"]').val( parseFloat(inventory2).toFixed(3) ); 
      $(this).parent().parent().find('[id*="MONTH6_OP"]').val(inventory2); //inventory become opening value for next month


      var invdays2 = parseFloat(parseFloat(inventory2)/20 ).toFixed(3);
      if(isNaN(invdays2))
      {
        invdays2=0;
      }
      invdays2 = Math.ceil(invdays2);
      $(this).parent().parent().find('[id*="MONTH5_ND"]').val(invdays2); //inventory days
      
      var rate2 = parseFloat($(this).parent().parent().find('[id*="MONTH5_RT"]').val()).toFixed(3); //Rate
      if(isNaN(rate2))
      {
        rate2=0;
      }

      var purval2 = parseFloat(parseFloat(E2) * parseFloat(rate2) ).toFixed(3);
      $(this).parent().parent().find('[id*="MONTH5_PV"]').val(purval2); 
    event.preventDefault();
});  

$('#Material').on('blur',"[id*='MONTH5_RT']",function()
{
      
      var rate2 = $.trim($(this).val());
      if(isNaN(rate2) || rate2=="" )
      {
        rate2 = 0;
      }  
      if(intRegex.test(rate2))
      {
        $(this).val((rate2 +'.00000'));
      }
      var r2 = $(this).val();  //rate
      var tp2 =  parseFloat($(this).parent().parent().find('[id*="MONTH5_TP"]').val()).toFixed(3); 

      var pval2 = parseFloat(parseFloat(tp2) * parseFloat(r2) ).toFixed(3);
      $(this).parent().parent().find('[id*="MONTH5_PV"]').val(pval2); 
     
      //set inventory 
      var iv2 =  parseFloat($(this).parent().parent().find('[id*="MONTH5_IV"]').val()).toFixed(3);
      $(this).parent().parent().find('[id*="MONTH6_OP"]').val(iv2); //inventory become opening value for next month

      event.preventDefault();
}); 
//-------------------------- August end

//------------------September
$('#Material').on('blur',"[id*='MONTH6_AP_']",function()
{
      var A2 =  parseFloat($(this).parent().parent().find('[id*="MONTH6_OP"]').val()).toFixed(3); //OPENNING 
      if(isNaN(A2))
      {
        A2=0;
      }

      var B2 =  parseFloat($(this).parent().parent().find('[id*="MONTH6_SL"]').val()).toFixed(3); //Sales 
      if(isNaN(B2))
      {
        B2=0;
      }
    
      var ap2= $.trim($(this).val());
      if(isNaN(ap2) || ap2=="" )
      {
        ap2 = 0;
      }  
      if(intRegex.test(ap2))
      {
        $(this).val((ap2 +'.000'));
      }
      var D2 = $(this).val();  //Add Purchase

      var C2 =  parseFloat($(this).parent().parent().find('[id*="MONTH6_PR"]').val()).toFixed(3); //Purchase (Sales-Opening) 
      if(isNaN(C2))
      {
        C2=0;
      }
         
      var E2 = parseFloat( parseFloat(D2) + parseFloat(C2) ).toFixed(3) ;    
      if(isNaN(E2)){
        E2=0;
      }
      $(this).parent().parent().find('[id*="MONTH6_TP"]').val(E2); //To Be procured 

      //INVENTORY =A2+C2+D2-B2
      var inventory2 = parseFloat( parseFloat(A2)+ parseFloat(C2)+ parseFloat(D2) ) - parseFloat(B2);
      if(isNaN(C2))
      {
        inventory2=0;
      }
      inventory2 = parseFloat(inventory2).toFixed(3)
      $(this).parent().parent().find('[id*="MONTH6_IV"]').val( parseFloat(inventory2).toFixed(3) ); 
      $(this).parent().parent().find('[id*="MONTH7_OP"]').val(inventory2); //inventory become opening value for next month


      var invdays2 = parseFloat(parseFloat(inventory2)/20 ).toFixed(3);
      if(isNaN(invdays2))
      {
        invdays2=0;
      }
      invdays2 = Math.ceil(invdays2);
      $(this).parent().parent().find('[id*="MONTH6_ND"]').val(invdays2); //inventory days
      
      var rate2 = parseFloat($(this).parent().parent().find('[id*="MONTH6_RT"]').val()).toFixed(3); //Rate
      if(isNaN(rate2))
      {
        rate2=0;
      }

      var purval2 = parseFloat(parseFloat(E2) * parseFloat(rate2) ).toFixed(3);
      $(this).parent().parent().find('[id*="MONTH6_PV"]').val(purval2); 
    event.preventDefault();
});  

$('#Material').on('blur',"[id*='MONTH6_RT']",function()
{
      
      var rate2 = $.trim($(this).val());
      if(isNaN(rate2) || rate2=="" )
      {
        rate2 = 0;
      }  
      if(intRegex.test(rate2))
      {
        $(this).val((rate2 +'.00000'));
      }
      var r2 = $(this).val();  //rate
      var tp2 =  parseFloat($(this).parent().parent().find('[id*="MONTH6_TP"]').val()).toFixed(3); 

      var pval2 = parseFloat(parseFloat(tp2) * parseFloat(r2) ).toFixed(3);
      $(this).parent().parent().find('[id*="MONTH6_PV"]').val(pval2); 
     
      //set inventory 
      var iv2 =  parseFloat($(this).parent().parent().find('[id*="MONTH6_IV"]').val()).toFixed(3);
      $(this).parent().parent().find('[id*="MONTH7_OP"]').val(iv2); //inventory become opening value for next month

      event.preventDefault();
}); 
//-------------------------- September end

//------------------Oct
$('#Material').on('blur',"[id*='MONTH7_AP_']",function()
{
      var A2 =  parseFloat($(this).parent().parent().find('[id*="MONTH7_OP"]').val()).toFixed(3); //OPENNING 
      if(isNaN(A2))
      {
        A2=0;
      }

      var B2 =  parseFloat($(this).parent().parent().find('[id*="MONTH7_SL"]').val()).toFixed(3); //Sales 
      if(isNaN(B2))
      {
        B2=0;
      }
    
      var ap2= $.trim($(this).val());
      if(isNaN(ap2) || ap2=="" )
      {
        ap2 = 0;
      }  
      if(intRegex.test(ap2))
      {
        $(this).val((ap2 +'.000'));
      }
      var D2 = $(this).val();  //Add Purchase

      var C2 =  parseFloat($(this).parent().parent().find('[id*="MONTH7_PR"]').val()).toFixed(3); //Purchase (Sales-Opening) 
      if(isNaN(C2))
      {
        C2=0;
      }
         
      var E2 = parseFloat( parseFloat(D2) + parseFloat(C2) ).toFixed(3) ;    
      if(isNaN(E2)){
        E2=0;
      }
      $(this).parent().parent().find('[id*="MONTH7_TP"]').val(E2); //To Be procured 

      //INVENTORY =A2+C2+D2-B2
      var inventory2 = parseFloat( parseFloat(A2)+ parseFloat(C2)+ parseFloat(D2) ) - parseFloat(B2);
      if(isNaN(C2))
      {
        inventory2=0;
      }
      inventory2 = parseFloat(inventory2).toFixed(3)
      $(this).parent().parent().find('[id*="MONTH7_IV"]').val( parseFloat(inventory2).toFixed(3) ); 
      $(this).parent().parent().find('[id*="MONTH8_OP"]').val(inventory2); //inventory become opening value for next month


      var invdays2 = parseFloat(parseFloat(inventory2)/20 ).toFixed(3);
      if(isNaN(invdays2))
      {
        invdays2=0;
      }
      invdays2 = Math.ceil(invdays2);
      $(this).parent().parent().find('[id*="MONTH7_ND"]').val(invdays2); //inventory days
      
      var rate2 = parseFloat($(this).parent().parent().find('[id*="MONTH7_RT"]').val()).toFixed(3); //Rate
      if(isNaN(rate2))
      {
        rate2=0;
      }

      var purval2 = parseFloat(parseFloat(E2) * parseFloat(rate2) ).toFixed(3);
      $(this).parent().parent().find('[id*="MONTH7_PV"]').val(purval2); 
    event.preventDefault();
});  

$('#Material').on('blur',"[id*='MONTH7_RT']",function()
{
      
      var rate2 = $.trim($(this).val());
      if(isNaN(rate2) || rate2=="" )
      {
        rate2 = 0;
      }  
      if(intRegex.test(rate2))
      {
        $(this).val((rate2 +'.00000'));
      }
      var r2 = $(this).val();  //rate
      var tp2 =  parseFloat($(this).parent().parent().find('[id*="MONTH7_TP"]').val()).toFixed(3); 

      var pval2 = parseFloat(parseFloat(tp2) * parseFloat(r2) ).toFixed(3);
      $(this).parent().parent().find('[id*="MONTH7_PV"]').val(pval2); 
     
      //set inventory 
      var iv2 =  parseFloat($(this).parent().parent().find('[id*="MONTH7_IV"]').val()).toFixed(3);
      $(this).parent().parent().find('[id*="MONTH8_OP"]').val(iv2); //inventory become opening value for next month

      event.preventDefault();
}); 
//-------------------------- Oct end

//------------------Nov
$('#Material').on('blur',"[id*='MONTH8_AP_']",function()
{
      var A2 =  parseFloat($(this).parent().parent().find('[id*="MONTH8_OP"]').val()).toFixed(3); //OPENNING 
      if(isNaN(A2))
      {
        A2=0;
      }

      var B2 =  parseFloat($(this).parent().parent().find('[id*="MONTH8_SL"]').val()).toFixed(3); //Sales 
      if(isNaN(B2))
      {
        B2=0;
      }
    
      var ap2= $.trim($(this).val());
      if(isNaN(ap2) || ap2=="" )
      {
        ap2 = 0;
      }  
      if(intRegex.test(ap2))
      {
        $(this).val((ap2 +'.000'));
      }
      var D2 = $(this).val();  //Add Purchase

      var C2 =  parseFloat($(this).parent().parent().find('[id*="MONTH8_PR"]').val()).toFixed(3); //Purchase (Sales-Opening) 
      if(isNaN(C2))
      {
        C2=0;
      }
         
      var E2 = parseFloat( parseFloat(D2) + parseFloat(C2) ).toFixed(3) ;    
      if(isNaN(E2)){
        E2=0;
      }
      $(this).parent().parent().find('[id*="MONTH8_TP"]').val(E2); //To Be procured 

      //INVENTORY =A2+C2+D2-B2
      var inventory2 = parseFloat( parseFloat(A2)+ parseFloat(C2)+ parseFloat(D2) ) - parseFloat(B2);
      if(isNaN(C2))
      {
        inventory2=0;
      }
      inventory2 = parseFloat(inventory2).toFixed(3)
      $(this).parent().parent().find('[id*="MONTH8_IV"]').val( parseFloat(inventory2).toFixed(3) ); 
      $(this).parent().parent().find('[id*="MONTH9_OP"]').val(inventory2); //inventory become opening value for next month


      var invdays2 = parseFloat(parseFloat(inventory2)/20 ).toFixed(3);
      if(isNaN(invdays2))
      {
        invdays2=0;
      }
      invdays2 = Math.ceil(invdays2);
      $(this).parent().parent().find('[id*="MONTH8_ND"]').val(invdays2); //inventory days
      
      var rate2 = parseFloat($(this).parent().parent().find('[id*="MONTH8_RT"]').val()).toFixed(3); //Rate
      if(isNaN(rate2))
      {
        rate2=0;
      }

      var purval2 = parseFloat(parseFloat(E2) * parseFloat(rate2) ).toFixed(3);
      $(this).parent().parent().find('[id*="MONTH8_PV"]').val(purval2); 
    event.preventDefault();
});  

$('#Material').on('blur',"[id*='MONTH8_RT']",function()
{
      
      var rate2 = $.trim($(this).val());
      if(isNaN(rate2) || rate2=="" )
      {
        rate2 = 0;
      }  
      if(intRegex.test(rate2))
      {
        $(this).val((rate2 +'.00000'));
      }
      var r2 = $(this).val();  //rate
      var tp2 =  parseFloat($(this).parent().parent().find('[id*="MONTH8_TP"]').val()).toFixed(3); 

      var pval2 = parseFloat(parseFloat(tp2) * parseFloat(r2) ).toFixed(3);
      $(this).parent().parent().find('[id*="MONTH8_PV"]').val(pval2); 
     
      //set inventory 
      var iv2 =  parseFloat($(this).parent().parent().find('[id*="MONTH8_IV"]').val()).toFixed(3);
      $(this).parent().parent().find('[id*="MONTH9_OP"]').val(iv2); //inventory become opening value for next month

      event.preventDefault();
}); 
//-------------------------- Nov end

//------------------Dec
$('#Material').on('blur',"[id*='MONTH9_AP_']",function()
{
      var A2 =  parseFloat($(this).parent().parent().find('[id*="MONTH9_OP"]').val()).toFixed(3); //OPENNING 
      if(isNaN(A2))
      {
        A2=0;
      }

      var B2 =  parseFloat($(this).parent().parent().find('[id*="MONTH9_SL"]').val()).toFixed(3); //Sales 
      if(isNaN(B2))
      {
        B2=0;
      }
    
      var ap2= $.trim($(this).val());
      if(isNaN(ap2) || ap2=="" )
      {
        ap2 = 0;
      }  
      if(intRegex.test(ap2))
      {
        $(this).val((ap2 +'.000'));
      }
      var D2 = $(this).val();  //Add Purchase

      var C2 =  parseFloat($(this).parent().parent().find('[id*="MONTH9_PR"]').val()).toFixed(3); //Purchase (Sales-Opening) 
      if(isNaN(C2))
      {
        C2=0;
      }
         
      var E2 = parseFloat( parseFloat(D2) + parseFloat(C2) ).toFixed(3) ;    
      if(isNaN(E2)){
        E2=0;
      }
      $(this).parent().parent().find('[id*="MONTH9_TP"]').val(E2); //To Be procured 

      //INVENTORY =A2+C2+D2-B2
      var inventory2 = parseFloat( parseFloat(A2)+ parseFloat(C2)+ parseFloat(D2) ) - parseFloat(B2);
      if(isNaN(C2))
      {
        inventory2=0;
      }
      inventory2 = parseFloat(inventory2).toFixed(3)
      $(this).parent().parent().find('[id*="MONTH9_IV"]').val( parseFloat(inventory2).toFixed(3) ); 
      $(this).parent().parent().find('[id*="MONTH10_OP"]').val(inventory2); //inventory become opening value for next month


      var invdays2 = parseFloat(parseFloat(inventory2)/20 ).toFixed(3);
      if(isNaN(invdays2))
      {
        invdays2=0;
      }
      invdays2 = Math.ceil(invdays2);
      $(this).parent().parent().find('[id*="MONTH9_ND"]').val(invdays2); //inventory days
      
      var rate2 = parseFloat($(this).parent().parent().find('[id*="MONTH9_RT"]').val()).toFixed(3); //Rate
      if(isNaN(rate2))
      {
        rate2=0;
      }

      var purval2 = parseFloat(parseFloat(E2) * parseFloat(rate2) ).toFixed(3);
      $(this).parent().parent().find('[id*="MONTH9_PV"]').val(purval2); 
    event.preventDefault();
});  

$('#Material').on('blur',"[id*='MONTH9_RT']",function()
{
      
      var rate2 = $.trim($(this).val());
      if(isNaN(rate2) || rate2=="" )
      {
        rate2 = 0;
      }  
      if(intRegex.test(rate2))
      {
        $(this).val((rate2 +'.00000'));
      }
      var r2 = $(this).val();  //rate
      var tp2 =  parseFloat($(this).parent().parent().find('[id*="MONTH9_TP"]').val()).toFixed(3); 

      var pval2 = parseFloat(parseFloat(tp2) * parseFloat(r2) ).toFixed(3);
      $(this).parent().parent().find('[id*="MONTH9_PV"]').val(pval2); 
     
      //set inventory 
      var iv2 =  parseFloat($(this).parent().parent().find('[id*="MONTH9_IV"]').val()).toFixed(3);
      $(this).parent().parent().find('[id*="MONTH10_OP"]').val(iv2); //inventory become opening value for next month

      event.preventDefault();
}); 
//-------------------------- Dec end

//------------------Jan
$('#Material').on('blur',"[id*='MONTH10_AP_']",function()
{
      var A2 =  parseFloat($(this).parent().parent().find('[id*="MONTH10_OP"]').val()).toFixed(3); //OPENNING 
      if(isNaN(A2))
      {
        A2=0;
      }

      var B2 =  parseFloat($(this).parent().parent().find('[id*="MONTH10_SL"]').val()).toFixed(3); //Sales 
      if(isNaN(B2))
      {
        B2=0;
      }
    
      var ap2= $.trim($(this).val());
      if(isNaN(ap2) || ap2=="" )
      {
        ap2 = 0;
      }  
      if(intRegex.test(ap2))
      {
        $(this).val((ap2 +'.000'));
      }
      var D2 = $(this).val();  //Add Purchase

      var C2 =  parseFloat($(this).parent().parent().find('[id*="MONTH10_PR"]').val()).toFixed(3); //Purchase (Sales-Opening) 
      if(isNaN(C2))
      {
        C2=0;
      }
         
      var E2 = parseFloat( parseFloat(D2) + parseFloat(C2) ).toFixed(3) ;    
      if(isNaN(E2)){
        E2=0;
      }
      $(this).parent().parent().find('[id*="MONTH10_TP"]').val(E2); //To Be procured 

      //INVENTORY =A2+C2+D2-B2
      var inventory2 = parseFloat( parseFloat(A2)+ parseFloat(C2)+ parseFloat(D2) ) - parseFloat(B2);
      if(isNaN(C2))
      {
        inventory2=0;
      }
      inventory2 = parseFloat(inventory2).toFixed(3)
      $(this).parent().parent().find('[id*="MONTH10_IV"]').val( parseFloat(inventory2).toFixed(3) ); 
      $(this).parent().parent().find('[id*="MONTH11_OP"]').val(inventory2); //inventory become opening value for next month


      var invdays2 = parseFloat(parseFloat(inventory2)/20 ).toFixed(3);
      if(isNaN(invdays2))
      {
        invdays2=0;
      }
      invdays2 = Math.ceil(invdays2);
      $(this).parent().parent().find('[id*="MONTH10_ND"]').val(invdays2); //inventory days
      
      var rate2 = parseFloat($(this).parent().parent().find('[id*="MONTH10_RT"]').val()).toFixed(3); //Rate
      if(isNaN(rate2))
      {
        rate2=0;
      }

      var purval2 = parseFloat(parseFloat(E2) * parseFloat(rate2) ).toFixed(3);
      $(this).parent().parent().find('[id*="MONTH10_PV"]').val(purval2); 
    event.preventDefault();
});  

$('#Material').on('blur',"[id*='MONTH10_RT']",function()
{
      
      var rate2 = $.trim($(this).val());
      if(isNaN(rate2) || rate2=="" )
      {
        rate2 = 0;
      }  
      if(intRegex.test(rate2))
      {
        $(this).val((rate2 +'.00000'));
      }
      var r2 = $(this).val();  //rate
      var tp2 =  parseFloat($(this).parent().parent().find('[id*="MONTH10_TP"]').val()).toFixed(3); 

      var pval2 = parseFloat(parseFloat(tp2) * parseFloat(r2) ).toFixed(3);
      $(this).parent().parent().find('[id*="MONTH10_PV"]').val(pval2); 
     
      //set inventory 
      var iv2 =  parseFloat($(this).parent().parent().find('[id*="MONTH10_IV"]').val()).toFixed(3);
      $(this).parent().parent().find('[id*="MONTH11_OP"]').val(iv2); //inventory become opening value for next month

      event.preventDefault();
}); 
//-------------------------- Jan end

//------------------Feb
$('#Material').on('blur',"[id*='MONTH11_AP_']",function()
{
      var A2 =  parseFloat($(this).parent().parent().find('[id*="MONTH11_OP"]').val()).toFixed(3); //OPENNING 
      if(isNaN(A2))
      {
        A2=0;
      }

      var B2 =  parseFloat($(this).parent().parent().find('[id*="MONTH11_SL"]').val()).toFixed(3); //Sales 
      if(isNaN(B2))
      {
        B2=0;
      }
    
      var ap2= $.trim($(this).val());
      if(isNaN(ap2) || ap2=="" )
      {
        ap2 = 0;
      }  
      if(intRegex.test(ap2))
      {
       $(this).val((ap2 +'.000'));
      
      }


      var D2 = $(this).val();  //Add Purchase

      var C2 =  parseFloat($(this).parent().parent().find('[id*="MONTH11_PR"]').val()).toFixed(3); //Purchase (Sales-Opening) 
      if(isNaN(C2))
      {
        C2=0;
      }
         
      var E2 = parseFloat( parseFloat(D2) + parseFloat(C2) ).toFixed(3) ;    
      if(isNaN(E2)){
        E2=0;
      }
      $(this).parent().parent().find('[id*="MONTH11_TP"]').val(E2); //To Be procured 

      //INVENTORY =A2+C2+D2-B2
      var inventory2 = parseFloat( parseFloat(A2)+ parseFloat(C2)+ parseFloat(D2) ) - parseFloat(B2);
      if(isNaN(C2))
      {
        inventory2=0;
      }
      inventory2 = parseFloat(inventory2).toFixed(3)
      $(this).parent().parent().find('[id*="MONTH11_IV"]').val( parseFloat(inventory2).toFixed(3) ); 
      $(this).parent().parent().find('[id*="MONTH12_OP"]').val(inventory2); //inventory become opening value for next month


      var invdays2 = parseFloat(parseFloat(inventory2)/20 ).toFixed(3);
      if(isNaN(invdays2))
      {
        invdays2=0;
      }
      invdays2 = Math.ceil(invdays2);
      $(this).parent().parent().find('[id*="MONTH11_ND"]').val(invdays2); //inventory days
      
      var rate2 = parseFloat($(this).parent().parent().find('[id*="MONTH11_RT"]').val()).toFixed(3); //Rate
      if(isNaN(rate2))
      {
        rate2=0;
      }

      var purval2 = parseFloat(parseFloat(E2) * parseFloat(rate2) ).toFixed(3);
      $(this).parent().parent().find('[id*="MONTH11_PV"]').val(purval2); 
    event.preventDefault();
});  

$('#Material').on('blur',"[id*='MONTH11_RT']",function()
{
      
      var rate2 = $.trim($(this).val());
      if(isNaN(rate2) || rate2=="" )
      {
        rate2 = 0;
      }  
      if(intRegex.test(rate2))
      {
        $(this).val((rate2 +'.00000'));
      }
      var r2 = $(this).val();  //rate
      var tp2 =  parseFloat($(this).parent().parent().find('[id*="MONTH11_TP"]').val()).toFixed(3); 

      var pval2 = parseFloat(parseFloat(tp2) * parseFloat(r2) ).toFixed(3);
      $(this).parent().parent().find('[id*="MONTH11_PV"]').val(pval2); 
     
      //set inventory 
      var iv2 =  parseFloat($(this).parent().parent().find('[id*="MONTH11_IV"]').val()).toFixed(3);
      $(this).parent().parent().find('[id*="MONTH12_OP"]').val(iv2); //inventory become opening value for next month

      event.preventDefault();
}); 
//-------------------------- Feb end

//------------------Mar
$('#Material').on('blur',"[id*='MONTH12_AP_']",function()
{
      var A2 =  parseFloat($(this).parent().parent().find('[id*="MONTH12_OP"]').val()).toFixed(3); //OPENNING 
      if(isNaN(A2))
      {
        A2=0;
      }

      var B2 =  parseFloat($(this).parent().parent().find('[id*="MONTH12_SL"]').val()).toFixed(3); //Sales 
      if(isNaN(B2))
      {
        B2=0;
      }
    
      var ap2= $.trim($(this).val());
      if(isNaN(ap2) || ap2=="" )
      {
        ap2 = 0;
      }  
      if(intRegex.test(ap2))
      {
        $(this).val((ap2 +'.000'));
      }
      var D2 = $(this).val();  //Add Purchase

      var C2 =  parseFloat($(this).parent().parent().find('[id*="MONTH12_PR"]').val()).toFixed(3); //Purchase (Sales-Opening) 
      if(isNaN(C2))
      {
        C2=0;
      }
         
      var E2 = parseFloat( parseFloat(D2) + parseFloat(C2) ).toFixed(3) ;    
      if(isNaN(E2)){
        E2=0;
      }
      $(this).parent().parent().find('[id*="MONTH12_TP"]').val(E2); //To Be procured 

      //INVENTORY =A2+C2+D2-B2
      var inventory2 = parseFloat( parseFloat(A2)+ parseFloat(C2)+ parseFloat(D2) ) - parseFloat(B2);
      if(isNaN(C2))
      {
        inventory2=0;
      }
      inventory2 = parseFloat(inventory2).toFixed(3)
      $(this).parent().parent().find('[id*="MONTH12_IV"]').val( parseFloat(inventory2).toFixed(3) ); 
      //$(this).parent().parent().find('[id*="MONTH13_OP"]').val(inventory2); //inventory become opening value for next month


      var invdays2 = parseFloat(parseFloat(inventory2)/20 ).toFixed(3);
      if(isNaN(invdays2))
      {
        invdays2=0;
      }
      invdays2 = Math.ceil(invdays2);
      $(this).parent().parent().find('[id*="MONTH12_ND"]').val(invdays2); //inventory days
      
      var rate2 = parseFloat($(this).parent().parent().find('[id*="MONTH12_RT"]').val()).toFixed(3); //Rate
      if(isNaN(rate2))
      {
        rate2=0;
      }

      var purval2 = parseFloat(parseFloat(E2) * parseFloat(rate2) ).toFixed(3);
      $(this).parent().parent().find('[id*="MONTH12_PV"]').val(purval2); 
    event.preventDefault();
});  

$('#Material').on('blur',"[id*='MONTH12_RT']",function()
{
      
      var rate2 = $.trim($(this).val());
      if(isNaN(rate2) || rate2=="" )
      {
        rate2 = 0;
      }  
      if(intRegex.test(rate2))
      {
        $(this).val((rate2 +'.00000'));
      }
      var r2 = $(this).val();  //rate
      var tp2 =  parseFloat($(this).parent().parent().find('[id*="MONTH12_TP"]').val()).toFixed(3); 

      var pval2 = parseFloat(parseFloat(tp2) * parseFloat(r2) ).toFixed(3);
      $(this).parent().parent().find('[id*="MONTH12_PV"]').val(pval2); 
     
      //set inventory 
      var iv2 =  parseFloat($(this).parent().parent().find('[id*="MONTH12_IV"]').val()).toFixed(3);
      //$(this).parent().parent().find('[id*="MONTH13_OP"]').val(iv2); //inventory become opening value for next month

      event.preventDefault();
}); 
//-------------------------- Mar end

function applyForceNum(){
  

  // $('#example2').on('blur','[id*="MONTH1_AP"]',function(){
  //     if(intRegex.test($(this).val())){
  //       $(this).val($(this).val()+'.000')
  //     }
  //     event.preventDefault();
  // });

  
  // $('#example2').on('blur','[id*="MONTH1_RT"]',function(){
  //     if(intRegex.test($(this).val())){
  //       $(this).val($(this).val()+'.00000')
  //     }
  //     event.preventDefault();
  // });

    

}
 

</script>


@endpush