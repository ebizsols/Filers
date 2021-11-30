<?php

function ut_get_twitch_video_type( $url ) {

    if( stristr( $url, 'clips.twitch.tv' ) ) {

        return array(
            'type'  => 'clip',
            'index' => 1,
            'regex' => '/http[s]?:\/\/(?:www\.|clips\.)twitch\.tv\/([0-9a-zA-Z\-\_]+)\/?(chat\/?$|[0-9a-z\-\_]*)?/'
        );

    }

    if( stristr($url, '/clip/') ) {

        return array(
            'type'  => 'clip',
            'index' => 3,
            'regex' => '/http[s]?:\/\/(?:www\.|clips\.)twitch\.tv\/([0-9a-zA-Z\-\_]+)\/([0-9a-zA-Z\-\_]+)\/([0-9a-zA-Z\-\_]+)?/'
        );

    }

    if( stristr($url, '/videos/') ) {

        return array(
            'type'  => 'video',
            'index' => 2,
            'regex' => '/http[s]?:\/\/(?:www\.|clips\.)twitch\.tv\/([0-9a-zA-Z\-\_]+)\/?(chat\/?$|[0-9a-z\-\_]*)?/'
        );

    }

    if( stristr($url, 'twitch.tv/') ) {

        if( stristr($url, 'player.twitch.tv/?channel') ) {

            return array(
                'type'  => 'channel',
                'index' => 2,
                'regex' => '~[\?&]([^&]+)=([^&]+)~'
            );

        } else {

            return array(
                'type'  => 'channel',
                'index' => 1,
                'regex' => '/http[s]?:\/\/(?:www\.|clips\.)twitch\.tv\/([0-9a-zA-Z\-\_]+)\/?(chat\/?$|[0-9a-z\-\_]*)?/'
            );

        }

    }

    return array();

}

if ( ! function_exists( 'ut_get_video_player' ) ) :

	function ut_get_video_player() {
        
        /* get video to check */
		$video = $_POST['video'];
        
        /* needed variables */
        $embed_code = NULL;
        
        /* check if youtube has been used */
        preg_match('~(?:http|https|)(?::\/\/|)(?:www.|)(?:youtu\.be\/|youtube\.com(?:\/embed\/|\/v\/|\/watch\?v=|\/ytscreeningroom\?v=|\/feeds\/api\/videos\/|\/user\S*[^\w\-\s]|\S*[^\w\-\s]))([\w\-]{11})[a-z0-9;:@#?&%=+\/\$_.-]*~i', trim($video) , $matches);        
            
        if( !empty($matches[1]) ) {
            $embed_code = '<iframe height="315" width="560" src="//www.youtube.com/embed/'.trim($matches[1]).'?wmode=transparent&vq=hd720&autoplay=1&rel=0" wmode="Opaque" allowfullscreen="" frameborder="0"></iframe>';          
        }

        // check for twitch
        $twitch_type = ut_get_twitch_video_type( trim($video) );

        if( !empty( $twitch_type ) ) {

            preg_match( $twitch_type['regex'], trim($video), $matches );

            if( !empty( $matches[$twitch_type['index']] ) ) {

                $channelName = esc_attr( $matches[$twitch_type['index']] );

                switch( $twitch_type['type'] ) {

                    case 'clip':
                        $src         = 'https://clips.twitch.tv/embed?clip=' . $channelName . '&autoplay=true';
                        $attr        = 'scrolling="no" frameborder="0" allowfullscreen="true"';
                        break;

                    case 'video':
                        $src         = 'https://player.twitch.tv/?video=' . $channelName;
                        $attr        = 'scrolling="no" frameborder="0" allowfullscreen="true"';
                        break;

                    case 'channel':
                        $src         = 'https://player.twitch.tv/?channel=' . $channelName;
                        $attr        = 'scrolling="no" frameborder="0" allowfullscreen="true"';
                        break;

                }

                if( !empty( $src ) ) {

                    $embed_code = '<iframe height="315" width="560" src="' . $src . '" ' . $attr . '></iframe>';

                }

            }

        }

        /* try to load video player */
        if( empty( $embed_code )) {

            if( strpos( trim($video), '.mp4' ) !== false ) {

                $embed_code = do_shortcode('[ut_simple_html5_video controls="true" mp4="' . stripslashes($video) . '"]');

            }

        }

        /* no video found so far , try to create a player  */
        if( empty($embed_code) ) {
            
            $video_embed = wp_oembed_get(trim($video));
            if( !empty($video_embed) ) {
                $embed_code = $video_embed;            
            }
            
        }
        
        /* still no video found , let's try to apply a shortcode */
        if( empty($embed_code) ) {
            $embed_code = do_shortcode(stripslashes($video));
        }



        echo $embed_code;
        
        die(1);
        
    }
    
endif;

add_action( 'wp_ajax_nopriv_ut_get_video_player', 'ut_get_video_player' );
add_action( 'wp_ajax_ut_get_video_player', 'ut_get_video_player' );