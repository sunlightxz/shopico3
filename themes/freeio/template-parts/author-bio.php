<?php  
$description = get_the_author_meta( 'description' );
?>
<?php if(!empty($description)){ ?>
<div class="author-info">
	<div class="about-container d-flex">
		<div class="avatar-img flex-shrink-0">
			<?php echo freeio_get_avatar( get_the_author_meta( 'ID' ),70 ); ?>
		</div>
		<!-- .author-avatar -->
		<div class="description flex-grow-1">
			<h4 class="author-title">
				<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
					<?php echo get_the_author(); ?>
				</a>
			</h4>
			<?php the_author_meta( 'description' ); ?>
		</div>
	</div>
</div>
<?php } ?>