@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[93,'index'])}}" class="btn singlebt">General Ledger Master</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
                        <button class="btn topnavbt" id="btnSaveSE" disabled="disabled"><i class="fa fa-save"></i> Save</button>
                        <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt" id="btnUndo"  disabled="disabled"><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-lock"></i> Approved</button>
                        <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                        <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                </div><!--col-10-->

            </div><!--row-->
    </div><!--topnav-->	
   
    <div class="container-fluid purchase-order-view filter">     
          <div class="inner-form">
          
                <div class="row">
                  <div class="col-lg-1 pl"><p>GL Code</p></div>
                  <div class="col-lg-2 pl">
                    <label> {{$objResponse->GLCODE}} </label>
                  </div>
               
                  <div class="col-lg-2 pl"><p>Name</p></div>
                  <div class="col-lg-4 pl">
                    <label> {{$objResponse->GLNAME}} </label>
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-1 pl"><p>Alias</p></div>
                  <div class="col-lg-2 pl">
                    <label> {{$objResponse->ALIAS}} </label>
                  </div>
               
                  <div class="col-lg-2 pl"><p>Account Sub Group</p></div>
                  <div class="col-lg-2 pl">
                  @foreach($objAccountSubGroupList as $AsgList)
                    <label> {{ $objResponse->ASGID_REF==$AsgList->ASGID?$AsgList->ASGCODE.' - '.$AsgList->ASGNAME:''}}</label>
                    @endforeach
                  </div>
                </div>

                <div class="row">
                <div class="col-lg-2 pl"><p>De-Activated</p></div>
                <div class="col-lg-1 pl">
                <label> {{$objResponse->DEACTIVATED == 1 ? "Yes" : ""}} </label>
                
                </div>
                
                <div class="col-lg-2 pl"><p>Date of De-Activated</p></div>
                <div class="col-lg-2 pl">
                  <label> {{ (is_null($objResponse->DODEACTIVATED) || $objResponse->DODEACTIVATED=='1900-01-01' )?'':
                  \Carbon\Carbon::parse($objResponse->DODEACTIVATED)->format('d/m/Y')   }} </label>
                </div>
              </div>

                <div class="row">
                  <br/>
                </div>
                
                <div class="row">
                  <div class="col-lg-3 pl"><p>Checks Flag</p></div>
                </div>
                
                <div class="row">
                  <div class="col-lg-2 pl "><p>Cost Centre Applicable</p></div>
                  <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
                    <label>             
                      @if($objResponse->CC == "1")
                          Yes
                      @elseif($objResponse->CC == "0")
                          No
                      @else
                          
                      @endif
                    </label>
                    </div>
                  </div>	
                  
                  <div class="col-lg-2 pl col-md-offset-2"><p>Sub Ledger</p></div>
                  <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
                    
                    <label>             
                      @if($objResponse->SUBLEDGER == "1")
                          Yes
                      @elseif($objResponse->SUBLEDGER == "0")
                          No
                      @else
                          
                      @endif
                    </label>
                    </div>
                  </div>	
                </div>
                
                <div class="row">
                  <div class="col-lg-2 pl"><p>Bank Account</p></div>
                  <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
                    
                    <label>             
                      @if($objResponse->BANKAC == "1")
                          Yes
                      @elseif($objResponse->BANKAC == "0")
                          No
                      @else
                          
                      @endif
                    </label>
                    </div>
                  </div>	
                  
                  <div class="col-lg-2 pl col-md-offset-2"><p>Belong to GST</p></div>
                  <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
              
                    <label>             
                      @if($objResponse->GST == "1")
                          Yes
                      @elseif($objResponse->GST == "0")
                          No
                      @else
                          
                      @endif
                    </label>
                    </div>
                  </div>	
                </div>
                
                <div class="row">
                  <div class="col-lg-2 pl"><p>GST Calculate on this GL</p></div>
                  <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
                   
                    <label>             
                      @if($objResponse->GST_ON_THISGL == "1")
                          Yes
                      @elseif($objResponse->GST_ON_THISGL == "0")
                          No
                      @else
                          
                      @endif
                    </label>
                    </div>
                  </div>	
                  
                  <div class="col-lg-2 pl col-md-offset-2"><p>Belong to TDS</p></div>
                  <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
                   
                    <label>             
                      @if($objResponse->TDS == "1")
                          Yes
                      @elseif($objResponse->TDS == "0")
                          No
                      @else
                          
                      @endif
                    </label>
                    </div>
                  </div>	
                </div>
                
                <div class="row">
                  <div class="col-lg-2 pl"><p>Inventory Values are affected</p></div>
                  <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
                   
                    <label>             
                      @if($objResponse->IVAFFECTED == "1")
                          Yes
                      @elseif($objResponse->IVAFFECTED == "0")
                          No
                      @else
                          
                      @endif
                    </label>
                    </div>
                  </div>	
                  
                  <div class="col-lg-2 pl col-md-offset-2"><p>Interest Calculation</p></div>
                  <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
                   
                    <label>             
                      @if($objResponse->ICALCULATION == "1")
                          Yes
                      @elseif($objResponse->ICALCULATION == "0")
                          No
                      @else
                          
                      @endif
                    </label>
                    </div>
                  </div>	
                </div>
                
                <div class="row">
                  <div class="col-lg-2 pl"><p>Use for Payroll</p></div>
                  <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
                   
                    <label>             
                      @if($objResponse->UPAYROLL == "1")
                          Yes
                      @elseif($objResponse->UPAYROLL == "0")
                          No
                      @else
                          
                      @endif
                    </label>
                    </div>
                  </div>	
                  
                  <div class="col-lg-2 pl col-md-offset-2"><p>Belong to VAT </p></div>
                  <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
                    
                    <label>             
                      @if($objResponse->VAT == "1")
                          Yes
                      @elseif($objResponse->VAT == "0")
                          No
                      @else
                          
                      @endif
                    </label>
                    </div>
                  </div>	
                </div>
                
                <div class="row">
                  <div class="col-lg-2 pl "><p>Belong to Service Tax</p></div>
                  <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
                   
                    <label>             
                      @if($objResponse->TAX == "1")
                          Yes
                      @elseif($objResponse->TAX == "0")
                          No
                      @else
                          
                      @endif
                    </label>
                    </div>
                  </div>	
                  
                  <div class="col-lg-2 pl col-md-offset-2"><p>Belong to Sale (Revenue)</p></div>
                  <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
                   
                    <label>             
                      @if($objResponse->SALE == "1")
                          Yes
                      @elseif($objResponse->SALE == "0")
                          No
                      @else
                          
                      @endif
                    </label>
                    </div>
                  </div>	
                </div>
                
                <div class="row">
                  <div class="col-lg-2 pl "><p>Belong to Purchase</p></div>
                  <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
        
                   
                    <label>             
                      @if($objResponse->PURCHASE == "1")
                          Yes
                      @elseif($objResponse->PURCHASE == "0")
                          No
                      @else
                          
                      @endif
                    </label>
                    </div>
                  </div>	
                  
                  <div class="col-lg-2 pl col-md-offset-2"><p>Belong to TCS</p></div>
                  <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
                    
                    <label>             
                      @if($objResponse->TCS == "1")
                          Yes
                      @elseif($objResponse->TCS == "0")
                          No
                      @else
                          
                      @endif
                    </label>
                    </div>
                  </div>	
                </div>



                
              





          

          </div>

    </div><!--purchase-order-view-->

    <script>
     $('#btnAdd').on('click', function() {
      var viewURL = '{{route("master",[93,"add"])}}';
      window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });
    </script>

@endsection