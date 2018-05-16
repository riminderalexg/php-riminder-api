<?php
declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';
require_once 'TestHelper.php';


use PHPUnit\Framework\TestCase;

final class RiminderTestJob extends TestCase {

  public function testGetJobs(): void {
      $api = new Riminder(TestHelper::getSecret());
      $refKeys = array('job_id', 'job_reference', 'name', 'archive', 'date_creation');

      $getJobs = function () use ($api) { return $api->job->getJobs(); };

      $resp = TestHelper::useApiFuncWithReportedErr($this, $getJobs);
      if (empty($resp)) {
        $this->markTestSkipped('No datas retrieved!');
        return;
      }
      TestHelper::assertArrayHasKeys($this, $resp[0], $refKeys);
      TestHelper::assertDateObj($this, $resp[0]['date_creation']);
  }

  public function testGet(): void {
      $api = new Riminder(TestHelper::getSecret());
      $refKeys = array('job_id',
        'job_reference',
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

      $getJobs = function () use ($api) {  return $api->job->getJobs(); };
      $jobs = TestHelper::useApiFuncWithReportedErrAsSkip($this, $getJobs);
      if (empty($jobs)) {
        $this->markTestSkipped('No jobs retrieved!');
        return;
      }
      $job_id = $jobs[0]['job_id'];
      $getJob = function () use ($api, $job_id) {  return $api->job->get($job_id); };
      $resp = TestHelper::useApiFuncWithReportedErr($this, $getJob, $job_id);
      if (empty($resp)) {
        $this->fail('No datas retrieved!');
        return;
      }

      TestHelper::assertArrayHasKeys($this, $resp, $refKeys);
      TestHelper::assertArrayHasKeys($this, $resp['filter'], $refFilterKeys);
      TestHelper::assertArrayHasKeys($this, $resp['stages'], $refStagesKeys);
      TestHelper::assertDateObj($this, $resp['date_creation']);
  }

  public function testGetWithInvalidJobId(): void {
      $api = new Riminder(TestHelper::getSecret());

      $job_id = 'zap';
      $getJob = function () use ($api, $job_id) {  return $api->job->get($job_id); };
      $resp = TestHelper::useApiFuncWithExpectedErr($this, $getJob, 'RiminderApiResponseException');
  }

}
 ?>
