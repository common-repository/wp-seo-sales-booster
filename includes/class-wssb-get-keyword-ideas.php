<?php

/**
 * Copyright 2017 Google Inc. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

//namespace Google\AdsApi\Examples\AdWords\v201809\Optimization;

require __DIR__ . '/../vendor/autoload.php';

use Google\AdsApi\AdWords\AdWordsServices;
use Google\AdsApi\AdWords\AdWordsSession;
use Google\AdsApi\AdWords\AdWordsSessionBuilder;
use Google\AdsApi\AdWords\v201809\cm\Language;
use Google\AdsApi\AdWords\v201809\cm\Location;
use Google\AdsApi\AdWords\v201809\cm\NetworkSetting;
use Google\AdsApi\AdWords\v201809\cm\Paging;
use Google\AdsApi\AdWords\v201809\o\AttributeType;
use Google\AdsApi\AdWords\v201809\o\IdeaType;
use Google\AdsApi\AdWords\v201809\o\LanguageSearchParameter;
use Google\AdsApi\AdWords\v201809\o\LocationSearchParameter;
use Google\AdsApi\AdWords\v201809\o\NetworkSearchParameter;
use Google\AdsApi\AdWords\v201809\o\RelatedToQuerySearchParameter;
use Google\AdsApi\AdWords\v201809\o\RequestType;
use Google\AdsApi\AdWords\v201809\o\SeedAdGroupIdSearchParameter;
use Google\AdsApi\AdWords\v201809\o\TargetingIdeaSelector;
use Google\AdsApi\AdWords\v201809\o\TargetingIdeaService;
use Google\AdsApi\Common\OAuth2TokenBuilder;
use Google\AdsApi\Common\Util\MapEntries;

/**
 * This example gets keyword ideas related to a seed keyword.
 */
class Wssb_Get_Keyword_Ideas
{

    // the path of your .ini file;
    // If you do not want to use an existing ad group to seed your request, you
    // can set this to null.
    const AD_GROUP_ID = null;
    const PAGE_LIMIT = 500;

    public static function runExample(
        AdWordsServices $adWordsServices,
        AdWordsSession $session,
        $adGroupId,
        $s_keyword,
        $language,
        $country
    ) {
        $targetingIdeaService = $adWordsServices->get($session, TargetingIdeaService::class);

        // Create selector.
        $selector = new TargetingIdeaSelector();
        $selector->setRequestType(RequestType::IDEAS);
        $selector->setIdeaType(IdeaType::KEYWORD);
        $selector->setRequestedAttributeTypes(
            [
                AttributeType::KEYWORD_TEXT,
                AttributeType::IDEA_TYPE,
                AttributeType::SEARCH_VOLUME,
                AttributeType::TARGETED_MONTHLY_SEARCHES,
                AttributeType::AVERAGE_CPC,
                AttributeType::COMPETITION,
                AttributeType::EXTRACTED_FROM_WEBPAGE,
                AttributeType::CATEGORY_PRODUCTS_AND_SERVICES,

            ]
        );

        $paging = new Paging();
        $paging->setStartIndex(0);
        $paging->setNumberResults(10);
        $selector->setPaging($paging);

        $searchParameters = [];
        // Create related to query search parameter.
        $relatedToQuerySearchParameter = new RelatedToQuerySearchParameter();
        $relatedToQuerySearchParameter->setQueries([$s_keyword]);
        $searchParameters[] = $relatedToQuerySearchParameter;

        // Create language search parameter (optional).
        // The ID can be found in the documentation:
        // https://developers.google.com/adwords/api/docs/appendix/languagecodes
        $languageParameter = new LanguageSearchParameter();
        $lang = new Language();
        $lang->setId($language);
        $languageParameter->setLanguages([$lang]);
        $searchParameters[] = $languageParameter;

        // Create location search parameter (optional).
        $locationParameter = new locationSearchParameter();
        $local = new location();
        $local->setId($country);
        $locationParameter->setLocations([$local]);
        $searchParameters[] = $locationParameter;

        // Create network search parameter (optional).
        $networkSetting = new NetworkSetting();
        $networkSetting->setTargetGoogleSearch(true);
        $networkSetting->setTargetSearchNetwork(false);
        $networkSetting->setTargetContentNetwork(false);
        $networkSetting->setTargetPartnerSearchNetwork(false);

        $networkSearchParameter = new NetworkSearchParameter();
        $networkSearchParameter->setNetworkSetting($networkSetting);
        $searchParameters[] = $networkSearchParameter;

        // Optional: Use an existing ad group to generate ideas.
        if (!empty($adGroupId)) {
            $seedAdGroupIdSearchParameter = new SeedAdGroupIdSearchParameter();
            $seedAdGroupIdSearchParameter->setAdGroupId($adGroupId);
            $searchParameters[] = $seedAdGroupIdSearchParameter;
        }
        $selector->setSearchParameters($searchParameters);
        $selector->setPaging(new Paging(0, self::PAGE_LIMIT));

        // Get keyword ideas.
        $page = $targetingIdeaService->get($selector);

        // Print out some information for each targeting idea.
        $entries = $page->getEntries();
        $result = array();
        if ($entries !== null) {
            foreach ($entries as $targetingIdea) {
                $data = MapEntries::toAssociativeArray($targetingIdea->getData());
                $keyword = $data[AttributeType::KEYWORD_TEXT]->getValue();
                // $ideaType = $data[AttributeType::IDEA_TYPE]->getValue();
                $averageCpc = $data[AttributeType::AVERAGE_CPC]->getValue();
                $searchVolume = ($data[AttributeType::SEARCH_VOLUME]->getValue() !== null)
                    ? $data[AttributeType::SEARCH_VOLUME]->getValue() : 0;
                $searchMonthlyVolume = ($data[AttributeType::TARGETED_MONTHLY_SEARCHES]->getValue() !== null)
                    ? $data[AttributeType::TARGETED_MONTHLY_SEARCHES]->getValue() : 0;
                $competitionScore = $data[AttributeType::COMPETITION]->getValue();

                if (!empty($searchMonthlyVolume) && is_array($searchMonthlyVolume)) {
                    $keyword_stats = array();
                    foreach ($searchMonthlyVolume as $month_stats) {
                        $stats = new stdClass();
                        $stats->year = $month_stats->getYear();
                        $stats->month = $month_stats->getMonth();
                        $stats->count = $month_stats->getCount();
                        $keyword_stats[] = $stats;
                    }
                }

                $result[] = [
                    "keyword" => $keyword,
                    "searchVolume" => $searchVolume,
                    "averageCpc" => ($averageCpc === null) ? 0 : $averageCpc->getMicroAmount() / 1000000,
                    "keyword_stats" => $keyword_stats,
                ];
            }
            return $result;
        }
        //return $keyword;

        if (empty($entries)) {
            return "No related keywords were found.\n";
        }
    }

    public static function main($s_keyword, $language, $country)
    {
        // Generate a refreshable OAuth2 credential for authentication.
        $oAuth2Credential = (new OAuth2TokenBuilder())->withClientId(WSSB_CLIENTID)->withClientSecret(WSSB_CLIENTSECRET)->withRefreshToken(get_option('wssb_google_refresh_code'))->build();
        // var_dump($oAuth2Credential);exit;

        // Construct an API session configured from a properties file and the
        // OAuth2 credentials above.
        $session = (new AdWordsSessionBuilder())->withClientCustomerId(WSSB_CUSTOMER_CLIENT_ID)->withDeveloperToken(WSSB_DEV_TOKEN)->withOAuth2Credential($oAuth2Credential)->build();
        return self::runExample(new AdWordsServices(), $session, self::AD_GROUP_ID, $s_keyword, $language, $country);
    }
}
// $check = Wssb_Get_Keyword_Ideas::main('hello','1000','2840');
// var_dump($check);
// exit;
