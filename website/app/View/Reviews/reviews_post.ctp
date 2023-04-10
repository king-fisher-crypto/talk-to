<!--<div id="leftcolumn" class="col-md-3 col-sm-4 col-xs-12">
    <?php
       // echo $this->Frontblock->getAccountSidebar();
       // echo $this->element('reviewbox');
        //echo $this->Html->script('/theme/default/js/rate_select', array('block' => 'script'));
		
    ?>
</div>-->
<?php
echo $this->Html->script('/theme/default/js/jquery.raty-fa', array('block' => 'script'));
        echo $this->Html->script('/theme/default/js/disabledButtonSubmit', array('block' => 'script'));
?>
<div class="container">
<div class="page profile-page mt20 mb40">
	<div class="row">
<div id="content_with_leftcolumn" class="content_box col-md-9 col-sm-8 col-xs-12" >
    <?php echo $this->Session->flash();

    /* titre de page */
   echo $this->element('title', array(

        'title' => __('Votre avis nous intéresse'),
        'icon' => 'comment',
        'breadcrumb' => array(

            0   =>  array(

                'name'  =>  __('Accueil'),
                'link'  =>  Router::url('/',true)
            ),
            1   =>  array(
                'name'  =>  __('Votre avis'),
                'link'  =>  ''
            )
        )
    )); ?>

    <div class="col-sm-12">
        <?php
        if(!empty($voyants)){
            echo $this->Form->create('reviews', array('action' => 'reviews_post?u='.$user_id.'&a='.$agent_id.'&c='.$consult_id, 'nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1,
                'inputDefaults' => array(
                    'div' => 'form-group',
                    'between' => '<div class="col-lg-7">',
                    'after' => '</div>',
                    'class' => 'form-control',
                )
            ));

            echo $this->Form->input('agent_number', array(
                'label' => array('text' => __('Sélectionner l\'expert'), 'class' => 'control-label col-lg-3 required'),
                'options' => $voyants,
                'empty' => __('Choisissez un expert'),
                'between' => '<div class="col-lg-3">',
                'selected'  => (isset($expert) && !empty($expert) && in_array($expert, array_keys($voyants)) ?$expert:false),
                'style'    => 'width:250px',
                'required' => true
            )); ?>
            <div class="form-group">
                <label class="control-label col-lg-3 required"><?php echo __('Evaluation de l\'expert'); ?></label><br />
                <div class="" id="review_stars2" style="font-size:15px;color:#ffa800;float:left"></div><div id="evaluation_expert" style="display:block;float:left;margin-left:5px;">5/5</div>
               <br /> <?php echo $this->Form->input('rate', array('type' => 'hidden', 'value' => 5)) ?>
            </div>

            <?php echo $this->Form->input('content', array(
                    'label' => array(
                        'text' => __('Votre avis'),
                        'class' => 'control-label col-lg-3 required'
                    ),
                    'required' => true,
					'maxlength'=> 1000,
                    'type' => 'textarea',
                    'between' => '<div class="col-lg-offset-3 col-lg-7">'
                )
            );

            echo $this->Form->end(array('label' => __('Envoyer'), 'class' => 'btn btn-primary col-lg-offset-4', 'div' => array('class' => 'form-group')));
        }else{
            echo __('Vous n\'avez consulté aucun expert ou aucun expert récemment.').' '. $this->Html->link(__('Rendez-vous ici'),array('controller' => 'home', 'action' => 'index')) .' '.__('pour en consulter un.');
        }
        ?>
    </div></div></div></div>
</div>

<script>
$('#review_stars2').raty({
  half   : true,
  size   : 15,
  target : '#evaluation_expert',
  score : 5,
  targetText: '',
  targetType: 'hint',
  hints  : ['<?php echo __('Mauvais'); ?>', '<?php echo __('Peu satisfaisant'); ?>','<?php echo __('Satisfaisant'); ?>','<?php echo __('Bon'); ?>','<?php echo __('Excellent'); ?>'],
   click: function(score, evt) {
   	$("#reviewsRate").val($("input[name=score]").val());
  }
});
</script>