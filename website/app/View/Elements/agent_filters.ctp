<div id="tuto_container" class="bg-white filter-box" style="display:none">
	<div class="row">
    	<div class="alert-info2 alert-dismissible alert-custom2" role="alert">
            <div class="box gray hidden-xs">
                <div class="box_content">
                    <div class="box-description">
                        <div id="tuto"></div>
                    </div>
                </div>
            </div>
		</div>
    </div>
</div>
<aside>
	<div class="filter-box" id="search_filters">
	<?php echo $this->Form->create('Category', array('class' => 'row', 'id' => 'filters_form','url' => array('controller' => 'category','action' => 'display', 'id' => 1))); ?>
		<div class="row2">
			<div class="col-md-7 col-sm-12 filtre-desktop">
				<ul class="list-unstyled list-inline mb0">
						<li class="filter-title">Type de consultation</li>
						
						 <?php
						foreach ($consult_medias_for_filters AS $media => $txt){
							echo '<li class="filter-icons wow fadeIn" data-wow-delay="0.2s">
										<input id="sf_media_'.$media.'" type="checkbox"'. (in_array($media,$datas['mediaChecked'])?'checked':'') .' name="sf_media" value="'.$media.'" style="display:none"/>';
										
										if($media == 'phone')
										echo '<label for="sf_media_'.$media.'"><img src="/theme/default/img/icons/filter-phone.png" data-toggle="tooltip" data-placement="top" title="'.$txt.'" alt="Spiriteo - agents par téléphone" ></label>';
										if($media == 'chat')
										echo '<label for="sf_media_'.$media.'"><img src="/theme/default/img/icons/filter-chat.png" data-toggle="tooltip" data-placement="top" title="'.$txt.'" alt="Spiriteo - agents par tchat" ></label>';
										if($media == 'email')
										echo '<label for="sf_media_'.$media.'"><img src="/theme/default/img/icons/filter-mail.png" data-toggle="tooltip" data-placement="top" title="'.$txt.'" alt="Spiriteo - agents par email" ></label>';
										
										
										echo '</li>';
						}
						?>
						<li class="filter-advance hidden-xs"><a role="button" id="filterCollapseContent">
							<i class="adv-filter-icons fa fa-plus-circle" aria-hidden="true"></i>
							Recherche Avancée</a></li>
					</ul> 
			</div>
			<div class="col-md-5 col-sm-12 col-xs-12">
				<div class="search-input filtre-desktop">
						<input type="text" class="search-query" placeholder="<?php echo __('Rechercher un expert'); ?>" name="sf_term" autocomplete="off"><input type="hidden" name="sf_term_novalue" value="<?php echo __('Rechercher un expert'); ?>" /><button class="btn" type="submit">
								<span class="glyphicon glyphicon-search"></span>
						</button>
				</div>
				<div class="search-select">
						<select name="search_filters[orderby]" id="sf_orderby" class="form-control-white form-control2">
													<?php foreach ($filters['filter_orderby'] AS $alias => $parms){ ?>
														<?php if ($parms['enabled']){ ?>
															<option value="<?php echo $alias; ?>"<?php echo (isset($parms['active'])?'selected':''); ?>><?php echo $parms['label']; ?></option>
														<?php } ?>
													<?php } ?>
						</select>
				</div>
				<a class="filter-mobile-menu navbar-toggle right-toggle collapse-toggle collapse" data-toggle="collapse" data-target="#offcanvasfilter"></a>
			</div>
		</div>
		<div class="collapse" id="filterCollapse">
				<div class="advance-option">
					<div class="fcompetence">
						<p>FILTRER PAR COMPÉTENCE</p>
						<div class="row list-group filtre-category" data-toggle="items">
							<a class="col-md-4 col-sm-6 list-group-item filtre-category-5 <?php if(isset($category_id) && $category_id == 5) echo 'active'; ?>">Voyants</a>
							<a class="col-md-4 col-sm-6 list-group-item filtre-category-27 <?php if(isset($category_id) && $category_id == 27) echo 'active'; ?>">Mediums</a>
							<a class="col-md-4 col-sm-6 list-group-item filtre-category-7 <?php if(isset($category_id) && $category_id == 7) echo 'active'; ?>">Tarologues</a>
							<a class="col-md-4 col-sm-6 list-group-item filtre-category-2 <?php if(isset($category_id) && $category_id == 2) echo 'active'; ?>">Astrologues</a>
							<a class="col-md-4 col-sm-6 list-group-item filtre-category-3 <?php if(isset($category_id) && $category_id == 3) echo 'active'; ?>">Cartomanciens</a>
							<a class="col-md-4 col-sm-6 list-group-item filtre-category-6 <?php if(isset($category_id) && $category_id == 6) echo 'active'; ?>">Numerologues</a>
							<a class="col-md-4 col-sm-6 list-group-item filtre-category-20 <?php if(isset($category_id) && $category_id == 20) echo 'active'; ?>">Magnetiseurs</a>
							<a class="col-md-4 col-sm-6 list-group-item filtre-category-28 <?php if(isset($category_id) && $category_id == 28) echo 'active'; ?>">Channeling</a>
							<a class="col-md-4 col-sm-6 list-group-item filtre-category-25 <?php if(isset($category_id) && $category_id == 25) echo 'active'; ?>">Coaching</a>
							<a class="col-md-4 col-sm-6 list-group-item filtre-category-26 <?php if(isset($category_id) && $category_id == 26) echo 'active'; ?>">Interprétation des rêves</a>
							<a class="col-md-4 col-sm-6 list-group-item filtre-category-0 ">Tous</a>
						</div>

					</div>
				</div>
			</div>
	<?php
	echo '<input type="hidden" name="data[Category][page]" class="form-control" value="'.$datas['page'].'" id="numPage"/>';
    echo  $this->Form->end();
    ?>	
	</div><!--filter END-->
	<nav class="navbar navbar-custom navbar-collapse navbar-fixed-top navbar-offcanvas-filter navbar-collapse" id="offcanvasfilter">
		<div class="content">
			<?php echo $this->FrontBlock->getHeaderMobileFilterAgent($filters); ?>
		</div>
	</nav><!--mobile-collapse END-->
</aside>