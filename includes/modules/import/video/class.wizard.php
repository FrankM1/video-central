<?php

/**
 *  Import Wizard class for Video Central.
 *
 * @since 1.0.0
 *
 * @author  Franklin M Gitonga
 */
class Video_Central_Importer_Wizard
{
    /**
     * Constructor. Hooks all interactions to initialize the class.
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        if (!video_central_allow_video_imports()) {
            return;
        }

        $this->post_type = video_central_get_video_post_type();

        // add extra menu pages
        add_action('admin_menu', array($this, 'menu_pages'), 1);
    }

    /**
     * Add subpages on our custom post type.
     *
     * @since 1.0.0
     *
     * @uses register_taxonomy() To register the taxonomy
     */
    public function menu_pages()
    {
        $video_import = add_submenu_page('edit.php?post_type='.$this->post_type, __('Import videos', 'video_central'), __('Import videos', 'video_central'), 'edit_posts', 'video_central_import', array($this, 'import_source_tabs'));

        add_action('load-'.$video_import, array($this, 'import_onload'));
    }

    /**
     * Output video importing page.
     *
     * @since 1.0.0
     */
    public function import_source_tabs()
    {
        $data_step_current = 1;
        $current = 'step1';

        if (isset($_REQUEST['video_central_search_nonce'])) {
            $data_step_current = 3;
            $current = 'step3';
        }

        ?>
        <div class="wizard-wrap" data-step="<?php echo esc_attr( $data_step_current ); ?>">

            <div class="wizard-container">

                <h1><?php _e('Video Import Wizard', 'video_central');
        ?></h1>

                <div class="progress_bar">
                    <hr class="all_steps">
                    <hr class="current_steps">

                    <div class="step <?php if ($current == 'step2' || $current == 'step3' || $current == 'step4') {
    echo 'complete';
}
        ?> <?php if ($current == 'step1') {
    echo 'current';
}
        ?>" id="step1" data-step="1">
                        <?php esc_html_e('Select Source', 'video_central');
        ?>
                    </div>

                    <div class="step <?php if ($current == 'step2' || $current == 'step3') {
    echo 'complete';
}
        ?> <?php if ($current == 'step2') {
    echo 'current';
}
        ?>" id="step2" data-step="2">
                        <?php esc_html_e('Set Parameters', 'video_central');
        ?>
                    </div>

                    <div class="step <?php if ($current == 'step4') {
    echo 'complete';
}
        ?> <?php if ($current == 'step3') {
    echo 'current';
}
        ?>" id="step3" data-step="3">
                        <?php esc_html_e('Select Videos & Import', 'video_central');
        ?>
                    </div>

                    <div class="step <?php if ($current == 'step4') {
    echo 'current';
}
        ?>" id="step4" data-step="4">
                        <?php esc_html_e('Done', 'video_central');
        ?>
                    </div>
                </div>

                <div id="blocks">

                    <div class="block" id="block1" <?php if ($current == 'step1') {
    echo 'style="left: 0%;"';
}
        ?>>
                        <div class="wrap video-central-import-source-tab">

                            <h2>Select Source</h2>

                            <div class="video-central-import-source-tab">
                                <?php do_action('video_central_import_source_tab');
        ?>
                            </div>

                            <br />
                            <a onclick="window.Video_Central_Import.Wizard_Step_Process(1, 2)" class="button"><?php esc_html_e('Start', 'video_central');
        ?></a>
                        </div>
                    </div>

                <div class="block" id="block2">
                    <div class="wrap video-central-import-parameters">
                        <div class="video-central-import-parameters-description">
                            <h2><?php esc_html_e('Set Parameters', 'video_central');
        ?></h2>
                            <p class="description">
                                <?php esc_html_e('Enter the search criteria and submit.', 'video_central');
        ?>
                            </p>
                        </div>
                         <div id="video-central-import-parameters">
                             <?php do_action('video_central_import_parameters');
        ?>
                         </div>
                    </div>
                </div>

                <div class="block" id="block3">
                    <div class="wrap video-central-import-table">
                        <div class="video-central-import-table-description">
                            <h2 class="video-central-ajax-response">
                                <span class="video-central-ajax-response-task"><?php _e('Select Videos to Import', 'video_central');
        ?></span><span class="sep">: </span>
                                <span class="video-central-ajax-response-progress"></span>
                            </h2>
                        </div>

                         <div id="video-central-import-table">
                             <?php do_action('video_central_import_table');
        ?>
                         </div>

                        <a onclick="window.Video_Central_Import.Wizard_Step_Process(3, 2, 'desc')" class="button"><?php _e('Go Back', 'video_central');
        ?></a>
                     </div>
                </div>

                <div class="block" id="block4">
                    <div class="wrap video-central-import-done">
                        <h2><?php esc_html_e('Done', 'video_central');
        ?></h2>
                        <div id="video-central-import-done"><?php do_action('video_central_import_complete');
        ?></div>
                        <br />
                        <a onclick="window.Video_Central_Import.Wizard_Step_Process(4, 1, 'restart')" class="button"><?php esc_html_e('Start Over', 'video_central');
        ?></a>
                        <a href="<?php echo admin_url('edit.php?post_type='.video_central_get_video_post_type());
        ?>" class="button"><?php esc_html_e('Go to Videos List', 'video_central');
        ?></a>

                    </div>
                </div>

                </div>
                <br class="clear" />

            </div>

        </div>

        <div class="import-global-loading"></div>
        <div class="import-global-message"></div>

        <?php

    }

    /**
     * On video import page load, perform actions.
     *
     * @since 1.0.0
     */
    public function import_onload()
    {
        do_action('video_central_import_onload');
    }
}

video_central()->admin->importer_wizard = new Video_Central_Importer_Wizard();
?>
