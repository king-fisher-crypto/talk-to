<?php
    echo $this->Html->script('/theme/default/js/nx_login_modal', array('block' => 'script'));
    echo $this->Form->create('User', array('action' => 'login', 'nobootstrap' => 1,'class' => 'con-login', 'default' => 1, 'id' => 'login_modal',
                                           'inputDefaults' => array(
                                               'class' => 'form-control'
                                           )));

    echo $this->Form->inputs(array(
            'legend' => false,
            'compte' => array('type' => 'hidden', 'value' => 'client'),
            'email'  => array('label' => '', 'required' => true, 'placeholder' => __('Votre E-mail')),
            'passwd' => array('label' => '', 'required' => true, 'placeholder' => __('Mot de passe')))
    );
?>
    <div class="connect-footer text-center">
        <ul class="list-inline">
            <?php
                echo '<li class="login-button-li">'.$this->Form->button(__('Connexion'),array('type' => 'submit', 'class' => 'btn btn-pink btn-connect-popup')).'</li>';

               /* echo '<li class="">'.__(' ou ').'</li>';

                echo '<li class="subscribe-button-li">'.$this->Html->link(
                    __('S\'inscrire'),
                    array('controller' => 'users', 'action' => 'subscribe'),
                    array('class' => 'btn btn-pink btn-connect-popup')
                ).'</li>';*/
            ?>
        </ul>
        <div class="text-bottom-form text-center" style="margin-bottom:20px;">
            <!--<a class="pas-links" title="Nouveau?" data-placement="top" data-toggle="tooltip" href="/users/subscribe" data-original-title="Nouveau?">Vous n'êtes pas inscrit ? </a>-->
            <a class="ins-links" title="Creez un compte" data-placement="top" data-toggle="tooltip" href="/users/subscribe" data-original-title="Creez un compte">Pas encore inscrit ? Inscrivez-vous !</a>
            </div>
    </div>
<?php
    echo $this->Form->end();