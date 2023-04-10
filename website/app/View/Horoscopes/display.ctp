<div class="slider-logged hidden-xs">
	<h1 class="wow fadeInDown" data-wow-delay="0.5s"><?php echo __('Horoscope du jour').' '.$horoscope['HoroscopeSign']['name']; ?></h1>
</div>



<?php
    function getIconName($title=false)
    {
        if (!$title)return false;

        $rubriques = array(
            'AMOUR'     =>  'icon1',
            'ARGENT'    =>  'icon2',
            'SANTÉ'     =>  'icon3',
            'TRAVAIL'   =>  'icon4',
            'FAMILLE'   =>  'icon5',
            'LOISIRS'   =>  'icon6',
            'CITATION'  =>  'icon7',
            'NOMBRE DE CHANCE'    =>  'icon8',
            'CLIN D\'OEIL'=>'icon9',
            'FÊTE À SOUHAITER' => 'icon10'
        );

        foreach ($rubriques AS $alias => $icon)
            if (strpos(strtoupper($title), $alias) !== false)
                return $icon;

        return 'default';
    }

    ?>

<div class="container">
	<div class="page mt10 mb10 ">
		<div class="row">
			<div class="horoscope_boxcontainer col-sm-12 col-md-12 mb10  wow fadeIn animated" data-wow-delay="0.4s">
				<div class="horoscope_box col-sm-12 col-md-12">
					<div class="col-sm-12 col-md-6 horoscope-single">
						<div class="col-sm-12 col-md-3 hor-header">
							<div class="img-logo text-center">
									<img src="/theme/default/img/horoscope/<?php echo $horoscope['HoroscopeLang']['sign_id']; ?>.png" alt="Horoscope du jour <?php echo $horoscope['HoroscopeSign']['name']; ?>" >
							</div>
						</div>
						<div class="col-sm-12 col-md-9 hor-txt">
							<div class="horo-desc">
									<p class="uppercase bold"><?php echo $horoscope['HoroscopeSign']['name']; ?></p>
									<p class="small"><?php echo $horoscope_sign_dates; ?></p>
							</div>
						</div>
						<div class="col-sm-12 col-md-12 mt20 horoscope_info">	
							<?php
							$id_text = '';
							switch ($horoscope['HoroscopeLang']['sign_id']) {
								case 1:
									$id_text = 339;
									break;
								case 2:
									$id_text = 340;
									break;
								case 3:
									$id_text = 341;
									break;
								case 4:
									$id_text = 342;
									break;
								case 5:
									$id_text = 343;
									break;
								case 6:
									$id_text = 344;
									break;
								case 7:
									$id_text = 345;
									break;
								case 8:
									$id_text = 346;
									break;
								case 9:
									$id_text = 347;
									break;
								case 10:
									$id_text = 348;
									break;
								case 11:
									$id_text = 349;
									break;
								case 12:
									$id_text = 350;
									break;
							}

						echo $this->FrontBlock->getPageBlocTexte($id_text); ?>	
							<div class="col-sm-12 col-md-12 mt10 mb20 visible-sm horoscope_sep"></div>
						</div>
						
					</div>
					<div class="col-sm-12 col-md-6">
						<div class="row" style="margin:0">
						<div class="col-sm-12 col-md-12 horo-terme">
							<div class="visible-xs">
								<div class="terme_horo_block">
								<span class="horo-terme-title"><?php echo __('Le ').$horoscope['HoroscopeSign']['name'].__(' est :'); ?></span>
								
									<?php 
										$color = '#fff';
										if($horoscope['HoroscopeSign']['def1_color'] == "#fff" || $horoscope['HoroscopeSign']['def1_color'] == "#ffffff")$color = '#42424c';
									?>
									<span class="horo-term-block-txt">/ <?=$horoscope['HoroscopeSign']['def1']  ?></span>
									<?php 
										$color = '#fff';
										if($horoscope['HoroscopeSign']['def2_color'] == "#fff" || $horoscope['HoroscopeSign']['def2_color'] == "#ffffff")$color = '#42424c';
									?>
									<span class="horo-term-block-txt">/ <?=$horoscope['HoroscopeSign']['def2']  ?></span>
									<?php 
										$color = '#fff';
										if($horoscope['HoroscopeSign']['def3_color'] == "#fff" || $horoscope['HoroscopeSign']['def3_color'] == "#ffffff")$color = '#42424c';
									?>
									<span class="horo-term-block-txt">/ <?=$horoscope['HoroscopeSign']['def3']  ?></span>
									<?php 
										$color = '#fff';
										if($horoscope['HoroscopeSign']['def4_color'] == "#fff" || $horoscope['HoroscopeSign']['def4_color'] == "#ffffff")$color = '#42424c';
									?>
									<span class="horo-term-block-txt">/ <?=$horoscope['HoroscopeSign']['def4']  ?></span>
								</div>
							</div>
							<div class="hidden-xs horo-terme-block-container">
								<?php if($horoscope['HoroscopeSign']['def1_img']){ ?>
								<div class="horo-terme-block terme1">
									<?php 
									$color = '#fff';
									if($horoscope['HoroscopeSign']['def1_color'] == "#fff" || $horoscope['HoroscopeSign']['def1_color'] == "#ffffff")$color = '#42424c';
									 echo  $this->Html->image('/'.Configure::read('Site.pathHoroscope').'/'.$horoscope['HoroscopeSign']['def1_img'],array('alt'=>$horoscope['HoroscopeSign']['name'].' est '.$horoscope['HoroscopeSign']['def1'].' - horoscope Spiriteo'))
								?>
									<span class="horo-term-block-mobile" style="color:<?=$color ?>;background-color:<?=$horoscope['HoroscopeSign']['def1_color']  ?>"><?=$horoscope['HoroscopeSign']['def1']  ?></span>
								</div>
								<?php } ?>
								<?php if($horoscope['HoroscopeSign']['def2_img']){ ?>
								<div class="horo-terme-block terme2">
									<?php 
									$color = '#fff';
									if($horoscope['HoroscopeSign']['def2_color'] == "#fff" || $horoscope['HoroscopeSign']['def2_color'] == "#ffffff")$color = '#42424c';
									 echo  $this->Html->image('/'.Configure::read('Site.pathHoroscope').'/'.$horoscope['HoroscopeSign']['def2_img'],array('alt'=>$horoscope['HoroscopeSign']['name'].' est '.$horoscope['HoroscopeSign']['def2'].' - horoscope Spiriteo'))
								?>
									<span class="horo-term-block-mobile" style="color:<?=$color ?>;background-color:<?=$horoscope['HoroscopeSign']['def2_color']  ?>"><?=$horoscope['HoroscopeSign']['def2']  ?></span>
								</div>
								<?php } ?>
								<?php if($horoscope['HoroscopeSign']['def3_img']){ ?>
								<div class="horo-terme-block terme3">
									<?php 
									$color = '#fff';
									if($horoscope['HoroscopeSign']['def3_color'] == "#fff" || $horoscope['HoroscopeSign']['def3_color'] == "#ffffff")$color = '#42424c';
									 echo  $this->Html->image('/'.Configure::read('Site.pathHoroscope').'/'.$horoscope['HoroscopeSign']['def3_img'],array('alt'=>$horoscope['HoroscopeSign']['name'].' est '.$horoscope['HoroscopeSign']['def3'].' - horoscope Spiriteo'))
								?>
									<span class="horo-term-block-mobile" style="color:<?=$color ?>;background-color:<?=$horoscope['HoroscopeSign']['def3_color']  ?>"><?=$horoscope['HoroscopeSign']['def3']  ?></span>
								</div>
								<?php } ?>
								<?php if($horoscope['HoroscopeSign']['def4_img']){ ?>
								<div class="horo-terme-block terme4">
									<?php 
									$color = '#fff';
									if($horoscope['HoroscopeSign']['def4_color'] == "#fff" || $horoscope['HoroscopeSign']['def4_color'] == "#ffffff")$color = '#42424c';
									 echo  $this->Html->image('/'.Configure::read('Site.pathHoroscope').'/'.$horoscope['HoroscopeSign']['def4_img'],array('alt'=>$horoscope['HoroscopeSign']['name'].' est '.$horoscope['HoroscopeSign']['def4'].' - horoscope Spiriteo'))
								?>
									<span class="horo-term-block-mobile" style="color:<?=$color ?>;background-color:<?=$horoscope['HoroscopeSign']['def4_color']  ?>"><?=$horoscope['HoroscopeSign']['def4']  ?></span>
								</div>
								<?php } ?>
							</div>
						</div>
					</div></div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class=" col-sm-12 col-md-9 content-horo">
		<div class="content_box  col-sm-12 col-md-12  wow fadeIn animated" data-wow-delay="0.4s">
			
			<?php
			
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
			
			?>
			<h2 class="horo-titre wow fadeIn animated" data-wow-delay="0.5s"><?php echo __('Votre horoscope gratuit et complet du '); ?> <?php echo date("d", strtotime($horoscope_date)); ?> <?php echo $month[date("m", strtotime($horoscope_date))]; ?> <?php echo date("Y", strtotime($horoscope_date)); ?> <?php echo $horoscope['HoroscopeSign']['name']; ?></h2>
			
            <div class="horoscope-page">
            	<div class="horoscope-details row">
             <?php

            $text = $horoscope['HoroscopeLang']['content'];

            /* On supprime le dernier paragraphe */
              $text = explode("<h2>", $text);
              unset($text[count($text)-1]);
              $text = implode("<h2>", $text);
              $text = str_replace("</b>","</b> :&nbsp;", $text);

            $pattern = "/<h2>(.*?)<\/h2>/";
            preg_match_all($pattern, $text, $matches);
			
			$num = 1;
			
			
            foreach ($matches['1'] AS $h2){
                $newH2 = explode("- ", $h2);
                $newH2 = $newH2['1'];
				if($num == 1)
                $text = str_replace('<h2>'.$h2.'</h2>',
                                    '<section class="hor-section wow fadeIn animated" data-wow-delay="0.4s"><div class="uppercase icon_hor_rub icon_hor_rub_icon'.$num.' '.getIconName($h2).'"><span></span> <h2>'.$newH2.'</h2></div><p>',
                                    $text
                );
				if($num > 1)
                $text = str_replace('<h2>'.$h2.'</h2>',
                                    '</p></div><section class="hor-section wow fadeIn animated" data-wow-delay="0.4s"><div class="uppercase icon_hor_rub icon_hor_rub_icon'.$num.' '.getIconName($h2).'"><span></span> <h2>'.$newH2.'</h2></div><p>',
                                    $text
                );
				$text = str_replace('</p></div>',
                                    '</p></section>',
                                    $text
                );
				$num ++;
            }
            
					//refont en col
					$list_section = explode('</section>',$text);
					$new_text = '<div class="col-sm-12 col-md-6">';
					$current_index = 0;
					foreach($list_section as $k=>$sect){
						if($k == 0)$new_text .= $sect.'</section>';
						if($k == 1)$new_text .= $sect.'</section></div>';
						if($k == 2)$new_text .= '<div class="col-sm-12 col-md-6">'.$sect.'</section>';
						if($k == 3)$new_text .= $sect.'</section></div>';
						if($k == 3)break;
					}
					if(!$this->Session->read('Auth.User')){
						$new_text .= '<div class="col-sm-12 hidden-xs horo-pub"><a href="'.$horoscope['HoroscopeSign']['pub_link'].'" title="agents en ligne">'.$this->Html->image('/'.Configure::read('Site.pathHoroscope').'/'.$horoscope['HoroscopeSign']['pub'],array('alt'=>'agents en ligne')).'</a></div>';
						$new_text .= '<div class="col-sm-12 visible-xs horo-pub"><a href="'.$horoscope['HoroscopeSign']['pub_mobile_link'].'" title="agents en ligne">'.$this->Html->image('/'.Configure::read('Site.pathHoroscope').'/'.$horoscope['HoroscopeSign']['pub_mobile'],array('alt'=>'agents en ligne')).'</a></div>';
					}else{
						$new_text .= '<div class="col-sm-12">&nbsp;</div>';
					}
					$new_text .= '<div class="col-sm-12 col-md-6">';
					foreach($list_section as $k=>$sect){
						if($k == 4)$new_text .= $sect.'</section>';
						if($k == 5)$new_text .= $sect.'</section></div>';
						if($k == 6)$new_text .= '<div class="col-sm-12 col-md-6">'.$sect.'</section>';
						if($k == 7)$new_text .= $sect.'</section>';
						if($k == 8)$new_text .= $sect.'</section></div>';
					}
					echo $new_text; ?>
				</div>
               
            </div>
			<span class="horo_share_content_url hidden"><?php echo (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?></span>
			<div class="col-sm-12 col-md-12 horo-fb">
				<img class="hidden-xs" src="/theme/default/img/horo-fb.png" alt="Spiriteo Horoscope" />
				<img class="hidden-sm  hidden-md hidden-lg" src="/theme/default/img/horo-fb-mobile.png" alt="Spiriteo Horoscope" />
			</div>
			<!--   <ul class="list-inline linksubscribe">
				<li>
				<a class="pas-links" href="/users/subscribe" data-toggle="tooltip" data-placement="top" title="" data-original-title="Inscrivez-vous ?">Inscrivez-vous et recevez votre horoscope gratuit régulièrement </a>
				</li>
				</ul>-->
			
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
			</div></div>
		<aside class="col-sm-12 col-md-3 pub-top hidden-sm hidden-xs">
			<?php
			if($horoscope['HoroscopeSign']['pub_sidebar_top'])
			echo '<div class="mb10 horo-pub horo-pub-top"><a href="'.$horoscope['HoroscopeSign']['pub_sidebar_top_link'].'" title="agents en ligne">'.$this->Html->image('/'.Configure::read('Site.pathHoroscope').'/'.$horoscope['HoroscopeSign']['pub_sidebar_top'],array('alt'=>'agents en ligne')).'</a></div>';
			?>
		</aside>
		<?php
			echo $this->Frontblock->getRightSidebar(true,'Nos astrologues experts');
			?>
			<aside class="col-sm-12 col-md-3 pub-top mt10 hidden-sm hidden-xs">
			<?php
				if($horoscope['HoroscopeSign']['pub_sidebar_bottom'])
			echo '<div class="horo-pub horo-pub-bottom"><a href="'.$horoscope['HoroscopeSign']['pub_sidebar_bottom_link'].'" title="agents en ligne">'.$this->Html->image('/'.Configure::read('Site.pathHoroscope').'/'.$horoscope['HoroscopeSign']['pub_sidebar_bottom'],array('alt'=>'agents en ligne')).'</a></div>';
			?>
		</aside>
	</div></div>
	
		<?php
		/*<aside class="col-sm-12 mt20 page_widget visible-xs">
     	
			echo $this->Frontblock->getBottomWidgetHoroscope();
			
     </aside>*/
	 ?>

</div>