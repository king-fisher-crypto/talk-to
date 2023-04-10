<?php
    echo $this->Metronic->titlePage(__('Sales Reconciliation'),__('Modification'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
       1 => array(
            'text' => __('Modifier'),
            'classes' => 'icon-barcode',
            'link' => $this->Html->url(array('controller' => 'sales', 'action' => 'edit', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
   <?php
                echo $this->Form->create('SaleReconciliation', array('nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1, 'inputDefaults' => array(
                    'div' => 'control-group',
                    'between' => '<div class="controls">',
                    'after' => '</div>',
                    'class' => 'span8'
                )));
            ?>

            <div class="row-fluid">
                <div class="span12">
                        <?php
                            //Les inputs du formulaire
                           $conf = array(
                                'invoice_agent'   => array(
										'label' => array('text' => __('Total Facture'), 'class' => 'col-sm-12 col-md-4 control-label required disabled'),
										'value' => $order['SaleReconciliation']['invoice_agent'],
										'required' => true,
									),
							   'vat_invoice_agent'   => array(
										'label' => array('text' => __('Total TVA'), 'class' => 'col-sm-12 col-md-4 control-label required disabled'),
										'value' => $order['SaleReconciliation']['vat_invoice_agent'],
										'required' => true,
									),
							   'credit_note'   => array(
										'label' => array('text' => __('Total avoir'), 'class' => 'col-sm-12 col-md-4 control-label required disabled'),
										'value' => $order['SaleReconciliation']['credit_note'],
										'required' => true,
									),
							   'owed_agent'   => array(
										'label' => array('text' => __('Total transfert expert'), 'class' => 'col-sm-12 col-md-4 control-label required disabled'),
										'value' => $order['SaleReconciliation']['owed_agent'],
										'required' => true,
									),
							   'error_agent'   => array(
										'label' => array('text' => __('Total erreur expert'), 'class' => 'col-sm-12 col-md-4 control-label required disabled'),
										'value' => $order['SaleReconciliation']['error_agent'],
										'required' => true,
									),
							   'bankwire_agent'   => array(
										'label' => array('text' => __('Total virement expert'), 'class' => 'col-sm-12 col-md-4 control-label required disabled'),
										'value' => $order['SaleReconciliation']['bankwire_agent'],
										'required' => true,
									),
							   'stripe'   => array(
										'label' => array('text' => __('Total Stripe'), 'class' => 'col-sm-12 col-md-4 control-label required disabled'),
										'value' => $order['SaleReconciliation']['stripe'],
										'required' => true,
									),
							   'paypal'   => array(
										'label' => array('text' => __('Total Paypal'), 'class' => 'col-sm-12 col-md-4 control-label required disabled'),
										'value' => $order['SaleReconciliation']['paypal'],
										'required' => true,
									),
							   'unused_credit'   => array(
										'label' => array('text' => __('Total credits non utilisé'), 'class' => 'col-sm-12 col-md-4 control-label required disabled'),
										'value' => $order['SaleReconciliation']['unused_credit'],
										'required' => true,
									),
							   'currency_diff'   => array(
										'label' => array('text' => __('Total conversion devise'), 'class' => 'col-sm-12 col-md-4 control-label required disabled'),
										'value' => $order['SaleReconciliation']['currency_diff'],
										'required' => true,
									),
							    'premium_number'   => array(
										'label' => array('text' => __('Total audiotel'), 'class' => 'col-sm-12 col-md-4 control-label required disabled'),
										'value' => $order['SaleReconciliation']['premium_number'],
										'required' => true,
									),
                            );

                            echo $this->Form->inputs($conf);
                    ?>
                </div>
                
            </div>

<div class="row-fluid">
	<input name="data[SaleReconciliation][id]" value="<?=$order['SaleReconciliation']['id'] ?>" type="hidden" id="SaleReconciliationId">
    <?php
        echo $this->Form->end(array(
            'label' => __('Enregistrer'),
            'class' => 'btn blue rfloat',
            'div' => array('class' => 'controls')
        ));
    ?>
</div>