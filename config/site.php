<?php

return [

    'contries'=>[
        'AF'=> 'Afghanistan', 
        'AX' => 'Åland Islands', 
        'AL' => 'Albania', 
        'DZ' => 'Algeria', 
        'AS' => 'American Samoa', 
        'AD' => 'Andorra', 
        'AO' => 'Angola', 
        'AI' => 'Anguilla', 
        'AQ' => 'Antarctica',
        'AG'=> 'Antigua and Barbuda', 
        'AR' => 'Argentina',
        'AM'=> 'Armenia',
        'AW'=> 'Aruba',
        'AU'=> 'Australia',
        'AT'=> 'Austria',
        'AZ'=> 'Azerbaijan',
        'BS'=> 'Bahamas',
    ],
    
    'emailes'=>[
        'customer-welcome-email'=>'Customer welcome email',
        'new-file-service-created-email'=>'New file service email',
        'file-service-modified-email'=>'File service modified/completed',
        'file-service-processed-email'=>'File service processed',
        'new-subscription-email'=>'New subscription email',
        'subscription-cancelled'=>'Subscription cancelled',
        'payment-completed'=>'Payment completed',
        'payment-denied'=>'Payment denied',
        'payment-pending'=>'Payment pending',
		'new-ticket-created'=>'New ticket',
		'new-file-ticket-created'=>'Reply to Your ticket',
		'reply-to-your-ticket' =>'Ticket Reply',
		'customer-activate-email' =>'Company  Registration Activate',
		'new-company-apply'=>'New Company apply'
    ],

    'currency_sign'=>'£',

    'order_status'=>[
        'completed'=>'Completed',
        'cancelled'=>'Pending',
    ],

    'transaction_status'=>[
        'pending'=>'Pending',
        'completed'=>'Completed',
    ],

    'transaction_type'=>[
        'A'=>'Give (+)',
        'S'=>'Take (-)',
    ],

    'file_service_staus'=>[
        'O'=>'Open',
        'W'=>'Waiting',
        'C'=>'Completed',
    ],

    'file_service_gearbox'=>[
        1=>5,
        2=>6,
        15=>7,
        3=>'Automatic Transmission',
        13=>'CVT',
        4=>'DSG',
        5=>'DSG6',
        6=>'DSG7',
        7=>'DKG',
        8=>'DCT',
        9=>'SMG',
        10=>'SMG2',
        14=>'SMG3',
        11=>'Tiptronic',
        12=>'Multitronic',
    ],

	'file_service_fuel_type'=>[
        'Diesel'=>'Diesel',
        'Petrol'=>'Petrol',
    ],

	
    'package_billing_interval'=>[
        'Day'=>'Daily',
        'Week'=>'Weekly',
        'Month'=>'Monthly',
        'Year'=>'Yearly',
    ],

    'paypal_mode'=>[
        'sandbox'=>'Sandbox',
        'live'=>'Live'
    ],

];
