<div class="bootbox modal fade bootbox-prompt in" id="myModal" tabindex="-1" role="dialog" style="display: block;" aria-hidden="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" data-dismiss="modal" class="bootbox-close-button close">×</button>
                <h4 class="modal-title"><?php echo __($title); ?></h4>
            </div>
            <div class="modal-body">
                <div class="bootbox-body">
					<?php
						if($msg_error){
							echo '<p>'.$msg_error.'</p>';
						}else{
					
					?>
                    <?php 
					
    echo $this->Form->create('Agent',array('nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1, 'action' => 'order_mail', 'admin' => true));
	echo $this->Form->input('bodymail',
        array(
            'label' => false,
            'type' => 'textarea',
            'class' => 'span5',
			//'tinymce' => true, 
            'value' => strip_tags(str_replace('</p>','
',str_replace('<br />','
',$body_mail))))
    );
							
	echo $this->Form->input('invoice_id',
        array(
            'label' => false,
            'type' => 'hidden',
            'class' => 'span5',
            'value' => $invoice_id)
    );
		
   
    echo $this->Form->end(array(
        'label' => __('Envoyer'),
        'class' => 'btn blue',
        'div' => array('class' => 'form-group admin_content_btn')
    ));
							
						}
					
					?>
                </div>
            </div>
            <div class="modal-footer">
                <button data-bb-handler="confirm" type="button" class="btn btn-primary ok_admin_modal"><?php echo __('Envoyer') ?></button>
                <button data-bb-handler="cancel" data-dismiss="modal" type="button" class="btn btn-default"><?php echo __('Annuler') ?></button>
            </div>
        </div>
    </div>
</div>