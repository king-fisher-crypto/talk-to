<?php
    echo $this->Metronic->getLinkButton(
        __('Supprimer'),
        array('controller' => 'menus', 'action' => 'delete_block_link', 'admin' => true, 'id' => $idLink),
        'btn red',
        'icon-remove',
        __('Voulez-vous vraiment supprimer le bloc de lien ?')
    );