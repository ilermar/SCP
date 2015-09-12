<?php

function scp_encrypt( $value, $crypt )
{
	$v1 = $value; 
	//scp_log_message(LOG_MINFO,'SCP', 'A0: '.$v1 );
	$v1 =  $crypt->encode($v1);
	//scp_log_message(LOG_MINFO,'SCP', 'A1: '.$v1);
	//$v1 = urlencode( $v1);
	//scp_log_message(LOG_MINFO,'SCP', 'A2: '.$v1);
    return $v1;
    
}

function scp_decrypt( $value, $crypt )
{
	$v1 = $value;
	//scp_log_message(LOG_MINFO,'SCP', 'B0: '.$v1 );
	//$v1 = urldecode($v1);
	//scp_log_message(LOG_MINFO,'SCP', 'B1: '.$v1 );
	$v1 = $crypt->decode( $v1 );
	//scp_log_message(LOG_MINFO,'SCP', 'B2: '.$v1 );
    return $v1;
}