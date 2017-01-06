// Cannot lazy load jQuery because of legacy script.
window.$ = window.jQuery = require('jquery');

require.ensure(["bootstrap"], function() {
    require('bootstrap');
});

require.ensure(["font-awesome/css/font-awesome.css"], function() {
    require('font-awesome/css/font-awesome.css');
});
// require('bootstrap/less/bootstrap.less');

import loadNightMode from './js/nightMode';
loadNightMode();
