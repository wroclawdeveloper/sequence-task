<?php

namespace App\Service;

class SequenceHelper
{
    public function getMaxSeguence($n)
    {
        for($i = 1; $i <= $n; $i++)
        {
            $number[$i] = $this->getSeguence($i);
        }

        if ($n==0) return 0;
        for($i=0, $max=0; $i<=$n; $i++) {
            if (($sec = $this->getSeguence($i)) > $max) {
                $max = $sec;
            }
        }
        return $max;
    }

    public function getSeguence($n)
    {
        if ($n == 0)
            return 0;
        else if ($n == 1)
            return 1;
        else if( $n % 2 == 0 )
            return $this->getSeguence($n/2);
        else
            return ($this->getSeguence(($n-1)/2) + $this->getSeguence(($n-1)/2 + 1));
    }
}
