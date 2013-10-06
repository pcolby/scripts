#!/usr/bin/php
<?php // See http://colby.id.au/benchmarking-sqs

require_once('sqs.class.php');
require_once('timer.class.php');

class rx extends sqs {

    function receive($numberToReceive = PHP_INT_MAX, $batchSize = null) {
        $batchSize = min(10, ($batchSize === null) ? $numberToReceive : $batchSize);
        $receiveArgs = array(
           'QueueUrl'            => self::queueUrl,
           'MaxNumberOfMessages' => $batchSize, // Optional; must be between 1 and 10
           'VisibilityTimeout'   => 30,         // Optional; must be between 0 and 43200.
           'WaitTimeSeconds'     => 20          // Optional; must be between 0 and 20.
        );

        $receiveTimer = new timer('receive');
        $deleteTimer = new timer('delete');
        $delayTimer = new timer('delay');
        $count = 0;
        while (count($delayTimer) < $numberToReceive) {
            $receiveTimer->start();
            $response = $this->sqs->receiveMessage($receiveArgs);
            $count = count($messages = $response['Messages']);
            if ($count >= 0) $receiveTimer->stop($count);
            else error_log('nothing received...');

            $receivedTime = microtime(true);
            foreach ($messages as $message) {
                $deleteTimer->start();
                $this->sqs->deleteMessage(array(
                    'ReceiptHandle' => $message['ReceiptHandle'],
                    'QueueUrl'      => self::queueUrl
                ));
                $deleteTimer->stop();

                $delayTimer->start((float)$message['Body']);
                $delayTimer->stop(1, $receivedTime);

                if (((count($delayTimer) % 100) == 0) || (count($delayTimer) == count($receiveTimer))) {
                    print $receiveTimer;
                    print $deleteTimer;
                    print $delayTimer;
                }
            }
        }
    }

}

$rx = new rx;
$rx->receive();

?>
