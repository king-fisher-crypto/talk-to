<!DOCTYPE html>
<html>
<head>
    <?php echo $this->Html->charset(); ?>
    <title><?php echo substr($site_vars['meta_title'],0,Configure::read('Site.lengthMetaTitle')); ?></title>
    <?php

        if (!empty($site_vars['meta_keywords'])) echo $this->Html->meta('keywords', substr($site_vars['meta_keywords'],0,Configure::read('Site.lengthMetaKeywords')));
        if (!empty($site_vars['meta_description'])) echo $this->Html->meta('description', substr($site_vars['meta_description'],0,Configure::read('Site.lengthMetaDescription')));
  echo '<meta name="robots" content="noindex">';
		echo '<link rel="canonical" href="https://fr.spiriteo.com/" />';
  
        echo $this->Html->meta('icon');

        echo $this->Html->meta(array('name' => 'viewport', 'content' => 'width=device-width, initial-scale=1.0'), NULL, array('inline' => false));
        echo $this->Html->meta(array('name' => 'og:type', 'content' => 'website'), NULL, array('inline' => false));
        echo $this->Html->meta(array('name' => 'og:title', 'content' => $site_vars['meta_title']), NULL, array('inline' => false));
        //echo $this->Html->meta(array('name' => 'viewport', 'content' => 'width=996px, user-scalable=yes'), NULL, array('inline' => false));

		
		?>
        <!-- Google Tag Manager -->
		<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-PLV6MD');</script>
        <!-- End Google Tag Manager -->
        <?php
		
        /* Site normal */
		echo $this->Html->css('/theme/default/css/bootstrap.css');
        echo $this->Html->css('/theme/default/css/style_generiq.css');

        if (isset($site_vars['css_links']))
            foreach ($site_vars['css_links'] AS $css)
                echo $this->Html->css($css);
		
		echo $this->Html->script('/theme/default/js/jquery-1.10.2.js');
		echo $this->Html->script('/theme/default/js/bootstrap.js');

        echo $this->fetch('meta');
        echo $this->fetch('css');
        echo $this->fetch('script');



    
    ?>


</head>
<body id="domains_page">
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PLV6MD"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<iframe src="https://fr.spiriteo.com/" width="100%" height="1200" allowtransparency="true"></iframe>


<!-- Modal -->
<div class="modal comodal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"  data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <!-- <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Modal title</h4>
      </div> -->
      <div class="modal-body">
        <div class="logo text-center">
        	<img src="/theme/default/img/logo.jpg">
        </div>
        <div class="content">
        	<ul class="list-group" style="list-style:none">
        		<li class="fr">
        			<a href="https://fr.spiriteo.com" title="Spiriteo France" class="list-group-item"><span class="flags"><img src="/theme/default/img/flag/france.png"></span> <span class="country">fr</span>.spiriteo.com <span class="arrow pull-right"><img src="/theme/default/img/arrow.png"/></span></a>
        		</li>
        		<li class="ch">
        			<a href="https://ch.spiriteo.com" title="Spiriteo Suisse" class="list-group-item"><span class="flags"><img src="/theme/default/img/flag/suisse.png"></span> <span class="country">ch</span>.spiriteo.com <span class="arrow pull-right"><img src="/theme/default/img/arrow.png"/></a>
        		</li>
        		<li class="be">
        			<a href="https://be.spiriteo.com" title="Spiriteo Belgique" class="list-group-item"><span class="flags"><img src="/theme/default/img/flag/belgium.png"></span> <span class="country">be</span>.spiriteo.com <span class="arrow pull-right"><img src="/theme/default/img/arrow.png"/></a>
        		</li>
        		<li class="lu">
        			<a href="https://lu.spiriteo.com" title="Spiriteo Luxembourg" class="list-group-item"><span class="flags"><img src="/theme/default/img/flag/luxumborg.png"></span> <span class="country">lu</span>.spiriteo.com <span class="arrow pull-right"><img src="/theme/default/img/arrow.png"/></a>
        		</li>
        		<li class="ca">
        			<a href="https://ca.spiriteo.com" title="Spiriteo Canada" class="list-group-item"><span class="flags"><img src="/theme/default/img/flag/canada.png"></span> <span class="country">ca</span>.spiriteo.com <span class="arrow pull-right"><img src="/theme/default/img/arrow.png"/></a>
        		</li>
        	</ul>
        </div><!--content END-->
      </div>
      <!-- <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div> -->
    </div>
  </div>
</div>
<script type="text/javascript">
    $(window).load(function(){
        $('#myModal').modal('show');
    });
</script>
<!--

<table id="page-table"><tr><td id="page-id">
    <div id="site" class="">
        <?php echo $this->Session->flash(); ?>
        <h1><?php echo Configure::read('Site.name'); ?></h1>
        <span class="title"><?php echo __('Choose your country'); ?></span>
        <hr/>

        <?php
            //On coupe en 2
            $half = ceil(count($domains) / 2);
        ?>


        <div class="domain_list">
            <div class="column column1">
                <ul class="domain_link">
                    <?php $i=0; foreach($domains as $k => $row): ?>
                        <?php if($i == $half) break; ?>
                        <li>
                            <span class="link">
                                <?php echo $this->Html->link($row['Domain']['domain'],
                                    'http://'.$row['Domain']['domain'],
                                    array('title' => (empty($row['CategoryLang']['meta_title']) ?false:$row['CategoryLang']['meta_title']))
                                ); ?>
                            </span>

                            <span class="flag">
                                <?php echo $this->Html->link('<span class="country_flags country_'.$row['Domain']['country_id'].'"></span>',
                                    'http://'.$row['Domain']['domain'],
                                    array('escape' => false, 'title' => (empty($row['CategoryLang']['meta_title']) ?false:$row['CategoryLang']['meta_title']))
                                ); ?>
                            </span>
                        </li>
                        <?php $i++; unset($domains[$k]); ?>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="column column2">
                <ul class="domain_link">
                    <?php foreach($domains as $row): ?>
                        <li>
                            <span class="link">
                                <?php echo $this->Html->link($row['Domain']['domain'],
                                    'http://'.$row['Domain']['domain'],
                                    array('title' => (empty($row['CategoryLang']['meta_title']) ?false:$row['CategoryLang']['meta_title']))
                                ); ?>
                            </span>

                            <span class="flag">
                                <?php echo $this->Html->link('<span class="country_flags country_'.$row['Domain']['country_id'].'"></span>',
                                    'http://'.$row['Domain']['domain'],
                                    array('escape' => false, 'title' => (empty($row['CategoryLang']['meta_title']) ?false:$row['CategoryLang']['meta_title']))
                                ); ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="clear"></div>
        </div>

    </div>
</td></tr></table>

-->
</body>
</html>
