<?php

namespace WechatWorkGroupChatBundle\Tests\Request;

use HttpClientBundle\Request\ApiRequest;
use PHPUnit\Framework\TestCase;
use WechatWorkGroupChatBundle\Request\GetGroupChatDetailRequest;

/**
 * GetGroupChatDetailRequest 测试
 */
class GetGroupChatDetailRequestTest extends TestCase
{
    public function test_inheritance(): void
    {
        // 测试继承关系
        $request = new GetGroupChatDetailRequest();
        $this->assertInstanceOf(ApiRequest::class, $request);
        $this->assertTrue(method_exists($request, 'getAgent'));
        $this->assertTrue(method_exists($request, 'setAgent'));
    }

    public function test_chatId_setterAndGetter(): void
    {
        // 测试群聊ID设置和获取
        $request = new GetGroupChatDetailRequest();
        $chatId = 'wrOgQhDgAAMYQiS5ol9G7gK9JVAAAA';
        
        $request->setChatId($chatId);
        $this->assertSame($chatId, $request->getChatId());
    }

    public function test_needName_setterAndGetter(): void
    {
        // 测试是否需要返回群成员名字设置和获取
        $request = new GetGroupChatDetailRequest();
        
        $request->setNeedName(true);
        $this->assertTrue($request->isNeedName());
        
        $request->setNeedName(false);
        $this->assertFalse($request->isNeedName());
    }

    public function test_needName_withNull(): void
    {
        // 测试null值的needName - 由于源代码是bool类型，不支持null
        $request = new GetGroupChatDetailRequest();
        // 默认值应该是false
        $this->assertFalse($request->isNeedName());
    }

    public function test_requestPath(): void
    {
        // 测试请求路径
        $request = new GetGroupChatDetailRequest();
        $this->assertSame('/cgi-bin/externalcontact/groupchat/get', $request->getRequestPath());
    }

    public function test_requestOptions_withAllParameters(): void
    {
        // 测试全部参数的请求选项
        $request = new GetGroupChatDetailRequest();
        $chatId = 'wrOgQhDgAAMYQiS5ol9G7gK9JVBBBB';
        $needName = true;
        
        $request->setChatId($chatId);
        $request->setNeedName($needName);
        
        $expected = [
            'json' => [
                'chat_id' => $chatId,
                'need_name' => $needName ? 1 : 0,
            ],
        ];
        
        $this->assertSame($expected, $request->getRequestOptions());
    }

    public function test_requestOptions_withOnlyChatId(): void
    {
        // 测试仅有群聊ID的请求选项 - 会包含默认的need_name
        $request = new GetGroupChatDetailRequest();
        $chatId = 'wrOgQhDgAAMYQiS5ol9G7gK9JVCCCC';
        
        $request->setChatId($chatId);
        
        $expected = [
            'json' => [
                'chat_id' => $chatId,
                'need_name' => 0, // 默认值false转换为0
            ],
        ];
        
        $this->assertSame($expected, $request->getRequestOptions());
    }

    public function test_requestOptions_withNeedNameFalse(): void
    {
        // 测试needName为false的请求选项
        $request = new GetGroupChatDetailRequest();
        $chatId = 'wrOgQhDgAAMYQiS5ol9G7gK9JVDDDD';
        $needName = false;
        
        $request->setChatId($chatId);
        $request->setNeedName($needName);
        
        $expected = [
            'json' => [
                'chat_id' => $chatId,
                'need_name' => 0,
            ],
        ];
        
        $this->assertSame($expected, $request->getRequestOptions());
    }

    public function test_requestOptions_withNeedNameTrue(): void
    {
        // 测试needName为true的请求选项
        $request = new GetGroupChatDetailRequest();
        $chatId = 'wrOgQhDgAAMYQiS5ol9G7gK9JVEEEE';
        $needName = true;
        
        $request->setChatId($chatId);
        $request->setNeedName($needName);
        
        $expected = [
            'json' => [
                'chat_id' => $chatId,
                'need_name' => 1,
            ],
        ];
        
        $this->assertSame($expected, $request->getRequestOptions());
    }

    public function test_requestOptions_withNullNeedName(): void
    {
        // 由于源代码needName是bool类型，测试默认值行为
        $request = new GetGroupChatDetailRequest();
        $chatId = 'wrOgQhDgAAMYQiS5ol9G7gK9JVFFFF';
        
        $request->setChatId($chatId);
        // 不设置needName，使用默认值
        
        $expected = [
            'json' => [
                'chat_id' => $chatId,
                'need_name' => 0, // 默认false转换为0
            ],
        ];
        
        $this->assertSame($expected, $request->getRequestOptions());
    }

    public function test_requestOptionsStructure(): void
    {
        // 测试请求选项结构
        $request = new GetGroupChatDetailRequest();
        $request->setChatId('test_chat_id');
        $request->setNeedName(true);
        
        $options = $request->getRequestOptions();
        $this->assertArrayHasKey('json', $options);
        $this->assertArrayHasKey('chat_id', $options['json']);
        $this->assertArrayHasKey('need_name', $options['json']);
        $this->assertCount(2, $options['json']);
    }

    public function test_businessScenario_getGroupDetailWithNames(): void
    {
        // 测试业务场景：获取群详情包含成员名字
        $request = new GetGroupChatDetailRequest();
        $customerGroupId = 'wrOgQhDgAAMYQiS5ol9G7gK9JV001';
        
        $request->setChatId($customerGroupId);
        $request->setNeedName(true); // 需要成员名字
        
        $this->assertSame($customerGroupId, $request->getChatId());
        $this->assertTrue($request->isNeedName());
        
        $options = $request->getRequestOptions();
        $this->assertSame($customerGroupId, $options['json']['chat_id']);
        $this->assertSame(1, $options['json']['need_name']);
        
        // 验证API路径正确
        $this->assertSame('/cgi-bin/externalcontact/groupchat/get', $request->getRequestPath());
    }

    public function test_businessScenario_getBasicGroupInfo(): void
    {
        // 测试业务场景：获取基本群信息（不需要成员名字）
        $request = new GetGroupChatDetailRequest();
        $groupId = 'wrOgQhDgAAMYQiS5ol9G7gK9JV002';
        
        $request->setChatId($groupId);
        $request->setNeedName(false); // 不需要成员名字
        
        $options = $request->getRequestOptions();
        $this->assertSame($groupId, $options['json']['chat_id']);
        $this->assertSame(0, $options['json']['need_name']);
    }

    public function test_businessScenario_simpleGroupQuery(): void
    {
        // 测试业务场景：简单群查询（仅提供群ID）
        $request = new GetGroupChatDetailRequest();
        $simpleGroupId = 'wrOgQhDgAAMYQiS5ol9G7gK9JV003';
        
        $request->setChatId($simpleGroupId);
        // 不设置needName，保持默认值
        
        $this->assertSame($simpleGroupId, $request->getChatId());
        $this->assertFalse($request->isNeedName()); // 默认为false
        
        $options = $request->getRequestOptions();
        $this->assertSame($simpleGroupId, $options['json']['chat_id']);
        $this->assertSame(0, $options['json']['need_name']); // 默认值0
    }

    public function test_chatIdSpecialCharacters(): void
    {
        // 测试群聊ID特殊字符
        $request = new GetGroupChatDetailRequest();
        $specialChatId = 'wr-OgQh_DgAAMYQi.S5ol9G7gK9JV@test';
        
        $request->setChatId($specialChatId);
        
        $this->assertSame($specialChatId, $request->getChatId());
        
        $options = $request->getRequestOptions();
        $this->assertSame($specialChatId, $options['json']['chat_id']);
    }

    public function test_longChatId(): void
    {
        // 测试长群聊ID
        $request = new GetGroupChatDetailRequest();
        $longChatId = str_repeat('wrOgQhDgAAMYQi', 5) . 'END';
        
        $request->setChatId($longChatId);
        
        $this->assertSame($longChatId, $request->getChatId());
    }

    public function test_multipleSetOperations(): void
    {
        // 测试多次设置值
        $request = new GetGroupChatDetailRequest();
        
        $firstChatId = 'first_chat_id';
        $firstNeedName = true;
        $secondChatId = 'second_chat_id';
        $secondNeedName = false;
        
        $request->setChatId($firstChatId);
        $request->setNeedName($firstNeedName);
        
        $this->assertSame($firstChatId, $request->getChatId());
        $this->assertTrue($request->isNeedName());
        
        $request->setChatId($secondChatId);
        $request->setNeedName($secondNeedName);
        
        $this->assertSame($secondChatId, $request->getChatId());
        $this->assertFalse($request->isNeedName());
        
        $options = $request->getRequestOptions();
        $this->assertSame($secondChatId, $options['json']['chat_id']);
        $this->assertSame(0, $options['json']['need_name']);
    }

    public function test_needNameBooleanToIntConversion(): void
    {
        // 测试needName布尔值转整数
        $request = new GetGroupChatDetailRequest();
        $chatId = 'test_conversion_chat';
        
        $request->setChatId($chatId);
        
        // 测试true转换为1
        $request->setNeedName(true);
        $options = $request->getRequestOptions();
        $this->assertSame(1, $options['json']['need_name']);
        $this->assertIsInt($options['json']['need_name']);
        
        // 测试false转换为0
        $request->setNeedName(false);
        $options = $request->getRequestOptions();
        $this->assertSame(0, $options['json']['need_name']);
        $this->assertIsInt($options['json']['need_name']);
    }

    public function test_idempotentMethodCalls(): void
    {
        // 测试方法调用是幂等的
        $request = new GetGroupChatDetailRequest();
        $chatId = 'idempotent_chat_id';
        $needName = true;
        
        $request->setChatId($chatId);
        $request->setNeedName($needName);
        
        // 多次调用应该返回相同结果
        $this->assertSame($chatId, $request->getChatId());
        $this->assertSame($chatId, $request->getChatId());
        
        $this->assertTrue($request->isNeedName());
        $this->assertTrue($request->isNeedName());
        
        $options1 = $request->getRequestOptions();
        $options2 = $request->getRequestOptions();
        $this->assertSame($options1, $options2);
        
        $path1 = $request->getRequestPath();
        $path2 = $request->getRequestPath();
        $this->assertSame($path1, $path2);
    }

    public function test_immutableRequestOptions(): void
    {
        // 测试获取请求选项不会修改原始数据
        $request = new GetGroupChatDetailRequest();
        $originalChatId = 'original_chat_id';
        $originalNeedName = true;
        
        $request->setChatId($originalChatId);
        $request->setNeedName($originalNeedName);
        
        $options1 = $request->getRequestOptions();
        $options2 = $request->getRequestOptions();
        
        // 修改返回的数组不应影响原始数据
        $options1['json']['chat_id'] = 'modified_chat_id';
        $options1['json']['need_name'] = 0;
        $options1['json']['new_field'] = 'new_value';
        $options1['new_key'] = 'new_value';
        
        $this->assertSame($originalChatId, $request->getChatId());
        $this->assertTrue($request->isNeedName());
        
        $this->assertSame($originalChatId, $options2['json']['chat_id']);
        $this->assertSame(1, $options2['json']['need_name']);
        $this->assertArrayNotHasKey('new_field', $options2['json']);
        $this->assertArrayNotHasKey('new_key', $options2);
    }

    public function test_agentAwareTrait(): void
    {
        // 测试AgentAware特性
        $request = new GetGroupChatDetailRequest();
        
        // 测试trait提供的方法存在
        $this->assertTrue(method_exists($request, 'getAgent'));
        $this->assertTrue(method_exists($request, 'setAgent'));
        $this->assertTrue(is_callable([$request, 'getAgent']));
        $this->assertTrue(is_callable([$request, 'setAgent']));
    }

    public function test_emptyStringChatId(): void
    {
        // 测试空字符串群聊ID
        $request = new GetGroupChatDetailRequest();
        $request->setChatId('');
        
        $this->assertSame('', $request->getChatId());
        
        $options = $request->getRequestOptions();
        $this->assertSame('', $options['json']['chat_id']);
    }

    public function test_requestParametersCorrectness(): void
    {
        // 测试请求参数正确性
        $request = new GetGroupChatDetailRequest();
        $chatId = 'param_test_chat_id';
        $needName = true;
        
        $request->setChatId($chatId);
        $request->setNeedName($needName);
        
        $options = $request->getRequestOptions();
        
        // 验证参数结构正确
        $this->assertArrayHasKey('json', $options);
        $this->assertArrayHasKey('chat_id', $options['json']);
        $this->assertArrayHasKey('need_name', $options['json']);
        $this->assertSame($chatId, $options['json']['chat_id']);
        $this->assertSame(1, $options['json']['need_name']);
        
        // 验证只包含设置的参数
        $this->assertCount(1, $options);
        $this->assertCount(2, $options['json']);
    }

    public function test_apiEndpointCorrectness(): void
    {
        // 测试API端点正确性
        $request = new GetGroupChatDetailRequest();
        $path = $request->getRequestPath();
        
        $this->assertStringContainsString('externalcontact', $path);
        $this->assertStringContainsString('groupchat', $path);
        $this->assertStringContainsString('get', $path);
        $this->assertStringStartsWith('/cgi-bin/', $path);
        $this->assertStringEndsWith('/get', $path);
    }

    public function test_jsonRequestFormat(): void
    {
        // 测试JSON请求格式
        $request = new GetGroupChatDetailRequest();
        $chatId = 'json_format_chat_id';
        $needName = false;
        
        $request->setChatId($chatId);
        $request->setNeedName($needName);
        
        $options = $request->getRequestOptions();
        
        // 验证使用json而不是query格式
        $this->assertArrayHasKey('json', $options);
        $this->assertArrayNotHasKey('query', $options);
        $this->assertArrayNotHasKey('body', $options);
        $this->assertArrayNotHasKey('form_params', $options);
    }

    public function test_businessScenario_customerServiceGroupDetail(): void
    {
        // 测试业务场景：客服群详情查询
        $request = new GetGroupChatDetailRequest();
        $customerServiceGroupId = 'wrOgQhDgAAMYQiS5ol9G7gK9JV_CS';
        
        $request->setChatId($customerServiceGroupId);
        $request->setNeedName(true); // 客服需要知道成员名字
        
        $this->assertSame($customerServiceGroupId, $request->getChatId());
        $this->assertTrue($request->isNeedName());
        
        // 验证客服群详情查询的API路径
        $this->assertStringContainsString('groupchat/get', $request->getRequestPath());
    }

    public function test_businessScenario_salesGroupAnalysis(): void
    {
        // 测试业务场景：销售群分析
        $request = new GetGroupChatDetailRequest();
        $salesGroupId = 'wrOgQhDgAAMYQiS5ol9G7gK9JV_SALES';
        
        $request->setChatId($salesGroupId);
        $request->setNeedName(false); // 仅需要群基本信息
        
        $options = $request->getRequestOptions();
        $this->assertSame($salesGroupId, $options['json']['chat_id']);
        $this->assertSame(0, $options['json']['need_name']);
    }

    public function test_businessScenario_groupMemberManagement(): void
    {
        // 测试业务场景：群成员管理
        $request = new GetGroupChatDetailRequest();
        $managementGroupId = 'wrOgQhDgAAMYQiS5ol9G7gK9JV_MGT';
        
        $request->setChatId($managementGroupId);
        $request->setNeedName(true); // 管理需要详细的成员信息
        
        $this->assertSame($managementGroupId, $request->getChatId());
        $this->assertTrue($request->isNeedName());
        
        // 验证支持群成员管理的参数格式
        $options = $request->getRequestOptions();
        $this->assertArrayHasKey('chat_id', $options['json']);
        $this->assertArrayHasKey('need_name', $options['json']);
        $this->assertSame(1, $options['json']['need_name']);
    }

    public function test_requestDataIntegrity(): void
    {
        // 测试请求数据完整性
        $request = new GetGroupChatDetailRequest();
        $chatId = 'integrity_test_chat_id';
        $needName = true;
        
        $request->setChatId($chatId);
        $request->setNeedName($needName);
        
        $options = $request->getRequestOptions();
        
        // 验证请求数据结构完整性
        $this->assertArrayHasKey('json', $options);
        $this->assertSame($chatId, $options['json']['chat_id']);
        $this->assertSame(1, $options['json']['need_name']);
        
        // 验证只包含必要的字段
        $this->assertCount(1, $options);
        $this->assertCount(2, $options['json']);
    }

    public function test_parameterValidation(): void
    {
        // 测试参数验证
        $request = new GetGroupChatDetailRequest();
        
        // 测试群聊ID是必需的字符串
        $chatId = 'validation_test_chat_id';
        $request->setChatId($chatId);
        $this->assertIsString($request->getChatId());
        $this->assertSame($chatId, $request->getChatId());
        
        // 测试needName是可选的布尔值
        $request->setNeedName(true);
        $this->assertIsBool($request->isNeedName());
        $this->assertTrue($request->isNeedName());
        
        $request->setNeedName(false);
        $this->assertIsBool($request->isNeedName());
        $this->assertFalse($request->isNeedName());
        
        // 默认值测试
        $newRequest = new GetGroupChatDetailRequest();
        $this->assertIsBool($newRequest->isNeedName());
        $this->assertFalse($newRequest->isNeedName());
    }
} 