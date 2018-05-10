<?php
  declare(strict_types=1);
  require __DIR__ . '/../vendor/autoload.php';


  use PHPUnit\Framework\TestCase;

  final class RiminderTest extends TestCase {

    public $APISECRET = "ask_ce813e1812ebeb663489abdad8b13aea";
    // public $APISECRET = 'ask_9322c036347fd33a3b23fec1e94fb1a8';
    public $gSource_id = "";
    public $gprofile_id = "";

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
        $start =  new DateTime('2017-01-02');
        $end =  new DateTime();
        $seniority = "ALL";
        $source_ids = array();
        $job_id = null;
        $stage = null;
        $page = 2;
        $limit = 10;
        $sort_by = "RANKING";

        $srcResp = $api->source->getSources();
    for ($i = 0; $i < count($srcResp["data"]); $i++){
      if ($srcResp['data'][$i]['type'] == 'folder' || $srcResp['data'][$i]['type'] === 'link') {
        $source_ids[] = $srcResp['data'][$i]['source_id'];
      }
    }
        // var_dump($source_ids);
        $jobResp = $api->job->getJobs();
        if (count($jobResp['data']) > 0) {
            $jobResp = $jobResp['data'][0]['job_id'];
        }
        $resp = $api->profile->getProfiles($source_ids, $start->getTimestamp(), $end->getTimestamp(), $page);
        // var_dump($resp);
        $this->assertArrayHasKey('code', $resp);
        $this->assertArrayHasKey('message', $resp);
        $this->assertArrayHasKey('data', $resp);
        $this->assertArrayHasKey('page', $resp['data']);
        $this->assertArrayHasKey('maxPage', $resp['data']);
        $this->assertArrayHasKey('count_profiles', $resp['data']);
        $this->assertArrayHasKey('profiles', $resp['data']);
        if (count($resp['data']['profiles']) > 0){
          $this->assertArrayHasKey('profile_id', $resp['data']['profiles'][0]);
          $this->assertArrayHasKey('profile_reference', $resp['data']['profiles'][0]);
          $this->assertArrayHasKey('name', $resp['data']['profiles'][0]);
          $this->assertArrayHasKey('email', $resp['data']['profiles'][0]);
          $this->assertArrayHasKey('seniority', $resp['data']['profiles'][0]);

          $this->assertArrayHasKey('date_reception', $resp['data']['profiles'][0]);
          $this->assertArrayHasKey('date', $resp['data']['profiles'][0]['date_reception']);
          $this->assertArrayHasKey('timezone_type', $resp['data']['profiles'][0]['date_reception']);
          $this->assertArrayHasKey('timezone', $resp['data']['profiles'][0]['date_reception']);

          $this->assertArrayHasKey('date_creation', $resp['data']['profiles'][0]);
          $this->assertArrayHasKey('date', $resp['data']['profiles'][0]['date_creation']);
          $this->assertArrayHasKey('timezone_type', $resp['data']['profiles'][0]['date_creation']);
          $this->assertArrayHasKey('timezone', $resp['data']['profiles'][0]['date_creation']);

          $this->assertArrayHasKey('source', $resp['data']['profiles'][0]);
          $this->assertArrayHasKey('source_id', $resp['data']['profiles'][0]['source']);
          $this->assertArrayHasKey('name', $resp['data']['profiles'][0]['source']);
          $this->assertArrayHasKey('type', $resp['data']['profiles'][0]['source']);
          // $this->assertArrayHasKey('score', $resp['data']['profiles'][0]);
          // $this->assertArrayHasKey('rating', $resp['data']['profiles'][0]);
        }

    }

    public function testProfileGet(): void {
        $api = new Riminder($this->APISECRET);
        $start =  new DateTime('2017-01-02');
        $end =  new DateTime();
        $source_ids = array();

        $srcResp = $api->source->getSources();
        // print("\nGET /sources:\n");
        // var_dump($srcResp);
        // print("\n-------------------------------------------------\n");
        for ($i = 0; $i < count($srcResp["data"]); $i++){
          if ($srcResp['data'][$i]['type'] == 'folder' || $srcResp['data'][$i]['type'] === 'link') {
            $source_ids[] = $srcResp['data'][$i]['source_id'];
          }
        }
        // print("Selected source ids:\n");
        // var_dump($source_ids);
        // print("\n-------------------------------------------------\n");
        $respTmp = $api->profile->getProfiles($source_ids, $start->getTimestamp(), $end->getTimestamp(), 1, 1000);
        // print("\nGET /profiles result:\n");
        // var_dump($respTmp);
        // print("\n-------------------------------------------------\n");
        if (count($respTmp['data']['profiles']) > 0) {
          for ($i = 0; $i < count($respTmp['data']['profiles']); $i++) {
            $profile_id = $respTmp['data']['profiles'][$i]['profile_id'];
            $source_id = $respTmp['data']['profiles'][$i]['source']['source_id'];
            // print("Selected profile id:'$profile_id'\n");
            // print("Selected source id: '$source_id'\n");
            $resp = $api->profile->get($profile_id, $source_id);
            // print("GET /profile result\n");
            // var_dump($resp);
            if (array_key_exists('data', $resp)) {
              break;
            }
          }
          if (!array_key_exists('data', $resp)) {
            $this->fail('No profile has been retrieved, test failed');
          }
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
        $start =  new DateTime('2017-01-02');
        $end =  new DateTime();
        $source_ids = array();

        $srcResp = $api->source->getSources();
        for ($i = 0; $i < count($srcResp["data"]); $i++){
          if ($srcResp['data'][$i]['type'] == 'folder' || $srcResp['data'][$i]['type'] === 'link') {
            $source_ids[] = $srcResp['data'][$i]['source_id'];
          }
        }
        $respTmp = $api->profile->getProfiles($source_ids, $start->getTimestamp(), $end->getTimestamp(), 1);

        if (count($respTmp['data']['profiles']) > 0) {
          for ($i = 0; $i < count($respTmp['data']['profiles']); $i++) {
            $profile_id = $respTmp['data']['profiles'][$i]['profile_id'];
            $source_id = $respTmp['data']['profiles'][$i]['source']['source_id'];
            $resp = $api->profile->getDocuments($profile_id, $source_id);
            if (array_key_exists('data', $resp)) {
              break;
            }
          }
          if (!array_key_exists('data', $resp)) {
            $this->fail('No profile document has been retrieved, test failed');
          }
          $this->assertArrayHasKey('code', $resp);
          $this->assertArrayHasKey('message', $resp);
          $this->assertArrayHasKey('data', $resp);
           if (count($resp['data']) > 0) {
            $this->assertArrayHasKey('type', $resp['data'][0]);
            $this->assertArrayHasKey('file_name', $resp['data'][0]);
            $this->assertArrayHasKey('original_file_name', $resp['data'][0]);
            $this->assertArrayHasKey('file_size', $resp['data'][0]);
            $this->assertArrayHasKey('extension', $resp['data'][0]);
            $this->assertArrayHasKey('url', $resp['data'][0]);
            $this->assertArrayHasKey('timestamp', $resp['data'][0]);
        }
      }
    }

    public function testProfileGetExtractions(): void {
        $api = new Riminder($this->APISECRET);
        $start =  new DateTime('2017-01-02');
        $end =  new DateTime();
        $source_ids = array();

        $srcResp = $api->source->getSources();
        for ($i = 0; $i < count($srcResp["data"]); $i++){
          if ($srcResp['data'][$i]['type'] == 'folder' || $srcResp['data'][$i]['type'] === 'link') {
            $source_ids[] = $srcResp['data'][$i]['source_id'];
          }
        }
        $respTmp = $api->profile->getProfiles($source_ids, $start->getTimestamp(), $end->getTimestamp(), 1);

        if (count($respTmp['data']['profiles']) > 0) {
          for ($i = 0; $i < count($respTmp['data']['profiles']); $i++) {
            $profile_id = $respTmp['data']['profiles'][$i]['profile_id'];
            $source_id = $respTmp['data']['profiles'][$i]['source']['source_id'];
            $resp = $api->profile->getExtractions($profile_id, $source_id);
            if (array_key_exists('data', $resp)) {
              break;
            }
          }
          if (!array_key_exists('data', $resp)) {
            $this->fail('No profile extraction has been retrieved, test failed');
          }
          $this->assertArrayHasKey('code', $resp);
          $this->assertArrayHasKey('message', $resp);
          $this->assertArrayHasKey('data', $resp);
          $this->assertArrayHasKey('hard_skills', $resp['data']);
          $this->assertArrayHasKey('soft_skills', $resp['data']);
          $this->assertArrayHasKey('languages', $resp['data']);
          $this->assertArrayHasKey('seniority', $resp['data']);
          $this->assertArrayHasKey('experiences', $resp['data']);
          if (count($resp['data']['experiences']) > 0) {
            $this->assertArrayHasKey('title', $resp['data']['experiences'][0]);
            $this->assertArrayHasKey('description', $resp['data']['experiences'][0]);
            $this->assertArrayHasKey('company', $resp['data']['experiences'][0]);
            $this->assertArrayHasKey('location', $resp['data']['experiences'][0]);
            $this->assertArrayHasKey('start_date', $resp['data']['experiences'][0]);
            $this->assertArrayHasKey('end_date', $resp['data']['experiences'][0]);
          }
      }
    }

    public function testProfileGetJobs(): void {
        $api = new Riminder($this->APISECRET);
        $start =  new DateTime('2017-01-02');
        $end =  new DateTime();
        $source_ids = array();

        $srcResp = $api->source->getSources();
        for ($i = 0; $i < count($srcResp["data"]); $i++){
          if ($srcResp['data'][$i]['type'] == 'folder' || $srcResp['data'][$i]['type'] === 'link') {
            $source_ids[] = $srcResp['data'][$i]['source_id'];
          }
        }
        $respTmp = $api->profile->getProfiles($source_ids, $start->getTimestamp(), $end->getTimestamp(), 1);

        if (count($respTmp['data']['profiles']) > 0) {
          for ($i = 0; $i < count($respTmp['data']['profiles']); $i++) {
            $profile_id = $respTmp['data']['profiles'][$i]['profile_id'];
            $source_id = $respTmp['data']['profiles'][$i]['source']['source_id'];
            $resp = $api->profile->getJobs($profile_id, $source_id);
            // var_dump($resp);
            if (array_key_exists('data', $resp)) {
              break;
            }
          }
          if (!array_key_exists('data', $resp)) {
            $this->fail('No profile job has been retrieved, test failed');
          }
          $this->assertArrayHasKey('code', $resp);
          $this->assertArrayHasKey('message', $resp);
          $this->assertArrayHasKey('data', $resp);
          if (count($resp['data']) > 0) {
            $this->assertArrayHasKey('job_id', $resp['data'][0]);
            $this->assertArrayHasKey('job_reference', $resp['data'][0]);
            $this->assertArrayHasKey('name', $resp['data'][0]);
            $this->assertArrayHasKey('score', $resp['data'][0]);
            $this->assertArrayHasKey('rating', $resp['data'][0]);
            $this->assertArrayHasKey('stage', $resp['data'][0]);
          }
      }
    }

    public function testProfileAdd(): void {
      $api = new Riminder($this->APISECRET);
      $now =  new DateTime();
      $source_id = "";
      $file = file_get_contents("./test/testFile.pdf");
      $profile_ref = strval(rand(0, 99999));

      $srcResp = $api->source->getSources();
      for ($i = 0; $i < count($srcResp["data"]); $i++){
        if ($srcResp['data'][$i]['type'] == 'api') {
          $source_id = $srcResp['data'][$i]['source_id'];
          break;
        }
      }
      if ($source_id === "") {
        $this->fail('no source with type = api... abort this test');
        return;
      }
      $this->gSource_id = $source_id;
      $resp = $api->profile->add($source_id, $file, $profile_ref, $now->getTimestamp());

    }

    public function testProfileUpdateStage(): void {
      $api = new Riminder($this->APISECRET);
      $stage = "YES";

      if ($this->gprofile_id == "") {
        $this->fail('no profile previously created... abort this test');
        return;
      }
      $respJob = $api->profile->getJobs($this->gprofile_id);
      if (count($respJob['data'] > 0)) {
        $job_id = $respJob['data'][0]['job_id'];

        $resp = $api->updateStage($this->gprofile_id, $job_id,$stage);

        $this->assertArrayHasKey('code', $resp);
        $this->assertArrayHasKey('message', $resp);
        $this->assertArrayHasKey('data', $resp);

        $this->assertArrayHasKey('profile_id', $resp['data']);
        $this->assertArrayHasKey('profile_reference', $resp['data']);
        $this->assertArrayHasKey('job_id', $resp['data']);
        $this->assertArrayHasKey('job_reference', $resp['data']);
        $this->assertArrayHasKey('stage', $resp['data']);

        $this->assertEquals($this->gprofile_id, $resp['data']['profile_id']);
        $this->assertEquals($job_id, $resp['data']['job_id']);
        $this->assertEquals($stage, $resp['data']['stage']);
      }

    }
    public function testProfileUpdateRating(): void {
      $api = new Riminder($this->APISECRET);
      $rating = 2;

      if ($this->gprofile_id == "") {
        $this->fail('no profile previously created... abort this test');
        return;
      }
      $respJob = $api->profile->getJobs($this->gprofile_id);
      if (count($respJob['data'] > 0)) {
        $job_id = $respJob['data'][0]['job_id'];

        $resp = $api->updateStage($this->gprofile_id, $job_id,$rating);

        $this->assertArrayHasKey('code', $resp);
        $this->assertArrayHasKey('message', $resp);
        $this->assertArrayHasKey('data', $resp);

        $this->assertArrayHasKey('profile_id', $resp['data']);
        $this->assertArrayHasKey('profile_reference', $resp['data']);
        $this->assertArrayHasKey('job_id', $resp['data']);
        $this->assertArrayHasKey('job_reference', $resp['data']);
        $this->assertArrayHasKey('stage', $resp['data']);

        $this->assertEquals($this->gprofile_id, $resp['data']['profile_id']);
        $this->assertEquals($job_id, $resp['data']['job_id']);
        $this->assertEquals($rating, $resp['data']['stage']);
      }
    }
  }
 ?>
