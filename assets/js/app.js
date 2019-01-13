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

    $('#registration-modal-save').on('submit', function(e) {

        e.preventDefault();
        $.ajax({
            type: $(this).attr('method'),
            url: $(this).attr('action'),
            data: $(this).serialize()
        })
        .done(function (data) {
            if (typeof data.message !== 'undefined') {
                $('.form_error').html("<div class=\"alert alert-success mb-0\">" + data.message + "</div>");
            }
            
            $('.modal').animate({ scrollTop: 0 }, 'slow');

            setTimeout(function(){ document.location.reload(true) }, 2000);
        })
        .fail(function (data, textStatus, errorThrown) {
            if (typeof data.responseJSON !== 'undefined') {
                $('.form_error').html(data.responseJSON.message);
            } else {
                alert(errorThrown);
            }
            
            $('.modal').animate({ scrollTop: 0 }, 'slow');
        });
    });
});

require('../bootstrap-editable/js/bootstrap-editable.js');
require('../bootstrap-editable/scss/bootstrap-editable.scss');