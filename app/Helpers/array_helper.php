<?php

function has_dupes($array)
{
    return count($array) !== count(array_unique($array));
}
