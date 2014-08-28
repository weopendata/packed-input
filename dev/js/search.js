function search(e){
    e.preventDefault();

    // Angular controller
    var controller = angular.element($('#results')).scope();

    // Get form and submit button
    var form = $(e.target);
    var submit = $('button[type=submit]', form);

    // Disable submit button
    submit.prop('disabled', true);

    // Clear previous errors
    $('.row', form).removeClass('error');

    // Clear previous results
    $('#searchStatus', form).html('');
    controller.reset();

    // Rebuild query object
    buildQueryObj();

    // Build data object
    var localQueryObject = {};
    var validated = true;
    var minimumOne = false;
    $('form input[data-property]:not(:disabled), form select[data-property]:not(:disabled)').each(function(){
        var value = $(this).val();
        localQueryObject[$(this).data('property')] = value;

        if(!value || value == ''){
            $(this).closest('.row').addClass('error');
            validated = false;
        }

        minimumOne = true;
    });

    if(!validated){
        resetForm();
    }else if(!minimumOne){
        $('#searchStatus', form).html('Kies minstens één van de bovenstaande filters.');
        resetForm();
    }else{
        $('#results').show();
        $('.fa-spin').removeClass('hidden').stop().fadeIn();
        controller.loadWorks(localQueryObject);
    }
}

/**
 * Reset form for next search
 */
function resetForm() {
    var submit = $('button[type=submit]');
    submit.prop('disabled', false);
}

/**
 * Search status text
 */
function updateSearchStatus(data) {
    var count = data.length;

    if(count == 0){
        status = 'Geen resultaten';
    }else if(count == 1){
        status = '1 resultaat';
    }else{
        status = count + ' resultaten';
    }

    $('#searchStatus').html(status + ' gevonden.');
}
