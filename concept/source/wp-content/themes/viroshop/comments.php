<?php global $shortname; ?>

<div id="comments">
	<?php if(of_get_option('viro_social')) { ?>
	<div class="social-single">
		<div id="twitterbutton"><script src="http://platform.twitter.com/widgets.js" type="text/javascript"></script><div> <a href="http://twitter.com/share" class="twitter-share-button" data-url="<?php the_permalink() ?>" data-counturl="<?php the_permalink() ?>" data-text="<?php the_title(); ?>" data-via="<?php echo of_get_option('viro_twitter_id'); ?>" data-related="<?php echo of_get_option('viro_twitter_id'); ?>">Tweet</a></div></div>
		<div id="likebutton"><iframe src="http://www.facebook.com/plugins/like.php?href=<?php echo rawurlencode(get_permalink()); ?>&layout=button_count&show_faces=false&width=100&action=like&font=verdana&colorscheme=light&height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:100px; height:21px;" allowTransparency="true"></iframe></div>
		<div id="plusonebutton"><g:plusone size="medium"></g:plusone></div>
	</div>
	<?php } ?>
	
	<?php if ( post_password_required() ) : ?>
		<p class="nopassword"><?php _e( 'This post is password protected. Enter the password to view any comments.', 'pixelstores' ); ?></p>
	</div><!-- #comments -->
	<?php return; endif; ?>

	<?php if ( have_comments() ) : ?>		
		<h3 id="comments-title"><?php comments_number(__('no comments', 'pixelstores'), __('<span>1</span> comment', 'pixelstores'), __('<span>%</span> comments', 'pixelstores')); ?></h3>
		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
			<div class="navigation">
				<div class="nav-previous"><?php previous_comments_link( __( '<span class="meta-nav">&larr;</span> Older Comments', 'pixelstores' ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( __( 'Newer Comments <span class="meta-nav">&rarr;</span>', 'pixelstores' ) ); ?></div>
			</div> <!-- .navigation -->
		<?php endif; ?>

		<ol class="commentlist group">
			<?php wp_list_comments( array( 'avatar_size' => 60, 'type' => 'comment' ) ); ?>
		</ol>

	<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
			<div class="navigation">
				<div class="nav-previous"><?php previous_comments_link( __( '<span class="meta-nav">&larr;</span> Older Comments', 'pixelstores' ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( __( 'Newer Comments <span class="meta-nav">&rarr;</span>', 'pixelstores' ) ); ?></div>
			</div><!-- .navigation -->
<?php endif; // check for comment navigation ?>
    <?php if( pings_open( get_the_ID() ) ) : ?>
    <!-- START TRACKBACK & PINGBACK -->
	<h3>Trackbacks</h3>
	<?php $numero_trackback = 0; ?>
	<ol class="trackbacklist">
	<?php foreach ($comments as $comment) : 
       if ($comment->comment_type == "trackback" || $comment->comment_type == "pingback") {
       // Visualizzo solo i trackback e pingback
		?>
		<li id="comment-<?php comment_ID() ?>" class="group">
            <cite><?php comment_author_link() ?></cite>
			<br/>
			<?php comment_excerpt(); ?>
		</li>
		<?php 
		$numero_trackback++; 
	   } 
	endforeach; 
	?>
	</ol>
	<!-- END TRACKBACK & PINGBACK -->
	
	<?php
	if ($numero_trackback == 0) { ?>
		   <p><em><?php _e('No trackback or pingback available for this article', 'pixelstores'); ?></em></p>
		<?php }
	?>
    <?php endif; // ping_status ?>	
	

<?php else : // or, if we don't have comments:

	/* If there are no comments and comments are closed,
	 * let's leave a little note, shall we?
	 */
	if ( ! comments_open() ) :
?>
	<!--<p class="nocomments"><?php _e( '&nbsp;', 'pixelstores' ); ?></p>-->
<?php endif; // end ! comments_open() ?>

<?php endif; // end have_comments() ?>

<?php                           
	$commenter = wp_get_current_commenter();
	
	if ( is_user_logged_in() )
		$email_author = get_the_author_meta('user_email');
	else
		$email_author = $commenter['comment_author_email'];

	$req = get_option( 'require_name_email' );
	$aria_req = ( $req ? " aria-required='true'" : '' );
	$url_avatar = get_template_directory_uri() . '/images/noavatar.png';
	$fields =  array(
		'author' => '<p class="comment-form-author">' . '<label for="author">' . __( 'Name' ) . '</label> ' . 
		            '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /></p>',
		'email'  => '<p class="comment-form-email"><label for="email">' . __( 'Email' ) . '</label> ' . 
		            '<input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' /></p>',
		'url'    => '<p class="comment-form-url"><label for="url">' . __( 'Website' ) . '</label>' .
		            '<input id="url" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></p>',
	);
	
	//$required_text = sprintf( ' ' . __('Required fields are marked %s'), '<span class="required">*</span>' );
	$comment_args = array(
		'fields'               => apply_filters( 'comment_form_default_fields', $fields ),
		'comment_field'        => '<p class="comment-form-comment"><label for="comment">'.__( 'Your comment', 'pixelstores' ).'</label><textarea id="comment" name="comment" cols="45" rows="8"></textarea></p><div class="clear"></div>',
		'must_log_in'          => '<p class="must-log-in">' .  sprintf( __( 'You must be <a href="%s">logged in</a> to post a comment.' ), wp_login_url( apply_filters( 'the_permalink', get_permalink( get_the_ID() ) ) ) ) . '</p>',
		'logged_in_as'         => '<p class="logged-in-as">' . sprintf( __( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>' ), admin_url( 'profile.php' ), $user_identity, wp_logout_url( apply_filters( 'the_permalink', get_permalink( get_the_ID() ) ) ) ) . '</p>',
		'comment_notes_before' => '',
		'comment_notes_after'  => '',
		'id_form'              => 'commentform',
		'id_submit'            => 'submit',
		'title_reply'          => __( 'Leave a <span>Reply</span>', 'pixelstores' ),
		'title_reply_to'       => __( 'Leave a <span>Reply</span> to %s', 'pixelstores' ),
		'cancel_reply_link'    => __( 'Cancel reply', 'pixelstores' ),
		'label_submit'         => __( 'Post Comment', 'pixelstores' ),
	);
	
	comment_form( $comment_args ); 
?>

</div><!-- #comments -->
