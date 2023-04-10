<style>.checker { float:left; }</style>

<?php
    echo $this->Html->script('/theme/default/js/admin_voucher', array('block' => 'script'));
    echo $this->Metronic->titlePage(__('Bon de réduction'),(isset($edit) && $edit ?__('Editer un bon de réduction'):__('Créer un bon de réduction')));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => (isset($edit) && $edit
                    ?__('Editer le coupon').' '.$this->request->data['Voucher']['title']
                    :__('Ajouter un coupon')
                ),
            'classes' => 'icon-barcode',
            'link' => $this->Html->url((isset($edit) && $edit
                    ?array('controller' => 'vouchers', 'action' => 'edit', 'admin' => true, 'code' => $this->request->data['Voucher']['code'])
                    :array('controller' => 'vouchers', 'action' => 'create', 'admin' => true)
                ))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption">
                <?php echo (isset($edit) && $edit
                    ?__('Edition du coupon').' '.$this->request->data['Voucher']['title']
                    :__('Ajout d\'un coupon')
                ); ?>
            </div>
        </div>
        <div class="portlet-body form">
            <?php
                echo $this->Form->create('Voucher', array('nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1, 'enctype' => 'multipart/form-data', 'inputDefaults' => array(
                    'div' => 'control-group',
                    'between' => '<div class="controls">',
                    'class' => 'span10',
                    'after' => '</div>'
                )));
            ?>

                <div class="row-fluid">
                    <div class="span4">
                        <?php
                            //Les inputs du formulaire
						
							 echo $this->Metronic->inputActive('Voucher', (isset($edit) && $edit ?$this->request->data['Voucher']['active']:1));
						
                            $conf = array(

                                'code'              => array('label' => array('text' => __('Code'), 'class' => 'control-label required'), 'required' => true),
                                'title'             => array('label' => array('text' => __('Titre'), 'class' => 'control-label required'), 'required' => true),
								
                                'validity_start'    => array(
                                    'label'         => array('text' => __('Début de validité'), 'class' => 'control-label required'),
                                    'required'      => true,
                                    'type'          => 'text',
                                    'maxlength'     => 16,
                                    'placeholder'   => __('JJ-MM-AAAA HH:MM'),
                                    'value'         => (isset($edit) && $edit
                                            ?$this->Time->format(Tools::dateZoneUser($this->Session->read('Config.timezone_user'),$this->request->data['Voucher']['validity_start']),'%d-%m-%Y %R')
                                            :false
                                        )
                                ),
                                'validity_end'      => array(
                                    'label'         => array('text' => __('Fin de validité'), 'class' => 'control-label required'),
                                    'required'      => true,
                                    'type'          => 'text',
                                    'maxlength'     => 16,
                                    'placeholder'   => __('JJ-MM-AAAA HH:MM'),
                                    'value'         => (isset($edit) && $edit
                                            ?$this->Time->format(Tools::dateZoneUser($this->Session->read('Config.timezone_user'),$this->request->data['Voucher']['validity_end']),'%d-%m-%Y %R')
                                            :false
                                        )
                                ),
                                'credit'            => array('label' => array('text' => __('Crédit'), 'class' => 'control-label'), 'required' => false),
                                'amount'            => array(
                                    'label' => array('text' => __('Ou montant remise'), 'class' => 'control-label'),
                                    'required' => false,

                                ),
                                'percent'            => array(
                                    'label' => array('text' => __('Ou pourcentage'), 'class' => 'control-label'),
                                    'required' => false,

                                ),
                                'label_fr'             => array('label' => array('text' => __('Texte sous titre (FR)'), 'class' => 'control-label '), 'required' => false),
								'label_be'             => array('label' => array('text' => __('Texte sous titre (BE)'), 'class' => 'control-label '), 'required' => false),
								'label_ch'             => array('label' => array('text' => __('Texte sous titre (CH)'), 'class' => 'control-label '), 'required' => false),
								'label_lu'             => array('label' => array('text' => __('Texte sous titre (LU)'), 'class' => 'control-label '), 'required' => false),
								'label_ca'             => array('label' => array('text' => __('Texte sous titre (CA)'), 'class' => 'control-label '), 'required' => false),
								
                            );

                            if (isset($edit) && $edit){
                                $conf['codebackup'] = array('type' => 'hidden', 'value' => $this->request->data['Voucher']['code']);
                            }

                            echo $this->Form->inputs($conf);
						echo '<p>Les textes ci dessus remplace l affichage automatique du nombre de minutes offerte lors d\'une promotion par Credit. Permet d afficher prix euros par exemple. Champs facultatifs</p>';
                           
                        ?>
                    </div>
                    <div class="span6">
						<fieldset>
							<legend>Valable pour</legend>
						   <?php
                        $product_ids = isset($this->request->data['Voucher']['product_ids'])?explode(",", $this->request->data['Voucher']['product_ids']):'';
                        if (!empty($products)): ?>
                            <p><?php echo __('Produits :'); ?></p>
                            <div id="list-of-products" style="display:block; clear:both; background-color:#EEE; padding:10px; max-height:200px; overflow:auto">

                                <?php
                                $allChecked = false;
                                if (isset($edit) && $edit){
                                    if (isset($this->request->data['Voucher']['product_ids']) && $this->request->data['Voucher']['product_ids'] == 'all')
                                        $allChecked = true;
                                }else{
                                    $allChecked = true;
                                }
                                echo $this->Form->input('allproducts', array('label' => array('text' => __('Tous'), 'class' => 'lbl-inline'), 'type' => 'checkbox', 'between' => false, 'after' => false, 'value'=> 'all',
                                    'checked' => $allChecked
                                ));

                                echo '<div class="lop-check" style="'.($allChecked?'display:none':'').'">';

                                $i=0;
                                foreach($products as $id => $name):
                                    echo $this->Form->input('product.'.$id, array(
                                        'label' => array('text' => $name, 'class' => 'lbl-inline'),'type' => 'checkbox', 'between' => false, 'after' => false, 'hiddenField' => false,
                                        'checked' => is_array($product_ids)?in_array($id, array_values($product_ids))?true:false:false
                                    ));
                                endforeach;

                                echo '</div>';


                                ?>
                                <div style="clear:both"></div>
                            </div>
                        <?php
                        endif;
                        ?>


                        <?php
                        $country_ids = isset($this->request->data['Voucher']['country_ids'])?explode(",", $this->request->data['Voucher']['country_ids']):'';
                        if (!empty($products)): ?>
                            <p><?php echo __('Pays :'); ?></p>
                            <div id="list-of-countries" style="display:block; clear:both; background-color:#EEE; padding:10px; max-height:200px; overflow:auto">

                                <?php
                                $allChecked = false;
                                if (isset($edit) && $edit){
                                    if (isset($this->request->data['Voucher']['country_ids']) && $this->request->data['Voucher']['country_ids'] == 'all')
                                        $allChecked = true;
                                }else{
                                    $allChecked = true;
                                }
                                echo $this->Form->input('allcountries', array('label' => array('text' => __('Tous'), 'class' => 'lbl-inline'), 'type' => 'checkbox', 'between' => false, 'after' => false, 'value'=> 'all',
                                    'checked' => $allChecked
                                ));

                                echo '<div class="lop-check" style="'.($allChecked?'display:none':'').'">';

                                $i=0;
                                foreach($countries as $id => $name):
                                    echo $this->Form->input('country.'.$id, array(
                                        'label' => array('text' => $name, 'class' => 'lbl-inline'),'type' => 'checkbox', 'between' => false, 'after' => false, 'hiddenField' => false,
                                        'checked' => is_array($country_ids)?in_array($id, array_values($country_ids))?true:false:false
                                    ));
                                endforeach;

                                echo '</div>';


                                ?>
                                <div style="clear:both"></div>
                            </div>
                        <?php
                        endif;
                        ?>
						</fieldset>
						<fieldset>
							<legend>Affichage</legend>
							<?php
								 echo $this->Form->inputs(array(
								'public'        => array(
                                    'label' => array('text' => __('Promotion visible de la totalité du public'), 'class' => 'control-label','style'=>'margin-left:-150px;width:300px;margin-top:-5px;text-align:left;'),
									'type'=>"checkbox",
									'checked' => (isset($edit) && $this->request->data['Voucher']['public'] && $this->request->data['Voucher']['public']
                                            ?true
                                            :false
                                        ),
                                    'value' => (isset($edit) && $edit
                                            ?'1'
                                            :0
                                        ),'before' => '','after' => '</div>'
                                ),
								'show'        => array(
                                    'label' => array('text' => __('Promotion appliquée automatiquement sur compte client(s)'), 'class' => 'control-label','style'=>'margin-left:-150px;width:400px;margin-top:-5px;text-align:left;'),
									'type'=>"checkbox",
									'checked' => (isset($edit) && $this->request->data['Voucher']['show'] && $this->request->data['Voucher']['show']
                                            ?true
                                            :false
                                        ),
                                    'value' => (isset($edit) && $edit
                                            ?'1'
                                            :0
                                        ),'before' => '','after' => '</div>'
                                )
									));
							
							?>
						</fieldset>
						<fieldset>
							<legend>Utilisation</legend>
							<?php
								 echo $this->Form->inputs(array(
								'number_use'        => array(
                                    'label' => array('text' => __('Utilisation'), 'class' => 'control-label'),
                                    'value' => (isset($edit) && $edit
                                            ?$this->request->data['Voucher']['number_use']
                                            :1
                                        ),'after' => '<p>'. __('0 = Illimité') .'</p></div>'
                                ),
                                'number_use_by_user'        => array(
                                    'label' => array('text' => __('Utilisation par client'), 'class' => 'control-label'),
                                    'value' => (isset($edit) && $edit
                                            ?$this->request->data['Voucher']['number_use_by_user']
                                            :1
                                        ),'after' => '<p>'. __('0 = Illimité') .'</p></div>'
                                ),
									 /*'buy_only'        => array(
                                    'label' => array('text' => __('Réduction totale par coupon de réduction remisé à 100% et sans paiement en numéraire'), 'class' => 'control-label'),
									'type'=>"checkbox",
									'options' => $options_buy, 
									'checked' => (isset($edit) && $this->request->data['Voucher']['buy_only'] && $this->request->data['Voucher']['buy_only']
                                            ?true
                                            :false
                                        ),
                                    'value' => (isset($edit) && $edit
                                            ?'1'
                                            :0
                                        ),'before' => '<div style="display:inline-block; clear:both;width:100%; background-color:#EEE; padding:10px; max-height:200px;">','after' => '</div></div>'
                                ),*/
									));
							
							?>
						</fieldset>
						<fieldset>
							<legend>Pour</legend>
							
							<?php
								//Les inputs du formulaire
								echo $this->Form->inputs(array(

									'population'        => array('type' => 'hidden', 'id' => 'list_final'),
									'add_population'    => array(
										'label' => array('text' => __('Utilisateurs pouvant utiliser ce code'),'class' => 'control-label'),
										'div' => 'control-group',
										'type' => 'text',
										'placeholder' => __('Ex: 388800,... (Code client)'),
										'id'    => 'list_customer',
										'after' => '<button id="addCustomer" type="button" href="'.$this->Html->url(array('controller' => 'vouchers', 'action' => 'addCustomer', 'admin' => true)).'" class="btn btn-sm blue-stripe margin-left">'.__('Ajouter').'</button></div>'),
									'product_ids'        => array('type' => 'hidden', 'id' => 'list_final')
								));
							?>
							<div class="control-group">
							<label for="VoucherFile" class="control-label required">Import listing clients (.csv)<br /><span style="font-size:9px">(moins de 10 000 lignes)</span></label>
							<div class="controls"><input name="data[Voucher][file]" class="span12" id="VoucherFile" type="file"><p>1 ère colonne listing code client</p></div>
							</div>

							<ul id="list_population">
							</ul>
                        	<?php
								//Les inputs du formulaire
								echo $this->Form->inputs(array(

									'customer'        => array(
                                    'label' => array('text' => __('Tous les clients'), 'class' => 'control-label','style'=>'margin-left:-150px;width:300px;margin-top:-5px;text-align:left;'),
									'type'=>"checkbox",
									'checked' => (isset($edit) && $this->request->data['Voucher']['customer'] && $this->request->data['Voucher']['customer']
                                            ?true
                                            :false
                                        ),
                                    'value' => (isset($edit) && $edit
                                            ?'1'
                                            :0
                                        ),'before' => '','after' => '</div>'
                                ),
								'buyer'        => array(
                                    'label' => array('text' => __('Tous les clients ayant deja acheté'), 'class' => 'control-label','style'=>'margin-left:-150px;width:400px;margin-top:-5px;text-align:left;'),
									'type'=>"checkbox",
									'checked' => (isset($edit) && $this->request->data['Voucher']['buyer'] && $this->request->data['Voucher']['buyer']
                                            ?true
                                            :false
                                        ),
                                    'value' => (isset($edit) && $edit
                                            ?'1'
                                            :0
                                        ),'before' => '','after' => '</div>'
                                ),
								'nobuyer'        => array(
                                    'label' => array('text' => __('Tous les clients sans achat'), 'class' => 'control-label','style'=>'margin-left:-150px;width:400px;margin-top:-5px;text-align:left;'),
									'type'=>"checkbox",
									'checked' => (isset($edit) && $this->request->data['Voucher']['nobuyer']
                                            ?true
                                            :false
                                        ),
                                    'value' => (isset($edit) && $edit
                                            ?'1'
                                            :0
                                        ),'before' => '','after' => '</div>'
                                )
								));
							?>
						</fieldset>
						<fieldset>
							<legend>Interdir</legend>
							
							<?php
								//Les inputs du formulaire
								echo $this->Form->inputs(array(


									'ips'        => array(
										'label' => array('text' => __('Liste des IP interdites ( séparé par , )'), 'class' => 'control-label'),
										'type'=>"textarea",
										'value' => (isset($edit) && $this->request->data['Voucher']['ips']
												?$this->request->data['Voucher']['ips']
												:''),
										'before' => '','after' => '</div>'
									),

									
								));
							?>
							
                        
						</fieldset>
                    </div>
                </div>

            <?php
                echo $this->Form->end(array(
                    'label' => __((isset($edit) && $edit) ?'Modifier':'Créer'),
                    'class' => 'btn blue',
                    'div' => array('class' => 'controls')
                ));
            ?>
        </div>
    </div>
</div>