<?php

declare(strict_types=1);
use Braintree\Exception\NotFound;

class BraintreeHelper
{
    private $id;

    public function __construct($gateway, $id = null, bool $isTrial = true)
    {
        $this->gateway = $gateway;
        $this->isTrial = $isTrial;

        if ($id) {
            $this->id = $id;
        }
    }

    public function cleanId()
    {
        $this->id = null;
    }

    public function generateToken()
    {
        $token = $this->gateway->ClientToken()->generate([
          'customerId' => $this->id
        ]);
        $this->token = $token;

        return $token;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function createCustomer($nonce, $id = null)
    {
        $payload = [
            'firstName' => 'Mike-'. rand(0,1000),
            'lastName' => 'Jones',
            'company' => 'Jones Co.',
            'paymentMethodNonce' => $nonce,
        ];

        if ($id) {
            $payload['id'] = $id;
        }

        $result = $this->gateway->customer()->create($payload);

        $this->id = $result->customer->id;
        $this->token = $result->customer->paymentMethods[0]->token;

        return $result;
    }

    public function createSubscription($nonce, $planId = 'club-catch-monthly')
    {
        $payload = [
            'paymentMethodNonce' => $nonce,
            'planId' => $planId
        ];

        if (!$this->isTrial) {
            $payload['trialPeriod'] = $this->isTrial;
        }

        return $result = $this->gateway->subscription()->create($payload);
    }

    public function createSubscriptionWithToken($token, $planId = 'club-catch-monthly')
    {
        $payload = [
            'paymentMethodToken' => $token,
            'planId' => $planId
        ];

        if (!$this->isTrial) {
            $payload['trialPeriod'] = $this->isTrial;
        }

        return $result = $this->gateway->subscription()->create($payload);
    }

    public function processTransaction($amount)
    {
        return $this->gateway->transaction()->sale([
                'customerId' => $this->id,
                'amount' => $amount,
                'options' => [
                    'submitForSettlement' => true,
                ],
                'lineItems' => [
                    [
                        'name' => 'Product2',
                        'quantity' => 1,
                        'kind' => Braintree\TransactionLineItem::DEBIT,
                        'unitAmount' => 5,
                        'unitOfMeasure' => 'unit',
                        'totalAmount' => 5,
                        'productCode' => '154321',
                        'commodityCode' => '98765'
                    ],
                    [
                        'name' => 'Product3',
                        'kind' => Braintree\TransactionLineItem::DEBIT,
                        'quantity' => 2,
                        'unitAmount' => 2.5,
                        'unitOfMeasure' => 'unit',
                        'totalAmount' => 5,
                        'productCode' => '154321',
                        'commodityCode' => '98765'
                    ]
                ]
            ]);
    }

    public function findCustomer($id)
    {
        if (!$id) return null;
        try {
            return $this->gateway->customer()->find($id);
        } catch (NotFound $e) {
            return null;
        }

    }
}
