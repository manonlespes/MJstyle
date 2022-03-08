<?php
/**
 * Timber starter-theme
 * https://github.com/timber/starter-theme
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.1
 */

/**
 * If you are installing Timber as a Composer dependency in your theme, you'll need this block
 * to load your dependencies and initialize Timber. If you are using Timber via the WordPress.org
 * plug-in, you can safely delete this block.
 */
$composer_autoload = __DIR__ . '/vendor/autoload.php';
if ( file_exists( $composer_autoload ) ) {
	require_once $composer_autoload;
	$timber = new Timber\Timber();
} 

/**
 * This ensures that Timber is loaded and available as a PHP class.
 * If not, it gives an error message to help direct developers on where to activate
 */
if ( ! class_exists( 'Timber' ) ) {


	add_action(
		'admin_notices',
		function() {
			echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php' ) ) . '</a></p></div>';
		}
	);

	add_filter(
		'template_include',
		function( $template ) {
			return get_stylesheet_directory() . '/static/no-timber.html';
		}
	);
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
		add_filter( 'timber/context', array( $this, 'add_to_context' ) );
		add_filter( 'timber/twig', array( $this, 'add_to_twig' ) );
		add_action( 'init', array( $this, 'register_post_types' ) );
		add_action( 'init', array( $this, 'register_taxonomies' ) );
		add_action('wp_enqueue_scripts', array($this, 'enqueue_styles_and_scripts'));
		add_action('after_setup_theme', array($this, 'register_block_styles')); /* avoid too many classes and to overload the style with classes */
		parent::__construct();
	}
	/** This is where you can register custom post types. */
	public function register_post_types() {

	}
	/** This is where you can register custom taxonomies. */
	public function register_taxonomies() {

	}

	public function enqueue_styles_and_scripts() {
		/* var_dump(get_stylesheet_directory_uri().'/static/dist/main.css'); */
		wp_dequeue_style('wp-block-library'); /* delete the defaut style of Gutenberg plugin */
		wp_enqueue_style('project', get_stylesheet_directory_uri().'/static/dist/main.css');

	}

	public function topcat_lite_scripts(){
		/* addition of the social media icons to my theme */
		wp_enqueue_style( 'topcat-lite-fontawesome', get_template_directory_uri() . '/static/scss/fontawesome/font-awesome.scss' );
	}

	/* addition of custom style in Gutenberg blocks/components */
	public function register_block_styles (){
		register_block_style('core/button', [
			'name'=> 'as-link-button',
			'label'=> __('MJ Bouton')
		]); 
		register_block_style('core/quote', [
			'name'=>'quote-of-the-day',
			'label'=>__('MJ citation'), 
			'is_default' => true
		]);

		register_block_style('core/separator', [
			'name'=>'personnalised-separator',
			'label'=>__('MJ séparateur'), 
			'is_default' => true
		]);

	}

	

	/** This is where you add some context
	 *
	 * @param string $context context['this'] Being the Twig's {{ this }}.
	 */
	public function add_to_context( $context ) {
		/* $context['foo']   = 'bar'; */
		$context['stuff'] = 'I am a value set in your functions.php file';
		$context['notes'] = 'These values are available everytime you call Timber::context();';
		$context['menu']  = new Timber\Menu('Primary Menu');/* rajouter ici éventuellement le slug de mon menu */
		$context['site']  = $this;
		$custom_logo_url = wp_get_attachment_image_url( get_theme_mod( 'custom_logo' ), 'full' );
		$context['custom_logo_url'] = $custom_logo_url;   
		$context['footer_menu'] = new Timber\Menu('Footer Menu');
		$context['social_menu'] = new Timber\Menu('Social Menu');
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
			'html5',
			array(
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
			'post-formats',
			array(
				'aside',
				'image',
				'video',
				'quote',
				'link',
				'gallery',
				'audio',
			)
		);

		register_nav_menus([
			"primary_menu" => "Menu principal",
			"footer_menu" => "Menu du pied de page",
			"social_menu" => _("Menu liens icons")
		]);
	

		add_theme_support( 'menus' );


		/* change the color of the text in the header and addition of an image in the header*/
		$defaults = array(
			'default-image'          => false,
			'random-default'         => false,
			'width'                  => 0,
			'height'                 => 0,
			'flex-height'            => false,
			'flex-width'             => false,
			'default-text-color'     => '',
			'header-text'            => true,
			'uploads'                => false,
			'wp-head-callback'       => '',
			'admin-head-callback'    => '',
			'admin-preview-callback' => '',
			'video'                  => false,
			'video-active-callback'  => 'is_front_page',
		);
		add_theme_support( 'custom-header', $defaults ); 

		add_theme_support( 'custom-logo', array(
			'height'               => 800,
			'width'                => 800,
			'flex-height'          => true,
			'flex-width'           => true,
			'header-text'          => array( 'site-title', 'site-description' ),
			'unlink-homepage-logo' => true,
		) );

		
	}

	/** This Would return 'foo bar!'.
	 *
	 * @param string $text being 'foo', then returned 'foo bar!'.
	 */
	/* public function myfoo( $text ) {
		$text .= ' bar!';
		return $text;
	} */

	public function social_menu(){
		if ( has_nav_menu( 'social' ) ) {
			wp_nav_menu(
				array(
					'container'       => 'div',
					'container_id'    => 'menu-social',
					'container_class' => 'menu-social',
					'menu_id'         => 'menu-social-items',
					'menu_class'      => 'menu-items',
					'depth'           => 1,
					'link_before'     => '<span class="screen-reader-text">',
					'link_after'      => '</span>',
					'fallback_cb'     => '',
				)
			);
		}
	}

	/** This is where you can add your own functions to twig.
	 *
	 * @param string $twig get extension.
	 */
	public function add_to_twig( $twig ) {
		$twig->addExtension( new Twig\Extension\StringLoaderExtension() );
		$twig->addFilter( new Twig\TwigFilter( 'social_menu', array( $this, 'social_menu' ) ) );
		return $twig;
	}

}

new StarterSite();

