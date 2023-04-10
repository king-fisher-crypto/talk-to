<?php
    echo $this->Metronic->getLinkButton(
        __('Supprimer'),
        array('controller' => 'footers', 'action' => 'delete_link', 'admin' => true, 'id' => $idLink),
        'btn red',
        'icon-remove',
        __('Voulez-vous vraiment supprimer le lien ?')
    );