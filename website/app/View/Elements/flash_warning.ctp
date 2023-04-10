<div class="alert alert-warning alert-dismissable" data-logo="<?php echo '/'.Configure::read('Site.pathLogo').'/default.jpg' ;?>" data-site="<?php echo Configure::read('Site.name'); ?>">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <p class="flash-txt"><span class="glyphicon glyphicon-warning-sign flash-icon"></span><?php echo h($message); ?></p>
    <?php if(isset($link) && isset($messageLink)){ ?>
        <p class="flash-txt"><a href="<?php echo $link; ?>" class="alert-link"><?php echo $messageLink; ?></a></p>
    <?php } ?>
</div>