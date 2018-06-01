<?php
declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';
require_once 'TestHelper.php';


use PHPUnit\Framework\TestCase;

final class RiminderTestFilter extends TestCase {

  public function testGetFilters(): void {
      $api = new Riminder(TestHelper::getSecret());
      $refKeys = array('filter_id', 'filter_reference', 'name', 'archive', 'date_creation');

      $getFilters = function () use ($api) { return $api->filter->getFilters(); };

      $resp = TestHelper::useApiFuncWithReportedErr($this, $getFilters);
      if (empty($resp)) {
        $this->markTestSkipped('No datas retrieved!');
        return;
      }
      TestHelper::assertArrayHasKeys($this, $resp[0], $refKeys);
      TestHelper::assertDateObj($this, $resp[0]['date_creation']);
  }

  public function testGet(): void {
      $api = new Riminder(TestHelper::getSecret());
      $refKeys = array('filter_id',
        'filter_reference',
        'name',
        'description',
        'score_threshold',
        'filter',
        'seniority',
        'skills',
        'countries',
        'archive',
        'stages'
        );
      $refFilterKeys = array('name');
      $refStagesKeys = array('count_yes', 'count_later', 'count_no');

      $getFilters = function () use ($api) {  return $api->filter->getFilters(); };
      $filters = TestHelper::useApiFuncWithReportedErrAsSkip($this, $getFilters);
      if (empty($filters)) {
        $this->markTestSkipped('No filters retrieved!');
        return;
      }
      $filter_id = $filters[0]['filter_id'];
      $filter_reference = $filters[0]['filter_reference'];
      $getFilter = function () use ($api, $filter_id, $filter_reference) {  return $api->filter->get($filter_id, $filter_reference); };
      $resp = TestHelper::useApiFuncWithReportedErr($this, $getFilter, $filter_id);
      if (empty($resp)) {
        $this->fail('No datas retrieved!');
        return;
      }

      TestHelper::assertArrayHasKeys($this, $resp, $refKeys);
      TestHelper::assertArrayHasKeys($this, $resp['filter'], $refFilterKeys);
      TestHelper::assertArrayHasKeys($this, $resp['stages'], $refStagesKeys);
      TestHelper::assertDateObj($this, $resp['date_creation']);
  }

  public function testGetWithInvalidFilterId(): void {
      $api = new Riminder(TestHelper::getSecret());

      $filter_id = 'zap';
      $filter_reference = '$filters[0][]';
      $getFilter = function () use ($api, $filter_id, $filter_reference) {  return $api->filter->get($filter_id, $filter_reference); };
      $resp = TestHelper::useApiFuncWithExpectedErr($this, $getFilter, 'RiminderApiResponseException');
  }

}
 ?>
