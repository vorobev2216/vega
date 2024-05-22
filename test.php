<?php
include 'MobileDetect.php';
    require_once 'vendor/mobiledetect/mobiledetectlib/MobileDetect.php';    
    $detect = new MobileDetect();
    
    if($detect->isMobile()) {
        echo 'You are using a mobile device.';
    } else {
        echo 'You are not using a mobile device.';
    }
?>