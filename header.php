<?php
/**
 * Template Name: Header
 */

session_start();

$blog        = the_core()->get_blog();
$syndication = the_core()->get_syndication();
$others      = the_core()->get_other_links();
?>


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr" dir="ltr">
<head>
    <meta http-equiv="Content-Type" content="<?php echo $blog->html_type; ?>; charset=<?php echo $blog->charset; ?>" />
    <meta name="generator" content="WordPress <?php echo $blog->version; ?>" /> <!-- leave this for stats -->
    <meta name="title" content="<?php echo $blog->title; ?>" />

    <link rel="alternate" type="application/rss+xml" title="<?php echo $blog->name; ?> RSS Feed" href="<?php echo $blog->rss2_url; ?>" />
    <link rel="alternate" type="application/rss+xml" title="<?php echo $blog->name; ?> Comments RSS Feed" href="<?php echo $blog->comments_rss2_url; ?>" />
    <link rel="alternate" type="application/atom+xml" title="<?php echo $blog->name; ?> Atom Feed" href="<?php echo $blog->atom_url; ?>" />

    <title><?php echo $blog->name . $blog->title; ?></title>

    <!-- theme js -->
    <?php wp_head(); ?>
    <link rel="stylesheet" href="<?php echo $blog->stylesheet_url; ?>" type="text/css" media="screen and (min-width: 800px)" />
    <link rel="stylesheet" href="<?php echo ''; ?>" type="text/css" media="screen and (maw-width: 800px)" />
</head>
<body <?php body_class();?> >

    <header id="headerbar" class="headerbar">
        <h1 id="logo">
            <a href="<?php echo $blog->home_url; ?>" title="<?php echo $blog->description; ?>">
                <?php echo $blog->name; ?>
            </a>
        </h1>

        <nav>
            <ul class="menu txt">
                <?php foreach ($others as $o) {?>
                <li class="<?php echo $o->classes; ?>">
                    <a href="<?php echo $o->url; ?>" title="<?php echo $o->title; ?>" >
                        <?php echo $o->name; ?>
                    </a>
                </li>
                <?php } ?>
                <?php echo the_core()->get_page_list(); ?>
            </ul>
        </nav>

        <?php if ( ! empty($syndication) ) { ?>
        <div class="menu but syndication">
            <?php foreach ( $syndication as $s ) { ?>
            <a class="<?php  echo $s->class; ?>"
                href="<?php  echo $s->url;   ?>"
                title="<?php echo $s->title; ?>"
                target="_blank"></a>
            <?php } ?>
        </div>
        <?php } ?>

        <?php if ( is_home() || is_single() ) { ?>
        <div class="menu but color">
            <a class="white"     href="#"></a>
            <a class="dark_grey" href="#"></a>
            <a class="black"     href="#"></a>
        </div>
        <?php } ?>
        <div class="menu loading"></div>
    </header>

