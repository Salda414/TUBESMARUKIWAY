<?php
 

function rupiah($nominal) {
    return "Rp ".number_format($nominal);
}

function dolar($nominal) {
    return "USD ".number_format($nominal);
}

    function formatIDR($nominal) {
        return "IDR " . number_format($nominal, 0, ',', '.');
    }
