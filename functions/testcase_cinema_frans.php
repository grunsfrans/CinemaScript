<?php

class FCinema
{

  private $nrOfSeats            = 0;
  private $seats                = [];
  private $availableSeatGroups  = [];
  private $seatsToReserve       = [];
  //private $visitorGroup         = 0;


  public function __construct($nrOfSeats = 20){
    $this->nrOfSeats = $nrOfSeats;
    $this->createSeats();
    $this->determineAvailableSeatGroups();
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

    for ($i = 0; $i < $this->nrOfSeats; $i++) {
    	
      $currentSeat = $this->seats[$i]; 
       
      if ( $currentSeat == 'free' && $firstSeatOfGroup == -1 ) {
        $firstSeatOfGroup = $i;
        if ($i === $this->nrOfSeats -1){
          $this->availableSeatGroups[$firstSeatOfGroup] = 1;
        }
      } 
      elseif ($currentSeat == 'free' && $firstSeatOfGroup != -1 && $i == $this->nrOfSeats -1 )  {
        $seatGroupSize =  $i+1 - $firstSeatOfGroup;
        $this->availableSeatGroups[$firstSeatOfGroup] = $seatGroupSize;
        $firstSeatOfGroup = -1;
      }
      elseif ($currentSeat == 'taken' && $firstSeatOfGroup != -1) {
        $seatGroupSize =  $i - $firstSeatOfGroup;
        $this->availableSeatGroups[$firstSeatOfGroup] = $seatGroupSize;
        $firstSeatOfGroup = -1;
      }
    }
    echo count($this->availableSeatGroups) . " seatgroups \n\n";
    // print_r($this->availableSeatGroups);
  }


  public function getSeatsForVisitors($groupSize) {
    if ( $this->enoughSeatsAvailable($groupSize) ) {
      $time_start = microtime(true);
      $test = $this->reserveSeatsForVisitors($groupSize);
      $time_end = microtime(true); 
      //print_r($test);
      //echo $time_end - $time_start . " seconden voor een groep van size: " . $groupSize . " in een bioscoop met size: " . $this->nrOfSeats;
    }
    else{
      echo 'Not enough seats available for this group';
      die;
    }
  }


  private function reserveSeatsForVisitors($groupSize) {
    //echo "groupsize: " . $groupSize . " recurse  \n\n\n" . print_r($this->availableSeatGroups) ;
    $counter = 0;
    $currentGroupSize = $groupSize;
    while ($currentGroupSize > 0) {
      $group = $this->getBestSeatGroupToPlaceVisitors($groupSize);
      $chosenGroupSize =
      $amount = $groupSize >= $this->availableSeatGroups[$group] ? $this->availableSeatGroups[$group] : $groupSize; 
      $this->seatsToReserve += array_slice($this->seats, $group, $amount, true );
      unset($this->availableSeatGroups[$group]);
      $currentGroupSize -= $amount;
      $counter += 1;
    }
	echo $counter . " whiles\n\n";
   
   
    // RECURSIVE
    // if ($groupSize < 1) {
    //     return $this->seatsToReserve;
    // }
    // else{
    //   $group = $this->getBestSeatGroupToPlaceVisitors($groupSize);
    //   $amount = $groupSize >= $this->availableSeatGroups[$group] ? $this->availableSeatGroups[$group] : $groupSize; 
    //   $this->seatsToReserve += array_slice($this->seats, $group, $amount, true );
    //   unset($this->availableSeatGroups[$group]);
    //   return $this->reserveSeatsForVisitors($groupSize-$amount);
    // }
    
    
  }


  private function getBestSeatGroupToPlaceVisitors($groupSize) {
    $counter = 0;
    $bestGroup = -1;
    foreach ($this->availableSeatGroups as $key => $value) {
      $currentBestGroupSize = $this->availableSeatGroups[$bestGroup];
      if ( $bestGroup === -1 || ( $value > $currentBestGroupSize && $currentBestGroupSize !== $groupSize ) ) { 
        $bestGroup = $key;
        if( $groupSize <= $value) {
        //	echo "break : in if \n\n";
          break;
        }
      }
      elseif ( $groupSize <= $value ) {
        $bestGroup = $key;
        //echo "break : in elseif \n\n";
        break;
      }
      $counter += 1;
    }
    //echo "best group: " . $bestGroup . " with size: " . $this->availableSeatGroups[$bestGroup] . "\n\n";
    //echo $counter . "\n";
    return $bestGroup; 
  }


  private function enoughSeatsAvailable($groupSize){
    $seatUsage = array_count_values($this->seats);
    return ($seatUsage['free'] >= $groupSize ? true : false);
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



