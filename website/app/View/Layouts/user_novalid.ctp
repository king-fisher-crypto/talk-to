<!DOCTYPE html>
<html>
<head>
    <?php echo $this->Html->charset(); ?>
    <title><?php echo $site_vars['meta_title']; ?></title>
    <?php

        if (!empty($site_vars['meta_keywords'])) echo $this->Html->meta('keywords', substr($site_vars['meta_keywords'],0,Configure::read('Site.lengthMetaKeywords')));
        if (!empty($site_vars['meta_description'])) echo $this->Html->meta('description', substr($site_vars['meta_description'],0,Configure::read('Site.lengthMetaDescription')));

        echo $this->Html->meta('icon');

        echo $this->Html->meta(array('name' => 'viewport', 'content' => 'width=device-width, initial-scale=1.0'), NULL, array('inline' => false));
        echo $this->Html->meta(array('name' => 'og:type', 'content' => 'website'), NULL, array('inline' => false));
        echo $this->Html->meta(array('name' => 'og:title', 'content' => $site_vars['meta_title']), NULL, array('inline' => false));
        echo $this->Html->meta(array('name' => 'viewport', 'content' => 'width=996px, user-scalable=yes'), NULL, array('inline' => false));

    /*
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
    */


    echo $this->Html->css('/theme/default/css/reset.css');
    echo $this->Html->css('/btstrap/css/bootstrap.min.css');


    if (isset($this->params['prefix']))
        echo $this->Metronic->getCssJsLinks();
    else{
        /* Site normal */
        echo $this->Html->css('/theme/default/css/grid.css');
        echo $this->Html->css('/theme/default/css/main.css');
        echo $this->Html->css('/theme/default/css/tabs.css');
        echo $this->Html->css('/theme/default/css/chat');
        echo $this->Html->css('/theme/default/css/accounts.css');
    }

    if ($this->params['controller'] === 'category'){
        echo $this->Html->css('/theme/default/css/category.css');
    }

    if (isset($site_vars['css_links']))
        foreach ($site_vars['css_links'] AS $css)
            echo $this->Html->css($css);


    echo $this->Html->script('/js/jquery-2.0.3.min.js');
    echo $this->Html->script('/btstrap/js/bootstrap.min.js');
    echo $this->Html->script('/theme/default/js/main.js');
    echo $this->Html->script('/theme/default/js/chat');

    echo $this->fetch('meta');
    echo $this->fetch('css');
    echo $this->fetch('script');


    ?>

    <?php if (isset($this->params['prefix'])){ ?>
        <!--[if lt IE 9]>
        <script src="/assets/plugins/excanvas.min.js"></script>
        <script src="/assets/plugins/respond.min.js"></script>
        <![endif]-->
    <?php } ?>
</head>
<body class="login">
    <div id="fb-root"></div>
    <script>(function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/fr_FR/sdk.js#xfbml=1&version=v2.0";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>

<?php $userRole = $this->Session->read('Auth.User.role'); ?>

<div id="header" class="container_12">
    <?php echo ($userRole === 'agent' || $userRole === 'client' ?'<div class="container_fixed"><div class="div-a-center">':''); ?>
    <div id="track_header1" class="grid_12">
        <h1><?php echo $this->FrontBlock->getLogo(); ?></h1>
        <div id="user_block"><?php echo $this->FrontBlock->getHeaderUserBlock(); ?></div>
        <div id="flags_block">
            <div id="languages"><span><?php echo __('Langue').' : '; ?></span><?php echo $this->FrontBlock->getHeaderLangBlock(); ?></div>
            <div id="countries"><?php echo $this->FrontBlock->getHeaderCountryBlock(); ?></div>
        </div>
    </div>
    <?php echo ($userRole === 'agent' || $userRole === 'client' ?'</div></div>':''); ?>
    <div id="track_header2" class="grid_12<?php echo ($userRole === 'agent' || $userRole === 'client' ?' menu-fixed':''); ?>">
        <div id ="navigation">
            <?php echo $this->FrontBlock->getNavigation(); ?>
        </div>
    </div>
</div>

<div id="site" class="container_12">
    <?php echo $this->Session->flash(); ?>
    <?php echo $this->fetch('content'); ?>
</div>

<?php echo $this->element('footer'); ?>

</body>
</html>
