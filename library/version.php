<?php
/*     
    This file is part of Odyssey Theme for WordPress.

*//**

    Version helper functions
    
    @package Odyssey Theme for WordPress
    @subpackage Version
*/

    /* defines */

    /** The version of the theme */
    define('ODYSSEY_THEME_VERSION_BASE', '0.1 beta');
    
    /** An extension to the current version */
    define('ODYSSEY_THEME_VERSION_EXTENDED', '');
    
    /** The URL of the theme project */
    define('ODYSSEY_THEME_URL', 'https://github.com/pbodilis/');
    
    /** The URL of the YAPB project */
    define('ODYSSEY_YAPB_URL', 'http://wordpress.org/extend/plugins/yet-another-photoblog/');

    // set the extended version
    if (defined("ODYSSEY_THEME_VERSION_EXTENDED") && ODYSSEY_THEME_VERSION_EXTENDED != '' )  {
        /** The version of the theme */
        define('ODYSSEY_THEME_VERSION', ODYSSEY_THEME_VERSION_BASE . ' ' . ODYSSEY_THEME_VERSION_EXTENDED);
    } else {
        define('ODYSSEY_THEME_VERSION', ODYSSEY_THEME_VERSION_BASE);
    }
    
    /** The Odyssey-relative path to the file that triggers development mode */
    define("ODYSSEY_DEV_TRIGGER", "/func/moonsugar.php");

    /* include development build information */
    if (file_exists(TEMPLATEPATH.ODYSSEY_DEV_TRIGGER) ) {
        @require_once(TEMPLATEPATH.ODYSSEY_DEV_TRIGGER);
        if(!defined("ODYSSEY_THEME_VERSION_DEVBUILD")) define('ODYSSEY_THEME_VERSION_DEVBUILD', true);
        // Set header
        if (!headers_sent() ) header("X-Odyssey-Devbuild: R".ODYSSEY_THEME_VERSION_REVISION);
    }
    else {
        if(!defined("ODYSSEY_THEME_VERSION_DEVBUILD")) define('ODYSSEY_THEME_VERSION_DEVBUILD', false);
    }
    
    // Add version response header. Used for version determination in case of support requests.
    if (!headers_sent() && !defined("ODYSSEY_NO_VERSIONRESPONSE")) header("X-Odyssey-Version: ".ODYSSEY_THEME_VERSION);
    if (!headers_sent()) {
        global $yapb;
        header("X-Yapb-Version: ".$yapb->pluginVersion);
    }
    
    /**
     * odyssey_getodysseyfvlink() - Gets the HTML markup for the footer, containing the Version of Odyssey
     *
     * @since 0.3
     * @return string HTML markup containing the current version of Odyssey
     */
    function odyssey_getodysseyfvlink() {
        $odysseyName  = 'Odyssey' . (ODYSSEY_THEME_VERSION_DEVBUILD ? ' ' . ODYSSEY_THEME_VERSION : '');
        $odysseyTitle = 'Odyssey' . (ODYSSEY_THEME_VERSION_DEVBUILD ? ' (development' . (defined("ODYSSEY_THEME_VERSION_REVISION") ? ' R' . ODYSSEY_THEME_VERSION_REVISION : '' ) .')' : '');
        $odysseyURL   = '<a class="odyssey" href="'.ODYSSEY_THEME_URL.'" title="'.$odysseyTitle.'">'.$odysseyName.'</a>';
        return $odysseyURL;
    }
    
    /**
     * odyssey_version() - Gets a string containing the current version of Odyssey
     *
     * On development builds, "(dev)" is appended.
     *
     * @since 0.3
     * @return string The current version of Odyssey
     */
    function odyssey_version()
    {
        $version = ODYSSEY_THEME_VERSION;
        if (ODYSSEY_THEME_VERSION_DEVBUILD) $version .= ' (dev)';
        return $version;
    }

?>
