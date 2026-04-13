<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class ContextLogger
{
    /**
     * Determine the correct log channel based on the current request context.
     *
     * @return string
     */
    protected function getChannel(): string
    {
        $request = request();
        if ($request && $request->attributes->has('user_type')) {
            $userType = $request->attributes->get('user_type');
            if (in_array($userType, ['admin', 'customer'])) {
                return $userType;
            }
        }
        
        // Fallback to default
        return config('logging.default');
    }

    /**
     * Log an informational message to the dynamic channel.
     *
     * @param string $message
     * @param array $context Additional temporary context
     */
    public function info(string $message, array $context = []): void
    {
        Log::channel($this->getChannel())->info($message, $context);
    }

    /**
     * Log a debug message to the dynamic channel.
     *
     * @param string $message
     * @param array $context
     */
    public function debug(string $message, array $context = []): void
    {
        Log::channel($this->getChannel())->debug($message, $context);
    }

    /**
     * Log an error message to the dynamic channel.
     *
     * @param string $message
     * @param array $context
     */
    public function error(string $message, array $context = []): void
    {
        Log::channel($this->getChannel())->error($message, $context);
    }

    /**
     * Log a warning message to the dynamic channel.
     *
     * @param string $message
     * @param array $context
     */
    public function warning(string $message, array $context = []): void
    {
        Log::channel($this->getChannel())->warning($message, $context);
    }
}
