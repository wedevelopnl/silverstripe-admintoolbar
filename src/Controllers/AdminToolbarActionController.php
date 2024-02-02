<?php

namespace WeDevelop\AdminToolbar\Controllers;

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Security\SecurityToken;
use SilverStripe\Versioned\Versioned;

class AdminToolbarActionController extends Controller
{
    /** @config */
    private static string $url_segment = 'admintoolbaraction';

    /**
     * @config
     * @var array<string>
     */
    private static array $allowed_actions = [
        'pageUnpublish',
    ];


    public function pageUnpublish(HTTPRequest $request): HTTPResponse
    {
        $csrfToken = $request->getHeader('X-CSRF-Token');
        if (!SecurityToken::inst()->check($csrfToken)) {
            return $this->httpError(400, 'CSRF token mismatch');
        }

        $params = json_decode($request->getBody(), true);
        $pageId = $params['page_id'] ?? null;

        if (!$pageId) {
            return $this->httpError(400, 'No page ID provided');
        }

        $page = Versioned::get_by_stage(SiteTree::class, 'Stage')->byID($pageId);

        if (!$page) {
            return $this->httpError(404, 'Page not found');
        }

        $response = new HTTPResponse();

        if (!$page->isPublished()) {
            $response->setStatusCode(200);
            $response->setBody(json_encode(['message' => 'Page is already unpublished']));
            return $response;
        }

        $page->doUnpublish();

        $response->setStatusCode(200);
        $response->setBody(json_encode(['message' => 'Page unpublished successfully']));
        return $response;
        return $res;
    }
}
