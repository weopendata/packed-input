function search(e){
    e.preventDefault();

    var controller = angular.element($('#results')).scope();
    var form = $(e.target);
    var submit = $('button[type=submit]', form);
    submit.prop('disabled', true);
    submit.html("<i class='fa fa-spin fa-spinner'></i>")
    $('.row', form).removeClass('error');
    $('#searchStatus', form).html('');
    controller.reset();
    buildQueryObj();

    var localQueryObject = {};
    var validated = true;
    var minimumOne = false;
    localQueryObject.normalized = (queryObj.normalized == 'false')? 'false' : 'true';
    $('form input[data-property]:not(:disabled)').each(function(){
        var value = $(this).val();
        localQueryObject[$(this).data('property')] = value;

        if(!value || value == ''){
            $(this).closest('.row').addClass('error');
            validated = false;
        }

        minimumOne = true;
    });

    if(!validated){
        submit.html('Zoek');
        submit.prop('disabled', false);
    }else if(!minimumOne){
        $('#searchStatus', form).html('Kies minstens één van de bovenstaande filters.');
        submit.html('Zoek');
        submit.prop('disabled', false);
    }else{
        $.ajax(
            baseURL + "api/query?",
            {
                data: localQueryObject,
                method: 'GET',
                success: function(data) {
                    var innerData = {};
                    if('artists' in data){
                        innerData = data.artists;
                        controller.loadArtists(innerData);
                    }else{
                        innerData = data.works;
                        controller.loadWorks(innerData);
                    }

                    updateSearchStatus(innerData);

                    submit.html('Zoek');
                    submit.prop('disabled', false);
                },
                error: function(data) {
                    var error = JSON.parse(data.responseText);
                    error = error.error;
                    alertify.error('An error occurred: ' + data.status + ' - ' + error.message);

                    submit.html('Zoek');
                    submit.prop('disabled', false);
                }
            }
        );
    }
}

function updateSearchStatus(data) {
    var count = data.length;

    if(count == 0){
        status = 'Geen resultaten';
    }else if(count == 1){
        status = '1 resultaat';
    }else{
        status = count + ' resultaten';
    }

    $('#searchStatus').delay(1000).html(status + ' gevonden.')
}
