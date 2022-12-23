
@extends('layouts.app')
@section('content')
    
    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="#" class="btn singlebt">Country Master</a>
                </div><!--col-2-->

                <div class="col-lg-8 topnav-pd">
                        <button id="btnSelectedRows" class="btn topnavbt"><i class="fa fa-plus"></i> Add</button>
                        <a href="#" class="btn topnavbt"><i class="fa fa-pencil-square-o"></i> Edit</a>
                        <a href="#" class="btn topnavbt"><i class="fa fa-floppy-o"></i> Save</a>
                        <button class="btn topnavbt" id="btnView"><i class="fa fa-eye"></i> View</button>
                        <a href="{{route('master',[4,'index'])}}" class="btn topnavbt"><i class="fa fa-print"></i> Print</a>
                        <a href="{{route('master',[4,'show',212])}}" class="btn topnavbt"><i class="fa fa-undo"></i> Undo</a>
                        <a href="{{route('master',[5,'index'])}}" class="btn topnavbt"><i class="fa fa-times"></i> Cancel</a>
                        <a href="{{route('master',[5,'show',222])}}" class="btn topnavbt"><i class="fa fa-thumbs-o-up"></i> Approved</a>
                        <a href="#" class="btn topnavbt"><i class="fa fa-link"></i> Attachment</a>
                        <a href="#" class="btn topnavbt"><i class="fa fa-power-off"></i> Exit</a>
                </div><!--col-8-->

                <!-- <div class="col-lg-2">
                <form>
                    <div class="form-group">
                    <input type="text" name="" class="form-control" placeholder="Search">
                    </div>
                </form>
                </div> -->

            </div><!--row-->
    </div><!--topnav-->	
    <!-- multiple table-responsive table-wrapper-scroll-y my-custom-scrollbar -->

    <div class="container-fluid purchase-order-view">
        <div class="multiple table-responsive  ">
              <table id="countrymst" class="display nowrap table table-striped table-bordered" width="100%">
            <thead id="thead1">
            <tr>
                <th id="all-check"><input type="checkbox" class="js-selectall" data-target=".js-selectall1" />Select</th>
                <th>Country Code</th>
                <th>Country Name</th>
                <th>ISD Code</th>
                <th>Language</th>
                <th>Continental</th>
                <th>Capital</th>
            </tr>
           
            </thead>
        </table>
        </div>
    </div><!--purchase-order-view-->

</div>
@endsection
@section('alert')
<div id="alert" class="modal" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">System Alert Message</h4>
      </div>
      <div class="modal-body">
	  <h5>Duplicate Party Name, Do you want to continue?</h5>
        <div class="btdiv"><button class="btn alertbt"><div id="alert-active"></div> Yes</button> <button class="btn alertbt">No</button></div><!--btdiv-->
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Alert -->
@endsection
@push('bottom-scripts')
@push('bottom-css')
<style>
#custom_dropdown, #countrymst_filter {
    display: inline-table;
    margin-left: 15px;
}
.dataTables_wrapper .row:nth-child(1) .col-sm-6:nth-child(2){text-align:right;}
#filtercolumn{color: #555;
    background-color: #fff;
    background-image: none;
    border: 1px solid #ccc;
    }
</style>
@endpush

<script>
     

      // $('.nav-toggle').click(function(e) {
      //   e.preventDefault();
      //   $("body").toggleClass("openNav");
      //   $(".nav-toggle").toggleClass("active");

      // });

      // $('.single-select tr').click(function(e) {
      //     $('.single-select tr').removeClass('highlighted');
      //     $(this).addClass('highlighted');
      // });

  $(document).ready(function(){

       
        // Data table for serverside
         var mstcountryTable =  $('#countrymst').DataTable({

            "processing": true,
            "serverSide": true,
            initComplete: function() {
                      $('.dataTables_filter input').unbind();
                      $('.dataTables_filter input').bind('keyup', function(e){
                          var code = e.keyCode || e.which;
                          if (code == 13) { 
                            mstcountryTable.search(this.value).draw();
                          }
                      });
                   },
            "ajax":{

                     "url": "{{ route('masters.getcountries') }}",
                     "dataType": "json",
                     "type": "POST",
                      "data": function ( d ) {
                                  d._token= "{{csrf_token()}}";
                                  d.filtercolumn = $('#filtercolumn option:selected').val();
                              }
                   },
            "columns": [

                { "data": "NO" },
                { "data": "CTRYCODE" },
                { "data": "NAME" },
                { "data": "ISDCODE" },
                { "data": "LANG" },
                { "data": "CONTINENTAL" },
                { "data": "CAPITAL" }

            ],
            "order": [[ 3, "asc" ]],
            "lengthMenu": [ 10, 15, 20, 50, 100 ],
            "iDisplayLength": 10,
            aoColumnDefs: [
            {
               bSortable: false,
               aTargets: [ 0]
            },
          ] 
       
        }); //datatable

      //custom dropdown for filter  
      var htmlDD =  '<div id="custom_dropdown"><div id="countrymst_dropdown" class="dataTables_filter">'
        +'<select id="filtercolumn" name="filtercolumn">'
        +'<option value="CTRYCODE" >Country Code </option>'
        +'<option value="NAME">Country Name </option>'
        +'<option value="ISDCODE">ISD Code </option>'
        +'<option value="LANG">Language </option>'
        +'<option value="CONTINENTAL">Continental </option>'
        +'<option value="CAPITAL">Capital </option>'
        +'</select></div></div>';
      
          $( htmlDD ).insertBefore( "#countrymst_wrapper #countrymst_filter" ); 

      
        // select all checkboxes
        $('.js-selectall').on('change', function() {
          var isChecked = $(this).prop("checked");
          var selector = $(this).data('target');
          $(selector).prop("checked", isChecked);
        });


      //get the selected row
      $('#btnSelectedRows').on('click', function() {
        var AccountsJsonString = JSON.stringify(getSeletectedCBox());
        alert(AccountsJsonString);

      });

      $('#btnView').on('click', function() {
        var AccountsJsonString = JSON.stringify(getSeletectedCBox());
        alert('ids='+AccountsJsonString);
      });

      //get selected check boxes
      function getSeletectedCBox(){
            selectedIds=[];
            var checkedcollection = mstcountryTable.$(".js-selectall1:checked", { "page": "all" });
              checkedcollection.each(function (index, elem) {
              selectedIds.push($(elem).val());
            });
            return selectedIds;
      }
    
});  //reday
</script>


@endpush
