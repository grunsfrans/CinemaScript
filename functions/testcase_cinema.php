<?php

class Cinema
{

    private $maxSeats;
    private $seats;
    private $visitors;
    public $chosenSeats = [];
    public $availableSeatsGroups = [];

    public $groupNr = 0;
    public $groups = [];

    public $count = [
        'x' => 0,
        'y' => 0,
        'z' => 0,
        'u' => 0,
        'f' => 0,
        'p' => 0,
        'r'=> 0,
        'fix'=>0
    ];


    public function __construct($maxSeats = 18)
    {
        $this->maxSeats = $maxSeats;
        $this->createSeats();
    }

    function findAvailableSeatGroups()
    {
        $groupSize = 0;
        for ($i = 0; $i < $this->maxSeats; $i++) {
            if ($this->seats[$i] == 'taken') {
                if ($groupSize > 0) {
                    $this->availableSeatsGroups[($i-$groupSize)] = $groupSize;
                }
                $groupSize=0;
            } else {
                $groupSize++;
                if ($groupSize >= $this->visitors) {
                    $groupSize--;
                    $this->putInSeats(($i-$groupSize), $this->visitors);
                    return true;
                }
            }
        }
        $this->availableSeatsGroups[($i-$groupSize)] = $groupSize;
        return false;
    }

    public function sort()
    {
        $temp = $this->availableSeatsGroups;
        uksort($this->availableSeatsGroups, function ($a,$b) use ($temp) {
            if ($temp[$a] === $temp[$b]) {
                return $a - $b;
            }
            return $temp[$b] - $temp[$a];
        });
    }

    function giveSeatNumbers($visitors)
    {
        $this->visitors = $visitors;

        if (!$this->seatsAvailable()) {
            echo 'Past niet';
            return 'NULLIFY';
        }

        if(!$this->findAvailableSeatGroups()) {
            $this->sort();
            $this->findBestPositions();
        };

        return array(
            'Chosen seats' => $this->chosenSeats
        );
    }

    public function createSeats()
    {
        for ($i = 0; $i < $this->maxSeats; $i++) { $this->count['z']++;
            if (rand(1, 4) == 1) {
                $this->seats[$i] = 'taken';
                continue;
            }
            $this->seats[$i] = 'free';
        }
    }

    public function seatsAvailable()
    {
        $seatUsage = array_count_values($this->seats);
        return ($seatUsage['free'] >= $this->visitors ? true : false);
    }

    public function findBestPositions()
    {

        $queue = $this->visitors;
        while (list($key, $value) = each($this->availableSeatsGroups)) {
            $this->putInSeats($key, $value);
            $queue = $queue - $value;
            if ($queue <= 0) {
                break;
            }
        }
    }

    public function putInSeats($start, $amount) {
        for ($i = 0; $i < $amount; $i++) {
            $this->seats[($start+$i)] = 'new'.($this->groupNr < 11 ? $this->groupNr : '');
            array_push($this->chosenSeats, $start+$i);
        }
        $this->groupNr++;
        array_push($this->groups, [$amount, $start]);
    }

    public function display()
    {
        $output = '';
        for ($i = 0; $i < $this->maxSeats; $i++) {
            $output .= '<div class="seat ' . $this->seats[$i] . '">'
                . ($i + 0) .
                '</div>';
        }
        return $output;
    }
}



