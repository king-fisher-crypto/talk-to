       <div class="row"> <div class="mt20" style="display:block;float:left;border-top:1px solid #eee;padding-top:30px">
            <?php
       
	 echo $this->Form->inputs(array(
            'mail_infos'   => array(
                'label' => array('text' => __('Informations clients pour une consultation par email'), 'class' => 'col-sm-12 col-md-4 control-label'),
                'required' => false,
				'between' => '<div class="col-sm-12 col-md-8">', 'after'=> '<p>( Indiquez dans le champs ci-dessus les éléments dont vous avez besoin pour une consultation par Email, ces indications apparaîtront sur le site avant envoi du client )</p></div>',
		 		'value' => $mail_detail_agent, 'maxlength' => 240
            ))
    ); 
	?>
</div></div>


