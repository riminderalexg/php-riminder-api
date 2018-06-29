<?php

    class RiminderSeniority
    {
      const SENIOR = 'senior';
      const JUNIOR = 'junior';
      const ALL    = 'all';
    }

    class RiminderStage
    {
      const ALL   = null;
      const NEW   = 'new';
      const YES   = 'yes';
      const LATER = 'later';
      const NO    = 'no';
    }

    class RiminderSortBy
    {
      const RECEPTION = 'reception';
      const RANKING   = 'ranking';
    }

    class RiminderOrderBy
    {
      const DESC = 'desc';
      const ASC  = 'asc';
    }

    class RiminderField
    {
      const SOURCE_IDS       = 'source_ids';
      const SENIORITY        = 'seniority';
      const FILTER_ID        = 'filter_id';
      const FILTER_REFERENCE = 'filter_reference';
      const STAGE            = 'stage';
      const RATING           = 'rating';
      const DATE_START       = 'date_start';
      const DATE_END         = 'date_end';
      const PAGE             = 'page';
      const LIMIT            = 'limit';
      const SORT_BY          = 'sort_by';
      const ORDER_BY         = 'order_by';
    }

    class RiminderTrainingMetaData
    {
      const FILTER_ID = 'filter_id';
      const FILTER_REFERENCE = 'filter_reference';
      const STAGE = 'stage';
      const STAGE_TIMESTAMP = 'stage_timestamp';
      const RATING = 'rating';
      const RATING_TIMESTAMP = 'rating_timestamp';
    }

    class RiminderEvents
    {
      const PROFILE_PARSE_SUCCESS = 'profile.parse.success';
      const PROFILE_PARSE_ERROR = 'profile.parse.error';
      const PROFILE_SCORE_SUCCESS = 'profile.score.success';
      const PROFILE_SCORE_ERROR = 'profile.score.error';
      const FILTER_TRAIN_SUCCESS = 'filter.train.success';
      const FILTER_TRAIN_ERROR = 'filter.train.error';
      const FILTER_TRAIN_START = 'filter.train.start';
      const FILTER_SCORE_SUCCESS = 'filter.score.success';
      const FILTER_SCORE_ERROR = 'filter.score.error';
      const FILTER_SCORE_START = 'filter.score.start';
    }
  /**
   *
   */


 ?>
