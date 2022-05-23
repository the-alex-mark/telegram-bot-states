<?php

namespace ProgLib\Telegram\Database\Eloquent\Concerns;

use Illuminate\Database\Eloquent\Model;

/**
 * @property-read bool $json_pretty_print Определяет, требуется ли форматировать JSON
 *
 * @see Model
 */
trait HasOverrides {

    /**
     * @inheritDoc
     */
    public function fromJson($value, $asObject = false) {
        $data = json_decode($value, !$asObject);

        if (!$asObject)
            $data = is_null($data) ? [] : $data;

        return $data;
    }

    /**
     * @inheritDoc
     */
    protected function asJson($value) {
        $options = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

        if (isset($this->json_pretty_print) && $this->json_pretty_print === true)
            $options |= JSON_PRETTY_PRINT;

        return (empty($value))
            ? null
            : json_encode($value, $options);
    }
}
