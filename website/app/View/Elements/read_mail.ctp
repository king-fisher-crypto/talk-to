<div id="collapseMail" class="panel-collapse " role="tabpanel" aria-labelledby="collapseMail">
<div class="panel-body">
<?php foreach($conversation as $mail):
    $itsme = ($mail['from_id'] == $this->Session->read('Auth.User.id'))?true:false;
?>
	<div class="box_account well well-account well-small <?php echo $itsme? 'msg-me': 'msg-from'; ?> <?php echo $mail['etat'] == 3 ? 'msg-disable': ''; ?>">
    <div class="mail">

        <?php

            echo '<div class="info'. ($mail['from_id'] == $this->Session->read('Auth.User.id') ?' from':' to') .'">';
            if(!empty($mail['attachment']))
                echo $this->Html->link(''.__('Télécharger la pièce jointe'),
                    array(
                        'controller' => $controller,
                        'action' => 'downloadAttachment',
                        'name' => $mail['attachment']
                    ),
                    array(
                        'escape' => false,
                        'class' => 'btn btn-primary btn-xs attachment'
                    )
                );
			if(!empty($mail['attachment2']))
                echo '&nbsp;'.$this->Html->link(''.__('Télécharger la deuxième pièce jointe'),
                    array(
                        'controller' => $controller,
                        'action' => 'downloadAttachment',
                        'name' => $mail['attachment2']
                    ),
                    array(
                        'escape' => false,
                        'class' => 'btn btn-primary btn-xs attachment'
                    )
                );
				
			echo '<h4 class="tabs-heading">';	
				 echo (!$itsme?'<span class="glyphicon glyphicon-user"></span>':'<span class="glyphicon glyphicon-pencil"></span>').' '.$mail['from'];
				 echo '<span class="black small">&nbsp;('.__('à').' '.$mail['to'].')</span>';
		
			if($mail['etat'] == 3){
				echo '<span class="small pull-center alert-disable-title">Mail annulé</span>';
			}
		
            echo '<span class="small pull-right" style="text-align:center">'.$this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$mail['date']),' %d/%m/%y %Hh%M');
			
			if($is_in_live)
				echo '<br />En cours';
		
			echo '</span>';
            
            echo '</h4><hr/></div>';
			
            echo '<div class="corps">';
		
			$content = $mail['content'];
			if(substr_count($content, '<!---->')){
				$tabcontent = explode('<!---->', $content);
				$content = $tabcontent[0].'<br /><br />';
				$content .= $tabcontent[1].'<br /><br />';
				$content .= $tabcontent[2].'<br /><br />';
				$content .= $tabcontent[3];
			}
		
		
		 echo nl2br($content);
			
			echo '</div>';
			
			
        ?>
    </div></div>
<?php endforeach; ?>
</div></div>