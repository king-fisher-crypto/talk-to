<?php echo $this->element('agentslist', array('id_category' => isset($category_id)?$category_id:0, 'agents' => $agents, 'phones' => $phones)); ?>
