<?php

class CustomerHelper
{
    public function __construct($customer, $braintreeHelper)
    {
        $this->customer = $customer;
        $this->braintreeHelper = $braintreeHelper;
    }

    public function getSubscriptions()
    {
        return $this->customer->paypalAccounts[0]->subscriptions;
    }

    public function prepareSubscription()
    {
        $subscriptions = $this->getSubscriptions();

        foreach($subscriptions as $subscription) {
            if ($subscription->status === Braintree\Subscription::ACTIVE
) {
    $this->braintreeHelper->cancelSubscription($subscription->id);
            }
        }
    }

    public function getNonce(): string
    {
        $token = $this->customer->paymentMethods[0]->token;
        $resultPaymentMethodNonce = $this->braintreeHelper->getGateway()->paymentMethodNonce()->create($token);
        $nonce = $resultPaymentMethodNonce->paymentMethodNonce->nonce;

        return $nonce;
    }
}