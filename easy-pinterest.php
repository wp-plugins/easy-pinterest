<?php
/*
Plugin Name: Easy Pinterest
Plugin URI: http://thisismyurl.com/plugins/easy-pinterest-wordpress/
Description: An easy to use WordPress function to add Easy Pinterest to any theme as a function or Widget.
Author: Christopher Ross
Tags: easy pinterest, thisismyurl, pin terest, pintrest, social media, photo sharing, block pinterest
Author URI: http://thisismyurl.com/
Version: 1.2.5
*/


/**
 * Easy Pinterest file
 *
 * This file contains all the logic required for the plugin
 *
 * @link		http://wordpress.org/extend/plugins/easy-pinterest-wordpress/
 *
 * @package 		Easy Pinterest
 * @copyright		Copyright (c) 2012, Chrsitopher Ross
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License, v2 (or newer)
 *
 * @since 		Easy Pinterest 1.0
 */


class thisismyurl_easy_pinterest_widget extends WP_Widget
{


	function thisismyurl_easy_pinterest_widget() {
		$widget_ops = array( 	'classname' => 'widget_thisismyurl_easy_pinterest',
								'description' => __( "A WordPress widget to add your recent Pinterest posts to your WordPress website. Learn more at http://thisismyurl.com" ) );
		$control_ops = array( 'width' => 300, 'height' => 300 );
		$this->WP_Widget( 'thisismyurl_easy_pinterest_widget', __( 'Easy Pinterest' ), $widget_ops, $control_ops );
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( stripslashes( $new_instance['title'] ) );
		$instance['pinterest_username'] = strip_tags( stripslashes( $new_instance['pinterest_username'] ) );
		$instance['pinterest_quantity'] = strip_tags( stripslashes( $new_instance['pinterest_quantity'] ) );

		return $instance;
	}

	function form( $instance ) {
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


	function widget( $args, $instance ) {

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
				$pinterest_content = str_replace( 'href="','href="http://www.pinterest.com', $pinterest_content );

				$pinterest_content = strip_tags( $pinterest_content, "<a>,<img>" );
				$pinterest_content_array = explode( '</a>', $pinterest_content );
				$pinterest_content = $pinterest_content_array[0];

				?><ol><a href='<?php echo esc_url( $item->get_permalink() ); ?>'
					title='<?php echo 'Posted '.$item->get_date('j F Y | g:i a'); ?>'><?php echo $pinterest_content; ?></a></ol><?php
			}

			echo "</ul>";

			echo $after_widget;
		}
	}

}

function thisismyurl_easy_pinterest_css() {
	echo "<!-- Easy Pinterest by Christopher Ross (http://thisismyurl.com) --><style>

		h4.pinterest-title {background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGQAAAAaCAYAAABByvnlAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYxIDY0LjE0MDk0OSwgMjAxMC8xMi8wNy0xMDo1NzowMSAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNS4xIE1hY2ludG9zaCIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpGNkRCMEMzNkI0QUUxMUUwQkNBMUJGN0M5Q0U2MDg3RSIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpGNkRCMEMzN0I0QUUxMUUwQkNBMUJGN0M5Q0U2MDg3RSI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjQ3OUQ5ODc1QjRBQzExRTBCQ0ExQkY3QzlDRTYwODdFIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjQ3OUQ5ODc2QjRBQzExRTBCQ0ExQkY3QzlDRTYwODdFIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+WZKxdQAABuxJREFUeNrsWg1I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) no-repeat right; padding-top:20px;}
		ul.easy-pinterest ol {float:left; width: 90px; height: 90px; overflow: hidden; margin-right: 10px; margin: 0px 10px 10px 0px; background: #efefef;display:table-cell; vertical-align:middle;}
		ul.easy-pinterest ol img {max-width: 100%; height: auto;}
		ul.easy-pinterest ol p {display: none;}

	</style>";
}
add_action( 'wp_head', 'thisismyurl_easy_pinterest_css' );

function thisismyurl_easy_pinterest_widget_Init() {
	register_widget( 'thisismyurl_easy_pinterest_widget' );
}
add_action( 'widgets_init', 'thisismyurl_easy_pinterest_widget_Init' );





// add menu to WP admin

function thisismyurl_easy_pinterest_menu() {add_options_page( __( 'Easy Pinterest' ), __( 'Easy Pinterest' ), 10,'thisismyurl_easy_pinterest.php', 'thisismyurl_easy_pinterest_options' );}
add_action( 'admin_menu', 'thisismyurl_easy_pinterest_menu' );

function thisismyurl_easy_pinterest_options( $options='' ) {

	$options = get_option( 'thisismyurl_easy_pinterest' );

	// save page options
	if ( $_GET['page'] == 'thisismyurl_easy_pinterest.php' && !empty( $_POST['easy_pinterest_update'] ) ) {

		$options->easy_pinterest_block = $_POST['easy_pinterest_block'];

		update_option( 'thisismyurl_easy_pinterest',$options );
		$options = get_option( 'thisismyurl_easy_pinterest' );
	}

?>

   <div class="wrap">

    <div class='plugin_header' style='background:#EEF0F7; margin-top: 20px; padding: 20px; -moz-border-radius: 5px; border-radius: 5px;'>
    	<div  style='float:left; width: 55%;'>
            <h2><?php _e( 'Easy Pinterest by Christopher Ross','easy_pinterest' ) ?></h2>
            <p>An easy way to add recent Pinterest posts to your WordPress website as a widget.</p>
            <form name="_xclick" action="https://www.paypal.com/cgi-bin/webscr" method="post">
            <input type="hidden" name="cmd" value="_xclick">
            <input type="hidden" name="business" value="info@thisismyurl.com">
            <input type="hidden" name="item_name" value="Donation for <?php echo $cr_wplink;?>">
            <input type="hidden" name="currency_code" value="USD">
            <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="Make payments with PayPal">
            </form>
        </div>
        <a style='float:right; width: 40%;' href='http://thisismyurl.com/plugins/'><img alt="thisismyurl.com" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAcIAAADICAMAAABMKhGlAAAAA3NCSVQICAjb4U/gAAAAjVBMVEUsQ4vl6POjpKRmZmZQY5/Y3Ou9xdyAjrpubm+ztLbP1OZoeaw3TZGZmZmIiIqZpMjJys7g5O/u8PexutXZ2t+NmcHGzeFEWJhcbqZ3d3jl5+6+v8Lc3+2urrDp7PWlr87X3Op0g7PU1tyQkZO7vMDd3+XU2OjDxMjJz+POz9R/f4HDxcrp6/HM0eS1tb3h3Tt2AAAACXBIWXMAAArwAAAK8AFCrDSYAAAAHHRFWHRTb2Z0d2FyZQBBZG9iZSBGaXJld29ya3MgQ1M0BrLToAAAABV0RVh0Q3JlYXRpb24gVGltZQAyLzIxLzEyu5lXCwAADfRJREFUeJztnYt6oroahtFaDyOtJcqWYUTRatUu5/5vb+fAIYkJBIxKOv/3rHZI8pEArzmR2OUhkOPynn0BoFsFCJ0XIHRegNB5AULnBQidFyB0XoDQeQFC5wUInRcgdF6A0HkBQucFCJ0XIHRegNB5AULnBQidFyB0XoDQeQFC52WK8AXr/a5XYqbQwxrqUiOSapCLqe+ZMr5GQNhV/RiEpyEWFwaEV+o6QvlGAOGVAGFXBQgb5dpF/aMIf5IAofMChM4LEDqvpyCMz1jV6TshYidHXKsCYYBnjEHN6cx1qnXV+wI5+WSWb2s9HuH57fcL1fck5uMPOdf9N0kdvx3yEz5fxAhZ5KEOBxQZUUojC4QbmuItQ56i/CIgHS2py4tmAmxTX5r7WGn+Ns0ShlNPjGBZSp+otLzuVEoOyqQ8RFPnoyiK6Efj0QhjxoNpzFfFhMRgw3uRuqcnvJX+RJ3n0BMU0sgM4WlQxPtcXZBuO+RO92eouS9kviAqUjckOtjKl4XQjAT4c7Gobc5ft3hzoRDCqdltDRXXqJcdhPH3iyCOIUMoGPbyCW/KTCsQnnz+oZcMxdveihlsUWMfQxgMuFTMUAjn9pQcT8U7IFXb53KqQ5jf1jMQ9ln9en/P6trvMokh/GbJWVu7Q9T3ndtflD2oHuGM3KofRXnrV5wi3PaIpQ4K36axjyEcCKWd2NnY7nEPXOCVKeUImyBMfT7HxyKMKRDGYT/OKlomihD/+qQDlx2tfd8Y+TiJS7uyGp5wrxDRuyIHEXuw9FHgyIjeZjrInmom/rZTBp72MCntupaoqY+Whn9NaQmsmRtg5D6zb3wO0ki4EiLats65nGoQss8E/qw8AeGepBbDFBL4LNKSovGkisdZj7jjzx1ry1WOSD1vlAWDpfAoeDdtHovOacA9TnNfKFbLIKsmRdO98cqqdxLyIaKfh4DLqRohsURF0Q9G2BcSvwUmDOFECo/LyQRlKgxieakRlt0V/aRPVW5fqE9zLeoqHyut5BKKBNnJOaWlJ3WGPhdhgNDjm/BHIyRdWr8I9dkYNFMi9Y1neRD6qe0MiZQI/XJ0Tj/7kcKd8pUVV6BWvlBsV7NHHZYRU47M1hM7wyEPxQghT/AJCEsoyTVCftrwItW6pDnCERehQzOUHnYrXygl0lSPm97xZOYSJZqWKozXxTKE5bBMdeda2WlI8VBUGsCIoT1nlvu+Fgj5HscUTYi1aeoL5brhSTVNIOOLOZFCBkrjVbEMofje8JnvSBUIeUJyTi0Q8vdaiVCap6lyrfRVlyYbpkJiIHzYDBCKM5KnIlT0hU9AyNo8zVtMU18zhBvhWud8O2qCUGxHn4lwN+4GQjaJS5FKpr5mCAPBTeaJS7URqScV+muslG2Eh+zdZxHxNIR0upFPwityrfI1Q0hb0gIE+WiMNMYOI9wXL7OLqKchRPmbzOkcyTL1NURIPg35ACYV22c3EB6S8ctLhxCespeauD0TJlwNfA0RUmxZbSYdo68zdhNhtvz3Mk7eO4IQBdOcDW4nhc7O1NcQIa3QWV0mE315zaPTCM+/c4Ax6gxC/GiKhT7cL3GdnamvKULSkmb939Lj3rU6gHDPAP6mU/gOISTr8cXSonZdUe9ripC87hsUR9p3AKh7CP+jAN//Y6FOIcSab+UFBmNfU4S07tFaTOrjtMLYNYS0FS0WI7qGEGuebbQJGvoaIxzlzSfpXjcVxo4hpM1ouULYQYTyspGprzFCwoIOYnxPeB3edYR0LFouAHYSIVtUjxr6GiMk6MgrmaHUjnYdofhGraMI6dsvr6GvOUIyl0hZvLCC33GEUlo3EKZk1Yif5EVqhJW+5gjnrA8ceNwrbpVx1mmE351AOJSNGoSVvuYIEe0MSVUe8LHKcU9nEdLtbB1ByL/1JBVj2dDXAuGUnD2/IqKafXQIIekLy3X4fTcQ0j6N26FBw9OGvhYIybvRlNQxcQlSMtLFxA4hfOdHpNkuwyLxacMZcWeauGHe1NcCIfkIzJZClkQbAVK2m9EcIds0pn5KNhD2uXlh/P1CJ/rF/iYbCLmxnTlCuj+0eNE8pxvAVVP7Kl8LhCRvcamQiu60y7Hm3wjpDkKW/ycu4DDBdZC2pG/519RuRFh+30H4Wgx3AzqEbHt7NMc40nkkPjJTXxuEbAn56juQS1YKPhqSe2o4nLk3QlQs9DJXFmRpNyLctEaYP8tS5Vzb1NcGYUrzkPYyid+ewkU0nBfeHeGOW+v9jvN1C5Z2I8KgWI9tjPDqG0vlCy9TXxuEbBfAFsni1rO8QdA1hGj3u6iDpBPs20OITlO/LUI0477DtqzY7K71tUJI28jrTRzBqChj1PjtjA2Eddp/Yorjz6wQ8pVt7dckHqnNltaJaHv9SFv5THQSPx9cwojuDh6pd8u1li2EoEJkzqfbW3wPAULrIl2rvI/qngKE1iUvFd5bgNC2Ng9uRwGhbQVL5Xj0jgKEtsTaTrondVljtStAaEvhchuGbBPcQyshILSm8g3a9ZuZuwoQ2lL4JIKA0JpCxQu6hwgQWtNwFoazu/5xRLUAofMChM4LEDovQOi8AKEt9YIVVu/xBQNCW/pfptWjCwaEtnQq9OCaCAhtKQ16vV7wizBcP7RgQGhZrxjh10NLBIS29fX379+HNqWA0LZ6v379srxHrVo/HiHuoHomfZOpr14pZqiphhdciJUyeHUe4eqVid36ujzkkvNAjwTWfOKvTK8Bj8fUV12aykD0hfNRzSx6WSlfq7V8CfIFEZEoowlKxxGuv3KxfQ298pDplUTkgYAEekKoFPc4TH3VpakMedzXVYVep1wZgeoSpI8CiXqVc1Gp4wgD7q6Jrp7ZSovwlQEJsFZf4mM19VWXpjKU+cl3khbFkExezRD+hFq4ku7tUo/wwh0Xzl7axlddmsrAolaii2bMO3tpmnKXIF5Qrh+DEN8sP7xbk3CgMwQkkNUhcrjSnGjqqy5NZaCW1foqVigyz6gMS6mqC9Or6whV44dYZ4jLUUHlSMTUV12aykBHIT3qq7wRemI5gImFUJHRjxjOBHVDQMHQk9CsNUZTX3VpKgN1sALErk0+c2U0IjXa1991hGQBhwuv5QUdwdAjAc3kbsUZTX3VpakM5PQgy6XyRsRLUF3QKsuqVv8KwoB/Iqa+5gh7WcaBjBYQFmqHkC3HFkmmvurSlAasPEF4/v8uwl7xTJjWJNzTGWhARtOjsUKKqa+6NIVhXQTlEuQze7UXFEhnaOUYQtQM4brAIsSb+qpLUxjK5EvNmf84Qu2ney2mariY+2rrkmQoKyFPU3mmeAlrTdlG78S7jrAnvduXI4Twml9uoIGecgHC1Fdd2nUEn4lklc8ULkEKacrSyUWEa51hzQcyLqpcTX3VpV0brnjqzxQuQQppytKp4wgvdQgvV49tXR5qFwBb+AwuZ33Fc214pmq18qcgXF+w+IiLFCEauIBsFNTEpy9NNoiJold5pr6gugvj1XWERFURYpgLWPPpU6WzVWazMxXJyii1Oo5QfsI3IVSGan2NEOqv3QBhxdlV6jpCUK0AofMChM4LEDovQOi8AKHzAoTOCxA6L0DovACh8wKEzgsQOi9A6LwAofMChM4LEDovQOi8AKHzAoTOCxA6L0DovACh8wKEzgsQOi9A6LwAofMChM4LEDovQOi8AKHzAoTOCxA6L0DovACh8wKETXRsfU6I+H+4g0oZuQBhjf4cuMAbCwtxNYonaHQqnnPxuM2eu5ELENYoWVyH5bgqfZxRNGz7nAGhBf3p98/HM6Z2QHGCEhomP+jQf/uDkxf7M/Wx4CF5w0aUnPv9w5+3hPzZmOQy8gcb5G18f4NQhNDM90f8wcZfnkZeFKAtJn3akhTyMxpuN4DQhg7JR/wxwfiOaJfghpSEyQ9meukfUNLfsT/N3d9dkh3CaM993N4e0cfbAk0+EOF+GsxS5IVo6JPHnS6DYHniDkI084ZoOkMjDBT/ECL4J1rOU0BoRbjRjPtod5yg45H0hVlDitGQiITVQWJB5zP5jfoxsS1YOjpjjHlD6pH/hgNc1wL+AA1xpQtDjBqhwbBAuEHQkNoRQda/HDESfFQiXPSTpH8sekVCLPvNbDnCSSwhRAMSFA4yhMgPAr9w5on1AoQ1IpD2iwmaXPqIR5gsFou4FuGFxIgI0SYaBMJBjnA7n28BoX0RSOdjgo7nhEd4wKHFQkCIG1KKkG9Iz2RWKCAM8AxjFPIHBcL5aDsHhPZFIMXJBDM58ghx44omZ4bwvKA9IBnOHPDQFHEI92QGKSA8LXF1m/EHBUI8hAmwJcDjG0BoUX/w8BLhXi/GQ0w6tcdh8oPnDZgiRUjgkiBCuz4eovIIafs68kKuIZ0OBqz9LA4KhNMpcS8HW0D4EMULRTBeCP/7n3inOPE0lA9yjTY0+tTsQgBhZzScL43+Ir4sQNgZhdG81XmA0HkBQucFCJ0XIHRegNB5AULnBQidFyB0XoDQeQFC5wUInRcgNNAd9/FaECCs0Z338VoQIKzRnffxWhAgrNbt+3i1m4RtCRBW6/Z9vLpNwtYECGt06z5e7SZhawKENbp1H692k7A1AcIa3bqPFxA+Xbfu49VtErYnQFijm/bxVmwStidAWKOb9vFWbBK2J0DYTC328So3CVsUILSjtvt4LQgQ2lHbfbwWBAidFyB0XoDQeQFC5wUInRcgdF6A0HkBQucFCJ0XIHRegNB5AULnBQidFyB0XoDQeQFC5wUIndf/Ac636W5ahpKYAAAAAElFTkSuQmCC" /></a>
    	<div style='clear:both'></div>
    </div>


	<div class="postbox-container" style="width:60%; margin-right: 5%;">



    <form action='options-general.php?page=thisismyurl_easy_pinterest.php' method='POST'>
    	<input type="hidden" name="easy_pinterest_update" value="1">
		<div class="metabox-holder">
		<div id="normal-sortables" class="meta-box-sortables">

			<div id="easy_pinterestsettings" class="postbox">
			<div class="handlediv" title="Click to toggle"><br /></div>
			<h3 class="hndle"><span><?php _e( 'Easy Pinterest Settings','easy_pinterest' ) ?></span></h3>
			<div class="inside">
				<p><label><input name="easy_pinterest_block" type="checkbox" value="1" <?php if ( $options->easy_pinterest_block == '1' ) { echo "checked";}?> />&nbsp;<?php _e( 'Block Pinterest from this domain','easy_pinterest' ) ?></label></p>
			</div>
			</div>

		<input style='margin-bottom: 20px;' type="submit" class="button-primary" value="<?php _e( 'Save Changes','easy_pinterest' ) ?>" />
		</div>
		</div>

		</form>
	</div>

</div>
</div>
<?php
}


function thisismyurl_easy_pinterest_block() {

	// look to see if the block option is active and if it is, add a meta tag to the head of the HTML page

	$options = get_option( 'thisismyurl_easy_pinterest' );
	$options_block = $options->easy_pinterest_block;
	if ( $options_block == '1' )
		echo '<meta name="pinterest" content="nopin" />';

}
add_action( 'wp_head', 'thisismyurl_easy_pinterest_block' );
