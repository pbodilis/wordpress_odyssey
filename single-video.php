
<div id="photo_wrapper" class="<?php echo $post->class; ?>">
    <figure id="photo_container" class="">
        <?php echo wp_oembed_get($post->video->url); ?>

<!--        <figcaption id="photo_infos">
            <h2><?php echo $post->title; ?></h2>
        </figcaption>-->

    </figure>
</div>


<div id="wrapper" class="<?php echo $post->class; ?>">
    <h2 id="content_title">Info, rate &amp; Comments</h2>
    <nav>
        <a class="navdown"></a>
    </nav>
    <section id="content" class="<?php echo $post->class; ?>">
        <article class="post_content"><?php echo $post->content; ?></article>
        <article class="post_categories">
            <ul>
                <?php
                foreach ($post->categories as $cat_name => $cat_url) {
                    echo '<li><a href="' . $cat_url . '">&#91;' . $cat_name . '&#93;</a></li>';
                }
                ?>
            </ul>
        </article>

        <?php if (isset($post->ratings)) { ?>
        <article class="post_rating"> <?php echo $post->ratings; ?> </article>
        <?php } ?>

        <div class="clr"></div>
    </section>

    <?php comments_template(); ?>
</div>
