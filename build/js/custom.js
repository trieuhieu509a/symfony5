window.$ = global.$ = global.jQuery = require('jquery');
// window.myvar = [123]

console.log('xxxxxxxxxxx')
$.when( $.ready ).then(function() {
   console.log('xxxxxxxxxxx')
});


$(document).ready(function () {
    console.log('yyyyyyyyyyyyyy')
});