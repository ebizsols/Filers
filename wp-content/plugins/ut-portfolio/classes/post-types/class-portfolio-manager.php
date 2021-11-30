<?php

/*
 * Portfolio Management by United Themes
 * http://unitedthemes.com/
 */
		
if ( ! defined( 'ABSPATH' ) ) exit;

class UT_Portfolio_Manager {
	
    private $dir;
	private $file;
	private $assets_dir;
	private $assets_url;
	private $token;

	public function __construct( $file ) {
		
        $this->dir = dirname( $file );
		$this->file = $file;
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $file ) ) );
		$this->token = 'portfolio-manager';

		// Regsiter post type
		add_action( 'init' , array( &$this , 'register_post_type' ) );

		if ( is_admin() ) {

			// Handle custom fields for post
			add_action( 'admin_menu', array( &$this, 'meta_box_setup' ), 20 );
			add_action( 'save_post', array( &$this, 'meta_box_save' ) );	

			// Modify text in main title text box
			add_filter( 'enter_title_here', array( &$this, 'enter_title_here' ) );

			// Display custom update messages for posts edits
			add_filter( 'post_updated_messages', array( &$this, 'updated_messages' ) );

			// Handle post columns
			add_filter( 'manage_edit-' . $this->token . '_columns', array( &$this, 'register_custom_column_headings' ), 10, 1 );
			add_action( 'manage_posts_custom_column' , array( &$this, 'register_custom_columns' ), 10, 2 );
						
			// add a few custom styles for post page
			add_action('admin_print_styles-post.php', array( &$this, 'register_settings_styles' ) );
            add_action('admin_print_styles-post-new.php', array( &$this, 'register_settings_styles' ) );  
			
		}

	}	
	
	public function register_settings_styles() {
		
        global $post, $post_ID;        
        
        if( get_post_type($post_ID) == $this->token ) {
        
		/* core style files */
		wp_enqueue_style('wp-color-picker');
		
		/* core script files */
		wp_enqueue_script('jquery-ui');
		wp_enqueue_script('jquery-ui-widget');
		wp_enqueue_script('jquery-ui-mouse');
		wp_enqueue_script('jquery-ui-sortable');
		  
		/* custom css files */
		wp_enqueue_style('ut-manager-styles', $this->assets_url . 'css/admin/ut.portfolio.manager.css');
		
		/* custom js files */
        wp_enqueue_script('ut-manager-scripts', $this->assets_url . 'js/admin/ut.portfolio.manager.js' , array( 'wp-color-picker' ) );
        
        }
	
	}
	
	public function register_post_type() {
 
		$labels = array(
			'name' => _x( 'Showcase', 'post type general name' , 'ut_portfolio_lang' ),
			'singular_name' => _x( 'Showcase', 'post type singular name' , 'ut_portfolio_lang' ),
			'add_new' => _x( 'Add New UT Showcase', $this->token , 'ut_portfolio_lang' ),
			'add_new_item' => sprintf( __( 'Add New %s' , 'ut_portfolio_lang' ), __( 'UT Showcase' , 'ut_portfolio_lang' ) ),
			'edit_item' => sprintf( __( 'Edit %s' , 'ut_portfolio_lang' ), __( 'Showcase' , 'ut_portfolio_lang' ) ),
			'new_item' => sprintf( __( 'New %s' , 'ut_portfolio_lang' ), __( 'Showcase' , 'ut_portfolio_lang' ) ),
			'all_items' => __( 'Showcase' , 'ut_portfolio_lang' ),
			'view_item' => sprintf( __( 'View %s' , 'ut_portfolio_lang' ), __( 'Showcase' , 'ut_portfolio_lang' ) ),
			'search_items' => sprintf( __( 'Search %a' , 'ut_portfolio_lang' ), __( 'Showcases' , 'ut_portfolio_lang' ) ),
			'not_found' =>  sprintf( __( 'No %s Found' , 'ut_portfolio_lang' ), __( 'Showcases' , 'ut_portfolio_lang' ) ),
			'not_found_in_trash' => sprintf( __( 'No %s Found In Trash' , 'ut_portfolio_lang' ), __( 'Posts' , 'ut_portfolio_lang' ) ),
			'parent_item_colon' => '',
			'menu_name' => __( 'Showcase' , 'ut_portfolio_lang' )
		);
		
		$args = array(
			'labels' => $labels,
			'public' => false,
			'publicly_queryable' => true,
			'exclude_from_search' => true,
			'show_ui' => true,
			'show_in_menu' => 'edit.php?post_type=portfolio',
			'show_in_nav_menus' => false,
			'query_var' => false,
			'rewrite' => true,
			'capability_type' => 'post',
			'has_archive' => true,
			'hierarchical' => false,
			'supports' => array( 'title' ),
		);

		register_post_type( $this->token, $args );
	}

	
    public function register_custom_column_headings( $defaults ) {
		
		$new_columns = array(
			'showcase-shortcode' => __( 'Shortcode' , 'ut_portfolio_lang' )
		);
		
		$last_item = '';

		if ( isset( $defaults['date'] ) ) { unset( $defaults['date'] ); }

		if ( count( $defaults ) > 2 ) { 
			$last_item = array_slice( $defaults, -1 );

			array_pop( $defaults );
		}
		$defaults = array_merge( $defaults, $new_columns );
		
		if ( $last_item != '' ) {
			foreach ( $last_item as $k => $v ) {
				$defaults[$k] = $v;
				break;
			}
		}

		return $defaults;
	}

	public function register_custom_columns( $column_name, $id ) {
		
		global $post, $post_ID;		
                
		switch ( $column_name ) {

			case 'showcase-shortcode':
				echo '[ut_showcase id="' . $post->ID . '" name="' . esc_attr( get_the_title( $post->title ) ) . '"]';
			break;

			default:
			break;
		}

	}

	public function updated_messages( $messages ) {
	  
      global $post, $post_ID;

	  $messages[$this->token] = array(
	    0 => '', // Unused. Messages start at index 1.
	    1 => sprintf( __( 'Post updated. %sView post%s.' , 'ut_portfolio_lang' ), '<a href="' . esc_url( get_permalink( $post_ID ) ) . '">', '</a>' ),
	    2 => __( 'Custom field updated.' , 'ut_portfolio_lang' ),
	    3 => __( 'Custom field deleted.' , 'ut_portfolio_lang' ),
	    4 => __( 'Post updated.' , 'ut_portfolio_lang' ),
		
	    /* translators: %s: date and time of the revision */
	    5 => isset($_GET['revision']) ? sprintf( __( 'Post restored to revision from %s.' , 'ut_portfolio_lang' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
	    6 => sprintf( __( 'Post published. %sView post%s.' , 'ut_portfolio_lang' ), '<a href="' . esc_url( get_permalink( $post_ID ) ) . '">', '</a>' ),
	    7 => __( 'Post saved.' , 'ut_portfolio_lang' ),
	    8 => sprintf( __( 'Post submitted. %sPreview post%s.' , 'ut_portfolio_lang' ), '<a target="_blank" href="' . esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) . '">', '</a>' ),
	    9 => sprintf( __( 'Post scheduled for: %1$s. %2$sPreview post%3$s.' , 'ut_portfolio_lang' ), '<strong>' . date_i18n( __( 'M j, Y @ G:i' , 'ut_portfolio_lang' ), strtotime( $post->post_date ) ) . '</strong>', '<a target="_blank" href="' . esc_url( get_permalink( $post_ID ) ) . '">', '</a>' ),
	    10 => sprintf( __( 'Post draft updated. %sPreview post%s.' , 'ut_portfolio_lang' ), '<a target="_blank" href="' . esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) . '">', '</a>' ),
	  );

	  return $messages;
	}

	public function meta_box_setup() {		
		add_meta_box( 'ut-portfolio-manager', __( 'United Themes - Portfolio Showcase Settings' , 'ut_portfolio_lang' ) . ' v' . UT_PORTFOLIO_VERSION , array( &$this, 'meta_box_content' ), $this->token, 'normal', 'high' );
		add_meta_box( 'ut-portfolio-manager-info', __( 'Usage' , 'ut_portfolio_lang' ), array( &$this, 'meta_box_content_info' ), $this->token, 'side', 'high' );
	}
	
	public function checked_array( $current , $haystack ) {
								
		if( is_array($haystack) && isset($haystack[$current]) ) {
			$current = $haystack = 1;
			return checked( $haystack, $current , false );
		}

		
		
	}
    
    public function selected_array( $current , $key , $haystack ) {
												
		if( is_array($haystack) && isset($haystack[$key]) && $haystack[$key] == $current) {
			$current = $haystack = 1;
			return selected( $haystack, $current , false );
		}
		
	}
    
    public function validate_value( $value , $key , $default = '' ) {
								
        if( isset($value[$key]) ) {
        
            return esc_attr($value[$key]);
        
        } else {
        
            return $default;
        
        }
		
	}
	
	public function order_tax_categories( $taxonomies , $sortarray ) {
	
		$ordered = array();
		$counter = 1;
		
		if( is_array( $sortarray ) ) {
		
			foreach( $sortarray as $sortkey => $sortvalue) {
				
				foreach( $taxonomies as $taxkey => $taxvalue ) {
					
					if( $sortkey == $taxonomies[$taxkey]['term_id'] ) {
						
						$ordered[$counter] = $taxonomies[$taxkey];
						unset( $taxonomies[$taxkey] );
						
					} 
					
				}
				
				$counter ++;
				
			}
			
			return array_merge( $ordered , $taxonomies);
		
		} else {
		
			return $taxonomies;
		
		}
		
	}
	
	public function searchForId($id, $array) {
	   foreach ($array as $key => $val) {
		   if ($val['term_id'] === $id) {
			   return $key;
		   }
	   }
	   return null;
	}
	
	public function meta_box_content() {
		
		global $post_id;
		
		$fields = get_post_custom( $post_id );				
		$field_data = $this->get_custom_fields_settings();
		
		$html = '';
		
		$html .= '<input type="hidden" name="' . $this->token . '_nonce" id="' . $this->token . '_nonce" value="' . wp_create_nonce( plugin_basename( $this->dir ) ) . '" />';
		
		if ( 0 < count( $field_data ) ) {
			
            $html .= '<table class="form-table">' . "\n";
			$html .= '<tbody>' . "\n";
			$html .= '<tr valign="top"><td class="clearfix">' . "\n";
            
			foreach ( $field_data as $k => $v ) {
				
				$data = $v['default'];
								
				if ( isset( $fields[$k] ) && isset( $fields[$k][0] ) ) {
					$data = $fields[$k][0];
				}
				
                if( $v['type'] == 'showcase_info' ) {
                    
                    $html .= '<div class="ut-admin-info-box">';
                    
                        $html .= '<h2 class="ut-section-title">' . $v['name'] . '</h2>';
                        $html .= '<div class="ut-section-panel">';
                            $html .= '<textarea wrap="off" readonly="readonly" class="ut-shortcode-code">[ut_showcase id="' . $post_id . '" name="' . esc_attr( get_the_title( $post_id ) ) . '"]</textarea>';
                            $html .= $v['description'];
                        $html .= '</div>';
                    
                    $html .= '</div>';
                    
                    $html .= '<div class="clear"></div>';                    
                    $html .= '<div id="ut-admin-big-box-wrap">';
                
                } elseif( $v['type'] == 'taxonomy' ) {
					
					$taxonomies = get_terms( 'portfolio-category' , array( 'hide_empty' => false ) );
					$taxonomies = json_decode(json_encode($taxonomies), true);
					
					$html .= '<h2 class="ut-section-title">' . $v['name'] . '</h2>';					
					$html .= '<div class="ut-section-panel ut-sortable-tax">';
					
					$html .= '<ul id="ut-sortable-tax">';
										
					/* loop through taxonomy */
					if( is_array( $taxonomies ) && !empty( $taxonomies ) ) {
								
						$data = maybe_unserialize( $data );
						$taxonomies = $this->order_tax_categories( $taxonomies , $data );
												
						foreach ($taxonomies as $key => $item){
							
							$html .= '<li>';
								$html .= '<span class="ut-handle"><i class="fa fa-arrows-v"></i></span>';
								$html .= '<div class="ut-checkbox-single"><input name="' . esc_attr( $k ) . '[' . $taxonomies[$key]['term_id'] . ']" type="checkbox" id="' . esc_attr( $k ) . '_' . $key . '" ' . $this->checked_array( $taxonomies[$key]['term_id'] , $data ) . ' /> <label for="' . esc_attr( $k ) . '_' . $key . '"><span>' . $taxonomies[$key]['name'] . '</span></label></div>';								
								$html .= '<div class="clear"></div>';
							$html .= '</li>';							
						
						}
					 
					} else { 
						
						echo '<div class="alert">'.__( 'No Portfolio Categories created yet!', 'ut_portfolio_lang' ).'</div>'; 
					
					}
					
					$html .= '</ul>';
					$html .= '<span class="description">' . $v['description'] . '</span>';
					$html .= '</div>' . "\n";
					
				} elseif( $v['type'] == 'showcase_options' ) {
					
                    $data = maybe_unserialize( $data );
                    
                    $html .= '<section class="ut-option-section" id="' . esc_attr( $k ) . '">';
                    $html .= '<h2 class="ut-section-title">' . $v['name'] . '</h2>';
					$html .= '<div class="ut-section-panel">';					
					
                    /* start wp table */
                    $html .= '<table class="form-table"><tbody>';
                                        
                    /* transition effect */
                    $html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Animation Effect' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><select autocomplete="off" name="' . esc_attr( $k ) . '[animation]" id="' . esc_attr( $k ) . '_animation">';
                            $html .= '<option value="fade" ' . $this->selected_array( 'fade' , 'animation' , $data ) . '>' . __('Fade' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="slide" ' . $this->selected_array( 'slide' , 'animation' , $data ) . '>' . __('Slide' , 'ut_portfolio_lang') . '</option>';
                        $html .= '</select>';
                        $html .= '<p class="description">' . __('Select your animation type.' , 'ut_portfolio_lang') . '</p></td>';
                    $html .= '</tr>';

                    /* thumbnail navigation */
					$html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Thumbnail Navigation' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><div class="ut-checkbox"><input name="' . esc_attr( $k ) . '[display_thumbnail_navigation]" type="checkbox" id="' . esc_attr( $k ) . '_thumbnail_navigation" ' . $this->checked_array( 'display_thumbnail_navigation' , $data ) . ' /> <label for="' . esc_attr( $k ) . '_thumbnail_navigation"></label></div>';
					    $html .= '<p class="description">' . __('Show / hide thumbnail navigation beneath slider.' , 'ut_portfolio_lang') . '</p></td>';
					$html .= '</tr>';                    
                    
                    /* directionNav */
					$html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Slider Navigation' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><div class="ut-checkbox"><input name="' . esc_attr( $k ) . '[directionNav]" type="checkbox" id="' . esc_attr( $k ) . '_directionNav" ' . $this->checked_array( 'directionNav' , $data ) . ' /> <label for="' . esc_attr( $k ) . '_directionNav"></label></div>';
					    $html .= '<p class="description">' . __('Create navigation for paging control of each slide?.' , 'ut_portfolio_lang') . '</p></td>';
                    $html .= '</tr>';
                    
                    /* smoothHeight */
                    $html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Smooth Height' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><div class="ut-checkbox"><input name="' . esc_attr( $k ) . '[smoothHeight]" type="checkbox" id="' . esc_attr( $k ) . '_smoothHeight" ' . $this->checked_array( 'smoothHeight' , $data ) . ' /> <label for="' . esc_attr( $k ) . '_smoothHeight"></label></div>';
					    $html .= '<p class="description">' . __('Allow height of the slider to animate smoothly in horizontal mode.' , 'ut_portfolio_lang') . '</p></td>';
					$html .= '</tr>';
                    
                    /* slideshowSpeed */
                    $html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Slideshow Speed' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><input type="text" value="' . $this->validate_value( $data , 'slideshowSpeed' ) . '" class="regular-text" name="' . esc_attr( $k ) . '[slideshowSpeed]" id="' . esc_attr( $k ) . '_slideshowSpeed" /> <label for="' . esc_attr( $k ) . '_slideshowSpeed"></label>';
					    $html .= '<p class="description">' . __('Set the speed of the slideshow cycling, in milliseconds. (default : 7000) ' , 'ut_portfolio_lang') . '</p></td>';
                    $html .= '</tr>';
                    
                    /* animationSpeed */
                    $html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Animation Speed' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><input type="text" value="' . $this->validate_value( $data , 'animationSpeed' ) . '" class="regular-text" name="' . esc_attr( $k ) . '[animationSpeed]" id="' . esc_attr( $k ) . '_animationSpeed" /> <label for="' . esc_attr( $k ) . '_animationSpeed"></label>';
					    $html .= '<p class="description">' . __('Set the speed of the slideshow cycling, in milliseconds. (default : 600)' , 'ut_portfolio_lang') . '</p></td>';
                    $html .= '</tr>';
                    
                    /* end wp table */
                    $html .= '</tbody></table>';
                    
                    /* end panel & section */ 
                    $html .= '</div></section>' . "\n";
					
                } elseif( $v['type'] == 'carousel_options' ) {
                    
                    $data = maybe_unserialize( $data );                    
                                            
                    $html .= '<section class="ut-option-section" id="' . esc_attr( $k ) . '">';
                    $html .= '<h2 class="ut-section-title">' . $v['name'] . '</h2>';
					$html .= '<div class="ut-section-panel">';					
					
                    /* start wp table */
                    $html .= '<table class="form-table"><tbody>';
                    
                    /* column set */
                    $html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Overall Column Layout' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><input type="text" maxlength="1" value="' . $this->validate_value( $data , 'columns' ) . '" class="regular-text" name="' . esc_attr( $k ) . '[columns]" id="' . esc_attr( $k ) . '_columns" /> <label for="' . esc_attr( $k ) . '_columns"></label>';
					    $html .= '<p class="description">' . __('Images in a row (max value 9).' , 'ut_portfolio_lang') . '</p></td>';
                    $html .= '</tr>';                    
                    
					/* style */
                    $html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Portfolio Visual Style' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><select autocomplete="off" name="' . esc_attr( $k ) . '[style]" id="' . esc_attr( $k ) . '_style">';
                            $html .= '<option value="style_one" ' . $this->selected_array( 'style_one' , 'style' , $data ) . '>' . __('Style 1 ( only images )' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="style_two" ' . $this->selected_array( 'style_two' , 'style' , $data ) . '>' . __('Style 2 ( images with title ) ' , 'ut_portfolio_lang') . '</option>';
                            // $html .= '<option value="style_three" ' . $this->selected_array( 'style_three' , 'style' , $data ) . '>' . __('Style 3 ( image tilt ) ' , 'ut_portfolio_lang') . '</option>';
                        $html .= '</select>';
                        $html .= '<p class="description">' . __('Select your desired portfolio style.' , 'ut_portfolio_lang') . '</p></td>';
                    $html .= '</tr>';

                    /* Caption Visibility  */
                    $html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Portfolio Caption Visibility' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><select autocomplete="off" name="' . esc_attr( $k ) . '[caption_visibility]" id="' . esc_attr( $k ) . '_caption_visibility">';
                            $html .= '<option value="on_hover" ' . $this->selected_array( 'on_hover' , 'caption_visibility' , $data ) . '>' . __('On Hover' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="permanent" ' . $this->selected_array( 'permanent' , 'caption_visibility' , $data ) . '>' . __('Permanent' , 'ut_portfolio_lang') . '</option>';
                        $html .= '</select>';
                        $html .= '<p class="description">' . __('Select your desired portfolio caption visibility.' , 'ut_portfolio_lang') . '</p></td>';
                    $html .= '</tr>';

	                /* caption content */
	                $html .= '<tr valign="top" class="ut-settings-row">';
	                    $html .= '<td class="ut-label-cell">' . __('Portfolio Hover Caption Content' , 'ut_portfolio_lang') . '</td>';
	                        $html .= '<td>';

		                    /* Caption Style */
			                /* $html .= '<select autocomplete="off" name="' . esc_attr( $k ) . '[style_1_caption_content]" id="' . esc_attr( $k ) . '_style_1_caption_content">';
				                $html .= '<option value="title_category" ' . $this->selected_array( 'title_category' , 'style_1_caption_content' , $data ) . '>' . __('Title + Category' , 'ut_portfolio_lang') . '</option>';
				                $html .= '<option value="title_only" ' . $this->selected_array( 'title_only' , 'style_1_caption_content' , $data ) . '>' . __('Title Only' , 'ut_portfolio_lang') . '</option>';
				                $html .= '<option value="category_only" ' . $this->selected_array( 'category_only' , 'style_1_caption_content' , $data ) . '>' . __('Category Only' , 'ut_portfolio_lang') . '</option>';
				                $html .= '<option value="plus_sign" ' . $this->selected_array( 'plus_sign' , 'style_1_caption_content' , $data ) . '>' . __('Plus Sign' , 'ut_portfolio_lang') . '</option>';
				                $html .= '<option value="custom_text" ' . $this->selected_array( 'custom_text' , 'style_1_caption_content' , $data ) . '>' . __('Custom Text' , 'ut_portfolio_lang') . '</option>';
				                $html .= '<option value="none" ' . $this->selected_array( 'none' , 'style_1_caption_content' , $data ) . '>' . __('No Content' , 'ut_portfolio_lang') . '</option>';
			                $html .= '</select>'; */

			                // $html .= '<input style="margin-top:20px; display:block;" type="text" value="' . $this->validate_value( $data , 'style_1_caption_custom_text' ) . '" class="regular-text" name="' . esc_attr( $k ) . '[style_1_caption_custom_text]" id="' . esc_attr( $k ) . '_style_1_caption_custom_text" />';

			                $html .= '<select autocomplete="off" name="' . esc_attr( $k ) . '[style_2_caption_content]" id="' . esc_attr( $k ) . '_style_2_caption_content">';
				                $html .= '<option value="category_icon" ' . $this->selected_array( 'category_icon' , 'style_2_caption_content' , $data ) . '>' . __('Icon + Category' , 'ut_portfolio_lang') . '</option>';
				                $html .= '<option value="category_only" ' . $this->selected_array( 'category_only' , 'style_2_caption_content' , $data ) . '>' . __('Category Only' , 'ut_portfolio_lang') . '</option>';
				                // $html .= '<option value="plus_sign" ' . $this->selected_array( 'plus_sign' , 'style_2_caption_content' , $data ) . '>' . __('Plus Sign' , 'ut_portfolio_lang') . '</option>';
				                // $html .= '<option value="custom_text" ' . $this->selected_array( 'custom_text' , 'style_2_caption_content' , $data ) . '>' . __('Custom Text' , 'ut_portfolio_lang') . '</option>';
				                // $html .= '<option value="none" ' . $this->selected_array( 'none' , 'style_2_caption_content' , $data ) . '>' . __('No Content' , 'ut_portfolio_lang') . '</option>';
			                $html .= '</select>';

			                // $html .= '<input style="margin-top:20px; display:block;" type="text" value="' . $this->validate_value( $data , 'style_2_caption_custom_text' ) . '" class="regular-text" name="' . esc_attr( $k ) . '[style_2_caption_custom_text]" id="' . esc_attr( $k ) . '_style_2_caption_custom_text" />';

	                    $html .= '<p class="description">' . __('' , 'ut_portfolio_lang') . '</p></td>';
	                $html .= '</tr>';

                    /* image cropping */
					$html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Image Cropping' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td>';
                        
                        $html .= '<div id="ut_cropping_settings_carousel" class="ut-cropping-size">';    
                        $html .= '<label for="' . esc_attr( $k ) . '_crop_size_x">' . __('Width' , 'ut_portfolio_lang') . '</label>';
                        $html .= '<input type="text" value="' . $this->validate_value( $data , 'crop_size_x' ) . '" class="small-text code" name="' . esc_attr( $k ) . '[crop_size_x]" id="' . esc_attr( $k ) . '_crop_size_x" />';
                        $html .= '<label for="' . esc_attr( $k ) . '_crop_size_y">' . __('Height' , 'ut_portfolio_lang') . '</label>';
                        $html .= '<input type="text" value="' . $this->validate_value( $data , 'crop_size_y' ) . '" class="small-text code" name="' . esc_attr( $k ) . '[crop_size_y]" id="' . esc_attr( $k ) . '_crop_size_y" />';
                        $html .= '</div>';
                        
                        $html .= '<p class="description">' . __('Default: 1000x800' , 'ut_portfolio_lang') . '</p></td>';                        
                        
					$html .= '</tr>';
                    
                    /* end wp table */
                    $html .= '</tbody></table>';
                    
                    /* end panel & section */ 
                    $html .= '</div></section>' . "\n";

                } elseif( $v['type'] == 'react_carousel_options' ) {

                    $data = maybe_unserialize( $data );

                    $html .= '<section class="ut-option-section" id="' . esc_attr( $k ) . '">';
                    $html .= '<h2 class="ut-section-title">';
                        $html .= $v['name'];
	                    $html .= '<br /><span style="font-size: 12px">' . __('This showcase requires portrait images with a resolution of at least 750x1125 px or higher with same aspect ratio.', 'ut_portfolio_lang') . '</span>';
                    $html .= '</h2>';
	                $html .= '<div class="ut-section-panel">';

                    /* start wp table */
                    $html .= '<table class="form-table"><tbody>';

                    /* general settings */
                    $html .= '<tr valign="top" class="ut-settings-row ">';

                        $html .= '<td class="ut-label-cell">' . __('Rotate Carousel?' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell">';
                            $html .= '<select autocomplete="off" name="' . esc_attr( $k ) . '[rotate]" id="' . esc_attr( $k ) . '_rotate">';
                                $html .= '<option value="off" ' . $this->selected_array( 'off' , 'rotate' , $data ) . '>' . __('no, thanks!' , 'ut_portfolio_lang') . '</option>';
                                $html .= '<option value="on" ' . $this->selected_array( 'on' , 'rotate' , $data ) . '>' . __('yes, please!' , 'ut_portfolio_lang') . '</option>';
                            $html .= '</select>';
                            $html .= '<p class="description">' . __('Rotate Carousel. Note that this effect can cause overflow issues. Please check outer row / section overflow settings.' , 'ut_portfolio_lang') . '</p>';
                        $html .= '</td>';

                        $html .= '<td class="ut-label-cell">' . __('Activate Carousel Navigation?' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell">';
                            $html .= '<select autocomplete="off" name="' . esc_attr( $k ) . '[navigation]" id="' . esc_attr( $k ) . '_navigation">';
                                $html .= '<option value="on" ' . $this->selected_array( 'on' , 'navigation' , $data ) . '>' . __('yes, please!' , 'ut_portfolio_lang') . '</option>';
                                $html .= '<option value="off" ' . $this->selected_array( 'off' , 'navigation' , $data ) . '>' . __('no, thanks!' , 'ut_portfolio_lang') . '</option>';
                            $html .= '</select>';
                            $html .= '<p class="description">' . __('An arrow navigation below the carousel. If rotate carousel is turned on, the alignment of this navigation is right.' , 'ut_portfolio_lang') . '</p>';
                        $html .= '</td>';

                    $html .= '</tr>';

                    /* autoplay settings */
                    $html .= '<tr valign="top" class="ut-settings-row ">';

                        $html .= '<td class="ut-label-cell">' . __('Activate Autoplay?' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell">';
                            $html .= '<select autocomplete="off" name="' . esc_attr( $k ) . '[autoplay]" id="' . esc_attr( $k ) . '_autoplay">';
                                $html .= '<option value="on" ' . $this->selected_array( 'on' , 'autoplay' , $data ) . '>' . __('yes, please!' , 'ut_portfolio_lang') . '</option>';
                                $html .= '<option value="off" ' . $this->selected_array( 'off' , 'autoplay' , $data ) . '>' . __('no, thanks!' , 'ut_portfolio_lang') . '</option>';
                            $html .= '</select>';
                            $html .= '<p class="description">' . __('Automatically advances to the next cell. Auto-playing will pause when mouse is hovered over, and resume when mouse is hovered off. ' , 'ut_portfolio_lang') . '</p>';
                        $html .= '</td>';

                        $html .= '<td class="ut-label-cell">' . __('Autoplay Timer' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell">';
                            $html .= '<input type="text" value="' . $this->validate_value( $data , 'autoplay_timer' ) . '" class="regular-text" name="' . esc_attr( $k ) . '[autoplay_timer]" id="' . esc_attr( $k ) . '_autoplay_timer" /> <label for="' . esc_attr( $k ) . '_autoplay_timer"></label>';
                            $html .= '<p class="description">' . __('Advance cells every {Number} milliseconds. Default: 3000' , 'ut_portfolio_lang') . '</p>';
                        $html .= '</td>';

                    $html .= '</tr>';

                    /* preloader settings */
                    $html .= '<tr valign="top" class="ut-settings-row ">';

                        $html .= '<td class="ut-label-cell">' . __('Activate Preloader?' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell">';
                            $html .= '<select autocomplete="off" name="' . esc_attr( $k ) . '[preloader]" id="' . esc_attr( $k ) . '_preloader">';
                                $html .= '<option value="off" ' . $this->selected_array( 'off' , 'preloader' , $data ) . '>' . __('no, thanks!' , 'ut_portfolio_lang') . '</option>';
                                $html .= '<option value="on" ' . $this->selected_array( 'on' , 'preloader' , $data ) . '>' . __('yes, please!' , 'ut_portfolio_lang') . '</option>';
                            $html .= '</select>';
                            $html .= '<p class="description">' . __('Display a percentage count up until react slider is loaded. ' , 'ut_portfolio_lang') . '</p>';
                        $html .= '</td>';

                        $html .= '<td class="ut-label-cell">' . __('Preloader Color' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell">';
                            $html .= '<input value="' . $this->validate_value( $data , 'preloader_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[preloader_color]" id="' . esc_attr( $k ) . '_preloader_color" />';
                            $html .= '<p class="description">' . __('The text color of the preloader.' , 'ut_portfolio_lang') . '</p>';
                        $html .= '</td>';

                    $html .= '</tr>';

                    /* Carousel Cell  */
                    $html .= '<tr valign="top" class="ut-settings-row">';

                        $html.= '<td class="ut-headline-cell" colspan="4"><h3>' . __('Carousel Cell Content and Design' , 'ut_portfolio_lang') . '</h3></td>';

                    $html .= '</tr>';

                    $html .= '<tr valign="top" class="ut-settings-row">';

                        $html .= '<td class="ut-label-cell">' . __('Show Number Counter?' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell"><select autocomplete="off" name="' . esc_attr( $k ) . '[number_counter]" id="' . esc_attr( $k ) . '_number_counter">';
                            $html .= '<option value="on" ' . $this->selected_array( 'on' , 'number_counter' , $data ) . '>' . __('yes, please!' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="off" ' . $this->selected_array( 'off' , 'number_counter' , $data ) . '>' . __('no, thanks!' , 'ut_portfolio_lang') . '</option>';
                        $html .= '</select>';
                        $html .= '<p class="description">' . __('Display a counter above the portfolio image.' , 'ut_portfolio_lang') . '</p>';
                        $html .= '</td>';

                        $html .= '<td class="ut-label-cell">' . __('Activate Number Counter Stroke?' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell"><select autocomplete="off" name="' . esc_attr( $k ) . '[number_counter_stroke]" id="' . esc_attr( $k ) . '_number_counter_stroke">';
                            $html .= '<option value="on" ' . $this->selected_array( 'on' , 'number_counter_stroke' , $data ) . '>' . __('yes, please!' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="off" ' . $this->selected_array( 'off' , 'number_counter_stroke' , $data ) . '>' . __('no, thanks!' , 'ut_portfolio_lang') . '</option>';
                        $html .= '</select>';
                        $html .= '<p class="description">' . __('Display numbers with a stroke. Note that this will apply a transparent color to the font by default. You can change the color further down below or inside the portfolio settings.' , 'ut_portfolio_lang') . '</p>';
                        $html .= '</td>';

                    $html .= '</tr>';

                    $html .= '<tr valign="top" class="ut-settings-row">';

                        $html .= '<td class="ut-label-cell">' . __('Show Category?' , 'ut_portfolio_lang') . '</td>';
                            $html .= '<td class="ut-option-cell"><select autocomplete="off" name="' . esc_attr( $k ) . '[category]" id="' . esc_attr( $k ) . '_category">';
                                $html .= '<option value="on" ' . $this->selected_array( 'on' , 'category' , $data ) . '>' . __('yes, please!' , 'ut_portfolio_lang') . '</option>';
                                $html .= '<option value="off" ' . $this->selected_array( 'off' , 'category' , $data ) . '>' . __('no, thanks!' , 'ut_portfolio_lang') . '</option>';
                            $html .= '</select>';
                            $html .= '<p class="description">' . __('Display the category below portfolio image.' , 'ut_portfolio_lang') . '</p>';
                        $html .= '</td>';

                        $html .= '<td class="ut-label-cell">' . __('Show Background Titles?' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell"><select autocomplete="off" name="' . esc_attr( $k ) . '[background_titles]" id="' . esc_attr( $k ) . '_background_titles">';
                            $html .= '<option value="on" ' . $this->selected_array( 'on' , 'background_titles' , $data ) . '>' . __('yes, please!' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="off" ' . $this->selected_array( 'off' , 'background_titles' , $data ) . '>' . __('no, thanks!' , 'ut_portfolio_lang') . '</option>';
                        $html .= '</select>';
                        $html .= '<p class="description">' . __('Display titles in background?' , 'ut_portfolio_lang') . '</p>';
                        $html .= '</td>';

                    $html .= '</tr>';

                    $html .= '<tr valign="top" class="ut-settings-row">';

                        $html .= '<td class="ut-label-cell">' . __('Activate Image Shadow?' , 'ut_portfolio_lang') . '</td>';
                            $html .= '<td class="ut-option-cell"><select autocomplete="off" name="' . esc_attr( $k ) . '[shadow]" id="' . esc_attr( $k ) . '_shadow">';
                                $html .= '<option value="on" ' . $this->selected_array( 'on' , 'shadow' , $data ) . '>' . __('yes, please!' , 'ut_portfolio_lang') . '</option>';
                                $html .= '<option value="off" ' . $this->selected_array( 'off' , 'shadow' , $data ) . '>' . __('no, thanks!' , 'ut_portfolio_lang') . '</option>';
                            $html .= '</select>';
                            $html .= '<p class="description">' . __('Apply a shadow to the portfolio image for more visual depth.' , 'ut_portfolio_lang') . '</p>';
                        $html .= '</td>';

                    $html .= '<td class="ut-label-cell">' . __('Shadow Color' , 'ut_portfolio_lang') . '</td>';
                    $html .= '<td class="ut-option-cell"><input value="' . $this->validate_value( $data , 'shadow_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[shadow_color]" id="' . esc_attr( $k ) . '_shadow_color" />';
                    $html .= '<p class="description">' . __('Optional Shadow Color.' , 'ut_portfolio_lang') . '</p></td>';

                    $html .= '</tr>';

                    $html .= '<tr valign="top" class="ut-settings-row">';

                        $html.= '<td class="ut-headline-cell" colspan="4"><h3>' . __('Title Settings' , 'ut_portfolio_lang') . '</h3></td>';

                    $html .= '</tr>';

                    /* Activate Line Draw */
                    $html .= '<tr valign="top" class="ut-settings-row ">';

                        $html .= '<td class="ut-label-cell">' . __('Activate Title Line Draw?' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell"><select autocomplete="off" name="' . esc_attr( $k ) . '[line_draw]" id="' . esc_attr( $k ) . '_line_draw">';
                            $html .= '<option value="off" ' . $this->selected_array( 'off' , 'line_draw' , $data ) . '>' . __('no, thanks!' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="on" ' . $this->selected_array( 'on' , 'line_draw' , $data ) . '>' . __('yes, please!' , 'ut_portfolio_lang') . '</option>';
                        $html .= '</select>';
                        $html .= '<p class="description">' . __('Activate Line Draw for Portfolio Titles. Should be used in combination with transparent title color.' , 'ut_portfolio_lang') . '</p>';
                        $html .= '</td>';

                        $html .= '<td class="ut-label-cell">' . __('Title Line Draw Width?' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell"><select autocomplete="off" name="' . esc_attr( $k ) . '[line_draw_width]" id="' . esc_attr( $k ) . '_line_draw_width">';
                            $html .= '<option value="thin" ' . $this->selected_array( 'thin' , 'line_draw_width' , $data ) . '>' . __('thin' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="thicker" ' . $this->selected_array( 'thicker' , 'line_draw_width' , $data ) . '>' . __('thicker' , 'ut_portfolio_lang') . '</option>';
                        $html .= '</select>';
                        $html .= '<p class="description">' . __('Line Draw Width for Portfolio Titles.' , 'ut_portfolio_lang') . '</p>';
                        $html .= '</td>';



                    $html .= '</tr>';

                    /* Activate Blur */
                    $html .= '<tr valign="top" class="ut-settings-row">';

                        $html .= '<td class="ut-label-cell">' . __('Activate Title Shadow?' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell"><select autocomplete="off" name="' . esc_attr( $k ) . '[title_shadow]" id="' . esc_attr( $k ) . '_title_shadow">';
                            $html .= '<option value="on" ' . $this->selected_array( 'on' , 'title_shadow' , $data ) . '>' . __('yes, please!' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="off" ' . $this->selected_array( 'off' , 'title_shadow' , $data ) . '>' . __('no, thanks!' , 'ut_portfolio_lang') . '</option>';
                        $html .= '</select>';
                        $html .= '<p class="description">' . __('Add a light shadow.' , 'ut_portfolio_lang') . '</p>';
                        $html .= '</td>';

                        $html .= '<td class="ut-label-cell">' . __('Activate Title Blur?' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell"><select autocomplete="off" name="' . esc_attr( $k ) . '[blur]" id="' . esc_attr( $k ) . '_blur">';
                            $html .= '<option value="off" ' . $this->selected_array( 'off' , 'blur' , $data ) . '>' . __('no, thanks!' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="on" ' . $this->selected_array( 'on' , 'blur' , $data ) . '>' . __('yes, please!' , 'ut_portfolio_lang') . '</option>';
                        $html .= '</select>';
                        $html .= '<p class="description">' . __('Titles on left and right located portfolio slides are slightly blurred.' , 'ut_portfolio_lang') . '</p>';
                        $html .= '</td>';

                    $html .= '</tr>';

                    $html .= '<tr valign="top" class="ut-settings-row">';
                        $html.= '<td class="ut-headline-cell" colspan="4"><h3>' . __('Title Colors' , 'ut_portfolio_lang') . '</h3></td>';
                    $html .= '</tr>';

                    /* text color */
                    $html .= '<tr valign="top" class="ut-settings-row">';

                        $html .= '<td class="ut-label-cell">' . __('Title Default Color' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell"><input value="' . $this->validate_value( $data , 'title_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[title_color]" id="' . esc_attr( $k ) . '_title_color" />';
                        $html .= '<p class="description">' . __('Title Color for all cells. Can be changed per portfolio item inside the Brooklyn metapanel below the editor.' , 'ut_portfolio_lang') . '</p></td>';

                        $html .= '<td class="ut-label-cell">' . __('Active Title Color' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell"><input value="' . $this->validate_value( $data , 'title_active_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[title_active_color]" id="' . esc_attr( $k ) . '_title_active_color" />';
                        $html .= '<p class="description">' . __('Active Color for portfolio cell in middle. Can be changed per portfolio item inside the Brooklyn metapanel below the editor.' , 'ut_portfolio_lang') . '</p></td>';

                    $html .= '</tr>';

                    /* text color */
                    $html .= '<tr valign="top" class="ut-settings-row">';

                        $html .= '<td class="ut-label-cell">' . __('Title Line Draw Color' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell"><input value="' . $this->validate_value( $data , 'title_line_draw_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[title_line_draw_color]" id="' . esc_attr( $k ) . '_title_line_draw_color" />';
                        $html .= '<p class="description">' . __('Title Line Draw Color. Can be changed per portfolio item inside the Brooklyn metapanel below the editor.' , 'ut_portfolio_lang') . '</p></td>';

                        $html .= '<td class="ut-label-cell">' . __('Title Shadow Color' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell"><input value="' . $this->validate_value( $data , 'title_shadow_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[title_shadow_color]" id="' . esc_attr( $k ) . '_title_shadow_color" />';
                        $html .= '<p class="description">' . __('Shadow Color for all cells. Can be changed per portfolio item inside the Brooklyn metapanel below the editor.' , 'ut_portfolio_lang') . '</p></td>';

                    $html .= '</tr>';

                    /* background titles */
                    $html .= '<tr valign="top" class="ut-settings-row">';

                        $html.= '<td class="ut-headline-cell" colspan="4"><h3>' . __('Background Title Colors' , 'ut_portfolio_lang') . '</h3></td>';

                    $html .= '</tr>';

                    $html .= '<tr valign="top" class="ut-settings-row">';

                        $html .= '<td class="ut-label-cell">' . __('Title Default Color' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell"><input value="' . $this->validate_value( $data , 'title_background_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[title_background_color]" id="' . esc_attr( $k ) . '_title_background_color" />';
                        $html .= '<p class="description">' . __('Background Title Color for all cells. Can be changed per portfolio item inside the Brooklyn metapanel below the editor.' , 'ut_portfolio_lang') . '</p></td>';

                        $html .= '<td class="ut-label-cell">' . __('Title Line Draw Color' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell"><input value="' . $this->validate_value( $data , 'title_background_line_draw_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[title_background_line_draw_color]" id="' . esc_attr( $k ) . '_title_background_line_draw_color" />';
                        $html .= '<p class="description">' . __('Background Title Line Draw Color. Can be changed per portfolio item inside the Brooklyn metapanel below the editor.' , 'ut_portfolio_lang') . '</p></td>';

                    $html .= '</tr>';

                    /* number counter */
                    $html .= '<tr valign="top" class="ut-settings-row">';

                        $html.= '<td class="ut-headline-cell" colspan="4"><h3>' . __('Number Counter Colors' , 'ut_portfolio_lang') . '</h3></td>';

                    $html .= '</tr>';

                    $html .= '<tr valign="top" class="ut-settings-row">';

                        $html .= '<td class="ut-label-cell">' . __('Number Counter Color' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell"><input value="' . $this->validate_value( $data , 'number_counter_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[number_counter_color]" id="' . esc_attr( $k ) . '_number_counter_color" />';
                        $html .= '<p class="description">' . __('Only applies when number counter is active.' , 'ut_portfolio_lang') . '</p></td>';

                        $html .= '<td class="ut-label-cell">' . __('Number Counter Stroke Color' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell"><input value="' . $this->validate_value( $data , 'number_counter_stroke_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[number_counter_stroke_color]" id="' . esc_attr( $k ) . '_number_counter_stroke_color" />';
                        $html .= '<p class="description">' . __('Only applies when number counter stroke is active.' , 'ut_portfolio_lang') . '</p></td>';

                    $html .= '</tr>';

                    /* number counter */
                    $html .= '<tr valign="top" class="ut-settings-row">';

                        $html.= '<td class="ut-headline-cell" colspan="4"><h3>' . __('Category Colors' , 'ut_portfolio_lang') . '</h3></td>';

                    $html .= '</tr>';

                    $html .= '<tr valign="top" class="ut-settings-row">';

                        $html .= '<td class="ut-label-cell">' . __('Category Color' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell"><input value="' . $this->validate_value( $data , 'category_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[category_color]" id="' . esc_attr( $k ) . '_category_color" />';
                        $html .= '<p class="description">' . __('Only applies when categories are active.' , 'ut_portfolio_lang') . '</p></td>';

                    $html .= '</tr>';

                    /* navigation colors */
                    $html .= '<tr valign="top" class="ut-settings-row">';

                        $html.= '<td class="ut-headline-cell" colspan="4"><h3>' . __('Navigation Below Colors' , 'ut_portfolio_lang') . '</h3></td>';

                    $html .= '</tr>';

                    $html .= '<tr valign="top" class="ut-settings-row">';

                        $html .= '<td class="ut-label-cell">' . __('Arrow Color' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell"><input value="' . $this->validate_value( $data , 'nav_below_arrow_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[nav_below_arrow_color]" id="' . esc_attr( $k ) . '_nav_below_arrow_color" />';
                        $html .= '<p class="description">' . __('Color for left and right arrow.' , 'ut_portfolio_lang') . '</p></td>';

                        $html .= '<td class="ut-label-cell">' . __('Arrow Hover Color' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell"><input value="' . $this->validate_value( $data , 'nav_below_arrow_hover_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[nav_below_arrow_hover_color]" id="' . esc_attr( $k ) . '_nav_below_arrow_hover_color" />';
                        $html .= '<p class="description">' . __('Hover Color for left and right arrow.' , 'ut_portfolio_lang') . '</p></td>';

                    $html .= '</tr>';

                    $html .= '<tr valign="top" class="ut-settings-row">';

                        $html .= '<td class="ut-label-cell">' . __('Background Color' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell"><input value="' . $this->validate_value( $data , 'nav_below_arrow_background_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[nav_below_arrow_background_color]" id="' . esc_attr( $k ) . '_nav_below_arrow_background_color" />';
                        $html .= '<p class="description">' . __('Background Color for left and right arrow.' , 'ut_portfolio_lang') . '</p></td>';

                        $html .= '<td class="ut-label-cell">' . __('Background Hover Color' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell"><input value="' . $this->validate_value( $data , 'nav_below_arrow_background_hover_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[nav_below_arrow_background_hover_color]" id="' . esc_attr( $k ) . '_nav_below_arrow_background_hover_color" />';
                        $html .= '<p class="description">' . __('Background Hover Color for left and right arrow.' , 'ut_portfolio_lang') . '</p></td>';

                    $html .= '</tr>';

                    $html .= '<tr valign="top" class="ut-settings-row">';

                        $html .= '<td class="ut-label-cell">' . __('Border Color' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell"><input value="' . $this->validate_value( $data , 'nav_below_arrow_border_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[nav_below_arrow_border_color]" id="' . esc_attr( $k ) . '_nav_below_arrow_border_color" />';
                        $html .= '<p class="description">' . __('Border Color for left and right arrow.' , 'ut_portfolio_lang') . '</p></td>';

                        $html .= '<td class="ut-label-cell">' . __('Border Hover Color' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell"><input value="' . $this->validate_value( $data , 'nav_below_arrow_border_hover_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[nav_below_arrow_border_hover_color]" id="' . esc_attr( $k ) . '_nav_below_arrow_border_hover_color" />';
                        $html .= '<p class="description">' . __('Border Hover Color for left and right arrow.' , 'ut_portfolio_lang') . '</p></td>';

                    $html .= '</tr>';

                    /* end wp table */
                    $html .= '</tbody></table>';

                    /* end panel & section */
                    $html .= '</div></section>' . "\n";

                } elseif( $v['type'] == 'masonry_options' ) {

                    $data = maybe_unserialize( $data );

                    $html .= '<section class="ut-option-section" id="' . esc_attr( $k ) . '">';
                    $html .= '<h2 class="ut-section-title">' . $v['name'] . '</h2>';
					$html .= '<div class="ut-section-panel">';

                    /* start wp table */
                    $html .= '<table class="form-table"><tbody>';

                    /* column set */
                    $html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Desktop Columns' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><input type="text" maxlength="1" value="' . $this->validate_value( $data , 'columns' ) . '" class="regular-text" name="' . esc_attr( $k ) . '[columns]" id="' . esc_attr( $k ) . '_columns" /> <label for="' . esc_attr( $k ) . '_columns"></label>';
					    $html .= '<p class="description">' . __('Images in a row (max value 9).' , 'ut_portfolio_lang') . '</p></td>';
                    $html .= '</tr>';

                    /* tablet columns */
                    $html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Tablet Columns' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><select autocomplete="off" name="' . esc_attr( $k ) . '[tcolumns]" id="' . esc_attr( $k ) . '_tcolumns">';
                            $html .= '<option value="2" ' . $this->selected_array( '2' , 'tcolumns' , $data ) . '>' . __('2 Columns' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="3" ' . $this->selected_array( '3' , 'tcolumns' , $data ) . '>' . __('3 Columns' , 'ut_portfolio_lang') . '</option>';
                        $html .= '</select>';
                        $html .= '<p class="description">' . __('Select your desired tablet column layout.' , 'ut_portfolio_lang') . '</p>';
                    $html .= '</tr>';

                    /* mobile columns */
                    $html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Mobile Columns' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><select autocomplete="off" name="' . esc_attr( $k ) . '[mcolumns]" id="' . esc_attr( $k ) . '_mcolumns">';
                            $html .= '<option value="1" ' . $this->selected_array( '1' , 'mcolumns' , $data ) . '>' . __('1 Column' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="2" ' . $this->selected_array( '2' , 'mcolumns' , $data ) . '>' . __('2 Columns' , 'ut_portfolio_lang') . '</option>';
                        $html .= '</select>';
                        $html .= '<p class="description">' . __('Select your desired mobile column layout.' , 'ut_portfolio_lang') . '</p>';
                    $html .= '</tr>';

                     /* image cropping */
					$html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Image Cropping' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td>';

                        $html .= '<div id="ut_cropping_settings" class="ut-cropping-size">';
                        $html .= '<label for="' . esc_attr( $k ) . '_crop_size_x">' . __('Width' , 'ut_portfolio_lang') . '</label>';
                        $html .= '<input type="text" value="' . $this->validate_value( $data , 'crop_size_x' ) . '" class="small-text code" name="' . esc_attr( $k ) . '[crop_size_x]" id="' . esc_attr( $k ) . '_crop_size_x" />';
                        $html .= '<label for="' . esc_attr( $k ) . '_crop_size_y">' . __('Height' , 'ut_portfolio_lang') . '</label>';
                        $html .= '<input type="text" value="' . $this->validate_value( $data , 'crop_size_y' ) . '" class="small-text code" name="' . esc_attr( $k ) . '[crop_size_y]" id="' . esc_attr( $k ) . '_crop_size_y" />';
                        $html .= '</div>';

                        $html .= '<p class="description">' . __('Default: 1000x800' , 'ut_portfolio_lang') . '</p></td>';

					$html .= '</tr>';

					$html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Portfolio Animation In Style' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><select autocomplete="off" name="' . esc_attr( $k ) . '[animation_in]" id="' . esc_attr( $k ) . '_animation_in">';
                            $html .= '<option value="portfolioFadeIn" ' . $this->selected_array( 'portfolioFadeIn' , 'animation_in' , $data ) . '>' . __('Fade In' , 'ut_portfolio_lang') . '</option>';
							$html .= '<option value="portfolioFadeInUp" ' . $this->selected_array( 'portfolioFadeInUp' , 'animation_in' , $data ) . '>' . __('Fade In Up' , 'ut_portfolio_lang') . '</option>';
							$html .= '<option value="portfolioZoomIn" ' . $this->selected_array( 'portfolioZoomIn' , 'animation_in' , $data ) . '>' . __('Zoom In' , 'ut_portfolio_lang') . '</option>';
                        $html .= '</select>';
                        $html .= '<p class="description">' . __('Select your portfolio animation for appearing items.' , 'ut_portfolio_lang') . '</p>';
                    $html .= '</tr>';

                    /* end wp table */
                    $html .= '</tbody></table>';

                    /* end panel & section */
                    $html .= '</div></section>' . "\n";

                } elseif( $v['type'] == 'gallery_options' ) {

                    $data = maybe_unserialize( $data );

                    $html .= '<section class="ut-option-section" id="' . esc_attr( $k ) . '">';
                    $html .= '<h2 class="ut-section-title">' . $v['name'] . '</h2>';
					$html .= '<div class="ut-section-panel">';

                    /* start wp table */
                    $html .= '<table class="form-table"><tbody>';

                    /* columns */
                    $html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Overall Column Layout' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><select autocomplete="off" name="' . esc_attr( $k ) . '[columns]" id="' . esc_attr( $k ) . '_columns">';
                            $html .= '<option value="2" ' . $this->selected_array( '2' , 'columns' , $data ) . '>' . __('2 Columns' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="3" ' . $this->selected_array( '3' , 'columns' , $data ) . '>' . __('3 Columns' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="4" ' . $this->selected_array( '4' , 'columns' , $data ) . '>' . __('4 Columns' , 'ut_portfolio_lang') . '</option>';
                        $html .= '</select>';
                        $html .= '<p class="description">' . __('Select your desired overall column layout.' , 'ut_portfolio_lang') . '</p></td>';
                    $html .= '</tr>';


	                /* Filter Settings */
	                $html .= '<tr valign="top" class="ut-settings-row">';
	                    $html.= '<td class="ut-headline-cell" colspan="4"><h3>' . __('Filter Settings' , 'ut_portfolio_lang') . '</h3></td>';
	                $html .= '</tr>';

                    /* show hide filter */
                    $html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Gallery Filter' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><div class="ut-checkbox"><input name="' . esc_attr( $k ) . '[filter]" type="checkbox" id="' . esc_attr( $k ) . '_filter" ' . $this->checked_array( 'filter' , $data ) . ' /> <label for="' . esc_attr( $k ) . '_filter"></label></div>';
					    $html .= '<p class="description">' . __('Show or hide the filter above the gallery.' , 'ut_portfolio_lang') . '</p></td>';
					$html .= '</tr>';

                    /* show hide column switch @todo differnet demo
                    $html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Show Column Switch' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><div class="ut-checkbox"><input name="' . esc_attr( $k ) . '[column_switch]" type="checkbox" id="' . esc_attr( $k ) . '_column_switch" ' . $this->checked_array( 'column_switch' , $data ) . ' /> <label for="' . esc_attr( $k ) . '_column_switch"></label></div>';
                        $html .= '<p class="description">' . __('Show or hide column switch. Only available with active gallery filter.' , 'ut_portfolio_lang') . '</p></td>';
                    $html .= '</tr>';


                    $html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Alternate Column Layout' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><select autocomplete="off" name="' . esc_attr( $k ) . '[columns_alternate]" id="' . esc_attr( $k ) . '_columns_alternate">';
                            $html .= '<option value="2" ' . $this->selected_array( '2' , 'columns_alternate' , $data ) . '>' . __('2 Columns' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="3" ' . $this->selected_array( '3' , 'columns_alternate' , $data ) . '>' . __('3 Columns' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="4" ' . $this->selected_array( '4' , 'columns_alternate' , $data ) . '>' . __('4 Columns' , 'ut_portfolio_lang') . '</option>';
                        $html .= '</select>';
                        $html .= '<p class="description">' . __('Select your alternate column layout (applies on Column Switch).' , 'ut_portfolio_lang') . '</p></td>';
                    $html .= '</tr>';  */

					/* filter type */
                    $html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Gallery Filter Type' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><select autocomplete="off" name="' . esc_attr( $k ) . '[filter_type]" id="' . esc_attr( $k ) . '_filter_type">';
                            $html .= '<option value="ajax" ' . $this->selected_array( 'ajax' , 'filter_type' , $data ) . '>' . __('Ajax Filter' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="static" ' . $this->selected_array( 'static' , 'filter_type' , $data ) . '>' . __('Static Filter' , 'ut_portfolio_lang') . '</option>';
                        $html .= '</select>';
                        $html .= '<p class="description">' . __('Select your desired gallery filter type.' , 'ut_portfolio_lang') . '</p></td>';
                    $html .= '</tr>';

					/* filter style */
                    $html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Gallery Filter Style' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><select autocomplete="off" name="' . esc_attr( $k ) . '[filter_style]" id="' . esc_attr( $k ) . '_filter_style">';
                            $html .= '<option value="style_one" ' . $this->selected_array( 'style_one' , 'filter_style' , $data ) . '>' . __('Style One (light)' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="style_two" ' . $this->selected_array( 'style_two' , 'filter_style' , $data ) . '>' . __('Style Two (themecolor)' , 'ut_portfolio_lang') . '</option>';
							$html .= '<option value="style_three" ' . $this->selected_array( 'style_three' , 'filter_style' , $data ) . '>' . __('Style Three (dark)' , 'ut_portfolio_lang') . '</option>';
                        $html .= '</select>';
                        $html .= '<p class="description">' . __('Select your desired gallery filter style.' , 'ut_portfolio_lang') . '</p></td>';
                    $html .= '</tr>';

                    /* filter align */
                    $html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Gallery Filter Align' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><select autocomplete="off" name="' . esc_attr( $k ) . '[filter_align]" id="' . esc_attr( $k ) . '_filter_align">';
                            $html .= '<option value="center" ' . $this->selected_array( 'center' , 'filter_align' , $data ) . '>' . __('centred' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="left" ' . $this->selected_array( 'left' , 'filter_align' , $data ) . '>' . __('left' , 'ut_portfolio_lang') . '</option>';
							$html .= '<option value="right" ' . $this->selected_array( 'right' , 'filter_align' , $data ) . '>' . __('right' , 'ut_portfolio_lang') . '</option>';
                        $html .= '</select>';
                        $html .= '<p class="description">' . __('Select your desired gallery filter alignment.' , 'ut_portfolio_lang') . '</p></td>';
                    $html .= '</tr>';

					/* filter spacing */
                    $html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Gallery Filter Spacing' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><select autocomplete="off" name="' . esc_attr( $k ) . '[filter_spacing]" id="' . esc_attr( $k ) . '_filter_spacing">';
                            $html .= '<option value="20" ' . $this->selected_array( '20' , 'filter_spacing' , $data ) . '>' . __('20 Pixel' , 'ut_portfolio_lang') . '</option>';
							$html .= '<option value="40" ' . $this->selected_array( '40' , 'filter_spacing' , $data ) . '>' . __('40 Pixel' , 'ut_portfolio_lang') . '</option>';
							$html .= '<option value="60" ' . $this->selected_array( '60' , 'filter_spacing' , $data ) . '>' . __('60 Pixel' , 'ut_portfolio_lang') . '</option>';
							$html .= '<option value="80" ' . $this->selected_array( '80' , 'filter_spacing' , $data ) . '>' . __('80 Pixel' , 'ut_portfolio_lang') . '</option>';
							$html .= '<option value="100" ' . $this->selected_array( '100' , 'filter_spacing' , $data ) . '>' . __('100 Pixel' , 'ut_portfolio_lang') . '</option>';
                        $html .= '</select>';
                        $html .= '<p class="description">' . __('Select your desired gallery filter spacing, it is the spacing between filter elements.' , 'ut_portfolio_lang') . '</p></td>';
                    $html .= '</tr>';

                    /* reset */
                    $html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Gallery Filter Reset' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><input type="text" value="' . $this->validate_value( $data , 'reset_text' ) . '" class="regular-text" name="' . esc_attr( $k ) . '[reset_text]" id="' . esc_attr( $k ) . '_reset_text" /> <label for="' . esc_attr( $k ) . '_reset_text"></label>';
					    $html .= '<p class="description">' . __('Text for gallery filter reset (default: All).' , 'ut_portfolio_lang') . '</p></td>';
                    $html .= '</tr>';

	                /* Filter Settings */
	                $html .= '<tr valign="top" class="ut-settings-row">';
	                    $html.= '<td class="ut-headline-cell" colspan="4"><h3>' . __('Filter Colors' , 'ut_portfolio_lang') . '</h3></td>';
	                $html .= '</tr>';

                    /* filter default colors */
                    $html .= '<tr valign="top" class="ut-settings-row">';

                        $html .= '<td class="ut-label-cell">' . __('Filter Default Colors' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td>';

                            $html .= '<table class="form-table form-table-inner"><tbody>';

                                $html .= '<tr valign="top" class="ut-settings-row">';

                                    $html .= '<td><input value="' . $this->validate_value( $data , 'filter_text_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[filter_text_color]" id="' . esc_attr( $k ) . '_filter_text_color" /> <label for="' . esc_attr( $k ) . '_filter_text_color"></label>';
                                    $html .= '<p class="description">' . __('Text Color' , 'ut_portfolio_lang') . '</p></td>';

                                    $html .= '<td><input value="' . $this->validate_value( $data , 'filter_background_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[filter_background_color]" id="' . esc_attr( $k ) . '_filter_background_color" /> <label for="' . esc_attr( $k ) . '_filter_background_color"></label>';
                                    $html .= '<p class="description">' . __('Background Color' , 'ut_portfolio_lang') . '</p></td>';

                                    $html .= '<td><input value="' . $this->validate_value( $data , 'filter_border_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[filter_border_color]" id="' . esc_attr( $k ) . '_filter_border_color" /> <label for="' . esc_attr( $k ) . '_filter_border_color"></label>';
                                    $html .= '<p class="description">' . __('Border Color' , 'ut_portfolio_lang') . '</p></td>';

                                $html .= '</tr>';

                            $html .= '</tbody></table>';

                        $html .= '</td>';

                    $html .= '</tr>';


                    /* filter hover colors */
                    $html .= '<tr valign="top" class="ut-settings-row">';

                        $html .= '<td class="ut-label-cell">' . __('Filter Hover Colors' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td>';

                            $html .= '<table class="form-table form-table-inner"><tbody>';

                                $html .= '<tr valign="top" class="ut-settings-row">';

                                    $html .= '<td><input value="' . $this->validate_value( $data , 'filter_text_hover_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[filter_text_hover_color]" id="' . esc_attr( $k ) . '_filter_text_hover_color" /> <label for="' . esc_attr( $k ) . '_filter_text_hover_color"></label>';
                                    $html .= '<p class="description">' . __('Text Color' , 'ut_portfolio_lang') . '</p></td>';

                                    $html .= '<td><input value="' . $this->validate_value( $data , 'filter_background_hover_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[filter_background_hover_color]" id="' . esc_attr( $k ) . '_filter_background_hover_color" /> <label for="' . esc_attr( $k ) . '_filter_background_hover_color"></label>';
                                    $html .= '<p class="description">' . __('Background Color' , 'ut_portfolio_lang') . '</p></td>';

                                    $html .= '<td><input value="' . $this->validate_value( $data , 'filter_border_hover_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[filter_border_hover_color]" id="' . esc_attr( $k ) . '_filter_border_hover_color" /> <label for="' . esc_attr( $k ) . '_filter_border_hover_color"></label>';
                                    $html .= '<p class="description">' . __('Border Color' , 'ut_portfolio_lang') . '</p></td>';

                                $html .= '</tr>';

                            $html .= '</tbody></table>';

                        $html .= '</td>';

                    $html .= '</tr>';

                    /* filter selected colors */
                    $html .= '<tr valign="top" class="ut-settings-row">';

                        $html .= '<td class="ut-label-cell">' . __('Filter Selected Colors' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td>';

                            $html .= '<table class="form-table form-table-inner"><tbody>';

                                $html .= '<tr valign="top" class="ut-settings-row">';

                                    $html .= '<td><input value="' . $this->validate_value( $data , 'filter_text_selected_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[filter_text_selected_color]" id="' . esc_attr( $k ) . '_filter_text_selected_color" /> <label for="' . esc_attr( $k ) . '_filter_text_selected_color"></label>';
                                    $html .= '<p class="description">' . __('Text Color' , 'ut_portfolio_lang') . '</p></td>';

                                    $html .= '<td><input value="' . $this->validate_value( $data , 'filter_background_selected_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[filter_background_selected_color]" id="' . esc_attr( $k ) . '_filter_background_selected_color" /> <label for="' . esc_attr( $k ) . '_filter_background_selected_color"></label>';
                                    $html .= '<p class="description">' . __('Background Color' , 'ut_portfolio_lang') . '</p></td>';

                                    $html .= '<td><input value="' . $this->validate_value( $data , 'filter_border_selected_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[filter_border_selected_color]" id="' . esc_attr( $k ) . '_filter_border_selected_color" /> <label for="' . esc_attr( $k ) . '_filter_border_selected_color"></label>';
                                    $html .= '<p class="description">' . __('Border Color' , 'ut_portfolio_lang') . '</p></td>';


                                $html .= '</tr>';

                            $html .= '</tbody></table>';

                        $html .= '</td>';

                    $html .= '</tr>';

					$html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Deactivate Filter Border' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><select autocomplete="off" name="' . esc_attr( $k ) . '[filter_deactivate_border]" id="' . esc_attr( $k ) . '_filter_deactivate_border">';
                            $html .= '<option value="no" ' . $this->selected_array( 'no' , 'filter_deactivate_border' , $data ) . '>' . __('no, thanks!' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="yes" ' . $this->selected_array( 'yes' , 'filter_deactivate_border' , $data ) . '>' . __('yes, please!' , 'ut_portfolio_lang') . '</option>';
                        $html .= '</select>';
                        $html .= '<p class="description">' . __('This option also removed the padding.' , 'ut_portfolio_lang') . '</p></td>';
                    $html .= '</tr>';

	                /* Filter Settings */
	                $html .= '<tr valign="top" class="ut-settings-row">';
	                    $html.= '<td class="ut-headline-cell" colspan="4"><h3>' . __('Filter Font' , 'ut_portfolio_lang') . '</h3></td>';
	                $html .= '</tr>';

					/* filter font size */
					$html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Filter Font Size' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><select autocomplete="off" name="' . esc_attr( $k ) . '[filter_font_size]" id="' . esc_attr( $k ) . '_filter_font_size">';
                            $html .= '<option value="default" ' . $this->selected_array( 'default' , 'filter_font_size' , $data ) . '>' . __('Default Font Size' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="inherit" ' . $this->selected_array( 'inherit' , 'filter_font_size' , $data ) . '>' . __('Inherit from Body' , 'ut_portfolio_lang') . '</option>';
                        $html .= '</select>';
                        $html .= '<p class="description">' . __('By default the filter has a font size of 12. You can optionally inherit it from your body font.' , 'ut_portfolio_lang') . '</p></td>';
                    $html .= '</tr>';

                    /* filter font-weight */
                    $html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Filter Font Weight' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><select autocomplete="off" name="' . esc_attr( $k ) . '[filter_font_weight]" id="' . esc_attr( $k ) . '_font_weight">';
                            $html .= '<option value="normal" ' . $this->selected_array( 'normal' , 'filter_font_weight' , $data ) . '>' . __('normal' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="bold" ' . $this->selected_array( 'bold' , 'filter_font_weight' , $data ) . '>' . __('bold' , 'ut_portfolio_lang') . '</option>';
							$html .= '<option value="100" ' . $this->selected_array( '100' , 'filter_font_weight' , $data ) . '>' . __('100' , 'ut_portfolio_lang') . '</option>';
							$html .= '<option value="200" ' . $this->selected_array( '200' , 'filter_font_weight' , $data ) . '>' . __('200' , 'ut_portfolio_lang') . '</option>';
							$html .= '<option value="300" ' . $this->selected_array( '300' , 'filter_font_weight' , $data ) . '>' . __('300' , 'ut_portfolio_lang') . '</option>';
							$html .= '<option value="400" ' . $this->selected_array( '400' , 'filter_font_weight' , $data ) . '>' . __('400' , 'ut_portfolio_lang') . '</option>';
							$html .= '<option value="500" ' . $this->selected_array( '500' , 'filter_font_weight' , $data ) . '>' . __('500' , 'ut_portfolio_lang') . '</option>';
							$html .= '<option value="600" ' . $this->selected_array( '600' , 'filter_font_weight' , $data ) . '>' . __('600' , 'ut_portfolio_lang') . '</option>';
							$html .= '<option value="700" ' . $this->selected_array( '700' , 'filter_font_weight' , $data ) . '>' . __('700' , 'ut_portfolio_lang') . '</option>';
							$html .= '<option value="800" ' . $this->selected_array( '800' , 'filter_font_weight' , $data ) . '>' . __('800' , 'ut_portfolio_lang') . '</option>';
							$html .= '<option value="900" ' . $this->selected_array( '900' , 'filter_font_weight' , $data ) . '>' . __('900' , 'ut_portfolio_lang') . '</option>';
                        $html .= '</select>';
                        $html .= '<p class="description">' . __('Select your desired portfolio filter font weight.' , 'ut_portfolio_lang') . '</p></td>';
                    $html .= '</tr>';

					$html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Filter Text Transform' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><select autocomplete="off" name="' . esc_attr( $k ) . '[filter_text_transform]" id="' . esc_attr( $k ) . '_text_transform">';
                            $html .= '<option value="uppercase" ' . $this->selected_array( 'uppercase' , 'filter_text_transform' , $data ) . '>' . __('uppercase' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="lowercase" ' . $this->selected_array( 'lowercase' , 'filter_text_transform' , $data ) . '>' . __('lowercase' , 'ut_portfolio_lang') . '</option>';
							$html .= '<option value="capitalize" ' . $this->selected_array( 'capitalize' , 'filter_text_transform' , $data ) . '>' . __('capitalize' , 'ut_portfolio_lang') . '</option>';
							$html .= '<option value="none" ' . $this->selected_array( 'none' , 'filter_text_transform' , $data ) . '>' . __('none' , 'ut_portfolio_lang') . '</option>';
                        $html .= '</select>';
                        $html .= '<p class="description">' . __('Select your desired portfolio filter text transform.' , 'ut_portfolio_lang') . '</p></td>';
                    $html .= '</tr>';

                    $html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Filter Font Style' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><select autocomplete="off" name="' . esc_attr( $k ) . '[filter_font_style]" id="' . esc_attr( $k ) . '_font_style">';
                            $html .= '<option value="normal" ' . $this->selected_array( 'normal' , 'filter_font_style' , $data ) . '>' . __('normal' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="italic" ' . $this->selected_array( 'italic' , 'filter_font_style' , $data ) . '>' . __('italic' , 'ut_portfolio_lang') . '</option>';
                        $html .= '</select>';
                        $html .= '<p class="description">' . __('Select your desired portfolio filter font style.' , 'ut_portfolio_lang') . '</p></td>';
                    $html .= '</tr>';

                    /* letter spacing */
                    $html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Filter Letter Spacing' , 'ut_portfolio_lang') . '</td>';
					    $html .= '<td><div class="ut-range-slider-group ut-jquery-ui">';
						$html .= '<div data-state="' . $this->validate_value( $data , 'filter_letter_spacing' , "0" ) . '" class="ut-letter-spacing-slider"></div>';
						$html .= '<span class="ut-letter-spacing-value">' . $this->validate_value( $data , 'filter_letter_spacing' , "0" ) . '</span>';
						$html .= '<input value="' . $this->validate_value( $data , 'filter_letter_spacing' , "0" ) . '" class="regular-text ut-hidden-slider-input" name="' . esc_attr( $k ) . '[filter_letter_spacing]" id="' . esc_attr( $k ) . '_filter_letter_spacing" />';
						$html .= '</div>';
						$html .= '</td>';
                    $html .= '</tr>';

	                /* Portfolio Settings */
	                $html .= '<tr valign="top" class="ut-settings-row">';
	                    $html.= '<td class="ut-headline-cell" colspan="4"><h3>' . __('Portfolio Appearance' , 'ut_portfolio_lang') . '</h3></td>';
	                $html .= '</tr>';

					/* style */
                    $html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Portfolio Visual Style' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><select autocomplete="off" name="' . esc_attr( $k ) . '[style]" id="' . esc_attr( $k ) . '_style">';
                            $html .= '<option value="style_one" ' . $this->selected_array( 'style_one' , 'style' , $data ) . '>' . __('Style 1 ( only images )' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="style_two" ' . $this->selected_array( 'style_two' , 'style' , $data ) . '>' . __('Style 2 ( images with title ) ' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="style_three" ' . $this->selected_array( 'style_three' , 'style' , $data ) . '>' . __('Style 3 ( image tilt ) ' , 'ut_portfolio_lang') . '</option>';
                        $html .= '</select>';
                        $html .= '<p class="description">' . __('Select your desired portfolio style. Image tilt works best with an active gutter.' , 'ut_portfolio_lang') . '</p></td>';
                    $html .= '</tr>';

	                /* Caption Visibility  */
	                $html .= '<tr valign="top" class="ut-settings-row">';
		                $html .= '<td class="ut-label-cell">' . __('Portfolio Caption Visibility' , 'ut_portfolio_lang') . '</td>';
		                $html .= '<td><select autocomplete="off" name="' . esc_attr( $k ) . '[caption_visibility]" id="' . esc_attr( $k ) . '_caption_visibility">';
			                $html .= '<option value="on_hover" ' . $this->selected_array( 'on_hover' , 'caption_visibility' , $data ) . '>' . __('On Hover' , 'ut_portfolio_lang') . '</option>';
			                $html .= '<option value="permanent" ' . $this->selected_array( 'permanent' , 'caption_visibility' , $data ) . '>' . __('Permanent' , 'ut_portfolio_lang') . '</option>';
		                $html .= '</select>';
		                $html .= '<p class="description">' . __('Select your desired portfolio caption visibility.' , 'ut_portfolio_lang') . '</p></td>';
	                $html .= '</tr>';

					$html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Portfolio Animation In Style' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><select autocomplete="off" name="' . esc_attr( $k ) . '[animation_in]" id="' . esc_attr( $k ) . '_animation_in">';
                            $html .= '<option value="portfolioFadeIn" ' . $this->selected_array( 'portfolioFadeIn' , 'animation_in' , $data ) . '>' . __('Fade In' , 'ut_portfolio_lang') . '</option>';
							$html .= '<option value="portfolioFadeInUp" ' . $this->selected_array( 'portfolioFadeInUp' , 'animation_in' , $data ) . '>' . __('Fade In Up' , 'ut_portfolio_lang') . '</option>';
							$html .= '<option value="portfolioZoomIn" ' . $this->selected_array( 'portfolioZoomIn' , 'animation_in' , $data ) . '>' . __('Zoom In' , 'ut_portfolio_lang') . '</option>';
                        $html .= '</select>';
                        $html .= '<p class="description">' . __('Select your portfolio animation for appearing items.' , 'ut_portfolio_lang') . '</p></td>';
                    $html .= '</tr>';

					$html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Portfolio Animation Out Style' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><select autocomplete="off" name="' . esc_attr( $k ) . '[animation_out]" id="' . esc_attr( $k ) . '_animation_out">';
                            $html .= '<option value="portfolioZoomOut" ' . $this->selected_array( 'portfolioZoomOut' , 'animation_out' , $data ) . '>' . __('Zoom Out' , 'ut_portfolio_lang') . '</option>';
							$html .= '<option value="portfolioFadeOut" ' . $this->selected_array( 'portfolioFadeOut' , 'animation_out' , $data ) . '>' . __('Fade Out' , 'ut_portfolio_lang') . '</option>';
                        $html .= '</select>';
                        $html .= '<p class="description">' . __('Select your portfolio animation for disappearing items.' , 'ut_portfolio_lang') . '</p></td>';
                    $html .= '</tr>';

                    /* caption content */
                    $html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Portfolio Hover Caption Content' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td>';

                            $html .= '<select autocomplete="off" name="' . esc_attr( $k ) . '[style_1_caption_content]" id="' . esc_attr( $k ) . '_style_1_caption_content">';
                                $html .= '<option value="title_category" ' . $this->selected_array( 'title_category' , 'style_1_caption_content' , $data ) . '>' . __('Title + Category' , 'ut_portfolio_lang') . '</option>';
                                $html .= '<option value="title_only" ' . $this->selected_array( 'title_only' , 'style_1_caption_content' , $data ) . '>' . __('Title Only' , 'ut_portfolio_lang') . '</option>';
                                $html .= '<option value="category_only" ' . $this->selected_array( 'category_only' , 'style_1_caption_content' , $data ) . '>' . __('Category Only' , 'ut_portfolio_lang') . '</option>';
                                $html .= '<option value="plus_sign" ' . $this->selected_array( 'plus_sign' , 'style_1_caption_content' , $data ) . '>' . __('Plus Sign' , 'ut_portfolio_lang') . '</option>';
                                $html .= '<option value="custom_text" ' . $this->selected_array( 'custom_text' , 'style_1_caption_content' , $data ) . '>' . __('Custom Text' , 'ut_portfolio_lang') . '</option>';
                                $html .= '<option value="none" ' . $this->selected_array( 'none' , 'style_1_caption_content' , $data ) . '>' . __('No Content' , 'ut_portfolio_lang') . '</option>';
                            $html .= '</select>';

                            $html .= '<input style="margin-top:20px; display:block;" type="text" value="' . $this->validate_value( $data , 'style_1_caption_custom_text' ) . '" class="regular-text" name="' . esc_attr( $k ) . '[style_1_caption_custom_text]" id="' . esc_attr( $k ) . '_style_1_caption_custom_text" />';

                            $html .= '<select autocomplete="off" name="' . esc_attr( $k ) . '[style_2_caption_content]" id="' . esc_attr( $k ) . '_style_2_caption_content">';
                                $html .= '<option value="category_icon" ' . $this->selected_array( 'category_icon' , 'style_2_caption_content' , $data ) . '>' . __('Icon + Category' , 'ut_portfolio_lang') . '</option>';
                                $html .= '<option value="category_only" ' . $this->selected_array( 'category_only' , 'style_2_caption_content' , $data ) . '>' . __('Category Only' , 'ut_portfolio_lang') . '</option>';
                                $html .= '<option value="plus_sign" ' . $this->selected_array( 'plus_sign' , 'style_2_caption_content' , $data ) . '>' . __('Plus Sign' , 'ut_portfolio_lang') . '</option>';
                                $html .= '<option value="custom_text" ' . $this->selected_array( 'custom_text' , 'style_2_caption_content' , $data ) . '>' . __('Custom Text' , 'ut_portfolio_lang') . '</option>';
                                $html .= '<option value="none" ' . $this->selected_array( 'none' , 'style_2_caption_content' , $data ) . '>' . __('No Content' , 'ut_portfolio_lang') . '</option>';
                            $html .= '</select>';

                            $html .= '<input style="margin-top:20px; display:block;" type="text" value="' . $this->validate_value( $data , 'style_2_caption_custom_text' ) . '" class="regular-text" name="' . esc_attr( $k ) . '[style_2_caption_custom_text]" id="' . esc_attr( $k ) . '_style_2_caption_custom_text" />';

                        $html .= '<p class="description">' . __('' , 'ut_portfolio_lang') . '</p></td>';
                    $html .= '</tr>';

					/* gutter */
                    $html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Gutter' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><div class="ut-checkbox"><input name="' . esc_attr( $k ) . '[gutter]" type="checkbox" id="' . esc_attr( $k ) . '_gutter" ' . $this->checked_array( 'gutter' , $data ) . ' /> <label for="' . esc_attr( $k ) . '_gutter"></label></div>';
					    $html .= '<p class="description">' . __('Adds a small gutter between the portfolio images.' , 'ut_portfolio_lang') . '</p></td>';
					$html .= '</tr>';

                    /* gutter size */
                    $html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Gutter Size' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><select autocomplete="off" name="' . esc_attr( $k ) . '[gutter_size]" id="' . esc_attr( $k ) . '_gutter_size">';
                            $html .= '<option value="1" ' . $this->selected_array( '1' , 'gutter_size' , $data ) . '>' . __('20px' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="2" ' . $this->selected_array( '2' , 'gutter_size' , $data ) . '>' . __('40px' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="3" ' . $this->selected_array( '3' , 'gutter_size' , $data ) . '>' . __('60px' , 'ut_portfolio_lang') . '</option>';
                        $html .= '</select>';
                        $html .= '<p class="description">' . __('Select your desired gutter size.' , 'ut_portfolio_lang') . '</p></td>';
                    $html .= '</tr>';

                    /* gutter shadow */
                    $html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Gutter Shadow' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><div class="ut-checkbox"><input name="' . esc_attr( $k ) . '[gutter_shadow]" type="checkbox" id="' . esc_attr( $k ) . '_gutter_shadow" ' . $this->checked_array( 'gutter_shadow' , $data ) . ' /> <label for="' . esc_attr( $k ) . '_gutter_shadow"></label></div>';
					    $html .= '<p class="description">' . __('Adds a small gutter shadow between the portfolio images.' , 'ut_portfolio_lang') . '</p></td>';
					$html .= '</tr>';

					 /* image cropping */
					$html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Image Cropping' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td>';

                        $html .= '<div id="ut_cropping_settings_gallery" class="ut-cropping-size">';
                        $html .= '<label for="' . esc_attr( $k ) . '_crop_size_x">' . __('Width' , 'ut_portfolio_lang') . '</label>';
                        $html .= '<input type="text" value="' . $this->validate_value( $data , 'crop_size_x' ) . '" class="small-text code" name="' . esc_attr( $k ) . '[crop_size_x]" id="' . esc_attr( $k ) . '_crop_size_x" />';
                        $html .= '<label for="' . esc_attr( $k ) . '_crop_size_y">' . __('Height' , 'ut_portfolio_lang') . '</label>';
                        $html .= '<input type="text" value="' . $this->validate_value( $data , 'crop_size_y' ) . '" class="small-text code" name="' . esc_attr( $k ) . '[crop_size_y]" id="' . esc_attr( $k ) . '_crop_size_y" />';
                        $html .= '</div>';

                        $html .= '<p class="description" style="margin-bottom:30px;">' . __('Default: 1000x800' , 'ut_portfolio_lang') . '</p>';

                        /* hard or softcrop */
                        $html .= '<div class="ut-checkbox"><input name="' . esc_attr( $k ) . '[hardcrop]" type="checkbox" id="' . esc_attr( $k ) . '_hardcrop" ' . $this->checked_array( 'hardcrop' , $data ) . ' /> <label for="' . esc_attr( $k ) . '_hardcrop"></label></div>';
                        $html .= '<p style="max-width:550px;" class="description">' . __('<strong>Activate Image Soft Cropping?</strong> What does Soft Crop mean? A soft crop will never cut off any of the image, it will scale the image down until it fits within the dimensions specified, maintaining its original aspect ratio. What does Hard Crop mean? The image will be scaled and then cropped to the exact dimensions you have specified. Depending on the aspect ratio of the image in relation to the crop size, it might happen that the image will be cut off.' , 'ut_portfolio_lang') . '</p>';

                        $html .= '</td>';

					$html .= '</tr>';
                    
                    /* end wp table */
                    $html .= '</tbody></table>';
                    
                    /* end panel & section */ 
                    $html .= '</div></section>' . "\n";
                    
                        
				} elseif( $v['type'] == 'packery_options' ) {
                    
                    $data = maybe_unserialize( $data );
                    
                    $html .= '<section class="ut-option-section" id="' . esc_attr( $k ) . '">';
                    $html .= '<h2 class="ut-section-title">' . $v['name'] . '</h2>';
					$html .= '<div class="ut-section-panel">';					
					
                    /* start wp table */
                    $html .= '<table class="form-table"><tbody>';                    
                    
                    /* columns */
                    $html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Overall Column Layout' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><select autocomplete="off" name="' . esc_attr( $k ) . '[columns]" id="' . esc_attr( $k ) . '_columns">';
                            $html .= '<option value="2" ' . $this->selected_array( '2' , 'columns' , $data ) . '>' . __('2 Columns' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="3" ' . $this->selected_array( '3' , 'columns' , $data ) . '>' . __('3 Columns' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="4" ' . $this->selected_array( '4' , 'columns' , $data ) . '>' . __('4 Columns' , 'ut_portfolio_lang') . '</option>';
                        $html .= '</select>';
                        $html .= '<p class="description">' . __('Select your desired overall column layout.' , 'ut_portfolio_lang') . '</p>';
                    $html .= '</tr>';
										
                    /* show hide filter */
                    $html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Gallery Filter' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><div class="ut-checkbox"><input name="' . esc_attr( $k ) . '[filter]" type="checkbox" id="' . esc_attr( $k ) . '_filter" ' . $this->checked_array( 'filter' , $data ) . ' /> <label for="' . esc_attr( $k ) . '_filter"></label></div>';
					    $html .= '<p class="description">' . __('Show or hide the filter above the gallery.' , 'ut_portfolio_lang') . '</p></td>';
					$html .= '</tr>';
					
					/* filter style */
                    $html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Gallery Filter Style' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><select autocomplete="off" name="' . esc_attr( $k ) . '[filter_style]" id="' . esc_attr( $k ) . '_filter_style">';
                            $html .= '<option value="style_one" ' . $this->selected_array( 'style_one' , 'filter_style' , $data ) . '>' . __('Style One (light)' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="style_two" ' . $this->selected_array( 'style_two' , 'filter_style' , $data ) . '>' . __('Style Two (themecolor)' , 'ut_portfolio_lang') . '</option>';
							$html .= '<option value="style_three" ' . $this->selected_array( 'style_three' , 'filter_style' , $data ) . '>' . __('Style Three (dark)' , 'ut_portfolio_lang') . '</option>';
                        $html .= '</select>';
                        $html .= '<p class="description">' . __('Select your desired gallery filter style.' , 'ut_portfolio_lang') . '</p>';
                    $html .= '</tr>';

                    /* filter align */
                    $html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Gallery Filter Align' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><select autocomplete="off" name="' . esc_attr( $k ) . '[filter_align]" id="' . esc_attr( $k ) . '_filter_align">';
                            $html .= '<option value="center" ' . $this->selected_array( 'center' , 'filter_align' , $data ) . '>' . __('centred' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="left" ' . $this->selected_array( 'left' , 'filter_align' , $data ) . '>' . __('left' , 'ut_portfolio_lang') . '</option>';
							$html .= '<option value="right" ' . $this->selected_array( 'right' , 'filter_align' , $data ) . '>' . __('right' , 'ut_portfolio_lang') . '</option>';
                        $html .= '</select>';
                        $html .= '<p class="description">' . __('Select your desired gallery filter alignment.' , 'ut_portfolio_lang') . '</p>';
                    $html .= '</tr>';

					/* filter spacing */
                    $html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Gallery Filter Spacing' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><select autocomplete="off" name="' . esc_attr( $k ) . '[filter_spacing]" id="' . esc_attr( $k ) . '_filter_spacing">';
                            $html .= '<option value="20" ' . $this->selected_array( '20' , 'filter_spacing' , $data ) . '>' . __('20 Pixel' , 'ut_portfolio_lang') . '</option>';
							$html .= '<option value="40" ' . $this->selected_array( '40' , 'filter_spacing' , $data ) . '>' . __('40 Pixel' , 'ut_portfolio_lang') . '</option>';
							$html .= '<option value="60" ' . $this->selected_array( '60' , 'filter_spacing' , $data ) . '>' . __('60 Pixel' , 'ut_portfolio_lang') . '</option>';
							$html .= '<option value="80" ' . $this->selected_array( '80' , 'filter_spacing' , $data ) . '>' . __('80 Pixel' , 'ut_portfolio_lang') . '</option>';
							$html .= '<option value="100" ' . $this->selected_array( '100' , 'filter_spacing' , $data ) . '>' . __('100 Pixel' , 'ut_portfolio_lang') . '</option>';
                        $html .= '</select>';
                        $html .= '<p class="description">' . __('Select your desired gallery filter spacing, it is the spacing between filter elements.' , 'ut_portfolio_lang') . '</p>';
                    $html .= '</tr>';

                    /* reset */
                    $html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Gallery Filter Reset' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><input type="text" value="' . $this->validate_value( $data , 'reset_text' ) . '" class="regular-text" name="' . esc_attr( $k ) . '[reset_text]" id="' . esc_attr( $k ) . '_reset_text" /> <label for="' . esc_attr( $k ) . '_reset_text"></label>';
					    $html .= '<p class="description">' . __('Text for gallery filter reset (default: All).' , 'ut_portfolio_lang') . '</p></td>';
                    $html .= '</tr>';

                    /* filter default colors */
                    $html .= '<tr valign="top" class="ut-settings-row">';

                        $html .= '<td class="ut-label-cell">' . __('Filter Default Colors' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td>';

                            $html .= '<table class="form-table form-table-inner"><tbody>';

                                $html .= '<tr valign="top" class="ut-settings-row">';

                                    $html .= '<td class="ut-label-cell">' . __('Text Color' , 'ut_portfolio_lang') . '</td>';
                                    $html .= '<td><input value="' . $this->validate_value( $data , 'filter_text_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[filter_text_color]" id="' . esc_attr( $k ) . '_filter_text_color" /> <label for="' . esc_attr( $k ) . '_filter_text_color"></label>';
                                    $html .= '<p class="description">' . __('' , 'ut_portfolio_lang') . '</p></td>';

                                    $html .= '<td class="ut-label-cell">' . __('Background Color' , 'ut_portfolio_lang') . '</td>';
                                    $html .= '<td><input value="' . $this->validate_value( $data , 'filter_background_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[filter_background_color]" id="' . esc_attr( $k ) . '_filter_background_color" /> <label for="' . esc_attr( $k ) . '_filter_background_color"></label>';
                                    $html .= '<p class="description">' . __('' , 'ut_portfolio_lang') . '</p></td>';

                                    $html .= '<td class="ut-label-cell">' . __('Border Color' , 'ut_portfolio_lang') . '</td>';
                                    $html .= '<td><input value="' . $this->validate_value( $data , 'filter_border_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[filter_border_color]" id="' . esc_attr( $k ) . '_filter_border_color" /> <label for="' . esc_attr( $k ) . '_filter_border_color"></label>';
                                    $html .= '<p class="description">' . __('' , 'ut_portfolio_lang') . '</p></td>';

                                $html .= '</tr>';

                            $html .= '</tbody></table>';

                        $html .= '</td>';

                    $html .= '</tr>';


                    /* filter hover colors */
                    $html .= '<tr valign="top" class="ut-settings-row">';

                        $html .= '<td class="ut-label-cell">' . __('Filter Hover Colors' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td>';

                            $html .= '<table class="form-table form-table-inner"><tbody>';

                                $html .= '<tr valign="top" class="ut-settings-row">';

                                    $html .= '<td class="ut-label-cell">' . __('Text Color' , 'ut_portfolio_lang') . '</td>';
                                    $html .= '<td><input value="' . $this->validate_value( $data , 'filter_text_hover_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[filter_text_hover_color]" id="' . esc_attr( $k ) . '_filter_text_hover_color" /> <label for="' . esc_attr( $k ) . '_filter_text_hover_color"></label>';
                                    $html .= '<p class="description">' . __('' , 'ut_portfolio_lang') . '</p></td>';

                                    $html .= '<td class="ut-label-cell">' . __('Background Color' , 'ut_portfolio_lang') . '</td>';
                                    $html .= '<td><input value="' . $this->validate_value( $data , 'filter_background_hover_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[filter_background_hover_color]" id="' . esc_attr( $k ) . '_filter_background_hover_color" /> <label for="' . esc_attr( $k ) . '_filter_background_hover_color"></label>';
                                    $html .= '<p class="description">' . __('' , 'ut_portfolio_lang') . '</p></td>';

                                    $html .= '<td class="ut-label-cell">' . __('Border Color' , 'ut_portfolio_lang') . '</td>';
                                    $html .= '<td><input value="' . $this->validate_value( $data , 'filter_border_hover_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[filter_border_hover_color]" id="' . esc_attr( $k ) . '_filter_border_hover_color" /> <label for="' . esc_attr( $k ) . '_filter_border_hover_color"></label>';
                                    $html .= '<p class="description">' . __('' , 'ut_portfolio_lang') . '</p></td>';

                                $html .= '</tr>';

                            $html .= '</tbody></table>';

                        $html .= '</td>';

                    $html .= '</tr>';


                    /* filter selected colors */
                    $html .= '<tr valign="top" class="ut-settings-row">';

                        $html .= '<td class="ut-label-cell">' . __('Filter Selected Colors' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td>';

                            $html .= '<table class="form-table form-table-inner"><tbody>';

                                $html .= '<tr valign="top" class="ut-settings-row">';

                                    $html .= '<td class="ut-label-cell">' . __('Text Color' , 'ut_portfolio_lang') . '</td>';
                                    $html .= '<td><input value="' . $this->validate_value( $data , 'filter_text_selected_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[filter_text_selected_color]" id="' . esc_attr( $k ) . '_filter_text_selected_color" /> <label for="' . esc_attr( $k ) . '_filter_text_selected_color"></label>';
                                    $html .= '<p class="description">' . __('' , 'ut_portfolio_lang') . '</p></td>';

                                    $html .= '<td class="ut-label-cell">' . __('Background Color' , 'ut_portfolio_lang') . '</td>';
                                    $html .= '<td><input value="' . $this->validate_value( $data , 'filter_background_selected_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[filter_background_selected_color]" id="' . esc_attr( $k ) . '_filter_background_selected_color" /> <label for="' . esc_attr( $k ) . '_filter_background_selected_color"></label>';
                                    $html .= '<p class="description">' . __('' , 'ut_portfolio_lang') . '</p></td>';

                                    $html .= '<td class="ut-label-cell">' . __('Border Color' , 'ut_portfolio_lang') . '</td>';
                                    $html .= '<td><input value="' . $this->validate_value( $data , 'filter_border_selected_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[filter_border_selected_color]" id="' . esc_attr( $k ) . '_filter_border_selected_color" /> <label for="' . esc_attr( $k ) . '_filter_border_selected_color"></label>';
                                    $html .= '<p class="description">' . __('' , 'ut_portfolio_lang') . '</p></td>';


                                $html .= '</tr>';

                            $html .= '</tbody></table>';

                        $html .= '</td>';

                    $html .= '</tr>';

					$html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Deactivate Filter Border' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><select autocomplete="off" name="' . esc_attr( $k ) . '[filter_deactivate_border]" id="' . esc_attr( $k ) . '_filter_deactivate_border">';
                            $html .= '<option value="no" ' . $this->selected_array( 'no' , 'filter_deactivate_border' , $data ) . '>' . __('no, thanks!' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="yes" ' . $this->selected_array( 'yes' , 'filter_deactivate_border' , $data ) . '>' . __('yes, please!' , 'ut_portfolio_lang') . '</option>';
                        $html .= '</select>';
                        $html .= '<p class="description">' . __('This option also removed the padding.' , 'ut_portfolio_lang') . '</p>';
                    $html .= '</tr>';

					/* filter font size */
					$html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Filter Font Size' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><select autocomplete="off" name="' . esc_attr( $k ) . '[filter_font_size]" id="' . esc_attr( $k ) . '_filter_font_size">';
                            $html .= '<option value="default" ' . $this->selected_array( 'default' , 'filter_font_size' , $data ) . '>' . __('Default Font Size' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="inherit" ' . $this->selected_array( 'inherit' , 'filter_font_size' , $data ) . '>' . __('Inherit from Body' , 'ut_portfolio_lang') . '</option>';
                        $html .= '</select>';
                        $html .= '<p class="description">' . __('By default the filter has a font size of 12. You can optionally inherit it from your body font.' , 'ut_portfolio_lang') . '</p>';
                    $html .= '</tr>';

                    /* filter font-weight */
                    $html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Filter Font Weight' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><select autocomplete="off" name="' . esc_attr( $k ) . '[filter_font_weight]" id="' . esc_attr( $k ) . '_style">';
                            $html .= '<option value="normal" ' . $this->selected_array( 'normal' , 'filter_font_weight' , $data ) . '>' . __('normal' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="bold" ' . $this->selected_array( 'bold' , 'filter_font_weight' , $data ) . '>' . __('bold' , 'ut_portfolio_lang') . '</option>';
							$html .= '<option value="100" ' . $this->selected_array( '100' , 'filter_font_weight' , $data ) . '>' . __('100' , 'ut_portfolio_lang') . '</option>';
							$html .= '<option value="200" ' . $this->selected_array( '200' , 'filter_font_weight' , $data ) . '>' . __('200' , 'ut_portfolio_lang') . '</option>';
							$html .= '<option value="300" ' . $this->selected_array( '300' , 'filter_font_weight' , $data ) . '>' . __('300' , 'ut_portfolio_lang') . '</option>';
							$html .= '<option value="400" ' . $this->selected_array( '400' , 'filter_font_weight' , $data ) . '>' . __('400' , 'ut_portfolio_lang') . '</option>';
							$html .= '<option value="500" ' . $this->selected_array( '500' , 'filter_font_weight' , $data ) . '>' . __('500' , 'ut_portfolio_lang') . '</option>';
							$html .= '<option value="600" ' . $this->selected_array( '600' , 'filter_font_weight' , $data ) . '>' . __('600' , 'ut_portfolio_lang') . '</option>';
							$html .= '<option value="700" ' . $this->selected_array( '700' , 'filter_font_weight' , $data ) . '>' . __('700' , 'ut_portfolio_lang') . '</option>';
							$html .= '<option value="800" ' . $this->selected_array( '800' , 'filter_font_weight' , $data ) . '>' . __('800' , 'ut_portfolio_lang') . '</option>';
							$html .= '<option value="900" ' . $this->selected_array( '900' , 'filter_font_weight' , $data ) . '>' . __('900' , 'ut_portfolio_lang') . '</option>';
                        $html .= '</select>';
                        $html .= '<p class="description">' . __('Select your desired portfolio filter font weight.' , 'ut_portfolio_lang') . '</p>';
                    $html .= '</tr>';

					$html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Filter Text Transform' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><select autocomplete="off" name="' . esc_attr( $k ) . '[filter_text_transform]" id="' . esc_attr( $k ) . '_text_transform">';
                            $html .= '<option value="uppercase" ' . $this->selected_array( 'uppercase' , 'filter_text_transform' , $data ) . '>' . __('uppercase' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="lowercase" ' . $this->selected_array( 'lowercase' , 'filter_text_transform' , $data ) . '>' . __('lowercase' , 'ut_portfolio_lang') . '</option>';
							$html .= '<option value="capitalize" ' . $this->selected_array( 'capitalize' , 'filter_text_transform' , $data ) . '>' . __('capitalize' , 'ut_portfolio_lang') . '</option>';
							$html .= '<option value="none" ' . $this->selected_array( 'none' , 'filter_text_transform' , $data ) . '>' . __('none' , 'ut_portfolio_lang') . '</option>';
                        $html .= '</select>';
                        $html .= '<p class="description">' . __('Select your desired portfolio filter text transform.' , 'ut_portfolio_lang') . '</p>';
                    $html .= '</tr>';

                    $html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Filter Font Style' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><select autocomplete="off" name="' . esc_attr( $k ) . '[filter_font_style]" id="' . esc_attr( $k ) . '_font_style">';
                            $html .= '<option value="normal" ' . $this->selected_array( 'normal' , 'filter_font_style' , $data ) . '>' . __('normal' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="italic" ' . $this->selected_array( 'italic' , 'filter_font_style' , $data ) . '>' . __('italic' , 'ut_portfolio_lang') . '</option>';
                        $html .= '</select>';
                        $html .= '<p class="description">' . __('Select your desired portfolio filter font style.' , 'ut_portfolio_lang') . '</p>';
                    $html .= '</tr>';

                    /* letter spacing */
                    $html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Filter Letter Spacing' , 'ut_portfolio_lang') . '</td>';
					    $html .= '<td><div class="ut-range-slider-group ut-jquery-ui">';
						$html .= '<div data-state="' . $this->validate_value( $data , 'filter_letter_spacing' , "0" ) . '" class="ut-letter-spacing-slider"></div>';
						$html .= '<span class="ut-letter-spacing-value">' . $this->validate_value( $data , 'filter_letter_spacing' , "0" ) . '</span>';
						$html .= '<input value="' . $this->validate_value( $data , 'filter_letter_spacing' , "0" ) . '" class="regular-text ut-hidden-slider-input" name="' . esc_attr( $k ) . '[filter_letter_spacing]" id="' . esc_attr( $k ) . '_filter_letter_spacing" />';
						$html .= '</div>';
						$html .= '</td>';
                    $html .= '</tr>';

					/* style */
                    $html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Portfolio Visual Style' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><select autocomplete="off" name="' . esc_attr( $k ) . '[style]" id="' . esc_attr( $k ) . '_style">';
                            $html .= '<option value="style_one" ' . $this->selected_array( 'style_one' , 'style' , $data ) . '>' . __('Style 1 ( only images )' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="style_two" ' . $this->selected_array( 'style_two' , 'style' , $data ) . '>' . __('Style 2 ( images with title ) ' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="style_three" ' . $this->selected_array( 'style_three' , 'style' , $data ) . '>' . __('Style 3 ( image tilt ) ' , 'ut_portfolio_lang') . '</option>';
                        $html .= '</select>';
                        $html .= '<p class="description">' . __('Select your desired portfolio style.' , 'ut_portfolio_lang') . '</p>';
                    $html .= '</tr>';

	                /* Caption Visibility  */
	                $html .= '<tr valign="top" class="ut-settings-row">';
		                $html .= '<td class="ut-label-cell">' . __('Portfolio Caption Visibility' , 'ut_portfolio_lang') . '</td>';
		                $html .= '<td><select autocomplete="off" name="' . esc_attr( $k ) . '[caption_visibility]" id="' . esc_attr( $k ) . '_caption_visibility">';
			                $html .= '<option value="on_hover" ' . $this->selected_array( 'on_hover' , 'caption_visibility' , $data ) . '>' . __('On Hover' , 'ut_portfolio_lang') . '</option>';
			                $html .= '<option value="permanent" ' . $this->selected_array( 'permanent' , 'caption_visibility' , $data ) . '>' . __('Permanent' , 'ut_portfolio_lang') . '</option>';
		                $html .= '</select>';
		                $html .= '<p class="description">' . __('Select your desired portfolio caption visibility.' , 'ut_portfolio_lang') . '</p></td>';
	                $html .= '</tr>';

					$html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Portfolio Animation In Style' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><select autocomplete="off" name="' . esc_attr( $k ) . '[animation_in]" id="' . esc_attr( $k ) . '_animation_in">';
                            $html .= '<option value="portfolioFadeIn" ' . $this->selected_array( 'portfolioFadeIn' , 'animation_in' , $data ) . '>' . __('Fade In' , 'ut_portfolio_lang') . '</option>';
							$html .= '<option value="portfolioFadeInUp" ' . $this->selected_array( 'portfolioFadeInUp' , 'animation_in' , $data ) . '>' . __('Fade In Up' , 'ut_portfolio_lang') . '</option>';
							$html .= '<option value="portfolioZoomIn" ' . $this->selected_array( 'portfolioZoomIn' , 'animation_in' , $data ) . '>' . __('Zoom In' , 'ut_portfolio_lang') . '</option>';
                        $html .= '</select>';
                        $html .= '<p class="description">' . __('Select your portfolio animation for appearing items.' , 'ut_portfolio_lang') . '</p>';
                    $html .= '</tr>';

					$html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Portfolio Animation Out Style' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><select autocomplete="off" name="' . esc_attr( $k ) . '[animation_out]" id="' . esc_attr( $k ) . '_animation_out">';
                            $html .= '<option value="portfolioZoomOut" ' . $this->selected_array( 'portfolioZoomOut' , 'animation_out' , $data ) . '>' . __('Zoom Out' , 'ut_portfolio_lang') . '</option>';
							$html .= '<option value="portfolioFadeOut" ' . $this->selected_array( 'portfolioFadeOut' , 'animation_out' , $data ) . '>' . __('Fade Out' , 'ut_portfolio_lang') . '</option>';
                        $html .= '</select>';
                        $html .= '<p class="description">' . __('Select your portfolio animation for disappearing items.' , 'ut_portfolio_lang') . '</p>';
                    $html .= '</tr>';

					/* gutter
                    $html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Gutter' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><div class="ut-checkbox"><input name="' . esc_attr( $k ) . '[gutter]" type="checkbox" id="' . esc_attr( $k ) . '_gutter" ' . $this->checked_array( 'gutter' , $data ) . ' /> <label for="' . esc_attr( $k ) . '_gutter"></label></div>';
					    $html .= '<p class="description">' . __('Adds a small gutter between the portfolio images.' , 'ut_portfolio_lang') . '</p></td>';
					$html .= '</tr>'; */

                    /* gutter size
                    $html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Gutter Size' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><select autocomplete="off" name="' . esc_attr( $k ) . '[gutter_size]" id="' . esc_attr( $k ) . '_gutter_size">';
                            $html .= '<option value="1" ' . $this->selected_array( '1' , 'gutter_size' , $data ) . '>' . __('1x' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="2" ' . $this->selected_array( '2' , 'gutter_size' , $data ) . '>' . __('2x' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="3" ' . $this->selected_array( '3' , 'gutter_size' , $data ) . '>' . __('3x' , 'ut_portfolio_lang') . '</option>';
                        $html .= '</select>';
                        $html .= '<p class="description">' . __('Select your desired gutter size.' , 'ut_portfolio_lang') . '</p>';
                    $html .= '</tr>'; */

                    /* gutter shadow
                    $html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Gutter Shadow' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td><div class="ut-checkbox"><input name="' . esc_attr( $k ) . '[gutter_shadow]" type="checkbox" id="' . esc_attr( $k ) . '_gutter_shadow" ' . $this->checked_array( 'gutter_shadow' , $data ) . ' /> <label for="' . esc_attr( $k ) . '_gutter_shadow"></label></div>';
					    $html .= '<p class="description">' . __('Adds a small gutter shadow between the portfolio images.' , 'ut_portfolio_lang') . '</p></td>';
					$html .= '</tr>'; */

					/* image cropping */
					$html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Image Cropping' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td>';

                        $html .= '<div id="ut_cropping_settings_gallery" class="ut-cropping-size">';

                        $html .= '<label for="' . esc_attr( $k ) . '_crop_size_x">' . __('Width' , 'ut_portfolio_lang') . '</label>';
                        $html .= '<input type="text" value="' . $this->validate_value( $data , 'crop_size_x' ) . '" class="small-text code" name="' . esc_attr( $k ) . '[crop_size_x]" id="' . esc_attr( $k ) . '_crop_size_x" />';

                        $html .= '<label for="' . esc_attr( $k ) . '_crop_size_y">' . __('Height' , 'ut_portfolio_lang') . '</label>';
                        $html .= '<input type="text" value="' . $this->validate_value( $data , 'crop_size_y' ) . '" class="small-text code" name="' . esc_attr( $k ) . '[crop_size_y]" id="' . esc_attr( $k ) . '_crop_size_y" />';

                        $html .= '</div>';

                        $html .= '<p style="margin-bottom:30px;" class="description">' . __('Default: 1536x1024.' , 'ut_portfolio_lang') . '</p>';

                        /* hard or softcrop */
                        $html .= '<div class="ut-checkbox"><input name="' . esc_attr( $k ) . '[hardcrop]" type="checkbox" id="' . esc_attr( $k ) . '_hardcrop" ' . $this->checked_array( 'hardcrop' , $data ) . ' /> <label for="' . esc_attr( $k ) . '_hardcrop"></label></div>';
                        $html .= '<p style="max-width:550px;" class="description">' . __('<strong>Activate Image Soft Cropping?</strong> What does Soft Crop mean? A soft crop will never cut off any of the image, it will scale the image down until it fits within the dimensions specified, maintaining its original aspect ratio. What does Hard Crop mean? The image will be scaled and then cropped to the exact dimensions you have specified. Depending on the aspect ratio of the image in relation to the crop size, it might happen that the image will be cut off. ' , 'ut_portfolio_lang') . '</p>';
                        $html .= '</td>';

					$html .= '</tr>';

                    /* image cropping */
					$html .= '<tr valign="top" class="ut-settings-row">';

                        $html .= '<td class="ut-label-cell">' . __('Image Display Size' , 'ut_portfolio_lang') . '</td>';

                        $html .= '<td>';

                        $html .= '<div class="ut-display-size">';

                            $html .= '<div>';
                                $html .= '<label for="' . esc_attr( $k ) . '_desktop_base_height">' . __('Desktop Large Base Height (everything above 1920px)' , 'ut_portfolio_lang') . '</label>';
                                $html .= '<input type="text" value="' . $this->validate_value( $data , 'desktop_base_height' ) . '" class="small-text code" name="' . esc_attr( $k ) . '[desktop_base_height]" id="' . esc_attr( $k ) . '_desktop_base_height" />';
                                $html .= '<p class="description">' . __('Default: 600' , 'ut_portfolio_lang') . '</p>';
                            $html .= '</div>';

							$html .= '<div>';
                                $html .= '<label for="' . esc_attr( $k ) . '_desktop_medium_base_height">' . __('Desktop Medium Base Height (1537px to 1920px)' , 'ut_portfolio_lang') . '</label>';
                                $html .= '<input type="text" value="' . $this->validate_value( $data , 'desktop_medium_base_height' ) . '" class="small-text code" name="' . esc_attr( $k ) . '[desktop_medium_base_height]" id="' . esc_attr( $k ) . '_desktop_medium_base_height" />';
                                $html .= '<p class="description">' . __('Default: 450' , 'ut_portfolio_lang') . '</p>';
                            $html .= '</div>';

							$html .= '<div>';
                                $html .= '<label for="' . esc_attr( $k ) . '_desktop_small_base_height">' . __('Desktop Small Base Height (1024px to 1536px)' , 'ut_portfolio_lang') . '</label>';
                                $html .= '<input type="text" value="' . $this->validate_value( $data , 'desktop_small_base_height' ) . '" class="small-text code" name="' . esc_attr( $k ) . '[desktop_small_base_height]" id="' . esc_attr( $k ) . '_desktop_small_base_height" />';
                                $html .= '<p class="description">' . __('Default: 350' , 'ut_portfolio_lang') . '</p>';
                            $html .= '</div>';

                            $html .= '<div>';
                                $html .= '<label for="' . esc_attr( $k ) . '_tablet_base_height">' . __('Tablet Height' , 'ut_portfolio_lang') . '</label>';
                                $html .= '<input type="text" value="' . $this->validate_value( $data , 'tablet_base_height' ) . '" class="small-text code" name="' . esc_attr( $k ) . '[tablet_base_height]" id="' . esc_attr( $k ) . '_tablet_base_height" />';
                                $html .= '<p class="description">' . __('Default: 450' , 'ut_portfolio_lang') . '</p>';
                            $html .= '</div>';

                            $html .= '<div>';
                                $html .= '<label for="' . esc_attr( $k ) . '_mobile_base_height">' . __('Mobile Height' , 'ut_portfolio_lang') . '</label>';
                                $html .= '<input type="text" value="' . $this->validate_value( $data , 'mobile_base_height' ) . '" class="small-text code" name="' . esc_attr( $k ) . '[mobile_base_height]" id="' . esc_attr( $k ) . '_mobile_base_height" />';
                                $html .= '<p class="description">' . __('Default: 400' , 'ut_portfolio_lang') . '</p>';
                            $html .= '</div>';

                        $html .= '</div>';

                        $html .= '</td>';

					$html .= '</tr>';
                    
                    /* end wp table */
                    $html .= '</tbody></table>';
                    
                    /* end panel & section */ 
                    $html .= '</div></section>' . "\n";
                    
                        
				} elseif( $v['type'] == 'portfolio_settings' ) {
					
					$data = maybe_unserialize( $data );
					
                    $html .= '<h2 id="ut_no_type_options" class="ut-option-section ut-section-title" style="border-bottom:none;">'. __('No Portfolio Showcase Type selected. Please select one above!' , 'ut_portfolio_lang') .'</h2>';
                    
                    
                    $html .= '<section id="' . esc_attr( $k ) . '" style="padding-top:20px;">';
                    $html .= '<h2 class="ut-section-title">' . $v['name'] . '</h2>';
                    $html .= '<div class="ut-section-panel">';
                    
					/* start wp table */
                    $html .= '<table class="form-table"><tbody>'; 
					
					/* text color */
                    $html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Portfolio Background Color' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell"><input value="' . $this->validate_value( $data , 'background_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[background_color]" id="' . esc_attr( $k ) . '_background_color" /> <label for="' . esc_attr( $k ) . '_background_color"></label>';
					    $html .= '<p class="description">' . __('Portfolio background color.' , 'ut_portfolio_lang') . '</p></td>';
                    $html .= '</tr>';
					
					/* text color */
                    $html .= '<tr id="ut-general-title-color" valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Title Color' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell"><input value="' . $this->validate_value( $data , 'text_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[text_color]" id="' . esc_attr( $k ) . '_text_color" /> <label for="' . esc_attr( $k ) . '_text_color"></label>';
                        $html .= '<p class="description">' . __('Portfolio title color for overlay box.' , 'ut_portfolio_lang') . '</p></td>';
                    $html .= '</tr>';
                    
                    /* title background */
                    $html .= '<tr id="ut-general-title-background" valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Show Title Background' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell"><select autocomplete="off" name="' . esc_attr( $k ) . '[title_background]" id="' . esc_attr( $k ) . '_title_background">';
                            $html .= '<option value="on" ' . $this->selected_array( 'on' , 'title_background' , $data ) . '>' . __('show' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="off" ' . $this->selected_array( 'off' , 'title_background' , $data ) . '>' . __('hide' , 'ut_portfolio_lang') . '</option>';
                        $html .= '</select></td>';
                    $html .= '</tr>';

                    $html .= '<tr id="ut-general-title-background-color" valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Title Background Color' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell"><input value="' . $this->validate_value( $data , 'title_background_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[title_background_color]" id="' . esc_attr( $k ) . '_title_background_color" /> <label for="' . esc_attr( $k ) . '_title_background_color"></label>';
                        $html .= '<p class="description">' . __('Portfolio title background color only for portfolio visual style 2! Does not work with Grid Gallery!' , 'ut_portfolio_lang') . '</p></td>';
                    $html .= '</tr>';

                    $html .= '<tr id="ut-general-title-alignment" valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Title Alignment' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell"><select autocomplete="off" name="' . esc_attr( $k ) . '[title_align]" id="' . esc_attr( $k ) . '_title_align">';
                            $html .= '<option value="center" ' . $this->selected_array( 'center' , 'title_align' , $data ) . '>' . __('center' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="left" ' . $this->selected_array( 'left' , 'title_align' , $data ) . '>' . __('left' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="right" ' . $this->selected_array( 'right' , 'title_align' , $data ) . '>' . __('right' , 'ut_portfolio_lang') . '</option>';
                        $html .= '</select></td>';
                    $html .= '</tr>';

                    /* text color */
                    $html .= '<tr id="ut-general-caption-content-color" valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Category and Custom Text and Plus Color' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell"><input value="' . $this->validate_value( $data , 'category_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[category_color]" id="' . esc_attr( $k ) . '_category_color" /> <label for="' . esc_attr( $k ) . '_category_color"></label>';
					    $html .= '<p class="description">' . __('Portfolio category color for overlay box.' , 'ut_portfolio_lang') . '</p></td>';
                    $html .= '</tr>';
					
                    /* title and category position */
                    $html .= '<tr id="ut-general-caption-position" valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Caption Position' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell"><select autocomplete="off" name="' . esc_attr( $k ) . '[caption_position]" id="' . esc_attr( $k ) . '_caption_position">';
                            
                            if( !isset( $data['caption_position'] ) || empty( $data['caption_position'] ) ) {
                                $data['caption_position'] = 'middle-center';
                            }
                            
                            $html .= '<option value="top-left" ' . $this->selected_array( 'top-left' , 'caption_position' , $data ) . '>' . __('Top Left' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="top-center" ' . $this->selected_array( 'top-center' , 'caption_position' , $data ) . '>' . __('Top Center' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="top-right" ' . $this->selected_array( 'top-right' , 'caption_position' , $data ) . '>' . __('Top Right' , 'ut_portfolio_lang') . '</option>';
                            
                            $html .= '<option value="middle-left" ' . $this->selected_array( 'middle-left' , 'caption_position' , $data ) . '>' . __('Center Left' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="middle-center" ' . $this->selected_array( 'middle-center' , 'caption_position' , $data ) . '>' . __('Center Center' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="middle-right" ' . $this->selected_array( 'middle-right' , 'caption_position' , $data ) . '>' . __('Center Right' , 'ut_portfolio_lang') . '</option>';
                            
                            $html .= '<option value="bottom-left" ' . $this->selected_array( 'bottom-left' , 'caption_position' , $data ) . '>' . __('Bottom Left' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="bottom-center" ' . $this->selected_array( 'bottom-center' , 'caption_position' , $data ) . '>' . __('Bottom Center' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="bottom-right" ' . $this->selected_array( 'bottom-right' , 'caption_position' , $data ) . '>' . __('Bottom Right' , 'ut_portfolio_lang') . '</option>';
                            
                        $html .= '</select>';
                        $html .= '<p class="description">' . __('The Caption appears on hover and contains Title and Category.' , 'ut_portfolio_lang') . '</p>';
                    $html .= '</tr>';
                                        
					/* hover color */
                    $html .= '<tr id="ut-general-hover-color" valign="top" class="ut-settings-row">';
						$html .= '<div class="ut-minicolors-wrap">';
							$html .= '<td class="ut-label-cell">' . __('Hover Color' , 'ut_portfolio_lang') . '</td>';							
							$html .= '<td class="ut-option-cell">';
					
								$html .= '<input data-mode="gradient" type="text" name="' . esc_attr( $k ) . '[hover_color]" id="' . esc_attr( $k ) . '_hover_color" value="' . $this->validate_value( $data , 'hover_color' ) . '" class="ut-ui-form-input ut-gradient-picker" />';
								$html .= '<span class="ut-minicolors-swatch" style="background-color:' . $this->validate_value( $data , 'hover_color' ) . ';"></span>';            

							$html .= '</td>';
						$html .= '</div>';
					$html .= '</tr>';
					
					/* hover opacity */
                    $html .= '<tr id="ut-general-hover-color-opacity" valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Hover Color Opacity' , 'ut_portfolio_lang') . '</td>';
					    $html .= '<td class="ut-option-cell"><div class="ut-range-slider-group ut-jquery-ui">';
						$html .= '<div data-state="' . $this->validate_value( $data , 'hover_opacity' , "0.8" ) . '" class="ut-opacity-slider"></div>';
						$html .= '<span class="ut-opacity-value">' . $this->validate_value( $data , 'hover_opacity' , "0.8" ) . '</span>';
						$html .= '<input value="' . $this->validate_value( $data , 'hover_opacity' , "0.8" ) . '" class="regular-text ut-hidden-slider-input" name="' . esc_attr( $k ) . '[hover_opacity]" id="' . esc_attr( $k ) . '_hover_opacity" />';
						$html .= '</div>';
						$html .= '<p class="description">' . __('Only takes effect if Hover Color is HEX. This option is deprecated and kept for fallback reasons only.' , 'ut_portfolio_lang') . '</p></td>';
						$html .= '</td>';
                    $html .= '</tr>';
					
					/* image style */
                    $html .= '<tr id="ut-general-image-style" valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Image Style' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell"><select autocomplete="off" name="' . esc_attr( $k ) . '[image_style]" id="' . esc_attr( $k ) . '_image_style">';
                            $html .= '<option value="ut-square" ' . $this->selected_array( 'ut-square' , 'image_style' , $data ) . '>' . __('Square' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="ut-rounded" ' . $this->selected_array( 'ut-rounded' , 'image_style' , $data ) . '>' . __('Rounded' , 'ut_portfolio_lang') . '</option>';
                        $html .= '</select>';
                        $html .= '<p class="description">' . __('Choose between squared or rounded image styles.' , 'ut_portfolio_lang') . '</p></td>';
                    $html .= '</tr>';
					
                    /* border radius */
                    $html .= '<tr id="ut-general-border-radius" valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Border Radius' , 'ut_portfolio_lang') . '</td>';
					    $html .= '<td class="ut-option-cell"><div class="ut-range-slider-group ut-jquery-ui">';
						$html .= '<div data-state="' . $this->validate_value( $data , 'border_radius' , "4" ) . '" class="ut-border-radius-slider"></div>';
						$html .= '<span class="ut-border-radius-value">' . $this->validate_value( $data , 'border_radius' , "4" ) . '</span>';
						$html .= '<input value="' . $this->validate_value( $data , 'border_radius' , "4" ) . '" class="regular-text ut-hidden-slider-input" name="' . esc_attr( $k ) . '[border_radius]" id="' . esc_attr( $k ) . '_border_radius" />';
						$html .= '</div>';
                        $html .= '<p class="description">' . __('Only work for image style "Rounded".' , 'ut_portfolio_lang') . '</p></td>';
                    $html .= '</tr>';

	                /* Portfolio Settings */
	                $html .= '<tr id="ut-general-portfolio-details" valign="top" class="ut-settings-row">';
	                    $html.= '<td class="ut-headline-cell" colspan="4"><h3>' . __('Portfolio Details' , 'ut_portfolio_lang') . '</h3></td>';
	                $html .= '</tr>';

					/* popup style */
                    $html .= '<tr id="ut-general-portfolio-style" valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Detail Style' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell"><select id="ut-general-detail-style" autocomplete="off" name="' . esc_attr( $k ) . '[detail_style]" id="' . esc_attr( $k ) . '_detail_style">';
                            $html .= '<option value="popup" ' . $this->selected_array( 'popup' , 'detail_style' , $data ) . '>' . __('Popup (Lightbox)' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="slideup" ' . $this->selected_array( 'slideup' , 'detail_style' , $data ) . '>' . __('Slideup' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="internal" ' . $this->selected_array( 'internal' , 'detail_style' , $data ) . '>' . __('Separate Portfolio Page' , 'ut_portfolio_lang') . '</option>';                            
                        $html .= '</select>';
                        $html .= '<p class="description">' . __('Select your portfolio detail style.' , 'ut_portfolio_lang') . '</p>';
                    $html .= '</tr>';
                    
                    
                    $html .= '<tr id="ut-general-title-slideup" valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Show Portfolio Title in SlideUp ?' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell"><select autocomplete="off" name="' . esc_attr( $k ) . '[portfolio_title]" id="' . esc_attr( $k ) . '_portfolio_title">';
                            $html .= '<option value="on" ' . $this->selected_array( 'on' , 'portfolio_title' , $data ) . '>' . __('yes, please!' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="off" ' . $this->selected_array( 'off' , 'portfolio_title' , $data ) . '>' . __('no, thanks!' , 'ut_portfolio_lang') . '</option>';
                        $html .= '</select>';
                        $html .= '<p class="description">' . __('Allows you to hide the Portfolio Title inside the SlideUp Portfolio.' , 'ut_portfolio_lang') . '</p>';
                    $html .= '</tr>';
                    
                    $html .= '<tr id="ut-general-title-slideup-content" valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('SlideUp Content Width ?' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell"><select autocomplete="off" name="' . esc_attr( $k ) . '[slideup_width]" id="' . esc_attr( $k ) . 'slideup_width">';
                            $html .= '<option value="centered" ' . $this->selected_array( 'centered' , 'slideup_width' , $data ) . '>' . __('centered' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="fullwidth" ' . $this->selected_array( 'fullwidth' , 'slideup_width' , $data ) . '>' . __('fullwidth' , 'ut_portfolio_lang') . '</option>';
                        $html .= '</select>';
                        $html .= '<p class="description">' . __('Allows you to change the width of the content inside the slideup.' , 'ut_portfolio_lang') . '</p>';
                    $html .= '</tr>';

                    /* end wp table */
                    $html .= '</tbody></table>';

                    $html .= '<table id="ut-general-slideup-color" class="form-table"><tbody>';

                    $html .= '<tr valign="top" class="ut-settings-row">';

                        $html.= '<td class="ut-headline-cell" colspan="8"><h3>' . __('Slide Up Colors' , 'ut_portfolio_lang') . '</h3></td>';

                    $html .= '</tr>';

                    /* slideup colors */
                    $html .= '<tr valign="top" class="ut-settings-row">';

                        $html .= '<td class="ut-label-cell">' . __('Loader Arrow Color' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell">';
                            $html .= '<input value="' . $this->validate_value( $data , 'slideup_loader_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[slideup_loader_color]" id="' . esc_attr( $k ) . '_slideup_loader_color" />';
                        $html .= '</td>';

                        $html .= '<td class="ut-label-cell">' . __('Loader Background Color' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell">';
                            $html .= '<input value="' . $this->validate_value( $data , 'slideup_loader_background_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[slideup_loader_background_color]" id="' . esc_attr( $k ) . '_slideup_loader_background_color" />';
                        $html .= '</td>';

                        $html .= '<td class="ut-label-cell">' . __('Next and Previous Arrow Color' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell">';
                            $html .= '<input value="' . $this->validate_value( $data , 'slideup_arrow_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[slideup_arrow_color]" id="' . esc_attr( $k ) . '_slideup_arrow_color" />';
                        $html .= '</td>';

                        $html .= '<td class="ut-label-cell">' . __('Next and Previous Arrow Hover Color' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell">';
                            $html .= '<input value="' . $this->validate_value( $data , 'slideup_arrow_hover_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[slideup_arrow_hover_color]" id="' . esc_attr( $k ) . '_slideup_arrow_hover_color" />';
                        $html .= '</td>';

                    $html .= '</tr>';

                    $html .= '<tr valign="top" class="ut-settings-row">';

                        $html .= '<td class="ut-label-cell">' . __('Close Icon Color' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell">';
                            $html .= '<input value="' . $this->validate_value( $data , 'slideup_close_icon_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[slideup_close_icon_color]" id="' . esc_attr( $k ) . '_slideup_close_icon_color" />';
                        $html .= '</td>';

                        $html .= '<td class="ut-label-cell">' . __('Close Icon Hover Color' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell">';
                            $html .= '<input value="' . $this->validate_value( $data , 'slideup_close_icon_hover_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[slideup_close_icon_hover_color]" id="' . esc_attr( $k ) . '_slideup_close_icon_hover_color" />';
                        $html .= '</td>';

                        $html .= '<td class="ut-label-cell">' . __('Close Icon Background Color' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell">';
                            $html .= '<input value="' . $this->validate_value( $data , 'slideup_close_background_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[slideup_close_background_color]" id="' . esc_attr( $k ) . '_slideup_close_background_color" />';
                        $html .= '</td>';

                        $html .= '<td class="ut-label-cell">' . __('Close Icon Background Hover Color' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell">';
                            $html .= '<input value="' . $this->validate_value( $data , 'slideup_close_background_hover_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[slideup_close_background_hover_color]" id="' . esc_attr( $k ) . '_slideup_close_background_hover_color" />';
                        $html .= '</td>';

                    $html .= '</tr>';

                    $html .= '<tr valign="top" class="ut-settings-row">';

                        $html .= '<td class="ut-label-cell">' . __('Gallery Next and Previous Icon Color' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell">';
                            $html .= '<input value="' . $this->validate_value( $data , 'slideup_gallery_icon_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[slideup_gallery_icon_color]" id="' . esc_attr( $k ) . '_slideup_gallery_icon_color" />';
                        $html .= '</td>';

                        $html .= '<td class="ut-label-cell">' . __('Gallery Next and Previous Icon Hover Color' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell">';
                            $html .= '<input value="' . $this->validate_value( $data , 'slideup_gallery_icon_hover_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[slideup_gallery_icon_hover_color]" id="' . esc_attr( $k ) . '_slideup_gallery_icon_hover_color" /> <label for="' . esc_attr( $k ) . '_slideup_gallery_icon_hover_color">';
                        $html .= '</td>';

                        $html .= '<td class="ut-label-cell">' . __('Gallery Next and Previous Icon Background Color' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell">';
                            $html .= '<input value="' . $this->validate_value( $data , 'slideup_gallery_icon_background_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[slideup_gallery_icon_background_color]" id="' . esc_attr( $k ) . '_slideup_gallery_icon_background_color" /> <label for="' . esc_attr( $k ) . '_slideup_gallery_icon_background_color">';
                        $html .= '</td>';

                        $html .= '<td class="ut-label-cell">' . __('Gallery Next and Previous Icon Background Hover Color' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell">';
                            $html .= '<input value="' . $this->validate_value( $data , 'slideup_gallery_icon_background_hover_color' ) . '" data-mode="rgb" autocomplete="off" class="color-picker-hex wp-color-picker ut_color_picker" name="' . esc_attr( $k ) . '[slideup_gallery_icon_background_hover_color]" id="' . esc_attr( $k ) . '_slideup_gallery_icon_background_hover_color" />';
                        $html .= '</td>';

                    $html .= '</tr>';

                    /* end wp table */
                    $html .= '</tbody></table>';


                    $html .= '<table class="form-table"><tbody>';

                    $html .= '<tr valign="top" class="ut-settings-row">';

                        $html.= '<td class="ut-headline-cell" colspan="2"><h3>' . __('Miscellaneous' , 'ut_portfolio_lang') . '</h3></td>';

                    $html .= '</tr>';
                    
					/* posts per page */
                    $ppp = $this->validate_value( $data , 'posts_per_page' );
                    $ppp = empty( $ppp ) ? '-1' : $ppp;
                    
                    $html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Portfolios per Page' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell"><input type="text" value="' . $ppp . '" class="regular-text" name="' . esc_attr( $k ) . '[posts_per_page]" id="' . esc_attr( $k ) . '_posts_per_page" /> <label for="' . esc_attr( $k ) . '_posts_per_page"></label>';
					    $html .= '<p class="description">' . __('Portfolio images per page (default -1 for unlimted posts).' , 'ut_portfolio_lang') . '</p></td>';
                    $html .= '</tr>';
					
					/* optional class */
                    $html .= '<tr valign="top" class="ut-settings-row">';
                        $html .= '<td class="ut-label-cell">' . __('Optional Class' , 'ut_portfolio_lang') . '</td>';
                        $html .= '<td class="ut-option-cell"><input type="text" value="' . $this->validate_value( $data , 'optional_class' ) . '" class="regular-text" name="' . esc_attr( $k ) . '[optional_class]" id="' . esc_attr( $k ) . '_optional_class" /> <label for="' . esc_attr( $k ) . '_optional_class"></label>';
					    $html .= '<p class="description">' . __('Add an individual class to this portfolio.' , 'ut_portfolio_lang') . '</p></td>';
                    $html .= '</tr>';
					
					/* end wp table */
                    $html .= '</tbody></table>';
					
                    /* end panel & section */
                    $html .= '</div></section>' . "\n";
					
				} elseif( $v['type'] == 'portfolio_type' ) {
					
                    $html .= '<div class="ut-admin-info-box">';
                    
                        $html .= '<h2 class="ut-section-title">' . $v['name'] . '</h2>';
                        $html .= '<div class="ut-section-panel">';
                        
                        $html .= '<select autocomplete="off" name="' . esc_attr( $k ) . '" id="' . esc_attr( $k ) . '">';
                            $html .= '<option value="ut_no_type">' . __('Choose Portfolio Type' , 'ut_portfolio_lang') . '</option>';
                            //$html .= '<option value="ut_showcase" ' . selected('ut_showcase' , $data , false) . '>' . __('Showcase Slider' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="ut_masonry" ' . selected('ut_masonry' , $data , false) . '>' . __('Grid Gallery' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="ut_gallery" ' . selected('ut_gallery' , $data , false) . '>' . __('Filterable Portfolio Gallery' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="ut_packery" ' . selected('ut_packery' , $data , false) . '>' . __('Filterable Portfolio Gallery (Packery)' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="ut_carousel" ' . selected('ut_carousel' , $data , false) . '>' . __('Portfolio Carousel' , 'ut_portfolio_lang') . '</option>';
                            $html .= '<option value="ut_react_carousel" ' . selected('ut_react_carousel' , $data , false) . '>' . __('Portfolio React Carousel' , 'ut_portfolio_lang') . '</option>';
                        $html .= '</select>';
                        
                        $html .= '<p>' . $v['description'] . '</p>';
                        
                        /* end panel & section */
                        $html .= '</div>' . "\n";
                        
                    $html .= '</div>' . "\n";                    
					
				} elseif( $v['type'] == 'checkbox' ) {
					
					$html .= '<h2 class="ut-section-title">' . $v['name'] . '</h2>';
					$html .= '<div class="ut-section-panel">';					
					
					$html .= '<div class="ut-checkbox"><input name="' . esc_attr( $k ) . '" type="checkbox" id="' . esc_attr( $k ) . '" ' . checked( 'on' , $data , false ) . ' /> <label for="' . esc_attr( $k ) . '"></label></div>';
					$html .= '<span class="description">' . $v['description'] . '</span>';
					
					$html .= '</div>' . "\n";
					
				} else {
					
					$html .= '<h2 class="ut-section-title">' . $v['name'] . '</h2>';
					$html .= '<div class="ut-section-panel">';
					$html .= '<label for="' . esc_attr( $k ) . '">' . $v['name'] . '</label><input name="' . esc_attr( $k ) . '" type="text" id="' . esc_attr( $k ) . '" class="regular-text" value="' . esc_attr( $data ) . '" />' . "\n";
					$html .= '<p class="description">' . $v['description'] . '</p>' . "\n";
					$html .= '</div>' . "\n";
					
				}

			}
            
            $html .= '</div>';
            
            $html .= '</td>' . "\n";
            $html .= '</tr>' . "\n";
            $html .= '</tbody>' . "\n";
			$html .= '</table>' . "\n";

		}
		
		echo $html;	
	}
	
	public function meta_box_content_info() {
		
		global $post_id;
		
		$info  = '<p><strong>' . __('United Themes - Portfolio Showcase Shortcode' , 'ut_portfolio_lang') . '</strong></p>';
		$info .= '<textarea wrap="off" readonly="readonly" class="ut-shortcode-code">[ut_showcase id="' . $post_id . '" name="' . esc_attr( get_the_title( $post_id ) ) . '"]</textarea>';
		
		echo $info;
	
	}
	
	public function meta_box_save( $post_id ) {
		
		global $post, $messages;
		
		// Verify nonce
		if ( ( get_post_type() != $this->token ) || isset( $_POST[ $this->token . '_nonce'] ) && ! wp_verify_nonce( $_POST[ $this->token . '_nonce'], plugin_basename( $this->dir ) ) ) {  
			return $post_id;  
		}

		// Verify user permissions
		if ( ! current_user_can( 'edit_post', $post_id ) ) { 
			return $post_id;
		}
		
		// Handle custom fields
		$field_data = $this->get_custom_fields_settings();
		$fields = array_keys( $field_data );
		
		foreach ( $fields as $f ) {
			
			if( isset( $_POST[$f] ) && !is_array($_POST[$f]) ) {
				${$f} = strip_tags( trim( $_POST[$f] ) );
			}
			
			if( isset( $_POST[$f] ) && is_array($_POST[$f]) ) {
				/* WordPress will serialize the data later on */
				${$f} = $_POST[$f];
			}
			
			// Escape the URLs.
			if ( 'url' == $field_data[$f]['type'] ) {
				${$f} = esc_url( ${$f} );
			}
			
			if ( empty( ${$f} ) ) { 
				delete_post_meta( $post_id , $f , get_post_meta( $post_id , $f , true ) );
			} else {
				update_post_meta( $post_id , $f , ${$f} );
			}
		}

	}

	public function enter_title_here( $title ) {
		if ( get_post_type() == $this->token ) {
			$title = __( 'Enter the post title here' , 'ut_portfolio_lang' );
		}
		return $title;
	}

	public function get_custom_fields_settings() {

		$fields = array();
		
		$fields['ut_portfolio_type'] = array(
		    'name' => __( 'United Themes - Portfolio Showcase Type' , 'ut_portfolio_lang' ),
		    'description' => __( 'Choose between 4 types of portfolio layouts.' , 'ut_portfolio_lang' ),
		    'type' => 'portfolio_type',
		    'default' => '',
		    'section' => 'plugin-data'
		);
        
        $fields['ut_showcase_usage'] = array(
		    'name' => __( 'United Themes - Portfolio Showcase Shortcode' , 'ut_portfolio_lang' ),
		    'description' => __( 'Simply copy this United Themes - Portfolio Showcase Shortcode and <br />place it inside the text editor of the section / page you like to display <br />the showcase in. ' , 'ut_portfolio_lang' ),
		    'type' => 'showcase_info',
		    'default' => '',
		    'section' => 'plugin-data'
		);
        
		$fields['ut_portfolio_categories'] = array(
		    'name' => __( 'Choose Portfolio Categories' , 'ut_portfolio_lang' ),
		    'description' => __( 'Only display portfolio images of these categories. Use the dark arrow to change the order of the categories, this will change the order of the ajax filter categories.' , 'ut_portfolio_lang' ),
		    'type' => 'taxonomy',
		    'default' => '',
		    'section' => 'plugin-data'
		);
		
        $fields['ut_showcase_options'] = array(
		    'name' => __( 'Showcase Options' , 'ut_portfolio_lang' ),
		    'description' => '',
		    'type' => 'showcase_options',
		    'default' => '',
		    'section' => 'plugin-data'
		);
        
        $fields['ut_carousel_options'] = array(
		    'name' => __( 'Portfolio Carousel Options' , 'ut_portfolio_lang' ),
		    'description' => '',
		    'type' => 'carousel_options',
		    'default' => '',
		    'section' => 'plugin-data'
		);

        $fields['ut_react_carousel_options'] = array(
            'name' => __( 'Portfolio React Carousel Options' , 'ut_portfolio_lang' ),
            'description' => '',
            'type' => 'react_carousel_options',
            'default' => '',
            'section' => 'plugin-data'
        );
		
		$fields['ut_masonry_options'] = array(
		    'name' => __( 'Grid Gallery Options' , 'ut_portfolio_lang' ),
		    'description' => '',
		    'type' => 'masonry_options',
		    'default' => '',
		    'section' => 'plugin-data'
		);
		
		$fields['ut_gallery_options'] = array(
		    'name' => __( 'Filterable Portfolio Gallery Options' , 'ut_portfolio_lang' ),
		    'description' => '',
		    'type' => 'gallery_options',
		    'default' => '',
		    'section' => 'plugin-data'
		);
        
        $fields['ut_packery_options'] = array(
		    'name' => __( 'Filterable Portfolio Gallery with Packery Options' , 'ut_portfolio_lang' ),
		    'description' => '',
		    'type' => 'packery_options',
		    'default' => '',
		    'section' => 'plugin-data'
		);		
		
        $fields['ut_portfolio_settings'] = array(
		    'name' => __( 'Portfolio General Settings' , 'ut_portfolio_lang' ),
		    'description' => '',
		    'type' => 'portfolio_settings',
		    'default' => '',
		    'section' => 'plugin-data'
		);
        
		return $fields;
		
	}
	
}