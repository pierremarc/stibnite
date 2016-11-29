<?php

/**

    Page [page.php]

    Author: Pierre Marchand
    Date: 2012-02-17

*/

output_file_marker(__FILE__);



get_header();
get_sidebar();


if(have_posts() )
{

        the_post();
        echo '<div class="page-content">';
        the_content();
	echo '</div>';

}

get_footer();
