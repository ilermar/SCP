<?php


function scp_log_message($type, $supportCode, $message)
{
	log_message($type, 'SC: ' . $supportCode . ' : ' . $message);
}