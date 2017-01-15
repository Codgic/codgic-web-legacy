require('../js/hljs');

let codeTheme = nightMode ? 'midnight' : 'eclipse',
    cmConfig = window.editorConfig;
$('.btn-submit').click(function() {
    if (!user.logined)
    {
        $('#alert_error').html('<i class="fa fa-fw fa-remove"></i> Please login first...').fadeIn();
        setTimeout(function() { $('#alert_error').fadeOut(); }, 2000);
        return;
    }
    let emptyFunction = () => { };
    let focusEditor, clearEditor, changeMode;
    if (!window.editorLoaded)
    {
        if (cmConfig.enabled)
        {
            require.ensure([], function () {
                require('codemirror/lib/codemirror.css');
                require('codemirror/theme/' + codeTheme + '.css');
                require('codemirror/addon/display/fullscreen.css');
                require('codemirror/addon/display/placeholder');
                require('codemirror/addon/display/fullscreen');
                require('codemirror/mode/clike/clike');
                require('codemirror/mode/pascal/pascal');
                require('../js/codemirror-basic');
                if (cmConfig.mode != 'default')
                    require('codemirror/keymap/' + cmConfig.mode);

                let editorParameter = {
                    theme: codeTheme,
                    lineNumbers: true,
                    extraKeys: {
                        "F11": function (cm) {
                            if(cm.getOption("fullScreen")) {
                                toggle_fullscreen(1);
                                cm.setOption("fullScreen",false);
                            } else {
                                toggle_fullscreen(0);
                                cm.setOption("fullScreen", !cm.getOption("fullScreen"));
                            }
                        },
                    },
                };
                if (cmConfig.mode != 'default')
                {
                    editorParameter.keyMap = cmConfig.mode;
                    editorParameter.showCursorWhenSelecting = true;
                }

                let codeMirror = require('codemirror');
                let editor = window.editor = codeMirror.fromTextArea(document.getElementById('detail_input'), editorParameter);

                clearEditor = function() {
                    editor.getDoc().setValue('');
                };
                changeMode = function () {
                    var m = $("#slt_lang").val();
                    if(m == 1)
                        editor.setOption("mode", "text/x-csrc");
                    else if(m == 2)
                        editor.setOption("mode", "text/x-pascal");
                    else if (m == 6)
                        editor.setOption("mode", "text/x-basic");
                    else
                        editor.setOption("mode", "text/x-c++src");
                };
                focusEditor = () => { editor.refresh(); editor.focus(); };
                setTimeout(() => { focusEditor(); changeMode(); }, 100);
            });
        }
        else
        {
            let input = $('#detail_input');
            focusEditor = function() { input.focus(); };
            clearEditor = function () {
                input.val('');
            };
            changeMode = function () { };
        }

        $('#btn_clear').click(function() {
            clearEditor();
            focusEditor();
        });
        window.editorLoaded = true;
    }

    let modal = $('#SubmitModal');
    modal.modal('show');
});
