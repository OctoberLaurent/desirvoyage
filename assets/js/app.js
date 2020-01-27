/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../css/app.scss';
import 'materialize-css/sass/materialize.scss';
import 'materialize-css/dist/js/materialize.js';
import '@ciar4n/izmir/izmir.min.css';


// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
const $ = require('jquery');
global.$ = global.jQuery = $;

/* select login for birthday and country */
$(document).ready(function(){
    $('select').formSelect();
  });
  
console.log('Hello Webpack Encore! Edit me in assets/js/app.js');
