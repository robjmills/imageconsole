<?php
require_once __DIR__ . '/../vendor/autoload.php';

use ImageConsole\ImageValidate;
use PHPUnit\Framework\TestCase;

class ImageValidateTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testIsValidImage()
    {
        $resources = __DIR__ . '/../resources/local';
        $validImage = $resources . '/lebowski.jpg';
        $invalidImage = $resources . '/pdf-sample.pdf';
        $validator = new ImageValidate(new \Psr\Log\NullLogger());
        $this->assertTrue($validator->isValidImage($validImage));

        // invalid image will throw an Exception
        $this->expectException(Exception::class);
        $validator->isValidImage($invalidImage);
    }
}
