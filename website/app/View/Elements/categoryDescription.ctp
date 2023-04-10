<?php if(isset($categorie) && !empty($categorie) && !empty($categorie['CategoryLang']['description'])): ?>
    <div class="box gray" id="cat_category">
        <div class="box_content">
            <div class="box-description">
                <!--<div class="close_btn_cms"><i onclick="nxMain.hideCmsCategory();" class="glyphicon glyphicon-remove"></i></div>-->
                <?php
                    //echo Tools::texte_resume_html($categorie['CategoryLang']['description'], 410);
                    $description = $categorie['CategoryLang']['description'];
					
					if(substr_count($description, '</h1>')){
						$split_content = explode('</h1>',$description);
						$split_titre = explode('>',$split_content[0]);
						$description = $split_content[1];
					}
					
					
					$desc_suite = '';
                    if (strpos($description, '<!--PAGEBREAK-->') !== false){
                        $description = explode('<!--PAGEBREAK-->', $description);
						$desc_suite = $description['1'];
                        $description = $description['0'];
						
                    }else{
                        $description = $description;//Tools::texte_resume_html($description, 410);
                    }

					echo nl2br($description);	
                    
					
					

                    $options = array(
                        'language' => $this->Session->read('Config.language'),
                        'controller' => 'category',
                        'action' => 'displayUnivers',
                        'id'    => $categorie['CategoryLang']['category_id'],
                        'link_rewrite'  => $categorie['CategoryLang']['link_rewrite']
                    );



                    if ($categorie['CategoryLang']['category_id'] == 1){
                        unset($options['link_rewrite']);
                    }
                   // echo $this->Html->link(__('Lire la suite'), $category_link, array('title' => $categorie['CategoryLang']['meta_title'], 'class' => 'saymore'));
                  // echo $this->Html->link(__('Lire la suite...'), '#/', array('title' => $categorie['CategoryLang']['meta_title'], 'class' => 'saymore show_hide show_btn'));
					//echo '<a title="'.$categorie['CategoryLang']['meta_title'].'" class="saymore show_hide show_btn" style="display: block;">'.__('Lire la suite...').'</a>';
					//echo '<div class="slidingDiv">'.$desc_suite.'</div>';
					//echo $this->Html->link(__('Fermer'), '#/', array('title' => $categorie['CategoryLang']['meta_title'], 'class' => 'saymore_close show_hide hide_btn', ));
					//echo '<a title="'.$categorie['CategoryLang']['meta_title'].'" class="saymore_close show_hide hide_btn" style="display: block;">'.__('Fermer').'</a>';

			    ?>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
<?php endif; ?>