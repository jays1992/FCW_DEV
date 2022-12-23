/* Plugin API method to determine is a column is sortable */
$.fn.dataTable.Api.register('column().searchable()', function() {
  var ctx = this.context[0];
  return ctx.aoColumns[this[0]].bSearchable;
});

$(document).ready(function() {
  // Create the DataTable
  var table = $('#example2').DataTable({
    fixedHeader: true,
    pageLength: 25,
    orderCellsTop: true,
    columnDefs: [{
      searchable: false,
      targets: [0]
    }],
  });
  

  // Add filtering
  table.columns().every(function() {
    if (this.searchable()) {
      var that = this;


      var myList = $('<ul/>');
      var myMulti = $('<div class="mutliSelect"/>');
      myList.appendTo(myMulti);

      var myDd = $('<dd/>');
      myMulti.appendTo(myDd);

      var myDropdown = $('<dl class="dropdown"/>');
      myDropdown.append('<dt><a href="#"><span class="hida"><i class="fa fa-filter"></i></span><p class="multiSel"></p></a></dt>');
      myDd.appendTo(myDropdown);

      myDropdown
        .appendTo(
          $('#thead1 tr:eq(1) td').eq(this.index())
        )
        .on('change', function() {
          var vals = $(':checked', this).map(function(index, element) {
            return $.fn.dataTable.util.escapeRegex($(element).val());
          }).toArray().join('|');

          that
            .search(vals.length > 0 ? '^(' + vals + ')$' : '', true, false)
            .draw();
        });


      // Add data
      this
        .data()
        .sort()
        .unique()
        .each(function(d) {
          myList.append($('<li><input type="checkbox" value="' + d + '"/>' + d + '</option>'));
        });

    }
  });



var table = $('#example3').DataTable({
    fixedHeader: true,
    pageLength: 25,
    orderCellsTop: true,
    columnDefs: [{
      searchable: false,
      //targets: [0]
    }],
  });
  // Add filtering
  table.columns().every(function() {
    if (this.searchable()) {
      var that = this;


      var myList = $('<ul/>');
      var myMulti = $('<div class="mutliSelect"/>');
      myList.appendTo(myMulti);

      var myDd = $('<dd/>');
      myMulti.appendTo(myDd);

      var myDropdown = $('<dl class="dropdown"/>');
      myDropdown.append('<dt><a href="#"><span class="hida"><i class="fa fa-filter"></i></span><p class="multiSel"></p></a></dt>');
      myDd.appendTo(myDropdown);

      myDropdown
        .appendTo(
          $('#thead2 tr:eq(1) td').eq(this.index())
        )
        .on('change', function() {
          var vals = $(':checked', this).map(function(index, element) {
            return $.fn.dataTable.util.escapeRegex($(element).val());
          }).toArray().join('|');

          that
            .search(vals.length > 0 ? '^(' + vals + ')$' : '', true, false)
            .draw();
        });


      // Add data
      this
        .data()
        .sort()
        .unique()
        .each(function(d) {
          myList.append($('<li><input type="checkbox" value="' + d + '"/>' + d + '</option>'));
        });

    }
  });
  
  
  
  var table = $('#example4').DataTable({
    fixedHeader: true,
    pageLength: 25,
    orderCellsTop: true,
    columnDefs: [{
      searchable: false,
      targets: [0, 6, 7]
    }],
  });
  // Add filtering
  table.columns().every(function() {
    if (this.searchable()) {
      var that = this;


      var myList = $('<ul/>');
      var myMulti = $('<div class="mutliSelect"/>');
      myList.appendTo(myMulti);

      var myDd = $('<dd/>');
      myMulti.appendTo(myDd);

      var myDropdown = $('<dl class="dropdown"/>');
      myDropdown.append('<dt><a href="#"><span class="hida"><i class="fa fa-filter"></i></span><p class="multiSel"></p></a></dt>');
      myDd.appendTo(myDropdown);

      myDropdown
        .appendTo(
          $('#thead3 tr:eq(1) td').eq(this.index())
        )
        .on('change', function() {
          var vals = $(':checked', this).map(function(index, element) {
            return $.fn.dataTable.util.escapeRegex($(element).val());
          }).toArray().join('|');

          that
            .search(vals.length > 0 ? '^(' + vals + ')$' : '', true, false)
            .draw();
        });


      // Add data
      this
        .data()
        .sort()
        .unique()
        .each(function(d) {
          myList.append($('<li><input type="checkbox" value="' + d + '"/>' + d + '</option>'));
        });

    }
  });

  /*
  	Dropdown with Multiple checkbox select with jQuery - May 27, 2013
  	(c) 2013 @ElmahdiMahmoud
  	license: https://www.opensource.org/licenses/mit-license.php
  */

  $(".dropdown dt a").on('click', function(e) {
    var dropdown = $(this).closest('.dropdown');
    dropdown.find('ul').slideToggle('fast');

    $('.dropdown').not(dropdown).find('ul').slideUp('fast');

  });

  $(".dropdown dd ul li a").on('click', function() {
    $(".dropdown dd ul").hide();
  });

  function getSelectedValue(id) {
    return $("#" + id).find("dt a span.value").html();
  }

  $(document).bind('click', function(e) {
    var $clicked = $(e.target);
    if (!$clicked.parents().hasClass("dropdown")) $(".dropdown dd ul").slideUp('fast');

  });

  $('.multiSelect input[type="checkbox"]').on('click', function() {
    var title = $(this).closest('.multiSelect').find('input[type="checkbox"]').val(),
      title = $(this).val() + ",";

    if ($(this).is(':checked')) {
      var html = '<span title="' + title + '">' + title + '</span>';
      $('.multiSel').append(html);
      $(".hida").hide();
    } else {
      $('span[title="' + title + '"]').remove();
      var ret = $(".hida");
      $('.dropdown dt a').append(ret);

    }
  });
});