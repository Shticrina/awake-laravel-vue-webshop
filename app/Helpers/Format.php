<?php

namespace App\Helpers;

class Format
{
    public static function money($amount)
    {
        return str_replace(' ', '.',str_replace('.', ',', str_replace(',', ' ', number_format($amount, 2))))." €";
    }

    public static function locale_datetime($input_date, $locale = null) {

      	if (!isset($locale))
      		  $locale = app()->getLocale();

    	  $localeString = $locale.'_'.strtoupper($locale);
        $date = new \DateTime($input_date);

        setlocale(LC_ALL, $localeString.'.UTF8', $localeString.'.ISO-8859-1');
        $date = ucfirst(strftime("%a %e %b (%H:%M)", $date->getTimestamp()));

        // check if is_utf8($str) {
      	if (!preg_match('//u', $date))
  			    $date = utf8_encode($date);

        return $date;
    }

    /**
     * Convert a price string so it can be stored as a DECIMAL type in the DB
     * @param  string $string The price to convert
     * @return string         The price in DECIMAL format
     */
    public static function priceForDatabase($string) {
        // Remove invalid characters
        $cleaned = preg_replace("/[^0-9.,]/", "", $string);
        // Replace colons with dots
        $formatted = str_replace(",", ".", $cleaned);
        // Make sur there's only one colon
        if(substr_count($formatted, ".") > 1) throw new \Exception("Invalid price formatting", 1);

        return $formatted;
    }

    /**
     * Convert from DB price format to display format (e.g. 299,00)
     * @param  string $string The DB price to convert
     * @return string         The price formatted for display
     */
    public static function priceForDisplay($string) {
        $exploded = explode(".", $string);

        $parts = count($exploded);
        if($parts < 1 || $parts > 2) throw new \Exception('Invalid format');

        if($parts === 1) return $exploded[0] . ',00';

        if(strlen($exploded[1]) < 2) $exploded[1] = str_pad($exploded[1], 2, "0");

        // Implode the first part and the two first cifers of the second price
        return implode(",", [$exploded[0], substr($exploded[1], 0, 2)]);
    }

    /**
     * Convert from DB database format to Stripe format (integer in cents)
     * @param  string $string The DB price
     * @return int            The Stripe-compatible price
     */
    public static function priceForStripe($string) {
        $exploded = explode(".", $string);

        $parts = count($exploded);
        if($parts < 1 || $parts > 2) throw new \Exception('Invalid format');

        // If the number has no decimal part, MySQL will return it as an integer
        if($parts === 1) return $exploded[0] * 100;

        if(strlen($exploded[1]) < 2) $exploded[1] = str_pad($exploded[1], 2, "0");

        // Implode the first part and the two first cifers of the second price
        $imploded = implode("", [$exploded[0], substr($exploded[1], 0, 2)]);

        return (int) $imploded;
    }
}
