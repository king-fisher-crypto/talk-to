<?php
echo $this->Metronic->titlePage(__('Statement against Stripe'));
echo $this->Metronic->breadCrumb(array(
    0 => array(
        'text' => __('Accueil'), 'classes' => 'icon-home', 'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
    ),
    1 => array(
        'text' => __('Incident Stripe'), 'classes' => 'icon-euro'
    )
));

echo $this->Session->flash();


?>
<div class="row-fluid">
    <div class="portlet box yellow">
        <div class="portlet-title">
            <div class="caption">Stripe declaration</div>
		
        </div>
        <div class="portlet-body">
			<div class="row-fluid">
				<div class="span4">
					<fieldset>
						<legend>Stripe Transaction </legend>
						<p><strong>Transaction Date :</strong> <?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$order['Order']['date_add']),'%Y-%m-%d %H:%M'); ?></p>
						<p><strong> ID :</strong> <?php echo $order['stripe_logs']['id']; ?></p>
						 <p><strong>Payment method :</strong> <?php echo $order['stripe_logs']['payment_method']; ?></p>
					</fieldset>
				</div>
				<div class="span4">
					<fieldset>
						<legend>Transaction Spiriteo</legend>
						<p><strong>Client : </strong> <?php echo $this->Html->link($order['User']['firstname'],
                                            array(
                                                'controller' => 'accounts',
                                                'action' => 'view',
                                                'admin' => true,
                                                'id' => $order['User']['id']
                                            ),
                                            array('class' => '', 'escape' => false)
                                        ); ?></p>
						<p><strong>Client email : </strong> <?php echo $order['User']['email']; ?></p>
						<p><strong>Client phone : </strong> <?php echo $order['User']['phone_number']; ?></p>
						 <p><strong>Amount : </strong> <?php echo number_format($order['Order']['total'],2).' '.$order['Order']['currency']; ?></p>
						<p><strong>Credits : </strong> <?php echo $order['Order']['product_credits']; ?></p>
						<p><strong>Seconds : </strong> <?php echo $order['Order']['product_credits']; ?></p>
					</fieldset>
				</div>
				<div class="span4">
					<fieldset>
						<legend>Informations</legend>
						<textarea id="PaypalInfoOpposition" class="input  margin-left margin-right" style="width:80%" rel="<?php echo $order['paypal_logs']['order_id']; ?>" placeholder="Infos complémentaires"  rows="6" ><?php echo $order['paypal_logs']['comments']; ?></textarea>
					</fieldset>
				</div>
			</div>
			<div class="row-fluid"><span style="display:inline-block;float:left;">Cochez l'un de ces modes :</span>
				<div class="" style="margin-bottom:25px;display:inline-block;width:auto">
					<ul class="" style="list-style: none;">
						<li class="mode the_mode_phone" style="float:left;margin-right: 5px;"><img src="/theme/default/img/icons/phone_color.png" data-toggle="tooltip" data-placement="top" title="" alt="Spiriteo - agents par téléphone" data-original-title="agents par Téléphone"></li>
						<li class="mode the_mode_tchat" style="float:left;margin-right: 5px;"><img src="/theme/default/img/icons/chat_color.png" data-toggle="tooltip" data-placement="top" title="" alt="Spiriteo - agents par tchat" data-original-title="agents par Chat"></li>
						<li class="mode the_mode_email" style="float:left;margin-right: 5px;"><img src="/theme/default/img/icons/email_color.png" data-toggle="tooltip" data-placement="top" title="" alt="Spiriteo - agents par email" data-original-title="agents par E-mail"></li>
					</ul>
				</div>
				<div class="" >
					<fieldset id="mode_email" style="display:none;">
						<legend>Mode Email</legend>
						<p style="background:#efefef;width:90%;padding:10px;">Spiriteo provides the customers with different communication modes i.e. Telephone, Chat and E-mail to contact any experts he wants.<br /> 
In the following example the customer has used the E-Mail mode. Every customer has the option between 5 different communication packages from €19,90 for 10 minutes of communication to 90 minutes for €150. For instance the first package at 19,90€ represents one email of communication with any expert.<br /> 
All customers can use their credits purchased online in one time or several times. <br >
As soon as the customer signs up he is invited to purchase one of the available packages. No one can contact an expert by Email without creating an account. As soon as the customer has registered and purchased an account he can start his E-mail communication with any experts he wants. Each email is debited from his package.  We can track any emails sent by any customers.
</p>
					</fieldset>
					<fieldset id="mode_phone"  style="display:none;">
						<legend>Mode Phone</legend>
						<p style="background:#efefef;width:90%;padding:10px;">Spiriteo provides the customers with different communication modes i.e. Telephone, Chat and E-mail to contact any experts he wants.<br >
In the following example the customer has used the telephone mode. Every customer has the option between 5 different communication packages from €19,90 for 10 minutes of communication to 90 minutes for €150<br >
All customers can use their credits purchased online in one time or several times. <br >
As soon as the customer signs up he is invited to purchase one of the available packages. No one can contact an expert by telephone without creating an account. As soon as the customer has registered and purchased an account he can start his phone communication with any experts he wants. Each minute over the phone is debited from his package. <br >
So we always knows where he is  and what he does. We can track any emails sent by any customers.
</p>
					</fieldset>
					<fieldset id="mode_tchat"  style="display:none;">
						<legend>Mode Tchat</legend>
						<p style="background:#efefef;width:90%;padding:10px;">Spiriteo provides the customers with different communication modes i.e. Telephone, Chat and E-mail to contact any experts he wants.<br /> 
In the following example the customer has used the Tchat mode. Every customer has the option between 5 different communication packages from €19,90 for 10 minutes of communication to 90 minutes for €150<br /> 
All customers can use their credits purchased online in one time or several times. <br /> 
As soon as the customer signs up he is invited to purchase one of the available packages. No one can contact an expert by Tchat without creating an account. As soon as the customer has registered and purchased an account he can start his Tchat communication with any experts he wants. Each minute over the tchat is debited from his package. <br /> 
So we always knows where is he and what he does. Besides the proof of account related to a name we can see the historical exchange between this account and the experts. We can track any emails sent by any customers.
</p>
					</fieldset>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span12">
					<fieldset>
						<legend>Service benefits consumed after purchase :</legend>
					 	<table class="table table-striped table-hover table-bordered">
							<thead>
							<tr>
								<th>Expert</th>
								<th>Media</th>
								<th>Communication cost.</th>
								<th>Time in seconds</th>
								<th>Date</th>
							</tr>
							</thead>
                    		<tbody>
								<?php
								foreach($comm as $com){
								?>
								<tr>
									<td><?php echo $com['UserCreditLastHistory']['agent_pseudo'] ?></td>
									<td><?php echo $com['UserCreditLastHistory']['media'] ?></td>
									<td><?php echo $com['UserCreditLastHistory']['credits'] ?> credits</td>
									<td><?php echo $com['UserCreditLastHistory']['seconds'] ?> sec.</td>
									<td><?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$com['UserCreditLastHistory']['date_start']),'%Y-%m-%d %H:%M'); ?></td>
								</tr>
								
								<?php
								}
								?>
							</tbody>
                		</table>
					</fieldset>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span12">
					<fieldset>
						<legend>Client history before this purchase problem :</legend>
					 	<table class="table table-striped table-hover table-bordered">
							<thead>
							<tr>
								<th>Expert</th>
								<th>Media</th>
								<th>Communication cost.</th>
								<th>Time in seconds</th>
								<th>Date</th>
							</tr>
							</thead>
                    		<tbody>
								<?php
								foreach($comm_old as $com){
								?>
								<tr>
									<td><?php echo $com['UserCreditLastHistory']['agent_pseudo'] ?></td>
									<td><?php echo $com['UserCreditLastHistory']['media'] ?></td>
									<td><?php echo $com['UserCreditLastHistory']['credits'] ?> credits</td>
									<td><?php echo $com['UserCreditLastHistory']['seconds'] ?> sec.</td>
									<td><?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$com['UserCreditLastHistory']['date_start']),'%Y-%m-%d %H:%M'); ?></td>
								</tr>
								
								<?php
								}
								?>
							</tbody>
                		</table>
					</fieldset>
				</div>
			</div>
        </div>
    </div>
</div>