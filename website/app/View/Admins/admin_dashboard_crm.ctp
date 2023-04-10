<?php
echo $this->Metronic->titlePage('Backoffice',__('Dashboard'));
echo $this->Metronic->breadCrumb(array(0 => array('text' => 'Accueil', 'classes' => 'icon-home')));
echo $this->Html->script('/theme/default/js/chart.min', array('block' => 'script'));



?>
<div class="row-fluid">
   <div class="span6 responsive">
		<div class="portlet box yellow" >
			<div class="portlet-title">
				<div class="caption"><?php echo __('CRM - Envoi'); ?></div>
			</div>
			<div class="portlet-body">
				<ul class="nav nav-tabs">
				  <li class="active"><a  style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_crmsend_today" data-toggle="tab">Aujourd'hui</a></li>
				  <li><a  style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_crmsend_yesterday" data-toggle="tab">Hier</a></li>
				  <li><a  style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_crmsend_prev_day" data-toggle="tab">J-2</a></li>
				  <li><a  style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_crmsend_prev_week" data-toggle="tab">J-7</a></li>
				  <li><a  style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_crmsend_month" data-toggle="tab">Mois</a></li>
				  <li><a  style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_crmsend_prev_month" data-toggle="tab">Mois dernier</a></li>
				  <li><a  style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_crmsend_prev2_month" data-toggle="tab">Mois -2</a></li>
				  <li><a  style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_crmsend_prev_year" data-toggle="tab">J N-1</a></li>
				  <li><a  style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_crmsend_prev_month_year" data-toggle="tab">M N-1</a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane fade in active" id="tab_canvas_crmsend_today"><canvas id="canvas_crmsend_today"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_crmsend_yesterday"><canvas id="canvas_crmsend_yesterday"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_crmsend_prev_day"><canvas id="canvas_crmsend_prev_day"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_crmsend_prev_week"><canvas id="canvas_crmsend_prev_week"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_crmsend_prev_year"><canvas id="canvas_crmsend_prev_year"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_crmsend_month"><canvas id="canvas_crmsend_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_crmsend_prev_month"><canvas id="canvas_crmsend_prev_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_crmsend_prev2_month"><canvas id="canvas_crmsend_prev2_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_crmsend_prev_month_year"><canvas id="canvas_crmsend_prev_month_year"></canvas></div>
				</div>
			</div>
		</div>
	</div>
	<div class="span6 responsive">
		<div class="portlet box blue" >
			<div class="portlet-title">
				<div class="caption"><?php echo __('CRM - Achats'); ?></div>
			</div>
			<div class="portlet-body">
				<ul class="nav nav-tabs">
				  <li class="active"><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_crmcustomer_today" data-toggle="tab">Aujourd'hui</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_crmcustomer_yesterday" data-toggle="tab">Hier</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_crmcustomer_prev_day" data-toggle="tab">J-2</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_crmcustomer_prev_week" data-toggle="tab">J-7</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_crmcustomer_month" data-toggle="tab">Mois</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_crmcustomer_prev_month" data-toggle="tab">Mois dernier</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_crmcustomer_prev2_month" data-toggle="tab">Mois -2</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_crmcustomer_prev_year" data-toggle="tab">J N-1</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_crmcustomer_prev_month_year" data-toggle="tab">M N-1</a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane fade in active" id="tab_canvas_crmcustomer_today"><canvas id="canvas_crmcustomer_today"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_crmcustomer_yesterday"><canvas id="canvas_crmcustomer_yesterday"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_crmcustomer_prev_day"><canvas id="canvas_crmcustomer_prev_day"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_crmcustomer_prev_week"><canvas id="canvas_crmcustomer_prev_week"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_crmcustomer_prev_year"><canvas id="canvas_crmcustomer_prev_year"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_crmcustomer_month"><canvas id="canvas_crmcustomer_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_crmcustomer_prev_month"><canvas id="canvas_crmcustomer_prev_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_crmcustomer_prev2_month"><canvas id="canvas_crmcustomer_prev2_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_crmcustomer_prev_month_year"><canvas id="canvas_crmcustomer_prev_month_year"></canvas></div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="row-fluid">
   <div class="span6 responsive">
		<div class="portlet box blue" >
			<div class="portlet-title">
				<div class="caption"><?php echo __('CRM - Ouverture'); ?></div>
			</div>
			<div class="portlet-body">
				<ul class="nav nav-tabs">
				  <li class="active"><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_crmopen_today" data-toggle="tab">Aujourd'hui</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_crmopen_yesterday" data-toggle="tab">Hier</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_crmopen_prev_day" data-toggle="tab">J-2</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_crmopen_prev_week" data-toggle="tab">J-7</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_crmopen_month" data-toggle="tab">Mois</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_crmopen_prev_month" data-toggle="tab">Mois dernier</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_crmopen_prev2_month" data-toggle="tab">Mois -2</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_crmopen_prev_year" data-toggle="tab">J N-1</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_crmopen_prev_month_year" data-toggle="tab">M N-1</a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane fade in active" id="tab_canvas_crmopen_today"><canvas id="canvas_crmopen_today"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_crmopen_yesterday"><canvas id="canvas_crmopen_yesterday"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_crmopen_prev_day"><canvas id="canvas_crmopen_prev_day"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_crmopen_prev_week"><canvas id="canvas_crmopen_prev_week"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_crmopen_prev_year"><canvas id="canvas_crmopen_prev_year"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_crmopen_month"><canvas id="canvas_crmopen_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_crmopen_prev_month"><canvas id="canvas_crmopen_prev_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_crmopen_prev2_month"><canvas id="canvas_crmopen_prev2_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_crmopen_prev_month_year"><canvas id="canvas_crmopen_prev_month_year"></canvas></div>
				</div>
			</div>
		</div>
	</div>
	<div class="span6 responsive">
		<div class="portlet box blue" >
			<div class="portlet-title">
				<div class="caption"><?php echo __('CRM - Clic'); ?></div>
			</div>
			<div class="portlet-body">
				<ul class="nav nav-tabs">
				  <li class="active"><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_crmclic_today" data-toggle="tab">Aujourd'hui</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_crmclic_yesterday" data-toggle="tab">Hier</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_crmclic_prev_day" data-toggle="tab">J-2</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_crmclic_prev_week" data-toggle="tab">J-7</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_crmclic_month" data-toggle="tab">Mois</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_crmclic_prev_month" data-toggle="tab">Mois dernier</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_crmclic_prev2_month" data-toggle="tab">Mois -2</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_crmclic_prev_year" data-toggle="tab">J N-1</a></li>
				  <li><a style="padding-right: 5px;padding-left: 5px;" href="#tab_canvas_crmclic_prev_month_year" data-toggle="tab">M N-1</a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane fade in active" id="tab_canvas_crmclic_today"><canvas id="canvas_crmclic_today"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_crmclic_yesterday"><canvas id="canvas_crmclic_yesterday"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_crmclic_prev_day"><canvas id="canvas_crmclic_prev_day"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_crmclic_prev_week"><canvas id="canvas_crmclic_prev_week"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_crmclic_prev_year"><canvas id="canvas_crmclic_prev_year"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_crmclic_month"><canvas id="canvas_crmclic_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_crmclic_prev_month"><canvas id="canvas_crmclic_prev_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_crmclic_prev2_month"><canvas id="canvas_crmclic_prev2_month"></canvas></div>
					<div class="tab-pane fade" id="tab_canvas_crmclic_prev_month_year"><canvas id="canvas_crmclic_prev_month_year"></canvas></div>
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
				'Autre' => 'grey',
				'OLD_5_MIN' => 'blue',
				'NEW_5_MIN' => 'dark',
				'OLD_10_MIN' => 'green',
				'NEW_10_MIN' => 'purple',
				'ISA_1H_5MIN' => 'purple',
				'ISA_3H_5MIN' => 'blue',
				'ISA_1J_10MIN' => 'dark',
				'ISA_2J_10MIN' => 'green',
				'ISA_7J_10MIN' => 'green',
				'ISA_14J_10MIN' => 'green',
				'RAC_14J_5MIN' => 'purple',
				'RAC_45J_5MIN' => 'blue',
				'RAC_60J_5MIN' => 'blue',
				'RAC_90J_5MIN' => 'blue',
				'PA_RAC_1J_10MIN' => 'dark',
				'PA_ISA_1J_10MIN' => 'green',
				'PA_RAC_2J_10MIN' => 'purple',
				'PA_ISA_2J_10MIN' => 'dark',
				'JUST_REGISTERED_RELANCE_NO_BUY_1H' => 'red',
			'JUST_REGISTERED_RELANCE_NO_BUY_2H'=> 'green',
				'JUST_REGISTERED_RELANCE_NO_BUY_3H'=> 'green',
			'JUST_REGISTERED_RELANCE_NO_BUY_1J'=> 'blue',
			'JUST_REGISTERED_RELANCE_NO_BUY_2J'=> 'purple',
			'JUST_REGISTERED_RELANCE_NO_BUY_7J' => 'red',
			'JUST_REGISTERED_RELANCE_NO_BUY_14J'=> 'green',
			'JUST_REGISTERED_RELANCE_NO_BUY_30J'=> 'blue',
			'JUST_REGISTERED_RELANCE_NO_BUY_60J'=> 'purple',
			'JUST_REGISTERED_RELANCE_NO_BUY_90J' => 'red',
			'JUST_REGISTERED_RELANCE_NO_BUY_120J'=> 'green',
			'JUST_REGISTERED_RELANCE_NO_BUY_150J'=> 'blue',
			'JUST_REGISTERED_RELANCE_NO_BUY_180J'=> 'purple',
			'JUST_REGISTERED_RELANCE_NO_BUY_210J' => 'red',
			'JUST_REGISTERED_RELANCE_NO_BUY_240J'=> 'green',
			'JUST_REGISTERED_RELANCE_NO_BUY_270J'=> 'blue',
			'JUST_REGISTERED_RELANCE_NO_BUY_300J'=> 'purple',
			'JUST_REGISTERED_RELANCE_NO_BUY_330J' => 'red',
			'JUST_REGISTERED_RELANCE_NO_BUY_360J'=> 'green',
			'RELANCE_NO_BUY_SINCE_14J'=> 'blue',
			'RELANCE_NO_BUY_SINCE_45J'=> 'purple',
			'RELANCE_NO_BUY_SINCE_60J' => 'red',
			'RELANCE_NO_BUY_SINCE_90J'=> 'green',
			'RELANCE_NO_BUY_SINCE_120J'=> 'blue',
			'RELANCE_NO_BUY_SINCE_150J'=> 'purple',
			'RELANCE_NO_BUY_SINCE_180J' => 'red',
			'RELANCE_NO_BUY_SINCE_210J'=> 'green',
			'RELANCE_NO_BUY_SINCE_240J'=> 'blue',
			'RELANCE_NO_BUY_SINCE_270J'=> 'purple',
			'RELANCE_NO_BUY_SINCE_300J' => 'red',
			'RELANCE_NO_BUY_SINCE_330J'=> 'green',
			'RELANCE_NO_BUY_SINCE_360J'=> 'blue',
			'RELANCE_NO_BUY_SINCE_540J'=> 'purple',
			'RELANCE_NO_BUY_SINCE_720J' => 'red',
			'RELANCE_PANIER_NO_BUY_SINCE_LAST_VISIT'=> 'green',
			'RELANCE2_PANIER_NO_BUY_SINCE_LAST_VISIT'=> 'blue',
			'RELANCE_VISIT_PROFIL_EXPERT_LAST_VISIT'=> 'purple',
			'RELANCE_LOYALTY_NO_USE_SINCE_30J' => 'red',
			'RELANCE3_PANIER_NO_BUY_SINCE_LAST_VISIT'=> 'green',
			);
	
	
			foreach($dashboards as $stat => $dash){
				foreach($dash as $period => $statistiques){
		?>		
	
					var barChartDataHour_<?=$stat?>_<?=$period ?> = {
						<?php if($period == 'yesterday' || $period == 'today' || $period == 'prev_day' || $period == 'prev_week' || $period == 'prev_year'){ ?>
						labels: ["00", "01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23"],
						<?php } ?>
						<?php if($period == 'month' || $period == 'prev_month' || $period == 'prev2_month' || $period == 'prev_month_year'){ ?>
						labels: ["1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30", "31"],
						<?php } ?>
						
						<?php if(($period == 'month' || $period == 'prev_month' || $period == 'prev2_month' || $period == 'prev_month_year') && ( substr_count($stat,'_fr_') || substr_count($stat,'_be_') || substr_count($stat,'_ca_') || substr_count($stat,'_ch_') || substr_count($stat,'_lu_')) ){ ?>
						labels: ["00", "01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23"],
						<?php } ?>
						
						datasets: [
							
							<?php
								foreach($statistiques as $pays => $list_nb){
									if($pays){
										
										$total = 0;
										$max = 0;
										$min = 0;
										if($period == 'yesterday' || $period == 'today' || $period == 'prev_day' || $period == 'prev_week' || $period == 'prev_year')$max = 23;
										if($period == 'month' || $period == 'prev_month' || $period == 'prev2_month' || $period == 'prev_month_year')$max = 31;
										if($period == 'yesterday' || $period == 'today' || $period == 'prev_day' || $period == 'prev_week' || $period == 'prev_year')$min = 0;
										if($period == 'month' || $period == 'prev_month' || $period == 'prev2_month' || $period == 'prev_month_year')$min = 1;
										
										if(($period == 'month' || $period == 'prev_month' || $period == 'prev2_month' || $period == 'prev_month_year') && ( substr_count($stat,'_fr_') || substr_count($stat,'_be_') || substr_count($stat,'_ca_') || substr_count($stat,'_ch_') || substr_count($stat,'_lu_')) ){
											$max = 23;
											$min = 0;
										}
										
										
										for($n=$min;$n<=$max;$n++){
											if($list_nb[$n]) $total +=  $list_nb[$n];
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
												if($period == 'month' || $period == 'prev_month' || $period == 'prev2_month' || $period == 'prev_month_year')$max = 31;
												if($period == 'yesterday' || $period == 'today' || $period == 'prev_day' || $period == 'prev_week' || $period == 'prev_year')$min = 0;
												if($period == 'month' || $period == 'prev_month' || $period == 'prev2_month' || $period == 'prev_month_year')$min = 1;
										
												if(($period == 'month' || $period == 'prev_month' || $period == 'prev2_month' || $period == 'prev_month_year') && ( substr_count($stat,'_fr_') || substr_count($stat,'_be_') || substr_count($stat,'_ca_') || substr_count($stat,'_ch_') || substr_count($stat,'_lu_')) ){
													$max = 23;	
													$min = 0;
												}
										
												for($n=$min;$n<=$max;$n++){
													if($list_nb[$n]) echo $list_nb[$n].','; else echo '0,';
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
							if($period == 'month' || $period == 'prev_month' || $period == 'prev2_month' || $period == 'prev_month_year')$max = 31;
							if($period == 'yesterday' || $period == 'today' || $period == 'prev_day' || $period == 'prev_week' || $period == 'prev_year')$min = 0;
							if($period == 'month' || $period == 'prev_month' || $period == 'prev2_month' || $period == 'prev_month_year')$min = 1;
							if(($period == 'month' || $period == 'prev_month' || $period == 'prev2_month' || $period == 'prev_month_year') && ( substr_count($stat,'_fr_') || substr_count($stat,'_be_') || substr_count($stat,'_ca_') || substr_count($stat,'_ch_') || substr_count($stat,'_lu_')) ){
								$max = 23;
								$min = 0;
							}
							for($n=$min;$n<=$max;$n++){
								if($list_nb[$n]) $total +=  $list_nb[$n];
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
									position: 'custom',
									bodyFontSize : 9,
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