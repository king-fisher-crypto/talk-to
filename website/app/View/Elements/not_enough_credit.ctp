<div style="text-align:center">
    <p style="float:left"><?php echo __('Vous n\'avez pas assez de crédit pour une consultation par '.$consult.' avec').' '.$pseudo; ?></p>
    <?php
	
	 if(isset($minCredit))
            echo '<p style="float:left;clear:both">'.__('Il faut').' '.$minCredit.' '.__('crédits minimum pour une consultation par '.$consult).'</p>';
	
	 echo '<p style="float:left;clear:both">'.__('Pensez à recharger votre crédit').'</p>';
	 
	 echo '<p style="clear:both"></p>';
	 
	 echo '<a href="'.$this->Frontblock->getProductsLink().'" class="btn-link">'.__('RECHARGER MON COMPTE').'</a>';
	
        /*echo $this->Html->link(__('RECHARGER MON COMPTE'), array(
            'controller' => 'accounts',
            'action' => 'buycredits',
			
        ),array('class' => 'btn-link'));*/

       
    ?>
</div>