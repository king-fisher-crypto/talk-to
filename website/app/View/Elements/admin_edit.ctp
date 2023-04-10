<p><?php echo __($title) ?></p>
<p class="txt-bold"><?php echo __('Note : '.$note); ?></p>
<?php
    echo $this->Form->create($model,array('nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1));
	if(is_numeric($rate)){
	echo $this->Form->input('rate',
        array(
            'label' => false,
            'type' => 'input',
            'class' => 'span5',
            'value' => $rate)
    );
		echo $this->Form->input('pourcent',
        array(
            'label' => false,
            'type' => 'input',
            'class' => 'span5',
            'value' => $pourcent)
    );
		
		echo '<div class="input input"><div class="input number"><label for="ReviewDateAdd">Date</label><input name="data[Review][date_add]" class="span5" id="ReviewDateAdd" type="text" value="'.$date_add.'"></div></div>';
		/*echo $this->Form->input('date_add',
        array(
            'label' => false,
            'type' => 'input',
            'class' => 'span5',
            'value' => $date_add)
    );*/
	}
    echo $this->Form->input('content',
        array(
            'label' => false,
            'type' => 'textarea',
            'class' => 'span5',
			 'style'=>"margin:10px 0",
            'value' => $content)
    );
if(is_numeric($rate)){
echo ''.$this->Form->input('send_mail',
        array(
            'label' => 'Envoyer l\'email',
            'type' => 'checkbox',
			'style' => 'width:20px;margin-left:0px',
            'class' => 'span5',
            'value' => $send_mail)
    );
}
    echo $this->Form->end(array(
        'label' => __('Modifier'),
        'class' => 'btn blue',
        'div' => array('class' => 'form-group admin_content_btn')
    ));