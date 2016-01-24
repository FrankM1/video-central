<?php

$video_id = video_central_get_video_id();

$queried_post = get_post($video_id);

$url = get_permalink();

$title = get_the_title();

?>

<div class="video-central-share">
    <a class="icon icon-twitter icon_embed_tw" href="http://twitter.com/home/?status=<?php echo urlencode($url . " - " . $title); ?>" title="Twitter" target="blank">
    </a>
    <a class="icon icon-facebook icon_embed_fb" href="http://www.facebook.com/sharer.php?u=<?php echo urlencode( get_permalink() ); ?>&amp;t=<?php echo urlencode( get_the_title() ); ?>" title="Facebook" target="blank">
    </a>

    <a class="icon icon-google-plus" href="https://plusone.google.com/_/+1/confirm?hl=en&amp;url=<?php echo urlencode($url); ?>" title="Googleplus" target="blank">
    </a>

    <a class="icon icon-linkedin" href="http://www.linkedin.com/shareArticle?mini=true&amp;url=<?php echo urlencode($url); ?>&amp;summary=<?php echo urlencode($title); ?>&amp;source=<?php echo urlencode(home_url()); ?>" title="Linkin" target="blank">
    </a>

</div>
