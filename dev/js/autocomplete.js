var cache = {};

$('input[data-autocomplete]').each(function(){
    var el = $(this);
    el.autocomplete({
        minLength: 2,
        source: function( request, response ) {
            var localQueryObject = {};
            var property = el.data('property');
            localQueryObject.index = (queryObj.index == 'false')? 'false' : 'true';
            localQueryObject[property] = request.term;

            // Cache
            var term = property + request.term + localQueryObject.index;
            if (term in cache) {
              response(cache[term]);
              return;
            }

            // Fetch results
            $.getJSON( baseURL + "suggest?", localQueryObject, function( data, status, xhr ) {
                cache[term] = data;
                response(data);
            });
        },

        // Force pick a value
        // select: function (event, ui) {
        //     return false;
        // },

        // select: function (event, ui) {
        //     $(this).val(ui.item ? ui.item : " ");
        // },

        // change: function (event, ui) {
        //     if (!ui.item) {
        //         this.value = '';
        //     }
        // }
    }).keyup(function (e) {
        if(e.which === 13) {
            $(".ui-autocomplete").hide();
        }
    });
})