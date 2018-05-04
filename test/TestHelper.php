<?php
  class TestHelper
  {
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
        // I didn't see anything...
      }
      return array();
    }

    static public function useApiFuncWithReportedErr($testCase, $func) {
      try {
        $resp = $func();
        return $resp;

      } catch (\RiminderApiResponseException $e) {
        $this->fail('Test failed cause of invalid response: ' . $e);
      } catch (\RiminderApiArgumentException $e) {
        $this->fail('Test failed cause of invalid argument: ' . $e);
      } catch (\RiminderApiException $e) {
        $this->markTestSkipped('Test skipped cause of: ' . $e);
      }
    }

    static public function useApiFuncWithReportedErrAsSkip($testCase, $func) {
      try {
        $resp = $func();
        return $resp;

      } catch (\RiminderApiException $e) {
        $this->markTestSkipped('Test skipped cause of: ' . $e);
      }
    }
  }
 ?>
