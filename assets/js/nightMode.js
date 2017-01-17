export default function() {
    const resPath = '../';
    let hour = new Date().getHours(),
        config = window.nightModeConfig,
        nightMode;

    if (config.mode == 'on')
        nightMode = true;
    else if (config.mode == 'off')
        nightMode = false;
    else if (hour < config.dayStart || hour > config.nightStart)
        nightMode = true;
    else
        nightMode = false;

    window.nightMode = nightMode;

    if (nightMode)
    {
        require('bootswatch/slate/bootstrap.css');
        require('../css/docs_dark.css');
        require('codemirror/theme/midnight.css');
    }
    else
    {
        require('bootswatch/cerulean/bootstrap.css');
        require('../css/docs.css');
        require('codemirror/theme/eclipse.css');
    }
};
