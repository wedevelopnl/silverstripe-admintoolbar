<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Controller;

use Psr\Log\LoggerInterface;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Security\Member;
use SilverStripe\Security\Permission;
use SilverStripe\Security\Security;
use SilverStripe\Security\SecurityToken;
use SilverStripe\Versioned\Versioned;
use WeDevelop\AdminToolbar\AdminToolbar;
use WeDevelop\AdminToolbar\Menu\Page\PageActionItem;

/**
 * Executes the Page menu's actions. Every outcome answers JSON `{"message": …}`
 * so the toolbar can show it, whatever the status.
 */
final class PageActionController extends Controller
{
    private static string $url_segment = 'admintoolbaraction';

    /** @var list<string> */
    private static array $allowed_actions = [
        'pageAction',
    ];

    /** @var array<string, string> */
    private static array $dependencies = [
        'logger' => '%$' . LoggerInterface::class,
    ];

    public LoggerInterface $logger;

    public function pageAction(HTTPRequest $request): HTTPResponse
    {
        if (!$request->isPOST()) {
            return $this->reply(405, _t(self::class . '.METHOD', 'Page actions must be sent as POST.'))
                ->addHeader('Allow', 'POST');
        }

        if (!SecurityToken::inst()->checkRequest($request)) {
            return $this->reply(400, _t(self::class . '.TOKEN', 'Your session expired. Reload the page and try again.'));
        }

        $member = Security::getCurrentUser();

        if (!$member instanceof Member) {
            return $this->deny('unauthenticated', null);
        }

        if (!Permission::checkMember($member, AdminToolbar::PERMISSION)) {
            return $this->deny('toolbar-permission', $member);
        }

        $payload = json_decode((string) $request->getBody(), true);
        // A body that is not a JSON object reads as an empty one and fails the check below.
        $payload = is_array($payload) ? $payload : [];
        $pageId = filter_var($payload['page_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $action = $payload['action'] ?? null;

        if ($pageId === false || !is_string($action)) {
            return $this->reply(400, _t(self::class . '.MALFORMED', 'The request could not be understood.'));
        }

        $item = PageActionItem::forAction($action);

        if (!$item instanceof PageActionItem) {
            return $this->reply(400, _t(self::class . '.UNKNOWN_ACTION', 'This action is not available.'));
        }

        $page = Versioned::get_by_stage(SiteTree::class, Versioned::DRAFT)->byID($pageId);

        if (!$page instanceof SiteTree) {
            return $this->reply(404, _t(self::class . '.NOT_FOUND', 'The page no longer exists.'));
        }

        if (!$item->isAllowedFor($page, $member)) {
            return $this->deny('page-permission', $member, $action, $pageId);
        }

        if (!$item->appliesTo($page)) {
            return $this->reply(409, _t(self::class . '.CONFLICT', 'The page changed in the meantime. Reload the page and try again.'));
        }

        $item->perform($page);

        return $this->reply(200, $item->getSuccessMessage());
    }

    private function deny(string $reason, ?Member $member, ?string $action = null, ?int $pageId = null): HTTPResponse
    {
        $this->logger->warning('Admin toolbar page action denied', [
            'reason' => $reason,
            'memberID' => $member?->ID,
            'action' => $action,
            'pageID' => $pageId,
        ]);

        return $this->reply(403, _t(self::class . '.FORBIDDEN', 'You are not allowed to do this.'));
    }

    private function reply(int $status, string $message): HTTPResponse
    {
        return HTTPResponse::create(json_encode(['message' => $message], JSON_THROW_ON_ERROR), $status)
            ->addHeader('Content-Type', 'application/json');
    }
}
