<?php

class FCinema
{

  private $nrOfSeats            = 0;
  private $seats                = [];
  private $availableSeatGroups  = [];
  private $seatsToReserve       = [];


  public function __construct($nrOfSeats = 20){
    $this->nrOfSeats = $nrOfSeats;
    $this->createSeats();
    $this->determineAvailableSeatGroups();
    $this->sortAvailableSeatGroupsByDescValue();
    //print_r($this->availableSeatGroups);

    // $this->availableSeatGroups = array_reverse($this->availableSeatGroups, true);
    // reset($this->availableSeatGroups);
    //  do {
    // list($key, $value) = each($this->availableSeatGroups); 
    // echo $key ." " . $value . "\n";
    // prev($this->availableSeatGroups);
    // } while (next($this->availableSeatGroups)!=FALSE);

  }


  private function createSeats(){
    for ($i = 0; $i < $this->nrOfSeats; $i++) {
      if (rand(1, 4) == 1) {
        $this->seats[$i] = 'taken';
        continue;
      }
      $this->seats[$i] = 'free';
    }
  }


  private function determineAvailableSeatGroups() {
    $firstSeatOfGroup = -1 ;
    $seatGroupSize = 0;

    for ($i = 0; $i < $this->nrOfSeats; $i++) {
    	
      $currentSeat = $this->seats[$i]; 
       
      if ( $currentSeat == 'free' && $firstSeatOfGroup == -1 ) {  // Start of group
        $firstSeatOfGroup = $i;
        if ($i === $this->nrOfSeats -1){  //One avaiilable seat at the end of $seats
          $seatGroupSize = 1;
          $this->availableSeatGroups[$firstSeatOfGroup] = $seatGroupSize;
        }
      } 
      elseif ($currentSeat == 'free' && $firstSeatOfGroup != -1 && $i == $this->nrOfSeats -1 )  {   // Seatgroup ends at end of $seats
        $seatGroupSize =  $i+1 - $firstSeatOfGroup;
        $this->availableSeatGroups[$firstSeatOfGroup] = $seatGroupSize;
        $firstSeatOfGroup = -1;
      }
      elseif ($currentSeat == 'taken' && $firstSeatOfGroup != -1) {   // Seatgroup in between taken seats
        $seatGroupSize =  $i - $firstSeatOfGroup;
        $this->availableSeatGroups[$firstSeatOfGroup] = $seatGroupSize;
        $firstSeatOfGroup = -1;
      }
    }
    
    echo count($this->availableSeatGroups) . " seatgroups \n\n";

  }


  private function sortAvailableSeatGroupsByDescValue(){
    arsort($this->availableSeatGroups);
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
    $seatUsage = array_count_values($this->seats);
    return ($seatUsage['free'] >= $groupSize ? true : false);
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
      $this->seatsToReserve += array_slice($this->seats, $group, $amount, true );
      unset($this->availableSeatGroups[$group]);
      return $this->reserveSeatsForVisitors($groupSize-$amount);
    }
    
    
  }


  private function getBestSeatGroupToPlaceVisitors($groupSize) {
    reset($this->availableSeatGroups);
    $firstGroupSize = current($this->availableSeatGroups);
    echo $firstGroupSize . " first group size\n";
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

  



  public function display(){
    foreach ($this->seatsToReserve as $key => $value) {
      $this->seats[$key] = 'new';
    }

    $output = '';
    for ($i = 0; $i < $this->nrOfSeats; $i++) {
      $output .= '<div class="seat ' . $this->seats[$i] . '">'
      . ($i + 0) .
      '</div>';
    }
    return $output;
  }
}



