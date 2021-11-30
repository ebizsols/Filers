<?php

/**
 * Enhance Module Base Options
 *
 * @param array $base_options
 * @param array $enhanced_options
 * @param string $param
 *
 * @return  array
 *
 * @access  private
 * @since 4.9
 */

function ut_update_map_shortcode( $base_options, $enhanced_options, $param ) {

    $i = 0;
    $new_array=[];

    foreach( $base_options as $key => $value ) {

        $new_array[$i] = $value;

        if( $value['param_name'] == $param ) {

            foreach( $enhanced_options as $i_key => $i_value ) {

                $new_array[$i] = $i_value;
                $i++;

            }

        }

        $i++;

    }

    return $new_array;

}



/**
 * Add Glitch Settings
 *
 * @param string $group
 *
 * @return  array
 *
 * @access  private
 * @since 4.9.5
 */

function _vc_add_glitch_settings_to_element( $group = 'Special Effects' ) {

    return array (

        array(
            'type'        => 'dropdown',
            'heading'     => __( 'Is Glitch Image transparent?', 'unitedthemes' ),
            'description' => __( 'If the glitch image is transparent, please activate this option!', 'unitedthemes' ),
            'group'       => $group,
            'param_name'  => 'glitch_transparent',
            'value'       => array(
                esc_html__( 'no', 'unitedthemes' )                         => 'off',
                esc_html__( 'yes, image is transparent!', 'unitedthemes' ) => 'on',
            )
        ),

        array(
            'type'        => 'dropdown',
            'heading'     => __( 'Add Glitch Effect?', 'unitedthemes' ),
            'description' => __( 'Note that this effect is using clip path technique. The clip-path property is not supported in IE or Edge. All other Modern Browsers do support this feature.', 'unitedthemes' ),
            'group'       => $group,
            'param_name'  => 'glitch_effect',
            'value'       => array(
                esc_html__( 'No Glitch Effect', 'unitedthemes' )       => 'none',
                esc_html__( 'Ethereal', 'unitedthemes' )               => 'ethereal',
                esc_html__( 'Haunted', 'unitedthemes' )                => 'haunted',
                esc_html__( 'Prone', 'unitedthemes' )                  => 'prone',
                esc_html__( 'Equal', 'unitedthemes' )                  => 'equal',
                esc_html__( 'Gifted', 'unitedthemes' )                 => 'gifted',
                esc_html__( 'Past', 'unitedthemes' )                   => 'past',
                esc_html__( 'Ground (slightly zoom)', 'unitedthemes' ) => 'ground',
                esc_html__( 'Wide', 'unitedthemes' )                   => 'wide'
            ),
            'dependency'  => array(
                'element' => 'glitch_transparent',
                'value'   => array( 'off' ),
            )
        ),

        array(
            'type'        => 'dropdown',
            'heading'     => __( 'Add Glitch Effect?', 'unitedthemes' ),
            'description' => __( 'Note that this effect is using clip path technique. The clip-path property is not supported in IE or Edge. All other Modern Browsers do support this feature.', 'unitedthemes' ),
            'group'       => $group,
            'param_name'  => 'glitch_effect_transparent',
            'value'       => array(
                esc_html__( 'No Glitch Effect', 'unitedthemes' ) => 'none',
                esc_html__( 'Glitch Style 1', 'unitedthemes' )   => 'style-1',
                esc_html__( 'Glitch Style 2', 'unitedthemes' )   => 'style-2',
                esc_html__( 'Glitch Style 3', 'unitedthemes' )   => 'style-3',
            ),
            'dependency'  => array(
                'element' => 'glitch_transparent',
                'value'   => array( 'on' ),
            )
        ),

        array(
            'type' => 'dropdown',
            'heading' => __( 'Permanent Glitch or Glitch on Hover?', 'unitedthemes' ),
            'description' => __( 'Decide if glitch happens on hover or permanently in a loop.', 'unitedthemes' ),
            'group' => $group,
            'param_name' => 'permanent_glitch',
            'value' => array(
                esc_html__( 'Permanent Glitch', 'unitedthemes' )  => 'on',
                esc_html__( 'Glitch on Hover', 'unitedthemes' )   => 'off',
            ),
            'dependency'        => array(
                'element'           => 'glitch_effect',
                'value_not_equal_to'=> array('none'),
            )
        ),

        array(
            'type'          => 'colorpicker',
            'heading'       => esc_html__( 'Glitch Effect Accent Color (1)', 'unitedthemes' ),
            'description'   => __( 'Some glitch effects do support one or more colored layers. Leave empty to apply the default color.', 'unitedthemes' ),
            'param_name'    => 'accent_1',
            'group'         => $group,
            'dependency'    => array(
                'element' => 'glitch_effect',
                'value'   => array('ethereal','haunted','equal','gifted','past','ground','wide'),
            )
        ),

        array(
            'type'          => 'colorpicker',
            'heading'       => esc_html__( 'Glitch Effect Accent Color (2)', 'unitedthemes' ),
            'description'   => __( 'Some glitch effects do support one or more colored layers. Leave empty to apply the default color.', 'unitedthemes' ),
            'param_name'    => 'accent_2',
            'group'         => $group,
            'dependency'    => array(
                'element' => 'glitch_effect',
                'value'   => array('ethereal','equal','past','ground'),
            )
        ),

        array(
            'type'          => 'colorpicker',
            'heading'       => esc_html__( 'Glitch Effect Accent Color (3)', 'unitedthemes' ),
            'description'   => __( 'Some glitch effects do support one or more colored layers. Leave empty to apply the default color.', 'unitedthemes' ),
            'param_name'    => 'accent_3',
            'group'         => $group,
            'dependency'  => array(
                'element' => 'glitch_effect',
                'value'   => array('past'),
            )
        )

    );

}

// vc_add_params( 'ut_team_member', _vc_add_glitch_settings_to_element() );
vc_add_params( 'ut_hover_box_front', _vc_add_glitch_settings_to_element('Glitch' ) );
vc_add_params( 'ut_hover_box_back', _vc_add_glitch_settings_to_element( 'Glitch' ) );
vc_add_params( 'ut_video_player', _vc_add_glitch_settings_to_element( 'Poster' ) );

vc_add_params( 'vc_row', _vc_add_glitch_settings_to_element( 'Design Options' ) );
vc_add_params( 'vc_column', _vc_add_glitch_settings_to_element( 'Design Options' ) );
vc_add_params( 'vc_section', _vc_add_glitch_settings_to_element( 'Design Options' ) );


/**
 * Custom Cursor
 *
 * @return    array
 *
 * @access    private
 * @since     4.9.5
 */

if( !function_exists('_vc_get_cursor_skins')) {

	function _vc_get_cursor_skins( $inherit = false ) {

		if( $inherit ) {

			$all_cursor_skins = array(
				esc_html__( 'inherit', 'js_composer' ) => 'inherit',
				esc_html__( 'global', 'js_composer' ) => 'global',
				esc_html__( 'dark', 'js_composer' )   => 'dark',
				esc_html__( 'light', 'js_composer' )  => 'light',
			);

		} else {

			$all_cursor_skins = array(
				esc_html__( 'global', 'js_composer' ) => 'global',
				esc_html__( 'dark', 'js_composer' )   => 'dark',
				esc_html__( 'light', 'js_composer' )  => 'light',
			);

		}

		if( !function_exists('ot_get_option') ) {

			return $all_cursor_skins;

		}

		$cursor_color_skins = ot_get_option( "ut_custom_cursor_custom_skins" );

		if ( ! empty( $cursor_color_skins ) && is_array( $cursor_color_skins ) ) {

			foreach ( $cursor_color_skins as $skin ) {

				$all_cursor_skins[ $skin['title'] ] = $skin['unique_id'];

			}

		}

		return $all_cursor_skins;

	}

}