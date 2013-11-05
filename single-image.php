
<div id="photo_wrapper" class="<?php echo $post->class; ?>">
    <figure id="photo_container" class="">

        <img src="<?php echo $post->image->url; ?>" alt="<?php echo $post->title; ?>" />

        <figcaption id="photo_infos">
            <h2><?php echo $post->title; ?></h2>

            <?php if (isset($post->image) && isset($post->image->capture_date)) { ?>
            <p><?php echo $post->image->capture_date; ?></p>
            <?php } ?>
        </figcaption>

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

    <?php comments_template(); ?>
</div>
