<h1>
<?php
    if (isset($icon)){
        echo '<span class="glyphicon glyphicon-'.$icon.'"></span> ';
    }
    echo (isset($title)?__($title):'');
?>
</h1>
<?php if (isset($breadcrumb)){
    echo '<ol class="breadcrumb">';
    foreach ($breadcrumb AS $item){
        echo '<li>'.(isset($item['link']) && !empty($item['link'])?'<a href="'.$item['link'].'">'.__($item['name']).'</a>':__($item['name'])).'</li>';
    }
    echo '</ol>';
}