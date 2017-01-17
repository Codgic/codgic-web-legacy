if (nightMode)
    require('highlightjs/styles/androidstudio.css');
else
    require('highlightjs/styles/xcode.css');

let hljs = require('highlightjs');
hljs.initHighlightingOnLoad();
