<div id="wrapper" class="">
    <h2 id="content_title"><?php echo $post->title; ?></h2>
    <section id="content" class="<?php echo $post->class; ?>">
        <article class="post_content"><?php echo $post->content; ?></article>
        <article class="post_categories">
            <ul>
                <?php foreach ($post->categories as $cat) { ?>
                <li><a href="<?php echo $cat->url; ?>">&#91;<?php echo $cat->name; ?>&#93;</a></li>
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

