<?php
namespace Nekonomokochan\PhpJsonLogger;

use Monolog\Formatter\JsonFormatter as BaseJsonFormatter;

/**
 * Class JsonFormatter
 *
 * @package Nekonomokochan\PhpJsonLogger
 */
class JsonFormatter extends BaseJsonFormatter
{
    /**
     * @param array $record
     * @return array|mixed|string
     */
    public function format(array $record)
    {
        $formattedRecord = [
            'message'           => $record['message'],
            'trace_id'          => $record['message'],
            'context'           => $record['context'],
            'level_name'        => $record['level_name'],
            'remote_ip_address' => $this->extractIp(),
            'datetime'          => $record['datetime']->format('Y-m-d H:i:s'),
        ];

        if (empty($record['extra']) === false) {
            $formattedRecord['extra'] = $record['extra'];
        }

        if (isset($record['context']['errors'])) {
            $formattedRecord['errors'] = $record['context']['errors'];
            unset($formattedRecord['context']['errors']);
        }

        $json = $this->toJson($this->normalize($formattedRecord), true) . ($this->appendNewline ? "\n" : '');

        return $json;
    }

    /**
     * @return string
     */
    private function extractIp()
    {
        if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];

            $ipList = explode(',', $ip);

            return $ipList[0];
        }

        if (array_key_exists('REMOTE_ADDR', $_SERVER)) {
            return $_SERVER['REMOTE_ADDR'];
        }

        return '';
    }
}