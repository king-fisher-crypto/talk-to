<?php
    echo $this->Metronic->titlePage(__('Facture'),__('Création d\'une facture'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Ajouter une facture'),
            'classes' => 'icon-barcode',
            'link' => $this->Html->url(array('controller' => 'invoices', 'action' => 'create', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption">
                <?php echo __('Ajout d\'une facture'); ?>
            </div>
        </div>
        <div class="portlet-body form">
            <?php
                echo $this->Form->create('Invoices', array('nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1, 'inputDefaults' => array(
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
                                'society_id'   => array(
										'label' => array('text' => __('Société'), 'class' => 'col-sm-12 col-md-4 control-label required'),
										'options' => $select_society,
										'required' => true,
									),
							    'customer_id'   => array(
										'label' => array('text' => __('Client'), 'class' => 'col-sm-12 col-md-4 control-label required'),
										'options' => $select_customer,
										'required' => true,
										'after' => '</div><a href="/admin/invoices/create_customer" style="padding-left:15px;"> + Ajouter</a>'
									),
                                'date_order'   => array(
										'label' => array('text' => __('Date facture'), 'class' => 'col-sm-12 col-md-4 control-label required'),
										'required' => true,
										'placeholder' => 'JJ-MM-AAAA'
									),
							 /*  'date_due'   => array(
										'label' => array('text' => __('Date echeance'), 'class' => 'col-sm-12 col-md-4 control-label required'),
										'required' => true,
										'placeholder' => 'JJ-MM-AAAA'
									),*/
							   'vat_tx'   => array(
										'label' => array('text' => __('TVA'), 'class' => 'col-sm-12 col-md-4 control-label required'),
										'required' => true,
										'options' => array(0 => 0,5 => 5,10 => 10,17 => 17,20 => 20,21 => 21,23 => 23),
									),
							   
							   'currency'   => array(
										'label' => array('text' => __('Monnaie facture'), 'class' => 'col-sm-12 col-md-4 control-label required'),
										'required' => true,
								   		'options' => array('€' => '€','$' => '$','CHF' => 'CHF'),
									),
							   'mode'   => array(
										'label' => array('text' => __('Mode paiement'), 'class' => 'col-sm-12 col-md-4 control-label required'),
										'required' => true,
										'options' => array('Virement' => 'Virement','Cheque' => 'Cheque','Paypal' => 'Paypal'),
									),
							   'conditions'   => array(
										'label' => array('text' => __('Condition paiement'), 'class' => 'col-sm-12 col-md-4 control-label'),
										'required' => false
									),
							   'remarque'   => array(
										'label' => array('text' => __('Remarque'), 'class' => 'col-sm-12 col-md-4 control-label'),
										'required' => false,'type'=>'textarea'
									),
							   'deposit'   => array(
										'label' => array('text' => __('Acompte ?'), 'class' => 'col-sm-12 col-md-4 control-label'),
										'required' => false,
									),
                            );

                            echo $this->Form->inputs($conf);
                    ?>
                </div>
                
            </div>
			
			<div class="row-fluid">
                <div class="span12">
                	<table class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>#</th><th>Nom</th><th>Qte</th><th>Montant unitaire HT</th>
							</tr>
						</thead>
						<tbody>
							<?php for($nn=1;$nn<=$nb_product;$nn++){ ?>
							 <tr>
								 <td><?=$nn ?></td>
								 <td>
									 <textarea name="data[Invoices][ProductName<?=$nn ?>]"  class="span12" id="InvoicesProductName<?=$nn ?>" Placeholder="Libéllé"></textarea>
								</td>
								 <td><input name="data[Invoices][ProductQty<?=$nn ?>]"  type="text" id="InvoicesProductQty<?=$nn ?>" Placeholder="Qte"></td>
								 <td><input name="data[Invoices][ProductPrice<?=$nn ?>]"  type="text" id="InvoicesProductPrice<?=$nn ?>" Placeholder="Montant"></td>
							 </tr>
							<?php } ?>
						</tbody>	  
					</table>		  
                </div>
                
            </div>
			<input name="data[Invoices][preview]" value="0" type="hidden" id="InvoicesPreview">
			<input class="btn yellow lfloat preview_invoice" type="button" value="Preview">
            <?php
                echo $this->Form->end(array(
                    'label' => __('Créer'),
                    'class' => 'btn blue',
                    'div' => array('class' => 'controls submit_invoice')
                ));
            ?>
        </div>
    </div>
</div>