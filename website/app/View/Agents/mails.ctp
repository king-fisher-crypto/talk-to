<section class="slider-small">
		<h1 class="wow fadeInDown" data-wow-delay="0.5s"><?php echo __('Ma messagerie') ?></h1>
	</section>
   <?php
    echo $this->Html->script('/theme/default/js/message_new', array('block' => 'script'));
?> <div class="container">

		<section class="page profile-page mt20 mb40">
			<div class="row">
				<div class="col-sm-12 col-md-9">
					<div class="content_box mobile-center wow mb0 fadeIn" data-wow-delay="0.4s">
<?php

$titre = 'Consultation email';
 if(isset($this->params->query['private']) && $this->params->query['private'])
 	$titre= 'Mes messages privés';
    ?>



					<div class="page-header">
						<h2 class="uppercase wow fadeIn" data-wow-delay="0.3s">  <?php echo $titre; ?></h2>
						 <?php
						echo $this->Session->flash();
						/* titre de page */
						echo $this->element('title', array(
							'breadcrumb' => array(
								0   =>  array(
									'name'  =>  'Accueil',
									'link'  =>  Router::url('/',true)
								),
								1   =>  array(
									'name'  =>  $titre,
									'link'  =>  ''
								)
							)
						));
		?>

					</div><!--page-header END-->
<?php
if(isset($this->params->query['private']) && $this->params->query['private']){
	
		echo '<div style="background: #8777b4;padding:18px 10px 10px 10px;color:#fff;margin-bottom: 20px;text-align:center">';
	 echo '<p><b>'.__('Vous ne pouvez répondre aux messages privés qu\'une fois tous les 30 jours par client.').'</b></p>';
	
            echo '<p>'.__('Les messages privés ne sont pas facturés aux consultants.').'</p>';
						
					echo '</div>';
}
?>
					</div><!--content_box END-->
                    
					<div class="row">
							<div class="col-md-12 col-sm-12">
							<div class="content_box mb20 wow fadeIn" data-wow-delay="0.4s">
							  <!-- Nav tabs -->
							  <div class="head-tabs">

							  <ul class="nav nav-tabs nav-justified account-tabs" role="tablist" rel="<?php echo $this->Html->url(array('controller' => 'agents', 'action' => 'getMails', 'page' => (isset($this->params->query['page']) ?$this->params->query['page']:1))); ?>" update="<?php echo $this->Html->url(array('controller' => 'agents', 'action' => 'update_mail')); ?>" user="<?php echo $this->Session->read('Auth.User.id'); ?>">
                                      <?php if(!isset($this->params->query['private'])): ?>
            <li role="presentation"  class="customTab singl-line active mailConsult" param="message"><a href="#tab1" data-toggle="tab"><span class="glyphicon glyphicon-inbox"></span> <?php echo __('Consultations mail'); ?><?php echo ($dataNoRead['mailConsult'] ?' <i class="margin_left_5 glyphicon glyphicon-exclamation-sign"></i>':''); ?></a></li>
        <?php else: ?>
            <li role="presentation"  class="customTab singl-line active mailPrivate" param="private"><a href="#tab2" data-toggle="tab"><span class="glyphicon glyphicon-envelope"></span> <?php echo __('Messages privés'); ?><?php echo ($dataNoRead['mailPrivate'] ?' <i class="margin_left_5 glyphicon glyphicon-exclamation-sign"></i>':''); ?></a></li>
        <?php endif; ?>
        <li role="presentation"  class="customTab singl-line" param="archive"><a href="#tab3" data-toggle="tab"><span class="glyphicon glyphicon-folder-close"></span> <?php echo __('Messages archivés'); ?></a></li>

							  </ul>
							  </div>

							  <div class="clearifix"></div>
							  <!-- Tab panes -->
                               <div class="tab-content">
                                <?php echo $this->element('mails', array('controller' => 'agents', 'idMail' => $idMail)); ?>
								</div><!--tabs-data END-->



			</div><!--content-box END-->

							
							</div><!--col-sm-12-->

						</div><!--row END-->


				</div><!--col-9 END-->
<?php
				echo $this->Frontblock->getRightSidebar($agentStatus);
			?>
				<!--col-md-3 END-->
</div><!--row END-->
</section><!--expert-list END-->

</div>