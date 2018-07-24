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

  private function filterEmptyReference($profile_idPairs) {
    $res = [];

    foreach ($profile_idPairs as $profile_idPair) {
      if (!empty($profile_idPair['profile_reference'])) {
        $res[] = $profile_idPair;
      }
    }
    return $res;
  }

  private function useApiFuncWithValidProfile($profile_ids, $profileFunc) {
    foreach ($profile_ids as $profile_idPair) {
      $err;
      $profile_id = $profile_idPair['profile_id'];
      $source_id = $profile_idPair['source_id'];
      // profile_id as a string because profileFunc call already transform argument to profileIdent Object
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

  private function useApiFuncWithValidProfile_reference($profile_ids, $profileFunc) {
    foreach ($profile_ids as $profile_idPair) {
      $err;
      $profile_ref = $profile_idPair['profile_reference'];
      $source_id = $profile_idPair['source_id'];
      // profile_reference as a string because profileFunc call already transform argument to profileIdent Object
      $getProfile = function () use ($profileFunc, $profile_ref, $source_id) {  return $profileFunc($profile_ref, $source_id); };
      $resp = TestHelper::useApiFuncWithIgnoredErr($this, $getProfile);
      if (!empty($resp)) {
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


  private function getValidProfilePairId($api, $check_for_ref=false) {
    $profile_ids = $this->getSomeProfileIdsPair($api);
    if ($check_for_ref){
      $profile_ids = $this->filterEmptyReference($profile_ids);
    }
    foreach ($profile_ids as $profile_idPair) {
      $profile_id = $profile_idPair['profile_id'];
      $source_id = $profile_idPair['source_id'];
      $profileGet = function () use ($api, $profile_id, $source_id)
        { return $api->profile->get(new ProfileID($profile_id), $source_id); };
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

    $getSources = function () use ($api) { return $api->source->list(); };
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

  private function getSomeFilterIdsPair($api, $n_ids = 5) {
    $filter_ids = array();

    $getFilters = function () use ($api) { return $api->filter->list(); };
    $filters = TestHelper::useApiFuncWithReportedErrAsSkip($this, $getFilters);
    if (empty($filters)) {
      $this->markTestSkipped('No filters retrieved!');
      return;
    }
    foreach ($filters as $filter) {

      $filter_ids[] = array('id' => $filter['filter_id'],
        'reference' => $filter['filter_reference']);
      if (count($filter_ids) > $n_ids) {
        break;
      }
    }
    return $filter_ids;
  }

  private function getSomeProfileIdsPair($api) {

    $profile_ids = array();
    $source_ids = $this->getSomeNotSharedSourceIds($api, ['api'], TestHelper::getSourceTestName());
    $start =  new DateTime('2017-01-02');
    $end =  new DateTime();

    $args = array(
      RiminderField::SOURCE_IDS => $source_ids,
      RiminderField::DATE_START => $start->getTimestamp(),
      'date_end' => $end,
      'limit'    => 100,
      RiminderField::SORT_BY => RiminderSortBy::RANKING
     );
    $getProfiles = function () use ($api, $args)
     { return $api->profile->list($args); };

      $profiles = TestHelper::useApiFuncWithReportedErr($this, $getProfiles);
      foreach ($profiles['profiles'] as $profile) {
        $source = $profile['source'];
        $profile_ids[] = array('profile_id' => $profile['profile_id'],
                                'source_id' => $source['source_id'],
                                'profile_reference' => $profile['profile_reference']);
    }

    return $profile_ids;
  }

  private function getAfilterId($api) {
    $getfilters = function () use ($api) { return $api->filter->list(); };

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
        RiminderField::SOURCE_IDS => $source_ids,
        RiminderField::DATE_START => $start->getTimestamp(),
        'date_end' => $end,
        RiminderField::SENIORITY => $seniority,
        RiminderField::FILTER_ID => $filter_id,
        RiminderField::STAGE => $stage,
        'page' => $page,
        'limit' => $limit,
        'sort_by' => $sort_by,
        'order_by' => $order_by
       );
      $getProfiles = function () use ($api, $args)
       { return $api->profile->list($args); };

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

  public function testGetProfilesFullArgs_filterID(): void {
      $api = new Riminder(TestHelper::getSecret());

      $refKeys = array('page', 'maxPage', 'count_profiles', 'profiles');
      $refProfilesKeys = array('profile_id', 'profile_reference', 'name',
        'email', 'seniority', 'date_reception',
        'date_creation', 'source', 'score', 'rating', 'stage');
      $refSourceKeys = array ('source_id', 'name', 'type');

      $start =  new DateTime('2017-01-02');
      $end =  new DateTime();
      $seniority = "senior";
      $source_ids = $this->getSomeNotSharedSourceIds($api);
      $filter_id = $this->getSomeFilterIdsPair($api)[0]['id'];
      $stage = "NEW";
      $page = 2;
      $limit = 10;
      $sort_by = "ranking";
      $order_by = 'asc';

      $args = array(
        RiminderField::SOURCE_IDS => $source_ids,
        RiminderField::DATE_START => $start->getTimestamp(),
        'date_end' => $end,
        RiminderField::SENIORITY => $seniority,
        RiminderField::FILTER_ID => $filter_id,
        RiminderField::STAGE => $stage,
        'page' => $page,
        'limit' => $limit,
        'sort_by' => $sort_by,
        'order_by' => $order_by
       );
      $getProfiles = function () use ($api, $args)
       { return $api->profile->list($args); };

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

  public function testGetProfilesFullArgs_filterReference(): void {
      $api = new Riminder(TestHelper::getSecret());

      $refKeys = array('page', 'maxPage', 'count_profiles', 'profiles');
      $refProfilesKeys = array('profile_id', 'profile_reference', 'name',
        'email', 'seniority', 'date_reception',
        'date_creation', 'source', 'score', 'rating', 'stage');
      $refSourceKeys = array ('source_id', 'name', 'type');

      $start =  new DateTime('2017-01-02');
      $end =  new DateTime();
      $seniority = "senior";
      $source_ids = $this->getSomeNotSharedSourceIds($api);
      $filter_reference = $this->getSomeFilterIdsPair($api)[0]['reference'];
      $stage = "NEW";
      $page = 2;
      $limit = 10;
      $sort_by = "ranking";
      $order_by = 'asc';

      $args = array(
        RiminderField::SOURCE_IDS => $source_ids,
        RiminderField::DATE_START => $start->getTimestamp(),
        'date_end' => $end,
        RiminderField::SENIORITY => $seniority,
        RiminderField::FILTER_REFERENCE => $filter_reference,
        RiminderField::STAGE => $stage,
        'page' => $page,
        'limit' => $limit,
        'sort_by' => $sort_by,
        'order_by' => $order_by
       );
      $getProfiles = function () use ($api, $args)
       { return $api->profile->list($args); };

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
        RiminderField::SOURCE_IDS => $source_ids,
        RiminderField::DATE_START => $start->getTimestamp(),
        'date_end' => $end->getTimestamp(),
        RiminderField::SORT_BY => RiminderSortBy::RANKING
       );
      $getProfiles = function () use ($api, $args)
       { return $api->profile->list($args); };

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
        RiminderField::SOURCE_IDS => $source_ids,
        RiminderField::DATE_START => $start->getTimestamp(),
        'date_end' => $end,
       RiminderField::SORT_BY => RiminderSortBy::RANKING
       );
      $getProfiles = function () use ($api, $args)
       { return $api->profile->list($args); };

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
        RiminderField::SOURCE_IDS => $source_ids,
        RiminderField::DATE_START => $start,
        'date_end' => $end,
        RiminderField::SORT_BY => RiminderSortBy::RANKING
       );
      $getProfiles = function () use ($api, $args)
       { return $api->profile->list($args); };

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



  public function testGetProfile(): void {
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
        { return $api->profile->get(new ProfileID($profile_id), $source_id); };
      $resp = $this->useApiFuncWithValidProfile($profile_ids, $profileGet);
      if (empty($resp)) {
        $this->markTestSkipped('No valid profile retrieved!');
        return;
      }

      TestHelper::assertArrayHasKeys($this, $resp, $refKeys);
      TestHelper::assertDateObj($this, $resp['date_creation']);
      TestHelper::assertDateObj($this, $resp['date_reception']);
  }

  public function testGetProfile_reference(): void {
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
      $profile_ids = $this->filterEmptyReference($profile_ids);
      if (empty($profile_ids)) {
        $this->markTestSkipped('No profile with profile reference!');
      }

      $profileGet = function ($profile_ref, $source_id) use ($api)
        { return $api->profile->get(new ProfileReference($profile_ref), $source_id); };
      $resp = $this->useApiFuncWithValidProfile_reference($profile_ids, $profileGet);
      if (empty($resp)) {
        $this->markTestSkipped('No valid profile retrieved!');
        return;
      }

      TestHelper::assertArrayHasKeys($this, $resp, $refKeys);
      TestHelper::assertDateObj($this, $resp['date_creation']);
      TestHelper::assertDateObj($this, $resp['date_reception']);
  }

  public function testGetProfileWithInvalidProfileSourceId(): void {
      $api = new Riminder(TestHelper::getSecret());
      $profile_id = 'zap';
      $source_id = 'red apple corp';
      $getProfile = function () use ($api, $profile_id, $source_id)
        {  return $api->profile->get(new ProfileID($profile_id), $source_id); };
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
        { return $api->profile->document->list(new ProfileID($profile_id), $source_id); };

      $resp = $this->useApiFuncWithValidProfile($profile_ids, $profileGetDocument);
      if (empty($resp)) {
        $this->markTestSkipped('No valid profile documents retrieved!');
        return;
      }

      TestHelper::assertArrayHasKeys($this, $resp[0], $refKeys);
  }

  public function testGetDocuments_reference(): void {
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
      $profile_ids = $this->filterEmptyReference($profile_ids);
      if (empty($profile_ids)) {
        $this->markTestSkipped('No profile with profile reference!');
      }
      $profileGetDocument = function ($profile_ref, $source_id) use ($api)
        { return $api->profile->document->list(new ProfileReference($profile_ref), $source_id); };

      $resp = $this->useApiFuncWithValidProfile_reference($profile_ids, $profileGetDocument);
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
      $getProfile = function () use ($api, $profile_id, $source_id) {  return $api->profile->document->list(new ProfileID($profile_id), $source_id); };
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
        { return $api->profile->parsing->get(new ProfileID($profile_id), $source_id); };

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

  public function testGetParsing_reference(): void {
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
      $profile_ids = $this->filterEmptyReference($profile_ids);
      if (empty($profile_ids)) {
        $this->markTestSkipped('No profile with profile reference!');
      }
      $profileGetParsing= function ($profile_ref, $source_id) use ($api)
        { return $api->profile->parsing->get(new ProfileReference($profile_ref), $source_id); };

      $resp = $this->useApiFuncWithValidProfile_reference($profile_ids, $profileGetParsing);
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
      $getProfile = function () use ($api, $profile_id, $source_id) {  return $api->profile->parsing->get(new ProfileID($profile_id), $source_id); };
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
        { return $api->profile->scoring->list(new ProfileID($profile_id), $source_id); };

      $resp = $this->useApiFuncWithValidProfile($profile_ids, $profileGetScoring);
      if (empty($resp)) {
        $this->markTestSkipped('No valid profile documents retrieved!');
        return;
      }

      TestHelper::assertArrayHasKeys($this, $resp[0], $refKeys);
  }

  public function testGetScoring_reference(): void {
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
      $profile_ids = $this->filterEmptyReference($profile_ids);
      if (empty($profile_ids)) {
        $this->markTestSkipped('No profile with profile reference!');
      }
      $profileGetScoring = function ($profile_ref, $source_id) use ($api)
        { return $api->profile->scoring->list(new ProfileReference($profile_ref), $source_id); };

      $resp = $this->useApiFuncWithValidProfile_reference($profile_ids, $profileGetScoring);
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
      $getScoring = function () use ($api, $profile_id, $source_id) {  return $api->profile->scoring->list(new ProfileID($profile_id), $source_id); };
      $resp = TestHelper::useApiFuncWithExpectedErr($this, $getScoring, 'RiminderApiResponseException');
  }

  public function testPostProfile():void {
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
      $file = "./assets/test_cv.pdf";
      $profile_ref = strval(rand(0, 99999));

      $addProfile = function () use ($api, $now, $source_id, $file, $profile_ref)
      { return $api->profile->add($source_id, $file, $profile_ref, $now->getTimestamp()); };
      $resp = TestHelper::useApiFuncWithReportedErr($this, $addProfile);
      if (empty($resp)) {
        $this->fail('No datas retrieved!');
        return;
      }
      TestHelper::assertArrayHasKeys($this, $resp, $refKeys);
      TestHelper::assertDateObj($this, $resp['date_reception']);
      $this->assertEquals($resp['profile_reference'], $profile_ref);
  }

  public function testPostProfile_metadata():void {
      $api = new Riminder(TestHelper::getSecret());
      $refKeys = array('profile_reference', 'file_id', 'file_name', 'file_size', 'extension', 'date_reception');
      $now =  new DateTime();
      $filterType = array('api');
      $filterName = TestHelper::getSourceTestName();

      $source_ids = $this->getSomeNotSharedSourceIds($api, $filterType, $filterName);
      if (empty($source_ids)){
        $this->markTestSkipped('no api sources with this key');
      }
      $filters = $api->filter->list();
      $source_id = $source_ids[0];
      $file = "./assets/test_cv7.jpg";
      $profile_ref = strval(rand(0, 99999));
      $profile_metadata = [[
        'filter_reference' => $filters[0]['filter_reference'],
        'stage_timestamp' => $now->getTimestamp(),
        'stage' => 'no',
        'rating_timestamp' => $now->getTimestamp(),
        'rating' => 2
      ]];

      $addProfile = function () use ($api, $now, $source_id, $file, $profile_ref, $profile_metadata)
      { return $api->profile->add($source_id, $file, $profile_ref, $now->getTimestamp(), $profile_metadata); };
      $resp = TestHelper::useApiFuncWithReportedErr($this, $addProfile);
      if (empty($resp)) {
        $this->fail('No datas retrieved!');
        return;
      }
      TestHelper::assertArrayHasKeys($this, $resp, $refKeys);
      TestHelper::assertDateObj($this, $resp['date_reception']);
      $this->assertEquals($resp['profile_reference'], $profile_ref);
  }


  public function testPostProfiles():void {
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
      $file = "./assets";
      $profile_ref = strval(rand(0, 99999));

      $addProfile = function () use ($api, $now, $source_id, $file, $profile_ref)
      { return $api->profile->addList($source_id, $file, true, $now->getTimestamp()); };
      $resp = TestHelper::useApiFuncWithReportedErr($this, $addProfile);
      if (empty($resp)) {
        $this->fail('No file sended!');
        return;
      }
      if (count($resp) < 2) {
        $this->fail('Some files has not been sended!');
        return;
      }
      foreach ($resp as $oner) {
        TestHelper::assertArrayHasKeys($this, $oner, $refKeys);
        TestHelper::assertDateObj($this, $oner['date_reception']);
      }
  }

  private function findFilterWithRef($filters)
  {
    foreach ($filters as $filter) {
      if (!empty($filter['filter_reference'])) {
        return $filter;
      }
    }
    return None;
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
      {  return $api->profile->scoring->list(new ProfileID($profile_id), $source_id); };
    $filters = TestHelper::useApiFuncWithReportedErrAsSkip($this, $getScoring);
    if (empty($filters)) {
      $this->markTestSkipped('No filters retrieved!');
    }
    $filter = $filters[0];
    $stage = $filter['stage'];
    $filter_id = $filter['filter_id'];
    $updateProfile = function () use ($api, $profile_id, $source_id, $filter_id, $stage)
      {  return $api->profile->stage->set(new ProfileID($profile_id), $source_id, new FilterID($filter_id), $stage); };
    $resp = TestHelper::useApiFuncWithReportedErr($this, $updateProfile);
    TestHelper::assertArrayHasKeys($this, $resp, $refKeys);

    $this->assertEquals($profile_id, $resp['profile_id']);
    $this->assertEquals($filter_id, $resp['filter_id']);
    $this->assertEquals($stage, $resp['stage']);
  }

  public function testUpdateStage_reference(): void {
    $api = new Riminder(TestHelper::getSecret());
    $stage = "NO";
    $refKeys = array('profile_id', 'profile_reference', 'filter_id', 'filter_reference', 'stage');

    $profile_pair = $this->getValidProfilePairId($api, true);
    if (empty($profile_pair)) {
      $this->markTestSkipped('No valid profile, abotring test');
    }
    $profile_id = $profile_pair['profile_id'];
    $profile_ref = $profile_pair['profile_reference'];
    $source_id = $profile_pair['source_id'];
    $getScoring = function () use ($api, $profile_id, $source_id)
      {  return $api->profile->scoring->list(new ProfileID($profile_id), $source_id); };
    $filters = TestHelper::useApiFuncWithReportedErrAsSkip($this, $getScoring);
    if (empty($filters)) {
      $this->markTestSkipped('No filters retrieved!');
    }
    $filter = $this->findFilterWithRef($filters);
    $stage = $filter['stage'];
    $filter_ref = $filter['filter_reference'];
    $filter_id = $filter['filter_id'];
    $updateProfile = function () use ($api, $profile_ref, $source_id, $filter_ref, $stage)
      {  return $api->profile->stage->set(new ProfileReference($profile_ref), $source_id, new FilterReference($filter_ref), $stage); };
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
        {  return $api->profile->scoring->list(new ProfileID($profile_id), $source_id); };
      $filters = TestHelper::useApiFuncWithReportedErrAsSkip($this, $getScoring);
      if (empty($filters)) {
        $this->markTestSkipped('No filters retrieved!');
      }
      $filter = $filters[0];
      $rating = $filter['rating'];
      $filter_id = $filter['filter_id'];
      $updateProfile = function () use ($api, $profile_id, $source_id, $filter_id, $rating)
        {  return $api->profile->rating->set(new ProfileID($profile_id), $source_id, new FilterID($filter_id), $rating); };
      $resp = TestHelper::useApiFuncWithReportedErr($this, $updateProfile);
      TestHelper::assertArrayHasKeys($this, $resp, $refKeys);

      $this->assertEquals($profile_id, $resp['profile_id']);
      $this->assertEquals($filter_id, $resp['filter_id']);
      $this->assertEquals($rating, $resp['rating']);
    }

    public function testUpdateRating_reference(): void {
      $api = new Riminder(TestHelper::getSecret());
      $rating = 0;
      $refKeys = array('profile_id', 'profile_reference', 'filter_id', 'filter_reference', 'rating');

      $profile_pair = $this->getValidProfilePairId($api, true);
      if (empty($profile_pair)) {
        $this->markTestSkipped('No valid profile, abotring test');
      }
      $profile_id = $profile_pair['profile_id'];
      $source_id = $profile_pair['source_id'];
      $profile_ref = $profile_pair['profile_reference'];
      $getScoring = function () use ($api, $profile_id, $source_id)
        {  return $api->profile->scoring->list(new ProfileID($profile_id), $source_id); };
      $filters = TestHelper::useApiFuncWithReportedErrAsSkip($this, $getScoring);
      if (empty($filters)) {
        $this->markTestSkipped('No filters retrieved!');
      }
      $filter = $this->findFilterWithRef($filters);
      $rating = $filter['rating'];
      $filter_id = $filter['filter_id'];
      $filter_ref = $filter['filter_reference'];
      $updateProfile = function () use ($api, $profile_ref, $source_id, $filter_ref, $rating)
        {  return $api->profile->rating->set(new ProfileReference($profile_ref), $source_id, new FilterReference($filter_ref), $rating); };
      $resp = TestHelper::useApiFuncWithReportedErr($this, $updateProfile);
      TestHelper::assertArrayHasKeys($this, $resp, $refKeys);

      $this->assertEquals($profile_id, $resp['profile_id']);
      $this->assertEquals($filter_id, $resp['filter_id']);
      $this->assertEquals($rating, $resp['rating']);
    }

  private function getDataForProfileDataTest() {
    $metadata = [
            [
              "filter_reference"  => "reference0",
              "stage"             => null,
              "stage_timestamp"   => null,
              "rating"            => 2,
              "rating_timestamp"  => 1530607434
            ],
            [
              "filter_reference" => "reference1",
              "stage"            => null,
              "stage_timestamp"  => null,
              "rating"           => 2,
              "rating_timestamp" => 1530607434
            ]
          ];

        $profileData = [
            "name" => "test superman",
            "email" => "someone@someonelse.com",
            "address" => "1 rue de somexhereelse",
            "experiences" => [
              [
                "start" => "15/02/2018",
                "end" => "1/06/2018",
                "title" => "Advisor",
                "company" => "red apple corp",
                "location" => "Paris",
                "description" => "A ne pas confondre avec cyborg superman"
              ]
            ],
            "educations" => [
              [
                "start" => "2000",
                "end" => "2018",
                "title" => "Diplome d'ingénieur",
                "school" => "UTT",
                "description" => "Management des systèmes d'information",
                "location" => "Mars"
              ]
            ],
            "skills" => [
              "manual skill",
              "Creative spirit",
              "Writing skills",
              "World domination",
              "Project management",
              "French",
              "Italian",
              "Korean",
              "English",
              "Accounting",
              "Human resources"
            ]
          ];
        $res = ['data' => $profileData, 'meta' => $metadata];
        return $res;
  }

  public function testJsonAdd(): void {

        $api = new Riminder(TestHelper::getSecret());
        $profile_ref = strval(rand(0, 99999));
        $refKeys = array('profile_json', 'training_metadata');
        $now =  new DateTime();
        $filterType = array('api');
        $filterName = TestHelper::getSourceTestName();
        $datas = $this->getDataForProfileDataTest();
        $metadata = $datas['meta'];
        $profileData = $datas['data'];

        $source_ids = $this->getSomeNotSharedSourceIds($api, $filterType, $filterName);
        if (empty($source_ids)){
          $this->markTestSkipped('no api sources with this key');
        }
        $source_id = $source_ids[0];
        $addData = function () use ($api, $profileData, $metadata, $now, $profile_ref, $source_id)
          {  return $api->profile->json->add($source_id, $profileData, $metadata, $profile_ref, $now); };

        $resp = TestHelper::useApiFuncWithReportedErr($this, $addData);
        TestHelper::assertArrayHasKeys($this, $resp, $refKeys);
  }

  public function testJsonAdd_bad_metadata(): void {

        $api = new Riminder(TestHelper::getSecret());
        $profile_ref = strval(rand(0, 99999));
        $refKeys = array('profile_json', 'training_metadata');
        $now =  new DateTime();
        $filterType = array('api');
        $filterName = TestHelper::getSourceTestName();
        $datas = $this->getDataForProfileDataTest();
        $metadata = $datas['meta'];
        unset($metadata[0]['filter_reference']);
        $profileData = $datas['data'];

        $source_ids = $this->getSomeNotSharedSourceIds($api, $filterType, $filterName);
        if (empty($source_ids)){
          $this->markTestSkipped('no api sources with this key');
        }
        $source_id = $source_ids[0];
        $addData = function () use ($api, $profileData, $metadata, $now, $profile_ref, $source_id)
          {  return $api->profile->json->add($source_id, $profileData, $metadata, $profile_ref, $now); };

        TestHelper::useApiFuncWithExpectedErr($this, $addData, 'RiminderApiArgumentException');
  }

  public function testJsonCheck(): void {

        $api = new Riminder(TestHelper::getSecret());
        $profile_ref = strval(rand(0, 99999));
        $refKeys = array('profile_json', 'training_metadata');
        $datas = $this->getDataForProfileDataTest();
        $metadata = $datas['meta'];
        $profileData = $datas['data'];

        $addData = function () use ($api, $profileData, $metadata, $profile_ref)
          {  return $api->profile->json->check($profileData, $metadata, $profile_ref); };

        $resp = TestHelper::useApiFuncWithReportedErr($this, $addData);
        TestHelper::assertArrayHasKeys($this, $resp, $refKeys);
  }

}
 ?>
