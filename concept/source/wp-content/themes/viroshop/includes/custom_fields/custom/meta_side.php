<?php global $wpalchemy_media_access; ?>
<div class="box">
	<div class="pixelstore_meta_control_side">
		<div class="my_meta_control metabox">  
			<?php $mb->the_field('bgimgurl'); ?>
			<?php $wpalchemy_media_access->setGroupName('nn')->setInsertButtonLabel('Insert'); ?> 
			<p>
				<?php echo $wpalchemy_media_access->getField(array('name' => $mb->get_the_name(), 'placeholder' => 'http://','value' => $mb->get_the_value())); ?>
				<?php echo $wpalchemy_media_access->getButton(); ?>
			</p>
		</div>
	</div>
</div>

