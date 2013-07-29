<?php
require_once('../../../wp-blog-header.php');
$actiontotake = $_POST['action'];

if($actiontotake=="update"){
$id = $_POST['id'];
$url =$_POST['url'];
$crslorder =$_POST['crslorder'];
global $wpdb;
$as_tejus_crsl_table_name_sec = $wpdb->prefix."as_tejus_crsl_sec";
$sql = "update ".$as_tejus_crsl_table_name_sec. " set url = '".$url."', crslorder = '".$crslorder."' where id = ".$id;
$wpdb->query($sql);

}
if($actiontotake=="delete"){
$id= $_POST['id'];
global $wpdb;
$as_tejus_crsl_table_name_sec = $wpdb->prefix."as_tejus_crsl_sec";
$query = "DELETE FROM ".$as_tejus_crsl_table_name_sec." WHERE id = ".$id;
$wpdb->query($query);
}

?>