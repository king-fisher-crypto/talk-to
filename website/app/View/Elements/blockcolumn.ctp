<?php
    $datas = $this->FrontBlock->getColumnBlocks();

    foreach($datas as $data) : 
	
	if($data['LeftColumnLang']['left_column_id'] != 1 || ( $data['LeftColumnLang']['left_column_id'] == 1 && !$this->Session->read('Auth.User') )){
	
	?>
        <div class="box gray">
            <div class="box_title"><span class="box_icon"></span> <?php echo $data['LeftColumnLang']['title']; ?></div>
            <div class="box_content" style="margin-top:10px">
               <?php
                    if(empty($data['LeftColumnLang']['link'])):
                        echo $this->Html->image('/'.Configure::read('Site.pathLeftColumn').'/'.$data['LeftColumnLang']['name'], array(
                                'alt' => (empty($data['LeftColumnLang']['alt']) ?$data['LeftColumnLang']['title']:$data['LeftColumnLang']['alt']),
                                'title' => (empty($data['LeftColumnLang']['alt']) ?$data['LeftColumnLang']['title']:$data['LeftColumnLang']['alt'])
                            ));
                    else :
                        echo $this->Html->link(
                            $this->Html->image('/'.Configure::read('Site.pathLeftColumn').'/'.$data['LeftColumnLang']['name'], array(
                                    'alt' => (empty($data['LeftColumnLang']['alt']) ?$data['LeftColumnLang']['title']:$data['LeftColumnLang']['alt']))
                            ),
                            $data['LeftColumnLang']['link'],
                            array('escape' => false, 'title' => (empty($data['LeftColumnLang']['alt']) ?false:$data['LeftColumnLang']['alt']))
                        );
                    endif;
                ?>
            </div>
        </div>
    <?php
	}
	 endforeach; ?>