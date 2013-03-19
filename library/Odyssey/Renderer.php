<?php
/**
 * This file is part of Odyssey theme for wordpress.
 *
 * (c) 2013 Pierre Bodilis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Odyssey;

/**
 *  Rendering class: basically a wrapper for Mustache engine
 *  @package Odyssey Theme for WordPress
 *  @subpackage Renderer
 */
class Renderer
{
    protected $mustache;

    static private $instance;
    static public function get_instance(array $params = array())
    {
        if (!isset(self::$instance)) {
            self::$instance = new self($params);
        }
        return self::$instance;
    }

    public function __construct(array $params = array())
    {
        $this->mustache = new \Mustache_Engine(
            array(
            //     'template_class_prefix' => '__MyTemplates_',
            //     'cache' => dirname(__FILE__).'/tmp/cache/mustache',
            //     'cache_file_mode' => 0666, // Please, configure your umask instead of doing this :)
                'loader' => new \Mustache_Loader_FilesystemLoader(
                	dirname(dirname(dirname(__FILE__))) . '/templates',
                	$options = array('extension' => '.mustache.html',)
                ),
                'partials_loader' => new \Mustache_Loader_FilesystemLoader(
                    dirname(dirname(dirname(__FILE__))) . '/templates',
                    $options = array('extension' => '.partial.mustache.html',)
                ),
            //     'helpers' => array('i18n' => function($text) {
            //         // do something translatey here...
            //     }),
                'escape' => function($value) {
                    return htmlspecialchars($value, ENT_COMPAT, 'UTF-8');
                },
                'charset' => 'ISO-8859-1',
    //             'logger' => new Mustache_Logger_StreamLogger('php://stderr'),
            )
        );

        // add i18n localization
        $this->mustache->addHelper('_i18n', function($text) {return __( $text, 'odyssey' );});
    }

    public function render($template, $data)
    {
        $tpl = $this->mustache->loadTemplate($template);
        return $tpl->render($data);
    }
}

?>