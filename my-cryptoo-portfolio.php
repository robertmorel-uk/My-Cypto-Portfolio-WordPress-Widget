<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.linkedin.com/in/robertmorel/
 * @since             1.1.0
 * @package           My_Cryptoo_Portfolio
 *
 * @wordpress-plugin
 * Plugin Name:       My Cryptoo Portfolio
 * Plugin URI:        https://www.linkedin.com/in/robertmorel/
 * Description:       Calculate how much fiat money your crypto portfolio is worth.
 * Version:           1.1.0
 * Author:            Robert Christopher Morel
 * Author URI:        https://www.linkedin.com/in/robertmorel/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       my-cryptoo-portfolio
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.1.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLUGIN_NAME_VERSION', '1.1.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-my-cryptoo-portfolio-activator.php
 */
function activate_my_cryptoo_portfolio() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-my-cryptoo-portfolio-activator.php';
	My_Cryptoo_Portfolio_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-my-cryptoo-portfolio-deactivator.php
 */
function deactivate_my_cryptoo_portfolio() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-my-cryptoo-portfolio-deactivator.php';
	My_Cryptoo_Portfolio_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_my_cryptoo_portfolio' );
register_deactivation_hook( __FILE__, 'deactivate_my_cryptoo_portfolio' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-my-cryptoo-portfolio.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.1.0
 */
function run_my_cryptoo_portfolio() {

	$plugin = new My_Cryptoo_Portfolio();
	$plugin->run();

}
run_my_cryptoo_portfolio();

class My_Cryptoo_Portfolio_Widget extends WP_Widget {

	//Set default values
	protected $defaults = array(
        'title' => '',
		'crypt_id_0' => '',
		'crypt_id_1' => '',
		'crypt_id_2' => '',
		'crypt_id_3' => '',
        'crypt_id_4' => '',
		'coin_bal_0' => '',
		'coin_bal_1' => '',
		'coin_bal_2' => '',
		'coin_bal_3' => '',
		'coin_bal_4' => '',
	);

	//Widget constructor with name, text domain (mcp), description
	public function __construct() {
		parent::__construct( 
			'My_Cryptoo_Portfolio_Widget', 
			esc_html__( 'Cryptoo Portfolio Widget', 'mcp' ), 
			array( 
				'customize_selective_refresh' => true,
				'description' => esc_html__( 'Calculate how much fiat money your crypto portfolio is worth.', 'mcp' ) 
				) 
			);
	}
	
	//outputs the content of the widget to the user
	public function widget( $args, $instance ) {
		//pass default values to array of this class object
		$instance = wp_parse_args( (array) $instance, $this->defaults );
        //filters the widget title using the Base ID for the widget
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

		//set crypto id
        //allows code or classes to be passed as arguments above (before) the widget
		echo $args['before_widget'];
		//if the title form field is completed then display with optional classes before and after
		if ( $title ) {
			echo $args['before_title'] . esc_html__( $title ) . $args['after_title'];
		}
		
		//crypto portfolio balance list
		for($n = 0; $n < 5; $n++) {
			${'a_crypt_id_'.$n} = isset( $instance['crypt_id_'.$n] ) ? $instance['crypt_id_'.$n] : '';
			${'a_coin_bal_'.$n} = isset( $instance['coin_bal_'.$n] ) ? $instance['coin_bal_'.$n] : '';
		}


		$myCoins = array(
			$a_crypt_id_0 => array ( 'balance' => $a_coin_bal_0 ),
			$a_crypt_id_1 => array ( 'balance' => $a_coin_bal_1 ),
			$a_crypt_id_2 => array ( 'balance' => $a_coin_bal_2 ),
			$a_crypt_id_3 => array ( 'balance' => $a_coin_bal_3 ),
			$a_crypt_id_4 => array ( 'balance' => $a_coin_bal_4 )
		 );

		 //access coinmarketcap api to get current coin values
		 $coinbasePublicAPI = 'https://api.coinmarketcap.com/v1/ticker/';
		 //retrieve file
		 $coinData = file_get_contents($coinbasePublicAPI);
		 //decode json
		 $coinData = json_decode($coinData, true);
		 //set up html table
	
		 //set up html table
		 ?>
		 <table class="mcp-crypto-table">
			<tr>
				<td>NAME</td><td>SYMBOL</td><td>PRICE</td><td>HOLDINGS</td><td>VALUE</td>
			</tr>
		 <?php
		 //return the number of elements in coinData array
		 $numCoins = sizeof ($coinData);
		 $portfolioValue = 0;
		 for ( $i=0; $i < $numCoins; $i++) {
			//compare portfolio symbols with api symbols
			$thisCoinSymbol = $coinData[$i]['symbol'];
			// if thisCoinSymbol exists in myCoins array assign to coinHeld variable
			$coinHeld = array_key_exists($thisCoinSymbol, $myCoins); 
			?>
			<tr>
				<td>
				<?php echo $coinData[$i]['name']; ?></td><!--name-->
			    <td><?php echo $thisCoinSymbol; ?></td><!--symbol-->
			    <?php $thisCoinPrice = $coinData[$i]['price_usd']; ?><!--cprice-->
			    <td>&#36; <?php echo number_format($thisCoinPrice,2); ?></td>
                <td>
				<?php
				  if ($coinHeld) {
					 $myBalance_units = $myCoins[$thisCoinSymbol]['balance'];
					 echo number_format($myBalance_units,7);//holdings
				  } 
				?>
			   </td>
			   <?php
			   // track running total value of coins:
			   if ($coinHeld) {
				  $myBalance_USD = $myBalance_units * $thisCoinPrice;
				  $portfolioValue += $myBalance_USD;
			   }
			   ?>
			   <td>&#36; <?php echo number_format($myBalance_USD,2); ?></td>
			   </tr>
			   <?php } ?>
		       <tr>
				<td colspan="4"><strong>TOTAL</strong></td>
				<td colspan="4"><strong>&#36; <?php echo number_format($portfolioValue,2); ?></strong></td>
			   </tr>
		       </table>
			<?php
	
		echo $args['after_widget'];
	}

	public function form( $instance ) {
		// outputs the options form on admin
		$instance = wp_parse_args( (array) $instance, $this->defaults );

		$title = $instance['title'];

		for($i = 0; $i <= 5; $i++) {
			${'crypt_id_'.$i} = $instance['crypt_id_'.$i];
			${'coin_bal_'.$i} = $instance['coin_bal_'.$i];
		}
	?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
				<?php esc_html_e( 'Title:', 'mcp' ); ?>
			</label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" class="widefat" />
		</p>

		<?php for ($j=0; $j < 5; $j++){  ?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'crypt_id_'.$j ) ); ?>">
				<?php esc_html_e( 'Cryptocurrency:', 'mcp' ); ?>
			</label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'crypt_id_'.$j ) ); ?>" 
				name="<?php echo esc_attr( $this->get_field_name( 'crypt_id_'.$j ) ); ?>">
				<?php foreach ( $this->get_cryptocurrency_choices() as $key => $value ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( ${'crypt_id_'.$j}, $value ); ?>>
						<?php echo esc_html( $value ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'coin_bal_'.$j ) ); ?>">
				<?php esc_html_e( 'Balance:', 'mcp' ); ?>
			</label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'coin_bal_'.$j ) ); ?>
			" name="<?php echo esc_attr( $this->get_field_name( 'coin_bal_'.$j ) ); ?>
			" type="text" value="<?php echo esc_attr( ${'coin_bal_'.$j} ); ?>" class="widefat" />
		</p>

	<?php } ?>

		<?php
	}

	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
		$instance = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );

		for($m = 0; $m < 5; $m++) {
		$instance['crypt_id_'.$m] = sanitize_text_field( $new_instance['crypt_id_'.$m] );
		$instance['coin_bal_'.$m] = sanitize_text_field( $new_instance['coin_bal_'.$m] );
		}
		return $instance;
	}

	private function get_cryptocurrency_choices() {

		$api_url  = 'https://api.coinmarketcap.com/v2/ticker/?limit=10';
		$endpoint = 'cryptocurrency/map';
		
		$url = $api_url . $endpoint;
		
		$request_params = array(
			'timeout' => 60,
		);

		$response = wp_safe_remote_get( $url, $request_params );

		if ( ! is_wp_error( $response ) && ! empty( $response ) && isset( $response['response']['code'] ) 
		&& 200 === $response['response']['code'] ) {
			$json = json_decode( $response['body'] );
		}
	
		$choices = array();
		foreach ( $json->data as $dataReturn ) {
			$choices[ $dataReturn->id ] = sprintf( '%s', $dataReturn->symbol );
		}
		
		return $choices;

	}
}

add_action( 'widgets_init', function(){
	register_widget( 'My_Cryptoo_Portfolio_Widget' );
});
