<?php if(isset($inscription)): //-----POUR L'INSCRIPTION--------- ?>
    <div class="form-group mt20 wow fadeIn animated" data-wow-delay="0.2s" style="visibility: visible;-webkit-animation-delay: 0.2s; -moz-animation-delay: 0.2s; animation-delay: 0.2s;" model="<?php echo $nomModel; ?>" croph="<?php echo Configure::read('Site.photoDim.h'); ?>" cropw="<?php echo Configure::read('Site.photoDim.w'); ?>">
        <?php echo '<label for="'. $nomModel .'Photo" class="col-sm-12 col-md-4 control-label required">'.__('Photo (.jpg .png .gif)').' <span class="star-condition">*</span></label>' ?>
        <div class="col-sm-12 col-md-8">
            <div class="photo_agent preview" style="float:left"><?php echo $this->Html->image('/'.Configure::read('Site.defaultImage'), array('id' => 'previewCrop')); ?></div>
            <p style="float:right;width:250px"><?php echo __('Photo de mannequin ou de décoration refusées.'); ?></p>
			<p style="float:right;width:250px;font-size:12px;"><?php echo __('( Dimensions maximum : 500px / 500px )'); ?></p>
            <p style="clear:both;"></p>
            <br/>
			<img src="/<?php echo Configure::read('Site.loadingImage'); ?>" class="loading-crop"/> 
            
            <input type="file" class="form-control" url="<?php echo $this->Html->url(array('controller' => 'users', 'action' => 'modalPhotoAgent'));?>" name="data[<?php echo $nomModel; ?>][photo]" accept="image/*" id="<?php echo $nomModel; ?>Photo"/>
            <input type="hidden" name="data[<?php echo $nomModel; ?>][crop][x]" id="<?php echo $nomModel; ?>CropX"/>
            <input type="hidden" name="data[<?php echo $nomModel; ?>][crop][y]" id="<?php echo $nomModel; ?>CropY"/>
            <input type="hidden" name="data[<?php echo $nomModel; ?>][crop][h]" id="<?php echo $nomModel; ?>CropH"/>
            <input type="hidden" name="data[<?php echo $nomModel; ?>][crop][w]" id="<?php echo $nomModel; ?>CropW"/>

        </div>
    </div>
    <div class="form-group mt20 wow fadeIn animated" data-wow-delay="0.2s" style="visibility: visible;-webkit-animation-delay: 0.2s; -moz-animation-delay: 0.2s; animation-delay: 0.2s;">
        <label for="<?php echo $nomModel; ?>Audio" class="col-sm-12 col-md-4 control-label norequired"><?php echo __('Présentation audio (.mp3 max: 2Mo)'); ?></label>
        <div class="col-sm-12 col-md-8">
            <br/>
            <input type="file" class="form-control" name="data[<?php echo $nomModel; ?>][audio]" accept="audio/*"  id="<?php echo $nomModel; ?>Audio"/>
            <br/><?php echo __('Enregistrez une présentation audio afin de vous présenter auprès des internautes.'); ?>
        </div>
    </div>
<?php else: //-----POUR LE PROFIL--------- ?>
    <div class="box-media">
        <fieldset><legend><?php echo __('Présentation actuelle'); ?></legend></fieldset>
        <div class="form-group mt20 wow fadeIn animated" data-wow-delay="0.2s" style="visibility: visible;-webkit-animation-delay: 0.2s; -moz-animation-delay: 0.2s; animation-delay: 0.2s;">
            <label class="col-sm-12 col-md-4 control-label"><?php echo __('Photo') ?></label>
            <div class="col-lg-7">
                <?php echo $this->Html->image('/'.(!$namePhoto?Configure::read('Site.defaultImage'):$namePhoto),array(
                    'alt'   => (!$namePhoto?__('Photo manquante'):false),
                    'title' => (!$namePhoto?__('Photo manquante'):false),
                    'class' => 'photo_agent'
                )); ?>
            </div>
        </div>
        <div class="form-group mt20 wow fadeIn animated" data-wow-delay="0.2s" style="visibility: visible;-webkit-animation-delay: 0.2s; -moz-animation-delay: 0.2s; animation-delay: 0.2s;">
            <?php if(isset($nameAudio)): ?>
                <label class="col-sm-12 col-md-4 control-label"><?php echo __('Présentation audio') ?></label>
                <div class="col-sm-12 col-md-8">
                    <audio src="/<?php echo $nameAudio; ?>" controls preload="auto"></audio>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="box-media">
        <fieldset><legend><?php echo __('Modifier votre présentation'); ?></legend></fieldset>
        <div class="form-group" model="<?php echo $nomModel; ?>" croph="<?php echo Configure::read('Site.photoDim.h'); ?>" cropw="<?php echo Configure::read('Site.photoDim.w'); ?>">
            <?php echo '<label for="'. $nomModel .'Photo" class="col-sm-12 col-md-4 control-label norequired">'.__('Photo (.jpg .png .gif)').'</label>' ?>
            <div class="col-sm-12 col-md-8">
                <?php echo '<div class="photo_agent preview_profil">'
                    .$this->Html->image('/'.(isset($namePhotoValidation) ?$namePhotoValidation:Configure::read('Site.defaultImage')),
                        array('class' => 'photo_agent preview', 'id' => 'previewCrop'))
                    .'</div>'; ?>
                <br/>
                <br/>
                <img src="/<?php echo Configure::read('Site.loadingImage'); ?>" class="loading-crop"/>
                <input type="file" class="form-control" url="<?php echo $this->Html->url(array('controller' => 'users', 'action' => 'modalPhotoAgent'));?>" name="data[<?php echo $nomModel; ?>][photo]" accept="image/*" id="<?php echo $nomModel; ?>Photo"/>
                <input type="hidden" name="data[<?php echo $nomModel; ?>][crop][x]" id="<?php echo $nomModel; ?>CropX"/>
                <input type="hidden" name="data[<?php echo $nomModel; ?>][crop][y]" id="<?php echo $nomModel; ?>CropY"/>
                <input type="hidden" name="data[<?php echo $nomModel; ?>][crop][h]" id="<?php echo $nomModel; ?>CropH"/>
                <input type="hidden" name="data[<?php echo $nomModel; ?>][crop][w]" id="<?php echo $nomModel; ?>CropW"/>
            </div>
        </div>
        <div class="form-group mt20 wow fadeIn animated" data-wow-delay="0.2s" style="visibility: visible;-webkit-animation-delay: 0.2s; -moz-animation-delay: 0.2s; animation-delay: 0.2s;">
            <?php if(isset($nameAudioValidation)): ?>
                <div class="form-group" id="presentationValidation">
                    <label class="col-sm-12 col-md-4 control-label norequired"><?php echo __('Présentation audio en attente'); ?></label>
                    <div class="col-sm-12 col-md-8">
                        <audio src="/<?php echo $nameAudioValidation; ?>" controls preload="auto"></audio>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <div class="form-group mt20 wow fadeIn animated" data-wow-delay="0.2s" style="visibility: visible;-webkit-animation-delay: 0.2s; -moz-animation-delay: 0.2s; animation-delay: 0.2s;">
            <label class="col-sm-12 col-md-4 control-label norequired"><?php echo (isset($nameAudioValidation) || isset($nameAudio)
                    ?__('Nouvelle présentation audio (.mp3 max: 2Mo)')
                    :__('Présentation audio (.mp3 max: 2Mo)')); ?>
            </label>
            <div class="col-sm-12 col-md-8">
                <br/>
                <input type="file" class="form-control" name="data[<?php echo $nomModel; ?>][audio]" accept="audio/*" id="<?php echo $nomModel; ?>Audio"/>
                <br/>
                <?php echo __('Enregistrez une présentation audio afin de vous présenter auprès des internautes.'); ?>
            </div>
        </div>
    </div>
<?php endif; ?>