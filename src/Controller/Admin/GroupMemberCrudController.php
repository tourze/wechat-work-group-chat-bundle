<?php

declare(strict_types=1);

namespace WechatWorkGroupChatBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use WechatWorkGroupChatBundle\Entity\GroupMember;

/**
 * @phpstan-extends AbstractCrudController<GroupMember>
 */
#[AdminCrud(routePath: '/wechat-work-group-chat/group-member', routeName: 'wechat_work_group_chat_group_member')]
final class GroupMemberCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return GroupMember::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('群成员')
            ->setEntityLabelInPlural('群成员')
            ->setSearchFields(['userId', 'name', 'groupNickname', 'invitorUserId'])
            ->setDefaultSort(['createTime' => 'DESC'])
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::DELETE)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('groupChat')
            ->add('userId')
            ->add('name')
            ->add('groupNickname')
            ->add('type')
            ->add('joinScene')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->onlyOnDetail()
        ;

        yield AssociationField::new('groupChat', '所属群聊')
            ->setHelp('成员所属的客户群')
            ->setRequired(true)
        ;

        yield TextField::new('userId', '用户ID')
            ->setHelp('企业微信用户的唯一标识符')
            ->setRequired(true)
        ;

        yield TextField::new('name', '姓名')
            ->setHelp('成员的真实姓名')
        ;

        yield TextField::new('groupNickname', '群昵称')
            ->setHelp('成员在群内的昵称')
        ;

        yield IntegerField::new('type', '成员类型')
            ->setHelp('成员类型（1-企业成员，2-外部联系人）')
        ;

        yield DateTimeField::new('joinTime', '加入时间')
            ->setHelp('成员加入群聊的时间')
            ->onlyOnDetail()
        ;

        yield IntegerField::new('joinScene', '加入场景')
            ->setHelp('成员加入群聊的场景代码')
            ->onlyOnDetail()
        ;

        yield TextField::new('invitorUserId', '邀请人ID')
            ->setHelp('邀请该成员加入群聊的用户ID')
            ->onlyOnDetail()
        ;

        yield DateTimeField::new('createTime', '创建时间')
            ->setHelp('记录创建时间')
            ->onlyOnDetail()
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->setHelp('记录最后更新时间')
            ->onlyOnDetail()
        ;
    }
}
