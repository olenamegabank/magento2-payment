<?php

namespace MegaBank\Payment\Model\Config\Backend;

use Magento\Framework\App\Config\Value;
use Magento\Framework\Exception\CronException;

/**
 * Class CronExpr
 * @package MegaBank\Payment\Model\Config\Backend
 */
class CronExpr extends Value
{
    /**
     * @return CronExpr
     * @throws CronException
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        if ($value) {
            $e = preg_split('#\s+#', $value, null, PREG_SPLIT_NO_EMPTY);
            if (count($e) < 5 || count($e) > 6) {
                throw new CronException(__('Invalid cron expression: %1', $value));
            }
            foreach ($e as $expr) {
                $this->matchCronExpression($expr);
            }
        }
        return parent::beforeSave();
    }

    /**
     * Match cron expression.
     *
     * @param string $expr
     * @return bool
     * @throws CronException
     */
    protected function matchCronExpression($expr)
    {
        // handle ALL match
        if ($expr === '*') {
            return true;
        }

        // handle multiple options
        if (strpos($expr, ',') !== false) {
            foreach (explode(',', $expr) as $e) {
                if ($this->matchCronExpression($e)) {
                    return true;
                }
            }
            return false;
        }

        // handle modulus
        if (strpos($expr, '/') !== false) {
            $e = explode('/', $expr);
            if (count($e) !== 2) {
                throw new CronException(__('Invalid cron expression, expecting \'match/modulus\': %1', $expr));
            }
            if (!is_numeric($e[1])) {
                throw new CronException(__('Invalid cron expression, expecting numeric modulus: %1', $expr));
            }
            $expr = $e[0];
        }

        // handle all match by modulus
        if ($expr === '*') {
            $from = 0;
            $to = 60;
        } elseif (strpos($expr, '-') !== false) {
            // handle range
            $e = explode('-', $expr);
            if (count($e) !== 2) {
                throw new CronException(__('Invalid cron expression, expecting \'from-to\' structure: %1', $expr));
            }

            $from = $this->getNumeric($e[0]);
            $to = $this->getNumeric($e[1]);
        } else {
            // handle regular token
            $from = $this->getNumeric($expr);
            $to = $from;
        }

        if ($from === false || $to === false) {
            throw new CronException(__('Invalid cron expression: %1', $expr));
        }
        return true;
    }


    /**
     * @param $value
     * @return false|int|string
     */
    protected function getNumeric($value)
    {
        static $data = [
            'jan' => 1,
            'feb' => 2,
            'mar' => 3,
            'apr' => 4,
            'may' => 5,
            'jun' => 6,
            'jul' => 7,
            'aug' => 8,
            'sep' => 9,
            'oct' => 10,
            'nov' => 11,
            'dec' => 12,
            'sun' => 0,
            'mon' => 1,
            'tue' => 2,
            'wed' => 3,
            'thu' => 4,
            'fri' => 5,
            'sat' => 6,
        ];

        if (is_numeric($value)) {
            return $value;
        }

        if (is_string($value)) {
            $value = strtolower(substr($value, 0, 3));
            if (isset($data[$value])) {
                return $data[$value];
            }
        }

        return false;
    }
}

