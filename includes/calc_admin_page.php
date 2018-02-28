<?php
add_action( 'admin_menu', 'post_cost_calculator_plugin_menu' );

/** Step 1. Add Menu*/
function post_cost_calculator_plugin_menu() {
	add_menu_page(
		'Post Cost Calculator',
		'Post Cost Calculator',
		'manage_options',
		'post-cost-calculator',
		'post_cost_calculator_options',
		 plugins_url('/post-cost-calculator/assets/images/icon.png')
	);
}


//Check which author was previously selected
//selected="selected"

/** Step 2. */
function post_cost_calculator_options()  {
  //must check that the user has the required capability
    if (!current_user_can('manage_options'))
    {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }

		//query all authors
		$authors = get_users( 'orderby=display_name&role=author' );

    //variables for the field
    $option_Author = 'author';
		$option_date1 = 'date1';
		$option_date2 = 'date2';
		$option_rates = 'rate';
		$option_equation = 'equation';
		$option_acf = 'acfwhitelist';
		$option_acf_black = 'acfblacklist';

		//option names
    $hidden_field_name = 'mt_submit_hidden';
    $data_field_name = 'author';
		$data_field_from = 'from';
		$data_field_to = 'to';
		$data_field_equation = 'equation';
		$data_field_acf = 'acf';
		$data_field_acf_black = 'acf_black';


    // Read in existing option value from database
    $option_Author_val = get_option( $option_Author );
		$option_date1_val = get_option( $option_date1 );
		$option_date2_val = get_option( $option_date2 );
		$author_rates_val = get_option( $option_rates );
		$option_equation_val = get_option( $option_equation );
		$option_acf_val = get_option( $option_acf );
		$option_acf_black_val = get_option( $option_acf_black );

    // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {

        // Read their posted value
				if ( $_POST[ $data_field_name ] !== 'y2k' ) {
        $option_Author_val = $_POST[ $data_field_name ];
				}
				$option_date1_val = $_POST[ $data_field_from ];
				$option_date2_val = $_POST[ $data_field_to ];
				$option_equation_val = $_POST[ $data_field_equation ];
				$option_acf_val = $_POST[$data_field_acf];
				$option_acf_black_val = $_POST[$data_field_acf_black];

				$i = 0;
				foreach ( $authors as $author ) {
						$author_rates_val[$i] = $_POST['author'.$i];
						$i++;
				}

      // Save the posted value in the database
      update_option( $option_Author, $option_Author_val );
			update_option( $option_date1, $option_date1_val );
			update_option( $option_date2, $option_date2_val );
			update_option($option_rates, $author_rates_val);
			update_option($option_equation, $option_equation_val);
			update_option($option_acf, $option_acf_val);
			update_option($option_acf_black, $option_acf_black_val);

?>
<div class="updated"><p><strong><?php _e('settings saved.', 'menu-test' ); ?></strong></p></div>
<?php

//Query and calculation form
} ?>
<div class="wrap">
 <h2>Post Cost Calculator</h2>
 <div id="form-header">
	 <h3>Author:</h3>
	 <h3>Rate:</h3>
	 <h3>Date:</h3>
	 <h3>Update View</h3>
 	 <h3>Custom Equation</h3>
 </div>
  <form id="post-cost-form" name="form1" method="post" action="">
	  	<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

			<select class="post-calc-block" name="<?php echo $data_field_name; ?>" size="<?php echo count($authors) + 2; ?>">
				<option class="hidden_option" value="y2k" selected="selected"></option>
				<option value="">All</option>
			<?php
					foreach ( $authors as $author ) {
						echo '<option value="'. esc_html( $author->display_name ) .'" >'. esc_html( $author->display_name ) . '</option>';
					}
				?>
			</select>


			<ul id="author-rates" class="post-calc-block">

				<?php
				$i = 0;
				foreach ( $authors as $author ) {
						echo '<li class="author-rate">'. esc_html( $author->display_name ) . '<input class="rate-input" type="text" name="author'.$i.'" value="'.$author_rates_val[$i].'"></li>';
						$i++;
				}
				?>
			</ul>


			<div id="date-inputs" class="post-calc-block">
				<label for="from">From</label>
				<input type="text" id="from" name="<?php echo $data_field_from; ?>" value="<?php echo $option_date1_val; ?>">
				<label for="to">to</label>
				<input type="text" id="to" name="<?php echo $data_field_to; ?>" value="<?php echo $option_date2_val; ?>">
				<h3>ACF Fields</h3>
				<label><strong>White List:</strong> Fields to include in calculation. Seperate with a single space.</label>
				<input type="textarea" name="<?php echo $data_field_acf; ?>" value="<?php echo $option_acf_val; ?>"/><br>
				<label><strong>Black List:</strong> ACF sub-fields to exclude in calculation.</label>
				<input type="textarea" name="<?php echo $data_field_acf_black; ?>" value="<?php echo $option_acf_black_val; ?>"/><br>
			</div>


		<div class="post-calc-block submit">
			<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
		</div>

		<div class="post-calc-block">
			<label>Equation:</label>
			<br>
			<input id="post-calc-equation" type="text" name="<?php echo $data_field_equation; ?>" value="<?php echo $option_equation_val; ?>"/>
			<br>
			<label>Answer:</label>
			<br>
			<input id="post-calc-answer" text="text" disabled="disabled"/>
			<p>tc Total Cost Variable<br>+ Add<br>- Subtract<br> * Multiply<br>/ Division<br>( ) Contain</p>
		</div>
	</form>
</div>

<?php
//Execute query, execute calculations and display results
?>
<div id="post-cost-table-container">
	<?php

	echo '<span>Displaying results for '.  $option_Author_val . ' between '.  $option_date1_val . ' and '.  $option_date2_val . '.</span>';

	//query authors based on user inputs
	$after = get_option($option_date1) . ' 00:00:00';
	$before = get_option($option_date2) . ' 23:59:59';

  $articlesToCalc = new WP_Query( array(
	'posts_per_page'   => -1,
  'author_name' => get_option($option_Author),
  'date_query' => array(
    array(
      'after' => $after,
      'before' => $before,
			'inclusive' => true,
			'column' => 'post_date',
	    ),
	  ),
	));


	//Display data from query results in a table
	//intialize total cost variable
	$tc = 0;
	if ( $articlesToCalc->have_posts() ) {
		echo '<table class="post-cost-table"><tr><th>Author</th><th>Post Title</th><th>Date</th><th>Word Count</th><th>Rate</th><th>Cost</th></tr>';
		while ( $articlesToCalc->have_posts() ) {
			$articlesToCalc->the_post();
			$post = get_post( get_the_ID() );
			$wordCount = Post_Cost_Calculator::calculate_word_count($post);

			//Find current rate by author
			$rate = 0;
			$rate_options = get_option($option_rates);
			$authorIndex = array_search(get_userdata($post->post_author) , $authors);
			$rate = $rate_options[$authorIndex];

			$cost = $wordCount * $rate;
			$tc += $cost;
			echo '<tr><td>' . get_the_author() . '</td><td>'. get_the_title() . '</td><td>' . get_the_date() . '</td><td>' . $wordCount . '</td><td>' . $rate . '</td><td>' . $cost . '</td></tr>';
		}
		echo '</table>';
		/* Restore original Post Data */
		wp_reset_postdata();
	} else {
		echo'<p>No Articles Found with these parameters.</p>';
	}

	?>

	<div id="total-cost">Total Cost:
		<span id="total-cost-num">
			<?php echo $tc;?>
		</span>
	</div>
</div>

<?php
}
