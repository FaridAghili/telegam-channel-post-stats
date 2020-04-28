<?php

use Carbon\Carbon;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Exceptions\ChildNotFoundException;
use PHPHtmlParser\Exceptions\CircularException;
use PHPHtmlParser\Exceptions\CurlException;
use PHPHtmlParser\Exceptions\NotLoadedException;
use PHPHtmlParser\Exceptions\StrictException;

class ChannelPost
{
    private string $channel;
    private int $id;
    private string $views;
    private Carbon $dateTime;
    private bool $initialized = false;

    public function __construct(string $channel, int $id)
    {
        $this->channel = $channel;
        $this->id = $id;
    }

    public function init(): bool
    {
        $dom = new Dom;
        try {
            $dom->loadFromUrl('https://t.me/'.$this->channel.'/'.$this->id.'?embed=1');
        } catch (ChildNotFoundException $e) {
            echo '#1 ChildNotFoundException';
            return false;
        } catch (CircularException $e) {
            echo '#2 CircularException';
            return false;
        } catch (CurlException $e) {
            echo '#3 CurlException';
            return false;
        } catch (StrictException $e) {
            echo '#4 StrictException';
            return false;
        }

        try {
            $widgetMessageViews = $dom->getElementsByClass('tgme_widget_message_views');
        } catch (ChildNotFoundException $e) {
            echo '#5 ChildNotFoundException';
            return false;
        } catch (NotLoadedException $e) {
            echo '#6 NotLoadedException';
            return false;
        }

        if (! $widgetMessageViews->count()) {
            echo '#7';
            return false;
        }

        $this->views = $widgetMessageViews[0]->text;

        try {
            $timeTag = $dom->getElementsByTag('time');
        } catch (ChildNotFoundException $e) {
            echo '#8 ChildNotFoundException';
            return false;
        } catch (NotLoadedException $e) {
            echo '#9 NotLoadedException';
            return false;
        }

        if (! $timeTag->count()) {
            echo '#10';
            return false;
        }

        try {
            $this->dateTime = new Carbon($timeTag[0]->getAttribute('datetime'));
        } catch (Exception $e) {
            echo '#11 Exception';
            return false;
        }

        $this->initialized = true;
        return true;
    }

    public function getId(): ?int
    {
        return $this->initialized ? $this->id : null;
    }

    public function getViews(): ?string
    {
        return $this->initialized ? $this->views : null;
    }

    public function getDateTime(): ?Carbon
    {
        return $this->initialized ? $this->dateTime : null;
    }
}