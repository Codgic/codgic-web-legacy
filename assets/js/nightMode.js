export default function() {
    const resPath = '../';
    let hour = new Date().getHours(),
        config = document.nightMode,
        nightMode;

    if (config.mode == 'on')
        nightMode = true;
    else if (config.mode == 'off')
        nightMode = false;
    else if (hour < config.dayStart || hour > config.nightStart)
        nightMode = true;
    else
        nightMode = true;

    if (nightMode)
    {
        require('bootswatch/slate/bootstrap.css');
        require('../css/docs_dark.css');
    }
    else
    {
        require('bootswatch/cerulean/bootstrap.css');
        require('../css/docs.css');
    }
};
