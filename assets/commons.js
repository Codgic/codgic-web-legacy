import jQuery from 'jquery';

window.$ = window.jQuery = jQuery;

require('bootstrap');
// require('bootstrap/less/bootstrap.less');
require('font-awesome/css/font-awesome.css');

import loadNightMode from './js/nightMode';
loadNightMode();
