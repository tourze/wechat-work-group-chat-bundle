<?php

namespace WechatWorkGroupChatBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\TestCase;
use WechatWorkGroupChatBundle\Entity\GroupChat;
use WechatWorkGroupChatBundle\Repository\GroupChatRepository;

class GroupChatRepositoryTest extends TestCase
{
    public function testRepositoryClass(): void
    {
        // 验证类继承关系
        $reflection = new \ReflectionClass(GroupChatRepository::class);
        $this->assertTrue($reflection->isSubclassOf(ServiceEntityRepository::class));
    }

    public function testEntityClass(): void
    {
        // 验证仓储管理的实体类
        $reflection = new \ReflectionClass(GroupChatRepository::class);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();
        
        // 检查构造函数参数
        $this->assertCount(1, $parameters);
        $this->assertEquals('registry', $parameters[0]->getName());
    }

    public function testRepositoryMethods(): void
    {
        // 验证继承的方法存在
        $methods = get_class_methods(GroupChatRepository::class);
        
        $this->assertContains('find', $methods);
        $this->assertContains('findOneBy', $methods);
        $this->assertContains('findAll', $methods);
        $this->assertContains('findBy', $methods);
    }

    public function testClassDocBlock(): void
    {
        $reflection = new \ReflectionClass(GroupChatRepository::class);
        $docComment = $reflection->getDocComment();
        
        // 验证文档块包含正确的方法声明
        $this->assertStringContainsString('@method GroupChat|null find', $docComment);
        $this->assertStringContainsString('@method GroupChat|null findOneBy', $docComment);
        $this->assertStringContainsString('@method GroupChat[]    findAll', $docComment);
        $this->assertStringContainsString('@method GroupChat[]    findBy', $docComment);
    }

    public function testConstructorLogic(): void
    {
        $reflection = new \ReflectionClass(GroupChatRepository::class);
        $constructor = $reflection->getConstructor();
        
        // 获取构造函数的代码
        $fileName = $reflection->getFileName();
        $startLine = $constructor->getStartLine();
        $endLine = $constructor->getEndLine();
        
        $source = file($fileName);
        $constructorCode = implode('', array_slice($source, $startLine - 1, $endLine - $startLine + 1));
        
        // 验证构造函数调用了父类构造函数并传递了正确的实体类
        $this->assertStringContainsString('parent::__construct', $constructorCode);
        $this->assertStringContainsString('GroupChat::class', $constructorCode);
    }
}