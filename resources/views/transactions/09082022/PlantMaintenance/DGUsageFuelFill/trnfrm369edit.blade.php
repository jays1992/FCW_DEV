
@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
    <div class="row">
        <div class="col-lg-2">
        <a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">DG Usage & Fuel Fill </a>
        </div>

        <div class="col-lg-10 topnav-pd">
          <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
          <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
          <button class="btn topnavbt" id="btnSaveFormData" ><i class="fa fa-save"></i> Save</button>
          <button style="display:none" class="btn topnavbt buttonload"> <i class="fa fa-refresh fa-spin"></i> {{Session::get('save')}}</button>
          <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
          <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
          <button class="btn topnavbt" id="btnUndo"  ><i class="fa fa-undo"></i> Undo</button>
          <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
          <button style="display:none" class="btn topnavbt buttonload_approve" > <i class="fa fa-refresh fa-spin"></i> {{Session::get('approve')}}</button>
          <button class="btn topnavbt" id="btnApprove" {{($objRights->APPROVAL1||$objRights->APPROVAL2||$objRights->APPROVAL3||$objRights->APPROVAL4||$objRights->APPROVAL5) == 1 ? '' : 'disabled'}}><i class="fa fa-lock"></i> Approved</button>
          <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
          <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
        </div>
    </div>
</div>
  
<div class="container-fluid purchase-order-view filter">     
  
<form id="edit_trn_form" method="POST"  >
            @CSRF
            <div class="inner-form">
          <div class="row">
            <div class="col-lg-2 pl"><p>Document No</p></div>
            <div class="col-lg-2 pl">
         
                <input type="text" name="DGUFF_NO" id="DGUFF_NO" value="{{ $objResponse->DGUFF_NO }}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase" autofocus >
     
            
            </div>
            
            <div class="col-lg-2 pl"><p>Document Date</p></div>
            <div class="col-lg-2 pl">
                <input type="date" name="DGUFF_DATE" id="DGUFF_DATE" onchange="checkPeriodClosing('{{$FormId}}',this.value,1)" value="{{ $objResponse->DGUFF_DATE }}" class="form-control mandatory" autocomplete="off" placeholder="dd/mm/yyyy" >
            </div>      
         </div>


           <div class="row">
                  <div class="col-lg-2 pl"><p>Genset Code</p></div>
                  <div class="col-lg-2 pl">
                 
                
                  <input type="text" name="Machinepopup" id="txtMachinepopup" class="form-control mandatory" value="{{ $objResponse->MACHINE_NO }}"  autocomplete="off"  readonly/>
                    <input type="hidden" name="MACHINE_REF" id="MACHINE_REF" class="form-control" value="{{ $objResponse->MACHINEID_REF }}" autocomplete="off" />
                   
                     
                              
         
                    </div>
      

                  <div class="col-lg-2 pl"><p>Genset Description	</p></div>
                  <div class="col-lg-2 pl">
                    <input type="text" name="MACHINE_DESC" id="MACHINE_DESC" class="form-control mandatory" value="{{ $objResponse->MACHINE_DESC }}" readonly maxlength="200"  />
                  
                  </div>
            </div>

            <div class="row">
            <div class="col-lg-2 pl"><p>Fuel Type</p></div>
              <div class="col-lg-2 pl">
              <input type="text" name="FuelType_popup" id="txtFuelType_popup" class="form-control mandatory" value="{{ $objResponse->FUEL_CODE }}-{{ $objResponse->FUEL_DESC }}"  autocomplete="off"  readonly/>
                    <input type="hidden" name="FUELTYPE_REF" id="FUELTYPE_REF" class="form-control" value="{{ $objResponse->FUELID_REF }}" autocomplete="off" />
              </div>
                <div class="col-lg-2 pl"><p>Standard Consumption (Per Hour)		</p></div>
              <div class="col-lg-2 pl">
              <input type="text" name="CONSUMPTION_PER_HOUR" id="CONSUMPTION_PER_HOUR" class="form-control mandatory" value="{{ $objResponse->STANDARD_CONSUMPTION_PH }}"  maxlength="200"  />
              </div>
              
            </div>

            <div class="row">
            <div class="col-lg-2 pl"><p>UOM</p></div>
                <div class="col-lg-2 pl">                 
              
                <input type="text" name="UOM_popup" id="txtUOM_popup" class="form-control mandatory" value="{{ $objResponse->UOMCODE }}-{{ $objResponse->DESCRIPTIONS }}"  autocomplete="off"  readonly/>
                    <input type="hidden" name="UOMID_REF" id="UOMID_REF" class="form-control" value="{{ $objResponse->UOMID_REF }}" autocomplete="off" />            
                </div>
    
      
            </div>
            <div class="row">
            <div class="col-lg-2 pl"><p>Usage</p></div>
            <div class="col-lg-1 pl">
                <input type="checkbox" name="TYPE" id="USAGE"  value="usage" {{isset($objResponse) && $objResponse->USAGE=='1'?'checked':''}} />  
            </div>
            <div class="col-lg-1 pl"><p>Fuel Consumption</p></div>
            <div class="col-lg-1 pl">
                <input type="checkbox" name="TYPE" id="FUEL_CONSUMPTION" value="fuel_consumption" {{isset($objResponse) && $objResponse->FUEL_CONSUMPTION=='1'?'checked':''}}  />
            </div>     
            <div class="col-lg-1 pl"><p>Both</p></div>
            <div class="col-lg-1 pl">
                <input type="checkbox" name="TYPE" id="BOTH" value="both"  {{isset($objResponse) && $objResponse->BOTH=='1'?'checked':''}} />
            </div>     
        </div>


          <div class="row">
          <div class="col-lg-4 pl"><p>Usage</p></div>
           
            <div class="col-lg-4 pl"><p>Fuel Consumption</p></div>
            <div class="col-lg-2 pl">
            
            </div>
    
          </div>
         
          <div class="row">
          <div class="col-lg-2 pl"><p>From Date</p></div>
            <div class="col-lg-2 pl">
              <input type="date" name="FROMDATE" id="FROMDATE" value="{{ $objResponse->USAGE_FROMDATE }}" class="form-control mandatory" autocomplete="off" placeholder="dd/mm/yyyy" >
            </div>
            <div class="col-lg-2 pl"><p>Opening Fuel</p></div>
            <div class="col-lg-2 pl">
              <input type="text" name="OPENDING_FUEL" id="OPENDING_FUEL" class="form-control "  autocomplete="off" value="{{ $objResponse->OPENING_FUEL }}" maxlength="50" />
            </div>
       
          </div>


          <div class="row">
          <div class="col-lg-2 pl"><p>From time</p></div>
            <div class="col-lg-2 pl">
            <input type="time" name="FROMTIME" id="FROMTIME" class="form-control mandatory" value="{{ $FROMTIME }}"  autocomplete="off"  />
            </div>
            <div class="col-lg-2 pl"><p>Filled Fuel</p></div>
            <div class="col-lg-2 pl">
              <input type="text" name="FILLED_FUEL" id="FILLED_FUEL" class="form-control " value="{{ $objResponse->FILLED_FUEL }}"  autocomplete="off" maxlength="50" />
            </div>       
          </div>
          <div class="row">
          <div class="col-lg-2 pl"><p>To Date</p></div>
            <div class="col-lg-2 pl">
            <input type="date" name="TODATE" id="TODATE" value="{{ $objResponse->USAGE_TODATE }}" class="form-control mandatory" autocomplete="off" placeholder="dd/mm/yyyy" >
            </div>
            <div class="col-lg-2 pl"><p>Closing Fuel</p></div>
            <div class="col-lg-2 pl">
              <input type="text" name="CLOSING_FUEL" id="CLOSING_FUEL" readonly class="form-control " value="{{ $objResponse->CLOSING_FUEL }}"  autocomplete="off" maxlength="50" />
            </div>       
          </div>
          <div class="row">
          <div class="col-lg-2 pl"><p>To Time</p></div>
            <div class="col-lg-2 pl">
            <input type="time" name="TOTIME" id="TOTIME" class="form-control mandatory" value="{{ $TOTIME }}"  autocomplete="off"  />
            </div>
            <div class="col-lg-2 pl"><p>Fuel Filled by</p></div>
            <div class="col-lg-2 pl">
              <input type="text" name="FUEL_FILLED_BY" id="FUEL_FILLED_BY" class="form-control " value="{{ $objResponse->FUEL_FILLEDBY }}"  autocomplete="off" maxlength="50" />
            </div>       
          </div>
          <div class="row">
          <div class="col-lg-2 pl"><p>Genset Started by</p></div>
            <div class="col-lg-2 pl">
              <input type="text" name="GENSET_STARTED_BY" id="GENSET_STARTED_BY" class="form-control " value="{{ $objResponse->GENSET_STARTEDBY }}"  autocomplete="off" maxlength="20" />
            </div>
            <div class="col-lg-2 pl"><p>Remarks</p></div>
            <div class="col-lg-2 pl">
              <input type="text" name="CONSUMPTION_REMARKS" id="CONSUMPTION_REMARKS" class="form-control " value="{{ $objResponse->FUEL_REMARKS }}"  autocomplete="off" maxlength="50" />
            </div>       
          </div>
          <div class="row">
          <div class="col-lg-2 pl"><p>Genset Stopped by</p></div>
            <div class="col-lg-2 pl">
              <input type="text" name="GENSET_STOPPED_BY" id="GENSET_STOPPED_BY" class="form-control " value="{{ $objResponse->GENSET_STOPPEDBY }}"  autocomplete="off" maxlength="20" />
            </div>
            <div class="col-lg-2 pl"><p>Meter (KWH) Started reading</p></div>
            <div class="col-lg-2 pl">
              <input type="text" name="KWH_START_CONSUMPTION" id="KWH_START_CONSUMPTION" class="form-control " value="{{ $objResponse->FUEL_READING_START_KWH }}"  autocomplete="off" maxlength="50" />
            </div>       
          </div>
          <div class="row">
          <div class="col-lg-2 pl"><p>Meter (KWH) Start reading</p></div>
            <div class="col-lg-2 pl">
              <input type="text" name="KWH_START_USAGE" id="KWH_START_USAGE" class="form-control " value="{{ $objResponse->USAGE_READING_START_KWH }}"  autocomplete="off" maxlength="20" />
            </div>
            <div class="col-lg-2 pl"><p>Meter (KWH) Ended Reading</p></div>
            <div class="col-lg-2 pl">
              <input type="text" name="KWH_END_CONSUMPTION" id="KWH_END_CONSUMPTION" class="form-control " value="{{ $objResponse->FUEL_READING_END_KWH }}"  autocomplete="off" maxlength="50" />
            </div>       
          </div>
          <div class="row">
          <div class="col-lg-2 pl"><p>Meter (KWH) End Reading</p></div>
            <div class="col-lg-2 pl">
              <input type="text" name="KWH_END_USAGE" id="KWH_END_USAGE" class="form-control " value="{{ $objResponse->USAGE_READING_END_KWH }}" autocomplete="off" maxlength="20" />
            </div>
            <div class="col-lg-2 pl"><p>Observation</p></div>
            <div class="col-lg-2 pl">
              <input type="text" name="OBSERVATION" id="OBSERVATION" class="form-control " value="{{ $objResponse->OBSERVATION }}"  autocomplete="off" maxlength="50" />
            </div>       
          </div>
          <div class="row">

            <div class="col-lg-2 pl"><p>Remarks</p></div>
            <div class="col-lg-6 pl">
              <input type="text" name="REMARKS" id="REMARKS" class="form-control "  value="{{ $objResponse->USAGE_REMARKS }}" autocomplete="off" maxlength="50" />
            </div>       
          </div>






        
          <br/>
          <br/>
      
        </div>
          </form>
      </div><!--purchase-order-view-->
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


<!-- Machine Dropdown  -->
<div id="machine_popup" class="modal" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-md" style="width:60%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" id="item_closePopup1">&times;</button>
            </div>
            <div class="modal-body">
                <div class="tablename"><p>Genset List</p></div>
                <div class="single single-select table-responsive table-wrapper-scroll-y my-custom-scrollbar">
                <input type="hidden" class="mainitem_tab1">
                    <table id="MachineTable" class="display nowrap table table-striped table-bordered" style="width:100%;">
                        <thead>
                            <tr>
                            <th style="width:10%;">Select</th> 
                                <th style="width:30%;"> Code</th>
                                <th style="width:60%;"> Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                            <th style="text-align:center; width:10%;">&#10004;</th>

                       

                                <td style="width:30%;"> 
                                    <input type="text" id="Machinecodesearch" class="form-control" onkeyup="MachineCodeFunction()"  />
                                </td>
                                <td style="width:60%;">
                                    <input type="text" id="Machinenamesearch" class="form-control" onkeyup="MachineNameFunction()"  />
                                </td>
                          
                            </tr>
                        </tbody>
                    </table>
                    <table id="MachineTable2" class="display nowrap table table-striped table-bordered">
                        <thead id="thead2">
                        <tr>
        
                <td id="item_seach" colspan="4">please wait...</td>
          </tr>
                        </thead>
                        <tbody id="Machineresult">
                           
                        </tbody>
                    </table>
                </div>
                <div class="cl"></div>
            </div>
        </div>
    </div>
</div>




<!-- Fuel Type  Dropdown -->
<div id="FuelType_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:60%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='FuelType_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Fuel Type List</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="FuelTypeTable" class="display nowrap table  table-striped table-bordered" style="width:100%;">
    <thead>
    <tr>
            <th style="width:10%;"></th>
            <th style="width:30%;">Code</th>
            <th style="width:60%;">Date</th>
    </tr>
    </thead>
    <tbody>
    <tr>
    <th style="text-align:center; width:10%;">&#10004;</th>
   <td style="width:30%;"> 
    <input type="text" id="FuelTypescodesearch" class="form-control" onkeyup="FuelTypeCodeFunction()">
    </td>
    <td style="width:60%;">
    <input type="text" id="FuelTypenamesearch" class="form-control" onkeyup="FuelTypeNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="FuelTypeTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          
        </thead>
        <tbody >   
        @if(!empty($objFuelType))
   
        @foreach ($objFuelType as $index=>$objFuelTyperow)
        <tr >
    
          
        <td style="text-align:center; width:10%"> <input type="checkbox" name="complaints[]" id="fueltypeidcode_{{ $index }}" class="clsspid_fueltype"  value="{{ $objFuelTyperow->FUELID }}" ></td>

          <td style="width:30%">{{ $objFuelTyperow->FUEL_CODE }}
          <input type="hidden" id="txtfueltypeidcode_{{ $index }}"
           data-code="{{ $objFuelTyperow->FUEL_CODE }}-{{ $objFuelTyperow->FUEL_DESC }}"
           data-desc="{{ $objFuelTyperow->FUEL_DESC }}"
                    
           value="{{ $objFuelTyperow->FUELID }}"/>
          </td>
          <td style="width:60%">{{ $objFuelTyperow-> FUEL_DESC }} </td>
        </tr>
        @endforeach  
        @else
        <td colspan="2">Record not found.</td>
        @endif
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!--  Fuel Type  Dropdown ends here -->


<!-- UOM Dropdown -->
<div id="UOM_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:60%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='UOM_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>UOM List</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="UOMTable" class="display nowrap table  table-striped table-bordered" style="width:100%;">
    <thead>
    <tr>
            <th style="width:10%;"></th>
            <th style="width:30%;">Code</th>
            <th style="width:60%;">Date</th>
    </tr>
    </thead>
    <tbody>
    <tr>
    <th style="text-align:center; width:10%;">&#10004;</th>
   <td style="width:30%;"> 
    <input type="text" id="UOMcodesearch" class="form-control" onkeyup="UOMCodeFunction()">
    </td>
    <td style="width:60%;">
    <input type="text" id="UOMnamesearch" class="form-control" onkeyup="UOMNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="UOMTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          
        </thead>
        <tbody >   
        @if(!empty($objUom))
   
        @foreach ($objUom as $index=>$objUomRow)
        <tr >
    
          
        <td style="text-align:center; width:10%"> <input type="checkbox" name="uom[]" id="uomidcode_{{ $index }}" class="clsspid_uom"  value="{{ $objUomRow->UOMID }}" ></td>

          <td style="width:30%">{{ $objUomRow->UOMCODE }}
          <input type="hidden" id="txtuomidcode_{{ $index }}"
           data-code="{{ $objUomRow->UOMCODE }}-{{ $objUomRow->DESCRIPTIONS }}"
         
           
           value="{{ $objUomRow->UOMID }}"/>
          </td>
          <td style="width:60%">{{ $objUomRow-> DESCRIPTIONS }} </td>
        </tr>
        @endforeach  
        @else
        <td colspan="2">Record not found.</td>
        @endif
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!--  UOM  Dropdown ends here -->




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




      //Machine Dropdown starts here 


      let machine = "#MachineTable2";
      let machine2 = "#MachineTable";
      let machineheaders = document.querySelectorAll(machine2 + " th");

      // Sort the table element when clicking on the table headers
      machineheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(machine, ".clsspid_reasoncode1", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function MachineCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Machinecodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("MachineTable2");
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

  function MachineNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Machinenamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("MachineTable2");
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


  $("#item_closePopup1").click(function(event){
        $("#machine_popup").hide();
      });

  function bindMachineEvents(){




      $(".clsspid_machine").click(function(){

        

        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");
        var texcode =   $("#txt"+fieldid+"").data("code");
        //alert(texdesc); 

        $('#txtMachinepopup').val(texcode);
        $('#MACHINE_REF').val(txtval);
        $('#MACHINE_DESC').val(texdesc);
        
        $("#machine_popup").hide();   
        $("#Reasoncode1codesearch").val(''); 
        $("#Reasoncode1namesearch").val('');    
        
        event.preventDefault();
      });
  }

  //Machine Dropdown Ends here







  $('#txtMachinepopup').on('click',function(event){  
    var MACHINE_TYPE=$('[name="COMPLAINT_FOR"]:checked').val();
  
                $("#Machineresult").html('');

                var trnFormReq  = $("#edit_trn_form");
                var formData    = trnFormReq.serialize();
                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  });
                  $("#item_seach").show();
                  $.ajax({
                      url:'{{route("transaction",[369,"get_genset_details"])}}',
                      type:'POST',
                      data:{'formData':formData},
                      success:function(data) {                                
                        $("#item_seach").hide();
                        $("#Machineresult").html(data);   
                        showSelectedCheck($("#MACHINE_REF").val(),"machine");
                        bindMachineEvents();  
                                       
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                        $("#Machineresult").html('');                        
                      },
                  }); 

                  showSelectedCheck($("#REASONCODE1_REF").val(),"machine");
                  $("#machine_popup").show();                    
         
    });




   //Fuel Type dropdown Function starts here 
   let fuel = "#FuelTypeTable2";
      let fuel2 = "#FuelTypeTable";
      let fuelheaders = document.querySelectorAll(fuel2 + " th");

      // Sort the table element when clicking on the table headers
      fuelheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(fuel, ".clsspid_fueltype", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function FuelTypeCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("FuelTypescodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("FuelTypeTable2");
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

  function FuelTypeNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("FuelTypenamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("FuelTypeTable2");
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

  $('#txtFuelType_popup').click(function(event){

    showSelectedCheck($("#FUELTYPE_REF").val(),"complaints");
         $("#FuelType_popup").show();
      });

      $("#FuelType_closePopup").click(function(event){
        $("#FuelType_popup").hide();
      });
      $(".clsspid_fueltype").click(function(){
        var fieldid         = $(this).attr('id');
        var txtval          =    $("#txt"+fieldid+"").val();
        var code         =   $("#txt"+fieldid+"").data("code");
        var desc            =   $("#txt"+fieldid+"").data("desc");
      

        $('#txtFuelType_popup').val(code);
        $('#FUELTYPE_REF').val(txtval);
        $("#FuelType_popup").hide();        
        $("#FuelTypescodesearch").val(''); 
        $("#FuelTypenamesearch").val('');    
        event.preventDefault();
      });


 //UOM Dropdown Function starts here 
 let uom = "#UOMTable2";
      let uom2 = "#UOMTable";
      let uomheaders = document.querySelectorAll(uom2 + " th");

      // Sort the table element when clicking on the table headers
      uomheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(uom, ".clsspid_uom", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function UOMCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("UOMcodesearch");
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

  function UOMNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("UOMnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("UOMTable2");
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

  $('#txtUOM_popup').click(function(event){

    showSelectedCheck($("#UOMID_REF").val(),"uom");
         $("#UOM_popup").show();
      });

      $("#UOM_closePopup").click(function(event){
        $("#UOM_popup").hide();
      });
      $(".clsspid_uom").click(function(){
        var fieldid         = $(this).attr('id');
        var txtval          =    $("#txt"+fieldid+"").val();
        var code         =   $("#txt"+fieldid+"").data("code");
        $('#txtUOM_popup').val(code);
        $('#UOMID_REF').val(txtval);
        $("#UOM_popup").hide();        
        $("#UOMcodesearch").val(''); 
        $("#UOMnamesearch").val('');    
        event.preventDefault();
      });










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

$('#FILLED_FUEL').on('change',function(){
 

 var OPENDING_FUEL=parseInt($("#OPENDING_FUEL").val()); 
 
if($("#OPENDING_FUEL").val()!=''){

   var CLOSING=parseInt($(this).val()) + OPENDING_FUEL;
 
 if(intRegex.test(CLOSING)){
       $("#CLOSING_FUEL").val(CLOSING+'.00');
       }else{
       $("#CLOSING_FUEL").val(CLOSING);
       } 
       if(intRegex.test($(this).val())){
          $(this).val($(this).val()+'.00')
         }
}
     event.preventDefault();

 

     });

$('#OPENDING_FUEL').on('change',function(){
 

 var FILLED_FUEL=parseInt($("#FILLED_FUEL").val()); 

if($("#FILLED_FUEL").val()!=''){
   var CLOSING=parseInt($(this).val()) + FILLED_FUEL;
 
 if(intRegex.test(CLOSING)){
       $("#CLOSING_FUEL").val(CLOSING+'.00');
       }else{
       $("#CLOSING_FUEL").val(CLOSING);
       } 
       if(intRegex.test($(this).val())){
          $(this).val($(this).val()+'.00')
         }
 
}
     event.preventDefault();

 

     });




    $('#edit_trn_form').on('blur','#CONSUMPTION_PER_HOUR',function(){
            if(intRegex.test($(this).val())){
             $(this).val($(this).val()+'.00')
            }
            event.preventDefault();
        });

    $('#edit_trn_form').on('blur','#OPENDING_FUEL',function(){
            if(intRegex.test($(this).val())){
             $(this).val($(this).val()+'.00')
            }
            event.preventDefault();
        });



    $('#edit_trn_form').on('blur','#FILLED_FUEL',function(){
            if(intRegex.test($(this).val())){
             $(this).val($(this).val()+'.00')
            }
            event.preventDefault();
        });

    $('#edit_trn_form').on('blur','#KWH_START_USAGE',function(){
            if(intRegex.test($(this).val())){
             $(this).val($(this).val()+'.00')
            }
            event.preventDefault();
        });

    $('#edit_trn_form').on('blur','#KWH_END_USAGE',function(){
            if(intRegex.test($(this).val())){
             $(this).val($(this).val()+'.00')
            }
            event.preventDefault();
        });

    $('#edit_trn_form').on('blur','#KWH_START_CONSUMPTION',function(){
            if(intRegex.test($(this).val())){
             $(this).val($(this).val()+'.00')
            }
            event.preventDefault();
        });

    $('#edit_trn_form').on('blur','#KWH_END_CONSUMPTION',function(){
            if(intRegex.test($(this).val())){
             $(this).val($(this).val()+'.00')
            }
            event.preventDefault();
        });


/*================================== ONLOAD FUNCTION ==================================*/




  // var lastdt = <?php echo json_encode($objResponse->DGUFF_DATE); ?>;
  // var today = new Date(); 
  // var currentdate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
  // $('#DGUFF_DATE').attr('min',lastdt);
  // $('#DGUFF_DATE').attr('max',currentdate);

  var lastdt = <?php echo json_encode($objResponse->DGUFF_DATE); ?>;
  var dguff = <?php echo json_encode($objResponse); ?>;
  var today = new Date(); 
  var sodate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2);
  if(lastdt < dguff.DGUFF_DATE)
  {
	$('#DGUFF_DATE').attr('min',lastdt);
  }
  else
  {
	  $('#DGUFF_DATE').attr('min',dguff.DGUFF_DATE);
  }
  $('#DGUFF_DATE').attr('max',sodate);
  


  var d = new Date(); 
  var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
 // $('#EMC_DATE').val(today);




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







/*================================== TNC HEADER =================================*/
  
</script>

@endpush

@push('bottom-scripts')
<script>
var formTrans = $("#edit_trn_form");
formTrans.validate();

$( "#btnSaveFormData" ).click(function() {
  if(formTrans.valid()){
    validateForm("fnSaveData");
  }
});


$( "#btnApprove" ).click(function() {
 
  if(formTrans.valid()){
    validateForm("fnApproveData");
  }
});

function validateForm(saveAction){
  
  $("#FocusId").val('');

  var DGUFF_NO              = $.trim($("#DGUFF_NO").val());
  var DGUFF_DATE            = $.trim($("#DGUFF_DATE").val());
  var MACHINE_REF           = $.trim($("#MACHINE_REF").val());
  var FUELTYPE_REF          = $.trim($("#FUELTYPE_REF").val());
  var CONSUMPTION_PER_HOUR  = $.trim($("#CONSUMPTION_PER_HOUR").val());
  var UOMID_REF             = $.trim($("#UOMID_REF").val());

  var type                  = $('[name="TYPE"]:checked').val();


  var FROMDATE    = $.trim($("#FROMDATE").val());
  var FROMTIME    = $.trim($("#FROMTIME").val());
  var TODATE    = $.trim($("#TODATE").val());
  var TOTIME    = $.trim($("#TOTIME").val());
  var GENSET_STARTED_BY    = $.trim($("#GENSET_STARTED_BY").val());
  var GENSET_STOPPED_BY    = $.trim($("#GENSET_STOPPED_BY").val());
  var KWH_START_USAGE    = $.trim($("#KWH_START_USAGE").val());
  var KWH_END_USAGE    = $.trim($("#KWH_END_USAGE").val());


 var OPENDING_FUEL    = $.trim($("#OPENDING_FUEL").val());
  var FILLED_FUEL    = $.trim($("#FILLED_FUEL").val());
  var KWH_START_CONSUMPTION    = $.trim($("#KWH_START_CONSUMPTION").val());
  var KWH_END_CONSUMPTION    = $.trim($("#OPENDING_FUEL").val());


  
  if(DGUFF_NO ===""){
      $("#FocusId").val('DGUFF_NO');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Doc No is required.');
      $("#alert").modal('show')
      $("#OkBtn1").focus();
      return false;
  }
  else if(DGUFF_DATE ===""){
      $("#FocusId").val('DGUFF_DATE');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please select Date.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  }
  else if(MACHINE_REF ===""){
      $("#FocusId").val('Machinepopup');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please select Genset Code.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  }
  else if(FUELTYPE_REF ===""){
      $("#FocusId").val('txtFuelType_popup');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please Select Meter Code No.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(CONSUMPTION_PER_HOUR ===""){
      $("#FocusId").val('CONSUMPTION_PER_HOUR');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please Enter Consumption Per Hour.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(UOMID_REF ===""){
      $("#FocusId").val('txtUOM_popup');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please select UOM.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(type ===""){
      $("#FocusId").val('USAGE');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please select consumption type.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 

  else if(type ==="usage" && FROMDATE===""){
      $("#FocusId").val('FROMDATE');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please Select From Date.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(type ==="usage" && FROMTIME===""){
      $("#FocusId").val('FROMTIME');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please Select From Time.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(type ==="usage" && TODATE===""){
      $("#FocusId").val('TODATE');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please Select To Date.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(type ==="usage" && TOTIME===""){
      $("#FocusId").val('TOTIME');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please Select To Time.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(type ==="usage" && GENSET_STARTED_BY===""){
      $("#FocusId").val('GENSET_STARTED_BY');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please enter Genset Started By.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(type ==="usage" && GENSET_STOPPED_BY===""){
      $("#FocusId").val('GENSET_STOPPED_BY');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please enter Genset Stopped By.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(type ==="usage" && KWH_START_USAGE===""){
      $("#FocusId").val('KWH_START_USAGE');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please enter Meter (KWH) Start reading.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(type ==="usage" && KWH_END_USAGE===""){
      $("#FocusId").val('KWH_END_USAGE');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please enter Meter (KWH) End Reading.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 

  else if(type ==="fuel_consumption" && OPENDING_FUEL===""){
      $("#FocusId").val('OPENDING_FUEL');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please enter Opening Fuel.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(type ==="fuel_consumption" && FILLED_FUEL===""){
      $("#FocusId").val('FILLED_FUEL');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please enter Filled Fuel.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(type ==="fuel_consumption" && KWH_START_CONSUMPTION===""){
      $("#FocusId").val('KWH_START_CONSUMPTION');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please enter Meter (KWH) End Reading.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(type ==="fuel_consumption" && KWH_END_CONSUMPTION===""){
      $("#FocusId").val('KWH_END_CONSUMPTION');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please enter Meter (KWH) End Reading.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 


  /*=================================================IN BOTH VALIDATION========================================*/

  else if(type ==="both" && FROMDATE===""){
      $("#FocusId").val('FROMDATE');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please Select From Date.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(type ==="both" && FROMTIME===""){
      $("#FocusId").val('FROMTIME');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please Select From Time.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(type ==="both" && TODATE===""){
      $("#FocusId").val('TODATE');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please Select To Date.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(type ==="both" && TOTIME===""){
      $("#FocusId").val('TOTIME');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please Select To Time.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(type ==="both" && GENSET_STARTED_BY===""){
      $("#FocusId").val('GENSET_STARTED_BY');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please enter Genset Started By.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(type ==="both" && GENSET_STOPPED_BY===""){
      $("#FocusId").val('GENSET_STOPPED_BY');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please enter Genset Stopped By.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(type ==="both" && KWH_START_USAGE===""){
      $("#FocusId").val('KWH_START_USAGE');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please enter Meter (KWH) Start reading.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(type ==="both" && KWH_END_USAGE===""){
      $("#FocusId").val('KWH_END_USAGE');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please enter Meter (KWH) End Reading.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 

  else if(type ==="both" && OPENDING_FUEL===""){
      $("#FocusId").val('OPENDING_FUEL');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please enter Opening Fuel.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(type ==="both" && FILLED_FUEL===""){
      $("#FocusId").val('FILLED_FUEL');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please enter Filled Fuel.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(type ==="both" && KWH_START_CONSUMPTION===""){
      $("#FocusId").val('KWH_START_CONSUMPTION');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please enter Meter (KWH) End Reading.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(type ==="both" && KWH_END_CONSUMPTION===""){
      $("#FocusId").val('KWH_END_CONSUMPTION');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please enter Meter (KWH) End Reading.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(checkPeriodClosing('{{$FormId}}',$("#DGUFF_DATE").val(),0) ==0){
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
                $("#YesBtn").data("funcname",saveAction);
                $("#OkBtn1").hide();
                $("#OkBtn").hide();
                $("#YesBtn").show();
                $("#NoBtn").show();
                $("#YesBtn").focus();
                highlighFocusBtn('activeYes');
                }

}
  


$("#YesBtn").click(function(){

$("#alert").modal('hide');
var customFnName = $("#YesBtn").data("funcname");
    window[customFnName]();

});

window.fnSaveData = function (){

    event.preventDefault();
    var trnsoForm = $("#edit_trn_form");
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
        url:'{{ route("transaction",[$FormId,"update"])}}',
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

window.fnApproveData = function (){

    event.preventDefault();
    var trnsoForm = $("#edit_trn_form");
    var formData = trnsoForm.serialize();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $("#btnApprove").hide(); 
    $(".buttonload_approve").show();  
    $("#btnSaveFormData").prop("disabled", true);
    $.ajax({
        url:'{{ route("transaction",[$FormId,"Approve"])}}',
        type:'POST',
        data:formData,
        success:function(data) {
          $("#btnApprove").show();  
          $(".buttonload_approve").hide();  
          $("#btnSaveFormData").prop("disabled", false);
          
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
            $("#btnApprove").show();  
            $(".buttonload_approve").hide();  
            $("#btnSaveFormData").prop("disabled", false);
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
    $("#JWONO").focus();
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






$('#edit_trn_form').on('click','#TODATE',function(e){    
  if($('#FROMDATE').val()=='' )
{
      $("#FocusId").val('FROMDATE');
      $("#TODATE").val('');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please Select From Date First.');
      $("#alert").modal('show')
      $("#OkBtn1").focus();
      return false;
}

});
$('#edit_trn_form').on('change','#TODATE',function(e){    
  if($(this).val() <$('#FROMDATE').val() && $('#FROMDATE').val()!='' )
{
      $("#FocusId").val('TODATE');
      $("#TODATE").val('');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('To Date cannot be less than From Date.');
      $("#alert").modal('show')
      $("#OkBtn1").focus();
      return false;
}

});


$('#edit_trn_form').on('change','#FROMDATE',function(e){    
  if($(this).val() >$('#TODATE').val() && $('#TODATE').val()!='' )
{
      $("#FocusId").val('FROMDATE');
      $("#FROMDATE").val('');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('From Date cannot be greater than To Date.');
      $("#alert").modal('show')
      $("#OkBtn1").focus();
      return false;
}

});






$('#USAGE').on('change',function()
{
  if($(this).is(':checked') == true)
  {
    $('#FUEL_CONSUMPTION').prop('checked', false);
    $('#BOTH').prop('checked', false);

    $("#OPENDING_FUEL").prop('disabled', true);  
    $("#FILLED_FUEL").prop('disabled', true);  
    $("#CLOSING_FUEL").prop('disabled', true);  
    $("#FUEL_FILLED_BY").prop('disabled', true);  
    $("#CONSUMPTION_REMARKS").prop('disabled', true);  
    $("#KWH_START_CONSUMPTION").prop('disabled', true);  
    $("#KWH_END_CONSUMPTION").prop('disabled', true);  
    $("#OBSERVATION").prop('disabled', true);  

    $("#OPENDING_FUEL").val('');  
    $("#FILLED_FUEL").val('');  
    $("#CLOSING_FUEL").val('');  
    $("#FUEL_FILLED_BY").val('');  
    $("#CONSUMPTION_REMARKS").val('');  
    $("#KWH_START_CONSUMPTION").val('');  
    $("#KWH_END_CONSUMPTION").val('');  
    $("#OBSERVATION").val('');  

    $("#FROMDATE").prop('disabled', false);  
    $("#FROMTIME").prop('disabled', false);  
    $("#TODATE").prop('disabled', false);  
    $("#TOTIME").prop('disabled', false);  
    $("#GENSET_STARTED_BY").prop('disabled', false);  
    $("#GENSET_STOPPED_BY").prop('disabled', false);  
    $("#KWH_START_USAGE").prop('disabled', false);  
    $("#KWH_END_USAGE").prop('disabled', false);  


  }
  else
  {
    $(this).prop('checked', false);  
    $('#FUEL_CONSUMPTION').prop('checked', false);
    $('#BOTH').prop('checked', false);
  }
});

  $('#FUEL_CONSUMPTION').on('change',function()
{
  if($(this).is(':checked') == true)
  {
    $('#USAGE').prop('checked', false);
    $('#BOTH').prop('checked', false);

  
    $("#OPENDING_FUEL").prop('disabled', false);  
    $("#FILLED_FUEL").prop('disabled', false);  
    $("#CLOSING_FUEL").prop('disabled', false);  
    $("#FUEL_FILLED_BY").prop('disabled', false);  
    $("#CONSUMPTION_REMARKS").prop('disabled', false);  
    $("#KWH_START_CONSUMPTION").prop('disabled', false);  
    $("#KWH_END_CONSUMPTION").prop('disabled', false);  
    $("#OBSERVATION").prop('disabled', false);  

    $("#FROMDATE").val('');  
    $("#FROMTIME").val('');  
    $("#TODATE").val('');  
    $("#TOTIME").val('');  
    $("#GENSET_STARTED_BY").val('');  
    $("#GENSET_STOPPED_BY").val('');  
    $("#KWH_START_USAGE").val('');  
    $("#KWH_END_USAGE").val('');  

    $("#FROMDATE").prop('disabled', true);  
    $("#FROMTIME").prop('disabled', true);  
    $("#TODATE").prop('disabled', true);  
    $("#TOTIME").prop('disabled', true);  
    $("#GENSET_STARTED_BY").prop('disabled', true);  
    $("#GENSET_STOPPED_BY").prop('disabled', true);  
    $("#KWH_START_USAGE").prop('disabled', true);  
    $("#KWH_END_USAGE").prop('disabled', true);  

   // $("#MACHINE_REF").val('');
  }
  else
  {
    $(this).prop('checked', false);
    $('#BOTH').prop('checked', false);
    $('#USAGE').prop('checked', false);
  
  }
});

$('#BOTH').on('change',function()
{
  if($(this).is(':checked') == true)
  {
   $('#USAGE').prop('checked', false);
    $('#FUEL_CONSUMPTION').prop('checked', false);
 
  
    $("#OPENDING_FUEL").prop('disabled', false);  
    $("#FILLED_FUEL").prop('disabled', false);  
    $("#CLOSING_FUEL").prop('disabled', false);  
    $("#FUEL_FILLED_BY").prop('disabled', false);  
    $("#CONSUMPTION_REMARKS").prop('disabled', false);  
    $("#KWH_START_CONSUMPTION").prop('disabled', false);  
    $("#KWH_END_CONSUMPTION").prop('disabled', false);  
    $("#OBSERVATION").prop('disabled', false);  

    $("#FROMDATE").prop('disabled', false);  
    $("#FROMTIME").prop('disabled', false);  
    $("#TODATE").prop('disabled', false);  
    $("#TOTIME").prop('disabled', false);  
    $("#GENSET_STARTED_BY").prop('disabled', false);  
    $("#GENSET_STOPPED_BY").prop('disabled', false);  
    $("#KWH_START_USAGE").prop('disabled', false);  
    $("#KWH_END_USAGE").prop('disabled', false);  
  }
  else
  {
    $(this).prop('checked', false);  
    $('#FUEL_CONSUMPTION').prop('checked', false);
    $('#USAGE').prop('checked', false);
  }
});






$(document).ready(function(){
var type=$('[name="TYPE"]:checked').val();
if(type==='usage'){

  $('#FUEL_CONSUMPTION').prop('checked', false);
    $('#BOTH').prop('checked', false);

    $("#OPENDING_FUEL").prop('disabled', true);  
    $("#FILLED_FUEL").prop('disabled', true);  
    $("#CLOSING_FUEL").prop('disabled', true);  
    $("#FUEL_FILLED_BY").prop('disabled', true);  
    $("#CONSUMPTION_REMARKS").prop('disabled', true);  
    $("#KWH_START_CONSUMPTION").prop('disabled', true);  
    $("#KWH_END_CONSUMPTION").prop('disabled', true);  
    $("#OBSERVATION").prop('disabled', true);  

    $("#OPENDING_FUEL").val('');  
    $("#FILLED_FUEL").val('');  
    $("#CLOSING_FUEL").val('');  
    $("#FUEL_FILLED_BY").val('');  
    $("#CONSUMPTION_REMARKS").val('');  
    $("#KWH_START_CONSUMPTION").val('');  
    $("#KWH_END_CONSUMPTION").val('');  
    $("#OBSERVATION").val('');  

    $("#FROMDATE").prop('disabled', false);  
    $("#FROMTIME").prop('disabled', false);  
    $("#TODATE").prop('disabled', false);  
    $("#TOTIME").prop('disabled', false);  
    $("#GENSET_STARTED_BY").prop('disabled', false);  
    $("#GENSET_STOPPED_BY").prop('disabled', false);  
    $("#KWH_START_USAGE").prop('disabled', false);  
    $("#KWH_END_USAGE").prop('disabled', false);
  }
  else if(type==='fuel_consumption'){
    $('#USAGE').prop('checked', false);
    $('#BOTH').prop('checked', false);

  
    $("#OPENDING_FUEL").prop('disabled', false);  
    $("#FILLED_FUEL").prop('disabled', false);  
    $("#CLOSING_FUEL").prop('disabled', false);  
    $("#FUEL_FILLED_BY").prop('disabled', false);  
    $("#CONSUMPTION_REMARKS").prop('disabled', false);  
    $("#KWH_START_CONSUMPTION").prop('disabled', false);  
    $("#KWH_END_CONSUMPTION").prop('disabled', false);  
    $("#OBSERVATION").prop('disabled', false);  

    $("#FROMDATE").val('');  
    $("#FROMTIME").val('');  
    $("#TODATE").val('');  
    $("#TOTIME").val('');  
    $("#GENSET_STARTED_BY").val('');  
    $("#GENSET_STOPPED_BY").val('');  
    $("#KWH_START_USAGE").val('');  
    $("#KWH_END_USAGE").val('');  

    $("#FROMDATE").prop('disabled', true);  
    $("#FROMTIME").prop('disabled', true);  
    $("#TODATE").prop('disabled', true);  
    $("#TOTIME").prop('disabled', true);  
    $("#GENSET_STARTED_BY").prop('disabled', true);  
    $("#GENSET_STOPPED_BY").prop('disabled', true);  
    $("#KWH_START_USAGE").prop('disabled', true);  
    $("#KWH_END_USAGE").prop('disabled', true);  


  }  else if(type==='both'){
    $('#USAGE').prop('checked', false);
    $('#FUEL_CONSUMPTION').prop('checked', false);
 
  
    $("#OPENDING_FUEL").prop('disabled', false);  
    $("#FILLED_FUEL").prop('disabled', false);  
    $("#CLOSING_FUEL").prop('disabled', false);  
    $("#FUEL_FILLED_BY").prop('disabled', false);  
    $("#CONSUMPTION_REMARKS").prop('disabled', false);  
    $("#KWH_START_CONSUMPTION").prop('disabled', false);  
    $("#KWH_END_CONSUMPTION").prop('disabled', false);  
    $("#OBSERVATION").prop('disabled', false);  

    $("#FROMDATE").prop('disabled', false);  
    $("#FROMTIME").prop('disabled', false);  
    $("#TODATE").prop('disabled', false);  
    $("#TOTIME").prop('disabled', false);  
    $("#GENSET_STARTED_BY").prop('disabled', false);  
    $("#GENSET_STOPPED_BY").prop('disabled', false);  
    $("#KWH_START_USAGE").prop('disabled', false);  
    $("#KWH_END_USAGE").prop('disabled', false);  


  }




});



//$(document).ready(function(e) {

  //var today         =   new Date(); 
  //var currentdate   =   today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
//   var currentdate   =   <?php //echo json_encode($objMstResponse->JWODT); ?>;

//   $('[id*="EDA_"]').attr('min',currentdate);
//   $('[id*="EDA_"]').val(currentdate);

// });

</script>


@endpush