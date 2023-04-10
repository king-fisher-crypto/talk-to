<?php
//echo $this->Html->script('/theme/default/js/nx_select_history', array('block' => 'script'));
echo $this->Session->flash();
?>


<section class="promo_codes-page page ">


    <article class="marge-custom">
	<h1 class="">  <?= __('Rooms') ?></h1>
	<?= __("") ?>
    </article>

<?php if(isset($user) && $user['role'] && $user['role']=="agent") { ?>
    <div class="content-right">
    	<?php echo $this->Html->link('+ Add New Room',array('controller' => 'rooms', 'action' => 'add', 'full_base' => true),array('class'=>'btn acheter  lh24-28 p17b h70 blue2 up_case mb-2')); ?>
    </div>
    <br />
<?php } ?>

    <div class="cadre_table ">

	<?php if (empty($allrooms)) : ?>
    	<div class="txt_cent">
		<?php echo __('Aucun rooms'); ?>	</div>
	<?php else : ?>
		
		      
    	<div  class="overflow jswidth">
    	    <img class="arrow_right shake" src="/theme/black_blue/img/arrow_right.svg">

    	    <table class=" stries" > 
    		<thead class=""> 
    		    <tr>  
    			<th class="date"><?php echo __('Title'); ?></th> 
    			<th class="agent"><?php echo __('Slug'); ?></th> 
    			<th class="mode"><?php echo __('No of Invites'); ?></th> 
    			<th class="cout"><?php echo __('Date Start'); ?></th> 
    			<th class="cout"><?php echo __('Date End'); ?></th> 
    			<th class="duree"><?php echo __('Handle by') ; ?></th> 
    			<th class="duree"><?php echo __('Created at') ; ?></th> 
    			<th class="duree"><?php echo __('Action') ; ?></th> 
    		    </tr> 
    		</thead> 
    		<tbody>
				<?php foreach ($allrooms as $room) : 
					$user = isset($room['User'])?$room['User']:[];
					$invitedcount = isset($room[0]['totalinvites'])?$room[0]['totalinvites']:0;
					$room = $room['Rooms'];
					?>
				    <tr>
					    <td><?php echo $room['title']; ?></td>
						<td><?php echo $room['slug']; ?></td>
						<td><?php echo $room['no_of_invites']; ?></td>
						<td class="date"><?php echo $room['date_start']; ?></td>
						<td class="date"><?php echo $room['date_end']; ?></td>
						<td><?php echo isset($user['firstname'])?$user['firstname']:''; ?></td>
						<td><?php echo $room['created_at']; ?></td> 
						<td>
							<?php if($invitedcount!=$room['no_of_invites']) { ?>
							<?php if($userid==$room['user_id']) { ?>
								<?php echo $this->Html->link('Invite',array('controller' => 'rooms', 'action' => 'invite',$room['id'], 'full_base' => true)); ?>
							<?php } else { ?>
								<?php echo $this->Html->link('View',array('controller' => 'rooms', 'action' => 'invite',$room['id'], 'full_base' => true)); ?>
							<?php } ?>
						<?php } ?>
						<?php if($userid==$room['created_by']) { ?>
							<?php echo $this->Html->link('Delete',array('controller' => 'rooms', 'action' => 'remove', $room['id'],  'full_base' => true),array('class'=>'link-danger')); ?>
						<?php } ?>
						</td>
					</tr> 
				<?php endforeach; ?>
			</tbody>
    	<?php endif; ?> 
	    </table> 
	</div>

    </div>
    <?php if ($this->Paginator->param('pageCount') > 1) echo $this->FrontBlock->getPaginateObj($this->Paginator); ?>



<?php
//echo $this->Frontblock->getRightSidebar();
?>

   

</section>


