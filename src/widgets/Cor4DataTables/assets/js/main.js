function cor4DataTables( selector, options ) {

    jQuery(selector).DataTable({

      searching: false,
      ajax: '/api/listkess',
      paging: false,
      scrollY: '100%',
      pageLength: 0,
      buttons: [
        'click'
      ],

      initComplete : function() {

        console.log('initComplete');
        var table = this.api();

        $('<tr></tr>').appendTo($('thead'));
  
        // Add filtering
        table.columns().every(function() {
          var column = this;

          //#example > thead > tr:nth-child(1) > th.sorting.sorting_asc
          //#datatables_w0 > thead > tr > th:nth-child(2)

          $('<th></th>').appendTo($("thead tr:eq(1)"));
          //$("thead tr:eq(1)").appendTo('<th></th>')

          var index = this.index();
  
          var input = $('<input type="text" />')
            .appendTo($("thead tr:eq(1) th").eq(this.index()))
            .on("keyup", function(evt) {
              var searchText = '';
              table.columns().every(function() {
                searchText += 'search['+this.index()+']='+$("thead tr:eq(1) th input").eq(this.index()).val()+'&';
              });
              table.ajax.url('/api/listkess?'+searchText).load();
              //column.search($(this).val()).draw();
            });
        });
    }
    });
}
