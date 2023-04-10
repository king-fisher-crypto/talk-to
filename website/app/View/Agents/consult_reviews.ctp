<?php


			$content = '<div class="table-responsive"><table class="table table-striped no-border table-mobile text-center"><thead class="hidden-xs text-center"> 
				  	 		<tr> 
				  	 			<th class="text-center">'.__('Date de l\'avis').'</th> 
				  	 			<th class="text-center">'.__('Message').'</th> 
				  	 		</tr> 
				  	 	</thead><tbody> ';
			
			foreach($reviews as $review){
				$content .= '<tr> ';
				$content .= '<td class="veram">'.CakeTime::format(Tools::dateUser($this->Session->read('Config.timezone_user'),$review['Review']['date_add']),'%d/%m/%y %Hh%M').'</td>';
				$content .= '<td class="veram">'.h($review['Review']['content']).'</td>';				
				$content .= '</tr>';					
			}
			
			$content .= '</tbody></div></div>';
			echo $content;


?>