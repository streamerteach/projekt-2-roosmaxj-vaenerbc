<?php

function generatePassword($length = 10)
{
    return substr(str_shuffle(
        "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"
    ), 0, $length);
}
