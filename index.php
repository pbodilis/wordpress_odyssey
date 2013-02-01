<?php
/*
Template Name: Index Template
*/

get_header();
?>

<body>
    <div id="header">
        <h1 id="logo"><a href="index.php" title="Home"><?php bloginfo('name'); ?></a></h1>
        <div class="menu txt">
            <ul>
                <li><a class="item" href="index.php"                         title="Back to the latest picture"> Latest </a></li>
                <li><a class="item" href="index.php?x=browse&amp;filter=all" title="Browse pictures">            Browse </a></li>
                <li><a class="item" href="index.php?x=about"                 title="Details about the author">   About  </a></li>
            </ul>
        </div>
        <div class="menu but social">
            <a class="menu_but_rss" href="./index.php?x=rss"                                                        title="RSS Feed"></a>
            <a class="menu_but_tw"  href="http://twitter.com/home?status=<SITE_URL>/index.php?showimage=<IMAGE_ID>" title="Click to share this post on Twitter" target="_blank"></a>
            <a class="menu_but_fb"  href="http://www.facebook.com/pages/An-Everyday-Lifes-Odyssey/113318748702527"  title="Facebook" target="_blank"></a>
            <a class="menu_but_fr"  href="http://www.flickr.com/photos/rataki/"                                     title="Flickr"   target="_blank"></a>
                <!-- <a href="http://plus.google.com" class="menu_but_go" title="Google+" target="_blank"></a> -->
        </div>
        <div class="menu but color">
            <a class="white"      href="#"></a>
<!--            <a class="light_grey" href="#"></a> -->
            <a class="dark_grey"  href="#"></a>
            <a class="black"      href="#"></a>
        </div>
        <div class="menu but nav">
            <a class="prev" href="./index.php?showimage="<?php get_previous_post(); ?>><span></span></a>
            <a class="next" href="./index.php?showimage="<?php get_next_post(); ?>><span></span></a>
        </div>
        <div class="menu but tooltip">
            <a href="#">
                ?
            </a>
            <div class="help">
                <ul>
                    <li>Keyboard navigation:
                        <ul>
                            <li>press '&larr;' or '&rarr;' to jump to previous or next picture</li>
                            <li>press 'space' to toggle panel</li>
                        </ul>
                    </li>
                    <li>Use the square buttons to change background color</li>
                </ul>
            </div>
        </div>
    </div>
    <div id="panel">
        <div id="panel_handle">
            <div id="panel_handle_arrowbox">
                <div id="panel_handle_arrow"></div>
            </div>
            <h2>Info,&nbsp;rate&nbsp;&amp;&nbsp;Comments</h2>
        </div>
        <div id="panel_scroll">
            <div id="panel_content">
<!--                <h4 class="image_title"><IMAGE_TITLE></h4> -->
                <div class="image_notes"><IMAGE_NOTES></div>
                <div class="panel_blocks">
                    <ul>
                        <li><EXIF_CAMERA_MODEL></li>
                        <li><EXIF_FOCAL_LENGTH></li>
                        <li><EXIF_APERTURE></li>
                        <li><EXIF_EXPOSURE_TIME></li>
                        <li><EXIF_EXPOSURE_BIAS></li>
                        <li>ISO <EXIF_ISO></li>
                        <li><EXIF_FLASH_SUBIFD></li>
                        <li><EXIF_CAPTURE_DATE></li>
                    </ul>
                </div>
                <div class="panel_blocks"><ODYSSEY_IMAGE_CATEGORIES></div>
                <div class="panel_blocks extras">
                    <a href="http://www.coolphotoblogs.com/"><img src="http://www.coolphotoblogs.com/cpb.gif" alt="coolphotoblogs" width="80" height="15" border="0"/></a>
                    <a href="http://www.coolphotoblogs.com/profile4962"><img src="http://www.coolphotoblogs.com/profile.gif" alt="my profile" width="80" height="15" border="0"/></a>
                </div>
                <div class="panel_blocks"><AJAX_RATE stars="10" img_width="14px"></div>
                <h5>Leave a comment</h5>
                <div id="commentform">
                    <form id="form" method="post" action="index.php?x=save_comment" name="commentform" accept-charset="UTF-8">
                        <div id="vinfos_block">
                            <div id="name_block">
                                <label for="name">Name:</label>
                                <input id="name" class="input_vinfos" type="text" name="name" value="<VINFO_NAME>" />
                            </div>
                            <div id="email_block">
                                <label for="email">Email (not visible):</label>
                                <input id="email" class="input_vinfos" type="text" name="email" value="<VINFO_EMAIL>" />
                            </div>
                            <div id="url_block">
                                <label for="url">URL (if any):</label>
                                <input id="url" class="input_vinfos" type="text" name="url" value="<VINFO_URL>" />
                            </div>
                        </div>
                        <textarea class="input_vinfos" id="message" name="message" cols="0" rows="0"></textarea>
                        <div id="vinfos_save_block">
                            <input id="save_user_info" type="checkbox" value="set" name="vcookie" checked="checked" />
                            <label for="save_user_info">Save user info</label>
                        </div>
                        <input class="input_submit" type="submit" id="comment_submit" value="Add Comment" />
                        <div class="clr"></div>
                        <input type="hidden" name="parent_id" value="<IMAGE_ID>" />
                        <input type="hidden" name="parent_name" value="<IMAGE_NAME>" />
                        <input type="hidden" name="withthankyou" value="no" />
                    </form>
                </div>
                <div id="thank_for_comment">Thank you for visiting and taking the time to comment on the picture.</div>
                <h5><IMAGE_COMMENTS_NUMBER> Comment(s)</h5>
                <div id="image_comments">
                    <ODYSSEY_COMMENTS>
                </div>
<!--                 <div class="panel_blocks powered_template">Powered by <a href="http://pixelpost.org/" title="Pixelpost">Pixelpost</a> - Template: <a href="http://www.pixelpost.org/extend/templates/odyssey/">Rasul v2.6</a></div> -->
            </div>
        </div>
    </div>

<div id="photo_frame"></div>



<?php

if (have_posts()): 
	while (have_posts()): the_post();
		if ($post->image):
			// Build navigation links, image and imagemap
			// This is mostly adapted stuff from the grain theme
			$next = get_next_post();
			$previous = get_previous_post();

			// decide navigational messages and links
			$navigation_state = 0;

			if( $previous != null ) :
				$message_prev = NAV_PREVIOUS;
				$title_prev = TITLE_PREVIOUS;
				$link_prev = get_permalink($previous->ID);
				$navlink_prev = buildLink($link_prev, $message_prev);
			else:
				$message_prev = NAV_NO_PREVIOUS;
				$title_prev = TITLE_NO_PREVIOUS;
				$link_prev = '#';
				$navlink_prev = $message_prev;
				$navigation_state = -1;
			endif;

			if( $next != null ) :
				$message_next = NAV_NEXT;
				$title_next = TITLE_NEXT;
				$link_next = get_permalink($next->ID);
				$navlink_next = buildLink($link_next, $message_next);
			else:
				$message_next = NAV_NO_NEXT;
				$title_next = TITLE_NO_NEXT;
				$link_next = '#';
				$navlink_next = $message_next;
				$navigation_state = 1;
			endif;

			$navlink_center = monolit_get_comments_link();

			// decide which dimensions the image shall have
			$dimensions = getimagesize($post->image->systemFilePath());
			$width = $dimensions[0];
			$height = $dimensions[1];

			// recude image width to 900
			if ( $width > 900 ) :
				$height = $height * 900 / $width;
				$width = 900;
			endif;

			// recude image height to configure value
			if ( $height > MONOLIT_SET_MAX_HEIGHT ) :
				$width = $width * MONOLIT_SET_MAX_HEIGHT / $height;
				$height = MONOLIT_SET_MAX_HEIGHT;
			endif;

			// calculate half the image with for use with imagemap
			$width2 = (int)($width / 2);

			// correct odd image widths
			$right_width = $width2;
			if ( 2*$width2 < $width )
				$right_width++;

			// decide which title to output
			$title_attr = '';
			if ( $navigation_state != 0 )
				$title_attr = ($navigation_state > 0) ? $title_next : $title_prev;

			// build and output photo
 			$thephoto  = '<div id="image">';
			$thephoto .= '<img class="photo" ';
			$thephoto .= 	'title="' . $title_attr . '" ';
			$thephoto .= 	'alt="' . $post->post_title . '" ';
			$thephoto .= 	'style="width: ' . $width . 'px; height: ' . $height . 'px;" ';
			$thephoto .= 	'width="' . $width . '" height="' . $height . '" ';
			$thephoto .= 	'src="' . $post->image->uri . '" ';
			$thephoto .= 	'usemap="#bloglinks" ';
			$thephoto .= '/>';
			$thephoto .= '</div>' . "\n";
			print $thephoto;

			// build and output imagemap
			$imagemap  = '<map name="bloglinks" id="bloglinks">'."\n";

			if ( $previous != null ) :
				$imagemap .= '<area shape="rect" ';
				$imagemap .= 	'coords="0,0,' . $width2 . ',' . $height . '" ';
				$imagemap .= 	'title="' . $message_prev . '" ';
				$imagemap .= 	'alt="' . $message_prev . '" ';
				$imagemap .= 	'href="' . get_permalink($previous->ID) . '" ';
				$imagemap .= '/>' . "\n";
			endif;

			if ( $next != null ) :
				$imagemap .= '<area shape="rect" ';
				$imagemap .= 	'coords="' . $width2 . ',0,' . ($right_width+$width2) . ',' . $height . '" ';
				$imagemap .= 	'title="' . $message_next . '" ';
				$imagemap .= 	'alt="' . $message_next . '" ';
				$imagemap .= 	'href="' . get_permalink($next->ID) . '" ';
				$imagemap .= '/>' . "\n";
			endif;

			$imagemap .= '</map>' . "\n";
			print $imagemap;

			// output image navigation bar
			monolit_print_imagenavigation($navlink_prev, $navlink_center, $navlink_next);
		else:
		endif;

		// output the title, author, date, and content of the post in a container
		monolit_print_container(null, monolit_get_postoutput());
		// output the comments and comment form
		monolit_print_info();
	endwhile;
else:
endif;

get_footer();
?>
