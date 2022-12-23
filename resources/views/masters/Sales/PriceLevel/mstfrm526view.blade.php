@extends('layouts.app')
@section('content')

<div class="container-fluid topnav">
	<div class="row">
		<div class="col-lg-2"><a href="{{route('master',[526,'index'])}}" class="btn singlebt">Price List Standard Master</a></div>
		<div class="col-lg-10 topnav-pd">
      <button  id="btnSelectedRows" class="btn topnavbt" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
      <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
      <button class="btn topnavbt"  disabled><i class="fa fa-save"></i> Save</button>
      <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
      <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
      <button class="btn topnavbt" id="btnUndo"  disabled><i class="fa fa-undo"></i> Undo</button>
      <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
      <button class="btn topnavbt" id="btnApprove" disabled><i class="fa fa-lock"></i> Approved</button>
      <button class="btn topnavbt" id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
      <a href="{{route('home')}}" class="btn topnavbt"><i class="fa fa-power-off"></i> Exit</a>
		</div>
	</div>
</div>
@php
  //DUMP($objPopup1List);
//  DUMP($objList1);   
@endphp
<div class="container-fluid filter">
	<form id="form_data" method="POST" onsubmit="return false;" > 
   
		<div class="inner-form">
     
      <div class="row">
        <div class="col-lg-2 pl"><p>Price Level Code</p></div>
        <div class="col-lg-2 pl">
          <label>{{ $objMstResponse->PLCODE }}</label>
        </div>	
        <div class="col-lg-1 pl"><p>Price Level Name</p></div>
        <div class="col-lg-2 pl">
          <input type="text" name="PLCID_REF_POPUP" id="PLCID_REF_POPUP" class="form-control mandatory" value="{{ $objMstResponse->PLNAME }}" required readonly tabindex="3" />
        </div>
        <div class="col-lg-2 pl"><p>Remarks</p></div>
        <div class="col-lg-2 pl">
          <input type="text" name="PLCID_REF_POPUP" id="PLCID_REF_POPUP" class="form-control mandatory" value="{{ $objMstResponse->REMARKS }}" required readonly tabindex="3" />
        </div>
      </div>

      <div class="row">

        <div class="col-lg-2 pl"><p>De-Activated</p></div>
        <div class="col-lg-2 pl pr">
        <input type="checkbox"   name="DEACTIVATED"  id="deactive-checkbox_0" value='{{$objMstResponse->DEACTIVATED == 1 ? 1 : 0}}'  tabindex="3" disabled>
        </div>
        
        <div class="col-lg-2 pl"><p>Date of De-Activated</p></div>
        <div class="col-lg-2 pl">
        <div class="col-lg-8 pl">
          <label>{{ (!is_null($objMstResponse->DODEACTIVATED) && $objMstResponse->DODEACTIVATED!='1900-01-01')? \Carbon\Carbon::parse($objMstResponse->DODEACTIVATED)->format('d/m/Y') : ''   }}</label>
        </div>
        </div>   
        
      </div>

	</div>

                      <div class="container-fluid purchase-order-view">
                        <div class="row">
                            <ul class="nav nav-tabs">
                                <li><a data-toggle="tab" href="#udf">UDF</a></li>
                            </ul> 
                            <div class="tab-content">

                                <div id="udf" class="tab-pane fade active in">
                                    <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="margin-top:10px;height:280px;width:50%;">
                                        <table id="example4" class="display nowrap table table-striped table-bordered itemlist" style="height:auto !important;">
                                            <thead id="thead1"  style="position: sticky;top: 0">
                                            <tr >
                                                <th>UDF Fields<input class="form-control" type="hidden" name="Row_Count3" id ="Row_Count3" value="{{ $objCountUDF }}"></th>
                                                <th>Value / Comments</th>
                                            </tr>
                                            </thead>
                                            
                                            <tbody>
                                              @if(isset($objUdf) && !empty($objUdf))
                                              @foreach($objUdf as $udfkey => $udfrow)
                                              <tr  class="participantRow4">
                                                <td>
                                                  <input {{$ActionStatus}} name={{"udffie_popup_".$udfkey}} id={{"txtudffie_popup_".$udfkey}} value="{{$udfrow->LABEL}}" class="form-control @if ($udfrow->ISMANDATORY==1) mandatory @endif" autocomplete="off" maxlength="100" disabled/>
                                                </td>
                                
                                                <td hidden>
                                                  <input type="text" name='{{"udffie_".$udfkey}}' id='{{"hdnudffie_popup_".$udfkey}}' value="{{$udfrow->UDFID}}" class="form-control" maxlength="100" />
                                                </td>
                                
                                                <td hidden>
                                                  <input type="text" name={{"udffieismandatory_".$udfkey}} id={{"udffieismandatory_".$udfkey}} class="form-control" maxlength="100" value="{{$udfrow->ISMANDATORY}}" />
                                                </td>
                                
                                                <td id="{{"tdinputid_".$udfkey}}">
                                                  @php
                              
                                                      $dynamicid = "udfvalue_".$udfkey;
                                                      $chkvaltype = strtolower($udfrow->VALUETYPE); 

                                                    if($chkvaltype=='date'){

                                                      $strinp = '<input '.$ActionStatus.' type="date" placeholder="dd/mm/yyyy" name="'.$dynamicid.'" id="'.$dynamicid.'" class="form-control" value="'.$udfrow->UDF_VALUE.'" /> ';       

                                                    }else if($chkvaltype=='time'){

                                                        $strinp= '<input '.$ActionStatus.' type="time" placeholder="h:i" name="'.$dynamicid.'" id="'.$dynamicid.'" class="form-control"  value="'.$udfrow->UDF_VALUE.'"/> ';

                                                    }else if($chkvaltype=='numeric'){
                                                    $strinp = '<input '.$ActionStatus.' type="text" name="'.$dynamicid. '" id="'.$dynamicid.'" class="form-control" value="'.$udfrow->UDF_VALUE.'"/> ';

                                                    }else if($chkvaltype=='text'){

                                                    $strinp = '<input '.$ActionStatus.' type="text" name="'.$dynamicid. '" id="'.$dynamicid.'" class="form-control" value="'.$udfrow->UDF_VALUE.'"/> ';

                                                    }else if($chkvaltype=='boolean'){
                                                        $boolval = ''; 
                                                        if($udfrow->UDF_VALUE=='on' || $udfrow->UDF_VALUE=='1' ){
                                                          $boolval="checked";
                                                        }
                                                        $strinp = '<input '.$ActionStatus.' type="checkbox" name="'.$dynamicid. '" id="'.$dynamicid.'" class=""  '.$boolval.' /> ';

                                                    }else if($chkvaltype=='combobox'){
                                                      $strinp='';
                                                    $txtoptscombo =   strtolower($udfrow->DESCRIPTIONS); ;
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

                                                      $strinp = '<select '.$ActionStatus.' name="'.$dynamicid.'" id="'.$dynamicid.'" class="form-control" >'.$opts.'</select>' ;


                                                    }
                                                    echo $strinp;
                                                    @endphp
                                                </td>
                                              </tr>
                                              @endforeach
                                              @endif
                                              </tbody>
                                        </table>
                                    </div>
                                </div>
                                </div>
                        </div>
                    </div>
						
	</form>
  </div>
@endsection

@section('alert')
<div id="alert" class="modal"  role="dialog"  data-backdrop="static" >
  <div class="modal-dialog" style="position:relative;top:82px;left:273px;"  >
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
            <button class="btn alertbt" name='OkBtn1' id="OkBtn1" style="margin-left: 90px;display:none;">
              <div id="alert-active" class="activeOk1"></div>OK</button>
        </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

@endsection

@push('bottom-css')
@endpush

@push('bottom-scripts')

<script type="text/javascript">
//UDF Tab Starts
//------------------------

let udftid = "#UDFSOIDTable2";
      let udftid2 = "#UDFSOIDTable";
      let udfheaders = document.querySelectorAll(udftid2 + " th");

      // Sort the table element when clicking on the table headers
      udfheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(udftid, ".clsudfsoid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function UDFSOIDCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("UDFSOIDcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("UDFSOIDTable2");
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

  function UDFSOIDNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("UDFSOIDnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("UDFSOIDTable2");
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

$("#udfsoid_closePopup").on("click",function(event){ 
     $("#udfsoidpopup").hide();
});

$('.clsudfsoid').dblclick(function(){
    
        var id = $(this).attr('id');
        var txtid =    $("#txt"+id+"").val();
        var txtname =   $("#txt"+id+"").data("desc");
        var fieldid2 = $(this).find('[id*="udfvalue"]').attr('id');
        var txtvaluetype = $.trim($(this).find('[id*="udfvalue"]').text().trim());
        var txtismandatory =  $("#txt"+fieldid2+"").val();
        var txtdescription =  $("#txt"+fieldid2+"").data("desc");
        
        var txtcol = $('#hdn_UDFSOID').val();
        $("#"+txtcol).val(txtname);
        $("#"+txtcol).parent().parent().find("[id*='UDFSOID_REF']").val(txtid);
        $("#"+txtcol).parent().parent().find("[id*='UDFismandatory']").val(txtismandatory);
        
        var txt_id4 = $("#"+txtcol).parent().parent().find("[id*='udfinputid']").attr('id');  //<td> id 

        var strdyn = txt_id4.split('_');
        var lastele =   strdyn[strdyn.length-1];

        var dynamicid = "udfvalue_"+lastele;

        var chkvaltype2 =  txtvaluetype.toLowerCase();
        var strinp = '';

        if(chkvaltype2=='date'){

          strinp = '<input type="date" placeholder="dd/mm/yyyy" name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" /> ';       

        }else if(chkvaltype2=='time'){
          strinp= '<input type="time" placeholder="h:i" name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" /> ';

        }else if(chkvaltype2=='numeric'){
          strinp = '<input type="text" name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" /> ';

        }else if(chkvaltype2=='text'){

          strinp = '<input type="text" name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" /> ';
        
        }else if(chkvaltype2=='boolean'){

          strinp = '<input type="checkbox" name="'+dynamicid+ '" id="'+dynamicid+'" class="" /> ';
        
        }else if(chkvaltype2=='combobox'){
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
        $('#'+txt_id4).html(strinp);   //set dynamic input

        $("#udfsoidpopup").hide();
        $("#UDFSOIDcodesearch").val(''); 
        $("#UDFSOIDnamesearch").val(''); 
        UDFSOIDCodeFunction();
        event.preventDefault();
            
 });
 
//UDF Tab Ends
//------------------------ 


$.each( objtnc, function( tnckey, tncvalue ) {

$.each( tncheader, function( tnchkey, tnchvalue ) { 
    if(tncvalue.TNCID_REF == tnchvalue.TNCID)
    {
        $('#txtTNCID_popup').val(tnchvalue.TNC_CODE+' - '+tnchvalue.TNC_DESC);
    }
});

$.each( tncdetails, function( tncdkey, tncdvalue ) { 

  if(tncvalue.TNCDID_REF == tncdvalue.TNCDID)
  {
      $('#popupTNCDID_'+tnckey).val(tncdvalue.TNC_NAME);
  }

  if( $.trim(tncvalue.TNCDID_REF) == $.trim(tncdvalue.TNCDID))
  {        
            var txtvaltype =   tncdvalue.VALUE_TYPE;
            var txt_id4 = $('#tdinputid_'+tnckey).attr('id');
            var strdyn = txt_id4.split('_');
            var lastele =   strdyn[strdyn.length-1];
            var dynamicid = "tncdetvalue_"+lastele;
            
            var chkvaltype =  txtvaltype.toLowerCase();
            var strinp = '';

            if(chkvaltype=='date'){

            strinp = '<input {{$ActionStatus}} type="date" placeholder="dd/mm/yyyy" name="'+dynamicid+ '" id="'+dynamicid+'" autocomplete="off" class="form-control"  > ';       

            }
            else if(chkvaltype=='time'){
            strinp= '<input {{$ActionStatus}} type="time" placeholder="h:i" name="'+dynamicid+ '" id="'+dynamicid+'" autocomplete="off" class="form-control"  > ';

            }
            else if(chkvaltype=='numeric'){
            strinp = '<input {{$ActionStatus}} type="text" name="'+dynamicid+ '" id="'+dynamicid+'" autocomplete="off" class="form-control"   > ';

            }
            else if(chkvaltype=='text'){

            strinp = '<input {{$ActionStatus}} type="text" name="'+dynamicid+ '" id="'+dynamicid+'" autocomplete="off" class="form-control"  > ';
            
            }
            else if(chkvaltype=='boolean'){
              if(tncvalue.VALUE == "1")
              {
                strinp = '<input {{$ActionStatus}} type="checkbox" name="'+dynamicid+ '" id="'+dynamicid+'" class="" checked> ';
              }
              else{
                strinp = '<input {{$ActionStatus}} type="checkbox" name="'+dynamicid+ '" id="'+dynamicid+'" class="" > ';
              }                    
            }
            else if(chkvaltype=='combobox'){

            var txtoptscombo =   tncdvalue.DESCRIPTIONS;
            var strarray = txtoptscombo.split(',');
            var opts = '';

            for (var i = 0; i < strarray.length; i++) {
                opts = opts + '<option value="'+strarray[i]+'">'+strarray[i]+'</option> ';
            }

            strinp = '<select {{$ActionStatus}} name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" required>'+opts+'</select>' ;
            
            }
             
            $('#'+txt_id4).html('');  
            $('#'+txt_id4).html(strinp);   //set dynamic input
            $('#'+dynamicid).val(tncvalue.VALUE);
            $('#TNCismandatory_'+tnckey).val(tncdvalue.IS_MANDATORY); // mandatory
        
    }
});
});

</script>

@endpush