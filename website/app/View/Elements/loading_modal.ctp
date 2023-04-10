<!-- Modal -->
<div class="modal fade" id="myModalLoading" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">
            <img src="<?php echo '/'.Configure::read('Site.pathLogo').'/default.jpg' ;?>" alt="<?php echo Configure::read('Site.name'); ?>" title="<?php echo Configure::read('Site.name'); ?>" style="height: 35px !important; margin-right: 10px;">
            <?php echo $title; ?>
        </h4>
      </div>
      <div class="modal-body">
          <div>
              <p class="txt-center"><?php echo __('Traitement en cours...'); ?></p>
              <?php echo $content; ?>
          </div>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->