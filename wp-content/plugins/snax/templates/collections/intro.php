<?php
/**
 * Collection Intro
 *
 * @package snax 1.19
 * @subpackage Collections
 */

$slug = get_queried_object()->post_name;
?>

Common intro. Please create intro-<?php echo $slug; ?>.php in <?php echo dirname(__FILE__); ?> to override :)
