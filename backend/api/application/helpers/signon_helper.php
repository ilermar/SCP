<?php

function get_random_string($length = 8) 
{
    return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ%!*#&-$"), 0, $length);
}