<?php
/*
Plugin Name: Easy Pinterest
Plugin URI: http://thisismyurl.com/downloads/wordpress-plugins/easy-pinterest/
Description: An easy to use WordPress function to add Easy Pinterest to any theme as a function or Widget.
Author: Christopher Ross
Tags: pinterest
Author URI: http://thisismyurl.com
Version: 1.0.0
*/


/*  Copyright 2012 Christopher Ross  ( email : info@thisismyurl.com )

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    ( at your option ) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class thisismyurl_easy_pinterest_widget extends WP_Widget
{
	
	
	function thisismyurl_easy_pinterest_widget( ){
		$widget_ops = array( 'classname' => 'widget_thisismyurl_easy_pinterest', 'description' => __( "A WordPress widget to add your recent Pinterest posts to your WordPress website. Learn more at http://thisismyurl.com" ) );
		$control_ops = array( 'width' => 300, 'height' => 300 );
		$this->WP_Widget( 'thisismyurl_easy_pinterest_widget', __( 'Easy Pinterest' ), $widget_ops, $control_ops );
	}

	function update( $new_instance, $old_instance ){
		$instance = $old_instance;
		$instance['title'] = strip_tags( stripslashes( $new_instance['title'] ) );
		$instance['pinterest_username'] = strip_tags( stripslashes( $new_instance['pinterest_username'] ) );
		$instance['pinterest_quantity'] = strip_tags( stripslashes( $new_instance['pinterest_quantity'] ) );

		return $instance;
	}

	function form( $instance ){
		$instance = wp_parse_args( ( array ) $instance, array( 'title'=>'Pinterest Activity', 'pinterest_username'=>'thisismyurl', 'pinterest_quantity'=>'12' ) );

		$title = htmlspecialchars( $instance['title'] );
		$pinterest_username = ( $instance['pinterest_username'] );
		$pinterest_quantity = ( $instance['pinterest_quantity'] );
				
		echo '<p style="text-align:left;"><label for="' . $this->get_field_name( 'title' ) . '">' . __( 'Title:' ) . '</label><br />
				<input style="width: 300px;" id="' . $this->get_field_id( 'title' ) . '" name="' . $this->get_field_name( 'title' ) . '" type="text" value="' . $title . '" /></p>';

		echo '<p style="text-align:left;"><label for="' . $this->get_field_name( 'pinterest_username' ) . '">' . __( 'Pinterest Username:' ) . '</label><br />
				<input style="width: 300px;" id="' . $this->get_field_id( 'pinterest_username' ) . '" name="' . $this->get_field_name( 'pinterest_username' ) . '" type="text" value="' . $pinterest_username . '" /></p>';
				
		echo '<p style="text-align:left;"><label for="' . $this->get_field_name( 'pinterest_quantity' ) . '">' . __( '# to Show:' ) . '</label><br />
				<input style="width: 300px;" id="' . $this->get_field_id( 'pinterest_quantity' ) . '" name="' . $this->get_field_name( 'pinterest_quantity' ) . '" type="text" value="' . $pinterest_quantity . '" /></p>';
	
	}


	function widget( $args, $instance ){
		
		extract( $args );
		$instance = wp_parse_args( ( array ) $instance, array( 'title'=>'Pinterest Activity', 'pinterest_username'=>'thisismyurl', 'pinterest_quantity'=>'12') );

		$pinterest_feed = fetch_feed( "http://pinterest.com/" . $instance['pinterest_username'] . "/feed.rss" );
		
		if (!is_wp_error( $pinterest_feed ) ) : 
			$maxitems = $pinterest_feed->get_item_quantity( $instance['pinterest_quantity'] ); 
			$pinterest_feed = $pinterest_feed->get_items(0, $maxitems); 
		endif;
		
		if ( !empty( $pinterest_feed ) ) {
			
			echo $before_widget;
			
			echo '<h4 class="widgettitle pinterest-title"><a href="http://pinterest.com/' . $instance['pinterest_username'] . '" 
				target="_blank">' . $instance['title'].'</h4>';
				
			echo '<div class="content"><div class="textwidget">';
			echo '<ul class="easy-pinterest">';
			foreach ( $pinterest_feed as $item ) {
				$pinterest_content = $item->get_content();
				$pinterest_content = str_replace( '&gt;','>',$pinterest_content );
				$pinterest_content = str_replace( '&lt;','<',$pinterest_content );
				$pinterest_content = str_replace( '<a','<a target="_blank"',$pinterest_content );
				
				$pinterest_content = strip_tags($pinterest_content, "<a>,<img>");
				$pinterest_content_array = explode("</a>", $pinterest_content);
				$pinterest_content = $pinterest_content_array[0];
												
				?><ol><a href='<?php echo esc_url( $item->get_permalink() ); ?>' 
					title='<?php echo 'Posted '.$item->get_date('j F Y | g:i a'); ?>'><?php echo $pinterest_content; ?></a></ol><?php
			}

			echo "</ul>";
			echo $after_widget;
		}
	}

}

function thisismyurl_easy_pinterest_css( ) {
	echo "<!-- Easy Pinterest by Christopher Ross (http://thisismyurl.com) --><style>
	
		h4.pinterest-title {background: url(" . plugins_url( 'images/pinterest-red.png' , __FILE__ ) . ") no-repeat right; padding-top:20px;}
		ul.easy-pinterest ol {float:left; width: 90px; height: 90px; overflow: hidden; margin-right: 10px; margin: 0px 10px 10px 0px; background: #efefef;display:table-cell; vertical-align:middle;}
		ul.easy-pinterest ol img {max-width: 100%; height: auto;}
		ul.easy-pinterest ol p {display: none;}

	</style>";
}
add_action( 'wp_head', 'thisismyurl_easy_pinterest_css' );

function thisismyurl_easy_pinterest_widget_Init( ) {
	register_widget( 'thisismyurl_easy_pinterest_widget' );
}
add_action( 'widgets_init', 'thisismyurl_easy_pinterest_widget_Init' );
?>