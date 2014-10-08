<?php

// SIMPLE USE

require_once '../src/class.cryptsy.php';

$cryptsy = new cryptsy();

echo 'Volume for '.$cryptsy->getPrimaryCurrencyCode().' to '.$cryptsy->getSecondaryCurrencyCode().': ' . $cryptsy->getCurrentVolume() . '<br />';
echo 'Lowest Price for '.$cryptsy->getPrimaryCurrencyCode().' to '.$cryptsy->getSecondaryCurrencyCode().': ' . $cryptsy->getLowestPrice() . '<br />';
echo 'Highest Price for '.$cryptsy->getPrimaryCurrencyCode().' to '.$cryptsy->getSecondaryCurrencyCode().': ' . $cryptsy->getHighestPrice() . '<br />';
echo 'Latest Price for '.$cryptsy->getPrimaryCurrencyCode().' to '.$cryptsy->getSecondaryCurrencyCode().': ' . $cryptsy->getLatestPrice() . '<br />';

?>