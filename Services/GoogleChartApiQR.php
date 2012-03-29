<?php
/**
 * An interface for Google ChartAPI QR
 *
 * @category    Services
 * @package     Services_GoogleChartApiQR
 * @author      tknzk <info@tknzk.com>
 * @copyright   Copyright (c) 2010, tknzk.com All rights reserved.
 * @license     BSD License
 * @link        http://openpear.org/package/Services_GoogleChartApiQR
 * @link        http://code.google.com/intl/ja/apis/chart/docs/gallery/qr_codes.html
 * @link        https://github.com/tknzk/Services_GoogleChartApiQR
 *
 */

require_once 'PEAR/Exception.php';

class Services_GoogleChartApiQR
{
    const API_URL       = 'http://chart.apis.google.com';

    const CHOE_UTF8     = 'UTF-8';
    const CHOE_ShiftJIS = 'Shift-JIS';
    const CHOE_ISO88591 = 'ISO-8859-1';

    const CHLD_L        = 'L';
    const CHLD_M        = 'M';
    const CHLD_Q        = 'Q';
    const CHLD_H        = 'H';

    /**
     * chart
     */
    private $cht;

    /**
     * image size
     */
    private $chs;

    /**
     * image size width
     */
    private $chsWidth;

    /**
     * image size height
     */
    private $chsHeight;

    /**
     * data
     */
    private $chl;

    /**
     * output encoding
     */
    private $choe;

    /**
     * error correction level
     */
    private $chld;

    /**
     * force encode
     */
    private $forceEncode;

    /**
     * Default constructor
     *
     * @param integer $width
     * @param integer $height
     * @return  void
     */
    public function __construct($width = null, $height = null)
    {
        $this->setCht();

        if (!empty($width) && !empty($height)) {
            $this->setChs($width, $height);
        }

        $this->setForceEncode(false);
    }

    /**
     * create qr code
     *
     * @param string $data
     * @return string $apiUrl
     */
    public function view($data = null)
    {
        if (!empty($data)) {
            $this->setChl($data);
        }

        return self::buildApiUrl();
    }

    /**
     * create qr code binary data
     *
     * @param string $data
     * @return string $response
     */
    public function create($data = null)
    {
        if (!empty($data)) {
            $this->setChl($data);
        }

        $curl   = curl_init();
        curl_setopt($curl, CURLOPT_URL,             self::buildApiUrl());
        curl_setopt($curl, CURLOPT_HEADER,          false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,  true);

        $response = curl_exec($curl);

        if ($response === false) {
            throw new PEAR_Exception(curl_error($curl), curl_errno($curl));
        }

        curl_close($curl);

        return $response;
    }

    /**
     * build request api url
     *
     * @return string $apiUrl
     */
    private function buildApiUrl()
    {

        if (empty($this->chl)) {
            throw new PEAR_Exception('data requierd.');
        } else {
            if ($this->forceEncode) {
                if (mb_detect_encoding($this->chl) == self::CHOE_UTF8) {
                    $this->setChl(mb_convert_encoding(urlencode($this->chl), self::CHOE_ShiftJIS, self::CHOE_UTF8));
                    $this->setChoe(self::CHOE_ShiftJIS);
                }
            }
        }

        if (empty($this->chsWidth)) {
            throw new PEAR_Exception('size is requierd.');
        }

        return self::API_URL    . '/chart?'
                                . 'chs='    . $this->chsWidth . 'x' . $this->chsHeight
                                . '&cht='   . $this->cht
                                . '&chl='   . $this->chl
                                . '&choe='  . $this->choe
                                . '&chld='  . $this->chld
                                . '';
    }

    /**
     * set Cht
     */
    private function setCht()
    {
        $this->cht = (string) 'qr';
    }

    /**
     * set chs
     *
     * $param integer $width
     * $param integer $height
     *
     */
    public function setChs($width, $height)
    {
        $this->setChsWidth($width);
        $this->setChsHeight($height);
    }

    /**
     * set chsWidth
     *
     * $param integer $width
     */
    public function setChsWidth($width)
    {
        $this->chsWidth     = (integer) $width;
    }

    /**
     * set chsHeight
     *
     * $param integer $height
     */
    public function setChsHeight($height)
    {
        $this->chsHeight    = (integer) $height;
    }

    /**
     * set chl
     *
     * $param string $data
     */
    public function setChl($data)
    {
        $this->chl  = (string) ($data);
    }

    /**
     * set choe
     *
     * $param string $choe
     */
    public function setChoe($encode = self::CHOE_UTF8)
    {
        $this->choe  = (string) $encode;
    }

    /**
     * set chld
     *
     * $param string $chld
     */
    public function setChld($level = null)
    {
        if ($level !== null) {

            switch ($level) {
                case self::CHLD_L:
                    $this->chld  = (string) self::CHLD_L;
                    break;
                case self::CHLD_M:
                    $this->chld  = (string) self::CHLD_M;
                    break;
                case self::CHLD_Q:
                    $this->chld  = (string) self::CHLD_Q;
                    break;
                case self::CHLD_H:
                    $this->chld  = (string) self::CHLD_H;
                    break;
                default:
            }

        }
    }

    /**
     * force encode
     *
     * @return  void
     * @param   bool $force
     */
    public function setForceEncode($force = true)
    {
        $this->forceEncode = $force;
    }

}
