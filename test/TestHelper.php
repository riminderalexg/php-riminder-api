<?php
  class TestHelper
  {
    static public $APISECRET = "";

    static public function getSecret() {
      return self::$APISECRET;
    }

    static private $lastError = null;

    static public function  getLastError() {
      return self::$lastError;
    }
    // use './vendor/bin/phpunit --verbose --bootstrap vendor/autoload.php test/RiminderJobTest.php'
    // to run tests
    static public function assertArrayHasKeys($testCase, $toTest, $refKeys) {

      foreach ($refKeys as $refKey) {
        $testCase->assertArrayHasKey($refKey, $toTest);
      }
    }

    static public function assertDateObj($testCase, $toTest) {
      $refKeys = array('date', 'timezone_type', 'timezone');

      self::assertArrayHasKeys($testCase, $toTest, $refKeys);
    }

    static public function useApiFuncWithIgnoredErr($testCase, $func) {
      $resp;
      try {
        $resp = $func();
        return $resp;

      } catch (\RiminderApiException $e) {
        // I didn't see anything..
        self::$lastError = $e;
      }
      return array();
    }

    static public function useApiFuncWithReportedErr($testCase, $func) {
      try {
        $resp = $func();
        return $resp;

      } catch (\RiminderApiResponseException $e) {
        $testCase->fail('Test failed cause of invalid response: ' . $e);
      } catch (\RiminderApiArgumentException $e) {
        $testCase->fail('Test failed cause of invalid argument: ' . $e);
      } catch (\RiminderApiException $e) {
        $testCase->markTestSkipped('Test skipped cause of: ' . $e);
      }
    }

    static public function useApiFuncWithReportedErrAsSkip($testCase, $func) {
      try {
        $resp = $func();
        return $resp;

      } catch (\RiminderApiException $e) {
        $testCase->markTestSkipped('Test skipped cause of: ' . $e);
      }
    }

    static public function useApiFuncWithExpectedErr($testCase, $func, $expectedExp) {

      try {
        $resp = $func();
      }catch (\RiminderApiResponseException $e) {
        $testCase->assertInstanceOf($expectedExp, $e);
        return $e;
      } catch (\RiminderApiArgumentException $e) {
        $testCase->assertInstanceOf($expectedExp, $e);
        return $e;
      } catch (\RiminderApiException $e) {
        $testCase->assertInstanceOf($expectedExp, $e);
        return $e;
      }
      $testCase->fail('Expected an error of type ' . $expectedExp);
      return null;
    }
  }
 ?>
