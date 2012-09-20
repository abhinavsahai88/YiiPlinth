<?php 
    $lcLoginURL = "/login";
    $lcRegisterURL = "/register";

    if (!Utilities::isCurrentURL($lcLoginURL))
    {
	    echo PlinthHTML::link(
	        Utilities::getString('Sign in'), $lcLoginURL,
                    $this->ajaxLink ? array('dromos-module'=>'ajaxlink/dromos.ajaxlink') : array());
    }

    if (!Utilities::isCurrentURL($lcRegisterURL))
    {
    	echo PlinthHTML::link(
        	Utilities::getString('Register'), $lcRegisterURL,
            $this->ajaxLink ? array('dromos-module'=>'ajaxlink/dromos.ajaxlink') : array());
    }
?>
