window.$ = window.jQuery = require('jquery');
require('bootstrap');

let page = $('#page').val();

require('./components/sidebar.js');

switch (page) {
    case 'home':
        require('./pages/home.js');
        break;
    case 'languages':
        require('./pages/languages.js');
        break;
    case 'pages':
        require('./pages/pages.js');
        break;
    case 'page':
        require('./pages/page.js');
        break;
    default:
        console.log('error');
}