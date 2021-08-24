<?php
require_once("../includes/braintree_init.php");
require_once("../includes/BraintreeHelper.php");

$amount = $_POST["amount"];
$nonce = $_POST["payment_method_nonce"];
$id = $_POST["id"];
$braintreeHelper = new BraintreeHelper($gateway, $id);
if (!$id) {
    $resultCustomer = $braintreeHelper->createCustomer($nonce);
    $token = $braintreeHelper->getToken();
    echo '<pre>';
    var_dump($braintreeHelper->findCustomer($resultCustomer->customer->id)->paymentMethods[0]->subscriptions);
    echo '</pre>';
    $resultSubscription = $braintreeHelper->createSubscriptionWithToken($token);
} else {
    echo '<pre>';
    var_dump($braintreeHelper->findCustomer($id)->paymentMethods[0]->subscriptions);
    echo '</pre>';
    $resultSubscription = $braintreeHelper->createSubscription($nonce);
}

$resultTransaction = $braintreeHelper->processTransaction($amount);

// if (($resultTransaction->success) || !is_null($resultTransaction->transaction)) {
//     $transaction = $resultTransaction->transaction;
//     header("Location: " . $baseUrl . "transaction.php?id=" . $transaction->id);
// } else {
//     $errorString = "";

//     foreach($resultTransaction->errors->deepAll() as $error) {
//         $errorString .= 'Error: ' . $error->code . ": " . $error->message . "\n";
//     }

//     $_SESSION["errors"] = $errorString;
//     header("Location: " . $baseUrl . "index.php");
// }