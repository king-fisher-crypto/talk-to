<?php
    echo $this->Metronic->titlePage(__('Level'),__('Les levels'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Levels'),
            'classes' => 'icon-exchange',
            'link' => $this->Html->url(array('controller' => 'userlevelacl', 'action' => 'list', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Les levels'); ?></div>
        </div>
        <div class="portlet-body">
            <?php if(empty($levels)) :
                echo __('Aucun level');
            else : ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
                        <th><?php echo $this->Paginator->sort('Userlevelacl.level', __('Level')); ?></th>
                        <th><?php echo __('Actions'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php 
						
						$level_show = array();
						foreach ($levels as $level):
							if(!in_array($level['Userlevelacl']['level'],$level_show)){
						?>
                        <tr>
                            <td><?php echo $level['Userlevelacl']['level']; ?></td>
                            <td><?php
                                    echo $this->Metronic->getLinkButton(
                                        __('Modifier')   ,
                                        array('controller' => 'userlevelacl', 'action' => 'edit', 'admin' => true, 'id' => $level['Userlevelacl']['level']),
                                        'btn blue',
                                        'icon-edit'
                                    );
                                ?>
                            </td>
                        </tr>
                    <?php 
							array_push($level_show,$level['Userlevelacl']['level']);
							}
						
								endforeach; ?>
                    </tbody>
                </table>
                <?php if($this->Paginator->param('pageCount') > 1) echo $this->Metronic->pagination($this->Paginator); ?>
            <?php endif; ?>
        </div>
    </div>
</div>