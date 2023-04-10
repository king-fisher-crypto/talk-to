<p><?php echo __('Vous pouvez ajouter un motif pour le refus') ?></p>
<p class="txt-bold"><?php echo __('Note : '.$note); ?></p>
<?php
    echo $this->Form->create($model,array('nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1));
    echo $this->Form->input('motif',
        array(
            'label' => false,
            'class' => 'span5'
        )
    );
    echo $this->Form->end(array(
        'label' => __('Ok'),
        'class' => 'btn blue',
        'div' => array('class' => 'form-group admin_content_btn')
    ));