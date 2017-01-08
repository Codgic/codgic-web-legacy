<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="renderer" content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $oj_name, _(' - An Open-source Online Judge designed for OI trainings, built for you to code your future.');?>">
    <?php header("Content-Type:text/html;charset=utf-8");?>
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/res/apple-touch-icon.png?v=1">
    <link rel="icon" type="image/png" href="/assets/res/favicon-32x32.png?v=1" sizes="32x32">
    <link rel="icon" type="image/png" href="/assets/res/android-chrome-192x192.png?v=1" sizes="192x192">
    <link rel="icon" type="image/png" href="/assets/res/favicon-16x16.png?v=1" sizes="16x16">
    <link rel="manifest" href="/assets/res/manifest.json?v=1">
    <link rel="mask-icon" href="/assets/res/safari-pinned-tab.svg?v=1" color="#5bbad5">
    <link rel="shortcut icon" href="/assets/res/favicon.ico?v=1">
    <meta name="apple-mobile-web-app-title" content="CWOJ">
    <meta name="application-name" content="CWOJ">
    <meta name="msapplication-config" content="/assets/res/browserconfig.xml?v=1">
    <title><?php echo $Title?></title>
    <!--<script src="/assets/js/jquery.min.js?v=310"></script>
    <script src="/assets/js/bootstrap.min.js?v=337"></script>
    <link href="/assets/FontAwesome/css/font-awesome.min.css?v=470" rel="stylesheet" type="text/css" />
    -->
    <script>window.nightModeConfig = <?php echo json_encode(array('mode'=>$pref->night, 'dayStart' => $daystart, 'nightStart' => $nightstart)) ?>;</script>
    <script src="/assets_webpack/commons.js"></script>
    <script src="/assets_webpack/general.js"></script>

    <!--[if lt IE 9]>
        <script type="text/javascript">
            window.location = "fuckie.php"; 
        </script>
    <![endif]-->
    <!--[if lt IE 10]>
        <script src="/assets/js/html5.min.js"></script>
        <script src="/assets/js/respond.min.js"></script>
        <script src="/assets/js/jquery.placeholder.min.js"></script>
    <![endif]-->
</head>
