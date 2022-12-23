@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[147,'index'])}}" class="btn singlebt">Branch Master</a>
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

    <div class="col-lg-1 pl"><p>Company</p></div>
    <div class="col-lg-2 pl">
        @foreach ($objCompanyList as $key=>$val)
        <label>{{isset($objResponse->CYID_REF) && $objResponse->CYID_REF == $val-> CYID ?$val->CYCODE.' - '.$val->NAME:''}}</label>
        @endforeach
    </div>



    <div class="col-lg-1 pl"><p>Branch Code</p></div>
    <div class="col-lg-2 pl">
      <label> {{$objResponse->BRCODE}} </label>
  </div>


    <div class="col-lg-1 pl"><p>Branch Name</p></div>
    <div class="col-lg-2 pl">
    <label> {{$objResponse->BRNAME}} </label>
    
    </div>


      <div class="col-lg-1 pl"><p>Branch Group</p></div>
    <div class="col-lg-2 pl">
        @foreach ($objBranchGroupList as $key=>$val)
        <label>{{isset($objResponse->BGID_REF) && $objResponse->BGID_REF == $val-> BGID ?$val->BG_CODE.' - '.$val->BG_DESC:''}}</label>

        @endforeach

    </div>

  </div>


<div class="row">


  <div class="col-lg-1 pl"><p>GSTIN No</p></div>
  <div class="col-lg-2 pl"> 
  <label> {{$objResponse->GSTINNO}} </label>
    
  </div>
  
  <div class="col-lg-1 pl"><p>CIN No</p></div>
  <div class="col-lg-2 pl">
  <label> {{$objResponse->CINNO}} </label>
   
  </div>
  
  <div class="col-lg-1 pl"><p>Branch Address Line 1</p></div>
  <div class="col-lg-2 pl">
  <label> {{$objResponse->ADDL1}} </label>

  
  </div>
  
  <div class="col-lg-1 pl "><p>Branch Address Line 2</p></div>
  <div class="col-lg-2 pl">
  <label> {{$objResponse->ADDL2}} </label>

    
  </div>

</div>

<div class="row">
  
  <div class="col-lg-1 pl"><p>Country</p></div>
  <div class="col-lg-2 pl">
  <label> {{isset($objRegCountryName->CTRYCODE)?$objRegCountryName->CTRYCODE. ' - ':''}}  {{isset($objRegCountryName->NAME)?$objRegCountryName->NAME:''}} </label>

  </div>
  
  <div class="col-lg-1 pl"><p>State</p></div>
  <div class="col-lg-2 pl">
  <label>{{isset($objRegStateName->STCODE)?$objRegStateName->STCODE. ' - ':''}}  {{isset($objRegStateName->NAME)?$objRegStateName->NAME:''}}</label>

  </div>
  
  <div class="col-lg-1 pl"><p>City</p></div>
  <div class="col-lg-2 pl">
  <label> {{isset($objRegCityName->CITYCODE)?$objRegCityName->CITYCODE. ' - ':''}}  {{isset($objRegCityName->NAME)?$objRegCityName->NAME:''}} </label>

  </div>


  <div class="col-lg-1 pl"><p>Pincode</p></div>
    <div class="col-lg-2 pl">
    <label> {{$objResponse->PINCODE}} </label>
      
    </div>
  
 

</div>


<div class="row">
<div class="col-lg-1 pl"><p>Landmark</p></div>
  <div class="col-lg-2 pl">
  <label> {{$objResponse->BRLM}} </label>

  </div>


<div class="col-lg-1 pl"><p>Email ID</p></div>
<div class="col-lg-2 pl">
<label> {{$objResponse->EMAILID}} </label>

</div>

<div class="col-lg-1 pl"><p>Phone No</p></div>
<div class="col-lg-2 pl">
<label> {{$objResponse->PHNO}} </label>

</div>

<div class="col-lg-1 pl"><p>Mobile No</p></div>
<div class="col-lg-2 pl">
<label> {{$objResponse->MONO}} </label>

</div>


</div>

<div class="row">

<div class="col-lg-1 pl"><p>Website</p></div>
<div class="col-lg-2 pl">
<label> {{$objResponse->WEBSITE}} </label>

</div>		

<div class="col-lg-1 pl"><p>Skype</p></div>
<div class="col-lg-2 pl">
<label> {{$objResponse->SKYPEID}} </label>

</div>

<div class="col-lg-1 pl"><p>Authorised Person Name</p></div>
<div class="col-lg-2 pl">
<label> {{$objResponse->AUTHPNAME}} </label>

</div>

<div class="col-lg-1 pl"><p>Designation</p></div>
<div class="col-lg-2 pl">
<label> {{$objResponse->AUTHPDESG}} </label>

</div>
</div>

<div class="row">

<div class="col-lg-1 pl"><p>Industry Type</p></div>
<div class="col-lg-2 pl ">
<label> {{isset($objIndtypeName->INDSCODE)?$objIndtypeName->INDSCODE. ' - ':''}}  {{isset($objIndtypeName->DESCRIPTIONS)?$objIndtypeName->DESCRIPTIONS:''}}</label>

</div>

<div class="col-lg-1 pl"><p>Industry Vertical</p></div>
<div class="col-lg-2 pl">
<label> {{isset($objIndVerName->INDSVCODE)?$objIndVerName->INDSVCODE. ' - ':''}}  {{isset($objIndVerName->DESCRIPTIONS)?$objIndVerName->DESCRIPTIONS:''}}</label>

</div>



<div class="col-lg-1 pl"><p>Deals In</p></div>
<div class="col-lg-2 pl">
<label> {{$objResponse->DEALSIN}} </label>

</div>

<div class="col-lg-1 pl"><p>GST Type</p></div>
<div class="col-lg-2 pl">


  @foreach ($objGstTypeList as $index=>$GstType)
  <label>{{isset($objResponse->GSTTYPE) && $objResponse->GSTTYPE == $GstType-> GSTID ?$GstType->GSTCODE.' - '.$GstType->DESCRIPTIONS:''}}</label>

  @endforeach

</div>
</div>

<div class="row">

<div class="col-lg-1 pl"><p>MSME No</p></div>
<div class="col-lg-2 pl">
<label> {{$objResponse->MSME_NO}} </label>

</div>

<div class="col-lg-1 pl"><p>Factory ACT No</p></div>
<div class="col-lg-2 pl">
<label> {{$objResponse->FACTORY_ACT_NO}} </label>

</div>

                <div class="col-lg-1 pl"><p>De-Activated</p></div>
                <div class="col-lg-2 pl">
                <label> {{$objResponse->DEACTIVATED == 1 ? "Yes" : ""}} </label>
                
                </div>
                
                <div class="col-lg-1 pl"><p>Date of De-Activated</p></div>
                <div class="col-lg-2 pl">
                  <label> {{ (is_null($objResponse->DODEACTIVATED) || $objResponse->DODEACTIVATED=='1900-01-01' )?'':
                  \Carbon\Carbon::parse($objResponse->DODEACTIVATED)->format('d/m/Y')   }} </label>
                </div>
          </div>




             <div class="row">
<ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#tab0">Bank</a></li>
    <li class=""><a data-toggle="tab" href="#ALPSSpecific">ALPS Specific</a></li>
    <li class=""><a data-toggle="tab" href="#tab1">UDF</a></li>
	<!--<li class=""><a data-toggle="tab" href="#tab2">Logo</a></li>-->
</ul>



<div class="tab-content">



<div id="tab0" class="tab-pane fade in active">

<div class=" table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:300px;" >
    <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
      <thead id="thead1"  style="position: sticky;top: 0">
        <tr>
        <th>
            Bank Name 
            <input class="form-control" type="hidden" name="Row_Count" id ="Row_Count"> 
            <input type="hidden" id="focusid" >
            <input type="hidden" id="errorid" >
        </th>
        <th>IFSC</th>
        <th>Branch</th>
        <th>Account Type</th>
        <th>Account No</th>
        <th width="5%">Action</th>
        </tr>
      </thead>
      <tbody>
      @if(!empty($objDataResponse))
      @php $n=1; @endphp
      @foreach($objDataResponse as $key => $row)
        <tr  class="participantRow" >
            <td>
              <input disabled  class="form-control w-100" type="text" name={{"NAME_".$key}} id ={{"BANK_NAME_".$key}} value="{{ $row->NAME }}" maxlength="50" autocomplete="off" style="text-transform:uppercase;width:100%" >
              <input disabled  type="hidden" name={{"BBID_".$key}} id ={{"BBID_".$key}}  value="{{ $row->BBID }}" >
            </td>
            <td><input disabled  class="form-control w-100" type="text" name={{"IFSC_".$key}} id ={{"IFSC_".$key}} value="{{ $row->IFSC }}" maxlength="30" autocomplete="off" style="text-transform:uppercase;width:100%" ></td>
            <td><input disabled  class="form-control w-100" type="text" name={{"BRANCH_".$key}} id ={{"BRANCH_".$key}} value="{{ $row->BRANCH }}" maxlength="100" autocomplete="off" style="text-transform:uppercase;width:100%" ></td>
            <td>
            <select disabled name={{"ACTYPE_".$key}} id ={{"ACTYPE_".$key}} class="form-control w-100"  autocomplete="off" style="width:100%" >
              <option value="" selected >Select</option>
              <option {{isset($row->ACTYPE) && $row->ACTYPE =='SAVING ACCOUNT'?'selected="selected"':''}} value='SAVING ACCOUNT'>SAVING ACCOUNT</option>
              <option {{isset($row->ACTYPE) && $row->ACTYPE =='CURRENT ACCOUNT'?'selected="selected"':''}} value='CURRENT ACCOUNT'>CURRENT ACCOUNT</option>
              <option {{isset($row->ACTYPE) && $row->ACTYPE =='OD'?'selected="selected"':''}} value='OD'>OD</option>
              <option {{isset($row->ACTYPE) && $row->ACTYPE =='OTHERS'?'selected="selected"':''}} value='OTHERS'>OTHERS</option>
            </select>
      
            </td>
            <td><input disabled  class="form-control w-100" type="text" name={{"ACNO_".$key}} id ={{"ACNO_".$key}} value="{{ $row->ACNO }}" maxlength="30" autocomplete="off"style="text-transform:uppercase;width:100%" onkeypress="return isNumberKey(event,this)" ></td>
 
            <td align="center" >
                <button  disabled class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></button>
                <button disabled class="btn remove" title="Delete" data-toggle="tooltip" {{isset($n) && $n ==1?'disabled':''}}  ><i class="fa fa-trash" ></i></button>
            </td>
        </tr>
        @php $n++; @endphp
        @endforeach 

        @else

        <tr  class="participantRow" >
            <td><input disabled  class="form-control w-100" type="text" name="NAME_0" id ="BANK_NAME_0"  maxlength="50" autocomplete="off" style="text-transform:uppercase;width:100%" ></td>
            <td><input disabled class="form-control w-100" type="text" name="IFSC_0" id ="IFSC_0" maxlength="30" autocomplete="off" style="text-transform:uppercase;width:100%" ></td>
            <td><input disabled class="form-control w-100" type="text" name="BRANCH_0" id ="BRANCH_0" maxlength="100" autocomplete="off" style="text-transform:uppercase;width:100%" ></td>
            <td>
            <select disabled name="ACTYPE_0" id="ACTYPE_0" class="form-control w-100"  autocomplete="off" style="width:100%" >
              <option value="" selected >Select</option>
              <option value='SAVING ACCOUNT'>SAVING ACCOUNT</option>
              <option value='CURRENT ACCOUNT'>CURRENT ACCOUNT</option>
              <option value='OD'>OD</option>
              <option value='OTHERS'>OTHERS</option>
            </select>
      
            </td>
            <td><input disabled  class="form-control w-100" type="text" name="ACNO_0" id ="ACNO_0" maxlength="30" autocomplete="off"style="text-transform:uppercase;width:100%" onkeypress="return isNumberKey(event,this)" ></td>
            <td align="center" >
                <button disabled class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></button>
                <button  class="btn remove" title="Delete" data-toggle="tooltip" disabled><i class="fa fa-trash" ></i></button>
            </td>
        </tr>
        @endif 
        
      </tbody>
    </table>
  </div>

</div>
<div id="ALPSSpecific" class="tab-pane fade">                    
                    <div class="inner-form" style="margin-top:10px;">
                      <div class="row">
                        <div class="col-lg-1 pl"><p>SAP Code		</p></div>
                        <div class="col-lg-1 pl">
                        <input type="text" name="SAP_CODE" id="SAP_CODE" disabled value="{{ old('SAP_CODE',$objResponse->SAP_CODE) }}" class="form-control" style="text-transform:uppercase">
                        </div>
                        <div class="col-lg-1 pl"><p>ALPS Ref No			</p></div>
                        <div class="col-lg-1 pl">
                        <input type="text" name="ALPS_REFNO" id="ALPS_REFNO" disabled value="{{ old('ALPS_REFNO',$objResponse->ALPS_REFNO) }}"class="form-control" style="text-transform:uppercase">
                        </div>              
                      </div>                      
                    </div>
                  </div>


        <div id="tab1" class="tab-pane fade">
              <div class="table-responsive table-wrapper-scroll-y " style="margin-top:10px;height:280px;width:50%;">
                <table id="udffietable" class="display nowrap table table-striped table-bordered itemlist" style="height:auto !important;">
                  <thead id="thead1"  style="position: sticky;top: 0">
                    <tr >
                    <th>UDF Fields <input class="form-control" type="hidden" name="Row_Count4" id ="Row_Count4" value="{{ $objudfCount }}"> </th>
                    <th>Value / Comments</th>
                    </tr>
                  </thead>
                  <tbody>

                  @foreach($objUDF as $udfkey => $udfrow)
                  <tr  class="participantRow">
                    <td>
                      <input disabled name={{"udffie_popup_".$udfkey}} id={{"txtudffie_popup_".$udfkey}} value="{{$udfrow->LABEL}}" class="form-control @if ($udfrow->ISMANDATORY==1) mandatory @endif" autocomplete="off" maxlength="100" />
                    </td>

                    <td hidden>
                      <input disabled type="text" name='{{"udffie_".$udfkey}}' id='{{"hdnudffie_popup_".$udfkey}}' value="{{$udfrow->UDFBRID}}" class="form-control" maxlength="100" />
                    </td>

                    <td hidden>
                      <input disabled type="text" name={{"udffieismandatory_".$udfkey}} id={{"udffieismandatory_".$udfkey}} class="form-control" maxlength="100" value="{{$udfrow->ISMANDATORY}}" />
                    </td>

                    <td id="{{"tdinputid_".$udfkey}}">
                      @php
                        $dynamicid = "udfvalue_".$udfkey;
                        $chkvaltype = strtolower($udfrow->VALUETYPE); 

                      if($chkvaltype=='date'){

                        $strinp = '<input disabled type="date" placeholder="dd/mm/yyyy" name="'.$dynamicid.'" id="'.$dynamicid.'" class="form-control" value="'.$udfrow->UDF_VALUE.'" /> ';       

                      }else if($chkvaltype=='time'){

                          $strinp= '<input disabled type="time" placeholder="h:i" name="'.$dynamicid.'" id="'.$dynamicid.'" class="form-control"  value="'.$udfrow->UDF_VALUE.'"/> ';

                      }else if($chkvaltype=='numeric'){
                      $strinp = '<input disabled type="text" name="'.$dynamicid. '" id="'.$dynamicid.'" class="form-control" value="'.$udfrow->UDF_VALUE.'"/> ';

                      }else if($chkvaltype=='text'){

                      $strinp = '<input disabled type="text" name="'.$dynamicid. '" id="'.$dynamicid.'" class="form-control" value="'.$udfrow->UDF_VALUE.'"/> ';

                      }else if($chkvaltype=='boolean'){
                          $boolval = ''; 
                          if($udfrow->UDF_VALUE=='on' || $udfrow->UDF_VALUE=='1' ){
                            $boolval="checked";
                          }
                          $strinp = '<input disabled type="checkbox" name="'.$dynamicid. '" id="'.$dynamicid.'" class=""  '.$boolval.' /> ';

                      }else if($chkvaltype=='combobox'){
                        $strinp='';
                        $txtoptscombo =   strtoupper($udfrow->DESCRIPTIONS); ;
                        $strarray =  explode(',',$txtoptscombo);
                        $opts = '';
                        $chked='';
                          for ($i = 0; $i < count($strarray); $i++) {
                            $chked='';
                            if($strarray[$i]==$udfrow->UDF_VALUE){
                              $chked='selected="selected"';
                            }
                            $opts = $opts.'<option value="'.$strarray[$i].'"'.$chked.'  >'.$strarray[$i].'</option> ';
                          }

                        $strinp = '<select disabled name="'.$dynamicid.'" id="'.$dynamicid.'" class="form-control" >'.$opts.'</select>' ;
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
	
	<div id="tab2" class="tab-pane fade">
		<div class="table-wrapper-scroll-x" style="margin-top:10px;">
			<div class="row">
				<div class="col-lg-2 "><p>Company Logo </p></div>

        <div class="col-lg-3 ">
        @if($objResponse->LOGO !="")
        <img src='{{$objResponse->LOGO}}' >
        @endif      
       
          
				</div>
			</div>	
		</div>
    </div>
	
	
  </div>

</div>





                
              
          

          </div>

    </div><!--purchase-order-view-->

    <script>
     $('#btnAdd').on('click', function() {
      var viewURL = '{{route("master",[147,"add"])}}';
      window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });
    </script>

@endsection