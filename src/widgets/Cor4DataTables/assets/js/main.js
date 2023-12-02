function cor4DataTables( selector, options ) {

    jQuery(selector).DataTable({

      searching: false,
      //ajax: '/api/listkess',
      paging: true,
      scrollX: true,
      scroller: true,
      scrollY: '100%',
      pageLength: 25,
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
      
          //var index = this.index();
          let th_style = $("thead tr:eq(0) th").eq(this.index()).attr('style');
          let searchParams = new URLSearchParams(window.location.search);
          let param = searchParams.get("search["+this.index()+"]");
          if (param == null) {
            param = '';
          }
          var input = $('<input type="text" style="'+th_style+'" value="'+param+'"/>')
            .appendTo($("thead tr:eq(1) th").eq(this.index()))
            .on("keyup", function(evt) {
              if(evt.key == 'Enter') {
                var searchText = '';
                table.columns().every(function() {
                  searchText += 'search['+this.index()+']='+$("thead tr:eq(1) th input").eq(this.index()).val()+'&';
                });
                //table.ajax.url('/api/listkess?'+searchText).load();
                window.location = '/site/listkess?'+searchText;
              }
              //column.search($(this).val()).draw();
            });
        });
    }
    });
}
