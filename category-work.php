<?php

/**

    Works [category-work.php]
    
    Author: Pierre Marchand
    Date: 2012-02-17

*/

get_header();
get_sidebar();


if(have_posts() )
{ 
    $first = ' post-item-first';
    while(have_posts())
    {
        the_post();
	$date = new DateTime($post->post_date);
        echo '<div class="post-item'.$first.'">';
        $day = $date->format('d');
        $month = $date->format('m');
        $year = $date->format('Y');
        echo '<div class="post-date">
                <div class="post-date-day">'.$day.'&nbsp;―</div>
                <div class="post-date-month">'.$month.'&nbsp;―</div>
                <div class="post-date-year">'.$year.'</div>';
        echo '</div>';
        
        if ( has_post_thumbnail() ) 
        {
            the_post_thumbnail('medium');
        } 
        echo '<div class="post-item-content">';
        the_content();
	echo '</div>';
	echo '</div>';
        
        $first = '';
    }
}

get_footer();
