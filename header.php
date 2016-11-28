<?php

/**

    Header [header.php]
    
    Author: Pierre Marchand
    Date: 2012-02-17

*/


// Detect where we are
global $whereAmI;
$whereAmI = FALSE;
if(is_page())
{
	$whereAmI = $post->post_name;
}
else
{
	if(is_home() || is_front_page())
		$whereAmI = 'works';
	elseif(is_archive())
		$whereAmI = 'now';
		
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" lang="en"> 
<head> 
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>
    <?php
            bloginfo( 'name' );
            if( is_home() || is_front_page() )
                    echo ' | Contemporary artist';
    ?>
    </title>
<?php wp_head(); ?>

<LINK REL="SHORTCUT ICON" href="http://www.ludi.be/stibnite-favicon.ico">

</head>
<body>