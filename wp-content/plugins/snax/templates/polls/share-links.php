<?php
global $snax_question_id;

$snax_poll_id = wp_get_post_parent_id( $snax_question_id );
?>

<style>
    .snax-poll-share-links-active { display: block; }
    .snax-poll-share-links-inactive { display: none; }
</style>

<div class="snax-poll-share-links snax-poll-share-links-inactive">
    <?php echo snax_get_poll_share_links( $snax_poll_id, $snax_question_id ); ?>
</div>
