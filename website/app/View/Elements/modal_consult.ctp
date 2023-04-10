<!-- Modal -->
<div class="modal fade modal-footer-hide" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-consult">
    <div class="modal-content">
      <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
          <h4 class="m-title" id="myModalLabel">
             <?php
                  echo $title;
              ?>
          </h4>
		  <div class="consulter-img text-center">
                            <?php
								$avatar = $this->FrontBlock->getAvatar($User,false);
								echo $this->Html->image($avatar, array(
										'alt' => 'agents en ligne '.$User['pseudo'],
										'class' => 'img-responsive img-circle img-con status-'.$User['agent_status'],
										'itemprop' => 'image'
								));
			  ?>
		 </div>
      </div>
      <div class="modal-body"><?php echo $content; ?></div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->