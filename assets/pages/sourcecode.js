let codeTheme = nightMode ? 'midnight' : 'eclipse',
    cmConfig = window.editorConfig;
$(document).ready(function() {
    let emptyFunction = () => { };
    let focusEditor, clearEditor, changeMode;
    if (cmConfig.enabled)
    {
        require.ensure([], function () {
            require('codemirror/addon/display/fullscreen');
            require('codemirror/lib/codemirror.css');
            require('codemirror/addon/display/fullscreen.css');
            require('codemirror/theme/' + codeTheme + '.css');
            require('codemirror/mode/clike/clike');
            require('codemirror/mode/pascal/pascal');
            require('../js/codemirror-basic');

            let editorParameter = {
                theme: codeTheme,
                lineNumbers: true,
                readOnly: 'nocursor',
                viewportMargin: Infinity,
                mode: textMode
            };

            let codeMirror = require('codemirror');
            let editor = window.editor = codeMirror.fromTextArea(document.getElementById('text_code'), editorParameter);

            let toggleFullScreen = function() {
                editor.setOption("fullScreen", !editor.getOption("fullScreen"));
            };
            $("#btn_fullscreen").click(toggleFullScreen);
            editor.setOption("extraKeys", { "F11": toggleFullScreen });
        });
    }
});
