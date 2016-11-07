<?php
require __DIR__.'/inc/init.php';
header("Content-Type:text/html;charset=utf-8");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="renderer" content="webkit">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="<?php echo _('Please upgrade your browser before accessing CWOJ.')?>">
        <link rel="shortcut icon" href="/assets/res/favicon.ico" type="image/x-icon" />
        <link rel="apple-touch-icon" href="/assets/res/ojlogo_ios.png" />
        <title><?php echo _('Upgrade your browser')?></title>
        <base target="_blank" />
        <style type="text/css">
            body,h1,h2,h3,h4,h5,h6,hr,p,blockquote,dl,dt,dd,ul,ol,li,pre,form,fieldset,legend,button,input,textarea,th,td{margin:0;padding:0}
            a{text-decoration:none;color:#0072c6;}a:hover{text-decoration:none;color:#004d8c;}
            body{width:960px;margin:0 auto;padding:10px;font-size: 18px;line-height:24px;color:#454545;font-family:'Microsoft YaHei UI','Microsoft YaHei',DengXian,SimSun,'Segoe UI',Tahoma,Helvetica,sans-serif;}
            h1{font-size:45px;line-height:80px;font-weight:200;margin-bottom:10px;}
            h2{font-size:30px;line-height:45px;font-weight:200;margin:10px 0;}
            p{margin-bottom:10px;}
            .line{clear:both;width:100%;height:1px;overflow:hidden;line-height:1px;border:0;background:#ccc;margin:20px 0 10px;}
            img{width:34px;height:34px;border:0;float:left;margin-right:10px;}
            span{display:block;font-size:18px;line-height:24px;}
            .clean{clear:both;}
            .browser{padding:10px 0;}
            .browser li{width:220px;float:left;list-style:none;}
        </style>
    </head>
    <body>
        <h1><?php echo _('It\'s time to upgrade your browser')?></h1>
        <p><?php echo _('We\'re sorry to inform you that you\'re currently using an obsolete browser which cannot guarantee an enjoyable experience in CWOJ. You have to upgrade your browser before accessing CWOJ.')?></p>
        <div class="line"></div>
        <h2><?php echo _('Modern browsers')?></h2>
        <p><?php echo _('We strongly recommend you to try out one of the following modern browsers that will show you the beauty of web.');?></p>
        <ul class="browser">
            <li><a href="https://www.microsoft.com/windows/microsoft-edge"><img src="/assets/res/edge.png" alt="Microsoft Edge" /> Microsoft Edge</a></li>
            <li><a href="https://www.google.com/chrome/browser"><img src="/assets/res/chrome.png" alt="Google Chrome" /> Google Chrome</a></li>
            <li><a href="http://www.firefox.com/download"><img src="/assets/res/firefox.png" alt="Mozilla Firefox" /> Mozilla Firefox</a></li>
            <li><a href="http://www.opera.com/download/get"><img src="/assets/res/opera.png" alt="Opera" /> Opera</a></li>
            <div class="clean"></div>
        </ul>
        <div class="line"></div>
        <h2><?php echo _('What the heck is a browser?')?></h2>
        <p><?php echo _('That make things a lot easier. You just need to close this page, because CWOJ might be highly unsuitable for you.');?></p>

        <script type="text/javascript">
            var browser=navigator.appNam,b_version=navigator.appVersion; 
            var version=b_version.split(";"); 
            var trim_Version=version[0].replace(/[ ]/g,""); 
            if(browser!="Microsoft Internet Explorer") { 
                window.location = "index.php"; 
            }else{
                if(browser == "Microsoft Internet Explorer" && trim_Version == "MSIE11.0" || trim_Version == "MSIE10.0" || trim_Version == "MSIE9.0")
                window.location = "index.php"; 
            }
            function Request(name){
                new RegExp("(^|&)"+name+"=([^&]*)").exec(window.location.search.substr(1));
                return RegExp.$2
            }
            var referrer = Request("referrer");
            var url = document.referrer;
            if(!url==""){
                x=document.getElementById("referrer")
                x.innerHTML='&nbsp;<a href="'+url+'">'+url+'</a>&nbsp;';
            }else if(!referrer==""){
                x=document.getElementById("referrer")
                x.innerHTML='&nbsp;<a href="'+referrer+'">'+referrer+'</a>&nbsp;';
            }
        </script>
    </body>
</html>
