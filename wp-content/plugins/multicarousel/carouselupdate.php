<?php
require_once('../../../wp-blog-header.php');
$actiontotake = $_POST['action'];

if($actiontotake=="delete"){
$id= $_POST['crsl_id'];
global $wpdb;

$as_tejus_crsl_table_name_frst = $wpdb->prefix."as_tejus_crsl";
$query = "DELETE FROM ".$as_tejus_crsl_table_name_frst." WHERE crsl_id =".$id;
$wpdb->query($query);

$as_tejus_crsl_table_name_sec = $wpdb->prefix."as_tejus_crsl_sec";
$sql= "SELECT * FROM ".$as_tejus_crsl_table_name_sec."  WHERE crsl_id ='{$id }'"; 
$allimage =  $wpdb->get_results($sql, ARRAY_A);

foreach($allimage as $image){

$query = "DELETE FROM ".$as_tejus_crsl_table_name_sec." WHERE id =".$image['id'];
$wpdb->query($query);

}
}


?>