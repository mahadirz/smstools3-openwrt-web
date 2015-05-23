<?php
/**
 * Created by PhpStorm.
 * User: Mahadir
 * Date: 5/20/2015
 * Time: 1:54 PM
 */

function printr_die($var){
    echo "<pre>";
    print_r($var);
    echo "</pre>";
    die();
}

function pre($var){
    echo "<pre>";
    print_r($var);
    echo "</pre>";
}

function vardump_die($var){
    echo "<pre>";
    var_dump($var);
    echo "</pre>";
    die();
}

