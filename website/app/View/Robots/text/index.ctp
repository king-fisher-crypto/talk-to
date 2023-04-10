<?php

if (isset($content_robot)):
    echo $content_robot;
endif;
?>

<?php

if (isset($sitemap_url)):
    echo 'Sitemap: '.$sitemap_url;
endif;
?>

User-agent: Googlebot-Image
Disallow: /media/cms_photo/image/zconnect(1).png
