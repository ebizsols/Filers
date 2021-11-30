<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class UT_Menu_Manager {
	
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
		$this->token = 'ut-menu-manager';
               
		// Regsiter post type
		add_action( 'init' , array( $this, 'register_post_type' ) ); 
        
		if ( is_admin() ) {

			// Handle custom fields for post
			add_action( 'admin_menu', array( $this, 'meta_box_setup' ), 20 );
			add_action( 'save_post', array( $this, 'meta_box_save' ) );

			// Modify text in main title text box
			add_filter( 'enter_title_here', array( $this, 'enter_title_here' ) );

			// Display custom update messages for posts edits
			add_filter( 'post_updated_messages', array( $this, 'updated_messages' ) );

			// Handle post columns
			add_filter( 'manage_edit-' . $this->token . '_columns', array( $this, 'register_custom_column_headings' ), 10, 1 );
			add_action( 'manage_posts_custom_column', array( $this, 'register_custom_columns' ), 10, 2 );
			
			// add a few custom styles for post page
			add_action('admin_print_styles-post.php', array( &$this, 'register_settings_styles' ) );
            add_action('admin_print_styles-post-new.php', array( &$this, 'register_settings_styles' ) ); 
			
            // menu icon
            add_action( 'admin_head', array( &$this, 'add_menu_icon' ) );
            
		}

	}
	
    public function add_menu_icon() {
    
        echo '<style type="text/css"> #adminmenu .menu-icon-'.$this->token.' div.wp-menu-image:before { content: "\f513"; } </style>';
    
    }
	
	public function register_settings_styles() {
		
        global $post, $post_ID;        
        
        if( get_post_type($post_ID) == $this->token ) {
        
            /* core style files */
            wp_enqueue_style('wp-color-picker');
            
            /* core script files */
            wp_enqueue_script('jquery-ui');
            wp_enqueue_script('jquery-ui-tabs');
            wp_enqueue_script('jquery-ui-widget');
            wp_enqueue_script('jquery-ui-mouse ');
            
             /* custom css files */
            wp_enqueue_style(
                'ut-table-colorpicker', 
                $this->assets_url . 'admin/vendor/minicolors/css/jquery.minicolors.css'
            );
            
            /* custom css files */
            wp_enqueue_style(
                'ut-table-manager-styles', 
                $this->assets_url . 'admin/css/ut.table.manager.css'
            );
            
            /* colorpicker */
            wp_enqueue_script(
                'ut-table-colorpicker', 
                $this->assets_url . 'admin/vendor/minicolors/js/jquery.minicolors.min.js',
                array( 'wp-color-picker' ) 
            );            
                        
            /* custom js files */
            wp_enqueue_script(
                'ut-table-manager-scripts', 
                $this->assets_url . 'admin/js/ut.table.manager.js',
                array( 'ut-table-colorpicker' ) 
            );
            
            $translation_vars = array( 
                'title'      => esc_html__( 'Food Title', 'ut_table_lang' ),
                'ingredients'=> esc_html__( 'Ingredients', 'ut_table_lang' ),
                'price'      => esc_html__( 'Price', 'ut_table_lang' ),
            );
            wp_localize_script( 'ut-table-manager-scripts', 'ut_menu_manager', $translation_vars );
            
        }
        
	}
	
	/**
	 * Register new post type
	 * @return void
	 */
	public function register_post_type() {

		$labels = array(
			'name' => _x( 'United Themes - Menu Card Manager', 'post type general name' , 'ut_table_lang' ),
			'singular_name' => _x( 'United Themes - Menu Card Manager', 'post type singular name' , 'ut_table_lang' ),
			'add_new' => _x( 'Add UT Menu Card', $this->token , 'ut_table_lang' ),
			'add_new_item' => sprintf( __( 'Add %s' , 'ut_table_lang' ), __( 'UT Menu Card' , 'ut_table_lang' ) ),
			'edit_item' => sprintf( __( 'Edit %s' , 'ut_table_lang' ), __( 'Card' , 'ut_table_lang' ) ),
			'new_item' => sprintf( __( 'New %s' , 'ut_table_lang' ), __( 'Card' , 'ut_table_lang' ) ),
			'all_items' => sprintf( __( 'All %s' , 'ut_table_lang' ), __( 'UT Menu Cards' , 'ut_table_lang' ) ),
			'view_item' => sprintf( __( 'View %s' , 'ut_table_lang' ), __( 'Cards' , 'ut_table_lang' ) ),
			'search_items' => sprintf( __( 'Search %a' , 'ut_table_lang' ), __( 'Cards' , 'ut_table_lang' ) ),
			'not_found' =>  sprintf( __( 'No %s Found' , 'ut_table_lang' ), __( 'Cards' , 'ut_table_lang' ) ),
			'not_found_in_trash' => sprintf( __( 'No %s Found In Trash' , 'ut_table_lang' ), __( 'Posts' , 'ut_table_lang' ) ),
			'parent_item_colon' => '',
			'menu_name' => __( 'UT Menu Cards' , 'ut_table_lang' )
		);

		$args = array(
			'labels' => $labels,
			'public' => false,
			'publicly_queryable' => true,
			'exclude_from_search' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'show_in_nav_menus' => false,
			'query_var' => false,
			'rewrite' => true,
			'capability_type' => 'post',
			'has_archive' => true,
			'hierarchical' => false,
			'supports' => array( 'title' ),
			'menu_position' => 5,
			'menu_icon' => ''
		);

		register_post_type( $this->token, $args );
	}
	
    /**
     * Regsiter column headings for post type
     * @param  array $defaults Default columns
     * @return array           Modified columns
     */
    public function register_custom_column_headings( $defaults ) {
		$new_columns = array(
			'menu-shortcode' => __( 'Shortcode' , 'ut_table_lang' )
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

	/**
	 * Load data for post type columns
	 * @param  string  $column_name Name of column
	 * @param  integer $id          Post ID
	 * @return void
	 */
	public function register_custom_columns( $column_name, $id ) {
		
		global $post, $post_ID;
		
		switch ( $column_name ) {

			case 'menu-shortcode':
				echo '[ut_menu id="' . $post->ID . '"]';
			break;

			default:
			break;
		}

	}

	/**
	 * Set up admin messages for post type
	 * @param  array $messages Default message
	 * @return array           Modified messages
	 */
	public function updated_messages( $messages ) {
	  global $post, $post_ID;

	  $messages[$this->token] = array(
	    0 => '', // Unused. Messages start at index 1.
	    1 => sprintf( __( 'Post updated. %sView post%s.' , 'ut_table_lang' ), '<a href="' . esc_url( get_permalink( $post_ID ) ) . '">', '</a>' ),
	    2 => __( 'Custom field updated.' , 'ut_table_lang' ),
	    3 => __( 'Custom field deleted.' , 'ut_table_lang' ),
	    4 => __( 'Post updated.' , 'ut_table_lang' ),
	    /* translators: %s: date and time of the revision */
	    5 => isset($_GET['revision']) ? sprintf( __( 'Post restored to revision from %s.' , 'ut_table_lang' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
	    6 => sprintf( __( 'Post published. %sView post%s.' , 'ut_table_lang' ), '<a href="' . esc_url( get_permalink( $post_ID ) ) . '">', '</a>' ),
	    7 => __( 'Post saved.' , 'ut_table_lang' ),
	    8 => sprintf( __( 'Post submitted. %sPreview post%s.' , 'ut_table_lang' ), '<a target="_blank" href="' . esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) . '">', '</a>' ),
	    9 => sprintf( __( 'Post scheduled for: %1$s. %2$sPreview post%3$s.' , 'ut_table_lang' ), '<strong>' . date_i18n( __( 'M j, Y @ G:i' , 'ut_table_lang' ), strtotime( $post->post_date ) ) . '</strong>', '<a target="_blank" href="' . esc_url( get_permalink( $post_ID ) ) . '">', '</a>' ),
	    10 => sprintf( __( 'Post draft updated. %sPreview post%s.' , 'ut_table_lang' ), '<a target="_blank" href="' . esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) . '">', '</a>' ),
	  );

	  return $messages;
	}

	/**
	 * Add meta box to post type
	 * @return void
	 */
	public function meta_box_setup() {
		add_meta_box( 'ut-table-manager', __( 'United Themes - Menu Card Settings' , 'ut_table_lang' ) . ' v' . UT_PRICING_VERSION, array( &$this, 'meta_box_content' ), $this->token, 'normal', 'high' );
		add_meta_box( 'ut-table-manager-info', __( 'Usage' , 'ut_table_lang' ), array( &$this, 'meta_box_content_info' ), $this->token, 'side', 'high' );
	}

	public function checked_array( $current , $haystack , $offset = false ) {
		
		if( $offset && isset( $haystack[$offset] ) ) {
			
			$haystack = $haystack[$offset];
			
			if( !is_array($haystack) )					
			return checked( $haystack, $current , false );
			
		}
								
		if( is_array($haystack) && isset($haystack[$current]) ) {
			
			$current = $haystack = 1;
			return checked( $haystack, $current , false );
			
		}

				
	}
    
    public function selected_array( $key , $current , $haystack , $offset = false ) {
		
		if( $offset && isset( $haystack[$offset][$key] ) ) {
			
			$haystack = $haystack[$offset][$key];
			
			if( !is_array($haystack) )
			return selected( $haystack, $current , false );
			
		}
												
		if( is_array($haystack) && isset($haystack[$key]) && $haystack[$key] == $current) {
			
			$current = $haystack = 1;
			return selected( $haystack, $current , false );
			
		}

	}
	
	public function validate_value( $value , $key , $offset = false, $default = '' ) {
		
		if( $offset && isset($value[$offset][$key]) ) {
		
			return esc_attr($value[$offset][$key]);
		
		} elseif( isset($value[$key]) ) {
        
            return esc_attr($value[$key]);
        
        } else {
        
            return $default;
        
        }
		
	}
	
	public function meta_box_content() {
		
		global $post_id;
		
		$fields = get_post_custom( $post_id );
		$field_data = $this->get_custom_fields_settings();

		$html = NULL;

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
                                
                if( $v['type'] == 'select' ) {
                        
                        $html .= '<div class="ut-admin-info-box">';
                        
                            $html .= '<span class="ut-section-title">' . $v['name'] . '</span>';
                            $html .= '<p class="description">' . $v['description'] . '</p>';
                            
                            /* data available */
                            $data = maybe_unserialize( $data );
                            
                            $html .= '<div class="ut-section-panel">';
                            
                            
                            $html .= '<table class="form-table">' . "\n";
								
                                $html .= '<tbody>' . "\n";
                                
                                /* card title color */
                                $html .= '<tr valign="top">';
                                    
                                    $html .= '<th scope="row">' . esc_html__('Card Title Color' , 'ut_table_lang') . '</th>';
                                    $html .= '<td><input type="text" class="ut-color-picker" value="' . $this->validate_value( $data , 'card_title_color' , 1 ) . '" name="' . esc_attr( $k ) . '[card_title_color]" id="' . esc_attr( $k ) . '_card_title_color" /> <label for="' . esc_attr( $k ) . '_card_title_color"></label>';
                                    
                                $html .= '</tr>';
                                
                                /* card title background color */
                                $html .= '<tr valign="top">';
                                    
                                    $html .= '<th scope="row">' . esc_html__('Card Title Background Color' , 'ut_table_lang') . '</th>';
                                    $html .= '<td><input type="text" class="ut-color-picker" value="' . $this->validate_value( $data , 'card_title_background_color' , 1 ) . '" name="' . esc_attr( $k ) . '[card_title_background_color]" id="' . esc_attr( $k ) . '_card_title_background_color" /> <label for="' . esc_attr( $k ) . '_card_title_background_color"></label>';
                                    
                                $html .= '</tr>';
                                
                                /* food title color */
                                $html .= '<tr valign="top">';
                                    
                                    $html .= '<th scope="row">' . esc_html__('Food Title Color' , 'ut_table_lang') . '</th>';
                                    $html .= '<td><input type="text" class="ut-color-picker" value="' . $this->validate_value( $data , 'food_title_color' , 1 ) . '" name="' . esc_attr( $k ) . '[food_title_color]" id="' . esc_attr( $k ) . '_food_title_color" /> <label for="' . esc_attr( $k ) . '_food_title_color"></label>';
                                    
                                $html .= '</tr>';
                                
                                /* food title background color */
                                $html .= '<tr valign="top">';
                                    
                                    $html .= '<th scope="row">' . esc_html__('Food Title Background Color' , 'ut_table_lang') . '</th>';
                                    $html .= '<td><input type="text" class="ut-color-picker" value="' . $this->validate_value( $data , 'food_title_background_color' , 1 ) . '" name="' . esc_attr( $k ) . '[food_title_background_color]" id="' . esc_attr( $k ) . '_food_title_background_color" /> <label for="' . esc_attr( $k ) . '_food_title_background_color"></label>';
                                    
                                $html .= '</tr>';

				                /* food title size */
				                $html .= '<tr valign="top">';

				                $html .= '<th scope="row">' . esc_html__('Food Title Size' , 'ut_table_lang') . '</th>';

					                $html .= '<td><select name="' . esc_attr( $k ) . '[food_title_size]" id="' . esc_attr( $k ) . '_size">';

						                $html .= '<option value="17" ' . $this->selected_array( 'food_title_size' , '17' , $data , false ) . '>'  . __('small (17px)' , 'ut_table_lang') . '</option>';
						                $html .= '<option value="20" ' . $this->selected_array( 'food_title_size' , '20' , $data , false ) . '>'  . __('medium (20px)' , 'ut_table_lang') . '</option>';
						                $html .= '<option value="30" ' . $this->selected_array( 'food_title_size' , '30' , $data , false ) . '>'  . __('large (30px)' , 'ut_table_lang') . '</option>';

					                $html .= '</select></td>';

				                $html .= '</tr>';
                                
                                /* decoration line color */
                                $html .= '<tr valign="top">';
                                    
                                    $html .= '<th scope="row">' . esc_html__('Decoration Line Color' , 'ut_table_lang') . '</th>';
                                    $html .= '<td><input type="text" class="ut-color-picker" value="' . $this->validate_value( $data , 'decoration_line_color' , 1 ) . '" name="' . esc_attr( $k ) . '[decoration_line_color]" id="' . esc_attr( $k ) . '_decoration_line_color" /> <label for="' . esc_attr( $k ) . '_decoration_line_color"></label>';
                                    
                                $html .= '</tr>';
                                
                                /* food Ingredients color */
                                $html .= '<tr valign="top">';
                                    
                                    $html .= '<th scope="row">' . esc_html__('Ingredients Color' , 'ut_table_lang') . '</th>';
                                    $html .= '<td><input type="text" class="ut-color-picker" value="' . $this->validate_value( $data , 'ingredients_color' , 1 ) . '" name="' . esc_attr( $k ) . '[ingredients_color]" id="' . esc_attr( $k ) . '_ingredients_color" /> <label for="' . esc_attr( $k ) . '_ingredients_color"></label>';
                                    
                                $html .= '</tr>';

				                /* food item spacing */
				                $html .= '<tr valign="top">';

				                    $html .= '<th scope="row">' . esc_html__('Deactivate Ingredients Lowercase?' , 'ut_table_lang') . '</th>';

				                    $html .= '<td><select name="' . esc_attr( $k ) . '[ingredients_text_transform]" id="' . esc_attr( $k ) . '_text_transform">';

						                $html .= '<option value="off" ' . $this->selected_array( 'ingredients_text_transform' , 'off' , $data , false ) . '>'  . __('no, thanks!' , 'ut_table_lang') . '</option>';
						                $html .= '<option value="on" ' . $this->selected_array( 'ingredients_text_transform' , 'on' , $data , false ) . '>'  . __('yes, please!' , 'ut_table_lang') . '</option>';

					                $html .= '</select></td>';

				                $html .= '</tr>';
                                                                
                                /* currency */
                                $html .= '<tr valign="top">';
                                    
                                    $html .= '<th scope="row">' . esc_html__('Currency e.g. € or $' , 'ut_table_lang') . '</th>';
                                    $html .= '<td><input type="text" value="' . $this->validate_value( $data , 'currency' , 1 ) . '" name="' . esc_attr( $k ) . '[currency]" id="' . esc_attr( $k ) . '_currency" /> <label for="' . esc_attr( $k ) . '_currency"></label>';
                                    
                                $html .= '</tr>';
                                
                                /* currency color */
                                $html .= '<tr valign="top">';
                                    
                                    $html .= '<th scope="row">' . esc_html__('Currency Color' , 'ut_table_lang') . '</th>';
                                    $html .= '<td><input type="text" class="ut-color-picker" value="' . $this->validate_value( $data , 'currency_color' , 1 ) . '" name="' . esc_attr( $k ) . '[currency_color]" id="' . esc_attr( $k ) . '_currency_color" /> <label for="' . esc_attr( $k ) . '_currency_color"></label>';
                                    
                                $html .= '</tr>';
                                
                                /* currency background color */
                                $html .= '<tr valign="top">';
                                    
                                    $html .= '<th scope="row">' . esc_html__('Currency Background Color' , 'ut_table_lang') . '</th>';
                                    $html .= '<td><input type="text" class="ut-color-picker" value="' . $this->validate_value( $data , 'currency_background_color' , 1 ) . '" name="' . esc_attr( $k ) . '[currency_background_color]" id="' . esc_attr( $k ) . '_currency_background_color" /> <label for="' . esc_attr( $k ) . '_currency_background_color"></label>';
                                    
                                $html .= '</tr>';
					
								/* food item spacing */
								$html .= '<tr valign="top">';
									
									$html .= '<th scope="row">' . esc_html__('Spacing Between Food Listings' , 'ut_table_lang') . '</th>';	
									$html .= '<td><select name="' . esc_attr( $k ) . '[food_item_spacing]" id="' . esc_attr( $k ) . '_style">';

										$html .= '<option value="default" ' . $this->selected_array( 'food_item_spacing' , 'default' , $data , false ) . '>'  . __('default (40px)' , 'ut_table_lang') . '</option>';
										$html .= '<option value="small" ' . $this->selected_array( 'food_item_spacing' , 'small' , $data , false ) . '>'  . __('small (20px)' , 'ut_table_lang') . '</option>';

									$html .= '</select></td>';
					
								$html .= '</tr>';

                                $html .= '</tbody>' . "\n";
                                
							$html .= '</table>' . "\n";                        
                            
                            $html .= '</div>' . "\n";
                        
                        $html .= '</div>' . "\n";
                        
                        $html .= '<div class="ut-admin-info-box">';
                        
                            $html .= '<span class="ut-section-title">' . __('United Themes - Menu Card Shortcode' , 'ut_table_lang') . '</span>';                        
                            
                            $html .= '<div class="ut-section-panel">';
                            
                                $html .= '<textarea readonly class="ut-shortcode-code">[ut_menu id="' . $post_id . '"]</textarea>';
                                $html .= __('Simply copy this United Themes - Menu Card Shortcode and place <br /> it inside the text editor of the section / page you like to display the <br />Menu Card in. ' , 'ut_table_lang');
                                                  
                            $html .= '</div>' . "\n";
                        
                        $html .= '</div>' . "\n"; 
                        
                        $html .= '<div class="clear"></div>';                       
                
                } elseif( $v['type'] == 'ut_menu_data' ) {
					
                    $data = maybe_unserialize( $data );
                    
					$html .= '<div id="ut-table-tabs" class="ut-table-tabs">';
						
						$html .= '<ul class="ut-table-tabs-nav">';
                            
                            if( !empty( $data ) && is_array( $data ) ) {
                                
                                foreach( $data as $key => $column ) {
                                    
                                    $html .= '<li><a id="tab-for-table-column-' . $key . '" href="#table-column-' . $key . '">' . esc_html__('Card', 'ut_table_lang' ) .' ' . $key . ' </a></li>';
                                
                                }                                
                                
                            } 
							
						$html .= '</ul>';
						
                        $html .= '<a data-count="' . ( !empty( $data ) ? count( $data ) : '0' ) . '" class="ut-add-card" href="">' . esc_html__('Add Card', 'ut_table_lang' ) .'</a>';                        
						
                        $html .= '<div class="clear"></div>';
                        
                        if( !empty( $data ) && is_array( $data ) ) {
                            
                            foreach( $data as $key => $column ) {
                                                    
                                /* column 1 */
                                $html .= '<div id="table-column-' . $key . '">';					
                                        
                                        $html .= '<table class="form-table">' . "\n";
                                        $html .= '<tbody>' . "\n";
                                        
                                        $html .= '<tr valign="top">';
                                            
                                            $html .= '<th scope="row"><h2>' . esc_html__('Menu Card', 'ut_table_lang' ) .' ' . $key . '</h2></th>';
                                            $html .= '<td><a data-card="' . $key . '" class="ut-delete-card">' . esc_html__('delete this card', 'ut_table_lang' ) .'</a></td>';
                                            
                                        $html .= '</tr>';								
                                        
                                        /* column 1 headline */
                                        $html .= '<tr valign="top">';
                                            
                                            $html .= '<th scope="row">' . __('Headline' , 'ut_table_lang') . '</th>';									
                                            $html .= '<td><input type="text" class="regular-text" value="' . $this->validate_value( $column , 'headline' , 1 ) . '" name="' . esc_attr( $k ) . '[' . $key . '][headline]" id="' . esc_attr( $k ) . '_headline_' . $key . '" /> <label for="' . esc_attr( $k ) . '_headline_' . $key . '"></label>';
                                            
                                        $html .= '</tr>';
                                        
                                        /* column 1 custom header */
                                        $html .= '<tr valign="top">';
                                            
                                            $html .= '<th scope="row">' . __('Custom Header' , 'ut_table_lang') . '</th>';
                                            $html .= '<td><textarea class="large-text code" rows="7" name="' . esc_attr( $k ) . '[' . $key . '][header]" id="' . esc_attr( $k ) . '_header_' . $key . '" />' . $this->validate_value( $column , 'header' , 1 ) . '</textarea> <label for="' . esc_attr( $k ) . '_header_' . $key . '"></label>';
                                        
                                        $html .= '</tr>';		
                                        
                                        /* column 1 features */
                                        $html .= '<tr valign="top">';
                                            
                                            $c = $features = NULL;
                                            
                                            if( isset($data[$key]['foods']) && is_array($data[$key]['foods']) ) {
                                                    
                                                if ( count( $data[$key]['foods'] ) > 0 ) {
                                                    
                                                    foreach( $data[$key]['foods'] as $dkey => $dataset ) {
                                    
                                                        $features .= '<div class="ut-menu-card-item">';
                                                            $features .= '<p><label>' . esc_html__( 'Food Title', 'ut_table_lang' ) . '</label><input type="text" class="regular-text" name="' . esc_attr( $k ) . '[' . $key . '][foods]['.$dkey.'][title]" value="' . $dataset['title'] . '" /></p>';
                                                            $features .= '<p><label>' . esc_html__( 'Ingredients / Description', 'ut_table_lang' ) . '</label><textarea class="large-text code" rows="7" name="' . esc_attr( $k ) . '[' . $key . '][foods]['.$dkey.'][ingredients]">' . $dataset['ingredients'] . '</textarea></p>';
                                                            $features .= '<p><label>' . esc_html__( 'Price', 'ut_table_lang' ) . '</label><input type="text" class="regular-text" name="' . esc_attr( $k ) . '[' . $key . '][foods]['.$dkey.'][price]" value="' . $dataset['price'] . '" /></p>';
                                                            $features .= '<span class="ut-admin-remove remove-feature">' . __('X' , 'ut_table_lang') . '</span>';
                                                        $features .= '</div>';
                                                        
                                                        $c = $dkey;
                                                        
                                                    }
                                                    
                                                }
                                                
                                            }
                                            
                                            $html .= '<th scope="row">';
                                                $html .= __( 'Menu Food' , 'ut_table_lang' ) . '<br /><br />';
                                                $html .= '<span data-card="' . $key . '" data-foodgroup="' . esc_attr( $k ) . '" data-foodcount="' . $c . '" class="add-food ut-admin-add">' . __('Add Food' , 'ut_table_lang') . '</span>';
                                            $html .= '</th>';
        
                                                                            
                                            $html .= '<td>';
                                                
                                                $html .= '<div class="ut-admin-foods" id="ut-repeat-' . esc_attr( $k ) . '-' . $key . '">';
                                                
                                                    $html .= $features;
                                                    
                                                $html .= '</div>';
                                                
                                            $html .= '</td>';
                                            
                                            $html .= '</tr>';
                                            
                                            /* column 1 custom footer */
                                            $html .= '<tr valign="top">';
                                                
                                                $html .= '<th scope="row">' . __('Custom Footer' , 'ut_table_lang') . '</th>';
                                                $html .= '<td><textarea class="large-text code" rows="7" name="' . esc_attr( $k ) . '[' . $key . '][footer]" id="' . esc_attr( $k ) . '_footer_' . $key . '" />' . $this->validate_value( $column , 'footer' , 1 ) . '</textarea> <label for="' . esc_attr( $k ) . '_footer_' . $key . '"></label>';
                                            
                                            $html .= '</tr>';
                                            
                                            
                                            $html .= '</tbody>' . "\n";
                                            $html .= '</table>' . "\n";
                                            
                                $html .= '</div>';
					        
                            }
                            
                        }
						
						/* column to copy */
						$html .= '<div class="ut-column-to-copy">';					
								
								$html .= '<table class="form-table">' . "\n";
								$html .= '<tbody>' . "\n";
								
								$html .= '<tr valign="top">';
									
									$html .= '<th scope="row"><h2>' . esc_html__(' Menu Card', 'ut_table_lang' ) .' __id__</h2></th>';
									$html .= '<td><a data-card="__id__" class="ut-delete-card">' . esc_html__('delete this card', 'ut_table_lang' ) .'</a></td>';
									
								$html .= '</tr>';
								
                                /* column 2 headline */
								$html .= '<tr valign="top">';
									
									$html .= '<th scope="row">' . __('Headline' , 'ut_table_lang') . '</th>';									
									$html .= '<td><input type="text" class="regular-text" value="" name="' . esc_attr( $k ) . '[__id__][headline]" id="' . esc_attr( $k ) . '_headline___id__" /> <label for="' . esc_attr( $k ) . '_headline___id__"></label>';
									
								$html .= '</tr>';
                                
								/* column 2 custom header */
								$html .= '<tr valign="top">';
									
									$html .= '<th scope="row">' . __('Custom Header' , 'ut_table_lang') . '</th>';
									$html .= '<td><textarea class="large-text code" rows="7" name="' . esc_attr( $k ) . '[__id__][header]" id="' . esc_attr( $k ) . '_header___id__" /></textarea> <label for="' . esc_attr( $k ) . '_header___id__"></label>';
								
								$html .= '</tr>';		
								
								/* column 2 features */
								$html .= '<tr valign="top">';
									
                                    $c = 0;
                                    
									$html .= '<th scope="row">';
										$html .= __( 'Menu Food' , 'ut_table_lang' ) . '<br /><br />';
										$html .= '<span data-card="__id__" data-foodgroup="' . esc_attr( $k ) . '" data-foodcount="' . $c . '" class="add-food ut-admin-add">' . __('Add Food' , 'ut_table_lang') . '</span>';
									$html .= '</th>';
																	
									$html .= '<td>';
										
										$html .= '<div class="ut-admin-foods" id="ut-repeat-' . esc_attr( $k ) . '-__id__">';
											
										$html .= '</div>';
										
									$html .= '</td>';
									
									$html .= '</tr>';
								
                                /* column 2 custom footer */
								$html .= '<tr valign="top">';
									
									$html .= '<th scope="row">' . __('Custom Footer' , 'ut_table_lang') . '</th>';
									$html .= '<td><textarea class="large-text code" rows="7" name="' . esc_attr( $k ) . '[__id__][footer]" id="' . esc_attr( $k ) . '_footer_2" /></textarea> <label for="' . esc_attr( $k ) . '_footer___id__"></label>';
								
								$html .= '</tr>';                                	
                                    		
								$html .= '</tbody>' . "\n";
								$html .= '</table>' . "\n";
					
						$html .= '</div>';
						
					$html .= '</div>';	
					
				} else {
					
					$html .= '<span class="ut-section-title">' . $v['name'] . '</span>';
					$html .= '<div class="ut-section-panel">';
					$html .= '<label for="' . esc_attr( $k ) . '">' . $v['name'] . '</label><input name="' . esc_attr( $k ) . '" type="text" id="' . esc_attr( $k ) . '" class="regular-text" value="' . esc_attr( $data ) . '" />' . "\n";
					$html .= '<p class="description">' . $v['description'] . '</p>' . "\n";
					$html .= '</div>' . "\n";
					
				}

			}
            
            $html .= '<div class="ut-admin-info-box" style="margin-top:40px; margin-bottom:0px;">';
            
            $html .= '<span class="ut-section-title">' . __('United Themes - Menu Card Shortcode' , 'ut_table_lang') . '</span>';                        
            
                $html .= '<div class="ut-section-panel">';
                
                    $html .= '<textarea readonly class="ut-shortcode-code">[ut_menu id="' . $post_id . '"]</textarea>';
                    $html .= __('Simply copy this United Themes - Menu Card Shortcode and place <br /> it inside the text editor of the section / page you like to display the <br />Menu Card in.' , 'ut_table_lang');
                                      
                $html .= '</div>' . "\n";
            
            $html .= '</div>' . "\n";
            
            
            
			$html .= '</tbody>' . "\n";
			$html .= '</table>' . "\n";
		}

		echo $html;
	}
	
	
	public function meta_box_content_info() {
		
		global $post_id;
		
		$info  = '<p><strong>' . __('United Themes - Menu Card Shortcode' , 'ut_table_lang') . '</strong></p>';
		$info .= '<textarea readonly class="ut-shortcode-code">[ut_menu id="' . $post_id . '"]</textarea>';
		
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
			$title = __( 'Enter the post title here' , 'ut_table_lang' );
		}
		return $title;
	}

	public function get_custom_fields_settings() {
		
		$fields = array();
        
        $fields['ut_menu_style'] = array(
		    'name' => __( 'United Themes - Menu Card Style' , 'ut_table_lang' ),
		    'description' => 'You can activate the menu card animation side the menu card module of brooklyn pagebuiler.',
		    'type' => 'select',
		    'default' => '',
		    'section' => 'plugin-data'
		);        

		$fields['ut_menu_data'] = array(
		    'name' => __( 'Menu Card Options' , 'ut_table_lang' ),
		    'description' => '',
		    'type' => 'ut_menu_data',
		    'default' => '',
		    'section' => 'plugin-data'
		);
        
		return $fields;
		
	}

}