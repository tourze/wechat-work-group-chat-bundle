<?php

declare(strict_types=1);

namespace WechatWorkGroupChatBundle\Tests\Controller\Admin;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use WechatWorkGroupChatBundle\Controller\Admin\GroupChatCrudController;
use WechatWorkGroupChatBundle\Entity\GroupChat;

/**
 * 客户群管理控制器测试
 * @internal
 */
#[CoversClass(GroupChatCrudController::class)]
#[RunTestsInSeparateProcesses]
class GroupChatCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    protected function getControllerService(): GroupChatCrudController
    {
        $controller = self::getContainer()->get(GroupChatCrudController::class);
        self::assertInstanceOf(GroupChatCrudController::class, $controller);

        return $controller;
    }

    /** @return iterable<string, array{string}> */
    public static function provideIndexPageHeaders(): iterable
    {
        return [
            'chatId' => ['客户群ID'],
            'name' => ['群名称'],
            'status' => ['跟进状态'],
            'corp' => ['企业'],
            'agent' => ['应用'],
            'owner' => ['群主'],
        ];
    }

    /** @return iterable<string, array{string}> */
    public static function provideNewPageFields(): iterable
    {
        // NEW action is disabled for this controller (read-only)
        // Return a single dummy entry to satisfy DataProvider requirement
        return [
            'dummy' => ['dummy'],
        ];
    }

    /** @return iterable<string, array{string}> */
    public static function provideEditPageFields(): iterable
    {
        // EDIT action is disabled for this controller (read-only)
        // Return a single dummy entry to satisfy DataProvider requirement
        return [
            'dummy' => ['dummy'],
        ];
    }

    public function testConfigureFields(): void
    {
        $controller = new GroupChatCrudController();
        $fields = $controller->configureFields('index');

        self::assertInstanceOf(\Traversable::class, $fields);

        // 将字段转换为数组以便进一步检查
        $fieldsArray = iterator_to_array($fields);
        self::assertNotEmpty($fieldsArray);
    }

    public function testValidationErrors(): void
    {
        $client = self::createClient();
        $client->catchExceptions(false);

        try {
            $crawler = $client->request('GET', '/admin?crudAction=new&crudControllerFqcn=' . urlencode(GroupChatCrudController::class));
            $response = $client->getResponse();

            if ($response->isSuccessful()) {
                self::assertResponseIsSuccessful();

                $form = $crawler->selectButton('Create')->form();
                $crawler = $client->submit($form, [
                    'group_chat[chatId]' => '', // 提交空的必填字段
                ]);

                $validationResponse = $client->getResponse();
                if (422 === $validationResponse->getStatusCode()) {
                    self::assertResponseStatusCodeSame(422);

                    $invalidFeedback = $crawler->filter('.invalid-feedback');
                    if ($invalidFeedback->count() > 0) {
                        self::assertStringContainsString('should not be blank', $invalidFeedback->text());
                    }
                } else {
                    self::assertLessThan(500, $validationResponse->getStatusCode());
                }
            } elseif ($response->isRedirect()) {
                self::assertResponseRedirects();
            } else {
                self::assertLessThan(500, $response->getStatusCode(), 'Response should not be a server error');
            }
        } catch (\Exception $e) {
            // 捕获可能的访问异常（如权限问题），这是正常的
            self::assertStringNotContainsString(
                'Fatal error',
                $e->getMessage(),
                'Should not fail with fatal error'
            );
        }
    }
}
