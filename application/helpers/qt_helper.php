<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function _d($vars, $exit = FALSE)
{
    print '<pre>';
    var_dump($vars);
    print '</pre>';

    if ($exit) exit();
}

function _l($vars)
{
    $handle = fopen('d:\_download\logs.txt', 'a+');
    fwrite($handle, var_dump_to_string($vars));
    fclose($handle);
}

function var_dump_to_string($var)
{
    $output = "";
    _var_dump_to_string($var, $output);

    return $output;
}

function _var_dump_to_string($var, &$output, $prefix = "")
{
    foreach ($var as $key => $value)
    {
        if (is_array($value))
        {
            $output .= $prefix . $key . " = Array \n";
            _var_dump_to_string($value, $output, "  " . $prefix);
        }
        else
        {
            $output .= $prefix . $key . " => " . $value . "\n";
        }
    }
}

function get_plural($count, $singular, $plural = 's', $irregular = '')
{
    if ($count > 1)
    {
        if (!empty($irregular)) return $irregular;
        return $singular . $plural;
    }

    return $singular;
}

function nf($number, $decimal = 2)
{
    return number_format(round($number, 2), $decimal, ',', ' ');
}

function abz($number)
{
    if (abs(round($number)) == 0) return 0;

    return $number;
}

function alt($target, $value, $alt1, $alt2) {
    return ($value === $target ? $alt1 : $alt2);
}

function sendmail($mailer, $to, $subject, $message, $bcc) {
    $mailer->from('noreply@appbooking.net', 'AppBooking No-reply');
    $mailer->to($to);
    $mailer->bcc($bcc);
    $mailer->subject($subject);
    $mailer->message($message);
    $mailer->send();
}