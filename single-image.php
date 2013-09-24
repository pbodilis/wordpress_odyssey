
<div id="photo_wrapper" class="">
    <figure id="photo_container" class="">

        <img src="<?php echo $post->image->url; ?>" alt="<?php echo $post->title; ?>"
                style="width: <?php echo $post->frame->width; ?>px; height: <?php echo $post->frame->height; ?>px;" />

        <figcaption id="photo_infos">
            <h2><?php echo $post->title; ?></h2>

            <?php if (isset($post->image) && isset($post->image->capture_date)) { ?>
            <p><?php echo $post->image->capture_date; ?></p>
            <?php } ?>
        </figcaption>

    </figure>
</div>



<section id="content" class="<?php echo $post->class; ?>">
    <h2 id="content_title">Info, rate &amp; Comments</h2>
    <article class="post_content"><?php echo $post->content; ?></article>
    <article class="post_categories">
        <ul>
            <?php foreach ($post->categories as $cat) { ?>
            <li><a href="<?php echo $cat->url; ?>">&#91;<?php echo $cat->name; ?>&#93;</a></li>
            <?php } ?>
        </ul>
    </article>
    <article class="image_exifs">
        <ul>
            <?php foreach ($post->image->exifs as $name => $value) { ?>
            <li><?php echo $name . $value; ?></li>
            <?php } ?>
        </ul>
    </article>

    <?php if (isset($post->ratings)) { ?>
    <article class="post_rating"> <?php echo $post->ratings; ?> </article>
    <?php } ?>

    <div class="clr"></div>
</section>

<?php

// echo the_core()->render('photoblog_image', $post);
// echo the_core()->render('photoblog_content', $post);

?>
