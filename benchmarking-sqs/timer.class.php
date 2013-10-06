<?php // See http://colby.id.au/benchmarking-sqs

class timer implements Countable {

    protected $count = 0;
    protected $distribution;
    protected $distributionPrecision = 0;
    protected $startTime;
    protected $totalTime;
    protected $name;

    public function __construct($name, $distributionPrecision = 2) {
        $this->name = $name;
        $this->distribution = array();
        $this->distributionPrecision = $distributionPrecision;
        $this->start();
    }

    public function count() {
        return $this->count;
    }

    public function start($time = null) {
        return $this->startTime = ($time === null) ? microtime(true) : $time;
    }

    public function stop($count = 1, $time = null) {
        $interval = (($time === null) ? microtime(true) : $time) - $this->startTime;
        $this->totalTime += $interval;
        $key = (string)round($interval, $this->distributionPrecision);
        if  (!array_key_exists($key, $this->distribution)) $this->distribution[$key] = 0;
        $this->distribution[$key]++;
        $this->count += $count;
        return $interval;
    }

    public function __toString() {
        ksort($this->distribution);
        return "{$this->name}: " . print_r($this->distribution, true) .
               "{$this->name}: {$this->count}m " . round($this->totalTime,2) . 's ' .
               round($this->count/$this->totalTime,1) . "m/s\n";
    }

}


?>
