<?php
/**
 * Backbone Templates
 * This file contains all of the HTML used in our modal and the workflow itself.
 *
 * Each template is wrapped in a script block ( note the type is set to "text/html" ) and given an ID prefixed with
 * 'tmpl'. The wp.template method retrieves the contents of the script block and converts these blocks into compiled
 * templates to be used and reused in the application.
 */
?>
<script type="text/html" id='tmpl-video-central-playlist-modal-window'>
    <div class="media-modal wp-core-ui">
        <div  class="media-modal-content">

            <button type="button" class="button-link media-modal-close"><span class="media-modal-icon"><span class="screen-reader-text"><?php echo __('Close media panel', 'video_central'); ?></span></span></button>

            <div class="backbone_modal-content media-frame mode-select wp-core-ui">
                <div class="navigation-bar media-frame-menu">
                    <div class="media-menu visible">
                        <nav>
                            <ul></ul>
                        </nav>
                    </div>
                </div>
                <div class="media-frame-title"><h1><?php echo __('Select Playlist Videos', 'video_central'); ?></h1></div>
                <div class="media-frame-content" data-columns="5">

                    <div class="attachments-browser">
                        <div class="media-sidebar visible">
                            <div tabindex="0" data-id="3789" class="attachment-details save-ready">
                                <h3>Attachment Details</h3>
                                <div class="attachment-info">
                                    <div class="thumbnail thumbnail-image">
                                        <img src="http://global-wp-content.dev/uploads/2015/08/apple-watch-guided-tour-siri-300x169.jpg" draggable="false">
                                    </div>
                                    <div class="details">
                                        <div class="filename">apple-watch-guided-tour-siri.jpg</div>
                                        <div class="uploaded">August 25, 2015</div>
                                        <div class="file-size">65 kB</div>
                                        <div class="dimensions">1280 × 720</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <ul tabindex="-1" class="attachments ui-sortable ui-sortable-disabled"></ul>

                    </div>
                </div>
                <div class="media-frame-toolbar">
                    <div class="media-toolbar">
                        <div class="media-toolbar-primary">
                            <button id="btn-cancel" class="button button-large"><?php echo __('Cancel', 'video_central'); ?></button>
                            <button id="btn-ok" class="button button-primary button-large"><?php echo __('Save &amp; Continue', 'video_central'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</script>

<?php
/**
 * The Modal Backdrop.
 */
?>
<script type="text/html" id='tmpl-video-central-playlist-modal-backdrop'>
    <div class="media-modal-backdrop"></div>
</script>
<?php
/**
 * Base template for a navigation-bar menu item ( and the only *real* template in the file ).
 */
?>
<script type="text/html" id='tmpl-video-central-playlist-modal-menu-item'>
    <li class="nav-item"><a href="{{ data.url }}">{{ data.name }}</a></li>
</script>
<?php
/**
 * A menu item separator.
 */
?>
<script type="text/html" id='tmpl-video-central-playlist-modal-menu-item-separator'>
    <li class="separator">&nbsp;</li>
</script>
