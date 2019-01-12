// app.js
require('@fortawesome/fontawesome-free/js/all.js');
require('../css/app.scss');

const $ = require('jquery');
// this "modifies" the jquery module: adding behavior to it
// the bootstrap module doesn't export/return anything
require('bootstrap');

// or you can include specific pieces
// require('bootstrap/js/dist/tooltip');
// require('bootstrap/js/dist/popover');

function toggleEditable(el) {
    $(el).closest('tr').find('.apply-xeditable').editable('toggleDisabled');
}

$(document).ready(function() {
    $('[data-toggle="popover"]').popover();

    $(document).on('click', '.toggle-editable', function(e) {
        toggleEditable(this);
    });

    $.fn.editable.defaults.mode = 'inline';
    $.fn.editable.defaults.disabled = true;
    $.each($('.apply-xeditable'), function(index, value){
        if ($(value).hasClass('gender')) {
            $(this).editable({
                source: [
                      {value: 'm', text: 'Male'},
                      {value: 'f', text: 'Female'},
                   ],
                error: function(response, newValue) {
                    if(response.status === 500) {
                        return 'Service unavailable. Please try later.';
                    } else {
                        var errors = response.responseJSON;
                        errorsHtml = '<div class="alert alert-danger" role="alert"><h4 class="alert-heading">Error:</h4><ul>';
                        $.each( errors, function( key, value ) {
                            errorsHtml += '<li>'+ value + '</li>';
                        });
                        errorsHtml += '</ul></di>';
                        $(".editable-error-block").html(errorsHtml);
                    }
                }
            });
        } else {
            $(this).editable({
                error: function(response, newValue) {
                    if(response.status === 500) {
                        return 'Service unavailable. Please try later.';
                    } else {
                        var errors = response.responseJSON;
                        errorsHtml = '<div class="alert alert-danger" role="alert"><h4 class="alert-heading">Error:</h4><ul>';
                        $.each( errors, function( key, value ) {
                            errorsHtml += '<li>'+ value + '</li>';
                        });
                        errorsHtml += '</ul></di>';
                        $(".editable-error-block").html(errorsHtml);
                    }
                }
            });
        }
    });
});

require('../bootstrap-editable/js/bootstrap-editable.js');
require('../bootstrap-editable/scss/bootstrap-editable.scss');