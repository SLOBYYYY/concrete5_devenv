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

    <nav class="container navbar navbar-default">
          <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
              <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="<?= Config::get('concrete.seo.canonical_url') ?>"><?= Config::get('app.contact_details.business_name') ?></a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
              <ul class="nav navbar-nav">
                <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Courses <span class="caret"></span></a>
                  <ul class="dropdown-menu">
                    <li><a href="/acls-certification">ACLS Certification</a></li>
                    <li><a href="/acls-recertification">ACLS Recertification</a></li>
                    <li role="separator" class="divider"></li>
                    <li><a href="/bls-certification">BLS Certification</a></li>
                    <li><a href="/bls-recertification">BLS Recertification</a></li>
                    <li role="separator" class="divider"></li>
                    <li><a href="/pals-certification">PALS Certification</a></li>
                    <li><a href="/pals-recertification">PALS Recertification</a></li>
                    <li role="separator" class="divider"></li>
                    <li><a href="/nrp-certification">NRP Certification</a></li>
                    <li><a href="/nrp-recertification">NRP Recertification</a></li>
                  </ul>
                </li>
                <li><a href="/group-orders">Group Orders <span class="sr-only">(current)</span></a></li>
                <li><a href="/faq">FAQ's</a></li>
                <li><a href="/contact">Contact</a></li>
              </ul>
              <ul class="nav navbar-nav navbar-right">
              <li><a href="/member">My Account</a></li>
              </ul>
              <form class="navbar-form navbar-right" action="/search-results" method="get">
                <div class="form-group">
                  <input type="text" class="form-control" name="query" placeholder="Search">
                </div>
                <button type="submit" class="btn btn-default">Search</button>
              </form>
            </div><!-- /.navbar-collapse -->
          </div><!-- /.container-fluid -->
        </nav>
        