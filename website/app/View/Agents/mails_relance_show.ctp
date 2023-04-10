<?php


			$content = '<div class="table-responsive"><table class="table table-striped no-border table-mobile text-center"><thead class="hidden-xs text-center"> 
				  	 		<tr> 
				  	 			<th class="text-center">'.__('Date').'</th> 
								<th class="text-center">'.__('Message').'</th> 
				  	 		</tr> 
				  	 	</thead><tbody> ';
			
			foreach($messages as $message){
				$content .= '<tr> ';
				$content .= '<td class="veram">'.CakeTime::format(Tools::dateUser($this->Session->read('Config.timezone_user'),$message['Message']['date_add']),'%d/%m/%y %Hh%M').'</td>';
				$content .= '<td class="veram">'.$message['Message']['content'].'</td>';
								
				$content .= '</tr>';					
			}
			
			$content .= '</tbody></div></div>';
			echo $content;


?>