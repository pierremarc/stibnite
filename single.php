<?php

/**

    Single post [single.php]
    
    Author: Pierre Marchand
    Date: 2012-02-17

*/


if(isset($_GET['embed']) || isset($_POST['embed']))
{
	if(isset($_GET['gal_skel']) || isset($_POST['gal_skel']))
		Stibnite_gal_skeleton($post);
	elseif(isset($_GET['gal_content']) || isset($_POST['gal_content']))
		Stibnite_gal_content($post);
}
else
{
    get_header();
    get_sidebar();
    echo '<div id="wrkbox">';
    echo Stibnite_make_gallery($post);
    echo '</div>';
    get_footer();
}
?>

