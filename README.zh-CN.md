# wechat-work-group-chat-bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/wechat-work-group-chat-bundle.svg?style=flat-square)]
(https://packagist.org/packages/tourze/wechat-work-group-chat-bundle)
[![License](https://img.shields.io/packagist/l/tourze/wechat-work-group-chat-bundle.svg?style=flat-square)]
(https://packagist.org/packages/tourze/wechat-work-group-chat-bundle)
[![PHP Version](https://img.shields.io/packagist/dependency-v/tourze/wechat-work-group-chat-bundle/php.svg?style=flat-square)]
(https://packagist.org/packages/tourze/wechat-work-group-chat-bundle)
[![Build Status](https://img.shields.io/github/actions/workflow/status/tourze/monorepo/tests.yml?branch=master&style=flat-square)]
(https://github.com/tourze/monorepo/actions)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/monorepo?style=flat-square)]
(https://codecov.io/gh/tourze/monorepo)

用于管理企业微信客户群的 Symfony Bundle。该 Bundle 提供了企业微信客户群数据的实体、仓储和同步功能。

## 目录

- [功能特性](#功能特性)
- [依赖要求](#依赖要求)
- [安装](#安装)
- [配置](#配置)
- [快速开始](#快速开始)
- [使用方法](#使用方法)
  - [实体](#实体)
  - [控制台命令](#控制台命令)
- [仓储类](#仓储类)
- [高级用法](#高级用法)
  - [自定义仓储方法](#自定义仓储方法)
  - [事件集成](#事件集成)
  - [消息处理器](#消息处理器)
  - [枚举类型](#枚举类型)
- [API 集成](#api-集成)
- [开发](#开发)
- [贡献](#贡献)
- [许可证](#许可证)
- [文档](#文档)

## 功能特性

- 客户群和成员实体管理
- 自动从企业微信 API 同步客户群数据
- 支持客户群状态跟踪（正常、离职、继承中、继承完成）
- 集成消息队列进行异步处理

## 依赖要求

- PHP 8.1+
- Symfony 6.4+
- Doctrine ORM
- Symfony Messenger

## 安装

```bash
composer require tourze/wechat-work-group-chat-bundle
```

## 配置

该 Bundle 在安装时会自动配置。基本使用无需额外配置。

## 快速开始

```php
<?php

use WechatWorkGroupChatBundle\Entity\GroupChat;
use WechatWorkGroupChatBundle\Entity\GroupMember;
use WechatWorkGroupChatBundle\Repository\GroupChatRepository;

// 获取所有客户群
$groupChats = $entityManager->getRepository(GroupChat::class)->findAll();

// 根据群聊 ID 查找客户群
$groupChat = $entityManager->getRepository(GroupChat::class)->findOneBy(['chatId' => 'chat_id']);

// 获取群成员
$members = $entityManager->getRepository(GroupMember::class)->findBy(['groupChat' => $groupChat]);

// 使用控制台命令同步客户群数据
// php bin/console wechat-work:sync-group-chat-list
```

## 使用方法

### 实体

#### GroupChat（客户群）

表示企业微信客户群，包含以下属性：
- 群聊 ID
- 群名称
- 状态（正常、离职、继承中、继承完成）
- 群主（用户关联）
- 管理员列表
- 成员数量
- 创建时间
- 群公告

#### GroupMember（群成员）

表示客户群的成员，包含属性：
- 成员类型（内部/外部）
- 用户 ID
- 入群方式（直接邀请、邀请链接、二维码）
- 入群时间
- 邀请人信息

### 控制台命令

#### wechat-work:sync-group-chat-list

从企业微信同步客户群数据到本地数据库。

```bash
# 同步所有客户群
php bin/console wechat-work:sync-group-chat-list

# 该命令也配置为定时任务，每天早上 6:14 自动执行
```

## 仓储类

- `GroupChatRepository`：提供客户群查询方法
- `GroupMemberRepository`：提供群成员查询方法

## 高级用法

### 自定义仓储方法

```php
<?php

// 查询活跃的客户群
$activeChats = $groupChatRepository->findBy(['status' => GroupChatStatus::NORMAL]);

// 获取带成员数的群组
$groupsWithMembers = $groupChatRepository
    ->createQueryBuilder('g')
    ->select('g, COUNT(m.id) as memberCount')
    ->leftJoin('g.members', 'm')
    ->groupBy('g.id')
    ->getQuery()
    ->getResult();
```

### 事件集成

您可以监听同步过程中分发的事件：

```php
<?php

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GroupChatSyncSubscriber implements EventSubscriberInterface
{
    public function onGroupChatSynced(GroupChatSyncedEvent $event): void
    {
        // 客户群同步后的自定义逻辑
    }
    
    public static function getSubscribedEvents(): array
    {
        return [
            GroupChatSyncedEvent::class => 'onGroupChatSynced',
        ];
    }
}
```

### 消息处理器

Bundle 包含异步消息处理器用于处理客户群同步：
- `SyncGroupChatDetailHandler`：处理单个客户群详情同步

### 枚举类型

#### GroupChatStatus（客户群状态）

- `NORMAL` (0)：跟进人正常
- `RESIGN` (1)：跟进人离职
- `INHERIT_DOING` (2)：离职继承中
- `INHERIT_FINISHED` (3)：离职继承完成

## API 集成

该 Bundle 集成了企业微信 API，用于：
- 获取客户群列表
- 获取客户群详情
- 同步成员信息

## 开发

### 运行测试

```bash
# 运行所有测试
./vendor/bin/phpunit packages/wechat-work-group-chat-bundle/tests

# 运行 PHPStan 分析
php -d memory_limit=2G ./vendor/bin/phpstan analyse packages/wechat-work-group-chat-bundle
```

## 贡献

请查看 [CONTRIBUTING.md](../../CONTRIBUTING.md) 了解如何为此项目做贡献的详细信息。

- 通过 GitHub issues 提交错误报告和功能请求
- 遵循编码标准和测试要求
- 确保所有测试在提交 PR 之前通过

## 许可证

MIT 许可证。请查看 [许可证文件](../../LICENSE) 了解更多信息。

## 文档

- [企业微信客户群 API 文档](https://developer.work.weixin.qq.com/document/path/92120)
- [Tourze Monorepo 文档](../../README.md)