CKEDITOR.plugins.add( 'abbr',
{
	init: function( editor )
	{
		var iconPath = this.path + 'images/icon.png';
 
		editor.addCommand( 'abbrDialog',new CKEDITOR.dialogCommand( 'abbrDialog' ) );
 
		editor.ui.addButton( 'Abbr',
		{
			label: 'Insert Abbreviation',
			command: 'abbrDialog',
			icon: iconPath
		} );
 
		if ( editor.contextMenu )
		{
			editor.addMenuGroup( 'myGroup' );
			editor.addMenuItem( 'abbrItem',
			{
				label : 'Edit Abbreviation',
				icon : iconPath,
				command : 'abbrDialog',
				group : 'myGroup'
			});
			editor.contextMenu.addListener( function( element )
			{
				if ( element )
					element = element.getAscendant( 'abbr', true );
				if ( element && !element.isReadOnly() && !element.data( 'cke-realelement' ) )
 					return { abbrItem : CKEDITOR.TRISTATE_ON };
				return null;
			});
		}
 
		CKEDITOR.dialog.add( 'abbrDialog', function( editor )
		{
			return {
				title : 'Abbreviation Properties',
				minWidth : 400,
				minHeight : 200,
				contents :
				[
					{
						id : 'tab1',
						label : 'Basic Settings',
						elements :
						[
							{
								type : 'text',
								id : 'abbr',
								label : 'Abbreviation',
								validate : CKEDITOR.dialog.validate.notEmpty( "Abbreviation field cannot be empty" ),
								setup : function( element )
								{
									this.setValue( element.getText() );
								},
								commit : function( element )
								{
									element.setText( this.getValue() );
								}
							},
							{
								type : 'text',
								id : 'title',
								label : 'Explanation',
								validate : CKEDITOR.dialog.validate.notEmpty( "Explanation field cannot be empty" ),
								setup : function( element )
								{
									this.setValue( element.getAttribute( "title" ) );
								},
								commit : function( element )
								{
									element.setAttribute( "title", this.getValue() );
								}
							}	 
						]
					},
					{
						id : 'tab2',
						label : 'Advanced Settings',
						elements :
						[
							{
								type : 'text',
								id : 'id',
								label : 'Id',
								setup : function( element )
								{
									this.setValue( element.getAttribute( "id" ) );
								},
								commit : function ( element )
								{
									var id = this.getValue();
									if ( id )
										element.setAttribute( 'id', id );
									else if ( !this.insertMode )
										element.removeAttribute( 'id' );
								}
							}
						]
					}
				],
				onShow : function()
				{
					var sel = editor.getSelection(),
						element = sel.getStartElement();
					if ( element )
						element = element.getAscendant( 'abbr', true );
 
					if ( !element || element.getName() != 'abbr' || element.data( 'cke-realelement' ) )
					{
						element = editor.document.createElement( 'abbr' );
						this.insertMode = true;
					}
					else
						this.insertMode = false;
 
					this.element = element;
 
					this.setupContent( this.element );
				},
				onOk : function()
				{
					var dialog = this,
						abbr = this.element;
 
					if ( this.insertMode )
						editor.insertElement( abbr );
					this.commitContent( abbr );
				}
			};
		} );
	}
} );