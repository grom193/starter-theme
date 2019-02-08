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
	 * @param string $twig get extension.
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

add_action('wp_head','add_to_head_dns_prefetch');
add_action('wp_head','add_to_head_preload');
add_action('wp_head','add_tags_to_head');

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

add_image_size( 'resp1200', 1200, 9999 );
add_image_size( 'resp1000', 1000, 9999 );
add_image_size( 'resp800', 800, 9999 );
add_image_size( 'resp640', 640, 9999 );
add_image_size( 'resp480', 480, 9999 );
add_image_size( 'resp360', 360, 9999 );
add_image_size( 'resp300', 300, 9999 );
if (class_exists('ACF')) {
    add_filter( 'acf/load_field/type=image', function( $field ) {
        $field['return_format'] = 'id';

        return $field;
    } );
}
add_filter( 'timber/twig', function( \Twig_Environment $twig ) {
    $twig->addFunction( new Timber\Twig_Function( 'responsive_image', 'responsive_image' ) );

    return $twig;
} );
new StarterSite();
