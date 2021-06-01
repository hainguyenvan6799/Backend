<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Math\BigInteger;
use phpseclib3\Crypt\RSA;

class TestController extends Controller
{

    function KeyGeneration($size)
    {
        $p = null;
        $q = null;
        $check = false;
        while(!$check)
        {
            $p = $this->generatePrime($size);
            $q = $this->generatePrime($size);
            // $p = 17;
            // $q = 11;
            if (!($this->isPrime($p) && $this->isPrime($q))) {
                // raise ValueError('Both numbers must be prime.')
                $check = false;
            } else if ($p == $q) {
                // raise ValueError('p and q cannot be equal')
                $check = false;
            }
            $check = true;
        }
        

        #2)compute n=pq and phi=(p-1)(q-1)
        $n = $p * $q;
        $phi = ($p - 1) * ($q - 1);

        #3) select random integer "e" (1<e<phi) such that gcd(e,phi)=1
        $e = rand(1, $phi);
        $g = $this->gcd($e, $phi);
        while ($g != 1) {
            $e = rand(1, $phi);
            $g = $this->gcd($e, $phi);
        }
        


        #4)Use Extended Euclid's Algorithm to compute another unique integer "d" (1<d<phi) such that e.d≡1(mod phi)
        $d = $this->multiplicativeInverse($e, $phi);
        // dd(($d*$e)%$phi);
        
        #5)Return public and private keys
        #Public key is (e, n) and private key is (d, n)
        // dd($p, $q, ($d*$e) % $phi);
        // print_r([[$n, $e], [$d, $n]]);
        return [[$n, $e], [$d, $n]];
    }

    function generatePrime($keysize)
    {
        while (true) {
            $num = rand(pow(2, ($keysize - 1)), pow(2, ($keysize)));
            if ($this->isPrime($num)) {
                return $num;
            }
        }
    }
    function isPrime($num)
    {
        if ($num < 2){
            return false; # 0, 1, and negative numbers are not prime
        }
            
        $lowPrimes = [2, 3, 5, 7, 11, 13, 17, 19, 23, 29, 31, 37, 41, 43, 47, 53, 59, 61, 67, 71, 73, 79, 83, 89, 
                     97, 101, 103, 107, 109, 113, 127, 131, 137, 139, 149, 151, 157, 163, 167, 173, 179, 181, 191, 
                     193, 197, 199, 211, 223, 227, 229, 233, 239, 241, 251, 257, 263, 269, 271, 277, 281, 283, 293, 
                     307, 311, 313, 317, 331, 337, 347, 349, 353, 359, 367, 373, 379, 383, 389, 397, 401, 409, 419, 
                     421, 431, 433, 439, 443, 449, 457, 461, 463, 467, 479, 487, 491, 499, 503, 509, 521, 523, 541, 
                     547, 557, 563, 569, 571, 577, 587, 593, 599, 601, 607, 613, 617, 619, 631, 641, 643, 647, 653, 
                     659, 661, 673, 677, 683, 691, 701, 709, 719, 727, 733, 739, 743, 751, 757, 761, 769, 773, 787, 
                     797, 809, 811, 821, 823, 827, 829, 839, 853, 857, 859, 863, 877, 881, 883, 887, 907, 911, 919, 
                     929, 937, 941, 947, 953, 967, 971, 977, 983, 991, 997];
        
        if (in_array($num, $lowPrimes)){
            return true;
        }
        foreach($lowPrimes as $prime)
        {
            if($num % $prime == 0)
            {
                return false;
            }
        }
        
        return $this->millerRabin($num);
    }

    public function millerRabin($n, $k =7)
    {
        if ($n < 6){
            return [false, false, true, true, false, true][$n];
        }  
            
        else if( $n && 1 == 0){
            return false;
        }  
        else{
            $s =0;$d = $n - 1;
            while($d && 1 == 0){
                // s, d = s + 1, d >> 1
                $s = $s + 1;
                $d = $d >> 1;
            }
            foreach(array_rand(range(2, min($n - 2, 100)), min($n - 4, $k) ) as $a ){
                $x = pow($a, $d, $n);
                if ($x != 1 && ($x + 1) != $n){
                   foreach(range(1, $s) as $r){
                      $x = pow($x, 2, $n);
                      if ($x == 1)
                         return False; 
                      else if ($x == $n - 1){
                        $a = 0;  
                        break;
                      }
                    } 
                   if ($a)
                   {
                    return False;
                   }
                }
             return true;  
            }
               
        }
          
    }

    function multiplicativeInverse($a, $m)
    {
        // $x = 0;
        // $y = 1;
        // $lx = 1;
        // $ly = 0;
        // $oa = $a;
        // $ob = $b;
        // while ($b != 0) {
        //     $q = floor($a / $b);
        //     [$a, $b] = [$b, $a % $b];
        //     [$x, $lx] = [($lx - ($q * $x)), $x];
        //     [$y, $ly] = [($ly - ($q * $y)), $y];
        // }

        // if ($lx < 0) {
        //     $lx += $ob;
        // }

        // if ($ly < 0) {
        //     $ly += $oa;
        // }

        // return $lx;
        for ($x = 1; $x < $m; $x++)
        if ((($a%$m) * ($x%$m)) % $m == 1)
            return $x;
    }


    function gcd($a, $b)
    {
        while ($b != 0) {
            $temp = $a % $b;
            $a = $b;
            $b = $temp;
        }

        return $a;
    }

    function power($x, $y, $p)
    {

        // Initialize result
        $res = 1;

        // Update x if it is more than or
        // equal to p
        $x = $x % $p;
        while ($y > 0) {

            // If y is odd, multiply
            // x with result
            if ($y & 1)
                $res = ($res * $x) % $p;

            // y must be even now
            $y = $y >> 1; // $y = $y/2
            $x = ($x * $x) % $p;
        }
        return $res;
    }
    function miillerTest($n, $d = 7)
    {

        // Pick a random number in [2..n-2]
        // Corner cases make sure that n > 4
        $a = 2 + rand() % ($n - 4);

        // Compute a^d % n
        $x = $this->power($a, $d, $n);

        if ($x == 1 || $x == $n - 1)
            return true;

        // Keep squaring x while one
        // of the following doesn't
        // happen
        // (i) d does not reach n-1
        // (ii) (x^2) % n is not 1
        // (iii) (x^2) % n is not n-1
        while ($d != $n - 1) {
            $x = ($x * $x) % $n;
            $d *= 2;

            if ($x == 1)     return false;
            if ($x == $n - 1) return true;
        }

        // Return composite
        return false;
    }

    public function encRSA($M, $publicKey)
    {
        [$n, $e] = $publicKey;
        $data[0] = 1;
        for($i = 0; $i < $e; $i++)
        {
            $rest[$i] = pow($M, 1) % $n;
            if($data[$i] > $n)
            {
                $data[$i + 1] = $data[$i] * $rest[$i] % $n;
            }
            else
            {
                $data[$i+1] = $data[$i] * $rest[$i];
            }
        }
        $get = $data[$e] % $n;
        return $get;
    }

    function decRSA($E, $privateKey)
    {
        [$d, $n] = $privateKey;
        $data[0] = 1;
        for($i = 0; $i < $d; $i++)
        {
            $rest[$i] = pow($E, 1) % $n;
            if($data[$i] > $n)
            {
                $data[$i + 1] = $data[$i] * $rest[$i] % $n;
            }
            else
            {
                $data[$i+1] = $data[$i] * $rest[$i];
            }
        }
        $get = $data[$d] % $n;
        return $get;
    }

    public function encrypt($message, $publicKey){
        [$n, $e] = $publicKey;
        $enc = null;
        for($i = 0; $i < strlen($message); $i++)
        {
            $m = ord($message[$i]);
            if($m <= $n)
            {
                $enc = $enc.chr($this->encRSA($m, $publicKey));
            }
            else{
                $enc = $enc.$message[$i];
            }
        }
        return $enc;
    }

    public function decrypt($message, $encryptedString, $privateKey){
        [$d, $n] = $privateKey;
        $dec = null;
        for($i = 0; $i < strlen($message); $i++)
        {
            $m = ord($encryptedString[$i]);
            if($m <= $n)
            {
                $dec = $dec.chr($this->decRSA($m, $privateKey));
            }
            else{
                $dec = $dec.$encryptedString[$i];
            }
        }
        return $dec;
    }

    public function a($plainText, $publicKey){
        #1) obtain (n,e) 
    [$n, $e] = $publicKey;
    #2)message space [0,n-1]
    #3)compute c=m^e
    $plainText = str_split($plainText);
    foreach($plainText as $char)
    {
        $c = pow(ord($char), $e) % $n;
    }
    #4) send "C" to the other party
    return $c;
    }

    public function test()
    {
        [$pub, $pri] = $this->keyGeneration((int)5);
        
        // [$pub, $pri] = [[119, 35], [11, 119]];
        // dd([$pub, $pri]);
        $plainText = "17055221";
        // $encryptedString = $this->encrypt($plainText, $pub);
        // echo $encryptedString . '----';
        // echo '<br/>';
        // $decryptedString = $this->decrypt($plainText, $encryptedString, $pri);
        // echo ($decryptedString);
        dd($this->a($plainText, $pub));

    }
}
