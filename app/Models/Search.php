<?php

namespace App\Models;

trait Search
{
    private function buildWildCards($term) {
        if ($term == "") {
            return $term;
        }

        // Strip MySQL reserved symbols
        $reservedSymbols = ['-', '+', '<', '>', '@', '(', ')', '~'];
        $term = str_replace($reservedSymbols, '', $term);

        $words = explode(' ', $term);
        foreach($words as $idx => $word) {
            // Add operators so we can leverage the boolean mode of
            // fulltext indices.
            $words[$idx] = "+" . $word . "*";
        }
        $term = implode(' ', $words);
        return $term;
    }

   
}