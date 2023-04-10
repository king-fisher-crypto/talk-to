<div class="bootbox modal fade bootbox-prompt in" id="myModal" tabindex="-1" role="dialog" style="display: block;" aria-hidden="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" data-dismiss="modal" class="bootbox-close-button close">×</button>
                <h4 class="modal-title"><?php echo __($title); ?></h4>
            </div>
            <div class="modal-body">
                <div class="bootbox-body">
                    <p><?php echo __('Vous pouvez ajouter un motif pour le refus') ?></p>
                    <form class="bootbox-form">
                        <input class="bootbox-input bootbox-input-text form-control input-xlarge" type="text" id="motif">
                    </form>
                    <p><?php echo __($content); ?></p>
                </div>
            </div>
            <div class="modal-footer">
                <p class="none"><?php echo __('Traitement en cours...'); ?></p>
                <button data-bb-handler="confirm" type="button" class="btn btn-primary ok-refuse"><?php echo __('Ok') ?></button>
                <button data-bb-handler="cancel" data-dismiss="modal" type="button" class="btn btn-default"><?php echo __('Annuler') ?></button>
            </div>
        </div>
    </div>
</div>