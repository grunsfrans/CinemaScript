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
    $seatGroupSize = 0;

    for ($i = 0; $i < $this->nrOfSeats; $i++) {
    	
      $currentSeat = $this->seats[$i]; 
       
      if ( $currentSeat == 'free' && $firstSeatOfGroup == -1 ) {
        $firstSeatOfGroup = $i;
        if ($i === $this->nrOfSeats -1){
          $seatGroupSize = 1;
          $this->availableSeatGroups[$firstSeatOfGroup] = $seatGroupSize;
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
    arsort($this->availableSeatGroups);
    echo count($this->availableSeatGroups) . " seatgroups \n\n";

  }




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


  private function reserveSeatsForVisitors($groupSize) {
   
   
    //RECURSIVE
    if ($groupSize < 1) {
        return $this->seatsToReserve;
    }
    else{
      $group = $this->getBestSeatGroupToPlaceVisitors($groupSize);
      $amount = $groupSize >= $this->availableSeatGroups[$group] ? $this->availableSeatGroups[$group] : $groupSize; 
       //echo " place " . $amount ." in group: " . $group . "\n";
      $this->seatsToReserve += array_slice($this->seats, $group, $amount, true );
      unset($this->availableSeatGroups[$group]);
      return $this->reserveSeatsForVisitors($groupSize-$amount);
    }
    
    
  }


  private function getBestSeatGroupToPlaceVisitors($groupSize) {
    $firstGroupSize = array_values($this->availableSeatGroups)[0];
    if ( $groupSize >= $firstGroupSize ){
      reset($this->availableSeatGroups);
      $group = key($this->availableSeatGroups);
      return $group;
    }

    $group=$this->nrOfSeats;
    foreach ($this->availableSeatGroups as $key => $value) {
      if($key < $group && $groupSize <= $value){
        $group = $key;
      }
    }
    return $group;

  }

  // private function getBestSeatGroupToPlaceVisitors($groupSize) {
  //   $counter = 0;
  //   $bestGroup = -1;
  //   foreach ($this->availableSeatGroups as $key => $value) {
  //     $currentBestGroupSize = $bestGroup == -1 ? 0 :  $this->availableSeatGroups[$bestGroup];
  //     if ( $value > $currentBestGroupSize && $currentBestGroupSize !== $groupSize  ) { 
  //       $bestGroup = $key;
  //       if( $groupSize <= $value) {
  //       //	echo "break : in if \n\n";
  //         break;
  //       }
  //     }
  //     elseif ( $groupSize <= $value ) {
  //       $bestGroup = $key;
  //       //echo "break : in elseif \n\n";
  //       break;
  //     }
  //     $counter += 1;
  //   }
  //   //echo "best group: " . $bestGroup . " with size: " . $this->availableSeatGroups[$bestGroup] . "\n\n";
  //   //echo $counter . "\n";
  //   return $bestGroup; 
  // }


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



