<?php

class TemplateManager
{
    public function getTemplateComputed(Template $tpl, array $data)
    {
        if (!$tpl) {
            throw new \RuntimeException('no tpl given');
        }

        $replaced = clone($tpl);
        $replaced->subject = $this->computeText($replaced->subject, $data);
        $replaced->content = $this->computeText($replaced->content, $data);

        return $replaced;
    }

    private function computeText($text, array $data)
    {


        $quote = (isset($data['quote']) && $data['quote'] instanceof Quote) ? $data['quote'] : null;

        if ($quote)
        {
            $containsSummaryHtml = strpos($text, '[quote:summary_html]');
            $containsSummary     = strpos($text, '[quote:summary]');

            if ($containsSummaryHtml !== false || $containsSummary !== false) {
                $text = self::computeSummary($text, $quote);
            }

            if(strpos($text, '[quote:destination_name]') !== false) {
                $text = self::computeDestinationName($text, $quote);
            }

            if(strpos($text, '[quote:destination_link]') !== false){
                $text = self::computeDestinationLink($text, $quote);
            }
        }

        /*
         * USER
         * [user:*]
         */
        if(strpos($text, '[user:first_name]') !== false){
            $text = self::computeUser($text, $data);
        }

        return $text;
    }

    private static function computeSummary($text, Quote $quote)
    {
        $_quoteFromRepository = QuoteRepository::getInstance()->getById($quote->id);

        $text = str_replace('[quote:summary_html]', Quote::renderHtml($_quoteFromRepository), $text);
        $text = str_replace('[quote:summary]', Quote::renderText($_quoteFromRepository), $text);

        return $text;
    }

    private static function computeDestinationName($text, Quote $quote)
    {
        $destinationOfQuote = DestinationRepository::getInstance()->getById($quote->destinationId);
        return str_replace('[quote:destination_name]',$destinationOfQuote->countryName,$text);
    }

    private static function computeDestinationLink($text, Quote $quote)
    {
        $_quoteFromRepository = QuoteRepository::getInstance()->getById($quote->id);
        $usefulObject = SiteRepository::getInstance()->getById($quote->siteId);
        $destination = DestinationRepository::getInstance()->getById($quote->destinationId);

        return str_replace('[quote:destination_link]', $usefulObject->url . '/' . $destination->countryName . '/quote/' . $_quoteFromRepository->id, $text);
    }

    private static function computeUser($text, array $data)
    {
        $APPLICATION_CONTEXT = ApplicationContext::getInstance();
        $user  = (isset($data['user']) && ($data['user']  instanceof User))  ? $data['user']  : $APPLICATION_CONTEXT->getCurrentUser();
        if($user) {
            $text = str_replace('[user:first_name]',ucfirst(mb_strtolower($user->firstname)),$text);
        }

        return $text;
    }
}
