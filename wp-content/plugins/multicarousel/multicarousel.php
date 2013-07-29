<?php
/*
Plugin Name: Multiple Carousel
Plugin URI: http://www.tejuscreative.com
Description: This plugin is used to create multiple carousel
Version: 2.0
Author: dhananjaysingh, Ashwani Dhiman
Author URI: http://www.tejuscreative.com
License:  GPL2
*/
?>
<?php
/* call a function named as_tejus_crsl_activate to create a datbase at the activation of plugin*/
register_activation_hook( __FILE__, 'as_tejus_crsl_activate' );
/* call a function named as_tejus_crsl_deactivate to delete already craeted datbase at the deactivation of plugin*/
register_deactivation_hook( __FILE__, 'as_tejus_crsl_deactivate' );
/* craeting daatabase*/
function as_tejus_crsl_activate(){
global $wpdb;
$as_tejus_crsl_table_name_frst = $wpdb->prefix."as_tejus_crsl";
$sql = "CREATE TABLE  ".$as_tejus_crsl_table_name_frst." (
         crsl_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
		 name VARCHAR(100)
       );" ;
$wpdb->query($sql);
$as_tejus_crsl_table_name_sec = $wpdb->prefix."as_tejus_crsl_sec";
$sql = "CREATE TABLE  ".$as_tejus_crsl_table_name_sec." (
         id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
		 crsl_id INT,
		 path VARCHAR(300),
		 crslorder INT,
		 url VARCHAR(200)
		
       );" ;
$wpdb->query($sql);
$as_tejus_crsl_table_name_thrd = $wpdb->prefix."as_tejus_crsl_thrd";
$sql = "CREATE TABLE  ".$as_tejus_crsl_table_name_thrd." (
        crsl_id INT NOT NULL PRIMARY KEY,
		auto INT,
		scroll INT,
		animation VARCHAR(20),
		rtl VARCHAR(20),
		wrap VARCHAR(10),
		vertical VARCHAR(20),
		visible INT,
		start INT,
		easing VARCHAR(20),
		skin VARCHAR(20),
		cntrwidth INT,
		cntrheight INT,
		imgwidth INT,
		imgheight INT
       );" ;
$wpdb->query($sql);
}
/*deleting database*/
function as_tejus_crsl_deactivate(){
global $wpdb;
$as_tejus_crsl_table_name_frst = $wpdb->prefix."as_tejus_crsl";
$sql= "DROP TABLE IF EXISTS ".$as_tejus_crsl_table_name_frst ;
$wpdb->query($sql);
$as_tejus_crsl_table_name_sec = $wpdb->prefix."as_tejus_crsl_sec";
$sql= "DROP TABLE IF EXISTS ".$as_tejus_crsl_table_name_sec ;
$wpdb->query($sql); 
$as_tejus_crsl_table_name_thrd = $wpdb->prefix."as_tejus_crsl_thrd";
$sql= "DROP TABLE IF EXISTS ".$as_tejus_crsl_table_name_thrd ;
$wpdb->query($sql); 
}
add_action('init','as_tejus_crsl_init');
/* function to include javascript file*/
function as_tejus_crsl_init() {
wp_enqueue_script( 'jquery' );
$as_tejus_crsl_path = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
wp_enqueue_style( 'muticarouselcs',  $as_tejus_crsl_path . "css/muticarouselcs.css");

wp_enqueue_script('carsoueljs', $as_tejus_crsl_path . "js/jquery.jcarousel.min.js");
}
add_action('admin_menu','as_tejus_crsl_toaddmymenu');
/* to add various menus of plugin to admin panel*/
function as_tejus_crsl_toaddmymenu(){
/* adding top level menu */
add_menu_page( 'multiple carousel', 'Carousel', 'manage_options', 'as_tejus_crsl', 'as_tejus_crsl_carousel' );
add_submenu_page('as_tejus_crsl','add option','Image','manage_options','as_tejus_crsl_options','as_tejus_crsl_image');
add_submenu_page('as_tejus_crsl','add option','Setting','manage_options','as_tejus_crsl_setting','as_tejus_crsl_setting');
}
function as_tejus_crsl_carousel(){
$as_tejus_crsl_path = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
?>
<div class="wholeform">
<h5>use this shortcode in posts or pages. </h5>
<h5>[as_tejus_mu_carousel id=carousel id]</h5>
</div>
<div class="wholeform">
<form name="createcrsl" method="post" enctype="multipart/form-data">
<label> Carousel Name </label><input name="name" id="name" type="text" value="" size="35">
<input type="hidden" value="1" name="createvalueform" id="createvalueform"/>
<input type="submit" name="createbutton" value="Create" />
</form></div>
<?php
if($_POST['createvalueform']){
$as_tejus_crsl_name = $_POST['name'];
global $wpdb;
$as_tejus_crsl_table_name_frst =$wpdb->prefix."as_tejus_crsl";
$sql= "SELECT crsl_id FROM ".$as_tejus_crsl_table_name_frst."  WHERE name ='{$as_tejus_crsl_name }'"; 
$alreadyexists =  $wpdb->get_results($sql, ARRAY_A);
$astejuscrouselexp= "/^[a-zA-Z]+$/";
$matched = preg_match($astejuscrouselexp, trim($as_tejus_crsl_name)) ;
if($alreadyexists){
  echo "<div class='head'><h3>a carousel with this name is alraedy exists</h3></div>";
 }
else{
if($matched){
$sql = "INSERT INTO ".$as_tejus_crsl_table_name_frst."  VALUES (DEFAULT,'{$as_tejus_crsl_name}') ";
$astejuscrtsucces= $wpdb->query($sql);
if($astejuscrtsucces){
 echo "<h2> your carousel is created succesfully </h2>";
 }
 }
 else{
 echo "<div class='head'><h3>use only letters</h3></div>";
 }
 }
}
global $wpdb;
$as_tejus_crsl_table_name_frst = $wpdb->prefix."as_tejus_crsl";
$sql= "SELECT * FROM ". $as_tejus_crsl_table_name_frst;
$astejuscraouseldatas = $wpdb->get_results($sql, ARRAY_A);
?>

<div class="wholecategorywimagebox">
<table class="widefat" style="width: 50%;margin-top: -30px;">
<thead>
<tr>
<th>CAROUSEL ID</th>
<th>NAME</th>
<th>Delete</th>
</tr>
</thead>
<tfoot>
<tr>
<th>CAROUSEL ID</th>
<th>NAME</th>
<th>Delete</th>
</tr>
</tfoot>
<tbody>
<?php
foreach($astejuscraouseldatas as $astejuscraouseldata){
echo '<tr>';
echo  '<td id="crslid'.$astejuscraouseldata['crsl_id'].'">'.$astejuscraouseldata['crsl_id'].'</td><td id="crslname'.$astejuscraouseldata['crsl_id'].'">'.$astejuscraouseldata['name'].'</td><td><input type="button" name="delete" value="delete" id = "as_tejus_crsl_delete'.$astejuscraouseldata['crsl_id'].'"  ></td>';
echo '</tr>';
}
?>
 </tbody>
</table></div>
<script>
jQuery(function($){ 
$.ajaxSetup({
error:function(x,e){
if(x.status==0){
alert('You are offline!!\n Please Check Your Network.');
}else if(x.status==404){
alert('Requested URL not found.');
}else if(x.status==500){
alert('Internel Server Error.');
}else if(e=='parsererror'){
alert('Error.\nParsing JSON Request failed.');
}else if(e=='timeout'){
alert('Request Time out.');
}else {
alert('Unknow Error.\n'+x.responseText);
}
}
});
<?php
global $wpdb;
$as_tejus_crsl_table_name_frst = $wpdb->prefix."as_tejus_crsl";
$sql= "SELECT * FROM ". $as_tejus_crsl_table_name_frst;
$astejuscraouseldatas = $wpdb->get_results($sql, ARRAY_A);
foreach($astejuscraouseldatas as $astejuscraouseldata){
?>
$("#as_tejus_crsl_delete<?php echo $astejuscraouseldata['crsl_id']; ?>").click( function (){
var id<?php echo $astejuscraouseldata['crsl_id']; ?> = $("#<?php echo 'crslid'.$astejuscraouseldata['crsl_id']; ?>").html();
var datastring<?php echo $astejuscraouseldata['crsl_id']; ?> = "crsl_id="+id<?php echo $astejuscraouseldata['crsl_id']; ?>+"&action=delete";
//alert(datastring<?php echo $astejuscraouseldata['crsl_id']; ?>);
 $.ajax({
      type: "POST",
      url: "<?php echo $as_tejus_crsl_path; ?>carouselupdate.php",
      data: datastring<?php echo $astejuscraouseldata['crsl_id']; ?>,
      success: function(html) {   
       window.location.replace("<?php echo $_SERVER['REQUEST_URI']; ?>");
       }
     }); });
<?php } ?>

});
</script>
<?php } ?>
<?php
function as_tejus_crsl_image(){ 
if($_POST['createvalueformse']){
$crslid = $_POST['crsl_id'];
$crsl_url = $_POST['url'];

if(isset($_FILES[ 'astejuscrslimage' ]) && ($_FILES[ 'astejuscrslimage']['size'] > 0)) {
require_once( ABSPATH . 'wp-admin/includes/file.php' );
// Get the type of the uploaded file. This is returned as "type/extension"
$arr_file_type = wp_check_filetype(basename($_FILES['astejuscrslimage']['name']));
$uploaded_file_type = $arr_file_type['type'];
// Set an array containing a list of acceptable formats
$allowed_file_types = array('image/jpg','image/jpeg','image/gif','image/png');
 // If the uploaded file is the right format
if(in_array($uploaded_file_type, $allowed_file_types)) {
 // Options array for the wp_handle_upload function. 'test_upload' => false
$upload_overrides = array( 'test_form' => false ); 
 // Handle the upload using WP's wp_handle_upload function. Takes the posted file and an options array
$returnigurlofimage = wp_handle_upload($_FILES['astejuscrslimage'], $upload_overrides);
 }
if($returnigurlofimage['url']){ $savethisimageurl = $returnigurlofimage['url'] ;
//echo $crslid;
//echo $savethisimageurl;
global $wpdb;
$as_tejus_crsl_table_name_sec = $wpdb->prefix."as_tejus_crsl_sec";
$sql= "SELECT * FROM ". $as_tejus_crsl_table_name_sec. " WHERE crsl_id= ".$crslid;
$astejuscraouseldatas= $wpdb->get_results($sql, ARRAY_A);
$counter = 0;
foreach($astejuscraouseldatas as $astejuscraouseldata){
$counter++;
}
$counter = $counter+1; 
$as_tejus_crsl_table_name_sec = $wpdb->prefix."as_tejus_crsl_sec";
$sql = "INSERT INTO ".$as_tejus_crsl_table_name_sec."  VALUES (DEFAULT,{$crslid},'{$savethisimageurl}',{$counter},'{$crsl_url}') ";
$wpdb->query($sql);
}
}
}
$as_tejus_crsl_path = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
global $wpdb;
$as_tejus_crsl_table_name_sec = $wpdb->prefix."as_tejus_crsl_sec";
$sql= "SELECT * FROM ". $as_tejus_crsl_table_name_sec;
$astejuscraouseldatas= $wpdb->get_results($sql, ARRAY_A);
?>
<div class="wholeform">
<form name="createform" method="post" enctype="multipart/form-data">
<input name="astejuscrslimage" type="file" value="" size="100" >
<?php
global $wpdb;
$as_tejus_crsl_table_name_frst = $wpdb->prefix."as_tejus_crsl";
$sql= "SELECT * FROM ". $as_tejus_crsl_table_name_frst;
$astejuscraouseldatas = $wpdb->get_results($sql, ARRAY_A);
echo "<label> Carousel Name </label>";
echo '<select name="crsl_id">';
foreach($astejuscraouseldatas as $astejuscraouseldata){
echo '<option value="' .$astejuscraouseldata['crsl_id'].'">'.$astejuscraouseldata['name'].'</option>';
}
echo "</select>";
?>
<input type="hidden" value="1" name="createvalueformse"/>
<input type="submit" name="createbutton" value="upload" />
</form>
</div>
<div class="wholecategorywimagebox">
<?php
global $wpdb;
$as_tejus_crsl_table_name_frst = $wpdb->prefix."as_tejus_crsl";
$sql= "SELECT * FROM ". $as_tejus_crsl_table_name_frst;
$astejuscraouseldatas = $wpdb->get_results($sql, ARRAY_A);

foreach($astejuscraouseldatas as $astejuscraouseldataq){
?>
<table class="widefat" style="width: 70%;margin-top: -30px;margin-bottom: 50px;">
<thead>
<tr>
<th>ID</th>
<th>CAROUSEL ID</th>
<th>CAROUSEL NAME</th>
<th>IMAGE</th>
<th>Order</th>
<th>Link URL</th>
<th>UPDATE URL</th>
<th>Delete</th>
</tr>
</thead>
<tfoot>
<tr>
<th>ID</th>
<th>CAROUSEL ID</th>
<th>CAROUSEL NAME</th>
<th>IMAGE</th>
<th>order</th>
<th>Link URL</th>
<th>UPDATE URL</th>
<th>Delete</th>
</tr>
</tfoot>
<tbody>
<?php
global $wpdb;
$as_tejus_crsl_table_name_sec = $wpdb->prefix."as_tejus_crsl_sec";
$sql= "SELECT * FROM ". $as_tejus_crsl_table_name_sec. " WHERE crsl_id= ".$astejuscraouseldataq['crsl_id'];
$astejuscraouseldatas= $wpdb->get_results($sql, ARRAY_A);
$counter = 0;
foreach($astejuscraouseldatas as $astejuscraouseldata){
$counter++;
}
foreach($astejuscraouseldatas as $astejuscraouseldata){





$as_tejus_crsl_table_name_frst = $wpdb->prefix."as_tejus_crsl";
$sql= "SELECT name FROM ". $as_tejus_crsl_table_name_frst. " WHERE crsl_id= ".$astejuscraouseldata['crsl_id'];
$nameofmycarousel = $wpdb->get_row($sql, ARRAY_A);
echo '<tr>';
echo '<td id="imgthisid'.$astejuscraouseldata['id'].'">'.$astejuscraouseldata['id'].'</td><td id="imgthisids'.$astejuscraouseldata['id'].'">'.$astejuscraouseldata['crsl_id'].'</td><td id="imgthisname'.$astejuscraouseldata['id'].'">'.$nameofmycarousel['name'].'</td><td><img src="'.$astejuscraouseldata['path'].'" width="50" height="50"/></td><td><select name="crl_order" id="crslorder'.$astejuscraouseldata['id'].'">';
for( $i=1; $i<=$counter; $i++){
if($astejuscraouseldata['crslorder']== $i){
echo '<option selected ="selected" value = "'.$i.'">'.$i.'</option>';
}
else{
echo '<option value = "'.$i.'">'.$i.'</option>';

}
}
echo '</select></td><td><input type="text" name="url" value="'.$astejuscraouseldata['url'].' " id="urlimagesis'.$astejuscraouseldata['id'].'"></td><td><input type="button" name="update" id = "as_tejus_crsl_update'.$astejuscraouseldata['id'].'" value="update url" ></td><td><input type="button" name="delete" value="delete" id = "as_tejus_secrsl_delete'.$astejuscraouseldata['id'].'"  ></td>';
echo '</tr>';
}
?>
</tbody>
</table>
<?php  }?>
</div>
<script>
 jQuery(function($){ 
 $.ajaxSetup({
  error:function(x,e){
   if(x.status==0){
   alert('You are offline!!\n Please Check Your Network.');
   }else if(x.status==404){
   alert('Requested URL not found.');
   }else if(x.status==500){
   alert('Internel Server Error.');
   }else if(e=='parsererror'){
   alert('Error.\nParsing JSON Request failed.');
   }else if(e=='timeout'){
   alert('Request Time out.');
   }else {
   alert('Unknow Error.\n'+x.responseText);
   }
  }
 });
<?php
global $wpdb;
$as_tejus_crsl_table_name_sec = $wpdb->prefix."as_tejus_crsl_sec";
$sql= "SELECT * FROM ". $as_tejus_crsl_table_name_sec;
$astejuscraouseldatas = $wpdb->get_results($sql, ARRAY_A);
foreach($astejuscraouseldatas as $astejuscraouseldata){
?>
$("#as_tejus_crsl_update<?php echo $astejuscraouseldata['id']; ?>").click( function (){
var id<?php echo $astejuscraouseldata['id']; ?> = $("#<?php echo 'imgthisid'.$astejuscraouseldata['id']; ?>").html();
var url<?php echo $astejuscraouseldata['id']; ?> = $("#<?php echo 'urlimagesis'.$astejuscraouseldata['id']; ?>").val();
var crslorder<?php echo $astejuscraouseldata['id']; ?> = $("#<?php echo 'crslorder'.$astejuscraouseldata['id']; ?>").val();
var datastring<?php echo $astejuscraouseldata['id']; ?> = "id="+id<?php echo $astejuscraouseldata['id']; ?>+"&url="+url<?php echo $astejuscraouseldata['id']; ?>+"&crslorder="+crslorder<?php echo $astejuscraouseldata['id']; ?>+"&action=update";
//alert(datastring<?php echo $astejuscraouseldata['id']; ?>);
 $.ajax({
    type: "POST",
      url: "<?php echo $as_tejus_crsl_path; ?>crslupdate.php",
      data: datastring<?php echo $astejuscraouseldata['id']; ?>,

      success: function(html) {
       
       window.location.replace("<?php echo $_SERVER['REQUEST_URI']; ?>");
       
      }
     }); });


 $("#as_tejus_secrsl_delete<?php echo $astejuscraouseldata['id']; ?>").click( function (){
var id<?php echo $astejuscraouseldata['id']; ?> = $("#<?php echo 'imgthisid'.$astejuscraouseldata['id']; ?>").html();
var datastring<?php echo $astejuscraouseldata['id']; ?> = "id="+id<?php echo $astejuscraouseldata['id']; ?>+"&action=delete";
//alert(datastring<?php echo $astejuscraouseldata['crsl_id']; ?>);
 $.ajax({
      type: "POST",
      url: "<?php echo $as_tejus_crsl_path; ?>crslupdate.php",
      data: datastring<?php echo $astejuscraouseldata['id']; ?>,

      success: function(html) {
       
       window.location.replace("<?php echo $_SERVER['REQUEST_URI']; ?>");
       
      }
     }); });
<?php } ?>
});
 </script>
 <?php } 
function as_tejus_carousel($atts){
extract(shortcode_atts(array(
 "id" => '1'
 ), $atts));
$as_tejus_crsl_path = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));

$output='
<script type="text/javascript">
function mycarousel_initCallback(carousel)
{
    // Disable autoscrolling if the user clicks the prev or next button.
    carousel.buttonNext.bind("click", function() {
        carousel.startAuto(0);
    });

    carousel.buttonPrev.bind("click", function() {
        carousel.startAuto(0);
    });

    // Pause autoscrolling if the user moves with the cursor over the clip.
    carousel.clip.hover(function() {
        carousel.stopAuto();
    }, function() {
        carousel.startAuto();
    });
};
jQuery.easing["BounceEaseOut"] = function(p, t, b, c, d) {
	if ((t/=d) < (1/2.75)) {
		return c*(7.5625*t*t) + b;
	} else if (t < (2/2.75)) {
		return c*(7.5625*(t-=(1.5/2.75))*t + .75) + b;
	} else if (t < (2.5/2.75)) {
		return c*(7.5625*(t-=(2.25/2.75))*t + .9375) + b;
	} else {
		return c*(7.5625*(t-=(2.625/2.75))*t + .984375) + b;
	}
}';
 global $wpdb;
$as_tejus_crsl_table_name_frst = $wpdb->prefix."as_tejus_crsl";
$sql = 'select name from ' .$as_tejus_crsl_table_name_frst .' where crsl_id =' . $id;
$astejuscraouselname = $wpdb->get_row($sql, ARRAY_A);
global $wpdb;
$as_tejus_crsl_table_name_thrd = $wpdb->prefix."as_tejus_crsl_thrd";
$sql = 'select * from ' .$as_tejus_crsl_table_name_thrd. ' where crsl_id=' .$id;
$astejuscrsldt = $wpdb->get_row($sql, ARRAY_A);
$output.='
jQuery(document).ready(function() {
    jQuery("#'.$astejuscraouselname['name'].' ").jcarousel({
        auto:'; if($astejuscrsldt['auto']){ $output.= $astejuscrsldt['auto']; }else{ $output.= ' 3';} $output.=' ,
		scroll: '; if($astejuscrsldt['scroll']){$output.= $astejuscrsldt['scroll']; }else{ $output.= ' 2';} $output.=',
		animation: "';  if($astejuscrsldt['animation']){$output.= $astejuscrsldt['animation']; }else{ $output.= 'slow';}  $output.='",
		rtl:';  if($astejuscrsldt['rtl']){$output.= $astejuscrsldt['rtl']; }else{ $output.= 'false';} $output.=',
        wrap: "'; if($astejuscrsldt['wrap']){$output.= $astejuscrsldt['wrap']; }else{ $output.= 'both';} $output.='",
		vertical: ';  if($astejuscrsldt['vertical']){$output.= $astejuscrsldt['vertical']; }else{ $output.= 'false';} $output.=',
		visible: ';  if($astejuscrsldt['visible']){$output.=  $astejuscrsldt['visible']; }else{ $output.= ' 3';} $output.=',
		start: ';  if($astejuscrsldt['start']){$output.= $astejuscrsldt['start']; }else{ $output.= ' 1';} $output.=',
		
		easing:"' . $astejuscrsldt['easing'].'",
        initCallback: mycarousel_initCallback
    });
});
</script>

<STYLE type="text/css">
.jcarousel-skin-'; if($astejuscrsldt['skin']){$output.= $astejuscrsldt['skin']; }else {$output.=  'tango';} $output.= $astejuscrsldt['crsl_id']; $output.= ' .jcarousel-container-horizontal {
    width:';  if($astejuscrsldt['cntrwidth']){$output.=  $astejuscrsldt['cntrwidth']; }else {$output.= ' 245';} $output.= 'px;';
   $output.= ' padding: 20px 40px;';
	$output.= 'height: '; if($astejuscrsldt['cntrheight']){$output.=  $astejuscrsldt['cntrheight']; }else {$output.= '75';} $output.= 'px;
}
.jcarousel-skin-'; if($astejuscrsldt['skin']){$output.= $astejuscrsldt['skin']; }else {$output.=  'tango';} $output.= $astejuscrsldt['crsl_id']; $output.= ' .jcarousel-container-vertical {
    width:';  if($astejuscrsldt['cntrwidth']){$output.=  $astejuscrsldt['cntrwidth']; }else {$output.= ' 75';} $output.= 'px;';
   $output.= ' padding: 40px 20px;';
	$output.= 'height: '; if($astejuscrsldt['cntrheight']){$output.=  $astejuscrsldt['cntrheight']; }else {$output.= '245';} $output.= 'px;
}
.jcarousel-skin-'; if($astejuscrsldt['skin']){$output.=  $astejuscrsldt['skin']; }else {$output.=  'tango';} $output.=  $astejuscrsldt['crsl_id']; $output.=  '.jcarousel-clip-horizontal {
    width:';  if($astejuscrsldt['cntrwidth']){$output.=  $astejuscrsldt['cntrwidth']; }else {$output.=  '245';} $output.= 'px;
     height:';  if($astejuscrsldt['cntrheight']){$output.=  $astejuscrsldt['cntrheight']; }else {$output.= ' 75';} $output.= 'px;
}

.jcarousel-skin-';  if($astejuscrsldt['skin']){$output.=  $astejuscrsldt['skin']; }else {$output.=  'tango';} $output.= $astejuscrsldt['crsl_id']; $output.= ' .jcarousel-clip-vertical {
    width: ';  if($astejuscrsldt['cntrwidth']){$output.= $astejuscrsldt['cntrwidth']; }else {$output.= ' 75';} $output.= 'px;
    height:';  if($astejuscrsldt['cntrheight']){$output.=  $astejuscrsldt['cntrheight']; }else {$output.= ' 245';} $output.= 'px;

}
';



$output.= '.jcarousel-skin-tango'.$astejuscrsldt['crsl_id'].' .jcarousel-container {
    -moz-border-radius: 10px;
    -webkit-border-radius: 10px;
}

.jcarousel-skin-tango'.$astejuscrsldt['crsl_id'].' .jcarousel-direction-rtl {
	direction: rtl;
}





.jcarousel-skin-tango'.$astejuscrsldt['crsl_id'].' .jcarousel-clip {
    overflow: hidden;
}


.jcarousel-skin-tango'.$astejuscrsldt['crsl_id'].' .jcarousel-item-horizontal {
	margin-left: 0;
    margin-right: 10px;
}

.jcarousel-skin-tango'.$astejuscrsldt['crsl_id'].' .jcarousel-direction-rtl .jcarousel-item-horizontal {
	margin-left: 10px;
    margin-right: 0;
}

.jcarousel-skin-tango'.$astejuscrsldt['crsl_id'].' .jcarousel-item-vertical {
    margin-bottom: 10px;
}

.jcarousel-skin-tango'.$astejuscrsldt['crsl_id'].' .jcarousel-item-placeholder {
    background: #fff;
    color: #000;
}

/**
 *  Horizontal Buttons
 */
.jcarousel-skin-tango'.$astejuscrsldt['crsl_id'].' .jcarousel-next-horizontal {
    position: absolute;
    top: 43px;
    right: 5px;
    width: 32px;
    height: 32px;
    cursor: pointer;
    background: transparent url('. $as_tejus_crsl_path.'/images/next-horizontal.png) no-repeat 0 0;
}

.jcarousel-skin-tango'.$astejuscrsldt['crsl_id'].' .jcarousel-direction-rtl .jcarousel-next-horizontal {
    left: 5px;
    right: auto;
    background-image: url('. $as_tejus_crsl_path.'/images/prev-horizontal.png);
}

.jcarousel-skin-tango'.$astejuscrsldt['crsl_id'].' .jcarousel-next-horizontal:hover,
.jcarousel-skin-tango'.$astejuscrsldt['crsl_id'].' .jcarousel-next-horizontal:focus {
    background-position: -32px 0;
}

.jcarousel-skin-tango'.$astejuscrsldt['crsl_id'].' .jcarousel-next-horizontal:active {
    background-position: -64px 0;
}

.jcarousel-skin-tango'.$astejuscrsldt['crsl_id'].' .jcarousel-next-disabled-horizontal,
.jcarousel-skin-tango'.$astejuscrsldt['crsl_id'].' .jcarousel-next-disabled-horizontal:hover,
.jcarousel-skin-tango'.$astejuscrsldt['crsl_id'].' .jcarousel-next-disabled-horizontal:focus,
.jcarousel-skin-tango'.$astejuscrsldt['crsl_id'].' .jcarousel-next-disabled-horizontal:active {
    cursor: default;
    background-position: -96px 0;
}

.jcarousel-skin-tango'.$astejuscrsldt['crsl_id'].' .jcarousel-prev-horizontal {
    position: absolute;
    top: 43px;
    left: 5px;
    width: 32px;
    height: 32px;
    cursor: pointer;
    background: transparent url('. $as_tejus_crsl_path.'/images/prev-horizontal.png) no-repeat 0 0;
}

.jcarousel-skin-tango'.$astejuscrsldt['crsl_id'].' .jcarousel-direction-rtl .jcarousel-prev-horizontal {
    left: auto;
    right: 5px;
    background-image: url('. $as_tejus_crsl_path.'/images/next-horizontal.png);
}

.jcarousel-skin-tango'.$astejuscrsldt['crsl_id'].' .jcarousel-prev-horizontal:hover, 
.jcarousel-skin-tango'.$astejuscrsldt['crsl_id'].' .jcarousel-prev-horizontal:focus {
    background-position: -32px 0;
}

.jcarousel-skin-tango'.$astejuscrsldt['crsl_id'].' .jcarousel-prev-horizontal:active {
    background-position: -64px 0;
}

.jcarousel-skin-tango'.$astejuscrsldt['crsl_id'].' .jcarousel-prev-disabled-horizontal,
.jcarousel-skin-tango'.$astejuscrsldt['crsl_id'].' .jcarousel-prev-disabled-horizontal:hover,
.jcarousel-skin-tango'.$astejuscrsldt['crsl_id'].' .jcarousel-prev-disabled-horizontal:focus,
.jcarousel-skin-tango'.$astejuscrsldt['crsl_id'].' .jcarousel-prev-disabled-horizontal:active {
    cursor: default;
    background-position: -96px 0;
}

/**
 *  Vertical Buttons
 */
.jcarousel-skin-tango'.$astejuscrsldt['crsl_id'].' .jcarousel-next-vertical {
    position: absolute;
    bottom: 5px;
    left: 43px;
    width: 32px;
    height: 32px;
    cursor: pointer;
    background: transparent url('. $as_tejus_crsl_path.'/images/next-vertical.png) no-repeat 0 0;
}

.jcarousel-skin-tango'.$astejuscrsldt['crsl_id'].' .jcarousel-next-vertical:hover,
.jcarousel-skin-tango'.$astejuscrsldt['crsl_id'].' .jcarousel-next-vertical:focus {
    background-position: 0 -32px;
}

.jcarousel-skin-tango'.$astejuscrsldt['crsl_id'].' .jcarousel-next-vertical:active {
    background-position: 0 -64px;
}

.jcarousel-skin-tango'.$astejuscrsldt['crsl_id'].' .jcarousel-next-disabled-vertical,
.jcarousel-skin-tango'.$astejuscrsldt['crsl_id'].' .jcarousel-next-disabled-vertical:hover,
.jcarousel-skin-tango'.$astejuscrsldt['crsl_id'].' .jcarousel-next-disabled-vertical:focus,
.jcarousel-skin-tango'.$astejuscrsldt['crsl_id'].' .jcarousel-next-disabled-vertical:active {
    cursor: default;
    background-position: 0 -96px;
}

.jcarousel-skin-tango'.$astejuscrsldt['crsl_id'].' .jcarousel-prev-vertical {
    position: absolute;
    top: 5px;
    left: 43px;
    width: 32px;
    height: 32px;
    cursor: pointer;
    background: transparent url('. $as_tejus_crsl_path.'/images/prev-vertical.png) no-repeat 0 0;
}

.jcarousel-skin-tango'.$astejuscrsldt['crsl_id'].' .jcarousel-prev-vertical:hover,
.jcarousel-skin-tango'.$astejuscrsldt['crsl_id'].' .jcarousel-prev-vertical:focus {
    background-position: 0 -32px;
}

.jcarousel-skin-tango'.$astejuscrsldt['crsl_id'].' .jcarousel-prev-vertical:active {
    background-position: 0 -64px;
}

.jcarousel-skin-tango'.$astejuscrsldt['crsl_id'].' .jcarousel-prev-disabled-vertical,
.jcarousel-skin-tango'.$astejuscrsldt['crsl_id'].' .jcarousel-prev-disabled-vertical:hover,
.jcarousel-skin-tango'.$astejuscrsldt['crsl_id'].' .jcarousel-prev-disabled-vertical:focus,
.jcarousel-skin-tango'.$astejuscrsldt['crsl_id'].' .jcarousel-prev-disabled-vertical:active {
    cursor: default;
    background-position: 0 -96px;
}
.jcarousel-skin-ie7'.$astejuscrsldt['crsl_id'].' .jcarousel-container {
    -moz-border-radius: 10px;
    -webkit-border-radius: 10px;
    border-radius: 10px;
    background: #D4D0C8;
    border: 1px solid #808080;
}

.jcarousel-skin-ie7'.$astejuscrsldt['crsl_id'].' .jcarousel-direction-rtl {
	direction: rtl;
}




.jcarousel-skin-ie7'.$astejuscrsldt['crsl_id'].' .jcarousel-clip {
    overflow: hidden;
}





.jcarousel-skin-ie7'.$astejuscrsldt['crsl_id'].' .jcarousel-item:hover,
.jcarousel-skin-ie7'.$astejuscrsldt['crsl_id'].' .jcarousel-item:focus {
    border-color: #808080;
}

.jcarousel-skin-ie7'.$astejuscrsldt['crsl_id'].' .jcarousel-item-horizontal {
    margin-left: 0;
    margin-right: 7px;
}

.jcarousel-skin-ie7'.$astejuscrsldt['crsl_id'].' .jcarousel-direction-rtl .jcarousel-item-horizontal {
	margin-left: 7px;
	margin-right: 0;
}

.jcarousel-skin-ie7'.$astejuscrsldt['crsl_id'].' .jcarousel-item-vertical {
    margin-bottom: 7px;
}

.jcarousel-skin-ie7'.$astejuscrsldt['crsl_id'].' .jcarousel-item-placeholder {
}

/**
 *  Horizontal Buttons
 */
.jcarousel-skin-ie7'.$astejuscrsldt['crsl_id'].' .jcarousel-next-horizontal {
    position: absolute;
    top: 43px;
    right: 5px;
    width: 32px;
    height: 32px;
    cursor: pointer;
    background: transparent url('. $as_tejus_crsl_path.'/images/next-horizontal.gif) no-repeat 0 0;
}

.jcarousel-skin-ie7'.$astejuscrsldt['crsl_id'].' .jcarousel-direction-rtl .jcarousel-next-horizontal {
    left: 5px;
    right: auto;

    background-image: url('. $as_tejus_crsl_path.'/images/prev-horizontal.gif);
}

.jcarousel-skin-ie7'.$astejuscrsldt['crsl_id'].' .jcarousel-next-horizontal:hover,
.jcarousel-skin-ie7'.$astejuscrsldt['crsl_id'].' .jcarousel-next-horizontal:focus {
    background-position: -32px 0;
}

.jcarousel-skin-ie7'.$astejuscrsldt['crsl_id'].' .jcarousel-next-horizontal:active {
    background-position: -64px 0;
}

.jcarousel-skin-ie7'.$astejuscrsldt['crsl_id'].' .jcarousel-next-disabled-horizontal,
.jcarousel-skin-ie7'.$astejuscrsldt['crsl_id'].' .jcarousel-next-disabled-horizontal:hover,
.jcarousel-skin-ie7'.$astejuscrsldt['crsl_id'].' .jcarousel-next-disabled-horizontal:focus,
.jcarousel-skin-ie7'.$astejuscrsldt['crsl_id'].' .jcarousel-next-disabled-horizontal:active {
    cursor: default;
    background-position: -96px 0;
}

.jcarousel-skin-ie7'.$astejuscrsldt['crsl_id'].' .jcarousel-prev-horizontal {
    position: absolute;
    top: 43px;
    left: 5px;
    width: 32px;
    height: 32px;
    cursor: pointer;
    background: transparent url('. $as_tejus_crsl_path.'/images/prev-horizontal.gif) no-repeat 0 0;
}

.jcarousel-skin-ie7'.$astejuscrsldt['crsl_id'].' .jcarousel-direction-rtl .jcarousel-prev-horizontal {
    left: auto;
    right: 5px;
    background-image: url('. $as_tejus_crsl_path.'/images/next-horizontal.gif);
}

.jcarousel-skin-ie7'.$astejuscrsldt['crsl_id'].' .jcarousel-prev-horizontal:hover,
.jcarousel-skin-ie7'.$astejuscrsldt['crsl_id'].' .jcarousel-prev-horizontal:focus {
    background-position: -32px 0;
}

.jcarousel-skin-ie7'.$astejuscrsldt['crsl_id'].' .jcarousel-prev-horizontal:active {
    background-position: -64px 0;
}

.jcarousel-skin-ie7'.$astejuscrsldt['crsl_id'].' .jcarousel-prev-disabled-horizontal,
.jcarousel-skin-ie7'.$astejuscrsldt['crsl_id'].' .jcarousel-prev-disabled-horizontal:hover,
.jcarousel-skin-ie7'.$astejuscrsldt['crsl_id'].' .jcarousel-prev-disabled-horizontal:focus,
.jcarousel-skin-ie7'.$astejuscrsldt['crsl_id'].' .jcarousel-prev-disabled-horizontal:active {
    cursor: default;
    background-position: -96px 0;
}

/**
 *  Vertical Buttons
 */
.jcarousel-skin-ie7'.$astejuscrsldt['crsl_id'].' .jcarousel-next-vertical {
    position: absolute;
    bottom: 5px;
    left: 43px;
    width: 32px;
    height: 32px;
    cursor: pointer;
    background: transparent url('. $as_tejus_crsl_path.'/images/next-vertical.png) no-repeat 0 0;
}

.jcarousel-skin-ie7'.$astejuscrsldt['crsl_id'].' .jcarousel-next-vertical:hover,
.jcarousel-skin-ie7'.$astejuscrsldt['crsl_id'].' .jcarousel-next-vertical:focus {
    background-position: 0 -32px;
}

.jcarousel-skin-ie7'.$astejuscrsldt['crsl_id'].' .jcarousel-next-vertical:active {
    background-position: 0 -64px;
}

.jcarousel-skin-ie7'.$astejuscrsldt['crsl_id'].' .jcarousel-next-disabled-vertical,
.jcarousel-skin-ie7'.$astejuscrsldt['crsl_id'].' .jcarousel-next-disabled-vertical:hover,
.jcarousel-skin-ie7'.$astejuscrsldt['crsl_id'].' .jcarousel-next-disabled-vertical:focus,
.jcarousel-skin-ie7'.$astejuscrsldt['crsl_id'].' .jcarousel-next-disabled-vertical:active {
    cursor: default;
    background-position: 0 -96px;
}

.jcarousel-skin-ie7'.$astejuscrsldt['crsl_id'].' .jcarousel-prev-vertical {
    position: absolute;
    top: 5px;
    left: 43px;
    width: 32px;
    height: 32px;
    cursor: pointer;
    background: transparent url('. $as_tejus_crsl_path.'/images/prev-vertical.png) no-repeat 0 0;
}

.jcarousel-skin-ie7'.$astejuscrsldt['crsl_id'].' .jcarousel-prev-vertical:hover,
.jcarousel-skin-ie7'.$astejuscrsldt['crsl_id'].' .jcarousel-prev-vertical:focus {
    background-position: 0 -32px;
}

.jcarousel-skin-ie7'.$astejuscrsldt['crsl_id'].' .jcarousel-prev-vertical:active {
    background-position: 0 -64px;
}

.jcarousel-skin-ie7'.$astejuscrsldt['crsl_id'].' .jcarousel-prev-disabled-vertical,
.jcarousel-skin-ie7'.$astejuscrsldt['crsl_id'].' .jcarousel-prev-disabled-vertical:hover,
.jcarousel-skin-ie7'.$astejuscrsldt['crsl_id'].' .jcarousel-prev-disabled-vertical:focus,
.jcarousel-skin-ie7'.$astejuscrsldt['crsl_id'].' .jcarousel-prev-disabled-vertical:active {
    cursor: default;
    background-position: 0 -96px;
}


</STYLE>';

$output.= '<ul id="'. $astejuscraouselname['name'] .'" class="jcarousel-skin-'; if($astejuscrsldt['skin']){$output.=  $astejuscrsldt['skin']; }else {$output.= 'tango'; } $output.=  $astejuscrsldt['crsl_id'].'">';
 global $wpdb;
		$as_tejus_crsl_table_name_sec = $wpdb->prefix."as_tejus_crsl_sec";
		$sql ='SELECT * from '.$as_tejus_crsl_table_name_sec. ' where crsl_id ='. $id.' ORDER BY crslorder ASC'; ;
		$astejuscraouseldatas = $wpdb->get_results($sql, ARRAY_A);
		foreach($astejuscraouseldatas as $astejuscraouseldata){
//    $output.= '<li><a href=" '. $astejuscraouseldata['url'].'" target="_blank"><img src="'.  $astejuscraouseldata['path'].'" width="';  if($astejuscrsldt['imgwidth']){$output.=  $astejuscrsldt['imgwidth']; }else {$output.= ' 75';} $output.= '" height="';  if($astejuscrsldt['imgheight']){$output.=  $astejuscrsldt['imgheight']; }else {$output.= ' 75';} $output.= '" alt="" /></a>';
      $output.= '<li><img src="'.  $astejuscraouseldata['path'].'" width="';  if($astejuscrsldt['imgwidth']){$output.=  $astejuscrsldt['imgwidth']; }else {$output.= ' 75';} $output.= '" height="';  if($astejuscrsldt['imgheight']){$output.=  $astejuscrsldt['imgheight']; }else {$output.= ' 75';} $output.= '" alt="" />';
 } 
 $output.= '</li></ul>';
 
 return $output;
 } 
 
 
add_shortcode( 'as_tejus_mu_carousel', 'as_tejus_carousel' ); 
function as_tejus_crsl_setting(){
if($_POST['savevalue']){
$as_tejus_crslid = $_POST['crsl_id'];
$as_tejus_auto = $_POST['auto'];
$as_tejus_scroll = $_POST['scroll'];
$as_tejus_animation = $_POST['animation'];
$as_tejus_rtl = $_POST['rtl'];
$as_tejus_wrap = $_POST['wrap'];
$as_tejus_vertical = $_POST['vertical'];
$as_tejus_visible = $_POST['visible'];
//$as_tejus_start = $_POST['start'];
$as_tejus_easing = $_POST['easing'];
$as_tejus_skin = $_POST['skin'];
$as_tejus_cntrwidth = $_POST['cntrwidth'];
$as_tejus_cntrheight = $_POST['cntrheight'];
$as_tejus_imgwidth = $_POST['imgwidth'];
$as_tejus_imgheight = $_POST['imgheight'];


global $wpdb;
$as_tejus_crsl_table_name_thrd = $wpdb->prefix."as_tejus_crsl_thrd";

$sql = 'select * from ' .$as_tejus_crsl_table_name_thrd .' where crsl_id =' . $as_tejus_crslid;
$astejuscraouselid = $wpdb->get_row($sql, ARRAY_A);
//echo $astejuscraouselid['crsl_id'];
if($astejuscraouselid['crsl_id']){
$sql = "UPDATE ".$as_tejus_crsl_table_name_thrd." SET auto = '".$as_tejus_auto."', scroll = '".$as_tejus_scroll."', animation = '".$as_tejus_animation."', rtl = '".$as_tejus_rtl."', wrap = '".$as_tejus_wrap."', vertical = '".$as_tejus_vertical."', visible = '".$as_tejus_visible."', start = '".$as_tejus_start."', easing = '".$as_tejus_easing."', skin = '".$as_tejus_skin."', cntrwidth = '".$as_tejus_cntrwidth ."', cntrheight = '".$as_tejus_cntrheight."', imgwidth = '".$as_tejus_imgwidth."', imgheight = '".$as_tejus_imgheight."' WHERE crsl_id= '".$as_tejus_crslid."' ";
$wpdb->query($sql);
}
else {
$sql = "INSERT INTO ".$as_tejus_crsl_table_name_thrd."  VALUES ('{$as_tejus_crslid}','{$as_tejus_auto}','{$as_tejus_scroll}','{$as_tejus_animation}','{$as_tejus_rtl}','{$as_tejus_wrap}','{$as_tejus_vertical}','{$as_tejus_visible}','{$as_tejus_start}','{$as_tejus_easing}','{$as_tejus_skin}','{$as_tejus_cntrwidth}','{$as_tejus_cntrheight}','{$as_tejus_imgwidth}','{$as_tejus_imgheight}') ";
$wpdb->query($sql);

}
}
?>
<?php
global $wpdb;
$as_tejus_crsl_table_name_frst = $wpdb->prefix."as_tejus_crsl";
$sql= "SELECT * FROM ". $as_tejus_crsl_table_name_frst;
$astejuscraouseldatas = $wpdb->get_results($sql, ARRAY_A);

foreach($astejuscraouseldatas as $astejuscraouseldataq){


?>
<form name="saveform" method="post" enctype="multipart/form-data">
<input type="hidden" name="savevalue" value="true" />
<div class="stfrm">
<table border="0" cellspacing="15" cellpadding="0" style="width: 23%; margin: 20px 28px 0px 0px; float: left;">

<tr>
<td><h4>Carousel Name</h4></td>
<td name="crsl_id" id="crsl_id"><h2>

<?php
echo $astejuscraouseldataq['name'];

global $wpdb;
$as_tejus_crsl_table_name_thrd = $wpdb->prefix."as_tejus_crsl_thrd";
$sql = 'select * from ' .$as_tejus_crsl_table_name_thrd. ' where crsl_id=' .$astejuscraouseldataq['crsl_id'];
$astejuscraouseldata = $wpdb->get_row($sql, ARRAY_A);

?></h2></td>

<input type="hidden" name="crsl_id" value="<?php echo $astejuscraouseldataq['crsl_id'];?>"/>

</tr>

<tr>
<td>Time Delay in Second</td>
<td><input type="text" name="auto" size="10" value="<?php echo $astejuscraouseldata['auto'];?>"></td>
</tr>

<tr>
<td>No. of Images to Scroll</td>
<td><input type="text" name="scroll" size="10" value="<?php echo $astejuscraouseldata['scroll'];?>"></td>
</tr>

<tr>
<td>Animation</td>
<td><select name="animation">
<option id="slow" <?php  if( $astejuscraouseldata['animation']=="slow"){echo "selected";} ?> >slow</option>
<option id="fast" <?php  if( $astejuscraouseldata['animation']=="fast"){echo "selected";} ?> >fast</option>
</select></td>
</tr>

<tr>
<td>Scroll right to left</td>
<td><select name="rtl" >
<option id="true" <?php  if( $astejuscraouseldata['rtl']=="true"){echo "selected";} ?> >true</option>
<option id="false" <?php  if( $astejuscraouseldata['rtl']=="false"){echo "selected";} ?> >false</option>
</select></td>
</tr>

<tr>
<td>wrap</td><td>
<select name="wrap">
<option id="both" <?php  if( $astejuscraouseldata['wrap']=="both"){echo "selected";} ?> >both</option>
<option id="first" <?php  if( $astejuscraouseldata['wrap']=="first"){echo "selected";} ?> >first</option>
<option id="last" <?php  if( $astejuscraouseldata['wrap']=="last"){echo "selected";} ?> >last</option>
</select></td>
</tr>

<tr>
<td>vertical slide</td>
<td><select name="vertical">
<option id="true" <?php  if( $astejuscraouseldata['vertical']=="false"){echo "selected";} ?> >false</option>
<option id="false" <?php  if( $astejuscraouseldata['vertical']=="true"){echo "selected";} ?> >true</option>
</select></td>
</tr>

<tr>
<td>No. of Visible Images</td>
<td><input type="text" name="visible" size="10" value="<?php echo $astejuscraouseldata['visible'];?>" ></td>
</tr>

<!--<tr>
<td>Start index</td>
<td><input type="text" id="" name="start" size="10" value="<?php echo $astejuscraouseldata['start'];?>" ></td>
</tr>-->

<tr>
<td>Effect</td><td>
<select name="easing" >
<option  id="fade" <?php  if( $astejuscraouseldata['easing']==""){echo "selected";} ?> value="">fade</option>
<option  id="BounceEaseOut" <?php  if( $astejuscraouseldata['easing']=="BounceEaseOut"){echo "selected";} ?> value="BounceEaseOut">BounceEaseOut</option>
</select></td>
</tr>

<tr>
<td>Skin</td>
<td><select name="skin" >
<option  id="tango" <?php  if( $astejuscraouseldata['skin']=="tango"){echo "selected";} ?>>tango</option>
<option  id="ie7" <?php  if( $astejuscraouseldata['skin']=="ie7"){echo "selected";} ?>>ie7</option>
</select></td>
</tr>

<tr>
<td>Container Width</td>
<td><input type="text" name="cntrwidth" size="10" value="<?php echo $astejuscraouseldata['cntrwidth'];?>">px</td>
</tr>

<tr>
<td>Container Height</td><td><input type="text" name="cntrheight" size="10" value="<?php echo $astejuscraouseldata['cntrheight'];?>">px</td>
</tr>

<tr>
<td>Image Width</td><td><input type="text" name="imgwidth" size="10" value="<?php echo $astejuscraouseldata['imgwidth'];?>">px</td>
</tr>

<tr>
<td>Image Height</td><td><input type="text" name="imgheight" size="10" value="<?php echo $astejuscraouseldata['imgheight'];?>">px</td>
</tr>


<tr><td colspan="2"><input type="submit" name="submit" value="Update Carousel"/></td></tr>
</table>
</div>
</form>
<?php } ?>


<?php }?>