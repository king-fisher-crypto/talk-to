<?php


			$content = '<div class="table-responsive"><table class="table table-striped no-border table-mobile text-center"><thead class="hidden-xs text-center"> 
				  	 		<tr> 
				  	 			<th class="text-center">'.__('Media').'</th> 
				  	 			<th class="text-center">'.__('Durée/Seconde').'</th> 
				  	 			<th class="text-center">'.__('Date').'</th> 
				  	 		</tr> 
				  	 	</thead><tbody> ';
			
			foreach($lastComs as $com){
				$content .= '<tr> ';
				 $content .= '<td class="veram">'.__($consult_medias[$com['UserCreditLastHistory']['media']]).'</td>';
				$content .= '<td class="veram">';
				$content .= (empty($com['UserCreditLastHistory']['seconds'])
                                ?__('N/D')
                                :gmdate('H:i:s', $com['UserCreditLastHistory']['seconds']));
				$content .= '</td>';			 
				$content .= '<td class="veram">'.CakeTime::format(Tools::dateUser($this->Session->read('Config.timezone_user'),$com['UserCreditLastHistory']['date_start']),'%d/%m/%y %Hh%M').'</td>';
								
				$content .= '</tr>';					
			}
			
			$content .= '</tbody></div></div>';
			echo $content;


?>