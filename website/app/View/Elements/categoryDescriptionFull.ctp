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
				$desc = nl2br(str_replace('<!--PAGEBREAK-->','',$description));
					echo 	$desc;
                    

			    ?>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
<?php endif; ?>