<?php
    echo $this->Html->css('/assets/plugins/bootstrap-daterangepicker/daterangepicker', array('block' => 'css'));
    echo $this->Html->script('/assets/plugins/bootstrap-daterangepicker/date', array('block' => 'script'));
    echo $this->Html->script('/assets/plugins/bootstrap-daterangepicker/daterangepicker', array('block' => 'script'));
    echo $this->Html->script('/theme/default/js/nx_datepickerrange', array('block' => 'script'));
    echo $this->Metronic->titlePage(__('Agents'),__('Bilan CRM'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Crm'),
            'classes' => 'icon-user-md',
            'link' => $this->Html->url(array('controller' => 'crm', 'action' => 'index', 'admin' => true))
        ),
        2 => array(
            'text' => __('Bilan'),
            'classes' => 'icon-bullhorn',
            'link' => $this->Html->url(array('controller' => 'crm', 'action' => 'bilan', 'admin' => true))
        )
    ));

    echo $this->Session->flash();
?>

<div class="row-fluid">
    <?php echo $this->Metronic->getDateInput(); ?>
    <div class="portlet box yellow">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Bilan CRM'); ?></div>

            <?php echo $this->Metronic->getLinkButton(
                __('Export CSV'),
                array('controller' => 'crm', 'action' => 'export_bilan', 'admin' => true),
                'btn blue pull-right',
                'icon-file'
            ); ?>
        </div>
        <div class="portlet-body">
            <?php if(empty($bilan)): ?>
                <?php echo __('Pas de bilan'); ?>
            <?php else: ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
						<th>Date</th>
                        <th>Client</th>
                        <th>Code</th>
                        <th>Promo</th>
                        <th>NB</th>
						<th>Progress.</th>
                        <th>Envois</th>
                        <th>Ouvert.</th>
						<th>Tx Ouvert.</th>
						<th>Tx Convert.</th>
						<th>CA</th>
						<th>Progress.</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($bilan as $k => $row): ?>
                        <tr>
							<td><?php echo $row['date']; ?></td>
                           <td><?php echo $row['type']; ?></td>
							<td><?php echo $row['code']; ?></td>
							<td><?php echo $row['promo']; ?></td>
							<td><?php echo $row['nb']; ?></td>
							<td><?php if($row['nb_progress'])echo $row['nb_progress'].'%'; ?></td>
							<td><?php echo $row['send']; ?></td>
							<td><?php echo $row['view']; ?></td>
							<td><?php echo $row['tx_view']; ?>%</td>
							<td><?php echo $row['tx_convert']; ?>%</td>
							<td><?php echo $row['ca']; ?>€</td>
							<td><?php if($row['ca_progress'])echo $row['ca_progress'].'%'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php //if($this->Paginator->param('pageCount') > 1) echo $this->Metronic->pagination($this->Paginator); ?>
            <?php endif; ?>
        </div>
    </div>
	<fieldset>
		<legend>Légende</legend>
		<table cellpadding="10" border="1" bordercolor="#eee">
		<thead>
			<tr>
				<th>Programme CRM</th>
				<th>&nbsp;</th>
				<th>Tracker BO / GA</th>
				<th>Code promo BO</th>
				<th>Infos code promo</th>
			</tr>
			</thead>
		<tbody>
			<tr>
			<td colspan="5">ACQUISITION - ISA</td>
			</tr>
			<tr>
			<td>Inscrit mais n'ayant jamais acheté sur le site depuis 1H</td>
				
				<td>ISA1H</td>
				<td>JUST_REGISTERED_RELANCE_NO_BUY_1H</td>
				<td>ISA_1H_5MIN</td>
				<td>5 min offertes</td>
			</tr>
			<tr>
			<td>Inscrit mais n'ayant jamais acheté sur le site depuis 3H</td>
				<td>ISA3H</td>
				<td>JUST_REGISTERED_RELANCE_NO_BUY_3H</td>
			<td>ISA_3H_5MIN</td>
				<td>5 min offertes</td>
			</tr>
			<tr>
		<td>Inscrit mais n'ayant jamais acheté sur le site depuis 1J</td>
				<td>ISA1J</td>
				<td>JUST_REGISTERED_RELANCE_NO_BUY_1J</td>
		<td>ISA_1J_10MIN</td>
				<td>10 min offertes</td>
			</tr>
			<tr>
	<td>Inscrit mais n'ayant jamais acheté sur le site depuis 2J</td>
				<td>ISA2J</td>
				<td>JUST_REGISTERED_RELANCE_NO_BUY_2J</td>
<td>ISA_2J_10MIN</td>
				<td>10 min offertes</td>
			</tr>
<tr>
			<td colspan="5">FIDELITE - relance anciens clients RAC	</td>
			</tr>
			<tr>
	<td>Inscrit mais n ayant pas acheté depuis 14J</td>
				<td>RAC14J</td>
				<td>RELANCE_NO_BUY_SINCE_14J</td>
<td>RAC_14J_5MIN</td>
				<td>5 min offertes</td>
			</tr>
			<tr>
	<td>Inscrit mais n ayant pas acheté depuis 45J</td>
				<td>RAC45J</td>
				<td>RELANCE_NO_BUY_SINCE_45J</td>
<td>RAC_45J_5MIN</td>
				<td>5 min offertes</td>
			</tr>
			<tr>
			<td colspan="5">PANIERS ABANDONNES - PA		</td>
			</tr>
				<tr>
	<td>Panier abandonné depuis 30 mn</td>
				<td>PA30MN</td>
				<td>RELANCE_VISIT_PROFIL_EXPERT_LAST_VISIT</td>
<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
	<td>Panier abandonné depuis 1J</td>
				<td>PA1J</td>
				<td>RELANCE2_PANIER_NO_BUY_SINCE_LAST_VISIT</td>
<td>PA_RAC_1J_10MIN</td>
				<td>(si client a acheté entre temps) PA_ISA_1J_10MiN (si nv client)	10 min offertes</td>
			</tr>
				<tr>
	<td>Panier abandonné depuis 2J</td>
				<td>PA2J</td>
				<td>RELANCE3_PANIER_NO_BUY_SINCE_LAST_VISIT</td>
<td>PA_RAC_2J_10MIN</td>
				<td> (si client a acheté entre temps) PA_ISA_2J_10MiN (si nv client)	10 min offertes</td>
			</tr>
			</tbody>
		</table>
	</fieldset>
	
</div>