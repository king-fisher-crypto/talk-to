<?php
    echo $this->Html->script('/theme/default/js/admin_message', array('block' => 'script'));
?>
<?php
    echo $this->Metronic->titlePage(__('Administrateur'),__('Messagerie'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Messagerie'),
            'classes' => 'icon-envelope',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'mails', 'admin' => true))
        )
    ));
    echo $this->Session->flash();

?>

<div class="row-fluid">
    <div class="portlet box yellow">
        <div class="portlet-title">
            <div class="msgerie_admin_paginator"><?php if($this->Paginator->param('pageCount') > 1) echo $this->Metronic->pagination($this->Paginator); ?></div>
            <div class="caption">
                <?php echo __('Messages'); ?>
				<?php
                    echo $this->Form->create('Admin', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
                    echo $this->Form->input('adr_ip', array('class' => 'input-small  margin-left margin-right', 'style' => 'width:120px', 'type' => 'texte', 'label' => __('IP').' :', 'div' => false));
				echo $this->Form->input('email', array('class' => 'input-small  margin-left margin-right', 'style' => 'width:120px', 'type' => 'texte', 'label' => __('Email').' :', 'div' => false));
				echo $this->Form->input('Nom', array('class' => 'input-small  margin-left margin-right', 'style' => 'width:120px', 'type' => 'texte', 'label' => __('Nom').' :', 'div' => false));
                    echo '<input class="btn green" type="submit" value="Ok" /></form>';

                
                ?>
            </div>
        </div>
        <div class="portlet-body nx_mail" url="<?php echo $this->Html->url(array('controller' => 'admins', 'action' => 'readMail', 'admin' => true)); ?>">
            <?php if(empty($mails)): ?>
                <?php echo __('Pas de message'); ?>
            <?php else: ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
                        <th><?php echo __('Discussion'); ?></th>
                        <th><?php echo __('Date'); ?></th>
                        <th><?php echo __('IP'); ?></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($mails as $k => $mail):?>
                        <tr class="discussion<?php echo ($mail['LastMessage']['admin_read_flag'] == 0)?' noread':''; ?>" mail="<?php echo $mail['Message']['id']; ?>">
                            <td>
                                <div class="col_from_ad">
                                    <?php
                                        $key = ($mail['Message']['from_id'] == $id ?'To':'From');
                                        //Si invité ??
                                        if($mail['Message']['from_id'] == Configure::read('Guest.id'))
                                            echo '<span style="color:#666; font-size:11px"><span class="icon-question"></span> <i>'.__('(Invité)').' : </i></span> '.$mail['Guest']['firstname'].' '.$mail['Guest']['lastname'].' ('.$mail['Guest']['email'].')';
                                        else{
                                            echo '<span class="icon-user"></span> '.($mail[$key]['role'] === 'admin'
                                                ?'Moi'
                                                :(empty($mail[$key]['pseudo'])
                                                    ?$mail[$key]['firstname'].' ('.$mail[$key]['email'].')'
                                                    :$mail[$key]['pseudo'].' ('.$mail[$key]['email'].')')
                                            );
                                        }


                                    ?>
                                </div>
                                <?php

                                echo '<span class="preview">'.substr(strip_tags($mail['LastMessage']['content']),0,Configure::read('Site.previewMail')).(strlen($mail['LastMessage']['content']) < Configure::read('Site.previewMail') ?'':'...').'</span>';

                                ?>
                            </td>
                            <td><?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$mail['LastMessage']['date_add']),' %d/%m/%y %H:%M');
								//echo $this->Time->format($mail['LastMessage']['date_add'],' %d/%m/%y %H:%M');?></td>
							<td><?php if($mail['LastMessage']['IP']) echo $mail['LastMessage']['IP']; else echo $mail['Message']['IP']; ?></td>
                           <td>
                            <?php
							   
							   echo $this->Metronic->getLinkButton(
                                        __('Supprimer')   ,
                                        array('controller' => 'admins', 'action' => 'delete_mails', 'admin' => true, 'id' => $mail['Message']['id']),
                                        'btn mini red-stripe',
                                        'icon-remove'
                                    );
							   

                            ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>


<div id="mail-content"></div>
<div id="mail-answer"></div>