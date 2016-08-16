var initialSelect;
var mtype = '';
var url = '';
var src = '';

CKEDITOR.plugins.add( 'embed_video',
{
	init: function( editor )
	{
		var iconPath = this.path + 'images/icon.png';
		var fileIcon = this.path + "images/file.png";
		var folderIcon = this.path + "images/folder.png";
		var rootIcon = this.path + "images/root.png";
		var jsPath = this.path + "video/javascript";
		var basePath = this.path;
 
		editor.addCommand( 'videoDialog', new CKEDITOR.dialogCommand( 'videoDialog' ) );
 
		editor.ui.addButton( 'Video',
		{
			label: 'Insert Video',
			command: 'videoDialog',
			icon: iconPath
		} );
 
		if ( editor.contextMenu )
		{
			editor.addMenuGroup( 'videoGroup' );
			editor.addMenuItem( 'videoItem',
			{
				label : 'Edit Video',
				icon : iconPath,
				command : 'videoDialog',
				group : 'videoGroup'
			});
			editor.contextMenu.addListener( function( element )
			{
				if ( element )
					element = element.getAscendant( 'img', true );
				if ( element && !element.isReadOnly() && !element.data( 'cke-realelement' ) )
 					return { videoItem : CKEDITOR.TRISTATE_ON };
				return null;
			});
		}

		editor.element.getDocument().appendStyleSheet(this.path + 'template.css');

		/*var e=document.createElement('script');
		e.type='text/javascript';
		e.src=this.path + 'video/js/jquery-1.6.1.min.js';
		document.getElementsByTagName('head')[0].appendChild(e);

		e=document.createElement('script');
		e.type='text/javascript';
		e.src=this.path + 'video/js/jquery.jplayer.min.js';
		document.getElementsByTagName('head')[0].appendChild(e);

		e=document.createElement('script');
		e.type='text/javascript';
		e.src=this.path + 'video/_lib/jquery.cookie.js';
		document.getElementsByTagName('head')[0].appendChild(e);

		e=document.createElement('script');
		e.type='text/javascript';
		e.src=this.path + 'video/_lib/jquery.hotkeys.js';
		document.getElementsByTagName('head')[0].appendChild(e);

		e=document.createElement('script');
		e.type='text/javascript';
		e.src=this.path + 'video/jquery.jstree.js';
		document.getElementsByTagName('head')[0].appendChild(e);*/
 
		CKEDITOR.dialog.add( 'videoDialog', function( editor )
		{
			return {
				title : 'Insert/Edit Video',
				minWidth : 750,
				minHeight : 550,
				contents :
				[
					{
						id : 'video',
						label : 'Insert/Edit Video',
						elements :
						[
							{
								type : 'hbox',
								widths : [ '250px', '500px' ],
								align : 'right',
								children :
								[
									{
										type : 'vbox',
										heights : [ '20px', '450px' ],
										children :
										[
											{
												type : 'html',
												html : '<div>Select a video from server</div>'
											},
											{
												type : 'html',
												html : '<div id="serverTree" class="serverTree" style="height:450px;overflow-y:auto;width:250px;"></div>'
											}
										]
									},
									{
										type : 'vbox',
										heights : ['80px', '450px'],
										children :
										[
											{
												type : 'vbox',
												heights : ['10px', '70px'],
												children :
												[
													{
														type : 'html',
														html : '<div>Upload a new video</div>'
													},
													{
														type : 'html',
														html : '<form id="fileupload" action="_DAV/PUT" method="POST" enctype="multipart/form-data">' +
																'	<div class="row fileupload-buttonbar">' +
																'		<div class="span7">' +
																'			<span class="btn btn-success fileinput-button">' +
																'				<span><i class="icon-plus icon-white"></i> Add files...</span>' +
																'				<input type="file" name="files[]" id="uploadfile">  ' +
																'			</span>' +
																'			<button class="btn btn-primary start">' +
																'				<i class="icon-upload icon-white"></i> Start upload' +
																'			</button>' +
																'		</div>' +
																'		<div class="span5">' +
																'			<div class="progress progress-success progress-striped active fade">' +
																'				<div class="bar" style="width:0%;"></div>' +
																'			</div>' +
																'		</div>' +
																'	</div>' +
																'	<div class="fileupload-loading"></div>' +
																'	<br>' +
																'	<table class="table table-striped" style="display:none"><tbody class="files" data-toggle="modal-gallery" data-target="#modal-gallery"></tbody></table>' +
																'</form>' +
																'<script id="template-upload" type="text/x-tmpl">' +
																'	{% for (var i=0, files=o.files, l=files.length, file=files[0]; i<l; file=files[++i]) { %}' +
																'		<tr class="template-upload fade">' +
																'			<td class="preview"><span class="fade"></span></td>' +
																'			<td class="name">{%=file.name%}</td>' +
																'			<td class="size">{%=o.formatFileSize(file.size)%}</td>' +
																'			{% if (file.error) { %}' +
																'				<td class="error" colspan="2"><span class="label label-important">{%=locale.fileupload.error%}</span> {%=locale.fileupload.errors[file.error] || file.error%}</td>' +
																'			{% } else if (o.files.valid && !i) { %}' +
																'				<td>' +
																'					<div class="progress progress-success progress-striped active"><div class="bar" style="width:0%;"></div></div>' +
																'				</td>' +
																'				<td class="start">{% if (!o.options.autoUpload) { %}' +
																'					<button class="btn btn-primary">' +
																'						<i class="icon-upload icon-white"></i> {%=locale.fileupload.start%}' +
																'					</button>' +
																'				{% } %}</td>' +
																'			{% } else { %}' +
																'				<td colspan="2"></td>' +
																'			{% } %}' +
																'			<td class="cancel">{% if (!i) { %}' +
																'				<button class="btn btn-warning">' +
																'					<i class="icon-ban-circle icon-white"></i> {%=locale.fileupload.cancel%}' +
																'				</button>' +
																'			{% } %}</td>' +
																'		</tr>' +
																'	{% } %}' +
																'	</script> ' +     
																'	<script id="template-download" type="text/x-tmpl">' +
																'	{% for (var i=0, files=o.files, l=files.length, file=files[0]; i<l; file=files[++i]) { %}' +
																'		<tr class="template-download fade">' +
																'			{% if (file.error) { %}' +
																'				<td></td>' +
																'				<td class="name">{%=file.name%}</td>' +
																'				<td class="size">{%=o.formatFileSize(file.size)%}</td>' +
																'				<td class="error" colspan="2"><span class="label label-important">{%=locale.fileupload.error%}</span> {%=locale.fileupload.errors[file.error] || file.error%}</td>' +
																'			{% } else { %}' +
																'				<td class="preview">{% if (file.thumbnail_url) { %}' +
																'					<a href="{%=file.url%}" title="{%=file.name%}" rel="gallery" download="{%=file.name%}"><img src="{%=file.thumbnail_url%}"></a>' +
																'				{% } %}</td>' +
																'				<td class="name">' +
																'					<a href="{%=file.url%}" title="{%=file.name%}" rel="{%=file.thumbnail_url&&\'gallery\'%}" download="{%=file.name%}">{%=file.name%}</a>' +
																'				</td>' +
																'				<td class="size">{%=o.formatFileSize(file.size)%}</td>' +
																'				<td colspan="2"></td>' +
																'			{% } %}' +
																'			<td class="delete">' +
																'				<button class="btn btn-danger" data-type="{%=file.delete_type%}" data-url="{%=file.delete_url%}">' +
																'					<i class="icon-trash icon-white"></i> {%=locale.fileupload.destroy%}' +
																'				</button>' +
																'				<input type="checkbox" name="delete" value="1">' +
																'			</td>' +
																'		</tr>' +
																'	{% } %}' +
																'</script>'
													}
												]
											},
											{
												type : 'html',
												html :	'<div>Preview</div>' +
														'<div id="jp_container_1" class="jp-video jp-video-360p" style="width:500px">' +
														'	<div class="jp-type-single">' +
														'		<div id="jquery_jplayer_1" class="jp-jplayer"></div>' +
														'		<div class="jp-gui">' +
														'			<div class="jp-video-play">' +
														'				<a href="javascript:;" class="jp-video-play-icon" tabindex="1">play</a>' +
														'			</div>' +
														'			<div class="jp-interface">' +
														'				<div class="jp-progress">' +
														'					<div class="jp-seek-bar">' +
														'						<div class="jp-play-bar"></div>' +
														'					</div>' +
														'				</div>' +
														'				<div class="jp-current-time"></div>' +
														'				<div class="jp-duration"></div>' +
														'				<div class="jp-controls-holder">' +
														'					<ul class="jp-controls">' +
														'						<li><a href="javascript:;" class="jp-play" tabindex="1">play</a></li>' +
														'						<li><a href="javascript:;" class="jp-pause" tabindex="1">pause</a></li>' +
														'						<li><a href="javascript:;" class="jp-stop" tabindex="1">stop</a></li>' +
														'						<li><a href="javascript:;" class="jp-mute" tabindex="1" title="mute">mute</a></li>' +
														'						<li><a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute">unmute</a></li>' +
														'						<li><a href="javascript:;" class="jp-volume-max" tabindex="1" title="max volume">max volume</a></li>' +
														'					</ul>' +
														'					<div class="jp-volume-bar">' +
														'						<div class="jp-volume-bar-value"></div>' +
														'					</div>' +
														'					<ul class="jp-toggles">' +
														'						<li><a href="javascript:;" class="jp-full-screen" tabindex="1" title="full screen">full screen</a></li>' +
														'						<li><a href="javascript:;" class="jp-restore-screen" tabindex="1" title="restore screen">restore screen</a></li>' +
														'						<li><a href="javascript:;" class="jp-repeat" tabindex="1" title="repeat">repeat</a></li>' +
														'						<li><a href="javascript:;" class="jp-repeat-off" tabindex="1" title="repeat off">repeat off</a></li>' +
														'					</ul>' +
														'				</div>' +
														'				<div class="jp-title">' +
														'				</div>' +
														'			</div>' +
														'		</div>' +
														'		<div class="jp-no-solution">' +
														'			<span>Update Required</span>' +
														'			To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.' +
														'		</div>' +
														'	</div>' +
														'</div>'
											},
										]
									}
								]
							},
						]
					}
				],
				onShow : function()
				{
					var sel = editor.getSelection(),
						element = sel.getStartElement();
					if ( element )
						element = element.getAscendant( 'img', true );
 
					if ( !element || element.getName() != 'img' || element.data( 'cke-realelement' ) )
					{
						element = editor.document.createElement( 'img' );
						this.insertMode = true;
					}
					else {
						this.insertMode = false;
						initialSelect = element.getAttribute("id");
						url = element.getAttribute("url");
						mtype = element.getAttribute("mtype");
						src = element.getAttribute("src");
					}
 
					this.element = element;
 
					this.setupContent( this.element );
					$("#jquery_jplayer_1").jPlayer({
						ready: function () {
							$(this).jPlayer("setMedia", {
								m4v: "",
								webmv: "",
								poster: ""
							});
						},
						swfPath: jsPath,
						supplied: "webmv, m4v, ogv",
						solution:"flash,html",
						size: {
							width: "500px",
							height: "360px",
							cssClass: "jp-video-360p"
						}
					});
					$("#jquery_jplayer_1").jPlayer("play", 0);
					
					initTree();

					var e=document.createElement('script');
					e.type='text/javascript';
					e.src=basePath + 'video/upload/js/vendor/jquery.ui.widget.js';
					document.getElementsByTagName('head')[0].appendChild(e);

					e=document.createElement('script');
					e.type='text/javascript';
					e.src='http://blueimp.github.com/JavaScript-Templates/tmpl.min.js';
					document.getElementsByTagName('head')[0].appendChild(e);

					e=document.createElement('script');
					e.type='text/javascript';
					e.src='http://blueimp.github.com/JavaScript-Load-Image/load-image.min.js';
					document.getElementsByTagName('head')[0].appendChild(e);

					e=document.createElement('script');
					e.type='text/javascript';
					e.src='http://blueimp.github.com/JavaScript-Canvas-to-Blob/canvas-to-blob.min.js';
					document.getElementsByTagName('head')[0].appendChild(e);

					e=document.createElement('script');
					e.type='text/javascript';
					e.src='http://blueimp.github.com/cdn/js/bootstrap.min.js';
					document.getElementsByTagName('head')[0].appendChild(e);

					e=document.createElement('script');
					e.type='text/javascript';
					e.src='http://blueimp.github.com/Bootstrap-Image-Gallery/js/bootstrap-image-gallery.min.js';
					document.getElementsByTagName('head')[0].appendChild(e);

					e=document.createElement('script');
					e.type='text/javascript';
					e.src=basePath + 'video/upload/js/jquery.iframe-transport.js';
					document.getElementsByTagName('head')[0].appendChild(e);

					e=document.createElement('script');
					e.type='text/javascript';
					e.src=basePath + 'video/upload/js/jquery.fileupload.js';
					document.getElementsByTagName('head')[0].appendChild(e);

					e=document.createElement('script');
					e.type='text/javascript';
					e.src=basePath + 'video/upload/js/jquery.fileupload-ip.js';
					document.getElementsByTagName('head')[0].appendChild(e);

					e=document.createElement('script');
					e.type='text/javascript';
					e.src=basePath + 'video/upload/js/jquery.fileupload-ui.js';
					document.getElementsByTagName('head')[0].appendChild(e);

					e=document.createElement('script');
					e.type='text/javascript';
					e.src=basePath + 'video/upload/js/locale.js';
					document.getElementsByTagName('head')[0].appendChild(e);

					e=document.createElement('script');
					e.type='text/javascript';
					e.src=basePath + 'video/upload/js/main.js';
					document.getElementsByTagName('head')[0].appendChild(e);
				},
				onOk : function()
				{
					var dialog = this,
						img = this.element;
 
					img.setAttribute( "id", initialSelect );
					img.setAttribute( "mtype", mtype );
					img.setAttribute( "url", url );
					img.setAttribute( "src", src );
					if ( this.insertMode ) {
						editor.insertElement( img );
					}
					this.commitContent( img );
				}
			};
		} );
	}
} );

function initTree(url){
	$.ajax( {url:"http://dev.video.ettrema.com/skins/_DAV/PROPFIND?fields=name,href,iscollection&depth=10", dataType:"JSON", success: processData });
	//http://localhost/jsTree/_demo/server.php?operation=get_children
	//http://dev.video.ettrema.com/skins/_DAV/PROPFIND?fields=name,href,iscollection&depth=10
}

function processData(e, data){ 
	var content = "<ul>";
	var deep = 0;
	var before = new Array(10);
	var mtype;
	var url;

	$.each(e, function(index, test) {
		if ( index == 0 )
		{
			content += '<li id="' + test.href + '" class="jstree-open" rel="folder" mtype="" url="" src="">';
			content += '<ins class="jstree-icon">&nbsp;</ins>';
			content += '<a href="#" class=""><ins class="jstree-icon">&nbsp;</ins>' + test.name + '</a><ul>';
			before[0] = test.href;
		} else {
			if ( before[deep] != test.href.substring(0, before[deep].length) )
			{
				content += "</ul>";
				deep--;
			}
			if ( test.iscollection )
			{
				content += '<li id="' + test.href + '" class="jstree-open" rel="folder" mtype="" url="" src="">';
				content += '<ins class="jstree-icon">&nbsp;</ins>';
				content += '<a href="#" class=""><ins class="jstree-icon">&nbsp;</ins>' + test.name + '</a><ul>';
				deep++;
				before[deep] = test.href;
			} else {
				mtype = test.name.substr(test.name.lastIndexOf('.')+1, test.name.len);
				url = 'http://dev.video.ettrema.com' + test.href
				content += '<li id="' + test.href + '" class="jstree-open" rel="file" mtype="' + mtype + '" url="' + url + '" src="http://dev.video.ettrema.com/skins/videos/Big_Buck_Bunny_Trailer_480x270.png">';
				content += '<ins class="jstree-icon">&nbsp;</ins>';
				content += '<a href="#" class=""><ins class="jstree-icon">&nbsp;</ins>' + test.name + '</a>';
			}
		}
	});
	content += "</ul>";
	$('#serverTree').html(content);

	var fileIcon = "http://dev.video.ettrema.com/plugins/embed_video/images/file.png";
	var folderIcon = "http://dev.video.ettrema.com/plugins/embed_video/images/folder.png";

	$("#serverTree")
		.jstree({
			// the `plugins` array allows you to configure the active plugins on this instance
			"plugins" : ["themes","html_data","ui","crrm","hotkeys", "types", "ui"],
			// each plugin you have included can have its own config object
			"core" : { "initially_open" : [ "/skins/videos/" ] },
			"types" : {
				// I set both options to -2, as I do not need depth and children count checking
				// Those two checks may slow jstree a lot, so use only when needed
				"max_depth" : -2,
				"max_children" : -2,
				// I want only `drive` nodes to be root nodes 
				// This will prevent moving or creating any other type as a root node
				"valid_children" : [ "drive" ],
				"types" : {
					// The default type
					"file" : {
						// I want this type to have no children (so only leaf nodes)
						// In my case - those are files
						"valid_children" : "none",
						// If we specify an icon for the default type it WILL OVERRIDE the theme icons
						"icon" : {
							"image" : fileIcon
						}
					},
					// The `folder` type
					"folder" : {
						// can have files and other folders inside of it, but NOT `drive` nodes
						"valid_children" : [ "default", "folder" ],
						"icon" : {
							"image" : folderIcon
						}
					}
				}
			},
			"ui" : {
				// this makes the node with ID node_4 selected onload
				"initially_select" : [ initialSelect ]
			},
		})
		.bind("select_node.jstree", function (e, data) { 
			//if ( data.rslt.obj[0].getAttribute('rel') == 'file')
			//{
				initialSelect = data.rslt.obj[0].id;
				mtype = data.rslt.obj[0].getAttribute('mtype');
				url = data.rslt.obj[0].getAttribute('url');
				src = data.rslt.obj[0].getAttribute('src');
				document.getElementById("fileupload").action = initialSelect.substring(0, initialSelect.lastIndexOf('/')+1) + '_DAV/PUT';
				methodInvoke(mtype, url, src);
			//}
		});
}

function makeHTML(str){

}

CKEDITOR.config.toolbar_Full =
[
	{ name: 'document',		items : [ 'Source','-','Save','NewPage','DocProps','Preview','Print','-','Templates' ] },
	{ name: 'clipboard',	items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
	{ name: 'editing',		items : [ 'Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt' ] },
	{ name: 'forms',		items : [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
	'/',
	{ name: 'basicstyles',	items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
	{ name: 'paragraph',	items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl' ] },
	{ name: 'links',		items : [ 'Link','Unlink','Anchor' ] },
	{ name: 'insert',		items : [ 'Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak','Iframe' ] },
	'/',
	{ name: 'styles',		items : [ 'Styles','Format','Font','FontSize' ] },
	{ name: 'colors',		items : [ 'TextColor','BGColor' ] },
	{ name: 'tools',		items : [ 'Maximize', 'ShowBlocks','-','About' ] },
	{ name: 'video',		items : [ 'Video' ] }
];

CKEDITOR.config.toolbar = 'Full';

function methodInvoke(mtype, url, src) {
	switch ( mtype )
	{
	case 'm4v':
		$("#jquery_jplayer_1").jPlayer("setMedia", {
			m4v: url,
			poster: src
		});
		$("#jquery_jplayer_1").jPlayer({solution:"flash, html"});
		break;
	case 'ogv':
		$("#jquery_jplayer_1").jPlayer("setMedia", {
			ogv: url,
			poster: src
		});
		$("#jquery_jplayer_1").jPlayer({solution:"html, flash"});
		break;
	case 'webmv':
		$("#jquery_jplayer_1").jPlayer("setMedia", {
			webmv: url,
			poster: src
		});
		break;
	default:
		$("#jquery_jplayer_1").jPlayer("setMedia", {
			webmv: "",
			poster: src
		});
	}
	$("#jquery_jplayer_1").jPlayer("play", 0);
}