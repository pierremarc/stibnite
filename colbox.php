<?php

/**

    Columns box [colobox.php]
    
    Author: Pierre Marchand
    Date: 2012-02-17

*/

?>

<div id="colbox">

<?php
$works = Stibnite_get_works();
$col_c = floor(count($works) / 3);
$mod = count($works) % 3;

foreach(array(1,2,3) as $i=>$ci)
{
    $mod_comp = 0;
    if($mod > 0)
    {
        $mod_comp = 1;
        $mod -= 1;
    }
    echo '<div id="col'.$ci.'">';
    Stibnite_make_wrk_cols($works, $ci);
    echo '</div>';
}
?>

</div>
