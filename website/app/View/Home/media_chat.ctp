<!-- Modal -->
<div class="modal fade modal-footer-hide" id="myModalTchat" tabindex="-1" role="dialog" aria-labelledby="myModalLabelTchat" aria-hidden="true">
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
										'alt' => __('agents en ligne ').$User['pseudo'],
										'class' => 'img-responsive img-circle img-con status-'.$User['agent_status'],
										'itemprop' => 'image'
								));
			  ?>
		 </div>
      </div>
      <div class="modal-body">
		  <div class="consult-prepayed" >
			  <div style="text-align: left;padding:0 15px;" class="mb20"><?php echo $content; ?>
			  
			  <p>&gt; <?=_('Afin de faciliter l\'échange avec l’expert, il est recommandé de joindre une photo ( maximum 2 photos ) ') ?></p>
				  <form method="post" action="" enctype="multipart/form-data" id="myformchat">
				<span class="fileinput-button">
					<span><?=__('Joindre une photo') ?></span>
						<input accept="image/jpeg, image/png, image/gif," 

						 id="chat_files" multiple="multiple" name="chatfiles[]" type="file" />
				</span>
				  </form>
				  <div class="chat_thumbnail">
					  <ul class="thumb-Images" id="imgList"></ul>
				  </div>
			  </div>
			   <a class="btn btn-pink btn-2 modal_consult_btn_phone_call nx_chatboxpopup" href="#" rel="nofollow"><?=__('COMMENCER LE CHAT') ?></a>
			  <span class="linklink">/chats/do_session-<?=$User['id'] ?></span>
		  </div>
	</div>
		  
	
		</div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->