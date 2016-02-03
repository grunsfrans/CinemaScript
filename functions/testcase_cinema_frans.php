<?php

class FCinema
{

  private $nrOfSeats            = 0;
  private $takenSeats           = 0;
  private $availableSeatGroups  = [];
  private $takenSeatGroups      = [];
  private $seatsToReserve       = [];


  public function __construct($nrOfSeats = 20){
    $this->nrOfSeats = $nrOfSeats;
    $this->createSeatsAndDetermineAvailableGroups();
    $this->sortAvailableSeatGroupsByDescValue();
 
    //print_r($this->availableSeatGroups);


  }


  private function createSeatsAndDetermineAvailableGroups(){
    
    $counter        = 0;

    for ($i = 0; $i < $this->nrOfSeats; $i++) {

      if (rand(1, 4) == 1) {
        if ($counter > 0 ){
          $this->availableSeatGroups[$i-$counter] = $counter;
        } 
        $this->takenSeats++;
        $counter = 0;
        continue;
      }
      $counter++;
    }
    if ($counter > 0 ){
      $this->availableSeatGroups[$i-$counter] = $counter;
    } 

    
    echo count($this->availableSeatGroups) . " seatgroups \n\n";
  }


 
  private function sortAvailableSeatGroupsByDescValue(){
    $temp = $this->availableSeatGroups;
        uksort($this->availableSeatGroups, function ($a,$b) use ($temp) {
            if ($temp[$a] === $temp[$b]) {
                return $a - $b;
            }
            return $temp[$b] - $temp[$a];
        });
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
      $this->takenSeatGroups[$group] = $amount;
      unset($this->availableSeatGroups[$group]);
      return $this->reserveSeatsForVisitors($groupSize-$amount);
    }
    
    
  }


  private function getBestSeatGroupToPlaceVisitors($groupSize) {
    reset($this->availableSeatGroups);
    $firstGroupSize = current($this->availableSeatGroups);
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
    $free = 0;

    for ($i=0; $i < $this->nrOfSeats ; $i++) { 
      if ($free >0) {
        $class = array_key_exists($i, $this->seatsToReserve) ? 'new' : 'free';
        $output .= '<div class="seat ' . $class . '">'
                  . ($i + 0) . '</div>';
        $free-- ;
      }

      elseif(array_key_exists($i, $this->availableSeatGroups)){
        $free = $this->availableSeatGroups[$i];
        $class = array_key_exists($i, $this->seatsToReserve) ? 'new' : 'free';
        
        $output .= '<div class="seat ' . $class . '">'
                  . ($i + 0) . '</div>';

        $free-- ;

      }
      
      elseif(array_key_exists($i, $this->takenSeatGroups)){
        $free = $this->takenSeatGroups[$i];
        $class = array_key_exists($i, $this->seatsToReserve) ? 'new' : 'free';
        
        $output .= '<div class="seat ' . $class . '">'
                  . ($i + 0) . '</div>';

        $free-- ;

      }
      else{
        $output .= '<div class="seat taken">'
                  . ($i + 0) . '</div>';
      }  
    }

    return $output;
  }

}

