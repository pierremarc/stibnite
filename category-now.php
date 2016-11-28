<?php

/**

    Now [category-now.php]
    
    Author: Pierre Marchand
    Date: 2012-02-17

*/

$ret_array = array();

if(have_posts() )
{ 
    $first = ' post-item-first';
    while(have_posts())
    {
        the_post();
        
	$ret_str = "";
	$date = new DateTime($post->post_date);
        $ret_str .= '<div class="post-item'.$first.'">';
        $day = $date->format('d');
        $month = $date->format('m');
        $year = $date->format('Y');
        $ret_str .= '<div class="post-date">
                <div class="post-date-day">'.$day.'&nbsp;―</div>
                <div class="post-date-month">'.$month.'&nbsp;―</div>
                <div class="post-date-year">'.$year.'</div>';
        $ret_str .= '</div>';
        
        if ( has_post_thumbnail() ) 
        {
            $ret_str .= get_the_post_thumbnail($post->ID, 'medium');
        }
        $ret_str .= '<div class="post-item-content">';
        $ret_str .= get_the_content();
	$ret_str .= '</div>';
	$ret_str .= '</div>';
        
        $first = '';
        
        array_push($ret_array, $ret_str);
    }
}


if($wp_query->is_paged != 1)
{
	get_header();
	get_sidebar();

	echo implode($ret_array);

	echo '<script>
	var current_page = 1;
	var max_page = '.$wp_query->max_num_pages.';
	$(document).ready(function(){
		var more_box = $("<div></div>");
		var more = $("<div id=\"more_button\">More Nows</div>");
		more.on("click", function(){
				current_page += 1;
				var target_top = more.offset().top;
				if(current_page >= max_page)
				{
					more.hide();
				}
				$.ajax({
					url: "'.site_url().'/category/now/page/" + current_page,
					dataType: "json",
					success: function(data){
						$.each(data, function(key, val) {
								more_box.append(val);
							});
						$("html,body").animate({scrollTop : target_top}, 1000);
						}
				});
				
			});
		
		var body = $("body");
		body.append(more_box);
		body.append(more);
		});
		
	</script>';


	get_footer();
}
else
{
	echo json_encode($ret_array);
}


