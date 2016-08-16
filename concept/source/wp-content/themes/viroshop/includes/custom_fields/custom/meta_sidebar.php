<?php global $wpalchemy_media_access; ?>
<div class="box">
	<table id="sidebar" width="100%">
		<tr>		
			<td>
				<?php $selected = ' selected="selected"'; ?>
				<?php $metabox->the_field('sidebar'); ?>
				<select id="sideselect" name="<?php $metabox->the_name(); ?>">
					<?php
						global $wp_registered_sidebars, $post_type;
						$sidebar_replacements = $wp_registered_sidebars; //sidebar_generator::get_sidebars();
						if(is_array($sidebar_replacements) && !empty($sidebar_replacements)){
							foreach($sidebar_replacements as $sidebar){
							?>
							<option value="<?php echo $sidebar['name']; ?>"<?php if ($metabox->get_the_value() == ''. $sidebar['name'] .'') echo $selected; ?>><?php echo $sidebar['name']; ?></option>
							<?php
							}
						}
						
						?>
						<?php if (($_GET['post_type'] == 'product') || ($post_type == 'product')) : ?>
							<option value="No Sidebar"<?php if ($metabox->get_the_value() == 'No Sidebar') echo $selected; ?>>No Sidebar</option>
						<?php endif; ?>
				</select>
			</td>
		</tr>
	</table>							
</div>

