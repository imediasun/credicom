<?php
namespace App\modules\CreditRequest\Block\Epost;

class Strrevers {

  public function stristr_revers($haystack, $needle) { 
  $pos = stripos($haystack, $needle) + strlen($needle); 
  return substr($haystack, 0, $pos); 
} 
}
?>
