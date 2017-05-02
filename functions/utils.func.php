<?php
function secondsToTime($seconds, $contract = true)
{
    if (!empty($seconds)) {
        $dtF = new DateTime("@0");
        $dtT = new DateTime("@$seconds");
        if ($contract) {
            $mins = floor($seconds / 60);
            $hours = floor($mins / 60);
            $days = floor($hours / 24);
            $weeks = floor($days / 7);
            $months = floor($weeks / 4.348125);
            $years = floor($months / 12);

            $seconds -= $mins * 60;
            $mins -= $hours * 60;
            $hours -= $days * 24;
            $days -= $weeks * 7;
            $weeks -= $months * 4.348125;
            $months -= $years * 12;

            /* Text seconds */
            if (round($seconds) > 0) {
                if (round($seconds) > 1) {
                    $tseconds = $seconds . " seconds";
                } else {
                    $tseconds = $seconds . " second";
                }
            } else {
                $tseconds = "";
            }

            /* Text mins */
            if (round($mins) > 0) {
                if (round($mins) > 1) {
                    $tmins = $mins . " mins";
                } else {
                    $tmins = $mins . " min";
                }
            } else {
                $tmins = "";
            }

            /* Text hours */
            if (round($hours) > 0) {
                if (round($hours) > 1) {
                    $thours = $hours . " hours";
                } else {
                    $thours = $hours . " hours";
                }
            } else {
                $thours = "";
            }

            /* Text days */
            if (round($days) > 0) {
                if (round($days) > 1) {
                    $tdays = $days . " days";
                } else {
                    $tdays = $days . " day";
                }
            } else {
                $tdays = "";
            }

            /* Text weeks */
            if (round($weeks) > 0) {
                if (round($weeks) > 1) {
                    $tweeks = round($weeks) . " weeks";
                } else {
                    $tweeks = round($weeks) . " week";
                }
            } else {
                $tweeks = "";
            }

            /* Text months */
            if (round($months) > 0) {
                if (round($months) > 1) {
                    $tmonths = $months . " months";
                } else {
                    $tmonths = $months . " month";
                }
            } else {
                $tmonths = "";
            }

            /* Text years */
            if (round($years) > 0) {
                if (round($years) > 1) {
                    $tyears = $years . " years";
                } else {
                    $tyears = $years . " year";
                }
            } else {
                $tyears = "";
            }

            /*if ($years > 0) {
                return "$tyears, $tmonths and $tweeks";
            } else if ($months > 0) {
                return "$tmonths, $tweeks and $tdays";
            } else if ($weeks > 0) {
                return "$tweeks, $tdays and $thours";
            } else if ($days > 0) {
                return "$tdays and $thours";
            } else if ($hours > 0) {
                return "$thours and $tmins";
            } else if ($mins > 0) {
                return "$tmins and $tseconds";
            } else {
                return $tseconds;
            }
            */

            $output = "";
            $values = 0;

            if (!empty($tyears) and $values < 2) {
                $output.= $tyears;
                $values++;
            }

            if (!empty($tmonths) and $values < 2) {
                if ($values !== 0) {
                    $output.= " and ";
                }
                $output.= $tmonths;
                $values++;
            }

            if (!empty($tweeks) and $values < 2) {
                if ($values !== 0) {
                    $output.= " and ";
                }
                $output.= $tweeks;
                $values++;
            }

            if (!empty($tdays) and $values < 2) {
                if ($values !== 0) {
                    $output.= " and ";
                }
                $output.= $tdays;
                $values++;
            }
            
            if (!empty($thours) and $values < 2) {
                if ($values !== 0) {
                    $output.= " and ";
                }
                $output.= $thours;
                $values++;
            }

            if (!empty($mins) and $values < 2) {
                if ($values !== 0) {
                    $output.= " and ";
                }
                $output.= $tmins;
                $values++;
            }

            if (!empty($tseconds) and $values < 2) {
                if ($values !== 0) {
                    $output.= " and ";
                }
                $output.= $tseconds;
                $values++;
            }

            return $output;

        } else {
            if ($seconds >= 86400) {
                return $dtF->diff($dtT)->format('%ad:%hh:%mm:%ss');
            } elseif ($seconds >= 3600) {
                return $dtF->diff($dtT)->format('%hh:%im:%ss');
            } elseif ($seconds >= 60) {
                return $dtF->diff($dtT)->format('%im:%ss');
            } else {
                return $seconds . " seconds";
            }
        }
    }
    return false;
}

// Shortens a number and attaches K, M, B, etc. accordingly
function number_shorten($number, $precision = 3, $divisors = null)
{

    // Setup default $divisors if not provided
    if (!isset($divisors)) {
        $divisors = array(
            pow(1000, 0) => '', // 1000^0 == 1
            pow(1000, 1) => 'K', // Thousand
            pow(1000, 2) => 'M', // Million
            pow(1000, 3) => 'B', // Billion
            pow(1000, 4) => 'T', // Trillion
            pow(1000, 5) => 'Qa', // Quadrillion
            pow(1000, 6) => 'Qi', // Quintillion
        );
    }

    // Loop through each $divisor and find the
    // lowest amount that matches
    $divisor = 1;
    $shorthand = "";

    foreach ($divisors as $divisor => $shorthand) {
        if (abs($number) < ($divisor * 1000)) {
            // We found a match!
            break;
        }
    }

    // We found our match, or there were no matches.
    // Either way, use the last defined value for $divisor.
    if ($number < 1000)
        $precision = 0;
    return number_format($number / $divisor, $precision) . $shorthand;
}