<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Tests\Functional\Controller;

use Monolog\Handler\TestHandler;
use Monolog\Level;
use Monolog\Logger;
use Monolog\LogRecord;
use Page;
use Psr\Log\LoggerInterface;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Dev\FunctionalTest;
use SilverStripe\Security\Member;
use SilverStripe\Security\SecurityToken;
use SilverStripe\Versioned\Versioned;
use WeDevelop\AdminToolbar\Menu\Page\ArchivePageItem;
use WeDevelop\AdminToolbar\Tests\Integration\Fixture\FixturePages;

final class PageActionControllerTest extends FunctionalTest
{
    use FixturePages;

    private const string URL = 'admintoolbaraction/pageAction';

    protected static $fixture_file = '../../Integration/Fixture/pages.yml';

    private TestHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        // FunctionalTest switches CSRF protection off; these tests are about it.
        SecurityToken::enable();
        $this->handler = new TestHandler();
        Injector::inst()->registerService(new Logger('test', [$this->handler]), LoggerInterface::class);
        $this->publishFixturePages();
    }

    public function testGetIsRejected(): void
    {
        $this->logInAsMemberWith('ADMIN');

        $response = $this->get(self::URL);

        $this->assertReply(405, $response);
        $this->assertSame('POST', $response->getHeader('Allow'));
        $this->assertTrue($this->isOnStage(Versioned::LIVE, $this->pageId('published')));
    }

    public function testMissingTokenIsRejected(): void
    {
        $this->logInAsMemberWith('ADMIN');

        $response = $this->post(self::URL, [], null, null, $this->json('unpublish', 'published'));

        $this->assertReply(400, $response);
        $this->assertTrue($this->isOnStage(Versioned::LIVE, $this->pageId('published')));
    }

    public function testWrongTokenIsRejected(): void
    {
        $this->logInAsMemberWith('ADMIN');
        $this->sessionToken();

        $response = $this->post(self::URL, [], ['X-Securityid' => 'nope'], null, $this->json('unpublish', 'published'));

        $this->assertReply(400, $response);
        $this->assertTrue($this->isOnStage(Versioned::LIVE, $this->pageId('published')));
    }

    public function testAnonymousWithValidTokenIsForbidden(): void
    {
        // Any visitor gets a session token from a form, e.g. the login page.
        $this->get('Security/login');
        $token = $this->session()->get(SecurityToken::inst()->getName());
        $this->assertIsString($token);

        $response = $this->post(self::URL, [], ['X-Securityid' => $token], null, $this->json('unpublish', 'published'));

        $this->assertReply(403, $response);
        $this->assertTrue($this->isOnStage(Versioned::LIVE, $this->pageId('published')));
        $this->assertDenialLogged('unauthenticated', null);
    }

    public function testMemberWithoutToolbarPermissionIsForbidden(): void
    {
        $memberId = $this->logInAsMemberWith('CMS_ACCESS_CMSMain');

        $response = $this->postAction($this->json('unpublish', 'published'));

        $this->assertReply(403, $response);
        $this->assertTrue($this->isOnStage(Versioned::LIVE, $this->pageId('published')));
        $this->assertDenialLogged('toolbar-permission', $memberId);
    }

    public function testNonJsonBodyIsRejected(): void
    {
        $this->logInAsMemberWith('ADMIN');

        $this->assertReply(400, $this->postAction('not json'));
    }

    public function testNonObjectJsonIsRejected(): void
    {
        $this->logInAsMemberWith('ADMIN');

        foreach (['[1,2]', '"x"'] as $body) {
            $this->assertReply(400, $this->postAction($body), $body);
        }
    }

    public function testInvalidPageIdIsRejected(): void
    {
        $this->logInAsMemberWith('ADMIN');

        foreach (['"abc"', '"0"', '"-3"', '"12abc"', 'null'] as $pageId) {
            $this->assertReply(400, $this->postAction('{"page_id": ' . $pageId . ', "action": "unpublish"}'), $pageId);
        }

        $this->assertReply(400, $this->postAction('{"action": "unpublish"}'), 'missing page_id');
        $this->assertTrue($this->isOnStage(Versioned::LIVE, $this->pageId('published')));
    }

    public function testMissingOrNonStringActionIsRejected(): void
    {
        $this->logInAsMemberWith('ADMIN');
        $pageId = $this->pageId('published');

        $this->assertReply(400, $this->postAction(sprintf('{"page_id": "%d"}', $pageId)), 'missing action');
        $this->assertReply(400, $this->postAction(sprintf('{"page_id": "%d", "action": ["unpublish"]}', $pageId)), 'array action');
        $this->assertReply(400, $this->postAction(sprintf('{"page_id": "%d", "action": 5}', $pageId)), 'integer action');
        $this->assertTrue($this->isOnStage(Versioned::LIVE, $pageId));
    }

    public function testUnknownActionIsRejected(): void
    {
        $this->logInAsMemberWith('ADMIN');

        $this->assertReply(400, $this->postAction($this->json('delete', 'published')));
        $this->assertTrue($this->isOnStage(Versioned::DRAFT, $this->pageId('published')));
    }

    public function testDisabledActionIsRejected(): void
    {
        Config::modify()->set(ArchivePageItem::class, 'enabled', false);
        $this->logInAsMemberWith('ADMIN');

        $this->assertReply(400, $this->postAction($this->json('archive', 'draft')));
        $this->assertTrue($this->isOnStage(Versioned::DRAFT, $this->pageId('draft')));
    }

    public function testUnknownPageIsNotFound(): void
    {
        $this->logInAsMemberWith('ADMIN');

        $this->assertReply(404, $this->postAction('{"page_id": "999999", "action": "unpublish"}'));
    }

    public function testMemberWithoutPagePermissionIsForbidden(): void
    {
        $memberId = $this->logInAsMemberWith('ADMIN_TOOLBAR');

        $response = $this->postAction($this->json('unpublish', 'published'));

        $this->assertReply(403, $response);
        $this->assertTrue($this->isOnStage(Versioned::LIVE, $this->pageId('published')));
        $this->assertDenialLogged('page-permission', $memberId);
    }

    public function testUnpublishingAnUnpublishedPageConflicts(): void
    {
        $this->logInAsMemberWith('ADMIN');
        $pageId = $this->pageId('draft');

        $this->assertReply(409, $this->postAction($this->json('unpublish', 'draft')));
        $this->assertTrue($this->isOnStage(Versioned::DRAFT, $pageId));
        $this->assertFalse($this->isOnStage(Versioned::LIVE, $pageId));
    }

    public function testArchivingAPublishedPageConflicts(): void
    {
        $this->logInAsMemberWith('ADMIN');
        $pageId = $this->pageId('published');

        $this->assertReply(409, $this->postAction($this->json('archive', 'published')));
        $this->assertTrue($this->isOnStage(Versioned::LIVE, $pageId));
        $this->assertTrue($this->isOnStage(Versioned::DRAFT, $pageId));
    }

    public function testUnpublish(): void
    {
        $this->logInAsMemberWith('ADMIN');
        $pageId = $this->pageId('published');

        $response = $this->postAction($this->json('unpublish', 'published'));

        $this->assertReply(200, $response, message: 'Page unpublished');
        $this->assertFalse($this->isOnStage(Versioned::LIVE, $pageId));
        $this->assertTrue($this->isOnStage(Versioned::DRAFT, $pageId));
    }

    public function testArchive(): void
    {
        $this->logInAsMemberWith('ADMIN');
        $page = $this->fixturePage('draft');

        $response = $this->postAction($this->json('archive', 'draft'));

        $this->assertReply(200, $response, message: 'Page archived');
        $this->assertTrue($page->isArchived());
    }

    public function testUnpublishAndArchive(): void
    {
        $this->logInAsMemberWith('ADMIN');
        $pageId = $this->pageId('published');

        $response = $this->postAction($this->json('unpublishAndArchive', 'published'));

        $this->assertReply(200, $response, message: 'Page unpublished and archived');
        $this->assertFalse($this->isOnStage(Versioned::LIVE, $pageId));
        $this->assertFalse($this->isOnStage(Versioned::DRAFT, $pageId));
    }

    public function testTokenAsRequestVarIsAccepted(): void
    {
        $this->logInAsMemberWith('ADMIN');

        $response = $this->post(
            self::URL,
            [SecurityToken::inst()->getName() => $this->sessionToken()],
            null,
            null,
            $this->json('unpublish', 'published'),
        );

        $this->assertReply(200, $response, message: 'Page unpublished');
    }

    public function testNoSensitiveDataIsLogged(): void
    {
        $emails = [];

        foreach (['CMS_ACCESS_CMSMain', 'ADMIN_TOOLBAR'] as $permission) {
            $member = Member::get()->byID($this->logInAsMemberWith($permission));
            $this->assertInstanceOf(Member::class, $member);
            $emails[] = (string) $member->Email;
            $this->assertReply(403, $this->postAction($this->json('unpublish', 'published')));
            $this->logOut();
            $this->session()->clear('loggedInAs');
        }

        $this->assertReply(403, $this->postAction($this->json('unpublish', 'published')));

        $this->assertCount(3, $this->handler->getRecords());

        foreach ($this->handler->getRecords() as $record) {
            $logged = $record->message . json_encode($record->context, JSON_THROW_ON_ERROR);

            $this->assertStringNotContainsString($this->sessionToken(), $logged);
            $this->assertStringNotContainsString('SecurityID', $logged);

            foreach ($emails as $email) {
                $this->assertStringNotContainsString($email, $logged);
            }
        }
    }

    private function postAction(string $body): HTTPResponse
    {
        return $this->post(self::URL, [], ['X-Securityid' => $this->sessionToken()], null, $body);
    }

    /**
     * The request body the toolbar's script sends.
     */
    private function json(string $action, string $page): string
    {
        return json_encode(['page_id' => (string) $this->pageId($page), 'action' => $action], JSON_THROW_ON_ERROR);
    }

    /**
     * The token the session holds, as any page with a form would have stored it.
     */
    private function sessionToken(): string
    {
        $name = SecurityToken::inst()->getName();
        $token = $this->session()->get($name);

        if (!is_string($token)) {
            $token = bin2hex(random_bytes(16));
            $this->session()->set($name, $token);
        }

        return $token;
    }

    private function pageId(string $identifier): int
    {
        return $this->idFromFixture(Page::class, $identifier);
    }

    private function logInAsMemberWith(string $permission): int
    {
        $memberId = $this->logInWithPermission($permission);
        $this->session()->set('loggedInAs', $memberId);

        return $memberId;
    }

    private function assertReply(int $status, HTTPResponse $response, string $case = '', ?string $message = null): void
    {
        $this->assertSame($status, $response->getStatusCode(), $case);
        $this->assertSame('application/json', $response->getHeader('Content-Type'), $case);

        $body = json_decode((string) $response->getBody(), true);
        $this->assertIsArray($body, $case);
        $this->assertIsString($body['message'] ?? null, $case);
        $this->assertNotSame('', $body['message'], $case);

        if ($message !== null) {
            $this->assertSame($message, $body['message'], $case);
        }
    }

    private function assertDenialLogged(string $reason, ?int $memberId): void
    {
        $records = $this->handler->getRecords();

        $this->assertCount(1, $records);
        $this->assertInstanceOf(LogRecord::class, $records[0]);
        $this->assertSame(Level::Warning, $records[0]->level);
        $this->assertSame($reason, $records[0]->context['reason'] ?? null);
        $this->assertArrayHasKey('memberID', $records[0]->context);
        $this->assertSame($memberId, $records[0]->context['memberID']);
    }
}
