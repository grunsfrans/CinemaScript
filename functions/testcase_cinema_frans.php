<?php

class FCinema
{

  private $nrOfSeats            = 0;
  private $seats                = "";
  private $takenSeats           = 0;
  private $availableSeatGroups  = [];
  private $seatsToReserve       = [];


  public function __construct($nrOfSeats = 20){
    $this->nrOfSeats = $nrOfSeats;
    $this->createSeats();
    //echo "$this->seats \n";
    //echo intval(substr($this->seats, 2,1)) ."";
    $this->determineAvailableSeatGroups();
    //$this->sortAvailableSeatGroupsByDescValue();
    // print_r($this->availableSeatGroups);

    // $s ="-,-,-,-,-,10,-,5";
    // $number  = strtok($this->seats, ",");
    // echo $number . "\n"; 
    // while ($number != FALSE) {
    //   $number = strtok( ",");
    //   echo $number . "\n"; 
    // }

    // $this->availableSeatGroups = array_reverse($this->availableSeatGroups, true);
    // reset($this->availableSeatGroups);
    //  do {
    // list($key, $value) = each($this->availableSeatGroups); 
    // echo $key ." " . $value . "\n";
    // prev($this->availableSeatGroups);
    // } while (next($this->availableSeatGroups)!=FALSE);

  }


  private function createSeats(){
    $time_start = microtime(true);

    $str = "";
    $counter        = 0;
      
    for ($i = 0; $i < $this->nrOfSeats; $i++) {

      if (rand(1, 4) == 1) {
        $str =  $counter == 0 ? "-," : "{$counter},-,";
        $this->seats = "$this->seats{$str}" ;
        $this->takenSeats++;
        $counter = 0;
        continue;
      }
      $counter++;
    }
    $counter>0 ? $this->seats = ",$this->seats{$counter}" : null;

    $time_end = microtime(true);
    echo "\n\nseat maken : " . ($time_end - $time_start) . " seconden \n\n" ;
  }


  private function determineAvailableSeatGroups() {
    $indexFix= 0;
    $i = 0;
    $skip_i = 0;
    $number= 0;

    $val  = strtok($this->seats, ","); 
    while ($val !== false) {
      
       if ($val != "-"){
        $number = intval($val);
        $skip_i = strlen($val)-1;
        $this->availableSeatGroups[$i+$indexFix] = $number;
        $indexFix += $number - $skip_i -1;
      } 
      $i += $skip_i +1;
      $val = strtok( ",");
    }


    
    $this->sortAvailableSeatGroupsByDescValue();
    
    


    echo count($this->availableSeatGroups) . " seatgroups \n\n";

  }


  private function sortAvailableSeatGroupsByDescValue(){
     ksort($this->availableSeatGroups);
     arsort($this->availableSeatGroups);
    // $temp = $this->availableSeatGroups;
    //     uksort($this->availableSeatGroups, function ($a,$b) use ($temp) {
    //         if ($temp[$a] === $temp[$b]) {
    //             return $a - $b;
    //         }
    //         return $temp[$b] - $temp[$a];
    //     });
  }

//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
//     end of initialization     ^^
//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^




  public function getSeatsForVisitors($groupSize) {
    if ( $this->enoughSeatsAvailable($groupSize) ) {
      echo "\n\nTe plaatsen groepgrootte: " . $groupSize . " in zaal van ".$this->nrOfSeats."\n\n";
      $time_start = microtime(true);
      $test = $this->reserveSeatsForVisitors($groupSize);
      $time_end = microtime(true); 
    }
    else{
      echo 'Not enough seats available for this group';
      die;
    }
  }


  private function enoughSeatsAvailable($groupSize){
    return ( $this->nrOfSeats - $this->takenSeats) >= $groupSize;
  }


  private function reserveSeatsForVisitors($groupSize) { 
    //RECURSIVE
    if ($groupSize < 1) {
      return $this->seatsToReserve;
    }
    else{
      $group = $this->getBestSeatGroupToPlaceVisitors($groupSize);
      $amount = $groupSize >= $this->availableSeatGroups[$group] ? $this->availableSeatGroups[$group] : $groupSize; 
      //echo " place " . $amount ." in group starting at: " . $group . "\n";
      $this->reserveSeats($group, $group + $amount);
      unset($this->availableSeatGroups[$group]);
      return $this->reserveSeatsForVisitors($groupSize-$amount);
    }
    
    
  }


  private function getBestSeatGroupToPlaceVisitors($groupSize) {
    reset($this->availableSeatGroups);
    $firstGroupSize = current($this->availableSeatGroups);
    //echo $firstGroupSize . " first group size\n";
    if ( $groupSize > $firstGroupSize ){
      $group = key($this->availableSeatGroups);
      return $group;
    }


    ksort($this->availableSeatGroups);
    reset($this->availableSeatGroups);
     do {
      list($key, $value) = each($this->availableSeatGroups); 
      if ( $groupSize <= $value){
        return $group = $key;
      }
    prev($this->availableSeatGroups);
    } while (next($this->availableSeatGroups)!=FALSE);
  }


  private function reserveSeats($start, $amount){
    for ($i = $start; $i < $amount; $i++) {
      $this->seatsToReserve[$i] = $i;
     } 
  }
  



  public function display(){
    $output ="";
    $counter =0;

    $indexFix= 0;
    $i = 0;
    $skip_i = 0;

    $val  = strtok($this->seats, ","); 
    while ($val !== false) {
      
     
      if ($val != "-"){
        $number = intval($val);
        $skip_i = strlen($val)-1;
      
        for ($j=0; $j < $number; $j++){
          $class = array_key_exists($counter, $this->seatsToReserve) ? 'new' : 'free';
          $output .= '<div class="seat ' . $class . '">'
                  . ($counter + 0) . '</div>';
          $counter++;
        }
        
        $indexFix += $number - $skip_i -1;
      }
      else{
        $class = 'taken' ;
        $output .= '<div class="seat ' . $class . '">'
                . ($counter + 0) . '</div>';
        $counter++;
      }

      

      $i += $skip_i +1;
      $val = strtok( ",");
    } 
      


    
   
    return $output;
  }

}

