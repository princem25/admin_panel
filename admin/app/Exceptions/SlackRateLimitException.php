<?php
namespace App\Exceptions;

use Exception;

class SlackRateLimitException extends Exception
{
    protected $retryAfter;

    public function __construct($retryAfter, $message = "Slack rate limit hit", $code = 429)
    {
        $this->retryAfter = $retryAfter;
        parent::__construct($message, $code);
    }

    public function getRetryAfter()
    {
        return $this->retryAfter;
    }
}
