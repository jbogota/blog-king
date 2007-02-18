<?php
/**
	* @desc Output of  the Excerpt
	* @author Georg Leciejewski
	*/
	function c_widget_excerpt($args, $number = 1) {
			$options = get_option('c_widget_excerpt');
			$before_widget		= stripslashes($options[$number]['before_widget']);
			$after_widget 		= stripslashes($options[$number]['after_widget']);

			echo $before_widget."\n";
			the_excerpt();
			echo $after_widget."\n";
	}
	/**
	* @desc Output of plugin?s editform in the adminarea
	* @author Georg Leciejewski
	*/
	function c_widget_excerpt_control($number) {
		// Get our options and see if we're handling a form submission.
		$options = $newoptions = get_option('c_widget_excerpt');

		if ( $_POST["c_widget_excerpt_submit_$number"] )
		{
			$newoptions[$number]['before_widget']	= html_entity_decode($_POST["c_widget_excerpt_before_$number"]);
			$newoptions[$number]['after_widget']	= html_entity_decode($_POST["c_widget_excerpt_after_$number"]);
		}
		if ( $options != $newoptions )
		{
			$options = $newoptions;
			update_option('c_widget_excerpt', $options);
		}
		$before_widget		= stripslashes(htmlentities($options[$number]['before_widget']));
		$after_widget		= stripslashes(htmlentities($options[$number]['after_widget']));

		//before widget
		echo king_get_textbox_p(array(
				'Label_Id_Name' 	=>"c_widget_excerpt_before_$number",

				'Description' 	=> __('HTML before the Excerpt', 'widgetContent'),
				'Label_Title' 	=>  __('HTML which opens this widget. Can be something linke ul with a class, depending on your css and Theme', 'widgetContent'),
				'Value' 			=>$before_widget,
				'Size' 			=>'50',
				'Max' 			=>'300'));
		//after widget
		echo king_get_textbox_p(array(
				'Label_Id_Name' 	=>"c_widget_excerpt_after_$number",
				
				'Description' 	=> __('HTML after Excerpt', 'widgetContent'),
				'Label_Title' 	=>__('HTML which closes this widget. Can be something linke /ul , depending on what you set as HTML before', 'widgetContent'),
				'Value' 			=>$after_widget,
				'Size' 			=>'50',
				'Max' 			=>'300'));

		echo king_get_hidden("c_widget_excerpt_submit_$number",'1',"c_widget_excerpt_submit_$number");
	}

	/**
	* @desc takes the call from the number of boxes form and initiates new instances
	* @author Georg Leciejewski
	*/
	function c_widget_excerpt_setup() {
		$options = $newoptions = get_option('c_widget_excerpt');

		if ( isset($_POST['c_widget_excerpt_number_submit']) ) {
			$number = (int) $_POST['c_widget_excerpt_number'];
			if ( $number > 9 ) $number = 9;
			if ( $number < 1 ) $number = 1;
			$newoptions['number'] = $number;
		}
		if ( $options != $newoptions ) {
			$options = $newoptions;
			update_option('c_widget_excerpt', $options);
			c_widget_excerpt_register($options['number']);
		}
	}
	/**
	* @desc Admin Form to select number of Excerpt
	* @author Georg Leciejewski
	*/
	function c_widget_excerpt_page() {

		$options = $newoptions = get_option('c_widget_excerpt');
		echo king_get_start_form('wrap','','',$k_Method='post');
		?>
		<h2><?php _e('Excerpt Widgets', 'widgetContent'); ?></h2>
		<?php
		echo king_get_start_p();
		_e('How many Excerpt Boxes would you like? ', 'widgetContent');
		echo king_get_select("c_widget_excerpt_number", $options['number'], array('1', '2', '3', '4', '5', '6', '7', '8', '9',), 'c_widget_excerpt_number' );
		echo king_get_submit('c_widget_excerpt_number_submit','','c_widget_excerpt_number_submit');
		echo king_get_end_p();
		echo king_get_end_form ();
	}

	/**
	* @desc Calls all other functions in this file initializing them
	* @author Georg Leciejewski
	*/
	function c_widget_excerpt_register()
	{
		$options = get_option('c_widget_excerpt');
		$number = $options['number'];
		if ( $number < 1 ) $number = 1;
		if ( $number > 9 ) $number = 9;
		for ($i = 1; $i <= 9; $i++) {
			$name = array('Excerpt %s', null, $i);
			register_page_widget($name, $i <= $number ? 'c_widget_excerpt' : '', $i);
			register_c_widget_control($name, $i <= $number ? 'c_widget_excerpt_control' : '', 450, 200, $i);
		}
		add_action('page_admin_setup', 'c_widget_excerpt_setup');
		add_action('page_admin_page', 'c_widget_excerpt_page');
	}

?>
