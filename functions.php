<?php
/**
 * Timber starter-theme
 * https://github.com/timber/starter-theme
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.1
 */

if ( ! class_exists( 'Timber' ) ) {
    add_action( 'admin_notices', function() {
        echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php' ) ) . '</a></p></div>';
    });

    add_filter('template_include', function( $template ) {
        return get_stylesheet_directory() . '/static/no-timber.html';
    });

    return;
}

/**
 * Sets the directories (inside your theme) to find .twig files
 */
Timber::$dirname = array( 'templates', 'views' );

/**
 * By default, Timber does NOT autoescape values. Want to enable Twig's autoescape?
 * No prob! Just set this value to true
 */
Timber::$autoescape = false;


/**
 * We're going to configure our theme inside of a subclass of Timber\Site
 * You can move this to its own file and include here via php's include("MySite.php")
 */
class StarterSite extends Timber\Site {
    /** Add timber support. */
    public function __construct() {
        add_action( 'after_setup_theme', array( $this, 'theme_supports' ) );
        add_filter( 'timber_context', array( $this, 'add_to_context' ) );
        add_filter( 'get_twig', array( $this, 'add_to_twig' ) );
        add_action( 'init', array( $this, 'register_post_types' ) );
        add_action( 'init', array( $this, 'register_taxonomies' ) );
        parent::__construct();
    }
    /** This is where you can register custom post types. */
    public function register_post_types() {

    }
    /** This is where you can register custom taxonomies. */
    public function register_taxonomies() {

    }

    /** This is where you add some context
     *
     * @param string $context context['this'] Being the Twig's {{ this }}.
     */
    public function add_to_context( $context ) {
        $args = ['menu_class' => ''];
        $context['main_menu'] = new \Timber\Menu( 'main_menu', $args );
        $context['site'] = $this;
        return $context;
    }

    public function theme_supports() {
        // Add default posts and comments RSS feed links to head.
        add_theme_support( 'automatic-feed-links' );

        /*
         * Let WordPress manage the document title.
         * By adding theme support, we declare that this theme does not use a
         * hard-coded <title> tag in the document head, and expect WordPress to
         * provide it for us.
         */
        add_theme_support( 'title-tag' );

        /*
         * Enable support for Post Thumbnails on posts and pages.
         *
         * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
         */
        add_theme_support( 'post-thumbnails' );

        /*
         * Switch default core markup for search form, comment form, and comments
         * to output valid HTML5.
         */
        add_theme_support(
            'html5', array(
                'comment-form',
                'comment-list',
                'gallery',
                'caption',
            )
        );

        /*
         * Enable support for Post Formats.
         *
         * See: https://codex.wordpress.org/Post_Formats
         */
        add_theme_support(
            'post-formats', array(
                'aside',
                'image',
                'video',
                'quote',
                'link',
                'gallery',
                'audio',
            )
        );

        add_theme_support( 'menus' );
    }

    /** This is where you can add your own functions to twig.
     *
     * @param Asm89\Twig\ $twig .
     *
     * @return
     */
    public function add_to_twig( $twig ) {
        $twig->addExtension( new Twig_Extension_StringLoader() );

        return $twig;
    }
}

/**
 * My custom Twig functionality.
 *
 * @param Twig_Environment $twig
 * @return $twig
 */
add_filter( 'timber/twig', function( \Twig_Environment $twig ) {
    $twig->addFunction( new Timber\Twig_Function( 'responsive_image', 'responsive_image' ) );

    return $twig;
} );

/**
 * @param int $imageId
 * @param string|array $imageSize
 *
 * @return string
 */
function responsive_image($imageId, $imageSize) {
    $imgSrc = wp_get_attachment_image_url($imageId, $imageSize);
    $imgAlt = get_post_meta($imageId, '_wp_attachment_image_alt', true);
    $imageHTML = "<img src=\"{$imgSrc}\" alt=\"{$imgAlt}\"/>";
    $imageMetaData = wp_get_attachment_metadata($imageId);

    return wp_image_add_srcset_and_sizes($imageHTML, $imageMetaData, $imageId);
}

/**
 * @param string $imageUrl
 * @param \Timber\Image $timberImage
 *
 * @param string $imageSize
 *
 * @return null|string
 */
function get_image_srcset_sizes($image_src, $timberImage, $imageSize) {
    if ( empty( $timberImage->sizes ) ) {
        return null;
    }

    // Return early if we couldn't get the image source.
    if ( ! $image_src ) {
        return null;
    }

    if (!isset($timberImage->sizes[$imageSize])) {
        return null;
    }

    $width = $timberImage->sizes[$imageSize]['width'];
    $height = $timberImage->sizes[$imageSize]['height'];

    $size_array = array( $width, $height );
    $srcset = wp_calculate_image_srcset( $size_array, $image_src, $timberImage->image_meta, $timberImage->id );

    if ( $srcset ) {
        $sizes = wp_calculate_image_sizes( $size_array, $image_src, $timberImage->image_meta, $timberImage->id );
    }

    $attr = '';

    if ( $srcset && $sizes ) {
        // Format the 'srcset' and 'sizes' string and escape attributes.
        $attr = sprintf( ' srcset="%s"', esc_attr( $srcset ) );

        if ( is_string( $sizes ) ) {
            $attr .= sprintf( ' sizes="%s"', esc_attr( $sizes ) );
        }
    }

    return $attr;
}

add_action('wp_head','add_to_head_dns_prefetch');
add_action('wp_head','add_to_head_preload');
add_action('wp_head','add_tags_to_head');

/**
 *
 */
function add_to_head_dns_prefetch() {
    $sites_url = [
        'http://fonts.google.com',
    ];

    $output = null;
    if (count($sites_url) > 0) {
        $def = '<link rel="dns-prefetch" href="%s"/>';
        foreach ( $sites_url as $item ) {
            $output .= sprintf($def, $item);
        }
    }

    echo $output;
}

/**
 *
 */
function add_to_head_preload() {
    $preloads = [
        [
            'path' => '/assets/fonts/fontello.woff2',
            'attributes' => 'as="font" crossorigin',
        ],
    ];

    $output = null;
    if (count($preloads) > 0) {
        $def = '<link rel="preload" href="' . get_template_directory_uri() . '%s" %s />';
        foreach ( $preloads as $item ) {
            $output .= sprintf($def, $item['path'], $item['attributes']);
        }
    }

    echo $output;
}

function add_tags_to_head() {

    $output = '<meta name="theme-color" content="#005D34">';

    echo $output;
}

/**
 * @param [] $classes
 *
 * @return array
 */
function add_body_class($classes) {

    return $classes;
}

add_filter( 'body_class', 'add_body_class' );

if (class_exists('ACF')) {
    add_filter( 'acf/load_field/type=image', function( $field ) {
        $field['return_format'] = 'id';

        return $field;
    } );
}

add_filter( 'timmy/sizes', 'timmy_filter');

/**
 * @param $sizes
 *
 * @return array
 */
function timmy_filter($sizes) {
    $config = apply_filters('get_config_for_responsive_picture', [] );

    $timmy_config = [];
    foreach ($config as $alias => $value) {
        if (isset($value['convert']) && count($value['convert']) > 0) {
            $timmy_config[$alias] = array_merge($value, $value['convert']);
        } else {
            $timmy_config[$alias] = $value;
        }
    }

    return $timmy_config;
}

/**
 * @param \Timber\Image $timber_image
 * @param string $alias
 *
 * @return string
 */
function get_responsive_picture(Timber\Image $timber_image, $alias) {
    $config = apply_filters('get_config_for_responsive_picture', [] );
    $img = '<img %s alt="%s">';
    $source = '<source %s>';
    $output = '<picture>%s</picture>';
    $jpg_output = '';
    $webp_output = '';

    if (empty($alias) || !array_key_exists($alias, $config)) {
        $img_output = sprintf($img, 'src="' . $timber_image->src() . '"', $timber_image->alt());

        return sprintf($output, $img_output);
    }

    $srcset_sizes = get_timber_image_responsive_src($timber_image, $alias);
    $img_output = sprintf($img, $srcset_sizes, $timber_image->alt());

    if ( !is_array($config) && count($config) === 0) {

        return sprintf($output, $img_output);
    }

    if (isset($config[$alias]['convert']['towebp'])) {
        $srcset_sizes = get_timber_image_responsive_src($timber_image, $alias);
        $webp_output = sprintf($source, $srcset_sizes);
    }

    if (isset($config[$alias]['convert']['tojpg'])) {
        $srcset_sizes = get_timber_image_responsive_src($timber_image, $alias);
        $jpg_output = sprintf($source, $srcset_sizes);
    }

    return sprintf($output, $jpg_output . $webp_output . $img_output);
}

add_filter( 'get_config_for_responsive_picture', function( $sizes ) {
    return [
        /**
         * The thumbnail size is used to show thumbnails in the backend.
         * You should always have an entry with the 'thumbnail' key.
         */
        'resp800' => [
            'resize' => [ 800 ],
            'sizes'  => '(min-width: 62rem) 33.333vw, 100vw',
            'name'   => 'resp800',
            'srcset' => [
                [640],
                [480],
                [360],
                [300],
            ],
            'generate_srcset_sizes' => true,
            'convert' => [
                'towebp' => 100,
                'tojpg' => '#FFFFFF',
            ]
        ],
    ];
} );

add_filter( 'timber/twig', function( \Twig_Environment $twig ) {
    $twig->addFunction( new Timber\Twig_Function( 'responsive_image', 'responsive_image' ) );
    $twig->addFunction( new Timber\Twig_Function( 'get_image_srcset_sizes', 'get_image_srcset_sizes' ) );
    $twig->addFilter( new Twig_SimpleFilter('get_responsive_picture', 'get_responsive_picture'));

    return $twig;
} );

new StarterSite();
new Timmy\Timmy();
