<?php
require_once("../includes/braintree_init.php");
require_once("../includes/BraintreeHelper.php");
require_once("../includes/CustomerHelper.php");
require_once("../includes/TransactionHelper.php");
require_once("../includes/Exception/InvalidPaymentException.php");

$amount = $_POST["amount"];
$nonce = $_POST["payment_method_nonce"];
$id = $_POST["id"];
$braintreeHelper = new BraintreeHelper($gateway, $id);

$customer = $braintreeHelper->getOrCreateCustomer($id, $nonce);

$customerHelper = new CustomerHelper($customer, $braintreeHelper);
$nonce = $customerHelper->getNonce();

$resultTransaction = $braintreeHelper->processTransaction($amount);
if (!$resultTransaction->success) {
    $message = TransactionHelper::getErrorMessage($resultTransaction);
    throw new InvalidPaymentException($message);
}

$customerHelper->prepareSubscription();
$resultSubscription = $braintreeHelper->createSubscription($nonce);
if (!$resultSubscription->success) {
    $message = TransactionHelper::getErrorMessage($resultSubscription);
    throw new InvalidPaymentException($message);
}

if (($resultTransaction->success) || !is_null($resultTransaction->transaction)) {
    $transaction = $resultTransaction->transaction;
    header("Location: " . $baseUrl . "transaction.php?id=" . $transaction->id);
}