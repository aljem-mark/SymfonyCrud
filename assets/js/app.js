import Vue from 'vue';
import axios from 'axios';
import BootstrapVue from 'bootstrap-vue';

Vue.use(BootstrapVue);
Vue.prototype.$axios = axios;

const routes = require('../../web/js/fos_js_routes.json');
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

Routing.setRoutingData(routes);
Vue.prototype.$routing = Routing;

require('@fortawesome/fontawesome-free/js/all.js');
require('../css/app.scss');

const $ = require('jquery');
// this "modifies" the jquery module: adding behavior to it
// the bootstrap module doesn't export/return anything
require('bootstrap');

require('../bootstrap-editable/js/bootstrap-editable.js');
require('../bootstrap-editable/scss/bootstrap-editable.scss');

import Users from './components/user/index'
import Register from './components/registration/register'
import UserEdit from './components/user/edit'
import UserModal from './components/registration/modal'

new Vue({
    el: '#app',
    components: {
        Users,
        Register,
        UserEdit
    }
})

new Vue({
    el: '#app-user-modal',
    components: {
        UserModal
    }
})

var userData;

function toggleEditable(el) {
    $(el).closest('tr').find('.apply-xeditable').editable('toggleDisabled');
}

function getUserData(url)
{
    var returnVal;
    $.ajax({
        type: 'post',
        url: url
    })
    .done(function (data) {
        if (typeof data.user !== 'undefined')
        {
            setOnepageFormData(data.user);
        }
    })
    .fail(function (data, textStatus, errorThrown)
    {
    });
}

function setOnepageFormData(data)
{
    userData = data;
    onepageForm = document.forms['onepage-edit'];
    for (const name in userData) {
        if (userData.hasOwnProperty(name)) {
            if( name == 'id' )
            {
                onepageForm.elements[name].value = userData[name];
            }
            else
            {
                onepageForm.elements['appbundle_user[' + name + ']'].value = userData[name];
            }
        }
    }
}

$(document).ready(function() {
    $('[data-toggle="popover"]').popover();

    // set nav-item to active
    $('.nav-item.active').removeClass('active');
    $('.nav-link[href="' + location.pathname + '"]').closest('.nav-item').addClass('active');

    // START: Onepage Edit
    $(document).on('click', '.onepage-edit', function(e) {
        getUserData($(this).attr('data-href'));
    });

    $('#onepage-edit').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            type: $(this).attr('method'),
            url: $(this).attr('action'),
            data: $(this).serialize()
        })
        .done(function (data) {
            if (typeof data.message !== 'undefined')
            {
                $('#onepage-card .form_error').html("<div class=\"alert alert-success\">" + data.message + "</div>");
            }
            
            $('#onepage-card').animate({ scrollTop: 0 }, 'slow');
    
            setTimeout(function(){ document.location.reload(true) }, 2000);
        })
        .fail(function (data, textStatus, errorThrown)
        {
            if (typeof data.responseJSON !== 'undefined') {
                $('#onepage-card .form_error').html(data.responseJSON.message);
            } else {
                alert(errorThrown);
            }
            
            $('#onepage-card').animate({ scrollTop: 0 }, 'slow');
        });
    });
    // END: Onepage Edit

    // START: X-EDITABLE
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
    // END: X-EDITABLE

    // Registration Modal Save AJAX
    $('#registration-modal-save').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            type: $(this).attr('method'),
            url: $(this).attr('action'),
            data: $(this).serialize()
        })
        .done(function (data) {
            if (typeof data.message !== 'undefined') {
                $('.form_error').html("<div class=\"alert alert-success\">" + data.message + "</div>");
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