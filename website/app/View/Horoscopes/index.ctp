<div class="slider-logged hidden-xs">
		<h1 class="wow fadeInDown" data-wow-delay="0.5s"><?php echo __('Horoscope du jour gratuit'); ?></h1>
</div>
<div class="container">
	<div class="page mt20 mb40">
		<div class="row">
			<div class="col-sm-12 col-md-9 ">
				<div class="horoscope_info_index">	
					<?php
					
					$description = $this->FrontBlock->getPageBlocTexte(338);
					$desc_suite = '';
                    if (strpos($description, '<!--PAGEBREAK-->') !== false){
						 $description = str_replace('<!--PAGEBREAK--></p>','</p><!--PAGEBREAK-->',$description);
                        $description = explode('<!--PAGEBREAK-->', $description);
						$desc_suite = $description['1'];
                        $description = $description['0'];
						
                    }else{
                        $description = Tools::texte_resume_html($description, 410);
                    }

					echo $description.'</div></div>';	
					// echo $this->Html->link(__('Lire la suite...'), '#', array('title' => 'Lire la suite', 'class' => 'saymore show_hide show_btn'));
					//echo '<div class="slidingDiv">&nbsp;</div>';
					//echo $this->Html->link(__('Fermer'), '#', array('title' => 'Fermer', 'class' => 'saymore_close show_hide hide_btn', ));
					
					
					 ?>	
				</div>
					<div class="content_box " style="padding-top:10px;visibility: visible;margin-bottom: 0px;padding-bottom:0px;">
			
							
				<h2 class="" style="text-align:center;"><?php echo __('Accédez à votre horoscope du jour gratuit et complet');
					$month = array(
				'01' => 'janvier',
				'02' => 'février',
				'03' => 'mars',
				'04' => 'avril',
				'05' => 'mai',
				'06' => 'juin',
				'07' => 'juillet',
				'08' => 'aout',
				'09' => 'septembre',
				'10' => 'octobre',
				'11' => 'novembre',
				'12' => 'décembre'
			);
					
					echo ' du '.date("d").' ';
					echo $month[date("m")].' ';
					echo date("Y");
					?></h2>
				
				<?php /*	
					<div class="h2 inlinetext">du <?php
					
					$month = array(
				'01' => 'janvier',
				'02' => 'février',
				'03' => 'mars',
				'04' => 'avril',
				'05' => 'mai',
				'06' => 'juin',
				'07' => 'juillet',
				'08' => 'aout',
				'09' => 'septembre',
				'10' => 'octobre',
				'11' => 'novembre',
				'12' => 'décembre'
			);
					
					echo date("d").' ';
					echo $month[date("m")].' ';
					echo date("Y"); ?></div> */ ?>
				<!--<h3 class="wow fadeIn animated" data-wow-delay="0.6s" style="visibility: visible;-webkit-animation-delay: 0.6s; -moz-animation-delay: 0.6s; animation-delay: 0.6s;"><?php echo __('Veuillez sélectionner votre signe.'); ?></h3>
-->
				<div class="horoscope-page mt0 mb0">
					<ul class="text-center row" style="margin-bottom:0px;">
                    	 <?php 
						 $num = 1;
						 foreach($signs as $id => $name) : ?>
                         	<li class="col-sm-3 col-xs-6 wow fadeIn" data-wow-delay="0.1s">
                                <a href="<?php /*echo $this->Html->url(
                                                                    array(
                                                                        'controller' => 'horoscopes',
                                                                        'action' => 'display',
                                                                        'language' => $this->Session->read('Config.language'),
                                                                        'seo_word' => $id
                                                                    )); */
                                                                    
                                                    echo '/'.$this->Session->read('Config.language').'/horoscope-du-jour/'.$id;			
                                                                    ?>" class="hor-full" title="Horoscope du jour <?php echo $name; ?>"><figure><span class="img-logo"><img src="/theme/default/img/horoscope/<?php echo $num; ?>.png" alt="Horoscope du jour <?php echo $name; ?>" ></span><figcaption class="hor-name"><?php echo $name; ?></figcaption></figure></a>
                            </li>
                        <?php 
						$num++;
						endforeach; ?>
					</ul>
                   
				</div><!--horoscope-page END-->
				</div>
				<div class=" horo-subscribe">
				<p><?php echo __('Ne commencez plus votre journée sans lire votre horoscope, <b>c\'est gratuit !</b>'); ?></p>
				<?php echo $this->Form->create('Horoscopes', array('nobootstrap' => 1,'class' => 'form-horo', 'default' => 1,
														  'inputDefaults' => array(
															  'div' => '',
															 
															  'class' => 'form-control'
														  )
										));

						echo $this->Form->input('email', array(
											'label' => '',
											'value' => '',
											'after' => '',
											'placeholder' => 'VOTRE EMAIL',
											'type'  => 'text'
										));
									echo $this->Form->input('firstname', array(
											'label' => '',
											'value' => '',
											'after' => '',
											'placeholder' => 'VOTRE PRENOM',
											'type'  => 'text'
										));
									
										echo $this->Form->end(array('label' => __('s\'inscrire'), 'class' => 'btn btn-horo-subscribe', 'before' => '', 'after' => '')); ?>
				</div>	
				<div class="horoscope_info_index">	
					<?php
					
					$description = $this->FrontBlock->getPageBlocTexte(338);
					$desc_suite = '';
                    if (strpos($description, '<!--PAGEBREAK-->') !== false){
						 $description = str_replace('<!--PAGEBREAK--></p>','</p><!--PAGEBREAK-->',$description);
                        $description = explode('<!--PAGEBREAK-->', $description);
						$desc_suite = $description['1'];
                        $description = $description['0'];
						
                    }
				
				echo '<div><div>'.$desc_suite;	
					 ?>	
				</div>
			</div>
			<?php
			echo $this->Frontblock->getRightSidebar(null,'Nos astrologues experts');
			?>
		</div>
	</div>
</div><!--container END-->