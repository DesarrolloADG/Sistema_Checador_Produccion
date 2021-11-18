<?php  
require_once("/Applications/XAMPP/htdocs/lib/Conekta.php");

 \Conekta\Conekta::setApiKey("key_eYvWV7gSDkNYXsmr");
 \Conekta\Conekta::setLocale('es');
 \Conekta\Conekta::setApiVersion("2.0.0");



/************
//CREAR USUARIO
 $customer = \Conekta\Customer::create(
  array(
    'name'  => "JORGE ,MAÑON",
    'email' => "jorge@airmovil.com",
    'phone' => "+5215555555555",
    'plan_id'  => "gold-plan",
    'corporate' => false,
    'payment_sources' => array(array(
        'token_id' => "tok_test_visa_4242",
        'type' => "card"
    )),
    'shipping_contacts' => array(array(
      'phone' => "+5215555555555",
      'receiver' => "Marvin Fuller",
      'between_streets' => "Morelos Campeche",
      'address' => array(
        'street1' => "Nuevo Leon 4",
        'street2' => "fake street",
        'city' => "Ciudad de Mexico",
        'state' => "Ciudad de Mexico",
        'country' => "MX",
        'postal_code' => "06100",
        'residential' => true
      )
    ))
  )
);

 print_r($customer);
 /************
//ACTUALIZAR USUARIO
$customer = \Conekta\Customer::find("cus_2grEJvMRudnDS5TeB");
$customer->update(
  array(
    'name'  => "Jorge Mañon Arroyo",
    'email' => 'jorge.manon@airmovil.com',
  )
);
/************

//ELIMINAR USUARIO
$customer = \Conekta\Customer::find("cus_2grEJvMRudnDS5TeB");
$customer->delete();
print_r($customer);
 /************

//CREAR PLAN
$plan = \Conekta\Plan::create(array(
    'id' => "jorge-plan",
    'name' => "Jorgy Plan",
    'amount' => 10000,
    'currency' => "MXN",
    'interval' => "month",
    'frequency' => 1,
    'trial_period_days' => 15,
    'expiry_count' => 12
));
 /************

//MODIFICAR PLAN
$plan = \Conekta\Plan::find("jorge-plan");
$plan->update(
  array(
    'id' => "silver-plan",
    'name' => "Silver Plan"
));
print_r($plan);
 /************

//ELIMINAR PLAN
$plan = \Conekta\plan::find("jorge-plan");
$plan->delete();
print_r($plan);
/************

//CREAR SUSCRIPCION
$customer = \Conekta\Customer::find("cus_2grEJvMRudnDS5TeB");
$subscription = $customer->createSubscription(
  array(
    'plan' => 'jorge-plan'
  )
);
print_r($subscription);
/************

//MODIFICAR SUSCRIPCION
$customer = \Conekta\Customer::find("cus_2grEJvMRudnDS5TeB");
$subscription = $customer->subscription->update(array(
  'plan' => 'opal-plan'
));

print_r($subscription);

/************

//PAUSAR SUSCRIPCION
$customer = \Conekta\Customer::find("cus_2grEJvMRudnDS5TeB");
$subscription = $customer->subscription->pause();
print_r($subscription);
/************
//REANUDAR SUSCRIPCION
$customer = \Conekta\Customer::find("cus_2grEJvMRudnDS5TeB");
$subscription = $customer->subscription->resume();
print_r($subscription);

/************

//CANCELAR SUSCRIPCION
$customer = \Conekta\Customer::find("cus_2grEJvMRudnDS5TeB");
$subscription = $customer->subscription->cancel();
print_r($subscription);

/************
//Paginado
$object_window = \Conekta\Customer::where();
$object_window->next();
print_r($object_window);
/************

//CREAR METODO DE PAGO
$customer = \Conekta\Customer::find("cus_2grEJvMRudnDS5TeB");
$source = $customer->createPaymentSource(array(
  'token_id' => 'tok_test_visa_4242',
  'type'     => 'card'
));
print_r($source);
/************

//ACTUALIZAR METODO DE PAGO
$customer = \Conekta\Customer::find("cus_2grEJvMRudnDS5TeB");
$customer->payment_sources[0]->update(array(
  'exp_month' => 11
));

print_r($customer->payment_sources[0]);
/************

//ELIMINAR METODO DE PAGO
$customer = \Conekta\Customer::find("cus_2grEJvMRudnDS5TeB");
$source   = $customer->payment_sources[0]->delete();
print_r($customer->payment_sources[0]);

/***********
//CREAR ORDEN
$orden = \Conekta\Order::create(array(
  'currency' => 'MXN',
  'customer_info' => array(
    'customer_id' => 'cus_2grEJvMRudnDS5TeB'
  ),
  'line_items' => array(
    array(
      'name' => 'Box of Cohiba S1s',
      'unit_price' => 35000,
      'quantity' => 1
    )
  ),
  'charges' => array(
    array(
      'payment_method' => array(
        'type' => 'default'
      )
    )
  )
));
print_r($orden);

/**********/


$order = \Conekta\Order::find("ord_2fw8EWJusiRrxdPzT");
$order->update(array(
  'currency' => 'MXN'
));
print_r($order);
/**********/










