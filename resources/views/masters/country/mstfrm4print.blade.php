@extends('layouts.app')
@section('content')

<div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[4,'index'])}}" class="btn singlebt">Country Master</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <a href="{{route('master',[4,'add'])}}" id="btnSelectedRows" class="btn topnavbt" disabled="disabled"><i class="fa fa-plus"></i> Add</a>
                        <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
                        <button class="btn topnavbt" disabled="disabled"><i class="fa fa-save"></i> Save</button>
                        <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button href="#" class="btn topnavbt" id="btnPrint" ><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt" disabled="disabled"><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" disabled="disabled"><i class="fa fa-lock"></i> Approved</button>
                        <button class="btn topnavbt" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                        <a href="#" class="btn topnavbt" ><i class="fa fa-power-off"></i> Exit</a>
                </div>

            </div><!--row-->
    </div><!--topnav-->	
<div class="container-fluid purchase-order-view">
        <div class="multiple ">
              <table id="countrymst" class="display nowrap table table-striped table-bordered" width="100%">
            <thead id="thead1">
            <tr>
                <th>Country Code</th>
                <th>Country Name</th>
                <th>ISD Code</th>
                <th>Language</th>
                <th>Continental</th>
                <th>Capital</th>
            </tr>
            </thead>
            @forelse($objCountries as $country_row)
                <tr>
                    <td>{{ $country_row["CTRYCODE"] }}</td>
                    <td>{{ $country_row->NAME }}</td>
                    <td>{{ $country_row->ISDCODE }}</td>
                    <td>{{ $country_row->LANG }}</td>
                    <td>{{ $country_row->CONTINENTAL }}</td>
                    <td>{{ $country_row->CAPITAL }}</td>
                </tr>
              @empty
                <tr>
                    <td colspan="6">No record found.</td>
                </tr>
            @endforelse
        </table>
        </div>
    </div><!--purchase-order-view-->
@endsection

@push('bottom-scripts')
<script>
   $(document).ready(function(){

     function doprint(){
      window.print();

     }

    $("#btnPrint").click(function(){
      doprint();
    }); //btnPrint button

     doprint();  //on document load

   });    
</script>
@endpush