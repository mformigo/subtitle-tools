<?php

function hamburger_item($name)
{
    return '<a class="my-2 pr-3" href="'.route($name).'">'.__('nav.item.'.$name).'</a>';
}
