<?php

/*echo json_encode(array(
    'credit' => $credit,
    'text'   => $this->FrontBlock->getCreditString($credit),
    'busy_agents' => $busy_agents
));*/

    echo json_encode(array(
        'credit' => $credit,
        'text'   => $this->FrontBlock->getCreditString($credit)
    ));