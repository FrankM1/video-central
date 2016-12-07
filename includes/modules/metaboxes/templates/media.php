<script id="tmpl-video-central-metaboxes-media-item" type="text/html">
  <input type="hidden" name="{{{ data.fieldName }}}" value="{{{ data.id }}}" class="video-central-metaboxes-media-input">
  <div class="video-central-metaboxes-media-preview">
    <div class="video-central-metaboxes-media-content">
      <div class="centered">
        <# if ( 'image' === data.type && data.sizes ) { #>
          <# if ( data.sizes.thumbnail ) { #>
            <img src="{{{ data.sizes.thumbnail.url }}}">
          <# } else { #>
            <img src="{{{ data.sizes.full.url }}}">
          <# } #>
        <# } else { #>
          <# if ( data.image && data.image.src && data.image.src !== data.icon ) { #>
            <img src="{{ data.image.src }}" />
          <# } else { #>
            <img src="{{ data.icon }}" />
          <# } #>
        <# } #>
      </div>
    </div>
  </div>
  <div class="video-central-metaboxes-media-info">
    <h4>
      <a href="{{{ data.url }}}" target="_blank" title="{{{ i18nVcmMedia.view }}}">
        <# if( data.title ) { #> {{{ data.title }}}
          <# } else { #> {{{ i18nVcmMedia.noTitle }}}
        <# } #>
      </a>
    </h4>
    <p>{{{ data.mime }}}</p>
    <p>
      <a class="video-central-metaboxes-edit-media" title="{{{ i18nVcmMedia.edit }}}" href="{{{ data.editLink }}}" target="_blank">
        <span class="dashicons dashicons-edit"></span>{{{ i18nVcmMedia.edit }}}
      </a>
      <a href="#" class="video-central-metaboxes-remove-media" title="{{{ i18nVcmMedia.remove }}}">
        <span class="dashicons dashicons-no-alt"></span>{{{ i18nVcmMedia.remove }}}
      </a>
    </p>
  </div>
</script>

<script id="tmpl-video-central-metaboxes-media-status" type="text/html">
	<# if ( data.maxFiles > 0 ) { #>
		{{{ data.length }}}/{{{ data.maxFiles }}}
		<# if ( 1 < data.maxFiles ) { #>  {{{ i18nVcmMedia.multiple }}} <# } else {#> {{{ i18nVcmMedia.single }}} <# } #>
	<# } #>
</script>
