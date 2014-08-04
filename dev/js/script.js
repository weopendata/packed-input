/**
 * Enabling switches
 */
$('.form-enabler-switch').each(enablingSwitches);
$('.form-enabler-switch').on('change', enablingSwitches);
$('.switch').on('change', buildQueryObj);

function enablingSwitches() {
    var row = $(this).closest('.row');

    if ($('input', $(this)).is(':checked')) {
        $('input', row).prop('disabled', false);
    } else {
        $('input', row).prop('disabled', true);
    }

    $('input', this).prop('disabled', false);
}


/**
 * Form submission
 */
$('form').on('submit', search);

/**
 * Query object
 */
var queryObj = {};
function buildQueryObj() {
    queryObj = {};

    $('form input').not('input[id*=enabled], input[type=submit]').each(function(){
        var e = $(this);

        // Leave out disabled properties
        if(!e.prop('disabled')){
            if(e.is('input[type=checkbox]')){
                if(!e.is(':checked')){
                    queryObj[e.attr('id')] = 'false';
                }
            }else{
                queryObj[e.attr('id')] = e.val();
            }
        }
    })

    // Push state
    history.replaceState({}, 'PID demonstrator', '?' + $.param(queryObj));
}

/**
 * Initial load
 */
jQuery.extend({
  getQueryParameters : function(str) {
      return (str || document.location.search).replace(/(^\?)/,'').split("&").map(function(n){return n = n.split("="),this[n[0]] = n[1],this}.bind({}))[0];
  }
});

function init() {
    var obj = $.getQueryParameters();
    var keys = Object.keys(obj);
    if(keys.length > 0 && keys[0] != ""){

        // Reset form
        $('.form-enabler-switch input').each(function(){
            $(this).prop('checked', false);
            var row = $(this).closest('.row');
            $('input:not(input[type=checkbox])', row).prop('disabled', true);
        });

        // Init fields based on query
        for(var key in obj){
            var value = obj[key];
            if(value == 'false'){
                $('#' + key).prop('checked', false);
            }else{
                $('#' + key).val(decodeURIComponent(value).replace(/\+/g, ' '));

                var row = $('#' + key).closest('.row');
                $('input[type=checkbox]', row).prop('checked', true);
                $('input', row).prop('disabled', false);
            }
        }

        $('form').submit();
    }

    buildQueryObj();
}


$(document).ready(function(){
    init();
})
