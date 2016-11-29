<?php

/**

    Single post [single.php]

    Author: Pierre Marchand
    Date: 2012-02-17

*/

output_file_marker(__FILE__);

get_header();
get_sidebar();
echo '<div id="wrkbox">';
echo Stibnite_make_gallery($post);
echo '</div>';
get_footer();
