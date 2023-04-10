<?php
echo $this->Metronic->titlePage('Backoffice',__('Dashboard'));
echo $this->Html->css('/assets/plugins/bootstrap-daterangepicker/daterangepicker', array('block' => 'css'));
    echo $this->Html->script('/assets/plugins/bootstrap-daterangepicker/date', array('block' => 'script'));
    echo $this->Html->script('/assets/plugins/bootstrap-daterangepicker/daterangepicker', array('block' => 'script'));
    echo $this->Html->script('/theme/default/js/nx_datepickerrange', array('block' => 'script'));
echo $this->Metronic->breadCrumb(array(0 => array('text' => 'Accueil', 'classes' => 'icon-home')));
echo $this->Html->script('/theme/default/js/chart.min', array('block' => 'script'));


?>
<div class="row-fluid">
<?php echo $this->Metronic->getDateInput(); ?>
</div>
<div class="row-fluid">
   <div class="span6 responsive">
		<div class="portlet box yellow" >
			<div class="portlet-title">
				<div class="caption"><?php echo __('Nouveaux clients (achats : prepaye)'); ?></div>
			</div>
			<div class="portlet-body">
				<ul class="nav nav-tabs">
				  <li class="active"><a  style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_new_customer_buy_custom" data-toggle="tab">Perso</a></li>
				  <li ><a  style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_new_customer_buy_today" data-toggle="tab">Aujourd'hui</a></li>
				  <li><a  style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_new_customer_buy_yesterday" data-toggle="tab">Hier</a></li>
				  <li><a  style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_new_customer_buy_prev_day" data-toggle="tab">J-2</a></li>
				  <li><a  style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_new_customer_buy_prev_week" data-toggle="tab">J-7</a></li>
				  <li><a  style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_new_customer_buy_month" data-toggle="tab">Mois</a></li>
				  <li><a  style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_new_customer_buy_prev_month" data-toggle="tab">Mois dernier</a></li>
				  <li><a  style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_new_customer_buy_prev2_month" data-toggle="tab">Mois -2</a></li>
				  <li><a  style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_new_customer_buy_prev_year" data-toggle="tab">J N-1</a></li>
				  <li><a  style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_new_customer_buy_prev_month_year" data-toggle="tab">M N-1</a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane fade in active" id="tab_canvas_new_customer_buy_custom"><canvas id="canvas_new_customer_buy_custom"></canvas></div>
					<div class="tab-pane fade " id="tab_canvas_new_customer_buy_today"><canvas id="canvas_new_customer_buy_today"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_new_customer_buy_yesterday"><canvas id="canvas_new_customer_buy_yesterday"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_new_customer_buy_prev_day"><canvas id="canvas_new_customer_buy_prev_day"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_new_customer_buy_prev_week"><canvas id="canvas_new_customer_buy_prev_week"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_new_customer_buy_prev_year"><canvas id="canvas_new_customer_buy_prev_year"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_new_customer_buy_month"><canvas id="canvas_new_customer_buy_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_new_customer_buy_prev_month"><canvas id="canvas_new_customer_buy_prev_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_new_customer_buy_prev2_month"><canvas id="canvas_new_customer_buy_prev2_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_new_customer_buy_prev_month_year"><canvas id="canvas_new_customer_buy_prev_month_year"></canvas></div>
				</div>
			</div>
		</div>
	</div>
	<div class="span6 responsive">
		<div class="portlet box green" >
			<div class="portlet-title">
				<div class="caption"><?php echo __('Crédits achetés'); ?></div>
			</div>
			<div class="portlet-body">
				<ul class="nav nav-tabs">
				  <li class="active"><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_buy_custom" data-toggle="tab">Perso</a></li>
				  <li class=""><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_buy_today" data-toggle="tab">Aujourd'hui</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_buy_yesterday" data-toggle="tab">Hier</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_buy_prev_day" data-toggle="tab">J-2</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_buy_prev_week" data-toggle="tab">J-7</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_buy_month" data-toggle="tab">Mois</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_buy_prev_month" data-toggle="tab">Mois dernier</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_buy_prev2_month" data-toggle="tab">Mois -2</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_buy_prev_year" data-toggle="tab">J N-1</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_buy_prev_month_year" data-toggle="tab">M N-1</a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane fade in active" id="tab_canvas_buy_custom"><canvas id="canvas_buy_custom"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_buy_today"><canvas id="canvas_buy_today"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_buy_yesterday"><canvas id="canvas_buy_yesterday"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_buy_prev_day"><canvas id="canvas_buy_prev_day"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_buy_prev_week"><canvas id="canvas_buy_prev_week"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_buy_prev_year"><canvas id="canvas_buy_prev_year"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_buy_month"><canvas id="canvas_buy_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_buy_prev_month"><canvas id="canvas_buy_prev_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_buy_prev2_month"><canvas id="canvas_buy_prev2_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_buy_prev_month_year"><canvas id="canvas_buy_prev_month_year"></canvas></div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="row-fluid">
   <div class="span6 responsive">
		<div class="portlet box blue" >
			<div class="portlet-title">
				<div class="caption"><?php echo __('Nouveaux clients (consultations : prepaye + audiotel)'); ?></div>
			</div>
			<div class="portlet-body">
				<ul class="nav nav-tabs">
				  <li class="active"><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_new_customer_consult_custom" data-toggle="tab">Perso</a></li>
				  <li class=""><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_new_customer_consult_today" data-toggle="tab">Aujourd'hui</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_new_customer_consult_yesterday" data-toggle="tab">Hier</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_new_customer_consult_prev_day" data-toggle="tab">J-2</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_new_customer_consult_prev_week" data-toggle="tab">J-7</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_new_customer_consult_month" data-toggle="tab">Mois</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_new_customer_consult_prev_month" data-toggle="tab">Mois dernier</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_new_customer_consult_prev2_month" data-toggle="tab">Mois -2</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_new_customer_consult_prev_year" data-toggle="tab">J N-1</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_new_customer_consult_prev_month_year" data-toggle="tab">M N-1</a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane fade in active" id="tab_canvas_new_customer_consult_custom"><canvas id="canvas_new_customer_consult_custom"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_new_customer_consult_today"><canvas id="canvas_new_customer_consult_today"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_new_customer_consult_yesterday"><canvas id="canvas_new_customer_consult_yesterday"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_new_customer_consult_prev_day"><canvas id="canvas_new_customer_consult_prev_day"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_new_customer_consult_prev_week"><canvas id="canvas_new_customer_consult_prev_week"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_new_customer_consult_prev_year"><canvas id="canvas_new_customer_consult_prev_year"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_new_customer_consult_month"><canvas id="canvas_new_customer_consult_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_new_customer_consult_prev_month"><canvas id="canvas_new_customer_consult_prev_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_new_customer_consult_prev2_month"><canvas id="canvas_new_customer_consult_prev2_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_new_customer_consult_prev_month_year"><canvas id="canvas_new_customer_consult_prev_month_year"></canvas></div>
				</div>
			</div>
		</div>
	</div>
	<div class="span6 responsive">
		<div class="portlet box red" >
			<div class="portlet-title">
				<div class="caption"><?php echo __('Crédits consommés'); ?></div>
			</div>
			<div class="portlet-body">
				<ul class="nav nav-tabs">
				  <li class="active"><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_custom" data-toggle="tab">Perso</a></li>
				  <li class=""><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_today" data-toggle="tab">Aujourd'hui</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_yesterday" data-toggle="tab">Hier</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_prev_day" data-toggle="tab">J-2</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_prev_week" data-toggle="tab">J-7</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_month" data-toggle="tab">Mois</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_prev_month" data-toggle="tab">Mois dernier</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_prev2_month" data-toggle="tab">Mois -2</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_prev_year" data-toggle="tab">J N-1</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_prev_month_year" data-toggle="tab">M N-1</a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane fade in active" id="tab_canvas_consult_custom"><canvas id="canvas_consult_custom"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_today"><canvas id="canvas_consult_today"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_yesterday"><canvas id="canvas_consult_yesterday"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_prev_day"><canvas id="canvas_consult_prev_day"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_prev_week"><canvas id="canvas_consult_prev_week"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_prev_year"><canvas id="canvas_consult_prev_year"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_month"><canvas id="canvas_consult_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_prev_month"><canvas id="canvas_consult_prev_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_prev2_month"><canvas id="canvas_consult_prev2_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_prev_month_year"><canvas id="canvas_consult_prev_month_year"></canvas></div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="row-fluid">
   <div class="span6 responsive">
		<div class="portlet box blue" >
			<div class="portlet-title">
				<div class="caption"><?php echo __('Nouveaux clients par source'); ?></div>
			</div>
			<div class="portlet-body">
				<ul class="nav nav-tabs">
				  <li class="active"><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_new_customer_source_custom" data-toggle="tab">Perso</a></li>
				  <li class=""><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_new_customer_source_today" data-toggle="tab">Aujourd'hui</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_new_customer_source_yesterday" data-toggle="tab">Hier</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_new_customer_source_prev_day" data-toggle="tab">J-2</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_new_customer_source_prev_week" data-toggle="tab">J-7</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_new_customer_source_month" data-toggle="tab">Mois</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_new_customer_source_prev_month" data-toggle="tab">Mois dernier</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_new_customer_source_prev2_month" data-toggle="tab">Mois -2</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_new_customer_source_prev_year" data-toggle="tab">J N-1</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_new_customer_source_prev_month_year" data-toggle="tab">M N-1</a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane fade in active" id="tab_canvas_new_customer_source_custom"><canvas id="canvas_new_customer_source_custom"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_new_customer_source_today"><canvas id="canvas_new_customer_source_today"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_new_customer_source_yesterday"><canvas id="canvas_new_customer_source_yesterday"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_new_customer_source_prev_day"><canvas id="canvas_new_customer_source_prev_day"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_new_customer_source_prev_week"><canvas id="canvas_new_customer_source_prev_week"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_new_customer_source_prev_year"><canvas id="canvas_new_customer_source_prev_year"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_new_customer_source_month"><canvas id="canvas_new_customer_source_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_new_customer_source_prev_month"><canvas id="canvas_new_customer_source_prev_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_new_customer_source_prev2_month"><canvas id="canvas_new_customer_source_prev2_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_new_customer_source_prev_month_year"><canvas id="canvas_new_customer_source_prev_month_year"></canvas></div>
				</div>
			</div>
		</div>
	</div>
	<div class="span6 responsive">
		<div class="portlet box red" >
			<div class="portlet-title">
				<div class="caption"><?php echo __('Crédits consommés par source'); ?></div>
			</div>
			<div class="portlet-body">
				<ul class="nav nav-tabs">
				  <li class="active"><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_source_custom" data-toggle="tab">Perso</a></li>
				  <li class=""><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_source_today" data-toggle="tab">Aujourd'hui</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_source_yesterday" data-toggle="tab">Hier</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_source_prev_day" data-toggle="tab">J-2</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_source_prev_week" data-toggle="tab">J-7</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_source_month" data-toggle="tab">Mois</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_source_prev_month" data-toggle="tab">Mois dernier</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_source_prev2_month" data-toggle="tab">Mois -2</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_source_prev_year" data-toggle="tab">J N-1</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_source_prev_month_year" data-toggle="tab">M N-1</a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane fade in active" id="tab_canvas_consult_source_custom"><canvas id="canvas_consult_source_custom"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_source_today"><canvas id="canvas_consult_source_today"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_source_yesterday"><canvas id="canvas_consult_source_yesterday"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_source_prev_day"><canvas id="canvas_consult_source_prev_day"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_source_prev_week"><canvas id="canvas_consult_source_prev_week"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_source_prev_year"><canvas id="canvas_consult_source_prev_year"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_source_month"><canvas id="canvas_consult_source_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_source_prev_month"><canvas id="canvas_consult_source_prev_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_source_prev2_month"><canvas id="canvas_consult_source_prev2_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_source_prev_month_year"><canvas id="canvas_consult_source_prev_month_year"></canvas></div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row-fluid">
   <div class="span6 responsive">
		<div class="portlet box blue" >
			<div class="portlet-title">
				<div class="caption"><?php echo __('Nouveaux clients par pays'); ?></div>
			</div>
			<div class="portlet-body">
				<ul class="nav nav-tabs">
				  <li class="active"><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_new_customer_country_custom" data-toggle="tab">Perso</a></li>
				  <li class=""><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_new_customer_country_today" data-toggle="tab">Aujourd'hui</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_new_customer_country_yesterday" data-toggle="tab">Hier</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_new_customer_country_prev_day" data-toggle="tab">J-2</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_new_customer_country_prev_week" data-toggle="tab">J-7</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_new_customer_country_month" data-toggle="tab">Mois</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_new_customer_country_prev_month" data-toggle="tab">Mois dernier</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_new_customer_country_prev2_month" data-toggle="tab">Mois -2</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_new_customer_country_prev_year" data-toggle="tab">J N-1</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_new_customer_country_prev_month_year" data-toggle="tab">M N-1</a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane fade in active" id="tab_canvas_new_customer_country_custom"><canvas id="canvas_new_customer_country_custom"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_new_customer_country_today"><canvas id="canvas_new_customer_country_today"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_new_customer_country_yesterday"><canvas id="canvas_new_customer_country_yesterday"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_new_customer_country_prev_day"><canvas id="canvas_new_customer_country_prev_day"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_new_customer_country_prev_week"><canvas id="canvas_new_customer_country_prev_week"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_new_customer_country_prev_year"><canvas id="canvas_new_customer_country_prev_year"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_new_customer_country_month"><canvas id="canvas_new_customer_country_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_new_customer_country_prev_month"><canvas id="canvas_new_customer_country_prev_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_new_customer_country_prev2_month"><canvas id="canvas_new_customer_country_prev2_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_new_customer_country_prev_month_year"><canvas id="canvas_new_customer_country_prev_month_year"></canvas></div>
				</div>
			</div>
		</div>
	</div>
	<div class="span6 responsive">
		<div class="portlet box red" >
			<div class="portlet-title">
				<div class="caption"><?php echo __('Crédits consommés par pays'); ?></div>
			</div>
			<div class="portlet-body">
				<ul class="nav nav-tabs">
				  <li class="active"><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_country_custom" data-toggle="tab">Perso</a></li>
				  <li class=""><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_country_today" data-toggle="tab">Aujourd'hui</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_country_yesterday" data-toggle="tab">Hier</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_country_prev_day" data-toggle="tab">J-2</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_country_prev_week" data-toggle="tab">J-7</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_country_month" data-toggle="tab">Mois</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_country_prev_month" data-toggle="tab">Mois dernier</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_country_prev2_month" data-toggle="tab">Mois -2</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_country_prev_year" data-toggle="tab">J N-1</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_country_prev_month_year" data-toggle="tab">M N-1</a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane fade in active" id="tab_canvas_consult_country_custom"><canvas id="canvas_consult_country_custom"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_country_today"><canvas id="canvas_consult_country_today"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_country_yesterday"><canvas id="canvas_consult_country_yesterday"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_country_prev_day"><canvas id="canvas_consult_country_prev_day"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_country_prev_week"><canvas id="canvas_consult_country_prev_week"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_country_prev_year"><canvas id="canvas_consult_country_prev_year"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_country_month"><canvas id="canvas_consult_country_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_country_prev_month"><canvas id="canvas_consult_country_prev_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_country_prev2_month"><canvas id="canvas_consult_country_prev2_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_country_prev_month_year"><canvas id="canvas_consult_country_prev_month_year"></canvas></div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="row-fluid">
   <div class="span6 responsive">
		<div class="portlet box purple" >
			<div class="portlet-title">
				<div class="caption"><?php echo __('Consultations par mode'); ?></div>
			</div>
			<div class="portlet-body">
				<ul class="nav nav-tabs">
				  <li class="active"><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_nb_custom" data-toggle="tab">Perso</a></li>
				  <li class=""><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_nb_today" data-toggle="tab">Aujourd'hui</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_nb_yesterday" data-toggle="tab">Hier</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_nb_prev_day" data-toggle="tab">J-2</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_nb_prev_week" data-toggle="tab">J-7</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_nb_month" data-toggle="tab">Mois</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_nb_prev_month" data-toggle="tab">Mois dernier</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_nb_prev_year" data-toggle="tab">J N-1</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_nb_prev_month_year" data-toggle="tab">M N-1</a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane fade in active" id="tab_canvas_consult_nb_custom"><canvas id="canvas_consult_nb_custom"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_nb_today"><canvas id="canvas_consult_nb_today"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_nb_yesterday"><canvas id="canvas_consult_nb_yesterday"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_nb_prev_day"><canvas id="canvas_consult_nb_prev_day"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_nb_prev_week"><canvas id="canvas_consult_nb_prev_week"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_nb_prev_year"><canvas id="canvas_consult_nb_prev_year"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_nb_month"><canvas id="canvas_consult_nb_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_nb_prev_month"><canvas id="canvas_consult_nb_prev_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_nb_prev2_month"><canvas id="canvas_consult_nb_prev2_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_nb_prev_month_year"><canvas id="canvas_consult_nb_prev_month_year"></canvas></div>
				</div>
			</div>
		</div>
	</div>
	<div class="span6 responsive">
		<div class="portlet box red" >
			<div class="portlet-title">
				<div class="caption"><?php echo __('Consultations par Pays'); ?></div>
			</div>
			<div class="portlet-body">
				<ul class="nav nav-tabs">
				  <li class="active"><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_country_nb_custom" data-toggle="tab">Perso</a></li>
				  <li class=""><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_country_nb_today" data-toggle="tab">Aujourd'hui</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_country_nb_yesterday" data-toggle="tab">Hier</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_country_nb_prev_day" data-toggle="tab">J-2</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_country_nb_prev_week" data-toggle="tab">J-7</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_country_nb_month" data-toggle="tab">Mois</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_country_nb_prev_month" data-toggle="tab">Mois dernier</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_country_nb_prev2_month" data-toggle="tab">Mois -2</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_country_nb_prev_year" data-toggle="tab">J N-1</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_country_nb_prev_month_year" data-toggle="tab">M N-1</a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane fade in active" id="tab_canvas_consult_country_nb_custom"><canvas id="canvas_consult_country_nb_custom"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_country_nb_today"><canvas id="canvas_consult_country_nb_today"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_country_nb_yesterday"><canvas id="canvas_consult_country_nb_yesterday"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_country_nb_prev_day"><canvas id="canvas_consult_country_nb_prev_day"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_country_nb_prev_week"><canvas id="canvas_consult_country_nb_prev_week"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_country_nb_prev_year"><canvas id="canvas_consult_country_nb_prev_year"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_country_nb_month"><canvas id="canvas_consult_country_nb_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_country_nb_prev_month"><canvas id="canvas_consult_country_nb_prev_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_country_nb_prev2_month"><canvas id="canvas_consult_country_nb_prev2_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_country_nb_prev_month_year"><canvas id="canvas_consult_country_nb_prev_month_year"></canvas></div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="row-fluid">
   <div class="span6 responsive">
		<div class="portlet box purple" >
			<div class="portlet-title">
				<div class="caption"><?php echo __('Consultations par type'); ?></div>
			</div>
			<div class="portlet-body">
				<ul class="nav nav-tabs">
				  <li class="active"><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_type_nb_custom" data-toggle="tab">Perso</a></li>
				  <li class=""><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_type_nb_today" data-toggle="tab">Aujourd'hui</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_type_nb_yesterday" data-toggle="tab">Hier</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_type_nb_prev_day" data-toggle="tab">J-2</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_type_nb_prev_week" data-toggle="tab">J-7</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_type_nb_month" data-toggle="tab">Mois</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_type_nb_prev_month" data-toggle="tab">Mois dernier</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_type_nb_prev2_month" data-toggle="tab">Mois -2</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_type_nb_prev_year" data-toggle="tab">J N-1</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_consult_type_nb_prev_month_year" data-toggle="tab">M N-1</a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane fade in active" id="tab_canvas_consult_type_nb_custom"><canvas id="canvas_consult_type_nb_custom"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_type_nb_today"><canvas id="canvas_consult_type_nb_today"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_type_nb_yesterday"><canvas id="canvas_consult_type_nb_yesterday"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_type_nb_prev_day"><canvas id="canvas_consult_type_nb_prev_day"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_type_nb_prev_week"><canvas id="canvas_consult_type_nb_prev_week"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_type_nb_prev_year"><canvas id="canvas_consult_type_nb_prev_year"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_type_nb_month"><canvas id="canvas_consult_type_nb_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_type_nb_prev_month"><canvas id="canvas_consult_type_nb_prev_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_type_nb_prev2_month"><canvas id="canvas_consult_type_nb_prev2_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_consult_type_nb_prev_month_year"><canvas id="canvas_consult_type_nb_prev_month_year"></canvas></div>
				</div>
			</div>
		</div>
	</div>
	<div class="span6 responsive">
		<div class="portlet box red" >
			<div class="portlet-title">
				<div class="caption"><?php echo __('Crédits consommés par type'); ?></div>
			</div>
			<div class="portlet-body">
				<ul class="nav nav-tabs">
				  <li class="active"><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_buy_type_custom" data-toggle="tab">Perso</a></li>
				  <li class=""><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_buy_type_today" data-toggle="tab">Aujourd'hui</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_buy_type_yesterday" data-toggle="tab">Hier</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_buy_type_prev_day" data-toggle="tab">J-2</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_buy_type_prev_week" data-toggle="tab">J-7</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_buy_type_month" data-toggle="tab">Mois</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_buy_type_prev_month" data-toggle="tab">Mois dernier</a></li>
				   <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_buy_type_prev2_month" data-toggle="tab">Mois -2</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_buy_type_prev_year" data-toggle="tab">J N-1</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_buy_type_prev_month_year" data-toggle="tab">M N-1</a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane fade in active" id="tab_canvas_buy_type_custom"><canvas id="canvas_buy_type_custom"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_buy_type_today"><canvas id="canvas_buy_type_today"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_buy_type_yesterday"><canvas id="canvas_buy_type_yesterday"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_buy_type_prev_day"><canvas id="canvas_buy_type_prev_day"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_buy_type_prev_week"><canvas id="canvas_buy_type_prev_week"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_buy_type_prev_year"><canvas id="canvas_buy_type_prev_year"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_buy_type_month"><canvas id="canvas_buy_type_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_buy_type_prev_month"><canvas id="canvas_buy_type_prev_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_buy_type_prev2_month"><canvas id="canvas_buy_type_prev2_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_buy_type_prev_month_year"><canvas id="canvas_buy_type_prev_month_year"></canvas></div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="row-fluid">
   <div class="span6 responsive">
		<div class="portlet box purple" >
			<div class="portlet-title">
				<div class="caption"><?php echo __('Tunnel vente'); ?></div>
			</div>
			<div class="portlet-body">
				<ul class="nav nav-tabs">
				  <li class="active"><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_carts_custom" data-toggle="tab">Perso</a></li>
				  <li class=""><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_carts_today" data-toggle="tab">Aujourd'hui</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_carts_yesterday" data-toggle="tab">Hier</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_carts_prev_day" data-toggle="tab">J-2</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_carts_prev_week" data-toggle="tab">J-7</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_carts_month" data-toggle="tab">Mois</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_carts_prev_month" data-toggle="tab">Mois dernier</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_carts_prev2_month" data-toggle="tab">Mois -2</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_carts_prev_year" data-toggle="tab">J N-1</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_carts_prev_month_year" data-toggle="tab">M N-1</a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane fade in active" id="tab_canvas_carts_custom"><canvas id="canvas_carts_custom"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_carts_today"><canvas id="canvas_carts_today"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_carts_yesterday"><canvas id="canvas_carts_yesterday"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_carts_prev_day"><canvas id="canvas_carts_prev_day"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_carts_prev_week"><canvas id="canvas_carts_prev_week"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_carts_prev_year"><canvas id="canvas_carts_prev_year"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_carts_month"><canvas id="canvas_carts_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_carts_prev_month"><canvas id="canvas_carts_prev_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_carts_prev2_month"><canvas id="canvas_carts_prev2_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_carts_prev_month_year"><canvas id="canvas_carts_prev_month_year"></canvas></div>
				</div>
			</div>
		</div>
	</div>
	
</div>


<script defer>
	
		window.chartColors = {
			red: 'rgb(255, 99, 132)',
			orange: 'rgb(255, 159, 64)',
			yellow: 'rgb(255, 205, 86)',
			green: 'rgb(75, 192, 192)',
			blue: 'rgb(54, 162, 235)',
			purple: 'rgb(153, 102, 255)',
			grey: 'rgb(201, 203, 207)',
			black: 'rgb(0, 0, 0)',
		};
	
		
		
		/*window.randomScalingFactor = function() {
			return (Math.random() > 0.5 ? 1.0 : -1.0) * Math.round(Math.random() * 100);
		};*/
	
		<?php
	
			$pays_color = array(
				'France' => 'blue',
				'Belgique' => 'dark',
				'Canada' => 'green',
				'Luxembourg' => 'purple',
				'Suisse' => 'red',
				'chat' => 'green',
				'email' => 'blue',
				'phone' => 'red',
				'audiotel' => 'red',
				'prepaye' => 'blue',
				'Google Ads' => 'red',
				'Google DSA' => 'purple',
				'Bing' 	=> 'orange',
				'Direct'	=> 'purple',
				'Google' => 'purple',
				'Parrainage'	=> 'blue',
				'Indefini'	=> 'grey',
				'Autre'	=> 'grey',
				'SOS Voyants'	=> 'yellow',
				'Facebook'	=> 'blue',
				'Blog'	=> 'green',
				'panier abandon'	=> 'red',
				'achat abandon'	=> 'orange',
				'achat finalisé'	=> 'green',
				'AUDIOTEL'	=> 'blue',
				'AUDIOTEL Suisse'	=> 'red',
				'AUDIOTEL Belgique'	=> 'yellow',
				'AUDIOTEL Luxembourg'	=> 'purple',
				'AUDIOTEL Canada'	=> 'green',
				'go-fr.com'	=> 'grey',
				'duckduckgo.com'	=> 'grey',
				'forum.leparisien.fr'	=> 'grey',
				'fr.search.yahoo.com'	=> 'grey',
				'r.search.yahoo.com'	=> 'grey',
				'search.yahoo.com'	=> 'grey',
				'fr.zapmeta.ws'	=> 'grey',
				'int.search.myway.com'	=> 'grey',
				'int.search.tb.ask.com'	=> 'grey',
				'meilleurs-voyants-de-france.over-blog.com'	=> 'grey',
				'outlook.live.com'	=> 'grey',
				'search.1and1.com'	=> 'grey',
				'search.avira.com'	=> 'grey',
				'www.qwant.com'	=> 'grey',
				'search.lilo.org'	=> 'grey',
				'www.vinden.be'	=> 'grey',
				'search.handycafe.com'	=> 'grey',
				'forum.leparisien.fr'	=> 'grey',
				'www.zapmeta.fr'	=> 'grey',
				'chiens-en-pension.be'	=> 'grey',
			    'www.ecosia.org'	=> 'grey',
			     'emma.blog-a-idees.over-blog.com'	=> 'grey',
			      'Youtube'	=> 'grey',
				 'Instagram'	=> 'grey',
				 'wmail.orange.fr'	=> 'grey',
				'losx.xyz'	=> 'grey',
			     'forum.magicmaman.com'	=> 'grey',
				'www.searchingdog.com'	=> 'grey',
				'quebec-search.com'	=> 'grey',
				'carte cadeau'	=> 'grey',
				'us.search.yahoo.com'	=> 'grey',
				'search.visymo.com'	=> 'grey',
				'annonces.esopole.com'	=> 'grey',
				'esopole.com'	=> 'grey',
				'webmail1n.orange.fr'	=> 'grey',
				'm.laposte.net'	=> 'grey',
				'www.seekkees.com'	=> 'grey',
				'messagerieweb.globetrotter.net'	=> 'grey',
				'www.bestof-romandie.ch'	=> 'grey',
				'mcpl.xyz'	=> 'grey',
				'Landing'	=> 'grey',
				'www.search-story.com'	=> 'grey',
				'search.monstercrawler.com'	=> 'grey',
				'www.pronto.com'	=> 'grey',
			);
	
	
			foreach($dashboards as $stat => $dash){
				foreach($dash as $period => $statistiques){
		?>		
	
					var barChartDataHour_<?=$stat?>_<?=$period ?> = {
						<?php if($period == 'yesterday' || $period == 'today' || $period == 'prev_day' || $period == 'prev_week' || $period == 'prev_year'){ ?>
						labels: ["00", "01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23"],
						<?php } ?>
						<?php if($period == 'month' || $period == 'prev_month' || $period == 'prev2_month' || $period == 'prev_month_year' || ($period == "custom" && !count($label_custom))){ ?>
						labels: ["1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30", "31"],
						<?php }
						
						if($period == "custom" && count($label_custom)){
							?>
						labels: [<?php
							foreach($label_custom as $cu){
								if($cu != $label_max)
								echo '"'.$cu.'",';
							}
							echo '"'.$label_max.'"';
							?>],
						<?php 
							
						}
						
						?>
						datasets: [
							
							<?php
								foreach($statistiques as $pays => $list_nb){
									if($pays){
										
										$total = 0;
										$max = 0;
										$min = 0;
										if($period == 'yesterday' || $period == 'today' || $period == 'prev_day' || $period == 'prev_week' || $period == 'prev_year')$max = 23;
										if($period == 'month' || $period == 'prev_month' || $period == 'prev2_month' || $period == 'prev_month_year' || ($period == "custom" && !count($label_custom)))$max = 31;
										if($period == 'yesterday' || $period == 'today' || $period == 'prev_day' || $period == 'prev_week' || $period == 'prev_year')$min = 0;
										if($period == 'month' || $period == 'prev_month' || $period == 'prev2_month' || $period == 'prev_month_year' || ($period == "custom" && !count($label_custom)))$min = 1;
										
										if($period == 'custom'&& count($label_custom)){
											$min = $label_custom[0];
											$max = $label_max;
											
											foreach($label_custom as $cu){
												if($list_nb[$cu]) $total +=  $list_nb[$cu];
											}
											
										}else{
											for($n=$min;$n<=$max;$n++){
												if($list_nb[$n]) $total +=  $list_nb[$n];
											}
										}
										
										
									?>
										{
										label: '<?=$pays. ' ( '.number_format($total,0,'.', ' ').' )';?>',
										backgroundColor: window.chartColors.<?=$pays_color[$pays] ?>,
										stack: 'Stack',
										data: [
											<?php
												$max = 0;
												$min = 0;
												if($period == 'yesterday' || $period == 'today' || $period == 'prev_day' || $period == 'prev_week' || $period == 'prev_year')$max = 23;
												if($period == 'month' || $period == 'prev_month' || $period == 'prev2_month' || $period == 'prev_month_year' || ($period == "custom" && !count($label_custom)))$max = 31;
												if($period == 'yesterday' || $period == 'today' || $period == 'prev_day' || $period == 'prev_week' || $period == 'prev_year')$min = 0;
												if($period == 'month' || $period == 'prev_month' || $period == 'prev2_month' || $period == 'prev_month_year' || ($period == "custom" && !count($label_custom)))$min = 1;
										
												if($period == 'custom' && count($label_custom)){
													$min = $label_custom[0];
													$max = $label_max;

													foreach($label_custom as $cu){
														if($list_nb[$cu]) echo $list_nb[$cu].','; else echo '0,';
													}

												}else{
													for($n=$min;$n<=$max;$n++){
														if($list_nb[$n]) echo $list_nb[$n].','; else echo '0,';
													}
												}	

											?>
											]
										},
								<?php
									}
								}
							?>
						]

					};
		<?php
				}
			}
	?>
	window.onload = function() {
		<?php
			foreach($dashboards as $stat => $dash){
				foreach($dash as $period => $statistiques){
					$total = 0;
					foreach($statistiques as $pays => $list_nb){
						if($pays){
							$max = 0;
							$min = 0;
							if($period == 'yesterday' || $period == 'today' || $period == 'prev_day' || $period == 'prev_week' || $period == 'prev_year')$max = 23;
							if($period == 'month' || $period == 'prev_month' || $period == 'prev2_month' || $period == 'prev_month_year' || ($period == "custom" && !count($label_custom)))$max = 31;
							if($period == 'yesterday' || $period == 'today' || $period == 'prev_day' || $period == 'prev_week' || $period == 'prev_year')$min = 0;
							if($period == 'month' || $period == 'prev_month' || $period == 'prev2_month' || $period == 'prev_month_year' || ($period == "custom" && !count($label_custom)))$min = 1;
							if($period == 'custom' && count($label_custom)){
								$min = $label_custom[0];
								$max = $label_max;
								foreach($label_custom as $cu){
									if($list_nb[$cu]) $total +=  $list_nb[$cu];
								}
							}else{
								for($n=$min;$n<=$max;$n++){
									if($list_nb[$n]) $total +=  $list_nb[$n];
								}
							}
						}
					}
		?>
		Chart.Tooltip.positioners.custom = function(elements, position) { 
 
    if (!elements.length) { 
 
     return false; 
 
    } 
 
    var offset = 0; 
 
    //adjust the offset left or right depending on the event position 
 
    if (elements[0]._chart.width/2 > position.x) { 
 
     offset = 20; 
 
    } else { 
 
     offset = -20; 
 
    } 
 
    return { 
 
     x: position.x + offset, 
 
     y: 0//position.y 
 
    } 
 
    } 
			var ctx_<?=$stat ?>_<?=$period ?> = document.getElementById("<?=$stat ?>_<?=$period ?>").getContext("2d");
						var chart_<?=$stat ?>_<?=$period ?> = new Chart(ctx_<?=$stat ?>_<?=$period ?>, {
							type: 'bar',
							data: barChartDataHour_<?=$stat?>_<?=$period ?>,
							options: {
								legend: {
									display: true,
									position:'bottom',
								},
								title:{
									display:true,
									text:"TOTAL : <?=number_format($total,0,'.', ' ') ?>"
								},
								tooltips: {
									mode: 'label',
									intersect: false,
									bodyFontSize : 9,
									position: 'custom',
									callbacks: {
										afterTitle: function() {
											window.total = 0;
										},
										label: function(tooltipItem, data) {
											var corporation = data.datasets[tooltipItem.datasetIndex].label;
											var valor = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
											valor=parseFloat(valor);
											window.total += valor;
											return corporation + ": " + valor.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");             
										},
										footer: function() {
											return "TOTAL: " + window.total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
										}
									}
								},
								responsive: true,
								scales: {
									xAxes: [{
										stacked: true,
									}],
									yAxes: [{
										stacked: true
									}]
								}
							}
						});
	
	<?php
				}
			}
		?>
};
    </script>