<?php

namespace WechatWorkGroupChatBundle\Tests\Request;

use HttpClientBundle\Request\ApiRequest;
use PHPUnit\Framework\TestCase;
use WechatWorkGroupChatBundle\Request\GetGroupChatListRequest;

/**
 * GetGroupChatListRequest 测试
 */
class GetGroupChatListRequestTest extends TestCase
{
    public function test_inheritance(): void
    {
        // 测试继承关系
        $request = new GetGroupChatListRequest();
        $this->assertInstanceOf(ApiRequest::class, $request);
    }


    public function test_getRequestPath(): void
    {
        // 测试请求路径
        $request = new GetGroupChatListRequest();
        $this->assertSame('/cgi-bin/externalcontact/groupchat/list', $request->getRequestPath());
    }

    public function test_getRequestMethod(): void
    {
        // 测试请求方法
        $request = new GetGroupChatListRequest();
        $this->assertSame('POST', $request->getRequestMethod());
    }

    public function test_limit_setterAndGetter(): void
    {
        // 测试限制数量设置和获取
        $request = new GetGroupChatListRequest();
        $limit = 100;
        
        $request->setLimit($limit);
        $this->assertSame($limit, $request->getLimit());
    }

    public function test_limit_withMinValue(): void
    {
        // 测试最小值限制
        $request = new GetGroupChatListRequest();
        $request->setLimit(1);
        
        $this->assertSame(1, $request->getLimit());
    }

    public function test_limit_withMaxValue(): void
    {
        // 测试最大值限制
        $request = new GetGroupChatListRequest();
        $request->setLimit(1000);
        
        $this->assertSame(1000, $request->getLimit());
    }

    public function test_statusFilter_setterAndGetter(): void
    {
        // 测试状态过滤器设置和获取
        $request = new GetGroupChatListRequest();
        
        // 测试所有状态
        $request->setStatusFilter(0);
        $this->assertSame(0, $request->getStatusFilter());
        
        // 测试离职待继承
        $request->setStatusFilter(1);
        $this->assertSame(1, $request->getStatusFilter());
        
        // 测试离职继承中
        $request->setStatusFilter(2);
        $this->assertSame(2, $request->getStatusFilter());
        
        // 测试离职继承完成
        $request->setStatusFilter(3);
        $this->assertSame(3, $request->getStatusFilter());
    }

    public function test_statusFilter_withNull(): void
    {
        // 测试null状态过滤器
        $request = new GetGroupChatListRequest();
        $request->setStatusFilter(null);
        
        $this->assertNull($request->getStatusFilter());
    }

    public function test_ownerUserIds_setterAndGetter(): void
    {
        // 测试群主用户ID列表设置和获取
        $request = new GetGroupChatListRequest();
        $userIds = ['user1', 'user2', 'user3'];
        
        $request->setOwnerUserIds($userIds);
        $this->assertSame($userIds, $request->getOwnerUserIds());
    }

    public function test_ownerUserIds_withEmptyArray(): void
    {
        // 测试空数组群主用户ID
        $request = new GetGroupChatListRequest();
        $request->setOwnerUserIds([]);
        
        $this->assertSame([], $request->getOwnerUserIds());
    }

    public function test_ownerUserIds_withNull(): void
    {
        // 测试null群主用户ID
        $request = new GetGroupChatListRequest();
        $request->setOwnerUserIds(null);
        
        $this->assertNull($request->getOwnerUserIds());
    }

    public function test_cursor_setterAndGetter(): void
    {
        // 测试游标设置和获取
        $request = new GetGroupChatListRequest();
        $cursor = 'cursor_token_123';
        
        $request->setCursor($cursor);
        $this->assertSame($cursor, $request->getCursor());
    }

    public function test_cursor_withNull(): void
    {
        // 测试null游标
        $request = new GetGroupChatListRequest();
        $request->setCursor(null);
        
        $this->assertNull($request->getCursor());
    }

    public function test_cursor_withEmptyString(): void
    {
        // 测试空字符串游标
        $request = new GetGroupChatListRequest();
        $request->setCursor('');
        
        $this->assertSame('', $request->getCursor());
    }

    public function test_defaultValues(): void
    {
        // 测试默认值
        $request = new GetGroupChatListRequest();
        
        $this->assertNull($request->getStatusFilter());
        $this->assertSame([], $request->getOwnerUserIds());
        $this->assertNull($request->getCursor());
    }

    public function test_getRequestOptions_withMinimalParams(): void
    {
        // 测试最小参数的请求选项
        $request = new GetGroupChatListRequest();
        $request->setLimit(100);
        
        $options = $request->getRequestOptions();
        $this->assertArrayHasKey('json', $options);
        $this->assertArrayHasKey('limit', $options['json']);
        $this->assertSame(100, $options['json']['limit']);
        $this->assertArrayNotHasKey('status_filter', $options['json']);
        $this->assertArrayHasKey('owner_filter', $options['json']);
        $this->assertSame([], $options['json']['owner_filter']['userid_list']);
        $this->assertArrayNotHasKey('cursor', $options['json']);
    }

    public function test_getRequestOptions_withAllParams(): void
    {
        // 测试所有参数的请求选项
        $request = new GetGroupChatListRequest();
        $request->setLimit(500);
        $request->setStatusFilter(1);
        $request->setOwnerUserIds(['owner1', 'owner2']);
        $request->setCursor('test_cursor');
        
        $options = $request->getRequestOptions();
        
        $this->assertArrayHasKey('limit', $options['json']);
        $this->assertArrayHasKey('status_filter', $options['json']);
        $this->assertArrayHasKey('owner_filter', $options['json']);
        $this->assertArrayHasKey('cursor', $options['json']);
        
        $this->assertSame(500, $options['json']['limit']);
        $this->assertSame(1, $options['json']['status_filter']);
        $this->assertArrayHasKey('userid_list', $options['json']['owner_filter']);
        $this->assertSame(['owner1', 'owner2'], $options['json']['owner_filter']['userid_list']);
        $this->assertSame('test_cursor', $options['json']['cursor']);
    }

    public function test_getRequestOptions_withNullStatusFilter(): void
    {
        // 测试null状态过滤器的请求选项
        $request = new GetGroupChatListRequest();
        $request->setLimit(200);
        $request->setStatusFilter(null);
        
        $options = $request->getRequestOptions();
        
        $this->assertArrayNotHasKey('status_filter', $options['json']);
    }

    public function test_getRequestOptions_withNullOwnerUserIds(): void
    {
        // 测试null群主用户ID的请求选项
        $request = new GetGroupChatListRequest();
        $request->setLimit(300);
        $request->setOwnerUserIds(null);
        
        $options = $request->getRequestOptions();
        
        $this->assertArrayNotHasKey('owner_filter', $options['json']);
    }

    public function test_getRequestOptions_withEmptyOwnerUserIds(): void
    {
        // 测试空数组群主用户ID的请求选项
        $request = new GetGroupChatListRequest();
        $request->setLimit(400);
        $request->setOwnerUserIds([]);
        
        $options = $request->getRequestOptions();
        
        $this->assertArrayHasKey('owner_filter', $options['json']);
        $this->assertSame([], $options['json']['owner_filter']['userid_list']);
    }

    public function test_getRequestOptions_structure(): void
    {
        // 测试请求选项结构
        $request = new GetGroupChatListRequest();
        $request->setLimit(150);
        $request->setStatusFilter(2);
        
        $options = $request->getRequestOptions();
        $this->assertCount(1, $options);
        $this->assertArrayHasKey('json', $options);
        $this->assertGreaterThanOrEqual(2, count($options['json']));
    }

    public function test_inheritsFromApiRequest(): void
    {
        // 测试继承自ApiRequest的核心方法
        $request = new GetGroupChatListRequest();
        
        // 验证是ApiRequest的实例
        $this->assertInstanceOf(ApiRequest::class, $request);
    }

    public function test_businessScenario_getAllGroupChats(): void
    {
        // 测试业务场景：获取所有客户群
        $request = new GetGroupChatListRequest();
        $request->setLimit(100);
        $request->setStatusFilter(0); // 所有列表
        
        $this->assertSame('/cgi-bin/externalcontact/groupchat/list', $request->getRequestPath());
        $this->assertSame('POST', $request->getRequestMethod());
        $this->assertSame(100, $request->getLimit());
        $this->assertSame(0, $request->getStatusFilter());
        
        $options = $request->getRequestOptions();
        $this->assertSame(0, $options['json']['status_filter']);
    }

    public function test_businessScenario_getInheritancePendingGroups(): void
    {
        // 测试业务场景：获取离职待继承群聊
        $request = new GetGroupChatListRequest();
        $request->setLimit(50);
        $request->setStatusFilter(1); // 离职待继承
        
        $options = $request->getRequestOptions();
        $this->assertSame(1, $options['json']['status_filter']);
        
        // 验证API路径符合获取群聊列表要求
        $this->assertStringContainsString('groupchat', $request->getRequestPath());
        $this->assertStringContainsString('list', $request->getRequestPath());
    }

    public function test_businessScenario_getGroupsByOwner(): void
    {
        // 测试业务场景：获取特定群主的群聊
        $request = new GetGroupChatListRequest();
        $request->setLimit(200);
        $request->setOwnerUserIds(['manager001', 'manager002']);
        
        $this->assertSame(['manager001', 'manager002'], $request->getOwnerUserIds());
        
        $options = $request->getRequestOptions();
        $this->assertArrayHasKey('owner_filter', $options['json']);
        $this->assertSame(['manager001', 'manager002'], $options['json']['owner_filter']['userid_list']);
    }

    public function test_businessScenario_paginationQuery(): void
    {
        // 测试业务场景：分页查询
        $request = new GetGroupChatListRequest();
        $request->setLimit(1000); // 最大限制
        $request->setCursor('page_2_cursor_token');
        
        $this->assertSame(1000, $request->getLimit());
        $this->assertSame('page_2_cursor_token', $request->getCursor());
        
        $options = $request->getRequestOptions();
        $this->assertSame(1000, $options['json']['limit']);
        $this->assertSame('page_2_cursor_token', $options['json']['cursor']);
    }

    public function test_businessScenario_inheritanceInProgress(): void
    {
        // 测试业务场景：继承进行中的群聊
        $request = new GetGroupChatListRequest();
        $request->setLimit(75);
        $request->setStatusFilter(2); // 离职继承中
        $request->setOwnerUserIds(['hr_user001']);
        
        $options = $request->getRequestOptions();
        $this->assertSame(2, $options['json']['status_filter']);
        $this->assertSame(['hr_user001'], $options['json']['owner_filter']['userid_list']);
        
        // 验证使用POST方法符合企业微信API规范
        $this->assertSame('POST', $request->getRequestMethod());
    }

    public function test_businessScenario_inheritanceCompleted(): void
    {
        // 测试业务场景：继承完成的群聊
        $request = new GetGroupChatListRequest();
        $request->setLimit(300);
        $request->setStatusFilter(3); // 离职继承完成
        
        $options = $request->getRequestOptions();
        $this->assertSame(3, $options['json']['status_filter']);
        
        $this->assertStringContainsString('externalcontact', $request->getRequestPath());
    }

    public function test_multipleSetCalls(): void
    {
        // 测试多次设置值
        $request = new GetGroupChatListRequest();
        
        $request->setLimit(100);
        $request->setStatusFilter(1);
        $request->setOwnerUserIds(['user1']);
        $request->setCursor('cursor1');
        
        $this->assertSame(100, $request->getLimit());
        $this->assertSame(1, $request->getStatusFilter());
        $this->assertSame(['user1'], $request->getOwnerUserIds());
        $this->assertSame('cursor1', $request->getCursor());
        
        // 重新设置
        $request->setLimit(200);
        $request->setStatusFilter(2);
        $request->setOwnerUserIds(['user2', 'user3']);
        $request->setCursor('cursor2');
        
        $this->assertSame(200, $request->getLimit());
        $this->assertSame(2, $request->getStatusFilter());
        $this->assertSame(['user2', 'user3'], $request->getOwnerUserIds());
        $this->assertSame('cursor2', $request->getCursor());
        
        $options = $request->getRequestOptions();
        $this->assertSame(200, $options['json']['limit']);
        $this->assertSame(2, $options['json']['status_filter']);
        $this->assertSame(['user2', 'user3'], $options['json']['owner_filter']['userid_list']);
        $this->assertSame('cursor2', $options['json']['cursor']);
    }

    public function test_resetToNull(): void
    {
        // 测试重置为null
        $request = new GetGroupChatListRequest();
        
        $request->setLimit(500);
        $request->setStatusFilter(1);
        $request->setOwnerUserIds(['user1']);
        $request->setCursor('cursor');
        
        // 重置为null（除了limit，因为它不能为null）
        $request->setStatusFilter(null);
        $request->setOwnerUserIds(null);
        $request->setCursor(null);
        
        $this->assertNull($request->getStatusFilter());
        $this->assertNull($request->getOwnerUserIds());
        $this->assertNull($request->getCursor());
        
        $options = $request->getRequestOptions();
        $this->assertArrayNotHasKey('status_filter', $options['json']);
        $this->assertArrayNotHasKey('owner_filter', $options['json']);
        $this->assertArrayNotHasKey('cursor', $options['json']);
        $this->assertArrayHasKey('limit', $options['json']); // limit仍然存在
    }

    public function test_largeUserIdsList(): void
    {
        // 测试大量用户ID列表
        $request = new GetGroupChatListRequest();
        $largeUserIds = [];
        for ($i = 1; $i <= 100; $i++) {
            $largeUserIds[] = "user_{$i}";
        }
        
        $request->setLimit(50);
        $request->setOwnerUserIds($largeUserIds);
        
        $this->assertSame($largeUserIds, $request->getOwnerUserIds());
        $this->assertCount(100, $request->getOwnerUserIds());
        
        $options = $request->getRequestOptions();
        $this->assertSame($largeUserIds, $options['json']['owner_filter']['userid_list']);
    }

    public function test_requestOptionsDoesNotModifyOriginalData(): void
    {
        // 测试获取请求选项不会修改原始数据
        $request = new GetGroupChatListRequest();
        $originalUserIds = ['original_user1', 'original_user2'];
        $originalCursor = 'original_cursor';
        
        $request->setLimit(250);
        $request->setStatusFilter(1);
        $request->setOwnerUserIds($originalUserIds);
        $request->setCursor($originalCursor);
        
        $options1 = $request->getRequestOptions();
        $options2 = $request->getRequestOptions();
        
        // 修改返回的数组不应影响原始数据
        $options1['json']['limit'] = 999;
        $options1['json']['status_filter'] = 9;
        $options1['json']['owner_filter']['userid_list'] = ['modified_user'];
        $options1['json']['cursor'] = 'modified_cursor';
        
        $this->assertSame(250, $request->getLimit());
        $this->assertSame(1, $request->getStatusFilter());
        $this->assertSame($originalUserIds, $request->getOwnerUserIds());
        $this->assertSame($originalCursor, $request->getCursor());
        
        $this->assertSame(250, $options2['json']['limit']);
        $this->assertSame(1, $options2['json']['status_filter']);
        $this->assertSame($originalUserIds, $options2['json']['owner_filter']['userid_list']);
        $this->assertSame($originalCursor, $options2['json']['cursor']);
    }

    public function test_immutableBehavior(): void
    {
        // 测试不可变行为
        $request = new GetGroupChatListRequest();
        $userIds = ['test_user'];
        $cursor = 'test_cursor';
        
        $request->setLimit(123);
        $request->setStatusFilter(2);
        $request->setOwnerUserIds($userIds);
        $request->setCursor($cursor);
        
        $options = $request->getRequestOptions();
        
        // 修改选项不应影响request对象
        $options['json']['limit'] = 456;
        $options['json']['status_filter'] = 9;
        $options['json']['owner_filter']['userid_list'] = ['changed_user'];
        $options['json']['cursor'] = 'changed_cursor';
        $options['json']['new_param'] = 'new_value';
        
        $this->assertSame(123, $request->getLimit());
        $this->assertSame(2, $request->getStatusFilter());
        $this->assertSame($userIds, $request->getOwnerUserIds());
        $this->assertSame($cursor, $request->getCursor());
        
        $newOptions = $request->getRequestOptions();
        $this->assertSame(123, $newOptions['json']['limit']);
        $this->assertSame(2, $newOptions['json']['status_filter']);
        $this->assertSame($userIds, $newOptions['json']['owner_filter']['userid_list']);
        $this->assertSame($cursor, $newOptions['json']['cursor']);
        $this->assertArrayNotHasKey('new_param', $newOptions['json']);
    }

    public function test_methodCallsAreIdempotent(): void
    {
        // 测试方法调用是幂等的
        $request = new GetGroupChatListRequest();
        $request->setLimit(777);
        $request->setStatusFilter(3);
        $request->setOwnerUserIds(['idempotent_user']);
        $request->setCursor('idempotent_cursor');
        
        // 多次调用应该返回相同结果
        $path1 = $request->getRequestPath();
        $path2 = $request->getRequestPath();
        $this->assertSame($path1, $path2);
        
        $method1 = $request->getRequestMethod();
        $method2 = $request->getRequestMethod();
        $this->assertSame($method1, $method2);
        
        $options1 = $request->getRequestOptions();
        $options2 = $request->getRequestOptions();
        $this->assertSame($options1, $options2);
        
        $limit1 = $request->getLimit();
        $limit2 = $request->getLimit();
        $this->assertSame($limit1, $limit2);
        
        $status1 = $request->getStatusFilter();
        $status2 = $request->getStatusFilter();
        $this->assertSame($status1, $status2);
    }

} 