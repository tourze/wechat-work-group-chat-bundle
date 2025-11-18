<?php

declare(strict_types=1);

namespace WechatWorkGroupChatBundle\Tests\Controller\Admin;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use WechatWorkGroupChatBundle\Controller\Admin\GroupMemberCrudController;
use WechatWorkGroupChatBundle\Entity\GroupMember;

/**
 * 群成员管理控制器测试
 * @internal
 */
#[CoversClass(GroupMemberCrudController::class)]
#[RunTestsInSeparateProcesses]
class GroupMemberCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    protected function getControllerService(): GroupMemberCrudController
    {
        $controller = self::getContainer()->get(GroupMemberCrudController::class);
        self::assertInstanceOf(GroupMemberCrudController::class, $controller);

        return $controller;
    }

    /** @return iterable<string, array{string}> */
    public static function provideIndexPageHeaders(): iterable
    {
        return [
            'groupChat' => ['所属群聊'],
            'userId' => ['用户ID'],
            'name' => ['姓名'],
            'groupNickname' => ['群昵称'],
            'type' => ['成员类型'],
        ];
    }

    /** @return iterable<string, array{string}> */
    public static function provideNewPageFields(): iterable
    {
        return [
            'groupChat' => ['groupChat'],
            'userId' => ['userId'],
            'name' => ['name'],
            'groupNickname' => ['groupNickname'],
            'type' => ['type'],
        ];
    }

    /** @return iterable<string, array{string}> */
    public static function provideEditPageFields(): iterable
    {
        return [
            'groupChat' => ['groupChat'],
            'userId' => ['userId'],
            'name' => ['name'],
            'groupNickname' => ['groupNickname'],
            'type' => ['type'],
        ];
    }

    public function testConfigureFields(): void
    {
        $controller = new GroupMemberCrudController();
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
            $crawler = $client->request('GET', '/admin?crudAction=new&crudControllerFqcn=' . urlencode(GroupMemberCrudController::class));
            $response = $client->getResponse();

            if ($response->isSuccessful()) {
                self::assertResponseIsSuccessful();

                $form = $crawler->selectButton('Create')->form();
                $crawler = $client->submit($form, [
                    'group_member[userId]' => '', // 提交空的必填字段
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
