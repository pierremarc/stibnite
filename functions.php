<?php

/**

    Functions [functions.php]
    
    Author: Pierre Marchand
    Date: 2012-02-17

*/


add_theme_support( 'post-thumbnails' );
add_action('init', 'Stibnite_init');

function Stibnite_init()
{
    if(!is_admin())
    {
        wp_enqueue_script('myjquery',
                get_bloginfo('template_directory') . '/js/jquery.js',
                array(),
                '1.7.1' );
                
        wp_enqueue_script('myjquery-ui',
                get_bloginfo('template_directory') . '/js/jquery-ui.js',
                array('myjquery'),
                '1.8.17' );
                
        wp_enqueue_script('paperjs',
                get_bloginfo('template_directory') . '/js/paper.js',
                array(),
                '0.22' );        
                
        wp_enqueue_script('stibnite',
                get_bloginfo('template_directory') . '/js/stibnite.js',
                array('myjquery-ui', 'paperjs'),
                '0.1' );
        
        wp_register_style('stibnite_style', get_stylesheet_directory_uri() . '/style.css');
        wp_enqueue_style( 'stibnite_style');
    }
}

function Stibnite_get_works()
{
    global $work_posts;
    if(isset($work_posts))
        return $work_posts;
        
    $work_cat = get_term_by( 'slug', 'work', 'category');
    $args = array(
    'numberposts'     => -1,
    'offset'          => 0,
    'category'        => $work_cat->term_id,
    'orderby'         => 'post_date',
    'order'           => 'DESC',
    'post_type'       => 'post',
    'post_status'     => 'publish' );
    
    $work_posts = get_posts( $args );
    return $work_posts;
}

function Stibnite_get_nows()
{
    global $now_posts;
    if(isset($now_posts))
        return $now_posts;
        
    $now_cat = get_term_by( 'slug', 'now', 'category');
    $args = array(
    'numberposts'     => -1,
    'offset'          => 0,
    'category'        => $now_cat->term_id,
    'orderby'         => 'post_date',
    'order'           => 'DESC',
    'post_type'       => 'post',
    'post_status'     => 'publish' );
    
    $now_posts = get_posts( $args );
    return $now_posts;
}

function Stibnite_get_images($pid)
{
    $args = array(
        'numberposts' => -1,
        'order'=> 'ASC',
        'post_mime_type' => 'image',
        'post_parent' => $pid,
        'post_status' => null,
        'post_type' => 'attachment'
        );
    
    $result = get_posts($args);
    return $result;
}

function Stibnite_sort_wrks($a, $b)
{
    $r_a = get_post_meta($a->ID, 'Floor', true);
    $r_b = get_post_meta($b->ID, 'Floor', true);
    
    if ($r_a == $r_b) 
    {
        return 0;
    }
    return ($r_a < $r_b) ? -1 : 1;
}

function Stibnite_make_wrk_cols(&$works, $col_index)
{
    
    $t_col = array();
    foreach($works as $w)
    {
        $col = get_post_meta($w->ID, 'Column', true);
        if($col == $col_index)
        {
//             error_log('I_COL '.$col_index.' => '.$w->post_title);
            $t_col[] = $w;
        }
    }
    
    usort($t_col, 'Stibnite_sort_wrks');
    
    $count = 0;
    foreach($t_col as $w)
    {
        $link = get_permalink($w->ID);
        $img = get_the_post_thumbnail( $w->ID, 'thumbnail');
        
        $first_class = $count > 0 ? '' : ' wrk-wrap-top';
        echo '
        <div class="wrk-wrap'.$first_class.'">
        <a class="wrk-wrap-link" href="'.$link.'">
        '.$img.'
        </a>
        </div>
        ';
        $count += 1;
    }
}

function Stibnite_make_gallery($post)
{
    $pid = $post->ID;
    $content = apply_filters('the_content', $post->post_content);
    $ret = '<canvas id="wrk-canvas" width="600px" height="600px" />';
    $imgs = array();
    $desc = array();
    // first check if there's a featured image to append at the beginning of the list
    if(has_post_thumbnail($pid))
    {
        $tid = get_post_thumbnail_id($pid);
        $imgs[] = wp_get_attachment_image_src( $tid, 'large');
        $src_imgs[] = wp_get_attachment_image_src($tid, 'full');
        $ft = get_post($tid);
        $desc[] = $ft->post_content;
        $title[] = $ft->post_title;
    }
    $atts = Stibnite_get_images($pid);
    foreach($atts as $a)
    {
        $imgs[] = wp_get_attachment_image_src( $a->ID, 'large');
        $src_imgs[] = wp_get_attachment_image_src($a->ID, 'full');
        $desc[] = $a->post_content;
        $title[] = $a->post_title;
    }
    $cimgs = count($imgs);
    for($i = 0; $i < $cimgs; $i++)
    {
        $extraClass = '';
        if($i === 0)
            $extraClass = '-first';
        elseif($i === ($cimgs - 1))
            $extraClass = '-last';
	
	$prevClass = ' gal-nav-disable';
	$nextClass = ' gal-nav-disable';
	
	if($i > 0)
		$prevClass = ' gal-nav-enable';
	if($i < ($cimgs - 1))
		$nextClass = ' gal-nav-enable';
	
	$nav = ' 
	<span class="gal-nav gal-nav-prev '.$prevClass.'"> ← </span>
	<span class="gal-nav-ordinal">'.($i + 1).' / '.$cimgs.'</span> 
	<span class="gal-nav gal-nav-next '.$nextClass.'"> → </span>
	';
	
	
        $ret .= '
        <div id="gal-page-'.$i.'" class="gal-page gal-page'.$extraClass.'">
	
<!--  <img id="img-wrk-'.$i.'" class="img-wrk wrk-gal-item wrk-gal-item'.$extraClass.'" src="'.$imgs[$i][0].'" width="'.$imgs[$i][1].'" height="'.$imgs[$i][2].'"/> -->
            <div class="wrktxt-box">
			<div class="gal-nav-box">
			'.$nav.'
			</div>
                    <div class="wrk-titre">
                    '.$title[$i].'
                    </div>
                    <div class="wrktxt">
                    '.$content.'
                    </div>
                    <div class="wrk-tech">
                    '.$desc[$i].'
                    </div>
                    <div class="gal-button-box">
                    <span id="gal-button-full-'.$i.'" class="gal-button gal-button-full">view full size</span>
                    /
                    <span id="gal-button-fit-'.$i.'" class="gal-button gal-button-fit">fit to canvas</span>
                    </div>
            </div>
		<div style="display:none" id="gal-item-full-box-'.$i.'" class="gal-item-full-box">
			<div class="gal-item-full-close">close</div>
			<img class="gal-item-full" src="'.$src_imgs[$i][0].'"  width="'.$src_imgs[$i][1].'" height="'.$src_imgs[$i][2].'" />
		</div>
        </div>
        ';
    }
    
    return $ret;
    
}

function Stibnite_gal_skeleton($post)
{
	$ret = '
	<div id="canvas-wrap">
	<canvas id="wrk-canvas" width="600px" height="600px" />
	<div id="gal-button-box">
	<span id="gal-button-full" class="gal-button"><img src="'.get_template_directory_uri().'/img/view-full-size.png" title="View full size" alt="full size"/></span>
	<span id="gal-button-fit" class="gal-button"><img src="'.get_template_directory_uri().'/img/fit-to-canvas.png" title="Fit to canvas" alt="scale"/></span>
	</div>
	</div>
	<div id="wrktxt-box">
		<div id="gal-nav-box">
			<span id="gal-nav-prev" class="gal-nav gal-button"> ← </span>
			<span id="gal-nav-ordinal"></span> 
			<span id="gal-nav-next" class="gal-nav gal-button"> → </span>
		</div>
		<div id="wrk-titre">
		
		</div>
		<div id="wrktxt">
		
		</div>
		<div id="wrk-tech">
		
		</div>
	</div>
	
	';
	echo $ret;
}

function Stibnite_gal_content($post)
{
	header('Content-type: application/json');
	$pid = $post->ID;
	$content = apply_filters('the_content', $post->post_content);
	$pre_imgs = array();
	$src_imgs = array();
	$desc = array();
	$title = array();
	$tid = -1;
	if(has_post_thumbnail($pid))
	{
		$tid = get_post_thumbnail_id($pid);
		$pre_imgs[] = wp_get_attachment_image_src( $tid, 'medium');
		$src_imgs[] = wp_get_attachment_image_src($tid, 'full');
		$ft = get_post($tid);
		$desc[] = $ft->post_content;
		$title[] = $ft->post_title;
	}
	$atts = Stibnite_get_images($pid);
	foreach($atts as $a)
	{
		if($a->ID != $tid)
		{
			$pre_imgs[] = wp_get_attachment_image_src( $a->ID, 'medium');
			$src_imgs[] = wp_get_attachment_image_src($a->ID, 'full');
			$desc[] = $a->post_content;
			$title[] = $a->post_title;
		}
	}
	
	$ret = array();
	$ret["count"] = count($title);
	$ret["content"] = $content;
	$ret["pre_img"] = $pre_imgs;
	$ret["src_img"] = $src_imgs;
	$ret["desc"] = $desc;
	$ret["title"] = $title;
	echo json_encode($ret);
	
}


function Stibnite_make_date($d, $long = FALSE)
{
    if($long === FALSE)
        return str_replace(' ', '&nbsp;', date("d ― m", strtotime($d)));
}

?>
