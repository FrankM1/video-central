jQuery( function( $ )
{
	'use strict';
	function updateChecklist()
	{
		var $this = $( this ),
			$children = $this.closest( 'li' ).children('ul');

		if ( $this.is( ':checked' ) )
		{
			$children.removeClass( 'hidden' );
		}
		else
		{
			$children
				.addClass( 'hidden' )
				.find( 'input' )
				.removeAttr( 'checked' );
		}
	}

	$( '.video-central-metaboxes-input' )
		.on( 'change', '.video-central-metaboxes-choice-list.collapse :checkbox', updateChecklist )
		.on( 'clone', '.video-central-metaboxes-choice-list.collapse :checkbox', updateChecklist );
	$( '.video-central-metaboxes-choice-list.collapse :checkbox' ).each( updateChecklist );


	function updateSelectTree()
	{
		var $this = $( this ),
			val = $this.val(),
			$selected = $this.siblings( "[data-parent-id='" + val + "']" ),
			$notSelected = $this.parent().find( '.video-central-metaboxes-select-tree' ).not( $selected );

		$selected.removeClass( 'hidden' );
		$notSelected
			.addClass( 'hidden' )
			.find( 'select' )
			.prop( 'selectedIndex', 0 );
	}

	$( '.video-central-metaboxes-input' )
		.on( 'change', '.video-central-metaboxes-select-tree select', updateSelectTree )
		.on( 'clone', '.video-central-metaboxes-select-tree select', updateSelectTree );
} );
