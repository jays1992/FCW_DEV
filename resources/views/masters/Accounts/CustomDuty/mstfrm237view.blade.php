
@extends('layouts.app')
@section('content')
    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[237,'index'])}}" class="btn singlebt">Custom Duty & SWS Rate</a>
                </div><!--col-2-->
                <div class="col-lg-10 topnav-pd">
                        <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
                        <button class="btn topnavbt" id="btnSaveOSO" disabled="disabled"><i class="fa fa-save"></i> Save</button>
                        <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt" id="btnUndo"  disabled="disabled"><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove" {{($objRights->APPROVAL1||$objRights->APPROVAL2||$objRights->APPROVAL3||$objRights->APPROVAL4||$objRights->APPROVAL5) == 1 ? 'disabled' : 'disabled'}}><i class="fa fa-lock"></i> Approved</button>
                        <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                        <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                </div>
            </div>
    </div><!--topnav-->	
    <!-- multiple table-responsive table-wrapper-scroll-y my-custom-scrollbar -->
    <div class="container-fluid purchase-order-view">
        <form id="frm_trn_custom_duty"  method="POST">   
            @csrf
            {{isset($objCUSTOM->VCDID[0]) ? method_field('PUT') : '' }}
            <div class="container-fluid filter">

            <div class="container-fluid filter">
                    <div class="inner-form">
                        <div class="row">
                            <div class="col-lg-2 pl"><p>Doc No</p></div>
                            <div class="col-lg-2 pl">
         
                           
                  
                                <input type="text" name="DOCNO" disabled id="DOCNO" readonly value="{{ $objCUSTOM->VCD_DOCNO }}" class="form-control mandatory"  autocomplete="off" style="text-transform:uppercase" autofocus >
                                                     
                            </div>
                            <div class="col-lg-2 pl"><p>Date</p></div>
                            <div class="col-lg-2 pl">
                                <input type="date" name="CD_DT" disabled id="CD_DT" value="{{ $objCUSTOM->VCD_DOCDT }}" class="form-control mandatory" autocomplete="off"  placeholder="dd/mm/yyyy" >
                               
                            </div>    
                      
                            
                        </div>     

                        <div class="row">
                        <div class="col-lg-2 pl"><p>Validity From</p></div>
                            <div class="col-lg-2 pl">
                                <input type="date" disabled name="VALIDITY_FROM" id="VALIDITY_FROM" value="{{ $objCUSTOM->FROM_DT }}" class="form-control mandatory" autocomplete="off"  placeholder="dd/mm/yyyy" >
                               
                            </div>    
                            <div class="col-lg-2 pl"><p>Validity To</p></div>
                            <div class="col-lg-2 pl">
                                <input type="date" disabled name="VALIDITY_TO" id="VALIDITY_TO" value="{{ $objCUSTOM->TO_DT }}" class="form-control mandatory" autocomplete="off"  placeholder="dd/mm/yyyy" >
                               
                            </div>    
                      
                            
                        </div>     




              <div class="row" >
                <div class="col-lg-2 pl" ><p>De-Activated</p></div>
                <div class="col-lg-2 pl pl">
                <input type="checkbox" disabled   name="DEACTIVATED"  id="deactive-checkbox_0" {{$objCUSTOM->DEACTIVATED == 1 ? "checked" : ""}}
                 value='{{$objCUSTOM->DEACTIVATED == 1 ? 1 : 0}}' tabindex="2"  >
                </div>
                
                <div class="col-lg-2 pl" ><p>Date of De-Activated</p></div>
                <div class="col-lg-2 pl">
                  <input type="date" disabled name="DODEACTIVATED" class="form-control" id="DODEACTIVATED" {{$objCUSTOM->DEACTIVATED == 1 ? "" : "disabled"}} value="{{isset($objCUSTOM->DODEACTIVATED) && $objCUSTOM->DODEACTIVATED !="" && $objCUSTOM->DODEACTIVATED !="1900-01-01" ? $objCUSTOM->DODEACTIVATED:''}}" tabindex="3" placeholder="dd/mm/yyyy"  />
                </div>
             </div>

                    </div>          
                        </div>                 


                    <div class="container-fluid purchase-order-view">

                        <div class="row">
                    
                            <div class="tab-content">
                            

                                <div id="MaterialCustom" class="tab-pane fade in active">
                                    <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:380px;margin-top:10px;" >
                                        <table id="example3" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                                            <thead id="thead2"  style="position: sticky;top: 0">
                                                <tr>
                                                    <th>Vendor Code<input class="form-control" type="hidden" name="Row_Count2" id ="Row_Count2"></th>
                                                    <th>Vendor Name</th>
                                                    <th>Item Code</th>
                                                    <th>Item Name</th>
                                                    <th>Normal BCD %</th>
                                                    <th>Cess on Normal BCD %</th>                                              
                                                    <th>FTA BCD %</th>
                                                    <th>CEPA BCD %</th>
                                                    <th>SWS Rate %</th>
                                                    <th>TAX %</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @if(!empty($objCUSTOMMAT))
                                           @foreach($objCUSTOMMAT as $key => $row)    
                                            <tr  class="participantRow1">
                                                    <td><input type="text" disabled name={{"VENDOR_CODE_".$key}} id ={{"VENDOR_CODE_".$key}}  value="{{ $row->VCODE }}" onclick="get_vendor($(this).attr('id'))" class="form-control"  autocomplete="off"  readonly/></td>                                                
                                                    <td hidden><input type="text" name={{"VENDORID_REF_".$key}} id ={{"VENDORID_REF_".$key}}  value="{{ $row->VID_REF }}"  class="form-control" autocomplete="off" />
                                                    <input type="text" name="rowscount1[]"  />
                                                    </td>
                                                    <td><input type="text" disabled style="width: 243px;" name={{"VENDOR_NAME_".$key}} id ={{"VENDOR_NAME_".$key}}  value="{{ $row->VENDOR_NAME }}"   class="form-control"  autocomplete="off"  readonly/></td>
                                                    <td><input type="text" disabled name={{"MainItemCode1_".$key}} id ={{"MainItemCode1_".$key}}  value="{{ $row->ICODE }}"  onclick="get_item($(this).attr('id'))" class="form-control"  autocomplete="off"  readonly/></td>                                                
                                                    <td hidden><input type="hidden" name={{"MainItemId1_Ref_".$key}} id ={{"MainItemId1_Ref_".$key}}  value="{{ $row->ITEMID_REF }}"   class="form-control" autocomplete="off" /></td>
                                                    <td><input type="text" disabled name={{"MainItemName1_".$key}} id ={{"MainItemName1_".$key}}  value="{{ $row->NAME }}"   class="form-control"  autocomplete="off"  readonly/></td>
                                                                                     
                                                   
                                                    <td><input type="text" disabled name={{"NORMAL_BCD_".$key}} id ={{"NORMAL_BCD_".$key}}  value="{{ $row->BCD!='.0000'? $row->BCD:'' }}"  maxlength="8" class="form-control"  autocomplete="off"  /></td>
                                                    <td><input type="text" disabled name={{"CESS_NORMAL_BCD_".$key}} id ={{"CESS_NORMAL_BCD_".$key}}  value="{{ $row->CESS_BCD!='.0000'? $row->CESS_BCD:'' }}"  maxlength="8" class="form-control"  autocomplete="off"  /></td>
                                                    <td><input type="text" disabled name={{"FTA_BCD_".$key}} id ={{"FTA_BCD_".$key}}  value="{{ $row->FTA_BCD!='.0000'? $row->FTA_BCD:'' }}"  class="form-control" maxlength="8"  autocomplete="off"  /></td>
                                                    <td><input type="text" disabled name={{"CEPA_BCD_".$key}} id ={{"CEPA_BCD_".$key}}  value="{{ $row->CEPA_BCD!='.0000'? $row->CEPA_BCD:'' }}"  class="form-control" maxlength="8"  autocomplete="off"  /></td>
                                                    <td><input type="text" disabled name={{"SW_RATE_".$key}} id ={{"SW_RATE_".$key}}  value=" {{ $row->SWS!='.0000'? $row->SWS:'' }}"  class="form-control"  maxlength="8" autocomplete="off"  /></td>
                                                    <td><input type="text" disabled name={{"TAX_".$key}} id ={{"TAX_".$key}}  value="{{ $row->TAX!='.0000'? $row->TAX:'' }}"  class="form-control" maxlength="8"  autocomplete="off"  /></td>
                                                    <td align="center" ><button class="btn add" title="add" disabled data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button><button class="btn remove smaterial" disabled title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
                                                
                                                </tr>
                                                @endforeach          

                                              @else 
                                              <tr  class="participantRow1">
                                                    <td><input type="text" disabled name="VENDOR_CODE_0" id="VENDOR_CODE_0" onclick="get_vendor($(this).attr('id'))" class="form-control"  autocomplete="off"  readonly/></td>                                                
                                                    <td hidden><input type="text" disabled name="VENDORID_REF_0" id="VENDORID_REF_0" class="form-control" autocomplete="off" />
                                                    <input type="text" name="rowscount1[]"  />
                                                    </td>
                                                    <td><input type="text" disabled style="width: 243px;" name="VENDOR_NAME_0" id="VENDOR_NAME_0" class="form-control"  autocomplete="off"  readonly/></td>
                                                    <td><input type="text" disabled name="MainItemCode1_0" id="MainItemCode1_0" onclick="get_item($(this).attr('id'))" class="form-control"  autocomplete="off"  readonly/></td>                                                
                                                    <td hidden><input type="hidden" name="MainItemId1_Ref_0" id="MainItemId1_Ref_0" class="form-control" autocomplete="off" /></td>
                                                    <td><input type="text" disabled name="MainItemName1_0" id="MainItemName1_0" class="form-control"  autocomplete="off"  readonly/></td>
                                                                                     
                                                   
                                                    <td><input type="text" disabled name="NORMAL_BCD_0" id="NORMAL_BCD_0" maxlength="8" class="form-control"  autocomplete="off"  /></td>
                                                    <td><input type="text" disabled name="CESS_NORMAL_BCD_0" id="CESS_NORMAL_BCD_0" maxlength="8" class="form-control"  autocomplete="off"  /></td>
                                                    <td><input type="text" disabled name="FTA_BCD_0" id="FTA_BCD_0" class="form-control" maxlength="8"  autocomplete="off"  /></td>
                                                    <td><input type="text" disabled name="CEPA_BCD_0" id="CEPA_BCD_0" class="form-control" maxlength="8"  autocomplete="off"  /></td>
                                                    <td><input type="text" disabled name="SW_RATE_0" id="SW_RATE_0" class="form-control"  maxlength="8" autocomplete="off"  /></td>
                                                    <td><input type="text" disabled name="TAX_0" id="TAX_0" class="form-control" maxlength="8"  autocomplete="off"  /></td>
                                                    <td align="center" ><button class="btn add" disabled title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button><button class="btn remove smaterial" title="Delete" disabled data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
                                                
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
                </div>


    </div><!--purchase-order-view-->

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

</style>
@endpush
@push('bottom-scripts')
<script>
$("#YesBtn").click(function(){

$("#alert").modal('hide');
var customFnName = $("#YesBtn").data("funcname");
    window[customFnName]();

}); //yes button



//no button
$("#NoBtn").click(function(){
    $("#alert").modal('hide');
    $("#SONO").focus();
});

//ok button
$("#OkBtn").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn").hide();
    $(".text-danger").hide();
    window.location.href = '{{route("master",[237,"index"]) }}';
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

 
$('#btnExit').on('click', function() {
  var viewURL = '{{route('home')}}';
              window.location.href=viewURL;
});

</script>


@endpush