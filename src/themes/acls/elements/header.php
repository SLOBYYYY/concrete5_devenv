<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<!DOCTYPE html>
<html lang="<?php echo Localization::activeLanguage()?>">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <?php
        //The header required element below is mandatory and provides
        // Title
        // Meta Description, Keywords, Robots
        // Various Favicons
        // Content defined within page attribute "header_extra_content"
        // File that renders this by default is /concrete/elements/header_required.php
        // If modifications are required, copy it to /application/elements/header_required.php
        
        Loader::element('header_required', array('pageTitle' => isset($pageTitle) ? $pageTitle : '', 'pageDescription' => isset($pageDescription) ? $pageDescription : '', 'pageMetaKeywords' => isset($pageMetaKeywords) ? $pageMetaKeywords : ''));
        
        ?>

	    <meta name="viewport" content="width=device-width, initial-scale=1">
		
    <!-- Bootstrap Core CSS -->
    <link href="<?php echo $view->getThemePath()?>/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom Theme CSS -->
    <link href="<?php echo $view->getThemePath()?>/css/styles.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>
<body>
    <div class="<?php echo $c->getPageWrapperClass(); //This wrapper class is required for responsive grids to work within CMS ?>">
    
    <!-- Standard Bootstrap Nav Bar Below, You can hardcode it here, or you can create a GlobalArea, Like in the footer, and use the CMS to add the top nav stuff -->
