<?php
    echo $this->Metronic->titlePage(__('Facture'),__('Modification d\'une facture'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
       1 => array(
            'text' => __('Modifier une facture'),
            'classes' => 'icon-barcode',
            'link' => $this->Html->url(array('controller' => 'invoices', 'action' => 'edit', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
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
										'label' => array('text' => __('Société'), 'class' => 'col-sm-12 col-md-4 control-label required disabled'),
										'options' => $select_society,
										'value' => $invoice['InvoiceOther']['society_id'],
										'required' => true,
										'readonly' => 'readonly'
									),
							    'customer_id'   => array(
										'label' => array('text' => __('Client'), 'class' => 'col-sm-12 col-md-4 control-label required'),
										'options' => $select_customer,
										'required' => true,
										'value' => $invoice['InvoiceOther']['customer_id'],
									),
                                'date_order'   => array(
										'label' => array('text' => __('Date facture'), 'class' => 'col-sm-12 col-md-4 control-label required'),
										'required' => true,
										'value' => $this->Time->format(Tools::dateZoneUser($this->Session->read('Config.timezone_user'),$invoice['InvoiceOther']['date_order']),'%d-%m-%Y'),
									),
							   /*	'date_due'   => array(
										'label' => array('text' => __('Date echeance'), 'class' => 'col-sm-12 col-md-4 control-label required'),
										'required' => true,
										'value' => $this->Time->format(Tools::dateZoneUser($this->Session->read('Config.timezone_user'),$invoice['InvoiceOther']['date_due']),'%d-%m-%Y'),
									),*/
							   'vat_tx'   => array(
										'label' => array('text' => __('TVA'), 'class' => 'col-sm-12 col-md-4 control-label required'),
										'required' => true,
										'options' => array(0 => 0,5 => 5,10 => 10,17 => 17,20 => 20,21 => 21,23 => 23),
								   		'value' => $invoice['InvoiceOther']['vat_tx'],
									),
							   
							   'currency'   => array(
										'label' => array('text' => __('Monnaie facture'), 'class' => 'col-sm-12 col-md-4 control-label required'),
										'required' => true,
								   		'options' => array('€' => '€','$' => '$','CHF' => 'CHF'),
								   		'value' => $invoice['InvoiceOther']['currency'],
									),
							   'mode'   => array(
										'label' => array('text' => __('Mode paiement'), 'class' => 'col-sm-12 col-md-4 control-label required'),
										'required' => true,
										'options' => array('Virement' => 'Virement','Cheque' => 'Cheque','Paypal' => 'Paypal'),
									),
							   'conditions'   => array(
										'label' => array('text' => __('Condition paiement'), 'class' => 'col-sm-12 col-md-4 control-label'),
										'required' => false,'value' => $invoice['InvoiceOther']['conditions']
									),
							   'remarque'   => array(
										'label' => array('text' => __('Remarque'), 'class' => 'col-sm-12 col-md-4 control-label'),
										'required' => false,'type'=>'textarea','value' => $invoice['InvoiceOther']['remarque'],
									),
							   'deposit'   => array(
										'label' => array('text' => __('Acompte ?'), 'class' => 'col-sm-12 col-md-4 control-label'),
										'required' => false,'value' => $invoice['InvoiceOther']['deposit'],
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
								<th>#</th><th>Nom</th><th>Qte</th><th>Montant HT</th>
							</tr>
						</thead>
						<tbody>
							<?php $nn=1; foreach($details as $detail){ ?>
							 <tr>
								 <td><?=$nn ?></td>
								 <td><textarea name="data[Invoices][ProductName<?=$nn ?>]"  class="span12" id="InvoicesProductName<?=$nn ?>"><?=$detail['InvoiceOtherDetail']['label']  ?></textarea></td>
								 <td><input name="data[Invoices][ProductQty<?=$nn ?>]"  type="text" id="InvoicesProductQty<?=$nn ?>" Placeholder="Qte" value="<?=$detail['InvoiceOtherDetail']['qty']  ?>"></td>
								 <td><input name="data[Invoices][ProductPrice<?=$nn ?>]"  type="text" id="InvoicesProductPrice<?=$nn ?>" Placeholder="Montant" value="<?=$detail['InvoiceOtherDetail']['amount']  ?>"></td>
							 </tr>
							<?php $nn++; } ?>
							<?php
							if($nn < 37){
								for($nn2=$nn;$nn2<=$nb_product;$nn2++){ ?>
							 <tr>
								 <td><?=$nn2 ?></td>
								 <td><textarea name="data[Invoices][ProductName<?=$nn2 ?>]"  class="span12" id="InvoicesProductName<?=$nn2 ?>" Placeholder="Libéllé"></textarea></td>
								 <td><input name="data[Invoices][ProductQty<?=$nn2 ?>]"  type="text" id="InvoicesProductQty<?=$nn2 ?>" Placeholder="Qte"></td>
								 <td><input name="data[Invoices][ProductPrice<?=$nn2 ?>]"  type="text" id="InvoicesProductPrice<?=$nn2 ?>" Placeholder="Montant"></td>
							 </tr>
							<?php } 
								
							}
							
							?>
						</tbody>	  
					</table>		  
                </div>
                
            </div>
<div class="row-fluid">
	<input name="data[Invoices][preview]" value="0" type="hidden" id="InvoicesPreview">
	<input class="btn yellow lfloat preview_invoice" type="button" value="Preview">
    <?php
        echo $this->Form->end(array(
            'label' => __('Enregistrer'),
            'class' => 'btn blue rfloat submit_invoice',
            'div' => array('class' => 'controls')
        ));
    ?>
</div>