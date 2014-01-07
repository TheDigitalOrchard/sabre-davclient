<?php

namespace Sabre\DAVClient\RequestBuilder;

use Sabre\HTTP;

class SyncCollectionReportRequestBuilder implements RequestBuilderInterface
{
    protected $sync_token;

    protected $headers = ['Content-Type' => 'application/xml'];

    protected $method = 'REPORT';

    protected $url;

    public function __construct($url, array $sync_token = null, $sync_level = 'infinite')
    {
        $this->url = $url;
        $this->sync_token = $sync_token;
        $this->sync_level = $sync_level;
    }

    public function build()
    {
        return new HTTP\Request($this->method, $this->url, $this->headers, $this->writeXML());
    }

    protected function writeXML()
    {
        $xml = new \XMLWriter;
        $xml->openMemory();
        $xml->setIndent(4);
        $xml->startDocument('1.0', 'utf-8');
            $xml->startElement('d:sync-collection');
                $xml->writeAttribute('xmlns:d', 'DAV:');
                $xml->writeAttribute('xmlns:a', 'urn:ietf:params:xml:ns:carddav');
                $xml->writeElement('d:sync-token', $this->sync_token);
                $xml->writeElement('d:sync-level', $this->sync_level);
                $xml->startElement('d:prop');
                    $xml->writeElement('d:getcontentlength');
                    $xml->writeElement('d:getcontenttype');
                    $xml->writeElement('d:getetag');
                    $xml->writeElement('d:allprop');
                    $xml->writeElement('a:address-data');
                $xml->endElement();
                $xml->writeElement('a:filter');
            $xml->endElement();
        $xml->endDocument();

        return $xml->outputMemory();
    }
}
