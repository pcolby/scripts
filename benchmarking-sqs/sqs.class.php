<?php // See http://colby.id.au/benchmarking-sqs

require_once('AWSSDKforPHP/aws.phar');

class sqs {
    const accessKeyId     = FIXME;
    const secretAccessKey = FIXME;
    const region          = Aws\Common\Enum\Region::FIXME;
    const queueUrl        = FIXME;

    protected $sqs = null;


    function __construct() {
        $aws = Aws\Common\Aws::factory(array(
           'key'    => self::accessKeyId,
           'secret' => self::secretAccessKey,
           'region' => self::region,
        ));
        $this->sqs = $aws->get('sqs');
    }

    protected function output($name, $totalCount, $totalTime, $distribution) {
        ksort($distribution);
        print_r($distribution);
        print("$name: {$totalCount}m " . round($totalTime,2) . 's ' . round($totalCount/$totalTime,1) . "m/s\n");
    }

}

?>
