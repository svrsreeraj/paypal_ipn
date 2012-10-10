<?php
require_once('paypal.class.php');
$p	=	new paypal_class;

//$p->paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';   	
$p->paypal_url 	= 'https://www.paypal.com/cgi-bin/webscr';     			
$this_script 	= 'http://[URLHERE]/paypal_ipn/paypal.php';// CHANGE THIS

if(empty($_GET['action'])) $_GET['action'] = 'process';// if there is not action variable, set the default action of 'process'

switch ($_GET['action']) 
	{
		case 'process':
			// Process and order...
			$p->add_field('first_name', "FIRST NAME");
			$p->add_field('last_name', "LAST NAME");
			$p->add_field('business', 'test@gmail.com'); // CHANGE THIS
			$p->add_field('return', $this_script.'?action=success');
			$p->add_field('cancel_return', $this_script.'?action=cancel');
			$p->add_field('notify_url', $this_script.'?action=ipn');
			$p->add_field('item_name', 'Item Name'); // item_name will be come here from DB
			$p->add_field('amount', '0.01'); // payment will be come here from DB

			
			
			$p->submit_paypal_post(); 
			//$p->dump_fields();	// for debugging
		break;
		case 'success':        
			echo "<html><head><title>Success</title></head><body><h3>Thank you for your Membership Payment.</h3>";
			foreach ($_POST as $key => $value) { echo "$key: $value<br>"; }
			echo "</body></html>";
		break;
		case 'cancel':       
			// Order was canceled...
			echo "<html><head><title>Canceled</title></head><body><h3>The Payment was canceled.</h3>";
			echo "</body></html>";
		break;
		case 'ipn':          
			$to 		=	'test@gmail.com'; 
			if ($p->validate_ipn())//Paypal is calling page for IPN validation...
				{
					// For this example, we'll just email ourselves ALL the data.
					$subject 	=	'Instant Payment Notification - Recieved Payment';
					$body 		=	"An instant payment notification was successfully recieved\n";
					$body 		.=	"from ".$p->ipn_data['payer_email']." on ".date('d/m/Y');
					$body 		.=	" at ".date('g:i A')."\n\nDetails:\n";
					
					foreach ($p->ipn_data as $key => $value)	$body .= "\n$key: $value";
					mail($to, $subject, $body);
				}
			else
				{
					mail($to, "Not Recieved", ":(");
				}
		break;
	}     

?>
