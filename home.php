<?php

/**

    Home [home.php]
    
    Author: Pierre Marchand
    Date: 2012-02-17

*/

session_start();
get_header();
get_sidebar();

get_template_part('wrkbox');
get_template_part('colbox');

// if(!isset($_SESSION['home_view']))
{
	$_SESSION['home_view'] = true;
	$nows = Stibnite_get_nows();
	if($nows)
	{
	$now_post = $nows[0];
	$img = get_the_post_thumbnail( $now_post->ID, 'medium');
	echo '
	<div id="up">
		<div id="close-up">x</div>
		<div id="date-up">'.Stibnite_make_date($now_post->post_date).'</div>
		<div id="titre-up"><a href="'.site_url().'/category/now/'.'">'.apply_filters('the_title', $now_post->post_title).'</a></div>
		<div id="image-up">
		'.$img.'
		</div>
		<div id="up-post">'.apply_filters('the_content', $now_post->post_content).'</div>
	</div>
	';
	}
}
    
?>




<?php
get_footer()
?>

