<?php

namespace App\modules\Sigma\Model\Service;

use \App\SigmaOrder;
use \App\CreditOrder;


class SigmaCheckSearch
{
    public function findCreditOrder($credit_order_id)
    {
        $credit_order = CreditOrder::find($credit_order_id);
        return $credit_order;
    }
}


class SigmaCheck
{

    public static function process($credit_order_id)
    {
        $sigma_result = self::checkSigmaCriteria($credit_order_id);
        if ($sigma_result['result']) {
            SigmaOrder::create(['credit_order_id' => $credit_order_id]);
        }
        unset($sigma_result);
        $sigma_result = self::checkSigmaCriteria($credit_order_id, 1);
        if ($sigma_result['result']) {
            SigmaOrder::create(['credit_order_id' => $credit_order_id, 'credit_order_type' => 1]);
        }

    }


    public static function checkSigmaCriteria($credit_order_id, $type = 0)
    {
        $answer = [
            'result' => false,
            'problem_mismatch' => false,
        ];

        try {

            $es_countries = ['AT', 'BE', 'GB', 'HU', 'GR', 'IT', 'ES', 'DK', 'IE', 'LT', 'LV', 'CY', 'MT', 'NL', 'LU', 'SI', 'SK', 'PL', 'FI', 'FR', 'PT', 'HR', 'SE', 'CZ', 'EE'];
            $sigma_search = new SigmaCheckSearch();
            $credit_order = new CreditOrder();


            $credit_order = $sigma_search->findCreditOrder($credit_order_id);

            //Check exist order
            if (null == $credit_order) return $answer;


            //Check mismatch applicant|coapplicant
            if ($type != 0 && $credit_order->masteller != 1) return $answer;

            if ($type == 0) {
                $data_credit_order = [
                    'famstand' => $credit_order->famstand,
                    'anstellung_als' => $credit_order->anstellung_als,
                    'age' => self::calculate_difference($credit_order->gebdat),
                    'work_experience' => self::calculate_difference($credit_order->anstellung),
                    'staat' => $credit_order->staat,
                    'beruf' => $credit_order->beruf,
                    'netto' => $credit_order->netto,
                    'kinder' => $credit_order->kinder,
                ];
            } else {
                $kinder = $credit_order->gesamtbetrachtung == 1 ? $credit_order->kinder : 0;
                $data_credit_order = [
                    'famstand' => $credit_order->famstand1,
                    'anstellung_als' => $credit_order->anstellung_als1,
                    'age' => self::calculate_difference($credit_order->gebdat1),
                    'work_experience' => self::calculate_difference($credit_order->anstellung1),
                    'staat' => $credit_order->staat1,
                    'beruf' => $credit_order->beruf1,
                    'netto' => $credit_order->netto1,
                    'kinder' => $kinder,
                ];
            }

            //Check mismatch problem
            if ($data_credit_order['famstand'] == 1 || $data_credit_order['famstand'] == 3 || $data_credit_order['famstand'] == 4 || $data_credit_order['famstand'] == 6) {
                if ($data_credit_order['anstellung_als'] != 1 && $data_credit_order['anstellung_als'] != 2) {
                    $answer['problem_mismatch'] = true;
                }
            } else if ($data_credit_order['famstand'] == 2 || $data_credit_order['famstand'] == 5) {
                if ($data_credit_order['anstellung_als'] != 3 && $data_credit_order['anstellung_als'] != 4 && $data_credit_order['anstellung_als'] != 5) {
                    $answer['problem_mismatch'] = true;
                }
            }

            //Check age criterion
            if ($data_credit_order['age'] > 62 || $data_credit_order['age'] < 18) return $answer;

            //Check work experience	- preliminary check
            if ($data_credit_order['work_experience'] < 1) return $answer;

            if ($data_credit_order['staat'] != 'DE' && !in_array($data_credit_order['staat'], $es_countries)) return false;

            if (in_array($data_credit_order['staat'], $es_countries)) {
                if ($data_credit_order['age'] > 50 || $data_credit_order['age'] < 25) return $answer;
                if ($data_credit_order['work_experience'] < 5) return $answer;
            }

            //Check work status
            if ($data_credit_order['beruf'] != 29 && $data_credit_order['beruf'] != 31 && $data_credit_order['beruf'] != 6) return $answer;

            //Check wage - preliminary check
            if ($data_credit_order['netto'] < 1210) return $answer;

            //Check children count
            if ($data_credit_order['kinder'] > 3) return false;

            //Check marital status
            //if ($data_credit_order['famstand'] != 1 && $data_credit_order['famstand'] != 2) return false;

            $answer += [
                'result3500' => self::check3500($data_credit_order),
                'result5000' => self::check5000($data_credit_order),
                'result7500' => self::check7500($data_credit_order),
            ];

            if ($answer['result3500'] || $answer['result5000'] || $answer['result7500']) $answer['result'] = true;

        } catch (Exception $e) {


        }


        return $answer;
    }


    private static function check3500($data_credit_order)
    {

        if ($data_credit_order['work_experience'] < 1) return false;

        $children_count = (int)$data_credit_order['kinder'];
        $ch0 = 1210;
        $ch1 = 1680;
        $ch2 = 1950;
        $ch3 = 2260;

        switch ($children_count) {
            case 0:
                $cw = $ch0;
                $nw = $ch1;
                break;
            case 1:
                $cw = $ch1;
                $nw = $ch2;
                break;
            case 2:
                $cw = $ch2;
                $nw = $ch3;
                break;
            case 3:
                $cw = $ch3;
                $nw = 0;
                break;
            default:
                return false;
        }
        return self::getResult($data_credit_order, $cw, $nw);
    }


    private static function check5000($data_credit_order)
    {
        //Check work experience
        if ($data_credit_order['work_experience'] < 3) return false;

        $children_count = (int)$data_credit_order['kinder'];
        $ch0 = 1660;
        $ch1 = 1980;
        $ch2 = 2350;
        $ch3 = 2710;

        switch ($children_count) {
            case 0:
                $cw = $ch0;
                $nw = $ch1;
                break;
            case 1:
                $cw = $ch1;
                $nw = $ch2;
                break;
            case 2:
                $cw = $ch2;
                $nw = $ch3;
                break;
            case 3:
                $cw = $ch3;
                $nw = 0;
                break;
            default:
                return false;
        }
        return self::getResult($data_credit_order, $cw, $nw);
    }


    private static function check7500($data_credit_order)
    {
        if ($data_credit_order['staat'] != 'DE') return false;

        //Check work experience
        if ($data_credit_order['work_experience'] < 4) return false;

        $children_count = (int)$data_credit_order['kinder'];
        $ch0 = 1860;
        $ch1 = 2180;
        $ch2 = 2600;
        $ch3 = 3010;

        switch ($children_count) {
            case 0:
                $cw = $ch0;
                $nw = $ch1;
                break;
            case 1:
                $cw = $ch1;
                $nw = $ch2;
                break;
            case 2:
                $cw = $ch2;
                $nw = $ch3;
                break;
            case 3:
                $cw = $ch3;
                $nw = 0;
                break;
            default:
                return false;
        }
        return self::getResult($data_credit_order, $cw, $nw);
    }

    private static function calculate_difference($date)
    {
        $date_timestamp = strtotime($date);
        $difference = date('Y') - date('Y', $date_timestamp);
        if (date('md', $date_timestamp) > date('md')) {
            $difference--;
        }
        return $difference;
    }


    private static function getResult($data_credit_order, $cw, $nw)
    {
        $marital_status = 0;
        if ($data_credit_order['famstand'] == 1 || $data_credit_order['famstand'] == 3 || $data_credit_order['famstand'] == 4 || $data_credit_order['famstand'] == 6) {
            $marital_status = 1;
        } else if ($data_credit_order['famstand'] == 2 || $data_credit_order['famstand'] == 5) {
            $marital_status = 2;
        }

        if ($data_credit_order['netto'] >= $cw) { 
            if ($marital_status == 1) {
                return true;
            } else if ($marital_status == 2) {
                if ($data_credit_order['anstellung_als'] == 4 || $data_credit_order['anstellung_als'] == 5) {
                    return true;
                } else if ($data_credit_order['anstellung_als'] == 3) {
                    if ($nw > 0) {
                        if ($data_credit_order['netto'] >= $nw) {
                            return true;
                        } else {
                            return false;
                        }
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}