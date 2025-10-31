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

A Symfony bundle for managing WeChat Work group chats (customer groups). 
This bundle provides entities, repositories, and synchronization functionality 
for WeChat Work group chat data.

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Quick Start](#quick-start)
- [Usage](#usage)
  - [Entities](#entities)
  - [Console Commands](#console-commands)
- [Repositories](#repositories)
- [Advanced Usage](#advanced-usage)
  - [Custom Repository Methods](#custom-repository-methods)
  - [Event Integration](#event-integration)
  - [Message Handlers](#message-handlers)
  - [Enumerations](#enumerations)
- [API Integration](#api-integration)
- [Development](#development)
- [Contributing](#contributing)
- [License](#license)
- [Documentation](#documentation)

## Features

- Group chat and member entity management
- Automatic synchronization of group chat data from WeChat Work API
- Support for group chat status tracking (normal, resigned, inheritance in progress, inheritance completed)
- Message queue integration for asynchronous processing

## Requirements

- PHP 8.1+
- Symfony 6.4+
- Doctrine ORM
- Symfony Messenger

## Installation

```bash
composer require tourze/wechat-work-group-chat-bundle
```

## Configuration

This bundle automatically configures itself when installed. No additional configuration is required for basic usage.

## Quick Start

```php
<?php

use WechatWorkGroupChatBundle\Entity\GroupChat;
use WechatWorkGroupChatBundle\Entity\GroupMember;
use WechatWorkGroupChatBundle\Repository\GroupChatRepository;

// Get all group chats
$groupChats = $entityManager->getRepository(GroupChat::class)->findAll();

// Find group chat by specific criteria
$groupChat = $entityManager->getRepository(GroupChat::class)->findOneBy(['chatId' => 'chat_id']);

// Get group members
$members = $entityManager->getRepository(GroupMember::class)->findBy(['groupChat' => $groupChat]);

// Sync group chat data using console command
// php bin/console wechat-work:sync-group-chat-list
```

## Usage

### Entities

#### GroupChat

Represents a WeChat Work customer group with the following properties:
- Chat ID
- Group name
- Status (normal, resigned, inheritance in progress, inheritance completed)
- Owner (user association)
- Admin list
- Member count
- Creation time
- Notice (group announcement)

#### GroupMember

Represents a member of a group chat with properties:
- Member type (internal/external)
- User ID
- Join scene (direct invitation, invitation link, QR code)
- Join time
- Invitor information

### Console Commands

#### wechat-work:sync-group-chat-list

Synchronizes customer group data from WeChat Work to local database.

```bash
# Sync all group chats
php bin/console wechat-work:sync-group-chat-list

# The command is also configured as a cron job to run daily at 6:14 AM
```

## Repositories

- `GroupChatRepository`: Provides methods for querying group chats
- `GroupMemberRepository`: Provides methods for querying group members

## Advanced Usage

### Custom Repository Methods

```php
<?php

// Custom query for active group chats
$activeChats = $groupChatRepository->findBy(['status' => GroupChatStatus::NORMAL]);

// Get groups with member count
$groupsWithMembers = $groupChatRepository
    ->createQueryBuilder('g')
    ->select('g, COUNT(m.id) as memberCount')
    ->leftJoin('g.members', 'm')
    ->groupBy('g.id')
    ->getQuery()
    ->getResult();
```

### Event Integration

The bundle dispatches events during synchronization that you can listen to:

```php
<?php

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GroupChatSyncSubscriber implements EventSubscriberInterface
{
    public function onGroupChatSynced(GroupChatSyncedEvent $event): void
    {
        // Custom logic after group chat sync
    }
    
    public static function getSubscribedEvents(): array
    {
        return [
            GroupChatSyncedEvent::class => 'onGroupChatSynced',
        ];
    }
}
```

### Message Handlers

The bundle includes asynchronous message handlers for processing group chat synchronization:
- `SyncGroupChatDetailHandler`: Processes individual group chat detail synchronization

### Enumerations

#### GroupChatStatus

- `NORMAL` (0): Following person is normal
- `RESIGN` (1): Following person has resigned
- `INHERIT_DOING` (2): Resignation inheritance in progress
- `INHERIT_FINISHED` (3): Resignation inheritance completed

## API Integration

This bundle integrates with the WeChat Work API for:
- Getting group chat lists
- Getting group chat details
- Synchronizing member information

## Development

### Running Tests

```bash
# Run all tests
./vendor/bin/phpunit packages/wechat-work-group-chat-bundle/tests

# Run PHPStan analysis
php -d memory_limit=2G ./vendor/bin/phpstan analyse packages/wechat-work-group-chat-bundle
```

## Contributing

Please see [CONTRIBUTING.md](../../CONTRIBUTING.md) for details on how to contribute to this project.

- Submit bug reports and feature requests through GitHub issues
- Follow the coding standards and testing requirements
- Ensure all tests pass before submitting pull requests

## License

The MIT License (MIT). Please see [License File](../../LICENSE) for more information.

## Documentation

- [WeChat Work Group Chat API Documentation](https://developer.work.weixin.qq.com/document/path/92120)
- [Tourze Monorepo Documentation](../../README.md)