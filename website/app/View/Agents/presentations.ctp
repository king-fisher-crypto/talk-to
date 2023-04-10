<?php  echo $this->Html->script('/theme/default/js/disabledButtonSubmit', array('block' => 'script')); ?>
<section class="slider-small">
		<h1 class="wow fadeInDown" data-wow-delay="0.5s"><?php echo __('Mon compte') ?></h1>
</section>
 <div class="container">

		<section class="page profile-page mt20 mb40">
			<div class="row">
				<div class="col-sm-12 col-md-9">
					<div class="content_box mobile-center wow mb20 fadeIn" data-wow-delay="0.4s">

					<div class="page-header">
						<h2 class="uppercase wow fadeIn" data-wow-delay="0.3s"><?php echo __('Ma présentation') ?></h2>
						<?php
							echo $this->Session->flash();
							/* titre de page */
							echo $this->element('title', array(
					
								'breadcrumb' => array(
									0   =>  array(
										'name'  =>  'Accueil',
										'link'  =>  Router::url('/',true)
									),
									1 => array(
										'name'  =>  '<span class="active">'.__('Ma présentation').'</span>',
										'class' => 'active'
									)
								)
							));
						?>

					</div><!--page-header END-->

						<div class="row">
							<div class="col-md-12 col-sm-12">
                            <ul class="nav nav-tabs">
        <?php
        $firstBoucle = true;
        foreach ($presentations AS $idLang => $val){
			if($idLang != 8  && $idLang != 9  && $idLang != 10  && $idLang != 11  && $idLang != 12 ){
            if($firstBoucle){
                $firstBoucle = false;
                ?>
                <li class="customTab active"><a href=<?php echo "#tab".$idLang; ?> data-toggle="tab"><i class="<?php echo 'lang_flags lang_'.$val['Lang']['language_code']; ?>" title="<?php echo $val['Lang']['name'] ?>"></i><?php echo __($val['Lang']['name']); ?></a></li>
            <?php }else {?>
                <li class="customTab"><a href=<?php echo "#tab".$idLang; ?> data-toggle="tab"><i class="<?php echo 'lang_flags lang_'.$val['Lang']['language_code']; ?>" title="<?php echo $val['Lang']['name'] ?>"></i><?php echo __($val['Lang']['name']); ?></a></li>
            <?php }}
        } ?>
    </ul>

    <div class="tab-content">
        <?php
            $firstBoucle = true;
            foreach ($presentations AS $idLang => $val){
				if($idLang != 8  && $idLang != 9  && $idLang != 10  && $idLang != 11  && $idLang != 12 ){
                if($firstBoucle){
                    $firstBoucle = false;
                    echo '<div class="tab-pane active" id="tab'.$idLang.'">';
                }else
                    echo '<div class="tab-pane" id="tab'.$idLang.'">';

                echo $this->Form->create('Agent', array('action' => 'editAgentPresentation', 'nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1,
                    'inputDefaults' => array(
                        'div' => 'form-group',
                        'between' => '<div class="col-lg-8">',
                        'after' => '</div>',
                        'class' => 'form-control'
                    )
                )); ?>

                <div class="form-group">
                    <label class="control-label col-lg-4 norequired"><?php echo __('Présentation actuelle'); ?></label>
                    <div class="col-lg-8">
                        <br/>
                        <p><?php echo (!empty($val['UserPresentLang']['texte'])?$val['UserPresentLang']['texte']:__('Vous n\'avez aucune présentation pour cette langue')) ?></p>
                    </div>
                </div>
                <hr/>


                <?php
                
                if(!empty($val['UserPresentValidation']['texte'])){
                    echo '<p style="text-align: center;">'. __('La présentation suivante est en attente de validation.') .'</p>';
                    echo $this->element('agent_presentations', array('lang_id' => $idLang, 'presentation' => $val['UserPresentValidation']['texte']));
                    echo '</div>';
                }
                else
                    echo $this->element('agent_presentations', array('commentaireOn' => true, 'lang_id' => $idLang, 'presentation' => (!empty($val['UserPresentLang']['texte'])?html_entity_decode($val['UserPresentLang']['texte']):'')));

                echo $this->Form->end(array('label' => __('Modifier'), 'class' => 'btn btn-pink btn-pink-modified', 'div' => array('class' => 'form-group')));
                ?>
                </div>
            <?php }}
        ?>
    </div>
                            
                            	
                            
                            
							</div><!--col-sm-12-->

						</div><!--row END-->
					</div><!--content_box END-->




				</div><!--col-9 END-->
<?php
				echo $this->Frontblock->getRightSidebar($agentStatus);
			?>
				<!--col-md-3 END-->
</div><!--row END-->
</section><!--expert-list END-->

</div>