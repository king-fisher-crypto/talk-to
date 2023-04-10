<?php
echo $this->Session->flash();
$room = isset($room[0]['Rooms'])?$room[0]['Rooms']:'';



$isAdmin = (isset($room['user_id']) && $userid==$room['user_id'])?true:false;
   

    //      echo '<pre>'; print_r($room); echo '</pre>'; exit;
    
           

?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
          <br />
        </div>
        <div class="portlet-body form custom-form-sh">

<?php if($room=='') { 
			echo '<h3 class="form-section">'.__('You do not have access of this room').'</h3>';
 } else { ?>
    <?php 
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
			   					<th>Room Link</th>
			   					<td><?php echo isset($room['room_url'])?$room['room_url']:'' ?></td>
			   				</tr>
			   			</table>
			   		</div>
			   	</div>
    <?php } ?>
            <br/><br/>
        </div>
    </div>
</div>


