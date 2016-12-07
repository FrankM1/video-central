<?php

/**
 *  Import Wizard class for Video Central.
 *
 * @since 1.0.0
 *
 * @author  Franklin M Gitonga
 */
class Video_Central_Youtube_Importer_Video_Wizard extends Video_Central_Importer_Wizard
{
    /**
     * Constructor. Hooks all interactions to initialize the class.
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->post_type = video_central_get_video_post_type();

        //add tabs
        add_action('video_central_import_source_tab', array($this, 'add_source_tab'));
        add_action('video_central_import_parameters',      array($this, 'import_params'));
        add_action('video_central_import_table',      array($this, 'import_table'));
    }

    /**
     * Custom post type messages on edit, update, create, etc.
     *
     * @since 1.0.0
     *
     * @param array $messages
     */
    public function add_source_tab()
    {
        ?>
        <div class="video-central-import-source-tab">
            <a href="#" class="video-central-tab-select select-youtube"><img src="<?php echo video_central()->admin->images_url;
        ?>youtube.png" title="youtube" /></a>
        </div>
    <?php
    }

    /**
     * Output video import settings.
     *
     * @since 1.0.0
     */
    public function import_params()
    {
        require_once video_central()->includes_dir.'modules/import/youtube/views/import_videos.php';
    }

    /**
     * Output video importing page.
     *
     * @since 1.0.0
     */
    public function import_table()
    {
        $list_table = video_central()->admin->video_central_list_table;

        if ( ! empty( $list_table ) ) {
            $list_table->prepare_items();
            ?>
        <div class="import-progress">
            <div class="import-progress-inner" style="width: 0%;"></div>
        </div>

        <form method="post" action="" class="ajax-submit">
            <?php wp_nonce_field('video-central-import-videos-to-wp', 'video_central_import_nonce');
            ?>
            <input type="hidden" name="action" class="video_central_ajax_action" value="video_central_import_videos" />
            <input type="hidden" name="video_central_source" value="youtube" />
            <?php $list_table->display();
            ?>
        </form>

     <?php
        }
    }
}

video_central()->admin->youtube_importer_wizard = new Video_Central_Youtube_Importer_Video_Wizard();
?>
