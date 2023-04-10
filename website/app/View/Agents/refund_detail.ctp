<?php
//Ajax, l'historique de la conversation
    if(isset($isAjax) && $isAjax) : ?>
    
    <div class=" box_account well well-account well-small">
		<?php echo $messages ?>
        </div>

<?php endif; ?>