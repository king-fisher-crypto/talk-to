<!DOCTYPE html>
<html>
<head>
    <?php echo $this->Html->charset(); ?>
    <title><?php echo __('Administration ').Configure::read('Site.name'); ?></title>
    <?php
        echo $this->Html->meta('icon');
        echo $this->Html->css('/assets/plugins/bootstrap/css/bootstrap.min.css');
        echo $this->Html->css('/assets/plugins/bootstrap/css/bootstrap-responsive.min.css');
        echo $this->Html->css('/assets/plugins/font-awesome/css/font-awesome.min.css');
        echo $this->Html->css('/assets/css/style-metro.css');
        echo $this->Html->css('/assets/css/style.css');
        echo $this->Html->css('/assets/css/style-responsive.css');
        echo $this->Html->css('/assets/css/themes/default.css');
        echo $this->Html->css('/assets/plugins/uniform/css/uniform.default.css');
        echo $this->Html->css('/assets/plugins/select2/select2_metro.css');
        echo $this->Html->css('/assets/css/pages/login.css');
        echo $this->Html->css('/theme/default/css/admin');

	if(in_array($this->params['controller'], array('category', 'pages', 'horoscopes', 'admins', 'subscribes', 'support'))){
            echo $this->Html->css('/theme/default/css/tinymce');
        }

        echo $this->fetch('meta');
        echo $this->fetch('css');
        
    ?>
  
</head>
<body class="page-header-fixed">
    <div class="header navbar navbar-inverse navbar-fixed-top">
        <div class="navbar-inner">
            <div class="container-fluid">
                <?php

                echo $this->Html->link("Logo", array(
                    'controller' => 'admins',
                    'action' => 'index',
                    'admin' => true
                ), array(
                    'class' => 'brand',
                    'style' => 'background-size: 100% auto'
                ));

                ?>

                <ul class="nav pull-right">
                    <li class="dropdown user">
                        <a data-close-others="true" data-hover="dropdown" data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <span class="username"><?php echo $current_user['firstname'];?> <?php echo $current_user['lastname'];?></span>
                        <i class="icon-angle-down"></i>
                        </a>
                        <ul class="dropdown-menu">
                         
                            <li><a href="<?php echo $this->Html->url(array('controller' => 'users', 'action' => 'logout', 'admin' => false, '?' => 'adminLogout')); ?>"><i class="icon-key"></i> <?php echo __('Déconnexion');?></a></li>
                        </ul>
                    </li>
                </ul>
                <div class="hidden-phone">
                <?php
					 echo $this->Html->link(
                        __('G. Doc'),
                        'https://docs.google.com/spreadsheets/d/1qxh8SVG2qOkzoYaHtFQfVsCO4M6EjN9BDEAuktyhb7s/edit#gid=0',
                        array(
                            'class'     => 'btn blue pull-right',
                            'target'    => '_blank',
                            'escape'    => false
                        )
                    );
					 echo $this->Html->link(
                        __('G. Agenda'),
                        'https://www.google.com/calendar',
                        array(
                            'class'     => 'btn yellow pull-right',
                            'target'    => '_blank',
                            'escape'    => false
                        )
                    );
					
					$list_master = array(1,323,191,198,204,495);
					if( in_array($current_user['id'],$list_master)  ){
					
					 echo $this->Html->link(
                        __('Daotec'),
                        'http://ivr.daotec.com/ivrcc/text.svc?file=frameset.html',
                        array(
                            'class'     => 'btn red pull-right',
                            'target'    => '_blank',
                            'escape'    => false
                        )
                    );
                   
                    echo $this->Html->link(
                        __('G. Analytics'),
                        'https://www.google.com/analytics',
                        array(
                            'class'     => 'btn purple pull-right',
                            'target'    => '_blank',
                            'escape'    => false
                        )
                    );
                    echo $this->Html->link(
                        __('G. Gmail'),
                        'https://mail.google.com/mail/',
                        array(
                            'class'     => 'btn black pull-right',
                            'target'    => '_blank',
                            'escape'    => false
                        )
                    );
                   
						
					echo '<a class="btn purple pull-right" href="http://www.talkappdev.com/affiliate/" target="_blank">Affiliation</a>';
					echo '<a class="btn red pull-right" href="https://www.paypal.com/mep/dashboard" target="_blank">Paypal</a>';
					echo '<a class="btn blue pull-right" href="https://dashboard.stripe.com/dashboard" target="_blank">Stripe</a>';
					echo '<a class="btn black pull-right" href="https://clicky.com/user/" target="_blank">Clicky</a>';
					echo '<a class="btn yellow pull-right" href="https://www.clickcease.com/dashboard/#/dashboard	" target="_blank">ClickCease</a>';
					echo '<a class="btn purple pull-right" href="https://trello.com/b/gO1W1Ok2/spiriteo-d%C3%A9veloppement" target="_blank">Trello</a>';
					
						
					}
					
                    echo $this->Html->link(
                        __('Voir le site'),
                        array(
                            'controller'    => 'home',
                            'action'        => 'index',
                            'admin'         => false
                        ),
                        array(
                            'class'     => 'btn green pull-right margin-right-50',
                            'target'    => '_blank',
                            'escape'    => false
                        )
                    );
                ?>
                </div>
            </div>
        </div>
    </div>
    <div class="page-container">
        <div class="page-sidebar nav-collapse collapse">
            <?php echo $this->Metronic->renderSidebar($this->Metronic->getElementSidebar($badge)); ?>
        </div>
        <div class="page-content">
            <div class="container-fluid">
            	<?php echo $this->Session->flash(); ?>
                <?php echo $this->fetch('content'); ?>
            	<?php echo ''/*$this->element('sql_dump')*/; ?>
        	</div>
    	</div>
	</div>

    <!-- END JAVASCRIPTS -->
    <?php
    echo $this->Html->script('/assets/plugins/jquery-1.10.1.min.js');
    echo $this->Html->script('/assets/plugins/jquery-migrate-1.2.1.min.js');
    echo $this->Html->script('/assets/plugins/jquery-ui/jquery-ui-1.10.1.custom.min.js');
    echo $this->Html->script('/assets/plugins/bootstrap/js/bootstrap.min.js');
    echo $this->Html->script('/assets/plugins/bootstrap-hover-dropdown/twitter-bootstrap-hover-dropdown.min.js');
    echo $this->Html->script('/assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js');
    echo $this->Html->script('/assets/plugins/jquery.blockui.min.js');
    echo $this->Html->script('/assets/plugins/jquery.cookie.min.js');
    echo $this->Html->script('/assets/plugins/uniform/jquery.uniform.min.js');
    echo $this->Html->script('/assets/scripts/app.js');
    echo $this->Html->script('/assets/scripts/index.js');
    echo $this->Html->script('/assets/scripts/tasks.js');
    echo $this->Html->script('/theme/default/js/admin_main.js?'.date('YmdH'));

	if(in_array($this->params['controller'], array('category', 'pages', 'horoscopes', 'landings', 'admins', 'subscribes','support','cards'))){
        echo $this->Html->script('/theme/default/js/tinymce/tinymce.min');
        echo $this->Html->script('/theme/default/js/nx_tinymce');
    }

    echo $this->fetch('script');
    ?>

    <script type="text/javascript">
        $(document).ready(function() {
            App.init(); // initlayout and core plugins
            Index.init();
            //Index.initJQVMAP(); // init index page's custom scripts
            Index.initCalendar(); // init index page's custom scripts
            Index.initCharts(); // init index page's custom scripts
            Index.initChat();
            //Index.initMiniCharts();
            //Index.initDashboardDaterange();
            Index.initIntro();
            Tasks.initDashboardWidget();
        });
    </script>


</body>
</html>