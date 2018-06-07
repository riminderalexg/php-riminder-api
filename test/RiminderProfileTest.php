<?php
declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';
require_once 'TestHelper.php';


use PHPUnit\Framework\TestCase;

final class RiminderTestProfile extends TestCase {

  private  $testSourceId = null;
  private  $testProfileId = null;
  private static $lastValidProfileId;
  private static $lastValidSourceId;

  private function useApiFuncWithValidProfile($profile_ids, $profileFunc) {
    foreach ($profile_ids as $profile_idPair) {
      $err;
      $profile_id = $profile_idPair['profile_id'];
      $source_id = $profile_idPair['source_id'];
      $getProfile = function () use ($profileFunc, $profile_id, $source_id) {  return $profileFunc($profile_id, $source_id); };
      $resp = TestHelper::useApiFuncWithIgnoredErr($this, $getProfile);
      if (!empty($resp)) {
        self::$lastValidProfileId = $profile_id;
        self::$lastValidSourceId = $source_id;
        return $resp;
      }
      $err = TestHelper::getLastError();
      $isResponseExp = $err instanceof RiminderApiResponseException;
      if (!$isResponseExp || $err->getHttpCode() != 403) {
        $this->fail('Api Response Exception on profile retrieving: ' . $err);
      }
    }
    return null;
  }

  private function getValidProfilePairId($api) {
    $profile_ids = $this->getSomeProfileIdsPair($api);

    foreach ($profile_ids as $profile_idPair) {
      $profile_id = $profile_idPair['profile_id'];
      $source_id = $profile_idPair['source_id'];
      $profileGet = function () use ($api, $profile_id, $source_id)
        { return $api->profile->get($profile_id, $source_id); };
      $resp = TestHelper::useApiFuncWithIgnoredErr($this, $profileGet);
      if (!empty($resp)) {
        return $profile_idPair;
      }
    }
    return null;
  }

  private static function isFiltered($source, $field, $filters){
    if (empty($filters)) {
      return true;
    }
    foreach ($filters as $filter) {
      if ($source[$field] == $filter) {
        return true;
      }
    }
    return false;
  }

  private function getSomeNotSharedSourceIds($api, $typeFilters = array(), $nameFilters = array(), $n_ids = 5) {
    $source_ids = array();

    $getSources = function () use ($api) { return $api->source->getSources(); };
    $sources = TestHelper::useApiFuncWithReportedErrAsSkip($this, $getSources);
    if (empty($sources)) {
      $this->markTestSkipped('No sources retrieved!');
      return;
    }
    foreach ($sources as $source) {

      $ok = self::isFiltered($source, 'type', $typeFilters);
      $ok3 = self::isFiltered($source, 'name', $nameFilters);
      if ($ok && $ok3) {
        $source_ids[] = $source['source_id'];
      }
      if (count($source_ids) > $n_ids) {
        break;
      }
    }
    return $source_ids;
  }

  private function getSomeProfileIdsPair($api) {

    $profile_ids = array();
    $source_ids = $this->getSomeNotSharedSourceIds($api);
    $start =  new DateTime('2017-01-02');
    $end =  new DateTime();

    $args = array(
      $api->Fields->SOURCE_IDS => $source_ids,
      $api->Fields->DATE_START => $start->getTimestamp(),
      'date_end' => $end,
      'limit'    => 100,
      $api->Fields->SORT_BY => $api->Sort_by->RANKING
     );
    $getProfiles = function () use ($api, $args)
     { return $api->profile->getProfiles($args); };

      $profiles = TestHelper::useApiFuncWithReportedErr($this, $getProfiles);
      foreach ($profiles['profiles'] as $profile) {
        $source = $profile['source'];
        $profile_ids[] = array('profile_id' => $profile['profile_id'],
                                'source_id' => $source['source_id']);
    }

    return $profile_ids;
  }

  private function getAfilterId($api) {
    $getfilters = function () use ($api) { return $api->filter->getfilters(); };

    $resp = TestHelper::useApiFuncWithIgnoredErr($this, $getfilters);
    if (empty($resp)) {
      return null;
    }
    return $resp[0]['filter_id'];
  }

  public function testGetProfilesFullArgs(): void {
      $api = new Riminder(TestHelper::getSecret());

      $refKeys = array('page', 'maxPage', 'count_profiles', 'profiles');
      $refProfilesKeys = array('profile_id', 'profile_reference', 'name',
        'email', 'seniority', 'date_reception',
        'date_creation', 'source');
      $refSourceKeys = array ('source_id', 'name', 'type');

      $start =  new DateTime('2017-01-02');
      $end =  new DateTime();
      $seniority = "senior";
      $source_ids = $this->getSomeNotSharedSourceIds($api);
      $filter_id = null;
      $stage = null;
      $page = 2;
      $limit = 10;
      $sort_by = "ranking";
      $order_by = 'asc';

      $args = array(
        $api->Fields->SOURCE_IDS => $source_ids,
        $api->Fields->DATE_START => $start->getTimestamp(),
        'date_end' => $end,
        $api->Fields->SENIORITY => $seniority,
        $api->Fields->FILTER_ID => $filter_id,
        $api->Fields->STAGE => $stage,
        'page' => $page,
        'limit' => $limit,
        'sort_by' => $sort_by,
        'order_by' => $order_by
       );
      $getProfiles = function () use ($api, $args)
       { return $api->profile->getProfiles($args); };

      $resp = TestHelper::useApiFuncWithReportedErr($this, $getProfiles);
      TestHelper::assertArrayHasKeys($this, $resp, $refKeys);
      if (empty($resp['profiles'])) {
        $this->markTestSkipped('No profiles retrieved!');
        return;
      }
      TestHelper::assertArrayHasKeys($this, $resp['profiles'][0], $refProfilesKeys);
      TestHelper::assertDateObj($this, $resp['profiles'][0]['date_creation']);
      TestHelper::assertDateObj($this, $resp['profiles'][0]['date_reception']);
      TestHelper::assertArrayHasKeys($this, $resp['profiles'][0]['source'], $refSourceKeys);

      $this->assertLessThanOrEqual($page, $resp['page'], 'The page is not the same');
      $this->assertLessThanOrEqual($limit, count($resp), 'Too much element');
      foreach ($resp['profiles'] as $profile) {
        $this->assertEquals($seniority, $profile['seniority'], 'Seniority is not always the same');
      }
  }

  public function testGetProfilesMinArgs(): void {
      $api = new Riminder(TestHelper::getSecret());

      $refKeys = array('page', 'maxPage', 'count_profiles', 'profiles');
      $refProfilesKeys = array('profile_id', 'profile_reference', 'name',
        'email', 'seniority', 'date_reception',
        'date_creation', 'source');
      $refSourceKeys = array ('source_id', 'name', 'type');

      $start =  new DateTime('2017-01-02');
      $end =  new DateTime();
      $source_ids = $this->getSomeNotSharedSourceIds($api);

      $args = array(
        $api->Fields->SOURCE_IDS => $source_ids,
        $api->Fields->DATE_START => $start->getTimestamp(),
        'date_end' => $end->getTimestamp(),
        $api->Fields->SORT_BY => $api->Sort_by->RANKING
       );
      $getProfiles = function () use ($api, $args)
       { return $api->profile->getProfiles($args); };

      $resp = TestHelper::useApiFuncWithReportedErr($this, $getProfiles);
      TestHelper::assertArrayHasKeys($this, $resp, $refKeys);
      if (empty($resp['profiles'])) {
        $this->markTestSkipped('No profiles retrieved!');
        return;
      }
      TestHelper::assertArrayHasKeys($this, $resp['profiles'][0], $refProfilesKeys);
      TestHelper::assertDateObj($this, $resp['profiles'][0]['date_creation']);
      TestHelper::assertDateObj($this, $resp['profiles'][0]['date_reception']);
      TestHelper::assertArrayHasKeys($this, $resp['profiles'][0]['source'], $refSourceKeys);
  }

  public function testGetProfilesErrDate(): void {
      $api = new Riminder(TestHelper::getSecret());

      $refKeys = array('page', 'maxPage', 'count_profiles', 'profiles');
      $refProfilesKeys = array('profile_id', 'profile_reference', 'name',
        'email', 'seniority', 'date_reception',
        'date_creation', 'source');
      $refSourceKeys = array ('source_id', 'name', 'type');

      $start = new DateTime('2017-01-02');
      $end = 'zap';
      $source_ids = $this->getSomeNotSharedSourceIds($api);

      $args = array(
        $api->Fields->SOURCE_IDS => $source_ids,
        $api->Fields->DATE_START => $start->getTimestamp(),
        'date_end' => $end,
       $api->Fields->SORT_BY => $api->Sort_by->RANKING
       );
      $getProfiles = function () use ($api, $args)
       { return $api->profile->getProfiles($args); };

      $resp = TestHelper::useApiFuncWithExpectedErr($this, $getProfiles, 'RiminderApiArgumentException');
  }

  public function testGetProfilesDateTime(): void {
      $api = new Riminder(TestHelper::getSecret());

      $refKeys = array('page', 'maxPage', 'count_profiles', 'profiles');
      $refProfilesKeys = array('profile_id', 'profile_reference', 'name',
        'email', 'seniority', 'date_reception',
        'date_creation', 'source');
      $refSourceKeys = array ('source_id', 'name', 'type');

      $start =  new DateTime('2017-01-02');
      $end =  new DateTime();
      $source_ids = $this->getSomeNotSharedSourceIds($api);

      $args = array(
        $api->Fields->SOURCE_IDS => $source_ids,
        $api->Fields->DATE_START => $start,
        'date_end' => $end,
        $api->Fields->SORT_BY => $api->Sort_by->RANKING
       );
      $getProfiles = function () use ($api, $args)
       { return $api->profile->getProfiles($args); };

      $resp = TestHelper::useApiFuncWithReportedErr($this, $getProfiles);
      TestHelper::assertArrayHasKeys($this, $resp, $refKeys);
      if (empty($resp['profiles'])) {
        $this->markTestSkipped('No profiles retrieved!');
        return;
      }
      TestHelper::assertArrayHasKeys($this, $resp['profiles'][0], $refProfilesKeys);
      TestHelper::assertDateObj($this, $resp['profiles'][0]['date_creation']);
      TestHelper::assertDateObj($this, $resp['profiles'][0]['date_reception']);
      TestHelper::assertArrayHasKeys($this, $resp['profiles'][0]['source'], $refSourceKeys);
  }



  public function testGet(): void {
      $api = new Riminder(TestHelper::getSecret());
      $refKeys = array('profile_id',
        'profile_reference',
        'name',
        'email',
        'phone',
        'address',
        'source_id',
        'date_reception',
        'date_creation'
        );
      $profile_ids = $this->getSomeProfileIdsPair($api);

      $profileGet = function ($profile_id, $source_id) use ($api)
        { return $api->profile->get($profile_id, $source_id); };
      $resp = $this->useApiFuncWithValidProfile($profile_ids, $profileGet);
      if (empty($resp)) {
        $this->markTestSkipped('No valid profile retrieved!');
        return;
      }

      TestHelper::assertArrayHasKeys($this, $resp, $refKeys);
      TestHelper::assertDateObj($this, $resp['date_creation']);
      TestHelper::assertDateObj($this, $resp['date_reception']);
  }

  public function testGetWithInvalidProfileSourceId(): void {
      $api = new Riminder(TestHelper::getSecret());
      $profile_id = 'zap';
      $source_id = 'red apple corp';
      $getProfile = function () use ($api, $profile_id, $source_id)
        {  return $api->profile->get($profile_id, $source_id); };
      $resp = TestHelper::useApiFuncWithExpectedErr($this, $getProfile, 'RiminderApiResponseException');
  }

  public function testGetDocuments(): void {
      $api = new Riminder(TestHelper::getSecret());
      $refKeys = array('type',
        'file_name',
        'original_file_name',
        'file_size',
        'extension',
        'url',
        'timestamp'
        );
      $profile_ids = $this->getSomeProfileIdsPair($api);
      $profileGetDocument = function ($profile_id, $source_id) use ($api)
        { return $api->profile->getDocuments($profile_id, $source_id); };

      $resp = $this->useApiFuncWithValidProfile($profile_ids, $profileGetDocument);
      if (empty($resp)) {
        $this->markTestSkipped('No valid profile documents retrieved!');
        return;
      }

      TestHelper::assertArrayHasKeys($this, $resp[0], $refKeys);
  }

  public function testGetDocumentsWithInvalidProfileSourceId(): void {
      $api = new Riminder(TestHelper::getSecret());
      $profile_id = 'zap';
      $source_id = 'red apple corp';
      $getProfile = function () use ($api, $profile_id, $source_id) {  return $api->profile->getDocuments($profile_id, $source_id); };
      $resp = TestHelper::useApiFuncWithExpectedErr($this, $getProfile, 'RiminderApiResponseException');
  }

  public function testGetParsing(): void {
      $api = new Riminder(TestHelper::getSecret());
      $refKeys = array('hard_skills',
        'soft_skills',
        'languages',
        'seniority',
        'experiences'
        );
      $refExperiencesKeys = array('title', 'description', 'company', 'location',
        'start_date', 'end_date');
      $profile_ids = $this->getSomeProfileIdsPair($api);
      $profileGetParsing= function ($profile_id, $source_id) use ($api)
        { return $api->profile->getParsing($profile_id, $source_id); };

      $resp = $this->useApiFuncWithValidProfile($profile_ids, $profileGetParsing);
      if (empty($resp)) {
        $this->markTestSkipped('No valid profile documents retrieved!');
        return;
      }

      TestHelper::assertArrayHasKeys($this, $resp, $refKeys);
      if (!empty($resp['experiences'])) {
        TestHelper::assertArrayHasKeys($this, $resp['experiences'][0], $refExperiencesKeys);
      }
  }

  public function testGetParsingWithInvalidProfileSourceId(): void {
      $api = new Riminder(TestHelper::getSecret());
      $profile_id = 'zap';
      $source_id = 'red apple corp';
      $getProfile = function () use ($api, $profile_id, $source_id) {  return $api->profile->getParsing($profile_id, $source_id); };
      $resp = TestHelper::useApiFuncWithExpectedErr($this, $getProfile, 'RiminderApiResponseException');
  }

  public function testGetScoring(): void {
      $api = new Riminder(TestHelper::getSecret());
      $refKeys = array('filter_id',
        'filter_reference',
        'score',
        'rating',
        'stage',
        'filter_id',
        'template'
        );
      $profile_ids = $this->getSomeProfileIdsPair($api);
      $profileGetScoring = function ($profile_id, $source_id) use ($api)
        { return $api->profile->getScoring($profile_id, $source_id); };

      $resp = $this->useApiFuncWithValidProfile($profile_ids, $profileGetScoring);
      if (empty($resp)) {
        $this->markTestSkipped('No valid profile documents retrieved!');
        return;
      }

      TestHelper::assertArrayHasKeys($this, $resp[0], $refKeys);
  }

  public function testGetFiltersWithInvalidScoringSourceId(): void {
      $api = new Riminder(TestHelper::getSecret());
      $profile_id = 'zap';
      $source_id = 'red apple corp';
      $getScoring = function () use ($api, $profile_id, $source_id) {  return $api->profile->getScoring($profile_id, $source_id); };
      $resp = TestHelper::useApiFuncWithExpectedErr($this, $getScoring, 'RiminderApiResponseException');
  }

  public function testadd():void {
      $api = new Riminder(TestHelper::getSecret());
      $refKeys = array('profile_reference', 'file_id', 'file_name', 'file_size', 'extension', 'date_reception');
      $now =  new DateTime();
      $filterType = array('api');
      $filterName = TestHelper::getSourceTestName();

      $source_ids = $this->getSomeNotSharedSourceIds($api, $filterType, $filterName);
      if (empty($source_ids)){
        $this->markTestSkipped('no api sources with this key');
      }
      $source_id = $source_ids[0];
      $file = "./test/test_cv.pdf";
      $profile_ref = strval(rand(0, 99999));
      // $profile_ref = 10;

      $addProfile = function () use ($api, $now, $source_id, $file, $profile_ref)
      { return $api->profile->add($source_id, $file, $profile_ref, $now->getTimestamp()); };
      $resp = TestHelper::useApiFuncWithReportedErr($this, $addProfile);
      if (empty($resp)) {
        $this->fail('No datas retrieved!');
        return;
      }
      TestHelper::assertArrayHasKeys($this, $resp, $refKeys);
      TestHelper::assertDateObj($this, $resp['date_reception']);
  }

  public function testUpdateStage(): void {
    $api = new Riminder(TestHelper::getSecret());
    $stage = "NO";
    $refKeys = array('profile_id', 'profile_reference', 'filter_id', 'filter_reference', 'stage');

    $profile_pair = $this->getValidProfilePairId($api);
    if (empty($profile_pair)) {
      $this->markTestSkipped('No valid profile, abotring test');
    }
    $profile_id = $profile_pair['profile_id'];
    $source_id = $profile_pair['source_id'];
    $getScoring = function () use ($api, $profile_id, $source_id)
      {  return $api->profile->getScoring($profile_id, $source_id); };
    $filters = TestHelper::useApiFuncWithReportedErrAsSkip($this, $getScoring);
    if (empty($filters)) {
      $this->markTestSkipped('No filters retrieved!');
    }
    $filter = $filters[0];
    $stage = $filter['stage'];
    $filter_id = $filter['filter_id'];
    $updateProfile = function () use ($api, $profile_id, $source_id, $filter_id, $stage)
      {  return $api->profile->updateStage($profile_id, $source_id, $filter_id, $stage); };
    $resp = TestHelper::useApiFuncWithReportedErr($this, $updateProfile);
    TestHelper::assertArrayHasKeys($this, $resp, $refKeys);

    $this->assertEquals($profile_id, $resp['profile_id']);
    $this->assertEquals($filter_id, $resp['filter_id']);
    $this->assertEquals($stage, $resp['stage']);
  }


    public function testUpdateRating(): void {
      $api = new Riminder(TestHelper::getSecret());
      $rating = 0;
      $refKeys = array('profile_id', 'profile_reference', 'filter_id', 'filter_reference', 'rating');

      $profile_pair = $this->getValidProfilePairId($api);
      if (empty($profile_pair)) {
        $this->markTestSkipped('No valid profile, abotring test');
      }
      $profile_id = $profile_pair['profile_id'];
      $source_id = $profile_pair['source_id'];
      $getScoring = function () use ($api, $profile_id, $source_id)
        {  return $api->profile->getScoring($profile_id, $source_id); };
      $filters = TestHelper::useApiFuncWithReportedErrAsSkip($this, $getScoring);
      if (empty($filters)) {
        $this->markTestSkipped('No filters retrieved!');
      }
      $filter = $filters[0];
      $rating = $filter['rating'];
      $filter_id = $filter['filter_id'];
      $updateProfile = function () use ($api, $profile_id, $source_id, $filter_id, $rating)
        {  return $api->profile->updateRating($profile_id, $source_id, $filter_id, $rating); };
      $resp = TestHelper::useApiFuncWithReportedErr($this, $updateProfile);
      TestHelper::assertArrayHasKeys($this, $resp, $refKeys);

      $this->assertEquals($profile_id, $resp['profile_id']);
      $this->assertEquals($filter_id, $resp['filter_id']);
      $this->assertEquals($rating, $resp['rating']);
    }

}
 ?>
