<?php global $wpalchemy_media_access; ?>

<div class="box">
	<div class="pixelstore_meta_control">
								
		<!-- Dropdown Slider Type -->
		<table id="slidertype" width="100%">
			<tr>
				<td class="left">Type</td>
				<td>
  					<?php $selected = ' selected="selected"'; ?>
					<?php $metabox->the_field('slider'); ?>
					<select id="slidertypedrop" name="<?php $metabox->the_name(); ?>">
						<option value="YouTube"<?php if ($metabox->get_the_value() == 'YouTube') echo $selected; ?>>YouTube</option>
						<option value="NivoSlider"<?php if ($metabox->get_the_value() == 'NivoSlider') echo $selected; ?>>NivoSlider</option>
					</select>
				</td>
			</tr>
		</table>
		<div id="holder">
	
			<!-- YouTube -->
			<div id="youtubeholders">	
	
			
				<table id="sliderdim" width="100%">
					<tr>
						<td class="left">Youtube ID</td>
						<td><?php $mb->the_field('youtube_id'); ?><input type="text" name="<?php $mb->the_name(); ?>" placeholder="Embed ID" value="<?php $mb->the_value(); ?>"/></td>
					</tr>
					
				</table>		
							<table id="youtube-effects" width="100%">
			
			<tr class="on_off">
				<td>
					<label class="left" for="on_off">Autoplay</label>
					<?php $mb->the_field('youtube_autoplay'); ?><input type="checkbox" name="<?php $mb->the_name(); ?>" class="switch"  value="true"<?php $mb->the_checkbox_state('true'); ?>/>
				</td>
				<td>
					<label class="left" for="on_off">Controls</label>
					<?php $mb->the_field('youtube_controls'); ?><input type="checkbox" name="<?php $mb->the_name(); ?>" class="switch"  value="true"<?php $mb->the_checkbox_state('true'); ?>/>
				</td>
				<td>
					<label class="left" for="on_off">Video Title</label>
					<?php $mb->the_field('youtube_showinfo'); ?><input type="checkbox" name="<?php $mb->the_name(); ?>" class="switch"  value="true"<?php $mb->the_checkbox_state('true'); ?>/>
				</td>
				<td>
					<label class="left" for="on_off">Autohide</label>
					<?php $mb->the_field('youtube_autohide'); ?><input type="checkbox" name="<?php $mb->the_name(); ?>" class="switch"  value="true"<?php $mb->the_checkbox_state('true'); ?>/>
				</td>
			</tr>
				</table>
			</div>
			
			<!-- NivoSlider -->		
			<div id="nivoholders">			
			
				<!-- Slider Transisitions -->
				<table id="transitions" width="100%">
					<tr>
						<td class="left">Transition</td>
						<td>
							<?php $selected = ' selected="selected"'; ?>
							<?php $metabox->the_field('effect'); ?>
							<select id="select" name="<?php $metabox->the_name(); ?>">
								<option value="sliceDown"<?php if ($metabox->get_the_value() == 'sliceDown') echo $selected; ?>>sliceDown</option>
								<option value="sliceDownLeft"<?php if ($metabox->get_the_value() == 'sliceDownLeft') echo $selected; ?>>sliceDownLeft</option>
								<option value="sliceUp"<?php if ($metabox->get_the_value() == 'sliceUp') echo $selected; ?>>sliceUp</option>
								<option value="sliceUpLeft"<?php if ($metabox->get_the_value() == 'sliceUpLeft') echo $selected; ?>>sliceUpLeft</option>
								<option value="sliceUpDown"<?php if ($metabox->get_the_value() == 'sliceUpDown') echo $selected; ?>>sliceUpDown</option>
								<option value="sliceUpDownLeft"<?php if ($metabox->get_the_value() == 'sliceUpDownLeft') echo $selected; ?>>sliceUpDownLeft</option>
								<option value="fold"<?php if ($metabox->get_the_value() == 'fold') echo $selected; ?>>fold</option>
								<option value="fade"<?php if ($metabox->get_the_value() == 'fade') echo $selected; ?>>fade</option>
								<option value="random"<?php if ($metabox->get_the_value() == 'random') echo $selected; ?>>random</option>
								<option value="slideInRight"<?php if ($metabox->get_the_value() == 'slideInRight') echo $selected; ?>>slideInRight</option>
								<option value="slideInLeft"<?php if ($metabox->get_the_value() == 'slideInLeft') echo $selected; ?>>slideInLeft</option>
								<option value="boxRandom"<?php if ($metabox->get_the_value() == 'boxRandom') echo $selected; ?>>boxRandom</option>
								<option value="boxRain"<?php if ($metabox->get_the_value() == 'boxRain') echo $selected; ?>>boxRain</option>
								<option value="boxRainReverse"<?php if ($metabox->get_the_value() == 'boxRainReverse') echo $selected; ?>>boxRainReverse</option>
								<option value="boxRainGrow"<?php if ($metabox->get_the_value() == 'boxRainGrow') echo $selected; ?>>boxRainGrow</option>
								<option value="boxRainGrowReverse"<?php if ($metabox->get_the_value() == 'boxRainGrowReverse') echo $selected; ?>>boxRainGrowReverse</option>
							</select>
						</td>
					</tr>
				</table>		
			
				<!-- Slider Checkbox Effects -->
				<table id="effects" width="100%">	
					<tr class="on_off">
					<td>
						<label class="left" for="on_off">Hover Pause</label>
						<?php $mb->the_field('pauseOnHover'); ?><input type="checkbox" name="<?php $mb->the_name(); ?>" class="switch"  value="true"<?php $mb->the_checkbox_state('true'); ?>/>
					</td>
					<td>
						<label class="left" for="on_off">Control Nav</label>
						<?php $mb->the_field('controlNav'); ?><input type="checkbox" name="<?php $mb->the_name(); ?>" class="switch"  value="true"<?php $mb->the_checkbox_state('true'); ?>/>
					</td>
					<td>
						<label class="left" for="on_off">Direction Nav</label>
						<?php $mb->the_field('directionNav'); ?><input type="checkbox" name="<?php $mb->the_name(); ?>" class="switch"  value="true"<?php $mb->the_checkbox_state('true'); ?>/>
					</td>	
					<td>
				
						<label class="left" for="on_off">Random Start</label>
						<?php $mb->the_field('randomStart'); ?><input type="checkbox" name="<?php $mb->the_name(); ?>" class="switch"  value="true"<?php $mb->the_checkbox_state('true'); ?>/>
					</td>
					<td>
						<label class="left" for="on_off">Manual Advance</label>
						<?php $mb->the_field('manualAdvance'); ?><input type="checkbox" name="<?php $mb->the_name(); ?>" class="switch"  value="true"<?php $mb->the_checkbox_state('true'); ?>/>
					</td>	
				</tr>
				</table>
								
				<!-- Slider Settings jSlide -->
				<table id="startSlidetbl" width="100%">		
				<tr>
					<td class="left"><?php $mb->the_field('startSlide'); if(is_null($mb->get_the_value())) $mb->meta[$mb->name] = '0'; ?>Start Slide</td>
					<td><input id="startSlide" name="<?php $metabox->the_name(); ?>" value="<?php $metabox->the_value(); ?>"/></td>
				</tr>
					</table>			
				<table id="pauseTimetbl" width="100%">		
				<tr>
					<td class="left"><?php $mb->the_field('pauseTime'); if(is_null($mb->get_the_value())) $mb->meta[$mb->name] = '3000'; ?>Pause Time</td>
					<td><input id="pauseTime" type="slider" name="<?php $metabox->the_name(); ?>" value="<?php $metabox->the_value(); ?>"/></td>
				</tr>
				</table>			
				<table id="slicestbl" width="100%">	
				<tr>
					<td class="left"><?php $mb->the_field('slices'); if(is_null($mb->get_the_value())) $mb->meta[$mb->name] = '15'; ?>Slices</td>
					<td><input id="slices" type="slider" name="<?php $metabox->the_name(); ?>" value="<?php $metabox->the_value(); ?>"/></td>
				</tr>
				</table>	
				<table id="boxRowstbl" width="100%">	
				<tr>
					<td class="left"><?php $mb->the_field('boxRows'); if(is_null($mb->get_the_value())) $mb->meta[$mb->name] = '4'; ?>Box Rows</td>
					<td><input id="boxRows" type="slider" name="<?php $metabox->the_name(); ?>" value="<?php $metabox->the_value(); ?>"/></td>
				</tr>
			</table>
			
			</div>
			
		</div>
	</div>
</div>


<script type="text/javascript" charset="utf-8">
	jQuery('.switch').slickswitch();
	jQuery("#slices").slider({limits: false, from: 1, to: 50, step: 5, round: 1, skin: "round" });
	jQuery("#boxCols").slider({limits: false, from: 1, to: 20, step: 2, round: 1, skin: "round" });
	jQuery("#boxRows").slider({limits: false, from: 1, to: 20, step: 2, round: 1, skin: "round" });
	jQuery("#pauseTime").slider({limits: false, format: { format: '####' }, from: 1000, to: 5000, step: 100, skin: "round" });
	jQuery("#startSlide").slider({limits: false, from: 0, to: 10, step: 1, round: 1, skin: "round" });
	
// 	Display correct slider attributes on load
	jQuery(function($) {
	var x= $('#slidertypedrop').val();
	
	 if(x == 'YouTube') {
			$("#nivoholders").hide();  
			$("#basicholders").hide();    
	}
	else {
			$("#youtubeholders").hide();  
			$("#basicholders").hide(); 
	}
	
	});

// 	NivoSlider attribute transitions
	jQuery(function($){
		$('#select').bind('change', nivoAtts);	
		nivoAtts();
		function nivoAtts() {
		var value= $('#select').val();
	
		if(value == 'boxRandom' || value == 'boxRain' || value == 'boxRainReverse' || value == 'boxRainGrow' || value == 'boxRainGrowReverse' || value == 'random') {
			$('#boxColstbl td, #boxRowstbl td').fadeTo("slow", 1.0);
		} else {
			$('#boxColstbl td, #boxRowstbl td').fadeTo("slow", 0.6);
			
		}
		
		if(value == 'sliceDown' || value == 'sliceDownLeft' || value == 'sliceUp' || value == 'sliceUpLeft' || value == 'sliceUpDown'  || value == 'sliceUpDownLeft' || value == 'random') {
			$('#slicestbl td').fadeTo("slow", 1.0);
		}    
		else 
			 {
			  $('#slicestbl td').fadeTo("slow", 0.6);
			  }
	};
	
	});

// 	Slider type tables
	jQuery(function($){
		$('#slidertypedrop').bind('change', sliderType);	
		function sliderType() {
			var x= $('#slidertypedrop').val();
			
			$('#holder').fadeOut('slow', function() {
				if(x == 'YouTube') {
					$('#holder').show();
					$("#nivoholders, #basicholders").hide();  
					$('#youtubeholders').fadeIn();
				}    
				else if(x == 'NivoSlider') {
					$('#holder').show();
					$("#youtubeholders, #basicholders").hide();  
					$('#nivoholders').fadeIn();
				}
				else {
					$('#holder').show();
					$("#nivoholders, #youtubeholders").hide();  
					$('#basicholders').fadeIn();
				}
			});
		};
	});

</script>
<script type="text/javascript" charset="utf-8">
	document.observe("dom:loaded", function() {
		new iPhoneStyle('.on_off input[type=checkbox]', { checkedLabel: 'ON', uncheckedLabel: 'OFF' });
	});
</script>
