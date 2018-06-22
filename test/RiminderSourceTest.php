<?php

declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';
require_once 'TestHelper.php';


use PHPUnit\Framework\TestCase;

final class RiminderTestSource extends TestCase {

  public function testGetSources(): void {
      $api = new Riminder(TestHelper::getSecret());
      $refKeys = array('source_id', 'name', 'type', 'archive', 'date_creation');

      $getSources = function () use ($api) { return $api->source->getSources(); };

      $resp = TestHelper::useApiFuncWithReportedErr($this, $getSources);
      if (empty($resp)) {
        $this->markTestSkipped('No datas retrieved!');
        return;
      }
      TestHelper::assertArrayHasKeys($this, $resp[0], $refKeys);
      TestHelper::assertDateObj($this, $resp[0]['date_creation']);
  }

  public function testGetSource(): void {
      $api = new Riminder(TestHelper::getSecret());
      $refKeys = array('source_id',
        'name',
        'type',
        'archive',
        'count_source',
        'date_creation',
        );

      $getSources = function () use ($api) {  return $api->source->getSources(); };
      $sources = TestHelper::useApiFuncWithReportedErrAsSkip($this, $getSources);
      if (empty($sources)) {
        $this->markTestSkipped('No jobs retrieved!');
        return;
      }
      $source_id = $sources[0]['source_id'];
      $getSource = function () use ($api, $source_id) {  return $api->source->getSource($source_id); };
      $resp = TestHelper::useApiFuncWithReportedErr($this, $getSource);
      if (empty($resp)) {
        $this->fail('No datas retrieved!');
        return;
      }

      TestHelper::assertArrayHasKeys($this, $resp, $refKeys);
      TestHelper::assertDateObj($this, $resp['date_creation']);
  }

  public function testGetSourceWithInvalidSourceId(): void {
      $api = new Riminder(TestHelper::getSecret());

      $source_id = 'zap';
      $getSource = function () use ($api, $source_id) {  return $api->source->getSource($source_id); };
      $resp = TestHelper::useApiFuncWithExpectedErr($this, $getSource, 'RiminderApiResponseException');
  }

}
 ?>
