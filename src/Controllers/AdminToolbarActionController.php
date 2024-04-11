<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Controllers;

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Security\SecurityToken;
use SilverStripe\Versioned\Versioned;
use WeDevelop\AdminToolbar\Menus\Page\MenuItems\ArchiveMenuItem;
use WeDevelop\AdminToolbar\Menus\Page\MenuItems\UnpublishAndArchiveMenuItem;
use WeDevelop\AdminToolbar\Menus\Page\MenuItems\UnpublishMenuItem;

class AdminToolbarActionController extends Controller
{
    /** @config */
    private static string $url_segment = 'admintoolbaraction';

    /**
     * @config
     * @var array<string>
     */
    private static array $allowed_actions = [
        'pageAction',
    ];

    /**
     * @var array<class-string>
     */
    private static array $unpublishActions = [
        UnpublishMenuItem::ACTION,
        UnpublishAndArchiveMenuItem::ACTION,
    ];

    /**
     * @var array<class-string>
     */
    private static array $archiveActions = [
        UnpublishAndArchiveMenuItem::ACTION,
        ArchiveMenuItem::ACTION,
    ];

    /**
     * @var array<class-string, string>
     */
    private static $successMessages = [
        UnpublishMenuItem::ACTION => 'Page succesfully unpublished',
        ArchiveMenuItem::ACTION => 'Page succesfully archived',
        UnpublishAndArchiveMenuItem::ACTION => 'Page succesfully unpublished and archived',
    ];

    public function pageAction(HTTPRequest $request): HTTPResponse
    {
        if (!SecurityToken::inst()->checkRequest($request)) {
            return $this->httpError(400, 'CSRF token mismatch');
        }

        $params = json_decode((string)$request->getBody(), true);
        $pageId = $params['page_id'] ?? null;

        if (!$pageId) {
            return $this->httpError(400, 'No page ID provided');
        }

        /** @var SiteTree $page */
        $page = Versioned::get_by_stage(SiteTree::class, 'Stage')->byID($pageId);

        if (!$page) {
            return $this->httpError(404, 'Page not found');
        }

        $response = \SilverStripe\Control\HTTPResponse::create();
        $action = $params['action'];

        if (!in_array($action, array_merge(self::$unpublishActions, self::$archiveActions), true)) {
            return $this->httpError(404, 'This action is not allowed');
        }

        if (!$page->isPublished() && in_array($action, self::$unpublishActions, true)) {
            $response->setStatusCode(200);
            $response->setBody(json_encode(['message' => 'Page is already unpublished']));
            return $response;
        }

        if (in_array($action, self::$unpublishActions, true)) {
            $page->doUnpublish();
        }

        if (in_array($action, self::$archiveActions, true)) {
            $page->doArchive();
        }

        $response->setStatusCode(200);
        $response->setBody(json_encode(['message' => self::$successMessages[$action]]));

        return $response;
    }
}
