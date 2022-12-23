
@extends('layouts.app')
@section('content')
    
@endsection

@if(!empty($objitem))
        @foreach ($objitem as $index=>$objitems)
                            <tr id="mainitemidcode_substitute_{{ $index }}" class="mainitem_tab1">
                                <td width="25%">
                                    {{ $objitems-> ICODE }}
                                    <input
                                        type="hidden"
                                        id="txtmainitemidcode_substitute_{{ $index }}"
                                        data-code="{{ $objitems-> ICODE }}"
                                        data-uomno="{{ $objitems-> MAIN_UOMID_REF }}"
                                        data-name="{{ $objitems-> NAME }}"
                                        data-uom="{{ $objitems-> UOMCODE.'-'.$objitems-> DESCRIPTIONS }}"
                                        value="{{ $objitems-> ITEMID }}"
                                  
                                    />
                                </td>
                                <td width="25%">{{ $objitems-> NAME }}</td>
                                <td width="25%">{{ $objitems-> UOMCODE }}-{{ $objitems-> DESCRIPTIONS }}</td>
                                <td width="25%">{{ $objitems-> DRAWINGNO }}</td>
                            </tr>
        @endforeach
        @else

                         <tr>
                         <td colspan="4">No record found!</td>                                   
                        </tr

        @endif




    

@push('bottom-css')

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
    window.location.href = '{{route("transaction",[270,"index"]) }}';
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