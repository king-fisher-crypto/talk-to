<?php
echo $this->Session->flash();
$room = $room[0]['Rooms'];
$invitedPending = 0;
$invitedAlready = count($allinvites);
if($room['no_of_invites'] > 0) {
	$invitedPending = (int)$room['no_of_invites']-count($allinvites);
}
$allUsers = array();
foreach($allinvites as $kk=> $vv) {
	$allUsers[] = $vv['RoomInvites']['user_id'];
}

$finalClients = array();
if(count($allUsers) > 0) {
	//$getclients =
	foreach($getclients as $kv => $vk) {
		if(!in_array($kv, $allUsers)) {
			$finalClients[$kv] = $vk;
		}
	} 
}

$isAdmin = ($userid==$room['user_id'])?true:false;
    

?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
          <br />
        </div>
        <div class="portlet-body form custom-form-sh">

<div class="hidden d-none d-none-custom">
<input type="hidden" id="pendinginvites" value="<?php echo $invitedPending ?>" />
</div>
    <?php if($invitedPending > 0 && $isAdmin) { ?>
            <?php
            echo $this->Form->create('Rooms', array('nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1, 'inputDefaults' => array('class' => 'span10')));
        }
            echo '<h3 class="form-section">'.__('Room Details').'</h3>';

            //Les inputs du formulaire
            // $inputs = array(
            //     'title' => array('label' => array('text' => __('Title'), 'class' => 'control-label required'), 'required' => true, 'div' => 'control-group span4', 'between' => '<div class="controls">', 'after' => '</div>'),
            //     'slug' => array('label' => array('text' => __('Slug'), 'class' => 'control-label'), 'required' => true, 'div' => 'control-group span4', 'between' => '<div class="controls">', 'after' => '</div>'),
            //     'no_of_invites' => array('label' => array('text' => __('No. of invites'),  'class' => 'control-label'),'required' => true, 'div' => 'control-group span4', 'between' => '<div class="controls">', 'after' => '</div>'),
            //     'date_start' => array('label' => array('text' => __('Date Start'), 'class' => 'control-label'), 'div' => 'control-group span4', 'between' => '<div class="controls">', 'after' => '</div>', 'type' => 'date'),
            //     'date_end' => array('label' => array('text' => __('Date End'), 'class' => 'control-label'), 'div' => 'control-group span4', 'between' => '<div class="controls">', 'after' => '</div>', 'type' => 'date'),
            //     'role' => array('label' => array('text' => __('Handle By'), 'class' => 'control-label role_option_field custom-input roleinput-cls'), 'div' => 'control-group span4 custom-input', 'between' => '<div class="controls">', 'after' => '</div>', 'type' => 'select', 'options'=>array('1'=>'Admin','2'=>'Moderator')),
            //     'user_id' => array('label' => array('text' => __('Select Moderator'), 'class' => 'control-label custom-input '), 'div' => 'control-group span4 custom-input custom-user-id-cls custom-handler', 'between' => '<div class="controls user_id_input ">', 'after' => '</div>', 'type' => 'select', 'options'=>$getclients),
                
            // );//protege avec code admin level

           // echo $this->Metronic->inputsAdminEdit($inputs);

            //echo $this->Form->input();
?>
    <div class="sep-container">
   		<div class="table-container">
   			<table class="table table-hover table-custom-sh">
   				<tr>
   					<th>Title</th>
   					<td><?php echo isset($room['title'])?$room['title']:'' ?></td>
   				</tr>
   				<tr>
   					<th>Slug</th>
   					<td><?php echo isset($room['slug'])?$room['slug']:'' ?></td>
   				</tr>
   				<tr>
   					<th>Start Date</th>
   					<td><?php echo isset($room['date_start'])?$room['date_start']:'' ?></td>
   				</tr>
   				<tr>
   					<th>End Date</th>
   					<td><?php echo isset($room['date_end'])?$room['date_end']:'' ?></td>
   				</tr>
   				<tr>
   					<th>No. of Invites</th>
   					<td><?php echo isset($room['no_of_invites'])?$room['no_of_invites']:'' ?></td>
   				</tr>
   				<tr>
   					<th>No. of Invited Members</th>
   					<td><?php echo $invitedAlready ?></td>
   				</tr>
   				<tr>
   					<th class="noborder">Invited Members</th>
   					<td class="noborder">
   						<?php foreach ($allinvites as $key => $value) { ?>
   							<div><?php echo $key+1; ?>. <?php echo $value['User']['firstname']; ?></div>
   						<?php } ?>
   					</td>
   				</tr>
   			</table>
   		</div>
   	</div>
    <?php if($invitedPending > 0 && $isAdmin) { ?>
        <div class="cls">
          <div class="overflow-container-cs">
            <?php

              echo $this->Form->input('invited_users', array(
                  'label' => 'Invite More Members (Pending : '.$invitedPending.')',
                  'type' => 'select',
                  'multiple' => 'checkbox',
                  'options' => $finalClients,
                  //'selected' => $selectedWarnings
                ));

              echo $this->Form->input('room_id', array(
                'type'=>'hidden',
                'value'=>  $room['id'] 
              ))
            ?>
          </div>
        </div>

<?php


            echo $this->Form->end(array(
                'label' => __('Save'),
                'class' => 'btn ',
                'div' => array('class' => 'controls')
            ));
            ?>

<?php } ?>
            <br/><br/>
        </div>
    </div>
</div>


