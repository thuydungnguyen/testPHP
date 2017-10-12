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

    private function debug($var)
    {
        echo '<pre>';
        print_r($var);
        echo '</pre>';
    }

    private function computeText($text, array $data)
    {
        $APPLICATION_CONTEXT = ApplicationContext::getInstance();

        $quote = (isset($data['quote']) && $data['quote'] instanceof Quote) ? $data['quote'] : null;

        if ($quote)
        {
            $_quoteFromRepository = QuoteRepository::getInstance()->getById($quote->id);
            $usefulObject = SiteRepository::getInstance()->getById($quote->siteId);

            if(strpos($text, '[quote:destination_link]') !== false){
                $destination = DestinationRepository::getInstance()->getById($quote->destinationId);
            }

            $containsSummaryHtml = strpos($text, '[quote:summary_html]');
            $containsSummary     = strpos($text, '[quote:summary]');

            if ($containsSummaryHtml !== false || $containsSummary !== false) {
                $text = self::computeSummary($text, $quote);
            }

            if(strpos($text, '[quote:destination_name]') !== false) {
                $text = self::computeDestinationName($text, $quote);
            }
        }

        if (isset($destination))
            $text = str_replace('[quote:destination_link]', $usefulObject->url . '/' . $destination->countryName . '/quote/' . $_quoteFromRepository->id, $text);
        else
            $text = str_replace('[quote:destination_link]', '', $text);

        /*
         * USER
         * [user:*]
         */
        $_user  = (isset($data['user'])  and ($data['user']  instanceof User))  ? $data['user']  : $APPLICATION_CONTEXT->getCurrentUser();
        if($_user) {
            (strpos($text, '[user:first_name]') !== false) and $text = str_replace('[user:first_name]'       , ucfirst(mb_strtolower($_user->firstname)), $text);
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
}
