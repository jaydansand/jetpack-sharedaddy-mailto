<?php
class JPSDM_Share_Email extends Share_Email {
  public function get_display( $post ) {
    $email_subject = esc_attr($post->post_title);
    $email_body = rawurlencode(__('I think you might be interested in this post:') . "\r\n" . $post->post_title . "\r\n" . get_permalink( $post->ID ) . "\r\n");
		return $this->get_link( 'mailto:', _x( 'Email', 'share to', 'jetpack' ), __( 'Click to email this to a friend', 'jetpack' ), 'body=' . $email_body . '&amp;subject=' . $email_subject );
	}
}
