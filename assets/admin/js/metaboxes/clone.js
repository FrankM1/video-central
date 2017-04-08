/* global jQuery */
jQuery( function ( $ ) {
	'use strict';

	// Object holds all methods related to fields' index when clone
	var cloneIndex = {
		/**
		 * Set index for fields in a .video-central-metaboxes-clone
		 * @param $clone .video-central-metaboxes-clone element
		 * @param index Index value
		 */
		set: function ( $clone, index ) {
			$clone.find( ':input[class|="rwmb"]' ).each( function () {
				var $field = $( this );

				// Name attribute
				var name = $field.attr( 'name' );
				if ( name && ! $field.closest( '.video-central-metaboxes-group-clone' ).length ) {
					$field.attr( 'name', cloneIndex.replace( index, name, '[', ']', false ) );
				}

				// ID attribute
				var id = this.id;
				if ( id ) {
					$field.attr( 'id', cloneIndex.replace( index, id, '_' ) );
				}
			} );

			// Address button's value attribute
			var $address = $clone.find( '.video-central-metaboxes-map-goto-address-button' );
			if ( $address.length ) {
				var value = $address.attr( 'value' );
				$address.attr( 'value', cloneIndex.replace( index, value, '_' ) );
			}
		},

		/**
		 * Replace an attribute of a field with updated index
		 * @param index New index value
		 * @param value Attribute value
		 * @param before String before returned value
		 * @param after String after returned value
		 * @param alternative Check if attribute does not contain any integer, will reset the attribute?
		 * @return string
		 */
		replace: function ( index, value, before, after, alternative ) {
			before = before || '';
			after = after || '';
			alternative = alternative || true;

			var regex = new RegExp( cloneIndex.escapeRegex( before ) + '(\\d+)' + cloneIndex.escapeRegex( after ) ),
				newValue = before + index + after;

			return regex.test( value ) ? value.replace( regex, newValue ) : (alternative ? value + newValue : value );
		},

		/**
		 * Helper function to escape string in regular expression
		 * @link https://developer.mozilla.org/en-US/docs/Web/JavaScript/Guide/Regular_Expressions
		 * @param string
		 * @return string
		 */
		escapeRegex: function ( string ) {
			return string.replace( /[.*+?^${}()|[\]\\]/g, "\\$&" );
		},

		/**
		 * Helper function to create next index for clones
		 * @param $container .video-central-metaboxes-input container
		 * @return integer
		 */
		nextIndex: function ( $container ) {
			var nextIndex = $container.data( 'next-index' );
			$container.data( 'next-index', nextIndex + 1 );
			return nextIndex;
		}
	};

	/**
	 * Clone fields
	 * @param $container A div container which has all fields
	 * @return void
	 */
	function clone( $container ) {
		var $last = $container.children( '.video-central-metaboxes-clone:last' ),
			$clone = $last.clone(),
			$input = $clone.find( ':input[class|="rwmb"]' ),
			nextIndex = cloneIndex.nextIndex( $container );

		// Reset value for fields
		$input.each( function () {
			var $field = $( this );
			if ( $field.is( ':radio' ) || $field.is( ':checkbox' ) ) {
				// Reset 'checked' attribute
				$field.prop( 'checked', false );
			} else if ( $field.is( 'select' ) ) {
				// Reset select
				$field.prop( 'selectedIndex', - 1 )
			} else if ( ! $field.hasClass( 'video-central-metaboxes-hidden' ) ) {
				// Reset value
				$field.val( '' );
			}
		} );

		// Insert Clone
		$clone.insertAfter( $last );

		// Trigger custom event for the clone instance. Required for Group extension to update sub fields.
		$clone.trigger( 'clone_instance', nextIndex );

		// Set fields index. Must run before trigger clone event.
		cloneIndex.set( $clone, nextIndex );

		// Trigger custom clone event
		$input.trigger( 'clone', nextIndex );
	}

	/**
	 * Hide remove buttons when there's only 1 of them
	 *
	 * @param $container .video-central-metaboxes-input container
	 *
	 * @return void
	 */
	function toggleRemoveButtons( $container ) {
		var $clones = $container.children( '.video-central-metaboxes-clone' );
		$clones.children( '.remove-clone' ).toggle( $clones.length > 1 );

		// Recursive for nested groups.
		$container.find( '.video-central-metaboxes-input' ).each( function () {
			toggleRemoveButtons( $( this ) );
		} );
	}

	/**
	 * Toggle add button
	 * Used with [data-max-clone] attribute. When max clone is reached, the add button is hid and vice versa
	 *
	 * @param $container .video-central-metaboxes-input container
	 *
	 * @return void
	 */
	function toggleAddButton( $container ) {
		var $button = $container.find( '.add-clone' ),
			maxClone = parseInt( $container.data( 'max-clone' ) ),
			numClone = $container.find( '.video-central-metaboxes-clone' ).length;

		$button.toggle( isNaN( maxClone ) || ( maxClone && numClone < maxClone ) );
	}

	$( '#wpbody-content' )
		// Add clones
		.on( 'click', '.add-clone', function ( e ) {
			e.preventDefault();

			var $container = $( this ).closest( '.video-central-metaboxes-input' );
			clone( $container );

			toggleRemoveButtons( $container );
			toggleAddButton( $container );
		} )
		// Remove clones
		.on( 'click', '.remove-clone', function ( e ) {
			e.preventDefault();

			var $this = $( this ),
				$container = $this.closest( '.video-central-metaboxes-input' );

			// Remove clone only if there are 2 or more of them
			if ( $container.children( '.video-central-metaboxes-clone' ).length < 2 ) {
				return;
			}

			$this.parent().trigger( 'remove' ).remove();
			toggleRemoveButtons( $container );
			toggleAddButton( $container )
		} );

	$( '.video-central-metaboxes-input' ).each( function () {
		var $container = $( this );
		toggleRemoveButtons( $container );
		toggleAddButton( $container );

		$container
			.data( 'next-index', $container.children( '.video-central-metaboxes-clone' ).length )
			.sortable( {
				handle: '.video-central-metaboxes-clone-icon',
				placeholder: ' video-central-metaboxes-clone video-central-metaboxes-clone-placeholder',
				items: '.video-central-metaboxes-clone',
				start: function ( event, ui ) {
					// Make the placeholder has the same height as dragged item
					ui.placeholder.height( ui.item.height() );
				}
			} );
	} );
} );
