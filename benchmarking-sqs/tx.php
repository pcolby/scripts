#!/usr/bin/php
<?php // See http://colby.id.au/benchmarking-sqs

require_once('sqs.class.php');
require_once('timer.class.php');

class tx extends sqs {

    function send($numberToSend, $minSize=0, $maxSize=65500) {
        $timer = new timer('send');
        while (count($timer) < $numberToSend) {
            // Generate some random content.
            $size = rand($minSize, $maxSize);
            $data = '';
            while (strlen($data) < $size) $data .= rand(1, 100);

            // Send the message.
            $this->sqs->sendMessage(array(
                'MessageBody' => (string)$timer->start() . " $data",
                'QueueUrl'    => self::queueUrl
            ));
            $timer->stop();

            // Report some stats periodically.
            if (((count($timer) % 100) == 0) || (count($timer) == $numberToSend))
                print $timer;
        }
    }

}

$tx = new tx;
$tx->send(1000);

?>
