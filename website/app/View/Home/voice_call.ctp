
<?php
$user = $this->Session->read('Auth.User');
?>
<div class="consult-connected">

    <button type="button" class="btn btn-pink btn-2 modal_consult_btn_phone_call num_link_dynamic popupphone1" id="answer"><?=__('Answer ') ?></button>
    <button type="button" class="btn btn-danger btn-2 modal_consult_btn_phone_call num_link_dynamic popupphone1" id="reject"><?=__('Reject  ') ?></button>
    <button type="button" class="btn btn-danger btn-2 modal_consult_btn_phone_call num_link_dynamic popupphone1" id="end-call" style="display: none"><?=__('END') ?></button>
    <div id="status-voip"></div>
</div>

<style>
    input, button { font-size: 1rem; }
</style>