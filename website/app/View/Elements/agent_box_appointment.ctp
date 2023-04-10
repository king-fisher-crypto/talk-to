<?php if(!isset($statut)) $statut = 'free'; ?>

<?php if(!isset($isAjax)) echo '<div class="box-rdv">'; ?>
    <?php if($statut === 'free'): ?>
        <p class="box-rdv-txt-free"></p>
    <?php elseif($statut === 'busy'): ?>
        <p class="box-rdv-txt-busy"></p>
    <?php elseif($statut === 'cancel'): ?>
        <p class="box-rdv-txt-cancel"></p>
    <?php endif; ?>
<?php if(!isset($isAjax)) echo '</div>'; ?>