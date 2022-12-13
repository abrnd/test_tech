/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// start the Stimulus application
import './bootstrap';

const $ = require('jquery');

$(document).ready(function(){

    $('.status').change(function(){

        console.log($(this));
        console.log(this);
        console.log($(this).val());

        const url = "/status"

        const status_id = $(this).val();
        const request_id = $(this).data("request");

        const data = {
            status: status_id,
            request: request_id
        }

        $.ajax({
            method: 'POST',
            url: url,
            data: data
        })
    })

})