<?php
echo $this->Metronic->titlePage('Backoffice',__('Zone d\'administration'));
echo $this->Metronic->breadCrumb(array(0 => array('text' => 'Accueil', 'classes' => 'icon-home')));
echo $this->Html->script('/theme/default/js/chart.min', array('block' => 'script'));



?>


<?php
$dashboard = array();

$dashboard[] = array(
    'color'  => ($badge['Agent']['compte']>0)?'red':'blue',
    'title1' => 'Agents attente',
    'title2' => $badge['Agent']['compte']>0?$badge['Agent']['compte'].' en attente':'Aucun',
    'link'   => array(
        'controller' => 'agents',
        'action' => 'index',
        'admin' => true,
        '?' => 'compte'
    ),
    'link_title' => 'Voir les agents en attente de validation',
    'icon'   => 'icon-user-md'
);
$dashboard[] = array(
    'color'  => 'blue',
    'title1' => 'Non validés',
    'title2' => '0 aujourd\'hui',
    'link'   => array(
        'controller' => 'accounts',
        'action' => 'index',
        'admin' => true,
        '?' => 'compte'
    ),
    'link_title' => 'Voir les comptes clients non validés',
    'icon'   => 'icon-user'
);
$dashboard[] = array(
    'color'  => ($badge['Review']['count']>0)?'red':'blue',
    'title1' => 'Avis attente',
    'title2' => $badge['Review']['count']>0?$badge['Review']['count'].' en attente':'Aucun',
    'link'   => array(
        'controller' => 'reviews',
        'action' => 'index',
        'admin' => true
    ),
    'link_title' => 'Voir les avis en attente',
    'icon'   => 'icon-comments'
);






$dashboard[] = array(
    'color'  => 'green',
    'title1' => 'Clients',
    'title2' => $badge['Client']['count'],
    'link'   => array(
        'controller' => 'accounts',
        'action' => 'index',
        'admin' => true
    ),
    'link_title' => 'Voir les clients valides',
    'icon'   => 'icon-user'
);
$dashboard[] = array(
    'color'  => 'green',
    'title1' => 'Agents',
    'title2' => $badge['Agent']['count']>0?$badge['Agent']['count']:'Aucun',
    'link'   => array(
        'controller' => 'agents',
        'action' => 'index',
        'admin' => true
    ),
    'link_title' => 'Voir les agents valides',
    'icon'   => 'icon-user-md'
);
$dashboard[] = array(
    'color'  => 'green',
    'title1' => 'Avis client',
    'title2' => $badge['Review']['online']>0?$badge['Review']['online']:'Aucun',
    'link'   => array(
        'controller' => 'reviews',
        'action' => 'index',
        'admin' => true,
        '?' => 'online'
    ),
    'link_title' => 'Voir les avis clients en ligne',
    'icon'   => 'icon-comments'
);

  ?>
<div class="row-fluid">
    <?php foreach ($dashboard AS $k => $row): ?>
        <div data-desktop="span4" class="span4 responsive">
            <div class="dashboard-stat <?php echo $row['color']; ?>">
                <div class="visual"><?php echo !empty($row['icon'])?'<i class="'.$row['icon'].'"></i> ':''; ?></div>
                <div class="details">
                    <div class="number"><?php echo __($row['title1']); ?></div>
                    <div class="desc"><?php echo __($row['title2']); ?></div>
                </div>
                <?php
				if($level == 'admin')
                echo $this->Html->link($row['link_title'].' <i class="m-icon-swapright m-icon-white"></i>', $row['link'], array(
                    'class' => 'more',
                    'escape' => false
                ));

                ?>
            </div>
        </div>
        <?php if (($k+1)%3 == 0): ?>
            </div><div class="row-fluid">
        <?php endif; ?>
    <?php endforeach; ?>
</div>

<div class="row-fluid">
    <div class="portlet box yellow">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Informations constantes du site.'); ?></div>
        </div>
        <div class="portlet-body">
            <table class="table table-striped table-hover table-bordered table_center">
                <thead>
                <tr>
                    <th><?php echo __('Nom de la constante'); ?></th>
                    <th><?php echo __('Valeur de la constante'); ?></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><?php echo __('Nombre de jours de fonctionnement d\'une alerte client pour la disponibilité d\'un agent'); ?></td>
                    <td><?php echo Configure::read('Site.alerts.days'); ?></td>
                </tr>
                <tr>
                    <td><?php echo __('Le nombre de seconde pour 1 crédit'); ?></td>
                    <td><?php echo Configure::read('Site.secondePourUnCredit'); ?></td>
                </tr>
                <tr>
                    <td><?php echo __('Nombre de crédit par défaut pour une consultation par mail'); ?></td>
                    <td><?php echo Configure::read('Site.creditPourUnMail'); ?></td>
                </tr>
                <tr>
                    <td><?php echo __('Nombre de crédit minimum que doit avoir un client pour une consultation par chat '); ?></td>
                    <td><?php echo Configure::read('Chat.creditMinPourChat'); ?></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
