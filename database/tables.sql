CREATE ALGORITHM=TEMPTABLE DEFINER=`egwk3`@`%` SQL SECURITY DEFINER VIEW `egwk3_api_hymnal_book`  AS  select `egwk3_hymnal_book`.`title` AS `title`,`egwk3_hymnal_book`.`publisher` AS `publisher`,`egwk3_hymnal_book`.`year` AS `year`,`egwk3_hymnal_book`.`lang` AS `lang`,`egwk3_hymnal_book`.`slug` AS `slug`,`egwk3_hymnal_book`.`description` AS `description`,concat_ws('/','','hymnal',`egwk3_hymnal_book`.`slug`) AS `hymnal_uri` from `egwk3_hymnal_book` order by `egwk3_hymnal_book`.`id` ;
CREATE ALGORITHM=TEMPTABLE DEFINER=`egwk3`@`%` SQL SECURITY DEFINER VIEW `egwk3_api_hymnal_song`  AS  select `egwk3_hymnal_book`.`slug` AS `slug`,`egwk3_hymnal_song`.`hymn_no` AS `hymn_no`,`egwk3_hymnal_song`.`title` AS `title`,`egwk3_hymnal_song`.`composer` AS `composer`,`egwk3_hymnal_song`.`poet` AS `poet`,`egwk3_hymnal_song`.`translation` AS `translation`,`egwk3_hymnal_song`.`arranger` AS `arranger`,`egwk3_hymnal_song`.`tune` AS `tune`,`egwk3_hymnal_song`.`tune_year` AS `tune_year`,`egwk3_hymnal_song`.`lyrics_year` AS `lyrics_year`,`egwk3_hymnal_song`.`scripture` AS `scripture`,`egwk3_hymnal_song`.`topic` AS `topic`,`egwk3_hymnal_song`.`info` AS `info`,`egwk3_hymnal_song`.`copyright` AS `copyright`,`egwk3_hymnal_song`.`lily_score` AS `lily_score`,concat_ws('/','','hymn',`egwk3_hymnal_book`.`slug`,`egwk3_hymnal_song`.`hymn_no`) AS `hymn_uri` from (`egwk3_hymnal_song` join `egwk3_hymnal_book` on(`egwk3_hymnal_book`.`id` = `egwk3_hymnal_song`.`hymnal_id`)) order by `egwk3_hymnal_book`.`id`,cast(`egwk3_hymnal_song`.`hymn_no` as unsigned) ;
CREATE ALGORITHM=TEMPTABLE DEFINER=`egwk3`@`%` SQL SECURITY DEFINER VIEW `egwk3_api_hymnal_verse`  AS  select `egwk3_hymnal_book`.`slug` AS `slug`,`egwk3_hymnal_verse`.`hymn_no` AS `hymn_no`,`egwk3_hymnal_verse`.`verse_no` AS `verse_no`,`egwk3_hymnal_verse`.`content` AS `content`,`egwk3_hymnal_verse`.`lily_hyphenated` AS `lily_hyphenated`,`egwk3_hymnal_verse`.`note` AS `note`,concat_ws('/','','hymn',`egwk3_hymnal_book`.`slug`,`egwk3_hymnal_verse`.`hymn_no`,`egwk3_hymnal_verse`.`verse_no`) AS `verse_uri` from (`egwk3_hymnal_verse` join `egwk3_hymnal_book` on(`egwk3_hymnal_book`.`id` = `egwk3_hymnal_verse`.`hymnal_id`)) ;
CREATE ALGORITHM=TEMPTABLE DEFINER=`egwk3`@`%` SQL SECURITY DEFINER VIEW `egwk3_api_hymnal_synch`  AS  select `u`.`source_slug` AS `source_slug`,`u`.`source_lang` AS `source_lang`,`u`.`source_hymn_no` AS `source_hymn_no`,`u`.`slug` AS `slug`,`u`.`lang` AS `lang`,`u`.`hymn_no` AS `hymn_no`,`u`.`title` AS `title`,`u`.`hymn_uri` AS `hymn_uri` from (select `h1`.`slug` AS `source_slug`,`h1`.`lang` AS `source_lang`,`s1`.`hymn1_no` AS `source_hymn_no`,`h2`.`slug` AS `slug`,`h2`.`lang` AS `lang`,`s1`.`hymn2_no` AS `hymn_no`,`s`.`title` AS `title`,concat_ws('/','','hymn',`h2`.`slug`,`s1`.`hymn2_no`) AS `hymn_uri` from (((`egwk3`.`egwk3_hymnal_synch` `s1` join `egwk3`.`egwk3_hymnal_book` `h1` on(`s1`.`hymnal1_id` = `h1`.`id`)) join `egwk3`.`egwk3_hymnal_book` `h2` on(`s1`.`hymnal2_id` = `h2`.`id`)) join `egwk3`.`egwk3_hymnal_song` `s` on(`s1`.`hymnal2_id` = `s`.`hymnal_id` and `s1`.`hymn2_no` = `s`.`hymn_no`)) union select `h2`.`slug` AS `source_slug`,`h2`.`lang` AS `source_lang`,`s2`.`hymn2_no` AS `source_hymn_no`,`h1`.`slug` AS `slug`,`h1`.`lang` AS `lang`,`s2`.`hymn1_no` AS `hymn_no`,`s`.`title` AS `title`,concat_ws('/','','hymn',`h1`.`slug`,`s2`.`hymn1_no`) AS `hymn_uri` from (((`egwk3`.`egwk3_hymnal_synch` `s2` join `egwk3`.`egwk3_hymnal_book` `h1` on(`s2`.`hymnal1_id` = `h1`.`id`)) join `egwk3`.`egwk3_hymnal_book` `h2` on(`s2`.`hymnal2_id` = `h2`.`id`)) join `egwk3`.`egwk3_hymnal_song` `s` on(`s2`.`hymnal1_id` = `s`.`hymnal_id` and `s2`.`hymn1_no` = `s`.`hymn_no`))) `u` order by `u`.`source_lang`,cast(`u`.`source_hymn_no` as unsigned)


CREATE ALGORITHM=TEMPTABLE DEFINER=`egwk3`@`%` SQL SECURITY DEFINER VIEW `egwk3_api_toc_view`  AS  select `egwk3_original`.`para_id` AS `para_id`,`egwk3_original`.`id_prev` AS `id_prev`,`egwk3_original`.`id_next` AS `id_next`,`egwk3_original`.`refcode_1` AS `refcode_1`,`egwk3_original`.`refcode_2` AS `refcode_2`,`egwk3_original`.`refcode_3` AS `refcode_3`,`egwk3_original`.`refcode_4` AS `refcode_4`,`egwk3_original`.`refcode_short` AS `refcode_short`,`egwk3_original`.`refcode_long` AS `refcode_long`,`egwk3_original`.`element_type` AS `element_type`,`egwk3_original`.`element_subtype` AS `element_subtype`,`egwk3_original`.`content` AS `content`,`egwk3_original`.`puborder` AS `puborder`,`egwk3_original`.`parent_1` AS `parent_1`,`egwk3_original`.`parent_2` AS `parent_2`,`egwk3_original`.`parent_3` AS `parent_3`,`egwk3_original`.`parent_4` AS `parent_4`,`egwk3_original`.`parent_5` AS `parent_5`,`egwk3_original`.`parent_6` AS `parent_6`,`egwk3_translation`.`lang` AS `lang`,`egwk3_translation`.`publisher` AS `publisher`,`egwk3_translation`.`year` AS `year`,`egwk3_translation`.`no` AS `no`,`egwk3_translation`.`content` AS `tr_content`,concat_ws('/','','chapter',`egwk3_translation`.`para_id`,`egwk3_translation`.`lang`,`egwk3_translation`.`publisher`,`egwk3_translation`.`year`,`egwk3_translation`.`no`) AS `chapter_uri`,concat_ws('/','','toc',`egwk3_original`.`refcode_1`,`egwk3_translation`.`lang`,`egwk3_translation`.`publisher`,`egwk3_translation`.`year`,`egwk3_translation`.`no`) AS `toc_uri` from (`egwk3_original` join `egwk3_translation` on(`egwk3_original`.`para_id` = `egwk3_translation`.`para_id`)) where `egwk3_original`.`element_type` in ('h2','h3') ;


DROP TABLE `egwk3_api_chapter`;
CREATE TABLE `egwk3_api_chapter`  AS  select `egwk3_original`.`para_id` AS `para_id`,`egwk3_original`.`id_prev` AS `id_prev`,`egwk3_original`.`id_next` AS `id_next`,`egwk3_original`.`refcode_1` AS `refcode_1`,`egwk3_original`.`refcode_2` AS `refcode_2`,`egwk3_original`.`refcode_3` AS `refcode_3`,`egwk3_original`.`refcode_4` AS `refcode_4`,`egwk3_original`.`refcode_short` AS `refcode_short`,`egwk3_original`.`refcode_long` AS `refcode_long`,`egwk3_original`.`element_type` AS `element_type`,`egwk3_original`.`element_subtype` AS `element_subtype`,`egwk3_original`.`content` AS `content`,`egwk3_original`.`puborder` AS `puborder`,`egwk3_original`.`parent_1` AS `parent_1`,`egwk3_original`.`parent_2` AS `parent_2`,`egwk3_original`.`parent_3` AS `parent_3`,`egwk3_original`.`parent_4` AS `parent_4`,`egwk3_original`.`parent_5` AS `parent_5`,`egwk3_original`.`parent_6` AS `parent_6`,`egwk3_translation`.`lang` AS `lang`,`egwk3_translation`.`publisher` AS `publisher`,`egwk3_translation`.`year` AS `year`,`egwk3_translation`.`no` AS `no`,`egwk3_translation`.`content` AS `tr_content`,concat_ws('/','','toc',`egwk3_original`.`refcode_1`,convert(`egwk3_translation`.`lang` using utf8mb4),convert(`egwk3_translation`.`publisher` using utf8mb4),convert(`egwk3_translation`.`year` using utf8mb4),`egwk3_translation`.`no`) AS `toc_uri` FROM (`egwk3_original` join `egwk3_translation` on((`egwk3_original`.`para_id` = `egwk3_translation`.`para_id`))) WHERE (`egwk3_original`.`element_type` not in ('h1','h2','h3')) ORDER BY `egwk3_original`.`puborder`;

ALTER TABLE `egwk3_api_chapter`
  ADD PRIMARY KEY (`para_id`,`lang`,`publisher`,`year`,`no`),
  ADD KEY `parent_1` (`parent_1`),
  ADD KEY `parent_2` (`parent_2`),
  ADD KEY `parent_3` (`parent_3`),
  ADD KEY `refcode_short` (`refcode_short`);
COMMIT;


DROP TABLE `egwk3_api_book`;
CREATE TABLE `egwk3_api_book` select `egwk3_translation`.`book_code` AS `book_code`,`egwk3_edition`.`tr_code` AS `tr_code`,substr(`egwk3_translation`.`para_id`,1,(locate('.',`egwk3_translation`.`para_id`) - 1)) AS `book_id`,`egwk3_original`.`content` AS `title`,`egwk3_translation`.`content` AS `tr_title`,`egwk3_edition`.`tr_title_alt` AS `tr_title_alt`,`egwk3_edition`.`summary` AS `summary`,`egwk3_edition`.`translator` AS `translator`,`egwk3_translation`.`lang` AS `lang`,`egwk3_translation`.`publisher` AS `publisher`,`egwk3_translation`.`year` AS `year`,`egwk3_translation`.`no` AS `no`,`egwk3_publisher`.`name` AS `publisher_name`,`egwk3_publication`.`primary_collection_text_id` AS `primary_collection_text_id`,`egwk3_publication`.`seq` AS `seq`,`egwk3_edition`.`text_id` AS `text_id`,`egwk3_edition`.`text_id_alt` AS `text_id_alt`,`egwk3_edition`.`church_approved` AS `church_approved`,concat_ws('/',`egwk3_translation`.`book_code`,`egwk3_translation`.`lang`,`egwk3_translation`.`publisher`,`egwk3_translation`.`year`,`egwk3_translation`.`no`) AS `edition_id`,concat_ws('/','','book',`egwk3_translation`.`book_code`) AS `book_uri`,concat_ws('/','','toc',`egwk3_translation`.`book_code`,`egwk3_translation`.`lang`,`egwk3_translation`.`publisher`,`egwk3_translation`.`year`,`egwk3_translation`.`no`) AS `toc_uri`,concat_ws('/','','translation',`egwk3_translation`.`book_code`,`egwk3_translation`.`lang`,`egwk3_translation`.`publisher`,`egwk3_translation`.`year`,`egwk3_translation`.`no`) AS `translation_uri`,concat_ws('/','','zip','translation',`egwk3_translation`.`book_code`,`egwk3_translation`.`lang`,`egwk3_translation`.`publisher`,`egwk3_translation`.`year`,`egwk3_translation`.`no`) AS `zip_uri` from ((((`egwk3_translation` join `egwk3_edition` on(((`egwk3_translation`.`book_code` = `egwk3_edition`.`book_code`) and (`egwk3_translation`.`publisher` = `egwk3_edition`.`publisher_code`) and (`egwk3_translation`.`year` = `egwk3_edition`.`year`) and (`egwk3_translation`.`no` = `egwk3_edition`.`no`)))) join `egwk3_publisher` on((`egwk3_translation`.`publisher` = `egwk3_publisher`.`code`))) join `egwk3_publication` on((`egwk3_translation`.`book_code` = `egwk3_publication`.`book_code`))) JOIN `egwk3_original` on((`egwk3_original`.`para_id` = `egwk3_translation`.`para_id`))) WHERE (`egwk3_original`.`puborder` = 1) ORDER BY `egwk3_publication`.`seq`,`egwk3_edition`.`church_approved` desc ;

ALTER TABLE `egwk3_api_book`
  ADD PRIMARY KEY (`book_code`,`lang`,`publisher`,`year`,`no`);
COMMIT;

DROP TABLE `egwk3_api_toc`;
CREATE TABLE `egwk3_api_toc`  AS  select `egwk3_original`.`para_id` AS `para_id`,`egwk3_original`.`id_prev` AS `id_prev`,`egwk3_original`.`id_next` AS `id_next`,`egwk3_original`.`refcode_1` AS `refcode_1`,`egwk3_original`.`refcode_2` AS `refcode_2`,`egwk3_original`.`refcode_3` AS `refcode_3`,`egwk3_original`.`refcode_4` AS `refcode_4`,`egwk3_original`.`refcode_short` AS `refcode_short`,`egwk3_original`.`refcode_long` AS `refcode_long`,`egwk3_original`.`element_type` AS `element_type`,`egwk3_original`.`element_subtype` AS `element_subtype`,`egwk3_original`.`content` AS `content`,`egwk3_original`.`puborder` AS `puborder`,`egwk3_original`.`parent_1` AS `parent_1`,`egwk3_original`.`parent_2` AS `parent_2`,`egwk3_original`.`parent_3` AS `parent_3`,`egwk3_original`.`parent_4` AS `parent_4`,`egwk3_original`.`parent_5` AS `parent_5`,`egwk3_original`.`parent_6` AS `parent_6`,`egwk3_translation`.`lang` AS `lang`,`egwk3_translation`.`publisher` AS `publisher`,`egwk3_translation`.`year` AS `year`,`egwk3_translation`.`no` AS `no`,`egwk3_translation`.`content` AS `tr_content`,concat_ws('/','','chapter',`egwk3_translation`.`para_id`,`egwk3_translation`.`lang`,`egwk3_translation`.`publisher`,`egwk3_translation`.`year`,`egwk3_translation`.`no`) AS `chapter_uri`,concat_ws('/','','toc',`egwk3_original`.`refcode_1`,convert(`egwk3_translation`.`lang` using utf8mb4),convert(`egwk3_translation`.`publisher` using utf8mb4),convert(`egwk3_translation`.`year` using utf8mb4),`egwk3_translation`.`no`) AS `toc_uri` FROM (`egwk3_original` join `egwk3_translation` on((`egwk3_original`.`para_id` = `egwk3_translation`.`para_id`))) WHERE (`egwk3_original`.`element_type` in ('h2','h3'));

ALTER TABLE `egwk3_api_toc`
  ADD PRIMARY KEY (`para_id`,`lang`,`publisher`,`year`,`no`),
  ADD KEY `parent_1` (`parent_1`),
  ADD KEY `parent_2` (`parent_2`),
  ADD KEY `parent_3` (`parent_3`),
  ADD KEY `refcode_short` (`refcode_short`);
COMMIT;
