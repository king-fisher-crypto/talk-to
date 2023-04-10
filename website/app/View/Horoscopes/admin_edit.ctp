<?php
    echo $this->Metronic->titlePage(__('Horoscopes'),__('Edition d\'un horoscope').' ('.($horoscope['name'] !== false ?$horoscope['name']:__('Sans nom')).')');
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'), 'classes' => 'icon-home', 'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Horoscope'), 'classes' => 'icon-asterisk', 'link' => $this->Html->url(array('controller' => 'horoscopes', 'action' => 'list', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <?php echo $this->Form->create('Horoscope', array('nobootstrap' => 1,'class' => 'form', 'default' => 1,
                                                 'inputDefaults' => array(
                                                     'div' => 'control-group',
                                                     'between' => '<div class="controls">',
                                                     'after' => '</div>'
                                                 ))); ?>
    <div class="span8">
        <div class="tabbable tabbable-custom tabbable-full-width">
            <ul class="nav nav-tabs">
                <?php $i=0; foreach($langs as $id => $val):
                    echo '<li'.($i==0 ?' class="active"':'').'><a data-toggle="tab" href="#tab'.$id.'"><span class="margin-right lang_flags lang_'.array_keys($val)[0].'"></span>'.__($val[array_keys($val)[0]]).'</a></li>';
                    $i = 1;
                endforeach; ?>
            </ul>
            <div class="tab-content">
                <?php $i=0; foreach($langs as $id => $val): ?>
                    <div id="tab<?php echo $id; ?>" class="tab-pane<?php echo ($i==0 ?' active':''); ?>">
                        <?php echo $this->Metronic->formHoroscopeLang($id,$horoscope['id'],(isset($langDatas[$id])?$langDatas[$id]:array())); ?>
                    </div>
                    <?php $i = 1; ?>
                <?php endforeach; ?>
            </div>
            <?php echo $this->Form->submit(__('Enregistrer'),array('class' => 'btn blue')); ?>
        </div>
    </div>
    <div class="span3 block_edit_admin panel-contenu">
        <?php
            echo $this->Form->inputs(array(
                'date_publication'  => array('label' => array('text' => __('Date de publication'), 'class' => 'control-label required'), 'required' => true, 'class' => 'span8', 'maxlength' => 10, 'type' => 'text', 'placeholder' => 'JJ-MM-AAAA', 'value' => $this->Time->format($horoscope['date_publication'], '%d-%m-%Y')),
                'sign_id'           => array('type' => 'hidden', 'value' => $horoscope['sign_id'])
            ));
        ?>
    </div>
    <?php echo $this->Form->end(); ?>
</div>
                            
                            

