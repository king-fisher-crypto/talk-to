        <div class="row2"><div class="form-group2 mt20 wow fadeIn animated">
            <?php
       
	 echo $this->Form->inputs(array(
            'absence'   => array(
                'label' => array('text' => __('Absence, Maladie ou congés'), 'class' => 'col-sm-12 col-md-4 control-label'),
                'required' => false,
				'between' => '<div class="col-sm-12 col-md-8">', 'after'=> '<p style="text-align:justify">Indiquez dans le champs ci-dessus vos dates de congés, maladie ou absence diverses lorsque vous serez ou êtes absent(e) durant un délai prolongé, cela permettra aux administrateurs du site de comprendre les raisons d\'une inactivité temporaire ou prolongée.</p><p style="text-align:justify">
Attention vos administrateurs n\'en prendront connaissance qu\'en allant dans votre compte expert et n\'en seront pas avertis lorsque vous y mettrez ces indications, pour tout échange directe, privilégiez la page " Contact " de Spiriteo ou les emails ( Les informations ci-contre ne seront pas visibles des clients Spiriteo ).
</p></div>',
		 		'value' => $abs_agent
            ))
    ); 
	?>
</div></div>


