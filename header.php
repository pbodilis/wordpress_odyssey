<?php
/**
 * Template Name: Header
 */

session_start();
// if (isset($_REQUEST['info']) && !empty($_REQUEST['info']))
//     $_SESSION['odyssey:info'] = $_REQUEST['info'];
?>


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr" dir="ltr">
<head>
    <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
    <meta name="generator" content="WordPress <?php bloginfo('version'); ?>" /> <!-- leave this for stats -->
    <meta name="title" content="<?php wp_title(); ?>" />

    <link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
    <link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
    <link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> Comments RSS Feed" href="<?php bloginfo('comments_rss2_url'); ?>" />
    <link rel="alternate" type="application/atom+xml" title="<?php bloginfo('name'); ?> Atom Feed" href="<?php bloginfo('atom_url'); ?>" />

    <title><?php bloginfo('name'); ?> <?php wp_title(); ?></title>

    <!-- theme js -->
    <?php odyssey_embed_javascripts(); ?>

    <?php wp_head(); ?>
</head>
<body>

<?php

$data = array(
    'blogName'    => get_bloginfo('name'),
    'blogHomeUri' => get_bloginfo('url'),
);

$mustache = new Mustache_Engine(array(
    'template_class_prefix' => '__MyTemplates_',
//     'cache' => dirname(__FILE__).'/tmp/cache/mustache',
//     'cache_file_mode' => 0666, // Please, configure your umask instead of doing this :)
    'loader' => new Mustache_Loader_FilesystemLoader(dirname(__FILE__) . '/templates', $options = array('extension' => '.mustache.html',)),
//     'partials_loader' => new Mustache_Loader_FilesystemLoader(dirname(__FILE__).'/views/partials'),
//     'helpers' => array('i18n' => function($text) {
//         // do something translatey here...
//     }),
    'escape' => function($value) {
        return htmlspecialchars($value, ENT_COMPAT, 'UTF-8');
    },
    'charset' => 'ISO-8859-1',
    'logger' => new Mustache_Logger_StreamLogger('php://stderr'),
));

$tpl = $mustache->loadTemplate('photoblog_header');
echo $tpl->render($data);
?>