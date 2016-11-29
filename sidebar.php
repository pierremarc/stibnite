<?php

/**

    Sidebar [sidebar.php]

    Author: Pierre Marchand
    Date: 2012-02-17

*/

output_file_marker(__FILE__);

global $whereAmI;

$texts = get_page_by_title( 'texts' );
$books = get_page_by_title( 'books' );
$about = get_page_by_title( 'about' );
$contact = get_page_by_title( 'contact' );

$menu_big = array(
                'now' => site_url().'/category/now/',
                'works' => site_url(),
                'texts' => get_permalink($texts->ID),
                'books' => get_permalink($books->ID),
                );
$menu_small = array(
                'about' => get_permalink($about->ID),
                'contact' => get_permalink($contact->ID),
                );

?>

<div id="head">
    <div id="logo">
            <a href="<?php echo site_url(); ?>"><img src= "<?php echo get_template_directory_uri(); ?>/img/A_.png"/></a>
    </div>

    <ul id="menu-big">
    <?php
    foreach($menu_big as $k=>$v)
    {
	    echo ' <li '.($k === $whereAmI ? ' id="current-menu" style="background-image:url('.get_template_directory_uri().'/img/background-'.$k.'.png)"' : '').'><a href="'.$v.'">'.$k.'</a></li>'."\n";
    }
    ?>

    <li>
        <ul id="menu-small">
    <?php
    foreach($menu_small as $k=>$v)
    {
        echo ' <li><a href="'.$v.'"'.($k === $whereAmI ? ' id="current-menu-small"' : '').'>'.$k.'</a></li>'."\n";
    }
    ?>
        </ul>
    </li>
    </ul>
</div>
