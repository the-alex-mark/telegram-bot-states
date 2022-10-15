<?php

namespace ProgLib\Telegram\Bot\Logging\Formatter;

use Monolog\Formatter\LineFormatter as BaseLineFormatter;
use Monolog\Formatter\NormalizerFormatter;

class CustomizeTelegramLineFormatter extends NormalizerFormatter {

    /**
     * @inheritDoc
     */
    public function format(array $record): string {
        $vars   = parent::format($record);
        $output = '';

        $output .= '<b>Application:</b> ' . config('app.name') . PHP_EOL;
        $output .= '<b>Environment:</b> ' . config('app.env') . PHP_EOL;
        $output .= '<b>Log Level:</b> <code>' . $vars['level_name'] . '</code>' . PHP_EOL;

        if (!empty($vars['extra']))
            $output .= '<b>Extra:</b>' . PHP_EOL . '<code>' . json_encode($vars['extra']) . '</code>' . PHP_EOL;

        if (!empty($vars['context']))
            $output .= '<b>Context:</b>' . PHP_EOL . '<code>' . json_encode($vars['context']) . '</code>' . PHP_EOL;

        $output .= '<b>Message:</b>' . PHP_EOL . '<pre>' . $vars['message'] . '</pre>';

        return $output;
    }
}
