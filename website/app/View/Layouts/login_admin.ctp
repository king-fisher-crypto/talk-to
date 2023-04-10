<!DOCTYPE html>
<html>
<head>
    <?php echo $this->Html->charset(); ?>
    <title><?php echo __('Administration ').Configure::read('Site.name'); ?></title>
    <?php
        echo $this->Html->meta('icon');
        echo $this->Html->css('/assets/plugins/bootstrap/css/bootstrap.min.css');
        echo $this->Html->css('/assets/plugins/bootstrap/css/bootstrap-responsive.min.css');
        echo $this->Html->css('/assets/css/style-metro.css');
        echo $this->Html->css('/assets/css/themes/default.css');
        echo $this->Html->css('/theme/default/css/admin');

        echo $this->fetch('meta');
        echo $this->fetch('css');
        
    ?>
  
</head>
<body class="page-login">
    <div class="page-container">
        <div class="container-fluid">
            <?php echo $this->Session->flash(); ?>
            <?php echo $this->fetch('content'); ?>
        </div>
	</div>

    <?php
    echo $this->Html->script('/assets/plugins/jquery-1.10.1.min.js');
    echo $this->Html->script('/assets/plugins/jquery-migrate-1.2.1.min.js');
    echo $this->Html->script('/assets/plugins/jquery-ui/jquery-ui-1.10.1.custom.min.js');
    echo $this->Html->script('/assets/plugins/bootstrap/js/bootstrap.min.js');
    ?>

</body>
</html>