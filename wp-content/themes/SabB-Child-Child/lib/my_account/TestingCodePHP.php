<?php

function numberPairs($n, $ar) {
    sort($ar);
    $number_of_pairs = 0;
    $i = 0;
    $nn = $n - 1;
    while($i < $nn){
        
      if($ar[$i] == $ar[$i + 1]){
          //echo $ar[$i];
          $number_of_pairs += 1; 
            $i = $i + 2;

      }else{
        $i++;
      }
    }
   // return fwrite(STDOUT, $number_of_pairs);
  return $number_of_pairs;
}

$arr = array(1,2,1,2,3,3,4,8,9,5,6,1);
$n = count($arr);
$numbers = numberPairs($n, $arr);
//echo $numbers;



function countingValleys($n, $s) {
    $valleyCounter = 0; $altitude = 0;
    for($i = 0; $i < $n; $i++){
        $ch = $s[$i];
        if($ch == 'U'){
            $altitude++;
            if($altitude == 0){
                $valleyCounter++;
            }
        }else{
            $altitude--;
        }
    }
    return $valleyCounter;

}

$s = 'UDDDUDUU';
$n = 8;

$countingValleys = countingValleys($n, $s);

//echo $countingValleys;


function jumpingOnClouds($c){
   $num_of_jumps = 0;
    $i = 0;        
    while($i < count($c) - 1){
         $last_count = $i + 2;
         if($i + 2 == count($c) || $c[$i + 2] == 1){
            $i++;
            $num_of_jumps++;
        }else{
            $i += 2;
            $num_of_jumps++;
        }
    }
    return $num_of_jumps;
}

$c = array(0,0,1,0,0,1,0);
$jumpingOnClouds = jumpingOnClouds($c);
//echo $jumpingOnClouds;


function repeatedString($str, $x){
    $count = 0;
    $n = 10;
    for($i = 0; $i < strlen($str); $i++){
        if($str[$i] == $x)
        $count++;
    }
    $repetitions = (int)($n / strlen($str));
    $count = $count * $repetitions;    
    for($i = 0; $i <  $n % strlen($str); $i++){
        if($str[$i] == $x){
            $count++;
        }
    }
    return $count;
}

$x = "a";
$str = "abcac";

//echo repeatedString($str, 'a');

function makeAnagram($str1, $str2){
  
 $count1 = array(26);
 $count2 = array(26);

 for($i = 0; $i < strlen($str1); $i++){
    $count1[ord($str1[$i]) - ord('a')]++;
     //echo "<br />";
     //echo ord($str1[$i]) - ord('a');
 }
    
//print_r($count1);
 for($i = 0; $i < strlen($str2); $i++)
    $count2[ord($str2[$i]) - ord('a')]++;
 $result = 0;
 for($i = 0; $i < 26; $i++)
     $result += abs($count1[$i] - $count2[$i]);

 return $result;
}

$str1 = "fcrxzwscanmligyxyvym";
$str2 = "jxwtrhvujlmrpdoqbisbwhmgpmeoke";

$makeAnagram = makeAnagram($str1, $str2);
//echo $makeAnagram;

function alternatingCharacters($s){
    $deletions = 0;
    for($i = 0; $i < strlen($s); $i++){
        if($s[$i] == $s[$i + 1])
           $deletions++;
    }
    return $deletions;
}

$s = "AABAAB";
$ss = "AAAA";
$sss = "BBBBB";
$ssss = "ABABABAB";
$sssss = "BABABA";
$ssssss = "AAABBB";

$alternatingCharacters = alternatingCharacters($s);
$alternatingCharacterss = alternatingCharacters($ss);
$alternatingCharactersss = alternatingCharacters($sss);
$alternatingCharacterssss = alternatingCharacters($ssss);
$alternatingCharactersssss = alternatingCharacters($sssss);
$alternatingCharacterssssss = alternatingCharacters($ssssss);
/*
echo $alternatingCharacters;
echo "<br />";
echo $alternatingCharacterss;
echo "<br />";
echo $alternatingCharactersss;
echo "<br />";
echo $alternatingCharacterssss;
echo "<br />";
echo $alternatingCharactersssss;

echo $alternatingCharacterssssss;

*/

function rotLeft($a, $d){
    $count = count($a);
    $rotated_array = array();
    $i = 0;
    $rotate_index = $d;

    while($rotate_index < $count){
        $rotate_arr[$i] = $a[$rotate_index];
        $i++;
        $rotate_index++;
    }
    $rotate_index = 0;

    while($rotate_index < $d){
        $rotate_arr[$i] = $a[$rotate_index];
        $i++;
        $rotate_index++;
    }

    
     
    return $rotate_arr;

}

$a = array(1,2,3,4,5);
$d = 4;


$rotLeft = rotLeft($a, $d);
echo "<br />";
//print_r($rotLeft);

function hourglassSum($arr){
    $row = count($arr);
    $columns = count($arr[0]);
    $max_hourglass_sum = -63;
//print_r($arr);
    for($i = 0; $i < $row - 2; $i++){
        for($j = 0; $j < $columns - 2; $j++){
            $current_hourglass_sum = $arr[$i][$j] + $arr[$i][$j + 1] + $arr[$i][$j + 2] + $arr[$i + 1][$j + 1] + $arr[$i + 2][$j] + $arr[$i + 2][$j + 1] + $arr[$i + 2][$j + 2];
            $max_hourglass_sum = max($max_hourglass_sum, $current_hourglass_sum);
            $current_hourglass_sum;
            //echo "<br />";
        }
    }
    return $max_hourglass_sum;
}

$arr = array(array(1, 1, 1, 0, 0, 0), array(0, 1, 0, 0, 0, 0), array(1, 1, 1, 0, 0, 0), array(0, 0, 2, 4, 4, 0), array(0, 0, 0, 2, 0, 0), array(0, 0, 1, 2, 4, 0));
/*
1 1 1 0 0 0
0 1 0 0 0 0
1 1 1 0 0 0
0 0 2 4 4 0
0 0 0 2 0 0
0 0 1 2 4 0*/
$hourglassSum = hourglassSum($arr);

//echo $hourglassSum;


function maximumToys($prices, $k){
    $n = count($prices);
    $max_number_of_toys = 0;
    //echo $n;
    sort($prices);
    //print_r($prices);
    for($i = 0; $i < $n; $i++){
        $k -= $prices[$i];
        if($k < 0){
            return $max_number_of_toys;
        }else{
            $max_number_of_toys++;
        }
    }
    
}

$k = 50;
$prices = array(1,12,5,111,200,1000,10);

$maximumToys = maximumToys($prices, $k);
//echo $maximumToys;


function checkMagazine($magezine, $note){
    $mag_arr_explod = explode(" ", $magezine);
    $note_arr_explod = explode(" ", $note);
    $m_count = count($mag_arr_explod);
    $n_count = count($note_arr_explod);
    $j = 0;
    //print_r($mag_arr_explod);
    //echo "<br >";
    //print_r($note_arr_explod);
    for($i = 0; $i < $n_count; $i++){
        if($mag_arr_explod[$i] == $note_arr_explod[$j]){
            $j++;
            $can_I_use = "Yes";            
        }else{
            $can_I_use = "No";            
        }
    }
    return $can_I_use;
}

//$magezine = "give me one hamburger today night";
//$note = "give me one hamburger today";

//$magezine = "two times three is not four";
//$note = "two times two is four";

$magezine = "ive got a lovely bunch of coconuts";
$note = "ive got some coconuts";

$checkMagazine = checkMagazine($magezine, $note);

//echo $checkMagazine;


function twoStrings($s1, $s2){
  $s1_count = strlen($s1);
    $s2_count = strlen($s2);
    $string1_arr = array();
    $string2_arr = array();
  
    //echo $s2_count;
    for($i = 0; $i < $s1_count; $i++){
        //echo $s1[$i];
        $string1_arr[] = $s1[$i];        
    }
    //print_r($string1_arr);
    for($i = 0; $i < $s2_count; $i++){
        $string2_arr[] = $s2[$i];
    }
    //print_r($string2_arr);/**/

    $result = array_intersect($string1_arr, $string2_arr);
    //print_r($result);

    if(empty($result)){
        return "No";
    }else{
        return "Yes";
    }

}

$s1 = "tuuuuuuuuuuu";
$s2 = "jjjiokikkokk";

$twoStrings = twoStrings($s1, $s2);
//echo $twoStrings;



function Fibonacci($n){ 
    

    $num1 = 0; 
    $num2 = 1; 
  
    $counter = 0; 
    while ($counter < 30){ 
       // echo ' '.$num1;   
       if($counter == $n){
            return $num1;
        }
       //echo $counter;
        $num3 = $num2 + $num1; 
        $num1 = $num2; 
        $num2 = $num3; 
        $counter = $counter + 1;
        
      
        
    } 


} 
  
// Driver Code 
$n = 3; 
$Fibonacci = Fibonacci($n); 
//echo $Fibonacci;


function isValid($s) {
      
    $count = strlen($s);
    for($i = 0; $i < $count; $i++){
        //echo $s1[$i];
        $string1_arr[] = $s[$i];        
    }
    if(count($string1_arr) <= 1){
        return "Yes";
    }
        

    $count_arr = count($string1_arr);
    $frequency_ofString = array_count_values($string1_arr);
    
    for($i = 0; $i < $count_arr; $i++){
       //echo $i;
        if($string1_arr[$i] == $string1_arr[$i + 1]){
            unset($string1_arr[$i]);
            break;
        }         
    } 

    for($j = 0; $j < $count_arr; $j++){ 
         //echo $j;
         //echo "<br />";
         //echo $string1_arr[$j];
         if ($string1_arr[$j] == $string1_arr[$j + 1]){
             $can_use = "No";
             break;
         }else{
             $can_use = "Yes";
         }

        
    }
    return $can_use;

    
   
}

//$s = "aaddccddeefghi";
$s = "abccc";
$s = "abcdefghhgfedecba";
$isValid = isValid($s);
//echo $isValid;






$SIZE = 26; 
function printCharWithFreq($str) 
{ 
    global $SIZE; 
      
    // size of the string 'str' 
    $n = strlen($str); 
  
    // 'freq[]' implemented as hash table 
    $freq = array_fill(0, $SIZE, NULL); 
  
    // accumulate freqeuncy of each  
    // character in 'str' 
    for ($i = 0; $i < $n; $i++) 
        $freq[ord($str[$i]) - ord('a')]++; 
  
    // traverse 'str' from left to right 
    for ($i = 0; $i < $n; $i++)  
    { 
  
        // if frequency of character str[i]  
        // is not equal to 0 
        if ($freq[ord($str[$i]) - ord('a')] != 0)  
        { 
  
            // print the character along with  
            // its frequency 
            $str[$i] . $freq[ord($str[$i]) -  
                                  ord('a')] . " "; 
  
            // update frequency of str[i] to 0  
            // so that the same character is  
            // not printed again 
            $freq[ord($str[$i]) - ord('a')] = 0; 
        } 
    }
} 

    // Driver Code 
$str = "geeksforgeeks"; 
//printCharWithFreq($str); 

function substrCount($n, $s){

    $special_count = $n;

    for($i = 0; $i < $n; $i++){
        if($s[$i] == $s[$i + 1]){
            $special_count += 1;
        }

        if($s[$i] != $s[$i + 1] && $s[$i] == $s[$i + 2]){
            $special_count += 1;
        }

    }
    return $special_count;
    
}


$s = "aaaa";
$n = 4;
//{m, n, o, n, o, p, o, o, non, ono, opo, oo}

$substrCount = substrCount($n, $s);

//echo $substrCount;



function commonChild($s1, $s2) {

    $longest_string = 0;
    $s1_arr = array();
    $s2_arr = array();

    /*
    for($i = 0; $i < strlen($s1) - 1; $i++){
        $s1_arr[] = $s1[$i];
    }

    for($i = 0; $i < strlen($s2) - 1; $i++){
        $s2_arr[] = $s2[$i];
    }

    for($j = 0; $j < count($s1_arr) - 1; $j++){

        if($s1_arr[$j] == $s2_arr[$j]){
            $longest_string += 1;
        }else{
            $longest_string = 0;
        }
    }*/

    
    return $longest_string;

}  

$s1 = "HARRY";
$s2 = "SALLY";

$commonChild = commonChild($s1, $s2);

//echo $commonChild;




function lcs($X, $Y, $m, $n) 
{  
    if($m == 0 || $n == 0)  
    return 0;  
    else if ($X[$m - 1] == $Y[$n - 1])  
        return 1 + lcs($X, $Y,  
                       $m - 1, $n - 1);  
    else
        return max(lcs($X, $Y, $m, $n - 1),  
                   lcs($X, $Y, $m - 1, $n));  
} 
  
// Driver Code 
$X = "AGGTAB"; 
$Y = "GXTXAYB"; 
//echo "Length of LCS is "; 
//echo lcs($X , $Y, strlen($X), strlen($Y));  


function lcs2($s1, $s2){ 

                      
                  $m = strlen($s1);                  
                  $n = strlen($s2);

                  for($i = 0; $i <= $m; $i++ ){
                      for($j = 0; $j <= $n; $j++ ){
                          if($i == 0 || $j == 0){
                              $T[$i][$j] = 0;
                          }else if($s1[$i-1]==$s2[$j-1]){
                              $T[$i][$j] = 1+$T[$i-1][$j-1];
                          }else{
                              $T[$i][$j] = max($T[$i][$j-1], $T[$i-1][$j]);
                          }
                      }
                  }
                  
                  return $T[$m][$n];
}


$s1 = "SHINCHAN"; 
$s2 = "NOHARAAA"; 
$lcs2 = lcs2($s1, $s2);

//echo $lcs2;


function minimumSwaps($q) {
    $swap = 0;
    $bribes = 0;
    $pos = 0;
    
    for($i = count($q) - 1; $i >= 0; $i--){
        $j = 0;

        $bribes = $q[$pos] - ($pos+1);
        if($bribes > 2){

            echo "Too chaotic\n";
            return;
        }

        if($q[$i] - 2 > 0){
            $j = $q[$i] - 2;
        }

        while($j <= $i){
            if($q[$j] > $q[$i]){
                $swap++;
            }
            $j++;
        }
        $pos++;
    }
    
    echo $swap."\n";
    
    
    
    

}

$q = array(1,5,3,2,4);
//$q = array(1,2,3,5,4);

//$minimumSwaps = minimumSwaps($q);
//echo $minimumSwaps;


function minimumSwapss($a){
 
    $swap = 0;
    for($i = 0; $i < count($a); $i++){
        if($i + 1 != $a[$i]){
            $t = $i;
            while($a[$t] != $i + 1){
                $t++;
            }
            $temp = $a[$t];
            $a[$t] = $a[$i];
            $a[$i] = $temp;
            $swap++;
        }
    }
    return $swap;
}

$a = array(2, 3, 4, 1, 5);
$minimumSwapss = minimumSwapss($a);
//echo $minimumSwapss;




// A sorting based PHP program to 
// count pairs with difference k 
  
// Standard binary search function 
function binarySearch($arr, $low, 
                      $high, $x) 
{ 
    if ($high >= $low) 
    { 
        $mid = $low + ($high - $low)/2; 
        if ($x == $arr[$mid]) 
            return $mid; 
              
        if ($x > $arr[$mid]) 
            return binarySearch($arr, ($mid + 1),  
                                      $high, $x); 
        else
            return binarySearch($arr, $low, 
                               ($mid -1), $x); 
    } 
    return -1; 
} 
  
/* Returns count of pairs with 
   difference k in arr[] of size n. */
function countPairsWithDiffK($arr, $k) 
{ 
    $count = 0; 
    $i; 
    $n = count($arr); 
      
    // Sort array elements 
    sort($arr);  
  
    // Code to remove duplicates  
    // from arr[] 
      
    // Pick a first element point 
    for ($i = 0; $i < $n - 1; $i++) 
        if (binarySearch($arr, $i + 1, $n - 1,  
                         $arr[$i]) != $k) 
            $count++; 
  
    return $count; 
} 
  
    // Driver Code 
    $arr = array(1, 5, 3, 4, 2); 
              // 1  5  3  4  2
    $k = 2; 
    echo "Count of pairs with given diff is "
         , countPairsWithDiffK($arr, $k); 
           
// This code is contributed by anuj-67. 


function countPairsWithDiffKk($arr, $n, 
                                   $k) 
{ 
    $count = 0; 
      
    // Pick all elements one by one 
    for($i = 0; $i < $n; $i++) 
    {  
          
        // See if there is a pair of 
        // this picked element 
        for($j = $i + 1; $j < $n; $j++) 
            if ($arr[$i] - $arr[$j] == $k or
                $arr[$j] - $arr[$i] == $k) 
                $count++; 
    } 
    return $count; 
} 
  
    // Driver Code 
    $arr = array(1, 5, 3, 4, 2); 
    $n = count($arr); 
    $k = 2; 
   // echo "Count of pairs with given diff is "
      //  , countPairsWithDiffKk($arr, $n, $k); 

/*
javascript
      let result = [];
      let freq = [];
      let map = {};
  
      for (let i = 0; i < queries.length; i++) {
          const [op, x] = queries[i];
          const f = map[x] || 0;
  
          if (op === 1) {
              map[x] = f + 1;
              freq[f] = (freq[f] || 0) - 1;
              freq[f+1] = (freq[f+1] || 0) + 1;
          }
          if (op === 2) {
              map[x] = f - 1;
              freq[f-1] += 1;
              freq[f] -= 1;
          }
          if (op === 3) {
              result.push(freq[x] > 0 ? 1 : 0);
          }
      }
  
      return result;
  


      ICE Cream Shop or parlor
      Java8
      static int[] icecreamParlorA1(int m, int[] arr) {
		int result[] = new int[2];
		Map<Integer, Integer> map = new HashMap<>();
		for (int i = 0; i < arr.length; i++) {
			int x = arr[i];
			int y = m - x;
			Integer j = map.get(y);
			if (j != null) {
				result[0] = j + 1;
				result[1] = i + 1;
				break;
			}
			map.put(x, i);

		}
		return result;
    }
    Python 3
     hs = { }
    for i in range(len(cost)):
        if cost[i] in hs:
            print(hs[cost[i]], i + 1)
            break
        hs[money - cost[i]] = i + 1



Fraudulant activity C# solution
        public static int ActivityNotifications(int[] expenditure, int d)
{
 int notifications = 0;
 var arr = new int[d];
 Array.Copy(expenditure, arr, d);
 Array.Sort(arr);
 for (int i = d; i < expenditure.Length; i++)
 {
   if (expenditure[i] >= arr[d / 2] + arr[(d - 1) / 2])
   {
	   notifications++;
   }
   int index = Array.BinarySearch(arr, expenditure[i - d]);
   Array.Copy(arr, index + 1, arr, index, d - index - 1);
   index = Array.BinarySearch(arr, 0, d - 1, expenditure[i]);
   index = index >= 0 ? index : ~index;
   Array.Copy(arr, index, arr, index+1, d - index - 1);
   arr[index] = expenditure[i];
 }
 return notifications;
}



[Greedy] HackerRank Algorithms in Java ‘Max Min’ solution
https://geehye.github.io/hackerrank-09/
// Complete the maxMin function below.
    static int maxMin(int k, int[] arr) {
        Arrays.sort(arr);
        
        int min = Integer.MAX_VALUE;
        for(int i = 0; i <= arr.length - k; i++) 
            min = Math.min(min, arr[k + i - 1] - arr[i]);
        return min;

    }

Arrays.sort(arr);
        
        int min = Integer.MAX_VALUE;
        for(int i = 0; i <= arr.length - k; i++) 
            min = Math.min(min, arr[k + i - 1] - arr[i]);
        return min;





JAVA solution 
https://www.hackerrank.com/challenges/minimum-time-required/forum
Minimum Time Required
        // Complete the minTime function below.
    static long minTime(long[] machines, long goal) {
        Arrays.sort(machines);
        long max = machines[machines.length - 1];
        long minDays = 0;
        long maxDays = max*goal;
        long result = -1;
        while (minDays < maxDays) {
            long mid = (minDays + maxDays) / 2;
            long unit = 0;
            for (long machine : machines) {
                unit += mid / machine;
            }
            if (unit < goal) {
                minDays = mid+1;
            } else {
                result = mid;
                maxDays = mid;
            }
        }
        return result;

    }



    [Stacks] Balanced Brackets
    Javascript Solution
    https://www.hackerrank.com/challenges/balanced-brackets/forum
    var result = 'YES';
    var stack = [];
    s.split('').forEach(function(val) {
        switch(val) {
            case '{':
                stack.push('}');
                break;
            case '[':
                stack.push(']');
                break;
            case '(':
                stack.push(')');
                break;
            default:
                var test = stack.pop();
                if (val !== test) {
                    result = 'NO';
                }    
        }
    })
    if (stack.length) {
        result = 'NO';
    }
    return result;



Python 3 solution
https://www.hackerrank.com/challenges/largest-rectangle/forum
    # Complete the largestRectangle function below.
def largestRectangle(h):
    x=[]
    for i in range(len(h)):
        l=1
        b=0
        c=h[i]
        while(i-b-1>=0 and h[i-b-1]>=c):
            b+=1
        while(i+l<len(h)and h[i+l]>c):
            l+=1
        l=l+b
        s=l*c
        x.append(s)
    return max(x)






    https://www.hackerrank.com/challenges/min-max-riddle/forum
    [Stacks] Min Max Riddle
    Java 7 solution
    static long[] riddle(long[] arr) {
        // complete this function
         // complete the function
       int n=arr.length;
       Stack<Integer> st=new Stack<>();
       int[] left=new int[n+1];
       int[] right=new int[n+1];
       for(int i=0;i<n;i++){
           left[i]=-1;
           right[i]=n;
       }
       for(int i=0;i<n;i++){
           while(!st.isEmpty() && arr[st.peek()]>=arr[i])
               st.pop();
           
           if(!st.isEmpty())
               left[i]=st.peek();
           
           st.push(i);
       }
       while(!st.isEmpty()){
           st.pop();
       }

       for(int i=n-1;i>=0;i--){
           while(!st.isEmpty() && arr[st.peek()]>=arr[i])
               st.pop();
           
           if(!st.isEmpty())
               right[i]=st.peek();
           
           st.push(i);
       }
        long ans[] = new long[n+1]; 
        for (int i=0; i<=n; i++) {
            ans[i] = 0; 
        }
         for (int i=0; i<n; i++) 
        { 
            int len = right[i] - left[i] - 1; 
            ans[len] = Math.max(ans[len], arr[i]); 
        }
        for (int i=n-1; i>=1; i--) {
            ans[i] = Math.max(ans[i], ans[i+1]);  
        }
       long[] res=new long[n];
        for (int i=1; i<=n; i++) {
            res[i-1]=ans[i];
        }
        return res;

    }




castle on the grid hackerrank solution
https://www.hackerrank.com/challenges/castle-on-the-grid/forum
from collections import deque
def minimumMoves(grid, startX, startY, goalX, goalY):
    visited_nodes = set()
    q = deque()
    q.appendleft((startX, startY, 0))
    neighboring_nodes = [
        (-1, 0),
        (1, 0),
        (0, -1),
        (0, 1),
    ]
    grid_dimension = len(grid)
    while q:
        (current_x, current_y, dist) = q.pop()
        new_dist = dist + 1
        for neighboring_node_diff in neighboring_nodes:
            x = current_x + neighboring_node_diff[0]
            y = current_y + neighboring_node_diff[1]
            while (0 <= x < grid_dimension) and (0 <= y < grid_dimension) and (grid[x][y] != 'X'):
                if (x, y) == (goalX, goalY):
                    return new_dist
                elif (x, y) not in visited_nodes:
                    q.appendleft((x, y, new_dist))
                    visited_nodes.add((x, y))
                x += neighboring_node_diff[0]
                y += neighboring_node_diff[1]



trees swap nodes hackerrank solution
Python3 solution
https://www.hackerrank.com/challenges/swap-nodes-algo/forum

from queue import Queue

class Node():
    def __init__(self, data):
        self.data = data
        self.left = None
        self.right = None


def constree(ind):
    root = Node(1)
    temp = root
    q = Queue()
    q.put(temp)
    for l, r in ind:
        temp = q.get()
        if l != -1:
            v = Node(l)
            temp.left = v
            q.put(temp.left)
        if r != -1:
            v = Node(r)
            temp.right = v
            q.put(temp.right)

    return root


def inord(root):
    if root is None:
        return []

    current = root
    stack = []
    res = []

    while True:
        if current is not None:
            stack.append(current)
            current = current.left
        elif (stack):
            current = stack.pop()
            res.append(current.data)
            current = current.right

        else:
            break
    return res

def swapNodes(indexes, queries):
    #
    # Write your code here.
    #
    root = constree(indexes)
    res = []
    for k in queries:
        temp = root
        h = 1
        q = Queue()
        q.put((temp,1))
        while not q.empty():
            temp, lvl = q.get()
            h  = lvl + 1
            if lvl%k == 0:
                if temp is not None:
                    t = temp.left
                    temp.left = temp.right
                    temp.right = t

            if temp is not None:
                q.put((temp.left, h))
                q.put((temp.right, h))

        r = inord(root)
        res.append(r)

    return res



PHP solution
recursion recursive digit sum hackerrank solution
https://www.hackerrank.com/challenges/recursive-digit-sum/forum/comments/279679
    // Complete the superDigit function below.
function superDigit($n, $k) {

    return bcmod(bcmul($n,$k),'9')?:9;

}






recursion crossword puzzle hackerrank solution
https://www.hackerrank.com/challenges/crossword-puzzle/forum
Python3 solution
import math
import os
import random
import re
import sys

SIZE = 10

def possible(word, crossword)->list:
    n = len(word)
    res = []

    for i in range(SIZE):
        for j in range(SIZE): 
            #[i][j] is initial char for word
            # horizontal check
            goodH = True
            for k in range(n):
                if j+k >= SIZE or crossword[i][j+k] not in ['-', word[k]]:
                    goodH = False
                    break
            if goodH:
                res.append((i,j,True)) #True: horizontal

            # vertical check
            goodV = True
            for k in range(n):
                if i+k >= SIZE or crossword[i+k][j] not in ['-', word[k]]:
                    goodV = False
                    break
            if goodV:
                res.append((i,j,False)) #False: vertical
    
    return res

def fillup(word, pos, crossword):
    i, j, horizontal = pos
    if horizontal:
        for k in range(len(word)):
            crossword[i][j+k] = word[k]
    else:
        for k in range(len(word)):
            crossword[i+k][j] = word[k]
    

def revoke(word, pos, crossword):
    i, j, horizontal = pos
    if horizontal:
        for k in range(len(word)):
            crossword[i][j+k] = '-'
    else:
        for k in range(len(word)):
            crossword[i+k][j] = '-'

def helper(crossword, words):
    if not words:
        return crossword, True

    word = words.pop()
    
    for pos in possible(word, crossword):
        fillup(word, pos, crossword)
        candidate, flag= helper(crossword, words)
        if flag:
            return candidate, True 
        revoke(word, pos, crossword)
    words.append(word)
    return _, False

# Complete the crosswordPuzzle function below.
def crosswordPuzzle(crossword, words):
    wordList= list(words.split(';'))
    newC = [list(row) for row in crossword]
    newC= helper(newC, wordList)[0]
    crossword = [''.join(row) for row in newC]
    return crossword






Java solution

import java.io.*;
import java.math.*;
import java.security.*;
import java.text.*;
import java.util.*;
import java.util.concurrent.*;
import java.util.regex.*;

public class Solution {

    // Complete the stepPerms function below.
    static int stepPerms(int n) {
        int[] array = new int[n];
        if (n == 1) {
            return 1;
        }
        else if(n == 2) {
            return 2;
        }
        else if(n == 3) {
            return 4;
        }
        array[0] = 1;
        array[1] = 2;
        array[2] = 4;
        for (int i = 3; i < n; i++) {
            array[i] = array[i-1] + array[i-2] + array[i-3];
        }
        return array[array.length-1];

    }

    private static final Scanner scanner = new Scanner(System.in);

    public static void main(String[] args) throws IOException {
        BufferedWriter bufferedWriter = new BufferedWriter(new FileWriter(System.getenv("OUTPUT_PATH")));

        int s = scanner.nextInt();
        scanner.skip("(\r\n|[\n\r\u2028\u2029\u0085])?");

        for (int sItr = 0; sItr < s; sItr++) {
            int n = scanner.nextInt();
            scanner.skip("(\r\n|[\n\r\u2028\u2029\u0085])?");

            int res = stepPerms(n);

            bufferedWriter.write(String.valueOf(res));
            bufferedWriter.newLine();
        }

        bufferedWriter.close();

        scanner.close();
    }
}


[Graphs] Find the Nearest Clone
https://www.hackerrank.com/challenges/find-the-nearest-clone/forum
from queue import Queue
def findShortest(graph_nodes, graph_from, graph_to, ids, val):
    g = {i + 1: [] for i in range(graph_nodes)}
    for i in range(len(graph_from)):
        g[graph_from[i]].append(graph_to[i])
        g[graph_to[i]].append(graph_from[i])

    target_nodes = []

    for i in range(len(ids)):
        if ids[i] == val:
            target_nodes.append(i + 1)
    result = -1
    for node in target_nodes:
        w = weight(g, target_nodes, node, result)
        if w >0 and w < result or result == -1:
            result = w
    return result

def weight(g, target_nodes, node, limit=-1):
    visited = set()
    q = Queue()
    q.put((node, 0))
    while not q.empty():
        n, w = q.get()
        if n in visited:
            continue
        if n in target_nodes and n != node:
            return w
        visited.add(n)
        if w == limit:
            return -1
        for next_node in g[n]:
            if next_node not in visited:
                q.put((next_node, w + 1))
    return -1




arrays add values to array ranges hackerrank solution
from collections import Counter
https://www.thepoorcoder.com/hackerrank-array-manipulation-solution/
Python3 solution
def arrayManipulation(n, queries):
    c = Counter()
    for a,b,k in queries:
        c[a]  +=k
        c[b+1]-=k
    arrSum = 0
    maxSum = 0
    for i in sorted(c)[:-1]:
        arrSum+= c[i]
        maxSum = max(maxSum,arrSum)
    return maxSum

n,m = map(int,input().split())
queries = [list(map(int,input().split())) for _ in range(m)]
print(arrayManipulation(n, queries))









Hackerrank solution
https://www.hackerrank.com/challenges/ctci-merge-sort/forum
sorting counting inversions hackerrank solution
import java.io.*;
import java.math.*;
import java.security.*;
import java.text.*;
import java.util.*;
import java.util.concurrent.*;
import java.util.regex.*;

public class Solution {

    // Complete the countInversions function below.
    static long countInversions(int[] arr) {

        return mergeSort(arr, 0, arr.length - 1);

    }

    public static long mergeSort(int[] arr, int start, int end){
        if(start == end)
            return 0;
        int mid = (start + end) / 2;
        long count = 0;
        count += mergeSort(arr, start, mid); //left inversions
        count += mergeSort(arr, mid + 1, end);//right inversions
        count += merge(arr, start, end); // split inversions.
        return count;
    }
    
    public static long merge(int[] arr, int start, int end){
        int mid = (start + end) / 2;
        int[] newArr = new int[end - start + 1];
        int curr = 0;
        int i = start;
        int j = mid + 1;
        long count = 0;
        while(i <= mid && j <=end) {
            if(arr[i] > arr[j]) {
                newArr[curr++] = arr[j++];
                count += mid - i + 1; // Tricky part.
            }
            else
                newArr[curr++] = arr[i++];          
        }
         // Leftover elements here.
        while(i <= mid) {
            newArr[curr++] = arr[i++];    
        }
        
        while(j <= end) {
            newArr[curr++] = arr[j++];
        }
     
        System.arraycopy(newArr, 0, arr, start, end - start + 1); // Usual stuff from merge sort algorithm with arrays.
        return count;
    }

    private static final Scanner scanner = new Scanner(System.in);

    public static void main(String[] args) throws IOException {
        BufferedWriter bufferedWriter = new BufferedWriter(new FileWriter(System.getenv("OUTPUT_PATH")));

        int t = scanner.nextInt();
        scanner.skip("(\r\n|[\n\r\u2028\u2029\u0085])?");

        for (int tItr = 0; tItr < t; tItr++) {
            int n = scanner.nextInt();
            scanner.skip("(\r\n|[\n\r\u2028\u2029\u0085])?");

            int[] arr = new int[n];

            String[] arrItems = scanner.nextLine().split(" ");
            scanner.skip("(\r\n|[\n\r\u2028\u2029\u0085])?");

            for (int i = 0; i < n; i++) {
                int arrItem = Integer.parseInt(arrItems[i]);
                arr[i] = arrItem;
            }

            long result = countInversions(arr);

            bufferedWriter.write(String.valueOf(result));
            bufferedWriter.newLine();
        }

        bufferedWriter.close();

        scanner.close();
    }
}






Python3 solution hackerrank
https://www.hackerrank.com/challenges/poisonous-plants/forum
[Stacks] Poisonous Plants
import math
import os
import random
import re
import sys

# Complete the poisonousPlants function below.
def poisonousPlants(p):
    stack = []
    maxDays = -math.inf

    for plant in p:
        days = 1

        while stack and stack[-1][0] >= plant:
            _, d = stack.pop()
            days = max(days, d + 1)
        
        if not stack:
            days = 0
        
        maxDays = max(maxDays, days)
        stack.append((plant, days))
    
    return maxDays

if __name__ == '__main__':
    fptr = open(os.environ['OUTPUT_PATH'], 'w')

    n = int(input())

    p = list(map(int, input().rstrip().split()))

    result = poisonousPlants(p)

    fptr.write(str(result) + '\n')

    fptr.close()




https://algorithmsandme.com/stacks-stock-span-problem/
Python3 hackerrank solution
https://www.hackerrank.com/challenges/making-candies/forum
search making candies hackerrank solution
https://allhackerranksolutions.blogspot.com/2019/02/making-candies-hacker-rank-solution.html#:~:text=This%20problem%20can%20be%20solved,the%20required%20number%20of%20candies.
#!/bin/python3
import math
import os
import random
import re
import sys

# Complete the minimumPasses function below.
def minimumPasses(m, w, p, n):
    candy = 0
    invest = 0
    spend = sys.maxsize
    while candy < n:
        passes = (p - candy) // (m * w)
        if passes <= 0:
            mw = (candy // p) + m + w
            half = math.ceil(mw / 2)
            if m > w:
                m = max(m, half)
                w = mw - m
            else:
                w = max(w, half)
                m = mw - w
            candy %= p
            passes = 1
        candy += passes * m * w
        invest += passes
        spend = min(spend, invest + math.ceil((n - candy) / (m * w)))
    return min(invest, spend)
if __name__ == '__main__':
    fptr = open(os.environ['OUTPUT_PATH'], 'w')

    mwpn = input().split()

    m = int(mwpn[0])

    w = int(mwpn[1])

    p = int(mwpn[2])

    n = int(mwpn[3])

    result = minimumPasses(m, w, p, n)

    fptr.write(str(result) + '\n')

    fptr.close()











Hackerrank solution
https://github.com/thetechbuilder/algorithms/blob/master/balanced_tree.py
https://www.hackerrank.com/challenges/balanced-forest/forum
    #!/bin/python3

# HACKERRANK - find balanced tree

import math
import os

class TreeNode:
    def __init__(self, value, children):
        self.value = value
        self.children = children
        self.total_sum = None
    
    def __repr__(self):
        return "TreeNode(%s, %s)" % (self.value, self.total_sum)

def build_tree(tree_values, tree_edges):
    tree_nodes = [TreeNode(v, set()) for v in tree_values]
    for node_from, node_to in tree_edges:
        # The tree input is undirected so I am adding both as children and parent
        # I am later cleaning it up while doing DFS over the tree
        tree_nodes[node_from - 1].children.add(tree_nodes[node_to - 1])
        tree_nodes[node_to - 1].children.add(tree_nodes[node_from - 1])
    return tree_nodes[0]

def is_even_number(value):
    return not value & 1 

def populate_tree_sums(root):
    stack = (root, None)
    visited = set()

    while stack:
        selected_node = stack[0]

        if selected_node not in visited:
            visited.add(selected_node)
            for child in selected_node.children:
                #remove non children, this cleans out the "bad" inputs
                #the tree has undirected edges so when we convert it back to a
                #proper tree it's easier to work with...
                child.children.remove(selected_node)
                #populate the stack:
                stack = (child, stack)
        else:
            stack = stack[-1] # pop stack
            #calculate the total sum of the current node before going up the tree
            selected_node.total_sum = sum(
                map(
                    lambda tn: tn.total_sum, 
                    selected_node.children
                )
            ) + selected_node.value

def find_best_balanced_forest(root):
    stack = (root, None)
    #visited - visited nodes
    #visited_sums - sums that are currently visited
    #root complement sums complement sums (total_value - parent) on the way to the 
    #current node, the cardinality of root complement sums is increased when going
    #down the tree and decreased when going up the tree, it is okay to do that
    #because the sums are always unique in the root_complement_sums
    visited, visited_sums, root_complement_sums = set(), set(), set()
    min_result_value = math.inf

    while stack:
        selected_node = stack[0]

        if selected_node not in visited:
            visited.add(selected_node)

            #populate stack with children all at once:
            for child in selected_node.children:
                stack = (child, stack)

            #this is a complement sum: TOTAL - current_sum
            #I need to calculate it while going down the tree so when I go up
            #I will use those values in the root_complement_sums to check for
            #existance
            selected_sum_comp = root.total_sum - selected_node.total_sum
            root_complement_sums.add(selected_sum_comp)

            # Yes, no bitwise shifts, I present what I want to get accomplished,
            # but I don't care how it is accomplished
            # selected_node.total_sum * 3 >= root.total_sum is checking that
            # that if the cut is made in selected subtree and the visited subtree 
            # (in case the comp or sum exists in the visited sums)
            # the remaining subtree sum is equal or less than the sums 
            # (which are equal) of the current and the visited subtrees
            # this is just part of the requirement - I can balance the remaining
            # tree only with 0 or positive elements
            if (
                    (selected_node.total_sum * 2) in visited_sums or 
                    (root.total_sum - selected_node.total_sum * 2) in visited_sums
                ) and selected_node.total_sum * 3 >= root.total_sum:

                #get the candidate value and update min_result_value if it's less
                candidate_value = selected_node.total_sum * 3 - root.total_sum
                if candidate_value < min_result_value:
                    min_result_value = candidate_value
        else:
            # This is a case where two even halfs are found.
            if (selected_node.total_sum * 2) == root.total_sum:
                candidate_value = selected_node.total_sum
                # In this case a balanced forest is these two halfs + a new node as
                # a separate tree with the same value as the half of the existing 
                # tree sum
                if candidate_value < min_result_value:
                    min_result_value = candidate_value

            # check visited sums and root complements
            # root complements are the sums on the way from root to the selected
            # nodes taken from it's parents of if we have a tree
            #          (1)
            #        / |  \
            #      /   |   \
            #     /    |    \
            #   (2)   (3)   (4)
            #   / \    |    /\
            # (5)(6) (7)  (8)(9)
            # 
            # 
            # If I am at the node 8, I have the {TOTAL - (8).sum, TOTAL - (4).sum }
            # If I am at the node 9, I have the {TOTAL - (9).sum, TOTAL - (4).sum }
            # If I am at the node 2, I have the {TOTAL - (2).sum }
            if (
                    (
                        selected_node.total_sum in visited_sums or
                        selected_node.total_sum in root_complement_sums
                    ) and selected_node.total_sum * 3 >= root.total_sum
               ):
                # candidate split:
                candidate_value = selected_node.total_sum * 3 - root.total_sum
                if candidate_value < min_result_value:
                    min_result_value = candidate_value
            
            selected_sum_comp = root.total_sum - selected_node.total_sum
            if is_even_number(selected_sum_comp):
                #I am not trying to impress anyone with bitwise shifts here:
                selected_sum_comp_half = selected_sum_comp // 2
                if selected_sum_comp_half > selected_node.total_sum and (
                        selected_sum_comp_half in visited_sums or
                        selected_sum_comp_half in root_complement_sums
                    ):
                    #same candidate value
                    candidate_value = selected_sum_comp_half - selected_node.total_sum 
                    if candidate_value < min_result_value:
                        min_result_value = candidate_value

            #remove selected complement from root while going up the tree
            root_complement_sums.remove(selected_sum_comp)
            #added to the visited sums while going up the tree
            visited_sums.add(selected_node.total_sum)

            #stack pop:
            stack = stack[-1]

    if min_result_value == math.inf:
        min_result_value = -1
    return min_result_value

# Complete the balancedForest function below.
def balanced_forest(tree_values, tree_edges):
    root = build_tree(tree_values, tree_edges)
    populate_tree_sums(root)
    return find_best_balanced_forest(root)
    
    
    
if __name__ == '__main__':
    print("Expected 0:")
    print(
        balanced_forest(
            (
                1, 3, 4, 4,
            ),
            (
                (1, 2), (1, 3), (1, 4),
            )
        )
    )
    
    print("Expected -1:")
    print(
        balanced_forest(
            (7, 7, 21, 3, 1, 2), 
            ((1, 2), (3, 1), (2, 4), (5, 2), (2, 6))
        )
    )
    
    print("Expected 2:")
    print(
        balanced_forest(
            (1, 2, 2, 1, 1), 
            ((1, 2), (1, 3), (3, 5), (1, 4))
        )
    )
    
    
    print("Expected 19:")
    print(
        balanced_forest(
            (15, 12, 8, 14, 13), 
            ((1, 2), (1, 3), (1, 4), (4, 5))
        )
    )
    
    
    print("Expected 13:")
    print(
        balanced_forest(
            (12, 7, 11, 17, 20, 10), 
            ((1, 2), (2, 3), (4, 5), (6, 5), (1, 4))
        )
    )
    
    print("Expected -1:")
    print(
        balanced_forest(
            (7, 7, 4, 1, 1, 1), 
            ((1, 2), (3, 1), (2, 4), (2, 5), (2, 6))
        )
    )
    
    print("Expected 10:")
    print(
        balanced_forest(
            (1, 1, 1, 18, 10, 11, 5, 6), 
            ((1, 2), (1, 4), (2, 3), (1, 8), (8, 7), (7, 6), (5, 7))
        )
    )
    
    
    print("Expected 19:")
    print(
        balanced_forest(
            (15, 12, 8, 14, 13), 
            ((1, 2), (1, 3), (1, 4), (4, 5))
        )
    )






    arrays count matching pairs of numbers hackerrank solution


*/




function binarySearch($array, $searchFor) {
	$low = 0;
	$high = count($array) - 1;
	$mid = 0;

	while ($low <= $high) { // While the high pointer is greater or equal to the low pointer
		$mid = floor(($low + $high) / 2);
		$element = $array[$mid];

		if ($searchFor == $element) { // If this is the value we're searching for
			return $mid;
		} else if ($searchFor < $element) {
			$high = $mid - 1;
		} else {
			$low = $mid + 1;
		}
	}
	   return -1;
    }

    echo binarySearch($ar,$v);




    function insertionSort($ar) {
        $e = end($ar);
        $n = count($ar);
    
        for($i= $n-1; $i >= 0; $i--){
           if($ar[$i-1] > $e){
               $ar[$i] = $ar[$i -1];
           }else{
               $ar[$i] = $e;
               echo implode(' ',$ar)."\n";
               break;
           }
           echo implode(' ',$ar)."\n";
    
        }
    }



    function insertionSort(&$arr){
        for($i=0;$i<count($arr);$i++){
     
           $val = $arr[$i];
           $j = $i-1;
           // Verify if every element is targeter in this loop 
           while($j>=0 && $arr[$j] > $val){
     
              $arr[$j+1] = $arr[$j];
              $j--;
           }
     
     
           $arr[$j+1] = $val;
        }
     }
     


?>