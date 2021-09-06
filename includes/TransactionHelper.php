<?php

class TransactionHelper
{
    public static function getErrorMessage($result): string
    {
        $errorString = "";

        foreach($this->result->errors->deepAll() as $error) {
            $errorString .= 'Error: ' . $error->code . ": " . $error->message . "\n";
        }

        return $errorString;
    }
}