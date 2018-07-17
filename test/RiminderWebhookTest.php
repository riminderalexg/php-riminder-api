<?php

declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';
require_once 'TestHelper.php';

use PHPUnit\Framework\TestCase;


final class RiminderTestWebhook extends TestCase {

  static $zap = 0;
  static $lastType = null;
  static $decoded_request = null;

  static function reset_test_values() {
    self::$zap = null;
    self::$lastType = null;
    self::$decoded_request = null;
  }

  static function change_zap($decoded_request, $eventType) {
    self::$zap = 1;
    self::$lastType = $eventType;
    self::$decoded_request = $decoded_request;

  }

  static function change_zap2($decoded_request) {
    self::$zap = 1;
    self::$decoded_request = $decoded_request;

  }

  public function testwebhook_no_err() {
    self::reset_test_values();
    $api = new Riminder(TestHelper::getSecret(), TestHelper::getWebhookSecret());
    $api->webhooks->setHandler(RiminderEvents::PROFILE_PARSE_ERROR, 'RiminderTestWebhook::change_zap');
    $encoded_req = TestHelper::generateWebhookRequest(RiminderEvents::PROFILE_PARSE_ERROR);
    $api->webhooks->handle($encoded_req['HTTP-RIMINDER-SIGNATURE']);
    $this->assertEquals(self::$zap, 1);
    $this->assertEquals(self::$lastType, RiminderEvents::PROFILE_PARSE_ERROR);
    $ref_keys = ['type', 'message', 'profile'];
    $profile_ref_keys = ['profile_id', 'profile_reference'];
    TestHelper::assertArrayHasKeys($this, self::$decoded_request, $ref_keys);
    TestHelper::assertArrayHasKeys($this, self::$decoded_request['profile'], $profile_ref_keys);
  }

  public function testwebhook_no_err_array_arg() {
    self::reset_test_values();
    $api = new Riminder(TestHelper::getSecret(), TestHelper::getWebhookSecret());
    $api->webhooks->setHandler(RiminderEvents::PROFILE_PARSE_ERROR, 'RiminderTestWebhook::change_zap');
    $encoded_req = TestHelper::generateWebhookRequest(RiminderEvents::PROFILE_PARSE_ERROR);
    $api->webhooks->handle($encoded_req);
    $this->assertEquals(self::$zap, 1);
    $this->assertEquals(self::$lastType, RiminderEvents::PROFILE_PARSE_ERROR);
    $ref_keys = ['type', 'message', 'profile'];
    $profile_ref_keys = ['profile_id', 'profile_reference'];
    TestHelper::assertArrayHasKeys($this, self::$decoded_request, $ref_keys);
    TestHelper::assertArrayHasKeys($this, self::$decoded_request['profile'], $profile_ref_keys);
  }

  public function testwebhook_no_err_handler_one_arg() {
    self::reset_test_values();
    $api = new Riminder(TestHelper::getSecret(), TestHelper::getWebhookSecret());
    $api->webhooks->setHandler(RiminderEvents::PROFILE_PARSE_ERROR, 'RiminderTestWebhook::change_zap2');
    $encoded_req = TestHelper::generateWebhookRequest(RiminderEvents::PROFILE_PARSE_ERROR);
    $api->webhooks->handle($encoded_req);
    $this->assertEquals(self::$zap, 1);
    $ref_keys = ['type', 'message', 'profile'];
    $profile_ref_keys = ['profile_id', 'profile_reference'];
    TestHelper::assertArrayHasKeys($this, self::$decoded_request, $ref_keys);
    TestHelper::assertArrayHasKeys($this, self::$decoded_request['profile'], $profile_ref_keys);
  }


  public function testPostCheck(): void {
      $api = new Riminder(TestHelper::getSecret());
      $refKeys = array('team_name', 'webhook_url', 'webhook_id');

      $checkWebhook = function () use ($api) { return $api->webhooks->check(); };

      $resp = TestHelper::useApiFuncWithReportedErr($this, $checkWebhook);
      if (empty($resp)) {
        $this->markTestSkipped('No datas retrieved!');
        return;
      }
      TestHelper::assertArrayHasKeys($this, $resp['data'], $refKeys);
  }

}
 ?>
