<?php

function stripe_request($method, $path, $params = array()){
    $url="https://api.stripe.com/v1{$path}";
    if ($method == 'GET' && $params)
        $url.='?'.http_build_query($params);
    $ch=curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, STRIPE_SECRET_KEY.':');
    if ($method == 'POST'){
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    }
    $response=curl_exec($ch);
    if ($response === FALSE)
        throw new Exception('Stripe request failed: '.curl_error($ch));
    $status=curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $data=json_decode($response, true);
    if ($status >= 400)
        throw new Exception('Stripe error: '.($data['error']['message'] ?? $response));
    return $data;
}

function stripe_create_checkout_session($params){
    return stripe_request('POST', '/checkout/sessions', $params);
}

function stripe_retrieve_checkout_session($id){
    return stripe_request('GET', '/checkout/sessions/'.urlencode($id), array('expand' => array('payment_intent')));
}