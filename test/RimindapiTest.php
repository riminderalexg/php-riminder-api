<?php
  declare(strict_types=1);
  require __DIR__ . '/../vendor/autoload.php';


  use PHPUnit\Framework\TestCase;

  final class RiminderTest extends TestCase {

    public $APISECRET = "ask_ce813e1812ebeb663489abdad8b13aea";

    public function testJobGetJobs(): void {
        $api = new Riminder($this->APISECRET);
        $resp = $api->job->getJobs();

        $this->assertArrayHasKey('code', $resp);
        $this->assertArrayHasKey('message', $resp);
        $this->assertArrayHasKey('data', $resp);
        if (count($resp['data']) > 0) {
          $this->assertArrayHasKey('job_id', $resp['data'][0]);
          $this->assertArrayHasKey('job_reference', $resp['data'][0]);
          $this->assertArrayHasKey('name', $resp['data'][0]);
          $this->assertArrayHasKey('archive', $resp['data'][0]);
          $this->assertArrayHasKey('date_creation', $resp['data'][0]);
          $this->assertArrayHasKey('date', $resp['data'][0]['date_creation']);
          $this->assertArrayHasKey('timezone_type', $resp['data'][0]['date_creation']);
          $this->assertArrayHasKey('timezone', $resp['data'][0]['date_creation']);
        }
    }

    public function testJobGet(): void {
        $api = new Riminder($this->APISECRET);
        $respTmp = $api->job->getJobs();
        $resp;
        if (count($respTmp['data']) > 0) {
          $jobid = $respTmp['data'][0]['job_id'];
          $resp = $api->job->get($jobid);

          $this->assertArrayHasKey('code', $resp);
          $this->assertArrayHasKey('message', $resp);
          $this->assertArrayHasKey('data', $resp);
          $this->assertArrayHasKey('job_id', $resp['data']);
          $this->assertArrayHasKey('job_reference', $resp['data']);
          $this->assertArrayHasKey('name', $resp['data']);
          $this->assertArrayHasKey('description', $resp['data']);
          $this->assertArrayHasKey('score_threshold', $resp['data']);
          $this->assertArrayHasKey('filter', $resp['data']);
          $this->assertArrayHasKey('name', $resp['data']['filter']);
          $this->assertArrayHasKey('seniority', $resp['data']);
          $this->assertArrayHasKey('skills', $resp['data']);
          $this->assertArrayHasKey('countries', $resp['data']);
          $this->assertArrayHasKey('archive', $resp['data']);
          $this->assertArrayHasKey('stages', $resp['data']);
          $this->assertArrayHasKey('count_yes', $resp['data']['stages']);
          $this->assertArrayHasKey('count_later', $resp['data']['stages']);
          $this->assertArrayHasKey('count_no', $resp['data']['stages']);
          $this->assertArrayHasKey('date_creation', $resp['data']);
          $this->assertArrayHasKey('date', $resp['data']['date_creation']);
          $this->assertArrayHasKey('timezone_type', $resp['data']['date_creation']);
          $this->assertArrayHasKey('timezone', $resp['data']['date_creation']);
      }
    }

    public function testSourceGetSources(): void {
        $api = new Riminder($this->APISECRET);
        $resp = $api->source->getSources();

        $this->assertArrayHasKey('code', $resp);
        $this->assertArrayHasKey('message', $resp);
        $this->assertArrayHasKey('data', $resp);
        if (count($resp['data']) > 0) {
          $this->assertArrayHasKey('source_id', $resp['data'][0]);
          $this->assertArrayHasKey('name', $resp['data'][0]);
          $this->assertArrayHasKey('type', $resp['data'][0]);
          $this->assertArrayHasKey('archive', $resp['data'][0]);
          $this->assertArrayHasKey('date_creation', $resp['data'][0]);
          $this->assertArrayHasKey('date', $resp['data'][0]['date_creation']);
          $this->assertArrayHasKey('timezone_type', $resp['data'][0]['date_creation']);
          $this->assertArrayHasKey('timezone', $resp['data'][0]['date_creation']);
        }
    }

    public function testSourceGet(): void {
        $api = new Riminder($this->APISECRET);
        $respTmp = $api->source->getSources();

        if (count($respTmp['data']) > 0) {
          $source_id = $respTmp['data'][0]['source_id'];
          $resp = $api->source->get($source_id);

          $this->assertArrayHasKey('code', $resp);
          $this->assertArrayHasKey('message', $resp);
          $this->assertArrayHasKey('data', $resp);
          $this->assertArrayHasKey('source_id', $resp['data']);
          $this->assertArrayHasKey('name', $resp['data']);
          $this->assertArrayHasKey('type', $resp['data']);
          $this->assertArrayHasKey('archive', $resp['data']);
          $this->assertArrayHasKey('count_source', $resp['data']);
          $this->assertArrayHasKey('date_creation', $resp['data']);
          $this->assertArrayHasKey('date', $resp['data']['date_creation']);
          $this->assertArrayHasKey('timezone_type', $resp['data']['date_creation']);
          $this->assertArrayHasKey('timezone', $resp['data']['date_creation']);
      }
    }

    public function testProfileGetProfiles(): void {
        $api = new Riminder($this->APISECRET);
        $start =  new DateTime('2018-01-02');
        $end =  new DateTime();
        $seniority = "ALL";
        $source_ids = array();
        $job_id = null;
        $stage = null;
        $page = 1;
        $limit = 10;
        $sort_by = "RANKING";

        $srcResp = $api->source->getSources();
        for ($i = 0; $i < 5; $i++){
          if (count($srcResp["data"]) <= $i) {
            break;
          }
          $source_ids[] = $srcResp['data'][0]['source_id'];
        }
        $jobResp = $api->job->getJobs();
        if (count($jobResp['data']) > 0) {
            $jobResp = $jobResp['data'][0]['job_id'];
        }
        $resp = $api->profile->getProfiles($source_ids, $start, $end, $page, $limit, $sort_by, $seniority, $job_id, $stage);

        $this->assertArrayHasKey('code', $resp);
        $this->assertArrayHasKey('message', $resp);
        $this->assertArrayHasKey('data', $resp);
        $this->assertArrayHasKey('page', $resp['data']);
        $this->assertArrayHasKey('maxPage', $resp['data']);
        $this->assertArrayHasKey('count_profiles', $resp['data']);
        $this->assertArrayHasKey('profiles', $resp['data']);
        if (count($resp['data']['profiles']) > 0){
          $this->assertArrayHasKey('profile_id', $resp['data']['profiles']);
          $this->assertArrayHasKey('profile_reference', $resp['data']['profiles']);
          $this->assertArrayHasKey('name', $resp['data']['profiles']);
          $this->assertArrayHasKey('email', $resp['data']['profiles']);
          $this->assertArrayHasKey('seniority', $resp['data']['profiles']);

          $this->assertArrayHasKey('date_reception', $resp['data']['profiles']);
          $this->assertArrayHasKey('date', $resp['data']['profiles']['date_reception']);
          $this->assertArrayHasKey('timezone_type', $resp['data']['profiles']['date_reception']);
          $this->assertArrayHasKey('timezone', $resp['data']['profiles']['date_reception']);

          $this->assertArrayHasKey('date_creation', $resp['data']['profiles']);
          $this->assertArrayHasKey('date', $resp['data']['profiles']['date_creation']);
          $this->assertArrayHasKey('timezone_type', $resp['data']['profiles']['date_creation']);
          $this->assertArrayHasKey('timezone', $resp['data']['profiles']['date_creation']);

          $this->assertArrayHasKey('source', $resp['data']['profiles']);
          $this->assertArrayHasKey('source_id', $resp['data']['profiles']['source']);
          $this->assertArrayHasKey('name', $resp['data']['profiles']['source']);
          $this->assertArrayHasKey('type', $resp['data']['profiles']['source']);

          $this->assertArrayHasKey('score', $resp['data']['profiles']);
          $this->assertArrayHasKey('rating', $resp['data']['profiles']);
        }

    }

    public function testProfileGet(): void {
        $api = new Riminder($this->APISECRET);
        $start =  "1494539999";
        $end =  "1502488799";
        $source_ids = array();

        $srcResp = $api->source->getSources();
        for ($i = 0; $i < 2; $i++){
          if (count($srcResp["data"]) <= $i) {
            break;
          }
          $source_ids = $srcResp['data'][0]['source_id'];
        }
        // var_dump($source_ids);
        $respTmp = $api->profile->getProfiles($source_ids, $start, $end, 1);

        var_dump($respTmp);
        if (count($respTmp['data']['profiles']) > 0) {
          $profile_id = $respTmp['data']['profiles'][0]['profile_id'];
          $resp = $api->profile->get($profile_id);
          var_dump($resp);
          $this->assertArrayHasKey('code', $resp);
          $this->assertArrayHasKey('message', $resp);
          $this->assertArrayHasKey('data', $resp);
          $this->assertArrayHasKey('profile_id', $resp['data']);
          $this->assertArrayHasKey('profile_reference', $resp['data']);
          $this->assertArrayHasKey('name', $resp['data']);
          $this->assertArrayHasKey('email', $resp['data']);
          $this->assertArrayHasKey('phone', $resp['data']);
          $this->assertArrayHasKey('address', $resp['data']);
          $this->assertArrayHasKey('source_id', $resp['data']);
          $this->assertArrayHasKey('date_reception', $resp['data']);
          $this->assertArrayHasKey('date', $resp['data']['date_reception']);
          $this->assertArrayHasKey('timezone_type', $resp['data']['date_reception']);
          $this->assertArrayHasKey('timezone', $resp['data']['date_reception']);
          $this->assertArrayHasKey('date_creation', $resp['data']);
          $this->assertArrayHasKey('date', $resp['data']['date_creation']);
          $this->assertArrayHasKey('timezone_type', $resp['data']['date_creation']);
          $this->assertArrayHasKey('timezone', $resp['data']['date_creation']);
      }
    }

    public function testProfileGetDocuments(): void {
        $api = new Riminder($this->APISECRET);
        $start =  "1494539999";
        $end =  "1502488799";
        $source_ids = array();

        $srcResp = $api->source->getSources();
        for ($i = 0; $i < 2; $i++){
          if (count($srcResp["data"]) <= $i) {
            break;
          }
          $source_ids = $srcResp['data'][0]['source_id'];
        }
        // var_dump($source_ids);
        $respTmp = $api->profile->getProfiles($source_ids, $start, $end, 1);

        var_dump($respTmp);
        if (count($respTmp['data']['profiles']) > 0) {
          $profile_id = $respTmp['data']['profiles'][0]['profile_id'];
          $resp = $api->profile->getDocuments($profile_id);
          var_dump($resp);
          $this->assertArrayHasKey('code', $resp);
          $this->assertArrayHasKey('message', $resp);
          $this->assertArrayHasKey('data', $resp);
          $this->assertArrayHasKey('type', $resp['data']);
          $this->assertArrayHasKey('file_name', $resp['data']);
          $this->assertArrayHasKey('original_file_name', $resp['data']);
          $this->assertArrayHasKey('file_size', $resp['data']);
          $this->assertArrayHasKey('extension', $resp['data']);
          $this->assertArrayHasKey('url', $resp['data']);
          $this->assertArrayHasKey('timestamp', $resp['data']);
      }
    }

    public function testProfileGetExtractions(): void {
        $api = new Riminder($this->APISECRET);
        $start =  "1494539999";
        $end =  "1502488799";
        $source_ids = array();

        $srcResp = $api->source->getSources();
        for ($i = 0; $i < 2; $i++){
          if (count($srcResp["data"]) <= $i) {
            break;
          }
          $source_ids = $srcResp['data'][0]['source_id'];
        }
        // var_dump($source_ids);
        $respTmp = $api->profile->getProfiles($source_ids, $start, $end, 1);

        var_dump($respTmp);
        if (count($respTmp['data']['profiles']) > 0) {
          $profile_id = $respTmp['data']['profiles'][0]['profile_id'];
          $resp = $api->profile->getExtractions($profile_id);
          var_dump($resp);
          $this->assertArrayHasKey('code', $resp);
          $this->assertArrayHasKey('message', $resp);
          $this->assertArrayHasKey('data', $resp);
          $this->assertArrayHasKey('hard_skills', $resp['data']);
          $this->assertArrayHasKey('soft_skills', $resp['data']);
          $this->assertArrayHasKey('languages', $resp['data']);
          $this->assertArrayHasKey('seniority', $resp['data']);
          $this->assertArrayHasKey('experiences', $resp['data']);
          if (count($resp['data']['experiences'] > 0)) {
            $this->assertArrayHasKey('title', $resp['data']['experiences']);
            $this->assertArrayHasKey('description', $resp['data']['experiences']);
            $this->assertArrayHasKey('company', $resp['data']['experiences']);
            $this->assertArrayHasKey('location', $resp['data']['experiences']);
            $this->assertArrayHasKey('start_date', $resp['data']['experiences']);
            $this->assertArrayHasKey('end_date', $resp['data']['experiences']);
          }
      }
    }


  }
 ?>
