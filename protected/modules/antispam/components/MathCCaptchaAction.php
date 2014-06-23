<?php
/**********************************************************************************************
 *                            CMS Open Real Estate
 *                              -----------------
 *	version				:	1.8.1
 *	copyright			:	(c) 2014 Monoray
 *	website				:	http://www.monoray.ru/
 *	contact us			:	http://www.monoray.ru/contact
 *
 * This file is part of CMS Open Real Estate
 *
 * Open Real Estate is free software. This work is licensed under a GNU GPL.
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * Open Real Estate is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * Without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 ***********************************************************************************************/

class MathCCaptchaAction extends CCaptchaAction {
    const MODE_MATH_ADVANCED = 'math_advanced';
    const MODE_DEFAULT_ADVANCED = 'default_advanced';
    const MODE_DEFAULT = 'default';

    public $mode;
    public $useAdvanced = false;

    /**
     * @var integer how many times should the same CAPTCHA be displayed. Defaults to 3.
     * A value less than or equal to 0 means the test is unlimited (available since version 1.1.2).
     */
    public $testLimit = 1;
    /**
     * @var integer the height of the generated CAPTCHA image. Defaults to 50.
     */
    public $height = 50;
    /**
     * @var boolean whether to use transparent background. Defaults to false.
     */
    public $transparent = false;
    /**
     * @var integer the minimum length for randomly generated word. Defaults to 5.
     */
    public $minLength = 5;
    /**
     * @var integer the minimum length for randomly generated math. Defaults to 6.
     */
    public $minLengthMath = 6;
    /**
     * @var integer the maximum length for randomly generated word. Defaults to 6.
     */
    public $maxLength = 6;
    /**
     * @var integer the maximum length for randomly generated math. Defaults to 90.
     */
    public $maxLengthMath = 90;
    /**
     * @var integer the offset between characters. Defaults to 2.
     */
    public $offset = 2;
    /**
     * Dots density around characters 0 - 100 [%], defaults 5.
     */
    public $density = 5; // dots density 0 - 100%
    /**
     * The number of lines drawn through the generated captcha picture, default 3.
     */
    public $lines = 3;
    /**
     * The number of sections to be filled with random flood color, default 10.
     */
    public $fillSections = 10;

    public function run() {
        // allow self::MODE_DEFAULT, self::MODE_DEFAULT_ADVANCED,  self::MODE_MATH_ADVANCED
        $this->mode = self::MODE_DEFAULT_ADVANCED;

        if ($this->mode == self::MODE_DEFAULT_ADVANCED || $this->mode == self::MODE_MATH_ADVANCED)  {
            $this->useAdvanced = true;
            $this->fontFile = dirname(__FILE__).'/fonts/nimbus.ttf';
        }

        if(isset($_GET[self::REFRESH_GET_VAR]))  // AJAX request for regenerating code
        {
            $code=$this->getVerifyCode(true);
            echo CJSON::encode(array(
                'hash1'=>$this->generateValidationHash($code),
                'hash2'=>$this->generateValidationHash(strtolower($code)),
                // we add a random 'v' parameter so that FireFox can refresh the image
                // when src attribute of image tag is changed
                'url'=>$this->getController()->createUrl($this->getId(),array('v' => uniqid())),
            ));
        }
        else
            $this->renderImage($this->getVerifyCode(true));
        Yii::app()->end();
    }

    protected function generateVerifyCode() {
        if ($this->mode == self::MODE_MATH_ADVANCED) {
            return rand((int)$this->minLengthMath, (int)$this->maxLengthMath);
        }
        else {
            return parent::generateVerifyCode();
        }
    }

    protected function showCode($code) {
        $rand = rand(1, (int)$code-1);
        return (rand(0, 1)) ? (int)$code-$rand."+".(int)$rand : (int)$code+$rand."-".(int)$rand;
    }

    protected function renderImage($code) {
        if ($this->mode == self::MODE_MATH_ADVANCED) {
            $code = $this->showCode($code);
        }

        $image = imagecreatetruecolor($this->width,$this->height);

        $backColor = imagecolorallocate($image,
            (int)($this->backColor % 0x1000000 / 0x10000),
            (int)($this->backColor % 0x10000 / 0x100),
            $this->backColor % 0x100);
        imagefilledrectangle($image,0,0,$this->width,$this->height,$backColor);
        imagecolordeallocate($image,$backColor);

        if($this->transparent)
            imagecolortransparent($image,$backColor);

        $foreColor = imagecolorallocate($image,
            (int)($this->foreColor % 0x1000000 / 0x10000),
            (int)($this->foreColor % 0x10000 / 0x100),
            $this->foreColor % 0x100);

        if($this->fontFile === null)
            $this->fontFile = dirname(__FILE__).'/fonts/Duality.ttf';

        $length = strlen($code);
        $box = imagettfbbox(30,0,$this->fontFile,$code);
        $w = $box[4] - $box[0] + $this->offset * ($length - 1);
        $h = $box[1] - $box[5];
        $scale = min(($this->width - $this->padding * 2) / $w,($this->height - $this->padding * 2) / $h);
        $x = 10;
        $y = round($this->height * 27 / 40);

        if ($this->useAdvanced) {
            // random font color
            $r = (int)($this->foreColor % 0x1000000 / 0x10000);
            $g = (int)($this->foreColor % 0x10000 / 0x100);
            $b = $this->foreColor % 0x100;
            $foreColor = imagecolorallocate($image, mt_rand($r-50,$r+50), mt_rand($g-50,$g+50),mt_rand($b-50,$b+50));
        }

        for($i = 0; $i < $length; ++$i)
        {
            $fontSize = (int)(rand(26,32) * $scale * 0.8);
            $angle = rand(-10,10);
            $letter = $code[$i];

            if ($this->useAdvanced) {
                // random font color
                if(mt_rand(0,10)>7){
                    $foreColor = imagecolorallocate($image, mt_rand($r-50,$r+50), mt_rand($g-50,$g+50),mt_rand($b-50,$b+50));
                }
            }

            $box = imagettftext($image,$fontSize,$angle,$x,$y,$foreColor,$this->fontFile,$letter);
            $x = $box[2] + $this->offset;
        }

        if ($this->useAdvanced) {
            // add density dots
            $this->density = (int) $this->density;
            if($this->density > 0){
                $length = intval($this->width*$this->height/100*$this->density);
                $c = imagecolorallocate($image, mt_rand(0,255), mt_rand(0,255), mt_rand(0,255));
                for($i=0;$i<$length;++$i){
                    $x = mt_rand(0,$this->width);
                    $y = mt_rand(0,$this->height);
                    imagesetpixel($image, $x, $y, $c);
                }
            }

            // add lines
            $this->lines = (int) $this->lines;
            if($this->lines > 0){
                for($i=0; $i<$this->lines; ++$i){
                    imagesetthickness($image, mt_rand(1,2));
                    // gray lines only to save human eyes:-)
                    $c = imagecolorallocate($image, mt_rand(200,255), mt_rand(200,255), mt_rand(200,255));
                    $x = mt_rand(0, $this->width);
                    $y = mt_rand(0, $this->width);
                    imageline($image, $x, 0, $y, $this->height, $c);
                }
            }

            // filled flood section
            $this->fillSections = (int) $this->fillSections;
            if($this->fillSections > 0){
                for($i = 0; $i < $this->fillSections; ++$i){
                    $c = imagecolorallocate($image, mt_rand(200,255), mt_rand(200,255), mt_rand(200,255));
                    $x = mt_rand(0, $this->width);
                    $y = mt_rand(0, $this->width);
                    imagefill($image, $x, $y, $c);
                }
            }
        }

        imagecolordeallocate($image,$foreColor);

        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Transfer-Encoding: binary');
        header("Content-type: image/png");
        imagepng($image);
        imagedestroy($image);
    }
}