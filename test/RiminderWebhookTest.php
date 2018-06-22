<?php

declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';
require_once 'TestHelper.php';

use PHPUnit\Framework\TestCase;


final class RiminderTestWebhook extends TestCase {

  static $zap = 0;
  static $lastType = null;
  static $decoded_request = null;

  static function change_zap($eventType, $decoded_request) {
    self::$zap = 1;
    self::$lastType = $eventType;
    self::$decoded_request = $decoded_request;

  }

  public function testwebhook_no_err() {
    $api = new Riminder(TestHelper::getSecret(), TestHelper::getWebhookSecret());
    $api->webhook->setHandler(RiminderEvents::PROFILE_PARSE_ERROR, 'RiminderTestWebhook::change_zap');
    $encoded_req = TestHelper::generateWebhookRequest(RiminderEvents::PROFILE_PARSE_ERROR);
    $api->webhook->handleRequest($encoded_req['HTTP_RIMINDER_SIGNATURE']);
    $this->assertEquals(self::$zap, 1);
    $this->assertEquals(self::$lastType, RiminderEvents::PROFILE_PARSE_ERROR);
    $ref_keys = ['type', 'message', 'profile'];
    $profile_ref_keys = ['profile_id', 'profile_reference'];
    TestHelper::assertArrayHasKeys($this, self::$decoded_request, $ref_keys);
    TestHelper::assertArrayHasKeys($this, self::$decoded_request['profile'], $profile_ref_keys);
  }

  public function testPostCheck(): void {
      $api = new Riminder(TestHelper::getSecret());
      $refKeys = array('team_name', 'webhook_url', 'webhook_id');

      $checkWebhook = function () use ($api) { return $api->webhook->postCheck(); };

      $resp = TestHelper::useApiFuncWithReportedErr($this, $checkWebhook);
      if (empty($resp)) {
        $this->markTestSkipped('No datas retrieved!');
        return;
      }
      TestHelper::assertArrayHasKeys($this, $resp, $refKeys);
  }

}
 ?>
