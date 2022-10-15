<?php

namespace ProgLib\Telegram\Bot\Logging\Formatter;

use Monolog\Formatter\NormalizerFormatter;

class CustomizeTelegramJsonFormatter extends NormalizerFormatter {

    /**
     * @inheritDoc
     */
    public function format(array $record): string {
        $vars   = parent::format($record);
        $output = '<pre>' . json_encode($vars, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</pre>';

        return $output;
    }
}
