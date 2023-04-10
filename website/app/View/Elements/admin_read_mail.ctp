<div style="text-align:right">
<?php
    echo $this->Form->button('<span class="icon-check "></span>  '.__('Marquer comme non lue'), array('type' => 'button', 'class' => 'btn mini red-stripe', 'id' => 'notreadaction', 'msg-id' => $idMail));


?>
</div>
<?php

foreach($conversation as $mail):
    $itsme = ($mail['from_id'] == $this->Session->read('Auth.User.id'))?true:false;
    ?>
    <div class="mail<?php echo $itsme? '': ' notme'; ?>">

        <?php

            echo '<div class="info'. ($itsme ?' from':' to') .'">';
            if(!empty($mail['attachment']))
                echo $this->Html->link('<span class="glyphicon glyphicon-paperclip margin_right_5"></span>'.__('Télécharger'),
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
          //  echo '<span class="message_date">'.$this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$mail['date']),' %d/%m/%y %Hh%M').'</span>';
			echo '<span class="message_date">'.$this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$mail['date']),' %d/%m/%y %Hh%M').'</span>';

            echo '<span class="from_txt"> '.$mail['from'].'</span>';

            echo '<span class="to_txt"> ('.__('à').' '.$mail['to'].')</span>';
            echo '</div>';
            echo '<div class="corps">'.nl2br($mail['content']).'</div>';
        ?>
    </div>
<?php endforeach;