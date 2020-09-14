<?php

/**
 */

namespace app\modules\grabber\base;

/**
 */

 abstract class BaseTransport
 {
 	abstract public static function transportTitle();
 	abstract public function getContent();
 }
